<!-- components/dashboard/calendar/EventDetails.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";
import { format, parseISO, differenceInMinutes, isAfter } from "date-fns";
import type { CalendarEvent, Reminder } from "~/composables/useCalendarState";

interface Props {
  event: CalendarEvent;
  userType: string;
  hasReminder: boolean;
  reminder?: Reminder | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "create-reminder", eventId: string, reminder: Reminder): void;
  (e: "cancel-reminder", eventId: string): void;
  (e: "mark-complete" | "update-event", event: CalendarEvent): void;
}>();

// State for reminder creation
const isCreatingReminder = ref(false);
const reminderTime = ref("");
const reminderType = ref<"email" | "push" | "both">("both");
const reminderMessage = ref("");

// State for editing mode
const isEditing = ref(false);
const editedEvent = ref<CalendarEvent>({ ...props.event });

// Computed formatted dates
const formattedStartDate = computed(() => {
  return format(parseISO(props.event.startTime), "EEEE, MMMM d, yyyy");
});

const formattedTimeRange = computed(() => {
  const startDate = parseISO(props.event.startTime);
  const endDate = parseISO(props.event.endTime);
  return `${format(startDate, "h:mm a")} - ${format(endDate, "h:mm a")}`;
});

const duration = computed(() => {
  const startDate = parseISO(props.event.startTime);
  const endDate = parseISO(props.event.endTime);
  const minutes = differenceInMinutes(endDate, startDate);

  if (minutes < 60) {
    return `${minutes} minute${minutes !== 1 ? "s" : ""}`;
  } else {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;

    if (remainingMinutes === 0) {
      return `${hours} hour${hours !== 1 ? "s" : ""}`;
    } else {
      return `${hours} hour${
        hours !== 1 ? "s" : ""
      } ${remainingMinutes} minute${remainingMinutes !== 1 ? "s" : ""}`;
    }
  }
});

// Check if event is upcoming
const isUpcoming = computed(() => {
  return isAfter(parseISO(props.event.startTime), new Date());
});

// Event color based on type
const eventColor = computed(() => {
  switch (props.event.type) {
    case "course":
      return {
        bg: "bg-blue-500",
        text: "text-blue-800",
        border: "border-blue-500",
        light: "bg-blue-50",
      };
    case "meeting":
      return {
        bg: "bg-purple-500",
        text: "text-purple-800",
        border: "border-purple-500",
        light: "bg-purple-50",
      };
    case "assignment":
      return {
        bg: "bg-amber-500",
        text: "text-amber-800",
        border: "border-amber-500",
        light: "bg-amber-50",
      };
    case "reminder":
      return {
        bg: "bg-green-500",
        text: "text-green-800",
        border: "border-green-500",
        light: "bg-green-50",
      };
    case "consultation":
      return {
        bg: "bg-indigo-600",
        text: "text-indigo-800",
        border: "border-indigo-500",
        light: "bg-indigo-50",
      };
    case "deadline":
      return {
        bg: "bg-red-500",
        text: "text-red-800",
        border: "border-red-500",
        light: "bg-red-50",
      };
    default:
      return {
        bg: "bg-gray-500",
        text: "text-gray-800",
        border: "border-gray-500",
        light: "bg-gray-50",
      };
  }
});

// Handle reminder creation
const createReminder = () => {
  if (!reminderTime.value) return;

  isCreatingReminder.value = true;

  const reminder: Reminder = {
    eventId: props.event.id,
    time: reminderTime.value,
    type: reminderType.value,
    message: reminderMessage.value,
  };

  emit("create-reminder", props.event.id, reminder);

  // Reset form
  reminderTime.value = "";
  reminderType.value = "both";
  reminderMessage.value = "";

  isCreatingReminder.value = false;
};

// Handle reminder cancellation
const cancelReminder = () => {
  emit("cancel-reminder", props.event.id);
};

// Mark event as complete
const markComplete = () => {
  emit("mark-complete", props.event);
};

// Toggle edit mode
const toggleEditMode = () => {
  if (isEditing.value) {
    // Save changes
    emit("update-event", editedEvent.value);
  } else {
    // Enter edit mode - clone the event
    editedEvent.value = JSON.parse(JSON.stringify(props.event));
  }

  isEditing.value = !isEditing.value;
};

// Format reminder time
const formatReminderTime = (time: string) => {
  const reminderDate = parseISO(time);
  return format(reminderDate, "MMM d, yyyy h:mm a");
};
</script>

