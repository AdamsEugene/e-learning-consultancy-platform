<!-- pages/dashboard/instructor/analytics.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta
definePageMeta({
  layout: "dashboard-instructor",
});

// Set page title
useHead({
  title: "Analytics Dashboard - Instructor Portal",
});

// Types
interface Course {
  id: number;
  title: string;
  category: string;
  students: number;
  revenue: number;
  rating: number;
  completion: number;
  enrollmentData: { date: string; count: number }[];
}

interface StudentDemographic {
  location: string;
  count: number;
  percentage: number;
}

interface PerformanceMetric {
  month: string;
  revenue: number;
  students: number;
  completionRate: number;
}

// State
const isLoading = ref(true);
const selectedTimeframe = ref("month");
const selectedCourse = ref<number | null>(null);
const showCourseDetails = ref(false);
const activeTab = ref("overview");

// Mock data for the analytics
const courses = ref<Course[]>([
  {
    id: 1,
    title: "Complete Web Development Bootcamp",
    category: "Web Development",
    students: 1245,
    revenue: 32560.75,
    rating: 4.8,
    completion: 78,
    enrollmentData: [
      { date: "2025-01", count: 85 },
      { date: "2025-02", count: 120 },
      { date: "2025-03", count: 165 },
      { date: "2025-04", count: 142 },
    ],
  },
  {
    id: 2,
    title: "Advanced JavaScript: From Fundamentals to Mastery",
    category: "Web Development",
    students: 968,
    revenue: 21450.3,
    rating: 4.9,
    completion: 82,
    enrollmentData: [
      { date: "2025-01", count: 65 },
      { date: "2025-02", count: 82 },
      { date: "2025-03", count: 110 },
      { date: "2025-04", count: 98 },
    ],
  },
  {
    id: 3,
    title: "UI/UX Design Principles",
    category: "Design",
    students: 756,
    revenue: 15240.8,
    rating: 4.7,
    completion: 75,
    enrollmentData: [
      { date: "2025-01", count: 55 },
      { date: "2025-02", count: 78 },
      { date: "2025-03", count: 95 },
      { date: "2025-04", count: 86 },
    ],
  },
  {
    id: 8,
    title: "Responsive Web Design Masterclass",
    category: "Web Development",
    students: 523,
    revenue: 9870.25,
    rating: 4.6,
    completion: 81,
    enrollmentData: [
      { date: "2025-01", count: 42 },
      { date: "2025-02", count: 58 },
      { date: "2025-03", count: 67 },
      { date: "2025-04", count: 62 },
    ],
  },
]);

const studentDemographics = ref<StudentDemographic[]>([
  { location: "United States", count: 1240, percentage: 35.5 },
  { location: "India", count: 685, percentage: 19.6 },
  { location: "UK", count: 420, percentage: 12.0 },
  { location: "Canada", count: 315, percentage: 9.0 },
  { location: "Germany", count: 280, percentage: 8.0 },
  { location: "Australia", count: 210, percentage: 6.0 },
  { location: "France", count: 175, percentage: 5.0 },
  { location: "Other", count: 170, percentage: 4.9 },
]);

const performanceMetrics = ref<PerformanceMetric[]>([
  { month: "January", revenue: 10250.3, students: 425, completionRate: 72 },
  { month: "February", revenue: 12680.45, students: 487, completionRate: 75 },
  { month: "March", revenue: 15820.6, students: 562, completionRate: 79 },
  { month: "April", revenue: 14350.2, students: 520, completionRate: 81 },
]);

const revenueBreakdown = ref([
  { category: "Course Sales", amount: 42500.25, percentage: 78 },
  { category: "Consultations", amount: 8750.5, percentage: 16 },
  { category: "Workshop Fees", amount: 3250.0, percentage: 6 },
]);

// Computed values
const totalStudents = computed(() => {
  return courses.value.reduce((total, course) => total + course.students, 0);
});

const totalRevenue = computed(() => {
  return courses.value.reduce((total, course) => total + course.revenue, 0);
});

const averageRating = computed(() => {
  const sum = courses.value.reduce((total, course) => total + course.rating, 0);
  return (sum / courses.value.length).toFixed(1);
});

const averageCompletionRate = computed(() => {
  const sum = courses.value.reduce(
    (total, course) => total + course.completion,
    0
  );
  return Math.round(sum / courses.value.length);
});

