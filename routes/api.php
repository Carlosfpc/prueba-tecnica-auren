<?php

use App\Http\Controllers\Api\CountryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{cca3}', [CountryController::class, 'show']);

