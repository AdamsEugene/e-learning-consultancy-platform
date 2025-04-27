<script lang="ts" setup>
interface Props {
  visibleSections: {
    features: boolean;
    courses: boolean;
    consultancy: boolean;
    testimonials: boolean;
    cta: boolean;
  };
}

defineProps<Props>();

const featuredCourses = ref([
  {
    id: 1,
    title: "Full-Stack Web Development with Vue & Node",
    category: "Web Development",
    price: 89.99,
    rating: 4.8,
    students: 2345,
    image: "/images/courses/c1.jpg",
    badges: ["Bestseller", "New"],
    level: "Intermediate",
    colorClasses: {
      primary: "from-indigo-500 to-indigo-600",
      secondary: "bg-indigo-100",
      text: "text-indigo-600",
      accent: "border-indigo-500",
    },
  },
  {
    id: 2,
    title: "UI/UX Design Masterclass",
    category: "Design",
    price: 79.99,
    rating: 4.7,
    students: 1823,
    image: "/images/courses/c2.jpg",
    badges: ["Top Rated"],
    level: "All Levels",
    colorClasses: {
      primary: "from-purple-500 to-purple-600",
      secondary: "bg-purple-100",
      text: "text-purple-600",
      accent: "border-purple-500",
    },
  },
  {
    id: 3,
    title: "Cloud Architecture & DevOps",
    category: "DevOps",
    price: 99.99,
    rating: 4.9,
    students: 1245,
    image: "/images/courses/c3.jpg",
    badges: ["Hot & New"],
    level: "Advanced",
    colorClasses: {
      primary: "from-blue-500 to-blue-600",
      secondary: "bg-blue-100",
      text: "text-blue-600",
      accent: "border-blue-500",
    },
  },
]);

