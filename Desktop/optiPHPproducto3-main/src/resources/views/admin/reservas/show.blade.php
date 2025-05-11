@extends('layouts.app')

@section('title', 'Detalles de Reserva')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles de Reserva</h4>
                    <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Información General</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Localizador:</strong> {{ $reserva->localizador }}</p>
                                            <p><strong>Email Cliente:</strong> {{ $reserva->email_cliente }}</p>
                                            <p><strong>Fecha Reserva:</strong> {{ $reserva->fecha_reserva }}</p>
                                            <p><strong>Fecha Modificación:</strong> {{ $reserva->fecha_modificacion }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Estado:</strong> <span class="badge bg-success">Confirmada</span></p>
                                            <p><strong>Tipo Usuario:</strong> <span class="badge {{ $reserva->creado_por_admin ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $reserva->creado_por_admin ? 'Admin' : 'Usuario' }}</span></p>
                                            <p><strong>Número Pasajeros:</strong> {{ $reserva->num_viajeros }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Viaje -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Información de Viaje</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Fecha Entrada:</strong> {{ $reserva->fecha_entrada }}</p>
                                            <p><strong>Hora Entrada:</strong> {{ $reserva->hora_entrada }}</p>
                                            <p><strong>Número Vuelo Entrada:</strong> {{ $reserva->numero_vuelo_entrada }}</p>
                                            <p><strong>Origen Vuelo:</strong> {{ $reserva->origen_vuelo_entrada }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Fecha Vuelo Salida:</strong> {{ $reserva->fecha_vuelo_salida }}</p>
                                            <p><strong>Hora Vuelo Salida:</strong> {{ $reserva->hora_vuelo_salida }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información de Servicio -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Información de Servicio</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Hotel:</strong> {{ $reserva->hotel ? $reserva->hotel->Usuario : 'No asignado' }}</p>
                                            <p><strong>Tipo Reserva:</strong> {{ $reserva->tipoReserva ? $reserva->tipoReserva->Descripción : 'No especificado' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Vehículo:</strong> {{ $reserva->vehiculo ? $reserva->vehiculo->Descripción : 'No asignado' }}</p>
                                            <p><strong>Hora Recogida:</strong> {{ $reserva->hora_recogida }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Precio -->
                        <div class="col-md-6">
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Información de Precio</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Precio Total:</strong> {{ $reserva->precio }} €</p>
                                            <p><strong>Comisión Hotel:</strong> {{ $reserva->comision_hotel }} €</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Viajero -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Información del Viajero</h5>
                            <p><strong>Email:</strong> {{ $reserva->viajero ? $reserva->viajero->email : 'No asignado' }}</p>
                            <p><strong>Nombre:</strong> {{ $reserva->viajero ? $reserva->viajero->nombre : '' }}</p>
                            <p><strong>Apellidos:</strong> {{ $reserva->viajero ? $reserva->viajero->apellido1 . ' ' . $reserva->viajero->apellido2 : '' }}</p>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Acciones</h5>
                            <div class="btn-group">
                                <a href="{{ route('admin.reservas.edit', ['reserva' => $reserva->id_reserva]) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('admin.reservas.destroy', ['reserva' => $reserva->id_reserva]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta reserva?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
