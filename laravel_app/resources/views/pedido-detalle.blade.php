@extends('layouts.app')

@section('title', 'Pedido ' . ($pedido['folio'] ?? ''))

@push('styles')
<style>
    .timeline { position: relative; padding-left: 32px; }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px; top: 0; bottom: 0;
        width: 2px;
        background: var(--macuin-gray);
    }
    .timeline-item { position: relative; padding-bottom: 28px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot {
        position: absolute;
        left: -22px;
        top: 2px;
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 3px solid var(--macuin-gray);
        background: #fff;
        display: flex; align-items: center; justify-content: center;
    }
    .timeline-dot.active {
        border-color: var(--macuin-red);
        background: var(--macuin-red);
    }
    .timeline-dot.done {
        border-color: #16A34A;
        background: #16A34A;
    }
    .timeline-dot i { font-size: 9px; color: #fff; }
    .timeline-title { font-family: 'Oswald', sans-serif; font-size: 14px; font-weight: 600; text-transform: uppercase; }
    .timeline-date { font-size: 12px; color: var(--macuin-muted); margin-top: 2px; }
</style>
@endpush

@section('content')

{{-- Sección: Header --}}
<div style="background:var(--macuin-dark);padding:28px 0;">
    <div class="mac-container">
        <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-steel);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
            <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>
            <a href="/pedidos" style="color:var(--macuin-steel);text-decoration:none;">Mis Pedidos</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>{{ $pedido['folio'] ?? '' }}
        </p>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <h1 style="font-family:'Oswald',sans-serif;font-size:24px;font-weight:700;color:#fff;text-transform:uppercase;">
                Pedido <span style="color:var(--macuin-red);">#{{ $pedido['folio'] ?? '' }}</span>
            </h1>
            <div style="display:flex;gap:10px;">
                <a href="/pedido/{{ $pedido['id'] }}/pdf" target="_blank"
                   class="mac-btn mac-btn-outline mac-btn-sm" style="color:#fff;border-color:rgba(255,255,255,.3);text-decoration:none;">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
            </div>
        </div>
    </div>
</div>

<section style="padding:40px 0 60px;background:var(--macuin-white);">
    <div class="mac-container">
        <div style="display:grid;grid-template-columns:1fr 320px;gap:32px;align-items:start;">

            {{-- Columna principal --}}
            <div>

                {{-- Timeline de estado --}}
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;padding:24px;margin-bottom:24px;">
                    <h3 style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;text-transform:uppercase;margin-bottom:24px;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-route" style="color:var(--macuin-red);"></i>
                        Estado del Pedido
                        @php
                            $estadoColor = match($pedido['estado'] ?? '') {
                                'Completado' => ['bg'=>'rgba(22,163,74,.1)',  'color'=>'#16A34A'],
                                'Cancelado'  => ['bg'=>'rgba(220,38,38,.1)',  'color'=>'#DC2626'],
                                'Enviado'    => ['bg'=>'rgba(139,92,246,.1)', 'color'=>'#8B5CF6'],
                                'Pendiente'  => ['bg'=>'rgba(217,119,6,.1)',  'color'=>'#D97706'],
                                default      => ['bg'=>'rgba(59,130,246,.1)', 'color'=>'#3B82F6'],
                            };
                        @endphp
                        <span style="
                            margin-left:auto;
                            display:inline-flex;align-items:center;gap:6px;
                            padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700;
                            background:{{ $estadoColor['bg'] }};color:{{ $estadoColor['color'] }};
                            font-family:'Oswald',sans-serif;letter-spacing:.06em;
                        ">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $estadoColor['color'] }};"></span>
                            {{ $pedido['estado'] ?? '—' }}
                        </span>
                    </h3>
                    <div style="font-size:13px;color:var(--macuin-muted);">
                        <i class="fas fa-calendar-alt" style="color:var(--macuin-red);margin-right:6px;"></i>
                        Pedido realizado el
                        @if(!empty($pedido['creado_en']))
                            {{ \Carbon\Carbon::parse($pedido['creado_en'])->format('d \d\e F \d\e Y, H:i') }}
                        @endif
                    </div>
                </div>

                {{-- Tabla de artículos --}}
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
                    <div style="background:var(--macuin-dark);padding:14px 20px;">
                        <h3 style="font-family:'Oswald',sans-serif;font-size:15px;font-weight:700;color:#fff;text-transform:uppercase;margin:0;">Artículos del Pedido</h3>
                    </div>
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:var(--macuin-white);">
                                @foreach(['Autoparte','SKU','Cant.','Precio Unit.','Subtotal'] as $h)
                                <th style="font-family:'Oswald',sans-serif;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--macuin-muted);padding:12px 16px;border-bottom:2px solid var(--macuin-gray);text-align:left;white-space:nowrap;">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedido['items'] ?? [] as $d)
                            <tr style="border-bottom:1px solid var(--macuin-gray);">
                                <td style="padding:14px 16px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        @if(!empty($d['imagen']))
                                        <div style="width:56px;height:56px;flex-shrink:0;border:1px solid var(--macuin-gray);border-radius:4px;overflow:hidden;">
                                            <img src="{{ $d['imagen'] }}" alt="{{ $d['nombre'] }}" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                        @endif
                                        <span style="font-family:'Oswald',sans-serif;font-size:13px;font-weight:600;text-transform:uppercase;color:var(--macuin-text);">{{ $d['nombre'] }}</span>
                                    </div>
                                </td>
                                <td style="padding:14px 16px;"><span class="mac-mono" style="font-size:12px;color:var(--macuin-muted);">{{ $d['sku'] }}</span></td>
                                <td style="padding:14px 16px;font-weight:600;text-align:center;">{{ $d['cantidad'] }}</td>
                                <td style="padding:14px 16px;font-size:14px;color:var(--macuin-text);">${{ number_format($d['precio_unitario'], 2) }}</td>
                                <td style="padding:14px 16px;font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:var(--macuin-red);">${{ number_format($d['subtotal'], 2) }}</td>
                            </tr>
                            @endforeach
                            {{-- Totales --}}
                            <tr>
                                <td colspan="3" style="padding:14px 16px;"></td>
                                <td style="padding:14px 16px;font-size:13px;color:var(--macuin-muted);">Subtotal</td>
                                <td style="padding:14px 16px;font-weight:600;">${{ number_format($pedido['subtotal'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td style="padding:4px 16px;font-size:13px;color:var(--macuin-muted);">IVA (16%)</td>
                                <td style="padding:4px 16px;font-weight:600;">${{ number_format($pedido['impuestos'] ?? 0, 2) }}</td>
                            </tr>
                            <tr style="background:var(--macuin-white);">
                                <td colspan="3"></td>
                                <td style="padding:14px 16px;font-family:'Oswald',sans-serif;font-size:15px;font-weight:700;text-transform:uppercase;">TOTAL</td>
                                <td style="padding:14px 16px;font-family:'Oswald',sans-serif;font-size:22px;font-weight:700;color:var(--macuin-red);">${{ number_format($pedido['total'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Sección: Método de envío y notas --}}
                @if(!empty($pedido['metodo_envio']) || !empty($pedido['notas']))
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;padding:20px;margin-top:20px;">
                    @if(!empty($pedido['metodo_envio']))
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--macuin-gray);">
                        <span style="font-size:13px;color:var(--macuin-muted);">Método de envío</span>
                        <span style="font-size:13px;font-weight:600;color:var(--macuin-text);">{{ $pedido['metodo_envio'] }}</span>
                    </div>
                    @endif
                    @if(!empty($pedido['notas']))
                    <div style="padding-top:12px;">
                        <div style="font-size:13px;color:var(--macuin-muted);margin-bottom:6px;">Notas del pedido</div>
                        <div style="font-size:13px;color:var(--macuin-text);line-height:1.6;">{{ $pedido['notas'] }}</div>
                    </div>
                    @endif
                </div>
                @endif

            </div>

            {{-- Panel lateral --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Datos de envío --}}
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
                    <div style="background:var(--macuin-dark);padding:14px 20px;">
                        <h3 style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:700;color:#fff;text-transform:uppercase;margin:0;">Datos de Envío</h3>
                    </div>
                    <div style="padding:20px;display:flex;flex-direction:column;gap:10px;">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <i class="fas fa-map-marker-alt" style="color:var(--macuin-red);font-size:13px;width:14px;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-size:11px;color:var(--macuin-muted);text-transform:uppercase;font-family:'Oswald',sans-serif;letter-spacing:.08em;margin-bottom:1px;">Dirección</div>
                                <div style="font-size:13px;color:var(--macuin-text);font-weight:500;">{{ $pedido['dir_calle'] ?? '—' }}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <i class="fas fa-city" style="color:var(--macuin-red);font-size:13px;width:14px;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-size:11px;color:var(--macuin-muted);text-transform:uppercase;font-family:'Oswald',sans-serif;letter-spacing:.08em;margin-bottom:1px;">Ciudad / Estado</div>
                                <div style="font-size:13px;color:var(--macuin-text);font-weight:500;">
                                    {{ $pedido['dir_ciudad'] ?? '—' }}, {{ $pedido['dir_estado'] ?? '—' }} CP {{ $pedido['dir_cp'] ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <a href="/pedido/{{ $pedido['id'] }}/pdf" target="_blank"
                       class="mac-btn mac-btn-ghost mac-btn-block" style="text-decoration:none;text-align:center;">
                        <i class="fas fa-file-pdf" style="color:var(--macuin-red);"></i>
                        Descargar PDF del Pedido
                    </a>
                    <form action="/pedido/{{ $pedido['id'] }}/reordenar" method="POST">
                        @csrf
                        <button type="submit" class="mac-btn mac-btn-ghost mac-btn-block" style="width:100%;">
                            <i class="fas fa-redo" style="color:var(--macuin-red);"></i>
                            Reordenar estos artículos
                        </button>
                    </form>
                    <a href="/pedidos" style="text-align:center;font-size:13px;color:var(--macuin-muted);padding:8px;text-decoration:none;">← Volver a mis pedidos</a>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection
