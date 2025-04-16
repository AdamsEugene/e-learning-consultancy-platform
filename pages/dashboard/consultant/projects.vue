<!-- pages/consultant/projects/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define types for project data
interface Client {
  id: number;
  name: string;
  company: string;
  avatar: string;
}

interface Project {
  id: number;
  title: string;
  description: string;
  status: "active" | "pending" | "completed" | "canceled";
  client: Client;
  startDate?: string;
  endDate?: string;
  budget: number;
  milestone?: {
    current: number;
    total: number;
    nextDue?: string;
  };
  progress: number;
  priority: "high" | "medium" | "low";
  tags: string[];
  team?: {
    id: number;
    name: string;
    avatar: string;
    role: string;
  }[];
  lastActivity?: string;
}

definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Projects - Consultant Dashboard",
});

// State for projects data
const projects = ref<Project[]>([]);
const isLoading = ref(true);
const activeTab = ref("active");
const searchQuery = ref("");
const sortBy = ref("recent");
const showDeleteModal = ref(false);
const projectToDelete = ref<Project | null>(null);
const deleteConfirmText = ref("");

// Fetch projects (mock data for now)
const fetchProjects = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    projects.value = [
      {
        id: 1,
        title: "E-commerce Platform Redesign",
        description:
          "Complete redesign and development of the client's e-commerce platform with modern technologies and improved UX.",
        status: "active",
        client: {
          id: 101,
          name: "John Morrison",
          company: "RetailTech Inc.",
          avatar: "/api/placeholder/40/40",
        },
        startDate: "2025-02-15",
        endDate: "2025-06-30",
        budget: 24000,
        milestone: {
          current: 2,
          total: 5,
          nextDue: "2025-05-10",
        },
        progress: 45,
        priority: "high",
        tags: ["Web Development", "UX Design", "Frontend"],
        team: [
          {
            id: 201,
            name: "Alex Johnson",
            avatar: "/api/placeholder/40/40",
            role: "Lead Developer",
          },
          {
            id: 202,
            name: "Maria Garcia",
            avatar: "/api/placeholder/40/40",
            role: "UI/UX Designer",
          },
        ],
        lastActivity: "2025-04-12T10:30:00Z",
      },
      {
        id: 2,
        title: "Mobile App Development",
        description:
          "Building a native iOS and Android application for financial services with real-time data processing.",
        status: "active",
        client: {
          id: 102,
          name: "Sarah Williams",
          company: "FinCore Solutions",
          avatar: "/api/placeholder/40/40",
        },
        startDate: "2025-03-01",
        endDate: "2025-08-15",
        budget: 36000,
        milestone: {
          current: 1,
          total: 4,
          nextDue: "2025-05-15",
        },
        progress: 28,
        priority: "high",
        tags: ["Mobile", "React Native", "API Integration"],
        team: [
          {
            id: 203,
            name: "David Kim",
            avatar: "/api/placeholder/40/40",
            role: "Mobile Developer",
          },
          {
            id: 204,
            name: "Lisa Chen",
            avatar: "/api/placeholder/40/40",
            role: "Backend Developer",
          },
        ],
        lastActivity: "2025-04-14T14:45:00Z",
      },
      {
        id: 3,
        title: "Supply Chain Management System",
        description:
          "Developing a comprehensive supply chain management solution with advanced analytics and reporting.",
        status: "active",
        client: {
          id: 103,
          name: "Robert Thompson",
          company: "Global Logistics Ltd.",
          avatar: "/api/placeholder/40/40",
        },
        startDate: "2025-01-20",
        endDate: "2025-07-20",
        budget: 42000,
        milestone: {
          current: 3,
          total: 6,
          nextDue: "2025-04-30",
        },
        progress: 65,
        priority: "medium",
        tags: ["System Architecture", "Database", "Analytics"],
        team: [
          {
            id: 205,
            name: "Michael Brown",
            avatar: "/api/placeholder/40/40",
            role: "System Architect",
          },
          {
            id: 206,
            name: "Jennifer Wilson",
            avatar: "/api/placeholder/40/40",
            role: "Data Scientist",
          },
        ],
        lastActivity: "2025-04-10T09:15:00Z",
      },
      {
        id: 4,
        title: "Healthcare Patient Portal",
        description:
          "Proposal for developing a secure patient portal with telehealth capabilities and medical records management.",
        status: "pending",
        client: {
          id: 104,
          name: "Emily Davis",
          company: "MediCare Health Systems",
          avatar: "/api/placeholder/40/40",
        },
        budget: 28000,
        priority: "medium",
        tags: ["Healthcare", "Security", "Web Application"],
        progress: 0,
        lastActivity: "2025-04-08T11:20:00Z",
      },
      {
        id: 5,
        title: "AI-Powered Analytics Dashboard",
        description:
          "Proposal for creating an analytics dashboard with AI capabilities for business intelligence and predictive analytics.",
        status: "pending",
        client: {
          id: 105,
          name: "Daniel Martinez",
          company: "DataInsight Corp",
          avatar: "/api/placeholder/40/40",
        },
        budget: 32000,
        priority: "high",
        tags: ["AI/ML", "Data Visualization", "Business Intelligence"],
        progress: 0,
        lastActivity: "2025-04-05T15:45:00Z",
      },
      {
        id: 6,
        title: "Corporate Website Redesign",
        description:
          "Complete redesign of corporate website with focus on brand identity, user experience, and responsive design.",
        status: "completed",
        client: {
          id: 106,
          name: "Amanda Jackson",
          company: "TechFirm Solutions",
          avatar: "/api/placeholder/40/40",
        },
        startDate: "2024-10-15",
        endDate: "2025-01-30",
        budget: 18000,
        progress: 100,
        priority: "medium",
        tags: ["Web Design", "Branding", "Responsive"],
        lastActivity: "2025-01-30T16:20:00Z",
      },
      {
        id: 7,
        title: "CRM System Migration",
        description:
          "Migration of legacy CRM system to a modern cloud-based solution with improved functionality and user interface.",
        status: "completed",
        client: {
          id: 107,
          name: "Thomas Wilson",
          company: "Sales Optimize Inc.",
          avatar: "/api/placeholder/40/40",
        },
        startDate: "2024-11-01",
        endDate: "2025-02-28",
        budget: 25000,
        progress: 100,
        priority: "high",
        tags: ["CRM", "Cloud Migration", "Data Management"],
        lastActivity: "2025-02-28T13:10:00Z",
      },
      {
        id: 8,
        title: "IoT Home Automation System",
        description:
          "Development of a smart home automation system with IoT devices, mobile app control, and voice assistant integration.",
        status: "canceled",
        client: {
          id: 108,
          name: "Laura Smith",
          company: "SmartHome Technologies",
          avatar: "/api/placeholder/40/40",
        },
        startDate: "2024-12-10",
        endDate: "2025-01-15",
        budget: 22000,
        progress: 30,
        priority: "medium",
        tags: ["IoT", "Mobile App", "Voice Integration"],
        lastActivity: "2025-01-15T10:15:00Z",
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
        project.client.name.toLowerCase().includes(query) ||
        project.client.company.toLowerCase().includes(query) ||
        project.tags.some((tag) => tag.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort((a, b) => {
      return (
        new Date(b.lastActivity || "").getTime() -
        new Date(a.lastActivity || "").getTime()
      );
    });
  } else if (sortBy.value === "budget-high") {
    result.sort((a, b) => b.budget - a.budget);
  } else if (sortBy.value === "budget-low") {
    result.sort((a, b) => a.budget - b.budget);
  } else if (sortBy.value === "progress") {
    result.sort((a, b) => b.progress - a.progress);
  } else if (sortBy.value === "deadline") {
    result.sort((a, b) => {
      if (!a.endDate) return 1;
      if (!b.endDate) return -1;
      return new Date(a.endDate).getTime() - new Date(b.endDate).getTime();
    });
  } else if (sortBy.value === "priority") {
    const priorityWeight = { high: 3, medium: 2, low: 1 };
    result.sort((a, b) => {
      return priorityWeight[b.priority] - priorityWeight[a.priority];
    });
  }

  return result;
});

