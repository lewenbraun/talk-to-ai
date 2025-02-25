<?php

declare(strict_types=1);

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    //
});

Route::get('/{any}', AppController::class)
    ->where('any', '.*')
    ->name('app');
