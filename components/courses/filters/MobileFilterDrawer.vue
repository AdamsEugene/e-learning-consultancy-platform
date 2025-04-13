<!-- components/courses/filters/MobileFilterDrawer.vue -->
<script setup lang="ts">
import { ref, watch } from "vue";

interface Props {
  isOpen: boolean;
  categories: string[];
  instructors?: string[];
  priceRanges?: { label: string; value: string }[];
  levels?: string[];
  ratings?: { label: string; value: number }[];
  durations?: { label: string; value: string }[];
  filters: {
    category: string;
    instructor: string;
    price: string;
    level: string;
    rating: number;
    duration: string;
  };
  hasActiveFilters: boolean;
  activeFiltersCount: number;
  isAnimating: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "close" | "clear"): void;
  (e: "filter-change", type: string, value: string | number): void;
}>();

// Active accordion section
const activeFilterSection = ref<string | null>(null);

// Toggle filter section (for mobile accordion)
const toggleFilterSection = (section: string) => {
  if (activeFilterSection.value === section) {
    activeFilterSection.value = null;
  } else {
    activeFilterSection.value = section;
  }
};

// Handle filter changes
const onFilterChange = (type: string, value: string | number) => {
  emit("filter-change", type, value);
};

// Clear all filters
const clearAllFilters = () => {
  emit("clear");
};

// Close drawer
const closeDrawer = () => {
  emit("close");
};

// Apply animation to entry
const drawerAnimation = ref(false);

