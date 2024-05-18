/** @type {import('tailwindcss').Config} */
export default {
content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      screens: {
        'xs': '480px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

