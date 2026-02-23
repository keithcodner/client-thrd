/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./{app, components}/**/*.{js,jsx,ts,tsx}",
    "./components/**/*.{js,jsx,ts,tsx}",
  ],
  presets: [require("nativewind/preset")],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: require('tailwindcss/colors').emerald[500],
          light: require('tailwindcss/colors').emerald[300],
          dark: require('tailwindcss/colors').emerald[700],
        }
      }
    },
  },
  plugins: [],
}