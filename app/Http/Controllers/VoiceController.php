<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\VoiceMessageReceived;

class VoiceController extends Controller
{
    public function sendVoiceMessage(Request $request)
    {
        // Almacena el archivo de audio temporalmente
        $audio = $request->file('audio');
        $audioPath = $audio->store('voice_messages');

        // Emite el evento para los clientes conectados
        broadcast(new VoiceMessageReceived($audioPath));

        return response()->json(['message' => 'Voice message sent successfully']);
    }
}
