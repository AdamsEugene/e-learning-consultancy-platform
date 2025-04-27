<!-- components/courses/learn/CourseSidebar.vue -->
<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import {
  useSharedLesson,
  type CourseLesson,
} from "~/composables/useSharedLesson";

const props = defineProps({
  courseId: {
    type: Number,
    required: true,
  },
  currentLessonId: {
    type: Number,
    required: true,
  },
  isSmallScreen: {
    type: Boolean,
    default: false,
  },
  defaultWidth: {
    type: Number,
    default: 320,
  },
});

const emit = defineEmits(["close", "resize", "navigate"]);

// Get shared lesson state
const {
  currentCourse,
  isLoading,
  hasError,
  setCurrentLesson,
  getCourseProgress,
  getCourseById,
  findLessonById,
  fetchCourseData,
} = useSharedLesson();

// Local state
const expandedSections = ref<Set<number>>(new Set());
const isDragging = ref(false);
const startWidth = ref(props.defaultWidth);
const currentWidth = ref(props.defaultWidth);
const previousCourseId = ref<number | null>(null);

// Handling course data loading
const loadCourseData = async () => {
  // Check if we already have the course data in cache or loaded
  if (currentCourse.value?.id === props.courseId && !hasError.value) {
    findCurrentLessonSection();
    const currentLesson = findLessonById(props.currentLessonId);
    if (currentLesson) {
      setCurrentLesson(currentLesson);
    }
    return;
  }

  // Check if the course exists in our cached data
  const cachedCourse = getCourseById(props.courseId);
  if (cachedCourse) {
    const lessonToSelect = findLessonById(props.currentLessonId);
    if (lessonToSelect) {
      setCurrentLesson(lessonToSelect);
    }
    findCurrentLessonSection();
    return;
  }

  // Fetch course data from the composable
  const courseData = await fetchCourseData(props.courseId);

  if (courseData) {
    previousCourseId.value = props.courseId;
    findCurrentLessonSection();

    // Set current lesson
    const currentLesson = findLessonById(props.currentLessonId);
    if (currentLesson) {
      setCurrentLesson(currentLesson);
    }
  }
};

// Find section containing current lesson and expand it
const findCurrentLessonSection = () => {
  if (!currentCourse.value) return;

  for (const section of currentCourse.value.sections) {
    for (const lesson of section.lessons) {
      if (lesson.id === props.currentLessonId) {
        expandedSections.value.add(section.id);
        return;
      }
    }
  }
};

// Toggle section expansion
const toggleSection = (sectionId: number) => {
  if (expandedSections.value.has(sectionId)) {
    expandedSections.value.delete(sectionId);
  } else {
    expandedSections.value.add(sectionId);
  }
};

// Navigate to a lesson
const navigateToLesson = (lesson: CourseLesson) => {
  // Set the lesson in shared state
  setCurrentLesson(lesson);
  // Emit navigate event for the router
  emit("navigate", lesson);
};

// Resize handling
const handleMouseDown = (_e: MouseEvent) => {
  isDragging.value = true;
  startWidth.value = currentWidth.value;
  document.addEventListener("mousemove", handleMouseMove);
  document.addEventListener("mouseup", handleMouseUp);
  document.body.style.userSelect = "none"; // Prevent text selection during resize
};

const handleMouseMove = (e: MouseEvent) => {
  if (!isDragging.value) return;

  // Calculate new width based on mouse position
  const newWidth = Math.max(240, Math.min(480, e.clientX)); // Min 240px, max 480px
  currentWidth.value = newWidth;

  // Emit resize event
  emit("resize", { width: newWidth, height: window.innerHeight });
};

const handleMouseUp = () => {
  isDragging.value = false;
  document.removeEventListener("mousemove", handleMouseMove);
  document.removeEventListener("mouseup", handleMouseUp);
  document.body.style.userSelect = ""; // Re-enable text selection
};

// Watch for courseId changes
watch(
  () => props.courseId,
  (newCourseId, oldCourseId) => {
    if (newCourseId !== oldCourseId) {
      loadCourseData();
    }
  }
);

