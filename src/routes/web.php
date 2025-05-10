<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página de bienvenida
Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

// Ruta principal después del login
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registro
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Dashboard y Perfil de Usuario
    |--------------------------------------------------------------------------
    */
    
    // Dashboard principal
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Gestión de perfil de usuario
    Route::prefix('perfil')->group(function () {
        Route::get('/', [AuthController::class, 'showCambiarDatos'])->name('perfil');
        Route::get('/editar', [AuthController::class, 'editarPerfil'])->name('perfil.editar');
        Route::post('/', [AuthController::class, 'cambiarDatos']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | Gestión de Reservas
    |--------------------------------------------------------------------------
    */
    
    // Rutas de reservas con Resource Controller
    Route::resource('reservas', ReservaController::class);
    
    // Rutas adicionales de reservas
    Route::get('/calendario', [ReservaController::class, 'calendario'])->name('reservas.calendario');
    Route::get('/reservas/{id}/pdf', [ReservaController::class, 'generarPdf'])->name('reservas.pdf');
    Route::post('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
    
    /*
    |--------------------------------------------------------------------------
    | Rutas de Administración
    |--------------------------------------------------------------------------
    */
    
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        
        // Panel de administración
        Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
        Route::get('/menu', [AdminController::class, 'menu'])->name('menu');
        
        // Gestión de hoteles
        Route::prefix('hoteles')->name('hoteles.')->group(function () {
            Route::get('/', [AdminController::class, 'gestionarHoteles'])->name('index');
            Route::post('/', [AdminController::class, 'crearHotel'])->name('crear');
            Route::put('/{id}', [AdminController::class, 'actualizarHotel'])->name('actualizar');
            Route::delete('/{id}', [AdminController::class, 'eliminarHotel'])->name('eliminar');
        });
        
        // Gestión de vehículos
        Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
            Route::get('/', [AdminController::class, 'gestionarVehiculos'])->name('index');
            Route::post('/', [AdminController::class, 'crearVehiculo'])->name('crear');
            Route::put('/{id}', [AdminController::class, 'actualizarVehiculo'])->name('actualizar');
            Route::delete('/{id}', [AdminController::class, 'eliminarVehiculo'])->name('eliminar');
        });
        
        // Gestión de zonas
        Route::prefix('zonas')->name('zonas.')->group(function () {
            Route::get('/', [AdminController::class, 'gestionarZonas'])->name('index');
            Route::post('/', [AdminController::class, 'crearZona'])->name('crear');
            Route::put('/{id}', [AdminController::class, 'actualizarZona'])->name('actualizar');
            Route::delete('/{id}', [AdminController::class, 'eliminarZona'])->name('eliminar');
        });
        
        // Gestión de tipos de reserva
        Route::prefix('tipos-reserva')->name('tipos-reserva.')->group(function () {
            Route::get('/', [AdminController::class, 'gestionarTipos'])->name('index');
            Route::post('/', [AdminController::class, 'crearTipo'])->name('crear');
            Route::put('/{id}', [AdminController::class, 'actualizarTipo'])->name('actualizar');
            Route::delete('/{id}', [AdminController::class, 'eliminarTipo'])->name('eliminar');
        });
        
        // Gestión de precios
        Route::prefix('precios')->name('precios.')->group(function () {
            Route::get('/', [AdminController::class, 'gestionarPrecios'])->name('index');
            Route::post('/', [AdminController::class, 'crearPrecio'])->name('crear');
            Route::put('/{id}', [AdminController::class, 'actualizarPrecio'])->name('actualizar');
            Route::delete('/{id}', [AdminController::class, 'eliminarPrecio'])->name('eliminar');
        });
        
        // Reportes y estadísticas
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/reservas', [AdminController::class, 'reporteReservas'])->name('reservas');
            Route::get('/ingresos', [AdminController::class, 'reporteIngresos'])->name('ingresos');
            Route::get('/hoteles', [AdminController::class, 'reporteHoteles'])->name('hoteles');
        });
    });
});
