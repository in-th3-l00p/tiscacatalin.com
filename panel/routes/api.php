<?php

use App\Http\Controllers\Api\ApiCommandController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/command/{command}', [
        ApiCommandController::class,
        'executeCommand'
    ]);
});
