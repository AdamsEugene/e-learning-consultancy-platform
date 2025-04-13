<!-- pages/courses/[id].vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import type { Course } from "~/types/course";

// Get course ID from route
const route = useRoute();
const courseId = computed(() => Number(route.params.id));

// Course state
const course = ref<Course | null>(null);
const loading = ref(true);
const error = ref(false);
const activeTab = ref("overview");

// Fetch course data
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

// Handle tab switching
const setActiveTab = (tab: string) => {
  activeTab.value = tab;
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
});
</script>

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
      <CoursesDetailsCourseHeader :course="course" />

      <!-- Main course content -->
      <div class="container mx-auto px-4 md:px-6 -mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Course details - left column -->
          <div class="lg:col-span-2">
            <!-- Course content with tabs -->
            <CoursesDetailsCourseContent
              :course="course"
              :active-tab="activeTab"
              @set-tab="setActiveTab"
            />
          </div>

          <!-- Course sidebar - right column -->
          <div class="lg:col-span-1">
            <CoursesDetailsCourseSidebar
              :course="course"
              @enroll="enrollInCourse"
            />
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
