<!-- components/courses/tabs/CourseTabInstructors.vue -->
<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
    course: Course;
}

defineProps<Props>();
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="space-y-8">
    <div
      v-for="instructor in course.instructors"
      :key="instructor.id"
      class="flex flex-col md:flex-row items-start gap-6"
    >
      <div class="flex-shrink-0">
        <img
          :src="instructor.avatar"
          :alt="instructor.name"
          class="w-24 h-24 rounded-full object-cover"
        />
      </div>

      <div class="flex-grow">
        <div
          class="flex flex-col md:flex-row md:items-center justify-between mb-2"
        >
          <h3 class="text-xl font-bold text-gray-900">
            {{ instructor.name }}
          </h3>
          <NuxtLink
            :to="`/instructors/${instructor.id}`"
            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
          >
            View Profile
          </NuxtLink>
        </div>

        <div class="text-gray-600 font-medium mb-2">
          {{ instructor.role }}
        </div>

        <div class="flex flex-wrap gap-6 mb-4 text-sm text-gray-600">
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-yellow-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
            <span>{{ instructor.rating.toFixed(1) }} Instructor Rating</span>
          </div>

          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-indigo-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <span>{{ instructor.courses }} Courses</span>
          </div>

          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-green-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
            <span>{{ instructor.students.toLocaleString() }} Students</span>
          </div>
        </div>

        <!-- Instructor bio -->
        <p class="text-gray-700">
          {{ instructor.bio }}
        </p>
      </div>
    </div>
  </div>
</template>
