@extends('layouts.app')

@section('title', 'Mis Pedidos')

@push('styles')
<style>
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th {
        font-family: 'Oswald', sans-serif;
        font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: .1em;
        color: var(--macuin-muted);
        padding: 12px 16px;
        border-bottom: 2px solid var(--macuin-gray);
        text-align: left;
        white-space: nowrap;
    }
    .orders-table td { padding: 14px 16px; border-bottom: 1px solid var(--macuin-gray); vertical-align: middle; font-size: 14px; }
    .orders-table tbody tr:hover td { background: var(--macuin-white); }
    .orders-table tbody tr:last-child td { border-bottom: none; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 100px;
        font-size: 11px; font-weight: 700;
        font-family: 'Oswald', sans-serif;
        text-transform: uppercase; letter-spacing: .06em;
    }
    .status-pill::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .status-pending    { background:rgba(217,119,6,.1);  color:#D97706; }
    .status-processing { background:rgba(59,130,246,.1); color:#3B82F6; }
    .status-shipped    { background:rgba(139,92,246,.1); color:#8B5CF6; }
    .status-completed  { background:rgba(22,163,74,.1);  color:#16A34A; }
    .status-cancelled  { background:rgba(220,38,38,.1);  color:#DC2626; }
</style>
@endpush

@section('content')

{{-- Sección: Header --}}
<div style="background:var(--macuin-dark);padding:28px 0;">
    <div class="mac-container">
        <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-steel);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
            <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>Mis Pedidos
        </p>
        <h1 style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:#fff;text-transform:uppercase;">
            <i class="fas fa-box" style="color:var(--macuin-red);margin-right:10px;"></i>Mis Pedidos
        </h1>
    </div>
</div>

<section style="padding:40px 0 60px;background:var(--macuin-white);">
    <div class="mac-container">

        {{-- Filtros (client-side JS) --}}
        <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            {{-- Botones de estado --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;" id="filtros-estado">
                @foreach(['Todos','Pendiente','En proceso','Enviado','Completado','Cancelado'] as $f)
                <button data-estado="{{ $loop->first ? '' : $f }}"
                    class="filtro-btn {{ $loop->first ? 'filtro-activo' : '' }}"
                    style="
                        padding:7px 16px;border-radius:100px;font-size:12px;font-weight:600;
                        font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:.06em;
                        cursor:pointer;transition:all .2s;
                        {{ $loop->first ? 'background:var(--macuin-red);color:#fff;border:2px solid var(--macuin-red);' : 'background:transparent;color:var(--macuin-muted);border:2px solid var(--macuin-gray);' }}
                    ">{{ $f }}</button>
                @endforeach
            </div>
            {{-- Filtro de fechas --}}
            <div style="margin-left:auto;display:flex;align-items:center;gap:10px;">
                <input type="date" id="fecha-inicio" style="
                    padding:8px 12px;border:1px solid var(--macuin-gray);border-radius:4px;
                    font-family:'DM Sans',sans-serif;font-size:13px;outline:none;
                " onfocus="this.style.borderColor='var(--macuin-red)'" onblur="this.style.borderColor='var(--macuin-gray)'">
                <span style="font-size:12px;color:var(--macuin-muted);">—</span>
                <input type="date" id="fecha-fin" style="
                    padding:8px 12px;border:1px solid var(--macuin-gray);border-radius:4px;
                    font-family:'DM Sans',sans-serif;font-size:13px;outline:none;
                " onfocus="this.style.borderColor='var(--macuin-red)'" onblur="this.style.borderColor='var(--macuin-gray)'">
            </div>
        </div>

        {{-- Tabla de pedidos --}}
        <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
            <table class="orders-table">
                <thead>
                    <tr style="background:var(--macuin-white);">
                        <th># Folio</th>
                        <th>Fecha</th>
                        <th>Artículos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="orders-tbody">
                    @if(session('success'))
                        <tr><td colspan="5" style="padding:12px 16px;">
                            <div style="background:rgba(22,163,74,.1);color:#16a34a;padding:10px 14px;border-radius:6px;font-size:13px;">
                                {{ session('success') }}
                            </div>
                        </td></tr>
                    @endif
                    @forelse($pedidos as $p)
                    <tr data-estado="{{ $p['estado'] }}" data-fecha="{{ substr($p['creado_en'], 0, 10) }}">
                        {{-- Col 1: Folio --}}
                        <td>
                            <a href="/pedido/{{ $p['id'] }}" style="
                                font-family:'JetBrains Mono',monospace;
                                font-size:13px;font-weight:500;
                                color:var(--macuin-red);text-decoration:none;
                            ">{{ $p['folio'] }}</a>
                        </td>
                        {{-- Col 2: Fecha --}}
                        <td style="color:var(--macuin-muted);font-size:13px;">
                            {{ \Carbon\Carbon::parse($p['creado_en'])->format('d/m/Y') }}
                        </td>
                        {{-- Col 3: Artículos --}}
                        <td style="font-size:13px;color:var(--macuin-text);">
                            @if(!empty($p['primer_articulo']))
                                {{ $p['primer_articulo'] }}
                                @if(($p['items_count'] ?? 1) > 1)
                                    <span style="color:var(--macuin-muted);font-size:11px;">+{{ $p['items_count'] - 1 }} más</span>
                                @endif
                            @else
                                <span style="color:var(--macuin-muted);">—</span>
                            @endif
                        </td>
                        {{-- Col 4: Total --}}
                        <td>
                            <span style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:var(--macuin-text);">
                                ${{ number_format($p['total'], 2) }}
                            </span>
                        </td>
                        {{-- Col 5: Estado --}}
                        <td>
                            @php
                                $cls = match($p['estado']) {
                                    'Completado' => 'status-completed',
                                    'Cancelado'  => 'status-cancelled',
                                    'Pendiente'  => 'status-pending',
                                    'Enviado'    => 'status-shipped',
                                    default      => 'status-processing',
                                };
                            @endphp
                            <span class="status-pill {{ $cls }}">{{ $p['estado'] }}</span>
                        </td>
                        {{-- Col 6: Acciones --}}
                        <td>
                            <a href="/pedido/{{ $p['id'] }}" class="mac-btn mac-btn-outline mac-btn-sm">
                                <i class="fas fa-eye" style="font-size:11px;"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--macuin-muted);">Sin pedidos aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mac-pagination">
            <a href="#" class="mac-page-btn"><i class="fas fa-chevron-left" style="font-size:11px;"></i></a>
            <a href="#" class="mac-page-btn active">1</a>
            <a href="#" class="mac-page-btn">2</a>
            <a href="#" class="mac-page-btn"><i class="fas fa-chevron-right" style="font-size:11px;"></i></a>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .mac-pagination { display: flex; gap: 6px; align-items: center; justify-content: center; margin-top: 32px; }
    .mac-page-btn { width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:4px;font-size:13px;font-weight:600;text-decoration:none;color:var(--macuin-text);border:1px solid var(--macuin-gray);transition:all .2s; }
    .mac-page-btn:hover,.mac-page-btn.active { background:var(--macuin-red);color:#fff;border-color:var(--macuin-red); }
    .filtro-activo { background:var(--macuin-red) !important; color:#fff !important; border-color:var(--macuin-red) !important; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    let estadoActivo = '';

    const filas = () => document.querySelectorAll('#orders-tbody tr[data-estado]');

    function aplicar() {
        const inicio = document.getElementById('fecha-inicio').value; // YYYY-MM-DD o ''
        const fin    = document.getElementById('fecha-fin').value;

        filas().forEach(tr => {
            const estado = tr.dataset.estado;
            const fecha  = tr.dataset.fecha; // YYYY-MM-DD

            const passEstado = !estadoActivo || estado === estadoActivo;
            const passInicio = !inicio || fecha >= inicio;
            const passFin    = !fin    || fecha <= fin;

            tr.style.display = (passEstado && passInicio && passFin) ? '' : 'none';
        });
    }

    // Botones de estado
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            estadoActivo = btn.dataset.estado;

            document.querySelectorAll('.filtro-btn').forEach(b => {
                b.classList.remove('filtro-activo');
                b.style.background = 'transparent';
                b.style.color      = 'var(--macuin-muted)';
                b.style.borderColor = 'var(--macuin-gray)';
            });
            btn.classList.add('filtro-activo');
            btn.style.background  = 'var(--macuin-red)';
            btn.style.color       = '#fff';
            btn.style.borderColor = 'var(--macuin-red)';

            aplicar();
        });
    });

    // Inputs de fecha
    document.getElementById('fecha-inicio').addEventListener('change', aplicar);
    document.getElementById('fecha-fin').addEventListener('change', aplicar);
})();
</script>
@endpush
