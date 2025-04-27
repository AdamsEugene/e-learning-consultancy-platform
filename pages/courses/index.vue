<!-- pages/courses/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { coursesData, popularCategoriesData } from "~/constants/coursesData";

// Define types
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

// State
const courses = ref<Course[]>([]);
const featuredCourses = ref<Course[]>([]);
const popularCategories = ref<{ name: string; count: number; image: string }[]>(
  []
);
const loading = ref(true);
const showMobileFilters = ref(false);
const searchQuery = ref("");

// Filter state (shared with CourseFilters component)
const filters = ref({
  category: "all",
  instructor: "all",
  price: "all",
  level: "All Levels",
  rating: 0,
  duration: "any",
});

// Sort options
const sortOptions = [
  { label: "Most Popular", value: "popular" },
  { label: "Highest Rated", value: "rating" },
  { label: "Newest", value: "newest" },
  { label: "Price: Low to High", value: "price_asc" },
  { label: "Price: High to Low", value: "price_desc" },
];
const selectedSort = ref("popular");

// Fetch data
const fetchCoursesData = async () => {
  loading.value = true;

  try {
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    courses.value = [...coursesData];

    // Set featured courses (would be from API in real app)
    featuredCourses.value = courses.value
      .filter(
        (course) =>
          course.badges?.includes("Bestseller") ||
          course.badges?.includes("Top Rated")
      )
      .slice(0, 4);

    // Set popular categories (would be from API in real app)
    popularCategories.value = [...popularCategoriesData];
  } catch (error) {
    console.error("Error fetching courses:", error);
  } finally {
    loading.value = false;
  }
};

// Categories list
const categories = computed(() => {
  const categoriesSet = new Set<string>();
  courses.value.forEach((course) => categoriesSet.add(course.category));
  return Array.from(categoriesSet);
});

// Instructors list
const instructors = computed(() => {
  const instructorsSet = new Set<string>();
  courses.value.forEach((course) => instructorsSet.add(course.instructor));
  return Array.from(instructorsSet);
});

// Update filters from CourseFilters component
const updateFilters = (newFilters: typeof filters.value) => {
  filters.value = newFilters;
};

// Handle mobile filter toggle
const handleMobileFilterToggle = (isOpen: boolean) => {
  showMobileFilters.value = isOpen;
};

// Clear filters
const clearFilters = () => {
  filters.value = {
    category: "all",
    instructor: "all",
    price: "all",
    level: "All Levels",
    rating: 0,
    duration: "any",
  };
};

// Handle search input
const handleSearch = () => {
  // In a real app, you might make an API call with the search query
  console.log("Searching for:", searchQuery.value);
};

// Handle sort change
const handleSortChange = (event: Event) => {
  const select = event.target as HTMLSelectElement;
  selectedSort.value = select.value;
};

