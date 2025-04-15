<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

if (empty($_GET['id'])) {
    header("Location: mis_reservas.php");
    exit;
}

try {
    $sql = "SELECT 
                r.*, 
                v.Descripción as vehiculo,
                h.usuario as hotel,
                CASE 
                    WHEN r.creado_por_admin = 1 THEN 'Administrador'
                    ELSE 'Tú mismo'
                END as creador_reserva
            FROM transfer_reservas r
            JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
            LEFT JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
            WHERE r.id_reserva = :id AND r.email_cliente = :email";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id' => $_GET['id'],
        ':email' => $_SESSION['email']
    ]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reserva) {
        header("Location: mis_reservas.php?error=reserva_no_encontrada");
        exit;
    }
} catch (PDOException $e) {
    header("Location: mis_reservas.php?error=db_error");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Detalle de Reserva</h4>
                <span class="badge <?= $reserva['creado_por_admin'] ? 'bg-danger' : 'bg-success' ?>">
                    <?= $reserva['creador_reserva'] ?>
                </span>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Localizador:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($reserva['localizador']) ?></dd>
                    
                    <dt class="col-sm-3">Fecha/Hora:</dt>
                    <dd class="col-sm-9">
                        <?= date('d/m/Y', strtotime($reserva['fecha_entrada'])) ?>
                        <?= $reserva['hora_entrada'] ? ' a las ' . substr($reserva['hora_entrada'], 0, 5) : '' ?>
                    </dd>
                    
                    <dt class="col-sm-3">Hotel:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($reserva['hotel'] ?? 'N/A') ?></dd>
                    
                    <dt class="col-sm-3">Vehículo:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($reserva['vehiculo']) ?></dd>
                    
                    <dt class="col-sm-3">Pasajeros:</dt>
                    <dd class="col-sm-9"><?= $reserva['num_viajeros'] ?></dd>
                    
                    <?php if ($reserva['numero_vuelo_entrada']): ?>
                    <dt class="col-sm-3">Número de Vuelo:</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars($reserva['numero_vuelo_entrada']) ?></dd>
                    <?php endif; ?>
                </dl>
                
                <a href="mis_reservas.php" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </div>
</body>
</html>