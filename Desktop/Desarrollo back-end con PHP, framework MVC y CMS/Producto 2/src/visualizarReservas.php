<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

try {
    require_once 'conexion.php';
} catch (Exception $e) {
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit;
}

$email = $_SESSION['email'];
$sql = "SELECT rol FROM transfer_viajeros WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Obtener todas las reservas
$sql = "SELECT tr.*, 
               tv.Descripción AS vehiculo, 
               th.usuario AS hotel, 
               ttr.Descripción AS tipo_reserva 
        FROM transfer_reservas tr
        LEFT JOIN transfer_vehiculo tv ON tr.id_vehiculo = tv.id_vehiculo
        LEFT JOIN tranfer_hotel th ON tr.id_hotel = th.id_hotel
        LEFT JOIN transfer_tipo_reserva ttr ON tr.id_tipo_reserva = ttr.id_tipo_reserva
        ORDER BY tr.fecha_reserva DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generar eventos para el calendario
$eventos = [];
foreach ($reservas as $reserva) {
    $fechaInicio = null;
    $fechaFin = null;
    $titulo = "Reserva: " . $reserva['localizador'];

    if ($reserva['fecha_entrada'] && ($reserva['id_tipo_reserva'] == 1 || $reserva['id_tipo_reserva'] == 3)) {
        $fechaInicio = "{$reserva['fecha_entrada']}T{$reserva['hora_entrada']}";
    }
    if ($reserva['fecha_vuelo_salida'] && ($reserva['id_tipo_reserva'] == 2 || $reserva['id_tipo_reserva'] == 3)) {
        $fechaFin = "{$reserva['fecha_vuelo_salida']}T" . (new DateTime($reserva['hora_vuelo_salida']))->format('H:i:s');
    }

    if ($fechaInicio || $fechaFin) {
        $eventos[] = [
            'title' => $titulo,
            'start' => $fechaInicio ?: $fechaFin,
            'end' => $fechaFin,
            'url' => "modificarReserva.php?id_reserva={$reserva['id_reserva']}"
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
    <title>Visualizar Reservas - Isla-Transfers</title>
    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    
<?php include 'nav.php'; ?>

<div class="container my-5">
    <div class="text-center py-2">
        <h2>Todas las Reservas</h2>
    </div>

    <!-- Calendario -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">Calendario de Reservas</div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de reservas -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">Lista de Reservas</div>
                <div class="card-body">
                    <?php if (empty($reservas)): ?>
                        <p>No hay reservas disponibles.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Localizador</th>
                                    <th>Email Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Hotel</th>
                                    <th>Destino</th>
                                    <th>Tipo de Trayecto</th>
                                    <th>Número de Viajeros</th>
                                    <th>Fecha de Llegada</th>
                                    <th>Hora de Llegada</th>
                                    <th>Número de Vuelo (Llegada)</th>
                                    <th>Origen de Vuelo (Llegada)</th>
                                    <th>Fecha de Salida</th>
                                    <th>Hora de Salida</th>
                                    <th>Fecha de Reserva</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservas as $reserva): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reserva['localizador']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['email_cliente']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['vehiculo']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['hotel']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['id_destino']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['tipo_reserva']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['num_viajeros']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['fecha_entrada']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['hora_entrada']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['numero_vuelo_entrada']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['origen_vuelo_entrada']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['fecha_vuelo_salida']); ?></td>
                                        <td><?php echo htmlspecialchars((new DateTime($reserva['hora_vuelo_salida']))->format('H:i:s')); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['fecha_reserva']); ?></td>
                                        <td>
                                            <a href="modificarReserva.php?id_reserva=<?php echo $reserva['id_reserva']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="panelAdmin.php" class="btn btn-primary">Volver al Panel</a>
    </div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?php echo json_encode($eventos); ?>,
        eventClick: function(info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        }
    });
    calendar.render();
});
</script>
</body>
</html>