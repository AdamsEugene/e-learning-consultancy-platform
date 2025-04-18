/* eslint-disable @typescript-eslint/no-explicit-any */
import { ref } from "vue";

export const useLoading = () => {
  const isLoading = ref(false);
  const loadingText = ref("");
  const loadingOverlay = ref(false);

  const showLoading = (text = "Loading...", overlay = false) => {
    loadingText.value = text;
    loadingOverlay.value = overlay;
    isLoading.value = true;
  };

  const hideLoading = () => {
    isLoading.value = false;
    loadingText.value = "";
    loadingOverlay.value = false;
  };

  return {
    isLoading,
    loadingText,
    loadingOverlay,
    showLoading,
    hideLoading,
  };
};

export default {
  install: (app: any) => {
    const loading = useLoading();
    app.provide("loading", loading);
  },
};
