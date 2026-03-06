@extends('layouts.app')

@section('title', 'Mi Carrito')

@push('styles')
<style>
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 32px;
        align-items: start;
    }
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th {
        font-family: 'Oswald', sans-serif;
        font-size: 12px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .1em;
        color: var(--macuin-muted);
        padding: 12px 16px;
        border-bottom: 2px solid var(--macuin-gray);
        text-align: left;
        white-space: nowrap;
    }
    .cart-table td { padding: 16px; border-bottom: 1px solid var(--macuin-gray); vertical-align: middle; }
    .cart-table tr:last-child td { border-bottom: none; }
    .cart-table tr:hover td { background: var(--macuin-white); }

    .cart-qty { display: flex; align-items: center; border: 1px solid var(--macuin-gray); border-radius: 4px; overflow: hidden; width: fit-content; }
    .cart-qty-btn { width: 32px; height: 32px; background: none; border: none; cursor: pointer; font-size: 12px; transition: background .15s; }
    .cart-qty-btn:hover { background: var(--macuin-gray); }
    .cart-qty-inp { width: 44px; height: 32px; border: none; border-left: 1px solid var(--macuin-gray); border-right: 1px solid var(--macuin-gray); text-align: center; font-family: 'Oswald', sans-serif; font-size: 15px; outline: none; }

    .cart-summary {
        background: #fff;
        border: 1px solid var(--macuin-gray);
        border-radius: 8px;
        overflow: hidden;
        position: sticky;
        top: 88px;
    }

    @media (max-width: 900px) {
        .cart-layout { grid-template-columns: 1fr; }
        .cart-summary { position: static; }
        .cart-table td:nth-child(2), .cart-table th:nth-child(2) { display: none; }
    }
</style>
@endpush

@section('content')

{{-- Sección: Header --}}
<div style="background:var(--macuin-dark);padding:28px 0;">
    <div class="mac-container">
        <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-steel);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
            <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>Mi Carrito
        </p>
        <h1 style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:#fff;text-transform:uppercase;">
            <i class="fas fa-shopping-cart" style="color:var(--macuin-red);margin-right:10px;"></i>Mi Carrito
            <span style="font-size:16px;font-weight:400;color:var(--macuin-steel);margin-left:10px;">3 artículos</span>
        </h1>
    </div>
</div>

