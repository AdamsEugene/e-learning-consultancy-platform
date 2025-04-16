<!-- pages/dashboard/client/projects/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta
definePageMeta({
  layout: "dashboard-client",
});

// Page title and metadata
useHead({
  title: "My Projects - Client Dashboard",
  meta: [
    {
      name: "description",
      content: "Manage and track all your active and past projects",
    },
  ],
});

// Types for project data
interface Project {
  id: number;
  title: string;
  description: string;
  consultant: {
    id: number;
    name: string;
    avatar: string;
    specialization: string;
  };
  status: "active" | "pending" | "completed" | "on_hold" | "cancelled";
  progress: number;
  startDate: string;
  endDate?: string;
  budget: number;
  nextMilestone?: {
    title: string;
    dueDate: string;
  };
  lastUpdate: string;
  priority: "low" | "medium" | "high" | "urgent";
}

// State
const projects = ref<Project[]>([]);
const isLoading = ref(true);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("recent");
const showProjectDetailsModal = ref(false);
const selectedProject = ref<Project | null>(null);
const filterPriority = ref<string[]>([]);

// Fetch projects (mock data for demo)
const fetchProjects = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data for projects
    projects.value = [
      {
        id: 1,
        title: "E-Commerce Website Redesign",
        description:
          "Complete redesign of our online store with improved UX/UI and mobile responsiveness.",
        consultant: {
          id: 101,
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
          specialization: "UX/UI Design",
        },
        status: "active",
        progress: 65,
        startDate: "2025-01-15",
        budget: 12000,
        nextMilestone: {
          title: "Finalize checkout flow",
          dueDate: "2025-04-25",
        },
        lastUpdate: "2025-04-12",
        priority: "high",
      },
      {
        id: 2,
        title: "Customer Relationship Management System",
        description:
          "Implementation of a custom CRM system to improve customer service and sales tracking.",
        consultant: {
          id: 102,
          name: "David Johnson",
          avatar: "/api/placeholder/100/100",
          specialization: "Software Architecture",
        },
        status: "active",
        progress: 40,
        startDate: "2025-02-20",
        budget: 25000,
        nextMilestone: {
          title: "Database integration",
          dueDate: "2025-04-30",
        },
        lastUpdate: "2025-04-15",
        priority: "medium",
      },
      {
        id: 3,
        title: "Mobile App Development",
        description:
          "Creating a native mobile app for both iOS and Android to complement our web platform.",
        consultant: {
          id: 103,
          name: "Alex Chen",
          avatar: "/api/placeholder/100/100",
          specialization: "Mobile Development",
        },
        status: "pending",
        progress: 10,
        startDate: "2025-03-25",
        budget: 18000,
        nextMilestone: {
          title: "UI component library",
          dueDate: "2025-05-15",
        },
        lastUpdate: "2025-04-10",
        priority: "medium",
      },
      {
        id: 4,
        title: "SEO Optimization Project",
        description:
          "Comprehensive SEO audit and implementation of recommended improvements.",
        consultant: {
          id: 104,
          name: "Emily Wilson",
          avatar: "/api/placeholder/100/100",
          specialization: "Digital Marketing",
        },
        status: "completed",
        progress: 100,
        startDate: "2024-12-01",
        endDate: "2025-03-15",
        budget: 8500,
        lastUpdate: "2025-03-15",
        priority: "low",
      },
      {
        id: 5,
        title: "Data Analytics Dashboard",
        description:
          "Creating a custom analytics dashboard to visualize business metrics and KPIs.",
        consultant: {
          id: 105,
          name: "Michael Brown",
          avatar: "/api/placeholder/100/100",
          specialization: "Data Science",
        },
        status: "on_hold",
        progress: 35,
        startDate: "2025-01-30",
        budget: 15000,
        lastUpdate: "2025-03-28",
        priority: "medium",
      },
      {
        id: 6,
        title: "IT Infrastructure Upgrade",
        description:
          "Assessment and modernization of current IT infrastructure with cloud migration planning.",
        consultant: {
          id: 106,
          name: "Lisa Rodriguez",
          avatar: "/api/placeholder/100/100",
          specialization: "IT Infrastructure",
        },
        status: "active",
        progress: 75,
        startDate: "2025-02-10",
        budget: 30000,
        nextMilestone: {
          title: "Cloud migration strategy",
          dueDate: "2025-05-01",
        },
        lastUpdate: "2025-04-14",
        priority: "urgent",
      },
      {
        id: 7,
        title: "Content Marketing Strategy",
        description:
          "Development of a comprehensive content strategy to improve brand visibility.",
        consultant: {
          id: 107,
          name: "James Taylor",
          avatar: "/api/placeholder/100/100",
          specialization: "Content Marketing",
        },
        status: "cancelled",
        progress: 20,
        startDate: "2025-01-10",
        endDate: "2025-02-15",
        budget: 7500,
        lastUpdate: "2025-02-15",
        priority: "low",
      },
      {
        id: 8,
        title: "Security Audit & Enhancement",
        description:
          "Comprehensive security assessment and implementation of necessary improvements.",
        consultant: {
          id: 108,
          name: "Olivia Martinez",
          avatar: "/api/placeholder/100/100",
          specialization: "Cybersecurity",
        },
        status: "completed",
        progress: 100,
        startDate: "2025-02-05",
        endDate: "2025-03-25",
        budget: 12500,
        lastUpdate: "2025-03-25",
        priority: "high",
      },
    ];
  } catch (error) {
    console.error("Error fetching projects:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter projects based on active tab and search query
const filteredProjects = computed(() => {
  let result = [...projects.value];

  // Apply tab filter
  if (activeTab.value !== "all") {
    result = result.filter((project) => project.status === activeTab.value);
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (project) =>
        project.title.toLowerCase().includes(query) ||
        project.description.toLowerCase().includes(query) ||
        project.consultant.name.toLowerCase().includes(query)
    );
  }

  // Apply priority filter
  if (filterPriority.value.length > 0) {
    result = result.filter((project) =>
      filterPriority.value.includes(project.priority)
    );
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.lastUpdate).getTime() - new Date(a.lastUpdate).getTime()
    );
  } else if (sortBy.value === "deadline") {
    result.sort((a, b) => {
      if (!a.nextMilestone && !b.nextMilestone) return 0;
      if (!a.nextMilestone) return 1;
      if (!b.nextMilestone) return -1;
      return (
        new Date(a.nextMilestone.dueDate).getTime() -
        new Date(b.nextMilestone.dueDate).getTime()
      );
    });
  } else if (sortBy.value === "priority") {
    const priorityOrder = { urgent: 0, high: 1, medium: 2, low: 3 };
    result.sort(
      (a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]
    );
  } else if (sortBy.value === "progress") {
    result.sort((a, b) => b.progress - a.progress);
  } else if (sortBy.value === "budget") {
    result.sort((a, b) => b.budget - a.budget);
  }

  return result;
});

