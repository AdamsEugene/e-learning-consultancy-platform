<!-- pages/dashboard/admin/analytics.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page metadata using Nuxt's definePageMeta
definePageMeta({
  layout: "dashboard-admin",
});

// Set page title
useHead({
  title: "Reports & Analytics - Admin Dashboard",
});

// Dashboard state
const isLoading = ref(true);
const timeframeFilter = ref("last30days");
const chartType = ref("line");
const activeDataset = ref("revenue");
const selectedReport = ref("overview");

// Simulated analytics data
const analyticsData = ref({
  overview: {
    totalRevenue: 358750,
    coursesRevenue: 215600,
    consultancyRevenue: 143150,
    totalUsers: 3725,
    totalStudents: 1865,
    totalInstructors: 48,
    totalConsultants: 27,
    totalClients: 112,
    totalCourses: 150,
    totalProjects: 35,
    courseCompletionRate: 68,
    projectSuccessRate: 92,
    userGrowthRate: 12.4,
    revenueGrowthRate: 8.7,
  },
  // Monthly data for charting
  monthly: {
    revenue: [
      { month: "Jan", courses: 18200, consultancy: 12500, total: 30700 },
      { month: "Feb", courses: 17800, consultancy: 11000, total: 28800 },
      { month: "Mar", courses: 19500, consultancy: 13200, total: 32700 },
      { month: "Apr", courses: 17200, consultancy: 10800, total: 28000 },
      { month: "May", courses: 18900, consultancy: 12100, total: 31000 },
      { month: "Jun", courses: 17500, consultancy: 11800, total: 29300 },
      { month: "Jul", courses: 19800, consultancy: 12900, total: 32700 },
      { month: "Aug", courses: 21300, consultancy: 14200, total: 35500 },
      { month: "Sep", courses: 20500, consultancy: 12700, total: 33200 },
      { month: "Oct", courses: 22100, consultancy: 13500, total: 35600 },
      { month: "Nov", courses: 21500, consultancy: 14250, total: 35750 },
      { month: "Dec", courses: 21300, consultancy: 14100, total: 35400 },
    ],
    users: [
      {
        month: "Jan",
        students: 1625,
        instructors: 42,
        consultants: 22,
        clients: 98,
        total: 3442,
      },
      {
        month: "Feb",
        students: 1637,
        instructors: 43,
        consultants: 22,
        clients: 99,
        total: 3456,
      },
      {
        month: "Mar",
        students: 1652,
        instructors: 43,
        consultants: 23,
        clients: 101,
        total: 3474,
      },
      {
        month: "Apr",
        students: 1670,
        instructors: 44,
        consultants: 23,
        clients: 103,
        total: 3495,
      },
      {
        month: "May",
        students: 1695,
        instructors: 44,
        consultants: 24,
        clients: 104,
        total: 3522,
      },
      {
        month: "Jun",
        students: 1718,
        instructors: 45,
        consultants: 24,
        clients: 105,
        total: 3547,
      },
      {
        month: "Jul",
        students: 1742,
        instructors: 45,
        consultants: 25,
        clients: 106,
        total: 3573,
      },
      {
        month: "Aug",
        students: 1765,
        instructors: 46,
        consultants: 25,
        clients: 107,
        total: 3598,
      },
      {
        month: "Sep",
        students: 1792,
        instructors: 46,
        consultants: 26,
        clients: 108,
        total: 3627,
      },
      {
        month: "Oct",
        students: 1815,
        instructors: 47,
        consultants: 26,
        clients: 110,
        total: 3653,
      },
      {
        month: "Nov",
        students: 1841,
        instructors: 47,
        consultants: 27,
        clients: 111,
        total: 3681,
      },
      {
        month: "Dec",
        students: 1865,
        instructors: 48,
        consultants: 27,
        clients: 112,
        total: 3725,
      },
    ],
    courses: [
      {
        month: "Jan",
        published: 121,
        draft: 18,
        review: 5,
        archived: 3,
        total: 147,
      },
      {
        month: "Feb",
        published: 122,
        draft: 19,
        review: 4,
        archived: 3,
        total: 148,
      },
      {
        month: "Mar",
        published: 123,
        draft: 20,
        review: 3,
        archived: 3,
        total: 149,
      },
      {
        month: "Apr",
        published: 124,
        draft: 19,
        review: 4,
        archived: 3,
        total: 150,
      },
      {
        month: "May",
        published: 125,
        draft: 18,
        review: 3,
        archived: 4,
        total: 150,
      },
      {
        month: "Jun",
        published: 126,
        draft: 18,
        review: 3,
        archived: 4,
        total: 151,
      },
      {
        month: "Jul",
        published: 127,
        draft: 19,
        review: 2,
        archived: 4,
        total: 152,
      },
      {
        month: "Aug",
        published: 128,
        draft: 20,
        review: 2,
        archived: 4,
        total: 154,
      },
      {
        month: "Sep",
        published: 129,
        draft: 19,
        review: 3,
        archived: 4,
        total: 155,
      },
      {
        month: "Oct",
        published: 130,
        draft: 18,
        review: 3,
        archived: 5,
        total: 156,
      },
      {
        month: "Nov",
        published: 132,
        draft: 17,
        review: 3,
        archived: 5,
        total: 157,
      },
      {
        month: "Dec",
        published: 133,
        draft: 16,
        review: 2,
        archived: 5,
        total: 156,
      },
    ],
    projects: [
      { month: "Jan", active: 28, completed: 4, total: 32 },
      { month: "Feb", active: 29, completed: 4, total: 33 },
      { month: "Mar", active: 28, completed: 5, total: 33 },
      { month: "Apr", active: 29, completed: 4, total: 33 },
      { month: "May", active: 30, completed: 3, total: 33 },
      { month: "Jun", active: 31, completed: 3, total: 34 },
      { month: "Jul", active: 32, completed: 3, total: 35 },
      { month: "Aug", active: 32, completed: 4, total: 36 },
      { month: "Sep", active: 33, completed: 3, total: 36 },
      { month: "Oct", active: 34, completed: 3, total: 37 },
      { month: "Nov", active: 34, completed: 4, total: 38 },
      { month: "Dec", active: 35, completed: 4, total: 39 },
    ],
  },
  // Top performing items
  topItems: {
    courses: [
      {
        id: 1,
        title: "Full-Stack Web Development",
        students: 345,
        revenue: 28750,
        rating: 4.9,
      },
      {
        id: 2,
        title: "Advanced Data Science & Machine Learning",
        students: 287,
        revenue: 25830,
        rating: 4.8,
      },
      {
        id: 3,
        title: "UI/UX Design Masterclass",
        students: 264,
        revenue: 19800,
        rating: 4.9,
      },
      {
        id: 4,
        title: "Cloud Architecture & DevOps",
        students: 231,
        revenue: 18480,
        rating: 4.7,
      },
      {
        id: 5,
        title: "Mobile App Development with React Native",
        students: 218,
        revenue: 16350,
        rating: 4.8,
      },
    ],
    consultants: [
      {
        id: 1,
        name: "Sarah Parker",
        projects: 12,
        revenue: 36500,
        rating: 4.9,
      },
      {
        id: 2,
        name: "David Wilson",
        projects: 10,
        revenue: 32800,
        rating: 4.9,
      },
      {
        id: 3,
        name: "Linda Johnson",
        projects: 9,
        revenue: 28400,
        rating: 4.8,
      },
      {
        id: 4,
        name: "Michael Brown",
        projects: 8,
        revenue: 25600,
        rating: 4.7,
      },
      {
        id: 5,
        name: "Jennifer Davis",
        projects: 7,
        revenue: 21000,
        rating: 4.8,
      },
    ],
    instructors: [
      {
        id: 1,
        name: "Dr. Alex Thompson",
        courses: 8,
        students: 1245,
        revenue: 42500,
        rating: 4.9,
      },
      {
        id: 2,
        name: "Prof. Emma Rodriguez",
        courses: 6,
        students: 982,
        revenue: 35400,
        rating: 4.8,
      },
      {
        id: 3,
        name: "James Wilson, PhD",
        courses: 5,
        students: 876,
        revenue: 29800,
        rating: 4.9,
      },
      {
        id: 4,
        name: "Dr. Sophia Chen",
        courses: 4,
        students: 743,
        revenue: 26700,
        rating: 4.8,
      },
      {
        id: 5,
        name: "Robert Jackson, MSc",
        courses: 4,
        students: 695,
        revenue: 24200,
        rating: 4.7,
      },
    ],
    clients: [
      {
        id: 1,
        name: "Tech Innovations Inc.",
        projects: 5,
        consultations: 28,
        spending: 48500,
      },
      {
        id: 2,
        name: "Global Solutions Group",
        projects: 4,
        consultations: 22,
        spending: 39200,
      },
      {
        id: 3,
        name: "Future Systems LLC",
        projects: 3,
        consultations: 18,
        spending: 32700,
      },
      {
        id: 4,
        name: "Bright Ideas Co.",
        projects: 3,
        consultations: 15,
        spending: 28400,
      },
      {
        id: 5,
        name: "Digital Frontier",
        projects: 2,
        consultations: 12,
        spending: 21500,
      },
    ],
  },
  // Geographic distribution
  geoDistribution: {
    users: [
      { region: "North America", count: 1860, percentage: 49.9 },
      { region: "Europe", count: 935, percentage: 25.1 },
      { region: "Asia", count: 560, percentage: 15.0 },
      { region: "South America", count: 185, percentage: 5.0 },
      { region: "Australia/Oceania", count: 125, percentage: 3.4 },
      { region: "Africa", count: 60, percentage: 1.6 },
    ],
    revenue: [
      { region: "North America", amount: 179375, percentage: 50.0 },
      { region: "Europe", amount: 89688, percentage: 25.0 },
      { region: "Asia", amount: 53813, percentage: 15.0 },
      { region: "South America", amount: 17938, percentage: 5.0 },
      { region: "Australia/Oceania", amount: 12556, percentage: 3.5 },
      { region: "Africa", amount: 5381, percentage: 1.5 },
    ],
  },
});

