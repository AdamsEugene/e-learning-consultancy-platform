<!-- pages/dashboard/consultant/time-tracking/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watchEffect } from "vue";
import { useRoute, useRouter } from "vue-router";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Time Tracking - Consultant Dashboard",
});

// Router instance
const router = useRouter();

// Define time entry types
type TimeEntryStatus =
  | "billable"
  | "non-billable"
  | "pending-approval"
  | "approved"
  | "rejected";
type TimeEntryType =
  | "development"
  | "meeting"
  | "research"
  | "consulting"
  | "design"
  | "documentation"
  | "other";

interface Project {
  id: number;
  name: string;
  client: {
    id: number;
    name: string;
    avatar: string;
  };
  color: string;
  rate: number;
  status: "active" | "completed" | "on-hold";
}

interface TimeEntry {
  id: number;
  project: Project;
  description: string;
  date: string;
  startTime?: string;
  endTime?: string;
  duration: number; // in minutes
  status: TimeEntryStatus;
  type: TimeEntryType;
  billable: boolean;
  amount?: number;
  notes?: string;
  tags?: string[];
  invoiced: boolean;
  invoiceId?: number;
}

// State variables
const timeEntries = ref<TimeEntry[]>([]);
const projects = ref<Project[]>([]);
const isLoading = ref(true);
const activeTab = ref("today");
const searchQuery = ref("");
const selectedTimeEntry = ref<TimeEntry | null>(null);
const showTimeEntryDetailModal = ref(false);
const showDeleteModal = ref(false);
const showAddEntryModal = ref(false);
const isTimerRunning = ref(false);
const currentTimerProject = ref<Project | null>(null);
const currentTimerDescription = ref("");
const currentTimerStartTime = ref<Date | null>(null);
const elapsedTime = ref(0);
const timerInterval = ref<number | null>(null);
const currentDate = ref(new Date().toISOString().split("T")[0]);
const datePickerOpen = ref(false);
const groupByProject = ref(false);
const showApprovalPrompt = ref(false);
const approvalNote = ref("");

// Filter options
const dateRange = ref({
  start: new Date(new Date().setDate(new Date().getDate() - 7))
    .toISOString()
    .split("T")[0],
  end: new Date().toISOString().split("T")[0],
});
const selectedProjects = ref<number[]>([]);
const selectedTypes = ref<TimeEntryType[]>([]);
const selectedStatus = ref<TimeEntryStatus[]>([]);

// New time entry form
const newTimeEntry = ref({
  projectId: 0,
  description: "",
  date: new Date().toISOString().split("T")[0],
  startTime: "",
  endTime: "",
  duration: 0,
  type: "development" as TimeEntryType,
  billable: true,
  notes: "",
  tags: [] as string[],
});

// Time tracking stats
const timeTrackingStats = ref({
  totalHoursToday: 0,
  totalHoursWeek: 0,
  totalHoursMonth: 0,
  billableHoursToday: 0,
  billableHoursWeek: 0,
  billableHoursMonth: 0,
  utilization: 0,
  pendingApproval: 0,
  pendingInvoice: 0,
});

// Tags for entries
const availableTags = ref([
  "Frontend",
  "Backend",
  "API",
  "Database",
  "Testing",
  "UI/UX",
  "DevOps",
  "Meeting",
  "Planning",
  "Debugging",
  "Code Review",
  "Research",
  "Documentation",
]);

// Helper functions
const formatTime = (minutes: number): string => {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${hours}h ${mins}m`;
};

const formatDecimalHours = (minutes: number): string => {
  return (minutes / 60).toFixed(1);
};

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(amount);
};

const formatDate = (dateString: string): string => {
  const options: Intl.DateTimeFormatOptions = {
    weekday: "short",
    year: "numeric",
    month: "short",
    day: "numeric",
  };
  return new Date(dateString).toLocaleDateString("en-US", options);
};

const formatTimeOfDay = (timeString?: string): string => {
  if (!timeString) return "";
  return new Date(`2000-01-01T${timeString}`).toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

const isToday = (dateString: string): boolean => {
  const today = new Date();
  const date = new Date(dateString);
  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  );
};

const isThisWeek = (dateString: string): boolean => {
  const today = new Date();
  const date = new Date(dateString);
  const diffTime = Math.abs(today.getTime() - date.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays <= 7;
};

const isThisMonth = (dateString: string): boolean => {
  const today = new Date();
  const date = new Date(dateString);
  return (
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  );
};

// Get time entry type color and icon
const getEntryTypeInfo = (type: TimeEntryType) => {
  switch (type) {
    case "development":
      return {
        color: "bg-blue-100 text-blue-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>`,
      };
    case "meeting":
      return {
        color: "bg-purple-100 text-purple-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
              </svg>`,
      };
    case "research":
      return {
        color: "bg-yellow-100 text-yellow-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
              </svg>`,
      };
    case "consulting":
      return {
        color: "bg-green-100 text-green-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5A1 1 0 1110 9h1a2 2 0 012 2v1a1 1 0 01-1 1h-3a1 1 0 100 2h3a3 3 0 003-3v-1a4 4 0 00-4-4h-1.637A3.003 3.003 0 018 5zm2 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
              </svg>`,
      };
    case "design":
      return {
        color: "bg-pink-100 text-pink-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
              </svg>`,
      };
    case "documentation":
      return {
        color: "bg-indigo-100 text-indigo-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
              </svg>`,
      };
    default:
      return {
        color: "bg-gray-100 text-gray-800",
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5A1 1 0 1110 9h1a2 2 0 012 2v1a1 1 0 01-1 1h-3a1 1 0 100 2h3a3 3 0 003-3v-1a4 4 0 00-4-4h-1.637A3.003 3.003 0 018 5zm2 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
              </svg>`,
      };
  }
};

