<?php

use App\Http\Controllers\ArticleCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('article-category')->group(function(){
  Route::get('/list',[ArticleCategoryController::class,'index']);
  Route::get('/detail/{id}',[ArticleCategoryController::class,'show']);
  Route::post('/create',[ArticleCategoryController::class,'store']);
  Route::put('update/{id}',[ArticleCategoryController::class,'update']);
  Route::delete('/delete/{id}',[ArticleCategoryController::class,'destroy']);
});
