<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Crea tu cuenta en MACUIN — Regístrate en pocos pasos.">
    <title>Crear cuenta — MACUIN</title>

    @vite('resources/css/register.css')
</head>

<body>

    <main class="card" role="main">

        {{-- Logo --}}
        <div class="logo-wrap">
            <div class="logo-box">
                <img src="{{ asset('images/creacion_usuario.png') }}" alt="Logo MACUIN" width="220" height="110">
            </div>
        </div>

        {{-- Heading --}}
        <div class="heading">
            <h1>Crear cuenta</h1>
            <p>Completa los datos para registrarte</p>
        </div>

        {{-- Form (UI only — sin lógica de backend) --}}
        <form novalidate>

            {{-- Nombre y Apellido en una fila --}}
            <div class="form-row">

                {{-- Nombre(s) --}}
                <div class="form-group">
                    <label for="nombres">Nombre(s)</label>
                    <div class="input-wrap">
                        <svg class="input-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input id="nombres" class="form-control icon-text" type="text" name="nombres" placeholder="Juan"
                            required autocomplete="given-name">
                    </div>
                </div>

                {{-- Apellido(s) --}}
                <div class="form-group">
                    <label for="apellidos">Apellido(s)</label>
                    <div class="input-wrap">
                        <svg class="input-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input id="apellidos" class="form-control icon-text" type="text" name="apellidos"
                            placeholder="Pérez" required autocomplete="family-name">
                    </div>
                </div>

            </div>{{-- /form-row --}}

            {{-- Correo electrónico --}}
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <img class="input-icon-img" src="{{ asset('images/correo.jpg') }}" alt="" aria-hidden="true">
                    <input id="email" class="form-control" type="email" name="email" placeholder="tucorreo@ejemplo.com"
                        required autocomplete="email" spellcheck="false">
                </div>
            </div>

            {{-- Contraseña --}}
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <img class="input-icon-img" src="{{ asset('images/candado.jpg') }}" alt="" aria-hidden="true">
                    <input id="password" class="form-control" type="password" name="password" placeholder="••••••••"
                        required autocomplete="new-password">
                </div>
            </div>

            {{-- Confirmar contraseña --}}
            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <div class="input-wrap">
                    <img class="input-icon-img" src="{{ asset('images/candado.jpg') }}" alt="" aria-hidden="true">
                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
                        placeholder="••••••••" required autocomplete="new-password">
                </div>
            </div>

            {{-- Submit --}}
            <button class="btn-primary" type="submit">
                Crear cuenta
            </button>

        </form>

        {{-- Back to login --}}
        <p class="login-row">
            ¿Ya tienes cuenta? <a href="{{ url('/login') }}">Inicia sesión</a>
        </p>

    </main>

</body>

</html>