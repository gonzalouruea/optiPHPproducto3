<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'transfer_vehiculo';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_vehiculo';

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
        'email_conductor',
        'password'
    ];

    /**
     * Obtiene las reservas asociadas a este vehículo.
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_vehiculo', 'id_vehiculo');
    }
}
