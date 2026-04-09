<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;

// ── Raíz ──────────────────────────────────────────────────────────────────────
Route::get('/', [CatalogoController::class, 'dashboard']);

// ── Autenticación (públicas) ──────────────────────────────────────────────────
Route::get('/login',          [AuthController::class, 'showLogin']);
Route::post('/login',         [AuthController::class, 'login']);
Route::get('/registro',       [AuthController::class, 'showRegistro']);
Route::post('/registro',      [AuthController::class, 'registro']);
Route::get('/logout',         [AuthController::class, 'logout']);
Route::get('/forgot-password', fn() => view('forgot-password'));

// ── Portal del Cliente (protegidas) ───────────────────────────────────────────
// Dashboard y catálogo son públicos (pueden verse sin login)
Route::get('/dashboard',           [CatalogoController::class,  'dashboard']);
Route::get('/catalogo',            [CatalogoController::class,  'index']);
Route::get('/catalogo/{id}',       [CatalogoController::class,  'show']);

Route::middleware('check.session')->group(function () {

    Route::get('/carrito',             [CarritoController::class,   'index']);
    Route::post('/carrito/agregar',    [CarritoController::class,   'agregar']);
    Route::post('/carrito/actualizar', [CarritoController::class,   'actualizar']);
    Route::get('/checkout',            [CarritoController::class,   'showCheckout']);
    Route::post('/checkout',           [CarritoController::class,   'checkout']);

    Route::get('/pedidos',                    [PedidoController::class,  'index']);
    Route::get('/pedido/{id}',               [PedidoController::class,  'show']);
    Route::get('/pedido/{id}/pdf',           [PedidoController::class,  'pdf']);
    Route::post('/pedido/{id}/reordenar',    [CarritoController::class, 'reordenar']);

    Route::get('/perfil',              [PerfilController::class,    'index']);
    Route::put('/perfil',              [PerfilController::class,    'update']);
    Route::put('/perfil/password',     [PerfilController::class,    'updatePassword']);
});

// Legacy
Route::get('/register', fn() => redirect('/registro'));
