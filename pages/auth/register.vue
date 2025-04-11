<!-- pages/register.vue -->
<script setup lang="ts">
// Set the page layout to auth layout
definePageMeta({
  layout: "auth",
});

// Define meta for the page
useHead({
  title: "Create an Account - Your App Name",
  meta: [
    { name: "description", content: "Create a new account to get started" },
  ],
});
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <AuthRegisterForm />
  </div>
</template>
