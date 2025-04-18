<template>
  <div class="bg-white shadow overflow-hidden sm:rounded-md">
    <ul role="list" class="divide-y divide-gray-200">
      <li v-for="section in course?.sections" :key="section.id">
        <div class="px-4 py-4 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <button
                type="button"
                class="flex-shrink-0 h-5 w-5 text-gray-400 hover:text-gray-500"
                @click="toggleSection(String(section.id))"
              >
                <span class="sr-only">Toggle section</span>
                <svg
                  class="h-5 w-5"
                  :class="{
                    'transform rotate-90': isSectionExpanded(
                      String(section.id)
                    ),
                  }"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <p class="ml-3 text-sm font-medium text-gray-900">
                {{ section.title }}
              </p>
            </div>
            <div class="ml-2 flex-shrink-0 flex">
              <p
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
              >
                {{ section.lessons.length }} lessons
              </p>
            </div>
          </div>
        </div>
        <div
          v-show="isSectionExpanded(String(section.id))"
          class="border-t border-gray-200"
        >
          <ul role="list" class="divide-y divide-gray-200">
            <li
              v-for="lesson in section.lessons"
              :key="lesson.id"
              class="px-4 py-4 sm:px-6 hover:bg-gray-50 cursor-pointer"
              @click="selectLesson(lesson)"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <svg
                      class="h-5 w-5 text-gray-400"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                      aria-hidden="true"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                  <p class="ml-3 text-sm font-medium text-gray-900">
                    {{ lesson.title }}
                  </p>
                </div>
                <div class="ml-2 flex-shrink-0 flex">
                  <p
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': lesson.isCompleted,
                      'bg-gray-100 text-gray-800': !lesson.isCompleted,
                    }"
                  >
                    {{ formatLessonType(lesson.type) }}
                  </p>
                </div>
              </div>
              <div class="mt-2 sm:flex sm:justify-between">
                <div class="sm:flex">
                  <p class="flex items-center text-sm text-gray-500">
                    <svg
                      class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                      aria-hidden="true"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ lesson.duration }}
                  </p>
                </div>
                <div
                  class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0"
                >
                  <svg
                    v-if="lesson.isCompleted"
                    class="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span v-else class="text-gray-400">Not completed</span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import type { Course, CourseLesson } from "~/types/course";

const props = defineProps<{
  course: Course | null;
  expandedSections: string[];
}>();

const emit = defineEmits<{
  (e: "toggle-section", sectionId: string): void;
  (e: "select-lesson", lesson: CourseLesson): void;
}>();

const isSectionExpanded = (sectionId: string) => {
  return props.expandedSections.includes(sectionId);
};

const toggleSection = (sectionId: string) => {
  emit("toggle-section", sectionId);
};

const selectLesson = (lesson: CourseLesson) => {
  emit("select-lesson", lesson);
};

const formatLessonType = (type: string) => {
  return type.charAt(0).toUpperCase() + type.slice(1);
};
</script>
