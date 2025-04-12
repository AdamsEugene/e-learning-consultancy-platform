<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- pages/courses/category/[slug].vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";

// Types
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
  description?: string;
}

// Get category from route
const route = useRoute();
const category = computed(
  () => (route.query.category as string) || "Web Development"
);

// State
const courses = ref<Course[]>([]);
const loading = ref(true);
const error = ref(false);
const showMobileFilters = ref(false);
const activeSort = ref("popular");
const currentPage = ref(1);
const itemsPerPage = 9;

// Filter state
const filters = ref({
  subcategory: "all",
  level: "all",
  price: "all",
  rating: 0,
  instructor: "all",
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
    }
  > = {
    "Web Development": {
      title: "Web Development Courses",
      description:
        "Master the skills needed to build modern, responsive websites and web applications. Learn frontend technologies like HTML, CSS, and JavaScript, as well as backend development with Node.js, Python, PHP, and more.",
      image: "/api/placeholder/1200/300",
      subcategories: [
        "Frontend",
        "Backend",
        "Full-Stack",
        "JavaScript",
        "React",
        "Vue.js",
        "Angular",
        "Node.js",
        "PHP",
      ],
    },
    "Data Science": {
      title: "Data Science Courses",
      description:
        "Learn to analyze complex data, build machine learning models, and derive meaningful insights. Courses cover Python, R, statistics, visualization, and AI techniques to help you excel in this high-demand field.",
      image: "/api/placeholder/1200/300",
      subcategories: [
        "Machine Learning",
        "Deep Learning",
        "Python",
        "R",
        "Data Analysis",
        "Data Visualization",
        "Statistics",
      ],
    },
    "Mobile Development": {
      title: "Mobile Development Courses",
      description:
        "Build native and cross-platform mobile applications for iOS and Android. Master frameworks like React Native, Flutter, Swift, and Kotlin to create engaging mobile experiences.",
      image: "/api/placeholder/1200/300",
      subcategories: [
        "iOS",
        "Android",
        "React Native",
        "Flutter",
        "Swift",
        "Kotlin",
      ],
    },
    Design: {
      title: "Design Courses",
      description:
        "Develop your creative skills and learn industry-standard design tools and principles. Courses cover UI/UX design, graphic design, web design, and more to help you create beautiful digital experiences.",
      image: "/api/placeholder/1200/300",
      subcategories: [
        "UI/UX",
        "Graphic Design",
        "Web Design",
        "Adobe Creative Suite",
        "Figma",
        "Sketch",
      ],
    },
  };

  return (
    categoryData[category.value] || {
      title: `${category.value} Courses`,
      description: `Explore our comprehensive collection of ${category.value} courses.`,
      image: "/api/placeholder/1200/300",
      subcategories: [],
    }
  );
});

