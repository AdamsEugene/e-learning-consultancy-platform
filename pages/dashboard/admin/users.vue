<!-- pages/dashboard/admin/users/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Set the page layout to dashboard-admin
definePageMeta({
  layout: "dashboard-admin",
});

// Set page metadata
useHead({
  title: "User Management - Admin Dashboard",
  meta: [
    {
      name: "description",
      content: "Manage platform users, permissions, and accounts",
    },
  ],
});

// Types for user data
interface User {
  id: number;
  name: string;
  email: string;
  avatar: string;
  role: "student" | "instructor" | "consultant" | "client" | "admin";
  status: "active" | "pending" | "suspended" | "deactivated";
  dateJoined: string;
  lastLogin: string;
  verified: boolean;
  country?: string;
  company?: string;
  courses?: number;
  projects?: number;
  revenue?: number;
}

// State variables
const users = ref<User[]>([]);
const filteredUsers = ref<User[]>([]);
const isLoading = ref(true);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("recent");
const selectedUser = ref<User | null>(null);
const showUserDetailsModal = ref(false);
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);
const deleteConfirmText = ref("");
const currentPage = ref(1);
const itemsPerPage = ref(10);
const showBulkActionsDropdown = ref(false);
const selectedUsers = ref<number[]>([]);
const selectAll = ref(false);
const createUserModal = ref(false);

// New user form data
const newUser = ref({
  name: "",
  email: "",
  role: "student",
  password: "",
  confirmPassword: "",
  sendWelcomeEmail: true,
});

// Role options for dropdown
const roleOptions = [
  { value: "student", label: "Student" },
  { value: "instructor", label: "Instructor" },
  { value: "consultant", label: "Consultant" },
  { value: "client", label: "Client" },
  { value: "admin", label: "Admin" },
];

// Status filter options
const statusOptions = [
  { value: "all", label: "All Users" },
  { value: "active", label: "Active" },
  { value: "pending", label: "Pending" },
  { value: "suspended", label: "Suspended" },
  { value: "deactivated", label: "Deactivated" },
];

