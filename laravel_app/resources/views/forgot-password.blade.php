<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recupera el acceso a tu cuenta MACUIN ingresando tu correo electrónico.">
    <title>¿Olvidaste tu contraseña? — MACUIN</title>

    @vite('resources/css/forgot-password.css')
</head>

<body>

    <main class="card" role="main">

        {{-- Logo --}}
        <div class="logo-wrap">
            <div class="logo-box">
                <img src="{{ asset('images/candado_rojo.png') }}" alt="Recuperar contraseña MACUIN" width="220"
                    height="110">
            </div>
        </div>

        {{-- Heading --}}
        <div class="heading">
            <h1>¿Olvidaste tu contraseña?</h1>
            <p>Ingresa tu correo y te enviaremos un enlace para restablecerla</p>
        </div>

        {{-- Form (UI only — sin lógica de backend) --}}
        <form novalidate>

            {{-- Correo electrónico --}}
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrap">
                    <img class="input-icon" src="{{ asset('images/correo.jpg') }}" alt="" aria-hidden="true">
                    <input id="email" class="form-control" type="email" name="email" placeholder="tucorreo@ejemplo.com"
                        required autocomplete="email" spellcheck="false">
                </div>
            </div>

            {{-- Submit --}}
            <button class="btn-primary" type="submit">
                Enviar enlace de recuperación
            </button>

        </form>

        {{-- Back to login --}}
        <p class="login-row">
            ¿Recordaste tu contraseña? <a href="{{ url('/login') }}">Inicia sesión</a>
        </p>

    </main>

</body>

</html>