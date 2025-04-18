<!-- pages/courses/[id]/learn/index.vue -->
<script setup lang="ts">
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import { useCourse } from "~/composables/useCourse";

const route = useRoute();
const courseId = Number(route.params.id);

const {
  course,
  loading,
  error,
  activeTab,
  faqs,
  // expandedSections,
  selectedSection,
  selectedSectionData,
  totalLessons,
  formatTotalDuration,
  formattedUpdateDate,
  fetchCourseData,
  selectSection,
  startCourse,
  continueCourse,
  startLesson,
  formatLessonType,
} = useCourse(courseId);



onMounted(() => {
  fetchCourseData();
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-white min-h-screen">
    <!-- Loading state -->
    <div
      v-if="loading"
      class="flex-grow flex items-center justify-center py-20"
    >
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      />
    </div>

    <!-- Error state -->
    <div
      v-else-if="error"
      class="flex-grow flex items-center justify-center py-20"
    >
      <div class="text-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-red-500 mx-auto mb-4"
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
        <h3 class="text-xl font-bold mb-2">Failed to load course</h3>
        <p class="text-gray-600 mb-4">
          There was an error loading this course. Please try again.
        </p>
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          @click="fetchCourseData"
        >
          Retry
        </button>
      </div>
    </div>

    <!-- Course content -->
    <div v-else class="container mx-auto p-4 md:p-6">
      <!-- Course header -->
      <CourseHeader
        :course="course"
        :total-lessons="totalLessons"
        :format-total-duration="formatTotalDuration"
        :formatted-update-date="formattedUpdateDate"
        :continue-course="continueCourse"
        :start-course="startCourse"
      />
      <!-- Course content tabs -->
      <CourseTab
        :active-tab="activeTab"
        @update:active-tab="(tab) => (activeTab = tab)"
      />

      <!-- Tab content -->
      <div class="mb-8">
        <!-- Overview tab -->
        <CourseOverviewTab
          :course="course"
          :total-lessons="totalLessons"
          :format-total-duration="formatTotalDuration"
          :formatted-update-date="formattedUpdateDate"
          :continue-course="continueCourse"
          :start-course="startCourse"
          :active-tab="activeTab"
          :select-section="selectSection"
          :selected-section="selectedSection"
          :selected-section-data="selectedSectionData"
          :start-lesson="startLesson"
          :format-lesson-type="formatLessonType"
        />

        <!-- Curriculum tab -->
        <CourseCurriculumTab
          :course="course"
          :total-lessons="totalLessons"
          :format-total-duration="formatTotalDuration"
          :active-tab="activeTab"
          :select-section="selectSection"
          :selected-section="selectedSection"
          :selected-section-data="selectedSectionData"
          :start-lesson="startLesson"
          :format-lesson-type="formatLessonType"
        />

        <!-- Instructor tab -->
        <CourseInstructorTab :course="course" :active-tab="activeTab" />

        <!-- Reviews tab -->
        <CourseReviewsTab :course="course" :active-tab="activeTab" />
      </div>

      <!-- Related courses -->
      <CourseRelatedCourses :course="course" :active-tab="activeTab" />

      <!-- FAQ section -->
      <CommonSharedFAQSection :faqs="faqs" title="Frequently Asked Questions" />

      <!-- CTA section -->
      <CourseCTASection :start-course="startCourse" />
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9; /* gray-100 */
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1; /* gray-300 */
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1; /* indigo-500 */
}

/* Course roadmap line connector styles */
.roadmap-connector {
  position: absolute;
  left: 5px;
  top: 0;
  bottom: 0;
  width: 1px;
  background-color: #e5e7eb;
  z-index: 0;
}

/* Animation for content tabs */
.tab-content-enter-active,
.tab-content-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.tab-content-enter-from,
.tab-content-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
