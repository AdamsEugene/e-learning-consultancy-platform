<!-- pages/dashboard/consultant/appointments/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Appointments - Consultant Dashboard",
});

// Router instance
const router = useRouter();

// Define appointment types
type AppointmentStatus = "upcoming" | "completed" | "cancelled" | "rescheduled";
type AppointmentType =
  | "consultation"
  | "project_kickoff"
  | "progress_review"
  | "client_meeting"
  | "presentation";

interface Appointment {
  id: number;
  clientName: string;
  clientAvatar: string;
  clientCompany?: string;
  title: string;
  description?: string;
  date: string;
  startTime: string;
  endTime: string;
  duration: number; // in minutes
  status: AppointmentStatus;
  type: AppointmentType;
  location?: string;
  isOnline: boolean;
  meetingUrl?: string;
  notes?: string;
  documents?: Array<{
    id: number;
    name: string;
    type: string;
    url: string;
  }>;
  reminderSet: boolean;
  isPaid?: boolean;
  amount?: number;
}

// Date helper functions
const getCurrentDateString = () => {
  const today = new Date();
  return today.toISOString().split("T")[0];
};

const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  return new Date(dateString).toLocaleDateString("en-US", options);
};

const formatTime = (timeString: string) => {
  return new Date(`2000-01-01T${timeString}`).toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

const getTimeFromNow = (dateString: string, timeString: string) => {
  const now = new Date();
  const appointmentTime = new Date(`${dateString}T${timeString}`);

  const diffMs = appointmentTime.getTime() - now.getTime();
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffMs < 0) {
    return "Passed";
  } else if (diffDays === 0) {
    return "Today";
  } else if (diffDays === 1) {
    return "Tomorrow";
  } else if (diffDays < 7) {
    return `${diffDays} days`;
  } else if (diffDays < 30) {
    return `${Math.floor(diffDays / 7)} weeks`;
  } else {
    return `${Math.floor(diffDays / 30)} months`;
  }
};

// State for appointments
const appointments = ref<Appointment[]>([]);
const isLoading = ref(true);
const activeTab = ref("upcoming");
const searchQuery = ref("");
const selectedAppointment = ref<Appointment | null>(null);
const showAppointmentDetailModal = ref(false);
const showRescheduleModal = ref(false);
const showCancelModal = ref(false);
const showCreateModal = ref(false);
const cancelReason = ref("");
const currentCalendarDate = ref(getCurrentDateString());

// Calendar view state
const isCalendarView = ref(false);
const currentView = ref<"week" | "month">("week");

// New appointment form data
const newAppointment = ref({
  clientName: "",
  title: "",
  description: "",
  date: getCurrentDateString(),
  startTime: "09:00",
  endTime: "10:00",
  duration: 60,
  type: "consultation" as AppointmentType,
  isOnline: true,
  location: "",
  meetingUrl: "",
  notes: "",
  reminderSet: true,
});

