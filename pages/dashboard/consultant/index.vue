<!-- pages/consultant/dashboard/index.vue -->
<script setup lang="ts">
import { ref, onMounted, computed } from "vue";

definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Dashboard - Consultant Portal",
});

// Dashboard data states
const isLoading = ref(true);

// Mock data - in a real app, these would come from API calls
const dashboardStats = ref({
  activeProjects: 3,
  pendingProposals: 2,
  upcomingMeetings: 4,
  pendingInvoices: 2,
  totalEarnings: 24650,
  monthlyEarnings: 6500,
  totalHours: 187,
  monthlyHours: 42,
});

// Revenue data for chart
const revenueData = ref([
  { month: "Jan", amount: 4200 },
  { month: "Feb", amount: 3800 },
  { month: "Mar", amount: 5100 },
  { month: "Apr", amount: 6500 },
  { month: "May", amount: 0 }, // Future months
  { month: "Jun", amount: 0 },
  { month: "Jul", amount: 0 },
  { month: "Aug", amount: 0 },
  { month: "Sep", amount: 0 },
  { month: "Oct", amount: 0 },
  { month: "Nov", amount: 0 },
  { month: "Dec", amount: 0 },
]);

// Active projects
const activeProjects = ref([
  {
    id: 1,
    title: "E-commerce Platform Redesign",
    client: "RetailTech Inc.",
    progress: 45,
    dueDate: "2025-06-30",
    budget: 24000,
    spent: 10800,
  },
  {
    id: 2,
    title: "Mobile App Development",
    client: "FinCore Solutions",
    progress: 28,
    dueDate: "2025-08-15",
    budget: 36000,
    spent: 10080,
  },
  {
    id: 3,
    title: "Supply Chain Management System",
    client: "Global Logistics Ltd.",
    progress: 65,
    dueDate: "2025-07-20",
    budget: 42000,
    spent: 27300,
  },
]);

// Upcoming meetings
const upcomingMeetings = ref([
  {
    id: 1,
    title: "Project Kickoff",
    with: "MediCare Health Systems",
    date: "2025-04-18T14:00:00Z",
    duration: 60,
    type: "video",
    isConfirmed: true,
  },
  {
    id: 2,
    title: "Weekly Progress Update",
    with: "RetailTech Inc.",
    date: "2025-04-19T10:30:00Z",
    duration: 30,
    type: "video",
    isConfirmed: true,
  },
  {
    id: 3,
    title: "Requirements Gathering",
    with: "DataInsight Corp",
    date: "2025-04-20T15:00:00Z",
    duration: 90,
    type: "in-person",
    isConfirmed: false,
  },
  {
    id: 4,
    title: "Budget Discussion",
    with: "FinCore Solutions",
    date: "2025-04-22T11:00:00Z",
    duration: 45,
    type: "video",
    isConfirmed: true,
  },
]);

// Recent notifications
const notifications = ref([
  {
    id: 1,
    type: "message",
    content: "New message from John Morrison",
    time: "10 minutes ago",
    isRead: false,
  },
  {
    id: 2,
    type: "payment",
    content: "Payment of $5,400 received from RetailTech Inc.",
    time: "2 hours ago",
    isRead: false,
  },
  {
    id: 3,
    type: "project",
    content: "New milestone completed on Supply Chain Management System",
    time: "5 hours ago",
    isRead: true,
  },
  {
    id: 4,
    type: "review",
    content: "New 5-star review from Sarah Williams",
    time: "1 day ago",
    isRead: true,
  },
  {
    id: 5,
    type: "request",
    content: "New project request from DataInsight Corp",
    time: "2 days ago",
    isRead: true,
  },
]);

// Pending invoices
const pendingInvoices = ref([
  {
    id: 1,
    client: "RetailTech Inc.",
    amount: 5400,
    dueDate: "2025-04-25",
    status: "pending",
    overdue: false,
  },
  {
    id: 2,
    client: "FinCore Solutions",
    amount: 3600,
    dueDate: "2025-04-20",
    status: "pending",
    overdue: false,
  },
]);

