<!-- components/dashboard/CourseCard.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

interface Course {
  id: number;
  title: string;
  instructor: string;
  category: string;
  progress: number;
  lastAccessed: string;
  thumbnail: string;
  nextLesson: string;
  totalLessons: number;
  completedLessons: number;
  certificates?: boolean;
  status?: "active" | "completed" | "archived";
  rating?: number;
  description?: string;
  enrolledDate?: string;
  tags?: string[];
}

// Props
interface Props {
  course: Course;
  animationDelay?: number;
}

const props = withDefaults(defineProps<Props>(), {
  animationDelay: 0,
});

// Emits
const emit = defineEmits(["view", "continue"]);

// State
const isHovered = ref(false);
const isVisible = ref(false);
const showDetails = ref(false);

// Calculate progress color based on progress value
const progressColor = computed(() => {
  if (props.course.progress === 100) {
    return "bg-gradient-to-r from-green-500 to-emerald-500";
  } else if (props.course.progress >= 75) {
    return "bg-gradient-to-r from-indigo-500 to-blue-500";
  } else if (props.course.progress >= 50) {
    return "bg-gradient-to-r from-indigo-500 to-purple-500";
  } else if (props.course.progress >= 25) {
    return "bg-gradient-to-r from-purple-500 to-pink-500";
  } else {
    return "bg-gradient-to-r from-red-500 to-pink-500";
  }
});

// Format time ago utility function
const getTimeAgo = (dateString: string) => {
  const now = new Date();
  const date = new Date(dateString);
  const diffMs = now.getTime() - date.getTime();

  const diffSecs = Math.floor(diffMs / 1000);
  const diffMins = Math.floor(diffSecs / 60);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffDays > 30) {
    return formatDate(dateString);
  } else if (diffDays > 0) {
    return `${diffDays} day${diffDays > 1 ? "s" : ""} ago`;
  } else if (diffHours > 0) {
    return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
  } else if (diffMins > 0) {
    return `${diffMins} minute${diffMins > 1 ? "s" : ""} ago`;
  } else {
    return "Just now";
  }
};

// Format date utility function
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Handle continue learning button click
const handleContinue = () => {
  emit("continue", props.course.id);
};

// Handle view course button click
const handleView = () => {
  emit("view", props.course.id);
};

// Toggle course details visibility
const toggleDetails = () => {
  showDetails.value = !showDetails.value;
};

// Set card to visible after slight delay to enable animation
setTimeout(() => {
  isVisible.value = true;
}, 50 + props.animationDelay);
</script>

