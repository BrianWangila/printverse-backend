<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/import', [ProductImportController::class, 'showImportForm'])->name('admin.import.form');
    Route::post('/import', [ProductImportController::class, 'import'])->name('admin.import');
});