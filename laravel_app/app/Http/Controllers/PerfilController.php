<?php

namespace App\Http\Controllers;

class PerfilController extends Controller
{
    /** Muestra el perfil del usuario logueado (datos frescos de API). */
    public function index()
    {
        $usuario_sesion = session('usuario');
        
        // Refrescar datos desde la API
        try {
            $response = \Http::get(env('API_URL', 'http://localhost:8001') . '/v1/usuarios/externos/' . $usuario_sesion['id']);
            
            if ($response->successful()) {
                $usuario = $response->json()['data'];
                // Actualizar sesión con datos frescos
                session(['usuario' => $usuario]);
            } else {
                $usuario = $usuario_sesion;
            }
        } catch (\Exception $e) {
            // Si falla la API, usar datos de sesión
            $usuario = $usuario_sesion;
        }
        
        return view('perfil', compact('usuario'));
    }
}
