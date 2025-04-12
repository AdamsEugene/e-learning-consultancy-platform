<!-- components/courses/CourseCard.vue -->
<script setup lang="ts">
import { computed } from "vue";

interface CourseCardProps {
  id: number;
  title: string;
  category: string;
  image: string;
  instructor: string;
  rating: number;
  reviewCount: number;
  price: number;
  originalPrice?: number;
  level: string;
  duration: string;
  badges?: string[];
  isEnrolled?: boolean;
  progress?: number;
}

const props = defineProps<CourseCardProps>();

// Compute discount percentage if there's an original price
const discountPercentage = computed(() => {
  if (!props.originalPrice || props.originalPrice <= props.price) return null;
  const discount = Math.round(
    ((props.originalPrice - props.price) / props.originalPrice) * 100
  );
  return discount;
});

// Calculate whole stars, partial stars, and empty stars
const starRating = computed(() => {
  const wholeStars = Math.floor(props.rating);
  const hasPartialStar = props.rating % 1 >= 0.5;
  const emptyStars = 5 - wholeStars - (hasPartialStar ? 1 : 0);

  return {
    wholeStars,
    hasPartialStar,
    emptyStars,
  };
});

// Format price as currency
const formattedPrice = computed(() => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(props.price);
});

const formattedOriginalPrice = computed(() => {
  if (!props.originalPrice) return null;
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(props.originalPrice);
});

// Level badge color
const levelBadgeColor = computed(() => {
  switch (props.level.toLowerCase()) {
    case "beginner":
      return "bg-green-100 text-green-800";
    case "intermediate":
      return "bg-blue-100 text-blue-800";
    case "advanced":
      return "bg-purple-100 text-purple-800";
    case "all levels":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
});

// Badge colors
const getBadgeColor = (badge: string) => {
  switch (badge.toLowerCase()) {
    case "bestseller":
      return "bg-yellow-100 text-yellow-800";
    case "hot":
    case "hot & new":
      return "bg-red-100 text-red-800";
    case "new":
      return "bg-green-100 text-green-800";
    case "top rated":
      return "bg-purple-100 text-purple-800";
    case "popular":
      return "bg-blue-100 text-blue-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};
</script>

<template>
  <div
    class="group course-card bg-white rounded-xl overflow-hidden shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1"
  >
    <!-- Card image with badge and overlay -->
    <div class="relative overflow-hidden">
      <!-- Course thumbnail -->
      <img
        :src="image"
        :alt="title"
        class="w-full h-48 object-cover transform transition-transform duration-300 group-hover:scale-105"
      />

      <!-- Badges -->
      <div
        class="absolute top-0 left-0 right-0 p-3 flex justify-between items-start"
      >
        <div class="flex flex-wrap gap-2">
          <span
            v-for="(badge, index) in badges"
            :key="index"
            class="inline-block text-xs font-bold py-1 px-2 rounded text-center"
            :class="getBadgeColor(badge)"
          >
            {{ badge }}
          </span>
        </div>

        <!-- Category badge in top right -->
        <span
          class="inline-block bg-indigo-600 text-white text-xs font-bold py-1 px-2 rounded"
        >
          {{ category }}
        </span>
      </div>

      <!-- Gradient overlay -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      >
        <div class="absolute bottom-0 left-0 p-4 text-white">
          <div class="text-sm font-bold mb-1">{{ instructor }}</div>
          <div class="text-xs opacity-80">{{ duration }}</div>
        </div>
      </div>

      <!-- Progress indicator for enrolled courses -->
      <div
        v-if="isEnrolled && typeof progress !== 'undefined'"
        class="absolute bottom-0 left-0 right-0 h-1 bg-gray-200"
      >
        <div
          class="h-full bg-indigo-600 transition-all duration-300"
          :style="{ width: `${progress}%` }"
        />
      </div>
    </div>

    <!-- Card content -->
    <div class="p-5">
      <!-- Course level and duration -->
      <div
        class="flex items-center justify-between mb-2 text-xs font-medium text-gray-500"
      >
        <span :class="levelBadgeColor" class="px-2 py-1 rounded-full">
          {{ level }}
        </span>
        <span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 inline-block mr-1"
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
          {{ duration }}
        </span>
      </div>

      <!-- Course title -->
      <h3
        class="text-lg font-bold mb-3 leading-tight line-clamp-2 group-hover:text-indigo-600 transition-colors duration-300"
      >
        {{ title }}
      </h3>

      <!-- Ratings -->
      <div class="flex items-center mb-4">
        <div class="flex text-yellow-400 mr-1">
          <!-- Whole stars -->
          <svg
            v-for="i in starRating.wholeStars"
            :key="`whole-${i}`"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            />
          </svg>

          <!-- Partial star -->
          <svg
            v-if="starRating.hasPartialStar"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
          >
            <defs>
              <linearGradient id="partialFill">
                <stop offset="50%" stop-color="currentColor" />
                <stop offset="50%" stop-color="#D1D5DB" />
              </linearGradient>
            </defs>
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              fill="url(#partialFill)"
            />
          </svg>

          <!-- Empty stars -->
          <svg
            v-for="i in starRating.emptyStars"
            :key="`empty-${i}`"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 text-gray-300"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            />
          </svg>
        </div>
        <span class="text-gray-600 text-xs">
          {{ rating.toFixed(1) }} ({{ reviewCount.toLocaleString() }} reviews)
        </span>
      </div>

      <!-- Price and CTA -->
      <div class="border-t pt-4 flex justify-between items-center">
        <div>
          <span
            v-if="discountPercentage"
            class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded mr-2"
          >
            {{ discountPercentage }}% OFF
          </span>
          <span class="text-xl font-bold text-indigo-600">
            {{ formattedPrice }}
          </span>
          <span
            v-if="formattedOriginalPrice"
            class="text-sm line-through text-gray-400 ml-2"
          >
            {{ formattedOriginalPrice }}
          </span>
        </div>

        <NuxtLink
          :to="`/courses/${id}`"
          class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition-colors text-sm font-bold"
        >
          {{ isEnrolled ? "Continue" : "View Course" }}
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
