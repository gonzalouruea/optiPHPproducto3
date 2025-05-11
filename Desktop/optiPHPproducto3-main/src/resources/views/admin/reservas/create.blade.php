@extends('layouts.app')

@section('title', 'Nueva Reserva')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Nueva Reserva</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reservas.store') }}" method="POST">
                        @csrf
                        <!-- Selección de Viajero -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Viajero</label>
                                <select class="form-select @error('viajero') is-invalid @enderror" name="viajero" required>
                                    <option value="">Selecciona un viajero...</option>
                                    @foreach($viajeros as $viajero)
                                        <option value="{{ $viajero['id'] }}" {{ old('viajero') == $viajero['id'] ? 'selected' : '' }}>
                                            {{ $viajero['email'] }} - {{ $viajero['nombre_completo'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('viajero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="{{ route('admin.usuarios.crear') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i> Nuevo Viajero
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Número de Pasajeros</label>
                                <input type="number" class="form-control @error('num_viajeros') is-invalid @enderror" name="num_viajeros" value="{{ old('num_viajeros', 1) }}" min="1" required>
                                @error('num_viajeros')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tipo de Trayecto -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Trayecto</label>
                                <select class="form-select @error('tipoReserva') is-invalid @enderror" name="tipoReserva" id="tipoTrayecto" required>
                                    <option value="">Selecciona el tipo de trayecto...</option>
                                    @foreach($tiposReserva as $tipo)
                                        <option value="{{ $tipo['id_tipo_reserva'] }}" {{ old('tipoReserva') == $tipo['id_tipo_reserva'] ? 'selected' : '' }}>
                                            {{ $tipo['descripcion'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipoReserva')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Campos de Ida -->
                        <div id="camposIda" class="@if(old('tipoReserva') == 2) hidden-section @endif">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Hotel</label>
                                    <select class="form-select @error('id_hotel') is-invalid @enderror" name="id_hotel" required>
                                        <option value="">Selecciona un hotel...</option>
                                        @foreach($hoteles as $hotel)
                                            <option value="{{ $hotel['id_hotel'] }}" {{ old('id_hotel') == $hotel['id_hotel'] ? 'selected' : '' }}>
                                                {{ $hotel['usuario'] }} (Zona: {{ $hotel['zona'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_hotel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Vehículo</label>
                                    <select class="form-select @error('vehiculo') is-invalid @enderror" name="vehiculo" required>
                                        <option value="">Selecciona un vehículo...</option>
                                        @foreach($vehiculos as $vehiculo)
                                            <option value="{{ $vehiculo['id_vehiculo'] }}" {{ old('vehiculo') == $vehiculo['id_vehiculo'] ? 'selected' : '' }}>
                                                {{ $vehiculo['descripcion'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehiculo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Origen Vuelo Entrada</label>
                                    <input type="text" class="form-control @error('origen_vuelo_entrada') is-invalid @enderror" name="origen_vuelo_entrada" value="{{ old('origen_vuelo_entrada') }}" required>
                                    @error('origen_vuelo_entrada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Entrada</label>
                                    <input type="date" name="fecha_entrada" class="form-control @error('fecha_entrada') is-invalid @enderror" required>
                                    @error('fecha_entrada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hora Entrada</label>
                                    <input type="time" name="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror" required>
                                    @error('hora_entrada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Número Vuelo Entrada</label>
                                    <input type="text" name="numero_vuelo_entrada" class="form-control @error('numero_vuelo_entrada') is-invalid @enderror" required>
                                    @error('numero_vuelo_entrada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Campos de Vuelta -->
                        <div id="camposVuelta" class="@if(old('tipoReserva') != 3) hidden-section @endif">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Vuelo Salida</label>
                                    <input type="date" name="fecha_vuelo_salida" class="form-control @error('fecha_vuelo_salida') is-invalid @enderror" required>
                                    @error('fecha_vuelo_salida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hora Vuelo Salida</label>
                                    <input type="time" name="hora_vuelo_salida" class="form-control @error('hora_vuelo_salida') is-invalid @enderror" required>
                                    @error('hora_vuelo_salida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hora Recogida</label>
                                    <input type="time" name="hora_recogida" class="form-control @error('hora_recogida') is-invalid @enderror" required>
                                    @error('hora_recogida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Crear Reserva</button>
                                <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoTrayecto = document.getElementById('tipoTrayecto');
    const camposIda = document.getElementById('camposIda');
    const camposVuelta = document.getElementById('camposVuelta');

    tipoTrayecto.addEventListener('change', function() {
        const trayecto = this.options[this.selectedIndex].dataset.trayecto;
        
        if (trayecto === 'ida') {
            camposIda.classList.remove('hidden-section');
            camposVuelta.classList.add('hidden-section');
        } else if (trayecto === 'vuelta') {
            camposIda.classList.add('hidden-section');
            camposVuelta.classList.remove('hidden-section');
        } else if (trayecto === 'ambos') {
            camposIda.classList.remove('hidden-section');
            camposVuelta.classList.remove('hidden-section');
        } else {
            camposIda.classList.add('hidden-section');
            camposVuelta.classList.add('hidden-section');
        }
    });
});
</script>
@endsection
