<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoReserva extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'transfer_tipo_reserva';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_tipo_reserva';

    /**
     * Indica si el modelo debe tener marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Descripción',
    ];

    /**
     * Obtiene las reservas asociadas a este tipo de reserva.
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_tipo_reserva', 'id_tipo_reserva');
    }
}
