<!-- pages/dashboard/client/reports/index.vue -->
<script setup lang="ts">
import { ref, onMounted, computed } from "vue";

// Set page layout and metadata
definePageMeta({
  layout: "dashboard-client",
});

useHead({
  title: "Reports & Analytics - Client Dashboard",
  meta: [
    {
      name: "description",
      content:
        "View detailed reports and analytics for your projects and consultancy services",
    },
  ],
});

// State variables
const isLoading = ref(true);
const selectedDateRange = ref("30days");
const selectedReportType = ref("projects");
const selectedProjectId = ref<number | null>(null);

// Filter state
const showFilters = ref(false);
const filterConsultant = ref<string | null>(null);
const filterServiceType = ref<string | null>(null);
const filterStatus = ref<string | null>(null);

// Mock data - in a real app, this would come from an API
const projects = ref([
  {
    id: 1,
    name: "E-Commerce Website Redesign",
    completionPercentage: 75,
    consultantName: "Sarah Parker",
    startDate: "2025-01-15",
    endDate: "2025-05-15",
    status: "active",
    totalHours: 120,
    hoursLogged: 90,
    budget: 18000,
    spent: 13500,
  },
  {
    id: 2,
    name: "Mobile App Development",
    completionPercentage: 40,
    consultantName: "Alex Thompson",
    startDate: "2025-02-10",
    endDate: "2025-06-30",
    status: "active",
    totalHours: 200,
    hoursLogged: 80,
    budget: 30000,
    spent: 12000,
  },
  {
    id: 3,
    name: "CRM Integration",
    completionPercentage: 100,
    consultantName: "Maya Rodriguez",
    startDate: "2024-11-05",
    endDate: "2025-02-28",
    status: "completed",
    totalHours: 80,
    hoursLogged: 85,
    budget: 12000,
    spent: 12800,
  },
]);

const consultants = ref([
  {
    id: 1,
    name: "Sarah Parker",
    role: "Senior Software Consultant",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 2,
    name: "Alex Thompson",
    role: "Mobile Development Specialist",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 3,
    name: "Maya Rodriguez",
    role: "CRM Implementation Expert",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 4,
    name: "James Wilson",
    role: "UX/UI Design Consultant",
    avatar: "/api/placeholder/40/40",
  },
]);

const serviceTypes = ref([
  "Web Development",
  "Mobile Development",
  "UX/UI Design",
  "System Integration",
  "Database Design",
  "Cloud Architecture",
  "DevOps",
]);

const statusOptions = ref(["active", "completed", "on-hold", "cancelled"]);

// Activity data
const recentActivities = ref([
  {
    id: 1,
    type: "milestone",
    project: "E-Commerce Website Redesign",
    description: "Frontend development phase completed",
    date: "2025-04-10T14:30:00Z",
    user: "Sarah Parker",
  },
  {
    id: 2,
    type: "timeLog",
    project: "Mobile App Development",
    description: "API integration - 6 hours",
    date: "2025-04-09T10:15:00Z",
    user: "Alex Thompson",
  },
  {
    id: 3,
    type: "payment",
    project: "E-Commerce Website Redesign",
    description: "Invoice #INV-2025-042 paid",
    amount: 4500,
    date: "2025-04-08T16:45:00Z",
    user: "System",
  },
  {
    id: 4,
    type: "document",
    project: "CRM Integration",
    description: "Final delivery document uploaded",
    date: "2025-04-07T11:20:00Z",
    user: "Maya Rodriguez",
  },
  {
    id: 5,
    type: "meeting",
    project: "Mobile App Development",
    description: "Sprint planning meeting",
    date: "2025-04-06T15:00:00Z",
    duration: 60,
    user: "Alex Thompson",
  },
  {
    id: 6,
    type: "feedback",
    project: "CRM Integration",
    description: "Project satisfaction survey completed",
    rating: 4.8,
    date: "2025-04-05T09:30:00Z",
    user: "You",
  },
]);

