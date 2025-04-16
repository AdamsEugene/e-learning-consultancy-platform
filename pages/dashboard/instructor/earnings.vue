<!-- pages/dashboard/instructor/earnings/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define types
interface Transaction {
  id: string;
  date: string;
  courseId: number;
  courseTitle: string;
  studentName: string;
  amount: number;
  status: "completed" | "pending" | "processing";
  type: "sale" | "refund" | "affiliate" | "subscription";
}

interface EarningsSummary {
  totalRevenue: number;
  totalSales: number;
  totalRefunds: number;
  totalStudents: number;
  monthlySales: { [key: string]: number };
  courseRevenue: {
    courseId: number;
    courseTitle: string;
    revenue: number;
    students: number;
  }[];
}

// Page meta
definePageMeta({
  layout: "dashboard-instructor",
});

useHead({
  title: "Earnings Dashboard - Instructor Portal",
});

// State
const transactions = ref<Transaction[]>([]);
const summary = ref<EarningsSummary>({
  totalRevenue: 0,
  totalSales: 0,
  totalRefunds: 0,
  totalStudents: 0,
  monthlySales: {},
  courseRevenue: [],
});
const isLoading = ref(true);
const timeframe = ref("all");
const currentPage = ref(1);
const itemsPerPage = 10;
const activeTab = ref("transactions");

// Computed values
const paginatedTransactions = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredTransactions.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredTransactions.value.length / itemsPerPage);
});

const filteredTransactions = computed(() => {
  if (timeframe.value === "all") {
    return transactions.value;
  }

  const now = new Date();
  let cutoffDate = new Date();

  if (timeframe.value === "week") {
    cutoffDate.setDate(now.getDate() - 7);
  } else if (timeframe.value === "month") {
    cutoffDate.setMonth(now.getMonth() - 1);
  } else if (timeframe.value === "year") {
    cutoffDate.setFullYear(now.getFullYear() - 1);
  }

  return transactions.value.filter((transaction) => {
    const transactionDate = new Date(transaction.date);
    return transactionDate >= cutoffDate && transactionDate <= now;
  });
});

// Monthly revenue data for chart
const monthlyRevenueData = computed(() => {
  const months = Object.keys(summary.value.monthlySales).sort();
  return {
    labels: months,
    data: months.map((month) => summary.value.monthlySales[month]),
  };
});

// Course revenue distribution data for chart
const courseRevenueData = computed(() => {
  return {
    labels: summary.value.courseRevenue.map((course) => course.courseTitle),
    data: summary.value.courseRevenue.map((course) => course.revenue),
  };
});

