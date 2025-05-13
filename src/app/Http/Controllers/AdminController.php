<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\Viajero;
use App\Models\Hotel;
use App\Models\Zona;
use App\Models\TipoReserva;

class AdminController extends Controller
{
  /**
   * Constructor del controlador.
   */
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware('admin');
  }

  /**
   * Muestra el panel de administración.
   *
   * @return \Illuminate\View\View
   */
  public function panel()
  {
    // Cargamos estadísticas simples
    $stats = [
      'reservas_totales' => Reserva::count(),
      'reservas_hoy' => Reserva::whereDate('fecha_reserva', today())->count(),
      'hoteles' => Hotel::count(),
      'vehiculos' => Vehiculo::count(),
      'usuarios' => Viajero::where('rol', 'usuario')->count(),
    ];

    return view('admin.panel', compact('stats'));
  }

  /**
   * Muestra el menú de administración.
   *
   * @return \Illuminate\View\View
   */
  public function menu()
  {
    return view('admin.menu');
  }

  /**
   * Gestiona los hoteles.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function gestionarHoteles(Request $request)
  {
    $hoteles = Hotel::with('zona')->get();
    $zonas = Zona::all();

    return view('admin.gestionar_hoteles', compact('hoteles', 'zonas'));
  }

  /* ───────────────────────────────────────────────────────────
 |  LISTADO y FORMULARIO de usuarios
 *───────────────────────────────────────────────────────────*/
  public function gestionarUsuarios()
  {
    $usuarios = Viajero::with('hotel')->get();   // relación belongsTo en modelo
    $hoteles = Hotel::all();

    return view('admin.gestionar_usuarios', compact('usuarios', 'hoteles'));
  }

  /* ───────────────────────────────────────────────────────────
   |  CREAR usuario (normal / corporativo / admin)
   *───────────────────────────────────────────────────────────*/
  public function crearUsuario(Request $request)
  {
    $request->validate([
      'nombre' => 'required|string|max:100',
      'apellido1' => 'required|string|max:100',
      'apellido2' => 'nullable|string|max:100',
      'email' => 'required|email|max:100|unique:transfer_viajeros,email',
      'password' => 'required|string|min:6',
      'rol' => 'required|in:usuario,corporativo,admin',
      // estos solo importan si es corporativo
      'id_zona' => 'required_if:rol,corporativo|exists:transfer_zona,id_zona',
      'comision' => 'required_if:rol,corporativo|numeric|min:0',
    ]);

    DB::transaction(function () use ($request) {
      $hotelId = null;

      if ($request->rol === 'corporativo') {
        // 1) Creamos el hotel automáticamente
        $hotel = Hotel::create([
          'id_zona' => $request->id_zona,
          'descripcion' => $request->nombre . ' (Hotel)',
          'comision' => $request->comision,
          'Usuario' => $request->email,
          'password' => Hash::make($request->password),
        ]);
        $hotelId = $hotel->id_hotel;
      }

      Viajero::create([
        'nombre' => $request->nombre,
        'apellido1' => $request->apellido1,
        'apellido2' => $request->apellido2,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'rol' => $request->rol,
        'id_hotel' => $request->rol === 'corporativo' ? $hotelId : null,
      ]);
    });

    return back()->with('success', 'Usuario (y hotel si corresponde) creado correctamente.');
  }


  public function crearHotel(Request $request)
  {
    $request->validate([
      'id_zona' => 'required|exists:transfer_zona,id_zona',
      'descripcion' => 'required|string|max:100',
      'comision' => 'required|numeric',
      'Usuario' => 'required|string|max:100',
      'password' => 'required|string|min:6',
    ]);

    try {
      Hotel::create([
        'id_zona' => $request->id_zona,
        'descripcion' => $request->descripcion,
        'comision' => $request->comision,
        'Usuario' => $request->Usuario,
        'password' => Hash::make($request->password),
      ]);

      return redirect()->route('admin.hoteles.index')
        ->with('success', 'Hotel creado con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al crear hotel: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Actualiza un hotel existente.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function actualizarHotel(Request $request, $id)
  {
    $request->validate([
      'id_zona' => 'required|exists:transfer_zona,id_zona',
      'descripcion' => 'required|string|max:100',
      'comision' => 'required|numeric',
      'Usuario' => 'required|string|max:100',
      'password' => 'nullable|string|min:6',
    ]);

    try {
      $hotel = Hotel::findOrFail($id);

      $hotel->id_zona = $request->id_zona;
      $hotel->descripcion = $request->descripcion;
      $hotel->comision = $request->comision;
      $hotel->Usuario = $request->Usuario;

      if ($request->filled('password')) {
        $hotel->password = Hash::make($request->password);
      }

      $hotel->save();

      return redirect()->route('admin.hoteles.index')
        ->with('success', 'Hotel actualizado con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al actualizar hotel: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Elimina un hotel.
   *
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function eliminarHotel($id)
  {
    try {
      $hotel = Hotel::findOrFail($id);

      // Verificar si tiene reservas asociadas
      $reservasAsociadas = Reserva::where('id_hotel', $id)->exists();
      if ($reservasAsociadas) {
        return back()->with('error', 'No se puede eliminar el hotel porque tiene reservas asociadas');
      }

      $hotel->delete();
      return redirect()->route('admin.hoteles.index')
        ->with('success', 'Hotel eliminado con éxito');
    } catch (\Exception $e) {
      return back()->with('error', 'Error al eliminar hotel: ' . $e->getMessage());
    }
  }

  /**
   * Gestiona los vehículos.
   *
   * @return \Illuminate\View\View
   */
  public function gestionarVehiculos()
  {
    $vehiculos = Vehiculo::all();
    return view('admin.gestionar_vehiculos', compact('vehiculos'));
  }

  /**
   * Crea un nuevo vehículo.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function crearVehiculo(Request $request)
  {
    $request->validate([
      'Descripción' => 'required|string|max:100',
      'email_conductor' => 'required|email|max:100',
      'password' => 'required|string|min:6',
    ]);

    try {
      Vehiculo::create([
        'Descripción' => $request->Descripción,
        'email_conductor' => $request->email_conductor,
        'password' => Hash::make($request->password),
      ]);

      return redirect()->route('admin.vehiculos.index')
        ->with('success', 'Vehículo creado con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al crear vehículo: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Actualiza un vehículo existente.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function actualizarVehiculo(Request $request, $id)
  {
    $request->validate([
      'Descripción' => 'required|string|max:100',
      'email_conductor' => 'required|email|max:100',
      'password' => 'nullable|string|min:100',
    ]);

    try {
      $vehiculo = Vehiculo::findOrFail($id);

      $vehiculo->Descripción = $request->Descripción;
      $vehiculo->email_conductor = $request->email_conductor;
      if ($request->filled('password')) {
        $vehiculo->password = Hash::make($request->password);
      }
      $vehiculo->save();

      return redirect()->route('admin.vehiculos.index')
        ->with('success', 'Vehículo actualizado con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al actualizar vehículo: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Elimina un vehículo.
   *
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function eliminarVehiculo($id)
  {
    try {
      $vehiculo = Vehiculo::findOrFail($id);

      // Verificar si tiene reservas asociadas
      $reservasAsociadas = Reserva::where('id_vehiculo', $id)->exists();
      if ($reservasAsociadas) {
        return back()->with('error', 'No se puede eliminar el vehículo porque tiene reservas asociadas');
      }

      $vehiculo->delete();
      return redirect()->route('admin.vehiculos.index')
        ->with('success', 'Vehículo eliminado con éxito');
    } catch (\Exception $e) {
      return back()->with('error', 'Error al eliminar vehículo: ' . $e->getMessage());
    }
  }

  /**
   * Gestiona las zonas.
   *
   * @return \Illuminate\View\View
   */
  public function gestionarZonas()
  {
    $zonas = Zona::all();
    return view('admin.gestionar_zonas', compact('zonas'));
  }

  /**
   * Crea una nueva zona.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function crearZona(Request $request)
  {
    $request->validate([
      'descripcion' => 'required|string|max:100',
    ]);

    try {
      Zona::create([
        'descripcion' => $request->descripcion,
      ]);

      return redirect()->route('admin.zonas.index')
        ->with('success', 'Zona creada con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al crear zona: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Actualiza una zona existente.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function actualizarZona(Request $request, $id)
  {
    $request->validate([
      'descripcion' => 'required|string|max:100',
    ]);

    try {
      $zona = Zona::findOrFail($id);

      $zona->descripcion = $request->descripcion;
      $zona->save();

      return redirect()->route('admin.zonas.index')
        ->with('success', 'Zona actualizada con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al actualizar zona: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Elimina una zona.
   *
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function eliminarZona($id)
  {
    try {
      $zona = Zona::findOrFail($id);

      // Verificar si tiene hoteles asociados
      $hotelesAsociados = Hotel::where('id_zona', $id)->exists();
      if ($hotelesAsociados) {
        return back()->with('error', 'No se puede eliminar la zona porque tiene hoteles asociados');
      }

      $zona->delete();
      return redirect()->route('admin.zonas.index')
        ->with('success', 'Zona eliminada con éxito');
    } catch (\Exception $e) {
      return back()->with('error', 'Error al eliminar zona: ' . $e->getMessage());
    }
  }

  /**
   * Gestiona los tipos de reserva.
   *
   * @return \Illuminate\View\View
   */
  public function gestionarTipos()
  {
    // Obtener tipos con conteo de reservas asociadas
    $tiposReserva = TipoReserva::withCount('reservas')->get();
    return view('admin.gestionar_tipos_reserva', compact('tiposReserva'));
  }

  /**
   * Crea un nuevo tipo de reserva.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function crearTipo(Request $request)
  {
    $request->validate([
      'Descripción' => 'required|string|max:100',
    ]);

    try {
      TipoReserva::create([
        'Descripción' => $request->Descripción,
      ]);

      return redirect()->route('admin.tipos-reserva.index')
        ->with('success', 'Tipo de reserva creado con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al crear tipo de reserva: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Actualiza un tipo de reserva existente.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function actualizarTipo(Request $request, $id)
  {
    $request->validate([
      'Descripción' => 'required|string|max:100',
    ]);

    try {
      $tipo = TipoReserva::findOrFail($id);

      $tipo->Descripción = $request->Descripción;
      $tipo->save();

      return redirect()->route('admin.tipos-reserva.index')
        ->with('success', 'Tipo de reserva actualizado con éxito');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al actualizar tipo de reserva: ' . $e->getMessage(),
      ])->withInput();
    }
  }

  /**
   * Elimina un tipo de reserva.
   *
   * @param  int  $id
   * @return \Illuminate\Http\RedirectResponse
   */
  public function eliminarTipo($id)
  {
    try {
      $tipo = TipoReserva::findOrFail($id);

      // Verificar si tiene reservas asociadas
      $reservasAsociadas = Reserva::where('id_tipo_reserva', $id)->exists();
      if ($reservasAsociadas) {
        return back()->with('error', 'No se puede eliminar el tipo de reserva porque tiene reservas asociadas');
      }

      $tipo->delete();
      return redirect()->route('admin.tipos')
        ->with('success', 'Tipo de reserva eliminado con éxito');
    } catch (\Exception $e) {
      return back()->with('error', 'Error al eliminar tipo de reserva: ' . $e->getMessage());
    }
  }


  public function gestionarPrecios()
  {
    $precios = Precio::with(['zona', 'vehiculo', 'tipoReserva'])->get();
    $zonas = Zona::all();
    $vehiculos = Vehiculo::all();
    $tiposReserva = TipoReserva::all();

    return view('admin.gestionar_precios', compact('precios', 'zonas', 'vehiculos', 'tiposReserva'));
  }


  //crear nuevo precio
  public function crearPrecio(Request $request)
  {
    $request->validate([
      'id_zona' => 'required|exists:transfer_zona,id_zona',
      'id_vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
      'id_tipo_reserva' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
      'precio' => 'required|numeric|min:0',
    ]);

    Precio::create($request->only(['id_zona', 'id_vehiculo', 'id_tipo_reserva', 'precio']));

    return redirect()->back()->with('success', 'Precio creado correctamente.');
  }

  // Modificar precio
  public function actualizarPrecio(Request $request, $id)
  {
    $request->validate([
      'id_zona' => 'required|exists:transfer_zona,id_zona',
      'id_vehiculo' => 'required|exists:transfer_vehiculo,id_vehiculo',
      'id_tipo_reserva' => 'required|exists:transfer_tipo_reserva,id_tipo_reserva',
      'precio' => 'required|numeric|min:0',
    ]);

    $precio = Precio::findOrFail($id);
    $precio->update($request->only(['id_zona', 'id_vehiculo', 'id_tipo_reserva', 'precio']));

    return redirect()->back()->with('success', 'Precio actualizado correctamente.');
  }

  //Borrar precio
  public function eliminarPrecio($id)
  {
    $precio = Precio::findOrFail($id);
    $precio->delete();

    return redirect()->back()->with('success', 'Precio eliminado correctamente.');
  }


}
