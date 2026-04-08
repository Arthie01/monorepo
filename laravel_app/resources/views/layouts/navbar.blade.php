{{-- Sección: Top Banner --}}
<div style="background:var(--macuin-red);color:#fff;text-align:center;padding:9px 0;font-family:'DM Sans',sans-serif;font-size:13px;">
    <div class="mac-container" style="max-width:1280px;margin:0 auto;padding:0 24px;">
        <span><i class="fas fa-truck" style="margin-right:6px;"></i> Envío gratis en pedidos mayores a <strong>$1,500 MXN</strong></span>
        <span style="margin:0 20px;opacity:.4;">|</span>
        <span><i class="fas fa-phone" style="margin-right:6px;"></i> <a href="tel:+524491234567" style="color:#fff;font-weight:600;">449-123-4567</a></span>
    </div>
</div>

{{-- Sección: Navbar Principal --}}
<header id="mac-navbar" style="
    background: var(--macuin-dark);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 12px rgba(0,0,0,.4);
">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;display:flex;align-items:center;gap:24px;height:68px;">

        {{-- Logo --}}
        <a href="/dashboard" style="flex-shrink:0;text-decoration:none;display:flex;align-items:center;gap:10px;">
            <div style="
                background:var(--macuin-red);
                color:#fff;
                font-family:'Oswald',sans-serif;
                font-size:22px;
                font-weight:700;
                letter-spacing:.08em;
                text-transform:uppercase;
                padding:6px 14px;
                line-height:1;
            ">MACUIN</div>
            <div style="color:#8B949E;font-family:'DM Sans',sans-serif;font-size:11px;line-height:1.3;text-transform:uppercase;letter-spacing:.06em;">
                Autopartes<br>& Distribución
            </div>
        </a>

        {{-- Selector de Vehículo (compacto en navbar) --}}
        <div id="mac-vehicle-quick" style="
            flex:1;
            max-width:480px;
            display:flex;
            align-items:center;
            gap:6px;
            background:rgba(255,255,255,.07);
            border:1px solid rgba(255,255,255,.12);
            border-radius:6px;
            padding:0 10px;
            height:42px;
        ">
            <i class="fas fa-car" style="color:#8B949E;font-size:14px;flex-shrink:0;"></i>
            <select id="nav-marca" style="
                flex:1;background:transparent;border:none;outline:none;
                color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;
                cursor:pointer;
            ">
                <option value="" style="background:#0D0D0D;">Marca</option>
                <option value="chevrolet"  style="background:#0D0D0D;">Chevrolet</option>
                <option value="ford"       style="background:#0D0D0D;">Ford</option>
                <option value="nissan"     style="background:#0D0D0D;">Nissan</option>
                <option value="volkswagen" style="background:#0D0D0D;">Volkswagen</option>
                <option value="toyota"     style="background:#0D0D0D;">Toyota</option>
                <option value="honda"      style="background:#0D0D0D;">Honda</option>
                <option value="dodge"      style="background:#0D0D0D;">Dodge</option>
                <option value="kia"        style="background:#0D0D0D;">Kia</option>
                <option value="hyundai"    style="background:#0D0D0D;">Hyundai</option>
            </select>
            <span style="color:#333;font-size:10px;">|</span>
            <select id="nav-modelo" style="
                flex:1;background:transparent;border:none;outline:none;
                color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;
                cursor:pointer;
            ">
                <option value="" style="background:#0D0D0D;">Modelo</option>
            </select>
            <button onclick="buscarNav()" style="
                background:var(--macuin-red);border:none;color:#fff;
                width:32px;height:32px;border-radius:4px;cursor:pointer;
                display:flex;align-items:center;justify-content:center;flex-shrink:0;
                transition:background .2s;
            " title="Buscar autopartes">
                <i class="fas fa-search" style="font-size:12px;"></i>
            </button>
        </div>

        {{-- Navegación central --}}
        <nav style="display:flex;align-items:center;gap:4px;margin-left:auto;">
            <a href="/dashboard" style="
                color:{{ request()->is('dashboard') ? '#fff' : '#8B949E' }};
                font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;
                text-decoration:none;padding:8px 12px;border-radius:4px;
                transition:all .2s;white-space:nowrap;
                {{ request()->is('dashboard') ? 'background:rgba(255,255,255,.08);' : '' }}
            " onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.08)'"
               onmouseout="this.style.color='{{ request()->is('dashboard') ? '#fff' : '#8B949E' }}';this.style.background='{{ request()->is('dashboard') ? 'rgba(255,255,255,.08)' : 'transparent' }}'">
                Inicio
            </a>
            <a href="/catalogo" style="
                color:{{ request()->is('catalogo*') ? '#fff' : '#8B949E' }};
                font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;
                text-decoration:none;padding:8px 12px;border-radius:4px;
                transition:all .2s;white-space:nowrap;
                {{ request()->is('catalogo*') ? 'background:rgba(255,255,255,.08);' : '' }}
            " onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.08)'"
               onmouseout="this.style.color='{{ request()->is('catalogo*') ? '#fff' : '#8B949E' }}';this.style.background='{{ request()->is('catalogo*') ? 'rgba(255,255,255,.08)' : 'transparent' }}'">
                Catálogo
            </a>
            <a href="/pedidos" style="
                color:{{ request()->is('pedidos*') ? '#fff' : '#8B949E' }};
                font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;
                text-decoration:none;padding:8px 12px;border-radius:4px;
                transition:all .2s;white-space:nowrap;
            " onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.08)'"
               onmouseout="this.style.color='{{ request()->is('pedidos*') ? '#fff' : '#8B949E' }}';this.style.background='transparent'">
                Pedidos
            </a>
        </nav>

        {{-- Acciones: perfil + carrito --}}
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            {{-- Perfil --}}
            <a href="/perfil" style="
                display:flex;align-items:center;gap:8px;
                color:#8B949E;text-decoration:none;
                padding:8px 12px;border-radius:4px;
                font-family:'DM Sans',sans-serif;font-size:14px;
                transition:all .2s;white-space:nowrap;
            " onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.08)'"
               onmouseout="this.style.color='#8B949E';this.style.background='transparent'">
                <i class="fas fa-user" style="font-size:15px;"></i>
                <span style="display:none;" class="nav-user-name">Mi cuenta</span>
            </a>

            {{-- Carrito --}}
            <a href="/carrito" style="
                position:relative;
                display:flex;align-items:center;gap:8px;
                background:var(--macuin-red);color:#fff;
                text-decoration:none;padding:9px 16px;border-radius:4px;
                font-family:'Oswald',sans-serif;font-size:13px;font-weight:600;
                letter-spacing:.06em;text-transform:uppercase;
                transition:background .2s;
            " onmouseover="this.style.background='#8B0D21'" onmouseout="this.style.background='var(--macuin-red)'">
                <i class="fas fa-shopping-cart" style="font-size:14px;"></i>
                <span>Carrito</span>
                <span id="mac-cart-badge" style="
                    position:absolute;top:-6px;right:-6px;
                    background:#fff;color:var(--macuin-red);
                    font-size:10px;font-weight:700;font-family:'DM Sans',sans-serif;
                    border-radius:50%;width:18px;height:18px;
                    display:flex;align-items:center;justify-content:center;
                    display:none;
                ">0</span>
            </a>
        </div>

    </div>
