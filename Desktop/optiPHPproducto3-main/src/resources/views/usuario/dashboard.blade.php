@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Mi Panel de Usuario') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h4>Mis Datos Personales</h4>
                    <div class="card mb-4">
                        <div class="card-body">
                            <p><strong>Nombre:</strong> {{ $usuario->name }}</p>
                            <p><strong>Email:</strong> {{ $usuario->email }}</p>
                            <a href="{{ route('usuario.editar-perfil') }}" class="btn btn-primary">Editar Perfil</a>
                        </div>
                    </div>

                    <h4>Mis Reservas</h4>
                    @if($reservas->isEmpty())
                        <p>No tienes reservas realizadas.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha Salida</th>
                                        <th>Fecha Entrada</th>
                                        <th>Hotel</th>
                                        <th>Tipo Reserva</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservas as $reserva)
                                        <tr>
                                            <td>{{ $reserva->fecha_salida }}</td>
                                            <td>{{ $reserva->fecha_entrada }}</td>
                                            <td>{{ $reserva->hotel->nombre }}</td>
                                            <td>{{ $reserva->tipoReserva->nombre }}</td>
                                            <td>
                                                @if($reserva->editable)
                                                    <span class="badge bg-success">Editable</span>
                                                @else
                                                    <span class="badge bg-warning">No editable</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($reserva->editable)
                                                    <a href="{{ route('usuario.editar-reserva', $reserva->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                                    <form action="{{ route('usuario.cancelar-reserva', $reserva->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">Cancelar</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('usuario.reservas.create') }}" class="btn btn-success">Crear Nueva Reserva</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
