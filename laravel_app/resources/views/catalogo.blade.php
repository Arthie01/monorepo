@extends('layouts.app')

@section('title', 'Catálogo de Autopartes')

@push('styles')
<style>
    .cat-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 32px;
        align-items: start;
    }
    .cat-sidebar {
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--macuin-gray);
        overflow: hidden;
        position: sticky;
        top: 88px;
    }
    .cat-sidebar__section { padding: 20px; border-bottom: 1px solid var(--macuin-gray); }
    .cat-sidebar__section:last-child { border-bottom: none; }
    .cat-sidebar__title {
        font-family: 'Oswald', sans-serif;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--macuin-text);
        margin-bottom: 14px;
    }
    .filter-check { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer; }
    .filter-check input { width: 15px; height: 15px; accent-color: var(--macuin-red); }
    .filter-check span { font-size: 13px; color: var(--macuin-muted); }
    .filter-check:hover span { color: var(--macuin-text); }

    .price-range { display: flex; gap: 8px; align-items: center; }
    .price-input {
        flex: 1; padding: 8px 10px;
        border: 1px solid var(--macuin-gray);
        border-radius: 4px;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        outline: none;
    }
    .price-input:focus { border-color: var(--macuin-red); }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    @media (max-width: 1200px) { .product-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 900px)  {
        .cat-layout { grid-template-columns: 1fr; }
        .cat-sidebar { position: static; }
        .product-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px)  { .product-grid { grid-template-columns: 1fr; } }

    /* Pagination */
    .mac-pagination { display: flex; gap: 6px; align-items: center; justify-content: center; margin-top: 40px; }
    .mac-page-btn {
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 4px; font-size: 13px; font-weight: 600;
        text-decoration: none; color: var(--macuin-text);
        border: 1px solid var(--macuin-gray);
        transition: all .2s;
    }
    .mac-page-btn:hover, .mac-page-btn.active {
        background: var(--macuin-red); color: #fff; border-color: var(--macuin-red);
    }
</style>
@endpush

@section('content')

{{-- Sección: Page Header --}}
<div style="background:var(--macuin-dark);padding:28px 0;">
    <div class="mac-container">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-steel);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
                    <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
                    <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>Catálogo
                </p>
                <h1 style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:#fff;text-transform:uppercase;">Catálogo de Autopartes</h1>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6B7280;font-size:13px;pointer-events:none;"></i>
                    <input type="text" placeholder="Buscar autoparte..." style="
                        padding:10px 14px 10px 36px;
                        background:rgba(255,255,255,.08);
                        border:1px solid rgba(255,255,255,.15);
                        border-radius:4px;color:#fff;
                        font-family:'DM Sans',sans-serif;font-size:13px;
                        outline:none;width:240px;
                    " onfocus="this.style.borderColor='var(--macuin-red)'" onblur="this.style.borderColor='rgba(255,255,255,.15)'">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sección: Catálogo con sidebar --}}
