<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioReservaController;
use App\Http\Controllers\UsuarioController;

/*──────────────────────────────
|  Rutas públicas
|──────────────────────────────*/
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

/*──────────────────────────────
|  Autenticación
|──────────────────────────────*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

/*──────────────────────────────
|  Rutas protegidas (requieren login)
|──────────────────────────────*/
Route::middleware('auth')->group(function () {
    /* Panel de Usuario */
    Route::prefix('usuario')->name('usuario.')->group(function () {
        Route::get('/dashboard', [UsuarioController::class, 'dashboard'])->name('dashboard');
        Route::get('/editar-perfil', [UsuarioController::class, 'editarPerfil'])->name('editar-perfil');
        Route::post('/actualizar-perfil', [UsuarioController::class, 'actualizarPerfil'])->name('actualizar-perfil');
        Route::get('/crear-reserva', [UsuarioController::class, 'crearReserva'])->name('crear-reserva');
        Route::post('/store-reserva', [UsuarioController::class, 'storeReserva'])->name('store-reserva');

        /* Reservas (usuarios normales) */
        Route::prefix('reservas')->name('reservas.')->group(function () {
            Route::get('/', [App\Http\Controllers\UsuarioReservaController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\UsuarioReservaController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\UsuarioReservaController::class, 'store'])->name('store');
            Route::get('/{reserva}', [App\Http\Controllers\UsuarioReservaController::class, 'show'])->name('show');
            Route::get('/{reserva}/edit', [App\Http\Controllers\UsuarioReservaController::class, 'edit'])->name('edit');
            Route::put('/{reserva}', [App\Http\Controllers\UsuarioReservaController::class, 'update'])->name('update');
            Route::delete('/{reserva}', [App\Http\Controllers\UsuarioReservaController::class, 'destroy'])->name('destroy');
        });

        Route::get('/reservas/{id}/pdf', [ReservaController::class, 'generarPdf'])->name('pdf');
        Route::post('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar'])->name('cancelar');
    });
});

/*───────────────
|  Rutas ADMIN
|───────────────*/
Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    /* Panel admin */
    Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
    Route::get('/menu', [AdminController::class, 'menu'])->name('menu');
    Route::get('/editar-perfil', [AdminController::class, 'editarPerfil'])->name('editar-perfil');
    Route::get('/calendario', [ReservaController::class, 'calendario'])->name('calendario');
    Route::post('/actualizar-perfil', [AdminController::class, 'actualizarPerfil'])->name('actualizar-perfil');

    /* Usuarios (admin crea normal / corporativo / admin) */
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarUsuarios'])->name('index');
        Route::post('/', [AdminController::class, 'crearUsuario'])->name('crear');
    });

    /* Reservas (admin) */
    Route::prefix('reservas')->name('reservas.')->group(function () {
        Route::get('/', [ReservaController::class, 'index'])->name('admin.reservas.index');
        Route::get('/create', [ReservaController::class, 'create'])->name('create');
        Route::post('/', [ReservaController::class, 'store'])->name('store');
        Route::get('/{reserva}', [ReservaController::class, 'show'])->name('show');
        Route::get('/{reserva}/edit', [ReservaController::class, 'edit'])->name('edit');
        Route::put('/{reserva}', [ReservaController::class, 'update'])->name('update');
        Route::delete('/{reserva}', [ReservaController::class, 'destroy'])->name('destroy');
    });

    /* Calendario de reservas */
    Route::prefix('calendario')->name('calendario.')->group(function () {
        Route::get('/', [ReservaController::class, 'calendario'])->name('index');
        Route::get('/trayecto/{id}', [ReservaController::class, 'trayecto'])->name('trayecto');
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

    /* Zonas */
    Route::prefix('zonas')->name('zonas.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarZonas'])->name('index');
        Route::post('/', [AdminController::class, 'crearZona'])->name('crear');
        Route::put('/{id}', [AdminController::class, 'actualizarZona'])->name('actualizar');
        Route::delete('/{id}', [AdminController::class, 'eliminarZona'])->name('eliminar');
    });

    /* Tipos de reserva */
    Route::prefix('tipos-reserva')->name('tipos-reserva.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarTipos'])->name('index');
        Route::post('/', [AdminController::class, 'crearTipo'])->name('crear');
        Route::put('/{id}', [AdminController::class, 'actualizarTipo'])->name('actualizar');
        Route::delete('/{id}', [AdminController::class, 'eliminarTipo'])->name('eliminar');
    });

    /* Precios */
    Route::prefix('precios')->name('precios.')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarPrecios'])->name('index');
        Route::post('/', [AdminController::class, 'crearPrecio'])->name('crear');
        Route::put('/{id}', [AdminController::class, 'actualizarPrecio'])->name('actualizar');
        Route::delete('/{id}', [AdminController::class, 'eliminarPrecio'])->name('eliminar');
    });

    /* Reportes */
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/reservas', [AdminController::class, 'reporteReservas'])->name('reservas');
        Route::get('/ingresos', [AdminController::class, 'reporteIngresos'])->name('ingresos');
        Route::get('/hoteles', [AdminController::class, 'reporteHoteles'])->name('hoteles');
    });
});
        