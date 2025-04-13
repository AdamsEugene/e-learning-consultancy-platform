<!-- components/courses/tabs/CourseTabOverview.vue -->
<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course;
}

defineProps<Props>();
</script>

<template>
  <div class="space-y-8">
    <!-- What you'll learn section -->
    <div>
      <h3 class="text-xl font-bold mb-4 text-gray-900">What you'll learn</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div
          v-for="(item, index) in course.whatYouWillLearn"
          :key="index"
          class="flex items-start"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
          <span class="text-gray-700">{{ item }}</span>
        </div>
      </div>
    </div>

    <!-- Course description -->
    <div>
      <h3 class="text-xl font-bold mb-4 text-gray-900">Course Description</h3>
      <p class="text-gray-700 whitespace-pre-line">
        {{ course.description }}
      </p>
    </div>

    <!-- Requirements -->
    <div>
      <h3 class="text-xl font-bold mb-4 text-gray-900">Requirements</h3>
      <ul class="list-disc pl-6 space-y-2">
        <li
          v-for="(requirement, index) in course.requirements"
          :key="index"
          class="text-gray-700"
        >
          {{ requirement }}
        </li>
      </ul>
    </div>
  </div>
</template>
