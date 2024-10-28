<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walkie-Talkie</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
</head>
<body>
    <h1>Walkie-Talkie</h1>

    <button onclick="startRecording()">Start Recording</button>
    <button onclick="stopRecording()" disabled>Stop Recording</button>
    <button onclick="sendVoiceMessage()" disabled>Send Voice Message</button>

    <script>
        let recorder, audioBlob;

        // Función para iniciar la grabación de audio
        function startRecording() {
            try {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        console.log("Inicio de grabación de audio.");
                        recorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
                        recorder.start();
                        recorder.ondataavailable = e => {
                            audioBlob = e.data;
                            console.log("Audio grabado:", audioBlob);
                        };
                        document.querySelector('[onclick="stopRecording()"]').disabled = false;
                    }).catch(error => {
                        console.error("Error al obtener acceso al micrófono:", error);
                    });
            } catch (error) {
                console.error("Error en startRecording:", error);
            }
        }

        // Función para detener la grabación de audio
        function stopRecording() {
            try {
                recorder.stop();
                document.querySelector('[onclick="sendVoiceMessage()"]').disabled = false;
                console.log("Grabación detenida.");
            } catch (error) {
                console.error("Error en stopRecording:", error);
            }
        }

        // Función para enviar el mensaje de voz al servidor
        async function sendVoiceMessage() {
            try {
                const formData = new FormData();
                formData.append('audio', audioBlob, 'voice-message.webm');
                console.log("Enviando mensaje de voz al servidor...");

                const response = await fetch('/send-voice', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (response.ok) {
                    console.log("Mensaje de voz enviado exitosamente.");
                    alert("Mensaje de voz enviado");
                } else {
                    console.error("Error en la respuesta del servidor:", response.statusText);
                }
            } catch (error) {
                console.error("Error en sendVoiceMessage:", error);
            }
        }

        // Configuración de Pusher para recibir y reproducir mensajes de voz
        Pusher.logToConsole = true;
        const pusher = new Pusher('5f6362d8a1aefaf86a74', { cluster: 'us2' });
        const channel = pusher.subscribe('wakie-talkie');

        // Escuchar el evento de mensaje de voz y reproducirlo
        channel.bind('voice-message', function(data) {
            try {
                console.log("Evento de mensaje de voz recibido:", data);

                if (!data.audioUrl) {
                    console.error("No se encontró URL de audio en el evento.");
                    return;
                }

                const playButton = document.createElement("button");
                playButton.textContent = "Reproducir mensaje de voz";
                document.body.appendChild(playButton);

                playButton.onclick = () => {
                    const audio = new Audio(data.audioUrl);
                    audio.play()
                        .then(() => {
                            console.log("Reproducción de audio iniciada.");
                            playButton.remove();
                        })
                        .catch(error => {
                            console.error("Error al reproducir el audio:", error);
                        });

                    audio.onended = () => {
                        console.log("El mensaje de voz ha terminado de reproducirse.");
                    };
                };
            } catch (error) {
                console.error("Error al procesar el evento de mensaje de voz:", error);
            }
        });
    </script>
</body>
</html>
