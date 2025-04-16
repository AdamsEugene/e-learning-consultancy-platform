<!-- pages/dashboard/admin/courses/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";

// Page meta
definePageMeta({
  layout: "dashboard-admin",
});

// Set page title
useHead({
  title: "Course Management - Admin Dashboard",
});

// Types
interface Course {
  id: number;
  title: string;
  instructor: {
    id: number;
    name: string;
    avatar: string;
  };
  category: string;
  price: number;
  status: "published" | "pending_review" | "draft" | "archived" | "rejected";
  featured: boolean;
  rating: number;
  students: number;
  revenue: number;
  lastUpdated: string;
  createdAt: string;
  publishedAt?: string;
}

interface FilterOptions {
  status: string;
  category: string;
  featured: string;
  instructorId: string;
  priceRange: [number, number];
  dateRange: string;
  searchQuery: string;
  sortBy: string;
  sortOrder: "asc" | "desc";
}

// State variables
const courses = ref<Course[]>([]);
const filteredCourses = ref<Course[]>([]);
const isLoading = ref(true);
const showDeleteModal = ref(false);
const courseToDelete = ref<Course | null>(null);
const deleteConfirmText = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(10);
const totalPages = computed(() =>
  Math.ceil(filteredCourses.value.length / itemsPerPage.value)
);
const showBulkActions = ref(false);
const selectedCourses = ref<number[]>([]);
const selectedAll = ref(false);
const categories = ref<string[]>([]);
const instructors = ref<{ id: number; name: string }[]>([]);

// Filter options
const filters = ref<FilterOptions>({
  status: "all",
  category: "all",
  featured: "all",
  instructorId: "all",
  priceRange: [0, 1000],
  dateRange: "all",
  searchQuery: "",
  sortBy: "lastUpdated",
  sortOrder: "desc",
});

