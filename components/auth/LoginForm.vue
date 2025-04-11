<!-- components/auth/LoginForm.vue -->
<script setup lang="ts">
import { ref } from "vue";

const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const showPassword = ref(false);
const isLoading = ref(false);
const formError = ref("");

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value;
};

const handleLogin = async () => {
  // Reset error state
  formError.value = "";
  isLoading.value = true;

  try {
    // Add your login logic here
    // Example: await useAuth().login({ email: email.value, password: password.value })
    console.log("Login attempt with:", {
      email: email.value,
      password: password.value,
      rememberMe: rememberMe.value,
    });

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // For demo purposes, show error for specific email
    if (email.value === "error@example.com") {
      throw new Error("Invalid email or password");
    }

    // Navigate to dashboard after successful login
    // await navigateTo('/dashboard');
  } catch (error: unknown) {
    console.error("Login failed:", error);
    formError.value = (error instanceof Error ? error.message : "Login failed. Please try again.");
  } finally {
    isLoading.value = false;
  }
};

const inputClasses =
  "w-full pl-12 pr-10 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-700";
const btnClasses =
  "relative overflow-hidden w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200";
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="login-form">
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold mb-2 text-gray-900">Welcome back</h1>
      <p class="text-gray-600">Please sign in to continue to your account</p>
    </div>

    <form class="space-y-6" @submit.prevent="handleLogin">
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
        <div class="flex justify-between">
          <label for="password" class="block text-sm font-medium text-gray-700"
            >Password</label
          >
          <NuxtLink
            to="forgot-password"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
          >
            Forgot password?
          </NuxtLink>
        </div>
        <div class="relative">
          <span class="absolute left-4 top-3.5 text-gray-400">
            <Icon name="ic:baseline-lock" size="20" />
          </span>
          <input
            id="password"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="current-password"
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
      </div>

      <div class="flex items-center">
        <input
          id="remember"
          v-model="rememberMe"
          type="checkbox"
          class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
        />
        <label for="remember" class="ml-2 block text-sm text-gray-700">
          Keep me signed in
        </label>
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
          <Icon v-else name="ic:baseline-login" class="mr-2" size="20" />
          Sign In
        </span>
      </button>

      <div class="mt-6 text-center pt-4">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <NuxtLink
            to="register"
            class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
          >
            Create an account
          </NuxtLink>
        </p>
      </div>

      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300" />
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-gray-500">Or continue with</span>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <button
          type="button"
          class="flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
        >
          <Icon name="ic:baseline-facebook" size="20" class="text-blue-600" />
        </button>
        <button
          type="button"
          class="flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
        >
          <Icon name="ic:baseline-g-translate" size="20" class="text-red-500" />
        </button>
        <button
          type="button"
          class="flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
        >
          <Icon name="ic:baseline-apple" size="20" class="text-gray-900" />
        </button>
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
