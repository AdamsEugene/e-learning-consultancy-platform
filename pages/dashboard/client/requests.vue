<!-- pages/dashboard/client/requests/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define the page layout
definePageMeta({
  layout: "dashboard-client",
});

// Page meta
useHead({
  title: "Project Requests - Client Dashboard",
  meta: [
    {
      name: "description",
      content: "Manage your project requests and track their status",
    },
  ],
});

// Define types for project requests
interface ProjectRequest {
  id: number;
  title: string;
  description: string;
  category: string;
  status:
    | "draft"
    | "pending"
    | "approved"
    | "rejected"
    | "in_progress"
    | "completed";
  budget: {
    min: number;
    max: number;
    currency: string;
  };
  timeline: {
    startDate: string | null;
    deadline: string | null;
    duration: string | null;
  };
  created: string;
  updated: string;
  files?: string[];
  consultantAssigned?: string;
  consultantId?: number;
  consultantAvatar?: string;
  priority: "low" | "medium" | "high";
  attachments: number;
}

// Set up state
const projectRequests = ref<ProjectRequest[]>([]);
const isLoading = ref(true);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("recent");
const showDeleteModal = ref(false);
const requestToDelete = ref<ProjectRequest | null>(null);
const deleteConfirmText = ref("");
const showCreateModal = ref(false);

// Filter options for categories
const categories = [
  "Web Development",
  "Mobile Development",
  "UI/UX Design",
  "Cloud Architecture",
  "DevOps",
  "Data Analysis",
  "Digital Marketing",
  "Business Strategy",
  "Other",
];

// New request form data
const newRequest = ref({
  title: "",
  description: "",
  category: "",
  priority: "medium",
  budget: {
    min: 0,
    max: 0,
    currency: "USD",
  },
  timeline: {
    deadline: null,
    duration: "",
  },
});

// Fetch project requests (mock data for now)
const fetchProjectRequests = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    projectRequests.value = [
      {
        id: 1,
        title: "E-commerce Platform Redesign",
        description:
          "We need to redesign our e-commerce platform to improve user experience and increase conversion rates.",
        category: "UI/UX Design",
        status: "in_progress",
        budget: {
          min: 5000,
          max: 8000,
          currency: "USD",
        },
        timeline: {
          startDate: "2025-03-15",
          deadline: "2025-05-30",
          duration: "2.5 months",
        },
        created: "2025-03-10T10:30:00Z",
        updated: "2025-03-15T14:20:00Z",
        consultantAssigned: "Sarah Parker",
        consultantId: 12,
        consultantAvatar: "/api/placeholder/32/32",
        priority: "high",
        attachments: 3,
      },
      {
        id: 2,
        title: "Mobile App Development for Inventory Management",
        description:
          "Develop a mobile application for our warehouse team to manage inventory in real-time.",
        category: "Mobile Development",
        status: "pending",
        budget: {
          min: 10000,
          max: 15000,
          currency: "USD",
        },
        timeline: {
          startDate: null,
          deadline: "2025-07-01",
          duration: "3 months",
        },
        created: "2025-04-01T09:15:00Z",
        updated: "2025-04-01T09:15:00Z",
        priority: "medium",
        attachments: 2,
      },
      {
        id: 3,
        title: "Cloud Migration Strategy",
        description:
          "We need assistance in planning and executing a migration from on-premises infrastructure to a cloud-based solution.",
        category: "Cloud Architecture",
        status: "approved",
        budget: {
          min: 15000,
          max: 20000,
          currency: "USD",
        },
        timeline: {
          startDate: "2025-04-20",
          deadline: "2025-08-15",
          duration: "4 months",
        },
        created: "2025-03-25T11:45:00Z",
        updated: "2025-04-10T16:30:00Z",
        consultantAssigned: "Alex Johnson",
        consultantId: 8,
        consultantAvatar: "/api/placeholder/32/32",
        priority: "high",
        attachments: 5,
      },
      {
        id: 4,
        title: "SEO and Content Marketing Strategy",
        description:
          "Develop a comprehensive SEO and content marketing strategy to improve our online visibility.",
        category: "Digital Marketing",
        status: "completed",
        budget: {
          min: 3000,
          max: 5000,
          currency: "USD",
        },
        timeline: {
          startDate: "2025-02-15",
          deadline: "2025-03-31",
          duration: "1.5 months",
        },
        created: "2025-02-10T08:45:00Z",
        updated: "2025-04-02T15:15:00Z",
        consultantAssigned: "Emily Watson",
        consultantId: 15,
        consultantAvatar: "/api/placeholder/32/32",
        priority: "medium",
        attachments: 1,
      },
      {
        id: 5,
        title: "Business Growth Strategy",
        description:
          "Need expert guidance on scaling our business operations and entering new markets.",
        category: "Business Strategy",
        status: "draft",
        budget: {
          min: 7500,
          max: 12000,
          currency: "USD",
        },
        timeline: {
          startDate: null,
          deadline: null,
          duration: "2 months",
        },
        created: "2025-04-12T13:20:00Z",
        updated: "2025-04-12T13:20:00Z",
        priority: "low",
        attachments: 0,
      },
      {
        id: 6,
        title: "DevOps Implementation",
        description:
          "Implement CI/CD pipelines and improve our development workflow.",
        category: "DevOps",
        status: "rejected",
        budget: {
          min: 8000,
          max: 12000,
          currency: "USD",
        },
        timeline: {
          startDate: null,
          deadline: "2025-06-15",
          duration: "3 months",
        },
        created: "2025-03-18T14:30:00Z",
        updated: "2025-03-25T09:45:00Z",
        priority: "medium",
        attachments: 2,
      },
    ];
  } catch (error) {
    console.error("Error fetching project requests:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter project requests based on active tab and search query
const filteredRequests = computed(() => {
  let result = [...projectRequests.value];

  // Apply tab filter
  if (activeTab.value !== "all") {
    result = result.filter((request) => request.status === activeTab.value);
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (request) =>
        request.title.toLowerCase().includes(query) ||
        request.description.toLowerCase().includes(query) ||
        request.category.toLowerCase().includes(query)
    );
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) => new Date(b.updated).getTime() - new Date(a.updated).getTime()
    );
  } else if (sortBy.value === "oldest") {
    result.sort(
      (a, b) => new Date(a.created).getTime() - new Date(b.created).getTime()
    );
  } else if (sortBy.value === "budget-high") {
    result.sort((a, b) => b.budget.max - a.budget.max);
  } else if (sortBy.value === "budget-low") {
    result.sort((a, b) => a.budget.min - b.budget.min);
  } else if (sortBy.value === "title") {
    result.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sortBy.value === "priority") {
    const priorityOrder = { high: 0, medium: 1, low: 2 };
    result.sort(
      (a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]
    );
  }

  return result;
});

