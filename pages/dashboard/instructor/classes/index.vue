<!-- pages/dashboard/instructor/classes/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Types
interface ClassSession {
  id: number;
  title: string;
  courseId: number;
  courseName: string;
  date: string; // ISO string
  startTime: string; // ISO string
  endTime: string; // ISO string
  type: "lecture" | "workshop" | "qa" | "review" | "office_hours";
  location?: string;
  isOnline: boolean;
  meetingUrl?: string;
  registeredStudents: number;
  maxCapacity: number;
  status: "scheduled" | "live" | "completed" | "cancelled";
  recordingUrl?: string;
  description?: string;
  materials?: { title: string; url: string }[];
}

// Set the page layout to dashboard-instructor
definePageMeta({
  layout: "dashboard-instructor",
});

// Page meta
useHead({
  title: "Classes - Instructor Dashboard",
});

// State
const classes = ref<ClassSession[]>([]);
const isLoading = ref(true);
const activeTab = ref("upcoming");
const viewMode = ref("calendar"); // 'calendar' or 'list'
const searchQuery = ref("");
const selectedClass = ref<ClassSession | null>(null);
const showClassDetailsModal = ref(false);
const showCancelClassModal = ref(false);
const cancelReason = ref("");
const isCancelling = ref(false);

// Filter classes for upcoming/past
const upcomingClasses = computed(() => {
  return classes.value
    .filter((session) => {
      const now = new Date();
      const sessionDate = new Date(session.startTime);
      return sessionDate > now && session.status !== "cancelled";
    })
    .sort(
      (a, b) =>
        new Date(a.startTime).getTime() - new Date(b.startTime).getTime()
    );
});

const pastClasses = computed(() => {
  return classes.value
    .filter((session) => {
      const now = new Date();
      const sessionDate = new Date(session.startTime);
      return sessionDate < now || session.status === "completed";
    })
    .sort(
      (a, b) =>
        new Date(b.startTime).getTime() - new Date(a.startTime).getTime()
    );
});

const currentClasses = computed(() => {
  return classes.value.filter((session) => {
    const now = new Date();
    const startTime = new Date(session.startTime);
    const endTime = new Date(session.endTime);
    return startTime <= now && now <= endTime && session.status === "live";
  });
});

const cancelledClasses = computed(() => {
  return classes.value
    .filter((session) => session.status === "cancelled")
    .sort(
      (a, b) =>
        new Date(b.startTime).getTime() - new Date(a.startTime).getTime()
    );
});

// Calendar events data from classes
const calendarEvents = computed(() => {
  return classes.value.map((classSession) => {
    let color;
    switch (classSession.type) {
      case "lecture":
        color = "bg-indigo-500";
        break;
      case "workshop":
        color = "bg-purple-500";
        break;
      case "qa":
        color = "bg-green-500";
        break;
      case "review":
        color = "bg-amber-500";
        break;
      case "office_hours":
        color = "bg-blue-500";
        break;
      default:
        color = "bg-gray-500";
    }

    // If cancelled, make it gray
    if (classSession.status === "cancelled") {
      color = "bg-gray-400";
    }

    return {
      id: classSession.id.toString(),
      title: classSession.title,
      description: `${classSession.courseName} - ${formatClassType(
        classSession.type
      )}`,
      startTime: classSession.startTime,
      endTime: classSession.endTime,
      type:
        classSession.type === "lecture"
          ? "course"
          : classSession.type === "qa"
          ? "meeting"
          : classSession.type === "workshop"
          ? "consultation"
          : classSession.type === "review"
          ? "assignment"
          : "reminder",
      location:
        classSession.location || (classSession.isOnline ? "Online" : ""),
      url: classSession.meetingUrl,
      color,
      isCompleted:
        classSession.status === "completed" ||
        classSession.status === "cancelled",
    };
  });
});

