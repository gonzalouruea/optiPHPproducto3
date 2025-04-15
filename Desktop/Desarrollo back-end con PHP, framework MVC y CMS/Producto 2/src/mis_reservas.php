<?php
session_start();
require 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Obtener rol del usuario
$stmt = $db->prepare("SELECT rol FROM transfer_viajeros WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$rolUsuario = $stmt->fetchColumn();

// Consulta según el rol
if ($rolUsuario === 'admin') {
    // Admins ven todas las reservas
    $sql = "SELECT r.*, 
            v.Descripción as vehiculo_nombre,
            h.usuario as hotel_nombre,
            tr.Descripción as tipo_reserva_nombre
            FROM transfer_reservas r
            LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
            LEFT JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
            LEFT JOIN transfer_tipo_reserva tr ON r.id_tipo_reserva = tr.id_tipo_reserva
            ORDER BY r.fecha_reserva DESC";
    $reservas = $db->query($sql)->fetchAll();
} else {
    // Usuarios normales ven solo sus reservas
    $sql = "SELECT r.*, 
            v.Descripción as vehiculo_nombre,
            h.usuario as hotel_nombre,
            tr.Descripción as tipo_reserva_nombre
            FROM transfer_reservas r
            LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
            LEFT JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
            LEFT JOIN transfer_tipo_reserva tr ON r.id_tipo_reserva = tr.id_tipo_reserva
            WHERE r.email_cliente = ?
            ORDER BY r.fecha_reserva DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$_SESSION['email']]);
    $reservas = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .badge {
            font-size: 0.8rem;
        }
        .card-subtitle {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mis Reservas</h2>
            <a href="reserva.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Reserva
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <?php if (empty($reservas)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay reservas para mostrar.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($reservas as $reserva): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-ticket-perforated"></i>
                                    <?= htmlspecialchars($reserva['localizador']) ?>
                                </h5>
                                <?php if ($reserva['creado_por_admin']): ?>
                                    <span class="badge bg-info">
                                        <i class="bi bi-person-badge"></i> Creada por Admin
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-person-check"></i> Reserva Personal
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <?= htmlspecialchars($reserva['tipo_reserva_nombre'] ?? match($reserva['id_tipo_reserva']) {
                                            1 => 'Aeropuerto → Hotel',
                                            2 => 'Hotel → Aeropuerto',
                                            3 => 'Ida y vuelta',
                                            default => 'Desconocido'
                                        }) ?>
                                    </h6>
                                </div>

                                <div class="row g-3">
                                    <div class="col-6">
                                        <p class="mb-1"><i class="bi bi-building"></i> <strong>Hotel:</strong></p>
                                        <?= htmlspecialchars($reserva['hotel_nombre'] ?? 'Hotel ' . $reserva['id_hotel']) ?>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><i class="bi bi-people"></i> <strong>Pasajeros:</strong></p>
                                        <?= htmlspecialchars($reserva['num_viajeros']) ?>
                                    </div>
                                    <?php if ($reserva['fecha_entrada']): ?>
                                    <div class="col-6">
                                        <p class="mb-1"><i class="bi bi-calendar-event"></i> <strong>Llegada:</strong></p>
                                        <?= date('d/m/Y', strtotime($reserva['fecha_entrada'])) ?>
                                        <br><?= date('H:i', strtotime($reserva['hora_entrada'])) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($reserva['fecha_vuelo_salida']): ?>
                                    <div class="col-6">
                                        <p class="mb-1"><i class="bi bi-calendar-event"></i> <strong>Salida:</strong></p>
                                        <?= date('d/m/Y', strtotime($reserva['fecha_vuelo_salida'])) ?>
                                        <br><?= date('H:i', strtotime($reserva['hora_vuelo_salida'])) ?>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <p class="mb-1"><i class="bi bi-car-front"></i> <strong>Vehículo:</strong></p>
                                        <?= htmlspecialchars($reserva['vehiculo_nombre']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i>
                                        Reservada: <?= date('d/m/Y', strtotime($reserva['fecha_reserva'])) ?>
                                    </small>
                                    <div class="btn-group">
                                        <a href="detalle_reserva.php?id=<?= $reserva['id_reserva'] ?>" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <?php if ($rolUsuario === 'admin' || 
                                                  time() < strtotime($reserva['fecha_entrada'] ?? $reserva['fecha_vuelo_salida']) - 48*3600): ?>
                                        <a href="editar_reserva.php?id=<?= $reserva['id_reserva'] ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