watch(
  () => props.isOpen,
  (newValue) => {
    if (newValue) {
      // Trigger animation when drawer opens
      setTimeout(() => {
        drawerAnimation.value = true;
      }, 50);
    } else {
      drawerAnimation.value = false;
    }
  },
  { immediate: true }
);
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    id="mobile-filter-drawer"
    class="lg:hidden fixed inset-y-0 right-0 z-50 w-full max-w-xs bg-white shadow-xl overflow-y-auto transition-all duration-500 transform"
    :class="[
      isOpen ? 'translate-x-0' : 'translate-x-full',
      drawerAnimation ? 'filter-drawer-active' : '',
    ]"
  >
    <div
      class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center shadow-sm"
    >
      <h3
        class="text-lg font-bold text-gray-900 flex items-center filter-drawer-title"
      >
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
          class="ml-2 bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full filter-badge-count"
        >
          {{ activeFiltersCount }}
        </span>
      </h3>
      <button
        class="p-2 rounded-full hover:bg-gray-100 transition-colors close-btn"
        aria-label="Close filters"
        @click="closeDrawer"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-gray-500"
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

    <div class="p-4">
      <!-- Active filters pills -->
      <div v-if="hasActiveFilters" class="mb-4">
        <CoursesFiltersFilterActiveChips
          :filters="filters"
          :is-animating="isAnimating"
          @filter-change="onFilterChange"
        />
      </div>

      <!-- Mobile accordion sections -->
      <!-- Categories -->
      <div
        class="mb-3 border border-gray-200 rounded-lg overflow-hidden filter-accordion"
      >
        <button
          class="w-full px-4 py-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors"
          @click="toggleFilterSection('category')"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2 text-indigo-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
              />
            </svg>
            <span class="font-medium text-gray-800">Categories</span>
            <span
              v-if="filters.category !== 'all'"
              class="ml-2 bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full filter-badge"
              >1</span
            >
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 transition-transform duration-300"
            :class="{
              'transform rotate-180': activeFilterSection === 'category',
            }"
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
        <div
          class="px-4 py-3 space-y-2 border-t border-gray-200 bg-white transition-all duration-300 overflow-hidden filter-options"
          :class="{
            'filter-options-open': activeFilterSection === 'category',
            'filter-options-closed': activeFilterSection !== 'category',
          }"
        >
          <label class="filter-option indigo">
            <input
              :checked="filters.category === 'all'"
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
              :checked="filters.category === category"
              type="radio"
              name="category"
              :value="category"
              @change="onFilterChange('category', category)"
            />
            <span>{{ category }}</span>
          </label>
        </div>
      </div>

      <!-- Instructors -->
      <div
        v-if="instructors && instructors.length > 0"
        class="mb-3 border border-gray-200 rounded-lg overflow-hidden filter-accordion"
      >
        <button
          class="w-full px-4 py-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors"
          @click="toggleFilterSection('instructor')"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2 text-green-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
              />
            </svg>
            <span class="font-medium text-gray-800">Instructors</span>
            <span
              v-if="filters.instructor !== 'all'"
              class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full filter-badge"
              >1</span
            >
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 transition-transform duration-300"
            :class="{
              'transform rotate-180': activeFilterSection === 'instructor',
            }"
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
        <div
          class="px-4 py-3 space-y-2 border-t border-gray-200 bg-white transition-all duration-300 overflow-hidden max-h-60 overflow-y-auto custom-scrollbar filter-options"
          :class="{
            'filter-options-open': activeFilterSection === 'instructor',
            'filter-options-closed': activeFilterSection !== 'instructor',
          }"
        >
          <label class="filter-option green">
            <input
              :checked="filters.instructor === 'all'"
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
              :checked="filters.instructor === instructor"
              type="radio"
              name="instructor"
              :value="instructor"
              @change="onFilterChange('instructor', instructor)"
            />
            <span>{{ instructor }}</span>
          </label>
        </div>
      </div>

      <!-- Price -->
      <div
        class="mb-3 border border-gray-200 rounded-lg overflow-hidden filter-accordion"
      >
        <button
          class="w-full px-4 py-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors"
          @click="toggleFilterSection('price')"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2 text-purple-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
              />
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                clip-rule="evenodd"
              />
            </svg>
            <span class="font-medium text-gray-800">Price</span>
            <span
              v-if="filters.price !== 'all'"
              class="ml-2 bg-purple-100 text-purple-800 text-xs font-medium px-2 py-0.5 rounded-full filter-badge"
              >1</span
            >
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 transition-transform duration-300"
            :class="{ 'transform rotate-180': activeFilterSection === 'price' }"
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
        <div
          class="px-4 py-3 space-y-2 border-t border-gray-200 bg-white transition-all duration-300 overflow-hidden filter-options"
          :class="{
            'filter-options-open': activeFilterSection === 'price',
            'filter-options-closed': activeFilterSection !== 'price',
          }"
        >
          <label
            v-for="priceRange in priceRanges"
            :key="priceRange.value"
            class="filter-option purple"
          >
            <input
              :checked="filters.price === priceRange.value"
              type="radio"
              name="price"
              :value="priceRange.value"
              @change="onFilterChange('price', priceRange.value)"
            />
            <span>{{ priceRange.label }}</span>
          </label>
        </div>
      </div>

      <!-- Level -->
      <div
        class="mb-3 border border-gray-200 rounded-lg overflow-hidden filter-accordion"
      >
        <button
          class="w-full px-4 py-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors"
          @click="toggleFilterSection('level')"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2 text-blue-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"
              />
            </svg>
            <span class="font-medium text-gray-800">Level</span>
            <span
              v-if="filters.level !== 'All Levels'"
              class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full filter-badge"
              >1</span
            >
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 transition-transform duration-300"
            :class="{ 'transform rotate-180': activeFilterSection === 'level' }"
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
        <div
          class="px-4 py-3 space-y-2 border-t border-gray-200 bg-white transition-all duration-300 overflow-hidden filter-options"
          :class="{
            'filter-options-open': activeFilterSection === 'level',
            'filter-options-closed': activeFilterSection !== 'level',
          }"
        >
          <label
            v-for="level in levels"
            :key="level"
            class="filter-option blue"
          >
            <input
              :checked="filters.level === level"
              type="radio"
              name="level"
              :value="level"
              @change="onFilterChange('level', level)"
            />
            <span>{{ level }}</span>
          </label>
        </div>
      </div>

      <!-- Rating -->
      <div
        class="mb-3 border border-gray-200 rounded-lg overflow-hidden filter-accordion"
      >
        <button
          class="w-full px-4 py-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors"
          @click="toggleFilterSection('rating')"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2 text-yellow-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              />
            </svg>
            <span class="font-medium text-gray-800">Ratings</span>
            <span
              v-if="filters.rating !== 0"
              class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded-full filter-badge"
              >1</span
            >
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 transition-transform duration-300"
            :class="{
              'transform rotate-180': activeFilterSection === 'rating',
            }"
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
        <div
          class="px-4 py-3 space-y-2 border-t border-gray-200 bg-white transition-all duration-300 overflow-hidden filter-options"
          :class="{
            'filter-options-open': activeFilterSection === 'rating',
            'filter-options-closed': activeFilterSection !== 'rating',
          }"
        >
          <label
            v-for="rating in ratings"
            :key="rating.value"
            class="filter-option yellow"
          >
            <input
              :checked="filters.rating === rating.value"
              type="radio"
              name="rating"
              :value="rating.value"
              @change="onFilterChange('rating', rating.value)"
            />
            <span v-if="rating.value > 0" class="flex items-center">
              <CoursesFiltersStarRating :rating="rating.value" size="sm" />
              <span class="ml-1">& Up</span>
            </span>
            <span v-else>{{ rating.label }}</span>
          </label>
        </div>
      </div>

      <!-- Duration -->
      <div
        class="mb-3 border border-gray-200 rounded-lg overflow-hidden filter-accordion"
      >
        <button
          class="w-full px-4 py-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors"
          @click="toggleFilterSection('duration')"
        >
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2 text-red-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd"
              />
            </svg>
            <span class="font-medium text-gray-800">Duration</span>
            <span
              v-if="filters.duration !== 'any'"
              class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full filter-badge"
              >1</span
            >
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 transition-transform duration-300"
            :class="{
              'transform rotate-180': activeFilterSection === 'duration',
            }"
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
        <div
          class="px-4 py-3 space-y-2 border-t border-gray-200 bg-white transition-all duration-300 overflow-hidden filter-options"
          :class="{
            'filter-options-open': activeFilterSection === 'duration',
            'filter-options-closed': activeFilterSection !== 'duration',
          }"
        >
          <label
            v-for="duration in durations"
            :key="duration.value"
            class="filter-option red"
          >
            <input
              :checked="filters.duration === duration.value"
              type="radio"
              name="duration"
              :value="duration.value"
              @change="onFilterChange('duration', duration.value)"
            />
            <span>{{ duration.label }}</span>
          </label>
        </div>
      </div>

      <!-- Apply/Clear buttons for mobile -->
      <div
        class="sticky bottom-0 left-0 right-0 p-4 pt-2 bg-white border-t border-gray-200 mt-4 filter-actions"
      >
        <div class="flex space-x-3">
          <button
            v-if="hasActiveFilters"
            class="flex-1 py-3 text-center border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors clear-btn"
            @click="clearAllFilters"
          >
            Clear All
          </button>
          <button
            class="flex-1 py-3 text-center bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors apply-btn"
            @click="closeDrawer"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) rgba(229, 231, 235, 0.5);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(229, 231, 235, 0.5);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.7);
}

