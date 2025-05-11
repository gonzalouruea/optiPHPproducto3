<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos propios -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover {
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .content {
            min-height: 100vh;
            padding: 20px;
        }
        .main-content {
            margin-left: 250px;
        }
    </style>
    @yield('styles')
    
    <!-- Estilos principales -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Scripts principales -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Estilos propios -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover {
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .content {
            min-height: 100vh;
            padding: 20px;
        }
        .main-content {
            margin-left: 250px;
        }
        .calendar-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .calendar {
            width: 100%;
            min-height: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar col-md-3 col-lg-2">
            <div class="p-3">
                <h4 class="text-white mb-4">Panel de Administración</h4>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('admin.panel') }}">
                        <i class="fas fa-home me-2"></i> Panel Principal
                    </a>
                    <a class="nav-link" href="{{ route('admin.usuarios.index') }}">
                        <i class="fas fa-users me-2"></i> Usuarios
                    </a>
                    <a class="nav-link" href="{{ route('admin.hoteles.index') }}">
                        <i class="fas fa-hotel me-2"></i> Hoteles
                    </a>
                    <a class="nav-link" href="{{ route('admin.vehiculos.index') }}">
                        <i class="fas fa-car me-2"></i> Vehículos
                    </a>
                    <a class="nav-link" href="{{ route('admin.zonas.index') }}">
                        <i class="fas fa-map-marker-alt me-2"></i> Zonas
                    </a>
                    <a class="nav-link" href="{{ route('admin.tipos-reserva.index') }}">
                        <i class="fas fa-tags me-2"></i> Tipos de Reserva
                    </a>
                    <a class="nav-link" href="{{ route('admin.precios.index') }}">
                        <i class="fas fa-money-bill-wave me-2"></i> Precios
                    </a>
                    <a class="nav-link" href="{{ route('admin.reportes.reservas') }}">
                        <i class="fas fa-chart-bar me-2"></i> Reportes
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
                <div class="container-fluid">
                    <button class="btn btn-link p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0">@yield('title')</h4>
                    <div class="navbar-nav ms-auto">
                        <a href="{{ route('admin.panel') }}" class="nav-link me-3">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                        <span class="navbar-text me-3">
                            Bienvenido, {{ Auth::user()->nombre }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-link text-dark p-0" type="submit">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="container-fluid">
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

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('additional-scripts')
</body>
</html>
