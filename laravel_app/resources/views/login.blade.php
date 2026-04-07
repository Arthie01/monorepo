@extends('layouts.app')

@section('title', 'Iniciar Sesión')

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
        background: var(--macuin-red);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 60px 56px;
        overflow: hidden;
    }
    .auth-left::after {
        content: '';
        position: absolute;
        top: 0; right: -1px; bottom: 0;
        width: 80px;
        background: var(--macuin-white);
        clip-path: polygon(80px 0, 100% 0, 100% 100%, 0 100%);
    }
    .auth-right {
        background: var(--macuin-white);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 60px 56px;
    }
    .auth-form-wrap { width: 100%; max-width: 400px; }
    .mac-checkbox { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .mac-checkbox input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--macuin-red); }
    .mac-checkbox span { font-size: 13px; color: var(--macuin-muted); }

    @media (max-width: 768px) {
        .auth-split { grid-template-columns: 1fr; }
        .auth-left { display: none; }
        .auth-right { padding: 40px 24px; }
    }
</style>
@endpush

@section('content')
<div class="auth-split">

    {{-- Sección: Panel Izquierdo — Marca --}}
    <div class="auth-left">
        {{-- Círculos decorativos --}}
        <div style="position:absolute;width:320px;height:320px;border:1px solid rgba(255,255,255,.1);border-radius:50%;top:-100px;left:-80px;"></div>
        <div style="position:absolute;width:200px;height:200px;border:1px solid rgba(255,255,255,.15);border-radius:50%;bottom:60px;right:80px;"></div>
        <div style="position:absolute;width:80px;height:80px;border:1px solid rgba(255,255,255,.2);border-radius:50%;bottom:220px;left:50px;"></div>

        <div style="position:relative;z-index:1;">
            {{-- Logo --}}
            <div style="
                display:inline-block;background:rgba(255,255,255,.15);
                border:2px solid rgba(255,255,255,.3);
                padding:10px 20px;margin-bottom:36px;
            ">
                <span style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:#fff;letter-spacing:.1em;">MACUIN</span>
            </div>

            <h1 style="
                font-family:'Oswald',sans-serif;
                font-size:clamp(26px,3vw,42px);
                font-weight:700;color:#fff;
                text-transform:uppercase;line-height:1.15;
                letter-spacing:.02em;margin-bottom:16px;
            ">
                Tu distribuidor<br>de autopartes<br>
                <span style="color:rgba(255,255,255,.65);">de confianza</span>
            </h1>

            <p style="font-size:14px;color:rgba(255,255,255,.8);line-height:1.75;max-width:340px;margin-bottom:40px;">
                Más de 15,000 referencias para todas las marcas del mercado mexicano. Calidad garantizada para talleres y refaccionarias.
            </p>

            <div style="display:flex;gap:32px;">
                @foreach([['15K+','Referencias'],['500+','Marcas'],['24h','Entrega']] as [$n,$l])
                <div>
                    <div style="font-family:'Oswald',sans-serif;font-size:26px;font-weight:700;color:#fff;line-height:1;">{{ $n }}</div>
                    <div style="font-size:11px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.08em;margin-top:4px;">{{ $l }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sección: Panel Derecho — Formulario --}}
    <div class="auth-right">
        <div class="auth-form-wrap">
            <div style="margin-bottom:32px;">
                <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px;">Bienvenido de vuelta</p>
                <h2 style="font-family:'Oswald',sans-serif;font-size:30px;font-weight:700;text-transform:uppercase;color:var(--macuin-text);margin-bottom:8px;">Iniciar Sesión</h2>
                <div style="width:44px;height:3px;background:var(--macuin-red);border-radius:2px;"></div>
            </div>

            <form method="POST" action="/login">
                @csrf

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

                {{-- Contraseña --}}
                <div class="mac-form-group">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <label class="mac-label" for="password" style="margin-bottom:0;">Contraseña</label>
                        <a href="/forgot-password" style="font-size:12px;color:var(--macuin-red);text-decoration:none;font-weight:500;">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="mac-input-icon" style="position:relative;">
                        <i class="mac-input-icon__icon fas fa-lock"></i>
                        <input
                            type="password" id="password" name="password"
                            class="mac-input {{ $errors->has('password') ? 'mac-input-error' : '' }}"
                            placeholder="••••••••"
                            autocomplete="current-password" required
                        >
                        <button type="button" onclick="togglePass('password',this)" style="
                            position:absolute;right:12px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:var(--macuin-muted);font-size:14px;padding:0;
                        "><i class="fas fa-eye"></i></button>
                    </div>
                    @error('password')
                        <span class="mac-error-text"><i class="fas fa-exclamation-circle" style="margin-right:4px;"></i>{{ $message }}</span>
                    @enderror
                </div>

                {{-- Recuérdame --}}
                <div style="margin-bottom:24px;">
                    <label class="mac-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Mantener sesión iniciada</span>
                    </label>
                </div>

                @if($errors->has('api'))
                    <div class="auth-error" style="color:#C41230;font-size:13px;margin-bottom:12px;">
                        {{ $errors->first('api') }}
                    </div>
                @endif
                @if(session('success'))
                    <div style="color:#16a34a;font-size:13px;margin-bottom:12px;">{{ session('success') }}</div>
                @endif

                <button type="submit" class="mac-btn mac-btn-primary mac-btn-block mac-btn-lg" style="margin-bottom:20px;">
                    <i class="fas fa-sign-in-alt"></i>
                    INGRESAR
                </button>

                <p style="text-align:center;font-size:14px;color:var(--macuin-muted);">
                    ¿No tienes cuenta?
                    <a href="/registro" style="color:var(--macuin-red);font-weight:600;text-decoration:none;">Regístrate aquí</a>
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
