<!-- components/dashboard/CourseProgressCard.vue -->
<script setup lang="ts">
interface Course {
  id: number;
  title: string;
  instructor: string;
  progress: number;
  lastAccessed: string;
  thumbnail: string;
  nextLesson: string;
  totalLessons: number;
  completedLessons: number;
}

interface Props {
  courses: Course[];
  animationDelay?: number;
  isLoaded?: boolean;
}

const props = defineProps<Props>();

// Format date to relative time
const getRelativeTime = (dateString: string): string => {
  const now = new Date();
  const date = new Date(dateString);
  const diffMs = now.getTime() - date.getTime();

  const diffMinutes = Math.floor(diffMs / (1000 * 60));
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffMinutes < 60) {
    return diffMinutes === 1 ? "1 minute ago" : `${diffMinutes} minutes ago`;
  } else if (diffHours < 24) {
    return diffHours === 1 ? "1 hour ago" : `${diffHours} hours ago`;
  } else if (diffDays < 7) {
    return diffDays === 1 ? "Yesterday" : `${diffDays} days ago`;
  } else {
    return date.toLocaleDateString("en-US", { month: "short", day: "numeric" });
  }
};

// Get progress color based on percentage
const getProgressColor = (progress: number): string => {
  if (progress < 30) return "bg-indigo-400";
  if (progress < 70) return "bg-indigo-500";
  return "bg-indigo-600";
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Continue Learning</h3>
        <a
          href="/dashboard/courses"
          class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center transition-colors"
        >
          View All Courses
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </a>
      </div>
    </div>

    <div class="divide-y divide-gray-100">
      <!-- Course item -->
      <div
        v-for="(course, index) in courses"
        :key="course.id"
        class="flex flex-col md:flex-row p-4 md:p-6 transition-all duration-200 hover:bg-gray-50 group"
      >
        <div
          class="flex-shrink-0 md:mr-6 mb-4 md:mb-0 relative rounded-lg overflow-hidden md:w-60 h-32 md:h-auto"
        >
          <!-- Course thumbnail -->
          <img
            :src="course.thumbnail"
            :alt="course.title"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          />

          <!-- Play button overlay -->
          <div
            class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 group-hover:opacity-100"
          >
            <div
              class="bg-indigo-600 bg-opacity-80 rounded-full p-3 transform transition-transform duration-300 group-hover:scale-110 hover:bg-opacity-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-white"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>

          <!-- Last accessed badge -->
          <div
            class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white text-xs py-1 px-2 rounded"
          >
            {{ getRelativeTime(course.lastAccessed) }}
          </div>
        </div>

        <div class="flex-1">
          <!-- Course info -->
          <div class="mb-3">
            <h4 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">
              {{ course.title }}
            </h4>
            <p class="text-gray-600 text-sm">
              Instructor: {{ course.instructor }}
            </p>
          </div>

          <!-- Progress bar -->
          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-600"
                >Progress: {{ course.completedLessons }}/{{
                  course.totalLessons
                }}
                lessons</span
              >
              <span class="font-medium text-indigo-600"
                >{{ course.progress }}%</span
              >
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500 ease-out"
                :class="getProgressColor(course.progress)"
                :style="{ width: `${course.progress}%` }"
              ></div>
            </div>
          </div>

          <!-- Next lesson -->
          <div class="flex justify-between items-end">
            <div>
              <p class="text-xs text-gray-500 mb-1">NEXT LESSON</p>
              <p class="text-gray-700 font-medium">{{ course.nextLesson }}</p>
            </div>

            <a
              :href="`/courses/${course.id}/learn`"
              class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 transform hover:translate-y-px focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
            >
              Continue
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- If no courses -->
    <div v-if="courses.length === 0" class="p-8 text-center">
      <div
        class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-8 w-8 text-indigo-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
          />
        </svg>
      </div>
      <h4 class="text-lg font-bold text-gray-900 mb-2">
        No courses in progress
      </h4>
      <p class="text-gray-600 mb-4">
        Start learning today by enrolling in one of our courses.
      </p>
      <a
        href="/courses"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
      >
        Browse Courses
      </a>
    </div>
  </div>
</template>

<style scoped>
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