// Time tracking data (hours per day for the last 30 days)
const timeTrackingData = ref({
  labels: Array.from({ length: 30 }, (_, i) => {
    const date = new Date();
    date.setDate(date.getDate() - (29 - i));
    return date.toLocaleDateString("en-US", { month: "short", day: "numeric" });
  }),
  datasets: [
    {
      label: "E-Commerce Website Redesign",
      data: generateRandomData(30, 2, 6),
      borderColor: "#6366F1",
      backgroundColor: "rgba(99, 102, 241, 0.1)",
      fill: true,
    },
    {
      label: "Mobile App Development",
      data: generateRandomData(30, 1, 5),
      borderColor: "#10B981",
      backgroundColor: "rgba(16, 185, 129, 0.1)",
      fill: true,
    },
    {
      label: "CRM Integration",
      data: generateRandomData(30, 0, 4),
      borderColor: "#F59E0B",
      backgroundColor: "rgba(245, 158, 11, 0.1)",
      fill: true,
    },
  ],
});

// Budget utilization chart data
const budgetData = ref({
  labels: projects.value.map((p) => p.name),
  datasets: [
    {
      label: "Budget",
      data: projects.value.map((p) => p.budget),
      backgroundColor: "rgba(99, 102, 241, 0.8)",
    },
    {
      label: "Spent",
      data: projects.value.map((p) => p.spent),
      backgroundColor: "rgba(16, 185, 129, 0.8)",
    },
  ],
});

// Project completion chart data
const projectProgressData = ref({
  labels: projects.value.map((p) => p.name),
  datasets: [
    {
      label: "Completion Percentage",
      data: projects.value.map((p) => p.completionPercentage),
      backgroundColor: projects.value.map((p) => {
        if (p.completionPercentage < 30) return "rgba(239, 68, 68, 0.8)";
        if (p.completionPercentage < 70) return "rgba(245, 158, 11, 0.8)";
        return "rgba(16, 185, 129, 0.8)";
      }),
      borderColor: projects.value.map((p) => {
        if (p.completionPercentage < 30) return "rgb(239, 68, 68)";
        if (p.completionPercentage < 70) return "rgb(245, 158, 11)";
        return "rgb(16, 185, 129)";
      }),
      borderWidth: 1,
    },
  ],
});

// Helper to generate random data for the time tracking chart
function generateRandomData(days: number, min: number, max: number) {
  return Array.from(
    { length: days },
    () => Math.floor(Math.random() * (max - min + 1)) + min
  );
}

// Format date function
function formatDate(dateString: string, format: "short" | "long" = "short") {
  const date = new Date(dateString);
  if (format === "short") {
    return date.toLocaleDateString("en-US", { month: "short", day: "numeric" });
  }
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

// Format currency
function formatCurrency(amount: number) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
}

// Format relative time
function formatRelativeTime(dateString: string) {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffSec = Math.round(diffMs / 1000);
  const diffMin = Math.round(diffSec / 60);
  const diffHour = Math.round(diffMin / 60);
  const diffDay = Math.round(diffHour / 24);

  if (diffSec < 60) {
    return "just now";
  } else if (diffMin < 60) {
    return `${diffMin} minute${diffMin === 1 ? "" : "s"} ago`;
  } else if (diffHour < 24) {
    return `${diffHour} hour${diffHour === 1 ? "" : "s"} ago`;
  } else if (diffDay < 7) {
    return `${diffDay} day${diffDay === 1 ? "" : "s"} ago`;
  } else {
    return formatDate(dateString);
  }
}

// Get project by ID
const getProjectById = (id: number) => {
  return projects.value.find((p) => p.id === id) || null;
};

// Selected project (for detailed view)
const selectedProject = computed(() => {
  if (!selectedProjectId.value) return null;
  return getProjectById(selectedProjectId.value);
});

// Filter projects based on selected filters
const filteredProjects = computed(() => {
  let result = [...projects.value];

  if (filterConsultant.value) {
    result = result.filter((p) => p.consultantName === filterConsultant.value);
  }

  if (filterStatus.value) {
    result = result.filter((p) => p.status === filterStatus.value);
  }

  return result;
});

