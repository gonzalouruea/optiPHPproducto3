<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            \Log::error('Acceso no autorizado: usuario no autenticado', [
                'requested_url' => $request->url()
            ]);
            return redirect()->route('login')->with('error', 'Por favor, inicia sesión primero.');
        }

        $user = Auth::user();
        \Log::info('Verificando rol de usuario', [
            'user_id' => $user->id,
            'user_rol' => $user->rol,
            'requested_url' => $request->url()
        ]);

        if ($user->rol !== 'admin') {
            \Log::error('Acceso denegado: usuario no es admin', [
                'user_id' => $user->id,
                'user_rol' => $user->rol,
                'requested_url' => $request->url()
            ]);
            return redirect()->route('usuario.dashboard')->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
        }

        \Log::info('Acceso permitido: admin', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'requested_url' => $request->url()
        ]);

        return $next($request);
    }
}
