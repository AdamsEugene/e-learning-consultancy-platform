<!-- pages/dashboard/instructor/courses/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define types for course data
interface Course {
  id: number;
  title: string;
  category: string;
  thumbnail: string;
  status: "draft" | "published" | "archived" | "under_review";
  students: number;
  rating: number;
  price: number;
  revenue: number;
  completion: number;
  lastUpdated: string;
  isPublished: boolean;
}

// Set layout
definePageMeta({
  layout: "dashboard-instructor",
});

// Page meta
useHead({
  title: "My Courses - Instructor Dashboard",
});

// State for courses data
const courses = ref<Course[]>([]);
const isLoading = ref(true);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("recent");
const showDeleteModal = ref(false);
const courseToDelete = ref<Course | null>(null);
const deleteConfirmText = ref("");

// Fetch courses (mock data for now)
const fetchCourses = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    courses.value = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
        students: 1245,
        rating: 4.8,
        price: 89.99,
        revenue: 32560.75,
        completion: 100,
        lastUpdated: "2025-03-10T10:30:00Z",
        isPublished: true,
      },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Mastery",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
        students: 968,
        rating: 4.9,
        price: 79.99,
        revenue: 21450.3,
        completion: 100,
        lastUpdated: "2025-02-18T14:45:00Z",
        isPublished: true,
      },
      {
        id: 3,
        title: "UI/UX Design Principles",
        category: "Design",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
        students: 756,
        rating: 4.7,
        price: 69.99,
        revenue: 15240.8,
        completion: 100,
        lastUpdated: "2025-01-25T09:15:00Z",
        isPublished: true,
      },
      {
        id: 4,
        title: "React Native for Mobile Development",
        category: "Mobile Development",
        thumbnail: "/api/placeholder/400/225",
        status: "draft",
        students: 0,
        rating: 0,
        price: 89.99,
        revenue: 0,
        completion: 65,
        lastUpdated: "2025-04-02T11:20:00Z",
        isPublished: false,
      },
      {
        id: 5,
        title: "Data Science with Python",
        category: "Data Science",
        thumbnail: "/api/placeholder/400/225",
        status: "under_review",
        students: 0,
        rating: 0,
        price: 94.99,
        revenue: 0,
        completion: 100,
        lastUpdated: "2025-03-28T16:50:00Z",
        isPublished: false,
      },
      {
        id: 6,
        title: "Node.js API Development",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "archived",
        students: 342,
        rating: 4.3,
        price: 74.99,
        revenue: 7650.45,
        completion: 100,
        lastUpdated: "2024-10-15T08:30:00Z",
        isPublished: false,
      },
      {
        id: 7,
        title: "Cloud Architecture Fundamentals",
        category: "Cloud Computing",
        thumbnail: "/api/placeholder/400/225",
        status: "draft",
        students: 0,
        rating: 0,
        price: 99.99,
        revenue: 0,
        completion: 30,
        lastUpdated: "2025-04-05T13:40:00Z",
        isPublished: false,
      },
      {
        id: 8,
        title: "Responsive Web Design Masterclass",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
        students: 523,
        rating: 4.6,
        price: 64.99,
        revenue: 9870.25,
        completion: 100,
        lastUpdated: "2025-02-02T10:10:00Z",
        isPublished: true,
      },
    ];
  } catch (error) {
    console.error("Error fetching courses:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter courses based on active tab and search query
const filteredCourses = computed(() => {
  let result = [...courses.value];

  // Apply tab filter
  if (activeTab.value !== "all") {
    if (activeTab.value === "published") {
      result = result.filter((course) => course.status === "published");
    } else if (activeTab.value === "draft") {
      result = result.filter((course) => course.status === "draft");
    } else if (activeTab.value === "review") {
      result = result.filter((course) => course.status === "under_review");
    } else if (activeTab.value === "archived") {
      result = result.filter((course) => course.status === "archived");
    }
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (course) =>
        course.title.toLowerCase().includes(query) ||
        course.category.toLowerCase().includes(query)
    );
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.lastUpdated).getTime() - new Date(a.lastUpdated).getTime()
    );
  } else if (sortBy.value === "popular") {
    result.sort((a, b) => b.students - a.students);
  } else if (sortBy.value === "rating") {
    result.sort((a, b) => b.rating - a.rating);
  } else if (sortBy.value === "revenue") {
    result.sort((a, b) => b.revenue - a.revenue);
  } else if (sortBy.value === "title") {
    result.sort((a, b) => a.title.localeCompare(b.title));
  }

  return result;
});

