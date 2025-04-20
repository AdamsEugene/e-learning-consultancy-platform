<!-- components/dashboard/calendar/CalendarMonthView.vue -->
<script setup lang="ts">
import { computed } from "vue";
import {
  startOfWeek,
//   endOfWeek,
  eachDayOfInterval,
  startOfMonth,
  endOfMonth,
  addDays,
  getDay,
  isSameDay,
  isSameMonth,
  isToday,
  parseISO,
  isBefore,
  format,
} from "date-fns";
import type { CalendarEvent } from "~/composables/useCalendarState";

interface Props {
  currentDate: Date;
  events: CalendarEvent[];
  highlightToday: boolean;
  showWeekends: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "day-click" | "time-slot-click", date: Date): void;
  (e: "event-click", event: CalendarEvent): void;
}>();

// Calendar days generation
const calendarDays = computed(() => {
  const days = [];

  // First day of the month
  const firstDay = startOfMonth(props.currentDate);
  // Last day of the month
  const lastDay = endOfMonth(props.currentDate);

  // First day of the calendar (might be in the previous month)
  const start = startOfWeek(firstDay);
  // Show 6 weeks always to maintain consistent height
  let end = addDays(start, 41); // 6 weeks * 7 days - 1

  // If the end date is before the last day of the month, adjust
  if (isBefore(end, lastDay)) {
    end = addDays(end, 7); // Add one more week
  }

  // Create array of days
  const dateInterval = eachDayOfInterval({ start, end });

  // Create calendar grid with week rows
  for (let i = 0; i < dateInterval.length; i += 7) {
    days.push(dateInterval.slice(i, i + 7));
  }

  return days;
});

// Get events for a specific day
const getEventsForDay = (day: Date) => {
  return props.events.filter((event) => {
    const startDate = parseISO(event.startTime);
    return isSameDay(startDate, day);
  });
};

// Check if a day has events
const hasEvents = (day: Date) => {
  return getEventsForDay(day).length > 0;
};

// Helper to check if a day is on the weekend
const isWeekend = (date: Date) => {
  const day = getDay(date);
  return day === 0 || day === 6;
};

// Day styling helpers
const getDayClass = (day: Date) => {
  let classes =
    "relative rounded-lg transition-all duration-200 h-full cursor-pointer ";

  if (isToday(day)) {
    classes += "bg-indigo-50 ";
  }

  if (isSameMonth(day, props.currentDate)) {
    classes += "text-gray-900 ";
  } else {
    classes += "text-gray-400 bg-gray-50 ";
  }

  if (hasEvents(day)) {
    classes += "hover:bg-indigo-100 ";
  } else {
    classes += "hover:bg-gray-100 ";
  }

  if (isWeekend(day) && !props.showWeekends) {
    classes += "opacity-50 ";
  }

  return classes;
};

// Get custom styling for different event types
const getEventStyles = (event: CalendarEvent) => {
  switch (event.type) {
    case "course":
      return "border-l-4 border-blue-600";
    case "meeting":
      return "border-l-4 border-purple-600";
    case "assignment":
      return "border-l-4 border-amber-600";
    case "reminder":
      return "border-l-4 border-green-600";
    case "consultation":
      return "border-l-4 border-indigo-700";
    case "deadline":
      return "border-l-4 border-red-600";
    default:
      return "border-l-4 border-gray-600";
  }
};

// Get event color based on type
const getEventColor = (event: CalendarEvent) => {
  if (event.color) return event.color;

  switch (event.type) {
    case "course":
      return "bg-blue-500";
    case "meeting":
      return "bg-purple-500";
    case "assignment":
      return "bg-amber-500";
    case "reminder":
      return "bg-green-500";
    case "consultation":
      return "bg-indigo-600";
    case "deadline":
      return "bg-red-500";
    default:
      return "bg-gray-500";
  }
};

// Get event icon based on type
const getEventIcon = (event: CalendarEvent) => {
  switch (event.type) {
    case "course":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
              </svg>`;
    case "meeting":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
              </svg>`;
    case "assignment":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
              </svg>`;
    case "reminder":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
              </svg>`;
    case "consultation":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd" />
              </svg>`;
    case "deadline":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
              </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
              </svg>`;
  }
};

// Format date for display
const formatDate = (date: Date, formatStr: string = "d") => {
  return format(date, formatStr);
};

// Handle day click
const onDayClick = (day: Date) => {
  emit("day-click", day);
};

// Handle event click
const onEventClick = (event: CalendarEvent, e: Event) => {
  e.stopPropagation();
  emit("event-click", event);
};

// Get day content classes
const getDayContentClasses = (day: Date) => {
  const dayEvents = getEventsForDay(day);
  let classes = "flex-1 ";

  if (dayEvents.length > 0) {
    classes += "min-h-[80px] ";
  } else {
    classes += "min-h-[80px] ";
  }

  return classes;
};
</script>

<template>
  <div class="month-view">
    <!-- Month Grid Headers (Days of Week) -->
    <div
      class="grid grid-cols-7 text-sm font-medium text-center py-2 bg-gray-50"
    >
      <div
        v-for="(day, i) in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
        :key="i"
        class="py-2"
      >
        {{ day }}
      </div>
    </div>

    <!-- Month Grid -->
    <div class="grid grid-cols-7 border-t border-gray-200">
      <!-- Days -->
      <template
        v-for="(week, weekIndex) in calendarDays"
        :key="`week-${weekIndex}`"
      >
        <div
          v-for="(day, dayIndex) in week"
          :key="`day-${weekIndex}-${dayIndex}`"
          class="day-cell border-b border-r h-auto transition-colors duration-300 border-gray-200"
          :class="{ 'border-l': dayIndex === 0 }"
        >
          <div :class="getDayClass(day)" @click="onDayClick(day)">
            <!-- Date Header -->
            <div class="p-1 text-right">
              <span
                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-sm transition-all duration-300"
                :class="{
                  'bg-indigo-600 text-white': isToday(day) && highlightToday,
                  'hover:bg-indigo-100': !isToday(day) || !highlightToday,
                }"
              >
                {{ formatDate(day) }}
              </span>
            </div>

            <!-- Day Content with Events -->
            <div :class="getDayContentClasses(day)" class="p-1 space-y-1">
              <!-- Events -->
              <div
                v-for="event in getEventsForDay(day).slice(0, 3)"
                :key="event.id"
                class="event-item text-xs p-1 rounded-sm truncate cursor-pointer transition-all duration-300 transform hover:scale-105 hover:shadow-md text-white"
                :class="[
                  getEventColor(event),
                  getEventStyles(event),
                  { 'opacity-50': event.isCompleted },
                ]"
                @click.stop="onEventClick(event, $event)"
              >
                <div class="flex items-center">
                  <span class="mr-1" v-html="getEventIcon(event)" />
                  <span class="truncate">{{ event.title }}</span>
                </div>
              </div>

              <!-- More events indicator -->
              <div
                v-if="getEventsForDay(day).length > 3"
                class="text-xs font-medium text-indigo-600 text-center cursor-pointer py-0.5 hover:underline"
                @click.stop="onDayClick(day)"
              >
                +{{ getEventsForDay(day).length - 3 }} more
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
.month-view {
  transition: all 0.3s ease-in-out;
}
</style>