// Filtered classes based on search query
const filteredClasses = computed(() => {
  if (!searchQuery.value) {
    if (activeTab.value === "upcoming") return upcomingClasses.value;
    if (activeTab.value === "past") return pastClasses.value;
    if (activeTab.value === "current") return currentClasses.value;
    if (activeTab.value === "cancelled") return cancelledClasses.value;
    return classes.value;
  }

  const query = searchQuery.value.toLowerCase();
  let filtered = classes.value.filter(
    (session) =>
      session.title.toLowerCase().includes(query) ||
      session.courseName.toLowerCase().includes(query) ||
      (session.description && session.description.toLowerCase().includes(query))
  );

  if (activeTab.value === "upcoming") {
    filtered = filtered.filter((session) => {
      const now = new Date();
      const sessionDate = new Date(session.startTime);
      return sessionDate > now && session.status !== "cancelled";
    });
  } else if (activeTab.value === "past") {
    filtered = filtered.filter((session) => {
      const now = new Date();
      const sessionDate = new Date(session.startTime);
      return sessionDate < now || session.status === "completed";
    });
  } else if (activeTab.value === "current") {
    filtered = filtered.filter((session) => {
      const now = new Date();
      const startTime = new Date(session.startTime);
      const endTime = new Date(session.endTime);
      return startTime <= now && now <= endTime && session.status === "live";
    });
  } else if (activeTab.value === "cancelled") {
    filtered = filtered.filter((session) => session.status === "cancelled");
  }

  if (activeTab.value === "past" || activeTab.value === "cancelled") {
    filtered.sort(
      (a, b) =>
        new Date(b.startTime).getTime() - new Date(a.startTime).getTime()
    );
  } else {
    filtered.sort(
      (a, b) =>
        new Date(a.startTime).getTime() - new Date(b.startTime).getTime()
    );
  }

  return filtered;
});

// Statistics
const statistics = computed(() => {
  const totalClasses = classes.value.length;
  const completedClasses = classes.value.filter(
    (session) => session.status === "completed"
  ).length;
  const totalStudents = classes.value.reduce(
    (sum, session) => sum + session.registeredStudents,
    0
  );
  const averageAttendance =
    completedClasses > 0 ? Math.round(totalStudents / completedClasses) : 0;

  const classTypeCount = classes.value.reduce((acc, session) => {
    const type = session.type;
    acc[type] = (acc[type] || 0) + 1;
    return acc;
  }, {} as Record<string, number>);

  const mostFrequentType = Object.entries(classTypeCount).reduce(
    (a, b) => (a[1] > b[1] ? a : b),
    ["", 0]
  )[0];

  return {
    totalClasses,
    upcomingClasses: upcomingClasses.value.length,
    completedClasses,
    cancellationRate:
      totalClasses > 0
        ? Math.round((cancelledClasses.value.length / totalClasses) * 100)
        : 0,
    totalStudents,
    averageAttendance,
    mostFrequentType,
  };
});

