/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './resources/js/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0F172A',
        secondary: '#1E293B',
        accent: '#3B82F6',
      },
    },
  },
  plugins: [],
}
