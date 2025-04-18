<script setup lang="ts">
definePageMeta({
  layout: "public",
  title: "Blog - LearnConsult",
  description:
    "Stay updated with the latest insights, trends, and news in e-learning and consultancy services.",
});

// Mock blog posts data
const featuredPost = {
  id: 1,
  title: "The Future of E-Learning: Trends to Watch in 2024",
  excerpt:
    "Explore the latest developments in e-learning technology and how they're shaping the future of education.",
  image: "/images/blog/featured-post.jpg",
  author: {
    name: "Sarah Johnson",
    avatar: "/images/authors/sarah.jpg",
    role: "Education Technology Expert",
  },
  date: "March 15, 2024",
  readTime: "8 min read",
  category: "Technology",
  slug: "future-of-e-learning-trends-2024",
};

const recentPosts = [
  {
    id: 2,
    title: "How to Choose the Right Online Course Platform",
    excerpt:
      "A comprehensive guide to selecting the perfect e-learning platform for your educational needs.",
    image: "/images/blog/course-platform.jpg",
    author: {
      name: "Michael Chen",
      avatar: "/images/authors/michael.jpg",
      role: "E-Learning Consultant",
    },
    date: "March 12, 2024",
    readTime: "6 min read",
    category: "Platform Selection",
    slug: "how-to-choose-online-course-platform",
  },
  {
    id: 3,
    title: "The Impact of AI on Education",
    excerpt:
      "Discover how artificial intelligence is revolutionizing the way we learn and teach.",
    image: "/images/blog/ai-education.jpg",
    author: {
      name: "Emily Rodriguez",
      avatar: "/images/authors/emily.jpg",
      role: "AI Education Specialist",
    },
    date: "March 10, 2024",
    readTime: "7 min read",
    category: "Technology",
    slug: "impact-of-ai-on-education",
  },
  {
    id: 4,
    title: "Best Practices for Remote Learning",
    excerpt:
      "Learn effective strategies for successful online education and remote learning.",
    image: "/images/blog/remote-learning.jpg",
    author: {
      name: "David Wilson",
      avatar: "/images/authors/david.jpg",
      role: "Educational Consultant",
    },
    date: "March 8, 2024",
    readTime: "5 min read",
    category: "Learning Strategies",
    slug: "best-practices-for-remote-learning",
  },
];

const categories = [
  { name: "Technology", count: 12, slug: "technology" },
  { name: "Learning Strategies", count: 8, slug: "learning-strategies" },
  { name: "Platform Selection", count: 6, slug: "platform-selection" },
  { name: "Industry News", count: 4, slug: "industry-news" },
  { name: "Case Studies", count: 3, slug: "case-studies" },
];

const popularTags = [
  { name: "E-Learning", slug: "e-learning" },
  { name: "Education Technology", slug: "education-technology" },
  { name: "Online Courses", slug: "online-courses" },
  { name: "Remote Learning", slug: "remote-learning" },
  { name: "Digital Education", slug: "digital-education" },
  { name: "Learning Management System", slug: "learning-management-system" },
  { name: "Educational Consulting", slug: "educational-consulting" },
  { name: "Professional Development", slug: "professional-development" },
  { name: "Corporate Training", slug: "corporate-training" },
  { name: "Student Success", slug: "student-success" },
];

// Search functionality
const searchQuery = ref("");
const handleSearch = (e: Event) => {
  e.preventDefault();
  // In a real application, this would filter posts or redirect to search results
  console.log("Searching for:", searchQuery.value);
};

// Newsletter signup
const email = ref("");
const isSubscribed = ref(false);
const handleSubscribe = (e: Event) => {
  e.preventDefault();
  // In a real application, this would submit to an API
  console.log("Subscribing email:", email.value);
  isSubscribed.value = true;
  email.value = "";
};

// Pagination
const currentPage = ref(1);
const postsPerPage = 3;
const totalPages = Math.ceil(recentPosts.length / postsPerPage);

const paginatedPosts = computed(() => {
  const start = (currentPage.value - 1) * postsPerPage;
  const end = start + postsPerPage;
  return recentPosts.slice(start, end);
});

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages) {
    currentPage.value = page;
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
};
</script>

