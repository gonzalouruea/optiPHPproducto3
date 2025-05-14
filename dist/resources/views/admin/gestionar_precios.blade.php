@extends('layouts.app')

@section('title', 'Gestionar Precios')

@section('content')
<div class="card shadow">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Gestionar Precios</h4>
        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createPrecioModal">
            <i class="fas fa-plus"></i> Nuevo Precio
        </button>
    </div>
    <div class="card-body">
        @if($precios->isEmpty())
            <div class="alert alert-info">No hay precios registrados.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Zona</th>
                            <th>Vehículo</th>
                            <th>Tipo de Reserva</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($precios as $precio)
                            <tr>
                                <td>{{ $precio->id_precio }}</td>
                                <td>{{ $precio->zona->descripcion ?? 'Sin zona' }}</td>
                                <td>{{ $precio->vehiculo->Descripción ?? 'Sin vehículo' }}</td>
                                <td>{{ $precio->tipoReserva->Descripción ?? 'Sin tipo' }}</td>
                                <td>{{ number_format($precio->precio, 2) }} €</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editPrecioModal{{ $precio->id_precio }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deletePrecioModal{{ $precio->id_precio }}">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal de edición para cada precio -->
                            <div class="modal fade" id="editPrecioModal{{ $precio->id_precio }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-secondary text-white">
                                            <h5 class="modal-title">Editar Precio</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.precios.actualizar', $precio->id_precio) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="id_zona{{ $precio->id_precio }}" class="form-label">Zona</label>
                                                    <select class="form-select" id="id_zona{{ $precio->id_precio }}" name="id_zona" required>
                                                        @foreach($zonas as $zona)
                                                            <option value="{{ $zona->id_zona }}" 
                                                                {{ $precio->id_zona == $zona->id_zona ? 'selected' : '' }}>
                                                                {{ $zona->descripcion }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="id_vehiculo{{ $precio->id_precio }}" class="form-label">Vehículo</label>
                                                    <select class="form-select" id="id_vehiculo{{ $precio->id_precio }}" name="id_vehiculo" required>
                                                        @foreach($vehiculos as $vehiculo)
                                                            <option value="{{ $vehiculo->id_vehiculo }}" 
                                                                {{ $precio->id_vehiculo == $vehiculo->id_vehiculo ? 'selected' : '' }}>
                                                                {{ $vehiculo->Descripción }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="id_tipo_reserva{{ $precio->id_precio }}" class="form-label">Tipo de Reserva</label>
                                                    <select class="form-select" id="id_tipo_reserva{{ $precio->id_precio }}" name="id_tipo_reserva" required>
                                                        @foreach($tiposReserva as $tipo)
                                                            <option value="{{ $tipo->id_tipo_reserva }}" 
                                                                {{ $precio->id_tipo_reserva == $tipo->id_tipo_reserva ? 'selected' : '' }}>
                                                                {{ $tipo->Descripción }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="precio{{ $precio->id_precio }}" class="form-label">Precio (€)</label>
                                                    <input type="number" step="0.01" min="0" class="form-control" id="precio{{ $precio->id_precio }}" 
                                                           name="precio" value="{{ $precio->precio }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal de eliminación para cada precio -->
                            <div class="modal fade" id="deletePrecioModal{{ $precio->id_precio }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de que desea eliminar este precio?</p>
                                            <ul>
                                                <li><strong>Zona:</strong> {{ $precio->zona->descripcion ?? 'Sin zona' }}</li>
                                                <li><strong>Vehículo:</strong> {{ $precio->vehiculo->Descripción ?? 'Sin vehículo' }}</li>
                                                <li><strong>Tipo de Reserva:</strong> {{ $precio->tipoReserva->Descripción ?? 'Sin tipo' }}</li>
                                                <li><strong>Precio:</strong> {{ number_format($precio->precio, 2) }} €</li>
                                            </ul>
                                            <p class="text-danger">Esta acción no se puede deshacer y podría afectar a las reservas futuras.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.precios.eliminar', $precio->id_precio) }}" method="POST">
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

<!-- Modal para crear nuevo precio -->
<div class="modal fade" id="createPrecioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Nuevo Precio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.precios.crear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_zona" class="form-label">Zona</label>
                        <select class="form-select" id="id_zona" name="id_zona" required>
                            <option value="">Seleccionar zona...</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona->id_zona }}">{{ $zona->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_vehiculo" class="form-label">Vehículo</label>
                        <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                            <option value="">Seleccionar vehículo...</option>
                            @foreach($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->id_vehiculo }}">{{ $vehiculo->Descripción }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_tipo_reserva" class="form-label">Tipo de Reserva</label>
                        <select class="form-select" id="id_tipo_reserva" name="id_tipo_reserva" required>
                            <option value="">Seleccionar tipo de reserva...</option>
                            @foreach($tiposReserva as $tipo)
                                <option value="{{ $tipo->id_tipo_reserva }}">{{ $tipo->Descripción }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio (€)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Precio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
