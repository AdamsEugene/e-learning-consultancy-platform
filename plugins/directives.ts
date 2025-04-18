import { defineNuxtPlugin } from "#app";
import directives from "../directives";

export default defineNuxtPlugin((nuxtApp) => {
  // Register all directives
  nuxtApp.vueApp.use(directives);
});
