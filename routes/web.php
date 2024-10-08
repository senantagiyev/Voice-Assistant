<?php

use App\Http\Controllers\VoiceCommandController;
use Illuminate\Support\Facades\Route;

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

Route::post('/voice-command', [VoiceCommandController::class, 'handleCommand']);
Route::get('/chat', [VoiceCommandController::class, 'chat'])->name('askView');
Route::post('/ask-qeuestion', [VoiceCommandController::class, 'askQuestions'])->name('ask');

