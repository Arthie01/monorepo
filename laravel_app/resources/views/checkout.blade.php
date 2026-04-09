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

                    {{-- 2. Dirección de envío --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">2</div>
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
                                    <input type="text" name="refs" class="mac-input" placeholder="Entre calles, color de casa...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Método de envío --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">3</div>
                            <div class="checkout-section__title">Método de Envío</div>
                        </div>
                        <div class="checkout-section__body">
                            @foreach([['express','Envío Express (24 hrs)','fas fa-bolt','$0','Gratis por compra mayor a $1,500'],['estandar','Envío Estándar (3–5 días)','fas fa-truck','$0','Gratis en pedidos calificados'],['recoger','Recoger en sucursal','fas fa-store','$0','Aguascalientes, Centro']] as [$val,$label,$icon,$precio,$desc])
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

                    {{-- 4. Notas --}}
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <div class="checkout-section__num">4</div>
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
                                <div style="display:flex;justify-content:space-between;font-size:13px;">
                                    <span style="color:var(--macuin-muted);">Envío</span>
                                    <span style="font-weight:600;color:#16A34A;">Gratis</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:13px;">
                                    <span style="color:var(--macuin-muted);">IVA (16%)</span>
                                    <span style="font-weight:600;">${{ number_format($iva, 2) }}</span>
                                </div>
                            </div>

                            <div style="
                                background:var(--macuin-white);border-radius:6px;
                                padding:14px;margin-bottom:20px;
                                display:flex;justify-content:space-between;align-items:center;
                            ">
                                <span style="font-family:'Oswald',sans-serif;font-size:15px;font-weight:700;text-transform:uppercase;">TOTAL</span>
                                <span style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:var(--macuin-red);">${{ number_format($total, 2) }}</span>
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

@endsection
