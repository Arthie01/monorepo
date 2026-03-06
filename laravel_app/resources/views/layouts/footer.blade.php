{{-- Sección: Footer MACUIN --}}
<footer style="background:var(--macuin-dark);color:#fff;font-family:'DM Sans',sans-serif;margin-top:auto;">

    {{-- Banner diagonal rojo --}}
    <div style="
        background:var(--macuin-red);
        clip-path:polygon(0 0,100% 0,100% 70%,0 100%);
        padding:48px 0 72px;
        text-align:center;
    ">
        <div style="max-width:600px;margin:0 auto;padding:0 24px;">
            <p style="font-family:'Oswald',sans-serif;font-size:clamp(20px,3vw,28px);font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;">
                ¿Necesitas una autoparte específica?
            </p>
            <p style="font-size:14px;opacity:.9;margin-bottom:24px;">
                Contamos con más de 15,000 referencias para todas las marcas del mercado mexicano
            </p>
            <a href="/catalogo" style="
                display:inline-block;background:#fff;color:var(--macuin-red);
                font-family:'Oswald',sans-serif;font-size:14px;font-weight:700;
                text-transform:uppercase;letter-spacing:.1em;
                padding:12px 28px;border-radius:4px;text-decoration:none;
                transition:transform .2s,box-shadow .2s;
            " onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.3)'"
               onmouseout="this.style.transform='';this.style.boxShadow=''">
                VER CATÁLOGO →
            </a>
        </div>
    </div>

    {{-- Contenido principal del footer --}}
    <div style="max-width:1280px;margin:0 auto;padding:56px 24px 40px;">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1.5fr;gap:48px;flex-wrap:wrap;">

            {{-- Columna: Sobre MACUIN --}}
            <div>
                <div style="
                    display:inline-block;
                    background:var(--macuin-red);
                    font-family:'Oswald',sans-serif;
                    font-size:24px;font-weight:700;
                    letter-spacing:.08em;text-transform:uppercase;
                    padding:6px 14px;margin-bottom:16px;
                ">MACUIN</div>
                <p style="font-size:14px;color:#8B949E;line-height:1.7;margin-bottom:20px;">
                    Distribuidor líder de autopartes en México. Servimos a talleres mecánicos, refaccionarias y compradores finales con calidad garantizada.
                </p>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:8px;">
                    <li style="font-size:13px;color:#8B949E;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-map-marker-alt" style="color:var(--macuin-red);width:14px;"></i>
                        Querétaro, México
                    </li>
                    <li style="font-size:13px;color:#8B949E;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-phone" style="color:var(--macuin-red);width:14px;"></i>
                        <a href="tel:+524491234567" style="color:#8B949E;text-decoration:none;">449-123-4567</a>
                    </li>
                    <li style="font-size:13px;color:#8B949E;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-envelope" style="color:var(--macuin-red);width:14px;"></i>
                        <a href="mailto:ventas@macuin.mx" style="color:#8B949E;text-decoration:none;">ventas@macuin.mx</a>
                    </li>
                    <li style="font-size:13px;color:#8B949E;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-clock" style="color:var(--macuin-red);width:14px;"></i>
                        Lun–Vie: 8:00–18:00
                    </li>
                </ul>
                {{-- Redes sociales --}}
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <a href="#" aria-label="Facebook" style="
                        width:36px;height:36px;border-radius:50%;
                        background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);
                        display:flex;align-items:center;justify-content:center;
                        color:#8B949E;text-decoration:none;font-size:14px;
                        transition:all .2s;
                    " onmouseover="this.style.background='var(--macuin-red)';this.style.color='#fff';this.style.borderColor='var(--macuin-red)'"
                       onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.color='#8B949E';this.style.borderColor='rgba(255,255,255,.1)'">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" aria-label="Instagram" style="
                        width:36px;height:36px;border-radius:50%;
                        background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);
                        display:flex;align-items:center;justify-content:center;
                        color:#8B949E;text-decoration:none;font-size:14px;
                        transition:all .2s;
                    " onmouseover="this.style.background='var(--macuin-red)';this.style.color='#fff';this.style.borderColor='var(--macuin-red)'"
                       onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.color='#8B949E';this.style.borderColor='rgba(255,255,255,.1)'">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" aria-label="WhatsApp" style="
                        width:36px;height:36px;border-radius:50%;
                        background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);
                        display:flex;align-items:center;justify-content:center;
                        color:#8B949E;text-decoration:none;font-size:14px;
                        transition:all .2s;
                    " onmouseover="this.style.background='var(--macuin-red)';this.style.color='#fff';this.style.borderColor='var(--macuin-red)'"
                       onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.color='#8B949E';this.style.borderColor='rgba(255,255,255,.1)'">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            {{-- Columna: Categorías --}}
            <div>
                <h4 style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#fff;margin-bottom:20px;">Categorías</h4>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
                    @php
                        $cats = ['Motor','Suspensión','Frenos','Sistema Eléctrico','Transmisión','Filtros','Carrocería','Climatización'];
                    @endphp
                    @foreach($cats as $cat)
                    <li>
                        <a href="/catalogo?categoria={{ Str::slug($cat) }}" style="
                            color:#8B949E;text-decoration:none;font-size:13px;
                            display:flex;align-items:center;gap:6px;transition:color .2s;
                        " onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">
                            <i class="fas fa-chevron-right" style="font-size:9px;"></i>
                            {{ $cat }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Columna: Mi Cuenta --}}
            <div>
                <h4 style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#fff;margin-bottom:20px;">Mi Cuenta</h4>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
                    <li><a href="/dashboard" style="color:#8B949E;text-decoration:none;font-size:13px;transition:color .2s;" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">Inicio</a></li>
                    <li><a href="/perfil" style="color:#8B949E;text-decoration:none;font-size:13px;transition:color .2s;" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">Mi Perfil</a></li>
                    <li><a href="/pedidos" style="color:#8B949E;text-decoration:none;font-size:13px;transition:color .2s;" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">Mis Pedidos</a></li>
                    <li><a href="/carrito" style="color:#8B949E;text-decoration:none;font-size:13px;transition:color .2s;" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">Mi Carrito</a></li>
                    <li><a href="/login" style="color:#8B949E;text-decoration:none;font-size:13px;transition:color .2s;" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">Iniciar Sesión</a></li>
                    <li><a href="/registro" style="color:#8B949E;text-decoration:none;font-size:13px;transition:color .2s;" onmouseover="this.style.color='var(--macuin-red)'" onmouseout="this.style.color='#8B949E'">Registrarse</a></li>
                </ul>
            </div>

            {{-- Columna: Newsletter --}}
            <div>
                <h4 style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#fff;margin-bottom:20px;">Novedades</h4>
                <p style="font-size:13px;color:#8B949E;margin-bottom:16px;line-height:1.6;">
                    Recibe ofertas exclusivas, nuevos productos y promociones especiales para talleres.
                </p>
                <form onsubmit="return false;" style="display:flex;flex-direction:column;gap:8px;">
                    <input
                        type="email"
                        placeholder="Tu correo electrónico"
                        style="
                            padding:11px 14px;
                            background:rgba(255,255,255,.07);
                            border:1px solid rgba(255,255,255,.12);
                            border-radius:4px;color:#fff;
                            font-family:'DM Sans',sans-serif;font-size:13px;
                            outline:none;transition:border-color .2s;
                        "
                        onfocus="this.style.borderColor='var(--macuin-red)'"
                        onblur="this.style.borderColor='rgba(255,255,255,.12)'"
                    >
                    <button type="submit" style="
                        background:var(--macuin-red);color:#fff;border:none;
                        padding:11px 14px;border-radius:4px;cursor:pointer;
                        font-family:'Oswald',sans-serif;font-size:13px;font-weight:600;
                        text-transform:uppercase;letter-spacing:.08em;
                        transition:background .2s;
                    " onmouseover="this.style.background='#8B0D21'" onmouseout="this.style.background='var(--macuin-red)'">
                        SUSCRIBIRME →
                    </button>
                </form>

                {{-- Métodos de pago --}}
                <div style="margin-top:24px;">
                    <p style="font-size:11px;color:#6B7280;text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">Métodos de pago</p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @foreach(['VISA','MC','AMEX','OXXO','SPEI'] as $pay)
                        <div style="
                            background:rgba(255,255,255,.08);
                            border:1px solid rgba(255,255,255,.1);
                            border-radius:4px;padding:4px 8px;
                            font-size:10px;font-weight:600;color:#8B949E;
                            font-family:'Oswald',sans-serif;letter-spacing:.06em;
                        ">{{ $pay }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Línea divisora --}}
    <div style="border-top:1px solid rgba(255,255,255,.08);max-width:1280px;margin:0 auto;"></div>

    {{-- Copyright --}}
    <div style="max-width:1280px;margin:0 auto;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <p style="font-size:13px;color:#6B7280;">
            &copy; {{ date('Y') }} MACUIN Autopartes y Distribución. Todos los derechos reservados.
        </p>
        <div style="display:flex;gap:20px;">
            <a href="#" style="font-size:12px;color:#6B7280;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#6B7280'">Aviso de Privacidad</a>
            <a href="#" style="font-size:12px;color:#6B7280;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#8B949E'" onmouseout="this.style.color='#6B7280'">Términos y Condiciones</a>
        </div>
    </div>

</footer>

{{-- Responsive: ocultar columnas en móvil --}}
<style>
@media (max-width: 768px) {
    footer > div:nth-child(2) > div:first-child {
        grid-template-columns: 1fr 1fr !important;
        gap: 32px !important;
    }
}
@media (max-width: 480px) {
    footer > div:nth-child(2) > div:first-child {
        grid-template-columns: 1fr !important;
    }
}
</style>
