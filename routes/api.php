<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CardsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('users')->group(function(){

	Route::post('/login',[UsersController::class,'login']);
	Route::get('/recoverPass',[UsersController::class,'recoverPass']);
	Route::put('/register',[UsersController::class,'register']);
    Route::get('/sellSearch',[CardsController::class,'sellSearch']);

});


Route::middleware(['apitoken','permisos'])->prefix('users')->group(function(){

    Route::put('/cardRegister',[UsersController::class,'cardRegister']);
    Route::put('/CreateCollection',[CardsController::class,'CreateCollection']);
    Route::put('/CardCollectionAsociation',[CardsController::class, 'CardCollectionAsociation']);

});


Route::middleware(['apitoken','permisos2'])->prefix('users')->group(function(){
    Route::put('/cardPurchase',[UsersController::class,'cardPurchase']);
    Route::put('/cardSale',[UsersController::class,'cardSale']);
    Route::get('/searchCard',[CardsController::class,'searchCard']);
    

});