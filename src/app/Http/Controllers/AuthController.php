<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Viajero;

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

        // Intentar autenticar con el modelo Viajero
        $viajero = Viajero::where('email', $credentials['email'])->first();

        if ($viajero && Hash::check($credentials['password'], $viajero->password)) {
            // Autenticación manual
            Auth::login($viajero);
            
            // Guardar información adicional en la sesión
            session([
                'email' => $viajero->email,
                'id_viajero' => $viajero->id_viajero,
                'rol' => $viajero->rol,
                'admin' => ($viajero->rol == 'admin') ? 1 : 0
            ]);

            return redirect()->intended(route('dashboard'));
        }

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
        return view('auth.register');
    }

    /**
     * Maneja el registro de un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
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
        ]);

        $data = $request->all();
        $data['rol'] = $data['rol'] ?? 'usuario';
        $data['codigoPostal'] = $data['codPostal']; // Mapeo de nombres de campo
        $data['password'] = Hash::make($data['password']);

        try {
            $viajero = Viajero::create($data);
            
            // Redireccionar con mensaje de éxito
            return redirect()->route('login')
                ->with('success', 'El usuario ha sido creado con éxito. Ahora inicia sesión.');
        } catch (\Exception $e) {
            // Si hay un error (por ejemplo, email duplicado)
            return back()->withErrors([
                'email' => 'Error al crear usuario: ' . $e->getMessage(),
            ])->withInput($request->except('password'));
        }
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
        $stats = [
            'reservas_totales' => $user->reservas()->count(),
            'reservas_activas' => $user->reservas()->where('fecha', '>=', now())->count(),
            'reservas_proximas' => $user->reservas()->whereBetween('fecha', [now(), now()->addDays(7)])->count()
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