// Fetch courses (mock data for demonstration)
const fetchCourses = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mock data
    courses.value = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        instructor: {
          id: 1,
          name: "Dr. Alex Thompson",
          avatar: "/api/placeholder/40/40",
        },
        category: "Web Development",
        price: 89.99,
        status: "published",
        featured: true,
        rating: 4.8,
        students: 1245,
        revenue: 87560.75,
        lastUpdated: "2025-03-15T10:30:00Z",
        createdAt: "2025-01-10T08:15:00Z",
        publishedAt: "2025-01-20T14:20:00Z",
      },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Mastery",
        instructor: {
          id: 1,
          name: "Dr. Alex Thompson",
          avatar: "/api/placeholder/40/40",
        },
        category: "Web Development",
        price: 79.99,
        status: "published",
        featured: false,
        rating: 4.9,
        students: 968,
        revenue: 65540.3,
        lastUpdated: "2025-02-18T14:45:00Z",
        createdAt: "2024-12-05T11:10:00Z",
        publishedAt: "2024-12-15T09:30:00Z",
      },
      {
        id: 3,
        title: "UI/UX Design Principles",
        instructor: {
          id: 2,
          name: "Emily Garcia",
          avatar: "/api/placeholder/40/40",
        },
        category: "Design",
        price: 69.99,
        status: "published",
        featured: true,
        rating: 4.7,
        students: 756,
        revenue: 43240.8,
        lastUpdated: "2025-01-25T09:15:00Z",
        createdAt: "2024-11-12T16:20:00Z",
        publishedAt: "2024-11-30T13:45:00Z",
      },
      {
        id: 4,
        title: "React Native for Mobile Development",
        instructor: {
          id: 3,
          name: "Michael Chen",
          avatar: "/api/placeholder/40/40",
        },
        category: "Mobile Development",
        price: 89.99,
        status: "pending_review",
        featured: false,
        rating: 0,
        students: 0,
        revenue: 0,
        lastUpdated: "2025-04-02T11:20:00Z",
        createdAt: "2025-03-28T10:10:00Z",
      },
      {
        id: 5,
        title: "Data Science with Python",
        instructor: {
          id: 4,
          name: "Sophia Williams",
          avatar: "/api/placeholder/40/40",
        },
        category: "Data Science",
        price: 94.99,
        status: "pending_review",
        featured: false,
        rating: 0,
        students: 0,
        revenue: 0,
        lastUpdated: "2025-03-28T16:50:00Z",
        createdAt: "2025-03-20T09:30:00Z",
      },
      {
        id: 6,
        title: "Node.js API Development",
        instructor: {
          id: 1,
          name: "Dr. Alex Thompson",
          avatar: "/api/placeholder/40/40",
        },
        category: "Web Development",
        price: 74.99,
        status: "archived",
        featured: false,
        rating: 4.3,
        students: 342,
        revenue: 19750.45,
        lastUpdated: "2024-10-15T08:30:00Z",
        createdAt: "2024-08-20T14:15:00Z",
        publishedAt: "2024-09-01T11:20:00Z",
      },
      {
        id: 7,
        title: "Cloud Architecture Fundamentals",
        instructor: {
          id: 5,
          name: "Robert Johnson",
          avatar: "/api/placeholder/40/40",
        },
        category: "Cloud Computing",
        price: 99.99,
        status: "draft",
        featured: false,
        rating: 0,
        students: 0,
        revenue: 0,
        lastUpdated: "2025-04-05T13:40:00Z",
        createdAt: "2025-04-01T10:15:00Z",
      },
      {
        id: 8,
        title: "Responsive Web Design Masterclass",
        instructor: {
          id: 2,
          name: "Emily Garcia",
          avatar: "/api/placeholder/40/40",
        },
        category: "Web Development",
        price: 64.99,
        status: "published",
        featured: false,
        rating: 4.6,
        students: 523,
        revenue: 27870.25,
        lastUpdated: "2025-02-02T10:10:00Z",
        createdAt: "2024-12-15T09:25:00Z",
        publishedAt: "2025-01-05T16:30:00Z",
      },
      {
        id: 9,
        title: "Machine Learning Fundamentals",
        instructor: {
          id: 4,
          name: "Sophia Williams",
          avatar: "/api/placeholder/40/40",
        },
        category: "Data Science",
        price: 94.99,
        status: "published",
        featured: true,
        rating: 4.9,
        students: 876,
        revenue: 67480.5,
        lastUpdated: "2025-03-10T11:25:00Z",
        createdAt: "2024-11-05T14:20:00Z",
        publishedAt: "2024-12-01T10:15:00Z",
      },
      {
        id: 10,
        title: "Advanced CSS and Sass",
        instructor: {
          id: 2,
          name: "Emily Garcia",
          avatar: "/api/placeholder/40/40",
        },
        category: "Web Development",
        price: 59.99,
        status: "rejected",
        featured: false,
        rating: 0,
        students: 0,
        revenue: 0,
        lastUpdated: "2025-02-15T14:30:00Z",
        createdAt: "2025-01-25T10:20:00Z",
      },
      {
        id: 11,
        title: "AWS Certified Solutions Architect",
        instructor: {
          id: 5,
          name: "Robert Johnson",
          avatar: "/api/placeholder/40/40",
        },
        category: "Cloud Computing",
        price: 129.99,
        status: "published",
        featured: true,
        rating: 4.8,
        students: 698,
        revenue: 73210.75,
        lastUpdated: "2025-03-20T10:15:00Z",
        createdAt: "2025-01-10T09:30:00Z",
        publishedAt: "2025-02-01T11:45:00Z",
      },
      {
        id: 12,
        title: "Introduction to Flutter Development",
        instructor: {
          id: 3,
          name: "Michael Chen",
          avatar: "/api/placeholder/40/40",
        },
        category: "Mobile Development",
        price: 84.99,
        status: "draft",
        featured: false,
        rating: 0,
        students: 0,
        revenue: 0,
        lastUpdated: "2025-04-08T15:40:00Z",
        createdAt: "2025-04-05T11:25:00Z",
      },
      {
        id: 13,
        title: "Django Web Framework Masterclass",
        instructor: {
          id: 6,
          name: "David Wilson",
          avatar: "/api/placeholder/40/40",
        },
        category: "Web Development",
        price: 79.99,
        status: "pending_review",
        featured: false,
        rating: 0,
        students: 0,
        revenue: 0,
        lastUpdated: "2025-04-10T13:20:00Z",
        createdAt: "2025-03-25T10:15:00Z",
      },
      {
        id: 14,
        title: "Adobe Photoshop for Beginners",
        instructor: {
          id: 2,
          name: "Emily Garcia",
          avatar: "/api/placeholder/40/40",
        },
        category: "Design",
        price: 54.99,
        status: "published",
        featured: false,
        rating: 4.5,
        students: 412,
        revenue: 18750.3,
        lastUpdated: "2025-02-25T09:30:00Z",
        createdAt: "2024-12-20T14:15:00Z",
        publishedAt: "2025-01-10T10:20:00Z",
      },
      {
        id: 15,
        title: "Blockchain Development with Solidity",
        instructor: {
          id: 7,
          name: "James Martin",
          avatar: "/api/placeholder/40/40",
        },
        category: "Blockchain",
        price: 109.99,
        status: "published",
        featured: true,
        rating: 4.7,
        students: 325,
        revenue: 29140.8,
        lastUpdated: "2025-03-05T11:45:00Z",
        createdAt: "2025-01-15T13:20:00Z",
        publishedAt: "2025-02-10T09:15:00Z",
      },
    ];

    // Extract unique categories and instructors for filters
    const uniqueCategories = new Set<string>();
    const uniqueInstructors = new Map<number, { id: number; name: string }>();

    courses.value.forEach((course) => {
      uniqueCategories.add(course.category);
      uniqueInstructors.set(course.instructor.id, {
        id: course.instructor.id,
        name: course.instructor.name,
      });
    });

    categories.value = Array.from(uniqueCategories);
    instructors.value = Array.from(uniqueInstructors.values());

    applyFilters();
  } catch (error) {
    console.error("Error fetching courses:", error);
  } finally {
    isLoading.value = false;
  }
};