<template>
  <div class="event-details p-4">
    <!-- Event header -->
    <div :class="[eventColor.light, 'p-4 rounded-lg mb-4']">
      <div class="flex justify-between items-start">
        <div class="flex items-center">
          <!-- Event type badge -->
          <span
            :class="[
              eventColor.bg,
              'text-white px-2 py-1 rounded-md text-xs font-bold capitalize',
            ]"
          >
            {{ props.event.type }}
          </span>

          <!-- Completed badge -->
          <span
            v-if="props.event.isCompleted"
            class="ml-2 bg-gray-200 text-gray-800 px-2 py-1 rounded-md text-xs font-bold"
          >
            Completed
          </span>
        </div>

        <!-- Action buttons -->
        <div class="flex space-x-1">
          <button
            v-if="isUpcoming && !props.event.isCompleted && !isEditing"
            @click="markComplete"
            class="p-1 rounded-full hover:bg-white transition-colors"
            :class="eventColor.text"
            title="Mark as complete"
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
            @click="toggleEditMode"
            class="p-1 rounded-full hover:bg-white transition-colors"
            :class="eventColor.text"
            :title="isEditing ? 'Save changes' : 'Edit event'"
          >
            <svg
              v-if="!isEditing"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
              />
            </svg>
            <svg
              v-else
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
        </div>
      </div>

      <!-- Event title -->
      <div v-if="!isEditing" class="mt-3">
        <h2 class="text-2xl font-bold" :class="eventColor.text">
          {{ props.event.title }}
        </h2>
      </div>
      <div v-else class="mt-3">
        <input
          v-model="editedEvent.title"
          type="text"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2"
          :class="[
            `focus:ring-${eventColor.border.split('-')[1]}-500`,
            'focus:border-transparent',
          ]"
          placeholder="Event title"
        />
      </div>

      <!-- Date and time -->
      <div class="mt-3 text-gray-700">
        <div class="flex items-center mb-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
              clip-rule="evenodd"
            />
          </svg>
          <span>{{ formattedStartDate }}</span>
        </div>

        <div class="flex items-center mb-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
              clip-rule="evenodd"
            />
          </svg>
          <span>{{ formattedTimeRange }} ({{ duration }})</span>
        </div>

        <div v-if="props.event.location" class="flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
              clip-rule="evenodd"
            />
          </svg>
          <span>{{ props.event.location }}</span>
        </div>
      </div>
    </div>

    <!-- Event description -->
    <div class="mb-4">
      <h3 class="font-bold text-gray-700 mb-2">Description</h3>
      <div v-if="!isEditing" class="text-gray-600">
        <p v-if="props.event.description">{{ props.event.description }}</p>
        <p v-else class="text-gray-400 italic">No description provided</p>
      </div>
      <div v-else>
        <textarea
          v-model="editedEvent.description"
          class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          rows="4"
          placeholder="Event description"
        ></textarea>
      </div>
    </div>

    <!-- Attendees (if any) -->
    <div
      v-if="props.event.attendees && props.event.attendees.length > 0"
      class="mb-4"
    >
      <h3 class="font-bold text-gray-700 mb-2">Attendees</h3>
      <div class="flex flex-wrap -m-1">
        <div
          v-for="(attendee, index) in props.event.attendees"
          :key="index"
          class="flex items-center p-1 m-1 bg-gray-100 rounded-full"
        >
          <img
            :src="attendee.avatar"
            :alt="attendee.name"
            class="w-6 h-6 rounded-full mr-2"
          />
          <span class="text-sm mr-1">{{ attendee.name }}</span>
          <span class="text-xs text-gray-500">({{ attendee.role }})</span>
        </div>
      </div>
    </div>

    <!-- Reminder section -->
    <div class="mb-4">
      <h3 class="font-bold text-gray-700 mb-2">Reminder</h3>

      <div
        v-if="hasReminder && reminder"
        class="bg-blue-50 border border-blue-200 rounded-lg p-3"
      >
        <div class="flex justify-between items-center">
          <div>
            <div class="font-medium text-blue-800">
              Reminder set for {{ formatReminderTime(reminder.time) }}
            </div>
            <div class="text-sm text-gray-600">
              Notification via:
              {{
                reminder.type === "both"
                  ? "Email & Push"
                  : reminder.type === "email"
                  ? "Email"
                  : "Push Notification"
              }}
            </div>
            <div v-if="reminder.message" class="text-sm text-gray-600 mt-1">
              "{{ reminder.message }}"
            </div>
          </div>

          <button
            @click="cancelReminder"
            class="text-blue-700 hover:text-blue-900"
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

      <div v-else-if="isUpcoming && !props.event.isCompleted">
        <div v-if="!isCreatingReminder" class="text-center py-3">
          <button
            @click="isCreatingReminder = true"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
          >
            <span class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                />
              </svg>
              Add Reminder
            </span>
          </button>
        </div>

        <div v-else class="space-y-3 bg-gray-50 p-3 rounded-lg">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Reminder Time</label
            >
            <input
              v-model="reminderTime"
              type="datetime-local"
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Notification Type</label
            >
            <select
              v-model="reminderType"
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="email">Email</option>
              <option value="push">Push Notification</option>
              <option value="both">Both</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Message (Optional)</label
            >
            <textarea
              v-model="reminderMessage"
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              rows="2"
              placeholder="Add a reminder message"
            ></textarea>
          </div>

          <div class="flex space-x-2">
            <button
              @click="createReminder"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex-grow"
            >
              Set Reminder
            </button>
            <button
              @click="isCreatingReminder = false"
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>

      <div v-else class="text-gray-500 italic text-center py-2">
        Reminders are not available for past or completed events
      </div>
    </div>

    <!-- Additional event information based on type -->
    <div v-if="props.event.courseId" class="mb-4">
      <h3 class="font-bold text-gray-700 mb-2">Course Information</h3>
      <div class="bg-blue-50 p-3 rounded-lg">
        <div class="flex items-center text-blue-800">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
            />
          </svg>
          <span>Course ID: {{ props.event.courseId }}</span>
        </div>
      </div>
    </div>

    <div v-if="props.event.projectId" class="mb-4">
      <h3 class="font-bold text-gray-700 mb-2">Project Information</h3>
      <div class="bg-purple-50 p-3 rounded-lg">
        <div class="flex items-center text-purple-800">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
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
          <span>Project ID: {{ props.event.projectId }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.event-details {
  transition: all 0.3s ease-in-out;
}
</style>
