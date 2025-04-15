<!-- layouts/default.vue -->
<script setup lang="ts">
import { ref, onMounted, computed, onBeforeUnmount, watch } from "vue";
import { useRoute } from "vue-router";

// Router and current route
const route = useRoute();
const currentRoute = ref(route.path);

// Theme toggling
const isDarkMode = ref(false);
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  localStorage.setItem("darkMode", isDarkMode.value.toString());
  applyThemeClass();
};

// Apply theme class to document
const applyThemeClass = () => {
  if (isDarkMode.value) {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
};

// Load theme preference from localStorage
const loadThemePreference = () => {
  const darkMode = localStorage.getItem("darkMode");
  isDarkMode.value = darkMode === "true";
  applyThemeClass();
};

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

// Header scroll effect - maintaining original behavior
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

// Navigation links with animation delay values
const navLinks = [
  { to: "/courses", label: "Courses", delay: "delay-75" },
  { to: "/consultancy", label: "Consultancy", delay: "delay-150" },
  { to: "/#testimonials", label: "Testimonials", delay: "delay-225" },
];

// Social media links with icons
const socialLinks = [
  { url: "#", icon: "twitter", label: "Twitter" },
  { url: "#", icon: "facebook", label: "Facebook" },
  { url: "#", icon: "instagram", label: "Instagram" },
  { url: "#", icon: "linkedin", label: "LinkedIn" },
];

// Footer sections
const footerSections = [
  {
    title: "Quick Links",
    links: [
      { label: "Courses", to: "/courses" },
      { label: "Consultancy", to: "/consultancy" },
      { label: "About Us", to: "/about" },
      { label: "Contact", to: "/contact" },
      { label: "Blog", to: "/blog" },
    ],
  },
  {
    title: "Support",
    links: [
      { label: "Help Center", to: "/help" },
      { label: "FAQs", to: "/faq" },
      { label: "Terms of Service", to: "/terms" },
      { label: "Privacy Policy", to: "/privacy" },
      { label: "Cookie Policy", to: "/cookie" },
    ],
  },
];

// Parallax effect for footer bubbles
const parallaxOffset = ref({ x: 0, y: 0 });
const updateParallax = (e: MouseEvent) => {
  const speed = 0.05;
  const x = (window.innerWidth / 2 - e.clientX) * speed;
  const y = (window.innerHeight / 2 - e.clientY) * speed;
  parallaxOffset.value = { x, y };
};

// Lifecycle hooks
onMounted(() => {
  window.addEventListener("scroll", updateScrollPosition);
  document.addEventListener("mousemove", updateParallax);
  loadThemePreference();
  updateScrollPosition(); // Initial check
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", updateScrollPosition);
  document.removeEventListener("mousemove", updateParallax);
  if (isMobileMenuOpen.value) {
    document.body.style.overflow = "";
  }
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    class="min-h-screen flex flex-col transition-colors duration-500"
    :class="
      isDarkMode ? 'dark bg-gray-900 text-gray-100' : 'bg-white text-gray-900'
    "
  >
    <!-- Floating header that appears on scroll - KEEPING ORIGINAL BEHAVIOR -->
    <header
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
      :class="[
        isDarkMode
          ? 'bg-gray-800/90 backdrop-blur-md'
          : 'bg-white/90 backdrop-blur-md',
        headerOpacity > 0 ? 'shadow-lg' : '',
      ]"
      :style="{
        opacity: headerOpacity,
        transform: headerOpacity === 0 ? 'translateY(-100%)' : 'translateY(0)',
      }"
    >
      <div
        class="container mx-auto px-4 py-3 flex justify-between items-center"
      >
        <!-- Logo with animation -->
        <div class="flex items-center">
          <NuxtLink
            to="/"
            class="flex items-center space-x-2 transition-all duration-300 transform hover:scale-105"
          >
            <!-- Animated logo icon -->
            <div
              class="w-8 h-8 rounded-lg flex items-center justify-center relative overflow-hidden"
              :class="
                isDarkMode
                  ? 'bg-gradient-to-br from-indigo-500 to-purple-600'
                  : 'bg-gradient-to-br from-indigo-600 to-purple-700'
              "
            >
              <div
                class="absolute inset-0 opacity-0 hover:opacity-100 transition-opacity duration-700 bg-gradient-to-tl from-blue-500 to-purple-500"
              />
              <span class="text-xl font-bold text-white relative z-10">L</span>
            </div>

            <span
              class="text-xl font-bold transition-colors duration-300"
              :class="isDarkMode ? 'text-white' : 'text-indigo-600'"
            >
              LearnConsult
            </span>
          </NuxtLink>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-8">
          <div
            v-for="(link, index) in navLinks"
            :key="index"
            class="relative overflow-hidden"
          >
            <NuxtLink
              :to="link.to"
              class="font-medium transition-all duration-300 group"
              :class="[
                currentRoute === link.to
                  ? isDarkMode
                    ? 'text-indigo-400'
                    : 'text-indigo-600'
                  : isDarkMode
                  ? 'text-gray-300 hover:text-white'
                  : 'text-gray-700 hover:text-indigo-600',
              ]"
            >
              {{ link.label }}
              <!-- Animated underline effect -->
              <span
                class="absolute bottom-0 left-0 w-full h-0.5 transform scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"
                :class="isDarkMode ? 'bg-indigo-400' : 'bg-indigo-600'"
              />
            </NuxtLink>
          </div>
        </nav>

        <!-- Authentication buttons with animations -->
        <div class="hidden md:flex space-x-4 items-center">
          <NuxtLink
            to="/auth/login"
            class="font-semibold transition-colors duration-300 relative group overflow-hidden"
            :class="
              isDarkMode
                ? 'text-white hover:text-indigo-300'
                : 'text-indigo-600 hover:text-indigo-800'
            "
          >
            <span class="relative z-10">Log in</span>
            <span
              class="absolute bottom-0 left-0 w-full h-0.5 transform scale-x-0 transition-transform duration-300 origin-left group-hover:scale-x-100"
              :class="isDarkMode ? 'bg-indigo-400' : 'bg-indigo-600'"
            />
          </NuxtLink>

          <NuxtLink
            to="/auth/register"
            class="px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg group"
            :class="
              isDarkMode
                ? 'bg-indigo-500 hover:bg-indigo-400 text-white'
                : 'bg-indigo-600 hover:bg-indigo-700 text-white'
            "
          >
            <span class="relative">
              <span class="relative z-10">Sign up</span>
              <!-- Shine effect -->
              <span
                class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 blur-sm transform -translate-x-full group-hover:translate-x-full transition-all duration-1000"
              />
            </span>
          </NuxtLink>
        </div>

        <!-- Mobile menu button -->
        <button
          class="md:hidden focus:outline-none"
          :class="isDarkMode ? 'text-white' : 'text-gray-700'"
          aria-label="Toggle menu"
          @click="toggleMobileMenu"
        >
          <svg
            v-if="!isMobileMenuOpen"
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
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
          <svg
            v-else
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
    </header>

    <!-- Mobile menu overlay -->
    <transition
      enter-active-class="transition-opacity duration-300"
      enter-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isMobileMenuOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="toggleMobileMenu"
      />
    </transition>

    <!-- Mobile menu drawer -->
    <transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-class="transform -translate-x-full"
      enter-to-class="transform translate-x-0"
      leave-active-class="transition-transform duration-300 ease-in"
      leave-class="transform translate-x-0"
      leave-to-class="transform -translate-x-full"
    >
      <div
        v-if="isMobileMenuOpen"
        class="fixed inset-y-0 left-0 w-5/6 max-w-xs z-50 overflow-y-auto transition-all"
        :class="
          isDarkMode ? 'bg-gray-800 text-white' : 'bg-white text-gray-900'
        "
      >
        <div class="p-6 space-y-6">
          <!-- Mobile menu header -->
          <div class="flex items-center justify-between">
            <NuxtLink
              to="/"
              class="flex items-center space-x-2"
              @click="toggleMobileMenu"
            >
              <div
                class="w-8 h-8 rounded-lg flex items-center justify-center"
                :class="
                  isDarkMode
                    ? 'bg-gradient-to-br from-indigo-500 to-purple-600'
                    : 'bg-gradient-to-br from-indigo-600 to-purple-700'
                "
              >
                <span class="text-xl font-bold text-white">L</span>
              </div>
              <span
                class="text-xl font-bold"
                :class="isDarkMode ? 'text-white' : 'text-indigo-600'"
              >
                LearnConsult
              </span>
            </NuxtLink>
            <button
              class="focus:outline-none"
              :class="isDarkMode ? 'text-white' : 'text-gray-700'"
              aria-label="Close menu"
              @click="toggleMobileMenu"
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

          <!-- Mobile navigation links -->
          <nav class="space-y-6">
            <div v-for="(link, index) in navLinks" :key="index" class="py-2">
              <NuxtLink
                :to="link.to"
                class="text-lg font-medium block w-full py-2 transition-colors duration-300"
                :class="[
                  currentRoute === link.to
                    ? isDarkMode
                      ? 'text-indigo-400'
                      : 'text-indigo-600'
                    : isDarkMode
                    ? 'text-gray-300 hover:text-white'
                    : 'text-gray-700 hover:text-indigo-600',
                ]"
                @click="toggleMobileMenu"
              >
                {{ link.label }}
              </NuxtLink>
            </div>

            <!-- Mobile menu auth buttons -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
              <NuxtLink
                to="/auth/login"
                class="block w-full text-center py-3 font-semibold rounded-lg mb-3 transition-colors duration-300"
                :class="
                  isDarkMode
                    ? 'text-white border border-gray-700 hover:bg-gray-700'
                    : 'text-indigo-600 border border-indigo-100 hover:bg-indigo-50'
                "
                @click="toggleMobileMenu"
              >
                Log in
              </NuxtLink>
              <NuxtLink
                to="/auth/register"
                class="block w-full text-center py-3 font-semibold rounded-lg text-white transition-colors duration-300"
                :class="
                  isDarkMode
                    ? 'bg-indigo-500 hover:bg-indigo-400'
                    : 'bg-indigo-600 hover:bg-indigo-700'
                "
                @click="toggleMobileMenu"
              >
                Sign up
              </NuxtLink>
            </div>

            <!-- Theme toggle in mobile menu -->
            <div class="pt-6 flex items-center justify-between">
              <span class="font-medium">Dark Mode</span>
              <button
                class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors duration-300"
                :class="isDarkMode ? 'bg-indigo-500' : 'bg-gray-300'"
                @click="toggleDarkMode"
              >
                <span
                  class="inline-block w-4 h-4 transform transition-transform duration-300 rounded-full bg-white"
                  :class="isDarkMode ? 'translate-x-6' : 'translate-x-1'"
                />
              </button>
            </div>
          </nav>
        </div>
      </div>
    </transition>

    <!-- Main content with page transition -->
    <transition
      enter-active-class="transition-opacity duration-500"
      enter-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-500"
      leave-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <main v-if="true" class="flex-grow">
        <slot />
      </main>
    </transition>

    <!-- Footer with enhanced animations -->
    <footer
      class="relative overflow-hidden pt-16 pb-8 px-4 transition-colors duration-500"
      :class="isDarkMode ? 'bg-gray-800 text-white' : 'bg-gray-900 text-white'"
    >
      <!-- Animated background elements -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <!-- Floating bubble decorations with parallax effect -->
        <div
          class="absolute -top-20 -right-20 w-64 h-64 rounded-full opacity-10 transition-transform duration-200"
          :class="isDarkMode ? 'bg-indigo-400' : 'bg-indigo-200'"
          :style="{
            transform: `translate(${parallaxOffset.x * -0.5}px, ${
              parallaxOffset.y * -0.5
            }px)`,
          }"
        />
        <div
          class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full opacity-10 transition-transform duration-200"
          :class="isDarkMode ? 'bg-purple-400' : 'bg-purple-200'"
          :style="{
            transform: `translate(${parallaxOffset.x * 0.3}px, ${
              parallaxOffset.y * 0.3
            }px)`,
          }"
        />
        <div
          class="absolute top-1/4 left-1/4 w-32 h-32 rounded-full opacity-5 transition-transform duration-200"
          :class="isDarkMode ? 'bg-blue-400' : 'bg-blue-200'"
          :style="{
            transform: `translate(${parallaxOffset.x * 0.7}px, ${
              parallaxOffset.y * 0.7
            }px)`,
          }"
        />
      </div>

      <div class="container mx-auto max-w-6xl relative z-10">
        <div class="grid gap-8 grid-cols-2 md:grid-cols-4 lg:grid-cols-5 mb-12">
          <!-- Logo and Social -->
          <div class="col-span-2 lg:col-span-2">
            <div class="font-bold text-2xl mb-4 relative">
              <!-- Gradient text effect -->
              <span
                class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-400"
              >
                LearnConsult
              </span>
              <!-- Animated underline -->
              <span
                class="absolute -bottom-1 left-0 w-16 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded"
              />
            </div>
            <p class="text-gray-400 mb-6 pr-4">
              Empowering professionals through expert-led courses and
              personalized consultancy services.
            </p>
            <div class="flex space-x-4">
              <a
                v-for="(social, index) in socialLinks"
                :key="index"
                :href="social.url"
                :aria-label="social.label"
                class="text-gray-400 hover:text-white transition-colors duration-300 transform hover:scale-110"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <!-- Twitter icon -->
                  <path
                    v-if="social.icon === 'twitter'"
                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                  />
                  <!-- Facebook icon -->
                  <path
                    v-if="social.icon === 'facebook'"
                    d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"
                  />
                  <!-- Instagram icon -->
                  <path
                    v-if="social.icon === 'instagram'"
                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                  />
                  <!-- LinkedIn icon -->
                  <path
                    v-if="social.icon === 'linkedin'"
                    d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"
                  />
                </svg>
              </a>
            </div>
          </div>

          <!-- Footer sections with staggered animations -->
          <div
            v-for="(section, sectionIndex) in footerSections"
            :key="sectionIndex"
          >
            <h4 class="font-bold text-lg mb-4 relative">
              {{ section.title }}
              <!-- Animated underline -->
              <span
                class="absolute -bottom-1 left-0 w-8 h-0.5 bg-indigo-500 rounded"
              />
            </h4>
            <ul class="space-y-2">
              <li
                v-for="(link, linkIndex) in section.links"
                :key="linkIndex"
                class="transform transition-transform hover:-translate-x-1 duration-300"
                :style="`transition-delay: ${linkIndex * 50}ms`"
              >
                <NuxtLink
                  :to="link.to"
                  class="text-gray-400 hover:text-white transition-colors duration-300 flex items-center group"
                >
                  <span
                    class="w-0 overflow-hidden transition-all duration-300 group-hover:w-2 group-hover:mr-1"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </span>
                  {{ link.label }}
                </NuxtLink>
              </li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="font-bold text-lg mb-4 relative">
              Contact Us
              <!-- Animated underline -->
              <span
                class="absolute -bottom-1 left-0 w-8 h-0.5 bg-indigo-500 rounded"
              />
            </h4>
            <ul class="space-y-4">
              <li
                class="text-gray-400 flex items-start hover:text-gray-300 transition-colors duration-300 group"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0 text-indigo-400 group-hover:text-indigo-300 transition-colors duration-300"
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
                <span class="hover:underline">support@learnconsult.com</span>
              </li>
              <li
                class="text-gray-400 flex items-start hover:text-gray-300 transition-colors duration-300 group"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0 text-indigo-400 group-hover:text-indigo-300 transition-colors duration-300"
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
                <span class="hover:underline">+1 (555) 123-4567</span>
              </li>
              <li
                class="text-gray-400 flex items-start hover:text-gray-300 transition-colors duration-300 group"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0 text-indigo-400 group-hover:text-indigo-300 transition-colors duration-300"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                <div>
                  <span class="hover:underline inline-block"
                    >123 Education St.</span
                  ><br />
                  <span class="hover:underline inline-block"
                    >San Francisco, CA 94105</span
                  >
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Animated divider -->
        <div class="relative h-px w-full my-8 overflow-hidden">
          <div
            class="absolute inset-0 bg-gradient-to-r from-transparent via-gray-400 to-transparent opacity-30"
          />
          <!-- Animated shine effect -->
          <div
            class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-white to-transparent opacity-30 animate-shine"
          />
        </div>

        <!-- Copyright -->
        <div class="text-center text-gray-400 text-sm">
          <p>
            &copy; {{ new Date().getFullYear() }} LearnConsult. All rights
            reserved.
          </p>
        </div>
      </div>

      <!-- Back to top button - functional with smooth scroll -->
      <button
        v-if="scrollY > 500"
        class="fixed bottom-6 right-6 p-3 rounded-full shadow-lg z-50 transition-all duration-300 transform hover:scale-110 text-white animate-bounce-subtle"
        :class="
          isDarkMode
            ? 'bg-indigo-600 hover:bg-indigo-500'
            : 'bg-indigo-600 hover:bg-indigo-500'
        "
        aria-label="Back to top"
        @click="scrollToTop"
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
            d="M5 10l7-7m0 0l7 7m-7-7v18"
          />
        </svg>
      </button>
    </footer>

    <!-- Support Chat Component -->
    <UiSupportChat
      brand-name="LearnConsult"
      agent-name="Alex from Support"
      agent-avatar="/images/instructors/i3.jpg"
      brand-color="purple"
      position="bottom-right"
      greeting-message="👋 Hi there! Need any help with your courses or consultancy?"
      response-time="We aim to respond within an hour"
    />
  </div>
