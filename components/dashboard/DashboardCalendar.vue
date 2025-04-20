<!-- components/dashboard/DashboardCalendar.vue -->
<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";
import {
  format,
  startOfWeek,
  endOfWeek,
  isSameMonth,
  addMonths,
  subMonths,
  addWeeks,
  subWeeks,
  addDays,
  subDays,
  addYears,
  subYears,
} from "date-fns";
import { useCalendarState } from "~/composables/useCalendarState";
import type {
  CalendarEvent,
  Reminder,
  CalendarViewType,
} from "~/composables/useCalendarState";

// Props
interface Props {
  events?: CalendarEvent[];
  userType?: "student" | "instructor" | "client" | "consultant" | "admin";
  initialView?: CalendarViewType;
  highlightToday?: boolean;
  showWeekends?: boolean;
  allowCreate?: boolean;
  allowEdit?: boolean;
  isLoading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  events: () => [],
  userType: "student",
  initialView: "month",
  highlightToday: true,
  showWeekends: true,
  allowCreate: true,
  allowEdit: true,
  isLoading: false,
});

// Emitted events
const emit = defineEmits<{
  (e: "event-create", dateInfo: { date: Date; view: string }): void;
  (e: "reminder-set", eventId: string, reminder: Reminder): void;
  (e: "view-change" | "reminder-cancel", view: string): void;
  (e: "date-change", date: Date): void;
  (e: "event-click" | "event-update", event: CalendarEvent): void;
}>();

// Initialize calendar state using composable
const calendar = useCalendarState();

// Set initial values in the state
calendar.initialize({
  events: props.events,
  userType: props.userType,
  currentView: props.initialView,
  highlightToday: props.highlightToday,
  showWeekends: props.showWeekends,
  allowCreate: props.allowCreate,
  allowEdit: props.allowEdit,
});

// State variables
const selectedEvent = ref<CalendarEvent | null>(null);
const calendarHeight = ref("auto");
const isFullScreen = ref(false);
const viewTransitioning = ref(false);
const showDrawer = ref(false);
const drawerMode = ref<"viewEvent" | "createEvent">("viewEvent");
const selectedDate = ref(new Date());

// Update calendar height based on view
const updateCalendarHeight = () => {
  if (calendar.state.value.currentView === "year") {
    calendarHeight.value = "800px";
  } else if (calendar.state.value.currentView === "month") {
    calendarHeight.value = "800px";
  } else if (calendar.state.value.currentView === "week") {
    calendarHeight.value = "600px";
  } else if (calendar.state.value.currentView === "day") {
    calendarHeight.value = "700px";
  } else {
    calendarHeight.value = "auto";
  }
};

// View animation states
const viewStates = {
  year: ref(calendar.state.value.currentView === "year"),
  month: ref(calendar.state.value.currentView === "month"),
  week: ref(calendar.state.value.currentView === "week"),
  day: ref(calendar.state.value.currentView === "day"),
  agenda: ref(calendar.state.value.currentView === "agenda"),
};

// Change calendar view
const changeView = (view: CalendarViewType) => {
  if (view === calendar.state.value.currentView) return;

  viewTransitioning.value = true;

  // Set animation states
  viewStates[view].value = true;

  // After animation, update view in store and hide previous view
  setTimeout(() => {
    viewStates[calendar.state.value.currentView].value = false;
    calendar.setView(view);
    updateCalendarHeight();
    emit("view-change", view);

    // Complete transition
    setTimeout(() => {
      viewTransitioning.value = false;
    }, 300);
  }, 300);
};

// Handle event click
const onEventClick = (event: CalendarEvent) => {
  selectedEvent.value = event;
  drawerMode.value = "viewEvent";
  showDrawer.value = true;
  emit("event-click", event);
};

// Handle month click in year view
const onMonthClick = (date: Date) => {
  calendar.setDate(date);
  changeView("month");
  emit("date-change", date);
};

// Handle time slot click for creating events
const onTimeSlotClick = (date: Date) => {
  if (!calendar.state.value.allowCreate) return;

  selectedDate.value = date;
  drawerMode.value = "createEvent";
  showDrawer.value = true;

  emit("event-create", {
    date: date,
    view: calendar.state.value.currentView,
  });
};

// Handle create reminder
const onCreateReminder = (eventId: string, reminder: Reminder) => {
  calendar.addReminder(eventId, reminder);
  emit("reminder-set", eventId, reminder);
};

// Handle reminder cancel
const onCancelReminder = (eventId: string) => {
  calendar.removeReminder(eventId);
  emit("reminder-cancel", eventId);
};

// Mark event as complete
const markEventComplete = (event: CalendarEvent) => {
  const updatedEvent = { ...event, isCompleted: true };
  calendar.updateEvent(updatedEvent);
  emit("event-update", updatedEvent);
};

