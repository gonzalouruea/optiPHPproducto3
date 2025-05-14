@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@section('content')
  <div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Gestionar Usuarios</h4>
    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createUsuarioModal">
      <i class="fas fa-plus"></i> Nuevo Usuario
    </button>
    </div>
    <div class="card-body">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($usuarios->isEmpty())
    <div class="alert alert-info">No hay usuarios registrados.</div>
    @else
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
      <thead>
      <tr>
      <th>ID</th>
      <th>Nombre completo</th>
      <th>Email</th>
      <th>Rol</th>
      </tr>
      </thead>
      <tbody>
      @foreach($usuarios as $usuario)
      <tr>
      <td>{{ $usuario->id_viajero }}</td>
      <td>{{ $usuario->nombre }} {{ $usuario->apellido1 }} {{ $usuario->apellido2 }}</td>
      <td>{{ $usuario->email }}</td>
      <td>{{ ucfirst($usuario->rol) }}</td>
      </tr>
      @endforeach
      </tbody>
      </table>
    </div>
    @endif
    </div>
  </div>

  <!-- Modal para crear nuevo usuario -->
  <div class="modal fade" id="createUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.usuarios.crear') }}">
      @csrf
      <div class="modal-header bg-primary text-white">
      <h5 class="modal-title">Nuevo Usuario</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="apellido1" class="form-label">Primer Apellido</label>
        <input type="text" name="apellido1" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="apellido2" class="form-label">Segundo Apellido</label>
        <input type="text" name="apellido2" class="form-control">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required minlength="6">
      </div>
      <div class="mb-3">
        <label for="rol" class="form-label">Rol</label>
        <select name="rol" id="rol" class="form-select" required>
        <option value="usuario">Usuario</option>
        <option value="corporativo">Corporativo</option>
        <option value="admin">Admin</option>
        </select>
      </div>
      <div class="mb-3 d-none" id="hotel-select">
        <label for="id_hotel" class="form-label">Hotel (solo para corporativos)</label>
        <select name="id_hotel" class="form-select">
        <option value="">Seleccionar hotel</option>
        @foreach($hoteles as $hotel)
      <option value="{{ $hotel->id_hotel }}">{{ $hotel->descripcion }}</option>
      @endforeach
        </select>
      </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-primary">Crear Usuario</button>
      </div>
    </form>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const rolSelect = document.querySelector('#rol');
    const hotelSelect = document.querySelector('#hotel-select');

    function toggleHotelSelect() {
      if (rolSelect.value === 'corporativo') {
      hotelSelect.classList.remove('d-none');
      } else {
      hotelSelect.classList.add('d-none');
      }
    }

    rolSelect.addEventListener('change', toggleHotelSelect);
    toggleHotelSelect();
    });
  </script>
@endpush
