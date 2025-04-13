<!-- pages/dashboard/courses.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";

// Set page layout
definePageMeta({
  layout: "dashboard",
});

// Page meta
useHead({
  title: "My Courses - Dashboard | LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "View and manage all your enrolled courses, track your progress, and continue learning.",
    },
  ],
});

// Course interface
interface Course {
  id: number;
  title: string;
  instructor: string;
  category: string;
  progress: number;
  lastAccessed: string;
  thumbnail: string;
  nextLesson: string;
  totalLessons: number;
  completedLessons: number;
  certificates?: boolean;
  status?: "active" | "completed" | "archived";
  rating?: number;
  description?: string;
  enrolledDate?: string;
  tags?: string[];
}

// Filter state
const filters = ref({
  status: "all", // all, active, completed, archived
  category: "all",
  sortBy: "recent", // recent, progress, title
  search: "",
});

// Loading state
const isLoading = ref(true);
const isLoaded = ref(false);
const showFilterPanel = ref(false);

// Stats animation
const showStats = ref(false);
const statsValues = ref({
  totalCourses: 0,
  activeCourses: 0,
  completedCourses: 0,
  totalHours: 0,
});

// View mode - grid or list
const viewMode = ref<"grid" | "list">("grid");

// Mock courses data - in a real app this would be from an API call
const courses = ref<Course[]>([
  {
    id: 1,
    title: "Complete Web Development Bootcamp",
    instructor: "Alex Johnson",
    category: "Web Development",
    progress: 68,
    lastAccessed: "2023-10-20T14:30:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "CSS Flexbox Layout",
    totalLessons: 42,
    completedLessons: 28,
    status: "active",
    rating: 4.8,
    description:
      "A comprehensive bootcamp that takes you from beginner to advanced web developer.",
    enrolledDate: "2023-09-01T00:00:00",
    tags: ["HTML", "CSS", "JavaScript", "React"],
  },
  {
    id: 2,
    title: "UI/UX Design Masterclass",
    instructor: "Sarah Chen",
    category: "Design",
    progress: 35,
    lastAccessed: "2023-10-19T09:15:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "User Research Methods",
    totalLessons: 36,
    completedLessons: 12,
    status: "active",
    rating: 4.7,
    description:
      "Learn to design beautiful and functional user interfaces with practical projects.",
    enrolledDate: "2023-09-15T00:00:00",
    tags: ["Figma", "UX Research", "Prototyping"],
  },
  {
    id: 3,
    title: "JavaScript Algorithms and Data Structures",
    instructor: "Michael Brown",
    category: "Programming",
    progress: 100,
    lastAccessed: "2023-10-21T16:45:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "Course Completed",
    totalLessons: 48,
    completedLessons: 48,
    status: "completed",
    certificates: true,
    rating: 4.9,
    description:
      "Master algorithms and data structures essential for technical interviews and efficient coding.",
    enrolledDate: "2023-08-10T00:00:00",
    tags: ["Algorithms", "Data Structures", "JavaScript"],
  },
  {
    id: 4,
    title: "Mobile App Development with React Native",
    instructor: "Emma Wilson",
    category: "Mobile Development",
    progress: 12,
    lastAccessed: "2023-10-18T11:20:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "Setting Up Your Development Environment",
    totalLessons: 38,
    completedLessons: 4,
    status: "active",
    rating: 4.6,
    description:
      "Build native mobile applications for iOS and Android using React Native.",
    enrolledDate: "2023-10-05T00:00:00",
    tags: ["React Native", "Mobile", "JavaScript"],
  },
  {
    id: 5,
    title: "Python Data Science Fundamentals",
    instructor: "Robert Zhang",
    category: "Data Science",
    progress: 92,
    lastAccessed: "2023-10-10T10:30:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "Machine Learning Introduction",
    totalLessons: 32,
    completedLessons: 29,
    status: "active",
    rating: 4.9,
    description:
      "Learn data analysis, visualization, and machine learning with Python.",
    enrolledDate: "2023-07-20T00:00:00",
    tags: ["Python", "Data Science", "Machine Learning"],
  },
  {
    id: 6,
    title: "Introduction to Digital Marketing",
    instructor: "Jennifer Lopez",
    category: "Marketing",
    progress: 100,
    lastAccessed: "2023-08-15T13:45:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "Course Completed",
    totalLessons: 24,
    completedLessons: 24,
    status: "archived",
    certificates: true,
    rating: 4.5,
    description:
      "Master the fundamentals of digital marketing strategies and tools.",
    enrolledDate: "2023-06-01T00:00:00",
    tags: ["SEO", "Social Media", "Content Marketing"],
  },
  {
    id: 7,
    title: "Advanced CSS and Sass",
    instructor: "David Miller",
    category: "Web Development",
    progress: 75,
    lastAccessed: "2023-10-17T09:30:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "Advanced CSS Animations",
    totalLessons: 36,
    completedLessons: 27,
    status: "active",
    rating: 4.8,
    description:
      "Take your CSS skills to the next level with Sass, Flexbox, and CSS Grid.",
    enrolledDate: "2023-09-10T00:00:00",
    tags: ["CSS", "Sass", "Animations", "Responsive Design"],
  },
  {
    id: 8,
    title: "Docker and Kubernetes for DevOps",
    instructor: "Thomas Clark",
    category: "DevOps",
    progress: 50,
    lastAccessed: "2023-10-15T15:20:00",
    thumbnail: "/api/placeholder/400/225",
    nextLesson: "Kubernetes Services",
    totalLessons: 30,
    completedLessons: 15,
    status: "active",
    rating: 4.7,
    description:
      "Learn containerization with Docker and orchestration with Kubernetes.",
    enrolledDate: "2023-09-20T00:00:00",
    tags: ["Docker", "Kubernetes", "DevOps", "Containers"],
  },
]);

