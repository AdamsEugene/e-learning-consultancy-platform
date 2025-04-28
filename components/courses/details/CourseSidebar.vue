<!-- components/courses/CourseSidebar.vue -->
<script setup lang="ts">
import type { ExtendedCourse } from "~/composables/useCourseDetails";

interface Props {
  course: ExtendedCourse;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: "enroll"): void;
}>();

// Wishlist state
const isWishlistOpen = ref(false);
const wishlist = ref<ExtendedCourse[]>([]);

// Calculate discount percentage
const discountPercentage = computed(() => {
  if (!props.course.originalPrice) return null;

  const discount = Math.round(
    ((props.course.originalPrice - props.course.price) /
      props.course.originalPrice) *
      100
  );
  return discount;
});

// Format prices
const formattedPrice = computed(() => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(props.course.price);
});

const formattedOriginalPrice = computed(() => {
  if (!props.course.originalPrice) return "";

  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(props.course.originalPrice);
});

// Handle enrollment action
const enrollInCourse = () => {
  emit("enroll");
};

// Handle wishlist actions
const toggleWishlist = () => {
  const courseInWishlist = wishlist.value.find((c) => c.id === props.course.id);
  if (!courseInWishlist) {
    wishlist.value.push(props.course);
  }
  isWishlistOpen.value = true;
};

const removeFromWishlist = (courseId: number) => {
  wishlist.value = wishlist.value.filter((course) => course.id !== courseId);
  if (wishlist.value.length === 0) {
    isWishlistOpen.value = false;
  }
};

// Check if course is in wishlist
const isInWishlist = computed(() => {
  return wishlist.value.some((course) => course.id === props.course.id);
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="sticky top-24">
    <!-- Course preview card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
      <!-- Preview image -->
      <img
        :src="course.image"
        :alt="course.title"
        class="w-full h-48 object-cover"
      />

      <!-- Card content -->
      <div class="p-6">
        <!-- Price -->
        <div class="mb-4">
          <div class="flex items-center">
            <span class="text-3xl font-bold text-gray-900 mr-2">{{
              formattedPrice
            }}</span>
            <span
              v-if="formattedOriginalPrice"
              class="text-xl text-gray-500 line-through"
            >
              {{ formattedOriginalPrice }}
            </span>
            <span
              v-if="discountPercentage"
              class="ml-2 bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded"
            >
              {{ discountPercentage }}% OFF
            </span>
          </div>
        </div>

        <!-- Action buttons -->
        <div class="mb-6 space-y-3">
          <button
            v-if="!course.isEnrolled"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-bold transition-colors flex items-center justify-center"
            @click="enrollInCourse"
          >
            Enroll Now
          </button>
          <NuxtLink
            v-else
            :to="`/courses/${course.id}/learn`"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-bold transition-colors flex items-center justify-center"
          >
            Continue Learning
          </NuxtLink>

          <button
            class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center"
            @click="toggleWishlist"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              :class="{ 'text-red-500 fill-current': isInWishlist }"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
              />
            </svg>
            {{ isInWishlist ? "In Wishlist" : "Add to Wishlist" }}
          </button>
        </div>

        <!-- Course includes -->
        <div class="mb-6">
          <h4 class="font-bold text-gray-900 mb-3">This course includes:</h4>
          <ul class="space-y-2">
            <li
              v-for="(feature, index) in course.features"
              :key="index"
              class="flex items-start"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
              <span class="text-gray-700 text-sm">{{ feature }}</span>
            </li>
          </ul>
        </div>

        <!-- Share course -->
        <div>
          <h4 class="font-bold text-gray-900 mb-3">Share this course</h4>
          <div class="flex space-x-3">
            <button
              class="text-gray-600 hover:text-indigo-600 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                />
              </svg>
            </button>
            <button
              class="text-gray-600 hover:text-indigo-600 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"
                />
              </svg>
            </button>
            <button
              class="text-gray-600 hover:text-indigo-600 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"
                />
              </svg>
            </button>
            <button
              class="text-gray-600 hover:text-indigo-600 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  fill-rule="evenodd"
                  d="M22.5 13.5a7.5 7.5 0 01-3.037 6.02l.525 3.065a.5.5 0 01-.722.487l-3.082-1.57a7.5 7.5 0 01-8.193-12.493A7.5 7.5 0 0122.5 13.5zM10.545 9.454a.5.5 0 10-.766.642L11.893 13l-2.114 2.904a.5.5 0 00.766.642l2.25-3a.5.5 0 000-.642l-2.25-3.45zm4.5 0a.5.5 0 00-.766.642L16.393 13l-2.114 2.904a.5.5 0 00.766.642l2.25-3a.5.5 0 000-.642l-2.25-3.45z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Wishlist Panel -->
    <WishlistPanel
      :is-open="isWishlistOpen"
      :wishlist="wishlist"
      @update:is-open="isWishlistOpen = $event"
      @remove="removeFromWishlist"
    />
  </div>
</template>
