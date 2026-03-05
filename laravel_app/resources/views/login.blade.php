<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Iniciar sesión en MACUIN — Accede a tu cuenta de forma segura.">
    <title>Iniciar sesión — MACUIN</title>

    @vite('resources/css/login.css')
</head>

<body>

    <main class="card" role="main">

        {{-- Logo --}}
        <div class="logo-wrap">
            <div class="logo-box">
                <img src="{{ asset('images/logo2.jpeg') }}" alt="Logo MACUIN" width="220" height="110">
            </div>
        </div>

        {{-- Heading --}}
        <div class="heading">
            <h1>Bienvenido de nuevo</h1>
            <p>Ingresa a tu cuenta para continuar</p>
        </div>

        {{-- Form (UI only — no backend logic) --}}
        <form novalidate>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <img class="input-icon" src="{{ asset('images/correo.jpg') }}" alt="" aria-hidden="true">
                    <input id="email" class="form-control" type="email" name="email" placeholder="tucorreo@ejemplo.com"
                        required autocomplete="email" spellcheck="false">
                </div>
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <img class="input-icon" src="{{ asset('images/candado.jpg') }}" alt="" aria-hidden="true">
                    <input id="password" class="form-control" type="password" name="password" placeholder="••••••••"
                        required autocomplete="current-password">
                </div>
            </div>

            {{-- Checkbox + Forgot --}}
            <div class="extras-row">
                <label class="checkbox-label" for="remember">
                    <input type="checkbox" id="remember" name="remember">
                    Recuérdame
                </label>
                <a class="forgot-link" href="{{ url('/forgot-password') }}">¿Olvidaste tu contraseña?</a>
            </div>

            {{-- Submit --}}
            <button class="btn-primary" type="submit">
                Iniciar sesión
            </button>

        </form>

        {{-- Register --}}
        <p class="register-row">
            ¿No tienes cuenta? <a href="{{ url('/register') }}">Regístrate aquí</a>
        </p>

    </main>

</body>

</html>