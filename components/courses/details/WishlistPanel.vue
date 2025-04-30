<!-- components/courses/details/WishlistPanel.vue -->
<script setup lang="ts">
import type { ExtendedCourse } from "~/composables/useCourseDetails";

interface Props {
  isOpen: boolean;
  wishlist: ExtendedCourse[];
}

defineProps<Props>();
const emit = defineEmits<{
  (e: "update:is-open", value: boolean): void;
  (e: "remove", courseId: number): void;
}>();

const closePanel = () => {
  emit("update:is-open", false);
};

const removeCourse = (courseId: number) => {
  emit("remove", courseId);
};
</script>

<template>
  <UiDrawer
    :model-value="isOpen"
    title="My Wishlist"
    position="right"
    width="400px"
    :z-index="100"
    @update:model-value="emit('update:is-open', $event)"
    class="max-w-md"
  >
    <div class="h-full flex flex-col">
      <!-- Header -->
      <div class="px-4 py-6 bg-white border-b">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">My Wishlist</h2>
          <button class="text-gray-400 hover:text-gray-500" @click="closePanel">
            <span class="sr-only">Close panel</span>
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="wishlist.length === 0" class="p-8 text-center">
          <svg
            class="mx-auto h-12 w-12 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            No courses in wishlist
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            Start adding courses you're interested in!
          </p>
        </div>

        <ul v-else class="divide-y divide-gray-200">
          <li
            v-for="course in wishlist"
            :key="course.id"
            class="p-4 hover:bg-gray-50"
          >
            <div class="flex space-x-4">
              <img
                :src="course.image"
                :alt="course.title"
                class="h-20 w-20 object-cover rounded-lg"
              />
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-900 truncate">
                  {{ course.title }}
                </h4>
                <p class="mt-1 text-sm text-gray-500">
                  {{ course.instructor.name }}
                </p>
                <div class="mt-2 flex items-center justify-between">
                  <span class="text-sm font-medium text-gray-900">
                    ${{ course.price }}
                  </span>
                  <button
                    class="text-sm text-red-600 hover:text-red-800"
                    @click="removeCourse(course.id)"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </UiDrawer>
</template>
