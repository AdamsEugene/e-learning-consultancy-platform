<script setup lang="ts">
import type { Course, CourseSection } from "~/types/course";
interface Props {
  course: Course | null;
  totalLessons: number;
  formatTotalDuration: string;
  formattedUpdateDate: string;
  continueCourse: () => void;
  startCourse: () => void;
  activeTab: "overview" | "curriculum" | "instructor" | "reviews";
  selectSection: (sectionId: number) => void;
  selectedSection: number | null;
  selectedSectionData: CourseSection | null;
  startLesson: (lessonId: number) => void;
  formatLessonType: (lessonType: string) => string;
}

interface Emits {
  (
    e: "update:activeTab",
    tab: "overview" | "curriculum" | "instructor" | "reviews"
  ): void;
}

defineProps<Props>();
defineEmits<Emits>();
</script>

<template>
  <div
    v-if="activeTab === 'overview'"
    class="grid grid-cols-1 lg:grid-cols-3 gap-8"
  >
    <div class="lg:col-span-2">
      <h2 class="text-2xl font-bold mb-4">About This Course</h2>
      <p class="text-gray-700 mb-6">{{ course?.description }}</p>

      <div class="mb-8">
        <h3 class="text-xl font-bold mb-4">What You'll Learn</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div
            v-for="(objective, idx) in course?.objectives"
            :key="idx"
            class="flex"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <span>{{ objective }}</span>
          </div>
        </div>
      </div>

      <div class="mb-8">
        <h3 class="text-xl font-bold mb-4">Requirements</h3>
        <ul class="space-y-2">
          <li
            v-for="(requirement, idx) in course?.requirements"
            :key="idx"
            class="flex"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400 mr-3 flex-shrink-0 mt-0.5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            <span>{{ requirement }}</span>
          </li>
        </ul>
      </div>

      <div>
        <h3 class="text-xl font-bold mb-4">Course Structure</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
          <div class="bg-indigo-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600 mb-1">
              {{ course?.sections.length }}
            </div>
            <div class="text-gray-600 text-sm">Sections</div>
          </div>
          <div class="bg-indigo-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600 mb-1">
              {{ totalLessons }}
            </div>
            <div class="text-gray-600 text-sm">Lessons</div>
          </div>
          <div class="bg-indigo-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600 mb-1">
              {{ course?.totalDuration }}
            </div>
            <div class="text-gray-600 text-sm">Duration</div>
          </div>
          <div class="bg-indigo-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600 mb-1">Lifetime</div>
            <div class="text-gray-600 text-sm">Access</div>
          </div>
        </div>

        <p class="text-gray-700 mb-4">
          This comprehensive course is structured to take you from the very
          basics to advanced concepts in web development. Starting with
          fundamentals before moving to more complex topics, you'll build
          real-world projects along the way to reinforce your learning.
        </p>

        <button
          class="text-indigo-600 font-medium flex items-center hover:text-indigo-800 transition-colors"
          @click="$emit('update:activeTab', 'curriculum')"
        >
          View detailed curriculum
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 ml-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Course roadmap/journey visualization -->
    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
      <h3 class="text-xl font-bold mb-4">Course Roadmap</h3>

      <!-- Course flow visualization -->
      <div class="mb-6 overflow-hidden relative">
        <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-indigo-200" />

        <div
          v-for="(section, index) in course?.sections"
          :key="section.id"
          class="flex mb-4 relative cursor-pointer group"
          @click="selectSection(section.id)"
        >
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 z-10 shadow-sm transition-all duration-200"
            :class="[
              selectedSection === section.id
                ? 'bg-indigo-600 text-white'
                : 'bg-white text-indigo-600 border-2 border-indigo-200 group-hover:border-indigo-400',
            ]"
          >
            {{ index + 1 }}
          </div>

          <div
            class="ml-4 p-3 rounded-lg transition-all duration-200 flex-grow"
            :class="[
              selectedSection === section.id
                ? 'bg-indigo-50 border border-indigo-200'
                : 'group-hover:bg-gray-100',
            ]"
          >
            <h4 class="font-medium text-gray-900">{{ section.title }}</h4>
            <p class="text-sm text-gray-600">
              {{ section.estimatedCompletionTime }}
            </p>
          </div>
        </div>
      </div>

      <!-- Selected section preview -->
      <div
        v-if="selectedSectionData"
        class="border border-gray-200 rounded-lg p-4 bg-white"
      >
        <h4 class="font-semibold text-lg mb-2">
          {{ selectedSectionData.title }}
        </h4>
        <p class="text-gray-700 text-sm mb-4">
          {{ selectedSectionData.description }}
        </p>

        <div class="mb-3 text-sm text-gray-600">
          {{ selectedSectionData.lessons.length }} lessons •
          {{ selectedSectionData.estimatedCompletionTime }}
        </div>

        <div
          v-if="selectedSectionData.lessons.length > 0"
          class="space-y-2 mb-4"
        >
          <div
            v-for="lesson in selectedSectionData.lessons.slice(0, 3)"
            :key="lesson.id"
            class="flex items-center text-sm"
          >
            <span class="w-5 text-center mr-2">{{
              formatLessonType(lesson.type)
            }}</span>
            <span class="truncate">{{ lesson.title }}</span>
          </div>

          <div
            v-if="selectedSectionData.lessons.length > 3"
            class="text-gray-500 text-sm ml-7"
          >
            + {{ selectedSectionData.lessons.length - 3 }} more lessons
          </div>
        </div>

        <button
          class="w-full bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium py-2 rounded transition-colors text-sm"
          :disabled="!selectedSectionData.lessons.length"
          @click="startLesson(selectedSectionData.lessons[0]?.id)"
        >
          Start This Section
        </button>
      </div>
    </div>
  </div>
</template>
