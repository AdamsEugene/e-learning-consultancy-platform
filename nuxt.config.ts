// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  css: ["~/assets/css/main.css"],

  vite: {
    plugins: [tailwindcss()],
  },

  modules: [
    "@nuxt/content",
    "@nuxt/eslint",
    "@nuxt/fonts",
    "@nuxt/icon",
    "@nuxt/image",
    "@nuxt/scripts",
    "@nuxt/test-utils",
    "@nuxt/ui",
  ],

  // Make sure layouts are properly configured
  app: {
    layoutTransition: {
      name: "layout",
      mode: "out-in",
    },
    pageTransition: {
      name: "page",
      mode: "out-in",
    },
  },

  // Explicitly enable layouts
  features: {
    // Removed invalid property 'layoutAsCustomElement'
  },

  // If you're using TypeScript, ensure your tsconfig.json includes "vue" and "vue-router"
  typescript: {
    strict: true,
    shim: false,
  },
});
