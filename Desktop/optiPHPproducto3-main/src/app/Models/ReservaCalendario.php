<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaCalendario extends Model
{
    use HasFactory;

    protected $table = 'transfer_reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_reserva',
        'id_hotel',
        'id_vehiculo',
        'id_tipo_reserva',
        'fecha_entrada',
        'fecha_salida',
        'origen_vuelo_entrada',
        'origen_vuelo_salida',
        'localizador',
        'email_cliente'
    ];

    protected $casts = [
        'fecha_entrada' => 'datetime',
        'fecha_salida' => 'datetime',
        'localizador' => 'string'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function tipoReserva()
    {
        return $this->belongsTo(TipoReserva::class, 'id_tipo_reserva');
    }

    public function scopePorMes($query, $mes, $ano)
    {
        return $query->whereYear('fecha_entrada', $ano)
                    ->whereMonth('fecha_entrada', $mes);
    }

    public function scopePorSemana($query, $fechaInicio)
    {
        return $query->whereBetween('fecha_entrada', [
            $fechaInicio,
            $fechaInicio->copy()->addDays(6)
        ]);
    }

    public function scopePorDia($query, $fecha)
    {
        return $query->whereDate('fecha_entrada', $fecha);
    }
}
