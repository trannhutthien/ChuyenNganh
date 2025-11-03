/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#79E3D6',
          50: '#F0FCFA',
          100: '#D9F7F2',
          200: '#B3EFE5',
          300: '#8DE7D8',
          400: '#79E3D6',
          500: '#53D9C8',
          600: '#3CC0B0',
          700: '#2E9589',
          800: '#206A62',
          900: '#12403B',
        },
      },
    },
  },
  plugins: [],
}
