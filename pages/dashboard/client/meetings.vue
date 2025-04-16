<!-- pages/dashboard/client/meetings/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta using the dashboard-client layout
definePageMeta({
  layout: "dashboard-client",
});

// Set page title and meta description
useHead({
  title: "My Meetings - Client Dashboard",
  meta: [
    {
      name: "description",
      content: "Schedule and manage your meetings with consultants",
    },
  ],
});

// Types for meetings data
interface Meeting {
  id: string;
  title: string;
  startTime: string; // ISO string format
  endTime: string; // ISO string format
  consultant: {
    id: string;
    name: string;
    avatar: string;
    specialization: string;
  };
  project?: {
    id: string;
    title: string;
  };
  description?: string;
  location: "video" | "in-person" | "phone";
  status: "upcoming" | "completed" | "cancelled";
  notes?: string;
  documents?: Array<{
    id: string;
    name: string;
    type: string;
    url: string;
  }>;
}

// State variables
const isLoading = ref(true);
const meetings = ref<Meeting[]>([]);
const filteredMeetings = ref<Meeting[]>([]);
const selectedMeeting = ref<Meeting | null>(null);
const showMeetingModal = ref(false);
const activeTab = ref("upcoming");
const searchQuery = ref("");
const calendarView = ref("month");
const currentDate = ref(new Date());
const showScheduleModal = ref(false);
const showCancelModal = ref(false);
const isCancelling = ref(false);
const cancelReason = ref("");

// Consultants list for scheduling new meetings
const consultants = ref([
  {
    id: "c1",
    name: "Sarah Parker",
    avatar: "/api/placeholder/100/100",
    specialization: "Full-Stack Development",
    rating: 4.9,
    availableDates: [
      "2025-04-17T10:00:00Z",
      "2025-04-18T14:00:00Z",
      "2025-04-19T11:00:00Z",
    ],
  },
  {
    id: "c2",
    name: "Alex Thompson",
    avatar: "/api/placeholder/100/100",
    specialization: "UI/UX Design",
    rating: 4.7,
    availableDates: [
      "2025-04-17T09:00:00Z",
      "2025-04-18T13:00:00Z",
      "2025-04-20T10:00:00Z",
    ],
  },
  {
    id: "c3",
    name: "David Chen",
    avatar: "/api/placeholder/100/100",
    specialization: "Cloud Architecture",
    rating: 4.8,
    availableDates: [
      "2025-04-18T11:00:00Z",
      "2025-04-19T15:00:00Z",
      "2025-04-21T10:00:00Z",
    ],
  },
]);

// Projects list for associating with meetings
const projects = ref([
  { id: "p1", title: "Website Redesign" },
  { id: "p2", title: "Mobile App Development" },
  { id: "p3", title: "E-commerce Integration" },
]);

