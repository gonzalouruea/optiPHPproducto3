<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'transfer_hotel';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_hotel';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_hotel',
        'id_zona',
        'Usuario',
        'Comision'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'id_hotel' => 'integer',
        'id_zona' => 'integer',
        'Comision' => 'float'
    ];

    /**
     * Obtener todos los hoteles con sus datos completos
     */
    public static function getAllHotels()
    {
        return self::select([
            'id_hotel',
            'id_zona',
            'Usuario',
            'Comision'
        ])->get();
    }

  /**
   * Obtiene la zona asociada al hotel.
   */
  public function zona()
  {
    return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
  }

  /**
   * Obtiene las reservas asociadas al hotel.
   */
  public function reservas()
  {
    return $this->hasMany(Reserva::class, 'id_hotel', 'id_hotel');
  }

  public function viajerosCorporativos()
  {
    return $this->hasMany(Viajero::class, 'id_hotel', 'id_hotel');
  }
}
