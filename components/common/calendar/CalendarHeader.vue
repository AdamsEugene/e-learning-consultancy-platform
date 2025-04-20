<!-- components/dashboard/calendar/CalendarHeader.vue -->
<script setup lang="ts">
import { ref } from "vue";
import type { CalendarViewType } from "~/composables/useCalendarState";

interface Props {
  title: string;
  currentView: CalendarViewType;
  isLoading: boolean;
  isFullScreen: boolean;
}

defineProps<Props>();

const emit = defineEmits<{
  (e: "change-view", view: CalendarViewType): void;
  (e: "toggle-full-screen" | "today"): void;
  (e: "navigate", direction: "next" | "prev"): void;
}>();

// For mobile view selector
const showCalendarMenu = ref(false);

// Available views
const availableViews: CalendarViewType[] = [
  "year",
  "month",
  "week",
  "day",
  "agenda",
];

// Change view
const changeView = (view: CalendarViewType) => {
  emit("change-view", view);
  showCalendarMenu.value = false;
};

// Navigate dates
const navigate = (direction: "next" | "prev") => {
  emit("navigate", direction);
};

// Go to today
const goToToday = () => {
  emit("today");
};

// Toggle fullscreen
const toggleFullScreen = () => {
  emit("toggle-full-screen");
};
</script>

<template>
  <div
    class="calendar-header border-b p-4 flex flex-col sm:flex-row items-center justify-between bg-white"
  >
    <!-- Date Navigation -->
    <div class="flex items-center mb-4 sm:mb-0">
      <h2
        class="text-xl font-bold mr-4 transition-all transform hover:scale-105 duration-300"
      >
        {{ title }}
      </h2>

      <div class="flex space-x-2">
        <button
          class="p-2 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none hover:bg-gray-100 text-gray-700"
          aria-label="Previous"
          @click="navigate('prev')"
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
          class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-300 transform hover:scale-105 focus:outline-none text-indigo-700 hover:bg-indigo-50"
          @click="goToToday"
        >
          Today
        </button>

        <button
          class="p-2 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none hover:bg-gray-100 text-gray-700"
          aria-label="Next"
          @click="navigate('next')"
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
        class="hidden sm:flex border rounded-lg overflow-hidden border-gray-200"
      >
        <button
          v-for="view in availableViews"
          :key="view"
          class="px-3 py-1.5 text-sm font-medium transition-all duration-300"
          :class="{
            'bg-indigo-600 text-white': currentView === view,
            'hover:bg-gray-100 text-gray-700': currentView !== view,
          }"
          @click="changeView(view)"
        >
          {{ view.charAt(0).toUpperCase() + view.slice(1) }}
        </button>
      </div>

      <!-- Mobile View Selector -->
      <div class="sm:hidden relative">
        <button
          class="flex items-center px-3 py-1.5 border rounded-lg text-sm font-medium transition-colors duration-300 border-gray-200 hover:bg-gray-100"
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
          class="absolute right-0 mt-1 z-10 w-36 rounded-md shadow-lg transition-all duration-300 transform origin-top-right bg-white border border-gray-200"
        >
          <div class="py-1">
            <button
              v-for="view in availableViews"
              :key="view"
              class="block w-full px-4 py-2 text-sm text-left transition-colors duration-300"
              :class="{
                'text-indigo-600 font-medium bg-indigo-50':
                  currentView === view,
                'text-gray-700 hover:bg-gray-100': currentView !== view,
              }"
              @click="changeView(view)"
            >
              {{ view.charAt(0).toUpperCase() + view.slice(1) }}
            </button>
          </div>
        </div>
      </div>

      <!-- Loading Indicator (small) -->
      <div v-if="isLoading" class="ml-2">
        <div
          class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"
        ></div>
      </div>

      <!-- Fullscreen Toggle -->
      <button
        class="p-2 rounded-full transition-all duration-300 transform hover:scale-110 focus:outline-none hover:bg-gray-100 text-gray-700"
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
</template>

<style scoped>
.calendar-header {
  z-index: 10;
}

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