<template>
  <div
    class="course-card bg-white rounded-xl overflow-hidden transition-all duration-500 transform"
    :class="[
      isVisible
        ? 'translate-y-0 opacity-100 shadow-sm'
        : 'translate-y-8 opacity-0 shadow-none',
      isHovered ? 'shadow-xl translate-y-[-4px]' : 'shadow-sm hover:shadow-md',
      showDetails ? 'ring-2 ring-indigo-500 ring-opacity-50' : 'ring-0',
    ]"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
    :style="`transition-delay: ${animationDelay}ms;`"
  >
    <!-- Course image with progress overlay -->
    <div class="relative aspect-video overflow-hidden group">
      <!-- Image with hover zoom effect -->
      <img
        :src="course.thumbnail"
        :alt="course.title"
        class="w-full h-full object-cover transition-transform duration-700 ease-in-out"
        :class="isHovered ? 'scale-110' : 'scale-100'"
      />

      <!-- Overlay gradient -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent opacity-70 transition-opacity duration-300"
        :class="isHovered ? 'opacity-80' : 'opacity-70'"
      ></div>

      <!-- Status badge -->
      <div
        class="absolute top-3 left-3 px-3 py-1 text-xs font-bold rounded-full shadow-lg backdrop-blur-sm transition-transform duration-300"
        :class="{
          'bg-indigo-500/90 text-white': course.status === 'active',
          'bg-green-500/90 text-white': course.status === 'completed',
          'bg-gray-500/90 text-white': course.status === 'archived',
          'scale-110': isHovered,
        }"
      >
        {{
          course.status === "active"
            ? "In Progress"
            : course.status === "completed"
            ? "Completed"
            : "Archived"
        }}
      </div>

      <!-- Certificate badge -->
      <div
        v-if="course.certificates"
        class="absolute top-3 right-3 bg-amber-500/90 text-white px-3 py-1 text-xs font-bold rounded-full flex items-center shadow-lg backdrop-blur-sm transition-transform duration-300"
        :class="{ 'scale-110': isHovered }"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3 w-3 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        Certificate
      </div>

      <!-- Progress circular indicator -->
      <div
        class="absolute bottom-3 right-3 transition-transform duration-300"
        :class="{ 'scale-110': isHovered }"
      >
        <div class="relative w-12 h-12">
          <!-- Background circle -->
          <svg class="w-12 h-12" viewBox="0 0 36 36">
            <circle
              cx="18"
              cy="18"
              r="16"
              fill="none"
              stroke="rgba(255, 255, 255, 0.3)"
              stroke-width="3"
            ></circle>

            <!-- Progress circle with animation -->
            <circle
              cx="18"
              cy="18"
              r="16"
              fill="none"
              :stroke="isHovered ? '#6366f1' : 'white'"
              stroke-width="3"
              stroke-dasharray="100"
              :stroke-dashoffset="100 - course.progress"
              transform="rotate(-90 18 18)"
              class="transition-all duration-700"
            ></circle>
          </svg>

          <!-- Percentage text -->
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-xs font-bold text-white"
              >{{ course.progress }}%</span
            >
          </div>
        </div>
      </div>

      <!-- Course title, instructor, category on the image -->
      <div
        class="absolute bottom-3 left-3 right-16 transition-transform duration-300"
      >
        <h3 class="font-bold text-lg text-white line-clamp-2 transition-colors">
          {{ course.title }}
        </h3>

        <!-- Rating -->
        <div class="flex items-center mt-1 mb-1">
          <div class="flex items-center text-amber-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
            <span class="text-xs font-medium text-white ml-1">{{
              course.rating
            }}</span>
          </div>

          <!-- Instructor and category -->
          <div class="flex items-center text-xs text-white/80 ml-3">
            <span>{{ course.instructor }}</span>
            <span class="mx-1">•</span>
            <span>{{ course.category }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Course content - collapsed view -->
    <div v-if="!showDetails" class="p-5 flex flex-col group">
      <!-- Progress bar -->
      <div class="h-1.5 w-full bg-gray-200 rounded-full mb-4 overflow-hidden">
        <div
          class="h-full rounded-full transition-all duration-700 ease-out"
          :class="progressColor"
          :style="{ width: `${course.progress}%` }"
        ></div>
      </div>

      <!-- Progress stats -->
      <div class="flex items-center justify-between mb-3 text-sm">
        <div class="font-medium">
          <span class="text-indigo-600">{{ course.completedLessons }}</span>
          <span class="text-gray-600">/{{ course.totalLessons }} lessons</span>
        </div>
        <div class="text-gray-500 text-xs">
          Last accessed {{ getTimeAgo(course.lastAccessed) }}
        </div>
      </div>

      <!-- Next lesson -->
      <div class="mb-4 group">
        <div
          class="text-xs uppercase tracking-wide text-gray-500 mb-1 group-hover:text-indigo-500 transition-colors"
        >
          NEXT LESSON
        </div>
        <div
          class="text-sm font-medium text-gray-800 truncate group-hover:text-indigo-600 transition-colors"
        >
          {{ course.nextLesson }}
        </div>
      </div>

      <!-- Action buttons -->
      <div class="flex space-x-2 mt-auto">
        <button
          v-if="course.progress < 100"
          @click="handleContinue"
          class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-2.5 px-4 rounded-lg transition-all duration-300 flex items-center justify-center font-medium text-sm shadow-sm hover:shadow-md"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1.5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
              clip-rule="evenodd"
            />
          </svg>
          Continue Learning
        </button>
        <button
          v-else
          @click="handleView"
          class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-2.5 px-4 rounded-lg transition-all duration-300 flex items-center justify-center font-medium text-sm shadow-sm hover:shadow-md"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1.5"
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
          Review Course
        </button>
        <button
          @click="toggleDetails"
          class="flex-none bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 p-2.5 rounded-lg transition-colors shadow-sm hover:shadow hover:border-indigo-300"
          title="View details"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Course content - expanded details view -->
    <div v-else class="p-5 space-y-4 bg-indigo-50/50">
      <div class="flex justify-between">
        <h4 class="font-bold text-sm uppercase tracking-wide text-indigo-600">
          Course Details
        </h4>
        <button
          @click="toggleDetails"
          class="text-gray-500 hover:text-indigo-600 transition-colors"
          title="Close details"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Progress stats with more details -->
      <div class="bg-white p-3 rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <div class="text-sm font-medium text-gray-700">Course Progress</div>
          <div class="text-indigo-600 font-bold">{{ course.progress }}%</div>
        </div>
        <div class="h-2 w-full bg-gray-200 rounded-full mb-3 overflow-hidden">
          <div
            class="h-full rounded-full transition-all duration-700 ease-out"
            :class="progressColor"
            :style="{ width: `${course.progress}%` }"
          ></div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
          <div class="bg-indigo-50 p-2 rounded">
            <div class="text-xs text-gray-500">Completed</div>
            <div class="text-indigo-600 font-bold">
              {{ course.completedLessons }}
            </div>
          </div>
          <div class="bg-indigo-50 p-2 rounded">
            <div class="text-xs text-gray-500">Remaining</div>
            <div class="text-indigo-600 font-bold">
              {{ course.totalLessons - course.completedLessons }}
            </div>
          </div>
          <div class="bg-indigo-50 p-2 rounded">
            <div class="text-xs text-gray-500">Total</div>
            <div class="text-indigo-600 font-bold">
              {{ course.totalLessons }}
            </div>
          </div>
        </div>
      </div>

      <!-- Course description -->
      <div v-if="course.description" class="text-sm text-gray-600">
        {{ course.description }}
      </div>

      <!-- Next lesson details -->
      <div class="bg-white p-3 rounded-lg shadow-sm">
        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
          NEXT LESSON
        </div>
        <div class="text-base font-medium text-indigo-600 mb-2">
          {{ course.nextLesson }}
        </div>
        <button
          v-if="course.progress < 100"
          @click="handleContinue"
          class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center font-medium text-sm shadow-sm hover:shadow-md"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1.5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
              clip-rule="evenodd"
            />
          </svg>
          Continue Learning
        </button>
      </div>

      <!-- Tags cloud -->
      <div class="flex flex-wrap gap-1.5">
        <span
          v-for="tag in course.tags"
          :key="tag"
          class="px-2.5 py-1 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm"
        >
          {{ tag }}
        </span>
      </div>

      <!-- Course metadata -->
      <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
        <div>
          <span class="font-bold">Instructor:</span> {{ course.instructor }}
        </div>
        <div>
          <span class="font-bold">Category:</span> {{ course.category }}
        </div>
        <div>
          <span class="font-bold">Enrolled:</span>
          {{ course.enrolledDate ? formatDate(course.enrolledDate) : "N/A" }}
        </div>
        <div>
          <span class="font-bold">Last accessed:</span>
          {{ getTimeAgo(course.lastAccessed) }}
        </div>
      </div>

      <!-- Action buttons -->
      <div class="flex pt-2">
        <button
          @click="handleView"
          class="w-full bg-white border border-indigo-500 text-indigo-600 hover:bg-indigo-50 py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium text-sm"
        >
          View Full Course
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Line clamp for multiline text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Progress circle animation */
@keyframes progress {
  0% {
    stroke-dasharray: 0 100;
  }
}

circle {
  transition: stroke-dashoffset 0.8s ease-in-out;
}

/* Card hover animation */
.course-card {
  will-change: transform, box-shadow;
}

.course-card:hover {
  z-index: 10;
}

/* Subtle image pulse animation for completed courses */
@keyframes subtle-pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

/* Entrance animation */
@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(1rem);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
