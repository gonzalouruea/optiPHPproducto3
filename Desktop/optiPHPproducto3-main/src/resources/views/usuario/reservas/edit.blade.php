@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar Reserva') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('usuario.reservas.update', $reserva->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="hotel_id" class="form-label">Hotel</label>
                            <select id="hotel_id" class="form-select @error('hotel_id') is-invalid @enderror" name="hotel_id" required disabled>
                                <option value="{{ $reserva->hotel->id }}">{{ $reserva->hotel->nombre }}</option>
                            </select>
                            @error('hotel_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tipo_reserva_id" class="form-label">Tipo de Reserva</label>
                            <select id="tipo_reserva_id" class="form-select @error('tipo_reserva_id') is-invalid @enderror" name="tipo_reserva_id" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach($tiposReserva as $tipo)
                                    <option value="{{ $tipo->id }}" {{ $reserva->id_tipo_reserva == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_reserva_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_salida" class="form-label">Fecha Salida</label>
                            <input id="fecha_salida" type="date" class="form-control @error('fecha_salida') is-invalid @enderror" name="fecha_salida" value="{{ old('fecha_salida', $reserva->fecha_salida) }}" required>
                            @error('fecha_salida')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="hora_salida" class="form-label">Hora Salida</label>
                            <input id="hora_salida" type="time" class="form-control @error('hora_salida') is-invalid @enderror" name="hora_salida" value="{{ old('hora_salida', $reserva->hora_salida) }}" required>
                            @error('hora_salida')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_entrada" class="form-label">Fecha Entrada</label>
                            <input id="fecha_entrada" type="date" class="form-control @error('fecha_entrada') is-invalid @enderror" name="fecha_entrada" value="{{ old('fecha_entrada', $reserva->fecha_entrada) }}" required>
                            @error('fecha_entrada')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="hora_entrada" class="form-label">Hora Entrada</label>
                            <input id="hora_entrada" type="time" class="form-control @error('hora_entrada') is-invalid @enderror" name="hora_entrada" value="{{ old('hora_entrada', $reserva->hora_entrada) }}" required>
                            @error('hora_entrada')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="origen_vuelo_salida" class="form-label">Origen Vuelo Salida</label>
                            <input id="origen_vuelo_salida" type="text" class="form-control @error('origen_vuelo_salida') is-invalid @enderror" name="origen_vuelo_salida" value="{{ old('origen_vuelo_salida', $reserva->origen_vuelo_salida) }}" required>
                            @error('origen_vuelo_salida')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="origen_vuelo_entrada" class="form-label">Origen Vuelo Entrada</label>
                            <input id="origen_vuelo_entrada" type="text" class="form-control @error('origen_vuelo_entrada') is-invalid @enderror" name="origen_vuelo_entrada" value="{{ old('origen_vuelo_entrada', $reserva->origen_vuelo_entrada) }}" required>
                            @error('origen_vuelo_entrada')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre', $reserva->nombre) }}" required>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input id="apellidos" type="text" class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos', $reserva->apellidos) }}" required>
                            @error('apellidos')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono', $reserva->telefono) }}" required>
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pasajeros" class="form-label">Número de Pasajeros</label>
                            <input id="pasajeros" type="number" class="form-control @error('pasajeros') is-invalid @enderror" name="pasajeros" min="1" value="{{ old('pasajeros', $reserva->pasajeros) }}" required>
                            @error('pasajeros')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vehiculo_id" class="form-label">Vehículo</label>
                            <select id="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" name="vehiculo_id" required>
                                <option value="">Selecciona un vehículo</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" {{ $reserva->id_vehiculo == $vehiculo->id ? 'selected' : '' }}>{{ $vehiculo->matricula }} - {{ $vehiculo->tipo }}</option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('usuario.dashboard') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Reserva</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
