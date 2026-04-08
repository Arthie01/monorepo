<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Client\ApiException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin()
    {
        if (session('usuario')) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $usuario = $this->authService->login(
                $request->input('email'),
                $request->input('password')
            );
            session(['usuario' => $usuario]);
            return redirect('/dashboard');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }

    public function showRegistro()
    {
        if (session('usuario')) {
            return redirect('/dashboard');
        }
        return view('register');
    }

    public function registro(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|min:2',
            'apellidos' => 'required|min:2',
            'email'     => 'required|email',
            'password'  => 'required|min:4',
            'telefono'  => 'nullable|string|max:15',
        ]);

        try {
            $this->authService->registro($request->only([
                'nombre', 'apellidos', 'email', 'password', 'telefono'
            ]));
            return redirect('/login')->with('success', 'Cuenta creada. Inicia sesión.');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }

    public function logout()
    {
        session()->forget('usuario');
        session()->forget('carrito');
        return redirect('/login');
    }
}
