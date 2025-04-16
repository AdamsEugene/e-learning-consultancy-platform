<!-- pages/dashboard/consultant/support/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Help & Support - Consultant Dashboard",
});

// Search functionality
const searchQuery = ref("");
const isSearching = ref(false);

// FAQ collapse state
const expandedFaq = ref<number | null>(null);
const toggleFaq = (id: number) => {
  expandedFaq.value = expandedFaq.value === id ? null : id;
};

// Active category for mobile view
const activeCategoryId = ref("getting-started");

// Contact form state
const contactForm = ref({
  subject: "",
  message: "",
  priority: "normal",
  attachScreenshot: false,
});

const showContactSuccess = ref(false);
const isSending = ref(false);

// Knowledge base categories
const categories = [
  {
    id: "getting-started",
    name: "Getting Started",
    description: "Learn the basics of using the platform",
    icon: "book-open",
  },
  {
    id: "client-management",
    name: "Client Management",
    description: "How to manage clients and projects",
    icon: "users",
  },
  {
    id: "billing",
    name: "Billing & Payments",
    description: "Payment processing and invoicing",
    icon: "credit-card",
  },
  {
    id: "scheduling",
    name: "Scheduling & Appointments",
    description: "Manage your calendar and appointments",
    icon: "calendar",
  },
  {
    id: "profile",
    name: "Profile & Settings",
    description: "Update your profile and settings",
    icon: "user-circle",
  },
  {
    id: "security",
    name: "Security & Privacy",
    description: "Account security and data privacy",
    icon: "shield-check",
  },
];

// Knowledge base articles
const articles = [
  {
    id: 1,
    title: "Getting Started as a Consultant",
    category: "getting-started",
    description:
      "Learn how to set up your consultant profile and start taking clients.",
    views: 1245,
    createdAt: "2025-01-15",
    isFeatured: true,
  },
  {
    id: 2,
    title: "Setting Up Your Availability Calendar",
    category: "scheduling",
    description:
      "Configure your availability to allow clients to book appointments with you.",
    views: 986,
    createdAt: "2025-01-18",
    isFeatured: true,
  },
  {
    id: 3,
    title: "Managing Client Projects",
    category: "client-management",
    description:
      "Learn how to create, organize, and track client projects effectively.",
    views: 872,
    createdAt: "2025-01-22",
    isFeatured: true,
  },
  {
    id: 4,
    title: "Creating and Sending Invoices",
    category: "billing",
    description: "How to create, customize, and send invoices to clients.",
    views: 756,
    createdAt: "2025-01-28",
    isFeatured: false,
  },
  {
    id: 5,
    title: "Setting Up Two-Factor Authentication",
    category: "security",
    description: "Secure your account with two-factor authentication.",
    views: 684,
    createdAt: "2025-02-02",
    isFeatured: false,
  },
  {
    id: 6,
    title: "Optimizing Your Consultant Profile",
    category: "profile",
    description: "Tips to make your profile stand out to potential clients.",
    views: 598,
    createdAt: "2025-02-05",
    isFeatured: false,
  },
  {
    id: 7,
    title: "Managing Client Communications",
    category: "client-management",
    description: "Best practices for effective client communication.",
    views: 542,
    createdAt: "2025-02-10",
    isFeatured: false,
  },
  {
    id: 8,
    title: "Setting Up Payment Methods",
    category: "billing",
    description: "Configure how you receive payments from clients.",
    views: 487,
    createdAt: "2025-02-12",
    isFeatured: false,
  },
  {
    id: 9,
    title: "Managing Appointment Notifications",
    category: "scheduling",
    description: "Customize how you receive appointment notifications.",
    views: 439,
    createdAt: "2025-02-15",
    isFeatured: false,
  },
  {
    id: 10,
    title: "Data Privacy and GDPR Compliance",
    category: "security",
    description: "Understanding how we handle your data and ensure compliance.",
    views: 412,
    createdAt: "2025-02-18",
    isFeatured: false,
  },
  {
    id: 11,
    title: "Creating and Sharing Project Proposals",
    category: "client-management",
    description:
      "How to draft, customize, and share project proposals with potential clients.",
    views: 387,
    createdAt: "2025-02-20",
    isFeatured: false,
  },
  {
    id: 12,
    title: "Integrating External Calendars",
    category: "scheduling",
    description:
      "Sync your platform calendar with Google Calendar, Outlook, or Apple Calendar.",
    views: 365,
    createdAt: "2025-02-22",
    isFeatured: false,
  },
];

