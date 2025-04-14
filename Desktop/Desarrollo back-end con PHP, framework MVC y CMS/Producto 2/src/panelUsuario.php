<?php
require_once 'auth.php';
requireLogin();
requireRole('usuario');

require_once 'conexion.php';
$email = $_SESSION['email'];

$error = '';
try {
    $sqlReservas = "
        SELECT tr.localizador, tr.fecha_reserva, tr.num_viajeros, 
               ttr.Descripción AS tipo_reserva, th.nombre AS hotel_nombre
        FROM transfer_reservas tr
        LEFT JOIN transfer_tipo_reserva ttr ON tr.id_tipo_reserva = ttr.id_tipo_reserva
        LEFT JOIN transfer_hotel th ON tr.id_hotel = th.id_hotel
        WHERE tr.email_cliente = ?
        ORDER BY tr.fecha_reserva DESC";
    $stmtReservas = $db->prepare($sqlReservas);
    $stmtReservas->execute([$email]);
    $reservas = $stmtReservas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "No se pudieron cargar las reservas. Por favor, intenta de nuevo.";
    error_log("Error en panelUsuario.php: " . $e->getMessage());
    $reservas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Panel de Usuario - Isla-Transfers</title>
    <style>
        .table th, .table td { vertical-align: middle; }
        .badge-user { background-color: #28a745; }
    </style>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Panel de Usuario</h2>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['email']); ?>!</p>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Opciones</div>
                <div class="card-body">
                    <a href="/crearReserva.php" class="btn btn-primary mb-2">Crear Nueva Reserva</a>
                    <a href="/modificarDatos.php" class="btn btn-secondary mb-2">Modificar Datos</a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Mis Reservas</div>
                <div class="card-body">
                    <?php if (empty($reservas)): ?>
                        <p>No tienes reservas registradas.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Localizador</th>
                                        <th>Fecha de Reserva</th>
                                        <th>Tipo de Reserva</th>
                                        <th>Hotel</th>
                                        <th>Número de Viajeros</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservas as $reserva): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['localizador']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['tipo_reserva']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['hotel_nombre'] ?: 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['num_viajeros']); ?></td>
                                            <td>
                                                <a href="/visualizarReserva.php?localizador=<?php echo urlencode($reserva['localizador']); ?>" class="btn btn-sm btn-info">Ver Detalles</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>