<script setup>
definePageMeta({
  layout: "components",
});
</script>

<template>
  <div class="space-y-8 p-8 bg-gray-50">
    <h2 class="text-2xl font-bold mb-4">Button Variants</h2>
    <div class="flex flex-wrap gap-4">
      <UiButton variant="primary">Primary</UiButton>
      <UiButton variant="secondary">Secondary</UiButton>
      <UiButton variant="tertiary">Tertiary</UiButton>
      <UiButton variant="success">Success</UiButton>
      <UiButton variant="danger">Danger</UiButton>
      <UiButton variant="warning">Warning</UiButton>
      <UiButton variant="info">Info</UiButton>
      <UiButton variant="ghost">Ghost</UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Button Sizes</h2>
    <div class="flex flex-wrap items-center gap-4">
      <UiButton size="sm">Small</UiButton>
      <UiButton size="md">Medium</UiButton>
      <UiButton size="lg">Large</UiButton>
      <UiButton size="xl">Extra Large</UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Icon Buttons</h2>
    <div class="flex flex-wrap items-center gap-4">
      <UiButton iconPosition="left">
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
        Left Icon
      </UiButton>

      <UiButton iconPosition="right">
        Right Icon
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </UiButton>

      <UiButton iconPosition="only" aria-label="Add item">
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Button States</h2>
    <div class="flex flex-wrap items-center gap-4">
      <UiButton>Default</UiButton>
      <UiButton disabled>Disabled</UiButton>
      <UiButton loading>Loading</UiButton>
      <UiButton active>Active</UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Styled Buttons</h2>
    <div class="flex flex-wrap items-center gap-4">
      <UiButton elevated>Elevated</UiButton>
      <UiButton outlined>Outlined</UiButton>
      <UiButton rounded>Rounded</UiButton>
      <UiButton outlined rounded>Outlined & Rounded</UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Buttons with Badges</h2>
    <div class="flex flex-wrap items-center gap-4">
      <UiButton badge="3">Notifications</UiButton>
      <UiButton badge="New" badgeVariant="success">Updates</UiButton>
      <UiButton badge="!" badgeVariant="danger" iconPosition="only">
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h2a1 1 0 100-2H9z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Full Width Button</h2>
    <div class="max-w-md">
      <UiButton fullWidth>Submit Form</UiButton>
    </div>

    <h2 class="text-2xl font-bold mb-4">Common UI Patterns</h2>
    <div class="flex flex-wrap items-center gap-4">
      <!-- Form submit button -->
      <UiButton variant="primary" type="submit">
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
        Save Changes
      </UiButton>

      <!-- Delete button -->
      <UiButton variant="danger" outlined>
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
        Delete
      </UiButton>

      <!-- Filters button with badge -->
      <UiButton variant="ghost" badge="3" badgeVariant="info">
        <template #icon>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
        Filters
      </UiButton>
    </div>
  </div>
</template>
