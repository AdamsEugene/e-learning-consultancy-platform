<!-- pages/dashboard/admin/notifications/index.vue -->
<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";

// Define page meta
definePageMeta({
  layout: "dashboard-admin",
});

// Page title and meta
useHead({
  title: "Notifications - Admin Dashboard",
  meta: [
    { name: "description", content: "Manage system notifications and alerts" },
  ],
});

// Interface for notification objects
interface Notification {
  id: string;
  type: "system" | "user" | "course" | "project" | "payment" | "security";
  title: string;
  message: string;
  timestamp: string;
  isRead: boolean;
  priority: "low" | "medium" | "high" | "urgent";
  actionRequired: boolean;
  actionUrl?: string;
  actionText?: string;
  sender?: {
    id: string;
    name: string;
    avatar: string;
    role: string;
  };
  relatedEntityId?: string;
  relatedEntityType?: string;
}

// Notification state
const notifications = ref<Notification[]>([]);
const isLoading = ref(true);
const selectedNotification = ref<Notification | null>(null);
const showNotificationDetails = ref(false);
const activeFilter = ref("all");
const searchQuery = ref("");
const sortBy = ref("newest");

// Pagination
const currentPage = ref(1);
const perPage = ref(10);
const totalNotifications = ref(0);

// Batch operations
const selectedNotificationIds = ref<string[]>([]);
const selectAll = ref(false);
const showBulkActions = computed(
  () => selectedNotificationIds.value.length > 0
);

// Filter options
const filterOptions = [
  { id: "all", label: "All Notifications" },
  { id: "unread", label: "Unread" },
  { id: "actionRequired", label: "Action Required" },
  { id: "system", label: "System" },
  { id: "user", label: "User" },
  { id: "course", label: "Courses" },
  { id: "project", label: "Projects" },
  { id: "payment", label: "Payments" },
  { id: "security", label: "Security" },
];

// Sort options
const sortOptions = [
  { id: "newest", label: "Newest First" },
  { id: "oldest", label: "Oldest First" },
  { id: "priority", label: "Priority" },
];

// Advanced settings
const preferences = ref({
  autoRefresh: true,
  refreshInterval: 5, // minutes
  emailNotifications: true,
  desktopNotifications: true,
  mobileNotifications: true,
  batchNotifications: true,
  batchInterval: 15, // minutes
  notificationHistory: 30, // days
});

// Toast notification state
const toast = ref({
  show: false,
  message: "",
  type: "success", // 'success', 'error', 'warning', 'info'
});

// Show toast message
const showToast = (message: string, type: string = "success") => {
  toast.value = {
    show: true,
    message,
    type,
  };

  // Hide toast after 3 seconds
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
};

// Format date for display
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  }).format(date);
};

// Relative time (e.g. "2 hours ago")
const getRelativeTime = (dateString: string) => {
  const now = new Date();
  const date = new Date(dateString);
  const diffMs = now.getTime() - date.getTime();

  const diffSecs = Math.floor(diffMs / 1000);
  const diffMins = Math.floor(diffSecs / 60);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffSecs < 60) {
    return "just now";
  } else if (diffMins < 60) {
    return `${diffMins} minute${diffMins > 1 ? "s" : ""} ago`;
  } else if (diffHours < 24) {
    return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
  } else if (diffDays < 7) {
    return `${diffDays} day${diffDays > 1 ? "s" : ""} ago`;
  } else {
    return formatDate(dateString);
  }
};

