<!-- pages/courses/[id]/learn/[lessonId].vue -->
<script setup lang="ts">
import type {
  Course,
  CourseLesson,
  CourseSection,
  Note,
} from "~/types/courseTemp";

// Types

// Route params
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));
const lessonId = computed(() => Number(route.params.lessonId));

// State
const course = ref<Course | null>(null);
const currentLesson = ref<CourseLesson | null>(null);
const currentSection = ref<CourseSection | null>(null);
const loading = ref(true);
const error = ref(false);
const isSidebarOpen = ref(true);
const watchProgress = ref(0); // Track video progress percentage
const isLessonCompleted = ref(false);
const activeTab = ref<"content" | "resources" | "notes" | "discussions">(
  "content"
);
const courseProgress = ref(0);
const isNotesPanelOpen = ref(false);

// Notes functionality
const notes = ref<Note[]>([]);
const noteContent = ref("");
const notesSaved = ref(false);

// Responsive state
const isSmallScreen = ref(false);

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
      instructor: {
        name: "Sarah Johnson",
        avatar: "/images/instructors/i2.jpg",
        title: "Senior Web Developer",
        bio: "15+ years of experience in web development. Worked at Google and Microsoft.",
      },
      sections: [
        {
          id: 1,
          title: "Introduction to Web Development",
          description:
            "Get started with the fundamentals of web development and set up your environment.",
          order: 1,
          lessons: [
            {
              id: 101,
              title: "Course Overview and Setup",
              description:
                "Introduction to the course structure and setup of your development environment.",
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
              description:
                "Understanding the fundamental concepts of how the web works.",
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
              description:
                "Step-by-step guide to setting up your development tools and environment.",
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
              description:
                "Essential tools and resources for effective web development.",
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
              description:
                "Test your understanding of web development fundamentals.",
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
          description:
            "Learn the core HTML concepts to structure web pages effectively.",
          order: 2,
          lessons: [
            {
              id: 201,
              title: "HTML Basics and Document Structure",
              description:
                "Learn the fundamental structure of HTML documents and semantic elements.",
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
              description:
                "Format and structure text content using HTML elements.",
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
              description:
                "Add links, images, and multimedia content to your web pages.",
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
              description:
                "Create interactive forms with various input types and validation.",
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
              description:
                "Organize tabular data and use semantic HTML elements for better accessibility.",
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
              description:
                "Apply your HTML knowledge to build a complete webpage from scratch.",
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
              description:
                "Test your understanding of HTML concepts and best practices.",
              duration: "30m",
              content: "This would be a quiz component in a real application.",
              type: "quiz",
              isCompleted: false,
              order: 7,
            },
          ],
        },
        {
          id: 3,
          title: "CSS Fundamentals",
          description:
            "Master the basics of CSS to style and layout web pages.",
          order: 3,
          lessons: [
            {
              id: 301,
              title: "Introduction to CSS",
              description:
                "Learn the basics of CSS including selectors, properties, and values.",
              duration: "40m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 1,
            },
            {
              id: 302,
              title: "CSS Box Model",
              description:
                "Understand the CSS box model and how it affects layout.",
              duration: "35m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 2,
            },
          ],
        },
      ],
    };

    // Find current lesson and section
    findCurrentLessonAndSection();

    // Load saved notes
    loadNotes();

    // Calculate course progress
    calculateCourseProgress();

    // Check screen size for responsive design
    checkScreenSize();
  } catch (err) {
    console.error("Error fetching course data:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

// Find the current lesson and section based on route params
const findCurrentLessonAndSection = () => {
  if (!course.value) return;

  for (const section of course.value.sections) {
    for (const lesson of section.lessons) {
      if (lesson.id === lessonId.value) {
        currentLesson.value = lesson;
        currentSection.value = section;
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

  // Close mobile sidebar on small screens
  if (isSmallScreen.value) {
    toggleSidebar(false);
  }
};

// Toggle sidebar with optional force parameter
const toggleSidebar = (forcedState?: boolean) => {
  if (typeof forcedState === "boolean") {
    isSidebarOpen.value = forcedState;
  } else {
    isSidebarOpen.value = !isSidebarOpen.value;
  }
};

// Toggle notes panel
const toggleNotesPanel = (forcedState?: boolean) => {
  if (typeof forcedState === "boolean") {
    isNotesPanelOpen.value = forcedState;
  } else {
    isNotesPanelOpen.value = !isNotesPanelOpen.value;
  }
};

// Calculate course progress
const calculateCourseProgress = () => {
  if (!course.value) return;

  let totalLessons = 0;
  let completedLessons = 0;

  course.value.sections.forEach((section) => {
    section.lessons.forEach((lesson) => {
      totalLessons++;
      if (lesson.isCompleted) {
        completedLessons++;
      }
    });
  });

  courseProgress.value =
    totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0;
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

// Format timestamp
// const formatTimestamp = (timestamp: number): string => {
//   return new Date(timestamp).toLocaleString();
// };

// Mark lesson as complete
const markLessonComplete = () => {
  if (!currentLesson.value) return;

  // In a real app, this would make an API call
  currentLesson.value.isCompleted = true;
  isLessonCompleted.value = true;

  // Recalculate course progress
  calculateCourseProgress();

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

// Check screen size for responsive design
const checkScreenSize = () => {
  isSmallScreen.value = window.innerWidth < 768;

  // Adjust sidebar visibility based on screen size
  isSidebarOpen.value = !isSmallScreen.value;
};

// Watch for route changes
watch(
  () => route.params.lessonId,
  (newId, oldId) => {
    if (newId !== oldId) {
      fetchCourseData();
      // Close notes panel on lesson change
      isNotesPanelOpen.value = false;
    }
  }
);

// Lifecycle hooks
onMounted(() => {
  fetchCourseData();
  window.addEventListener("resize", checkScreenSize);
  window.addEventListener("beforeunload", saveProgress);
});

onBeforeUnmount(() => {
  saveProgress();
  window.removeEventListener("beforeunload", saveProgress);
  window.removeEventListener("resize", checkScreenSize);
});
</script>

<template>
  <div class="bg-white text-gray-900 min-h-screen flex flex-col">
    <!-- Fixed mobile sidebar toggle button -->
    <button
      v-if="isSmallScreen && !isSidebarOpen"
      class="fixed bottom-4 left-4 z-30 p-3 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none"
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
    </button>

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
          <span class="text-lg font-medium truncate max-w-md hidden md:inline">
            {{ course?.title }}
          </span>
        </div>

        <div class="flex items-center space-x-2 md:space-x-4">
          <!-- Course progress -->
          <div class="hidden md:flex items-center text-sm text-gray-600">
            <span class="mr-2">{{ courseProgress }}% complete</span>
            <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
              <div
                class="h-full bg-green-500 transition-all duration-300"
                :style="{ width: `${courseProgress}%` }"
              />
            </div>
          </div>

          <!-- Notes button -->
          <button
            class="text-gray-600 hover:text-indigo-600 transition-colors p-2 relative"
            :class="{
              'bg-indigo-50 text-indigo-600 rounded-lg': isNotesPanelOpen,
            }"
            aria-label="Toggle notes"
            @click="toggleNotesPanel()"
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
            <span
              v-if="notes.length > 0"
              class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-xs bg-indigo-600 text-white rounded-full"
            >
              {{ notes.length }}
            </span>
          </button>

          <!-- Toggle sidebar button -->
          <button
            class="text-gray-600 hover:text-indigo-600 transition-colors p-2"
            :class="{
              'bg-indigo-50 text-indigo-600 rounded-lg': isSidebarOpen,
            }"
            aria-label="Toggle course content"
            @click="toggleSidebar()"
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
        </div>
      </div>
    </header>

    <!-- Loading state -->
    <div v-if="loading" class="flex-grow flex items-center justify-center">
      <div class="flex flex-col items-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mb-4"
        />
        <p class="text-gray-600">Loading lesson...</p>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="flex-grow flex items-center justify-center">
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
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          @click="fetchCourseData"
        >
          Retry
        </button>
      </div>
    </div>

    <!-- Main content with sidebar -->
    <div v-else class="flex-grow flex overflow-hidden">
      <!-- Course sidebar component -->
      <CoursesLearnCourseSidebar
        v-show="isSidebarOpen"
        :course="course"
        :current-lesson="currentLesson"
        :course-progress="courseProgress"
        :is-small-screen="isSmallScreen"
        @navigate="navigateToLesson"
        @close="toggleSidebar(false)"
      />

      <!-- Overlay for mobile sidebar -->
      <div
        v-if="isSmallScreen && isSidebarOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-20"
        @click="toggleSidebar(false)"
      />

      <!-- Main lesson content area -->
      <div class="flex-grow flex flex-col overflow-hidden relative bg-gray-50">
        <!-- Lesson content -->
        <div class="flex-grow overflow-y-auto">
          <div class="container mx-auto px-4 py-6 max-w-4xl">
            <!-- Lesson title and info -->
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6"
            >
              <div class="flex flex-wrap items-center justify-between">
                <!-- Breadcrumb -->
                <div
                  class="flex items-center text-sm text-gray-500 mb-2 sm:mb-0"
                >
                  <span>{{ currentSection?.title }}</span>
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
                  <span>{{ currentLesson?.title }}</span>
                </div>

                <!-- Completed badge -->
                <div
                  v-if="isLessonCompleted"
                  class="flex items-center text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full"
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

              <h1 class="text-2xl md:text-3xl font-bold mt-3">
                {{ currentLesson?.title }}
              </h1>

              <!-- Lesson metadata -->
              <div
                class="flex flex-wrap items-center mt-3 text-sm text-gray-600"
              >
                <div class="flex items-center mr-4">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ currentLesson?.duration }}
                </div>
                <div class="flex items-center capitalize">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                    />
                  </svg>
                  {{ currentLesson?.type }}
                </div>
              </div>

              <!-- Lesson description -->
              <p v-if="currentLesson?.description" class="mt-3 text-gray-700">
                {{ currentLesson.description }}
              </p>
            </div>

            <!-- Lesson content based on type -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
              <!-- Video player -->
              <div
                v-if="
                  currentLesson?.type === 'video' && currentLesson?.videoUrl
                "
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
                v-else-if="
                  currentLesson?.type === 'quiz' && currentLesson?.content
                "
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
              :instructor="course?.instructor"
              @update:active-tab="activeTab = $event"
            />

            <!-- Navigation buttons -->
            <CoursesLearnLessonNavigation
              :previous-lesson="previousLesson"
              :next-lesson="nextLesson"
              :is-completed="isLessonCompleted"
              @navigate="navigateToLesson"
              @complete="markLessonComplete"
            />
          </div>
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
