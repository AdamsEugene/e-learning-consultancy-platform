<script lang="ts" setup>
interface Props {
  visibleSections: {
    features: boolean;
    courses: boolean;
    consultancy: boolean;
    testimonials: boolean;
    cta: boolean;
  };
}

defineProps<Props>();

// Feature metadata with fixed styles and animations
const features = [
  {
    id: 1,
    title: "Expert-Led Courses",
    description:
      "Learn from industry professionals with real-world experience and proven expertise.",
    icon: "lightbulb",
    color: {
      primary: "bg-gradient-to-br from-indigo-500 to-indigo-700",
      secondary: "bg-indigo-100",
      text: "text-indigo-600",
      shadow: "shadow-indigo-200",
      ring: "ring-indigo-500/30",
    },
    bullets: [
      "Professional instructors from top companies",
      "Real-world case studies and applications",
      "Industry-validated content and certifications",
    ],
    animationDelay: "delay-0",
    scale: "hover:scale-105",
  },
  {
    id: 2,
    title: "Flexible Learning",
    description:
      "Study at your own pace with lifetime access to all course materials and resources.",
    icon: "clock",
    color: {
      primary: "bg-gradient-to-br from-purple-500 to-purple-700",
      secondary: "bg-purple-100",
      text: "text-purple-600",
      shadow: "shadow-purple-200",
      ring: "ring-purple-500/30",
    },
    bullets: [
      "Learn anytime, anywhere on any device",
      "Mobile-friendly platform with offline access",
      "Downloadable resources and practice materials",
    ],
    animationDelay: "delay-200",
    scale: "hover:scale-105",
  },
  {
    id: 3,
    title: "Personalized Consultancy",
    description:
      "Get tailored advice and solutions for your specific business challenges and goals.",
    icon: "users",
    color: {
      primary: "bg-gradient-to-br from-blue-500 to-blue-700",
      secondary: "bg-blue-100",
      text: "text-blue-600",
      shadow: "shadow-blue-200",
      ring: "ring-blue-500/30",
    },
    bullets: [
      "1-on-1 expert sessions with industry leaders",
      "Custom solution design for your specific needs",
      "Ongoing support and implementation guidance",
    ],
    animationDelay: "delay-400",
    scale: "hover:scale-105",
  },
];
</script>

