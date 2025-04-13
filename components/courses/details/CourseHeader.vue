<!-- components/courses/CourseHeader.vue -->
<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course;
}

defineProps<Props>();
</script>

<template>
  <div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-12">
    <div class="container mx-auto px-4 md:px-6">
      <div class="max-w-4xl">
        <!-- Breadcrumbs -->
        <div class="flex items-center space-x-2 text-sm mb-4 text-indigo-100">
          <NuxtLink to="/courses" class="hover:text-white transition-colors">
            Courses
          </NuxtLink>
          <span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </span>
          <NuxtLink
            :to="`/courses?category=${course.category}`"
            class="hover:text-white transition-colors"
          >
            {{ course.category }}
          </NuxtLink>
          <span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </span>
          <span class="text-indigo-100">{{ course.title }}</span>
        </div>

        <!-- Course badges -->
        <div class="flex flex-wrap gap-2 mb-3">
          <span
            v-for="badge in course.badges"
            :key="badge"
            class="bg-yellow-500 text-yellow-900 text-xs font-bold py-1 px-2 rounded"
          >
            {{ badge }}
          </span>
        </div>

        <!-- Course title and subtitle -->
        <h1 class="text-3xl md:text-4xl font-bold mb-3">
          {{ course.title }}
        </h1>
        <p class="text-xl text-indigo-100 mb-6">{{ course.subtitle }}</p>

        <!-- Course meta info -->
        <div class="flex flex-wrap gap-6 text-sm mb-8">
          <!-- Ratings -->
          <div class="flex items-center">
            <CommonSvgStarRating
              :rating="course.rating"
              :review-count="course.reviewCount"
              show-value
            />
          </div>

          <!-- Students -->
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
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
            <span>{{ course.enrollmentCount.toLocaleString() }} students</span>
          </div>

          <!-- Last updated -->
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
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
            <span>Last updated {{ course.lastUpdated }}</span>
          </div>

          <!-- Language -->
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
              />
            </svg>
            <span>{{ course.language }}</span>
          </div>
        </div>

        <!-- Instructors preview -->
        <div class="flex items-center space-x-2">
          <span class="mr-1">Created by</span>
          <div class="flex items-center">
            <NuxtLink
              v-for="(instructor, index) in course.instructors"
              :key="instructor.id"
              :to="`/instructors/${instructor.id}`"
              class="text-indigo-300 hover:text-white font-medium transition-colors"
            >
              {{ instructor.name }}
              {{ index < course.instructors.length - 1 ? ", " : "" }}
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
