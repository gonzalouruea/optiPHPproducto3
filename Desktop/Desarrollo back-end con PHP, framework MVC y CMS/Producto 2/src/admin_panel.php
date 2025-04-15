<?php
session_start();
require 'conexion.php';

// Verificar rol de administrador
$stmt = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$rolUsuario = $stmt->fetchColumn();

if ($rolUsuario !== 'admin') {
    header("Location: index.php?error=Acceso denegado");
    exit;
}

// Obtener estadísticas
$stats = [
    'reservas_totales' => $db->query("SELECT COUNT(*) FROM transfer_reservas")->fetchColumn(),
    'reservas_hoy' => $db->query("SELECT COUNT(*) FROM transfer_reservas WHERE DATE(fecha_reserva) = CURDATE()")->fetchColumn(),
    'vehiculos' => $db->query("SELECT COUNT(*) FROM transfer_vehiculo")->fetchColumn(),
    'hoteles' => $db->query("SELECT COUNT(*) FROM transfer_hotel")->fetchColumn(),
    'usuarios' => $db->query("SELECT COUNT(*) FROM transfer_viajeros WHERE rol = 'usuario'")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(45deg, #4158D0, #C850C0);
            color: white;
        }
        .action-card {
            background: white;
        }
        .action-card .card-title {
            color: #333;
            font-weight: 600;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'nav.php'; ?>
    
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-speedometer2"></i> Panel de Administración</h2>
            <a href="reserva.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Reserva
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Reservas Totales</h6>
                        <div class="stat-number"><?= $stats['reservas_totales'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Reservas Hoy</h6>
                        <div class="stat-number"><?= $stats['reservas_hoy'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Usuarios Registrados</h6>
                        <div class="stat-number"><?= $stats['usuarios'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Principales -->
        <h4 class="mb-3">Gestión del Sistema</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card action-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">Reservas</h5>
                        <p class="card-text">Gestiona todas las reservas del sistema</p>
                        <div class="d-grid gap-2">
                            <a href="mis_reservas.php" class="btn btn-primary">Ver Reservas</a>
                            <a href="calendario_reservas.php" class="btn btn-outline-primary">Ver Calendario</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card action-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-car-front fs-1 text-success mb-3"></i>
                        <h5 class="card-title">Vehículos</h5>
                        <p class="card-text">Administra la flota de vehículos</p>
                        <div class="d-grid gap-2">
                            <a href="admin/gestionar_vehiculos.php" class="btn btn-success">Gestionar Vehículos</a>
                            <span class="text-muted"><?= $stats['vehiculos'] ?> vehículos activos</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card action-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-building fs-1 text-info mb-3"></i>
                        <h5 class="card-title">Hoteles</h5>
                        <p class="card-text">Gestiona los hoteles y destinos</p>
                        <div class="d-grid gap-2">
                            <a href="admin/gestionar_hoteles.php" class="btn btn-info text-white">Gestionar Hoteles</a>
                            <span class="text-muted"><?= $stats['hoteles'] ?> hoteles registrados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
