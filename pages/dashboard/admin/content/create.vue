<!-- pages/dashboard/admin/content/create.vue -->
<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue";

// Define page metadata using Nuxt's definePageMeta
definePageMeta({
  layout: "dashboard-admin",
});

// Set page title and meta
useHead({
  title: "Create Content - Admin Dashboard",
  meta: [
    { name: "description", content: "Create and publish new platform content" },
  ],
});

// Content types
const contentTypes = [
  { id: "article", label: "Article", icon: "document-text" },
  { id: "video", label: "Video Tutorial", icon: "video-camera" },
  { id: "ebook", label: "E-Book", icon: "book-open" },
  { id: "infographic", label: "Infographic", icon: "chart-bar" },
  { id: "case-study", label: "Case Study", icon: "light-bulb" },
  { id: "webinar", label: "Webinar", icon: "presentation-chart" },
  { id: "podcast", label: "Podcast", icon: "microphone" },
  { id: "resource", label: "Downloadable Resource", icon: "download" },
  { id: "announcement", label: "Platform Announcement", icon: "speakerphone" },
  { id: "faq", label: "FAQ", icon: "question-mark-circle" },
];

// Categories
const categories = [
  // E-Learning categories
  { id: "web-dev", label: "Web Development", group: "e-learning" },
  { id: "mobile-dev", label: "Mobile Development", group: "e-learning" },
  { id: "data-science", label: "Data Science", group: "e-learning" },
  { id: "design", label: "UI/UX Design", group: "e-learning" },
  { id: "devops", label: "DevOps & Cloud", group: "e-learning" },
  { id: "ai-ml", label: "AI & Machine Learning", group: "e-learning" },
  { id: "cybersecurity", label: "Cybersecurity", group: "e-learning" },
  { id: "blockchain", label: "Blockchain", group: "e-learning" },

  // Consultancy categories
  { id: "business-strategy", label: "Business Strategy", group: "consultancy" },
  {
    id: "digital-transformation",
    label: "Digital Transformation",
    group: "consultancy",
  },
  {
    id: "software-architecture",
    label: "Software Architecture",
    group: "consultancy",
  },
  {
    id: "product-management",
    label: "Product Management",
    group: "consultancy",
  },
  {
    id: "tech-leadership",
    label: "Technical Leadership",
    group: "consultancy",
  },
  { id: "agile-methodology", label: "Agile Methodology", group: "consultancy" },

  // General categories
  { id: "platform-updates", label: "Platform Updates", group: "general" },
  { id: "company-news", label: "Company News", group: "general" },
  { id: "success-stories", label: "Success Stories", group: "general" },
  { id: "tutorials", label: "Tutorials", group: "general" },
  { id: "industry-insights", label: "Industry Insights", group: "general" },
];

// Form state
const contentForm = reactive({
  title: "",
  slug: "",
  contentType: "article",
  category: "",
  tags: [] as string[],
  description: "",
  content: "",
  featuredImage: null as File | null,
  imageName: "",
  imagePreview: "",
  seoTitle: "",
  seoDescription: "",
  seoKeywords: "",
  isPublished: false,
  isFeatured: false,
  isForELearning: true,
  isForConsultancy: true,
  accessLevel: "all", // all, registered, premium
  publishDate: new Date().toISOString().slice(0, 16),
  expiryDate: "",
  authors: [] as { id: string; name: string }[],
  relatedContent: [] as string[],
  status: "draft", // draft, review, published, archived
});

// Tags input handling
const tagInput = ref("");
const addTag = () => {
  const tag = tagInput.value.trim();
  if (tag && !contentForm.tags.includes(tag)) {
    contentForm.tags.push(tag);
  }
  tagInput.value = "";
};
const removeTag = (tag: string) => {
  contentForm.tags = contentForm.tags.filter((t) => t !== tag);
};

// Handle slug generation
const generateSlug = () => {
  contentForm.slug = contentForm.title
    .toLowerCase()
    .replace(/[^\w\s-]/g, "")
    .replace(/\s+/g, "-")
    .replace(/--+/g, "-")
    .trim();
};

