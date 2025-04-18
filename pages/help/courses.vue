<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Courses & Learning - LearnConsult Help",
  meta: [
    {
      name: "description",
      content:
        "Learn how to browse, enroll in, and complete courses on LearnConsult. Get the most out of your learning experience.",
    },
  ],
});

// Course sections
const sections = [
  {
    title: "Finding Courses",
    content:
      "Browse our course catalog by visiting the Courses page. You can filter courses by category, level, duration, and price. Use the search bar to find specific courses by keyword.",
  },
  {
    title: "Course Enrollment",
    content:
      "To enroll in a course, click on the course card to view its details. Review the course description, curriculum, and instructor information. Click the 'Enroll Now' button to start the enrollment process.",
  },
  {
    title: "Course Progress",
    content:
      "Track your progress in the 'My Courses' section of your dashboard. Each course shows your completion percentage, current module, and time spent. You can resume learning from where you left off at any time.",
  },
  {
    title: "Learning Materials",
    content:
      "Course materials include video lessons, downloadable resources, quizzes, and assignments. Access these materials through the course curriculum page. Some materials may be available offline for download.",
  },
  {
    title: "Assignments & Projects",
    content:
      "Complete assignments and projects to reinforce your learning. Submit your work through the platform, and receive feedback from instructors. Some courses include peer review components.",
  },
  {
    title: "Certificates",
    content:
      "Upon completing a course, you'll receive a certificate of completion. Download your certificate from the course page or your dashboard. Certificates include a unique verification code.",
  },
  {
    title: "Course Discussions",
    content:
      "Engage with instructors and fellow learners through course discussions. Ask questions, share insights, and participate in group activities. Use the discussion forum to enhance your learning experience.",
  },
  {
    title: "Course Reviews",
    content:
      "After completing a course, you can leave a review and rating. Your feedback helps other learners make informed decisions. Reviews are visible on the course page and your profile.",
  },
];
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <NuxtLink to="/" class="flex items-center">
              <span class="text-2xl font-bold text-indigo-600"
                >LearnConsult</span
              >
            </NuxtLink>
          </div>
          <nav class="hidden md:flex space-x-8">
            <NuxtLink to="/" class="text-gray-500 hover:text-gray-900"
              >Home</NuxtLink
            >
            <NuxtLink to="/courses" class="text-gray-500 hover:text-gray-900"
              >Courses</NuxtLink
            >
            <NuxtLink
              to="/consultancy"
              class="text-gray-500 hover:text-gray-900"
              >Consultancy</NuxtLink
            >
            <NuxtLink
              to="/auth/login"
              class="text-indigo-600 hover:text-indigo-800"
              >Login</NuxtLink
            >
            <NuxtLink
              to="/auth/register"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
              >Sign Up</NuxtLink
            >
          </nav>
        </div>
      </div>
    </header>

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
          <li>
            <div>
              <NuxtLink to="/help" class="text-gray-400 hover:text-gray-500">
                <svg
                  class="flex-shrink-0 h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                  />
                </svg>
                <span class="sr-only">Help</span>
              </NuxtLink>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg
                class="flex-shrink-0 h-5 w-5 text-gray-300"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
              >
                <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
              </svg>
              <span class="ml-4 text-sm font-medium text-gray-500"
                >Courses & Learning</span
              >
            </div>
          </li>
        </ol>
      </nav>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h1 class="text-2xl font-bold text-gray-900">
            Courses & Learning Guide
          </h1>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Learn how to make the most of your learning experience on
            LearnConsult.
          </p>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
          <div class="prose max-w-none">
            <div v-for="(section, index) in sections" :key="index" class="mb-8">
              <h2 class="text-xl font-semibold text-gray-900 mb-2">
                {{ section.title }}
              </h2>
              <p class="text-gray-600">{{ section.content }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Articles -->
      <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <NuxtLink
            to="/help/articles/course-navigation"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Course Navigation</h3>
            <p class="text-gray-600 text-sm mt-1">
              How to navigate through course content and track your progress.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/assignments"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Submitting Assignments</h3>
            <p class="text-gray-600 text-sm mt-1">
              Step-by-step guide to submitting assignments and projects.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/certificates"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Course Certificates</h3>
            <p class="text-gray-600 text-sm mt-1">
              How to access and verify your course completion certificates.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/discussions"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Course Discussions</h3>
            <p class="text-gray-600 text-sm mt-1">
              Making the most of course discussions and community learning.
            </p>
          </NuxtLink>
        </div>
      </div>

      <!-- Still Need Help -->
      <div class="mt-12 bg-indigo-50 rounded-lg p-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Still need help?</h2>
        <p class="text-gray-600 mb-4">
          Our support team is available to assist you with any questions.
        </p>
        <div
          class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4"
        >
          <NuxtLink
            to="/help"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Return to Help Center
          </NuxtLink>
          <a
            href="mailto:support@learnconsult.com"
            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Contact Support
          </a>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              LearnConsult
            </h3>
            <p class="text-gray-500 text-sm">
              Empowering learners and organizations with quality education and
              expert consultancy services.
            </p>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Quick Links
            </h3>
            <ul class="space-y-2">
              <li>
                <NuxtLink
                  to="/"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Home</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/courses"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Courses</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/consultancy"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Consultancy</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/about"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >About Us</NuxtLink
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legal</h3>
            <ul class="space-y-2">
              <li>
                <NuxtLink
                  to="/privacy"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Privacy Policy</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/cookie"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Cookie Policy</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/terms"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Terms of Service</NuxtLink
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
            <ul class="space-y-2">
              <li class="text-gray-500 text-sm">
                Email: info@learnconsult.com
              </li>
              <li class="text-gray-500 text-sm">Phone: +1 (123) 456-7890</li>
              <li class="text-gray-500 text-sm">
                Address: 123 Learning Street, Education City, EC 12345
              </li>
            </ul>
          </div>
        </div>
        <div
          class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-500 text-sm"
        >
          &copy; {{ new Date().getFullYear() }} LearnConsult. All rights
          reserved.
        </div>
      </div>
    </footer>
  </div>
</template>
