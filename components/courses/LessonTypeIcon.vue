<!-- components/courses/LessonTypeIcon.vue -->
<script setup lang="ts">
import type { LessonType } from "~/types/course";

interface Props {
  type: LessonType;
  size?: "sm" | "md" | "lg";
  color?: string;
}

const props = withDefaults(defineProps<Props>(), {
  size: "md",
  color: "text-gray-500",
});

// Size classes
const sizeClasses = {
  sm: "h-4 w-4",
  md: "h-5 w-5",
  lg: "h-6 w-6",
};

const iconSize = sizeClasses[props.size];
</script>

<template>
  <div :class="color">
    <!-- Video icon -->
    <svg
      v-if="type === 'video'"
      xmlns="http://www.w3.org/2000/svg"
      :class="iconSize"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
      />
    </svg>

    <!-- Quiz icon -->
    <svg
      v-else-if="type === 'quiz'"
      xmlns="http://www.w3.org/2000/svg"
      :class="iconSize"
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

    <!-- Assignment icon -->
    <svg
      v-else-if="type === 'assignment'"
      xmlns="http://www.w3.org/2000/svg"
      :class="iconSize"
      viewBox="0 0 20 20"
      fill="currentColor"
    >
      <path
        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
      />
    </svg>

    <!-- Resource icon -->
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      :class="iconSize"
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
</template>
