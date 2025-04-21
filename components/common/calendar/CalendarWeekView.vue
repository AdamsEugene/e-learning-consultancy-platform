<!-- components/dashboard/calendar/CalendarWeekView.vue -->
<script setup lang="ts">
import { computed, onMounted, ref, onBeforeUnmount } from "vue";
import {
  startOfWeek,
  endOfWeek,
  eachDayOfInterval,
  format,
  isToday,
  parseISO,
  getDay,
  isSameDay,
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
  (e: "day-click", date: Date): void;
  (e: "event-click", event: CalendarEvent): void;
  (e: "time-slot-click", date: Date, hour: number): void;
}>();

// Calendar days generation
const weekDays = computed(() => {
  const start = startOfWeek(props.currentDate);
  const end = endOfWeek(props.currentDate);
  return eachDayOfInterval({ start, end });
});

// Hours for day and week view
const hours = computed(() => {
  const result = [];
  for (let i = 0; i < 24; i++) {
    // Convert 24h to 12h format
    const hourFormatted =
      i === 0
        ? "12 AM"
        : i < 12
        ? `${i} AM`
        : i === 12
        ? "12 PM"
        : `${i - 12} PM`;
    result.push({
      hour: i,
      label: hourFormatted,
    });
  }
  return result;
});

// Current time for indicator
const currentTime = ref({
  hour: new Date().getHours(),
  minute: new Date().getMinutes(),
});

// Update current time every minute
let timeInterval: number | null = null;

onMounted(() => {
  // Initialize with current time
  const now = new Date();
  currentTime.value = {
    hour: now.getHours(),
    minute: now.getMinutes(),
  };

  timeInterval = window.setInterval(() => {
    const now = new Date();
    currentTime.value = {
      hour: now.getHours(),
      minute: now.getMinutes(),
    };
  }, 60000); // Update every minute
});

onBeforeUnmount(() => {
  if (timeInterval) {
    clearInterval(timeInterval);
  }
});

// Helper to check if a day is on the weekend
const isWeekend = (date: Date) => {
  const day = getDay(date);
  return day === 0 || day === 6;
};

// Get events for a specific day
const getEventsForDay = (day: Date) => {
  return props.events.filter((event) => {
    const startDate = parseISO(event.startTime);
    return isSameDay(startDate, day);
  });
};

// Get event position based on time
const getEventPosition = (event: CalendarEvent) => {
  const startDate = parseISO(event.startTime);
  const endDate = parseISO(event.endTime);

  // Calculate position based on time
  const startHour = startDate.getHours() + startDate.getMinutes() / 60;
  const duration = (endDate.getTime() - startDate.getTime()) / (1000 * 60 * 60); // Duration in hours

  return {
    top: `${startHour * 60}px`,
    height: `${duration * 60}px`,
  };
};

// Get event day column
const getEventDayColumn = (event: CalendarEvent) => {
  const startDate = parseISO(event.startTime);
  const dayOfWeek = getDay(startDate);
  return dayOfWeek;
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
const formatDate = (date: Date, formatStr: string = "eee d") => {
  return format(date, formatStr);
};

// Event time display
const getEventTimeDisplay = (event: CalendarEvent) => {
  const startDate = parseISO(event.startTime);
  const endDate = parseISO(event.endTime);

  const startFormat = format(startDate, "h:mm a");
  const endFormat = format(endDate, "h:mm a");

  return `${startFormat} - ${endFormat}`;
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

// Handle time slot click
const onTimeSlotClick = (day: Date, hour: number) => {
  emit("time-slot-click", day, hour);
};
</script>

<template>
  <div class="week-view">
    <!-- Day headers -->
    <div class="flex border-b border-gray-200">
      <div class="w-20 text-sm font-medium p-2 sticky left-0 bg-white">
        Time
      </div>
      <div
        v-for="(day, index) in weekDays"
        :key="index"
        class="flex-1 text-sm p-2 text-center font-medium border-l border-gray-200 cursor-pointer"
        :class="[
          isToday(day) ? 'bg-indigo-50' : '',
          !showWeekends && isWeekend(day) ? 'opacity-50' : '',
        ]"
        @click="onDayClick(day)"
      >
        <div>{{ formatDate(day, "EEE") }}</div>
        <div
          class="rounded-full w-7 h-7 flex items-center justify-center mx-auto mt-1"
          :class="{
            'bg-indigo-600 text-white': isToday(day) && highlightToday,
            'hover:bg-indigo-100': !isToday(day) || !highlightToday,
          }"
        >
          {{ formatDate(day, "d") }}
        </div>
      </div>
    </div>

    <!-- Time grid -->
    <div class="flex relative">
      <!-- Time labels -->
      <div class="w-20 sticky left-0 z-10 bg-white">
        <div
          v-for="hourSlot in hours"
          :key="hourSlot.hour"
          class="h-16 border-b border-r border-gray-200 flex items-center justify-center text-xs font-medium"
        >
          {{ hourSlot.label }}
        </div>
      </div>

      <!-- Day columns -->
      <div class="flex-grow grid grid-cols-7 relative">
        <!-- Hour rows -->
        <template v-for="hourSlot in hours" :key="hourSlot.hour">
          <div
            v-for="(day, dayIndex) in weekDays"
            :key="`${hourSlot.hour}-${dayIndex}`"
            class="h-16 border-b border-r border-gray-200 relative"
            :class="[
              isToday(day) ? 'bg-indigo-50/50' : '',
              !showWeekends && isWeekend(day) ? 'opacity-50' : '',
            ]"
            @click="onTimeSlotClick(day, hourSlot.hour)"
          />
        </template>

        <!-- Events rendered absolutely positioned -->
        <div
          v-for="event in props.events"
          :key="event.id"
          class="absolute rounded-md p-1 overflow-hidden z-10 text-white transition-all duration-300 transform hover:translate-x-1 hover:scale-105 hover:shadow-md"
          :class="[getEventColor(event), { 'opacity-50': event.isCompleted }]"
          :style="{
            ...getEventPosition(event),
            left: `${getEventDayColumn(event) * (100 / 7)}%`,
            width: `${85 / 7}%`,
          }"
          @click.stop="onEventClick(event, $event)"
        >
          <div class="flex items-center text-xs font-medium">
            <span class="mr-1" v-html="getEventIcon(event)" />
            <span class="truncate">{{ event.title }}</span>
          </div>
          <div class="text-xs opacity-90">{{ getEventTimeDisplay(event) }}</div>
        </div>

        <!-- Current time indicator -->
        <div
          v-if="isToday(props.currentDate)"
          class="absolute w-full h-0.5 bg-red-500 z-20"
          :style="`top: ${currentTime.hour * 60 + currentTime.minute}px;`"
        >
          <div
            class="absolute -left-1 -top-1.5 w-3 h-3 rounded-full bg-red-500"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.week-view {
  transition: all 0.3s ease-in-out;
}

/* Scrollable time grid */
.flex.relative {
  overflow-x: auto;
}
</style>
