<!-- components/dashboard/DashboardCalendar.vue -->
<script setup lang="ts">
import {
  format,
  startOfWeek,
  endOfWeek,
  eachDayOfInterval,
  addDays,
  getDay,
  parse,
  startOfMonth,
  endOfMonth,
  isSameDay,
  isSameMonth,
  addMonths,
  subMonths,
  isToday,
  parseISO,
  isBefore,
  isAfter,
  addMinutes,
} from "date-fns";

// Types
interface CalendarEvent {
  id: string;
  title: string;
  description?: string;
  startTime: string; // ISO string format
  endTime: string; // ISO string format
  type:
    | "course"
    | "meeting"
    | "assignment"
    | "reminder"
    | "consultation"
    | "deadline";
  location?: string;
  url?: string;
  color?: string;
  reminderSet?: boolean;
  courseId?: number;
  projectId?: number;
  isCompleted?: boolean;
  attendees?: Array<{
    id: string;
    name: string;
    avatar: string;
    role: string;
  }>;
}

interface Reminder {
  eventId: string;
  time: string; // ISO string format
  type: "email" | "push" | "both";
  message?: string;
}

// Props
interface Props {
  events?: CalendarEvent[];
  userType?: "student" | "instructor" | "client" | "consultant" | "admin";
  initialView?: "month" | "week" | "day" | "agenda";
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
  (e: "event-click", event: CalendarEvent): void;
  (e: "event-create", dateInfo: { date: Date; view: string }): void;
  (e: "reminder-set", eventId: string, reminder: Reminder): void;
  (e: "reminder-cancel", eventId: string): void;
  (e: "view-change", view: string): void;
  (e: "date-change", date: Date): void;
  (e: "event-update", event: CalendarEvent): void;
}>();

// State variables
const currentView = ref(props.initialView);
const currentDate = ref(new Date());
const selectedDate = ref(new Date());
const selectedEvent = ref<CalendarEvent | null>(null);
const reminders = ref<Reminder[]>([]);
const showEventDetailsModal = ref(false);
const showCreateEventModal = ref(false);
const showReminderModal = ref(false);
const isCreatingReminder = ref(false);
const newReminder = ref<Partial<Reminder>>({
  type: "both",
  time: "",
});
const focusedDay = ref<Date | null>(null);
const isDraggingEvent = ref(false);
const draggedEvent = ref<CalendarEvent | null>(null);
const isResizingEvent = ref(false);
const calendarHeight = ref("auto");
const animating = ref(false);
const showCalendarMenu = ref(false);
const isFullScreen = ref(false);
const animationDirection = ref("");
const viewTransitioning = ref(false);
const currentDragPosition = ref({ x: 0, y: 0 });

// Animation states for each calendar element
const monthViewVisible = ref(currentView.value === "month");
const weekViewVisible = ref(currentView.value === "week");
const dayViewVisible = ref(currentView.value === "day");
const agendaViewVisible = ref(currentView.value === "agenda");

// Mock data for reminders (in a real app, this would come from an API)
onMounted(() => {
  // Sample reminders
  reminders.value = [
    {
      eventId: props.events[0]?.id || "dummy1",
      time: new Date(new Date().getTime() - 30 * 60000).toISOString(), // 30 minutes before now
      type: "both",
      message: "Get ready for your class!",
    },
    {
      eventId: props.events[1]?.id || "dummy2",
      time: new Date(new Date().getTime() - 60 * 60000).toISOString(), // 1 hour before now
      type: "email",
      message: "Prepare for your consultation session",
    },
  ];
});

// Helper to check if the calendar should be in dark mode
const isDarkMode = ref(false);

// Check for system dark mode preference on component mount
onMounted(() => {
  const darkModePreference = localStorage.getItem("darkMode");
  if (
    darkModePreference === "true" ||
    (!darkModePreference &&
      window.matchMedia("(prefers-color-scheme: dark)").matches)
  ) {
    isDarkMode.value = true;
  }

  // Set calendar height based on current view
  updateCalendarHeight();

  // Listen for dark mode changes
  window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", (e) => {
      if (localStorage.getItem("darkMode") === null) {
        // Only update if no manual preference is set
        isDarkMode.value = e.matches;
      }
    });
});

// Update calendar height based on view
const updateCalendarHeight = () => {
  if (currentView.value === "month") {
    calendarHeight.value = "800px";
  } else if (currentView.value === "week") {
    calendarHeight.value = "600px";
  } else if (currentView.value === "day") {
    calendarHeight.value = "700px";
  } else {
    calendarHeight.value = "auto";
  }
};

// Month name for display
const currentMonthName = computed(() => {
  return format(currentDate.value, "MMMM yyyy");
});

