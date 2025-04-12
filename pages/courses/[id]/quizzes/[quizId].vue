<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- pages/courses/[id]/quizzes/[quizId].vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import CourseSidebar from "@/components/courses/CourseSidebar.vue";
import QuizComponent from "@/components/courses/QuizComponent.vue";

// Types
import type { Section } from "@/components/courses/CourseSidebar.vue";
import type {
  QuizQuestion,
  QuizResult,
} from "@/components/courses/QuizComponent.vue";

// Route params
const route = useRoute();
const router = useRouter();

const courseId = computed(() => route.params.id as string);
const quizId = computed(() => route.params.quizId as string);

// State
const isLoading = ref(true);
const isMobileSidebarOpen = ref(false);
const error = ref("");
const course = ref<any>(null);
const currentQuiz = ref<any>(null);
const quizQuestions = ref<QuizQuestion[]>([]);
const courseSections = ref<Section[]>([]);
const courseProgress = ref(0);
const quizResult = ref<QuizResult | null>(null);
const showCertificate = ref(false);

// Responsive state
const isSmallScreen = ref(false);

// Fetch course and quiz data
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

    // Mock quiz data
    currentQuiz.value = {
      id: quizId.value,
      title: "HTML Fundamentals Quiz",
      description:
        "Test your knowledge of HTML basics covering document structure, elements, and attributes.",
      timeLimit: 10, // minutes
      passingScore: 70, // percentage
      sectionId: "section-2",
      sectionTitle: "HTML Foundations",
    };

    // Mock quiz questions
    quizQuestions.value = [
      {
        id: "q1",
        question: "What does HTML stand for?",
        type: "single",
        options: [
          { id: "q1-a", text: "Hyper Text Markup Language", isCorrect: true },
          {
            id: "q1-b",
            text: "Hyperlinks and Text Markup Language",
            isCorrect: false,
          },
          { id: "q1-c", text: "Home Tool Markup Language", isCorrect: false },
          {
            id: "q1-d",
            text: "Hyper Technical Modern Language",
            isCorrect: false,
          },
        ],
        points: 10,
        explanation:
          "HTML stands for Hyper Text Markup Language. It is the standard markup language for creating web pages and web applications.",
      },
      {
        id: "q2",
        question: "Which HTML element is used to define the document title?",
        type: "single",
        options: [
          { id: "q2-a", text: "<header>", isCorrect: false },
          { id: "q2-b", text: "<heading>", isCorrect: false },
          { id: "q2-c", text: "<title>", isCorrect: true },
          { id: "q2-d", text: "<meta>", isCorrect: false },
        ],
        points: 10,
      },
      {
        id: "q3",
        question:
          "Which of the following are valid HTML5 semantic elements? (Select all that apply)",
        type: "multiple",
        options: [
          { id: "q3-a", text: "<article>", isCorrect: true },
          { id: "q3-b", text: "<section>", isCorrect: true },
          { id: "q3-c", text: "<container>", isCorrect: false },
          { id: "q3-d", text: "<aside>", isCorrect: true },
          { id: "q3-e", text: "<content>", isCorrect: false },
        ],
        points: 15,
        explanation:
          "HTML5 introduced several semantic elements including <article>, <section>, <nav>, <header>, <footer>, <aside>, <main>, and <figure> among others. <container> and <content> are not valid HTML5 elements.",
      },
      {
        id: "q4",
        question:
          "In HTML, which attribute is used to specify an alternate text for an image?",
        type: "single",
        options: [
          { id: "q4-a", text: "title", isCorrect: false },
          { id: "q4-b", text: "src", isCorrect: false },
          { id: "q4-c", text: "alt", isCorrect: true },
          { id: "q4-d", text: "description", isCorrect: false },
        ],
        points: 10,
      },
      {
        id: "q5",
        question: "Is it necessary to close all HTML tags?",
        type: "true-false",
        correctAnswer: "false",
        points: 10,
        explanation:
          "In HTML, not all tags need to be closed. Void elements such as <img>, <input>, and <br> do not require closing tags.",
      },
      {
        id: "q6",
        question: "What is the correct HTML for creating a hyperlink?",
        type: "single",
        options: [
          {
            id: "q6-a",
            text: '<a url="http://example.com">Example</a>',
            isCorrect: false,
          },
          {
            id: "q6-b",
            text: '<a href="http://example.com">Example</a>',
            isCorrect: true,
          },
          {
            id: "q6-c",
            text: '<link href="http://example.com">Example</link>',
            isCorrect: false,
          },
          {
            id: "q6-d",
            text: "<hyperlink>http://example.com</hyperlink>",
            isCorrect: false,
          },
        ],
        points: 10,
      },
      {
        id: "q7",
        question:
          "What is the correct HTML element for inserting a line break?",
        type: "single",
        options: [
          { id: "q7-a", text: "<break>", isCorrect: false },
          { id: "q7-b", text: "<lb>", isCorrect: false },
          { id: "q7-c", text: "<newline>", isCorrect: false },
          { id: "q7-d", text: "<br>", isCorrect: true },
        ],
        points: 10,
      },
      {
        id: "q8",
        question: "Which doctype declaration is correct for HTML5?",
        type: "single",
        options: [
          { id: "q8-a", text: "<!DOCTYPE html>", isCorrect: true },
          { id: "q8-b", text: "<!DOCTYPE HTML5>", isCorrect: false },
          { id: "q8-c", text: "<DOCTYPE html>", isCorrect: false },
          {
            id: "q8-d",
            text: '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0//EN">',
            isCorrect: false,
          },
        ],
        points: 10,
      },
      {
        id: "q9",
        question:
          "Which of these elements are typically block-level elements? (Select all that apply)",
        type: "multiple",
        options: [
          { id: "q9-a", text: "<div>", isCorrect: true },
          { id: "q9-b", text: "<span>", isCorrect: false },
          { id: "q9-c", text: "<p>", isCorrect: true },
          { id: "q9-d", text: "<h1>", isCorrect: true },
          { id: "q9-e", text: "<a>", isCorrect: false },
        ],
        points: 15,
      },
    ];

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
            isCompleted: true,
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

    isLoading.value = false;
  } catch (err) {
    console.error("Error fetching course data:", err);
    error.value = "Failed to load quiz data. Please try again.";
    isLoading.value = false;
  }
};

