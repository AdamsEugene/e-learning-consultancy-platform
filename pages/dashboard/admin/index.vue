<!-- pages/admin/dashboard/index.vue -->
<script setup lang="ts">
import { ref, onMounted, computed } from "vue";

// Set the layout
definePageMeta({
  layout: "dashboard-admin",
});

// Page meta
useHead({
  title: "Admin Dashboard - LearnConsult",
  meta: [
    {
      name: "description",
      content: "Admin dashboard for the LearnConsult platform",
    },
  ],
});

// Stats data
const platformStats = ref({
  totalUsers: 3725,
  activeStudents: 1865,
  activeInstructors: 48,
  activeConsultants: 27,
  activeClients: 112,
  totalCourses: 150,
  totalProjects: 72,
  activeProjects: 35,
  totalRevenue: 358750,
  coursesRevenue: 215600,
  consultancyRevenue: 143150,
  coursesEnrollments: 9782,
  newUsersToday: 32,
  projectsCompletedThisMonth: 8,
  supportTickets: 16,
  pendingApprovals: 5,
});

// Dashboard tabs
const activeTab = ref("overview");

// Date filter
const dateFilter = ref("all");

// User activity data for chart
const userActivityData = ref([
  { date: "Apr 09", students: 78, instructors: 8, consultants: 5, clients: 12 },
  {
    date: "Apr 10",
    students: 85,
    instructors: 10,
    consultants: 7,
    clients: 15,
  },
  {
    date: "Apr 11",
    students: 92,
    instructors: 12,
    consultants: 8,
    clients: 18,
  },
  {
    date: "Apr 12",
    students: 105,
    instructors: 14,
    consultants: 9,
    clients: 22,
  },
  {
    date: "Apr 13",
    students: 120,
    instructors: 15,
    consultants: 12,
    clients: 25,
  },
  {
    date: "Apr 14",
    students: 132,
    instructors: 18,
    consultants: 14,
    clients: 30,
  },
  {
    date: "Apr 15",
    students: 145,
    instructors: 20,
    consultants: 15,
    clients: 34,
  },
  {
    date: "Apr 16",
    students: 155,
    instructors: 22,
    consultants: 16,
    clients: 37,
  },
]);

// Revenue data for chart
const revenueData = ref([
  { month: "Jan", courses: 18500, consultancy: 9500, total: 28000 },
  { month: "Feb", courses: 22000, consultancy: 12500, total: 34500 },
  { month: "Mar", courses: 25500, consultancy: 16000, total: 41500 },
  { month: "Apr", courses: 31000, consultancy: 19500, total: 50500 },
  { month: "May", courses: 35000, consultancy: 22000, total: 57000 },
  { month: "Jun", courses: 40000, consultancy: 25000, total: 65000 },
  { month: "Jul", courses: 42500, consultancy: 27500, total: 70000 },
  { month: "Aug", courses: 39000, consultancy: 26500, total: 65500 },
]);

// Recent support tickets
const supportTickets = ref([
  {
    id: "T-1024",
    subject: "Payment processing issue",
    user: "John Smith",
    userType: "Student",
    status: "open",
    priority: "high",
    submitted: "2025-04-16T09:23:00Z",
    category: "Billing",
  },
  {
    id: "T-1023",
    subject: "Course access problem",
    user: "Emily Johnson",
    userType: "Student",
    status: "open",
    priority: "medium",
    submitted: "2025-04-16T08:45:00Z",
    category: "Learning",
  },
  {
    id: "T-1022",
    subject: "Consultant schedule conflict",
    user: "Sarah Parker",
    userType: "Consultant",
    status: "open",
    priority: "medium",
    submitted: "2025-04-15T17:12:00Z",
    category: "Scheduling",
  },
  {
    id: "T-1021",
    subject: "Course upload failure",
    user: "Alex Thompson",
    userType: "Instructor",
    status: "in_progress",
    priority: "high",
    submitted: "2025-04-15T14:30:00Z",
    category: "Content",
  },
  {
    id: "T-1020",
    subject: "Project milestone issue",
    user: "Michael Roberts",
    userType: "Client",
    status: "in_progress",
    priority: "medium",
    submitted: "2025-04-15T11:18:00Z",
    category: "Projects",
  },
]);

