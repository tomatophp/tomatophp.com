const colors = require("tailwindcss/colors");
export default {
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './Modules/**/resources/views/*.blade.php',
        './Modules/**/resources/views/**/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/views/vendor/pagination/*.blade.php',
    ],

    darkMode: "class", // or 'media' or 'class'
    theme: {
        fontFamily: {
            main: ["IBM Plex Sans Arabic"],
        },
        asideScrollbars: {
            light: "light",
            gray: "gray",
        },
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.rose,
                success: colors.green,
                warning: colors.amber,
                black: colors.black,
                main: colors.rose,
                secondary: colors.green
            },
            zIndex: {
                "-1": "-1",
            },
            flexGrow: {
                5: "5",
            },
            maxHeight: {
                "screen-menu": "calc(100vh - 3.5rem)",
                modal: "calc(100vh - 160px)",
            },
            transitionProperty: {
                position: "right, left, top, bottom, margin, padding",
                textColor: "color",
            },
            keyframes: {
                "fade-out": {
                    from: { opacity: 1 },
                    to: { opacity: 0 },
                },
                "fade-in": {
                    from: { opacity: 0 },
                    to: { opacity: 1 },
                },
            },
            animation: {
                "fade-out": "fade-out 250ms ease-in-out",
                "fade-in": "fade-in 250ms ease-in-out",
            },
        },
    },
}
