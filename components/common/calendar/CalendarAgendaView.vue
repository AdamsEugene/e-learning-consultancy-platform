<!-- components/dashboard/calendar/CalendarAgendaView.vue -->
<script setup lang="ts">
import { computed } from "vue";
import {
  format,
  parseISO,
  isToday,
  isTomorrow,
  isThisWeek,
  isThisMonth,
  isFuture,
//   addDays,
} from "date-fns";
import type { CalendarEvent } from "~/composables/useCalendarState";

interface Props {
  currentDate: Date;
  events: CalendarEvent[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "event-click", event: CalendarEvent): void;
}>();

// Group events by time period
const groupedEvents = computed(() => {
  const groups = {
    today: [] as CalendarEvent[],
    tomorrow: [] as CalendarEvent[],
    thisWeek: [] as CalendarEvent[],
    thisMonth: [] as CalendarEvent[],
    future: [] as CalendarEvent[],
  };

  // Filter and sort events
  const sortedEvents = [...props.events].sort((a, b) => {
    return parseISO(a.startTime).getTime() - parseISO(b.startTime).getTime();
  });

  // Group events by time period
  sortedEvents.forEach((event) => {
    const startDate = parseISO(event.startTime);

    if (isToday(startDate)) {
      groups.today.push(event);
    } else if (isTomorrow(startDate)) {
      groups.tomorrow.push(event);
    } else if (isThisWeek(startDate) && isFuture(startDate)) {
      groups.thisWeek.push(event);
    } else if (isThisMonth(startDate) && isFuture(startDate)) {
      groups.thisMonth.push(event);
    } else if (isFuture(startDate)) {
      groups.future.push(event);
    }
  });

  return groups;
});

// Format event date
const formatEventDate = (event: CalendarEvent) => {
  const startDate = parseISO(event.startTime);
  return format(startDate, "EEEE, MMMM d, yyyy");
};

// Format event time
const formatEventTime = (event: CalendarEvent) => {
  const startDate = parseISO(event.startTime);
  const endDate = parseISO(event.endTime);

  return `${format(startDate, "h:mm a")} - ${format(endDate, "h:mm a")}`;
};

// Get event color based on type
const getEventColor = (event: CalendarEvent) => {
  if (event.color) return event.color;

  switch (event.type) {
    case "course":
      return "border-blue-500 bg-blue-50";
    case "meeting":
      return "border-purple-500 bg-purple-50";
    case "assignment":
      return "border-amber-500 bg-amber-50";
    case "reminder":
      return "border-green-500 bg-green-50";
    case "consultation":
      return "border-indigo-500 bg-indigo-50";
    case "deadline":
      return "border-red-500 bg-red-50";
    default:
      return "border-gray-500 bg-gray-50";
  }
};

// Get text color based on event type
const getEventTextColor = (event: CalendarEvent) => {
  switch (event.type) {
    case "course":
      return "text-blue-800";
    case "meeting":
      return "text-purple-800";
    case "assignment":
      return "text-amber-800";
    case "reminder":
      return "text-green-800";
    case "consultation":
      return "text-indigo-800";
    case "deadline":
      return "text-red-800";
    default:
      return "text-gray-800";
  }
};

// Get event icon based on type
const getEventIcon = (event: CalendarEvent) => {
  let iconColor = "";
  switch (event.type) {
    case "course":
      iconColor = "text-blue-500";
      break;
    case "meeting":
      iconColor = "text-purple-500";
      break;
    case "assignment":
      iconColor = "text-amber-500";
      break;
    case "reminder":
      iconColor = "text-green-500";
      break;
    case "consultation":
      iconColor = "text-indigo-500";
      break;
    case "deadline":
      iconColor = "text-red-500";
      break;
    default:
      iconColor = "text-gray-500";
  }

  switch (event.type) {
    case "course":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
              </svg>`;
    case "meeting":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
              </svg>`;
    case "assignment":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
              </svg>`;
    case "reminder":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
              </svg>`;
    case "consultation":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
              </svg>`;
    case "deadline":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
              </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${iconColor}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
              </svg>`;
  }
};

// Handle event click
const onEventClick = (event: CalendarEvent) => {
  emit("event-click", event);
};

// Get relative time label
const getRelativeTimeLabel = (group: string) => {
  switch (group) {
    case "today":
      return "Today";
    case "tomorrow":
      return "Tomorrow";
    case "thisWeek":
      return "This Week";
    case "thisMonth":
      return "This Month";
    case "future":
      return "Future Events";
    default:
      return "Upcoming Events";
  }
};
</script>

<template>
  <div class="agenda-view p-4">
    <div
      v-if="Object.values(groupedEvents).every((group) => group.length === 0)"
      class="text-center py-12"
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
      <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming events</h3>
      <p class="text-gray-600">
        Your schedule is clear for the next few weeks.
      </p>
    </div>

    <template v-for="(group, groupKey) in groupedEvents" :key="groupKey">
      <div v-if="group.length > 0" class="mb-8">
        <h3 class="text-xl font-bold mb-4 flex items-center">
          <span class="mr-2">{{ getRelativeTimeLabel(groupKey) }}</span>
          <span
            class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded"
          >
            {{ group.length }} {{ group.length === 1 ? "event" : "events" }}
          </span>
        </h3>

        <div class="space-y-3">
          <div
            v-for="event in group"
            :key="event.id"
            class="border-l-4 rounded-lg shadow-sm p-4 bg-white transition-all duration-300 cursor-pointer transform hover:translate-x-1 hover:shadow-md"
            :class="[getEventColor(event), { 'opacity-50': event.isCompleted }]"
            @click="onEventClick(event)"
          >
            <div class="flex items-start">
              <div class="mr-3 mt-0.5" v-html="getEventIcon(event)" />

              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <h4
                    class="text-lg font-bold"
                    :class="getEventTextColor(event)"
                  >
                    {{ event.title }}
                  </h4>
                  <span
                    v-if="event.isCompleted"
                    class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-800 text-xs rounded-full"
                  >
                    Completed
                  </span>
                </div>

                <div class="text-sm text-gray-600 mt-1">
                  <div class="flex items-center mb-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1 text-gray-500"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ formatEventDate(event) }}
                  </div>

                  <div class="flex items-center mb-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1 text-gray-500"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ formatEventTime(event) }}
                  </div>

                  <div v-if="event.location" class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1 text-gray-500"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ event.location }}
                  </div>
                </div>

                <div
                  v-if="event.description"
                  class="mt-2 text-sm text-gray-700"
                >
                  {{ event.description }}
                </div>

                <div
                  v-if="event.attendees && event.attendees.length > 0"
                  class="mt-3"
                >
                  <div class="text-xs text-gray-500 mb-1">Attendees:</div>
                  <div class="flex flex-wrap -space-x-2 overflow-hidden">
                    <div
                      v-for="(attendee, index) in event.attendees.slice(0, 5)"
                      :key="index"
                      class="inline-block h-6 w-6 rounded-full ring-2 ring-white"
                    >
                      <img
                        :src="attendee.avatar"
                        :alt="attendee.name"
                        class="h-full w-full object-cover rounded-full"
                      />
                    </div>
                    <div
                      v-if="event.attendees.length > 5"
                      class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-200 text-xs font-bold text-gray-800 ring-2 ring-white"
                    >
                      +{{ event.attendees.length - 5 }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.agenda-view {
  transition: all 0.3s ease-in-out;
}
</style>
