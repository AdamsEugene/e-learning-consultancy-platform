<!-- components/courses/CourseSidebar.vue -->
<script setup lang="ts">
export interface Section {
  id: string;
  title: string;
  lessons: Lesson[];
  isCompleted?: boolean;
}

export interface Lesson {
  id: string;
  title: string;
  duration: number; // in minutes
  type: "video" | "quiz" | "assignment" | "article";
  isCompleted?: boolean;
  isLocked?: boolean;
}

interface CourseSidebarProps {
  courseId: string;
  courseTitle: string;
  sections: Section[];
  currentLessonId?: string;
  currentSectionId?: string;
  progress?: number; // 0-100
  isMobileOpen?: boolean;
}

const props = withDefaults(defineProps<CourseSidebarProps>(), {
  currentLessonId: "",
  currentSectionId: "",
  progress: 0,
  isMobileOpen: false,
});

const emit = defineEmits<{
  (e: "navigate", lessonId: string, sectionId: string): void;
  (e: "toggle-mobile", isOpen: boolean): void;
}>();

// Reactive refs
const expandedSections = ref<Record<string, boolean>>({});

// Initialize all sections as expanded if they contain the current lesson, otherwise collapsed
// This makes the section with the current lesson automatically expanded when the component loads
const initializeExpandedSections = () => {
  props.sections.forEach((section) => {
    const hasCurrentLesson = section.lessons.some(
      (lesson) => lesson.id === props.currentLessonId
    );
    expandedSections.value[section.id] =
      hasCurrentLesson || section.id === props.currentSectionId;
  });
};

// Call the initialization function
initializeExpandedSections();

// Toggle section expansion
const toggleSection = (sectionId: string) => {
  expandedSections.value[sectionId] = !expandedSections.value[sectionId];
};

