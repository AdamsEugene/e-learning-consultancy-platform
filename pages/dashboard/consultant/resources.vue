<!-- pages/dashboard/consultant/resources/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Resources - Consultant Dashboard",
});

// Define resource types
type ResourceType =
  | "document"
  | "template"
  | "code_snippet"
  | "image"
  | "video"
  | "presentation"
  | "link";
type ResourceCategory =
  | "project_management"
  | "development"
  | "design"
  | "marketing"
  | "legal"
  | "finance"
  | "client_resources"
  | "other";

interface Resource {
  id: number;
  title: string;
  description?: string;
  type: ResourceType;
  category: ResourceCategory;
  tags: string[];
  url: string;
  thumbnail?: string;
  created: string;
  updated: string;
  size?: string;
  owner: {
    id: number;
    name: string;
    avatar: string;
  };
  isShared: boolean;
  shareWith?: Array<{
    id: number;
    name: string;
    avatar: string;
  }>;
  isFavorite: boolean;
  downloadCount: number;
  viewCount: number;
}

interface ResourceCollection {
  id: number;
  title: string;
  description?: string;
  resourceCount: number;
  thumbnail?: string;
  created: string;
  updated: string;
  owner: {
    id: number;
    name: string;
    avatar: string;
  };
  isShared: boolean;
}

// State for resources
const resources = ref<Resource[]>([]);
const collections = ref<ResourceCollection[]>([]);
const isLoading = ref(true);
const activeView = ref<"grid" | "list">("grid");
const activeTab = ref("all");
const showResourceModal = ref(false);
const showCreateResourceModal = ref(false);
const showCreateCollectionModal = ref(false);
const selectedResource = ref<Resource | null>(null);
const searchQuery = ref("");
const selectedCategory = ref<ResourceCategory | "all">("all");
const selectedType = ref<ResourceType | "all">("all");
const sortBy = ref("updated");

// Filter by tags
const selectedTags = ref<string[]>([]);
const availableTags = ref<string[]>([
  "client-facing",
  "internal",
  "proposal",
  "contract",
  "guide",
  "tutorial",
  "case-study",
  "ui-design",
  "code",
  "marketing",
  "best-practice",
  "meeting-notes",
]);

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(12);

// New resource data
const newResource = ref({
  title: "",
  description: "",
  type: "document" as ResourceType,
  category: "project_management" as ResourceCategory,
  tags: [] as string[],
  url: "",
  isShared: false,
});

// New collection data
const newCollection = ref({
  title: "",
  description: "",
  isShared: false,
});

