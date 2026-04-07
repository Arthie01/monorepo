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

        {{-- Filtros --}}
        <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @foreach(['Todos','Pendiente','En proceso','Enviado','Completado','Cancelado'] as $f)
                <button style="
                    padding:7px 16px;border-radius:100px;font-size:12px;font-weight:600;
                    font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:.06em;
                    cursor:pointer;transition:all .2s;
                    {{ $loop->first ? 'background:var(--macuin-red);color:#fff;border:2px solid var(--macuin-red);' : 'background:transparent;color:var(--macuin-muted);border:2px solid var(--macuin-gray);' }}
                " onmouseover="if(!this.classList.contains('active'))this.style.borderColor='var(--macuin-red)'"
                   onmouseout="if(!this.classList.contains('active'))this.style.borderColor='var(--macuin-gray)'">
                    {{ $f }}
                </button>
                @endforeach
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:10px;">
                <input type="date" style="
                    padding:8px 12px;border:1px solid var(--macuin-gray);border-radius:4px;
                    font-family:'DM Sans',sans-serif;font-size:13px;outline:none;
                " onfocus="this.style.borderColor='var(--macuin-red)'" onblur="this.style.borderColor='var(--macuin-gray)'">
                <span style="font-size:12px;color:var(--macuin-muted);">—</span>
                <input type="date" style="
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
                <tbody>
                    @if(session('success'))
                        <tr><td colspan="5" style="padding:12px 16px;">
                            <div style="background:rgba(22,163,74,.1);color:#16a34a;padding:10px 14px;border-radius:6px;font-size:13px;">
                                {{ session('success') }}
                            </div>
                        </td></tr>
                    @endif
                    @forelse($pedidos as $p)
                    <tr>
                        <td>
                            <a href="/pedido/{{ $p['id'] }}" style="
                                font-family:'JetBrains Mono',monospace;
                                font-size:13px;font-weight:500;
                                color:var(--macuin-red);text-decoration:none;
                            ">{{ $p['folio'] }}</a>
                        </td>
                        <td style="color:var(--macuin-muted);font-size:13px;">
                            {{ \Carbon\Carbon::parse($p['creado_en'])->format('d/m/Y') }}
                        </td>
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
                        <td>
                            <span style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:var(--macuin-text);">
                                ${{ number_format($p['total'], 2) }}
                            </span>
                        </td>
                        <td>
                            <a href="/pedido/{{ $p['id'] }}" class="mac-btn mac-btn-outline mac-btn-sm">
                                <i class="fas fa-eye" style="font-size:11px;"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--macuin-muted);">Sin pedidos aún.</td></tr>
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
</style>
@endpush
