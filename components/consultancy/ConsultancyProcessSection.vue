<!-- components/consultancy/ConsultancyProcessSection.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Process steps
const processSteps = ref([
  {
    id: 1,
    title: "Discovery & Assessment",
    description:
      "We begin by understanding your business, challenges, and goals through in-depth discussions and analysis of your current systems and processes.",
    icon: "magnifying-glass",
    color: "indigo",
  },
  {
    id: 2,
    title: "Strategy Development",
    description:
      "Based on our findings, we create a comprehensive roadmap with clear objectives, milestones, and deliverables tailored to your specific needs.",
    icon: "light-bulb",
    color: "purple",
  },
  {
    id: 3,
    title: "Solution Implementation",
    description:
      "Our expert team executes the strategy, developing and implementing customized solutions while maintaining constant communication.",
    icon: "code",
    color: "blue",
  },
  {
    id: 4,
    title: "Testing & Optimization",
    description:
      "We rigorously test all solutions, collecting data and making continuous improvements to ensure optimal performance and results.",
    icon: "chart",
    color: "green",
  },
  {
    id: 5,
    title: "Deployment & Training",
    description:
      "Once fully optimized, we deploy the solution and provide comprehensive training to ensure your team can effectively utilize and maintain it.",
    icon: "academic-cap",
    color: "amber",
  },
  {
    id: 6,
    title: "Ongoing Support",
    description:
      "Our relationship doesn't end at deployment. We provide continuous support, monitoring, and updates to ensure long-term success.",
    icon: "lifebuoy",
    color: "red",
  },
]);

// Animation state
const isVisible = ref(false);

// Track visible steps for staggered animation
const visibleSteps = ref(new Set());

// Intersection observer for scroll animations
onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          isVisible.value = true;
          observer.disconnect();
        }
      });
    },
    { threshold: 0.1 }
  );

  const section = document.querySelector(".process-section");
  if (section) {
    observer.observe(section);
  }

  // Create observers for individual steps
  const stepObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const stepId = entry.target.getAttribute("data-step-id");
          if (stepId) {
            visibleSteps.value.add(parseInt(stepId));
          }
        }
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -100px 0px" }
  );

  // Observe each step
  document.querySelectorAll(".process-step").forEach((step) => {
    stepObserver.observe(step);
  });
});
</script>

<template>
  <section class="process-section py-20 bg-gray-50 overflow-hidden relative">
    <!-- Background decorative elements -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div
        class="absolute -top-20 right-10 w-80 h-80 bg-indigo-100 rounded-full opacity-30 blur-3xl"
      ></div>
      <div
        class="absolute -bottom-40 -left-20 w-80 h-80 bg-purple-100 rounded-full opacity-30 blur-3xl"
      ></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
      <!-- Section header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span
          class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 mb-4 transform transition-all duration-1000"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
        >
          Our Process
        </span>

        <h2
          class="text-3xl md:text-4xl font-bold mb-6 transform transition-all duration-1000"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 200ms"
        >
          How We Deliver Results
        </h2>

        <p
          class="text-lg text-gray-600 transform transition-all duration-1000"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 400ms"
        >
          We follow a proven, systematic approach to ensure successful outcomes
          for every project. Our process is designed to be collaborative,
          transparent, and focused on delivering measurable results.
        </p>
      </div>

      <!-- Process timeline -->
      <div class="relative max-w-4xl mx-auto">
        <!-- Timeline line -->
        <div
          class="absolute top-0 bottom-0 left-1/2 w-1 bg-gray-200 transform -translate-x-1/2 hidden md:block"
        ></div>

        <!-- Process steps -->
        <div class="space-y-16 md:space-y-0">
          <div
            v-for="(step, index) in processSteps"
            :key="step.id"
            class="process-step flex flex-col md:flex-row md:items-center md:space-x-6"
            :class="
              index % 2 === 0
                ? 'md:flex-row'
                : 'md:flex-row-reverse md:space-x-reverse'
            "
            :data-step-id="step.id"
          >
            <!-- Step number with icon (center of timeline) -->
            <div
              class="flex-shrink-0 w-16 h-16 rounded-full mx-auto md:mx-0 flex items-center justify-center z-10 shadow-lg transform transition-all duration-1000 border-4 border-white"
              :class="[
                `bg-${step.color}-600`,
                visibleSteps.has(step.id)
                  ? 'opacity-100 scale-100'
                  : 'opacity-0 scale-50',
              ]"
              :style="`transition-delay: ${(step.id - 1) * 100}ms`"
            >
              <!-- Magnifying glass icon -->
              <svg
                v-if="step.icon === 'magnifying-glass'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
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

              <!-- Light bulb icon -->
              <svg
                v-else-if="step.icon === 'light-bulb'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
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

              <!-- Code icon -->
              <svg
                v-else-if="step.icon === 'code'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                />
              </svg>

              <!-- Chart icon -->
              <svg
                v-else-if="step.icon === 'chart'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                />
              </svg>

              <!-- Academic cap icon -->
              <svg
                v-else-if="step.icon === 'academic-cap'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path
                  d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
                />
              </svg>

              <!-- Lifebuoy icon -->
              <svg
                v-else-if="step.icon === 'lifebuoy'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>

              <!-- Default icon -->
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                />
              </svg>
            </div>

            <!-- Content card -->
            <div
              class="bg-white rounded-xl p-6 shadow-lg flex-1 mt-6 md:mt-0 transform transition-all duration-1000 relative"
              :class="[
                visibleSteps.has(step.id)
                  ? 'opacity-100 translate-y-0 translate-x-0'
                  : 'opacity-0',
                index % 2 === 0 ? 'md:-translate-x-8' : 'md:translate-x-8',
              ]"
              :style="`transition-delay: ${100 + (step.id - 1) * 100}ms`"
            >
              <!-- Card connector (hidden on mobile) -->
              <div
                v-if="index % 2 === 0"
                class="absolute top-1/2 -left-4 w-4 h-4 bg-white transform rotate-45 -translate-y-1/2 hidden md:block"
              ></div>
              <div
                v-else
                class="absolute top-1/2 -right-4 w-4 h-4 bg-white transform rotate-45 -translate-y-1/2 hidden md:block"
              ></div>

              <!-- Step number badge -->
              <div
                class="absolute -top-4 left-6 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                :class="`bg-${step.color}-600`"
              >
                {{ step.id }}
              </div>

              <!-- Content -->
              <div class="pt-2">
                <h3
                  class="text-xl font-bold mb-2"
                  :class="`text-${step.color}-600`"
                >
                  {{ step.title }}
                </h3>
                <p class="text-gray-600">{{ step.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
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

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.5);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
