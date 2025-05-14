@extends('layouts.app')

@section('title', Auth::user()->esAdmin() ? 'Todas las Reservas' : 'Mis Reservas')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ Auth::user()->esAdmin() ? 'Todas las Reservas' : 'Mis Reservas' }}</h2>
    <a href="{{ route('reservas.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nueva Reserva
    </a>
  </div>

  @if($reservas->isEmpty())
    <div class="alert alert-info">No hay reservas para mostrar</div>
  @else
    <div class="row row-cols-1 row-cols-md-2 g-4">
    @foreach($reservas as $reserva)
    <div class="col">
    <div class="card h-100 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
      <span><strong>Localizador:</strong> {{ $reserva->localizador }}</span>
      @if($reserva->creado_por_admin)
      <span class="badge bg-warning text-dark">Creado por Admin</span>
    @endif
      </div>
      <div class="card-body">
      @if(Auth::user()->esAdmin())
      <p><strong>Usuario:</strong> {{ $reserva->email_cliente }}</p>
    @endif
      <p><strong>Tipo:</strong> {{ $reserva->tipoReserva->Descripción ?? 'Desconocido' }}</p>
      <p><strong>Hotel:</strong>
      {{ $reserva->hotel ? $reserva->hotel->descripcion : 'N/A' }}
      </p>
      <p><strong>Vehículo:</strong> {{ $reserva->vehiculo->Descripción ?? 'N/A' }}</p>
      <p><strong>Pasajeros:</strong> {{ $reserva->num_viajeros }}</p>

      @if($reserva->fecha_entrada)
      <p><strong>Llegada:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
      a las {{ \Carbon\Carbon::parse($reserva->hora_entrada)->format('H:i') }}</p>
    @endif

      @if($reserva->fecha_vuelo_salida)
      <p><strong>Salida:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_vuelo_salida)->format('d/m/Y') }}
      a las {{ \Carbon\Carbon::parse($reserva->hora_vuelo_salida)->format('H:i') }}</p>
    @endif
      </div>
      <div class="card-footer">
      <small class="text-muted">Reservado:
      {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y H:i') }}</small>
      <div class="mt-2">
      <a class="btn btn-sm btn-outline-success" href="{{ route('reservas.show', $reserva->id_reserva) }}">
      <i class="fas fa-eye"></i> Ver
      </a>

      @php
      $puedeEditar = Auth::user()->esAdmin() || $reserva->puedeSerModificadaPorUsuario();
    @endphp

      @if($puedeEditar)
      <a class="btn btn-sm btn-outline-primary" href="{{ route('reservas.edit', $reserva->id_reserva) }}">
      <i class="fas fa-edit"></i> Editar
      </a>
      <form action="{{ route('reservas.destroy', $reserva->id_reserva) }}" method="POST" class="d-inline"
      onsubmit="return confirm('¿Está seguro de que desea cancelar esta reserva?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-sm btn-outline-danger">
      <i class="fas fa-trash"></i> Cancelar
      </button>
      </form>
      @else
      <span class="badge bg-info text-white ms-2">No se puede modificar (menos de 48h)</span>
      @endif
      </div>
      </div>
    </div>
    </div>
    @endforeach
    </div>
  @endif
@endsection
