@extends('layouts.app')

@section('title', 'Gestionar Hoteles')

@section('content')
  <div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Gestionar Hoteles</h4>
    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createHotelModal">
      <i class="fas fa-plus"></i> Nuevo Hotel
    </button>
    </div>
    <div class="card-body">
    @if($hoteles->isEmpty())
    <div class="alert alert-info">No hay hoteles registrados.</div>
    @else
    <div class="table-responsive">
      <table class="table table-striped table-hover">
      <thead>
      <tr>
      <th>ID</th>
      <th>Usuario</th>
      <th>Zona</th>
      <th>Comisión</th>
      <th>Acciones</th>
      </tr>
      </thead>
      <tbody>
      @foreach($hoteles as $hotel)
      <tr>
      <td>{{ $hotel->id_hotel }}</td>
      <td>{{ $hotel->Usuario }}</td>
      <td>{{ $hotel->zona->descripcion ?? 'Sin zona' }}</td>
      <td>{{ $hotel->comision }}%</td>
      <td>
      <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
      data-bs-target="#editHotelModal{{ $hotel->id_hotel }}">
      <i class="fas fa-edit"></i> Editar
      </button>
      <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
      data-bs-target="#deleteHotelModal{{ $hotel->id_hotel }}">
      <i class="fas fa-trash"></i> Eliminar
      </button>
      </td>
      </tr>

      <!-- Modal de edición para cada hotel -->
      <div class="modal fade" id="editHotelModal{{ $hotel->id_hotel }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header bg-primary text-white">
      <h5 class="modal-title">Editar Hotel</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.hoteles.actualizar', $hotel->id_hotel) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-body">
      <div class="mb-3">
        <label for="Usuario{{ $hotel->id_hotel }}" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="Usuario{{ $hotel->id_hotel }}" name="Usuario"
        value="{{ $hotel->Usuario }}" required>
      </div>
      <div class="mb-3">
        <label for="descripcion{{ $hotel->id_hotel }}" class="form-label">Descripcion</label>
        <input type="text" class="form-control" id="descripcion{{ $hotel->id_hotel }}" name="descripcion"
        value="{{ $hotel->descripcion }}" required>
      </div>
      <div class="mb-3">
        <label for="id_zona{{ $hotel->id_hotel }}" class="form-label">Zona</label>
        <select class="form-select" id="id_zona{{ $hotel->id_hotel }}" name="id_zona" required>
        @foreach($zonas as $zona)
      <option value="{{ $zona->id_zona }}" {{ $hotel->id_zona == $zona->id_zona ? 'selected' : '' }}>
      {{ $zona->descripcion }}
      </option>
      @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label for="comision{{ $hotel->id_hotel }}" class="form-label">Comisión (%)</label>
        <input type="number" class="form-control" id="comision{{ $hotel->id_hotel }}" name="comision"
        value="{{ $hotel->comision }}" required min="0" max="100">
      </div>
      <div class="mb-3">
        <label for="password{{ $hotel->id_hotel }}" class="form-label">Contraseña (dejar en blanco para no
        cambiar)</label>
        <input type="password" class="form-control" id="password{{ $hotel->id_hotel }}" name="password">
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

      <!-- Modal de eliminación para cada hotel -->
      <div class="modal fade" id="deleteHotelModal{{ $hotel->id_hotel }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header bg-danger text-white">
      <h5 class="modal-title">Confirmar Eliminación</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <p>¿Está seguro de que desea eliminar el hotel <strong>{{ $hotel->Usuario }}</strong>?</p>
      <p class="text-danger">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <form action="{{ route('admin.hoteles.eliminar', $hotel->id_hotel) }}" method="POST">
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

  <!-- Modal para crear nuevo hotel -->
  <div class="modal fade" id="createHotelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
      <h5 class="modal-title">Nuevo Hotel</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.hoteles.crear') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="mb-3">
        <label for="Usuario" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="Usuario" name="Usuario" required>
        </div>
        <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
        </div>
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
        <label for="comision" class="form-label">Comisión (%)</label>
        <input type="number" class="form-control" id="comision" name="comision" required min="0" max="100">
        </div>
        <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Crear Hotel</button>
      </div>
      </form>
    </div>
    </div>
  </div>
@endsection
