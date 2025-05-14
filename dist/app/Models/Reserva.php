<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reserva extends Model
{
  use HasFactory;

  /**
   * La tabla asociada con el modelo.
   *
   * @var string
   */
  protected $table = 'transfer_reservas';

  /**
   * La clave primaria asociada con la tabla.
   *
   * @var string
   */
  protected $primaryKey = 'id_reserva';

  /**
   * Indica si el modelo debe tener marcas de tiempo.
   *
   * @var bool
   */
  public $timestamps = true;

  /**
   * Los nombres de las columnas de fecha.
   *
   * @var array
   */
  protected $dates = [
    'fecha_reserva',
    'fecha_modificacion',
    'fecha_entrada',
    'fecha_vuelo_salida',
    'created_at',
    'updated_at'
  ];

  /**
   * Los atributos que son asignables en masa.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'localizador',
    'id_hotel',
    'id_tipo_reserva',
    'precio',            // ← añadido
    'comision_hotel',
    'email_cliente',
    'fecha_reserva',
    'fecha_modificacion',
    'id_destino',
    'fecha_entrada',
    'hora_entrada',
    'numero_vuelo_entrada',
    'origen_vuelo_entrada',
    'hora_vuelo_salida',
    'fecha_vuelo_salida',
    'num_viajeros',
    'id_vehiculo',
    'hora_recogida',
    'creado_por_admin'
  ];

  /**
   * Obtiene el hotel asociado a la reserva.
   */
  public function hotel()
  {
    return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
  }

  /**
   * Obtiene el tipo de reserva asociado.
   */
  public function tipoReserva()
  {
    return $this->belongsTo(TipoReserva::class, 'id_tipo_reserva', 'id_tipo_reserva');
  }

  /**
   * Obtiene el vehículo asociado a la reserva.
   */
  public function vehiculo()
  {
    return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculo');
  }

  /**
   * Obtiene el usuario (viajero) asociado a la reserva.
   */
  public function usuario()
  {
    // Si mantienes Viajero como modelo de usuarios:
    return $this->belongsTo(Viajero::class, 'email_cliente', 'email');
  }


  /**
   * Verifica si la reserva puede ser modificada por un usuario normal (no admin).
   * Las reservas deben tener más de 48 horas para poder ser modificadas.
   *
   * @return bool
   */
  public function puedeSerModificadaPorUsuario()
  {
    // Si no hay fecha de entrada ni fecha de vuelo, no se puede modificar
    if (empty($this->fecha_entrada) && empty($this->fecha_vuelo_salida)) {
      return false;
    }

    // Calcular la fecha de la reserva (la que sea más próxima)
    $fechaReserva = null;

    if (!empty($this->fecha_entrada)) {
      $fechaReserva = Carbon::parse($this->fecha_entrada . ' ' . $this->hora_entrada);
    } elseif (!empty($this->fecha_vuelo_salida)) {
      $fechaReserva = Carbon::parse($this->fecha_vuelo_salida . ' ' . $this->hora_vuelo_salida);
    }

    // Verificar si faltan más de 48 horas
    if ($fechaReserva) {
      return $fechaReserva->diffInHours(Carbon::now()) > 48;
    }

    return false;
  }

  /**
   * Genera un localizador único para la reserva.
   *
   * @return string
   */
  public static function generarLocalizador()
  {
    do {
      $localizador = strtoupper(substr(uniqid(), -8));
      $existe = self::where('localizador', $localizador)->exists();
    } while ($existe);

    return $localizador;
  }
}
