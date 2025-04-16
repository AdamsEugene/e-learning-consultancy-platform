<!-- layouts/dashboard.vue -->
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
const notificationCount = ref(3);

// User data - in a real app, this would be from your user store or API
const user = ref({
  name: "Emily Johnson",
  avatar: "/images/instructors/i1.jpg",
  role: "student",
});

// Navigation menu
const navItems = [
  { title: "Dashboard", path: "/dashboard/student", icon: "home" },
  {
    title: "My Courses",
    path: "/dashboard/student/courses",
    icon: "book-open",
  },
  {
    title: "Upcoming Classes",
    path: "/dashboard/student/classes",
    icon: "calendar",
  },
  {
    title: "Assignments",
    path: "/dashboard/student/assignments",
    icon: "clipboard",
  },
  {
    title: "Certificates",
    path: "/dashboard/student/certificates",
    icon: "award",
  },
  {
    title: "Messages",
    path: "/dashboard/student/messages",
    icon: "message-circle",
    badge: notificationCount,
  },
  { title: "Settings", path: "/dashboard/student/settings", icon: "settings" },
  {
    title: "Help & Support",
    path: "/dashboard/student/support",
    icon: "help-circle",
  },
];

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
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
      </svg>`;
    case "calendar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
      </svg>`;
    case "clipboard":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
      </svg>`;
    case "award":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "message-circle":
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
      class="sidebar bg-white border-r border-gray-200 transition-all duration-300 ease-in-out flex flex-col"
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
          class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none hidden lg:block"
          @click="toggleSidebar"
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

      <!-- Navigation -->
      <nav class="flex-1 pt-4 pb-4 overflow-y-auto">
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
              />

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
              />
            </NuxtLink>
          </li>
        </ul>
      </nav>

      <!-- User section -->
      <div class="border-t border-gray-200 p-4">
        <div class="flex items-center">
          <!-- Avatar -->
          <img
            :src="user.avatar"
            :alt="user.name"
            class="h-9 w-9 rounded-full object-cover border-2 border-indigo-100"
          />

          <!-- User info - only show if sidebar is open -->
          <div v-if="isSidebarOpen" class="ml-3">
            <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
            <p class="text-xs text-gray-500 capitalize">{{ user.role }}</p>
          </div>

          <!-- Logout button - only show if sidebar is open -->
          <button
            v-if="isSidebarOpen"
            class="ml-auto p-1 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
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
            class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none lg:hidden"
            @click="toggleSidebar"
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
            Student Dashboard
          </div>

          <!-- Right navigation items -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                type="text"
                placeholder="Search..."
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

    <UiSupportChat
      brand-name="LearnConsult Enterprise"
      agent-name="Enterprise Support"
      agent-avatar="/images/instructors/i3.jpg"
      brand-color="blue"
      position="bottom-right"
      greeting-message="Welcome to Enterprise Support. How may we assist you today?"
      response-time="Our dedicated team typically responds within 15 minutes"
    />
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
</style>
