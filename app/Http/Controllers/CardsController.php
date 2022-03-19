<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\User;
use App\Models\Collection;
use App\Models\CardCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\SellCard;

class CardsController extends Controller
{
    
  

    public function CreateCollection(Request $req)
    {
        $respuesta = ['status' => 1, 'msg' => ''];
    
        $validator = Validator::make(json_decode($req->getContent(), true), [
            'Name' => ['required', 'max:50'],
            'Symbol' => ['required', 'max:100'],
            'LaunchDate' => ['required', 'date'],
            'Card' => ['required']
        ]);
        
        if ($validator->fails()) {
            $respuesta['status'] = 0;
            $respuesta['msg'] = $validator->errors();
        } else {

            $datos = $req -> getContent();
            $datos = json_decode($datos); 
            $cardCollect =[];

            foreach ($datos->Card as $newCard) {
                if(isset($newCard->id)){
                    $Card = Carta::where('id','=',$newCard->id)->first();
                    if($Card){
                        array_push($cardCollect,$Card->id);
                    }
                }elseif (isset($newCard->Name) && isset($newCard->Description)) {
                    $reNewCard = new Card();
                    $reNewCard->Name = $newCard->Name;
                    $reNewCard->Description = $newCard->Description;
    
                        try {
                            $reNewCard->save();
                            array_push($cardCollect,$reNewCard->id);
                            $respuesta['msg'] ='Carta guardada con id ' .$reNewCard->id;
                                        
                        } catch (\Exception $e) {
                            $respuesta['status'] = 0;
                             $respuesta['msg'] ='Se ha producido un error: ' . $e->getMessage();
                        }
                }else{
                    $respuesta['status'] = 0;
                    $respuesta['msg'] ='Los datos ingresados son erroneos';
                }  
            }
    
            if(!empty($cardCollect)){
                $cardIds = implode (", ",$cardCollect); 
                try{

                    $collection = new Collection();
                    $collection -> Name = $datos->Name;
                    $collection -> Symbol = $datos->Symbol;
                    $collection -> LaunchDate = $datos->LaunchDate;
                    $collection->save();
                    $respuesta["msg"] = "Generada Coleccion con id ".$collection->id;
                     
                    foreach($cartasCol as $id){
                        $cardCollection = new CardCollection();
                        $cardCollection->card_id = $id;
                        $cardCollection->collection_id = $collection->id;
                        $cardCollection->save();
                    }
                        $respuesta['msg'] ='Generada coleccion Numero: '.$cardCollection->id .' junto con las cartas con id: '.$cardIds;
                
                }catch (\Exception $e) {
                    $respuesta['status'] = 0;
                    $respuesta['msg'] ='Se ha producido un error: ' . $e->getMessage();
                }
            }
        }
        
        return response()->json($respuesta);
    }




    public function cardRegister(Request $req){
    	$respuesta = ["status" => 1, "msg" => ""];


    		$validator = validator::make(json_decode($req->getContent(),true), 
    			['Name' => 'required|unique:App\Models\Card|max:55', 
    			 'Description' => 'required|email|unique:App\Models\Card,email|max:100',
    			 'Collection' => ['required', 'integer'],

    			]);

    		if ($validator->fails()) {
    			$respuesta["status"] = 0;
    			$respuesta["msg"] = $validator->errors();
    			
    		}else{

                $datos = $req->getContent();
    			$datos = json_decode($datos);

                $Collection = Collection::where('id','=',$datos->Collection)->first();

                if ($Collection) {
                    $Card = new Carta();
                    $Card->Name = $datos->Name;
                    $Card->Description = $datos->Description;

    			

    			
                    try{
                        
                        $Card = new Card();
                        $Card->Name = $datos->Name;
                        $Card->Description = $datos->Description;
                        $cardCollection = new CardCollection();
                        $cardCollection->card_id = $Card->id;
                        $cardCollection->collection_id = $Collection->id;
                        $cardCollection->save();

                        $respuesta['msg'] ='Carta generada con id ' .$Card->id .' y asociada con la coleccion ' .$datos->Collection;
                        
                    }catch(\Exception $e){
                        $respuesta['status'] = 0;
                        $respuesta['msg'] = "Se ha producido un error ".$e->getMessage();
                    }

                } else {
                    $respuesta['status'] = 0;
                    $respuesta['msg'] = 'La coleccion es erronea o no existe';
                }
		    	
    		}
    		return response()->json($respuesta);
    }





    public function CardCollectionAsociation(Request $req)
    {
         $respuesta = ['status' => 1, 'msg' => ''];

         $validator = Validator::make(json_decode($req->getContent(), true), [
            'Card' => ['required'],
            'Collection' => ['required']
        ]);
    
        if ($validator->fails()) {
            $respuesta['status'] = 0;
            $respuesta['msg'] = $validator->errors();

        } else {

            $datos = $req -> getContent();
            $datos = json_decode($datos); 

            try{
                $Card = Card::where('id','=',$datos->Card)->first();
                $Collection = Collection::where('id','=',$datos->Collection)->first();
                if($Card && $Collection){
                    $cardCollection = new CardCollection();
                    $cardCollection->card_id = $datos->Card;
                    $cardCollection->collection_id = $datos->Collection;
                    $cardCollection->save();
                    
                    $respuesta['msg'] ='Generada coleccion Numero: '.$datos->Collection .' junto con la cartas con id: '.$datos->Card;
                }else {
                    $respuesta['status'] = 0;
                    $respuesta['msg'] ='Nombre de coleccion o carta erróneo o no existen';
                }
            }catch (\Exception $e) {
                $respuesta['status'] = 0;
                $respuesta['msg'] ='Se ha producido un error: ' . $e->getMessage();
            }
        }
        return response()->json($respuesta);
    }






    public function searchCard(Request $request){
        
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

    public function sellSearch(Request $search){

        $respuesta = ["status" => 1, "msg" => ""];
        try{

            if($search -> has('search')){

               $cards = SellCard::select(['card_id','Amount','Price','User'])
                        ->join('Users', 'Users.id', '=', 'card_sell.User')
                        ->join('Cards', 'Cards.id', '=', 'card_sell.card_id')
                        ->select('Cards.Name', 'card_sell.Amount', 'card_sell.Price', 'Users.Name as Seller')
                        ->where('Cards.Name','like','%'. $search -> input('search').'%')
                        ->orderBy('card_sell.Price','ASC')
                        ->get();                           
                        
                        $respuesta['datos'] = $cards;
            }else{
                $respuesta['msg'] = "Posible busqueda erronea";
            }
            
            
        }catch(\Exception $e){
            $respuesta['status'] = 0;
            $respuesta['msg'] = "Se ha producido un error: ".$e->getMessage();
        }
        return response()->json($respuesta);
    }

}