// Pending approvals
const pendingApprovals = ref([
  {
    id: "C-487",
    title: "Advanced Machine Learning with Python",
    type: "course",
    submitter: "Dr. Alex Thompson",
    submitterType: "instructor",
    submitted: "2025-04-15T08:30:00Z",
  },
  {
    id: "C-486",
    title: "UX/UI Design Masterclass 2025",
    type: "course",
    submitter: "Maria Garcia",
    submitterType: "instructor",
    submitted: "2025-04-14T16:45:00Z",
  },
  {
    id: "P-129",
    title: "E-commerce Platform Migration",
    type: "project",
    submitter: "Sarah Parker",
    submitterType: "consultant",
    submitted: "2025-04-14T14:20:00Z",
  },
  {
    id: "C-485",
    title: "Blockchain Development Fundamentals",
    type: "course",
    submitter: "James Wilson",
    submitterType: "instructor",
    submitted: "2025-04-14T10:15:00Z",
  },
  {
    id: "P-128",
    title: "Customer Portal Redesign",
    type: "project",
    submitter: "Robert Chen",
    submitterType: "consultant",
    submitted: "2025-04-13T15:30:00Z",
  },
]);

// Recent user activations
const recentUsers = ref([
  {
    id: "U-8723",
    name: "Jessica Martinez",
    email: "jessica.martinez@example.com",
    type: "student",
    joined: "2025-04-16T10:15:00Z",
    avatarUrl: "/api/placeholder/40/40",
  },
  {
    id: "U-8722",
    name: "David Wilson",
    email: "david.wilson@example.com",
    type: "student",
    joined: "2025-04-16T09:42:00Z",
    avatarUrl: "/api/placeholder/40/40",
  },
  {
    id: "U-8721",
    name: "Thomas Anderson",
    email: "thomas.anderson@techcorp.com",
    type: "client",
    joined: "2025-04-16T08:55:00Z",
    avatarUrl: "/api/placeholder/40/40",
  },
  {
    id: "U-8720",
    name: "Linda Johnson",
    email: "linda.johnson@designfirm.com",
    type: "consultant",
    joined: "2025-04-16T07:20:00Z",
    avatarUrl: "/api/placeholder/40/40",
  },
  {
    id: "U-8719",
    name: "Robert Zhang",
    email: "robert.zhang@university.edu",
    type: "instructor",
    joined: "2025-04-15T22:35:00Z",
    avatarUrl: "/api/placeholder/40/40",
  },
]);

// Platform health data
const systemHealth = ref({
  status: "normal", // normal, warning, error
  uptime: "99.98%",
  responseTime: "248ms",
  lastIssue: "2025-04-01T14:32:00Z",
  serverLoad: 32,
  databaseStatus: "optimal",
  apiRequests: "12.4k/min",
  activeSessions: 872,
});

// Format price to currency
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

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleString("en-US", {
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

// Get time ago
const getTimeAgo = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

  if (diffInSeconds < 60) {
    return `${diffInSeconds} seconds ago`;
  }

  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) {
    return `${diffInMinutes} minute${diffInMinutes === 1 ? "" : "s"} ago`;
  }

  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) {
    return `${diffInHours} hour${diffInHours === 1 ? "" : "s"} ago`;
  }

  const diffInDays = Math.floor(diffInHours / 24);
  return `${diffInDays} day${diffInDays === 1 ? "" : "s"} ago`;
};

// Get status badge class
const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case "open":
      return "bg-yellow-100 text-yellow-800";
    case "in_progress":
      return "bg-blue-100 text-blue-800";
    case "resolved":
      return "bg-green-100 text-green-800";
    case "closed":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get priority badge class