</header>

<script>
    const macModelos = {
        chevrolet:  ['Aveo','Beat','Trax','Equinox','Silverado','Express','Cheyenne','Captiva'],
        ford:       ['Fiesta','Focus','Mustang','Ranger','F-150','Escape','Explorer','Lobo'],
        nissan:     ['Tsuru','Versa','Sentra','Altima','X-Trail','Kicks','NP300','Frontier'],
        volkswagen: ['Jetta','Golf','Vento','Tiguan','Passat','Polo','Amarok','Saveiro'],
        toyota:     ['Corolla','Camry','Hilux','RAV4','Fortuner','Yaris','Avanza','Prius'],
        honda:      ['Civic','Accord','CR-V','HR-V','Fit','City','Pilot','Ridgeline'],
        dodge:      ['Attitude','Journey','Durango','Charger','RAM 700','RAM 1500','Challenger'],
        kia:        ['Rio','Forte','Seltos','Sportage','Sorento','Stinger','Carnival'],
        hyundai:    ['Accent','Elantra','Tucson','Santa Fe','Creta','Ioniq','Venue'],
    };
    function updateNavMarca() {
        const marca = document.getElementById('nav-marca').value;
        const modeloSel = document.getElementById('nav-modelo');
        modeloSel.innerHTML = '<option value="" style="background:#0D0D0D;">Modelo</option>';
        if (macModelos[marca]) {
            macModelos[marca].forEach(m => {
                const o = document.createElement('option');
                o.value = m;
                o.textContent = m;
                o.style.background = '#0D0D0D';
                modeloSel.appendChild(o);
            });
        }
    }
    document.getElementById('nav-marca').addEventListener('change', updateNavMarca);

    function buscarNav() {
        const marca  = document.getElementById('nav-marca').value;
        const modelo = document.getElementById('nav-modelo').value;
        const params = new URLSearchParams();
        if (marca)  params.set('marca_vehiculo',  marca);
        if (modelo) params.set('modelo_vehiculo', modelo);
        const qs = params.toString();
        window.location.href = '/catalogo' + (qs ? '?' + qs : '');
    }
</script>
