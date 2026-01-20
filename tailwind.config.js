/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        colors: {
            'dark-primary': '#2d2850',
            'dark-secondary': '#413a73',
            'accent-green': '#53ff86',
        }
    },
  },
  plugins: [],
}