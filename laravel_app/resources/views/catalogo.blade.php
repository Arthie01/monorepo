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
                        $cats = ['Motor (3,240)','Suspensión (1,890)','Frenos (2,105)','Sistema Eléctrico (4,320)','Transmisión (1,560)','Filtros (980)','Carrocería (720)','Climatización (640)'];
                    @endphp
                    @foreach($cats as $c)
                    <label class="filter-check">
                        <input type="checkbox">
                        <span>{{ $c }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Marca de auto --}}
                <div class="cat-sidebar__section">
                    <div class="cat-sidebar__title">Marca de Auto</div>
                    @foreach(['Chevrolet','Ford','Nissan','Volkswagen','Toyota','Honda'] as $m)
                    <label class="filter-check">
                        <input type="checkbox">
                        <span>{{ $m }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Rango de precio --}}
                <div class="cat-sidebar__section">
                    <div class="cat-sidebar__title">Rango de Precio</div>
                    <div class="price-range">
                        <input type="number" class="price-input" placeholder="Mín" min="0">
                        <span style="color:var(--macuin-muted);font-size:12px;">—</span>
                        <input type="number" class="price-input" placeholder="Máx" min="0">
                    </div>
                </div>

                {{-- Stock --}}
                <div class="cat-sidebar__section">
                    <div class="cat-sidebar__title">Disponibilidad</div>
                    <label class="filter-check"><input type="radio" name="stock"> <span>Todos</span></label>
                    <label class="filter-check"><input type="radio" name="stock"> <span>En stock</span></label>
                    <label class="filter-check"><input type="radio" name="stock"> <span>Poco stock</span></label>
                </div>

                <div class="cat-sidebar__section">
                    <button class="mac-btn mac-btn-primary mac-btn-block mac-btn-sm">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                    <button class="mac-btn mac-btn-ghost mac-btn-block mac-btn-sm" style="margin-top:8px;">
                        Limpiar filtros
                    </button>
                </div>
            </aside>

            {{-- Grid de Productos --}}
            <div>
                {{-- Toolbar --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                    <p style="font-size:14px;color:var(--macuin-muted);">
                        Mostrando <strong style="color:var(--macuin-text);">1–12</strong> de <strong style="color:var(--macuin-text);">248</strong> resultados
                    </p>
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
                @php
                    $productos = [
                        ['sku'=>'FRN-001','name'=>'Pastillas de Freno Delanteras Premium Brembo','price'=>'$485','orig'=>'$620','badge'=>'available','cat'=>'Frenos'],
                        ['sku'=>'MOT-034','name'=>'Filtro de Aceite Universal Bosch','price'=>'$189','orig'=>null,'badge'=>'available','cat'=>'Motor'],
                        ['sku'=>'SUS-112','name'=>'Amortiguador Delantero Gabriel Ultra','price'=>'$1,240','orig'=>'$1,500','badge'=>'low','cat'=>'Suspensión'],
                        ['sku'=>'ELE-078','name'=>'Batería de Auto Optima 65Ah 12V','price'=>'$2,890','orig'=>null,'badge'=>'available','cat'=>'Eléctrico'],
                        ['sku'=>'FRN-045','name'=>'Disco de Freno Ventilado Brembo 300mm','price'=>'$950','orig'=>'$1,150','badge'=>'available','cat'=>'Frenos'],
                        ['sku'=>'MOT-089','name'=>'Bujía NGK Iridium Alto Rendimiento Set x4','price'=>'$640','orig'=>null,'badge'=>'out','cat'=>'Motor'],
                        ['sku'=>'SUS-078','name'=>'Rótula Inferior Delantera TRW','price'=>'$380','orig'=>null,'badge'=>'available','cat'=>'Suspensión'],
                        ['sku'=>'FIL-023','name'=>'Filtro de Aire K&N Alto Flujo','price'=>'$890','orig'=>'$1,050','badge'=>'available','cat'=>'Filtros'],
                        ['sku'=>'ELE-034','name'=>'Alternador 80A Remanufacturado','price'=>'$1,650','orig'=>null,'badge'=>'low','cat'=>'Eléctrico'],
                        ['sku'=>'MOT-156','name'=>'Correa de Distribución Gates PowerGrip','price'=>'$420','orig'=>null,'badge'=>'available','cat'=>'Motor'],
                        ['sku'=>'FRN-089','name'=>'Liquido de Frenos DOT4 500ml Bosch','price'=>'$98','orig'=>null,'badge'=>'available','cat'=>'Frenos'],
                        ['sku'=>'SUS-201','name'=>'Terminales de Dirección par Delantero','price'=>'$560','orig'=>'$680','badge'=>'available','cat'=>'Suspensión'],
                    ];
                    $badgeMap = ['available'=>['label'=>'Disponible','class'=>'mac-badge--available'],'low'=>['label'=>'Poco stock','class'=>'mac-badge--low'],'out'=>['label'=>'Sin stock','class'=>'mac-badge--out']];
                    $imgs = ['https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80','https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=400&q=80','https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=400&q=80','https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=400&q=80'];
                @endphp

                <div class="product-grid">
                    @foreach($productos as $i => $p)
                    <div class="mac-product-card">
                        <a href="/catalogo/{{ $i+1 }}" style="display:block;">
                            <div class="mac-product-card__image">
                                <img src="{{ $imgs[$i % 4] }}" alt="{{ $p['name'] }}" loading="lazy">
                            </div>
                        </a>
                        <div class="mac-product-card__body">
                            <div class="mac-product-card__sku">SKU: {{ $p['sku'] }} · {{ $p['cat'] }}</div>
                            <a href="/catalogo/{{ $i+1 }}" style="text-decoration:none;">
                                <div class="mac-product-card__name">{{ $p['name'] }}</div>
                            </a>
                            <div style="display:flex;align-items:baseline;gap:10px;">
                                <div class="mac-product-card__price">{{ $p['price'] }}</div>
                                @if($p['orig'])
                                <div style="font-size:13px;color:var(--macuin-muted);text-decoration:line-through;">{{ $p['orig'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mac-product-card__footer">
                            <span class="mac-badge {{ $badgeMap[$p['badge']]['class'] }}">{{ $badgeMap[$p['badge']]['label'] }}</span>
                            @if($p['badge'] !== 'out')
                            <a href="/carrito" class="mac-btn mac-btn-primary mac-btn-sm">
                                <i class="fas fa-cart-plus"></i>
                            </a>
                            @else
                            <span class="mac-btn mac-btn-ghost mac-btn-sm" style="cursor:default;opacity:.5;">Agotado</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
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
