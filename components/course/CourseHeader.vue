<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course | null;
  totalLessons: number;
  formatTotalDuration: string;
  formattedUpdateDate: string;
  continueCourse: () => void;
  startCourse: () => void;
}

defineProps<Props>();

defineEmits<{
  (e: "continue"): void;
}>();
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    class="bg-gradient-to-r from-indigo-700 to-purple-800 rounded-xl p-6 md:p-10 text-white mb-8 shadow-lg"
  >
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
      <div class="mb-6 md:mb-0 md:mr-6">
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4">
          {{ course?.title }}
        </h1>

        <div class="flex flex-wrap items-center text-sm mb-4">
          <span class="flex items-center mr-4 mb-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
            {{ course?.rating }} rating ({{
              Math.floor((course?.studentsCount || 1) / 100)
            }}K+ reviews)
          </span>

          <span class="flex items-center mr-4 mb-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                clip-rule="evenodd"
              />
            </svg>
            {{ course?.studentsCount.toLocaleString() }} students
          </span>

          <span class="flex items-center mr-4 mb-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd"
              />
            </svg>
            {{ formatTotalDuration }}
          </span>

          <span class="flex items-center mr-4 mb-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                clip-rule="evenodd"
              />
            </svg>
            {{ totalLessons }} lessons
          </span>

          <span class="flex items-center mb-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                clip-rule="evenodd"
              />
            </svg>
            Last updated: {{ formattedUpdateDate }}
          </span>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
          <span
            v-for="tag in course?.tags"
            :key="tag"
            class="bg-white/20 text-xs px-3 py-1 rounded-full backdrop-blur-sm"
          >
            {{ tag }}
          </span>
        </div>

        <div class="flex items-center">
          <img
            :src="course?.instructor.avatar"
            :alt="course?.instructor.name"
            class="w-10 h-10 rounded-full mr-3"
          />
          <div>
            <span class="text-sm opacity-90">Instructor:</span>
            <div class="font-semibold">{{ course?.instructor.name }}</div>
          </div>
        </div>
      </div>

      <div class="flex flex-col max-w-sm">
        <!-- Progress card -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 shadow-md mb-4">
          <h3 class="text-lg font-semibold mb-2">Your Progress</h3>

          <div v-if="course?.progress" class="mb-4">
            <div class="flex justify-between text-sm mb-1">
              <span
                >{{ course.progress.completed }} /
                {{ course.progress.total }} lessons completed</span
              >
              <span>{{ course.progress.percentage }}%</span>
            </div>
            <div class="w-full h-2 bg-white/20 rounded-full overflow-hidden">
              <div
                class="h-full bg-green-400"
                :style="{ width: `${course.progress.percentage}%` }"
              />
            </div>
          </div>

          <div v-if="course?.progress?.lastLesson" class="text-sm mb-4">
            <p class="opacity-90 mb-1">Last lesson:</p>
            <p class="font-medium truncate">
              {{ course.progress.lastLesson.title }}
            </p>
          </div>

          <div class="flex flex-col space-y-2">
            <button
              v-if="course?.progress?.lastLesson"
              class="bg-white text-indigo-700 font-semibold py-3 px-6 rounded-lg transition-colors hover:bg-indigo-50 flex items-center justify-center"
              @click="continueCourse"
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
              Continue Learning
            </button>

            <button
              class="bg-indigo-500 hover:bg-indigo-600 font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
              :class="{ 'mt-2': course?.progress?.lastLesson }"
              @click="startCourse"
            >
              <svg
                v-if="!course?.progress?.lastLesson"
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
              {{
                course?.progress?.lastLesson
                  ? "Start from Beginning"
                  : "Start Learning"
              }}
            </button>
          </div>
        </div>

        <!-- Course level -->
        <div
          class="bg-white/10 backdrop-blur-sm rounded-lg p-4 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 mr-3"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 10V3L4 14h7v7l9-11h-7z"
            />
          </svg>
          <div>
            <span class="text-sm opacity-90">Course Level:</span>
            <div class="font-semibold">{{ course?.level }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
