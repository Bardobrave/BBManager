<?php

namespace App\Http\Middleware;

use Closure;

class checkAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*Este middleware almacena el identificador y el rol del usuario en
          sesión en dos variables del controlador que lo use*/
        if ($controller = $request->route()->controller) {
          $controller->userId = $request->user()->id;
          $controller->userRol = $request->user()->rol;
        }

        return $next($request);
    }
}
