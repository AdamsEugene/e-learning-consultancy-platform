<!-- pages/dashboard/consultant/requests/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define the page metadata
definePageMeta({
  layout: "dashboard-consultant",
});

// Set page title
useHead({
  title: "Client Requests - Consultant Dashboard",
});

// Define types for request data
interface ClientRequest {
  id: number;
  clientName: string;
  clientAvatar: string;
  companyName: string;
  projectTitle: string;
  description: string;
  budget: {
    min: number;
    max: number;
    currency: string;
  };
  status: "pending" | "reviewing" | "accepted" | "declined";
  priority: "low" | "medium" | "high" | "urgent";
  skills: string[];
  timeline: {
    start?: string;
    deadline?: string;
    duration?: string;
  };
  documents?: string[];
  submittedAt: string;
  lastUpdated: string;
}

// State
const requests = ref<ClientRequest[]>([]);
const isLoading = ref(true);
const activeTab = ref("pending");
const searchQuery = ref("");
const sortBy = ref("recent");
const selectedRequest = ref<ClientRequest | null>(null);
const showDetailsModal = ref(false);
const showConfirmModal = ref(false);
const confirmAction = ref<"accept" | "decline" | null>(null);
const confirmMessage = ref("");

// Mock data for requests
const fetchRequests = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    requests.value = [
      {
        id: 1001,
        clientName: "Alex Johnson",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "Tech Innovate LLC",
        projectTitle: "E-commerce Platform Migration",
        description:
          "We need help migrating our existing e-commerce platform to a modern microservices architecture. The current system is built with monolithic PHP and we want to move to a more scalable solution using Node.js and React.",
        budget: {
          min: 15000,
          max: 25000,
          currency: "USD",
        },
        status: "pending",
        priority: "high",
        skills: ["Node.js", "React", "Microservices", "AWS", "CI/CD"],
        timeline: {
          deadline: "2025-07-15",
          duration: "3 months",
        },
        documents: [
          "Project Brief.pdf",
          "Current Architecture.png",
          "Requirements Specification.docx",
        ],
        submittedAt: "2025-04-10T14:30:00Z",
        lastUpdated: "2025-04-10T14:30:00Z",
      },
      {
        id: 1002,
        clientName: "Samantha Williams",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "HealthFirst Inc.",
        projectTitle: "Healthcare Patient Portal",
        description:
          "Development of a secure patient portal for our healthcare facility. Patients need to be able to schedule appointments, view medical records, and communicate with healthcare providers.",
        budget: {
          min: 20000,
          max: 30000,
          currency: "USD",
        },
        status: "reviewing",
        priority: "medium",
        skills: ["React", "Node.js", "HIPAA Compliance", "Security", "MongoDB"],
        timeline: {
          start: "2025-05-01",
          deadline: "2025-08-30",
          duration: "4 months",
        },
        documents: ["Requirements.pdf", "Design Mockups.zip"],
        submittedAt: "2025-04-05T10:15:00Z",
        lastUpdated: "2025-04-12T09:45:00Z",
      },
      {
        id: 1003,
        clientName: "Michael Chen",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "FinTech Solutions",
        projectTitle: "Mobile Payment App Development",
        description:
          "We need to develop a secure mobile payment application for iOS and Android platforms. The app should support various payment methods, peer-to-peer transfers, and include biometric authentication.",
        budget: {
          min: 35000,
          max: 50000,
          currency: "USD",
        },
        status: "pending",
        priority: "urgent",
        skills: [
          "React Native",
          "iOS",
          "Android",
          "Payment Gateway Integration",
          "Security",
        ],
        timeline: {
          deadline: "2025-06-30",
          duration: "4 months",
        },
        documents: ["RFP Document.pdf", "App Mockups.sketch"],
        submittedAt: "2025-04-08T16:20:00Z",
        lastUpdated: "2025-04-08T16:20:00Z",
      },
      {
        id: 1004,
        clientName: "Emily Rodriguez",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "EduLearn Academy",
        projectTitle: "Online Learning Platform Enhancement",
        description:
          "We need to enhance our existing online learning platform with new features including live sessions, improved analytics, and interactive assessments. The current platform is built with Laravel and Vue.js.",
        budget: {
          min: 10000,
          max: 18000,
          currency: "USD",
        },
        status: "accepted",
        priority: "medium",
        skills: ["Laravel", "Vue.js", "WebRTC", "Analytics", "MySQL"],
        timeline: {
          start: "2025-05-15",
          deadline: "2025-07-30",
          duration: "2.5 months",
        },
        documents: ["Platform Overview.pdf", "Feature Requirements.xlsx"],
        submittedAt: "2025-04-03T11:00:00Z",
        lastUpdated: "2025-04-11T15:30:00Z",
      },
      {
        id: 1005,
        clientName: "David Wilson",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "RetailPlus Corp",
        projectTitle: "Inventory Management System",
        description:
          "Development of a comprehensive inventory management system with real-time tracking, reporting, and integration with our existing POS system. The solution should be cloud-based and accessible across multiple devices.",
        budget: {
          min: 25000,
          max: 40000,
          currency: "USD",
        },
        status: "declined",
        priority: "low",
        skills: [
          "Cloud Architecture",
          "Database Design",
          "API Development",
          "React",
          "Node.js",
        ],
        timeline: {
          deadline: "2025-08-15",
          duration: "3 months",
        },
        documents: ["System Requirements.pdf"],
        submittedAt: "2025-04-01T09:00:00Z",
        lastUpdated: "2025-04-09T14:20:00Z",
      },
      {
        id: 1006,
        clientName: "Jessica Brown",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "Creative Media Agency",
        projectTitle: "Digital Asset Management Platform",
        description:
          "We need a custom digital asset management platform to organize, store, and distribute our growing library of digital assets. The platform should support various file formats, include robust search capabilities, and provide access control features.",
        budget: {
          min: 15000,
          max: 25000,
          currency: "USD",
        },
        status: "pending",
        priority: "medium",
        skills: [
          "AWS S3",
          "Node.js",
          "React",
          "ElasticSearch",
          "Media Processing",
        ],
        timeline: {
          deadline: "2025-07-30",
          duration: "3 months",
        },
        documents: ["Project Scope.pdf", "User Workflows.pdf"],
        submittedAt: "2025-04-12T13:45:00Z",
        lastUpdated: "2025-04-12T13:45:00Z",
      },
      {
        id: 1007,
        clientName: "Robert Taylor",
        clientAvatar: "/api/placeholder/40/40",
        companyName: "LogisticsPro Inc.",
        projectTitle: "Fleet Management System",
        description:
          "Development of a comprehensive fleet management system with GPS tracking, maintenance scheduling, driver management, and fuel consumption analytics. The system should include both web and mobile interfaces.",
        budget: {
          min: 30000,
          max: 45000,
          currency: "USD",
        },
        status: "reviewing",
        priority: "high",
        skills: [
          "GPS Integration",
          "React",
          "Node.js",
          "Mobile Development",
          "Data Analytics",
        ],
        timeline: {
          start: "2025-05-01",
          deadline: "2025-09-30",
          duration: "5 months",
        },
        documents: ["System Specifications.docx", "Workflow Diagrams.pdf"],
        submittedAt: "2025-04-07T15:30:00Z",
        lastUpdated: "2025-04-13T10:15:00Z",
      },
    ];
  } catch (error) {
    console.error("Error fetching requests:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter requests based on active tab and search query
const filteredRequests = computed(() => {
  let result = [...requests.value];

  // Apply tab filter
  if (activeTab.value !== "all") {
    result = result.filter((request) => request.status === activeTab.value);
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (request) =>
        request.clientName.toLowerCase().includes(query) ||
        request.companyName.toLowerCase().includes(query) ||
        request.projectTitle.toLowerCase().includes(query) ||
        request.description.toLowerCase().includes(query) ||
        request.skills.some((skill) => skill.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime()
    );
  } else if (sortBy.value === "deadline") {
    result.sort((a, b) => {
      const aDeadline = a.timeline.deadline
        ? new Date(a.timeline.deadline).getTime()
        : Infinity;
      const bDeadline = b.timeline.deadline
        ? new Date(b.timeline.deadline).getTime()
        : Infinity;
      return aDeadline - bDeadline;
    });
  } else if (sortBy.value === "budget-high") {
    result.sort((a, b) => b.budget.max - a.budget.max);
  } else if (sortBy.value === "budget-low") {
    result.sort((a, b) => a.budget.min - b.budget.min);
  } else if (sortBy.value === "priority") {
    const priorityOrder = { urgent: 0, high: 1, medium: 2, low: 3 };
    result.sort(
      (a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]
    );
  }

  return result;
});

// Count of requests by status
const requestCounts = computed(() => {
  return {
    all: requests.value.length,
    pending: requests.value.filter((r) => r.status === "pending").length,
    reviewing: requests.value.filter((r) => r.status === "reviewing").length,
    accepted: requests.value.filter((r) => r.status === "accepted").length,
    declined: requests.value.filter((r) => r.status === "declined").length,
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

// Format budget to display range
const formatBudget = (budget: {
  min: number;
  max: number;
  currency: string;
}) => {
  return `${
    budget.currency
  } ${budget.min.toLocaleString()} - ${budget.max.toLocaleString()}`;
};

// Get priority badge classes
const getPriorityClasses = (priority: string) => {
  switch (priority) {
    case "urgent":
      return "bg-red-100 text-red-800";
    case "high":
      return "bg-orange-100 text-orange-800";
    case "medium":
      return "bg-blue-100 text-blue-800";
    case "low":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "reviewing":
      return "bg-blue-100 text-blue-800";
    case "accepted":
      return "bg-green-100 text-green-800";
    case "declined":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Calculate days remaining until deadline
const getDaysRemaining = (deadline?: string) => {
  if (!deadline) return null;

  const deadlineDate = new Date(deadline);
  const today = new Date();
  const diffTime = deadlineDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return diffDays;
};

// View request details
const viewRequestDetails = (request: ClientRequest) => {
  selectedRequest.value = request;
  showDetailsModal.value = true;
};

// Handle request action
const handleRequestAction = (
  request: ClientRequest,
  action: "accept" | "decline"
) => {
  selectedRequest.value = request;
  confirmAction.value = action;
  confirmMessage.value =
    action === "accept"
      ? `Are you sure you want to accept the project request from ${request.clientName}?`
      : `Are you sure you want to decline the project request from ${request.clientName}?`;
  showConfirmModal.value = true;
};

// Confirm request action
const confirmRequestAction = () => {
  if (!selectedRequest.value || !confirmAction.value) return;

  // In a real app, this would make an API call
  selectedRequest.value.status =
    confirmAction.value === "accept" ? "accepted" : "declined";
  selectedRequest.value.lastUpdated = new Date().toISOString();

  showConfirmModal.value = false;
  confirmAction.value = null;
  selectedRequest.value = null;
};

// Start request review
const startReview = (request: ClientRequest) => {
  // In a real app, this would make an API call
  request.status = "reviewing";
  request.lastUpdated = new Date().toISOString();
};

// Load data when component mounts
onMounted(() => {
  fetchRequests();
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Client Requests</h1>
          <p class="text-gray-600">
            Review and manage incoming consultation requests from clients
          </p>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                  d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">New Requests</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ requestCounts.pending }}
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
              <p class="text-sm text-gray-500 font-medium">Under Review</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ requestCounts.reviewing }}
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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Accepted</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ requestCounts.accepted }}
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
                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Declined</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ requestCounts.declined }}
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
            All Requests
            <span
              class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700"
              >{{ requestCounts.all }}</span
            >
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
            <span
              class="ml-1 px-2 py-0.5 text-xs rounded-full bg-yellow-200 text-yellow-700"
              >{{ requestCounts.pending }}</span
            >
          </button>
          <button
            @click="activeTab = 'reviewing'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'reviewing'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Reviewing
            <span
              class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-200 text-blue-700"
              >{{ requestCounts.reviewing }}</span
            >
          </button>
          <button
            @click="activeTab = 'accepted'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'accepted'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Accepted
            <span
              class="ml-1 px-2 py-0.5 text-xs rounded-full bg-green-200 text-green-700"
              >{{ requestCounts.accepted }}</span
            >
          </button>
          <button
            @click="activeTab = 'declined'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'declined'
                ? 'bg-gray-200 text-gray-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Declined
            <span
              class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-300 text-gray-700"
              >{{ requestCounts.declined }}</span
            >
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
              <option value="recent">Recently Submitted</option>
              <option value="deadline">Upcoming Deadlines</option>
              <option value="budget-high">Budget (High to Low)</option>
              <option value="budget-low">Budget (Low to High)</option>
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
        <div class="flex items-start space-x-4">
          <div class="bg-gray-200 h-10 w-10 rounded-full"></div>
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
            <div class="flex space-x-4">
              <div class="h-4 bg-gray-200 rounded w-16"></div>
              <div class="h-4 bg-gray-200 rounded w-16"></div>
              <div class="h-4 bg-gray-200 rounded w-16"></div>
            </div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-md"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-md"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-md"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Request list -->
    <div
      v-else-if="filteredRequests.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="request in filteredRequests"
        :key="`request-${request.id}`"
        class="p-6 border-b border-gray-200 hover:bg-gray-50 transition-colors"
      >
        <div class="flex flex-col md:flex-row md:items-start">
          <!-- Client info -->
          <div class="flex items-start space-x-4 mb-4 md:mb-0 flex-1">
            <img
              :src="request.clientAvatar"
              :alt="request.clientName"
              class="h-10 w-10 rounded-full object-cover flex-shrink-0"
            />
            <div class="flex-1 min-w-0">
              <div
                class="flex flex-col md:flex-row md:items-center md:justify-between"
              >
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ request.projectTitle }}
                  </h3>
                  <div class="flex items-center mt-1 mb-2">
                    <span class="text-sm text-gray-600"
                      >{{ request.clientName }} •
                      {{ request.companyName }}</span
                    >
                  </div>
                </div>
                <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                  <span
                    class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                    :class="getStatusBadgeClasses(request.status)"
                  >
                    {{ request.status }}
                  </span>
                  <span
                    class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                    :class="getPriorityClasses(request.priority)"
                  >
                    {{ request.priority }} priority
                  </span>
                </div>
              </div>

              <!-- Brief description -->
              <p class="text-gray-600 mb-4 line-clamp-2">
                {{ request.description }}
              </p>

              <!-- Request details -->
              <div class="flex flex-wrap gap-x-6 gap-y-2 mt-2">
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-700">{{
                    formatBudget(request.budget)
                  }}</span>
                </div>

                <div v-if="request.timeline.deadline" class="flex items-center">
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
                  <span class="text-sm text-gray-700">
                    Deadline: {{ formatDate(request.timeline.deadline) }}
                    <span
                      v-if="getDaysRemaining(request.timeline.deadline)"
                      :class="{
                        'text-red-600 font-semibold': getDaysRemaining(request.timeline.deadline)! < 15,
                        'text-yellow-600': getDaysRemaining(request.timeline.deadline)! >= 15 && getDaysRemaining(request.timeline.deadline)! < 30,
                        'text-green-600': getDaysRemaining(request.timeline.deadline)! >= 30
                      }"
                    >
                      ({{ getDaysRemaining(request.timeline.deadline) }} days
                      left)
                    </span>
                  </span>
                </div>

                <div class="flex items-center">
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
                  <span class="text-sm text-gray-700"
                    >Submitted: {{ formatDate(request.submittedAt) }}</span
                  >
                </div>

                <div v-if="request.timeline.duration" class="flex items-center">
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
                  <span class="text-sm text-gray-700"
                    >Duration: {{ request.timeline.duration }}</span
                  >
                </div>
              </div>

              <!-- Required skills -->
              <div class="flex flex-wrap gap-2 mt-4">
                <span
                  v-for="skill in request.skills"
                  :key="skill"
                  class="bg-indigo-50 text-indigo-700 text-xs px-2 py-1 rounded-md"
                >
                  {{ skill }}
                </span>
                <span
                  v-if="request.documents && request.documents.length > 0"
                  class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-md flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ request.documents.length }} document{{
                    request.documents.length > 1 ? "s" : ""
                  }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="flex items-center space-x-2 mt-4 md:mt-0">
            <button
              @click="viewRequestDetails(request)"
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
            </button>

            <div v-if="request.status === 'pending'">
              <button
                @click="startReview(request)"
                class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                title="Start Review"
              >
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
              </button>

              <button
                @click="handleRequestAction(request, 'accept')"
                class="p-2 rounded-lg text-green-500 hover:bg-green-50 hover:text-green-700 transition-colors"
                title="Accept Request"
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

              <button
                @click="handleRequestAction(request, 'decline')"
                class="p-2 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors"
                title="Decline Request"
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

            <div v-else-if="request.status === 'reviewing'">
              <button
                @click="handleRequestAction(request, 'accept')"
                class="p-2 rounded-lg text-green-500 hover:bg-green-50 hover:text-green-700 transition-colors"
                title="Accept Request"
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

              <button
                @click="handleRequestAction(request, 'decline')"
                class="p-2 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors"
                title="Decline Request"
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
            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No requests found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No requests match your search criteria."
            : "You don't have any client requests at the moment."
        }}
      </p>
    </div>

    <!-- Request Details Modal -->
    <div
      v-if="showDetailsModal && selectedRequest"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      @click="showDetailsModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl m-4"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">
                {{ selectedRequest.projectTitle }}
              </h2>
              <div class="flex items-center mt-1">
                <img
                  :src="selectedRequest.clientAvatar"
                  :alt="selectedRequest.clientName"
                  class="h-6 w-6 rounded-full mr-2"
                />
                <span class="text-gray-600"
                  >{{ selectedRequest.clientName }} •
                  {{ selectedRequest.companyName }}</span
                >
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <span
                class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                :class="getStatusBadgeClasses(selectedRequest.status)"
              >
                {{ selectedRequest.status }}
              </span>
              <span
                class="px-2 py-1 rounded-full text-xs font-semibold capitalize"
                :class="getPriorityClasses(selectedRequest.priority)"
              >
                {{ selectedRequest.priority }} priority
              </span>
              <button
                @click="showDetailsModal = false"
                class="text-gray-500 hover:text-gray-700 focus:outline-none"
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
          </div>

          <div class="border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-semibold mb-2">Project Description</h3>
            <p class="text-gray-700">{{ selectedRequest.description }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <h3 class="text-lg font-semibold mb-3">Budget</h3>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">
                  {{ formatBudget(selectedRequest.budget) }}
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-semibold mb-3">Timeline</h3>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div
                  v-if="selectedRequest.timeline.start"
                  class="flex items-center mb-2"
                >
                  <span class="text-gray-600 w-24">Start Date:</span>
                  <span class="font-medium">{{
                    formatDate(selectedRequest.timeline.start)
                  }}</span>
                </div>
                <div
                  v-if="selectedRequest.timeline.deadline"
                  class="flex items-center mb-2"
                >
                  <span class="text-gray-600 w-24">Deadline:</span>
                  <span class="font-medium">
                    {{ formatDate(selectedRequest.timeline.deadline) }}
                    <span
                      v-if="getDaysRemaining(selectedRequest.timeline.deadline)"
                      :class="{
                        'text-red-600': getDaysRemaining(selectedRequest.timeline.deadline)! < 15,
                        'text-yellow-600': getDaysRemaining(selectedRequest.timeline.deadline)! >= 15 && getDaysRemaining(selectedRequest.timeline.deadline)! < 30,
                        'text-green-600': getDaysRemaining(selectedRequest.timeline.deadline)! >= 30
                      }"
                    >
                      ({{
                        getDaysRemaining(selectedRequest.timeline.deadline)
                      }}
                      days left)
                    </span>
                  </span>
                </div>
                <div
                  v-if="selectedRequest.timeline.duration"
                  class="flex items-center"
                >
                  <span class="text-gray-600 w-24">Duration:</span>
                  <span class="font-medium">{{
                    selectedRequest.timeline.duration
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">Required Skills</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="skill in selectedRequest.skills"
                :key="`detail-${skill}`"
                class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-md"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <div
            v-if="
              selectedRequest.documents && selectedRequest.documents.length > 0
            "
            class="mb-6"
          >
            <h3 class="text-lg font-semibold mb-3">Attached Documents</h3>
            <div class="space-y-2">
              <div
                v-for="doc in selectedRequest.documents"
                :key="doc"
                class="flex items-center bg-gray-50 p-3 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="flex-grow">{{ doc }}</span>
                <button
                  class="text-indigo-600 hover:text-indigo-800 transition-colors"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <div
            class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t border-gray-200"
          >
            <div>Submitted: {{ formatDate(selectedRequest.submittedAt) }}</div>
            <div>
              Last updated: {{ formatDate(selectedRequest.lastUpdated) }}
            </div>
          </div>

          <div
            v-if="
              selectedRequest.status === 'pending' ||
              selectedRequest.status === 'reviewing'
            "
            class="mt-6 flex justify-end space-x-4"
          >
            <button
              @click="
                handleRequestAction(selectedRequest, 'decline');
                showDetailsModal = false;
              "
              class="px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors"
            >
              Decline Request
            </button>
            <button
              @click="
                handleRequestAction(selectedRequest, 'accept');
                showDetailsModal = false;
              "
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
              Accept Request
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div
      v-if="showConfirmModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"
            :class="confirmAction === 'accept' ? 'bg-green-100' : 'bg-red-100'"
          >
            <svg
              v-if="confirmAction === 'accept'"
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
            <svg
              v-else
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
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            {{
              confirmAction === "accept" ? "Accept Request" : "Decline Request"
            }}
          </h3>
          <p class="text-sm text-gray-600">
            {{ confirmMessage }}
          </p>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showConfirmModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="confirmRequestAction"
            class="px-4 py-2 text-sm font-medium text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2"
            :class="
              confirmAction === 'accept'
                ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
                : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
            "
          >
            {{ confirmAction === "accept" ? "Accept" : "Decline" }}
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

/* Multi-line text truncation */
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
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
