<!-- pages/reset-password/[token].vue -->
<script setup lang="ts">
const route = useRoute();
const token = route.params.token as string;

// Set the page layout to auth layout
definePageMeta({
  layout: "auth",
});

// Define meta for the page
useHead({
  title: "Reset Password - Your App Name",
  meta: [
    { name: "description", content: "Create a new password for your account" },
  ],
});
</script>

<template>
  <div class="w-full max-w-md mx-auto">
    <AuthResetPasswordForm :token="token" />
  </div>
</template>
