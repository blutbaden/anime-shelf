const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './public/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                anime: {
                    50:  '#f0f4ff',
                    100: '#dde8ff',
                    200: '#c3d4ff',
                    300: '#9ab5ff',
                    400: '#6a8eff',
                    500: '#4262ff',
                    600: '#2a41f5',
                    700: '#1f2fe0',
                    800: '#1c27b5',
                    900: '#1c268e',
                    950: '#111455',
                },
            },
            scale: {
                '-1': '-1',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
