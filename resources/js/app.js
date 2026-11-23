// Muat konfigurasi browser bersama dan pengaturan HTTP.
import './bootstrap';

// Daftarkan Alpine sebagai sistem antarmuka interaktif.
import Alpine from 'alpinejs';

// Pasang Alpine agar komponen Blade dan skrip sebaris dapat mengenalinya.
window.Alpine = Alpine;

// Jalankan Alpine setelah komponen terdaftar.
Alpine.start();
