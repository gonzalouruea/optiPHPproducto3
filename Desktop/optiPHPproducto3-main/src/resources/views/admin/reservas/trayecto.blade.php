@extends('layouts.admin')

@section('title', 'Detalles del Trayecto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detalles del Trayecto</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Datos del Viaje</h5>
                            <div class="mb-3">
                                <strong>Localizador:</strong> {{ $reserva->localizador }}
                            </div>
                            <div class="mb-3">
                                <strong>Fecha Entrada:</strong> {{ $reserva->fecha_entrada }}
                            </div>
                            <div class="mb-3">
                                <strong>Hora Entrada:</strong> {{ $reserva->hora_entrada }}
                            </div>
                            <div class="mb-3">
                                <strong>Tipo de Reserva:</strong> {{ $reserva->tipoReserva->Descripción }}
                            </div>
                            <div class="mb-3">
                                <strong>Nº Pasajeros:</strong> {{ $reserva->num_viajeros }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Datos del Cliente</h5>
                            <div class="mb-3">
                                <strong>Email Cliente:</strong> {{ $reserva->email_cliente }}
                            </div>
                            <div class="mb-3">
                                <strong>Hotel:</strong> {{ $reserva->hotel->Usuario }}
                            </div>
                            <div class="mb-3">
                                <strong>Vehículo:</strong> {{ $reserva->vehiculo->Descripción }}
                            </div>
                            <div class="mb-3">
                                <strong>Viajero Asignado:</strong> {{ $reserva->viajero ? $reserva->viajero->email : 'No asignado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
