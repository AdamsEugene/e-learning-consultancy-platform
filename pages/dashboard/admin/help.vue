<!-- pages/dashboard/admin/help/index.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Define page meta for layout
definePageMeta({
  layout: "dashboard-admin",
});

// Set page title and meta
useHead({
  title: "Help & Resources - Admin Dashboard",
  meta: [
    {
      name: "description",
      content: "Administrative help, guides, and support resources",
    },
  ],
});

// Knowledge base categories
interface KnowledgeCategory {
  id: string;
  title: string;
  description: string;
  icon: string;
  articles: KnowledgeArticle[];
}

// Knowledge base article
interface KnowledgeArticle {
  id: string;
  title: string;
  summary: string;
  lastUpdated: string;
  popularity: number; // 1-5 scale
}

// Support ticket type
interface SupportTicket {
  id: string;
  status: "open" | "in-progress" | "resolved";
  subject: string;
  dateCreated: string;
  lastResponse: string;
  priority: "low" | "medium" | "high" | "critical";
}

// Search state
const searchQuery = ref("");
const isSearching = ref(false);
const searchResults = ref<KnowledgeArticle[]>([]);

// Active tab and currently viewed article
const activeTab = ref("knowledge-base");
const selectedCategory = ref<string | null>(null);
const selectedArticle = ref<KnowledgeArticle | null>(null);
const showFullArticle = ref(false);

// New support ticket form
const newTicket = ref({
  subject: "",
  priority: "medium",
  description: "",
  attachments: [] as File[],
});

// Simulate search functionality
const searchKnowledgeBase = () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    return;
  }

  isSearching.value = true;

  // Simulate API delay
  setTimeout(() => {
    const query = searchQuery.value.toLowerCase();

    // Search across all categories and articles
    searchResults.value = knowledgeBase.value
      .flatMap((category) => category.articles)
      .filter(
        (article) =>
          article.title.toLowerCase().includes(query) ||
          article.summary.toLowerCase().includes(query)
      );

    isSearching.value = false;
  }, 500);
};