// Get status badge color
const getStatusBadgeClasses = (status: TimeEntryStatus) => {
  switch (status) {
    case "billable":
      return "bg-green-100 text-green-800";
    case "non-billable":
      return "bg-gray-100 text-gray-800";
    case "pending-approval":
      return "bg-yellow-100 text-yellow-800";
    case "approved":
      return "bg-blue-100 text-blue-800";
    case "rejected":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format status display
const formatStatus = (status: TimeEntryStatus): string => {
  return status.replace("-", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Generate mock data for time entries and projects
const generateMockData = () => {
  // Mock projects
  const mockProjects: Project[] = [
    {
      id: 1,
      name: "E-commerce Website Redesign",
      client: {
        id: 101,
        name: "Retail Innovations Inc.",
        avatar: "/api/placeholder/40/40",
      },
      color: "#4F46E5", // indigo-600
      rate: 125,
      status: "active",
    },
    {
      id: 2,
      name: "Mobile Banking App",
      client: {
        id: 102,
        name: "Financial Services Group",
        avatar: "/api/placeholder/40/40",
      },
      color: "#059669", // emerald-600
      rate: 150,
      status: "active",
    },
    {
      id: 3,
      name: "CRM System Integration",
      client: {
        id: 103,
        name: "Global Marketing Solutions",
        avatar: "/api/placeholder/40/40",
      },
      color: "#D97706", // amber-600
      rate: 135,
      status: "active",
    },
    {
      id: 4,
      name: "Healthcare Portal",
      client: {
        id: 104,
        name: "MediCare Providers",
        avatar: "/api/placeholder/40/40",
      },
      color: "#DC2626", // red-600
      rate: 140,
      status: "on-hold",
    },
    {
      id: 5,
      name: "Analytics Dashboard",
      client: {
        id: 105,
        name: "Data Insights Co.",
        avatar: "/api/placeholder/40/40",
      },
      color: "#7C3AED", // violet-600
      rate: 130,
      status: "completed",
    },
  ];

  // Generate time entries for the past 30 days
  const mockTimeEntries: TimeEntry[] = [];
  const today = new Date();

  // Helper to generate random time entries
  const generateRandomTimeEntry = (date: Date, id: number): TimeEntry => {
    const project =
      mockProjects[Math.floor(Math.random() * mockProjects.length)];
    const types: TimeEntryType[] = [
      "development",
      "meeting",
      "research",
      "consulting",
      "design",
      "documentation",
      "other",
    ];
    const type = types[Math.floor(Math.random() * types.length)];
    const statuses: TimeEntryStatus[] = [
      "billable",
      "non-billable",
      "pending-approval",
      "approved",
      "rejected",
    ];
    const status = statuses[Math.floor(Math.random() * statuses.length)];
    const duration = Math.floor(Math.random() * 240) + 30; // 30 minutes to 4.5 hours
    const billable =
      status === "billable" ||
      status === "approved" ||
      status === "pending-approval";

    // Start time (between 8 AM and 5 PM)
    const startHour = 8 + Math.floor(Math.random() * 9);
    const startMinute = Math.floor(Math.random() * 4) * 15; // 0, 15, 30, or 45 minutes
    const startTime = `${startHour.toString().padStart(2, "0")}:${startMinute
      .toString()
      .padStart(2, "0")}:00`;

    // Calculate end time
    const endDate = new Date(date);
    endDate.setHours(startHour);
    endDate.setMinutes(startMinute + duration);
    const endHour = endDate.getHours();
    const endMinute = endDate.getMinutes();
    const endTime = `${endHour.toString().padStart(2, "0")}:${endMinute
      .toString()
      .padStart(2, "0")}:00`;

    // Random tags
    const allTags = availableTags.value;
    const numTags = Math.floor(Math.random() * 3) + 1; // 1-3 tags
    const tags: string[] = [];
    for (let i = 0; i < numTags; i++) {
      const randomTag = allTags[Math.floor(Math.random() * allTags.length)];
      if (!tags.includes(randomTag)) {
        tags.push(randomTag);
      }
    }

    // Description options based on type
    const descriptions: Record<TimeEntryType, string[]> = {
      development: [
        "Implementing user authentication module",
        "Fixing responsive layout issues",
        "Creating API endpoints for data sync",
        "Code refactoring and optimization",
        "Setting up CI/CD pipeline",
      ],
      meeting: [
        "Weekly client status update",
        "Project kickoff meeting",
        "Requirements gathering session",
        "Sprint planning meeting",
        "Technical discussion with team",
      ],
      research: [
        "Investigating new framework options",
        "Security best practices research",
        "Performance optimization techniques",
        "Competitor analysis",
        "Exploring third-party integration options",
      ],
      consulting: [
        "Technical architecture consultation",
        "Solution design discussion",
        "Strategy planning with client",
        "Technology stack recommendation",
        "Process improvement consultation",
      ],
      design: [
        "UI component design",
        "Creating wireframes for new features",
        "User flow optimization",
        "Design system updates",
        "Creating mockups for client review",
      ],
      documentation: [
        "API documentation updates",
        "Creating user guides",
        "Writing technical specifications",
        "Code documentation",
        "Process documentation for client",
      ],
      other: [
        "Administrative tasks",
        "DevOps infrastructure setup",
        "Environment configuration",
        "Team mentoring",
        "Learning new technology",
      ],
    };

    const description =
      descriptions[type][Math.floor(Math.random() * descriptions[type].length)];

    return {
      id,
      project,
      description,
      date: date.toISOString().split("T")[0],
      startTime,
      endTime,
      duration,
      status,
      type,
      billable,
      amount: billable ? (duration / 60) * project.rate : 0,
      notes:
        Math.random() > 0.7
          ? "Some additional notes about this time entry."
          : undefined,
      tags,
      invoiced: status === "approved" && Math.random() > 0.5,
      invoiceId:
        status === "approved" && Math.random() > 0.5
          ? Math.floor(Math.random() * 1000) + 1000
          : undefined,
    };
  };

  // Generate entries for each day
  let id = 1;
  for (let i = 0; i < 30; i++) {
    const date = new Date(today);
    date.setDate(date.getDate() - i);

    // Generate 1-4 entries for each day
    const numEntries = Math.floor(Math.random() * 4) + 1;
    for (let j = 0; j < numEntries; j++) {
      mockTimeEntries.push(generateRandomTimeEntry(date, id++));
    }
  }

  // Add a running timer for today
  if (Math.random() > 0.5) {
    const project =
      mockProjects[Math.floor(Math.random() * mockProjects.length)];
    const startTime = new Date();
    startTime.setHours(startTime.getHours() - 1); // Started 1 hour ago

    currentTimerProject.value = project;
    currentTimerDescription.value = "Working on new feature implementation";
    currentTimerStartTime.value = startTime;
    isTimerRunning.value = true;

    // Start the timer
    startTimer();
  }

  return { projects: mockProjects, timeEntries: mockTimeEntries };
};

// Fetch time entries and projects (mock data)
const fetchTimeEntryData = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    const mockData = generateMockData();
    projects.value = mockData.projects;
    timeEntries.value = mockData.timeEntries;

    // Calculate stats
    calculateStats();
  } catch (error) {
    console.error("Error fetching time entries:", error);
  } finally {
    isLoading.value = false;
  }
};

// Calculate time tracking stats
const calculateStats = () => {
  const today = new Date().toISOString().split("T")[0];
  const entries = timeEntries.value;

  // Total hours today
  const todayEntries = entries.filter((entry) => entry.date === today);
  const todayMinutes = todayEntries.reduce(
    (sum, entry) => sum + entry.duration,
    0
  );
  timeTrackingStats.value.totalHoursToday = todayMinutes / 60;

  // Billable hours today
  const todayBillableMinutes = todayEntries
    .filter((entry) => entry.billable)
    .reduce((sum, entry) => sum + entry.duration, 0);
  timeTrackingStats.value.billableHoursToday = todayBillableMinutes / 60;

  // Calculate week hours
  const weekEntries = entries.filter((entry) => isThisWeek(entry.date));
  const weekMinutes = weekEntries.reduce(
    (sum, entry) => sum + entry.duration,
    0
  );
  timeTrackingStats.value.totalHoursWeek = weekMinutes / 60;

  // Billable hours week
  const weekBillableMinutes = weekEntries
    .filter((entry) => entry.billable)
    .reduce((sum, entry) => sum + entry.duration, 0);
  timeTrackingStats.value.billableHoursWeek = weekBillableMinutes / 60;

  // Calculate month hours
  const monthEntries = entries.filter((entry) => isThisMonth(entry.date));
  const monthMinutes = monthEntries.reduce(
    (sum, entry) => sum + entry.duration,
    0
  );
  timeTrackingStats.value.totalHoursMonth = monthMinutes / 60;

  // Billable hours month
  const monthBillableMinutes = monthEntries
    .filter((entry) => entry.billable)
    .reduce((sum, entry) => sum + entry.duration, 0);
  timeTrackingStats.value.billableHoursMonth = monthBillableMinutes / 60;

  // Utilization (billable hours / total hours for the month)
  timeTrackingStats.value.utilization =
    monthMinutes > 0 ? (monthBillableMinutes / monthMinutes) * 100 : 0;

  // Pending approval count
  timeTrackingStats.value.pendingApproval = entries.filter(
    (entry) => entry.status === "pending-approval"
  ).length;

  // Pending invoice count
  timeTrackingStats.value.pendingInvoice = entries.filter(
    (entry) => entry.status === "approved" && !entry.invoiced
  ).length;
};

// Filter time entries based on active tab, date range, and search
const filteredTimeEntries = computed(() => {
  let result = [...timeEntries.value];

  // Apply date filter based on active tab
  if (activeTab.value === "today") {
    const today = new Date().toISOString().split("T")[0];
    result = result.filter((entry) => entry.date === today);
  } else if (activeTab.value === "week") {
    result = result.filter((entry) => isThisWeek(entry.date));
  } else if (activeTab.value === "month") {
    result = result.filter((entry) => isThisMonth(entry.date));
  } else if (activeTab.value === "custom") {
    result = result.filter(
      (entry) =>
        entry.date >= dateRange.value.start && entry.date <= dateRange.value.end
    );
  }

  // Apply project filter
  if (selectedProjects.value.length > 0) {
    result = result.filter((entry) =>
      selectedProjects.value.includes(entry.project.id)
    );
  }

  // Apply type filter
  if (selectedTypes.value.length > 0) {
    result = result.filter((entry) => selectedTypes.value.includes(entry.type));
  }

  // Apply status filter
  if (selectedStatus.value.length > 0) {
    result = result.filter((entry) =>
      selectedStatus.value.includes(entry.status)
    );
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (entry) =>
        entry.description.toLowerCase().includes(query) ||
        entry.project.name.toLowerCase().includes(query) ||
        entry.project.client.name.toLowerCase().includes(query) ||
        (entry.tags &&
          entry.tags.some((tag) => tag.toLowerCase().includes(query)))
    );
  }

  // Sort by date (newest first) and then by start time
  result.sort((a, b) => {
    if (a.date !== b.date) {
      return b.date.localeCompare(a.date);
    }

    if (a.startTime && b.startTime) {
      return a.startTime.localeCompare(b.startTime);
    }

    return 0;
  });

  return result;
});

// Group entries by date
const entriesByDate = computed(() => {
  const grouped: Record<string, TimeEntry[]> = {};

  filteredTimeEntries.value.forEach((entry) => {
    if (!grouped[entry.date]) {
      grouped[entry.date] = [];
    }
    grouped[entry.date].push(entry);
  });

  return grouped;
});

// Group entries by project
const entriesByProject = computed(() => {
  const grouped: Record<string, TimeEntry[]> = {};

  filteredTimeEntries.value.forEach((entry) => {
    const projectId = entry.project.id.toString();
    if (!grouped[projectId]) {
      grouped[projectId] = [];
    }
    grouped[projectId].push(entry);
  });

  return grouped;
});

// Calculate total time for date groups
const totalTimeByDate = computed(() => {
  const totals: Record<
    string,
    { duration: number; billableDuration: number; amount: number }
  > = {};

  Object.entries(entriesByDate.value).forEach(([date, entries]) => {
    const duration = entries.reduce((sum, entry) => sum + entry.duration, 0);
    const billableDuration = entries
      .filter((entry) => entry.billable)
      .reduce((sum, entry) => sum + entry.duration, 0);
    const amount = entries
      .filter((entry) => entry.billable)
      .reduce((sum, entry) => sum + (entry.amount || 0), 0);

    totals[date] = { duration, billableDuration, amount };
  });

  return totals;
});

// Calculate total time for project groups
const totalTimeByProject = computed(() => {
  const totals: Record<
    string,
    { duration: number; billableDuration: number; amount: number }
  > = {};

  Object.entries(entriesByProject.value).forEach(([projectId, entries]) => {
    const duration = entries.reduce((sum, entry) => sum + entry.duration, 0);
    const billableDuration = entries
      .filter((entry) => entry.billable)
      .reduce((sum, entry) => sum + entry.duration, 0);
    const amount = entries
      .filter((entry) => entry.billable)
      .reduce((sum, entry) => sum + (entry.amount || 0), 0);

    totals[projectId] = { duration, billableDuration, amount };
  });

  return totals;
});

// Overall totals for filtered entries
const filteredTotals = computed(() => {
  const entries = filteredTimeEntries.value;
  const duration = entries.reduce((sum, entry) => sum + entry.duration, 0);
  const billableDuration = entries
    .filter((entry) => entry.billable)
    .reduce((sum, entry) => sum + entry.duration, 0);
  const amount = entries
    .filter((entry) => entry.billable)
    .reduce((sum, entry) => sum + (entry.amount || 0), 0);

  return { duration, billableDuration, amount };
});

// Get project by ID
const getProjectById = (id: number) => {
  return projects.value.find((project) => project.id === id);
};

// Timer functions
const startTimer = () => {
  if (!currentTimerProject.value) return;

  // Set start time if not already set
  if (!currentTimerStartTime.value) {
    currentTimerStartTime.value = new Date();
  }

  isTimerRunning.value = true;

  // Update elapsed time every second
  timerInterval.value = window.setInterval(() => {
    if (!currentTimerStartTime.value) return;

    const now = new Date();
    const diffMs = now.getTime() - currentTimerStartTime.value.getTime();
    elapsedTime.value = Math.floor(diffMs / 1000 / 60); // Convert to minutes
  }, 1000);
};

const pauseTimer = () => {
  if (timerInterval.value !== null) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }

  isTimerRunning.value = false;
};

