<!-- components/courses/LessonNavigation.vue -->
<script setup lang="ts">
import type { CourseLesson } from "~/types/courseTemp";

interface Props {
  previousLesson: CourseLesson | null;
  nextLesson: CourseLesson | null;
  isCompleted?: boolean;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  isCompleted: false,
});

const emit = defineEmits(["navigate", "complete"]);

const navigateToPrevious = () => {
  if (props.previousLesson) {
    emit("navigate", props.previousLesson);
  }
};

const navigateToNext = () => {
  if (props.nextLesson) {
    emit("navigate", props.nextLesson);
  }
};

const markComplete = () => {
  emit("complete");
};
</script>

<template>
  <div class="flex justify-between items-center mb-8 gap-4">
    <!-- Previous button -->
    <UiButton
      v-if="previousLesson"
      variant="secondary"
      state="default"
      size="md"
      class="group min-w-[180px]"
      @click="navigateToPrevious"
    >
      <template #prefix>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </template>
      <div class="flex items-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M15 18l-6-6 6-6" />
        </svg>
        <div class="font-medium truncate">
          {{ previousLesson.title }}
        </div>
      </div>
    </UiButton>
    <div v-else class="min-w-[180px]" />

    <!-- Middle complete button -->
    <div class="flex-1 flex justify-center">
      <transition name="fade" mode="out-in">
        <UiButton
          v-if="!isCompleted"
          variant="primary"
          state="default"
          size="md"
          class=""
          @click="markComplete"
        >
          Mark as Complete
        </UiButton>
      </transition>
    </div>

    <!-- Next button -->
    <UiButton
      v-if="nextLesson"
      variant="secondary"
      state="default"
      size="md"
      class="group min-w-[180px]"
      @click="navigateToNext"
    >
      <div class="flex items-center">
        <div class="font-medium truncate">
          {{ nextLesson.title }}
        </div>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 ml-2"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M9 6l6 6-6 6" />
        </svg>
      </div>
    </UiButton>
    <div v-else class="min-w-[180px]" />
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
