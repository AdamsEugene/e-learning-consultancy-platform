<!-- pages/forgot-password.vue -->
<script setup lang="ts">
// Set the page layout to auth layout
definePageMeta({
  layout: "auth",
});

// Define meta for the page
useHead({
  title: "Forgot Password - Your App Name",
  meta: [{ name: "description", content: "Reset your password" }],
});
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <AuthForgotPasswordForm />
  </div>
</template>
