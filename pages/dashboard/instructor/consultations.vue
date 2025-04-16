<!-- pages/dashboard/instructor/consultations/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";

// Define page layout
definePageMeta({
  layout: "dashboard-instructor",
});

// Set page metadata
useHead({
  title: "My Consultations - Instructor Dashboard",
});

// Types
interface Attendee {
  id: string;
  name: string;
  avatar: string;
  role: string;
  email?: string;
}

interface Consultation {
  id: string;
  title: string;
  description: string;
  startTime: string; // ISO string
  endTime: string; // ISO string
  status: "scheduled" | "completed" | "cancelled" | "pending";
  type: "one-on-one" | "group" | "workshop" | "review";
  category: string;
  attendees: Attendee[];
  notes?: string;
  meetingUrl?: string;
  recordingUrl?: string;
  materials?: {
    id: string;
    name: string;
    type: string;
    url: string;
  }[];
  price: number;
  paid: boolean;
  rating?: number;
  feedback?: string;
}

// State variables
const consultations = ref<Consultation[]>([]);
const filteredConsultations = ref<Consultation[]>([]);
const activeTab = ref("upcoming");
const searchQuery = ref("");
const isLoading = ref(true);
const sortOption = ref("date");
const showConsultationModal = ref(false);
const showRescheduleModal = ref(false);
const showCancelModal = ref(false);
const selectedConsultation = ref<Consultation | null>(null);
const viewMode = ref<"calendar" | "list">("list");
const currentMonth = ref(new Date());

// Consultation statistics for overview
const consultationStats = computed(() => {
  return {
    upcoming: consultations.value.filter((c) => c.status === "scheduled")
      .length,
    completed: consultations.value.filter((c) => c.status === "completed")
      .length,
    cancelled: consultations.value.filter((c) => c.status === "cancelled")
      .length,
    pending: consultations.value.filter((c) => c.status === "pending").length,
    totalEarnings: consultations.value
      .filter((c) => c.status === "completed" && c.paid)
      .reduce((sum, curr) => sum + curr.price, 0),
    averageRating: calculateAverageRating(),
  };
});

// Calendar data for calendar view
const calendarDays = computed(() => {
  // This would generate the days for the current month view
  // Simplified for now
  const year = currentMonth.value.getFullYear();
  const month = currentMonth.value.getMonth();
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  // Create array with all days in month
  const days = [];
  for (let i = 1; i <= lastDay.getDate(); i++) {
    days.push(new Date(year, month, i));
  }

  return days;
});

// Calculate average rating
function calculateAverageRating(): number {
  const completedWithRating = consultations.value.filter(
    (c) => c.status === "completed" && c.rating !== undefined
  );

  if (completedWithRating.length === 0) return 0;

  const totalRating = completedWithRating.reduce(
    (sum, curr) => sum + (curr.rating || 0),
    0
  );
  return parseFloat((totalRating / completedWithRating.length).toFixed(1));
}

