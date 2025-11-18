<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
// v1 API routes
 Route::prefix('v1')->group(function(){
    // Authors Resource
    Route::get('authors',[AuthorController::class,'index']);   
    Route::post('authors',[AuthorController::class,'store']);
    Route::get('authors/{id}' , [AuthorController::class,'show']);
    Route::put('authors/{id}', [AuthorController::class,'update']);   
    Route::delete('authors/{id}', [AuthorController::class,'destroy']);

    // Books Resource
    Route::get('books',[BookController::class,'index']);
    Route::post('books',[BookController::class,'store']);
    Route::get('books/{id}',[BookController::class,'show']);
    Route::put('books/{id}',[BookController::class,'update']);
    Route::delete('books/{id}',[BookController::class,'destroy']);
 });