// FAQs data
const faqs = [
  {
    id: 1,
    question: "How do I set up my availability for consultations?",
    answer:
      "You can set up your availability by going to Settings > Availability. From there, you can specify your working hours, buffer time between appointments, and mark specific days as unavailable. Clients will only be able to book appointments during your specified available hours.",
    category: "scheduling",
  },
  {
    id: 2,
    question: "How do payments work on the platform?",
    answer:
      "Our platform uses Stripe to process payments. When a client books a consultation or pays an invoice, the funds are deposited into your connected Stripe account. You can set up your payment details in Settings > Billing. Standard processing fees apply, and payouts are typically available within 2-3 business days.",
    category: "billing",
  },
  {
    id: 3,
    question: "What happens if a client cancels a meeting?",
    answer:
      "If a client cancels more than 24 hours before the scheduled meeting, they receive a full refund and you'll be notified. For cancellations within 24 hours, your cancellation policy applies (set in Settings > Appointments). You can choose to enforce a cancellation fee, which can be a percentage of the booking fee or a flat rate.",
    category: "scheduling",
  },
  {
    id: 4,
    question: "How do I integrate my calendar with external tools?",
    answer:
      "Go to Settings > Integrations to connect your Google Calendar, Outlook, or Apple Calendar. Once connected, your external appointments will be synced to prevent double-booking, and new appointments will be added to your external calendar. You can also connect video conferencing tools like Zoom or Microsoft Teams.",
    category: "scheduling",
  },
  {
    id: 5,
    question: "Can I offer packages of multiple sessions?",
    answer:
      "Yes, you can create service packages by going to Services > Create Package. You can bundle multiple sessions at a discounted rate and specify validity periods. Clients can purchase these packages and book individual sessions from their package allocation without paying again.",
    category: "billing",
  },
  {
    id: 6,
    question: "How do I customize my client intake forms?",
    answer:
      "Navigate to Settings > Client Intake to customize the information you collect from clients before they book with you. You can add custom fields, make fields required, and create different intake forms for different services to gather the most relevant information.",
    category: "client-management",
  },
  {
    id: 7,
    question: "What security measures are in place to protect my data?",
    answer:
      "We use industry-standard encryption for all data both in transit and at rest. Two-factor authentication is available and recommended. We maintain strict access controls and regular security audits. All personal and client information is protected according to GDPR, CCPA, and other relevant privacy regulations.",
    category: "security",
  },
  {
    id: 8,
    question: "How can I track my earnings and generate tax reports?",
    answer:
      "Go to Billing > Reports to view earnings breakdowns by client, service, or time period. You can generate and download tax-friendly reports for any date range. The platform keeps track of all transactions and can generate annual reports for tax purposes.",
    category: "billing",
  },
];

// Filtered articles based on search and category
const filteredArticles = computed(() => {
  let result = [...articles];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (article) =>
        article.title.toLowerCase().includes(query) ||
        article.description.toLowerCase().includes(query)
    );
  }

  return result;
});

// Get featured articles for featured section
const featuredArticles = computed(() => {
  return articles.filter((article) => article.isFeatured);
});

