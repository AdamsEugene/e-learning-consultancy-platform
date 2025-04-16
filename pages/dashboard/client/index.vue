<!-- pages/client/dashboard/index.vue -->
<script setup lang="ts">
definePageMeta({
  layout: "dashboard-client",
});

// Page meta
useHead({
  title: "Client Dashboard - LearnConsult",
});

// Define types
interface Project {
  id: number;
  title: string;
  description: string;
  status: "active" | "pending" | "completed" | "canceled";
  progress: number;
  consultant: {
    id: number;
    name: string;
    avatar: string;
    rating: number;
  };
  startDate: string;
  endDate: string;
  budget: number;
  spent: number;
}

interface Meeting {
  id: number;
  title: string;
  date: string;
  time: string;
  duration: number;
  type: "video" | "phone" | "in-person";
  participants: {
    id: number;
    name: string;
    avatar: string;
    role: string;
  }[];
  link?: string;
  location?: string;
}

interface Invoice {
  id: number;
  projectId: number;
  projectTitle: string;
  amount: number;
  date: string;
  dueDate: string;
  status: "paid" | "pending" | "overdue";
}

interface Resource {
  id: number;
  title: string;
  type: "document" | "video" | "course" | "article";
  category: string;
  thumbnail: string;
  date: string;
}

// State
const isLoading = ref(true);
const activeProjects = ref<Project[]>([]);
const upcomingMeetings = ref<Meeting[]>([]);
const recentInvoices = ref<Invoice[]>([]);
const recommendedResources = ref<Resource[]>([]);

// Fetch data (mock)
const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    // Simulate API calls
    await Promise.all([
      fetchProjects(),
      fetchMeetings(),
      fetchInvoices(),
      fetchResources(),
    ]);
  } catch (error) {
    console.error("Error fetching dashboard data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch projects (mock)
const fetchProjects = async () => {
  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 600));

  activeProjects.value = [
    {
      id: 1,
      title: "E-Commerce Platform Redesign",
      description:
        "Modernizing the user interface and improving the checkout experience",
      status: "active",
      progress: 75,
      consultant: {
        id: 101,
        name: "Sarah Parker",
        avatar: "/api/placeholder/40/40",
        rating: 4.9,
      },
      startDate: "2025-03-10",
      endDate: "2025-05-15",
      budget: 12000,
      spent: 9000,
    },
    {
      id: 2,
      title: "API Integration for Payment Gateway",
      description:
        "Implementing new payment methods and optimizing transaction flow",
      status: "active",
      progress: 40,
      consultant: {
        id: 102,
        name: "Alex Thompson",
        avatar: "/api/placeholder/40/40",
        rating: 4.7,
      },
      startDate: "2025-04-05",
      endDate: "2025-05-30",
      budget: 8500,
      spent: 3400,
    },
    {
      id: 3,
      title: "Mobile App Development",
      description: "Creating iOS and Android apps with shared codebase",
      status: "pending",
      progress: 10,
      consultant: {
        id: 103,
        name: "Jenna Williams",
        avatar: "/api/placeholder/40/40",
        rating: 4.8,
      },
      startDate: "2025-04-20",
      endDate: "2025-07-15",
      budget: 24000,
      spent: 2400,
    },
  ];
};

// Fetch meetings (mock)
const fetchMeetings = async () => {
  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 500));

  upcomingMeetings.value = [
    {
      id: 1,
      title: "E-Commerce Project Weekly Sync",
      date: "2025-04-17",
      time: "10:00 AM",
      duration: 60,
      type: "video",
      participants: [
        {
          id: 101,
          name: "Sarah Parker",
          avatar: "/api/placeholder/40/40",
          role: "Lead Consultant",
        },
        {
          id: 201,
          name: "Michael Roberts",
          avatar: "/api/placeholder/40/40",
          role: "Client",
        },
        {
          id: 301,
          name: "David Chen",
          avatar: "/api/placeholder/40/40",
          role: "Project Manager",
        },
      ],
      link: "https://meet.example.com/abcd1234",
    },
    {
      id: 2,
      title: "Payment Gateway Requirements Review",
      date: "2025-04-19",
      time: "2:30 PM",
      duration: 90,
      type: "video",
      participants: [
        {
          id: 102,
          name: "Alex Thompson",
          avatar: "/api/placeholder/40/40",
          role: "Backend Developer",
        },
        {
          id: 201,
          name: "Michael Roberts",
          avatar: "/api/placeholder/40/40",
          role: "Client",
        },
        {
          id: 302,
          name: "Lisa Johnson",
          avatar: "/api/placeholder/40/40",
          role: "Financial Advisor",
        },
      ],
      link: "https://meet.example.com/efgh5678",
    },
  ];
};