// Date navigation methods
const previousMonth = () => {
  animationDirection.value = "slide-right";
  animating.value = true;

  setTimeout(() => {
    currentDate.value = subMonths(currentDate.value, 1);
    animating.value = false;
  }, 300);
};

const nextMonth = () => {
  animationDirection.value = "slide-left";
  animating.value = true;

  setTimeout(() => {
    currentDate.value = addMonths(currentDate.value, 1);
    animating.value = false;
  }, 300);
};

const goToToday = () => {
  animationDirection.value = "fade";
  animating.value = true;

  setTimeout(() => {
    currentDate.value = new Date();
    selectedDate.value = new Date();
    emit("date-change", currentDate.value);
    animating.value = false;
  }, 300);
};

// Calendar view methods
const changeView = (view: "month" | "week" | "day" | "agenda") => {
  if (view === currentView.value) return;

  viewTransitioning.value = true;

  // Update visibility flags for animation
  if (view === "month") monthViewVisible.value = true;
  else if (view === "week") weekViewVisible.value = true;
  else if (view === "day") dayViewVisible.value = true;
  else if (view === "agenda") agendaViewVisible.value = true;

  // Set animation direction based on "hierarchy" of views
  const viewOrder = ["month", "week", "day", "agenda"];
  const currentIdx = viewOrder.indexOf(currentView.value);
  const newIdx = viewOrder.indexOf(view);

  if (newIdx > currentIdx) {
    animationDirection.value = "zoom-in";
  } else {
    animationDirection.value = "zoom-out";
  }

  // After a short delay, update the current view and finish transition
  setTimeout(() => {
    // Hide previous view
    if (currentView.value === "month") monthViewVisible.value = false;
    else if (currentView.value === "week") weekViewVisible.value = false;
    else if (currentView.value === "day") dayViewVisible.value = false;
    else if (currentView.value === "agenda") agendaViewVisible.value = false;

    currentView.value = view;
    updateCalendarHeight();
    emit("view-change", view);

    // Complete transition
    setTimeout(() => {
      viewTransitioning.value = false;
    }, 300);
  }, 300);
};