// Fetch meetings (mock data for now)
const fetchMeetings = async () => {
  isLoading.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data for meetings
    meetings.value = [
      {
        id: "m1",
        title: "Project Kickoff Meeting",
        startTime: "2025-04-17T10:00:00Z",
        endTime: "2025-04-17T11:00:00Z",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
          specialization: "Full-Stack Development",
        },
        project: {
          id: "p1",
          title: "Website Redesign",
        },
        description:
          "Initial meeting to discuss project requirements and timeline.",
        location: "video",
        status: "upcoming",
        notes: "Prepare list of requirements and goals for the project.",
      },
      {
        id: "m2",
        title: "UI Design Review",
        startTime: "2025-04-18T14:00:00Z",
        endTime: "2025-04-18T15:30:00Z",
        consultant: {
          id: "c2",
          name: "Alex Thompson",
          avatar: "/api/placeholder/100/100",
          specialization: "UI/UX Design",
        },
        project: {
          id: "p1",
          title: "Website Redesign",
        },
        description: "Review initial wireframes and design concepts.",
        location: "video",
        status: "upcoming",
        documents: [
          {
            id: "d1",
            name: "Wireframes_v1.pdf",
            type: "pdf",
            url: "#",
          },
        ],
      },
      {
        id: "m3",
        title: "Technical Consultation",
        startTime: "2025-04-19T11:00:00Z",
        endTime: "2025-04-19T12:00:00Z",
        consultant: {
          id: "c3",
          name: "David Chen",
          avatar: "/api/placeholder/100/100",
          specialization: "Cloud Architecture",
        },
        project: {
          id: "p2",
          title: "Mobile App Development",
        },
        description:
          "Discuss cloud infrastructure requirements for the mobile application.",
        location: "video",
        status: "upcoming",
      },
      {
        id: "m4",
        title: "Weekly Progress Update",
        startTime: "2025-04-12T10:00:00Z",
        endTime: "2025-04-12T10:45:00Z",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
          specialization: "Full-Stack Development",
        },
        project: {
          id: "p1",
          title: "Website Redesign",
        },
        description: "Regular check-in on project progress.",
        location: "video",
        status: "completed",
        notes:
          "Frontend development is 50% complete. Backend APIs are being implemented.",
      },
      {
        id: "m5",
        title: "E-commerce Integration Planning",
        startTime: "2025-04-10T15:00:00Z",
        endTime: "2025-04-10T16:00:00Z",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
          specialization: "Full-Stack Development",
        },
        project: {
          id: "p3",
          title: "E-commerce Integration",
        },
        description: "Plan integration strategy for e-commerce platform.",
        location: "in-person",
        status: "cancelled",
        notes: "Cancelled due to consultant unavailability.",
      },
      {
        id: "m6",
        title: "Architecture Review",
        startTime: "2025-04-08T13:00:00Z",
        endTime: "2025-04-08T14:30:00Z",
        consultant: {
          id: "c3",
          name: "David Chen",
          avatar: "/api/placeholder/100/100",
          specialization: "Cloud Architecture",
        },
        project: {
          id: "p2",
          title: "Mobile App Development",
        },
        description: "Review system architecture and infrastructure plans.",
        location: "video",
        status: "completed",
        documents: [
          {
            id: "d2",
            name: "Architecture_Diagram.pdf",
            type: "pdf",
            url: "#",
          },
        ],
      },
    ];

    filterMeetings();
  } catch (error) {
    console.error("Failed to fetch meetings:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter meetings based on active tab and search query
const filterMeetings = () => {
  let filtered = [...meetings.value];

  // Filter by tab
  if (activeTab.value === "upcoming") {
    filtered = filtered.filter((meeting) => meeting.status === "upcoming");
  } else if (activeTab.value === "completed") {
    filtered = filtered.filter((meeting) => meeting.status === "completed");
  } else if (activeTab.value === "cancelled") {
    filtered = filtered.filter((meeting) => meeting.status === "cancelled");
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (meeting) =>
        meeting.title.toLowerCase().includes(query) ||
        meeting.consultant.name.toLowerCase().includes(query) ||
        (meeting.project?.title &&
          meeting.project.title.toLowerCase().includes(query))
    );
  }

  // Sort by date (most recent first for completed/cancelled, soonest first for upcoming)
  if (activeTab.value === "upcoming") {
    filtered.sort(
      (a, b) =>
        new Date(a.startTime).getTime() - new Date(b.startTime).getTime()
    );
  } else {
    filtered.sort(
      (a, b) =>
        new Date(b.startTime).getTime() - new Date(a.startTime).getTime()
    );
  }

  filteredMeetings.value = filtered;
};

// Watch for changes in activeTab or searchQuery
watch(() => activeTab.value, filterMeetings);
watch(() => searchQuery.value, filterMeetings);

// Format date for display
const formatDate = (dateString: string, includeYear = true) => {
  const options: Intl.DateTimeFormatOptions = {
    weekday: "short",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  };

  if (includeYear) {
    options.year = "numeric";
  }

  return new Date(dateString).toLocaleString("en-US", options);
};

// Calculate meeting duration in minutes
const getMeetingDuration = (startTime: string, endTime: string) => {
  const start = new Date(startTime);
  const end = new Date(endTime);
  const durationMs = end.getTime() - start.getTime();
  return Math.round(durationMs / 60000); // Convert to minutes
};

// Get icon for meeting location
const getLocationIcon = (location: string) => {
  switch (location) {
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "in-person":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "phone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get document icon based on type
const getDocumentIcon = (type: string) => {
  switch (type) {
    case "pdf":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "doc":
    case "docx":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
    case "image":
    case "png":
    case "jpg":
    case "jpeg":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "upcoming":
      return "bg-green-100 text-green-800";
    case "completed":
      return "bg-blue-100 text-blue-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Open meeting details
const openMeetingDetails = (meeting: Meeting) => {
  selectedMeeting.value = meeting;
  showMeetingModal.value = true;
};

// Cancel meeting
const cancelMeeting = () => {
  if (!selectedMeeting.value) return;

  isCancelling.value = true;

  // Simulate API call to cancel meeting
  setTimeout(() => {
    if (selectedMeeting.value) {
      const index = meetings.value.findIndex(
        (m) => m.id === selectedMeeting.value?.id
      );
      if (index !== -1) {
        meetings.value[index].status = "cancelled";
        meetings.value[index].notes = cancelReason.value
          ? `Cancelled: ${cancelReason.value}`
          : "Meeting cancelled by client.";
      }
    }

    // Reset and close modals
    isCancelling.value = false;
    showCancelModal.value = false;
    showMeetingModal.value = false;
    cancelReason.value = "";
    filterMeetings();
  }, 1000);
};

// Initialize component
onMounted(() => {
  fetchMeetings();
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">My Meetings</h1>
          <p class="text-gray-600">
            Schedule and manage meetings with your consultants
          </p>
        </div>
        <div class="mt-4 sm:mt-0">
          <button
            @click="showScheduleModal = true"
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
            <span>Schedule Meeting</span>
          </button>
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
              <p class="text-sm text-gray-500 font-medium">Upcoming Meetings</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ meetings.filter((m) => m.status === "upcoming").length }}
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
                Completed Meetings
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ meetings.filter((m) => m.status === "completed").length }}
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
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Consultants</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ consultants.length }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-amber-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-amber-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Projects</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ projects.length }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View options and filters -->
    <div class="bg-white rounded-lg shadow mb-6 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between p-4 space-y-4 md:space-y-0"
      >
        <!-- View toggle -->
        <div class="flex space-x-2">
          <button
            @click="calendarView = 'list'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              calendarView === 'list'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            <span class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
              List View
            </span>
          </button>
          <button
            @click="calendarView = 'month'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              calendarView === 'month'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            <span class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                  clip-rule="evenodd"
                />
              </svg>
              Calendar
            </span>
          </button>
        </div>

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
            All Meetings
          </button>
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
        </div>

        <!-- Search meetings -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search meetings..."
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full md:w-64"
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
          <div class="rounded-full bg-gray-200 h-12 w-12"></div>
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar View - Empty state -->
    <div
      v-else-if="calendarView === 'month' && filteredMeetings.length === 0"
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
      <h3 class="text-lg font-medium text-gray-900 mb-2">No meetings found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No meetings match your search criteria."
            : "You don't have any meetings scheduled yet."
        }}
      </p>
      <button
        @click="showScheduleModal = true"
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
        <span>Schedule Your First Meeting</span>
      </button>
    </div>

    <!-- List View - Empty state -->
    <div
      v-else-if="calendarView === 'list' && filteredMeetings.length === 0"
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
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No meetings found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No meetings match your search criteria."
            : activeTab === "upcoming"
            ? "You don't have any upcoming meetings."
            : activeTab === "completed"
            ? "You don't have any completed meetings."
            : "You don't have any cancelled meetings."
        }}
      </p>
      <button
        v-if="activeTab === 'upcoming' || activeTab === 'all'"
        @click="showScheduleModal = true"
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
        <span>Schedule Meeting</span>
      </button>
    </div>

    <!-- Simplified Calendar View (month view) -->
    <div
      v-else-if="calendarView === 'month' && filteredMeetings.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
    >
      <div
        class="p-4 border-b border-gray-200 flex items-center justify-between"
      >
        <h3 class="text-lg font-semibold text-gray-900">April 2025</h3>
        <div class="flex space-x-2">
          <button
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
            class="px-3 py-1 text-sm font-medium rounded-md text-indigo-700 hover:bg-indigo-50"
          >
            Today
          </button>
          <button
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

      <!-- Calendar grid -->
      <div
        class="grid grid-cols-7 text-sm font-medium text-center py-2 bg-gray-50"
      >
        <div class="py-2">Sun</div>
        <div class="py-2">Mon</div>
        <div class="py-2">Tue</div>
        <div class="py-2">Wed</div>
        <div class="py-2">Thu</div>
        <div class="py-2">Fri</div>
        <div class="py-2">Sat</div>
      </div>

      <div class="grid grid-cols-7 border-t border-gray-200">
        <!-- Calendar days -->
        <template v-for="i in 30" :key="i">
          <div
            class="border-b border-r h-auto min-h-[100px] p-1"
            :class="{
              'bg-indigo-50': i === 16,
              'text-gray-400': i < 6 || i > 30,
            }"
          >
            <div class="text-right p-1">
              <span
                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-sm"
                :class="{ 'bg-indigo-600 text-white': i === 16 }"
              >
                {{ i < 6 ? i + 25 : i > 30 ? i - 30 : i }}
              </span>
            </div>

            <!-- Events on a specific day -->
            <div class="space-y-1">
              <!-- Display meetings for April 17 -->
              <div
                v-if="i === 17"
                class="text-xs p-1 rounded-sm truncate cursor-pointer bg-green-500 text-white border-l-4 border-green-600"
                @click="
                  openMeetingDetails(meetings.find((m) => m.id === 'm1')!)
                "
              >
                <div class="flex items-center">
                  <span class="mr-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                      />
                    </svg>
                  </span>
                  <span>10:00 AM - Kickoff</span>
                </div>
              </div>

              <!-- Display meetings for April 18 -->
              <div
                v-if="i === 18"
                class="text-xs p-1 rounded-sm truncate cursor-pointer bg-green-500 text-white border-l-4 border-green-600"
                @click="
                  openMeetingDetails(meetings.find((m) => m.id === 'm2')!)
                "
              >
                <div class="flex items-center">
                  <span class="mr-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                      />
                    </svg>
                  </span>
                  <span>2:00 PM - Design</span>
                </div>
              </div>

              <!-- Display meetings for April 19 -->
              <div
                v-if="i === 19"
                class="text-xs p-1 rounded-sm truncate cursor-pointer bg-green-500 text-white border-l-4 border-green-600"
                @click="
                  openMeetingDetails(meetings.find((m) => m.id === 'm3')!)
                "
              >
                <div class="flex items-center">
                  <span class="mr-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                      />
                    </svg>
                  </span>
                  <span>11:00 AM - Tech</span>
                </div>
              </div>

              <!-- Display previous completed meetings -->
              <div
                v-if="i === 12"
                class="text-xs p-1 rounded-sm truncate cursor-pointer bg-blue-500 text-white border-l-4 border-blue-600"
                @click="
                  openMeetingDetails(meetings.find((m) => m.id === 'm4')!)
                "
              >
                <div class="flex items-center">
                  <span class="mr-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                      />
                    </svg>
                  </span>
                  <span>10:00 AM - Update</span>
                </div>
              </div>

              <!-- Display cancelled meetings -->
              <div
                v-if="i === 10"
                class="text-xs p-1 rounded-sm truncate cursor-pointer bg-red-500 text-white border-l-4 border-red-600"
                @click="
                  openMeetingDetails(meetings.find((m) => m.id === 'm5')!)
                "
              >
                <div class="flex items-center">
                  <span class="mr-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </span>
                  <span>3:00 PM - E-comm</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- List View -->
    <div
      v-else-if="calendarView === 'list' && filteredMeetings.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="overflow-hidden">
        <ul class="divide-y divide-gray-200">
          <li
            v-for="meeting in filteredMeetings"
            :key="meeting.id"
            class="p-4 hover:bg-gray-50 transition-colors cursor-pointer"
            @click="openMeetingDetails(meeting)"
          >
            <div class="flex flex-col md:flex-row md:items-center">
              <!-- Meeting info -->
              <div class="flex items-start space-x-4 flex-1">
                <img
                  :src="meeting.consultant.avatar"
                  :alt="meeting.consultant.name"
                  class="h-12 w-12 rounded-full object-cover flex-shrink-0"
                />

                <div class="flex-1 min-w-0">
                  <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between"
                  >
                    <div>
                      <h3
                        class="text-base font-semibold text-gray-900 truncate"
                      >
                        {{ meeting.title }}
                      </h3>
                      <div class="flex items-center mt-1 mb-1">
                        <span
                          class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize mr-2"
                          :class="getStatusBadgeClasses(meeting.status)"
                        >
                          {{ meeting.status }}
                        </span>
                        <span class="text-sm text-gray-600">{{
                          meeting.project?.title
                        }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-600">
                    <div class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-gray-400 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span>{{ formatDate(meeting.startTime) }}</span>
                    </div>

                    <div class="flex items-center">
                      <span
                        class="mr-1 text-gray-400"
                        v-html="getLocationIcon(meeting.location)"
                      ></span>
                      <span>{{
                        meeting.location === "video"
                          ? "Video Call"
                          : meeting.location === "in-person"
                          ? "In-Person"
                          : "Phone Call"
                      }}</span>
                    </div>

                    <div class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-gray-400 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span>{{ meeting.consultant.name }}</span>
                    </div>

                    <div
                      v-if="meeting.documents && meeting.documents.length > 0"
                      class="flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-gray-400 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span
                        >{{ meeting.documents.length }}
                        {{
                          meeting.documents.length === 1
                            ? "document"
                            : "documents"
                        }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action buttons - only show for appropriate meeting status -->
              <div class="flex items-center mt-4 md:mt-0">
                <div
                  v-if="meeting.status === 'upcoming'"
                  class="flex items-center space-x-2 text-sm"
                >
                  <button
                    class="text-indigo-600 hover:text-indigo-800 flex items-center"
                    @click.stop="window.open('#')"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>Join</span>
                  </button>
                  <button
                    class="text-red-600 hover:text-red-800 flex items-center"
                    @click.stop="
                      selectedMeeting = meeting;
                      showCancelModal = true;
                    "
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>Cancel</span>
                  </button>
                </div>
                <button
                  v-else
                  class="text-gray-600 hover:text-gray-800 flex items-center"
                  @click.stop="openMeetingDetails(meeting)"
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
                  <span>Details</span>
                </button>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Meeting Details Modal -->
    <div
      v-if="showMeetingModal && selectedMeeting"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="showMeetingModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-2xl w-full shadow-xl overflow-hidden"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900">
              {{ selectedMeeting.title }}
            </h2>
            <button
              @click="showMeetingModal = false"
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

          <div class="mt-4">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
              :class="getStatusBadgeClasses(selectedMeeting.status)"
            >
              {{ selectedMeeting.status }}
            </span>
          </div>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Date & Time</h3>
              <p class="mt-1 text-sm text-gray-900">
                {{ formatDate(selectedMeeting.startTime, true) }}
              </p>
              <p class="mt-1 text-sm text-gray-600">
                Duration:
                {{
                  getMeetingDuration(
                    selectedMeeting.startTime,
                    selectedMeeting.endTime
                  )
                }}
                minutes
              </p>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-500">Meeting Type</h3>
              <p class="mt-1 text-sm text-gray-900 flex items-center">
                <span
                  class="mr-1 text-gray-500"
                  v-html="getLocationIcon(selectedMeeting.location)"
                ></span>
                <span>{{
                  selectedMeeting.location === "video"
                    ? "Video Call"
                    : selectedMeeting.location === "in-person"
                    ? "In-Person Meeting"
                    : "Phone Call"
                }}</span>
              </p>
              <p
                v-if="selectedMeeting.location === 'video'"
                class="mt-1 text-sm text-indigo-600 hover:text-indigo-800 cursor-pointer"
              >
                Join Meeting
              </p>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-500">Consultant</h3>
              <div class="mt-1 flex items-center">
                <img
                  :src="selectedMeeting.consultant.avatar"
                  :alt="selectedMeeting.consultant.name"
                  class="h-8 w-8 rounded-full mr-2 object-cover"
                />
                <div>
                  <p class="text-sm text-gray-900">
                    {{ selectedMeeting.consultant.name }}
                  </p>
                  <p class="text-xs text-gray-600">
                    {{ selectedMeeting.consultant.specialization }}
                  </p>
                </div>
              </div>
            </div>

            <div v-if="selectedMeeting.project">
              <h3 class="text-sm font-medium text-gray-500">Related Project</h3>
              <p class="mt-1 text-sm text-gray-900">
                {{ selectedMeeting.project.title }}
              </p>
            </div>

            <div class="md:col-span-2">
              <h3 class="text-sm font-medium text-gray-500">Description</h3>
              <p class="mt-1 text-sm text-gray-900">
                {{ selectedMeeting.description }}
              </p>
            </div>

            <div v-if="selectedMeeting.notes" class="md:col-span-2">
              <h3 class="text-sm font-medium text-gray-500">Notes</h3>
              <p class="mt-1 text-sm text-gray-900">
                {{ selectedMeeting.notes }}
              </p>
            </div>

            <div
              v-if="
                selectedMeeting.documents &&
                selectedMeeting.documents.length > 0
              "
              class="md:col-span-2"
            >
              <h3 class="text-sm font-medium text-gray-500">Documents</h3>
              <div class="mt-1 space-y-2">
                <div
                  v-for="doc in selectedMeeting.documents"
                  :key="doc.id"
                  class="flex items-center p-2 border border-gray-200 rounded-md hover:bg-gray-50"
                >
                  <span
                    class="mr-2 text-gray-500"
                    v-html="getDocumentIcon(doc.type)"
                  ></span>
                  <span class="text-sm text-gray-900">{{ doc.name }}</span>
                  <a
                    :href="doc.url"
                    class="ml-auto text-indigo-600 hover:text-indigo-800"
                    @click.stop
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
          </div>

          <!-- Action buttons based on meeting status -->
          <div class="mt-6 flex justify-end space-x-3">
            <button
              v-if="selectedMeeting.status === 'upcoming'"
              @click="
                showCancelModal = true;
                showMeetingModal = false;
              "
              class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Cancel Meeting
            </button>
            <button
              v-if="
                selectedMeeting.status === 'upcoming' &&
                selectedMeeting.location === 'video'
              "
              @click="window.open('#')"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Join Meeting
            </button>
            <button
              @click="showMeetingModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel Meeting Modal -->
    <div
      v-if="showCancelModal && selectedMeeting"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="showCancelModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full shadow-xl overflow-hidden"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900">Cancel Meeting</h2>
            <button
              @click="showCancelModal = false"
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

          <div class="mt-4">
            <p class="text-sm text-gray-600">
              Are you sure you want to cancel your meeting with
              <span class="font-semibold">{{
                selectedMeeting.consultant.name
              }}</span>
              scheduled for
              <span class="font-semibold">{{
                formatDate(selectedMeeting.startTime)
              }}</span
              >?
            </p>

            <div class="mt-4">
              <label
                for="cancel-reason"
                class="block text-sm font-medium text-gray-700"
                >Reason for cancellation (optional)</label
              >
              <textarea
                id="cancel-reason"
                v-model="cancelReason"
                rows="3"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Please provide a reason for cancellation..."
              ></textarea>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              @click="showCancelModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="isCancelling"
            >
              Never mind
            </button>
            <button
              @click="cancelMeeting"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              :disabled="isCancelling"
            >
              <span v-if="isCancelling" class="flex items-center">
                <svg
                  class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Cancelling...
              </span>
              <span v-else>Yes, Cancel Meeting</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Meeting Modal (simplified for demo) -->
    <div
      v-if="showScheduleModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="showScheduleModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-2xl w-full shadow-xl overflow-hidden"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start">
            <h2 class="text-xl font-bold text-gray-900">
              Schedule New Meeting
            </h2>
            <button
              @click="showScheduleModal = false"
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

          <div class="mt-4 space-y-4">
            <!-- Meeting title -->
            <div>
              <label
                for="meeting-title"
                class="block text-sm font-medium text-gray-700"
                >Meeting Title</label
              >
              <input
                id="meeting-title"
                type="text"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter meeting title..."
              />
            </div>

            <!-- Select consultant -->
            <div>
              <label
                for="consultant"
                class="block text-sm font-medium text-gray-700"
                >Select Consultant</label
              >
              <select
                id="consultant"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="">Select a consultant</option>
                <option
                  v-for="consultant in consultants"
                  :key="consultant.id"
                  :value="consultant.id"
                >
                  {{ consultant.name }} - {{ consultant.specialization }}
                </option>
              </select>
            </div>

            <!-- Select project -->
            <div>
              <label
                for="project"
                class="block text-sm font-medium text-gray-700"
                >Related Project</label
              >
              <select
                id="project"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="">Select a project</option>
                <option
                  v-for="project in projects"
                  :key="project.id"
                  :value="project.id"
                >
                  {{ project.title }}
                </option>
              </select>
            </div>

            <!-- Date and time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label
                  for="meeting-date"
                  class="block text-sm font-medium text-gray-700"
                  >Date</label
                >
                <input
                  id="meeting-date"
                  type="date"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label
                  for="meeting-time"
                  class="block text-sm font-medium text-gray-700"
                  >Time</label
                >
                <input
                  id="meeting-time"
                  type="time"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
            </div>

            <!-- Meeting format -->
            <div>
              <label class="block text-sm font-medium text-gray-700"
                >Meeting Format</label
              >
              <div class="mt-1 flex space-x-4">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    name="meeting-format"
                    value="video"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                    checked
                  />
                  <span class="ml-2 text-sm text-gray-700">Video Call</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    name="meeting-format"
                    value="in-person"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">In-Person</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    name="meeting-format"
                    value="phone"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Phone Call</span>
                </label>
              </div>
            </div>

            <!-- Meeting description -->
            <div>
              <label
                for="meeting-description"
                class="block text-sm font-medium text-gray-700"
                >Description</label
              >
              <textarea
                id="meeting-description"
                rows="3"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter meeting description..."
              ></textarea>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="mt-6 flex justify-end space-x-3">
            <button
              @click="showScheduleModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              @click="showScheduleModal = false"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Schedule Meeting
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Loading animation */
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
  transition: opacity 0.3s, transform 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* Spinning animation for loading buttons */
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
