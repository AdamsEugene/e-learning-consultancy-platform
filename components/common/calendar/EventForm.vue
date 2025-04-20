<!-- components/dashboard/calendar/EventForm.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";
import { format, addHours, parseISO } from "date-fns";
import type { CalendarEvent } from "~/composables/useCalendarState";

interface Props {
  userType: string;
  initialDate: Date;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "save", event: CalendarEvent): void;
  (e: "cancel"): void;
}>();

// Generate a unique ID for the new event
const generateId = () => {
  return "event_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
};

// Set up default values based on initialDate
const startTime = ref(format(props.initialDate, "yyyy-MM-dd'T'HH:mm"));
const endTime = ref(
  format(addHours(props.initialDate, 1), "yyyy-MM-dd'T'HH:mm")
);
const title = ref("");
const description = ref("");
const eventType = ref<
  "course" | "meeting" | "assignment" | "reminder" | "consultation" | "deadline"
>("meeting");
const location = ref("");
const courseId = ref<number | undefined>(undefined);
const projectId = ref<number | undefined>(undefined);
const attendees = ref<
  Array<{ id: string; name: string; avatar: string; role: string }>
>([]);

// Dynamic fields based on event type
const showsCourseId = computed(() => eventType.value === "course");
const showsProjectId = computed(() =>
  ["consultation", "meeting"].includes(eventType.value)
);
const showsAttendees = computed(() =>
  ["meeting", "consultation"].includes(eventType.value)
);

// Available event types based on user role
const availableEventTypes = computed(() => {
  const types = [
    { value: "meeting", label: "Meeting" },
    { value: "reminder", label: "Reminder" },
  ];

  // Add role-specific event types
  switch (props.userType) {
    case "student":
      types.push(
        { value: "course", label: "Course Session" },
        { value: "assignment", label: "Assignment" }
      );
      break;
    case "instructor":
      types.push(
        { value: "course", label: "Course Session" },
        { value: "assignment", label: "Assignment" }
      );
      break;
    case "consultant":
      types.push(
        { value: "consultation", label: "Consultation" },
        { value: "deadline", label: "Deadline" }
      );
      break;
    case "client":
      types.push(
        { value: "consultation", label: "Consultation" },
        { value: "deadline", label: "Deadline" }
      );
      break;
    case "admin":
      types.push(
        { value: "course", label: "Course Session" },
        { value: "assignment", label: "Assignment" },
        { value: "consultation", label: "Consultation" },
        { value: "deadline", label: "Deadline" }
      );
      break;
  }

  return types;
});

// Sample people for attendees (in a real app, this would come from an API)
const availablePeople = [
  {
    id: "p1",
    name: "John Doe",
    avatar: "/api/placeholder/40/40",
    role: "Instructor",
  },
  {
    id: "p2",
    name: "Jane Smith",
    avatar: "/api/placeholder/40/40",
    role: "Student",
  },
  {
    id: "p3",
    name: "Robert Johnson",
    avatar: "/api/placeholder/40/40",
    role: "Consultant",
  },
  {
    id: "p4",
    name: "Sarah Williams",
    avatar: "/api/placeholder/40/40",
    role: "Client",
  },
  {
    id: "p5",
    name: "Michael Brown",
    avatar: "/api/placeholder/40/40",
    role: "Admin",
  },
];

// Form validation
const errors = ref({
  title: "",
  startTime: "",
  endTime: "",
  eventType: "",
});

const validateForm = () => {
  let isValid = true;

  // Reset errors
  errors.value = {
    title: "",
    startTime: "",
    endTime: "",
    eventType: "",
  };

  // Title validation
  if (!title.value.trim()) {
    errors.value.title = "Title is required";
    isValid = false;
  }

  // Start time validation
  if (!startTime.value) {
    errors.value.startTime = "Start time is required";
    isValid = false;
  }

  // End time validation
  if (!endTime.value) {
    errors.value.endTime = "End time is required";
    isValid = false;
  } else if (parseISO(endTime.value) <= parseISO(startTime.value)) {
    errors.value.endTime = "End time must be after start time";
    isValid = false;
  }

  // Event type validation
  if (!eventType.value) {
    errors.value.eventType = "Event type is required";
    isValid = false;
  }

  return isValid;
};

// Toggle an attendee
const toggleAttendee = (person: (typeof availablePeople)[0]) => {
  const index = attendees.value.findIndex((a) => a.id === person.id);

  if (index === -1) {
    attendees.value.push(person);
  } else {
    attendees.value.splice(index, 1);
  }
};

// Check if person is selected
const isAttendeeSelected = (id: string) => {
  return attendees.value.some((a) => a.id === id);
};

// Save the event
const saveEvent = () => {
  if (!validateForm()) return;

  const event: CalendarEvent = {
    id: generateId(),
    title: title.value,
    description: description.value,
    startTime: startTime.value,
    endTime: endTime.value,
    type: eventType.value,
    location: location.value,
    isCompleted: false,
  };

  // Add optional fields based on type
  if (showsCourseId.value && courseId.value) {
    event.courseId = courseId.value;
  }

  if (showsProjectId.value && projectId.value) {
    event.projectId = projectId.value;
  }

  if (showsAttendees.value && attendees.value.length > 0) {
    event.attendees = [...attendees.value];
  }

  emit("save", event);
};

