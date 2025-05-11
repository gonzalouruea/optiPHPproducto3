@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <div class="flex items-center space-x-4">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();"
               class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                Cerrar Sesión
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Reservas Totales</h3>
                <span class="text-3xl font-bold text-blue-600">{{ $stats['reservas_totales'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Reservas de Hoy</h3>
                <span class="text-3xl font-bold text-green-600">{{ $stats['reservas_hoy'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Hoteles</h3>
                <span class="text-3xl font-bold text-yellow-600">{{ $stats['hoteles'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Vehículos</h3>
                <span class="text-3xl font-bold text-purple-600">{{ $stats['vehiculos'] }}</span>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Gestión de Reservas</h3>
                <a href="{{ route('usuario.reservas.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Ver Reservas
                </a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Nueva Reserva</h3>
                <a href="{{ route('usuario.reservas.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    Crear Reserva
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