// Apply filters to courses
const applyFilters = () => {
  let result = [...courses.value];

  // Status filter
  if (filters.value.status !== "all") {
    result = result.filter((course) => course.status === filters.value.status);
  }

  // Category filter
  if (filters.value.category !== "all") {
    result = result.filter(
      (course) => course.category === filters.value.category
    );
  }

  // Featured filter
  if (filters.value.featured !== "all") {
    const isFeatured = filters.value.featured === "featured";
    result = result.filter((course) => course.featured === isFeatured);
  }

  // Instructor filter
  if (filters.value.instructorId !== "all") {
    const instructorId = parseInt(filters.value.instructorId);
    result = result.filter((course) => course.instructor.id === instructorId);
  }

  // Price range filter
  result = result.filter(
    (course) =>
      course.price >= filters.value.priceRange[0] &&
      course.price <= filters.value.priceRange[1]
  );

  // Date range filter
  if (filters.value.dateRange !== "all") {
    const now = new Date();

    if (filters.value.dateRange === "last-30-days") {
      const thirtyDaysAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
      result = result.filter(
        (course) => new Date(course.lastUpdated) >= thirtyDaysAgo
      );
    } else if (filters.value.dateRange === "last-90-days") {
      const ninetyDaysAgo = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
      result = result.filter(
        (course) => new Date(course.lastUpdated) >= ninetyDaysAgo
      );
    } else if (filters.value.dateRange === "this-year") {
      const firstDayOfYear = new Date(now.getFullYear(), 0, 1);
      result = result.filter(
        (course) => new Date(course.lastUpdated) >= firstDayOfYear
      );
    }
  }

  // Search filter
  if (filters.value.searchQuery) {
    const query = filters.value.searchQuery.toLowerCase();
    result = result.filter(
      (course) =>
        course.title.toLowerCase().includes(query) ||
        course.instructor.name.toLowerCase().includes(query) ||
        course.category.toLowerCase().includes(query)
    );
  }

  // Sort results
  if (filters.value.sortBy) {
    result.sort((a, b) => {
      let valueA, valueB;

      switch (filters.value.sortBy) {
        case "title":
          valueA = a.title;
          valueB = b.title;
          return filters.value.sortOrder === "asc"
            ? valueA.localeCompare(valueB)
            : valueB.localeCompare(valueA);

        case "price":
          valueA = a.price;
          valueB = b.price;
          break;

        case "rating":
          valueA = a.rating;
          valueB = b.rating;
          break;

        case "students":
          valueA = a.students;
          valueB = b.students;
          break;

        case "revenue":
          valueA = a.revenue;
          valueB = b.revenue;
          break;

        case "createdAt":
          valueA = new Date(a.createdAt).getTime();
          valueB = new Date(b.createdAt).getTime();
          break;

        case "lastUpdated":
        default:
          valueA = new Date(a.lastUpdated).getTime();
          valueB = new Date(b.lastUpdated).getTime();
          break;
      }

      if (filters.value.sortOrder === "asc") {
        return valueA - valueB;
      } else {
        return valueB - valueA;
      }
    });
  }

  filteredCourses.value = result;
  currentPage.value = 1; // Reset to first page when filters change

  // Reset selection when filters change
  selectedCourses.value = [];
  selectedAll.value = false;
};

