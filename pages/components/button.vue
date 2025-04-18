<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Button Component</h1>
    <p class="text-gray-600 mb-8">
      A versatile button component with multiple variants, sizes, and states.
    </p>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Button Variants</h2>
      <p class="text-gray-600 mb-6">
        Different button styles for various use cases.
      </p>
      <div class="flex flex-wrap gap-4">
        <UiButton variant="solid" color="primary">Primary</UiButton>
        <UiButton variant="solid" color="secondary">Secondary</UiButton>
        <UiButton variant="solid" color="success">Success</UiButton>
        <UiButton variant="solid" color="danger">Danger</UiButton>
        <UiButton variant="solid" color="warning">Warning</UiButton>
        <UiButton variant="solid" color="info">Info</UiButton>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Button Sizes</h2>
      <p class="text-gray-600 mb-6">
        Different button sizes for various contexts.
      </p>
      <div class="flex flex-wrap items-center gap-4">
        <UiButton size="xs">Extra Small</UiButton>
        <UiButton size="sm">Small</UiButton>
        <UiButton size="md">Medium</UiButton>
        <UiButton size="lg">Large</UiButton>
        <UiButton size="xl">Extra Large</UiButton>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Button States</h2>
      <p class="text-gray-600 mb-6">
        Different button states including loading and disabled.
      </p>
      <div class="flex flex-wrap gap-4">
        <UiButton>Normal</UiButton>
        <UiButton :loading="true">Loading</UiButton>
        <UiButton :disabled="true">Disabled</UiButton>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Button with Icons
      </h2>
      <p class="text-gray-600 mb-6">
        Buttons with icons for enhanced visual communication.
      </p>
      <div class="flex flex-wrap gap-4">
        <UiButton>
          <template #prefix>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
          </template>
          With Prefix Icon
        </UiButton>
        <UiButton>
          With Suffix Icon
          <template #suffix>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </template>
        </UiButton>
        <UiButton variant="icon" color="secondary">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
            <path
              fill-rule="evenodd"
              d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
              clip-rule="evenodd"
            />
          </svg>
        </UiButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "components",
});
</script>
