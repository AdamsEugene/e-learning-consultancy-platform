<!-- layouts/dashboard-client.vue -->
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
const notificationCount = ref(4);

// User data - in a real app, this would be from your user store or API
const user = ref({
  name: "Michael Roberts",
  avatar: "/api/placeholder/100/100",
  role: "client",
  company: "TechInnovate Solutions",
  position: "Product Director",
  since: "January 2024",
});

// Navigation menu for client
const navItems = [
  { title: "Dashboard", path: "/dashboard/client", icon: "home" },
  { title: "My Projects", path: "/dashboard/client/projects", icon: "briefcase" },
  { title: "Project Requests", path: "/dashboard/client/requests", icon: "document-add" },
  { title: "Consultants", path: "/dashboard/client/consultants", icon: "users" },
  { title: "Meetings", path: "/dashboard/client/meetings", icon: "calendar" },
  { title: "Learning Hub", path: "/dashboard/client/learning", icon: "academic-cap" },
  { title: "Documents", path: "/dashboard/client/documents", icon: "document-text" },
  { title: "Payments", path: "/dashboard/client/payments", icon: "cash" },
  { title: "Reports", path: "/dashboard/client/reports", icon: "chart-bar" },
  {
    title: "Messages",
    path: "/dashboard/client/messages",
    icon: "chat",
    badge: notificationCount,
  },
  { title: "Settings", path: "/dashboard/client/settings", icon: "settings" },
  { title: "Help & Support", path: "/dashboard/client/support", icon: "help-circle" },
];

// Quick Actions for clients
const quickActions = [
  {
    title: "New Project Request",
    path: "/dashboard/client/requests/create",
    icon: "plus-circle",
    color: "bg-indigo-500",
  },
  {
    title: "Schedule Meeting",
    path: "/dashboard/client/meetings/schedule",
    icon: "calendar-add",
    color: "bg-green-500",
  },
  {
    title: "Find Consultants",
    path: "/dashboard/client/consultants/discover",
    icon: "search",
    color: "bg-amber-500",
  },
  {
    title: "View Resources",
    path: "/dashboard/client/learning/resources",
    icon: "library",
    color: "bg-purple-500",
  },
];

// Stats summary
const clientStats = ref({
  activeProjects: 3,
  completedProjects: 7,
  upcomingMeetings: 2,
  resourcesAccessed: 15,
});

// Lifecycle hooks
onMounted(() => {
  checkMobileView();
  window.addEventListener("resize", checkMobileView);
});

// Render the right icon based on name
const renderIcon = (iconName: string) => {
  switch (iconName) {
    case "home":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
      </svg>`;
    case "briefcase":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "document-add":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
      </svg>`;
    case "calendar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
      </svg>`;
    case "academic-cap":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
      </svg>`;
    case "document-text":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "cash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "chart-bar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
      </svg>`;
    case "chat":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
      </svg>`;
    case "settings":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>`;
    case "help-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
    case "plus-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
      </svg>`;
    case "calendar-add":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
        <path fill-rule="evenodd" d="M10 8a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1H8a1 1 0 110-2h1V9a1 1 0 011-1z" clip-rule="evenodd" />
      </svg>`;
    case "search":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
      </svg>`;
    case "library":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get project status color
const getProjectStatusColor = (status: string) => {
  switch (status) {
    case "active":
      return "bg-green-500";
    case "pending":
      return "bg-yellow-500";
    case "completed":
      return "bg-blue-500";
    default:
      return "bg-gray-500";
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

      <!-- Client Profile Summary -->
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
            <p class="text-xs text-gray-500 truncate">{{ user.company }}</p>
            <p class="text-xs text-gray-600 mt-1">
              <span
                class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs"
              >
                Client since {{ user.since }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <!-- Client Stats Summary (only when expanded) -->
      <div
        v-if="isSidebarOpen"
        class="grid grid-cols-2 gap-2 p-4 border-b border-gray-200 text-center"
      >
        <div class="bg-green-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-green-600">
            {{ clientStats.activeProjects }}
          </div>
          <div class="text-xs text-gray-600">Active Projects</div>
        </div>
        <div class="bg-blue-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-blue-600">
            {{ clientStats.completedProjects }}
          </div>
          <div class="text-xs text-gray-600">Completed</div>
        </div>
        <div class="bg-amber-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-amber-600">
            {{ clientStats.upcomingMeetings }}
          </div>
          <div class="text-xs text-gray-600">Meetings</div>
        </div>
        <div class="bg-purple-50 p-2 rounded-lg">
          <div class="text-xl font-bold text-purple-600">
            {{ clientStats.resourcesAccessed }}
          </div>
          <div class="text-xs text-gray-600">Resources</div>
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
          Quick Actions
        </h4>
        <div class="space-y-2">
          <NuxtLink
            v-for="action in quickActions"
            :key="action.title"
            :to="action.path"
            class="flex items-center px-3 py-2 text-sm rounded-lg text-white transition-all duration-200 transform hover:translate-x-1 hover:shadow-md"
            :class="action.color"
          >
            <span v-html="renderIcon(action.icon)" class="mr-2"></span>
            <span>{{ action.title }}</span>
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
            class="h-10 w-10 flex items-center justify-center rounded-full text-white transition-all duration-200 transform hover:scale-110 hover:shadow-md"
            :class="action.color"
            :title="action.title"
          >
            <span v-html="renderIcon(action.icon)"></span>
          </NuxtLink>
        </div>
      </div>

      <!-- User section -->
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
          <div class="text-xl font-bold text-gray-800 hidden sm:block">
            Client Dashboard
          </div>

          <!-- Right navigation items -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative hidden md:block">
              <input
                type="text"
                placeholder="Search projects, consultants..."
                class="w-full md:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

            <!-- New Project Button (visible on larger screens) -->
            <NuxtLink
              to="/dashboard/client/requests/create"
              class="hidden md:flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
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
              <span>New Project</span>
            </NuxtLink>

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

            <!-- Messages -->
            <button
              class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                />
                <path
                  d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                />
              </svg>
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

/* Quick action items hover effect */
.quick-action:hover {
  transform: translateX(5px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
