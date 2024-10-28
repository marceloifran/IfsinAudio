<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walkie-Talkie</title>
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script src="{{ mix('js/app.js') }}" defer></script> --}}
</head>

<body>
    <div id="app">
        <h1>Walkie-Talkie</h1>

        <!-- Grabación y Envío de Mensaje de Voz -->
        <section>
            <h2>Grabar y Enviar Mensaje de Voz</h2>
            <button id="startRecording" onclick="startRecording()">Comenzar Grabación</button>
            <button id="stopRecording" onclick="stopRecording()" disabled>Detener Grabación</button>
            <p id="recordingStatus">Estado: Esperando...</p>
        </section>

        <!-- Reproducción de Mensajes de Voz -->
        <section>
            <h2>Mensajes de Voz Recibidos</h2>
            <ul id="receivedMessages"></ul>
        </section>
    </div>

    <script>
        let mediaRecorder;
        let audioChunks = [];

        // Iniciar grabación
        function startRecording() {
            navigator.mediaDevices.getUserMedia({
                    audio: true
                })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();

                    document.getElementById('recordingStatus').innerText = "Estado: Grabando...";
                    document.getElementById('startRecording').disabled = true;
                    document.getElementById('stopRecording').disabled = false;

                    mediaRecorder.ondataavailable = event => {
                        audioChunks.push(event.data);
                    };
                })
                .catch(error => console.error('Error al acceder al micrófono:', error));
        }

        // Detener grabación y enviar mensaje
        function stopRecording() {
            mediaRecorder.stop();
            document.getElementById('recordingStatus').innerText = "Estado: Enviando...";
            document.getElementById('startRecording').disabled = false;
            document.getElementById('stopRecording').disabled = true;

            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, {
                    type: 'audio/webm'
                });
                const formData = new FormData();
                formData.append('audio', audioBlob, 'voice-message.webm');

                fetch('/send-voice', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Mensaje de voz enviado:', data.message);
                        document.getElementById('recordingStatus').innerText = "Estado: Esperando...";
                    })
                    .catch(error => console.error('Error al enviar mensaje de voz:', error));

                audioChunks = [];
            };
        }

        // Configurar Laravel Echo para escuchar mensajes de voz en tiempo real
        Echo.channel('wakie-talkie')
            .listen('.voice-message', (event) => {
                const messageUrl = `/storage/${event.message}`;

                // Crear un elemento de audio para reproducir el mensaje
                const listItem = document.createElement('li');
                const audio = document.createElement('audio');
                audio.src = messageUrl;
                audio.controls = true;

                listItem.appendChild(audio);
                document.getElementById('receivedMessages').appendChild(listItem);
            });
    </script>
</body>

</html>