// Get unique categories for filter options
const categories = computed(() => {
  const categorySet = new Set<string>();
  courses.value.forEach((course) => categorySet.add(course.category));
  return ["all", ...Array.from(categorySet)];
});

// Status counts for filter badges
const statusCounts = computed(() => {
  const counts = {
    all: courses.value.length,
    active: 0,
    completed: 0,
    archived: 0,
  };

  courses.value.forEach((course) => {
    if (course.status) {
      counts[course.status]++;
    }
  });

  return counts;
});

// Overall stats
const overallStats = computed(() => {
  const stats = {
    totalCourses: courses.value.length,
    activeCourses: 0,
    completedCourses: 0,
    totalHours: 0,
  };

  courses.value.forEach((course) => {
    if (course.status === "active") stats.activeCourses++;
    if (course.status === "completed") stats.completedCourses++;
    // Estimate hours based on lessons (assuming 20 minutes per lesson)
    stats.totalHours += (course.completedLessons * 20) / 60;
  });

  return stats;
});

// Filtered courses based on current filters
const filteredCourses = computed(() => {
  let result = [...courses.value];

  // Filter by status
  if (filters.value.status !== "all") {
    result = result.filter((course) => course.status === filters.value.status);
  }

  // Filter by category
  if (filters.value.category !== "all") {
    result = result.filter(
      (course) => course.category === filters.value.category
    );
  }

  // Apply search filter
  if (filters.value.search.trim() !== "") {
    const searchTerm = filters.value.search.toLowerCase();
    result = result.filter(
      (course) =>
        course.title.toLowerCase().includes(searchTerm) ||
        course.instructor.toLowerCase().includes(searchTerm) ||
        course.description?.toLowerCase().includes(searchTerm) ||
        course.tags?.some((tag) => tag.toLowerCase().includes(searchTerm))
    );
  }

  // Apply sorting
  switch (filters.value.sortBy) {
    case "recent":
      result.sort(
        (a, b) =>
          new Date(b.lastAccessed).getTime() -
          new Date(a.lastAccessed).getTime()
      );
      break;
    case "progress":
      result.sort((a, b) => b.progress - a.progress);
      break;
    case "title":
      result.sort((a, b) => a.title.localeCompare(b.title));
      break;
  }

  return result;
});

// Apply search filter when user stops typing
const debouncedSearch = ref("");
let searchTimeout: number | null = null;

const updateSearch = (event: Event) => {
  const target = event.target as HTMLInputElement;
  debouncedSearch.value = target.value;

  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = window.setTimeout(() => {
    filters.value.search = debouncedSearch.value;
  }, 300);
};

// View course details function
const viewCourse = (courseId: number) => {
  // In a real app, this would navigate to the course page
  console.log(`Navigate to course ${courseId}`);
  // Example: router.push(`/dashboard/courses/${courseId}`);
};

