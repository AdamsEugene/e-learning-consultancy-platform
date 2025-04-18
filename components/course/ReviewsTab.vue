<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course | null;
  activeTab: "overview" | "curriculum" | "instructor" | "reviews";
}

defineProps<Props>();
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div v-if="activeTab === 'reviews'">
    <div class="flex flex-col md:flex-row md:items-start gap-8 mb-12">
      <!-- Reviews summary -->
      <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 w-full md:w-80 flex-shrink-0"
      >
        <div class="text-center mb-6">
          <div class="text-5xl font-bold text-gray-900 mb-2">
            {{ course?.rating }}
          </div>
          <div class="flex justify-center mb-2">
            <div class="flex text-yellow-400">
              <svg
                v-for="i in 5"
                :key="i"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                :class="{
                  'text-gray-300': i > Math.floor(course?.rating || 0),
                }"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </div>
          </div>
          <div class="text-gray-600 text-sm">
            {{ Math.floor(course?.studentsCount || 0 / 100) }}K ratings
          </div>
        </div>

        <!-- Rating distribution -->
        <div class="space-y-2">
          <div
            v-for="rating in [5, 4, 3, 2, 1]"
            :key="rating"
            class="flex items-center"
          >
            <span class="text-sm w-2 text-gray-600 mr-2">{{ rating }}</span>
            <div class="flex text-yellow-400 mr-2">
              <svg
                v-for="i in 5"
                :key="i"
                xmlns="http://www.w3.org/2000/svg"
                class="h-3.5 w-3.5"
                :class="{ 'text-gray-300': i > rating }"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </div>
            <div class="h-2 bg-gray-200 rounded-full flex-grow overflow-hidden">
              <div
                class="h-full bg-yellow-400"
                :style="{
                  width: `${
                    rating === 5
                      ? '78'
                      : rating === 4
                      ? '12'
                      : rating === 3
                      ? '6'
                      : rating === 2
                      ? '3'
                      : '1'
                  }%`,
                }"
              />
            </div>
            <span class="text-sm text-gray-600 ml-2 w-8 text-right">
              {{
                rating === 5
                  ? "78"
                  : rating === 4
                  ? "12"
                  : rating === 3
                  ? "6"
                  : rating === 2
                  ? "3"
                  : "1"
              }}%
            </span>
          </div>
        </div>
      </div>

      <!-- Reviews list -->
      <div class="flex-grow">
        <h2 class="text-2xl font-bold mb-6">Student Reviews</h2>

        <div class="mb-6">
          <div class="relative">
            <input
              type="text"
              placeholder="Search reviews..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg pl-10"
            />
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 absolute left-3 top-3.5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
        </div>

        <!-- Sample reviews -->
        <div class="space-y-6">
          <div
            v-for="i in 3"
            :key="i"
            class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0"
          >
            <div class="flex mb-3">
              <img
                :src="`/images/instructors/i${i}.jpg`"
                alt="User avatar"
                class="w-12 h-12 rounded-full mr-4"
              />
              <div>
                <div class="font-medium">
                  {{
                    i === 1
                      ? "Michael Chen"
                      : i === 2
                      ? "Jessica Williams"
                      : "David Rodriguez"
                  }}
                </div>
                <div class="flex items-center text-sm text-gray-600">
                  <div class="flex text-yellow-400 mr-2">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      :class="{
                        'text-gray-300': star > (i === 2 ? 4 : 5),
                      }"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                  <span>{{
                    i === 1
                      ? "1 month ago"
                      : i === 2
                      ? "2 weeks ago"
                      : "3 days ago"
                  }}</span>
                </div>
              </div>
            </div>

            <div class="text-gray-700">
              <p>
                {{
                  i === 1
                    ? "This course exceeded my expectations. The instructor breaks down complex concepts into easy-to-understand segments, and the projects really helped solidify my understanding. I feel much more confident in my web development skills now."
                    : i === 2
                    ? "Great course overall, though I wish there was a bit more focus on responsive design techniques. The JavaScript sections are incredibly well done and the instructor is very responsive to questions."
                    : "Amazing course! I went from knowing almost nothing about web development to building full-stack applications. The progression from basic HTML/CSS to complex JavaScript and React concepts is very well structured."
                }}
              </p>
            </div>

            <div class="mt-3 text-sm">
              <button class="text-gray-500 hover:text-gray-700 mr-4">
                Helpful ({{ i === 1 ? "24" : i === 2 ? "18" : "7" }})
              </button>
              <button class="text-gray-500 hover:text-gray-700">Report</button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
          <nav class="flex items-center">
            <button
              class="px-2 py-1 border border-gray-300 rounded-l-md bg-white text-gray-500 hover:bg-gray-50"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <button
              class="px-3 py-1 border-t border-b border-gray-300 bg-indigo-100 text-indigo-700"
            >
              1
            </button>
            <button
              class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
            >
              2
            </button>
            <button
              class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
            >
              3
            </button>
            <span
              class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700"
              >...</span
            >
            <button
              class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
            >
              12
            </button>
            <button
              class="px-2 py-1 border border-gray-300 rounded-r-md bg-white text-gray-500 hover:bg-gray-50"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>
