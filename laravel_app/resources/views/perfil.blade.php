@extends('layouts.app')

@section('title', 'Mi Perfil')

@push('styles')
<style>
    .profile-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 32px;
        align-items: start;
    }
    .profile-sidebar {
        background: #fff;
        border: 1px solid var(--macuin-gray);
        border-radius: 8px;
        overflow: hidden;
        position: sticky;
        top: 88px;
    }
    .profile-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 500;
        color: var(--macuin-muted);
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all .2s;
    }
    .profile-nav-link:hover, .profile-nav-link.active {
        color: var(--macuin-red);
        border-left-color: var(--macuin-red);
        background: rgba(196,18,48,.04);
    }
    .profile-nav-link i { width: 16px; text-align: center; }

    .profile-section {
        background: #fff;
        border: 1px solid var(--macuin-gray);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .profile-section__header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--macuin-gray);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .profile-section__title {
        font-family: 'Oswald', sans-serif;
        font-size: 15px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .08em;
        color: var(--macuin-text);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .profile-section__title i { color: var(--macuin-red); }
    .profile-section__body { padding: 24px; }

    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    @media (max-width: 900px) {
        .profile-layout { grid-template-columns: 1fr; }
        .profile-sidebar { position: static; }
        .form-grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Sección: Header --}}
<div style="background:var(--macuin-dark);padding:28px 0;">
    <div class="mac-container">
        <p style="font-family:'Oswald',sans-serif;font-size:12px;color:var(--macuin-steel);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
            <a href="/dashboard" style="color:var(--macuin-steel);text-decoration:none;">Inicio</a>
            <i class="fas fa-chevron-right" style="font-size:9px;margin:0 6px;"></i>Mi Perfil
        </p>
        <h1 style="font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;color:#fff;text-transform:uppercase;">
            <i class="fas fa-user-circle" style="color:var(--macuin-red);margin-right:10px;"></i>Mi Cuenta
        </h1>
    </div>
</div>

