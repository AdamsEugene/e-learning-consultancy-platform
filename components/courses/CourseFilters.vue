<!-- components/courses/CourseFilters.vue -->
<script setup lang="ts">
import {
  ref,
  computed,
  watch,
  nextTick,
  onMounted,
  onBeforeUnmount,
} from "vue";
import MobileFilterDrawer from "./filters/MobileFilterDrawer.vue";

// Props
interface Props {
  categories?: string[];
  instructors?: string[];
  priceRanges?: { label: string; value: string }[];
  levels?: string[];
  ratings?: { label: string; value: number }[];
  durations?: { label: string; value: string }[];
  showMobileFilter?: boolean;
  initialFilters?: {
    category?: string;
    instructor?: string;
    price?: string;
    level?: string;
    rating?: number;
    duration?: string;
  };
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
  initialFilters: () => ({
    category: "all",
    instructor: "all",
    price: "all",
    level: "All Levels",
    rating: 0,
    duration: "any",
  }),
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
  (e: "mobile-drawer-toggle", isOpen: boolean): void;
}>();

// Filter state
const selectedCategory = ref(props.initialFilters?.category || "all");
const selectedInstructor = ref(props.initialFilters?.instructor || "all");
const selectedPrice = ref(props.initialFilters?.price || "all");
const selectedLevel = ref(props.initialFilters?.level || "All Levels");
const selectedRating = ref(props.initialFilters?.rating || 0);
const selectedDuration = ref(props.initialFilters?.duration || "any");

// Mobile filter drawer
const isMobileFilterOpen = ref(props.showMobileFilter);
// Animation states
const isAnimating = ref(false);

// Count active filters
const activeFiltersCount = computed(() => {
  let count = 0;
  if (selectedCategory.value !== "all") count++;
  if (selectedInstructor.value !== "all") count++;
  if (selectedPrice.value !== "all") count++;
  if (selectedLevel.value !== "All Levels") count++;
  if (selectedRating.value !== 0) count++;
  if (selectedDuration.value !== "any") count++;
  return count;
});

// Computed properties
const hasActiveFilters = computed(() => activeFiltersCount.value > 0);

// Collected filters for passing to child components
const filters = computed(() => ({
  category: selectedCategory.value,
  instructor: selectedInstructor.value,
  price: selectedPrice.value,
  level: selectedLevel.value,
  rating: selectedRating.value,
  duration: selectedDuration.value,
}));

