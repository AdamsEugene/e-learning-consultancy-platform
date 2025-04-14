<!-- pages/dashboard/courses.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

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

// Mock courses data - in a real app this would be from an API call
const courses = ref<Course[]>([
  {
    id: 1,
    title: "Complete Web Development Bootcamp",
    instructor: "Alex Johnson",
    category: "Web Development",
    progress: 68,
    lastAccessed: "2023-10-20T14:30:00",
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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
    thumbnail: "/images/courses/c3.jpg",
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

// Format date utility function
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format time ago utility function
const getTimeAgo = (dateString: string) => {
  const now = new Date();
  const date = new Date(dateString);
  const diffMs = now.getTime() - date.getTime();

  const diffSecs = Math.floor(diffMs / 1000);
  const diffMins = Math.floor(diffSecs / 60);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffDays > 30) {
    return formatDate(dateString);
  } else if (diffDays > 0) {
    return `${diffDays} day${diffDays > 1 ? "s" : ""} ago`;
  } else if (diffHours > 0) {
    return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
  } else if (diffMins > 0) {
    return `${diffMins} minute${diffMins > 1 ? "s" : ""} ago`;
  } else {
    return "Just now";
  }
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

// Animation flags for staggered entrance
onMounted(() => {
  // Simulate API loading delay
  setTimeout(() => {
    isLoading.value = false;

    // Small delay for animation start
    setTimeout(() => {
      isLoaded.value = true;
    }, 100);
  }, 800);
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">My Courses</h1>
      <p class="text-gray-600 mt-1">
        Manage your enrolled courses and track your learning progress
      </p>
    </div>

    <!-- Filters and controls -->
    <div
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 transition-all duration-500 transform"
      :class="{
        'opacity-0 translate-y-4': !isLoaded,
        'opacity-100 translate-y-0': isLoaded,
      }"
      style="transition-delay: 100ms"
    >
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Left side - Status filter tabs -->
        <div class="flex space-x-1 overflow-x-auto pb-1 -mx-1 px-1">
          <button
            v-for="status in ['all', 'active', 'completed', 'archived']"
            :key="status"
            @click="filters.status = status"
            class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-all duration-200"
            :class="[
              filters.status === status
                ? 'bg-indigo-600 text-white shadow-sm'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            {{ status.charAt(0).toUpperCase() + status.slice(1) }}
            <span
              class="ml-1 px-1.5 py-0.5 text-xs rounded-full font-bold"
              :class="[
                filters.status === status
                  ? 'bg-white text-indigo-600'
                  : 'bg-gray-200 text-gray-700',
              ]"
            >
              {{ statusCounts[status] }}
            </span>
          </button>
        </div>

        <!-- Right side - Search, category, and sort -->
        <div
          class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2"
        >
          <!-- Search input -->
          <div class="relative">
            <input
              type="text"
              placeholder="Search courses..."
              :value="debouncedSearch"
              @input="updateSearch"
              class="pl-9 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full sm:w-64"
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
          </div>

          <!-- Category dropdown -->
          <div class="relative">
            <select
              v-model="filters.category"
              class="appearance-none bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
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

          <!-- Sort dropdown -->
          <div class="relative">
            <select
              v-model="filters.sortBy"
              class="appearance-none bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
            >
              <option value="recent">Recently Accessed</option>
              <option value="progress">Progress</option>
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
      </div>
    </div>

    <!-- Courses grid -->
    <div v-if="isLoading" class="flex justify-center items-center py-20">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      ></div>
    </div>

    <div
      v-else-if="filteredCourses.length === 0"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center"
    >
      <div class="flex justify-center mb-4">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-gray-400"
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
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">No courses found</h3>
      <p class="text-gray-600 mb-6">
        Try adjusting your filters or search term
      </p>
      <button
        @click="
          filters = {
            status: 'all',
            category: 'all',
            sortBy: 'recent',
            search: '',
          }
        "
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
      >
        Clear Filters
      </button>
    </div>

    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Course cards with staggered animation -->
      <div
        v-for="(course, index) in filteredCourses"
        :key="course.id"
        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-500 transform hover:shadow-md"
        :class="{
          'opacity-0 translate-y-4': !isLoaded,
          'opacity-100 translate-y-0': isLoaded,
        }"
        :style="`transition-delay: ${200 + index * 50}ms`"
      >
        <!-- Course image with progress overlay -->
        <div class="relative">
          <img
            :src="course.thumbnail"
            :alt="course.title"
            class="w-full h-40 object-cover"
          />
          <!-- Status badge -->
          <div
            class="absolute top-3 left-3 px-2 py-1 text-xs font-bold rounded-full"
            :class="{
              'bg-indigo-100 text-indigo-700': course.status === 'active',
              'bg-green-100 text-green-700': course.status === 'completed',
              'bg-gray-100 text-gray-700': course.status === 'archived',
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
            class="absolute top-3 right-3 bg-yellow-100 text-yellow-700 px-2 py-1 text-xs font-bold rounded-full flex items-center"
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
              class="h-full transition-all duration-500"
              :class="{
                'bg-indigo-600': course.progress < 100,
                'bg-green-500': course.progress === 100,
              }"
              :style="{ width: `${course.progress}%` }"
            ></div>
          </div>
        </div>

        <!-- Course content -->
        <div class="p-5">
          <div class="flex items-start justify-between mb-2">
            <h3
              class="font-bold text-lg text-gray-900 hover:text-indigo-600 transition-colors line-clamp-2"
            >
              {{ course.title }}
            </h3>
            <div class="flex items-center ml-2 flex-shrink-0">
              <span class="flex items-center text-yellow-500 ml-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
                <span class="text-xs font-medium text-gray-700 ml-1">{{
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
          </div>

          <!-- Progress info -->
          <div class="flex items-center justify-between mb-4">
            <div class="text-sm font-medium">
              <span class="text-indigo-600">{{ course.completedLessons }}</span>
              <span class="text-gray-600"
                >/{{ course.totalLessons }} lessons</span
              >
            </div>
            <div class="text-sm font-medium text-gray-600">
              {{ course.progress }}% complete
            </div>
          </div>

          <!-- Next lesson indicator -->
          <div class="mb-4">
            <div class="text-xs text-gray-500 mb-1">NEXT LESSON</div>
            <div class="text-sm font-medium text-gray-800 truncate">
              {{ course.nextLesson }}
            </div>
          </div>

          <!-- Tags -->
          <div class="mb-4 flex flex-wrap gap-1">
            <span
              v-for="tag in course.tags"
              :key="tag"
              class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700"
            >
              {{ tag }}
            </span>
          </div>

          <!-- Last accessed info -->
          <div class="text-xs text-gray-500 mb-4">
            Last accessed {{ getTimeAgo(course.lastAccessed) }}
          </div>

          <!-- Action buttons -->
          <div class="flex space-x-2">
            <button
              v-if="course.progress < 100"
              @click="continueLearning(course.id)"
              class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium text-sm"
            >
              Continue Learning
            </button>
            <button
              v-else
              @click="viewCourse(course.id)"
              class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium text-sm"
            >
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
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path
                  fill-rule="evenodd"
                  d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Line clamp for multiline text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Smooth animations */
.transition-all {
  transition-property: all;
}

/* Improved scrollbars for the filters */
.overflow-x-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 9999px;
}

/* Hover effects for cards */
.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transform: translateY(-2px);
}
</style>