const currentMonthGrowth = computed(() => {
  // Compare current month with previous month
  const currentMonth =
    performanceMetrics.value[performanceMetrics.value.length - 1];
  const previousMonth =
    performanceMetrics.value[performanceMetrics.value.length - 2];

  return {
    revenue: (
      ((currentMonth.revenue - previousMonth.revenue) / previousMonth.revenue) *
      100
    ).toFixed(1),
    students: (
      ((currentMonth.students - previousMonth.students) /
        previousMonth.students) *
      100
    ).toFixed(1),
  };
});

const popularCourses = computed(() => {
  return [...courses.value].sort((a, b) => b.students - a.students).slice(0, 3);
});

const topRatedCourses = computed(() => {
  return [...courses.value].sort((a, b) => b.rating - a.rating).slice(0, 3);
});

const highestRevenueCourses = computed(() => {
  return [...courses.value].sort((a, b) => b.revenue - a.revenue).slice(0, 3);
});

// Methods

// Format currency
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 2,
  }).format(value);
};

// Format number with thousand separators
const formatNumber = (value: number) => {
  return new Intl.NumberFormat("en-US").format(value);
};

// Change timeframe
const changeTimeframe = (timeframe: string) => {
  selectedTimeframe.value = timeframe;
  // In a real application, you'd fetch data based on the timeframe
};

// Select course for detailed view
const selectCourse = (courseId: number) => {
  selectedCourse.value = courseId;
  showCourseDetails.value = true;
};

// Get course by ID
const getCourseById = (courseId: number) => {
  return courses.value.find((course) => course.id === courseId);
};

// Get enrollment chart data for a course
const getEnrollmentChartData = (courseId: number) => {
  const course = getCourseById(courseId);
  if (!course) return [];

  return course.enrollmentData;
};

// Get color for chart based on index
const getChartColor = (index: number) => {
  const colors = ["#4f46e5", "#8b5cf6", "#ec4899", "#f97316", "#06b6d4"];
  return colors[index % colors.length];
};

// Simulate loading data
const loadData = async () => {
  isLoading.value = true;

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 800));

  isLoading.value = false;
};

// Lifecycle hooks
onMounted(() => {
  loadData();
});
</script>

