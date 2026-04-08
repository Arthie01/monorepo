<?php

namespace App\Http\Controllers;

use App\Http\Services\PedidosService;
use App\Http\Client\ApiException;

class PerfilController extends Controller
{
    public function __construct(private PedidosService $pedidosService) {}

    /** Muestra el perfil del usuario logueado (datos frescos de API). */
    public function index()
    {
        $usuario_sesion = session('usuario');

        // Refrescar datos desde la API
        try {
            $response = \Http::get(env('API_URL', 'http://localhost:8001') . '/v1/usuarios/externos/' . $usuario_sesion['id']);

            if ($response->successful()) {
                $usuario = $response->json()['data'];
                session(['usuario' => $usuario]);
            } else {
                $usuario = $usuario_sesion;
            }
        } catch (\Exception $e) {
            $usuario = $usuario_sesion;
        }

        // Últimos 3 pedidos del usuario
        try {
            $todosPedidos = $this->pedidosService->listarPorUsuario($usuario_sesion['id']);
            $pedidos = array_slice($todosPedidos, 0, 3);
        } catch (ApiException $e) {
            $pedidos = [];
        }

        return view('perfil', compact('usuario', 'pedidos'));
    }

    /** Actualiza información personal del usuario (nombre, apellidos, email, teléfono). */
    public function update(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'nombre'    => 'required|min:2|max:50',
            'apellidos' => 'required|min:2|max:100',
            'email'     => 'required|email|max:120',
            'telefono'  => 'nullable|string|max:15',
        ]);

        $usuarioId = session('usuario.id');

        try {
            $response = \Http::put(
                env('API_URL', 'http://localhost:8001') . '/v1/usuarios/externos/' . $usuarioId,
                $request->only(['nombre', 'apellidos', 'email', 'telefono'])
            );

            if ($response->successful()) {
                $actualizado = $response->json()['data'];
                // Refrescar sesión con datos nuevos
                $sesion = session('usuario');
                $sesion['nombre']    = $actualizado['nombre'];
                $sesion['apellidos'] = $actualizado['apellidos'];
                $sesion['email']     = $actualizado['email'];
                $sesion['telefono']  = $actualizado['telefono'] ?? null;
                session(['usuario' => $sesion]);

                return redirect('/perfil')->with('success_info', 'Información actualizada correctamente.');
            }

            return back()->withErrors(['api' => 'No se pudo actualizar la información.']);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Error de conexión con el servidor.']);
        }
    }

    /** Actualiza la contraseña del usuario. */
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:4',
            'password_confirmation' => 'required|same:password',
        ]);

        $usuario = session('usuario');

        // Verificar contraseña actual contra la API
        try {
            $loginResp = \Http::asForm()->post(
                env('API_URL', 'http://localhost:8001') . '/v1/auth/login/externo?email=' .
                urlencode($usuario['email']) . '&password=' . urlencode($request->current_password)
            );

            if (!$loginResp->successful()) {
                return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Error de conexión con el servidor.']);
        }

        // Actualizar contraseña
        try {
            $response = \Http::put(
                env('API_URL', 'http://localhost:8001') . '/v1/usuarios/externos/' . $usuario['id'],
                ['password' => $request->password]
            );

            if ($response->successful()) {
                return redirect('/perfil')->with('success_password', 'Contraseña actualizada correctamente.');
            }

            return back()->withErrors(['api' => 'No se pudo actualizar la contraseña.']);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Error de conexión con el servidor.']);
        }
    }
}
