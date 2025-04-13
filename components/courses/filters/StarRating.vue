<!-- components/courses/filters/StarRating.vue -->
<script setup lang="ts">
interface Props {
  rating: number;
  maxStars?: number;
  size?: "sm" | "md" | "lg";
  animated?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  maxStars: 5,
  size: "md",
  animated: false,
});

// Calculate the number of full, half, and empty stars
const stars = computed(() => {
  const fullStars = Math.floor(props.rating);
  const hasHalfStar = props.rating % 1 >= 0.5;
  const emptyStars = props.maxStars - fullStars - (hasHalfStar ? 1 : 0);

  return {
    full: fullStars,
    half: hasHalfStar ? 1 : 0,
    empty: emptyStars,
  };
});

// Size class
const sizeClass = computed(() => {
  switch (props.size) {
    case "sm":
      return "h-3 w-3";
    case "lg":
      return "h-6 w-6";
    default:
      return "h-4 w-4";
  }
});
</script>

<template>
  <div class="flex text-yellow-400" :class="{ 'star-animated': animated }">
    <!-- Full stars -->
    <svg
      v-for="i in stars.full"
      :key="`full-${i}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="[sizeClass, 'star-icon']"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Half star -->
    <svg
      v-if="stars.half"
      xmlns="http://www.w3.org/2000/svg"
      :class="[sizeClass, 'star-icon']"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <defs>
        <linearGradient
          id="half-star-gradient"
          x1="0%"
          y1="0%"
          x2="100%"
          y2="0%"
        >
          <stop offset="50%" stop-color="currentColor" />
          <stop offset="50%" stop-color="#D1D5DB" />
        </linearGradient>
      </defs>
      <path
        fill="url(#half-star-gradient)"
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>

    <!-- Empty stars -->
    <svg
      v-for="i in stars.empty"
      :key="`empty-${i}`"
      xmlns="http://www.w3.org/2000/svg"
      :class="[sizeClass, 'star-icon text-gray-300']"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
      />
    </svg>
  </div>
</template>

<style scoped>
.star-animated .star-icon {
  transition: all 0.3s;
  transform-origin: center;
}

.star-animated:hover .star-icon {
  filter: drop-shadow(0 0 3px rgba(251, 191, 36, 0.5));
}

.star-animated .star-icon:nth-child(1) {
  animation-delay: 0ms;
}
.star-animated .star-icon:nth-child(2) {
  animation-delay: 50ms;
}
.star-animated .star-icon:nth-child(3) {
  animation-delay: 100ms;
}
.star-animated .star-icon:nth-child(4) {
  animation-delay: 150ms;
}
.star-animated .star-icon:nth-child(5) {
  animation-delay: 200ms;
}

@keyframes pulse-star {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}

.star-animated:hover .star-icon {
  animation: pulse-star 1s infinite;
}
</style>
