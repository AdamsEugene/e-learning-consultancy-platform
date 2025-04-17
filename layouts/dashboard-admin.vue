<!-- layouts/dashboard-admin.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";

// Route and active menu handling
const route = useRoute();
const currentPath = computed(() => route.path);

// Sidebar state
const isSidebarOpen = ref(true);
const isMobileView = ref(false);

// Toggle sidebar
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

// Check if mobile view and adjust sidebar
const checkMobileView = () => {
  isMobileView.value = window.innerWidth < 1024;
  if (isMobileView.value) {
    isSidebarOpen.value = false;
  } else {
    isSidebarOpen.value = true;
  }
};

// Notification badge
const notificationCount = ref(9);

// User data - in a real app, this would be from your user store or API
const user = ref({
  name: "Admin User",
  avatar: "/api/placeholder/100/100",
  role: "admin",
  department: "Platform Management",
  adminLevel: "Super Admin",
  dateJoined: "June 2023",
});

// Platform statistics
const platformStats = ref({
  totalUsers: 3725,
  activeStudents: 1865,
  activeInstructors: 48,
  activeConsultants: 27,
  activeClients: 112,
  activeCourses: 150,
  activeProjects: 35,
  totalRevenue: 358750,
  coursesRevenue: 215600,
  consultancyRevenue: 143150,
});

// Navigation menu for admin
const navItems = [
  { title: "Dashboard", path: "/dashboard/admin", icon: "home" },
  { title: "User Management", path: "/dashboard/admin/users", icon: "users" },
  { title: "Course Management", path: "/dashboard/admin/courses", icon: "book-open" },
  { title: "Project Management", path: "/dashboard/admin/projects", icon: "briefcase" },
  { title: "Financial Management", path: "/dashboard/admin/financials", icon: "cash" },
  { title: "Reports & Analytics", path: "/dashboard/admin/analytics", icon: "chart-bar" },
  {
    title: "Content Management",
    path: "/dashboard/admin/content",
    icon: "document-text",
  },
  { title: "System Settings", path: "/dashboard/admin/settings", icon: "settings-knobs" },
  {
    title: "Notifications",
    path: "/dashboard/admin/notifications",
    icon: "bell",
    badge: notificationCount,
  },
  {
    title: "Support Tickets",
    path: "/dashboard/admin/support",
    icon: "ticket",
    badge: 4,
  },
  { title: "Platform Logs", path: "/dashboard/admin/logs", icon: "clipboard-list" },
  { title: "API Management", path: "/dashboard/admin/api", icon: "code" },
  { title: "Help & Resources", path: "/dashboard/admin/help", icon: "help-circle" },
];

// Admin Tools & Quick Actions
const quickActions = [
  {
    title: "Create New User",
    path: "/dashboard/admin/users/create",
    icon: "user-add",
    color: "bg-indigo-600",
  },
  {
    title: "Approve Course",
    path: "/dashboard/admin/courses/approve",
    icon: "check-circle",
    color: "bg-green-600",
    badge: 3,
  },
  {
    title: "Review Project",
    path: "/dashboard/admin/projects/review",
    icon: "eye",
    color: "bg-amber-600",
    badge: 2,
  },
  {
    title: "Financial Report",
    path: "/dashboard/admin/financials/report",
    icon: "document-report",
    color: "bg-purple-600",
  },
  {
    title: "System Health",
    path: "/dashboard/admin/system/health",
    icon: "shield-check",
    color: "bg-blue-600",
  },
];

// Lifecycle hooks
onMounted(() => {
  checkMobileView();
  window.addEventListener("resize", checkMobileView);
});

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