<template>
  <section
    class="features-section py-20 px-4 bg-gray-50 relative overflow-hidden"
  >
    <!-- Background elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Animated gradient background -->
      <div
        class="absolute inset-0 bg-gradient-to-b from-gray-50 to-indigo-50 opacity-70"
      />

      <!-- Decorative circles -->
      <div
        class="absolute top-0 right-0 w-96 h-96 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 opacity-30 blur-2xl transform -translate-x-1/4 -translate-y-1/4"
      />
      <div
        class="absolute bottom-0 left-0 w-96 h-96 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 opacity-30 blur-2xl transform translate-x-1/4 translate-y-1/4"
      />

      <!-- Decorative grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
      <!-- Section header with animated underline -->
      <div class="text-center mb-20 relative">
        <div
          class="inline-block text-sm font-bold px-3 py-1 rounded-full mb-4 bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 shadow-sm"
        >
          Our Platform Benefits
        </div>

        <h2
          class="text-3xl md:text-5xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600"
        >
          Why Learn With Us
        </h2>

        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Our platform combines quality education with professional expertise to
          provide you with the best learning experience.
        </p>

        <!-- Animated underline -->
        <div
          class="h-1 w-24 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto mt-6 rounded-full relative overflow-hidden"
        >
          <div class="absolute inset-0 bg-white animate-shimmer" />
        </div>
      </div>

      <!-- Features grid with staggered animations -->
      <div class="grid md:grid-cols-3 gap-8 relative">
        <div v-for="feature in features" :key="feature.id" class="feature-card">
          <!-- Card with hover effects and animations -->
          <div
            class="h-full bg-white rounded-2xl p-8 shadow-lg border border-gray-100 transition-all duration-500 hover:shadow-xl relative group overflow-hidden animate-fade-in"
            :class="[
              feature.color.shadow,
              feature.scale,
              feature.animationDelay,
            ]"
          >
            <!-- Decorative background gradient that appears on hover -->
            <div
              class="absolute inset-0 opacity-0 group-hover:opacity-5 transition-opacity duration-500 pointer-events-none"
              :class="feature.color.primary"
            />

            <!-- Animated border on hover -->
            <div
              class="absolute inset-0 rounded-2xl ring-1 ring-gray-200 group-hover:ring-4 transition-all duration-500"
              :class="feature.color.ring"
            />

            <!-- Feature icon with floating animation -->
            <div class="relative mb-6">
              <div
                class="inline-flex items-center justify-center p-3 rounded-full text-white shadow-lg animate-float"
                :class="feature.color.primary"
              >
                <!-- Lightbulb icon -->
                <svg
                  v-if="feature.icon === 'lightbulb'"
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
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                  />
                </svg>

                <!-- Clock icon -->
                <svg
                  v-if="feature.icon === 'clock'"
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
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>

                <!-- Users icon -->
                <svg
                  v-if="feature.icon === 'users'"
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
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
              </div>

              <!-- Animated ripple effect -->
              <div
                class="animate-ping absolute inset-0 rounded-full opacity-30"
                :class="feature.color.secondary"
              />
            </div>

            <!-- Feature content with animated underline -->
            <h3
              class="text-2xl font-bold mb-3 transition-colors duration-300 group-hover:bg-clip-text group-hover:text-transparent"
              :class="[
                feature.color.text,
                `group-hover:bg-gradient-to-r from-${
                  feature.color.text.split('-')[1]
                }-600 to-${feature.color.text.split('-')[1]}-500`,
              ]"
            >
              {{ feature.title }}
            </h3>

            <p class="text-gray-600 mb-6 leading-relaxed">
              {{ feature.description }}
            </p>

            <!-- Animated bullet points that slide in on hover -->
            <ul class="space-y-3">
              <li
                v-for="(bullet, index) in feature.bullets"
                :key="index"
                class="flex items-start transition-all duration-500 transform group-hover:translate-x-2"
                :style="`transition-delay: ${index * 100}ms`"
              >
                <div
                  class="flex-shrink-0 h-6 w-6 rounded-full flex items-center justify-center mt-0.5"
                  :class="feature.color.secondary"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    :class="feature.color.text"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <span class="ml-3 text-gray-700">{{ bullet }}</span>
              </li>
            </ul>

            <!-- Learn more button that appears on hover -->
            <div
              class="mt-8 overflow-hidden h-0 group-hover:h-12 transition-all duration-500"
            >
              <button
                class="w-full py-3 rounded-lg text-white font-medium transition-transform duration-300 transform hover:translate-y-0 translate-y-12 flex items-center justify-center"
                :class="feature.color.primary"
              >
                <span>Learn More</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 ml-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats counter -->
      <div
        class="mt-20 bg-white p-10 rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden"
      >
        <div
          class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-purple-50 opacity-50"
        />

        <!-- Decorative elements -->
        <div
          class="absolute top-0 left-0 w-20 h-20 bg-indigo-100 rounded-br-full opacity-50"
        />
        <div
          class="absolute bottom-0 right-0 w-20 h-20 bg-purple-100 rounded-tl-full opacity-50"
        />

        <div class="relative grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <!-- Animated counters -->
          <div class="stat-item">
            <div
              class="text-indigo-600 font-bold text-4xl md:text-5xl mb-2 counter-animate"
            >
              150+
            </div>
            <div class="text-gray-500">Expert Courses</div>
          </div>

          <div class="stat-item">
            <div
              class="text-purple-600 font-bold text-4xl md:text-5xl mb-2 counter-animate"
            >
              48
            </div>
            <div class="text-gray-500">Industry Experts</div>
          </div>

          <div class="stat-item">
            <div
              class="text-blue-600 font-bold text-4xl md:text-5xl mb-2 counter-animate"
            >
              15k+
            </div>
            <div class="text-gray-500">Active Students</div>
          </div>

          <div class="stat-item">
            <div
              class="text-emerald-600 font-bold text-4xl md:text-5xl mb-2 counter-animate"
            >
              98%
            </div>
            <div class="text-gray-500">Satisfaction Rate</div>
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

/* Animation keyframes */
@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
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

@keyframes ping {
  0% {
    transform: scale(0.8);
    opacity: 1;
  }
  75%,
  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Applied animations */
.animate-fade-in {
  animation: fadeInUp 1s forwards;
}

.animate-float {
  animation: float 5s ease-in-out infinite;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

.animate-ping {
  animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.counter-animate {
  opacity: 0;
  animation: fadeInUp 1s forwards;
  animation-delay: 0.5s;
}

.stat-item:nth-child(1) .counter-animate {
  animation-delay: 0.2s;
}

.stat-item:nth-child(2) .counter-animate {
  animation-delay: 0.4s;
}

.stat-item:nth-child(3) .counter-animate {
  animation-delay: 0.6s;
}

.stat-item:nth-child(4) .counter-animate {
  animation-delay: 0.8s;
}

.delay-0 {
  animation-delay: 0ms;
}

.delay-200 {
  animation-delay: 200ms;
}

.delay-400 {
  animation-delay: 400ms;
}
</style>
