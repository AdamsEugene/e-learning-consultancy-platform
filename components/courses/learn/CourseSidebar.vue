<!-- components/courses/CourseSidebar.vue -->
<script setup lang="ts">
import type { Course, CourseLesson, CourseSection } from "~/types/courseTemp";

interface Props {
  course?: Course | null;
  currentLesson?: CourseLesson | null;
  courseProgress?: number;
  isSmallScreen?: boolean;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  course: null,
  currentLesson: null,
  courseProgress: 0,
  isSmallScreen: false,
});

// Emits
const emit = defineEmits(["navigate", "close"]);

// Expanded sections tracking
const expandedSections = ref(new Set());

// Initialize with current section expanded
if (props.currentLesson && props.course) {
  for (const section of props.course.sections) {
    if (
      section.lessons.some((lesson) => lesson.id === props.currentLesson?.id)
    ) {
      expandedSections.value.add(section.id);
      break;
    }
  }
}

// Toggle section expansion
const toggleSection = (sectionId: number) => {
  if (expandedSections.value.has(sectionId)) {
    expandedSections.value.delete(sectionId);
  } else {
    expandedSections.value.add(sectionId);
  }
};

// Navigate to lesson
const navigateToLesson = (lesson: CourseLesson) => {
  emit("navigate", lesson);
};

// Close sidebar (mobile only)
const closeSidebar = () => {
  emit("close");
};

// Calculate section progress
const getSectionProgress = (section: CourseSection) => {
  const totalLessons = section.lessons.length;
  if (totalLessons === 0) return 0;

  const completedLessons = section.lessons.filter(
    (lesson) => lesson.isCompleted
  ).length;
  return Math.round((completedLessons / totalLessons) * 100);
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    class="bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0 transition-all duration-300 h-full"
    :class="
      isSmallScreen ? 'fixed inset-0 z-30 w-4/5 max-w-sm' : 'w-80 sticky top-0'
    "
  >
    <!-- Sidebar header with close button for mobile -->
    <div
      class="p-4 border-b border-gray-200 sticky top-0 bg-white z-10 flex justify-between items-center"
    >
      <h2 class="text-lg font-bold">Course Content</h2>
      <button
        v-if="isSmallScreen"
        class="text-gray-500 hover:text-gray-700 p-1 rounded hover:bg-gray-100"
        aria-label="Close menu"
        @click="closeSidebar"
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
    </div>

    <!-- Course progress bar -->
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
      <div class="flex justify-between text-sm text-gray-600 mb-1">
        <span>Course Progress</span>
        <span>{{ courseProgress }}%</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
        <div
          class="h-full bg-green-500 transition-all duration-500"
          :style="{ width: `${courseProgress}%` }"
        />
      </div>
    </div>

    <!-- Course instructor (if available) -->
    <div
      v-if="course?.instructor"
      class="p-4 border-b border-gray-200 flex items-center"
    >
      <img
        :src="course.instructor.avatar"
        :alt="course.instructor.name"
        class="w-10 h-10 rounded-full mr-3 object-cover"
      />
      <div>
        <div class="font-medium">{{ course.instructor.name }}</div>
        <div class="text-xs text-gray-500">{{ course.instructor.title }}</div>
      </div>
    </div>

    <!-- Sections and lessons -->
    <div class="py-2">
      <div v-for="section in course?.sections" :key="section.id" class="mb-2">
        <!-- Section header -->
        <div
          class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between"
          @click="toggleSection(section.id)"
        >
          <div class="flex-1">
            <div class="font-medium flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-2 transition-transform duration-200"
                :class="
                  expandedSections.has(section.id) ? 'transform rotate-90' : ''
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              {{ section.title }}
            </div>
            <div class="flex items-center mt-1 text-xs text-gray-500">
              <span>{{ section.lessons.length }} lessons</span>
              <span class="mx-2">•</span>
              <span>{{ getSectionProgress(section) }}% complete</span>
            </div>
          </div>
        </div>

        <!-- Lessons list (collapsible) -->
        <div
          v-if="expandedSections.has(section.id)"
          class="space-y-1 pl-4 pr-2 mt-1 mb-3 overflow-hidden transition-all duration-300"
        >
          <button
            v-for="lesson in section.lessons"
            :key="lesson.id"
            class="w-full px-3 py-2 rounded-lg flex items-center text-left transition-colors relative group"
            :class="[
              lesson.id === currentLesson?.id
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100',
            ]"
            @click="navigateToLesson(lesson)"
          >
            <!-- Lesson completion status -->
            <div class="mr-3 flex-shrink-0">
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
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                :class="
                  lesson.id === currentLesson?.id
                    ? 'text-white'
                    : 'text-gray-400'
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>

            <!-- Lesson info -->
            <div class="flex-grow min-w-0">
              <div
                class="font-medium truncate"
                :class="{ 'text-white': lesson.id === currentLesson?.id }"
              >
                {{ lesson.title }}
              </div>
              <div
                class="flex items-center text-xs"
                :class="
                  lesson.id === currentLesson?.id
                    ? 'text-indigo-200'
                    : 'text-gray-500'
                "
              >
                <!-- Lesson type icon -->
                <svg
                  v-if="lesson.type === 'video'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3 w-3 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                  />
                </svg>
                <svg
                  v-else-if="lesson.type === 'quiz'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3 w-3 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                  <path
                    fill-rule="evenodd"
                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else-if="lesson.type === 'assignment'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3 w-3 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                  />
                </svg>

                <!-- Lesson type and duration -->
                <span>
                  {{
                    lesson.type.charAt(0).toUpperCase() + lesson.type.slice(1)
                  }}
                  • {{ lesson.duration }}
                </span>
              </div>
            </div>

            <!-- Active lesson indicator -->
            <div
              v-if="lesson.id === currentLesson?.id"
              class="absolute -left-4 top-1/2 transform -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r"
            />
          </button>
        </div>
      </div>
    </div>
  </div>
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
