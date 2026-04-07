<?php

namespace App\Http\Controllers;

use App\Http\Services\PedidosService;
use App\Http\Client\ApiException;

class PedidoController extends Controller
{
    public function __construct(private PedidosService $pedidosService) {}

    /** Lista los pedidos del usuario logueado. */
    public function index()
    {
        $usuarioId = session('usuario.id');
        try {
            $pedidos = $this->pedidosService->listarPorUsuario($usuarioId);
        } catch (ApiException $e) {
            $pedidos = [];
        }
        return view('pedidos', compact('pedidos'));
    }

    /** Detalle de un pedido. */
    public function show(int $id)
    {
        try {
            $pedido = $this->pedidosService->obtener($id);
        } catch (ApiException $e) {
            return redirect('/pedidos')->withErrors(['api' => $e->getMessage()]);
        }
        return view('pedido-detalle', compact('pedido'));
    }
}
