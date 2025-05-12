<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\TipoReserva;
use App\Models\Precio; // tabla de tarifas por zona/vehículo

class ReservaController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'corporativo']);
  }

  /** Lista de reservas del hotel */
  public function index()
  {
    $hotelId = Auth::user()->id_hotel;
    $reservas = Reserva::with(['vehiculo', 'tipoReserva'])
      ->where('id_hotel', $hotelId)
      ->latest('created_at')
      ->get();

    return view('reservas.index', compact('reservas'));
  }

  /** formulario nueva reserva */
  public function create()
  {
    return view('reservas.create', [
      'vehiculos' => Vehiculo::all(),
      'tiposReserva' => TipoReserva::all(),
    ]);
  }

  /** almacena la reserva + cálculo de comisión */
  public function store(Request $r)
  {
    // Validaciones de los datos del formulario
    $r->validate([
      'id_vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
      'id_tipo_reserva' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
      'num_viajeros' => 'required|integer|min:1',
      // Al menos una fecha/hora
      'fecha_hotel' => 'nullable|date',
      'hora_hotel' => 'nullable',
      'fecha_vuelo' => 'nullable|date',
      'hora_vuelo' => 'nullable',
    ]);

    // 1. Datos básicos
    $hotel = Auth::user()->hotel;

    // Asegúrate de que se pase un precio válido calculado
    $precio = $this->calcularPrecio($hotel->id_zona, $r->id_vehiculo, $r->id_tipo_reserva);

    // 2. Comisión variable según lo pactado
    $comision = round($precio * $hotel->comision / 100, 2);

    // 3. Guardamos la reserva
    Reserva::create([
      'localizador' => Reserva::generarLocalizador(),
      'id_hotel' => $hotel->id_hotel,
      'id_tipo_reserva' => $r->id_tipo_reserva,
      'email_cliente' => Auth::user()->email,
      'fecha_reserva' => now(),
      'fecha_modificacion' => now(),
      'fecha_entrada' => $r->fecha_hotel,
      'hora_entrada' => $r->hora_hotel,
      'fecha_vuelo_salida' => $r->fecha_vuelo,
      'hora_vuelo_salida' => $r->hora_vuelo,
      'num_viajeros' => $r->num_viajeros,
      'id_vehiculo' => $r->id_vehiculo,
      'precio' => $precio, // Asegúrate de que el valor de precio siempre esté presente
      'comision_hotel' => $comision,
      'numero_vuelo_entrada' => $r->numero_vuelo_entrada, // Incluyendo el campo de vuelo
    ]);

    return back()->with('success', 'Reserva creada correctamente.');
  }

  /** Muestra detalle */
  public function show(Reserva $reserva)
  {
    $this->autorizar($reserva);
    return view('reservas.show', compact('reserva'));
  }

  /** Eliminar (cancelar) */
  public function destroy(Reserva $reserva)
  {
    $this->autorizar($reserva);
    $reserva->delete();

    return back()->with('success', 'Reserva eliminada');
  }

  /* ··· helpers ··· */
  private function autorizar(Reserva $reserva)
  {
    if ($reserva->id_hotel !== Auth::user()->id_hotel) {
      abort(403, 'Entra por aqui');
    }
  }

  private function calcularPrecio($idZona, $idVehiculo, $idTipo)
  {
    // Asegúrate de que siempre se devuelva un precio válido
    return Precio::where([
      'id_zona' => $idZona,
      'id_vehiculo' => $idVehiculo,
      'id_tipo_reserva' => $idTipo
    ])->value('precio') ?? 0; // Si no se encuentra, devolver 0 por defecto
  }

}
