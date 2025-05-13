@extends('layouts.app')

@section('title', 'Calendario de Reservas')

@section('styles')
  <!-- FullCalendar v5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet" />
  <style>
    #calendar {
    max-width: 1200px;
    margin: 0 auto;
    }

    .fc-event {
    cursor: pointer;
    }
  </style>
@endsection

@section('content')
  <div class="card shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Calendario de Reservas</h4>
    <a href="{{ route('reservas.create') }}" class="btn btn-light">
      <i class="fas fa-plus"></i> Nueva Reserva
    </a>
    </div>
    <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-4">
      <div class="btn-group" role="group">
        <button type="button" id="btnMes" class="btn btn-outline-primary active">Mes</button>
        <button type="button" id="btnSemana" class="btn btn-outline-primary">Semana</button>
        <button type="button" id="btnDia" class="btn btn-outline-primary">Día</button>
      </div>
      </div>
      <div class="col-md-8 text-end">
      <div class="d-flex justify-content-end align-items-center">
        <div class="me-3">
        <span class="badge bg-success">Llegada</span>
        <span class="badge bg-danger">Salida</span>
        </div>
        <button type="button" id="btnHoy" class="btn btn-sm btn-outline-secondary">Hoy</button>
      </div>
      </div>
    </div>

    <div id="calendar"></div>
    </div>
  </div>
@endsection

@section('scripts')
  <!-- FullCalendar v5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
  <!-- Todo en un único fichero de locales -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Elementos UI
    const calendarEl = document.getElementById('calendar');
    const btnMes = document.getElementById('btnMes');
    const btnSemana = document.getElementById('btnSemana');
    const btnDia = document.getElementById('btnDia');
    const btnHoy = document.getElementById('btnHoy');

    // Tus eventos inyectados desde el controlador
    const eventos = @json($eventos);

    // Inicializar FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'es',
      headerToolbar: false,    // quitamos la toolbar nativa
      events: eventos,
      eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
      },
      dayMaxEvents: true,
      height: 'auto',
      firstDay: 1               // lunes como primer día
    });

    calendar.render();

    // Helper para marcar botón activo
    function setActive(btn) {
      [btnMes, btnSemana, btnDia].forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    }

    // Asignamos nuestros botones
    btnMes.addEventListener('click', () => { calendar.changeView('dayGridMonth'); setActive(btnMes); });
    btnSemana.addEventListener('click', () => { calendar.changeView('timeGridWeek'); setActive(btnSemana); });
    btnDia.addEventListener('click', () => { calendar.changeView('timeGridDay'); setActive(btnDia); });
    btnHoy.addEventListener('click', () => calendar.today());
    });
  </script>
@endsection
