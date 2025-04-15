<?php
session_start();
require 'conexion.php';

// Verificar sesión y permisos
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Obtener el tipo de vista (día, semana, mes)
$vista = $_GET['vista'] ?? 'mes';
$fecha = $_GET['fecha'] ?? date('Y-m-d');

// Calcular rango de fechas según la vista
$inicio = new DateTime($fecha);
$fin = clone $inicio;

switch ($vista) {
    case 'dia':
        $titulo = $inicio->format('d/m/Y');
        break;
    case 'semana':
        $inicio->modify('monday this week');
        $fin->modify('sunday this week');
        $titulo = $inicio->format('d/m/Y') . " - " . $fin->format('d/m/Y');
        break;
    default: // mes
        $inicio->modify('first day of this month');
        $fin->modify('last day of this month');
        $titulo = $inicio->format('F Y');
        break;
}

// Obtener reservas en el rango de fechas
$sql = "SELECT r.*, 
               h.usuario as hotel_nombre,
               v.Descripción as vehiculo_descripcion,
               tr.Descripción as tipo_reserva_descripcion
        FROM transfer_reservas r
        LEFT JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
        LEFT JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
        LEFT JOIN transfer_tipo_reserva tr ON r.id_tipo_reserva = tr.id_tipo_reserva
        WHERE (fecha_entrada BETWEEN :inicio AND :fin)
           OR (fecha_vuelo_salida BETWEEN :inicio AND :fin)
        ORDER BY fecha_entrada, hora_entrada, fecha_vuelo_salida, hora_vuelo_salida";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':inicio' => $inicio->format('Y-m-d'),
    ':fin' => $fin->format('Y-m-d')
]);
$reservas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        #calendario {
            margin: 20px auto;
            padding: 0 10px;
        }
        .fc-event {
            cursor: pointer;
        }
        .tooltip-inner {
            max-width: 300px;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container my-4">
        <h2 class="text-center mb-4">Calendario de Reservas</h2>
        <div id="calendario"></div>
    </div>

    <!-- Modal para detalles de reserva -->
    <div class="modal fade" id="reservaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reservaModalBody">
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendario');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: '<?= $vista ?>',
                initialDate: '<?= $fecha ?>',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?= json_encode(array_map(function($r) {
                    return [
                        'title' => $r['localizador'],
                        'start' => $r['fecha_entrada'] ?? $r['fecha_vuelo_salida'],
                        'url' => 'detalle_reserva.php?id=' . $r['id_reserva']
                    ];
                }, $reservas)) ?>
            });
            calendar.render();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convertir las reservas PHP a eventos del calendario
            const eventos = [
                <?php foreach ($reservas as $reserva): ?>
                    <?php
                    // Evento para la llegada
                    if ($reserva['fecha_entrada'] && $reserva['hora_entrada']): ?>
                    {
                        title: 'Llegada: ' + <?= json_encode($reserva['localizador']) ?>,
                        start: <?= json_encode($reserva['fecha_entrada'] . 'T' . $reserva['hora_entrada']) ?>,
                        backgroundColor: '#28a745',
                        extendedProps: {
                            tipo: 'llegada',
                            localizador: <?= json_encode($reserva['localizador']) ?>,
                            tipo_reserva: <?= json_encode($reserva['tipo_reserva_descripcion']) ?>,
                            hotel: <?= json_encode($reserva['hotel_nombre']) ?>,
                            vehiculo: <?= json_encode($reserva['vehiculo_descripcion']) ?>,
                            fecha: <?= json_encode($reserva['fecha_entrada']) ?>,
                            hora: <?= json_encode($reserva['hora_entrada']) ?>
                        }
                    },
                    <?php endif; ?>
                    <?php
                    // Evento para la salida
                    if ($reserva['fecha_vuelo_salida'] && $reserva['hora_vuelo_salida']): ?>
                    {
                        title: 'Salida: ' + <?= json_encode($reserva['localizador']) ?>,
                        start: <?= json_encode($reserva['fecha_vuelo_salida'] . 'T' . $reserva['hora_vuelo_salida']) ?>,
                        backgroundColor: '#dc3545',
                        extendedProps: {
                            tipo: 'salida',
                            localizador: <?= json_encode($reserva['localizador']) ?>,
                            tipo_reserva: <?= json_encode($reserva['tipo_reserva_descripcion']) ?>,
                            hotel: <?= json_encode($reserva['hotel_nombre']) ?>,
                            vehiculo: <?= json_encode($reserva['vehiculo_descripcion']) ?>,
                            fecha: <?= json_encode($reserva['fecha_vuelo_salida']) ?>,
                            hora: <?= json_encode($reserva['hora_vuelo_salida']) ?>
                        }
                    },
                    <?php endif; ?>
                <?php endforeach; ?>
            ];

            // Inicializar el calendario
            const calendarEl = document.getElementById('calendario');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: '<?= $vista === 'dia' ? 'timeGridDay' : ($vista === 'semana' ? 'timeGridWeek' : 'dayGridMonth') ?>',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'es',
                events: eventos,
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    const modalBody = document.getElementById('reservaModalBody');
                    modalBody.innerHTML = `
                        <p><strong>Localizador:</strong> ${props.localizador}</p>
                        <p><strong>Tipo de Reserva:</strong> ${props.tipo_reserva}</p>
                        <p><strong>Hotel:</strong> ${props.hotel}</p>
                        <p><strong>Vehículo:</strong> ${props.vehiculo}</p>
                        <p><strong>${props.tipo === 'llegada' ? 'Llegada' : 'Salida'}:</strong> ${props.fecha} ${props.hora}</p>
                    `;
                    const modal = new bootstrap.Modal(document.getElementById('reservaModal'));
                    modal.show();
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
