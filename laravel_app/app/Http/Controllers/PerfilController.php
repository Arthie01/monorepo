<?php

namespace App\Http\Controllers;

class PerfilController extends Controller
{
    /** Muestra el perfil del usuario logueado (datos de session). */
    public function index()
    {
        $usuario = session('usuario');
        return view('perfil', compact('usuario'));
    }
}