// Create mock data
const generateMockAppointments = () => {
  const mockAppointments: Appointment[] = [
    {
      id: 1,
      clientName: "John Smith",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Acme Corp",
      title: "Initial Project Consultation",
      description: "Discussing requirements for the new e-commerce platform.",
      date: "2025-04-16",
      startTime: "09:00:00",
      endTime: "10:00:00",
      duration: 60,
      status: "upcoming",
      type: "consultation",
      isOnline: true,
      meetingUrl: "https://zoom.us/j/123456789",
      notes: "Prepare demo of similar projects",
      reminderSet: true,
      isPaid: true,
      amount: 150,
    },
    {
      id: 2,
      clientName: "Emily Johnson",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "TechStart Inc",
      title: "Project Kickoff Meeting",
      description: "Setting up project timeline and deliverables.",
      date: "2025-04-17",
      startTime: "13:00:00",
      endTime: "14:30:00",
      duration: 90,
      status: "upcoming",
      type: "project_kickoff",
      isOnline: true,
      meetingUrl: "https://teams.microsoft.com/l/meeting/123",
      documents: [
        {
          id: 1,
          name: "Project Proposal",
          type: "pdf",
          url: "#",
        },
      ],
      reminderSet: true,
      isPaid: true,
      amount: 250,
    },
    {
      id: 3,
      clientName: "Michael Chen",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Innovate Solutions",
      title: "Weekly Progress Review",
      description: "Review progress on the mobile app development.",
      date: "2025-04-18",
      startTime: "10:00:00",
      endTime: "11:00:00",
      duration: 60,
      status: "upcoming",
      type: "progress_review",
      isOnline: false,
      location: "Client Office - 123 Business St, Suite 200",
      reminderSet: true,
      isPaid: false,
    },
    {
      id: 4,
      clientName: "Sarah Williams",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Design Masters",
      title: "UI/UX Consultation",
      description: "Discussing the user interface improvements.",
      date: "2025-04-20",
      startTime: "15:00:00",
      endTime: "16:00:00",
      duration: 60,
      status: "upcoming",
      type: "consultation",
      isOnline: true,
      meetingUrl: "https://zoom.us/j/987654321",
      reminderSet: false,
      isPaid: true,
      amount: 150,
    },
    {
      id: 5,
      clientName: "David Rodriguez",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Global Retail",
      title: "Website Redesign Presentation",
      description:
        "Presenting the final design concepts for the website redesign.",
      date: "2025-04-22",
      startTime: "11:00:00",
      endTime: "12:30:00",
      duration: 90,
      status: "upcoming",
      type: "presentation",
      isOnline: true,
      meetingUrl: "https://meet.google.com/abc-defg-hij",
      documents: [
        {
          id: 2,
          name: "Design Concept",
          type: "pdf",
          url: "#",
        },
      ],
      reminderSet: true,
      isPaid: true,
      amount: 300,
    },
    {
      id: 6,
      clientName: "Jessica Lee",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Health Solutions",
      title: "App Feature Discussion",
      description: "Discussing new features for the health tracking app.",
      date: "2025-04-10",
      startTime: "14:00:00",
      endTime: "15:00:00",
      duration: 60,
      status: "completed",
      type: "client_meeting",
      isOnline: true,
      meetingUrl: "https://zoom.us/j/112233445",
      notes: "Client was very satisfied with the proposed features",
      reminderSet: true,
      isPaid: true,
      amount: 150,
    },
    {
      id: 7,
      clientName: "Robert Taylor",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Finance Pro",
      title: "Database Security Review",
      description:
        "Review of database security measures for the financial application.",
      date: "2025-04-08",
      startTime: "10:00:00",
      endTime: "11:30:00",
      duration: 90,
      status: "completed",
      type: "progress_review",
      isOnline: true,
      meetingUrl: "https://teams.microsoft.com/l/meeting/456",
      documents: [
        {
          id: 3,
          name: "Security Audit Report",
          type: "pdf",
          url: "#",
        },
      ],
      reminderSet: true,
      isPaid: true,
      amount: 250,
    },
    {
      id: 8,
      clientName: "Amanda Harris",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Marketing Experts",
      title: "Marketing Automation Strategy",
      description: "Planning the implementation of marketing automation tools.",
      date: "2025-04-19",
      startTime: "13:00:00",
      endTime: "14:00:00",
      duration: 60,
      status: "cancelled",
      type: "consultation",
      isOnline: true,
      meetingUrl: "https://zoom.us/j/998877665",
      notes: "Cancelled by client due to scheduling conflict",
      reminderSet: false,
    },
    {
      id: 9,
      clientName: "Thomas Wilson",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Wilson & Associates",
      title: "Legal Documentation System",
      description:
        "Discussion of requirements for the legal document management system.",
      date: "2025-04-15",
      startTime: "11:00:00",
      endTime: "12:00:00",
      duration: 60,
      status: "rescheduled",
      type: "consultation",
      isOnline: false,
      location: "456 Legal Avenue, Suite 300",
      notes: "Rescheduled from April 12 due to client request",
      reminderSet: true,
      isPaid: false,
    },
    {
      id: 10,
      clientName: "Lisa Brown",
      clientAvatar: "/api/placeholder/40/40",
      clientCompany: "Education First",
      title: "E-Learning Platform Review",
      description:
        "Review of current progress on the e-learning platform development.",
      date: "2025-04-14",
      startTime: "09:30:00",
      endTime: "10:30:00",
      duration: 60,
      status: "completed",
      type: "progress_review",
      isOnline: true,
      meetingUrl: "https://meet.google.com/lmn-opqr-stu",
      documents: [
        {
          id: 4,
          name: "Progress Report",
          type: "pdf",
          url: "#",
        },
      ],
      reminderSet: true,
      isPaid: true,
      amount: 150,
    },
  ];

  return mockAppointments;
};