// Summary statistics
const statistics = computed(() => {
  return {
    totalRequests: projectRequests.value.length,
    pendingRequests: projectRequests.value.filter((r) => r.status === "pending")
      .length,
    approvedRequests: projectRequests.value.filter(
      (r) => r.status === "approved" || r.status === "in_progress"
    ).length,
    completedRequests: projectRequests.value.filter(
      (r) => r.status === "completed"
    ).length,
    draftRequests: projectRequests.value.filter((r) => r.status === "draft")
      .length,
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

// Format price with currency symbol
const formatPrice = (min: number, max: number, currency: string = "USD") => {
  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: currency,
    maximumFractionDigits: 0,
  });

  return `${formatter.format(min)} - ${formatter.format(max)}`;
};

// Get status badge classes based on request status
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "draft":
      return "bg-gray-100 text-gray-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "approved":
      return "bg-blue-100 text-blue-800";
    case "in_progress":
      return "bg-indigo-100 text-indigo-800";
    case "completed":
      return "bg-green-100 text-green-800";
    case "rejected":
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
      return "bg-amber-100 text-amber-800";
    case "low":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Open delete confirmation modal
const confirmDelete = (request: ProjectRequest) => {
  requestToDelete.value = request;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

// Delete a project request
const deleteRequest = () => {
  if (!requestToDelete.value || deleteConfirmText.value !== "delete") {
    return;
  }

  // In a real app, this would make an API call
  projectRequests.value = projectRequests.value.filter(
    (request) => request.id !== requestToDelete.value?.id
  );
  showDeleteModal.value = false;
  requestToDelete.value = null;
  deleteConfirmText.value = "";
};

// Submit new project request
const submitNewRequest = () => {
  // Validate form (simplified for this example)
  if (
    !newRequest.value.title ||
    !newRequest.value.description ||
    !newRequest.value.category
  ) {
    alert("Please fill in all required fields");
    return;
  }

  // In a real app, this would make an API call
  const now = new Date().toISOString();

  const newProjectRequest: ProjectRequest = {
    id: Math.max(...projectRequests.value.map((r) => r.id)) + 1,
    title: newRequest.value.title,
    description: newRequest.value.description,
    category: newRequest.value.category,
    status: "draft",
    budget: newRequest.value.budget,
    timeline: {
      startDate: null,
      deadline: newRequest.value.timeline.deadline,
      duration: newRequest.value.timeline.duration,
    },
    created: now,
    updated: now,
    priority: newRequest.value.priority as "low" | "medium" | "high",
    attachments: 0,
  };

  projectRequests.value.push(newProjectRequest);

  // Reset form and close modal
  newRequest.value = {
    title: "",
    description: "",
    category: "",
    priority: "medium",
    budget: {
      min: 0,
      max: 0,
      currency: "USD",
    },
    timeline: {
      deadline: null,
      duration: "",
    },
  };

  showCreateModal.value = false;
};

// Get formatted status for display
const getStatusDisplay = (status: string) => {
  return status.replace("_", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Load data when component mounts
onMounted(() => {
  fetchProjectRequests();
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
            Project Requests
          </h1>
          <p class="text-gray-600">
            Create, submit and track your project requests
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <button
            @click="showCreateModal = true"
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
            <span>New Project Request</span>
          </button>
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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Requests</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.totalRequests }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-yellow-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-yellow-600"
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
              <p class="text-sm text-gray-500 font-medium">Pending</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.pendingRequests }}
              </p>
            </div>
          </div>
        </div>

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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Approved/Active</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.approvedRequests }}
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
                  d="M5 13l4 4L19 7"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Completed</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.completedRequests }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-gray-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-gray-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Drafts</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.draftRequests }}
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
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'all'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            All Requests
          </button>
          <button
            @click="activeTab = 'draft'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'draft'
                ? 'bg-gray-200 text-gray-800'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Drafts
          </button>
          <button
            @click="activeTab = 'pending'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'pending'
                ? 'bg-yellow-100 text-yellow-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Pending
          </button>
          <button
            @click="activeTab = 'approved'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'approved'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Approved
          </button>
          <button
            @click="activeTab = 'in_progress'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'in_progress'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            In Progress
          </button>
          <button
            @click="activeTab = 'completed'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'completed'
                ? 'bg-green-100 text-green-700'
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
              placeholder="Search requests..."
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
              <option value="oldest">Oldest First</option>
              <option value="budget-high">Budget (High to Low)</option>
              <option value="budget-low">Budget (Low to High)</option>
              <option value="priority">Priority</option>
              <option value="title">Title (A-Z)</option>
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
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-start space-x-4">
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-3"></div>
            <div class="flex space-x-2 mb-3">
              <div class="h-6 w-20 bg-gray-200 rounded-full"></div>
              <div class="h-6 w-24 bg-gray-200 rounded-full"></div>
            </div>
            <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Requests list -->
    <div
      v-else-if="filteredRequests.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="request in filteredRequests"
        :key="`request-${request.id}`"
        class="p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors"
      >
        <div class="flex flex-col md:flex-row md:items-start">
          <!-- Project request info -->
          <div class="flex-1 min-w-0">
            <div
              class="flex flex-col md:flex-row md:items-center md:justify-between mb-2"
            >
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ request.title }}
                </h3>
                <div class="flex flex-wrap gap-2 mt-2 mb-3">
                  <span
                    class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                    :class="getStatusBadgeClasses(request.status)"
                  >
                    {{ getStatusDisplay(request.status) }}
                  </span>
                  <span
                    class="bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  >
                    {{ request.category }}
                  </span>
                  <span
                    class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                    :class="getPriorityBadgeClasses(request.priority)"
                  >
                    {{ request.priority }} priority
                  </span>
                  <span
                    v-if="request.attachments > 0"
                    class="bg-gray-100 text-gray-800 px-2.5 py-0.5 rounded-full text-xs font-semibold flex items-center"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ request.attachments }} file{{
                      request.attachments !== 1 ? "s" : ""
                    }}
                  </span>
                </div>
              </div>

              <div
                class="mt-2 md:mt-0 flex flex-col items-start md:items-end text-right"
              >
                <div class="text-sm font-medium text-gray-900">
                  {{
                    formatPrice(
                      request.budget.min,
                      request.budget.max,
                      request.budget.currency
                    )
                  }}
                </div>
                <div class="text-xs text-gray-500">Budget range</div>
              </div>
            </div>

            <p class="text-gray-700 mb-4 line-clamp-2">
              {{ request.description }}
            </p>

            <!-- Project request metadata -->
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600">
              <!-- Timeline info -->
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span v-if="request.timeline.duration"
                  >{{ request.timeline.duration }} timeline</span
                >
                <span v-else>Timeline not specified</span>
              </div>

              <!-- Deadline info -->
              <div v-if="request.timeline.deadline" class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span
                  >Deadline: {{ formatDate(request.timeline.deadline) }}</span
                >
              </div>

              <!-- Consultant info -->
              <div v-if="request.consultantAssigned" class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="flex items-center">
                  <img
                    :src="request.consultantAvatar"
                    :alt="request.consultantAssigned"
                    class="h-5 w-5 rounded-full mr-1"
                  />
                  {{ request.consultantAssigned }}
                </span>
              </div>

              <!-- Creation date -->
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v11a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm11 14a1 1 0 01-1 1H5a1 1 0 01-1-1v-6h12v6z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Created: {{ formatDate(request.created) }}</span>
              </div>

              <!-- Last updated date -->
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Updated: {{ formatDate(request.updated) }}</span>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="flex mt-4 md:mt-0 md:ml-4 space-x-2">
            <NuxtLink
              :to="`/dashboard/client/requests/${request.id}`"
              class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
              title="View Details"
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

            <NuxtLink
              v-if="request.status === 'draft'"
              :to="`/dashboard/client/requests/${request.id}/edit`"
              class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
              title="Edit Request"
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
              v-if="request.status === 'draft' || request.status === 'pending'"
              @click="confirmDelete(request)"
              class="p-2 rounded-lg text-gray-500 hover:bg-red-100 hover:text-red-700 transition-colors"
              title="Delete Request"
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
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">
        No project requests found
      </h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No requests match your search criteria."
            : "You haven't created any project requests yet."
        }}
      </p>
      <button
        @click="showCreateModal = true"
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
        <span>Create Your First Project Request</span>
      </button>
    </div>

    <!-- Pagination (simplified for demo) -->
    <div v-if="filteredRequests.length > 0" class="mt-6 flex justify-center">
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
                Math.min(10, filteredRequests.length)
              }}</span>
              of
              <span class="font-medium">{{ filteredRequests.length }}</span>
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
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                1
              </a>
              <span
                class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600"
              >
                2
              </span>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                3
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
            Delete Project Request
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete "{{ requestToDelete?.title }}"? This
            action cannot be undone.
          </p>
        </div>

        <!-- Confirmation input -->
        <div class="mb-4">
          <label
            for="confirm-delete"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Please type <span class="font-semibold">delete</span> to confirm:
          </label>
          <input
            id="confirm-delete"
            v-model="deleteConfirmText"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="Type 'delete' here"
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
            @click="deleteRequest"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="deleteConfirmText !== 'delete'"
            :class="{
              'opacity-50 cursor-not-allowed': deleteConfirmText !== 'delete',
            }"
          >
            Delete Request
          </button>
        </div>
      </div>
    </div>

    <!-- Create Project Request Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 overflow-y-auto"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full p-6 shadow-xl m-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Create New Project Request
          </h3>
          <button
            @click="showCreateModal = false"
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

        <form @submit.prevent="submitNewRequest" class="space-y-6">
          <!-- Title -->
          <div>
            <label
              for="title"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Project Title <span class="text-red-600">*</span>
            </label>
            <input
              id="title"
              v-model="newRequest.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="E.g., E-commerce Website Redesign"
            />
          </div>

          <!-- Description -->
          <div>
            <label
              for="description"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Project Description <span class="text-red-600">*</span>
            </label>
            <textarea
              id="description"
              v-model="newRequest.description"
              rows="4"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Describe your project requirements, objectives, and any specific details..."
            ></textarea>
          </div>

          <!-- Category and Priority -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="category"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Category <span class="text-red-600">*</span>
              </label>
              <select
                id="category"
                v-model="newRequest.category"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="" disabled selected>Select a category</option>
                <option
                  v-for="category in categories"
                  :key="category"
                  :value="category"
                >
                  {{ category }}
                </option>
              </select>
            </div>

            <div>
              <label
                for="priority"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Priority
              </label>
              <select
                id="priority"
                v-model="newRequest.priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
          </div>

          <!-- Budget -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Budget Range
            </label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label
                  for="budget-min"
                  class="block text-xs text-gray-500 mb-1"
                >
                  Minimum
                </label>
                <div class="relative">
                  <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"
                    >$</span
                  >
                  <input
                    id="budget-min"
                    v-model.number="newRequest.budget.min"
                    type="number"
                    min="0"
                    class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="0"
                  />
                </div>
              </div>

              <div>
                <label
                  for="budget-max"
                  class="block text-xs text-gray-500 mb-1"
                >
                  Maximum
                </label>
                <div class="relative">
                  <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500"
                    >$</span
                  >
                  <input
                    id="budget-max"
                    v-model.number="newRequest.budget.max"
                    type="number"
                    min="0"
                    class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="0"
                  />
                </div>
              </div>

              <div>
                <label
                  for="budget-currency"
                  class="block text-xs text-gray-500 mb-1"
                >
                  Currency
                </label>
                <select
                  id="budget-currency"
                  v-model="newRequest.budget.currency"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                  <option value="GBP">GBP</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="deadline"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Deadline (if any)
              </label>
              <input
                id="deadline"
                v-model="newRequest.timeline.deadline"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="duration"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Estimated Duration
              </label>
              <input
                id="duration"
                v-model="newRequest.timeline.duration"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="E.g., 2 weeks, 3 months"
              />
            </div>
          </div>

          <!-- Submit & Cancel buttons -->
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Create Request
            </button>
          </div>
        </form>
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

/* Line clamp for description text */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
