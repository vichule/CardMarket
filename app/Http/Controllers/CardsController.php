<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\DB;

class CardsController extends Controller
{
    public function searchCard(Request $req){
    	Log::info('Inicio búsqueda');
        $respuesta = ["status" => 1, "msg" => ""];
        $datos = $req-> getContent();
        $datos = json_decode($datos);
        Log::info('Procesacion datos completado')                                                                                                                         
        try{

            $card = DB::Table('card');

            if ($req->has('Name')) {
            Log::info('Procesando nombre')
            $card = Card::withCount('cards as cards')
            ->where('Name', 'like', '%' .$req->input('Name'). '%')
            ->get();
            $respuesta['datos'] = $card;
            Log::info('Proceso completado')
        	} else {
        	Log::warning('Nombre incorrecto')
        	$card = Card::withCount('cards as cards')->get();
            $respuesta['datos'] = $card;
        	}
            
        }catch(\Exception $e){
        	Log::error('Error en la búsqueda');
            $respuesta['status'] = 0;
            $respuesta['msg'] = "Se ha producido un error: ".$e->getMessage();
        }
        Log::info('Proceso finalizado')
        return response()->json($respuesta);
        Log::debug($respuesta);
    }
}
