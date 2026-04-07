<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class PedidosService
{
    public function __construct(private ApiClient $client) {}

    /**
     * Crea un pedido.
     * $usuarioId: id del cliente externo logueado.
     * $items: [['autoparte_id' => int, 'cantidad' => int], ...]
     * $direccion: ['calle' => str, 'ciudad' => str, 'estado' => str, 'cp' => str]
     */
    public function crear(int $usuarioId, array $items, array $direccion): array
    {
        return $this->client->post('/v1/pedidos/', [
            'usuario_externo_id' => $usuarioId,
            'items'              => $items,
            'dir_calle'          => $direccion['calle']  ?? '',
            'dir_ciudad'         => $direccion['ciudad'] ?? '',
            'dir_estado'         => $direccion['estado'] ?? '',
            'dir_cp'             => $direccion['cp']     ?? '',
        ]);
    }

    /** Lista pedidos de un cliente externo específico. */
    public function listarPorUsuario(int $usuarioId): array
    {
        $resp = $this->client->get("/v1/pedidos/usuario/{$usuarioId}");
        return $resp['data'] ?? [];
    }

    /** Detalle completo de un pedido con líneas. */
    public function obtener(int $id): array
    {
        $resp = $this->client->get("/v1/pedidos/{$id}");
        return $resp['data'] ?? [];
    }
}
