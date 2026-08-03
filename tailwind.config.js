import daisyui from 'daisyui';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
    ],
    plugins: [daisyui, forms],
    daisyui: {
        themes: ['light', 'dark'],
    },
};