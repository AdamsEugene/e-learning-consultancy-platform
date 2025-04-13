<!-- components/courses/tabs/CourseTabReviews.vue -->
<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course;
}

defineProps<Props>();
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl font-bold text-gray-900">Student Feedback</h3>

      <div class="flex items-center">
        <CommonSvgStarRating
          :rating="course.rating"
          size="lg"
          show-value
          :review-count="course.reviewCount"
        />
      </div>
    </div>

    <!-- Reviews list -->
    <div class="space-y-6">
      <div
        v-for="review in course.reviews"
        :key="review.id"
        class="border-b border-gray-200 pb-6 last:border-b-0"
      >
        <div class="flex justify-between mb-2">
          <div class="flex items-center">
            <img
              :src="review.user.avatar"
              :alt="review.user.name"
              class="w-10 h-10 rounded-full object-cover mr-3"
            />
            <div>
              <div class="font-medium text-gray-900">
                {{ review.user.name }}
              </div>
              <div class="text-xs text-gray-500">
                {{ review.date }}
              </div>
            </div>
          </div>

          <CommonSvgStarRating :rating="review.rating" />
        </div>

        <p class="text-gray-700 mb-3">
          {{ review.content }}
        </p>

        <div class="flex items-center text-sm text-gray-500">
          <button
            class="flex items-center hover:text-indigo-600 transition-colors"
          >
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
                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
              />
            </svg>
            Helpful ({{ review.helpfulCount }})
          </button>
        </div>
      </div>
    </div>

    <!-- Show more reviews button -->
    <div v-if="course.reviewCount > course.reviews.length" class="text-center">
      <button class="text-indigo-600 hover:text-indigo-800 font-medium">
        Show More Reviews
      </button>
    </div>
  </div>
</template>
