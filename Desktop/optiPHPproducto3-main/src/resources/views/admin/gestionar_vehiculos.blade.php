@extends('layouts.app')

@section('title', 'Gestionar Vehículos')

@section('content')
<div class="card shadow">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Gestionar Vehículos</h4>
        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createVehiculoModal">
            <i class="fas fa-plus"></i> Nuevo Vehículo
        </button>
    </div>
    <div class="card-body">
        @if($vehiculos->isEmpty())
            <div class="alert alert-info">No hay vehículos registrados.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculos as $vehiculo)
                            <tr>
                                <td>{{ $vehiculo->id_vehiculo }}</td>
                                <td>{{ $vehiculo->Descripción }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editVehiculoModal{{ $vehiculo->id_vehiculo }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteVehiculoModal{{ $vehiculo->id_vehiculo }}">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal de edición para cada vehículo -->
                            <div class="modal fade" id="editVehiculoModal{{ $vehiculo->id_vehiculo }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Editar Vehículo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.vehiculos.actualizar', $vehiculo->id_vehiculo) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="Descripción{{ $vehiculo->id_vehiculo }}" class="form-label">Descripción</label>
                                                    <input type="text" class="form-control" id="Descripción{{ $vehiculo->id_vehiculo }}" 
                                                           name="Descripción" value="{{ $vehiculo->Descripción }}" required>
                                                </div>
                                                <div class="mb-3">
                                                        <label for="email_conductor{{ $vehiculo->email_conductor }}" class="form-label">Email</label>
                                                        <input type="text" class="form-control" id="email_conductor{{ $vehiculo->email_conductor }}" 
                                                            name="email_conductor" value="{{ $vehiculo->email_conductor }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password{{ $vehiculo->password }}" class="form-label">Password (Dejar en blanco para no cambiar)</label>
                                                    <input type="password" class="form-control" id="password{{ $vehiculo->password }}" 
                                                            name="password" value="" >
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal de eliminación para cada vehículo -->
                            <div class="modal fade" id="deleteVehiculoModal{{ $vehiculo->id_vehiculo }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de que desea eliminar el vehículo <strong>{{ $vehiculo->Descripción }}</strong>?</p>
                                            <p class="text-danger">Esta acción no se puede deshacer y podría afectar a las reservas existentes.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.vehiculos.eliminar', $vehiculo->id_vehiculo) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal para crear nuevo vehículo -->
<div class="modal fade" id="createVehiculoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nuevo Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.vehiculos.crear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Descripción" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="Descripción" name="Descripción" required>
                        <div class="form-text">Ejemplo: Sedan, SUV, Minivan, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email_conductor" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email_conductor" name="email_conductor" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
