<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;

class DashboardController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth']);
  }



  public function index()
  {
    $hotelId = Auth::user()->id_hotel;

    $stats = [];

    $stats["reservas"] = Reserva::selectRaw("
    DATE_FORMAT(created_at,'%Y-%m') as mes,
    COUNT(*)                         as traslados,
    SUM(comision_hotel)              as total_comision")
      ->where('id_hotel', $hotelId)
      ->groupByRaw("DATE_FORMAT(created_at,'%Y-%m')")
      ->orderBy('mes')
      ->get();

    // Estadísticas para el dashboard
    $stats['reservas_totales'] = Reserva::count();
    $stats['reservas_hoy'] =Reserva::whereDate('fecha_reserva', today())->count();

    return view('hotel.dashboard', compact('stats'));
  }
}
