<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleCorporativo
{
  /**
   * Permite el paso solo a usuarios con rol 'corporativo'.
   */
  public function handle($request, Closure $next)
  {
    if (!Auth::check() || Auth::user()->rol !== 'corporativo') {
      abort(403); // Forbidden
    }

    return $next($request);
  }
}