const getPriorityBadgeClass = (priority: string) => {
  switch (priority) {
    case "high":
      return "bg-red-100 text-red-800";
    case "medium":
      return "bg-yellow-100 text-yellow-800";
    case "low":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get user type badge class
const getUserTypeBadgeClass = (type: string) => {
  switch (type) {
    case "student":
      return "bg-indigo-100 text-indigo-800";
    case "instructor":
      return "bg-purple-100 text-purple-800";
    case "consultant":
      return "bg-blue-100 text-blue-800";
    case "client":
      return "bg-green-100 text-green-800";
    case "admin":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get item type badge class
const getItemTypeBadgeClass = (type: string) => {
  switch (type) {
    case "course":
      return "bg-purple-100 text-purple-800";
    case "project":
      return "bg-blue-100 text-blue-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get health status indicator class
const getHealthStatusClass = (status: string) => {
  switch (status) {
    case "normal":
      return "bg-green-500";
    case "warning":
      return "bg-yellow-500";
    case "error":
      return "bg-red-500";
    default:
      return "bg-gray-500";
  }
};

// Filtered stats based on date range (in a real app, this would fetch from an API)
const filteredStats = computed(() => {
  // In a real app, this would filter based on the selected date range
  return platformStats.value;
});

// Random data generator for charts (for demo purposes only)
const refreshChartData = () => {
  // This would actually fetch fresh data from the API in a real application
  // For demo, we'll just randomize the current data slightly

  userActivityData.value = userActivityData.value.map((day) => {
    return {
      date: day.date,
      students: Math.max(
        50,
        day.students + Math.floor(Math.random() * 20) - 10
      ),
      instructors: Math.max(
        5,
        day.instructors + Math.floor(Math.random() * 4) - 2
      ),
      consultants: Math.max(
        3,
        day.consultants + Math.floor(Math.random() * 3) - 1
      ),
      clients: Math.max(8, day.clients + Math.floor(Math.random() * 6) - 3),
    };
  });

  revenueData.value = revenueData.value.map((month) => {
    const coursesDelta = Math.floor(Math.random() * 2000) - 1000;
    const consultancyDelta = Math.floor(Math.random() * 1500) - 750;

    return {
      month: month.month,
      courses: Math.max(5000, month.courses + coursesDelta),
      consultancy: Math.max(3000, month.consultancy + consultancyDelta),
      total: Math.max(8000, month.total + coursesDelta + consultancyDelta),
    };
  });
};

onMounted(() => {
  // Initialize charts and fetch latest data
  // This would be an API call in a real app
});
</script>

<template>
  <div>
    <!-- Admin Dashboard Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
          <p class="text-gray-600">
            Welcome back, Admin. Here's what's happening with your platform
            today.
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Date Range:</span>
            <select
              v-model="dateFilter"
              class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
              <option value="quarter">This Quarter</option>
              <option value="year">This Year</option>
              <option value="all">All Time</option>
            </select>
          </div>
          <button
            @click="refreshChartData"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            Refresh Data
          </button>
        </div>
      </div>
    </div>

    <!-- Dashboard Tabs -->
    <div class="mb-6 border-b border-gray-200">
      <nav class="flex space-x-8">
        <button
          @click="activeTab = 'overview'"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
          :class="
            activeTab === 'overview'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Overview
        </button>
        <button
          @click="activeTab = 'e-learning'"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
          :class="
            activeTab === 'e-learning'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          E-Learning
        </button>
        <button
          @click="activeTab = 'consultancy'"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
          :class="
            activeTab === 'consultancy'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Consultancy
        </button>
        <button
          @click="activeTab = 'users'"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
          :class="
            activeTab === 'users'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Users
        </button>
        <button
          @click="activeTab = 'system'"
          class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
          :class="
            activeTab === 'system'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          System Health
        </button>
      </nav>
    </div>

    <!-- Dashboard Content -->
    <div v-if="activeTab === 'overview'" class="space-y-6">
      <!-- Stats Cards Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Users</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatNumber(filteredStats.totalUsers) }}
              </p>
              <p class="text-xs text-green-600 mt-1">
                <span class="font-medium"
                  >+{{ filteredStats.newUsersToday }}</span
                >
                today
              </p>
            </div>
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-green-600"
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
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(filteredStats.totalRevenue) }}
              </p>
              <div class="flex text-xs mt-1 space-x-2">
                <span class="text-blue-600 font-medium"
                  >{{ formatCurrency(filteredStats.coursesRevenue) }}
                  <span class="text-gray-500">courses</span></span
                >
                <span class="text-purple-600 font-medium"
                  >{{ formatCurrency(filteredStats.consultancyRevenue) }}
                  <span class="text-gray-500">consultancy</span></span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Courses & Projects Card -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-purple-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Content</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ filteredStats.totalCourses + filteredStats.totalProjects }}
              </p>
              <div class="flex text-xs mt-1 space-x-2">
                <span class="text-indigo-600 font-medium"
                  >{{ filteredStats.totalCourses }}
                  <span class="text-gray-500">courses</span></span
                >
                <span class="text-blue-600 font-medium"
                  >{{ filteredStats.totalProjects }}
                  <span class="text-gray-500">projects</span></span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Action Items -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-yellow-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Pending Actions</p>
              <p class="text-2xl font-bold text-gray-900">
                {{
                  filteredStats.supportTickets + filteredStats.pendingApprovals
                }}
              </p>
              <div class="flex text-xs mt-1 space-x-2">
                <span class="text-red-600 font-medium"
                  >{{ filteredStats.supportTickets }}
                  <span class="text-gray-500">tickets</span></span
                >
                <span class="text-yellow-600 font-medium"
                  >{{ filteredStats.pendingApprovals }}
                  <span class="text-gray-500">approvals</span></span
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Activity Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-900">User Activity</h2>
            <div class="text-xs text-gray-500">Last 7 days</div>
          </div>
          <!-- In a real app, replace this with an actual chart component -->
          <div
            class="h-64 w-full bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200"
          >
            <div class="text-center">
              <p class="text-gray-500 mb-2">User Activity Chart</p>
              <p class="text-sm text-gray-400">
                (Chart visualization would be here)
              </p>
              <p class="text-xs text-gray-400 mt-2">
                Latest:
                {{
                  userActivityData[userActivityData.length - 1].students
                }}
                student logins today
              </p>
            </div>
          </div>

          <div class="grid grid-cols-4 gap-2 mt-4">
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">Students</div>
              <div class="text-indigo-600 font-bold">
                {{ formatNumber(filteredStats.activeStudents) }}
              </div>
            </div>
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">Instructors</div>
              <div class="text-purple-600 font-bold">
                {{ formatNumber(filteredStats.activeInstructors) }}
              </div>
            </div>
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">Consultants</div>
              <div class="text-blue-600 font-bold">
                {{ formatNumber(filteredStats.activeConsultants) }}
              </div>
            </div>
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">Clients</div>
              <div class="text-green-600 font-bold">
                {{ formatNumber(filteredStats.activeClients) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-900">Revenue Breakdown</h2>
            <div class="text-xs text-gray-500">Last 8 months</div>
          </div>
          <!-- In a real app, replace this with an actual chart component -->
          <div
            class="h-64 w-full bg-gray-50 rounded-lg flex items-center justify-center border border-gray-200"
          >
            <div class="text-center">
              <p class="text-gray-500 mb-2">Revenue Chart</p>
              <p class="text-sm text-gray-400">
                (Chart visualization would be here)
              </p>
              <p class="text-xs text-gray-400 mt-2">
                Latest month:
                {{ formatCurrency(revenueData[revenueData.length - 1].total) }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-2 mt-4">
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">E-Learning</div>
              <div class="text-indigo-600 font-bold">
                {{ formatCurrency(filteredStats.coursesRevenue) }}
              </div>
            </div>
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">Consultancy</div>
              <div class="text-purple-600 font-bold">
                {{ formatCurrency(filteredStats.consultancyRevenue) }}
              </div>
            </div>
            <div class="text-center">
              <div class="text-xs font-medium text-gray-500">Total</div>
              <div class="text-green-600 font-bold">
                {{ formatCurrency(filteredStats.totalRevenue) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending Approvals -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">
                Pending Approvals
              </h2>
              <NuxtLink
                to="/admin/approvals"
                class="text-sm text-indigo-600 font-medium hover:text-indigo-800"
                >View all</NuxtLink
              >
            </div>
          </div>

          <div class="divide-y divide-gray-200">
            <div
              v-for="item in pendingApprovals.slice(0, 3)"
              :key="item.id"
              class="p-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <span
                      class="inline-flex items-center justify-center h-10 w-10 rounded-md"
                      :class="
                        item.type === 'course' ? 'bg-purple-100' : 'bg-blue-100'
                      "
                    >
                      <svg
                        v-if="item.type === 'course'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-purple-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                        />
                      </svg>
                      <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-blue-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5z"
                        />
                        <path
                          d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"
                        />
                      </svg>
                    </span>
                  </div>
                  <div>
                    <h3 class="text-sm font-medium text-gray-900">
                      {{ item.title }}
                    </h3>
                    <div class="mt-1 flex items-center">
                      <span
                        class="text-xs px-2 py-0.5 rounded-full"
                        :class="getItemTypeBadgeClass(item.type)"
                      >
                        {{
                          item.type.charAt(0).toUpperCase() + item.type.slice(1)
                        }}
                      </span>
                      <span class="mx-2 text-gray-500 text-xs">•</span>
                      <span class="text-xs text-gray-500"
                        >by {{ item.submitter }}</span
                      >
                      <span
                        class="ml-1 text-xs px-2 py-0.5 rounded-full"
                        :class="getUserTypeBadgeClass(item.submitterType)"
                      >
                        {{ item.submitterType }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xs text-gray-500">
                    {{ getTimeAgo(item.submitted) }}
                  </div>
                  <div class="mt-2 flex space-x-2">
                    <button
                      class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded hover:bg-green-200"
                    >
                      Approve
                    </button>
                    <button
                      class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200"
                    >
                      Reject
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="pendingApprovals.length > 3"
            class="px-6 py-3 bg-gray-50 border-t border-gray-200"
          >
            <NuxtLink
              to="/admin/approvals"
              class="text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center justify-center"
            >
              Show {{ pendingApprovals.length - 3 }} more
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </NuxtLink>
          </div>
        </div>

        <!-- Support Tickets -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">Support Tickets</h2>
              <NuxtLink
                to="/admin/support"
                class="text-sm text-indigo-600 font-medium hover:text-indigo-800"
                >View all</NuxtLink
              >
            </div>
          </div>

          <div class="divide-y divide-gray-200">
            <div
              v-for="ticket in supportTickets.slice(0, 3)"
              :key="ticket.id"
              class="p-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <span
                      class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-gray-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </span>
                  </div>
                  <div>
                    <div class="flex items-center">
                      <h3 class="text-sm font-medium text-gray-900">
                        {{ ticket.subject }}
                      </h3>
                      <span
                        class="ml-2 text-xs px-2 py-0.5 rounded-full"
                        :class="getPriorityBadgeClass(ticket.priority)"
                      >
                        {{ ticket.priority }}
                      </span>
                    </div>
                    <div class="mt-1 flex items-center">
                      <span
                        class="text-xs px-2 py-0.5 rounded-full"
                        :class="getStatusBadgeClass(ticket.status)"
                      >
                        {{ ticket.status.replace("_", " ") }}
                      </span>
                      <span class="mx-2 text-gray-500 text-xs">•</span>
                      <span class="text-xs text-gray-500"
                        >from {{ ticket.user }}</span
                      >
                      <span
                        class="ml-1 text-xs px-2 py-0.5 rounded-full"
                        :class="
                          getUserTypeBadgeClass(ticket.userType.toLowerCase())
                        "
                      >
                        {{ ticket.userType }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xs text-gray-500">
                    {{ getTimeAgo(ticket.submitted) }}
                  </div>
                  <span
                    class="mt-1 inline-block text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-800"
                  >
                    {{ ticket.category }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="supportTickets.length > 3"
            class="px-6 py-3 bg-gray-50 border-t border-gray-200"
          >
            <NuxtLink
              to="/admin/support"
              class="text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center justify-center"
            >
              Show {{ supportTickets.length - 3 }} more
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Users -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden lg:col-span-2"
        >
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">
                Recent User Activations
              </h2>
              <NuxtLink
                to="/admin/users"
                class="text-sm text-indigo-600 font-medium hover:text-indigo-800"
                >View all users</NuxtLink
              >
            </div>
          </div>

          <div class="divide-y divide-gray-200">
            <div
              v-for="user in recentUsers"
              :key="user.id"
              class="p-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <img
                    :src="user.avatarUrl"
                    :alt="user.name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div>
                    <h3 class="text-sm font-medium text-gray-900">
                      {{ user.name }}
                    </h3>
                    <p class="text-xs text-gray-500">{{ user.email }}</p>
                  </div>
                </div>
                <div class="flex items-center">
                  <span
                    class="text-xs px-2 py-0.5 rounded-full mr-3"
                    :class="getUserTypeBadgeClass(user.type)"
                  >
                    {{ user.type }}
                  </span>
                  <div class="text-xs text-gray-500">
                    {{ getTimeAgo(user.joined) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Health -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">System Health</h2>
              <div class="flex items-center">
                <div
                  class="h-2.5 w-2.5 rounded-full animate-pulse mr-2"
                  :class="getHealthStatusClass(systemHealth.status)"
                ></div>
                <span class="text-sm font-medium capitalize">{{
                  systemHealth.status
                }}</span>
              </div>
            </div>
          </div>

          <div class="p-4 space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Uptime</span>
              <span class="text-sm font-medium">{{ systemHealth.uptime }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Response Time</span>
              <span class="text-sm font-medium">{{
                systemHealth.responseTime
              }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Server Load</span>
              <span class="text-sm font-medium"
                >{{ systemHealth.serverLoad }}%</span
              >
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Database Status</span>
              <span class="text-sm font-medium capitalize">{{
                systemHealth.databaseStatus
              }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">API Requests</span>
              <span class="text-sm font-medium">{{
                systemHealth.apiRequests
              }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Active Sessions</span>
              <span class="text-sm font-medium">{{
                formatNumber(systemHealth.activeSessions)
              }}</span>
            </div>
            <div class="pt-2 border-t border-gray-200">
              <span class="text-xs text-gray-500"
                >Last issue: {{ formatDate(systemHealth.lastIssue) }}</span
              >
            </div>
          </div>

          <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <NuxtLink
              to="/admin/system/health"
              class="text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center justify-center"
            >
              View detailed report
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- E-Learning Tab Content -->
    <div
      v-else-if="activeTab === 'e-learning'"
      class="bg-white rounded-lg shadow p-6 border border-gray-200"
    >
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">E-Learning Platform</h2>
        <NuxtLink
          to="/admin/courses"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
        >
          Manage Courses
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
          <h3 class="text-lg font-semibold text-indigo-700 mb-2">
            Total Courses
          </h3>
          <p class="text-3xl font-bold text-indigo-800">
            {{ filteredStats.totalCourses }}
          </p>
          <div class="mt-2 text-sm text-indigo-600">
            {{
              filteredStats.totalCourses - filteredStats.pendingApprovals
            }}
            published, {{ filteredStats.pendingApprovals }} pending
          </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
          <h3 class="text-lg font-semibold text-green-700 mb-2">Enrollments</h3>
          <p class="text-3xl font-bold text-green-800">
            {{ formatNumber(filteredStats.coursesEnrollments) }}
          </p>
          <div class="mt-2 text-sm text-green-600">
            Avg.
            {{
              (
                filteredStats.coursesEnrollments / filteredStats.totalCourses
              ).toFixed(0)
            }}
            per course
          </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
          <h3 class="text-lg font-semibold text-purple-700 mb-2">Revenue</h3>
          <p class="text-3xl font-bold text-purple-800">
            {{ formatCurrency(filteredStats.coursesRevenue) }}
          </p>
          <div class="mt-2 text-sm text-purple-600">
            {{
              (
                (filteredStats.coursesRevenue / filteredStats.totalRevenue) *
                100
              ).toFixed(0)
            }}% of total revenue
          </div>
        </div>
      </div>

      <p class="text-gray-600 mb-4">
        Please select E-Learning Management from the left sidebar to access
        detailed reports, course management tools, and instructor analytics.
      </p>

      <div
        class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-8 border border-gray-200"
      >
        <div class="text-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            E-Learning Dashboard
          </h3>
          <p class="text-sm text-gray-500">
            More detailed reports and management tools are available in the full
            dashboard view.
          </p>
        </div>
        <NuxtLink
          to="/admin/courses"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
        >
          Go to E-Learning Dashboard
        </NuxtLink>
      </div>
    </div>

    <!-- Consultancy Tab Content -->
    <div
      v-else-if="activeTab === 'consultancy'"
      class="bg-white rounded-lg shadow p-6 border border-gray-200"
    >
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Consultancy Platform</h2>
        <NuxtLink
          to="/admin/projects"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
        >
          Manage Projects
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
          <h3 class="text-lg font-semibold text-blue-700 mb-2">
            Active Projects
          </h3>
          <p class="text-3xl font-bold text-blue-800">
            {{ filteredStats.activeProjects }}
          </p>
          <div class="mt-2 text-sm text-blue-600">
            {{ filteredStats.projectsCompletedThisMonth }} completed this month
          </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
          <h3 class="text-lg font-semibold text-green-700 mb-2">Consultants</h3>
          <p class="text-3xl font-bold text-green-800">
            {{ filteredStats.activeConsultants }}
          </p>
          <div class="mt-2 text-sm text-green-600">
            Avg.
            {{
              (
                filteredStats.activeProjects / filteredStats.activeConsultants
              ).toFixed(1)
            }}
            projects per consultant
          </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
          <h3 class="text-lg font-semibold text-purple-700 mb-2">Revenue</h3>
          <p class="text-3xl font-bold text-purple-800">
            {{ formatCurrency(filteredStats.consultancyRevenue) }}
          </p>
          <div class="mt-2 text-sm text-purple-600">
            {{
              (
                (filteredStats.consultancyRevenue /
                  filteredStats.totalRevenue) *
                100
              ).toFixed(0)
            }}% of total revenue
          </div>
        </div>
      </div>

      <p class="text-gray-600 mb-4">
        Please select Consultancy Management from the left sidebar to access
        detailed reports, project management tools, and consultant analytics.
      </p>

      <div
        class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-8 border border-gray-200"
      >
        <div class="text-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            Consultancy Dashboard
          </h3>
          <p class="text-sm text-gray-500">
            More detailed reports and management tools are available in the full
            dashboard view.
          </p>
        </div>
        <NuxtLink
          to="/admin/projects"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
        >
          Go to Consultancy Dashboard
        </NuxtLink>
      </div>
    </div>

    <!-- Users Tab Content -->
    <div
      v-else-if="activeTab === 'users'"
      class="bg-white rounded-lg shadow p-6 border border-gray-200"
    >
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">User Management</h2>
        <NuxtLink
          to="/admin/users"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
        >
          Manage Users
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
          <h3 class="text-lg font-semibold text-indigo-700 mb-2">Students</h3>
          <p class="text-3xl font-bold text-indigo-800">
            {{ formatNumber(filteredStats.activeStudents) }}
          </p>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
          <h3 class="text-lg font-semibold text-purple-700 mb-2">
            Instructors
          </h3>
          <p class="text-3xl font-bold text-purple-800">
            {{ filteredStats.activeInstructors }}
          </p>
        </div>

        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
          <h3 class="text-lg font-semibold text-blue-700 mb-2">Consultants</h3>
          <p class="text-3xl font-bold text-blue-800">
            {{ filteredStats.activeConsultants }}
          </p>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
          <h3 class="text-lg font-semibold text-green-700 mb-2">Clients</h3>
          <p class="text-3xl font-bold text-green-800">
            {{ filteredStats.activeClients }}
          </p>
        </div>
      </div>

      <h3 class="text-lg font-medium text-gray-900 mb-4">
        Recent User Activations
      </h3>

      <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
        <div class="divide-y divide-gray-200">
          <div
            v-for="user in recentUsers"
            :key="user.id"
            class="p-4 hover:bg-white transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <img
                  :src="user.avatarUrl"
                  :alt="user.name"
                  class="h-10 w-10 rounded-full object-cover"
                />
                <div>
                  <h3 class="text-sm font-medium text-gray-900">
                    {{ user.name }}
                  </h3>
                  <p class="text-xs text-gray-500">{{ user.email }}</p>
                </div>
              </div>
              <div class="flex items-center">
                <span
                  class="text-xs px-2 py-0.5 rounded-full mr-3"
                  :class="getUserTypeBadgeClass(user.type)"
                >
                  {{ user.type }}
                </span>
                <div class="text-xs text-gray-500">
                  {{ getTimeAgo(user.joined) }}
                </div>
                <div class="ml-4 flex space-x-2">
                  <button
                    class="p-1 text-gray-500 hover:text-indigo-600"
                    title="Edit User"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                      />
                    </svg>
                  </button>
                  <button
                    class="p-1 text-gray-500 hover:text-red-600"
                    title="Delete User"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
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
        </div>
      </div>

      <div class="mt-6 flex justify-center">
        <NuxtLink
          to="/admin/users"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center"
        >
          View All Users
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </NuxtLink>
      </div>
    </div>

    <!-- System Health Tab Content -->
    <div
      v-else-if="activeTab === 'system'"
      class="bg-white rounded-lg shadow p-6 border border-gray-200"
    >
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">System Health</h2>
        <div class="flex items-center">
          <div
            class="h-3 w-3 rounded-full animate-pulse mr-2"
            :class="getHealthStatusClass(systemHealth.status)"
          ></div>
          <span class="font-medium capitalize"
            >Status: {{ systemHealth.status }}</span
          >
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Performance Metrics
          </h3>

          <div class="space-y-4">
            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm font-medium text-gray-700"
                  >Server Load</span
                >
                <span class="text-sm font-medium"
                  >{{ systemHealth.serverLoad }}%</span
                >
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="h-2 rounded-full"
                  :class="[
                    systemHealth.serverLoad < 50
                      ? 'bg-green-500'
                      : systemHealth.serverLoad < 80
                      ? 'bg-yellow-500'
                      : 'bg-red-500',
                  ]"
                  :style="{ width: `${systemHealth.serverLoad}%` }"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm font-medium text-gray-700">Database</span>
                <span class="text-sm font-medium capitalize">{{
                  systemHealth.databaseStatus
                }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="h-2 rounded-full bg-green-500"
                  :style="{
                    width:
                      systemHealth.databaseStatus === 'optimal'
                        ? '100%'
                        : '70%',
                  }"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm font-medium text-gray-700"
                  >Response Time</span
                >
                <span class="text-sm font-medium">{{
                  systemHealth.responseTime
                }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="h-2 rounded-full bg-green-500"
                  style="width: 85%"
                ></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm font-medium text-gray-700">Uptime</span>
                <span class="text-sm font-medium">{{
                  systemHealth.uptime
                }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="h-2 rounded-full bg-green-500"
                  style="width: 99%"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Traffic & Usage
          </h3>

          <div class="space-y-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-blue-100 mr-4">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-blue-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500 font-medium">API Requests</p>
                <p class="text-xl font-bold text-gray-900">
                  {{ systemHealth.apiRequests }}
                </p>
              </div>
            </div>

            <div class="flex items-center">
              <div class="p-3 rounded-full bg-green-100 mr-4">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-green-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                  />
                </svg>
              </div>
              <div>
                <p class="text-sm text-gray-500 font-medium">Active Sessions</p>
                <p class="text-xl font-bold text-gray-900">
                  {{ formatNumber(systemHealth.activeSessions) }}
                </p>
              </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
              <h4 class="text-sm font-medium text-gray-700 mb-2">
                Last System Issue
              </h4>
              <p class="text-sm text-gray-600">
                {{ formatDate(systemHealth.lastIssue) }}
              </p>
              <p class="text-xs text-gray-500 mt-1">
                Database connection timeout (resolved in 2 minutes)
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          class="bg-green-50 rounded-lg p-4 border border-green-100 flex items-center"
        >
          <div class="p-2 rounded-full bg-green-100 mr-3">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-green-600"
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
          <div>
            <h3 class="text-sm font-medium text-green-800">Backups</h3>
            <p class="text-xs text-green-600">
              Last successful: Today, 4:00 AM
            </p>
          </div>
        </div>

        <div
          class="bg-blue-50 rounded-lg p-4 border border-blue-100 flex items-center"
        >
          <div class="p-2 rounded-full bg-blue-100 mr-3">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-blue-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-blue-800">Security</h3>
            <p class="text-xs text-blue-600">
              All systems secured, 0 threats detected
            </p>
          </div>
        </div>

        <div
          class="bg-purple-50 rounded-lg p-4 border border-purple-100 flex items-center"
        >
          <div class="p-2 rounded-full bg-purple-100 mr-3">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-purple-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-purple-800">Performance</h3>
            <p class="text-xs text-purple-600">
              All services operating at optimal levels
            </p>
          </div>
        </div>
      </div>

      <div class="mt-6 flex justify-center">
        <NuxtLink
          to="/admin/system/health"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center"
        >
          View Detailed System Reports
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>
