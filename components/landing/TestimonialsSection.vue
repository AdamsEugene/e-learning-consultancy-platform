<script lang="ts" setup>
interface Props {
  testimonials: {
    id: number;
    name: string;
    role: string;
    text: string;
    rating: number;
    image?: string;
  }[];
  activeTestimonial: number;
  setTestimonial: (index: number) => void;
}

defineProps<Props>();

// Generate avatars for testimonials that don't have images
const getInitials = (name: string) => {
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase();
};

// Avatar background colors based on first letter of name
const avatarColors = [
  "bg-gradient-to-br from-indigo-500 to-indigo-700",
  "bg-gradient-to-br from-purple-500 to-purple-700",
  "bg-gradient-to-br from-blue-500 to-blue-700",
  "bg-gradient-to-br from-pink-500 to-pink-700",
  "bg-gradient-to-br from-green-500 to-green-700",
  "bg-gradient-to-br from-amber-500 to-amber-700",
];

const getAvatarColor = (name: string) => {
  const index = name.charCodeAt(0) % avatarColors.length;
  return avatarColors[index];
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    id="testimonials"
    class="py-20 px-4 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden"
  >
    <!-- Background elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Decorative blurred circles -->
      <div
        class="absolute -right-64 top-20 w-96 h-96 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full opacity-30 blur-2xl"
      />
      <div
        class="absolute -left-32 bottom-20 w-80 h-80 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full opacity-30 blur-2xl"
      />

      <!-- Floating quotes -->
      <div
        v-for="i in 10"
        :key="i"
        class="quote-particle absolute text-3xl font-serif opacity-5 animate-float"
      >
        "
      </div>

      <!-- Decorative grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
      <!-- Section Header with animated underline -->
      <div class="text-center mb-16">
        <div class="mb-3">
          <span
            class="bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-800 text-sm font-bold px-4 py-2 rounded-full shadow-sm"
          >
            Success Stories
          </span>
        </div>
        <h2
          class="text-3xl md:text-5xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600"
        >
          What Our Students Say
        </h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Hear from students and clients who have transformed their careers and
          businesses with our platform.
        </p>

        <!-- Animated underline -->
        <div
          class="h-1 w-24 bg-gradient-to-r from-purple-500 to-indigo-500 mx-auto mt-6 rounded-full relative overflow-hidden"
        >
          <div class="absolute inset-0 bg-white animate-shimmer" />
        </div>
      </div>

      <div class="relative">
        <!-- Large quotation mark -->
        <div
          class="absolute -top-10 left-10 text-8xl text-purple-200 font-serif z-0 opacity-50"
        >
          "
        </div>

        <!-- Testimonial cards carousel with shadow effect -->
        <div class="overflow-hidden rounded-2xl shadow-2xl">
          <div
            class="flex transition-all duration-700 ease-in-out"
            :style="{ transform: `translateX(-${activeTestimonial * 100}%)` }"
          >
            <div
              v-for="testimonial in testimonials"
              :key="testimonial.id"
              class="w-full flex-shrink-0 p-1"
            >
              <div
                class="bg-white rounded-xl p-8 h-full relative overflow-hidden"
              >
                <!-- Background decoration -->
                <div
                  class="absolute top-0 right-0 w-40 h-40 bg-purple-50 rounded-full -mr-20 -mt-20 z-0"
                />
                <div
                  class="absolute bottom-0 left-0 w-40 h-40 bg-indigo-50 rounded-full -ml-20 -mb-20 z-0"
                />

                <!-- Content area with staggered animation -->
                <div class="relative z-10">
                  <!-- User info with avatar -->
                  <div
                    class="flex items-center mb-8 animate-fade-right"
                    style="animation-delay: 0ms"
                  >
                    <div class="relative">
                      <div
                        v-if="testimonial.image"
                        class="w-20 h-20 rounded-full overflow-hidden shadow-lg border-2 border-white"
                      >
                        <img
                          :src="testimonial.image"
                          :alt="testimonial.name"
                          class="w-full h-full object-cover"
                        />
                      </div>
                      <div
                        v-else
                        class="w-20 h-20 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg"
                        :class="getAvatarColor(testimonial.name)"
                      >
                        {{ getInitials(testimonial.name) }}
                      </div>

                      <!-- Pulsing ring effect -->
                      <div
                        class="absolute inset-0 rounded-full border-4 border-purple-300 opacity-0 animate-ping-slow"
                      />
                    </div>

                    <div class="ml-6">
                      <h4 class="font-bold text-xl text-gray-800">
                        {{ testimonial.name }}
                      </h4>
                      <p class="text-purple-600 font-medium">
                        {{ testimonial.role }}
                      </p>
                      <!-- Star rating -->
                      <div class="flex text-yellow-400 mt-2">
                        <svg
                          v-for="i in 5"
                          :key="i"
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          :class="{ 'text-gray-300': i > testimonial.rating }"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- Testimonial quote -->
                  <blockquote
                    class="relative animate-fade-up"
                    style="animation-delay: 300ms"
                  >
                    <p
                      class="text-gray-700 text-xl leading-relaxed font-medium"
                    >
                      "{{ testimonial.text }}"
                    </p>
                  </blockquote>

                  <!-- Additional decoration -->
                  <div
                    class="mt-8 pt-4 border-t border-gray-100 flex justify-between items-center animate-fade-up"
                    style="animation-delay: 500ms"
                  >
                    <div class="text-sm text-gray-500">Verified Review</div>
                    <div class="text-sm text-indigo-600 font-semibold">
                      Course Completed
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Improved carousel navigation -->
        <div class="mt-12 flex justify-center items-center">
          <!-- Previous button -->
          <button
            class="w-12 h-12 rounded-full bg-white shadow-md flex items-center justify-center mr-6 transition-all hover:shadow-lg disabled:opacity-50"
            :disabled="activeTestimonial === 0"
            @click="
              activeTestimonial > 0 && setTestimonial(activeTestimonial - 1)
            "
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-indigo-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>

          <!-- Indicators -->
          <div class="flex items-center space-x-2">
            <button
              v-for="(_, index) in testimonials"
              :key="index"
              class="h-3 rounded-full transition-all duration-300 shadow-sm"
              :class="[
                activeTestimonial === index
                  ? 'bg-gradient-to-r from-purple-500 to-indigo-500 w-10'
                  : 'bg-gray-300 hover:bg-gray-400 w-3',
              ]"
              @click="setTestimonial(index)"
            />
          </div>

          <!-- Next button -->
          <button
            class="w-12 h-12 rounded-full bg-white shadow-md flex items-center justify-center ml-6 transition-all hover:shadow-lg disabled:opacity-50"
            :disabled="activeTestimonial === testimonials.length - 1"
            @click="
              activeTestimonial < testimonials.length - 1 &&
                setTestimonial(activeTestimonial + 1)
            "
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-indigo-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </div>

        <!-- Testimonial count -->
        <div class="mt-8 text-center text-gray-500">
          <span>{{ activeTestimonial + 1 }} of {{ testimonials.length }}</span>
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

