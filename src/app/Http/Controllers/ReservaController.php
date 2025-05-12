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
  public function index()
  {
    $hotelId = Auth::user()->id_hotel;
    $reservas = Reserva::with(['vehiculo', 'tipoReserva'])
      ->where('id_hotel', $hotelId)
      ->latest('created_at')
      ->get();

    return view('reservas.index', compact('reservas'));
  }

  public function calendario()
  {
    $user = auth()->user();

    /* ── ❷ eventos según rol ─────────────────────────────────── */
    if ($user->rol === 'corporativo') {
      if (!$user->id_hotel) {
        return back()->withErrors(
          'Tu cuenta corporativa aún no tiene hotel asignado.'
        );
      }

      $query = Reserva::where('id_hotel', $user->id_hotel);
    } elseif ($user->rol === 'admin') {
      $query = Reserva::query();                // todas las reservas
    } else {   // usuario particular
      $query = Reserva::where('email_cliente', $user->email);
    }

    /* ── ❸ selecciona columnas reales de tu tabla ────────────── */
    $eventos = $query->select([
      'id_reserva  as id',
      \DB::raw("CONCAT(localizador,' · ',num_viajeros,' pax') as title"),
      \DB::raw("CONCAT(fecha_entrada,'T',hora_entrada) as start")
    ])->get();

    return view('reservas.calendario', [
      'eventos' => $eventos->toJson(),
    ]);
  }

  /** formulario nueva reserva */
  public function create()
  {
    $vehiculos = Vehiculo::all();
    $tiposReserva = TipoReserva::all();

    /* solo si el que entra es admin */
    $usuarios = Auth::user()->rol === 'admin'
      ? Viajero::select('id_viajero', 'email')->get()
      : collect();   // colección vacía para no-admins

    return view('reservas.create', compact('vehiculos', 'tiposReserva', 'usuarios'));
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

        // Verificar si el usuario es corporativo
        if (Auth::user()->rol === 'corporativo') {
            // Si es corporativo, obtener el hotel asociado
            $hotel = Auth::user()->hotel;

            // Asegúrate de que el hotel tiene una zona asociada
            if ($hotel && $hotel->id_zona) {
                // Calcular el precio utilizando la zona del hotel
                $precio = $this->calcularPrecio($hotel->id_zona, $r->id_vehiculo, $r->id_tipo_reserva);
                // Comisión variable según lo pactado
                $comision = round($precio * $hotel->comision / 100, 2);
            } else {
                // Maneja el caso cuando no haya zona asociada al hotel
                return redirect()->back()->with('error', 'El hotel no tiene zona asociada.');
            }
        } else {
            // Si no es corporativo, manejar la lógica sin asociar el hotel
            // Definir un precio predeterminado o cualquier otro comportamiento
            $precio = $this->calcularPrecioDefault($r->id_vehiculo, $r->id_tipo_reserva);
            $comision = 0; // Si no hay hotel, no hay comisión
        }

        // Guardamos la reserva
        Reserva::create([
            'localizador' => Reserva::generarLocalizador(),
            'id_hotel' => $hotel->id_hotel ?? null, // Puede ser null si no es corporativo
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
            'precio' => $precio,
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

    // Método para calcular el precio predeterminado cuando no hay hotel
    private function calcularPrecioDefault($id_vehiculo, $id_tipo_reserva)
    {
        // Lógica de cálculo del precio predeterminado
        // Puede ser un precio fijo o cualquier otro cálculo necesario
        return 100; // Ejemplo de precio fijo
    }
}
