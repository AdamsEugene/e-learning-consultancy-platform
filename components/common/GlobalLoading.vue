<script setup lang="ts">
import { inject } from "vue";

interface LoadingState {
  isLoading: boolean;
  loadingText: string;
  loadingOverlay: boolean;
  showLoading: (text?: string, overlay?: boolean) => void;
  hideLoading: () => void;
}

const loading = inject<LoadingState>("loading");
</script>

<template>
  <CommonLoadingScreen
    v-if="loading?.isLoading"
    :text="loading?.loadingText"
    :overlay="loading?.loadingOverlay"
    full-screen
    size="lg"
  />
</template>
