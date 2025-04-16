<!-- pages/dashboard/consultant/analytics/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Analytics & Performance - Consultant Dashboard",
});

// Router instance
const router = useRouter();

// Types
interface Client {
  id: number;
  name: string;
  avatar: string;
  company: string;
  engagementScore: number;
  projectsCount: number;
  totalValue: number;
  lastActivity: string;
}

interface Project {
  id: number;
  title: string;
  clientName: string;
  status: "in_progress" | "completed" | "pending" | "delayed";
  startDate: string;
  endDate: string;
  revenue: number;
  hours: number;
  progress: number;
}

interface RevenueData {
  month: string;
  consulting: number;
  courses: number;
  other: number;
  total: number;
}

interface TimeDistribution {
  category: string;
  hours: number;
  percentage: number;
  color: string;
}

// State
const isLoading = ref(true);
const dateRangeFilter = ref("last30days");
const clients = ref<Client[]>([]);
const projects = ref<Project[]>([]);
const revenueData = ref<RevenueData[]>([]);
const timeDistribution = ref<TimeDistribution[]>([]);

// Performance metrics
const performanceMetrics = ref({
  totalRevenue: 0,
  projectsCompleted: 0,
  activeClients: 0,
  avgProjectValue: 0,
  utilization: 0,
  avgClientRating: 0,
  clientRetention: 0,
  avgResponseTime: 0,
});

// Chart options
const chartTimeRange = ref("year");
const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const isExpandedView = ref(false);

