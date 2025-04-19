<!-- components/consultancy/ConsultancyProcessSection.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

interface ProcessStep {
  id: number;
  title: string;
  description: string;
  icon: string;
  color: string;
}

interface Props {
  title?: string;
  subtitle?: string;
  description?: string;
  steps?: ProcessStep[];
}

// Props
const props = withDefaults(defineProps<Props>(), {
  title: "Our Consultancy Process",
  subtitle: "How We Work With You",
  description:
    "We follow a proven, structured approach to deliver exceptional results for your business. Our consultancy process ensures clear communication and measurable outcomes at every stage.",
  steps: () => [
    {
      id: 1,
      title: "Discovery & Assessment",
      description:
        "We start by deeply understanding your business, goals, and challenges through comprehensive analysis and stakeholder interviews.",
      icon: "search",
      color: "indigo",
    },
    {
      id: 2,
      title: "Strategy Development",
      description:
        "Based on our findings, we craft a tailored strategy and detailed implementation plan with clear timelines and deliverables.",
      icon: "document",
      color: "purple",
    },
    {
      id: 3,
      title: "Implementation",
      description:
        "Our expert team works alongside yours to execute the strategy, providing guidance and support throughout the process.",
      icon: "cog",
      color: "blue",
    },
    {
      id: 4,
      title: "Evaluation & Optimization",
      description:
        "We continuously monitor progress, measure results against KPIs, and make data-driven adjustments to optimize outcomes.",
      icon: "chart",
      color: "teal",
    },
    {
      id: 5,
      title: "Knowledge Transfer",
      description:
        "We ensure your team has the skills and knowledge to maintain and build upon the improvements we've implemented.",
      icon: "academic",
      color: "green",
    },
  ],
});

// Animation state
const visibleSteps = ref<number[]>([]);

// Check if step is visible
const isStepVisible = (stepId: number) => {
  return visibleSteps.value.includes(stepId);
};

// Observe elements for animation
onMounted(() => {
  if (typeof window !== "undefined" && "IntersectionObserver" in window) {
    const options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.2,
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const stepId = parseInt(
            entry.target.getAttribute("data-step-id") || "0"
          );
          if (stepId && !visibleSteps.value.includes(stepId)) {
            visibleSteps.value.push(stepId);
          }
        }
      });
    }, options);

    // Observe each step
    document.querySelectorAll(".process-step").forEach((step) => {
      observer.observe(step);
    });
  } else {
    // Fallback for browsers without IntersectionObserver
    props.steps.forEach((step) => {
      visibleSteps.value.push(step.id);
    });
  }
});

