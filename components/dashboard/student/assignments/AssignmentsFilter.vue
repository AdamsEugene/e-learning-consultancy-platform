<!-- components/dashboard/DashboardAssignmentsFilters.vue -->
<script setup lang="ts">
import { ref, watch } from "vue";
import type {
  Course,
  AssignmentStatus,
  AssignmentType,
  SortOption,
} from "~/types/assignments";

// Props
interface FilterState {
  status: AssignmentStatus;
  type: AssignmentType;
  course: number | "all";
  search: string;
  sort: SortOption;
  showCompleted: boolean;
}

const props = defineProps<{
  courses: Course[];
  filterState: FilterState;
  isLoaded: boolean;
}>();

// Emits
const emit = defineEmits<{
  (e: "update:filters", filters: Partial<FilterState>): void;
}>();

// Internal state with debouncing for search
const searchQuery = ref(props.filterState.search);
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

// Pass changes to parent
const updateFilter = (key: keyof FilterState, value: any) => {
  emit("update:filters", { [key]: value });
};

// Debounced search
watch(searchQuery, (newValue) => {
  if (searchTimeout) clearTimeout(searchTimeout);

  searchTimeout = setTimeout(() => {
    updateFilter("search", newValue);
  }, 300); // Debounce for 300ms
});

// Track expanded state for mobile filters
const isMobileFiltersExpanded = ref(false);
const toggleMobileFilters = () => {
  isMobileFiltersExpanded.value = !isMobileFiltersExpanded.value;
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6 transform transition-all duration-500"
    :class="isLoaded ? 'animate-slide-down' : 'opacity-0 translate-y-8'"
  >
    <!-- Mobile filter toggle button (visible only on small screens) -->
    <div class="md:hidden mb-4">
      <button
        @click="toggleMobileFilters"
        class="w-full px-4 py-3 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-medium rounded-lg shadow-sm transition-colors flex items-center justify-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
            clip-rule="evenodd"
          />
        </svg>
        {{ isMobileFiltersExpanded ? "Hide Filters" : "Show Filters" }}

        <!-- Filter counter badge -->
        <span
          v-if="
            filterState.status !== 'all' ||
            filterState.type !== 'all' ||
            filterState.course !== 'all' ||
            filterState.search
          "
          class="ml-2 bg-indigo-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center"
        >
          {{
            [
              filterState.status !== "all",
              filterState.type !== "all",
              filterState.course !== "all",
              filterState.search ? true : false,
            ].filter(Boolean).length
          }}
        </span>
      </button>
    </div>

    <!-- Main filters container -->
    <div
      class="grid md:grid-cols-12 gap-4"
      :class="{ 'hidden md:grid': !isMobileFiltersExpanded }"
    >
      <!-- Status filter -->
      <div class="md:col-span-2 filter-group animate-fade-in">
        <label
          for="status-filter"
          class="block text-xs font-medium text-gray-500 mb-1.5"
          >Status</label
        >
        <select
          id="status-filter"
          :value="filterState.status"
          @change="updateFilter('status', $event.target.value)"
          class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
          <option value="all">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="submitted">Submitted</option>
          <option value="graded">Graded</option>
          <option value="late">Late</option>
        </select>
      </div>

      <!-- Type filter -->
      <div class="md:col-span-2 filter-group animate-fade-in">
        <label
          for="type-filter"
          class="block text-xs font-medium text-gray-500 mb-1.5"
          >Type</label
        >
        <select
          id="type-filter"
          :value="filterState.type"
          @change="updateFilter('type', $event.target.value)"
          class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
          <option value="all">All Types</option>
          <option value="assignment">Assignment</option>
          <option value="project">Project</option>
          <option value="quiz">Quiz</option>
        </select>
      </div>

      <!-- Course filter -->
      <div class="md:col-span-3 filter-group animate-fade-in">
        <label
          for="course-filter"
          class="block text-xs font-medium text-gray-500 mb-1.5"
          >Course</label
        >
        <select
          id="course-filter"
          :value="filterState.course"
          @change="updateFilter('course', $event.target.value)"
          class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
          <option value="all">All Courses</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.title }}
          </option>
        </select>
      </div>

      <!-- Search field -->
      <div class="md:col-span-3 filter-group animate-fade-in">
        <label
          for="search"
          class="block text-xs font-medium text-gray-500 mb-1.5"
          >Search</label
        >
        <div class="relative">
          <input
            id="search"
            type="text"
            v-model="searchQuery"
            placeholder="Search assignments..."
            class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              />
            </svg>
          </div>

          <!-- Clear search button -->
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Sort & Show completed -->
      <div class="md:col-span-2 flex flex-col filter-group animate-fade-in">
        <div>
          <label
            for="sort"
            class="block text-xs font-medium text-gray-500 mb-1.5"
            >Sort by</label
          >
          <select
            id="sort"
            :value="filterState.sort"
            @change="updateFilter('sort', $event.target.value)"
            class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="dueDate-asc">Due Date (Earliest)</option>
            <option value="dueDate-desc">Due Date (Latest)</option>
            <option value="title-asc">Title (A-Z)</option>
            <option value="title-desc">Title (Z-A)</option>
            <option value="course-asc">Course (A-Z)</option>
            <option value="course-desc">Course (Z-A)</option>
          </select>
        </div>

        <!-- Show completed toggle -->
        <div class="mt-4 flex items-center ml-1">
          <label class="inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              :checked="filterState.showCompleted"
              @change="updateFilter('showCompleted', $event.target.checked)"
              class="sr-only peer"
            />
            <div
              class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 toggle-animation"
            ></div>
            <span class="ml-3 text-sm font-medium text-gray-600"
              >Show completed</span
            >
          </label>
        </div>
      </div>
    </div>

    <!-- Active filters indicators -->
    <div
      v-if="
        filterState.status !== 'all' ||
        filterState.type !== 'all' ||
        filterState.course !== 'all' ||
        filterState.search
      "
      class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200"
    >
      <div class="text-xs font-medium text-gray-500 flex items-center mr-1">
        Active filters:
      </div>

      <!-- Status filter badge -->
      <div
        v-if="filterState.status !== 'all'"
        class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center group hover:bg-indigo-200 transition-colors cursor-pointer"
        @click="updateFilter('status', 'all')"
      >
        Status:
        {{
          filterState.status.charAt(0).toUpperCase() +
          filterState.status.slice(1)
        }}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 ml-1 text-indigo-500 group-hover:text-indigo-700"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Type filter badge -->
      <div
        v-if="filterState.type !== 'all'"
        class="bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center group hover:bg-purple-200 transition-colors cursor-pointer"
        @click="updateFilter('type', 'all')"
      >
        Type:
        {{
          filterState.type.charAt(0).toUpperCase() + filterState.type.slice(1)
        }}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 ml-1 text-purple-500 group-hover:text-purple-700"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Course filter badge -->
      <div
        v-if="filterState.course !== 'all'"
        class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center group hover:bg-blue-200 transition-colors cursor-pointer"
        @click="updateFilter('course', 'all')"
      >
        Course: {{ courses.find((c) => c.id === filterState.course)?.title }}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 ml-1 text-blue-500 group-hover:text-blue-700"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Search filter badge -->
      <div
        v-if="filterState.search"
        class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center group hover:bg-green-200 transition-colors cursor-pointer"
        @click="searchQuery = ''"
      >
        Search: "{{ filterState.search }}"
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 ml-1 text-green-500 group-hover:text-green-700"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
          />
        </svg>
      </div>

      <!-- Clear all filters button -->
      <button
        @click="
          () => {
            updateFilter('status', 'all');
            updateFilter('type', 'all');
            updateFilter('course', 'all');
            searchQuery = '';
          }
        "
        class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center hover:bg-red-200 transition-colors"
      >
        Clear All Filters
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
  </div>
</template>

<style scoped>
.filter-group {
  transition-duration: 0.4s;
}

.filter-group:nth-child(1) {
  transition-delay: 0ms;
}

.filter-group:nth-child(2) {
  transition-delay: 50ms;
}

.filter-group:nth-child(3) {
  transition-delay: 100ms;
}

.filter-group:nth-child(4) {
  transition-delay: 150ms;
}

.filter-group:nth-child(5) {
  transition-delay: 200ms;
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

.animate-slide-down {
  animation: slideDown 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Toggle switch animation */
.toggle-animation {
  position: relative;
  overflow: hidden;
}

.toggle-animation:after {
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.toggle-animation:before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.6) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transition: all 0.4s ease-in-out;
  z-index: 10;
}

.peer:checked ~ .toggle-animation:before {
  animation: shine 1s;
}

@keyframes shine {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}
</style>
