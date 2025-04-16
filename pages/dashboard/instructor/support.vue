<!-- pages/dashboard/instructor/support/index.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Page meta
definePageMeta({
  layout: "dashboard-instructor",
});

useHead({
  title: "Help & Support - Instructor Dashboard",
});

// Types
interface SupportArticle {
  id: number;
  title: string;
  excerpt: string;
  category: string;
  tags: string[];
  views: number;
  helpful: number;
  lastUpdated: string;
}

interface SupportCategory {
  id: string;
  name: string;
  icon: string;
  description: string;
  articleCount: number;
}

interface FaqItem {
  question: string;
  answer: string;
  isExpanded: boolean;
}

interface SupportTicket {
  id: string;
  subject: string;
  status: "open" | "in_progress" | "resolved" | "closed";
  priority: "low" | "medium" | "high" | "urgent";
  category: string;
  created: string;
  lastUpdated: string;
}

// State
const searchQuery = ref("");
const isSearching = ref(false);
const searchResults = ref<SupportArticle[]>([]);
const popularArticles = ref<SupportArticle[]>([]);
const supportCategories = ref<SupportCategory[]>([]);
const faqItems = ref<FaqItem[]>([]);
const activeTab = ref("knowledge-base");
const showNewTicketForm = ref(false);
const ticketSubject = ref("");
const ticketDescription = ref("");
const ticketCategory = ref("");
const ticketPriority = ref("medium");
const isSubmittingTicket = ref(false);
const supportTickets = ref<SupportTicket[]>([]);

