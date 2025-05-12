<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Viajero extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * La tabla asociada con el modelo.
   *
   * @var string
   */
  protected $table = 'transfer_viajeros';

  /**
   * La clave primaria asociada con la tabla.
   *
   * @var string
   */
  protected $primaryKey = 'id_viajero';

  /**
   * Indica si el modelo debe tener marcas de tiempo.
   *
   * @var bool
   */
  public $timestamps = true;

  /**
   * Los atributos que son asignables en masa.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'nombre',
    'apellido1',
    'apellido2',
    'direccion',
    'codigoPostal',
    'ciudad',
    'pais',
    'email',
    'password',
    'rol',
    'id_hotel',

  ];

  /**
   * Los atributos que deben ocultarse para la serialización.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Los atributos que deben convertirse.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  /**
   * Obtiene las reservas asociadas a este viajero.
   */
  public function reservas()
  {
    return $this->hasMany(Reserva::class, 'email_cliente', 'email');
  }

  /**
   * Verifica si el usuario es administrador.
   *
   * @return bool
   */
  public function esAdmin()
  {
    return $this->rol === 'admin';
  }

  public function hotel()
  {
    return $this->belongsTo(Hotel::class, 'id_hotel', 'id_hotel');
  }


  /**
   * Verifica si el usuario es corporativo.
   *
   * @return bool
   */
  public function esCorporativo()
  {
    return $this->rol === 'corporativo';
  }

  /**
   * Verifica si el usuario es un usuario normal.
   *
   * @return bool
   */
  public function esUsuarioNormal()
  {
    return $this->rol === 'usuario';
  }

  /**
   * Obtiene el nombre completo del viajero.
   *
   * @return string
   */
  public function getNombreCompletoAttribute()
  {
    return "{$this->nombre} {$this->apellido1} {$this->apellido2}";
  }
}
