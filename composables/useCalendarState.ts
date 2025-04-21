// composables/useCalendarState.ts
import { useState } from "nuxt/app";
import {
  startOfWeek,
  endOfWeek,
  eachDayOfInterval,
  startOfMonth,
  endOfMonth,
  startOfYear,
  endOfYear,
  addDays,
  isSameDay,
  parseISO,
  isAfter,
  isBefore,
} from "date-fns";

// Types
export interface CalendarEvent {
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

export interface Reminder {
  eventId: string;
  time: string; // ISO string format
  type: "email" | "push" | "both";
  message?: string;
}

export type CalendarViewType = "year" | "month" | "week" | "day" | "agenda";

export interface CalendarState {
  events: CalendarEvent[];
  reminders: Reminder[];
  currentDate: Date;
  currentView: CalendarViewType;
  userType: "student" | "instructor" | "client" | "consultant" | "admin";
  highlightToday: boolean;
  showWeekends: boolean;
  allowCreate: boolean;
  allowEdit: boolean;
}

export const useCalendarState = () => {
  const state = useState<CalendarState>("calendarState", () => ({
    events: [],
    reminders: [],
    currentDate: new Date(),
    currentView: "month",
    userType: "student",
    highlightToday: true,
    showWeekends: true,
    allowCreate: true,
    allowEdit: true,
  }));

  // Set initial state
  const initialize = (initialState: Partial<CalendarState>) => {
    state.value = { ...state.value, ...initialState };
  };

  // Calendar navigation
  const setDate = (date: Date) => {
    state.value.currentDate = date;
  };

  const setView = (view: CalendarViewType) => {
    state.value.currentView = view;
  };

  // CRUD operations for events
  const addEvent = (event: CalendarEvent) => {
    state.value.events = [...state.value.events, event];
  };

  const updateEvent = (updatedEvent: CalendarEvent) => {
    state.value.events = state.value.events.map((event) =>
      event.id === updatedEvent.id ? updatedEvent : event
    );
  };

  const deleteEvent = (eventId: string) => {
    state.value.events = state.value.events.filter(
      (event) => event.id !== eventId
    );
  };

  // Reminder operations
  const addReminder = (eventId: string, reminder: Reminder) => {
    state.value.reminders = [
      ...state.value.reminders,
      { ...reminder, eventId },
    ];
  };

  const removeReminder = (eventId: string) => {
    state.value.reminders = state.value.reminders.filter(
      (reminder) => reminder.eventId !== eventId
    );
  };

  const hasReminder = (eventId: string) => {
    return state.value.reminders.some(
      (reminder) => reminder.eventId === eventId
    );
  };

  const getReminderForEvent = (eventId: string) => {
    return state.value.reminders.find(
      (reminder) => reminder.eventId === eventId
    );
  };

  // Calendar utility functions
  const getCalendarDays = () => {
    let days: Date[][] = [];

    if (state.value.currentView === "month") {
      // First day of the month
      const firstDay = startOfMonth(state.value.currentDate);
      // Last day of the month
      const lastDay = endOfMonth(state.value.currentDate);

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
    } else if (state.value.currentView === "week") {
      // Week view shows 7 days starting from sunday or monday
      const start = startOfWeek(state.value.currentDate);
      const end = endOfWeek(state.value.currentDate);
      days = [eachDayOfInterval({ start, end })];
    } else if (state.value.currentView === "day") {
      // Day view shows just the selected date
      days = [[state.value.currentDate]];
    }

    return days;
  };

  // Get events for a specific day
  const getEventsForDay = (day: Date) => {
    return state.value.events.filter((event) => {
      const startDate = parseISO(event.startTime);
      return isSameDay(startDate, day);
    });
  };

  // Filter events for the current view
  const getFilteredEvents = () => {
    if (!state.value.events.length) return [];

    if (state.value.currentView === "year") {
      // For year view, include all events in the current year
      const yearStart = startOfYear(state.value.currentDate);
      const yearEnd = endOfYear(state.value.currentDate);

      return state.value.events.filter((event) => {
        const startDate = parseISO(event.startTime);
        return isAfter(startDate, yearStart) && isBefore(startDate, yearEnd);
      });
    } else if (state.value.currentView === "month") {
      // For month view, include all events in the displayed month
      const calendarDays = getCalendarDays();
      return state.value.events.filter((event) => {
        const startDate = parseISO(event.startTime);
        const firstDay = calendarDays[0][0];
        const lastDay = calendarDays[calendarDays.length - 1][6];

        return isAfter(startDate, firstDay) && isBefore(startDate, lastDay);
      });
    } else if (state.value.currentView === "week") {
      // For week view, include all events in the current week
      const start = startOfWeek(state.value.currentDate);
      const end = endOfWeek(state.value.currentDate);

      return state.value.events.filter((event) => {
        const startDate = parseISO(event.startTime);
        return isAfter(startDate, start) && isBefore(startDate, end);
      });
    } else if (state.value.currentView === "day") {
      // For day view, include only events on the selected date
      return state.value.events.filter((event) => {
        const startDate = parseISO(event.startTime);
        return isSameDay(startDate, state.value.currentDate);
      });
    } else if (state.value.currentView === "agenda") {
      // For agenda view, include events from today onwards
      const today = new Date();
      return state.value.events
        .filter((event) => {
          const startDate = parseISO(event.startTime);
          return !isBefore(startDate, today);
        })
        .sort((a, b) => {
          return (
            parseISO(a.startTime).getTime() - parseISO(b.startTime).getTime()
          );
        });
    }

    return [];
  };

  return {
    state,
    initialize,
    setDate,
    setView,
    addEvent,
    updateEvent,
    deleteEvent,
    addReminder,
    removeReminder,
    hasReminder,
    getReminderForEvent,
    getCalendarDays,
    getEventsForDay,
    getFilteredEvents,
  };
};