// Watch for changes to filters and reapply
watch(
  filters,
  () => {
    applyFilters();
  },
  { deep: true }
);

// Reset filters
const resetFilters = () => {
  filters.value = {
    status: "all",
    category: "all",
    featured: "all",
    instructorId: "all",
    priceRange: [0, 1000],
    dateRange: "all",
    searchQuery: "",
    sortBy: "lastUpdated",
    sortOrder: "desc",
  };
};

// Get status badge class
const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case "published":
      return "bg-green-100 text-green-800";
    case "pending_review":
      return "bg-yellow-100 text-yellow-800";
    case "draft":
      return "bg-gray-100 text-gray-800";
    case "archived":
      return "bg-blue-100 text-blue-800";
    case "rejected":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format date string
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format price with currency
const formatPrice = (price: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price);
};

// Format number with commas
const formatNumber = (num: number) => {
  return num.toLocaleString("en-US");
};

// Toggle select all courses
const toggleSelectAll = () => {
  selectedAll.value = !selectedAll.value;

  if (selectedAll.value) {
    // Get IDs of all courses on the current page
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = Math.min(
      startIndex + itemsPerPage.value,
      filteredCourses.value.length
    );
    const currentPageCourses = filteredCourses.value.slice(
      startIndex,
      endIndex
    );
    selectedCourses.value = currentPageCourses.map((course) => course.id);
  } else {
    selectedCourses.value = [];
  }

  showBulkActions.value = selectedCourses.value.length > 0;
};

// Toggle select individual course
const toggleSelectCourse = (courseId: number) => {
  const index = selectedCourses.value.indexOf(courseId);

  if (index === -1) {
    selectedCourses.value.push(courseId);
  } else {
    selectedCourses.value.splice(index, 1);
  }

  // Update "select all" state based on if all visible courses are selected
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = Math.min(
    startIndex + itemsPerPage.value,
    filteredCourses.value.length
  );
  const currentPageCourses = filteredCourses.value.slice(startIndex, endIndex);
  selectedAll.value = currentPageCourses.every((course) =>
    selectedCourses.value.includes(course.id)
  );

  showBulkActions.value = selectedCourses.value.length > 0;
};

