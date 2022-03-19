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

        $respuesta = ["status" => 1, "msg" => ""];

        if(isset($request->api_token)){

            $apitoken = $request->api_token;

            if($user = User::where('api_token',$apitoken)->first()){
                
                $user = User::where('api_token',$apitoken)->first();

                $respuesta["msg"] = "Api token valido";
                $request->user = $user;

                return $next($request);

            }else{

                $respuesta["msg"] = "token incorrecto o no existe";
            }

        }else{
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Error en el apitoken";
        }

        return response()->json($respuesta);
    }
}