// Update chart data based on date range
const updateChartData = (range: string) => {
  selectedDateRange.value = range;

  // In a real app, you would fetch data for the specific date range
  // Here we're just simulating by generating new random data

  const daysInRange =
    range === "7days"
      ? 7
      : range === "30days"
      ? 30
      : range === "90days"
      ? 90
      : 30;

  timeTrackingData.value = {
    labels: Array.from({ length: daysInRange }, (_, i) => {
      const date = new Date();
      date.setDate(date.getDate() - (daysInRange - 1 - i));
      return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
      });
    }),
    datasets: [
      {
        label: "E-Commerce Website Redesign",
        data: generateRandomData(daysInRange, 2, 6),
        borderColor: "#6366F1",
        backgroundColor: "rgba(99, 102, 241, 0.1)",
        fill: true,
      },
      {
        label: "Mobile App Development",
        data: generateRandomData(daysInRange, 1, 5),
        borderColor: "#10B981",
        backgroundColor: "rgba(16, 185, 129, 0.1)",
        fill: true,
      },
      {
        label: "CRM Integration",
        data: generateRandomData(daysInRange, 0, 4),
        borderColor: "#F59E0B",
        backgroundColor: "rgba(245, 158, 11, 0.1)",
        fill: true,
      },
    ],
  };

  // Simulating loading state
  isLoading.value = true;
  setTimeout(() => {
    isLoading.value = false;
  }, 800);
};

// Reset filters
const resetFilters = () => {
  filterConsultant.value = null;
  filterServiceType.value = null;
  filterStatus.value = null;
};

// Download report
const downloadReport = (format: "pdf" | "csv" | "excel") => {
  // In a real app, this would trigger a download
  alert(`Downloading report in ${format.toUpperCase()} format`);
};

// Toggle filters visibility
const toggleFilters = () => {
  showFilters.value = !showFilters.value;
};

// Select a project for detailed view
const selectProject = (projectId: number) => {
  selectedProjectId.value = projectId;
  selectedReportType.value = "projectDetail";
};

// Back to projects list
const backToProjects = () => {
  selectedProjectId.value = null;
  selectedReportType.value = "projects";
};

// Simulate data loading on mount
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 1000);
});

