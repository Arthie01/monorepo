@extends('layouts.app')

@section('title', 'Inicio')

@push('styles')
<style>
    /* ── Hero ── */
    .mac-hero {
        background: var(--macuin-dark);
        min-height: 520px;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .mac-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=1400&q=80') center/cover no-repeat;
        opacity: .18;
    }
    .mac-hero__content {
        position: relative;
        z-index: 1;
        max-width: 1280px;
        margin: 0 auto;
        padding: 60px 24px;
        width: 100%;
    }

    /* ── Vehicle Selector Hero ── */
    .mac-vs-hero {
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 8px;
        padding: 20px 24px;
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        max-width: 700px;
        margin-top: 32px;
        backdrop-filter: blur(10px);
    }
    .mac-vs-hero__group { flex: 1; min-width: 130px; }
    .mac-vs-label {
        font-family: 'Oswald', sans-serif;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--macuin-steel);
        margin-bottom: 6px;
    }
    .mac-vs-select {
        width: 100%;
        padding: 10px 14px;
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 4px;
        color: #fff;
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
        appearance: none;
        -webkit-appearance: none;
    }
    .mac-vs-select:focus { border-color: var(--macuin-red); }
    .mac-vs-select option { background: #0D0D0D; }

    /* ── Category Grid ── */
    .mac-cat-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
    }
    @media (max-width: 1024px) { .mac-cat-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px)  { .mac-cat-grid { grid-template-columns: repeat(2, 1fr); } }

    /* ── Promo Banner ── */
    .mac-promo {
        background: var(--macuin-red);
        clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        padding: 64px 0 90px;
    }

    /* ── Swiper cards ── */
    .mac-swiper .swiper-slide { height: auto; }
    .mac-swiper .swiper-button-next,
    .mac-swiper .swiper-button-prev {
        color: var(--macuin-red);
        background: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }
    .mac-swiper .swiper-button-next::after,
    .mac-swiper .swiper-button-prev::after { font-size: 14px; font-weight: 700; }
    .mac-swiper .swiper-pagination-bullet-active { background: var(--macuin-red); }

    @media (max-width: 640px) {
        .mac-hero { min-height: 400px; }
        .mac-vs-hero { flex-direction: column; }
        .mac-vs-hero__group { min-width: 100%; }
    }
</style>
@endpush

@section('content')

