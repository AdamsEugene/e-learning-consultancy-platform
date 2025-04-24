<!-- components/common/StarRating.vue -->
<script setup lang="ts">
interface Props {
  rating: number;
  size?: "sm" | "md" | "lg";
  showValue?: boolean;
  reviewCount?: number;
}

const props = withDefaults(defineProps<Props>(), {
  size: "md",
  showValue: false,
  reviewCount: 0,
});

// Calculate star values
const wholeStars = Math.floor(props.rating);
const hasPartialStar = props.rating % 1 >= 0.5;
const emptyStars = 5 - wholeStars - (hasPartialStar ? 1 : 0);

// Size classes
const sizeClasses = {
  sm: "h-4 w-4",
  md: "h-5 w-5",
  lg: "h-6 w-6",
};

const starSize = sizeClasses[props.size];
</script>

<template>
  <div class="flex items-center">
    <div class="flex text-yellow-400">
      <svg
        v-for="i in wholeStars"
        :key="`whole-${i}`"
        xmlns="http://www.w3.org/2000/svg"
        :class="starSize"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
        />
      </svg>
      <svg
        v-if="hasPartialStar"
        xmlns="http://www.w3.org/2000/svg"
        :class="starSize"
        viewBox="0 0 20 20"
      >
        <defs>
          <linearGradient id="partialFill">
            <stop offset="50%" stop-color="currentColor" />
            <stop offset="50%" stop-color="rgba(255,255,255,0.3)" />
          </linearGradient>
        </defs>
        <path
          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
          fill="url(#partialFill)"
        />
      </svg>
      <svg
        v-for="i in emptyStars"
        :key="`empty-${i}`"
        xmlns="http://www.w3.org/2000/svg"
        :class="`${starSize} text-gray-300`"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
        />
      </svg>
    </div>

    <span v-if="showValue" class="ml-1 font-medium text-white">
      {{ rating.toFixed(1) }}
      <span v-if="reviewCount" class="text-white font-normal">
        ({{ reviewCount.toLocaleString() }})
      </span>
    </span>
  </div>
</template>
