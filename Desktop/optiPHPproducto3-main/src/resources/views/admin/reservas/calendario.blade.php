@extends('layouts.admin')

@section('title', 'Calendario de Trayectos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Calendario de Trayectos</h4>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del trayecto -->
<div class="modal fade" id="detallesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Trayecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detallesContenido"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        .calendar-container {
            width: 100%;
            height: 600px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Estilos para los eventos */
        .fc-event {
            border-radius: 4px;
            padding: 5px;
            margin: 2px;
        }

        /* Estilos para los eventos de entrada */
        .fc-event[data-event-type="entrada"] {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        /* Estilos para los eventos de salida */
        .fc-event[data-event-type="salida"] {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Scripts de FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Iniciando FullCalendar');
            
            var calendarEl = document.getElementById('calendar');
            
            if (!calendarEl) {
                console.error('No se encontró el elemento del calendario');
                return;
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($eventos),
                eventDidMount: function(info) {
                    console.log('Evento montado:', info.event.title);
                    // Añadir atributo data-event-type para estilizar
                    info.el.dataset.eventType = info.event.extendedProps.tipo;
                    info.el.style.cursor = 'pointer';
                },
                eventClick: function(info) {
                    console.log('Evento clickeado:', info.event.title);
                    const evento = info.event;
                    
                    // Mostrar modal con detalles
                    const contenido = `
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Localizador: ${evento.extendedProps.localizador}</h5>
                                <p><strong>Tipo de Trayecto:</strong> ${evento.extendedProps.tipo === 'entrada' ? 'Entrada' : 'Salida'}</p>
                                <p><strong>Hotel:</strong> ${evento.extendedProps.hotel}</p>
                                <p><strong>Tipo Reserva:</strong> ${evento.extendedProps.tipo_reserva}</p>
                                <p><strong>Vehículo:</strong> ${evento.extendedProps.vehiculo}</p>
                                <p><strong>Número Pasajeros:</strong> ${evento.extendedProps.pasajeros}</p>
                                <p><strong>Email Cliente:</strong> ${evento.extendedProps.email_cliente}</p>
                                <p><strong>Estado:</strong> <span class="badge bg-success">${evento.extendedProps.estado}</span></p>
                                <p><strong>Viajero:</strong> ${evento.extendedProps.viajero}</p>
                                <p><strong>Zona:</strong> ${evento.extendedProps.zona}</p>
                            </div>
                        </div>
                    `;

                    $('#detallesContenido').html(contenido);
                    const modal = new bootstrap.Modal(document.getElementById('detallesModal'));
                    modal.show();
                }
            });

            calendar.render();
            console.log('Calendario renderizado');
        });
    </script>
@endpush
