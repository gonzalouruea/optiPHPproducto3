<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
  use HasFactory;

  /**
   * La tabla asociada con el modelo.
   *
   * @var string
   */
  protected $table = 'transfer_precios';

  /**
   * La clave primaria asociada con la tabla.
   *
   * @var string
   */
  protected $primaryKey = 'id_precio';

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
  protected $fillable = ['id_zona', 'id_vehiculo', 'id_tipo_reserva', 'precio'];


  /**
   * Obtiene la zona asociada al precio.
   */
  public function zona()
  {
    return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
  }

  /**
   * Obtiene el vehículo asociado al precio.
   */
  public function vehiculo()
  {
    return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
  }

  /**
   * Obtiene el tipo de reserva asociado al precio.
   */
  public function tipoReserva()
  {
    return $this->belongsTo(TipoReserva::class, 'id_tipo_reserva', 'id_tipo_reserva');
  }
}
