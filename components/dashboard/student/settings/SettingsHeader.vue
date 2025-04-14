<!-- components/dashboard/settings/SettingsHeader.vue -->
<script setup lang="ts">
defineProps<{
  title: string;
  description?: string;
}>();
</script>

<template>
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ title }}</h1>
    <p v-if="description" class="text-gray-600">
      {{ description }}
    </p>
  </div>
</template>
