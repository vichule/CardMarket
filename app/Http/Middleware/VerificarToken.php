<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class VerificarToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user->PuestoTrabajo == 'Administrador'){
            return $next($request);

        }else{
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Permiso denegado";
        //Fallo
        }

        return response()->json($respuesta);
    }
}