<template>
  <div class="py-6">
    <!-- Dashboard Header -->
    <div
      class="px-4 sm:px-6 md:px-8 mb-6 flex flex-col md:flex-row md:items-center md:justify-between"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
        <p class="text-gray-600 mt-1">
          Track your courses, students, and revenue metrics
        </p>
      </div>

      <div class="flex items-center space-x-3 mt-4 md:mt-0">
        <!-- Time period selector -->
        <div class="relative">
          <select
            v-model="selectedTimeframe"
            class="appearance-none pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
          >
            <option value="week">Last 7 Days</option>
            <option value="month">Last 30 Days</option>
            <option value="quarter">Last 3 Months</option>
            <option value="year">Last 12 Months</option>
          </select>
          <div
            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
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

        <!-- Export data button -->
        <button
          class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2 text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          Export Data
        </button>
      </div>
    </div>

    <!-- Stats Cards Section -->
    <div class="px-4 sm:px-6 md:px-8 mb-8">
      <div
        v-if="isLoading"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
      >
        <div
          v-for="i in 4"
          :key="i"
          class="bg-white rounded-lg shadow border border-gray-200 p-6 animate-pulse"
        >
          <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
          <div class="h-6 bg-gray-200 rounded w-1/2 mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Students Card -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 p-6 transition-transform duration-300 hover:shadow-lg"
        >
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
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-500">Total Students</h3>
              <div class="flex items-center">
                <p class="text-3xl font-bold text-gray-900">
                  {{ formatNumber(totalStudents) }}
                </p>
                <span
                  class="ml-2 text-sm font-medium text-green-600 flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 15l7-7 7 7"
                    />
                  </svg>
                  {{ currentMonthGrowth.students }}%
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 p-6 transition-transform duration-300 hover:shadow-lg"
        >
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-500">Total Revenue</h3>
              <div class="flex items-center">
                <p class="text-3xl font-bold text-gray-900">
                  {{ formatCurrency(totalRevenue) }}
                </p>
                <span
                  class="ml-2 text-sm font-medium text-green-600 flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 15l7-7 7 7"
                    />
                  </svg>
                  {{ currentMonthGrowth.revenue }}%
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Avg. Rating Card -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 p-6 transition-transform duration-300 hover:shadow-lg"
        >
          <div class="flex items-center">
            <div class="bg-yellow-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-yellow-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-500">Average Rating</h3>
              <div class="flex items-center">
                <p class="text-3xl font-bold text-gray-900">
                  {{ averageRating }}
                </p>
                <div class="ml-2 flex">
                  <svg
                    v-for="i in 5"
                    :key="i"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    :class="
                      i <= Math.round(Number(averageRating))
                        ? 'text-yellow-400'
                        : 'text-gray-300'
                    "
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Completion Rate -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 p-6 transition-transform duration-300 hover:shadow-lg"
        >
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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-medium text-gray-500">Completion Rate</h3>
              <div class="flex items-center">
                <p class="text-3xl font-bold text-gray-900">
                  {{ averageCompletionRate }}%
                </p>
                <div
                  class="ml-3 bg-gray-200 h-2 w-24 rounded-full overflow-hidden"
                >
                  <div
                    class="h-full bg-blue-600 rounded-full"
                    :style="{ width: `${averageCompletionRate}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="px-4 sm:px-6 md:px-8 mb-6">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'overview'"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            :class="
              activeTab === 'overview'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Overview
          </button>
          <button
            @click="activeTab = 'courses'"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            :class="
              activeTab === 'courses'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Courses
          </button>
          <button
            @click="activeTab = 'students'"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            :class="
              activeTab === 'students'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Students
          </button>
          <button
            @click="activeTab = 'revenue'"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            :class="
              activeTab === 'revenue'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Revenue
          </button>
        </nav>
      </div>
    </div>

    <!-- Tab Content -->
    <div class="px-4 sm:px-6 md:px-8 mb-8">
      <!-- Overview Tab Content -->
      <div v-if="activeTab === 'overview'" class="space-y-6">
        <!-- Performance Metrics Chart Card -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Performance Metrics
          </h3>
          <div v-if="isLoading" class="h-72 flex items-center justify-center">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
          <div v-else class="h-72">
            <!-- This would be a chart in a real implementation -->
            <div
              class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="text-center">
                <p class="text-gray-500 mb-2">Monthly Performance Chart</p>
                <p class="text-sm text-gray-400">
                  (Placeholder for Chart.js or similar library)
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Performing Courses -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Top Performing Courses
          </h3>
          <div v-if="isLoading" class="animate-pulse space-y-4">
            <div v-for="i in 3" :key="i" class="h-12 bg-gray-200 rounded"></div>
          </div>
          <div v-else>
            <div
              class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg"
            >
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                    >
                      Course
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Students
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Revenue
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Rating
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Completion
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr
                    v-for="course in popularCourses"
                    :key="course.id"
                    @click="selectCourse(course.id)"
                    class="cursor-pointer hover:bg-gray-50"
                  >
                    <td
                      class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6"
                    >
                      <div class="flex items-center">
                        <div
                          class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 flex items-center justify-center"
                        >
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
                        <div class="ml-4">
                          <div class="font-medium text-gray-900">
                            {{ course.title }}
                          </div>
                          <div class="text-gray-500">{{ course.category }}</div>
                        </div>
                      </div>
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatNumber(course.students) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatCurrency(course.revenue) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      <div class="flex items-center">
                        <span>{{ course.rating.toFixed(1) }}</span>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-yellow-400 ml-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      <div class="flex items-center">
                        <span>{{ course.completion }}%</span>
                        <div
                          class="ml-2 bg-gray-200 h-1.5 w-16 rounded-full overflow-hidden"
                        >
                          <div
                            class="h-full rounded-full"
                            :style="{
                              width: `${course.completion}%`,
                              backgroundColor:
                                course.completion > 80
                                  ? '#10B981'
                                  : course.completion > 60
                                  ? '#F59E0B'
                                  : '#EF4444',
                            }"
                          ></div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Student Demographics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Student Demographics
            </h3>
            <div v-if="isLoading" class="h-64 flex items-center justify-center">
              <div
                class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
              ></div>
            </div>
            <div v-else class="h-64">
              <!-- This would be a chart in a real implementation -->
              <div
                class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
              >
                <div class="text-center">
                  <p class="text-gray-500 mb-2">Student Location Chart</p>
                  <p class="text-sm text-gray-400">
                    (Placeholder for Chart.js or similar library)
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Revenue Breakdown -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Revenue Breakdown
            </h3>
            <div v-if="isLoading" class="h-64 flex items-center justify-center">
              <div
                class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
              ></div>
            </div>
            <div v-else class="h-64">
              <!-- This would be a chart in a real implementation -->
              <div
                class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
              >
                <div class="text-center">
                  <p class="text-gray-500 mb-2">Revenue Breakdown Chart</p>
                  <p class="text-sm text-gray-400">
                    (Placeholder for Chart.js or similar library)
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Courses Tab Content -->
      <div v-if="activeTab === 'courses'" class="space-y-6">
        <!-- All Courses Performance -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              Course Performance
            </h3>
            <NuxtLink
              to="/dashboard/instructor/courses"
              class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
            >
              View All Courses
            </NuxtLink>
          </div>

          <div v-if="isLoading" class="animate-pulse space-y-4">
            <div v-for="i in 4" :key="i" class="h-14 bg-gray-200 rounded"></div>
          </div>
          <div v-else>
            <div
              class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg"
            >
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                    >
                      Course
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Students
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Rating
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Revenue
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Completion
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                      <span class="sr-only">View</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr
                    v-for="course in courses"
                    :key="course.id"
                    class="hover:bg-gray-50"
                  >
                    <td
                      class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6"
                    >
                      <div class="font-medium text-gray-900">
                        {{ course.title }}
                      </div>
                      <div class="text-gray-500">{{ course.category }}</div>
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatNumber(course.students) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      <div class="flex items-center">
                        <span>{{ course.rating.toFixed(1) }}</span>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-yellow-400 ml-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatCurrency(course.revenue) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      <div class="flex items-center">
                        <span>{{ course.completion }}%</span>
                        <div
                          class="ml-2 bg-gray-200 h-1.5 w-16 rounded-full overflow-hidden"
                        >
                          <div
                            class="h-full rounded-full"
                            :style="{
                              width: `${course.completion}%`,
                              backgroundColor:
                                course.completion > 80
                                  ? '#10B981'
                                  : course.completion > 60
                                  ? '#F59E0B'
                                  : '#EF4444',
                            }"
                          ></div>
                        </div>
                      </div>
                    </td>
                    <td
                      class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6"
                    >
                      <button
                        @click="selectCourse(course.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        View<span class="sr-only">, {{ course.title }}</span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Course Enrollment Trends -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Enrollment Trends
          </h3>
          <div v-if="isLoading" class="h-72 flex items-center justify-center">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
          <div v-else class="h-72">
            <!-- This would be a chart in a real implementation -->
            <div
              class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="text-center">
                <p class="text-gray-500 mb-2">Course Enrollment Trends</p>
                <p class="text-sm text-gray-400">
                  (Placeholder for Chart.js or similar library)
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Completion Analysis -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Completion Analysis
          </h3>
          <div v-if="isLoading" class="h-72 flex items-center justify-center">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
          <div v-else class="h-72">
            <!-- This would be a chart in a real implementation -->
            <div
              class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="text-center">
                <p class="text-gray-500 mb-2">Course Completion Analysis</p>
                <p class="text-sm text-gray-400">
                  (Placeholder for Chart.js or similar library)
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Students Tab Content -->
      <div v-if="activeTab === 'students'" class="space-y-6">
        <!-- Student Growth Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Student Growth</h3>
          <div v-if="isLoading" class="h-72 flex items-center justify-center">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
          <div v-else class="h-72">
            <!-- This would be a chart in a real implementation -->
            <div
              class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="text-center">
                <p class="text-gray-500 mb-2">Student Growth Over Time</p>
                <p class="text-sm text-gray-400">
                  (Placeholder for Chart.js or similar library)
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Demographics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Geographic Distribution
            </h3>
            <div v-if="isLoading" class="animate-pulse space-y-4">
              <div
                v-for="i in 5"
                :key="i"
                class="h-8 bg-gray-200 rounded"
              ></div>
            </div>
            <div v-else>
              <div class="space-y-4">
                <div
                  v-for="(demographic, index) in studentDemographics"
                  :key="index"
                  class="flex items-center"
                >
                  <div class="w-32 text-sm text-gray-500">
                    {{ demographic.location }}
                  </div>
                  <div class="flex-1">
                    <div class="relative pt-1">
                      <div class="flex items-center justify-between">
                        <div>
                          <span
                            class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full"
                            :style="{
                              backgroundColor: getChartColor(index) + '20',
                              color: getChartColor(index),
                            }"
                          >
                            {{ demographic.percentage }}%
                          </span>
                        </div>
                        <div class="text-right">
                          <span
                            class="text-xs font-semibold inline-block text-gray-600"
                          >
                            {{ formatNumber(demographic.count) }} students
                          </span>
                        </div>
                      </div>
                      <div
                        class="overflow-hidden h-2 mb-1 text-xs flex rounded bg-gray-200 mt-1"
                      >
                        <div
                          :style="{
                            width: `${demographic.percentage}%`,
                            backgroundColor: getChartColor(index),
                          }"
                          class="transition-all duration-500 ease-out"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Engagement Metrics
            </h3>
            <div v-if="isLoading" class="h-64 flex items-center justify-center">
              <div
                class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
              ></div>
            </div>
            <div v-else class="h-64">
              <!-- This would be a chart in a real implementation -->
              <div
                class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
              >
                <div class="text-center">
                  <p class="text-gray-500 mb-2">Student Engagement Chart</p>
                  <p class="text-sm text-gray-400">
                    (Placeholder for Chart.js or similar library)
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Student Feedback Analysis -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Feedback Analysis
          </h3>
          <div v-if="isLoading" class="h-72 flex items-center justify-center">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
          <div v-else class="h-72">
            <!-- This would be a chart in a real implementation -->
            <div
              class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="text-center">
                <p class="text-gray-500 mb-2">Student Feedback Analysis</p>
                <p class="text-sm text-gray-400">
                  (Placeholder for Chart.js or similar library)
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Tab Content -->
      <div v-if="activeTab === 'revenue'" class="space-y-6">
        <!-- Revenue Trends Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trends</h3>
          <div v-if="isLoading" class="h-72 flex items-center justify-center">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
          <div v-else class="h-72">
            <!-- This would be a chart in a real implementation -->
            <div
              class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
            >
              <div class="text-center">
                <p class="text-gray-500 mb-2">Revenue Over Time</p>
                <p class="text-sm text-gray-400">
                  (Placeholder for Chart.js or similar library)
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Sources and Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Revenue Sources
            </h3>
            <div v-if="isLoading" class="h-64 flex items-center justify-center">
              <div
                class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
              ></div>
            </div>
            <div v-else class="h-64">
              <!-- This would be a chart in a real implementation -->
              <div
                class="h-full flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200"
              >
                <div class="text-center">
                  <p class="text-gray-500 mb-2">Revenue Sources Chart</p>
                  <p class="text-sm text-gray-400">
                    (Placeholder for Chart.js or similar library)
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Revenue Breakdown
            </h3>
            <div v-if="isLoading" class="animate-pulse space-y-4">
              <div
                v-for="i in 3"
                :key="i"
                class="h-12 bg-gray-200 rounded"
              ></div>
            </div>
            <div v-else>
              <div class="space-y-6">
                <div
                  v-for="(item, index) in revenueBreakdown"
                  :key="index"
                  class="space-y-2"
                >
                  <div class="flex justify-between items-center">
                    <div class="font-medium text-gray-700">
                      {{ item.category }}
                    </div>
                    <div class="text-gray-900 font-bold">
                      {{ formatCurrency(item.amount) }}
                    </div>
                  </div>
                  <div class="relative pt-1">
                    <div
                      class="overflow-hidden h-2 text-xs flex rounded bg-gray-200"
                    >
                      <div
                        :style="{
                          width: `${item.percentage}%`,
                          backgroundColor: getChartColor(index),
                        }"
                        class="transition-all duration-500 ease-out"
                      ></div>
                    </div>
                    <div class="text-right mt-1">
                      <span
                        class="text-xs font-semibold inline-block text-gray-600"
                      >
                        {{ item.percentage }}% of total
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Revenue Courses -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Top Revenue Courses
          </h3>
          <div v-if="isLoading" class="animate-pulse space-y-4">
            <div v-for="i in 3" :key="i" class="h-14 bg-gray-200 rounded"></div>
          </div>
          <div v-else>
            <div
              class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg"
            >
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                    >
                      Course
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Students
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Price
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Revenue
                    </th>
                    <th
                      scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                    >
                      Growth
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr
                    v-for="course in highestRevenueCourses"
                    :key="course.id"
                    class="hover:bg-gray-50 cursor-pointer"
                    @click="selectCourse(course.id)"
                  >
                    <td
                      class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6"
                    >
                      <div class="font-medium text-gray-900">
                        {{ course.title }}
                      </div>
                      <div class="text-gray-500">{{ course.category }}</div>
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatNumber(course.students) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatCurrency(course.revenue / course.students) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      {{ formatCurrency(course.revenue) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                    >
                      <span
                        class="text-green-600 font-medium flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 mr-1"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 15l7-7 7 7"
                          />
                        </svg>
                        {{ Math.floor(Math.random() * 20) + 5 }}%
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Course Details Modal -->
    <div v-if="showCourseDetails" class="fixed inset-0 overflow-y-auto z-50">
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="fixed inset-0 transition-opacity"
          aria-hidden="true"
          @click="showCourseDetails = false"
        >
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal content -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <div class="flex justify-between items-start">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ getCourseById(selectedCourse!)?.title }}
                  </h3>
                  <button
                    @click="showCourseDetails = false"
                    class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                  >
                    <span class="sr-only">Close</span>
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

                <!-- Course stats summary -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-500">Students</div>
                    <div class="text-2xl font-bold text-gray-900">
                      {{
                        formatNumber(
                          getCourseById(selectedCourse!)?.students || 0
                        )
                      }}
                    </div>
                  </div>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-500">Revenue</div>
                    <div class="text-2xl font-bold text-gray-900">
                      {{
                        formatCurrency(
                          getCourseById(selectedCourse!)?.revenue || 0
                        )
                      }}
                    </div>
                  </div>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-500">Rating</div>
                    <div
                      class="text-2xl font-bold text-gray-900 flex items-center"
                    >
                      {{ getCourseById(selectedCourse!)?.rating.toFixed(1) }}
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-yellow-400 ml-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </div>
                  </div>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-500">Completion Rate</div>
                    <div class="text-2xl font-bold text-gray-900">
                      {{ getCourseById(selectedCourse!)?.completion || 0 }}%
                    </div>
                  </div>
                </div>

                <!-- Enrollment chart -->
                <div class="mt-6">
                  <h4 class="text-md font-medium text-gray-900 mb-2">
                    Enrollment Trend
                  </h4>
                  <div
                    class="bg-gray-50 p-4 rounded-lg border border-gray-200 h-64 flex items-center justify-center"
                  >
                    <div class="text-center">
                      <p class="text-gray-500 mb-2">Enrollment Trend Chart</p>
                      <p class="text-sm text-gray-400">
                        (Placeholder for Chart.js or similar library)
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Other metrics -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h4 class="text-md font-medium text-gray-900 mb-2">
                      Student Engagement
                    </h4>
                    <div
                      class="bg-gray-50 p-4 rounded-lg border border-gray-200 h-48 flex items-center justify-center"
                    >
                      <div class="text-center">
                        <p class="text-gray-500 mb-2">
                          Student Engagement Chart
                        </p>
                        <p class="text-sm text-gray-400">
                          (Placeholder for Chart.js or similar library)
                        </p>
                      </div>
                    </div>
                  </div>
                  <div>
                    <h4 class="text-md font-medium text-gray-900 mb-2">
                      Content Consumption
                    </h4>
                    <div
                      class="bg-gray-50 p-4 rounded-lg border border-gray-200 h-48 flex items-center justify-center"
                    >
                      <div class="text-center">
                        <p class="text-gray-500 mb-2">
                          Content Consumption Chart
                        </p>
                        <p class="text-sm text-gray-400">
                          (Placeholder for Chart.js or similar library)
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <NuxtLink
              :to="`/dashboard/instructor/courses/${selectedCourse}`"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              View Course Details
            </NuxtLink>
            <button
              @click="showCourseDetails = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation for pulse loading */
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

/* Tab transitions */
.tab-content-enter-active,
.tab-content-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}
.tab-content-enter-from,
.tab-content-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Number counter animation */
@keyframes count-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-animate {
  animation: count-up 1s ease-out forwards;
}

/* Hover effects for interactive elements */
.hover-card {
  transition: transform 0.3s, box-shadow 0.3s;
}

.hover-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
