<?php

use App\Http\Controllers\Api\VisitorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/enter', [VisitorController::class, 'enter']);
  });
  
