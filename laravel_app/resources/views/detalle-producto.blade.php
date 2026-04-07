@extends('layouts.app')

@section('title', isset($autoparte['nombre']) ? $autoparte['nombre'] : 'Detalle de Autoparte')

@push('styles')
<style>
    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 48px;
        align-items: start;
    }
    .thumb-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-top: 12px;
    }
    .thumb-btn {
        aspect-ratio: 1;
        border: 2px solid var(--macuin-gray);
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color .2s;
    }
    .thumb-btn:hover, .thumb-btn.active { border-color: var(--macuin-red); }
    .thumb-btn img { width: 100%; height: 100%; object-fit: cover; }

    .qty-control {
        display: flex;
        align-items: center;
        border: 2px solid var(--macuin-gray);
        border-radius: 4px;
        overflow: hidden;
        width: fit-content;
    }
    .qty-btn {
        width: 40px; height: 44px;
        background: none; border: none; cursor: pointer;
        font-size: 16px; color: var(--macuin-text);
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
    }
    .qty-btn:hover { background: var(--macuin-gray); }
    .qty-input {
        width: 60px; height: 44px;
        border: none; border-left: 2px solid var(--macuin-gray); border-right: 2px solid var(--macuin-gray);
        text-align: center;
        font-family: 'Oswald', sans-serif; font-size: 18px; font-weight: 600;
        outline: none;
    }

    .spec-table { width: 100%; border-collapse: collapse; }
    .spec-table tr:nth-child(even) td { background: var(--macuin-white); }
    .spec-table td {
        padding: 10px 14px; font-size: 13px;
        border-bottom: 1px solid var(--macuin-gray);
    }
    .spec-table td:first-child {
        font-weight: 600; color: var(--macuin-text);
        font-family: 'Oswald', sans-serif; text-transform: uppercase;
        letter-spacing: .05em; width: 40%; background: #fff !important;
    }
    .spec-table td:last-child { color: var(--macuin-muted); }

    @media (max-width: 900px) {
        .detail-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Sección: Breadcrumb --}}
<div style="background:var(--macuin-dark);padding:16px 0;">
    <div class="mac-container">
        <p style="font-size:13px;color:var(--macuin-steel);">
            <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 8px;"></i>
            <a href="/catalogo" style="color:var(--macuin-steel);text-decoration:none;">Catálogo</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 8px;"></i>
            <a href="/catalogo?categoria=frenos" style="color:var(--macuin-steel);text-decoration:none;">Frenos</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 8px;"></i>
            <span style="color:#fff;">Pastillas de Freno Delanteras Premium</span>
        </p>
    </div>
</div>