// Filter courses based on current filters and search query
const filteredCourses = computed(() => {
  let result = [...courses.value];

  // Filter by search query
  if (searchQuery.value.trim() !== "") {
    const searchTerm = searchQuery.value.toLowerCase();
    result = result.filter(
      (course) =>
        course.title.toLowerCase().includes(searchTerm) ||
        course.category.toLowerCase().includes(searchTerm) ||
        course.instructor.toLowerCase().includes(searchTerm)
    );
  }

  // Filter by category
  if (filters.value.category !== "all") {
    result = result.filter(
      (course) => course.category === filters.value.category
    );
  }

  // Filter by instructor
  if (filters.value.instructor !== "all") {
    result = result.filter(
      (course) => course.instructor === filters.value.instructor
    );
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

  // Filter by level
  if (filters.value.level !== "All Levels") {
    result = result.filter((course) => course.level === filters.value.level);
  }

  // Filter by rating
  if (filters.value.rating > 0) {
    result = result.filter((course) => course.rating >= filters.value.rating);
  }

  // Filter by duration
  if (filters.value.duration !== "any") {
    // This would need a more complex implementation for real duration filtering
    // Just a placeholder for now
    switch (filters.value.duration) {
      case "short":
        // Under 3 hours
        result = result.filter(
          (course) => parseFloat(course.duration.split("h")[0]) < 3
        );
        break;
      case "medium":
        // 3-10 hours
        result = result.filter((course) => {
          const hours = parseFloat(course.duration.split("h")[0]);
          return hours >= 3 && hours <= 10;
        });
        break;
      case "long":
        // Over 10 hours
        result = result.filter(
          (course) => parseFloat(course.duration.split("h")[0]) > 10
        );
        break;
    }
  }

  // Apply sorting
  switch (selectedSort.value) {
    case "popular":
      result.sort((a, b) => b.reviewCount - a.reviewCount);
      break;
    case "rating":
      result.sort((a, b) => b.rating - a.rating);
      break;
    case "newest":
      // In a real app, you'd have createdAt field
      result.sort((a, b) => b.id - a.id);
      break;
    case "price_asc":
      result.sort((a, b) => a.price - b.price);
      break;
    case "price_desc":
      result.sort((a, b) => b.price - a.price);
      break;
  }

  return result;
});

// Load data on component mount
onMounted(() => {
  fetchCoursesData();
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Hero section -->
    <section
      class="relative bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-20"
    >
      <!-- Background pattern -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10" />
      </div>

      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
          <h1 class="text-4xl md:text-5xl font-bold mb-6 animate-fade-in">
            Expand Your Knowledge with Our Expert Courses
          </h1>
          <p
            class="text-xl mb-8 text-indigo-100 animate-fade-in animation-delay-200"
          >
            Discover thousands of courses taught by industry experts to help you
            master new skills, advance your career, or explore new hobbies.
          </p>

          <!-- Search form -->
          <div class="max-w-xl mx-auto animate-fade-in animation-delay-400">
            <form class="relative" @submit.prevent="handleSearch">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search for courses, skills, or topics..."
                class="w-full py-4 px-5 pr-12 rounded-lg border shadow-lg text-white"
              />
              <button
                type="submit"
                class="absolute inset-y-0 right-0 px-4 flex items-center bg-indigo-700 hover:bg-indigo-800 text-white rounded-r-lg transition-colors"
              >
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
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular categories section -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <div
          class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8"
        >
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
            Popular Categories
          </h2>
          <NuxtLink
            to="/categories"
            class="mt-2 md:mt-0 text-indigo-600 hover:text-indigo-800 font-medium flex items-center"
          >
            View All Categories
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 ml-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </NuxtLink>
        </div>

        <!-- Categories grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
          <NuxtLink
            v-for="category in popularCategories"
            :key="category.name"
            :to="`/courses/category/${encodeURIComponent(category.name)}`"
            class="group relative rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 bg-white border border-gray-200"
          >
            <!-- Category image with overlay -->
            <div class="h-36 relative overflow-hidden">
              <img
                :src="category.image"
                :alt="category.name"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent group-hover:from-black/80 transition-all duration-300"
              />
              <div class="absolute inset-x-0 bottom-0 p-4">
                <h3 class="text-white font-bold">{{ category.name }}</h3>
                <span class="text-white text-sm"
                  >{{ category.count }} courses</span
                >
              </div>
            </div>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Featured courses section -->
    <section class="py-16 bg-gray-100">
      <div class="container mx-auto px-4">
        <div
          class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8"
        >
          <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
            Featured Courses
          </h2>
          <NuxtLink
            to="/courses"
            class="mt-2 md:mt-0 text-indigo-600 hover:text-indigo-800 font-medium flex items-center"
          >
            View All Courses
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 ml-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </NuxtLink>
        </div>

        <!-- Featured courses grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <CoursesCourseCard
            v-for="course in featuredCourses"
            :key="course.id"
            v-bind="course"
          />
        </div>
      </div>
    </section>

    <!-- Main courses catalog -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold mb-8 text-gray-900">
          Browse All Courses
        </h2>

        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Sidebar filters - desktop -->
          <div class="lg:w-1/4">
            <CoursesCourseFilters
              :categories="categories"
              :instructors="instructors"
              :show-mobile-filter="showMobileFilters"
              :initial-filters="filters"
              @update:filters="updateFilters"
              @clear="clearFilters"
              @mobile-drawer-toggle="handleMobileFilterToggle"
            />
          </div>

          <!-- Course list area -->
          <div class="lg:w-3/4">
            <!-- Sorting and results count -->
            <div
              class="bg-white p-4 rounded-lg mb-6 flex flex-col md:flex-row justify-between items-center border border-gray-200 shadow-sm"
            >
              <div class="text-gray-700 mb-4 md:mb-0">
                Showing
                <span class="font-bold">{{ filteredCourses.length }}</span>
                results
              </div>
              <div class="flex items-center">
                <!-- <label for="sort" class="text-gray-700 mr-2">Sort by:</label> -->
                <select
                  id="sort"
                  v-model="selectedSort"
                  class="appearance-none bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none  text-gray-900 text-sm"
                  @change="handleSortChange"
                >
                  <option
                    v-for="option in sortOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Show empty state if no courses found -->
            <div
              v-if="!loading && filteredCourses.length === 0"
              class="bg-white p-10 rounded-lg border border-gray-200 text-center shadow"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-16 w-16 mx-auto mb-4 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd"
                />
              </svg>
              <h3 class="text-lg font-bold text-gray-900 mb-2">
                No courses found
              </h3>
              <p class="text-gray-600 mb-4">
                Try changing your search or filter criteria.
              </p>
              <button
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors"
                @click="clearFilters"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                    clip-rule="evenodd"
                  />
                </svg>
                Clear Filters
              </button>
            </div>

            <!-- Course list component -->
            <CoursesCourseList
              v-else
              :courses="filteredCourses"
              :loading="loading"
              :title="searchQuery ? 'Search Results' : 'All Courses'"
              :subtitle="`${filteredCourses.length} courses found`"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section
      class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white"
    >
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          Ready to Start Learning?
        </h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto">
          Join thousands of students who are already taking the next step in
          their careers. Get unlimited access to our course library, expert
          instructors, and community.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <NuxtLink
            to="/courses"
            class="px-8 py-4 bg-white text-indigo-600 hover:bg-gray-100 rounded-lg font-bold transition-colors shadow-md hover:shadow-lg"
          >
            Browse All Courses
          </NuxtLink>
          <NuxtLink
            to="/auth/register"
            class="px-8 py-4 border-2 border-white text-white hover:bg-white hover:text-indigo-600 rounded-lg font-bold transition-colors"
          >
            Sign Up Now
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
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
.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1s forwards;
}

.animation-delay-200 {
  animation-delay: 200ms;
}

.animation-delay-400 {
  animation-delay: 400ms;
}

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
</style>