// Get articles by category
const getArticlesByCategory = (categoryId: string) => {
  return articles.filter((article) => article.category === categoryId);
};

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Get icon by name (for categories)
const getCategoryIcon = (iconName: string) => {
  switch (iconName) {
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>`;
    case "credit-card":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
      </svg>`;
    case "calendar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>`;
    case "user-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>`;
    case "shield-check":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>`;
  }
};

// Get FAQs for a specific category
const getFaqsByCategory = (categoryId: string) => {
  return faqs.filter((faq) => faq.category === categoryId);
};

// Handle contact form submission
const submitContactForm = async () => {
  // Validate the form
  if (!contactForm.value.subject || !contactForm.value.message) {
    return;
  }

  isSending.value = true;

  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 1500));

  // Show success message
  showContactSuccess.value = true;
  isSending.value = false;

  // Reset form
  contactForm.value = {
    subject: "",
    message: "",
    priority: "normal",
    attachScreenshot: false,
  };

  // Hide success message after a few seconds
  setTimeout(() => {
    showContactSuccess.value = false;
  }, 5000);
};

// Handle search
const handleSearch = () => {
  if (!searchQuery.value) return;

  isSearching.value = true;

  // Simulate search delay
  setTimeout(() => {
    isSearching.value = false;
  }, 500);
};

// On component mount
onMounted(() => {
  // Initialize any data that needs to be fetched
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Help & Support</h1>
      <p class="text-gray-600">Find answers, tutorials, and contact support</p>
    </div>

    <!-- Search Bar -->
    <div class="mb-8">
      <div class="relative max-w-2xl mx-auto">
        <input
          v-model="searchQuery"
          @keyup.enter="handleSearch"
          type="text"
          placeholder="Search for help articles, tutorials, FAQs..."
          class="w-full px-4 py-3 pl-12 pr-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        />
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-gray-400"
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
        </div>
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          class="absolute inset-y-0 right-0 pr-3 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-400 hover:text-gray-600"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Search Results -->
    <div v-if="searchQuery && !isSearching" class="mb-8">
      <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Search Results for "{{ searchQuery }}"
        </h2>

        <div v-if="filteredArticles.length === 0" class="text-center py-8">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 mx-auto text-gray-400 mb-4"
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
          <h3 class="text-lg font-medium text-gray-900 mb-1">
            No results found
          </h3>
          <p class="text-gray-600">
            Try a different search term or browse the categories below.
          </p>
        </div>

        <ul v-else class="divide-y divide-gray-200">
          <li
            v-for="article in filteredArticles"
            :key="article.id"
            class="py-4"
          >
            <a href="#" class="flex items-start">
              <div
                class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-md bg-indigo-100 text-indigo-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-4">
                <h3
                  class="text-base font-medium text-indigo-600 hover:text-indigo-800"
                >
                  {{ article.title }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                  {{ article.description }}
                </p>
                <div class="mt-2 flex items-center text-xs text-gray-500">
                  <span class="mr-2">{{ formatDate(article.createdAt) }}</span>
                  <span class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                      <path
                        fill-rule="evenodd"
                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ article.views }} views
                  </span>
                </div>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div
      v-if="!searchQuery || isSearching"
      class="grid grid-cols-1 lg:grid-cols-7 gap-8"
    >
      <!-- Knowledge Base Categories (Sidebar) -->
      <div class="lg:col-span-2">
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="px-4 py-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Knowledge Base</h2>
          </div>

          <!-- Categories Menu -->
          <nav class="flex flex-col">
            <a
              v-for="category in categories"
              :key="category.id"
              @click.prevent="activeCategoryId = category.id"
              href="#"
              class="flex items-center px-4 py-3 transition-colors duration-200"
              :class="[
                activeCategoryId === category.id
                  ? 'bg-indigo-50 text-indigo-600 border-l-4 border-indigo-600'
                  : 'text-gray-700 hover:bg-gray-50 border-l-4 border-transparent',
              ]"
            >
              <span
                class="text-current transition-colors duration-200"
                :class="
                  activeCategoryId === category.id
                    ? 'text-indigo-600'
                    : 'text-gray-500'
                "
                v-html="getCategoryIcon(category.icon)"
              ></span>
              <div class="ml-3">
                <span class="text-sm font-medium">{{ category.name }}</span>
                <p class="text-xs text-gray-500 mt-0.5">
                  {{ category.description }}
                </p>
              </div>
            </a>
          </nav>

          <!-- Quick Contact Section -->
          <div class="px-4 py-5 border-t border-gray-200">
            <h3 class="text-base font-medium text-gray-900 mb-3">
              Need more help?
            </h3>
            <a
              href="#contact-form"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"
                />
              </svg>
              Contact Support
            </a>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="lg:col-span-5 space-y-8">
        <!-- Featured Articles Section -->
        <div
          v-if="!searchQuery"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg overflow-hidden"
        >
          <div class="p-6 sm:p-8 text-white">
            <h2 class="text-xl font-bold mb-4">Featured Resources</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <a
                v-for="article in featuredArticles"
                :key="article.id"
                href="#"
                class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4 hover:bg-opacity-20 transition-colors duration-200"
              >
                <h3 class="font-medium text-white mb-2">{{ article.title }}</h3>
                <p class="text-white text-opacity-80 text-sm">
                  {{ article.description }}
                </p>
              </a>
            </div>
          </div>
        </div>

        <!-- Knowledge Base Content for Selected Category -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">
              {{ categories.find((c) => c.id === activeCategoryId)?.name }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
              {{
                categories.find((c) => c.id === activeCategoryId)?.description
              }}
            </p>
          </div>

          <!-- Articles for Selected Category -->
          <div class="p-6">
            <h3 class="text-base font-medium text-gray-900 mb-4">Articles</h3>
            <ul class="space-y-4">
              <li
                v-for="article in getArticlesByCategory(activeCategoryId)"
                :key="article.id"
              >
                <a href="#" class="flex items-start group">
                  <div
                    class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-md bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200 transition-colors duration-200"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                  <div class="ml-4">
                    <h4
                      class="text-base font-medium text-indigo-600 group-hover:text-indigo-800 transition-colors duration-200"
                    >
                      {{ article.title }}
                    </h4>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ article.description }}
                    </p>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                      <span class="mr-2">{{
                        formatDate(article.createdAt)
                      }}</span>
                      <span class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 mr-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                          <path
                            fill-rule="evenodd"
                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        {{ article.views }} views
                      </span>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </div>

          <!-- FAQs for Selected Category -->
          <div class="p-6 border-t border-gray-200 bg-gray-50">
            <h3 class="text-base font-medium text-gray-900 mb-4">
              Frequently Asked Questions
            </h3>

            <div class="space-y-4">
              <div
                v-for="faq in getFaqsByCategory(activeCategoryId)"
                :key="faq.id"
                class="border border-gray-200 rounded-lg overflow-hidden bg-white"
              >
                <button
                  @click="toggleFaq(faq.id)"
                  class="w-full flex justify-between items-center p-4 text-left focus:outline-none"
                >
                  <span class="text-sm font-medium text-gray-900">{{
                    faq.question
                  }}</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-500 transition-transform duration-200"
                    :class="{ 'transform rotate-180': expandedFaq === faq.id }"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
                <div
                  v-show="expandedFaq === faq.id"
                  class="p-4 pt-0 text-sm text-gray-600 border-t border-gray-200"
                >
                  {{ faq.answer }}
                </div>
              </div>

              <div
                v-if="getFaqsByCategory(activeCategoryId).length === 0"
                class="text-center py-4 text-sm text-gray-500"
              >
                No FAQs available for this category yet.
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Support Form -->
        <div
          id="contact-form"
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Contact Support</h2>
            <p class="text-sm text-gray-600 mt-1">
              Can't find what you're looking for? Send us a message.
            </p>
          </div>

          <div class="p-6">
            <form @submit.prevent="submitContactForm">
              <div class="space-y-5">
                <div>
                  <label
                    for="subject"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Subject</label
                  >
                  <input
                    id="subject"
                    v-model="contactForm.subject"
                    type="text"
                    placeholder="What's your question about?"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  />
                </div>

                <div>
                  <label
                    for="message"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Message</label
                  >
                  <textarea
                    id="message"
                    v-model="contactForm.message"
                    rows="4"
                    placeholder="Describe your issue in detail..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  ></textarea>
                </div>

                <div>
                  <label
                    for="priority"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Priority</label
                  >
                  <select
                    id="priority"
                    v-model="contactForm.priority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="low">
                      Low - General inquiry or feedback
                    </option>
                    <option value="normal">
                      Normal - I need help but it's not urgent
                    </option>
                    <option value="high">
                      High - This is affecting my work
                    </option>
                    <option value="urgent">Urgent - I can't work at all</option>
                  </select>
                </div>

                <div class="flex items-center">
                  <input
                    id="screenshot"
                    v-model="contactForm.attachScreenshot"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  />
                  <label for="screenshot" class="ml-2 text-sm text-gray-700"
                    >Include screenshot of my current page</label
                  >
                </div>

                <div class="flex justify-end">
                  <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :disabled="isSending"
                  >
                    <svg
                      v-if="isSending"
                      class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                      ></circle>
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                    {{ isSending ? "Sending..." : "Send Message" }}
                  </button>
                </div>

                <!-- Success Message -->
                <div
                  v-if="showContactSuccess"
                  class="rounded-md bg-green-50 p-4"
                >
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg
                        class="h-5 w-5 text-green-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-green-800">
                        Your message has been sent! We'll get back to you within
                        24 hours.
                      </p>
                    </div>
                    <div class="ml-auto pl-3">
                      <div class="-mx-1.5 -my-1.5">
                        <button
                          @click="showContactSuccess = false"
                          class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600"
                        >
                          <span class="sr-only">Dismiss</span>
                          <svg
                            class="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-enter-active {
  animation: fadeIn 0.3s ease-out;
}

/* Smooth scrolling for anchor links */
html {
  scroll-behavior: smooth;
}

/* Add padding to account for fixed header when scrolling to anchor points */
:target {
  scroll-margin-top: 100px;
}
</style>
