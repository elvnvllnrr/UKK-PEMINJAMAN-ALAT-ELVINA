// Konfigurasikan klien HTTP Axios yang digunakan bersama.
import axios from 'axios';
// Sediakan Axios untuk skrip aplikasi.
window.axios = axios;

// Tandai permintaan browser sebagai AJAX agar server dapat memprosesnya dengan tepat.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
