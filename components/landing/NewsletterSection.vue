<script lang="ts" setup>
// Newsletter form logic
const email = ref("");
const isSubmitting = ref(false);
const isSuccess = ref(false);
const isError = ref(false);
const errorMessage = ref("");

const validateEmail = (email: string): boolean => {
  const re =
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

const submitForm = () => {
  if (email.value.trim() === "") {
    isError.value = true;
    errorMessage.value = "Please enter your email address";
    return;
  }

  if (!validateEmail(email.value)) {
    isError.value = true;
    errorMessage.value = "Please enter a valid email address";
    return;
  }

  isError.value = false;
  isSubmitting.value = true;

  // Simulate API call
  setTimeout(() => {
    isSubmitting.value = false;
    isSuccess.value = true;

    // Reset success message after a few seconds
    setTimeout(() => {
      isSuccess.value = false;
      email.value = "";
    }, 3000);
  }, 1500);
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    class="py-16 px-4 bg-gradient-to-r from-indigo-50 via-purple-50 to-indigo-50 relative overflow-hidden"
  >
    <!-- Background decoration -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Decorative circles -->
      <div
        class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-indigo-200 to-indigo-300 rounded-full opacity-10 blur-3xl -mr-48 -mt-48"
      />
      <div
        class="absolute bottom-0 left-0 w-96 h-96 bg-gradient-to-br from-purple-200 to-purple-300 rounded-full opacity-10 blur-3xl -ml-48 -mb-48"
      />

      <!-- Floating envelope icons -->
      <div
        v-for="i in 5"
        :key="i"
        class="envelope-particle absolute opacity-10"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-8 w-8 text-indigo-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
          />
        </svg>
      </div>

      <!-- Grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto max-w-4xl relative z-10">
      <div
        class="bg-white rounded-2xl shadow-xl p-8 md:p-10 border border-gray-200 animate-fade-in overflow-hidden relative"
      >
        <!-- Accent corner -->
        <div
          class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-bl-full opacity-10 -mr-10 -mt-10"
        />

        <!-- Content -->
        <div class="text-center relative">
          <!-- Section icon -->
          <div
            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white mb-6 mx-auto shadow-lg animate-float"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
          </div>

          <h3
            class="text-2xl md:text-3xl font-bold mb-3 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600"
          >
            Stay Updated
          </h3>
          <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Subscribe to our newsletter for the latest course updates, industry
            insights, and special offers.
          </p>

          <!-- Newsletter form -->
          <form class="max-w-lg mx-auto" @submit.prevent="submitForm">
            <div class="flex flex-col sm:flex-row gap-2">
              <div class="relative flex-grow">
                <!-- Email icon -->
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                    />
                  </svg>
                </div>

                <!-- Input field -->
                <input
                  v-model="email"
                  type="email"
                  placeholder="Enter your email address"
                  class="pl-10 w-full py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-gray-300 transition-all duration-300 bg-white"
                  :class="{ 'border-red-500 focus:ring-red-500': isError }"
                />

                <!-- Success indicator -->
                <div
                  v-if="isSuccess"
                  class="absolute inset-y-0 right-3 flex items-center text-green-500"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>

              <!-- Submit button -->
              <button
                type="submit"
                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-lg transition-all duration-300 font-semibold transform hover:translate-y-px hover:shadow-lg flex items-center justify-center group relative overflow-hidden"
                :disabled="isSubmitting"
              >
                <!-- Normal state -->
                <span
                  v-if="!isSubmitting && !isSuccess"
                  class="relative z-10 flex items-center"
                >
                  Subscribe
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 ml-1 transform transition-transform duration-300 group-hover:translate-x-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>

                <!-- Loading state -->
                <span v-if="isSubmitting" class="flex items-center">
                  <svg
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    />
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                  </svg>
                  Processing...
                </span>

                <!-- Success state -->
                <span v-if="isSuccess" class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Subscribed!
                </span>

                <!-- Animated shine effect -->
                <div
                  class="absolute top-0 left-0 w-full h-full bg-white opacity-0 group-hover:opacity-20 transform -translate-x-full group-hover:translate-x-full transition-all duration-700"
                />
              </button>
            </div>

            <!-- Error message -->
            <div
              v-if="isError"
              class="mt-2 text-red-500 text-sm text-left ml-2 animate-fade-in"
            >
              {{ errorMessage }}
            </div>

            <!-- Success message -->
            <div
              v-if="isSuccess"
              class="mt-2 text-green-500 text-sm animate-fade-in"
            >
              Thanks for subscribing! We'll be in touch soon.
            </div>
          </form>

          <!-- Privacy info -->
          <p class="text-gray-500 text-xs mt-6">
            We respect your privacy. Unsubscribe at any time.
            <a
              href="#privacy"
              class="text-indigo-600 hover:text-indigo-800 underline-offset-2 hover:underline"
              >Privacy Policy</a
            >
          </p>

          <!-- Newsletter benefits -->
          <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
            <div class="flex items-start">
              <div
                class="flex-shrink-0 rounded-full p-1.5 bg-indigo-100 text-indigo-600 mr-3"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-semibold text-gray-800">
                  Course Updates
                </h4>
                <p class="text-xs text-gray-500">
                  Be the first to know about new courses
                </p>
              </div>
            </div>

            <div class="flex items-start">
              <div
                class="flex-shrink-0 rounded-full p-1.5 bg-purple-100 text-purple-600 mr-3"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-semibold text-gray-800">
                  Industry Insights
                </h4>
                <p class="text-xs text-gray-500">Expert analysis and trends</p>
              </div>
            </div>

            <div class="flex items-start">
              <div
                class="flex-shrink-0 rounded-full p-1.5 bg-pink-100 text-pink-600 mr-3"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <h4 class="text-sm font-semibold text-gray-800">
                  Exclusive Offers
                </h4>
                <p class="text-xs text-gray-500">
                  Special discounts and early access
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Grid pattern background */
.bg-grid-pattern {
  background-image: linear-gradient(
      to right,
      rgba(0, 0, 0, 0.05) 1px,
      transparent 1px
    ),
    linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
  background-size: 20px 20px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes particle-float {
  0%,
  100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(10deg);
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s forwards;
}

.animate-float {
  animation: float 5s ease-in-out infinite;
}

/* Envelope particles */
.envelope-particle {
  animation: particle-float 8s infinite ease-in-out;
}

.envelope-particle:nth-child(1) {
  left: 10%;
  top: 20%;
  animation-delay: 0s;
}
.envelope-particle:nth-child(2) {
  left: 80%;
  top: 15%;
  animation-delay: 1s;
}
.envelope-particle:nth-child(3) {
  left: 20%;
  top: 70%;
  animation-delay: 2s;
}
.envelope-particle:nth-child(4) {
  left: 70%;
  top: 60%;
  animation-delay: 3s;
}
.envelope-particle:nth-child(5) {
  left: 50%;
  top: 30%;
  animation-delay: 1.5s;
}
</style>