// Get priority class for styling
const getPriorityClass = (priority: string) => {
  switch (priority) {
    case "low":
      return "bg-blue-100 text-blue-800";
    case "medium":
      return "bg-yellow-100 text-yellow-800";
    case "high":
      return "bg-orange-100 text-orange-800";
    case "urgent":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get type icon
const getTypeIcon = (type: string) => {
  switch (type) {
    case "system":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>`;
    case "user":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
      </svg>`;
    case "course":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
      </svg>`;
    case "project":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "payment":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "security":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Toggle select all notifications
watch(selectAll, (newValue) => {
  if (newValue) {
    selectedNotificationIds.value = filteredNotifications.value.map(
      (n) => n.id
    );
  } else {
    selectedNotificationIds.value = [];
  }
});

// Watch for filter changes
watch([activeFilter, searchQuery, sortBy], () => {
  // Reset pagination when filters change
  currentPage.value = 1;
});

// Computed filtered notifications
const filteredNotifications = computed(() => {
  let result = [...notifications.value];

  // Apply filter
  if (activeFilter.value === "unread") {
    result = result.filter((notification) => !notification.isRead);
  } else if (activeFilter.value === "actionRequired") {
    result = result.filter((notification) => notification.actionRequired);
  } else if (activeFilter.value !== "all") {
    result = result.filter(
      (notification) => notification.type === activeFilter.value
    );
  }

  // Apply search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (notification) =>
        notification.title.toLowerCase().includes(query) ||
        notification.message.toLowerCase().includes(query)
    );
  }

  // Apply sorting
  if (sortBy.value === "newest") {
    result.sort(
      (a, b) =>
        new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
    );
  } else if (sortBy.value === "oldest") {
    result.sort(
      (a, b) =>
        new Date(a.timestamp).getTime() - new Date(b.timestamp).getTime()
    );
  } else if (sortBy.value === "priority") {
    const priorityOrder = { urgent: 0, high: 1, medium: 2, low: 3 };
    result.sort(
      (a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]
    );
  }

  return result;
});

// Computed pagination
const paginatedNotifications = computed(() => {
  const startIndex = (currentPage.value - 1) * perPage.value;
  const endIndex = startIndex + perPage.value;
  return filteredNotifications.value.slice(startIndex, endIndex);
});

const totalPages = computed(() =>
  Math.ceil(filteredNotifications.value.length / perPage.value)
);

// Computed unread count
const unreadCount = computed(
  () => notifications.value.filter((n) => !n.isRead).length
);

// Computed action required count
const actionRequiredCount = computed(
  () => notifications.value.filter((n) => n.actionRequired).length
);

// Fetching notifications (mock data for now)
const fetchNotifications = async () => {
  isLoading.value = true;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mock data
    notifications.value = [
      {
        id: "1",
        type: "security",
        title: "Multiple Failed Login Attempts",
        message:
          "Multiple failed login attempts detected for admin account john.doe@example.com from IP 192.168.1.100. Account has been temporarily locked.",
        timestamp: new Date(new Date().getTime() - 15 * 60000).toISOString(),
        isRead: false,
        priority: "high",
        actionRequired: true,
        actionUrl: "/dashboard/admin/users/accounts/456",
        actionText: "Review Activity",
        relatedEntityId: "456",
        relatedEntityType: "user",
      },
      {
        id: "2",
        type: "course",
        title: "New Course Pending Approval",
        message:
          'A new course "Advanced Machine Learning" has been submitted for approval by instructor Emily Johnson.',
        timestamp: new Date(new Date().getTime() - 3 * 3600000).toISOString(),
        isRead: false,
        priority: "medium",
        actionRequired: true,
        actionUrl: "/dashboard/admin/courses/review/123",
        actionText: "Review Course",
        sender: {
          id: "789",
          name: "Emily Johnson",
          avatar: "/api/placeholder/100/100",
          role: "instructor",
        },
        relatedEntityId: "123",
        relatedEntityType: "course",
      },
      {
        id: "3",
        type: "payment",
        title: "Payout Processing Failed",
        message:
          "Monthly instructor payout batch processing failed. 23 payouts affected. Technical error: API timeout from payment gateway.",
        timestamp: new Date(new Date().getTime() - 8 * 3600000).toISOString(),
        isRead: false,
        priority: "urgent",
        actionRequired: true,
        actionUrl: "/dashboard/admin/financials/payouts/batch/52",
        actionText: "Resolve Issue",
      },
      {
        id: "4",
        type: "system",
        title: "System Update Complete",
        message:
          "Platform update to version 2.5.0 completed successfully. New features: enhanced reporting, course analytics dashboard, and instructor payout automation.",
        timestamp: new Date(new Date().getTime() - 24 * 3600000).toISOString(),
        isRead: true,
        priority: "low",
        actionRequired: false,
      },
      {
        id: "5",
        type: "user",
        title: "New Consultant Application",
        message:
          "New consultant application received from Michael Roberts. Experience: 8 years in software development and architecture.",
        timestamp: new Date(new Date().getTime() - 36 * 3600000).toISOString(),
        isRead: true,
        priority: "medium",
        actionRequired: true,
        actionUrl: "/dashboard/admin/users/applications/234",
        actionText: "Review Application",
        sender: {
          id: "234",
          name: "Michael Roberts",
          avatar: "/api/placeholder/100/100",
          role: "client",
        },
        relatedEntityId: "234",
        relatedEntityType: "user",
      },
      {
        id: "6",
        type: "project",
        title: "Project Status Update",
        message:
          'Project "E-commerce Redesign" has been marked as completed by consultant Sarah Parker. Client review pending.',
        timestamp: new Date(new Date().getTime() - 48 * 3600000).toISOString(),
        isRead: true,
        priority: "low",
        actionRequired: false,
        relatedEntityId: "345",
        relatedEntityType: "project",
      },
      {
        id: "7",
        type: "security",
        title: "Database Backup Failed",
        message:
          "Scheduled database backup failed at 02:00 AM. Error: Insufficient storage space on backup server.",
        timestamp: new Date(new Date().getTime() - 52 * 3600000).toISOString(),
        isRead: false,
        priority: "urgent",
        actionRequired: true,
        actionUrl: "/dashboard/admin/system/backups",
        actionText: "Fix Backup",
      },
      {
        id: "8",
        type: "user",
        title: "User Reported Issue",
        message:
          'Student James Wilson reported an issue with course video playback on "Web Development Fundamentals" course.',
        timestamp: new Date(new Date().getTime() - 72 * 3600000).toISOString(),
        isRead: true,
        priority: "medium",
        actionRequired: false,
        sender: {
          id: "567",
          name: "James Wilson",
          avatar: "/api/placeholder/100/100",
          role: "student",
        },
        relatedEntityId: "789",
        relatedEntityType: "course",
      },
      {
        id: "9",
        type: "payment",
        title: "Large Transaction Alert",
        message:
          "Large transaction detected: $5,000 payment for enterprise subscription from TechSoft Inc. Transaction ID: TX-98765.",
        timestamp: new Date(
          new Date().getTime() - 4 * 24 * 3600000
        ).toISOString(),
        isRead: true,
        priority: "low",
        actionRequired: false,
        relatedEntityId: "678",
        relatedEntityType: "transaction",
      },
      {
        id: "10",
        type: "system",
        title: "API Rate Limit Warning",
        message:
          "External API integration with Zoom is approaching rate limit threshold. Current usage: 85% of allocated limit.",
        timestamp: new Date(
          new Date().getTime() - 5 * 24 * 3600000
        ).toISOString(),
        isRead: false,
        priority: "medium",
        actionRequired: true,
        actionUrl: "/dashboard/admin/integrations/zoom",
        actionText: "Review API Usage",
      },
      {
        id: "11",
        type: "course",
        title: "Course Flagged for Review",
        message:
          'Course "Financial Planning Basics" has been flagged for review due to multiple user reports about outdated content.',
        timestamp: new Date(
          new Date().getTime() - 6 * 24 * 3600000
        ).toISOString(),
        isRead: true,
        priority: "medium",
        actionRequired: true,
        actionUrl: "/dashboard/admin/courses/567/review",
        actionText: "Review Course",
        relatedEntityId: "567",
        relatedEntityType: "course",
      },
      {
        id: "12",
        type: "system",
        title: "Storage Space Alert",
        message:
          "Platform storage usage has reached 85% of allocated capacity. Consider upgrading storage or cleaning up unused files.",
        timestamp: new Date(
          new Date().getTime() - 7 * 24 * 3600000
        ).toISOString(),
        isRead: true,
        priority: "medium",
        actionRequired: false,
        actionUrl: "/dashboard/admin/system/storage",
        actionText: "Manage Storage",
      },
    ];

    totalNotifications.value = notifications.value.length;
  } catch (error) {
    console.error("Error fetching notifications:", error);
    showToast("Failed to load notifications", "error");
  } finally {
    isLoading.value = false;
  }
};

// Mark notification as read
const markAsRead = async (notificationId: string) => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 500));

    const notification = notifications.value.find(
      (n) => n.id === notificationId
    );
    if (notification) {
      notification.isRead = true;
    }

    showToast("Notification marked as read");
  } catch (error) {
    console.error("Error marking notification as read:", error);
    showToast("Failed to update notification", "error");
  }
};

// Mark all as read
const markAllAsRead = async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    notifications.value.forEach((notification) => {
      notification.isRead = true;
    });

    showToast("All notifications marked as read");
  } catch (error) {
    console.error("Error marking all notifications as read:", error);
    showToast("Failed to update notifications", "error");
  }
};

// Mark selected as read
const markSelectedAsRead = async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    notifications.value.forEach((notification) => {
      if (selectedNotificationIds.value.includes(notification.id)) {
        notification.isRead = true;
      }
    });

    showToast(
      `${selectedNotificationIds.value.length} notifications marked as read`
    );
    selectedNotificationIds.value = [];
    selectAll.value = false;
  } catch (error) {
    console.error("Error marking selected notifications as read:", error);
    showToast("Failed to update notifications", "error");
  }
};

// Delete notification
const deleteNotification = async (notificationId: string) => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 500));

    notifications.value = notifications.value.filter(
      (n) => n.id !== notificationId
    );
    showNotificationDetails.value = false;

    showToast("Notification deleted");
  } catch (error) {
    console.error("Error deleting notification:", error);
    showToast("Failed to delete notification", "error");
  }
};

// Delete selected notifications
const deleteSelectedNotifications = async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    notifications.value = notifications.value.filter(
      (notification) => !selectedNotificationIds.value.includes(notification.id)
    );

    showToast(`${selectedNotificationIds.value.length} notifications deleted`);
    selectedNotificationIds.value = [];
    selectAll.value = false;
  } catch (error) {
    console.error("Error deleting selected notifications:", error);
    showToast("Failed to delete notifications", "error");
  }
};

// View notification details
const viewNotificationDetails = (notification: Notification) => {
  selectedNotification.value = notification;
  showNotificationDetails.value = true;

  // Mark as read if unread
  if (!notification.isRead) {
    markAsRead(notification.id);
  }
};

// Close notification details modal
const closeNotificationDetails = () => {
  showNotificationDetails.value = false;
  selectedNotification.value = null;
};

// Toggle notification selection
const toggleNotificationSelection = (notificationId: string) => {
  const index = selectedNotificationIds.value.indexOf(notificationId);
  if (index === -1) {
    selectedNotificationIds.value.push(notificationId);
  } else {
    selectedNotificationIds.value.splice(index, 1);
  }
};

// Test notification settings
const testNotificationSettings = async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    showToast("Test notification sent successfully");
  } catch (error) {
    console.error("Error testing notification settings:", error);
    showToast("Failed to send test notification", "error");
  }
};

// Save notification preferences
const saveNotificationPreferences = async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    showToast("Notification preferences saved");
  } catch (error) {
    console.error("Error saving notification preferences:", error);
    showToast("Failed to save preferences", "error");
  }
};

// Fetch data on mount
onMounted(() => {
  fetchNotifications();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage system notifications and alerts
          </p>
        </div>

        <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
          <button
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="unreadCount === 0"
            @click="markAllAsRead"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1.5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            Mark All Read
          </button>

          <button
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="fetchNotifications"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1.5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            Refresh
          </button>
        </div>
      </div>

      <!-- Stats Overview Cards -->
      <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-indigo-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total Notifications
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ totalNotifications }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-blue-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                    clip-rule="evenodd"
                  />
                  <path
                    d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Unread
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ unreadCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-amber-100 rounded-md p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-amber-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Action Required
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ actionRequiredCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-green-600"
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
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Read
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ totalNotifications - unreadCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Action Bar -->
    <div
      v-if="showBulkActions"
      class="w-full bg-indigo-50 border border-indigo-200 p-3 rounded-lg mb-4 flex items-center justify-between animate-fade-in"
    >
      <div class="text-indigo-700 text-sm font-medium">
        {{ selectedNotificationIds.length }}
        {{
          selectedNotificationIds.length === 1
            ? "notification"
            : "notifications"
        }}
        selected
      </div>
      <div class="flex space-x-3">
        <button
          class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          @click="markSelectedAsRead"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          Mark as Read
        </button>
        <button
          class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          @click="deleteSelectedNotifications"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          Delete
        </button>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-4 sm:px-6 py-4">
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-4">
          <!-- View Options -->
          <div class="flex overflow-x-auto py-1 no-scrollbar">
            <div class="flex space-x-2">
              <button
                v-for="filter in filterOptions"
                :key="filter.id"
                class="px-3 py-1.5 text-sm font-medium rounded-md whitespace-nowrap transition-colors duration-200"
                :class="
                  activeFilter === filter.id
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                "
                @click="activeFilter = filter.id"
              >
                {{ filter.label }}

                <!-- Badge for unread count -->
                <span
                  v-if="filter.id === 'unread' && unreadCount > 0"
                  class="ml-1.5 px-1.5 py-0.5 text-xs font-medium rounded-full bg-indigo-600 text-white"
                >
                  {{ unreadCount }}
                </span>

                <!-- Badge for action required count -->
                <span
                  v-if="
                    filter.id === 'actionRequired' && actionRequiredCount > 0
                  "
                  class="ml-1.5 px-1.5 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-800"
                >
                  {{ actionRequiredCount }}
                </span>
              </button>
            </div>
          </div>

          <div class="flex-1" />

          <!-- Search -->
          <div class="relative flex-grow max-w-md">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search notifications..."
              class="block w-full border-gray-300 rounded-md shadow-sm pl-10 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
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

          <!-- Sort -->
          <div class="relative">
            <select
              v-model="sortBy"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option
                v-for="option in sortOptions"
                :key="option.id"
                :value="option.id"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Loading state -->
      <div v-if="isLoading" class="p-20 flex justify-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
        />
      </div>

      <!-- Empty state -->
      <div
        v-else-if="filteredNotifications.length === 0"
        class="p-12 text-center"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-14 w-14 mx-auto text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
          />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">
          No notifications found
        </h3>
        <p class="mt-1 text-gray-500">
          {{
            searchQuery
              ? "Try adjusting your search filters."
              : "You have no notifications at this time."
          }}
        </p>
      </div>

      <!-- Notifications table -->
      <div v-else>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-4 py-3 w-8">
                  <div class="flex items-center">
                    <input
                      id="select-all"
                      v-model="selectAll"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                </th>
                <th
                  scope="col"
                  class="w-16 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Type
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Notification
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Priority
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Time
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="notification in paginatedNotifications"
                :key="notification.id"
                :class="notification.isRead ? '' : 'bg-indigo-50'"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <input
                      :id="`select-${notification.id}`"
                      v-model="selectedNotificationIds"
                      :value="notification.id"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      @change="
                        () => toggleNotificationSelection(notification.id)
                      "
                    />
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div
                    class="p-2 rounded-lg"
                    :class="{
                      'bg-purple-100 text-purple-700':
                        notification.type === 'system',
                      'bg-blue-100 text-blue-700': notification.type === 'user',
                      'bg-green-100 text-green-700':
                        notification.type === 'course',
                      'bg-amber-100 text-amber-700':
                        notification.type === 'project',
                      'bg-emerald-100 text-emerald-700':
                        notification.type === 'payment',
                      'bg-red-100 text-red-700':
                        notification.type === 'security',
                    }"
                  >
                    <span v-html="getTypeIcon(notification.type)"></span>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <div
                    class="cursor-pointer"
                    @click="viewNotificationDetails(notification)"
                  >
                    <p
                      class="font-medium mb-1"
                      :class="
                        notification.isRead ? 'text-gray-700' : 'text-gray-900'
                      "
                    >
                      {{ notification.title }}
                      <span
                        v-if="notification.actionRequired"
                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800"
                      >
                        Action Required
                      </span>
                      <span
                        v-if="!notification.isRead"
                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                      >
                        New
                      </span>
                    </p>
                    <p
                      class="text-gray-500 text-sm truncate max-w-lg line-clamp-2"
                    >
                      {{ notification.message }}
                    </p>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                    :class="getPriorityClass(notification.priority)"
                  >
                    {{ notification.priority }}
                  </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ getRelativeTime(notification.timestamp) }}
                </td>
                <td
                  class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                >
                  <button
                    v-if="!notification.isRead"
                    @click="markAsRead(notification.id)"
                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                  >
                    Mark Read
                  </button>
                  <button
                    @click="deleteNotification(notification.id)"
                    class="text-red-600 hover:text-red-900 font-medium"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
        >
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
            >
              Previous
            </button>
            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              :class="{
                'opacity-50 cursor-not-allowed': currentPage === totalPages,
              }"
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
                  (currentPage - 1) * perPage + 1
                }}</span>
                to
                <span class="font-medium">{{
                  Math.min(currentPage * perPage, filteredNotifications.length)
                }}</span>
                of
                <span class="font-medium">{{
                  filteredNotifications.length
                }}</span>
                results
              </p>
            </div>
            <div v-if="totalPages > 1">
              <nav
                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                aria-label="Pagination"
              >
                <button
                  @click="currentPage--"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  :class="{
                    'opacity-50 cursor-not-allowed': currentPage === 1,
                  }"
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

                <!-- Show page numbers -->
                <template v-for="page in totalPages" :key="page">
                  <button
                    v-if="
                      page === currentPage ||
                      page === 1 ||
                      page === totalPages ||
                      (page >= currentPage - 1 && page <= currentPage + 1)
                    "
                    @click="currentPage = page"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="
                      currentPage === page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                    "
                  >
                    {{ page }}
                  </button>
                  <span
                    v-else-if="page === 2 || page === totalPages - 1"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                  >
                    ...
                  </span>
                </template>

                <button
                  @click="currentPage++"
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  :class="{
                    'opacity-50 cursor-not-allowed': currentPage === totalPages,
                  }"
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

    <!-- Notification Settings Panel -->
    <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">
          Notification Preferences
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-900 mb-4">
              Notification Channels
            </h3>

            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="emailNotifications"
                    v-model="preferences.emailNotifications"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    for="emailNotifications"
                    class="font-medium text-gray-700"
                    >Email Notifications</label
                  >
                  <p class="text-gray-500">Receive notifications via email</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="desktopNotifications"
                    v-model="preferences.desktopNotifications"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    for="desktopNotifications"
                    class="font-medium text-gray-700"
                    >Desktop Notifications</label
                  >
                  <p class="text-gray-500">
                    Receive browser desktop notifications
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="mobileNotifications"
                    v-model="preferences.mobileNotifications"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    for="mobileNotifications"
                    class="font-medium text-gray-700"
                    >Mobile Push Notifications</label
                  >
                  <p class="text-gray-500">
                    Receive push notifications on mobile devices
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-sm font-medium text-gray-900 mb-4">
              Notification Settings
            </h3>

            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="autoRefresh"
                    v-model="preferences.autoRefresh"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="autoRefresh" class="font-medium text-gray-700"
                    >Auto-refresh Notifications</label
                  >
                  <p class="text-gray-500">
                    Automatically refresh notifications
                  </p>
                </div>
              </div>

              <div v-if="preferences.autoRefresh" class="ml-7">
                <label
                  for="refreshInterval"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Refresh Interval (minutes)
                </label>
                <select
                  id="refreshInterval"
                  v-model="preferences.refreshInterval"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option value="1">1 minute</option>
                  <option value="5">5 minutes</option>
                  <option value="10">10 minutes</option>
                  <option value="15">15 minutes</option>
                  <option value="30">30 minutes</option>
                </select>
              </div>

              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input
                    id="batchNotifications"
                    v-model="preferences.batchNotifications"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    for="batchNotifications"
                    class="font-medium text-gray-700"
                    >Batch Notifications</label
                  >
                  <p class="text-gray-500">
                    Combine multiple notifications into one message
                  </p>
                </div>
              </div>

              <div v-if="preferences.batchNotifications" class="ml-7">
                <label
                  for="batchInterval"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Batch Interval (minutes)
                </label>
                <select
                  id="batchInterval"
                  v-model="preferences.batchInterval"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option value="5">5 minutes</option>
                  <option value="15">15 minutes</option>
                  <option value="30">30 minutes</option>
                  <option value="60">1 hour</option>
                </select>
              </div>

              <div class="mt-6">
                <label
                  for="notificationHistory"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Keep Notification History (days)
                </label>
                <select
                  id="notificationHistory"
                  v-model="preferences.notificationHistory"
                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option value="7">7 days</option>
                  <option value="15">15 days</option>
                  <option value="30">30 days</option>
                  <option value="60">60 days</option>
                  <option value="90">90 days</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
          <button
            type="button"
            @click="testNotificationSettings"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Test Settings
          </button>
          <button
            type="button"
            @click="saveNotificationPreferences"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Save Settings
          </button>
        </div>
      </div>
    </div>

    <!-- Notification Details Modal -->
    <div
      v-if="showNotificationDetails"
      class="fixed z-10 inset-0 overflow-y-auto"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="closeNotificationDetails"
        ></div>

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div v-if="selectedNotification">
              <div class="flex items-center mb-4">
                <div
                  class="flex-shrink-0 p-2 rounded-md mr-3"
                  :class="{
                    'bg-purple-100 text-purple-700':
                      selectedNotification.type === 'system',
                    'bg-blue-100 text-blue-700':
                      selectedNotification.type === 'user',
                    'bg-green-100 text-green-700':
                      selectedNotification.type === 'course',
                    'bg-amber-100 text-amber-700':
                      selectedNotification.type === 'project',
                    'bg-emerald-100 text-emerald-700':
                      selectedNotification.type === 'payment',
                    'bg-red-100 text-red-700':
                      selectedNotification.type === 'security',
                  }"
                >
                  <span
                    v-html="getTypeIcon(selectedNotification.type)"
                    class="h-6 w-6"
                  ></span>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ selectedNotification.title }}
                  </h3>
                  <div class="mt-1 flex items-center">
                    <p class="text-sm text-gray-500">
                      {{ formatDate(selectedNotification.timestamp) }}
                    </p>
                    <span
                      class="ml-3 px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="getPriorityClass(selectedNotification.priority)"
                    >
                      {{ selectedNotification.priority }}
                    </span>
                    <span
                      v-if="selectedNotification.actionRequired"
                      class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800"
                    >
                      Action Required
                    </span>
                  </div>
                </div>
              </div>

              <!-- Sender info if available -->
              <div
                v-if="selectedNotification.sender"
                class="flex items-center mb-4 bg-gray-50 p-3 rounded-lg"
              >
                <img
                  :src="selectedNotification.sender.avatar"
                  :alt="selectedNotification.sender.name"
                  class="h-10 w-10 rounded-full object-cover"
                />
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ selectedNotification.sender.name }}
                  </p>
                  <p class="text-xs text-gray-500 capitalize">
                    {{ selectedNotification.sender.role }}
                  </p>
                </div>
              </div>

              <div class="mt-4">
                <p class="text-sm text-gray-700 whitespace-pre-line">
                  {{ selectedNotification.message }}
                </p>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              v-if="
                selectedNotification?.actionRequired &&
                selectedNotification?.actionUrl
              "
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ selectedNotification?.actionText || "Take Action" }}
            </button>
            <button
              v-if="selectedNotification && !selectedNotification.isRead"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              @click="markAsRead(selectedNotification.id)"
            >
              Mark as Read
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
              @click="closeNotificationDetails"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div
      v-if="toast.show"
      class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50"
    >
      <div
        class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
        :class="{ 'animate-enter': toast.show, 'animate-leave': !toast.show }"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg
                v-if="toast.type === 'success'"
                class="h-6 w-6 text-green-400"
                xmlns="http://www.w3.org/2000/svg"
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
              <svg
                v-else-if="toast.type === 'error'"
                class="h-6 w-6 text-red-400"
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
              <svg
                v-else
                class="h-6 w-6 text-blue-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
              <p class="text-sm font-medium text-gray-900">
                {{ toast.message }}
              </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="toast.show = false"
                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <span class="sr-only">Close</span>
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
    </div>
  </div>
</template>

<style scoped>
/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Toast animations */
@keyframes enter {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes leave {
  from {
    transform: translateY(0);
    opacity: 1;
  }
  to {
    transform: translateY(100%);
    opacity: 0;
  }
}

.animate-enter {
  animation: enter 0.3s ease-out;
}

.animate-leave {
  animation: leave 0.2s ease-in forwards;
}

/* Fade in animation for new elements */
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hide scrollbars for the filter tabs container */
.no-scrollbar {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

.no-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
</style>