// Fetch appointments (mock)
const fetchAppointments = async () => {
  try {
    isLoading.value = true;
    // Simulating API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    appointments.value = generateMockAppointments();
  } catch (error) {
    console.error("Error fetching appointments:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter appointments based on active tab and search query
const filteredAppointments = computed(() => {
  let result = [...appointments.value];

  // Apply status filter based on active tab
  if (activeTab.value !== "all") {
    result = result.filter(
      (appointment) => appointment.status === activeTab.value
    );
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (appointment) =>
        appointment.clientName.toLowerCase().includes(query) ||
        appointment.title.toLowerCase().includes(query) ||
        (appointment.clientCompany &&
          appointment.clientCompany.toLowerCase().includes(query))
    );
  }

  // Sort by date and time
  result.sort((a, b) => {
    const dateA = new Date(`${a.date}T${a.startTime}`);
    const dateB = new Date(`${b.date}T${b.startTime}`);
    return dateA.getTime() - dateB.getTime();
  });

  return result;
});

// Get appointments for today
const todayAppointments = computed(() => {
  const today = getCurrentDateString();
  return appointments.value
    .filter(
      (appointment) =>
        appointment.date === today && appointment.status === "upcoming"
    )
    .sort((a, b) => {
      return a.startTime.localeCompare(b.startTime);
    });
});

// Get appointments for current calendar view
const calendarViewAppointments = computed(() => {
  // In a real app, this would filter based on the current calendar view date range
  return appointments.value.filter(
    (appointment) =>
      appointment.status !== "cancelled" && appointment.status !== "completed"
  );
});

// Get appointment type badge classes
const getAppointmentTypeClasses = (type: AppointmentType) => {
  switch (type) {
    case "consultation":
      return "bg-indigo-100 text-indigo-800";
    case "project_kickoff":
      return "bg-green-100 text-green-800";
    case "progress_review":
      return "bg-blue-100 text-blue-800";
    case "client_meeting":
      return "bg-amber-100 text-amber-800";
    case "presentation":
      return "bg-purple-100 text-purple-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get appointment status badge classes
const getAppointmentStatusClasses = (status: AppointmentStatus) => {
  switch (status) {
    case "upcoming":
      return "bg-green-100 text-green-800";
    case "completed":
      return "bg-blue-100 text-blue-800";
    case "cancelled":
      return "bg-red-100 text-red-800";
    case "rescheduled":
      return "bg-amber-100 text-amber-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format appointment type display
const formatAppointmentType = (type: AppointmentType) => {
  return type.replace("_", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Calculate time remaining until appointment
const getTimeRemaining = (appointment: Appointment) => {
  const appointmentDate = new Date(
    `${appointment.date}T${appointment.startTime}`
  );
  const now = new Date();

  const diffMs = appointmentDate.getTime() - now.getTime();
  if (diffMs < 0) return "Passed";

  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor(
    (diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );
  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

  if (diffDays > 0) {
    return `${diffDays}d ${diffHours}h`;
  } else if (diffHours > 0) {
    return `${diffHours}h ${diffMinutes}m`;
  } else {
    return `${diffMinutes}m`;
  }
};

// View appointment details
const viewAppointmentDetails = (appointment: Appointment) => {
  selectedAppointment.value = appointment;
  showAppointmentDetailModal.value = true;
};

// Handle reschedule appointment
const rescheduleAppointment = () => {
  if (!selectedAppointment.value) return;

  // In a real app, this would make an API call
  const appointmentToUpdate = appointments.value.find(
    (a) => a.id === selectedAppointment.value!.id
  );
  if (appointmentToUpdate) {
    appointmentToUpdate.status = "rescheduled";
    // Additional logic for rescheduling would go here
  }

  showRescheduleModal.value = false;
  showAppointmentDetailModal.value = false;
};

// Handle cancel appointment
const cancelAppointment = () => {
  if (!selectedAppointment.value || !cancelReason.value) return;

  // In a real app, this would make an API call
  const appointmentToCancel = appointments.value.find(
    (a) => a.id === selectedAppointment.value!.id
  );
  if (appointmentToCancel) {
    appointmentToCancel.status = "cancelled";
    appointmentToCancel.notes = cancelReason.value;
  }

  showCancelModal.value = false;
  showAppointmentDetailModal.value = false;
  cancelReason.value = "";
};

// Handle create appointment
const createAppointment = () => {
  // Validate form data
  if (
    !newAppointment.value.clientName ||
    !newAppointment.value.title ||
    !newAppointment.value.date ||
    !newAppointment.value.startTime ||
    !newAppointment.value.endTime
  ) {
    // Show validation error
    return;
  }

  // In a real app, this would make an API call
  const newId = Math.max(...appointments.value.map((a) => a.id)) + 1;

  // Create new appointment
  const appointment: Appointment = {
    id: newId,
    clientName: newAppointment.value.clientName,
    clientAvatar: "/api/placeholder/40/40", // Default avatar
    clientCompany: "",
    title: newAppointment.value.title,
    description: newAppointment.value.description,
    date: newAppointment.value.date,
    startTime: newAppointment.value.startTime + ":00",
    endTime: newAppointment.value.endTime + ":00",
    duration: newAppointment.value.duration,
    status: "upcoming",
    type: newAppointment.value.type,
    isOnline: newAppointment.value.isOnline,
    location: newAppointment.value.isOnline
      ? ""
      : newAppointment.value.location,
    meetingUrl: newAppointment.value.isOnline
      ? newAppointment.value.meetingUrl
      : "",
    notes: newAppointment.value.notes,
    reminderSet: newAppointment.value.reminderSet,
    isPaid: false,
  };

  appointments.value.push(appointment);

  // Reset form and close modal
  newAppointment.value = {
    clientName: "",
    title: "",
    description: "",
    date: getCurrentDateString(),
    startTime: "09:00",
    endTime: "10:00",
    duration: 60,
    type: "consultation",
    isOnline: true,
    location: "",
    meetingUrl: "",
    notes: "",
    reminderSet: true,
  };

  showCreateModal.value = false;
};

// Handle time input change and calculate duration
const updateDuration = () => {
  if (newAppointment.value.startTime && newAppointment.value.endTime) {
    const start = new Date(`2000-01-01T${newAppointment.value.startTime}`);
    const end = new Date(`2000-01-01T${newAppointment.value.endTime}`);

    const diffMs = end.getTime() - start.getTime();
    const diffMinutes = Math.floor(diffMs / (1000 * 60));

    newAppointment.value.duration = diffMinutes > 0 ? diffMinutes : 60;
  }
};

// Toggle calendar/list view
const toggleView = () => {
  isCalendarView.value = !isCalendarView.value;
};

// Navigate to new appointment page
const navigateToNewAppointment = () => {
  router.push("/dashboard/consultant/appointments/schedule");
};

// Load data on mount
onMounted(() => {
  fetchAppointments();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Appointments</h1>
          <p class="text-gray-600">
            Manage your appointments with clients and team members
          </p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="toggleView"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              v-if="!isCalendarView"
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
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
              />
            </svg>
            {{ isCalendarView ? "List View" : "Calendar View" }}
          </button>
          <button
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
            Schedule Appointment
          </button>
        </div>
      </div>
    </div>

    <!-- Today's Appointments -->
    <div class="mb-8 bg-white rounded-lg shadow p-5 border border-gray-200">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-gray-900">Today's Appointments</h2>
        <div class="text-sm text-gray-500">
          {{ formatDate(getCurrentDateString()) }}
        </div>
      </div>

      <div v-if="isLoading" class="py-6 flex justify-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-600"
        ></div>
      </div>

      <div v-else-if="todayAppointments.length === 0" class="py-6 text-center">
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
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <p class="text-gray-600">No appointments scheduled for today.</p>
        <button
          @click="showCreateModal = true"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
          Schedule New Appointment
        </button>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="appointment in todayAppointments"
          :key="appointment.id"
          class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0 h-12 w-12 rounded-full overflow-hidden">
              <img
                :src="appointment.clientAvatar"
                :alt="appointment.clientName"
                class="h-full w-full object-cover"
              />
            </div>
            <div class="ml-4">
              <h3 class="text-sm font-medium text-gray-900">
                {{ appointment.title }}
              </h3>
              <div class="text-xs text-gray-500">
                {{ formatTime(appointment.startTime) }} -
                {{ formatTime(appointment.endTime) }}
                <span class="mx-2">•</span>
                <span class="capitalize">{{
                  appointment.isOnline ? "Online" : "In Person"
                }}</span>
                <span class="mx-2">•</span>
                <span>{{ appointment.clientName }}</span>
                <span v-if="appointment.clientCompany"
                  >, {{ appointment.clientCompany }}</span
                >
              </div>
            </div>
          </div>

          <div class="flex items-center">
            <div class="mr-4 text-center">
              <div class="text-sm font-medium text-gray-900">
                Time Remaining
              </div>
              <div class="text-sm text-indigo-600">
                {{ getTimeRemaining(appointment) }}
              </div>
            </div>
            <button
              @click="viewAppointmentDetails(appointment)"
              class="inline-flex items-center p-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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

    <!-- Main Content - Calendar or List View -->
    <div
      v-if="isCalendarView"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <!-- Calendar View Placeholder - In a real app, this would be a full calendar component -->
      <div class="p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-2">
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
            <h2 class="text-lg font-medium text-gray-900">April 2025</h2>
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
          <div class="flex items-center space-x-2">
            <button
              class="px-3 py-1 text-sm font-medium rounded-md"
              :class="
                currentView === 'month'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
              "
              @click="currentView = 'month'"
            >
              Month
            </button>
            <button
              class="px-3 py-1 text-sm font-medium rounded-md"
              :class="
                currentView === 'week'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
              "
              @click="currentView = 'week'"
            >
              Week
            </button>
            <button
              class="px-3 py-1 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-md"
              @click="currentCalendarDate = getCurrentDateString()"
            >
              Today
            </button>
          </div>
        </div>

        <!-- Calendar Content -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
          <!-- Calendar Header (Days of Week) -->
          <div
            class="grid grid-cols-7 text-center py-2 border-b border-gray-200 bg-gray-50"
          >
            <div
              v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
              :key="day"
              class="text-sm font-medium text-gray-500"
            >
              {{ day }}
            </div>
          </div>

          <!-- Calendar Grid for Month View -->
          <div
            v-if="currentView === 'month'"
            class="grid grid-cols-7 grid-rows-5 gap-px bg-gray-200"
          >
            <div v-for="i in 35" :key="i" class="bg-white min-h-[100px] p-2">
              <div class="flex justify-between items-center mb-1">
                <span
                  class="text-sm font-medium"
                  :class="
                    i === 15
                      ? 'text-indigo-600 bg-indigo-100 w-6 h-6 flex items-center justify-center rounded-full'
                      : ''
                  "
                >
                  {{ ((i - 1) % 31) + 1 }}
                </span>
                <button
                  v-if="i === 15 || i === 17 || i === 22"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                    />
                  </svg>
                </button>
              </div>

              <!-- Example appointments on specific days -->
              <div v-if="i === 15" class="mt-1">
                <div
                  class="text-xs bg-indigo-100 text-indigo-800 rounded px-1 py-0.5 mb-1 truncate"
                >
                  9:00 AM - Initial Consult
                </div>
                <div
                  class="text-xs bg-green-100 text-green-800 rounded px-1 py-0.5 truncate"
                >
                  2:00 PM - Project Kickoff
                </div>
              </div>
              <div v-if="i === 17" class="mt-1">
                <div
                  class="text-xs bg-amber-100 text-amber-800 rounded px-1 py-0.5 truncate"
                >
                  11:00 AM - Client Meeting
                </div>
              </div>
              <div v-if="i === 22" class="mt-1">
                <div
                  class="text-xs bg-purple-100 text-purple-800 rounded px-1 py-0.5 truncate"
                >
                  1:00 PM - Presentation
                </div>
              </div>
            </div>
          </div>

          <!-- Calendar Grid for Week View -->
          <div
            v-else-if="currentView === 'week'"
            class="grid grid-cols-7 gap-px bg-gray-200"
          >
            <!-- Week days header -->
            <div
              v-for="(day, index) in [
                'Sun 13',
                'Mon 14',
                'Tue 15',
                'Wed 16',
                'Thu 17',
                'Fri 18',
                'Sat 19',
              ]"
              :key="index"
              class="bg-white p-2 text-center border-b border-gray-200"
              :class="{ 'bg-indigo-50': index === 3 }"
            >
              <div
                class="text-sm font-medium"
                :class="{ 'text-indigo-600': index === 3 }"
              >
                {{ day }}
              </div>
            </div>

            <!-- Time slots -->
            <template
              v-for="hour in [9, 10, 11, 12, 13, 14, 15, 16, 17]"
              :key="hour"
            >
              <div
                v-for="day in 7"
                :key="`${hour}-${day}`"
                class="bg-white border-t border-gray-100 p-1 h-16 relative"
                :class="{ 'bg-indigo-50': day === 4 }"
              >
                <div
                  v-if="day === 1"
                  class="absolute -left-16 top-1/2 transform -translate-y-1/2 w-14 text-right pr-2 text-xs text-gray-500"
                >
                  {{
                    hour === 12
                      ? "12 PM"
                      : hour < 12
                      ? `${hour} AM`
                      : `${hour - 12} PM`
                  }}
                </div>

                <!-- Example appointments on specific time slots -->
                <div
                  v-if="hour === 9 && day === 4"
                  class="absolute inset-0 m-1 bg-indigo-100 rounded p-1 text-xs text-indigo-800 border-l-4 border-indigo-500"
                >
                  <div class="font-medium">Initial Consultation</div>
                  <div>John Smith</div>
                </div>
                <div
                  v-if="hour === 14 && day === 4"
                  class="absolute inset-0 m-1 bg-green-100 rounded p-1 text-xs text-green-800 border-l-4 border-green-500"
                >
                  <div class="font-medium">Project Kickoff</div>
                  <div>Emily Johnson</div>
                </div>
                <div
                  v-if="hour === 11 && day === 6"
                  class="absolute inset-0 m-1 bg-amber-100 rounded p-1 text-xs text-amber-800 border-l-4 border-amber-500"
                >
                  <div class="font-medium">Client Meeting</div>
                  <div>Michael Chen</div>
                </div>
                <div
                  v-if="hour === 13 && day === 5"
                  class="absolute inset-0 m-1 bg-purple-100 rounded p-1 text-xs text-purple-800 border-l-4 border-purple-500"
                >
                  <div class="font-medium">Presentation</div>
                  <div>Sarah Williams</div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
        >
          <!-- Tab Navigation -->
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
              @click="activeTab = 'rescheduled'"
              class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
              :class="
                activeTab === 'rescheduled'
                  ? 'bg-amber-100 text-amber-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Rescheduled
            </button>
            <button
              @click="activeTab = 'all'"
              class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
              :class="
                activeTab === 'all'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              All
            </button>
          </div>

          <!-- Search Input -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search appointments..."
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

      <!-- Loading Skeleton -->
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
              <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-1/3"></div>
            </div>
            <div class="flex space-x-2">
              <div class="h-8 w-20 bg-gray-200 rounded-md"></div>
              <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Appointments List -->
      <div
        v-else-if="filteredAppointments.length > 0"
        class="bg-white rounded-lg shadow border border-gray-200"
      >
        <div
          v-for="appointment in filteredAppointments"
          :key="`appointment-${appointment.id}`"
          class="p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors"
        >
          <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
          >
            <!-- Appointment info -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <img
                  :src="appointment.clientAvatar"
                  :alt="appointment.clientName"
                  class="h-10 w-10 rounded-full"
                />
              </div>
              <div>
                <h3 class="text-base font-medium text-gray-900">
                  {{ appointment.title }}
                </h3>
                <div class="text-sm text-gray-500">
                  {{ formatDate(appointment.date) }} •
                  {{ formatTime(appointment.startTime) }} -
                  {{ formatTime(appointment.endTime) }}
                </div>
                <div class="flex items-center mt-1 space-x-2">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="getAppointmentTypeClasses(appointment.type)"
                  >
                    {{ formatAppointmentType(appointment.type) }}
                  </span>
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="getAppointmentStatusClasses(appointment.status)"
                  >
                    {{ appointment.status }}
                  </span>
                  <span class="inline-flex items-center text-xs text-gray-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      :class="
                        appointment.isOnline
                          ? 'text-green-500'
                          : 'text-blue-500'
                      "
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        v-if="appointment.isOnline"
                        fill-rule="evenodd"
                        d="M7 2a1 1 0 00-.707 1.707L7 4.414v3.758a1 1 0 01-.293.707l-4 4C.817 14.769 2.156 18 4.828 18h10.343c2.673 0 4.012-3.231 2.122-5.121l-4-4A1 1 0 0113 8.172V4.414l.707-.707A1 1 0 0013 2H7zm2 6.172V4h2v4.172a3 3 0 00.879 2.12l1.168 1.168a4 4 0 00-2.925 0l1.168-1.168a3 3 0 00.88-2.12z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ appointment.isOnline ? "Online" : "In Person" }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Client Info -->
            <div class="flex items-center md:w-64">
              <div class="text-sm">
                <div class="font-medium text-gray-900">
                  {{ appointment.clientName }}
                </div>
                <div v-if="appointment.clientCompany" class="text-gray-500">
                  {{ appointment.clientCompany }}
                </div>
              </div>
            </div>

            <!-- Time Remaining & Actions -->
            <div class="flex items-center justify-end space-x-4">
              <div
                v-if="appointment.status === 'upcoming'"
                class="text-center md:w-32"
              >
                <div class="text-xs text-gray-500">Time Remaining</div>
                <div
                  class="text-sm font-medium"
                  :class="
                    getTimeRemaining(appointment) === 'Today'
                      ? 'text-green-600'
                      : 'text-gray-900'
                  "
                >
                  {{ getTimeRemaining(appointment) }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-1">
                <button
                  @click="viewAppointmentDetails(appointment)"
                  class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600"
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
                  v-if="appointment.status === 'upcoming'"
                  @click="
                    selectedAppointment = appointment;
                    showRescheduleModal = true;
                  "
                  class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-amber-600"
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
                  v-if="appointment.status === 'upcoming'"
                  @click="
                    selectedAppointment = appointment;
                    showCancelModal = true;
                  "
                  class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-red-600"
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
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
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
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          No appointments found
        </h3>
        <p class="text-gray-600 mb-6">
          {{
            searchQuery
              ? "No appointments match your search criteria."
              : "You haven't scheduled any appointments yet."
          }}
        </p>
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
          Schedule Your First Appointment
        </button>
      </div>
    </div>

    <!-- Appointment Details Modal -->
    <div
      v-if="showAppointmentDetailModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-lg w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="relative">
          <!-- Header with color based on appointment type -->
          <div
            class="px-6 py-4"
            :class="{
              'bg-indigo-500': selectedAppointment?.type === 'consultation',
              'bg-green-500': selectedAppointment?.type === 'project_kickoff',
              'bg-blue-500': selectedAppointment?.type === 'progress_review',
              'bg-amber-500': selectedAppointment?.type === 'client_meeting',
              'bg-purple-500': selectedAppointment?.type === 'presentation',
            }"
          >
            <div class="flex justify-between items-start">
              <h3 class="text-lg font-bold text-white">
                {{ selectedAppointment?.title }}
              </h3>
              <button
                @click="showAppointmentDetailModal = false"
                class="text-white hover:text-gray-200"
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
            <div class="text-white opacity-90 mt-1">
              {{ formatDate(selectedAppointment?.date || "") }} •
              {{ formatTime(selectedAppointment?.startTime || "") }} -
              {{ formatTime(selectedAppointment?.endTime || "") }}
            </div>
          </div>

          <!-- Content -->
          <div class="px-6 py-4">
            <!-- Client info -->
            <div class="flex items-center mb-4">
              <img
                :src="selectedAppointment?.clientAvatar"
                :alt="selectedAppointment?.clientName"
                class="h-12 w-12 rounded-full mr-4 object-cover"
              />
              <div>
                <div class="font-medium text-gray-900">
                  {{ selectedAppointment?.clientName }}
                </div>
                <div
                  v-if="selectedAppointment?.clientCompany"
                  class="text-gray-500"
                >
                  {{ selectedAppointment?.clientCompany }}
                </div>
              </div>
            </div>

            <!-- Status and type -->
            <div class="flex space-x-3 mb-4">
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                :class="
                  selectedAppointment
                    ? getAppointmentTypeClasses(selectedAppointment.type)
                    : ''
                "
              >
                {{
                  selectedAppointment
                    ? formatAppointmentType(selectedAppointment.type)
                    : ""
                }}
              </span>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                :class="
                  selectedAppointment
                    ? getAppointmentStatusClasses(selectedAppointment.status)
                    : ''
                "
              >
                {{ selectedAppointment?.status }}
              </span>
            </div>

            <!-- Description -->
            <div v-if="selectedAppointment?.description" class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">
                Description
              </h4>
              <p class="text-gray-600 text-sm">
                {{ selectedAppointment.description }}
              </p>
            </div>

            <!-- Location -->
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">Location</h4>
              <div class="flex items-center text-sm">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  :class="
                    selectedAppointment?.isOnline
                      ? 'text-green-500'
                      : 'text-blue-500'
                  "
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    v-if="selectedAppointment?.isOnline"
                    fill-rule="evenodd"
                    d="M7 2a1 1 0 00-.707 1.707L7 4.414v3.758a1 1 0 01-.293.707l-4 4C.817 14.769 2.156 18 4.828 18h10.343c2.673 0 4.012-3.231 2.122-5.121l-4-4A1 1 0 0113 8.172V4.414l.707-.707A1 1 0 0013 2H7zm2 6.172V4h2v4.172a3 3 0 00.879 2.12l1.168 1.168a4 4 0 00-2.925 0l1.168-1.168a3 3 0 00.88-2.12z"
                    clip-rule="evenodd"
                  />
                  <path
                    v-else
                    fill-rule="evenodd"
                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span v-if="selectedAppointment?.isOnline">
                  Online Meeting
                  <a
                    v-if="selectedAppointment?.meetingUrl"
                    :href="selectedAppointment.meetingUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="ml-2 text-indigo-600 hover:text-indigo-800"
                  >
                    Join Meeting
                  </a>
                </span>
                <span v-else>
                  {{
                    selectedAppointment?.location || "Location not specified"
                  }}
                </span>
              </div>
            </div>

            <!-- Duration -->
            <div class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">Duration</h4>
              <p class="text-sm text-gray-600">
                {{ selectedAppointment?.duration }} minutes
              </p>
            </div>

            <!-- Payment Status -->
            <div v-if="selectedAppointment?.isPaid !== undefined" class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">
                Payment Status
              </h4>
              <p class="text-sm flex items-center">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-2"
                  :class="
                    selectedAppointment?.isPaid
                      ? 'bg-green-100 text-green-800'
                      : 'bg-amber-100 text-amber-800'
                  "
                >
                  {{ selectedAppointment?.isPaid ? "Paid" : "Unpaid" }}
                </span>
                <span v-if="selectedAppointment?.amount">
                  ${{ selectedAppointment.amount.toFixed(2) }}
                </span>
              </p>
            </div>

            <!-- Notes -->
            <div v-if="selectedAppointment?.notes" class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">Notes</h4>
              <p class="text-sm text-gray-600">
                {{ selectedAppointment.notes }}
              </p>
            </div>

            <!-- Documents -->
            <div
              v-if="
                selectedAppointment?.documents &&
                selectedAppointment.documents.length > 0
              "
              class="mb-4"
            >
              <h4 class="text-sm font-medium text-gray-900 mb-1">Documents</h4>
              <ul class="text-sm space-y-1">
                <li
                  v-for="doc in selectedAppointment.documents"
                  :key="doc.id"
                  class="flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <a
                    :href="doc.url"
                    class="text-indigo-600 hover:text-indigo-800"
                    target="_blank"
                  >
                    {{ doc.name }}
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <!-- Actions -->
          <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <button
              v-if="selectedAppointment?.status === 'upcoming'"
              @click="
                showAppointmentDetailModal = false;
                showCancelModal = true;
              "
              class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
            >
              Cancel Appointment
            </button>
            <button
              v-if="selectedAppointment?.status === 'upcoming'"
              @click="
                showAppointmentDetailModal = false;
                showRescheduleModal = true;
              "
              class="inline-flex items-center px-4 py-2 border border-amber-300 rounded-md shadow-sm text-sm font-medium text-amber-700 bg-white hover:bg-amber-50"
            >
              Reschedule
            </button>
            <button
              @click="showAppointmentDetailModal = false"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reschedule Modal -->
    <div
      v-if="showRescheduleModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl"
      >
        <div class="px-6 py-4 bg-amber-500">
          <div class="flex justify-between items-start">
            <h3 class="text-lg font-bold text-white">Reschedule Appointment</h3>
            <button
              @click="showRescheduleModal = false"
              class="text-white hover:text-gray-200"
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

        <div class="px-6 py-4">
          <p class="text-gray-700 mb-4">
            You are about to reschedule appointment:
            <span class="font-medium">{{ selectedAppointment?.title }}</span>
            with
            <span class="font-medium">{{
              selectedAppointment?.clientName
            }}</span>
          </p>

          <div class="space-y-4">
            <div>
              <label
                for="reschedule-date"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                New Date
              </label>
              <input
                type="date"
                id="reschedule-date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                :min="getCurrentDateString()"
                :value="selectedAppointment?.date"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label
                  for="reschedule-start-time"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Start Time
                </label>
                <input
                  type="time"
                  id="reschedule-start-time"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  :value="selectedAppointment?.startTime.substring(0, 5)"
                />
              </div>

              <div>
                <label
                  for="reschedule-end-time"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  End Time
                </label>
                <input
                  type="time"
                  id="reschedule-end-time"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  :value="selectedAppointment?.endTime.substring(0, 5)"
                />
              </div>
            </div>

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

            <div class="flex items-center">
              <input
                id="notify-client"
                type="checkbox"
                checked
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label
                for="notify-client"
                class="ml-2 block text-sm text-gray-700"
              >
                Notify client about this change
              </label>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
          <button
            @click="showRescheduleModal = false"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="rescheduleAppointment"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
          >
            Reschedule
          </button>
        </div>
      </div>
    </div>

    <!-- Cancel Modal -->
    <div
      v-if="showCancelModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl"
      >
        <div class="px-6 py-4 bg-red-500">
          <div class="flex justify-between items-start">
            <h3 class="text-lg font-bold text-white">Cancel Appointment</h3>
            <button
              @click="showCancelModal = false"
              class="text-white hover:text-gray-200"
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

        <div class="px-6 py-4">
          <div class="flex items-center justify-center mb-4 text-red-600">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-12 w-12"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
          </div>

          <p class="text-center text-gray-900 font-medium mb-4">
            Are you sure you want to cancel this appointment?
          </p>

          <p class="text-gray-700 mb-4">
            <span class="font-medium">{{ selectedAppointment?.title }}</span>
            with
            <span class="font-medium">{{
              selectedAppointment?.clientName
            }}</span>
            on {{ formatDate(selectedAppointment?.date || "") }} at
            {{ formatTime(selectedAppointment?.startTime || "") }}
          </p>

          <div>
            <label
              for="cancel-reason"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Reason for Cancellation
            </label>
            <textarea
              id="cancel-reason"
              v-model="cancelReason"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Please provide a reason for cancellation..."
            ></textarea>
          </div>

          <div class="mt-4 flex items-center">
            <input
              id="notify-client-cancel"
              type="checkbox"
              checked
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label
              for="notify-client-cancel"
              class="ml-2 block text-sm text-gray-700"
            >
              Notify client about this cancellation
            </label>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
          <button
            @click="showCancelModal = false"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Go Back
          </button>
          <button
            @click="cancelAppointment"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            :disabled="!cancelReason"
            :class="{ 'opacity-50 cursor-not-allowed': !cancelReason }"
          >
            Cancel Appointment
          </button>
        </div>
      </div>
    </div>

    <!-- Create Appointment Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-2xl w-full overflow-hidden shadow-xl"
      >
        <div class="px-6 py-4 bg-indigo-600">
          <div class="flex justify-between items-start">
            <h3 class="text-lg font-bold text-white">
              Schedule New Appointment
            </h3>
            <button
              @click="showCreateModal = false"
              class="text-white hover:text-gray-200"
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

        <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
          <div class="space-y-4">
            <!-- Basic Information -->
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3 pb-2 border-b">
                Basic Information
              </h4>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label
                    for="client-name"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Client Name *
                  </label>
                  <input
                    id="client-name"
                    v-model="newAppointment.clientName"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter client name"
                    required
                  />
                </div>

                <div>
                  <label
                    for="appointment-type"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Appointment Type *
                  </label>
                  <select
                    id="appointment-type"
                    v-model="newAppointment.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  >
                    <option value="consultation">Consultation</option>
                    <option value="project_kickoff">Project Kickoff</option>
                    <option value="progress_review">Progress Review</option>
                    <option value="client_meeting">Client Meeting</option>
                    <option value="presentation">Presentation</option>
                  </select>
                </div>

                <div class="sm:col-span-2">
                  <label
                    for="appointment-title"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Title/Subject *
                  </label>
                  <input
                    id="appointment-title"
                    v-model="newAppointment.title"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter appointment title"
                    required
                  />
                </div>

                <div class="sm:col-span-2">
                  <label
                    for="appointment-description"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Description
                  </label>
                  <textarea
                    id="appointment-description"
                    v-model="newAppointment.description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Describe the purpose of the appointment..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Date and Time -->
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3 pb-2 border-b">
                Date and Time
              </h4>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                  <label
                    for="appointment-date"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Date *
                  </label>
                  <input
                    id="appointment-date"
                    v-model="newAppointment.date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :min="getCurrentDateString()"
                    required
                  />
                </div>

                <div>
                  <label
                    for="appointment-start-time"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Start Time *
                  </label>
                  <input
                    id="appointment-start-time"
                    v-model="newAppointment.startTime"
                    type="time"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                    @change="updateDuration"
                  />
                </div>

                <div>
                  <label
                    for="appointment-end-time"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    End Time *
                  </label>
                  <input
                    id="appointment-end-time"
                    v-model="newAppointment.endTime"
                    type="time"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                    @change="updateDuration"
                  />
                </div>

                <div>
                  <label
                    for="appointment-duration"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Duration (minutes)
                  </label>
                  <input
                    id="appointment-duration"
                    v-model="newAppointment.duration"
                    type="number"
                    min="15"
                    step="15"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-gray-100"
                    readonly
                  />
                </div>
              </div>
            </div>

            <!-- Location -->
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3 pb-2 border-b">
                Location
              </h4>

              <div class="grid grid-cols-1 gap-4">
                <div class="flex items-center space-x-4">
                  <div class="flex items-center">
                    <input
                      id="location-online"
                      v-model="newAppointment.isOnline"
                      type="radio"
                      :value="true"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <label
                      for="location-online"
                      class="ml-2 block text-sm text-gray-700"
                    >
                      Online Meeting
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="location-in-person"
                      v-model="newAppointment.isOnline"
                      type="radio"
                      :value="false"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                    />
                    <label
                      for="location-in-person"
                      class="ml-2 block text-sm text-gray-700"
                    >
                      In Person
                    </label>
                  </div>
                </div>

                <div v-if="newAppointment.isOnline">
                  <label
                    for="meeting-url"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Meeting URL
                  </label>
                  <input
                    id="meeting-url"
                    v-model="newAppointment.meetingUrl"
                    type="url"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Zoom, Google Meet, or Teams URL"
                  />
                </div>

                <div v-else>
                  <label
                    for="physical-location"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Physical Location
                  </label>
                  <input
                    id="physical-location"
                    v-model="newAppointment.location"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Office, client location, etc."
                  />
                </div>
              </div>
            </div>

            <!-- Additional Information -->
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3 pb-2 border-b">
                Additional Information
              </h4>

              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label
                    for="appointment-notes"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Notes
                  </label>
                  <textarea
                    id="appointment-notes"
                    v-model="newAppointment.notes"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Additional notes for your reference..."
                  ></textarea>
                </div>

                <div class="flex items-center">
                  <input
                    id="set-reminder"
                    v-model="newAppointment.reminderSet"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label
                    for="set-reminder"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    Set reminder notification for this appointment
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="notify-client-new"
                    type="checkbox"
                    checked
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label
                    for="notify-client-new"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    Send invitation to client
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
          <button
            @click="showCreateModal = false"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            @click="createAppointment"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Schedule Appointment
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

/* Calendar view styles */
.day-cell:hover {
  background-color: rgba(79, 70, 229, 0.05);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .day-cell {
    min-height: 80px;
  }
}
</style>
