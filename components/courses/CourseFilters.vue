<!-- components/courses/CourseFilters.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

// Props
interface Props {
  categories: string[];
  instructors?: string[];
  priceRanges?: { label: string; value: string }[];
  levels?: string[];
  ratings?: { label: string; value: number }[];
  durations?: { label: string; value: string }[];
  showMobileFilter?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  categories: () => [],
  instructors: () => [],
  priceRanges: () => [
    { label: "All Prices", value: "all" },
    { label: "Free", value: "free" },
    { label: "Paid", value: "paid" },
    { label: "Under $50", value: "under50" },
    { label: "Under $100", value: "under100" },
    { label: "$100 & Above", value: "over100" },
  ],
  levels: () => ["All Levels", "Beginner", "Intermediate", "Advanced"],
  ratings: () => [
    { label: "All Ratings", value: 0 },
    { label: "4.5 & Up", value: 4.5 },
    { label: "4.0 & Up", value: 4.0 },
    { label: "3.5 & Up", value: 3.5 },
    { label: "3.0 & Up", value: 3.0 },
  ],
  durations: () => [
    { label: "Any Duration", value: "any" },
    { label: "Short (0-3 hours)", value: "short" },
    { label: "Medium (3-10 hours)", value: "medium" },
    { label: "Long (10+ hours)", value: "long" },
  ],
  showMobileFilter: false,
});

// Emits
const emit = defineEmits<{
  (
    e: "update:filters",
    filters: {
      category: string;
      instructor: string;
      price: string;
      level: string;
      rating: number;
      duration: string;
    }
  ): void;
  (e: "clear"): void;
}>();

// Filter state
const selectedCategory = ref("all");
const selectedInstructor = ref("all");
const selectedPrice = ref("all");
const selectedLevel = ref("all");
const selectedRating = ref(0);
const selectedDuration = ref("any");

// Mobile filter drawer
const isMobileFilterOpen = ref(props.showMobileFilter);

// Computed properties
const hasActiveFilters = computed(() => {
  return (
    selectedCategory.value !== "all" ||
    selectedInstructor.value !== "all" ||
    selectedPrice.value !== "all" ||
    selectedLevel.value !== "All Levels" ||
    selectedRating.value !== 0 ||
    selectedDuration.value !== "any"
  );
});

// Watch for changes and emit
const updateFilters = () => {
  emit("update:filters", {
    category: selectedCategory.value,
    instructor: selectedInstructor.value,
    price: selectedPrice.value,
    level: selectedLevel.value,
    rating: selectedRating.value,
    duration: selectedDuration.value,
  });
};

// Clear all filters
const clearAllFilters = () => {
  selectedCategory.value = "all";
  selectedInstructor.value = "all";
  selectedPrice.value = "all";
  selectedLevel.value = "All Levels";
  selectedRating.value = 0;
  selectedDuration.value = "any";

  emit("clear");
  updateFilters();
};

// Toggle mobile filter drawer
const toggleMobileFilter = () => {
  isMobileFilterOpen.value = !isMobileFilterOpen.value;
};

// Methods to handle individual filter changes
const setCategory = (category: string) => {
  selectedCategory.value = category;
  updateFilters();
};

const setInstructor = (instructor: string) => {
  selectedInstructor.value = instructor;
  updateFilters();
};

const setPrice = (price: string) => {
  selectedPrice.value = price;
  updateFilters();
};

const setLevel = (level: string) => {
  selectedLevel.value = level;
  updateFilters();
};

const setRating = (rating: number) => {
  selectedRating.value = rating;
  updateFilters();
};

const setDuration = (duration: string) => {
  selectedDuration.value = duration;
  updateFilters();
};
</script>

