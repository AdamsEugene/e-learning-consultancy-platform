<script setup lang="ts">
import { ref } from "vue";

const props = defineProps<{
  token: string;
}>();

const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const showPassword = ref(false);
const isLoading = ref(false);
const formError = ref("");
const isSuccess = ref(false);

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value;
};

const handleSubmit = async () => {
  // Reset states
  formError.value = "";

  // Validate passwords match
  if (password.value !== confirmPassword.value) {
    formError.value = "Passwords do not match";
    return;
  }

  isLoading.value = true;

  try {
    // Add your password reset logic here
    // Example: await useAuth().resetPassword({ token: props.token, email: email.value, password: password.value })
    console.log("Reset password request:", {
      token: props.token,
      email: email.value,
      password: password.value,
    });

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // For demo purposes, show error for specific token
    if (props.token === "invalid") {
      throw new Error("This reset link is invalid or has expired");
    }

    // Show success message
    isSuccess.value = true;
  } catch (error) {
    console.error("Reset password failed:", error);
    formError.value =
      error instanceof Error
        ? error.message
        : "Password reset failed. Please try again.";
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
  <div class="reset-password-form">
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold mb-2 text-gray-900">Create new password</h1>
      <p class="text-gray-600">
        Your new password must be different from previous passwords
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
            Password reset successful!
          </p>
          <p class="text-sm text-green-600 mt-1">
            Your password has been reset. You can now log in with your new
            password.
          </p>
          <div class="mt-3">
            <NuxtLink
              to="/login"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green active:bg-green-700 transition ease-in-out duration-150"
            >
              <Icon name="ic:baseline-login" class="mr-2" size="16" />
              Go to login
            </NuxtLink>
          </div>
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

    <form v-if="!isSuccess" class="space-y-6" @submit.prevent="handleSubmit">
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

      <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-gray-700"
          >New password</label
        >
        <div class="relative">
          <span class="absolute left-4 top-3.5 text-gray-400">
            <Icon name="ic:baseline-lock" size="20" />
          </span>
          <input
            id="password"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="new-password"
            :class="inputClasses"
            required
          />
          <button
            type="button"
            class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 transition-colors"
            @click="toggleShowPassword"
          >
            <Icon
              :name="
                showPassword
                  ? 'ic:baseline-visibility-off'
                  : 'ic:baseline-visibility'
              "
              size="20"
            />
          </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">
          Min. 8 characters with at least one number and one special character
        </p>
      </div>

      <div class="space-y-2">
        <label
          for="confirmPassword"
          class="block text-sm font-medium text-gray-700"
          >Confirm new password</label
        >
        <div class="relative">
          <span class="absolute left-4 top-3.5 text-gray-400">
            <Icon name="ic:baseline-lock" size="20" />
          </span>
          <input
            id="confirmPassword"
            v-model="confirmPassword"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="new-password"
            :class="inputClasses"
            required
          />
        </div>
      </div>

      <button type="submit" :class="btnClasses" :disabled="isLoading">
        <span class="btn-shine"></span>
        <span class="flex items-center">
          <Icon
            v-if="isLoading"
            name="ic:baseline-refresh"
            class="animate-spin mr-2"
            size="20"
          />
          <Icon v-else name="ic:baseline-lock-reset" class="mr-2" size="20" />
          Reset Password
        </span>
      </button>

      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          <NuxtLink
            to="login"
            class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
          >
            Back to login
          </NuxtLink>
        </p>
      </div>
    </form>
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
