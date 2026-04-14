@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    .checkout-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 32px;
        align-items: start;
    }
    .checkout-section {
        background: #fff;
        border: 1px solid var(--macuin-gray);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .checkout-section__header {
        background: var(--macuin-white);
        padding: 14px 20px;
        border-bottom: 1px solid var(--macuin-gray);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .checkout-section__num {
        width: 26px; height: 26px;
        background: var(--macuin-red); color: #fff;
        border-radius: 50%;
        font-family: 'Oswald', sans-serif;
        font-size: 13px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .checkout-section__title {
        font-family: 'Oswald', sans-serif;
        font-size: 14px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .08em;
        color: var(--macuin-text);
    }
    .checkout-section__body { padding: 20px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .order-summary { background: #fff; border: 1px solid var(--macuin-gray); border-radius: 8px; overflow: hidden; position: sticky; top: 88px; }

    /* Método de pago */
    .pay-option { display:flex; align-items:center; gap:14px; padding:14px 16px; border:2px solid var(--macuin-gray); border-radius:8px; cursor:pointer; margin-bottom:10px; transition:border-color .2s, background .2s; }
    .pay-option:hover { border-color:var(--macuin-red); }
    .pay-option input[type=radio] { accent-color:var(--macuin-red); width:17px; height:17px; flex-shrink:0; }
    .pay-option.selected { border-color:var(--macuin-red); background:#fff5f6; }
    .pay-option__icon { width:36px; height:36px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .pay-sub { margin-top:14px; padding:18px; border:1px solid var(--macuin-gray); border-radius:8px; background:#fafafa; display:none; }
    .pay-sub.active { display:block; }
    .card-input-wrap { position:relative; }
    .card-input-wrap .card-brand { position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:11px; font-weight:700; color:var(--macuin-muted); letter-spacing:.05em; }
    .transfer-box { background:var(--macuin-white); border:1px solid var(--macuin-gray); border-radius:8px; overflow:hidden; }
    .transfer-box__header { background:var(--macuin-red); padding:10px 16px; display:flex; align-items:center; gap:8px; }
    .transfer-box__header span { font-family:'Oswald',sans-serif; font-size:11px; color:#fff; text-transform:uppercase; letter-spacing:.1em; }
    .transfer-box__body { padding:4px 16px 8px; }
    .transfer-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--macuin-gray); font-size:13px; }
    .transfer-row:last-child { border-bottom:none; }
    .transfer-row__label { color:var(--macuin-muted); font-size:12px; font-weight:500; }
    .transfer-row__val { font-family:'JetBrains Mono',monospace; font-weight:600; font-size:13px; color:var(--macuin-text); }
    .credit-info { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:16px; }
    .credit-info.over { background:#fff5f5; border-color:#fecaca; }

    @media (max-width: 900px) {
        .checkout-layout { grid-template-columns: 1fr; }
        .order-summary { position: static; }
        .form-grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Sección: Header --}}
<div style="background:var(--macuin-dark);padding:28px 0;">
    <div class="mac-container">
        <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-steel);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
            <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>
            <a href="/carrito" style="color:var(--macuin-steel);text-decoration:none;">Carrito</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>Checkout
        </p>
        <h1 style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:#fff;text-transform:uppercase;">
            <i class="fas fa-lock" style="color:var(--macuin-red);margin-right:10px;font-size:20px;"></i>Finalizar Pedido
        </h1>
    </div>
</div>

<section style="padding:40px 0 60px;background:var(--macuin-white);">
    <div class="mac-container">
        <form method="POST" action="/checkout">
            @csrf
            <div class="checkout-layout">

                {{-- Columna Izquierda --}}
                <div>

                    {{-- 1. Datos del cliente --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">1</div>
                            <div class="checkout-section__title">Datos del Cliente</div>
                        </div>
                        <div class="checkout-section__body">
                            <div class="form-grid-2">
                                <div class="mac-form-group">
                                    <label class="mac-label" for="co-name">Nombre(s)</label>
                                    <div class="mac-input-icon">
                                        <i class="mac-input-icon__icon fas fa-user"></i>
                                        <input type="text" id="co-name" name="name" class="mac-input" value="{{ $usuario['nombre'] ?? '' }}" placeholder="Nombre(s)" required>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label" for="co-apellidos">Apellidos</label>
                                    <div class="mac-input-icon">
                                        <i class="mac-input-icon__icon fas fa-user"></i>
                                        <input type="text" id="co-apellidos" name="apellidos" class="mac-input" value="{{ $usuario['apellidos'] ?? '' }}" placeholder="Apellidos" required>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label" for="co-email">Email</label>
                                    <div class="mac-input-icon">
                                        <i class="mac-input-icon__icon fas fa-envelope"></i>
                                        <input type="email" id="co-email" name="email" class="mac-input" value="{{ $usuario['email'] ?? '' }}" placeholder="correo@ejemplo.com" required>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label" for="co-phone">Teléfono</label>
                                    <div class="mac-input-icon">
                                        <i class="mac-input-icon__icon fas fa-phone"></i>
                                        <input type="tel" id="co-phone" name="phone" class="mac-input" value="{{ $usuario['telefono'] ?? '' }}" placeholder="Ej. 449-123-4567" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Método de pago --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">2</div>
                            <div class="checkout-section__title">Método de Pago</div>
                        </div>
                        <div class="checkout-section__body">
                            @error('metodo_pago')
                                <p style="color:#C41230;font-size:13px;margin-bottom:12px;"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>{{ $message }}</p>
                            @enderror

                            {{-- Opciones de pago --}}
                            @php $metodoOld = old('metodo_pago', 'tarjeta'); @endphp

                            <label class="pay-option {{ $metodoOld === 'tarjeta' ? 'selected' : '' }}" id="lbl-tarjeta">
                                <input type="radio" name="metodo_pago" value="tarjeta" {{ $metodoOld === 'tarjeta' ? 'checked' : '' }}>
                                <div class="pay-option__icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-credit-card"></i></div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:14px;">Tarjeta de crédito / débito</div>
                                    <div style="font-size:12px;color:var(--macuin-muted);">VISA, Mastercard, American Express</div>
                                </div>
                                @if($descuento > 0)
                                <span style="font-size:11px;font-weight:700;color:#16a34a;background:#f0fdf4;padding:3px 8px;border-radius:4px;">{{ $descuento }}% DESC</span>
                                @endif
                            </label>

                            <label class="pay-option {{ $metodoOld === 'transferencia' ? 'selected' : '' }}" id="lbl-transferencia">
                                <input type="radio" name="metodo_pago" value="transferencia" {{ $metodoOld === 'transferencia' ? 'checked' : '' }}>
                                <div class="pay-option__icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-university"></i></div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:14px;">Transferencia bancaria / SPEI</div>
                                    <div style="font-size:12px;color:var(--macuin-muted);">Pago en 24–48 hrs hábiles</div>
                                </div>
                                @if($descuento > 0)
                                <span style="font-size:11px;font-weight:700;color:#16a34a;background:#f0fdf4;padding:3px 8px;border-radius:4px;">{{ $descuento }}% DESC</span>
                                @endif
                            </label>

                            <label class="pay-option {{ $metodoOld === 'credito_macuin' ? 'selected' : '' }}" id="lbl-credito">
                                <input type="radio" name="metodo_pago" value="credito_macuin" {{ $metodoOld === 'credito_macuin' ? 'checked' : '' }}>
                                <div class="pay-option__icon" style="background:#fff5f6; color:var(--macuin-red);"><i class="fas fa-hand-holding-usd"></i></div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:14px;">Crédito MACUIN</div>
                                    <div style="font-size:12px;color:var(--macuin-muted);">Disponible: ${{ number_format($limiteCredito, 2) }}</div>
                                </div>
                            </label>

                            {{-- Sub-sección: Tarjeta --}}
                            <div class="pay-sub {{ $metodoOld === 'tarjeta' ? 'active' : '' }}" id="sub-tarjeta">
                                <div class="form-grid-2" style="margin-bottom:14px;">
                                    <div class="mac-form-group" style="grid-column:1/-1;">
                                        <label class="mac-label">Número de tarjeta</label>
                                        <div class="card-input-wrap">
                                            <input type="text" id="card-number" class="mac-input" placeholder="0000 0000 0000 0000" maxlength="19" autocomplete="cc-number">
                                            <span class="card-brand" id="card-brand-label"></span>
                                        </div>
                                    </div>
                                    <div class="mac-form-group">
                                        <label class="mac-label">Vencimiento</label>
                                        <input type="text" id="card-expiry" class="mac-input" placeholder="MM/AA" maxlength="5" autocomplete="cc-exp">
                                    </div>
                                    <div class="mac-form-group">
                                        <label class="mac-label">CVV</label>
                                        <input type="text" id="card-cvv" class="mac-input" placeholder="123" maxlength="4" autocomplete="cc-csc">
                                    </div>
                                    <div class="mac-form-group" style="grid-column:1/-1;">
                                        <label class="mac-label">Nombre en la tarjeta</label>
                                        <input type="text" id="card-name" class="mac-input" placeholder="Como aparece en la tarjeta" autocomplete="cc-name">
                                    </div>
                                </div>
                                <p style="font-size:11px;color:var(--macuin-muted);"><i class="fas fa-lock" style="color:#16a34a;margin-right:4px;"></i>Tus datos están protegidos con cifrado SSL de 256 bits.</p>
                            </div>

                            {{-- Sub-sección: Transferencia --}}
                            <div class="pay-sub {{ $metodoOld === 'transferencia' ? 'active' : '' }}" id="sub-transferencia">
                                <p style="font-size:13px;color:var(--macuin-muted);margin-bottom:12px;">Realiza tu transferencia a la siguiente cuenta y envía tu comprobante a <strong>pagos@macuin.mx</strong></p>
                                <div class="transfer-box">
                                    <div class="transfer-box__header">
                                        <i class="fas fa-university" style="color:#fff;font-size:13px;"></i>
                                        <span>Datos de transferencia</span>
                                    </div>
                                    <div class="transfer-box__body">
                                        @foreach([
                                            ['Beneficiario','MACUIN Autopartes y Distribución S.A. de C.V.'],
                                            ['Banco','BBVA México'],
                                            ['CLABE','012 310 001 2345 678 90'],
                                            ['Cuenta','1234 5678 90'],
                                            ['Concepto','Pedido MACUIN'],
                                        ] as [$label, $val])
                                        <div class="transfer-row">
                                            <span class="transfer-row__label">{{ $label }}</span>
                                            <span class="transfer-row__val">{{ $val }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Sub-sección: Crédito MACUIN --}}
                            <div class="pay-sub {{ $metodoOld === 'credito_macuin' ? 'active' : '' }}" id="sub-credito">
                                <div class="credit-info" id="credit-info-box">
                                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                                        <i class="fas fa-info-circle" style="color:#16a34a;font-size:18px;"></i>
                                        <span style="font-weight:600;font-size:14px;color:#15803d;">Crédito disponible</span>
                                    </div>
                                    <p style="font-size:13px;color:#166534;margin:0;">Tu límite de Crédito MACUIN es de <strong>${{ number_format($limiteCredito, 2) }}</strong>. Si el total de tu pedido supera este monto, la compra no podrá procesarse con este método.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Dirección de envío --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">3</div>
                            <div class="checkout-section__title">Dirección de Envío</div>
                        </div>
                        <div class="checkout-section__body">
                            @error('api')
                                <p style="color:#C41230;font-size:13px;margin-bottom:12px;"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>{{ $message }}</p>
                            @enderror
                            <div class="mac-form-group">
                                <label class="mac-label">Calle y número</label>
                                <div class="mac-input-icon">
                                    <i class="mac-input-icon__icon fas fa-map-marker-alt"></i>
                                    <input type="text" name="calle" class="mac-input"
                                        placeholder="Av. López Mateos #1234, Col. Centro"
                                        value="{{ old('calle', $perfil['calle'] ?? '') }}" required>
                                </div>
                            </div>
                            <div class="form-grid-2">
                                <div class="mac-form-group">
                                    <label class="mac-label">Ciudad</label>
                                    <input type="text" name="ciudad" class="mac-input"
                                        placeholder="Querétaro"
                                        value="{{ old('ciudad', $perfil['ciudad'] ?? '') }}" required>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Estado</label>
                                    @php $estadoSel = old('estado', $perfil['estado_geo'] ?? ''); @endphp
                                    <select name="estado" class="mac-input" required>
                                        <option value="">-- Seleccionar --</option>
                                        @foreach(['QRO'=>'Querétaro','AGS'=>'Aguascalientes','CDMX'=>'Ciudad de México','JAL'=>'Jalisco','NL'=>'Nuevo León','GTO'=>'Guanajuato','COAH'=>'Coahuila','CHIH'=>'Chihuahua','SON'=>'Sonora'] as $val => $label)
                                        <option value="{{ $val }}" {{ $estadoSel === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Código Postal</label>
                                    <input type="text" name="cp" class="mac-input" placeholder="20000" maxlength="5" pattern="[0-9]{5}" value="{{ old('cp', $perfil['cp'] ?? '') }}" required>
                                    @error('cp')
                                        <p style="color:#C41230;font-size:12px;margin-top:4px;"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Referencias</label>
                                    <input type="text" name="refs" class="mac-input" placeholder="Entre calles, color de casa..." value="{{ old('refs', $perfil['referencia'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Método de envío --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">4</div>
                            <div class="checkout-section__title">Método de Envío</div>
                        </div>
                        <div class="checkout-section__body">
                            @foreach([['express','Envío Express (24 hrs)','fas fa-bolt','$0','Gratis por compra mayor a $1,500'],['estandar','Envío Estándar (3–5 días)','fas fa-truck','$0','Gratis en pedidos calificados'],['recoger','Recoger en sucursal','fas fa-store','$0','Querétaro, Centro']] as [$val,$label,$icon,$precio,$desc])
                            <label style="
                                display:flex;align-items:center;gap:14px;
                                padding:14px;border:2px solid var(--macuin-gray);
                                border-radius:6px;cursor:pointer;margin-bottom:10px;
                                transition:border-color .2s;
                            " onmouseover="this.style.borderColor='var(--macuin-red)'" onmouseout="this.style.borderColor='var(--macuin-gray)'">
                                <input type="radio" name="shipping" value="{{ $val }}" {{ $loop->first ? 'checked' : '' }} style="accent-color:var(--macuin-red);width:16px;height:16px;flex-shrink:0;">
                                <i class="fas {{ $icon }}" style="color:var(--macuin-red);font-size:18px;flex-shrink:0;width:20px;text-align:center;"></i>
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:14px;color:var(--macuin-text);">{{ $label }}</div>
                                    <div style="font-size:12px;color:var(--macuin-muted);">{{ $desc }}</div>
                                </div>
                                <div style="font-family:'Oswald',sans-serif;font-weight:700;color:#16A34A;">{{ $precio === '$0' ? 'Gratis' : $precio }}</div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- 5. Notas --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">5</div>
                            <div class="checkout-section__title">Notas del Pedido (opcional)</div>
                        </div>
                        <div class="checkout-section__body">
                            <textarea name="notes" rows="3" class="mac-input" placeholder="Instrucciones especiales, horario de entrega preferido..." style="resize:vertical;"></textarea>
                        </div>
                    </div>

                </div>

                {{-- Resumen del Pedido (sticky) --}}
                <div>
                    <div class="order-summary">
                        <div style="background:var(--macuin-dark);padding:16px 20px;">
                            <h3 style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:#fff;text-transform:uppercase;margin:0;">Tu Pedido</h3>
                        </div>
                        <div style="padding:20px;">
                            {{-- Items del carrito --}}
                            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:16px;">
                                @forelse($carrito as $item)
                                <div style="display:flex;justify-content:space-between;gap:12px;font-size:13px;">
                                    <span style="color:var(--macuin-muted);flex:1;line-height:1.4;">
                                        <strong style="color:var(--macuin-text);">x{{ $item['cantidad'] }}</strong> {{ $item['nombre'] }}
                                    </span>
                                    <span style="font-weight:600;color:var(--macuin-text);white-space:nowrap;">
                                        ${{ number_format($item['precio'] * $item['cantidad'], 2) }}
                                    </span>
                                </div>
                                @empty
                                <p style="font-size:13px;color:var(--macuin-muted);">El carrito está vacío.</p>
                                @endforelse
                            </div>

                            <div style="border-top:1px solid var(--macuin-gray);padding-top:14px;display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
                                <div style="display:flex;justify-content:space-between;font-size:13px;">
                                    <span style="color:var(--macuin-muted);">Subtotal</span>
                                    <span style="font-weight:600;">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                @if($descuento > 0)
                                <div id="descuento-row" style="display:flex;justify-content:space-between;font-size:13px;">
                                    <span style="color:#16a34a;"><i class="fas fa-tag" style="margin-right:4px;"></i>Descuento ({{ $descuento }}%)</span>
                                    <span style="font-weight:600;color:#16a34a;">-${{ number_format($subtotal * $descuento / 100, 2) }}</span>
                                </div>
                                @endif
                                <div style="display:flex;justify-content:space-between;font-size:13px;">
                                    <span style="color:var(--macuin-muted);">Envío</span>
                                    <span style="font-weight:600;color:#16A34A;">Gratis</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:13px;">
                                    <span style="color:var(--macuin-muted);">IVA (16%)</span>
                                    <span style="font-weight:600;" id="iva-display">${{ number_format($iva, 2) }}</span>
                                </div>
                            </div>

                            <div style="
                                background:var(--macuin-white);border-radius:6px;
                                padding:14px;margin-bottom:20px;
                                display:flex;justify-content:space-between;align-items:center;
                            ">
                                <span style="font-family:'Oswald',sans-serif;font-size:15px;font-weight:700;text-transform:uppercase;">TOTAL</span>
                                <span style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:var(--macuin-red);" id="total-display">${{ number_format($total, 2) }}</span>
                            </div>

                            <button type="submit" class="mac-btn mac-btn-primary mac-btn-block mac-btn-lg" style="margin-bottom:12px;">
                                <i class="fas fa-check-circle"></i>
                                CONFIRMAR PEDIDO
                            </button>

                            <div style="text-align:center;">
                                <p style="font-size:11px;color:var(--macuin-muted);">
                                    <i class="fas fa-shield-alt" style="color:#16A34A;margin-right:4px;"></i>
                                    Pedido procesado de forma segura
                                </p>
                                <div style="display:flex;justify-content:center;gap:8px;margin-top:8px;flex-wrap:wrap;">
                                    @foreach(['VISA','MC','OXXO','SPEI'] as $p)
                                    <span style="
                                        font-family:'Oswald',sans-serif;font-size:10px;font-weight:600;
                                        padding:2px 7px;border:1px solid var(--macuin-gray);border-radius:3px;
                                        color:var(--macuin-muted);
                                    ">{{ $p }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
(function () {
    // ── Datos del resumen ──────────────────────────────────────────────────
    const subtotal    = {{ $subtotal }};
    const descuentoPct = {{ $descuento }};
    const subtotalDesc = subtotal * (1 - descuentoPct / 100);
    const ivaDesc      = Math.round(subtotalDesc * 0.16 * 100) / 100;
    const totalDesc    = Math.round((subtotalDesc + ivaDesc) * 100) / 100;
    const ivaBase      = Math.round(subtotal * 0.16 * 100) / 100;
    const totalBase    = Math.round((subtotal + ivaBase) * 100) / 100;

    const fmt = n => '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // ── Elementos del DOM ──────────────────────────────────────────────────
    const radios        = document.querySelectorAll('input[name="metodo_pago"]');
    const subs          = { tarjeta: 'sub-tarjeta', transferencia: 'sub-transferencia', credito_macuin: 'sub-credito' };
    const lbls          = { tarjeta: 'lbl-tarjeta', transferencia: 'lbl-transferencia', credito_macuin: 'lbl-credito' };
    const descRow       = document.getElementById('descuento-row');
    const ivaDisplay    = document.getElementById('iva-display');
    const totalDisplay  = document.getElementById('total-display');

    // ── Campos de tarjeta (solo requeridos cuando esa sección está activa) ─
    const cardFields = ['card-number', 'card-expiry', 'card-cvv', 'card-name'];

    function applyCardRequired(active) {
        cardFields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.required = active;
        });
    }

    // ── Actualizar resumen y secciones ─────────────────────────────────────
    function update(metodo) {
        // Mostrar/ocultar sub-secciones
        Object.entries(subs).forEach(([key, subId]) => {
            const el = document.getElementById(subId);
            if (el) el.classList.toggle('active', key === metodo);
        });
        // Resaltar opción seleccionada
        Object.entries(lbls).forEach(([key, lblId]) => {
            const el = document.getElementById(lblId);
            if (el) el.classList.toggle('selected', key === metodo);
        });
        // Campos de tarjeta requeridos solo si está seleccionada
        applyCardRequired(metodo === 'tarjeta');

        // Actualizar totales en resumen
        const aplicaDescuento = (metodo === 'tarjeta' || metodo === 'transferencia') && descuentoPct > 0;
        if (descRow)     descRow.style.display    = aplicaDescuento ? 'flex' : 'none';
        if (ivaDisplay)  ivaDisplay.textContent   = aplicaDescuento ? fmt(ivaDesc)   : fmt(ivaBase);
        if (totalDisplay) totalDisplay.textContent = aplicaDescuento ? fmt(totalDesc) : fmt(totalBase);
    }

    // ── Escuchar cambios ───────────────────────────────────────────────────
    radios.forEach(r => r.addEventListener('change', () => update(r.value)));

    // ── Estado inicial ─────────────────────────────────────────────────────
    const checked = document.querySelector('input[name="metodo_pago"]:checked');
    if (checked) update(checked.value);

    // ── Formateo automático número de tarjeta ──────────────────────────────
    const cardNum = document.getElementById('card-number');
    if (cardNum) {
        cardNum.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 16);
            // Detectar marca
            const brand = document.getElementById('card-brand-label');
            if (brand) {
                if (/^4/.test(v))      brand.textContent = 'VISA';
                else if (/^5[1-5]/.test(v)) brand.textContent = 'MC';
                else if (/^3[47]/.test(v))  brand.textContent = 'AMEX';
                else brand.textContent = '';
            }
            this.value = v.replace(/(.{4})/g, '$1 ').trim();
        });
    }

    // ── Formateo MM/AA vencimiento ─────────────────────────────────────────
    const cardExp = document.getElementById('card-expiry');
    if (cardExp) {
        cardExp.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 3) v = v.substring(0, 2) + '/' + v.substring(2);
            this.value = v;
        });
    }

    // ── Solo dígitos en CVV ────────────────────────────────────────────────
    const cardCvv = document.getElementById('card-cvv');
    if (cardCvv) {
        cardCvv.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }
})();
</script>
@endpush

@endsection
