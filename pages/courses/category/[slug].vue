<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- pages/courses/category/[slug].vue -->
<script setup lang="ts">
import { allCoursesData, categoryInfoData } from "~/constants/coursesData";

// Types
interface Course {
  id: number;
  title: string;
  category: string;
  subcategory?: string;
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
  description?: string;
}

// Get category from route
const route = useRoute();
const category = computed(
  () => (route.params.slug as string) || "Web Development"
);

// State
const courses = ref<Course[]>([]);
const loading = ref(true);
const error = ref(false);
const showMobileFilters = ref(false);
const activeSort = ref("popular");
const currentPage = ref(1);
const itemsPerPage = 9;
const layout = ref<"grid" | "list">("grid");

// Filter state
const filters = ref({
  subcategory: "all",
  instructor: "all",
  price: "all",
  level: "All Levels",
  rating: 0,
  duration: "any",
});

// Category metadata
const categoryInfo = computed(() => {
  const categoryData: Record<
    string,
    {
      title: string;
      description: string;
      image: string;
      subcategories: string[];
      stats?: { label: string; value: string | number }[];
    }
  > = { ...categoryInfoData };

  // Format the slug to match the object keys
  const formattedSlug = (category.value || "")
    .toLowerCase()
    .replace(/\s+/g, "-");

  return (
    categoryData[formattedSlug] || {
      title: `${category.value.replace(/-/g, " ")} Courses`,
      description: `Explore our comprehensive collection of ${category.value.replace(
        /-/g,
        " "
      )} courses designed to help you master skills and achieve your professional goals.`,
      image: "/images/courses/c3.jpg",
      subcategories: [],
      stats: [
        { label: "Students", value: "100K+" },
        { label: "Courses", value: 50 },
        { label: "Instructors", value: 20 },
        { label: "Job Growth", value: "15%" },
      ],
    }
  );
});

// Format category title for display
const formattedCategoryTitle = computed(() => {
  return category.value
    .replace(/-/g, " ")
    .replace(/\b\w/g, (l) => l.toUpperCase());
});