// Apply bulk action to selected courses
const applyBulkAction = (action: string) => {
  if (selectedCourses.value.length === 0) return;

  // In a real app, this would make an API call
  console.log(`Applying ${action} to courses:`, selectedCourses.value);

  // Update course statuses locally for demo purposes
  if (
    ["publish", "archive", "reject", "feature", "unfeature"].includes(action)
  ) {
    courses.value = courses.value.map((course) => {
      if (selectedCourses.value.includes(course.id)) {
        switch (action) {
          case "publish":
            return {
              ...course,
              status: "published" as const,
              publishedAt: new Date().toISOString(),
            };
          case "archive":
            return { ...course, status: "archived" as const };
          case "reject":
            return { ...course, status: "rejected" as const };
          case "feature":
            return { ...course, featured: true };
          case "unfeature":
            return { ...course, featured: false };
        }
      }
      return course;
    });

    // Reset selections and reapply filters
    selectedCourses.value = [];
    selectedAll.value = false;
    showBulkActions.value = false;
    applyFilters();
  }
};

// Open delete confirmation modal
const confirmDelete = (course: Course) => {
  courseToDelete.value = course;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

// Delete course
const deleteCourse = () => {
  if (
    !courseToDelete.value ||
    deleteConfirmText.value !== courseToDelete.value.title
  ) {
    return;
  }

  // In a real app, this would make an API call
  courses.value = courses.value.filter(
    (course) => course.id !== courseToDelete.value?.id
  );

  // Close modal and reset state
  showDeleteModal.value = false;
  courseToDelete.value = null;
  deleteConfirmText.value = "";

  // Reapply filters to update the list
  applyFilters();
};

// Get visible courses for current page
const paginatedCourses = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = Math.min(
    startIndex + itemsPerPage.value,
    filteredCourses.value.length
  );
  return filteredCourses.value.slice(startIndex, endIndex);
});

// Generate array of page numbers
const pageNumbers = computed(() => {
  const totalPages = Math.ceil(
    filteredCourses.value.length / itemsPerPage.value
  );
  const pages = [];

  // For pagination with ellipsis
  if (totalPages <= 7) {
    // Show all pages if 7 or fewer
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i);
    }
  } else {
    // Show first page, last page, and pages around current
    pages.push(1);

    if (currentPage.value > 3) {
      pages.push("...");
    }

    // Pages around current
    const start = Math.max(2, currentPage.value - 1);
    const end = Math.min(totalPages - 1, currentPage.value + 1);

    for (let i = start; i <= end; i++) {
      pages.push(i);
    }

    if (currentPage.value < totalPages - 2) {
      pages.push("...");
    }

    pages.push(totalPages);
  }

  return pages;
});

// Update page
const setPage = (page: number | string) => {
  if (typeof page === "number") {
    currentPage.value = page;
  }
};

// Get page classes
const getPageClass = (page: number | string) => {
  if (page === "...") {
    return "relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300";
  }

  if (page === currentPage.value) {
    return "relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 hover:bg-indigo-700";
  }

  return "relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50";
};

// Calculate total platform statistics
const stats = computed(() => {
  return {
    totalCourses: courses.value.length,
    publishedCourses: courses.value.filter((c) => c.status === "published")
      .length,
    pendingReview: courses.value.filter((c) => c.status === "pending_review")
      .length,
    totalStudents: courses.value.reduce(
      (sum, course) => sum + course.students,
      0
    ),
    totalRevenue: courses.value.reduce(
      (sum, course) => sum + course.revenue,
      0
    ),
    featuredCourses: courses.value.filter((c) => c.featured).length,
  };
});

// Fetch data on component mount
onMounted(() => {
  fetchCourses();
});
</script>

