<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MACUIN') — Autopartes y Distribución</title>

    {{-- Preconnect --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    {{-- Fuentes MACUIN: Oswald + DM Sans + JetBrains Mono --}}
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>

    {{-- Swiper.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    {{-- CSS base (copiado de Seals Edition) --}}
    <link rel="stylesheet" href="{{ asset('css/preloader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/weiboo-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    {{-- CSS MACUIN --}}
    <link rel="stylesheet" href="{{ asset('css/macuin.css') }}">

    @stack('styles')
</head>
<body style="font-family: 'DM Sans', sans-serif;">

    {{-- Sección: Preloader --}}
    <div class="preloader-wrapper" id="preloader">
        <div class="preloader-new">
            <svg class="cart_preloader" role="img" aria-label="Cargando MACUIN" viewBox="0 0 128 128" width="128px" height="128px" xmlns="http://www.w3.org/2000/svg">
                <g fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="8">
                    <g class="cart__track" stroke="hsla(0,10%,10%,0.1)">
                        <polyline points="4,4 21,4 26,22 124,22 112,64 35,64 39,80 106,80"></polyline>
                        <circle cx="43" cy="111" r="13"></circle>
                        <circle cx="102" cy="111" r="13"></circle>
                    </g>
                    <g class="cart__lines" stroke="#C41230">
                        <polyline class="cart__top" points="4,4 21,4 26,22 124,22 112,64 35,64 39,80 106,80" stroke-dasharray="338 338" stroke-dashoffset="-338"></polyline>
                        <g class="cart__wheel1" transform="rotate(-90,43,111)">
                            <circle class="cart__wheel-stroke" cx="43" cy="111" r="13" stroke-dasharray="81.68 81.68" stroke-dashoffset="81.68"></circle>
                        </g>
                        <g class="cart__wheel2" transform="rotate(90,102,111)">
                            <circle class="cart__wheel-stroke" cx="102" cy="111" r="13" stroke-dasharray="81.68 81.68" stroke-dashoffset="81.68"></circle>
                        </g>
                    </g>
                </g>
            </svg>
        </div>
    </div>

    {{-- Sección: Toast Notifications --}}
    <div id="mac-toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;"></div>

    {{-- Sección: Navbar --}}
    @include('layouts.navbar')

    {{-- Sección: Contenido Principal --}}
    <main>
        @yield('content')
    </main>

    {{-- Sección: Footer --}}
    @include('layouts.footer')

    {{-- Swiper.js --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- Toast Script MACUIN --}}
    <script>
        function showToast(message, type = 'success', title = '') {
            const container = document.getElementById('mac-toast-container');
            const icons = {
                success: '<i class="fas fa-check-circle"></i>',
                error:   '<i class="fas fa-exclamation-circle"></i>',
                warning: '<i class="fas fa-exclamation-triangle"></i>',
                info:    '<i class="fas fa-info-circle"></i>'
            };
            const defaultTitles = {
                success: '¡Éxito!',
                error:   'Error',
                warning: 'Advertencia',
                info:    'Información'
            };
            const iconColors = { success:'#16A34A', error:'#DC2626', warning:'#D97706', info:'#2196F3' };
            const borderColors = { success:'#16A34A', error:'#DC2626', warning:'#D97706', info:'#2196F3' };

            const toast = document.createElement('div');
            toast.style.cssText = `
                min-width:300px;max-width:380px;padding:14px 18px;background:#fff;
                border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.2);
                display:flex;align-items:flex-start;gap:10px;
                border-left:4px solid ${borderColors[type]||'#C41230'};
                animation:mac-slideIn .3s ease-out;font-family:'DM Sans',sans-serif;
            `;
            toast.innerHTML = `
                <span style="color:${iconColors[type]};font-size:18px;flex-shrink:0;margin-top:2px;">${icons[type]||icons.success}</span>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13px;color:#1A1A1A;margin-bottom:2px;">${title||defaultTitles[type]}</div>
                    <div style="font-size:13px;color:#6B7280;line-height:1.4;">${message}</div>
                </div>
                <button onclick="removeToast(this.parentElement)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:18px;line-height:1;padding:0;">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => removeToast(toast), 4500);
        }

        function removeToast(toast) {
            toast.style.animation = 'mac-slideOut .3s ease-in forwards';
            setTimeout(() => { if (toast.parentElement) toast.parentElement.removeChild(toast); }, 300);
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
            @if(session('warning'))
                showToast("{{ session('warning') }}", 'warning');
            @endif
            @if(session('info'))
                showToast("{{ session('info') }}", 'info');
            @endif
        });
    </script>

    {{-- Preloader Script --}}
    <script>
        window.addEventListener('load', function () {
            const preloader = document.getElementById('preloader');
            preloader.classList.add('hidden');
            setTimeout(() => { preloader.style.display = 'none'; }, 600);
        });
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                const preloader = document.getElementById('preloader');
                preloader.classList.add('hidden');
                setTimeout(() => { preloader.style.display = 'none'; }, 600);
            }
        });
    </script>

    @stack('scripts')

    <style>
        @keyframes mac-slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to   { transform: translateX(0);     opacity: 1; }
        }
        @keyframes mac-slideOut {
            from { transform: translateX(0);     opacity: 1; }
            to   { transform: translateX(400px); opacity: 0; }
        }
    </style>
</body>
</html>
