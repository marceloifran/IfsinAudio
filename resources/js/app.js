import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

Pusher.logToConsole = true;

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: true,
    disableStats: true,
});

window.Echo.channel('wakie-talkie')
    .listen('.voice-message', (event) => {
        console.log('Mensaje de voz recibido:', event.message);
        alert(`Mensaje de voz: ${event.message}`);
    });
