@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Mis Reservas') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Lista de Reservas</h4>
                        <a href="{{ route('usuario.reservas.create') }}" class="btn btn-primary">Nueva Reserva</a>
                    </div>

                    @if($reservas->isEmpty())
                        <p>No tienes reservas realizadas.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha Entrada</th>
                                        <th>Fecha Salida</th>
                                        <th>Hotel</th>
                                        <th>Tipo Reserva</th>
                                        <th>Vehículo</th>
                                        <th>Pasajeros</th>
                                        <th>Estado</th>
                                        <th>Tipo Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($reservas as $reserva)
                                        <tr>
                                            <td>{{ $reserva->fecha_entrada }}</td>
                                            <td>{{ $reserva->fecha_vuelo_salida }}</td>
                                            <td>{{ $reserva->hotel->nombre }}</td>
                                            <td>{{ $reserva->tipoReserva->descripcion }}</td>
                                            <td>{{ $reserva->vehiculo->descripcion }}</td>
                                            <td>{{ $reserva->num_viajeros }}</td>
                                            <td>
                                                <span class="badge bg-success">Confirmada</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $reserva->creado_por_admin ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $reserva->creado_por_admin ? 'Admin' : 'Usuario' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('usuario.reservas.show', ['reserva' => $reserva->id_reserva]) }}" class="btn btn-sm btn-info">Ver</a>
                                                    <a href="{{ route('usuario.reservas.edit', ['reserva' => $reserva->id_reserva]) }}" class="btn btn-sm btn-warning">Editar</a>
                                                    <form action="{{ route('usuario.reservas.destroy', ['reserva' => $reserva->id_reserva]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">Cancelar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
