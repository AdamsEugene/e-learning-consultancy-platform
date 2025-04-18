<script lang="ts" setup>
interface FAQ {
  question: string;
  answer: string;
  icon?: string;
  delay?: string;
  color?: string;
}

interface Props {
  faqs: FAQ[];
  title?: string;
  subtitle?: string;
  showSearch?: boolean;
}

withDefaults(defineProps<Props>(), {
  title: "Frequently Asked Questions",
  subtitle: "Find answers to common questions about our platform and services.",
  showSearch: true,
});

// Track which FAQ is open
const openFaqIndex = ref<number | null>(null);

const toggleFaq = (index: number) => {
  openFaqIndex.value = openFaqIndex.value === index ? null : index;
};

// Get default values for missing properties
const getFaqWithDefaults = (faq: FAQ, index: number): FAQ => {
  return {
    icon:
      faq.icon ||
      ["shopping-cart", "lightbulb", "badge-check", "users"][index % 4],
    delay: faq.delay || `delay-${(index % 5) * 100}`,
    color: faq.color || ["indigo", "purple", "blue", "pink"][index % 4],
    ...faq,
  };
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    id="faq"
    class="py-20 px-4 bg-gradient-to-b from-white to-indigo-50 relative overflow-hidden"
  >
    <!-- Background decoration -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Decorative blurred shapes -->
      <div
        class="absolute top-20 right-20 w-72 h-72 bg-indigo-100 rounded-full opacity-30 blur-3xl"
      />
      <div
        class="absolute -left-20 bottom-40 w-64 h-64 bg-purple-100 rounded-full opacity-30 blur-3xl"
      />

      <!-- Decorative grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />

      <!-- Floating question marks -->
      <div
        v-for="i in 5"
        :key="i"
        class="question-mark absolute text-4xl font-serif opacity-10 animate-float"
      >
        ?
      </div>
    </div>

    <div class="container mx-auto max-w-4xl relative z-10">
      <!-- Section header with animated underline -->
      <div class="text-center mb-16">
        <div class="mb-3 animate-fade-in">
          <span
            class="bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 text-sm font-bold px-4 py-1.5 rounded-full shadow-sm"
          >
            Common Questions
          </span>
        </div>
        <h2
          class="text-3xl md:text-5xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 animate-fade-in"
          style="animation-delay: 100ms"
        >
          {{ title }}
        </h2>
        <p
          class="text-xl text-gray-600 max-w-3xl mx-auto mb-8 animate-fade-in"
          style="animation-delay: 200ms"
        >
          {{ subtitle }}
        </p>

        <!-- Animated underline -->
        <div
          class="h-1 w-24 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full relative overflow-hidden animate-fade-in"
          style="animation-delay: 300ms"
        >
          <div class="absolute inset-0 bg-white animate-shimmer" />
        </div>
      </div>

      <!-- FAQ accordion with animations -->
      <div class="space-y-6">
        <div
          v-for="(faq, index) in faqs"
          :key="index"
          class="animate-fade-in rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-500"
          :class="getFaqWithDefaults(faq, index).delay"
        >
          <div
            class="border border-gray-200 rounded-xl overflow-hidden bg-white group transform transition-all duration-500"
            :class="{ 'shadow-lg ring-2': openFaqIndex === index }"
            :style="{
              borderColor:
                openFaqIndex === index
                  ? `rgb(var(--color-${
                      getFaqWithDefaults(faq, index).color
                    }-200))`
                  : 'transparent',
            }"
          >
            <!-- Question header with icon -->
            <button
              class="w-full flex justify-between items-center p-6 text-left font-semibold hover:bg-gray-50/80 focus:outline-none focus:bg-gray-50/80 transition-all duration-300"
              :class="{ 'bg-gray-50/80': openFaqIndex === index }"
              @click="toggleFaq(index)"
            >
              <div class="flex items-center">
                <!-- Icon based on question type -->
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center mr-4 transition-all duration-500"
                  :class="[
                    openFaqIndex === index
                      ? `bg-${getFaqWithDefaults(faq, index).color}-100 text-${
                          getFaqWithDefaults(faq, index).color
                        }-600`
                      : 'bg-gray-100 text-gray-500',
                  ]"
                >
                  <!-- Shopping cart icon -->
                  <svg
                    v-if="
                      getFaqWithDefaults(faq, index).icon === 'shopping-cart'
                    "
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                    />
                  </svg>

                  <!-- Lightbulb icon -->
                  <svg
                    v-if="getFaqWithDefaults(faq, index).icon === 'lightbulb'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"
                    />
                  </svg>

                  <!-- Badge icon -->
                  <svg
                    v-if="getFaqWithDefaults(faq, index).icon === 'badge-check'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>

                  <!-- Users icon -->
                  <svg
                    v-if="getFaqWithDefaults(faq, index).icon === 'users'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                    />
                  </svg>
                </div>

                <!-- Question text -->
                <span
                  class="text-lg transition-colors duration-300"
                  :class="{
                    [`text-${getFaqWithDefaults(faq, index).color}-700`]:
                      openFaqIndex === index,
                    'text-gray-800': openFaqIndex !== index,
                  }"
                >
                  {{ faq.question }}
                </span>
              </div>

              <!-- Animated chevron icon -->
              <div
                class="ml-4 flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300"
                :class="{
                  [`bg-${getFaqWithDefaults(faq, index).color}-100`]:
                    openFaqIndex === index,
                  'bg-gray-100': openFaqIndex !== index,
                }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transition-transform duration-500"
                  :class="{
                    'transform rotate-180': openFaqIndex === index,
                    [`text-${getFaqWithDefaults(faq, index).color}-600`]:
                      openFaqIndex === index,
                    'text-gray-400': openFaqIndex !== index,
                  }"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </button>

            <!-- Answer with smooth animation -->
            <div
              class="transition-all duration-500 ease-in-out overflow-hidden bg-gradient-to-br from-white to-gray-50"
              :style="{
                maxHeight: openFaqIndex === index ? '500px' : '0px',
                opacity: openFaqIndex === index ? 1 : 0,
              }"
            >
              <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                <div
                  class="border-l-2 pl-5"
                  :class="`border-${getFaqWithDefaults(faq, index).color}-300`"
                >
                  {{ faq.answer }}
                </div>

                <!-- Action button based on question type -->
                <div class="mt-4 flex justify-end">
                  <button
                    class="px-4 py-2 rounded-lg text-white text-sm font-medium transition-all duration-300 transform hover:scale-105 flex items-center space-x-1"
                    :class="`bg-gradient-to-r from-${
                      getFaqWithDefaults(faq, index).color
                    }-500 to-${
                      getFaqWithDefaults(faq, index).color
                    }-600 shadow-sm hover:shadow`"
                  >
                    <span
                      v-if="
                        getFaqWithDefaults(faq, index).icon === 'shopping-cart'
                      "
                      >Enroll Now</span
                    >
                    <span
                      v-if="getFaqWithDefaults(faq, index).icon === 'lightbulb'"
                      >Learn More</span
                    >
                    <span
                      v-if="
                        getFaqWithDefaults(faq, index).icon === 'badge-check'
                      "
                      >View Certifications</span
                    >
                    <span v-if="getFaqWithDefaults(faq, index).icon === 'users'"
                      >Request Training</span
                    >

                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search bar and support info -->
      <div
        v-if="showSearch"
        class="mt-12 bg-white p-8 rounded-2xl shadow-lg border border-gray-200 animate-fade-in"
        style="animation-delay: 500ms"
      >
        <div class="text-center mb-6">
          <h3 class="text-xl font-bold text-gray-800 mb-2">
            Still have questions?
          </h3>
          <p class="text-gray-600">
            Search our knowledge base or contact our support team
          </p>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
          <!-- Search form -->
          <div class="flex-grow relative">
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
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </div>
            <input
              type="text"
              placeholder="Search our knowledge base..."
              class="pl-10 pr-4 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 outline-none"
            />
          </div>

          <!-- Contact support button -->
          <button
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-300 flex items-center justify-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
              />
            </svg>
            Contact Support
          </button>
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
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  20%,
  80% {
    opacity: 0.5;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-15px) rotate(5deg);
  }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.8s forwards;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Question marks positioning and animation delays */
.question-mark {
  font-size: 80px;
  color: rgba(79, 70, 229, 0.1);
}

.question-mark:nth-child(1) {
  top: 10%;
  left: 5%;
  animation-delay: 0s;
}
.question-mark:nth-child(2) {
  top: 30%;
  right: 5%;
  animation-delay: 1.5s;
}
.question-mark:nth-child(3) {
  top: 60%;
  left: 10%;
  animation-delay: 1s;
}
.question-mark:nth-child(4) {
  top: 80%;
  right: 10%;
  animation-delay: 2s;
}
.question-mark:nth-child(5) {
  top: 40%;
  left: 50%;
  animation-delay: 0.5s;
}

/* Animation delay classes */
.delay-0 {
  animation-delay: 0ms;
}

.delay-100 {
  animation-delay: 100ms;
}

.delay-200 {
  animation-delay: 200ms;
}

.delay-300 {
  animation-delay: 300ms;
}

.delay-400 {
  animation-delay: 400ms;
}

.delay-500 {
  animation-delay: 500ms;
}
</style>