// Knowledge base data
const knowledgeBase = ref<KnowledgeCategory[]>([
  {
    id: "general",
    title: "Platform Overview",
    description: "Learn about the platform architecture and key concepts",
    icon: "globe",
    articles: [
      {
        id: "platform-intro",
        title: "Introduction to LearnConsult Admin Panel",
        summary:
          "Get familiar with the admin dashboard and navigation structure. This guide provides an overview of all administrative functions available to platform managers and super admins.",
        lastUpdated: "2025-02-12",
        popularity: 5,
      },
      {
        id: "admin-roles",
        title: "Administrative Roles and Permissions",
        summary:
          "Understanding the different admin roles within the platform and the permissions associated with each role level.",
        lastUpdated: "2025-03-05",
        popularity: 4,
      },
      {
        id: "platform-architecture",
        title: "Platform Architecture Overview",
        summary:
          "Technical overview of the LearnConsult platform architecture, including server infrastructure, data models, and integration points.",
        lastUpdated: "2025-01-20",
        popularity: 3,
      },
    ],
  },
  {
    id: "user-management",
    title: "User Management",
    description: "Guides for managing users, permissions, and access control",
    icon: "users",
    articles: [
      {
        id: "create-user",
        title: "Creating and Managing User Accounts",
        summary:
          "Step-by-step guide for creating new user accounts, modifying user information, and managing account status across different user types.",
        lastUpdated: "2025-03-10",
        popularity: 5,
      },
      {
        id: "bulk-user-operations",
        title: "Bulk User Operations",
        summary:
          "Instructions for performing bulk operations on users, such as importing users, bulk email sending, and batch account modifications.",
        lastUpdated: "2025-02-28",
        popularity: 3,
      },
      {
        id: "user-verification",
        title: "User Verification Processes",
        summary:
          "Guidelines for verifying instructor and consultant accounts, including document verification and approval workflows.",
        lastUpdated: "2025-01-15",
        popularity: 4,
      },
    ],
  },
  {
    id: "content-management",
    title: "Content Management",
    description:
      "Documentation for managing courses, resources, and learning materials",
    icon: "book-open",
    articles: [
      {
        id: "course-approval",
        title: "Course Review and Approval Process",
        summary:
          "Detailed explanation of the course review criteria, approval workflow, and quality standards enforcement for the platform.",
        lastUpdated: "2025-03-15",
        popularity: 5,
      },
      {
        id: "content-moderation",
        title: "Content Moderation Guidelines",
        summary:
          "Standards and processes for moderating user-generated content, including forum posts, reviews, and comments.",
        lastUpdated: "2025-02-25",
        popularity: 4,
      },
      {
        id: "featured-content",
        title: "Managing Featured Content",
        summary:
          "Guide for selecting and promoting featured courses, instructors, and consultants across different platform sections.",
        lastUpdated: "2025-03-08",
        popularity: 3,
      },
    ],
  },
  {
    id: "financial",
    title: "Financial Management",
    description:
      "Guides for payments, refunds, revenue, and financial reporting",
    icon: "cash",
    articles: [
      {
        id: "payment-processing",
        title: "Payment Processing and Gateway Configuration",
        summary:
          "Configuration guide for payment gateways, transaction management, and payment reconciliation.",
        lastUpdated: "2025-02-18",
        popularity: 4,
      },
      {
        id: "refund-management",
        title: "Refund Policies and Processing",
        summary:
          "Procedures for handling refund requests, partial refunds, and dispute resolution.",
        lastUpdated: "2025-03-01",
        popularity: 5,
      },
      {
        id: "financial-reporting",
        title: "Financial Reporting and Revenue Analytics",
        summary:
          "Instructions for generating financial reports, analyzing revenue streams, and tracking platform performance metrics.",
        lastUpdated: "2025-02-10",
        popularity: 4,
      },
    ],
  },
  {
    id: "system",
    title: "System Settings",
    description:
      "Documentation for platform configuration, security, and technical settings",
    icon: "cog",
    articles: [
      {
        id: "site-configuration",
        title: "Platform Configuration Settings",
        summary:
          "Comprehensive guide to all configurable platform settings, including appearance, behavior, and feature toggles.",
        lastUpdated: "2025-03-12",
        popularity: 5,
      },
      {
        id: "security-settings",
        title: "Security Settings and Best Practices",
        summary:
          "Security configuration guidelines, including authentication requirements, password policies, and data protection measures.",
        lastUpdated: "2025-02-22",
        popularity: 5,
      },
      {
        id: "api-integration",
        title: "API Integration Management",
        summary:
          "Managing API keys, webhooks, and third-party integrations with the platform.",
        lastUpdated: "2025-01-30",
        popularity: 3,
      },
    ],
  },
]);

// Support tickets data (mock)
const supportTickets = ref<SupportTicket[]>([
  {
    id: "TICKET-1023",
    status: "open",
    subject: "User Import Tool Error",
    dateCreated: "2025-04-10T14:30:00Z",
    lastResponse: "2025-04-10T15:45:00Z",
    priority: "high",
  },
  {
    id: "TICKET-1022",
    status: "in-progress",
    subject: "Payment Gateway Integration Issue",
    dateCreated: "2025-04-09T10:15:00Z",
    lastResponse: "2025-04-15T09:20:00Z",
    priority: "critical",
  },
  {
    id: "TICKET-1020",
    status: "resolved",
    subject: "Course Approval Workflow Question",
    dateCreated: "2025-04-05T16:20:00Z",
    lastResponse: "2025-04-06T11:10:00Z",
    priority: "medium",
  },
]);

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Handle selecting a category
const selectCategory = (categoryId: string) => {
  if (selectedCategory.value === categoryId) {
    // Toggle off if already selected
    selectedCategory.value = null;
    selectedArticle.value = null;
    showFullArticle.value = false;
  } else {
    selectedCategory.value = categoryId;
    selectedArticle.value = null;
    showFullArticle.value = false;
  }
};

