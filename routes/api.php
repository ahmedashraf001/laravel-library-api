<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthorController;

// v1 API routes
 Route::prefix('v1')->group(function(){
    // Authors Resource
    Route::get('authors',[AuthorController::class,'index']);   
    Route::post('authors',[AuthorController::class,'store']);
    Route::get('authors/{id}' , [AuthorController::class,'show']);
    Route::put('authors/{id}', [AuthorController::class,'update']);   
    Route::delete('authors/{id}', [AuthorController::class,'destroy']);
 });