// Watch for mobile filter changes
watch(isMobileFilterOpen, (newValue) => {
  emit("mobile-drawer-toggle", newValue);

  if (newValue) {
    // Prevent body scrolling when filter is open
    document.body.style.overflow = "hidden";
  } else {
    // Re-enable scrolling when filter is closed
    document.body.style.overflow = "";
  }
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

  // Add animation
  isAnimating.value = true;
  setTimeout(() => {
    isAnimating.value = false;
  }, 500);
};

// Toggle mobile filter drawer
const toggleMobileFilter = () => {
  isMobileFilterOpen.value = !isMobileFilterOpen.value;
};

// Methods to handle individual filter changes
const onFilterChange = (type: string, value: string | number) => {
  switch (type) {
    case "category":
      selectedCategory.value = value as string;
      break;
    case "instructor":
      selectedInstructor.value = value as string;
      break;
    case "price":
      selectedPrice.value = value as string;
      break;
    case "level":
      selectedLevel.value = value as string;
      break;
    case "rating":
      selectedRating.value = value as number;
      break;
    case "duration":
      selectedDuration.value = value as string;
      break;
  }
  updateFilters();
};

// Close mobile filters when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const filterDrawer = document.getElementById("mobile-filter-drawer");
  const filterToggle = document.getElementById("mobile-filter-toggle");

  if (
    filterDrawer &&
    !filterDrawer.contains(event.target as Node) &&
    filterToggle &&
    !filterToggle.contains(event.target as Node) &&
    isMobileFilterOpen.value
  ) {
    isMobileFilterOpen.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener("click", handleClickOutside);

  // Initial update
  nextTick(() => {
    updateFilters();
  });
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);

  // Ensure body scrolling is re-enabled
  document.body.style.overflow = "";
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div>
    <!-- Mobile filter toggle button - visible on small screens -->
    <div class="lg:hidden mb-6">
      <button
        id="mobile-filter-toggle"
        class="course-filter-button w-full flex items-center justify-center space-x-2 text-white py-3 px-4 rounded-lg"
        @click="toggleMobileFilter"
      >
        <span class="relative">
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
          <span
            v-if="hasActiveFilters"
            class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 text-xs bg-white text-indigo-600 font-bold rounded-full shadow animate-pulse-slow"
          >
            {{ activeFiltersCount }}
          </span>
        </span>
        <span class="font-medium">Filters</span>
      </button>
    </div>

    <!-- Mobile filter overlay -->
    <div
      v-show="isMobileFilterOpen"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 backdrop-blur-sm"
      @click="toggleMobileFilter"
    />

    <!-- Mobile filter drawer as a component -->
    <MobileFilterDrawer
      v-show="isMobileFilterOpen"
      :is-open="isMobileFilterOpen"
      :categories="categories"
      :instructors="instructors"
      :price-ranges="priceRanges"
      :levels="levels"
      :ratings="ratings"
      :durations="durations"
      :filters="filters"
      :has-active-filters="hasActiveFilters"
      :active-filters-count="activeFiltersCount"
      :is-animating="isAnimating"
      @close="toggleMobileFilter"
      @clear="clearAllFilters"
      @filter-change="onFilterChange"
    />

    <!-- Desktop filters sidebar -->
    <div class="hidden lg:block">
      <div
        class="filter-card bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100"
      >
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-indigo-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                clip-rule="evenodd"
              />
            </svg>
            Filters
            <span
              v-if="hasActiveFilters"
              class="filter-badge ml-2 bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full"
            >
              {{ activeFiltersCount }}
            </span>
          </h3>
          <button
            v-if="hasActiveFilters"
            class="clear-button text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center"
            @click="clearAllFilters"
          >
            <span>Clear All</span>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 ml-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>

        <!-- Active filters chips -->
        <CoursesFiltersFilterActiveChips
          v-if="hasActiveFilters"
          :filters="filters"
          :is-animating="isAnimating"
          @filter-change="onFilterChange"
        />

        <!-- Categories filter section -->
        <CoursesFiltersFilterSection
          title="Categories"
          icon-type="category"
          color="indigo"
          :selected-value="selectedCategory"
          :has-selection="selectedCategory !== 'all'"
          filter-type="category"
          @change="onFilterChange"
        >
          <template #options>
            <label class="filter-option indigo">
              <input
                :checked="selectedCategory === 'all'"
                type="radio"
                name="category"
                value="all"
                @change="onFilterChange('category', 'all')"
              />
              <span>All Categories</span>
            </label>
            <label
              v-for="category in categories"
              :key="category"
              class="filter-option indigo"
            >
              <input
                :checked="selectedCategory === category"
                type="radio"
                name="category"
                :value="category"
                @change="onFilterChange('category', category)"
              />
              <span>{{ category }}</span>
            </label>
          </template>
        </CoursesFiltersFilterSection>

        <!-- Instructors filter section -->
        <CoursesFiltersFilterSection
          v-if="instructors.length > 0"
          title="Instructors"
          icon-type="instructor"
          color="green"
          :selected-value="selectedInstructor"
          :has-selection="selectedInstructor !== 'all'"
          filter-type="instructor"
          :max-height="true"
          @change="onFilterChange"
        >
          <template #options>
            <label class="filter-option green">
              <input
                :checked="selectedInstructor === 'all'"
                type="radio"
                name="instructor"
                value="all"
                @change="onFilterChange('instructor', 'all')"
              />
              <span>All Instructors</span>
            </label>
            <label
              v-for="instructor in instructors"
              :key="instructor"
              class="filter-option green"
            >
              <input
                :checked="selectedInstructor === instructor"
                type="radio"
                name="instructor"
                :value="instructor"
                @change="onFilterChange('instructor', instructor)"
              />
              <span>{{ instructor }}</span>
            </label>
          </template>
        </CoursesFiltersFilterSection>

        <!-- Price filter section -->
        <CoursesFiltersFilterSection
          title="Price"
          icon-type="price"
          color="purple"
          :selected-value="selectedPrice"
          :has-selection="selectedPrice !== 'all'"
          filter-type="price"
          @change="onFilterChange"
        >
          <template #options>
            <label
              v-for="priceRange in priceRanges"
              :key="priceRange.value"
              class="filter-option purple"
            >
              <input
                :checked="selectedPrice === priceRange.value"
                type="radio"
                name="price"
                :value="priceRange.value"
                @change="onFilterChange('price', priceRange.value)"
              />
              <span>{{ priceRange.label }}</span>
            </label>
          </template>
        </CoursesFiltersFilterSection>

        <!-- Level filter section -->
        <CoursesFiltersFilterSection
          title="Level"
          icon-type="level"
          color="blue"
          :selected-value="selectedLevel"
          :has-selection="selectedLevel !== 'All Levels'"
          filter-type="level"
          @change="onFilterChange"
        >
          <template #options>
            <label
              v-for="level in levels"
              :key="level"
              class="filter-option blue"
            >
              <input
                :checked="selectedLevel === level"
                type="radio"
                name="level"
                :value="level"
                @change="onFilterChange('level', level)"
              />
              <span>{{ level }}</span>
            </label>
          </template>
        </CoursesFiltersFilterSection>

        <!-- Rating filter section -->
        <CoursesFiltersFilterSection
          title="Ratings"
          icon-type="rating"
          color="yellow"
          :selected-value="selectedRating.toString()"
          :has-selection="selectedRating !== 0"
          filter-type="rating"
          @change="onFilterChange"
        >
          <template #options>
            <label
              v-for="rating in ratings"
              :key="rating.value"
              class="filter-option yellow"
            >
              <input
                :checked="selectedRating === rating.value"
                type="radio"
                name="rating"
                :value="rating.value"
                @change="onFilterChange('rating', rating.value)"
              />
              <span v-if="rating.value > 0" class="flex items-center">
                <CoursesFiltersStarRating :rating="rating.value" />
                <span class="ml-1">& Up</span>
              </span>
              <span v-else class="text-gray-700">{{ rating.label }}</span>
            </label>
          </template>
        </CoursesFiltersFilterSection>

        <!-- Duration filter section -->
        <CoursesFiltersFilterSection
          title="Duration"
          icon-type="duration"
          color="red"
          :selected-value="selectedDuration"
          :has-selection="selectedDuration !== 'any'"
          filter-type="duration"
          class="mb-0"
          @change="onFilterChange"
        >
          <template #options>
            <label
              v-for="duration in durations"
              :key="duration.value"
              class="filter-option red"
            >
              <input
                :checked="selectedDuration === duration.value"
                type="radio"
                name="duration"
                :value="duration.value"
                @change="onFilterChange('duration', duration.value)"
              />
              <span>{{ duration.label }}</span>
            </label>
          </template>
        </CoursesFiltersFilterSection>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Base styles */
.filter-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  transform-origin: center;
}