// Load sample data
onMounted(() => {
  // Mock categories
  supportCategories.value = [
    {
      id: "getting-started",
      name: "Getting Started",
      icon: "rocket",
      description:
        "Learn the basics of creating and publishing your first course",
      articleCount: 8,
    },
    {
      id: "course-creation",
      name: "Course Creation",
      icon: "book-open",
      description: "Best practices and tutorials for creating engaging content",
      articleCount: 15,
    },
    {
      id: "video-content",
      name: "Video Content",
      icon: "video",
      description: "Guidelines for recording, editing and uploading videos",
      articleCount: 12,
    },
    {
      id: "assessments",
      name: "Quizzes & Assignments",
      icon: "clipboard-check",
      description: "How to create effective assessments for your students",
      articleCount: 9,
    },
    {
      id: "student-management",
      name: "Student Management",
      icon: "users",
      description: "Managing enrollments, communications and feedback",
      articleCount: 11,
    },
    {
      id: "payments",
      name: "Payments & Revenue",
      icon: "cash",
      description: "Understanding the payment system and revenue sharing",
      articleCount: 7,
    },
    {
      id: "consultations",
      name: "Consultation Services",
      icon: "chat-alt",
      description: "Setting up and managing your consultation sessions",
      articleCount: 6,
    },
    {
      id: "technical-issues",
      name: "Technical Issues",
      icon: "cog",
      description: "Troubleshooting common technical problems",
      articleCount: 14,
    },
  ];

  // Mock popular articles
  popularArticles.value = [
    {
      id: 1,
      title: "How to Record High-Quality Video Lessons",
      excerpt:
        "Learn the best practices for recording professional-looking video content for your courses.",
      category: "Video Content",
      tags: ["video", "recording", "quality"],
      views: 2547,
      helpful: 943,
      lastUpdated: "2025-03-10T14:30:00Z",
    },
    {
      id: 2,
      title: "Setting the Right Price for Your Course",
      excerpt:
        "Guidelines for pricing your course effectively to maximize enrollments and revenue.",
      category: "Payments & Revenue",
      tags: ["pricing", "revenue", "strategy"],
      views: 1982,
      helpful: 876,
      lastUpdated: "2025-03-15T09:45:00Z",
    },
    {
      id: 3,
      title: "Creating Engaging Quizzes and Assignments",
      excerpt:
        "Tips for designing assessments that test knowledge effectively and keep students motivated.",
      category: "Quizzes & Assignments",
      tags: ["assessment", "engagement", "quizzes"],
      views: 1654,
      helpful: 742,
      lastUpdated: "2025-03-08T11:20:00Z",
    },
    {
      id: 4,
      title: "Optimizing Your Course for Search",
      excerpt:
        "SEO tips to help students find your course more easily in search results.",
      category: "Course Creation",
      tags: ["seo", "discoverability", "marketing"],
      views: 2123,
      helpful: 956,
      lastUpdated: "2025-03-12T16:15:00Z",
    },
  ];

  // Mock FAQs
  faqItems.value = [
    {
      question: "How do I get paid for my courses?",
      answer:
        "Payments are processed monthly. Your earnings are calculated based on your course enrollments and any consultation sessions you've conducted. Payments are issued on the 15th of each month for the previous month's earnings. You can view your earnings and payment history in the Earnings section of your instructor dashboard.",
      isExpanded: false,
    },
    {
      question: "What are the video requirements for courses?",
      answer:
        "For optimal quality, we recommend recording videos in 1080p (1920x1080) resolution or higher, with good lighting and clear audio. Videos should be in MP4 format with H.264 encoding. The platform supports videos up to 4GB in size and 4 hours in length per video. For longer content, consider breaking it into smaller sections.",
      isExpanded: false,
    },
    {
      question: "How can I increase student engagement in my courses?",
      answer:
        "To boost engagement, keep videos concise (5-15 minutes), include interactive elements like quizzes and assignments, respond promptly to student questions, provide comprehensive resources, create a course community, and regularly update your content with new information.",
      isExpanded: false,
    },
    {
      question: "Can I offer discounts or promotional pricing?",
      answer:
        'Yes, you can create custom discount coupons for your courses. Navigate to your course page, select "Promotions" from the sidebar, and create new coupon codes with specific discount percentages and expiration dates. You can share these codes directly with potential students or use them in marketing campaigns.',
      isExpanded: false,
    },
    {
      question: "What happens if a student requests a refund?",
      answer:
        "Students can request refunds within 30 days of purchasing a course if they're unsatisfied. Each refund request is reviewed based on student activity and feedback. If approved, the refund amount is deducted from your current earnings. You'll receive notifications about refund requests, and you can provide additional context if needed.",
      isExpanded: false,
    },
    {
      question: "How do I schedule consultation sessions?",
      answer:
        'To offer consultations, go to the "Consultations" section in your dashboard, set your availability by defining time slots, specify your session rates and duration options, and enable booking. Students can then browse available instructors and book sessions with you based on your availability. You\'ll receive notifications for new bookings.',
      isExpanded: false,
    },
  ];

  // Mock support tickets
  supportTickets.value = [
    {
      id: "TKT-2025-1042",
      subject: "Video processing stuck at 80%",
      status: "in_progress",
      priority: "high",
      category: "Technical Issues",
      created: "2025-04-10T09:23:00Z",
      lastUpdated: "2025-04-10T14:45:00Z",
    },
    {
      id: "TKT-2025-0985",
      subject: "Payment not received for March earnings",
      status: "open",
      priority: "urgent",
      category: "Payments & Revenue",
      created: "2025-04-05T11:30:00Z",
      lastUpdated: "2025-04-05T15:20:00Z",
    },
    {
      id: "TKT-2025-0921",
      subject: "Student reported broken quiz in Module 3",
      status: "resolved",
      priority: "medium",
      category: "Assessments",
      created: "2025-03-28T14:10:00Z",
      lastUpdated: "2025-03-30T08:45:00Z",
    },
    {
      id: "TKT-2025-0896",
      subject: "Request to update instructor profile information",
      status: "closed",
      priority: "low",
      category: "Account Management",
      created: "2025-03-20T10:15:00Z",
      lastUpdated: "2025-03-21T13:30:00Z",
    },
  ];
});