{{-- Sección: Hero Banner --}}
<section class="mac-hero">
    <div class="mac-hero__content">
        <p style="font-family:'Oswald',sans-serif;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.15em;color:var(--macuin-red);margin-bottom:12px;">
            <i class="fas fa-bolt" style="margin-right:6px;"></i>Distribución Profesional de Autopartes
        </p>
        <h1 style="
            font-family:'Oswald',sans-serif;
            font-size:clamp(32px,5vw,64px);
            font-weight:700;
            color:#fff;
            text-transform:uppercase;
            line-height:1.05;
            letter-spacing:.02em;
            margin-bottom:16px;
        ">
            La autoparte<br>
            que necesitas,<br>
            <span style="color:var(--macuin-red);">cuando la necesitas</span>
        </h1>
        <p style="font-size:16px;color:#8B949E;max-width:480px;line-height:1.7;margin-bottom:8px;">
            +15,000 referencias disponibles. Envíos a todo México. Garantía en cada pieza.
        </p>

        {{-- Selector de Vehículo Hero --}}
        <div class="mac-vs-hero">
            <div class="mac-vs-hero__group">
                <div class="mac-vs-label"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i>Año</div>
                <select id="vs-year" class="mac-vs-select" onchange="updateVsMarca()">
                    <option value="">Seleccionar</option>
                    @for($y = 2024; $y >= 1995; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="mac-vs-hero__group">
                <div class="mac-vs-label"><i class="fas fa-car" style="margin-right:4px;"></i>Marca</div>
                <select id="vs-marca" class="mac-vs-select" onchange="updateVsModelo()">
                    <option value="">Seleccionar</option>
                    <option value="chevrolet">Chevrolet</option>
                    <option value="ford">Ford</option>
                    <option value="nissan">Nissan</option>
                    <option value="volkswagen">Volkswagen</option>
                    <option value="toyota">Toyota</option>
                    <option value="honda">Honda</option>
                    <option value="dodge">Dodge</option>
                    <option value="kia">Kia</option>
                    <option value="hyundai">Hyundai</option>
                    <option value="mazda">Mazda</option>
                </select>
            </div>
            <div class="mac-vs-hero__group">
                <div class="mac-vs-label"><i class="fas fa-cog" style="margin-right:4px;"></i>Modelo</div>
                <select id="vs-modelo" class="mac-vs-select">
                    <option value="">Seleccionar</option>
                </select>
            </div>
            <a href="/catalogo" class="mac-btn mac-btn-primary" style="flex-shrink:0;height:42px;align-self:flex-end;">
                <i class="fas fa-search"></i>
                BUSCAR PARTES
            </a>
        </div>
    </div>
</section>

{{-- Sección: Categorías Destacadas --}}
<section style="padding:60px 0;background:var(--macuin-white);">
    <div class="mac-container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px;gap:16px;flex-wrap:wrap;">
            <div>
                <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-red);text-transform:uppercase;letter-spacing:.12em;margin-bottom:6px;">Nuestro inventario</p>
                <h2 class="mac-section-title">Categorías <span>Principales</span></h2>
            </div>
            <a href="/catalogo" style="font-size:13px;color:var(--macuin-red);text-decoration:none;font-weight:600;white-space:nowrap;">Ver todo el catálogo →</a>
        </div>

        <div class="mac-cat-grid">
            @php
                $categorias = [
                    ['icon'=>'fa-cog',          'name'=>'Motor',            'count'=>'3,240'],
                    ['icon'=>'fa-car-crash',     'name'=>'Suspensión',       'count'=>'1,890'],
                    ['icon'=>'fa-circle-notch',  'name'=>'Frenos',           'count'=>'2,105'],
                    ['icon'=>'fa-bolt',          'name'=>'Sistema Eléctrico','count'=>'4,320'],
                    ['icon'=>'fa-sliders-h',     'name'=>'Transmisión',      'count'=>'1,560'],
                    ['icon'=>'fa-filter',        'name'=>'Filtros',          'count'=>'980'],
                ];
            @endphp
            @foreach($categorias as $cat)
            <a href="/catalogo?categoria={{ Str::slug($cat['name']) }}" class="mac-category-card">
                <div class="mac-category-card__icon">
                    <i class="fas {{ $cat['icon'] }}"></i>
                </div>
                <div class="mac-category-card__name">{{ $cat['name'] }}</div>
                <div style="font-size:11px;color:inherit;opacity:.7;">{{ $cat['count'] }} refs.</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Sección: Productos Destacados (Swiper) --}}
<section style="padding:60px 0;background:#fff;">
    <div class="mac-container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px;gap:16px;flex-wrap:wrap;">
            <div>
                <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-red);text-transform:uppercase;letter-spacing:.12em;margin-bottom:6px;">Los más vendidos</p>
                <h2 class="mac-section-title">Productos <span>Destacados</span></h2>
            </div>
            <a href="/catalogo" style="font-size:13px;color:var(--macuin-red);text-decoration:none;font-weight:600;white-space:nowrap;">Ver todos →</a>
        </div>

        <div class="swiper mac-swiper" id="mac-featured-swiper">
            <div class="swiper-wrapper">
                @forelse($autopartes as $a)
                <div class="swiper-slide">
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
                            <div class="mac-product-card__sku">SKU: {{ $a['sku'] }}</div>
                            <a href="/catalogo/{{ $a['id'] }}" style="text-decoration:none;">
                                <div class="mac-product-card__name">{{ $a['nombre'] }}</div>
                            </a>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
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
                                    'en_stock'   => ['label' => 'Disponible', 'class' => 'mac-badge--available'],
                                    'bajo_stock' => ['label' => 'Poco stock', 'class' => 'mac-badge--low'],
                                    default      => ['label' => 'Sin stock',  'class' => 'mac-badge--out'],
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
                                    <i class="fas fa-shopping-cart"></i> Agregar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="swiper-slide">
                    <p style="text-align:center;padding:40px;color:var(--macuin-muted);">Sin productos disponibles.</p>
                </div>
                @endforelse
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

