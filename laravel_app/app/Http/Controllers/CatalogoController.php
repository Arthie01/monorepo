<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AutopartesService;
use App\Http\Client\ApiException;

class CatalogoController extends Controller
{
    public function __construct(private AutopartesService $autopartesService) {}

    /** Dashboard — muestra autopartes destacadas y extrae marcas/modelos reales de la BD. */
    public function dashboard()
    {
        try {
            $todas = $this->autopartesService->listar();
            $autopartes = array_slice($todas, 0, 8);

            // Extraer marcas y modelos únicos desde el campo marca_vehiculo / modelo_vehiculo
            $marcas = [];
            $modelosPorMarca = [];
            foreach ($todas as $a) {
                $marcasList  = !empty($a['marca_vehiculo'])  ? array_map('trim', explode(',', $a['marca_vehiculo']))  : [];
                $modelosList = !empty($a['modelo_vehiculo']) ? array_map('trim', explode(',', $a['modelo_vehiculo'])) : [];
                foreach ($marcasList as $m) {
                    if (!$m) continue;
                    $marcas[$m] = $m;
                    if (!isset($modelosPorMarca[$m])) $modelosPorMarca[$m] = [];
                    foreach ($modelosList as $mod) {
                        if ($mod && !in_array($mod, $modelosPorMarca[$m])) {
                            $modelosPorMarca[$m][] = $mod;
                        }
                    }
                }
            }
            ksort($marcas);
            $marcas = array_values($marcas);
        } catch (ApiException $e) {
            $autopartes      = [];
            $marcas          = [];
            $modelosPorMarca = [];
        }
        return view('dashboard', compact('autopartes', 'marcas', 'modelosPorMarca'));
    }

    /** Catálogo completo con filtros opcionales: categoría, marca y modelo de vehículo. */
    public function index(Request $request)
    {
        $categoria      = $request->query('categoria');
        $marcaVehiculo  = $request->query('marca_vehiculo');
        $modeloVehiculo = $request->query('modelo_vehiculo');
        try {
            $autopartes = $this->autopartesService->listar($categoria, $marcaVehiculo, $modeloVehiculo);
        } catch (ApiException $e) {
            $autopartes = [];
        }
        $filtros = [
            'categoria'      => $categoria,
            'marca_vehiculo' => $marcaVehiculo,
            'modelo_vehiculo'=> $modeloVehiculo,
        ];
        return view('catalogo', compact('autopartes', 'filtros'));
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