<section style="padding:40px 0 60px;background:var(--macuin-white);">
    <div class="mac-container">
        <div class="cat-layout">

            {{-- Sidebar de Filtros --}}
            <form method="GET" action="/catalogo" id="cat-filter-form">
            <aside class="cat-sidebar">
                <div class="cat-sidebar__section" style="background:var(--macuin-dark);">
                    <h3 style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.08em;margin:0;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-sliders-h" style="color:var(--macuin-red);"></i> Filtros
                    </h3>
                </div>

                {{-- Categoría --}}
                <div class="cat-sidebar__section">
                    <div class="cat-sidebar__title">Categoría</div>
                    @php
                        $cats = ['Motor','Suspensión','Frenos','Eléctrico','Transmisión','Filtros'];
                    @endphp
                    @foreach($cats as $c)
                    <label class="filter-check">
                        <input type="radio" name="categoria" value="{{ $c }}"
                            {{ ($filtros['categoria'] ?? '') === $c ? 'checked' : '' }}
                            onchange="document.getElementById('cat-filter-form').submit()">
                        <span>{{ $c }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Marca de vehículo --}}
                <div class="cat-sidebar__section">
                    <div class="cat-sidebar__title">Marca de Vehículo</div>
                    @foreach(['Chevrolet','Ford','Nissan','Volkswagen','Toyota','Honda','Dodge','Kia','Hyundai','Mazda'] as $m)
                    <label class="filter-check">
                        <input type="radio" name="marca_vehiculo" value="{{ $m }}"
                            {{ ($filtros['marca_vehiculo'] ?? '') === $m ? 'checked' : '' }}
                            onchange="document.getElementById('cat-filter-form').submit()">
                        <span>{{ $m }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="cat-sidebar__section">
                    <button type="submit" class="mac-btn mac-btn-primary mac-btn-block mac-btn-sm">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                    <a href="/catalogo" class="mac-btn mac-btn-ghost mac-btn-block mac-btn-sm" style="margin-top:8px;text-align:center;text-decoration:none;">
                        Limpiar filtros
                    </a>
                </div>
            </aside>
            </form>

            {{-- Grid de Productos --}}
            <div>
                {{-- Toolbar --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                    <div>
                        <p style="font-size:14px;color:var(--macuin-muted);margin-bottom:6px;">
                            <strong style="color:var(--macuin-text);">{{ count($autopartes) }}</strong> resultado(s)
                            @if(!empty($filtros['marca_vehiculo']))
                                para <strong style="color:var(--macuin-text);">{{ $filtros['marca_vehiculo'] }}</strong>
                            @endif
                            @if(!empty($filtros['modelo_vehiculo']))
                                — <strong style="color:var(--macuin-text);">{{ $filtros['modelo_vehiculo'] }}</strong>
                            @endif
                            @if(!empty($filtros['categoria']))
                                en <strong style="color:var(--macuin-text);">{{ $filtros['categoria'] }}</strong>
                            @endif
                        </p>
                        {{-- Tags de filtros activos --}}
                        @if(!empty($filtros['marca_vehiculo']) || !empty($filtros['modelo_vehiculo']) || !empty($filtros['categoria']))
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if(!empty($filtros['categoria']))
                            <span style="background:var(--macuin-red);color:#fff;font-size:11px;font-family:'DM Sans',sans-serif;padding:3px 10px;border-radius:20px;display:flex;align-items:center;gap:5px;">
                                {{ $filtros['categoria'] }}
                                <a href="/catalogo?{{ http_build_query(array_filter(array_merge($filtros, ['categoria'=>null]))) }}" style="color:rgba(255,255,255,.7);text-decoration:none;font-weight:700;">×</a>
                            </span>
                            @endif
                            @if(!empty($filtros['marca_vehiculo']))
                            <span style="background:var(--macuin-red);color:#fff;font-size:11px;font-family:'DM Sans',sans-serif;padding:3px 10px;border-radius:20px;display:flex;align-items:center;gap:5px;">
                                {{ $filtros['marca_vehiculo'] }}
                                <a href="/catalogo?{{ http_build_query(array_filter(array_merge($filtros, ['marca_vehiculo'=>null]))) }}" style="color:rgba(255,255,255,.7);text-decoration:none;font-weight:700;">×</a>
                            </span>
                            @endif
                            @if(!empty($filtros['modelo_vehiculo']))
                            <span style="background:var(--macuin-red);color:#fff;font-size:11px;font-family:'DM Sans',sans-serif;padding:3px 10px;border-radius:20px;display:flex;align-items:center;gap:5px;">
                                {{ $filtros['modelo_vehiculo'] }}
                                <a href="/catalogo?{{ http_build_query(array_filter(array_merge($filtros, ['modelo_vehiculo'=>null]))) }}" style="color:rgba(255,255,255,.7);text-decoration:none;font-weight:700;">×</a>
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <label style="font-size:13px;color:var(--macuin-muted);">Ordenar:</label>
                        <select style="
                            padding:8px 12px;border:1px solid var(--macuin-gray);
                            border-radius:4px;font-family:'DM Sans',sans-serif;font-size:13px;
                            outline:none;cursor:pointer;background:#fff;
                        ">
                            <option>Relevancia</option>
                            <option>Precio: Menor a Mayor</option>
                            <option>Precio: Mayor a Menor</option>
                            <option>Nombre A–Z</option>
                            <option>Disponibilidad</option>
                        </select>
                    </div>
                </div>

                {{-- Productos --}}
                <div class="product-grid">
                    @forelse($autopartes as $a)
                    <div class="mac-product-card">
                        <a href="/catalogo/{{ $a['id'] }}" style="display:block;">
                            <div class="mac-product-card__image">
                                @if(!empty($a['imagen']))
                                    <img src="{{ $a['imagen'] }}" alt="{{ $a['nombre'] }}" loading="lazy">
                                @else
                                    <div style="background:#eee;height:200px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-image" style="font-size:40px;color:#ccc;"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="mac-product-card__body">
                            <div class="mac-product-card__sku">SKU: {{ $a['sku'] }} · {{ $a['categoria'] }}</div>
                            <a href="/catalogo/{{ $a['id'] }}" style="text-decoration:none;">
                                <div class="mac-product-card__name">{{ $a['nombre'] }}</div>
                            </a>
                            <div style="display:flex;align-items:baseline;gap:10px;">
                                <div class="mac-product-card__price">${{ number_format($a['precio'], 2) }}</div>
                                @if(!empty($a['precio_original']))
                                    <div style="font-size:13px;color:var(--macuin-muted);text-decoration:line-through;">
                                        ${{ number_format($a['precio_original'], 2) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mac-product-card__footer">
                            @php
                                $badge = match($a['estado']) {
                                    'en_stock'   => ['label' => 'Disponible',  'class' => 'mac-badge--available'],
                                    'bajo_stock' => ['label' => 'Poco stock',  'class' => 'mac-badge--low'],
                                    default      => ['label' => 'Sin stock',   'class' => 'mac-badge--out'],
                                };
                            @endphp
                            <span class="mac-badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            <form action="/carrito/agregar" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="autoparte_id" value="{{ $a['id'] }}">
                                <input type="hidden" name="nombre" value="{{ $a['nombre'] }}">
                                <input type="hidden" name="precio" value="{{ $a['precio'] }}">
                                <input type="hidden" name="imagen" value="{{ $a['imagen'] ?? '' }}">
                                <button type="submit" class="mac-btn mac-btn-primary mac-btn-sm"
                                    {{ $a['estado'] === 'sin_stock' ? 'disabled' : '' }}>
                                    <i class="fas fa-cart-plus"></i> Agregar
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p style="text-align:center;padding:40px;color:var(--macuin-muted);">Sin productos disponibles.</p>
                    @endforelse
                </div>

                {{-- Paginación --}}
                <div class="mac-pagination">
                    <a href="#" class="mac-page-btn"><i class="fas fa-chevron-left" style="font-size:11px;"></i></a>
                    <a href="#" class="mac-page-btn active">1</a>
                    <a href="#" class="mac-page-btn">2</a>
                    <a href="#" class="mac-page-btn">3</a>
                    <span style="color:var(--macuin-muted);font-size:13px;padding:0 4px;">···</span>
                    <a href="#" class="mac-page-btn">21</a>
                    <a href="#" class="mac-page-btn"><i class="fas fa-chevron-right" style="font-size:11px;"></i></a>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
