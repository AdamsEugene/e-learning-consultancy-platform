<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- pages/courses/[id]/lessons/[lessonId].vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import CourseSidebar from "@/components/courses/CourseSidebar.vue";
import LessonPlayer from "@/components/courses/LessonPlayer.vue";

// Types
import type { Section } from "@/components/courses/CourseSidebar.vue";

// Route params
const route = useRoute();
const router = useRouter();

const courseId = computed(() => route.params.id as string);
const lessonId = computed(() => route.params.lessonId as string);

// State
const isLoading = ref(true);
const isMobileSidebarOpen = ref(false);
const error = ref("");
const course = ref<any>(null);
const currentLesson = ref<any>(null);
const nextLesson = ref<any>(null);
const prevLesson = ref<any>(null);
const courseSections = ref<Section[]>([]);
const lessonProgress = ref(0);
const courseProgress = ref(0);
const isLessonCompleted = ref(false);
const noteContent = ref("");
const notes = ref<
  { id: string; text: string; timestamp: number; lessonId: string }[]
>([]);

// Responsive state
const isSmallScreen = ref(false);

// Tabs for additional content
const activeTab = ref("content"); // 'content', 'discussions', 'resources', 'notes'

// Fetch course data
const fetchCourseData = async () => {
  isLoading.value = true;
  error.value = "";

  try {
    // In a real implementation, this would be an API call
    // For demo, we'll use mock data

    // Simulate API request delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock course data
    course.value = {
      id: courseId.value,
      title: "Complete Web Development Bootcamp",
      description: "Learn full-stack web development from scratch",
      instructor: {
        id: "instructor-1",
        name: "Sarah Johnson",
        avatar: "/api/placeholder/64/64",
        title: "Senior Web Developer",
        bio: "15+ years of experience in web development. Worked at Google and Microsoft.",
      },
    };

    // Mock sections and lessons
    courseSections.value = [
      {
        id: "section-1",
        title: "Introduction to Web Development",
        lessons: [
          {
            id: "lesson-1",
            title: "Welcome to the Course",
            duration: 10,
            type: "video",
            isCompleted: true,
          },
          {
            id: "lesson-2",
            title: "Setting Up Your Development Environment",
            duration: 25,
            type: "video",
            isCompleted: true,
          },
        ],
        isCompleted: true,
      },
      {
        id: "section-2",
        title: "HTML Foundations",
        lessons: [
          {
            id: "lesson-3",
            title: "HTML Document Structure",
            duration: 18,
            type: "video",
            isCompleted: true,
          },
          {
            id: "lesson-4",
            title: "HTML Elements and Attributes",
            duration: 22,
            type: "video",
            isCompleted: false,
          },
          {
            id: "lesson-5",
            title: "HTML Quiz",
            duration: 15,
            type: "quiz",
            isCompleted: false,
          },
        ],
        isCompleted: false,
      },
      {
        id: "section-3",
        title: "CSS Basics",
        lessons: [
          {
            id: "lesson-6",
            title: "Introduction to CSS",
            duration: 20,
            type: "video",
            isCompleted: false,
          },
          {
            id: "lesson-7",
            title: "CSS Selectors",
            duration: 25,
            type: "video",
            isCompleted: false,
          },
          {
            id: "lesson-8",
            title: "CSS Box Model",
            duration: 28,
            type: "video",
            isCompleted: false,
            isLocked: true,
          },
        ],
        isCompleted: false,
      },
    ];

    // Find current lesson
    let foundCurrentLesson = false;
    let foundPrevLesson = null;

    // Loop through sections to find the current, next, and previous lessons
    for (let i = 0; i < courseSections.value.length; i++) {
      const section = courseSections.value[i];

      for (let j = 0; j < section.lessons.length; j++) {
        const lesson = section.lessons[j];

        if (foundCurrentLesson) {
          // We already found the current lesson, so this is the next one
          nextLesson.value = {
            ...lesson,
            sectionId: section.id,
            sectionTitle: section.title,
          };
          break;
        }

        if (lesson.id === lessonId.value) {
          // This is the current lesson
          currentLesson.value = {
            ...lesson,
            sectionId: section.id,
            sectionTitle: section.title,
          };
          foundCurrentLesson = true;
        } else {
          // Save this as the previous lesson
          foundPrevLesson = {
            ...lesson,
            sectionId: section.id,
            sectionTitle: section.title,
          };
        }
      }

      if (foundCurrentLesson && nextLesson.value) {
        break;
      }
    }

    // Set previous lesson
    prevLesson.value = foundPrevLesson;

    // Set lesson completion status
    isLessonCompleted.value = currentLesson.value?.isCompleted || false;

    // Calculate course progress
    let completedLessons = 0;
    let totalLessons = 0;

    courseSections.value.forEach((section) => {
      section.lessons.forEach((lesson) => {
        totalLessons++;
        if (lesson.isCompleted) {
          completedLessons++;
        }
      });
    });

    courseProgress.value =
      totalLessons > 0
        ? Math.round((completedLessons / totalLessons) * 100)
        : 0;

    // Load notes for this lesson
    loadNotes();

    isLoading.value = false;
  } catch (err) {
    console.error("Error fetching course data:", err);
    error.value = "Failed to load course data. Please try again.";
    isLoading.value = false;
  }
};

