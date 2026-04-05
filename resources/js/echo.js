import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 6001,
    forceTLS: false,
    enabledTransports: ['ws'],
});

// Listen to your event
window.Echo.channel('test')
    .listen('.TestEvent', (e) => {
        console.log('🎉 RECEIVED:', e.message);
        // SPA-friendly reload
        // window.location.reload();
    });

// Optional: listen to all events for debugging
window.Echo.channel('test')
    .listenToAll((event, e) => console.log('ALL EVENT:', event, e));
//////
window.Echo.channel('test')
    .listen('.TestEvent', (e) => {
        console.log('🎉 RECEIVED:', e.message);
        window.location.reload();
    });