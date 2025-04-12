<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- pages/courses/[id]/certificate.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import CertificateTemplate from "@/components/courses/CertificateTemplate.vue";

// Route params
const route = useRoute();
const router = useRouter();

const courseId = computed(() => route.params.id as string);

// State
const isLoading = ref(true);
const error = ref("");
const course = ref<any>(null);
const user = ref<any>(null);

// Certificate style and type
const certificateStyle = ref<"classic" | "modern" | "minimalist">("classic");
const certificateType = ref<"completion" | "achievement" | "excellence">(
  "completion"
);

// Fetch course and user data
const fetchData = async () => {
  isLoading.value = true;
  error.value = "";

  try {
    // In a real implementation, this would be an API call
    // For demo, we'll use mock data

    // Simulate API request delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock course data
    course.value = {
      id: courseId.value,
      title: "Complete Web Development Bootcamp",
      description: "Learn full-stack web development from scratch",
      completionDate: new Date(),
      instructor: {
        id: "instructor-1",
        name: "Sarah Johnson",
        title: "Senior Web Developer",
      },
    };

    // Mock user data
    user.value = {
      id: "user-1",
      name: "John Doe",
      email: "john.doe@example.com",
    };

    isLoading.value = false;
  } catch (err) {
    console.error("Error fetching data:", err);
    error.value = "Failed to load certificate data. Please try again.";
    isLoading.value = false;
  }
};

// Handle certificate download
const handleDownload = (format: "pdf" | "png") => {
  console.log(`Downloading certificate as ${format}...`);
  // In a real implementation, this would call an API to generate a PDF/PNG
};

// Handle certificate sharing
const handleShare = (
  platform: "linkedin" | "twitter" | "facebook" | "email"
) => {
  console.log(`Sharing certificate on ${platform}...`);
  // In a real implementation, this would integrate with social media APIs
};

// Return to course
const returnToCourse = () => {
  router.push(`/courses/${courseId.value}`);
};

// Change certificate style
const changeCertificateStyle = (style: "classic" | "modern" | "minimalist") => {
  certificateStyle.value = style;
};

