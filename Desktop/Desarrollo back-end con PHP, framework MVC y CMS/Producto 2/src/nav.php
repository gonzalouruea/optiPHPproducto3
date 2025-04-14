<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Isla-Transfers</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>

        <?php if (!isset($_SESSION['email'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
        <?php endif; ?>

        <?php if (isset($_SESSION['email'])): ?>
          <?php if ($_SESSION['rol'] == 'admin'): ?>
            <!-- Menú Admin original -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Menú Admin
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="nueva_reserva.php">Nueva reserva</a></li>
                <li><a class="dropdown-item" href="ver_reservas.php">Ver reservas</a></li>
              </ul>
            </li>
          <?php else: ?>
            <!-- Menú Usuario -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Menú Usuario
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="mis_reservas.php">Mis reservas</a></li>
                <li><a class="dropdown-item" href="reserva.php">Nueva reserva</a></li>
                <li><a class="dropdown-item" href="cambiarDatosUsuarios.php">Modificar datos personales</a></li>
              </ul>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <?php if (isset($_SESSION['email'])): ?>
          <li class="nav-item">
            <span class="nav-link">Bienvenido, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar sesión</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="altaUsuario.php">Registrarse</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>