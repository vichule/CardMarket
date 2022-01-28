<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    			$usuario = new User();
    			$usuario->Username = $datos->Username;
		    	$usuario->Email = $datos->Email;
		    	$usuario->Password = Hash::make($datos->Password);
		    	$usuario->Rol = $datos->Rol;

		    	try{
		            
		    		$usuario->save();
		    		$respuesta['status'] = 1;
		    		$respuesta['msg'] = "Usuario guardado con id ".$usuario->id;
		            
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

		//validar
    	//encontrar al usuario con ese email
    	
		
    	//Pasar validacion
		if($usuario = User::where('Email',$email)->first()){
			$usuario = User::where('Email',$email)->first();
			//comprobar contraseña
			if (Hash::check($datos->Password, $usuario->Password)) {
				//Todo correcto
	
				//Generar el api token
				do{
					$apitoken = Hash::make($usuario->id.now());
				}while (User::where('api_token', $apitoken)->first()); 
					
					$usuario->api_token = $apitoken;
					$usuario->save();
	
					try{
						$respuesta["status"] = 1;
						$respuesta["msg"] = "Login correcto, tu token es: ".$usuario->api_token;
						//return response()->json($apitoken);
						
					}catch(\Exception $e){
						$respuesta['status'] = 0;
						$respuesta['msg'] = "Se ha producido un error ".$e->getMessage();
					}
	
			}else{
				//Login mal
				
				$respuesta["status"] = 0;
				$respuesta["msg"] = "El login ha fallado, pruebe de nuevo ".$usuario->Nombre;
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

		if($usuario = User::where('Email',$email)->first()){
			$usuario = User::where('Email',$email)->first();
			$usuario->api_token = null;

			

			$password = Str::random(8);
			
			$usuario->Password = Hash::make($password);
			$usuario->save();

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
    }}
