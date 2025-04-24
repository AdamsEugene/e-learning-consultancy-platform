<!-- components/consultancy/ConsultancyServiceCard.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

interface Props {
  service: {
    id: number;
    title: string;
    description: string;
    icon: string;
    color: string;
    features: string[];
    popular?: boolean;
  };
  animated?: boolean;
  delay?: number;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  animated: true,
  delay: 0,
});

// Animation state
const isHovered = ref(false);

// Computed gradient based on color
const gradient = computed(() => {
  switch (props.service.color) {
    case "indigo":
      return "from-indigo-500 to-indigo-700";
    case "purple":
      return "from-purple-500 to-purple-700";
    case "blue":
      return "from-blue-500 to-blue-700";
    case "green":
      return "from-green-500 to-green-700";
    case "teal":
      return "from-teal-500 to-teal-700";
    case "red":
      return "from-red-500 to-red-700";
    default:
      return "from-indigo-500 to-indigo-700";
  }
});

// Computed color classes


// Determine icon component

</script>

<template>
  <div
    class="group relative overflow-hidden rounded-xl bg-white p-6 border border-gray-200 shadow-lg transition-all duration-500 h-full"
    :class="[
      `hover:border-${service.color}-500`,
      isHovered ? `shadow-glow-${service.color}` : '',
    ]"
    :style="animated ? `transition-delay: ${delay}ms;` : ''"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Decorative background gradient -->
    <div
      class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-10 bg-gradient-to-br transition-opacity duration-700"
      :class="gradient"
      :style="isHovered ? 'opacity: 0.2' : 'opacity: 0.1'"
    />

    <!-- Popular badge if applicable -->
    <div
      v-if="service.popular"
      class="absolute top-3 right-3 rounded-full px-3 py-1 text-xs font-bold shadow-md"
      :class="[`bg-${service.color}-100`, `text-${service.color}-800`]"
    >
      Popular
    </div>

    <!-- Service icon -->
    <div
      class="relative mb-4 inline-flex items-center justify-center p-3 rounded-lg transition-all duration-500"
      :class="[
        `bg-${service.color}-100`, 
        isHovered ? `shadow-sm bg-${service.color}-50` : ''
      ]"
    >
      <svg
        v-if="service.icon === 'code'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
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
      <svg
        v-else-if="service.icon === 'refresh-cw'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
        />
      </svg>
      <svg
        v-else-if="service.icon === 'layout'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"
        />
      </svg>
      <svg
        v-else-if="service.icon === 'users'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
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
      <svg
        v-else-if="service.icon === 'trending-up'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
        />
      </svg>
      <svg
        v-else-if="service.icon === 'database'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
        />
      </svg>
      <svg
        v-else-if="service.icon === 'shield'"
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
        />
      </svg>
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 transition-colors duration-500"
        :class="`text-${service.color}-600`"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
    </div>

    <!-- Service title with slide effect -->
    <h3
      class="text-xl font-bold mb-2 transition-all duration-500 relative"
      :class="isHovered ? `text-${service.color}-700` : 'text-gray-900'"
    >
      {{ service.title }}
      <span 
        class="absolute bottom-0 left-0 w-0 h-0.5 transition-all duration-500" 
        :class="`bg-${service.color}-600`"
        :style="isHovered ? 'width: 100%' : 'width: 0'"
      ></span>
    </h3>

    <!-- Service description -->
    <p class="text-gray-600 mb-4 relative">{{ service.description }}</p>

    <!-- Features list with subtle animation -->
    <ul class="space-y-2 mb-6 relative">
      <li
        v-for="(feature, index) in service.features"
        :key="index"
        class="flex items-center transition-colors duration-300 cursor-default"
        :style="`transition-delay: ${index * 50}ms`"
        :class="isHovered ? `text-${service.color}-900` : ''"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 transition-colors duration-500"
          :class="`text-${service.color}-500`"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <span>{{ feature }}</span>
      </li>
    </ul>

    <!-- Learn more button with shadow on hover -->
    <div class="relative">
      <button
        class="inline-flex items-center font-medium rounded-lg transition-all duration-500 cursor-pointer px-4 py-2 border"
        :class="[
          isHovered
            ? `text-white bg-${service.color}-600 border-${service.color}-600 shadow-md`
            : `text-${service.color}-600 border-${service.color}-300 bg-${service.color}-50 hover:bg-${service.color}-100`,
        ]"
      >
        <span>Learn more</span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 ml-1 transition-all duration-500"
          :class="isHovered ? 'translate-x-1' : ''"
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
</template>

<style scoped>
/* Additional animations for the card */
@keyframes pulse-subtle {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
}

.popular-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: linear-gradient(
    135deg,
    var(--color-primary),
    var(--color-secondary)
  );
  color: white;
  font-weight: bold;
  font-size: 0.75rem;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Glow shadows */
.shadow-glow-indigo {
  box-shadow: 0 0 15px rgba(79, 70, 229, 0.2);
}
.shadow-glow-purple {
  box-shadow: 0 0 15px rgba(168, 85, 247, 0.2);
}
.shadow-glow-blue {
  box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
}
.shadow-glow-green {
  box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
}
.shadow-glow-teal {
  box-shadow: 0 0 15px rgba(20, 184, 166, 0.2);
}
.shadow-glow-red {
  box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);
}
</style>
