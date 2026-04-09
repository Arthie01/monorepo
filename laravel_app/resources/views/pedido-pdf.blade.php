<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pedido {{ $pedido['folio'] ?? '' }} — MACUIN</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Arial', sans-serif; font-size: 13px; color: #1A1A1A; background: #fff; }

    .ticket { max-width: 720px; margin: 0 auto; padding: 32px; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 3px solid #C41230; }
    .brand-name { font-family: 'Arial Black', sans-serif; font-size: 28px; font-weight: 900; color: #C41230; letter-spacing: 2px; text-transform: uppercase; }
    .brand-sub { font-size: 10px; color: #6B7280; letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; }
    .header-right { text-align: right; }
    .doc-title { font-size: 18px; font-weight: 700; text-transform: uppercase; color: #0D0D0D; }
    .doc-folio { font-family: 'Courier New', monospace; font-size: 12px; color: #C41230; font-weight: 700; margin-top: 4px; }
    .doc-fecha { font-size: 11px; color: #6B7280; margin-top: 2px; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    .info-box { background: #F5F5F0; border-radius: 6px; padding: 14px 16px; }
    .info-box h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 8px; font-weight: 700; }
    .info-box p { font-size: 13px; color: #1A1A1A; line-height: 1.5; }

    /* Estado */
    .estado-badge { display: inline-block; padding: 3px 12px; border-radius: 100px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .estado-Completado  { background: rgba(22,163,74,.15);  color: #16A34A; }
    .estado-Pendiente   { background: rgba(217,119,6,.15);  color: #D97706; }
    .estado-Enviado     { background: rgba(139,92,246,.15); color: #8B5CF6; }
    .estado-Cancelado   { background: rgba(220,38,38,.15);  color: #DC2626; }
    .estado-default     { background: rgba(59,130,246,.15); color: #3B82F6; }

    /* Tabla de artículos */
    .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #fff; background: #C41230; padding: 8px 14px; margin-bottom: 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    thead th { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7280; padding: 10px 12px; border-bottom: 2px solid #E5E5E0; text-align: left; background: #F5F5F0; }
    thead th:last-child, tbody td:last-child { text-align: right; }
    tbody tr { border-bottom: 1px solid #E5E5E0; }
    tbody tr:last-child { border-bottom: none; }
    tbody td { padding: 10px 12px; font-size: 13px; }
    .item-nombre { font-weight: 600; }
    .item-sku { font-family: 'Courier New', monospace; font-size: 11px; color: #6B7280; display: block; margin-top: 2px; }
    .subtotal-col { font-weight: 700; color: #C41230; }

    /* Totales */
    .totales-wrap { border-top: 2px solid #E5E5E0; padding: 14px 12px; }
    .total-row { display: flex; justify-content: space-between; font-size: 13px; color: #6B7280; margin-bottom: 6px; }
    .total-final { display: flex; justify-content: space-between; font-size: 18px; font-weight: 900; color: #C41230; margin-top: 10px; padding-top: 10px; border-top: 2px solid #C41230; }

    /* Footer */
    .footer { margin-top: 28px; padding-top: 16px; border-top: 1px solid #E5E5E0; text-align: center; font-size: 11px; color: #6B7280; }
    .footer strong { color: #C41230; }

    /* Tabla wrapper con borde */
    .table-wrap { border: 1px solid #E5E5E0; border-radius: 6px; overflow: hidden; margin-bottom: 24px; }

    @media print {
        body { padding: 0; }
        .ticket { padding: 16px; max-width: 100%; }
        .no-print { display: none !important; }
    }
</style>
</head>
<body>
<div class="ticket">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand-name">MACUIN</div>
            <div class="brand-sub">Autopartes &amp; Distribución</div>
        </div>
        <div class="header-right">
            <div class="doc-title">Confirmación de Pedido</div>
            <div class="doc-folio">{{ $pedido['folio'] ?? '' }}</div>
            <div class="doc-fecha">
                @if(!empty($pedido['creado_en']))
                    {{ \Carbon\Carbon::parse($pedido['creado_en'])->format('d/m/Y H:i') }}
                @endif
            </div>
        </div>
    </div>

    {{-- Info: estado y dirección --}}
    <div class="info-grid">
        <div class="info-box">
            <h4>Estado del Pedido</h4>
            @php
                $e = $pedido['estado'] ?? 'default';
                $cls = in_array($e, ['Completado','Pendiente','Enviado','Cancelado']) ? "estado-{$e}" : 'estado-default';
            @endphp
            <span class="estado-badge {{ $cls }}">{{ $e }}</span>
        </div>
        <div class="info-box">
            <h4>Dirección de Envío</h4>
            <p>
                {{ $pedido['dir_calle'] ?? '—' }}<br>
                {{ $pedido['dir_ciudad'] ?? '' }}@if(!empty($pedido['dir_estado'])), {{ $pedido['dir_estado'] }}@endif
                @if(!empty($pedido['dir_cp'])) CP {{ $pedido['dir_cp'] }}@endif
            </p>
        </div>
    </div>

    {{-- Artículos --}}
    <div class="table-wrap">
        <div class="section-title">Artículos del Pedido</div>
        <table>
            <thead>
                <tr>
                    <th>Autoparte</th>
                    <th>SKU</th>
                    <th style="text-align:center;">Cant.</th>
                    <th style="text-align:right;">Precio Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido['items'] ?? [] as $d)
                <tr>
                    <td>
                        <span class="item-nombre">{{ $d['nombre'] }}</span>
                    </td>
                    <td><span class="item-sku">{{ $d['sku'] }}</span></td>
                    <td style="text-align:center;font-weight:600;">{{ $d['cantidad'] }}</td>
                    <td style="text-align:right;">${{ number_format($d['precio_unitario'], 2) }}</td>
                    <td class="subtotal-col">${{ number_format($d['subtotal'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="totales-wrap">
            <div class="total-row"><span>Subtotal</span><span>${{ number_format($pedido['subtotal'] ?? 0, 2) }}</span></div>
            <div class="total-row"><span>Envío</span><span>${{ number_format($pedido['envio'] ?? 0, 2) }}</span></div>
            <div class="total-row"><span>IVA (16%)</span><span>${{ number_format($pedido['impuestos'] ?? 0, 2) }}</span></div>
            <div class="total-final"><span>TOTAL</span><span>${{ number_format($pedido['total'] ?? 0, 2) }}</span></div>
        </div>
    </div>

    {{-- Botón imprimir (no aparece al imprimir) --}}
    <div class="no-print" style="text-align:center;margin-bottom:20px;">
        <button onclick="window.print()" style="
            background:#C41230;color:#fff;border:none;padding:10px 28px;
            border-radius:4px;font-size:13px;font-weight:700;cursor:pointer;
            text-transform:uppercase;letter-spacing:0.5px;
        ">
            Imprimir / Guardar PDF
        </button>
        <a href="/pedido/{{ $pedido['id'] }}" style="display:inline-block;margin-left:16px;font-size:13px;color:#6B7280;text-decoration:none;">← Volver al pedido</a>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <strong>MACUIN Autopartes &amp; Distribución</strong> · Tel: 449-123-4567<br>
        Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

</div>
<script>
    // Auto-abre el diálogo de impresión al cargar
    window.addEventListener('load', () => window.print());
</script>
</body>
</html>