// Cancel creation
const cancel = () => {
  emit("cancel");
};

// Format date for display
const formatDate = (date: Date) => {
  return format(date, "EEEE, MMMM d, yyyy");
};
</script>

<template>
  <div class="event-form p-4">
    <h2 class="text-xl font-bold text-gray-800 mb-4">
      New Event - {{ formatDate(initialDate) }}
    </h2>

    <form @submit.prevent="saveEvent" class="space-y-4">
      <!-- Event Type Selection -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Event Type</label
        >
        <div class="grid grid-cols-2 gap-2">
          <div
            v-for="type in availableEventTypes"
            :key="type.value"
            @click="eventType = type.value as any"
            class="p-2 rounded-lg border cursor-pointer transition-colors duration-200 flex items-center"
            :class="
              eventType === type.value
                ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                : 'border-gray-300 hover:bg-gray-50'
            "
          >
            <input
              type="radio"
              :value="type.value"
              v-model="eventType"
              class="h-4 w-4 text-indigo-600 border-gray-300 mr-2"
            />
            <span>{{ type.label }}</span>
          </div>
        </div>
        <p v-if="errors.eventType" class="text-red-500 text-xs mt-1">
          {{ errors.eventType }}
        </p>
      </div>

      <!-- Title -->
      <div>
        <label
          for="event-title"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Title</label
        >
        <input
          id="event-title"
          v-model="title"
          type="text"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          placeholder="Event title"
        />
        <p v-if="errors.title" class="text-red-500 text-xs mt-1">
          {{ errors.title }}
        </p>
      </div>

      <!-- Time Range -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label
            for="start-time"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Start Time</label
          >
          <input
            id="start-time"
            v-model="startTime"
            type="datetime-local"
            class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
          <p v-if="errors.startTime" class="text-red-500 text-xs mt-1">
            {{ errors.startTime }}
          </p>
        </div>

        <div>
          <label
            for="end-time"
            class="block text-sm font-medium text-gray-700 mb-1"
            >End Time</label
          >
          <input
            id="end-time"
            v-model="endTime"
            type="datetime-local"
            class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
          <p v-if="errors.endTime" class="text-red-500 text-xs mt-1">
            {{ errors.endTime }}
          </p>
        </div>
      </div>

      <!-- Location -->
      <div>
        <label
          for="location"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Location (Optional)</label
        >
        <input
          id="location"
          v-model="location"
          type="text"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          placeholder="Location"
        />
      </div>

      <!-- Description -->
      <div>
        <label
          for="description"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Description (Optional)</label
        >
        <textarea
          id="description"
          v-model="description"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          rows="4"
          placeholder="Add a description..."
        ></textarea>
      </div>

      <!-- Course ID field (conditional) -->
      <div v-if="showsCourseId">
        <label
          for="course-id"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Course ID</label
        >
        <input
          id="course-id"
          v-model.number="courseId"
          type="number"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          placeholder="Enter course ID"
        />
      </div>

      <!-- Project ID field (conditional) -->
      <div v-if="showsProjectId">
        <label
          for="project-id"
          class="block text-sm font-medium text-gray-700 mb-1"
          >Project ID</label
        >
        <input
          id="project-id"
          v-model.number="projectId"
          type="number"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          placeholder="Enter project ID"
        />
      </div>

      <!-- Attendees selector (conditional) -->
      <div v-if="showsAttendees">
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >Attendees</label
        >
        <div class="border rounded-lg p-3 max-h-48 overflow-y-auto bg-gray-50">
          <div
            v-for="person in availablePeople"
            :key="person.id"
            @click="toggleAttendee(person)"
            class="flex items-center p-2 cursor-pointer hover:bg-gray-100 rounded-md transition-colors"
            :class="{
              'bg-indigo-50 border border-indigo-200': isAttendeeSelected(
                person.id
              ),
            }"
          >
            <div class="flex-shrink-0">
              <img
                :src="person.avatar"
                :alt="person.name"
                class="w-8 h-8 rounded-full"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">{{ person.name }}</p>
              <p class="text-xs text-gray-500">{{ person.role }}</p>
            </div>
            <div class="ml-auto">
              <input
                type="checkbox"
                :checked="isAttendeeSelected(person.id)"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                @click.stop
              />
            </div>
          </div>
          <div
            v-if="availablePeople.length === 0"
            class="p-2 text-gray-500 text-sm italic text-center"
          >
            No people available
          </div>
        </div>
        <div v-if="attendees.length > 0" class="mt-2">
          <span class="text-xs font-medium text-gray-700"
            >Selected: {{ attendees.length }}</span
          >
        </div>
      </div>

      <!-- Form Actions -->
      <div class="flex justify-end space-x-3 pt-4 border-t">
        <button
          type="button"
          @click="cancel"
          class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 font-medium transition-colors"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white font-medium transition-colors"
        >
          Create Event
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.event-form {
  transition: all 0.3s ease-in-out;
}
</style>
