<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/send-voice', [VoiceController::class, 'sendVoiceMessage']);

Route::get('/walkie-talkie', function () {
    return view('walkie_talkie');
});
