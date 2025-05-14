<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Sistema de Reservas')</title>

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Font Awesome --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  {{-- Estilos propios rápidos --}}
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .content {
      flex: 1;
    }

    footer {
      margin-top: auto;
    }
  </style>

  @yield('styles')
</head>

<body>
  <!--·─────────────────────────────────────────────
|  Barra de navegación
·─────────────────────────────────────────────-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
      <a class="navbar-brand" href="{{ route('index') }}">Sistema de Reservas</a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        {{-- enlaces a la izquierda --}}
        <ul class="navbar-nav me-auto">
          @auth
          {{-- Panel Hotel solo para corporativo --}}
          @if(Auth::user()->rol === 'admin')
            <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Dashboard</a></li>
          @endif
          @if(Auth::user()->rol === 'corporativo')
            <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Dashboard</a></li>
          @endif
          @if(Auth::user()->rol === 'usuario')
            <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">Dashboard</a></li>
          @endif
          <li class="nav-item"><a class="nav-link" href="{{ route('reservas.index') }}">Mis Reservas</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reservas.create') }}">Nueva Reserva</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reservas.calendario') }}">Calendario</a></li>


          {{-- Menú Administración solo para admin --}}
          @if(Auth::user()->rol === 'admin')
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              Administración
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('admin.panel') }}">Panel</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
              <li>
              <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="{{ route('admin.hoteles.index') }}">Hoteles</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.vehiculos.index') }}">Vehículos</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.zonas.index') }}">Zonas</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.tipos-reserva.index') }}">Tipos de Reserva</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.precios.index') }}">Precios</a></li>
              <li>
              <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="{{ route('admin.reportes.reservas') }}">Reportes</a></li>
            </ul>
          </li>
        @endif
      @endauth
        </ul>

        {{-- zona de la derecha --}}
        <ul class="navbar-nav">
          @guest
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrarse</a></li>
      @else
        <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          {{ Auth::user()->nombre }} ({{ Auth::user()->rol }})
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ route('perfil') }}">Mi Perfil</a></li>
          <li>
          <hr class="dropdown-divider">
          </li>
          <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item" type="submit">Cerrar Sesión</button>
          </form>
          </li>
        </ul>
        </li>
      @endguest
        </ul>
      </div><!-- /.collapse -->
    </div><!-- /.container -->
  </nav>

  <!--·─────────────────────────────────────────────
|  Contenido principal
·─────────────────────────────────────────────-->
  <div class="container content">
    {{-- mensajes flash --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="mb-0">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

    {{-- contenido de cada vista --}}
    @yield('content')
  </div>

  <!--·─────────────────────────────────────────────
|  Footer
·─────────────────────────────────────────────-->
  <footer class="bg-dark text-white py-4">
    <div class="container d-flex justify-content-between flex-column flex-md-row">
      <div>
        <h5>Sistema de Reservas</h5>
        <p class="mb-0">Gestión de reservas de viajes y transfers</p>
      </div>
      <div class="text-md-end">
        <p class="mb-0">&copy; {{ date('Y') }} Sistema de Reservas. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  @yield('scripts')
</body>

</html>