<section style="padding:40px 0 60px;background:var(--macuin-white);">
    <div class="mac-container">
        <div class="cart-layout">

            {{-- Tabla del carrito --}}
            <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
                <table class="cart-table">
                    <thead>
                        <tr style="background:var(--macuin-white);">
                            <th>Autoparte</th>
                            <th>SKU</th>
                            <th>Precio Unit.</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $items = [
                                ['sku'=>'FRN-001','name'=>'Pastillas de Freno Delanteras Premium Brembo','price'=>485,'qty'=>1,'badge'=>'available','img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=120&q=80'],
                                ['sku'=>'MOT-034','name'=>'Filtro de Aceite Universal Bosch','price'=>189,'qty'=>2,'badge'=>'available','img'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=120&q=80'],
                                ['sku'=>'SUS-112','name'=>'Amortiguador Delantero Gabriel Ultra','price'=>1240,'qty'=>1,'badge'=>'low','img'=>'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=120&q=80'],
                            ];
                        @endphp
                        @foreach($items as $i => $item)
                        <tr id="row-{{ $i }}">
                            {{-- Producto --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:14px;">
                                    <div style="width:72px;height:72px;flex-shrink:0;border:1px solid var(--macuin-gray);border-radius:6px;overflow:hidden;">
                                        <img src="{{ $item['img'] }}" alt="{{ $item['name'] }}" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                    <div>
                                        <a href="/catalogo/1" style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:600;text-transform:uppercase;color:var(--macuin-text);text-decoration:none;display:block;margin-bottom:4px;">
                                            {{ $item['name'] }}
                                        </a>
                                        @php
                                            $bm = ['available'=>['label'=>'Disponible','class'=>'mac-badge--available'],'low'=>['label'=>'Poco stock','class'=>'mac-badge--low']];
                                        @endphp
                                        <span class="mac-badge {{ $bm[$item['badge']]['class'] }}">{{ $bm[$item['badge']]['label'] }}</span>
                                    </div>
                                </div>
                            </td>
                            {{-- SKU --}}
                            <td>
                                <span class="mac-mono" style="font-size:12px;color:var(--macuin-muted);">{{ $item['sku'] }}</span>
                            </td>
                            {{-- Precio --}}
                            <td>
                                <span style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:600;color:var(--macuin-text);">
                                    ${{ number_format($item['price'], 0) }}
                                </span>
                            </td>
                            {{-- Cantidad --}}
                            <td>
                                <div class="cart-qty">
                                    <button class="cart-qty-btn" onclick="changeCartQty({{ $i }}, -1)"><i class="fas fa-minus" style="font-size:10px;"></i></button>
                                    <input type="number" class="cart-qty-inp" id="cqty-{{ $i }}" value="{{ $item['qty'] }}" min="1" max="99">
                                    <button class="cart-qty-btn" onclick="changeCartQty({{ $i }}, 1)"><i class="fas fa-plus" style="font-size:10px;"></i></button>
                                </div>
                            </td>
                            {{-- Subtotal --}}
                            <td>
                                <span id="sub-{{ $i }}" style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:var(--macuin-red);">
                                    ${{ number_format($item['price'] * $item['qty'], 0) }}
                                </span>
                            </td>
                            {{-- Eliminar --}}
                            <td>
                                <button onclick="removeItem({{ $i }})" style="
                                    background:none;border:none;cursor:pointer;
                                    color:var(--macuin-muted);font-size:16px;padding:4px;
                                    transition:color .2s;
                                " title="Eliminar" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='var(--macuin-muted)'">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Resumen del Pedido --}}
            <div class="cart-summary">
                <div style="background:var(--macuin-dark);padding:16px 20px;">
                    <h3 style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:#fff;text-transform:uppercase;margin:0;">Resumen del Pedido</h3>
                </div>
                <div style="padding:20px;">
                    <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span style="color:var(--macuin-muted);">Subtotal (4 piezas)</span>
                            <span style="font-weight:600;color:var(--macuin-text);">$2,103</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span style="color:var(--macuin-muted);">Envío estimado</span>
                            <span style="font-weight:600;color:#16A34A;">Gratis</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:14px;">
                            <span style="color:var(--macuin-muted);">IVA (16%)</span>
                            <span style="font-weight:600;color:var(--macuin-text);">$336.48</span>
                        </div>
                    </div>
                    <div style="border-top:2px solid var(--macuin-gray);padding-top:14px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:baseline;">
                        <span style="font-family:'Oswald',sans-serif;font-size:15px;font-weight:700;text-transform:uppercase;color:var(--macuin-text);">Total</span>
                        <span style="font-family:'Oswald',sans-serif;font-size:26px;font-weight:700;color:var(--macuin-red);">$2,439.48</span>
                    </div>

                    {{-- Cupón --}}
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;gap:8px;">
                            <input type="text" placeholder="Código de descuento" style="
                                flex:1;padding:10px 12px;
                                border:1px solid var(--macuin-gray);border-radius:4px;
                                font-family:'DM Sans',sans-serif;font-size:13px;outline:none;
                            " onfocus="this.style.borderColor='var(--macuin-red)'" onblur="this.style.borderColor='var(--macuin-gray)'">
                            <button class="mac-btn mac-btn-outline mac-btn-sm">Aplicar</button>
                        </div>
                    </div>

                    <a href="/checkout" class="mac-btn mac-btn-primary mac-btn-block mac-btn-lg" style="margin-bottom:10px;">
                        <i class="fas fa-lock" style="font-size:12px;"></i>
                        PROCEDER AL CHECKOUT
                    </a>
                    <a href="/catalogo" class="mac-btn mac-btn-ghost mac-btn-block mac-btn-sm">
                        <i class="fas fa-arrow-left" style="font-size:11px;"></i>
                        Continuar Comprando
                    </a>

                    <div style="margin-top:16px;padding-top:12px;border-top:1px solid var(--macuin-gray);text-align:center;">
                        <p style="font-size:11px;color:var(--macuin-muted);">
                            <i class="fas fa-shield-alt" style="color:#16A34A;margin-right:4px;"></i>
                            Compra segura garantizada
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    const prices = [485, 189, 1240];
    function changeCartQty(i, delta) {
        const inp = document.getElementById('cqty-' + i);
        const v = Math.max(1, parseInt(inp.value) + delta);
        inp.value = v;
        document.getElementById('sub-' + i).textContent = '$' + (prices[i] * v).toLocaleString('es-MX');
    }
    function removeItem(i) {
        const row = document.getElementById('row-' + i);
        if (row) { row.style.opacity = '0'; row.style.transition = 'opacity .3s'; setTimeout(()=>row.remove(), 300); }
    }
</script>
@endpush
