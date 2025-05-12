<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Viajero;
use App\Models\Zona;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use App\Models\Reserva;

class AuthController extends Controller
{
  /**
   * Muestra el formulario de inicio de sesión.
   *
   * @return \Illuminate\View\View
   */
  public function showLoginForm()
  {
    return view('auth.login');
  }

  /**
   * Maneja el intento de inicio de sesión.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    // → Buscar al viajero
    $viajero = Viajero::where('email', $credentials['email'])->first();

    if ($viajero && Hash::check($credentials['password'], $viajero->password)) {

      Auth::login($viajero);               // ← LOGIN
      // -----------------------------------------
      // ⬇️ NUEVO  bloque de redirección según rol
      switch ($viajero->rol) {
        case 'corporativo':
          return redirect()->route('hotel.dashboard');   // panel hotel
        case 'admin':
          return redirect()->route('admin.panel');       // panel admin
        default:                                          // usuario normal
          return redirect()->route('dashboard');
      }
      // -----------------------------------------

    }

    /* Si no pasó la autenticación */
    return back()->withErrors([
      'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ])->withInput($request->except('password'));
  }


  /**
   * Cierra la sesión del usuario.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
  }

  /**
   * Muestra el formulario de registro.
   *
   * @return \Illuminate\View\View
   */
  public function showRegistrationForm()
  {
    $zonas = Zona::all();
    return view('auth.register', compact('zonas'));
  }

  /**
   * Maneja el registro de un nuevo usuario.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function register(Request $request)
  {
    // 1) Validación básica de TODOS los campos de registro
    $request->validate([
      'nombre' => ['required', 'string', 'max:100'],
      'apellido1' => ['required', 'string', 'max:100'],
      'apellido2' => ['required', 'string', 'max:100'],
      'direccion' => ['required', 'string', 'max:100'],
      'codPostal' => ['required', 'string', 'max:100'],
      'ciudad' => ['required', 'string', 'max:100'],
      'pais' => ['required', 'string', 'max:100'],
      'email' => ['required', 'string', 'email', 'max:100', 'unique:transfer_viajeros,email'],
      'password' => ['required', 'string', 'min:8'],
      'rol' => ['sometimes', 'string', Rule::in(['admin', 'usuario', 'corporativo'])],

      // ➡️ validamos ZONA y COMISIÓN **solo** si es admin creando un corporativo
      'id_zona' => ['required_if:rol,corporativo', 'exists:transfer_zona,id_zona'],
      'comision' => ['required_if:rol,corporativo', 'numeric', 'min:0'],
    ]);

    // 2) Extraemos los datos
    $data = $request->only([
      'nombre',
      'apellido1',
      'apellido2',
      'direccion',
      'codPostal',
      'ciudad',
      'password',
      'pais',
      'email',
      'rol',
      'id_zona',
      'comision'
    ]);
    $data['rol'] = $data['rol'] ?? 'usuario';
    $data['codigoPostal'] = $data['codPostal'];
    $data['password'] = Hash::make($data['password']);

    // 3) En una transacción: si es un corporativo, primero creamos hotel y guardamos su ID
    DB::transaction(function () use ($data, &$viajero) {
      if ($data['rol'] === 'corporativo') {
        $hotel = Hotel::create([
          'id_zona' => $data['id_zona'],
          'descripcion' => $data['nombre'] . ' (Hotel)',
          'comision' => $data['comision'],
          'Usuario' => $data['email'],
          'password' => $data['password'],  // oye, ¡mejor cambiar luego!
        ]);
        $data['id_hotel'] = $hotel->id_hotel;
      }

      // 4) Creamos el usuario/viajero ya con id_hotel si correspondía
      $viajero = Viajero::create($data);
    });

    // 5) Redirigimos
    return redirect()->route('login')
      ->with('success', 'Usuario creado con éxito.');
  }


  /**
   * Muestra el perfil del usuario.
   *
   * @return \Illuminate\View\View
   */
  public function showCambiarDatos()
  {
    $user = Auth::user();

    // Obtener estadísticas básicas para el usuario
    // app/Http/Controllers/AuthController.php  (método showCambiarDatos)

    $stats = [
      'reservas_totales' => $user->reservas()->count(),

      // reservas con llegada hoy o en el futuro
      'reservas_activas' => $user->reservas()
        ->whereDate('fecha_entrada', '>=', today())
        ->count(),

      // reservas que llegarán en los próximos 7 días
      'reservas_proximas' => $user->reservas()
        ->whereBetween('fecha_entrada', [today(), today()->addDays(7)])
        ->count(),
    ];


    return view('auth.perfil', compact('user', 'stats'));
  }

  /**
   * Muestra el formulario para editar datos personales.
   *
   * @return \Illuminate\View\View
   */
  public function editarPerfil()
  {
    $user = Auth::user();
    return view('auth.cambiar_datos', compact('user'));
  }

  /**
   * Actualiza los datos personales del usuario.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function cambiarDatos(Request $request)
  {
    $user = Auth::user();
    $oldEmail = $user->email;

    $request->validate([
      'nombre' => ['required', 'string', 'max:100'],
      'apellido1' => ['required', 'string', 'max:100'],
      'apellido2' => ['required', 'string', 'max:100'],
      'email' => [
        'required',
        'string',
        'email',
        'max:100',
        Rule::unique('transfer_viajeros', 'email')->ignore($user->id_viajero, 'id_viajero')
      ],
      'password' => ['nullable', 'string', 'min:8'],
    ]);

    // Actualizar datos básicos
    $user->nombre = $request->nombre;
    $user->apellido1 = $request->apellido1;
    $user->apellido2 = $request->apellido2;
    $user->email = $request->email;

    // Actualizar contraseña si se proporciona
    if ($request->filled('password')) {
      $user->password = Hash::make($request->password);
    }

    try {
      $user->save();

      // Actualizar la sesión si el email ha cambiado
      if ($oldEmail != $request->email) {
        session(['email' => $request->email]);
      }

      return back()->with('success', 'Datos modificados correctamente');
    } catch (\Exception $e) {
      return back()->withErrors([
        'error' => 'Error al actualizar datos: ' . $e->getMessage(),
      ]);
    }
  }
}
