<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class AutopartesService
{
    public function __construct(private ApiClient $client) {}

    /** Lista todas las autopartes. Filtros opcionales: categoria, marca y modelo de vehículo. */
    public function listar(?string $categoria = null, ?string $marcaVehiculo = null, ?string $modeloVehiculo = null): array
    {
        $query = [];
        if ($categoria)      $query['categoria']       = $categoria;
        if ($marcaVehiculo)  $query['marca_vehiculo']  = $marcaVehiculo;
        if ($modeloVehiculo) $query['modelo_vehiculo'] = $modeloVehiculo;
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
