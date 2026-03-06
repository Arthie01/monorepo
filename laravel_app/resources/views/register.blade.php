@extends('layouts.app')

@section('title', 'Registro de Usuario')

@push('styles')
<style>
    #mac-navbar, header[id="mac-navbar"], .site-footer, footer { display: none !important; }
    main { padding: 0; }
    body { background: var(--macuin-dark); }

    .auth-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 100vh;
    }
    .auth-left {
        background: var(--macuin-dark);
        border-right: 4px solid var(--macuin-red);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 60px 56px;
        overflow: hidden;
    }
    .auth-right {
        background: var(--macuin-white);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px 56px;
        overflow-y: auto;
    }
    .auth-form-wrap { width: 100%; max-width: 420px; }
    .mac-checkbox { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; }
    .mac-checkbox input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--macuin-red); margin-top: 2px; flex-shrink: 0; }
    .mac-checkbox span { font-size: 13px; color: var(--macuin-muted); line-height: 1.5; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    @media (max-width: 900px) {
        .auth-split { grid-template-columns: 1fr; }
        .auth-left { display: none; }
        .auth-right { padding: 40px 24px; }
    }
    @media (max-width: 480px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="auth-split">

    {{-- Sección: Panel Izquierdo — Beneficios --}}
    <div class="auth-left">
        {{-- Elementos decorativos --}}
        <div style="position:absolute;width:400px;height:400px;border:1px solid rgba(196,18,48,.2);border-radius:50%;top:-100px;right:-100px;"></div>
        <div style="position:absolute;width:200px;height:200px;border:1px solid rgba(196,18,48,.15);border-radius:50%;bottom:80px;left:-40px;"></div>

        <div style="position:relative;z-index:1;">
            <div style="
                display:inline-block;background:var(--macuin-red);
                padding:8px 16px;margin-bottom:36px;
            ">
                <span style="font-family:'Oswald',sans-serif;font-size:26px;font-weight:700;color:#fff;letter-spacing:.1em;">MACUIN</span>
            </div>

            <h2 style="
                font-family:'Oswald',sans-serif;font-size:clamp(22px,2.5vw,36px);
                font-weight:700;color:#fff;text-transform:uppercase;
                line-height:1.15;margin-bottom:24px;
            ">
                Crea tu cuenta<br>y accede a<br>
                <span style="color:var(--macuin-red);">beneficios exclusivos</span>
            </h2>

            {{-- Lista de beneficios --}}
            <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:40px;">
                @foreach([
                    ['fa-tags',      'Precios preferenciales para talleres y refaccionarias'],
                    ['fa-truck',     'Envío rápido a todo México con rastreo en tiempo real'],
                    ['fa-headset',   'Soporte técnico especializado para identificación de piezas'],
                    ['fa-history',   'Historial de pedidos y reordenamiento con un clic'],
                    ['fa-shield-alt','Garantía de calidad en todas nuestras autopartes'],
                ] as [$icon, $text])
                <div style="display:flex;align-items:flex-start;gap:14px;">
                    <div style="
                        width:36px;height:36px;flex-shrink:0;
                        background:rgba(196,18,48,.2);border:1px solid rgba(196,18,48,.3);
                        border-radius:4px;display:flex;align-items:center;justify-content:center;
                    ">
                        <i class="fas {{ $icon }}" style="color:var(--macuin-red);font-size:14px;"></i>
                    </div>
                    <p style="font-size:13px;color:#8B949E;line-height:1.6;margin-top:8px;">{{ $text }}</p>
                </div>
                @endforeach
            </div>

            <p style="font-size:12px;color:#6B7280;border-top:1px solid rgba(255,255,255,.08);padding-top:20px;">
                ¿Ya tienes cuenta?
                <a href="/login" style="color:var(--macuin-red);font-weight:600;text-decoration:none;">Inicia sesión aquí →</a>
            </p>
        </div>
    </div>

    {{-- Sección: Panel Derecho — Formulario --}}
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div style="margin-bottom:28px;">
                <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px;">Portal de Clientes</p>
                <h2 style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;text-transform:uppercase;color:var(--macuin-text);margin-bottom:8px;">Crear Cuenta</h2>
                <div style="width:44px;height:3px;background:var(--macuin-red);border-radius:2px;"></div>
            </div>

            <form method="POST" action="/registro">
                @csrf

                {{-- Nombre y Apellidos --}}
                <div class="form-row">
                    <div class="mac-form-group">
                        <label class="mac-label" for="name">Nombre(s)</label>
                        <div class="mac-input-icon">
                            <i class="mac-input-icon__icon fas fa-user"></i>
                            <input
                                type="text" id="name" name="name"
                                class="mac-input {{ $errors->has('name') ? 'mac-input-error' : '' }}"
                                placeholder="Juan"
                                value="{{ old('name') }}"
                                autocomplete="given-name" required
                            >
                        </div>
                        @error('name')
                            <span class="mac-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mac-form-group">
                        <label class="mac-label" for="apellidos">Apellidos</label>
                        <div class="mac-input-icon">
                            <i class="mac-input-icon__icon fas fa-user"></i>
                            <input
                                type="text" id="apellidos" name="apellidos"
                                class="mac-input {{ $errors->has('apellidos') ? 'mac-input-error' : '' }}"
                                placeholder="García López"
                                value="{{ old('apellidos') }}"
                                autocomplete="family-name"
                            >
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="mac-form-group">
                    <label class="mac-label" for="email">Correo Electrónico</label>
                    <div class="mac-input-icon">
                        <i class="mac-input-icon__icon fas fa-envelope"></i>
                        <input
                            type="email" id="email" name="email"
                            class="mac-input {{ $errors->has('email') ? 'mac-input-error' : '' }}"
                            placeholder="correo@ejemplo.com"
                            value="{{ old('email') }}"
                            autocomplete="email" required
                        >
                    </div>
                    @error('email')
                        <span class="mac-error-text"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>{{ $message }}</span>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div class="mac-form-group">
                    <label class="mac-label" for="phone">Teléfono</label>
                    <div class="mac-input-icon">
                        <i class="mac-input-icon__icon fas fa-phone"></i>
                        <input
                            type="tel" id="phone" name="phone"
                            class="mac-input"
                            placeholder="449-123-4567"
                            value="{{ old('phone') }}"
                            autocomplete="tel"
                        >
                    </div>
                </div>

                {{-- Contraseñas --}}
                <div class="form-row">
                    <div class="mac-form-group">
                        <label class="mac-label" for="password">Contraseña</label>
                        <div class="mac-input-icon" style="position:relative;">
                            <i class="mac-input-icon__icon fas fa-lock"></i>
                            <input
                                type="password" id="password" name="password"
                                class="mac-input {{ $errors->has('password') ? 'mac-input-error' : '' }}"
                                placeholder="••••••••"
                                autocomplete="new-password" required
                            >
                            <button type="button" onclick="togglePass('password',this)" style="
                                position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                background:none;border:none;cursor:pointer;color:var(--macuin-muted);font-size:13px;padding:0;
                            "><i class="fas fa-eye"></i></button>
                        </div>
                        @error('password')
                            <span class="mac-error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mac-form-group">
                        <label class="mac-label" for="password_confirmation">Confirmar</label>
                        <div class="mac-input-icon" style="position:relative;">
                            <i class="mac-input-icon__icon fas fa-lock"></i>
                            <input
                                type="password" id="password_confirmation" name="password_confirmation"
                                class="mac-input"
                                placeholder="••••••••"
                                autocomplete="new-password" required
                            >
                            <button type="button" onclick="togglePass('password_confirmation',this)" style="
                                position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                background:none;border:none;cursor:pointer;color:var(--macuin-muted);font-size:13px;padding:0;
                            "><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>

                {{-- Términos y condiciones --}}
                <div style="margin-bottom:24px;">
                    <label class="mac-checkbox">
                        <input type="checkbox" name="terms" required>
                        <span>
                            Acepto los
                            <a href="#" style="color:var(--macuin-red);font-weight:600;text-decoration:none;">Términos y Condiciones</a>
                            y el
                            <a href="#" style="color:var(--macuin-red);font-weight:600;text-decoration:none;">Aviso de Privacidad</a>
                            de MACUIN.
                        </span>
                    </label>
                </div>

                <button type="submit" class="mac-btn mac-btn-primary mac-btn-block mac-btn-lg" style="margin-bottom:16px;">
                    <i class="fas fa-user-plus"></i>
                    CREAR CUENTA
                </button>

                <p style="text-align:center;font-size:13px;color:var(--macuin-muted);">
                    ¿Ya tienes cuenta?
                    <a href="/login" style="color:var(--macuin-red);font-weight:600;text-decoration:none;">Inicia sesión</a>
                </p>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
</script>
@endpush
