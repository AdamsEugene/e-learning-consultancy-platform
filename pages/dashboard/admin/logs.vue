<!-- pages/dashboard/admin/logs/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";

// Define page meta to use the dashboard-admin layout
definePageMeta({
  layout: "dashboard-admin",
});

// Page head metadata
useHead({
  title: "Platform Logs - Admin Dashboard",
  meta: [
    {
      name: "description",
      content: "View and manage platform logs and system events",
    },
  ],
});

// Types for logs
interface LogEntry {
  id: string;
  timestamp: string;
  level: "info" | "warning" | "error" | "debug";
  category:
    | "system"
    | "auth"
    | "course"
    | "payment"
    | "project"
    | "user"
    | "api";
  message: string;
  details?: string;
  user?: {
    id: string;
    name: string;
    role: string;
  };
  ip?: string;
  userAgent?: string;
}

// State
const logs = ref<LogEntry[]>([]);
const filteredLogs = ref<LogEntry[]>([]);
const isLoading = ref(true);
const showDetails = ref<string | null>(null);
const selectedLog = ref<LogEntry | null>(null);
const showModal = ref(false);
const searchQuery = ref("");
const selectedLevel = ref<string[]>(["info", "warning", "error", "debug"]);
const selectedCategory = ref<string[]>([
  "system",
  "auth",
  "course",
  "payment",
  "project",
  "user",
  "api",
]);
const currentPage = ref(1);
const itemsPerPage = ref(20);
const sortDirection = ref<"asc" | "desc">("desc");
const sortField = ref("timestamp");
const dateRange = ref({
  start: null as Date | null,
  end: null as Date | null,
});

// Mock data generator
const generateMockLogs = () => {
  const mockLogs: LogEntry[] = [];
  const levels = ["info", "warning", "error", "debug"];
  const categories = [
    "system",
    "auth",
    "course",
    "payment",
    "project",
    "user",
    "api",
  ];
  const userRoles = ["student", "instructor", "consultant", "client", "admin"];

  const messages = {
    system: [
      "System startup completed",
      "Database backup initiated",
      "Server resource usage high",
      "Scheduled maintenance started",
      "Cache cleared",
    ],
    auth: [
      "User login successful",
      "Failed login attempt",
      "Password reset requested",
      "Account locked after multiple failed attempts",
      "User session expired",
    ],
    course: [
      "New course created",
      "Course updated",
      "Course published",
      "Course enrollment completed",
      "Course review submitted",
    ],
    payment: [
      "Payment processed successfully",
      "Payment failed",
      "Refund issued",
      "Subscription renewed",
      "Invoice generated",
    ],
    project: [
      "Project proposal submitted",
      "Project status updated",
      "Project milestone completed",
      "Project document uploaded",
      "Project comment added",
    ],
    user: [
      "New user registered",
      "User profile updated",
      "User role changed",
      "User account deactivated",
      "User verification email sent",
    ],
    api: [
      "API rate limit exceeded",
      "Invalid API request",
      "API key created",
      "API request completed",
      "API authentication failed",
    ],
  };

  // Generate 100 mock log entries
  for (let i = 0; i < 100; i++) {
    // Random date within the last 30 days
    const date = new Date();
    date.setDate(date.getDate() - Math.floor(Math.random() * 30));
    date.setHours(Math.floor(Math.random() * 24));
    date.setMinutes(Math.floor(Math.random() * 60));
    date.setSeconds(Math.floor(Math.random() * 60));

    const level = levels[Math.floor(Math.random() * levels.length)] as
      | "info"
      | "warning"
      | "error"
      | "debug";
    const category = categories[
      Math.floor(Math.random() * categories.length)
    ] as "system" | "auth" | "course" | "payment" | "project" | "user" | "api";

    // Get random message from the category
    const message =
      messages[category][Math.floor(Math.random() * messages[category].length)];

    // User information for certain categories
    let user = undefined;
    if (["auth", "course", "payment", "project", "user"].includes(category)) {
      const userId = Math.floor(1000 + Math.random() * 9000).toString();
      const userName = [
        "John Smith",
        "Sarah Johnson",
        "Michael Chen",
        "Emily Garcia",
        "David Kim",
      ][Math.floor(Math.random() * 5)];
      const userRole = userRoles[Math.floor(Math.random() * userRoles.length)];

      user = {
        id: userId,
        name: userName,
        role: userRole,
      };
    }

    mockLogs.push({
      id: `log-${i + 1}`,
      timestamp: date.toISOString(),
      level,
      category,
      message,
      details:
        level === "error"
          ? `Error details for ${message.toLowerCase()}. Stack trace information and error context would be shown here in a real application.`
          : undefined,
      user,
      ip: `192.168.${Math.floor(Math.random() * 255)}.${Math.floor(
        Math.random() * 255
      )}`,
      userAgent: [
        "Chrome/98.0.4758.102",
        "Firefox/97.0",
        "Safari/15.4",
        "Edge/98.0.1108.62",
      ][Math.floor(Math.random() * 4)],
    });
  }

  return mockLogs.sort(
    (a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
  );
};

// Load logs data
const fetchLogs = async () => {
  try {
    isLoading.value = true;
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 800)); // Simulate API delay
    logs.value = generateMockLogs();
    applyFilters();
  } catch (error) {
    console.error("Error fetching logs:", error);
  } finally {
    isLoading.value = false;
  }
};

