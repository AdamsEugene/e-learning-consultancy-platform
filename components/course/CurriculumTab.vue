<script setup lang="ts">
import type { Course, CourseSection } from "~/types/course";

interface Props {
  course: Course | null;
  totalLessons: number;
  formatTotalDuration: string;
  activeTab: "overview" | "curriculum" | "instructor" | "reviews";
  selectSection: (sectionId: number) => void;
  selectedSection: number | null;
  selectedSectionData: CourseSection | null;
}

interface Emits {
  (
    e: "update:activeTab",
    tab: "overview" | "curriculum" | "instructor" | "reviews"
  ): void;
  (e: "start-lesson", lessonId: number): void;
  (e: "start-course"): void;
}
defineProps<Props>();
defineEmits<Emits>();

const expandedSections = ref<number[]>([1]);

const isSectionExpanded = (sectionId: number) => {
  return expandedSections.value.includes(sectionId);
};

const toggleSection = (sectionId: number) => {
  expandedSections.value = expandedSections.value.includes(sectionId)
    ? expandedSections.value.filter((id) => id !== sectionId)
    : [...expandedSections.value, sectionId];
};
</script>

<template>
  <div v-if="activeTab === 'curriculum'">
    <h2 class="text-2xl font-bold mb-6">Course Curriculum</h2>

    <div class="mb-4">
      <div class="flex justify-between text-sm text-gray-600 mb-2">
        <span
          >{{ course?.sections.length }} sections • {{ totalLessons }} lessons •
          {{ formatTotalDuration }} total length</span
        >

        <button
          class="text-indigo-600 hover:text-indigo-800 transition-colors"
          @click="expandedSections = course?.sections.map((s) => s.id) || []"
        >
          Expand all sections
        </button>
      </div>
    </div>

    <!-- Course sections accordion -->
    <div
      class="border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-200 mb-8"
    >
      <div
        v-for="section in course?.sections"
        :id="`section-${section.id}`"
        :key="section.id"
        class="bg-white"
      >
        <!-- Section header -->
        <button
          class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors text-left"
          @click="toggleSection(section.id)"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 transition-transform"
              :class="
                isSectionExpanded(section.id)
                  ? 'transform rotate-180 text-indigo-600'
                  : 'text-gray-400'
              "
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>

            <div class="mr-3">
              <h3 class="font-semibold text-gray-900">
                {{ section.title }}
              </h3>
            </div>
          </div>

          <div class="text-sm text-gray-600">
            {{ section.lessons.length }} lessons •
            {{ section.estimatedCompletionTime }}
          </div>
        </button>

        <!-- Section content (lessons) -->
        <div v-if="isSectionExpanded(section.id)" class="px-6 pb-4 space-y-2">
          <div class="mb-2 pb-2 border-b border-gray-100 text-sm text-gray-700">
            {{ section.description }}
          </div>

          <div
            v-for="lesson in section.lessons"
            :key="lesson.id"
            class="flex py-3 border-b border-gray-100 last:border-0"
          >
            <div class="mr-3">
              <!-- Lesson type icon -->
              <div
                class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                :class="{
                  'bg-indigo-100 text-indigo-600': lesson.type === 'video',
                  'bg-orange-100 text-orange-600': lesson.type === 'quiz',
                  'bg-green-100 text-green-600': lesson.type === 'assignment',
                  'bg-blue-100 text-blue-600': lesson.type === 'resource',
                }"
              >
                <svg
                  v-if="lesson.type === 'video'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                  />
                </svg>

                <svg
                  v-if="lesson.type === 'quiz'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                  <path
                    fill-rule="evenodd"
                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                    clip-rule="evenodd"
                  />
                </svg>

                <svg
                  v-if="lesson.type === 'assignment'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                  />
                </svg>

                <svg
                  v-if="lesson.type === 'resource'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>

            <div class="flex-grow">
              <div class="flex justify-between mb-1">
                <h4 class="font-medium text-gray-900">
                  {{ lesson.title }}
                </h4>
                <span class="text-sm text-gray-600">{{ lesson.duration }}</span>
              </div>

              <p class="text-sm text-gray-600 mb-2">
                {{ lesson.description }}
              </p>

              <!-- Lesson objectives and requirements -->
              <div
                v-if="
                  lesson.learningObjectives &&
                  lesson.learningObjectives.length > 0
                "
                class="mb-3"
              >
                <div class="flex items-center text-xs text-gray-500 mb-1">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3.5 w-3.5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path
                      fill-rule="evenodd"
                      d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Learning Objectives:
                </div>
                <ul class="text-xs text-gray-600 ml-5 list-disc space-y-1">
                  <li
                    v-for="(objective, idx) in lesson.learningObjectives.slice(
                      0,
                      2
                    )"
                    :key="idx"
                  >
                    {{ objective }}
                  </li>
                  <li
                    v-if="lesson.learningObjectives.length > 2"
                    class="text-gray-500"
                  >
                    + {{ lesson.learningObjectives.length - 2 }} more objectives
                  </li>
                </ul>
              </div>

              <div
                v-if="lesson.requirements && lesson.requirements.length > 0"
                class="text-xs text-gray-500"
              >
                <span class="font-medium">Requirements:</span>
                {{ lesson.requirements.join(", ") }}
              </div>

              <!-- Lesson action button -->
              <button
                class="mt-3 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                :class="[
                  lesson.isCompleted
                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                    : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200',
                ]"
                @click="$emit('start-lesson', lesson.id)"
              >
                <svg
                  v-if="lesson.isCompleted"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3.5 w-3.5 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3.5 w-3.5 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ lesson.isCompleted ? "Review" : "Start" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Start course button -->
    <div class="flex justify-center">
      <button
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 px-10 rounded-lg transition-colors shadow-md flex items-center"
        @click="$emit('start-course')"
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
        Start Learning Now
      </button>
    </div>
  </div>
</template>
