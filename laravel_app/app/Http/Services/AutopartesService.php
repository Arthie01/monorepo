<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class AutopartesService
{
    public function __construct(private ApiClient $client) {}

    /** Lista todas las autopartes. Si $categoria se pasa, filtra. */
    public function listar(?string $categoria = null): array
    {
        $query = $categoria ? ['categoria' => $categoria] : [];
        $resp = $this->client->get('/v1/autopartes/', $query);
        return $resp['data'] ?? [];
    }

    /** Retorna una autoparte por id. */
    public function obtener(int $id): array
    {
        $resp = $this->client->get("/v1/autopartes/{$id}");
        return $resp['data'] ?? [];
    }
}