// Summary statistics
const statistics = computed(() => {
  return {
    totalCourses: courses.value.length,
    publishedCourses: courses.value.filter((c) => c.status === "published")
      .length,
    draftCourses: courses.value.filter((c) => c.status === "draft").length,
    totalStudents: courses.value.reduce(
      (sum, course) => sum + course.students,
      0
    ),
    totalRevenue: courses.value.reduce(
      (sum, course) => sum + course.revenue,
      0
    ),
  };
});

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format number with commas
const formatNumber = (num: number) => {
  return num.toLocaleString("en-US");
};

// Format price with currency symbol
const formatPrice = (price: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Get status badge classes based on course status
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "published":
      return "bg-green-100 text-green-800";
    case "draft":
      return "bg-yellow-100 text-yellow-800";
    case "under_review":
      return "bg-blue-100 text-blue-800";
    case "archived":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Open delete confirmation modal
const confirmDelete = (course: Course) => {
  courseToDelete.value = course;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

// Delete a course
const deleteCourse = () => {
  if (
    !courseToDelete.value ||
    deleteConfirmText.value !== courseToDelete.value.title
  ) {
    return;
  }

  // In a real app, this would make an API call
  courses.value = courses.value.filter(
    (course) => course.id !== courseToDelete.value?.id
  );
  showDeleteModal.value = false;
  courseToDelete.value = null;
  deleteConfirmText.value = "";
};

// Duplicate a course
const duplicateCourse = (course: Course) => {
  // In a real app, this would make an API call
  const newCourse = {
    ...course,
    id: Math.max(...courses.value.map((c) => c.id)) + 1,
    title: `${course.title} (Copy)`,
    status: "draft" as const,
    students: 0,
    revenue: 0,
    lastUpdated: new Date().toISOString(),
    isPublished: false,
  };

  courses.value.push(newCourse);
};

// Load data when component mounts
onMounted(() => {
  fetchCourses();
});
</script>

<template>
  <div>
    <!-- Page header with stats and actions -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">My Courses</h1>
          <p class="text-gray-600">
            Manage your courses, track performance and create new content
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/dashboard/instructor/courses/create"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Create New Course</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-indigo-100 rounded-full p-3 mr-4">
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
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Courses</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.totalCourses }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-green-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-green-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Published Courses</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.publishedCourses }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-blue-600"
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
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Students</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatNumber(statistics.totalStudents) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-purple-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-purple-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatPrice(statistics.totalRevenue) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Tab navigation -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            @click="activeTab = 'all'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'all'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            All Courses
          </button>
          <button
            @click="activeTab = 'published'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'published'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Published
          </button>
          <button
            @click="activeTab = 'draft'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'draft'
                ? 'bg-yellow-100 text-yellow-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Drafts
          </button>
          <button
            @click="activeTab = 'review'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'review'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Under Review
          </button>
          <button
            @click="activeTab = 'archived'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'archived'
                ? 'bg-gray-200 text-gray-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Archived
          </button>
        </div>

        <!-- Search and sort controls -->
        <div class="flex items-center space-x-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search courses..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="recent">Recently Updated</option>
              <option value="popular">Most Popular</option>
              <option value="rating">Highest Rated</option>
              <option value="revenue">Highest Revenue</option>
              <option value="title">Title (A-Z)</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="i in 3"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="bg-gray-200 h-24 w-32 rounded"></div>
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Course list -->
    <div
      v-else-if="filteredCourses.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="course in filteredCourses"
        :key="`course-${course.id}`"
        class="p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors"
      >
        <div class="flex flex-col md:flex-row md:items-center">
          <!-- Course thumbnail and info -->
          <div class="flex items-start space-x-4 flex-1">
            <div class="flex-shrink-0">
              <img
                :src="course.thumbnail"
                :alt="course.title"
                class="h-24 w-32 object-cover rounded-lg"
              />
            </div>
            <div class="flex-1 min-w-0">
              <div
                class="flex flex-col md:flex-row md:items-center md:justify-between"
              >
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 truncate">
                    {{ course.title }}
                  </h3>
                  <div class="flex items-center mt-1 mb-2">
                    <span class="text-sm text-gray-600 mr-3">{{
                      course.category
                    }}</span>
                    <span
                      class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                      :class="getStatusBadgeClasses(course.status)"
                    >
                      {{ course.status.replace("_", " ") }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Course stats -->
              <div class="flex flex-wrap gap-4 mt-2">
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                    />
                  </svg>
                  <span class="text-sm text-gray-700"
                    >{{ formatNumber(course.students) }} students</span
                  >
                </div>

                <div v-if="course.rating > 0" class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  <span class="text-sm text-gray-700">{{
                    course.rating.toFixed(1)
                  }}</span>
                </div>

                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-700"
                    >Updated {{ formatDate(course.lastUpdated) }}</span
                  >
                </div>

                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                    />
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-700">{{
                    formatPrice(course.price)
                  }}</span>
                </div>

                <div v-if="course.revenue > 0" class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                    />
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-700"
                    >{{ formatPrice(course.revenue) }} revenue</span
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <div
              v-if="course.status === 'draft'"
              class="text-sm text-gray-600 mr-2 bg-yellow-50 px-2 py-1 rounded"
            >
              <span v-if="course.completion < 100"
                >{{ course.completion }}% complete</span
              >
            </div>

            <NuxtLink
              :to="`/dashboard/instructor/courses/${course.id}`"
              class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
              title="View Course"
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
            </NuxtLink>

            <NuxtLink
              :to="`/dashboard/instructor/courses/${course.id}/edit`"
              class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
              title="Edit Course"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                />
              </svg>
            </NuxtLink>

            <button
              @click="duplicateCourse(course)"
              class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
              title="Duplicate Course"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z"
                />
                <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
              </svg>
            </button>

            <button
              @click="confirmDelete(course)"
              class="p-2 rounded-lg text-gray-500 hover:bg-red-100 hover:text-red-700 transition-colors"
              title="Delete Course"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else
      class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
    >
      <div
        class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12 text-gray-400"
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
      <h3 class="text-lg font-medium text-gray-900 mb-2">No courses found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No courses match your search criteria."
            : "You haven't created any courses yet."
        }}
      </p>
      <NuxtLink
        to="/dashboard/instructor/courses/create"
        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
            clip-rule="evenodd"
          />
        </svg>
        <span>Create Your First Course</span>
      </NuxtLink>
    </div>

    <!-- Pagination (simplified for demo) -->
    <div v-if="filteredCourses.length > 0" class="mt-6 flex justify-center">
      <div
        class="bg-white px-4 py-3 flex items-center justify-between border border-gray-200 rounded-lg shadow sm:px-6"
      >
        <div class="flex-1 flex justify-between sm:hidden">
          <a
            href="#"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </a>
          <a
            href="#"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </a>
        </div>
        <div
          class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
        >
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">1</span> to
              <span class="font-medium">{{
                Math.min(10, filteredCourses.length)
              }}</span>
              of
              <span class="font-medium">{{ filteredCourses.length }}</span>
              results
            </p>
          </div>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <a
                href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                1
              </a>
              <span
                class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600"
              >
                2
              </span>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                3
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-red-600"
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
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Delete Course
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete "{{ courseToDelete?.title }}"? This
            action cannot be undone.
          </p>
        </div>

        <!-- Confirmation input -->
        <div class="mb-4">
          <label
            for="confirm-delete"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Please type
            <span class="font-semibold">{{ courseToDelete?.title }}</span> to
            confirm:
          </label>
          <input
            id="confirm-delete"
            v-model="deleteConfirmText"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="Type course title here"
          />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="deleteCourse"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="deleteConfirmText !== courseToDelete?.title"
            :class="{
              'opacity-50 cursor-not-allowed':
                deleteConfirmText !== courseToDelete?.title,
            }"
          >
            Delete Course
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
