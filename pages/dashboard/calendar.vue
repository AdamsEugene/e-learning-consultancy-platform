<!-- pages/dashboard/calendar.vue -->
<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";

// Components

// Set page layout
definePageMeta({
  layout: "dashboard",
});

// Page meta
useHead({
  title: "Calendar - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Manage your schedule, classes, meetings, and deadlines in one place",
    },
  ],
});

// User type from store or props - in a real app, this would come from auth
const userType = ref<
  "student" | "instructor" | "client" | "consultant" | "admin"
>("student");

// State
const events = ref([]);
const isLoading = ref(true);
const showCreateEventModal = ref(false);
const newEventDate = ref(new Date());
const formError = ref("");

// Filter state
const selectedEventTypes = ref<string[]>([
  "course",
  "meeting",
  "assignment",
  "reminder",
  "consultation",
  "deadline",
]);

// Creating new event
const newEvent = ref({
  title: "",
  description: "",
  startTime: "",
  endTime: "",
  type: "meeting",
  location: "",
  url: "",
  courseId: "",
  projectId: "",
});

// Toast notification state
const toast = ref({
  show: false,
  message: "",
  type: "success",
});

// Computed properties
const calendarEventTypes = computed(() => {
  // Different event types depending on user role
  if (userType.value === "student") {
    return [
      { value: "course", label: "Course" },
      { value: "meeting", label: "Meeting" },
      { value: "assignment", label: "Assignment" },
      { value: "reminder", label: "Reminder" },
      { value: "deadline", label: "Deadline" },
    ];
  } else if (userType.value === "instructor") {
    return [
      { value: "course", label: "Course" },
      { value: "meeting", label: "Meeting" },
      { value: "assignment", label: "Assignment" },
      { value: "reminder", label: "Reminder" },
      { value: "office_hours", label: "Office Hours" },
    ];
  } else if (userType.value === "consultant" || userType.value === "client") {
    return [
      { value: "consultation", label: "Consultation" },
      { value: "meeting", label: "Meeting" },
      { value: "deadline", label: "Deadline" },
      { value: "reminder", label: "Reminder" },
      { value: "project_milestone", label: "Project Milestone" },
    ];
  } else {
    // Admin has access to all event types
    return [
      { value: "course", label: "Course" },
      { value: "meeting", label: "Meeting" },
      { value: "assignment", label: "Assignment" },
      { value: "reminder", label: "Reminder" },
      { value: "consultation", label: "Consultation" },
      { value: "deadline", label: "Deadline" },
      { value: "office_hours", label: "Office Hours" },
      { value: "project_milestone", label: "Project Milestone" },
    ];
  }
});

// Format date for input fields
const formatDateForInput = (date: Date) => {
  return format(date, "yyyy-MM-dd'T'HH:mm");
};

// Methods for calendar interactions
const onEventClick = (event) => {
  console.log("Event clicked:", event);
  // Additional handling if needed
};

const onEventCreate = ({ date, view }) => {
  // Set up the new event with the date
  newEventDate.value = date;

  // Default start/end time based on the date
  const startTime = new Date(date);
  const endTime = new Date(date);
  endTime.setHours(endTime.getHours() + 1);

  newEvent.value = {
    title: "",
    description: "",
    startTime: formatDateForInput(startTime),
    endTime: formatDateForInput(endTime),
    type: "meeting",
    location: "",
    url: "",
    courseId: "",
    projectId: "",
  };

  // Show the create event modal
  showCreateEventModal.value = true;
};

const onReminderSet = (eventId, reminder) => {
  // Show success toast
  showToast("Reminder set successfully", "success");

  // In a real app, this would send data to an API
  console.log("Reminder set for event:", eventId, reminder);
};

const onReminderCancel = (eventId) => {
  // Show success toast
  showToast("Reminder cancelled", "success");

  // In a real app, this would send data to an API
  console.log("Reminder cancelled for event:", eventId);
};

const onViewChange = (view) => {
  // In a real app, you might want to persist this preference
  console.log("View changed to:", view);
};

const onDateChange = (date) => {
  // In a real app, you might want to fetch events for the new date range
  console.log("Date changed:", date);
};

const onEventUpdate = (event) => {
  // Update the event in the local state
  const index = events.value.findIndex((e) => e.id === event.id);
  if (index !== -1) {
    events.value[index] = event;

    // Show success toast
    showToast("Event updated successfully", "success");

    // In a real app, this would send data to an API
    console.log("Event updated:", event);
  }
};

