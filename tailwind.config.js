module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.php',
        './resources/**/*.{vue,js,ts,jsx,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                baloo: ['"Baloo 2"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                gold: "#FFD700",
                purple: "#8a2be2",
            },
            boxShadow: {
                glossy: "0 10px 25px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.08)",
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
