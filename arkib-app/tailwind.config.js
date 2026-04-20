import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'uitm-purple': {
                    DEFAULT: '#6b21a8',
                    50:  '#faf5ff',
                    100: '#f3e8ff',
                    200: '#e9d5ff',
                    300: '#d8b4fe',
                    400: '#c084fc',
                    500: '#a855f7',
                    600: '#9333ea',
                    700: '#7e22ce',
                    800: '#6b21a8',
                    900: '#581c87',
                },
                'uitm-gold': {
                    DEFAULT: '#FDB913',
                    50:  '#fffaeb',
                    100: '#fff1c5',
                    200: '#ffe287',
                    300: '#ffcc49',
                    400: '#fdb913',
                    500: '#e09a08',
                    600: '#bb7405',
                    700: '#955608',
                    800: '#7a440f',
                    900: '#683910',
                },
            },
            boxShadow: {
                'uitm-sm': '0 1px 2px 0 rgba(107, 33, 168, 0.06)',
                'uitm':    '0 2px 6px -1px rgba(107, 33, 168, 0.10), 0 1px 3px -1px rgba(107, 33, 168, 0.06)',
                'uitm-md': '0 6px 14px -4px rgba(107, 33, 168, 0.14), 0 2px 6px -2px rgba(107, 33, 168, 0.08)',
                'uitm-lg': '0 14px 30px -8px rgba(107, 33, 168, 0.18), 0 6px 12px -4px rgba(107, 33, 168, 0.10)',
            },
        },
    },

    plugins: [forms],
};
