/** @type {import('tailwindcss').Config} */
const colors = require("tailwindcss/colors");
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    container: {
      center: true,
      padding:'2rem'
    },
    extend: {
      colors: {
        darkblue:'#29388a',
        lightblue:'#3e4c96',
        skyblue:'#1787FC',
        blue: {
          50: "#E7EAF8",
          100: "#CCD1F0",
          200: "#99A4E1",
          300: "#6676D1",
          400: "#384CBD",
          500: "#29388A",
          600: "#212C6E",
          700: "#192153",
          800: "#101637",
          900: "#080B1C"
        },
        primary: {
          50: "#E7EAF8",
          100: "#CCD1F0",
          200: "#99A4E1",
          300: "#6676D1",
          400: "#384CBD",
          500: "#29388A",
          600: "#212C6E",
          700: "#192153",
          800: "#101637",
          900: "#080B1C"
        },
        // blue: colors.blue,
        neutral: colors.neutral,
        red: colors.red,
        gray: colors.slate,
        slate: colors.slate,
        white: colors.white,
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ],
}