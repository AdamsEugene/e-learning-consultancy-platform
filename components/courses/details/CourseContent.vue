<!-- components/courses/CourseContent.vue -->
<script setup lang="ts">
import type { Course } from "~/types/course";

interface Props {
  course: Course;
  activeTab: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: "setTab", tab: string): void;
}>();

// Section expansion state
const expandedSections = ref<Set<number>>(new Set());

// Total lessons count
const totalLessons = computed(() => {
  return props.course.sections.reduce((total, section) => {
    return total + section.lessons.length;
  }, 0);
});

// Toggle section expansion
const toggleSection = (sectionId: number) => {
  if (expandedSections.value.has(sectionId)) {
    expandedSections.value.delete(sectionId);
  } else {
    expandedSections.value.add(sectionId);
  }
};

// Set active tab
const setActiveTab = (tab: string) => {
  emit("setTab", tab);
};
</script>

<template>
  <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <!-- Navigation tabs -->
    <div class="flex border-b mb-6 overflow-x-auto hide-scrollbar">
      <button
        :class="[
          'px-4 py-2 font-medium',
          activeTab === 'overview'
            ? 'border-b-2 border-indigo-600 text-indigo-600'
            : 'text-gray-600 hover:text-indigo-600',
        ]"
        @click="setActiveTab('overview')"
      >
        Overview
      </button>
      <button
        :class="[
          'px-4 py-2 font-medium',
          activeTab === 'curriculum'
            ? 'border-b-2 border-indigo-600 text-indigo-600'
            : 'text-gray-600 hover:text-indigo-600',
        ]"
        @click="setActiveTab('curriculum')"
      >
        Curriculum
      </button>
      <button
        :class="[
          'px-4 py-2 font-medium',
          activeTab === 'instructors'
            ? 'border-b-2 border-indigo-600 text-indigo-600'
            : 'text-gray-600 hover:text-indigo-600',
        ]"
        @click="setActiveTab('instructors')"
      >
        Instructors
      </button>
      <button
        :class="[
          'px-4 py-2 font-medium',
          activeTab === 'reviews'
            ? 'border-b-2 border-indigo-600 text-indigo-600'
            : 'text-gray-600 hover:text-indigo-600',
        ]"
        @click="setActiveTab('reviews')"
      >
        Reviews
      </button>
    </div>

    <!-- Tab content - Overview -->
    <CoursesTabsCourseTabOverview v-if="activeTab === 'overview'" :course="course" />

    <!-- Tab content - Curriculum -->
    <CoursesTabsCourseTabCurriculum
      v-else-if="activeTab === 'curriculum'"
      :course="course"
      :expanded-sections="expandedSections"
      :total-lessons="totalLessons"
      @toggle-section="toggleSection"
    />

    <!-- Tab content - Instructors -->
    <CoursesTabsCourseTabInstructors
      v-else-if="activeTab === 'instructors'"
      :course="course"
    />

    <!-- Tab content - Reviews -->
    <CoursesTabsCourseTabReviews v-else-if="activeTab === 'reviews'" :course="course" />
  </div>
</template>

<style scoped>
.hide-scrollbar {
  scrollbar-width: none; /* Firefox */
}

.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}
</style>
