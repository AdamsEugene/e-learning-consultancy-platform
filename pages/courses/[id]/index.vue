<!-- pages/courses/[id].vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";

// Define course types
interface CourseLesson {
  id: number;
  title: string;
  duration: string;
  videoUrl?: string;
  type: "video" | "quiz" | "assignment" | "resource";
  isPreview?: boolean;
  isCompleted?: boolean;
}

interface CourseSection {
  id: number;
  title: string;
  lessons: CourseLesson[];
  totalDuration: string;
}

interface CourseInstructor {
  id: number;
  name: string;
  avatar: string;
  role: string;
  bio: string;
  courses: number;
  students: number;
  rating: number;
}

interface CourseReview {
  id: number;
  user: {
    name: string;
    avatar: string;
  };
  rating: number;
  date: string;
  content: string;
  helpfulCount: number;
}

interface Course {
  id: number;
  title: string;
  subtitle: string;
  description: string;
  category: string;
  subCategory?: string;
  level: string;
  language: string;
  price: number;
  originalPrice?: number;
  image: string;
  rating: number;
  reviewCount: number;
  enrollmentCount: number;
  createdAt: string;
  lastUpdated: string;
  totalDuration: string;
  totalLessons: number;
  features: string[];
  requirements: string[];
  whatYouWillLearn: string[];
  sections: CourseSection[];
  instructors: CourseInstructor[];
  reviews: CourseReview[];
  isEnrolled?: boolean;
  progress?: number;
  badges?: string[];
}

// Get course ID from route
const route = useRoute();
const courseId = computed(() => Number(route.params.id));

// Course state
const course = ref<Course | null>(null);
const loading = ref(true);
const error = ref(false);
const activeTab = ref("overview");
const expandedSections = ref<Set<number>>(new Set());

