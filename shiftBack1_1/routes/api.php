<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\formulaController;
use App\Http\Controllers\shiftController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/selus', [userController::class, 'selus']);

Route::get('/orderstat/{date}/{type}', [orderController::class, 'orderCount']);

Route::get('/datatypeorder', [formulaController::class, 'dataType']);
Route::get('/dataprofession', [formulaController::class, 'dataProf']);
Route::get('/getidprof/{name}', [formulaController::class, 'selectProfession']); 
Route::post('/createformule', [formulaController::class, 'createFormule']);
Route::get('/avertime/{id}/{count_day}', [formulaController::class, 'averTime']);

Route::post('/createshift', [shiftController::class, 'createShifts']);
Route::get('/allformul', [formulaController::class, 'allFormul']);
Route::get('/shiftlist/{selectedDate}', [shiftController::class, 'shiftList']);
Route::get('/countday/{profession_id}', [shiftController::class, 'getCountDay']);