const stopAndSaveTimer = () => {
  pauseTimer();

  if (!currentTimerProject.value || !currentTimerStartTime.value) return;

  // Calculate duration
  const now = new Date();
  const diffMs = now.getTime() - currentTimerStartTime.value.getTime();
  const durationMinutes = Math.max(1, Math.floor(diffMs / 1000 / 60)); // At least 1 minute

  // Format times
  const formatTimeComponent = (date: Date) => {
    return {
      hours: date.getHours().toString().padStart(2, "0"),
      minutes: date.getMinutes().toString().padStart(2, "0"),
      seconds: date.getSeconds().toString().padStart(2, "0"),
    };
  };

  const startComponents = formatTimeComponent(currentTimerStartTime.value);
  const endComponents = formatTimeComponent(now);

  const startTime = `${startComponents.hours}:${startComponents.minutes}:${startComponents.seconds}`;
  const endTime = `${endComponents.hours}:${endComponents.minutes}:${endComponents.seconds}`;

  // Create new time entry
  const newId = Math.max(...timeEntries.value.map((e) => e.id)) + 1;
  const today = new Date().toISOString().split("T")[0];

  const newEntry: TimeEntry = {
    id: newId,
    project: currentTimerProject.value,
    description: currentTimerDescription.value,
    date: today,
    startTime,
    endTime,
    duration: durationMinutes,
    status: "billable",
    type: "development", // Default type
    billable: true,
    amount: (durationMinutes / 60) * currentTimerProject.value.rate,
    invoiced: false,
  };

  // Add to time entries
  timeEntries.value.push(newEntry);

  // Reset timer
  currentTimerProject.value = null;
  currentTimerDescription.value = "";
  currentTimerStartTime.value = null;
  elapsedTime.value = 0;

  // Recalculate stats
  calculateStats();
};