// Badge configurations for cleaner code
const badgeColors: Record<string, string> = {
  Bestseller: "from-amber-500 to-amber-600",
  New: "from-green-500 to-green-600",
  "Top Rated": "from-purple-500 to-purple-600",
  "Hot & New": "from-red-500 to-red-600",
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    id="courses"
    class="py-20 px-4 relative overflow-hidden bg-gradient-to-b from-white to-gray-50"
  >
    <!-- Background decoration with animated particles -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Decorative blurred circles -->
      <div
        class="absolute -right-64 top-20 w-96 h-96 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-full opacity-30 blur-2xl"
      />
      <div
        class="absolute -left-32 bottom-20 w-80 h-80 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full opacity-30 blur-2xl"
      />

      <!-- Animated particles -->
      <div
        v-for="i in 10"
        :key="i"
        class="particle absolute rounded-full bg-white opacity-60"
      />

      <!-- Decorative grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
      <!-- Section Header with animated underline -->
      <div class="flex flex-col md:flex-row justify-between items-center mb-16">
        <div>
          <div class="mb-3">
            <span
              class="bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 text-sm font-bold px-4 py-1.5 rounded-full shadow-sm"
            >
              Popular Courses
            </span>
          </div>
          <h2
            class="text-3xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600"
          >
            Explore Our Courses
          </h2>

          <!-- Animated underline -->
          <div
            class="h-1 w-24 bg-gradient-to-r from-indigo-500 to-purple-500 mt-4 rounded-full relative overflow-hidden hidden md:block"
          >
            <div class="absolute inset-0 bg-white animate-shimmer" />
          </div>
        </div>

        <a
          href="/courses"
          class="text-indigo-600 font-semibold flex items-center hover:text-indigo-800 group mt-6 md:mt-0 bg-white py-2 px-4 rounded-lg shadow-sm hover:shadow-md transition-all"
        >
          View All Courses
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 ml-1 transform transition-transform group-hover:translate-x-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </a>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        <div
          v-for="(course, index) in featuredCourses"
          :key="course.id"
          class="course-card animate-fade-in"
          :style="`animation-delay: ${index * 200}ms`"
        >
          <div
            class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-500 border border-gray-100 h-full flex flex-col"
          >
            <!-- Course image with overlay -->
            <div class="relative h-48 overflow-hidden">
              <img
                :src="course.image"
                :alt="course.title"
                class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-700"
              />

              <!-- Gradient overlay -->
              <div
                class="absolute inset-0 bg-gradient-to-br opacity-60"
                :class="course.colorClasses.primary"
              />

              <!-- Badges and category -->
              <div
                class="absolute top-0 left-0 right-0 flex justify-between p-4"
              >
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="(badge, idx) in course.badges"
                    :key="idx"
                    class="inline-block text-xs font-bold py-1 px-2 rounded shadow-md bg-gradient-to-r text-white transform hover:scale-105 transition-transform"
                    :class="badgeColors[badge] || 'from-gray-500 to-gray-600'"
                  >
                    {{ badge }}
                  </span>
                </div>
                <div
                  class="bg-white text-gray-800 text-xs font-bold py-1 px-2 rounded-lg shadow-md transform hover:scale-105 transition-transform"
                >
                  {{ course.category }}
                </div>
              </div>

              <!-- Course difficulty level badge -->
              <div
                class="absolute bottom-4 left-4 bg-white bg-opacity-90 backdrop-blur-sm text-xs font-medium py-1 px-3 rounded-full flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  :class="course.colorClasses.text"
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
                <span>{{ course.level }}</span>
              </div>
            </div>

            <!-- Course content -->
            <div class="p-6 flex-grow flex flex-col">
              <h3
                class="text-xl text-black font-bold mb-3 line-clamp-2 hover:text-indigo-600 hover:cursor-default transition-colors group-hover:text-transparent bg-clip-text bg-gradient-to-r"
                :class="course.colorClasses.primary"
              >
                {{ course.title }}
              </h3>

              <!-- Rating and students -->
              <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                  <svg
                    v-for="i in 5"
                    :key="i"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    :class="{ 'text-gray-300': i > Math.floor(course.rating) }"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                </div>
                <span class="text-gray-600 ml-2 text-sm">
                  <span class="font-semibold">{{ course.rating }}</span> ({{
                    course.students.toLocaleString()
                  }}
                  students)
                </span>
              </div>

              <!-- Spacer to push price/button to bottom -->
              <div class="flex-grow" />

              <!-- Price and enroll button -->
              <div class="flex justify-between items-center">
                <div class="flex flex-col">
                  <span class="text-gray-500 text-xs line-through"
                    >${{ (course.price * 1.3).toFixed(2) }}</span
                  >
                  <span
                    class="text-2xl font-bold"
                    :class="course.colorClasses.text"
                  >
                    ${{ course.price.toFixed(2) }}
                  </span>
                </div>
                <button
                  class="text-white py-2 px-4 rounded-lg transition-all transform hover:scale-105 hover:shadow-md bg-gradient-to-r"
                  :class="course.colorClasses.primary"
                >
                  Enroll Now
                </button>
              </div>
            </div>

            <!-- Progress indicator at the bottom -->
            <div class="h-1 w-full bg-gray-100">
              <div
                class="h-full rounded-r-full bg-gradient-to-r w-3/4 transition-all duration-1000 ease-out"
                :class="course.colorClasses.primary"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- CTA Button -->
      <div class="mt-16 text-center">
        <div
          class="inline-block transform transition-transform hover:scale-105"
        >
          <a
            href="/courses"
            class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 px-10 rounded-xl hover:shadow-lg transition-all relative overflow-hidden group"
          >
            <span class="relative z-10">Browse All Courses</span>

            <!-- Animated shine effect -->
            <div
              class="absolute top-0 left-0 w-full h-full bg-white opacity-0 group-hover:opacity-20 transform -translate-x-full group-hover:translate-x-full transition-all duration-700"
            />
          </a>
        </div>
      </div>

      <!-- Course count indicator -->
      <div class="mt-8 text-center text-gray-600">
        <span class="text-lg"
          ><span class="font-semibold text-indigo-600">150+</span> courses
          available in our catalog</span
        >
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Grid pattern background */
.bg-grid-pattern {
  background-image: linear-gradient(
      to right,
      rgba(0, 0, 0, 0.05) 1px,
      transparent 1px
    ),
    linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
  background-size: 20px 20px;
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

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  20%,
  80% {
    opacity: 0.5;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

@keyframes particle-float {
  0%,
  100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(0) translateX(15px);
  }
  75% {
    transform: translateY(20px) translateX(5px);
  }
}

.animate-fade-in {
  animation: fadeInUp 1s forwards;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

/* Particle styling */
.particle {
  width: 4px;
  height: 4px;
  animation: particle-float calc(15s + (var(--random) * 10s)) infinite linear;
}

.particle:nth-child(1) {
  --random: 0.1;
  left: 10%;
  top: 20%;
}
.particle:nth-child(2) {
  --random: 0.2;
  left: 20%;
  top: 80%;
}
.particle:nth-child(3) {
  --random: 0.3;
  left: 30%;
  top: 30%;
}
.particle:nth-child(4) {
  --random: 0.4;
  left: 40%;
  top: 70%;
}
.particle:nth-child(5) {
  --random: 0.5;
  left: 50%;
  top: 40%;
}
.particle:nth-child(6) {
  --random: 0.6;
  left: 60%;
  top: 90%;
}
.particle:nth-child(7) {
  --random: 0.7;
  left: 70%;
  top: 10%;
}
.particle:nth-child(8) {
  --random: 0.8;
  left: 80%;
  top: 50%;
}
.particle:nth-child(9) {
  --random: 0.9;
  left: 90%;
  top: 30%;
}
.particle:nth-child(10) {
  --random: 1;
  left: 95%;
  top: 60%;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