// Reports available in the system
const availableReports = [
  { id: "overview", name: "Platform Overview", icon: "chart-bar" },
  { id: "users", name: "User Analytics", icon: "users" },
  { id: "courses", name: "Course Performance", icon: "book-open" },
  { id: "consultancy", name: "Consultancy Metrics", icon: "briefcase" },
  { id: "financial", name: "Financial Reports", icon: "cash" },
  { id: "geographic", name: "Geographic Distribution", icon: "globe" },
];

// Timeframe options
const timeframeOptions = [
  { value: "last7days", label: "Last 7 Days" },
  { value: "last30days", label: "Last 30 Days" },
  { value: "last90days", label: "Last 90 Days" },
  { value: "lastYear", label: "Last Year" },
  { value: "allTime", label: "All Time" },
];

// Format currency value
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
};

// Format number with commas
const formatNumber = (num: number) => {
  return num.toLocaleString("en-US");
};

// Format percentage
const formatPercentage = (value: number) => {
  return `${value}%`;
};

// Computed property to get data for the active chart
const chartData = computed(() => {
  // Simply return monthly data based on activeDataset
  return analyticsData.value.monthly[activeDataset.value];
});

// Get current overview data
const overview = computed(() => {
  return analyticsData.value.overview;
});

// Render the right icon based on name
const renderIcon = (iconName: string) => {
  switch (iconName) {
    case "chart-bar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
      </svg>`;
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
      </svg>`;
    case "briefcase":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "cash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "globe":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Function to create and update chart (simulated)
const updateChart = () => {
  // In a real application, this would create or update a chart using a library
  // like Chart.js, ApexCharts, etc.
  console.log("Chart updated with:", {
    type: chartType.value,
    dataset: activeDataset.value,
    timeframe: timeframeFilter.value,
    data: chartData.value,
  });
};

// Generate a PDF report (simulated)
const generatePDFReport = () => {
  alert(
    "Generating PDF report for " +
      selectedReport.value +
      " with timeframe " +
      timeframeFilter.value
  );
};

// Export data to CSV (simulated)
const exportToCSV = () => {
  alert(
    "Exporting data to CSV for " +
      selectedReport.value +
      " with timeframe " +
      timeframeFilter.value
  );
};

// Simulate data loading
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
    updateChart();
  }, 800);
});

