<script setup lang="ts">
interface Props {
  size?: "sm" | "md" | "lg";
  color?: "primary" | "secondary" | "white";
  text?: string;
  fullScreen?: boolean;
  overlay?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  size: "md",
  color: "primary",
  text: "Loading...",
  fullScreen: false,
  overlay: false,
});

const sizeClasses = {
  sm: "h-8 w-8",
  md: "h-12 w-12",
  lg: "h-16 w-16",
};

const colorClasses = {
  primary: "border-indigo-600",
  secondary: "border-gray-600",
  white: "border-white",
};

const containerClasses = computed(() => {
  const baseClasses = "flex flex-col items-center justify-center";
  const spacingClasses = props.fullScreen ? "min-h-screen" : "py-20";
  const overlayClasses = props.overlay
    ? "fixed inset-0 bg-black bg-opacity-50 z-50"
    : "";
  return `${baseClasses} ${spacingClasses} ${overlayClasses}`;
});
</script>

<template>
  <div :class="containerClasses">
    <div
      class="animate-spin rounded-full border-t-2 border-b-2"
      :class="[sizeClasses[size], colorClasses[color]]"
    />
    <p
      v-if="text"
      class="mt-4 text-gray-600"
      :class="{ 'text-white': color === 'white' }"
    >
      {{ text }}
    </p>
  </div>
</template>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
