@extends('layouts.app')

@section('title', 'Nueva Reserva')

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
        <h4 class="mb-0">Nueva Reserva</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reservas.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Hotel</label>
                    <select class="form-select @error('id_hotel') is-invalid @enderror" name="id_hotel" required>
                        <option value="">Seleccionar hotel...</option>
                        @forelse($hoteles as $hotel)
                            <option value="{{ $hotel->id_hotel }}" {{ old('id_hotel') == $hotel->id_hotel ? 'selected' : '' }}>
                                {{ $hotel->Usuario }} (Zona: {{ $hotel->id_zona }})
                            </option>
                        @empty
                            <option value="" disabled>No hay hoteles disponibles</option>
                        @endforelse
                    </select>
                    @error('id_hotel')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de Reserva</label>
                    <select class="form-select @error('tipoReserva') is-invalid @enderror" name="tipoReserva" required>
                        <option value="">Seleccionar tipo...</option>
                        @forelse($tiposReserva as $tipo)
                            <option value="{{ $tipo->id_tipo_reserva }}" {{ old('tipoReserva') == $tipo->id_tipo_reserva ? 'selected' : '' }}>
                                {{ $tipo->Descripción }}
                            </option>
                        @empty
                            <option value="" disabled>No hay tipos de reserva disponibles</option>
                        @endforelse
                    </select>
                    @error('id_tipo_reserva')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vehículo</label>
                    <select class="form-select @error('vehiculo') is-invalid @enderror" name="vehiculo" required>
                        <option value="">Seleccionar vehículo...</option>
                        @forelse($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id_vehiculo }}" {{ old('vehiculo') == $vehiculo->id_vehiculo ? 'selected' : '' }}>
                                {{ $vehiculo->Descripción }}
                            </option>
                        @empty
                            <option value="" disabled>No hay vehículos disponibles</option>
                        @endforelse
                    </select>
                    @error('id_vehiculo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Usuario</label>
                    <select class="form-select @error('id_viajero') is-invalid @enderror" name="id_viajero">
                        <option value="">Seleccionar usuario...</option>
                        @forelse($viajeros as $viajero)
                            <option value="{{ $viajero->id_viajero }}" {{ old('id_viajero') == $viajero->id_viajero ? 'selected' : '' }}>
                                {{ $viajero->nombre }} {{ $viajero->apellido1 }} ({{ $viajero->email }})
                            </option>
                        @empty
                            <option value="" disabled>No hay usuarios disponibles</option>
                        @endforelse
                    </select>
                    @error('id_viajero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user-plus"></i> Registrar Nuevo Usuario
                    </a>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Número de Pasajeros</label>
                    <input type="number" name="num_viajeros" class="form-control @error('num_viajeros') is-invalid @enderror" min="1" required>
                    @error('num_viajeros')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hora de Recogida</label>
                    <input type="time" name="hora_recogida" class="form-control @error('hora_recogida') is-invalid @enderror" required>
                    @error('hora_recogida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha / Hora Recogida en HOTEL</label>
                    <input type="date" name="fecha_entrada" class="form-control @error('fecha_entrada') is-invalid @enderror" required>
                    <input type="time" name="hora_entrada" class="form-control mt-1 @error('hora_entrada') is-invalid @enderror" required>
                    @error('fecha_entrada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('hora_entrada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número de Vuelo</label>
                    <input type="text" name="numero_vuelo_entrada" class="form-control @error('numero_vuelo_entrada') is-invalid @enderror" required>
                    @error('numero_vuelo_entrada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Origen del Vuelo</label>
                    <input type="text" name="origen_vuelo_entrada" class="form-control @error('origen_vuelo_entrada') is-invalid @enderror" required>
                    @error('origen_vuelo_entrada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha / Hora Vuelo de Salida</label>
                    <input type="date" name="fecha_vuelo_salida" class="form-control @error('fecha_vuelo_salida') is-invalid @enderror">
                    <input type="time" name="hora_vuelo_salida" class="form-control mt-1 @error('hora_vuelo_salida') is-invalid @enderror">
                    @error('fecha_vuelo_salida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('hora_vuelo_salida')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
                <h5 class="border-bottom pb-2 mb-3">Datos de Salida</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha Vuelo Salida</label>
                        <input type="date" name="fecha_vuelo_salida" class="form-control @error('fecha_vuelo_salida') is-invalid @enderror"
                               value="{{ old('fecha_vuelo_salida') }}">
                        @error('fecha_vuelo_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora Vuelo Salida</label>
                        <input type="time" name="hora_vuelo_salida" class="form-control @error('hora_vuelo_salida') is-invalid @enderror"
                               value="{{ old('hora_vuelo_salida') }}">
                        @error('hora_vuelo_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora de Recogida</label>
                        <input type="time" name="hora_recogida" class="form-control @error('hora_recogida') is-invalid @enderror"
                               value="{{ old('hora_recogida') }}">
                        @error('hora_recogida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Confirmar Reserva
                </button>
                <a href="{{ route('admin.reservas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipoTrayecto');
        const camposLlegada = document.getElementById('camposLlegada');
        const camposSalida = document.getElementById('camposSalida');

        // Función para actualizar la visibilidad de los campos
        function actualizarCampos() {
            const tipo = tipoSelect.value;

            // Si es tipo 1 (Aeropuerto → Hotel) o 3 (Ida y Vuelta) => mostrar Llegada
            camposLlegada.style.display = (tipo === '1' || tipo === '3') ? 'block' : 'none';

            // Si es tipo 2 (Hotel → Aeropuerto) o 3 (Ida y Vuelta) => mostrar Salida
            camposSalida.style.display = (tipo === '2' || tipo === '3') ? 'block' : 'none';
        }

        // Asignar evento change
        tipoSelect.addEventListener('change', actualizarCampos);

        // Ejecutar al cargar la página para manejar valores preseleccionados
        actualizarCampos();
    });
</script>
@endsection
