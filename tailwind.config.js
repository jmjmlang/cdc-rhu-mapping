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
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary — muted teal for headers, nav, primary actions
                primary: {
                    50:  '#f0f7f7',
                    100: '#d9ecec',
                    200: '#b5d9d9',
                    300: '#88c1c1',
                    400: '#5ea3a3',
                    500: '#438888',
                    600: '#376e6e',
                    700: '#305a5a',
                    800: '#2b4a4a',
                    900: '#273f3f',
                    950: '#132525',
                },
                // Status — soft, accessible, non-neon
                status: {
                    pending:  '#b08d3b',  // warm amber
                    approved: '#3d7a4a',  // forest green
                    rejected: '#a04040',  // muted red
                },
                // DSS risk levels
                risk: {
                    low:      '#3d7a4a',  // forest green
                    moderate: '#b08d3b',  // warm amber
                    high:     '#c06030',  // burnt orange
                    critical: '#a04040',  // muted red
                },
                // Surface colors
                surface: {
                    DEFAULT: '#f8f8f6',   // warm off-white background
                    card:    '#ffffff',
                    muted:   '#f0efed',
                },
            },
        },
    },

    plugins: [forms],
};
