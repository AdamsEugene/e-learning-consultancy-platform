<!-- pages/courses/[id]/learn/[lessonId].vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import CourseVideoPlayer from "~/components/courses/CourseVideoPlayer.vue";

// Types
interface CourseLesson {
  id: number;
  title: string;
  duration: string;
  videoUrl?: string;
  content?: string;
  type: "video" | "quiz" | "assignment" | "resource";
  isCompleted?: boolean;
  order: number;
}

interface CourseSection {
  id: number;
  title: string;
  lessons: CourseLesson[];
  order: number;
}

interface Course {
  id: number;
  title: string;
  sections: CourseSection[];
}

// Route params
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));
const lessonId = computed(() => Number(route.params.lessonId));

// State
const course = ref<Course | null>(null);
const currentLesson = ref<CourseLesson | null>(null);
const loading = ref(true);
const error = ref(false);
const isSidebarOpen = ref(false);
const isTakingNotes = ref(false);
const notes = ref("");
const notesSaved = ref(false);
const watchProgress = ref(0); // Track video progress percentage
const isLessonCompleted = ref(false);

// Sidebar width/state preferences
const sidebarWidth = ref(320);
const isResizing = ref(false);
const startX = ref(0);
const lastKnownWidth = ref(320);

// Mock API call to fetch course data
const fetchCourseData = async () => {
  loading.value = true;
  error.value = false;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    course.value = {
      id: courseId.value,
      title: "Complete Web Development Bootcamp",
      sections: [
        {
          id: 1,
          title: "Introduction to Web Development",
          order: 1,
          lessons: [
            {
              id: 101,
              title: "Course Overview and Setup",
              duration: "15m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: true,
              order: 1,
            },
            {
              id: 102,
              title: "How the Web Works: A High-Level Overview",
              duration: "25m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 2,
            },
            {
              id: 103,
              title: "Setting Up Your Development Environment",
              duration: "30m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 3,
            },
            {
              id: 104,
              title: "Web Developer Tools and Resources",
              duration: "45m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 4,
            },
            {
              id: 105,
              title: "Introduction Quiz",
              duration: "20m",
              content: "This would be a quiz component in a real application.",
              type: "quiz",
              isCompleted: false,
              order: 5,
            },
          ],
        },
        {
          id: 2,
          title: "HTML Fundamentals",
          order: 2,
          lessons: [
            {
              id: 201,
              title: "HTML Basics and Document Structure",
              duration: "40m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 1,
            },
            {
              id: 202,
              title: "Working with Text Elements",
              duration: "35m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 2,
            },
            {
              id: 203,
              title: "Links, Images, and Multimedia",
              duration: "50m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 3,
            },
            {
              id: 204,
              title: "HTML Forms and Input Elements",
              duration: "55m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 4,
            },
            {
              id: 205,
              title: "Tables and Semantic HTML",
              duration: "45m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 5,
            },
            {
              id: 206,
              title: "HTML Project: Building Your First Webpage",
              duration: "1h 20m",
              content:
                "This would be an assignment component in a real application.",
              type: "assignment",
              isCompleted: false,
              order: 6,
            },
            {
              id: 207,
              title: "HTML Section Assessment",
              duration: "30m",
              content: "This would be a quiz component in a real application.",
              type: "quiz",
              isCompleted: false,
              order: 7,
            },
          ],
        },
      ],
    };

    // Find current lesson
    findCurrentLesson();
    loadNotes();
  } catch (err) {
    console.error("Error fetching course data:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

// Find the current lesson based on route params
const findCurrentLesson = () => {
  if (!course.value) return;

  for (const section of course.value.sections) {
    for (const lesson of section.lessons) {
      if (lesson.id === lessonId.value) {
        currentLesson.value = lesson;
        isLessonCompleted.value = !!lesson.isCompleted;
        return;
      }
    }
  }
};

// Get all lessons flattened into an array
const allLessons = computed(() => {
  if (!course.value) return [];

  return course.value.sections
    .flatMap((section) => section.lessons)
    .sort((a, b) => {
      // Sort by section order first, then by lesson order
      const sectionA = course.value!.sections.find((s) =>
        s.lessons.includes(a)
      )!;
      const sectionB = course.value!.sections.find((s) =>
        s.lessons.includes(b)
      )!;

      if (sectionA.order !== sectionB.order) {
        return sectionA.order - sectionB.order;
      }

      return a.order - b.order;
    });
});

// Get previous and next lessons
const previousLesson = computed(() => {
  if (!currentLesson.value || allLessons.value.length === 0) return null;

  const currentIndex = allLessons.value.findIndex(
    (lesson) => lesson.id === currentLesson.value!.id
  );
  if (currentIndex <= 0) return null;

  return allLessons.value[currentIndex - 1];
});

const nextLesson = computed(() => {
  if (!currentLesson.value || allLessons.value.length === 0) return null;

  const currentIndex = allLessons.value.findIndex(
    (lesson) => lesson.id === currentLesson.value!.id
  );
  if (currentIndex === -1 || currentIndex >= allLessons.value.length - 1)
    return null;

  return allLessons.value[currentIndex + 1];
});

// Navigate to a lesson
const navigateToLesson = (lesson: CourseLesson) => {
  if (lesson.id === currentLesson.value?.id) return;

  // Save current lesson's progress
  saveProgress();

  // Navigate to new lesson
  router.push(`/courses/${courseId.value}/learn/${lesson.id}`);
};

// Toggle sidebar
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

// Toggle notes section
const toggleNotes = () => {
  isTakingNotes.value = !isTakingNotes.value;

  if (!isTakingNotes.value) {
    saveNotes();
  }
};

// Save notes
const saveNotes = () => {
  // In a real app, this would save to an API or localStorage
  localStorage.setItem(
    `course_${courseId.value}_lesson_${lessonId.value}_notes`,
    notes.value
  );
  notesSaved.value = true;

  // Reset the saved indicator after a delay
  setTimeout(() => {
    notesSaved.value = false;
  }, 2000);
};

// Load notes
const loadNotes = () => {
  // In a real app, this would load from an API or localStorage
  const savedNotes = localStorage.getItem(
    `course_${courseId.value}_lesson_${lessonId.value}_notes`
  );
  if (savedNotes) {
    notes.value = savedNotes;
  }
};

// Mark lesson as complete
const markLessonComplete = () => {
  if (!currentLesson.value) return;

  // In a real app, this would make an API call
  currentLesson.value.isCompleted = true;
  isLessonCompleted.value = true;

  // If there's a next lesson, you might want to navigate to it
  if (nextLesson.value) {
    navigateToLesson(nextLesson.value);
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

// Sidebar resize handlers
const startResize = (event: MouseEvent) => {
  isResizing.value = true;
  startX.value = event.clientX;
  lastKnownWidth.value = sidebarWidth.value;

  document.addEventListener("mousemove", resize);
  document.addEventListener("mouseup", stopResize);
};

const resize = (event: MouseEvent) => {
  if (!isResizing.value) return;

  const diff = event.clientX - startX.value;
  const newWidth = Math.max(250, Math.min(500, lastKnownWidth.value + diff));
  sidebarWidth.value = newWidth;
};

const stopResize = () => {
  isResizing.value = false;
  document.removeEventListener("mousemove", resize);
  document.removeEventListener("mouseup", stopResize);
};

// Lifecycle hooks
onMounted(() => {
  fetchCourseData();

  // Handle beforeunload to save progress
  window.addEventListener("beforeunload", saveProgress);
});

onBeforeUnmount(() => {
  saveProgress();
  window.removeEventListener("beforeunload", saveProgress);
  document.removeEventListener("mousemove", resize);
  document.removeEventListener("mouseup", stopResize);
});
</script>

<template>
  <div class="bg-gray-900 text-white min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 py-2 px-4">
      <div class="container mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <!-- Back to course button -->
          <NuxtLink
            :to="`/courses/${courseId}`"
            class="text-gray-300 hover:text-white transition-colors flex items-center"
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
          <span class="text-lg font-medium truncate max-w-md">{{
            course?.title
          }}</span>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Toggle sidebar button -->
          <button
            @click="toggleSidebar"
            class="text-gray-300 hover:text-white transition-colors p-2"
            :class="{ 'bg-gray-700 rounded-lg': isSidebarOpen }"
            aria-label="Toggle course content"
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
          </button>

          <!-- Toggle notes button -->
          <button
            @click="toggleNotes"
            class="text-gray-300 hover:text-white transition-colors p-2"
            :class="{ 'bg-gray-700 rounded-lg': isTakingNotes }"
            aria-label="Toggle notes"
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
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />
            </svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Loading state -->
    <div v-if="loading" class="flex-grow flex items-center justify-center">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      ></div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="flex-grow flex items-center justify-center">
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
        <h3 class="text-xl font-bold mb-2">Failed to load lesson</h3>
        <p class="text-gray-400 mb-4">
          There was an error loading this lesson. Please try again.
        </p>
        <button
          @click="fetchCourseData"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Retry
        </button>
      </div>
    </div>

    <!-- Main content with sidebar -->
    <div v-else class="flex-grow flex overflow-hidden">
      <!-- Sidebar (course contents) -->
      <div
        v-show="isSidebarOpen"
        class="bg-gray-800 border-r border-gray-700 overflow-y-auto flex-shrink-0 transition-all duration-300"
        :style="{ width: `${sidebarWidth}px` }"
      >
        <!-- Sidebar header -->
        <div class="p-4 border-b border-gray-700 sticky top-0 bg-gray-800 z-10">
          <h2 class="text-lg font-bold">Course Content</h2>
        </div>

        <!-- Sections and lessons -->
        <div class="p-2">
          <div
            v-for="section in course?.sections"
            :key="section.id"
            class="mb-4"
          >
            <!-- Section header -->
            <div class="px-3 py-2 bg-gray-700 rounded-lg mb-2">
              <h3 class="font-medium">{{ section.title }}</h3>
            </div>

            <!-- Lessons list -->
            <div class="space-y-1 ml-2">
              <button
                v-for="lesson in section.lessons"
                :key="lesson.id"
                @click="navigateToLesson(lesson)"
                class="w-full px-3 py-2 rounded-lg flex items-center text-left transition-colors"
                :class="[
                  lesson.id === currentLesson?.id
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-300 hover:bg-gray-700',
                ]"
              >
                <!-- Lesson completion status -->
                <div class="mr-3 flex-shrink-0">
                  <svg
                    v-if="lesson.isCompleted"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-400"
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
                    class="h-5 w-5 text-gray-400"
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
                  <div class="font-medium truncate">{{ lesson.title }}</div>
                  <div class="flex items-center text-xs text-gray-400">
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
                    <span
                      >{{
                        lesson.type.charAt(0).toUpperCase() +
                        lesson.type.slice(1)
                      }}
                      • {{ lesson.duration }}</span
                    >
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar resize handle -->
        <div
          class="absolute top-0 right-0 bottom-0 w-1 cursor-ew-resize bg-gray-700 hover:bg-indigo-500 transition-colors"
          @mousedown="startResize"
        ></div>
      </div>

      <!-- Main lesson content area -->
      <div class="flex-grow flex flex-col overflow-hidden relative">
        <!-- Lesson content -->
        <div
          class="flex-grow overflow-y-auto p-4 lg:p-6"
          :class="{ 'mr-96': isTakingNotes }"
        >
          <div class="max-w-4xl mx-auto">
            <!-- Lesson title and info -->
            <div class="mb-6">
              <h1 class="text-2xl lg:text-3xl font-bold mb-2">
                {{ currentLesson?.title }}
              </h1>
              <div class="flex items-center text-gray-400 text-sm">
                <span class="capitalize">{{ currentLesson?.type }}</span>
                <span class="mx-2">•</span>
                <span>{{ currentLesson?.duration }}</span>

                <!-- Completed badge -->
                <span
                  v-if="isLessonCompleted"
                  class="ml-2 bg-green-900 text-green-300 text-xs px-2 py-0.5 rounded-full"
                >
                  Completed
                </span>
              </div>
            </div>

            <!-- Video player for video lessons -->
            <div
              v-if="currentLesson?.type === 'video' && currentLesson?.videoUrl"
            >
              <CourseVideoPlayer
                :videoUrl="currentLesson.videoUrl"
                :title="currentLesson.title"
                :poster="''"
                @time-update="handleVideoProgress"
                @ended="handleVideoEnded"
              />

              <!-- Video progress -->
              <div class="mt-4 bg-gray-700 rounded-full h-2 overflow-hidden">
                <div
                  class="h-full bg-indigo-500 transition-all duration-300"
                  :style="{ width: `${watchProgress}%` }"
                ></div>
              </div>
            </div>

            <!-- Quiz or assignment content -->
            <div
              v-else-if="currentLesson?.content"
              class="prose prose-invert max-w-none"
            >
              <div
                v-if="currentLesson.type === 'quiz'"
                class="bg-gray-800 rounded-lg p-6 border border-gray-700"
              >
                <h2 class="text-xl font-bold mb-4">
                  Quiz: {{ currentLesson.title }}
                </h2>
                <p class="mb-6 text-gray-300">{{ currentLesson.content }}</p>

                <!-- Placeholder for quiz component -->
                <div class="bg-gray-700 p-6 rounded-lg text-center">
                  <p class="text-gray-300 mb-4">
                    This is a placeholder for the quiz component.
                  </p>
                  <p class="text-gray-400 text-sm mb-6">
                    In a real application, this would be replaced with an
                    interactive quiz.
                  </p>

                  <button
                    @click="markLessonComplete"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                  >
                    Complete Quiz
                  </button>
                </div>
              </div>

              <div
                v-else-if="currentLesson.type === 'assignment'"
                class="bg-gray-800 rounded-lg p-6 border border-gray-700"
              >
                <h2 class="text-xl font-bold mb-4">
                  Assignment: {{ currentLesson.title }}
                </h2>
                <p class="mb-6 text-gray-300">{{ currentLesson.content }}</p>

                <!-- Placeholder for assignment component -->
                <div class="bg-gray-700 p-6 rounded-lg text-center">
                  <p class="text-gray-300 mb-4">
                    This is a placeholder for the assignment component.
                  </p>
                  <p class="text-gray-400 text-sm mb-6">
                    In a real application, this would include assignment
                    details, submission form, etc.
                  </p>

                  <button
                    @click="markLessonComplete"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                  >
                    Submit Assignment
                  </button>
                </div>
              </div>

              <div v-else>
                <p>{{ currentLesson.content }}</p>
              </div>
            </div>

            <!-- Navigation buttons -->
            <div class="mt-8 flex justify-between">
              <button
                v-if="previousLesson"
                @click="navigateToLesson(previousLesson)"
                class="flex items-center bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                Previous Lesson
              </button>
              <div v-else></div>

              <div>
                <button
                  v-if="!isLessonCompleted"
                  @click="markLessonComplete"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  Mark as Complete
                </button>

                <button
                  v-else-if="nextLesson"
                  @click="navigateToLesson(nextLesson)"
                  class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  Next Lesson
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 ml-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Notes sidebar (optional) -->
      <div
        v-show="isTakingNotes"
        class="absolute right-0 top-0 bottom-0 w-96 bg-gray-800 border-l border-gray-700 overflow-y-auto transition-all duration-300"
      >
        <div
          class="p-4 border-b border-gray-700 flex justify-between items-center sticky top-0 bg-gray-800 z-10"
        >
          <h3 class="font-bold">Lesson Notes</h3>
          <div class="flex items-center">
            <span v-if="notesSaved" class="text-green-400 text-xs mr-2"
              >Saved ✓</span
            >
            <button
              @click="toggleNotes"
              class="text-gray-400 hover:text-white transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>

        <div class="p-4">
          <textarea
            v-model="notes"
            class="w-full h-96 bg-gray-700 text-white border border-gray-600 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
            placeholder="Take notes for this lesson..."
            @input="notesSaved = false"
          ></textarea>

          <button
            @click="saveNotes"
            class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition-colors"
          >
            Save Notes
          </button>
        </div>
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
  background: #1f2937; /* gray-800 */
}

::-webkit-scrollbar-thumb {
  background: #4b5563; /* gray-600 */
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1; /* indigo-500 */
}

.prose {
  color: #e5e7eb; /* text-gray-200 */
}

.prose h1,
.prose h2,
.prose h3,
.prose h4 {
  color: white;
}

.prose a {
  color: #93c5fd; /* text-blue-300 */
}

.prose strong {
  color: white;
}

.prose blockquote {
  border-left-color: #4b5563; /* border-gray-600 */
}

.prose pre {
  background-color: #111827; /* bg-gray-900 */
}

.prose code {
  color: #93c5fd; /* text-blue-300 */
}
</style>
