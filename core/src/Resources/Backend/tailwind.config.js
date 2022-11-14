module.exports = {
    mode: "jit",
    content: [
        "./resources/Backend/js/**/*.blade.php",
        "./resources/Backend/js/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#FF6F00",
                    dark: "#E56400",
                    darker: "#DB5F00",
                },
            },
            fontSize: {
                "2xs": ["0.55rem", { lineHeight: "0.75rem" }],
            },
        },
    },
    plugins: [],
};
