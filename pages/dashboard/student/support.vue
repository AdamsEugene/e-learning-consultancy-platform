<!-- pages/dashboard/support.vue -->
<script setup lang="ts">
// Set page layout
definePageMeta({
  layout: "dashboard-student",
});

// Page meta
useHead({
  title: "Help & Support - LearnConsult",
  meta: [
    {
      name: "description",
      content: "Get help and support for your learning experience",
    },
  ],
});

// Support categories
const supportCategories = ref([
  {
    id: "account",
    name: "Account & Profile",
    icon: "user",
    articles: [
      { id: 1, title: "How to update your profile information", views: 1452 },
      { id: 2, title: "Changing your password", views: 983 },
      { id: 3, title: "Managing notification preferences", views: 721 },
      { id: 4, title: "Setting up two-factor authentication", views: 543 },
    ],
  },
  {
    id: "courses",
    name: "Courses & Content",
    icon: "book",
    articles: [
      { id: 5, title: "Accessing course materials", views: 2134 },
      { id: 6, title: "Downloading resources for offline use", views: 1876 },
      { id: 7, title: "Troubleshooting video playback issues", views: 1543 },
      { id: 8, title: "How to submit assignments", views: 1245 },
    ],
  },
  {
    id: "billing",
    name: "Billing & Payments",
    icon: "credit-card",
    articles: [
      { id: 9, title: "Managing your payment methods", views: 985 },
      { id: 10, title: "Understanding your invoice", views: 743 },
      { id: 11, title: "Requesting a refund", views: 692 },
      { id: 12, title: "Subscription plans explained", views: 534 },
    ],
  },
  {
    id: "technical",
    name: "Technical Issues",
    icon: "tool",
    articles: [
      { id: 13, title: "Fixing login problems", views: 1876 },
      { id: 14, title: "Browser compatibility issues", views: 1453 },
      { id: 15, title: "Clearing your cache", views: 1201 },
      { id: 16, title: "Mobile app troubleshooting", views: 987 },
    ],
  },
]);

// Popular FAQs
const popularFaqs = ref([
  {
    id: 1,
    question: "How do I reset my password?",
    answer:
      'To reset your password, click on the "Forgot Password" link on the login page. You will receive an email with instructions to create a new password. If you don\'t receive the email, check your spam folder or contact support.',
    category: "account",
    isOpen: false,
  },
  {
    id: 2,
    question: "Why can't I access my course?",
    answer:
      "There could be several reasons: your subscription might have expired, the course might be temporarily unavailable for maintenance, or there might be a technical issue. Check your enrollment status in your dashboard. If everything seems correct, try clearing your browser cache or contact support for assistance.",
    category: "courses",
    isOpen: false,
  },
  {
    id: 3,
    question: "How do I download course materials for offline viewing?",
    answer:
      "For downloadable resources, look for the download icon next to the resource name. For video content, some courses offer offline viewing through our mobile app. Note that not all content is available for offline access due to copyright restrictions.",
    category: "courses",
    isOpen: false,
  },
  {
    id: 4,
    question: "When will I receive my certificate after completing a course?",
    answer:
      'Certificates are automatically generated and issued within 24 hours of completing all required course components, including quizzes and assignments. You can find your certificates in the "Certificates" section of your dashboard.',
    category: "courses",
    isOpen: false,
  },
  {
    id: 5,
    question: "How do I update my billing information?",
    answer:
      "You can update your billing information by going to Settings > Payment Methods. There you can add, edit, or remove payment methods, and update your billing address.",
    category: "billing",
    isOpen: false,
  },
]);

// Contact form state
const contactForm = reactive({
  issueType: "",
  subject: "",
  description: "",
  attachments: [],
  courseId: "",
  priority: "medium",
});

// Form validation and submission state
const formErrors = reactive({
  issueType: "",
  subject: "",
  description: "",
});

const isSubmitting = ref(false);
const submissionStatus = ref("");
const selectedCategory = ref("general");

// Recently submitted tickets
const recentTickets = ref([
  {
    id: "TKT-12345",
    subject: "Cannot access video content in React course",
    status: "open",
    priority: "high",
    created: "2025-04-10T14:30:00",
    lastUpdate: "2025-04-12T09:15:00",
    category: "technical",
  },
  {
    id: "TKT-12340",
    subject: "Question about assignment deadline extension",
    status: "closed",
    priority: "medium",
    created: "2025-04-02T10:20:00",
    lastUpdate: "2025-04-03T16:45:00",
    category: "courses",
  },
  {
    id: "TKT-12335",
    subject: "Invoice correction request",
    status: "pending",
    priority: "low",
    created: "2025-03-28T11:30:00",
    lastUpdate: "2025-03-29T13:20:00",
    category: "billing",
  },
]);

