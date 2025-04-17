<!-- pages/dashboard/admin/content/index.vue -->
<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue";

// Page meta
definePageMeta({
  layout: "dashboard-admin",
});

useHead({
  title: "Content Management - Admin Dashboard",
});

// Types for content management
interface ContentItem {
  id: number;
  title: string;
  type:
    | "article"
    | "lesson"
    | "resource"
    | "faq"
    | "announcement"
    | "landing_page";
  status: "published" | "draft" | "archived" | "under_review";
  author: {
    id: number;
    name: string;
    avatar: string;
  };
  category: string;
  dateCreated: string;
  dateModified: string;
  views: number;
  featured: boolean;
  hasMedia: boolean;
}

interface Category {
  id: number;
  name: string;
  count: number;
}

// State
const isLoading = ref(true);
const searchQuery = ref("");
const activeTab = ref("all");
const sortBy = ref("recent");
const contentItems = ref<ContentItem[]>([]);
const categories = ref<Category[]>([]);
const selectedItems = ref<number[]>([]);
const showDeleteModal = ref(false);
const itemToDelete = ref<ContentItem | null>(null);
const deleteConfirmText = ref("");
const bulkActionOpen = ref(false);
const showFeaturedModal = ref(false);
const showContentDetails = ref(false);
const selectedContentItem = ref<ContentItem | null>(null);
const page = ref(1);
const totalPages = ref(5);
const itemsPerPage = ref(10);

// Filters
const filters = reactive({
  types: [] as string[],
  categories: [] as string[],
  status: [] as string[],
  dateRange: {
    start: "",
    end: "",
  },
  featured: null as boolean | null,
});

