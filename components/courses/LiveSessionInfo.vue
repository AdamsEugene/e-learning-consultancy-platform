<!-- components/courses/LiveSessionInfo.vue -->
<script setup lang="ts">
interface LiveSession {
  id: number;
  title: string;
  description: string;
  startTime: string;
  endTime: string;
  instructorId: number;
  instructorName: string;
  instructorAvatar: string;
  status: "scheduled" | "live" | "ended";
  attendees: number;
  courseId: number;
  lessonId?: number;
}

const props = defineProps<{
  session: LiveSession;
}>();

const emit = defineEmits<{
  (e: "join-session" | "back-to-course"): void;
}>();

// Format session time
const formatSessionTime = (dateString: string): string => {
  return new Date(dateString).toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });
};

// Format session date
const formatSessionDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString([], {
    weekday: "long",
    month: "long",
    day: "numeric",
    year: "numeric",
  });
};

// Check if session is currently live
const isLive = computed(() => props.session.status === "live");

// Check if session has ended
const hasEnded = computed(() => props.session.status === "ended");

// Handle join session button click
const handleJoinSession = () => {
  emit("join-session");
};

// Handle back to course button click
const handleBackToCourse = () => {
  emit("back-to-course");
};
</script>

<template>
  <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
    <div class="md:flex">
      <div class="md:flex-shrink-0">
        <img
          class="h-48 w-full object-cover md:w-48"
          src="/api/placeholder/512/512"
          alt="Live Session"
        />
      </div>
      <div class="p-8">
        <div class="flex items-center">
          <div
            class="uppercase tracking-wide text-sm text-indigo-500 font-semibold"
          >
            Live Session
          </div>
          <div
            v-if="isLive"
            class="ml-2 flex items-center text-green-600 bg-green-100 px-2 py-0.5 rounded-full text-xs font-medium"
          >
            <span
              class="animate-pulse inline-block h-2 w-2 rounded-full bg-green-600 mr-1"
            />
            Live Now
          </div>

          <div
            v-else-if="hasEnded"
            class="ml-2 flex items-center text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full text-xs font-medium"
          >
            Ended
          </div>

          <div
            v-else
            class="ml-2 flex items-center text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full text-xs font-medium"
          >
            Scheduled
          </div>
        </div>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">
          {{ session.title }}
        </h1>
        <p class="mt-2 text-gray-600">{{ session.description }}</p>

        <div class="mt-4 flex items-center">
          <img
            class="h-10 w-10 rounded-full mr-2"
            :src="session.instructorAvatar"
            :alt="session.instructorName"
          />
          <div>
            <p class="text-sm font-medium text-gray-900">
              {{ session.instructorName }}
            </p>
            <p class="text-sm text-gray-500">Instructor</p>
          </div>
        </div>

        <div
          class="mt-4 flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400 mr-1"
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
            <span class="text-gray-600">{{
              formatSessionDate(session.startTime)
            }}</span>
          </div>
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400 mr-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <span class="text-gray-600">
              {{ formatSessionTime(session.startTime) }} -
              {{ formatSessionTime(session.endTime) }}
            </span>
          </div>
        </div>

        <div class="flex items-center mt-6">
          <span class="text-gray-600 mr-2"
            >{{ session.attendees }} people attending</span
          >
          <button
            v-if="isLive"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
            @click="handleJoinSession"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
              />
            </svg>
            Join Live Session
          </button>

          <button
            v-else-if="hasEnded"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
            @click="handleBackToCourse"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Watch Recording
          </button>

          <div
            v-else
            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-medium inline-flex items-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Session Not Started
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
