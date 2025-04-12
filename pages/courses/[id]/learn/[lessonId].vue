<!-- pages/courses/[id]/learn/[lessonId].vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

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
  description?: string;
}

interface CourseSection {
  id: number;
  title: string;
  description?: string;
  lessons: CourseLesson[];
  order: number;
}

interface Course {
  id: number;
  title: string;
  sections: CourseSection[];
  instructor?: {
    id?: string;
    name: string;
    avatar: string;
    title: string;
    bio: string;
  };
}

interface Note {
  id: string;
  text: string;
  timestamp: number;
  lessonId: number | string;
}

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
const isSidebarOpen = ref(false);
const watchProgress = ref(0); // Track video progress percentage
const isLessonCompleted = ref(false);
const activeTab = ref<"content" | "resources" | "notes" | "discussions">(
  "content"
);
const courseProgress = ref(0);

// Notes functionality
const notes = ref<Note[]>([]);
const noteContent = ref("");
const notesSaved = ref(false);

// Responsive state
const isSmallScreen = ref(false);

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
      instructor: {
        name: "Sarah Johnson",
        avatar: "/api/placeholder/64/64",
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

  // Close mobile sidebar if open
  if (isSmallScreen.value && isSidebarOpen.value) {
    isSidebarOpen.value = false;
  }
};

// Toggle sidebar
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
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

// Notes functionality
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
const formatTimestamp = (timestamp: number): string => {
  return new Date(timestamp).toLocaleString();
};

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

  // Automatically close sidebar on small screens
  if (isSmallScreen.value) {
    isSidebarOpen.value = false;
  } else {
    isSidebarOpen.value = true;
  }
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

