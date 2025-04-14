<?php
session_start();
require_once 'conexion.php';

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Obtener reservas para el calendario
$reservas = $db->query("
    SELECT r.*, h.nombre as hotel, v.descripcion as vehiculo 
    FROM transfer_reservas r
    JOIN transfer_hotel h ON r.id_hotel = h.id_hotel
    JOIN transfer_vehiculo v ON r.id_vehiculo = v.id_vehiculo
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Calendario de Reservas</h2>
        <a href="nueva_reserva.php" class="btn btn-primary">Nueva Reserva</a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="day-tab" data-bs-toggle="tab" href="#day">Diario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="week-tab" data-bs-toggle="tab" href="#week">Semanal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="month-tab" data-bs-toggle="tab" href="#month">Mensual</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal para detalles de reserva -->
<div class="modal fade" id="reservaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="editarReserva">Editar</button>
                <button type="button" class="btn btn-danger" id="cancelarReserva">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const reservas = <?= json_encode($reservas) ?>;
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: reservas.map(reserva => {
            let title = reserva.localizador;
            let start, end;
            
            if (reserva.fecha_entrada) {
                start = reserva.fecha_entrada + 'T' + reserva.hora_entrada;
                title += ' (Llegada)';
            } else if (reserva.fecha_vuelo_salida) {
                start = reserva.fecha_vuelo_salida + 'T' + reserva.hora_recogida;
                title += ' (Salida)';
            }
            
            return {
                id: reserva.id_reserva,
                title: title,
                start: start,
                extendedProps: reserva
            };
        }),
        eventClick: function(info) {
            const reserva = info.event.extendedProps;
            let content = `
                <h6>Localizador: ${reserva.localizador}</h6>
                <p><strong>Cliente:</strong> ${reserva.email_cliente}</p>
                <p><strong>Hotel:</strong> ${reserva.hotel}</p>
                <p><strong>Vehículo:</strong> ${reserva.vehiculo}</p>
                <p><strong>Viajeros:</strong> ${reserva.num_viajeros}</p>
            `;
            
            if (reserva.fecha_entrada) {
                content += `
                    <hr>
                    <h5>Datos de Llegada</h5>
                    <p><strong>Fecha:</strong> ${reserva.fecha_entrada}</p>
                    <p><strong>Hora:</strong> ${reserva.hora_entrada}</p>
                    <p><strong>Vuelo:</strong> ${reserva.numero_vuelo_entrada} desde ${reserva.origen_vuelo_entrada}</p>
                `;
            }
            
            if (reserva.fecha_vuelo_salida) {
                content += `
                    <hr>
                    <h5>Datos de Salida</h5>
                    <p><strong>Fecha Vuelo:</strong> ${reserva.fecha_vuelo_salida}</p>
                    <p><strong>Hora Vuelo:</strong> ${reserva.hora_vuelo_salida}</p>
                    <p><strong>Hora Recogida:</strong> ${reserva.hora_recogida}</p>
                    <p><strong>Vuelo:</strong> ${reserva.numero_vuelo_salida}</p>
                `;
            }
            
            document.getElementById('modalBody').innerHTML = content;
            document.getElementById('editarReserva').onclick = function() {
                window.location.href = 'editar_reserva.php?id=' + reserva.id_reserva;
            };
            document.getElementById('cancelarReserva').onclick = function() {
                if (confirm('¿Está seguro de cancelar esta reserva?')) {
                    window.location.href = 'cancelar_reserva.php?id=' + reserva.id_reserva;
                }
            };
            
            const modal = new bootstrap.Modal(document.getElementById('reservaModal'));
            modal.show();
        }
    });
    
    calendar.render();
    
    // Cambiar vista según pestaña seleccionada
    document.getElementById('day-tab').addEventListener('click', function() {
        calendar.changeView('timeGridDay');
    });
    
    document.getElementById('week-tab').addEventListener('click', function() {
        calendar.changeView('timeGridWeek');
    });
    
    document.getElementById('month-tab').addEventListener('click', function() {
        calendar.changeView('dayGridMonth');
    });
});
</script>
</body>
</html>