<template>
  <div>
    <!-- Mobile filter toggle button - visible on small screens -->
    <div class="lg:hidden mb-6">
      <button
        class="w-full flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg transition-colors"
        @click="toggleMobileFilter"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
            clip-rule="evenodd"
          />
        </svg>
        <span>Filters</span>
        <span
          v-if="hasActiveFilters"
          class="ml-1 flex items-center justify-center w-5 h-5 text-xs bg-white text-indigo-600 rounded-full"
        >
          •
        </span>
      </button>
    </div>

    <!-- Filters sidebar - always visible on large screens, conditionally on small screens -->
    <div
      :class="[
        'transition-all duration-300',
        'lg:block',
        isMobileFilterOpen ? 'block' : 'hidden',
      ]"
    >
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-bold text-gray-900">Filters</h3>
          <button
            v-if="hasActiveFilters"
            class="text-sm text-indigo-600 hover:text-indigo-800"
            @click="clearAllFilters"
          >
            Clear All
          </button>
        </div>

        <!-- Categories filter section -->
        <div class="mb-6">
          <h4 class="font-medium text-gray-900 mb-3">Categories</h4>
          <div class="space-y-2">
            <label class="block cursor-pointer">
              <input
                v-model="selectedCategory"
                type="radio"
                name="category"
                value="all"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              All Categories
            </label>
            <label
              v-for="category in categories"
              :key="category"
              class="block cursor-pointer"
            >
              <input
                v-model="selectedCategory"
                type="radio"
                name="category"
                :value="category"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              {{ category }}
            </label>
          </div>
        </div>

        <!-- Instructors filter - only show if instructors provided -->
        <div v-if="instructors.length > 0" class="mb-6">
          <h4 class="font-medium text-gray-900 mb-3">Instructors</h4>
          <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
            <label class="block cursor-pointer">
              <input
                v-model="selectedInstructor"
                type="radio"
                name="instructor"
                value="all"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              All Instructors
            </label>
            <label
              v-for="instructor in instructors"
              :key="instructor"
              class="block cursor-pointer"
            >
              <input
                v-model="selectedInstructor"
                type="radio"
                name="instructor"
                :value="instructor"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              {{ instructor }}
            </label>
          </div>
        </div>

        <!-- Price filter -->
        <div class="mb-6">
          <h4 class="font-medium text-gray-900 mb-3">Price</h4>
          <div class="space-y-2">
            <label
              v-for="priceRange in priceRanges"
              :key="priceRange.value"
              class="block cursor-pointer"
            >
              <input
                v-model="selectedPrice"
                type="radio"
                name="price"
                :value="priceRange.value"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              {{ priceRange.label }}
            </label>
          </div>
        </div>

        <!-- Level filter -->
        <div class="mb-6">
          <h4 class="font-medium text-gray-900 mb-3">Level</h4>
          <div class="space-y-2">
            <label
              v-for="level in levels"
              :key="level"
              class="block cursor-pointer"
            >
              <input
                v-model="selectedLevel"
                type="radio"
                name="level"
                :value="level"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              {{ level }}
            </label>
          </div>
        </div>

        <!-- Rating filter -->
        <div class="mb-6">
          <h4 class="font-medium text-gray-900 mb-3">Ratings</h4>
          <div class="space-y-2">
            <label
              v-for="rating in ratings"
              :key="rating.value"
              class="block cursor-pointer flex items-center"
            >
              <input
                v-model="selectedRating"
                type="radio"
                name="rating"
                :value="rating.value"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              <span v-if="rating.value > 0" class="flex items-center">
                <div class="flex text-yellow-400">
                  <svg
                    v-for="i in Math.floor(rating.value)"
                    :key="i"
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
                    v-if="rating.value % 1 !== 0"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
                <span class="ml-1">& Up</span>
              </span>
              <span v-else>{{ rating.label }}</span>
            </label>
          </div>
        </div>

        <!-- Duration filter -->
        <div>
          <h4 class="font-medium text-gray-900 mb-3">Duration</h4>
          <div class="space-y-2">
            <label
              v-for="duration in durations"
              :key="duration.value"
              class="block cursor-pointer"
            >
              <input
                v-model="selectedDuration"
                type="radio"
                name="duration"
                :value="duration.value"
                class="mr-2 text-indigo-600 focus:ring-indigo-500"
                @change="updateFilters"
              />
              {{ duration.label }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) rgba(229, 231, 235, 0.5);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(229, 231, 235, 0.5);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 4px;
}
</style>
