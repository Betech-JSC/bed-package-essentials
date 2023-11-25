const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
    mode: "jit",
    content: [
        "./resources/Backend/js/**/*.blade.php",
        "./resources/Backend/js/**/*.vue",
        "./packages/betech-package-essentials/core/resources/Backend/js/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#FF6F00",
                    dark: "#E56400",
                    darker: "#DB5F00",
                },

                gray: {
                    ...defaultTheme.colors.gray,
                    800: "#1c1c1c",
                    900: "#111111",
                },
            },
            fontSize: {
                "2xs": ["0.55rem", { lineHeight: "0.75rem" }],
            },
        },
    },
    plugins: [],
};
