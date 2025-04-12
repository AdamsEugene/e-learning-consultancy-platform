<!-- components/courses/CourseList.vue -->
<script setup lang="ts">
import { ref, computed, watch } from "vue";
import CourseCard from "./CourseCard.vue";

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
  showFilters?: boolean;
  showPagination?: boolean;
  itemsPerPage?: number;
  title?: string;
  subtitle?: string;
}

// Props with defaults
const props = withDefaults(defineProps<Props>(), {
  loading: false,
  showFilters: true,
  showPagination: true,
  itemsPerPage: 9,
  title: "Courses",
  subtitle: "Browse our latest courses",
});

// Pagination state
const currentPage = ref(1);
const totalPages = computed(() =>
  Math.ceil(filteredCourses.value.length / props.itemsPerPage)
);

// Filtering state
const filterCategory = ref("all");
const filterLevel = ref("all");
const filterPrice = ref("all");
const searchQuery = ref("");
const sortBy = ref("popular");

// Computed available categories, levels from courses
const categories = computed(() => {
  const uniqueCategories = new Set<string>();
  props.courses.forEach((course) => uniqueCategories.add(course.category));
  return Array.from(uniqueCategories).sort();
});

const levels = computed(() => {
  const uniqueLevels = new Set<string>();
  props.courses.forEach((course) => uniqueLevels.add(course.level));
  return Array.from(uniqueLevels).sort();
});

// Filtered and sorted courses
const filteredCourses = computed(() => {
  let result = [...props.courses];

  // Apply category filter
  if (filterCategory.value !== "all") {
    result = result.filter(
      (course) => course.category === filterCategory.value
    );
  }

  // Apply level filter
  if (filterLevel.value !== "all") {
    result = result.filter((course) => course.level === filterLevel.value);
  }

  // Apply price filter
  if (filterPrice.value !== "all") {
    switch (filterPrice.value) {
      case "free":
        result = result.filter((course) => course.price === 0);
        break;
      case "paid":
        result = result.filter((course) => course.price > 0);
        break;
      case "under50":
        result = result.filter(
          (course) => course.price > 0 && course.price < 50
        );
        break;
      case "under100":
        result = result.filter(
          (course) => course.price > 0 && course.price < 100
        );
        break;
      case "over100":
        result = result.filter((course) => course.price >= 100);
        break;
    }
  }

  // Apply search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    result = result.filter((course) => {
      return (
        course.title.toLowerCase().includes(query) ||
        course.instructor.toLowerCase().includes(query) ||
        course.category.toLowerCase().includes(query)
      );
    });
  }

  // Apply sorting
  switch (sortBy.value) {
    case "popular":
      result = result.sort((a, b) => b.reviewCount - a.reviewCount);
      break;
    case "rating":
      result = result.sort((a, b) => b.rating - a.rating);
      break;
    case "newest":
      // In a real app, this would sort by date
      result = result.sort((a, b) => b.id - a.id);
      break;
    case "priceAsc":
      result = result.sort((a, b) => a.price - b.price);
      break;
    case "priceDesc":
      result = result.sort((a, b) => b.price - a.price);
      break;
  }

  return result;
});

// Paginated courses to display
const paginatedCourses = computed(() => {
  const startIndex = (currentPage.value - 1) * props.itemsPerPage;
  const endIndex = startIndex + props.itemsPerPage;
  return filteredCourses.value.slice(startIndex, endIndex);
});

// Empty state check
const isEmptyState = computed(() => {
  return filteredCourses.value.length === 0 && !props.loading;
});

// Methods
const goToPage = (page: number) => {
  currentPage.value = page;
  // Scroll to top of course list
  document
    .getElementById("course-list-top")
    ?.scrollIntoView({ behavior: "smooth" });
};

const clearFilters = () => {
  filterCategory.value = "all";
  filterLevel.value = "all";
  filterPrice.value = "all";
  searchQuery.value = "";
  sortBy.value = "popular";
};

// Reset to page 1 when filters change
watch([filterCategory, filterLevel, filterPrice, searchQuery, sortBy], () => {
  currentPage.value = 1;
});
</script>

