<!-- components/dashboard/DashboardWelcomeCard.vue -->
<script setup lang="ts">
interface Props {
  greeting: string;
  userName: string;
  streak: number;
  enrolledCourses: number;
  hoursLearned: number;
  totalPoints: number;
  animationDelay?: number;
  isLoaded?: boolean;
}

defineProps<Props>();

// Current date
const today = new Date().toLocaleDateString("en-US", {
  weekday: "long",
  year: "numeric",
  month: "long",
  day: "numeric",
});
</script>

<template>
  <div
    class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl shadow-lg overflow-hidden fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <!-- Background decorative elements -->
    <div
      class="absolute top-0 right-0 w-48 h-48 rounded-full bg-indigo-500 opacity-20 transform translate-x-20 -translate-y-20"
    ></div>
    <div
      class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-purple-500 opacity-20 transform -translate-x-32 translate-y-32"
    ></div>

    <div class="px-6 py-6 sm:p-8 relative z-10">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between">
        <!-- Greeting and date -->
        <div class="mb-4 sm:mb-0">
          <div class="flex items-center">
            <h2 class="text-white text-2xl font-bold">
              {{ greeting }}, {{ userName }}!
            </h2>
            <span class="ml-2 text-lg">👋</span>
          </div>
          <p class="text-indigo-100 mt-1">{{ today }}</p>
        </div>

        <!-- Quick action buttons -->
        <div class="flex space-x-3">
          <button
            class="bg-white bg-opacity-20 backdrop-blur-sm hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 flex items-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Resume Learning</span>
          </button>
          <button
            class="bg-white text-indigo-700 px-4 py-2 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white flex items-center font-medium"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
              />
            </svg>
            <span>Explore Courses</span>
          </button>
        </div>
      </div>

      <!-- Stats section -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <!-- Streak -->
        <div
          class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center transform transition-all duration-200 hover:scale-105 hover:bg-opacity-30"
        >
          <div
            class="text-2xl font-bold text-white flex justify-center items-center"
          >
            {{ streak }}
            <span class="ml-2 text-lg">🔥</span>
          </div>
          <div class="text-indigo-100 text-sm mt-1">Day Streak</div>
        </div>

        <!-- Enrolled Courses -->
        <div
          class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center transform transition-all duration-200 hover:scale-105 hover:bg-opacity-30"
        >
          <div class="text-2xl font-bold text-white">
            {{ enrolledCourses }}
          </div>
          <div class="text-indigo-100 text-sm mt-1">Courses Enrolled</div>
        </div>

        <!-- Hours Learned -->
        <div
          class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center transform transition-all duration-200 hover:scale-105 hover:bg-opacity-30"
        >
          <div class="text-2xl font-bold text-white">
            {{ hoursLearned }}
          </div>
          <div class="text-indigo-100 text-sm mt-1">Hours Learned</div>
        </div>

        <!-- Total Points -->
        <div
          class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center transform transition-all duration-200 hover:scale-105 hover:bg-opacity-30"
        >
          <div
            class="text-2xl font-bold text-white flex justify-center items-center"
          >
            {{ totalPoints }}
            <span class="ml-2 text-lg">⭐</span>
          </div>
          <div class="text-indigo-100 text-sm mt-1">Total Points</div>
        </div>
      </div>
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
</style>
