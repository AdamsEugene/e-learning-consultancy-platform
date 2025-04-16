<!-- pages/dashboard/client/support.vue -->
<script setup lang="ts">
// Set dashboard-client layout for this page
definePageMeta({
  layout: "dashboard-client",
});

// Set page meta info
useHead({
  title: "Help & Support - Client Dashboard",
  meta: [
    {
      name: "description",
      content: "Get help and support with your projects and platform features",
    },
  ],
});

// State for active support topic and search
const searchQuery = ref("");
const activeCategory = ref("getting-started");
const isContactModalOpen = ref(false);
const supportMessage = ref({
  subject: "",
  description: "",
  priority: "medium",
  attachments: [],
});
const isSubmitting = ref(false);
const showSuccessMessage = ref(false);

// Support categories
const supportCategories = [
  { id: "getting-started", label: "Getting Started", icon: "flag" },
  { id: "projects", label: "Projects", icon: "briefcase" },
  { id: "consultants", label: "Finding Consultants", icon: "users" },
  { id: "payments", label: "Payments & Billing", icon: "cash" },
  { id: "technical", label: "Technical Issues", icon: "code" },
  { id: "account", label: "Account Settings", icon: "user" },
];

// FAQ data organized by category
const faqItems = {
  "getting-started": [
    {
      question: "How do I get started with my first project?",
      answer:
        'To start your first project, navigate to the "Project Requests" section and click on "New Project". Fill in the required details about your project needs, including scope, timeframe, and budget. Once submitted, our team will match you with suitable consultants.',
    },
    {
      question: "What information should I include in my project brief?",
      answer:
        "A good project brief should include: clear objectives, detailed requirements, expected deliverables, timeline constraints, budget range, and any specific skills or expertise needed. The more detailed your brief, the better we can match you with the right consultants.",
    },
    {
      question: "How does the consultant matching process work?",
      answer:
        "Our matching algorithm considers several factors: consultant expertise, availability, past project success rates, client ratings, and your specific requirements. You'll receive recommendations within 24-48 hours, after which you can review profiles and schedule initial consultations.",
    },
  ],
  projects: [
    {
      question: "How do I track the progress of my project?",
      answer:
        'You can track your project progress in the "My Projects" section of your dashboard. Each project has a dedicated page showing milestones, deliverables, timeline, and communication history. You can also set up email notifications for important updates.',
    },
    {
      question: "Can I modify my project scope after it has started?",
      answer:
        'Yes, project scopes can be modified. Navigate to your project page, select "Request Change", and detail your requested modifications. Your consultant will review the changes and may adjust timelines or costs if necessary. Both parties must agree to scope changes.',
    },
    {
      question: "What happens if I'm not satisfied with the project results?",
      answer:
        "We have a satisfaction guarantee policy. If you're not satisfied, first discuss your concerns directly with your consultant. If issues remain unresolved, contact our support team through this portal. We'll mediate and may offer revision work, partial refunds, or consultant replacement depending on the situation.",
    },
  ],
  consultants: [
    {
      question: "How are consultants vetted on the platform?",
      answer:
        "All consultants undergo a rigorous vetting process: credentials verification, portfolio review, technical assessments, case study analysis, and background checks. Only the top 10% of applicants become platform consultants. We also continuously monitor performance and client satisfaction.",
    },
    {
      question: "Can I request a specific consultant for my project?",
      answer:
        "Yes, if you have a preferred consultant, you can request them specifically when creating your project. If they're available, they'll be notified of your request. You can find consultants to request through our \"Consultants\" directory or from your past projects.",
    },
    {
      question:
        "What if my assigned consultant becomes unavailable mid-project?",
      answer:
        "If a consultant becomes unavailable, we immediately notify you and provide options: pause the project until they return (for short absences), transfer to an associate of their team, or match you with a new consultant. We ensure a smooth handover process with all project knowledge transferred.",
    },
  ],
  payments: [
    {
      question: "What payment methods are accepted?",
      answer:
        "We accept major credit/debit cards (Visa, Mastercard, American Express), bank transfers, PayPal, and some cryptocurrencies. Payment options may vary by region. For enterprise clients, we also offer invoicing with net-30 payment terms.",
    },
    {
      question: "How does the milestone payment system work?",
      answer:
        "Projects are typically broken into milestones. Payments are held in escrow and released when you approve each milestone completion. This ensures you only pay for completed and satisfactory work. You can set up automatic approvals or review each milestone manually.",
    },
    {
      question: "What happens to my payment if a project is cancelled?",
      answer:
        "If a project is cancelled, unused funds held in escrow are returned to you within 3-5 business days. For work already completed, consultants receive appropriate compensation. Our dispute resolution team can help determine fair payment allocation if there's disagreement.",
    },
  ],
  technical: [
    {
      question: "What browsers and devices are supported?",
      answer:
        "Our platform supports all modern browsers (Chrome, Firefox, Safari, Edge) and devices. For the best experience, we recommend using the latest version of Chrome or Firefox. The platform is fully responsive and works on desktop, tablet, and mobile devices.",
    },
    {
      question: "How secure is my data on the platform?",
      answer:
        "We implement bank-level security measures: end-to-end encryption, secure socket layers (SSL), two-factor authentication, regular security audits, and compliance with GDPR and other privacy regulations. Your data is backed up daily and stored in redundant, secure data centers.",
    },
    {
      question: "What should I do if I experience technical issues?",
      answer:
        'For technical issues, first try clearing your browser cache and cookies, or use an alternative browser. If problems persist, contact our technical support through the "Contact Support" button on this page. Include details like your device, browser, and screenshots of any error messages.',
    },
  ],
  account: [
    {
      question: "How do I update my company information?",
      answer:
        'To update company information, go to "Settings" in your dashboard, then select "Company Profile." You can edit your company name, address, billing details, logo, and other information. Changes are saved immediately but may take up to 24 hours to appear throughout the platform.',
    },
    {
      question: "Can I add team members to my account?",
      answer:
        'Yes, you can add team members with different access levels. Go to "Settings" > "Team Members" and click "Add Member." Enter their email address, select appropriate permissions (admin, project manager, viewer), and they\'ll receive an invitation to join your workspace.',
    },
    {
      question: "How do I change notification preferences?",
      answer:
        'To manage notifications, go to "Settings" > "Notifications." You can customize email, in-app, and mobile notifications for different events (project updates, messages, etc.). You can also set up daily or weekly digest summaries instead of individual notifications.',
    },
  ],
};