// Enrolled courses for selection in contact form
const enrolledCourses = ref([
  { id: 1, title: "Complete Web Development Bootcamp" },
  { id: 2, title: "UI/UX Design Masterclass" },
  { id: 3, title: "JavaScript Algorithms and Data Structures" },
  { id: 4, title: "Mobile App Development with React Native" },
]);

// Search functionality
const searchQuery = ref("");
const isSearching = ref(false);
const searchResults = ref([]);

// Toggle FAQ accordion
const toggleFaq = (id: number) => {
  const faq = popularFaqs.value.find((f) => f.id === id);
  if (faq) {
    faq.isOpen = !faq.isOpen;
  }
};

// Handle form submit
const submitSupportRequest = () => {
  // Reset errors
  formErrors.issueType = "";
  formErrors.subject = "";
  formErrors.description = "";

  // Validate form
  let isValid = true;

  if (!contactForm.issueType) {
    formErrors.issueType = "Please select an issue type";
    isValid = false;
  }

  if (!contactForm.subject.trim()) {
    formErrors.subject = "Please enter a subject";
    isValid = false;
  }

  if (!contactForm.description.trim() || contactForm.description.length < 20) {
    formErrors.description =
      "Please provide a detailed description (at least 20 characters)";
    isValid = false;
  }

  if (isValid) {
    isSubmitting.value = true;
    submissionStatus.value = "";

    // Simulate API call
    setTimeout(() => {
      isSubmitting.value = false;
      submissionStatus.value = "success";

      // Reset form after successful submission
      contactForm.issueType = "";
      contactForm.subject = "";
      contactForm.description = "";
      contactForm.attachments = [];
      contactForm.courseId = "";
      contactForm.priority = "medium";

      // Add the new ticket to recent tickets
      const newTicket = {
        id: `TKT-${Math.floor(10000 + Math.random() * 90000)}`,
        subject: contactForm.subject,
        status: "open",
        priority: contactForm.priority,
        created: new Date().toISOString(),
        lastUpdate: new Date().toISOString(),
        category: contactForm.issueType,
      };

      recentTickets.value.unshift(newTicket);

      // Reset form after 3 seconds
      setTimeout(() => {
        submissionStatus.value = "";
      }, 3000);
    }, 1500);
  }
};

// Handle file upload
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    const files = Array.from(input.files);
    contactForm.attachments = [...contactForm.attachments, ...files];
  }
};

// Remove uploaded file
const removeFile = (index: number) => {
  contactForm.attachments.splice(index, 1);
};

// Search articles and FAQs
const searchKnowledgeBase = () => {
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    return;
  }

  isSearching.value = true;
  searchResults.value = [];

  // Simulate search delay
  setTimeout(() => {
    const query = searchQuery.value.toLowerCase();

    // Search in support articles
    const articleResults = supportCategories.value.flatMap((category) =>
      category.articles
        .filter((article) => article.title.toLowerCase().includes(query))
        .map((article) => ({
          id: `article-${article.id}`,
          title: article.title,
          type: "article",
          category: category.name,
        }))
    );

    // Search in FAQs
    const faqResults = popularFaqs.value
      .filter(
        (faq) =>
          faq.question.toLowerCase().includes(query) ||
          faq.answer.toLowerCase().includes(query)
      )
      .map((faq) => ({
        id: `faq-${faq.id}`,
        title: faq.question,
        type: "faq",
        category:
          supportCategories.value.find((c) => c.id === faq.category)?.name ||
          "General",
      }));

    // Combine results
    searchResults.value = [...articleResults, ...faqResults];
    isSearching.value = false;
  }, 500);
};

// Format date utility function
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

