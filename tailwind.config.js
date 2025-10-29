/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./vendor/symfony/twig-bridge/Resources/views/Form/*.html.twig",
  ],
  darkMode: false,
  theme: {
      extend: {
          colors: {
              'leadforge': '#0B261C',
          }
      },
  },
  plugins: [
    require("@tailwindcss/forms")({
      strategy: 'base', // only generate global styles
    }),
    require('daisyui'),
  ],
}