// Watch for changes that should trigger chart updates
watch([chartType, activeDataset, timeframeFilter], () => {
  updateChart();
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
          <p class="mt-1 text-sm text-gray-600">
            View and analyze platform performance metrics and generate custom
            reports
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
          <button
            @click="generatePDFReport"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                clip-rule="evenodd"
              />
              <path
                d="M8 11h4m-2-2v4"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
            Generate PDF
          </button>
          <button
            @click="exportToCSV"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            Export CSV
          </button>
        </div>
      </div>
    </div>

    <!-- Report Selection and Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
      <!-- Report Selection Sidebar -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Available Reports
          </h2>
          <nav class="space-y-1">
            <button
              v-for="report in availableReports"
              :key="report.id"
              @click="selectedReport = report.id"
              class="flex items-center px-3 py-2 w-full text-left rounded-md transition-colors"
              :class="[
                selectedReport === report.id
                  ? 'bg-indigo-50 text-indigo-700 font-medium'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
              ]"
            >
              <span
                class="flex-shrink-0 mr-3"
                v-html="renderIcon(report.icon)"
              ></span>
              <span class="truncate">{{ report.name }}</span>
            </button>
          </nav>
        </div>
      </div>

      <!-- Filter controls and Chart display area -->
      <div class="lg:col-span-4 space-y-6">
        <!-- Filter Controls -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center space-x-4">
              <div>
                <label
                  for="timeframe"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Time Period</label
                >
                <select
                  id="timeframe"
                  v-model="timeframeFilter"
                  class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option
                    v-for="option in timeframeOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>

              <div>
                <label
                  for="dataset"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Dataset</label
                >
                <select
                  id="dataset"
                  v-model="activeDataset"
                  class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option value="revenue">Revenue</option>
                  <option value="users">Users</option>
                  <option value="courses">Courses</option>
                  <option value="projects">Projects</option>
                </select>
              </div>
            </div>

            <div class="mt-4 sm:mt-0">
              <label
                for="chartType"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Chart Type</label
              >
              <div class="flex border border-gray-300 rounded-md p-1">
                <button
                  @click="chartType = 'line'"
                  type="button"
                  class="px-3 py-1.5 text-sm rounded-md"
                  :class="
                    chartType === 'line'
                      ? 'bg-indigo-100 text-indigo-700 font-medium'
                      : 'text-gray-700 hover:bg-gray-50'
                  "
                >
                  Line
                </button>
                <button
                  @click="chartType = 'bar'"
                  type="button"
                  class="px-3 py-1.5 text-sm rounded-md"
                  :class="
                    chartType === 'bar'
                      ? 'bg-indigo-100 text-indigo-700 font-medium'
                      : 'text-gray-700 hover:bg-gray-50'
                  "
                >
                  Bar
                </button>
                <button
                  @click="chartType = 'area'"
                  type="button"
                  class="px-3 py-1.5 text-sm rounded-md"
                  :class="
                    chartType === 'area'
                      ? 'bg-indigo-100 text-indigo-700 font-medium'
                      : 'text-gray-700 hover:bg-gray-50'
                  "
                >
                  Area
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main dashboard content -->
    <div class="space-y-6">
      <!-- Loading state -->
      <div
        v-if="isLoading"
        class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
      >
        <div
          class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mb-4"
        ></div>
        <p class="text-gray-600">Loading analytics data...</p>
      </div>

      <!-- Platform Overview Dashboard -->
      <div v-else-if="selectedReport === 'overview'" class="space-y-6">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Revenue Card -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-100 rounded-full p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-indigo-600"
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
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total Revenue
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ formatCurrency(overview.totalRevenue) }}
                    </div>
                    <div
                      class="ml-2 flex items-baseline text-sm font-semibold text-green-600"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 flex-shrink-0 self-center"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="sr-only">Increased by</span>
                      {{ overview.revenueGrowthRate }}%
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- Users Card -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-blue-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total Users
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ formatNumber(overview.totalUsers) }}
                    </div>
                    <div
                      class="ml-2 flex items-baseline text-sm font-semibold text-green-600"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 flex-shrink-0 self-center"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="sr-only">Increased by</span>
                      {{ overview.userGrowthRate }}%
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- Courses Card -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-green-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Active Courses
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ formatNumber(overview.totalCourses) }}
                    </div>
                    <div
                      class="ml-2 flex items-baseline text-sm font-semibold text-green-600"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 flex-shrink-0 self-center"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="sr-only">Completion rate</span>
                      {{ overview.courseCompletionRate }}%
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>

          <!-- Projects Card -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-purple-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                    clip-rule="evenodd"
                  />
                  <path
                    d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Active Projects
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ formatNumber(overview.totalProjects) }}
                    </div>
                    <div
                      class="ml-2 flex items-baseline text-sm font-semibold text-green-600"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 flex-shrink-0 self-center"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="sr-only">Success rate</span>
                      {{ overview.projectSuccessRate }}%
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart Section (Simulated) -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-6">
            {{ activeDataset.charAt(0).toUpperCase() + activeDataset.slice(1) }}
            Trends
          </h2>

          <!-- This would be a real chart in a production app -->
          <div
            class="bg-gray-50 border border-gray-200 rounded-lg p-4 h-80 flex items-center justify-center text-gray-500"
          >
            <p class="text-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-12 w-12 mx-auto mb-2 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                />
              </svg>
              <span class="block mb-1"
                >{{
                  chartType.charAt(0).toUpperCase() + chartType.slice(1)
                }}
                chart would display here</span
              >
              <span class="text-sm"
                >Showing {{ activeDataset }} data for
                {{ timeframeFilter }}</span
              >
            </p>
          </div>
        </div>

        <!-- Top Performing Items -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Top Courses -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Top Performing Courses
            </h2>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Course
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Students
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Revenue
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Rating
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="course in analyticsData.topItems.courses"
                    :key="course.id"
                  >
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium"
                    >
                      {{ course.title }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ formatNumber(course.students) }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ formatCurrency(course.revenue) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center">
                        <span class="text-sm text-gray-900 mr-1">{{
                          course.rating
                        }}</span>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-yellow-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Top Consultants -->
          <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Top Performing Consultants
            </h2>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Name
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Projects
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Revenue
                    </th>
                    <th
                      scope="col"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Rating
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="consultant in analyticsData.topItems.consultants"
                    :key="consultant.id"
                  >
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium"
                    >
                      {{ consultant.name }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ consultant.projects }}
                    </td>
                    <td
                      class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ formatCurrency(consultant.revenue) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center">
                        <span class="text-sm text-gray-900 mr-1">{{
                          consultant.rating
                        }}</span>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-yellow-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-6">
            Revenue Breakdown
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Revenue Breakdown Chart (Simulated) -->
            <div
              class="bg-gray-50 border border-gray-200 rounded-lg p-4 h-64 flex items-center justify-center"
            >
              <p class="text-center text-gray-500">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-10 w-10 mx-auto mb-2 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"
                  />
                </svg>
                <span class="block">Pie chart would display here</span>
              </p>
            </div>

            <!-- Revenue Breakdown Stats -->
            <div>
              <div class="grid gap-4">
                <div class="bg-indigo-50 rounded-lg p-4">
                  <h3 class="text-sm font-medium text-indigo-700 mb-2">
                    E-Learning Revenue
                  </h3>
                  <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-indigo-700">{{
                      formatCurrency(overview.coursesRevenue)
                    }}</span>
                    <span class="text-sm font-medium text-indigo-700"
                      >{{
                        Math.round(
                          (overview.coursesRevenue / overview.totalRevenue) *
                            100
                        )
                      }}%</span
                    >
                  </div>
                  <!-- Progress bar -->
                  <div class="w-full bg-indigo-200 rounded-full h-2 mt-2">
                    <div
                      class="bg-indigo-600 h-2 rounded-full"
                      :style="`width: ${
                        (overview.coursesRevenue / overview.totalRevenue) * 100
                      }%`"
                    ></div>
                  </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                  <h3 class="text-sm font-medium text-purple-700 mb-2">
                    Consultancy Revenue
                  </h3>
                  <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-purple-700">{{
                      formatCurrency(overview.consultancyRevenue)
                    }}</span>
                    <span class="text-sm font-medium text-purple-700"
                      >{{
                        Math.round(
                          (overview.consultancyRevenue /
                            overview.totalRevenue) *
                            100
                        )
                      }}%</span
                    >
                  </div>
                  <!-- Progress bar -->
                  <div class="w-full bg-purple-200 rounded-full h-2 mt-2">
                    <div
                      class="bg-purple-600 h-2 rounded-full"
                      :style="`width: ${
                        (overview.consultancyRevenue / overview.totalRevenue) *
                        100
                      }%`"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Other report types would be implemented here -->
      <div
        v-else
        class="bg-white rounded-lg shadow border border-gray-200 p-8 text-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-indigo-300 mx-auto mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
        <h2 class="text-xl font-medium text-gray-900 mb-2">
          {{ selectedReport.charAt(0).toUpperCase() + selectedReport.slice(1) }}
          Report
        </h2>
        <p class="text-gray-600 mb-6">
          This report view is currently being developed.
        </p>
        <button
          @click="selectedReport = 'overview'"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Return to Overview
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom animations */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* For simulating chart animations */
.animate-float {
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}
</style>