.filter-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
  transform: translateY(-2px);
}

.filter-badge {
  transition: all 0.3s;
  animation: pulse-badge 2s infinite;
}

.clear-button {
  transition: all 0.2s;
  border-radius: 0.375rem;
  padding: 0.25rem 0.5rem;
}

.clear-button:hover {
  background-color: rgba(79, 70, 229, 0.1);
  transform: scale(1.05);
}

.course-filter-button {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3),
    0 2px 4px -1px rgba(79, 70, 229, 0.06);
  transition: all 0.3s;
}

.course-filter-button:hover {
  background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
  transform: translateY(-1px);
  box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.4),
    0 3px 6px -1px rgba(79, 70, 229, 0.1);
}

.course-filter-button:active {
  transform: translateY(1px);
  box-shadow: 0 2px 4px -1px rgba(79, 70, 229, 0.2);
}

/* Common filter option styles */
.filter-option {
  display: block;
  cursor: pointer;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  padding-top: 0.375rem;
  padding-bottom: 0.375rem;
  border-radius: 0.5rem;
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
  display: flex;
  align-items: center;
}

.filter-option input {
  margin-right: 0.75rem;
}

.filter-option input:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0
    var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0
    calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow),
    var(--tw-shadow, 0 0 #0000);
  --tw-ring-offset-width: 0px;
}

.filter-option:hover {
  transform: translateX(2px);
}

/* Animation for clearing filters */
@keyframes pulse-badge {
  0% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
  }
  70% {
    box-shadow: 0 0 0 6px rgba(79, 70, 229, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
  }
}

@keyframes pulse-slow {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

.animate-pulse-slow {
  animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Color variants for filter options */
.filter-option.indigo:hover {
  --tw-bg-opacity: 1;
  background-color: rgba(238, 242, 255, var(--tw-bg-opacity));
}

.filter-option.green:hover {
  --tw-bg-opacity: 1;
  background-color: rgba(236, 253, 245, var(--tw-bg-opacity));
}

.filter-option.purple:hover {
  --tw-bg-opacity: 1;
  background-color: rgba(245, 243, 255, var(--tw-bg-opacity));
}

.filter-option.blue:hover {
  --tw-bg-opacity: 1;
  background-color: rgba(239, 246, 255, var(--tw-bg-opacity));
}

.filter-option.yellow:hover {
  --tw-bg-opacity: 1;
  background-color: rgba(254, 252, 232, var(--tw-bg-opacity));
}

.filter-option.red:hover {
  --tw-bg-opacity: 1;
  background-color: rgba(254, 242, 242, var(--tw-bg-opacity));
}

/* Radio inputs by color */
.filter-option.indigo input {
  --tw-text-opacity: 1;
  color: rgba(79, 70, 229, var(--tw-text-opacity));
}
.filter-option.indigo input:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(99, 102, 241, var(--tw-ring-opacity));
}

.filter-option.green input {
  --tw-text-opacity: 1;
  color: rgba(5, 150, 105, var(--tw-text-opacity));
}
.filter-option.green input:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(16, 185, 129, var(--tw-ring-opacity));
}

.filter-option.purple input {
  --tw-text-opacity: 1;
  color: rgba(124, 58, 237, var(--tw-text-opacity));
}
.filter-option.purple input:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(139, 92, 246, var(--tw-ring-opacity));
}

.filter-option.blue input {
  --tw-text-opacity: 1;
  color: rgba(37, 99, 235, var(--tw-text-opacity));
}
.filter-option.blue input:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(59, 130, 246, var(--tw-ring-opacity));
}

.filter-option.yellow input {
  --tw-text-opacity: 1;
  color: rgba(245, 158, 11, var(--tw-text-opacity));
}
.filter-option.yellow input:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(251, 191, 36, var(--tw-ring-opacity));
}

.filter-option.red input {
  --tw-text-opacity: 1;
  color: rgba(239, 68, 68, var(--tw-text-opacity));
}
.filter-option.red input:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(248, 113, 113, var(--tw-ring-opacity));
}
</style>
