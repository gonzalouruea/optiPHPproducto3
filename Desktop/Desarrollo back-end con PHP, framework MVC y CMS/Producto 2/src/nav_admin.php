<!-- Incluir iconos de Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="../index.php">Isla-Transfers</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="../index.php">Home</a>
        </li>

        <?php if (isset($_SESSION['email'])): ?>
          <!-- Menú Admin -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Panel Admin
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../admin_panel.php">
                  <i class="bi bi-speedometer2"></i> Dashboard
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="../reserva.php">
                  <i class="bi bi-plus-circle"></i> Nueva Reserva
              </a></li>
              <li><a class="dropdown-item" href="../mis_reservas.php">
                  <i class="bi bi-list-ul"></i> Ver Reservas
              </a></li>
              <li><a class="dropdown-item" href="../calendario_reservas.php">
                  <i class="bi bi-calendar3"></i> Calendario
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="gestionar_vehiculos.php">
                  <i class="bi bi-car-front"></i> Gestionar Vehículos
              </a></li>
              <li><a class="dropdown-item" href="gestionar_hoteles.php">
                  <i class="bi bi-building"></i> Gestionar Hoteles
              </a></li>
              <li><a class="dropdown-item" href="gestionar_zonas.php">
                  <i class="bi bi-geo-alt"></i> Gestionar Zonas
              </a></li>
              <li><a class="dropdown-item" href="gestionar_tipos_reserva.php">
                  <i class="bi bi-tags"></i> Gestionar Tipos de Reserva
              </a></li>
            </ul>
          </li>
          
          <!-- Perfil y Logout -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['email']); ?>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../cambiarDatosUsuarios.php">
                  <i class="bi bi-person-gear"></i> Mi Perfil
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">
                  <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
              </a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
