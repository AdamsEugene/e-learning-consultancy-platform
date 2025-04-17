<!-- pages/dashboard/admin/projects/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";

// Define page meta to use the admin dashboard layout
definePageMeta({
  layout: "dashboard-admin",
});

// Set page metadata
useHead({
  title: "Project Management - Admin Dashboard",
  meta: [
    {
      name: "description",
      content: "Manage all consultancy projects on the platform",
    },
  ],
});

// Types for our data
interface Project {
  id: number;
  title: string;
  clientName: string;
  clientId: number;
  consultantName: string;
  consultantId: number;
  status:
    | "pending"
    | "approved"
    | "in_progress"
    | "on_hold"
    | "completed"
    | "cancelled"
    | "awaiting_review";
  startDate: string;
  endDate: string | null;
  budget: number;
  completion: number;
  category: string;
  priority: "low" | "medium" | "high" | "urgent";
  lastUpdated: string;
  createdAt: string;
}

interface ProjectStats {
  total: number;
  pending: number;
  approved: number;
  inProgress: number;
  onHold: number;
  completed: number;
  cancelled: number;
  awaitingReview: number;
  totalRevenue: number;
  avgBudget: number;
  avgCompletionTime: number;
}

// State
const projects = ref<Project[]>([]);
const projectStats = ref<ProjectStats>({
  total: 0,
  pending: 0,
  approved: 0,
  inProgress: 0,
  onHold: 0,
  completed: 0,
  cancelled: 0,
  awaitingReview: 0,
  totalRevenue: 0,
  avgBudget: 0,
  avgCompletionTime: 0,
});
const isLoading = ref(true);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("recent");
const showDeleteModal = ref(false);
const showReviewModal = ref(false);
const projectToDelete = ref<Project | null>(null);
const projectToReview = ref<Project | null>(null);
const deleteConfirmText = ref("");
const reviewDecision = ref<"approve" | "reject" | null>(null);
const reviewComment = ref("");
const projectReviews = ref<{
  [key: number]: { status: string; comment: string };
}>({});
const selectedCategory = ref("all");
const selectedPriority = ref("all");
const dateRange = ref<{ start: string | null; end: string | null }>({
  start: null,
  end: null,
});

// List of categories and priority levels for filtering
const categories = ref([
  "Web Development",
  "Mobile Development",
  "UI/UX Design",
  "Data Science",
  "Cloud Solutions",
  "DevOps",
  "Digital Transformation",
  "IT Consulting",
  "Security Solutions",
  "Other",
]);

const priorities = ref([
  { value: "low", label: "Low", color: "bg-gray-500" },
  { value: "medium", label: "Medium", color: "bg-blue-500" },
  { value: "high", label: "High", color: "bg-amber-500" },
  { value: "urgent", label: "Urgent", color: "bg-red-500" },
]);