// Search functionality
const performSearch = () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    return;
  }

  isSearching.value = true;

  // Simulate API search delay
  setTimeout(() => {
    // Mocked search results based on query
    const query = searchQuery.value.toLowerCase();

    // In a real app, this would be an API call
    searchResults.value = [
      {
        id: 101,
        title: "How to Upload and Manage Video Content",
        excerpt:
          "Learn how to efficiently upload, organize, and manage your video lessons on the platform.",
        category: "Video Content",
        tags: ["upload", "management", "video"],
        views: 1548,
        helpful: 623,
        lastUpdated: "2025-02-18T10:30:00Z",
      },
      {
        id: 102,
        title: "Best Practices for Video Recording Settings",
        excerpt:
          "Recommended camera settings, resolution, and format for optimal video quality.",
        category: "Video Content",
        tags: ["settings", "resolution", "quality"],
        views: 1243,
        helpful: 587,
        lastUpdated: "2025-03-05T15:45:00Z",
      },
      {
        id: 103,
        title: "Troubleshooting Video Upload Issues",
        excerpt:
          "Common problems with video uploads and how to resolve them quickly.",
        category: "Technical Issues",
        tags: ["troubleshooting", "upload", "errors"],
        views: 1876,
        helpful: 845,
        lastUpdated: "2025-03-22T09:15:00Z",
      },
    ].filter(
      (article) =>
        article.title.toLowerCase().includes(query) ||
        article.excerpt.toLowerCase().includes(query) ||
        article.tags.some((tag) => tag.includes(query))
    );

    isSearching.value = false;
  }, 500);
};