@keyframes float {
  0%,
  100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-15px) rotate(5deg);
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

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeRight {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes pingSlow {
  0% {
    transform: scale(1);
    opacity: 0;
  }
  50% {
    opacity: 0.3;
  }
  100% {
    transform: scale(1.5);
    opacity: 0;
  }
}

.animate-float {
  animation: float 8s ease-in-out infinite;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

.animate-fade-up {
  opacity: 0;
  animation: fadeUp 0.8s forwards;
}

.animate-fade-right {
  opacity: 0;
  animation: fadeRight 0.8s forwards;
}

.animate-ping-slow {
  animation: pingSlow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.quote-particle {
  font-size: 80px;
  color: rgba(124, 58, 237, 0.1);
}

.quote-particle:nth-child(1) {
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}
.quote-particle:nth-child(2) {
  top: 30%;
  left: 20%;
  animation-delay: 2s;
}
.quote-particle:nth-child(3) {
  top: 60%;
  left: 5%;
  animation-delay: 4s;
}
.quote-particle:nth-child(4) {
  top: 20%;
  left: 80%;
  animation-delay: 1s;
}
.quote-particle:nth-child(5) {
  top: 70%;
  left: 85%;
  animation-delay: 3s;
}
.quote-particle:nth-child(6) {
  top: 40%;
  left: 90%;
  animation-delay: 5s;
}
.quote-particle:nth-child(7) {
  top: 80%;
  left: 40%;
  animation-delay: 2.5s;
}
.quote-particle:nth-child(8) {
  top: 50%;
  left: 60%;
  animation-delay: 1.5s;
}
.quote-particle:nth-child(9) {
  top: 5%;
  left: 50%;
  animation-delay: 3.5s;
}
.quote-particle:nth-child(10) {
  top: 90%;
  left: 10%;
  animation-delay: 4.5s;
}
</style>