// Get status color class
const getStatusColor = (status: string) => {
  switch (status) {
    case "open":
      return "bg-blue-100 text-blue-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "in-progress":
      return "bg-indigo-100 text-indigo-800";
    case "resolved":
      return "bg-green-100 text-green-800";
    case "closed":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get priority color class
const getPriorityColor = (priority: string) => {
  switch (priority) {
    case "high":
      return "bg-red-100 text-red-800";
    case "medium":
      return "bg-yellow-100 text-yellow-800";
    case "low":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get icon based on ID
const getIcon = (iconId: string) => {
  switch (iconId) {
    case "user":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>`;
    case "book":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>`;
    case "credit-card":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
      </svg>`;
    case "tool":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>`;
  }
};

// Animation state
const isLoaded = ref(false);

onMounted(() => {
  // Small delay for loading animation
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});
</script>

<template>
  <div class="support-page pb-12">
    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Help & Support</h1>
      <p class="text-gray-600">
        Find answers, get help, and contact our support team
      </p>
    </div>

    <!-- Main support container -->
    <div class="space-y-8">
      <!-- Top section: Search and quick help -->
      <div
        class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 transition-all duration-300 transform opacity-0 translate-y-4"
        :class="{ 'opacity-100 translate-y-0': isLoaded }"
        style="transition-delay: 100ms"
      >
        <div
          class="p-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white"
        >
          <h2 class="text-xl font-bold mb-2">How can we help you?</h2>
          <p class="mb-4 text-indigo-100">
            Search our knowledge base or browse popular topics below
          </p>

          <!-- Search bar -->
          <div class="relative max-w-2xl">
            <input
              v-model="searchQuery"
              type="text"
              class="w-full py-3 pl-12 pr-4 text-gray-700 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-md"
              placeholder="Search for help articles, FAQs, or topics..."
              @keyup.enter="searchKnowledgeBase"
            />
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
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
              class="absolute inset-y-0 right-0 flex items-center px-4 text-indigo-600 bg-indigo-100 hover:bg-indigo-200 rounded-r-lg transition-colors"
              @click="searchKnowledgeBase"
            >
              Search
            </button>
          </div>

          <!-- Search results -->
          <div
            v-if="searchQuery && (searchResults.length > 0 || isSearching)"
            class="mt-4 bg-white rounded-lg p-4 text-gray-800 shadow-lg max-w-2xl"
          >
            <div v-if="isSearching" class="flex justify-center py-4">
              <div
                class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"
              ></div>
            </div>

            <div v-else>
              <h3 class="font-medium text-gray-900 mb-2">Search Results</h3>
              <ul class="divide-y divide-gray-200">
                <li
                  v-for="result in searchResults"
                  :key="result.id"
                  class="py-2"
                >
                  <a
                    href="#"
                    class="hover:text-indigo-600 transition-colors flex items-start"
                  >
                    <span
                      class="inline-block px-2 py-1 text-xs rounded mr-2"
                      :class="
                        result.type === 'article'
                          ? 'bg-blue-100 text-blue-800'
                          : 'bg-green-100 text-green-800'
                      "
                    >
                      {{ result.type === "article" ? "Article" : "FAQ" }}
                    </span>
                    <div>
                      <span class="font-medium">{{ result.title }}</span>
                      <span class="text-xs text-gray-500 block">{{
                        result.category
                      }}</span>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Support categories -->
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Browse Support Categories
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
              v-for="category in supportCategories"
              :key="category.id"
              class="bg-gray-50 hover:bg-indigo-50 border border-gray-200 rounded-lg p-4 transition-all duration-300 hover:shadow-md transform hover:-translate-y-1"
            >
              <div class="flex items-start">
                <div
                  class="flex-shrink-0 p-2 rounded-md bg-indigo-100 text-indigo-600 mr-3"
                  v-html="getIcon(category.icon)"
                ></div>
                <div>
                  <h4 class="font-medium text-gray-900">{{ category.name }}</h4>
                  <p class="text-sm text-gray-500 mb-2">
                    {{ category.articles.length }} articles
                  </p>
                  <ul class="space-y-1">
                    <li
                      v-for="article in category.articles.slice(0, 2)"
                      :key="article.id"
                      class="text-sm"
                    >
                      <a
                        href="#"
                        class="text-indigo-600 hover:text-indigo-800 hover:underline transition-colors"
                      >
                        {{ article.title }}
                      </a>
                    </li>
                  </ul>
                  <a
                    href="#"
                    class="text-xs text-indigo-600 hover:text-indigo-800 mt-2 inline-block"
                    >View all articles →</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Middle section: FAQs -->
      <div
        class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 transition-all duration-300 transform opacity-0 translate-y-4"
        :class="{ 'opacity-100 translate-y-0': isLoaded }"
        style="transition-delay: 200ms"
      >
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">Popular FAQs</h2>
        </div>

        <div class="divide-y divide-gray-200">
          <div v-for="faq in popularFaqs" :key="faq.id" class="p-6">
            <button
              @click="toggleFaq(faq.id)"
              class="flex justify-between items-center w-full text-left focus:outline-none"
            >
              <h3 class="text-lg font-medium text-gray-900">
                {{ faq.question }}
              </h3>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-500 transform transition-transform duration-200"
                :class="{ 'rotate-180': faq.isOpen }"
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
              v-show="faq.isOpen"
              class="mt-4 text-gray-600 bg-gray-50 rounded-lg p-4 animate-fade-in"
            >
              <p>{{ faq.answer }}</p>
            </div>
          </div>
        </div>

        <div class="p-6 bg-gray-50 text-center">
          <a
            href="#"
            class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center justify-center"
          >
            View all FAQs
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 ml-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </a>
        </div>
      </div>

      <!-- Bottom section: Contact form and tickets -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact form -->
        <div
          class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 transition-all duration-300 transform opacity-0 translate-y-4"
          :class="{ 'opacity-100 translate-y-0': isLoaded }"
          style="transition-delay: 300ms"
        >
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Contact Support</h2>
            <p class="text-gray-600 mt-1">
              Fill out the form below to submit a support ticket
            </p>
          </div>

          <div class="p-6">
            <!-- Success message -->
            <div
              v-if="submissionStatus === 'success'"
              class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 animate-fade-in"
            >
              <div class="flex">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-3 text-green-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <div>
                  <p class="font-medium">
                    Support ticket submitted successfully!
                  </p>
                  <p class="text-sm mt-1">
                    We'll respond to your request as soon as possible.
                  </p>
                </div>
              </div>
            </div>

            <form @submit.prevent="submitSupportRequest" class="space-y-6">
              <!-- Issue type selector -->
              <div>
                <label
                  for="issueType"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Issue Type</label
                >
                <select
                  id="issueType"
                  v-model="contactForm.issueType"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="">Select an issue type</option>
                  <option value="account">Account & Profile</option>
                  <option value="courses">Courses & Content</option>
                  <option value="billing">Billing & Payments</option>
                  <option value="technical">Technical Issue</option>
                  <option value="other">Other</option>
                </select>
                <p
                  v-if="formErrors.issueType"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ formErrors.issueType }}
                </p>
              </div>

              <!-- Related course selector (if applicable) -->
              <div
                v-if="
                  contactForm.issueType === 'courses' ||
                  contactForm.issueType === 'technical'
                "
              >
                <label
                  for="courseId"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Related Course (if applicable)</label
                >
                <select
                  id="courseId"
                  v-model="contactForm.courseId"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="">Select a course</option>
                  <option
                    v-for="course in enrolledCourses"
                    :key="course.id"
                    :value="course.id"
                  >
                    {{ course.title }}
                  </option>
                </select>
              </div>

              <!-- Priority selector -->
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
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>

              <!-- Subject field -->
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
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Brief description of your issue"
                />
                <p v-if="formErrors.subject" class="mt-1 text-sm text-red-600">
                  {{ formErrors.subject }}
                </p>
              </div>

              <!-- Description field -->
              <div>
                <label
                  for="description"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Description</label
                >
                <textarea
                  id="description"
                  v-model="contactForm.description"
                  rows="5"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Please provide as much detail as possible..."
                ></textarea>
                <p
                  v-if="formErrors.description"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ formErrors.description }}
                </p>
              </div>

              <!-- File attachments -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Attachments (optional)</label
                >
                <div
                  class="flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                >
                  <div class="space-y-1 text-center">
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
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                      />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label
                        for="file-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                      >
                        <span>Upload files</span>
                        <input
                          id="file-upload"
                          name="file-upload"
                          type="file"
                          class="sr-only"
                          multiple
                          @change="handleFileUpload"
                        />
                      </label>
                      <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                      PNG, JPG, GIF, PDF up to 10MB
                    </p>
                  </div>
                </div>

                <!-- Attachment list -->
                <ul
                  v-if="contactForm.attachments.length > 0"
                  class="mt-2 divide-y divide-gray-200 border border-gray-200 rounded-md"
                >
                  <li
                    v-for="(file, index) in contactForm.attachments"
                    :key="index"
                    class="pl-3 pr-4 py-3 flex items-center justify-between text-sm"
                  >
                    <div class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                        />
                      </svg>
                      <span class="ml-2 flex-1 w-0 truncate">{{
                        file.name
                      }}</span>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                      <button
                        type="button"
                        @click="removeFile(index)"
                        class="font-medium text-red-600 hover:text-red-500"
                      >
                        Remove
                      </button>
                    </div>
                  </li>
                </ul>
              </div>

              <!-- Submit button -->
              <div>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full"
                  :disabled="isSubmitting"
                >
                  <svg
                    v-if="isSubmitting"
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
                  {{
                    isSubmitting ? "Submitting..." : "Submit Support Request"
                  }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Recent tickets -->
        <div
          class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 transition-all duration-300 transform opacity-0 translate-y-4"
          :class="{ 'opacity-100 translate-y-0': isLoaded }"
          style="transition-delay: 400ms"
        >
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">
              Recent Support Tickets
            </h2>
          </div>

          <div class="divide-y divide-gray-200">
            <div
              v-for="ticket in recentTickets"
              :key="ticket.id"
              class="p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex justify-between items-start">
                <h3 class="font-medium text-gray-900">{{ ticket.subject }}</h3>
                <span
                  class="px-2 py-1 text-xs rounded-full"
                  :class="getStatusColor(ticket.status)"
                >
                  {{
                    ticket.status.charAt(0).toUpperCase() +
                    ticket.status.slice(1)
                  }}
                </span>
              </div>

              <div class="mt-2 flex items-center text-sm text-gray-500">
                <span>Ticket ID: {{ ticket.id }}</span>
                <span class="mx-2">•</span>
                <span>{{ formatDate(ticket.created) }}</span>
              </div>

              <div class="mt-2 flex justify-between items-center">
                <span
                  class="px-2 py-1 text-xs rounded-full"
                  :class="getPriorityColor(ticket.priority)"
                >
                  {{
                    ticket.priority.charAt(0).toUpperCase() +
                    ticket.priority.slice(1)
                  }}
                  Priority
                </span>

                <a
                  href="#"
                  class="text-sm text-indigo-600 hover:text-indigo-800"
                  >View details</a
                >
              </div>
            </div>
          </div>

          <div class="p-4 bg-gray-50 text-center">
            <a
              href="#"
              class="text-indigo-600 hover:text-indigo-800 font-medium"
              >View all tickets</a
            >
          </div>
        </div>
      </div>

      <!-- Live chat and alternative contact options -->
      <div
        class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 transition-all duration-300 transform opacity-0 translate-y-4"
        :class="{ 'opacity-100 translate-y-0': isLoaded }"
        style="transition-delay: 500ms"
      >
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">
            Additional Ways to Reach Us
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Live chat -->
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
              <div class="flex">
                <div
                  class="flex-shrink-0 bg-indigo-100 p-2 rounded-full text-indigo-600 mr-4"
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
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                    />
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900 mb-1">Live Chat</h3>
                  <p class="text-sm text-gray-600 mb-3">
                    Chat with our support team in real-time
                  </p>
                  <button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition-colors shadow-sm"
                  >
                    Start Chat
                  </button>
                </div>
              </div>
            </div>

            <!-- Email support -->
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
              <div class="flex">
                <div
                  class="flex-shrink-0 bg-blue-100 p-2 rounded-full text-blue-600 mr-4"
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
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    />
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900 mb-1">Email Support</h3>
                  <p class="text-sm text-gray-600 mb-3">
                    Email us directly for complex issues
                  </p>
                  <a
                    href="mailto:support@learnconsult.com"
                    class="text-blue-600 hover:text-blue-800 font-medium"
                  >
                    support@learnconsult.com
                  </a>
                </div>
              </div>
            </div>

            <!-- Phone support -->
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
              <div class="flex">
                <div
                  class="flex-shrink-0 bg-green-100 p-2 rounded-full text-green-600 mr-4"
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
                <div>
                  <h3 class="font-medium text-gray-900 mb-1">Phone Support</h3>
                  <p class="text-sm text-gray-600 mb-3">
                    Call us during business hours (9am-5pm ET)
                  </p>
                  <a
                    href="tel:+15551234567"
                    class="text-green-600 hover:text-green-800 font-medium"
                  >
                    +1 (555) 123-4567
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation classes */
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

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* Transition delays for staggered animations */
.delay-100 {
  transition-delay: 100ms;
}

.delay-200 {
  transition-delay: 200ms;
}

.delay-300 {
  transition-delay: 300ms;
}

.delay-400 {
  transition-delay: 400ms;
}

.delay-500 {
  transition-delay: 500ms;
}
</style>
