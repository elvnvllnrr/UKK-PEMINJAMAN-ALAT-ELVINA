import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // Pindai Blade dan view Laravel agar class CSS yang dipakai ikut dibuat.
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Gunakan Figtree sebagai sans-serif utama dengan fallback bawaan Tailwind.
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    // Aktifkan styling form bawaan Tailwind.
    plugins: [forms],
};
