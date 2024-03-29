<?php

use App\Http\Controllers\Api\FundsController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'funds'], static function () {
    Route::get('', [FundsController::class, 'index'])
        ->name('funds.index');
    Route::patch('{fund}', [FundsController::class, 'update'])
        ->name('funds.update');
    Route::get('duplicates', [FundsController::class, 'listDuplicates'])
        ->name('funds.list_duplicates');
});
