<!-- components/courses/CourseSearch.vue -->
<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useDebounce } from "@/composables/useDebounce";

interface Category {
  id: string;
  name: string;
}

interface CourseSearchProps {
  categories?: Category[];
  initialQuery?: string;
}

interface EmitOptions {
  (e: "search", query: string): void;
  (e: "filter", filters: CourseFilters): void;
}

interface CourseFilters {
  categories: string[];
  priceRange: [number, number];
  skillLevel: string[];
  rating: number | null;
  duration: string[];
}

const props = withDefaults(defineProps<CourseSearchProps>(), {
  categories: () => [],
  initialQuery: "",
});

const emit = defineEmits<EmitOptions>();

// Search state
const searchQuery = ref(props.initialQuery);
const debouncedSearch = useDebounce(searchQuery, 500);

// Filter state
const isFilterOpen = ref(false);
const filters = ref<CourseFilters>({
  categories: [],
  priceRange: [0, 1000],
  skillLevel: [],
  rating: null,
  duration: [],
});

// Price range slider
const minPrice = ref(0);
const maxPrice = ref(1000);

// Options for filters
const skillLevelOptions = [
  { value: "beginner", label: "Beginner" },
  { value: "intermediate", label: "Intermediate" },
  { value: "advanced", label: "Advanced" },
  { value: "all-levels", label: "All Levels" },
];

const durationOptions = [
  { value: "0-1", label: "Under 1 hour" },
  { value: "1-3", label: "1-3 hours" },
  { value: "3-6", label: "3-6 hours" },
  { value: "6-17", label: "6-17 hours" },
  { value: "17+", label: "17+ hours" },
];

const ratingOptions = [
  { value: 4.5, label: "4.5 & Up" },
  { value: 4.0, label: "4.0 & Up" },
  { value: 3.5, label: "3.5 & Up" },
  { value: 3.0, label: "3.0 & Up" },
];

// Format price for display
const formatPrice = (price: number): string => {
  return `$${price}`;
};

// Computed property for the current price range display
const priceRangeText = computed(() => {
  return `${formatPrice(filters.value.priceRange[0])} - ${formatPrice(
    filters.value.priceRange[1]
  )}`;
});

// Apply filters
const applyFilters = () => {
  filters.value.priceRange = [minPrice.value, maxPrice.value];
  emit("filter", filters.value);
  isFilterOpen.value = false;
};

// Reset filters
const resetFilters = () => {
  filters.value = {
    categories: [],
    priceRange: [0, 1000],
    skillLevel: [],
    rating: null,
    duration: [],
  };
  minPrice.value = 0;
  maxPrice.value = 1000;
};

// Toggle category in filter
const toggleCategory = (categoryId: string) => {
  const index = filters.value.categories.indexOf(categoryId);
  if (index === -1) {
    filters.value.categories.push(categoryId);
  } else {
    filters.value.categories.splice(index, 1);
  }
};

// Toggle skill level in filter
const toggleSkillLevel = (level: string) => {
  const index = filters.value.skillLevel.indexOf(level);
  if (index === -1) {
    filters.value.skillLevel.push(level);
  } else {
    filters.value.skillLevel.splice(index, 1);
  }
};

// Toggle duration in filter
const toggleDuration = (duration: string) => {
  const index = filters.value.duration.indexOf(duration);
  if (index === -1) {
    filters.value.duration.push(duration);
  } else {
    filters.value.duration.splice(index, 1);
  }
};

// Set rating filter
const setRating = (rating: number) => {
  filters.value.rating = filters.value.rating === rating ? null : rating;
};

// Watch for changes to search query
watch(debouncedSearch, (newValue) => {
  emit("search", newValue as string);
});

// Check if any filters are active
// const hasActiveFilters = computed(() => {
//   return (
//     filters.value.categories.length > 0 ||
//     filters.value.skillLevel.length > 0 ||
//     filters.value.rating !== null ||
//     filters.value.duration.length > 0 ||
//     filters.value.priceRange[0] > 0 ||
//     filters.value.priceRange[1] < 1000
//   );
// });

