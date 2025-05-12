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
  protected $fillable = ['descripcion', 'id_zona', 'comision', 'Usuario', 'password'];


  /**
   * Los atributos que deben ocultarse para la serialización.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
  ];

  public function getComisionAttribute()   // accessor → $hotel->Comision siempre decimal
  {
    return $this->attributes['Comision'];
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
