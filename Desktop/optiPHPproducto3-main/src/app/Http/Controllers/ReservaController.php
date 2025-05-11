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
use App\Models\Viajero;
use Exception;
use Illuminate\Support\Facades\DB;

/*──────────────────────────────
|  ReservaController
|──────────────────────────────*/
class ReservaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['admin'])->except(['create', 'store']);
    }

    /** Lista de reservas del hotel */
    public function index()
    {
        if (Auth::user()->esAdmin()) {
            $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])
                ->latest('created_at')
                ->get();

            // Agregar campo creado_por_admin
            $reservas = $reservas->map(function($reserva) {
                $reserva->creado_por_admin = Auth::user()->esAdmin();
                return $reserva;
            });

            return view('admin.reservas.index', compact('reservas'));
        }

        $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva'])
            ->where('email_cliente', Auth::user()->email)
            ->latest('created_at')
            ->get();

        return view('usuario.reservas.index', compact('reservas'));
    }

    /** Vista calendario de trayectos */
    public function calendario(Request $request)
    {
        // Obtener todas las reservas con sus relaciones
        $reservas = Reserva::with(['hotel', 'vehiculo', 'tipoReserva', 'usuario', 'zona'])
            ->get();

        // Preparar datos para el calendario
        $eventos = $reservas->map(function($reserva) {
            try {
                // Crear evento para la entrada
                $eventoEntrada = [
                    'id' => 'entrada-' . $reserva->id_reserva,
                    'title' => 'Entrada: ' . $reserva->hotel->Usuario,
                    'start' => $reserva->fecha_entrada . 'T' . $reserva->hora_entrada,
                    'color' => '#28a745',
                    'extendedProps' => [
                        'localizador' => $reserva->localizador,
                        'hotel' => $reserva->hotel->Usuario,
                        'tipo_reserva' => $reserva->tipoReserva->Descripción,
                        'vehiculo' => $reserva->vehiculo->Descripción,
                        'pasajeros' => $reserva->num_viajeros,
                        'email_cliente' => $reserva->email_cliente,
                        'estado' => 'Confirmada',
                        'viajero' => $reserva->usuario ? $reserva->usuario->email : 'No asignado',
                        'zona' => $reserva->zona ? $reserva->zona->Nombre : 'No especificada',
                        'tipo' => 'entrada'
                    ]
                ];

                // Crear evento para la salida si hay fecha de salida
                $eventoSalida = null;
                if ($reserva->fecha_vuelo_salida) {
                    $eventoSalida = [
                        'id' => 'salida-' . $reserva->id_reserva,
                        'title' => 'Salida: ' . $reserva->hotel->Usuario,
                        'start' => $reserva->fecha_vuelo_salida . 'T' . $reserva->hora_vuelo_salida,
                        'color' => '#dc3545',
                        'extendedProps' => [
                            'localizador' => $reserva->localizador,
                            'hotel' => $reserva->hotel->Usuario,
                            'tipo_reserva' => $reserva->tipoReserva->Descripción,
                            'vehiculo' => $reserva->vehiculo->Descripción,
                            'pasajeros' => $reserva->num_viajeros,
                            'email_cliente' => $reserva->email_cliente,
                            'estado' => 'Confirmada',
                            'viajero' => $reserva->usuario ? $reserva->usuario->email : 'No asignado',
                            'zona' => $reserva->zona ? $reserva->zona->Nombre : 'No especificada',
                            'tipo' => 'salida'
                        ]
                    ];
                }

                // Devolver ambos eventos en un array
                return [$eventoEntrada, $eventoSalida];

            } catch (\Exception $e) {
                \Log::error('Error al crear evento para reserva ' . $reserva->id_reserva . ': ' . $e->getMessage());
                return null;
            }
        });

        // Filtrar y aplanar el array de eventos
        $eventos = $eventos->filter()->flatten();

        return view('admin.reservas.calendario', compact('eventos'));

        // Preparar datos para el calendario
        $eventos = $reservas->map(function($reserva) {
            try {
                // Crear evento para la entrada
                $eventoEntrada = [
                    'id' => 'entrada-' . $reserva->id_reserva,
                    'title' => 'Entrada: ' . $reserva->hotel->Usuario,
                    'start' => $reserva->fecha_entrada . 'T' . $reserva->hora_entrada,
                    'color' => '#28a745',
                    'extendedProps' => [
                        'localizador' => $reserva->localizador,
                        'hotel' => $reserva->hotel->Usuario,
                        'tipo_reserva' => $reserva->tipoReserva->Descripción,
                        'vehiculo' => $reserva->vehiculo->Descripción,
                        'pasajeros' => $reserva->num_viajeros,
                        'email_cliente' => $reserva->email_cliente,
                        'estado' => 'Confirmada',
                        'viajero' => $reserva->usuario ? $reserva->usuario->email : 'No asignado',
                        'zona' => $reserva->hotel ? $reserva->hotel->zona->Nombre : 'No especificada',
                        'tipo' => 'entrada'
                    ]
                ];

                // Crear evento para la salida si existe
                $eventoSalida = null;
                if ($reserva->fecha_vuelo_salida && $reserva->hora_vuelo_salida) {
                    $eventoSalida = [
                        'id' => 'salida-' . $reserva->id_reserva,
                        'title' => 'Salida: ' . $reserva->hotel->Usuario,
                        'start' => $reserva->fecha_vuelo_salida . 'T' . $reserva->hora_vuelo_salida,
                        'color' => '#dc3545',
                        'extendedProps' => [
                            'localizador' => $reserva->localizador,
                            'hotel' => $reserva->hotel->Usuario,
                            'tipo_reserva' => $reserva->tipoReserva->Descripción,
                            'vehiculo' => $reserva->vehiculo->Descripción,
                            'pasajeros' => $reserva->num_viajeros,
                            'email_cliente' => $reserva->email_cliente,
                            'estado' => 'Confirmada',
                            'viajero' => $reserva->usuario ? $reserva->usuario->email : 'No asignado',
                            'zona' => $reserva->zona ? $reserva->zona->Nombre : 'No especificada',
                            'tipo' => 'salida'
                        ]
                    ];
                }

                // Devolver ambos eventos si hay salida, solo entrada si no
                return $eventoSalida ? [$eventoEntrada, $eventoSalida] : [$eventoEntrada];
            } catch (\Exception $e) {
                \Log::error('Error al crear evento:', [
                    'reserva_id' => $reserva->id_reserva,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        })->filter(); // Eliminar los nulls

        // Aplanar el array si hay eventos de salida
        $eventos = $eventos->flatten();

        return view('admin.reservas.calendario', compact('eventos'));

        // Log para depuración
        \Log::info('Eventos finales:', ['eventos' => $eventos->toArray()]);

        if ($request->ajax()) {
            return response()->json($eventos->toArray());
        }

        return view('admin.reservas.calendario', ['eventos' => $eventos]);
    }

    /** Muestra los detalles de un trayecto */
    public function trayecto($id)
    {
        $reserva = Reserva::with(['hotel', 'vehiculo', 'tipoReserva', 'viajero'])
            ->findOrFail($id);

        return view('admin.reservas.trayecto', compact('reserva'));
    }

    /** formulario nueva reserva */
    public function create()
    {
        // Verificar si el usuario es admin
        if (!Auth::user()->esAdmin()) {
            \Log::error('Acceso denegado: usuario no es admin', [
                'user_id' => Auth::id(),
                'user_rol' => Auth::user()->rol
            ]);
            return redirect()->route('usuario.dashboard')->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
        }

        // Cargar datos con relaciones
        $vehiculos = Vehiculo::with('reservas')->get();
        $tiposReserva = TipoReserva::with('reservas')->get();
        $hoteles = Hotel::with('reservas')->get();
        $viajeros = Viajero::with('reservas')->get();

        // Verificar si hay datos
        if ($vehiculos->isEmpty() || $tiposReserva->isEmpty() || $hoteles->isEmpty() || $viajeros->isEmpty()) {
            \Log::error('Algunas tablas están vacías');
            return back()->with('error', 'No hay datos disponibles en algunas tablas');
        }

        // Preparar datos para la vista
        $data = [
            'vehiculos' => $vehiculos->map(function($vehiculo) {
                return [
                    'id_vehiculo' => $vehiculo->id_vehiculo,
                    'descripcion' => $vehiculo->Descripción
                ];
            }),
            'tiposReserva' => $tiposReserva->map(function($tipo) {
                return [
                    'id_tipo_reserva' => $tipo->id_tipo_reserva,
                    'descripcion' => $tipo->Descripción
                ];
            }),
            'hoteles' => $hoteles->map(function($hotel) {
                return [
                    'id_hotel' => $hotel->id_hotel,
                    'usuario' => $hotel->Usuario,
                    'zona' => $hotel->id_zona
                ];
            }),
            'viajeros' => Viajero::select('id_viajero', 'email', 'nombre', 'apellido1', 'apellido2')->get()->map(function($viajero) {
                return [
                    'id' => $viajero->id_viajero,
                    'email' => $viajero->email,
                    'nombre_completo' => $viajero->nombre . ' ' . $viajero->apellido1 . ' ' . $viajero->apellido2
                ];
            }),
            'viajero' => Auth::user()->id_viajero
        ];

        return view('admin.reservas.create', $data);
    }

    /** almacena la reserva + cálculo de comisión */
    public function store(Request $request)
    {
        try {
            // Validar campos básicos
            $rules = [
                'id_hotel' => 'required|exists:transfer_hotel,id_hotel',
                'vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
                'tipoReserva' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
                'num_viajeros' => 'required|integer|min:1|max:8', // Máximo 8 pasajeros
                'fecha_entrada' => 'required|date|after:today',
                'hora_entrada' => 'required|date_format:H:i',
                'hora_recogida' => 'required|date_format:H:i',
                'viajero' => 'required|exists:transfer_viajeros,id_viajero',
            ];

            $tipoReserva = TipoReserva::findOrFail($request->tipoReserva);
            
            // Validar campos específicos según el tipo de reserva
            if ($tipoReserva->id_tipo_reserva == 1) { // Aeropuerto -> Hotel
                $rules['numero_vuelo_entrada'] = 'required|string|max:20';
                $rules['origen_vuelo_entrada'] = 'required|string|max:50';
            } elseif ($tipoReserva->id_tipo_reserva == 2) { // Hotel -> Aeropuerto
                $rules['fecha_vuelo_salida'] = 'required|date|after:fecha_entrada';
                $rules['hora_vuelo_salida'] = 'required|date_format:H:i';
            } elseif ($tipoReserva->id_tipo_reserva == 3) { // Ida y Vuelta
                $rules['numero_vuelo_entrada'] = 'required|string|max:20';
                $rules['origen_vuelo_entrada'] = 'required|string|max:50';
                $rules['fecha_vuelo_salida'] = 'required|date|after:fecha_entrada';
                $rules['hora_vuelo_salida'] = 'required|date_format:H:i';
            }

            $validated = $request->validate($rules);

            // Verificar disponibilidad del vehículo
            $fechaEntrada = Carbon::parse($validated['fecha_entrada']);
            $horaEntrada = Carbon::parse($validated['hora_entrada']);
            $horaRecogida = Carbon::parse($validated['hora_recogida']);
            
            $vehiculoOcupado = Reserva::where('id_vehiculo', $validated['vehiculo'])
                ->whereDate('fecha_entrada', $fechaEntrada)
                ->where(function ($query) use ($horaEntrada, $horaRecogida) {
                    $query->whereBetween('hora_entrada', [$horaEntrada, $horaRecogida])
                        ->orWhereBetween('hora_recogida', [$horaEntrada, $horaRecogida]);
                })
                ->exists();

            if ($vehiculoOcupado) {
                return back()->withErrors([
                    'vehiculo' => 'El vehículo seleccionado no está disponible en ese horario.'
                ])->withInput();
            }

            // Validación de 48 horas para usuarios normales
            if (!Auth::user()->esAdmin()) {
                $ahora = Carbon::now();
                
                if ($fechaEntrada->diffInHours($ahora) < 48) {
                    return back()->withErrors([
                        'fecha_entrada' => 'Las reservas deben realizarse con al menos 48 horas de antelación.'
                    ])->withInput();
                }
            }

            // Verificar que la hora de recogida sea después de la hora de entrada
            if ($horaRecogida <= $horaEntrada) {
                return back()->withErrors([
                    'hora_recogida' => 'La hora de recogida debe ser posterior a la hora de entrada.'
                ])->withInput();
            }

            // Generar localizador único
            do {
                $localizador = 'RES' . strtoupper(uniqid()) . '-' . date('Ymd');
            } while (Reserva::where('localizador', $localizador)->exists());

            // Cargar relaciones necesarias
            $hotel = Hotel::with('zona')->findOrFail($validated['id_hotel']);
            $viajero = Viajero::findOrFail($validated['viajero']);

            // Crear reserva dentro de una transacción
            DB::beginTransaction();

            $reserva = Reserva::create([
                'id_hotel' => $validated['id_hotel'],
                'id_vehiculo' => $validated['vehiculo'],
                'id_tipo_reserva' => $validated['tipoReserva'],
                'num_viajeros' => $validated['num_viajeros'],
                'fecha_entrada' => $validated['fecha_entrada'],
                'hora_entrada' => $validated['hora_entrada'],
                'numero_vuelo_entrada' => $validated['numero_vuelo_entrada'] ?? null,
                'origen_vuelo_entrada' => $validated['origen_vuelo_entrada'] ?? null,
                'fecha_vuelo_salida' => $validated['fecha_vuelo_salida'] ?? null,
                'hora_vuelo_salida' => $validated['hora_vuelo_salida'] ?? null,
                'hora_recogida' => $validated['hora_recogida'],
                'localizador' => $localizador,
                'email_cliente' => $viajero->email,
                'id_viajero' => $validated['viajero'],
                'estado' => 'confirmada',
                'precio' => $this->calcularPrecio($hotel->zona->id_zona, $validated['vehiculo'], $validated['tipoReserva']),
                'comision' => $hotel->Comision
            ]);

            DB::commit();

            // Registrar en el log
            \Log::info('Reserva creada exitosamente', [
                'localizador' => $localizador,
                'hotel' => $hotel->Usuario,
                'tipo_reserva' => $tipoReserva->Descripción,
                'usuario_id' => Auth::id()
            ]);

            return redirect()->route('admin.reservas.index')
                ->with('success', 'Reserva creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear reserva', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->withErrors([
                'error' => 'Error al crear la reserva. Por favor, inténtelo de nuevo.'
            ])->withInput();
        }
    } 
    /** Muestra los detalles de una reserva */
    public function show(Reserva $reserva)
    {
        // Cargar las relaciones necesarias
        $reserva->load(['hotel', 'vehiculo', 'tipoReserva', 'viajero']);

        // Verificar autorización
        $this->autorizar($reserva);

        return view('admin.reservas.show', compact('reserva'));
    }

    /** Muestra el formulario para editar una reserva */
    public function edit($id)
    {
        $reserva = Reserva::with(['hotel', 'vehiculo', 'tipoReserva', 'viajero'])->findOrFail($id);
        
        // Verificar si el usuario puede modificar la reserva
        if (!Auth::user()->esAdmin()) {
            $fechaEntrada = Carbon::parse($reserva->fecha_entrada);
            $ahora = Carbon::now();
            
            if ($fechaEntrada->diffInHours($ahora) < 48) {
                return back()->with('error', 'No se puede modificar una reserva con menos de 48 horas de antelación.');
            }
        }

        $vehiculos = Vehiculo::all();
        $tiposReserva = TipoReserva::all();
        $hoteles = Hotel::all();
        $viajeros = Viajero::all();

        return view('admin.reservas.edit', compact('reserva', 'vehiculos', 'tiposReserva', 'hoteles', 'viajeros'));
    }

    /** Actualiza una reserva existente */
    public function update(Request $request, $id)
    {
        try {
            $reserva = Reserva::with(['hotel', 'vehiculo'])->findOrFail($id);
            
            // Verificar autorización
            $this->autorizar($reserva);

            // Validar campos básicos
            $rules = [
                'id_hotel' => 'required|exists:transfer_hotel,id_hotel',
                'vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
                'tipoReserva' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
                'num_viajeros' => 'required|integer|min:1|max:8', // Máximo 8 pasajeros
                'fecha_entrada' => 'required|date|after:today',
                'hora_entrada' => 'required|date_format:H:i',
                'hora_recogida' => 'required|date_format:H:i',
                'viajero' => 'required|exists:transfer_viajeros,id_viajero',
            ];

            $tipoReserva = TipoReserva::findOrFail($request->tipoReserva);
            
            // Validar campos específicos según el tipo de reserva
            if ($tipoReserva->id_tipo_reserva == 1) { // Aeropuerto -> Hotel
                $rules['numero_vuelo_entrada'] = 'required|string|max:20';
                $rules['origen_vuelo_entrada'] = 'required|string|max:50';
            } elseif ($tipoReserva->id_tipo_reserva == 2) { // Hotel -> Aeropuerto
                $rules['fecha_vuelo_salida'] = 'required|date|after:fecha_entrada';
                $rules['hora_vuelo_salida'] = 'required|date_format:H:i';
            } elseif ($tipoReserva->id_tipo_reserva == 3) { // Ida y Vuelta
                $rules['numero_vuelo_entrada'] = 'required|string|max:20';
                $rules['origen_vuelo_entrada'] = 'required|string|max:50';
                $rules['fecha_vuelo_salida'] = 'required|date|after:fecha_entrada';
                $rules['hora_vuelo_salida'] = 'required|date_format:H:i';
            }

            $validated = $request->validate($rules);

            // Verificar disponibilidad del vehículo
            $fechaEntrada = Carbon::parse($validated['fecha_entrada']);
            $horaEntrada = Carbon::parse($validated['hora_entrada']);
            $horaRecogida = Carbon::parse($validated['hora_recogida']);
            
            // Verificar que el vehículo no esté ocupado en otro horario
            $vehiculoOcupado = Reserva::where('id_vehiculo', $validated['vehiculo'])
                ->where('id_reserva', '!=', $id)
                ->whereDate('fecha_entrada', $fechaEntrada)
                ->where(function ($query) use ($horaEntrada, $horaRecogida) {
                    $query->whereBetween('hora_entrada', [$horaEntrada, $horaRecogida])
                        ->orWhereBetween('hora_recogida', [$horaEntrada, $horaRecogida]);
                })
                ->exists();

            if ($vehiculoOcupado) {
                return back()->withErrors([
                    'vehiculo' => 'El vehículo seleccionado no está disponible en ese horario.'
                ])->withInput();
            }

            // Validación de 48 horas para usuarios normales
            if (!Auth::user()->esAdmin()) {
                $ahora = Carbon::now();
                
                if ($fechaEntrada->diffInHours($ahora) < 48) {
                    return back()->withErrors([
                        'fecha_entrada' => 'No se puede modificar una reserva con menos de 48 horas de antelación.'
                    ])->withInput();
                }
            }

            // Verificar que la hora de recogida sea después de la hora de entrada
            if ($horaRecogida <= $horaEntrada) {
                return back()->withErrors([
                    'hora_recogida' => 'La hora de recogida debe ser posterior a la hora de entrada.'
                ])->withInput();
            }

            // Actualizar la reserva dentro de una transacción
            DB::beginTransaction();

            $reserva->update([
                'id_hotel' => $validated['id_hotel'],
                'id_vehiculo' => $validated['vehiculo'],
                'id_tipo_reserva' => $validated['tipoReserva'],
                'num_viajeros' => $validated['num_viajeros'],
                'fecha_entrada' => $validated['fecha_entrada'],
                'hora_entrada' => $validated['hora_entrada'],
                'numero_vuelo_entrada' => $validated['numero_vuelo_entrada'] ?? null,
                'origen_vuelo_entrada' => $validated['origen_vuelo_entrada'] ?? null,
                'fecha_vuelo_salida' => $validated['fecha_vuelo_salida'] ?? null,
                'hora_vuelo_salida' => $validated['hora_vuelo_salida'] ?? null,
                'hora_recogida' => $validated['hora_recogida'],
                'id_viajero' => $validated['viajero'],
                'precio' => $this->calcularPrecio($reserva->hotel->id_zona, $validated['vehiculo'], $validated['tipoReserva']),
                'comision' => $reserva->hotel->Comision
            ]);

            DB::commit();

            // Registrar en el log
            \Log::info('Reserva actualizada exitosamente', [
                'reserva_id' => $id,
                'hotel' => $reserva->hotel->Usuario,
                'tipo_reserva' => $tipoReserva->Descripción,
                'usuario_id' => Auth::id()
            ]);

            return redirect()->route('admin.reservas.index')
                ->with('success', 'Reserva actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar reserva', [
                'error' => $e->getMessage(),
                'reserva_id' => $id,
                'usuario_id' => Auth::id()
            ]);
            return back()->withErrors([
                'error' => 'Error al actualizar la reserva. Por favor, inténtelo de nuevo.'
            ])->withInput();
        }
    }
    /** Cancela una reserva */
    public function destroy($id)
    {
        try {
            $reserva = Reserva::with(['hotel'])->findOrFail($id);

            // Verificar autorización
            $this->autorizar($reserva);

            // Verificar si el usuario puede cancelar la reserva
            if (!Auth::user()->esAdmin()) {
                $fechaEntrada = Carbon::parse($reserva->fecha_entrada);
                $ahora = Carbon::now();
                
                if ($fechaEntrada->diffInHours($ahora) < 48) {
                    return back()->with('error', 'No se puede cancelar una reserva con menos de 48 horas de antelación.');
                }
            }

            // Eliminar la reserva dentro de una transacción
            DB::beginTransaction();

            $reserva->delete();

            DB::commit();

            // Registrar en el log
            \Log::info('Reserva cancelada', [
                'reserva_id' => $id,
                'hotel' => $reserva->hotel->Usuario,
                'usuario_id' => Auth::id()
            ]);

            return redirect()->route('admin.reservas.index')
                ->with('success', 'Reserva cancelada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al cancelar reserva', [
                'error' => $e->getMessage(),
                'reserva_id' => $id,
                'usuario_id' => Auth::id()
            ]);
            return back()->with('error', 'Error al cancelar la reserva. Por favor, inténtelo de nuevo.');
        }
    }

    /* ··· helpers ··· */
    private function autorizar(Reserva $reserva)
    {
        try {
            if (!Auth::check()) {
                throw new \Exception('Usuario no autenticado');
            }

            if (!Auth::user()->esAdmin()) {
                if ($reserva->id_hotel !== Auth::user()->id_hotel) {
                    \Log::warning('Intento de acceso no autorizado', [
                        'user_id' => Auth::id(),
                        'reserva_id' => $reserva->id_reserva
                    ]);
                    abort(403, 'No tienes permisos para acceder a esta reserva');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error en autorización', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            abort(403, 'Error de autorización');
        }
    }

    private function calcularPrecio($id_zona, $id_vehiculo, $id_tipo_reserva)
    {
        // Usar un precio base fijo por ahora
        $precioBase = 50.00; // Precio base en euros

        // Obtener el factor del tipo de reserva
        $tipoReserva = TipoReserva::find($id_tipo_reserva);
        if (!$tipoReserva) {
            throw new \Exception('Tipo de reserva no encontrado.');
        }

        // Ajustar según el tipo de reserva
        switch ($id_tipo_reserva) {
            case 1: // Aeropuerto -> Hotel
                $precioFinal = $precioBase * $tipoReserva->factor;
                break;
            case 2: // Hotel -> Aeropuerto
                $precioFinal = $precioBase * $tipoReserva->factor;
                break;
            case 3: // Ida y vuelta
                $precioFinal = ($precioBase * $tipoReserva->factor) * 2;
                break;
            default:
                throw new \Exception('Tipo de reserva no válido.');
        }

        return $precioFinal;
    }
} 
