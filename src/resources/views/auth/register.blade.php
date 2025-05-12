@extends('layouts.app')

@section('title', 'Registro de Usuario')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Registro de Usuario</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('register') }}">
          @csrf

          {{-- Nombre y apellidos --}}
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text"
                     class="form-control @error('nombre') is-invalid @enderror"
                     id="nombre" name="nombre" value="{{ old('nombre') }}" required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="apellido1" class="form-label">Primer Apellido</label>
              <input type="text"
                     class="form-control @error('apellido1') is-invalid @enderror"
                     id="apellido1" name="apellido1" value="{{ old('apellido1') }}" required>
              @error('apellido1')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="apellido2" class="form-label">Segundo Apellido</label>
              <input type="text"
                     class="form-control @error('apellido2') is-invalid @enderror"
                     id="apellido2" name="apellido2" value="{{ old('apellido2') }}" required>
              @error('apellido2')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Email y contraseña --}}
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     id="email" name="email" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     id="password" name="password" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Dirección completa --}}
          <div class="row mb-3">
            <div class="col-md-12">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text"
                     class="form-control @error('direccion') is-invalid @enderror"
                     id="direccion" name="direccion" value="{{ old('direccion') }}" required>
              @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- CP, Ciudad, País --}}
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="codPostal" class="form-label">Código Postal</label>
              <input type="text"
                     class="form-control @error('codPostal') is-invalid @enderror"
                     id="codPostal" name="codPostal" value="{{ old('codPostal') }}" required>
              @error('codPostal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="ciudad" class="form-label">Ciudad</label>
              <input type="text"
                     class="form-control @error('ciudad') is-invalid @enderror"
                     id="ciudad" name="ciudad" value="{{ old('ciudad') }}" required>
              @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="pais" class="form-label">País</label>
              <input type="text"
                     class="form-control @error('pais') is-invalid @enderror"
                     id="pais" name="pais" value="{{ old('pais') }}" required>
              @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Si es admin, puede elegir rol --}}
          @if(Auth::check() && Auth::user()->rol === 'admin')
            <div class="mb-3">
              <label for="rol" class="form-label">Rol</label>
              <select class="form-select @error('rol') is-invalid @enderror"
                      id="rol" name="rol" required>
                <option value="usuario"      {{ old('rol')=='usuario'?'selected':'' }}>Usuario</option>
                <option value="corporativo"  {{ old('rol')=='corporativo'?'selected':'' }}>Corporativo</option>
                <option value="admin"        {{ old('rol')=='admin'?'selected':'' }}>Administrador</option>
              </select>
              @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Zona y comisión: sólo si rol=corporativo --}}
            <div id="bloque-corporativo" style="display: none;">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="id_zona" class="form-label">Zona</label>
                  <select name="id_zona" id="id_zona"
                          class="form-select @error('id_zona') is-invalid @enderror">
                    <option value="">Selecciona una zona…</option>
                    @foreach($zonas as $zona)
                      <option value="{{ $zona->id_zona }}"
                        {{ old('id_zona')==$zona->id_zona?'selected':'' }}>
                        {{ $zona->descripcion }}
                      </option>
                    @endforeach
                  </select>
                  @error('id_zona')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label for="comision" class="form-label">Comisión (%)</label>
                  <input type="number" name="comision" id="comision"
                         class="form-control @error('comision') is-invalid @enderror"
                         value="{{ old('comision',10) }}" min="0" step="0.1">
                  @error('comision')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          @else
            {{-- If not admin, fuerza rol=usuario --}}
            <input type="hidden" name="rol" value="usuario">
          @endif

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Registrarse</button>
          </div>
        </form>
      </div>
      <div class="card-footer text-center">
        <p class="mb-0">
          ¿Ya tienes cuenta?
          <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const rolSelect = document.getElementById('rol');
    const corpBlock = document.getElementById('bloque-corporativo');

    function toggleCorpFields() {
      if (rolSelect.value === 'corporativo') {
        corpBlock.style.display = 'block';
      } else {
        corpBlock.style.display = 'none';
      }
    }

    if (rolSelect) {
      rolSelect.addEventListener('change', toggleCorpFields);
      // Lo llamamos una vez para el caso de validación fallida
      toggleCorpFields();
    }
  });
</script>
@endsection
