@extends('layouts.app')

@section('title', 'Todas las Reservas')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Todas las Reservas</h4>
                    <a href="{{ route('admin.reservas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Reserva
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($reservas->isEmpty())
                        <p>No hay reservas registradas.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Localizador</th>
                                        <th>Fecha Entrada</th>
                                        <th>Fecha Salida</th>
                                        <th>Hotel</th>
                                        <th>Tipo Reserva</th>
                                        <th>Vehículo</th>
                                        <th>Pasajeros</th>
                                        <th>Estado</th>
                                        <th>Email Cliente</th>
                                        <th>Tipo Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($reservas as $reserva)
                                        <tr>
                                            <td>{{ $reserva->localizador }}</td>
                                            <td>{{ $reserva->fecha_entrada }}</td>
                                            <td>{{ $reserva->fecha_vuelo_salida }}</td>
                                            <td>{{ $reserva->hotel ? $reserva->hotel->Usuario : 'No asignado' }}</td>
                                            <td>{{ $reserva->tipoReserva ? $reserva->tipoReserva->Descripción : 'No especificado' }}</td>
                                            <td>{{ $reserva->vehiculo ? $reserva->vehiculo->Descripción : 'No asignado' }}</td>
                                            <td>{{ $reserva->num_viajeros }}</td>
                                            <td>
                                                <span class="badge bg-success">Confirmada</span>
                                            </td>
                                            <td>{{ $reserva->email_cliente }}</td>
                                            <td>
                                                <span class="badge {{ $reserva->creado_por_admin ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $reserva->creado_por_admin ? 'Admin' : 'Usuario' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.reservas.show', ['reserva' => $reserva->id_reserva]) }}" class="btn btn-sm btn-info">Ver</a>
                                                    <a href="{{ route('admin.reservas.edit', ['reserva' => $reserva->id_reserva]) }}" class="btn btn-sm btn-warning">Editar</a>
                                                    <form action="{{ route('admin.reservas.destroy', ['reserva' => $reserva->id_reserva]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta reserva?')">Eliminar</button>
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