const discardTimer = () => {
  pauseTimer();

  // Reset timer
  currentTimerProject.value = null;
  currentTimerDescription.value = "";
  currentTimerStartTime.value = null;
  elapsedTime.value = 0;
};

// View time entry details
const viewTimeEntryDetails = (entry: TimeEntry) => {
  selectedTimeEntry.value = entry;
  showTimeEntryDetailModal.value = true;
};

// Delete time entry
const deleteTimeEntry = () => {
  if (!selectedTimeEntry.value) return;

  const id = selectedTimeEntry.value.id;
  timeEntries.value = timeEntries.value.filter((entry) => entry.id !== id);

  showTimeEntryDetailModal.value = false;
  showDeleteModal.value = false;
  selectedTimeEntry.value = null;

  // Recalculate stats
  calculateStats();
};

// Add new time entry
const addTimeEntry = () => {
  if (
    !newTimeEntry.value.projectId ||
    !newTimeEntry.value.description ||
    !newTimeEntry.value.date ||
    !newTimeEntry.value.duration
  ) {
    // Show validation error
    return;
  }

  const project = getProjectById(newTimeEntry.value.projectId);
  if (!project) return;

  // Generate entry ID
  const newId = Math.max(...timeEntries.value.map((e) => e.id)) + 1;

  let startTime, endTime;

  // If start and end times are provided, use them
  if (newTimeEntry.value.startTime && newTimeEntry.value.endTime) {
    startTime = newTimeEntry.value.startTime + ":00";
    endTime = newTimeEntry.value.endTime + ":00";
  }

  // Create new entry
  const newEntry: TimeEntry = {
    id: newId,
    project,
    description: newTimeEntry.value.description,
    date: newTimeEntry.value.date,
    startTime,
    endTime,
    duration: newTimeEntry.value.duration,
    status: newTimeEntry.value.billable ? "billable" : "non-billable",
    type: newTimeEntry.value.type,
    billable: newTimeEntry.value.billable,
    amount: newTimeEntry.value.billable
      ? (newTimeEntry.value.duration / 60) * project.rate
      : 0,
    notes: newTimeEntry.value.notes,
    tags: newTimeEntry.value.tags,
    invoiced: false,
  };

  // Add to time entries
  timeEntries.value.push(newEntry);

  // Reset form
  newTimeEntry.value = {
    projectId: 0,
    description: "",
    date: new Date().toISOString().split("T")[0],
    startTime: "",
    endTime: "",
    duration: 0,
    type: "development",
    billable: true,
    notes: "",
    tags: [],
  };

  showAddEntryModal.value = false;

  // Recalculate stats
  calculateStats();
};

// Calculate duration from start and end times
const calculateDuration = () => {
  if (!newTimeEntry.value.startTime || !newTimeEntry.value.endTime) return;

  const startParts = newTimeEntry.value.startTime.split(":");
  const endParts = newTimeEntry.value.endTime.split(":");

  if (startParts.length < 2 || endParts.length < 2) return;

  const startDate = new Date();
  startDate.setHours(parseInt(startParts[0], 10));
  startDate.setMinutes(parseInt(startParts[1], 10));

  const endDate = new Date();
  endDate.setHours(parseInt(endParts[0], 10));
  endDate.setMinutes(parseInt(endParts[1], 10));

  // Handle case where end time is on the next day
  if (endDate < startDate) {
    endDate.setDate(endDate.getDate() + 1);
  }

  const diffMs = endDate.getTime() - startDate.getTime();
  const diffMinutes = Math.floor(diffMs / 1000 / 60);

  if (diffMinutes > 0) {
    newTimeEntry.value.duration = diffMinutes;
  }
};

// Submit time entries for approval
const submitForApproval = () => {
  // Get all billable entries that aren't already submitted or approved
  const entriesToSubmit = timeEntries.value.filter(
    (entry) =>
      entry.billable &&
      entry.status !== "pending-approval" &&
      entry.status !== "approved"
  );

  if (entriesToSubmit.length === 0) {
    // No entries to submit
    return;
  }

  // Update status of entries
  entriesToSubmit.forEach((entry) => {
    entry.status = "pending-approval";
  });

  showApprovalPrompt.value = true;

  // Recalculate stats
  calculateStats();
};

// Load data on component mount
onMounted(() => {
  fetchTimeEntryData();
});

// Clean up timer on component unmount
onBeforeUnmount(() => {
  if (timerInterval.value !== null) {
    clearInterval(timerInterval.value);
  }
});

// Watch for changes in time entry form
watchEffect(() => {
  if (newTimeEntry.value.startTime && newTimeEntry.value.endTime) {
    calculateDuration();
  }
});
</script>

