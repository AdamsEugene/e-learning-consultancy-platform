<!-- components/courses/filters/StarRating.vue -->
<script setup lang="ts">
// Props
interface Props {
  rating: number;
  size?: "sm" | "md" | "lg";
  showLabel?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  size: "md",
  showLabel: false,
});

// Calculate full and partial stars
const fullStars = Math.floor(props.rating);
const hasHalfStar = props.rating % 1 >= 0.5;
const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

// Classes based on size
const sizeClasses = {
  sm: {
    container: "text-xs",
    star: "h-3 w-3",
  },
  md: {
    container: "text-sm",
    star: "h-4 w-4",
  },
  lg: {
    container: "text-base",
    star: "h-5 w-5",
  },
};

// Format rating for display
const formattedRating = props.rating.toFixed(1);
</script>

<template>
  <div class="flex items-center" :class="sizeClasses[size].container">
    <!-- Full stars -->
    <svg
      v-for="i in fullStars"
      :key="`full-${i}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="sizeClasses[size].star"
      viewBox="0 0 20 20"
      fill="currentColor"
      class="text-yellow-400"
    >
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Half star -->
    <svg
      v-if="hasHalfStar"
      xmlns="http://www.w3.org/2000/svg"
      :class="sizeClasses[size].star"
      viewBox="0 0 20 20"
      fill="currentColor"
      class="text-yellow-400"
    >
      <!-- Clip path for half star effect -->
      <defs>
        <linearGradient id="halfStarGradient" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="50%" stop-color="currentColor" />
          <stop offset="50%" stop-color="#D1D5DB" />
        </linearGradient>
      </defs>
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
        fill="url(#halfStarGradient)"
      />
    </svg>

    <!-- Empty stars -->
    <svg
      v-for="i in emptyStars"
      :key="`empty-${i}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="sizeClasses[size].star"
      viewBox="0 0 20 20"
      fill="currentColor"
      class="text-gray-300"
    >
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Rating label -->
    <span v-if="showLabel" class="ml-1 text-gray-700">{{
      formattedRating
    }}</span>
  </div>
</template>
