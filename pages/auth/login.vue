<script setup lang="ts">
// Set the page layout to auth layout
definePageMeta({
  layout: "auth",
});

// Define meta for the page
useHead({
  title: "Login - Your App Name",
  meta: [{ name: "description", content: "Login to access your account" }],
});
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <AuthLoginForm />
  </div>
</template>
