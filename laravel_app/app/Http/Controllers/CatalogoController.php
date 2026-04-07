<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AutopartesService;
use App\Http\Client\ApiException;

class CatalogoController extends Controller
{
    public function __construct(private AutopartesService $autopartesService) {}

    /** Dashboard — muestra autopartes destacadas (primeras 8). */
    public function dashboard()
    {
        try {
            $autopartes = array_slice($this->autopartesService->listar(), 0, 8);
        } catch (ApiException $e) {
            $autopartes = [];
        }
        return view('dashboard', compact('autopartes'));
    }

    /** Catálogo completo con filtro opcional por categoría. */
    public function index(Request $request)
    {
        $categoria = $request->query('categoria');
        try {
            $autopartes = $this->autopartesService->listar($categoria);
        } catch (ApiException $e) {
            $autopartes = [];
        }
        return view('catalogo', compact('autopartes'));
    }

    /** Detalle de una autoparte. */
    public function show(int $id)
    {
        try {
            $autoparte = $this->autopartesService->obtener($id);
        } catch (ApiException $e) {
            return redirect('/catalogo')->withErrors(['api' => $e->getMessage()]);
        }
        return view('detalle-producto', compact('autoparte'));
    }
}
