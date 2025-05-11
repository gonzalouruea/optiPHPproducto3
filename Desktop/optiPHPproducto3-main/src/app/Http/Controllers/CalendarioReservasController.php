<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservaCalendario;
use Carbon\Carbon;

class CalendarioReservasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $fechaActual = Carbon::now();
        $mes = $request->query('mes', $fechaActual->month);
        $ano = $request->query('ano', $fechaActual->year);

        // Obtener todas las reservas del mes
        $reservasMes = ReservaCalendario::porMes($mes, $ano)
            ->with(['hotel', 'vehiculo', 'tipoReserva'])
            ->get();

        // Obtener todas las semanas del mes
        $semanas = [];
        $primeroDelMes = Carbon::create($ano, $mes, 1);
        $ultimoDelMes = Carbon::create($ano, $mes, $primeroDelMes->daysInMonth);

        $fecha = $primeroDelMes->copy();
        while ($fecha->lte($ultimoDelMes)) {
            $semanas[] = [
                'inicio' => $fecha->copy(),
                'fin' => $fecha->copy()->addDays(6),
                'reservas' => ReservaCalendario::porSemana($fecha)
                    ->with(['hotel', 'vehiculo', 'tipoReserva'])
                    ->get()
            ];
            $fecha->addDays(7);
        }

        return view('calendario.index', compact('reservasMes', 'semanas', 'mes', 'ano'));
    }

    public function semana(Request $request)
    {
        $fechaInicio = Carbon::parse($request->fecha);
        $reservas = ReservaCalendario::porSemana($fechaInicio)
            ->with(['hotel', 'vehiculo', 'tipoReserva'])
            ->get();

        return view('calendario.semana', compact('reservas', 'fechaInicio'));
    }

    public function dia(Request $request)
    {
        $fecha = Carbon::parse($request->fecha);
        $reservas = ReservaCalendario::porDia($fecha)
            ->with(['hotel', 'vehiculo', 'tipoReserva'])
            ->get();

        return view('calendario.dia', compact('reservas', 'fecha'));
    }
}