<template>
  <div>
    <!-- Header with title and filters -->
    <div id="course-list-top" class="mb-8">
      <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6"
      >
        <div>
          <h2 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900">
            {{ title }}
          </h2>
          <p class="text-gray-600">{{ subtitle }}</p>
        </div>

        <!-- Top filters -->
        <div
          v-if="showFilters"
          class="flex items-center space-x-2 mt-4 md:mt-0"
        >
          <!-- Search input -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search courses..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900"
            />
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
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
            </div>
          </div>

          <!-- Sort dropdown -->
          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="popular">Most Popular</option>
              <option value="rating">Highest Rated</option>
              <option value="newest">Newest</option>
              <option value="priceAsc">Price: Low to High</option>
              <option value="priceDesc">Price: High to Low</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- More detailed filters row -->
      <div v-if="showFilters" class="flex flex-wrap gap-4 mb-6">
        <!-- Category filter -->
        <div class="relative">
          <select
            v-model="filterCategory"
            class="pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="all">All Categories</option>
            <option
              v-for="category in categories"
              :key="category"
              :value="category"
            >
              {{ category }}
            </option>
          </select>
          <div
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>

        <!-- Level filter -->
        <div class="relative">
          <select
            v-model="filterLevel"
            class="pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="all">All Levels</option>
            <option v-for="level in levels" :key="level" :value="level">
              {{ level }}
            </option>
          </select>
          <div
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>

        <!-- Price filter -->
        <div class="relative">
          <select
            v-model="filterPrice"
            class="pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="all">All Prices</option>
            <option value="free">Free</option>
            <option value="paid">Paid</option>
            <option value="under50">Under $50</option>
            <option value="under100">Under $100</option>
            <option value="over100">$100 & Above</option>
          </select>
          <div
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>

        <!-- Clear filters button -->
        <button
          class="text-indigo-600 hover:text-indigo-800 font-medium"
          @click="clearFilters"
        >
          Clear filters
        </button>
      </div>

      <!-- Results count -->
      <div class="text-sm text-gray-600 mb-6">
        {{ filteredCourses.length }} courses found
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center items-center my-12">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      />
    </div>

    <!-- Empty state -->
    <div
      v-else-if="isEmptyState"
      class="flex flex-col items-center justify-center my-12 text-gray-500"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-16 w-16 mb-4"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      <h3 class="text-xl font-bold mb-2">No courses found</h3>
      <p class="mb-4 text-center">Try adjusting your filters or search terms</p>
      <button
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        @click="clearFilters"
      >
        Clear filters
      </button>
    </div>

    <!-- Course grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <CourseCard
        v-for="course in paginatedCourses"
        :key="course.id"
        v-bind="course"
      />
    </div>

    <!-- Pagination -->
    <div
      v-if="showPagination && totalPages > 1"
      class="flex justify-center mt-12"
    >
      <nav
        class="inline-flex rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <!-- Previous page button -->
        <button
          :disabled="currentPage === 1"
          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="goToPage(currentPage - 1)"
        >
          <span class="sr-only">Previous</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </button>

        <!-- Page numbers -->
        <template v-if="totalPages <= 7">
          <button
            v-for="page in totalPages"
            :key="page"
            :class="[
              currentPage === page
                ? 'bg-indigo-50  border-indigo-500  text-indigo-600  z-10'
                : 'bg-white  border-gray-300  text-gray-500  hover:bg-gray-50 ',
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
            ]"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>
        </template>

        <template v-else>
          <!-- First page -->
          <button
            :class="[
              currentPage === 1
                ? 'bg-indigo-50  border-indigo-500  text-indigo-600  z-10'
                : 'bg-white  border-gray-300  text-gray-500  hover:bg-gray-50 ',
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
            ]"
            @click="goToPage(1)"
          >
            1
          </button>

          <!-- Ellipsis if not near start -->
          <span
            v-if="currentPage > 3"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
          >
            ...
          </span>

          <!-- Pages around current page -->
          <template v-for="page in totalPages">
            <button
              v-if="
                page !== 1 &&
                page !== totalPages &&
                Math.abs(page - currentPage) <= 1
              "
              :key="page"
              :class="[
                currentPage === page
                  ? 'bg-indigo-50  border-indigo-500  text-indigo-600  z-10'
                  : 'bg-white  border-gray-300  text-gray-500  hover:bg-gray-50 ',
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
              ]"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
          </template>

          <!-- Ellipsis if not near end -->
          <span
            v-if="currentPage < totalPages - 2"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
          >
            ...
          </span>

          <!-- Last page -->
          <button
            :class="[
              currentPage === totalPages
                ? 'bg-indigo-50  border-indigo-500  text-indigo-600  z-10'
                : 'bg-white  border-gray-300  text-gray-500  hover:bg-gray-50 ',
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
            ]"
            @click="goToPage(totalPages)"
          >
            {{ totalPages }}
          </button>
        </template>

        <!-- Next page button -->
        <button
          :disabled="currentPage === totalPages"
          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="goToPage(currentPage + 1)"
        >
          <span class="sr-only">Next</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </nav>
    </div>
  </div>
</template>