// Mock data fetch - in a real app this would be an API call
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
        clientName: "ShopMaxx Inc.",
        clientId: 101,
        consultantName: "Sarah Parker",
        consultantId: 201,
        status: "in_progress",
        startDate: "2025-02-15",
        endDate: "2025-05-15",
        budget: 15000,
        completion: 65,
        category: "Web Development",
        priority: "high",
        lastUpdated: "2025-04-05T14:30:00Z",
        createdAt: "2025-02-10T09:20:00Z",
      },
      {
        id: 2,
        title: "Mobile Banking App Development",
        clientName: "SecureBank Ltd.",
        clientId: 102,
        consultantName: "James Wilson",
        consultantId: 202,
        status: "in_progress",
        startDate: "2025-01-20",
        endDate: "2025-06-30",
        budget: 28500,
        completion: 40,
        category: "Mobile Development",
        priority: "high",
        lastUpdated: "2025-04-02T10:15:00Z",
        createdAt: "2025-01-15T11:30:00Z",
      },
      {
        id: 3,
        title: "Cloud Migration Strategy",
        clientName: "Legacy Systems Corp.",
        clientId: 103,
        consultantName: "Michelle Rodriguez",
        consultantId: 203,
        status: "completed",
        startDate: "2024-11-01",
        endDate: "2025-02-28",
        budget: 22000,
        completion: 100,
        category: "Cloud Solutions",
        priority: "medium",
        lastUpdated: "2025-02-28T16:45:00Z",
        createdAt: "2024-10-25T13:10:00Z",
      },
      {
        id: 4,
        title: "Customer Portal UI Redesign",
        clientName: "ServiceFlow Inc.",
        clientId: 104,
        consultantName: "David Chen",
        consultantId: 204,
        status: "awaiting_review",
        startDate: "2025-03-10",
        endDate: null,
        budget: 9500,
        completion: 90,
        category: "UI/UX Design",
        priority: "medium",
        lastUpdated: "2025-04-08T09:20:00Z",
        createdAt: "2025-03-05T14:20:00Z",
      },
      {
        id: 5,
        title: "Supply Chain Analytics Dashboard",
        clientName: "GlobalLogistics Co.",
        clientId: 105,
        consultantName: "Emma Thompson",
        consultantId: 205,
        status: "pending",
        startDate: "2025-04-20",
        endDate: "2025-07-31",
        budget: 18700,
        completion: 0,
        category: "Data Science",
        priority: "low",
        lastUpdated: "2025-04-10T11:30:00Z",
        createdAt: "2025-04-10T11:30:00Z",
      },
      {
        id: 6,
        title: "DevOps Pipeline Implementation",
        clientName: "SoftTech Solutions",
        clientId: 106,
        consultantName: "Alex Johnson",
        consultantId: 206,
        status: "approved",
        startDate: "2025-04-15",
        endDate: "2025-06-15",
        budget: 12500,
        completion: 0,
        category: "DevOps",
        priority: "high",
        lastUpdated: "2025-04-12T15:40:00Z",
        createdAt: "2025-04-01T10:00:00Z",
      },
      {
        id: 7,
        title: "IT Security Assessment",
        clientName: "HealthCare Systems Inc.",
        clientId: 107,
        consultantName: "Lisa Kim",
        consultantId: 207,
        status: "on_hold",
        startDate: "2025-02-01",
        endDate: "2025-03-15",
        budget: 8000,
        completion: 35,
        category: "Security Solutions",
        priority: "urgent",
        lastUpdated: "2025-02-20T13:15:00Z",
        createdAt: "2025-01-25T09:45:00Z",
      },
      {
        id: 8,
        title: "ERP System Integration",
        clientName: "Manufacturing Plus",
        clientId: 108,
        consultantName: "Robert Lewis",
        consultantId: 208,
        status: "cancelled",
        startDate: "2025-01-10",
        endDate: "2025-04-30",
        budget: 32000,
        completion: 20,
        category: "IT Consulting",
        priority: "medium",
        lastUpdated: "2025-02-15T16:30:00Z",
        createdAt: "2025-01-05T11:20:00Z",
      },
      {
        id: 9,
        title: "Marketing Automation Implementation",
        clientName: "GrowthMaxx Agency",
        clientId: 109,
        consultantName: "Sarah Parker",
        consultantId: 201,
        status: "in_progress",
        startDate: "2025-03-01",
        endDate: "2025-05-30",
        budget: 14500,
        completion: 50,
        category: "Digital Transformation",
        priority: "medium",
        lastUpdated: "2025-04-07T10:30:00Z",
        createdAt: "2025-02-25T14:15:00Z",
      },
      {
        id: 10,
        title: "Blockchain Prototype Development",
        clientName: "FinTech Innovations",
        clientId: 110,
        consultantName: "Jason Smith",
        consultantId: 209,
        status: "awaiting_review",
        startDate: "2025-03-15",
        endDate: "2025-05-15",
        budget: 21000,
        completion: 95,
        category: "Web Development",
        priority: "high",
        lastUpdated: "2025-04-14T09:45:00Z",
        createdAt: "2025-03-10T13:30:00Z",
      },
      {
        id: 11,
        title: "Customer Experience Strategy",
        clientName: "RetailOne Solutions",
        clientId: 111,
        consultantName: "Michelle Rodriguez",
        consultantId: 203,
        status: "completed",
        startDate: "2024-12-01",
        endDate: "2025-03-15",
        budget: 19500,
        completion: 100,
        category: "Digital Transformation",
        priority: "medium",
        lastUpdated: "2025-03-15T14:20:00Z",
        createdAt: "2024-11-25T10:10:00Z",
      },
      {
        id: 12,
        title: "AI Product Recommendation Engine",
        clientName: "ShopMaxx Inc.",
        clientId: 101,
        consultantName: "Emma Thompson",
        consultantId: 205,
        status: "in_progress",
        startDate: "2025-02-20",
        endDate: "2025-06-20",
        budget: 25000,
        completion: 30,
        category: "Data Science",
        priority: "high",
        lastUpdated: "2025-04-10T11:15:00Z",
        createdAt: "2025-02-15T09:40:00Z",
      },
    ];

    // Calculate project statistics
    calculateProjectStats();
  } catch (error) {
    console.error("Error fetching projects:", error);
  } finally {
    isLoading.value = false;
  }
};