// Mock fetch users data
const fetchUsers = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock user data
    users.value = [
      {
        id: 1,
        name: "Sarah Johnson",
        email: "sarah.johnson@example.com",
        avatar: "/api/placeholder/100/100",
        role: "student",
        status: "active",
        dateJoined: "2024-01-15T10:30:00Z",
        lastLogin: "2025-04-12T09:45:20Z",
        verified: true,
        country: "United States",
        courses: 4,
      },
      {
        id: 2,
        name: "Michael Chen",
        email: "michael.chen@example.com",
        avatar: "/api/placeholder/100/100",
        role: "instructor",
        status: "active",
        dateJoined: "2023-09-22T14:30:00Z",
        lastLogin: "2025-04-15T15:22:10Z",
        verified: true,
        country: "Canada",
        courses: 3,
        revenue: 12500,
      },
      {
        id: 3,
        name: "Emily Garcia",
        email: "emily.garcia@example.com",
        avatar: "/api/placeholder/100/100",
        role: "client",
        status: "active",
        dateJoined: "2024-02-10T09:15:00Z",
        lastLogin: "2025-04-14T11:32:45Z",
        verified: true,
        country: "Spain",
        company: "Global Tech Solutions",
        projects: 2,
      },
      {
        id: 4,
        name: "Dr. Alex Thompson",
        email: "alex.thompson@example.com",
        avatar: "/api/placeholder/100/100",
        role: "consultant",
        status: "active",
        dateJoined: "2023-11-05T16:20:00Z",
        lastLogin: "2025-04-13T14:15:30Z",
        verified: true,
        country: "United Kingdom",
        projects: 5,
        revenue: 28750,
      },
      {
        id: 5,
        name: "Admin User",
        email: "admin@learnconsult.example",
        avatar: "/api/placeholder/100/100",
        role: "admin",
        status: "active",
        dateJoined: "2023-06-01T08:00:00Z",
        lastLogin: "2025-04-16T08:30:15Z",
        verified: true,
        country: "Australia",
      },
      {
        id: 6,
        name: "James Wilson",
        email: "james.wilson@example.com",
        avatar: "/api/placeholder/100/100",
        role: "student",
        status: "pending",
        dateJoined: "2025-04-10T11:45:00Z",
        lastLogin: "2025-04-10T11:50:22Z",
        verified: false,
        country: "United States",
        courses: 0,
      },
      {
        id: 7,
        name: "Sofia Rodriguez",
        email: "sofia.rodriguez@example.com",
        avatar: "/api/placeholder/100/100",
        role: "instructor",
        status: "suspended",
        dateJoined: "2023-10-18T13:25:00Z",
        lastLogin: "2025-03-25T16:40:10Z",
        verified: true,
        country: "Mexico",
        courses: 2,
        revenue: 8200,
      },
      {
        id: 8,
        name: "Robert Kim",
        email: "robert.kim@example.com",
        avatar: "/api/placeholder/100/100",
        role: "client",
        status: "deactivated",
        dateJoined: "2023-08-05T10:20:00Z",
        lastLogin: "2025-01-10T09:15:45Z",
        verified: true,
        country: "South Korea",
        company: "KimTech Innovations",
        projects: 1,
      },
      {
        id: 9,
        name: "Lisa Wang",
        email: "lisa.wang@example.com",
        avatar: "/api/placeholder/100/100",
        role: "consultant",
        status: "active",
        dateJoined: "2024-01-23T15:10:00Z",
        lastLogin: "2025-04-14T10:22:30Z",
        verified: true,
        country: "Singapore",
        projects: 3,
        revenue: 15400,
      },
      {
        id: 10,
        name: "David Martinez",
        email: "david.martinez@example.com",
        avatar: "/api/placeholder/100/100",
        role: "student",
        status: "active",
        dateJoined: "2024-03-08T09:30:00Z",
        lastLogin: "2025-04-15T18:05:12Z",
        verified: true,
        country: "Mexico",
        courses: 6,
      },
      {
        id: 11,
        name: "Emma Wilson",
        email: "emma.wilson@example.com",
        avatar: "/api/placeholder/100/100",
        role: "instructor",
        status: "active",
        dateJoined: "2023-07-15T11:20:00Z",
        lastLogin: "2025-04-12T14:30:45Z",
        verified: true,
        country: "Australia",
        courses: 5,
        revenue: 22800,
      },
      {
        id: 12,
        name: "Mohammed Al-Farsi",
        email: "mohammed.alfarsi@example.com",
        avatar: "/api/placeholder/100/100",
        role: "client",
        status: "active",
        dateJoined: "2024-02-28T13:15:00Z",
        lastLogin: "2025-04-13T11:25:18Z",
        verified: true,
        country: "United Arab Emirates",
        company: "Al-Farsi Enterprises",
        projects: 2,
      },
      {
        id: 13,
        name: "Olivia Taylor",
        email: "olivia.taylor@example.com",
        avatar: "/api/placeholder/100/100",
        role: "student",
        status: "pending",
        dateJoined: "2025-04-12T16:45:00Z",
        lastLogin: "2025-04-12T16:50:30Z",
        verified: false,
        country: "United Kingdom",
        courses: 0,
      },
      {
        id: 14,
        name: "Jamal Washington",
        email: "jamal.washington@example.com",
        avatar: "/api/placeholder/100/100",
        role: "consultant",
        status: "active",
        dateJoined: "2023-11-30T10:40:00Z",
        lastLogin: "2025-04-15T09:18:22Z",
        verified: true,
        country: "United States",
        projects: 4,
        revenue: 19250,
      },
      {
        id: 15,
        name: "Sophia Müller",
        email: "sophia.muller@example.com",
        avatar: "/api/placeholder/100/100",
        role: "student",
        status: "active",
        dateJoined: "2024-01-05T08:30:00Z",
        lastLogin: "2025-04-10T15:45:12Z",
        verified: true,
        country: "Germany",
        courses: 3,
      },
    ];

    applyFilters();
  } catch (error) {
    console.error("Error fetching users:", error);
  } finally {
    isLoading.value = false;
  }
};

