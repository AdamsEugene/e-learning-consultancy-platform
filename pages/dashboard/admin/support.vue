<!-- pages/dashboard/admin/support/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";

// Define page meta
definePageMeta({
  layout: "dashboard-admin",
});

// Page title and meta
useHead({
  title: "Support Tickets - Admin Dashboard",
  meta: [
    { name: "description", content: "Manage and respond to support tickets" },
  ],
});

// Define ticket interfaces
interface User {
  id: string;
  name: string;
  email: string;
  avatar: string;
  role: "student" | "instructor" | "consultant" | "client" | "admin";
}

interface Attachment {
  id: string;
  filename: string;
  filesize: number;
  fileType: string;
  url: string;
}

interface TicketMessage {
  id: string;
  ticketId: string;
  content: string;
  sender: User;
  timestamp: string;
  attachments: Attachment[];
  isInternal: boolean;
}

interface Ticket {
  id: string;
  subject: string;
  description: string;
  user: User;
  category:
    | "technical"
    | "billing"
    | "account"
    | "course"
    | "consultant"
    | "platform"
    | "other";
  status:
    | "open"
    | "in_progress"
    | "waiting_on_customer"
    | "waiting_on_third_party"
    | "resolved"
    | "closed";
  priority: "low" | "medium" | "high" | "urgent";
  createdAt: string;
  updatedAt: string;
  assignedTo?: User;
  messages: TicketMessage[];
  relatedEntityType?:
    | "course"
    | "project"
    | "user"
    | "payment"
    | "consultation";
  relatedEntityId?: string;
  tags: string[];
}

// State
const tickets = ref<Ticket[]>([]);
const isLoading = ref(true);
const selectedTicket = ref<Ticket | null>(null);
const showTicketDetails = ref(false);
const activeFilter = ref("all");
const searchQuery = ref("");
const sortBy = ref("newest");
const currentPage = ref(1);
const perPage = ref(10);
const totalTickets = ref(0);
const newReplyMessage = ref("");
const isSubmittingReply = ref(false);
const newInternalNote = ref("");
const isSubmittingNote = ref(false);
const ticketAttachments = ref<File[]>([]);
const showFilterPanel = ref(false);

// Filter states
const filterPriorities = ref<string[]>([]);
const filterCategories = ref<string[]>([]);
const filterStatuses = ref<string[]>([]);
const filterAssignedTo = ref<string>("");
const filterDateFrom = ref("");
const filterDateTo = ref("");

// Toast notification state
const toast = ref({
  show: false,
  message: "",
  type: "success", // 'success', 'error', 'warning', 'info'
});

// Current admin user (mocked)
const currentAdmin = ref<User>({
  id: "admin-1",
  name: "Admin User",
  email: "admin@learnconsult.com",
  avatar: "/api/placeholder/100/100",
  role: "admin",
});

// Filter options
const statusOptions = [
  { id: "open", label: "Open", color: "bg-green-100 text-green-800" },
  {
    id: "in_progress",
    label: "In Progress",
    color: "bg-blue-100 text-blue-800",
  },
  {
    id: "waiting_on_customer",
    label: "Waiting on Customer",
    color: "bg-amber-100 text-amber-800",
  },
  {
    id: "waiting_on_third_party",
    label: "Waiting on Third Party",
    color: "bg-purple-100 text-purple-800",
  },
  { id: "resolved", label: "Resolved", color: "bg-gray-100 text-gray-800" },
  { id: "closed", label: "Closed", color: "bg-gray-100 text-gray-800" },
];

const priorityOptions = [
  { id: "low", label: "Low", color: "bg-blue-100 text-blue-800" },
  { id: "medium", label: "Medium", color: "bg-yellow-100 text-yellow-800" },
  { id: "high", label: "High", color: "bg-orange-100 text-orange-800" },
  { id: "urgent", label: "Urgent", color: "bg-red-100 text-red-800" },
];

const categoryOptions = [
  { id: "technical", label: "Technical" },
  { id: "billing", label: "Billing" },
  { id: "account", label: "Account" },
  { id: "course", label: "Course" },
  { id: "consultant", label: "Consultant" },
  { id: "platform", label: "Platform" },
  { id: "other", label: "Other" },
];

// Admin list for assignment
const adminList = ref<User[]>([
  {
    id: "admin-1",
    name: "Admin User",
    email: "admin@learnconsult.com",
    avatar: "/api/placeholder/100/100",
    role: "admin",
  },
  {
    id: "admin-2",
    name: "Jane Smith",
    email: "jane.smith@learnconsult.com",
    avatar: "/api/placeholder/100/100",
    role: "admin",
  },
  {
    id: "admin-3",
    name: "Robert Johnson",
    email: "robert.johnson@learnconsult.com",
    avatar: "/api/placeholder/100/100",
    role: "admin",
  },
]);

// Sort options
const sortOptions = [
  { id: "newest", label: "Newest First" },
  { id: "oldest", label: "Oldest First" },
  { id: "priority", label: "Priority (Highest First)" },
  { id: "updated", label: "Recently Updated" },
];

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

// Get status class for styling
const getStatusClass = (status: string) => {
  const statusOption = statusOptions.find((option) => option.id === status);
  return statusOption ? statusOption.color : "bg-gray-100 text-gray-800";
};

// Get priority class for styling
const getPriorityClass = (priority: string) => {
  const priorityOption = priorityOptions.find(
    (option) => option.id === priority
  );
  return priorityOption ? priorityOption.color : "bg-gray-100 text-gray-800";
};

// Format file size
const formatFileSize = (bytes: number) => {
  if (bytes < 1024) {
    return bytes + " B";
  } else if (bytes < 1024 * 1024) {
    return (bytes / 1024).toFixed(1) + " KB";
  } else if (bytes < 1024 * 1024 * 1024) {
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
  } else {
    return (bytes / (1024 * 1024 * 1024)).toFixed(1) + " GB";
  }
};

