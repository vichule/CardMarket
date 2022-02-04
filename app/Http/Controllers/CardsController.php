<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardsController extends Controller
{
    public function searchCard(Request $req){
    	Log::info('Inicio búsqueda');
        $respuesta = ["status" => 1, "msg" => ""];
        $datos = $req-> getContent();
        $datos = json_decode($datos);
        Log::info('Procesacion datos completado');                                                                                                                       
        try{

            $card = DB::Table('cards');

            if ($datos->has('Name')) {
                Log::info('Procesando nombre');
                $card = DB::table('cards')
                ->where('Name', 'like', '%' .$datos->input('Name'). '%')
                ->get();
                $respuesta['status'] = 1;
                $respuesta['msg'] = "Se ha producido un error: ".$e->getMessage();
                //$respuesta['datos'] = $card;
                Log::info('Proceso completado');
        	} else {
                Log::warning('Nombre incorrecto');
                $respuesta['status'] = 0;
                $respuesta['Nombre incorrecto o no existe'];
        	}
            
        }catch(\Exception $e){
        	Log::error('Error en la búsqueda');
            $respuesta['status'] = 0;
            $respuesta['msg'] = "Se ha producido un error: ".$e->getMessage();
        }
        Log::info('Proceso finalizado');
        return response()->json($respuesta);
        Log::debug($respuesta);
    }
}
