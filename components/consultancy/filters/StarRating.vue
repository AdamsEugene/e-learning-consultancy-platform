<!-- components/consultancy/filters/StarRating.vue -->
<script setup lang="ts">
import { computed } from "vue";

interface Props {
  rating: number;
  maxRating?: number;
  size?: "sm" | "md" | "lg";
  color?: string;
  showValue?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  maxRating: 5,
  size: "md",
  color: "text-yellow-400",
  showValue: false,
});

// Number of full, half, and empty stars
const fullStars = computed(() => Math.floor(props.rating));
const hasHalfStar = computed(() => props.rating % 1 >= 0.5);
const emptyStars = computed(() =>
  Math.floor(props.maxRating - props.rating - (hasHalfStar.value ? 0.5 : 0))
);

// Star size classes
const sizeClasses = computed(() => {
  switch (props.size) {
    case "sm":
      return "h-3 w-3";
    case "lg":
      return "h-6 w-6";
    default:
      return "h-4 w-4";
  }
});

// Generate array of stars for v-for
const fullStarsList = computed(() => Array(fullStars.value).fill("full"));
const halfStarsList = computed(() => (hasHalfStar.value ? [1] : []));
const emptyStarsList = computed(() => Array(emptyStars.value).fill("empty"));
</script>

<template>
  <div class="flex items-center">
    <!-- Full stars -->
    <svg
      v-for="(_, index) in fullStarsList"
      :key="`full-${index}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="[sizeClasses, color]"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Half star -->
    <svg
      v-for="(_, index) in halfStarsList"
      :key="`half-${index}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="[sizeClasses, color]"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <defs>
        <linearGradient id="half-fill" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop
            offset="50%"
            :stop-color="
              color === 'text-yellow-400' ? '#f59e0b' : 'currentColor'
            "
          />
          <stop offset="50%" stop-color="#d1d5db" />
        </linearGradient>
      </defs>
      <path
        fill="url(#half-fill)"
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Empty stars -->
    <svg
      v-for="(_, index) in emptyStarsList"
      :key="`empty-${index}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="[sizeClasses, 'text-gray-300']"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Numeric rating -->
    <span
      v-if="showValue"
      class="ml-2 text-gray-600"
      :class="{ 'text-xs': size === 'sm', 'text-base': size === 'lg' }"
    >
      {{ rating.toFixed(1) }}
    </span>
  </div>
</template>
