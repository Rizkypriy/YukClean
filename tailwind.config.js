/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
    theme: {
        extend: {
            colors: {
                'yuk-teal': '#00bda2',
            },
        },
    },
    plugins: [],
};
