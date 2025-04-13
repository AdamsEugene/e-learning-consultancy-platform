<!-- components/courses/tabs/CourseTabCurriculum.vue -->
<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course;
  expandedSections: Set<number>;
  totalLessons: number;
}

defineProps<Props>();
const emit = defineEmits<{
  (e: "toggleSection", sectionId: number): void;
}>();

const toggleSection = (sectionId: number) => {
  emit("toggleSection", sectionId);
};
</script>

<template>
  <div class="space-y-4">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-xl font-bold text-gray-900">Course Content</h3>
      <div class="text-sm text-gray-600">
        {{ course.sections.length }} sections • {{ totalLessons }} lessons •
        {{ course.totalDuration }} total length
      </div>
    </div>

    <!-- Course sections accordion -->
    <div class="space-y-4">
      <div
        v-for="section in course.sections"
        :key="section.id"
        class="border border-gray-200 rounded-lg overflow-hidden"
      >
        <!-- Section header -->
        <div
          class="bg-gray-50 px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors"
          @click="toggleSection(section.id)"
        >
          <div class="flex items-center space-x-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-500 transition-transform duration-200"
              :class="{
                'transform rotate-90': expandedSections.has(section.id),
              }"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            <h4 class="font-medium text-gray-900">
              {{ section.title }}
            </h4>
          </div>
          <div class="text-sm text-gray-600">
            {{ section.lessons.length }} lessons •
            {{ section.totalDuration }}
          </div>
        </div>

        <!-- Section content (expandable) -->
        <div
          v-if="expandedSections.has(section.id)"
          class="divide-y divide-gray-200"
        >
          <div
            v-for="lesson in section.lessons"
            :key="lesson.id"
            class="px-4 py-3 flex justify-between items-center bg-white"
          >
            <div class="flex items-center">
              <!-- Lesson icon based on type -->
              <div class="mr-3">
                <CoursesLessonTypeIcon :type="lesson.type" />
              </div>

              <div>
                <div class="font-medium text-gray-900 flex items-center">
                  {{ lesson.title }}
                  <span
                    v-if="lesson.isPreview"
                    class="ml-2 text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded"
                  >
                    Preview
                  </span>
                </div>
                <div class="text-xs text-gray-500">
                  {{
                    lesson.type.charAt(0).toUpperCase() + lesson.type.slice(1)
                  }}
                </div>
              </div>
            </div>

            <div class="flex items-center">
              <!-- Lesson duration -->
              <span class="text-sm text-gray-600 mr-2">{{
                lesson.duration
              }}</span>

              <!-- Preview or locked icon -->
              <NuxtLink
                v-if="lesson.isPreview || course.isEnrolled"
                :to="`/courses/${course.id}/learn/${lesson.id}`"
                class="text-indigo-600 hover:text-indigo-800"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
              </NuxtLink>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
