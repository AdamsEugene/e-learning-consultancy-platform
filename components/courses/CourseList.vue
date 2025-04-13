<!-- components/courses/CourseList.vue -->
<script setup lang="ts">
interface Course {
  id: number;
  title: string;
  category: string;
  image: string;
  instructor: string;
  rating: number;
  reviewCount: number;
  price: number;
  originalPrice?: number;
  level: string;
  duration: string;
  badges?: string[];
  isEnrolled?: boolean;
  progress?: number;
}

interface Props {
  courses: Course[];
  loading?: boolean;
  title?: string;
  subtitle?: string;
  emptyMessage?: string;
  layout?: "grid" | "list";
  showLayoutToggle?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  title: "Courses",
  subtitle: "",
  emptyMessage: "No courses found. Try adjusting your filters.",
  layout: "grid",
  showLayoutToggle: true,
});

// Active layout (grid or list)
const activeLayout = ref(props.layout);

// Skeleton loader count (for loading state)
const skeletonCount = 6;

// Check if the course list is empty
const isEmpty = computed(() => {
  return !props.loading && props.courses.length === 0;
});

// Toggle layout view
const toggleLayout = (layout: "grid" | "list") => {
  activeLayout.value = layout;
};

// Generate random width for skeleton loaders
const getRandomWidth = () => {
  const widths = ["w-1/2", "w-3/5", "w-2/3", "w-3/4", "w-4/5"];
  return widths[Math.floor(Math.random() * widths.length)];
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div>
    <!-- Header section with title, subtitle, and layout toggle -->
    <div
      class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between"
    >
      <div>
        <h2 v-if="title" class="text-2xl font-bold text-gray-900 mb-1">
          {{ title }}
        </h2>
        <p v-if="subtitle" class="text-gray-600">{{ subtitle }}</p>
      </div>

      <!-- Layout toggle buttons -->
      <div v-if="showLayoutToggle" class="flex space-x-2 mt-4 md:mt-0">
        <button
          class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors duration-200"
          :class="
            activeLayout === 'grid'
              ? 'bg-indigo-600 text-white shadow-sm'
              : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
          "
          aria-label="Grid view"
          @click="toggleLayout('grid')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1.5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
            />
          </svg>
          <span>Grid</span>
        </button>
        <button
          class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors duration-200"
          :class="
            activeLayout === 'list'
              ? 'bg-indigo-600 text-white shadow-sm'
              : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
          "
          aria-label="List view"
          @click="toggleLayout('list')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1.5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 10h16M4 14h16M4 18h16"
            />
          </svg>
          <span>List</span>
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-if="isEmpty"
      class="bg-white rounded-lg p-8 text-center border border-gray-200 shadow-sm"
    >
      <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-full w-full"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-900 mb-2">{{ emptyMessage }}</h3>
      <p class="text-gray-600 max-w-sm mx-auto">
        Try changing your search criteria or clearing some filters to see more
        results.
      </p>
    </div>

    <!-- Loading Skeletons -->
    <div
      v-else-if="loading"
      :class="[
        activeLayout === 'grid'
          ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'
          : 'space-y-6',
      ]"
    >
      <div
        v-for="i in skeletonCount"
        :key="`skeleton-${i}`"
        class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm animate-pulse"
      >
        <!-- Skeleton for Card Layout -->
        <template v-if="activeLayout === 'grid'">
          <div class="aspect-video bg-gray-200" />
          <div class="p-5">
            <div class="flex justify-between mb-3">
              <div class="h-5 bg-gray-200 rounded w-1/4" />
              <div class="h-5 bg-gray-200 rounded w-1/4" />
            </div>
            <div class="h-6 bg-gray-300 rounded mb-3" />
            <div
              class="h-6 bg-gray-200 rounded mb-4"
              :class="getRandomWidth()"
            />
            <div class="flex items-center mb-4">
              <div class="flex space-x-1">
                <div
                  v-for="s in 5"
                  :key="`star-${s}`"
                  class="h-4 w-4 bg-gray-200 rounded"
                />
              </div>
              <div class="h-4 bg-gray-200 rounded w-1/4 ml-2" />
            </div>
            <div class="border-t pt-4 flex justify-between">
              <div class="h-8 bg-gray-300 rounded w-1/3" />
              <div class="h-8 bg-gray-200 rounded w-1/3" />
            </div>
          </div>
        </template>

        <!-- Skeleton for List Layout -->
        <template v-else>
          <div class="flex flex-col md:flex-row">
            <div class="md:w-1/3 lg:w-1/4 aspect-video bg-gray-200" />
            <div class="p-5 flex-1">
              <div class="flex flex-wrap gap-2 mb-2">
                <div class="h-5 bg-gray-200 rounded w-1/6" />
                <div class="h-5 bg-gray-200 rounded w-1/4" />
              </div>
              <div class="h-6 bg-gray-300 rounded mb-2" />
              <div
                class="h-6 bg-gray-200 rounded mb-3"
                :class="getRandomWidth()"
              />
              <div class="flex items-center mb-2">
                <div class="flex space-x-1">
                  <div
                    v-for="s in 5"
                    :key="`star-list-${s}`"
                    class="h-4 w-4 bg-gray-200 rounded"
                  />
                </div>
                <div class="h-4 bg-gray-200 rounded w-1/5 ml-2" />
              </div>
              <div class="flex justify-between items-end mt-4 pt-4 border-t">
                <div class="h-8 bg-gray-300 rounded w-1/5" />
                <div class="h-8 bg-gray-200 rounded w-1/6" />
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- Grid Layout View -->
    <div
      v-else-if="activeLayout === 'grid'"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-300"
    >
      <CoursesCourseCard
        v-for="course in courses"
        :key="course.id"
        v-bind="course"
        class="transition-all duration-300 opacity-0 animate-fade-in"
      />
    </div>

    <!-- List Layout View -->
    <div v-else class="space-y-6 transition-opacity duration-300">
      <div
        v-for="course in courses"
        :key="`list-${course.id}`"
        class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:border-indigo-200 transition-all duration-300 transform hover:-translate-y-1 animate-fade-in"
      >
        <div class="flex flex-col md:flex-row">
          <!-- Course image (smaller in list view) -->
          <div class="md:w-1/3 lg:w-1/4 relative overflow-hidden">
            <img
              :src="course.image"
              :alt="course.title"
              class="w-full h-full object-cover aspect-video md:aspect-auto md:h-full"
            />

            <!-- Overlay with badges -->
            <div class="absolute top-0 left-0 p-2 flex flex-wrap gap-1.5">
              <span
                v-for="(badge, index) in course.badges"
                :key="`list-badge-${index}`"
                class="inline-block text-xs font-bold py-0.5 px-2 rounded bg-gradient-to-r text-white shadow-sm"
                :class="[
                  badge === 'Bestseller'
                    ? 'from-yellow-500 to-amber-500'
                    : badge === 'Hot & New'
                    ? 'from-red-500 to-rose-500'
                    : badge === 'New'
                    ? 'from-green-500 to-emerald-500'
                    : badge === 'Top Rated'
                    ? 'from-purple-500 to-violet-500'
                    : 'bg-gray-700',
                ]"
              >
                {{ badge }}
              </span>
            </div>

            <!-- Progress bar for enrolled courses -->
            <div
              v-if="course.isEnrolled && typeof course.progress !== 'undefined'"
              class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-200"
            >
              <div
                class="h-full bg-gradient-to-r from-indigo-500 to-purple-500"
                :style="{ width: `${course.progress}%` }"
              >
                <div class="absolute inset-0 progress-shine" />
              </div>
            </div>
          </div>

          <!-- Course details -->
          <div class="p-5 flex-1">
            <div class="flex flex-wrap gap-2 mb-2">
              <!-- Category and level -->
              <div
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200"
              >
                {{ course.category }}
              </div>

              <div
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                :class="[
                  course.level === 'Beginner'
                    ? 'bg-green-100 text-green-800 border border-green-200'
                    : course.level === 'Intermediate'
                    ? 'bg-blue-100 text-blue-800 border border-blue-200'
                    : course.level === 'Advanced'
                    ? 'bg-purple-100 text-purple-800 border border-purple-200'
                    : 'bg-gray-100 text-gray-800 border border-gray-200',
                ]"
              >
                {{ course.level }}
              </div>
            </div>

            <!-- Course title -->
            <NuxtLink :to="`/courses/${course.id}`">
              <h3
                class="text-lg font-bold mb-2 leading-tight hover:text-indigo-600 transition-colors duration-300"
              >
                {{ course.title }}
              </h3>
            </NuxtLink>

            <!-- Instructor and duration -->
            <div class="flex items-center text-gray-600 text-sm space-x-4 mb-2">
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1 text-gray-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ course.instructor }}
              </div>

              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1 text-gray-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ course.duration }}
              </div>
            </div>

            <!-- Ratings -->
            <div class="flex items-center">
              <div class="flex text-yellow-400 mr-2">
                <svg
                  v-for="i in Math.floor(course.rating)"
                  :key="`list-star-${i}`"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
                <svg
                  v-if="course.rating % 1 >= 0.5"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                >
                  <defs>
                    <linearGradient id="partialFillList">
                      <stop offset="50%" stop-color="currentColor" />
                      <stop offset="50%" stop-color="#D1D5DB" />
                    </linearGradient>
                  </defs>
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    fill="url(#partialFillList)"
                  />
                </svg>
                <svg
                  v-for="i in 5 -
                  Math.floor(course.rating) -
                  (course.rating % 1 >= 0.5 ? 1 : 0)"
                  :key="`list-empty-star-${i}`"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-gray-300"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
              </div>
              <span class="text-gray-600 text-sm">
                <span class="font-semibold text-gray-700">{{
                  course.rating.toFixed(1)
                }}</span>
                <span class="mx-1">•</span>
                <span>{{ course.reviewCount.toLocaleString() }} reviews</span>
              </span>
            </div>

            <!-- Price and CTA button -->
            <div class="flex justify-between items-end mt-4 pt-4 border-t">
              <div>
                <span
                  v-if="
                    course.originalPrice && course.originalPrice > course.price
                  "
                  class="inline-flex items-center bg-green-100 text-green-800 text-xs font-bold px-2 py-0.5 rounded mr-2"
                >
                  {{
                    Math.round(
                      ((course.originalPrice - course.price) /
                        course.originalPrice) *
                        100
                    )
                  }}% OFF
                </span>
                <div class="flex items-end">
                  <span class="text-xl font-bold text-indigo-600">
                    {{
                      new Intl.NumberFormat("en-US", {
                        style: "currency",
                        currency: "USD",
                      }).format(course.price)
                    }}
                  </span>
                  <span
                    v-if="
                      course.originalPrice &&
                      course.originalPrice > course.price
                    "
                    class="text-sm line-through text-gray-400 ml-2"
                  >
                    {{
                      new Intl.NumberFormat("en-US", {
                        style: "currency",
                        currency: "USD",
                      }).format(course.originalPrice)
                    }}
                  </span>
                </div>
              </div>

              <NuxtLink
                :to="`/courses/${course.id}`"
                class="relative inline-flex items-center overflow-hidden px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 group"
                :class="
                  course.isEnrolled
                    ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white'
                    : 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white'
                "
              >
                <!-- Button shine effect -->
                <span
                  class="absolute inset-0 w-full h-full bg-white/30 translate-x-full transition-transform duration-700 ease-in-out group-hover:translate-x-0 group-hover:opacity-30 transform skew-x-[40deg]"
                />

                <!-- Button icon and text -->
                <svg
                  v-if="course.isEnrolled"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                  />
                </svg>

                <span class="relative">{{
                  course.isEnrolled ? "Continue" : "View Course"
                }}</span>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Aspect ratio utility */