// Handle quiz completion
const handleQuizComplete = (result: QuizResult) => {
  quizResult.value = result;

  if (result.passed) {
    // Mark quiz as completed
    courseSections.value.forEach((section) => {
      section.lessons.forEach((lesson) => {
        if (lesson.id === quizId.value) {
          lesson.isCompleted = true;
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

    // Check if course is complete
    if (courseProgress.value === 100) {
      showCertificate.value = true;
    }
  }
};

// Handle quiz retry
const handleQuizRetry = () => {
  quizResult.value = null;
};

// Navigate to a lesson
const navigateToLesson = (lessonId: string) => {
  // Check if destination is a quiz or regular lesson
  let isQuiz = false;

  courseSections.value.forEach((section) => {
    section.lessons.forEach((lesson) => {
      if (lesson.id === lessonId && lesson.type === "quiz") {
        isQuiz = true;
      }
    });
  });

  if (isQuiz) {
    router.push(`/courses/${courseId.value}/quizzes/${lessonId}`);
  } else {
    router.push(`/courses/${courseId.value}/lessons/${lessonId}`);
  }

  isMobileSidebarOpen.value = false;
};

// Continue to next lesson after quiz
// const continueToNextLesson = () => {
//   // Find the next lesson
//   let foundCurrentLesson = false;
//   let nextLessonId = "";
//   let nextSectionId = "";

//   for (const section of courseSections.value) {
//     for (const lesson of section.lessons) {
//       if (foundCurrentLesson) {
//         nextLessonId = lesson.id;
//         nextSectionId = section.id;
//         break;
//       }

//       if (lesson.id === quizId.value) {
//         foundCurrentLesson = true;
//       }
//     }

//     if (nextLessonId) break;
//   }

//   if (nextLessonId) {
//     navigateToLesson(nextLessonId, nextSectionId);
//   } else {
//     // No next lesson, show certificate
//     router.push(`/courses/${courseId.value}/certificate`);
//   }
// };

// Toggle mobile sidebar
const toggleMobileSidebar = (isOpen: boolean) => {
  isMobileSidebarOpen.value = isOpen;
};

// Check screen size
const checkScreenSize = () => {
  isSmallScreen.value = window.innerWidth < 768;
};

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

<template>
  <div class="quiz-page bg-gray-100 min-h-screen">
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
        :current-lesson-id="quizId"
        :current-section-id="currentQuiz?.sectionId"
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

        <!-- Quiz content -->
        <div v-else-if="currentQuiz && quizQuestions.length" class="pb-12">
          <!-- Course and quiz info -->
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
                  <span>{{ currentQuiz.sectionTitle }}</span>
                </div>
              </div>

              <!-- Quiz title -->
              <h1 class="text-2xl font-bold mt-2">
                {{ currentQuiz.title }}
              </h1>
            </div>
          </div>

          <div class="container mx-auto px-4 py-6">
            <!-- Quiz component -->
            <QuizComponent
              :quiz-id="quizId"
              :title="currentQuiz.title"
              :description="currentQuiz.description"
              :time-limit="currentQuiz.timeLimit"
              :passing-score="currentQuiz.passingScore"
              :questions="quizQuestions"
              :shuffle-questions="true"
              :shuffle-options="true"
              :show-correct-answers="true"
              :show-explanations="true"
              :allow-retry="true"
              @complete="handleQuizComplete"
              @retry="handleQuizRetry"
            />

            <!-- Certificate modal -->
            <div
              v-if="showCertificate"
              class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            >
              <div class="bg-white rounded-lg p-6 max-w-lg w-full">
                <div class="text-center">
                  <div
                    class="inline-block p-3 rounded-full bg-green-100 text-green-600 mb-4"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-10 w-10"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                  <h2 class="text-2xl font-bold mb-2">Congratulations!</h2>
                  <p class="text-gray-600 mb-6">
                    You've completed the entire course! Your certificate is now
                    available.
                  </p>
                  <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button
                      class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                      @click="showCertificate = false"
                    >
                      Continue Learning
                    </button>
                    <button
                      class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                      @click="router.push(`/courses/${courseId}/certificate`)"
                    >
                      View Certificate
                    </button>
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

<style scoped>
/* Custom scrollbar for content */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
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