// Fetch class data
const fetchClasses = async () => {
  try {
    isLoading.value = true;

    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    classes.value = [
      {
        id: 1,
        title: "Introduction to React Hooks",
        courseId: 2,
        courseName: "Advanced JavaScript: From Fundamentals to Mastery",
        date: "2025-04-18",
        startTime: "2025-04-18T14:00:00Z",
        endTime: "2025-04-18T16:00:00Z",
        type: "lecture",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456789",
        registeredStudents: 87,
        maxCapacity: 100,
        status: "scheduled",
        description:
          "In this lecture, we will cover the fundamentals of React Hooks and how they transform the way we build React components.",
      },
      {
        id: 2,
        title: "React Project Workshop",
        courseId: 2,
        courseName: "Advanced JavaScript: From Fundamentals to Mastery",
        date: "2025-04-20",
        startTime: "2025-04-20T15:00:00Z",
        endTime: "2025-04-20T17:30:00Z",
        type: "workshop",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456790",
        registeredStudents: 65,
        maxCapacity: 100,
        status: "scheduled",
        description:
          "Hands-on workshop to build a small React application using hooks and context API.",
      },
      {
        id: 3,
        title: "DOM Manipulation and Event Handling",
        courseId: 1,
        courseName: "Complete Web Development Bootcamp",
        date: "2025-04-16",
        startTime: "2025-04-16T13:00:00Z",
        endTime: "2025-04-16T15:00:00Z",
        type: "lecture",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456791",
        registeredStudents: 112,
        maxCapacity: 120,
        status: "completed",
        recordingUrl: "https://example.com/recordings/dom-manipulation",
        description:
          "Learn how to manipulate the DOM and handle events in JavaScript.",
      },
      {
        id: 4,
        title: "Mid-term Review Session",
        courseId: 1,
        courseName: "Complete Web Development Bootcamp",
        date: "2025-04-21",
        startTime: "2025-04-21T18:00:00Z",
        endTime: "2025-04-21T20:00:00Z",
        type: "review",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456792",
        registeredStudents: 95,
        maxCapacity: 120,
        status: "scheduled",
        description:
          "Review session covering all topics from the first half of the course.",
      },
      {
        id: 5,
        title: "Office Hours",
        courseId: 1,
        courseName: "Complete Web Development Bootcamp",
        date: "2025-04-17",
        startTime: "2025-04-17T16:00:00Z",
        endTime: "2025-04-17T18:00:00Z",
        type: "office_hours",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456793",
        registeredStudents: 28,
        maxCapacity: 50,
        status: "scheduled",
        description:
          "Drop in for help with assignments or any questions about the course material.",
      },
      {
        id: 6,
        title: "Q&A Session: JavaScript Fundamentals",
        courseId: 2,
        courseName: "Advanced JavaScript: From Fundamentals to Mastery",
        date: "2025-04-15",
        startTime: "2025-04-15T17:00:00Z",
        endTime: "2025-04-15T18:30:00Z",
        type: "qa",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456794",
        registeredStudents: 73,
        maxCapacity: 100,
        status: "completed",
        recordingUrl: "https://example.com/recordings/js-fundamentals-qa",
        description:
          "Open Q&A session focused on JavaScript fundamentals and common challenges.",
      },
      {
        id: 7,
        title: "HTML & CSS Workshop",
        courseId: 1,
        courseName: "Complete Web Development Bootcamp",
        date: "2025-04-14",
        startTime: "2025-04-14T14:00:00Z",
        endTime: "2025-04-14T16:30:00Z",
        type: "workshop",
        isOnline: false,
        location: "Tech Hub, Room 302",
        registeredStudents: 42,
        maxCapacity: 50,
        status: "completed",
        description:
          "In-person workshop to help you master HTML and CSS layout techniques.",
      },
      {
        id: 8,
        title: "React Native Fundamentals",
        courseId: 4,
        courseName: "React Native for Mobile Development",
        date: "2025-04-19",
        startTime: "2025-04-19T13:00:00Z",
        endTime: "2025-04-19T15:00:00Z",
        type: "lecture",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456795",
        registeredStudents: 0,
        maxCapacity: 80,
        status: "cancelled",
        description:
          "Introduction to React Native and its core concepts for mobile development.",
      },
      {
        id: 9,
        title: "Live Coding: Building a REST API with Node.js",
        courseId: 6,
        courseName: "Node.js API Development",
        date: "2025-04-15",
        startTime: new Date(new Date().getTime() - 30 * 60000).toISOString(), // 30 minutes ago
        endTime: new Date(new Date().getTime() + 90 * 60000).toISOString(), // 90 minutes from now
        type: "workshop",
        isOnline: true,
        meetingUrl: "https://zoom.us/j/123456796",
        registeredStudents: 56,
        maxCapacity: 70,
        status: "live",
        description:
          "Live coding session where we build a complete REST API with Node.js, Express, and MongoDB.",
      },
    ];
  } catch (error) {
    console.error("Error fetching classes:", error);
  } finally {
    isLoading.value = false;
  }
};

// Format date and time
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const formatTime = (timeString: string) => {
  const date = new Date(timeString);
  return date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

const formatDateShort = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
  });
};