// Apply filters and sorting to logs
const applyFilters = () => {
  let result = [...logs.value];

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (log) =>
        log.message.toLowerCase().includes(query) ||
        log.category.toLowerCase().includes(query) ||
        log.user?.name.toLowerCase().includes(query) ||
        false ||
        log.details?.toLowerCase().includes(query) ||
        false
    );
  }

  // Filter by log level
  if (selectedLevel.value.length > 0) {
    result = result.filter((log) => selectedLevel.value.includes(log.level));
  }

  // Filter by category
  if (selectedCategory.value.length > 0) {
    result = result.filter((log) =>
      selectedCategory.value.includes(log.category)
    );
  }

  // Filter by date range
  if (dateRange.value.start) {
    const startDate = new Date(dateRange.value.start);
    startDate.setHours(0, 0, 0, 0);
    result = result.filter((log) => new Date(log.timestamp) >= startDate);
  }

  if (dateRange.value.end) {
    const endDate = new Date(dateRange.value.end);
    endDate.setHours(23, 59, 59, 999);
    result = result.filter((log) => new Date(log.timestamp) <= endDate);
  }

  // Apply sorting
  result.sort((a, b) => {
    let comparison = 0;

    if (sortField.value === "timestamp") {
      comparison =
        new Date(a.timestamp).getTime() - new Date(b.timestamp).getTime();
    } else if (sortField.value === "level") {
      comparison = a.level.localeCompare(b.level);
    } else if (sortField.value === "category") {
      comparison = a.category.localeCompare(b.category);
    }

    return sortDirection.value === "asc" ? comparison : -comparison;
  });

  filteredLogs.value = result;
};

// Pagination logic
const totalPages = computed(() =>
  Math.ceil(filteredLogs.value.length / itemsPerPage.value)
);
const paginatedLogs = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return filteredLogs.value.slice(startIndex, endIndex);
});

// Format timestamp
const formatTimestamp = (timestamp: string) => {
  const date = new Date(timestamp);
  return date.toLocaleString();
};

// Open log details modal
const viewLogDetails = (log: LogEntry) => {
  selectedLog.value = log;
  showModal.value = true;
};

// Reset filters
const resetFilters = () => {
  searchQuery.value = "";
  selectedLevel.value = ["info", "warning", "error", "debug"];
  selectedCategory.value = [
    "system",
    "auth",
    "course",
    "payment",
    "project",
    "user",
    "api",
  ];
  dateRange.value = {
    start: null,
    end: null,
  };
  currentPage.value = 1;
};

// Toggle level filter
const toggleLevel = (level: string) => {
  if (selectedLevel.value.includes(level)) {
    selectedLevel.value = selectedLevel.value.filter((l) => l !== level);
  } else {
    selectedLevel.value.push(level);
  }
};

