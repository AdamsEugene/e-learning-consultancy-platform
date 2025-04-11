<!-- pages/two-factor-auth.vue -->
<script setup lang="ts">
// Set the page layout to auth layout
definePageMeta({
  layout: "auth",
});

// Define meta for the page
useHead({
  title: "Two-Factor Authentication - Your App Name",
  meta: [
    {
      name: "description",
      content: "Verify your identity with two-factor authentication",
    },
  ],
});
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <AuthTwoFactorForm />
  </div>
</template>
