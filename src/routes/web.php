<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservaController as ReservaHotelController; // alias si ya existe otro


Route::middleware(['auth', 'corporativo'])
  ->prefix('hotel')
  ->name('hotel.')
  ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
      ->name('dashboard');

    Route::resource('reservas', ReservaHotelController::class)
      ->only(['index', 'create', 'store', 'show', 'destroy']);
  });


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aquí registras todas las rutas de la aplicación.
*/

/*──────────────────────────────
|  Rutas públicas
|──────────────────────────────*/
Route::get('/', [HomeController::class, 'index'])->name('index');


Route::middleware('auth')
  ->prefix('viajero')
  ->name('viajero.')
  ->group(function () {
  Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
  });

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

  /* Dashboard y perfil */
  Route::get('/home', [HomeController::class, 'index'])->name('home');

  Route::prefix('perfil')->group(function () {
    Route::get('/', [AuthController::class, 'showCambiarDatos'])->name('perfil');
    Route::get('/editar', [AuthController::class, 'editarPerfil'])->name('perfil.editar');
    Route::post('/', [AuthController::class, 'cambiarDatos']);
  });

  /* Reservas (usuarios normales) */
  Route::resource('reservas', ReservaController::class);

  Route::get('/calendario', [ReservaController::class, 'calendario'])
    ->name('reservas.calendario');   // <-- sin ->middleware('auth') porque el
  //     grupo de arriba ya lo aplica




  Route::get('/reservas/{id}/pdf', [ReservaController::class, 'generarPdf'])->name('reservas.pdf');
  Route::post('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');

  /*───────────────
  |  Bloque ADMIN
  |───────────────*/
  Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

       /* Usuarios (admin crea normal / corporativo / admin) */
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
      Route::get('/', [AdminController::class, 'gestionarUsuarios'])->name('index');
      Route::post('/', [AdminController::class, 'crearUsuario'])->name('crear');
    });

 
    
  
  

    /* Panel admin */
    Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
    Route::get('/menu', [AdminController::class, 'menu'])->name('menu');

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
});
