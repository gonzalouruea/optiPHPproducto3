<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        // Estadísticas para el dashboard
        $stats = [
            'reservas_totales' => Reserva::count(),
            'reservas_hoy' => Reserva::whereDate('fecha_reserva', today())->count(),
            'hoteles' => Hotel::count(),
            'vehiculos' => Vehiculo::count(),
            'usuarios' => Viajero::where('rol', 'usuario')->count(),
        ];

        return view('auth.dashboard', compact('stats'));
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
