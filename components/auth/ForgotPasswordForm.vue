<script setup lang="ts">
import { ref } from "vue";

const email = ref("");
const isLoading = ref(false);
const formError = ref("");
const isSuccess = ref(false);
const formSubmitted = ref(false);

const handleSubmit = async () => {
  // Reset states
  formError.value = "";
  formSubmitted.value = false;
  isSuccess.value = false;
  isLoading.value = true;

  try {
    // Add your password reset logic here
    // Example: await useAuth().forgotPassword({ email: email.value })
    console.log("Forgot password request for:", email.value);

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // For demo purposes, show error for specific email
    if (email.value === "notfound@example.com") {
      throw new Error("No account found with this email address");
    }

    // Show success message
    isSuccess.value = true;
    formSubmitted.value = true;
  } catch (error) {
    console.error("Forgot password request failed:", error);
    formError.value =
      error instanceof Error
        ? error.message
        : "Request failed. Please try again.";
  } finally {
    isLoading.value = false;
  }
};

const inputClasses =
  "w-full pl-12 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-700";
const btnClasses =
  "relative overflow-hidden w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200";
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="forgot-password-form">
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold mb-2 text-gray-900">Reset your password</h1>
      <p class="text-gray-600">
        Enter your email to receive a password reset link
      </p>
    </div>

    <!-- Success message -->
    <div
      v-if="isSuccess"
      class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-6"
    >
      <div class="flex">
        <Icon
          name="ic:baseline-check-circle"
          class="text-green-500 mr-2"
          size="20"
        />
        <div>
          <p class="text-sm text-green-700 font-medium">
            Password reset email sent!
          </p>
          <p class="text-sm text-green-600 mt-1">
            Please check your email inbox for instructions to reset your
            password.
          </p>
        </div>
      </div>
    </div>

    <!-- Error message -->
    <div
      v-if="formError"
      class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6"
    >
      <div class="flex items-center">
        <Icon
          name="ic:baseline-error-outline"
          class="text-red-500 mr-2"
          size="20"
        />
        <p class="text-sm text-red-700">{{ formError }}</p>
      </div>
    </div>

    <form
      v-if="!formSubmitted"
      class="space-y-6"
      @submit.prevent="handleSubmit"
    >
      <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-gray-700"
          >Email address</label
        >
        <div class="relative">
          <span class="absolute left-4 top-3.5 text-gray-400">
            <Icon name="ic:baseline-email" size="20" />
          </span>
          <input
            id="email"
            v-model="email"
            type="email"
            autocomplete="email"
            :class="inputClasses"
            required
          />
        </div>
      </div>

      <button type="submit" :class="btnClasses" :disabled="isLoading">
        <span class="btn-shine" />
        <span class="flex items-center">
          <Icon
            v-if="isLoading"
            name="ic:baseline-refresh"
            class="animate-spin mr-2"
            size="20"
          />
          <Icon v-else name="ic:baseline-send" class="mr-2" size="20" />
          Send Reset Link
        </span>
      </button>

      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          Remember your password?
          <NuxtLink
            to="login"
            class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
          >
            Back to login
          </NuxtLink>
        </p>
      </div>
    </form>

    <div v-if="formSubmitted" class="mt-6 flex justify-center">
      <NuxtLink
        to="/login"
        class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors flex items-center"
      >
        <Icon name="ic:baseline-arrow-back" class="mr-2" size="20" />
        Back to login
      </NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.btn-shine {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.3) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: rotate(30deg);
  opacity: 0;
  transition: opacity 0.3s;
}

button:not(:disabled):hover .btn-shine {
  opacity: 1;
  animation: btnShine 1.5s infinite;
}

@keyframes btnShine {
  0% {
    transform: translateX(-100%) rotate(30deg);
  }
  100% {
    transform: translateX(100%) rotate(30deg);
  }
}
</style>
