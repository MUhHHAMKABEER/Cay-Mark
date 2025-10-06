import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Axios config
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Make Pusher available globally
window.Pusher = Pusher;

// Laravel Echo setup
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,   // comes from .env
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER, // comes from .env
    forceTLS: true, // if you want https, set to false for local dev
});