// Calculate project statistics
const calculateProjectStats = () => {
  const stats: ProjectStats = {
    total: projects.value.length,
    pending: projects.value.filter((p) => p.status === "pending").length,
    approved: projects.value.filter((p) => p.status === "approved").length,
    inProgress: projects.value.filter((p) => p.status === "in_progress").length,
    onHold: projects.value.filter((p) => p.status === "on_hold").length,
    completed: projects.value.filter((p) => p.status === "completed").length,
    cancelled: projects.value.filter((p) => p.status === "cancelled").length,
    awaitingReview: projects.value.filter((p) => p.status === "awaiting_review")
      .length,
    totalRevenue: projects.value.reduce((sum, project) => {
      // Only count revenue from completed projects
      return project.status === "completed" ? sum + project.budget : sum;
    }, 0),
    avgBudget:
      projects.value.length > 0
        ? Math.round(
            projects.value.reduce((sum, project) => sum + project.budget, 0) /
              projects.value.length
          )
        : 0,
    avgCompletionTime: calculateAvgCompletionDays(),
  };

  projectStats.value = stats;
};

// Calculate average completion time in days for completed projects
const calculateAvgCompletionDays = () => {
  const completedProjects = projects.value.filter(
    (p) => p.status === "completed" && p.endDate
  );

  if (completedProjects.length === 0) return 0;

  const totalDays = completedProjects.reduce((sum, project) => {
    const startDate = new Date(project.startDate).getTime();
    const endDate = new Date(project.endDate as string).getTime();
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
    return sum + days;
  }, 0);

  return Math.round(totalDays / completedProjects.length);
};

// Filter projects based on active tab, search query, and other filters
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
        project.clientName.toLowerCase().includes(query) ||
        project.consultantName.toLowerCase().includes(query) ||
        project.category.toLowerCase().includes(query)
    );
  }

  // Apply category filter
  if (selectedCategory.value !== "all") {
    result = result.filter(
      (project) => project.category === selectedCategory.value
    );
  }

  // Apply priority filter
  if (selectedPriority.value !== "all") {
    result = result.filter(
      (project) => project.priority === selectedPriority.value
    );
  }

  // Apply date range filter
  if (dateRange.value.start && dateRange.value.end) {
    const start = new Date(dateRange.value.start).getTime();
    const end = new Date(dateRange.value.end).getTime();

    result = result.filter((project) => {
      const projectStart = new Date(project.startDate).getTime();
      return projectStart >= start && projectStart <= end;
    });
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.lastUpdated).getTime() - new Date(a.lastUpdated).getTime()
    );
  } else if (sortBy.value === "title") {
    result.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sortBy.value === "budget_high") {
    result.sort((a, b) => b.budget - a.budget);
  } else if (sortBy.value === "budget_low") {
    result.sort((a, b) => a.budget - b.budget);
  } else if (sortBy.value === "start_date") {
    result.sort(
      (a, b) =>
        new Date(a.startDate).getTime() - new Date(b.startDate).getTime()
    );
  } else if (sortBy.value === "completion") {
    result.sort((a, b) => b.completion - a.completion);
  } else if (sortBy.value === "priority") {
    const priorityOrder = { urgent: 0, high: 1, medium: 2, low: 3 };
    result.sort(
      (a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]
    );
  }

  return result;
});

// Helper functions
const formatDate = (dateString: string) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "approved":
      return "bg-blue-100 text-blue-800";
    case "in_progress":
      return "bg-green-100 text-green-800";
    case "on_hold":
      return "bg-amber-100 text-amber-800";
    case "completed":
      return "bg-indigo-100 text-indigo-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    case "awaiting_review":
      return "bg-purple-100 text-purple-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