// Toggle fullscreen mode
const toggleFullScreen = () => {
  isFullScreen.value = !isFullScreen.value;

  const calendarElement = document.getElementById("dashboard-calendar");
  if (!calendarElement) return;

  if (isFullScreen.value) {
    if (calendarElement.requestFullscreen) {
      calendarElement.requestFullscreen();
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
};

// Close drawer
const closeDrawer = () => {
  showDrawer.value = false;
  selectedEvent.value = null;
};

// Handle drawer events
const handleEventSave = (event: CalendarEvent) => {
  if (drawerMode.value === "viewEvent" && selectedEvent.value) {
    // Update existing event
    calendar.updateEvent(event);
    emit("event-update", event);
  } else {
    // Create new event
    calendar.addEvent(event);
    emit("event-create", {
      date: new Date(event.startTime),
      view: calendar.state.value.currentView,
    });
  }

  closeDrawer();
};

// Handle date change from child components
const handleDateChange = (date: Date) => {
  calendar.setDate(date);
  emit("date-change", date);
};

// Get formatted title for the calendar header
const headerTitle = computed(() => {
  const date = calendar.state.value.currentDate;

  switch (calendar.state.value.currentView) {
    case "year":
      return format(date, "yyyy");
    case "month":
      return format(date, "MMMM yyyy");
    case "week": {
      const weekStart = startOfWeek(date);
      const weekEnd = endOfWeek(date);
      if (isSameMonth(weekStart, weekEnd)) {
        return `${format(weekStart, "MMMM d")} - ${format(weekEnd, "d, yyyy")}`;
      } else {
        return `${format(weekStart, "MMM d")} - ${format(
          weekEnd,
          "MMM d, yyyy"
        )}`;
      }
    }
    case "day":
      return format(date, "EEEE, MMMM d, yyyy");
    case "agenda":
      return "Upcoming Events";
    default:
      return format(date, "MMMM yyyy");
  }
});

// Handle navigation
const navigate = (direction: "next" | "prev") => {
  const { currentDate, currentView } = calendar.state.value;
  let newDate: Date;

  if (currentView === "year") {
    newDate =
      direction === "next"
        ? addYears(currentDate, 1)
        : subYears(currentDate, 1);
  } else if (currentView === "month") {
    newDate =
      direction === "next"
        ? addMonths(currentDate, 1)
        : subMonths(currentDate, 1);
  } else if (currentView === "week") {
    newDate =
      direction === "next"
        ? addWeeks(currentDate, 1)
        : subWeeks(currentDate, 1);
  } else if (currentView === "day") {
    newDate =
      direction === "next" ? addDays(currentDate, 1) : subDays(currentDate, 1);
  } else {
    newDate = currentDate; // Default, shouldn't happen
  }

  calendar.setDate(newDate);
  emit("date-change", newDate);
};

// Go to today
const goToToday = () => {
  calendar.setDate(new Date());
  emit("date-change", calendar.state.value.currentDate);
};

// Watch for view changes and update animation states
watch(
  () => calendar.state.value.currentView,
  () => {
    updateCalendarHeight();
  }
);

// Watch for external event changes
watch(
  () => props.events,
  (newEvents) => {
    // Update events in the calendar state
    calendar.state.value.events = newEvents;
  },
  { deep: true }
);

// Window resize handler
const onWindowResize = () => {
  updateCalendarHeight();
};

// Add resize listener on mount
onMounted(() => {
  window.addEventListener("resize", onWindowResize);
  updateCalendarHeight();
});

// Remove resize listener on unmount
onBeforeUnmount(() => {
  window.removeEventListener("resize", onWindowResize);
});
</script>

<template>
  <div
    id="dashboard-calendar"
    class="dashboard-calendar rounded-xl overflow-hidden border border-gray-200 shadow-lg transition-colors duration-300 bg-white"
    :class="{
      'fullscreen-calendar': isFullScreen,
    }"
  >
    <!-- Calendar Header -->
    <CommonCalendarHeader
      :title="headerTitle"
      :current-view="calendar.state.value.currentView"
      :is-loading="isLoading"
      :is-full-screen="isFullScreen"
      @change-view="changeView"
      @toggle-full-screen="toggleFullScreen"
      @navigate="navigate"
      @today="goToToday"
    />

    <!-- Calendar Loading State -->
    <div
      v-if="isLoading"
      class="flex items-center justify-center"
      :style="{ height: calendarHeight }"
    >
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      />
    </div>

    <!-- Calendar Content -->
    <div
      v-else
      class="calendar-content overflow-auto transition-all duration-300"
      :style="{ height: calendarHeight }"
    >
      <!-- Year View -->
      <CommonCalendarYearView
        v-if="viewStates.year.value"
        :class="{
          'opacity-0 scale-95':
            viewTransitioning && calendar.state.value.currentView !== 'year',
          'opacity-100 scale-100':
            !viewTransitioning || calendar.state.value.currentView === 'year',
        }"
        :current-date="calendar.state.value.currentDate"
        :events="calendar.getFilteredEvents()"
        :highlight-today="calendar.state.value.highlightToday"
        :show-weekends="calendar.state.value.showWeekends"
        @month-click="onMonthClick"
        @day-click="handleDateChange"
        @event-click="onEventClick"
      />

      <!-- Month View -->
      <CommonCalendarMonthView
        v-if="viewStates.month.value"
        :class="{
          'opacity-0 scale-95':
            viewTransitioning && calendar.state.value.currentView !== 'month',
          'opacity-100 scale-100':
            !viewTransitioning || calendar.state.value.currentView === 'month',
        }"
        :current-date="calendar.state.value.currentDate"
        :events="calendar.getFilteredEvents()"
        :highlight-today="calendar.state.value.highlightToday"
        :show-weekends="calendar.state.value.showWeekends"
        @day-click="handleDateChange"
        @event-click="onEventClick"
        @time-slot-click="onTimeSlotClick"
      />

      <!-- Week View -->
      <CommonCalendarWeekView
        v-if="viewStates.week.value"
        :class="{
          'opacity-0 scale-95':
            viewTransitioning && calendar.state.value.currentView !== 'week',
          'opacity-100 scale-100':
            !viewTransitioning || calendar.state.value.currentView === 'week',
        }"
        :current-date="calendar.state.value.currentDate"
        :events="calendar.getFilteredEvents()"
        :highlight-today="calendar.state.value.highlightToday"
        :show-weekends="calendar.state.value.showWeekends"
        @day-click="handleDateChange"
        @event-click="onEventClick"
        @time-slot-click="onTimeSlotClick"
      />

      <!-- Day View -->
      <CommonCalendarDayView
        v-if="viewStates.day.value"
        :class="{
          'opacity-0 scale-95':
            viewTransitioning && calendar.state.value.currentView !== 'day',
          'opacity-100 scale-100':
            !viewTransitioning || calendar.state.value.currentView === 'day',
        }"
        :current-date="calendar.state.value.currentDate"
        :events="calendar.getFilteredEvents()"
        :highlight-today="calendar.state.value.highlightToday"
        @event-click="onEventClick"
        @time-slot-click="onTimeSlotClick"
      />

      <!-- Agenda View -->
      <CommonCalendarAgendaView
        v-if="viewStates.agenda.value"
        :class="{
          'opacity-0 scale-95':
            viewTransitioning && calendar.state.value.currentView !== 'agenda',
          'opacity-100 scale-100':
            !viewTransitioning || calendar.state.value.currentView === 'agenda',
        }"
        :current-date="calendar.state.value.currentDate"
        :events="calendar.getFilteredEvents()"
        @event-click="onEventClick"
      />
    </div>

    <!-- Event Details Drawer -->
    <UiDrawer
      v-model="showDrawer"
      :title="drawerMode === 'viewEvent' ? 'Event Details' : 'New Event'"
      width="400px"
      position="right"
      :resizable="true"
      :min-width="'320px'"
      :max-width="'600px'"
      @close="closeDrawer"
    >
      <template #default>
        <!-- View Event Details -->
        <CommonEventDetails
          v-if="drawerMode === 'viewEvent' && selectedEvent"
          :event="selectedEvent"
          :user-type="calendar.state.value.userType"
          :has-reminder="calendar.hasReminder(selectedEvent.id)"
          :reminder="calendar.getReminderForEvent(selectedEvent.id)"
          @create-reminder="onCreateReminder"
          @cancel-reminder="onCancelReminder"
          @mark-complete="markEventComplete"
          @update-event="handleEventSave"
        />

        <!-- Create New Event -->
        <CommonEventForm
          v-else
          :user-type="calendar.state.value.userType"
          :initial-date="selectedDate"
          @save="handleEventSave"
          @cancel="closeDrawer"
        />
      </template>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 font-medium transition-colors"
            @click="closeDrawer"
          >
            Close
          </button>
        </div>
      </template>
    </UiDrawer>
  </div>
</template>

<style scoped>
.calendar-content {
  position: relative;
}

/* Transitions for views */
.opacity-0 {
  opacity: 0;
}

.opacity-100 {
  opacity: 1;
}

.scale-95 {
  transform: scale(0.95);
}

.scale-100 {
  transform: scale(1);
}

/* Transition durations */
.calendar-content > * {
  transition: all 0.3s ease-in-out;
}

/* Fullscreen calendar */
.fullscreen-calendar {
  position: fixed !important;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 100;
  margin: 0;
  border-radius: 0;
  border: none;
  height: 100vh;
  width: 100vw;
}

.fullscreen-calendar .calendar-content {
  height: calc(100vh - 64px) !important;
}

/* Animation for spinner */
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