// Continue learning function - go to the next lesson
const continueLearning = (courseId: number) => {
  // In a real app, this would navigate to the next lesson
  console.log(`Continue learning course ${courseId}`);
  // Example: router.push(`/dashboard/courses/${courseId}/lessons/next`);
};

// Toggle filter panel
const toggleFilterPanel = () => {
  showFilterPanel.value = !showFilterPanel.value;
};

// Animate stats counters
const animateStats = () => {
  const duration = 1500; // Animation duration in ms
  const steps = 60; // Number of steps for the animation
  const interval = duration / steps;

  const stats = overallStats.value;
  let step = 0;

  const animation = setInterval(() => {
    step++;
    const progress = step / steps;

    statsValues.value = {
      totalCourses: Math.round(progress * stats.totalCourses),
      activeCourses: Math.round(progress * stats.activeCourses),
      completedCourses: Math.round(progress * stats.completedCourses),
      totalHours: Math.round(progress * stats.totalHours * 10) / 10,
    };

    if (step >= steps) {
      clearInterval(animation);
    }
  }, interval);
};

// Reset filters
const resetFilters = () => {
  filters.value = {
    status: "all",
    category: "all",
    sortBy: "recent",
    search: "",
  };
  debouncedSearch.value = "";
};

// Watch for filter changes to track empty results
watch(
  () => filteredCourses.value.length,
  (newCount) => {
    if (newCount === 0 && !isLoading.value) {
      // Maybe show a tooltip or highlight the reset button
    }
  }
);

// Animation flags for staggered entrance
onMounted(() => {
  // Simulate API loading delay
  setTimeout(() => {
    isLoading.value = false;

    // Small delay for animation start
    setTimeout(() => {
      isLoaded.value = true;

      // Animate stats after page loads
      setTimeout(() => {
        showStats.value = true;
        animateStats();
      }, 500);
    }, 100);
  }, 800);
});
</script>

