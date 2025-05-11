<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\Hotel;
use App\Models\Viajero;
use App\Models\Zona;
use App\Models\TipoReserva;

class AdminController extends Controller
{
  /**
   * Constructor del controlador.
   */
  public function __construct()
  {
    $this->middleware(['auth', 'admin']);
  }

  /**
   * Muestra el panel de administración.
   *
   * @return \Illuminate\View\View
   */
  public function panel()
  {
    // Cargamos estadísticas completas
    $stats = [
      'reservas_totales' => Reserva::count(),
      'reservas_hoy' => Reserva::whereDate('fecha_reserva', today())->count(),
      'hoteles' => Hotel::count(),
      'vehiculos' => Vehiculo::count(),
      'zonas' => Zona::count(),
      'tipos_reserva' => TipoReserva::count(),
      'usuarios' => Viajero::where('rol', 'usuario')->count(),
      'corporativos' => Viajero::where('rol', 'corporativo')->count(),
      'admins' => Viajero::where('rol', 'admin')->count(),
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
      'id_hotel' => 'required_if:rol,corporativo|nullable|exists:transfer_hotel,id_hotel',
    ]);

    Viajero::create([
      'nombre' => $request->nombre,
      'apellido1' => $request->apellido1,
      'apellido2' => $request->apellido2,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'rol' => $request->rol,
      'id_hotel' => $request->rol === 'corporativo' ? $request->id_hotel : null,
    ]);

    return back()->with('success', 'Usuario creado correctamente');
  }


  /**
   * Crea un nuevo hotel.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function crearHotel(Request $request)
  {
    $request->validate([
      'id_zona' => 'required|exists:transfer_zona,id_zona',
      'descripcion' => 'required|string|max:100',
      'Comision' => 'required|numeric',
      'Usuario' => 'required|string|max:100',
      'password' => 'required|string|min:6',
    ]);

    try {
      Hotel::create([
        'id_zona' => $request->id_zona,
        'descripcion' => $request->descripcion,
        'Comision' => $request->Comision,
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
      'Comision' => 'required|numeric',
      'Usuario' => 'required|string|max:100',
      'password' => 'nullable|string|min:6',
    ]);

    try {
      $hotel = Hotel::findOrFail($id);

      $hotel->id_zona = $request->id_zona;
      $hotel->descripcion = $request->descripcion;
      $hotel->Comision = $request->Comision;
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
      return redirect()->route('admin.hoteles')
        ->with('success', 'Hotel eliminado con éxito');
    } catch (\Exception $e) {
      return back()->with('error', 'Error al eliminar hotel: ' . $e->getMessage());
    }
  }

  /**
   * Muestra el formulario para editar el perfil del administrador.
   *
   * @return \Illuminate\View\View
   */
  public function editarPerfil()
  {
      $usuario = auth()->user();
      return view('admin.editar-perfil', compact('usuario'));
  }

  /**
   * Actualiza el perfil del administrador.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function actualizarPerfil(Request $request)
  {
      $usuario = auth()->user();

      $request->validate([
          'nombre' => 'required|string|max:255',
          'apellido1' => 'required|string|max:255',
          'apellido2' => 'nullable|string|max:255',
          'email' => 'required|email|max:255|unique:transfer_viajeros,email,' . $usuario->id_viajero,
          'password' => 'nullable|string|min:6|confirmed',
      ]);

      $usuario->update([
          'nombre' => $request->nombre,
          'apellido1' => $request->apellido1,
          'apellido2' => $request->apellido2,
          'email' => $request->email,
      ]);

      $usuario->update([
          'nombre' => $request->nombre,
          'apellido1' => $request->apellido1,
          'apellido2' => $request->apellido2,
          'email' => $request->email,
      ]);

      if ($request->filled('password')) {
          $usuario->password = Hash::make($request->password);
      }

      $usuario->save();

      return redirect()->route('admin.panel')
          ->with('success', 'Perfil actualizado correctamente');
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
      return redirect()->route('admin.vehiculos')
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

      return redirect()->route('admin.zonas')
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

      return redirect()->route('admin.zonas')
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
      return redirect()->route('admin.zonas')
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
}
