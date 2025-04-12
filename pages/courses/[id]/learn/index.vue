<!-- pages/courses/[id]/learn/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

// Types
interface CourseLesson {
  id: number;
  title: string;
  duration: string;
  description: string;
  videoUrl?: string;
  content?: string;
  type: "video" | "quiz" | "assignment" | "resource";
  isCompleted?: boolean;
  order: number;
  learningObjectives?: string[];
  requirements?: string[];
}

interface CourseSection {
  id: number;
  title: string;
  description: string;
  lessons: CourseLesson[];
  order: number;
  estimatedCompletionTime?: string;
}

interface Course {
  id: number;
  title: string;
  description: string;
  instructor: {
    name: string;
    avatar: string;
    title: string;
    bio: string;
  };
  sections: CourseSection[];
  level: string;
  category: string;
  tags: string[];
  totalDuration: string;
  rating: number;
  studentsCount: number;
  updatedAt: string;
  objectives: string[];
  requirements: string[];
  progress?: {
    completed: number;
    total: number;
    percentage: number;
    lastLesson?: {
      id: number;
      title: string;
    };
  };
}

// Route params
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));

// State
const course = ref<Course | null>(null);
const loading = ref(true);
const error = ref(false);
const activeTab = ref<"overview" | "curriculum" | "instructor" | "reviews">(
  "curriculum"
);
const expandedSections = ref<number[]>([]);
const selectedSection = ref<number | null>(null);

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
      description:
        "Learn web development from scratch and build real-world projects with the latest technologies including HTML, CSS, JavaScript, React, Node.js, and more.",
      instructor: {
        name: "Sarah Johnson",
        avatar: "/images/instructors/i2.jpg",
        title: "Senior Web Developer & Educator",
        bio: "Sarah has over 12 years of experience in web development, working with Fortune 500 companies and tech startups. She's passionate about teaching and has helped more than 25,000 students master web development skills.",
      },
      level: "Beginner to Intermediate",
      category: "Web Development",
      tags: ["HTML", "CSS", "JavaScript", "React", "Node.js", "MongoDB"],
      totalDuration: "42h 15m",
      rating: 4.8,
      studentsCount: 15432,
      updatedAt: "2023-09-15",
      objectives: [
        "Build responsive and dynamic websites from scratch",
        "Understand front-end and back-end web development principles",
        "Create full-stack applications with modern JavaScript frameworks",
        "Deploy web applications to production environments",
        "Implement authentication, database storage, and API integrations",
      ],
      requirements: [
        "Basic computer skills and familiarity with using the internet",
        "No prior programming experience required - we'll start from the basics",
        "A computer with internet access (Windows, Mac, or Linux)",
        "Enthusiasm and willingness to practice what you learn",
      ],
      sections: [
        {
          id: 1,
          title: "Introduction to Web Development",
          description:
            "Get started with the fundamentals of web development and set up your development environment.",
          estimatedCompletionTime: "3 hours",
          order: 1,
          lessons: [
            {
              id: 101,
              title: "Course Overview and Setup",
              description:
                "Introduction to the course structure, learning pathway, and how to set up your development environment for optimal learning.",
              duration: "15m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: true,
              order: 1,
              learningObjectives: [
                "Understand the course structure and learning path",
                "Set up your development environment with VS Code",
                "Learn how to use course resources effectively",
              ],
              requirements: [],
            },
            {
              id: 102,
              title: "How the Web Works: A High-Level Overview",
              description:
                "A comprehensive overview of how the internet works, including browsers, servers, HTTP protocols, and the client-server architecture.",
              duration: "25m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 2,
              learningObjectives: [
                "Understand the client-server model",
                "Learn how HTTP requests and responses work",
                "Comprehend the role of DNS in web navigation",
              ],
              requirements: [],
            },
            {
              id: 103,
              title: "Setting Up Your Development Environment",
              description:
                "A step-by-step guide to installing and configuring all the necessary tools for web development, including code editors, browsers, and developer tools.",
              duration: "30m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 3,
              learningObjectives: [
                "Install and configure VS Code for web development",
                "Set up browser developer tools",
                "Install essential VS Code extensions for web development",
              ],
              requirements: [],
            },
            {
              id: 104,
              title: "Web Developer Tools and Resources",
              description:
                "Introduction to essential developer tools, documentation websites, and resources that will help you throughout your web development journey.",
              duration: "45m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 4,
              learningObjectives: [
                "Master Chrome DevTools for debugging",
                "Discover key web development resources",
                "Learn how to read and use documentation effectively",
              ],
              requirements: [],
            },
            {
              id: 105,
              title: "Introduction Quiz",
              description:
                "Test your understanding of the key concepts covered in the introduction section with this comprehensive quiz.",
              duration: "20m",
              content: "This would be a quiz component in a real application.",
              type: "quiz",
              isCompleted: false,
              order: 5,
              learningObjectives: [
                "Validate your understanding of basic web concepts",
                "Test your knowledge of the development environment setup",
                "Ensure you're ready to move to the next section",
              ],
              requirements: ["Complete all previous lessons in this section"],
            },
          ],
        },
        {
          id: 2,
          title: "HTML Fundamentals",
          description:
            "Learn the core HTML concepts to structure web pages effectively and create semantic, accessible content.",
          estimatedCompletionTime: "6 hours",
          order: 2,
          lessons: [
            {
              id: 201,
              title: "HTML Basics and Document Structure",
              description:
                "Learn the fundamental structure of HTML documents, including doctype declarations, head and body elements, and semantic page structure.",
              duration: "40m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 1,
              learningObjectives: [
                "Understand HTML document structure",
                "Create properly structured HTML pages",
                "Learn about HTML5 semantic elements",
              ],
              requirements: ["Complete the Introduction section"],
            },
            {
              id: 202,
              title: "Working with Text Elements",
              description:
                "Master HTML text formatting elements to properly structure and style your content for better readability and SEO.",
              duration: "35m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 2,
              learningObjectives: [
                "Use heading tags appropriately",
                "Structure content with paragraphs and lists",
                "Format text with semantic HTML elements",
              ],
              requirements: [],
            },
            {
              id: 203,
              title: "Links, Images, and Multimedia",
              description:
                "Learn how to incorporate links, images, audio, and video into your HTML documents with proper attributes and best practices.",
              duration: "50m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 3,
              learningObjectives: [
                "Create links between pages and sections",
                "Add responsive images with appropriate attributes",
                "Embed video and audio content",
              ],
              requirements: [],
            },
            {
              id: 204,
              title: "HTML Forms and Input Elements",
              description:
                "Comprehensive guide to creating forms for user input, including different input types, validation, and form submission methods.",
              duration: "55m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 4,
              learningObjectives: [
                "Structure forms with proper elements",
                "Use various input types for different data",
                "Implement basic client-side validation",
              ],
              requirements: [],
            },
            {
              id: 205,
              title: "Tables and Semantic HTML",
              description:
                "Learn when and how to use tables for tabular data, and dive deeper into semantic HTML elements for better page structure.",
              duration: "45m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 5,
              learningObjectives: [
                "Create properly structured tables",
                "Apply semantic HTML for page organization",
                "Improve accessibility with ARIA attributes",
              ],
              requirements: [],
            },
            {
              id: 206,
              title: "HTML Project: Building Your First Webpage",
              description:
                "Apply your HTML knowledge to build a complete webpage from scratch, incorporating all the elements you've learned.",
              duration: "1h 20m",
              content:
                "This would be an assignment component in a real application.",
              type: "assignment",
              isCompleted: false,
              order: 6,
              learningObjectives: [
                "Apply HTML concepts in a real project",
                "Structure a complete webpage properly",
                "Implement navigation, sections, and forms",
              ],
              requirements: ["Complete all previous HTML lessons"],
            },
            {
              id: 207,
              title: "HTML Section Assessment",
              description:
                "Comprehensive assessment covering all HTML topics to ensure you understand the core concepts before moving on.",
              duration: "30m",
              content: "This would be a quiz component in a real application.",
              type: "quiz",
              isCompleted: false,
              order: 7,
              learningObjectives: [
                "Demonstrate mastery of HTML fundamentals",
                "Test your knowledge of semantic HTML",
                "Validate your understanding of forms and multimedia",
              ],
              requirements: ["Complete all HTML lessons and the project"],
            },
          ],
        },
        {
          id: 3,
          title: "CSS Fundamentals",
          description:
            "Master the fundamentals of CSS to style web pages, create responsive layouts, and design beautiful user interfaces.",
          estimatedCompletionTime: "8 hours",
          order: 3,
          lessons: [
            {
              id: 301,
              title: "Introduction to CSS",
              description:
                "Learn the basics of CSS including selectors, properties, and values to begin styling your HTML documents.",
              duration: "40m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 1,
              learningObjectives: [
                "Understand CSS syntax and structure",
                "Apply CSS to HTML documents",
                "Use different types of CSS selectors",
              ],
              requirements: ["Complete the HTML Fundamentals section"],
            },
            {
              id: 302,
              title: "CSS Box Model and Layout",
              description:
                "Deep dive into the CSS box model, layout techniques, and positioning to control the placement of elements on the page.",
              duration: "55m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 2,
              learningObjectives: [
                "Master the CSS box model concept",
                "Use margin, padding, and borders effectively",
                "Apply different positioning techniques",
              ],
              requirements: [],
            },
          ],
        },
        {
          id: 4,
          title: "JavaScript Basics",
          description:
            "Introduction to programming with JavaScript, covering core concepts, DOM manipulation, and interactive web elements.",
          estimatedCompletionTime: "10 hours",
          order: 4,
          lessons: [
            {
              id: 401,
              title: "Introduction to JavaScript",
              description:
                "Get started with JavaScript programming, learning about variables, data types, and basic syntax.",
              duration: "50m",
              videoUrl:
                "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
              type: "video",
              isCompleted: false,
              order: 1,
              learningObjectives: [
                "Understand JavaScript's role in web development",
                "Write basic JavaScript statements",
                "Use variables and data types",
              ],
              requirements: ["Complete HTML and CSS sections"],
            },
          ],
        },
        {
          id: 5,
          title: "Advanced JavaScript",
          description:
            "Dive deeper into JavaScript with advanced concepts, asynchronous programming, and modern ES6+ features.",
          estimatedCompletionTime: "12 hours",
          order: 5,
          lessons: [],
        },
        {
          id: 6,
          title: "React Fundamentals",
          description:
            "Learn to build interactive user interfaces with React, the popular JavaScript library for UI development.",
          estimatedCompletionTime: "15 hours",
          order: 6,
          lessons: [],
        },
        {
          id: 7,
          title: "Node.js and Server-side Development",
          description:
            "Introduction to server-side programming with Node.js, Express, and MongoDB.",
          estimatedCompletionTime: "14 hours",
          order: 7,
          lessons: [],
        },
        {
          id: 8,
          title: "Final Project: Full-Stack Application",
          description:
            "Apply everything you've learned to create a complete full-stack web application from scratch.",
          estimatedCompletionTime: "20 hours",
          order: 8,
          lessons: [],
        },
      ],
      progress: {
        completed: 1,
        total: 30,
        percentage: 3,
        lastLesson: {
          id: 101,
          title: "Course Overview and Setup",
        },
      },
    };

    // Auto expand the first section
    if (course.value?.sections.length) {
      expandedSections.value = [course.value.sections[0].id];
      selectedSection.value = course.value.sections[0].id;
    }
  } catch (err) {
    console.error("Error fetching course data:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

// Calculate the total number of lessons
const totalLessons = computed(() => {
  if (!course.value) return 0;

  return course.value.sections.reduce((acc, section) => {
    return acc + section.lessons.length;
  }, 0);
});

// Calculate the total time for the course
const formatTotalDuration = computed(() => {
  return course.value?.totalDuration || "0h 0m";
});

// Toggle section expansion
const toggleSection = (sectionId: number) => {
  const index = expandedSections.value.indexOf(sectionId);
  if (index === -1) {
    expandedSections.value.push(sectionId);
  } else {
    expandedSections.value.splice(index, 1);
  }
};

// Check if a section is expanded
const isSectionExpanded = (sectionId: number) => {
  return expandedSections.value.includes(sectionId);
};

// Start the course (navigate to the first lesson or continue where left off)
const startCourse = () => {
  if (!course.value) return;

  // If the user has progress, navigate to the last lesson + 1
  if (course.value.progress && course.value.progress.lastLesson) {
    // Find the current lesson index and navigate to the next one
    const allLessons = getAllLessons();
    const lastLessonIndex = allLessons.findIndex(
      (lesson) => lesson.id === course.value?.progress?.lastLesson?.id
    );

    if (lastLessonIndex !== -1 && lastLessonIndex < allLessons.length - 1) {
      // Navigate to the next lesson
      router.push(
        `/courses/${courseId.value}/learn/${allLessons[lastLessonIndex + 1].id}`
      );
      return;
    }
  }

  // Otherwise, navigate to the first lesson
  const firstLesson = getFirstLesson();
  if (firstLesson) {
    router.push(`/courses/${courseId.value}/learn/${firstLesson.id}`);
  }
};

// Continue course (navigate to the last accessed lesson)
const continueCourse = () => {
  if (
    !course.value ||
    !course.value.progress ||
    !course.value.progress.lastLesson
  )
    return;

  router.push(
    `/courses/${courseId.value}/learn/${course.value.progress.lastLesson.id}`
  );
};

// Start a specific lesson
const startLesson = (lessonId: number) => {
  router.push(`/courses/${courseId.value}/learn/${lessonId}`);
};

// Get the first lesson of the course
const getFirstLesson = () => {
  if (!course.value || !course.value.sections.length) return null;

  const firstSection = course.value.sections.sort(
    (a, b) => a.order - b.order
  )[0];
  if (!firstSection.lessons.length) return null;

  return firstSection.lessons.sort((a, b) => a.order - b.order)[0];
};

// Get all lessons of the course in order
const getAllLessons = () => {
  if (!course.value) return [];

  const allLessons = [];
  const sortedSections = [...course.value.sections].sort(
    (a, b) => a.order - b.order
  );

  for (const section of sortedSections) {
    const sortedLessons = [...section.lessons].sort(
      (a, b) => a.order - b.order
    );
    allLessons.push(...sortedLessons);
  }

  return allLessons;
};

// Format the lesson type for display
const formatLessonType = (type: string) => {
  const icons = {
    video: "📹",
    quiz: "📝",
    assignment: "🏆",
    resource: "📚",
  };

  return icons[type as keyof typeof icons] || "📄";
};

// Select a section to show in the roadmap
const selectSection = (sectionId: number) => {
  selectedSection.value = sectionId;

  // Make sure the section is expanded
  if (!expandedSections.value.includes(sectionId)) {
    expandedSections.value.push(sectionId);
  }

  // Scroll to the section in the curriculum tab
  setTimeout(() => {
    const element = document.getElementById(`section-${sectionId}`);
    if (element) {
      element.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }, 100);
};

// Get the selected section data
const selectedSectionData = computed(() => {
  if (!course.value || !selectedSection.value) return null;

  return (
    course.value.sections.find(
      (section) => section.id === selectedSection.value
    ) || null
  );
});

// Format the course update date
const formattedUpdateDate = computed(() => {
  if (!course.value?.updatedAt) return "";

  return new Date(course.value.updatedAt).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});

// Lifecycle hooks
onMounted(() => {
  fetchCourseData();
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-white min-h-screen">
    <!-- Loading state -->
    <div
      v-if="loading"
      class="flex-grow flex items-center justify-center py-20"
    >
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      />
    </div>

    <!-- Error state -->
    <div
      v-else-if="error"
      class="flex-grow flex items-center justify-center py-20"
    >
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
        <h3 class="text-xl font-bold mb-2">Failed to load course</h3>
        <p class="text-gray-600 mb-4">
          There was an error loading this course. Please try again.
        </p>
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          @click="fetchCourseData"
        >
          Retry
        </button>
      </div>
    </div>

    <!-- Course content -->
    <div v-else class="container mx-auto p-4 md:p-6">
      <!-- Course header -->
      <div
        class="bg-gradient-to-r from-indigo-700 to-purple-800 rounded-xl p-6 md:p-10 text-white mb-8 shadow-lg"
      >
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between"
        >
          <div class="mb-6 md:mb-0 md:mr-6">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4">
              {{ course?.title }}
            </h1>

            <div class="flex flex-wrap items-center text-sm mb-4">
              <span class="flex items-center mr-4 mb-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
                {{ course?.rating }} rating ({{
                  Math.floor((course?.studentsCount || 1) / 100)
                }}K+ reviews)
              </span>

              <span class="flex items-center mr-4 mb-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ course?.studentsCount.toLocaleString() }} students
              </span>

              <span class="flex items-center mr-4 mb-2">
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
                {{ formatTotalDuration }}
              </span>

              <span class="flex items-center mr-4 mb-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ totalLessons }} lessons
              </span>

              <span class="flex items-center mb-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  />
                </svg>
                Last updated: {{ formattedUpdateDate }}
              </span>
            </div>

            <div class="flex flex-wrap gap-2 mb-6">
              <span
                v-for="tag in course?.tags"
                :key="tag"
                class="bg-white/20 text-xs px-3 py-1 rounded-full backdrop-blur-sm"
              >
                {{ tag }}
              </span>
            </div>

            <div class="flex items-center">
              <img
                :src="course?.instructor.avatar"
                :alt="course?.instructor.name"
                class="w-10 h-10 rounded-full mr-3"
              />
              <div>
                <span class="text-sm opacity-90">Instructor:</span>
                <div class="font-semibold">{{ course?.instructor.name }}</div>
              </div>
            </div>
          </div>

          <div class="flex flex-col max-w-sm">
            <!-- Progress card -->
            <div
              class="bg-white/10 backdrop-blur-sm rounded-lg p-6 shadow-md mb-4"
            >
              <h3 class="text-lg font-semibold mb-2">Your Progress</h3>

              <div v-if="course?.progress" class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                  <span
                    >{{ course.progress.completed }} /
                    {{ course.progress.total }} lessons completed</span
                  >
                  <span>{{ course.progress.percentage }}%</span>
                </div>
                <div
                  class="w-full h-2 bg-white/20 rounded-full overflow-hidden"
                >
                  <div
                    class="h-full bg-green-400"
                    :style="{ width: `${course.progress.percentage}%` }"
                  />
                </div>
              </div>

              <div v-if="course?.progress?.lastLesson" class="text-sm mb-4">
                <p class="opacity-90 mb-1">Last lesson:</p>
                <p class="font-medium truncate">
                  {{ course.progress.lastLesson.title }}
                </p>
              </div>

              <div class="flex flex-col space-y-2">
                <button
                  v-if="course?.progress?.lastLesson"
                  class="bg-white text-indigo-700 font-semibold py-3 px-6 rounded-lg transition-colors hover:bg-indigo-50 flex items-center justify-center"
                  @click="continueCourse"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Continue Learning
                </button>

                <button
                  class="bg-indigo-500 hover:bg-indigo-600 font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                  :class="{ 'mt-2': course?.progress?.lastLesson }"
                  @click="startCourse"
                >
                  <svg
                    v-if="!course?.progress?.lastLesson"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{
                    course?.progress?.lastLesson
                      ? "Start from Beginning"
                      : "Start Learning"
                  }}
                </button>
              </div>
            </div>

            <!-- Course level -->
            <div
              class="bg-white/10 backdrop-blur-sm rounded-lg p-4 flex items-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 mr-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                />
              </svg>
              <div>
                <span class="text-sm opacity-90">Course Level:</span>
                <div class="font-semibold">{{ course?.level }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Course content tabs -->
      <div class="mb-8">
        <div class="border-b border-gray-200">
          <nav class="flex space-x-8">
            <button
              class="px-1 py-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'overview'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
              @click="activeTab = 'overview'"
            >
              Overview
            </button>
            <button
              class="px-1 py-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'curriculum'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
              @click="activeTab = 'curriculum'"
            >
              Curriculum
            </button>
            <button
              class="px-1 py-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'instructor'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
              @click="activeTab = 'instructor'"
            >
              Instructor
            </button>
            <button
              class="px-1 py-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'reviews'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
              @click="activeTab = 'reviews'"
            >
              Reviews
            </button>
          </nav>
        </div>
      </div>

      <!-- Tab content -->
      <div class="mb-8">
        <!-- Overview tab -->
        <div
          v-if="activeTab === 'overview'"
          class="grid grid-cols-1 lg:grid-cols-3 gap-8"
        >
          <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-4">About This Course</h2>
            <p class="text-gray-700 mb-6">{{ course?.description }}</p>

            <div class="mb-8">
              <h3 class="text-xl font-bold mb-4">What You'll Learn</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div
                  v-for="(objective, idx) in course?.objectives"
                  :key="idx"
                  class="flex"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span>{{ objective }}</span>
                </div>
              </div>
            </div>

            <div class="mb-8">
              <h3 class="text-xl font-bold mb-4">Requirements</h3>
              <ul class="space-y-2">
                <li
                  v-for="(requirement, idx) in course?.requirements"
                  :key="idx"
                  class="flex"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-3 flex-shrink-0 mt-0.5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span>{{ requirement }}</span>
                </li>
              </ul>
            </div>

            <div>
              <h3 class="text-xl font-bold mb-4">Course Structure</h3>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-indigo-50 rounded-lg p-4 text-center">
                  <div class="text-2xl font-bold text-indigo-600 mb-1">
                    {{ course?.sections.length }}
                  </div>
                  <div class="text-gray-600 text-sm">Sections</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4 text-center">
                  <div class="text-2xl font-bold text-indigo-600 mb-1">
                    {{ totalLessons }}
                  </div>
                  <div class="text-gray-600 text-sm">Lessons</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4 text-center">
                  <div class="text-2xl font-bold text-indigo-600 mb-1">
                    {{ course?.totalDuration }}
                  </div>
                  <div class="text-gray-600 text-sm">Duration</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4 text-center">
                  <div class="text-2xl font-bold text-indigo-600 mb-1">
                    Lifetime
                  </div>
                  <div class="text-gray-600 text-sm">Access</div>
                </div>
              </div>

              <p class="text-gray-700 mb-4">
                This comprehensive course is structured to take you from the
                very basics to advanced concepts in web development. Starting
                with fundamentals before moving to more complex topics, you'll
                build real-world projects along the way to reinforce your
                learning.
              </p>

              <button
                class="text-indigo-600 font-medium flex items-center hover:text-indigo-800 transition-colors"
                @click="activeTab = 'curriculum'"
              >
                View detailed curriculum
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 ml-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Course roadmap/journey visualization -->
          <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <h3 class="text-xl font-bold mb-4">Course Roadmap</h3>

            <!-- Course flow visualization -->
            <div class="mb-6 overflow-hidden relative">
              <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-indigo-200" />

              <div
                v-for="(section, index) in course?.sections"
                :key="section.id"
                class="flex mb-4 relative cursor-pointer group"
                @click="selectSection(section.id)"
              >
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 z-10 shadow-sm transition-all duration-200"
                  :class="[
                    selectedSection === section.id
                      ? 'bg-indigo-600 text-white'
                      : 'bg-white text-indigo-600 border-2 border-indigo-200 group-hover:border-indigo-400',
                  ]"
                >
                  {{ index + 1 }}
                </div>

                <div
                  class="ml-4 p-3 rounded-lg transition-all duration-200 flex-grow"
                  :class="[
                    selectedSection === section.id
                      ? 'bg-indigo-50 border border-indigo-200'
                      : 'group-hover:bg-gray-100',
                  ]"
                >
                  <h4 class="font-medium text-gray-900">{{ section.title }}</h4>
                  <p class="text-sm text-gray-600">
                    {{ section.estimatedCompletionTime }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Selected section preview -->
            <div
              v-if="selectedSectionData"
              class="border border-gray-200 rounded-lg p-4 bg-white"
            >
              <h4 class="font-semibold text-lg mb-2">
                {{ selectedSectionData.title }}
              </h4>
              <p class="text-gray-700 text-sm mb-4">
                {{ selectedSectionData.description }}
              </p>

              <div class="mb-3 text-sm text-gray-600">
                {{ selectedSectionData.lessons.length }} lessons •
                {{ selectedSectionData.estimatedCompletionTime }}
              </div>

              <div
                v-if="selectedSectionData.lessons.length > 0"
                class="space-y-2 mb-4"
              >
                <div
                  v-for="lesson in selectedSectionData.lessons.slice(0, 3)"
                  :key="lesson.id"
                  class="flex items-center text-sm"
                >
                  <span class="w-5 text-center mr-2">{{
                    formatLessonType(lesson.type)
                  }}</span>
                  <span class="truncate">{{ lesson.title }}</span>
                </div>

                <div
                  v-if="selectedSectionData.lessons.length > 3"
                  class="text-gray-500 text-sm ml-7"
                >
                  + {{ selectedSectionData.lessons.length - 3 }} more lessons
                </div>
              </div>

              <button
                class="w-full bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium py-2 rounded transition-colors text-sm"
                :disabled="!selectedSectionData.lessons.length"
                @click="startLesson(selectedSectionData.lessons[0]?.id)"
              >
                Start This Section
              </button>
            </div>
          </div>
        </div>

        <!-- Curriculum tab -->
        <div v-if="activeTab === 'curriculum'">
          <h2 class="text-2xl font-bold mb-6">Course Curriculum</h2>

          <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
              <span
                >{{ course?.sections.length }} sections •
                {{ totalLessons }} lessons • {{ formatTotalDuration }} total
                length</span
              >

              <button
                class="text-indigo-600 hover:text-indigo-800 transition-colors"
                @click="
                  expandedSections = course?.sections.map((s) => s.id) || []
                "
              >
                Expand all sections
              </button>
            </div>
          </div>

          <!-- Course sections accordion -->
          <div
            class="border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-200 mb-8"
          >
            <div
              v-for="section in course?.sections"
              :id="`section-${section.id}`"
              :key="section.id"
              class="bg-white"
            >
              <!-- Section header -->
              <button
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors text-left"
                @click="toggleSection(section.id)"
              >
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2 transition-transform"
                    :class="
                      isSectionExpanded(section.id)
                        ? 'transform rotate-180 text-indigo-600'
                        : 'text-gray-400'
                    "
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>

                  <div class="mr-3">
                    <h3 class="font-semibold text-gray-900">
                      {{ section.title }}
                    </h3>
                  </div>
                </div>

                <div class="text-sm text-gray-600">
                  {{ section.lessons.length }} lessons •
                  {{ section.estimatedCompletionTime }}
                </div>
              </button>

              <!-- Section content (lessons) -->
              <div
                v-if="isSectionExpanded(section.id)"
                class="px-6 pb-4 space-y-2"
              >
                <div
                  class="mb-2 pb-2 border-b border-gray-100 text-sm text-gray-700"
                >
                  {{ section.description }}
                </div>

                <div
                  v-for="lesson in section.lessons"
                  :key="lesson.id"
                  class="flex py-3 border-b border-gray-100 last:border-0"
                >
                  <div class="mr-3">
                    <!-- Lesson type icon -->
                    <div
                      class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                      :class="{
                        'bg-indigo-100 text-indigo-600':
                          lesson.type === 'video',
                        'bg-orange-100 text-orange-600': lesson.type === 'quiz',
                        'bg-green-100 text-green-600':
                          lesson.type === 'assignment',
                        'bg-blue-100 text-blue-600': lesson.type === 'resource',
                      }"
                    >
                      <svg
                        v-if="lesson.type === 'video'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                        />
                      </svg>

                      <svg
                        v-if="lesson.type === 'quiz'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
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
                        v-if="lesson.type === 'assignment'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                        />
                      </svg>

                      <svg
                        v-if="lesson.type === 'resource'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="flex-grow">
                    <div class="flex justify-between mb-1">
                      <h4 class="font-medium text-gray-900">
                        {{ lesson.title }}
                      </h4>
                      <span class="text-sm text-gray-600">{{
                        lesson.duration
                      }}</span>
                    </div>

                    <p class="text-sm text-gray-600 mb-2">
                      {{ lesson.description }}
                    </p>

                    <!-- Lesson objectives and requirements -->
                    <div
                      v-if="
                        lesson.learningObjectives &&
                        lesson.learningObjectives.length > 0
                      "
                      class="mb-3"
                    >
                      <div class="flex items-center text-xs text-gray-500 mb-1">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-3.5 w-3.5 mr-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                          <path
                            fill-rule="evenodd"
                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        Learning Objectives:
                      </div>
                      <ul
                        class="text-xs text-gray-600 ml-5 list-disc space-y-1"
                      >
                        <li
                          v-for="(
                            objective, idx
                          ) in lesson.learningObjectives.slice(0, 2)"
                          :key="idx"
                        >
                          {{ objective }}
                        </li>
                        <li
                          v-if="lesson.learningObjectives.length > 2"
                          class="text-gray-500"
                        >
                          + {{ lesson.learningObjectives.length - 2 }} more
                          objectives
                        </li>
                      </ul>
                    </div>

                    <div
                      v-if="
                        lesson.requirements && lesson.requirements.length > 0
                      "
                      class="text-xs text-gray-500"
                    >
                      <span class="font-medium">Requirements:</span>
                      {{ lesson.requirements.join(", ") }}
                    </div>

                    <!-- Lesson action button -->
                    <button
                      class="mt-3 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                      :class="[
                        lesson.isCompleted
                          ? 'bg-green-100 text-green-700 hover:bg-green-200'
                          : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200',
                      ]"
                      @click="startLesson(lesson.id)"
                    >
                      <svg
                        v-if="lesson.isCompleted"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3.5 w-3.5 mr-1"
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
                        class="h-3.5 w-3.5 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ lesson.isCompleted ? "Review" : "Start" }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Start course button -->
          <div class="flex justify-center">
            <button
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-10 rounded-lg transition-colors shadow-md flex items-center"
              @click="startCourse"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                  clip-rule="evenodd"
                />
              </svg>
              Start Learning Now
            </button>
          </div>
        </div>

        <!-- Instructor tab -->
        <div v-if="activeTab === 'instructor'" class="max-w-3xl">
          <h2 class="text-2xl font-bold mb-6">Your Instructor</h2>

          <div
            class="flex flex-col md:flex-row md:items-start bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8"
          >
            <img
              :src="course?.instructor.avatar"
              :alt="course?.instructor.name"
              class="w-24 h-24 rounded-full object-cover mb-4 md:mb-0 md:mr-6"
            />

            <div>
              <h3 class="text-xl font-bold mb-1">
                {{ course?.instructor.name }}
              </h3>
              <p class="text-gray-600 mb-3">{{ course?.instructor.title }}</p>

              <div class="flex items-center mb-4">
                <div class="flex items-center mr-4">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  <span>4.9 Instructor Rating</span>
                </div>

                <div class="flex items-center mr-4">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                    />
                  </svg>
                  <span>25,000+ Students</span>
                </div>

                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 01.788 0l4 1.714a1 1 0 01.356.257l2.644-1.131a1 1 0 000-1.84l-7-3z"
                    />
                    <path
                      d="M4 11.794V16l4-1.714a1 1 0 01.788 0L12.78 16v-4.206l-4.128 1.769a1 1 0 01-.788 0L4 11.794z"
                    />
                  </svg>
                  <span>8 Courses</span>
                </div>
              </div>

              <p class="text-gray-700 mb-6">{{ course?.instructor.bio }}</p>

              <div class="flex space-x-3">
                <a
                  href="#"
                  class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                    />
                  </svg>
                  LinkedIn
                </a>
                <a
                  href="#"
                  class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                    />
                  </svg>
                  Twitter
                </a>
                <a
                  href="#"
                  class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                    />
                  </svg>
                  GitHub
                </a>
              </div>
            </div>
          </div>

          <!-- Instructor courses -->
          <h3 class="text-xl font-bold mb-4">
            Other Courses by {{ course?.instructor.name }}
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 flex flex-col"
            >
              <img
                src="/images/courses/c1.jpg"
                alt="Course thumbnail"
                class="w-full h-40 object-cover"
              />
              <div class="p-4 flex-grow">
                <h4 class="font-bold text-lg mb-2">
                  Advanced JavaScript: From Fundamentals to Functional JS
                </h4>
                <div class="flex items-center text-sm mb-2">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-yellow-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  <span>4.8 (1,432 ratings)</span>
                </div>
                <p class="text-gray-600 text-sm mb-4">
                  Master advanced JavaScript concepts including closures,
                  prototypes, and functional programming techniques.
                </p>
                <a
                  href="#"
                  class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center mt-auto"
                >
                  View Course
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 ml-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </a>
              </div>
            </div>

            <div
              class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 flex flex-col"
            >
              <img
                src="/images/courses/c2.jpg"
                alt="Course thumbnail"
                class="w-full h-40 object-cover"
              />
              <div class="p-4 flex-grow">
                <h4 class="font-bold text-lg mb-2">
                  React Native: Build Mobile Apps with JavaScript
                </h4>
                <div class="flex items-center text-sm mb-2">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-yellow-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  <span>4.9 (988 ratings)</span>
                </div>
                <p class="text-gray-600 text-sm mb-4">
                  Learn to create cross-platform mobile applications for iOS and
                  Android using React Native and your web development skills.
                </p>
                <a
                  href="#"
                  class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center mt-auto"
                >
                  View Course
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 ml-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Reviews tab -->
        <div v-if="activeTab === 'reviews'">
          <div class="flex flex-col md:flex-row md:items-start gap-8 mb-12">
            <!-- Reviews summary -->
            <div
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 w-full md:w-80 flex-shrink-0"
            >
              <div class="text-center mb-6">
                <div class="text-5xl font-bold text-gray-900 mb-2">
                  {{ course?.rating }}
                </div>
                <div class="flex justify-center mb-2">
                  <div class="flex text-yellow-400">
                    <svg
                      v-for="i in 5"
                      :key="i"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      :class="{
                        'text-gray-300': i > Math.floor(course?.rating || 0),
                      }"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="text-gray-600 text-sm">
                  {{ Math.floor(course?.studentsCount || 0 / 100) }}K ratings
                </div>
              </div>

              <!-- Rating distribution -->
              <div class="space-y-2">
                <div
                  v-for="rating in [5, 4, 3, 2, 1]"
                  :key="rating"
                  class="flex items-center"
                >
                  <span class="text-sm w-2 text-gray-600 mr-2">{{
                    rating
                  }}</span>
                  <div class="flex text-yellow-400 mr-2">
                    <svg
                      v-for="i in 5"
                      :key="i"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3.5 w-3.5"
                      :class="{ 'text-gray-300': i > rating }"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                  <div
                    class="h-2 bg-gray-200 rounded-full flex-grow overflow-hidden"
                  >
                    <div
                      class="h-full bg-yellow-400"
                      :style="{
                        width: `${
                          rating === 5
                            ? '78'
                            : rating === 4
                            ? '12'
                            : rating === 3
                            ? '6'
                            : rating === 2
                            ? '3'
                            : '1'
                        }%`,
                      }"
                    />
                  </div>
                  <span class="text-sm text-gray-600 ml-2 w-8 text-right">
                    {{
                      rating === 5
                        ? "78"
                        : rating === 4
                        ? "12"
                        : rating === 3
                        ? "6"
                        : rating === 2
                        ? "3"
                        : "1"
                    }}%
                  </span>
                </div>
              </div>
            </div>

            <!-- Reviews list -->
            <div class="flex-grow">
              <h2 class="text-2xl font-bold mb-6">Student Reviews</h2>

              <div class="mb-6">
                <div class="relative">
                  <input
                    type="text"
                    placeholder="Search reviews..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg pl-10"
                  />
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 absolute left-3 top-3.5 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                  </svg>
                </div>
              </div>

              <!-- Sample reviews -->
              <div class="space-y-6">
                <div
                  v-for="i in 3"
                  :key="i"
                  class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"
                >
                  <div class="flex mb-3">
                    <img
                      :src="`/images/instructors/i${i}.jpg`"
                      alt="User avatar"
                      class="w-12 h-12 rounded-full mr-4"
                    />
                    <div>
                      <div class="font-medium">
                        {{
                          i === 1
                            ? "Michael Chen"
                            : i === 2
                            ? "Jessica Williams"
                            : "David Rodriguez"
                        }}
                      </div>
                      <div class="flex items-center text-sm text-gray-600">
                        <div class="flex text-yellow-400 mr-2">
                          <svg
                            v-for="star in 5"
                            :key="star"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            :class="{
                              'text-gray-300': star > (i === 2 ? 4 : 5),
                            }"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                          </svg>
                        </div>
                        <span>{{
                          i === 1
                            ? "1 month ago"
                            : i === 2
                            ? "2 weeks ago"
                            : "3 days ago"
                        }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="text-gray-700">
                    <p>
                      {{
                        i === 1
                          ? "This course exceeded my expectations. The instructor breaks down complex concepts into easy-to-understand segments, and the projects really helped solidify my understanding. I feel much more confident in my web development skills now."
                          : i === 2
                          ? "Great course overall, though I wish there was a bit more focus on responsive design techniques. The JavaScript sections are incredibly well done and the instructor is very responsive to questions."
                          : "Amazing course! I went from knowing almost nothing about web development to building full-stack applications. The progression from basic HTML/CSS to complex JavaScript and React concepts is very well structured."
                      }}
                    </p>
                  </div>

                  <div class="mt-3 text-sm">
                    <button class="text-gray-500 hover:text-gray-700 mr-4">
                      Helpful ({{ i === 1 ? "24" : i === 2 ? "18" : "7" }})
                    </button>
                    <button class="text-gray-500 hover:text-gray-700">
                      Report
                    </button>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <div class="mt-8 flex justify-center">
                <nav class="flex items-center">
                  <button
                    class="px-2 py-1 border border-gray-300 rounded-l-md bg-white text-gray-500 hover:bg-gray-50"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <button
                    class="px-3 py-1 border-t border-b border-gray-300 bg-indigo-100 text-indigo-700"
                  >
                    1
                  </button>
                  <button
                    class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                  >
                    2
                  </button>
                  <button
                    class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                  >
                    3
                  </button>
                  <span
                    class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700"
                    >...</span
                  >
                  <button
                    class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                  >
                    12
                  </button>
                  <button
                    class="px-2 py-1 border border-gray-300 rounded-r-md bg-white text-gray-500 hover:bg-gray-50"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
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
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Related courses -->
      <div class="bg-gray-50 rounded-xl p-8 border border-gray-200 mb-12">
        <h2 class="text-2xl font-bold mb-6">Related Courses</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="i in 3"
            :key="i"
            class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200"
          >
            <img
              :src="`/images/courses/c${i}.jpg`"
              alt="Course thumbnail"
              class="w-full h-40 object-cover"
            />
            <div class="p-5">
              <h3 class="font-bold text-lg mb-2">
                {{
                  i === 1
                    ? "CSS Mastery: Advanced Styling and Layouts"
                    : i === 2
                    ? "JavaScript: The Complete Guide"
                    : "React Fundamentals for Beginners"
                }}
              </h3>
              <div class="flex items-center text-sm mb-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-yellow-400 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
                <span
                  >{{ i === 1 ? "4.7" : i === 2 ? "4.9" : "4.8" }} ({{
                    i === 1 ? "1,245" : i === 2 ? "3,821" : "2,367"
                  }}
                  ratings)</span
                >
              </div>
              <div class="text-sm text-gray-600 mb-4">
                {{ i === 1 ? "8h 45m" : i === 2 ? "21h 30m" : "15h 15m" }} •
                {{
                  i === 1 ? "Intermediate" : i === 2 ? "All Levels" : "Beginner"
                }}
              </div>
              <div class="flex justify-between items-center">
                <span class="font-bold text-gray-900"
                  >${{ i === 1 ? "49.99" : i === 2 ? "89.99" : "69.99" }}</span
                >
                <a
                  href="#"
                  class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center"
                >
                  View Course
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 ml-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FAQ section -->
      <div class="mb-12 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-center">
          Frequently Asked Questions
        </h2>

        <div class="space-y-4">
          <div class="border border-gray-200 rounded-lg overflow-hidden">
            <button
              class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors focus:outline-none text-left font-medium"
            >
              <span
                >Do I need any prior programming experience for this
                course?</span
              >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <div
              class="p-5 pt-0 text-gray-700 border-t border-gray-100 bg-white"
            >
              <p>
                No, this course is designed for complete beginners. We start
                from the very basics and gradually build up your knowledge and
                skills. The only requirements are basic computer skills and a
                willingness to learn.
              </p>
            </div>
          </div>

          <div class="border border-gray-200 rounded-lg overflow-hidden">
            <button
              class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors focus:outline-none text-left font-medium"
            >
              <span>How long do I have access to the course?</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <div
              class="p-5 pt-0 text-gray-700 border-t border-gray-100 bg-white"
            >
              <p>
                Once you enroll, you have lifetime access to the course. This
                includes all future updates and additional content that may be
                added over time.
              </p>
            </div>
          </div>

          <div class="border border-gray-200 rounded-lg overflow-hidden">
            <button
              class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors focus:outline-none text-left font-medium"
            >
              <span>Will I get a certificate upon completion?</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <div
              class="p-5 pt-0 text-gray-700 border-t border-gray-100 bg-white"
            >
              <p>
                Yes, upon completing all the course modules and assignments,
                you'll receive a certificate of completion that you can add to
                your LinkedIn profile or resume.
              </p>
            </div>
          </div>

          <div class="border border-gray-200 rounded-lg overflow-hidden">
            <button
              class="w-full flex justify-between items-center p-5 bg-white hover:bg-gray-50 transition-colors focus:outline-none text-left font-medium"
            >
              <span>Is there support available if I get stuck?</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <div
              class="p-5 pt-0 text-gray-700 border-t border-gray-100 bg-white"
            >
              <p>
                Absolutely! You can post your questions in the course discussion
                forums where both the instructor and other students can help.
                The instructor typically responds to questions within 24-48
                hours.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA section -->
      <div
        class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl p-8 text-white text-center"
      >
        <h2 class="text-2xl md:text-3xl font-bold mb-4">
          Ready to start your web development journey?
        </h2>
        <p class="text-lg mb-6 max-w-2xl mx-auto">
          Join thousands of students who have already taken this course and
          transformed their careers.
        </p>
        <button
          class="bg-white text-indigo-700 font-bold py-4 px-10 rounded-lg hover:bg-indigo-50 transition-colors shadow-md inline-flex items-center"
          @click="startCourse"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
              clip-rule="evenodd"
            />
          </svg>
          Start Learning Now
        </button>
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

/* Course roadmap line connector styles */
.roadmap-connector {
  position: absolute;
  left: 5px;
  top: 0;
  bottom: 0;
  width: 1px;
  background-color: #e5e7eb;
  z-index: 0;
}

/* Animation for content tabs */
.tab-content-enter-active,
.tab-content-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.tab-content-enter-from,
.tab-content-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
