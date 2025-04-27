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
    class="fixed top-[65px] left-0 bottom-0 bg-white border-r border-gray-100 shadow-xl text-sm flex flex-col overflow-hidden transition-all duration-300 z-50"
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
      class="absolute top-2 right-2 text-gray-400 hover:text-indigo-600 p-1 z-20"
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
    <div class="px-6 py-4 border-b border-gray-100 bg-white/80 backdrop-blur-md">
      <h2 class="text-lg font-bold truncate mb-2">
        {{ currentCourse?.title || "Course Content" }}
      </h2>
      <div class="flex items-center gap-3 text-sm text-gray-600">
        <span class="font-medium">{{ getCourseProgress() }}% complete</span>
        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
          <div
            class="h-full bg-green-500 rounded-full transition-all duration-300"
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
    <div v-else class="flex-grow overflow-y-auto py-4 px-2">
      <div
        v-for="section in currentCourse?.sections"
        :key="section.id"
        class="mb-6"
      >
        <!-- Section header -->
        <button
          class="w-full px-3 py-2 flex items-center justify-between text-left font-semibold text-gray-800 bg-gray-50 rounded-lg hover:bg-indigo-50 transition-colors mb-2 shadow-sm"
          @click="toggleSection(section.id)"
        >
          <span>{{ section.title }}</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 transition-transform"
            :class="{
              'rotate-180': expandedSections.has(section.id),
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
            class="px-4 py-3 my-1 rounded-xl cursor-pointer flex items-center gap-3 group transition-all border-2"
            :class="{
              'border-indigo-500 bg-indigo-50 shadow-md': lesson.id === currentLessonId,
              'border-transparent hover:border-indigo-200 hover:bg-gray-50': lesson.id !== currentLessonId,
              'opacity-60': lesson.isCompleted && lesson.id !== currentLessonId,
            }"
            @click="navigateToLesson(lesson)"
          >
            <!-- Lesson completion indicator -->
            <div class="flex-shrink-0">
              <svg
                v-if="lesson.isCompleted"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <circle cx="10" cy="10" r="8" fill="#22c55e" />
                <path
                  d="M7.5 10.5l2 2 3-3"
                  stroke="#fff"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  fill="none"
                />
              </svg>
              <svg
                v-else-if="lesson.id === currentLessonId"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <circle cx="10" cy="10" r="8" fill="#6366f1" />
                <polygon points="8,7 14,10 8,13" fill="#fff" />
              </svg>
              <div
                v-else
                class="h-5 w-5 border-2 border-gray-300 rounded-full"
              ></div>
            </div>

            <!-- Lesson info -->
            <div class="flex-grow min-w-0">
              <div class="text-sm font-medium truncate text-gray-900 group-hover:text-indigo-700">
                {{ lesson.title }}
              </div>
              <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                <span class="capitalize">{{ lesson.type }}</span>
                <span>•</span>
                <span>{{ lesson.duration }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="border-b border-gray-100 mt-4"></div>
      </div>
    </div>
  </aside>
</template>

<style scoped>
/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>