<section style="padding:40px 0 60px;background:var(--macuin-white);">
    <div class="mac-container">
        <div class="profile-layout">

            {{-- Sidebar de Perfil --}}
            <aside class="profile-sidebar">
                {{-- Avatar --}}
                <div style="padding:24px;text-align:center;border-bottom:1px solid var(--macuin-gray);">
                    @php
                        $iniciales = strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1) . substr($usuario['apellidos'] ?? '', 0, 1));
                    @endphp
                    <div style="
                        width:80px;height:80px;border-radius:50%;
                        background:var(--macuin-red);
                        font-family:'Oswald',sans-serif;font-size:28px;font-weight:700;
                        color:#fff;display:flex;align-items:center;justify-content:center;
                        margin:0 auto 12px;
                    ">{{ $iniciales }}</div>
                    <div style="font-family:'Oswald',sans-serif;font-size:16px;font-weight:700;color:var(--macuin-text);">
                        {{ $usuario['nombre'] ?? '' }} {{ $usuario['apellidos'] ?? '' }}
                    </div>
                    <div style="font-size:12px;color:var(--macuin-muted);margin-top:2px;">{{ $usuario['email'] ?? '' }}</div>
                    <div style="
                        display:inline-flex;align-items:center;gap:4px;
                        margin-top:10px;padding:3px 10px;border-radius:100px;
                        background:rgba(22,163,74,.1);color:#16A34A;font-size:11px;font-weight:600;font-family:'Oswald',sans-serif;
                    ">
                        <span style="width:5px;height:5px;border-radius:50%;background:#16A34A;"></span>
                        Cliente Activo
                    </div>
                </div>
                {{-- Navegación --}}
                <nav style="padding:8px 0;">
                    <a href="#info"      class="profile-nav-link active"><i class="fas fa-user"></i> Información Personal</a>
                    <a href="#password"  class="profile-nav-link"><i class="fas fa-lock"></i> Cambiar Contraseña</a>
                    <a href="#pedidos"   class="profile-nav-link"><i class="fas fa-box"></i> Mis Pedidos</a>
                    <a href="/pedidos"   class="profile-nav-link"><i class="fas fa-history"></i> Historial Completo</a>
                </nav>
                <div style="padding:12px;border-top:1px solid var(--macuin-gray);">
                    <a href="/logout" class="mac-btn mac-btn-ghost mac-btn-block mac-btn-sm" style="color:var(--macuin-muted);">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </aside>

            {{-- Contenido principal --}}
            <div>

                {{-- Sección: Información Personal --}}
                <div id="info" class="profile-section">
                    <div class="profile-section__header">
                        <div class="profile-section__title">
                            <i class="fas fa-user"></i>
                            Información Personal
                        </div>
                        <button class="mac-btn mac-btn-outline mac-btn-sm" onclick="toggleEdit('personal-form')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                    <div class="profile-section__body">
                        @if(session('success_info'))
                            <div style="background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#16A34A;display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-check-circle"></i> {{ session('success_info') }}
                            </div>
                        @endif
                        @if($errors->has('api'))
                            <div style="background:rgba(196,18,48,.08);border:1px solid rgba(196,18,48,.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:var(--macuin-red);display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('api') }}
                            </div>
                        @endif
                        <form id="personal-form" method="POST" action="/perfil">
                            @csrf
                            @method('PUT')
                            <div class="form-grid-2">
                                <div class="mac-form-group">
                                    <label class="mac-label">Nombre(s)</label>
                                    <input type="text" name="nombre" class="mac-input" value="{{ $usuario['nombre'] ?? '' }}" disabled>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Apellidos</label>
                                    <input type="text" name="apellidos" class="mac-input" value="{{ $usuario['apellidos'] ?? '' }}" disabled>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Correo Electrónico</label>
                                    <div class="mac-input-icon">
                                        <i class="mac-input-icon__icon fas fa-envelope"></i>
                                        <input type="email" name="email" class="mac-input" value="{{ $usuario['email'] ?? '' }}" disabled>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Teléfono</label>
                                    <div class="mac-input-icon">
                                        <i class="mac-input-icon__icon fas fa-phone"></i>
                                        <input type="tel" name="telefono" class="mac-input" value="{{ $usuario['telefono'] ?? '' }}" placeholder="449-123-4567" disabled>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Tipo de Cliente</label>
                                    <input type="text" class="mac-input" value="{{ $usuario['tipo_cliente'] ?? '—' }}" disabled style="background:var(--macuin-gray);color:var(--macuin-muted);">
                                    <p style="font-size:11px;color:var(--macuin-muted);margin-top:5px;display:flex;align-items:center;gap:5px;">
                                        <i class="fas fa-info-circle" style="color:var(--macuin-steel);"></i>
                                        Para cambiar tu tipo de cliente, contacta al equipo de ventas.
                                    </p>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Empresa</label>
                                    <input type="text" class="mac-input" value="{{ $usuario['empresa'] ?? '—' }}" disabled style="background:var(--macuin-gray);color:var(--macuin-muted);">
                                    <p style="font-size:11px;color:var(--macuin-muted);margin-top:5px;display:flex;align-items:center;gap:5px;">
                                        <i class="fas fa-info-circle" style="color:var(--macuin-steel);"></i>
                                        El equipo de ventas actualiza los datos de empresa.
                                    </p>
                                </div>
                            </div>
                            <div id="personal-actions" style="display:none;gap:12px;margin-top:8px;">
                                <button type="submit" class="mac-btn mac-btn-primary mac-btn-sm">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <button type="button" class="mac-btn mac-btn-ghost mac-btn-sm" onclick="toggleEdit('personal-form', true)">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sección: Cambiar Contraseña --}}
                <div id="password" class="profile-section">
                    <div class="profile-section__header">
                        <div class="profile-section__title">
                            <i class="fas fa-lock"></i>
                            Cambiar Contraseña
                        </div>
                    </div>
                    <div class="profile-section__body">
                        @if(session('success_password'))
                            <div style="background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#16A34A;display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-check-circle"></i> {{ session('success_password') }}
                            </div>
                        @endif
                        @if($errors->has('current_password'))
                            <div style="background:rgba(196,18,48,.08);border:1px solid rgba(196,18,48,.25);border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:var(--macuin-red);display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('current_password') }}
                            </div>
                        @endif
                        <form method="POST" action="/perfil/password">
                            @csrf
                            @method('PUT')
                            <div style="max-width:400px;display:flex;flex-direction:column;gap:0;">
                                <div class="mac-form-group">
                                    <label class="mac-label">Contraseña Actual</label>
                                    <div class="mac-input-icon" style="position:relative;">
                                        <i class="mac-input-icon__icon fas fa-lock"></i>
                                        <input type="password" id="cp0" name="current_password" class="mac-input" placeholder="••••••••">
                                        <button type="button" onclick="togglePass('cp0',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--macuin-muted);font-size:13px;padding:0;"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Nueva Contraseña</label>
                                    <div class="mac-input-icon" style="position:relative;">
                                        <i class="mac-input-icon__icon fas fa-lock"></i>
                                        <input type="password" id="cp1" name="password" class="mac-input" placeholder="••••••••">
                                        <button type="button" onclick="togglePass('cp1',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--macuin-muted);font-size:13px;padding:0;"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <div class="mac-form-group">
                                    <label class="mac-label">Confirmar Nueva Contraseña</label>
                                    <div class="mac-input-icon" style="position:relative;">
                                        <i class="mac-input-icon__icon fas fa-lock"></i>
                                        <input type="password" id="cp2" name="password_confirmation" class="mac-input" placeholder="••••••••">
                                        <button type="button" onclick="togglePass('cp2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--macuin-muted);font-size:13px;padding:0;"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <button type="submit" class="mac-btn mac-btn-primary mac-btn-sm" style="align-self:flex-start;">
                                    <i class="fas fa-key"></i> Actualizar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sección: Últimos Pedidos --}}
                <div id="pedidos" class="profile-section">
                    <div class="profile-section__header">
                        <div class="profile-section__title">
                            <i class="fas fa-box"></i>
                            Últimos Pedidos
                        </div>
                        <a href="/pedidos" class="mac-btn mac-btn-ghost mac-btn-sm">Ver todo el historial</a>
                    </div>
                    <div style="padding:0;">
                        @forelse($pedidos as $p)
                        @php
                            $estado = $p['estado'] ?? '';
                            $estadoStyle = match($estado) {
                                'Completado' => 'background:rgba(22,163,74,.1);color:#16A34A;',
                                'Enviado'    => 'background:rgba(139,92,246,.1);color:#8B5CF6;',
                                'Cancelado'  => 'background:rgba(196,18,48,.1);color:#C41230;',
                                'Pendiente'  => 'background:rgba(234,179,8,.1);color:#CA8A04;',
                                default      => 'background:rgba(59,130,246,.1);color:#3B82F6;',
                            };
                            $fecha = $p['fecha_pedido'] ?? $p['created_at'] ?? '';
                            $fechaFormato = $fecha ? \Carbon\Carbon::parse($fecha)->format('d M Y') : '—';
                            $items = count($p['detalles'] ?? []);
                        @endphp
                        <div style="display:flex;align-items:center;gap:16px;padding:14px 24px;border-bottom:1px solid var(--macuin-gray);flex-wrap:wrap;">
                            <div style="flex:1;min-width:140px;">
                                <a href="/pedido/{{ $p['id'] }}" style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--macuin-red);text-decoration:none;font-weight:500;">{{ $p['folio'] }}</a>
                                <div style="font-size:12px;color:var(--macuin-muted);margin-top:2px;">
                                    {{ $fechaFormato }}@if($items > 0) · {{ $items }} {{ $items === 1 ? 'artículo' : 'artículos' }}@endif
                                </div>
                            </div>
                            <div>
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:100px;font-size:10px;font-weight:700;font-family:'Oswald',sans-serif;{{ $estadoStyle }}">
                                    <span style="width:5px;height:5px;border-radius:50%;background:currentColor;"></span>
                                    {{ $estado }}
                                </span>
                            </div>
                            <div style="font-family:'Oswald',sans-serif;font-size:18px;font-weight:700;color:var(--macuin-text);">
                                ${{ number_format($p['total'] ?? 0, 2) }}
                            </div>
                            <a href="/pedido/{{ $p['id'] }}" class="mac-btn mac-btn-outline mac-btn-sm">Ver →</a>
                        </div>
                        @empty
                        <div style="padding:40px 24px;text-align:center;">
                            <i class="fas fa-box-open" style="font-size:32px;color:var(--macuin-gray);display:block;margin-bottom:12px;"></i>
                            <p style="font-family:'Oswald',sans-serif;font-size:14px;color:var(--macuin-muted);text-transform:uppercase;letter-spacing:.05em;margin:0 0 16px;">Aún no tienes pedidos</p>
                            <a href="/catalogo" class="mac-btn mac-btn-primary mac-btn-sm">Explorar catálogo</a>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
    // Campos que el cliente NO puede editar (gestionados por ventas)
    const LOCKED_NAMES = ['tipo_cliente', 'empresa'];

    function toggleEdit(formId, cancel = false) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input, select, textarea');
        const actions = document.getElementById(formId.replace('-form', '-actions'));
        if (cancel) {
            inputs.forEach(i => i.disabled = true);
            if (actions) actions.style.display = 'none';
        } else {
            inputs.forEach(i => {
                if (!LOCKED_NAMES.includes(i.name)) i.disabled = false;
            });
            if (actions) actions.style.display = 'flex';
        }
    }
</script>
@endpush