// Calendar days generation
const calendarDays = computed(() => {
  let days = [];

  if (currentView.value === "month") {
    // First day of the month
    const firstDay = startOfMonth(currentDate.value);
    // Last day of the month
    const lastDay = endOfMonth(currentDate.value);

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
  } else if (currentView.value === "week") {
    // Week view shows 7 days starting from sunday or monday
    const start = startOfWeek(currentDate.value);
    const end = endOfWeek(currentDate.value);
    days = [eachDayOfInterval({ start, end })];
  } else if (currentView.value === "day") {
    // Day view shows just the selected date
    days = [[currentDate.value]];
  }

  return days;
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

// Filter events for the selected date and view
const filteredEvents = computed(() => {
  if (!props.events.length) return [];

  if (currentView.value === "month") {
    // For month view, include all events in the displayed month
    return props.events.filter((event) => {
      const startDate = parseISO(event.startTime);
      const firstDay = calendarDays.value[0][0];
      const lastDay = calendarDays.value[calendarDays.value.length - 1][6];

      return isAfter(startDate, firstDay) && isBefore(startDate, lastDay);
    });
  } else if (currentView.value === "week") {
    // For week view, include all events in the current week
    const startOfWeekDate = startOfWeek(currentDate.value);
    const endOfWeekDate = endOfWeek(currentDate.value);

    return props.events.filter((event) => {
      const startDate = parseISO(event.startTime);
      return (
        isAfter(startDate, startOfWeekDate) &&
        isBefore(startDate, endOfWeekDate)
      );
    });
  } else if (currentView.value === "day") {
    // For day view, include only events on the selected date
    return props.events.filter((event) => {
      const startDate = parseISO(event.startTime);
      return isSameDay(startDate, currentDate.value);
    });
  } else if (currentView.value === "agenda") {
    // For agenda view, include events from today onwards
    const today = new Date();
    return props.events
      .filter((event) => {
        const startDate = parseISO(event.startTime);
        return !isBefore(startDate, today) || isToday(startDate);
      })
      .sort((a, b) => {
        return (
          parseISO(a.startTime).getTime() - parseISO(b.startTime).getTime()
        );
      });
  }

  return [];
});

// Group events by date for agenda view
const eventsGroupedByDate = computed(() => {
  if (currentView.value !== "agenda") return {};

  const groupedEvents: Record<string, CalendarEvent[]> = {};

  filteredEvents.value.forEach((event) => {
    const dateKey = format(parseISO(event.startTime), "yyyy-MM-dd");

    if (!groupedEvents[dateKey]) {
      groupedEvents[dateKey] = [];
    }

    groupedEvents[dateKey].push(event);
  });

  return groupedEvents;
});

// Format date for display in agenda view
const formatAgendaDate = (dateKey: string) => {
  const date = parse(dateKey, "yyyy-MM-dd", new Date());
  return format(date, "EEEE, MMMM d, yyyy");
};

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

// Event rendering helpers
const getEventPosition = (event: CalendarEvent, day: Date) => {
  const startDate = parseISO(event.startTime);
  const endDate = parseISO(event.endTime);

  if (currentView.value === "month") {
    return {}; // Month view events are positioned differently
  } else if (currentView.value === "week" || currentView.value === "day") {
    // Calculate position based on time
    const startHour = startDate.getHours() + startDate.getMinutes() / 60;
    const duration =
      (endDate.getTime() - startDate.getTime()) / (1000 * 60 * 60); // Duration in hours

    return {
      top: `${startHour * 60}px`,
      height: `${duration * 60}px`,
    };
  }

  return {};
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

// Helper to check if a day is on the weekend
const isWeekend = (date: Date) => {
  const day = getDay(date);
  return day === 0 || day === 6;
};

// Day styling helpers
const getDayClass = (day: Date) => {
  let classes = "relative rounded-lg transition-all duration-200 ";

  if (isToday(day)) {
    classes += "bg-indigo-50 dark:bg-indigo-900/30 ";
  }

  if (isSameMonth(day, currentDate.value)) {
    classes += "text-gray-900 dark:text-gray-100 ";
  } else {
    classes +=
      "text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/30 ";
  }

  if (isSameDay(day, selectedDate.value)) {
    classes += "ring-2 ring-indigo-500 font-bold ";
  }

  if (isSameDay(day, focusedDay.value || new Date())) {
    classes += "shadow-md ";
  }

  if (isWeekend(day) && !props.showWeekends) {
    classes += "opacity-50 ";
  }

  if (hasEvents(day)) {
    classes +=
      "hover:bg-indigo-100 dark:hover:bg-indigo-800/30 cursor-pointer ";
  } else {
    classes += "hover:bg-gray-100 dark:hover:bg-gray-800/50 cursor-pointer ";
  }

  return classes;
};

// Format date for display
const formatDate = (date: Date, formatStr: string = "MMM d") => {
  return format(date, formatStr);
};

// Handle day click
const onDayClick = (day: Date) => {
  selectedDate.value = day;
  currentDate.value = day;
  emit("date-change", day);

  // If day is clicked in month view, consider showing day view of that date
  if (currentView.value === "month") {
    // Optionally switch to day view
  }
};

// Handle event click
const onEventClick = (event: CalendarEvent, evt: MouseEvent) => {
  evt.stopPropagation();
  selectedEvent.value = event;
  showEventDetailsModal.value = true;
  emit("event-click", event);
};

// Handle time slot click for creating events
const onTimeSlotClick = (date: Date, hour?: number) => {
  if (!props.allowCreate) return;

  const newDate = new Date(date);

  if (hour !== undefined) {
    newDate.setHours(hour);
    newDate.setMinutes(0);
    newDate.setSeconds(0);
  }

  showCreateEventModal.value = true;
  emit("event-create", { date: newDate, view: currentView.value });
};

// Handle create reminder
const onCreateReminder = () => {
  if (!selectedEvent.value || !newReminder.value.time) return;

  isCreatingReminder.value = true;

  // In a real app, this would be an API call
  setTimeout(() => {
    const reminder: Reminder = {
      eventId: selectedEvent.value!.id,
      time: newReminder.value.time!,
      type: newReminder.value.type as "email" | "push" | "both",
      message: newReminder.value.message,
    };

    reminders.value.push(reminder);
    emit("reminder-set", selectedEvent.value!.id, reminder);

    isCreatingReminder.value = false;
    showReminderModal.value = false;

    // Reset reminder form
    newReminder.value = {
      type: "both",
      time: "",
    };
  }, 1000);
};

// Handle reminder cancel
const onCancelReminder = (eventId: string) => {
  reminders.value = reminders.value.filter(
    (reminder) => reminder.eventId !== eventId
  );
  emit("reminder-cancel", eventId);
};

// Calculate time until reminder
const getTimeUntilReminder = (reminderTime: string) => {
  const now = new Date();
  const reminderDate = parseISO(reminderTime);

  if (isBefore(reminderDate, now)) {
    return "Notified";
  }

  const diffMs = reminderDate.getTime() - now.getTime();
  const diffMinutes = Math.floor(diffMs / 60000);

  if (diffMinutes < 60) {
    return `${diffMinutes} minute${diffMinutes !== 1 ? "s" : ""} before`;
  } else if (diffMinutes < 1440) {
    const hours = Math.floor(diffMinutes / 60);
    return `${hours} hour${hours !== 1 ? "s" : ""} before`;
  } else {
    const days = Math.floor(diffMinutes / 1440);
    return `${days} day${days !== 1 ? "s" : ""} before`;
  }
};

// Mark event as complete
const markEventComplete = (event: CalendarEvent) => {
  event.isCompleted = true;
  emit("event-update", event);
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

// Watch for view changes and update animation states
watch(currentView, () => {
  updateCalendarHeight();
});

// Watch for external event changes
watch(
  () => props.events,
  () => {
    // Handle external event updates
  },
  { deep: true }
);

// Drag and drop functionality for events
const onDragStart = (event: CalendarEvent, evt: DragEvent) => {
  if (!props.allowEdit || !evt.dataTransfer) return;

  isDraggingEvent.value = true;
  draggedEvent.value = event;
  evt.dataTransfer.effectAllowed = "move";
  evt.dataTransfer.setData("text/plain", event.id);

  // Add a custom class to the body to style during drag
  document.body.classList.add("event-dragging");
};

const onDragOver = (day: Date, evt: DragEvent) => {
  if (!props.allowEdit || !isDraggingEvent.value || !evt.dataTransfer) return;

  evt.preventDefault();
  evt.dataTransfer.dropEffect = "move";

  // Update current drag position
  currentDragPosition.value = { x: evt.clientX, y: evt.clientY };

  // Highlight the target day
  focusedDay.value = day;
};

const onDragEnd = () => {
  isDraggingEvent.value = false;
  draggedEvent.value = null;
  focusedDay.value = null;

  // Remove the custom class from the body
  document.body.classList.remove("event-dragging");
};

const onDrop = (day: Date, evt: DragEvent) => {
  if (
    !props.allowEdit ||
    !isDraggingEvent.value ||
    !draggedEvent.value ||
    !evt.dataTransfer
  )
    return;

  evt.preventDefault();

  const eventId = evt.dataTransfer.getData("text/plain");
  if (!eventId) return;

  // In a real app, you would update the event date through an API
  // Here we're just emitting an update event
  const updatedEvent = { ...draggedEvent.value };

  // Calculate the new start and end times
  const startDate = parseISO(updatedEvent.startTime);
  const endDate = parseISO(updatedEvent.endTime);
  const duration = endDate.getTime() - startDate.getTime();

  const newStartDate = new Date(day);
  newStartDate.setHours(startDate.getHours());
  newStartDate.setMinutes(startDate.getMinutes());

  updatedEvent.startTime = newStartDate.toISOString();
  updatedEvent.endTime = new Date(
    newStartDate.getTime() + duration
  ).toISOString();

  emit("event-update", updatedEvent);

  // Reset state
  onDragEnd();
};

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

// Get day styling classes based on day contents
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

// Get event time display
const getEventTimeDisplay = (event: CalendarEvent) => {
  const startDate = parseISO(event.startTime);
  const endDate = parseISO(event.endTime);

  const startFormat = format(startDate, "h:mm a");
  const endFormat = format(endDate, "h:mm a");

  return `${startFormat} - ${endFormat}`;
};

// Check if an event has a reminder
const hasReminder = (eventId: string) => {
  return reminders.value.some((reminder) => reminder.eventId === eventId);
};

// Get reminder for an event
const getReminderForEvent = (eventId: string) => {
  return reminders.value.find((reminder) => reminder.eventId === eventId);
};

// Show Reminder Modal
const showAddReminderModal = (event: CalendarEvent) => {
  selectedEvent.value = event;

  // Set default reminder time to 30 minutes before event
  const eventTime = parseISO(event.startTime);
  const reminderTime = addMinutes(eventTime, -30);
  newReminder.value.time = reminderTime.toISOString();

  showReminderModal.value = true;
};

// Close modals
const closeModals = () => {
  showEventDetailsModal.value = false;
  showCreateEventModal.value = false;
  showReminderModal.value = false;
  selectedEvent.value = null;
};

// Return to today's date
const returnToToday = () => {
  currentDate.value = new Date();
  selectedDate.value = new Date();
  emit("date-change", new Date());
};
</script>

<template>
  <div
    id="dashboard-calendar"
    class="dashboard-calendar rounded-xl overflow-hidden border border-gray-200 shadow-lg transition-colors duration-300"
    :class="{
      'bg-white': !isDarkMode,
      'bg-gray-900 text-white': isDarkMode,
      'fullscreen-calendar': isFullScreen,
    }"
  >
    <!-- Calendar Header -->
    <div
      class="calendar-header border-b p-4 flex flex-col sm:flex-row items-center justify-between transition-colors duration-300"
      :class="{
        'bg-white': !isDarkMode,
        'bg-gray-800 border-gray-700': isDarkMode,
      }"
    >
      <!-- Date Navigation -->
      <div class="flex items-center mb-4 sm:mb-0">
        <h2
          class="text-xl font-bold mr-4 transition-all transform hover:scale-105 duration-300"
        >
          {{ currentMonthName }}
        </h2>

        <div class="flex space-x-2">
          <button
            class="p-2 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none"
            :class="{
              'hover:bg-gray-100 text-gray-700': !isDarkMode,
              'hover:bg-gray-700 text-gray-300': isDarkMode,
            }"
            aria-label="Previous month"
            @click="previousMonth"
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

          <button
            class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-300 transform hover:scale-105 focus:outline-none"
            :class="{
              'text-indigo-700 hover:bg-indigo-50': !isDarkMode,
              'text-indigo-400 hover:bg-indigo-900/30': isDarkMode,
            }"
            @click="goToToday"
          >
            Today
          </button>

          <button
            class="p-2 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none"
            :class="{
              'hover:bg-gray-100 text-gray-700': !isDarkMode,
              'hover:bg-gray-700 text-gray-300': isDarkMode,
            }"
            aria-label="Next month"
            @click="nextMonth"
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
      </div>

      <!-- View Tabs & Controls -->
      <div class="flex items-center space-x-1">
        <!-- View Options -->
        <div
          class="hidden sm:flex border rounded-lg overflow-hidden transition-colors duration-300"
          :class="{
            'border-gray-200': !isDarkMode,
            'border-gray-700': isDarkMode,
          }"
        >
          <button
            v-for="view in ['month', 'week', 'day', 'agenda']"
            :key="view"
            class="px-3 py-1.5 text-sm font-medium transition-all duration-300"
            :class="{
              'bg-indigo-600 text-white': currentView === view,
              'hover:bg-gray-100 text-gray-700':
                currentView !== view && !isDarkMode,
              'hover:bg-gray-700 text-gray-300':
                currentView !== view && isDarkMode,
            }"
            @click="changeView(view)"
          >
            {{ view.charAt(0).toUpperCase() + view.slice(1) }}
          </button>
        </div>

        <!-- Mobile View Selector -->
        <div class="sm:hidden relative">
          <button
            class="flex items-center px-3 py-1.5 border rounded-lg text-sm font-medium transition-colors duration-300"
            :class="{
              'border-gray-200 hover:bg-gray-100': !isDarkMode,
              'border-gray-700 hover:bg-gray-700': isDarkMode,
            }"
            @click="showCalendarMenu = !showCalendarMenu"
          >
            <span>{{
              currentView.charAt(0).toUpperCase() + currentView.slice(1)
            }}</span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 ml-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- View Dropdown -->
          <div
            v-if="showCalendarMenu"
            class="absolute right-0 mt-1 z-10 w-36 rounded-md shadow-lg transition-all duration-300 transform origin-top-right"
            :class="{
              'bg-white border border-gray-200': !isDarkMode,
              'bg-gray-800 border border-gray-700': isDarkMode,
            }"
          >
            <div class="py-1">
              <button
                v-for="view in ['month', 'week', 'day', 'agenda']"
                :key="view"
                class="block w-full px-4 py-2 text-sm text-left transition-colors duration-300"
                :class="{
                  'text-indigo-600 font-medium bg-indigo-50':
                    currentView === view && !isDarkMode,
                  'text-indigo-400 font-medium bg-indigo-900/30':
                    currentView === view && isDarkMode,
                  'text-gray-700 hover:bg-gray-100':
                    currentView !== view && !isDarkMode,
                  'text-gray-300 hover:bg-gray-700':
                    currentView !== view && isDarkMode,
                }"
                @click="
                  changeView(view);
                  showCalendarMenu = false;
                "
              >
                {{ view.charAt(0).toUpperCase() + view.slice(1) }}
              </button>
            </div>
          </div>
        </div>

        <!-- Fullscreen Toggle -->
        <button
          class="p-2 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none"
          :class="{
            'hover:bg-gray-100 text-gray-700': !isDarkMode,
            'hover:bg-gray-700 text-gray-300': isDarkMode,
          }"
          aria-label="Toggle fullscreen"
          @click="toggleFullScreen"
        >
          <svg
            v-if="!isFullScreen"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z"
              clip-rule="evenodd"
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
              d="M5 4a1 1 0 00-1 1v4a1 1 0 102 0V6.414l2.293 2.293a1 1 0 001.414-1.414L7.414 5H9a1 1 0 000-2H5zm14 0a1 1 0 00-1 1v4a1 1 0 102 0V6.414l-2.293 2.293a1 1 0 101.414 1.414L19.586 5H18a1 1 0 000 2h4a1 1 0 001-1V5a1 1 0 00-1-1h-4zm-6 10a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 000-2h-1.586l2.293-2.293a1 1 0 00-1.414-1.414L15 15.586V14a1 1 0 00-1-1h-1zm-8 0a1 1 0 100 2h1.586l-2.293 2.293a1 1 0 001.414 1.414L9 15.586V17a1 1 0 102 0v-4a1 1 0 00-1-1H5z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

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
      <!-- Month View -->
      <div
        v-if="monthViewVisible"
        class="month-view transition-all duration-300"
        :class="[
          {
            'opacity-0 scale-95': viewTransitioning && currentView !== 'month',
            'opacity-100 scale-100':
              !viewTransitioning || currentView === 'month',
          },
          animationDirection === 'slide-left' && animating
            ? 'animate-slide-left'
            : '',
          animationDirection === 'slide-right' && animating
            ? 'animate-slide-right'
            : '',
          animationDirection === 'fade' && animating ? 'animate-fade' : '',
          animationDirection === 'zoom-in' && animating
            ? 'animate-zoom-in'
            : '',
          animationDirection === 'zoom-out' && animating
            ? 'animate-zoom-out'
            : '',
        ]"
      >
        <!-- Month Grid Headers (Days of Week) -->
        <div
          class="grid grid-cols-7 text-sm font-medium text-center py-2 transition-colors duration-300"
          :class="{ 'bg-gray-50': !isDarkMode, 'bg-gray-800': isDarkMode }"
        >
          <div
            v-for="(day, i) in [
              'Sun',
              'Mon',
              'Tue',
              'Wed',
              'Thu',
              'Fri',
              'Sat',
            ]"
            :key="i"
            class="py-2"
          >
            {{ day }}
          </div>
        </div>

        <!-- Month Grid -->
        <div
          class="grid grid-cols-7 border-t transition-colors duration-300"
          :class="{
            'border-gray-200': !isDarkMode,
            'border-gray-700': isDarkMode,
          }"
        >
          <!-- Days -->
          <template
            v-for="(week, weekIndex) in calendarDays"
            :key="`week-${weekIndex}`"
          >
            <div
              v-for="(day, dayIndex) in week"
              :key="`day-${weekIndex}-${dayIndex}`"
              class="day-cell border-b border-r h-auto transition-colors duration-300"
              :class="{
                'border-gray-200': !isDarkMode,
                'border-gray-700': isDarkMode,
                'border-l': dayIndex === 0,
              }"
              @dragover="onDragOver(day, $event)"
              @drop="onDrop(day, $event)"
            >
              <div
                :class="getDayClass(day)"
                class="h-full"
                @click="onDayClick(day)"
              >
                <!-- Date Header -->
                <div class="p-1 text-right">
                  <span
                    class="inline-flex items-center justify-center w-7 h-7 rounded-full text-sm transition-all duration-300"
                    :class="{
                      'bg-indigo-600 text-white':
                        isToday(day) && props.highlightToday,
                      'hover:bg-indigo-100 dark:hover:bg-indigo-800/50':
                        !isToday(day) || !props.highlightToday,
                    }"
                  >
                    {{ formatDate(day, "d") }}
                  </span>
                </div>

                <!-- Day Content with Events -->
                <div :class="getDayContentClasses(day)" class="p-1 space-y-1">
                  <!-- Events -->
                  <div
                    v-for="event in getEventsForDay(day).slice(0, 3)"
                    :key="event.id"
                    draggable="true"
                    class="event-item text-xs p-1 rounded-sm truncate cursor-pointer transition-all duration-300 transform hover:scale-105 hover:shadow-md"
                    :class="[
                      getEventColor(event),
                      getEventStyles(event),
                      {
                        'text-white': !isDarkMode,
                        'opacity-50': event.isCompleted,
                      },
                    ]"
                    @click.stop="onEventClick(event, $event)"
                    @dragstart="onDragStart(event, $event)"
                    @dragend="onDragEnd"
                  >
                    <div class="flex items-center">
                      <span class="mr-1" v-html="getEventIcon(event)"/>
                      <span class="truncate">{{ event.title }}</span>
                    </div>
                  </div>

                  <!-- More events indicator -->
                  <div
                    v-if="getEventsForDay(day).length > 3"
                    class="text-xs font-medium text-indigo-600 dark:text-indigo-400 text-center cursor-pointer py-0.5 hover:underline"
                    @click.stop="
                      onDayClick(day);
                      changeView('day');
                    "
                  >
                    +{{ getEventsForDay(day).length - 3 }} more
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Week View -->
      <div
        v-if="weekViewVisible"
        class="week-view transition-all duration-300"
        :class="[
          {
            'opacity-0 scale-95': viewTransitioning && currentView !== 'week',
            'opacity-100 scale-100':
              !viewTransitioning || currentView === 'week',
          },
          animationDirection === 'zoom-in' && animating
            ? 'animate-zoom-in'
            : '',
          animationDirection === 'zoom-out' && animating
            ? 'animate-zoom-out'
            : '',
        ]"
      >
        <!-- Day headers -->
        <div
          class="flex border-b transition-colors duration-300"
          :class="{
            'border-gray-200': !isDarkMode,
            'border-gray-700': isDarkMode,
          }"
        >
          <div
            class="w-20 text-sm font-medium p-2 sticky left-0 transition-colors duration-300"
            :class="{ 'bg-white': !isDarkMode, 'bg-gray-800': isDarkMode }"
          >
            Time
          </div>
          <div
            v-for="(day, index) in calendarDays[0]"
            :key="index"
            class="flex-1 text-sm p-2 text-center font-medium border-l transition-colors duration-300"
            :class="[
              { 'border-gray-200': !isDarkMode, 'border-gray-700': isDarkMode },
              isToday(day) ? 'bg-indigo-50 dark:bg-indigo-900/30' : '',
            ]"
          >
            <div>{{ formatDate(day, "EEE") }}</div>
            <div
              class="rounded-full w-7 h-7 flex items-center justify-center mx-auto mt-1 transition-colors duration-300"
              :class="{
                'bg-indigo-600 text-white': isToday(day),
                'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700':
                  !isToday(day),
              }"
              @click="onDayClick(day)"
            >
              {{ formatDate(day, "d") }}
            </div>
          </div>
        </div>

        <!-- Time grid -->
        <div class="flex relative">
          <!-- Time labels -->
          <div
            class="w-20 sticky left-0 z-10 transition-colors duration-300"
            :class="{ 'bg-white': !isDarkMode, 'bg-gray-800': isDarkMode }"
          >
            <div
              v-for="hourSlot in hours"
              :key="hourSlot.hour"
              class="h-16 border-b border-r flex items-center justify-center text-xs font-medium transition-colors duration-300"
              :class="{
                'border-gray-200': !isDarkMode,
                'border-gray-700': isDarkMode,
              }"
            >
              {{ hourSlot.label }}
            </div>
          </div>

          <!-- Day columns -->
          <div class="flex-grow grid grid-cols-7 relative">
            <!-- Hour rows -->
            <template v-for="hourSlot in hours" :key="hourSlot.hour">
              <div
                v-for="(day, dayIndex) in calendarDays[0]"
                :key="`${hourSlot.hour}-${dayIndex}`"
                class="h-16 border-b border-r relative transition-colors duration-300"
                :class="[
                  {
                    'border-gray-200': !isDarkMode,
                    'border-gray-700': isDarkMode,
                  },
                  isToday(day) ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '',
                ]"
                @click="onTimeSlotClick(day, hourSlot.hour)"
                @dragover="onDragOver(day, $event)"
                @drop="onDrop(day, $event)"
              />
            </template>

            <!-- Events rendered absolutely positioned -->
            <div
              v-for="event in filteredEvents"
              :key="event.id"
              class="absolute rounded-md p-1 overflow-hidden z-10 transition-all duration-300"
              :class="[
                getEventColor(event),
                {
                  'text-white': !isDarkMode,
                  'opacity-50': event.isCompleted,
                },
              ]"
              :style="getEventPosition(event)"
              draggable="true"
              @click.stop="onEventClick(event, $event)"
              @dragstart="onDragStart(event, $event)"
              @dragend="onDragEnd"
            >
              <div class="flex items-center text-xs font-medium">
                <span class="mr-1" v-html="getEventIcon(event)"/>
                <span>{{ event.title }}</span>
              </div>
              <div class="text-xs opacity-90">
                {{ getEventTimeDisplay(event) }}
              </div>
            </div>

            <!-- Current time indicator -->
            <div
              class="absolute w-full h-0.5 bg-red-500 z-20"
              :style="`top: ${
                new Date().getHours() * 60 + new Date().getMinutes()
              }px;`"
            >
              <div
                class="absolute -left-1 -top-1.5 w-3 h-3 rounded-full bg-red-500"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Day View -->
      <div
        v-if="dayViewVisible"
        class="day-view transition-all duration-300"
        :class="[
          {
            'opacity-0 scale-95': viewTransitioning && currentView !== 'day',
            'opacity-100 scale-100':
              !viewTransitioning || currentView === 'day',
          },
          animationDirection === 'zoom-in' && animating
            ? 'animate-zoom-in'
            : '',
          animationDirection === 'zoom-out' && animating
            ? 'animate-zoom-out'
            : '',
        ]"
      >
        <!-- Day header -->
        <div
          class="p-4 border-b flex items-center justify-between transition-colors duration-300"
          :class="{
            'border-gray-200': !isDarkMode,
            'border-gray-700': isDarkMode,
          }"
        >
          <h3 class="text-lg font-bold">
            {{ formatDate(currentDate.value, "EEEE, MMMM d, yyyy") }}
            <span
              v-if="isToday(currentDate.value)"
              class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100"
            >
              Today
            </span>
          </h3>

          <div class="flex space-x-2">
            <button
              class="p-1 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none"
              :class="{
                'hover:bg-gray-100 text-gray-700': !isDarkMode,
                'hover:bg-gray-700 text-gray-300': isDarkMode,
              }"
              aria-label="Previous day"
              @click="currentDate.value = addDays(currentDate.value, -1)"
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

            <button
              class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-300 transform hover:scale-105 focus:outline-none"
              :class="{
                'text-indigo-700 hover:bg-indigo-50': !isDarkMode,
                'text-indigo-400 hover:bg-indigo-900/30': isDarkMode,
              }"
              @click="returnToToday"
            >
              Today
            </button>

            <button
              class="p-1 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none"
              :class="{
                'hover:bg-gray-100 text-gray-700': !isDarkMode,
                'hover:bg-gray-700 text-gray-300': isDarkMode,
              }"
              aria-label="Next day"
              @click="currentDate.value = addDays(currentDate.value, 1)"
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
        </div>

        <!-- Time grid -->
        <div class="flex relative">
          <!-- Time labels -->
          <div
            class="w-20 sticky left-0 z-10 transition-colors duration-300"
            :class="{ 'bg-white': !isDarkMode, 'bg-gray-800': isDarkMode }"
          >
            <div
              v-for="hourSlot in hours"
              :key="hourSlot.hour"
              class="h-16 border-b border-r flex items-center justify-center text-xs font-medium transition-colors duration-300"
              :class="{
                'border-gray-200': !isDarkMode,
                'border-gray-700': isDarkMode,
              }"
            >
              {{ hourSlot.label }}
            </div>
          </div>

          <!-- Day column -->
          <div class="flex-grow relative">
            <!-- Hour cells -->
            <div
              v-for="hourSlot in hours"
              :key="hourSlot.hour"
              class="h-16 border-b relative transition-colors duration-300"
              :class="{
                'border-gray-200': !isDarkMode,
                'border-gray-700': isDarkMode,
              }"
              @click="onTimeSlotClick(currentDate.value, hourSlot.hour)"
              @dragover="onDragOver(currentDate.value, $event)"
              @drop="onDrop(currentDate.value, $event)"
            />

            <!-- Events rendered absolutely positioned -->
            <div
              v-for="event in filteredEvents"
              :key="event.id"
              class="absolute rounded-md p-2 overflow-hidden z-10 transition-all duration-300 w-4/5 right-0 transform hover:translate-x-1 hover:shadow-lg"
              :class="[
                getEventColor(event),
                {
                  'text-white': !isDarkMode,
                  'opacity-50': event.isCompleted,
                },
              ]"
              :style="getEventPosition(event)"
              draggable="true"
              @click.stop="onEventClick(event, $event)"
              @dragstart="onDragStart(event, $event)"
              @dragend="onDragEnd"
            >
              <div class="flex items-center font-medium">
                <span class="mr-1" v-html="getEventIcon(event)"/>
                <span>{{ event.title }}</span>
              </div>
              <div class="text-xs opacity-90">
                {{ getEventTimeDisplay(event) }}
              </div>
              <div v-if="event.location" class="text-xs mt-1 opacity-80">
                <span class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 mr-1"
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
                </span>
              </div>
            </div>

            <!-- Current time indicator -->
            <div
              class="absolute w-full h-0.5 bg-red-500 z-20"
              :style="`top: ${
                new Date().getHours() * 60 + new Date().getMinutes()
              }px;`"
            >
              <div
                class="absolute -left-1 -top-1.5 w-3 h-3 rounded-full bg-red-500"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
