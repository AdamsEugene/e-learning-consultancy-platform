<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course | null;
}

defineProps<Props>();
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-gray-50 rounded-xl p-8 border border-gray-200 mb-12">
    <h2 class="text-2xl font-bold mb-6 text-gray-900">Related Courses</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="i in 3"
        :key="i"
        class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200"
      >
        <img
          :src="`/images/courses/c${i}.jpg`"
          alt="Course thumbnail"
          class="w-full h-40 object-cover"
        />
        <div class="p-5">
          <h3 class="font-bold text-lg mb-2 text-gray-900">
            {{
              i === 1
                ? "CSS Mastery: Advanced Styling and Layouts"
                : i === 2
                ? "JavaScript: The Complete Guide"
                : "React Fundamentals for Beginners"
            }}
          </h3>
          <div class="flex items-center text-sm mb-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-yellow-400 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
            <span class="text-gray-500"
              >{{ i === 1 ? "4.7" : i === 2 ? "4.9" : "4.8" }} ({{
                i === 1 ? "1,245" : i === 2 ? "3,821" : "2,367"
              }}
              ratings)</span
            >
          </div>
          <div class="text-sm text-gray-600 mb-4">
            {{ i === 1 ? "8h 45m" : i === 2 ? "21h 30m" : "15h 15m" }} •
            {{ i === 1 ? "Intermediate" : i === 2 ? "All Levels" : "Beginner" }}
          </div>
          <div class="flex justify-between items-center">
            <span class="font-bold text-gray-900"
              >${{ i === 1 ? "49.99" : i === 2 ? "89.99" : "69.99" }}</span
            >
            <a
              href="#"
              class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center"
            >
              View Course
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
