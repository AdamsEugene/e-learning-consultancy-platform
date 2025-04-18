<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Help & Support - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Get help and support for LearnConsult's e-learning and consultancy platform. Find resources, contact support, and access troubleshooting guides.",
    },
  ],
});

// Support categories
const supportCategories = [
  {
    title: "Getting Started",
    description:
      "New to LearnConsult? Find resources to help you get started with our platform.",
    icon: "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
    link: "/help/getting-started",
  },
  {
    title: "Account & Profile",
    description:
      "Learn how to manage your account settings, update your profile, and control your privacy.",
    icon: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
    link: "/help/account",
  },
  {
    title: "Courses & Learning",
    description:
      "Find information about enrolling in courses, tracking progress, and accessing course materials.",
    icon: "M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253",
    link: "/help/courses",
  },
  {
    title: "Consultancy Services",
    description:
      "Learn about our consultancy services, how to request them, and what to expect.",
    icon: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
    link: "/help/consultancy",
  },
  {
    title: "Technical Issues",
    description:
      "Troubleshoot common technical problems and find solutions to platform issues.",
    icon: "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z",
    link: "/help/technical",
  },
  {
    title: "Billing & Payments",
    description:
      "Information about payment methods, refunds, and managing your subscription.",
    icon: "M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z",
    link: "/help/billing",
  },
];

// Popular articles
const popularArticles = [
  {
    title: "How to reset your password",
    link: "/help/articles/reset-password",
  },
  {
    title: "Troubleshooting video playback issues",
    link: "/help/articles/video-playback",
  },
  {
    title: "Understanding your course progress",
    link: "/help/articles/course-progress",
  },
  {
    title: "How to download course materials",
    link: "/help/articles/download-materials",
  },
  {
    title: "Requesting a refund",
    link: "/help/articles/refund-policy",
  },
];
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Hero Section -->
      <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
          How can we help you?
        </h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
          Find answers to your questions and get support for LearnConsult's
          platform.
        </p>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
          <div class="relative">
            <input
              type="text"
              placeholder="Search for help articles..."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
            />
            <button
              class="absolute right-3 top-3 text-gray-400 hover:text-gray-500"
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
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Support Categories -->
      <div class="mb-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Support Categories
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(category, index) in supportCategories"
            :key="index"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
          >
            <NuxtLink :to="category.link" class="block p-6">
              <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      :d="category.icon"
                    />
                  </svg>
                </div>
                <h3 class="ml-4 text-lg font-medium text-gray-900">
                  {{ category.title }}
                </h3>
              </div>
              <p class="text-gray-600">{{ category.description }}</p>
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Popular Articles -->
      <div class="mb-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Popular Help Articles
        </h2>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
          <ul class="divide-y divide-gray-200">
            <li v-for="(article, index) in popularArticles" :key="index">
              <NuxtLink
                :to="article.link"
                class="block px-6 py-4 hover:bg-gray-50"
              >
                <div class="flex items-center justify-between">
                  <span class="text-gray-900">{{ article.title }}</span>
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
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </div>
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <!-- Contact Support -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Still need help?
          </h2>
          <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            Our support team is available to help you with any questions or
            issues you may have. Contact us through one of the methods below.
          </p>
          <div
            class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4"
          >
            <a
              href="mailto:support@learnconsult.com"
              class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
              Email Support
            </a>
            <a
              href="tel:+11234567890"
              class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                />
              </svg>
              Call Support
            </a>
            <NuxtLink
              to="/contact"
              class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                />
              </svg>
              Contact Form
            </NuxtLink>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