// Fetch data
const fetchData = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock transactions data
    transactions.value = [
      {
        id: "txn-001",
        date: "2025-04-10T09:23:14Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "John Smith",
        amount: 89.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-002",
        date: "2025-04-09T15:47:21Z",
        courseId: 2,
        courseTitle: "Advanced JavaScript: From Fundamentals to Mastery",
        studentName: "Emily Johnson",
        amount: 79.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-003",
        date: "2025-04-08T11:32:09Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "Michael Wilson",
        amount: 89.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-004",
        date: "2025-04-05T14:19:45Z",
        courseId: 3,
        courseTitle: "UI/UX Design Principles",
        studentName: "Sarah Davis",
        amount: 69.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-005",
        date: "2025-04-05T08:56:32Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "Chris Thompson",
        amount: -89.99,
        status: "completed",
        type: "refund",
      },
      {
        id: "txn-006",
        date: "2025-04-03T16:41:08Z",
        courseId: 2,
        courseTitle: "Advanced JavaScript: From Fundamentals to Mastery",
        studentName: "David Lee",
        amount: 79.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-007",
        date: "2025-04-01T10:28:53Z",
        courseId: 8,
        courseTitle: "Responsive Web Design Masterclass",
        studentName: "Lisa Rodriguez",
        amount: 64.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-008",
        date: "2025-03-29T13:17:25Z",
        courseId: 3,
        courseTitle: "UI/UX Design Principles",
        studentName: "Kevin Brown",
        amount: 69.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-009",
        date: "2025-03-27T09:05:41Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "Amanda Martinez",
        amount: 89.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-010",
        date: "2025-03-25T17:38:12Z",
        courseId: 2,
        courseTitle: "Advanced JavaScript: From Fundamentals to Mastery",
        studentName: "Daniel Garcia",
        amount: 79.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-011",
        date: "2025-03-22T11:52:35Z",
        courseId: 8,
        courseTitle: "Responsive Web Design Masterclass",
        studentName: "Rachel Kim",
        amount: 64.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-012",
        date: "2025-03-20T14:29:07Z",
        courseId: 3,
        courseTitle: "UI/UX Design Principles",
        studentName: "James Wilson",
        amount: 69.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-013",
        date: "2025-03-18T16:14:52Z",
        courseId: 2,
        courseTitle: "Advanced JavaScript: From Fundamentals to Mastery",
        studentName: "Sophie Taylor",
        amount: -79.99,
        status: "completed",
        type: "refund",
      },
      {
        id: "txn-014",
        date: "2025-03-15T09:48:23Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "Thomas Anderson",
        amount: 89.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-015",
        date: "2025-03-12T13:36:59Z",
        courseId: 8,
        courseTitle: "Responsive Web Design Masterclass",
        studentName: "Olivia Parker",
        amount: 64.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-016",
        date: "2025-03-10T10:27:18Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "Robert Johnson",
        amount: 89.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-017",
        date: "2025-03-07T15:19:43Z",
        courseId: 3,
        courseTitle: "UI/UX Design Principles",
        studentName: "Jennifer Lewis",
        amount: 69.99,
        status: "completed",
        type: "sale",
      },
      {
        id: "txn-018",
        date: "2025-03-05T11:42:36Z",
        courseId: 2,
        courseTitle: "Advanced JavaScript: From Fundamentals to Mastery",
        studentName: "Ryan Miller",
        amount: 79.99,
        status: "pending",
        type: "sale",
      },
      {
        id: "txn-019",
        date: "2025-03-02T08:15:27Z",
        courseId: 8,
        courseTitle: "Responsive Web Design Masterclass",
        studentName: "Maria Rodriguez",
        amount: 64.99,
        status: "processing",
        type: "sale",
      },
      {
        id: "txn-020",
        date: "2025-03-01T14:29:08Z",
        courseId: 1,
        courseTitle: "Complete Web Development Bootcamp",
        studentName: "Paul Davis",
        amount: 89.99,
        status: "completed",
        type: "sale",
      },
    ];

    // Mock summary data
    summary.value = {
      totalRevenue: 1239.84,
      totalSales: 18,
      totalRefunds: 2,
      totalStudents: 16,
      monthlySales: {
        "Nov 2024": 450.95,
        "Dec 2024": 789.92,
        "Jan 2025": 1102.45,
        "Feb 2025": 968.74,
        "Mar 2025": 815.87,
        "Apr 2025": 485.95,
      },
      courseRevenue: [
        {
          courseId: 1,
          courseTitle: "Complete Web Development Bootcamp",
          revenue: 539.94,
          students: 6,
        },
        {
          courseId: 2,
          courseTitle: "Advanced JavaScript: From Fundamentals to Mastery",
          revenue: 319.96,
          students: 4,
        },
        {
          courseId: 3,
          courseTitle: "UI/UX Design Principles",
          revenue: 209.97,
          students: 3,
        },
        {
          courseId: 8,
          courseTitle: "Responsive Web Design Masterclass",
          revenue: 194.97,
          students: 3,
        },
      ],
    };
  } catch (error) {
    console.error("Error fetching earnings data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format time to readable string
const formatTime = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(amount);
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "completed":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "processing":
      return "bg-blue-100 text-blue-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get type badge classes
const getTypeBadgeClasses = (type: string, amount: number) => {
  if (type === "refund" || amount < 0) {
    return "bg-red-100 text-red-800";
  } else if (type === "sale") {
    return "bg-green-100 text-green-800";
  } else if (type === "affiliate") {
    return "bg-purple-100 text-purple-800";
  } else if (type === "subscription") {
    return "bg-blue-100 text-blue-800";
  }
  return "bg-gray-100 text-gray-800";
};

// Pagination methods
const goToPage = (page: number) => {
  currentPage.value = page;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

// Download earnings report
const downloadEarningsReport = () => {
  // In a real app, this would generate and download a CSV or PDF report
  alert("Downloading earnings report...");
};

// Handle timeframe change
const changeTimeframe = (newTimeframe: string) => {
  timeframe.value = newTimeframe;
  currentPage.value = 1; // Reset pagination when filter changes
};

// Handle tab change
const changeTab = (tab: string) => {
  activeTab.value = tab;
};

// Load data on component mount
onMounted(() => {
  fetchData();
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
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Earnings Dashboard
          </h1>
          <p class="text-gray-600">
            Track your revenue, sales, and financial performance
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
          <button
            @click="downloadEarningsReport"
            class="inline-flex items-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Download Report</span>
          </button>
          <NuxtLink
            to="/dashboard/instructor/earnings/settings"
            class="inline-flex items-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Payment Settings</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div
          class="bg-white rounded-lg shadow p-5 border border-gray-200 hover:shadow-md transition-shadow"
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(summary.totalRevenue) }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="bg-white rounded-lg shadow p-5 border border-gray-200 hover:shadow-md transition-shadow"
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
                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Sales</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ summary.totalSales }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="bg-white rounded-lg shadow p-5 border border-gray-200 hover:shadow-md transition-shadow"
        >
          <div class="flex items-center">
            <div class="bg-red-100 rounded-full p-3 mr-4">
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
                  d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Refunds</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ summary.totalRefunds }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="bg-white rounded-lg shadow p-5 border border-gray-200 hover:shadow-md transition-shadow"
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
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Students</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ summary.totalStudents }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts & Transactions Tab Navigation -->
    <div class="bg-white rounded-lg shadow mb-6 border border-gray-200">
      <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px">
          <li class="mr-2">
            <button
              @click="changeTab('overview')"
              class="inline-block py-4 px-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'overview'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              Overview
            </button>
          </li>
          <li class="mr-2">
            <button
              @click="changeTab('transactions')"
              class="inline-block py-4 px-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'transactions'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              Transactions
            </button>
          </li>
          <li class="mr-2">
            <button
              @click="changeTab('courses')"
              class="inline-block py-4 px-4 font-medium text-sm border-b-2 transition-colors"
              :class="
                activeTab === 'courses'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              Course Revenue
            </button>
          </li>
        </ul>
      </div>

      <div class="p-6">
        <!-- Overview Tab Content -->
        <div v-if="activeTab === 'overview'" class="space-y-6">
          <!-- Loading skeleton -->
          <div v-if="isLoading" class="space-y-6">
            <div class="animate-pulse">
              <div class="h-64 bg-gray-200 rounded w-full"></div>
            </div>
          </div>

          <div v-else>
            <!-- Monthly Revenue Chart -->
            <div class="bg-white rounded-lg p-4 border border-gray-200 mb-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                  Monthly Revenue
                </h3>
                <div class="flex space-x-2">
                  <button
                    v-for="period in ['week', 'month', 'year', 'all']"
                    :key="period"
                    @click="changeTimeframe(period)"
                    class="px-3 py-1 text-sm font-medium rounded transition-colors"
                    :class="
                      timeframe === period
                        ? 'bg-indigo-100 text-indigo-700'
                        : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
                    "
                  >
                    {{ period.charAt(0).toUpperCase() + period.slice(1) }}
                  </button>
                </div>
              </div>

              <!-- Chart Canvas (Would use Chart.js or similar in a real app) -->
              <div class="h-64 w-full relative">
                <div
                  class="absolute bottom-0 left-0 right-0 h-64 flex items-end"
                >
                  <div
                    v-for="(value, index) in monthlyRevenueData.data"
                    :key="index"
                    class="w-1/6 mx-2 bg-gradient-to-t from-indigo-600 to-indigo-400 rounded-t-md hover:from-indigo-700 hover:to-indigo-500 transition-all hover:shadow-md cursor-pointer"
                    :style="{ height: `${Math.max(10, value / 12)}px` }"
                  >
                    <div
                      class="mt-2 text-center text-xs font-medium text-gray-700 transform -rotate-45 origin-left"
                    >
                      {{ monthlyRevenueData.labels[index] }}
                    </div>
                  </div>
                </div>

                <!-- Y-axis labels would go here in a real chart -->
              </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <!-- Top Courses -->
              <div
                class="bg-white rounded-lg p-4 border border-gray-200 h-full"
              >
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                  Top Courses by Revenue
                </h3>
                <div class="space-y-3">
                  <div
                    v-for="(course, index) in summary.courseRevenue.slice(0, 3)"
                    :key="index"
                    class="flex items-center p-2 hover:bg-gray-50 rounded-md transition-colors"
                  >
                    <div class="bg-indigo-100 rounded p-2 mr-3">
                      <span class="text-lg font-bold text-indigo-700">{{
                        index + 1
                      }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-medium text-gray-900 truncate">
                        {{ course.courseTitle }}
                      </h4>
                      <div class="flex items-center text-xs text-gray-500">
                        <span>{{ course.students }} students</span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatCurrency(course.revenue) }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-4 text-right">
                  <button
                    @click="changeTab('courses')"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                  >
                    View all courses
                  </button>
                </div>
              </div>

              <!-- Recent Transactions -->
              <div
                class="bg-white rounded-lg p-4 border border-gray-200 h-full"
              >
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                  Recent Transactions
                </h3>
                <div class="space-y-3">
                  <div
                    v-for="(transaction, index) in transactions.slice(0, 5)"
                    :key="index"
                    class="flex items-center p-2 hover:bg-gray-50 rounded-md transition-colors"
                  >
                    <div
                      class="rounded-full w-8 h-8 flex items-center justify-center mr-3"
                      :class="
                        transaction.type === 'refund' || transaction.amount < 0
                          ? 'bg-red-100'
                          : 'bg-green-100'
                      "
                    >
                      <svg
                        v-if="
                          transaction.type === 'refund' ||
                          transaction.amount < 0
                        "
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-red-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-green-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-medium text-gray-900 truncate">
                        {{ transaction.courseTitle }}
                      </h4>
                      <div class="flex items-center text-xs text-gray-500">
                        <span>{{ formatDate(transaction.date) }}</span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div
                        class="text-sm font-medium"
                        :class="
                          transaction.amount < 0
                            ? 'text-red-600'
                            : 'text-green-600'
                        "
                      >
                        {{ formatCurrency(transaction.amount) }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-4 text-right">
                  <button
                    @click="changeTab('transactions')"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                  >
                    View all transactions
                  </button>
                </div>
              </div>
            </div>

            <!-- Payout Information -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    Next Payout
                  </h3>
                  <p class="text-gray-600 mt-1">
                    Your next payout is scheduled for May 1, 2025
                  </p>
                </div>
                <div class="text-right">
                  <div class="text-3xl font-bold text-gray-900">
                    {{ formatCurrency(985.45) }}
                  </div>
                  <div class="text-sm text-gray-500 mt-1">Current Balance</div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-2">
                  Payout Method
                </h4>
                <div class="flex items-center">
                  <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6 text-blue-600"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                    >
                      <path
                        d="M11.953 2C6.465 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.493 2 11.953 2zM12 20c-4.411 0-8-3.589-8-8s3.567-8 7.953-8C16.391 4 20 7.589 20 12s-3.589 8-8 8z"
                      />
                      <path d="M11 7h2v7h-2zm0 8h2v2h-2z" />
                    </svg>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">PayPal</div>
                    <div class="text-xs text-gray-500">
                      instructor@example.com
                    </div>
                  </div>
                  <div class="ml-auto">
                    <NuxtLink
                      to="/dashboard/instructor/earnings/settings"
                      class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                    >
                      Change method
                    </NuxtLink>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Transactions Tab Content -->
        <div v-if="activeTab === 'transactions'">
          <!-- Filter and controls -->
          <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6"
          >
            <div class="flex space-x-2 mb-4 md:mb-0">
              <button
                v-for="period in ['week', 'month', 'year', 'all']"
                :key="period"
                @click="changeTimeframe(period)"
                class="px-3 py-1 text-sm font-medium rounded transition-colors"
                :class="
                  timeframe === period
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
                "
              >
                {{ period.charAt(0).toUpperCase() + period.slice(1) }}
              </button>
            </div>
            <div class="relative">
              <input
                type="text"
                placeholder="Search transactions..."
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
          </div>

          <!-- Loading skeleton -->
          <div v-if="isLoading" class="space-y-4">
            <div
              v-for="i in 3"
              :key="`skeleton-${i}`"
              class="animate-pulse py-4 border-b border-gray-200"
            >
              <div class="flex items-center space-x-4">
                <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                <div class="flex-1">
                  <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="h-8 bg-gray-200 rounded w-24"></div>
              </div>
            </div>
          </div>

          <!-- Transactions list -->
          <div
            v-else-if="paginatedTransactions.length > 0"
            class="rounded-lg border border-gray-200 overflow-hidden"
          >
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Date
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Transaction ID
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Course
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Student
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Type
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="transaction in paginatedTransactions"
                    :key="transaction.id"
                    class="hover:bg-gray-50 transition-colors"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatDate(transaction.date) }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ formatTime(transaction.date) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500">
                        {{ transaction.id }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div
                        class="text-sm font-medium text-gray-900 truncate max-w-xs"
                      >
                        {{ transaction.courseTitle }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ transaction.studentName }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="
                          getTypeBadgeClasses(
                            transaction.type,
                            transaction.amount
                          )
                        "
                      >
                        {{
                          transaction.type.charAt(0).toUpperCase() +
                          transaction.type.slice(1)
                        }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="getStatusBadgeClasses(transaction.status)"
                      >
                        {{
                          transaction.status.charAt(0).toUpperCase() +
                          transaction.status.slice(1)
                        }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div
                        class="text-sm font-medium"
                        :class="
                          transaction.amount < 0
                            ? 'text-red-600'
                            : 'text-green-600'
                        "
                      >
                        {{ formatCurrency(transaction.amount) }}
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Empty state -->
          <div
            v-else
            class="text-center py-12 bg-white rounded-lg border border-gray-200"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-12 w-12 mx-auto text-gray-400 mb-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              No transactions found
            </h3>
            <p class="text-gray-600">
              There are no transactions for the selected time period.
            </p>
          </div>

          <!-- Pagination -->
          <div
            v-if="paginatedTransactions.length > 0"
            class="mt-6 flex justify-center"
          >
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium"
                :class="
                  currentPage === 1
                    ? 'text-gray-300 cursor-not-allowed'
                    : 'text-gray-500 hover:bg-gray-50'
                "
              >
                <span class="sr-only">Previous</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
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

              <template v-if="totalPages <= 7">
                <button
                  v-for="page in totalPages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    currentPage === page
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  ]"
                >
                  {{ page }}
                </button>
              </template>

              <template v-else>
                <!-- First page -->
                <button
                  @click="goToPage(1)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    currentPage === 1
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  ]"
                >
                  1
                </button>

                <!-- Ellipsis if needed -->
                <span
                  v-if="currentPage > 3"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  ...
                </span>

                <!-- Pages around current page -->
                <button
                  v-for="page in totalPages"
                  :key="page"
                  v-if="
                    page !== 1 &&
                    page !== totalPages &&
                    Math.abs(page - currentPage) <= 1
                  "
                  @click="goToPage(page)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    currentPage === page
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  ]"
                >
                  {{ page }}
                </button>

                <!-- Ellipsis if needed -->
                <span
                  v-if="currentPage < totalPages - 2"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  ...
                </span>

                <!-- Last page -->
                <button
                  v-if="totalPages > 1"
                  @click="goToPage(totalPages)"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    currentPage === totalPages
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  ]"
                >
                  {{ totalPages }}
                </button>
              </template>

              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium"
                :class="
                  currentPage === totalPages
                    ? 'text-gray-300 cursor-not-allowed'
                    : 'text-gray-500 hover:bg-gray-50'
                "
              >
                <span class="sr-only">Next</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
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

        <!-- Courses Revenue Tab Content -->
        <div v-if="activeTab === 'courses'">
          <!-- Loading skeleton -->
          <div v-if="isLoading" class="space-y-4">
            <div
              v-for="i in 3"
              :key="`skeleton-${i}`"
              class="animate-pulse py-4 border-b border-gray-200"
            >
              <div class="flex items-center space-x-4">
                <div class="h-12 w-16 bg-gray-200 rounded"></div>
                <div class="flex-1">
                  <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="h-8 bg-gray-200 rounded w-24"></div>
              </div>
            </div>
          </div>

          <!-- Course revenue list -->
          <div v-else class="space-y-4">
            <div
              v-for="(course, index) in summary.courseRevenue"
              :key="course.courseId"
              class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-md transition-shadow"
            >
              <div class="flex flex-col md:flex-row md:items-center">
                <div class="flex items-center mb-4 md:mb-0 md:flex-1">
                  <div
                    class="bg-indigo-100 rounded-full w-10 h-10 flex items-center justify-center mr-4 text-indigo-700 font-bold"
                  >
                    {{ index + 1 }}
                  </div>
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">
                      {{ course.courseTitle }}
                    </h3>
                    <div class="flex items-center mt-1">
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
                        >{{ course.students }} students</span
                      >
                    </div>
                  </div>
                </div>

                <!-- Course revenue visualization -->
                <div class="flex items-center space-x-4">
                  <div
                    class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden w-32"
                  >
                    <div
                      class="h-full bg-indigo-600"
                      :style="{
                        width: `${
                          (course.revenue / summary.courseRevenue[0].revenue) *
                          100
                        }%`,
                      }"
                    ></div>
                  </div>
                  <div class="text-right w-24">
                    <div class="text-lg font-bold text-gray-900">
                      {{ formatCurrency(course.revenue) }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{
                        ((course.revenue / summary.totalRevenue) * 100).toFixed(
                          1
                        )
                      }}% of total
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course revenue chart -->
            <div class="bg-white rounded-lg p-6 border border-gray-200 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Revenue Distribution by Course
              </h3>

              <!-- Chart would be here in a real app (using Chart.js or similar) -->
              <div class="h-64 flex items-center justify-center">
                <div class="text-gray-400 text-sm">
                  Course revenue distribution chart would appear here.
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
/* Animation for loading skeleton */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Truncate long text */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