// Handle file uploads
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files.length > 0) {
    const file = input.files[0];
    contentForm.featuredImage = file;
    contentForm.imageName = file.name;

    // Create image preview
    const reader = new FileReader();
    reader.onload = (e) => {
      if (e.target) {
        contentForm.imagePreview = e.target.result as string;
      }
    };
    reader.readAsDataURL(file);
  }
};

// Remove uploaded image
const removeImage = () => {
  contentForm.featuredImage = null;
  contentForm.imageName = "";
  contentForm.imagePreview = "";
};

// Category options grouped
const groupedCategories = computed(() => {
  const groups = {
    "e-learning": { label: "E-Learning", categories: [] as typeof categories },
    consultancy: { label: "Consultancy", categories: [] as typeof categories },
    general: { label: "General", categories: [] as typeof categories },
  };

  categories.forEach((category) => {
    if (category.group in groups) {
      groups[category.group as keyof typeof groups].categories.push(category);
    }
  });

  return groups;
});

// Search and add authors
const authorSearchInput = ref("");
const searchResults = ref<
  { id: string; name: string; position: string; avatar: string }[]
>([]);
const isSearching = ref(false);

// Mock search authors function
const searchAuthors = () => {
  isSearching.value = true;
  // Simulate API call delay
  setTimeout(() => {
    // Mock search results
    searchResults.value = [
      {
        id: "1",
        name: "Jessica Parker",
        position: "Content Writer",
        avatar: "/api/placeholder/40/40",
      },
      {
        id: "2",
        name: "Michael Thompson",
        position: "Technical Editor",
        avatar: "/api/placeholder/40/40",
      },
      {
        id: "3",
        name: "Emma Watson",
        position: "Senior Designer",
        avatar: "/api/placeholder/40/40",
      },
      {
        id: "4",
        name: "David Chen",
        position: "Course Creator",
        avatar: "/api/placeholder/40/40",
      },
    ].filter(
      (author) =>
        author.name
          .toLowerCase()
          .includes(authorSearchInput.value.toLowerCase()) &&
        !contentForm.authors.some((a) => a.id === author.id)
    );
    isSearching.value = false;
  }, 500);
};

// Add author to content
const addAuthor = (author: { id: string; name: string }) => {
  if (!contentForm.authors.some((a) => a.id === author.id)) {
    contentForm.authors.push(author);
  }
  searchResults.value = [];
  authorSearchInput.value = "";
};

// Remove author from content
const removeAuthor = (authorId: string) => {
  contentForm.authors = contentForm.authors.filter(
    (author) => author.id !== authorId
  );
};

// Update SEO Title when main title changes
const updateSeoTitle = () => {
  if (!contentForm.seoTitle) {
    contentForm.seoTitle = contentForm.title;
  }
};

// Search and add related content
const contentSearchInput = ref("");
const contentSearchResults = ref<{ id: string; title: string; type: string }[]>(
  []
);
const isSearchingContent = ref(false);

// Mock search content function
const searchRelatedContent = () => {
  isSearchingContent.value = true;
  // Simulate API call delay
  setTimeout(() => {
    // Mock search results
    contentSearchResults.value = [
      { id: "c1", title: "Getting Started with Vue.js", type: "article" },
      { id: "c2", title: "Advanced JavaScript Techniques", type: "video" },
      { id: "c3", title: "UI/UX Design Principles", type: "ebook" },
      { id: "c4", title: "Cloud Architecture Overview", type: "webinar" },
    ].filter(
      (content) =>
        content.title
          .toLowerCase()
          .includes(contentSearchInput.value.toLowerCase()) &&
        !contentForm.relatedContent.includes(content.id)
    );
    isSearchingContent.value = false;
  }, 500);
};

// Add related content
const addRelatedContent = (contentId: string) => {
  if (!contentForm.relatedContent.includes(contentId)) {
    contentForm.relatedContent.push(contentId);
  }
};

// Remove related content
const removeRelatedContent = (contentId: string) => {
  contentForm.relatedContent = contentForm.relatedContent.filter(
    (id) => id !== contentId
  );
};

