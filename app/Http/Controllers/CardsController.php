<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CardsController extends Controller
{
    public function searchCard(Request $request){
    	/*Log::info('Inicio búsqueda');
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
    }*/

        Log::info('Inicio validación');
        $respuesta = ["status" => 1, "msg" => ""];
        $validator = Validator::make($request->all(), ['Name' => 'required|max:30']);
        //$validator = Validator::make($req->all(), ['input' => 'required|max:30']);
        if($validator->fails()){
            Log::warning('Nombre o parámetro incorrecto');
            $respuesta['status'] = 0;
            $respuesta['msg'] = $validator->errors();
            //$respuesta['msg'] = "hola";
        }else{
            Log::info('Proceso validación correcto');

            try{
                //$datos = $req-> getContent();
                //$datos = json_decode($datos);
                Log::info('Inicio de búsqueda');
                $input = DB::table('cards')
                        ->select(['id','Name','Description'])
                        ->where('Name', 'like', '%'.$request->Name.'%')
                        ->get();
                        Log::info('Búsqueda finalizada con los parámetros introducidos');
                if($input->isEmpty()){
                    $respuesta['status'] = 0;
                    $respuesta['msg'] = "Nombre incorrecto o no existe";
                    Log::warning('Nombre incorrecto o no existe');
                } else {

                    $respuesta['status'] = 1;
                    $respuesta['msg'] = "Nombre correcto";
                    $respuesta = $input;
                    Log::info('Nombre correcto, se procede la obtención de datos');
                }

            }catch(\Exception $e){
                Log::error('Error en la búsqueda');
                $respuesta['status'] = 0;
                $respuesta['msg'] = "Se ha producido un error: ".$e->getMessage();
                //$respuesta['msg'] = "adios";
            }



        }

        Log::info('Proceso finalizado, se muestran resultados');
        return response()->json($respuesta);
        Log::debug($respuesta);
    }
}
