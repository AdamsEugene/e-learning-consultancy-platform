<!-- pages/courses/[id]/learn/[lessonId].vue -->
<script setup lang="ts">
import type { Note } from "~/types/courseTemp";
import {
  useSharedLesson,
  type CourseLesson,
} from "~/composables/useSharedLesson";

// Define layout
definePageMeta({
  layout: "course-lesson",
});

// Route params
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));
const lessonId = computed(() => Number(route.params.lessonId));

// Get shared lesson state
const {
  currentCourse,
  currentLesson,
  // currentSection,
  isLoading,
  hasError,
  markLessonAsCompleted,
  getCourseProgress,
  getNextLesson,
  getPreviousLesson,
  setCurrentLesson,
} = useSharedLesson();

// Local state
const watchProgress = ref(0); // Track video progress percentage
const isLessonCompleted = ref(false);
const activeTab = ref<"content" | "resources" | "notes" | "discussions">(
  "content"
);
const isNotesPanelOpen = ref(false);

// Get sidebar state from the layout
const { isSidebarOpen, toggleSidebar, isSmallScreen } = inject("sidebarState", {
  isSidebarOpen: ref(true),
  toggleSidebar: (forcedState: boolean | undefined) => {
    if (typeof forcedState === "boolean") {
      isSidebarOpen.value = forcedState;
    } else {
      isSidebarOpen.value = !isSidebarOpen.value;
    }
  },
  isSmallScreen: ref(false),
  sidebarWidth: ref(320),
});

// Notes functionality
const notes = ref<Note[]>([]);
const noteContent = ref("");
const notesSaved = ref(false);

// Toggle notes panel
const toggleNotesPanel = (forcedState?: boolean) => {
  if (typeof forcedState === "boolean") {
    isNotesPanelOpen.value = forcedState;
  } else {
    isNotesPanelOpen.value = !isNotesPanelOpen.value;
  }
};

// Navigate to a lesson
const navigateToLesson = (lesson: CourseLesson) => {
  if (lesson.id === currentLesson.value?.id) return;

  // Save current lesson's progress
  saveProgress();

  // Set the lesson in shared state
  setCurrentLesson(lesson);

  // Navigate to new lesson
  router.push(`/courses/${courseId.value}/learn/${lesson.id}`);

  // Close mobile sidebar on small screens
  if (isSmallScreen.value) {
    toggleSidebar(false);
  }
};

// Save note
const saveNote = () => {
  if (!noteContent.value.trim() || !currentLesson.value) return;

  const newNote = {
    id: `note-${Date.now()}`,
    text: noteContent.value.trim(),
    timestamp: Date.now(),
    lessonId: lessonId.value,
  };

  // Add to notes array
  notes.value.unshift(newNote);

  // Save to localStorage
  const savedNotes = JSON.parse(
    localStorage.getItem(`course_${courseId.value}_notes`) || "[]"
  );
  savedNotes.push(newNote);
  localStorage.setItem(
    `course_${courseId.value}_notes`,
    JSON.stringify(savedNotes)
  );

  // Clear input
  noteContent.value = "";

  // Show saved indicator
  notesSaved.value = true;
  setTimeout(() => {
    notesSaved.value = false;
  }, 2000);
};

// Load notes
const loadNotes = () => {
  if (!courseId.value) return;

  // Load from localStorage
  const savedNotes = JSON.parse(
    localStorage.getItem(`course_${courseId.value}_notes`) || "[]"
  );

  // Filter notes for current lesson
  notes.value = savedNotes.filter(
    (note: Note) => Number(note.lessonId) === lessonId.value
  );
};

// Delete note
const deleteNote = (noteId: string) => {
  // Remove from current notes
  notes.value = notes.value.filter((note) => note.id !== noteId);

  // Update localStorage
  const savedNotes = JSON.parse(
    localStorage.getItem(`course_${courseId.value}_notes`) || "[]"
  );
  const updatedNotes = savedNotes.filter(
    (note: { id: string }) => note.id !== noteId
  );
  localStorage.setItem(
    `course_${courseId.value}_notes`,
    JSON.stringify(updatedNotes)
  );
};

// Mark lesson as complete
const markLessonComplete = () => {
  if (!currentLesson.value) return;

  // Use shared state to mark lesson as completed
  markLessonAsCompleted(currentLesson.value.id);
  isLessonCompleted.value = true;

  // If there's a next lesson, you might want to navigate to it
  const nextLesson = getNextLesson();
  if (nextLesson) {
    navigateToLesson(nextLesson);
  }
};

// Handle video progress
const handleVideoProgress = (time: number) => {
  if (!currentLesson.value || currentLesson.value.type !== "video") return;

  // Calculate progress percentage (this would be more accurate with actual video duration)
  const duration = 600; // Mock duration in seconds
  watchProgress.value = Math.min(100, Math.round((time / duration) * 100));

  // Auto-mark as complete if watched >90%
  if (watchProgress.value > 90 && !isLessonCompleted.value) {
    markLessonComplete();
  }
};

