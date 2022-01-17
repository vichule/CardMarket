<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function register(Request $req){
    	$respuesta = ["status" => 1, "msg" => ""];


    		$validator = validator::make(json_decode($req->getContent(),true), 
    			['Nombre' => 'required|unique:App\Models\User|max:55', 
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
    			$usuario->Nombre = $datos->Nombre;
		    	$usuario->Email = $datos->Email;
		    	$usuario->Password = Hash::make($datos->Password);
		    	$usuario->Salario = $datos->Salario;
		    	$usuario->PuestoTrabajo = $datos->PuestoTrabajo;
		    	$usuario->Biografia = $datos->Biografia;

		    	try{
		            
		    		$usuario->save();
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
						$respuesta["status"] = 0;
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

    	//Buscar email
    	$email = $datos->Email;
    	//Obtener el email y validarlo como login

		if($usuario = User::where('Email',$email)->first()){
			$usuario = User::where('Email',$email)->first();
			//Si encontramos al usuario 
			$usuario->api_token = null;

			//$newPassword = /*generarla aleatoriamente*/;

			$password = Str::random(8);
			
			$usuario->Password = Hash::make($password);
			$usuario->save();

			try{
				//Enviarla por email
				Mail::to($usuario->Email)->send(new OrderShipped($password));
				$respuesta["status"] = 0;
				$respuesta["msg"] = "Password enviada al email";
				
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
