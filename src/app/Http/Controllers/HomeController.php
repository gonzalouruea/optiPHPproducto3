<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;        // <-- Import de Carbon
use App\Models\Reserva;
use App\Models\Hotel;
use App\Models\Vehiculo;
use App\Models\Viajero;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Muestra la página de inicio después del login.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    // Fecha de hoy en formato YYYY-MM-DD
    $hoy = Carbon::today()->toDateString();

    // Total de reservas
    $total = Reserva::count();

    // Reservas cuya llegada o salida está programada para hoy
    $reservasHoy = Reserva::where(function ($q) use ($hoy) {
      $q->whereDate('fecha_entrada', $hoy)
        ->orWhereDate('fecha_vuelo_salida', $hoy);
    })->count();

    return view('home', [
      'reservas_totales' => $total,
      'reservas_hoy' => $reservasHoy,
      // … otros datos que pases a la vista …
    ]);
  }

  /**
   * Muestra la página de bienvenida.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function welcome()
  {
    return view('welcome');
  }
}
