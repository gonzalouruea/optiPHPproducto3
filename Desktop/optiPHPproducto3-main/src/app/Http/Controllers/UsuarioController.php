<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;
use App\Models\Usuario;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'usuario']);
    }

    public function dashboard()
    {
        $usuario = Auth::user();
        $reservas = Reserva::where('email_cliente', $usuario->email)
            ->orderBy('fecha_entrada', 'desc')
            ->get();

        // Calcular si las reservas pueden ser modificadas/canceladas
        foreach ($reservas as $reserva) {
            $fechaEntrada = Carbon::parse($reserva->fecha_entrada);
            $now = Carbon::now();
            $reserva->editable = $fechaEntrada->diffInHours($now) > 48;
        }

        return view('usuario.dashboard', compact('usuario', 'reservas'));
    }

    public function editarPerfil()
    {
        $usuario = Auth::user();
        return view('usuario.editar-perfil', compact('usuario'));
    }

    public function actualizarPerfil()
    {
        $usuario = Auth::user();
        
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        
        if ($validated['password']) {
            $usuario->password = bcrypt($validated['password']);
        }

        $usuario->save();

        return redirect()->route('usuario.dashboard')->with('success', 'Perfil actualizado correctamente');
    }

    public function crearReserva()
    {
        return view('usuario.crear-reserva');
    }

    public function storeReserva()
    {
        $usuario = Auth::user();
        $now = Carbon::now();
        
        $validated = request()->validate([
            'fecha_salida' => 'required|date|after:' . $now->addHours(48)->format('Y-m-d'),
            'fecha_entrada' => 'required|date|after:fecha_salida',
            'hotel_id' => 'required|exists:transfer_hotel,id',
            'tipo_reserva_id' => 'required|exists:transfer_tipo_reserva,id',
            'email' => 'required|email|unique:transfer_reservas,email',
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

        $validated['email'] = $usuario->email;
        $validated['usuario_id'] = $usuario->id;
        $validated['fecha_creacion'] = $now;
        $validated['fecha_modificacion'] = $now;

        Reserva::create($validated);

        return redirect()->route('usuario.dashboard')->with('success', 'Reserva creada correctamente');
    }
}
