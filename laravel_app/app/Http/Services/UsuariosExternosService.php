<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class UsuariosExternosService
{
    public function __construct(private ApiClient $client) {}

    public function obtener(int $id): array
    {
        $resp = $this->client->get("/v1/usuarios/externos/{$id}");
        return $resp['data'] ?? [];
    }
}