// Fetch consultation data
const fetchConsultations = async () => {
  isLoading.value = true;

  try {
    // Simulate API call with timeout
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    consultations.value = [
      {
        id: "cons-001",
        title: "Web Development Career Guidance",
        description:
          "One-on-one session to discuss career paths in web development and create a learning roadmap.",
        startTime: "2025-04-20T14:00:00Z",
        endTime: "2025-04-20T15:00:00Z",
        status: "scheduled",
        type: "one-on-one",
        category: "Career Guidance",
        attendees: [
          {
            id: "user-123",
            name: "Sarah Johnson",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "sarah.j@example.com",
          },
        ],
        notes:
          "Prepare learning roadmap template, discuss front-end vs back-end specialization",
        meetingUrl: "https://zoom.us/meeting/123456789",
        materials: [
          {
            id: "mat-001",
            name: "Web Developer Roadmap 2025",
            type: "pdf",
            url: "/documents/web-dev-roadmap.pdf",
          },
        ],
        price: 75.0,
        paid: true,
      },
      {
        id: "cons-002",
        title: "React Project Code Review",
        description:
          "Code review session for e-commerce project built with React and Redux.",
        startTime: "2025-04-22T10:00:00Z",
        endTime: "2025-04-22T11:30:00Z",
        status: "scheduled",
        type: "review",
        category: "Code Review",
        attendees: [
          {
            id: "user-456",
            name: "Michael Chen",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "michael.c@example.com",
          },
        ],
        notes:
          "Focus on state management, component structure, and performance optimization",
        meetingUrl: "https://zoom.us/meeting/987654321",
        price: 120.0,
        paid: true,
      },
      {
        id: "cons-003",
        title: "JavaScript Fundamentals Workshop",
        description:
          "Group workshop covering advanced JavaScript concepts including closures, promises, and async/await.",
        startTime: "2025-04-25T15:00:00Z",
        endTime: "2025-04-25T17:00:00Z",
        status: "scheduled",
        type: "workshop",
        category: "Workshop",
        attendees: [
          {
            id: "user-789",
            name: "Emily Davis",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "emily.d@example.com",
          },
          {
            id: "user-101",
            name: "James Wilson",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "james.w@example.com",
          },
          {
            id: "user-102",
            name: "Sophia Martinez",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "sophia.m@example.com",
          },
        ],
        notes: "Prepare code examples for promises and async patterns",
        meetingUrl: "https://zoom.us/meeting/567890123",
        materials: [
          {
            id: "mat-002",
            name: "Advanced JS Slides",
            type: "pdf",
            url: "/documents/advanced-js-slides.pdf",
          },
          {
            id: "mat-003",
            name: "Practice Exercises",
            type: "zip",
            url: "/documents/js-exercises.zip",
          },
        ],
        price: 250.0,
        paid: true,
      },
      {
        id: "cons-004",
        title: "Node.js API Development",
        description:
          "One-on-one session to troubleshoot REST API implementation issues with Node.js and Express.",
        startTime: "2025-04-10T13:00:00Z",
        endTime: "2025-04-10T14:00:00Z",
        status: "completed",
        type: "one-on-one",
        category: "Troubleshooting",
        attendees: [
          {
            id: "user-112",
            name: "David Thompson",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "david.t@example.com",
          },
        ],
        notes: "Resolved authentication issues and optimized database queries",
        meetingUrl: "https://zoom.us/meeting/234567890",
        recordingUrl: "https://zoom.us/rec/234567890",
        price: 85.0,
        paid: true,
        rating: 5,
        feedback:
          "Alex was incredibly helpful and solved my API authentication issues quickly. Great explanations and follow-up resources!",
      },
      {
        id: "cons-005",
        title: "Portfolio Website Review",
        description:
          "Review session for personal portfolio website with feedback on design, code, and user experience.",
        startTime: "2025-04-05T11:00:00Z",
        endTime: "2025-04-05T12:00:00Z",
        status: "completed",
        type: "review",
        category: "Portfolio Review",
        attendees: [
          {
            id: "user-113",
            name: "Olivia Baker",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "olivia.b@example.com",
          },
        ],
        notes:
          "Provided feedback on responsive design, accessibility improvements, and SEO optimization",
        meetingUrl: "https://zoom.us/meeting/345678901",
        recordingUrl: "https://zoom.us/rec/345678901",
        price: 65.0,
        paid: true,
        rating: 4.5,
        feedback:
          "Great feedback on my portfolio! Already implemented many of the suggestions and my site looks much better.",
      },
      {
        id: "cons-006",
        title: "Database Design Consultation",
        description:
          "Consultation on database schema design for social networking application.",
        startTime: "2025-04-15T16:00:00Z",
        endTime: "2025-04-15T17:00:00Z",
        status: "cancelled",
        type: "one-on-one",
        category: "Database Design",
        attendees: [
          {
            id: "user-114",
            name: "Ryan Clark",
            avatar: "/api/placeholder/100/100",
            role: "student",
            email: "ryan.c@example.com",
          },
        ],
        notes: "Cancelled by student due to scheduling conflict",
        price: 90.0,
        paid: false,
      },
      {
        id: "cons-007",
        title: "Full-Stack Architecture Planning",
        description:
          "Planning session for e-learning platform architecture using MERN stack.",
        startTime: "2025-03-28T15:00:00Z",
        endTime: "2025-03-28T16:30:00Z",
        status: "completed",
        type: "one-on-one",
        category: "Architecture Planning",
        attendees: [
          {
            id: "user-115",
            name: "Zoe Robinson",
            avatar: "/api/placeholder/100/100",
            role: "client",
            email: "zoe.r@example.com",
          },
        ],
        notes:
          "Drafted architecture diagram, discussed scaling and deployment strategies",
        meetingUrl: "https://zoom.us/meeting/456789012",
        recordingUrl: "https://zoom.us/rec/456789012",
        materials: [
          {
            id: "mat-004",
            name: "Architecture Diagram",
            type: "pdf",
            url: "/documents/architecture-diagram.pdf",
          },
        ],
        price: 150.0,
        paid: true,
        rating: 5,
        feedback:
          "Outstanding session! Alex helped me plan out the entire architecture for my application and identified potential issues I hadn't considered.",
      },
      {
        id: "cons-008",
        title: "Mobile App Development Strategy",
        description:
          "Consultation on hybrid vs native app development approaches for fitness tracking application.",
        startTime: "2025-04-08T09:00:00Z",
        endTime: "2025-04-08T10:00:00Z",
        status: "completed",
        type: "one-on-one",
        category: "Strategy Session",
        attendees: [
          {
            id: "user-116",
            name: "Jessica Kim",
            avatar: "/api/placeholder/100/100",
            role: "client",
            email: "jessica.k@example.com",
          },
        ],
        notes:
          "Discussed React Native vs Flutter options, evaluated requirements and technical constraints",
        meetingUrl: "https://zoom.us/meeting/567890123",
        recordingUrl: "https://zoom.us/rec/567890123",
        price: 95.0,
        paid: true,
        rating: 4,
        feedback:
          "Very informative session that helped me decide which technology to use for my app.",
      },
    ];

    applyFilters();
  } catch (error) {
    console.error("Error fetching consultations:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter consultations based on tab and search query
const applyFilters = () => {
  let results = [...consultations.value];

  // Apply tab filter
  if (activeTab.value === "upcoming") {
    results = results.filter((c) => c.status === "scheduled");
  } else if (activeTab.value === "completed") {
    results = results.filter((c) => c.status === "completed");
  } else if (activeTab.value === "cancelled") {
    results = results.filter((c) => c.status === "cancelled");
  } else if (activeTab.value === "pending") {
    results = results.filter((c) => c.status === "pending");
  }

  // Apply search filter
  if (searchQuery.value.trim() !== "") {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(
      (c) =>
        c.title.toLowerCase().includes(query) ||
        c.description.toLowerCase().includes(query) ||
        c.category.toLowerCase().includes(query) ||
        c.attendees.some((a) => a.name.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  if (sortOption.value === "date") {
    results.sort(
      (a, b) =>
        new Date(a.startTime).getTime() - new Date(b.startTime).getTime()
    );
  } else if (sortOption.value === "price") {
    results.sort((a, b) => b.price - a.price);
  } else if (sortOption.value === "duration") {
    results.sort((a, b) => {
      const durationA =
        new Date(a.endTime).getTime() - new Date(a.startTime).getTime();
      const durationB =
        new Date(b.endTime).getTime() - new Date(b.startTime).getTime();
      return durationB - durationA;
    });
  } else if (sortOption.value === "attendees") {
    results.sort((a, b) => b.attendees.length - a.attendees.length);
  }

  filteredConsultations.value = results;
};

// Format date for display
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    weekday: "short",
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

// Format time for display
const formatTime = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Format date and time
const formatDateTime = (dateString: string) => {
  return `${formatDate(dateString)} at ${formatTime(dateString)}`;
};

// Calculate consultation duration in minutes
const getConsultationDuration = (consultation: Consultation) => {
  const start = new Date(consultation.startTime);
  const end = new Date(consultation.endTime);
  const durationMs = end.getTime() - start.getTime();
  const durationMinutes = Math.round(durationMs / (1000 * 60));

  return durationMinutes;
};

// Format duration for display
const formatDuration = (consultation: Consultation) => {
  const minutes = getConsultationDuration(consultation);
  if (minutes < 60) {
    return `${minutes} min`;
  }

  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;

  if (remainingMinutes === 0) {
    return `${hours} hr`;
  }

  return `${hours} hr ${remainingMinutes} min`;
};

// Format price with currency symbol
const formatPrice = (price: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Get status badge classes based on consultation status
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "scheduled":
      return "bg-green-100 text-green-800";
    case "completed":
      return "bg-blue-100 text-blue-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get consultation type badge classes
const getTypeBadgeClasses = (type: string) => {
  switch (type) {
    case "one-on-one":
      return "bg-indigo-100 text-indigo-800";
    case "group":
      return "bg-purple-100 text-purple-800";
    case "workshop":
      return "bg-amber-100 text-amber-800";
    case "review":
      return "bg-teal-100 text-teal-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Check if a consultation starts today
const isToday = (dateString: string) => {
  const today = new Date();
  const consultationDate = new Date(dateString);

  return (
    today.getDate() === consultationDate.getDate() &&
    today.getMonth() === consultationDate.getMonth() &&
    today.getFullYear() === consultationDate.getFullYear()
  );
};

// View consultation details
const viewConsultation = (consultation: Consultation) => {
  selectedConsultation.value = consultation;
  showConsultationModal.value = true;
};

// Initiate consultation reschedule
const initiateReschedule = (consultation: Consultation) => {
  selectedConsultation.value = consultation;
  showRescheduleModal.value = true;
};

// Confirm consultation cancellation
const confirmCancellation = (consultation: Consultation) => {
  selectedConsultation.value = consultation;
  showCancelModal.value = true;
};

// Send reminder to attendees
const sendReminder = (consultation: Consultation) => {
  // In a real app, this would send an API request
  console.log(`Sending reminder for consultation: ${consultation.id}`);

  // Show success notification
  alert(
    `Reminder sent to ${consultation.attendees.map((a) => a.name).join(", ")}`
  );
};

// Start consultation meeting
const startMeeting = (consultation: Consultation) => {
  if (consultation.meetingUrl) {
    window.open(consultation.meetingUrl, "_blank");
  }
};

// Cancel consultation
const cancelConsultation = () => {
  if (!selectedConsultation.value) return;

  // In a real app, this would send an API request
  const index = consultations.value.findIndex(
    (c) => c.id === selectedConsultation.value?.id
  );
  if (index !== -1) {
    consultations.value[index].status = "cancelled";
    applyFilters();
  }

  showCancelModal.value = false;
  selectedConsultation.value = null;
};

// Switch view mode between list and calendar
const switchViewMode = (mode: "list" | "calendar") => {
  viewMode.value = mode;
};

// Get consultations for a specific day (for calendar view)
const getConsultationsForDay = (day: Date) => {
  return consultations.value.filter((consultation) => {
    const consultationDate = new Date(consultation.startTime);
    return (
      consultationDate.getDate() === day.getDate() &&
      consultationDate.getMonth() === day.getMonth() &&
      consultationDate.getFullYear() === day.getFullYear()
    );
  });
};

// Navigate to previous month in calendar view
const previousMonth = () => {
  const newMonth = new Date(currentMonth.value);
  newMonth.setMonth(newMonth.getMonth() - 1);
  currentMonth.value = newMonth;
};

// Navigate to next month in calendar view
const nextMonth = () => {
  const newMonth = new Date(currentMonth.value);
  newMonth.setMonth(newMonth.getMonth() + 1);
  currentMonth.value = newMonth;
};

// Watch for filter changes and update results
watch([activeTab, searchQuery, sortOption], () => {
  applyFilters();
});

// Initialize data on component mount
onMounted(() => {
  fetchConsultations();
});
</script>

<template>
  <div>
    <!-- Page Header with Stats -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            My Consultations
          </h1>
          <p class="text-gray-600">
            Manage your consultation sessions and track your schedule
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/dashboard/instructor/consultations/create"
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
            <span>Create New Consultation</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Upcoming Sessions</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ consultationStats.upcoming }}
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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">
                Completed Sessions
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ consultationStats.completed }}
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
              <p class="text-sm text-gray-500 font-medium">Total Earnings</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatPrice(consultationStats.totalEarnings) }}
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
                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Average Rating</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ consultationStats.averageRating }}
                <span class="text-sm text-gray-500 font-normal"> / 5</span>
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
            @click="activeTab = 'upcoming'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'upcoming'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Upcoming
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
        </div>

        <!-- Search and sort controls -->
        <div class="flex items-center space-x-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search consultations..."
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
              v-model="sortOption"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="date">Date (Upcoming)</option>
              <option value="price">Price (Highest)</option>
              <option value="duration">Duration (Longest)</option>
              <option value="attendees">Attendees (Most)</option>
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

          <!-- View switcher -->
          <div class="hidden md:flex border rounded-lg overflow-hidden">
            <button
              @click="switchViewMode('list')"
              class="px-3 py-2 transition-colors"
              :class="
                viewMode === 'list'
                  ? 'bg-indigo-600 text-white'
                  : 'bg-white text-gray-500 hover:bg-gray-100'
              "
              aria-label="List view"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <button
              @click="switchViewMode('calendar')"
              class="px-3 py-2 transition-colors"
              :class="
                viewMode === 'calendar'
                  ? 'bg-indigo-600 text-white'
                  : 'bg-white text-gray-500 hover:bg-gray-100'
              "
              aria-label="Calendar view"
            >
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
          <div class="bg-gray-200 h-12 w-12 rounded-full"></div>
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div
      v-else-if="viewMode === 'list' && filteredConsultations.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="consultation in filteredConsultations"
        :key="consultation.id"
        class="p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors"
      >
        <div class="flex flex-col md:flex-row md:items-center">
          <!-- Consultation info -->
          <div
            class="flex-1 flex flex-col md:flex-row md:items-center gap-4 mb-4 md:mb-0"
          >
            <!-- Attendee avatar(s) -->
            <div class="flex-shrink-0">
              <div
                v-if="consultation.attendees.length === 1"
                class="h-12 w-12 rounded-full overflow-hidden"
              >
                <img
                  :src="consultation.attendees[0].avatar"
                  :alt="consultation.attendees[0].name"
                  class="h-full w-full object-cover"
                />
              </div>
              <div v-else class="flex -space-x-2">
                <img
                  v-for="(attendee, index) in consultation.attendees.slice(
                    0,
                    3
                  )"
                  :key="attendee.id"
                  :src="attendee.avatar"
                  :alt="attendee.name"
                  class="h-10 w-10 rounded-full border-2 border-white object-cover"
                  :style="{ zIndex: 3 - index }"
                />
                <div
                  v-if="consultation.attendees.length > 3"
                  class="h-10 w-10 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-xs font-medium text-indigo-600"
                >
                  +{{ consultation.attendees.length - 3 }}
                </div>
              </div>
            </div>

            <!-- Consultation details -->
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-semibold text-gray-900 mb-1">
                {{ consultation.title }}
              </h3>
              <div class="flex flex-wrap items-center gap-2 mb-2">
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                  :class="getStatusBadgeClasses(consultation.status)"
                >
                  {{ consultation.status }}
                </span>
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                  :class="getTypeBadgeClasses(consultation.type)"
                >
                  {{ consultation.type.replace("-", " ") }}
                </span>
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"
                >
                  {{ consultation.category }}
                </span>
                <span
                  v-if="isToday(consultation.startTime)"
                  class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800"
                >
                  Today
                </span>
              </div>
              <div
                class="flex flex-wrap items-center text-sm text-gray-500 gap-4"
              >
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ formatDate(consultation.startTime) }}
                </div>
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ formatTime(consultation.startTime) }} -
                  {{ formatTime(consultation.endTime) }}
                  <span class="ml-1 text-xs"
                    >({{ formatDuration(consultation) }})</span
                  >
                </div>
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-indigo-600">
                    {{ formatPrice(consultation.price) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="flex items-center space-x-2">
            <button
              @click="viewConsultation(consultation)"
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

            <button
              v-if="consultation.status === 'scheduled'"
              @click="startMeeting(consultation)"
              class="p-2 rounded-lg text-gray-500 hover:bg-blue-100 hover:text-blue-700 transition-colors"
              title="Join Meeting"
              :disabled="!consultation.meetingUrl"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                />
              </svg>
            </button>

            <button
              v-if="consultation.status === 'scheduled'"
              @click="sendReminder(consultation)"
              class="p-2 rounded-lg text-gray-500 hover:bg-yellow-100 hover:text-yellow-700 transition-colors"
              title="Send Reminder"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                />
              </svg>
            </button>

            <button
              v-if="consultation.status === 'scheduled'"
              @click="initiateReschedule(consultation)"
              class="p-2 rounded-lg text-gray-500 hover:bg-indigo-100 hover:text-indigo-700 transition-colors"
              title="Reschedule"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>

            <button
              v-if="consultation.status === 'scheduled'"
              @click="confirmCancellation(consultation)"
              class="p-2 rounded-lg text-gray-500 hover:bg-red-100 hover:text-red-700 transition-colors"
              title="Cancel"
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

    <!-- Calendar View (Simplified) -->
    <div
      v-else-if="viewMode === 'calendar' && filteredConsultations.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200 p-4"
    >
      <!-- Calendar Header -->
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">
          {{
            currentMonth.toLocaleDateString("en-US", {
              month: "long",
              year: "numeric",
            })
          }}
        </h3>
        <div class="flex space-x-2">
          <button
            @click="previousMonth"
            class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
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
          <button
            @click="nextMonth"
            class="p-1 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Calendar Grid (Days of Week) -->
      <div
        class="grid grid-cols-7 gap-1 mb-1 text-center text-sm font-medium text-gray-500"
      >
        <div>Sun</div>
        <div>Mon</div>
        <div>Tue</div>
        <div>Wed</div>
        <div>Thu</div>
        <div>Fri</div>
        <div>Sat</div>
      </div>

      <!-- Calendar Grid (Days) -->
      <div class="grid grid-cols-7 gap-1">
        <div
          v-for="day in calendarDays"
          :key="day.toISOString()"
          :class="[
            'min-h-24 p-1 border rounded',
            isToday(day.toISOString())
              ? 'bg-indigo-50 border-indigo-200'
              : 'border-gray-200',
          ]"
        >
          <div class="flex justify-between items-start">
            <span
              :class="[
                'text-sm font-medium rounded-full h-6 w-6 flex items-center justify-center',
                isToday(day.toISOString())
                  ? 'bg-indigo-600 text-white'
                  : 'text-gray-700',
              ]"
            >
              {{ day.getDate() }}
            </span>
            <span
              v-if="getConsultationsForDay(day).length > 0"
              class="text-xs px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-medium"
            >
              {{ getConsultationsForDay(day).length }}
            </span>
          </div>
          <div class="mt-1 space-y-1">
            <div
              v-for="consultation in getConsultationsForDay(day).slice(0, 2)"
              :key="consultation.id"
              @click="viewConsultation(consultation)"
              class="text-xs p-1 rounded cursor-pointer truncate"
              :class="[
                getStatusBadgeClasses(consultation.status),
                { 'font-medium': isToday(consultation.startTime) },
              ]"
            >
              {{ formatTime(consultation.startTime) }} -
              {{
                consultation.title.length > 15
                  ? consultation.title.substring(0, 15) + "..."
                  : consultation.title
              }}
            </div>
            <div
              v-if="getConsultationsForDay(day).length > 2"
              class="text-xs text-indigo-600 font-medium cursor-pointer text-center p-1"
              @click="
                activeTab = 'upcoming';
                viewMode = 'list';
              "
            >
              + {{ getConsultationsForDay(day).length - 2 }} more
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!isLoading"
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
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">
        No consultations found
      </h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No consultations match your search criteria."
            : "You don't have any consultations in this category yet."
        }}
      </p>
      <NuxtLink
        to="/dashboard/instructor/consultations/create"
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
        <span>Create New Consultation</span>
      </NuxtLink>
    </div>

    <!-- Consultation Detail Modal -->
    <div
      v-if="showConsultationModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full shadow-xl overflow-hidden"
      >
        <!-- Modal Header -->
        <div class="p-4 sm:p-6 border-b border-gray-200">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-bold text-gray-900">
                {{ selectedConsultation?.title }}
              </h3>
              <div class="flex flex-wrap mt-2 gap-2">
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="
                    getStatusBadgeClasses(selectedConsultation?.status || '')
                  "
                >
                  {{ selectedConsultation?.status }}
                </span>
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="getTypeBadgeClasses(selectedConsultation?.type || '')"
                >
                  {{ selectedConsultation?.type.replace("-", " ") }}
                </span>
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"
                >
                  {{ selectedConsultation?.category }}
                </span>
              </div>
            </div>
            <button
              @click="showConsultationModal = false"
              class="text-gray-400 hover:text-gray-500"
            >
              <svg
                class="h-6 w-6"
                xmlns="http://www.w3.org/2000/svg"
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

        <!-- Modal Body -->
        <div class="p-4 sm:p-6 max-h-[70vh] overflow-y-auto">
          <div class="space-y-6">
            <!-- Session Details -->
            <div>
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Session Details
              </h4>
              <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <div class="text-sm font-medium text-gray-500">
                    Date & Time
                  </div>
                  <div class="text-base text-gray-900">
                    {{ formatDateTime(selectedConsultation?.startTime || "") }}
                  </div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">Duration</div>
                  <div class="text-base text-gray-900">
                    {{ formatDuration(selectedConsultation) }}
                  </div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">Price</div>
                  <div class="text-base text-gray-900">
                    {{ formatPrice(selectedConsultation?.price || 0) }}
                  </div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500">
                    Payment Status
                  </div>
                  <div class="text-base">
                    <span
                      v-if="selectedConsultation?.paid"
                      class="text-green-600 font-medium"
                      >Paid</span
                    >
                    <span v-else class="text-red-600 font-medium">Unpaid</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div>
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Description
              </h4>
              <p class="mt-2 text-gray-700">
                {{ selectedConsultation?.description }}
              </p>
            </div>

            <!-- Attendees -->
            <div>
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Attendees
              </h4>
              <div class="mt-2 space-y-3">
                <div
                  v-for="attendee in selectedConsultation?.attendees"
                  :key="attendee.id"
                  class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg"
                >
                  <img
                    :src="attendee.avatar"
                    :alt="attendee.name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div>
                    <div class="font-medium text-gray-900">
                      {{ attendee.name }}
                    </div>
                    <div class="text-sm text-gray-500 flex items-center">
                      <span class="capitalize mr-2">{{ attendee.role }}</span>
                      <span v-if="attendee.email" class="text-gray-400">|</span>
                      <a
                        v-if="attendee.email"
                        :href="`mailto:${attendee.email}`"
                        class="text-indigo-600 ml-2 hover:underline"
                      >
                        {{ attendee.email }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Personal Notes -->
            <div v-if="selectedConsultation?.notes">
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Personal Notes
              </h4>
              <div
                class="mt-2 p-3 bg-yellow-50 border border-yellow-100 rounded-lg text-gray-700"
              >
                {{ selectedConsultation?.notes }}
              </div>
            </div>

            <!-- Meeting Link -->
            <div v-if="selectedConsultation?.meetingUrl">
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Meeting Link
              </h4>
              <div class="mt-2 flex">
                <a
                  :href="selectedConsultation?.meetingUrl"
                  target="_blank"
                  class="inline-flex items-center text-indigo-600 hover:text-indigo-800"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"
                    />
                    <path
                      d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"
                    />
                  </svg>
                  <span>Open Meeting Link</span>
                </a>
              </div>
            </div>

            <!-- Recording Link -->
            <div v-if="selectedConsultation?.recordingUrl">
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Recording
              </h4>
              <div class="mt-2 flex">
                <a
                  :href="selectedConsultation?.recordingUrl"
                  target="_blank"
                  class="inline-flex items-center text-indigo-600 hover:text-indigo-800"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                    />
                  </svg>
                  <span>View Recording</span>
                </a>
              </div>
            </div>

            <!-- Materials -->
            <div
              v-if="
                selectedConsultation?.materials &&
                selectedConsultation.materials.length > 0
              "
            >
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Materials
              </h4>
              <div class="mt-2 space-y-2">
                <div
                  v-for="material in selectedConsultation.materials"
                  :key="material.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center">
                    <!-- File type icon -->
                    <div
                      class="mr-3 p-2 rounded-md bg-indigo-100 text-indigo-700"
                    >
                      <svg
                        v-if="material.type === 'pdf'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                        />
                      </svg>
                      <svg
                        v-else-if="material.type === 'zip'"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                        />
                      </svg>
                      <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
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
                    <div>
                      <div class="font-medium text-gray-900">
                        {{ material.name }}
                      </div>
                      <div class="text-xs text-gray-500 uppercase">
                        {{ material.type }}
                      </div>
                    </div>
                  </div>
                  <a
                    :href="material.url"
                    target="_blank"
                    class="text-indigo-600 hover:text-indigo-800"
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
                  </a>
                </div>
              </div>
            </div>

            <!-- Student Feedback -->
            <div v-if="selectedConsultation?.feedback">
              <h4 class="text-sm font-medium text-gray-500 uppercase">
                Student Feedback
              </h4>
              <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center mb-2">
                  <div class="flex text-yellow-400">
                    <svg
                      v-for="i in 5"
                      :key="i"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      :class="{
                        'text-gray-300':
                          i > Math.floor(selectedConsultation?.rating || 0),
                      }"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                  <span class="ml-2 font-medium">{{
                    selectedConsultation?.rating
                  }}</span>
                </div>
                <p class="text-gray-700 italic">
                  {{ selectedConsultation?.feedback }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
          <button
            v-if="selectedConsultation?.status === 'scheduled'"
            @click="
              showConsultationModal = false;
              startMeeting(selectedConsultation);
            "
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
              />
            </svg>
            Join Meeting
          </button>
          <button
            @click="showConsultationModal = false"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Close
          </button>
        </div>
      </div>

      <!-- Reschedule Modal -->
      <div
        v-if="showRescheduleModal"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
          <div class="text-center mb-5">
            <div
              class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4"
            >
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
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
              Reschedule Consultation
            </h3>
            <p class="text-sm text-gray-600">
              Send a reschedule request for your consultation with
              {{ selectedConsultation?.attendees[0]?.name }}.
            </p>
          </div>

          <!-- Reschedule Form (simplified) -->
          <div class="space-y-4 mb-5">
            <!-- Date Picker -->
            <div>
              <label
                for="reschedule-date"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                New Date
              </label>
              <input
                id="reschedule-date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                min="2025-04-15"
              />
            </div>

            <!-- Time Picker -->
            <div>
              <label
                for="reschedule-time"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                New Time
              </label>
              <input
                id="reschedule-time"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Reason -->
            <div>
              <label
                for="reschedule-reason"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Reason for Rescheduling
              </label>
              <textarea
                id="reschedule-reason"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Please provide a reason for rescheduling..."
              ></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showRescheduleModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Cancel
            </button>
            <button
              @click="showRescheduleModal = false"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Send Request
            </button>
          </div>
        </div>
      </div>

      <!-- Cancel Confirmation Modal -->
      <div
        v-if="showCancelModal"
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
              Cancel Consultation
            </h3>
            <p class="text-sm text-gray-600">
              Are you sure you want to cancel your consultation with
              {{ selectedConsultation?.attendees[0]?.name }} on
              {{ formatDate(selectedConsultation?.startTime || "") }}?
            </p>
            <p class="text-sm text-red-600 mt-2">
              This action cannot be undone.
            </p>
          </div>

          <div class="mb-4">
            <label
              for="cancel-reason"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Reason for Cancellation
            </label>
            <textarea
              id="cancel-reason"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Please provide a reason for cancellation..."
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showCancelModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              No, Keep It
            </button>
            <button
              @click="cancelConsultation"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
              Yes, Cancel
            </button>
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

/* Calendar day hover effect */
.calendar-day:hover {
  background-color: rgba(79, 70, 229, 0.05);
}
</style>
