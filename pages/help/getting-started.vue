<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Getting Started - LearnConsult Help",
  meta: [
    {
      name: "description",
      content:
        "Learn how to get started with LearnConsult's e-learning and consultancy platform. Find guides for new users, platform overview, and first steps.",
    },
  ],
});

// Getting started sections
const sections = [
  {
    title: "Welcome to LearnConsult",
    content:
      "LearnConsult is an e-learning and consultancy platform designed to help individuals and organizations enhance their skills and knowledge. This guide will walk you through the basics of using our platform.",
  },
  {
    title: "Creating Your Account",
    content:
      "To get started, you'll need to create an account. Click the 'Sign Up' button in the top right corner of our website. You'll need to provide your name, email address, and create a password. Once registered, you can log in to access our courses and consultancy services.",
  },
  {
    title: "Platform Overview",
    content:
      "Our platform consists of two main areas: E-Learning and Consultancy Services. The E-Learning section offers courses across various disciplines, while the Consultancy section provides professional services for organizations.",
  },
  {
    title: "Navigating the Dashboard",
    content:
      "After logging in, you'll be taken to your personalized dashboard. Here you can see your enrolled courses, upcoming sessions, recent activity, and recommended content. The navigation menu on the left allows you to access different sections of the platform.",
  },
  {
    title: "Finding and Enrolling in Courses",
    content:
      "Browse our course catalog by clicking on 'Courses' in the main navigation. You can filter courses by category, level, or search for specific topics. Click on a course to view details and click 'Enroll' to join the course.",
  },
  {
    title: "Accessing Course Materials",
    content:
      "Once enrolled in a course, you can access all materials through your dashboard or the course page. Course content includes video lessons, downloadable resources, interactive exercises, and assessments.",
  },
  {
    title: "Requesting Consultancy Services",
    content:
      "If you're interested in our consultancy services, navigate to the 'Consultancy' section. Here you can learn about our services, view case studies, and submit a project request. Our team will review your request and get back to you within 2 business days.",
  },
  {
    title: "Getting Help",
    content:
      "If you need assistance, you can access our help resources through the 'Help' section. Here you'll find FAQs, guides, and contact information for our support team.",
  },
  {
    title: "Next Steps",
    content:
      "Now that you're familiar with the basics, we encourage you to explore our courses, connect with other learners, and take advantage of our learning resources. Check out our recommended courses for new users to get started on your learning journey.",
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
                >Getting Started</span
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
            Getting Started with LearnConsult
          </h1>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            A guide for new users to help you get the most out of our platform.
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
            to="/help/articles/platform-overview"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Platform Overview</h3>
            <p class="text-gray-600 text-sm mt-1">
              Learn about the different sections and features of our platform.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/first-course"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Your First Course</h3>
            <p class="text-gray-600 text-sm mt-1">
              Tips for getting the most out of your first course on
              LearnConsult.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/account-settings"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Account Settings</h3>
            <p class="text-gray-600 text-sm mt-1">
              How to manage your account settings and preferences.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/recommended-courses"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Recommended Courses</h3>
            <p class="text-gray-600 text-sm mt-1">
              Discover courses recommended for new users.
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