// Generate mock data
const generateMockResources = (): Resource[] => {
  return [
    {
      id: 1,
      title: "Client Onboarding Template",
      description: "Standard template for new client onboarding process",
      type: "document",
      category: "project_management",
      tags: ["client-facing", "template", "onboarding"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-15T10:30:00Z",
      updated: "2025-04-10T14:45:00Z",
      size: "2.4 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      shareWith: [
        { id: 2, name: "Michael Johnson", avatar: "/api/placeholder/40/40" },
        { id: 3, name: "Emily Davis", avatar: "/api/placeholder/40/40" },
      ],
      isFavorite: true,
      downloadCount: 32,
      viewCount: 78,
    },
    {
      id: 2,
      title: "React Component Library Documentation",
      description: "Documentation for our custom React component library",
      type: "document",
      category: "development",
      tags: ["internal", "code", "documentation", "react"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-02-18T09:15:00Z",
      updated: "2025-04-08T11:30:00Z",
      size: "4.7 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 45,
      viewCount: 126,
    },
    {
      id: 3,
      title: "Project Estimation Spreadsheet",
      description:
        "Excel template for creating project cost and time estimates",
      type: "document",
      category: "project_management",
      tags: ["template", "estimation", "finance"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-05T15:20:00Z",
      updated: "2025-03-05T15:20:00Z",
      size: "1.8 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: false,
      isFavorite: true,
      downloadCount: 27,
      viewCount: 41,
    },
    {
      id: 4,
      title: "Client Proposal Template",
      description: "Standard template for creating client proposals",
      type: "presentation",
      category: "client_resources",
      tags: ["client-facing", "proposal", "template"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-01-25T10:10:00Z",
      updated: "2025-04-12T16:40:00Z",
      size: "5.2 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 63,
      viewCount: 128,
    },
    {
      id: 5,
      title: "API Authentication Snippet",
      description: "Code snippet for JWT authentication implementation",
      type: "code_snippet",
      category: "development",
      tags: ["code", "security", "authentication", "jwt"],
      url: "#",
      created: "2025-04-02T13:45:00Z",
      updated: "2025-04-02T13:45:00Z",
      size: "8 KB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: true,
      downloadCount: 51,
      viewCount: 87,
    },
    {
      id: 6,
      title: "User Research Report - Banking App",
      description: "Findings from user interviews for the banking app project",
      type: "document",
      category: "design",
      tags: ["user-research", "report", "banking-app"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-18T09:30:00Z",
      updated: "2025-03-18T09:30:00Z",
      size: "3.6 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 18,
      viewCount: 42,
    },
    {
      id: 7,
      title: "E-commerce Wireframe Kit",
      description: "Wireframe templates for e-commerce projects",
      type: "image",
      category: "design",
      tags: ["wireframe", "ui-design", "e-commerce"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-02-28T11:15:00Z",
      updated: "2025-02-28T11:15:00Z",
      size: "12.4 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: false,
      isFavorite: true,
      downloadCount: 35,
      viewCount: 89,
    },
    {
      id: 8,
      title: "Client Contract Template",
      description: "Standard legal contract template for new projects",
      type: "document",
      category: "legal",
      tags: ["contract", "legal", "template"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-01-10T14:20:00Z",
      updated: "2025-04-05T10:30:00Z",
      size: "1.2 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 42,
      viewCount: 67,
    },
    {
      id: 9,
      title: "Database Schema Documentation",
      description: "Documentation of our standard database schema",
      type: "document",
      category: "development",
      tags: ["database", "documentation", "schema"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-25T16:45:00Z",
      updated: "2025-03-25T16:45:00Z",
      size: "2.8 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 24,
      viewCount: 56,
    },
    {
      id: 10,
      title: "Marketing Strategy Presentation",
      description: "Presentation template for digital marketing strategies",
      type: "presentation",
      category: "marketing",
      tags: ["marketing", "presentation", "strategy"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-02-15T10:30:00Z",
      updated: "2025-04-01T14:15:00Z",
      size: "6.7 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: false,
      isFavorite: true,
      downloadCount: 37,
      viewCount: 82,
    },
    {
      id: 11,
      title: "Responsive CSS Grid System",
      description: "Custom CSS grid system for responsive designs",
      type: "code_snippet",
      category: "development",
      tags: ["code", "css", "responsive", "grid"],
      url: "#",
      created: "2025-04-10T13:20:00Z",
      updated: "2025-04-10T13:20:00Z",
      size: "15 KB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 29,
      viewCount: 64,
    },
    {
      id: 12,
      title: "Project Management Methodology Guide",
      description: "Comprehensive guide to our project management methodology",
      type: "document",
      category: "project_management",
      tags: ["guide", "best-practice", "methodology"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-01-20T11:10:00Z",
      updated: "2025-03-28T09:45:00Z",
      size: "4.5 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: true,
      downloadCount: 58,
      viewCount: 126,
    },
    {
      id: 13,
      title: "Client Feedback Survey Template",
      description:
        "Template for gathering client feedback after project completion",
      type: "link",
      category: "client_resources",
      tags: ["client-facing", "feedback", "survey"],
      url: "https://forms.example.com/client-feedback",
      created: "2025-03-12T15:30:00Z",
      updated: "2025-03-12T15:30:00Z",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 0,
      viewCount: 42,
    },
    {
      id: 14,
      title: "Invoice Template",
      description: "Standard template for creating client invoices",
      type: "document",
      category: "finance",
      tags: ["invoice", "finance", "template"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-02-10T13:40:00Z",
      updated: "2025-02-10T13:40:00Z",
      size: "1.5 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: false,
      downloadCount: 34,
      viewCount: 56,
    },
    {
      id: 15,
      title: "Development Environment Setup Guide",
      description:
        "Step-by-step guide for setting up the development environment",
      type: "document",
      category: "development",
      tags: ["guide", "setup", "development"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-05T09:15:00Z",
      updated: "2025-04-15T10:30:00Z",
      size: "2.2 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
      isFavorite: true,
      downloadCount: 62,
      viewCount: 135,
    },
    {
      id: 16,
      title: "Brand Style Guide Template",
      description: "Template for creating comprehensive brand style guides",
      type: "document",
      category: "design",
      tags: ["branding", "style-guide", "template"],
      url: "#",
      thumbnail: "/api/placeholder/400/225",
      created: "2025-02-22T14:50:00Z",
      updated: "2025-02-22T14:50:00Z",
      size: "8.3 MB",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: false,
      isFavorite: false,
      downloadCount: 28,
      viewCount: 67,
    },
  ];
};

// Generate mock collections
const generateMockCollections = (): ResourceCollection[] => {
  return [
    {
      id: 1,
      title: "Project Management Templates",
      description: "Essential templates for project management",
      resourceCount: 5,
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-10T10:30:00Z",
      updated: "2025-04-10T14:45:00Z",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
    },
    {
      id: 2,
      title: "Development Guides",
      description: "Documentation and guides for developers",
      resourceCount: 7,
      thumbnail: "/api/placeholder/400/225",
      created: "2025-02-15T09:15:00Z",
      updated: "2025-04-12T11:30:00Z",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
    },
    {
      id: 3,
      title: "Client Resources",
      description: "Resources to share with clients",
      resourceCount: 4,
      thumbnail: "/api/placeholder/400/225",
      created: "2025-03-20T15:20:00Z",
      updated: "2025-03-20T15:20:00Z",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: false,
    },
    {
      id: 4,
      title: "Design Assets",
      description: "UI/UX design assets and templates",
      resourceCount: 6,
      thumbnail: "/api/placeholder/400/225",
      created: "2025-01-25T10:10:00Z",
      updated: "2025-04-05T16:40:00Z",
      owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
      isShared: true,
    },
  ];
};

// Fetch resources (mock)
const fetchResources = async () => {
  try {
    isLoading.value = true;
    // Simulating API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    resources.value = generateMockResources();
    collections.value = generateMockCollections();
  } catch (error) {
    console.error("Error fetching resources:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter resources based on active tab, search query, and filters
const filteredResources = computed(() => {
  let result = [...resources.value];

  // Apply favorite filter based on active tab
  if (activeTab.value === "favorites") {
    result = result.filter((resource) => resource.isFavorite);
  } else if (activeTab.value === "shared") {
    result = result.filter((resource) => resource.isShared);
  }

  // Apply category filter
  if (selectedCategory.value !== "all") {
    result = result.filter(
      (resource) => resource.category === selectedCategory.value
    );
  }

  // Apply type filter
  if (selectedType.value !== "all") {
    result = result.filter((resource) => resource.type === selectedType.value);
  }

  // Apply tag filters
  if (selectedTags.value.length > 0) {
    result = result.filter((resource) =>
      selectedTags.value.some((tag) => resource.tags.includes(tag))
    );
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (resource) =>
        resource.title.toLowerCase().includes(query) ||
        (resource.description &&
          resource.description.toLowerCase().includes(query)) ||
        resource.tags.some((tag) => tag.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  if (sortBy.value === "title") {
    result.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sortBy.value === "updated") {
    result.sort(
      (a, b) => new Date(b.updated).getTime() - new Date(a.updated).getTime()
    );
  } else if (sortBy.value === "created") {
    result.sort(
      (a, b) => new Date(b.created).getTime() - new Date(a.created).getTime()
    );
  } else if (sortBy.value === "popular") {
    result.sort(
      (a, b) => b.viewCount + b.downloadCount - (a.viewCount + a.downloadCount)
    );
  } else if (sortBy.value === "size") {
    result.sort((a, b) => {
      if (!a.size || !b.size) return 0;
      const sizeA = parseFileSize(a.size);
      const sizeB = parseFileSize(b.size);
      return sizeB - sizeA;
    });
  }

  return result;
});

// Paginated resources
const paginatedResources = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return filteredResources.value.slice(startIndex, endIndex);
});

// Total pages
const totalPages = computed(() => {
  return Math.ceil(filteredResources.value.length / itemsPerPage.value);
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

// Format file size (helper)
const parseFileSize = (sizeString: string): number => {
  const parts = sizeString.split(" ");
  if (parts.length !== 2) return 0;

  const size = parseFloat(parts[0]);
  const unit = parts[1].toUpperCase();

  switch (unit) {
    case "KB":
      return size * 1024;
    case "MB":
      return size * 1024 * 1024;
    case "GB":
      return size * 1024 * 1024 * 1024;
    default:
      return size;
  }
};

// Get resource type icon
const getResourceTypeIcon = (type: ResourceType) => {
  switch (type) {
    case "document":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "template":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
      </svg>`;
    case "code_snippet":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>`;
    case "image":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
      </svg>`;
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "presentation":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
      </svg>`;
    case "link":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get type color
const getResourceTypeColor = (type: ResourceType) => {
  switch (type) {
    case "document":
      return "bg-blue-100 text-blue-800";
    case "template":
      return "bg-indigo-100 text-indigo-800";
    case "code_snippet":
      return "bg-amber-100 text-amber-800";
    case "image":
      return "bg-green-100 text-green-800";
    case "video":
      return "bg-red-100 text-red-800";
    case "presentation":
      return "bg-purple-100 text-purple-800";
    case "link":
      return "bg-teal-100 text-teal-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get category color
const getCategoryColor = (category: ResourceCategory) => {
  switch (category) {
    case "project_management":
      return "bg-blue-100 text-blue-800";
    case "development":
      return "bg-green-100 text-green-800";
    case "design":
      return "bg-purple-100 text-purple-800";
    case "marketing":
      return "bg-amber-100 text-amber-800";
    case "legal":
      return "bg-red-100 text-red-800";
    case "finance":
      return "bg-emerald-100 text-emerald-800";
    case "client_resources":
      return "bg-indigo-100 text-indigo-800";
    case "other":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format resource type display
const formatResourceType = (type: ResourceType) => {
  return type.replace("_", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Format category display
const formatCategory = (category: ResourceCategory) => {
  return category.replace("_", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Toggle favorite
const toggleFavorite = (resource: Resource) => {
  resource.isFavorite = !resource.isFavorite;
  // In a real app, this would make an API call
};

// View resource details
const viewResourceDetails = (resource: Resource) => {
  selectedResource.value = resource;
  showResourceModal.value = true;

  // Increment view count (in a real app, this would make an API call)
  resource.viewCount++;
};

// Download resource
const downloadResource = (resource: Resource) => {
  // In a real app, this would initiate a download
  // For now, just increment the download count
  resource.downloadCount++;

  // Mock download by opening in a new tab
  window.open(resource.url, "_blank");
};

// Toggle tag selection
const toggleTag = (tag: string) => {
  const index = selectedTags.value.indexOf(tag);
  if (index === -1) {
    selectedTags.value.push(tag);
  } else {
    selectedTags.value.splice(index, 1);
  }
};

// Create new resource
const createResource = () => {
  // Validate form data
  if (
    !newResource.value.title ||
    !newResource.value.type ||
    !newResource.value.category
  ) {
    // Show validation error
    return;
  }

  // In a real app, this would make an API call and handle file uploads
  const newId = Math.max(...resources.value.map((r) => r.id)) + 1;

  // Create resource
  const resource: Resource = {
    id: newId,
    title: newResource.value.title,
    description: newResource.value.description,
    type: newResource.value.type,
    category: newResource.value.category,
    tags: newResource.value.tags,
    url: newResource.value.url || "#",
    created: new Date().toISOString(),
    updated: new Date().toISOString(),
    owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
    isShared: newResource.value.isShared,
    isFavorite: false,
    downloadCount: 0,
    viewCount: 0,
  };

  // For image, document types etc., add placeholder thumbnail
  if (["document", "image", "presentation", "video"].includes(resource.type)) {
    resource.thumbnail = "/api/placeholder/400/225";
  }

  // Add size for files
  if (resource.type !== "link") {
    resource.size = "0.1 MB";
  }

  resources.value.push(resource);

  // Reset form and close modal
  newResource.value = {
    title: "",
    description: "",
    type: "document",
    category: "project_management",
    tags: [],
    url: "",
    isShared: false,
  };

  showCreateResourceModal.value = false;
};

// Create new collection
const createCollection = () => {
  // Validate form data
  if (!newCollection.value.title) {
    // Show validation error
    return;
  }

  // In a real app, this would make an API call
  const newId = Math.max(...collections.value.map((c) => c.id)) + 1;

  // Create collection
  const collection: ResourceCollection = {
    id: newId,
    title: newCollection.value.title,
    description: newCollection.value.description,
    resourceCount: 0,
    thumbnail: "/api/placeholder/400/225",
    created: new Date().toISOString(),
    updated: new Date().toISOString(),
    owner: { id: 1, name: "Sarah Parker", avatar: "/api/placeholder/40/40" },
    isShared: newCollection.value.isShared,
  };

  collections.value.push(collection);

  // Reset form and close modal
  newCollection.value = {
    title: "",
    description: "",
    isShared: false,
  };

  showCreateCollectionModal.value = false;
};

// Load data on mount
onMounted(() => {
  fetchResources();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Resources</h1>
          <p class="text-gray-600">
            Manage your resources, templates, and knowledge base
          </p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="showCreateCollectionModal = true"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"
              />
            </svg>
            New Collection
          </button>
          <button
            @click="showCreateResourceModal = true"
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
            Add Resource
          </button>
        </div>
      </div>
    </div>

    <!-- Resource Collections -->
    <div class="mb-8 bg-white rounded-lg shadow p-5 border border-gray-200">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-gray-900">Collections</h2>
        <button
          @click="showCreateCollectionModal = true"
          class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
              clip-rule="evenodd"
            />
          </svg>
          New Collection
        </button>
      </div>

      <div v-if="isLoading" class="py-6 flex justify-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-600"
        ></div>
      </div>

      <div v-else-if="collections.length === 0" class="py-6 text-center">
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
            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
          />
        </svg>
        <p class="text-gray-600">
          No collections found. Create your first collection to organize
          resources.
        </p>
        <button
          @click="showCreateCollectionModal = true"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
          Create Collection
        </button>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div
          v-for="collection in collections"
          :key="`collection-${collection.id}`"
          class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
        >
          <div class="relative">
            <img
              :src="collection.thumbnail"
              :alt="collection.title"
              class="w-full h-32 object-cover"
            />
            <div class="absolute top-2 right-2 flex space-x-1">
              <div
                v-if="collection.isShared"
                class="bg-indigo-100 rounded-full p-1"
                title="Shared"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-indigo-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                  />
                </svg>
              </div>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-medium text-gray-900 mb-1">
              {{ collection.title }}
            </h3>
            <p class="text-sm text-gray-500 mb-2 line-clamp-2">
              {{ collection.description }}
            </p>
            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-500"
                >{{ collection.resourceCount }} resources</span
              >
              <span class="text-xs text-gray-500">{{
                formatDate(collection.updated)
              }}</span>
            </div>
          </div>
          <div
            class="bg-gray-50 px-4 py-2 border-t border-gray-200 flex justify-between"
          >
            <button class="text-sm text-indigo-600 hover:text-indigo-800">
              Open
            </button>
            <div class="flex items-center space-x-1">
              <button
                class="p-1 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Filters and Controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div class="flex flex-col space-y-4">
        <div class="flex flex-wrap gap-4 justify-between">
          <!-- Tab Navigation -->
          <div class="flex space-x-1 overflow-x-auto">
            <button
              @click="activeTab = 'all'"
              class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
              :class="
                activeTab === 'all'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              All Resources
            </button>
            <button
              @click="activeTab = 'favorites'"
              class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
              :class="
                activeTab === 'favorites'
                  ? 'bg-amber-100 text-amber-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Favorites
            </button>
            <button
              @click="activeTab = 'shared'"
              class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
              :class="
                activeTab === 'shared'
                  ? 'bg-green-100 text-green-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Shared
            </button>
          </div>

          <!-- View toggle and search -->
          <div class="flex items-center space-x-2">
            <div class="flex border border-gray-300 rounded-md overflow-hidden">
              <button
                @click="activeView = 'grid'"
                class="px-3 py-1.5 transition-colors"
                :class="
                  activeView === 'grid'
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-700 hover:bg-gray-100'
                "
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                  />
                </svg>
              </button>
              <button
                @click="activeView = 'list'"
                class="px-3 py-1.5 transition-colors"
                :class="
                  activeView === 'list'
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-700 hover:bg-gray-100'
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
                    d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>

            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search resources..."
                class="w-40 sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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
          </div>
        </div>

        <div class="flex flex-wrap gap-4 justify-between">
          <!-- Filter Controls -->
          <div class="flex flex-wrap gap-2">
            <!-- Category Filter -->
            <div class="relative">
              <select
                v-model="selectedCategory"
                class="pl-3 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="all">All Categories</option>
                <option value="project_management">Project Management</option>
                <option value="development">Development</option>
                <option value="design">Design</option>
                <option value="marketing">Marketing</option>
                <option value="legal">Legal</option>
                <option value="finance">Finance</option>
                <option value="client_resources">Client Resources</option>
                <option value="other">Other</option>
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

            <!-- Type Filter -->
            <div class="relative">
              <select
                v-model="selectedType"
                class="pl-3 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="all">All Types</option>
                <option value="document">Document</option>
                <option value="template">Template</option>
                <option value="code_snippet">Code Snippet</option>
                <option value="image">Image</option>
                <option value="video">Video</option>
                <option value="presentation">Presentation</option>
                <option value="link">Link</option>
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

            <!-- Tags Filter (Dropdown) -->
            <div class="relative">
              <button
                @click="$refs.tagDropdown.classList.toggle('hidden')"
                class="flex items-center space-x-2 px-3 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <span>Tags</span>
                <span
                  v-if="selectedTags.length > 0"
                  class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded-full"
                >
                  {{ selectedTags.length }}
                </span>
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
              </button>

              <div
                ref="tagDropdown"
                class="absolute z-10 mt-2 w-64 bg-white rounded-md shadow-lg border border-gray-200 hidden"
              >
                <div class="p-3">
                  <div class="mb-2 text-sm font-medium text-gray-700">
                    Select Tags
                  </div>
                  <div class="max-h-40 overflow-y-auto">
                    <div
                      v-for="tag in availableTags"
                      :key="tag"
                      class="flex items-center mb-2"
                    >
                      <input
                        :id="`tag-${tag}`"
                        type="checkbox"
                        :checked="selectedTags.includes(tag)"
                        @change="toggleTag(tag)"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                      <label
                        :for="`tag-${tag}`"
                        class="ml-2 text-sm text-gray-700"
                        >{{ tag }}</label
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sort Control -->
          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-3 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="updated">Last Updated</option>
              <option value="created">Date Created</option>
              <option value="title">Title (A-Z)</option>
              <option value="popular">Most Popular</option>
              <option value="size">File Size</option>
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
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
        <div v-for="i in 6" :key="`skeleton-${i}`" class="animate-pulse">
          <div class="bg-gray-200 h-40 rounded-t-lg"></div>
          <div class="border border-gray-200 border-t-0 rounded-b-lg p-4">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
            <div class="flex justify-between">
              <div class="h-3 bg-gray-200 rounded w-1/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Grid View -->
    <div
      v-else-if="activeView === 'grid' && filteredResources.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4"
      >
        <div
          v-for="resource in paginatedResources"
          :key="`resource-grid-${resource.id}`"
          class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
        >
          <!-- Resource thumbnail (if available) -->
          <div
            v-if="resource.thumbnail"
            class="relative"
            @click="viewResourceDetails(resource)"
          >
            <img
              :src="resource.thumbnail"
              :alt="resource.title"
              class="w-full h-40 object-cover cursor-pointer"
            />
            <div class="absolute top-2 right-2 flex space-x-1">
              <button
                @click.stop="toggleFavorite(resource)"
                class="bg-white rounded-full p-1 shadow hover:bg-gray-50"
                :class="{
                  'text-amber-500': resource.isFavorite,
                  'text-gray-400': !resource.isFavorite,
                }"
                :title="
                  resource.isFavorite
                    ? 'Remove from favorites'
                    : 'Add to favorites'
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
              <div
                v-if="resource.isShared"
                class="bg-indigo-100 rounded-full p-1"
                title="Shared"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-indigo-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                  />
                </svg>
              </div>
            </div>

            <!-- Resource type badge -->
            <div class="absolute top-2 left-2">
              <span
                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                :class="getResourceTypeColor(resource.type)"
              >
                <span
                  class="mr-1"
                  v-html="getResourceTypeIcon(resource.type)"
                ></span>
                {{ formatResourceType(resource.type) }}
              </span>
            </div>
          </div>

          <!-- Resource info -->
          <div class="p-4">
            <h3
              class="font-medium text-gray-900 mb-1 hover:text-indigo-600 cursor-pointer"
              @click="viewResourceDetails(resource)"
            >
              {{ resource.title }}
            </h3>
            <p
              v-if="resource.description"
              class="text-sm text-gray-500 mb-2 line-clamp-2"
            >
              {{ resource.description }}
            </p>

            <!-- Tags -->
            <div
              v-if="resource.tags.length > 0"
              class="mb-2 flex flex-wrap gap-1"
            >
              <span
                v-for="tag in resource.tags.slice(0, 2)"
                :key="tag"
                class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded"
              >
                {{ tag }}
              </span>
              <span
                v-if="resource.tags.length > 2"
                class="inline-block text-gray-500 text-xs"
              >
                +{{ resource.tags.length - 2 }} more
              </span>
            </div>

            <!-- Stats: size and date -->
            <div
              class="flex items-center justify-between text-xs text-gray-500"
            >
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ formatDate(resource.updated) }}
              </div>
              <span v-if="resource.size">{{ resource.size }}</span>
            </div>
          </div>

          <!-- Actions footer -->
          <div
            class="bg-gray-50 px-4 py-2 border-t border-gray-200 flex justify-between items-center"
          >
            <div class="flex items-center space-x-3 text-xs text-gray-500">
              <div class="flex items-center" title="Views">
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
                {{ resource.viewCount }}
              </div>
              <div class="flex items-center" title="Downloads">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ resource.downloadCount }}
              </div>
            </div>
            <div class="flex items-center space-x-1">
              <button
                @click="downloadResource(resource)"
                class="p-1 text-gray-500 hover:text-indigo-600 rounded-full hover:bg-gray-100"
                title="Download"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <button
                class="p-1 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100"
                title="More options"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination for grid view -->
      <div
        class="px-4 py-3 flex items-center justify-between border-t border-gray-200"
      >
        <div class="flex-1 flex justify-between items-center">
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{
              (currentPage - 1) * itemsPerPage + 1
            }}</span>
            to
            <span class="font-medium">{{
              Math.min(currentPage * itemsPerPage, filteredResources.length)
            }}</span>
            of
            <span class="font-medium">{{ filteredResources.length }}</span>
            results
          </p>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <button
                @click="currentPage > 1 ? currentPage-- : null"
                :disabled="currentPage <= 1"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage <= 1 }"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>

              <button
                v-for="page in Math.min(5, totalPages)"
                :key="page"
                @click="currentPage = page"
                :class="{
                  'z-10 bg-indigo-50 border-indigo-500 text-indigo-600':
                    page === currentPage,
                  'bg-white border-gray-300 text-gray-500 hover:bg-gray-50':
                    page !== currentPage,
                }"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              >
                {{ page }}
              </button>

              <button
                @click="currentPage < totalPages ? currentPage++ : null"
                :disabled="currentPage >= totalPages"
                :class="{
                  'opacity-50 cursor-not-allowed': currentPage >= totalPages,
                }"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
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

    <!-- List View -->
    <div
      v-else-if="activeView === 'list' && filteredResources.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Resource
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Type & Category
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Tags
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Stats
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Updated
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="resource in paginatedResources"
              :key="`resource-list-${resource.id}`"
            >
              <!-- Resource info -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div
                    v-if="resource.thumbnail"
                    class="flex-shrink-0 h-10 w-10"
                  >
                    <img
                      class="h-10 w-10 rounded-md object-cover"
                      :src="resource.thumbnail"
                      :alt="resource.title"
                    />
                  </div>
                  <div
                    v-else
                    class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-md flex items-center justify-center"
                  >
                    <span
                      v-html="getResourceTypeIcon(resource.type)"
                      class="text-gray-500"
                    ></span>
                  </div>
                  <div class="ml-4 min-w-0 flex-1">
                    <div class="flex items-center">
                      <div
                        class="text-sm font-medium text-gray-900 truncate hover:text-indigo-600 cursor-pointer"
                        @click="viewResourceDetails(resource)"
                      >
                        {{ resource.title }}
                      </div>
                      <button
                        @click="toggleFavorite(resource)"
                        class="ml-2"
                        :class="{
                          'text-amber-500': resource.isFavorite,
                          'text-gray-300 hover:text-gray-400':
                            !resource.isFavorite,
                        }"
                        :title="
                          resource.isFavorite
                            ? 'Remove from favorites'
                            : 'Add to favorites'
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
                      <div
                        v-if="resource.isShared"
                        class="ml-2 bg-indigo-100 p-1 rounded-full"
                        title="Shared"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-3 w-3 text-indigo-600"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                          />
                        </svg>
                      </div>
                    </div>
                    <div
                      v-if="resource.description"
                      class="text-sm text-gray-500 truncate"
                    >
                      {{ resource.description }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Type & Category -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex flex-col space-y-1">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                    :class="getResourceTypeColor(resource.type)"
                  >
                    <span
                      class="mr-1"
                      v-html="getResourceTypeIcon(resource.type)"
                    ></span>
                    {{ formatResourceType(resource.type) }}
                  </span>
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                    :class="getCategoryColor(resource.category)"
                  >
                    {{ formatCategory(resource.category) }}
                  </span>
                </div>
              </td>

              <!-- Tags -->
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="tag in resource.tags.slice(0, 3)"
                    :key="tag"
                    class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded"
                  >
                    {{ tag }}
                  </span>
                  <span
                    v-if="resource.tags.length > 3"
                    class="text-gray-500 text-xs"
                  >
                    +{{ resource.tags.length - 3 }} more
                  </span>
                </div>
              </td>

              <!-- Stats -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center space-x-4">
                  <div class="flex items-center" title="Views">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1 text-gray-400"
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
                    {{ resource.viewCount }}
                  </div>
                  <div class="flex items-center" title="Downloads">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ resource.downloadCount }}
                  </div>
                  <div
                    v-if="resource.size"
                    class="flex items-center"
                    title="Size"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    {{ resource.size }}
                  </div>
                </div>
              </td>

              <!-- Updated date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(resource.updated) }}
              </td>

              <!-- Actions -->
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="downloadResource(resource)"
                    class="text-indigo-600 hover:text-indigo-900"
                    title="Download"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <button
                    class="text-gray-500 hover:text-gray-700"
                    title="More options"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                      />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination for list view -->
      <div
        class="px-6 py-3 flex items-center justify-between border-t border-gray-200"
      >
        <div class="flex-1 flex justify-between items-center">
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{
              (currentPage - 1) * itemsPerPage + 1
            }}</span>
            to
            <span class="font-medium">{{
              Math.min(currentPage * itemsPerPage, filteredResources.length)
            }}</span>
            of
            <span class="font-medium">{{ filteredResources.length }}</span>
            results
          </p>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <button
                @click="currentPage > 1 ? currentPage-- : null"
                :disabled="currentPage <= 1"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage <= 1 }"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>

              <button
                v-for="page in Math.min(5, totalPages)"
                :key="page"
                @click="currentPage = page"
                :class="{
                  'z-10 bg-indigo-50 border-indigo-500 text-indigo-600':
                    page === currentPage,
                  'bg-white border-gray-300 text-gray-500 hover:bg-gray-50':
                    page !== currentPage,
                }"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              >
                {{ page }}
              </button>

              <button
                @click="currentPage < totalPages ? currentPage++ : null"
                :disabled="currentPage >= totalPages"
                :class="{
                  'opacity-50 cursor-not-allowed': currentPage >= totalPages,
                }"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
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

    <!-- Empty State -->
    <div
      v-else-if="!isLoading"
      class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
    >
      <div
        class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12 text-gray-400"
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
      <h3 class="text-lg font-medium text-gray-900 mb-2">No resources found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery ||
          selectedCategory !== "all" ||
          selectedType !== "all" ||
          selectedTags.length > 0
            ? "No resources match your search criteria. Try adjusting your filters."
            : "You haven't added any resources yet."
        }}
      </p>
      <button
        @click="showCreateResourceModal = true"
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
        Add Your First Resource
      </button>
    </div>

    <!-- Resource Detail Modal -->
    <div
      v-if="showResourceModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="relative">
          <!-- Header with resource type color -->
          <div
            class="px-6 py-4"
            :class="
              selectedResource
                ? getResourceTypeColor(selectedResource.type)
                : ''
            "
          >
            <div class="flex justify-between items-start">
              <div class="flex items-center">
                <span
                  v-if="selectedResource"
                  class="mr-2"
                  v-html="getResourceTypeIcon(selectedResource.type)"
                ></span>
                <h3 class="text-lg font-bold">
                  {{ selectedResource?.title }}
                </h3>
              </div>
              <button
                @click="showResourceModal = false"
                class="text-gray-500 hover:text-gray-700"
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

          <!-- Content -->
          <div class="px-6 py-4">
            <!-- Resource thumbnail if available -->
            <div v-if="selectedResource?.thumbnail" class="mb-4">
              <img
                :src="selectedResource.thumbnail"
                :alt="selectedResource.title"
                class="w-full h-64 object-cover rounded-lg"
              />
            </div>

            <!-- Description -->
            <div v-if="selectedResource?.description" class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">
                Description
              </h4>
              <p class="text-gray-600">{{ selectedResource.description }}</p>
            </div>

            <!-- Resource metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-1">Details</h4>
                <ul class="text-sm text-gray-600 space-y-2">
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      :class="
                        selectedResource
                          ? getCategoryColor(selectedResource.category)
                          : ''
                      "
                    >
                      {{
                        selectedResource
                          ? formatCategory(selectedResource.category)
                          : ""
                      }}
                    </span>
                  </li>
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span
                      >Created:
                      {{
                        selectedResource
                          ? formatDate(selectedResource.created)
                          : ""
                      }}</span
                    >
                  </li>
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span
                      >Last Updated:
                      {{
                        selectedResource
                          ? formatDate(selectedResource.updated)
                          : ""
                      }}</span
                    >
                  </li>
                  <li v-if="selectedResource?.size" class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>Size: {{ selectedResource.size }}</span>
                  </li>
                </ul>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-1">Stats</h4>
                <ul class="text-sm text-gray-600 space-y-2">
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
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
                    <span>{{ selectedResource?.viewCount }} Views</span>
                  </li>
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>{{ selectedResource?.downloadCount }} Downloads</span>
                  </li>
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"
                      />
                    </svg>
                    <span>Owner: {{ selectedResource?.owner.name }}</span>
                  </li>
                  <li class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                      />
                    </svg>
                    <span
                      >Shared:
                      {{ selectedResource?.isShared ? "Yes" : "No" }}</span
                    >
                  </li>
                </ul>
              </div>
            </div>

            <!-- Tags -->
            <div v-if="selectedResource?.tags.length" class="mb-4">
              <h4 class="text-sm font-medium text-gray-900 mb-1">Tags</h4>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="tag in selectedResource.tags"
                  :key="tag"
                  class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded"
                >
                  {{ tag }}
                </span>
              </div>
            </div>

            <!-- Shared with users -->
            <div
              v-if="
                selectedResource?.shareWith &&
                selectedResource.shareWith.length > 0
              "
              class="mb-4"
            >
              <h4 class="text-sm font-medium text-gray-900 mb-1">
                Shared With
              </h4>
              <div class="flex -space-x-2 overflow-hidden">
                <img
                  v-for="user in selectedResource.shareWith"
                  :key="user.id"
                  :src="user.avatar"
                  :alt="user.name"
                  :title="user.name"
                  class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                />
              </div>
            </div>
          </div>

          <!-- Footer with actions -->
          <div
            class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200"
          >
            <div class="flex items-center">
              <button
                @click="toggleFavorite(selectedResource!)"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :class="{ 'text-amber-500': selectedResource?.isFavorite }"
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
                {{
                  selectedResource?.isFavorite
                    ? "Remove from Favorites"
                    : "Add to Favorites"
                }}
              </button>
            </div>
            <div class="flex items-center space-x-2">
              <button
                @click="showResourceModal = false"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Close
              </button>
              <button
                @click="
                  downloadResource(selectedResource!);
                  showResourceModal = false;
                "
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
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
                Download
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Resource Modal -->
    <div
      v-if="showCreateResourceModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-lg w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="px-6 py-4 bg-indigo-600 text-white">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold">Add New Resource</h3>
            <button
              @click="showCreateResourceModal = false"
              class="text-white hover:text-gray-200"
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

        <div class="p-6">
          <form @submit.prevent="createResource">
            <!-- Basic Information -->
            <div class="mb-4">
              <label
                for="resource-title"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Title *</label
              >
              <input
                id="resource-title"
                v-model="newResource.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Resource title"
              />
            </div>

            <div class="mb-4">
              <label
                for="resource-description"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <textarea
                id="resource-description"
                v-model="newResource.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Brief description of this resource"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label
                  for="resource-type"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Type *</label
                >
                <select
                  id="resource-type"
                  v-model="newResource.type"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="document">Document</option>
                  <option value="template">Template</option>
                  <option value="code_snippet">Code Snippet</option>
                  <option value="image">Image</option>
                  <option value="video">Video</option>
                  <option value="presentation">Presentation</option>
                  <option value="link">Link</option>
                </select>
              </div>

              <div>
                <label
                  for="resource-category"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Category *</label
                >
                <select
                  id="resource-category"
                  v-model="newResource.category"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="project_management">Project Management</option>
                  <option value="development">Development</option>
                  <option value="design">Design</option>
                  <option value="marketing">Marketing</option>
                  <option value="legal">Legal</option>
                  <option value="finance">Finance</option>
                  <option value="client_resources">Client Resources</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>

            <div class="mb-4">
              <label
                for="resource-tags"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Tags</label
              >
              <div class="flex flex-wrap gap-2 mb-2">
                <div
                  v-for="tag in newResource.tags"
                  :key="tag"
                  class="bg-indigo-100 text-indigo-700 text-xs py-1 px-2 rounded flex items-center"
                >
                  <span>{{ tag }}</span>
                  <button
                    type="button"
                    @click="
                      newResource.tags = newResource.tags.filter(
                        (t) => t !== tag
                      )
                    "
                    class="ml-1 text-indigo-500 hover:text-indigo-700"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
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
              <div class="flex items-center">
                <div class="relative flex-grow">
                  <select
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    @change="
                      $event.target.value &&
                      !newResource.tags.includes($event.target.value)
                        ? newResource.tags.push($event.target.value)
                        : null;
                      $event.target.value = '';
                    "
                  >
                    <option value="">Select or type a tag...</option>
                    <option
                      v-for="tag in availableTags.filter(
                        (tag) => !newResource.tags.includes(tag)
                      )"
                      :key="tag"
                      :value="tag"
                    >
                      {{ tag }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label
                for="resource-url"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                {{ newResource.type === "link" ? "URL *" : "File Upload *" }}
              </label>
              <input
                id="resource-url"
                v-model="newResource.url"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                :placeholder="
                  newResource.type === 'link'
                    ? 'https://example.com/resource'
                    : 'Upload a file...'
                "
              />
              <p class="mt-1 text-xs text-gray-500">
                {{
                  newResource.type === "link"
                    ? "Enter the URL of the external resource"
                    : "In a real app, this would be a file upload field"
                }}
              </p>
            </div>

            <div class="mb-4">
              <div class="flex items-center">
                <input
                  id="resource-shared"
                  v-model="newResource.isShared"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  for="resource-shared"
                  class="ml-2 block text-sm text-gray-900"
                >
                  Share with team
                </label>
              </div>
              <p class="mt-1 text-xs text-gray-500">
                When enabled, this resource will be visible to all team members
              </p>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
              <button
                type="button"
                @click="showCreateResourceModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Add Resource
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Create Collection Modal -->
    <div
      v-if="showCreateCollectionModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-lg w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="px-6 py-4 bg-indigo-600 text-white">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold">Create New Collection</h3>
            <button
              @click="showCreateCollectionModal = false"
              class="text-white hover:text-gray-200"
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

        <div class="p-6">
          <form @submit.prevent="createCollection">
            <div class="mb-4">
              <label
                for="collection-title"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Collection Name *</label
              >
              <input
                id="collection-title"
                v-model="newCollection.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Collection name"
              />
            </div>

            <div class="mb-4">
              <label
                for="collection-description"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <textarea
                id="collection-description"
                v-model="newCollection.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Brief description of this collection"
              ></textarea>
            </div>

            <div class="mb-4">
              <div class="flex items-center">
                <input
                  id="collection-shared"
                  v-model="newCollection.isShared"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  for="collection-shared"
                  class="ml-2 block text-sm text-gray-900"
                >
                  Share with team
                </label>
              </div>
              <p class="mt-1 text-xs text-gray-500">
                When enabled, this collection will be visible to all team
                members
              </p>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
              <button
                type="button"
                @click="showCreateCollectionModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Create Collection
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Two-line text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Pulse animation for badges */
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

/* Animation for resource and collection cards */
.hover\:shadow-md {
  transition: box-shadow 0.3s ease-in-out, transform 0.2s ease;
}

.hover\:shadow-md:hover {
  transform: translateY(-2px);
}

/* Loading animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Transition effects */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c0c0c0;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Tag dropdown */
.tag-dropdown {
  transition: all 0.2s ease-in-out;
}

/* Grid and list view transitions */
.grid-view-transition,
.list-view-transition {
  transition: all 0.3s ease-in-out;
}
</style>