// Render the right icon based on name
const renderIcon = (iconName: string) => {
  switch (iconName) {
    case "home":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
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
    case "chart-bar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
      </svg>`;
    case "document-text":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "settings-knobs":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>`;
    case "bell":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
      </svg>`;
    case "ticket":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z" />
      </svg>`;
    case "clipboard-list":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
      </svg>`;
    case "code":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>`;
    case "help-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
    case "user-add":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
      </svg>`;
    case "check-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "eye":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
      </svg>`;
    case "document-report":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd" />
      </svg>`;
    case "shield-check":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};
</script>

<template>
  <div class="flex h-screen bg-gray-50 overflow-hidden">
    <!-- Sidebar -->
    <div
      class="sidebar bg-white border-r border-gray-200 transition-all duration-300 ease-in-out flex flex-col z-30"
      :class="isSidebarOpen ? 'w-64' : 'w-20'"
    >
      <!-- Logo section -->
      <div
        class="flex items-center justify-between h-16 px-4 border-b border-gray-200"
      >
        <NuxtLink to="/" class="flex items-center">
          <div
            class="w-8 h-8 rounded-lg flex items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-700"
          >
            <span class="text-xl font-bold text-white">L</span>
          </div>
          <span
            v-if="isSidebarOpen"
            class="ml-2 text-xl font-bold text-indigo-600 transition-opacity duration-300"
          >
            LearnConsult
          </span>
        </NuxtLink>

        <!-- Toggle button - visible on desktop only -->
        <button
          @click="toggleSidebar"
          class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none hidden lg:block"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 transform transition-transform duration-300"
            :class="isSidebarOpen ? 'rotate-0' : 'rotate-180'"
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
      </div>

      <!-- Admin Profile Summary -->
      <div
        v-if="isSidebarOpen"
        class="p-4 border-b border-gray-200 bg-indigo-50 transition-all duration-300 ease-in-out"
      >
        <div class="flex items-center space-x-3">
          <div class="relative">
            <img
              :src="user.avatar"
              :alt="user.name"
              class="h-12 w-12 rounded-full object-cover border-2 border-indigo-100"
            />
            <div
              class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-green-500 border-2 border-white"
            ></div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ user.name }}
            </p>
            <p class="text-xs text-gray-500 truncate">{{ user.department }}</p>
            <p class="text-xs text-gray-600 mt-1">
              <span
                class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs"
              >
                {{ user.adminLevel }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <!-- Platform Overview Stats (only when expanded) -->
      <div
        v-if="isSidebarOpen"
        class="grid grid-cols-2 gap-2 p-4 border-b border-gray-200 text-center"
      >
        <div class="bg-blue-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-blue-600">
            {{ formatNumber(platformStats.totalUsers) }}
          </div>
          <div class="text-xs text-gray-600">Total Users</div>
        </div>
        <div class="bg-green-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-green-600">
            {{ formatNumber(platformStats.activeCourses) }}
          </div>
          <div class="text-xs text-gray-600">Active Courses</div>
        </div>
        <div class="bg-amber-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-amber-600">
            {{ platformStats.activeProjects }}
          </div>
          <div class="text-xs text-gray-600">Active Projects</div>
        </div>
        <div class="bg-purple-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-purple-600">
            {{ formatCurrency(platformStats.totalRevenue) }}
          </div>
          <div class="text-xs text-gray-600">Total Revenue</div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 pt-2 pb-4 overflow-y-auto">
        <ul class="px-2 space-y-1">
          <li v-for="item in navItems" :key="item.title">
            <NuxtLink
              :to="item.path"
              class="flex items-center px-3 py-3 rounded-lg transition-all duration-200 group relative overflow-hidden"
              :class="[
                currentPath === item.path
                  ? 'bg-indigo-50 text-indigo-600'
                  : 'text-gray-700 hover:bg-gray-100',
              ]"
            >
              <!-- Icon -->
              <span
                class="text-current transition-colors duration-200"
                :class="
                  currentPath === item.path
                    ? 'text-indigo-600'
                    : 'text-gray-500 group-hover:text-indigo-600'
                "
                v-html="renderIcon(item.icon)"
              ></span>

              <!-- Title - only show if sidebar is open -->
              <span
                v-if="isSidebarOpen"
                class="ml-3 text-sm font-medium whitespace-nowrap transition-opacity duration-300"
              >
                {{ item.title }}
              </span>

              <!-- Badge (if applicable) -->
              <span
                v-if="item.badge && isSidebarOpen"
                class="ml-auto bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full animate-pulse"
              >
                {{ item.badge }}
              </span>

              <!-- Mini badge (if sidebar collapsed) -->
              <span
                v-if="item.badge && !isSidebarOpen"
                class="absolute top-1 right-1 w-5 h-5 flex items-center justify-center bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full animate-pulse"
              >
                {{ item.badge }}
              </span>

              <!-- Active indicator -->
              <span
                v-if="currentPath === item.path"
                class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r"
              ></span>
            </NuxtLink>
          </li>
        </ul>
      </nav>

      <!-- Quick Actions (only when expanded) -->
      <div v-if="isSidebarOpen" class="border-t border-gray-200 p-4">
        <h4
          class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2"
        >
          Admin Tools
        </h4>
        <div class="space-y-2">
          <NuxtLink
            v-for="action in quickActions"
            :key="action.title"
            :to="action.path"
            class="flex items-center justify-between px-3 py-2 text-sm rounded-lg text-white transition-all duration-200 transform hover:translate-x-1 hover:shadow-md"
            :class="action.color"
          >
            <div class="flex items-center">
              <span v-html="renderIcon(action.icon)" class="mr-2"></span>
              <span>{{ action.title }}</span>
            </div>

            <!-- Badge for admin tools (if applicable) -->
            <span
              v-if="action.badge"
              class="bg-white bg-opacity-20 text-white text-xs font-bold px-2 py-0.5 rounded-full"
            >
              {{ action.badge }}
            </span>
          </NuxtLink>
        </div>
      </div>

      <!-- Collapsed Quick Actions -->
      <div v-if="!isSidebarOpen" class="border-t border-gray-200 py-2">
        <div class="flex flex-col items-center space-y-2">
          <NuxtLink
            v-for="action in quickActions"
            :key="action.title"
            :to="action.path"
            class="relative h-10 w-10 flex items-center justify-center rounded-full text-white transition-all duration-200 transform hover:scale-110 hover:shadow-md"
            :class="action.color"
            :title="action.title"
          >
            <span v-html="renderIcon(action.icon)"></span>

            <!-- Mini badge for admin tools (if applicable) -->
            <span
              v-if="action.badge"
              class="absolute -top-1 -right-1 w-5 h-5 flex items-center justify-center bg-white text-indigo-700 text-xs font-bold rounded-full"
            >
              {{ action.badge }}
            </span>
          </NuxtLink>
        </div>
      </div>

      <!-- User section for collapsed sidebar -->
      <div class="border-t border-gray-200 p-4" v-if="!isSidebarOpen">
        <div class="flex justify-center">
          <!-- Avatar -->
          <div class="relative">
            <img
              :src="user.avatar"
              :alt="user.name"
              class="h-10 w-10 rounded-full object-cover border-2 border-indigo-100"
            />
            <div
              class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full bg-green-500 border-2 border-white"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top navigation -->
      <header
        class="bg-white shadow-sm border-b border-gray-200 h-16 flex items-center"
      >
        <div class="flex items-center justify-between w-full px-4">
          <!-- Mobile menu button -->
          <button
            @click="toggleSidebar"
            class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none lg:hidden"
          >
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
                d="M4 6h16M4 12h16M4 18h7"
              />
            </svg>
          </button>

          <!-- Page title - can be dynamic based on current route -->
          <div class="hidden sm:flex items-center">
            <h1 class="text-xl font-bold text-gray-800">Admin Dashboard</h1>
            <div
              class="ml-2 px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-xs font-medium"
            >
              Admin Access
            </div>
          </div>

          <!-- Right navigation items -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative hidden md:block">
              <input
                type="text"
                placeholder="Search users, courses, projects..."
                class="w-full md:w-72 pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

            <!-- System Health Status Indicator -->
            <div
              class="hidden md:flex items-center px-3 py-1.5 bg-green-100 text-green-800 text-sm font-medium rounded-lg"
            >
              <div
                class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"
              ></div>
              <span>System Normal</span>
            </div>

            <!-- Admin Actions Dropdown Button -->
            <div class="relative">
              <button
                class="hidden md:flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <span class="mr-1 font-medium">Admin Actions</span>
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
              </button>
            </div>

            <!-- Notifications -->
            <button
              class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none relative"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                />
              </svg>
              <span
                v-if="notificationCount > 0"
                class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full transform -translate-y-1 translate-x-1"
              >
                {{ notificationCount }}
              </span>
            </button>

            <!-- Support Tickets -->
            <button
              class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none relative"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z"
                />
              </svg>
              <span
                class="absolute top-0 right-0 bg-amber-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full transform -translate-y-1 translate-x-1"
              >
                4
              </span>
            </button>

            <!-- User menu (mobile only) -->
            <div class="lg:hidden">
              <img
                :src="user.avatar"
                :alt="user.name"
                class="h-8 w-8 rounded-full object-cover border-2 border-indigo-100"
              />
            </div>
          </div>
        </div>
      </header>

      <!-- Main content area with scrolling -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar */
.sidebar::-webkit-scrollbar,
main::-webkit-scrollbar {
  width: 4px;
}

.sidebar::-webkit-scrollbar-track,
main::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.sidebar::-webkit-scrollbar-thumb,
main::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}

.sidebar::-webkit-scrollbar-thumb:hover,
main::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Badge pulse animation */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover and animation effects */
.sidebar .nav-item:hover {
  transform: translateX(3px);
}

/* Admin Tool hover effects */
.quick-action:hover {
  transform: translateX(5px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Admin actions dropdown animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.3s ease-out;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