// Lifecycle hooks
onMounted(() => {
  fetchData();
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="certificate-page min-h-screen bg-gray-100 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Back button -->
      <div class="mb-6">
        <button
          class="flex items-center text-gray-600 hover:text-indigo-600 transition-colors"
          @click="returnToCourse"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
              clip-rule="evenodd"
            />
          </svg>
          Back to course
        </button>
      </div>

      <!-- Page header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-2">Course Completion Certificate</h1>
        <p class="text-gray-600">
          Congratulations on completing the course! You can download, print, or
          share your certificate.
        </p>
      </div>

      <!-- Loading state -->
      <div v-if="isLoading" class="flex justify-center items-center my-16">
        <div
          class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
        />
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="flex justify-center items-center my-16">
        <div class="text-center p-8 max-w-md">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-red-500 mx-auto mb-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <h2 class="text-xl font-bold mb-2">{{ error }}</h2>
          <button
            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            @click="fetchData"
          >
            Retry
          </button>
        </div>
      </div>

      <!-- Certificate content -->
      <div v-else>
        <!-- Certificate style options -->
        <div class="mb-8 flex flex-wrap justify-center gap-4">
          <button
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              certificateStyle === 'classic'
                ? 'bg-amber-100 text-amber-800 border border-amber-200'
                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
            ]"
            @click="changeCertificateStyle('classic')"
          >
            Classic Style
          </button>

          <button
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              certificateStyle === 'modern'
                ? 'bg-indigo-100 text-indigo-800 border border-indigo-200'
                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
            ]"
            @click="changeCertificateStyle('modern')"
          >
            Modern Style
          </button>

          <button
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              certificateStyle === 'minimalist'
                ? 'bg-gray-100 text-gray-800 border border-gray-200'
                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
            ]"
            @click="changeCertificateStyle('minimalist')"
          >
            Minimalist Style
          </button>
        </div>

        <!-- Certificate display -->
        <div class="mb-8">
          <CertificateTemplate
            :student-name="user.name"
            :course-name="course.title"
            :instructor-name="course.instructor.name"
            :completion-date="course.completionDate"
            :certificate-type="certificateType"
            :certificate-style="certificateStyle"
            :is-preview="true"
            @download="handleDownload"
            @share="handleShare"
            @print="() => {}"
          />
        </div>

        <!-- Certificate badges -->
        <div class="mt-12 border-t border-gray-200 pt-12">
          <h2 class="text-2xl font-bold mb-6 text-center">
            Your Digital Badges
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <!-- Course completion badge -->
            <div
              class="bg-white p-6 rounded-lg shadow-md text-center transform transition-transform hover:scale-105"
            >
              <div class="w-32 h-32 mx-auto mb-4 relative">
                <svg
                  viewBox="0 0 24 24"
                  class="w-full h-full text-indigo-600"
                  fill="currentColor"
                >
                  <path
                    d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm0-15.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm-1 5.5h2v6h-2v-6z"
                  />
                </svg>
                <div
                  class="absolute inset-0 flex items-center justify-center text-white font-bold"
                >
                  <span class="text-sm">Web Dev</span>
                </div>
              </div>
              <h3 class="font-bold text-lg mb-2">Web Developer</h3>
              <p class="text-gray-600 text-sm">
                Awarded for completing the full web development course
              </p>
              <div class="mt-4">
                <button
                  class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
                >
                  Add to LinkedIn
                </button>
              </div>
            </div>

            <!-- HTML proficiency badge -->
            <div
              class="bg-white p-6 rounded-lg shadow-md text-center transform transition-transform hover:scale-105"
            >
              <div class="w-32 h-32 mx-auto mb-4 relative">
                <svg
                  viewBox="0 0 24 24"
                  class="w-full h-full text-orange-500"
                  fill="currentColor"
                >
                  <path
                    d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"
                  />
                </svg>
                <div
                  class="absolute inset-0 flex items-center justify-center text-white font-bold"
                >
                  <span class="text-sm">HTML5</span>
                </div>
              </div>
              <h3 class="font-bold text-lg mb-2">HTML Expert</h3>
              <p class="text-gray-600 text-sm">
                Awarded for mastering HTML5 fundamentals
              </p>
              <div class="mt-4">
                <button
                  class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
                >
                  Add to LinkedIn
                </button>
              </div>
            </div>

            <!-- Achievement badge -->
            <div
              class="bg-white p-6 rounded-lg shadow-md text-center transform transition-transform hover:scale-105"
            >
              <div class="w-32 h-32 mx-auto mb-4 relative opacity-40">
                <svg
                  viewBox="0 0 24 24"
                  class="w-full h-full text-blue-500"
                  fill="currentColor"
                >
                  <path
                    d="M12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28z"
                  />
                  <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"
                  />
                </svg>
                <div
                  class="absolute inset-0 flex items-center justify-center text-gray-600 font-bold"
                >
                  <span class="text-sm">CSS3</span>
                </div>
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-500">CSS Stylist</h3>
              <p class="text-gray-500 text-sm">
                Complete the CSS module to unlock this badge
              </p>
              <div class="mt-4">
                <button
                  class="text-gray-400 cursor-not-allowed text-sm font-medium"
                >
                  Locked
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Recommended next steps -->
        <div class="mt-12 border-t border-gray-200 pt-12">
          <h2 class="text-2xl font-bold mb-6 text-center">
            Continue Your Learning Journey
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Next course -->
            <div
              class="bg-white p-6 rounded-lg shadow-md border border-gray-100"
            >
              <h3 class="font-bold text-lg mb-2">Recommended Next Course</h3>
              <p class="text-gray-600 mb-4">
                Based on your interests and learning progress, we recommend:
              </p>
              <div class="border rounded-lg overflow-hidden">
                <img
                  src=""
                  alt="Advanced JavaScript"
                  class="w-full h-36 object-cover"
                />
                <div class="p-4">
                  <h4 class="font-bold">Advanced JavaScript & ES6</h4>
                  <p class="text-sm text-gray-600 mt-1">
                    Take your JavaScript skills to the next level
                  </p>
                  <button
                    class="mt-4 w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                  >
                    View Course
                  </button>
                </div>
              </div>
            </div>

            <!-- Learning path -->
            <div
              class="bg-white p-6 rounded-lg shadow-md border border-gray-100"
            >
              <h3 class="font-bold text-lg mb-2">Complete Learning Path</h3>
              <p class="text-gray-600 mb-4">
                Follow our curated learning path to become a full-stack
                developer:
              </p>
              <ul class="space-y-3">
                <li class="flex items-center text-gray-500">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2 text-green-500"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="line-through">Web Development Basics</span>
                </li>
                <li class="flex items-center font-medium text-indigo-600">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Advanced JavaScript & ES6
                </li>
                <li class="flex items-center text-gray-500">
                  <div
                    class="h-5 w-5 mr-2 border-2 border-gray-300 rounded-full"
                  />
                  Frontend Frameworks (React)
                </li>
                <li class="flex items-center text-gray-500">
                  <div
                    class="h-5 w-5 mr-2 border-2 border-gray-300 rounded-full"
                  />
                  Backend Development (Node.js)
                </li>
                <li class="flex items-center text-gray-500">
                  <div
                    class="h-5 w-5 mr-2 border-2 border-gray-300 rounded-full"
                  />
                  Database Design & Implementation
                </li>
              </ul>
              <button
                class="mt-6 w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
              >
                View Learning Path
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Certificate page specific styles */
.certificate-page {
  background-image: radial-gradient(
      circle at 20% 20%,
      rgba(236, 237, 255, 0.2) 0%,
      transparent 40%
    ),
    radial-gradient(
      circle at 80% 80%,
      rgba(236, 237, 255, 0.2) 0%,
      transparent 40%
    );
}
</style>
