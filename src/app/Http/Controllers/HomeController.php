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

    $user = auth()->user();
    
    $stats = [
      'reservas_totales' => $total,
      'reservas_hoy' => $reservasHoy
    ];
    if ($user->esCorporativo()) {
      // el blade que tienes en resources/views/hotel/dashboard.blade.php
      $view = 'hotel.dashboard';
      $stats['comisiones_por_mes'] = Reserva::selectRaw("
        DATE_FORMAT(created_at,'%Y-%m') as mes,
        COUNT(*)                         as traslados,
        SUM(comision_hotel)              as total_comision")
        ->where('id_hotel', $user->id_hotel)
        ->groupByRaw("DATE_FORMAT(created_at,'%Y-%m')")
        ->orderBy('mes')
        ->get();

    } elseif ($user->esUsuarioNormal()) {
      // el blade que tienes en resources/views/viajero/dashboard.blade.php
      $view = 'usuario.dashboard';

    } elseif ($user->esAdmin()) {
      // el blade que tienes en resources/views/admin/dashboard.blade.php
      $view = 'admin.panel';
      $stats['hoteles'] = Hotel::count();
      $stats['vehiculos'] = Vehiculo::count();
      $stats['usuarios'] = Viajero::where('rol', 'usuario')->count();
    }

    return view($view, compact('stats'));
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