<template>
  <div>
    <!-- Page Header with Stats -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Time Tracking</h1>
          <p class="text-gray-600">
            Track your billable hours, manage time entries, and view reports
          </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <button
            v-if="isTimerRunning"
            @click="stopAndSaveTimer"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
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
            Stop & Save ({{ formatTime(elapsedTime) }})
          </button>
          <button
            v-else
            @click="showAddEntryModal = true"
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
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                clip-rule="evenodd"
              />
            </svg>
            Add Time Entry
          </button>

          <button
            @click="submitForApproval"
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
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            Submit for Approval
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
        <!-- Today's Hours -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
          <div class="flex justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Today's Hours</p>
              <div class="flex items-end mt-1">
                <p class="text-2xl font-bold text-gray-900">
                  {{ timeTrackingStats.totalHoursToday.toFixed(1) }}
                </p>
                <p class="text-sm text-gray-500 ml-2 mb-0.5">
                  <span class="text-green-600 font-medium">{{
                    timeTrackingStats.billableHoursToday.toFixed(1)
                  }}</span>
                  billable
                </p>
              </div>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
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
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
          </div>

          <!-- Progress bar -->
          <div class="mt-4">
            <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
              <div
                class="bg-green-500 h-2.5"
                :style="`width: ${
                  timeTrackingStats.totalHoursToday > 0
                    ? (timeTrackingStats.billableHoursToday /
                        timeTrackingStats.totalHoursToday) *
                      100
                    : 0
                }%`"
              ></div>
            </div>
            <div class="flex justify-between mt-1 text-xs text-gray-500">
              <span
                >{{
                  Math.round(
                    timeTrackingStats.totalHoursToday > 0
                      ? (timeTrackingStats.billableHoursToday /
                          timeTrackingStats.totalHoursToday) *
                          100
                      : 0
                  )
                }}% billable</span
              >
              <span>Goal: 8 hours</span>
            </div>
          </div>
        </div>

        <!-- This Week / Month -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
          <div class="flex justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">This Week / Month</p>
              <div class="flex items-end mt-1">
                <p class="text-2xl font-bold text-gray-900">
                  {{ timeTrackingStats.totalHoursWeek.toFixed(1) }}
                </p>
                <p class="text-sm text-gray-500 ml-2 mb-0.5">
                  / {{ timeTrackingStats.totalHoursMonth.toFixed(1) }}
                </p>
              </div>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
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
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
          </div>

          <!-- Week/Month progress -->
          <div class="mt-4">
            <div class="flex justify-between mb-1">
              <span class="text-xs text-gray-500">This Week</span>
              <span class="text-xs text-gray-900 font-medium"
                >{{ timeTrackingStats.billableHoursWeek.toFixed(1) }} / 40
                hours</span
              >
            </div>
            <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
              <div
                class="bg-purple-500 h-2.5"
                :style="`width: ${Math.min(
                  100,
                  (timeTrackingStats.billableHoursWeek / 40) * 100
                )}%`"
              ></div>
            </div>

            <div class="flex justify-between mb-1 mt-3">
              <span class="text-xs text-gray-500">This Month</span>
              <span class="text-xs text-gray-900 font-medium"
                >{{ timeTrackingStats.billableHoursMonth.toFixed(1) }} / 160
                hours</span
              >
            </div>
            <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
              <div
                class="bg-indigo-500 h-2.5"
                :style="`width: ${Math.min(
                  100,
                  (timeTrackingStats.billableHoursMonth / 160) * 100
                )}%`"
              ></div>
            </div>
          </div>
        </div>

        <!-- Utilization & Pending -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
          <div class="flex justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Utilization Rate</p>
              <div class="flex items-end mt-1">
                <p class="text-2xl font-bold text-gray-900">
                  {{ Math.round(timeTrackingStats.utilization) }}%
                </p>
                <p class="text-sm text-gray-500 ml-2 mb-0.5">billable time</p>
              </div>
            </div>
            <div class="bg-green-100 rounded-full p-3">
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
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                />
              </svg>
            </div>
          </div>

          <!-- Pending items -->
          <div class="mt-4 space-y-3">
            <div
              v-if="timeTrackingStats.pendingApproval > 0"
              class="flex items-center justify-between px-4 py-2 bg-yellow-50 rounded-lg"
            >
              <div class="flex items-center text-yellow-800">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-sm font-medium"
                  >{{ timeTrackingStats.pendingApproval }} entries pending
                  approval</span
                >
              </div>
            </div>

            <div
              v-if="timeTrackingStats.pendingInvoice > 0"
              class="flex items-center justify-between px-4 py-2 bg-blue-50 rounded-lg"
            >
              <div class="flex items-center text-blue-800">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-sm font-medium"
                  >{{ timeTrackingStats.pendingInvoice }} approved entries not
                  invoiced</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Running Timer Alert -->
    <div
      v-if="isTimerRunning"
      class="mb-6 bg-white rounded-lg shadow-md border border-indigo-200 p-4"
    >
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center mb-4 sm:mb-0">
          <div
            class="h-10 w-10 rounded-full flex items-center justify-center mr-4"
            :style="`background-color: ${
              currentTimerProject?.color || '#4F46E5'
            }`"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-white"
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
            <div class="text-lg font-medium text-gray-900">
              Timer Running: {{ formatTime(elapsedTime) }}
            </div>
            <div class="text-sm text-gray-600">
              {{ currentTimerProject?.name }} - {{ currentTimerDescription }}
              <span class="mx-2">•</span>
              Started at
              {{
                currentTimerStartTime
                  ? new Date(currentTimerStartTime).toLocaleTimeString()
                  : ""
              }}
            </div>
          </div>
        </div>

        <div class="flex space-x-2">
          <button
            v-if="isTimerRunning"
            @click="pauseTimer"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            Pause
          </button>
          <button
            v-else
            @click="startTimer"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                clip-rule="evenodd"
              />
            </svg>
            Resume
          </button>
          <button
            @click="stopAndSaveTimer"
            class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
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
            Save
          </button>
          <button
            @click="discardTimer"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            Discard
          </button>
        </div>
      </div>
    </div>

    <!-- Filters and Controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Tab Navigation -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            @click="activeTab = 'today'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'today'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Today
          </button>
          <button
            @click="activeTab = 'week'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'week'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            This Week
          </button>
          <button
            @click="activeTab = 'month'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'month'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            This Month
          </button>
          <button
            @click="activeTab = 'custom'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'custom'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Custom Range
          </button>
        </div>

        <!-- Search and Group By -->
        <div class="flex items-center space-x-3">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search time entries..."
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

          <div class="flex items-center text-sm">
            <input
              id="group-by-project"
              v-model="groupByProject"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label for="group-by-project" class="ml-2 block text-gray-700">
              Group by project
            </label>
          </div>
        </div>
      </div>

      <!-- Date range selection (custom) -->
      <div
        v-if="activeTab === 'custom'"
        class="mt-3 pt-3 border-t border-gray-200"
      >
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex items-center space-x-2">
            <label
              for="start-date"
              class="block text-sm font-medium text-gray-700"
            >
              From:
            </label>
            <input
              id="start-date"
              v-model="dateRange.start"
              type="date"
              class="block w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <div class="flex items-center space-x-2">
            <label
              for="end-date"
              class="block text-sm font-medium text-gray-700"
            >
              To:
            </label>
            <input
              id="end-date"
              v-model="dateRange.end"
              type="date"
              class="block w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            />
          </div>

          <!-- Quick date range selections -->
          <div class="space-x-2">
            <button
              @click="
                dateRange = {
                  start: new Date(new Date().setDate(new Date().getDate() - 7))
                    .toISOString()
                    .split('T')[0],
                  end: new Date().toISOString().split('T')[0],
                }
              "
              class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Last 7 days
            </button>
            <button
              @click="
                dateRange = {
                  start: new Date(new Date().setDate(new Date().getDate() - 30))
                    .toISOString()
                    .split('T')[0],
                  end: new Date().toISOString().split('T')[0],
                }
              "
              class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Last 30 days
            </button>
            <button
              @click="
                dateRange = {
                  start: new Date(
                    new Date().getFullYear(),
                    new Date().getMonth(),
                    1
                  )
                    .toISOString()
                    .split('T')[0],
                  end: new Date().toISOString().split('T')[0],
                }
              "
              class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              This Month
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200 p-10 text-center"
    >
      <div
        class="inline-block animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-indigo-600"
      ></div>
      <p class="mt-4 text-gray-600">Loading your time entries...</p>
    </div>

    <!-- Time Entries List -->
    <div v-else-if="filteredTimeEntries.length > 0">
      <!-- Group by Date View -->
      <div v-if="!groupByProject">
        <div v-for="(entries, date) in entriesByDate" :key="date" class="mb-6">
          <!-- Date Header with Totals -->
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-2 mb-2 border-b border-gray-200"
          >
            <h3 class="text-lg font-semibold text-gray-900">
              {{ formatDate(date) }}
              <span
                v-if="isToday(date)"
                class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 font-medium"
              >
                Today
              </span>
            </h3>

            <div class="mt-2 sm:mt-0 flex flex-wrap gap-2 sm:gap-4">
              <div class="text-sm text-gray-700">
                <span class="font-medium">Total:</span>
                {{ formatTime(totalTimeByDate[date].duration) }}
              </div>
              <div class="text-sm text-green-700">
                <span class="font-medium">Billable:</span>
                {{ formatTime(totalTimeByDate[date].billableDuration) }}
              </div>
              <div class="text-sm text-indigo-700">
                <span class="font-medium">Amount:</span>
                {{ formatCurrency(totalTimeByDate[date].amount) }}
              </div>
            </div>
          </div>

          <!-- Entries for this date -->
          <div class="space-y-3">
            <div
              v-for="entry in entries"
              :key="entry.id"
              class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md transition-shadow"
            >
              <div
                class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0"
              >
                <!-- Project Color & Info -->
                <div class="flex flex-1 items-start space-x-3">
                  <div
                    class="w-2 h-full min-h-[2.5rem] rounded-full flex-shrink-0"
                    :style="`background-color: ${entry.project.color}`"
                  ></div>

                  <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                      <h4 class="font-medium text-gray-900 truncate">
                        {{ entry.description }}
                      </h4>

                      <!-- Status badge -->
                      <span
                        class="ml-2 px-2 py-0.5 text-xs rounded-full capitalize flex-shrink-0"
                        :class="getStatusBadgeClasses(entry.status)"
                      >
                        {{ formatStatus(entry.status) }}
                      </span>

                      <!-- Type badge -->
                      <span
                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                        :class="getEntryTypeInfo(entry.type).color"
                      >
                        <span
                          v-html="getEntryTypeInfo(entry.type).icon"
                          class="mr-1"
                        ></span>
                        {{ entry.type.replace("_", " ") }}
                      </span>
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                      <span class="font-medium">{{ entry.project.name }}</span>
                      <span class="mx-1">•</span>
                      <span>{{ entry.project.client.name }}</span>
                      <span v-if="entry.startTime" class="mx-1">•</span>
                      <span v-if="entry.startTime"
                        >{{ formatTimeOfDay(entry.startTime) }} -
                        {{ formatTimeOfDay(entry.endTime) }}</span
                      >
                    </div>

                    <!-- Tags -->
                    <div
                      v-if="entry.tags && entry.tags.length > 0"
                      class="mt-2 flex flex-wrap gap-1"
                    >
                      <span
                        v-for="(tag, tagIndex) in entry.tags"
                        :key="`${entry.id}-tag-${tagIndex}`"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                      >
                        # {{ tag }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Duration & Actions -->
                <div
                  class="flex items-center justify-between sm:justify-end space-x-4 sm:ml-4"
                >
                  <div class="text-center">
                    <div class="text-sm text-gray-500">Duration</div>
                    <div class="text-base font-medium text-gray-900">
                      {{ formatTime(entry.duration) }}
                    </div>
                  </div>

                  <div
                    v-if="entry.billable"
                    class="text-center hidden sm:block"
                  >
                    <div class="text-sm text-gray-500">Amount</div>
                    <div class="text-base font-medium text-green-600">
                      {{ formatCurrency(entry.amount || 0) }}
                    </div>
                  </div>

                  <div class="flex space-x-1">
                    <button
                      @click="viewTimeEntryDetails(entry)"
                      class="inline-flex items-center p-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      title="View Details"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
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

                    <button
                      v-if="
                        entry.status !== 'approved' &&
                        entry.status !== 'rejected'
                      "
                      @click="
                        selectedTimeEntry = entry;
                        showDeleteModal = true;
                      "
                      class="inline-flex items-center p-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      title="Delete Entry"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-red-600"
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
        </div>
      </div>

      <!-- Group by Project View -->
      <div v-else>
        <div
          v-for="(entries, projectId) in entriesByProject"
          :key="projectId"
          class="mb-6"
        >
          <!-- Project Header with Totals -->
          <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-2 mb-2 border-b border-gray-200"
          >
            <div class="flex items-center">
              <div
                class="w-5 h-5 rounded-full mr-2"
                :style="`background-color: ${entries[0].project.color}`"
              ></div>
              <h3 class="text-lg font-semibold text-gray-900">
                {{ entries[0].project.name }}
                <span class="ml-2 text-sm font-normal text-gray-600">
                  {{ entries[0].project.client.name }}
                </span>
              </h3>
            </div>

            <div class="mt-2 sm:mt-0 flex flex-wrap gap-2 sm:gap-4">
              <div class="text-sm text-gray-700">
                <span class="font-medium">Total:</span>
                {{ formatTime(totalTimeByProject[projectId].duration) }}
              </div>
              <div class="text-sm text-green-700">
                <span class="font-medium">Billable:</span>
                {{ formatTime(totalTimeByProject[projectId].billableDuration) }}
              </div>
              <div class="text-sm text-indigo-700">
                <span class="font-medium">Amount:</span>
                {{ formatCurrency(totalTimeByProject[projectId].amount) }}
              </div>
            </div>
          </div>

          <!-- Entries for this project -->
          <div class="space-y-3">
            <div
              v-for="entry in entries"
              :key="entry.id"
              class="bg-white rounded-lg shadow border border-gray-200 p-4 hover:shadow-md transition-shadow"
            >
              <div
                class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0"
              >
                <!-- Entry Info -->
                <div class="flex flex-1 items-start space-x-3">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                      <h4 class="font-medium text-gray-900 truncate">
                        {{ entry.description }}
                      </h4>

                      <!-- Status badge -->
                      <span
                        class="ml-2 px-2 py-0.5 text-xs rounded-full capitalize flex-shrink-0"
                        :class="getStatusBadgeClasses(entry.status)"
                      >
                        {{ formatStatus(entry.status) }}
                      </span>

                      <!-- Type badge -->
                      <span
                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                        :class="getEntryTypeInfo(entry.type).color"
                      >
                        <span
                          v-html="getEntryTypeInfo(entry.type).icon"
                          class="mr-1"
                        ></span>
                        {{ entry.type.replace("_", " ") }}
                      </span>
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                      <span>{{ formatDate(entry.date) }}</span>
                      <span v-if="entry.startTime" class="mx-1">•</span>
                      <span v-if="entry.startTime"
                        >{{ formatTimeOfDay(entry.startTime) }} -
                        {{ formatTimeOfDay(entry.endTime) }}</span
                      >
                    </div>

                    <!-- Tags -->
                    <div
                      v-if="entry.tags && entry.tags.length > 0"
                      class="mt-2 flex flex-wrap gap-1"
                    >
                      <span
                        v-for="(tag, tagIndex) in entry.tags"
                        :key="`${entry.id}-tag-${tagIndex}`"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                      >
                        # {{ tag }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Duration & Actions -->
                <div
                  class="flex items-center justify-between sm:justify-end space-x-4 sm:ml-4"
                >
                  <div class="text-center">
                    <div class="text-sm text-gray-500">Duration</div>
                    <div class="text-base font-medium text-gray-900">
                      {{ formatTime(entry.duration) }}
                    </div>
                  </div>

                  <div
                    v-if="entry.billable"
                    class="text-center hidden sm:block"
                  >
                    <div class="text-sm text-gray-500">Amount</div>
                    <div class="text-base font-medium text-green-600">
                      {{ formatCurrency(entry.amount || 0) }}
                    </div>
                  </div>

                  <div class="flex space-x-1">
                    <button
                      @click="viewTimeEntryDetails(entry)"
                      class="inline-flex items-center p-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      title="View Details"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
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

                    <button
                      v-if="
                        entry.status !== 'approved' &&
                        entry.status !== 'rejected'
                      "
                      @click="
                        selectedTimeEntry = entry;
                        showDeleteModal = true;
                      "
                      class="inline-flex items-center p-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      title="Delete Entry"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-red-600"
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
        </div>
      </div>

      <!-- Summary footer -->
      <div class="mt-6 bg-white rounded-lg shadow border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row justify-between items-center">
          <div class="text-sm text-gray-600">
            Showing {{ filteredTimeEntries.length }} time
            {{ filteredTimeEntries.length === 1 ? "entry" : "entries" }}
          </div>
          <div class="mt-3 sm:mt-0 flex flex-wrap gap-3">
            <div class="text-sm">
              <span class="text-gray-600">Total Hours:</span>
              <span class="font-semibold text-gray-900">{{
                formatDecimalHours(filteredTotals.duration)
              }}</span>
            </div>
            <div class="text-sm">
              <span class="text-gray-600">Billable Hours:</span>
              <span class="font-semibold text-green-600">{{
                formatDecimalHours(filteredTotals.billableDuration)
              }}</span>
            </div>
            <div class="text-sm">
              <span class="text-gray-600">Total Amount:</span>
              <span class="font-semibold text-indigo-600">{{
                formatCurrency(filteredTotals.amount)
              }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
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
      <h3 class="text-lg font-medium text-gray-900 mb-2">
        No time entries found
      </h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No time entries match your search criteria."
            : "You haven't tracked any time yet."
        }}
      </p>
      <button
        @click="showAddEntryModal = true"
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
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
            clip-rule="evenodd"
          />
        </svg>
        Add Your First Time Entry
      </button>
    </div>

    <!-- Time Entry Detail Modal -->
    <div
      v-if="showTimeEntryDetailModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-2xl w-full overflow-hidden shadow-xl transform transition-all mx-4"
      >
        <div class="relative">
          <!-- Header with color based on project -->
          <div
            class="px-6 py-4"
            :style="`background-color: ${selectedTimeEntry?.project.color}`"
          >
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-bold text-white">
                  {{ selectedTimeEntry?.description }}
                </h3>
                <div class="flex items-center text-white text-opacity-90 mt-1">
                  <span>{{ selectedTimeEntry?.project.name }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ selectedTimeEntry?.project.client.name }}</span>
                </div>
              </div>
              <button
                @click="showTimeEntryDetailModal = false"
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

          <!-- Content -->
          <div class="px-6 py-4">
            <!-- Time and Date -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <span class="block text-sm font-medium text-gray-500"
                    >Date</span
                  >
                  <span class="text-gray-900">{{
                    formatDate(selectedTimeEntry?.date || "")
                  }}</span>
                </div>
                <div>
                  <span class="block text-sm font-medium text-gray-500"
                    >Time</span
                  >
                  <span class="text-gray-900">
                    {{
                      selectedTimeEntry?.startTime
                        ? `${formatTimeOfDay(
                            selectedTimeEntry.startTime
                          )} - ${formatTimeOfDay(selectedTimeEntry.endTime)}`
                        : "Not specified"
                    }}
                  </span>
                </div>
                <div>
                  <span class="block text-sm font-medium text-gray-500"
                    >Duration</span
                  >
                  <span class="text-gray-900">{{
                    formatTime(selectedTimeEntry?.duration || 0)
                  }}</span>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                  <span class="block text-sm font-medium text-gray-500"
                    >Status</span
                  >
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="
                      selectedTimeEntry
                        ? getStatusBadgeClasses(selectedTimeEntry.status)
                        : ''
                    "
                  >
                    {{
                      selectedTimeEntry
                        ? formatStatus(selectedTimeEntry.status)
                        : ""
                    }}
                  </span>
                </div>
                <div>
                  <span class="block text-sm font-medium text-gray-500"
                    >Type</span
                  >
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="
                      selectedTimeEntry
                        ? getEntryTypeInfo(selectedTimeEntry.type).color
                        : ''
                    "
                  >
                    <span
                      v-if="selectedTimeEntry"
                      v-html="getEntryTypeInfo(selectedTimeEntry.type).icon"
                      class="mr-1"
                    ></span>
                    {{
                      selectedTimeEntry
                        ? selectedTimeEntry.type.replace("_", " ")
                        : ""
                    }}
                  </span>
                </div>
                <div>
                  <span class="block text-sm font-medium text-gray-500"
                    >Billable</span
                  >
                  <span
                    v-if="selectedTimeEntry?.billable"
                    class="inline-flex items-center"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-green-500 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span class="text-green-700">
                      Yes ({{ formatCurrency(selectedTimeEntry?.amount || 0) }})
                    </span>
                  </span>
                  <span v-else class="inline-flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span class="text-gray-700">No</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="selectedTimeEntry?.notes" class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Notes</h4>
              <div class="bg-gray-50 p-3 rounded-md text-gray-700">
                {{ selectedTimeEntry.notes }}
              </div>
            </div>

            <!-- Tags -->
            <div
              v-if="
                selectedTimeEntry?.tags && selectedTimeEntry.tags.length > 0
              "
              class="mb-6"
            >
              <h4 class="text-sm font-medium text-gray-700 mb-2">Tags</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="(tag, tagIndex) in selectedTimeEntry.tags"
                  :key="`detail-tag-${tagIndex}`"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800"
                >
                  # {{ tag }}
                </span>
              </div>
            </div>

            <!-- Invoice info if applicable -->
            <div
              v-if="selectedTimeEntry?.invoiced"
              class="mb-6 p-3 bg-blue-50 rounded-lg"
            >
              <div class="flex items-start">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-blue-500 mr-2 mt-0.5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z"
                    clip-rule="evenodd"
                  />
                </svg>
                <div>
                  <h4 class="font-medium text-blue-800">Invoiced</h4>
                  <p class="text-sm text-blue-700">
                    This time entry has been invoiced - Invoice #{{
                      selectedTimeEntry.invoiceId
                    }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div
            class="px-6 py-3 bg-gray-50 flex flex-col-reverse sm:flex-row gap-3 sm:justify-between"
          >
            <div>
              <button
                v-if="
                  selectedTimeEntry?.status !== 'approved' &&
                  selectedTimeEntry?.status !== 'rejected'
                "
                @click="
                  selectedTimeEntry &&
                    ((showTimeEntryDetailModal = false),
                    (showDeleteModal = true))
                "
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
                Delete Entry
              </button>
            </div>

            <div class="flex gap-3">
              <button
                @click="showTimeEntryDetailModal = false"
                class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Close
              </button>

              <button
                v-if="selectedTimeEntry?.status !== 'approved'"
                @click="showTimeEntryDetailModal = false"
                class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                  />
                </svg>
                Edit Entry
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all mx-4"
      >
        <div class="px-6 pt-6 pb-4">
          <div class="flex items-center justify-center mb-4">
            <div class="bg-red-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-red-600"
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
          </div>
          <h3 class="text-xl font-medium text-gray-900 text-center mb-2">
            Delete Time Entry
          </h3>
          <p class="text-gray-600 text-center mb-6">
            Are you sure you want to delete this time entry? This action cannot
            be undone.
          </p>

          <!-- Entry details summary -->
          <div class="bg-gray-50 p-3 rounded-lg mb-6">
            <div class="text-sm text-gray-700">
              <div>
                <span class="font-medium">Project:</span>
                {{ selectedTimeEntry?.project.name }}
              </div>
              <div>
                <span class="font-medium">Description:</span>
                {{ selectedTimeEntry?.description }}
              </div>
              <div>
                <span class="font-medium">Date:</span>
                {{ formatDate(selectedTimeEntry?.date || "") }}
              </div>
              <div>
                <span class="font-medium">Duration:</span>
                {{ formatTime(selectedTimeEntry?.duration || 0) }}
              </div>
            </div>
          </div>
        </div>

        <div
          class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row justify-between gap-3"
        >
          <button
            @click="showDeleteModal = false"
            class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>

          <button
            @click="deleteTimeEntry"
            class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            Delete Entry
          </button>
        </div>
      </div>
    </div>

    <!-- Add Time Entry Modal -->
    <div
      v-if="showAddEntryModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full overflow-hidden shadow-xl transform transition-all mx-4"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Add Time Entry</h3>
            <button
              @click="showAddEntryModal = false"
              class="text-gray-400 hover:text-gray-500"
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

        <!-- Form -->
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Project Selection -->
            <div>
              <label
                for="project"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Project *
              </label>
              <select
                id="project"
                v-model="newTimeEntry.projectId"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
                <option value="0" disabled>Select a project</option>
                <option
                  v-for="project in projects"
                  :key="project.id"
                  :value="project.id"
                >
                  {{ project.name }} ({{ project.client.name }})
                </option>
              </select>
            </div>

            <!-- Description -->
            <div>
              <label
                for="description"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Description *
              </label>
              <input
                id="description"
                v-model="newTimeEntry.description"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="What did you work on?"
                required
              />
            </div>

            <!-- Date -->
            <div>
              <label
                for="date"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Date *
              </label>
              <input
                id="date"
                v-model="newTimeEntry.date"
                type="date"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              />
            </div>

            <!-- Entry Type -->
            <div>
              <label
                for="type"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Type *
              </label>
              <select
                id="type"
                v-model="newTimeEntry.type"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              >
                <option value="development">Development</option>
                <option value="meeting">Meeting</option>
                <option value="research">Research</option>
                <option value="consulting">Consulting</option>
                <option value="design">Design</option>
                <option value="documentation">Documentation</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <!-- Time tracking -->
          <div class="mt-4 border-t border-gray-200 pt-4">
            <div class="mb-2 flex items-center justify-between">
              <span class="block text-sm font-medium text-gray-700"
                >Time Tracking</span
              >
              <span class="text-sm text-gray-500">
                {{ formatTime(newTimeEntry.duration) }}
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label
                  for="start-time"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Start Time
                </label>
                <input
                  id="start-time"
                  v-model="newTimeEntry.startTime"
                  type="time"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="09:00"
                />
              </div>

              <div>
                <label
                  for="end-time"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  End Time
                </label>
                <input
                  id="end-time"
                  v-model="newTimeEntry.endTime"
                  type="time"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="17:00"
                />
              </div>

              <div>
                <label
                  for="duration"
                  class="block text-sm font-medium text-gray-700 mb-1"
                >
                  Duration (minutes) *
                </label>
                <input
                  id="duration"
                  v-model="newTimeEntry.duration"
                  type="number"
                  min="1"
                  step="1"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="60"
                  required
                />
              </div>
            </div>
          </div>

          <!-- Additional Options -->
          <div class="mt-4 border-t border-gray-200 pt-4">
            <div class="flex items-center mb-4">
              <input
                id="billable"
                v-model="newTimeEntry.billable"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="billable" class="ml-2 block text-sm text-gray-700">
                Billable
              </label>
            </div>

            <!-- Tags -->
            <div class="mb-4">
              <label
                for="tags"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Tags
              </label>
              <div class="flex flex-wrap gap-2 mb-2">
                <span
                  v-for="(tag, i) in newTimeEntry.tags"
                  :key="`new-tag-${i}`"
                  class="inline-flex items-center rounded-md bg-indigo-100 px-2.5 py-0.5 text-sm font-medium text-indigo-800"
                >
                  {{ tag }}
                  <button
                    type="button"
                    @click="newTimeEntry.tags.splice(i, 1)"
                    class="ml-1 inline-flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full text-indigo-600 hover:bg-indigo-200 hover:text-indigo-500 focus:bg-indigo-500 focus:text-white focus:outline-none"
                  >
                    <svg
                      class="h-2 w-2"
                      stroke="currentColor"
                      fill="none"
                      viewBox="0 0 8 8"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-width="1.5"
                        d="M1 1l6 6m0-6L1 7"
                      />
                    </svg>
                  </button>
                </span>
              </div>
              <div class="flex">
                <select
                  id="tag-select"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  @change="
                    $event.target.value !== '' &&
                    !newTimeEntry.tags.includes($event.target.value)
                      ? newTimeEntry.tags.push($event.target.value)
                      : null;
                    $event.target.value = '';
                  "
                >
                  <option value="">Select a tag</option>
                  <option
                    v-for="tag in availableTags.filter(
                      (tag) => !newTimeEntry.tags.includes(tag)
                    )"
                    :key="tag"
                    :value="tag"
                  >
                    {{ tag }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Notes -->
            <div>
              <label
                for="notes"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Notes
              </label>
              <textarea
                id="notes"
                v-model="newTimeEntry.notes"
                rows="3"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Add any additional notes here..."
              ></textarea>
            </div>
          </div>

          <!-- Or start a timer -->
          <div
            v-if="!isTimerRunning"
            class="mt-6 pt-4 border-t border-gray-200"
          >
            <button
              type="button"
              class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                  clip-rule="evenodd"
                />
              </svg>
              Start a timer instead
            </button>
            <p class="mt-1 text-xs text-gray-500">
              Use a timer to track your time as you work. The timer will
              continue running until you stop it.
            </p>
          </div>
        </div>

        <!-- Form actions -->
        <div
          class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row justify-between gap-3"
        >
          <button
            @click="showAddEntryModal = false"
            class="mt-3 sm:mt-0 inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>

          <button
            @click="addTimeEntry"
            class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
            Add Time Entry
          </button>
        </div>
      </div>
    </div>

    <!-- Submit for Approval Success Message -->
    <div
      v-if="showApprovalPrompt"
      class="fixed bottom-4 right-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-md max-w-sm z-50"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-green-500"
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
          <h3 class="text-sm font-medium text-green-800">Success</h3>
          <div class="mt-1 text-sm text-green-700">
            Your time entries have been submitted for approval.
          </div>
          <div class="mt-2">
            <button
              @click="showApprovalPrompt = false"
              class="text-sm font-medium text-green-800 hover:text-green-700"
            >
              Dismiss
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9; /* gray-100 */
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1; /* gray-300 */
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1; /* indigo-500 */
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