// Fetch dashboard data
const fetchDashboardData = async () => {
  isLoading.value = true;

  try {
    // Simulate API calls
    await new Promise((resolve) => setTimeout(resolve, 800));

    // In a real app, you would fetch data from the backend here
    // dashboardStats.value = await fetchStats();
    // activeProjects.value = await fetchActiveProjects();
    // etc.
  } catch (error) {
    console.error("Error fetching dashboard data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Calculate days remaining until a date
const getDaysRemaining = (dateString: string) => {
  const today = new Date();
  const dueDate = new Date(dateString);
  const timeDiff = dueDate.getTime() - today.getTime();
  const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

  return daysDiff;
};

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    weekday: "short",
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

// Format time
const formatTime = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Check if a meeting is today
const isMeetingToday = (dateString: string) => {
  const today = new Date();
  const meetingDate = new Date(dateString);

  return (
    today.getDate() === meetingDate.getDate() &&
    today.getMonth() === meetingDate.getMonth() &&
    today.getFullYear() === meetingDate.getFullYear()
  );
};

// Get the appropriate icon for notification type
const getNotificationIcon = (type: string) => {
  switch (type) {
    case "message":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
      </svg>`;
    case "payment":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "project":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "review":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>`;
    case "request":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get meeting type icon
const getMeetingTypeIcon = (type: string) => {
  switch (type) {
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "in-person":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
      </svg>`;
    case "phone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get the total notification count
const unreadNotificationCount = computed(() => {
  return notifications.value.filter((n) => !n.isRead).length;
});

// Calculate earning growth percentage from previous month
const earningGrowth = computed(() => {
  const currentMonth = revenueData.value[3].amount; // April
  const previousMonth = revenueData.value[2].amount; // March

  if (previousMonth === 0) return 100;

  return Math.round(((currentMonth - previousMonth) / previousMonth) * 100);
});

// Get classes for earning growth indicator
const earningGrowthClasses = computed(() => {
  return earningGrowth.value >= 0
    ? "text-green-600 bg-green-100"
    : "text-red-600 bg-red-100";
});

// Calculate hours growth percentage from previous month
const hoursGrowth = computed(() => {
  // Mock data, in a real app this would be calculated from actual data
  return 8;
});

// Get classes for hours growth indicator
const hoursGrowthClasses = computed(() => {
  return hoursGrowth.value >= 0
    ? "text-green-600 bg-green-100"
    : "text-red-600 bg-red-100";
});

// Mark a notification as read
const markAsRead = (id: number) => {
  const notification = notifications.value.find((n) => n.id === id);
  if (notification) {
    notification.isRead = true;
  }
};

// Mark all notifications as read
const markAllAsRead = () => {
  notifications.value.forEach((notification) => {
    notification.isRead = true;
  });
};

// Load dashboard data on mount
onMounted(() => {
  fetchDashboardData();
});
</script>

<template>
  <div>
    <!-- Dashboard Header -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Dashboard</h1>
          <p class="text-gray-600">
            Welcome back! Here's an overview of your consultancy activity
          </p>
        </div>
        <div class="mt-4 md:mt-0 space-x-2">
          <NuxtLink
            to="/consultant/projects/create"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
                clip-rule="evenodd"
              />
            </svg>
            New Proposal
          </NuxtLink>
          <NuxtLink
            to="/consultant/appointments/schedule"
            class="inline-flex items-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                clip-rule="evenodd"
              />
            </svg>
            Schedule Meeting
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      ></div>
    </div>

    <div v-else>
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Projects -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm font-medium">Active Projects</h3>
            <div class="bg-indigo-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
                <path
                  d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"
                />
              </svg>
            </div>
          </div>
          <div class="flex items-end">
            <div class="text-3xl font-bold text-gray-900">
              {{ dashboardStats.activeProjects }}
            </div>
            <div class="text-sm text-gray-500 ml-2 mb-1">projects</div>
          </div>
          <div class="mt-4">
            <NuxtLink
              to="/consultant/projects?tab=active"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              View all projects →
            </NuxtLink>
          </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm font-medium">Upcoming Meetings</h3>
            <div class="bg-green-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-600"
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
          <div class="flex items-end">
            <div class="text-3xl font-bold text-gray-900">
              {{ dashboardStats.upcomingMeetings }}
            </div>
            <div class="text-sm text-gray-500 ml-2 mb-1">scheduled</div>
          </div>
          <div class="mt-4">
            <NuxtLink
              to="/consultant/appointments"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              View calendar →
            </NuxtLink>
          </div>
        </div>

        <!-- Monthly Earnings -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm font-medium">Monthly Earnings</h3>
            <div class="bg-purple-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-purple-600"
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
          </div>
          <div class="flex items-end">
            <div class="text-3xl font-bold text-gray-900">
              {{ formatCurrency(dashboardStats.monthlyEarnings) }}
            </div>
            <div
              class="text-sm px-2 py-0.5 rounded ml-2 mb-1"
              :class="earningGrowthClasses"
            >
              <span class="font-bold"
                >{{ earningGrowth > 0 ? "+" : "" }}{{ earningGrowth }}%</span
              >
            </div>
          </div>
          <div class="mt-4">
            <NuxtLink
              to="/consultant/invoices"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              View earnings →
            </NuxtLink>
          </div>
        </div>

        <!-- Monthly Hours -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm font-medium">Monthly Hours</h3>
            <div class="bg-blue-100 rounded-full p-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-blue-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
          <div class="flex items-end">
            <div class="text-3xl font-bold text-gray-900">
              {{ dashboardStats.monthlyHours }}
            </div>
            <div
              class="text-sm px-2 py-0.5 rounded ml-2 mb-1"
              :class="hoursGrowthClasses"
            >
              <span class="font-bold"
                >{{ hoursGrowth > 0 ? "+" : "" }}{{ hoursGrowth }}%</span
              >
            </div>
          </div>
          <div class="mt-4">
            <NuxtLink
              to="/consultant/time-tracking"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              View hours →
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Projects -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Active Projects -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">
                  Active Projects
                </h2>
                <NuxtLink
                  to="/consultant/projects"
                  class="text-sm text-indigo-600 hover:text-indigo-800"
                >
                  View all
                </NuxtLink>
              </div>
            </div>

            <div class="divide-y divide-gray-200">
              <div
                v-for="project in activeProjects"
                :key="project.id"
                class="p-6 hover:bg-gray-50 transition-colors"
              >
                <div
                  class="flex flex-col md:flex-row justify-between md:items-center space-y-4 md:space-y-0"
                >
                  <div class="flex-1">
                    <h3 class="text-base font-medium text-gray-900 mb-1">
                      {{ project.title }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ project.client }}</p>

                    <!-- Progress and due date -->
                    <div class="mt-3 space-y-2">
                      <div>
                        <div class="flex justify-between mb-1">
                          <span class="text-xs font-medium text-gray-700"
                            >Progress</span
                          >
                          <span class="text-xs font-medium text-gray-700"
                            >{{ project.progress }}%</span
                          >
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                          <div
                            class="bg-indigo-600 h-2 rounded-full"
                            :style="{ width: `${project.progress}%` }"
                          ></div>
                        </div>
                      </div>

                      <div class="flex justify-between text-xs text-gray-500">
                        <span>
                          <span class="font-medium text-gray-700">Budget:</span>
                          {{ formatCurrency(project.budget) }}
                        </span>
                        <span>
                          <span class="font-medium text-gray-700">Spent:</span>
                          {{ formatCurrency(project.spent) }}
                        </span>
                      </div>

                      <div class="flex justify-between text-xs text-gray-500">
                        <span>
                          <span class="font-medium text-gray-700">Due:</span>
                          {{ formatDate(project.dueDate) }}
                        </span>
                        <span
                          class="text-indigo-600 font-medium"
                          v-if="getDaysRemaining(project.dueDate) > 0"
                        >
                          {{ getDaysRemaining(project.dueDate) }} days left
                        </span>
                        <span
                          class="text-red-600 font-medium"
                          v-else-if="getDaysRemaining(project.dueDate) < 0"
                        >
                          {{ Math.abs(getDaysRemaining(project.dueDate)) }} days
                          overdue
                        </span>
                        <span class="text-orange-600 font-medium" v-else>
                          Due today
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Action buttons -->
                  <div class="flex space-x-2">
                    <NuxtLink
                      :to="`/consultant/projects/${project.id}`"
                      class="inline-flex items-center justify-center p-2 border border-transparent rounded-full text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                      :to="`/consultant/projects/${project.id}/edit`"
                      class="inline-flex items-center justify-center p-2 border border-transparent rounded-full text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
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
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty state if no projects -->
            <div
              v-if="activeProjects.length === 0"
              class="p-8 text-center text-gray-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-12 w-12 mx-auto text-gray-400 mb-4"
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
              <p class="mb-4">You don't have any active projects</p>
              <NuxtLink
                to="/consultant/projects/create"
                class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd"
                  />
                </svg>
                Create new project
              </NuxtLink>
            </div>
          </div>

          <!-- Revenue chart -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-lg font-semibold text-gray-900">
                Revenue Overview
              </h2>
              <div class="text-sm text-gray-500">
                <span class="font-medium text-gray-700">YTD Earnings:</span>
                {{ formatCurrency(dashboardStats.totalEarnings) }}
              </div>
            </div>

            <!-- Chart placeholder -->
            <div class="h-64 relative">
              <!-- Basic bar chart illustration -->
              <div class="absolute inset-0 flex items-end px-8">
                <div
                  v-for="(month, index) in revenueData.slice(0, 4)"
                  :key="index"
                  class="w-1/6 mx-2 group"
                >
                  <div
                    class="bg-indigo-500 hover:bg-indigo-600 transition-all rounded-t-sm relative cursor-pointer group"
                    :class="index === 3 ? 'bg-indigo-700' : ''"
                    :style="{
                      height: `${(month.amount / 7000) * 100}%`,
                    }"
                  >
                    <!-- Tooltip -->
                    <div
                      class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-2 py-1 rounded text-xs font-medium opacity-0 group-hover:opacity-100 transition whitespace-nowrap"
                    >
                      {{ formatCurrency(month.amount) }}
                    </div>
                  </div>
                  <div class="text-xs font-medium text-center mt-2">
                    {{ month.month }}
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 text-center">
              <NuxtLink
                to="/consultant/analytics"
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
              >
                View detailed analytics →
              </NuxtLink>
            </div>
          </div>
        </div>

        <!-- Right Column - Upcoming & Notifications -->
        <div class="space-y-6">
          <!-- Upcoming meetings -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">
                  Upcoming Meetings
                </h2>
                <NuxtLink
                  to="/consultant/appointments"
                  class="text-sm text-indigo-600 hover:text-indigo-800"
                >
                  View all
                </NuxtLink>
              </div>
            </div>

            <div class="divide-y divide-gray-200">
              <div
                v-for="meeting in upcomingMeetings.slice(0, 3)"
                :key="meeting.id"
                class="p-4 hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-start">
                  <div
                    class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3"
                  >
                    <span v-html="getMeetingTypeIcon(meeting.type)"></span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                      <h3 class="text-sm font-medium text-gray-900 truncate">
                        {{ meeting.title }}
                      </h3>
                      <span
                        v-if="isMeetingToday(meeting.date)"
                        class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-medium"
                      >
                        Today
                      </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                      with <span class="font-medium">{{ meeting.with }}</span>
                    </p>
                    <div class="flex mt-2 text-xs text-gray-500">
                      <div class="flex items-center mr-3">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-3 w-3 mr-1 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        {{ formatTime(meeting.date) }}
                      </div>
                      <div class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-3 w-3 mr-1 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        {{ formatDate(meeting.date) }}
                      </div>
                    </div>
                  </div>
                  <div class="flex-shrink-0 ml-2">
                    <span
                      v-if="meeting.isConfirmed"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                    >
                      Confirmed
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"
                    >
                      Pending
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- View more meetings link -->
            <div
              v-if="upcomingMeetings.length > 3"
              class="p-4 text-center border-t border-gray-200"
            >
              <NuxtLink
                to="/consultant/appointments"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
              >
                View {{ upcomingMeetings.length - 3 }} more
                {{ upcomingMeetings.length - 3 === 1 ? "meeting" : "meetings" }}
              </NuxtLink>
            </div>

            <!-- Empty state if no meetings -->
            <div
              v-if="upcomingMeetings.length === 0"
              class="p-6 text-center text-gray-500"
            >
              <p>No upcoming meetings scheduled</p>
            </div>
          </div>

          <!-- Notifications -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <div class="flex items-center">
                  <h2 class="text-lg font-semibold text-gray-900">
                    Notifications
                  </h2>
                  <span
                    v-if="unreadNotificationCount > 0"
                    class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full font-medium"
                  >
                    {{ unreadNotificationCount }} new
                  </span>
                </div>
                <button
                  v-if="unreadNotificationCount > 0"
                  @click="markAllAsRead"
                  class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >
                  Mark all as read
                </button>
              </div>
            </div>

            <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
              <div
                v-for="notification in notifications"
                :key="notification.id"
                class="p-4 hover:bg-gray-50 transition-colors"
                :class="{ 'bg-indigo-50': !notification.isRead }"
              >
                <div class="flex items-start">
                  <div
                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3"
                    v-html="getNotificationIcon(notification.type)"
                  ></div>
                  <div class="flex-1 min-w-0">
                    <p
                      class="text-sm text-gray-900"
                      :class="{ 'font-medium': !notification.isRead }"
                    >
                      {{ notification.content }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ notification.time }}
                    </p>
                  </div>
                  <div class="flex-shrink-0 ml-2">
                    <button
                      v-if="!notification.isRead"
                      @click="markAsRead(notification.id)"
                      class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                    >
                      Mark as read
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="notifications.length === 0"
              class="p-6 text-center text-gray-500"
            >
              <p>No notifications</p>
            </div>
          </div>

          <!-- Pending Invoices -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <div class="flex items-center">
                  <h2 class="text-lg font-semibold text-gray-900">
                    Pending Invoices
                  </h2>
                  <span
                    v-if="pendingInvoices.length > 0"
                    class="ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-medium"
                  >
                    {{ pendingInvoices.length }}
                  </span>
                </div>
                <NuxtLink
                  to="/consultant/invoices"
                  class="text-sm text-indigo-600 hover:text-indigo-800"
                >
                  View all
                </NuxtLink>
              </div>
            </div>

            <div class="divide-y divide-gray-200">
              <div
                v-for="invoice in pendingInvoices"
                :key="invoice.id"
                class="p-4 hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ invoice.client }}
                    </p>
                    <div class="flex mt-1 text-xs text-gray-500">
                      <div class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-3 w-3 mr-1 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        Due {{ formatDate(invoice.dueDate) }}
                      </div>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">
                      {{ formatCurrency(invoice.amount) }}
                    </p>
                    <span
                      v-if="invoice.overdue"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1"
                    >
                      Overdue
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1"
                    >
                      Pending
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="pendingInvoices.length === 0"
              class="p-6 text-center text-gray-500"
            >
              <p>No pending invoices</p>
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
</style>
