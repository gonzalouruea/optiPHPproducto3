<?php
session_start();

// Verificar rol de administrador
require 'conexion.php';
$stmt = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$rolUsuario = $stmt->fetchColumn();

if ($rolUsuario !== 'admin') {
    header("Location: index.php?error=Acceso denegado");
    exit;
}

// Obtener la página actual para resaltar el menú activo
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin_panel.php">
            <i class="bi bi-speedometer2"></i> Panel Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'admin_panel.php' ? 'active' : '' ?>" 
                       href="admin_panel.php">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                </li>
                
                <!-- Gestión de Reservas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($current_page, ['mis_reservas.php', 'calendario_reservas.php']) ? 'active' : '' ?>" 
                       href="#" id="reservasDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar-check"></i> Reservas
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="mis_reservas.php">
                                <i class="bi bi-list-ul"></i> Lista de Reservas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="calendario_reservas.php">
                                <i class="bi bi-calendar3"></i> Calendario
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="reserva.php">
                                <i class="bi bi-plus-circle"></i> Nueva Reserva
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Gestión de Recursos -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= strpos($current_page, 'gestionar_') !== false ? 'active' : '' ?>" 
                       href="#" id="recursosDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Gestión
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="admin/gestionar_vehiculos.php">
                                <i class="bi bi-car-front"></i> Vehículos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="admin/gestionar_hoteles.php">
                                <i class="bi bi-building"></i> Hoteles
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="admin/gestionar_zonas.php">
                                <i class="bi bi-geo-alt"></i> Zonas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="admin/gestionar_tipos_reserva.php">
                                <i class="bi bi-bookmark"></i> Tipos de Reserva
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'reportes.php' ? 'active' : '' ?>" 
                       href="reportes.php">
                        <i class="bi bi-graph-up"></i> Reportes
                    </a>
                </li>
            </ul>

            <!-- Perfil y Cerrar Sesión -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" 
                       data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> 
                        <?= htmlspecialchars($_SESSION['email']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="perfil.php">
                                <i class="bi bi-person"></i> Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