// Fetch course data (mocked for now - would be API call in real app)
const fetchCourseData = async () => {
  loading.value = true;
  error.value = false;

  try {
    // Simulating API call with timeout
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Mock course data - would be from API
    course.value = {
      id: courseId.value,
      title: "Complete Web Development Bootcamp",
      subtitle:
        "Master HTML, CSS, JavaScript, Node.js and more to become a full-stack web developer",
      description:
        "This comprehensive course will take you from beginner to professional web developer. You'll learn both front-end and back-end technologies, starting with HTML, CSS, and JavaScript fundamentals, then advancing to modern frameworks like Vue.js and Node.js. By the end of this course, you'll have built multiple real-world projects for your portfolio and gained the skills needed to land your first web development job.",
      category: "Web Development",
      subCategory: "Full-Stack",
      level: "All Levels",
      language: "English",
      price: 89.99,
      originalPrice: 199.99,
      image: "/images/courses/c1.jpg",
      rating: 4.8,
      reviewCount: 1245,
      enrollmentCount: 15782,
      createdAt: "2023-06-10",
      lastUpdated: "2024-03-15",
      totalDuration: "42h 30m",
      totalLessons: 285,
      features: [
        "Lifetime access to all course content",
        "42.5 hours of on-demand video",
        "285 lessons with practical exercises",
        "45 downloadable resources",
        "15 coding challenges and projects",
        "Certificate of completion",
        "Mobile and TV access",
      ],
      requirements: [
        "No prior programming experience needed - we'll teach you everything from scratch",
        "A computer with internet access and a modern web browser",
        "Basic computer skills and familiarity with using the internet",
      ],
      whatYouWillLearn: [
        "Build 15+ real world web development projects from scratch",
        "Master HTML5, CSS3, and modern JavaScript from beginner to advanced",
        "Create responsive, accessible, and dynamic websites",
        "Build full-stack web applications using Vue.js, Node.js, and MongoDB",
        "Implement authentication, API integration, and database management",
        "Deploy your applications to production with cloud hosting services",
        "Apply best practices for web security, performance, and SEO",
        "Collaborate using Git and GitHub workflow",
      ],
      sections: [
        {
          id: 1,
          title: "Introduction to Web Development",
          totalDuration: "3h 15m",
          lessons: [
            {
              id: 101,
              title: "Course Overview and Setup",
              duration: "15m",
              type: "video",
              isPreview: true,
              isCompleted: false,
            },
            {
              id: 102,
              title: "How the Web Works: A High-Level Overview",
              duration: "25m",
              type: "video",
              isPreview: true,
              isCompleted: false,
            },
            {
              id: 103,
              title: "Setting Up Your Development Environment",
              duration: "30m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 104,
              title: "Web Developer Tools and Resources",
              duration: "45m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 105,
              title: "Introduction Quiz",
              duration: "20m",
              type: "quiz",
              isCompleted: false,
            },
          ],
        },
        {
          id: 2,
          title: "HTML Fundamentals",
          totalDuration: "5h 45m",
          lessons: [
            {
              id: 201,
              title: "HTML Basics and Document Structure",
              duration: "40m",
              type: "video",
              isPreview: true,
              isCompleted: false,
            },
            {
              id: 202,
              title: "Working with Text Elements",
              duration: "35m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 203,
              title: "Links, Images, and Multimedia",
              duration: "50m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 204,
              title: "HTML Forms and Input Elements",
              duration: "55m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 205,
              title: "Tables and Semantic HTML",
              duration: "45m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 206,
              title: "HTML Project: Building Your First Webpage",
              duration: "1h 20m",
              type: "assignment",
              isCompleted: false,
            },
            {
              id: 207,
              title: "HTML Section Assessment",
              duration: "30m",
              type: "quiz",
              isCompleted: false,
            },
          ],
        },
        {
          id: 3,
          title: "CSS Styling",
          totalDuration: "8h 20m",
          lessons: [
            {
              id: 301,
              title: "CSS Basics and Selectors",
              duration: "45m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 302,
              title: "Colors, Typography, and Text Styling",
              duration: "40m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 303,
              title: "The Box Model and Layout",
              duration: "55m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 304,
              title: "Flexbox and Grid Systems",
              duration: "1h 15m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 305,
              title: "CSS Transitions and Animations",
              duration: "50m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 306,
              title: "Responsive Design and Media Queries",
              duration: "1h",
              type: "video",
              isCompleted: false,
            },
            {
              id: 307,
              title: "CSS Frameworks: Introduction to Bootstrap and Tailwind",
              duration: "1h 30m",
              type: "video",
              isCompleted: false,
            },
            {
              id: 308,
              title: "CSS Project: Styling Your Portfolio Website",
              duration: "1h 25m",
              type: "assignment",
              isCompleted: false,
            },
            {
              id: 309,
              title: "CSS Mastery Quiz",
              duration: "30m",
              type: "quiz",
              isCompleted: false,
            },
          ],
        },
      ],
      instructors: [
        {
          id: 1,
          name: "Alex Johnson",
          avatar: "/images/instructors/i1.jpg",
          role: "Senior Web Developer & Instructor",
          bio: "Alex has over 10 years of experience in web development and has worked with companies like Google and Microsoft. He specializes in modern JavaScript frameworks and has helped over 50,000 students master web development skills.",
          courses: 12,
          students: 58750,
          rating: 4.9,
        },
        {
          id: 2,
          name: "Sarah Chen",
          avatar: "/images/instructors/i3.jpg",
          role: "UX Designer & Frontend Developer",
          bio: "Sarah combines her design background with frontend development expertise to teach the principles of beautiful, functional web design. She has worked with numerous startups and Fortune 500 companies to create engaging user experiences.",
          courses: 8,
          students: 32450,
          rating: 4.8,
        },
      ],
      reviews: [
        {
          id: 1,
          user: {
            name: "Michael P.",
            avatar: "/images/instructors/i3.jpg",
          },
          rating: 5,
          date: "2024-02-15",
          content:
            "This course exceeded my expectations! I had zero coding experience, and now I'm building full-stack applications. The projects are practical and relevant to real-world scenarios. Highly recommend!",
          helpfulCount: 42,
        },
        {
          id: 2,
          user: {
            name: "Jennifer K.",
            avatar: "/images/instructors/i2.jpg",
          },
          rating: 4,
          date: "2024-01-28",
          content:
            "Very comprehensive coverage of web development technologies. The sections on JavaScript were particularly helpful. I only wish there was a bit more on testing frameworks, but otherwise excellent.",
          helpfulCount: 18,
        },
        {
          id: 3,
          user: {
            name: "David T.",
            avatar: "/images/instructors/i1.jpg",
          },
          rating: 5,
          date: "2023-12-10",
          content:
            "I've taken several coding courses online, and this is by far the best one. The instructors explain complex concepts in an easy-to-understand way, and the hands-on projects really cement your learning.",
          helpfulCount: 35,
        },
      ],
      badges: ["Bestseller", "Top Rated"],
      isEnrolled: false,
    };
  } catch (err) {
    console.error("Error fetching course data:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

// Calculate discount percentage
const discountPercentage = computed(() => {
  if (!course.value || !course.value.originalPrice) return null;

  const discount = Math.round(
    ((course.value.originalPrice - course.value.price) /
      course.value.originalPrice) *
      100
  );
  return discount;
});

// Format prices
const formattedPrice = computed(() => {
  if (!course.value) return "";

  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(course.value.price);
});

const formattedOriginalPrice = computed(() => {
  if (!course.value || !course.value.originalPrice) return "";

  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(course.value.originalPrice);
});

// Star rating display calculation
const starRating = computed(() => {
  if (!course.value)
    return { wholeStars: 0, hasPartialStar: false, emptyStars: 5 };

  const wholeStars = Math.floor(course.value.rating);
  const hasPartialStar = course.value.rating % 1 >= 0.5;
  const emptyStars = 5 - wholeStars - (hasPartialStar ? 1 : 0);

  return {
    wholeStars,
    hasPartialStar,
    emptyStars,
  };
});

// Total lessons count
const totalLessons = computed(() => {
  if (!course.value) return 0;

  return course.value.sections.reduce((total, section) => {
    return total + section.lessons.length;
  }, 0);
});

// Handle tab switching
const setActiveTab = (tab: string) => {
  activeTab.value = tab;
};

// Toggle section expansion
const toggleSection = (sectionId: number) => {
  if (expandedSections.value.has(sectionId)) {
    expandedSections.value.delete(sectionId);
  } else {
    expandedSections.value.add(sectionId);
  }
};

// Enrollment handling
const enrollInCourse = () => {
  if (!course.value) return;

  // In a real app, this would make an API call to enroll
  course.value.isEnrolled = true;
  course.value.progress = 0;

  // Redirect to course content or first lesson
  // navigateTo(`/courses/${course.value.id}/learn`);
};

// Load data when component mounts
onMounted(() => {
  fetchCourseData();
  // Expand the first section by default
  if (course.value && course.value.sections.length > 0) {
    expandedSections.value.add(course.value.sections[0].id);
  }
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Loading state -->
    <div v-if="loading" class="container mx-auto py-16 px-4">
      <div class="flex flex-col items-center justify-center py-12">
        <div
          class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-indigo-600 mb-4"
        />
        <p class="text-gray-600">Loading course details...</p>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="container mx-auto py-16 px-4">
      <div class="flex flex-col items-center justify-center py-12 text-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-red-500 mb-4"
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
        <h3 class="text-xl font-bold text-gray-900 mb-2">
          Unable to load course
        </h3>
        <p class="text-gray-600 mb-4">
          There was an error loading this course. Please try again later.
        </p>
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          @click="fetchCourseData"
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- Course content -->
    <div v-else-if="course" class="pb-16">
      <!-- Course header banner -->
      <div
        class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-12"
      >
        <div class="container mx-auto px-4 md:px-6">
          <div class="max-w-4xl">
            <!-- Breadcrumbs -->
            <div
              class="flex items-center space-x-2 text-sm mb-4 text-indigo-100"
            >
              <NuxtLink to="/courses" class="hover:text-white transition-colors"
                >Courses</NuxtLink
              >
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
              <NuxtLink
                :to="`/courses?category=${course.category}`"
                class="hover:text-white transition-colors"
                >{{ course.category }}</NuxtLink
              >
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
              <span class="text-indigo-100">{{ course.title }}</span>
            </div>

            <!-- Course badges -->
            <div class="flex flex-wrap gap-2 mb-3">
              <span
                v-for="badge in course.badges"
                :key="badge"
                class="bg-yellow-500 text-yellow-900 text-xs font-bold py-1 px-2 rounded"
              >
                {{ badge }}
              </span>
            </div>

            <!-- Course title and subtitle -->
            <h1 class="text-3xl md:text-4xl font-bold mb-3">
              {{ course.title }}
            </h1>
            <p class="text-xl text-indigo-100 mb-6">{{ course.subtitle }}</p>

            <!-- Course meta info -->
            <div class="flex flex-wrap gap-6 text-sm mb-8">
              <!-- Ratings -->
              <div class="flex items-center">
                <div class="flex text-yellow-400 mr-1">
                  <svg
                    v-for="i in starRating.wholeStars"
                    :key="`whole-${i}`"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  <svg
                    v-if="starRating.hasPartialStar"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                  >
                    <defs>
                      <linearGradient id="partialFill">
                        <stop offset="50%" stop-color="currentColor" />
                        <stop offset="50%" stop-color="rgba(255,255,255,0.3)" />
                      </linearGradient>
                    </defs>
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      fill="url(#partialFill)"
                    />
                  </svg>
                  <svg
                    v-for="i in starRating.emptyStars"
                    :key="`empty-${i}`"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-300 text-opacity-50"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
                <span
                  >{{ course.rating.toFixed(1) }} ({{
                    course.reviewCount.toLocaleString()
                  }}
                  reviews)</span
                >
              </div>

              <!-- Students -->
              <div class="flex items-center">
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
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
                <span
                  >{{ course.enrollmentCount.toLocaleString() }} students</span
                >
              </div>

              <!-- Last updated -->
              <div class="flex items-center">
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
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
                <span>Last updated {{ course.lastUpdated }}</span>
              </div>

              <!-- Language -->
              <div class="flex items-center">
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
                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
                  />
                </svg>
                <span>{{ course.language }}</span>
              </div>
            </div>

            <!-- Instructors preview -->
            <div class="flex items-center space-x-2">
              <span class="mr-1">Created by</span>
              <div class="flex items-center">
                <NuxtLink
                  v-for="(instructor, index) in course.instructors"
                  :key="instructor.id"
                  :to="`/instructors/${instructor.id}`"
                  class="text-indigo-300 hover:text-white font-medium transition-colors"
                >
                  {{ instructor.name
                  }}{{ index < course.instructors.length - 1 ? ", " : "" }}
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main course content -->
      <div class="container mx-auto px-4 md:px-6 -mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Course details - left column -->
          <div class="lg:col-span-2">
            <!-- Course details card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
              <!-- Navigation tabs -->
              <div class="flex border-b mb-6 overflow-x-auto hide-scrollbar">
                <button
                  :class="[
                    'px-4 py-2 font-medium',
                    activeTab === 'overview'
                      ? 'border-b-2 border-indigo-600 text-indigo-600 '
                      : 'text-gray-600  hover:text-indigo-600 ',
                  ]"
                  @click="setActiveTab('overview')"
                >
                  Overview
                </button>
                <button
                  :class="[
                    'px-4 py-2 font-medium',
                    activeTab === 'curriculum'
                      ? 'border-b-2 border-indigo-600 text-indigo-600 '
                      : 'text-gray-600  hover:text-indigo-600 ',
                  ]"
                  @click="setActiveTab('curriculum')"
                >
                  Curriculum
                </button>
                <button
                  :class="[
                    'px-4 py-2 font-medium',
                    activeTab === 'instructors'
                      ? 'border-b-2 border-indigo-600 text-indigo-600 '
                      : 'text-gray-600  hover:text-indigo-600 ',
                  ]"
                  @click="setActiveTab('instructors')"
                >
                  Instructors
                </button>
                <button
                  :class="[
                    'px-4 py-2 font-medium',
                    activeTab === 'reviews'
                      ? 'border-b-2 border-indigo-600 text-indigo-600 '
                      : 'text-gray-600  hover:text-indigo-600 ',
                  ]"
                  @click="setActiveTab('reviews')"
                >
                  Reviews
                </button>
              </div>

              <!-- Tab content - Overview -->
              <div v-if="activeTab === 'overview'" class="space-y-8">
                <!-- What you'll learn section -->
                <div>
                  <h3 class="text-xl font-bold mb-4 text-gray-900">
                    What you'll learn
                  </h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div
                      v-for="(item, index) in course.whatYouWillLearn"
                      :key="index"
                      class="flex items-start"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="text-gray-700">{{ item }}</span>
                    </div>
                  </div>
                </div>

                <!-- Course description -->
                <div>
                  <h3 class="text-xl font-bold mb-4 text-gray-900">
                    Course Description
                  </h3>
                  <p class="text-gray-700 whitespace-pre-line">
                    {{ course.description }}
                  </p>
                </div>

                <!-- Requirements -->
                <div>
                  <h3 class="text-xl font-bold mb-4 text-gray-900">
                    Requirements
                  </h3>
                  <ul class="list-disc pl-6 space-y-2">
                    <li
                      v-for="(requirement, index) in course.requirements"
                      :key="index"
                      class="text-gray-700"
                    >
                      {{ requirement }}
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Tab content - Curriculum -->
              <div v-if="activeTab === 'curriculum'" class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-xl font-bold text-gray-900">
                    Course Content
                  </h3>
                  <div class="text-sm text-gray-600">
                    {{ course.sections.length }} sections •
                    {{ totalLessons }} lessons •
                    {{ course.totalDuration }} total length
                  </div>
                </div>

                <!-- Course sections accordion -->
                <div class="space-y-4">
                  <div
                    v-for="section in course.sections"
                    :key="section.id"
                    class="border border-gray-200 rounded-lg overflow-hidden"
                  >
                    <!-- Section header -->
                    <div
                      class="bg-gray-50 px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors"
                      @click="toggleSection(section.id)"
                    >
                      <div class="flex items-center space-x-2">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-500 transition-transform duration-200"
                          :class="{
                            'transform rotate-90': expandedSections.has(
                              section.id
                            ),
                          }"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <h4 class="font-medium text-gray-900">
                          {{ section.title }}
                        </h4>
                      </div>
                      <div class="text-sm text-gray-600">
                        {{ section.lessons.length }} lessons •
                        {{ section.totalDuration }}
                      </div>
                    </div>

                    <!-- Section content (expandable) -->
                    <div
                      v-if="expandedSections.has(section.id)"
                      class="divide-y divide-gray-200"
                    >
                      <div
                        v-for="lesson in section.lessons"
                        :key="lesson.id"
                        class="px-4 py-3 flex justify-between items-center bg-white"
                      >
                        <div class="flex items-center">
                          <!-- Lesson icon based on type -->
                          <div class="mr-3 text-gray-500">
                            <svg
                              v-if="lesson.type === 'video'"
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5"
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
                              class="h-5 w-5"
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
                              class="h-5 w-5"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                              />
                            </svg>
                            <svg
                              v-else
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5"
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

                          <div>
                            <div
                              class="font-medium text-gray-900 flex items-center"
                            >
                              {{ lesson.title }}
                              <span
                                v-if="lesson.isPreview"
                                class="ml-2 text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded"
                              >
                                Preview
                              </span>
                            </div>
                            <div class="text-xs text-gray-500">
                              {{
                                lesson.type.charAt(0).toUpperCase() +
                                lesson.type.slice(1)
                              }}
                            </div>
                          </div>
                        </div>

                        <div class="flex items-center">
                          <!-- Lesson duration -->
                          <span class="text-sm text-gray-600 mr-2">{{
                            lesson.duration
                          }}</span>

                          <!-- Preview or locked icon -->
                          <NuxtLink
                            v-if="lesson.isPreview || course.isEnrolled"
                            :to="`/courses/${course.id}/learn/${lesson.id}`"
                            class="text-indigo-600 hover:text-indigo-800"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                clip-rule="evenodd"
                              />
                            </svg>
                          </NuxtLink>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tab content - Instructors -->
              <div v-if="activeTab === 'instructors'" class="space-y-8">
                <div
                  v-for="instructor in course.instructors"
                  :key="instructor.id"
                  class="flex flex-col md:flex-row items-start gap-6"
                >
                  <div class="flex-shrink-0">
                    <img
                      :src="instructor.avatar"
                      :alt="instructor.name"
                      class="w-24 h-24 rounded-full object-cover"
                    />
                  </div>

                  <div class="flex-grow">
                    <div
                      class="flex flex-col md:flex-row md:items-center justify-between mb-2"
                    >
                      <h3 class="text-xl font-bold text-gray-900">
                        {{ instructor.name }}
                      </h3>
                      <NuxtLink
                        :to="`/instructors/${instructor.id}`"
                        class="text-indigo-60 hover:text-indigo-800 text-sm font-medium"
                      >
                        View Profile
                      </NuxtLink>
                    </div>

                    <div class="text-gray-600 font-medium mb-2">
                      {{ instructor.role }}
                    </div>

                    <div
                      class="flex flex-wrap gap-6 mb-4 text-sm text-gray-600"
                    >
                      <div class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 mr-2 text-yellow-500"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                        <span
                          >{{ instructor.rating.toFixed(1) }} Instructor
                          Rating</span
                        >
                      </div>

                      <div class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 mr-2 text-indigo-500"
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
                        <span>{{ instructor.courses }} Courses</span>
                      </div>

                      <div class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 mr-2 text-green-500"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                          />
                        </svg>
                        <span
                          >{{
                            instructor.students.toLocaleString()
                          }}
                          Students</span
                        >
                      </div>
                    </div>

                    <p class="text-gray-700">
                      {{ instructor.bio }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Tab content - Reviews -->
              <div v-if="activeTab === 'reviews'" class="space-y-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-xl font-bold text-gray-900">
                    Student Feedback
                  </h3>

                  <div class="flex items-center">
                    <div class="flex text-yellow-400 mr-2">
                      <svg
                        v-for="i in 5"
                        :key="i"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        :class="
                          i <= Math.round(course.rating) ? '' : 'text-gray-300 '
                        "
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{
                      course.rating.toFixed(1)
                    }}</span>
                    <span class="text-gray-600 ml-2"
                      >({{ course.reviewCount.toLocaleString() }} reviews)</span
                    >
                  </div>
                </div>

                <!-- Reviews list -->
                <div class="space-y-6">
                  <div
                    v-for="review in course.reviews"
                    :key="review.id"
                    class="border-b border-gray-200 pb-6 last:border-b-0"
                  >
                    <div class="flex justify-between mb-2">
                      <div class="flex items-center">
                        <img
                          :src="review.user.avatar"
                          :alt="review.user.name"
                          class="w-10 h-10 rounded-full object-cover mr-3"
                        />
                        <div>
                          <div class="font-medium text-gray-900">
                            {{ review.user.name }}
                          </div>
                          <div class="text-xs text-gray-500">
                            {{ review.date }}
                          </div>
                        </div>
                      </div>

                      <div class="flex text-yellow-400">
                        <svg
                          v-for="i in 5"
                          :key="i"
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          :class="i <= review.rating ? '' : 'text-gray-300 '"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </div>

                    <p class="text-gray-700 mb-3">
                      {{ review.content }}
                    </p>

                    <div class="flex items-center text-sm text-gray-500">
                      <button
                        class="flex items-center hover:text-indigo-600 transition-colors"
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
                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
                          />
                        </svg>
                        Helpful ({{ review.helpfulCount }})
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Show more reviews button -->
                <div
                  v-if="course.reviewCount > course.reviews.length"
                  class="text-center"
                >
                  <button
                    class="text-indigo-60 hover:text-indigo-800 font-medium"
                  >
                    Show More Reviews
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Course sidebar - right column -->
          <div class="lg:col-span-1">
            <div class="sticky top-24">
              <!-- Course preview card -->
              <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Preview image -->
                <img
                  :src="course.image"
                  :alt="course.title"
                  class="w-full h-48 object-cover"
                />

                <!-- Card content -->
                <div class="p-6">
                  <!-- Price -->
                  <div class="mb-4">
                    <div class="flex items-center">
                      <span class="text-3xl font-bold text-gray-900 mr-2">{{
                        formattedPrice
                      }}</span>
                      <span
                        v-if="formattedOriginalPrice"
                        class="text-xl text-gray-500 line-through"
                      >
                        {{ formattedOriginalPrice }}
                      </span>
                      <span
                        v-if="discountPercentage"
                        class="ml-2 bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded"
                      >
                        {{ discountPercentage }}% OFF
                      </span>
                    </div>
                  </div>

                  <!-- Action buttons -->
                  <div class="mb-6 space-y-3">
                    <button
                      v-if="!course.isEnrolled"
                      class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-bold transition-colors flex items-center justify-center"
                      @click="enrollInCourse"
                    >
                      Enroll Now
                    </button>
                    <NuxtLink
                      v-else
                      :to="`/courses/${course.id}/learn`"
                      class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-bold transition-colors flex items-center justify-center"
                    >
                      Continue Learning
                    </NuxtLink>

                    <button
                      class="w-full border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-4 rounded-lg font-medium transition-colors"
                    >
                      Add to Wishlist
                    </button>
                  </div>

                  <!-- Course includes -->
                  <div class="mb-6">
                    <h4 class="font-bold text-gray-900 mb-3">
                      This course includes:
                    </h4>
                    <ul class="space-y-2">
                      <li
                        v-for="(feature, index) in course.features"
                        :key="index"
                        class="flex items-start"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        <span class="text-gray-700 text-sm">{{ feature }}</span>
                      </li>
                    </ul>
                  </div>

                  <!-- Share course -->
                  <div>
                    <h4 class="font-bold text-gray-900 mb-3">
                      Share this course
                    </h4>
                    <div class="flex space-x-3">
                      <button
                        class="text-gray-600 hover:text-indigo-600 transition-colors"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                          />
                        </svg>
                      </button>
                      <button
                        class="text-gray-600 hover:text-indigo-600 transition-colors"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"
                          />
                        </svg>
                      </button>
                      <button
                        class="text-gray-600 hover:text-indigo-600 transition-colors"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"
                          />
                        </svg>
                      </button>
                      <button
                        class="text-gray-600 hover:text-indigo-600 transition-colors"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-6 w-6"
                          fill="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M22.5 13.5a7.5 7.5 0 01-3.037 6.02l.525 3.065a.5.5 0 01-.722.487l-3.082-1.57a7.5 7.5 0 01-8.193-12.493A7.5 7.5 0 0122.5 13.5zM10.545 9.454a.5.5 0 10-.766.642L11.893 13l-2.114 2.904a.5.5 0 00.766.642l2.25-3a.5.5 0 000-.642l-2.25-3.45zm4.5 0a.5.5 0 00-.766.642L16.393 13l-2.114 2.904a.5.5 0 00.766.642l2.25-3a.5.5 0 000-.642l-2.25-3.45z"
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
      </div>
    </div>
  </div>
</template>

<style scoped>
.hide-scrollbar {
  scrollbar-width: none; /* Firefox */
}

.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}
</style>
