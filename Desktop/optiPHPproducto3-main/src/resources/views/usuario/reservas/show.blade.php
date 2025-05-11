@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Detalles de la Reserva') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Datos del Cliente</h5>
                            <p><strong>Nombre:</strong> {{ $reserva->nombre }} {{ $reserva->apellidos }}</p>
                            <p><strong>Teléfono:</strong> {{ $reserva->telefono }}</p>
                            <p><strong>Email:</strong> {{ $reserva->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Datos de la Reserva</h5>
                            <p><strong>Fecha Creación:</strong> {{ $reserva->fecha_creacion }}</p>
                            <p><strong>Fecha Modificación:</strong> {{ $reserva->fecha_modificacion }}</p>
                            <p><strong>Estado:</strong> <span class="badge bg-success">Confirmada</span></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Salida</h5>
                            <p><strong>Fecha:</strong> {{ $reserva->fecha_salida }}</p>
                            <p><strong>Hora:</strong> {{ $reserva->hora_salida }}</p>
                            <p><strong>Origen Vuelo:</strong> {{ $reserva->origen_vuelo_salida }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Entrada</h5>
                            <p><strong>Fecha:</strong> {{ $reserva->fecha_entrada }}</p>
                            <p><strong>Hora:</strong> {{ $reserva->hora_entrada }}</p>
                            <p><strong>Origen Vuelo:</strong> {{ $reserva->origen_vuelo_entrada }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Hotel</h5>
                            <p><strong>Nombre:</strong> {{ $reserva->hotel->nombre }}</p>
                            <p><strong>Dirección:</strong> {{ $reserva->hotel->direccion }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Vehículo</h5>
                            <p><strong>Matrícula:</strong> {{ $reserva->vehiculo->matricula }}</p>
                            <p><strong>Tipo:</strong> {{ $reserva->vehiculo->tipo }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Tipo de Reserva</h5>
                            <p><strong>Nombre:</strong> {{ $reserva->tipoReserva->nombre }}</p>
                            <p><strong>Descripción:</strong> {{ $reserva->tipoReserva->descripcion }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Información Adicional</h5>
                            <p><strong>Pasajeros:</strong> {{ $reserva->pasajeros }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('usuario.dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