// Toggle event type filter
const toggleEventTypeFilter = (type) => {
  if (selectedEventTypes.value.includes(type)) {
    // Remove type if already selected
    selectedEventTypes.value = selectedEventTypes.value.filter(
      (t) => t !== type
    );
  } else {
    // Add type if not selected
    selectedEventTypes.value.push(type);
  }
};

// Create new event
const submitNewEvent = () => {
  // Validate form
  if (!newEvent.value.title) {
    formError.value = "Please enter a title";
    return;
  }

  if (!newEvent.value.startTime || !newEvent.value.endTime) {
    formError.value = "Please specify start and end times";
    return;
  }

  // Create a unique ID
  const eventId = `event-${Date.now()}`;

  // Create the new event object
  const event = {
    id: eventId,
    title: newEvent.value.title,
    description: newEvent.value.description,
    startTime: new Date(newEvent.value.startTime).toISOString(),
    endTime: new Date(newEvent.value.endTime).toISOString(),
    type: newEvent.value.type,
    location: newEvent.value.location,
    url: newEvent.value.url,
    isCompleted: false,
  };

  // Add courseId or projectId if provided
  if (newEvent.value.courseId) {
    event.courseId = parseInt(newEvent.value.courseId, 10);
  }

  if (newEvent.value.projectId) {
    event.projectId = parseInt(newEvent.value.projectId, 10);
  }

  // Add the event to the local state
  events.value.push(event);

  // Show success toast
  showToast("Event created successfully", "success");

  // Reset and close the modal
  resetNewEventForm();
  showCreateEventModal.value = false;

  // In a real app, this would send data to an API
  console.log("New event created:", event);
};

// Reset the new event form
const resetNewEventForm = () => {
  formError.value = "";
  newEvent.value = {
    title: "",
    description: "",
    startTime: "",
    endTime: "",
    type: "meeting",
    location: "",
    url: "",
    courseId: "",
    projectId: "",
  };
};

// Show toast message
const showToast = (message, type = "success") => {
  toast.value = {
    show: true,
    message,
    type,
  };

  // Hide toast after 3 seconds
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
};

// Filter events based on selected types
const filteredEvents = computed(() => {
  return events.value.filter((event) =>
    selectedEventTypes.value.includes(event.type)
  );
});

// Fetch events
const fetchEvents = async () => {
  isLoading.value = true;

  try {
    // In a real app, this would be an API call
    // For demo, we'll create mock data
    await new Promise((resolve) => setTimeout(resolve, 1000));

    const today = new Date();

    // Mock events
    const mockEvents = [
      // Course events
      {
        id: "event-1",
        title: "Web Development Fundamentals",
        description: "Introduction to HTML, CSS, and JavaScript",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate(),
          10,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate(),
          12,
          0
        ).toISOString(),
        type: "course",
        location: "Virtual Classroom 1",
        courseId: 101,
        isCompleted: false,
      },
      {
        id: "event-2",
        title: "Advanced JavaScript Workshop",
        description: "Deep dive into ES6+ features and patterns",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 1,
          14,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 1,
          16,
          0
        ).toISOString(),
        type: "course",
        location: "Virtual Classroom 2",
        courseId: 102,
        isCompleted: false,
      },

      // Assignment events
      {
        id: "event-3",
        title: "Submit React Project",
        description: "Final project submission for the React course",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 3,
          23,
          59
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 3,
          23,
          59
        ).toISOString(),
        type: "assignment",
        courseId: 103,
        isCompleted: false,
      },

      // Meeting events
      {
        id: "event-4",
        title: "Study Group Meeting",
        description: "Weekly study group for JavaScript course",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 2,
          18,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 2,
          19,
          30
        ).toISOString(),
        type: "meeting",
        location: "Discord - JavaScript Channel",
        url: "https://discord.com/channels/123456789",
        isCompleted: false,
      },

      // Consultation events (for consultants/clients)
      {
        id: "event-5",
        title: "Client Onboarding Session",
        description: "Initial consultation for new client project",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() - 1,
          11,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() - 1,
          12,
          30
        ).toISOString(),
        type: "consultation",
        location: "Zoom Meeting Room",
        url: "https://zoom.us/j/123456789",
        projectId: 201,
        isCompleted: true,
        attendees: [
          {
            id: "user-1",
            name: "John Smith",
            avatar: "/api/placeholder/40/40",
            role: "Client",
          },
          {
            id: "user-2",
            name: "Maria Garcia",
            avatar: "/api/placeholder/40/40",
            role: "Consultant",
          },
        ],
      },

      // Deadline events
      {
        id: "event-6",
        title: "Project Proposal Deadline",
        description: "Submit the initial project proposal document",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 5,
          17,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 5,
          17,
          0
        ).toISOString(),
        type: "deadline",
        projectId: 202,
        isCompleted: false,
      },

      // Reminder events
      {
        id: "event-7",
        title: "Prepare for Tomorrow's Meeting",
        description: "Review project documentation and prepare questions",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 1,
          9,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 1,
          9,
          30
        ).toISOString(),
        type: "reminder",
        isCompleted: false,
      },

      // Office hours events (for instructors)
      {
        id: "event-8",
        title: "Office Hours",
        description: "Open office hours for student questions and help",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 2,
          13,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 2,
          15,
          0
        ).toISOString(),
        type: "office_hours",
        location: "Virtual Office Room",
        url: "https://meet.google.com/abc-defg-hij",
        isCompleted: false,
      },

      // Project milestone events (for consultants/clients)
      {
        id: "event-9",
        title: "Project Milestone: Frontend Prototype",
        description: "Complete and present the frontend prototype to client",
        startTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 7,
          10,
          0
        ).toISOString(),
        endTime: new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() + 7,
          11,
          30
        ).toISOString(),
        type: "project_milestone",
        projectId: 203,
        isCompleted: false,
      },
    ];

    // Set the events
    events.value = mockEvents;
  } catch (error) {
    console.error("Error fetching events:", error);
    // Show error toast
    showToast("Failed to load events. Please try again.", "error");
  } finally {
    isLoading.value = false;
  }
};