/* Common filter option styles */
.filter-option {
  display: flex;
  align-items: center;
  padding: 0.375rem 0.5rem;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.filter-option:hover {
  transform: translateX(2px);
}

.filter-option input {
  margin-right: 0.75rem;
}

/* Color variants */
.filter-option.indigo:hover {
  background-color: rgba(238, 242, 255, 0.7);
}

.filter-option.green:hover {
  background-color: rgba(236, 253, 245, 0.7);
}

.filter-option.purple:hover {
  background-color: rgba(243, 232, 255, 0.7);
}

.filter-option.blue:hover {
  background-color: rgba(235, 245, 255, 0.7);
}

.filter-option.yellow:hover {
  background-color: rgba(254, 243, 199, 0.7);
}

.filter-option.red:hover {
  background-color: rgba(254, 226, 226, 0.7);
}

/* Radio inputs by color */
.filter-option.indigo input {
  color: rgb(79, 70, 229);
}

.filter-option.green input {
  color: rgb(16, 185, 129);
}

.filter-option.purple input {
  color: rgb(139, 92, 246);
}

.filter-option.blue input {
  color: rgb(59, 130, 246);
}

.filter-option.yellow input {
  color: rgb(245, 158, 11);
}

.filter-option.red input {
  color: rgb(239, 68, 68);
}

/* Accordion animations */
.filter-accordion {
  margin-bottom: 0.75rem;
  transform: translateZ(0);
  transition: all 0.3s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-accordion:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.filter-options {
  max-height: 0;
  padding-top: 0;
  padding-bottom: 0;
  opacity: 0;
}

.filter-options-open {
  max-height: 300px;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  opacity: 1;
}

.filter-options-closed {
  max-height: 0;
  padding-top: 0;
  padding-bottom: 0;
  opacity: 0;
}

.filter-badge {
  transition: all 0.3s;
}

.filter-accordion:hover .filter-badge {
  transform: scale(1.1);
}

/* Drawer entry animations */
.filter-drawer-active .filter-drawer-title,
.filter-drawer-active .close-btn {
  animation: fadeInDown 0.5s forwards;
}

.filter-drawer-active .filter-accordion {
  opacity: 0;
  animation: slideInRight 0.5s forwards;
}

.filter-drawer-active .filter-accordion:nth-child(1) {
  animation-delay: 0.05s;
}
.filter-drawer-active .filter-accordion:nth-child(2) {
  animation-delay: 0.1s;
}
.filter-drawer-active .filter-accordion:nth-child(3) {
  animation-delay: 0.15s;
}
.filter-drawer-active .filter-accordion:nth-child(4) {
  animation-delay: 0.2s;
}
.filter-drawer-active .filter-accordion:nth-child(5) {
  animation-delay: 0.25s;
}
.filter-drawer-active .filter-accordion:nth-child(6) {
  animation-delay: 0.3s;
}

.filter-drawer-active .filter-actions {
  opacity: 0;
  animation: fadeInUp 0.5s forwards 0.4s;
}

.filter-badge-count {
  animation: pulse 2s infinite;
}

.apply-btn {
  position: relative;
  overflow: hidden;
}

.apply-btn::after {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    transparent 0%,
    rgba(255, 255, 255, 0.2) 50%,
    transparent 100%
  );
  animation: shine 3s infinite;
  animation-delay: 1s;
}

.clear-btn {
  position: relative;
  overflow: hidden;
}

/* Animations */
@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes pulse {
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

@keyframes shine {
  0% {
    left: -100%;
  }
  20%,
  100% {
    left: 100%;
  }
}
</style>
