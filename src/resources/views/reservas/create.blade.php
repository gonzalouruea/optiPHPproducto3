{{-- resources/views/reservas/create.blade.php --}}
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
{{-- Sólo para usuarios normales --}}
@unless(Auth::user()->rol === 'corporativo' || Auth::user()->esAdmin())
  <div class="mb-3">
    <label for="id_hotel" class="form-label">Hotel</label>
    <select name="id_hotel" id="id_hotel"
            class="form-select @error('id_hotel') is-invalid @enderror"
            required>
      <option value="">Selecciona hotel…</option>
      @foreach($hoteles as $hotel)
  <option value="{{ $hotel->id_hotel }}"
    {{ old('id_hotel') == $hotel->id_hotel ? 'selected' : '' }}>
    {{ $hotel->descripcion }}
  </option>
@endforeach

    </select>
    @error('id_hotel')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
@endunless

            <!-- Tipo de Trayecto -->
            <div class="mb-3">
                <label for="id_tipo_reserva" class="form-label">Tipo de Trayecto</label>
                <select name="id_tipo_reserva" id="id_tipo_reserva"
                        class="form-select @error('id_tipo_reserva') is-invalid @enderror"
                        required>
                    <option value="">Elige tipo…</option>
                    @foreach($tiposReserva as $tipo)
                        <option value="{{ $tipo->id_tipo_reserva }}"
                            {{ old('id_tipo_reserva') == $tipo->id_tipo_reserva ? 'selected' : '' }}>
                            {{ $tipo->Descripción }}
                        </option>
                    @endforeach
                </select>
                @error('id_tipo_reserva')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Número de Pasajeros -->
            <div class="mb-3">
                <label class="form-label">Número de Pasajeros</label>
                <input type="number" name="num_viajeros"
                       class="form-control @error('num_viajeros') is-invalid @enderror"
                       value="{{ old('num_viajeros') }}" required min="1">
                @error('num_viajeros')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Vehículo -->
            <div class="mb-3">
                <label class="form-label">Vehículo</label>
                <select name="id_vehiculo" id="id_vehiculo"
                        class="form-select @error('id_vehiculo') is-invalid @enderror"
                        required>
                    <option value="">Seleccionar vehículo…</option>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id_vehiculo }}"
                            {{ old('id_vehiculo') == $vehiculo->id_vehiculo ? 'selected' : '' }}>
                            {{ $vehiculo->Descripción }}
                        </option>
                    @endforeach
                </select>
                @error('id_vehiculo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Campos de Llegada -->
            <div id="camposLlegada" class="hidden-section mb-4">
                <h5 class="border-bottom pb-2 mb-3">Datos de Llegada</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha Llegada</label>
                        <input type="date" name="fecha_entrada"
                               class="form-control @error('fecha_entrada') is-invalid @enderror"
                               value="{{ old('fecha_entrada') }}">
                        @error('fecha_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora Llegada</label>
                        <input type="time" name="hora_entrada"
                               class="form-control @error('hora_entrada') is-invalid @enderror"
                               value="{{ old('hora_entrada') }}">
                        @error('hora_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Número Vuelo (Llegada)</label>
                        <input type="text" name="numero_vuelo_entrada"
                               class="form-control @error('numero_vuelo_entrada') is-invalid @enderror"
                               value="{{ old('numero_vuelo_entrada') }}">
                        @error('numero_vuelo_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aeropuerto de Origen</label>
                        <input type="text" name="origen_vuelo_entrada"
                               class="form-control @error('origen_vuelo_entrada') is-invalid @enderror"
                               value="{{ old('origen_vuelo_entrada') }}">
                        @error('origen_vuelo_entrada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Campos de Salida -->
            <div id="camposSalida" class="hidden-section mb-4">
                <h5 class="border-bottom pb-2 mb-3">Datos de Salida</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha Vuelo Salida</label>
                        <input type="date" name="fecha_vuelo_salida"
                               class="form-control @error('fecha_vuelo_salida') is-invalid @enderror"
                               value="{{ old('fecha_vuelo_salida') }}">
                        @error('fecha_vuelo_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora Vuelo Salida</label>
                        <input type="time" name="hora_vuelo_salida"
                               class="form-control @error('hora_vuelo_salida') is-invalid @enderror"
                               value="{{ old('hora_vuelo_salida') }}">
                        @error('hora_vuelo_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hora de Recogida</label>
                        <input type="time" name="hora_recogida"
                               class="form-control @error('hora_recogida') is-invalid @enderror"
                               value="{{ old('hora_recogida') }}">
                        @error('hora_recogida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
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
@php
  // Buscamos los 3 tipos por descripción
  $idLlegada  = $tiposReserva->firstWhere('Descripción','Aeropuerto → Hotel')->id_tipo_reserva;
  $idSalida   = $tiposReserva->firstWhere('Descripción','Hotel → Aeropuerto')->id_tipo_reserva;
  $idIdaVta   = $tiposReserva->firstWhere('Descripción','Ida y Vuelta')->id_tipo_reserva;
@endphp

@section('scripts')
<script>
  // IDs inyectados desde Blade
  const ID_LLEGADA  = "{{ $idLlegada }}";
  const ID_SALIDA   = "{{ $idSalida }}";
  const ID_IDAVTA   = "{{ $idIdaVta }}";

  const tipoSelect    = document.getElementById('id_tipo_reserva');
  const camposLlegada = document.getElementById('camposLlegada');
  const camposSalida  = document.getElementById('camposSalida');

  function actualizarCampos() {
    const t = tipoSelect.value;
    camposLlegada.style.display = (t === ID_LLEGADA  || t === ID_IDAVTA) ? 'block' : 'none';
    camposSalida .style.display = (t === ID_SALIDA   || t === ID_IDAVTA) ? 'block' : 'none';
  }

  // debug opcional
  console.log('TipoReserva seleccionado:', tipoSelect.value, '— IDs:', ID_LLEGADA, ID_SALIDA, ID_IDAVTA);

  tipoSelect.addEventListener('change', actualizarCampos);
  actualizarCampos();
</script>
@endsection
