<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [App\Http\Controllers\ImageController::class, 'index'])->name('images.index');
Route::post('/images/upload', [App\Http\Controllers\ImageController::class, 'upload'])->name('images.upload');
Route::post('/generate-pdf', [App\Http\Controllers\ImageController::class, 'generatePdf'])->name('generate-pdf');

