<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\BuyCard;
use App\Models\SellCard;

class UsersController extends Controller
{
    public function register(Request $req){
    	$respuesta = ["status" => 1, "msg" => ""];


    		$validator = validator::make(json_decode($req->getContent(),true), 
    			['Username' => 'required|unique:App\Models\User|max:55', 
    			 'Email' => 'required|email|unique:App\Models\User,email|max:30',
    			 'Password' => 'required|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}/',
    			 'Rol' => 'required|in:Particular,Profesional,Administrador'

    			]);

    		if ($validator->fails()) {
    			$respuesta["status"] = 0;
    			$respuesta["msg"] = $validator->errors();
    			
    		}else{

    			$datos = $req->getContent();
    			$datos = json_decode($datos);

    			$user = new User();
    			$user->Username = $datos->Username;
		    	$user->Email = $datos->Email;
		    	$user->Password = Hash::make($datos->Password);
		    	$user->Rol = $datos->Rol;

		    	try{
		            
		    		$user->save();
		    		$respuesta['status'] = 1;
		    		$respuesta['msg'] = "Usuario guardado con id ".$user->id;
		            
		    	}catch(\Exception $e){
		    		$respuesta['status'] = 0;
		    		$respuesta['msg'] = "Se ha producido un error ".$e->getMessage();
		    	}
		    	
    		}
    		return response()->json($respuesta);
    }



    public function login(Request $req){
		$respuesta = ["status" => 1, "msg" => ""];

		$datos = $req->getContent();
		$datos = json_decode($datos);

    	//Buscar email
    	$email = $datos->Email;

		
    	
		
		if($user = User::where('Email',$email)->first()){
			$user = User::where('Email',$email)->first();
			//comprobar contraseña
			if (Hash::check($datos->Password, $user->Password)) {
				//Todo correcto
	
				//Generar el api token
				do{
					$apitoken = Hash::make($user->id.now());
				}while (User::where('api_token', $apitoken)->first()); 
					
					$user->api_token = $apitoken;
					$user->save();
	
					try{
						$respuesta["status"] = 1;
						$respuesta["msg"] = "Login correcto, tu token es: ".$user->api_token;
						//return response()->json($apitoken);
						
					}catch(\Exception $e){
						$respuesta['status'] = 0;
						$respuesta['msg'] = "Se ha producido un error ".$e->getMessage();
					}
	
			}else{
				//Login mal
				
				$respuesta["status"] = 0;
				$respuesta["msg"] = "La contraseña no es correcta, pruebe de nuevo ".$user->Nombre;
			}

		}else{
			
				$respuesta["status"] = 0;
				$respuesta["msg"] = "El login ha fallado, pruebe de nuevo";
			
		}
    	return response()->json($respuesta);
		
		
    }

    public function recoverPass(Request $req){

		$respuesta = ["status" => 1, "msg" => ""];

		$datos = $req->getContent();
		$datos = json_decode($datos);

    	$email = $datos->Email;

		if($user = User::where('Email',$email)->first()){
			$user = User::where('Email',$email)->first();
			$user->api_token = null;

			

			$password = Str::random(8);
			
			$user->Password = Hash::make($password);
			$user->save();

			try{
				
				$respuesta["status"] = 1;
				$respuesta["msg"] = "New password: "($password);
				
			}catch(\Exception $e){
				$respuesta['status'] = 0;
				$respuesta['msg'] = "Se ha producido un error ".$e->getMessage();
			}

		}else{
			
				$respuesta["status"] = 0;
				$respuesta["msg"] = "El email es incorrecto o no existe";
			
		}
    	return response()->json($respuesta);	
    }


	public function cardPurchase(Request $req)
    {
        $respuesta = ['status' => 1, 'msg' => ''];


            $datos = $req->getContent();
            $datos = json_decode($datos);

            $user = User::where('api_token', '=', $req->api_token)->first();

            $card = Card::where('id','=',$datos->card)->first();

            if ($user) {
                try {
                    $cardBuy = new CartaCompra();
                    $cardBuy->card_id = $card->id;
                    $cardBuy->user_id = $user->id;
                    $cardBuy->save();
                    $respuesta['msg'] ='Carta comprada id: ' .$card->id;
                } catch (\Exception $e) {
                    $respuesta['status'] = 0;
                    $respuesta['msg'] = 'Se ha producido un error: ' . $e->getMessage();
                }
            } else {
                $respuesta['status'] = 0;
                $respuesta['msg'] = 'La carta introducida es erronea o no existe';
            }
        return response()->json($respuesta);
    }




    public function cardSale(Request $req)
    {
        $respuesta = ['status' => 1, 'msg' => ''];

        $validator = Validator::make(json_decode($req->getContent(), true),
        [
           'card_id' => ['required', 'integer'],
           'Amount' => ['required', 'integer'],
           'Price' => ['required', 'numeric','min:0','not_in:0'],

       ]);
        if ($validator->fails()) {

            $respuesta['status'] = 0;
            $respuesta['msg'] = $validator->errors();

        } else {

            $datos = $req -> getContent();
            $datos = json_decode($datos); 
            $user = User::where('api_token', '=', $req->api_token)->first();
            $cards = Card::select('id')                           
            ->where('id','=',$datos->card_id)
            ->get();

            if ($cards){

                $cardSale = new SellCard();
                $cardSale -> card_id = $datos -> card_id;
                $cardSale -> Amount = $datos -> Amount;
                $cardSale -> Price = $datos->Price;
                $cardSale -> User = $user->id;
                
                try {
                    $cardSale->save();
                    $respuesta['msg'] = "Se ha guardado la venta de la carta ".$cardSale->id;
                } catch (\Exception $e) {
                    $respuesta['status'] = 0;
                    $respuesta['msg'] ='Se ha producido un error: ' . $e->getMessage();
                }
    
            }else{
                $respuesta['msg'] =
                'La carta que ha introducido es erronea o no esta registrada';
                    
             }
        }
           
            

        return response()->json($respuesta);
    }

	
}