// Knowledge base articles
const knowledgeBaseArticles = [
  {
    id: 1,
    title: "Creating your first project request",
    category: "getting-started",
    readTime: "5 min read",
    excerpt:
      "Learn how to create a detailed project request that helps match you with the perfect consultant.",
    link: "/knowledge-base/creating-project-request",
  },
  {
    id: 2,
    title: "Understanding project milestones",
    category: "projects",
    readTime: "7 min read",
    excerpt:
      "A guide to project milestones, how they work, and why they're important for successful project delivery.",
    link: "/knowledge-base/project-milestones",
  },
  {
    id: 3,
    title: "Consultant selection process",
    category: "consultants",
    readTime: "6 min read",
    excerpt:
      "The complete guide to our consultant vetting process and how we ensure quality expertise.",
    link: "/knowledge-base/consultant-selection",
  },
  {
    id: 4,
    title: "Payment methods and security",
    category: "payments",
    readTime: "4 min read",
    excerpt:
      "Overview of available payment methods and our robust security measures to protect your transactions.",
    link: "/knowledge-base/payment-security",
  },
  {
    id: 5,
    title: "Platform compatibility guide",
    category: "technical",
    readTime: "3 min read",
    excerpt:
      "Details on supported browsers, devices, and troubleshooting common technical issues.",
    link: "/knowledge-base/platform-compatibility",
  },
  {
    id: 6,
    title: "Managing team access and permissions",
    category: "account",
    readTime: "8 min read",
    excerpt:
      "How to add team members and configure appropriate access levels for your organization.",
    link: "/knowledge-base/team-access",
  },
];

// Recent help topics for quick access
const recentHelpTopics = [
  {
    title: "Invoice generation and management",
    link: "/knowledge-base/invoice-management",
    icon: "document-text",
  },
  {
    title: "Communication tools and best practices",
    link: "/knowledge-base/communication-tools",
    icon: "chat",
  },
  {
    title: "Using the resource library",
    link: "/knowledge-base/resource-library",
    icon: "library",
  },
  {
    title: "Understanding consultant ratings",
    link: "/knowledge-base/consultant-ratings",
    icon: "star",
  },
];