<template>
  <div class="bg-white">
    <!-- Featured Post -->
    <div class="relative bg-gray-900">
      <div class="absolute inset-0">
        <img
          class="w-full h-full object-cover opacity-50"
          :src="featuredPost.image"
          :alt="featuredPost.title"
          @error="(e) => (e.target as HTMLImageElement).src = '/images/placeholder.jpg'"
        />
      </div>
      <div
        class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8"
      >
        <div class="max-w-3xl">
          <NuxtLink
            :to="`/blog/category/${featuredPost.category.toLowerCase()}`"
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700"
          >
            {{ featuredPost.category }}
          </NuxtLink>
          <NuxtLink :to="`/blog/${featuredPost.slug}`">
            <h1
              class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl hover:text-indigo-200 transition-colors"
            >
              {{ featuredPost.title }}
            </h1>
          </NuxtLink>
          <p class="mt-6 text-xl text-gray-300 max-w-3xl">
            {{ featuredPost.excerpt }}
          </p>
          <div class="mt-10 flex items-center">
            <div class="flex-shrink-0">
              <img
                class="h-12 w-12 rounded-full"
                :src="featuredPost.author.avatar"
                :alt="featuredPost.author.name"
                @error="(e) => (e.target as HTMLImageElement).src = '/images/avatar-placeholder.jpg'"
              />
            </div>
            <div class="ml-4">
              <div class="text-base font-medium text-white">
                {{ featuredPost.author.name }}
              </div>
              <div class="text-base text-gray-300">
                {{ featuredPost.date }} · {{ featuredPost.readTime }}
              </div>
            </div>
          </div>
          <div class="mt-6">
            <NuxtLink
              :to="`/blog/${featuredPost.slug}`"
              class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-indigo-600 bg-white hover:bg-indigo-50"
            >
              Read Article
              <svg
                class="ml-2 -mr-1 h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Posts -->
        <div class="lg:col-span-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-8">Recent Posts</h2>
          <div class="grid gap-8">
            <article
              v-for="post in paginatedPosts"
              :key="post.id"
              class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300"
            >
              <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3">
                  <NuxtLink :to="`/blog/${post.slug}`">
                    <img
                      class="h-48 w-full object-cover md:h-full"
                      :src="post.image"
                      :alt="post.title"
                      @error="(e) => (e.target as HTMLImageElement).src = '/images/placeholder.jpg'"
                    />
                  </NuxtLink>
                </div>
                <div class="p-6 md:w-2/3">
                  <div class="flex items-center text-sm text-gray-500 mb-2">
                    <NuxtLink
                      :to="`/blog/category/${post.category
                        .toLowerCase()
                        .replace(' ', '-')}`"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200"
                    >
                      {{ post.category }}
                    </NuxtLink>
                    <span class="mx-2">·</span>
                    <span>{{ post.date }}</span>
                    <span class="mx-2">·</span>
                    <span>{{ post.readTime }}</span>
                  </div>
                  <NuxtLink :to="`/blog/${post.slug}`">
                    <h3
                      class="text-xl font-semibold text-gray-900 mb-2 hover:text-indigo-600 transition-colors"
                    >
                      {{ post.title }}
                    </h3>
                  </NuxtLink>
                  <p class="text-gray-500 mb-4">
                    {{ post.excerpt }}
                  </p>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <img
                        class="h-10 w-10 rounded-full"
                        :src="post.author.avatar"
                        :alt="post.author.name"
                        @error="(e) => (e.target as HTMLImageElement).src = '/images/avatar-placeholder.jpg'"
                      />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                          {{ post.author.name }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ post.author.role }}
                        </p>
                      </div>
                    </div>
                    <NuxtLink
                      :to="`/blog/${post.slug}`"
                      class="text-indigo-600 hover:text-indigo-800 font-medium"
                    >
                      Read More
                    </NuxtLink>
                  </div>
                </div>
              </div>
            </article>
          </div>

          <!-- Pagination -->
          <div class="mt-8 flex justify-center">
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium"
                :class="
                  currentPage === 1
                    ? 'text-gray-300 cursor-not-allowed'
                    : 'text-gray-500 hover:bg-gray-50'
                "
              >
                <span class="sr-only">Previous</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <button
                v-for="page in totalPages"
                :key="page"
                @click="goToPage(page)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium"
                :class="
                  currentPage === page
                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                    : 'text-gray-500 hover:bg-gray-50'
                "
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium"
                :class="
                  currentPage === totalPages
                    ? 'text-gray-300 cursor-not-allowed'
                    : 'text-gray-500 hover:bg-gray-50'
                "
              >
                <span class="sr-only">Next</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
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

        <!-- Sidebar -->
        <div class="lg:col-span-4">
          <!-- Search -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Search</h3>
            <form @submit="handleSearch" class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search articles..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              />
              <button
                type="submit"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
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
            </form>
          </div>

          <!-- Categories -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
            <ul class="space-y-2">
              <li v-for="category in categories" :key="category.name">
                <NuxtLink
                  :to="`/blog/category/${category.slug}`"
                  class="flex items-center justify-between text-gray-600 hover:text-indigo-600"
                >
                  <span>{{ category.name }}</span>
                  <span
                    class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs"
                  >
                    {{ category.count }}
                  </span>
                </NuxtLink>
              </li>
            </ul>
          </div>

          <!-- Popular Tags -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Popular Tags
            </h3>
            <div class="flex flex-wrap gap-2">
              <NuxtLink
                v-for="tag in popularTags"
                :key="tag.name"
                :to="`/blog/tag/${tag.slug}`"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-indigo-100 hover:text-indigo-800"
              >
                {{ tag.name }}
              </NuxtLink>
            </div>
          </div>

          <!-- Newsletter Signup -->
          <div class="bg-indigo-50 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Subscribe to Our Newsletter
            </h3>
            <p class="text-gray-600 mb-4">
              Get the latest articles and insights delivered straight to your
              inbox.
            </p>
            <form
              v-if="!isSubscribed"
              @submit="handleSubscribe"
              class="space-y-4"
            >
              <input
                v-model="email"
                type="email"
                placeholder="Enter your email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              />
              <button
                type="submit"
                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Subscribe
              </button>
            </form>
            <div v-else class="text-center py-4">
              <svg
                class="mx-auto h-12 w-12 text-green-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                ></path>
              </svg>
              <p class="mt-2 text-sm text-gray-600">
                Thank you for subscribing to our newsletter!
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
