<?php

use Illuminate\Support\Facades\Route;

// Redirigir raíz a login
Route::get('/', fn() => redirect('/login'));

// ── Autenticación ──────────────────────────────────────
Route::get('/login',           fn() => view('login'));
Route::post('/login',          fn() => redirect('/dashboard'));   // placeholder
Route::get('/registro',        fn() => view('register'));
Route::post('/registro',       fn() => redirect('/login'));       // placeholder
Route::get('/forgot-password', fn() => view('forgot-password'));

// ── Portal del Cliente (estáticas) ────────────────────
Route::get('/dashboard',        fn() => view('dashboard'));
Route::get('/catalogo',         fn() => view('catalogo'));
Route::get('/catalogo/{id}',    fn() => view('detalle-producto'));
Route::get('/carrito',          fn() => view('carrito'));
Route::get('/checkout',         fn() => view('checkout'));
Route::get('/pedidos',          fn() => view('pedidos'));
Route::get('/pedido/{id}',      fn() => view('pedido-detalle'));
Route::get('/perfil',           fn() => view('perfil'));

// Legacy (rama Diego)
Route::get('/register',         fn() => redirect('/registro'));
