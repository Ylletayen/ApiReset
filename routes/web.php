<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentinelController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('sentinel')->group(function () {
    Route::get('/', [SentinelController::class, 'index'])->name('sentinel.index');
    Route::post('/store', [SentinelController::class, 'store'])->name('sentinel.store');
    Route::post('/settings', [SentinelController::class, 'updateSettings'])->name('sentinel.settings.update');
    Route::get('/export', [SentinelController::class, 'exportCsv'])->name('sentinel.export');
    Route::post('/train', [SentinelController::class, 'trainModel'])->name('sentinel.train');
    Route::post('/model/upload', [SentinelController::class, 'uploadModel'])->name('sentinel.model.upload');
    Route::get('/model/export', [SentinelController::class, 'exportModel'])->name('sentinel.model.export');
});