// Summary statistics
const statistics = computed(() => {
  const activeProjects = projects.value.filter(
    (p) => p.status === "active"
  ).length;
  const pendingProjects = projects.value.filter(
    (p) => p.status === "pending"
  ).length;
  const completedProjects = projects.value.filter(
    (p) => p.status === "completed"
  ).length;

  const totalBudget = projects.value
    .filter((p) => p.status === "active" || p.status === "completed")
    .reduce((sum, project) => sum + project.budget, 0);

  const pendingBudget = projects.value
    .filter((p) => p.status === "pending")
    .reduce((sum, project) => sum + project.budget, 0);

  return {
    activeProjects,
    pendingProjects,
    completedProjects,
    totalBudget,
    pendingBudget,
  };
});

// Format date to readable string
const formatDate = (dateString?: string) => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format number with commas
const formatNumber = (num: number) => {
  return num.toLocaleString("en-US");
};

// Format price with currency symbol
const formatCurrency = (price: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "active":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "completed":
      return "bg-blue-100 text-blue-800";
    case "canceled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get priority badge classes
const getPriorityBadgeClasses = (priority: string) => {
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

// Calculate days left until deadline
const getDaysRemaining = (endDate?: string) => {
  if (!endDate) return null;

  const today = new Date();
  const deadline = new Date(endDate);
  const diffTime = deadline.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return diffDays;
};

// Open delete confirmation modal
const confirmDelete = (project: Project) => {
  projectToDelete.value = project;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

// Delete a project
const deleteProject = () => {
  if (
    !projectToDelete.value ||
    deleteConfirmText.value !== projectToDelete.value.title
  ) {
    return;
  }

  // In a real app, this would make an API call
  projects.value = projects.value.filter(
    (project) => project.id !== projectToDelete.value?.id
  );
  showDeleteModal.value = false;
  projectToDelete.value = null;
  deleteConfirmText.value = "";
};

// Handle project action based on status
const performAction = (project: Project) => {
  if (project.status === "pending") {
    // Start project
    project.status = "active";
    project.startDate = new Date().toISOString().split("T")[0];
    const endDate = new Date();
    endDate.setMonth(endDate.getMonth() + 3); // Set end date 3 months from now
    project.endDate = endDate.toISOString().split("T")[0];
    project.milestone = {
      current: 0,
      total: 4,
      nextDue: new Date(endDate.setMonth(endDate.getMonth() - 2))
        .toISOString()
        .split("T")[0],
    };
  } else if (project.status === "active") {
    // Complete project
    project.status = "completed";
    project.progress = 100;
    project.lastActivity = new Date().toISOString();
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
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Projects</h1>
          <p class="text-gray-600">Manage your client projects and proposals</p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/consultant/projects/create"
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
            <span>New Project Proposal</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Revenue</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(statistics.totalBudget) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
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
        </div>

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
              <option value="budget-high">Budget: High to Low</option>
              <option value="budget-low">Budget: Low to High</option>
              <option value="progress">Progress</option>
              <option value="deadline">Deadline</option>
              <option value="priority">Priority</option>
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
        class="animate-pulse p-6 border-b border-gray-200"
      >
        <div
          class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0"
        >
          <div class="flex-1 space-y-4">
            <div class="h-5 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            <div class="flex space-x-2">
              <div class="h-6 w-16 bg-gray-200 rounded"></div>
              <div class="h-6 w-16 bg-gray-200 rounded"></div>
            </div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Projects list -->
    <div v-else-if="filteredProjects.length > 0" class="space-y-6">
      <div
        v-for="project in filteredProjects"
        :key="`project-${project.id}`"
        class="bg-white rounded-lg shadow border border-gray-200 transition-all duration-300 hover:shadow-md"
      >
        <div class="p-6">
          <div
            class="flex flex-col lg:flex-row lg:items-start space-y-4 lg:space-y-0 lg:space-x-4"
          >
            <!-- Project Info -->
            <div class="flex-1">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-1">
                    {{ project.title }}
                  </h3>

                  <div class="flex flex-wrap items-center mt-1 mb-2 space-x-2">
                    <span
                      class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                      :class="getStatusBadgeClasses(project.status)"
                    >
                      {{ project.status }}
                    </span>

                    <span
                      class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                      :class="getPriorityBadgeClasses(project.priority)"
                    >
                      {{ project.priority }} priority
                    </span>

                    <span
                      v-if="project.startDate && project.endDate"
                      class="text-sm text-gray-600"
                    >
                      {{ formatDate(project.startDate) }} -
                      {{ formatDate(project.endDate) }}
                    </span>
                  </div>
                </div>

                <!-- Project Budget -->
                <div class="hidden md:block text-right">
                  <div class="text-lg font-bold text-gray-900">
                    {{ formatCurrency(project.budget) }}
                  </div>
                  <div class="text-xs text-gray-500">Budget</div>
                </div>
              </div>

              <!-- Project Description -->
              <p class="text-gray-600 mb-4">{{ project.description }}</p>

              <!-- Project Tags -->
              <div class="flex flex-wrap gap-2 mb-4">
                <span
                  v-for="(tag, tagIndex) in project.tags"
                  :key="`tag-${project.id}-${tagIndex}`"
                  class="bg-gray-100 text-gray-700 text-xs rounded-full px-3 py-1"
                >
                  {{ tag }}
                </span>
              </div>

              <!-- Progress bar (for active projects) -->
              <div v-if="project.status === 'active'" class="mb-4">
                <div class="flex justify-between items-center mb-1">
                  <div class="text-sm font-medium text-gray-700">Progress</div>
                  <div class="text-sm font-medium text-gray-700">
                    {{ project.progress }}%
                  </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="bg-indigo-600 h-2.5 rounded-full"
                    :style="{ width: `${project.progress}%` }"
                  ></div>
                </div>
              </div>

              <!-- Next milestone (for active projects) -->
              <div
                v-if="project.status === 'active' && project.milestone"
                class="mb-4"
              >
                <div class="flex items-center text-sm text-gray-600">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-indigo-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span>
                    Milestone {{ project.milestone.current + 1 }}/{{
                      project.milestone.total
                    }}
                    due {{ formatDate(project.milestone.nextDue) }}
                    <span
                      v-if="
                        project.milestone.nextDue &&
                        getDaysRemaining(project.milestone.nextDue) !== null
                      "
                      :class="getDaysRemaining(project.milestone.nextDue)! < 7 ? 'text-red-600 font-medium' : ''"
                    >
                      ({{ getDaysRemaining(project.milestone.nextDue) }} days
                      left)
                    </span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Client and Actions -->
            <div class="flex-shrink-0 flex flex-col items-end space-y-4">
              <!-- Client info -->
              <div class="flex items-center space-x-2">
                <div class="text-right">
                  <div class="text-sm font-medium text-gray-900">
                    {{ project.client.name }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ project.client.company }}
                  </div>
                </div>
                <img
                  :src="project.client.avatar"
                  :alt="project.client.name"
                  class="h-10 w-10 rounded-full object-cover border border-gray-200"
                />
              </div>

              <!-- Mobile budget -->
              <div class="md:hidden text-right">
                <div class="text-lg font-bold text-gray-900">
                  {{ formatCurrency(project.budget) }}
                </div>
                <div class="text-xs text-gray-500">Budget</div>
              </div>

              <!-- Team members (if any) -->
              <div
                v-if="project.team && project.team.length > 0"
                class="flex -space-x-2"
              >
                <img
                  v-for="member in project.team.slice(0, 3)"
                  :key="member.id"
                  :src="member.avatar"
                  :alt="member.name"
                  :title="member.name + ' - ' + member.role"
                  class="h-8 w-8 rounded-full border-2 border-white object-cover"
                />
                <div
                  v-if="project.team.length > 3"
                  class="h-8 w-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-600"
                >
                  +{{ project.team.length - 3 }}
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex space-x-2">
                <NuxtLink
                  :to="`/consultant/projects/${project.id}`"
                  class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                  title="View Project"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
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
                </NuxtLink>

                <button
                  v-if="
                    project.status === 'pending' || project.status === 'active'
                  "
                  @click="performAction(project)"
                  class="p-2 rounded-lg text-gray-500 hover:bg-green-100 hover:text-green-700 transition-colors"
                  :title="
                    project.status === 'pending'
                      ? 'Accept Proposal'
                      : 'Complete Project'
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>

                <NuxtLink
                  :to="`/consultant/projects/${project.id}/edit`"
                  class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                  title="Edit Project"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                    />
                  </svg>
                </NuxtLink>

                <button
                  @click="confirmDelete(project)"
                  class="p-2 rounded-lg text-gray-500 hover:bg-red-100 hover:text-red-700 transition-colors"
                  title="Delete Project"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
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
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No projects found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No projects match your search criteria."
            : "You haven't created any projects yet."
        }}
      </p>
      <NuxtLink
        to="/consultant/projects/create"
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
        <span>Create New Project</span>
      </NuxtLink>
    </div>

    <!-- Pagination (simplified for demo) -->
    <div v-if="filteredProjects.length > 5" class="mt-6 flex justify-center">
      <div
        class="bg-white px-4 py-3 flex items-center justify-between border border-gray-200 rounded-lg shadow sm:px-6"
      >
        <div class="flex-1 flex justify-between sm:hidden">
          <a
            href="#"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </a>
          <a
            href="#"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </a>
        </div>
        <div
          class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
        >
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">1</span> to
              <span class="font-medium">{{
                Math.min(5, filteredProjects.length)
              }}</span>
              of
              <span class="font-medium">{{ filteredProjects.length }}</span>
              results
            </p>
          </div>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <a
                href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600"
              >
                1
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                2
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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
              </a>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"
          >
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
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Delete Project
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete "{{ projectToDelete?.title }}"? This
            action cannot be undone.
          </p>
        </div>

        <!-- Confirmation input -->
        <div class="mb-4">
          <label
            for="confirm-delete"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Please type
            <span class="font-semibold">{{ projectToDelete?.title }}</span> to
            confirm:
          </label>
          <input
            id="confirm-delete"
            v-model="deleteConfirmText"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="Type project title here"
          />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="deleteProject"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="deleteConfirmText !== projectToDelete?.title"
            :class="{
              'opacity-50 cursor-not-allowed':
                deleteConfirmText !== projectToDelete?.title,
            }"
          >
            Delete Project
          </button>
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
</style>
