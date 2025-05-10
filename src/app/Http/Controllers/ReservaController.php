<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\Hotel;
use App\Models\TipoReserva;
use App\Models\Viajero;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el listado de reservas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Si es admin, ve todas las reservas; si no, solo sus reservas
        if ($user->esAdmin()) {
            $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])->orderBy('fecha_reserva', 'desc')->get();
        } else {
            $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])
                ->where('email_cliente', $user->email)
                ->orderBy('fecha_reserva', 'desc')
                ->get();
        }

        return view('reservas.index', compact('reservas'));
    }

    /**
     * Muestra el formulario para crear una nueva reserva.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $vehiculos = Vehiculo::all();
        $hoteles = Hotel::all();
        $tiposReserva = TipoReserva::all();
        
        // Si es admin, podemos elegir el usuario
        $usuarios = [];
        if (Auth::user()->esAdmin()) {
            $usuarios = Viajero::all();
        }

        return view('reservas.create', compact('vehiculos', 'hoteles', 'tiposReserva', 'usuarios'));
    }

    /**
     * Almacena una nueva reserva en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $rolUsuario = $user->esAdmin() ? 'admin' : 'usuario';

        $request->validate([
            'tipo_trayecto' => 'required',
            'num_viajeros' => 'required|numeric|min:1',
            'id_vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
            'id_hotel' => 'required|exists:transfer_hotel,id_hotel',
            'fecha_entrada' => 'nullable|date',
            'hora_entrada' => 'nullable',
            'numero_vuelo_entrada' => 'nullable|string',
            'origen_vuelo_entrada' => 'nullable|string',
            'fecha_vuelo_salida' => 'nullable|date',
            'hora_vuelo_salida' => 'nullable',
            'hora_recogida' => 'nullable',
        ]);

        // Validación de 48 horas para usuarios normales
        if ($rolUsuario !== 'admin' && $request->filled('fecha_entrada') && $request->filled('hora_entrada')) {
            $fechaEntrada = Carbon::parse($request->fecha_entrada . ' ' . $request->hora_entrada);
            if ($fechaEntrada->diffInHours(Carbon::now()) < 48) {
                return back()->withErrors([
                    'fecha_entrada' => 'Las reservas deben hacerse con mínimo 48 horas de antelación',
                ])->withInput();
            }
        }

        // Determinar el email del cliente
        $emailCliente = $user->email;
        if ($rolUsuario === 'admin' && $request->filled('id_viajero')) {
            $viajero = Viajero::find($request->id_viajero);
            if ($viajero) {
                $emailCliente = $viajero->email;
            }
        }

        try {
            $reserva = new Reserva();
            $reserva->localizador = Reserva::generarLocalizador();
            $reserva->id_tipo_reserva = $request->tipo_trayecto;
            $reserva->email_cliente = $emailCliente;
            $reserva->fecha_reserva = Carbon::now();
            $reserva->fecha_modificacion = Carbon::now();
            $reserva->id_hotel = $request->id_hotel;
            $reserva->fecha_entrada = $request->fecha_entrada;
            $reserva->hora_entrada = $request->hora_entrada;
            $reserva->numero_vuelo_entrada = $request->numero_vuelo_entrada;
            $reserva->origen_vuelo_entrada = $request->origen_vuelo_entrada;
            $reserva->fecha_vuelo_salida = $request->fecha_vuelo_salida;
            $reserva->hora_vuelo_salida = $request->hora_vuelo_salida;
            $reserva->hora_recogida = $request->hora_recogida;
            $reserva->num_viajeros = $request->num_viajeros;
            $reserva->id_vehiculo = $request->id_vehiculo;
            $reserva->creado_por_admin = $user->esAdmin() ? 1 : 0;
            $reserva->save();

            return redirect()->route('reservas.index')->with('success', 'Reserva creada con éxito');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al crear la reserva: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Muestra el detalle de una reserva específica.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Filtrar por email si no es admin
        if ($user->esAdmin()) {
            $reserva = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])->findOrFail($id);
        } else {
            $reserva = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])
                ->where('id_reserva', $id)
                ->where('email_cliente', $user->email)
                ->firstOrFail();
        }

        return view('reservas.show', compact('reserva'));
    }

    /**
     * Muestra el formulario para editar una reserva existente.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        $reserva = Reserva::findOrFail($id);
        
        // Verificar permiso (admin o dueño de la reserva)
        if ($reserva->email_cliente !== $user->email && !$user->esAdmin()) {
            return redirect()->route('reservas.index')
                ->with('error', 'No tienes permiso para editar esta reserva');
        }
        
        // Verificar restricción de 48 horas para usuarios normales
        if (!$user->esAdmin() && !$reserva->puedeSerModificadaPorUsuario()) {
            return redirect()->route('reservas.index')
                ->with('error', 'No se puede modificar una reserva con menos de 48 horas de antelación');
        }

        $vehiculos = Vehiculo::all();
        $hoteles = Hotel::all();
        $tiposReserva = TipoReserva::all();
        
        return view('reservas.edit', compact('reserva', 'vehiculos', 'hoteles', 'tiposReserva'));
    }

    /**
     * Actualiza una reserva específica en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $reserva = Reserva::findOrFail($id);
        
        // Verificar permiso (admin o dueño de la reserva)
        if ($reserva->email_cliente !== $user->email && !$user->esAdmin()) {
            return redirect()->route('reservas.index')
                ->with('error', 'No tienes permiso para editar esta reserva');
        }
        
        // Verificar restricción de 48 horas para usuarios normales
        if (!$user->esAdmin() && !$reserva->puedeSerModificadaPorUsuario()) {
            return redirect()->route('reservas.index')
                ->with('error', 'No se puede modificar una reserva con menos de 48 horas de antelación');
        }

        $request->validate([
            'id_hotel' => 'required|exists:transfer_hotel,id_hotel',
            'id_vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
            'num_viajeros' => 'required|numeric|min:1',
            'fecha_entrada' => 'nullable|date',
            'hora_entrada' => 'nullable',
            'numero_vuelo_entrada' => 'nullable|string',
            'origen_vuelo_entrada' => 'nullable|string',
            'fecha_vuelo_salida' => 'nullable|date',
            'hora_vuelo_salida' => 'nullable',
            'hora_recogida' => 'nullable',
        ]);

        try {
            $reserva->id_hotel = $request->id_hotel;
            $reserva->id_vehiculo = $request->id_vehiculo;
            $reserva->num_viajeros = $request->num_viajeros;
            $reserva->fecha_entrada = $request->fecha_entrada;
            $reserva->hora_entrada = $request->hora_entrada;
            $reserva->numero_vuelo_entrada = $request->numero_vuelo_entrada;
            $reserva->origen_vuelo_entrada = $request->origen_vuelo_entrada;
            $reserva->fecha_vuelo_salida = $request->fecha_vuelo_salida;
            $reserva->hora_vuelo_salida = $request->hora_vuelo_salida;
            $reserva->hora_recogida = $request->hora_recogida;
            $reserva->fecha_modificacion = Carbon::now();
            $reserva->save();

            return redirect()->route('reservas.index')
                ->with('success', 'Reserva actualizada con éxito');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar la reserva: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * Elimina una reserva específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $reserva = Reserva::findOrFail($id);
        
        // Verificar permiso (solo admin puede eliminar o el dueño si faltan más de 48h)
        if (!$user->esAdmin()) {
            if ($reserva->email_cliente !== $user->email) {
                return redirect()->route('reservas.index')
                    ->with('error', 'No tienes permiso para eliminar esta reserva');
            }
            
            if (!$reserva->puedeSerModificadaPorUsuario()) {
                return redirect()->route('reservas.index')
                    ->with('error', 'No se puede cancelar una reserva con menos de 48 horas de antelación');
            }
        }

        try {
            $reserva->delete();
            return redirect()->route('reservas.index')
                ->with('success', 'Reserva eliminada con éxito');
        } catch (\Exception $e) {
            return redirect()->route('reservas.index')
                ->with('error', 'Error al eliminar la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el calendario de reservas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function calendario(Request $request)
    {
        $user = Auth::user();
        $vista = $request->input('vista', 'dayGridMonth');
        $fecha = $request->input('fecha', Carbon::now()->format('Y-m-d'));
        
        // Si es admin, ve todas las reservas; si no, solo sus reservas
        if ($user->esAdmin()) {
            $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])->get();
        } else {
            $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])
                ->where('email_cliente', $user->email)
                ->get();
        }
        
        // Formatear reservas para el calendario
        $eventos = [];
        foreach ($reservas as $reserva) {
            // Evento para fecha de entrada
            if (!empty($reserva->fecha_entrada)) {
                $eventos[] = [
                    'id' => $reserva->id_reserva,
                    'title' => 'Llegada: ' . $reserva->hotel->descripcion,
                    'start' => $reserva->fecha_entrada . 'T' . $reserva->hora_entrada,
                    'url' => route('reservas.show', $reserva->id_reserva),
                    'backgroundColor' => '#4CAF50',
                ];
            }
            
            // Evento para fecha de salida
            if (!empty($reserva->fecha_vuelo_salida)) {
                $eventos[] = [
                    'id' => $reserva->id_reserva . '-salida',
                    'title' => 'Salida: ' . $reserva->hotel->descripcion,
                    'start' => $reserva->fecha_vuelo_salida . 'T' . $reserva->hora_vuelo_salida,
                    'url' => route('reservas.show', $reserva->id_reserva),
                    'backgroundColor' => '#F44336',
                ];
            }
        }

        return view('reservas.calendario', compact('eventos', 'vista', 'fecha'));
    }
}
