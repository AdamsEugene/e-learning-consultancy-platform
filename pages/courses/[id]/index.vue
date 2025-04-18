<!-- pages/courses/[id].vue -->
<script setup lang="ts">
import { onMounted, inject } from "vue";
import { useRoute } from "vue-router";
import { useCourseDetails } from "~/composables/useCourseDetails";

interface LoadingState {
  isLoading: boolean;
  loadingText: string;
  loadingOverlay: boolean;
  showLoading: (text?: string, overlay?: boolean) => void;
  hideLoading: () => void;
}

// Get course ID from route
const route = useRoute();
const courseId = Number(route.params.id);

// Get loading state
const loading = inject<LoadingState>("loading");

// Use the course details composable
const {
  course,
  loading: courseLoading,
  error,
  activeTab,
  fetchCourseData,
  setActiveTab,
  enrollInCourse,
  discountPercentage,
  formattedPrice,
  formattedOriginalPrice,
  formattedRating,
  formattedReviewCount,
  formattedEnrollmentCount,
  formattedLastUpdated,
} = useCourseDetails(courseId);

// Load data when component mounts
onMounted(async () => {
  loading?.showLoading("Loading course details...", true);
  await fetchCourseData();
  loading?.hideLoading();
});
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Loading state -->
    <CommonLoadingScreen
      v-if="courseLoading"
      text="Loading course details..."
      size="lg"
      full-screen
    />

    <!-- Error state -->
    <div v-else-if="error" class="container mx-auto py-16 px-4">
      <div class="flex flex-col items-center justify-center py-12 text-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-red-500 mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">
          Unable to load course
        </h3>
        <p class="text-gray-600 mb-4">
          There was an error loading this course. Please try again later.
        </p>
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          @click="fetchCourseData"
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- Course content -->
    <div v-else-if="course" class="pb-16">
      <!-- Course header banner -->
      <CoursesDetailsCourseHeader :course="course" />

      <!-- Main course content -->
      <div class="container mx-auto px-4 md:px-6 -mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Course details - left column -->
          <div class="lg:col-span-2">
            <!-- Course content with tabs -->
            <CoursesDetailsCourseContent
              :course="course"
              :active-tab="activeTab"
              @set-tab="setActiveTab"
            />
          </div>

          <!-- Course sidebar - right column -->
          <div class="lg:col-span-1">
            <CoursesDetailsCourseSidebar
              :course="course"
              :discount-percentage="discountPercentage"
              :formatted-price="formattedPrice"
              :formatted-original-price="formattedOriginalPrice"
              :formatted-rating="formattedRating"
              :formatted-review-count="formattedReviewCount"
              :formatted-enrollment-count="formattedEnrollmentCount"
              :formatted-last-updated="formattedLastUpdated"
              @enroll="enrollInCourse"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.hide-scrollbar {
  scrollbar-width: none; /* Firefox */
}

.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}
</style>
