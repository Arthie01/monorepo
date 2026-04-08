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

    /** Catálogo completo con filtros opcionales: categoría, marca, modelo, búsqueda y orden. */
    public function index(Request $request)
    {
        $categoria      = $request->query('categoria');
        $marcaVehiculo  = $request->query('marca_vehiculo');
        $modeloVehiculo = $request->query('modelo_vehiculo');
        $q              = $request->query('q');
        $orden          = $request->query('orden');

        try {
            $autopartes = $this->autopartesService->listar($categoria, $marcaVehiculo, $modeloVehiculo);
        } catch (ApiException $e) {
            $autopartes = [];
        }

        // Filtrar por búsqueda (nombre o SKU)
        if ($q) {
            $qLower     = strtolower($q);
            $autopartes = array_values(array_filter($autopartes, function ($a) use ($qLower) {
                return str_contains(strtolower($a['nombre'] ?? ''), $qLower)
                    || str_contains(strtolower($a['sku'] ?? ''), $qLower);
            }));
        }

        // Ordenar resultados
        switch ($orden) {
            case 'precio_asc':
                usort($autopartes, fn($a, $b) => $a['precio'] <=> $b['precio']);
                break;
            case 'precio_desc':
                usort($autopartes, fn($a, $b) => $b['precio'] <=> $a['precio']);
                break;
            case 'nombre_az':
                usort($autopartes, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
                break;
            case 'disponibilidad':
                $prioridad = ['en_stock' => 0, 'bajo_stock' => 1, 'sin_stock' => 2];
                usort($autopartes, fn($a, $b) => ($prioridad[$a['estado']] ?? 3) <=> ($prioridad[$b['estado']] ?? 3));
                break;
        }

        $filtros = [
            'categoria'      => $categoria,
            'marca_vehiculo' => $marcaVehiculo,
            'modelo_vehiculo'=> $modeloVehiculo,
            'q'              => $q,
            'orden'          => $orden,
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