// Apply all filters, sorting, and pagination
const applyFilters = () => {
  let result = [...users.value];

  // Filter by role/tab
  if (activeTab.value !== "all") {
    result = result.filter((user) => user.role === activeTab.value);
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (user) =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query) ||
        (user.company && user.company.toLowerCase().includes(query)) ||
        (user.country && user.country.toLowerCase().includes(query))
    );
  }

  // Sort results
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.dateJoined).getTime() - new Date(a.dateJoined).getTime()
    );
  } else if (sortBy.value === "name") {
    result.sort((a, b) => a.name.localeCompare(b.name));
  } else if (sortBy.value === "login") {
    result.sort(
      (a, b) =>
        new Date(b.lastLogin).getTime() - new Date(a.lastLogin).getTime()
    );
  } else if (sortBy.value === "status") {
    result.sort((a, b) => a.status.localeCompare(b.status));
  }

  filteredUsers.value = result;
};

// Watch for changes to filters and search
watch([activeTab, searchQuery, sortBy], () => {
  applyFilters();
  currentPage.value = 1; // Reset to first page when filters change
});

// Computed for pagination
const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredUsers.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / itemsPerPage.value);
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

// Format time ago for last login
const timeAgo = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

  if (diffInSeconds < 60) {
    return "just now";
  } else if (diffInSeconds < 3600) {
    const minutes = Math.floor(diffInSeconds / 60);
    return `${minutes} minute${minutes > 1 ? "s" : ""} ago`;
  } else if (diffInSeconds < 86400) {
    const hours = Math.floor(diffInSeconds / 3600);
    return `${hours} hour${hours > 1 ? "s" : ""} ago`;
  } else if (diffInSeconds < 2592000) {
    const days = Math.floor(diffInSeconds / 86400);
    return `${days} day${days > 1 ? "s" : ""} ago`;
  } else {
    return formatDate(dateString);
  }
};

// Get badge classes for user status
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "active":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "suspended":
      return "bg-red-100 text-red-800";
    case "deactivated":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get role badge classes
