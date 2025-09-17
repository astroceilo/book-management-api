<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Logout    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // CRUD Buku
    Route::apiResource('books', BookController::class);

    // Loan & Return Buku
    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loans/borrow', [LoanController::class, 'borrow']);
    Route::post('/loans/return', [LoanController::class, 'return']);
    
    // // Add your protected API routes here
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
});