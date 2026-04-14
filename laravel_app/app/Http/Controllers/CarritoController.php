<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PedidosService;
use App\Http\Services\UsuariosExternosService;
use App\Http\Client\ApiException;

class CarritoController extends Controller
{
    public function __construct(
        private PedidosService $pedidosService,
        private UsuariosExternosService $usuariosService,
    ) {}

    /** Muestra el carrito actual desde session. */
    public function index()
    {
        $carrito  = session('carrito', []);
        $subtotal = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        $iva      = round($subtotal * 0.16, 2);
        $total    = $subtotal + $iva;
        return view('carrito', compact('carrito', 'subtotal', 'iva', 'total'));
    }

    /** Agrega o actualiza item en el carrito (session). */
    public function agregar(Request $request)
    {
        $id       = $request->input('autoparte_id');
        $nombre   = $request->input('nombre');
        $precio   = (float) $request->input('precio');
        $cantidad = max(1, (int) $request->input('cantidad', 1));
        $imagen   = $request->input('imagen', '');

        $carrito = session('carrito', []);
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad'] += $cantidad;
        } else {
            $carrito[$id] = compact('id', 'nombre', 'precio', 'cantidad', 'imagen');
        }
        session(['carrito' => $carrito]);

        return redirect('/carrito')->with('success', 'Producto agregado al carrito.');
    }

    /** Actualiza cantidades desde el carrito. */
    public function actualizar(Request $request)
    {
        $carrito = session('carrito', []);
        foreach ($request->input('cantidades', []) as $id => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) {
                unset($carrito[$id]);
            } else {
                $carrito[$id]['cantidad'] = $qty;
            }
        }
        session(['carrito' => $carrito]);
        return redirect('/carrito');
    }

    /** Muestra form de checkout con datos del carrito. */
    public function showCheckout()
    {
        $carrito  = session('carrito', []);

        if (empty($carrito)) {
            return redirect('/carrito')->withErrors(['api' => 'Tu carrito está vacío. Agrega productos antes de continuar.']);
        }

        $subtotal = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        $iva      = round($subtotal * 0.16, 2);
        $total    = $subtotal + $iva;

        // Obtener perfil completo del usuario (incluye dirección si fue registrada desde portal interno)
        $usuario = session('usuario', []);
        $perfil  = [];
        if (!empty($usuario['id'])) {
            try {
                $perfil = $this->usuariosService->obtener($usuario['id']);
            } catch (\Throwable) {
                $perfil = [];
            }
        }

        $descuento     = (float) ($perfil['descuento']       ?? $usuario['descuento']    ?? 0);
        $limiteCredito = (float) ($perfil['limite_credito'] ?? 0);

        return view('checkout', compact('carrito', 'subtotal', 'iva', 'total', 'usuario', 'perfil', 'descuento', 'limiteCredito'));
    }

    /** Agrega al carrito todos los items de un pedido anterior. */
    public function reordenar(int $id)
    {
        try {
            $pedido = $this->pedidosService->obtener($id);
        } catch (ApiException $e) {
            return redirect('/pedidos')->withErrors(['api' => $e->getMessage()]);
        }

        $carrito = session('carrito', []);
        foreach ($pedido['items'] ?? [] as $item) {
            $apId = $item['autoparte_id'];
            if (isset($carrito[$apId])) {
                $carrito[$apId]['cantidad'] += $item['cantidad'];
            } else {
                $carrito[$apId] = [
                    'id'       => $apId,
                    'nombre'   => $item['nombre'],
                    'precio'   => $item['precio_unitario'],
                    'cantidad' => $item['cantidad'],
                    'imagen'   => $item['imagen'] ?? '',
                ];
            }
        }
        session(['carrito' => $carrito]);
        return redirect('/carrito')->with('success', 'Artículos del pedido agregados al carrito.');
    }

    /** Procesa checkout → llama API → limpia carrito. */
    public function checkout(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|in:tarjeta,transferencia,credito_macuin',
            'calle'       => 'required|max:200',
            'ciudad'      => 'required|max:100',
            'estado'      => 'required|max:5',
            'cp'          => 'required|digits:5',
        ], [
            'metodo_pago.required' => 'Selecciona un método de pago.',
            'metodo_pago.in'       => 'Método de pago no válido.',
            'cp.digits'            => 'El código postal debe tener exactamente 5 dígitos.',
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return redirect('/carrito')->withErrors(['api' => 'El carrito está vacío.']);
        }

        $items = collect($carrito)->map(fn($i) => [
            'autoparte_id' => (int) $i['id'],
            'cantidad'     => (int) $i['cantidad'],
        ])->values()->all();

        $direccion   = $request->only(['calle', 'ciudad', 'estado', 'cp']);
        $usuarioId   = session('usuario.id');
        $metodoPago  = $request->input('metodo_pago');
        $metodoEnvio = $request->input('shipping', 'estandar');
        $notas       = $request->input('notes');

        try {
            $this->pedidosService->crear($usuarioId, $items, $direccion, $metodoPago, $metodoEnvio, $notas);
            session()->forget('carrito');
            return redirect('/pedidos')->with('success', 'Pedido realizado exitosamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }
}