// Format duration (minutes to "h m" format)
const formatDuration = (minutes: number): string => {
  if (minutes < 60) {
    return `${minutes}m`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}m` : `${hours}h`;
};

// Get icon for lesson type
const getLessonTypeIcon = (type: Lesson["type"]) => {
  switch (type) {
    case "video":
      return "play-circle";
    case "quiz":
      return "check-circle";
    case "assignment":
      return "clipboard-list";
    case "article":
      return "document-text";
    default:
      return "play-circle";
  }
};

// Check if a lesson is the current lesson
const isCurrentLesson = (lessonId: string): boolean => {
  return lessonId === props.currentLessonId;
};

// Navigate to a lesson
const navigateToLesson = (
  lessonId: string,
  sectionId: string,
  isLocked: boolean = false
) => {
  if (isLocked) return; // Prevent navigation to locked lessons
  emit("navigate", lessonId, sectionId);

  // Close mobile sidebar after navigation
  if (props.isMobileOpen) {
    emit("toggle-mobile", false);
  }
};

// Computed property for total course duration
const totalDuration = computed(() => {
  let total = 0;
  props.sections.forEach((section) => {
    section.lessons.forEach((lesson) => {
      total += lesson.duration;
    });
  });
  return formatDuration(total);
});

// Computed property for number of completed lessons
const completedLessonsCount = computed(() => {
  let completed = 0;
  props.sections.forEach((section) => {
    section.lessons.forEach((lesson) => {
      if (lesson.isCompleted) completed++;
    });
  });
  return completed;
});

// Computed property for total lessons
const totalLessonsCount = computed(() => {
  let total = 0;
  props.sections.forEach((section) => {
    total += section.lessons.length;
  });
  return total;
});

// Close mobile sidebar
const closeMobileSidebar = () => {
  emit("toggle-mobile", false);
};
</script>

<template>
  <aside
    :class="[
      'course-sidebar transition-all duration-300 bg-white border-r overflow-hidden flex flex-col',
      isMobileOpen ? 'fixed inset-0 z-40' : 'hidden lg:flex',
    ]"
  >
    <!-- Mobile sidebar header (visible only on mobile) -->
    <div
      v-if="isMobileOpen"
      class="flex items-center justify-between p-4 border-b lg:hidden"
    >
      <h3 class="font-bold text-lg truncate pr-4">
        {{ courseTitle }}
      </h3>
      <button
        class="text-gray-500 hover:text-gray-800 focus:outline-none"
        @click="closeMobileSidebar"
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

    <!-- Course info and progress -->
    <div class="px-4 py-5 border-b">
      <h3 class="font-bold text-lg mb-2 truncate hidden lg:block">
        {{ courseTitle }}
      </h3>

      <!-- Progress bar and stats -->
      <div class="mb-4">
        <div class="flex justify-between text-sm text-gray-600 mb-1">
          <span>Your progress</span>
          <span>{{ progress }}%</span>
        </div>
        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
          <div
            class="h-full bg-indigo-600 transition-all duration-500"
            :style="{ width: `${progress}%` }"
          />
        </div>
      </div>

      <!-- Course stats -->
      <div class="flex items-center justify-between text-sm text-gray-600">
        <div class="flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
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
          <span>{{ totalDuration }}</span>
        </div>
        <div class="flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span
            >{{ completedLessonsCount }}/{{ totalLessonsCount }} lessons</span
          >
        </div>
      </div>
    </div>

    <!-- Course content navigation -->
    <div class="flex-grow overflow-y-auto">
      <nav class="py-2">
        <ul class="space-y-1">
          <li v-for="section in sections" :key="section.id" class="px-4">
            <!-- Section header -->
            <div
              class="flex items-center justify-between py-3 px-2 cursor-pointer hover:bg-gray-100 rounded-md transition-colors"
              @click="toggleSection(section.id)"
            >
              <div class="flex items-start flex-grow pr-2">
                <span
                  v-if="section.isCompleted"
                  class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0 text-green-500"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>
                <span
                  v-else
                  class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0 text-gray-400"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>
                <span class="font-medium">{{ section.title }}</span>
              </div>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400 transform transition-transform"
                :class="expandedSections[section.id] ? 'rotate-90' : ''"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </div>

            <!-- Lessons in section (collapsible) -->
            <transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-200 ease-out"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <ul
                v-show="expandedSections[section.id]"
                class="pl-7 pr-1 space-y-1 my-1"
              >
                <li v-for="lesson in section.lessons" :key="lesson.id">
                  <div
                    :class="[
                      'flex items-center py-2 px-2 rounded-md cursor-pointer transition-colors',
                      isCurrentLesson(lesson.id)
                        ? 'bg-indigo-50 text-indigo-700'
                        : 'hover:bg-gray-100 text-gray-700',
                      lesson.isLocked && 'cursor-not-allowed opacity-70',
                    ]"
                    @click="
                      navigateToLesson(lesson.id, section.id, lesson.isLocked)
                    "
                  >
                    <!-- Lesson completion status -->
                    <span
                      v-if="lesson.isCompleted"
                      class="w-5 h-5 mr-2 text-green-500 flex-shrink-0"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </span>

                    <!-- Lesson type icon -->
                    <span
                      v-else-if="
                        getLessonTypeIcon(lesson.type) === 'play-circle'
                      "
                      class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </span>

                    <span
                      v-else-if="
                        getLessonTypeIcon(lesson.type) === 'check-circle'
                      "
                      class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </span>

                    <span
                      v-else-if="
                        getLessonTypeIcon(lesson.type) === 'clipboard-list'
                      "
                      class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                        />
                      </svg>
                    </span>

                    <span
                      v-else-if="
                        getLessonTypeIcon(lesson.type) === 'document-text'
                      "
                      class="w-5 h-5 mr-2 text-gray-400 flex-shrink-0"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                      </svg>
                    </span>

                    <!-- Lesson title and duration -->
                    <div class="flex flex-grow items-center justify-between">
                      <span
                        class="text-sm"
                        :class="{ 'font-medium': isCurrentLesson(lesson.id) }"
                      >
                        {{ lesson.title }}
                      </span>
                      <div class="flex items-center ml-2">
                        <!-- Lock icon for locked lessons -->
                        <svg
                          v-if="lesson.isLocked"
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-gray-400 mr-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                            clip-rule="evenodd"
                          />
                        </svg>

                        <!-- Duration badge -->
                        <span class="text-xs text-gray-500">
                          {{ formatDuration(lesson.duration) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </transition>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background-color: rgba(243, 244, 246, 0.5);
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(107, 114, 128, 0.7);
}
</style>