// Handle video completion
const handleVideoEnded = () => {
  if (!isLessonCompleted.value) {
    markLessonComplete();
  }
};

// Save progress before leaving
const saveProgress = () => {
  // In a real app, this would make an API call to save progress
  console.log(
    "Saving progress for lesson",
    currentLesson.value?.id,
    "progress:",
    watchProgress.value
  );
};

// Mock lesson content for rich display
const lessonContent = computed(() => {
  if (!currentLesson.value) return "";

  return `
    <div class="prose prose-indigo max-w-none">
      <h2>Lesson Overview</h2>
      <p>In this lesson, we'll learn about ${currentLesson.value.title}. This is crucial knowledge for becoming a proficient web developer.</p>
      <p>We'll cover the following topics:</p>
      <ul>
        <li>Core concepts and principles</li>
        <li>Best practices and common patterns</li>
        <li>Practical examples and demos</li>
        <li>Common pitfalls and how to avoid them</li>
      </ul>
      <h3>Learning Objectives</h3>
      <p>By the end of this lesson, you will be able to:</p>
      <ul>
        <li>Understand the fundamentals of ${currentLesson.value.title}</li>
        <li>Apply these concepts in your own projects</li>
        <li>Troubleshoot common issues</li>
      </ul>
      <h3>Additional Resources</h3>
      <p>The following resources may be helpful for further learning:</p>
      <ul>
        <li><a href="#" class="text-indigo-600">Official Documentation</a></li>
        <li><a href="#" class="text-indigo-600">Supplementary Reading Material</a></li>
        <li><a href="#" class="text-indigo-600">Practice Exercises</a></li>
      </ul>
    </div>
  `;
});

// Update lesson completion status
const updateLessonStatus = () => {
  if (currentLesson.value) {
    isLessonCompleted.value = !!currentLesson.value.isCompleted;
  }
};

// Watch for route changes
watch(
  () => route.params.lessonId,
  (newId, oldId) => {
    if (newId !== oldId) {
      // Close notes panel on lesson change
      isNotesPanelOpen.value = false;
      // Reset watch progress
      watchProgress.value = 0;
      // Load notes for new lesson
      loadNotes();
      // Update lesson completion status
      updateLessonStatus();
    }
  }
);

// Watch for changes in current lesson
watch(
  () => currentLesson.value,
  (newLesson) => {
    if (newLesson) {
      updateLessonStatus();
    }
  }
);

// Lifecycle hooks
onMounted(() => {
  loadNotes();
  updateLessonStatus();
  window.addEventListener("beforeunload", saveProgress);
});

onBeforeUnmount(() => {
  saveProgress();
  window.removeEventListener("beforeunload", saveProgress);
});
</script>