</template>

<style scoped>
/* Custom animations */

/* Smooth scrolling for anchor links */
html {
  scroll-behavior: smooth;
}

/* Add padding to account for fixed header when scrolling to anchor points */
:target {
  scroll-margin-top: 70px;
}

/* Shine animation for footer divider */
@keyframes shine {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(200%);
  }
}

.animate-shine {
  animation: shine 3s infinite;
}

/* Subtle bounce animation for back-to-top button */
@keyframes bounce-subtle {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-bounce-subtle {
  animation: bounce-subtle 2s infinite ease-in-out;
}

/* Dark mode styles */
.dark {
  --tw-bg-opacity: 1;
  background-color: rgba(17, 24, 39, var(--tw-bg-opacity));
  --tw-text-opacity: 1;
  color: rgba(243, 244, 246, var(--tw-text-opacity));
}

/* Backdrop blur */
.backdrop-blur-md {
  backdrop-filter: blur(12px);
}

/* Logo hover effect */
.logo-hover:hover {
  filter: brightness(1.2);
  transform: scale(1.05);
}

/* Group hover effects for icons */
.group:hover .group-hover\:text-indigo-300 {
  --tw-text-opacity: 1;
  color: rgba(165, 180, 252, var(--tw-text-opacity));
}

/* Social icons hover effects */
.social-icon {
  transition: all 0.3s ease;
}

.social-icon:hover {
  transform: scale(1.2);
  filter: brightness(1.2);
}
</style>
