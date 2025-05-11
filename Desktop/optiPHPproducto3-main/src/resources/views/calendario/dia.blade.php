@extends('layouts.app')

@section('title', 'Día de Reservas')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ $fecha->format('l, d/m/Y') }}</h4>
                    <a href="{{ route('admin.calendario.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Mes
                    </a>
                </div>
                <div class="card-body">
                    @if($reservas->isEmpty())
                        <p>No hay reservas para este día.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Hotel</th>
                                        <th>Tipo Reserva</th>
                                        <th>Vehículo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservas as $reserva)
                                        <tr>
                                            <td>{{ $reserva->hotel ? $reserva->hotel->Usuario : 'No asignado' }}</td>
                                            <td>{{ $reserva->tipoReserva ? $reserva->tipoReserva->Descripción : 'No especificado' }}</td>
                                            <td>{{ $reserva->vehiculo ? $reserva->vehiculo->Descripción : 'No asignado' }}</td>
                                            <td><span class="badge bg-success">Confirmada</span></td>
                                            <td>
                                                <a href="{{ route('admin.reservas.show', $reserva->id_reserva) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