// Toggle FAQ expansion
const toggleFaq = (index: number) => {
  faqItems.value[index].isExpanded = !faqItems.value[index].isExpanded;
};

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Submit support ticket
const submitSupportTicket = () => {
  if (
    !ticketSubject.value ||
    !ticketDescription.value ||
    !ticketCategory.value
  ) {
    return;
  }

  isSubmittingTicket.value = true;

  // Simulate API call
  setTimeout(() => {
    // Create ticket (in a real app this would be handled by an API)
    const newTicket: SupportTicket = {
      id: `TKT-2025-${1000 + Math.floor(Math.random() * 1000)}`,
      subject: ticketSubject.value,
      status: "open",
      priority: ticketPriority.value as "low" | "medium" | "high" | "urgent",
      category: ticketCategory.value,
      created: new Date().toISOString(),
      lastUpdated: new Date().toISOString(),
    };

    supportTickets.value.unshift(newTicket);

    // Reset form
    ticketSubject.value = "";
    ticketDescription.value = "";
    ticketCategory.value = "";
    ticketPriority.value = "medium";
    showNewTicketForm.value = false;
    isSubmittingTicket.value = false;

    // Switch to tickets tab to show the new ticket
    activeTab.value = "tickets";
  }, 1000);
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "open":
      return "bg-blue-100 text-blue-800";
    case "in_progress":
      return "bg-yellow-100 text-yellow-800";
    case "resolved":
      return "bg-green-100 text-green-800";
    case "closed":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get priority badge classes
const getPriorityBadgeClasses = (priority: string) => {
  switch (priority) {
    case "low":
      return "bg-gray-100 text-gray-800";
    case "medium":
      return "bg-blue-100 text-blue-800";
    case "high":
      return "bg-orange-100 text-orange-800";
    case "urgent":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Render icon based on category name
const renderCategoryIcon = (iconName: string) => {
  switch (iconName) {
    case "rocket":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
      </svg>`;
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>`;
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
      </svg>`;
    case "clipboard-check":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>`;
    case "cash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>`;
    case "chat-alt":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
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
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Help & Support</h1>
          <p class="text-gray-600">
            Find answers, guides, and assistance for your instructor activities
          </p>
        </div>

        <!-- Search bar -->
        <div class="mt-4 md:mt-0 relative w-full md:w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search knowledge base..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            @keyup.enter="performSearch"
          >
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
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
          <button
            v-if="searchQuery"
            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
            @click="
              searchQuery = '';
              searchResults = [];
            "
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
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

    <!-- Search results -->
    <div
      v-if="searchQuery && (isSearching || searchResults.length > 0)"
      class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200"
    >
      <div v-if="isSearching" class="py-8 flex justify-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-600"
        />
      </div>

      <div v-else-if="searchResults.length > 0">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          Search Results for "{{ searchQuery }}"
        </h2>
        <div class="space-y-4">
          <div
            v-for="article in searchResults"
            :key="`search-${article.id}`"
            class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition-all duration-200"
          >
            <h3 class="text-lg font-medium text-indigo-600 mb-2">
              {{ article.title }}
            </h3>
            <p class="text-gray-600 mb-3">{{ article.excerpt }}</p>
            <div class="flex items-center text-xs text-gray-500">
              <span
                class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full mr-2"
                >{{ article.category }}</span
              >
              <span class="mr-2 flex items-center">
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
                {{ article.views }}
              </span>
              <span class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"
                  />
                </svg>
                {{ article.helpful }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="py-8 text-center">
        <div
          class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8 text-gray-400"
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
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No results found</h3>
        <p class="text-gray-600">
          Please try a different search term or browse our knowledge base
        </p>
      </div>
    </div>

    <!-- Main content with tabs -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
      <!-- Tab navigation -->
      <div class="border-b border-gray-200">
        <nav class="flex space-x-2 px-4">
          <button
            class="py-4 px-2 inline-flex items-center text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            :class="
              activeTab === 'knowledge-base'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            @click="activeTab = 'knowledge-base'"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
              />
            </svg>
            Knowledge Base
          </button>

          <button
            class="py-4 px-2 inline-flex items-center text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            :class="
              activeTab === 'faq'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            @click="activeTab = 'faq'"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                clip-rule="evenodd"
              />
            </svg>
            FAQ
          </button>

          <button
            class="py-4 px-2 inline-flex items-center text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            :class="
              activeTab === 'contact'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            @click="activeTab = 'contact'"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
              />
              <path
                d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
              />
            </svg>
            Contact Us
          </button>

          <button
            class="py-4 px-2 inline-flex items-center text-sm font-medium border-b-2 whitespace-nowrap transition-colors"
            :class="
              activeTab === 'tickets'
                ? 'border-indigo-600 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            @click="activeTab = 'tickets'"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                clip-rule="evenodd"
              />
            </svg>
            Support Tickets
            <span
              v-if="
                supportTickets.filter(
                  (t) => t.status === 'open' || t.status === 'in_progress'
                ).length > 0
              "
              class="ml-2 bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5 rounded-full"
            >
              {{
                supportTickets.filter(
                  (t) => t.status === "open" || t.status === "in_progress"
                ).length
              }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Tab content -->
      <div class="p-6">
        <!-- Knowledge Base Tab -->
        <div v-if="activeTab === 'knowledge-base'">
          <div class="mb-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Popular Articles
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="article in popularArticles"
                :key="`popular-${article.id}`"
                class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition-all duration-200"
              >
                <h3 class="text-lg font-medium text-indigo-600 mb-2">
                  {{ article.title }}
                </h3>
                <p class="text-gray-600 mb-3">{{ article.excerpt }}</p>
                <div class="flex items-center text-xs text-gray-500">
                  <span
                    class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full mr-2"
                    >{{ article.category }}</span
                  >
                  <span class="mr-2 flex items-center">
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
                    {{ article.views }}
                  </span>
                  <span class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"
                      />
                    </svg>
                    {{ article.helpful }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Browse by Category
          </h2>
          <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
          >
            <div
              v-for="category in supportCategories"
              :key="category.id"
              class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-md transition-all duration-200"
            >
              <div class="flex items-center mb-3">
                <div
                  class="bg-indigo-100 p-2 rounded-lg mr-3 text-indigo-600"
                  v-html="renderCategoryIcon(category.icon)"
                />
                <h3 class="text-lg font-medium text-gray-900">
                  {{ category.name }}
                </h3>
              </div>
              <p class="text-gray-600 mb-3">{{ category.description }}</p>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500"
                  >{{ category.articleCount }} articles</span
                >
                <button
                  class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center"
                >
                  View
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 ml-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- FAQ Tab -->
        <div v-if="activeTab === 'faq'">
          <h2 class="text-lg font-medium text-gray-900 mb-6">
            Frequently Asked Questions
          </h2>
          <div class="space-y-4">
            <div
              v-for="(item, index) in faqItems"
              :key="`faq-${index}`"
              class="border border-gray-200 rounded-lg overflow-hidden transition-colors"
              :class="{ 'border-indigo-300 bg-indigo-50': item.isExpanded }"
            >
              <button
                class="w-full flex justify-between items-center p-4 text-left focus:outline-none transition-colors"
                :class="{ 'text-indigo-700': item.isExpanded }"
                @click="toggleFaq(index)"
              >
                <h3 class="text-lg font-medium">{{ item.question }}</h3>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transform transition-transform"
                  :class="{ 'rotate-180': item.isExpanded }"
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
                v-if="item.isExpanded"
                class="p-4 pt-0 border-t border-gray-200"
              >
                <p class="text-gray-600">{{ item.answer }}</p>
              </div>
            </div>
          </div>

          <div
            class="mt-8 bg-indigo-50 border border-indigo-100 rounded-lg p-6 text-center"
          >
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              Didn't find an answer to your question?
            </h3>
            <p class="text-gray-600 mb-4">
              Our support team is here to help you with any other questions
            </p>
            <button
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
              @click="activeTab = 'contact'"
            >
              Contact Support
            </button>
          </div>
        </div>

        <!-- Contact Us Tab -->
        <div v-if="activeTab === 'contact'">
          <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-1">
              Contact Support
            </h2>
            <p class="text-gray-600">
              Get in touch with our instructor support team
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
              class="border border-gray-200 rounded-lg p-6 text-center hover:border-indigo-300 hover:shadow-md transition-all duration-200"
            >
              <div
                class="mx-auto h-12 w-12 bg-indigo-100 flex items-center justify-center rounded-full mb-4"
              >
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
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">
                Email Support
              </h3>
              <p class="text-gray-600 mb-4">
                Send us an email and we'll respond within 24 hours
              </p>
              <a
                href="mailto:instructor-support@learnconsult.com"
                class="text-indigo-600 hover:text-indigo-800 font-medium"
              >
                instructor-support@learnconsult.com
              </a>
            </div>

            <div
              class="border border-gray-200 rounded-lg p-6 text-center hover:border-indigo-300 hover:shadow-md transition-all duration-200"
            >
              <div
                class="mx-auto h-12 w-12 bg-green-100 flex items-center justify-center rounded-full mb-4"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-green-600"
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
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">Live Chat</h3>
              <p class="text-gray-600 mb-4">
                Chat with our support team in real-time
              </p>
              <button
                class="text-green-600 hover:text-green-800 font-medium flex items-center justify-center mx-auto"
              >
                <span>Start Live Chat</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>

            <div
              class="border border-gray-200 rounded-lg p-6 text-center hover:border-indigo-300 hover:shadow-md transition-all duration-200"
            >
              <div
                class="mx-auto h-12 w-12 bg-purple-100 flex items-center justify-center rounded-full mb-4"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-purple-600"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">
                Help Center
              </h3>
              <p class="text-gray-600 mb-4">
                Explore our comprehensive knowledge base
              </p>
              <button
                class="text-purple-600 hover:text-purple-800 font-medium flex items-center justify-center mx-auto"
                @click="activeTab = 'knowledge-base'"
              >
                <span>Browse Articles</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Submit a Support Ticket
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label
                  for="ticket-subject"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Subject</label
                >
                <input
                  id="ticket-subject"
                  v-model="ticketSubject"
                  type="text"
                  placeholder="Briefly describe your issue"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
              </div>

              <div>
                <label
                  for="ticket-category"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Category</label
                >
                <select
                  id="ticket-category"
                  v-model="ticketCategory"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                  <option value="">Select a category</option>
                  <option value="Course Creation">Course Creation</option>
                  <option value="Video Content">Video Content</option>
                  <option value="Assessments">Quizzes & Assignments</option>
                  <option value="Student Management">Student Management</option>
                  <option value="Payments & Revenue">Payments & Revenue</option>
                  <option value="Technical Issues">Technical Issues</option>
                  <option value="Account Management">Account Management</option>
                </select>
              </div>
            </div>

            <div class="mb-4">
              <label
                for="ticket-description"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <textarea
                id="ticket-description"
                v-model="ticketDescription"
                rows="5"
                placeholder="Please provide as much detail as possible about your issue"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div class="mb-6">
              <label
                for="ticket-priority"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Priority</label
              >
              <div class="flex space-x-4">
                <label class="inline-flex items-center">
                  <input
                    v-model="ticketPriority"
                    type="radio"
                    value="low"
                    class="text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-700">Low</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    v-model="ticketPriority"
                    type="radio"
                    value="medium"
                    class="text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-700">Medium</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    v-model="ticketPriority"
                    type="radio"
                    value="high"
                    class="text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-700">High</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    v-model="ticketPriority"
                    type="radio"
                    value="urgent"
                    class="text-indigo-600 focus:ring-indigo-500"
                  >
                  <span class="ml-2 text-sm text-gray-700">Urgent</span>
                </label>
              </div>
            </div>

            <div class="flex justify-end">
              <button
                :disabled="
                  !ticketSubject ||
                  !ticketDescription ||
                  !ticketCategory ||
                  isSubmittingTicket
                "
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center"
                :class="{
                  'opacity-50 cursor-not-allowed':
                    !ticketSubject ||
                    !ticketDescription ||
                    !ticketCategory ||
                    isSubmittingTicket,
                }"
                @click="submitSupportTicket"
              >
                <div
                  v-if="isSubmittingTicket"
                  class="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white mr-2"
                />
                <span>{{
                  isSubmittingTicket ? "Submitting..." : "Submit Ticket"
                }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Support Tickets Tab -->
        <div v-if="activeTab === 'tickets'">
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
          >
            <div>
              <h2 class="text-lg font-medium text-gray-900 mb-1">
                Support Tickets
              </h2>
              <p class="text-gray-600">
                Track and manage your support requests
              </p>
            </div>
            <button
              class="mt-3 sm:mt-0 inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
              @click="
                showNewTicketForm = true;
                activeTab = 'contact';
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>New Ticket</span>
            </button>
          </div>

          <div v-if="supportTickets.length === 0" class="text-center py-8">
            <div
              class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-gray-400"
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
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">
              No support tickets
            </h3>
            <p class="text-gray-600 mb-6">
              You haven't created any support tickets yet
            </p>
            <button
              class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
              @click="
                showNewTicketForm = true;
                activeTab = 'contact';
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Create Your First Ticket</span>
            </button>
          </div>

          <div v-else class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Ticket ID
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Subject
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Priority
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Category
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Date
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="ticket in supportTickets"
                    :key="ticket.id"
                    class="hover:bg-gray-50 transition-colors cursor-pointer"
                  >
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600"
                    >
                      {{ ticket.id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                      {{ ticket.subject }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                        :class="getPriorityBadgeClasses(ticket.priority)"
                      >
                        {{ ticket.priority }}
                      </span>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ ticket.category }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ formatDate(ticket.created) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Support ticket details (would be expanded in a real app) -->
          <div
            class="mt-8 bg-indigo-50 border border-indigo-100 rounded-lg p-6 text-center"
          >
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              Need more help?
            </h3>
            <p class="text-gray-600 mb-4">
              Our support team is available to assist you 24/7
            </p>
            <button
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
              @click="
                showNewTicketForm = true;
                activeTab = 'contact';
              "
            >
              Create New Ticket
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Information -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm text-center"
      >
        <div class="flex flex-col items-center">
          <div
            class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-4"
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h3 class="font-medium text-gray-900 mb-1">Support Hours</h3>
          <p class="text-gray-600">24/7 support available</p>
          <p class="text-gray-600">Response time: 2-24 hours</p>
        </div>
      </div>

      <div
        class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm text-center"
      >
        <div class="flex flex-col items-center">
          <div
            class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-4"
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
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
              />
            </svg>
          </div>
          <h3 class="font-medium text-gray-900 mb-1">Phone Support</h3>
          <p class="text-gray-600">+1 (555) 123-4567</p>
          <p class="text-gray-600">Mon-Fri: 9am-5pm EST</p>
        </div>
      </div>

      <div
        class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm text-center"
      >
        <div class="flex flex-col items-center">
          <div
            class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-4"
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
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h3 class="font-medium text-gray-900 mb-1">Instructor Community</h3>
          <p class="text-gray-600">Join our instructor forums</p>
          <p class="text-gray-600">Learn from peers and experts</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Transitions for FAQ expansion */
.faq-answer-enter-active,
.faq-answer-leave-active {
  transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
  max-height: 1000px;
  overflow: hidden;
}
.faq-answer-enter-from,
.faq-answer-leave-to {
  max-height: 0;
  opacity: 0;
}

/* Loading spinner animation */
@keyframes spinner {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spinner 0.8s linear infinite;
}

/* Animation for search results and tab transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Hover animations for knowledge base categories */
.category-card:hover {
  transform: translateY(-3px);
}
</style>
