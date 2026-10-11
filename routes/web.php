<?php

use App\Http\Controllers\PenalCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PenalCodeController::class, 'index'])->name('penal-code.index');
Route::get('/pasal.json', [PenalCodeController::class, 'data'])->name('penal-code.data');