// Mock content data fetch
const fetchContentItems = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock categories
    categories.value = [
      { id: 1, name: "Web Development", count: 47 },
      { id: 2, name: "UX/UI Design", count: 32 },
      { id: 3, name: "Machine Learning", count: 18 },
      { id: 4, name: "Data Science", count: 24 },
      { id: 5, name: "Cloud Computing", count: 15 },
      { id: 6, name: "Mobile Development", count: 21 },
      { id: 7, name: "DevOps", count: 14 },
      { id: 8, name: "Cybersecurity", count: 11 },
      { id: 9, name: "Blockchain", count: 8 },
    ];

    // Mock content items
    contentItems.value = [
      {
        id: 1,
        title: "Getting Started with Web Development",
        type: "article",
        status: "published",
        author: {
          id: 1,
          name: "Sarah Parker",
          avatar: "/images/courses/c3.jpg",
        },
        category: "Web Development",
        dateCreated: "2025-03-15T10:30:00Z",
        dateModified: "2025-03-20T14:45:00Z",
        views: 1253,
        featured: true,
        hasMedia: true,
      },
      {
        id: 2,
        title: "Advanced JavaScript Concepts",
        type: "lesson",
        status: "published",
        author: {
          id: 2,
          name: "Michael Roberts",
          avatar: "/images/courses/c3.jpg",
        },
        category: "Web Development",
        dateCreated: "2025-03-10T09:15:00Z",
        dateModified: "2025-03-18T11:20:00Z",
        views: 876,
        featured: false,
        hasMedia: true,
      },
      {
        id: 3,
        title: "Introduction to User Experience Design",
        type: "article",
        status: "published",
        author: {
          id: 3,
          name: "Emily Johnson",
          avatar: "/images/courses/c3.jpg",
        },
        category: "UX/UI Design",
        dateCreated: "2025-03-05T16:45:00Z",
        dateModified: "2025-03-12T08:30:00Z",
        views: 724,
        featured: true,
        hasMedia: true,
      },
      {
        id: 4,
        title: "Platform Updates - April 2025",
        type: "announcement",
        status: "published",
        author: { id: 4, name: "Admin User", avatar: "/images/courses/c3.jpg" },
        category: "Announcements",
        dateCreated: "2025-04-01T12:00:00Z",
        dateModified: "2025-04-01T12:00:00Z",
        views: 1580,
        featured: true,
        hasMedia: false,
      },
      {
        id: 5,
        title: "Understanding Neural Networks",
        type: "lesson",
        status: "under_review",
        author: { id: 5, name: "David Chen", avatar: "/images/courses/c3.jpg" },
        category: "Machine Learning",
        dateCreated: "2025-03-28T14:30:00Z",
        dateModified: "2025-04-02T10:15:00Z",
        views: 0,
        featured: false,
        hasMedia: true,
      },
      {
        id: 6,
        title: "Cloud Computing Fundamentals",
        type: "resource",
        status: "draft",
        author: {
          id: 2,
          name: "Michael Roberts",
          avatar: "/images/courses/c3.jpg",
        },
        category: "Cloud Computing",
        dateCreated: "2025-04-03T09:45:00Z",
        dateModified: "2025-04-03T09:45:00Z",
        views: 0,
        featured: false,
        hasMedia: true,
      },
      {
        id: 7,
        title: "Summer Promotion - 2025",
        type: "landing_page",
        status: "published",
        author: { id: 4, name: "Admin User", avatar: "/images/courses/c3.jpg" },
        category: "Marketing",
        dateCreated: "2025-03-25T11:30:00Z",
        dateModified: "2025-04-01T16:45:00Z",
        views: 2475,
        featured: true,
        hasMedia: true,
      },
      {
        id: 8,
        title: "Frequently Asked Questions About Courses",
        type: "faq",
        status: "published",
        author: {
          id: 1,
          name: "Sarah Parker",
          avatar: "/images/courses/c3.jpg",
        },
        category: "Support",
        dateCreated: "2025-03-20T13:15:00Z",
        dateModified: "2025-03-30T09:30:00Z",
        views: 975,
        featured: false,
        hasMedia: false,
      },
      {
        id: 9,
        title: "Mobile App Development with React Native",
        type: "lesson",
        status: "archived",
        author: {
          id: 3,
          name: "Emily Johnson",
          avatar: "/images/courses/c3.jpg",
        },
        category: "Mobile Development",
        dateCreated: "2024-12-10T10:00:00Z",
        dateModified: "2025-01-15T14:30:00Z",
        views: 543,
        featured: false,
        hasMedia: true,
      },
      {
        id: 10,
        title: "Data Visualization Techniques",
        type: "resource",
        status: "published",
        author: { id: 5, name: "David Chen", avatar: "/images/courses/c3.jpg" },
        category: "Data Science",
        dateCreated: "2025-03-18T08:45:00Z",
        dateModified: "2025-03-25T15:30:00Z",
        views: 687,
        featured: false,
        hasMedia: true,
      },
      {
        id: 11,
        title: "DevOps Best Practices",
        type: "article",
        status: "under_review",
        author: {
          id: 2,
          name: "Michael Roberts",
          avatar: "/images/courses/c3.jpg",
        },
        category: "DevOps",
        dateCreated: "2025-04-02T16:20:00Z",
        dateModified: "2025-04-04T11:15:00Z",
        views: 0,
        featured: false,
        hasMedia: false,
      },
      {
        id: 12,
        title: "Cybersecurity Essentials",
        type: "lesson",
        status: "published",
        author: {
          id: 1,
          name: "Sarah Parker",
          avatar: "/images/courses/c3.jpg",
        },
        category: "Cybersecurity",
        dateCreated: "2025-02-28T13:10:00Z",
        dateModified: "2025-03-10T09:45:00Z",
        views: 842,
        featured: false,
        hasMedia: true,
      },
    ];
  } catch (error) {
    console.error("Error fetching content items:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filtered content items
const filteredContentItems = computed(() => {
  let result = [...contentItems.value];

  // Apply tab filter
  if (activeTab.value !== "all") {
    if (activeTab.value === "published") {
      result = result.filter((item) => item.status === "published");
    } else if (activeTab.value === "draft") {
      result = result.filter((item) => item.status === "draft");
    } else if (activeTab.value === "review") {
      result = result.filter((item) => item.status === "under_review");
    } else if (activeTab.value === "archived") {
      result = result.filter((item) => item.status === "archived");
    } else if (activeTab.value === "featured") {
      result = result.filter((item) => item.featured);
    }
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (item) =>
        item.title.toLowerCase().includes(query) ||
        item.category.toLowerCase().includes(query) ||
        item.author.name.toLowerCase().includes(query)
    );
  }

  // Apply advanced filters
  if (filters.types.length > 0) {
    result = result.filter((item) => filters.types.includes(item.type));
  }

  if (filters.categories.length > 0) {
    result = result.filter((item) =>
      filters.categories.includes(item.category)
    );
  }

  if (filters.status.length > 0) {
    result = result.filter((item) => filters.status.includes(item.status));
  }

  if (filters.featured !== null) {
    result = result.filter((item) => item.featured === filters.featured);
  }

  if (filters.dateRange.start && filters.dateRange.end) {
    const startDate = new Date(filters.dateRange.start).getTime();
    const endDate = new Date(filters.dateRange.end).getTime();
    result = result.filter((item) => {
      const itemDate = new Date(item.dateModified).getTime();
      return itemDate >= startDate && itemDate <= endDate;
    });
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.dateModified).getTime() - new Date(a.dateModified).getTime()
    );
  } else if (sortBy.value === "oldest") {
    result.sort(
      (a, b) =>
        new Date(a.dateModified).getTime() - new Date(b.dateModified).getTime()
    );
  } else if (sortBy.value === "a-z") {
    result.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sortBy.value === "z-a") {
    result.sort((a, b) => b.title.localeCompare(a.title));
  } else if (sortBy.value === "views") {
    result.sort((a, b) => b.views - a.views);
  }

  return result;
});

