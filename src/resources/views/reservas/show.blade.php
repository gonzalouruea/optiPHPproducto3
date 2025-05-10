@extends('layouts.app')

@section('title', 'Detalles de Reserva')

@section('content')
<div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detalles de Reserva #{{ $reserva->id_reserva }}</h4>
        <div>
            @php
                $puedeEditar = Auth::user()->esAdmin() || $reserva->puedeSerModificadaPorUsuario();
            @endphp
            
            @if($puedeEditar)
                <a href="{{ route('reservas.edit', $reserva->id_reserva) }}" class="btn btn-light">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Información General</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Localizador:</strong> {{ $reserva->localizador }}</p>
                        <p><strong>Cliente:</strong> {{ $reserva->email_cliente }}</p>
                        <p><strong>Fecha de Reserva:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y H:i') }}</p>
                        <p><strong>Última Modificación:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_modificacion)->format('d/m/Y H:i') }}</p>
                        <p><strong>Tipo de Reserva:</strong> {{ $reserva->tipoReserva->Descripción ?? 'Desconocido' }}</p>
                        <p><strong>Número de Viajeros:</strong> {{ $reserva->num_viajeros }}</p>
                        <p><strong>Vehículo:</strong> {{ $reserva->vehiculo->Descripción ?? 'Desconocido' }}</p>
                        <p><strong>Hotel:</strong> {{ $reserva->hotel->Usuario ?? 'Desconocido' }}</p>
                        
                        @if($reserva->creado_por_admin)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Esta reserva fue creada por un administrador.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                @if($reserva->id_tipo_reserva == 1 || $reserva->id_tipo_reserva == 3)
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Datos de Llegada</h5>
                    </div>
                    <div class="card-body">
                        @if($reserva->fecha_entrada)
                            <p><strong>Fecha de Llegada:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</p>
                        @endif
                        
                        @if($reserva->hora_entrada)
                            <p><strong>Hora de Llegada:</strong> {{ \Carbon\Carbon::parse($reserva->hora_entrada)->format('H:i') }}</p>
                        @endif
                        
                        @if($reserva->numero_vuelo_entrada)
                            <p><strong>Número de Vuelo:</strong> {{ $reserva->numero_vuelo_entrada }}</p>
                        @endif
                        
                        @if($reserva->origen_vuelo_entrada)
                            <p><strong>Aeropuerto de Origen:</strong> {{ $reserva->origen_vuelo_entrada }}</p>
                        @endif
                        
                        @if(!$reserva->fecha_entrada && !$reserva->hora_entrada && !$reserva->numero_vuelo_entrada)
                            <div class="alert alert-info">No hay datos de llegada registrados.</div>
                        @endif
                    </div>
                </div>
                @endif
                
                @if($reserva->id_tipo_reserva == 2 || $reserva->id_tipo_reserva == 3)
                <div class="card mb-3">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Datos de Salida</h5>
                    </div>
                    <div class="card-body">
                        @if($reserva->fecha_vuelo_salida)
                            <p><strong>Fecha de Vuelo:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_vuelo_salida)->format('d/m/Y') }}</p>
                        @endif
                        
                        @if($reserva->hora_vuelo_salida)
                            <p><strong>Hora de Vuelo:</strong> {{ \Carbon\Carbon::parse($reserva->hora_vuelo_salida)->format('H:i') }}</p>
                        @endif
                        
                        @if($reserva->hora_recogida)
                            <p><strong>Hora de Recogida:</strong> {{ \Carbon\Carbon::parse($reserva->hora_recogida)->format('H:i') }}</p>
                        @endif
                        
                        @if(!$reserva->fecha_vuelo_salida && !$reserva->hora_vuelo_salida)
                            <div class="alert alert-info">No hay datos de salida registrados.</div>
                        @endif
                    </div>
                </div>
                @endif
                
                @if(!$puedeEditar && !Auth::user()->esAdmin())
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i> <strong>Nota:</strong> No se puede modificar esta reserva porque faltan menos de 48 horas para la fecha programada.
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between">
            <a href="{{ route('reservas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Reservas
            </a>
            
            @if($puedeEditar)
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Cancelar Reserva
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
@if($puedeEditar)
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea cancelar esta reserva? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <form action="{{ route('reservas.destroy', $reserva->id_reserva) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
