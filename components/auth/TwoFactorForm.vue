<script setup lang="ts">
const code = ref(["", "", "", "", "", ""]);
const isLoading = ref(false);
const formError = ref("");
const countdown = ref(60);
const countdownActive = ref(false);
const focusIndex = ref(0);

// Combine code digits
const fullCode = computed(() => code.value.join(""));

const inputRefs = ref<HTMLInputElement[]>([]);

// Focus on next input after entering a digit
const handleInput = (index: number, event: Event) => {
  const target = event.target as HTMLInputElement;
  const value = target.value;

  // Keep only the last character if the user pastes multiple digits
  code.value[index] = value.slice(-1);

  // Move to next input if a digit was entered
  if (value && index < 5) {
    focusIndex.value = index + 1;
    inputRefs.value[index + 1].focus();
  }

  // If all codes are filled, submit automatically
  if (
    code.value.every((digit) => digit !== "") &&
    fullCode.value.length === 6
  ) {
    handleVerify();
  }
};

// Handle backspace to move to previous input
const handleKeyDown = (index: number, event: KeyboardEvent) => {
  if (event.key === "Backspace" && !code.value[index] && index > 0) {
    focusIndex.value = index - 1;
    inputRefs.value[index - 1].focus();
  }
};

// Handle paste for all inputs
const handlePaste = (event: ClipboardEvent) => {
  event.preventDefault();
  const pastedData = event.clipboardData?.getData("text");

  if (pastedData && /^\d+$/.test(pastedData)) {
    const digits = pastedData.slice(0, 6).split("");

    digits.forEach((digit, index) => {
      if (index < 6) {
        code.value[index] = digit;
      }
    });

    // Focus on the next empty input or the last one
    const nextEmptyIndex = code.value.findIndex((digit) => digit === "");
    const focusOn = nextEmptyIndex === -1 ? 5 : nextEmptyIndex;
    focusIndex.value = focusOn;
    inputRefs.value[focusOn].focus();

    // Auto-submit if all fields are filled
    if (
      code.value.every((digit) => digit !== "") &&
      fullCode.value.length === 6
    ) {
      handleVerify();
    }
  }
};

const startCountdown = () => {
  countdown.value = 60;
  countdownActive.value = true;

  const timer = setInterval(() => {
    countdown.value--;

    if (countdown.value <= 0) {
      clearInterval(timer);
      countdownActive.value = false;
    }
  }, 1000);
};

const resendCode = async () => {
  try {
    // Your logic to resend code
    console.log("Resending authentication code");

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Start countdown
    startCountdown();
  } catch (error) {
    console.error("Failed to resend code:", error);
  }
};

const handleVerify = async () => {
  // Reset error
  formError.value = "";

  // Validate code
  if (fullCode.value.length !== 6) {
    formError.value = "Please enter all 6 digits of your authentication code";
    return;
  }

  isLoading.value = true;

  try {
    // Add your 2FA verification logic here
    console.log("Verifying 2FA code:", fullCode.value);

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // For demo purposes, show error for specific code
    if (fullCode.value === "123456") {
      throw new Error("Invalid verification code");
    }

    // Navigate to dashboard after successful verification
    // await navigateTo('/dashboard');
  } catch (error) {
    console.error("2FA verification failed:", error);
    formError.value =
      error instanceof Error
        ? error.message
        : "Verification failed. Please try again.";

    // Reset code fields on error
    code.value = ["", "", "", "", "", ""];
    focusIndex.value = 0;
    inputRefs.value[0].focus();
  } finally {
    isLoading.value = false;
  }
};

// Start countdown on mount
onMounted(() => {
  startCountdown();
  // Focus first input
  nextTick(() => {
    inputRefs.value[0].focus();
  });
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="two-factor-form">
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold mb-2 text-gray-900">
        Two-Factor Authentication
      </h1>
      <p class="text-gray-600">Enter the 6-digit code sent to your device</p>
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

    <form class="space-y-6" @submit.prevent="handleVerify">
      <div class="flex justify-center space-x-3">
        <template v-for="(digit, index) in code" :key="index">
          <input
            :ref="
              (el) => {
                if (el) inputRefs[index] = el as HTMLInputElement;
              }
            "
            v-model="code[index]"
            type="text"
            maxlength="1"
            class="w-12 h-14 text-center text-xl font-semibold border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-700"
            inputmode="numeric"
            pattern="[0-9]*"
            autocomplete="one-time-code"
            :class="{ 'bg-gray-100': isLoading }"
            :disabled="isLoading"
            :autofocus="index === focusIndex"
            @input="handleInput(index, $event)"
            @keydown="handleKeyDown(index, $event)"
            @paste="handlePaste"
          />
        </template>
      </div>

      <button
        type="submit"
        class="relative overflow-hidden w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
        :disabled="isLoading || fullCode.length !== 6"
      >
        <span class="btn-shine" />
        <span class="flex items-center">
          <Icon
            v-if="isLoading"
            name="ic:baseline-refresh"
            class="animate-spin mr-2"
            size="20"
          />
          <Icon v-else name="ic:baseline-security" class="mr-2" size="20" />
          Verify
        </span>
      </button>

      <div class="text-center">
        <p class="text-sm text-gray-600 mb-2">Didn't receive a code?</p>
        <button
          type="button"
          class="text-indigo-600 hover:text-indigo-800 font-medium text-sm focus:outline-none disabled:opacity-50 disabled:hover:text-indigo-600 transition-colors"
          :disabled="countdownActive"
          @click="resendCode"
        >
          {{ countdownActive ? `Resend code in ${countdown}s` : "Resend code" }}
        </button>
      </div>

      <div class="mt-6 text-center pt-4 border-t border-gray-200">
        <p class="flex items-center justify-center text-sm text-gray-600">
          <Icon name="ic:baseline-info" class="mr-1 text-gray-400" size="16" />
          Having trouble?
          <NuxtLink
            to="/contact"
            class="ml-1 font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
            >Contact Support</NuxtLink
          >
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

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  appearance: textfield;
  -moz-appearance: textfield;
}
</style>
