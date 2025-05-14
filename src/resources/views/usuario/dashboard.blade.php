@extends('layouts.app')

@section('title', 'Panel de Usuario')

@section('content')
  <div class="row mb-4">
    <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Panel de Usuario</h4>
      </div>
      <div class="card-body">
      <p class="lead">Bienvenido a la aplicación de viajes, {{ Auth::user()->nombre }}.</p>
      <p>Desde aquí puedes gestionar todos los aspectos del sistema de reservas.</p>
      </div>
    </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 mb-4">
    <div class="card bg-primary text-white shadow">
      <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
        <h5 class="card-title">Reservas Totales</h5>
        <h2 class="mb-0">{{ $stats["reservas_totales"] }}</h2>

        </div>
        <i class="fas fa-calendar-check fa-3x opacity-50"></i>
      </div>
      </div>
      <div class="card-footer bg-primary-dark">
      <a href="{{ route('reservas.index') }}" class="text-white text-decoration-none">
        <small>Ver todas <i class="fas fa-arrow-right"></i></small>
      </a>
      </div>
    </div>
    </div>

    <div class="col-md-3 mb-4">
    <div class="card bg-success text-white shadow">
      <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
        <h5 class="card-title">Reservas Hoy</h5>
        <h2 class="mb-0">{{ $stats["reservas_hoy"] }}</h2>
        </div>
        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
      </div>
      </div>
      <div class="card-footer bg-success-dark">
      <a href="{{ route('reservas.calendario') }}" class="text-white text-decoration-none">
        <small>Ver calendario <i class="fas fa-arrow-right"></i></small>
      </a>
      </div>
    </div>
    </div>

  </div>

  <div class="row">
    <div class="col-md-12">
    <div class="card shadow">
      <div class="card-header bg-secondary text-white">
      <h5 class="mb-0">Acciones Rápidas</h5>
      </div>
      <div class="card-body">
      <div class="row">
        <div class="col-md-3 mb-3">
        <a href="{{ route('reservas.create') }}" class="btn btn-primary w-100 py-3">
          <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
          Nueva Reserva
        </a>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>
@endsection