// Paginated content items
const paginatedContentItems = computed(() => {
  const startIndex = (page.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return filteredContentItems.value.slice(startIndex, endIndex);
});

// Content type counts
const contentCounts = computed(() => {
  return {
    total: contentItems.value.length,
    published: contentItems.value.filter((item) => item.status === "published")
      .length,
    draft: contentItems.value.filter((item) => item.status === "draft").length,
    review: contentItems.value.filter((item) => item.status === "under_review")
      .length,
    archived: contentItems.value.filter((item) => item.status === "archived")
      .length,
    featured: contentItems.value.filter((item) => item.featured).length,
  };
});

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Function to check if all items are selected
const allSelected = computed(() => {
  return (
    paginatedContentItems.value.length > 0 &&
    selectedItems.value.length === paginatedContentItems.value.length
  );
});

// Toggle select all
const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = paginatedContentItems.value.map((item) => item.id);
  }
};

// Toggle item selection
const toggleSelect = (id: number) => {
  const index = selectedItems.value.indexOf(id);
  if (index === -1) {
    selectedItems.value.push(id);
  } else {
    selectedItems.value.splice(index, 1);
  }
};

// Get badge color for content type
const getContentTypeBadgeClass = (type: string) => {
  switch (type) {
    case "article":
      return "bg-blue-100 text-blue-800";
    case "lesson":
      return "bg-green-100 text-green-800";
    case "resource":
      return "bg-purple-100 text-purple-800";
    case "faq":
      return "bg-yellow-100 text-yellow-800";
    case "announcement":
      return "bg-red-100 text-red-800";
    case "landing_page":
      return "bg-indigo-100 text-indigo-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get badge color for content status
const getContentStatusBadgeClass = (status: string) => {
  switch (status) {
    case "published":
      return "bg-green-100 text-green-800";
    case "draft":
      return "bg-yellow-100 text-yellow-800";
    case "under_review":
      return "bg-blue-100 text-blue-800";
    case "archived":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Delete item confirmation
const confirmDelete = (item: ContentItem) => {
  itemToDelete.value = item;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

// Delete content item
const deleteItem = () => {
  if (!itemToDelete.value || deleteConfirmText.value !== "delete") {
    return;
  }

  // In a real app, this would make an API call
  contentItems.value = contentItems.value.filter(
    (item) => item.id !== itemToDelete.value?.id
  );
  showDeleteModal.value = false;
  itemToDelete.value = null;
  deleteConfirmText.value = "";
  selectedItems.value = selectedItems.value.filter(
    (id) => id !== itemToDelete.value?.id
  );
};

// Bulk delete items
const bulkDeleteItems = () => {
  if (selectedItems.value.length === 0) return;

  // In a real app, this would make an API call
  contentItems.value = contentItems.value.filter(
    (item) => !selectedItems.value.includes(item.id)
  );
  selectedItems.value = [];
  bulkActionOpen.value = false;
};

// Bulk update status
const bulkUpdateStatus = (status: string) => {
  if (selectedItems.value.length === 0) return;

  // In a real app, this would make an API call
  contentItems.value = contentItems.value.map((item) => {
    if (selectedItems.value.includes(item.id)) {
      return { ...item, status: status as any };
    }
    return item;
  });

  selectedItems.value = [];
  bulkActionOpen.value = false;
};

// Toggle featured status
const toggleFeatured = (item: ContentItem) => {
  // In a real app, this would make an API call
  contentItems.value = contentItems.value.map((i) => {
    if (i.id === item.id) {
      return { ...i, featured: !i.featured };
    }
    return i;
  });
};

// Bulk toggle featured status
const bulkToggleFeatured = (featured: boolean) => {
  if (selectedItems.value.length === 0) return;

  // In a real app, this would make an API call
  contentItems.value = contentItems.value.map((item) => {
    if (selectedItems.value.includes(item.id)) {
      return { ...item, featured: featured };
    }
    return item;
  });

  selectedItems.value = [];
  bulkActionOpen.value = false;
};

// Show content details
const viewContentDetails = (item: ContentItem) => {
  selectedContentItem.value = item;
  showContentDetails.value = true;
};

// Reset filters
const resetFilters = () => {
  filters.types = [];
  filters.categories = [];
  filters.status = [];
  filters.dateRange = { start: "", end: "" };
  filters.featured = null;
  searchQuery.value = "";
  sortBy.value = "recent";
  activeTab.value = "all";
};

// Change page
const changePage = (newPage: number) => {
  if (newPage > 0 && newPage <= totalPages.value) {
    page.value = newPage;
    selectedItems.value = [];
  }
};

// Update total pages based on filtered items and items per page
const updateTotalPages = computed(() => {
  return Math.ceil(filteredContentItems.value.length / itemsPerPage.value);
});

// Watch for changes to filtered items and update total pages
watch(filteredContentItems, () => {
  totalPages.value = updateTotalPages.value;
  // If current page is beyond total pages, reset to page 1
  if (page.value > totalPages.value) {
    page.value = 1;
  }
});

// Load data when component mounts
onMounted(() => {
  fetchContentItems();
});
</script>

<template>
  <div>
    <!-- Page header with tabs and filters -->
    <div class="mb-6">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Content Management
          </h1>
          <p class="text-gray-600">
            Manage, update, and organize all platform content
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
          <NuxtLink
            to="/dashboard/admin/content/create"
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
            Create Content
          </NuxtLink>

          <button
            @click="resetFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            Reset Filters
          </button>
        </div>
      </div>

      <!-- Statistics cards -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div
          @click="activeTab = 'all'"
          class="bg-white rounded-lg shadow-sm p-4 border cursor-pointer transition-all hover:shadow-md"
          :class="
            activeTab === 'all'
              ? 'border-indigo-500 ring-2 ring-indigo-200'
              : 'border-gray-200'
          "
        >
          <div class="flex justify-between items-center">
            <div class="text-sm font-medium text-gray-500">All Content</div>
            <div
              class="flex h-8 w-8 rounded-full bg-indigo-100 items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
                />
              </svg>
            </div>
          </div>
          <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ contentCounts.total }}
          </div>
        </div>

        <div
          @click="activeTab = 'published'"
          class="bg-white rounded-lg shadow-sm p-4 border cursor-pointer transition-all hover:shadow-md"
          :class="
            activeTab === 'published'
              ? 'border-green-500 ring-2 ring-green-200'
              : 'border-gray-200'
          "
        >
          <div class="flex justify-between items-center">
            <div class="text-sm font-medium text-gray-500">Published</div>
            <div
              class="flex h-8 w-8 rounded-full bg-green-100 items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
          <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ contentCounts.published }}
          </div>
        </div>

        <div
          @click="activeTab = 'draft'"
          class="bg-white rounded-lg shadow-sm p-4 border cursor-pointer transition-all hover:shadow-md"
          :class="
            activeTab === 'draft'
              ? 'border-yellow-500 ring-2 ring-yellow-200'
              : 'border-gray-200'
          "
        >
          <div class="flex justify-between items-center">
            <div class="text-sm font-medium text-gray-500">Drafts</div>
            <div
              class="flex h-8 w-8 rounded-full bg-yellow-100 items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-yellow-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                />
                <path
                  fill-rule="evenodd"
                  d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
          <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ contentCounts.draft }}
          </div>
        </div>

        <div
          @click="activeTab = 'review'"
          class="bg-white rounded-lg shadow-sm p-4 border cursor-pointer transition-all hover:shadow-md"
          :class="
            activeTab === 'review'
              ? 'border-blue-500 ring-2 ring-blue-200'
              : 'border-gray-200'
          "
        >
          <div class="flex justify-between items-center">
            <div class="text-sm font-medium text-gray-500">Under Review</div>
            <div
              class="flex h-8 w-8 rounded-full bg-blue-100 items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-blue-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path
                  fill-rule="evenodd"
                  d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
          <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ contentCounts.review }}
          </div>
        </div>

        <div
          @click="activeTab = 'archived'"
          class="bg-white rounded-lg shadow-sm p-4 border cursor-pointer transition-all hover:shadow-md"
          :class="
            activeTab === 'archived'
              ? 'border-gray-500 ring-2 ring-gray-200'
              : 'border-gray-200'
          "
        >
          <div class="flex justify-between items-center">
            <div class="text-sm font-medium text-gray-500">Archived</div>
            <div
              class="flex h-8 w-8 rounded-full bg-gray-100 items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                <path
                  fill-rule="evenodd"
                  d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
          <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ contentCounts.archived }}
          </div>
        </div>

        <div
          @click="activeTab = 'featured'"
          class="bg-white rounded-lg shadow-sm p-4 border cursor-pointer transition-all hover:shadow-md"
          :class="
            activeTab === 'featured'
              ? 'border-purple-500 ring-2 ring-purple-200'
              : 'border-gray-200'
          "
        >
          <div class="flex justify-between items-center">
            <div class="text-sm font-medium text-gray-500">Featured</div>
            <div
              class="flex h-8 w-8 rounded-full bg-purple-100 items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-purple-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </div>
          </div>
          <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ contentCounts.featured }}
          </div>
        </div>
      </div>
    </div>

    <!-- Search, filters and bulk actions -->
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 mb-6">
      <div
        class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4"
      >
        <!-- Search -->
        <div class="relative flex-grow">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search content by title, category, or author..."
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
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
        </div>

        <!-- Sort dropdown -->
        <div class="relative w-full md:w-auto">
          <select
            v-model="sortBy"
            class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full"
          >
            <option value="recent">Recently Modified</option>
            <option value="oldest">Oldest First</option>
            <option value="a-z">Title (A-Z)</option>
            <option value="z-a">Title (Z-A)</option>
            <option value="views">Most Views</option>
          </select>
          <div
            class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>

        <!-- Advanced filters button -->
        <button
          @click="$refs.filtersPanel.classList.toggle('hidden')"
          class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2 text-gray-500"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
              clip-rule="evenodd"
            />
          </svg>
          Filters
        </button>

        <!-- Bulk actions -->
        <div class="relative">
          <button
            @click="bulkActionOpen = !bulkActionOpen"
            class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="selectedItems.length === 0"
            :class="{
              'opacity-50 cursor-not-allowed': selectedItems.length === 0,
            }"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"
              />
            </svg>
            Bulk Actions ({{ selectedItems.length }})
          </button>

          <!-- Bulk actions menu -->
          <div
            v-if="bulkActionOpen"
            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
          >
            <div class="py-1">
              <button
                @click="bulkUpdateStatus('published')"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Publish Selected
              </button>
              <button
                @click="bulkUpdateStatus('draft')"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Move to Drafts
              </button>
              <button
                @click="bulkUpdateStatus('archived')"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Archive Selected
              </button>
              <button
                @click="bulkToggleFeatured(true)"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Mark as Featured
              </button>
              <button
                @click="bulkToggleFeatured(false)"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Remove Featured
              </button>
              <div class="border-t border-gray-100 my-1"></div>
              <button
                @click="bulkDeleteItems"
                class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50"
              >
                Delete Selected
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Advanced filters panel (hidden by default) -->
      <div ref="filtersPanel" class="hidden mt-4 pt-4 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Content types filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Content Types</label
            >
            <div class="space-y-2">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="type-article"
                  v-model="filters.types"
                  value="article"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="type-article" class="ml-2 text-sm text-gray-700"
                  >Articles</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="type-lesson"
                  v-model="filters.types"
                  value="lesson"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="type-lesson" class="ml-2 text-sm text-gray-700"
                  >Lessons</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="type-resource"
                  v-model="filters.types"
                  value="resource"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="type-resource" class="ml-2 text-sm text-gray-700"
                  >Resources</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="type-faq"
                  v-model="filters.types"
                  value="faq"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="type-faq" class="ml-2 text-sm text-gray-700"
                  >FAQs</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="type-announcement"
                  v-model="filters.types"
                  value="announcement"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  for="type-announcement"
                  class="ml-2 text-sm text-gray-700"
                  >Announcements</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="type-landing_page"
                  v-model="filters.types"
                  value="landing_page"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  for="type-landing_page"
                  class="ml-2 text-sm text-gray-700"
                  >Landing Pages</label
                >
              </div>
            </div>
          </div>

          <!-- Categories filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Categories</label
            >
            <div class="max-h-36 overflow-y-auto space-y-2 pr-2">
              <div
                v-for="category in categories"
                :key="category.id"
                class="flex items-center"
              >
                <input
                  type="checkbox"
                  :id="`category-${category.id}`"
                  v-model="filters.categories"
                  :value="category.name"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  :for="`category-${category.id}`"
                  class="ml-2 text-sm text-gray-700"
                >
                  {{ category.name }} ({{ category.count }})
                </label>
              </div>
            </div>
          </div>

          <!-- Status filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Status</label
            >
            <div class="space-y-2">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="status-published"
                  v-model="filters.status"
                  value="published"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="status-published" class="ml-2 text-sm text-gray-700"
                  >Published</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="status-draft"
                  v-model="filters.status"
                  value="draft"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="status-draft" class="ml-2 text-sm text-gray-700"
                  >Draft</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="status-under_review"
                  v-model="filters.status"
                  value="under_review"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  for="status-under_review"
                  class="ml-2 text-sm text-gray-700"
                  >Under Review</label
                >
              </div>
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="status-archived"
                  v-model="filters.status"
                  value="archived"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="status-archived" class="ml-2 text-sm text-gray-700"
                  >Archived</label
                >
              </div>
            </div>
          </div>

          <!-- Date and featured filters -->
          <div>
            <!-- Date range filter -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Date Range</label
              >
              <div class="flex flex-col space-y-2">
                <div>
                  <label for="date-from" class="block text-xs text-gray-500"
                    >From</label
                  >
                  <input
                    type="date"
                    id="date-from"
                    v-model="filters.dateRange.start"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label for="date-to" class="block text-xs text-gray-500"
                    >To</label
                  >
                  <input
                    type="date"
                    id="date-to"
                    v-model="filters.dateRange.end"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
              </div>
            </div>

            <!-- Featured filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Featured Status</label
              >
              <div class="flex space-x-4">
                <div class="flex items-center">
                  <input
                    type="radio"
                    id="featured-all"
                    :checked="filters.featured === null"
                    @change="filters.featured = null"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label for="featured-all" class="ml-2 text-sm text-gray-700"
                    >All</label
                  >
                </div>
                <div class="flex items-center">
                  <input
                    type="radio"
                    id="featured-yes"
                    :checked="filters.featured === true"
                    @change="filters.featured = true"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label for="featured-yes" class="ml-2 text-sm text-gray-700"
                    >Featured</label
                  >
                </div>
                <div class="flex items-center">
                  <input
                    type="radio"
                    id="featured-no"
                    :checked="filters.featured === false"
                    @change="filters.featured = false"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label for="featured-no" class="ml-2 text-sm text-gray-700"
                    >Not Featured</label
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content table -->
    <div
      class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
    >
      <!-- Loading state -->
      <div v-if="isLoading" class="animate-pulse p-4">
        <div class="h-6 bg-gray-200 rounded w-1/4 mb-4"></div>
        <div class="space-y-3">
          <div class="h-4 bg-gray-200 rounded w-full"></div>
          <div class="h-4 bg-gray-200 rounded w-full"></div>
          <div class="h-4 bg-gray-200 rounded w-full"></div>
          <div class="h-4 bg-gray-200 rounded w-full"></div>
          <div class="h-4 bg-gray-200 rounded w-full"></div>
        </div>
      </div>

      <!-- Content table -->
      <div
        v-else-if="paginatedContentItems.length > 0"
        class="min-w-full divide-y divide-gray-200"
      >
        <!-- Table header -->
        <div
          class="bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider grid grid-cols-12 gap-4"
        >
          <div class="col-span-1 flex items-center">
            <input
              type="checkbox"
              :checked="allSelected"
              @change="toggleSelectAll"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
          </div>
          <div class="col-span-4 md:col-span-3">Title</div>
          <div class="hidden md:block md:col-span-2">Author</div>
          <div class="col-span-2">Type</div>
          <div class="col-span-2">Status</div>
          <div class="hidden md:block md:col-span-2">Last Modified</div>
          <div class="col-span-3 md:col-span-2">Actions</div>
        </div>

        <!-- Table body -->
        <div class="bg-white divide-y divide-gray-200">
          <div
            v-for="item in paginatedContentItems"
            :key="item.id"
            class="px-6 py-4 grid grid-cols-12 gap-4 hover:bg-gray-50 transition-colors"
          >
            <!-- Checkbox -->
            <div class="col-span-1 flex items-center">
              <input
                type="checkbox"
                :checked="selectedItems.includes(item.id)"
                @change="toggleSelect(item.id)"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </div>

            <!-- Title -->
            <div class="col-span-4 md:col-span-3">
              <div class="flex items-center">
                <div
                  class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3"
                >
                  <svg
                    v-if="!item.hasMedia"
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
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>
                  <img
                    v-else
                    :src="`/images/courses/c3.jpg`"
                    alt="Thumbnail"
                    class="h-10 w-10 object-cover rounded-lg"
                  />
                </div>
                <div>
                  <div
                    @click="viewContentDetails(item)"
                    class="font-medium text-indigo-600 hover:text-indigo-900 cursor-pointer truncate max-w-xs"
                  >
                    {{ item.title }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    <span>{{ item.category }}</span>
                    <span
                      v-if="item.featured"
                      class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3 mr-0.5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                      Featured
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Author -->
            <div class="hidden md:flex md:col-span-2 items-center">
              <img
                :src="item.author.avatar"
                :alt="item.author.name"
                class="h-6 w-6 rounded-full mr-2"
              />
              <span class="text-sm text-gray-900">{{ item.author.name }}</span>
            </div>

            <!-- Type -->
            <div class="col-span-2 flex items-center">
              <span
                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getContentTypeBadgeClass(item.type)"
              >
                {{
                  item.type.replace("_", " ").charAt(0).toUpperCase() +
                  item.type.replace("_", " ").slice(1)
                }}
              </span>
            </div>

            <!-- Status -->
            <div class="col-span-2 flex items-center">
              <span
                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getContentStatusBadgeClass(item.status)"
              >
                {{
                  item.status.replace("_", " ").charAt(0).toUpperCase() +
                  item.status.replace("_", " ").slice(1)
                }}
              </span>
            </div>

            <!-- Last Modified -->
            <div
              class="hidden md:flex md:col-span-2 items-center text-sm text-gray-500"
            >
              {{ formatDate(item.dateModified) }}
            </div>

            <!-- Actions -->
            <div class="col-span-3 md:col-span-2 flex space-x-2">
              <button
                @click="viewContentDetails(item)"
                class="text-gray-500 hover:text-indigo-700 transition-colors focus:outline-none"
                title="View Details"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
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
              </button>
              <NuxtLink
                :to="`/dashboard/admin/content/edit/${item.id}`"
                class="text-gray-500 hover:text-indigo-700 transition-colors focus:outline-none"
                title="Edit"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                  />
                </svg>
              </NuxtLink>
              <button
                @click="toggleFeatured(item)"
                class="text-gray-500 hover:text-yellow-500 transition-colors focus:outline-none"
                :class="{ 'text-yellow-500': item.featured }"
                :title="
                  item.featured ? 'Remove from Featured' : 'Add to Featured'
                "
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
              </button>
              <button
                @click="confirmDelete(item)"
                class="text-gray-500 hover:text-red-600 transition-colors focus:outline-none"
                title="Delete"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="p-8 text-center">
        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-full w-full"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No content found</h3>
        <p class="text-gray-500 mb-6">
          {{
            searchQuery
              ? "No content matches your search criteria."
              : "There are no content items to display."
          }}
        </p>
        <div class="flex justify-center">
          <NuxtLink
            to="/dashboard/admin/content/create"
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
            Create New Content
          </NuxtLink>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="paginatedContentItems.length > 0"
        class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
      >
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(page - 1)"
            :disabled="page === 1"
            :class="{ 'opacity-50 cursor-not-allowed': page === 1 }"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </button>
          <button
            @click="changePage(page + 1)"
            :disabled="page === totalPages"
            :class="{ 'opacity-50 cursor-not-allowed': page === totalPages }"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </button>
        </div>
        <div
          class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
        >
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{
                (page - 1) * itemsPerPage + 1
              }}</span>
              to
              <span class="font-medium">{{
                Math.min(page * itemsPerPage, filteredContentItems.length)
              }}</span>
              of
              <span class="font-medium">{{ filteredContentItems.length }}</span>
              results
            </p>
          </div>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <button
                @click="changePage(page - 1)"
                :disabled="page === 1"
                :class="{ 'opacity-50 cursor-not-allowed': page === 1 }"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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

              <template v-for="pageNum in totalPages" :key="pageNum">
                <button
                  v-if="
                    pageNum === page ||
                    pageNum === 1 ||
                    pageNum === totalPages ||
                    (pageNum >= page - 1 && pageNum <= page + 1)
                  "
                  @click="changePage(pageNum)"
                  :class="[
                    pageNum === page
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  ]"
                >
                  {{ pageNum }}
                </button>
                <span
                  v-else-if="
                    (pageNum === 2 && page > 3) ||
                    (pageNum === totalPages - 1 && page < totalPages - 2)
                  "
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                >
                  ...
                </span>
              </template>

              <button
                @click="changePage(page + 1)"
                :disabled="page === totalPages"
                :class="{
                  'opacity-50 cursor-not-allowed': page === totalPages,
                }"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
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
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-red-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Delete Content
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete "{{ itemToDelete?.title }}"? This
            action cannot be undone.
          </p>
        </div>

        <!-- Confirmation input -->
        <div class="mb-4">
          <label
            for="confirm-delete"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Type <span class="font-semibold">delete</span> to confirm:
          </label>
          <input
            id="confirm-delete"
            v-model="deleteConfirmText"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="Type 'delete' here"
          />
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="deleteItem"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="deleteConfirmText !== 'delete'"
            :class="{
              'opacity-50 cursor-not-allowed': deleteConfirmText !== 'delete',
            }"
          >
            Delete Content
          </button>
        </div>
      </div>
    </div>

    <!-- Content detail modal -->
    <div
      v-if="showContentDetails && selectedContentItem"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-xl"
      >
        <div
          class="flex justify-between items-start px-6 py-4 border-b border-gray-200"
        >
          <h3 class="text-lg font-medium text-gray-900">Content Details</h3>
          <button
            @click="showContentDetails = false"
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

        <div class="p-6">
          <!-- Content header info -->
          <div
            class="flex flex-col md:flex-row md:items-start md:space-x-6 mb-8"
          >
            <!-- Content thumbnail/image -->
            <div class="w-full md:w-1/3 mb-4 md:mb-0">
              <div
                class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center"
              >
                <img
                  v-if="selectedContentItem.hasMedia"
                  src="/images/courses/c3.jpg"
                  alt="Content thumbnail"
                  class="object-cover w-full h-full"
                />
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-16 w-16 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
              </div>
            </div>

            <!-- Content details -->
            <div class="w-full md:w-2/3">
              <h2 class="text-2xl font-bold text-gray-900 mb-2">
                {{ selectedContentItem.title }}
              </h2>

              <div class="flex flex-wrap gap-2 mb-4">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getContentTypeBadgeClass(selectedContentItem.type)"
                >
                  {{
                    selectedContentItem.type
                      .replace("_", " ")
                      .charAt(0)
                      .toUpperCase() +
                    selectedContentItem.type.replace("_", " ").slice(1)
                  }}
                </span>

                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="
                    getContentStatusBadgeClass(selectedContentItem.status)
                  "
                >
                  {{
                    selectedContentItem.status
                      .replace("_", " ")
                      .charAt(0)
                      .toUpperCase() +
                    selectedContentItem.status.replace("_", " ").slice(1)
                  }}
                </span>

                <span
                  v-if="selectedContentItem.featured"
                  class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  Featured
                </span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-500">Category</h4>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ selectedContentItem.category }}
                  </p>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500">Author</h4>
                  <div class="mt-1 flex items-center">
                    <img
                      :src="selectedContentItem.author.avatar"
                      :alt="selectedContentItem.author.name"
                      class="h-6 w-6 rounded-full mr-2"
                    />
                    <span class="text-sm text-gray-900">{{
                      selectedContentItem.author.name
                    }}</span>
                  </div>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500">
                    Created Date
                  </h4>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ formatDate(selectedContentItem.dateCreated) }}
                  </p>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500">
                    Last Modified
                  </h4>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ formatDate(selectedContentItem.dateModified) }}
                  </p>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500">Views</h4>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ selectedContentItem.views.toLocaleString() }}
                  </p>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500">ID</h4>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ selectedContentItem.id }}
                  </p>
                </div>
              </div>

              <!-- Content description placeholder -->
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">
                  Description
                </h4>
                <p class="text-sm text-gray-700">
                  {{
                    selectedContentItem.type === "article"
                      ? "This article provides comprehensive information about web development fundamentals, helping beginners understand the core concepts and technologies used in modern web applications."
                      : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
                  }}
                </p>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="border-t border-gray-200 pt-4 flex justify-end space-x-3">
            <button
              @click="showContentDetails = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Close
            </button>
            <NuxtLink
              :to="`/dashboard/admin/content/edit/${selectedContentItem.id}`"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Edit Content
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Transitions and animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide animation for filters panel */
.slide-enter-active,
.slide-leave-active {
  transition: max-height 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  max-height: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c5c5c5;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Loading animation */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Create aspect ratio container for thumbnails */
.aspect-w-16 {
  position: relative;
  padding-bottom: calc(9 / 16 * 100%);
}

.aspect-h-9 > * {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
}
</style>
