<!-- components/dashboard/calendar/CalendarYearView.vue -->
<script setup lang="ts">
import {
  eachMonthOfInterval,
  startOfYear,
  endOfYear,
  format,
  isToday,
  isSameMonth,
  getMonth,
  parseISO,
//   setMonth,
  startOfMonth,
  endOfMonth,
  eachDayOfInterval,
  isSameDay,
  getDay,
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
  (e: "month-click" | "day-click", date: Date): void;
  (e: "event-click", event: CalendarEvent): void;
}>();

// Get all months in the current year
const months = computed(() => {
  const start = startOfYear(props.currentDate);
  const end = endOfYear(props.currentDate);
  return eachMonthOfInterval({ start, end });
});

// Get year
const currentYear = computed(() => {
  return format(props.currentDate, "yyyy");
});

// Get events count for each month
const getEventsCountForMonth = (month: Date) => {
  return props.events.filter((event) => {
    const eventDate = parseISO(event.startTime);
    return isSameMonth(eventDate, month);
  }).length;
};

// Get the most eventful month (for highlighting)
const mostEventfulMonth = computed(() => {
  let maxEvents = 0;
  let maxMonth = 0;

  months.value.forEach((month, index) => {
    const count = getEventsCountForMonth(month);
    if (count > maxEvents) {
      maxEvents = count;
      maxMonth = index;
    }
  });

  return maxMonth;
});

// Get calendar days for each month
const getMonthDays = (month: Date) => {
  const monthStart = startOfMonth(month);
  const monthEnd = endOfMonth(month);
  const days = eachDayOfInterval({ start: monthStart, end: monthEnd });

  // Create empty slots for days of the week before the first day of month
  const firstDayOfWeek = getDay(days[0]);
  const emptyDaysBefore = Array(firstDayOfWeek).fill(null);

  return [...emptyDaysBefore, ...days];
};

// Get events for a specific day
const getEventsForDay = (day: Date) => {
  if (!day) return []; // Handle empty days

  return props.events.filter((event) => {
    const eventDate = parseISO(event.startTime);
    return isSameDay(eventDate, day);
  });
};

// Format month name
const formatMonthName = (month: Date) => {
  return format(month, "MMM");
};

// Handle month click
const onMonthClick = (month: Date) => {
  emit("month-click", month);
};

// Handle day click
const onDayClick = (day: Date) => {
  if (day) {
    // Only handle clicks on actual days, not empty slots
    emit("day-click", day);
  }
};

// Handle event click
const onEventClick = (event: CalendarEvent, e: Event) => {
  e.stopPropagation();
  emit("event-click", event);
};

// Get events color indicators for a day
const getEventIndicators = (day: Date, maxCount = 3) => {
  if (!day) return []; // Handle empty days

  const dayEvents = getEventsForDay(day);
  return dayEvents.slice(0, maxCount);
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

// Helper to check if a day is on the weekend
const isWeekend = (date: Date) => {
  if (!date) return false; // Handle empty days
  const day = getDay(date);
  return day === 0 || day === 6;
};

// Get className for day cell
const getDayClass = (day: Date) => {
  if (!day) return "invisible"; // Hide empty days

  let classes =
    "relative w-full aspect-square text-xs p-1 rounded-sm transition-all duration-200 ";

  if (isToday(day)) {
    classes += "bg-indigo-50 font-bold ";
  }

  if (getEventsForDay(day).length > 0) {
    classes += "hover:bg-indigo-100 cursor-pointer ";
  } else {
    classes += "hover:bg-gray-100 cursor-pointer ";
  }

  if (isWeekend(day) && !props.showWeekends) {
    classes += "opacity-50 ";
  }

  return classes;
};

// Get class for month container
const getMonthClass = (monthIndex: number) => {
  const month = months.value[monthIndex];
  const currentMonth = getMonth(props.currentDate);

  let classes =
    "month-container p-1 rounded-lg transition-all duration-300 hover:shadow-md ";

  if (isSameMonth(month, new Date())) {
    classes += "ring-2 ring-indigo-500 ";
  }

  if (monthIndex === mostEventfulMonth.value) {
    classes += "bg-indigo-50 ";
  }

  if (getMonth(month) === currentMonth) {
    classes += "bg-blue-50 ";
  }

  return classes;
};
</script>

<template>
  <div class="year-view p-4">
    <h2 class="text-2xl font-bold mb-4 text-center">{{ currentYear }}</h2>

    <!-- Grid of months -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="(month, monthIndex) in months"
        :key="monthIndex"
        class="month-wrapper"
      >
        <div :class="getMonthClass(monthIndex)" @click="onMonthClick(month)">
          <!-- Month header -->
          <div class="flex justify-between items-center mb-2 px-1">
            <h3 class="text-sm font-semibold text-gray-700">
              {{ formatMonthName(month) }}
            </h3>
            <span
              v-if="getEventsCountForMonth(month) > 0"
              class="text-xs px-1.5 py-0.5 bg-indigo-100 text-indigo-800 rounded-full font-medium"
            >
              {{ getEventsCountForMonth(month) }}
            </span>
          </div>

          <!-- Days of week header -->
          <div class="grid grid-cols-7 text-center mb-1">
            <div
              v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']"
              :key="day"
              class="text-[0.6rem] text-gray-500 font-medium"
            >
              {{ day }}
            </div>
          </div>

          <!-- Calendar days -->
          <div class="grid grid-cols-7 gap-px">
            <div
              v-for="(day, dayIndex) in getMonthDays(month)"
              :key="`${monthIndex}-${dayIndex}`"
              :class="getDayClass(day)"
              @click="day && onDayClick(day)"
            >
              <!-- Day number -->
              <div
                v-if="day"
                class="flex justify-center items-center h-5 w-5 text-[0.7rem] rounded-full"
                :class="isToday(day) ? 'bg-indigo-600 text-white' : ''"
              >
                {{ format(day, "d") }}
              </div>

              <!-- Event indicators -->
              <div
                v-if="day"
                class="event-indicators flex flex-wrap gap-0.5 mt-1 justify-center"
              >
                <div
                  v-for="(event, eventIndex) in getEventIndicators(day)"
                  :key="eventIndex"
                  class="h-1 w-1 rounded-full"
                  :class="getEventColor(event)"
                  @click.stop="onEventClick(event, $event)"
                ></div>
                <div
                  v-if="getEventsForDay(day).length > 3"
                  class="text-[0.5rem] text-gray-500"
                >
                  +{{ getEventsForDay(day).length - 3 }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.year-view {
  transition: all 0.3s ease-in-out;
}

.month-container {
  transition: all 0.3s ease-in-out;
  cursor: pointer;
}

.month-container:hover {
  transform: translateY(-2px);
}
</style>
