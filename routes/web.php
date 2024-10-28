<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\VoiceMessageReceived;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\VoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/send-voice', function (Request $request) {
    $request->validate([
        'audio' => 'required|file|mimetypes:audio/webm'
    ]);

    $audioPath = $request->file('audio')->store('voices', 'public');
    $audioUrl = Storage::url($audioPath);

    // Agregar log para verificar
    \Log::info("Evento VoiceMessageReceived emitido con URL: {$audioUrl}");

    broadcast(new VoiceMessageReceived($audioUrl));

    return response()->json(['status' => 'Mensaje de voz enviado', 'audioUrl' => $audioUrl]);
});



Route::get('/walkie-talkie', function () {
    return view('walkie_talkie');
});

Route::get('/trigger-voice-message', function () {
    broadcast(new VoiceMessageReceived("Este es un mensaje de prueba desde Laravel!"));
    return 'Evento VoiceMessageReceived Emitido!';
});