// Generate mock data
const generateMockData = () => {
  // Generate clients
  clients.value = [
    {
      id: 1,
      name: "Acme Corporation",
      avatar: "/api/placeholder/40/40",
      company: "Acme Corporation",
      engagementScore: 95,
      projectsCount: 3,
      totalValue: 28500,
      lastActivity: "2025-04-10T15:30:00Z",
    },
    {
      id: 2,
      name: "TechStart Inc",
      avatar: "/api/placeholder/40/40",
      company: "TechStart Inc",
      engagementScore: 88,
      projectsCount: 2,
      totalValue: 17500,
      lastActivity: "2025-04-12T09:45:00Z",
    },
    {
      id: 3,
      name: "Global Services Ltd",
      avatar: "/api/placeholder/40/40",
      company: "Global Services Ltd",
      engagementScore: 92,
      projectsCount: 1,
      totalValue: 12000,
      lastActivity: "2025-04-05T14:20:00Z",
    },
    {
      id: 4,
      name: "Innovate Solutions",
      avatar: "/api/placeholder/40/40",
      company: "Innovate Solutions",
      engagementScore: 78,
      projectsCount: 2,
      totalValue: 15800,
      lastActivity: "2025-04-08T11:15:00Z",
    },
    {
      id: 5,
      name: "NextGen Retail",
      avatar: "/api/placeholder/40/40",
      company: "NextGen Retail",
      engagementScore: 85,
      projectsCount: 1,
      totalValue: 9500,
      lastActivity: "2025-03-28T16:30:00Z",
    },
  ];

  // Generate projects
  projects.value = [
    {
      id: 1,
      title: "E-commerce Platform Redesign",
      clientName: "Acme Corporation",
      status: "in_progress",
      startDate: "2025-03-15",
      endDate: "2025-06-30",
      revenue: 15000,
      hours: 120,
      progress: 65,
    },
    {
      id: 2,
      title: "Mobile App Development",
      clientName: "TechStart Inc",
      status: "in_progress",
      startDate: "2025-02-01",
      endDate: "2025-05-15",
      revenue: 12500,
      hours: 95,
      progress: 80,
    },
    {
      id: 3,
      title: "SEO Optimization",
      clientName: "NextGen Retail",
      status: "completed",
      startDate: "2025-01-10",
      endDate: "2025-03-15",
      revenue: 9500,
      hours: 70,
      progress: 100,
    },
    {
      id: 4,
      title: "Data Analytics Dashboard",
      clientName: "Global Services Ltd",
      status: "in_progress",
      startDate: "2025-03-01",
      endDate: "2025-05-30",
      revenue: 12000,
      hours: 85,
      progress: 45,
    },
    {
      id: 5,
      title: "Cloud Migration Strategy",
      clientName: "Innovate Solutions",
      status: "pending",
      startDate: "2025-04-20",
      endDate: "2025-07-31",
      revenue: 18500,
      hours: 140,
      progress: 0,
    },
    {
      id: 6,
      title: "Website Accessibility Upgrade",
      clientName: "Acme Corporation",
      status: "delayed",
      startDate: "2025-02-15",
      endDate: "2025-04-30",
      revenue: 7500,
      hours: 60,
      progress: 35,
    },
    {
      id: 7,
      title: "Custom CRM Implementation",
      clientName: "TechStart Inc",
      status: "completed",
      startDate: "2024-11-01",
      endDate: "2025-02-28",
      revenue: 22000,
      hours: 160,
      progress: 100,
    },
  ];

  // Generate revenue data
  revenueData.value = [
    { month: "Jan", consulting: 7800, courses: 2500, other: 800, total: 11100 },
    { month: "Feb", consulting: 8500, courses: 3200, other: 950, total: 12650 },
    {
      month: "Mar",
      consulting: 9200,
      courses: 3800,
      other: 1200,
      total: 14200,
    },
    {
      month: "Apr",
      consulting: 11500,
      courses: 4100,
      other: 1100,
      total: 16700,
    },
    {
      month: "May",
      consulting: 10800,
      courses: 4500,
      other: 1300,
      total: 16600,
    },
    {
      month: "Jun",
      consulting: 12400,
      courses: 5200,
      other: 1500,
      total: 19100,
    },
    {
      month: "Jul",
      consulting: 14500,
      courses: 5800,
      other: 1700,
      total: 22000,
    },
    {
      month: "Aug",
      consulting: 15200,
      courses: 6100,
      other: 1800,
      total: 23100,
    },
    {
      month: "Sep",
      consulting: 13800,
      courses: 5500,
      other: 1600,
      total: 20900,
    },
    {
      month: "Oct",
      consulting: 12200,
      courses: 4800,
      other: 1400,
      total: 18400,
    },
    {
      month: "Nov",
      consulting: 10500,
      courses: 4200,
      other: 1100,
      total: 15800,
    },
    {
      month: "Dec",
      consulting: 9800,
      courses: 3900,
      other: 1000,
      total: 14700,
    },
  ];

  // Generate time distribution data
  timeDistribution.value = [
    {
      category: "Client Meetings",
      hours: 45,
      percentage: 28,
      color: "bg-indigo-500",
    },
    {
      category: "Development",
      hours: 65,
      percentage: 41,
      color: "bg-blue-500",
    },
    { category: "Research", hours: 18, percentage: 11, color: "bg-green-500" },
    {
      category: "Documentation",
      hours: 15,
      percentage: 9,
      color: "bg-amber-500",
    },
    {
      category: "Admin Tasks",
      hours: 10,
      percentage: 6,
      color: "bg-purple-500",
    },
    { category: "Learning", hours: 8, percentage: 5, color: "bg-pink-500" },
  ];

  // Generate performance metrics
  const totalRevenue = projects.value.reduce(
    (sum, project) => sum + project.revenue,
    0
  );
  const completedProjects = projects.value.filter(
    (project) => project.status === "completed"
  ).length;

  performanceMetrics.value = {
    totalRevenue: totalRevenue,
    projectsCompleted: completedProjects,
    activeClients: clients.value.length,
    avgProjectValue: Math.round(totalRevenue / projects.value.length),
    utilization: 85, // percentage
    avgClientRating: 4.8,
    clientRetention: 92, // percentage
    avgResponseTime: 2.5, // hours
  };
};