// Watch for lessonId changes
watch(
  () => props.currentLessonId,
  (newLessonId) => {
    // If we already have course data, just update the current lesson and expanded sections
    if (currentCourse.value) {
      findCurrentLessonSection();
      const currentLesson = findLessonById(newLessonId);
      if (currentLesson) {
        setCurrentLesson(currentLesson);
      }
    }
  }
);

// Lifecycle
onMounted(() => {
  loadCourseData();
});

// Clean up event listeners
onBeforeUnmount(() => {
  if (isDragging.value) {
    document.removeEventListener("mousemove", handleMouseMove);
    document.removeEventListener("mouseup", handleMouseUp);
    document.body.style.userSelect = "";
  }
});
</script>

<template>
  <aside
    class="fixed top-[65px] left-0 bottom-0 bg-white border-r border-gray-200 flex flex-col overflow-hidden transition-all duration-300 z-50"
    :style="{ width: `${currentWidth}px` }"
  >
    <!-- Resize handle -->
    <div
      class="absolute top-0 right-0 w-2 h-full cursor-ew-resize bg-transparent hover:bg-indigo-100 z-10"
      @mousedown="handleMouseDown"
    ></div>

    <!-- Close button for mobile view -->
    <button
      v-if="isSmallScreen"
      class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 p-1 z-20"
      aria-label="Close sidebar"
      @click="$emit('close')"
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
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>

    <!-- Sidebar header -->
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold truncate">
        {{ currentCourse?.title || "Course Content" }}
      </h2>
      <div class="flex items-center mt-2 text-sm text-gray-600">
        <span class="mr-2">{{ getCourseProgress() }}% complete</span>
        <div class="flex-grow bg-gray-200 rounded-full h-2 overflow-hidden">
          <div
            class="h-full bg-green-500 transition-all duration-300"
            :style="{ width: `${getCourseProgress()}%` }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div
      v-if="isLoading"
      class="flex-grow flex items-center justify-center p-4"
    >
      <div
        class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-600"
      ></div>
    </div>

    <!-- Error state -->
    <div v-else-if="hasError" class="p-4 text-center text-red-500">
      Failed to load course content.
      <button
        class="block mx-auto mt-2 text-indigo-600 hover:underline"
        @click="loadCourseData"
      >
        Retry
      </button>
    </div>

    <!-- Course content -->
    <div v-else class="flex-grow overflow-y-auto py-2">
      <div
        v-for="section in currentCourse?.sections"
        :key="section.id"
        class="mb-2"
      >
        <!-- Section header -->
        <button
          class="w-full px-4 py-2 flex items-center justify-between text-left hover:bg-gray-50 transition-colors"
          @click="toggleSection(section.id)"
        >
          <span class="font-medium">{{ section.title }}</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 transition-transform"
            :class="{
              'transform rotate-180': expandedSections.has(section.id),
            }"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7"
            />
          </svg>
        </button>

        <!-- Section lessons -->
        <div v-if="expandedSections.has(section.id)" class="overflow-hidden">
          <div
            v-for="lesson in section.lessons"
            :key="lesson.id"
            class="px-4 py-2 border-l-4 transition-colors ml-4 cursor-pointer flex items-center"
            :class="{
              'border-indigo-600 bg-indigo-50': lesson.id === currentLessonId,
              'border-transparent hover:border-gray-300 hover:bg-gray-50':
                lesson.id !== currentLessonId,
              'text-gray-400':
                lesson.isCompleted && lesson.id !== currentLessonId,
            }"
            @click="navigateToLesson(lesson)"
          >
            <!-- Lesson completion indicator -->
            <div class="mr-2 flex-shrink-0">
              <svg
                v-if="lesson.isCompleted"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else-if="lesson.id === currentLessonId"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                  clip-rule="evenodd"
                />
              </svg>
              <div
                v-else
                class="h-5 w-5 border-2 border-gray-300 rounded-full"
              ></div>
            </div>

            <!-- Lesson info -->
            <div class="flex-grow">
              <div class="text-sm font-medium truncate">{{ lesson.title }}</div>
              <div class="text-xs text-gray-500 flex items-center">
                <span class="capitalize">{{ lesson.type }}</span>
                <span class="mx-1">•</span>
                <span>{{ lesson.duration }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<style scoped>
/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 5px;
  height: 5px;
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
</style>
