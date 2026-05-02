//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */
//import 'laravel-datatables-vite';
import './bootstrap';
import './echo';
import Alpine from 'alpinejs'; // <-- Tambahin ini
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';

window.Alpine = Alpine;
Alpine.start(); // <-- Jalankan


