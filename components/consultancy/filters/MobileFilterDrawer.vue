<!-- components/consultancy/filters/MobileFilterDrawer.vue -->
<script setup lang="ts">
import { computed } from "vue";

interface Filters {
  industry: string;
  service: string;
  year: number | null;
  search: string;
}

interface Props {
  isOpen: boolean;
  categories?: string[];
  industries: string[];
  services: string[];
  years: number[];
  filters: Filters;
  hasActiveFilters: boolean;
  activeFiltersCount: number;
  isAnimating?: boolean;
}

interface Emits {
  (e: "close" | "clear"): void;
  (e: "filter-change", type: string, value: string | number | null): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const onFilterChange = (type: string, value: string | number | null) => {
  emit("filter-change", type, value);
};

const clearFilters = () => {
  emit("clear");
};

const close = () => {
  emit("close");
};

// Handle active filter counts by type
const industryActive = computed(() => props.filters.industry !== "");
const serviceActive = computed(() => props.filters.service !== "");
const yearActive = computed(() => props.filters.year !== null);
</script>

<template>
  <div
    id="mobile-filter-drawer"
    class="fixed inset-y-0 right-0 w-80 max-w-full bg-white shadow-lg z-50 overflow-y-auto transition-transform duration-300"
    :class="isOpen ? 'translate-x-0' : 'translate-x-full'"
  >
    <!-- Header -->
    <div class="sticky top-0 bg-white z-10 border-b border-gray-200">
      <div class="p-4 flex items-center justify-between">
        <h3 class="font-bold text-lg flex items-center">
          <span>Filters</span>
          <span
            v-if="hasActiveFilters"
            class="ml-2 bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full"
          >
            {{ activeFiltersCount }}
          </span>
        </h3>

        <div class="flex items-center space-x-4">
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-sm text-indigo-600 font-medium hover:text-indigo-800 transition-colors"
          >
            Clear All
          </button>

          <button
            @click="close"
            class="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
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
    </div>

    <!-- Filter options -->
    <div class="p-4 space-y-6">
      <!-- Industry filter -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h4 class="font-medium text-gray-900">Industry</h4>
          <span
            v-if="industryActive"
            class="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full"
          >
            Active
          </span>
        </div>

        <div class="space-y-2">
          <label class="filter-option">
            <input
              type="radio"
              name="mobile-industry"
              :checked="filters.industry === ''"
              @change="onFilterChange('industry', '')"
              class="mr-2"
            />
            <span class="text-gray-700">All Industries</span>
          </label>

          <label
            v-for="industry in industries"
            :key="industry"
            class="filter-option"
          >
            <input
              type="radio"
              name="mobile-industry"
              :checked="filters.industry === industry"
              @change="onFilterChange('industry', industry)"
              class="mr-2"
            />
            <span class="text-gray-700">{{ industry }}</span>
          </label>
        </div>
      </div>

      <!-- Service filter -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h4 class="font-medium text-gray-900">Service</h4>
          <span
            v-if="serviceActive"
            class="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full"
          >
            Active
          </span>
        </div>

        <div class="space-y-2">
          <label class="filter-option">
            <input
              type="radio"
              name="mobile-service"
              :checked="filters.service === ''"
              @change="onFilterChange('service', '')"
              class="mr-2"
            />
            <span class="text-gray-700">All Services</span>
          </label>

          <label
            v-for="service in services"
            :key="service"
            class="filter-option"
          >
            <input
              type="radio"
              name="mobile-service"
              :checked="filters.service === service"
              @change="onFilterChange('service', service)"
              class="mr-2"
            />
            <span class="text-gray-700">{{ service }}</span>
          </label>
        </div>
      </div>

      <!-- Year filter -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h4 class="font-medium text-gray-900">Year</h4>
          <span
            v-if="yearActive"
            class="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full"
          >
            Active
          </span>
        </div>

        <div class="space-y-2">
          <label class="filter-option">
            <input
              type="radio"
              name="mobile-year"
              :checked="filters.year === null"
              @change="onFilterChange('year', null)"
              class="mr-2"
            />
            <span class="text-gray-700">All Years</span>
          </label>

          <label v-for="year in years" :key="year" class="filter-option">
            <input
              type="radio"
              name="mobile-year"
              :checked="filters.year === year"
              @change="onFilterChange('year', year)"
              class="mr-2"
            />
            <span class="text-gray-700">{{ year }}</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Apply button (footer) -->
    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-4">
      <button
        @click="close"
        class="w-full bg-indigo-600 text-white font-medium rounded-lg py-3 hover:bg-indigo-700 transition-colors"
      >
        Apply Filters
      </button>
    </div>
  </div>
</template>

<style scoped>
.filter-option {
  display: block;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.filter-option:hover {
  background-color: rgba(79, 70, 229, 0.05);
}

/* Custom radio styles */
.filter-option input[type="radio"] {
  appearance: none;
  -webkit-appearance: none;
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid #d1d5db;
  border-radius: 50%;
  outline: none;
  transition: all 0.2s;
  position: relative;
  vertical-align: middle;
}

.filter-option input[type="radio"]:checked {
  border-color: #4f46e5;
  background-color: #4f46e5;
}

.filter-option input[type="radio"]:checked::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 0.375rem;
  height: 0.375rem;
  border-radius: 50%;
  background-color: white;
}

.filter-option input[type="radio"]:focus {
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.3);
}
</style>
