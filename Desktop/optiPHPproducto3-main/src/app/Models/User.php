<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  protected $table = 'transfer_viajeros';
  protected $primaryKey = 'id_viajero';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'nombre',
    'email',
    'password',
    'rol'
  ];

  protected $casts = [
    'password' => 'hashed',
    'rol' => 'enum:admin,usuario,corporativo'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Verifica si el usuario es administrador
   *
   * @return bool
   */
  public function esAdmin()
  {
    return $this->rol === 'admin';
  }

  public function getAuthPassword()
  {
    return $this->password;
  }

  public function validateEmail($email)
  {
    return !static::where('email', $email)->exists();
  }

  public function getAuthIdentifier()
  {
    return $this->{$this->primaryKey};
  }

  public function getNameAttribute()
  {
    return $this->nombre;
  }
}