{{-- Sección: Banner Promocional --}}
<section class="mac-promo">
    <div class="mac-container">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;">
            <div>
                <p style="font-family:'Oswald',sans-serif;font-size:12px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.15em;margin-bottom:12px;">Oferta de la semana</p>
                <h2 style="font-family:'Oswald',sans-serif;font-size:clamp(28px,4vw,48px);font-weight:700;color:#fff;text-transform:uppercase;line-height:1.1;margin-bottom:16px;">
                    Hasta <span style="color:rgba(255,255,255,.85);">30% OFF</span><br>en autopartes<br>de motor
                </h2>
                <p style="font-size:14px;color:rgba(255,255,255,.75);margin-bottom:28px;line-height:1.7;">
                    Filtros, bujías, bandas y más. Stock limitado, aprovecha ahora.
                </p>
                <a href="/catalogo?categoria=motor" class="mac-btn mac-btn-dark mac-btn-lg">
                    VER OFERTAS <i class="fas fa-arrow-right" style="margin-left:8px;"></i>
                </a>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                @foreach([['fa-shield-alt','Garantía','12 meses en todas las piezas'],['fa-shipping-fast','Envío','24-48 hrs a todo México'],['fa-headset','Soporte','Asesoría técnica gratuita'],['fa-sync','Devoluciones','30 días sin preguntas']] as [$icon,$title,$desc])
                <div style="background:rgba(255,255,255,.1);border-radius:8px;padding:20px;text-align:center;">
                    <i class="fas {{ $icon }}" style="font-size:24px;color:rgba(255,255,255,.85);margin-bottom:10px;display:block;"></i>
                    <div style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:600;color:#fff;margin-bottom:4px;text-transform:uppercase;">{{ $title }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,.65);line-height:1.5;">{{ $desc }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Sección: Marcas de Vehículos --}}
<section style="padding:48px 0;background:var(--macuin-white);">
    <div class="mac-container">
        <p style="text-align:center;font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:24px;">Compatibilidad con las principales marcas</p>
        <div style="display:flex;justify-content:center;align-items:center;gap:32px;flex-wrap:wrap;opacity:.6;">
            @foreach(['Chevrolet','Ford','Nissan','Volkswagen','Toyota','Honda','Dodge','Kia','Hyundai','Mazda'] as $brand)
            <span style="font-family:'Oswald',sans-serif;font-size:15px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--macuin-text);">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Swiper de productos destacados
    new Swiper('#mac-featured-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        pagination: { el: '.swiper-pagination', clickable: true },
        breakpoints: {
            640:  { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1280: { slidesPerView: 4 },
        }
    });

    // Selector de vehículo
    const vsModelos = {
        chevrolet:  ['Aveo','Beat','Trax','Equinox','Silverado','Cheyenne','Captiva','Spark'],
        ford:       ['Fiesta','Focus','Mustang','Ranger','F-150','Escape','Explorer','Lobo'],
        nissan:     ['Tsuru','Versa','Sentra','Altima','X-Trail','Kicks','NP300','Frontier'],
        volkswagen: ['Jetta','Golf','Vento','Tiguan','Passat','Polo','Amarok','Saveiro'],
        toyota:     ['Corolla','Camry','Hilux','RAV4','Fortuner','Yaris','Avanza','Prius'],
        honda:      ['Civic','Accord','CR-V','HR-V','Fit','City','Pilot','Ridgeline'],
        dodge:      ['Attitude','Journey','Durango','Charger','RAM 700','RAM 1500','Challenger'],
        kia:        ['Rio','Forte','Seltos','Sportage','Sorento','Stinger','Carnival'],
        hyundai:    ['Accent','Elantra','Tucson','Santa Fe','Creta','Ioniq','Venue'],
        mazda:      ['Mazda2','Mazda3','Mazda6','CX-3','CX-5','CX-30','MX-5'],
    };
    function updateVsModelo() {
        const marca = document.getElementById('vs-marca').value;
        const sel = document.getElementById('vs-modelo');
        sel.innerHTML = '<option value="">Seleccionar</option>';
        if (vsModelos[marca]) {
            vsModelos[marca].forEach(m => {
                const o = document.createElement('option');
                o.value = m.toLowerCase().replace(/\s/g,'-');
                o.textContent = m;
                sel.appendChild(o);
            });
        }
    }
</script>
@endpush
