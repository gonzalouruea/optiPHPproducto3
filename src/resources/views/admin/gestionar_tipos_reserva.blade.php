@extends('layouts.app')

@section('title', 'Gestionar Tipos de Reserva')

@section('content')
<div class="card shadow">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Gestionar Tipos de Reserva</h4>
        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createTipoReservaModal">
            <i class="fas fa-plus"></i> Nuevo Tipo de Reserva
        </button>
    </div>
    <div class="card-body">
        @if($tiposReserva->isEmpty())
            <div class="alert alert-info">No hay tipos de reserva registrados.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Reservas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tiposReserva as $tipo)
                            <tr>
                                <td>{{ $tipo->id_tipo_reserva }}</td>
                                <td>{{ $tipo->Descripción }}</td>
                                <td>{{ $tipo->reservas->count() }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editTipoReservaModal{{ $tipo->id_tipo_reserva }}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger {{ $tipo->reservas->count() > 0 ? 'disabled' : '' }}" 
                                            data-bs-toggle="modal" data-bs-target="#deleteTipoReservaModal{{ $tipo->id_tipo_reserva }}"
                                            {{ $tipo->reservas->count() > 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal de edición para cada tipo de reserva -->
                            <div class="modal fade" id="editTipoReservaModal{{ $tipo->id_tipo_reserva }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">Editar Tipo de Reserva</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.tipos-reserva.actualizar', $tipo->id_tipo_reserva) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="Descripción{{ $tipo->id_tipo_reserva }}" class="form-label">Descripción</label>
                                                    <input type="text" class="form-control" id="Descripción{{ $tipo->id_tipo_reserva }}" 
                                                           name="Descripción" value="{{ $tipo->Descripción }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-info">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal de eliminación para cada tipo de reserva -->
                            <div class="modal fade" id="deleteTipoReservaModal{{ $tipo->id_tipo_reserva }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($tipo->reservas->count() > 0)
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> No se puede eliminar este tipo de reserva porque tiene {{ $tipo->reservas->count() }} reservas asociadas.
                                                </div>
                                            @else
                                                <p>¿Está seguro de que desea eliminar el tipo de reserva <strong>{{ $tipo->Descripción }}</strong>?</p>
                                                <p class="text-danger">Esta acción no se puede deshacer.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            @if($tipo->reservas->count() == 0)
                                                <form action="{{ route('admin.tipos-reserva.eliminar', $tipo->id_tipo_reserva) }}" method="POST">
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

<!-- Modal para crear nuevo tipo de reserva -->
<div class="modal fade" id="createTipoReservaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Nuevo Tipo de Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.tipos-reserva.crear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Descripción" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="Descripción" name="Descripción" required>
                        <div class="form-text">Ejemplo: Ida, Ida y Vuelta, etc.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Crear Tipo de Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
