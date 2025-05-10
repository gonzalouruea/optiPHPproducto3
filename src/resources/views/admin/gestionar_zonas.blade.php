@extends('layouts.app')

@section('title', 'Gestionar Zonas')

@section('content')
<div class="card shadow">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Gestionar Zonas</h4>
        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createZonaModal">
            <i class="fas fa-plus"></i> Nueva Zona
        </button>
    </div>
    <div class="card-body">
        @if($zonas->isEmpty())
            <div class="alert alert-info">No hay zonas registradas.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Hoteles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($zonas as $zona)
                            <tr>
                                <td>{{ $zona->id_zona }}</td>
                                <td>{{ $zona->descripcion }}</td>
                                <td>{{ $zona->hoteles->count() }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editZonaModal{{ $zona->id_zona }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger {{ $zona->hoteles->count() > 0 ? 'disabled' : '' }}" 
                                            data-bs-toggle="modal" data-bs-target="#deleteZonaModal{{ $zona->id_zona }}"
                                            {{ $zona->hoteles->count() > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal de edición para cada zona -->
                            <div class="modal fade" id="editZonaModal{{ $zona->id_zona }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Editar Zona</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.zonas.actualizar', $zona->id_zona) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="descripcion{{ $zona->id_zona }}" class="form-label">Descripción</label>
                                                    <input type="text" class="form-control" id="descripcion{{ $zona->id_zona }}" 
                                                           name="descripcion" value="{{ $zona->descripcion }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal de eliminación para cada zona -->
                            <div class="modal fade" id="deleteZonaModal{{ $zona->id_zona }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($zona->hoteles->count() > 0)
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> No se puede eliminar esta zona porque tiene {{ $zona->hoteles->count() }} hoteles asociados.
                                                </div>
                                            @else
                                                <p>¿Está seguro de que desea eliminar la zona <strong>{{ $zona->descripcion }}</strong>?</p>
                                                <p class="text-danger">Esta acción no se puede deshacer.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            @if($zona->hoteles->count() == 0)
                                                <form action="{{ route('admin.zonas.eliminar', $zona->id_zona) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            @endif
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

<!-- Modal para crear nueva zona -->
<div class="modal fade" id="createZonaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Nueva Zona</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.zonas.crear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                        <div class="form-text">Ejemplo: Centro, Norte, Sur, etc.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Crear Zona</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
