<!-- components/dashboard/certificates/CertificatesHeader.vue -->
<script setup lang="ts">
interface Props {
  stats: {
    completed: number;
    inProgress: number;
    featured: number;
    expired: number;
  };
  isLoaded: boolean;
}

defineProps<Props>();
</script>

<template>
  <div
    class="mb-6 transform transition-all duration-500"
    :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
  >
    <h1 class="text-2xl font-bold text-gray-900 mb-2">My Certificates</h1>
    <p class="text-gray-600">
      Showcase your skills and achievements with digital certificates
    </p>

    <!-- Certificate statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 transition-all hover:shadow-md transform hover:-translate-y-1"
        style="animation-delay: 100ms"
      >
        <div class="flex items-center">
          <div class="bg-indigo-100 p-3 rounded-xl">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-indigo-700"
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
            <div class="text-sm font-medium text-gray-500">Completed</div>
            <div class="text-xl font-bold text-gray-900">
              {{ stats.completed }}
            </div>
          </div>
        </div>
      </div>

      <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 transition-all hover:shadow-md transform hover:-translate-y-1"
        style="animation-delay: 200ms"
      >
        <div class="flex items-center">
          <div class="bg-yellow-100 p-3 rounded-xl">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-yellow-700"
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
            <div class="text-sm font-medium text-gray-500">In Progress</div>
            <div class="text-xl font-bold text-gray-900">
              {{ stats.inProgress }}
            </div>
          </div>
        </div>
      </div>

      <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 transition-all hover:shadow-md transform hover:-translate-y-1"
        style="animation-delay: 300ms"
      >
        <div class="flex items-center">
          <div class="bg-purple-100 p-3 rounded-xl">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-purple-700"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-500">Featured</div>
            <div class="text-xl font-bold text-gray-900">
              {{ stats.featured }}
            </div>
          </div>
        </div>
      </div>

      <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 transition-all hover:shadow-md transform hover:-translate-y-1"
        style="animation-delay: 400ms"
      >
        <div class="flex items-center">
          <div class="bg-red-100 p-3 rounded-xl">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-red-700"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-4">
            <div class="text-sm font-medium text-gray-500">Expired</div>
            <div class="text-xl font-bold text-gray-900">
              {{ stats.expired }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation for card hover */
.hover\:-translate-y-1:hover {
  transform: translateY(-0.25rem);
}

/* Staggered animation delays */
[style*="animation-delay"] {
  animation: fadeInUp 0.5s ease-out forwards;
  opacity: 0;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
