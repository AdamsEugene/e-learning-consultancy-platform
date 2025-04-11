<!-- pages/verify-email/[token].vue -->
<script setup lang="ts">
const route = useRoute();
const token = route.params.token as string;
const isLoading = ref(true);
const isVerified = ref(false);
const verificationError = ref("");

// Set the page layout to auth layout
definePageMeta({
  layout: "auth",
});

// Define meta for the page
useHead({
  title: "Verify Email - Your App Name",
  meta: [{ name: "description", content: "Verify your email address" }],
});

// Verify email on page load
onMounted(async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // For demo purposes
    if (token === "invalid") {
      throw new Error("Invalid or expired verification link");
    }

    isVerified.value = true;
  } catch (error) {
    console.error("Email verification failed:", error);
    if (error instanceof Error) {
      verificationError.value = error.message;
    } else {
      verificationError.value = "Verification failed. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <div class="w-full max-w-md mx-auto text-center">
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-2 text-gray-900">Email Verification</h1>
    </div>

    <!-- Loading state -->
    <div
      v-if="isLoading"
      class="flex flex-col items-center justify-center py-8"
    >
      <div
        class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mb-4"
      />
      <p class="text-gray-700">Verifying your email address...</p>
    </div>

    <!-- Success state -->
    <div
      v-else-if="isVerified"
      class="bg-green-50 border border-green-200 rounded-xl p-8 text-center"
    >
      <div
        class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4"
      >
        <Icon name="ic:baseline-check" class="h-10 w-10 text-green-600" />
      </div>
      <h2 class="text-2xl font-medium text-green-800 mb-2">Email Verified!</h2>
      <p class="text-green-700 mb-6">
        Your email has been successfully verified. You can now access all
        features of your account.
      </p>
      <NuxtLink
        to="/login"
        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <Icon name="ic:baseline-login" class="mr-2" size="20" />
        Proceed to Login
      </NuxtLink>
    </div>

    <!-- Error state -->
    <div
      v-else
      class="bg-red-50 border border-red-200 rounded-xl p-8 text-center"
    >
      <div
        class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4"
      >
        <Icon name="ic:baseline-error" class="h-10 w-10 text-red-600" />
      </div>
      <h2 class="text-2xl font-medium text-red-800 mb-2">
        Verification Failed
      </h2>
      <p class="text-red-700 mb-6">
        {{
          verificationError ||
          "We could not verify your email. The link may be invalid or expired."
        }}
      </p>
      <div class="space-y-3">
        <NuxtLink
          to="/login"
          class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <Icon name="ic:baseline-login" class="mr-2" size="20" />
          Back to Login
        </NuxtLink>
        <p class="text-sm text-gray-600 mt-4">
          Need help?
          <NuxtLink
            to="/contact"
            class="font-medium text-indigo-600 hover:text-indigo-800"
            >Contact Support</NuxtLink
          >
        </p>
      </div>
    </div>
  </div>
</template>