// Format class duration
const formatDuration = (startTime: string, endTime: string) => {
  const start = new Date(startTime);
  const end = new Date(endTime);
  const diffMs = end.getTime() - start.getTime();
  const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
  const diffMins = Math.round((diffMs % (1000 * 60 * 60)) / (1000 * 60));

  if (diffHrs === 0) {
    return `${diffMins} mins`;
  } else if (diffMins === 0) {
    return `${diffHrs} hr${diffHrs > 1 ? "s" : ""}`;
  } else {
    return `${diffHrs} hr${diffHrs > 1 ? "s" : ""} ${diffMins} mins`;
  }
};

// Format class type to be human-readable
const formatClassType = (type: string) => {
  switch (type) {
    case "lecture":
      return "Lecture";
    case "workshop":
      return "Workshop";
    case "qa":
      return "Q&A Session";
    case "review":
      return "Review Session";
    case "office_hours":
      return "Office Hours";
    default:
      return type.charAt(0).toUpperCase() + type.slice(1).replace("_", " ");
  }
};

// Get class type badge color
const getClassTypeBadgeClasses = (type: string) => {
  switch (type) {
    case "lecture":
      return "bg-indigo-100 text-indigo-800";
    case "workshop":
      return "bg-purple-100 text-purple-800";
    case "qa":
      return "bg-green-100 text-green-800";
    case "review":
      return "bg-amber-100 text-amber-800";
    case "office_hours":
      return "bg-blue-100 text-blue-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get status badge color
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "scheduled":
      return "bg-blue-100 text-blue-800";
    case "live":
      return "bg-green-100 text-green-800";
    case "completed":
      return "bg-gray-100 text-gray-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Show class details
const showClassDetails = (classSession: ClassSession) => {
  selectedClass.value = classSession;
  showClassDetailsModal.value = true;
};

// Open cancel class confirmation
const confirmCancelClass = (classSession: ClassSession) => {
  selectedClass.value = classSession;
  showCancelClassModal.value = true;
  cancelReason.value = "";
};

// Cancel class
const cancelClass = async () => {
  if (!selectedClass.value || !cancelReason.value) return;

  try {
    isCancelling.value = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Update local state
    const index = classes.value.findIndex(
      (session) => session.id === selectedClass.value?.id
    );
    if (index !== -1) {
      classes.value[index].status = "cancelled";
    }

    showCancelClassModal.value = false;
    selectedClass.value = null;
    cancelReason.value = "";
  } catch (error) {
    console.error("Error cancelling class:", error);
  } finally {
    isCancelling.value = false;
  }
};

// Handle calendar event click
const onCalendarEventClick = (event: any) => {
  const classSession = classes.value.find(
    (session) => session.id.toString() === event.id
  );
  if (classSession) {
    showClassDetails(classSession);
  }
};

// Handle calendar date change
const onCalendarDateChange = (date: Date) => {
  console.log("Calendar date changed:", date);
};

// Navigate to start class page
const startClass = (classSession: ClassSession) => {
  // In a real app, this would navigate to a classroom page or launch the meeting
  window.open(classSession.meetingUrl, "_blank");
};

// Calculate capacity percentage
const getCapacityPercentage = (registered: number, max: number) => {
  return Math.round((registered / max) * 100);
};

// Load data on component mount
onMounted(() => {
  fetchClasses();
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
            Classes & Sessions
          </h1>
          <p class="text-gray-600">
            Manage your live classes, workshops, and office hours
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/dashboard/instructor/classes/schedule"
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
            <span>Schedule New Class</span>
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
                viewBox="0 0 20 20"
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
              <p class="text-sm text-gray-500 font-medium">Upcoming Classes</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.upcomingClasses }}
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
              <p class="text-sm text-gray-500 font-medium">Completed Classes</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.completedClasses }}
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
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Avg. Attendance</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.averageAttendance }} students
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-red-100 rounded-full p-3 mr-4">
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
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Cancellation Rate</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ statistics.cancellationRate }}%
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View toggle and filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Tab navigation -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'upcoming'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
            @click="activeTab = 'upcoming'"
          >
            Upcoming Classes
          </button>
          <button
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors flex items-center"
            :class="
              activeTab === 'current'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
            @click="activeTab = 'current'"
          >
            Live Now
            <span
              v-if="currentClasses.length > 0"
              class="ml-2 bg-green-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"
            >
              {{ currentClasses.length }}
            </span>
          </button>
          <button
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'past'
                ? 'bg-gray-200 text-gray-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
            @click="activeTab = 'past'"
          >
            Past Classes
          </button>
          <button
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'cancelled'
                ? 'bg-red-100 text-red-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
            @click="activeTab = 'cancelled'"
          >
            Cancelled
          </button>
        </div>

        <!-- View toggle and search controls -->
        <div class="flex items-center space-x-2">
          <!-- View toggle buttons -->
          <div class="flex border rounded-lg overflow-hidden">
            <button
              class="px-3 py-1.5 text-sm font-medium transition-colors"
              :class="
                viewMode === 'calendar'
                  ? 'bg-indigo-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              "
              @click="viewMode = 'calendar'"
            >
              <svg
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
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </button>
            <button
              class="px-3 py-1.5 text-sm font-medium transition-colors"
              :class="
                viewMode === 'list'
                  ? 'bg-indigo-600 text-white'
                  : 'bg-white text-gray-700 hover:bg-gray-100'
              "
              @click="viewMode = 'list'"
            >
              <svg
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
                  d="M4 6h16M4 10h16M4 14h16M4 18h16"
                />
              </svg>
            </button>
          </div>

          <!-- Search box -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search classes..."
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
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200 p-6"
    >
      <div class="animate-pulse space-y-4">
        <div
          v-if="viewMode === 'calendar'"
          class="h-[600px] bg-gray-200 rounded-lg"
        />
        <div v-else class="space-y-4">
          <div
            v-for="i in 3"
            :key="`skeleton-${i}`"
            class="flex items-center space-x-4 p-4 border-b border-gray-200"
          >
            <div class="bg-gray-200 h-12 w-12 rounded-full" />
            <div class="flex-1">
              <div class="h-5 bg-gray-200 rounded w-3/4 mb-3" />
              <div class="h-4 bg-gray-200 rounded w-1/4 mb-3" />
              <div class="h-4 bg-gray-200 rounded w-1/2" />
            </div>
            <div class="flex space-x-2">
              <div class="h-8 w-8 bg-gray-200 rounded-full" />
              <div class="h-8 w-8 bg-gray-200 rounded-full" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar view -->
    <div
      v-else-if="viewMode === 'calendar' && !isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <DashboardCalendar
        :events="calendarEvents"
        user-type="instructor"
        initial-view="month"
        :highlight-today="true"
        :show-weekends="true"
        :allow-create="true"
        :allow-edit="true"
        :is-loading="false"
        @event-click="onCalendarEventClick"
        @date-change="onCalendarDateChange"
      />
    </div>

    <!-- List view -->
    <div
      v-else-if="viewMode === 'list' && !isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <!-- Live now section (always shown if there are live classes) -->
      <div
        v-if="currentClasses.length > 0 && activeTab !== 'current'"
        class="p-4 bg-green-50 border-b border-green-200"
      >
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-lg font-semibold text-green-800 flex items-center">
            <span class="relative flex h-3 w-3 mr-2">
              <span
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"
              />
              <span
                class="relative inline-flex rounded-full h-3 w-3 bg-green-500"
              />
            </span>
            Live Now
          </h3>
          <button
            class="text-sm text-green-700 hover:text-green-900 hover:underline"
            @click="activeTab = 'current'"
          >
            View All Live Classes
          </button>
        </div>
        <div class="bg-white rounded-lg border border-green-200 p-4">
          <div
            v-for="classSession in currentClasses.slice(0, 1)"
            :key="`live-${classSession.id}`"
            class="flex items-center justify-between"
          >
            <div class="flex items-center">
              <div class="bg-green-100 p-3 rounded-full mr-4">
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
                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
              </div>
              <div>
                <h4 class="font-medium text-gray-900">
                  {{ classSession.title }}
                </h4>
                <p class="text-sm text-gray-600">
                  {{ classSession.courseName }}
                </p>
                <div class="flex items-center mt-1">
                  <span
                    class="text-xs px-2 py-0.5 rounded-full mr-2"
                    :class="getClassTypeBadgeClasses(classSession.type)"
                  >
                    {{ formatClassType(classSession.type) }}
                  </span>
                  <span class="text-xs text-gray-500"
                    >{{ formatTime(classSession.startTime) }} -
                    {{ formatTime(classSession.endTime) }}</span
                  >
                </div>
              </div>
            </div>
            <div class="flex space-x-2">
              <button
                class="flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                @click="startClass(classSession)"
              >
                <span class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Join Now
                </span>
              </button>
              <button
                class="p-1 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                title="View Details"
                @click="showClassDetails(classSession)"
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
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filteredClasses.length === 0" class="p-8 text-center">
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">No classes found</h3>
        <p v-if="searchQuery" class="text-gray-600 mb-6">
          No classes match your search criteria.
        </p>
        <p v-else-if="activeTab === 'upcoming'" class="text-gray-600 mb-6">
          You don't have any upcoming classes scheduled.
        </p>
        <p v-else-if="activeTab === 'current'" class="text-gray-600 mb-6">
          You don't have any classes happening right now.
        </p>
        <p v-else-if="activeTab === 'past'" class="text-gray-600 mb-6">
          You haven't conducted any classes yet.
        </p>
        <p v-else-if="activeTab === 'cancelled'" class="text-gray-600 mb-6">
          You don't have any cancelled classes.
        </p>

        <NuxtLink
          to="/dashboard/instructor/classes/schedule"
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
          <span>Schedule New Class</span>
        </NuxtLink>
      </div>

      <!-- Classes list -->
      <div v-else class="divide-y divide-gray-200">
        <div
          v-for="classSession in filteredClasses"
          :key="`class-${classSession.id}`"
          class="p-4 hover:bg-gray-50 transition-colors"
        >
          <div
            class="flex flex-col md:flex-row md:items-center md:justify-between"
          >
            <!-- Class info -->
            <div class="flex items-start mb-4 md:mb-0">
              <!-- Class type icon -->
              <div
                class="rounded-full p-3 mr-4 flex-shrink-0"
                :class="
                  getClassTypeBadgeClasses(classSession.type)
                    .replace('text-', 'bg-')
                    .replace('100', '50')
                "
              >
                <svg
                  v-if="classSession.type === 'lecture'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  :class="
                    getClassTypeBadgeClasses(classSession.type)
                      .replace('bg-', 'text-')
                      .replace('100', '600')
                  "
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <svg
                  v-else-if="classSession.type === 'workshop'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  :class="
                    getClassTypeBadgeClasses(classSession.type)
                      .replace('bg-', 'text-')
                      .replace('100', '600')
                  "
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"
                  />
                </svg>
                <svg
                  v-else-if="classSession.type === 'qa'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  :class="
                    getClassTypeBadgeClasses(classSession.type)
                      .replace('bg-', 'text-')
                      .replace('100', '600')
                  "
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                  />
                </svg>
                <svg
                  v-else-if="classSession.type === 'review'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  :class="
                    getClassTypeBadgeClasses(classSession.type)
                      .replace('bg-', 'text-')
                      .replace('100', '600')
                  "
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  />
                </svg>
                <svg
                  v-else-if="classSession.type === 'office_hours'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  :class="
                    getClassTypeBadgeClasses(classSession.type)
                      .replace('bg-', 'text-')
                      .replace('100', '600')
                  "
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

              <!-- Class details -->
              <div class="flex-1 min-w-0">
                <div
                  class="flex flex-col md:flex-row md:items-center md:justify-between"
                >
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ classSession.title }}
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ classSession.courseName }}
                    </p>
                  </div>
                </div>

                <!-- Class metadata -->
                <div class="flex flex-wrap gap-2 mt-2">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getClassTypeBadgeClasses(classSession.type)"
                  >
                    {{ formatClassType(classSession.type) }}
                  </span>

                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusBadgeClasses(classSession.status)"
                  >
                    {{
                      classSession.status.charAt(0).toUpperCase() +
                      classSession.status.slice(1)
                    }}
                  </span>

                  <span class="inline-flex items-center text-xs text-gray-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
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
                    {{ formatDate(classSession.date) }}
                  </span>

                  <span class="inline-flex items-center text-xs text-gray-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
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
                    {{ formatTime(classSession.startTime) }} -
                    {{ formatTime(classSession.endTime) }} ({{
                      formatDuration(
                        classSession.startTime,
                        classSession.endTime
                      )
                    }})
                  </span>

                  <span class="inline-flex items-center text-xs text-gray-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                    </svg>
                    {{
                      classSession.isOnline ? "Online" : classSession.location
                    }}
                  </span>

                  <span class="inline-flex items-center text-xs text-gray-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                      />
                    </svg>
                    {{ classSession.registeredStudents }} /
                    {{ classSession.maxCapacity }} students
                  </span>
                </div>

                <!-- Capacity bar -->
                <div
                  class="mt-2 w-full bg-gray-200 rounded-full h-2.5 relative"
                >
                  <div
                    class="h-2.5 rounded-full"
                    :class="{
                      'bg-green-500':
                        getCapacityPercentage(
                          classSession.registeredStudents,
                          classSession.maxCapacity
                        ) < 70,
                      'bg-yellow-500':
                        getCapacityPercentage(
                          classSession.registeredStudents,
                          classSession.maxCapacity
                        ) >= 70 &&
                        getCapacityPercentage(
                          classSession.registeredStudents,
                          classSession.maxCapacity
                        ) < 90,
                      'bg-red-500':
                        getCapacityPercentage(
                          classSession.registeredStudents,
                          classSession.maxCapacity
                        ) >= 90,
                    }"
                    :style="`width: ${getCapacityPercentage(
                      classSession.registeredStudents,
                      classSession.maxCapacity
                    )}%`"
                  />
                </div>
              </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-2 md:flex-col lg:flex-row">
              <button
                v-if="classSession.status === 'live'"
                class="flex items-center justify-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                @click="startClass(classSession)"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Join Now</span>
              </button>

              <button
                v-else-if="
                  classSession.status === 'scheduled' &&
                  new Date(classSession.startTime) > new Date()
                "
                class="flex items-center justify-center px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition-colors"
                @click="confirmCancelClass(classSession)"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
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

              <button
                v-else-if="
                  classSession.status === 'completed' &&
                  classSession.recordingUrl
                "
                :href="classSession.recordingUrl"
                target="_blank"
                class="flex items-center justify-center px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>View Recording</span>
              </button>

              <NuxtLink
                v-if="
                  classSession.status === 'scheduled' &&
                  new Date(classSession.startTime) > new Date()
                "
                :to="`/dashboard/instructor/classes/${classSession.id}/edit`"
                class="flex items-center justify-center px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                  />
                </svg>
                <span>Edit</span>
              </NuxtLink>

              <button
                class="flex items-center justify-center px-3 py-1.5 bg-gray-50 text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
                @click="showClassDetails(classSession)"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
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
        </div>
      </div>
    </div>

    <!-- Cancel class confirmation modal -->
    <div
      v-if="showCancelClassModal"
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
            Cancel Class
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to cancel "{{ selectedClass?.title }}"
            scheduled for {{ formatDateShort(selectedClass?.date || "") }} at
            {{ formatTime(selectedClass?.startTime || "") }}?
          </p>
        </div>

        <!-- Cancellation reason -->
        <div class="mb-4">
          <label
            for="cancel-reason"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Please provide a reason for cancellation:
          </label>
          <textarea
            id="cancel-reason"
            v-model="cancelReason"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="Enter cancellation reason (will be sent to students)"
          />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            @click="showCancelClassModal = false"
          >
            Never mind
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="!cancelReason || isCancelling"
            :class="{
              'opacity-50 cursor-not-allowed': !cancelReason || isCancelling,
            }"
            @click="cancelClass"
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
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
              </svg>
              Cancelling...
            </span>
            <span v-else>Cancel Class</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
