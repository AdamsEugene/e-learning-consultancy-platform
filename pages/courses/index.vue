<!-- pages/courses/index.vue -->
<script setup lang="ts">
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

// Filter state (shared with CourseFilters component)
const filters = ref({
  category: "all",
  instructor: "all",
  price: "all",
  level: "All Levels",
  rating: 0,
  duration: "any",
});

// Fetch data
const fetchCoursesData = async () => {
  loading.value = true;

  try {
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    courses.value = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        category: "Web Development",
        image: "/images/courses/c1.jpg",
        instructor: "Alex Johnson",
        rating: 4.8,
        reviewCount: 1245,
        price: 89.99,
        originalPrice: 199.99,
        level: "All Levels",
        duration: "42h 30m",
        badges: ["Bestseller", "Top Rated"],
      },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Functional JS",
        category: "JavaScript",
        image: "/images/courses/c2.jpg",
        instructor: "Sarah Chen",
        rating: 4.9,
        reviewCount: 982,
        price: 79.99,
        originalPrice: 149.99,
        level: "Intermediate",
        duration: "28h 15m",
        badges: ["Hot & New"],
      },
      {
        id: 3,
        title: "Flutter & Dart: The Complete Guide",
        category: "Mobile Development",
        image: "/images/courses/c3.jpg",
        instructor: "Mark Williams",
        rating: 4.7,
        reviewCount: 843,
        price: 94.99,
        originalPrice: 189.99,
        level: "Beginner",
        duration: "32h 45m",
        badges: ["Bestseller"],
      },
      {
        id: 4,
        title: "Machine Learning A-Z: Hands-On Python & R",
        category: "Data Science",
        image: "/images/courses/c1.jpg",
        instructor: "Jessica Taylor",
        rating: 4.6,
        reviewCount: 1532,
        price: 129.99,
        originalPrice: 199.99,
        level: "Advanced",
        duration: "45h 20m",
        badges: ["Highest Rated"],
      },
      {
        id: 5,
        title: "The Complete React Developer Course",
        category: "Web Development",
        image: "/images/courses/c2.jpg",
        instructor: "Michael Brown",
        rating: 4.8,
        reviewCount: 1089,
        price: 84.99,
        originalPrice: 169.99,
        level: "Intermediate",
        duration: "30h 10m",
        badges: ["Bestseller"],
      },
      {
        id: 6,
        title: "UX & UI Design Masterclass",
        category: "Design",
        image: "/images/courses/c3.jpg",
        instructor: "Emma Wilson",
        rating: 4.7,
        reviewCount: 756,
        price: 69.99,
        originalPrice: 139.99,
        level: "All Levels",
        duration: "24h 30m",
      },
      {
        id: 7,
        title: "AWS Certified Solutions Architect",
        category: "Cloud Computing",
        image: "/images/courses/c1.jpg",
        instructor: "David Miller",
        rating: 4.9,
        reviewCount: 1342,
        price: 119.99,
        originalPrice: 229.99,
        level: "Advanced",
        duration: "38h 15m",
        badges: ["Highest Rated", "Bestseller"],
      },
      {
        id: 8,
        title: "Python for Data Science and Machine Learning",
        category: "Data Science",
        image: "/images/courses/c2.jpg",
        instructor: "Rebecca Lee",
        rating: 4.8,
        reviewCount: 982,
        price: 89.99,
        originalPrice: 179.99,
        level: "Intermediate",
        duration: "36h 45m",
        badges: ["Top Rated"],
      },
      {
        id: 9,
        title: "The Complete Digital Marketing Course",
        category: "Marketing",
        image: "/images/courses/c3.jpg",
        instructor: "Thomas Clark",
        rating: 4.6,
        reviewCount: 1120,
        price: 94.99,
        originalPrice: 189.99,
        level: "Beginner",
        duration: "32h 15m",
      },
      {
        id: 10,
        title: "iOS App Development with Swift",
        category: "Mobile Development",
        image: "/images/courses/c1.jpg",
        instructor: "Jennifer Roberts",
        rating: 4.7,
        reviewCount: 876,
        price: 99.99,
        originalPrice: 199.99,
        level: "Intermediate",
        duration: "34h 20m",
      },
      {
        id: 11,
        title: "Advanced CSS and Sass: Flexbox, Grid, Animations",
        category: "Web Development",
        image: "/images/courses/c2.jpg",
        instructor: "Alex Johnson",
        rating: 4.9,
        reviewCount: 1245,
        price: 74.99,
        originalPrice: 149.99,
        level: "Intermediate",
        duration: "28h 30m",
        badges: ["Top Rated"],
      },
      {
        id: 12,
        title: "Full-Stack JavaScript: Node.js, Express, MongoDB",
        category: "Web Development",
        image: "/images/courses/c3.jpg",
        instructor: "Michael Brown",
        rating: 4.8,
        reviewCount: 932,
        price: 89.99,
        originalPrice: 179.99,
        level: "Intermediate",
        duration: "32h 45m",
        badges: ["Bestseller"],
      },
    ];

    // Set featured courses (would be from API in real app)
    featuredCourses.value = courses.value
      .filter(
        (course) =>
          course.badges?.includes("Bestseller") ||
          course.badges?.includes("Top Rated")
      )
      .slice(0, 4);

    // Set popular categories (would be from API in real app)
    popularCategories.value = [
      {
        name: "Web Development",
        count: 425,
        image: "/images/courses/c1.jpg",
      },
      {
        name: "Data Science",
        count: 320,
        image: "/images/courses/c3.jpg",
      },
      {
        name: "Mobile Development",
        count: 275,
        image: "/images/courses/c2.jpg",
      },
      {
        name: "Design",
        count: 245,
        image: "/images/courses/c1.jpg",
      },
      {
        name: "Marketing",
        count: 180,
        image: "/images/courses/c3.jpg",
      },
      {
        name: "Cloud Computing",
        count: 165,
        image: "/images/courses/c1.jpg",
      },
    ];
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

