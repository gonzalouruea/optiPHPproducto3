@extends('layouts.app')

@section('title', 'Calendario de Reservas')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Calendario de Reservas</h4>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" onclick="mostrarMes()">Mes</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="mostrarSemana()">Semana</button>
                        <button class="btn-sm btn-outline-secondary" onclick="mostrarDia()">Día</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendario-mes" class="show">
                        <div class="row">
                            @foreach($semanas as $semana)
                                <div class="col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            Semana del {{ $semana['inicio']->format('d/m/Y') }} al {{ $semana['fin']->format('d/m/Y') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Hotel</th>
                                                            <th>Tipo Reserva</th>
                                                            <th>Vehículo</th>
                                                            <th>Estado</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($semana['reservas'] as $reserva)
                                                            <tr>
                                                                <td>{{ $reserva->fecha_entrada->format('d/m/Y') }}</td>
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
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="calendario-semana" class="hide">
                        <!-- Contenido de la semana se cargará dinámicamente -->
                    </div>

                    <div id="calendario-dia" class="hide">
                        <!-- Contenido del día se cargará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar detalles -->
<div class="modal fade" id="detallesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detallesContenido"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .show { display: block; }
    .hide { display: none; }
</style>
@endpush

@push('scripts')
<script>
function mostrarMes() {
    document.getElementById('calendario-mes').classList.add('show');
    document.getElementById('calendario-mes').classList.remove('hide');
    document.getElementById('calendario-semana').classList.add('hide');
    document.getElementById('calendario-dia').classList.add('hide');
}

function mostrarSemana() {
    // Implementar carga dinámica de la semana
    alert('Funcionalidad de semana en desarrollo');
}

function mostrarDia() {
    // Implementar carga dinámica del día
    alert('Funcionalidad de día en desarrollo');
}
</script>
@endpush