// Filter knowledge base articles based on search and category
const filteredArticles = computed(() => {
  let results = [...knowledgeBaseArticles];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(
      (article) =>
        article.title.toLowerCase().includes(query) ||
        article.excerpt.toLowerCase().includes(query)
    );
    return results;
  }

  if (activeCategory.value !== "all") {
    results = results.filter(
      (article) => article.category === activeCategory.value
    );
  }

  return results;
});

// Filter FAQ items based on search and category
const filteredFaqItems = computed(() => {
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    const results = [];

    // Search across all categories
    for (const category in faqItems) {
      const categoryItems = faqItems[category].filter(
        (item) =>
          item.question.toLowerCase().includes(query) ||
          item.answer.toLowerCase().includes(query)
      );

      results.push(...categoryItems);
    }

    return results;
  }

  return faqItems[activeCategory.value] || [];
});

// Get icon for category
const getCategoryIcon = (categoryId: string) => {
  const category = supportCategories.find((c) => c.id === categoryId);
  return renderIcon(category?.icon || "help-circle");
};

// Render icon based on name
const renderIcon = (iconName: string) => {
  switch (iconName) {
    case "flag":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
      </svg>`;
    case "briefcase":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
      </svg>`;
    case "cash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "code":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>`;
    case "user":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
      </svg>`;
    case "document-text":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "chat":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
      </svg>`;
    case "library":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>`;
    case "star":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>`;
    case "phone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
      </svg>`;
    case "mail":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
      </svg>`;
    case "video-camera":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
        <path d="M14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "help-circle":
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Toggle accordion for FAQ items
const toggleFaq = (index: number) => {
  const faqItem = document.getElementById(`faq-${index}`);
  const faqAnswer = document.getElementById(`faq-answer-${index}`);

  if (faqItem && faqAnswer) {
    faqItem.classList.toggle("faq-active");

    if (faqAnswer.style.maxHeight) {
      faqAnswer.style.maxHeight = "";
    } else {
      faqAnswer.style.maxHeight = faqAnswer.scrollHeight + "px";
    }
  }
};

// Open contact support modal
const openContactModal = () => {
  isContactModalOpen.value = true;
};

// Close contact support modal
const closeContactModal = () => {
  isContactModalOpen.value = false;
  // Reset form after a delay to allow close animation
  setTimeout(() => {
    supportMessage.value = {
      subject: "",
      description: "",
      priority: "medium",
      attachments: [],
    };
    showSuccessMessage.value = false;
  }, 300);
};

// Submit support message
const submitSupportMessage = () => {
  isSubmitting.value = true;

  // Simulate API call
  setTimeout(() => {
    isSubmitting.value = false;
    showSuccessMessage.value = true;

    // Close modal after showing success message
    setTimeout(() => {
      closeContactModal();
    }, 3000);
  }, 1500);
};

// Handle file upload for attachments
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files.length > 0) {
    // In a real app, you would upload the files to your server
    // Here we just store the file names
    for (let i = 0; i < input.files.length; i++) {
      supportMessage.value.attachments.push(input.files[i].name);
    }
  }
};

