<!-- components/dashboard/DashboardAssignmentsHeader.vue -->
<script setup lang="ts">
// Props
defineProps<{
  counts: {
    pending: number;
    submitted: number;
    graded: number;
    late: number;
  };
  isLoaded: boolean;
}>();
</script>

<template>
  <div
    class="mb-6 transform transition-all duration-500"
    :class="isLoaded ? 'animate-fade-up' : 'opacity-0'"
  >
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Assignments</h1>
    <div class="text-gray-600">
      Manage all your course assignments and submissions in one place
    </div>

    <!-- Assignment stats cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
      <!-- Pending Assignments Card -->
      <div
        class="relative overflow-hidden bg-white rounded-xl shadow-md border border-gray-200 p-5 transition-all duration-500 hover:shadow-lg transform hover:-translate-y-1 border-l-4 border-yellow-500"
        :class="isLoaded ? 'animate-fade-slide' : 'opacity-0'"
      >
        <div class="flex items-center">
          <div class="bg-yellow-100 p-4 rounded-lg">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-yellow-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-500">Pending</div>
            <div class="text-3xl font-bold text-gray-900 count-animate">
              {{ counts.pending }}
            </div>
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8 text-yellow-50 absolute top-2 right-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>

      <!-- Submitted Assignments Card -->
      <div
        class="relative overflow-hidden bg-white rounded-xl shadow-md border border-gray-200 p-5 transition-all duration-500 hover:shadow-lg transform hover:-translate-y-1 border-l-4 border-blue-500"
        :class="isLoaded ? 'animate-fade-slide' : 'opacity-0'"
        style="animation-delay: 100ms"
      >
        <div class="flex items-center">
          <div class="bg-blue-100 p-4 rounded-lg">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-blue-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"
              />
              <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
            </svg>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-500">Submitted</div>
            <div class="text-3xl font-bold text-gray-900 count-animate">
              {{ counts.submitted }}
            </div>
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8 text-blue-50 absolute top-2 right-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"
            />
            <path d="M9 13h2v5a1 1 0 11-2 0v-5z" />
          </svg>
        </div>
      </div>

      <!-- Graded Assignments Card -->
      <div
        class="relative overflow-hidden bg-white rounded-xl shadow-md border border-gray-200 p-5 transition-all duration-500 hover:shadow-lg transform hover:-translate-y-1 border-l-4 border-green-500"
        :class="isLoaded ? 'animate-fade-slide' : 'opacity-0'"
        style="animation-delay: 200ms"
      >
        <div class="flex items-center">
          <div class="bg-green-100 p-4 rounded-lg">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-green-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-500">Graded</div>
            <div class="text-3xl font-bold text-gray-900 count-animate">
              {{ counts.graded }}
            </div>
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8 text-green-50 absolute top-2 right-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>

      <!-- Late Assignments Card -->
      <div
        class="relative overflow-hidden bg-white rounded-xl shadow-md border border-gray-200 p-5 transition-all duration-500 hover:shadow-lg transform hover:-translate-y-1 border-l-4 border-red-500"
        :class="isLoaded ? 'animate-fade-slide' : 'opacity-0'"
        style="animation-delay: 300ms"
      >
        <div class="flex items-center">
          <div class="bg-red-100 p-4 rounded-lg">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-red-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-500">Late</div>
            <div class="text-3xl font-bold text-gray-900 count-animate">
              {{ counts.late }}
            </div>
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8 text-red-50 absolute top-2 right-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.count-animate {
  animation: countUp 1.5s ease-out forwards;
  opacity: 0;
}

@keyframes countUp {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-up {
  animation: fadeUp 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.animate-fade-slide {
  animation: fadeSlide 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeSlide {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
