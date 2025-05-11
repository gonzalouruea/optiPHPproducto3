@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-placeholder bg-light rounded-circle mx-auto mb-3" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user fa-3x text-primary"></i>
                    </div>
                    <h4>{{ $user->nombre }} {{ $user->apellido1 }} {{ $user->apellido2 }}</h4>
                    <p class="text-muted">{{ ucfirst($user->rol) }}</p>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-envelope me-2 text-primary"></i> Email</span>
                        <span>{{ $user->email }}</span>
                    </li>
                    @if($user->direccion)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-map-marker-alt me-2 text-primary"></i> Dirección</span>
                        <span>{{ $user->direccion }}</span>
                    </li>
                    @endif
                    @if($user->ciudad)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-city me-2 text-primary"></i> Ciudad</span>
                        <span>{{ $user->ciudad }}</span>
                    </li>
                    @endif
                    @if($user->pais)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-globe me-2 text-primary"></i> País</span>
                        <span>{{ $user->pais }}</span>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('perfil.editar') }}" class="btn btn-primary w-100">
                    <i class="fas fa-edit me-2"></i> Editar Perfil
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Resumen de Actividad</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-primary">{{ $stats['reservas_totales'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Reservas Totales</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-success">{{ $stats['reservas_activas'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Reservas Activas</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-info">{{ $stats['reservas_proximas'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Próximas Reservas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('reservas.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                            Nueva Reserva
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('reservas.index') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-list fa-2x mb-2"></i><br>
                            Mis Reservas
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('reservas.calendario') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
                            Calendario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