// Mock lesson content
const lessonContent = computed(() => {
  if (!currentLesson.value) return "";

  // In a real implementation, this would come from the API
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

// Navigate to a lesson
const navigateToLesson = (lessonId: string) => {
  router.push(`/courses/${courseId.value}/lessons/${lessonId}`);
  isMobileSidebarOpen.value = false;
};

// Toggle mobile sidebar
const toggleMobileSidebar = (isOpen: boolean) => {
  isMobileSidebarOpen.value = isOpen;
};

// Handle video progress
const handleLessonProgress = (time: number) => {
  if (!currentLesson.value) return;

  // Calculate progress percentage
  const totalDuration = currentLesson.value.duration * 60; // convert to seconds
  lessonProgress.value = Math.round((time / totalDuration) * 100);

  // Mark lesson as completed if progress is > 90%
  if (lessonProgress.value > 90 && !isLessonCompleted.value) {
    markLessonAsCompleted();
  }
};

// Mark lesson as completed
const markLessonAsCompleted = async () => {
  if (isLessonCompleted.value) return;

  try {
    // In a real implementation, this would be an API call
    // For demo, we'll update our local state

    // Find the lesson and update its completion status
    courseSections.value.forEach((section) => {
      section.lessons.forEach((lesson) => {
        if (lesson.id === lessonId.value) {
          lesson.isCompleted = true;
          isLessonCompleted.value = true;
        }
      });
    });

    // Recalculate course progress
    let completedLessons = 0;
    let totalLessons = 0;

    courseSections.value.forEach((section) => {
      section.lessons.forEach((lesson) => {
        totalLessons++;
        if (lesson.isCompleted) {
          completedLessons++;
        }
      });
    });

    courseProgress.value =
      totalLessons > 0
        ? Math.round((completedLessons / totalLessons) * 100)
        : 0;

    console.log("Lesson marked as completed");
  } catch (err) {
    console.error("Error marking lesson as completed:", err);
  }
};

// Handle lesson completion
const handleLessonCompleted = () => {
  markLessonAsCompleted();
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

  // In a real implementation, this would be saved to the API
  notes.value.unshift(newNote);

  // Save to localStorage for persistence across page refreshes
  const savedNotes = JSON.parse(
    localStorage.getItem(`course_notes_${courseId.value}`) || "[]"
  );
  savedNotes.push(newNote);
  localStorage.setItem(
    `course_notes_${courseId.value}`,
    JSON.stringify(savedNotes)
  );

  // Clear input
  noteContent.value = "";
};

// Load notes
const loadNotes = () => {
  if (!courseId.value) return;

  // In a real implementation, this would be loaded from the API
  // For demo, we'll use localStorage
  const savedNotes = JSON.parse(
    localStorage.getItem(`course_notes_${courseId.value}`) || "[]"
  );

  // Filter notes for the current lesson
  notes.value = savedNotes.filter(
    (note: { lessonId: string }) => note.lessonId === lessonId.value
  );
};

// Format timestamp
const formatTimestamp = (timestamp: number): string => {
  return new Date(timestamp).toLocaleString();
};

// Delete note
const deleteNote = (noteId: string) => {
  // Remove from current notes
  notes.value = notes.value.filter((note) => note.id !== noteId);

  // Update localStorage
  const savedNotes = JSON.parse(
    localStorage.getItem(`course_notes_${courseId.value}`) || "[]"
  );
  const updatedNotes = savedNotes.filter(
    (note: { id: string }) => note.id !== noteId
  );
  localStorage.setItem(
    `course_notes_${courseId.value}`,
    JSON.stringify(updatedNotes)
  );
};

// Check screen size
const checkScreenSize = () => {
  isSmallScreen.value = window.innerWidth < 768;
};

// Watch for route changes to load new lesson data
watch(
  () => route.params.lessonId,
  (newLessonId, oldLessonId) => {
    if (newLessonId && newLessonId !== oldLessonId) {
      fetchCourseData();
    }
  }
);

// Lifecycle hooks
onMounted(() => {
  fetchCourseData();
  checkScreenSize();
  window.addEventListener("resize", checkScreenSize);
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", checkScreenSize);
});
</script>

<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="lesson-page bg-gray-100 min-h-screen">
    <!-- Mobile sidebar toggle button -->
    <button
      v-if="isSmallScreen && !isMobileSidebarOpen"
      class="fixed bottom-4 left-4 z-30 p-3 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
      aria-label="Open course navigation"
      @click="isMobileSidebarOpen = true"
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
    </button>

    <!-- Course layout -->
    <div class="flex h-screen overflow-hidden">
      <!-- Course sidebar -->
      <CourseSidebar
        v-if="!isLoading && course"
        :course-id="courseId"
        :course-title="course.title"
        :sections="courseSections"
        :current-lesson-id="lessonId"
        :current-section-id="currentLesson?.sectionId"
        :progress="courseProgress"
        :is-mobile-open="isMobileSidebarOpen"
        class="w-64 flex-shrink-0"
        @navigate="navigateToLesson"
        @toggle-mobile="toggleMobileSidebar"
      />

      <!-- Main content -->
      <main class="flex-grow overflow-y-auto">
        <!-- Loading state -->
        <div v-if="isLoading" class="flex justify-center items-center h-screen">
          <div
            class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
          />
        </div>

        <!-- Error state -->
        <div
          v-else-if="error"
          class="flex justify-center items-center h-screen"
        >
          <div class="text-center p-8 max-w-md">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-12 w-12 text-red-500 mx-auto mb-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <h2 class="text-xl font-bold mb-2">{{ error }}</h2>
            <button
              class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
              @click="fetchCourseData"
            >
              Retry
            </button>
          </div>
        </div>

        <!-- Lesson content -->
        <div v-else-if="currentLesson" class="pb-12">
          <!-- Course and lesson info -->
          <div class="bg-white shadow-sm border-b p-4">
            <div class="container mx-auto">
              <div class="flex flex-wrap items-center justify-between">
                <!-- Breadcrumb -->
                <div
                  class="flex items-center text-sm text-gray-500 mb-2 sm:mb-0"
                >
                  <span>{{ course.title }}</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mx-2"
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
                  <span>{{ currentLesson.sectionTitle }}</span>
                </div>

                <!-- Lesson completion status -->
                <div
                  v-if="isLessonCompleted"
                  class="flex items-center text-sm text-green-600"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Completed
                </div>
              </div>

              <!-- Lesson title -->
              <h1 class="text-2xl font-bold mt-2">
                {{ currentLesson.title }}
              </h1>
            </div>
          </div>

          <div class="container mx-auto px-4 py-6">
            <!-- Video player -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
              <LessonPlayer
                :video-url="`https://samplelib.com/lib/preview/mp4/sample-5s.mp4`"
                :poster-url="`/api/placeholder/1280/720`"
                :start-time="0"
                :save-progress="true"
                class="w-full"
                @timeupdate="handleLessonProgress"
                @ended="handleLessonCompleted"
              />
            </div>

            <!-- Content tabs -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
              <!-- Tab navigation -->
              <div class="border-b px-6">
                <div class="flex overflow-x-auto">
                  <button
                    :class="[
                      'px-4 py-3 text-sm font-medium whitespace-nowrap',
                      activeTab === 'content'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700',
                    ]"
                    @click="activeTab = 'content'"
                  >
                    Lesson Content
                  </button>

                  <button
                    :class="[
                      'px-4 py-3 text-sm font-medium whitespace-nowrap',
                      activeTab === 'resources'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700',
                    ]"
                    @click="activeTab = 'resources'"
                  >
                    Resources
                  </button>

                  <button
                    :class="[
                      'px-4 py-3 text-sm font-medium whitespace-nowrap',
                      activeTab === 'notes'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700',
                    ]"
                    @click="activeTab = 'notes'"
                  >
                    My Notes
                  </button>

                  <button
                    :class="[
                      'px-4 py-3 text-sm font-medium whitespace-nowrap',
                      activeTab === 'discussions'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700',
                    ]"
                    @click="activeTab = 'discussions'"
                  >
                    Discussions
                  </button>
                </div>
              </div>

              <!-- Tab content -->
              <div class="p-6">
                <!-- Lesson content tab -->
                <div v-if="activeTab === 'content'" v-html="lessonContent" />

                <!-- Resources tab -->
                <div v-else-if="activeTab === 'resources'">
                  <h3 class="text-lg font-medium mb-4">Lesson Resources</h3>
                  <ul class="space-y-3">
                    <li class="flex items-start">
                      <div class="flex-shrink-0 h-6 w-6 mt-1">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6 text-indigo-600"
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
                      </div>
                      <div class="ml-3">
                        <h4 class="text-base font-medium">Lesson Slides</h4>
                        <p class="text-sm text-gray-500">
                          Download the presentation slides from this lesson
                        </p>
                        <a
                          href="#"
                          class="mt-1 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500"
                        >
                          Download PDF
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="ml-1 h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                            />
                          </svg>
                        </a>
                      </div>
                    </li>

                    <li class="flex items-start">
                      <div class="flex-shrink-0 h-6 w-6 mt-1">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6 text-indigo-600"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                          />
                        </svg>
                      </div>
                      <div class="ml-3">
                        <h4 class="text-base font-medium">Code Examples</h4>
                        <p class="text-sm text-gray-500">
                          Source code from the examples shown in this lesson
                        </p>
                        <a
                          href="#"
                          class="mt-1 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500"
                        >
                          View on GitHub
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="ml-1 h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                            />
                          </svg>
                        </a>
                      </div>
                    </li>

                    <li class="flex items-start">
                      <div class="flex-shrink-0 h-6 w-6 mt-1">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6 text-indigo-600"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                          />
                        </svg>
                      </div>
                      <div class="ml-3">
                        <h4 class="text-base font-medium">
                          Practice Exercises
                        </h4>
                        <p class="text-sm text-gray-500">
                          Additional exercises to reinforce your learning
                        </p>
                        <a
                          href="#"
                          class="mt-1 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500"
                        >
                          Download Exercises
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="ml-1 h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                            />
                          </svg>
                        </a>
                      </div>
                    </li>
                  </ul>
                </div>

                <!-- Notes tab -->
                <div v-else-if="activeTab === 'notes'">
                  <h3 class="text-lg font-medium mb-4">My Notes</h3>

                  <!-- Note editor -->
                  <div class="mb-6">
                    <textarea
                      v-model="noteContent"
                      rows="3"
                      placeholder="Add a note about this lesson..."
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <div class="mt-2 flex justify-end">
                      <button
                        :disabled="!noteContent.trim()"
                        :class="[
                          'px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                          noteContent.trim()
                            ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                            : 'bg-gray-300 text-gray-500 cursor-not-allowed',
                        ]"
                        @click="saveNote"
                      >
                        Save Note
                      </button>
                    </div>
                  </div>

                  <!-- Notes list -->
                  <div v-if="notes.length > 0" class="space-y-4">
                    <div
                      v-for="note in notes"
                      :key="note.id"
                      class="p-4 border rounded-lg bg-gray-50"
                    >
                      <div class="flex items-start justify-between">
                        <p class="text-sm text-gray-500">
                          {{ formatTimestamp(note.timestamp) }}
                        </p>
                        <button
                          class="text-gray-400 hover:text-red-500"
                          @click="deleteNote(note.id)"
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
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                          </svg>
                        </button>
                      </div>
                      <p class="mt-2 whitespace-pre-wrap">
                        {{ note.text }}
                      </p>
                    </div>
                  </div>

                  <!-- Empty state -->
                  <div v-else class="text-center py-8">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-12 w-12 text-gray-300 mx-auto mb-4"
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
                    <p class="text-gray-500">
                      You haven't added any notes for this lesson yet.
                    </p>
                  </div>
                </div>

                <!-- Discussions tab -->
                <div v-else-if="activeTab === 'discussions'">
                  <h3 class="text-lg font-medium mb-4">Lesson Discussions</h3>

                  <!-- Discussion form -->
                  <div class="mb-6 p-4 border rounded-lg">
                    <textarea
                      rows="3"
                      placeholder="Ask a question or share your thoughts..."
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <div class="mt-2 flex justify-end">
                      <button
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                      >
                        Post
                      </button>
                    </div>
                  </div>

                  <!-- Sample discussions -->
                  <div class="space-y-6">
                    <!-- Discussion thread 1 -->
                    <div class="p-4 border rounded-lg">
                      <div class="flex items-start">
                        <div class="flex-1">
                          <div class="flex justify-between">
                            <div>
                              <h4 class="font-medium">Alex Thompson</h4>
                              <p class="text-sm text-gray-500">2 days ago</p>
                            </div>
                            <button class="text-gray-400 hover:text-indigo-600">
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
                                  d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                                />
                              </svg>
                            </button>
                          </div>
                          <p class="mt-2">
                            Can someone explain the difference between inline
                            and block elements in more detail? The lesson
                            touched on it, but I'm still a bit confused.
                          </p>
                          <div class="mt-3 flex items-center space-x-4">
                            <button
                              class="flex items-center text-gray-500 hover:text-indigo-600"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M7 11l5-5m0 0l5 5m-5-5v12"
                                />
                              </svg>
                              Upvote (3)
                            </button>
                            <button
                              class="flex items-center text-gray-500 hover:text-indigo-600"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                              </svg>
                              Reply (2)
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Reply -->
                      <div class="ml-12 mt-4 pl-4 border-l-2 border-gray-200">
                        <div class="flex items-start">
                          <div class="flex-1">
                            <div class="flex justify-between">
                              <div>
                                <h4 class="font-medium">Sarah Johnson</h4>
                                <p class="text-sm text-gray-500">1 day ago</p>
                              </div>
                            </div>
                            <p class="mt-2">
                              Great question! The main difference is how they
                              interact with other elements in the document
                              flow...
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Discussion thread 2 -->
                    <div class="p-4 border rounded-lg">
                      <div class="flex items-start">
                        <div class="flex-1">
                          <div class="flex justify-between">
                            <div>
                              <h4 class="font-medium">Michael Chang</h4>
                              <p class="text-sm text-gray-500">5 days ago</p>
                            </div>
                            <button class="text-gray-400 hover:text-indigo-600">
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
                                  d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                                />
                              </svg>
                            </button>
                          </div>
                          <p class="mt-2">
                            I found a great resource on this topic! Check out
                            this article:
                            <a href="#" class="text-indigo-600"
                              >Understanding HTML Document Structure</a
                            >.
                          </p>
                          <div class="mt-3 flex items-center space-x-4">
                            <button
                              class="flex items-center text-gray-500 hover:text-indigo-600"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M7 11l5-5m0 0l5 5m-5-5v12"
                                />
                              </svg>
                              Upvote (5)
                            </button>
                            <button
                              class="flex items-center text-gray-500 hover:text-indigo-600"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                              </svg>
                              Reply (0)
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
