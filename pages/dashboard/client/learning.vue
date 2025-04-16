<!-- pages/dashboard/client/learning/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta (layout)
definePageMeta({
  layout: "dashboard-client",
});

// Set page head metadata
useHead({
  title: "Learning Hub - Client Dashboard",
  meta: [
    {
      name: "description",
      content:
        "Access educational resources, courses, and knowledge base articles to enhance your skills and project outcomes.",
    },
  ],
});

// State for resources and learning data
const isLoading = ref(true);
const searchQuery = ref("");
const activeTab = ref("recommended");
const activeFilter = ref("all");
const selectedCategory = ref("all");
const sortOrder = ref("newest");
const showResourceModal = ref(false);
const selectedResource = ref(null);

// Define resource types
interface Resource {
  id: string;
  title: string;
  description: string;
  type: "article" | "video" | "guide" | "template" | "course" | "case-study";
  category: string;
  tags: string[];
  thumbnail: string;
  author: {
    name: string;
    avatar: string;
    role: string;
  };
  date: string;
  duration: number; // minutes
  views: number;
  likes: number;
  bookmarked: boolean;
  completed: boolean;
  progress?: number; // percentage
  url?: string;
}

interface Category {
  id: string;
  name: string;
  count: number;
  color: string;
  icon: string;
}

// Mock data for learning resources
const resources = ref<Resource[]>([]);
const featuredResources = ref<Resource[]>([]);
const completedResources = ref<Resource[]>([]);
const inProgressResources = ref<Resource[]>([]);
const bookmarkedResources = ref<Resource[]>([]);

// Categories for filtering
const categories = ref<Category[]>([
  {
    id: "all",
    name: "All Categories",
    count: 0,
    color: "bg-gray-600",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
    </svg>`,
  },
  {
    id: "web-development",
    name: "Web Development",
    count: 0,
    color: "bg-blue-600",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
    </svg>`,
  },
  {
    id: "design",
    name: "UI/UX Design",
    count: 0,
    color: "bg-purple-600",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
      <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
    </svg>`,
  },
  {
    id: "project-management",
    name: "Project Management",
    count: 0,
    color: "bg-green-600",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
      <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
    </svg>`,
  },
  {
    id: "business",
    name: "Business & Strategy",
    count: 0,
    color: "bg-amber-600",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
      <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
    </svg>`,
  },
  {
    id: "data",
    name: "Data & Analytics",
    count: 0,
    color: "bg-indigo-600",
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
    </svg>`,
  },
]);

// User learning statistics
const learningStats = ref({
  totalResources: 0,
  completedResources: 0,
  inProgressResources: 0,
  hoursLearned: 0,
  certificatesEarned: 0,
});

// Latest activity
const latestActivity = ref([
  {
    id: "1",
    type: "completed",
    resourceTitle: "Agile Project Management Fundamentals",
    date: "2025-04-14T15:30:00Z",
  },
  {
    id: "2",
    type: "started",
    resourceTitle: "Client Communication Best Practices",
    date: "2025-04-13T10:15:00Z",
  },
  {
    id: "3",
    type: "bookmarked",
    resourceTitle: "Effective Requirements Gathering Techniques",
    date: "2025-04-12T14:45:00Z",
  },
  {
    id: "4",
    type: "certificate",
    resourceTitle: "Digital Transformation Strategy",
    date: "2025-04-10T09:20:00Z",
  },
]);

// Recommended resources for upcoming project
const recommendedForProject = ref([
  {
    id: "proj-1",
    title: "Building Scalable Web Applications",
    type: "course",
    thumbnail: "/api/placeholder/300/160",
    duration: 240,
  },
  {
    id: "proj-2",
    title: "User Experience Design Principles",
    type: "guide",
    thumbnail: "/api/placeholder/300/160",
    duration: 45,
  },
  {
    id: "proj-3",
    title: "Effective Client Feedback Implementation",
    type: "article",
    thumbnail: "/api/placeholder/300/160",
    duration: 15,
  },
]);

// Mock learning paths
const learningPaths = ref([
  {
    id: "path-1",
    title: "Project Management Essentials",
    description:
      "Master the fundamentals of project management to deliver successful projects on time and within budget.",
    progress: 40,
    totalResources: 8,
    completedResources: 3,
    thumbnail: "/api/placeholder/400/200",
    estimatedHours: 12,
  },
  {
    id: "path-2",
    title: "Digital Transformation",
    description:
      "Learn how to guide your organization through successful digital transformation initiatives.",
    progress: 20,
    totalResources: 10,
    completedResources: 2,
    thumbnail: "/api/placeholder/400/200",
    estimatedHours: 15,
  },
]);

