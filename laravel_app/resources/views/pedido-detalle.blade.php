@extends('layouts.app')

@section('title', 'Pedido MAC-2024-0089')

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
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>MAC-2024-0089
        </p>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <h1 style="font-family:'Oswald',sans-serif;font-size:24px;font-weight:700;color:#fff;text-transform:uppercase;">
                Pedido <span style="color:var(--macuin-red);">#MAC-2024-0089</span>
            </h1>
            <div style="display:flex;gap:10px;">
                <button class="mac-btn mac-btn-outline mac-btn-sm" style="color:#fff;border-color:rgba(255,255,255,.3);">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
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
                        <span style="
                            margin-left:auto;
                            display:inline-flex;align-items:center;gap:6px;
                            padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700;
                            background:rgba(22,163,74,.1);color:#16A34A;
                            font-family:'Oswald',sans-serif;letter-spacing:.06em;
                        ">
                            <span style="width:6px;height:6px;border-radius:50%;background:#16A34A;"></span>
                            Completado
                        </span>
                    </h3>
                    <div class="timeline">
                        @php
                            $steps = [
                                ['label'=>'Pedido confirmado','date'=>'12 Ene 2025, 9:32 am','status'=>'done'],
                                ['label'=>'Pago verificado','date'=>'12 Ene 2025, 10:15 am','status'=>'done'],
                                ['label'=>'Pedido en preparación','date'=>'13 Ene 2025, 8:00 am','status'=>'done'],
                                ['label'=>'Enviado con DHL','date'=>'14 Ene 2025, 11:30 am','status'=>'done'],
                                ['label'=>'Entregado','date'=>'15 Ene 2025, 2:45 pm','status'=>'done'],
                            ];
                        @endphp
                        @foreach($steps as $step)
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $step['status'] }}">
                                <i class="fas {{ $step['status']==='done' ? 'fa-check' : ($step['status']==='active' ? 'fa-circle' : '') }}"></i>
                            </div>
                            <div class="timeline-title" style="color:{{ $step['status']==='pending' ? 'var(--macuin-muted)' : 'var(--macuin-text)' }};">{{ $step['label'] }}</div>
                            <div class="timeline-date">{{ $step['date'] }}</div>
                        </div>
                        @endforeach
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
                            @php
                                $items = [
                                    ['name'=>'Pastillas de Freno Delanteras Premium Brembo','sku'=>'FRN-001','qty'=>1,'price'=>'$485','sub'=>'$485','img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=80&q=80'],
                                    ['name'=>'Filtro de Aceite Universal Bosch','sku'=>'MOT-034','qty'=>2,'price'=>'$189','sub'=>'$378','img'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=80&q=80'],
                                    ['name'=>'Amortiguador Delantero Gabriel Ultra','sku'=>'SUS-112','qty'=>1,'price'=>'$1,240','sub'=>'$1,240','img'=>'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=80&q=80'],
                                ];
                            @endphp
                            @foreach($items as $item)
                            <tr style="border-bottom:1px solid var(--macuin-gray);">
                                <td style="padding:14px 16px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:56px;height:56px;flex-shrink:0;border:1px solid var(--macuin-gray);border-radius:4px;overflow:hidden;">
                                            <img src="{{ $item['img'] }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                        <a href="/catalogo/1" style="font-family:'Oswald',sans-serif;font-size:13px;font-weight:600;text-transform:uppercase;color:var(--macuin-text);text-decoration:none;">{{ $item['name'] }}</a>
                                    </div>
                                </td>
                                <td style="padding:14px 16px;"><span class="mac-mono" style="font-size:12px;color:var(--macuin-muted);">{{ $item['sku'] }}</span></td>
                                <td style="padding:14px 16px;font-weight:600;text-align:center;">{{ $item['qty'] }}</td>
                                <td style="padding:14px 16px;font-size:14px;color:var(--macuin-text);">{{ $item['price'] }}</td>
                                <td style="padding:14px 16px;font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:var(--macuin-red);">{{ $item['sub'] }}</td>
                            </tr>
                            @endforeach
                            {{-- Totales --}}
                            <tr>
                                <td colspan="3" style="padding:14px 16px;"></td>
                                <td style="padding:14px 16px;font-size:13px;color:var(--macuin-muted);">Subtotal</td>
                                <td style="padding:14px 16px;font-weight:600;">$2,103</td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td style="padding:4px 16px;font-size:13px;color:var(--macuin-muted);">IVA (16%)</td>
                                <td style="padding:4px 16px;font-weight:600;">$336.48</td>
                            </tr>
                            <tr style="background:var(--macuin-white);">
                                <td colspan="3"></td>
                                <td style="padding:14px 16px;font-family:'Oswald',sans-serif;font-size:15px;font-weight:700;text-transform:uppercase;">TOTAL</td>
                                <td style="padding:14px 16px;font-family:'Oswald',sans-serif;font-size:22px;font-weight:700;color:var(--macuin-red);">$2,439.48</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Panel lateral --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Datos de envío --}}
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;overflow:hidden;">
                    <div style="background:var(--macuin-dark);padding:14px 20px;">
                        <h3 style="font-family:'Oswald',sans-serif;font-size:14px;font-weight:700;color:#fff;text-transform:uppercase;margin:0;">Datos de Envío</h3>
                    </div>
                    <div style="padding:20px;display:flex;flex-direction:column;gap:10px;">
                        @foreach([
                            ['fa-user','Destinatario','Juan García López'],
                            ['fa-map-marker-alt','Dirección','Av. López Mateos #1234, Col. Centro'],
                            ['fa-city','Ciudad/Estado','Aguascalientes, Ags. CP 20000'],
                            ['fa-phone','Teléfono','449-123-4567'],
                            ['fa-truck','Guía DHL','1Z999AA10123456784'],
                        ] as [$icon,$label,$val])
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <i class="fas {{ $icon }}" style="color:var(--macuin-red);font-size:13px;width:14px;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <div style="font-size:11px;color:var(--macuin-muted);text-transform:uppercase;font-family:'Oswald',sans-serif;letter-spacing:.08em;margin-bottom:1px;">{{ $label }}</div>
                                <div style="font-size:13px;color:var(--macuin-text);font-weight:500;">{{ $val }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Acciones --}}
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <button class="mac-btn mac-btn-ghost mac-btn-block">
                        <i class="fas fa-file-pdf" style="color:var(--macuin-red);"></i>
                        Descargar PDF del Pedido
                    </button>
                    <button class="mac-btn mac-btn-ghost mac-btn-block">
                        <i class="fas fa-redo" style="color:var(--macuin-red);"></i>
                        Reordenar estos artículos
                    </button>
                    <a href="/pedidos" style="text-align:center;font-size:13px;color:var(--macuin-muted);padding:8px;text-decoration:none;">← Volver a mis pedidos</a>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection
