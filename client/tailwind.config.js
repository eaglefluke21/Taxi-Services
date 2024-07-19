/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        anta: ['Anta', 'sans-serif'],
        quick: ['Quicksand', 'sans-serif'],
        comic: ['Comic Neue', 'sans-serif'],
      },
    },
  },
  plugins: [],
}