// Toggle category filter
const toggleCategory = (category: string) => {
  if (selectedCategory.value.includes(category)) {
    selectedCategory.value = selectedCategory.value.filter(
      (c) => c !== category
    );
  } else {
    selectedCategory.value.push(category);
  }
};

// Get color for log level
const getLevelColor = (level: string) => {
  switch (level) {
    case "info":
      return "bg-blue-100 text-blue-800";
    case "warning":
      return "bg-yellow-100 text-yellow-800";
    case "error":
      return "bg-red-100 text-red-800";
    case "debug":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get color for category
const getCategoryColor = (category: string) => {
  switch (category) {
    case "system":
      return "bg-purple-100 text-purple-800";
    case "auth":
      return "bg-blue-100 text-blue-800";
    case "course":
      return "bg-indigo-100 text-indigo-800";
    case "payment":
      return "bg-green-100 text-green-800";
    case "project":
      return "bg-amber-100 text-amber-800";
    case "user":
      return "bg-pink-100 text-pink-800";
    case "api":
      return "bg-cyan-100 text-cyan-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Export logs to CSV
const exportLogs = () => {
  // In a real app, this would handle exporting logs to CSV or another format
  alert("Exporting logs functionality would be implemented here");
};

// Clear logs (demonstration only)
const clearLogs = () => {
  // In a real app, this might open a confirmation modal before clearing logs
  alert("Clearing logs functionality would be implemented here");
};

// Watch for filter changes
watch(
  [
    searchQuery,
    selectedLevel,
    selectedCategory,
    dateRange,
    sortDirection,
    sortField,
  ],
  () => {
    applyFilters();
    currentPage.value = 1; // Reset to first page when filters change
  }
);

// Fetch data when component mounts
onMounted(() => {
  fetchLogs();
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Platform Logs</h1>
          <p class="text-gray-600">
            View and analyze system events and user actions
          </p>
        </div>

        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
          <button
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
            @click="fetchLogs"
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
            Refresh
          </button>

          <button
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center"
            @click="exportLogs"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            Export
          </button>

          <button
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
            @click="clearLogs"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            Clear
          </button>
        </div>
      </div>

      <!-- Filter controls -->
      <div class="space-y-4">
        <div
          class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4"
        >
          <!-- Search -->
          <div class="relative flex-grow">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search logs..."
              class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

          <!-- Date range pickers -->
          <div class="flex items-center space-x-2">
            <div class="relative">
              <input
                v-model="dateRange.start"
                type="date"
                class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <span class="text-gray-500">to</span>

            <div class="relative">
              <input
                v-model="dateRange.end"
                type="date"
                class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Reset filters button -->
          <button
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            @click="resetFilters"
          >
            Reset Filters
          </button>
        </div>

        <!-- Level & category filters -->
        <div class="flex flex-wrap gap-2">
          <div class="mr-2 text-sm font-medium text-gray-700">Level:</div>
          <button
            v-for="level in ['info', 'warning', 'error', 'debug']"
            :key="`level-${level}`"
            class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
            :class="
              selectedLevel.includes(level)
                ? getLevelColor(level)
                : 'bg-gray-200 text-gray-700'
            "
            @click="toggleLevel(level)"
          >
            {{ level.charAt(0).toUpperCase() + level.slice(1) }}
          </button>

          <div class="ml-4 mr-2 text-sm font-medium text-gray-700">
            Category:
          </div>
          <button
            v-for="category in [
              'system',
              'auth',
              'course',
              'payment',
              'project',
              'user',
              'api',
            ]"
            :key="`category-${category}`"
            class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
            :class="
              selectedCategory.includes(category)
                ? getCategoryColor(category)
                : 'bg-gray-200 text-gray-700'
            "
            @click="toggleCategory(category)"
          >
            {{ category.charAt(0).toUpperCase() + category.slice(1) }}
          </button>
        </div>
      </div>
    </div>

    <!-- Logs table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Loading state -->
      <div v-if="isLoading" class="p-8 text-center">
        <div
          class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-4"
        />
        <p class="text-gray-600">Loading logs...</p>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredLogs.length === 0" class="p-8 text-center">
        <div
          class="mx-auto w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8"
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
        <p class="text-gray-600 mb-2">No logs found</p>
        <p class="text-gray-500 text-sm">Try adjusting your filter criteria</p>
      </div>

      <!-- Logs table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                @click="
                  sortField = 'timestamp';
                  sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                "
              >
                <div class="flex items-center">
                  Timestamp
                  <svg
                    v-if="sortField === 'timestamp'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="ml-1 h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      :transform="sortDirection === 'asc' ? 'rotate(180)' : ''"
                    />
                  </svg>
                </div>
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                @click="
                  sortField = 'level';
                  sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                "
              >
                <div class="flex items-center">
                  Level
                  <svg
                    v-if="sortField === 'level'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="ml-1 h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      :transform="sortDirection === 'asc' ? 'rotate(180)' : ''"
                    />
                  </svg>
                </div>
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                @click="
                  sortField = 'category';
                  sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                "
              >
                <div class="flex items-center">
                  Category
                  <svg
                    v-if="sortField === 'category'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="ml-1 h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      :transform="sortDirection === 'asc' ? 'rotate(180)' : ''"
                    />
                  </svg>
                </div>
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Message
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                User
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="log in paginatedLogs"
              :key="log.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatTimestamp(log.timestamp) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getLevelColor(log.level)"
                >
                  {{ log.level.toUpperCase() }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getCategoryColor(log.category)"
                >
                  {{ log.category }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900 max-w-md truncate">
                {{ log.message }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div v-if="log.user" class="flex items-center">
                  <span class="text-gray-900 font-medium">{{
                    log.user.name
                  }}</span>
                  <span
                    class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"
                  >
                    {{ log.user.role }}
                  </span>
                </div>
                <span v-else>-</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  class="text-indigo-600 hover:text-indigo-900 transition-colors"
                  @click="viewLogDetails(log)"
                >
                  View Details
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="filteredLogs.length > 0"
        class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
      >
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''"
            @click="currentPage > 1 ? currentPage-- : null"
          >
            Previous
          </button>
          <button
            :disabled="currentPage === totalPages"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            :class="
              currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''
            "
            @click="currentPage < totalPages ? currentPage++ : null"
          >
            Next
          </button>
        </div>
        <div
          class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
        >
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{
                (currentPage - 1) * itemsPerPage + 1
              }}</span>
              to
              <span class="font-medium">{{
                Math.min(currentPage * itemsPerPage, filteredLogs.length)
              }}</span>
              of
              <span class="font-medium">{{ filteredLogs.length }}</span> results
            </p>
          </div>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <button
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                :class="
                  currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''
                "
                @click="currentPage = 1"
              >
                <span class="sr-only">First</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <button
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                :class="
                  currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''
                "
                @click="currentPage > 1 ? currentPage-- : null"
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
              </button>

              <!-- Page numbers -->
              <template v-if="totalPages <= 7">
                <button
                  v-for="page in totalPages"
                  :key="page"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                  :class="
                    currentPage === page
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white text-gray-500 hover:bg-gray-50'
                  "
                  @click="currentPage = page"
                >
                  {{ page }}
                </button>
              </template>
              <template v-else>
                <!-- First page -->
                <button
                  v-if="currentPage > 3"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  @click="currentPage = 1"
                >
                  1
                </button>

                <!-- Ellipsis -->
                <span
                  v-if="currentPage > 4"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  ...
                </span>

                <!-- Pages around current page -->
                <template v-for="page in totalPages">
                  <button
                    v-if="
                      page >= currentPage - 1 &&
                      page <= currentPage + 1 &&
                      page > 0 &&
                      page <= totalPages
                    "
                    :key="page"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                    :class="
                      currentPage === page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white text-gray-500 hover:bg-gray-50'
                    "
                    @click="currentPage = page"
                  >
                    {{ page }}
                  </button>
                </template>

                <!-- Ellipsis -->
                <span
                  v-if="currentPage < totalPages - 3"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  ...
                </span>

                <!-- Last page -->
                <button
                  v-if="currentPage < totalPages - 2"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  @click="currentPage = totalPages"
                >
                  {{ totalPages }}
                </button>
              </template>

              <button
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                :class="
                  currentPage === totalPages
                    ? 'opacity-50 cursor-not-allowed'
                    : ''
                "
                @click="currentPage < totalPages ? currentPage++ : null"
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
              </button>
              <button
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                :class="
                  currentPage === totalPages
                    ? 'opacity-50 cursor-not-allowed'
                    : ''
                "
                @click="currentPage = totalPages"
              >
                <span class="sr-only">Last</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 15.707a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 1.414L8.586 10 4.293 14.293a1 1 0 000 1.414zm6 0a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 1.414L14.586 10l-4.293 4.293a1 1 0 000 1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Log details modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-4xl w-full p-6 shadow-xl max-h-[90vh] overflow-y-auto"
      >
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-medium text-gray-900">Log Details</h3>
          <button
            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
            @click="showModal = false"
          >
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
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

        <div v-if="selectedLog" class="space-y-4">
          <!-- Basic log info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <div class="text-sm font-medium text-gray-500">ID</div>
              <div class="text-sm text-gray-900">{{ selectedLog.id }}</div>
            </div>
            <div class="space-y-2">
              <div class="text-sm font-medium text-gray-500">Timestamp</div>
              <div class="text-sm text-gray-900">
                {{ formatTimestamp(selectedLog.timestamp) }}
              </div>
            </div>
            <div class="space-y-2">
              <div class="text-sm font-medium text-gray-500">Level</div>
              <div class="text-sm">
                <span
                  class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getLevelColor(selectedLog.level)"
                >
                  {{ selectedLog.level.toUpperCase() }}
                </span>
              </div>
            </div>
            <div class="space-y-2">
              <div class="text-sm font-medium text-gray-500">Category</div>
              <div class="text-sm">
                <span
                  class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getCategoryColor(selectedLog.category)"
                >
                  {{ selectedLog.category }}
                </span>
              </div>
            </div>
          </div>

          <!-- Message -->
          <div class="space-y-2">
            <div class="text-sm font-medium text-gray-500">Message</div>
            <div class="text-sm text-gray-900 p-3 bg-gray-50 rounded-lg">
              {{ selectedLog.message }}
            </div>
          </div>

          <!-- Details if available -->
          <div v-if="selectedLog.details" class="space-y-2">
            <div class="text-sm font-medium text-gray-500">Details</div>
            <div
              class="text-sm text-gray-900 p-3 bg-gray-50 rounded-lg whitespace-pre-wrap"
            >
              {{ selectedLog.details }}
            </div>
          </div>

          <!-- User information if available -->
          <div v-if="selectedLog.user" class="space-y-2">
            <div class="text-sm font-medium text-gray-500">
              User Information
            </div>
            <div class="text-sm bg-gray-50 p-3 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <span class="font-medium text-gray-500">User ID:</span>
                  {{ selectedLog.user.id }}
                </div>
                <div>
                  <span class="font-medium text-gray-500">Name:</span>
                  {{ selectedLog.user.name }}
                </div>
                <div>
                  <span class="font-medium text-gray-500">Role:</span>
                  <span
                    class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800"
                  >
                    {{ selectedLog.user.role }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional metadata -->
          <div class="space-y-2">
            <div class="text-sm font-medium text-gray-500">
              Additional Information
            </div>
            <div class="text-sm bg-gray-50 p-3 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <span class="font-medium text-gray-500">IP Address:</span>
                  {{ selectedLog.ip || "N/A" }}
                </div>
                <div>
                  <span class="font-medium text-gray-500">User Agent:</span>
                  {{ selectedLog.userAgent || "N/A" }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            v-if="selectedLog && selectedLog.level === 'error'"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            Mark as Resolved
          </button>
          <button
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
            @click="showModal = false"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

/* Truncate long text in table cells */
.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