.aspect-video {
  aspect-ratio: 16 / 9;
}

/* Progress bar shine animation */
@keyframes progressShine {
  0% {
    transform: translateX(-100%) skewX(-45deg);
  }
  100% {
    transform: translateX(200%) skewX(-45deg);
  }
}

.progress-shine {
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.4) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: skewX(-20deg);
  animation: progressShine 2s infinite;
}

/* Fade in animation for cards */
@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

/* Staggered animation delay for grid items */
.grid > .animate-fade-in:nth-child(1) {
  animation-delay: 0.1s;
}
.grid > .animate-fade-in:nth-child(2) {
  animation-delay: 0.2s;
}
.grid > .animate-fade-in:nth-child(3) {
  animation-delay: 0.3s;
}
.grid > .animate-fade-in:nth-child(4) {
  animation-delay: 0.4s;
}
.grid > .animate-fade-in:nth-child(5) {
  animation-delay: 0.5s;
}
.grid > .animate-fade-in:nth-child(6) {
  animation-delay: 0.6s;
}
.grid > .animate-fade-in:nth-child(n + 7) {
  animation-delay: 0.7s;
}

/* Staggered animation delay for list items */
.space-y-6 > .animate-fade-in:nth-child(1) {
  animation-delay: 0.1s;
}
.space-y-6 > .animate-fade-in:nth-child(2) {
  animation-delay: 0.2s;
}
.space-y-6 > .animate-fade-in:nth-child(3) {
  animation-delay: 0.3s;
}
.space-y-6 > .animate-fade-in:nth-child(4) {
  animation-delay: 0.4s;
}
.space-y-6 > .animate-fade-in:nth-child(5) {
  animation-delay: 0.5s;
}
.space-y-6 > .animate-fade-in:nth-child(n + 6) {
  animation-delay: 0.6s;
}
</style>
