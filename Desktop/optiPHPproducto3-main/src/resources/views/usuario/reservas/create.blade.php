@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Nueva Reserva</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('usuario.reservas.store') }}">
                        @csrf

                        <!-- Tipo de Trayecto -->
                        <div class="mb-3">
                            <label for="tipo_reserva_id" class="form-label">Tipo de Trayecto</label>
                            <select id="tipo_reserva_id" class="form-select @error('tipo_reserva_id') is-invalid @enderror" name="tipo_reserva_id" required>
                                <option value="">Elige tipo...</option>
                                @foreach($tipos_reserva as $tipo)
                                    <option value="{{ $tipo->id_tipo_reserva }}">{{ $tipo->Descripción }}</option>
                                @endforeach
                            </select>
                            @error('tipo_reserva_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Campos para Aeropuerto → Hotel -->
                        <div id="camposAeropuertoHotel" style="display: none;">
                            <div class="mb-3">
                                <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                                <input type="number" class="form-control" id="pasajeros" name="pasajeros" min="1" max="8" required>
                            </div>

                            <div class="mb-3">
                                <label for="vehiculo_id" class="form-label">Vehículo</label>
                                <select class="form-select" id="vehiculo_id" name="vehiculo_id" required>
                                    <option value="">Seleccione un vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculo }}">{{ $vehiculo->Descripción }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Hotel (destino/recogida)</label>
                                <select class="form-select" id="hotel_id" name="hotel_id" required>
                                    <option value="">Seleccione un hotel</option>
                                    @foreach($hoteles as $hotel)
                                        <option value="{{ $hotel->id_hotel }}">{{ $hotel->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                                    <input type="date" class="form-control" id="fecha_llegada" name="fecha_llegada" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hora_llegada" class="form-label">Hora Llegada</label>
                                    <input type="time" class="form-control" id="hora_llegada" name="hora_llegada" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="numero_vuelo_llegada" class="form-label">Número Vuelo (Llegada)</label>
                                <input type="text" class="form-control" id="numero_vuelo_llegada" name="numero_vuelo_llegada" required>
                            </div>

                            <div class="mb-3">
                                <label for="origen_vuelo" class="form-label">Aeropuerto de Origen</label>
                                <input type="text" class="form-control" id="origen_vuelo" name="origen_vuelo" required>
                            </div>
                        </div>

                        <!-- Campos para Hotel → Aeropuerto -->
                        <div id="camposHotelAeropuerto" style="display: none;">
                            <div class="mb-3">
                                <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                                <input type="number" class="form-control" id="pasajeros" name="pasajeros" min="1" max="8" required>
                            </div>

                            <div class="mb-3">
                                <label for="vehiculo_id" class="form-label">Vehículo</label>
                                <select class="form-select" id="vehiculo_id" name="vehiculo_id" required>
                                    <option value="">Seleccione un vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculo }}">{{ $vehiculo->Descripción }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Hotel (origen/recogida)</label>
                                <select class="form-select" id="hotel_id" name="hotel_id" required>
                                    <option value="">Seleccione un hotel</option>
                                    @foreach($hoteles as $hotel)
                                        <option value="{{ $hotel->id_hotel }}">{{ $hotel->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_salida" class="form-label">Fecha Salida</label>
                                    <input type="date" class="form-control" id="fecha_salida" name="fecha_salida" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hora_salida" class="form-label">Hora Salida</label>
                                    <input type="time" class="form-control" id="hora_salida" name="hora_salida" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="numero_vuelo_salida" class="form-label">Número Vuelo (Salida)</label>
                                <input type="text" class="form-control" id="numero_vuelo_salida" name="numero_vuelo_salida" required>
                            </div>

                            <div class="mb-3">
                                <label for="destino_vuelo" class="form-label">Aeropuerto de Destino</label>
                                <input type="text" class="form-control" id="destino_vuelo" name="destino_vuelo" required>
                            </div>
                        </div>

                        <!-- Campos para Ida y Vuelta -->
                        <div id="camposIdaVuelta" style="display: none;">
                            <div class="mb-3">
                                <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                                <input type="number" class="form-control" id="pasajeros" name="pasajeros" min="1" max="8" required>
                            </div>

                            <div class="mb-3">
                                <label for="vehiculo_id" class="form-label">Vehículo</label>
                                <select class="form-select" id="vehiculo_id" name="vehiculo_id" required>
                                    <option value="">Seleccione un vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculo }}">{{ $vehiculo->Descripción }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Hotel (origen/recogida)</label>
                                <select class="form-select" id="hotel_id" name="hotel_id" required>
                                    <option value="">Seleccione un hotel</option>
                                    @foreach($hoteles as $hotel)
                                        <option value="{{ $hotel->id_hotel }}">{{ $hotel->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_salida" class="form-label">Fecha Salida</label>
                                    <input type="date" class="form-control" id="fecha_salida" name="fecha_salida" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hora_salida" class="form-label">Hora Salida</label>
                                    <input type="time" class="form-control" id="hora_salida" name="hora_salida" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="numero_vuelo_salida" class="form-label">Número Vuelo (Salida)</label>
                                <input type="text" class="form-control" id="numero_vuelo_salida" name="numero_vuelo_salida" required>
                            </div>

                            <div class="mb-3">
                                <label for="destino_vuelo" class="form-label">Aeropuerto de Destino</label>
                                <input type="text" class="form-control" id="destino_vuelo" name="destino_vuelo" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                                    <input type="date" class="form-control" id="fecha_llegada" name="fecha_llegada" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hora_llegada" class="form-label">Hora Llegada</label>
                                    <input type="time" class="form-control" id="hora_llegada" name="hora_llegada" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="numero_vuelo_llegada" class="form-label">Número Vuelo (Llegada)</label>
                                <input type="text" class="form-control" id="numero_vuelo_llegada" name="numero_vuelo_llegada" required>
                            </div>

                            <div class="mb-3">
                                <label for="origen_vuelo" class="form-label">Aeropuerto de Origen</label>
                                <input type="text" class="form-control" id="origen_vuelo" name="origen_vuelo" required>
                            </div>
                        </div>

                            <div class="mb-3">
                                <label for="vehiculo_id" class="form-label">Vehículo</label>
                                <select class="form-select" id="vehiculo_id" name="vehiculo_id" required>
                                    <option value="">Seleccione un vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculo }}">{{ $vehiculo->Descripción }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="hotel_id" class="form-label">Hotel (destino/recogida)</label>
                                <select class="form-select" id="hotel_id" name="hotel_id" required>
                                    <option value="">Seleccione un hotel</option>
                                    @foreach($hoteles as $hotel)
                                        <option value="{{ $hotel->id_hotel }}">{{ $hotel->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                                    <input type="date" class="form-control" id="fecha_llegada" name="fecha_llegada" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hora_llegada" class="form-label">Hora Llegada</label>
                                    <input type="time" class="form-control" id="hora_llegada" name="hora_llegada" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="numero_vuelo_llegada" class="form-label">Número Vuelo (Llegada)</label>
                                <input type="text" class="form-control" id="numero_vuelo_llegada" name="numero_vuelo_llegada" required>
                            </div>

                            <div class="mb-3">
                                <label for="origen_vuelo" class="form-label">Aeropuerto de Origen</label>
                                <input type="text" class="form-control" id="origen_vuelo" name="origen_vuelo" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('usuario.dashboard') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Reserva</button>
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
    // Obtener referencias a los elementos del DOM
    const tipoReservaSelect = document.getElementById('tipo_reserva_id');
    const camposAeropuertoHotel = document.getElementById('camposAeropuertoHotel');
    const camposHotelAeropuerto = document.getElementById('camposHotelAeropuerto');
    const camposIdaVuelta = document.getElementById('camposIdaVuelta');
    const fechaLlegada = document.getElementById('fecha_llegada');
    const fechaSalida = document.getElementById('fecha_vuelo_salida');
    const errorSalida = document.createElement('div');
    errorSalida.className = 'invalid-feedback';
    errorSalida.style.display = 'none';

    // Ocultar todos los campos al inicio
    camposSalida.style.display = 'none';
    camposEntrada.style.display = 'none';
    camposVehiculo.style.display = 'none';
    camposHotel.style.display = 'none';

    tipoReservaSelect.addEventListener('change', function() {
        const tipo = this.value;
        
        // Ocultar todos los campos
        camposAeropuertoHotel.style.display = 'none';
        camposHotelAeropuerto.style.display = 'none';
        camposIdaVuelta.style.display = 'none';
        
        // Mostrar campos según el tipo de trayecto
        switch(tipo) {
            case '1': // Aeropuerto → Hotel
                camposAeropuertoHotel.style.display = 'block';
                break;
            case '2': // Hotel → Aeropuerto
                camposHotelAeropuerto.style.display = 'block';
                break;
            case '3': // Ida y Vuelta
                camposIdaVuelta.style.display = 'block';
                break;
            default:
                // Todos los campos ya están ocultos por defecto
                break;
        }

        // Hacer campos requeridos según el tipo de trayecto
        if (tipo !== '1') {
            camposAeropuertoHotel.querySelectorAll('input, select').forEach(input => {
                input.required = false;
            });
        }
    });

    // Validación de fecha de salida
    if (fechaEntrada && fechaSalida) {
        fechaEntrada.addEventListener('change', function() {
            const fechaEntradaValue = new Date(this.value);
            const fechaSalidaValue = new Date(fechaSalida.value);
            
            if (fechaSalidaValue <= fechaEntradaValue) {
                errorSalida.textContent = 'La fecha de salida debe ser posterior a la fecha de entrada';
                errorSalida.style.display = 'block';
                fechaSalida.classList.add('is-invalid');
            } else {
                errorSalida.style.display = 'none';
                fechaSalida.classList.remove('is-invalid');
            }
        });

        fechaSalida.addEventListener('change', function() {
            const fechaEntradaValue = new Date(fechaEntrada.value);
            const fechaSalidaValue = new Date(this.value);
            
            if (fechaSalidaValue <= fechaEntradaValue) {
                errorSalida.textContent = 'La fecha de salida debe ser posterior a la fecha de entrada';
                errorSalida.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                errorSalida.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });

        // Insertar el mensaje de error después del input de fecha de salida
        fechaSalida.parentNode.insertBefore(errorSalida, fechaSalida.nextSibling);
    }
});
</script>
@endsection
