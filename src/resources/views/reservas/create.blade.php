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
        <form action="{{ route('reservas.store') }}" method="POST">
            @csrf

            @if(Auth::user()->esAdmin())
                <div class="row mb-3">
                    <div class="col-md-9">
                        <label class="form-label">Usuario</label>
                        <select class="form-select @error('id_viajero') is-invalid @enderror" name="id_viajero">
                            <option value="">Seleccionar usuario...</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id_viajero }}" {{ old('id_viajero') == $usuario->id_viajero ? 'selected' : '' }}>
                                    {{ $usuario->email }}
                                </option>
                            @endforeach
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
            @endif

            <!-- Tipo Trayecto -->
            <div class="mb-3">
                <label class="form-label">Tipo de Trayecto</label>
                <select class="form-select @error('tipo_trayecto') is-invalid @enderror" name="tipo_trayecto" id="tipoTrayecto" required>
                    <option value="">Elige tipo...</option>
                    @foreach($tiposReserva as $tipo)
                        <option value="{{ $tipo->id_tipo_reserva }}" {{ old('tipo_trayecto') == $tipo->id_tipo_reserva ? 'selected' : '' }}>
                            {{ $tipo->Descripción }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_trayecto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
    <label class="form-label">Tipo de trayecto</label>
    <select name="id_tipo_reserva" class="form-select" required>
        @foreach($tiposReserva as $tipo)
            <option value="{{ $tipo->id_tipo_reserva }}">{{ $tipo->Descripción }}</option>
        @endforeach
    </select>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Fecha / Hora Recogida en HOTEL</label>
        <input type="date"  name="fecha_hotel"  class="form-control">
        <input type="time"  name="hora_hotel"   class="form-control mt-1">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Fecha / Hora Vuelo</label>
        <input type="date"  name="fecha_vuelo"  class="form-control">
        <input type="time"  name="hora_vuelo"   class="form-control mt-1">
    </div>
</div>


            <div class="mb-3">
                <label class="form-label">Número de Pasajeros</label>
                <input type="number" name="num_viajeros" class="form-control @error('num_viajeros') is-invalid @enderror"
                       value="{{ old('num_viajeros') }}" required min="1">
                @error('num_viajeros')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Vehículo</label>
                <select name="id_vehiculo" class="form-select @error('id_vehiculo') is-invalid @enderror" required>
                    <option value="">Seleccionar vehículo...</option>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id_vehiculo }}" {{ old('id_vehiculo') == $vehiculo->id_vehiculo ? 'selected' : '' }}>
                            {{ $vehiculo->Descripción }} )
                        </option>
                    @endforeach
                </select>
                @error('id_vehiculo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <!-- Llegada -->
            <div id="camposLlegada" class="hidden-section mb-4">
                <h5 class="border-bottom pb-2 mb-3">Datos de Llegada</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha Llegada</label>
                        <input type="date" name="fecha_entrada" class="form-control @error('fecha_entrada') is-invalid @enderror"
                               value="{{ old('fecha_entrada') }}">
                        @error('fecha_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora Llegada</label>
                        <input type="time" name="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror"
                               value="{{ old('hora_entrada') }}">
                        @error('hora_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Número Vuelo (Llegada)</label>
                        <input type="text" name="numero_vuelo_entrada" class="form-control @error('numero_vuelo_entrada') is-invalid @enderror"
                               value="{{ old('numero_vuelo_entrada') }}">
                        @error('numero_vuelo_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aeropuerto de Origen</label>
                        <input type="text" name="origen_vuelo_entrada" class="form-control @error('origen_vuelo_entrada') is-invalid @enderror"
                               value="{{ old('origen_vuelo_entrada') }}">
                        @error('origen_vuelo_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Salida -->
            <div id="camposSalida" class="hidden-section mb-4">
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
                <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary">
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
