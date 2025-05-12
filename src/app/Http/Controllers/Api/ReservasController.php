<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

class ReservasController extends Controller
{
    public function reservasPorZona()
    {
        $reservas = Reserva::with('hotel.zona')->get();

        $totalReservas = $reservas->count();

        $result = $reservas->groupBy('hotel.zona.descripcion') 
            ->map(function ($group, $zona) use ($totalReservas) {
                $numberTraslados = $group->count();
                return [
                    'zona' => $zona,
                    'numero_traslados' => $numberTraslados,
                    'porcentaje' => round(($numberTraslados / $totalReservas) * 100, 2)
                ];
            })
            ->values();

        return response()->json($result);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_zona');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona');
    }

}
