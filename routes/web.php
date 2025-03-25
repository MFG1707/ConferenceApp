<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/inscription', [ParticipantController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [ParticipantController::class, 'store'])->name('inscription.store');

// Affichage du tableau de bord admin
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// Export en CSV
Route::get('/admin/export-csv', [AdminController::class, 'exportCSV'])->name('admin.export_csv');