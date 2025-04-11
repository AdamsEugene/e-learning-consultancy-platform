<!-- components/auth/TestimonialBadge.vue -->
<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  name: string;
  role: string;
  avatar?: string;
  quote?: string;
}>();

// Calculate initials from name - this is used in the template
const initials = computed(() => {
  return props.name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
});

const hoverRef = ref(null);
const isHovering = ref(false);

// Set up event listeners for hover
const setHovering = (value: boolean) => {
  isHovering.value = value;
};

</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    ref="hoverRef"
    class="testimonial-badge relative"
    @mouseenter="setHovering(true)"
    @mouseleave="setHovering(false)"
  >
    <div class="flex items-center group">
      <div class="relative">
        <div
          class="h-12 w-12 rounded-full overflow-hidden bg-indigo-400 border-2 border-white shadow-md transition-all duration-300 group-hover:scale-105"
        >
          <img
            v-if="avatar"
            :src="avatar"
            :alt="name"
            class="h-full w-full object-cover"
          />
          <div
            v-else
            class="flex h-full w-full items-center justify-center text-white font-medium bg-gradient-to-br from-indigo-500 to-purple-600"
          >
            {{ initials }}
          </div>
        </div>
        <div
          class="absolute -bottom-1 -right-1 h-5 w-5 bg-green-400 rounded-full border-2 border-white"
        />
      </div>
      <div class="ml-3">
        <p class="text-sm font-bold text-white">{{ name }}</p>
        <p class="text-xs text-indigo-200">{{ role }}</p>
      </div>
    </div>

    <!-- Testimonial Quote Popup -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isHovering && quote"
        class="absolute -left-1 bottom-20 mt-2 p-4 bg-white rounded-lg shadow-xl w-64 z-10 text-gray-700"
      >
        <div
          class="absolute -bottom-2 left-6 w-4 h-4 bg-white transform rotate-45"
        />
        <p class="text-sm italic">"{{ quote }}"</p>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.testimonial-badge {
  transition: all 0.3s ease;
}
</style>