// Toggle mobile filters
const toggleMobileFilters = () => {
  showMobileFilters.value = !showMobileFilters.value;
};

// Filter courses based on current filters
const filteredCourses = computed(() => {
  let result = [...courses.value];

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
            <div class="relative">
              <input
                type="text"
                placeholder="Search for courses, skills, or topics..."
                class="w-full py-4 px-5 pr-12 rounded-lg border-none shadow-lg text-gray-900 focus:ring-2 focus:ring-indigo-400"
              />
              <button
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
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular categories section -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold mb-8 text-gray-900">
          Popular Categories
        </h2>

        <!-- Categories grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
          <NuxtLink
            v-for="category in popularCategories"
            :key="category.name"
            :to="`/courses/category/${encodeURIComponent(category.name)}`"
            class="group relative rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 bg-white"
          >
            <!-- Category image with overlay -->
            <div class="h-36 relative overflow-hidden">
              <img
                :src="category.image"
                :alt="category.name"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-300"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"
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
          <div class="hidden lg:block lg:w-1/4">
            <CoursesCourseFilters
              :categories="categories"
              :instructors="instructors"
              @update:filters="updateFilters"
              @clear="fetchCoursesData"
            />
          </div>

          <!-- Mobile filter toggle button -->
          <div class="lg:hidden mb-4">
            <button
              class="w-full flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition-colors"
              @click="toggleMobileFilters"
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
            </button>

            <!-- Mobile filters drawer -->
            <div
              v-if="showMobileFilters"
              class="fixed inset-0 z-50 overflow-hidden"
            >
              <div
                class="absolute inset-0 bg-black bg-opacity-50"
                @click="toggleMobileFilters"
              />
              <div class="absolute inset-y-0 left-0 max-w-full flex">
                <div class="relative w-screen max-w-md">
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
                    <div class="p-4">
                      <CourseFilters
                        :categories="categories"
                        :instructors="instructors"
                        @update:filters="updateFilters"
                        @clear="fetchCoursesData"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Course list -->
          <div class="lg:w-3/4">
            <CoursesCourseList
              :courses="filteredCourses"
              :loading="loading"
              title="All Courses"
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
            class="px-8 py-4 bg-white text-indigo-600 hover:bg-gray-100 rounded-lg font-bold transition-colors"
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