// Fetch invoices (mock)
const fetchInvoices = async () => {
  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 400));

  recentInvoices.value = [
    {
      id: 1001,
      projectId: 1,
      projectTitle: "E-Commerce Platform Redesign",
      amount: 3000,
      date: "2025-04-01",
      dueDate: "2025-04-15",
      status: "paid",
    },
    {
      id: 1002,
      projectId: 2,
      projectTitle: "API Integration for Payment Gateway",
      amount: 2000,
      date: "2025-04-05",
      dueDate: "2025-04-20",
      status: "pending",
    },
    {
      id: 1003,
      projectId: 3,
      projectTitle: "Mobile App Development",
      amount: 5000,
      date: "2025-04-10",
      dueDate: "2025-04-25",
      status: "pending",
    },
  ];
};

// Fetch resources (mock)
const fetchResources = async () => {
  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 350));

  recommendedResources.value = [
    {
      id: 501,
      title: "Mobile UX Design Best Practices",
      type: "course",
      category: "Design",
      thumbnail: "/api/placeholder/120/80",
      date: "2025-03-25",
    },
    {
      id: 502,
      title: "Payment Gateway Security Guide",
      type: "document",
      category: "Development",
      thumbnail: "/api/placeholder/120/80",
      date: "2025-04-02",
    },
    {
      id: 503,
      title: "E-Commerce Conversion Optimization",
      type: "video",
      category: "Marketing",
      thumbnail: "/api/placeholder/120/80",
      date: "2025-03-18",
    },
    {
      id: 504,
      title: "API Integration Strategies",
      type: "article",
      category: "Development",
      thumbnail: "/api/placeholder/120/80",
      date: "2025-04-05",
    },
  ];
};

// Format currency with dollar sign
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

// Format date to readable format
const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = {
    year: "numeric",
    month: "short",
    day: "numeric",
  };
  return new Date(dateString).toLocaleDateString("en-US", options);
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
    case "paid":
      return "bg-green-100 text-green-800";
    case "overdue":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get meeting type icon
