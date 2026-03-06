@extends('layouts.app')

@section('title', 'Pastillas de Freno Delanteras Premium')

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
                    <img id="main-img-el" src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=85" alt="Pastillas de Freno" style="width:100%;height:100%;object-fit:contain;padding:20px;">
                </div>
                {{-- Thumbnails --}}
                <div class="thumb-grid">
                    @php
                        $thumbs = [
                            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=200&q=80',
                            'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=200&q=80',
                            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=200&q=80',
                            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=200&q=80',
                        ];
                    @endphp
                    @foreach($thumbs as $i => $t)
                    <button class="thumb-btn {{ $i===0?'active':'' }}" onclick="setImg(this,'{{ str_replace('200','700',$t) }}')">
                        <img src="{{ $t }}" alt="Vista {{ $i+1 }}">
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Info del Producto --}}
            <div>
                {{-- Encabezado --}}
                <div style="margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                        <span class="mac-badge mac-badge--new">Nuevo</span>
                        <span class="mac-badge mac-badge--available">Disponible</span>
                    </div>
                    <h1 style="font-family:'Oswald',sans-serif;font-size:clamp(20px,3vw,30px);font-weight:700;text-transform:uppercase;color:var(--macuin-text);line-height:1.2;margin-bottom:8px;">
                        Pastillas de Freno Delanteras Premium Brembo
                    </h1>
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <span class="mac-mono" style="font-size:13px;color:var(--macuin-muted);">SKU: FRN-001</span>
                        <span style="width:1px;height:14px;background:var(--macuin-gray);"></span>
                        <span style="font-size:13px;color:var(--macuin-muted);">Marca: <strong style="color:var(--macuin-text);">Brembo</strong></span>
                        <span style="width:1px;height:14px;background:var(--macuin-gray);"></span>
                        <div style="display:flex;gap:2px;">
                            @for($s=0;$s<5;$s++)<i class="fas fa-star" style="color:#F59E0B;font-size:13px;"></i>@endfor
                        </div>
                        <span style="font-size:12px;color:var(--macuin-muted);">(24 reseñas)</span>
                    </div>
                </div>

                {{-- Precio --}}
                <div style="
                    background:#fff;border:1px solid var(--macuin-gray);
                    border-radius:8px;padding:20px;margin-bottom:24px;
                ">
                    <div style="display:flex;align-items:baseline;gap:14px;margin-bottom:6px;">
                        <span style="font-family:'Oswald',sans-serif;font-size:36px;font-weight:700;color:var(--macuin-red);">$485</span>
                        <span style="font-size:18px;color:var(--macuin-muted);text-decoration:line-through;">$620</span>
                        <span style="
                            background:rgba(196,18,48,.1);color:var(--macuin-red);
                            font-family:'Oswald',sans-serif;font-size:13px;font-weight:700;
                            padding:2px 8px;border-radius:4px;
                        ">-22%</span>
                    </div>
                    <p style="font-size:12px;color:var(--macuin-muted);">IVA incluido · Stock: <strong style="color:#16A34A;">48 unidades</strong></p>
                </div>

                {{-- Descripción breve --}}
                <p style="font-size:14px;color:var(--macuin-muted);line-height:1.75;margin-bottom:24px;">
                    Pastillas de freno de alto rendimiento para uso diario y deportivo. Material cerámico de última generación que garantiza frenado limpio sin polvo, baja temperatura y larga vida útil. Compatible con los modelos indicados en las especificaciones.
                </p>

                {{-- Compatibilidad --}}
                <div style="
                    background:rgba(196,18,48,.05);border:1px solid rgba(196,18,48,.15);
                    border-radius:6px;padding:14px 18px;margin-bottom:24px;
                    display:flex;align-items:center;gap:12px;
                ">
                    <i class="fas fa-car" style="color:var(--macuin-red);font-size:18px;flex-shrink:0;"></i>
                    <div>
                        <div style="font-family:'Oswald',sans-serif;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--macuin-red);margin-bottom:2px;">Compatibilidad verificada</div>
                        <div style="font-size:13px;color:var(--macuin-muted);">Chevrolet Aveo 2008–2017 · Spark 2010–2019 · Beat 2020–2024</div>
                    </div>
                </div>

                {{-- Cantidad y Agregar --}}
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                    <div>
                        <div style="font-family:'Oswald',sans-serif;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--macuin-muted);margin-bottom:6px;">Cantidad</div>
                        <div class="qty-control">
                            <button class="qty-btn" onclick="changeQty(-1)"><i class="fas fa-minus" style="font-size:12px;"></i></button>
                            <input type="number" class="qty-input" id="qty" value="1" min="1" max="48">
                            <button class="qty-btn" onclick="changeQty(1)"><i class="fas fa-plus" style="font-size:12px;"></i></button>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <a href="/carrito" class="mac-btn mac-btn-primary mac-btn-block mac-btn-lg" style="min-width:200px;">
                            <i class="fas fa-shopping-cart"></i>
                            AGREGAR AL CARRITO
                        </a>
                    </div>
                </div>
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
                        <tr><td>Marca</td><td>Brembo</td></tr>
                        <tr><td>Material</td><td>Cerámico premium</td></tr>
                        <tr><td>Posición</td><td>Delantera</td></tr>
                        <tr><td>Espesor</td><td>14mm</td></tr>
                        <tr><td>Ancho</td><td>62mm</td></tr>
                        <tr><td>Largo</td><td>120mm</td></tr>
                        <tr><td>Temp. máx.</td><td>650°C</td></tr>
                        <tr><td>Certificación</td><td>ECE R90</td></tr>
                        <tr><td>Contenido</td><td>Par (2 pastillas)</td></tr>
                        <tr><td>Peso</td><td>0.85 kg</td></tr>
                    </table>
                </div>
            </div>
            <div>
                <h2 style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:700;text-transform:uppercase;margin-bottom:20px;padding-bottom:10px;border-bottom:3px solid var(--macuin-red);">
                    Vehículos <span style="color:var(--macuin-red);">Compatibles</span>
                </h2>
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
                    <table class="spec-table">
                        @foreach([['Chevrolet','Aveo','2008–2017'],['Chevrolet','Spark','2010–2019'],['Chevrolet','Beat','2020–2024'],['Chevrolet','Sonic','2012–2018'],['Buick','Encore','2013–2017'],['Opel','Corsa D','2006–2014']] as [$marca,$modelo,$anos])
                        <tr>
                            <td>{{ $marca }}</td>
                            <td>{{ $modelo }} ({{ $anos }})</td>
                        </tr>
                        @endforeach
                    </table>
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
