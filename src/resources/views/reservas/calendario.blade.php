@extends('layouts.app')

@section('title', 'Calendario de Reservas')

@section('styles')
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
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
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js'></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const btnMes = document.getElementById('btnMes');
    const btnSemana = document.getElementById('btnSemana');
    const btnDia = document.getElementById('btnDia');
    const btnHoy = document.getElementById('btnHoy');

    // Eventos del calendario
    const eventos = @json($eventos);

    // Inicializar calendario
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'es',
      headerToolbar: {
      left: 'prev,next',
      center: 'title',
      right: ''
      },
      events: eventos,
      eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
      },
      dayMaxEvents: true,
      height: 'auto',
      firstDay: 1, // Lunes como primer día de la semana
    });

    calendar.render();

    // Cambiar vistas
    btnMes.addEventListener('click', function () {
      calendar.changeView('dayGridMonth');
      setActiveButton(btnMes);
    });

    btnSemana.addEventListener('click', function () {
      calendar.changeView('timeGridWeek');
      setActiveButton(btnSemana);
    });

    btnDia.addEventListener('click', function () {
      calendar.changeView('timeGridDay');
      setActiveButton(btnDia);
    });

    btnHoy.addEventListener('click', function () {
      calendar.today();
    });

    function setActiveButton(activeBtn) {
      [btnMes, btnSemana, btnDia].forEach(btn => {
      btn.classList.remove('active');
      });
      activeBtn.classList.add('active');
    }
    });
  </script>
@endsection
