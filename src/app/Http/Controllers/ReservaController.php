<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;   // añade arriba

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Viajero;   // o User, según tu diseño
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\TipoReserva;
use App\Models\Precio; // tabla de tarifas por zona/vehículo

class ReservaController extends Controller
{
  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      // Permite el acceso a los usuarios con roles 'admin', 'corporativo' o 'usuario'
      if (Auth::check() && in_array(Auth::user()->rol, ['admin', 'corporativo', 'usuario'])) {
        return $next($request);
      }

      abort(403, 'Acceso no autorizado');
    });
  }

  /** Lista de reservas del hotel */
  /**
   * Lista de reservas según rol.
   */
  public function index()
  {
    $user = Auth::user();

    if ($user->esAdmin()) {
      // Admin ve todas
      $reservas = Reserva::with(['vehiculo', 'tipoReserva', 'hotel'])
        ->latest('created_at')
        ->get();
    } elseif ($user->esCorporativo()) {
      // Corporativo ve solo las de su hotel
      $reservas = Reserva::with(['vehiculo', 'tipoReserva'])
        ->where('id_hotel', $user->id_hotel)
        ->latest('created_at')
        ->get();
    } else {
      // Usuario normal ve solo las suyas
      $reservas = Reserva::with(['vehiculo', 'tipoReserva'])
        ->where('email_cliente', $user->email)
        ->latest('created_at')
        ->get();
    }

    return view('reservas.index', compact('reservas'));
  }


  public function calendario()
  {
    $user = auth()->user();

    // 1) Filtrar según rol
    if ($user->rol === 'corporativo') {
      $query = Reserva::where('id_hotel', $user->id_hotel);
    } elseif ($user->rol === 'admin') {
      $query = Reserva::query();
    } else {
      $query = Reserva::where('email_cliente', $user->email);
    }

    // 2) Sacamos las llegadas
    $llegadas = (clone $query)
      ->whereNotNull('fecha_entrada')
      ->select([
        \DB::raw("CONCAT('l-', id_reserva) as id"),
        \DB::raw("CONCAT(localizador,' · ',num_viajeros,' pax (Llegada)') as title"),
        \DB::raw("CONCAT(fecha_entrada,'T',hora_entrada) as start"),
      ])->get();

    // 2) Sacamos salidas
    $salidas = (clone $query)
      ->whereNotNull('fecha_vuelo_salida')
      ->select([
        \DB::raw("CONCAT('s-', id_reserva) as id"),
        \DB::raw("CONCAT(localizador,' · ',num_viajeros,' pax (Salida)') as title"),
        \DB::raw("CONCAT(fecha_vuelo_salida,'T',hora_vuelo_salida) as start"),
      ])->get();

    // 3) Mezclamos y enviamos
    $eventos = $llegadas->merge($salidas);

    return view('reservas.calendario', compact('eventos'));

  }


  /** formulario nueva reserva */
  // app/Http/Controllers/ReservaController.php

  public function create()
  {
    $tiposReserva = [];

    // 1) Si no hay tipos en BD, creamos los tres de serie:
    if (TipoReserva::count() === 0) {
      TipoReserva::insert([
        ['Descripción' => 'Aeropuerto → Hotel'],
        ['Descripción' => 'Hotel → Aeropuerto'],
        ['Descripción' => 'Ida y Vuelta'],
      ]);
    }

    // 2) Recuperamos todos (ahora incluye los 3 básicos + los que haya creado el admin)
    $tiposReserva = TipoReserva::all();
    $vehiculos = Vehiculo::all();

    // 3) Si es admin, también pasamos la lista de usuarios para asignar reserva
    $usuarios = Auth::user()->esAdmin()
      ? Viajero::select('id_viajero', 'email')->get()
      : collect();

    return view('reservas.create', compact('vehiculos', 'tiposReserva', 'usuarios'));
  }





  /** almacena la reserva + cálculo de comisión */
  // En ReservaController@store

  public function store(Request $r)
  {
    // 1) Validaciones
    $r->validate([
      'id_vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
      'id_tipo_reserva' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
      'num_viajeros' => 'required|integer|min:1',
      'fecha_entrada' => 'nullable|date',
      'hora_entrada' => 'nullable|date_format:H:i',
      'fecha_vuelo_salida' => 'nullable|date',
      'hora_vuelo_salida' => 'nullable|date_format:H:i',
      'numero_vuelo_entrada' => 'nullable|string|max:50',
      'origen_vuelo_entrada' => 'nullable|string|max:100',
      'hora_recogida' => 'nullable|date_format:H:i',
    ]);

    // 2) Precio y comisión (siempre definimos ambas variables)
    $user = Auth::user();
    $hotel = $user->rol === 'corporativo' ? $user->hotel : null;

    if ($hotel && $hotel->id_zona) {
      // calculas con la zona del hotel
      $precio = $this->calcularPrecio($hotel->id_zona, $r->id_vehiculo, $r->id_tipo_reserva);
      $comision = round($precio * $hotel->comision / 100, 2);
    } else {
      // precio por defecto para admin/usuario
      $precio = $this->calcularPrecioDefault($r->id_vehiculo, $r->id_tipo_reserva);
      $comision = 0;
    }

    // 3) Creación
    Reserva::create([
      'localizador' => Reserva::generarLocalizador(),
      'id_hotel' => $hotel->id_hotel ?? null,
      'id_tipo_reserva' => $r->id_tipo_reserva,
      'email_cliente' => $user->email,
      'fecha_reserva' => now(),
      'fecha_modificacion' => now(),

      // **Campos de llegada / salida, igual que en el form**
      'fecha_entrada' => $r->fecha_entrada,
      'hora_entrada' => $r->hora_entrada,
      'fecha_vuelo_salida' => $r->fecha_vuelo_salida,
      'hora_vuelo_salida' => $r->hora_vuelo_salida,
      'numero_vuelo_entrada' => $r->numero_vuelo_entrada,
      'origen_vuelo_entrada' => $r->origen_vuelo_entrada,
      'hora_recogida' => $r->hora_recogida,

      // resto de datos
      'num_viajeros' => $r->num_viajeros,
      'id_vehiculo' => $r->id_vehiculo,
      'precio' => $precio,
      'comision_hotel' => $comision,
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

  // Método para calcular el precio predeterminado cuando no hay hotel
  private function calcularPrecioDefault($id_vehiculo, $id_tipo_reserva)
  {
    // Lógica de cálculo del precio predeterminado
    // Puede ser un precio fijo o cualquier otro cálculo necesario
    return 100; // Ejemplo de precio fijo
  }
}