const getMeetingTypeIcon = (type: string) => {
  switch (type) {
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
        <path d="M14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "phone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
      </svg>`;
    case "in-person":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get resource type icon
const getResourceTypeIcon = (type: string) => {
  switch (type) {
    case "document":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
        <path d="M14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "course":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
      </svg>`;
    case "article":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Calculate budget percentage used
const getBudgetPercentage = (spent: number, budget: number) => {
  return Math.round((spent / budget) * 100);
};

// Lifecycle hooks
onMounted(() => {
  fetchDashboardData();
});
</script>

<template>
  <div>
    <!-- Welcome section -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Welcome back, Michael!</h1>
      <p class="text-gray-600">
        Here's an overview of your projects and upcoming activities.
      </p>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      ></div>
    </div>

    <div v-else>
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Active Projects -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Active Projects</h2>
            <div
              class="bg-indigo-100 text-indigo-800 text-lg font-bold h-10 w-10 rounded-full flex items-center justify-center"
            >
              {{ activeProjects.filter((p) => p.status === "active").length }}
            </div>
          </div>
          <div class="space-y-2">
            <div
              v-for="project in activeProjects
                .filter((p) => p.status === 'active')
                .slice(0, 2)"
              :key="project.id"
              class="flex justify-between items-center"
            >
              <span class="text-gray-600 truncate">{{ project.title }}</span>
              <span
                class="text-sm font-medium bg-green-100 text-green-800 py-0.5 px-2 rounded-full"
                >{{ project.progress }}%</span
              >
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-100">
            <NuxtLink
              to="/client/projects"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
            >
              View all projects
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

        <!-- Upcoming Meetings -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
              Upcoming Meetings
            </h2>
            <div
              class="bg-green-100 text-green-800 text-lg font-bold h-10 w-10 rounded-full flex items-center justify-center"
            >
              {{ upcomingMeetings.length }}
            </div>
          </div>
          <div class="space-y-2">
            <div
              v-for="meeting in upcomingMeetings.slice(0, 2)"
              :key="meeting.id"
              class="flex justify-between items-center"
            >
              <span class="text-gray-600 truncate">{{ meeting.title }}</span>
              <span class="text-sm font-medium">{{
                formatDate(meeting.date)
              }}</span>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-100">
            <NuxtLink
              to="/client/meetings"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
            >
              View calendar
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

        <!-- Pending Invoices -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
              Pending Invoices
            </h2>
            <div
              class="bg-amber-100 text-amber-800 text-lg font-bold h-10 w-10 rounded-full flex items-center justify-center"
            >
              {{ recentInvoices.filter((i) => i.status === "pending").length }}
            </div>
          </div>
          <div class="space-y-2">
            <div
              v-for="invoice in recentInvoices
                .filter((i) => i.status === 'pending')
                .slice(0, 2)"
              :key="invoice.id"
              class="flex justify-between items-center"
            >
              <span class="text-gray-600 truncate">{{
                invoice.projectTitle
              }}</span>
              <span class="text-sm font-medium">{{
                formatCurrency(invoice.amount)
              }}</span>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-gray-100">
            <NuxtLink
              to="/client/payments"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
            >
              View all invoices
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

      <!-- Projects Overview -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">Current Projects</h2>
          <NuxtLink
            to="/client/projects/create"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            New Project
          </NuxtLink>
        </div>

        <div
          class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden"
        >
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
                    Progress
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="project in activeProjects"
                  :key="project.id"
                  class="hover:bg-gray-50 transition-colors"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col">
                      <div class="text-sm font-medium text-gray-900">
                        {{ project.title }}
                      </div>
                      <div class="text-sm text-gray-500 truncate max-w-xs">
                        {{ project.description }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <img
                          :src="project.consultant.avatar"
                          :alt="project.consultant.name"
                          class="h-8 w-8 rounded-full"
                        />
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ project.consultant.name }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500">
                          <div class="flex text-yellow-400 mr-1">
                            <svg
                              v-for="i in 5"
                              :key="i"
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-3 w-3"
                              :class="{
                                'text-gray-300': i > project.consultant.rating,
                              }"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                              />
                            </svg>
                          </div>
                          {{ project.consultant.rating }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="getStatusBadgeClasses(project.status)"
                    >
                      {{ project.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-medium">
                      {{ formatCurrency(project.spent) }} /
                      {{ formatCurrency(project.budget) }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ getBudgetPercentage(project.spent, project.budget) }}%
                      used
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                      <div
                        class="bg-indigo-600 h-2.5 rounded-full"
                        :style="{ width: project.progress + '%' }"
                      ></div>
                    </div>
                    <div class="text-xs text-gray-500 text-right">
                      {{ project.progress }}%
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Two-Column Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - Upcoming Meetings -->
        <div class="md:col-span-2">
          <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-gray-900">Upcoming Meetings</h2>
              <NuxtLink
                to="/client/meetings/schedule"
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
              >
                Schedule Meeting
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </NuxtLink>
            </div>

            <div class="space-y-4">
              <div
                v-for="meeting in upcomingMeetings"
                :key="meeting.id"
                class="flex border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
              >
                <!-- Date column -->
                <div
                  class="bg-indigo-50 text-center p-4 w-24 flex flex-col justify-center"
                >
                  <div class="text-gray-500 text-sm">
                    {{
                      new Date(meeting.date).toLocaleDateString("en-US", {
                        weekday: "short",
                      })
                    }}
                  </div>
                  <div class="text-2xl font-bold text-indigo-700">
                    {{ new Date(meeting.date).getDate() }}
                  </div>
                  <div class="text-gray-500 text-sm">
                    {{
                      new Date(meeting.date).toLocaleDateString("en-US", {
                        month: "short",
                      })
                    }}
                  </div>
                </div>

                <!-- Meeting details -->
                <div class="flex-1 p-4">
                  <div class="flex items-start justify-between">
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900">
                        {{ meeting.title }}
                      </h3>
                      <div class="flex items-center mt-1">
                        <span
                          v-html="getMeetingTypeIcon(meeting.type)"
                          class="mr-1"
                        ></span>
                        <span class="text-sm text-gray-600 capitalize"
                          >{{ meeting.type }} Meeting</span
                        >
                        <span class="text-gray-400 mx-2">•</span>
                        <span class="text-sm text-gray-600">{{
                          meeting.time
                        }}</span>
                        <span class="text-gray-400 mx-2">•</span>
                        <span class="text-sm text-gray-600"
                          >{{ meeting.duration }} min</span
                        >
                      </div>
                    </div>
                    <div>
                      <a
                        v-if="meeting.link"
                        :href="meeting.link"
                        target="_blank"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
                      >
                        Join
                      </a>
                    </div>
                  </div>

                  <!-- Participants -->
                  <div class="mt-3">
                    <div class="text-xs font-medium text-gray-500 mb-2">
                      PARTICIPANTS
                    </div>
                    <div class="flex -space-x-2 overflow-hidden">
                      <div
                        v-for="participant in meeting.participants"
                        :key="participant.id"
                        class="relative group"
                      >
                        <img
                          :src="participant.avatar"
                          :alt="participant.name"
                          class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                        />
                        <div
                          class="absolute bottom-0 inset-x-0 transform translate-y-full mt-2 hidden group-hover:block z-10"
                        >
                          <div
                            class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap"
                          >
                            {{ participant.name }}
                            <span class="text-gray-400"
                              >({{ participant.role }})</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 text-center" v-if="upcomingMeetings.length === 0">
              <p class="text-gray-500">No upcoming meetings scheduled.</p>
            </div>
          </div>
        </div>

        <!-- Right Column - Resources and Recent Invoices -->
        <div class="space-y-6">
          <!-- Recommended Resources -->
          <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Recommended Resources
            </h2>

            <div class="space-y-4">
              <div
                v-for="resource in recommendedResources.slice(0, 3)"
                :key="resource.id"
                class="flex space-x-3 items-center hover:bg-gray-50 p-2 rounded-lg transition-colors"
              >
                <img
                  :src="resource.thumbnail"
                  :alt="resource.title"
                  class="w-12 h-12 object-cover rounded"
                />
                <div>
                  <div class="flex items-center">
                    <span
                      v-html="getResourceTypeIcon(resource.type)"
                      class="mr-1"
                    ></span>
                    <span class="text-xs font-medium uppercase text-gray-500">{{
                      resource.category
                    }}</span>
                  </div>
                  <h3 class="text-sm font-medium text-gray-900 line-clamp-1">
                    {{ resource.title }}
                  </h3>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200">
              <NuxtLink
                to="/client/learning"
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center justify-center"
              >
                View all resources
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

          <!-- Recent Invoices -->
          <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Recent Invoices
            </h2>

            <div class="space-y-3">
              <div
                v-for="invoice in recentInvoices.slice(0, 3)"
                :key="invoice.id"
                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0"
              >
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ formatCurrency(invoice.amount) }}
                  </div>
                  <div class="text-xs text-gray-500">
                    Due {{ formatDate(invoice.dueDate) }}
                  </div>
                </div>
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                  :class="getStatusBadgeClasses(invoice.status)"
                >
                  {{ invoice.status }}
                </span>
              </div>
            </div>

            <div class="mt-4 pt-2">
              <NuxtLink
                to="/client/payments"
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center justify-center"
              >
                View all invoices
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
  </div>
</template>

<style scoped>
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

/* For truncating text */
.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}

/* Transitions */
.transition-shadow {
  transition-property: box-shadow;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
