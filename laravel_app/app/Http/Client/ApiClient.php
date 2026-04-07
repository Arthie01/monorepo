<?php

namespace App\Http\Client;

use Illuminate\Support\Facades\Http;

class ApiException extends \RuntimeException
{
    public function __construct(public int $statusCode, string $message)
    {
        parent::__construct($message);
    }
}

class ApiClient
{
    private string $base;

    public function __construct()
    {
        $this->base = config('services.api.url', 'http://localhost:8001');
    }

    private function handle(\Illuminate\Http\Client\Response $resp): array
    {
        if ($resp->failed()) {
            $detail = $resp->json('detail') ?? $resp->body();
            if (is_array($detail)) {
                $detail = collect($detail)->pluck('msg')->implode('; ');
            }
            throw new ApiException($resp->status(), (string) $detail);
        }
        return $resp->json() ?? [];
    }

    public function get(string $path, array $query = []): array
    {
        return $this->handle(Http::get($this->base . $path, $query));
    }

    public function post(string $path, array $data = []): array
    {
        return $this->handle(Http::post($this->base . $path, $data));
    }

    public function postQuery(string $path, array $query = []): array
    {
        return $this->handle(
            Http::withQueryParameters($query)->post($this->base . $path)
        );
    }

    public function patch(string $path, array $data = []): array
    {
        return $this->handle(Http::patch($this->base . $path, $data));
    }

    public function delete(string $path, string $user = 'macuin', string $pass = '123456'): array
    {
        return $this->handle(
            Http::withBasicAuth($user, $pass)->delete($this->base . $path)
        );
    }
}