// Fetch analytics data
const fetchAnalyticsData = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Generate mock data
    generateMockData();
  } catch (error) {
    console.error("Error fetching analytics data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Format currency value
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
};

// Format date
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format time ago
const formatTimeAgo = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));

  if (diffDays > 0) {
    return `${diffDays}d ago`;
  } else {
    return `${diffHours}h ago`;
  }
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "in_progress":
      return "bg-blue-100 text-blue-800";
    case "completed":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-amber-100 text-amber-800";
    case "delayed":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format status display
const formatStatus = (status: string) => {
  return status.replace("_", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Get engagement score color
const getEngagementScoreColor = (score: number) => {
  if (score >= 90) return "text-green-600";
  if (score >= 80) return "text-blue-600";
  if (score >= 70) return "text-amber-600";
  return "text-red-600";
};

// Filter revenue data based on time range
const filteredRevenueData = computed(() => {
  let data = [...revenueData.value];

  if (chartTimeRange.value === "quarter") {
    // Just show last 3 months
    data = data.slice(-3);
  } else if (chartTimeRange.value === "month") {
    // Just show last month
    data = data.slice(-1);
  }

  return data;
});

// Total revenue year to date
const yearToDateRevenue = computed(() => {
  return revenueData.value.reduce((sum, month) => sum + month.total, 0);
});

// Top clients by revenue
const topClients = computed(() => {
  return [...clients.value]
    .sort((a, b) => b.totalValue - a.totalValue)
    .slice(0, 3);
});

// Projects by status
const projectsByStatus = computed(() => {
  const counts = {
    in_progress: 0,
    completed: 0,
    pending: 0,
    delayed: 0,
  };

  projects.value.forEach((project) => {
    counts[project.status]++;
  });

  return counts;
});

// Filter data when date range changes
watch(dateRangeFilter, () => {
  fetchAnalyticsData();
});

// Load data on mount
onMounted(() => {
  fetchAnalyticsData();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Analytics & Performance
          </h1>
          <p class="text-gray-600">
            Track your performance metrics, revenue, and client engagement
          </p>
        </div>
        <div class="flex space-x-3">
          <!-- Date range filter -->
          <div class="relative">
            <select
              v-model="dateRangeFilter"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="last30days">Last 30 Days</option>
              <option value="last90days">Last 90 Days</option>
              <option value="year">This Year</option>
              <option value="all">All Time</option>
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

          <!-- Export report button -->
          <button
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
            Export Report
          </button>
        </div>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="animate-pulse">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div
          v-for="i in 4"
          :key="`metric-${i}`"
          class="bg-white rounded-lg shadow p-6 border border-gray-200 h-40"
        ></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div
          class="bg-white rounded-lg shadow p-6 border border-gray-200 h-96"
        ></div>
        <div
          class="bg-white rounded-lg shadow p-6 border border-gray-200 h-96"
        ></div>
      </div>
    </div>

    <div v-else>
      <!-- Key Performance Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
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
                {{ formatCurrency(performanceMetrics.totalRevenue) }}
              </p>
              <div class="mt-1 flex items-center">
                <span
                  class="text-green-600 text-sm font-medium flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  12.8%
                </span>
                <span class="text-gray-500 text-xs ml-1">vs last period</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Projects Completed -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
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
              <p class="text-sm text-gray-500 font-medium">
                Projects Completed
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ performanceMetrics.projectsCompleted }}
              </p>
              <div class="mt-1 flex items-center">
                <span
                  class="text-green-600 text-sm font-medium flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  3
                </span>
                <span class="text-gray-500 text-xs ml-1"
                  >more than last period</span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Client Retention -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
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
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Client Retention</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ performanceMetrics.clientRetention }}%
              </p>
              <div class="mt-1 flex items-center">
                <span
                  class="text-green-600 text-sm font-medium flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  5%
                </span>
                <span class="text-gray-500 text-xs ml-1">vs last period</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Utilization Rate -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
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
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Utilization Rate</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ performanceMetrics.utilization }}%
              </p>
              <div class="mt-1 flex items-center">
                <span
                  class="text-amber-600 text-sm font-medium flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  2%
                </span>
                <span class="text-gray-500 text-xs ml-1">vs target (87%)</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Charts & Time Distribution -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-lg font-bold text-gray-900">Revenue Breakdown</h2>
              <div class="flex space-x-2">
                <button
                  v-for="option in ['year', 'quarter', 'month']"
                  :key="option"
                  @click="chartTimeRange = option"
                  class="px-3 py-1 text-sm font-medium rounded-md transition-colors"
                  :class="
                    chartTimeRange === option
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
                  "
                >
                  {{ option.charAt(0).toUpperCase() + option.slice(1) }}
                </button>
              </div>
            </div>

            <!-- Revenue Chart (placeholder) -->
            <div class="relative" style="height: 300px">
              <!-- This would be replaced with an actual chart component in a real app -->
              <div class="absolute bottom-0 left-0 right-0 flex h-64">
                <div
                  v-for="(month, index) in filteredRevenueData"
                  :key="index"
                  class="flex-1 flex flex-col justify-end items-center px-1"
                >
                  <!-- Consulting Revenue -->
                  <div
                    class="w-full bg-indigo-500 transition-all duration-300"
                    :style="{
                      height: `${(month.consulting / month.total) * 100}%`,
                    }"
                  ></div>

                  <!-- Course Revenue -->
                  <div
                    class="w-full bg-green-500 transition-all duration-300"
                    :style="{
                      height: `${(month.courses / month.total) * 100}%`,
                    }"
                  ></div>

                  <!-- Other Revenue -->
                  <div
                    class="w-full bg-amber-500 transition-all duration-300"
                    :style="{ height: `${(month.other / month.total) * 100}%` }"
                  ></div>

                  <!-- Month Label -->
                  <div class="text-xs text-gray-500 mt-2">
                    {{ month.month }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Chart Legend -->
            <div class="mt-4 flex justify-center items-center space-x-6">
              <div class="flex items-center">
                <div class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Consulting</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Courses</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-amber-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Other</span>
              </div>
            </div>

            <!-- Chart Summary -->
            <div class="mt-6 grid grid-cols-3 gap-4">
              <div class="text-center">
                <div class="text-xl font-bold text-indigo-600">
                  {{ formatCurrency(yearToDateRevenue) }}
                </div>
                <div class="text-sm text-gray-500">Total YTD</div>
              </div>
              <div class="text-center">
                <div class="text-xl font-bold text-green-600">
                  {{ formatCurrency(performanceMetrics.avgProjectValue) }}
                </div>
                <div class="text-sm text-gray-500">Avg. Project Value</div>
              </div>
              <div class="text-center">
                <div class="text-xl font-bold text-amber-600">4.8</div>
                <div class="text-sm text-gray-500">Avg. Rating</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Time Distribution -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-lg font-bold text-gray-900">Time Distribution</h2>
              <button
                @click="isExpandedView = !isExpandedView"
                class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center font-medium"
              >
                {{ isExpandedView ? "Collapse" : "Expand" }}
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1"
                  :class="{ 'transform rotate-180': isExpandedView }"
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
            </div>

            <!-- Time Distribution Chart (horizontal bars) -->
            <div class="space-y-4">
              <div
                v-for="category in timeDistribution"
                :key="category.category"
                class="group"
              >
                <div class="flex justify-between items-center text-sm mb-1">
                  <span class="font-medium text-gray-700">{{
                    category.category
                  }}</span>
                  <div class="text-right">
                    <span class="font-medium text-gray-900"
                      >{{ category.hours }}h</span
                    >
                    <span class="text-gray-500 ml-1"
                      >({{ category.percentage }}%)</span
                    >
                  </div>
                </div>
                <div class="h-6 bg-gray-200 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-500 transform origin-left group-hover:scale-x-105"
                    :class="category.color"
                    :style="{ width: `${category.percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Total Hours -->
            <div class="mt-6 text-center">
              <div class="text-2xl font-bold text-gray-900">
                {{
                  timeDistribution.reduce((sum, item) => sum + item.hours, 0)
                }}
                hours
              </div>
              <div class="text-sm text-gray-500">
                Total billable hours this period
              </div>
            </div>

            <!-- Additional stats when expanded -->
            <div
              v-if="isExpandedView"
              class="mt-6 pt-6 border-t border-gray-200"
            >
              <div class="grid grid-cols-2 gap-6">
                <div>
                  <div class="text-sm text-gray-500 mb-1">
                    Average Daily Hours
                  </div>
                  <div class="text-lg font-bold text-gray-900">7.2 hours</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">Billable Ratio</div>
                  <div class="text-lg font-bold text-gray-900">89%</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">
                    Most Productive Day
                  </div>
                  <div class="text-lg font-bold text-gray-900">Tuesday</div>
                </div>
                <div>
                  <div class="text-sm text-gray-500 mb-1">
                    Most Productive Time
                  </div>
                  <div class="text-lg font-bold text-gray-900">9am - 12pm</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Projects Overview -->
      <div class="bg-white rounded-lg shadow mb-8 border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Projects Overview</h2>
            <div class="flex items-center space-x-6">
              <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600"
                  >In Progress ({{ projectsByStatus.in_progress }})</span
                >
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600"
                  >Completed ({{ projectsByStatus.completed }})</span
                >
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-amber-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600"
                  >Pending ({{ projectsByStatus.pending }})</span
                >
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600"
                  >Delayed ({{ projectsByStatus.delayed }})</span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Project List -->
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="bg-gray-50">
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Project
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Client
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Timeline
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Revenue
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Progress
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="project in projects"
                :key="project.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ project.title }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ project.clientName }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ formatDate(project.startDate) }} -
                    {{ formatDate(project.endDate) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                    :class="getStatusBadgeClasses(project.status)"
                  >
                    {{ formatStatus(project.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ formatCurrency(project.revenue) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm text-gray-900 mb-1">
                      {{ project.progress }}%
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full"
                        :class="{
                          'bg-blue-500': project.status === 'in_progress',
                          'bg-green-500': project.status === 'completed',
                          'bg-amber-500': project.status === 'pending',
                          'bg-red-500': project.status === 'delayed',
                        }"
                        :style="{ width: `${project.progress}%` }"
                      ></div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Client Engagement -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Top Clients -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Top Clients</h2>
          </div>
          <div class="p-6">
            <div class="space-y-6">
              <div
                v-for="client in topClients"
                :key="client.id"
                class="flex items-start"
              >
                <img
                  :src="client.avatar"
                  :alt="client.name"
                  class="h-10 w-10 rounded-full mr-4 object-cover"
                />
                <div class="flex-1">
                  <div class="flex justify-between items-start">
                    <div>
                      <h3 class="text-sm font-medium text-gray-900">
                        {{ client.name }}
                      </h3>
                      <p class="text-xs text-gray-500">
                        {{ client.projectsCount }} projects
                      </p>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatCurrency(client.totalValue) }}
                      </div>
                      <div
                        :class="`text-xs ${getEngagementScoreColor(
                          client.engagementScore
                        )}`"
                      >
                        {{ client.engagementScore }}% engagement
                      </div>
                    </div>
                  </div>
                  <div
                    class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden"
                  >
                    <div
                      class="h-full rounded-full"
                      :class="{
                        'bg-green-500': client.engagementScore >= 90,
                        'bg-blue-500':
                          client.engagementScore >= 80 &&
                          client.engagementScore < 90,
                        'bg-amber-500':
                          client.engagementScore >= 70 &&
                          client.engagementScore < 80,
                        'bg-red-500': client.engagementScore < 70,
                      }"
                      :style="{ width: `${client.engagementScore}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 text-center">
              <button
                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
              >
                View All Clients
              </button>
            </div>
          </div>
        </div>

        <!-- Client Satisfaction -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Client Satisfaction</h2>
          </div>
          <div class="p-6">
            <!-- Satisfaction Score -->
            <div class="text-center mb-6">
              <div class="text-4xl font-bold text-gray-900 mb-2">
                {{ performanceMetrics.avgClientRating }}
              </div>
              <div class="flex justify-center">
                <div class="flex text-yellow-400">
                  <svg
                    v-for="i in 5"
                    :key="i"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
              </div>
              <div class="text-sm text-gray-500 mt-2">
                Average rating from {{ clients.length }} clients
              </div>
            </div>

            <!-- Rating Breakdown -->
            <div class="space-y-2 mb-6">
              <div
                v-for="rating in [5, 4, 3, 2, 1]"
                :key="rating"
                class="flex items-center"
              >
                <div class="flex text-yellow-400 items-center w-16">
                  <span class="text-sm font-medium text-gray-700 mr-2">{{
                    rating
                  }}</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
                <div
                  class="flex-1 h-2 bg-gray-200 rounded-full ml-2 overflow-hidden"
                >
                  <div
                    class="h-full bg-yellow-400 rounded-full"
                    :style="{
                      width:
                        rating === 5
                          ? '75%'
                          : rating === 4
                          ? '20%'
                          : rating === 3
                          ? '5%'
                          : rating === 2
                          ? '0%'
                          : '0%',
                    }"
                  ></div>
                </div>
                <div class="ml-2 text-xs text-gray-500 w-8 text-right">
                  {{
                    rating === 5
                      ? "75%"
                      : rating === 4
                      ? "20%"
                      : rating === 3
                      ? "5%"
                      : rating === 2
                      ? "0%"
                      : "0%"
                  }}
                </div>
              </div>
            </div>

            <!-- Response Time -->
            <div class="text-center pt-4 border-t border-gray-200">
              <div class="text-lg font-bold text-gray-900 mb-1">
                {{ performanceMetrics.avgResponseTime }} hours
              </div>
              <div class="text-sm text-gray-500">Average response time</div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
          </div>
          <div class="p-6">
            <ul class="space-y-4">
              <li class="flex items-start">
                <div class="bg-blue-100 p-2 rounded-full mr-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-blue-600"
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
                </div>
                <div>
                  <p class="text-sm text-gray-900">
                    Project proposal for
                    <span class="font-medium">Global Services Ltd</span> was
                    sent
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ formatTimeAgo("2025-04-15T10:30:00Z") }}
                  </p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="bg-green-100 p-2 rounded-full mr-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-600"
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
                  <p class="text-sm text-gray-900">
                    Completed
                    <span class="font-medium">SEO Optimization</span> project
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ formatTimeAgo("2025-04-14T16:45:00Z") }}
                  </p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="bg-indigo-100 p-2 rounded-full mr-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-indigo-600"
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
                  <p class="text-sm text-gray-900">
                    Received payment of
                    <span class="font-medium">{{ formatCurrency(12500) }}</span>
                    from <span class="font-medium">TechStart Inc</span>
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ formatTimeAgo("2025-04-13T11:20:00Z") }}
                  </p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="bg-amber-100 p-2 rounded-full mr-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-amber-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-900">
                    New project request from
                    <span class="font-medium">Innovate Solutions</span>
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ formatTimeAgo("2025-04-12T09:15:00Z") }}
                  </p>
                </div>
              </li>
              <li class="flex items-start">
                <div class="bg-purple-100 p-2 rounded-full mr-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-purple-600"
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
                </div>
                <div>
                  <p class="text-sm text-gray-900">
                    Meeting scheduled with
                    <span class="font-medium">Acme Corporation</span>
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ formatTimeAgo("2025-04-11T14:30:00Z") }}
                  </p>
                </div>
              </li>
            </ul>

            <div class="mt-6 text-center">
              <button
                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
              >
                View All Activity
              </button>
            </div>
          </div>
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

/* Chart bar animations */
.bar-enter-active,
.bar-leave-active {
  transition: all 0.5s ease;
}
.bar-enter-from,
.bar-leave-to {
  transform: scaleY(0);
  opacity: 0;
}
</style>
