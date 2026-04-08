<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class AuthService
{
    public function __construct(private ApiClient $client) {}

    /**
     * Registra un usuario externo.
     * $data: nombre, apellidos, email, password
     */
    public function registro(array $data): array
    {
        // Eliminar telefono si viene vacío para no enviar null innecesario
        if (isset($data['telefono']) && $data['telefono'] === '') {
            unset($data['telefono']);
        }
        $resp = $this->client->post('/v1/auth/registro', $data);
        return $resp['data'] ?? $resp;
    }

    /**
     * Autentica un cliente externo.
     * Retorna: id, nombre, apellidos, email, tipo_cliente, descuento, lista_precio
     */
    public function login(string $email, string $password): array
    {
        $resp = $this->client->postQuery('/v1/auth/login/externo', [
            'email'    => $email,
            'password' => $password,
        ]);
        return $resp['data'];
    }
}
