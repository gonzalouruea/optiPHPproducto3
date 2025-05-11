<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservaController;

Route::prefix('admin')->name('admin.')->group(function () {
    /* Panel admin */
    Route::get('/panel', [AdminController::class, 'panel'])->name('panel');

    /* Reservas (admin) */
    Route::prefix('reservas')->name('reservas.')->group(function () {
        Route::get('/', [ReservaController::class, 'index'])->name('index');
        Route::get('/create', [ReservaController::class, 'create'])->name('create');
        Route::post('/', [ReservaController::class, 'store'])->name('store');
        Route::get('/{reserva}', [ReservaController::class, 'show'])->name('show');
        Route::get('/{reserva}/edit', [ReservaController::class, 'edit'])->name('edit');
        Route::put('/{reserva}', [ReservaController::class, 'update'])->name('update');
        Route::delete('/{reserva}', [ReservaController::class, 'destroy'])->name('destroy');
        Route::get('/calendario', [AdminController::class, 'calendario'])->name('calendario');
    });

    /* Hoteles */
    Route::prefix('hoteles')->name('hoteles.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarHoteles'])->name('index');
        Route::post('/', [AdminController::class, 'crearHotel'])->name('crear');
        Route::put('/{id}', [AdminController::class, 'actualizarHotel'])->name('actualizar');
        Route::delete('/{id}', [AdminController::class, 'eliminarHotel'])->name('eliminar');
    });

    /* Vehículos */
    Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarVehiculos'])->name('index');
        Route::post('/', [AdminController::class, 'crearVehiculo'])->name('crear');
        Route::put('/{id}', [AdminController::class, 'actualizarVehiculo'])->name('actualizar');
        Route::delete('/{id}', [AdminController::class, 'eliminarVehiculo'])->name('eliminar');
    });

    /* Tipos de reserva */
    Route::prefix('tipos-reserva')->name('tipos-reserva.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarTipos'])->name('index');
        Route::post('/', [AdminController::class, 'crearTipo'])->name('crear');
        Route::put('/{id}', [AdminController::class, 'actualizarTipo'])->name('actualizar');
        Route::delete('/{id}', [AdminController::class, 'eliminarTipo'])->name('eliminar');
    });
});