<template>
  <div class="bg-white text-gray-900 min-h-screen flex flex-col">
    <!-- Floating notes button -->
    <CoursesLearnFloatingNotesButton
      :is-open="isNotesPanelOpen"
      :has-notes="notes.length > 0"
      @toggle="toggleNotesPanel()"
    />

    <!-- Notes panel (slides in from right) -->
    <CoursesLearnNotesPanel
      v-if="currentLesson"
      :is-open="isNotesPanelOpen"
      :notes="notes"
      :note-content="noteContent"
      :notes-saved="notesSaved"
      :course-id="courseId"
      :lesson-id="lessonId"
      @close="toggleNotesPanel(false)"
      @save="saveNote"
      @delete="deleteNote"
      @update:note-content="noteContent = $event"
      @update:is-open="isNotesPanelOpen = $event"
    />

    <!-- Header -->
    <header
      class="bg-white border-b border-gray-200 py-2 px-4 shadow-sm sticky top-0 z-20"
    >
      <div class="container mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <!-- Back to course button -->
          <NuxtLink
            :to="`/courses/${courseId}`"
            class="text-gray-600 hover:text-indigo-600 transition-colors flex items-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            Back to Course
          </NuxtLink>

          <!-- Course title -->
          <span class="text-md font-medium truncate max-w-md hidden md:inline">
            {{ currentCourse?.title }}
          </span>
        </div>

        <div class="flex items-center space-x-2 md:space-x-4">
          <!-- Course progress -->
          <div class="hidden md:flex items-center text-sm text-gray-600">
            <span class="mr-2">{{ getCourseProgress() }}% complete</span>
            <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
              <div
                class="h-full bg-green-500 transition-all duration-300"
                :style="{ width: `${getCourseProgress()}%` }"
              />
            </div>
          </div>

          <!-- Notes button -->
          <UiButton
            variant="ghost"
            color="primary"
            size="md"
            class="relative"
            :class="{
              'bg-indigo-50 text-indigo-600': isNotesPanelOpen,
            }"
            aria-label="Toggle notes"
            @click="toggleNotesPanel()"
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
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />  
            </svg>
            <span
              v-if="notes.length > 0"
              class="relative -top-[5px] -left-[18px] !z-50 flex items-center justify-center w-4 h-4 text-xs bg-indigo-600 text-white rounded-full"
            >
              {{ notes.length }}
            </span>
          </UiButton>

          <!-- Toggle sidebar button -->
          <UiButton
            variant="ghost"
            color="primary"
            size="md"
            :class="{
              'bg-indigo-50 text-indigo-600': isSidebarOpen,
            }"
            aria-label="Toggle course content"
            @click="() => toggleSidebar(undefined)"
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
                d="M4 6h16M4 12h16M4 18h7"
              />
            </svg>
          </UiButton>
        </div>
      </div>
    </header>

    <!-- Loading state -->
    <div v-if="isLoading" class="flex-grow flex items-center justify-center">
      <div class="flex flex-col items-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mb-4"
        />
        <p class="text-gray-600">Loading lesson...</p>
      </div>
    </div>

    <!-- Error state -->
    <div
      v-else-if="hasError"
      class="flex-grow flex items-center justify-center"
    >
      <div
        class="text-center max-w-md mx-auto p-6 bg-white rounded-lg shadow-md"
      >
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
        <h3 class="text-xl font-bold mb-2">Failed to load lesson</h3>
        <p class="text-gray-600 mb-4">
          There was an error loading this lesson. Please try again.
        </p>
        <UiButton variant="primary" color="primary" size="md"> Retry </UiButton>
      </div>
    </div>

    <!-- Main lesson content -->
    <div v-else-if="currentLesson" class="flex-grow overflow-y-auto">
      <div class="container mx-auto px-4 py-6 max-w-6xl">
        <!-- Lesson content based on type -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
          <!-- Video player -->
          <div
            v-if="currentLesson?.type === 'video' && currentLesson?.videoUrl"
          >
            <CoursesLearnVideoPlayer
              :video-url="currentLesson.videoUrl"
              :progress="watchProgress"
              @time-update="handleVideoProgress"
              @ended="handleVideoEnded"
            />
          </div>

          <!-- Quiz content -->
          <CoursesLearnQuizComponent
            v-else-if="currentLesson?.type === 'quiz' && currentLesson?.content"
            :title="currentLesson.title"
            :content="currentLesson.content"
            @complete="markLessonComplete"
          />

          <!-- Assignment content -->
          <CoursesLearnAssignmentComponent
            v-else-if="
              currentLesson?.type === 'assignment' && currentLesson?.content
            "
            :title="currentLesson.title"
            :content="currentLesson.content"
            @complete="markLessonComplete"
          />
        </div>

        <!-- Tabbed content area -->
        <CoursesLearnTabsLessonTabs
          :active-tab="activeTab"
          :lesson-content="lessonContent"
          :instructor="currentCourse?.instructor"
          @update:active-tab="activeTab = $event"
        />

        <!-- Navigation buttons -->
        <CoursesLearnLessonNavigation
          :previous-lesson="getPreviousLesson()"
          :next-lesson="getNextLesson()"
          :is-completed="isLessonCompleted"
          @navigate="navigateToLesson"
          @complete="markLessonComplete"
        />
      </div>
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

/* Prose styles for rich text content */
:deep(.prose) {
  color: #334155; /* text-slate-700 */
  max-width: none;
}

:deep(.prose h1),
:deep(.prose h2),
:deep(.prose h3),
:deep(.prose h4) {
  color: #1e293b; /* text-slate-800 */
  margin-top: 1.5em;
  margin-bottom: 0.5em;
}

:deep(.prose h2) {
  font-size: 1.5em;
  font-weight: 700;
}

:deep(.prose h3) {
  font-size: 1.25em;
  font-weight: 600;
}

:deep(.prose a) {
  color: #4f46e5; /* text-indigo-600 */
  text-decoration: none;
}

:deep(.prose a:hover) {
  text-decoration: underline;
}

:deep(.prose strong) {
  color: #0f172a; /* text-slate-900 */
  font-weight: 600;
}

:deep(.prose blockquote) {
  border-left-color: #cbd5e1; /* border-gray-300 */
  color: #475569; /* text-slate-600 */
}

:deep(.prose pre) {
  background-color: #f8fafc; /* bg-slate-50 */
  border-radius: 0.375rem;
  overflow-x: auto;
}

:deep(.prose code) {
  color: #4f46e5; /* text-indigo-600 */
  font-weight: 500;
}

:deep(.prose ul) {
  margin-top: 0.5em;
  margin-bottom: 0.5em;
}

:deep(.prose ol) {
  margin-top: 0.5em;
  margin-bottom: 0.5em;
}

:deep(.prose li) {
  margin-top: 0.25em;
  margin-bottom: 0.25em;
}
</style>