{{-- Sección: Detalle del Producto --}}
<section style="padding:48px 0;background:var(--macuin-white);">
    <div class="mac-container">
        <div class="detail-layout">

            {{-- Galería de Imágenes --}}
            <div>
                {{-- Imagen principal --}}
                <div id="main-img" style="
                    aspect-ratio:1;background:#fff;border-radius:8px;
                    border:1px solid var(--macuin-gray);overflow:hidden;
                ">
                    @if(!empty($autoparte['imagen']))
                        <img id="main-img-el" src="{{ $autoparte['imagen'] }}" alt="{{ $autoparte['nombre'] }}" style="width:100%;height:100%;object-fit:contain;padding:20px;">
                    @else
                        <div id="main-img-el" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f5f5f0;">
                            <i class="fas fa-image" style="font-size:64px;color:#ccc;"></i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info del Producto --}}
            <div>
                {{-- Encabezado --}}
                <div style="margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                        @php
                            $estadoBadge = match($autoparte['estado'] ?? '') {
                                'en_stock'   => ['label' => 'Disponible', 'class' => 'mac-badge--available'],
                                'bajo_stock' => ['label' => 'Poco stock', 'class' => 'mac-badge--low'],
                                default      => ['label' => 'Sin stock',  'class' => 'mac-badge--out'],
                            };
                        @endphp
                        <span class="mac-badge {{ $estadoBadge['class'] }}">{{ $estadoBadge['label'] }}</span>
                    </div>
                    <h1 style="font-family:'Oswald',sans-serif;font-size:clamp(20px,3vw,30px);font-weight:700;text-transform:uppercase;color:var(--macuin-text);line-height:1.2;margin-bottom:8px;">
                        {{ $autoparte['nombre'] }}
                    </h1>
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <span class="mac-mono" style="font-size:13px;color:var(--macuin-muted);">SKU: {{ $autoparte['sku'] }}</span>
                        <span style="width:1px;height:14px;background:var(--macuin-gray);"></span>
                        <span style="font-size:13px;color:var(--macuin-muted);">Marca: <strong style="color:var(--macuin-text);">{{ $autoparte['marca'] ?? '—' }}</strong></span>
                        <span style="width:1px;height:14px;background:var(--macuin-gray);"></span>
                        <span style="font-size:13px;color:var(--macuin-muted);">Categoría: <strong style="color:var(--macuin-text);">{{ $autoparte['categoria'] }}</strong></span>
                    </div>
                </div>

                {{-- Precio --}}
                <div style="
                    background:#fff;border:1px solid var(--macuin-gray);
                    border-radius:8px;padding:20px;margin-bottom:24px;
                ">
                    <div style="display:flex;align-items:baseline;gap:14px;margin-bottom:6px;">
                        <span style="font-family:'Oswald',sans-serif;font-size:36px;font-weight:700;color:var(--macuin-red);">
                            ${{ number_format($autoparte['precio'], 2) }}
                        </span>
                        @if(!empty($autoparte['precio_original']))
                        <span style="font-size:18px;color:var(--macuin-muted);text-decoration:line-through;">
                            ${{ number_format($autoparte['precio_original'], 2) }}
                        </span>
                        @endif
                    </div>
                    <p style="font-size:12px;color:var(--macuin-muted);">IVA incluido · Stock: <strong style="color:#16A34A;">{{ $autoparte['stock'] }} unidades</strong></p>
                </div>

                {{-- Aplicación / Compatibilidad --}}
                @if(!empty($autoparte['aplicacion']))
                <div style="
                    background:rgba(196,18,48,.05);border:1px solid rgba(196,18,48,.15);
                    border-radius:6px;padding:14px 18px;margin-bottom:24px;
                    display:flex;align-items:center;gap:12px;
                ">
                    <i class="fas fa-car" style="color:var(--macuin-red);font-size:18px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-family:'Oswald',sans-serif;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--macuin-red);margin-bottom:2px;">Compatibilidad verificada</div>
                        <div style="font-size:13px;color:var(--macuin-muted);">{{ $autoparte['aplicacion'] }}</div>
                    </div>
                </div>
                @endif

                {{-- Cantidad y Agregar --}}
                <form action="/carrito/agregar" method="POST">
                    @csrf
                    <input type="hidden" name="autoparte_id" value="{{ $autoparte['id'] }}">
                    <input type="hidden" name="nombre" value="{{ $autoparte['nombre'] }}">
                    <input type="hidden" name="precio" value="{{ $autoparte['precio'] }}">
                    <input type="hidden" name="imagen" value="{{ $autoparte['imagen'] ?? '' }}">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                        <div>
                            <div style="font-family:'Oswald',sans-serif;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--macuin-muted);margin-bottom:6px;">Cantidad</div>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" onclick="changeQty(-1)"><i class="fas fa-minus" style="font-size:12px;"></i></button>
                                <input type="number" class="qty-input" id="qty" name="cantidad" value="1" min="1" max="{{ $autoparte['stock'] }}">
                                <button type="button" class="qty-btn" onclick="changeQty(1)"><i class="fas fa-plus" style="font-size:12px;"></i></button>
                            </div>
                        </div>
                        <div style="flex:1;">
                            <button type="submit" class="mac-btn mac-btn-primary mac-btn-block mac-btn-lg" style="min-width:200px;"
                                {{ ($autoparte['estado'] ?? '') === 'sin_stock' ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart"></i>
                                AGREGAR AL CARRITO
                            </button>
                        </div>
                    </div>
                </form>
                <a href="/checkout" class="mac-btn mac-btn-dark mac-btn-block" style="margin-bottom:20px;">
                    <i class="fas fa-bolt"></i> COMPRAR AHORA
                </a>

                {{-- Beneficios --}}
                <div style="display:flex;gap:16px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--macuin-gray);">
                    @foreach([['fa-shield-alt','Garantía','12 meses'],['fa-truck','Envío','24–48 hrs'],['fa-sync','Devoluciones','30 días']] as [$ic,$tt,$dd])
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--macuin-muted);">
                        <i class="fas {{ $ic }}" style="color:var(--macuin-red);"></i>
                        <span><strong style="color:var(--macuin-text);">{{ $tt }}:</strong> {{ $dd }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sección: Especificaciones Técnicas --}}
        <div style="margin-top:56px;display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start;">
            <div>
                <h2 style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:700;text-transform:uppercase;margin-bottom:20px;padding-bottom:10px;border-bottom:3px solid var(--macuin-red);">
                    Especificaciones <span style="color:var(--macuin-red);">Técnicas</span>
                </h2>
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
                    <table class="spec-table">
                        <tr><td>SKU</td><td>{{ $autoparte['sku'] }}</td></tr>
                        <tr><td>Categoría</td><td>{{ $autoparte['categoria'] }}</td></tr>
                        <tr><td>Marca</td><td>{{ $autoparte['marca'] ?? '—' }}</td></tr>
                        <tr><td>Marca Vehículo</td><td>{{ $autoparte['marca_vehiculo'] ?? '—' }}</td></tr>
                        <tr><td>Modelo Vehículo</td><td>{{ $autoparte['modelo_vehiculo'] ?? '—' }}</td></tr>
                        <tr><td>Unidad</td><td>{{ $autoparte['unidad'] ?? '—' }}</td></tr>
                        <tr><td>Ubicación</td><td>{{ $autoparte['ubicacion'] ?? '—' }}</td></tr>
                        <tr><td>Stock mínimo</td><td>{{ $autoparte['stock_minimo'] ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>
            <div>
                <h2 style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:700;text-transform:uppercase;margin-bottom:20px;padding-bottom:10px;border-bottom:3px solid var(--macuin-red);">
                    Aplicación <span style="color:var(--macuin-red);">/ Compatibilidad</span>
                </h2>
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;padding:20px;">
                    @if(!empty($autoparte['aplicacion']))
                        <p style="font-size:14px;color:var(--macuin-muted);line-height:1.75;">{{ $autoparte['aplicacion'] }}</p>
                    @else
                        <p style="font-size:14px;color:var(--macuin-muted);">Sin información de compatibilidad disponible.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    function setImg(btn, src) {
        document.getElementById('main-img-el').src = src;
        document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    function changeQty(delta) {
        const inp = document.getElementById('qty');
        const v = parseInt(inp.value) + delta;
        if (v >= 1 && v <= 48) inp.value = v;
    }
</script>
@endpush