const getRoleBadgeClasses = (role: string) => {
  switch (role) {
    case "student":
      return "bg-blue-100 text-blue-800";
    case "instructor":
      return "bg-purple-100 text-purple-800";
    case "consultant":
      return "bg-indigo-100 text-indigo-800";
    case "client":
      return "bg-amber-100 text-amber-800";
    case "admin":
      return "bg-gray-800 text-white";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// View user details
const viewUserDetails = (user: User) => {
  selectedUser.value = user;
  showUserDetailsModal.value = true;
};

// Pagination methods
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Select all users in current page
const toggleSelectAll = () => {
  if (selectAll.value) {
    // Select all users in current page
    selectedUsers.value = paginatedUsers.value.map((user) => user.id);
  } else {
    // Deselect all
    selectedUsers.value = [];
  }
};

// Toggle individual user selection
const toggleUserSelection = (userId: number) => {
  const index = selectedUsers.value.indexOf(userId);
  if (index === -1) {
    selectedUsers.value.push(userId);
  } else {
    selectedUsers.value.splice(index, 1);
  }

  // Update selectAll checkbox state based on if all current page items are selected
  selectAll.value = paginatedUsers.value.every((user) =>
    selectedUsers.value.includes(user.id)
  );
};

// Check if a user is selected
const isUserSelected = (userId: number) => {
  return selectedUsers.value.includes(userId);
};

// Bulk action methods
const applyBulkAction = (action: string) => {
  if (selectedUsers.value.length === 0) return;

  // In a real app, you'd make API calls here
  switch (action) {
    case "activate":
      users.value = users.value.map((user) => {
        if (selectedUsers.value.includes(user.id)) {
          return { ...user, status: "active" };
        }
        return user;
      });
      break;
    case "suspend":
      users.value = users.value.map((user) => {
        if (selectedUsers.value.includes(user.id)) {
          return { ...user, status: "suspended" };
        }
        return user;
      });
      break;
    case "delete":
      users.value = users.value.filter(
        (user) => !selectedUsers.value.includes(user.id)
      );
      break;
  }

  // Reset selection and close dropdown
  selectedUsers.value = [];
  selectAll.value = false;
  showBulkActionsDropdown.value = false;

  // Re-apply filters
  applyFilters();
};

// Confirm delete user
const confirmDeleteUser = (user: User) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
  deleteConfirmText.value = "";
};

// Delete user
const deleteUser = () => {
  if (
    !userToDelete.value ||
    deleteConfirmText.value !== userToDelete.value.email
  ) {
    return;
  }

  // In a real app, this would be an API call
  users.value = users.value.filter(
    (user) => user.id !== userToDelete.value?.id
  );
  applyFilters();

  showDeleteModal.value = false;
  userToDelete.value = null;
  deleteConfirmText.value = "";
};

// Handle form submission for new user
const createNewUser = () => {
  // Validate form
  if (!newUser.value.name || !newUser.value.email || !newUser.value.password) {
    // Show error message in a real app
    return;
  }

  if (newUser.value.password !== newUser.value.confirmPassword) {
    // Show password mismatch error in a real app
    return;
  }

  // In a real app, this would be an API call
  const newUserId = Math.max(...users.value.map((u) => u.id)) + 1;

  users.value.push({
    id: newUserId,
    name: newUser.value.name,
    email: newUser.value.email,
    avatar: "/api/placeholder/100/100", // Default avatar
    role: newUser.value.role as any,
    status: "active",
    dateJoined: new Date().toISOString(),
    lastLogin: new Date().toISOString(),
    verified: true,
    country: "United States", // Default country
  });

  // Reset form and close modal
  newUser.value = {
    name: "",
    email: "",
    role: "student",
    password: "",
    confirmPassword: "",
    sendWelcomeEmail: true,
  };

  createUserModal.value = false;
  applyFilters();
};

// Change user status
const changeUserStatus = (
  user: User,
  newStatus: "active" | "pending" | "suspended" | "deactivated"
) => {
  // In a real app, this would be an API call
  users.value = users.value.map((u) => {
    if (u.id === user.id) {
      return { ...u, status: newStatus };
    }
    return u;
  });

  applyFilters();
};

// Load data when component mounts
onMounted(() => {
  fetchUsers();
});
</script>

<template>
  <div>
    <!-- Page header with summary and actions -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">User Management</h1>
          <p class="text-gray-600">
            Manage platform users, roles, and accounts
          </p>
        </div>
        <div class="mt-4 sm:mt-0">
          <button
            @click="createUserModal = true"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
              />
            </svg>
            <span>Create New User</span>
          </button>
        </div>
      </div>

      <!-- User stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
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
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Users</p>
              <p class="text-2xl font-bold text-gray-900">{{ users.length }}</p>
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
                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                <path
                  d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Students</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ users.filter((u) => u.role === "student").length }}
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
                  d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Instructors</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ users.filter((u) => u.role === "instructor").length }}
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
                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Clients</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ users.filter((u) => u.role === "client").length }}
              </p>
            </div>
          </div>
        </div>

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
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Consultants</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ users.filter((u) => u.role === "consultant").length }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Tab navigation -->
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
            All Users
          </button>
          <button
            @click="activeTab = 'student'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'student'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Students
          </button>
          <button
            @click="activeTab = 'instructor'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'instructor'
                ? 'bg-purple-100 text-purple-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Instructors
          </button>
          <button
            @click="activeTab = 'consultant'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'consultant'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Consultants
          </button>
          <button
            @click="activeTab = 'client'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'client'
                ? 'bg-amber-100 text-amber-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Clients
          </button>
          <button
            @click="activeTab = 'admin'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'admin'
                ? 'bg-gray-800 text-white'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Admins
          </button>
        </div>

        <!-- Search and sort controls -->
        <div class="flex items-center space-x-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search users..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="recent">Recently Joined</option>
              <option value="name">Name (A-Z)</option>
              <option value="login">Last Login</option>
              <option value="status">Status</option>
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

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="p-4 border-b border-gray-200">
        <div class="flex justify-between">
          <div class="h-6 bg-gray-200 rounded w-40"></div>
          <div class="h-6 bg-gray-200 rounded w-32"></div>
        </div>
      </div>
      <div
        v-for="i in 5"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="bg-gray-200 h-10 w-10 rounded-full"></div>
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-1/3 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Users table -->
    <div
      v-else-if="filteredUsers.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
    >
      <!-- Table header with bulk actions -->
      <div
        class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center"
      >
        <div class="flex items-center">
          <input
            type="checkbox"
            id="select-all"
            v-model="selectAll"
            @change="toggleSelectAll"
            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
          <label for="select-all" class="ml-2 text-sm font-medium text-gray-700"
            >Select All</label
          >

          <!-- Bulk actions dropdown -->
          <div class="relative ml-3" v-if="selectedUsers.length > 0">
            <button
              @click="showBulkActionsDropdown = !showBulkActionsDropdown"
              class="flex items-center text-sm text-gray-700 bg-white border border-gray-300 rounded-md px-3 py-1.5 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <span>Bulk Actions ({{ selectedUsers.length }})</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="ml-1 h-5 w-5 text-gray-400"
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
              v-if="showBulkActionsDropdown"
              class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10"
            >
              <div class="py-1">
                <button
                  @click="applyBulkAction('activate')"
                  class="text-gray-700 group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mr-3 h-5 w-5 text-green-500"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Activate Users
                </button>
                <button
                  @click="applyBulkAction('suspend')"
                  class="text-gray-700 group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mr-3 h-5 w-5 text-yellow-500"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Suspend Users
                </button>
              </div>
              <div class="py-1">
                <button
                  @click="applyBulkAction('delete')"
                  class="text-red-700 group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-red-50"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mr-3 h-5 w-5 text-red-500"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Delete Selected
                </button>
              </div>
            </div>
          </div>
        </div>

        <span class="text-sm text-gray-700">
          Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
          {{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} of
          {{ filteredUsers.length }} users
        </span>
      </div>

      <!-- Table content -->
      <div class="divide-y divide-gray-200">
        <div
          v-for="user in paginatedUsers"
          :key="`user-${user.id}`"
          class="p-4 hover:bg-gray-50 transition-colors"
        >
          <div class="sm:flex sm:items-center sm:justify-between">
            <!-- User info -->
            <div class="flex items-center space-x-4 flex-1">
              <input
                type="checkbox"
                :id="`select-user-${user.id}`"
                :checked="isUserSelected(user.id)"
                @change="toggleUserSelection(user.id)"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
              <img
                :src="user.avatar"
                :alt="user.name"
                class="h-10 w-10 rounded-full object-cover"
              />

              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                  {{ user.name }}
                </p>
                <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
                <div class="flex items-center mt-1">
                  <span
                    class="px-2 py-0.5 rounded-full text-xs font-medium mr-2"
                    :class="getRoleBadgeClasses(user.role)"
                  >
                    {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                  </span>
                  <span
                    class="px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusBadgeClasses(user.status)"
                  >
                    {{
                      user.status.charAt(0).toUpperCase() + user.status.slice(1)
                    }}
                  </span>
                </div>
              </div>
            </div>

            <!-- User meta data -->
            <div
              class="mt-4 sm:mt-0 flex items-center sm:space-x-6 text-sm text-gray-500"
            >
              <div class="hidden lg:block">
                <div class="font-medium">Joined</div>
                <div>{{ formatDate(user.dateJoined) }}</div>
              </div>

              <div class="hidden lg:block">
                <div class="font-medium">Last login</div>
                <div>{{ timeAgo(user.lastLogin) }}</div>
              </div>

              <!-- Action buttons -->
              <div class="flex space-x-2 sm:ml-4">
                <button
                  @click="viewUserDetails(user)"
                  class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                  title="View details"
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
                  :to="`/dashboard/admin/users/${user.id}/edit`"
                  class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                  title="Edit user"
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

                <div class="relative" v-if="user.role !== 'admin'">
                  <div class="dropdown">
                    <button
                      class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                      title="Change status"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                    <div
                      class="dropdown-menu hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10"
                    >
                      <div class="py-1">
                        <button
                          @click="changeUserStatus(user, 'active')"
                          class="text-gray-700 group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                          :disabled="user.status === 'active'"
                          :class="{
                            'opacity-50 cursor-not-allowed':
                              user.status === 'active',
                          }"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-3 h-5 w-5 text-green-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Activate
                        </button>
                        <button
                          @click="changeUserStatus(user, 'suspended')"
                          class="text-gray-700 group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                          :disabled="user.status === 'suspended'"
                          :class="{
                            'opacity-50 cursor-not-allowed':
                              user.status === 'suspended',
                          }"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-3 h-5 w-5 text-yellow-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Suspend
                        </button>
                        <button
                          @click="changeUserStatus(user, 'deactivated')"
                          class="text-gray-700 group flex items-center px-4 py-2 text-sm w-full text-left hover:bg-gray-100"
                          :disabled="user.status === 'deactivated'"
                          :class="{
                            'opacity-50 cursor-not-allowed':
                              user.status === 'deactivated',
                          }"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-3 h-5 w-5 text-gray-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Deactivate
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <button
                  v-if="user.role !== 'admin'"
                  @click="confirmDeleteUser(user)"
                  class="p-2 rounded-lg text-gray-500 hover:bg-red-100 hover:text-red-700 transition-colors"
                  title="Delete user"
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
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              :class="[
                'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md',
                currentPage === 1
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                  : 'bg-white text-gray-700 hover:bg-gray-50',
              ]"
            >
              Previous
            </button>
            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              :class="[
                'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md',
                currentPage === totalPages
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                  : 'bg-white text-gray-700 hover:bg-gray-50',
              ]"
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
                  (currentPage - 1) * itemsPerPage + 1
                }}</span>
                to
                <span class="font-medium">{{
                  Math.min(currentPage * itemsPerPage, filteredUsers.length)
                }}</span>
                of
                <span class="font-medium">{{ filteredUsers.length }}</span>
                results
              </p>
            </div>
            <div v-if="totalPages > 1">
              <nav
                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                aria-label="Pagination"
              >
                <button
                  @click="goToPage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  :class="[
                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium',
                    currentPage === 1
                      ? 'text-gray-300 cursor-not-allowed'
                      : 'text-gray-500 hover:bg-gray-50',
                  ]"
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

                <!-- Page numbers -->
                <template v-for="page in totalPages" :key="`page-${page}`">
                  <button
                    v-if="
                      page === 1 ||
                      page === totalPages ||
                      (page >= currentPage - 1 && page <= currentPage + 1)
                    "
                    @click="goToPage(page)"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      currentPage === page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    ]"
                  >
                    {{ page }}
                  </button>

                  <!-- Ellipsis -->
                  <span
                    v-else-if="
                      (page === 2 && currentPage > 3) ||
                      (page === totalPages - 1 && currentPage < totalPages - 2)
                    "
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                  >
                    ...
                  </span>
                </template>

                <button
                  @click="goToPage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  :class="[
                    'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium',
                    currentPage === totalPages
                      ? 'text-gray-300 cursor-not-allowed'
                      : 'text-gray-500 hover:bg-gray-50',
                  ]"
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
    </div>

    <!-- Empty state -->
    <div
      v-else
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
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No users match your search criteria."
            : "There are no users in the system yet."
        }}
      </p>
      <button
        @click="createUserModal = true"
        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
          />
        </svg>
        <span>Create New User</span>
      </button>
    </div>

    <!-- User details modal -->
    <div
      v-if="showUserDetailsModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-3xl w-full p-6 shadow-xl">
        <div class="flex justify-between items-start mb-6">
          <h3 class="text-xl font-semibold text-gray-900">User Details</h3>
          <button
            @click="showUserDetailsModal = false"
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
          >
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
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

        <div v-if="selectedUser" class="space-y-6">
          <!-- User summary -->
          <div class="flex items-start space-x-4">
            <img
              :src="selectedUser.avatar"
              :alt="selectedUser.name"
              class="h-16 w-16 rounded-full object-cover"
            />
            <div>
              <h4 class="text-lg font-medium text-gray-900">
                {{ selectedUser.name }}
              </h4>
              <p class="text-gray-500">{{ selectedUser.email }}</p>
              <div class="flex items-center space-x-2 mt-2">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getRoleBadgeClasses(selectedUser.role)"
                >
                  {{
                    selectedUser.role.charAt(0).toUpperCase() +
                    selectedUser.role.slice(1)
                  }}
                </span>
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="getStatusBadgeClasses(selectedUser.status)"
                >
                  {{
                    selectedUser.status.charAt(0).toUpperCase() +
                    selectedUser.status.slice(1)
                  }}
                </span>
              </div>
            </div>
          </div>

          <!-- Details grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h5 class="font-medium text-gray-700 mb-2">
                Account Information
              </h5>
              <dl class="space-y-2">
                <div class="flex justify-between">
                  <dt class="text-gray-500">Account ID:</dt>
                  <dd class="text-gray-900 font-medium">
                    {{ selectedUser.id }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Date Joined:</dt>
                  <dd class="text-gray-900 font-medium">
                    {{ formatDate(selectedUser.dateJoined) }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Last Login:</dt>
                  <dd class="text-gray-900 font-medium">
                    {{ timeAgo(selectedUser.lastLogin) }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Email Verified:</dt>
                  <dd class="text-gray-900 font-medium">
                    <span
                      v-if="selectedUser.verified"
                      class="text-green-500 flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Verified
                    </span>
                    <span v-else class="text-red-500 flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Not Verified
                    </span>
                  </dd>
                </div>
                <div v-if="selectedUser.country" class="flex justify-between">
                  <dt class="text-gray-500">Country:</dt>
                  <dd class="text-gray-900 font-medium">
                    {{ selectedUser.country }}
                  </dd>
                </div>
                <div v-if="selectedUser.company" class="flex justify-between">
                  <dt class="text-gray-500">Company:</dt>
                  <dd class="text-gray-900 font-medium">
                    {{ selectedUser.company }}
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Role-specific information -->
            <div
              v-if="selectedUser.role === 'student'"
              class="bg-blue-50 p-4 rounded-lg"
            >
              <h5 class="font-medium text-blue-800 mb-2">
                Student Information
              </h5>
              <dl class="space-y-2">
                <div class="flex justify-between">
                  <dt class="text-blue-700">Enrolled Courses:</dt>
                  <dd class="text-blue-900 font-medium">
                    {{ selectedUser.courses || 0 }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-blue-700">Progress:</dt>
                  <dd class="text-blue-900 font-medium">
                    {{ selectedUser.courses ? "78%" : "No courses" }}
                  </dd>
                </div>
              </dl>
            </div>

            <div
              v-else-if="selectedUser.role === 'instructor'"
              class="bg-purple-50 p-4 rounded-lg"
            >
              <h5 class="font-medium text-purple-800 mb-2">
                Instructor Information
              </h5>
              <dl class="space-y-2">
                <div class="flex justify-between">
                  <dt class="text-purple-700">Active Courses:</dt>
                  <dd class="text-purple-900 font-medium">
                    {{ selectedUser.courses || 0 }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-purple-700">Total Students:</dt>
                  <dd class="text-purple-900 font-medium">
                    {{
                      selectedUser.courses
                        ? Math.floor(Math.random() * 1000)
                        : 0
                    }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-purple-700">Total Revenue:</dt>
                  <dd class="text-purple-900 font-medium">
                    ${{ selectedUser.revenue?.toLocaleString() || 0 }}
                  </dd>
                </div>
              </dl>
            </div>

            <div
              v-else-if="selectedUser.role === 'consultant'"
              class="bg-indigo-50 p-4 rounded-lg"
            >
              <h5 class="font-medium text-indigo-800 mb-2">
                Consultant Information
              </h5>
              <dl class="space-y-2">
                <div class="flex justify-between">
                  <dt class="text-indigo-700">Active Projects:</dt>
                  <dd class="text-indigo-900 font-medium">
                    {{ selectedUser.projects || 0 }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-indigo-700">Client Count:</dt>
                  <dd class="text-indigo-900 font-medium">
                    {{
                      selectedUser.projects ? Math.floor(Math.random() * 20) : 0
                    }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-indigo-700">Total Revenue:</dt>
                  <dd class="text-indigo-900 font-medium">
                    ${{ selectedUser.revenue?.toLocaleString() || 0 }}
                  </dd>
                </div>
              </dl>
            </div>

            <div
              v-else-if="selectedUser.role === 'client'"
              class="bg-amber-50 p-4 rounded-lg"
            >
              <h5 class="font-medium text-amber-800 mb-2">
                Client Information
              </h5>
              <dl class="space-y-2">
                <div class="flex justify-between">
                  <dt class="text-amber-700">Active Projects:</dt>
                  <dd class="text-amber-900 font-medium">
                    {{ selectedUser.projects || 0 }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-amber-700">Total Spend:</dt>
                  <dd class="text-amber-900 font-medium">
                    ${{
                      selectedUser.projects
                        ? (
                            Math.floor(Math.random() * 10) * 1000
                          ).toLocaleString()
                        : 0
                    }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="flex justify-end space-x-3 border-t pt-6">
            <button
              v-if="selectedUser.role !== 'admin'"
              @click="
                confirmDeleteUser(selectedUser);
                showUserDetailsModal = false;
              "
              class="px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
              Delete User
            </button>
            <NuxtLink
              :to="`/dashboard/admin/users/${selectedUser.id}/edit`"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
              Edit
            </NuxtLink>
            <button
              @click="showUserDetailsModal = false"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create user modal -->
    <div
      v-if="createUserModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="flex justify-between items-start mb-6">
          <h3 class="text-xl font-semibold text-gray-900">Create New User</h3>
          <button
            @click="createUserModal = false"
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
          >
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
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

        <form @submit.prevent="createNewUser" class="space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700"
              >Full Name</label
            >
            <input
              id="name"
              v-model="newUser.name"
              type="text"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700"
              >Email Address</label
            >
            <input
              id="email"
              v-model="newUser.email"
              type="email"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div>
            <label for="role" class="block text-sm font-medium text-gray-700"
              >User Role</label
            >
            <select
              id="role"
              v-model="newUser.role"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option
                v-for="option in roleOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>

          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700"
              >Password</label
            >
            <input
              id="password"
              v-model="newUser.password"
              type="password"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div>
            <label
              for="confirm-password"
              class="block text-sm font-medium text-gray-700"
              >Confirm Password</label
            >
            <input
              id="confirm-password"
              v-model="newUser.confirmPassword"
              type="password"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div class="flex items-start mt-4">
            <div class="flex items-center h-5">
              <input
                id="send-welcome"
                v-model="newUser.sendWelcomeEmail"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
            </div>
            <div class="ml-3 text-sm">
              <label for="send-welcome" class="font-medium text-gray-700"
                >Send welcome email</label
              >
              <p class="text-gray-500">
                The user will receive onboarding instructions.
              </p>
            </div>
          </div>

          <div class="flex justify-end space-x-3 border-t pt-6 mt-6">
            <button
              type="button"
              @click="createUserModal = false"
              class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              Create User
            </button>
          </div>
        </form>
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
            Delete User
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete "{{ userToDelete?.name }}"? This
            action cannot be undone.
          </p>
        </div>

        <!-- Confirmation input -->
        <div class="mb-4">
          <label
            for="confirm-delete"
            class="block text-sm font-medium text-gray-700 mb-2"
          >
            Please type
            <span class="font-semibold">{{ userToDelete?.email }}</span> to
            confirm:
          </label>
          <input
            id="confirm-delete"
            v-model="deleteConfirmText"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="Type email here"
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
            @click="deleteUser"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="deleteConfirmText !== userToDelete?.email"
            :class="{
              'opacity-50 cursor-not-allowed':
                deleteConfirmText !== userToDelete?.email,
            }"
          >
            Delete User
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

/* Dropdown menu behavior */
.dropdown:hover .dropdown-menu {
  display: block;
}
</style>