<template>
  <div>
    <!-- Page header with beautiful gradient background -->
    <div class="relative mb-8 rounded-xl overflow-hidden">
      <div
        class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600"
      ></div>
      <div class="absolute inset-0 bg-pattern opacity-10"></div>

      <!-- Animated particles in background -->
      <div class="absolute inset-0 overflow-hidden">
        <div
          v-for="i in 20"
          :key="i"
          class="absolute rounded-full bg-white animate-float"
          :style="{
            left: `${Math.random() * 100}%`,
            top: `${Math.random() * 100}%`,
            width: `${1 + Math.random() * 3}px`,
            height: `${1 + Math.random() * 3}px`,
            opacity: 0.1 + Math.random() * 0.3,
            animationDuration: `${15 + Math.random() * 30}s`,
            animationDelay: `${Math.random() * 5}s`,
          }"
        ></div>
      </div>

      <div class="relative z-10 px-6 py-10 text-white">
        <div class="flex flex-col md:flex-row md:items-end justify-between">
          <div>
            <h1 class="text-3xl font-bold mb-2 md:text-4xl slide-in-left">
              My Learning Journey
            </h1>
            <p
              class="text-white/80 slide-in-left"
              style="animation-delay: 200ms"
            >
              Track your progress, continue your courses, and expand your
              knowledge
            </p>
          </div>

          <!-- Stats cards with animations -->
          <div
            class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 md:mt-0 fade-in"
            style="animation-delay: 400ms"
          >
            <div
              v-for="(stat, index) in [
                {
                  label: 'Courses',
                  value: statsValues.totalCourses,
                  icon: 'book',
                },
                {
                  label: 'Active',
                  value: statsValues.activeCourses,
                  icon: 'play',
                },
                {
                  label: 'Completed',
                  value: statsValues.completedCourses,
                  icon: 'check',
                },
                {
                  label: 'Hours',
                  value: statsValues.totalHours,
                  icon: 'clock',
                  suffix: 'h',
                },
              ]"
              :key="index"
              class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center border border-white/20 flex flex-col items-center justify-center transform transition-transform hover:scale-105 hover:bg-white/20"
              :class="{ 'stats-visible': showStats }"
            >
              <div class="text-white/70 text-xs mb-1">{{ stat.label }}</div>
              <div class="text-2xl font-bold mb-1">
                {{ stat.value }}{{ stat.suffix || "" }}
              </div>
              <div class="text-white/80">
                <svg
                  v-if="stat.icon === 'book'"
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
                  v-if="stat.icon === 'play'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-if="stat.icon === 'check'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
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
                  v-if="stat.icon === 'clock'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controls section with filters -->
    <div
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 transition-all duration-500 fade-in"
      style="animation-delay: 300ms"
    >
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4"
      >
        <!-- Left side controls -->
        <div class="flex items-center gap-3">
          <!-- Toggle filter panel button -->
          <button
            @click="toggleFilterPanel"
            class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg font-medium hover:bg-indigo-100 transition-colors flex items-center gap-2"
            :class="{ 'bg-indigo-100': showFilterPanel }"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                clip-rule="evenodd"
              />
            </svg>
            Filters
            <span
              v-if="
                filters.status !== 'all' ||
                filters.category !== 'all' ||
                filters.search !== ''
              "
              class="bg-indigo-600 text-white text-xs font-bold h-5 w-5 flex items-center justify-center rounded-full"
            >
              {{
                (filters.status !== "all" ? 1 : 0) +
                (filters.category !== "all" ? 1 : 0) +
                (filters.search ? 1 : 0)
              }}
            </span>
          </button>

          <!-- View mode toggle -->
          <div class="bg-gray-100 rounded-lg flex overflow-hidden">
            <button
              @click="viewMode = 'grid'"
              class="p-2 transition-colors flex items-center justify-center"
              :class="
                viewMode === 'grid'
                  ? 'bg-indigo-600 text-white'
                  : 'text-gray-700'
              "
              title="Grid view"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                />
              </svg>
            </button>
            <button
              @click="viewMode = 'list'"
              class="p-2 transition-colors flex items-center justify-center"
              :class="
                viewMode === 'list'
                  ? 'bg-indigo-600 text-white'
                  : 'text-gray-700'
              "
              title="List view"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>

          <!-- Status quick filters with animated counter badges -->
          <div class="overflow-x-auto hide-scrollbar flex space-x-2">
            <button
              v-for="status in ['all', 'active', 'completed', 'archived']"
              :key="status"
              @click="filters.status = status"
              class="flex items-center whitespace-nowrap px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 border-2"
              :class="[
                filters.status === status
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-200 hover:bg-indigo-50',
              ]"
            >
              <span class="capitalize">{{
                status === "all" ? "All Courses" : status
              }}</span>
              <span
                class="ml-1.5 px-1.5 py-0.5 min-w-[20px] text-center text-xs rounded-full font-bold"
                :class="[
                  filters.status === status
                    ? 'bg-white text-indigo-600'
                    : 'bg-gray-100 text-gray-700',
                ]"
              >
                {{ statusCounts[status] }}
              </span>
            </button>
          </div>
        </div>

        <!-- Search input with animation -->
        <div class="relative flex-shrink-0 w-full md:w-64">
          <input
            type="text"
            placeholder="Search courses..."
            :value="debouncedSearch"
            @input="updateSearch"
            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300"
            :class="{ 'border-indigo-300 shadow-sm': debouncedSearch }"
          />
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <button
            v-if="debouncedSearch"
            @click="
              debouncedSearch = '';
              filters.search = '';
            "
            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Expanded filter controls -->
      <div
        v-if="showFilterPanel"
        class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-1 sm:grid-cols-3 gap-4 slide-down"
      >
        <!-- Category filter -->
        <div>
          <label
            for="category-filter"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Category
          </label>
          <div class="relative">
            <select
              id="category-filter"
              v-model="filters.category"
              class="appearance-none block w-full bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
            >
              <option
                v-for="category in categories"
                :key="category"
                :value="category"
              >
                {{ category === "all" ? "All Categories" : category }}
              </option>
            </select>
            <div
              class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Sort by filter -->
        <div>
          <label
            for="sort-filter"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Sort By
          </label>
          <div class="relative">
            <select
              id="sort-filter"
              v-model="filters.sortBy"
              class="appearance-none block w-full bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
            >
              <option value="recent">Recently Accessed</option>
              <option value="progress">Progress (High to Low)</option>
              <option value="title">Title (A-Z)</option>
            </select>
            <div
              class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Reset filters button -->
        <div class="flex items-end">
          <button
            @click="resetFilters"
            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1.5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Loading state with beautiful animation -->
    <div
      v-if="isLoading"
      class="flex flex-col items-center justify-center py-20"
    >
      <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
          <circle
            class="path"
            cx="50"
            cy="50"
            r="20"
            fill="none"
            stroke-width="3"
            stroke-miterlimit="10"
          />
        </svg>
      </div>
      <p class="mt-4 text-gray-500 text-center">Loading your courses...</p>
    </div>

    <!-- Empty state with illustration -->
    <div
      v-else-if="filteredCourses.length === 0"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center fade-in"
    >
      <div class="flex justify-center mb-6">
        <div class="relative">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-24 w-24 text-gray-200"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
            />
          </svg>
          <div
            class="absolute -top-2 -right-2 h-8 w-8 bg-amber-400 rounded-full flex items-center justify-center text-white animate-bounce"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">No courses found</h3>
      <p class="text-gray-600 mb-6 max-w-md mx-auto">
        We couldn't find any courses matching your current filters. Try
        adjusting your search or filter criteria.
      </p>
      <button
        @click="resetFilters"
        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium inline-flex items-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
            clip-rule="evenodd"
          />
        </svg>
        Clear Filters
      </button>
    </div>

    <!-- Course grid view -->
    <div
      v-else-if="viewMode === 'grid'"
      class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 fade-in"
      style="animation-delay: 500ms"
    >
      <DashboardStudentCourseCard
        v-for="(course, index) in filteredCourses"
        :key="course.id"
        :course="course"
        :animation-delay="index * 100"
        @view="viewCourse"
        @continue="continueLearning"
      />
    </div>

    <!-- Course list view -->
    <div v-else class="space-y-4 fade-in" style="animation-delay: 500ms">
      <div
        v-for="(course, index) in filteredCourses"
        :key="course.id"
        class="bg-white rounded-xl overflow-hidden transition-all duration-500 transform border border-gray-200 hover:shadow-md flex flex-col md:flex-row"
        :class="{
          'opacity-0 translate-y-4': !isLoaded,
          'opacity-100 translate-y-0': isLoaded,
        }"
        :style="`transition-delay: ${200 + index * 50}ms;`"
      >
        <!-- Course image -->
        <div class="md:w-64 flex-shrink-0 relative">
          <img
            :src="course.thumbnail"
            :alt="course.title"
            class="w-full h-40 md:h-full object-cover"
          />

          <!-- Status badge -->
          <div
            class="absolute top-3 left-3 px-3 py-1 text-xs font-bold rounded-full shadow-lg backdrop-blur-sm"
            :class="{
              'bg-indigo-500/90 text-white': course.status === 'active',
              'bg-green-500/90 text-white': course.status === 'completed',
              'bg-gray-500/90 text-white': course.status === 'archived',
            }"
          >
            {{
              course.status === "active"
                ? "In Progress"
                : course.status === "completed"
                ? "Completed"
                : "Archived"
            }}
          </div>

          <!-- Certificate badge -->
          <div
            v-if="course.certificates"
            class="absolute top-3 right-3 bg-amber-500/90 text-white px-3 py-1 text-xs font-bold rounded-full flex items-center shadow-lg backdrop-blur-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-3 w-3 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            Certificate
          </div>

          <!-- Progress bar -->
          <div class="absolute bottom-0 left-0 right-0 h-2 bg-gray-200">
            <div
              class="h-full transition-all duration-700"
              :class="{
                'bg-gradient-to-r from-indigo-500 to-blue-500':
                  course.progress < 100,
                'bg-gradient-to-r from-green-500 to-emerald-500':
                  course.progress === 100,
              }"
              :style="{ width: `${course.progress}%` }"
            ></div>
          </div>
        </div>

        <!-- Course content -->
        <div class="p-5 flex-grow">
          <div class="flex items-start justify-between mb-2">
            <h3
              class="font-bold text-lg text-gray-900 hover:text-indigo-600 transition-colors"
            >
              {{ course.title }}
            </h3>
            <div class="flex items-center ml-2 flex-shrink-0">
              <span class="flex items-center text-amber-500">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
                <span class="text-sm font-medium text-gray-700 ml-1">{{
                  course.rating
                }}</span>
              </span>
            </div>
          </div>

          <!-- Instructor and category -->
          <div class="flex items-center text-sm text-gray-600 mb-3">
            <span>{{ course.instructor }}</span>
            <span class="mx-2">•</span>
            <span>{{ course.category }}</span>
            <span class="mx-2">•</span>
            <span class="text-xs">{{ course.progress }}% complete</span>
          </div>

          <!-- Progress info -->
          <div class="flex items-center mb-4">
            <div class="text-sm font-medium">
              <span class="text-indigo-600">{{ course.completedLessons }}</span>
              <span class="text-gray-600"
                >/{{ course.totalLessons }} lessons completed</span
              >
            </div>
          </div>

          <!-- Next lesson indicator -->
          <div class="mb-4">
            <div class="text-xs text-gray-500 mb-1">NEXT LESSON</div>
            <div class="text-sm font-medium text-gray-800">
              {{ course.nextLesson }}
            </div>
          </div>

          <!-- Tags -->
          <div class="mb-4 flex flex-wrap gap-1">
            <span
              v-for="tag in course.tags"
              :key="tag"
              class="px-2 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700 border border-indigo-100"
            >
              {{ tag }}
            </span>
          </div>

          <!-- Action buttons -->
          <div class="flex space-x-2 mt-2">
            <button
              v-if="course.progress < 100"
              @click="continueLearning(course.id)"
              class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium text-sm shadow-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1.5"
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
              v-else
              @click="viewCourse(course.id)"
              class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium text-sm shadow-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1.5"
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
              Review Course
            </button>
            <button
              @click="viewCourse(course.id)"
              class="flex-none bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2 px-3 rounded-lg transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty courses message with CTA -->
    <div
      v-if="courses.length === 0 && !isLoading"
      class="bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 fade-in"
    >
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-32 relative">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
      </div>
      <div class="p-8 text-center">
        <div
          class="w-20 h-20 bg-white rounded-full shadow-lg flex items-center justify-center mx-auto -mt-12 mb-4 border-4 border-white"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10 text-indigo-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
            />
          </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Start Your Learning Journey</h3>
        <p class="text-gray-600 mb-6 max-w-md mx-auto">
          You haven't enrolled in any courses yet. Explore our catalog and find
          courses to help you achieve your goals.
        </p>
        <NuxtLink
          to="/courses"
          class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-sm"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
            />
          </svg>
          Browse Courses
        </NuxtLink>
      </div>
    </div>

    <!-- Pagination if many courses -->
    <div
      v-if="filteredCourses.length > 9 && !isLoading"
      class="mt-8 flex justify-center fade-in"
      style="animation-delay: 800ms"
    >
      <nav class="flex items-center space-x-2" aria-label="Pagination">
        <button
          class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
        >
          <span class="sr-only">Previous</span>
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
          class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium"
        >
          1
        </button>
        <button
          class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
        >
          2
        </button>
        <button
          class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
        >
          3
        </button>
        <span class="px-3 py-2 text-gray-500">...</span>
        <button
          class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
        >
          <span class="sr-only">Next</span>
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
</template>