// Summary statistics
const statistics = computed(() => {
  const active = projects.value.filter((p) => p.status === "active").length;
  const pending = projects.value.filter((p) => p.status === "pending").length;
  const completed = projects.value.filter(
    (p) => p.status === "completed"
  ).length;
  const onHold = projects.value.filter((p) => p.status === "on_hold").length;

  const totalBudget = projects.value.reduce(
    (sum, project) => sum + project.budget,
    0
  );
  const activeBudget = projects.value
    .filter((p) => p.status === "active")
    .reduce((sum, project) => sum + project.budget, 0);

  return {
    totalProjects: projects.value.length,
    activeProjects: active,
    pendingProjects: pending,
    completedProjects: completed,
    onHoldProjects: onHold,
    totalBudget,
    activeBudget,
  };
});

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(amount);
};

// Get status badge classes based on project status
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "active":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "completed":
      return "bg-blue-100 text-blue-800";
    case "on_hold":
      return "bg-orange-100 text-orange-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get priority badge classes
const getPriorityBadgeClasses = (priority: string) => {
  switch (priority) {
    case "urgent":
      return "bg-red-100 text-red-800";
    case "high":
      return "bg-orange-100 text-orange-800";
    case "medium":
      return "bg-yellow-100 text-yellow-800";
    case "low":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Progress bar color based on progress percentage
const getProgressBarColor = (progress: number) => {
  if (progress < 25) return "bg-red-500";
  if (progress < 50) return "bg-yellow-500";
  if (progress < 75) return "bg-blue-500";
  return "bg-green-500";
};

// Show project details modal
const showProjectDetails = (project: Project) => {
  selectedProject.value = project;
  showProjectDetailsModal.value = true;
};

// Toggle priority filter
const togglePriorityFilter = (priority: string) => {
  if (filterPriority.value.includes(priority)) {
    filterPriority.value = filterPriority.value.filter((p) => p !== priority);
  } else {
    filterPriority.value.push(priority);
  }
};

// Load data when component mounts
onMounted(() => {
  fetchProjects();
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
          <h1 class="text-2xl font-bold text-gray-900 mb-1">My Projects</h1>
          <p class="text-gray-600">
            Manage and monitor all your active, pending, and completed projects
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/dashboard/client/requests/create"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
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
            <span>Request New Project</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Projects</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.totalProjects }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
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
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Projects</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.activeProjects }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">
                Completed Projects
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.completedProjects }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Budget</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(statistics.activeBudget) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div class="flex flex-col space-y-4">
        <!-- Tab navigation -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            @click="activeTab = 'all'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'all'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            All Projects
          </button>
          <button
            @click="activeTab = 'active'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'active'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Active
          </button>
          <button
            @click="activeTab = 'pending'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'pending'
                ? 'bg-yellow-100 text-yellow-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Pending
          </button>
          <button
            @click="activeTab = 'on_hold'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'on_hold'
                ? 'bg-orange-100 text-orange-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            On Hold
          </button>
          <button
            @click="activeTab = 'completed'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'completed'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Completed
          </button>
          <button
            @click="activeTab = 'cancelled'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'cancelled'
                ? 'bg-red-100 text-red-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Cancelled
          </button>
        </div>

        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0"
        >
          <!-- Search and sort controls -->
          <div class="flex items-center space-x-2">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search projects..."
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

            <div class="relative">
              <select
                v-model="sortBy"
                class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="recent">Recently Updated</option>
                <option value="deadline">Upcoming Deadlines</option>
                <option value="priority">Priority (High to Low)</option>
                <option value="progress">Progress (High to Low)</option>
                <option value="budget">Budget (High to Low)</option>
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
          </div>

          <!-- Priority filters -->
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Priority:</span>
            <button
              @click="togglePriorityFilter('urgent')"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                filterPriority.includes('urgent')
                  ? 'bg-red-100 text-red-700 ring-1 ring-red-400'
                  : 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-700'
              "
            >
              Urgent
            </button>
            <button
              @click="togglePriorityFilter('high')"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                filterPriority.includes('high')
                  ? 'bg-orange-100 text-orange-700 ring-1 ring-orange-400'
                  : 'bg-gray-100 text-gray-600 hover:bg-orange-50 hover:text-orange-700'
              "
            >
              High
            </button>
            <button
              @click="togglePriorityFilter('medium')"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                filterPriority.includes('medium')
                  ? 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-400'
                  : 'bg-gray-100 text-gray-600 hover:bg-yellow-50 hover:text-yellow-700'
              "
            >
              Medium
            </button>
            <button
              @click="togglePriorityFilter('low')"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                filterPriority.includes('low')
                  ? 'bg-green-100 text-green-700 ring-1 ring-green-400'
                  : 'bg-gray-100 text-gray-600 hover:bg-green-50 hover:text-green-700'
              "
            >
              Low
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="i in 3"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="bg-gray-200 h-24 w-24 rounded-full"></div>
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Project list -->
    <div v-else-if="filteredProjects.length > 0" class="space-y-4">
      <div
        v-for="project in filteredProjects"
        :key="`project-${project.id}`"
        class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300"
      >
        <div class="p-5">
          <!-- Project header with title, status, and priority -->
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"
          >
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900 mb-1">
                {{ project.title }}
              </h3>
              <div class="flex flex-wrap items-center space-x-2">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getStatusBadgeClasses(project.status)"
                >
                  {{ project.status.replace("_", " ") }}
                </span>
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getPriorityBadgeClasses(project.priority)"
                >
                  {{ project.priority }}
                </span>
                <span class="text-sm text-gray-500">
                  Started: {{ formatDate(project.startDate) }}
                </span>
                <span v-if="project.endDate" class="text-sm text-gray-500">
                  Ended: {{ formatDate(project.endDate) }}
                </span>
              </div>
            </div>

            <div class="flex items-center space-x-2 mt-3 sm:mt-0">
              <button
                @click="showProjectDetails(project)"
                class="inline-flex items-center bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors text-sm font-medium"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                  <path
                    fill-rule="evenodd"
                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                View Details
              </button>
              <NuxtLink
                :to="`/dashboard/client/projects/${project.id}/messages`"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                title="Messages"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
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
              </NuxtLink>
              <NuxtLink
                :to="`/dashboard/client/projects/${project.id}/files`"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                title="Files"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                    clip-rule="evenodd"
                  />
                </svg>
              </NuxtLink>
            </div>
          </div>

          <!-- Project description -->
          <p class="text-gray-600 mb-4">{{ project.description }}</p>

          <!-- Project details row -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Consultant info -->
            <div class="flex items-center">
              <img
                :src="project.consultant.avatar"
                :alt="project.consultant.name"
                class="h-10 w-10 rounded-full object-cover mr-3"
              />
              <div>
                <p class="text-sm font-medium text-gray-900">
                  {{ project.consultant.name }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ project.consultant.specialization }}
                </p>
              </div>
            </div>

            <!-- Next milestone (if available) -->
            <div v-if="project.nextMilestone" class="flex items-center">
              <div class="bg-blue-100 rounded-full p-2 mr-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-blue-600"
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
              <div>
                <p class="text-sm font-medium text-gray-900">
                  {{ project.nextMilestone.title }}
                </p>
                <p class="text-xs text-gray-500">
                  Due: {{ formatDate(project.nextMilestone.dueDate) }}
                </p>
              </div>
            </div>

            <!-- Budget info -->
            <div class="flex items-center">
              <div class="bg-green-100 rounded-full p-2 mr-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-green-600"
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
                <p class="text-sm font-medium text-gray-900">
                  {{ formatCurrency(project.budget) }}
                </p>
                <p class="text-xs text-gray-500">Project Budget</p>
              </div>
            </div>
          </div>

          <!-- Progress bar -->
          <div class="mb-1 flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">Progress</span>
            <span class="text-sm font-medium text-gray-700"
              >{{ project.progress }}%</span
            >
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div
              class="h-2.5 rounded-full"
              :class="getProgressBarColor(project.progress)"
              :style="{ width: `${project.progress}%` }"
            ></div>
          </div>

          <!-- Last update and action button -->
          <div
            class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100"
          >
            <span class="text-xs text-gray-500"
              >Last updated: {{ formatDate(project.lastUpdate) }}</span
            >

            <div class="flex space-x-2">
              <NuxtLink
                :to="`/dashboard/client/projects/${project.id}`"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
              >
                Project Dashboard
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
    </div>

    <!-- Empty state -->
    <div
      v-else
      class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
    >
      <div
        class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No projects found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery || filterPriority.length > 0
            ? "No projects match your search criteria."
            : "You haven't started any projects yet."
        }}
      </p>
      <NuxtLink
        to="/dashboard/client/requests/create"
        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
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
        <span>Start Your First Project</span>
      </NuxtLink>
    </div>

    <!-- Project Details Modal -->
    <div
      v-if="showProjectDetailsModal && selectedProject"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      @click="showProjectDetailsModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full mx-4 shadow-xl overflow-hidden"
        @click.stop
      >
        <!-- Header with close button -->
        <div
          class="flex justify-between items-center border-b border-gray-200 px-6 py-4"
        >
          <h3 class="text-lg font-bold text-gray-900">Project Details</h3>
          <button
            @click="showProjectDetailsModal = false"
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
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
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- Project content -->
        <div class="px-6 py-4">
          <div class="flex items-center space-x-2 mb-4">
            <h2 class="text-2xl font-bold text-gray-900">
              {{ selectedProject.title }}
            </h2>
            <span
              class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
              :class="getStatusBadgeClasses(selectedProject.status)"
            >
              {{ selectedProject.status.replace("_", " ") }}
            </span>
            <span
              class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
              :class="getPriorityBadgeClasses(selectedProject.priority)"
            >
              {{ selectedProject.priority }}
            </span>
          </div>

          <p class="text-gray-600 mb-6">{{ selectedProject.description }}</p>

          <!-- Project metadata -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-4">
              <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Dates</h4>
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <div>
                    <p class="text-sm text-gray-900">
                      Start Date: {{ formatDate(selectedProject.startDate) }}
                    </p>
                    <p
                      v-if="selectedProject.endDate"
                      class="text-sm text-gray-900"
                    >
                      End Date: {{ formatDate(selectedProject.endDate) }}
                    </p>
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Budget</h4>
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-2"
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
                  <p class="text-sm text-gray-900">
                    {{ formatCurrency(selectedProject.budget) }}
                  </p>
                </div>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">
                  Last Updated
                </h4>
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <p class="text-sm text-gray-900">
                    {{ formatDate(selectedProject.lastUpdate) }}
                  </p>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">
                  Consultant
                </h4>
                <div class="flex items-center">
                  <img
                    :src="selectedProject.consultant.avatar"
                    :alt="selectedProject.consultant.name"
                    class="h-10 w-10 rounded-full object-cover mr-3"
                  />
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ selectedProject.consultant.name }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ selectedProject.consultant.specialization }}
                    </p>
                  </div>
                </div>
              </div>

              <div v-if="selectedProject.nextMilestone">
                <h4 class="text-sm font-medium text-gray-500 mb-1">
                  Next Milestone
                </h4>
                <div class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-2 mt-0.5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ selectedProject.nextMilestone.title }}
                    </p>
                    <p class="text-xs text-gray-500">
                      Due:
                      {{ formatDate(selectedProject.nextMilestone.dueDate) }}
                    </p>
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Progress</h4>
                <div class="mb-1 flex items-center justify-between">
                  <span class="text-xs font-medium text-gray-700"
                    >{{ selectedProject.progress }}% Complete</span
                  >
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="h-2.5 rounded-full"
                    :class="getProgressBarColor(selectedProject.progress)"
                    :style="{ width: `${selectedProject.progress}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer with actions -->
        <div
          class="bg-gray-50 px-6 py-4 flex justify-between border-t border-gray-200"
        >
          <button
            @click="showProjectDetailsModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Close
          </button>
          <div class="flex space-x-3">
            <NuxtLink
              :to="`/dashboard/client/projects/${selectedProject.id}/messages`"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Contact Consultant
            </NuxtLink>
            <NuxtLink
              :to="`/dashboard/client/projects/${selectedProject.id}`"
              class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            >
              View Project Dashboard
            </NuxtLink>
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

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