// Watch for route changes
watch(
  () => route.params.lessonId,
  (newId, oldId) => {
    if (newId !== oldId) {
      fetchCourseData();
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
  document.removeEventListener("mousemove", resize);
  document.removeEventListener("mouseup", stopResize);
});
</script>

<template>
  <div class="bg-white text-gray-900 min-h-screen flex flex-col">
    <!-- Fixed mobile sidebar toggle button -->
    <button
      v-if="isSmallScreen && !isSidebarOpen"
      class="fixed bottom-4 left-4 z-30 p-3 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none"
      aria-label="Open course menu"
      @click="toggleSidebar"
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
          <span
            class="text-lg font-medium truncate max-w-md hidden md:inline"
            >{{ course?.title }}</span
          >
        </div>

        <div class="flex items-center space-x-2 md:space-x-4">
          <!-- Course progress -->
          <div class="hidden md:flex items-center text-sm text-gray-600">
            <span class="mr-2">{{ courseProgress }}% complete</span>
            <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
              <div
                class="h-full bg-green-500"
                :style="{ width: `${courseProgress}%` }"
              />
            </div>
          </div>

          <!-- Toggle sidebar button -->
          <button
            class="text-gray-600 hover:text-indigo-600 transition-colors p-2"
            :class="{ 'bg-gray-100 rounded-lg': isSidebarOpen }"
            aria-label="Toggle course content"
            @click="toggleSidebar"
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
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      />
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
      <!-- Sidebar (course contents) -->
      <div
        v-show="isSidebarOpen"
        class="bg-white border-r border-gray-200 overflow-y-auto flex-shrink-0 transition-all duration-300 fixed md:relative inset-0 z-30 md:z-auto"
        :class="{ 'md:w-auto': isSmallScreen }"
        :style="{ width: isSmallScreen ? '85%' : `${sidebarWidth}px` }"
      >
        <!-- Sidebar header with close button for mobile -->
        <div
          class="p-4 border-b border-gray-200 sticky top-0 bg-white z-10 flex justify-between items-center"
        >
          <h2 class="text-lg font-bold">Course Content</h2>
          <button
            v-if="isSmallScreen"
            class="text-gray-500 hover:text-gray-700"
            aria-label="Close menu"
            @click="toggleSidebar"
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
          <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div
              class="h-full bg-green-500 transition-all duration-300"
              :style="{ width: `${courseProgress}%` }"
            />
          </div>
        </div>

        <!-- Sections and lessons -->
        <div class="p-2">
          <div
            v-for="section in course?.sections"
            :key="section.id"
            class="mb-4"
          >
            <!-- Section header -->
            <div class="px-3 py-2 bg-gray-100 rounded-lg mb-2">
              <h3 class="font-medium">{{ section.title }}</h3>
              <p v-if="section.description" class="text-xs text-gray-600 mt-1">
                {{ section.description }}
              </p>
            </div>

            <!-- Lessons list -->
            <div class="space-y-1 ml-2">
              <button
                v-for="lesson in section.lessons"
                :key="lesson.id"
                class="w-full px-3 py-2 rounded-lg flex items-center text-left transition-colors"
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
                        lesson.type.charAt(0).toUpperCase() +
                        lesson.type.slice(1)
                      }}
                      • {{ lesson.duration }}
                    </span>
                  </div>

                  <!-- Lesson description preview (if available) -->
                  <p
                    v-if="lesson.description"
                    class="text-xs mt-1 truncate opacity-80"
                    :class="
                      lesson.id === currentLesson?.id
                        ? 'text-indigo-100'
                        : 'text-gray-500'
                    "
                  >
                    {{ lesson.description }}
                  </p>
                </div>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar resize handle (desktop only) -->
        <div
          v-if="!isSmallScreen"
          class="absolute top-0 right-0 bottom-0 w-1 cursor-ew-resize bg-gray-200 hover:bg-indigo-500 transition-colors"
          @mousedown="startResize"
        />
      </div>

      <!-- Overlay for mobile sidebar -->
      <div
        v-if="isSmallScreen && isSidebarOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-20"
        @click="toggleSidebar"
      />

      <!-- Main lesson content area -->
      <div class="flex-grow flex flex-col overflow-hidden relative bg-gray-50">
        <!-- Lesson content -->
        <div class="flex-grow overflow-y-auto">
          <div class="container mx-auto px-4 py-6 max-w-4xl">
            <!-- Lesson title and info -->
            <div
              class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6"
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

              <h1 class="text-2xl font-bold mt-2">
                {{ currentLesson?.title }}
              </h1>

              <!-- Lesson metadata -->
              <div
                class="flex flex-wrap items-center mt-2 text-sm text-gray-600"
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
              <p v-if="currentLesson?.description" class="mt-2 text-gray-700">
                {{ currentLesson.description }}
              </p>
            </div>

            <!-- Video player for video lessons -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
              <div
                v-if="
                  currentLesson?.type === 'video' && currentLesson?.videoUrl
                "
              >
                <video
                  :src="currentLesson.videoUrl"
                  :poster="'/api/placeholder/1280/720'"
                  controls
                  class="w-full"
                  @timeupdate="(e) => handleVideoProgress(e.target.currentTime)"
                  @ended="handleVideoEnded"
                />

                <!-- Video progress -->
                <div class="p-3 border-t border-gray-100">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span>{{ watchProgress }}%</span>
                  </div>
                  <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div
                      class="h-full bg-indigo-600 transition-all duration-300"
                      :style="{ width: `${watchProgress}%` }"
                    />
                  </div>
                </div>
              </div>

              <!-- Quiz content -->
              <div
                v-else-if="
                  currentLesson?.type === 'quiz' && currentLesson?.content
                "
                class="p-6"
              >
                <h2 class="text-xl font-bold mb-4">
                  Quiz: {{ currentLesson.title }}
                </h2>
                <p class="mb-6">{{ currentLesson.content }}</p>

                <!-- Placeholder for quiz component -->
                <div class="bg-gray-100 p-6 rounded-lg text-center">
                  <p class="mb-4">
                    This is a placeholder for the quiz component.
                  </p>
                  <p class="text-gray-500 text-sm mb-6">
                    In a real application, this would be replaced with an
                    interactive quiz.
                  </p>

                  <button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                    @click="markLessonComplete"
                  >
                    Complete Quiz
                  </button>
                </div>
              </div>

              <!-- Assignment content -->
              <div
                v-else-if="
                  currentLesson?.type === 'assignment' && currentLesson?.content
                "
                class="p-6"
              >
                <h2 class="text-xl font-bold mb-4">
                  Assignment: {{ currentLesson.title }}
                </h2>
                <p class="mb-6">{{ currentLesson.content }}</p>

                <!-- Placeholder for assignment component -->
                <div class="bg-gray-100 p-6 rounded-lg text-center">
                  <p class="mb-4">
                    This is a placeholder for the assignment component.
                  </p>
                  <p class="text-gray-500 text-sm mb-6">
                    In a real application, this would include assignment
                    details, submission form, etc.
                  </p>

                  <button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                    @click="markLessonComplete"
                  >
                    Submit Assignment
                  </button>
                </div>
              </div>
            </div>

            <!-- Tabbed content area -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
              <!-- Tab navigation -->
              <div class="border-b">
                <div class="flex overflow-x-auto">
                  <button
                    class="px-4 py-3 text-sm font-medium"
                    :class="
                      activeTab === 'content'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700'
                    "
                    @click="activeTab = 'content'"
                  >
                    Lesson Content
                  </button>
                  <button
                    class="px-4 py-3 text-sm font-medium"
                    :class="
                      activeTab === 'resources'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700'
                    "
                    @click="activeTab = 'resources'"
                  >
                    Resources
                  </button>
                  <button
                    class="px-4 py-3 text-sm font-medium"
                    :class="
                      activeTab === 'notes'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700'
                    "
                    @click="activeTab = 'notes'"
                  >
                    My Notes
                  </button>
                  <button
                    class="px-4 py-3 text-sm font-medium"
                    :class="
                      activeTab === 'discussions'
                        ? 'border-b-2 border-indigo-600 text-indigo-600'
                        : 'text-gray-500 hover:text-gray-700'
                    "
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
                    <div class="mt-2 flex justify-between items-center">
                      <span v-if="notesSaved" class="text-green-600 text-sm"
                        >Note saved!</span
                      >
                      <div />
                      <!-- Empty div for flex spacing -->
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
                        <img
                          src="/images/courses/c2.jpg"
                          alt="User avatar"
                          class="w-10 h-10 rounded-full mr-4"
                        />
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
                      <div class="ml-14 mt-4 pl-4 border-l-2 border-gray-200">
                        <div class="flex items-start">
                          <img
                            src="/images/courses/c1.jpg"
                            alt="Instructor avatar"
                            class="w-10 h-10 rounded-full mr-4"
                          />
                          <div class="flex-1">
                            <div class="flex justify-between">
                              <div>
                                <h4 class="font-medium">Sarah Johnson</h4>
                                <div
                                  class="text-xs text-white bg-indigo-600 rounded-full px-2 py-0.5 inline-block"
                                >
                                  Instructor
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                  1 day ago
                                </p>
                              </div>
                            </div>
                            <p class="mt-2">
                              Great question! The main difference is how they
                              interact with other elements in the document flow.
                              Block elements always start on a new line and take
                              up the full width available, while inline elements
                              only take up as much width as necessary and don't
                              force a new line. Examples of block elements
                              include div, p, and h1-h6. Inline elements include
                              span, a, and strong.
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigation buttons -->
            <div class="flex justify-between mb-8">
              <button
                v-if="previousLesson"
                class="flex items-center bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg transition-colors shadow-sm"
                @click="navigateToLesson(previousLesson)"
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
              <div v-else />

              <div class="flex space-x-3">
                <button
                  v-if="!isLessonCompleted"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm"
                  @click="markLessonComplete"
                >
                  Mark as Complete
                </button>

                <button
                  v-else-if="nextLesson"
                  class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm"
                  @click="navigateToLesson(nextLesson)"
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

/* Mobile sidebar transition */
.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}

/* Tab content transition */
.tab-content-enter-active,
.tab-content-leave-active {
  transition: opacity 0.3s ease;
}

.tab-content-enter-from,
.tab-content-leave-to {
  opacity: 0;
}
</style>