// Activity icon mapping
const getActivityIcon = (type: string) => {
  switch (type) {
    case "milestone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "timeLog":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
      </svg>`;
    case "payment":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
      </svg>`;
    case "document":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "meeting":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z" clip-rule="evenodd" />
      </svg>`;
    case "feedback":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Activity background color mapping
const getActivityBgColor = (type: string) => {
  switch (type) {
    case "milestone":
      return "bg-green-100 text-green-800";
    case "timeLog":
      return "bg-blue-100 text-blue-800";
    case "payment":
      return "bg-purple-100 text-purple-800";
    case "document":
      return "bg-indigo-100 text-indigo-800";
    case "meeting":
      return "bg-amber-100 text-amber-800";
    case "feedback":
      return "bg-pink-100 text-pink-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Status styles
const getStatusStyles = (status: string) => {
  switch (status) {
    case "active":
      return "bg-green-100 text-green-800";
    case "completed":
      return "bg-blue-100 text-blue-800";
    case "on-hold":
      return "bg-amber-100 text-amber-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get project completion color
const getCompletionColor = (percentage: number) => {
  if (percentage < 30) return "bg-red-500";
  if (percentage < 70) return "bg-amber-500";
  return "bg-green-500";
};
</script>

<template>
  <div>
    <!-- Page Header -->
    <div
      class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">
          Reports & Analytics
        </h1>
        <p class="text-gray-600">
          Track your projects, monitor progress, and analyze performance
        </p>
      </div>

      <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
        <button
          @click="toggleFilters"
          class="flex items-center gap-1 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors"
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
        </button>

        <div class="dropdown relative">
          <button
            class="flex items-center gap-1 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"
              />
              <path
                d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"
              />
              <path
                d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"
              />
            </svg>
            Export Report
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
          </button>

          <div
            class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg overflow-hidden z-10 border border-gray-200"
          >
            <button
              @click="downloadReport('pdf')"
              class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center gap-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-red-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                    clip-rule="evenodd"
                  />
                </svg>
                Export as PDF
              </div>
            </button>
            <button
              @click="downloadReport('excel')"
              class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center gap-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-green-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v1H3V4zm0 3h18v8a1 1 0 01-1 1H4a1 1 0 01-1-1V7z"
                    clip-rule="evenodd"
                  />
                </svg>
                Export as Excel
              </div>
            </button>
            <button
              @click="downloadReport('csv')"
              class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
            >
              <div class="flex items-center gap-2">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-blue-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
                  />
                </svg>
                Export as CSV
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div
      v-if="showFilters"
      class="bg-white rounded-lg shadow-md border border-gray-200 p-4 mb-6 transition-all duration-300 ease-in-out"
    >
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800">Filter Reports</h3>
        <button
          @click="toggleFilters"
          class="text-gray-500 hover:text-gray-700"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >Consultant</label
          >
          <select
            v-model="filterConsultant"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="">All Consultants</option>
            <option
              v-for="consultant in consultants"
              :key="consultant.id"
              :value="consultant.name"
            >
              {{ consultant.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >Service Type</label
          >
          <select
            v-model="filterServiceType"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="">All Service Types</option>
            <option
              v-for="(type, index) in serviceTypes"
              :key="index"
              :value="type"
            >
              {{ type }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >Status</label
          >
          <select
            v-model="filterStatus"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="">All Statuses</option>
            <option
              v-for="(status, index) in statusOptions"
              :key="index"
              :value="status"
            >
              {{ status.charAt(0).toUpperCase() + status.slice(1) }}
            </option>
          </select>
        </div>
      </div>

      <div class="flex justify-end mt-4">
        <button
          @click="resetFilters"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors mr-2"
        >
          Reset
        </button>
        <button
          @click="toggleFilters"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Apply Filters
        </button>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading" class="flex items-center justify-center py-10">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      ></div>
    </div>

    <div v-else>
      <!-- Report Type Tabs -->
      <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
        <div class="flex overflow-x-auto">
          <button
            @click="
              selectedReportType = 'projects';
              selectedProjectId = null;
            "
            class="px-6 py-3 text-sm font-medium border-b-2 focus:outline-none whitespace-nowrap"
            :class="
              selectedReportType === 'projects'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Project Overview
          </button>
          <button
            @click="selectedReportType = 'time'"
            class="px-6 py-3 text-sm font-medium border-b-2 focus:outline-none whitespace-nowrap"
            :class="
              selectedReportType === 'time'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Time & Activity
          </button>
          <button
            @click="selectedReportType = 'finance'"
            class="px-6 py-3 text-sm font-medium border-b-2 focus:outline-none whitespace-nowrap"
            :class="
              selectedReportType === 'finance'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Budget & Financials
          </button>
          <button
            @click="selectedReportType = 'performance'"
            class="px-6 py-3 text-sm font-medium border-b-2 focus:outline-none whitespace-nowrap"
            :class="
              selectedReportType === 'performance'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
          >
            Performance Metrics
          </button>
        </div>
      </div>

      <!-- Project Detail View -->
      <div v-if="selectedReportType === 'projectDetail' && selectedProject">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
          <div
            class="p-4 border-b border-gray-200 flex justify-between items-center"
          >
            <div class="flex items-center">
              <button
                @click="backToProjects"
                class="mr-2 p-1 rounded-full hover:bg-gray-100 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500"
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
              <h2 class="text-xl font-bold text-gray-800">
                {{ selectedProject.name }}
              </h2>
              <span
                class="ml-3 px-2 py-1 text-xs font-semibold rounded-full capitalize"
                :class="getStatusStyles(selectedProject.status)"
              >
                {{ selectedProject.status }}
              </span>
            </div>
            <div>
              <button
                @click="downloadReport('pdf')"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                    clip-rule="evenodd"
                  />
                </svg>
                Export Project Report
              </button>
            </div>
          </div>

          <div class="p-6">
            <!-- Project Details Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-1">
                  Project Timeline
                </h3>
                <p class="text-gray-800 mb-1">
                  <span class="font-semibold">Start:</span>
                  {{ formatDate(selectedProject.startDate, "long") }}
                </p>
                <p class="text-gray-800">
                  <span class="font-semibold">End:</span>
                  {{ formatDate(selectedProject.endDate, "long") }}
                </p>

                <!-- Timeline Progress -->
                <div class="mt-4">
                  <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                      <div>
                        <span
                          class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-200"
                        >
                          Timeline Progress
                        </span>
                      </div>
                      <div class="text-right">
                        <span
                          class="text-xs font-semibold inline-block text-indigo-600"
                        >
                          {{ selectedProject.completionPercentage }}%
                        </span>
                      </div>
                    </div>
                    <div
                      class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-200"
                    >
                      <div
                        :style="{
                          width: `${selectedProject.completionPercentage}%`,
                        }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Budget</h3>
                <p class="text-gray-800 mb-1">
                  <span class="font-semibold">Total Budget:</span>
                  {{ formatCurrency(selectedProject.budget) }}
                </p>
                <p class="text-gray-800">
                  <span class="font-semibold">Spent to Date:</span>
                  {{ formatCurrency(selectedProject.spent) }}
                </p>

                <!-- Budget Progress -->
                <div class="mt-4">
                  <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                      <div>
                        <span
                          class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200"
                        >
                          Budget Utilized
                        </span>
                      </div>
                      <div class="text-right">
                        <span
                          class="text-xs font-semibold inline-block text-green-600"
                        >
                          {{
                            Math.round(
                              (selectedProject.spent / selectedProject.budget) *
                                100
                            )
                          }}%
                        </span>
                      </div>
                    </div>
                    <div
                      class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200"
                    >
                      <div
                        :style="{
                          width: `${Math.round(
                            (selectedProject.spent / selectedProject.budget) *
                              100
                          )}%`,
                        }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Hours</h3>
                <p class="text-gray-800 mb-1">
                  <span class="font-semibold">Total Hours:</span>
                  {{ selectedProject.totalHours }}
                </p>
                <p class="text-gray-800">
                  <span class="font-semibold">Hours Logged:</span>
                  {{ selectedProject.hoursLogged }}
                </p>

                <!-- Hours Progress -->
                <div class="mt-4">
                  <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                      <div>
                        <span
                          class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200"
                        >
                          Hours Used
                        </span>
                      </div>
                      <div class="text-right">
                        <span
                          class="text-xs font-semibold inline-block text-blue-600"
                        >
                          {{
                            Math.round(
                              (selectedProject.hoursLogged /
                                selectedProject.totalHours) *
                                100
                            )
                          }}%
                        </span>
                      </div>
                    </div>
                    <div
                      class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200"
                    >
                      <div
                        :style="{
                          width: `${Math.round(
                            (selectedProject.hoursLogged /
                              selectedProject.totalHours) *
                              100
                          )}%`,
                        }"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Project Activity -->
            <div>
              <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Recent Project Activity
              </h3>
              <div class="space-y-4">
                <div
                  v-for="activity in recentActivities.filter(
                    (a) => a.project === selectedProject.name
                  )"
                  :key="activity.id"
                  class="bg-gray-50 p-4 rounded-lg"
                >
                  <div class="flex items-start">
                    <div
                      class="p-2 rounded-full mr-3 flex-shrink-0"
                      :class="getActivityBgColor(activity.type)"
                    >
                      <span
                        class="inline-block"
                        v-html="getActivityIcon(activity.type)"
                      ></span>
                    </div>

                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900">
                        {{ activity.description }}
                      </p>
                      <div class="flex items-center mt-1">
                        <span class="text-xs text-gray-500">{{
                          activity.user
                        }}</span>
                        <span class="mx-2 text-gray-300">•</span>
                        <span class="text-xs text-gray-500">{{
                          formatRelativeTime(activity.date)
                        }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Project Overview Tab -->
      <div v-else-if="selectedReportType === 'projects'">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
          <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
              Projects Summary
            </h2>
          </div>

          <div class="p-6">
            <!-- Project cards -->
            <div class="space-y-4">
              <div
                v-for="project in filteredProjects"
                :key="project.id"
                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                @click="selectProject(project.id)"
              >
                <div
                  class="flex flex-col md:flex-row md:items-center md:justify-between"
                >
                  <div class="mb-4 md:mb-0">
                    <div class="flex items-center">
                      <h3 class="text-lg font-semibold text-gray-900">
                        {{ project.name }}
                      </h3>
                      <span
                        class="ml-3 px-2 py-0.5 text-xs font-medium rounded-full capitalize"
                        :class="getStatusStyles(project.status)"
                      >
                        {{ project.status }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                      <span class="font-medium">Consultant:</span>
                      {{ project.consultantName }}
                    </p>
                    <p class="text-sm text-gray-600">
                      <span class="font-medium">Timeline:</span>
                      {{ formatDate(project.startDate) }} -
                      {{ formatDate(project.endDate) }}
                    </p>
                  </div>

                  <div class="flex flex-col md:items-end">
                    <div class="flex items-center mb-2">
                      <span class="text-sm font-medium text-gray-700 mr-2"
                        >Completion:</span
                      >
                      <div class="w-32 bg-gray-200 rounded-full h-2.5">
                        <div
                          class="h-2.5 rounded-full"
                          :class="
                            getCompletionColor(project.completionPercentage)
                          "
                          :style="{ width: `${project.completionPercentage}%` }"
                        ></div>
                      </div>
                      <span class="ml-2 text-sm font-medium text-gray-700"
                        >{{ project.completionPercentage }}%</span
                      >
                    </div>

                    <div
                      class="flex items-center justify-between w-full md:w-auto"
                    >
                      <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                          <span class="font-medium">Budget:</span>
                          {{ formatCurrency(project.budget) }}
                        </div>
                        <div class="text-sm text-gray-600">
                          <span class="font-medium">Hours:</span>
                          {{ project.hoursLogged }}/{{ project.totalHours }}
                        </div>
                      </div>

                      <button
                        class="flex items-center text-indigo-600 hover:text-indigo-800 transition-colors"
                        @click.stop="selectProject(project.id)"
                      >
                        <span class="text-sm font-medium">Details</span>
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 ml-1"
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
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty state if no projects match filters -->
            <div v-if="filteredProjects.length === 0" class="text-center py-10">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-16 w-16 text-gray-300 mx-auto mb-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"
                  clip-rule="evenodd"
                />
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-1">
                No projects found
              </h3>
              <p class="text-gray-600 mb-4">
                Try adjusting your filters to see more results
              </p>
              <button
                @click="resetFilters"
                class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
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

        <!-- Project chart - completion percentage -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
          <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
              Project Completion Status
            </h2>
          </div>

          <div class="p-6">
            <!-- This would render a chart in a real implementation -->
            <div
              class="h-80 bg-gray-50 rounded-lg flex items-center justify-center"
            >
              <div class="text-center p-6">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-16 w-16 text-gray-300 mx-auto mb-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                  />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">
                  Project Completion Chart
                </h3>
                <p class="text-gray-600 mb-4">
                  Bar chart showing completion percentage for each project
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Time & Activity Tab -->
      <div v-else-if="selectedReportType === 'time'">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <div
            class="bg-white rounded-lg shadow-md border border-gray-200 lg:col-span-2"
          >
            <div class="p-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                  Time Tracking
                </h2>
                <div class="flex space-x-2">
                  <button
                    @click="updateChartData('7days')"
                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors"
                    :class="
                      selectedDateRange === '7days'
                        ? 'bg-indigo-100 text-indigo-700'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    "
                  >
                    7 Days
                  </button>
                  <button
                    @click="updateChartData('30days')"
                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors"
                    :class="
                      selectedDateRange === '30days'
                        ? 'bg-indigo-100 text-indigo-700'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    "
                  >
                    30 Days
                  </button>
                  <button
                    @click="updateChartData('90days')"
                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors"
                    :class="
                      selectedDateRange === '90days'
                        ? 'bg-indigo-100 text-indigo-700'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    "
                  >
                    90 Days
                  </button>
                </div>
              </div>
            </div>

            <div class="p-6">
              <!-- Chart placeholder - would use a real chart library in production -->
              <div
                class="h-80 bg-gray-50 rounded-lg flex items-center justify-center"
              >
                <div class="text-center p-6">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 text-gray-300 mx-auto mb-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                    />
                  </svg>
                  <h3 class="text-lg font-medium text-gray-900 mb-1">
                    Budget Comparison Chart
                  </h3>
                  <p class="text-gray-600">
                    Visualizing budget vs. actual spend across projects
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-800">
                Financial Summary
              </h2>
            </div>

            <div class="p-6">
              <div class="space-y-6">
                <div class="flex flex-col">
                  <div class="text-3xl font-bold text-gray-800 mb-1">
                    {{
                      formatCurrency(
                        projects.reduce((sum, p) => sum + p.budget, 0)
                      )
                    }}
                  </div>
                  <div class="text-sm text-gray-500">
                    Total Budget Across All Projects
                  </div>
                </div>

                <div class="flex flex-col">
                  <div class="text-3xl font-bold text-green-600 mb-1">
                    {{
                      formatCurrency(
                        projects.reduce((sum, p) => sum + p.spent, 0)
                      )
                    }}
                  </div>
                  <div class="text-sm text-gray-500">Total Spent to Date</div>
                </div>

                <div class="flex flex-col">
                  <div class="text-3xl font-bold text-indigo-600 mb-1">
                    {{
                      formatCurrency(
                        projects.reduce((sum, p) => sum + p.budget - p.spent, 0)
                      )
                    }}
                  </div>
                  <div class="text-sm text-gray-500">Remaining Budget</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Project budget details -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
          <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
              Project Budget Details
            </h2>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Project
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Total Budget
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Spent to Date
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Remaining
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    % Used
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Status
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="project in projects"
                  :key="project.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ project.name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ formatCurrency(project.budget) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ formatCurrency(project.spent) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ formatCurrency(project.budget - project.spent) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span class="text-sm text-gray-900 mr-2"
                        >{{
                          Math.round((project.spent / project.budget) * 100)
                        }}%</span
                      >
                      <div class="w-16 bg-gray-200 rounded-full h-2">
                        <div
                          class="h-2 rounded-full"
                          :class="
                            project.spent / project.budget > 1
                              ? 'bg-red-500'
                              : 'bg-green-500'
                          "
                          :style="{
                            width: `${Math.min(
                              Math.round(
                                (project.spent / project.budget) * 100
                              ),
                              100
                            )}%`,
                          }"
                        ></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="getStatusStyles(project.status)"
                    >
                      {{ project.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent payment activities -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
          <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Payments</h2>
          </div>

          <div class="divide-y divide-gray-200">
            <div
              v-for="activity in recentActivities.filter(
                (a) => a.type === 'payment'
              )"
              :key="activity.id"
              class="p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start">
                <div
                  class="p-2 rounded-full mr-3 flex-shrink-0 bg-purple-100 text-purple-800"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                    <path
                      fill-rule="evenodd"
                      d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">
                      {{ activity.description }}
                    </p>
                    <span class="text-sm font-semibold text-green-600">{{
                      formatCurrency(activity.amount)
                    }}</span>
                  </div>
                  <div class="flex items-center mt-1">
                    <span class="text-xs font-medium text-gray-700">{{
                      activity.project
                    }}</span>
                    <span class="mx-2 text-gray-300">•</span>
                    <span class="text-xs text-gray-500">{{
                      formatDate(activity.date)
                    }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty state for no payment activities -->
            <div
              v-if="
                recentActivities.filter((a) => a.type === 'payment').length ===
                0
              "
              class="p-6 text-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-12 w-12 text-gray-300 mx-auto mb-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path
                  fill-rule="evenodd"
                  d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                  clip-rule="evenodd"
                />
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-1">
                No payment activity yet
              </h3>
              <p class="text-gray-600">
                Recent payment transactions will appear here
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Metrics Tab -->
      <div v-else-if="selectedReportType === 'performance'">
        <!-- Performance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">
                Project Completion
              </h3>
              <div class="p-2 rounded-full bg-green-100 text-green-600">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <div class="text-3xl font-bold text-gray-900 mb-2">
              {{
                Math.round(
                  projects.reduce((sum, p) => sum + p.completionPercentage, 0) /
                    projects.length
                )
              }}%
            </div>

            <div class="text-sm text-gray-600 mb-4">
              Average completion rate across all projects
            </div>

            <div class="relative pt-1">
              <div
                class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200"
              >
                <div
                  :style="{
                    width: `${Math.round(
                      projects.reduce(
                        (sum, p) => sum + p.completionPercentage,
                        0
                      ) / projects.length
                    )}%`,
                  }"
                  class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"
                ></div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">
                Budget Utilization
              </h3>
              <div class="p-2 rounded-full bg-indigo-100 text-indigo-600">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <div class="text-3xl font-bold text-gray-900 mb-2">
              {{
                Math.round(
                  (projects.reduce((sum, p) => sum + p.spent, 0) /
                    projects.reduce((sum, p) => sum + p.budget, 0)) *
                    100
                )
              }}%
            </div>

            <div class="text-sm text-gray-600 mb-4">
              Average budget utilization across all projects
            </div>

            <div class="relative pt-1">
              <div
                class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-200"
              >
                <div
                  :style="{
                    width: `${Math.round(
                      (projects.reduce((sum, p) => sum + p.spent, 0) /
                        projects.reduce((sum, p) => sum + p.budget, 0)) *
                        100
                    )}%`,
                  }"
                  class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500"
                ></div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">
                Time Efficiency
              </h3>
              <div class="p-2 rounded-full bg-amber-100 text-amber-600">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
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

            <div class="text-3xl font-bold text-gray-900 mb-2">
              {{
                Math.round(
                  (projects.reduce((sum, p) => sum + p.hoursLogged, 0) /
                    projects.reduce((sum, p) => sum + p.totalHours, 0)) *
                    100
                )
              }}%
            </div>

            <div class="text-sm text-gray-600 mb-4">
              Time logged vs estimated across all projects
            </div>

            <div class="relative pt-1">
              <div
                class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-amber-200"
              >
                <div
                  :style="{
                    width: `${Math.round(
                      (projects.reduce((sum, p) => sum + p.hoursLogged, 0) /
                        projects.reduce((sum, p) => sum + p.totalHours, 0)) *
                        100
                    )}%`,
                  }"
                  class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-amber-500"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Consultant Performance -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
          <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
              Consultant Performance
            </h2>
          </div>

          <div class="p-6">
            <div class="space-y-6">
              <div
                v-for="consultant in consultants"
                :key="consultant.id"
                class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0"
              >
                <div class="flex items-center mb-4">
                  <img
                    :src="consultant.avatar"
                    :alt="consultant.name"
                    class="h-10 w-10 rounded-full object-cover mr-3"
                  />
                  <div>
                    <h3 class="font-medium text-gray-900">
                      {{ consultant.name }}
                    </h3>
                    <p class="text-sm text-gray-600">{{ consultant.role }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <div class="text-sm text-gray-600 mb-1">
                      Completion Rate
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                      <div
                        class="bg-green-500 h-2.5 rounded-full"
                        style="width: 92%"
                      ></div>
                    </div>
                    <div class="text-sm text-right">92%</div>
                  </div>

                  <div>
                    <div class="text-sm text-gray-600 mb-1">
                      On-time Delivery
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                      <div
                        class="bg-indigo-500 h-2.5 rounded-full"
                        style="width: 87%"
                      ></div>
                    </div>
                    <div class="text-sm text-right">87%</div>
                  </div>

                  <div>
                    <div class="text-sm text-gray-600 mb-1">
                      Client Satisfaction
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                      <div
                        class="bg-amber-500 h-2.5 rounded-full"
                        style="width: 95%"
                      ></div>
                    </div>
                    <div class="text-sm text-right">95%</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Client Satisfaction Metrics -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
          <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
              Satisfaction Metrics
            </h2>
          </div>

          <div class="p-6">
            <!-- Chart placeholder - would use a real chart library in production -->
            <div
              class="h-80 bg-gray-50 rounded-lg flex items-center justify-center mb-6"
            >
              <div class="text-center p-6">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-16 w-16 text-gray-300 mx-auto mb-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                  />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">
                  Satisfaction Ratings Chart
                </h3>
                <p class="text-gray-600">
                  Visualizing satisfaction metrics across projects
                </p>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-800 mb-4">
                Recent Feedback
              </h3>

              <div class="space-y-4">
                <div
                  v-for="activity in recentActivities.filter(
                    (a) => a.type === 'feedback'
                  )"
                  :key="activity.id"
                  class="border border-gray-200 rounded-lg p-4"
                >
                  <div class="flex items-start">
                    <div class="flex-1">
                      <div class="flex items-center">
                        <div class="flex text-yellow-400 mr-2">
                          <svg
                            v-for="i in 5"
                            :key="i"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                          </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700"
                          >{{ activity.rating }} out of 5</span
                        >
                      </div>

                      <p class="text-gray-800 mt-2">
                        {{ activity.description }}
                      </p>

                      <div class="mt-2 text-sm text-gray-500">
                        <span>{{ activity.project }}</span>
                        <span class="mx-2 text-gray-300">•</span>
                        <span>{{ formatDate(activity.date) }}</span>
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
  </div>
</template>

<style scoped>
/* Dropdown show on hover */
.dropdown:hover .dropdown-menu {
  display: block;
}

/* Animation for chart loading */
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

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

/* Animation for loading spinner */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
