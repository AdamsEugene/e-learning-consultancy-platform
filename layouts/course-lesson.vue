<!-- layouts/course-lesson.vue -->
<script setup lang="ts">
import { ref, onMounted, computed, provide, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import type { CourseLesson } from "~/composables/useSharedLesson";

// Route params and router
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));
const lessonId = computed(() => Number(route.params.lessonId));

// Sidebar state
const isSidebarOpen = ref(true);
const isSmallScreen = ref(false);
const sidebarWidth = ref(320); // Default sidebar width

// Toggle sidebar with optional force parameter
const toggleSidebar = (forcedState?: boolean) => {
  if (typeof forcedState === "boolean") {
    isSidebarOpen.value = forcedState;
  } else {
    isSidebarOpen.value = !isSidebarOpen.value;
  }
};

// Check screen size for responsive design
const checkScreenSize = () => {
  isSmallScreen.value = window.innerWidth < 768;

  // Adjust sidebar visibility based on screen size
  isSidebarOpen.value = !isSmallScreen.value;
};

// Handle sidebar resize
const handleSidebarResize = (dimensions: { width: number; height: number }) => {
  sidebarWidth.value = dimensions.width;
};

// Handle lesson navigation
const handleLessonNavigation = (lesson: CourseLesson) => {
  // Navigate to the new lesson
  router.push(`/courses/${courseId.value}/learn/${lesson.id}`);

  // On mobile, close sidebar after navigation
  if (isSmallScreen.value) {
    toggleSidebar(false);
  }
};

// Lifecycle hooks
onMounted(() => {
  checkScreenSize();
  window.addEventListener("resize", checkScreenSize);
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", checkScreenSize);
});

// Provide sidebar state to child components
provide("sidebarState", {
  isSidebarOpen,
  toggleSidebar,
  isSmallScreen,
  sidebarWidth,
  handleSidebarResize,
});
</script>

<template>
  <!-- Use the default layout as base -->
  <NuxtLayout name="default">
    <div class="bg-white text-gray-900 min-h-screen flex flex-col">
      <!-- Fixed mobile sidebar toggle button -->
      <UiButton
        v-if="isSmallScreen && !isSidebarOpen"
        variant="primary"
        color="primary"
        size="lg"
        class="fixed bottom-4 left-4 z-30 rounded-full shadow-lg"
        aria-label="Open course menu"
        @click="toggleSidebar()"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h7"
          />
        </svg>
      </UiButton>

      <!-- Main content with sidebar -->
      <div class="flex-grow flex overflow-hidden">
        <!-- Course sidebar component -->
        <CoursesLearnCourseSidebar
          v-show="isSidebarOpen"
          :course-id="courseId"
          :current-lesson-id="lessonId"
          :is-small-screen="isSmallScreen"
          :default-width="sidebarWidth"
          @close="toggleSidebar(false)"
          @resize="handleSidebarResize"
          @navigate="handleLessonNavigation"
        />

        <!-- Overlay for mobile sidebar -->
        <div
          v-if="isSmallScreen && isSidebarOpen"
          class="fixed inset-0 bg-black bg-opacity-50 z-5"
          @click="toggleSidebar(false)"
        />

        <!-- Main content area -->
        <div
          class="flex-grow flex flex-col overflow-hidden relative bg-gray-50 transition-all duration-300"
          :style="{
            marginLeft:
              isSidebarOpen && !isSmallScreen ? `${sidebarWidth}px` : '0px',
            width:
              isSidebarOpen && !isSmallScreen
                ? `calc(100% - ${sidebarWidth}px)`
                : '100%',
          }"
        >
          <slot />
        </div>
      </div>
    </div>
  </NuxtLayout>
</template>
