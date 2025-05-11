<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\TipoReserva;
use App\Models\Hotel;

class UsuarioReservaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'usuario']);
    }

    public function index()
    {
        $usuario = Auth::user();
        $reservas = Reserva::where('email_cliente', $usuario->email)
            ->with(['vehiculo', 'tipoReserva'])
            ->latest('created_at')
            ->get();

        return view('usuario.reservas.index', compact('reservas'));
    }

    public function create()
    {
        return view('usuario.reservas.create', [
            'hoteles' => Hotel::all(),
            'vehiculos' => Vehiculo::all(),
            'tipos_reserva' => TipoReserva::all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $usuario = Auth::user();
            $now = Carbon::now();

            // Validar campos básicos
            $rules = [
                'hotel_id' => 'required|exists:transfer_hotel,id_hotel',
                'vehiculo_id' => 'required|exists:transfer_vehiculo,id_vehiculo',
                'tipo_reserva_id' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
                'pasajeros' => 'required|integer|min:1|max:8',
                'fecha_entrada' => 'required|date|after:' . $now->addHours(48)->format('Y-m-d'),
                'hora_entrada' => 'required|date_format:H:i',
                'origen_vuelo_entrada' => 'required|string|max:50',
                'numero_vuelo_entrada' => 'required|string|max:20',
                'fecha_vuelo_salida' => 'required_with:hora_recogida|date|after:fecha_entrada',
                'hora_vuelo_salida' => 'required_with:fecha_vuelo_salida|date_format:H:i',
                'hora_recogida' => 'required_with:fecha_vuelo_salida|date_format:H:i|after:hora_entrada',
                'numero_vuelo_salida' => 'required_with:fecha_vuelo_salida|string|max:20',
                'origen_vuelo_salida' => 'required_with:fecha_vuelo_salida|string|max:50',
            ];

            $validated = $request->validate($rules);

            // Verificar disponibilidad del vehículo
            $fechaEntrada = Carbon::parse($validated['fecha_entrada']);
            $horaEntrada = Carbon::parse($validated['hora_entrada']);
            $horaRecogida = null;

            if ($validated['hora_recogida']) {
                $horaRecogida = Carbon::parse($validated['hora_recogida']);
                if ($horaRecogida <= $horaEntrada) {
                    return back()->withErrors([
                        'hora_recogida' => 'La hora de recogida debe ser posterior a la hora de entrada.'
                    ])->withInput();
                }
            }

            // Generar localizador único
            do {
                $localizador = 'RES' . strtoupper(uniqid()) . '-' . date('Ymd');
            } while (Reserva::where('localizador', $localizador)->exists());

            // Crear reserva
            $reservaData = [
                'localizador' => $localizador,
                'id_hotel' => $validated['hotel_id'],
                'id_tipo_reserva' => $validated['tipo_reserva_id'],
                'email_cliente' => $usuario->email,
                'fecha_reserva' => $now,
                'fecha_modificacion' => $now,
                'fecha_entrada' => $validated['fecha_entrada'],
                'hora_entrada' => $validated['hora_entrada'],
                'num_viajeros' => $validated['pasajeros'],
                'id_vehiculo' => $validated['vehiculo_id'],
                'creado_por_admin' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ];

            // Agregar campos opcionales solo si tienen valor
            if (!empty($validated['numero_vuelo_entrada'])) {
                $reservaData['numero_vuelo_entrada'] = $validated['numero_vuelo_entrada'];
            }
            if (!empty($validated['origen_vuelo_entrada'])) {
                $reservaData['origen_vuelo_entrada'] = $validated['origen_vuelo_entrada'];
            }
            if (!empty($validated['fecha_vuelo_salida'])) {
                $reservaData['fecha_vuelo_salida'] = $validated['fecha_vuelo_salida'];
            }
            if (!empty($validated['hora_vuelo_salida'])) {
                $reservaData['hora_vuelo_salida'] = $validated['hora_vuelo_salida'];
            }
            if (!empty($validated['hora_recogida'])) {
                $reservaData['hora_recogida'] = $validated['hora_recogida'];
            }

            $reserva = Reserva::create($reservaData);

            return redirect()->route('usuario.reservas.index')
                ->with('success', 'Reserva creada exitosamente');

        } catch (\Exception $e) {
            \Log::error('Error al crear reserva', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->withErrors([
                'error' => 'Error al crear la reserva. Por favor, inténtelo de nuevo.'
            ])->withInput();
        }
    }

    public function show(Reserva $reserva)
    {
        $this->authorize('view', $reserva);
        return view('usuario.reservas.show', compact('reserva'));
    }

    public function edit(Reserva $reserva)
    {
        $this->authorize('update', $reserva);
        return view('usuario.reservas.edit', [
            'reserva' => $reserva,
            'tiposReserva' => App\Models\TipoReserva::all(),
            'vehiculos' => App\Models\Vehiculo::all()
        ]);
    }

    public function update(Request $request, Reserva $reserva)
    {
        $this->authorize('update', $reserva);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'origen_vuelo_salida' => 'required|string|max:255',
            'origen_vuelo_entrada' => 'required|string|max:255',
            'hora_salida' => 'required|date_format:H:i',
            'hora_entrada' => 'required|date_format:H:i',
            'pasajeros' => 'required|integer|min:1',
            'vehiculo_id' => 'required|exists:transfer_vehiculo,id',
        ]);

        $reserva->update($validated);
        return redirect()->route('usuario.dashboard')->with('success', 'Reserva actualizada correctamente');
    }

    public function destroy(Reserva $reserva)
    {
        $this->authorize('delete', $reserva);
        $reserva->delete();
        return redirect()->route('usuario.dashboard')->with('success', 'Reserva cancelada correctamente');
    }
}