const getPriorityBadgeClasses = (priority: string) => {
  switch (priority) {
    case "low":
      return "bg-gray-100 text-gray-800";
    case "medium":
      return "bg-blue-100 text-blue-800";
    case "high":
      return "bg-amber-100 text-amber-800";
    case "urgent":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Action handlers
const confirmDelete = (project: Project) => {
  projectToDelete.value = project;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

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
  calculateProjectStats();

  showDeleteModal.value = false;
  projectToDelete.value = null;
  deleteConfirmText.value = "";
};

const openReviewModal = (project: Project) => {
  projectToReview.value = project;
  reviewDecision.value = null;
  reviewComment.value = "";
  showReviewModal.value = true;
};

const submitReview = () => {
  if (!projectToReview.value || !reviewDecision.value) {
    return;
  }

  // In a real app, this would make an API call
  const project = projects.value.find(
    (p) => p.id === projectToReview.value?.id
  );
  if (project) {
    if (reviewDecision.value === "approve") {
      project.status = "approved";
    } else {
      project.status = "pending";
    }

    project.lastUpdated = new Date().toISOString();

    // Store the review in our review tracking object
    projectReviews.value[project.id] = {
      status: reviewDecision.value,
      comment: reviewComment.value,
    };
  }

  calculateProjectStats();
  showReviewModal.value = false;
  projectToReview.value = null;
  reviewDecision.value = null;
  reviewComment.value = "";
};

const changeProjectStatus = (project: Project, newStatus: string) => {
  // In a real app, this would make an API call
  const projectToUpdate = projects.value.find((p) => p.id === project.id);
  if (projectToUpdate) {
    projectToUpdate.status = newStatus as any;
    projectToUpdate.lastUpdated = new Date().toISOString();

    // If marking as completed, set endDate to today and completion to 100%
    if (newStatus === "completed") {
      projectToUpdate.endDate = new Date().toISOString().split("T")[0];
      projectToUpdate.completion = 100;
    }
  }

  calculateProjectStats();
};

// Reset filters
const resetFilters = () => {
  activeTab.value = "all";
  searchQuery.value = "";
  selectedCategory.value = "all";
  selectedPriority.value = "all";
  dateRange.value = { start: null, end: null };
  sortBy.value = "recent";
};

// Watch for changes in date range
watch([dateRange], () => {
  if (dateRange.value.start && dateRange.value.end) {
    const startDate = new Date(dateRange.value.start);
    const endDate = new Date(dateRange.value.end);

    if (startDate > endDate) {
      // Swap dates if end is before start
      dateRange.value = {
        start: dateRange.value.end,
        end: dateRange.value.start,
      };
    }
  }
});

// Load data when component mounts
onMounted(() => {
  fetchProjects();
});
</script>

<template>
  <div>
    <!-- Page header with stats -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Project Management
          </h1>
          <p class="text-gray-600">
            Monitor and manage all consultancy projects across the platform
          </p>
        </div>

        <!-- Action buttons -->
        <div class="mt-4 md:mt-0 flex space-x-2">
          <button
            @click="resetFilters"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <div class="flex items-center">
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
              <span>Reset Filters</span>
            </div>
          </button>

          <a
            href="/admin/projects/export"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <div class="flex items-center">
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
              <span>Export Data</span>
            </div>
          </a>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Projects Card -->
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
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Projects</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ projectStats.total }}
              </p>
            </div>
          </div>
        </div>

        <!-- Active Projects Card -->
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
              <p class="text-sm text-gray-500 font-medium">In Progress</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ projectStats.inProgress }}
              </p>
            </div>
          </div>
        </div>

        <!-- Pending Review Card -->
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
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Awaiting Review</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ projectStats.awaitingReview }}
              </p>
              <p
                v-if="projectStats.awaitingReview > 0"
                class="text-xs text-purple-600 font-medium mt-1"
              >
                Action Needed
              </p>
            </div>
          </div>
        </div>

        <!-- Revenue Card -->
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">
                Revenue (Completed)
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(projectStats.totalRevenue) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional stats row -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Average Budget</p>
              <p class="text-xl font-bold text-gray-900">
                {{ formatCurrency(projectStats.avgBudget) }}
              </p>
            </div>
            <div class="bg-gray-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">
                Avg. Completion Time
              </p>
              <p class="text-xl font-bold text-gray-900">
                {{ projectStats.avgCompletionTime }} days
              </p>
            </div>
            <div class="bg-gray-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-600"
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
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Status Breakdown</p>
              <div class="flex items-center space-x-2 mt-2">
                <span class="flex items-center">
                  <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                  <span class="text-xs text-gray-600"
                    >{{ projectStats.inProgress }} In Progress</span
                  >
                </span>
                <span class="flex items-center">
                  <span class="w-2 h-2 rounded-full bg-indigo-500 mr-1"></span>
                  <span class="text-xs text-gray-600"
                    >{{ projectStats.completed }} Completed</span
                  >
                </span>
                <span class="flex items-center">
                  <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                  <span class="text-xs text-gray-600"
                    >{{ projectStats.pending }} Pending</span
                  >
                </span>
              </div>
            </div>
            <div class="bg-gray-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div class="flex flex-col space-y-4">
        <!-- Tab navigation -->
        <div class="flex flex-wrap space-x-1 overflow-x-auto pb-2">
          <button
            @click="activeTab = 'all'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'all'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            All Projects ({{ projectStats.total }})
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
            Pending ({{ projectStats.pending }})
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
            Approved ({{ projectStats.approved }})
          </button>
          <button
            @click="activeTab = 'in_progress'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'in_progress'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            In Progress ({{ projectStats.inProgress }})
          </button>
          <button
            @click="activeTab = 'on_hold'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'on_hold'
                ? 'bg-amber-100 text-amber-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            On Hold ({{ projectStats.onHold }})
          </button>
          <button
            @click="activeTab = 'completed'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'completed'
                ? 'bg-indigo-100 text-indigo-800'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Completed ({{ projectStats.completed }})
          </button>
          <button
            @click="activeTab = 'awaiting_review'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'awaiting_review'
                ? 'bg-purple-100 text-purple-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Awaiting Review ({{ projectStats.awaitingReview }})
          </button>
          <button
            @click="activeTab = 'cancelled'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
            :class="
              activeTab === 'cancelled'
                ? 'bg-red-100 text-red-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Cancelled ({{ projectStats.cancelled }})
          </button>
        </div>

        <!-- Advanced filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search projects..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

          <!-- Category filter -->
          <div class="relative">
            <select
              v-model="selectedCategory"
              class="w-full pl-10 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Categories</option>
              <option
                v-for="category in categories"
                :key="category"
                :value="category"
              >
                {{ category }}
              </option>
            </select>
            <div class="absolute left-3 top-2.5 text-gray-400">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 11-2 0 1 1 0 012 0zM2 13a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2zm14 1a1 1 0 11-2 0 1 1 0 012 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
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

          <!-- Priority filter -->
          <div class="relative">
            <select
              v-model="selectedPriority"
              class="w-full pl-10 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Priorities</option>
              <option
                v-for="priority in priorities"
                :key="priority.value"
                :value="priority.value"
              >
                {{ priority.label }}
              </option>
            </select>
            <div class="absolute left-3 top-2.5 text-gray-400">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 3a1 1 0 000 2h10a1 1 0 100-2H3zm0 4a1 1 0 000 2h6a1 1 0 100-2H3zm0 4a1 1 0 100 2h4a1 1 0 100-2H3zm12-3a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
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

          <!-- Sort by -->
          <div class="relative">
            <select
              v-model="sortBy"
              class="w-full pl-10 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="recent">Recently Updated</option>
              <option value="title">Title (A-Z)</option>
              <option value="budget_high">Budget (High to Low)</option>
              <option value="budget_low">Budget (Low to High)</option>
              <option value="start_date">Start Date</option>
              <option value="completion">Completion %</option>
              <option value="priority">Priority</option>
            </select>
            <div class="absolute left-3 top-2.5 text-gray-400">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"
                />
              </svg>
            </div>
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

        <!-- Date range filter -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="relative">
            <label
              for="startDate"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Start Date Range (From)</label
            >
            <input
              id="startDate"
              v-model="dateRange.start"
              type="date"
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div class="absolute left-3 top-9 text-gray-400">
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

          <div class="relative">
            <label
              for="endDate"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Start Date Range (To)</label
            >
            <input
              id="endDate"
              v-model="dateRange.end"
              type="date"
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div class="absolute left-3 top-9 text-gray-400">
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
      </div>
    </div>

    <!-- Projects table -->
    <div
      class="bg-white rounded-lg shadow overflow-hidden border border-gray-200"
    >
      <!-- Loading state -->
      <div v-if="isLoading" class="p-6">
        <div class="flex flex-col space-y-4">
          <div v-for="i in 3" :key="i" class="animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
            <div class="h-8 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-8 bg-gray-200 rounded w-full mb-2"></div>
            <div class="h-8 bg-gray-200 rounded w-full"></div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredProjects.length === 0" class="p-8 text-center">
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
          No projects found
        </h3>
        <p class="text-gray-600 mb-6">
          {{
            searchQuery ||
            selectedCategory !== "all" ||
            selectedPriority !== "all" ||
            dateRange.start ||
            activeTab !== "all"
              ? "No projects match your current filter criteria."
              : "There are no projects in the system yet."
          }}
        </p>
        <button
          @click="resetFilters"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
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
          <span>Clear Filters</span>
        </button>
      </div>

      <!-- Project list -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Project Details
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Client
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Consultant
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Budget
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Timeline
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
              v-for="project in filteredProjects"
              :key="project.id"
              class="hover:bg-gray-50"
            >
              <!-- Project Details -->
              <td class="px-6 py-4">
                <div>
                  <div class="flex items-center">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ project.title }}
                    </p>
                    <span
                      v-if="project.status === 'awaiting_review'"
                      class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 animate-pulse"
                    >
                      Needs Review
                    </span>
                  </div>
                  <div class="flex items-center mt-1">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mr-2"
                      :class="getPriorityBadgeClasses(project.priority)"
                    >
                      {{
                        project.priority.charAt(0).toUpperCase() +
                        project.priority.slice(1)
                      }}
                      Priority
                    </span>
                    <span class="text-sm text-gray-500">{{
                      project.category
                    }}</span>
                  </div>
                  <div class="mt-2">
                    <div class="relative pt-1">
                      <div class="flex items-center justify-between">
                        <div>
                          <span
                            class="text-xs font-semibold inline-block text-indigo-600"
                          >
                            Completion: {{ project.completion }}%
                          </span>
                        </div>
                      </div>
                      <div
                        class="overflow-hidden h-2 text-xs flex rounded bg-indigo-100"
                      >
                        <div
                          :style="{ width: `${project.completion}%` }"
                          class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Client -->
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900">
                  {{ project.clientName }}
                </div>
                <div class="text-xs text-gray-500">
                  ID: {{ project.clientId }}
                </div>
              </td>

              <!-- Consultant -->
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900">
                  {{ project.consultantName }}
                </div>
                <div class="text-xs text-gray-500">
                  ID: {{ project.consultantId }}
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getStatusBadgeClasses(project.status)"
                >
                  {{
                    project.status.replace("_", " ").charAt(0).toUpperCase() +
                    project.status.replace("_", " ").slice(1)
                  }}
                </span>
                <div class="text-xs text-gray-500 mt-1">
                  Updated: {{ formatDate(project.lastUpdated) }}
                </div>
              </td>

              <!-- Budget -->
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">
                  {{ formatCurrency(project.budget) }}
                </div>
              </td>

              <!-- Timeline -->
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900">
                  Start: {{ formatDate(project.startDate) }}
                </div>
                <div class="text-sm text-gray-500">
                  End:
                  {{ project.endDate ? formatDate(project.endDate) : "TBD" }}
                </div>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                <div class="flex space-x-2">
                  <!-- View -->
                  <a
                    :href="`/admin/projects/${project.id}`"
                    class="text-indigo-600 hover:text-indigo-900"
                    title="View Project Details"
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
                  </a>

                  <!-- Edit -->
                  <a
                    :href="`/admin/projects/${project.id}/edit`"
                    class="text-blue-600 hover:text-blue-900"
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
                  </a>

                  <!-- Status change button -->
                  <div class="relative inline-block text-left">
                    <button
                      class="text-green-600 hover:text-green-900"
                      title="Change Status"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947z"
                          clip-rule="evenodd"
                        />
                        <path
                          d="M15.704 7.704a.75.75 0 01.042 1.06l-4.5 4.75a.75.75 0 01-1.088.022l-2.25-2.25a.75.75 0 011.062-1.06l1.7 1.7 3.978-4.2a.75.75 0 011.06-.042z"
                        />
                      </svg>
                    </button>

                    <!-- Status dropdown menu -->
                    <div
                      class="dropdown hidden absolute z-10 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                      <div class="py-1">
                        <a
                          href="#"
                          @click.prevent="
                            changeProjectStatus(project, 'pending')
                          "
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          >Pending</a
                        >
                        <a
                          href="#"
                          @click.prevent="
                            changeProjectStatus(project, 'approved')
                          "
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          >Approved</a
                        >
                        <a
                          href="#"
                          @click.prevent="
                            changeProjectStatus(project, 'in_progress')
                          "
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          >In Progress</a
                        >
                        <a
                          href="#"
                          @click.prevent="
                            changeProjectStatus(project, 'on_hold')
                          "
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          >On Hold</a
                        >
                        <a
                          href="#"
                          @click.prevent="
                            changeProjectStatus(project, 'completed')
                          "
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          >Completed</a
                        >
                        <a
                          href="#"
                          @click.prevent="
                            changeProjectStatus(project, 'cancelled')
                          "
                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                          >Cancelled</a
                        >
                      </div>
                    </div>
                  </div>

                  <!-- Review button (only for awaiting_review status) -->
                  <button
                    v-if="project.status === 'awaiting_review'"
                    @click="openReviewModal(project)"
                    class="text-purple-600 hover:text-purple-900"
                    title="Review Project"
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
                  </button>

                  <!-- Delete -->
                  <button
                    @click="confirmDelete(project)"
                    class="text-red-600 hover:text-red-900"
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
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="filteredProjects.length > 0"
        class="px-6 py-4 bg-white border-t border-gray-200"
      >
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500">
            Showing
            <span class="font-medium">{{ filteredProjects.length }}</span>
            projects
          </div>
          <div class="flex space-x-2">
            <button
              class="px-3 py-1 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
              disabled
            >
              Previous
            </button>
            <button
              class="px-3 py-1 rounded-md bg-indigo-50 border border-indigo-300 text-sm font-medium text-indigo-700 hover:bg-indigo-100"
            >
              1
            </button>
            <button
              class="px-3 py-1 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
              disabled
            >
              Next
            </button>
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
            Are you sure you want to delete "<span class="font-semibold">{{
              projectToDelete?.title
            }}</span
            >"? This action cannot be undone.
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

    <!-- Review modal -->
    <div
      v-if="showReviewModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-xl">
        <div class="mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Review Project
            </h3>
            <button
              @click="showReviewModal = false"
              class="text-gray-400 hover:text-gray-500"
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
          <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <p class="text-sm font-medium text-gray-700 mb-2">
              Project Details:
            </p>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">
              {{ projectToReview?.title }}
            </h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-500">Client:</p>
                <p class="font-medium">{{ projectToReview?.clientName }}</p>
              </div>
              <div>
                <p class="text-gray-500">Consultant:</p>
                <p class="font-medium">{{ projectToReview?.consultantName }}</p>
              </div>
              <div>
                <p class="text-gray-500">Budget:</p>
                <p class="font-medium">
                  {{
                    projectToReview
                      ? formatCurrency(projectToReview.budget)
                      : ""
                  }}
                </p>
              </div>
              <div>
                <p class="text-gray-500">Completion:</p>
                <p class="font-medium">{{ projectToReview?.completion }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Review form -->
        <div class="space-y-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Review Decision
            </label>
            <div class="mt-2 space-y-2">
              <div class="flex items-center">
                <input
                  id="approve"
                  name="review-decision"
                  type="radio"
                  v-model="reviewDecision"
                  value="approve"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <label
                  for="approve"
                  class="ml-3 block text-sm font-medium text-gray-700"
                >
                  Approve Project
                </label>
              </div>
              <div class="flex items-center">
                <input
                  id="reject"
                  name="review-decision"
                  type="radio"
                  v-model="reviewDecision"
                  value="reject"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                />
                <label
                  for="reject"
                  class="ml-3 block text-sm font-medium text-gray-700"
                >
                  Send Back for Revision
                </label>
              </div>
            </div>
          </div>

          <div>
            <label
              for="review-comment"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Review Comments
            </label>
            <textarea
              id="review-comment"
              v-model="reviewComment"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Add comments for the consultant and client..."
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showReviewModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="submitReview"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            :disabled="!reviewDecision"
            :class="{ 'opacity-50 cursor-not-allowed': !reviewDecision }"
          >
            Submit Review
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

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Status change dropdown animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