// Count active filters for display
const activeFiltersCount = computed(() => {
  let count = 0;
  count += filters.value.categories.length;
  count += filters.value.skillLevel.length;
  count += filters.value.duration.length;
  if (filters.value.rating !== null) count += 1;
  if (filters.value.priceRange[0] > 0 || filters.value.priceRange[1] < 1000)
    count += 1;

  return count;
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="course-search">
    <!-- Search bar -->
    <div class="relative flex items-center mb-6">
      <div class="relative flex-grow">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search for courses..."
          class="w-full pl-10 pr-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
        />
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        <button
          v-if="searchQuery"
          class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
          @click="searchQuery = ''"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
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

      <!-- Filter button -->
      <div class="ml-4">
        <button
          class="relative px-4 py-3 border rounded-lg flex items-center hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          @click="isFilterOpen = !isFilterOpen"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
            />
          </svg>
          Filters
          <span
            v-if="activeFiltersCount > 0"
            class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-indigo-600 rounded-full ml-2"
          >
            {{ activeFiltersCount }}
          </span>
        </button>
      </div>
    </div>

    <!-- Filter panel (collapsible) -->
    <div
      v-show="isFilterOpen"
      class="mb-8 p-6 border rounded-lg bg-white shadow-md"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Categories filter -->
        <div>
          <h3 class="font-semibold mb-3 text-gray-700">Categories</h3>
          <div class="space-y-2 max-h-60 overflow-y-auto pr-4">
            <div
              v-for="category in props.categories"
              :key="category.id"
              class="flex items-center"
            >
              <input
                :id="`category-${category.id}`"
                type="checkbox"
                :checked="filters.categories.includes(category.id)"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                @change="toggleCategory(category.id)"
              />
              <label
                :for="`category-${category.id}`"
                class="ml-2 text-sm text-gray-700"
              >
                {{ category.name }}
              </label>
            </div>
          </div>
        </div>

        <!-- Skill level filter -->
        <div>
          <h3 class="font-semibold mb-3 text-gray-700">Skill Level</h3>
          <div class="space-y-2">
            <div
              v-for="level in skillLevelOptions"
              :key="level.value"
              class="flex items-center"
            >
              <input
                :id="`level-${level.value}`"
                type="checkbox"
                :checked="filters.skillLevel.includes(level.value)"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                @change="toggleSkillLevel(level.value)"
              />
              <label
                :for="`level-${level.value}`"
                class="ml-2 text-sm text-gray-700"
              >
                {{ level.label }}
              </label>
            </div>
          </div>
        </div>

        <!-- Course duration filter -->
        <div>
          <h3 class="font-semibold mb-3 text-gray-700">Course Duration</h3>
          <div class="space-y-2">
            <div
              v-for="duration in durationOptions"
              :key="duration.value"
              class="flex items-center"
            >
              <input
                :id="`duration-${duration.value}`"
                type="checkbox"
                :checked="filters.duration.includes(duration.value)"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                @change="toggleDuration(duration.value)"
              />
              <label
                :for="`duration-${duration.value}`"
                class="ml-2 text-sm text-gray-700"
              >
                {{ duration.label }}
              </label>
            </div>
          </div>
        </div>

        <!-- Rating filter -->
        <div>
          <h3 class="font-semibold mb-3 text-gray-700">Rating</h3>
          <div class="space-y-2">
            <div
              v-for="rating in ratingOptions"
              :key="rating.value"
              class="flex items-center"
            >
              <input
                :id="`rating-${rating.value}`"
                type="radio"
                :checked="filters.rating === rating.value"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                @change="setRating(rating.value)"
              />
              <label
                :for="`rating-${rating.value}`"
                class="ml-2 text-sm text-gray-700 flex items-center"
              >
                <span class="mr-1">{{ rating.label }}</span>
                <div class="flex text-yellow-400">
                  <svg
                    v-for="i in 5"
                    :key="i"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    :class="{
                      'text-gray-300': i > Math.floor(rating.value),
                    }"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Price range filter -->
      <div class="mt-6">
        <h3 class="font-semibold mb-3 text-gray-700">
          Price Range: {{ priceRangeText }}
        </h3>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="min-price" class="block text-sm text-gray-500"
              >Min Price</label
            >
            <input
              id="min-price"
              v-model.number="minPrice"
              type="number"
              min="0"
              max="1000"
              step="5"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label for="max-price" class="block text-sm text-gray-500"
              >Max Price</label
            >
            <input
              id="max-price"
              v-model.number="maxPrice"
              type="number"
              min="0"
              max="1000"
              step="5"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>
        </div>
      </div>

      <!-- Filter actions -->
      <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
        <button class="text-gray-600 hover:text-gray-800" @click="resetFilters">
          Reset Filters
        </button>
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          @click="applyFilters"
        >
          Apply Filters
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for filter categories */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background-color: rgba(243, 244, 246, 0.5);
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(107, 114, 128, 0.7);
}
</style>