// Fetch courses data
const fetchCourses = async () => {
  loading.value = true;
  error.value = false;

  try {
    // In a real app, this would be an API call with the category as a parameter
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data - in a real app, this would come from an API
    const allCourses = [...allCoursesData];

    // Filter by category - match category either exactly or with slug format
    courses.value = allCourses.filter(
      (course) =>
        course.category.toLowerCase() ===
          formattedCategoryTitle.value.toLowerCase() ||
        course.category.toLowerCase().replace(/\s+/g, "-") ===
          category.value.toLowerCase()
    );
  } catch (err) {
    console.error("Error fetching courses:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

// Apply filters and sorting
const filteredCourses = computed(() => {
  let result = [...courses.value];

  // Filter by subcategory
  if (filters.value.subcategory !== "all") {
    result = result.filter(
      (course) => course.subcategory === filters.value.subcategory
    );
  }

  // Filter by level
  if (filters.value.level !== "All Levels") {
    result = result.filter((course) => course.level === filters.value.level);
  }

  // Filter by price
  if (filters.value.price !== "all") {
    switch (filters.value.price) {
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

  // Filter by rating
  if (filters.value.rating > 0) {
    result = result.filter((course) => course.rating >= filters.value.rating);
  }

  // Filter by instructor
  if (filters.value.instructor !== "all") {
    result = result.filter(
      (course) => course.instructor === filters.value.instructor
    );
  }

  // Apply sorting
  switch (activeSort.value) {
    case "popular":
      result = result.sort((a, b) => b.reviewCount - a.reviewCount);
      break;
    case "highest_rated":
      result = result.sort((a, b) => b.rating - a.rating);
      break;
    case "newest":
      // In a real app, this would sort by date
      result = result.sort((a, b) => b.id - a.id);
      break;
    case "price_low":
      result = result.sort((a, b) => a.price - b.price);
      break;
    case "price_high":
      result = result.sort((a, b) => b.price - a.price);
      break;
  }

  return result;
});

// Pagination
const paginatedCourses = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return filteredCourses.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(filteredCourses.value.length / itemsPerPage);
});

// Update filters using our enhanced CourseFilters component
const updateFilters = (newFilters: any) => {
  filters.value = { ...filters.value, ...newFilters };
  currentPage.value = 1; // Reset pagination when filters change
};

// Handle filter updates from subcategory pills
const updateSubcategory = (subcategory: string) => {
  filters.value.subcategory = subcategory;
  currentPage.value = 1; // Reset pagination when filters change
};

// Change sort option
const changeSort = (sortOption: string) => {
  activeSort.value = sortOption;
  currentPage.value = 1; // Reset pagination when sort changes
};

// Toggle mobile filters
const toggleMobileFilters = (isOpen: boolean) => {
  showMobileFilters.value = isOpen;
};

// Reset all filters
const resetFilters = () => {
  filters.value = {
    subcategory: "all",
    level: "All Levels",
    price: "all",
    rating: 0,
    instructor: "all",
    duration: "any",
  };
  activeSort.value = "popular";
  currentPage.value = 1;
};

// Page navigation
const goToPage = (page: number) => {
  currentPage.value = page;
  // Scroll to top of the course list
  document
    .getElementById("courses-top")
    ?.scrollIntoView({ behavior: "smooth" });
};

// Toggle layout
const toggleLayout = (newLayout: typeof layout.value) => {
  layout.value = newLayout;
};

// Subcategories from the category info
const subcategories = computed(() => {
  return categoryInfo.value.subcategories;
});

// Get unique instructors
const instructors = computed(() => {
  const instructorsSet = new Set<string>();
  courses.value.forEach((course) => instructorsSet.add(course.instructor));
  return Array.from(instructorsSet);
});

// Sort options for the dropdown
const sortOptions = [
  { label: "Most Popular", value: "popular" },
  { label: "Highest Rated", value: "highest_rated" },
  { label: "Newest", value: "newest" },
  { label: "Price: Low to High", value: "price_low" },
  { label: "Price: High to Low", value: "price_high" },
];

// Watch for category changes
watch(
  () => route.params.slug,
  () => {
    fetchCourses();
    resetFilters();
  }
);

// Fetch data on mount
onMounted(() => {
  fetchCourses();
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Category Hero Section with animated background and parallax effect -->
    <section class="relative overflow-hidden">
      <!-- Background image with overlay -->
      <div class="relative h-80 md:h-96">
        <img
          :src="categoryInfo.image"
          :alt="categoryInfo.title"
          class="absolute inset-0 w-full h-full object-cover"
        />
        <div
          class="absolute inset-0 bg-gradient-to-r from-indigo-600/90 to-purple-600/90"
        >
          <!-- Animated pattern overlay -->
          <div class="absolute inset-0 bg-pattern opacity-10" />
        </div>
      </div>

      <!-- Category info with animated entrance -->
      <div class="container mx-auto px-4 md:px-6 relative">
        <div class="absolute bottom-12 left-0 right-0 text-white px-4 md:px-6">
          <div class="max-w-3xl animate-fade-in">
            <!-- Label badge -->
            <div
              class="inline-block mb-4 bg-white/20 backdrop-blur-sm rounded-full px-4 py-1 text-white text-sm font-bold shadow-lg"
            >
              Courses & Tutorials
            </div>

            <h1 class="text-3xl md:text-5xl font-bold mb-4 text-shadow">
              {{ categoryInfo.title }}
            </h1>

            <p class="text-lg md:text-xl text-white/90 mb-6 max-w-2xl">
              {{ categoryInfo.description }}
            </p>

            <!-- Category stats -->
            <div
              v-if="categoryInfo.stats"
              class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mt-8 mb-2"
            >
              <div
                v-for="(stat, i) in categoryInfo.stats"
                :key="i"
                class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center border border-white/20 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1"
                :style="{ 'animation-delay': `${i * 100}ms` }"
              >
                <div class="text-2xl md:text-3xl font-bold text-white">
                  {{ stat.value }}
                </div>
                <div class="text-sm text-white/80">{{ stat.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-4 md:px-6 py-8 md:py-12">
      <!-- Breadcrumbs -->
      <div class="mb-6 text-sm">
        <NuxtLink
          to="/"
          class="text-gray-500 hover:text-indigo-600 transition-colors"
          >Home</NuxtLink
        >
        <span class="mx-2 text-gray-400">/</span>
        <NuxtLink
          to="/courses"
          class="text-gray-500 hover:text-indigo-600 transition-colors"
          >Courses</NuxtLink
        >
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900 font-medium">{{
          formattedCategoryTitle
        }}</span>
      </div>

      <!-- Subcategories Pills with animation -->
      <div
        v-if="subcategories.length > 0"
        class="mb-8 flex flex-wrap gap-2 max-w-4xl animate-fade-in animation-delay-200"
      >
        <button
          class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105"
          :class="
            filters.subcategory === 'all'
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
          "
          @click="updateSubcategory('all')"
        >
          All Subcategories
        </button>
        <button
          v-for="(subcat, idx) in subcategories"
          :key="subcat"
          class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105"
          :class="
            filters.subcategory === subcat
              ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
          "
          :style="{ 'animation-delay': `${300 + idx * 50}ms` }"
          @click="updateSubcategory(subcat)"
        >
          {{ subcat }}
        </button>
      </div>

      <!-- Course Stats & Sort Controls -->
      <div
        id="courses-top"
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 animate-fade-in animation-delay-300"
      >
        <div class="text-gray-600 mb-4 sm:mb-0 flex items-center">
          <span
            class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded font-bold mr-2"
            >{{ filteredCourses.length }}</span
          >
          courses in
          <span class="font-semibold text-gray-800 px-2">{{
            formattedCategoryTitle
          }}</span>
          <span v-if="filters.subcategory !== 'all'" class="ml-1">
            ·
            <span class="text-indigo-600 font-medium">{{
              filters.subcategory
            }}</span>
          </span>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Layout toggle buttons -->
          <div class="hidden sm:flex space-x-2">
            <button
              class="inline-flex items-center px-2 py-1 rounded-md text-sm transition-colors duration-200"
              :class="
                layout === 'grid'
                  ? 'bg-indigo-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              "
              aria-label="Grid view"
              @click="toggleLayout('grid')"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
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
            </button>
            <button
              class="inline-flex items-center px-2 py-1 rounded-md text-sm transition-colors duration-200"
              :class="
                layout === 'list'
                  ? 'bg-indigo-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              "
              aria-label="List view"
              @click="toggleLayout('list')"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
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
            </button>
          </div>

          <!-- Sort dropdown -->
          <div class="relative">
           <!--  <label
              for="sort-select"
              class="text-sm text-gray-600 mr-2 hidden sm:inline-block"
              >Sort by:</label
            > -->
            <div class="relative">
              <select
                id="sort-select"
                v-model="activeSort"
                class="appearance-none bg-white text-gray-900 border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none text-sm"
                @change="changeSort(activeSort)"
              >
                <option
                  v-for="option in sortOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
              <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
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
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar filters - desktop -->
        <div class="lg:w-1/4 xl:w-1/5">
          <CoursesCourseFilters
            :categories="[]"
            :instructors="instructors"
            :show-mobile-filter="showMobileFilters"
            :initial-filters="filters"
            @update:filters="updateFilters"
            @clear="resetFilters"
            @mobile-drawer-toggle="toggleMobileFilters"
          />
        </div>

        <!-- Course list area -->
        <div class="lg:w-3/4 xl:w-4/5">
          <!-- Use our enhanced CourseList component -->
          <CoursesCourseList
            :courses="paginatedCourses"
            :loading="loading"
            title=""
            subtitle=""
            :layout="layout"
            :empty-message="'No courses found. Try adjusting your filters.'"
            :show-layout-toggle="false"
          />

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8 flex justify-center">
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <!-- Previous page -->
              <button
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium transition-colors"
                :class="
                  currentPage === 1
                    ? 'text-gray-400 cursor-not-allowed'
                    : 'text-gray-700 hover:bg-indigo-50'
                "
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
                  :key="`full-${page}`"
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors"
                  :class="[
                    currentPage === page
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-indigo-50',
                  ]"
                  @click="goToPage(page)"
                >
                  {{ page }}
                </button>
              </template>

              <template v-else>
                <!-- First page -->
                <button
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors"
                  :class="[
                    currentPage === 1
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-indigo-50',
                  ]"
                  @click="goToPage(1)"
                >
                  1
                </button>

                <!-- Ellipsis if needed -->
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
                    :key="`partial-${page}`"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors"
                    :class="[
                      currentPage === page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-indigo-50',
                    ]"
                    @click="goToPage(page)"
                  >
                    {{ page }}
                  </button>
                </template>

                <!-- Ellipsis if needed -->
                <span
                  v-if="currentPage < totalPages - 2"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  ...
                </span>

                <!-- Last page -->
                <button
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors"
                  :class="[
                    currentPage === totalPages
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-indigo-50',
                  ]"
                  @click="goToPage(totalPages)"
                >
                  {{ totalPages }}
                </button>
              </template>

              <!-- Next page -->
              <button
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium transition-colors"
                :class="
                  currentPage === totalPages
                    ? 'text-gray-400 cursor-not-allowed'
                    : 'text-gray-700 hover:bg-indigo-50'
                "
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
      </div>
    </div>

    <!-- Newsletter section with enhanced design -->
    <section
      class="py-16 mt-12 bg-gradient-to-r from-indigo-600 to-purple-600 text-white"
    >
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-xl mx-auto text-center">
          <div
            class="mb-4 inline-block px-4 py-1 rounded-full bg-white/20 backdrop-blur-sm text-sm font-bold"
          >
            Stay Updated
          </div>

          <h2 class="text-3xl font-bold mb-4 text-white">
            Get {{ formattedCategoryTitle }} Updates
          </h2>

          <p class="text-lg mb-8 text-white/80">
            Sign up for our newsletter to receive the latest courses, tutorials,
            and industry news in {{ formattedCategoryTitle }}.
          </p>

          <div class="flex flex-col sm:flex-row gap-2 relative">
            <input
              type="email"
              placeholder="Enter your email address"
              class="flex-grow px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 border-none shadow-lg !text-white !border !border-white"
            />
            <button
              class="sm:px-6 py-3 bg-white text-indigo-600 hover:bg-indigo-50 rounded-lg font-bold transition-all duration-300 transform hover:scale-105 shadow-lg"
            >
              Subscribe
            </button>

            <!-- Decorative shine element for the input field -->
            <div
              class="absolute inset-y-0 left-0 w-full sm:w-3/4 h-full overflow-hidden pointer-events-none rounded-lg"
            >
              <div
                class="absolute inset-0 w-1/3 bg-white/10 -translate-x-full hover:translate-x-full transition-all duration-1500 transform skew-x-[45deg] newsletter-shine"
              />
            </div>
          </div>

          <p class="text-white/70 text-sm mt-4">
            We respect your privacy. Unsubscribe at any time.
          </p>
        </div>
      </div>
    </section>

    <!-- Related topics section with enhanced design -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4 md:px-6">
        <h2 class="text-2xl font-bold mb-8 text-gray-900 text-center">
          Popular Topics in {{ formattedCategoryTitle }}
        </h2>

        <div
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
        >
          <NuxtLink
            v-for="(topic, index) in subcategories.length > 0
              ? subcategories
              : [
                  'JavaScript',
                  'CSS',
                  'HTML',
                  'React',
                  'Vue.js',
                  'Node.js',
                  'TypeScript',
                  'Angular',
                  'GraphQL',
                  'Web Security',
                  'Performance',
                ]"
            :key="index"
            :to="`/courses?topic=${topic}`"
            class="bg-white border border-gray-200 rounded-xl py-4 px-4 text-center shadow-sm hover:shadow-md hover:border-indigo-200 hover:text-indigo-600 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group"
            :style="{ 'animation-delay': `${index * 50}ms` }"
          >
            <!-- Subtle animated gradient on hover -->
            <div
              class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-purple-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
            />

            <!-- Topic text -->
            <span
              class="font-medium text-gray-800 group-hover:text-indigo-600 transition-colors duration-300 relative z-10"
            >
              {{ topic }}
            </span>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- FAQ Section (New) -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4 md:px-6">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4 text-gray-900">
            Frequently Asked Questions
          </h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Find answers to common questions about our
            {{ formattedCategoryTitle.toLowerCase() }} courses
          </p>
        </div>

        <div class="max-w-3xl mx-auto">
          <!-- FAQ Items -->
          <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <details class="group">
                <summary
                  class="flex items-center justify-between p-5 cursor-pointer bg-white hover:bg-gray-50 transition-colors"
                >
                  <h3 class="text-lg font-semibold text-gray-800">
                    What prior knowledge do I need for
                    {{ formattedCategoryTitle }} courses?
                  </h3>
                  <span
                    class="transition-transform duration-300 group-open:rotate-180"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-indigo-600"
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
                  </span>
                </summary>
                <div class="p-5 border-t border-gray-200 bg-gray-50">
                  <p class="text-gray-600">
                    The requirements vary by course level. Beginner courses
                    typically require basic computer literacy only, while
                    intermediate and advanced courses may require prior
                    experience with specific technologies or concepts. Each
                    course page lists specific prerequisites in the
                    "Requirements" section.
                  </p>
                </div>
              </details>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <details class="group">
                <summary
                  class="flex items-center justify-between p-5 cursor-pointer bg-white hover:bg-gray-50 transition-colors"
                >
                  <h3 class="text-lg font-semibold text-gray-800">
                    Do I get a certificate upon completion?
                  </h3>
                  <span
                    class="transition-transform duration-300 group-open:rotate-180"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-indigo-600"
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
                  </span>
                </summary>
                <div class="p-5 border-t border-gray-200 bg-gray-50">
                  <p class="text-gray-600">
                    Yes! Upon successfully completing all course requirements,
                    including quizzes and assignments, you'll receive a
                    certificate of completion that you can share on your resume,
                    portfolio, or LinkedIn profile.
                  </p>
                </div>
              </details>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <details class="group">
                <summary
                  class="flex items-center justify-between p-5 cursor-pointer bg-white hover:bg-gray-50 transition-colors"
                >
                  <h3 class="text-lg font-semibold text-gray-800">
                    How long do I have access to the course materials?
                  </h3>
                  <span
                    class="transition-transform duration-300 group-open:rotate-180"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-indigo-600"
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
                  </span>
                </summary>
                <div class="p-5 border-t border-gray-200 bg-gray-50">
                  <p class="text-gray-600">
                    Once you enroll in a course, you'll have lifetime access to
                    all course materials, including videos, assignments, and
                    supplementary resources. This allows you to learn at your
                    own pace and revisit concepts whenever needed.
                  </p>
                </div>
              </details>
            </div>

            <!-- FAQ Item 4 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <details class="group">
                <summary
                  class="flex items-center justify-between p-5 cursor-pointer bg-white hover:bg-gray-50 transition-colors"
                >
                  <h3 class="text-lg font-semibold text-gray-800">
                    Can I get personalized help if I'm stuck?
                  </h3>
                  <span
                    class="transition-transform duration-300 group-open:rotate-180"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-indigo-600"
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
                  </span>
                </summary>
                <div class="p-5 border-t border-gray-200 bg-gray-50">
                  <p class="text-gray-600">
                    Absolutely! All our courses include access to Q&A forums
                    where instructors and fellow students can help with
                    questions. Many courses also offer direct messaging with
                    instructors, live Q&A sessions, or community forums where
                    you can get support.
                  </p>
                </div>
              </details>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Line clamping for multiline text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Text shadow for better readability on hero section */
.text-shadow {
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

/* Animation for newsletter shine effect */
@keyframes shine {
  0% {
    transform: translateX(-100%) skewX(45deg);
  }
  100% {
    transform: translateX(200%) skewX(45deg);
  }
}

.newsletter-shine {
  animation: shine 3s infinite;
}

/* Background pattern for hero section */
.bg-pattern {
  background-image: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.1) 1px,
    transparent 1px
  );
  background-size: 20px 20px;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.8s forwards;
}

.animation-delay-200 {
  animation-delay: 200ms;
}

.animation-delay-300 {
  animation-delay: 300ms;
}

/* Open/close animation for FAQs */
details summary::-webkit-details-marker {
  display: none;
}

@keyframes open {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

details[open] > div {
  animation: open 0.3s ease-in-out;
}

/* Badge animation effect */
@keyframes pulse {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
  }
  50% {
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0);
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Group modifier for the details element to apply styles when open */
.group[open] .group-open\:rotate-180 {
  transform: rotate(180deg);
}
</style>