<style scoped>
/* Gradient background pattern */
.bg-pattern {
  background-image: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.1) 1px,
    transparent 1px
  );
  background-size: 20px 20px;
}

/* Custom loader animation */
.loader {
  position: relative;
  width: 60px;
  height: 60px;
}

.circular {
  animation: rotate 2s linear infinite;
  transform-origin: center center;
}

.path {
  stroke: #6366f1;
  stroke-dasharray: 89, 200;
  stroke-dashoffset: 0;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes rotate {
  100% {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 89, 200;
    stroke-dashoffset: -35px;
  }
  100% {
    stroke-dasharray: 89, 200;
    stroke-dashoffset: -124px;
  }
}

/* Floating particles animation */
@keyframes float {
  0%,
  100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-10px) translateX(5px);
  }
  50% {
    transform: translateY(0) translateX(10px);
  }
  75% {
    transform: translateY(10px) translateX(5px);
  }
}

.animate-float {
  animation-name: float;
  animation-duration: 15s;
  animation-iteration-count: infinite;
  animation-timing-function: ease-in-out;
}

/* Fade in animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  opacity: 0;
  animation: fadeIn 0.8s ease-out forwards;
}

/* Slide in left animation */
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.slide-in-left {
  opacity: 0;
  animation: slideInLeft 0.8s ease-out forwards;
}

/* Slide down animation */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
    max-height: 0;
  }
  to {
    opacity: 1;
    transform: translateY(0);
    max-height: 500px;
  }
}

.slide-down {
  animation: slideDown 0.5s ease-out forwards;
}

/* Stats counter animation */
.stats-visible {
  animation: fadeIn 0.8s ease-out forwards;
}

/* Hide horizontal scrollbar but keep functionality */
.hide-scrollbar {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
</style>