// Handle selecting an article
const selectArticle = (article: KnowledgeArticle) => {
  selectedArticle.value = article;
  showFullArticle.value = true;
};

// Get the currently selected category data
const currentCategory = computed(() => {
  if (!selectedCategory.value) return null;
  return (
    knowledgeBase.value.find((cat) => cat.id === selectedCategory.value) || null
  );
});

// Handle creating new support ticket
const submitTicket = () => {
  // In a real app, this would submit the ticket to an API
  alert(
    "Support ticket submitted successfully! Our team will contact you shortly."
  );

  // Reset form
  newTicket.value = {
    subject: "",
    priority: "medium",
    description: "",
    attachments: [],
  };
};

// Get priority badge class
const getPriorityClass = (priority: string) => {
  switch (priority) {
    case "low":
      return "bg-gray-100 text-gray-800";
    case "medium":
      return "bg-blue-100 text-blue-800";
    case "high":
      return "bg-amber-100 text-amber-800";
    case "critical":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get status badge class
const getStatusClass = (status: string) => {
  switch (status) {
    case "open":
      return "bg-green-100 text-green-800";
    case "in-progress":
      return "bg-blue-100 text-blue-800";
    case "resolved":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// File input reference
const fileInput = ref<HTMLInputElement | null>(null);
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    // Convert FileList to array and add to attachments
    const files = Array.from(input.files);
    newTicket.value.attachments = [...newTicket.value.attachments, ...files];
  }
};

// Remove attachment from list
const removeAttachment = (index: number) => {
  newTicket.value.attachments.splice(index, 1);
};

// Render the icon for a category
const renderCategoryIcon = (iconName: string) => {
  switch (iconName) {
    case "globe":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>`;
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>`;
    case "cash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>`;
    case "cog":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>`;
  }
};

// Handle tab change
const changeTab = (tab: string) => {
  activeTab.value = tab;
  selectedCategory.value = null;
  selectedArticle.value = null;
  showFullArticle.value = false;
  searchQuery.value = "";
  searchResults.value = [];
};

// Fake article content (for demo purposes)
const getArticleContent = (articleId: string) => {
  return `
    <h2 class="text-2xl font-bold mb-4">Comprehensive Guide</h2>
    <p class="mb-4">This comprehensive guide explains all the key aspects administrators need to know about this functionality. Our platform provides a wide range of tools to help you manage content, users, and system settings effectively.</p>
    
    <h3 class="text-xl font-bold mb-3 mt-6">Key Concepts</h3>
    <p class="mb-4">Understanding the fundamentals is essential for efficient platform administration. The key concepts include:</p>
    <ul class="list-disc pl-6 mb-4 space-y-2">
      <li>User roles and permissions management</li>
      <li>Content approval workflows</li>
      <li>System configuration options</li>
      <li>Financial transaction processing</li>
      <li>Reporting and analytics tools</li>
    </ul>
    
    <h3 class="text-xl font-bold mb-3 mt-6">Step-by-Step Instructions</h3>
    <p class="mb-4">Follow these steps to complete the process:</p>
    <ol class="list-decimal pl-6 mb-4 space-y-2">
      <li>Navigate to the appropriate section in the admin dashboard</li>
      <li>Select the item you wish to modify or review</li>
      <li>Make the necessary changes according to platform guidelines</li>
      <li>Save your changes and verify they appear correctly</li>
      <li>Document any significant modifications for other administrators</li>
    </ol>
    
    <h3 class="text-xl font-bold mb-3 mt-6">Best Practices</h3>
    <p class="mb-4">For optimal results, consider the following best practices:</p>
    <ul class="list-disc pl-6 mb-4 space-y-2">
      <li>Regularly review system logs for unusual activity</li>
      <li>Implement the principle of least privilege for user permissions</li>
      <li>Create and maintain documentation for custom configurations</li>
      <li>Perform regular backups of critical system data</li>
      <li>Test changes in a staging environment before applying to production</li>
    </ul>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
      <h4 class="text-lg font-bold text-blue-800 mb-2">Pro Tip</h4>
      <p class="text-blue-800">Use the bulk operations tool to make changes to multiple records simultaneously, saving valuable administration time.</p>
    </div>
    
    <div class="mt-8 pt-6 border-t border-gray-200">
      <p class="text-sm text-gray-600">Last updated on April 15, 2025 by Admin Team</p>
    </div>
  `;
};

// Component lifecycle hooks
onMounted(() => {
  // Set the first category as selected by default
  if (knowledgeBase.value.length > 0) {
    selectedCategory.value = knowledgeBase.value[0].id;
  }
});
</script>

<template>
  <div class="min-h-full">
    <!-- Page header -->
    <div class="bg-white shadow">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div class="py-6 md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <!-- Breadcrumbs -->
            <div class="flex items-center text-sm text-gray-500 mb-1">
              <a href="#" class="hover:text-gray-700">Admin</a>
              <svg
                class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400"
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
              <span>Help & Resources</span>
            </div>

            <!-- Title and description -->
            <h1
              class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate"
            >
              Help & Resources
            </h1>
            <p class="mt-2 text-sm text-gray-500">
              Access platform documentation, guides, and support resources for
              administrators
            </p>
          </div>

          <!-- Search help center -->
          <div class="mt-6 md:mt-0 md:ml-4">
            <div class="mt-1 relative rounded-md shadow-sm">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                placeholder="Search help articles..."
                @input="searchKnowledgeBase"
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content tabs -->
    <div class="mt-6">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div class="sm:hidden">
          <!-- Mobile tab selector -->
          <label for="tabs" class="sr-only">Select a tab</label>
          <select
            id="tabs"
            name="tabs"
            class="block w-full py-2 pl-3 pr-10 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            @change="changeTab($event.target.value)"
          >
            <option
              value="knowledge-base"
              :selected="activeTab === 'knowledge-base'"
            >
              Knowledge Base
            </option>
            <option
              value="support-tickets"
              :selected="activeTab === 'support-tickets'"
            >
              Support Tickets
            </option>
            <option
              value="contact-support"
              :selected="activeTab === 'contact-support'"
            >
              Contact Support
            </option>
          </select>
        </div>

        <!-- Desktop tabs -->
        <div class="hidden sm:block">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
              <button
                :class="[
                  activeTab === 'knowledge-base'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                ]"
                @click="changeTab('knowledge-base')"
              >
                Knowledge Base
              </button>
              <button
                :class="[
                  activeTab === 'support-tickets'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                ]"
                @click="changeTab('support-tickets')"
              >
                Support Tickets
              </button>
              <button
                :class="[
                  activeTab === 'contact-support'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                ]"
                @click="changeTab('contact-support')"
              >
                Contact Support
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content area -->
    <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8 py-8">
      <!-- Search results overlay -->
      <div v-if="searchQuery && searchResults.length > 0" class="mb-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Search Results for "{{ searchQuery }}"
            </h3>

            <ul class="divide-y divide-gray-200">
              <li
                v-for="article in searchResults"
                :key="article.id"
                class="py-4"
              >
                <div class="flex items-start">
                  <div class="flex-shrink-0 mt-1">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-indigo-600"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>

                  <!-- Attached files list -->
                  <div v-if="newTicket.attachments.length > 0" class="mt-3">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                      Attached Files:
                    </h4>
                    <ul
                      class="divide-y divide-gray-200 border border-gray-200 rounded-md overflow-hidden"
                    >
                      <li
                        v-for="(file, index) in newTicket.attachments"
                        :key="index"
                        class="pl-3 pr-4 py-3 flex items-center justify-between text-sm bg-white"
                      >
                        <div class="flex items-center">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="flex-shrink-0 h-5 w-5 text-gray-400 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <span class="truncate">{{ file.name }}</span>
                          <span class="ml-2 text-gray-500 text-xs"
                            >{{ (file.size / 1024).toFixed(1) }} KB</span
                          >
                        </div>
                        <div class="ml-4 flex-shrink-0">
                          <button
                            type="button"
                            class="font-medium text-red-600 hover:text-red-800"
                            @click="removeAttachment(index)"
                          >
                            Remove
                          </button>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="ml-3 flex-1">
                    <button
                      class="text-left text-base font-semibold text-indigo-600 hover:text-indigo-800"
                      @click="selectArticle(article)"
                    >
                      {{ article.title }}
                    </button>
                    <p class="mt-1 text-sm text-gray-600">
                      {{ article.summary }}
                    </p>
                    <div class="mt-2 text-xs text-gray-500">
                      Last updated {{ formatDate(article.lastUpdated) }}
                    </div>
                  </div>
                </div>
              </li>
            </ul>

            <div class="mt-4 text-right">
              <button
                class="text-sm text-gray-600 hover:text-gray-900"
                @click="
                  searchQuery = '';
                  searchResults = [];
                "
              >
                Clear search results
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading indicator for search -->
      <div v-if="isSearching" class="flex justify-center my-8">
        <svg
          class="animate-spin h-6 w-6 text-indigo-600"
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
          />
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          />
        </svg>
      </div>

      <!-- No search results message -->
      <div
        v-if="searchQuery && !isSearching && searchResults.length === 0"
        class="mb-8"
      >
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-4 py-5 sm:p-6 text-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="mx-auto h-12 w-12 text-gray-400"
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
            <h3 class="mt-2 text-lg font-medium text-gray-900">
              No results found
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              We couldn't find any articles matching "{{ searchQuery }}". Please
              try a different search term.
            </p>
            <div class="mt-4">
              <button
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                @click="
                  searchQuery = '';
                  searchResults = [];
                "
              >
                Clear search
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Knowledge Base Tab -->
      <div v-if="activeTab === 'knowledge-base'" class="space-y-8">
        <!-- Knowledge Base display when no article is selected -->
        <div
          v-if="!showFullArticle"
          class="bg-white shadow rounded-lg overflow-hidden"
        >
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
              Admin Knowledge Base
            </h3>

            <!-- Categories grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
              <div
                v-for="category in knowledgeBase"
                :key="category.id"
                class="relative rounded-lg border border-gray-200 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-indigo-400 hover:ring-1 hover:ring-indigo-500 cursor-pointer transition-all duration-200"
                :class="{
                  'ring-2 ring-indigo-500 border-indigo-400':
                    selectedCategory === category.id,
                }"
                @click="selectCategory(category.id)"
              >
                <div class="flex-shrink-0">
                  <div
                    class="h-10 w-10 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center"
                  >
                    <span v-html="renderCategoryIcon(category.icon)"/>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="focus:outline-none">
                    <span class="absolute inset-0" aria-hidden="true"/>
                    <p class="text-md font-medium text-gray-900">
                      {{ category.title }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ category.description }}
                    </p>
                    <p class="text-xs text-indigo-600 mt-1">
                      {{ category.articles.length }} articles
                    </p>
                  </div>
                </div>
                <div class="flex-shrink-0 text-gray-400">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    :class="{
                      'transform rotate-90': selectedCategory === category.id,
                    }"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Articles for selected category -->
            <div v-if="selectedCategory && currentCategory" class="mt-8">
              <h4 class="text-lg font-medium text-gray-900 mb-4">
                {{ currentCategory.title }} Articles
              </h4>

              <ul class="divide-y divide-gray-200">
                <li
                  v-for="article in currentCategory.articles"
                  :key="article.id"
                  class="py-4 hover:bg-gray-50 transition-colors duration-150"
                >
                  <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-indigo-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div class="ml-3 flex-1">
                      <button
                        class="text-left text-base font-semibold text-indigo-600 hover:text-indigo-800"
                        @click="selectArticle(article)"
                      >
                        {{ article.title }}
                      </button>
                      <p class="mt-1 text-sm text-gray-600">
                        {{ article.summary }}
                      </p>
                      <div
                        class="mt-2 flex items-center text-xs text-gray-500 space-x-6"
                      >
                        <div>
                          Last updated {{ formatDate(article.lastUpdated) }}
                        </div>
                        <div class="flex items-center">
                          <div class="flex text-amber-400">
                            <span v-for="n in article.popularity" :key="n">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                />
                              </svg>
                            </span>
                            <span
                              v-for="n in 5 - article.popularity"
                              :key="`empty-${n}`"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-gray-300"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                />
                              </svg>
                            </span>
                          </div>
                          <span class="ml-1">Popularity</span>
                        </div>
                      </div>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                      <button
                        class="font-medium text-indigo-600 hover:text-indigo-800 text-sm"
                        @click="selectArticle(article)"
                      >
                        Read more
                        <span aria-hidden="true"> &rarr;</span>
                      </button>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Full article view -->
        <div
          v-if="showFullArticle && selectedArticle"
          class="bg-white shadow rounded-lg overflow-hidden"
        >
          <div class="px-4 py-5 sm:p-6">
            <!-- Article header -->
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-2xl font-bold text-gray-900">
                  {{ selectedArticle.title }}
                </h3>
                <div
                  class="mt-2 flex items-center text-sm text-gray-500 space-x-4"
                >
                  <div>
                    Last updated {{ formatDate(selectedArticle.lastUpdated) }}
                  </div>
                  <div class="flex items-center">
                    <div class="flex text-amber-400">
                      <span v-for="n in selectedArticle.popularity" :key="n">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </span>
                      <span
                        v-for="n in 5 - selectedArticle.popularity"
                        :key="`empty-${n}`"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 text-gray-300"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                          />
                        </svg>
                      </span>
                    </div>
                    <span class="ml-1"
                      >{{ selectedArticle.popularity }} / 5 rating</span
                    >
                  </div>
                </div>
              </div>
              <button
                class="text-gray-400 hover:text-gray-500"
                @click="showFullArticle = false"
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

            <!-- Article content -->
            <div
              class="mt-6 prose prose-indigo max-w-none"
              v-html="getArticleContent(selectedArticle.id)"
            />

            <!-- Article footer -->
            <div
              class="mt-8 border-t border-gray-200 pt-6 flex items-center justify-between"
            >
              <div class="flex items-center space-x-4">
                <button
                  class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  Rate this article
                </button>
                <button
                  class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                    />
                  </svg>
                  Suggest edits
                </button>
              </div>
              <div>
                <button
                  class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Download PDF
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Support Tickets Tab -->
      <div v-if="activeTab === 'support-tickets'" class="space-y-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-4 py-5 sm:p-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center justify-between"
            >
              <span>Your Support Tickets</span>
              <button
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
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd"
                  />
                </svg>
                New Ticket
              </button>
            </h3>

            <!-- Tickets table -->
            <div class="flex flex-col mt-2">
              <div
                class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg"
              >
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Ticket ID
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Subject
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Status
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Priority
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Date Created
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Last Response
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="ticket in supportTickets"
                      :key="ticket.id"
                      class="hover:bg-gray-50"
                    >
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                      >
                        {{ ticket.id }}
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                      >
                        {{ ticket.subject }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="getStatusClass(ticket.status)"
                        >
                          {{
                            ticket.status
                              .replace("_", " ")
                              .charAt(0)
                              .toUpperCase() +
                            ticket.status.replace("_", " ").slice(1)
                          }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                          :class="getPriorityClass(ticket.priority)"
                        >
                          {{
                            ticket.priority.charAt(0).toUpperCase() +
                            ticket.priority.slice(1)
                          }}
                        </span>
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                      >
                        {{ formatDate(ticket.dateCreated) }}
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                      >
                        {{ formatDate(ticket.lastResponse) }}
                      </td>
                      <td
                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                      >
                        <button
                          class="text-indigo-600 hover:text-indigo-900 mr-3"
                        >
                          View
                        </button>
                        <button
                          class="text-gray-600 hover:text-gray-900"
                          :class="{
                            'opacity-50 pointer-events-none':
                              ticket.status === 'resolved',
                          }"
                          :disabled="ticket.status === 'resolved'"
                        >
                          Update
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination - simplified for demo -->
            <nav
              class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4"
              aria-label="Pagination"
            >
              <div class="hidden sm:block">
                <p class="text-sm text-gray-700">
                  Showing <span class="font-medium">1</span> to
                  <span class="font-medium">3</span> of
                  <span class="font-medium">3</span> results
                </p>
              </div>
              <div class="flex-1 flex justify-between sm:justify-end">
                <button
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 opacity-50 pointer-events-none"
                  disabled
                >
                  Previous
                </button>
                <button
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 opacity-50 pointer-events-none"
                  disabled
                >
                  Next
                </button>
              </div>
            </nav>
          </div>
        </div>
      </div>

      <!-- Contact Support Tab -->
      <div v-if="activeTab === 'contact-support'" class="space-y-8">
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Create Support Ticket
            </h3>

            <div
              class="mb-6 p-4 border border-blue-200 rounded-md bg-blue-50 text-blue-700"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-3 text-sm">
                  <p>
                    Submit a ticket for technical issues, account problems, or
                    general inquiries. For urgent matters, please include all
                    relevant details to expedite resolution.
                  </p>
                </div>
              </div>
            </div>

            <!-- Ticket form -->
            <form @submit.prevent="submitTicket">
              <div class="space-y-6">
                <div>
                  <label
                    for="subject"
                    class="block text-sm font-medium text-gray-700"
                    >Subject</label
                  >
                  <div class="mt-1">
                    <input
                      id="subject"
                      v-model="newTicket.subject"
                      type="text"
                      required
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                      placeholder="Brief description of your issue"
                    >
                  </div>
                </div>

                <div>
                  <label
                    for="priority"
                    class="block text-sm font-medium text-gray-700"
                    >Priority</label
                  >
                  <div class="mt-1">
                    <select
                      id="priority"
                      v-model="newTicket.priority"
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    >
                      <option value="low">
                        Low - General inquiry or minor issue
                      </option>
                      <option value="medium">
                        Medium - Issue affecting functionality
                      </option>
                      <option value="high">
                        High - Significant system issue
                      </option>
                      <option value="critical">
                        Critical - System outage or data loss
                      </option>
                    </select>
                  </div>
                </div>

                <div>
                  <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                    >Description</label
                  >
                  <div class="mt-1">
                    <textarea
                      id="description"
                      v-model="newTicket.description"
                      rows="6"
                      required
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                      placeholder="Please provide detailed information about your issue including any error messages, steps to reproduce, and expected outcome."
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700"
                    >Attachments</label
                  >
                  <div class="mt-1 flex items-center">
                    <input
                      ref="fileInput"
                      type="file"
                      class="hidden"
                      multiple
                      @change="handleFileUpload"
                    >
                    <button
                      type="button"
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      @click="fileInput && fileInput.click()"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2 text-gray-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Upload Files
                    </button>
                    <p class="ml-3 text-xs text-gray-500">
                      Upload screenshots or relevant documents (PDF, PNG, JPG,
                      max 10MB each)
                    </p>
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