// Fetch courses data
const fetchCourses = async () => {
  loading.value = true;
  error.value = false;

  try {
    // In a real app, this would be an API call with the category as a parameter
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data - in a real app, this would come from an API
    const allCourses = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        category: "Web Development",
        subcategory: "Full-Stack",
        image: "/api/placeholder/600/400",
        instructor: "Alex Johnson",
        rating: 4.8,
        reviewCount: 1245,
        price: 89.99,
        originalPrice: 199.99,
        level: "All Levels",
        duration: "42h 30m",
        badges: ["Bestseller", "Top Rated"],
        description:
          "A comprehensive bootcamp covering HTML, CSS, JavaScript, Node.js, and more to turn you into a full-stack web developer.",
      },
      {
        id: 5,
        title: "The Complete React Developer Course",
        category: "Web Development",
        subcategory: "Frontend",
        image: "/api/placeholder/600/400",
        instructor: "Michael Brown",
        rating: 4.8,
        reviewCount: 1089,
        price: 84.99,
        originalPrice: 169.99,
        level: "Intermediate",
        duration: "30h 10m",
        badges: ["Bestseller"],
        description:
          "Learn React by building real projects. Master modern React features including hooks, context API, and Redux.",
      },
      {
        id: 11,
        title: "Advanced CSS and Sass: Flexbox, Grid, Animations",
        category: "Web Development",
        subcategory: "Frontend",
        image: "/api/placeholder/600/400",
        instructor: "Alex Johnson",
        rating: 4.9,
        reviewCount: 1245,
        price: 74.99,
        originalPrice: 149.99,
        level: "Intermediate",
        duration: "28h 30m",
        badges: ["Top Rated"],
        description:
          "Take your CSS skills to the next level. Master flexbox, CSS Grid, responsive design, and advanced animations.",
      },
      {
        id: 12,
        title: "Full-Stack JavaScript: Node.js, Express, MongoDB",
        category: "Web Development",
        subcategory: "Backend",
        image: "/api/placeholder/600/400",
        instructor: "Michael Brown",
        rating: 4.8,
        reviewCount: 932,
        price: 89.99,
        originalPrice: 179.99,
        level: "Intermediate",
        duration: "32h 45m",
        badges: ["Bestseller"],
        description:
          "Build complete backend applications with Node.js, Express, and MongoDB. Includes authentication, API design, and deployment.",
      },
      {
        id: 13,
        title: "Vue.js - The Complete Guide",
        category: "Web Development",
        subcategory: "Frontend",
        image: "/api/placeholder/600/400",
        instructor: "Sarah Chen",
        rating: 4.9,
        reviewCount: 876,
        price: 79.99,
        originalPrice: 159.99,
        level: "All Levels",
        duration: "26h 15m",
        badges: ["Bestseller"],
        description:
          "Master Vue.js from the ground up. Learn Vue 3, Vuex, Vue Router, and how to build single-page applications.",
      },
      {
        id: 14,
        title: "PHP & MySQL - Complete Web Development Course",
        category: "Web Development",
        subcategory: "Backend",
        image: "/api/placeholder/600/400",
        instructor: "David Miller",
        rating: 4.6,
        reviewCount: 732,
        price: 69.99,
        originalPrice: 139.99,
        level: "Beginner",
        duration: "34h 20m",
        badges: [],
        description:
          "Learn PHP and MySQL to build dynamic, database-driven websites. Includes practical projects and security best practices.",
      },
      {
        id: 15,
        title: "Responsive Web Design Masterclass",
        category: "Web Development",
        subcategory: "Frontend",
        image: "/api/placeholder/600/400",
        instructor: "Emma Wilson",
        rating: 4.7,
        reviewCount: 654,
        price: 59.99,
        originalPrice: 119.99,
        level: "All Levels",
        duration: "18h 45m",
        badges: [],
        description:
          "Create websites that look great on any device. Master media queries, flexbox, CSS Grid, and responsive images.",
      },
      {
        id: 16,
        title: "Angular - The Complete Guide",
        category: "Web Development",
        subcategory: "Frontend",
        image: "/api/placeholder/600/400",
        instructor: "Thomas Clark",
        rating: 4.8,
        reviewCount: 921,
        price: 84.99,
        originalPrice: 169.99,
        level: "Intermediate",
        duration: "33h 30m",
        badges: ["Top Rated"],
        description:
          "Comprehensive course on Angular framework. Learn to build sophisticated SPAs with TypeScript and Angular.",
      },
      {
        id: 17,
        title: "Modern JavaScript From The Beginning",
        category: "Web Development",
        subcategory: "JavaScript",
        image: "/api/placeholder/600/400",
        instructor: "Alex Johnson",
        rating: 4.9,
        reviewCount: 1184,
        price: 69.99,
        originalPrice: 139.99,
        level: "All Levels",
        duration: "21h 50m",
        badges: ["Bestseller"],
        description:
          "Learn modern JavaScript from the fundamentals to advanced topics like ES6+, asynchronous JS, and the DOM.",
      },
      {
        id: 18,
        title: "GraphQL with Node.js - The Complete Guide",
        category: "Web Development",
        subcategory: "Backend",
        image: "/api/placeholder/600/400",
        instructor: "Sarah Chen",
        rating: 4.7,
        reviewCount: 687,
        price: 79.99,
        originalPrice: 159.99,
        level: "Intermediate",
        duration: "24h 15m",
        badges: ["Hot & New"],
        description:
          "Master GraphQL API development with Node.js. Learn schema design, resolvers, authentication, and performance optimization.",
      },
      {
        id: 19,
        title: "Web Accessibility - Build Inclusive Websites",
        category: "Web Development",
        subcategory: "Frontend",
        image: "/api/placeholder/600/400",
        instructor: "Emma Wilson",
        rating: 4.8,
        reviewCount: 532,
        price: 64.99,
        originalPrice: 129.99,
        level: "Intermediate",
        duration: "16h 20m",
        badges: [],
        description:
          "Learn how to make websites accessible to everyone. Master WCAG guidelines, ARIA, keyboard navigation, and testing.",
      },
      {
        id: 20,
        title: "TypeScript for Web Developers",
        category: "Web Development",
        subcategory: "JavaScript",
        image: "/api/placeholder/600/400",
        instructor: "Michael Brown",
        rating: 4.9,
        reviewCount: 743,
        price: 74.99,
        originalPrice: 149.99,
        level: "Intermediate",
        duration: "19h 45m",
        badges: ["Top Rated"],
        description:
          "Comprehensive TypeScript course for web developers. Learn type safety, interfaces, generics, and integration with frameworks.",
      },
    ];

    // Filter by category
    courses.value = allCourses.filter(
      (course) => course.category === category.value
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
      (course) => course.category === filters.value.subcategory
    );
  }

  // Filter by level
  if (filters.value.level !== "all") {
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

// Update filters
const updateFilters = (newFilters: any) => {
  filters.value = { ...filters.value, ...newFilters };
  currentPage.value = 1; // Reset pagination when filters change
};

// Change sort option
const changeSort = (sortOption: string) => {
  activeSort.value = sortOption;
  currentPage.value = 1; // Reset pagination when sort changes
};

// Toggle mobile filters
const toggleMobileFilters = () => {
  showMobileFilters.value = !showMobileFilters.value;
};

// Reset all filters
const resetFilters = () => {
  filters.value = {
    subcategory: "all",
    level: "all",
    price: "all",
    rating: 0,
    instructor: "all",
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

// Subcategories from the category info
const subcategories = computed(() => {
  return categoryInfo.value.subcategories;
});

// Get unique instructors
const instructors = computed(() => {
  const instructorsSet = new Set();
  courses.value.forEach((course) => instructorsSet.add(course.instructor));
  return Array.from(instructorsSet);
});

// Watch for category changes
watch(
  () => category.value,
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
    <!-- Category Hero Section -->
    <section class="relative overflow-hidden">
      <!-- Background image with overlay -->
      <div class="relative h-64 md:h-80">
        <img
          src=""
          :alt="categoryInfo.title"
          class="absolute inset-0 w-full h-full object-cover"
        />
        <div
          class="absolute inset-0 bg-gradient-to-r from-indigo-600/90 to-indigo-800/90"
        />
      </div>

      <!-- Category info -->
      <div class="container mx-auto px-4 md:px-6 relative">
        <div class="absolute bottom-8 left-0 right-0 text-white px-4 md:px-6">
          <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">
              {{ categoryInfo.title }}
            </h1>
            <p class="text-lg md:text-xl text-indigo-100">
              {{ categoryInfo.description }}
            </p>
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
        <span class="text-gray-900 font-medium">{{ category }}</span>
      </div>

      <!-- Subcategories Pills -->
      <div
        v-if="subcategories.length > 0"
        class="mb-8 flex flex-wrap gap-2 max-w-3xl"
      >
        <button
          class="px-4 py-2 rounded-full text-sm font-medium transition-colors"
          :class="
            filters.subcategory === 'all'
              ? 'bg-indigo-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
          "
          @click="updateFilters({ subcategory: 'all' })"
        >
          All Subcategories
        </button>
        <button
          v-for="subcat in subcategories"
          :key="subcat"
          class="px-4 py-2 rounded-full text-sm font-medium transition-colors"
          :class="
            filters.subcategory === subcat
              ? 'bg-indigo-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
          "
          @click="updateFilters({ subcategory: subcat })"
        >
          {{ subcat }}
        </button>
      </div>

      <!-- Course Stats & Sort -->
      <div
        id="courses-top"
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6"
      >
        <div class="text-gray-600 mb-4 sm:mb-0">
          <strong>{{ filteredCourses.length }}</strong> courses in
          <strong>{{ category }}</strong>
          <span v-if="filters.subcategory !== 'all'">
            · {{ filters.subcategory }}</span
          >
        </div>

        <div class="flex items-center">
          <label for="sort-select" class="text-sm text-gray-600 mr-2"
            >Sort by:</label
          >
          <div class="relative">
            <select
              id="sort-select"
              v-model="activeSort"
              class="appearance-none bg-white border border-gray-300 rounded-lg py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="changeSort(activeSort)"
            >
              <option value="popular">Most Popular</option>
              <option value="highest_rated">Highest Rated</option>
              <option value="newest">Newest</option>
              <option value="price_low">Price: Low to High</option>
              <option value="price_high">Price: High to Low</option>
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

          <!-- Mobile filter toggle -->
          <button
            class="ml-4 sm:hidden bg-white border border-gray-300 rounded-lg p-2"
            aria-label="Show filters"
            @click="toggleMobileFilters"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-600"
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
          </button>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar filters - desktop -->
        <div class="hidden lg:block lg:w-1/4 xl:w-1/5">
          <div
            class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden"
          >
            <div
              class="p-4 border-b border-gray-200 flex justify-between items-center"
            >
              <h3 class="font-medium text-gray-900">Filters</h3>
              <button
                v-if="
                  filters.subcategory !== 'all' ||
                  filters.level !== 'all' ||
                  filters.price !== 'all' ||
                  filters.rating > 0
                "
                class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors"
                @click="resetFilters"
              >
                Clear All
              </button>
            </div>

            <!-- Filter sections -->
            <div class="p-4 space-y-6">
              <!-- Level filter -->
              <div>
                <h4 class="font-medium text-gray-900 mb-3">Level</h4>
                <div class="space-y-2">
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.level"
                      type="radio"
                      name="level"
                      value="all"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ level: filters.level })"
                    />
                    <span class="ml-2 text-gray-700">All Levels</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.level"
                      type="radio"
                      name="level"
                      value="Beginner"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ level: filters.level })"
                    />
                    <span class="ml-2 text-gray-700">Beginner</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.level"
                      type="radio"
                      name="level"
                      value="Intermediate"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ level: filters.level })"
                    />
                    <span class="ml-2 text-gray-700">Intermediate</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.level"
                      type="radio"
                      name="level"
                      value="Advanced"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ level: filters.level })"
                    />
                    <span class="ml-2 text-gray-700">Advanced</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.level"
                      type="radio"
                      name="level"
                      value="All Levels"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ level: filters.level })"
                    />
                    <span class="ml-2 text-gray-700">All Levels</span>
                  </label>
                </div>
              </div>

              <!-- Price filter -->
              <div>
                <h4 class="font-medium text-gray-900 mb-3">Price</h4>
                <div class="space-y-2">
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.price"
                      type="radio"
                      name="price"
                      value="all"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ price: filters.price })"
                    />
                    <span class="ml-2 text-gray-700">All Prices</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.price"
                      type="radio"
                      name="price"
                      value="free"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ price: filters.price })"
                    />
                    <span class="ml-2 text-gray-700">Free</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.price"
                      type="radio"
                      name="price"
                      value="under50"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ price: filters.price })"
                    />
                    <span class="ml-2 text-gray-700">Under $50</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.price"
                      type="radio"
                      name="price"
                      value="under100"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ price: filters.price })"
                    />
                    <span class="ml-2 text-gray-700">Under $100</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.price"
                      type="radio"
                      name="price"
                      value="over100"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ price: filters.price })"
                    />
                    <span class="ml-2 text-gray-700">$100 & Above</span>
                  </label>
                </div>
              </div>

              <!-- Rating filter -->
              <div>
                <h4 class="font-medium text-gray-900 mb-3">Rating</h4>
                <div class="space-y-2">
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.rating"
                      type="radio"
                      name="rating"
                      :value="0"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ rating: filters.rating })"
                    />
                    <span class="ml-2 text-gray-700">Any Rating</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.rating"
                      type="radio"
                      name="rating"
                      :value="4.5"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ rating: filters.rating })"
                    />
                    <div class="ml-2 flex items-center">
                      <div class="flex text-yellow-400">
                        <svg
                          v-for="i in 5"
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
                      </div>
                      <span class="ml-1 text-gray-700">4.5 & up</span>
                    </div>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.rating"
                      type="radio"
                      name="rating"
                      :value="4.0"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ rating: filters.rating })"
                    />
                    <div class="ml-2 flex items-center">
                      <div class="flex text-yellow-400">
                        <svg
                          v-for="i in 4"
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
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-gray-300"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                      <span class="ml-1 text-gray-700">4.0 & up</span>
                    </div>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.rating"
                      type="radio"
                      name="rating"
                      :value="3.5"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="updateFilters({ rating: filters.rating })"
                    />
                    <div class="ml-2 flex items-center">
                      <div class="flex text-yellow-400">
                        <svg
                          v-for="i in 3"
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
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-gray-300"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </div>
                      <span class="ml-1 text-gray-700">3.5 & up</span>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Instructor filter -->
              <div v-if="instructors.length > 0">
                <h4 class="font-medium text-gray-900 mb-3">Instructor</h4>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                  <label class="flex items-center cursor-pointer">
                    <input
                      v-model="filters.instructor"
                      type="radio"
                      name="instructor"
                      value="all"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="
                        updateFilters({ instructor: filters.instructor })
                      "
                    />
                    <span class="ml-2 text-gray-700">All Instructors</span>
                  </label>
                  <label
                    v-for="instructor in instructors"
                    :key="instructor as string"
                    class="flex items-center cursor-pointer"
                  >
                    <input
                      v-model="filters.instructor"
                      type="radio"
                      name="instructor"
                      :value="instructor"
                      class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      @change="
                        updateFilters({ instructor: filters.instructor })
                      "
                    />
                    <span class="ml-2 text-gray-700">{{ instructor }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile filters (slide-in panel) -->
        <div
          v-if="showMobileFilters"
          class="fixed inset-0 z-50 overflow-hidden lg:hidden"
        >
          <div
            class="absolute inset-0 bg-black bg-opacity-50"
            @click="toggleMobileFilters"
          />
          <div class="absolute inset-y-0 left-0 max-w-full flex">
            <div class="relative w-screen max-w-xs">
              <div
                class="h-full flex flex-col bg-white shadow-xl overflow-y-auto"
              >
                <!-- Mobile filter header -->
                <div
                  class="px-4 py-5 border-b border-gray-200 flex items-center justify-between"
                >
                  <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                  <button
                    class="text-gray-500 hover:text-gray-700"
                    @click="toggleMobileFilters"
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

                <!-- Mobile filters content -->
                <div class="p-4 space-y-6">
                  <!-- Subcategory filter - only on mobile -->
                  <div v-if="subcategories.length > 0">
                    <h4 class="font-medium text-gray-900 mb-3">Subcategory</h4>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.subcategory"
                          type="radio"
                          name="mobile-subcategory"
                          value="all"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="
                            updateFilters({ subcategory: filters.subcategory })
                          "
                        />
                        <span class="ml-2 text-gray-700"
                          >All Subcategories</span
                        >
                      </label>
                      <label
                        v-for="subcat in subcategories"
                        :key="`mobile-${subcat}`"
                        class="flex items-center cursor-pointer"
                      >
                        <input
                          v-model="filters.subcategory"
                          type="radio"
                          name="mobile-subcategory"
                          :value="subcat"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="
                            updateFilters({ subcategory: filters.subcategory })
                          "
                        />
                        <span class="ml-2 text-gray-700">{{ subcat }}</span>
                      </label>
                    </div>
                  </div>

                  <!-- Level filter -->
                  <div>
                    <h4 class="font-medium text-gray-900 mb-3">Level</h4>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.level"
                          type="radio"
                          name="mobile-level"
                          value="all"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ level: filters.level })"
                        />
                        <span class="ml-2 text-gray-700">All Levels</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.level"
                          type="radio"
                          name="mobile-level"
                          value="Beginner"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ level: filters.level })"
                        />
                        <span class="ml-2 text-gray-700">Beginner</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.level"
                          type="radio"
                          name="mobile-level"
                          value="Intermediate"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ level: filters.level })"
                        />
                        <span class="ml-2 text-gray-700">Intermediate</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.level"
                          type="radio"
                          name="mobile-level"
                          value="Advanced"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ level: filters.level })"
                        />
                        <span class="ml-2 text-gray-700">Advanced</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.level"
                          type="radio"
                          name="mobile-level"
                          value="All Levels"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ level: filters.level })"
                        />
                        <span class="ml-2 text-gray-700">All Levels</span>
                      </label>
                    </div>
                  </div>

                  <!-- Price filter -->
                  <div>
                    <h4 class="font-medium text-gray-900 mb-3">Price</h4>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.price"
                          type="radio"
                          name="mobile-price"
                          value="all"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ price: filters.price })"
                        />
                        <span class="ml-2 text-gray-700">All Prices</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.price"
                          type="radio"
                          name="mobile-price"
                          value="free"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ price: filters.price })"
                        />
                        <span class="ml-2 text-gray-700">Free</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.price"
                          type="radio"
                          name="mobile-price"
                          value="under50"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ price: filters.price })"
                        />
                        <span class="ml-2 text-gray-700">Under $50</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.price"
                          type="radio"
                          name="mobile-price"
                          value="under100"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ price: filters.price })"
                        />
                        <span class="ml-2 text-gray-700">Under $100</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.price"
                          type="radio"
                          name="mobile-price"
                          value="over100"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ price: filters.price })"
                        />
                        <span class="ml-2 text-gray-700">$100 & Above</span>
                      </label>
                    </div>
                  </div>

                  <!-- Rating filter -->
                  <div>
                    <h4 class="font-medium text-gray-900 mb-3">Rating</h4>
                    <div class="space-y-2">
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.rating"
                          type="radio"
                          name="mobile-rating"
                          :value="0"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ rating: filters.rating })"
                        />
                        <span class="ml-2 text-gray-700">Any Rating</span>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.rating"
                          type="radio"
                          name="mobile-rating"
                          :value="4.5"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ rating: filters.rating })"
                        />
                        <div class="ml-2 flex items-center">
                          <div class="flex text-yellow-400">
                            <svg
                              v-for="i in 5"
                              :key="`m-r45-${i}`"
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
                          <span class="ml-1 text-gray-700">4.5 & up</span>
                        </div>
                      </label>
                      <label class="flex items-center cursor-pointer">
                        <input
                          v-model="filters.rating"
                          type="radio"
                          name="mobile-rating"
                          :value="4.0"
                          class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                          @change="updateFilters({ rating: filters.rating })"
                        />
                        <div class="ml-2 flex items-center">
                          <div class="flex text-yellow-400">
                            <svg
                              v-for="i in 4"
                              :key="`m-r4-${i}`"
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
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4 text-gray-300"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                              />
                            </svg>
                          </div>
                          <span class="ml-1 text-gray-700">4.0 & up</span>
                        </div>
                      </label>
                    </div>
                  </div>

                  <!-- Action buttons -->
                  <div class="pt-4 border-t border-gray-200 flex space-x-3">
                    <button
                      class="flex-1 py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      @click="resetFilters"
                    >
                      Clear All
                    </button>
                    <button
                      class="flex-1 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      @click="toggleMobileFilters"
                    >
                      Apply
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Course cards grid -->
        <div class="lg:w-3/4 xl:w-4/5">
          <!-- Loading state -->
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
            />
          </div>

          <!-- Empty state -->
          <div
            v-else-if="paginatedCourses.length === 0"
            class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-16 w-16 text-gray-400 mx-auto mb-4"
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
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              No courses found
            </h3>
            <p class="text-gray-600 mb-4">
              Try adjusting your filters or search criteria
            </p>
            <button
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click="resetFilters"
            >
              Clear filters
            </button>
          </div>

          <!-- Course grid -->
          <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
          >
            <div
              v-for="course in paginatedCourses"
              :key="course.id"
              class="group bg-white rounded-lg shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md transform hover:-translate-y-1"
            >
              <!-- Course card header -->
              <div class="relative overflow-hidden rounded-t-lg">
                <img
                  src=""
                  :alt="course.title"
                  class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                />

                <!-- Badges -->
                <div class="absolute top-0 left-0 p-3 flex flex-wrap gap-2">
                  <span
                    v-for="(badge, index) in course.badges"
                    :key="index"
                    class="inline-block text-xs font-bold py-1 px-2 rounded text-center bg-yellow-100 text-yellow-800"
                  >
                    {{ badge }}
                  </span>
                </div>

                <!-- Category badge -->
                <div class="absolute top-0 right-0 p-3">
                  <span
                    class="inline-block bg-indigo-600 text-white text-xs font-bold py-1 px-2 rounded"
                  >
                    {{ course.category }}
                  </span>
                </div>
              </div>

              <!-- Course content -->
              <div class="p-5">
                <!-- Level and duration -->
                <div
                  class="flex items-center justify-between mb-2 text-xs font-medium text-gray-500"
                >
                  <span
                    class="px-2 py-1 rounded-full"
                    :class="{
                      'bg-green-100 text-green-800':
                        course.level === 'Beginner',
                      'bg-blue-100 text-blue-800':
                        course.level === 'Intermediate',
                      'bg-purple-100 text-purple-800':
                        course.level === 'Advanced',
                      'bg-gray-100 text-gray-800':
                        course.level === 'All Levels',
                    }"
                  >
                    {{ course.level }}
                  </span>
                  <span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 inline-block mr-1"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    {{ course.duration }}
                  </span>
                </div>

                <!-- Course title -->
                <h3
                  class="text-lg font-bold mb-3 leading-tight line-clamp-2 group-hover:text-indigo-600 transition-colors"
                >
                  <NuxtLink :to="`/courses/${course.id}`">
                    {{ course.title }}
                  </NuxtLink>
                </h3>

                <!-- Course description (truncated) -->
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                  {{ course.description }}
                </p>

                <!-- Ratings -->
                <div class="flex items-center mb-4">
                  <div class="flex text-yellow-400 mr-1">
                    <svg
                      v-for="i in Math.floor(course.rating)"
                      :key="`star-${course.id}-${i}`"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>

                    <!-- Half star -->
                    <svg
                      v-if="course.rating % 1 >= 0.5"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      viewBox="0 0 20 20"
                    >
                      <defs>
                        <linearGradient id="halfStar">
                          <stop offset="50%" stop-color="currentColor" />
                          <stop offset="50%" stop-color="#D1D5DB" />
                        </linearGradient>
                      </defs>
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        fill="url(#halfStar)"
                      />
                    </svg>

                    <!-- Empty stars -->
                    <svg
                      v-for="i in Math.floor(5 - course.rating)"
                      :key="`empty-${course.id}-${i}`"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 text-gray-300"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                  <span class="text-gray-600 text-xs">
                    {{ course.rating.toFixed(1) }} ({{
                      course.reviewCount.toLocaleString()
                    }}
                    reviews)
                  </span>
                </div>

                <!-- Instructor -->
                <div class="text-gray-600 text-sm mb-4">
                  <span class="font-medium">Instructor:</span>
                  {{ course.instructor }}
                </div>

                <!-- Price and action -->
                <div class="border-t pt-4 flex justify-between items-center">
                  <div>
                    <span
                      v-if="
                        course.originalPrice &&
                        course.originalPrice > course.price
                      "
                      class="text-gray-500 text-sm line-through mr-2"
                    >
                      ${{ course.originalPrice.toFixed(2) }}
                    </span>
                    <span class="text-2xl font-bold text-indigo-600">
                      ${{ course.price.toFixed(2) }}
                    </span>
                  </div>

                  <NuxtLink
                    :to="`/courses/${course.id}`"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors"
                  >
                    View Course
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8 flex justify-center">
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <!-- Previous page -->
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
                  :key="`full-${page}`"
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  :class="[
                    currentPage === page
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  ]"
                  @click="goToPage(page)"
                >
                  {{ page }}
                </button>
              </template>

              <template v-else>
                <!-- First page -->
                <button
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  :class="[
                    currentPage === 1
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
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
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="[
                      currentPage === page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
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
                  class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                  :class="[
                    currentPage === totalPages
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  ]"
                  @click="goToPage(totalPages)"
                >
                  {{ totalPages }}
                </button>
              </template>

              <!-- Next page -->
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
      </div>
    </div>

    <!-- Newsletter section -->
    <section class="bg-gray-100 py-12 mt-12">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-xl mx-auto text-center">
          <h2 class="text-2xl font-bold mb-4 text-gray-900">
            Get Web Development updates
          </h2>
          <p class="text-gray-600 mb-6">
            Sign up for our newsletter to receive the latest courses, tutorials,
            and industry news in
            {{ category }}.
          </p>

          <div class="flex flex-col sm:flex-row gap-2">
            <input
              type="email"
              placeholder="Enter your email address"
              class="flex-grow px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-gray-300"
            />
            <button
              class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors"
            >
              Subscribe
            </button>
          </div>

          <p class="text-gray-500 text-sm mt-4">
            We respect your privacy. Unsubscribe at any time.
          </p>
        </div>
      </div>
    </section>

    <!-- Related topics section -->
    <section class="py-12">
      <div class="container mx-auto px-4 md:px-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Related Topics</h2>

        <div
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
        >
          <NuxtLink
            v-for="(topic, index) in [
              'JavaScript',
              'CSS',
              'HTML',
              'React',
              'Vue.js',
              'Node.js',
              'TypeScript',
              'Angular',
              'GraphQL',
              'Responsive Design',
              'Web Security',
              'Performance',
            ]"
            :key="index"
            :to="`/courses?topic=${topic}`"
            class="bg-white border border-gray-200 rounded-lg py-3 px-4 text-center hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition-colors"
          >
            {{ topic }}
          </NuxtLink>
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

/* Custom form styles */
.form-radio {
  appearance: none;
  -webkit-appearance: none;
  border-radius: 50%;
  width: 1rem;
  height: 1rem;
  border: 1px solid #d1d5db;
  background-color: #fff;
  cursor: pointer;
}

.form-radio:checked {
  background-color: #4f46e5;
  border-color: #4f46e5;
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='4'/%3e%3c/svg%3e");
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
}

.form-radio:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
  border-color: #4f46e5;
}
</style>
