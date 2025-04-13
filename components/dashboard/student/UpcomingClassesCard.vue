<!-- components/dashboard/UpcomingClassesCard.vue -->
<script setup lang="ts">
interface Class {
  id: number;
  title: string;
  instructor: string;
  date: string;
  duration: number;
  courseId: number;
  meetingUrl: string;
}

interface Props {
  classes: Class[];
  formatDate: (dateString: string) => string;
  animationDelay?: number;
  isLoaded?: boolean;
}

const props = defineProps<Props>();

// Check if a class is happening today
const isToday = (dateString: string): boolean => {
  const today = new Date();
  const classDate = new Date(dateString);
  return (
    today.getDate() === classDate.getDate() &&
    today.getMonth() === classDate.getMonth() &&
    today.getFullYear() === classDate.getFullYear()
  );
};

// Check if a class is happening now (within the class duration)
const isNow = (dateString: string, duration: number): boolean => {
  const now = new Date();
  const classDate = new Date(dateString);
  const classEndDate = new Date(classDate.getTime() + duration * 60000);

  return now >= classDate && now <= classEndDate;
};

// Get time until class starts
const getTimeUntilClass = (dateString: string): string => {
  const now = new Date();
  const classDate = new Date(dateString);
  const diffMs = classDate.getTime() - now.getTime();

  if (diffMs <= 0) return "Starting now";

  const diffMinutes = Math.floor(diffMs / (1000 * 60));
  const diffHours = Math.floor(diffMinutes / 60);
  const remainingMinutes = diffMinutes % 60;

  if (diffHours === 0) {
    return `In ${diffMinutes} minute${diffMinutes === 1 ? "" : "s"}`;
  }

  if (remainingMinutes === 0) {
    return `In ${diffHours} hour${diffHours === 1 ? "" : "s"}`;
  }

  return `In ${diffHours} hour${
    diffHours === 1 ? "" : "s"
  } ${remainingMinutes} minute${remainingMinutes === 1 ? "" : "s"}`;
};

// Format time
const formatTime = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

// Format duration
const formatDuration = (minutes: number): string => {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;

  if (hours === 0) {
    return `${mins} min${mins === 1 ? "" : "s"}`;
  }

  if (mins === 0) {
    return `${hours} hour${hours === 1 ? "" : "s"}`;
  }

  return `${hours} hour${hours === 1 ? "" : "s"} ${mins} min${
    mins === 1 ? "" : "s"
  }`;
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Upcoming Classes</h3>
        <a
          href="/dashboard/classes"
          class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center transition-colors"
        >
          View All
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </a>
      </div>
    </div>

    <div class="divide-y divide-gray-100">
      <!-- Class item -->
      <div
        v-for="(classItem, index) in classes"
        :key="classItem.id"
        class="p-4 md:p-6 transition-all duration-200 hover:bg-gray-50 flex flex-col md:flex-row md:items-center"
      >
        <!-- Date and time column -->
        <div
          class="flex-shrink-0 md:mr-6 mb-4 md:mb-0 flex md:flex-col md:items-center"
        >
          <!-- Today badge -->
          <div
            v-if="isToday(classItem.date)"
            class="bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded mb-1 md:mb-2 md:w-full text-center"
          >
            TODAY
          </div>
          <div
            v-else
            class="bg-gray-100 text-gray-700 text-xs font-bold px-2 py-1 rounded mb-1 md:mb-2 md:w-full text-center"
          >
            {{
              new Date(classItem.date)
                .toLocaleDateString("en-US", { weekday: "short" })
                .toUpperCase()
            }}
          </div>

          <!-- Time -->
          <div class="flex md:flex-col items-center text-center ml-3 md:ml-0">
            <div class="text-xl font-bold text-gray-800">
              {{ formatTime(classItem.date) }}
            </div>
            <div class="text-gray-500 text-sm ml-2 md:ml-0">
              {{ formatDuration(classItem.duration) }}
            </div>
          </div>
        </div>

        <!-- Class info -->
        <div class="flex-1">
          <h4 class="text-lg font-bold text-gray-900 mb-1">
            {{ classItem.title }}
          </h4>
          <p class="text-gray-600 mb-2">
            Instructor: {{ classItem.instructor }}
          </p>

          <!-- Status badge and join button -->
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
          >
            <div
              v-if="isNow(classItem.date, classItem.duration)"
              class="px-3 py-1 bg-green-100 text-green-800 rounded-full inline-flex items-center w-fit mb-2 sm:mb-0"
            >
              <span
                class="w-2 h-2 bg-green-600 rounded-full mr-2 animate-pulse"
              ></span>
              <span class="font-medium text-sm">Live Now</span>
            </div>
            <div v-else class="text-gray-500 text-sm mb-2 sm:mb-0">
              {{ getTimeUntilClass(classItem.date) }}
            </div>

            <div>
              <a
                :href="classItem.meetingUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 inline-flex items-center"
                :class="
                  isNow(classItem.date, classItem.duration)
                    ? 'animate-pulse-subtle'
                    : ''
                "
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                  />
                </svg>
                {{
                  isNow(classItem.date, classItem.duration)
                    ? "Join Now"
                    : "Join Class"
                }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- If no classes -->
    <div v-if="classes.length === 0" class="p-8 text-center">
      <div
        class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-8 w-8 text-indigo-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <h4 class="text-lg font-bold text-gray-900 mb-2">No upcoming classes</h4>
      <p class="text-gray-600 mb-4">
        You don't have any scheduled classes at the moment.
      </p>
      <a
        href="/dashboard/classes/browse"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
      >
        Browse Live Classes
      </a>
    </div>
  </div>
</template>

<style scoped>
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}

@keyframes pulse-subtle {
  0%,
  100% {
    background-color: rgba(79, 70, 229, 1);
  }
  50% {
    background-color: rgba(67, 56, 202, 1);
  }
}

.animate-pulse-subtle {
  animation: pulse-subtle 2s infinite;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
