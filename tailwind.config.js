const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    modules: {
        appearance: ['responsive'],
        backgroundAttachment: ['responsive'],
        backgroundColors: ['responsive', 'hover'],
        backgroundPosition: ['responsive'],
        backgroundRepeat: ['responsive'],
        // ...
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                grotesk: ['HK Grotesk', ...defaultTheme.fontFamily.sans],
                proxima: ['Proxima Nova', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#00d49f',
                secondary: '#F01F1F'
            },
            backgroundColor: {
                primary: '#00d49f',
            },
            backgroundOpacity: {
                '10': '0.1',
                '20': '0.2'
            },
            letterSpacing: {
                tightest: '-.075em',
                tighter: '-.05em',
                tight: '-.025em',
                normal: '0',
                wide: '1px',
                wider: '2px',
                widest: '3px'
            }
        },
    },

    variants: {
        opacity: ['responsive', 'hover', 'focus', 'disabled']
    },

    plugins: [require('@tailwindcss/ui')],
};
