@extends('layouts.app')

@section('title', 'Editar Reserva')

@section('styles')
<style>
    .hidden-section {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Editar Reserva #{{ $reserva->id_reserva }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('reservas.update', $reserva->id_reserva) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Localizador: <strong>{{ $reserva->localizador }}</strong>
                <br>
                <i class="fas fa-calendar-alt"></i> Fecha de reserva: <strong>{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y H:i') }}</strong>
                <br>
                <i class="fas fa-user"></i> Cliente: <strong>{{ $reserva->email_cliente }}</strong>
            </div>
            
            <!-- Tipo Trayecto (solo mostrar, no editable) -->
            <div class="mb-3">
                <label class="form-label">Tipo de Trayecto</label>
                <input type="text" class="form-control" value="{{ $reserva->tipoReserva->Descripción ?? 'Desconocido' }}" readonly>
                <input type="hidden" name="id_tipo_reserva" value="{{ $reserva->id_tipo_reserva }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Número de Pasajeros</label>
                <input type="number" name="num_viajeros" class="form-control @error('num_viajeros') is-invalid @enderror" 
                       value="{{ old('num_viajeros', $reserva->num_viajeros) }}" required min="1">
                @error('num_viajeros')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Vehículo</label>
                <select name="id_vehiculo" class="form-select @error('id_vehiculo') is-invalid @enderror" required>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id_vehiculo }}" 
                            {{ old('id_vehiculo', $reserva->id_vehiculo) == $vehiculo->id_vehiculo ? 'selected' : '' }}>
                            {{ $vehiculo->Descripción }} 
                        </option>
                    @endforeach
                </select>
                @error('id_vehiculo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Hotel (destino/recogida)</label>
                <select name="id_hotel" class="form-select @error('id_hotel') is-invalid @enderror" required>
                    @foreach($hoteles as $hotel)
                        <option value="{{ $hotel->id_hotel }}" 
                            {{ old('id_hotel', $reserva->id_hotel) == $hotel->id_hotel ? 'selected' : '' }}>
                            {{ $hotel->Usuario }} ({{ $hotel->descripcion }})
                        </option>
                    @endforeach
                </select>
                @error('id_hotel')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Llegada -->
            <div id="camposLlegada" class="{{ in_array($reserva->id_tipo_reserva, [1, 3]) ? '' : 'hidden-section' }} mb-4">
                <h5 class="border-bottom pb-2 mb-3">Datos de Llegada</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha Llegada</label>
                        <input type="date" name="fecha_entrada" class="form-control @error('fecha_entrada') is-invalid @enderror" 
                               value="{{ old('fecha_entrada', $reserva->fecha_entrada) }}">
                        @error('fecha_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora Llegada</label>
                        <input type="time" name="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror" 
                               value="{{ old('hora_entrada', $reserva->hora_entrada) }}">
                        @error('hora_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Número Vuelo (Llegada)</label>
                        <input type="text" name="numero_vuelo_entrada" class="form-control @error('numero_vuelo_entrada') is-invalid @enderror" 
                               value="{{ old('numero_vuelo_entrada', $reserva->numero_vuelo_entrada) }}">
                        @error('numero_vuelo_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aeropuerto de Origen</label>
                        <input type="text" name="origen_vuelo_entrada" class="form-control @error('origen_vuelo_entrada') is-invalid @enderror" 
                               value="{{ old('origen_vuelo_entrada', $reserva->origen_vuelo_entrada) }}">
                        @error('origen_vuelo_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Salida -->
            <div id="camposSalida" class="{{ in_array($reserva->id_tipo_reserva, [2, 3]) ? '' : 'hidden-section' }} mb-4">
                <h5 class="border-bottom pb-2 mb-3">Datos de Salida</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha Vuelo Salida</label>
                        <input type="date" name="fecha_vuelo_salida" class="form-control @error('fecha_vuelo_salida') is-invalid @enderror" 
                               value="{{ old('fecha_vuelo_salida', $reserva->fecha_vuelo_salida) }}">
                        @error('fecha_vuelo_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora Vuelo Salida</label>
                        <input type="time" name="hora_vuelo_salida" class="form-control @error('hora_vuelo_salida') is-invalid @enderror" 
                               value="{{ old('hora_vuelo_salida', $reserva->hora_vuelo_salida) }}">
                        @error('hora_vuelo_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora de Recogida</label>
                        <input type="time" name="hora_recogida" class="form-control @error('hora_recogida') is-invalid @enderror" 
                               value="{{ old('hora_recogida', $reserva->hora_recogida) }}">
                        @error('hora_recogida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                
                <div>
                    <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Cancelar Reserva
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
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
@endsection