// Get icon based on name - Using the previously unused variable getIconComponent
const getIconComponent = (iconName: string) => {
  switch (iconName) {
    case "search":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>`;
    case "document":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>`;
    case "cog":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>`;
    case "chart":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
      </svg>`;
    case "academic":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path d="M12 14l9-5-9-5-9 5 9 5z" />
        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>`;
  }
};

// Get color classes for a step - Using the previously unused variable getColorClasses
const getColorClasses = (color: string) => {
  switch (color) {
    case "indigo":
      return {
        bg: "bg-indigo-100",
        text: "text-indigo-600",
        line: "border-indigo-200",
        iconBg: "bg-indigo-500",
      };
    case "purple":
      return {
        bg: "bg-purple-100",
        text: "text-purple-600",
        line: "border-purple-200",
        iconBg: "bg-purple-500",
      };
    case "blue":
      return {
        bg: "bg-blue-100",
        text: "text-blue-600",
        line: "border-blue-200",
        iconBg: "bg-blue-500",
      };
    case "teal":
      return {
        bg: "bg-teal-100",
        text: "text-teal-600",
        line: "border-teal-200",
        iconBg: "bg-teal-500",
      };
    case "green":
      return {
        bg: "bg-green-100",
        text: "text-green-600",
        line: "border-green-200",
        iconBg: "bg-green-500",
      };
    case "red":
      return {
        bg: "bg-red-100",
        text: "text-red-600",
        line: "border-red-200",
        iconBg: "bg-red-500",
      };
    default:
      return {
        bg: "bg-gray-100",
        text: "text-gray-600",
        line: "border-gray-200",
        iconBg: "bg-gray-500",
      };
  }
};
</script>

<!-- eslint-disable vue/no-v-html -->
<template>
  <section class="py-20 bg-white">
    <div class="container mx-auto px-4">
      <!-- Section header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span
          class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 mb-4"
        >
          {{ subtitle }}
        </span>
        <h2 class="text-3xl md:text-4xl font-bold mb-6">{{ title }}</h2>
        <p class="text-lg text-gray-600">{{ description }}</p>
      </div>

      <!-- Process steps timeline -->
      <div class="max-w-4xl mx-auto relative">
        <!-- Timeline line -->
        <div
          class="absolute left-1/2 top-0 bottom-0 w-0.5 bg-gray-200 transform -translate-x-1/2"
        />

        <!-- Process steps -->
        <div class="space-y-20">
          <div
            v-for="(step, index) in steps"
            :key="step.id"
            class="process-step relative"
            :data-step-id="step.id"
          >
            <!-- Step with alternating layout -->
            <div
              class="flex flex-col md:flex-row items-center"
              :class="index % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'"
            >
              <!-- Left content (or right on odd steps) -->
              <div
                class="w-full md:w-5/12 mb-8 md:mb-0 transition-all duration-1000 transform"
                :class="[
                  isStepVisible(step.id) ? 'opacity-100' : 'opacity-0',
                  index % 2 === 0
                    ? isStepVisible(step.id)
                      ? 'md:translate-x-0'
                      : 'md:-translate-x-10'
                    : isStepVisible(step.id)
                    ? 'md:translate-x-0'
                    : 'md:translate-x-10',
                ]"
                :style="`transition-delay: ${index * 200}ms`"
              >
                <div
                  class="p-6 rounded-xl shadow-lg border border-gray-100 h-full bg-white relative overflow-hidden"
                  :class="[
                    `hover:border-${step.color}-200`,
                    `hover:shadow-${step.color}-100/30`,
                  ]"
                >
                  <!-- Using the getColorClasses for consistent styling -->
                  <div
                    class="absolute top-0 left-0 w-2 h-full"
                    :class="getColorClasses(step.color).iconBg"
                  />

                  <!-- Step number badge -->
                  <div
                    class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm"
                    :class="getColorClasses(step.color).iconBg"
                  >
                    {{ step.id }}
                  </div>

                  <div class="pl-6">
                    <h3
                      class="text-xl font-bold mb-3"
                      :class="`text-${step.color}-700`"
                    >
                      {{ step.title }}
                    </h3>
                    <p class="text-gray-600">{{ step.description }}</p>
                  </div>
                </div>
              </div>

              <!-- Center icon - using the getColorClasses and getIconComponent -->
              <div
                class="relative z-10 mx-8 transition-all duration-1000 transform timeline-icon"
                :class="[
                  isStepVisible(step.id)
                    ? 'opacity-100 scale-100'
                    : 'opacity-0 scale-0',
                ]"
                :style="`transition-delay: ${index * 200 + 300}ms`"
              >
                <div
                  class="w-16 h-16 rounded-full flex items-center justify-center border-4 border-white shadow-lg text-white step-icon transition-transform duration-300"
                  :class="getColorClasses(step.color).iconBg"
                  v-html="getIconComponent(step.icon)"
                />
              </div>

              <!-- Right content (or left on odd steps) -->
              <div
                class="w-full md:w-5/12 transition-all duration-1000 transform step-content"
                :class="[
                  isStepVisible(step.id) ? 'opacity-100' : 'opacity-0',
                  index % 2 === 0
                    ? isStepVisible(step.id)
                      ? 'md:translate-x-0'
                      : 'md:translate-x-10'
                    : isStepVisible(step.id)
                    ? 'md:translate-x-0'
                    : 'md:-translate-x-10',
                ]"
                :style="`transition-delay: ${index * 200 + 200}ms`"
              >
                <!-- Empty div for layout in mobile -->
                <div class="md:hidden h-8" />

                <!-- Empty div for layout on desktop -->
                <div class="hidden md:block">
                  <!-- This space intentionally left empty for layout purposes -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA at the bottom of the process section -->
      <div class="text-center mt-20">
        <p class="text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
          Ready to transform your business with our proven consultancy process?
          Get in touch with our experts today.
        </p>
        <NuxtLink
          to="/consultancy/request"
          class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition-all transform hover:scale-105 shadow-lg"
        >
          Start Your Consultation
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
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Animation classes for the process timeline */
.process-step:hover .step-icon {
  transform: scale(1.1);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

@media (min-width: 768px) {
  .process-step:nth-child(odd) .step-content {
    text-align: right;
    margin-right: 2rem;
  }

  .process-step:nth-child(even) .step-content {
    text-align: left;
    margin-left: 2rem;
  }
}

/* Pulse animation for the timeline circles */
@keyframes pulse-soft {
  0%,
  100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 0 10px rgba(99, 102, 241, 0);
  }
}

.timeline-icon:hover {
  animation: pulse-soft 2s infinite;
}
</style>