// Get ticket category icon
const getCategoryIcon = (category: string) => {
  switch (category) {
    case "technical":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>`;
    case "billing":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "account":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
      </svg>`;
    case "course":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
      </svg>`;
    case "consultant":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "platform":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get user role badge class
const getUserRoleBadgeClass = (role: string) => {
  switch (role) {
    case "student":
      return "bg-blue-100 text-blue-800";
    case "instructor":
      return "bg-purple-100 text-purple-800";
    case "consultant":
      return "bg-indigo-100 text-indigo-800";
    case "client":
      return "bg-green-100 text-green-800";
    case "admin":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Watch for filter changes
watch(
  [
    activeFilter,
    searchQuery,
    sortBy,
    filterPriorities,
    filterCategories,
    filterStatuses,
    filterAssignedTo,
    filterDateFrom,
    filterDateTo,
  ],
  () => {
    // Reset pagination when filters change
    currentPage.value = 1;
  }
);

// Computed filtered tickets
const filteredTickets = computed(() => {
  let result = [...tickets.value];

  // Apply active filter
  if (activeFilter.value === "open") {
    result = result.filter(
      (ticket) => ticket.status === "open" || ticket.status === "in_progress"
    );
  } else if (activeFilter.value === "waiting") {
    result = result.filter(
      (ticket) =>
        ticket.status === "waiting_on_customer" ||
        ticket.status === "waiting_on_third_party"
    );
  } else if (activeFilter.value === "closed") {
    result = result.filter(
      (ticket) => ticket.status === "resolved" || ticket.status === "closed"
    );
  } else if (activeFilter.value === "my_tickets") {
    result = result.filter(
      (ticket) => ticket.assignedTo?.id === currentAdmin.value.id
    );
  }

  // Apply search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (ticket) =>
        ticket.subject.toLowerCase().includes(query) ||
        ticket.description.toLowerCase().includes(query) ||
        ticket.user.name.toLowerCase().includes(query) ||
        ticket.user.email.toLowerCase().includes(query) ||
        ticket.id.toLowerCase().includes(query)
    );
  }

  // Apply advanced filters
  if (filterPriorities.value.length > 0) {
    result = result.filter((ticket) =>
      filterPriorities.value.includes(ticket.priority)
    );
  }

  if (filterCategories.value.length > 0) {
    result = result.filter((ticket) =>
      filterCategories.value.includes(ticket.category)
    );
  }

  if (filterStatuses.value.length > 0) {
    result = result.filter((ticket) =>
      filterStatuses.value.includes(ticket.status)
    );
  }

  if (filterAssignedTo.value) {
    if (filterAssignedTo.value === "unassigned") {
      result = result.filter((ticket) => !ticket.assignedTo);
    } else {
      result = result.filter(
        (ticket) => ticket.assignedTo?.id === filterAssignedTo.value
      );
    }
  }

  if (filterDateFrom.value) {
    const fromDate = new Date(filterDateFrom.value);
    result = result.filter((ticket) => new Date(ticket.createdAt) >= fromDate);
  }

  if (filterDateTo.value) {
    const toDate = new Date(filterDateTo.value);
    toDate.setHours(23, 59, 59, 999); // End of day
    result = result.filter((ticket) => new Date(ticket.createdAt) <= toDate);
  }

  // Apply sorting
  if (sortBy.value === "newest") {
    result.sort(
      (a, b) =>
        new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
    );
  } else if (sortBy.value === "oldest") {
    result.sort(
      (a, b) =>
        new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime()
    );
  } else if (sortBy.value === "priority") {
    const priorityOrder = { urgent: 0, high: 1, medium: 2, low: 3 };
    result.sort(
      (a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]
    );
  } else if (sortBy.value === "updated") {
    result.sort(
      (a, b) =>
        new Date(b.updatedAt).getTime() - new Date(a.updatedAt).getTime()
    );
  }

  return result;
});

// Computed pagination
const paginatedTickets = computed(() => {
  const startIndex = (currentPage.value - 1) * perPage.value;
  const endIndex = startIndex + perPage.value;
  return filteredTickets.value.slice(startIndex, endIndex);
});

const totalPages = computed(() =>
  Math.ceil(filteredTickets.value.length / perPage.value)
);

// Computed counts
const openTicketsCount = computed(
  () =>
    tickets.value.filter(
      (t) => t.status === "open" || t.status === "in_progress"
    ).length
);
const waitingTicketsCount = computed(
  () =>
    tickets.value.filter(
      (t) =>
        t.status === "waiting_on_customer" ||
        t.status === "waiting_on_third_party"
    ).length
);
const closedTicketsCount = computed(
  () =>
    tickets.value.filter(
      (t) => t.status === "resolved" || t.status === "closed"
    ).length
);
const myTicketsCount = computed(
  () =>
    tickets.value.filter((t) => t.assignedTo?.id === currentAdmin.value.id)
      .length
);
const urgentTicketsCount = computed(
  () =>
    tickets.value.filter(
      (t) =>
        t.priority === "urgent" &&
        (t.status === "open" || t.status === "in_progress")
    ).length
);

// Fetching tickets data (mock data for now)
const fetchTickets = async () => {
  isLoading.value = true;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mock data
    tickets.value = [
      {
        id: "TKT-10001",
        subject: "Course Video Not Playing",
        description:
          'I can\'t play the videos in Module 3 of the "Advanced JavaScript" course. Getting an error: "Video unavailable".',
        user: {
          id: "user-1",
          name: "John Smith",
          email: "john.smith@example.com",
          avatar: "/api/placeholder/100/100",
          role: "student",
        },
        category: "course",
        status: "open",
        priority: "high",
        createdAt: new Date(new Date().getTime() - 3 * 3600000).toISOString(),
        updatedAt: new Date(new Date().getTime() - 2 * 3600000).toISOString(),
        messages: [
          {
            id: "msg-1",
            ticketId: "TKT-10001",
            content:
              'I can\'t play the videos in Module 3 of the "Advanced JavaScript" course. Getting an error: "Video unavailable". I have tried using both Chrome and Firefox browsers, but the issue persists. Other course videos seem to be working fine.',
            sender: {
              id: "user-1",
              name: "John Smith",
              email: "john.smith@example.com",
              avatar: "/api/placeholder/100/100",
              role: "student",
            },
            timestamp: new Date(
              new Date().getTime() - 3 * 3600000
            ).toISOString(),
            attachments: [
              {
                id: "attach-1",
                filename: "error_screenshot.png",
                filesize: 245000,
                fileType: "image/png",
                url: "/api/placeholder/800/600",
              },
            ],
            isInternal: false,
          },
        ],
        relatedEntityType: "course",
        relatedEntityId: "course-123",
        tags: ["video", "playback", "technical"],
      },
      {
        id: "TKT-10002",
        subject: "Billing Question - Pro Plan Monthly vs Annual",
        description:
          "I'm considering upgrading to the Pro plan but I'm not sure about the differences between monthly and annual billing.",
        user: {
          id: "user-2",
          name: "Sarah Johnson",
          email: "sarah.johnson@example.com",
          avatar: "/api/placeholder/100/100",
          role: "student",
        },
        category: "billing",
        status: "waiting_on_customer",
        priority: "medium",
        createdAt: new Date(new Date().getTime() - 8 * 3600000).toISOString(),
        updatedAt: new Date(new Date().getTime() - 5 * 3600000).toISOString(),
        assignedTo: {
          id: "admin-2",
          name: "Jane Smith",
          email: "jane.smith@learnconsult.com",
          avatar: "/api/placeholder/100/100",
          role: "admin",
        },
        messages: [
          {
            id: "msg-2",
            ticketId: "TKT-10002",
            content:
              "I'm considering upgrading to the Pro plan but I'm not sure about the differences between monthly and annual billing. Is there a significant cost savings with annual? And can I switch between plans easily?",
            sender: {
              id: "user-2",
              name: "Sarah Johnson",
              email: "sarah.johnson@example.com",
              avatar: "/api/placeholder/100/100",
              role: "student",
            },
            timestamp: new Date(
              new Date().getTime() - 8 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-3",
            ticketId: "TKT-10002",
            content:
              "Hi Sarah, with the annual plan you save approximately 20% compared to monthly billing. You can switch from monthly to annual at any time, but switching from annual to monthly is only available when your current billing cycle ends. Let me know if you have any other questions!",
            sender: {
              id: "admin-2",
              name: "Jane Smith",
              email: "jane.smith@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 7 * 3600000
            ).toISOString(),
            attachments: [
              {
                id: "attach-2",
                filename: "pricing_comparison.pdf",
                filesize: 420000,
                fileType: "application/pdf",
                url: "#",
              },
            ],
            isInternal: false,
          },
          {
            id: "msg-4",
            ticketId: "TKT-10002",
            content:
              "We should probably follow up with Sarah as she hasn't responded yet. She seems like a good candidate for the annual plan based on her course activity.",
            sender: {
              id: "admin-2",
              name: "Jane Smith",
              email: "jane.smith@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 5 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: true,
          },
        ],
        tags: ["billing", "pro plan", "upgrade"],
      },
      {
        id: "TKT-10003",
        subject: "Consultant Availability Issue",
        description:
          "I booked a consultation but the consultant was not available during the scheduled time.",
        user: {
          id: "user-3",
          name: "Michael Chen",
          email: "michael.chen@example.com",
          avatar: "/api/placeholder/100/100",
          role: "client",
        },
        category: "consultant",
        status: "in_progress",
        priority: "urgent",
        createdAt: new Date(new Date().getTime() - 24 * 3600000).toISOString(),
        updatedAt: new Date(new Date().getTime() - 20 * 3600000).toISOString(),
        assignedTo: {
          id: "admin-1",
          name: "Admin User",
          email: "admin@learnconsult.com",
          avatar: "/api/placeholder/100/100",
          role: "admin",
        },
        messages: [
          {
            id: "msg-5",
            ticketId: "TKT-10003",
            content:
              "I booked a consultation with Alex Thompson for yesterday at 3:00 PM but they never showed up to the Zoom meeting. I waited for 30 minutes. This is the second time this has happened, and I'm quite frustrated as I'm paying for these sessions.",
            sender: {
              id: "user-3",
              name: "Michael Chen",
              email: "michael.chen@example.com",
              avatar: "/api/placeholder/100/100",
              role: "client",
            },
            timestamp: new Date(
              new Date().getTime() - 24 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-6",
            ticketId: "TKT-10003",
            content:
              "I've checked the system and I see the booking was confirmed. I'll contact Alex right away to understand what happened and get back to you ASAP. I sincerely apologize for this experience.",
            sender: {
              id: "admin-1",
              name: "Admin User",
              email: "admin@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 23 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-7",
            ticketId: "TKT-10003",
            content:
              "I've contacted Alex. Apparently there was a mix-up with timezone settings in his calendar. We need to fix this system-wide as it may be affecting other consultants too.",
            sender: {
              id: "admin-1",
              name: "Admin User",
              email: "admin@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 22 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: true,
          },
        ],
        relatedEntityType: "consultation",
        relatedEntityId: "consult-456",
        tags: ["consultation", "schedule", "no-show"],
      },
      {
        id: "TKT-10004",
        subject: "Can't Access My Account After Password Reset",
        description:
          "I tried to reset my password but now I can't log in with the new password.",
        user: {
          id: "user-4",
          name: "Emily Garcia",
          email: "emily.garcia@example.com",
          avatar: "/api/placeholder/100/100",
          role: "student",
        },
        category: "account",
        status: "resolved",
        priority: "high",
        createdAt: new Date(new Date().getTime() - 48 * 3600000).toISOString(),
        updatedAt: new Date(new Date().getTime() - 45 * 3600000).toISOString(),
        assignedTo: {
          id: "admin-3",
          name: "Robert Johnson",
          email: "robert.johnson@learnconsult.com",
          avatar: "/api/placeholder/100/100",
          role: "admin",
        },
        messages: [
          {
            id: "msg-8",
            ticketId: "TKT-10004",
            content:
              "I tried to reset my password but now I can't log in with the new password. I've tried multiple times but keep getting \"invalid credentials\" error.",
            sender: {
              id: "user-4",
              name: "Emily Garcia",
              email: "emily.garcia@example.com",
              avatar: "/api/placeholder/100/100",
              role: "student",
            },
            timestamp: new Date(
              new Date().getTime() - 48 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-9",
            ticketId: "TKT-10004",
            content:
              "Hi Emily, I'm sorry you're having trouble logging in. I've checked your account and I noticed that your email verification wasn't completed after the password reset. I've manually verified it now. Please try logging in again, and let me know if it works!",
            sender: {
              id: "admin-3",
              name: "Robert Johnson",
              email: "robert.johnson@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 47 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-10",
            ticketId: "TKT-10004",
            content:
              "Thank you! That worked perfectly. I'm able to log in now.",
            sender: {
              id: "user-4",
              name: "Emily Garcia",
              email: "emily.garcia@example.com",
              avatar: "/api/placeholder/100/100",
              role: "student",
            },
            timestamp: new Date(
              new Date().getTime() - 46 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
        ],
        tags: ["account", "login", "password reset"],
      },
      {
        id: "TKT-10005",
        subject: "Feature Request: Dark Mode",
        description:
          "Would like to suggest adding dark mode to the platform for better accessibility and night time viewing.",
        user: {
          id: "user-5",
          name: "David Wilson",
          email: "david.wilson@example.com",
          avatar: "/api/placeholder/100/100",
          role: "instructor",
        },
        category: "platform",
        status: "closed",
        priority: "low",
        createdAt: new Date(
          new Date().getTime() - 7 * 24 * 3600000
        ).toISOString(),
        updatedAt: new Date(
          new Date().getTime() - 6 * 24 * 3600000
        ).toISOString(),
        messages: [
          {
            id: "msg-11",
            ticketId: "TKT-10005",
            content:
              "Would like to suggest adding dark mode to the platform for better accessibility and night time viewing. Many of my students work late and have mentioned this would be helpful.",
            sender: {
              id: "user-5",
              name: "David Wilson",
              email: "david.wilson@example.com",
              avatar: "/api/placeholder/100/100",
              role: "instructor",
            },
            timestamp: new Date(
              new Date().getTime() - 7 * 24 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-12",
            ticketId: "TKT-10005",
            content:
              "Hi David, thank you for your suggestion! We actually have dark mode on our product roadmap for the next quarter. We appreciate your feedback as it helps us prioritize our development efforts. I'll mark this as a +1 vote for the feature.",
            sender: {
              id: "admin-2",
              name: "Jane Smith",
              email: "jane.smith@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 6.5 * 24 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-13",
            ticketId: "TKT-10005",
            content: "Great to hear! Looking forward to seeing it implemented.",
            sender: {
              id: "user-5",
              name: "David Wilson",
              email: "david.wilson@example.com",
              avatar: "/api/placeholder/100/100",
              role: "instructor",
            },
            timestamp: new Date(
              new Date().getTime() - 6 * 24 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
        ],
        tags: ["feature request", "dark mode", "ui", "accessibility"],
      },
      {
        id: "TKT-10006",
        subject: "Payment Failed but Amount Deducted",
        description:
          "My payment for course enrollment failed, but the amount was deducted from my account.",
        user: {
          id: "user-6",
          name: "Lisa Brown",
          email: "lisa.brown@example.com",
          avatar: "/api/placeholder/100/100",
          role: "student",
        },
        category: "billing",
        status: "in_progress",
        priority: "urgent",
        createdAt: new Date(new Date().getTime() - 5 * 3600000).toISOString(),
        updatedAt: new Date(new Date().getTime() - 4 * 3600000).toISOString(),
        assignedTo: {
          id: "admin-2",
          name: "Jane Smith",
          email: "jane.smith@learnconsult.com",
          avatar: "/api/placeholder/100/100",
          role: "admin",
        },
        messages: [
          {
            id: "msg-14",
            ticketId: "TKT-10006",
            content:
              'I tried to enroll in the "Data Science Fundamentals" course, but got an error after payment. However, checking my bank account shows that $89.99 was already deducted. I don\'t have access to the course and need this resolved urgently as the cohort starts tomorrow.',
            sender: {
              id: "user-6",
              name: "Lisa Brown",
              email: "lisa.brown@example.com",
              avatar: "/api/placeholder/100/100",
              role: "student",
            },
            timestamp: new Date(
              new Date().getTime() - 5 * 3600000
            ).toISOString(),
            attachments: [
              {
                id: "attach-3",
                filename: "payment_receipt.pdf",
                filesize: 325000,
                fileType: "application/pdf",
                url: "#",
              },
            ],
            isInternal: false,
          },
          {
            id: "msg-15",
            ticketId: "TKT-10006",
            content:
              "Hi Lisa, I'm sorry to hear about this issue. I can see the attempted payment in our system - it shows as \"pending\" which means it was authorized but might be reversed by your bank within 1-3 business days. In the meantime, I've manually granted you access to the course so you don't miss the start. I'll monitor the payment status and keep you updated.",
            sender: {
              id: "admin-2",
              name: "Jane Smith",
              email: "jane.smith@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 4 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
        ],
        relatedEntityType: "payment",
        relatedEntityId: "payment-789",
        tags: ["payment", "enrollment", "failed transaction"],
      },
      {
        id: "TKT-10007",
        subject: "Missing Certificate After Course Completion",
        description:
          "I've completed all modules and assessments but haven't received my certificate.",
        user: {
          id: "user-7",
          name: "James Taylor",
          email: "james.taylor@example.com",
          avatar: "/api/placeholder/100/100",
          role: "student",
        },
        category: "course",
        status: "waiting_on_third_party",
        priority: "medium",
        createdAt: new Date(
          new Date().getTime() - 3 * 24 * 3600000
        ).toISOString(),
        updatedAt: new Date(
          new Date().getTime() - 2 * 24 * 3600000
        ).toISOString(),
        assignedTo: {
          id: "admin-3",
          name: "Robert Johnson",
          email: "robert.johnson@learnconsult.com",
          avatar: "/api/placeholder/100/100",
          role: "admin",
        },
        messages: [
          {
            id: "msg-16",
            ticketId: "TKT-10007",
            content:
              "I've completed all modules and assessments for the \"Project Management Professional\" course but haven't received my certificate. The course shows 100% completion in my dashboard.",
            sender: {
              id: "user-7",
              name: "James Taylor",
              email: "james.taylor@example.com",
              avatar: "/api/placeholder/100/100",
              role: "student",
            },
            timestamp: new Date(
              new Date().getTime() - 3 * 24 * 3600000
            ).toISOString(),
            attachments: [
              {
                id: "attach-4",
                filename: "completion_screenshot.png",
                filesize: 185000,
                fileType: "image/png",
                url: "/api/placeholder/800/600",
              },
            ],
            isInternal: false,
          },
          {
            id: "msg-17",
            ticketId: "TKT-10007",
            content:
              "Hi James, thanks for reaching out. I've checked your course progress and you're right - you've completed everything successfully. There seems to be an issue with our certificate generation system. I've raised this with our development team and they're working on a fix. I'll make sure your certificate is generated as soon as the system is working again.",
            sender: {
              id: "admin-3",
              name: "Robert Johnson",
              email: "robert.johnson@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 2.5 * 24 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: false,
          },
          {
            id: "msg-18",
            ticketId: "TKT-10007",
            content:
              "I've checked with the dev team. The certificate generator is having issues with the PDF template for this particular course. They're working on it but said it might take another day to fix.",
            sender: {
              id: "admin-3",
              name: "Robert Johnson",
              email: "robert.johnson@learnconsult.com",
              avatar: "/api/placeholder/100/100",
              role: "admin",
            },
            timestamp: new Date(
              new Date().getTime() - 2 * 24 * 3600000
            ).toISOString(),
            attachments: [],
            isInternal: true,
          },
        ],
        relatedEntityType: "course",
        relatedEntityId: "course-456",
        tags: ["certificate", "course completion", "technical"],
      },
    ];

    totalTickets.value = tickets.value.length;
  } catch (error) {
    console.error("Error fetching tickets:", error);
    showToast("Failed to load tickets", "error");
  } finally {
    isLoading.value = false;
  }
};

// View ticket details
const viewTicketDetails = (ticket: Ticket) => {
  selectedTicket.value = ticket;
  showTicketDetails.value = true;
  newReplyMessage.value = "";
  newInternalNote.value = "";
  ticketAttachments.value = [];
};

// Close ticket details modal
const closeTicketDetails = () => {
  showTicketDetails.value = false;
  selectedTicket.value = null;
};

// Submit reply to ticket
const submitReply = async () => {
  if (!selectedTicket.value || !newReplyMessage.value.trim()) return;

  isSubmittingReply.value = true;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Create new message
    const newMessage: TicketMessage = {
      id: `msg-${Date.now()}`,
      ticketId: selectedTicket.value.id,
      content: newReplyMessage.value.trim(),
      sender: currentAdmin.value,
      timestamp: new Date().toISOString(),
      attachments: [],
      isInternal: false,
    };

    // Update ticket status to in_progress if it was open
    if (selectedTicket.value.status === "open") {
      selectedTicket.value.status = "in_progress";
    }

    // Add message to ticket
    selectedTicket.value.messages.push(newMessage);
    selectedTicket.value.updatedAt = new Date().toISOString();

    // If ticket wasn't assigned, assign to current admin
    if (!selectedTicket.value.assignedTo) {
      selectedTicket.value.assignedTo = currentAdmin.value;
    }

    // Reset form
    newReplyMessage.value = "";
    ticketAttachments.value = [];

    showToast("Reply sent successfully");
  } catch (error) {
    console.error("Error submitting reply:", error);
    showToast("Failed to send reply", "error");
  } finally {
    isSubmittingReply.value = false;
  }
};

// Submit internal note
const submitInternalNote = async () => {
  if (!selectedTicket.value || !newInternalNote.value.trim()) return;

  isSubmittingNote.value = true;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Create new internal message
    const newMessage: TicketMessage = {
      id: `msg-${Date.now()}`,
      ticketId: selectedTicket.value.id,
      content: newInternalNote.value.trim(),
      sender: currentAdmin.value,
      timestamp: new Date().toISOString(),
      attachments: [],
      isInternal: true,
    };

    // Add message to ticket
    selectedTicket.value.messages.push(newMessage);
    selectedTicket.value.updatedAt = new Date().toISOString();

    // Reset form
    newInternalNote.value = "";

    showToast("Internal note added");
  } catch (error) {
    console.error("Error adding internal note:", error);
    showToast("Failed to add note", "error");
  } finally {
    isSubmittingNote.value = false;
  }
};

// Handle file upload
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    for (let i = 0; i < input.files.length; i++) {
      ticketAttachments.value.push(input.files[i]);
    }
  }
};

// Remove attachment
const removeAttachment = (index: number) => {
  ticketAttachments.value.splice(index, 1);
};

// Update ticket status
const updateTicketStatus = async (newStatus: string) => {
  if (!selectedTicket.value) return;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    selectedTicket.value.status = newStatus as any;
    selectedTicket.value.updatedAt = new Date().toISOString();

    showToast(`Ticket status updated to ${newStatus.replace("_", " ")}`);
  } catch (error) {
    console.error("Error updating ticket status:", error);
    showToast("Failed to update ticket status", "error");
  }
};

// Update ticket priority
const updateTicketPriority = async (newPriority: string) => {
  if (!selectedTicket.value) return;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    selectedTicket.value.priority = newPriority as any;
    selectedTicket.value.updatedAt = new Date().toISOString();

    showToast(`Ticket priority updated to ${newPriority}`);
  } catch (error) {
    console.error("Error updating ticket priority:", error);
    showToast("Failed to update ticket priority", "error");
  }
};

// Assign ticket
const assignTicket = async (adminId: string) => {
  if (!selectedTicket.value) return;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    const admin = adminList.value.find((a) => a.id === adminId);
    if (admin) {
      selectedTicket.value.assignedTo = admin;
      selectedTicket.value.updatedAt = new Date().toISOString();

      showToast(`Ticket assigned to ${admin.name}`);
    }
  } catch (error) {
    console.error("Error assigning ticket:", error);
    showToast("Failed to assign ticket", "error");
  }
};

// Reset all filters
const resetFilters = () => {
  activeFilter.value = "all";
  searchQuery.value = "";
  sortBy.value = "newest";
  filterPriorities.value = [];
  filterCategories.value = [];
  filterStatuses.value = [];
  filterAssignedTo.value = "";
  filterDateFrom.value = "";
  filterDateTo.value = "";
  showFilterPanel.value = false;

  showToast("Filters reset");
};

// Fetch data on mount
onMounted(() => {
  fetchTickets();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage support tickets from users across the platform
          </p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-3">
          <button
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="showFilterPanel = !showFilterPanel"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                clip-rule="evenodd"
              />
            </svg>
            {{ showFilterPanel ? "Hide Filters" : "Advanced Filters" }}
          </button>

          <button
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="fetchTickets"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
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

      <!-- Stats Cards -->
      <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
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
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Total Tickets
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ totalTickets }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 px-4 py-4 sm:px-6 cursor-pointer hover:bg-gray-100 transition-colors"
            @click="activeFilter = 'all'"
          >
            <div class="text-sm">
              <a class="font-medium text-indigo-600 hover:text-indigo-500"
                >View all tickets</a
              >
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
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Open Tickets
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ openTicketsCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 px-4 py-4 sm:px-6 cursor-pointer hover:bg-gray-100 transition-colors"
            @click="activeFilter = 'open'"
          >
            <div class="text-sm">
              <a class="font-medium text-indigo-600 hover:text-indigo-500"
                >View open tickets</a
              >
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
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Waiting
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ waitingTicketsCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 px-4 py-4 sm:px-6 cursor-pointer hover:bg-gray-100 transition-colors"
            @click="activeFilter = 'waiting'"
          >
            <div class="text-sm">
              <a class="font-medium text-indigo-600 hover:text-indigo-500"
                >View waiting tickets</a
              >
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 text-red-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    Urgent
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ urgentTicketsCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 px-4 py-4 sm:px-6 cursor-pointer hover:bg-gray-100 transition-colors"
            @click="
              activeFilter = 'open';
              filterPriorities = ['urgent'];
              showFilterPanel = true;
            "
          >
            <div class="text-sm">
              <a class="font-medium text-indigo-600 hover:text-indigo-500"
                >View urgent tickets</a
              >
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
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    My Tickets
                  </dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ myTicketsCount }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div
            class="bg-gray-50 px-4 py-4 sm:px-6 cursor-pointer hover:bg-gray-100 transition-colors"
            @click="activeFilter = 'my_tickets'"
          >
            <div class="text-sm">
              <a class="font-medium text-indigo-600 hover:text-indigo-500"
                >View my tickets</a
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Advanced Filter Panel (Expandable) -->
      <div
        v-if="showFilterPanel"
        class="mt-6 bg-white shadow rounded-lg overflow-hidden transition-all duration-300 ease-in-out"
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Advanced Filters</h3>
            <button
              class="text-sm text-indigo-600 hover:text-indigo-500"
              @click="resetFilters"
            >
              Reset all filters
            </button>
          </div>

          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <!-- Priority Filter -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >Priority</label
              >
              <div class="space-y-2">
                <div
                  v-for="priority in priorityOptions"
                  :key="priority.id"
                  class="flex items-center"
                >
                  <input
                    :id="`priority-${priority.id}`"
                    v-model="filterPriorities"
                    type="checkbox"
                    :value="priority.id"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  >
                  <label
                    :for="`priority-${priority.id}`"
                    class="ml-2 text-sm text-gray-700"
                    >{{ priority.label }}</label
                  >
                </div>
              </div>
            </div>

            <!-- Category Filter -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >Category</label
              >
              <div class="space-y-2">
                <div
                  v-for="category in categoryOptions"
                  :key="category.id"
                  class="flex items-center"
                >
                  <input
                    :id="`category-${category.id}`"
                    v-model="filterCategories"
                    type="checkbox"
                    :value="category.id"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  >
                  <label
                    :for="`category-${category.id}`"
                    class="ml-2 text-sm text-gray-700"
                    >{{ category.label }}</label
                  >
                </div>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >Status</label
              >
              <div class="space-y-2">
                <div
                  v-for="status in statusOptions"
                  :key="status.id"
                  class="flex items-center"
                >
                  <input
                    :id="`status-${status.id}`"
                    v-model="filterStatuses"
                    type="checkbox"
                    :value="status.id"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  >
                  <label
                    :for="`status-${status.id}`"
                    class="ml-2 text-sm text-gray-700"
                    >{{ status.label }}</label
                  >
                </div>
              </div>
            </div>

            <!-- Assigned To Filter -->
            <div class="sm:col-span-2">
              <label
                for="assigned-to"
                class="block text-sm font-medium text-gray-700 mb-2"
                >Assigned To</label
              >
              <select
                id="assigned-to"
                v-model="filterAssignedTo"
                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
              >
                <option value="">Any Assignment</option>
                <option value="unassigned">Unassigned</option>
                <option
                  v-for="admin in adminList"
                  :key="admin.id"
                  :value="admin.id"
                >
                  {{ admin.name }}
                </option>
              </select>
            </div>

            <!-- Date Range -->
            <div class="sm:col-span-2">
              <label
                for="date-from"
                class="block text-sm font-medium text-gray-700 mb-2"
                >Created From</label
              >
              <input
                id="date-from"
                v-model="filterDateFrom"
                type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
            </div>

            <div class="sm:col-span-2">
              <label
                for="date-to"
                class="block text-sm font-medium text-gray-700 mb-2"
                >Created To</label
              >
              <input
                id="date-to"
                v-model="filterDateTo"
                type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation + Search and Sort -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-4 sm:px-6 py-4">
        <div class="flex flex-wrap items-center gap-4">
          <!-- Tab Navigation -->
          <div class="flex overflow-x-auto py-1 no-scrollbar">
            <div class="flex space-x-4">
              <button
                v-for="(filter, index) in [
                  { id: 'all', label: 'All Tickets' },
                  { id: 'open', label: `Open (${openTicketsCount})` },
                  { id: 'waiting', label: `Waiting (${waitingTicketsCount})` },
                  { id: 'closed', label: `Closed (${closedTicketsCount})` },
                  { id: 'my_tickets', label: `My Tickets (${myTicketsCount})` },
                ]"
                :key="index"
                class="px-3 py-1.5 text-sm font-medium rounded-md whitespace-nowrap transition-colors duration-200"
                :class="
                  activeFilter === filter.id
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                "
                @click="activeFilter = filter.id"
              >
                {{ filter.label }}
              </button>
            </div>
          </div>

          <div class="flex-1"/>

          <!-- Search and Sort Controls -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search tickets..."
                class="block w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
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

            <!-- Sort Dropdown -->
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
    </div>

    <!-- Tickets List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Loading state -->
      <div v-if="isLoading" class="p-20 flex justify-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
        />
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredTickets.length === 0" class="p-12 text-center">
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
            d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"
          />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No tickets found</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{
            searchQuery ||
            filterPriorities.length ||
            filterCategories.length ||
            filterStatuses.length ||
            filterAssignedTo ||
            filterDateFrom ||
            filterDateTo
              ? "Try adjusting your search filters."
              : "There are no tickets matching the current view."
          }}
        </p>
      </div>

      <!-- Tickets table -->
      <div v-else>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Ticket
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  User
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Category
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
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
                  Last Updated
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Assigned To
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="ticket in paginatedTickets"
                :key="ticket.id"
                class="hover:bg-gray-50 transition-colors cursor-pointer"
                @click="viewTicketDetails(ticket)"
              >
                <!-- Ticket ID & Subject -->
                <td class="px-4 py-4">
                  <div class="flex flex-col">
                    <div class="flex items-center">
                      <span class="font-mono text-xs text-gray-500 mr-2">{{
                        ticket.id
                      }}</span>
                      <span
                        v-if="!ticket.isRead"
                        class="h-2 w-2 rounded-full bg-blue-500"
                      />
                    </div>
                    <div class="text-sm font-medium text-gray-900 mt-1">
                      {{ ticket.subject }}
                    </div>
                  </div>
                </td>

                <!-- User -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <img
                        class="h-8 w-8 rounded-full"
                        :src="ticket.user.avatar"
                        :alt="ticket.user.name"
                      >
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900">
                        {{ ticket.user.name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ ticket.user.email }}
                      </div>
                      <div class="mt-1">
                        <span
                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium capitalize"
                          :class="getUserRoleBadgeClass(ticket.user.role)"
                        >
                          {{ ticket.user.role }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Category -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div
                      class="p-1.5 rounded-lg bg-gray-100 text-gray-700 mr-2"
                    >
                      <span v-html="getCategoryIcon(ticket.category)"/>
                    </div>
                    <span class="text-sm text-gray-900 capitalize">{{
                      ticket.category
                    }}</span>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    class="px-2.5 py-1.5 inline-flex text-xs leading-5 font-medium rounded-full capitalize"
                    :class="getStatusClass(ticket.status)"
                  >
                    {{ ticket.status.replace("_", " ") }}
                  </span>
                </td>

                <!-- Priority -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    class="px-2.5 py-1.5 inline-flex text-xs leading-5 font-medium rounded-full capitalize"
                    :class="getPriorityClass(ticket.priority)"
                  >
                    {{ ticket.priority }}
                  </span>
                </td>

                <!-- Last Updated -->
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ getRelativeTime(ticket.updatedAt) }}
                </td>

                <!-- Assigned To -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <div v-if="ticket.assignedTo" class="flex items-center">
                    <div class="flex-shrink-0 h-6 w-6">
                      <img
                        class="h-6 w-6 rounded-full"
                        :src="ticket.assignedTo.avatar"
                        :alt="ticket.assignedTo.name"
                      >
                    </div>
                    <div class="ml-2 text-sm text-gray-900">
                      {{ ticket.assignedTo.name }}
                    </div>
                  </div>
                  <div v-else class="text-sm text-gray-500">Unassigned</div>
                </td>

                <!-- Actions -->
                <td
                  class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium"
                >
                  <button
                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                    @click.stop="viewTicketDetails(ticket)"
                  >
                    View
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="totalPages > 1"
          class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
        >
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
              @click="currentPage--"
            >
              Previous
            </button>
            <button
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              :class="{
                'opacity-50 cursor-not-allowed': currentPage === totalPages,
              }"
              @click="currentPage++"
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
                  Math.min(currentPage * perPage, filteredTickets.length)
                }}</span>
                of
                <span class="font-medium">{{ filteredTickets.length }}</span>
                results
              </p>
            </div>
            <div>
              <nav
                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                aria-label="Pagination"
              >
                <button
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  :class="{
                    'opacity-50 cursor-not-allowed': currentPage === 1,
                  }"
                  @click="currentPage--"
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
                <template v-for="page in totalPages" :key="page">
                  <button
                    v-if="
                      page === 1 ||
                      page === totalPages ||
                      Math.abs(page - currentPage) < 3
                    "
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="
                      currentPage === page
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                    "
                    @click="currentPage = page"
                  >
                    {{ page }}
                  </button>

                  <!-- Ellipsis -->
                  <span
                    v-else-if="
                      (page === 2 && currentPage > 4) ||
                      (page === totalPages - 1 && currentPage < totalPages - 3)
                    "
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                  >
                    ...
                  </span>
                </template>

                <button
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  :class="{
                    'opacity-50 cursor-not-allowed': currentPage === totalPages,
                  }"
                  @click="currentPage++"
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

    <!-- Ticket Details Modal -->
    <div
      v-if="showTicketDetails && selectedTicket"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <!-- Background overlay -->
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="closeTicketDetails"
        />

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
        >
          <!-- Header -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-start">
              <div>
                <h3
                  id="modal-title"
                  class="text-lg leading-6 font-medium text-gray-900"
                >
                  {{ selectedTicket.subject }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 flex items-center">
                  <span class="font-mono">{{ selectedTicket.id }}</span>
                  <span class="mx-2">•</span>
                  <span
                    >Created {{ formatDate(selectedTicket.createdAt) }}</span
                  >
                </p>
              </div>
              <button
                class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                @click="closeTicketDetails"
              >
                <span class="sr-only">Close</span>
                <svg
                  class="h-6 w-6"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
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
          <div class="bg-gray-50 px-4 py-5 sm:p-6 sm:py-5">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
              <!-- Messages & Replies (Left Column) -->
              <div class="lg:col-span-3 space-y-4">
                <!-- Conversation Timeline -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Conversation
                    </h3>
                  </div>

                  <div class="px-4 py-5 sm:p-6 space-y-6">
                    <!-- Original message -->
                    <div class="flex space-x-3">
                      <div class="flex-shrink-0">
                        <img
                          class="h-10 w-10 rounded-full"
                          :src="selectedTicket.user.avatar"
                          :alt="selectedTicket.user.name"
                        >
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-sm font-medium text-gray-900">
                              {{ selectedTicket.user.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                              {{ formatDate(selectedTicket.createdAt) }}
                            </p>
                          </div>
                          <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                            :class="
                              getUserRoleBadgeClass(selectedTicket.user.role)
                            "
                          >
                            {{ selectedTicket.user.role }}
                          </span>
                        </div>
                        <div class="mt-2 text-sm text-gray-700 space-y-4">
                          <p>{{ selectedTicket.description }}</p>
                        </div>

                        <!-- First message attachments -->
                        <div
                          v-if="
                            selectedTicket.messages[0]?.attachments?.length > 0
                          "
                          class="mt-4"
                        >
                          <h4
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2"
                          >
                            Attachments
                          </h4>
                          <div class="flex flex-wrap gap-4">
                            <div
                              v-for="attachment in selectedTicket.messages[0]
                                .attachments"
                              :key="attachment.id"
                              class="flex items-center p-3 border border-gray-200 rounded-lg bg-gray-50 max-w-xs"
                            >
                              <!-- File type icon based on mime type -->
                              <div class="flex-shrink-0 mr-3">
                                <div
                                  v-if="
                                    attachment.fileType.startsWith('image/')
                                  "
                                  class="h-10 w-10 bg-blue-100 flex items-center justify-center rounded-lg"
                                >
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
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                  </svg>
                                </div>
                                <div
                                  v-else-if="
                                    attachment.fileType === 'application/pdf'
                                  "
                                  class="h-10 w-10 bg-red-100 flex items-center justify-center rounded-lg"
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
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    />
                                  </svg>
                                </div>
                                <div
                                  v-else
                                  class="h-10 w-10 bg-gray-100 flex items-center justify-center rounded-lg"
                                >
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-gray-600"
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

                              <!-- File info -->
                              <div class="flex-1 min-w-0">
                                <p
                                  class="text-sm font-medium text-gray-900 truncate"
                                >
                                  {{ attachment.filename }}
                                </p>
                                <p class="text-xs text-gray-500">
                                  {{ formatFileSize(attachment.filesize) }}
                                </p>
                              </div>

                              <!-- Download button -->
                              <div v-if="attachment.url" class="ml-3">
                                <a
                                  :href="attachment.url"
                                  target="_blank"
                                  class="text-indigo-600 hover:text-indigo-900"
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
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Message thread (excluding first message which we showed above) -->
                    <div
                      v-for="(message, index) in selectedTicket.messages.slice(
                        1
                      )"
                      :key="message.id"
                      class="flex space-x-3"
                      :class="{ 'border-t border-gray-100 pt-6': index > 0 }"
                    >
                      <div class="flex-shrink-0">
                        <img
                          class="h-10 w-10 rounded-full"
                          :src="message.sender.avatar"
                          :alt="message.sender.name"
                        >
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-sm font-medium text-gray-900">
                              {{ message.sender.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                              {{ formatDate(message.timestamp) }}
                            </p>
                          </div>
                          <div class="flex items-center">
                            <span
                              v-if="message.isInternal"
                              class="inline-flex items-center mr-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                            >
                              Internal Note
                            </span>
                            <span
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                              :class="
                                getUserRoleBadgeClass(message.sender.role)
                              "
                            >
                              {{ message.sender.role }}
                            </span>
                          </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-700 space-y-4">
                          <p>{{ message.content }}</p>
                        </div>

                        <!-- Message attachments -->
                        <div
                          v-if="message.attachments?.length > 0"
                          class="mt-4"
                        >
                          <h4
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2"
                          >
                            Attachments
                          </h4>
                          <div class="flex flex-wrap gap-4">
                            <div
                              v-for="attachment in message.attachments"
                              :key="attachment.id"
                              class="flex items-center p-3 border border-gray-200 rounded-lg bg-gray-50 max-w-xs"
                            >
                              <!-- File type icon based on mime type -->
                              <div class="flex-shrink-0 mr-3">
                                <div
                                  v-if="
                                    attachment.fileType.startsWith('image/')
                                  "
                                  class="h-10 w-10 bg-blue-100 flex items-center justify-center rounded-lg"
                                >
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
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                  </svg>
                                </div>
                                <div
                                  v-else-if="
                                    attachment.fileType === 'application/pdf'
                                  "
                                  class="h-10 w-10 bg-red-100 flex items-center justify-center rounded-lg"
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
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    />
                                  </svg>
                                </div>
                                <div
                                  v-else
                                  class="h-10 w-10 bg-gray-100 flex items-center justify-center rounded-lg"
                                >
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-gray-600"
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

                              <!-- File info -->
                              <div class="flex-1 min-w-0">
                                <p
                                  class="text-sm font-medium text-gray-900 truncate"
                                >
                                  {{ attachment.filename }}
                                </p>
                                <p class="text-xs text-gray-500">
                                  {{ formatFileSize(attachment.filesize) }}
                                </p>
                              </div>

                              <!-- Download button -->
                              <div v-if="attachment.url" class="ml-3">
                                <a
                                  :href="attachment.url"
                                  target="_blank"
                                  class="text-indigo-600 hover:text-indigo-900"
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
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Reply Form -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Reply to Ticket
                    </h3>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div class="mb-4">
                      <label
                        for="reply-message"
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Your Message</label
                      >
                      <textarea
                        id="reply-message"
                        v-model="newReplyMessage"
                        rows="4"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :placeholder="`Reply to ${selectedTicket.user.name}...`"
                      />
                    </div>

                    <!-- File Attachments -->
                    <div class="mb-4">
                      <label
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Attachments</label
                      >
                      <div class="flex items-center">
                        <label
                          class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                          <span>Add files</span>
                          <input
                            type="file"
                            class="sr-only"
                            multiple
                            @change="handleFileUpload"
                          >
                        </label>
                        <p class="ml-3 text-xs text-gray-500">
                          Upload up to 5 files (max 10MB each)
                        </p>
                      </div>

                      <!-- File list -->
                      <div
                        v-if="ticketAttachments.length > 0"
                        class="mt-4 space-y-2"
                      >
                        <div
                          v-for="(file, index) in ticketAttachments"
                          :key="index"
                          class="flex items-center py-2 px-3 bg-gray-50 border border-gray-200 rounded-md"
                        >
                          <span class="truncate text-sm text-gray-500">{{
                            file.name
                          }}</span>
                          <span class="ml-2 text-xs text-gray-400">{{
                            formatFileSize(file.size)
                          }}</span>
                          <button
                            class="ml-auto text-gray-400 hover:text-gray-500"
                            @click="removeAttachment(index)"
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

                    <div class="flex justify-end">
                      <button
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        :disabled="!newReplyMessage.trim() || isSubmittingReply"
                        :class="{
                          'opacity-50 cursor-not-allowed':
                            !newReplyMessage.trim() || isSubmittingReply,
                        }"
                        @click="submitReply"
                      >
                        <span v-if="isSubmittingReply">
                          <svg
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
                            />
                            <path
                              class="opacity-75"
                              fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            />
                          </svg>
                          Sending...
                        </span>
                        <span v-else>
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                            />
                          </svg>
                          Send Reply
                        </span>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Internal Notes -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                  <div
                    class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-purple-50"
                  >
                    <h3 class="text-lg leading-6 font-medium text-purple-900">
                      Internal Notes
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-purple-700">
                      Only visible to admin team members
                    </p>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div class="mb-4">
                      <label
                        for="internal-note"
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Add Internal Note</label
                      >
                      <textarea
                        id="internal-note"
                        v-model="newInternalNote"
                        rows="3"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Add a note for the team..."
                      />
                    </div>

                    <div class="flex justify-end">
                      <button
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                        :disabled="!newInternalNote.trim() || isSubmittingNote"
                        :class="{
                          'opacity-50 cursor-not-allowed':
                            !newInternalNote.trim() || isSubmittingNote,
                        }"
                        @click="submitInternalNote"
                      >
                        <span v-if="isSubmittingNote">
                          <svg
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
                            />
                            <path
                              class="opacity-75"
                              fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            />
                          </svg>
                          Adding...
                        </span>
                        <span v-else>
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Add Note
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Ticket Information (Right Column) -->
              <div class="lg:col-span-1 space-y-6">
                <!-- Ticket Status -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Ticket Status
                    </h3>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                      <!-- Current Status -->
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Current Status</label
                        >
                        <span
                          class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium w-full justify-center capitalize"
                          :class="getStatusClass(selectedTicket.status)"
                        >
                          {{ selectedTicket.status.replace("_", " ") }}
                        </span>
                      </div>

                      <!-- Update Status -->
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Update Status</label
                        >
                        <div class="grid grid-cols-2 gap-2">
                          <button
                            v-for="status in statusOptions"
                            :key="status.id"
                            class="inline-flex items-center justify-center px-3 py-1.5 border rounded-md text-sm font-medium capitalize transition-colors duration-200"
                            :class="[
                              selectedTicket.status === status.id
                                ? 'border-gray-300 cursor-default ' +
                                  status.color
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50 hover:' +
                                  status.color,
                            ]"
                            @click="updateTicketStatus(status.id)"
                          >
                            {{ status.label }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Ticket Priority -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Priority
                    </h3>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                      <!-- Current Priority -->
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Current Priority</label
                        >
                        <span
                          class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium w-full justify-center capitalize"
                          :class="getPriorityClass(selectedTicket.priority)"
                        >
                          {{ selectedTicket.priority }}
                        </span>
                      </div>

                      <!-- Update Priority -->
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Update Priority</label
                        >
                        <div class="grid grid-cols-2 gap-2">
                          <button
                            v-for="priority in priorityOptions"
                            :key="priority.id"
                            class="inline-flex items-center justify-center px-3 py-1.5 border rounded-md text-sm font-medium capitalize transition-colors duration-200"
                            :class="[
                              selectedTicket.priority === priority.id
                                ? 'border-gray-300 cursor-default ' +
                                  priority.color
                                : 'border-gray-300 text-gray-700 hover:bg-gray-50 hover:' +
                                  priority.color,
                            ]"
                            @click="updateTicketPriority(priority.id)"
                          >
                            {{ priority.label }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Assignment -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Assignment
                    </h3>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                      <!-- Current Assignment -->
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Currently Assigned To</label
                        >
                        <div
                          v-if="selectedTicket.assignedTo"
                          class="flex items-center p-2 border border-gray-200 rounded-md"
                        >
                          <img
                            class="h-8 w-8 rounded-full object-cover"
                            :src="selectedTicket.assignedTo.avatar"
                            :alt="selectedTicket.assignedTo.name"
                          >
                          <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">
                              {{ selectedTicket.assignedTo.name }}
                            </p>
                            <p class="text-xs text-gray-500">
                              {{ selectedTicket.assignedTo.email }}
                            </p>
                          </div>
                        </div>
                        <div
                          v-else
                          class="text-sm text-gray-500 p-2 border border-gray-200 rounded-md"
                        >
                          No one is currently assigned to this ticket
                        </div>
                      </div>

                      <!-- Update Assignment -->
                      <div>
                        <label
                          for="assign-to"
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Assign To</label
                        >
                        <select
                          id="assign-to"
                          class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                          @change="assignTicket($event.target.value)"
                        >
                          <option value="" disabled selected>
                            Change assignment
                          </option>
                          <option
                            v-for="admin in adminList"
                            :key="admin.id"
                            :value="admin.id"
                          >
                            {{ admin.name }}
                          </option>
                          <option value="">Unassign</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Related Information -->
                <div
                  v-if="selectedTicket.relatedEntityType"
                  class="bg-white shadow rounded-lg overflow-hidden"
                >
                  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Related Information
                    </h3>
                  </div>
                  <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Related To</label
                        >
                        <div class="p-2 border border-gray-200 rounded-md">
                          <div class="flex items-center">
                            <div
                              v-if="
                                selectedTicket.relatedEntityType === 'course'
                              "
                              class="p-1.5 bg-blue-100 rounded-md mr-2"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-blue-700"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                                />
                              </svg>
                            </div>
                            <div
                              v-else-if="
                                selectedTicket.relatedEntityType === 'project'
                              "
                              class="p-1.5 bg-green-100 rounded-md mr-2"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-green-700"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <div
                              v-else-if="
                                selectedTicket.relatedEntityType === 'payment'
                              "
                              class="p-1.5 bg-yellow-100 rounded-md mr-2"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-yellow-700"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <div
                              v-else-if="
                                selectedTicket.relatedEntityType ===
                                'consultation'
                              "
                              class="p-1.5 bg-purple-100 rounded-md mr-2"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-purple-700"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z"
                                  clip-rule="evenodd"
                                />
                              </svg>
                            </div>
                            <div
                              v-else
                              class="p-1.5 bg-gray-100 rounded-md mr-2"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-700"
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
                            <div>
                              <span
                                class="block text-sm font-medium text-gray-900 capitalize"
                                >{{ selectedTicket.relatedEntityType }}</span
                              >
                              <span class="block text-xs text-gray-500"
                                >ID: {{ selectedTicket.relatedEntityId }}</span
                              >
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Tags -->
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Tags</label
                        >
                        <div class="flex flex-wrap gap-2">
                          <span
                            v-for="(tag, tagIndex) in selectedTicket.tags"
                            :key="tagIndex"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                          >
                            {{ tag }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div
      v-if="toast.show"
      class="fixed bottom-4 right-4 w-72 bg-white rounded-lg shadow-lg overflow-hidden border-l-4 transition-all duration-500 transform translate-x-0 opacity-100 z-50"
      :class="{
        'border-green-500': toast.type === 'success',
        'border-red-500': toast.type === 'error',
        'border-yellow-500': toast.type === 'warning',
        'border-blue-500': toast.type === 'info',
      }"
    >
      <div class="p-4">
        <div class="flex items-start">
          <!-- Success Icon -->
          <div v-if="toast.type === 'success'" class="flex-shrink-0">
            <svg
              class="h-6 w-6 text-green-500"
              xmlns="http://www.w3.org/2000/svg"
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
          <!-- Error Icon -->
          <div v-if="toast.type === 'error'" class="flex-shrink-0">
            <svg
              class="h-6 w-6 text-red-500"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <!-- Warning Icon -->
          <div v-if="toast.type === 'warning'" class="flex-shrink-0">
            <svg
              class="h-6 w-6 text-yellow-500"
              xmlns="http://www.w3.org/2000/svg"
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
          <!-- Info Icon -->
          <div v-if="toast.type === 'info'" class="flex-shrink-0">
            <svg
              class="h-6 w-6 text-blue-500"
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
            <p class="text-sm font-medium text-gray-900">{{ toast.message }}</p>
          </div>

          <div class="ml-4 flex-shrink-0 flex">
            <button
              class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
              @click="toast.show = false"
            >
              <span class="sr-only">Close</span>
              <svg
                class="h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
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
</template>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Hide scrollbars for tabs but keep functionality */
.no-scrollbar {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

.no-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

/* Animation transitions */
.animate-slide-left {
  animation: slideLeft 0.3s ease-in-out;
}

.animate-slide-right {
  animation: slideRight 0.3s ease-in-out;
}

.animate-fade {
  animation: fade 0.3s ease-in-out;
}

.animate-zoom-in {
  animation: zoomIn 0.3s ease-in-out;
}

.animate-zoom-out {
  animation: zoomOut 0.3s ease-in-out;
}

@keyframes slideLeft {
  0% {
    transform: translateX(0);
    opacity: 1;
  }
  50% {
    opacity: 0;
    transform: translateX(-20px);
  }
  51% {
    transform: translateX(20px);
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slideRight {
  0% {
    transform: translateX(0);
    opacity: 1;
  }
  50% {
    opacity: 0;
    transform: translateX(20px);
  }
  51% {
    transform: translateX(-20px);
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fade {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@keyframes zoomIn {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    opacity: 0;
    transform: scale(0.9);
  }
  51% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes zoomOut {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    opacity: 0;
    transform: scale(1.1);
  }
  51% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