// Get event type label
const getEventTypeLabel = (type) => {
  const eventType = calendarEventTypes.value.find((et) => et.value === type);
  return eventType ? eventType.label : type;
};

// Get color class for event type
const getEventTypeColorClass = (type) => {
  switch (type) {
    case "course":
      return "bg-blue-500 text-white";
    case "meeting":
      return "bg-purple-500 text-white";
    case "assignment":
      return "bg-amber-500 text-white";
    case "reminder":
      return "bg-green-500 text-white";
    case "consultation":
      return "bg-indigo-600 text-white";
    case "deadline":
      return "bg-red-500 text-white";
    case "office_hours":
      return "bg-teal-500 text-white";
    case "project_milestone":
      return "bg-pink-500 text-white";
    default:
      return "bg-gray-500 text-white";
  }
};

// Fetch events on component mount
onMounted(() => {
  fetchEvents();
});

// Watch for changes in selected event types to update filtered events
watch(selectedEventTypes, () => {
  // In a real app, you might want to persist these preferences
  console.log("Event filters updated:", selectedEventTypes.value);
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Calendar</h1>
      <p class="text-gray-600">
        Manage your schedule, classes, meetings, and deadlines
      </p>
    </div>

    <!-- Calendar filters -->
    <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
      <div class="mb-4">
        <h2 class="text-lg font-medium text-gray-900 mb-2">Event Filters</h2>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="eventType in calendarEventTypes"
            :key="eventType.value"
            class="px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 flex items-center space-x-1"
            :class="[
              selectedEventTypes.includes(eventType.value)
                ? getEventTypeColorClass(eventType.value)
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
            @click="toggleEventTypeFilter(eventType.value)"
          >
            <span v-if="selectedEventTypes.includes(eventType.value)">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
            <span v-else>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
            <span>{{ eventType.label }}</span>
          </button>
        </div>
      </div>

      <!-- Add Event button -->
      <button
        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="showCreateEventModal = true"
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
        Add Event
      </button>
    </div>

    <!-- Calendar component -->
    <DashboardCalendar
      :events="filteredEvents"
      :user-type="userType"
      initial-view="month"
      :highlight-today="true"
      :show-weekends="true"
      :allow-create="true"
      :allow-edit="true"
      :is-loading="isLoading"
      @event-click="onEventClick"
      @event-create="onEventCreate"
      @reminder-set="onReminderSet"
      @reminder-cancel="onReminderCancel"
      @view-change="onViewChange"
      @date-change="onDateChange"
      @event-update="onEventUpdate"
    />

    <!-- Create Event Modal -->
    <div
      v-if="showCreateEventModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="showCreateEventModal = false"
        />

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
          <div>
            <div class="flex justify-between items-center mb-5">
              <h3
                id="modal-title"
                class="text-lg leading-6 font-medium text-gray-900"
              >
                Create Event
              </h3>
              <button
                class="text-gray-400 hover:text-gray-500 transition-colors duration-200"
                @click="showCreateEventModal = false"
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

            <!-- Error message -->
            <div
              v-if="formError"
              class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md"
            >
              {{ formError }}
            </div>

            <!-- Event form -->
            <form @submit.prevent="submitNewEvent">
              <div class="space-y-4">
                <!-- Event type -->
                <div>
                  <label
                    for="event-type"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Event Type</label
                  >
                  <select
                    id="event-type"
                    v-model="newEvent.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option
                      v-for="eventType in calendarEventTypes"
                      :key="eventType.value"
                      :value="eventType.value"
                    >
                      {{ eventType.label }}
                    </option>
                  </select>
                </div>

                <!-- Event title -->
                <div>
                  <label
                    for="event-title"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Title</label
                  >
                  <input
                    id="event-title"
                    v-model="newEvent.title"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter event title"
                  />
                </div>

                <!-- Start & End times -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label
                      for="event-start"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Start Time</label
                    >
                    <input
                      id="event-start"
                      v-model="newEvent.startTime"
                      type="datetime-local"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label
                      for="event-end"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >End Time</label
                    >
                    <input
                      id="event-end"
                      v-model="newEvent.endTime"
                      type="datetime-local"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>

                <!-- Location -->
                <div>
                  <label
                    for="event-location"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Location (optional)</label
                  >
                  <input
                    id="event-location"
                    v-model="newEvent.location"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter location or meeting URL"
                  />
                </div>

                <!-- Description -->
                <div>
                  <label
                    for="event-description"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Description (optional)</label
                  >
                  <textarea
                    id="event-description"
                    v-model="newEvent.description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter event details"
                  />
                </div>

                <!-- Course/Project Selector (based on event type) -->
                <div v-if="['course', 'assignment'].includes(newEvent.type)">
                  <label
                    for="event-course"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Course</label
                  >
                  <select
                    id="event-course"
                    v-model="newEvent.courseId"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Select a course</option>
                    <option value="101">Web Development Fundamentals</option>
                    <option value="102">Advanced JavaScript</option>
                    <option value="103">React Masterclass</option>
                    <option value="104">Backend with Node.js</option>
                  </select>
                </div>

                <div
                  v-if="
                    ['consultation', 'project_milestone', 'deadline'].includes(
                      newEvent.type
                    )
                  "
                >
                  <label
                    for="event-project"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Project</label
                  >
                  <select
                    id="event-project"
                    v-model="newEvent.projectId"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Select a project</option>
                    <option value="201">E-commerce Website Redesign</option>
                    <option value="202">Mobile App Development</option>
                    <option value="203">CRM Integration</option>
                    <option value="204">Marketing Automation</option>
                  </select>
                </div>
              </div>

              <!-- Modal actions -->
              <div class="mt-6 sm:flex sm:flex-row-reverse">
                <button
                  type="submit"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  Create Event
                </button>
                <button
                  type="button"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                  @click="showCreateEventModal = false"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast notification -->
    <div
      v-if="toast.show"
      class="fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-y-0 opacity-100"
      :class="{
        'bg-green-100 border border-green-200 text-green-800':
          toast.type === 'success',
        'bg-red-100 border border-red-200 text-red-800': toast.type === 'error',
        'bg-blue-100 border border-blue-200 text-blue-800':
          toast.type === 'info',
      }"
    >
      <div class="flex items-center">
        <!-- Success icon -->
        <svg
          v-if="toast.type === 'success'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 text-green-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>

        <!-- Error icon -->
        <svg
          v-if="toast.type === 'error'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 text-red-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>

        <!-- Info icon -->
        <svg
          v-if="toast.type === 'info'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 text-blue-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
          />
        </svg>

        <span>{{ toast.message }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom animations for calendar elements */
.calendar-animation-enter-active,
.calendar-animation-leave-active {
  transition: all 0.3s ease;
}

.calendar-animation-enter-from,
.calendar-animation-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Toast animations */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
