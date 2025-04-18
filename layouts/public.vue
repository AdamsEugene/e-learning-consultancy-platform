<script setup lang="ts">
import { ref, onMounted, computed, onBeforeUnmount, watch } from "vue";
import { useRoute } from "vue-router";

// Router and current route
const route = useRoute();
const currentRoute = ref(route.path);

// Mobile menu state
const isMobileMenuOpen = ref(false);
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  if (isMobileMenuOpen.value) {
    document.body.style.overflow = "hidden"; // Prevent scrolling when menu is open
  } else {
    document.body.style.overflow = ""; // Restore scrolling
  }
};

// Auto-close mobile menu when route changes
watch(
  () => route.path,
  (newPath) => {
    if (isMobileMenuOpen.value) {
      isMobileMenuOpen.value = false;
      document.body.style.overflow = "";
    }
    currentRoute.value = newPath;
  }
);

// Header scroll effect
const scrollY = ref(0);
const headerOpacity = computed(() => {
  if (scrollY.value < 100) return 0;
  if (scrollY.value > 200) return 1;
  return (scrollY.value - 100) / 100;
});

const updateScrollPosition = () => {
  scrollY.value = window.scrollY;
};

// Scroll to top function
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};

// Navigation links
const navLinks = [
  { to: "/", label: "Home" },
  { to: "/courses", label: "Courses" },
  { to: "/consultancy", label: "Consultancy" },
  { to: "/blog", label: "Blog" },
  { to: "/about", label: "About" },
];

// Footer sections
const footerSections = [
  {
    title: "Quick Links",
    links: [
      { label: "Home", to: "/" },
      { label: "Courses", to: "/courses" },
      { label: "Consultancy", to: "/consultancy" },
      { label: "Blog", to: "/blog" },
      { label: "About", to: "/about" },
    ],
  },
  {
    title: "Legal",
    links: [
      { label: "Privacy Policy", to: "/privacy" },
      { label: "Cookie Policy", to: "/cookie" },
      { label: "Terms of Service", to: "/terms" },
    ],
  },
  {
    title: "Support",
    links: [
      { label: "Help Center", to: "/help" },
      { label: "FAQ", to: "/faq" },
      { label: "Contact", to: "/contact" },
    ],
  },
];

// Lifecycle hooks
onMounted(() => {
  window.addEventListener("scroll", updateScrollPosition);
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", updateScrollPosition);
});
</script>

<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Main Header -->
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

          <!-- Desktop Navigation -->
          <nav class="hidden md:flex space-x-8">
            <NuxtLink
              v-for="link in navLinks"
              :key="link.to"
              :to="link.to"
              class="text-gray-500 hover:text-gray-900"
              :class="{
                'text-indigo-600 hover:text-indigo-800':
                  currentRoute === link.to,
              }"
            >
              {{ link.label }}
            </NuxtLink>
            <NuxtLink to="/auth/login" class="text-gray-500 hover:text-gray-900"
              >Login</NuxtLink
            >
            <NuxtLink
              to="/auth/register"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
              >Sign Up</NuxtLink
            >
          </nav>

          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button
              @click="toggleMobileMenu"
              class="text-gray-500 hover:text-gray-900 focus:outline-none"
              aria-label="Toggle menu"
            >
              <svg
                v-if="!isMobileMenuOpen"
                class="h-6 w-6"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
              <svg
                v-else
                class="h-6 w-6"
                xmlns="http://www.w3.org/2000/svg"
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
        </div>
      </div>

      <!-- Mobile menu -->
      <div
        v-if="isMobileMenuOpen"
        class="md:hidden bg-white border-t border-gray-200"
      >
        <div class="px-2 pt-2 pb-3 space-y-1">
          <NuxtLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="block px-3 py-2 rounded-md text-base font-medium"
            :class="
              currentRoute === link.to
                ? 'text-indigo-600 bg-indigo-50'
                : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50'
            "
            @click="toggleMobileMenu"
          >
            {{ link.label }}
          </NuxtLink>
          <NuxtLink
            to="/auth/login"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50"
            @click="toggleMobileMenu"
          >
            Login
          </NuxtLink>
          <NuxtLink
            to="/auth/register"
            class="block px-3 py-2 rounded-md text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700"
            @click="toggleMobileMenu"
          >
            Sign Up
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Floating header that appears on scroll -->
    <header
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
      :class="[
        'bg-white/90 backdrop-blur-md',
        headerOpacity > 0 ? 'shadow-lg' : '',
      ]"
      :style="{
        opacity: headerOpacity,
        transform: headerOpacity === 0 ? 'translateY(-100%)' : 'translateY(0)',
      }"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-3">
          <div class="flex items-center">
            <NuxtLink to="/" class="flex items-center">
              <span class="text-xl font-bold text-indigo-600"
                >LearnConsult</span
              >
            </NuxtLink>
          </div>

          <!-- Desktop Navigation -->
          <nav class="hidden md:flex space-x-8">
            <NuxtLink
              v-for="link in navLinks"
              :key="link.to"
              :to="link.to"
              class="text-gray-500 hover:text-gray-900"
              :class="{
                'text-indigo-600 hover:text-indigo-800':
                  currentRoute === link.to,
              }"
            >
              {{ link.label }}
            </NuxtLink>
            <NuxtLink to="/auth/login" class="text-gray-500 hover:text-gray-900"
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

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
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
          <div v-for="section in footerSections" :key="section.title">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              {{ section.title }}
            </h3>
            <ul class="space-y-2">
              <li v-for="link in section.links" :key="link.to">
                <NuxtLink
                  :to="link.to"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                >
                  {{ link.label }}
                </NuxtLink>
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

    <!-- Scroll to top button -->
    <button
      v-if="scrollY > 500"
      @click="scrollToTop"
      class="fixed bottom-8 right-8 bg-indigo-600 text-white p-3 rounded-full shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 z-50"
      aria-label="Scroll to top"
    >
      <svg
        class="h-6 w-6"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M5 10l7-7m0 0l7 7m-7-7v18"
        />
      </svg>
    </button>
  </div>
</template>