// Form validation
const formErrors = ref<Record<string, string>>({});
const validateForm = () => {
  const errors: Record<string, string> = {};

  if (!contentForm.title) errors.title = "Title is required";
  if (!contentForm.slug) errors.slug = "Slug is required";
  if (!contentForm.category) errors.category = "Category is required";
  if (!contentForm.description) errors.description = "Description is required";
  if (!contentForm.content) errors.content = "Content is required";

  formErrors.value = errors;
  return Object.keys(errors).length === 0;
};

// Get icon for content type
const getContentTypeIcon = (iconName: string) => {
  switch (iconName) {
    case "document-text":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "video-camera":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
        <path d="M14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
      </svg>`;
    case "chart-bar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
      </svg>`;
    case "light-bulb":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z" />
      </svg>`;
    case "presentation-chart":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
      </svg>`;
    case "microphone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd" />
      </svg>`;
    case "download":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>`;
    case "speakerphone":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd" />
      </svg>`;
    case "question-mark-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Submission handling
const isSubmitting = ref(false);
const showSuccessAlert = ref(false);

const submitForm = () => {
  if (!validateForm()) {
    // Scroll to first error
    const firstError = Object.keys(formErrors.value)[0];
    const element = document.getElementById(firstError);
    if (element) {
      element.scrollIntoView({ behavior: "smooth", block: "center" });
      element.focus();
    }
    return;
  }

  isSubmitting.value = true;

  // Simulate API call
  setTimeout(() => {
    // In a real app, this would submit the form data to the server
    console.log("Submitting content:", contentForm);

    // Show success message
    showSuccessAlert.value = true;
    isSubmitting.value = false;

    // Auto-hide success message after 5 seconds
    setTimeout(() => {
      showSuccessAlert.value = false;
    }, 5000);

    // Reset form in a real app if creating new content
    // resetForm();
  }, 1500);
};

// Reset form
const resetForm = () => {
  // Reset all form fields
  contentForm.title = "";
  contentForm.slug = "";
  contentForm.contentType = "article";
  contentForm.category = "";
  contentForm.tags = [];
  contentForm.description = "";
  contentForm.content = "";
  contentForm.featuredImage = null;
  contentForm.imageName = "";
  contentForm.imagePreview = "";
  contentForm.seoTitle = "";
  contentForm.seoDescription = "";
  contentForm.seoKeywords = "";
  contentForm.isPublished = false;
  contentForm.isFeatured = false;
  contentForm.isForELearning = true;
  contentForm.isForConsultancy = true;
  contentForm.accessLevel = "all";
  contentForm.publishDate = new Date().toISOString().slice(0, 16);
  contentForm.expiryDate = "";
  contentForm.authors = [];
  contentForm.relatedContent = [];
  contentForm.status = "draft";

  // Reset UI state
  formErrors.value = {};
  tagInput.value = "";
  authorSearchInput.value = "";
  contentSearchInput.value = "";
  searchResults.value = [];
  contentSearchResults.value = [];
};

// Get status badge style
const getStatusBadgeStyle = (status: string) => {
  switch (status) {
    case "draft":
      return "bg-amber-100 text-amber-800";
    case "review":
      return "bg-blue-100 text-blue-800";
    case "published":
      return "bg-green-100 text-green-800";
    case "archived":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format date helper
const formatDate = (dateString: string) => {
  if (!dateString) return "";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Mock data for related content
const mockRelatedContentMap = ref(
  new Map([
    ["c1", { title: "Getting Started with Vue.js", type: "article" }],
    ["c2", { title: "Advanced JavaScript Techniques", type: "video" }],
    ["c3", { title: "UI/UX Design Principles", type: "ebook" }],
    ["c4", { title: "Cloud Architecture Overview", type: "webinar" }],
  ])
);

// Get related content details
const getRelatedContentDetails = (contentId: string) => {
  return mockRelatedContentMap.value.get(contentId);
};

// Handle keyboard shortcuts for form submission
onMounted(() => {
  window.addEventListener("keydown", (e) => {
    // Ctrl+S or Cmd+S to save
    if ((e.ctrlKey || e.metaKey) && e.key === "s") {
      e.preventDefault();
      submitForm();
    }
  });
});
</script>

<template>
  <div class="min-h-full pb-10">
    <!-- Page header -->
    <div class="bg-white shadow-sm">
      <div
        class="py-6 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between"
      >
        <div class="flex-1 min-w-0">
          <h1 class="text-2xl font-bold text-gray-900 sm:truncate">
            Create New Content
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Create and publish new content for your platform
          </p>
        </div>
        <div class="mt-4 flex space-x-3 sm:mt-0">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="resetForm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 -ml-1 text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            Reset
          </button>
          <button
            type="button"
            :disabled="isSubmitting"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="submitForm"
          >
            <svg
              v-if="!isSubmitting"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 -ml-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else
              class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
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
            {{ isSubmitting ? "Saving..." : "Save Content" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Success alert -->
    <div
      v-if="showSuccessAlert"
      class="bg-green-50 border-l-4 border-green-500 p-4 m-4"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <svg
            class="h-5 w-5 text-green-500"
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
          <p class="text-sm text-green-800">
            Content has been successfully saved! You can continue editing or go
            to the content list.
          </p>
        </div>
        <div class="ml-auto pl-3">
          <div class="-mx-1.5 -my-1.5">
            <button
              class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600"
              @click="showSuccessAlert = false"
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

    <!-- Content form -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Content Information
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Basic details about the content you're creating
          </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <!-- Content type selection -->
            <div class="sm:col-span-6">
              <label class="block text-sm font-medium text-gray-700"
                >Content Type</label
              >
              <div class="mt-2 grid grid-cols-2 md:grid-cols-5 gap-3">
                <div
                  v-for="type in contentTypes"
                  :key="type.id"
                  class="border rounded-lg p-3 cursor-pointer transition-colors"
                  :class="
                    contentForm.contentType === type.id
                      ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                      : 'border-gray-300 hover:bg-gray-50'
                  "
                  @click="contentForm.contentType = type.id"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <span
                        class="text-indigo-500 mr-2"
                        v-html="getContentTypeIcon(type.icon)"
                      />
                      <span class="text-sm font-medium">{{ type.label }}</span>
                    </div>
                    <div
                      v-if="contentForm.contentType === type.id"
                      class="text-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
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
                  </div>
                </div>
              </div>
            </div>

            <!-- Title -->
            <div class="sm:col-span-4">
              <label
                for="title"
                class="block text-sm font-medium text-gray-700"
              >
                Title <span class="text-red-500">*</span>
              </label>
              <div class="mt-1">
                <input
                  id="title"
                  v-model="contentForm.title"
                  type="text"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  :class="{ 'border-red-500': formErrors.title }"
                  @blur="
                    generateSlug();
                    updateSeoTitle();
                  "
                />
                <p v-if="formErrors.title" class="mt-1 text-sm text-red-600">
                  {{ formErrors.title }}
                </p>
              </div>
            </div>

            <!-- Slug -->
            <div class="sm:col-span-2">
              <label for="slug" class="block text-sm font-medium text-gray-700">
                Slug <span class="text-red-500">*</span>
              </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <span
                  class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm"
                >
                  /content/
                </span>
                <input
                  id="slug"
                  v-model="contentForm.slug"
                  type="text"
                  class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300"
                  :class="{ 'border-red-500': formErrors.slug }"
                />
              </div>
              <p v-if="formErrors.slug" class="mt-1 text-sm text-red-600">
                {{ formErrors.slug }}
              </p>
              <p v-else class="mt-1 text-xs text-gray-500">
                URL-friendly name (auto-generated from title)
              </p>
            </div>

            <!-- Category -->
            <div class="sm:col-span-3">
              <label
                for="category"
                class="block text-sm font-medium text-gray-700"
              >
                Category <span class="text-red-500">*</span>
              </label>
              <div class="mt-1">
                <select
                  id="category"
                  v-model="contentForm.category"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  :class="{ 'border-red-500': formErrors.category }"
                >
                  <option value="">Select a category</option>
                  <optgroup
                    v-for="(group, groupKey) in groupedCategories"
                    :key="groupKey"
                    :label="group.label"
                  >
                    <option
                      v-for="category in group.categories"
                      :key="category.id"
                      :value="category.id"
                    >
                      {{ category.label }}
                    </option>
                  </optgroup>
                </select>
                <p v-if="formErrors.category" class="mt-1 text-sm text-red-600">
                  {{ formErrors.category }}
                </p>
              </div>
            </div>

            <!-- Status select -->
            <div class="sm:col-span-3">
              <label
                for="status"
                class="block text-sm font-medium text-gray-700"
                >Status</label
              >
              <div class="mt-1">
                <select
                  id="status"
                  v-model="contentForm.status"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                >
                  <option value="draft">Draft</option>
                  <option value="review">Ready for Review</option>
                  <option value="published">Published</option>
                  <option value="archived">Archived</option>
                </select>
              </div>
              <div class="mt-2">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="getStatusBadgeStyle(contentForm.status)"
                >
                  {{ contentForm.status }}
                </span>
              </div>
            </div>

            <!-- Tags -->
            <div class="sm:col-span-6">
              <label for="tags" class="block text-sm font-medium text-gray-700"
                >Tags</label
              >
              <div class="mt-1 flex rounded-md shadow-sm">
                <input
                  id="tags"
                  v-model="tagInput"
                  type="text"
                  placeholder="Add a tag and press Enter"
                  class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  @keydown.enter.prevent="addTag"
                />
                <button
                  type="button"
                  class="ml-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  @click="addTag"
                >
                  Add
                </button>
              </div>
              <div class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="tag in contentForm.tags"
                  :key="tag"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                >
                  {{ tag }}
                  <button
                    type="button"
                    class="ml-1.5 inline-flex text-indigo-500 focus:outline-none"
                    @click="removeTag(tag)"
                  >
                    <svg
                      class="h-4 w-4"
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
                </span>
              </div>
            </div>

            <!-- Description -->
            <div class="sm:col-span-6">
              <label
                for="description"
                class="block text-sm font-medium text-gray-700"
              >
                Description <span class="text-red-500">*</span>
              </label>
              <div class="mt-1">
                <textarea
                  id="description"
                  v-model="contentForm.description"
                  rows="3"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  :class="{ 'border-red-500': formErrors.description }"
                />
                <p
                  v-if="formErrors.description"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ formErrors.description }}
                </p>
                <p v-else class="mt-1 text-xs text-gray-500">
                  Brief description of your content. This will be displayed in
                  search results and content previews.
                </p>
              </div>
            </div>

            <!-- Main content editor -->
            <div class="sm:col-span-6">
              <label
                for="content"
                class="block text-sm font-medium text-gray-700"
              >
                Content <span class="text-red-500">*</span>
              </label>
              <div class="mt-1">
                <textarea
                  id="content"
                  v-model="contentForm.content"
                  rows="12"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  :class="{ 'border-red-500': formErrors.content }"
                  placeholder="Write your content here..."
                />
                <p v-if="formErrors.content" class="mt-1 text-sm text-red-600">
                  {{ formErrors.content }}
                </p>
                <p v-else class="mt-1 text-xs text-gray-500">
                  Rich text editor would be implemented here in a production
                  environment.
                </p>
              </div>
            </div>

            <!-- Featured image -->
            <div class="sm:col-span-6">
              <label
                for="featured-image"
                class="block text-sm font-medium text-gray-700"
                >Featured Image</label
              >
              <div
                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
              >
                <div class="space-y-1 text-center">
                  <div v-if="contentForm.imagePreview" class="mb-4">
                    <img
                      :src="contentForm.imagePreview"
                      alt="Image preview"
                      class="mx-auto h-48 object-cover"
                    />
                    <button
                      type="button"
                      class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      @click="removeImage"
                    >
                      Remove image
                    </button>
                  </div>
                  <div v-else>
                    <svg
                      class="mx-auto h-12 w-12 text-gray-400"
                      stroke="currentColor"
                      fill="none"
                      viewBox="0 0 48 48"
                      aria-hidden="true"
                    >
                      <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label
                        for="file-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                      >
                        <span>Upload a file</span>
                        <input
                          id="file-upload"
                          name="file-upload"
                          type="file"
                          class="sr-only"
                          @change="handleFileUpload"
                        />
                      </label>
                      <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Authors section -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Authors</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Add authors who contributed to this content
          </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
          <!-- Authors search and selection -->
          <div class="mb-4">
            <label
              for="author-search"
              class="block text-sm font-medium text-gray-700"
              >Search Authors</label
            >
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                id="author-search"
                v-model="authorSearchInput"
                type="text"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="Search by name"
                autocomplete="off"
                @input="searchAuthors"
              />
              <div
                v-if="isSearching"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <svg
                  class="animate-spin h-5 w-5 text-gray-400"
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
            </div>

            <!-- Search results -->
            <div
              v-if="searchResults.length > 0"
              class="mt-1 bg-white shadow-lg rounded-md py-1 absolute z-10 max-h-60 w-full max-w-lg overflow-auto"
            >
              <div
                v-for="author in searchResults"
                :key="author.id"
                class="px-4 py-2 hover:bg-indigo-50 cursor-pointer flex items-center"
                @click="addAuthor(author)"
              >
                <img
                  :src="author.avatar"
                  :alt="author.name"
                  class="h-8 w-8 rounded-full mr-3"
                />
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ author.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ author.position }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Selected authors -->
          <div v-if="contentForm.authors.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-gray-500 mb-2">
              Selected Authors
            </h4>
            <div class="space-y-2">
              <div
                v-for="author in contentForm.authors"
                :key="author.id"
                class="bg-white border border-gray-200 rounded-md px-4 py-3 flex items-center justify-between"
              >
                <div class="flex items-center">
                  <img
                    :src="`/api/placeholder/40/40`"
                    :alt="author.name"
                    class="h-8 w-8 rounded-full mr-3"
                  />
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ author.name }}
                    </div>
                  </div>
                </div>
                <button
                  type="button"
                  class="text-gray-400 hover:text-red-500 transition-colors"
                  @click="removeAuthor(author.id)"
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

      <!-- Related content section -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Related Content
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Link to other relevant content on your platform
          </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
          <!-- Content search -->
          <div class="mb-4">
            <label
              for="content-search"
              class="block text-sm font-medium text-gray-700"
              >Search Content</label
            >
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                id="content-search"
                v-model="contentSearchInput"
                type="text"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="Search by title"
                autocomplete="off"
                @input="searchRelatedContent"
              />
              <div
                v-if="isSearchingContent"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <svg
                  class="animate-spin h-5 w-5 text-gray-400"
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
            </div>

            <!-- Search results -->
            <div
              v-if="contentSearchResults.length > 0"
              class="mt-1 bg-white shadow-lg rounded-md py-1 absolute z-10 max-h-60 w-full max-w-lg overflow-auto"
            >
              <div
                v-for="content in contentSearchResults"
                :key="content.id"
                class="px-4 py-2 hover:bg-indigo-50 cursor-pointer"
                @click="addRelatedContent(content.id)"
              >
                <div class="flex items-center">
                  <span
                    class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800 mr-2 capitalize"
                    >{{ content.type }}</span
                  >
                  <span class="text-sm font-medium text-gray-900">{{
                    content.title
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Selected related content -->
          <div v-if="contentForm.relatedContent.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-gray-500 mb-2">
              Selected Related Content
            </h4>
            <div class="space-y-2">
              <div
                v-for="contentId in contentForm.relatedContent"
                :key="contentId"
                class="bg-white border border-gray-200 rounded-md px-4 py-3 flex items-center justify-between"
              >
                <div>
                  <div class="flex items-center">
                    <span
                      class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800 mr-2 capitalize"
                    >
                      {{ getRelatedContentDetails(contentId)?.type }}
                    </span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ getRelatedContentDetails(contentId)?.title }}
                    </span>
                  </div>
                </div>
                <button
                  type="button"
                  class="text-gray-400 hover:text-red-500 transition-colors"
                  @click="removeRelatedContent(contentId)"
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

      <!-- Advanced settings section -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Advanced Settings
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Configure publishing, visibility, and SEO settings
          </p>
        </div>

        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <!-- Publishing section -->
            <div class="sm:col-span-6">
              <fieldset>
                <legend class="text-sm font-medium text-gray-700">
                  Publishing Options
                </legend>
                <div class="mt-2 space-y-4">
                  <div class="flex items-center">
                    <div class="flex items-center h-5">
                      <input
                        id="is-published"
                        v-model="contentForm.isPublished"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="is-published" class="text-sm text-gray-700"
                        >Publish immediately</label
                      >
                      <p class="text-xs text-gray-500">
                        If unchecked, content will be saved as a draft or
                        scheduled.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-center">
                    <div class="flex items-center h-5">
                      <input
                        id="is-featured"
                        v-model="contentForm.isFeatured"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label for="is-featured" class="text-sm text-gray-700"
                        >Feature this content</label
                      >
                      <p class="text-xs text-gray-500">
                        Featured content appears in prominent locations on the
                        platform.
                      </p>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>

            <!-- Publish date and expiry date -->
            <div class="sm:col-span-3">
              <label
                for="publish-date"
                class="block text-sm font-medium text-gray-700"
                >Publish Date</label
              >
              <div class="mt-1">
                <input
                  id="publish-date"
                  v-model="contentForm.publishDate"
                  type="datetime-local"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </div>
              <p class="mt-1 text-xs text-gray-500">
                When the content should be published. Current date and time if
                empty.
              </p>
            </div>

            <div class="sm:col-span-3">
              <label
                for="expiry-date"
                class="block text-sm font-medium text-gray-700"
                >Expiry Date (Optional)</label
              >
              <div class="mt-1">
                <input
                  id="expiry-date"
                  v-model="contentForm.expiryDate"
                  type="datetime-local"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </div>
              <p class="mt-1 text-xs text-gray-500">
                When the content should be automatically archived. Leave empty
                for no expiry.
              </p>
            </div>

            <!-- SEO Settings -->
            <div class="sm:col-span-6 pt-4 border-t border-gray-200">
              <h4 class="text-sm font-medium text-gray-700">SEO Settings</h4>
              <p class="mt-1 text-xs text-gray-500">
                Optimize your content for search engines
              </p>
            </div>

            <div class="sm:col-span-6">
              <label
                for="seo-title"
                class="block text-sm font-medium text-gray-700"
                >SEO Title</label
              >
              <div class="mt-1">
                <input
                  id="seo-title"
                  v-model="contentForm.seoTitle"
                  type="text"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  placeholder="Title that appears in search results"
                />
              </div>
              <p class="mt-1 text-xs text-gray-500">
                If left empty, the main title will be used. Recommended length:
                50-60 characters.
              </p>
            </div>

            <div class="sm:col-span-6">
              <label
                for="seo-description"
                class="block text-sm font-medium text-gray-700"
                >Meta Description</label
              >
              <div class="mt-1">
                <textarea
                  id="seo-description"
                  v-model="contentForm.seoDescription"
                  rows="2"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  placeholder="Short description for search engines"
                />
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Brief description that appears in search results. Recommended
                length: 150-160 characters.
              </p>
            </div>

            <div class="sm:col-span-6">
              <label
                for="seo-keywords"
                class="block text-sm font-medium text-gray-700"
                >Focus Keywords</label
              >
              <div class="mt-1">
                <input
                  id="seo-keywords"
                  v-model="contentForm.seoKeywords"
                  type="text"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  placeholder="Comma-separated keywords"
                />
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Primary keywords to target for this content. Separate with
                commas.
              </p>
            </div>

            <!-- Additional metadata -->
            <div class="sm:col-span-6 pt-4 border-t border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="text-sm font-medium text-gray-700">
                    Additional Information
                  </h4>
                  <p class="mt-1 text-xs text-gray-500">
                    Automatically generated metadata
                  </p>
                </div>
              </div>

              <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                  <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                      Created By
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">Admin User</dd>
                  </div>
                  <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                      Created Date
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ formatDate(new Date().toISOString()) }}
                    </dd>
                  </div>
                  <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">Auto-generated</dd>
                  </div>
                  <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Version</dt>
                    <dd class="mt-1 text-sm text-gray-900">1.0</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Visibility section -->
            <div class="sm:col-span-6">
              <fieldset>
                <legend class="text-sm font-medium text-gray-700">
                  Visibility Options
                </legend>
                <div class="mt-2 space-y-4">
                  <div class="flex items-center">
                    <div class="flex items-center h-5">
                      <input
                        id="is-for-elearning"
                        v-model="contentForm.isForELearning"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label
                        for="is-for-elearning"
                        class="text-sm text-gray-700"
                        >Show in E-Learning section</label
                      >
                      <p class="text-xs text-gray-500">
                        Content will be visible in the E-Learning module of the
                        platform.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-center">
                    <div class="flex items-center h-5">
                      <input
                        id="is-for-consultancy"
                        v-model="contentForm.isForConsultancy"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3">
                      <label
                        for="is-for-consultancy"
                        class="text-sm text-gray-700"
                        >Show in Consultancy section</label
                      >
                      <p class="text-xs text-gray-500">
                        Content will be visible in the Consultancy module of the
                        platform.
                      </p>
                    </div>
                  </div>

                  <div>
                    <label
                      for="access-level"
                      class="block text-sm font-medium text-gray-700"
                      >Access Level</label
                    >
                    <select
                      id="access-level"
                      v-model="contentForm.accessLevel"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                      <option value="all">
                        Public - Available to all visitors
                      </option>
                      <option value="registered">
                        Registered - Available to registered users only
                      </option>
                      <option value="premium">
                        Premium - Available to premium subscribers only
                      </option>
                    </select>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Custom styling for the content creation page */
.content-creation-page {
  min-height: 100%;
  padding-bottom: 2.5rem;
}

/* Custom form element styling */
.input-focus:focus {
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  border-color: #6366f1;
  outline: none;
  ring-width: 2px;
  ring-color: #6366f1;
}

.tag-item {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.625rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  background-color: #e0e7ff;
  color: #4f46e5;
}

.tag-remove-btn {
  margin-left: 0.375rem;
  display: inline-flex;
  color: #6366f1;
}

.tag-remove-btn:focus {
  outline: none;
}

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.25rem;
}

.form-input {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  width: 100%;
  font-size: 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

.form-input:focus {
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  border-color: #6366f1;
  outline: none;
}

.form-error {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc2626;
}

.form-help-text {
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #6b7280;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid transparent;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  font-size: 0.875rem;
  font-weight: 500;
  color: white;
  background-color: #4f46e5;
}

.btn-primary:hover {
  background-color: #4338ca;
}

.btn-primary:focus {
  outline: none;
  box-shadow: 0 0 0 2px #f9fafb, 0 0 0 4px #6366f1;
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  background-color: white;
}

.btn-secondary:hover {
  background-color: #f9fafb;
}

.btn-secondary:focus {
  outline: none;
  box-shadow: 0 0 0 2px #f9fafb, 0 0 0 4px #6366f1;
}

.section-header {
  border-bottom: 1px solid #e5e7eb;
  padding: 1.25rem 1rem;
}

.section-title {
  font-size: 1.125rem;
  line-height: 1.5rem;
  font-weight: 500;
  color: #111827;
}

.section-description {
  margin-top: 0.25rem;
  max-width: 42rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.section-content {
  padding: 1.25rem 1rem;
}

.card {
  background-color: white;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  overflow: hidden;
  border-radius: 0.5rem;
  margin-top: 2rem;
}

/* Status badges */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.125rem 0.625rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: capitalize;
}

.badge-draft {
  background-color: #fef3c7;
  color: #92400e;
}

.badge-review {
  background-color: #dbeafe;
  color: #1e40af;
}

.badge-published {
  background-color: #d1fae5;
  color: #065f46;
}

.badge-archived {
  background-color: #f3f4f6;
  color: #1f2937;
}

/* Animations */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-in {
  animation: slideIn 0.3s ease-out forwards;
}

@keyframes slideIn {
  0% {
    opacity: 0;
    transform: translateX(-10px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Transitions for elements */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-y-enter-active,
.slide-y-leave-active {
  transition: all 0.3s ease;
}

.slide-y-enter-from,
.slide-y-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Hover effects */
.hover-scale {
  transition: transform 0.2s;
}

.hover-scale:hover {
  transform: scale(1.05);
}

/* Keyboard shortcut styling */
kbd {
  padding: 0.125rem 0.5rem;
  background-color: #f3f4f6;
  border-radius: 0.25rem;
  border: 1px solid #d1d5db;
  font-family: sans-serif;
  font-size: 0.75rem;
}
</style>
