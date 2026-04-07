<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PedidosService;
use App\Http\Client\ApiException;

class CarritoController extends Controller
{
    public function __construct(private PedidosService $pedidosService) {}

    /** Muestra el carrito actual desde session. */
    public function index()
    {
        $carrito = session('carrito', []);
        $subtotal = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        return view('carrito', compact('carrito', 'subtotal'));
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
        $subtotal = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        return view('checkout', compact('carrito', 'subtotal'));
    }

    /** Procesa checkout → llama API → limpia carrito. */
    public function checkout(Request $request)
    {
        $request->validate([
            'calle'   => 'required',
            'ciudad'  => 'required',
            'estado'  => 'required',
            'cp'      => 'required',
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return redirect('/carrito')->withErrors(['api' => 'El carrito está vacío.']);
        }

        $items = collect($carrito)->map(fn($i) => [
            'autoparte_id' => (int) $i['id'],
            'cantidad'     => (int) $i['cantidad'],
        ])->values()->all();

        $direccion = $request->only(['calle', 'ciudad', 'estado', 'cp']);
        $usuarioId = session('usuario.id');

        try {
            $this->pedidosService->crear($usuarioId, $items, $direccion);
            session()->forget('carrito');
            return redirect('/pedidos')->with('success', 'Pedido realizado exitosamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }
}