// Helper to format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

// Helper to format time duration
const formatDuration = (minutes: number) => {
  if (minutes < 60) {
    return `${minutes} min`;
  } else {
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    if (remainingMinutes === 0) {
      return `${hours} hr`;
    } else {
      return `${hours} hr ${remainingMinutes} min`;
    }
  }
};

// Filter resources based on active tab, category and search query
const filteredResources = computed(() => {
  let result: Resource[] = [];

  // First determine which collection to use based on active tab
  switch (activeTab.value) {
    case "recommended":
      result = [...resources.value];
      break;
    case "completed":
      result = [...completedResources.value];
      break;
    case "inProgress":
      result = [...inProgressResources.value];
      break;
    case "bookmarked":
      result = [...bookmarkedResources.value];
      break;
    default:
      result = [...resources.value];
  }

  // Apply category filter if not 'all'
  if (selectedCategory.value !== "all") {
    result = result.filter(
      (resource) => resource.category === selectedCategory.value
    );
  }

  // Apply type filter if not 'all'
  if (activeFilter.value !== "all") {
    result = result.filter((resource) => resource.type === activeFilter.value);
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (resource) =>
        resource.title.toLowerCase().includes(query) ||
        resource.description.toLowerCase().includes(query) ||
        resource.tags.some((tag) => tag.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  if (sortOrder.value === "newest") {
    result.sort(
      (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
    );
  } else if (sortOrder.value === "popularity") {
    result.sort((a, b) => b.views - a.views);
  } else if (sortOrder.value === "duration") {
    result.sort((a, b) => a.duration - b.duration);
  }

  return result;
});

// Helper to get resource type badge color
const getResourceTypeBadge = (type: string) => {
  switch (type) {
    case "article":
      return "bg-blue-100 text-blue-800";
    case "video":
      return "bg-red-100 text-red-800";
    case "guide":
      return "bg-green-100 text-green-800";
    case "template":
      return "bg-purple-100 text-purple-800";
    case "course":
      return "bg-amber-100 text-amber-800";
    case "case-study":
      return "bg-indigo-100 text-indigo-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Helper to get resource type icon
const getResourceTypeIcon = (type: string) => {
  switch (type) {
    case "article":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "video":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
      </svg>`;
    case "guide":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
      </svg>`;
    case "template":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
      </svg>`;
    case "course":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
      </svg>`;
    case "case-study":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm1 8a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Helper to get activity icon
const getActivityIcon = (type: string) => {
  switch (type) {
    case "completed":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "started":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
      </svg>`;
    case "bookmarked":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
      </svg>`;
    case "certificate":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Activity text formatting
const getActivityText = (activity: any) => {
  switch (activity.type) {
    case "completed":
      return `You completed "${activity.resourceTitle}"`;
    case "started":
      return `You started learning "${activity.resourceTitle}"`;
    case "bookmarked":
      return `You bookmarked "${activity.resourceTitle}"`;
    case "certificate":
      return `You earned a certificate for "${activity.resourceTitle}"`;
    default:
      return `You interacted with "${activity.resourceTitle}"`;
  }
};

// Format relative time for activities
const getRelativeTime = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - date.getTime());
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 0) {
    return "Today";
  } else if (diffDays === 1) {
    return "Yesterday";
  } else if (diffDays < 7) {
    return `${diffDays} days ago`;
  } else if (diffDays < 30) {
    const weeks = Math.floor(diffDays / 7);
    return `${weeks} ${weeks === 1 ? "week" : "weeks"} ago`;
  } else {
    return formatDate(dateString);
  }
};

// Toggle bookmark on a resource
const toggleBookmark = (resource: Resource) => {
  resource.bookmarked = !resource.bookmarked;
  if (resource.bookmarked) {
    // Add to bookmarked collection if not already there
    if (!bookmarkedResources.value.some((r) => r.id === resource.id)) {
      bookmarkedResources.value.push(resource);
    }
  } else {
    // Remove from bookmarked collection
    bookmarkedResources.value = bookmarkedResources.value.filter(
      (r) => r.id !== resource.id
    );
  }
};

// View resource details
const viewResource = (resource: Resource) => {
  selectedResource.value = resource;
  showResourceModal.value = true;
};

// Close resource modal
const closeResourceModal = () => {
  showResourceModal.value = false;
  selectedResource.value = null;
};

// Fetch resources (mock data)
const fetchResources = async () => {
  try {
    isLoading.value = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    const mockResources: Resource[] = [
      {
        id: "1",
        title: "Effective Requirements Gathering for IT Projects",
        description:
          "Learn how to effectively gather, document, and manage project requirements to ensure successful project outcomes.",
        type: "guide",
        category: "project-management",
        tags: ["requirements", "documentation", "project management"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Jennifer Wilson",
          avatar: "/api/placeholder/40/40",
          role: "Senior Project Manager",
        },
        date: "2025-04-01T10:30:00Z",
        duration: 45,
        views: 1240,
        likes: 86,
        bookmarked: false,
        completed: false,
        progress: 0,
      },
      {
        id: "2",
        title: "Introduction to Modern Web Development",
        description:
          "A comprehensive overview of modern web development technologies, frameworks, and best practices.",
        type: "course",
        category: "web-development",
        tags: ["web development", "JavaScript", "HTML", "CSS"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Michael Chen",
          avatar: "/api/placeholder/40/40",
          role: "Lead Developer",
        },
        date: "2025-03-15T14:45:00Z",
        duration: 320,
        views: 3450,
        likes: 248,
        bookmarked: true,
        completed: false,
        progress: 35,
      },
      {
        id: "3",
        title: "UI/UX Design Principles for Business Applications",
        description:
          "Learn essential design principles for creating intuitive and effective business application interfaces.",
        type: "video",
        category: "design",
        tags: ["UI", "UX", "design", "business applications"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Emma Rodriguez",
          avatar: "/api/placeholder/40/40",
          role: "UX Design Lead",
        },
        date: "2025-03-28T09:15:00Z",
        duration: 60,
        views: 1875,
        likes: 142,
        bookmarked: false,
        completed: true,
        progress: 100,
      },
      {
        id: "4",
        title: "Agile Project Management Fundamentals",
        description:
          "Master the basics of Agile methodology and learn how to apply it effectively in your projects.",
        type: "course",
        category: "project-management",
        tags: ["agile", "scrum", "project management"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "David Thompson",
          avatar: "/api/placeholder/40/40",
          role: "Agile Coach",
        },
        date: "2025-02-20T11:20:00Z",
        duration: 240,
        views: 4120,
        likes: 312,
        bookmarked: false,
        completed: true,
        progress: 100,
      },
      {
        id: "5",
        title: "Data Visualization Best Practices",
        description:
          "Learn how to create effective and insightful data visualizations that communicate complex information clearly.",
        type: "article",
        category: "data",
        tags: ["data visualization", "analytics", "reporting"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Sarah Johnson",
          avatar: "/api/placeholder/40/40",
          role: "Data Scientist",
        },
        date: "2025-04-05T15:40:00Z",
        duration: 15,
        views: 980,
        likes: 64,
        bookmarked: true,
        completed: false,
        progress: 0,
      },
      {
        id: "6",
        title: "Business Strategy for Digital Transformation",
        description:
          "Develop strategies for successful digital transformation initiatives within your organization.",
        type: "case-study",
        category: "business",
        tags: ["digital transformation", "strategy", "innovation"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Robert Miller",
          avatar: "/api/placeholder/40/40",
          role: "Digital Strategy Consultant",
        },
        date: "2025-03-10T13:30:00Z",
        duration: 30,
        views: 1650,
        likes: 124,
        bookmarked: false,
        completed: true,
        progress: 100,
      },
      {
        id: "7",
        title: "Client Communication Best Practices",
        description:
          "Learn effective communication strategies to maintain strong client relationships throughout project lifecycles.",
        type: "video",
        category: "project-management",
        tags: ["client management", "communication", "soft skills"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Olivia Spencer",
          avatar: "/api/placeholder/40/40",
          role: "Client Success Manager",
        },
        date: "2025-04-08T10:00:00Z",
        duration: 45,
        views: 1240,
        likes: 98,
        bookmarked: false,
        completed: false,
        progress: 20,
      },
      {
        id: "8",
        title: "Responsive Design Templates",
        description:
          "A collection of responsive design templates for various types of web applications and components.",
        type: "template",
        category: "design",
        tags: ["responsive design", "templates", "CSS"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Jason Kim",
          avatar: "/api/placeholder/40/40",
          role: "Frontend Developer",
        },
        date: "2025-03-22T16:15:00Z",
        duration: 20,
        views: 2180,
        likes: 178,
        bookmarked: true,
        completed: false,
        progress: 0,
      },
      {
        id: "9",
        title: "JavaScript Framework Comparison",
        description:
          "An in-depth comparison of popular JavaScript frameworks to help you choose the right one for your project.",
        type: "article",
        category: "web-development",
        tags: ["JavaScript", "frameworks", "React", "Vue", "Angular"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Ethan Williams",
          avatar: "/api/placeholder/40/40",
          role: "Software Architect",
        },
        date: "2025-04-03T09:45:00Z",
        duration: 25,
        views: 3250,
        likes: 215,
        bookmarked: false,
        completed: false,
        progress: 0,
      },
      {
        id: "10",
        title: "Market Analysis Techniques",
        description:
          "Learn effective market analysis techniques to make data-driven business decisions.",
        type: "guide",
        category: "business",
        tags: ["market analysis", "business intelligence", "strategy"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Natalie Parker",
          avatar: "/api/placeholder/40/40",
          role: "Business Analyst",
        },
        date: "2025-03-05T11:30:00Z",
        duration: 50,
        views: 1420,
        likes: 105,
        bookmarked: false,
        completed: false,
        progress: 0,
      },
      {
        id: "11",
        title: "Database Optimization Strategies",
        description:
          "Learn how to optimize database performance for complex web applications.",
        type: "video",
        category: "web-development",
        tags: ["database", "optimization", "performance"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Daniel Lee",
          avatar: "/api/placeholder/40/40",
          role: "Database Engineer",
        },
        date: "2025-02-28T14:20:00Z",
        duration: 55,
        views: 1960,
        likes: 132,
        bookmarked: false,
        completed: false,
        progress: 0,
      },
      {
        id: "12",
        title: "Data Analytics for Business Decision Making",
        description:
          "Leverage data analytics to make better business decisions and drive strategic initiatives.",
        type: "course",
        category: "data",
        tags: ["data analytics", "business intelligence", "decision making"],
        thumbnail: "/api/placeholder/400/200",
        author: {
          name: "Amanda Wilson",
          avatar: "/api/placeholder/40/40",
          role: "Analytics Director",
        },
        date: "2025-03-18T10:15:00Z",
        duration: 280,
        views: 2140,
        likes: 156,
        bookmarked: false,
        completed: false,
        progress: 0,
      },
    ];

    resources.value = mockResources;

    // Set featured resources (newest ones)
    featuredResources.value = [...mockResources]
      .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
      .slice(0, 4);

    // Filter completed resources
    completedResources.value = mockResources.filter((r) => r.completed);

    // Filter in-progress resources
    inProgressResources.value = mockResources.filter(
      (r) => r.progress > 0 && r.progress < 100
    );

    // Filter bookmarked resources
    bookmarkedResources.value = mockResources.filter((r) => r.bookmarked);

    // Update category counts
    categories.value = categories.value.map((category) => {
      if (category.id === "all") {
        return { ...category, count: mockResources.length };
      } else {
        const count = mockResources.filter(
          (r) => r.category === category.id
        ).length;
        return { ...category, count };
      }
    });

    // Update learning stats
    learningStats.value = {
      totalResources: mockResources.length,
      completedResources: completedResources.value.length,
      inProgressResources: inProgressResources.value.length,
      hoursLearned: Math.floor(
        completedResources.value.reduce((sum, r) => sum + r.duration, 0) / 60
      ),
      certificatesEarned: 2,
    };
  } catch (error) {
    console.error("Error fetching resources:", error);
  } finally {
    isLoading.value = false;
  }
};

// Load data when component mounts
onMounted(() => {
  fetchResources();
});
</script>

<template>
  <div>
    <!-- Page header with stats -->
    <div class="mb-6">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Learning Hub</h1>
          <p class="text-gray-600">
            Access educational resources to enhance your skills and project
            outcomes
          </p>
        </div>
      </div>

      <!-- Quick stats overview -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-indigo-100 rounded-full p-2 mr-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                />
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium">
                Available Resources
              </p>
              <p class="text-lg font-bold text-gray-900">
                {{ learningStats.totalResources }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-green-100 rounded-full p-2 mr-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-green-600"
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
            <div>
              <p class="text-xs text-gray-500 font-medium">Completed</p>
              <p class="text-lg font-bold text-gray-900">
                {{ learningStats.completedResources }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-blue-100 rounded-full p-2 mr-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-blue-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium">In Progress</p>
              <p class="text-lg font-bold text-gray-900">
                {{ learningStats.inProgressResources }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-amber-100 rounded-full p-2 mr-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-amber-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                />
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium">Hours Learned</p>
              <p class="text-lg font-bold text-gray-900">
                {{ learningStats.hoursLearned }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-purple-100 rounded-full p-2 mr-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-purple-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-500 font-medium">Certificates</p>
              <p class="text-lg font-bold text-gray-900">
                {{ learningStats.certificatesEarned }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content area -->
    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Left sidebar (categories) -->
      <div class="lg:w-64 flex-shrink-0">
        <!-- Categories -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-6">
          <h2 class="text-lg font-bold text-gray-900 mb-4">Categories</h2>
          <ul class="space-y-2">
            <li v-for="category in categories" :key="category.id">
              <button
                @click="selectedCategory = category.id"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-colors"
                :class="
                  selectedCategory === category.id
                    ? `text-white ${category.color}`
                    : 'text-gray-700 hover:bg-gray-100'
                "
              >
                <div class="flex items-center">
                  <span
                    class="mr-2"
                    :class="
                      selectedCategory === category.id
                        ? 'text-white'
                        : 'text-gray-500'
                    "
                    v-html="category.icon"
                  ></span>
                  <span>{{ category.name }}</span>
                </div>
                <span
                  class="text-xs py-1 px-2 rounded-full"
                  :class="
                    selectedCategory === category.id
                      ? 'bg-white bg-opacity-20'
                      : 'bg-gray-100 text-gray-600'
                  "
                >
                  {{ category.count }}
                </span>
              </button>
            </li>
          </ul>
        </div>

        <!-- Activity feed -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
          <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h2>
          <ul class="divide-y divide-gray-200">
            <li
              v-for="activity in latestActivity"
              :key="activity.id"
              class="py-3"
            >
              <div class="flex space-x-3">
                <div
                  class="flex-shrink-0"
                  v-html="getActivityIcon(activity.type)"
                ></div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-800">
                    {{ getActivityText(activity) }}
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ getRelativeTime(activity.date) }}
                  </p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Main content -->
      <div class="flex-1">
        <!-- Featured learning paths -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-900">Your Learning Paths</h2>
            <a
              href="#"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold"
              >View All</a
            >
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div
              v-for="path in learningPaths"
              :key="path.id"
              class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
            >
              <div class="flex items-center mb-4">
                <img
                  :src="path.thumbnail"
                  alt="Learning Path"
                  class="w-16 h-16 rounded-lg object-cover mr-4"
                />
                <div>
                  <h3 class="font-bold text-gray-900">{{ path.title }}</h3>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ path.estimatedHours }} hours total
                  </p>
                </div>
              </div>
              <p class="text-sm text-gray-700 mb-4 line-clamp-2">
                {{ path.description }}
              </p>

              <div class="mb-2">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                  <span>Progress</span>
                  <span
                    >{{ path.completedResources }}/{{
                      path.totalResources
                    }}
                    resources</span
                  >
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="bg-indigo-600 h-2.5 rounded-full"
                    :style="`width: ${path.progress}%`"
                  ></div>
                </div>
              </div>

              <div class="mt-4 flex justify-between">
                <a
                  href="#"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                  Continue Learning
                </a>
                <span
                  class="text-xs bg-indigo-100 text-indigo-800 py-1 px-2 rounded-full"
                >
                  {{ path.progress }}% complete
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Project recommendations -->
        <div
          class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow p-6 mb-6 text-white"
        >
          <h2 class="text-lg font-bold mb-1">
            Recommended for Your Current Project
          </h2>
          <p class="text-indigo-100 mb-6">
            Resources that will help you with your e-commerce implementation
            project.
          </p>

          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="resource in recommendedForProject"
              :key="resource.id"
              class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-lg overflow-hidden hover:bg-opacity-20 transition-all group cursor-pointer"
            >
              <div class="relative h-32">
                <img
                  :src="resource.thumbnail"
                  :alt="resource.title"
                  class="w-full h-full object-cover"
                />
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60"
                ></div>
                <div class="absolute bottom-0 left-0 right-0 p-3">
                  <span
                    class="inline-block px-2 py-1 text-xs font-bold rounded text-white capitalize mb-1"
                    :class="
                      resource.type === 'course'
                        ? 'bg-amber-500'
                        : resource.type === 'guide'
                        ? 'bg-green-500'
                        : 'bg-blue-500'
                    "
                  >
                    {{ resource.type }}
                  </span>
                  <h3
                    class="text-white font-bold text-sm group-hover:text-indigo-200 transition-colors line-clamp-2"
                  >
                    {{ resource.title }}
                  </h3>
                </div>
              </div>
              <div class="p-3 flex justify-between items-center">
                <span class="text-xs flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ formatDuration(resource.duration) }}
                </span>
                <button
                  class="text-white hover:text-indigo-200 transition-colors"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Use featured resources section -->
          <div
            v-if="
              activeTab === 'recommended' &&
              !searchQuery &&
              selectedCategory === 'all' &&
              activeFilter === 'all'
            "
            class="p-6 border-b border-gray-200"
          >
            <h3 class="text-lg font-semibold mb-4">Featured Resources</h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div
                v-for="resource in featuredResources"
                :key="`feature-${resource.id}`"
                class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all cursor-pointer"
                @click="viewResource(resource)"
              >
                <div class="relative h-36">
                  <img
                    :src="resource.thumbnail"
                    :alt="resource.title"
                    class="w-full h-full object-cover"
                  />
                  <div
                    class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60"
                  ></div>
                  <div class="absolute bottom-0 left-0 right-0 p-3">
                    <span
                      class="inline-block px-2 py-1 text-xs font-medium rounded-full mb-1"
                      :class="getResourceTypeBadge(resource.type)"
                    >
                      {{ resource.type }}
                    </span>
                    <h4 class="text-white font-bold text-sm line-clamp-2">
                      {{ resource.title }}
                    </h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Browse resources section -->
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="p-6 border-b border-gray-200">
            <div
              class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0"
            >
              <h2 class="text-lg font-bold text-gray-900">
                Browse Learning Resources
              </h2>

              <!-- Search input -->
              <div class="relative max-w-md">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search resources..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

          <!-- Tabs navigation -->
          <div class="border-b border-gray-200 flex overflow-x-auto">
            <button
              @click="activeTab = 'recommended'"
              class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
              :class="
                activeTab === 'recommended'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              Recommended
            </button>
            <button
              @click="activeTab = 'inProgress'"
              class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
              :class="
                activeTab === 'inProgress'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              In Progress ({{ inProgressResources.length }})
            </button>
            <button
              @click="activeTab = 'bookmarked'"
              class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
              :class="
                activeTab === 'bookmarked'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              Bookmarked ({{ bookmarkedResources.length }})
            </button>
            <button
              @click="activeTab = 'completed'"
              class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
              :class="
                activeTab === 'completed'
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              "
            >
              Completed ({{ completedResources.length }})
            </button>
          </div>

          <!-- Filters and sorting -->
          <div
            class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-center"
          >
            <div class="text-sm text-gray-500 mr-2">Filter by:</div>

            <!-- Resource type filters -->
            <button
              @click="activeFilter = 'all'"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                activeFilter === 'all'
                  ? 'bg-indigo-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              "
            >
              All Types
            </button>
            <button
              @click="activeFilter = 'article'"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                activeFilter === 'article'
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              "
            >
              Articles
            </button>
            <button
              @click="activeFilter = 'video'"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                activeFilter === 'video'
                  ? 'bg-red-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              "
            >
              Videos
            </button>
            <button
              @click="activeFilter = 'guide'"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                activeFilter === 'guide'
                  ? 'bg-green-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              "
            >
              Guides
            </button>
            <button
              @click="activeFilter = 'course'"
              class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
              :class="
                activeFilter === 'course'
                  ? 'bg-amber-600 text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              "
            >
              Courses
            </button>

            <div class="flex-1"></div>

            <!-- Sorting options -->
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-500">Sort:</span>
              <select
                v-model="sortOrder"
                class="text-sm border border-gray-300 rounded-md bg-white py-1 pl-3 pr-8 appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="newest">Newest</option>
                <option value="popularity">Most Popular</option>
                <option value="duration">Duration</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