// Remove attachment
const removeAttachment = (index: number) => {
  supportMessage.value.attachments.splice(index, 1);
};
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row items-start md:items-center justify-between"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Help & Support</h1>
          <p class="text-gray-600">
            Find answers to your questions or contact our support team
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <button
            @click="openContactModal"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Contact Support</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Contact options -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
      <h2 class="text-lg font-semibold mb-4">Contact Options</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Email support -->
        <div class="flex items-start">
          <div
            class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center"
          >
            <span class="text-indigo-600" v-html="renderIcon('mail')"></span>
          </div>
          <div class="ml-4">
            <h3 class="font-medium">Email Support</h3>
            <p class="text-sm text-gray-600 mb-1">
              Available 24/7 with response in 24 hours
            </p>
            <a
              href="mailto:support@learnconsult.com"
              class="text-indigo-600 text-sm hover:underline"
              >support@learnconsult.com</a
            >
          </div>
        </div>

        <!-- Phone support -->
        <div class="flex items-start">
          <div
            class="h-10 w-10 flex-shrink-0 bg-green-100 rounded-full flex items-center justify-center"
          >
            <span class="text-green-600" v-html="renderIcon('phone')"></span>
          </div>
          <div class="ml-4">
            <h3 class="font-medium">Phone Support</h3>
            <p class="text-sm text-gray-600 mb-1">
              Monday-Friday, 9 AM - 5 PM ET
            </p>
            <a
              href="tel:+1-555-123-4567"
              class="text-indigo-600 text-sm hover:underline"
              >+1 (555) 123-4567</a
            >
          </div>
        </div>

        <!-- Video consultations -->
        <div class="flex items-start">
          <div
            class="h-10 w-10 flex-shrink-0 bg-purple-100 rounded-full flex items-center justify-center"
          >
            <span
              class="text-purple-600"
              v-html="renderIcon('video-camera')"
            ></span>
          </div>
          <div class="ml-4">
            <h3 class="font-medium">Video Consultation</h3>
            <p class="text-sm text-gray-600 mb-1">
              Schedule a 30-minute call with support
            </p>
            <button
              class="text-indigo-600 text-sm hover:underline"
              @click="openContactModal"
            >
              Schedule now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
      <div class="max-w-xl mx-auto">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search articles and FAQs..."
            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
          <div class="absolute left-3 top-3.5 text-gray-400">
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
        </div>
      </div>
    </div>

    <!-- Recent help topics -->
    <div v-if="!searchQuery" class="mb-8">
      <h2 class="text-lg font-semibold mb-4">Recent Help Topics</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a
          v-for="topic in recentHelpTopics"
          :key="topic.title"
          :href="topic.link"
          class="bg-white rounded-lg border border-gray-200 p-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200 flex items-start"
        >
          <div
            class="h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3"
          >
            <span
              class="text-indigo-600"
              v-html="renderIcon(topic.icon)"
            ></span>
          </div>
          <span class="font-medium">{{ topic.title }}</span>
        </a>
      </div>
    </div>

    <!-- Main content with tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Category sidebar (only shown when not searching) -->
      <div v-if="!searchQuery" class="lg:col-span-1">
        <div
          class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
        >
          <h3 class="font-medium p-4 border-b border-gray-200 bg-gray-50">
            Categories
          </h3>
          <ul>
            <li v-for="category in supportCategories" :key="category.id">
              <button
                @click="activeCategory = category.id"
                class="w-full px-4 py-3 text-left flex items-center space-x-3 hover:bg-gray-50 transition-colors"
                :class="
                  activeCategory === category.id
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-gray-700'
                "
              >
                <span
                  class="text-indigo-600"
                  v-html="renderIcon(category.icon)"
                ></span>
                <span>{{ category.label }}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>

      <!-- Main content area -->
      <div class="lg:col-span-3">
        <!-- FAQ Section -->
        <div
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8"
        >
          <div class="mb-6 flex items-center">
            <span
              class="text-indigo-600 mr-2"
              v-html="getCategoryIcon(activeCategory)"
            ></span>
            <h2 class="text-xl font-semibold">
              {{
                searchQuery
                  ? "Search Results"
                  : supportCategories.find((c) => c.id === activeCategory)
                      ?.label
              }}
              FAQ
            </h2>
          </div>

          <div v-if="filteredFaqItems.length === 0" class="text-center py-8">
            <div
              class="mx-auto h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-gray-400"
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
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              No results found
            </h3>
            <p class="text-gray-600">
              Try using different keywords or browsing the categories
            </p>
          </div>

          <!-- FAQ Questions -->
          <div v-else class="space-y-4">
            <div
              v-for="(item, index) in filteredFaqItems"
              :key="index"
              :id="`faq-${index}`"
              class="border border-gray-200 rounded-lg overflow-hidden"
            >
              <button
                @click="toggleFaq(index)"
                class="w-full p-4 flex justify-between items-center text-left bg-gray-50 hover:bg-gray-100 transition-colors"
              >
                <span class="font-medium">{{ item.question }}</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500 transition-transform duration-200"
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
                :id="`faq-answer-${index}`"
                class="overflow-hidden transition-all duration-300 max-h-0"
              >
                <div class="p-4 bg-white">
                  <p class="text-gray-700">{{ item.answer }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Knowledge Base Articles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold mb-6">
            {{ searchQuery ? "Related Articles" : "Knowledge Base Articles" }}
          </h2>

          <div v-if="filteredArticles.length === 0" class="text-center py-8">
            <div
              class="mx-auto h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-gray-400"
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
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              No articles found
            </h3>
            <p class="text-gray-600">
              Try using different keywords or browsing the categories
            </p>
          </div>

          <div v-else class="grid gap-6">
            <a
              v-for="article in filteredArticles"
              :key="article.id"
              :href="article.link"
              class="block p-5 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition-all duration-200"
            >
              <div class="flex items-start">
                <div class="flex-1">
                  <div class="flex items-center mb-2">
                    <span
                      class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full"
                    >
                      {{
                        supportCategories.find((c) => c.id === article.category)
                          ?.label
                      }}
                    </span>
                    <span class="text-gray-500 text-xs ml-3">{{
                      article.readTime
                    }}</span>
                  </div>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">
                    {{ article.title }}
                  </h3>
                  <p class="text-gray-600 mb-3">{{ article.excerpt }}</p>
                  <div
                    class="text-indigo-600 text-sm font-medium flex items-center"
                  >
                    Read more
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 ml-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Support Modal -->
    <div
      v-if="isContactModalOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
      >
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Contact Support</h2>
            <button
              @click="closeContactModal"
              class="text-gray-400 hover:text-gray-500 focus:outline-none"
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
        </div>

        <!-- Success message -->
        <div v-if="showSuccessMessage" class="p-6 text-center">
          <div
            class="mx-auto w-16 h-16 bg-green-100 flex items-center justify-center rounded-full mb-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 text-green-600"
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
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            Message Sent Successfully
          </h3>
          <p class="text-gray-600 mb-4">
            Our team will get back to you shortly. Thank you for your patience!
          </p>
          <p class="text-gray-600 text-sm">
            A confirmation has been sent to your email address.
          </p>
        </div>

        <!-- Form content -->
        <div v-else class="p-6">
          <div class="space-y-4">
            <!-- Subject -->
            <div>
              <label
                for="subject"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Subject</label
              >
              <input
                id="subject"
                v-model="supportMessage.subject"
                type="text"
                placeholder="How can we help you?"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Priority -->
            <div>
              <label
                for="priority"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Priority</label
              >
              <select
                id="priority"
                v-model="supportMessage.priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="low">Low - General question</option>
                <option value="medium">Medium - Need assistance</option>
                <option value="high">High - Issue affecting work</option>
                <option value="urgent">Urgent - Critical issue</option>
              </select>
            </div>

            <!-- Description -->
            <div>
              <label
                for="description"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <textarea
                id="description"
                v-model="supportMessage.description"
                rows="5"
                placeholder="Please provide details about your issue or question..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              ></textarea>
            </div>

            <!-- File attachments -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Attachments</label
              >
              <div
                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
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
                      d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
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
              <div v-if="supportMessage.attachments.length > 0" class="mt-3">
                <div
                  v-for="(file, index) in supportMessage.attachments"
                  :key="index"
                  class="flex items-center justify-between bg-gray-50 p-2 rounded-md mt-2"
                >
                  <span class="text-sm truncate">{{ file }}</span>
                  <button
                    @click="removeAttachment(index)"
                    class="text-red-500 hover:text-red-700 focus:outline-none"
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
          </div>
        </div>

        <!-- Form actions -->
        <div
          v-if="!showSuccessMessage"
          class="p-6 border-t border-gray-200 flex justify-end space-x-3"
        >
          <button
            @click="closeContactModal"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            @click="submitSupportMessage"
            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="
              isSubmitting ||
              !supportMessage.subject ||
              !supportMessage.description
            "
            :class="{
              'opacity-50 cursor-not-allowed':
                isSubmitting ||
                !supportMessage.subject ||
                !supportMessage.description,
            }"
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
            {{ isSubmitting ? "Sending..." : "Send Message" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* FAQ accordion styles */
#faq-answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease-out;
}

.faq-active svg {
  transform: rotate(180deg);
}

/* Animations */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(20px);
  opacity: 0;
}

/* Tooltip styles */
.tooltip {
  position: relative;
}

.tooltip:before {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  padding: 5px 10px;
  background-color: #374151;
  color: white;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s ease;
}

.tooltip:hover:before {
  opacity: 1;
}
</style>