<template>
  <div>
    <!-- Page header with stats cards -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Course Management
          </h1>
          <p class="text-gray-600">
            Manage all courses available on the platform
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
          <button
            @click="resetFilters"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="-ml-1 mr-2 h-5 w-5 text-gray-500"
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
          <NuxtLink
            to="/dashboard/admin/courses/create"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="-ml-1 mr-2 h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            Add New Course
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-indigo-100 rounded-full p-3 mr-4">
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
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Courses</p>
              <p class="text-xl font-bold text-gray-900">
                {{ stats.totalCourses }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-green-100 rounded-full p-3 mr-4">
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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Published</p>
              <p class="text-xl font-bold text-gray-900">
                {{ stats.publishedCourses }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-yellow-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-yellow-600"
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
            <div>
              <p class="text-sm text-gray-500 font-medium">Pending Review</p>
              <p class="text-xl font-bold text-gray-900">
                {{ stats.pendingReview }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Students</p>
              <p class="text-xl font-bold text-gray-900">
                {{ formatNumber(stats.totalStudents) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-purple-100 rounded-full p-3 mr-4">
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
              <p class="text-xl font-bold text-gray-900">
                {{ formatPrice(stats.totalRevenue) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-amber-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-amber-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Featured Courses</p>
              <p class="text-xl font-bold text-gray-900">
                {{ stats.featuredCourses }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div class="md:flex md:justify-between mb-4">
        <!-- Left side filters -->
        <div class="md:flex space-y-3 md:space-y-0 md:space-x-4 mb-4 md:mb-0">
          <!-- Status filter -->
          <div>
            <label
              for="status-filter"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Status</label
            >
            <select
              id="status-filter"
              v-model="filters.status"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="all">All Statuses</option>
              <option value="published">Published</option>
              <option value="pending_review">Pending Review</option>
              <option value="draft">Draft</option>
              <option value="archived">Archived</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>

          <!-- Category filter -->
          <div>
            <label
              for="category-filter"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Category</label
            >
            <select
              id="category-filter"
              v-model="filters.category"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="all">All Categories</option>
              <option
                v-for="category in categories"
                :key="category"
                :value="category"
              >
                {{ category }}
              </option>
            </select>
          </div>

          <!-- Instructor filter -->
          <div>
            <label
              for="instructor-filter"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Instructor</label
            >
            <select
              id="instructor-filter"
              v-model="filters.instructorId"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="all">All Instructors</option>
              <option
                v-for="instructor in instructors"
                :key="instructor.id"
                :value="instructor.id"
              >
                {{ instructor.name }}
              </option>
            </select>
          </div>

          <!-- Featured filter -->
          <div>
            <label
              for="featured-filter"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Featured</label
            >
            <select
              id="featured-filter"
              v-model="filters.featured"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="all">All Courses</option>
              <option value="featured">Featured</option>
              <option value="not-featured">Not Featured</option>
            </select>
          </div>
        </div>

        <!-- Right side search and sort -->
        <div class="md:flex space-y-3 md:space-y-0 md:space-x-4">
          <!-- Search -->
          <div class="flex-grow">
            <label
              for="search"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Search</label
            >
            <div class="relative">
              <input
                id="search"
                v-model="filters.searchQuery"
                type="text"
                placeholder="Search courses, instructors..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
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
            </div>
          </div>

          <!-- Sort by -->
          <div>
            <label
              for="sort-by"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Sort By</label
            >
            <div class="flex space-x-2">
              <select
                id="sort-by"
                v-model="filters.sortBy"
                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
              >
                <option value="lastUpdated">Last Updated</option>
                <option value="createdAt">Created Date</option>
                <option value="title">Title</option>
                <option value="price">Price</option>
                <option value="rating">Rating</option>
                <option value="students">Students</option>
                <option value="revenue">Revenue</option>
              </select>

              <button
                @click="
                  filters.sortOrder =
                    filters.sortOrder === 'asc' ? 'desc' : 'asc'
                "
                class="p-2 border border-gray-300 rounded-md"
                :title="
                  filters.sortOrder === 'asc' ? 'Ascending' : 'Descending'
                "
              >
                <svg
                  v-if="filters.sortOrder === 'asc'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional filters row (collapsible) -->
      <div class="pt-3 border-t border-gray-200 mt-3">
        <div class="md:flex md:space-x-4 items-end">
          <!-- Date range filter -->
          <div class="mb-3 md:mb-0">
            <label
              for="date-range"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Date Range</label
            >
            <select
              id="date-range"
              v-model="filters.dateRange"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option value="all">All Time</option>
              <option value="last-30-days">Last 30 Days</option>
              <option value="last-90-days">Last 90 Days</option>
              <option value="this-year">This Year</option>
            </select>
          </div>

          <!-- Price range filter - simplified for demo -->
          <div class="flex-grow md:flex md:space-x-4 items-center">
            <div class="mb-1 md:mb-0">
              <label
                for="min-price"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Price Range</label
              >
              <div class="flex items-center space-x-2">
                <div class="relative rounded-md">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    id="min-price"
                    type="number"
                    min="0"
                    v-model="filters.priceRange[0]"
                    placeholder="Min"
                    class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
                <span class="text-gray-500">to</span>
                <div class="relative rounded-md">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    id="max-price"
                    type="number"
                    min="0"
                    v-model="filters.priceRange[1]"
                    placeholder="Max"
                    class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Filter results count -->
          <div class="mt-3 md:mt-0 text-sm text-gray-500">
            <span class="font-medium">{{ filteredCourses.length }}</span>
            courses found
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk actions bar (visible when items are selected) -->
    <div
      v-if="showBulkActions"
      class="fixed bottom-0 left-0 right-0 z-20 bg-white shadow-lg border-t border-gray-200 p-4 transition-all duration-300 transform"
      style="animation: slideUp 0.3s ease-out forwards"
    >
      <div class="container mx-auto flex items-center justify-between">
        <div class="flex items-center">
          <span class="mr-4 font-medium"
            >{{ selectedCourses.length }} courses selected</span
          >
          <div class="flex space-x-2">
            <button
              @click="applyBulkAction('publish')"
              class="px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Publish
            </button>
            <button
              @click="applyBulkAction('archive')"
              class="px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Archive
            </button>
            <button
              @click="applyBulkAction('reject')"
              class="px-3 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Reject
            </button>
            <button
              @click="applyBulkAction('feature')"
              class="px-3 py-2 bg-amber-600 text-white text-sm font-medium rounded hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
            >
              Feature
            </button>
            <button
              @click="applyBulkAction('unfeature')"
              class="px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
              Unfeature
            </button>
          </div>
        </div>
        <button
          @click="
            selectedCourses = [];
            selectedAll = false;
            showBulkActions = false;
          "
          class="text-gray-600 hover:text-gray-900"
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

    <!-- Pagination -->
    <div
      v-if="filteredCourses.length > 0"
      class="bg-white px-4 py-3 flex items-center justify-between border border-gray-200 rounded-lg shadow-sm mt-4"
    >
      <div class="flex-1 flex justify-between sm:hidden">
        <button
          @click="setPage(Math.max(1, currentPage - 1))"
          :disabled="currentPage === 1"
          :class="[
            currentPage === 1
              ? 'opacity-50 cursor-not-allowed'
              : 'hover:bg-gray-50',
            'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white',
          ]"
        >
          Previous
        </button>
        <button
          @click="setPage(Math.min(totalPages, currentPage + 1))"
          :disabled="currentPage === totalPages"
          :class="[
            currentPage === totalPages
              ? 'opacity-50 cursor-not-allowed'
              : 'hover:bg-gray-50',
            'ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white',
          ]"
        >
          Next
        </button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{
              (currentPage - 1) * itemsPerPage + 1
            }}</span>
            to
            <span class="font-medium">{{
              Math.min(currentPage * itemsPerPage, filteredCourses.length)
            }}</span>
            of
            <span class="font-medium">{{ filteredCourses.length }}</span>
            results
          </p>
        </div>
        <div>
          <nav
            class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
            aria-label="Pagination"
          >
            <button
              @click="setPage(Math.max(1, currentPage - 1))"
              :disabled="currentPage === 1"
              :class="[
                currentPage === 1
                  ? 'opacity-50 cursor-not-allowed'
                  : 'hover:bg-gray-50',
                'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500',
              ]"
            >
              <span class="sr-only">Previous</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
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
              v-for="page in pageNumbers"
              :key="page"
              @click="setPage(page)"
              :class="getPageClass(page)"
            >
              {{ page }}
            </button>

            <button
              @click="setPage(Math.min(totalPages, currentPage + 1))"
              :disabled="currentPage === totalPages"
              :class="[
                currentPage === totalPages
                  ? 'opacity-50 cursor-not-allowed'
                  : 'hover:bg-gray-50',
                'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500',
              ]"
            >
              <span class="sr-only">Next</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
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

    <!-- Courses table -->
    <div
      class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
    >
      <!-- Loading skeleton -->
      <div v-if="isLoading" class="animate-pulse">
        <div class="h-16 bg-gray-200 border-b border-gray-200"></div>
        <div
          v-for="i in 5"
          :key="`skeleton-${i}`"
          class="p-4 border-b border-gray-200"
        >
          <div class="flex items-center space-x-4">
            <div class="h-5 w-5 bg-gray-200 rounded"></div>
            <div class="h-10 bg-gray-200 rounded w-full"></div>
          </div>
        </div>
      </div>

      <!-- Courses table -->
      <table
        v-else-if="filteredCourses.length > 0"
        class="min-w-full divide-y divide-gray-200"
      >
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12"
            >
              <div class="flex items-center">
                <input
                  type="checkbox"
                  :checked="selectedAll"
                  @change="toggleSelectAll"
                  class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
              </div>
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Course
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Instructor
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
              Price
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Students
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Revenue
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Last Updated
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="course in paginatedCourses"
            :key="course.id"
            class="hover:bg-gray-50"
          >
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  :checked="selectedCourses.includes(course.id)"
                  @change="toggleSelectCourse(course.id)"
                  class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-start">
                <div class="mr-3 flex-shrink-0">
                  <div class="h-10 w-16 bg-gray-200 rounded-sm"></div>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900 line-clamp-1">
                    {{ course.title }}
                  </div>
                  <div class="flex items-center mt-1">
                    <span class="text-xs text-gray-500 mr-2">{{
                      course.category
                    }}</span>
                    <span
                      v-if="course.featured"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800"
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
                  <div v-if="course.rating > 0" class="flex items-center mt-1">
                    <div class="flex text-amber-400">
                      <svg
                        v-for="i in 5"
                        :key="`star-${i}`"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3"
                        :class="
                          i > Math.floor(course.rating) ? 'text-gray-300' : ''
                        "
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </div>
                    <span class="ml-1 text-xs text-gray-600">{{
                      course.rating.toFixed(1)
                    }}</span>
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <img
                  class="h-8 w-8 rounded-full"
                  :src="course.instructor.avatar"
                  :alt="course.instructor.name"
                />
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900">
                    {{ course.instructor.name }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getStatusBadgeClass(course.status)"
              >
                {{
                  course.status.replace("_", " ").charAt(0).toUpperCase() +
                  course.status.replace("_", " ").slice(1)
                }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatPrice(course.price) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatNumber(course.students) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatPrice(course.revenue) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(course.lastUpdated) }}
            </td>
            <td
              class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
            >
              <div class="flex justify-end space-x-2">
                <NuxtLink
                  :to="`/dashboard/admin/courses/${course.id}`"
                  class="text-indigo-600 hover:text-indigo-900"
                  title="View"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                    />
                  </svg>
                </NuxtLink>
                <NuxtLink
                  :to="`/dashboard/admin/courses/${course.id}/edit`"
                  class="text-blue-600 hover:text-blue-900"
                  title="Edit"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                  </svg>
                </NuxtLink>
                <button
                  v-if="course.status !== 'published'"
                  @click="applyBulkAction('publish', [course.id])"
                  class="text-green-600 hover:text-green-900"
                  title="Publish"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                </button>
                <button
                  v-if="!course.featured"
                  @click="applyBulkAction('feature', [course.id])"
                  class="text-amber-600 hover:text-amber-900"
                  title="Feature"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                    />
                  </svg>
                </button>
                <button
                  @click="confirmDelete(course)"
                  class="text-red-600 hover:text-red-900"
                  title="Delete"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
