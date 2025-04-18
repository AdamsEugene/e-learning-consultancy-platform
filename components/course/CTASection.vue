<script setup lang="ts">
interface Props {
  startCourse: () => void;
}

defineProps<Props>();
</script>

<template>
  <div
    class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl p-8 text-white text-center"
  >
    <h2 class="text-2xl md:text-3xl font-bold mb-4">
      Ready to start your web development journey?
    </h2>
    <p class="text-lg mb-6 max-w-2xl mx-auto">
      Join thousands of students who have already taken this course and
      transformed their careers.
    </p>
    <button
      class="bg-white text-indigo-700 font-bold py-4 px-10 rounded-lg hover:bg-indigo-50 transition-colors shadow-md inline-flex items-center"
      @click="startCourse"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5 mr-2"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
          clip-rule="evenodd"
        />
      </svg>
      Start Learning Now
    </button>
  </div>
</template>
