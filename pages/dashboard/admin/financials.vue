<!-- pages/dashboard/admin/financials/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Set the layout
definePageMeta({
  layout: "dashboard-admin",
});

// Set page meta
useHead({
  title: "Financial Management - Admin Dashboard",
  meta: [
    {
      name: "description",
      content:
        "Manage platform finances, review transactions, and generate reports",
    },
  ],
});

// Types
interface Transaction {
  id: number;
  date: string;
  user: {
    id: number;
    name: string;
    email: string;
    type: "student" | "client" | "instructor" | "consultant";
    avatar: string;
  };
  description: string;
  amount: number;
  status: "completed" | "pending" | "failed" | "refunded";
  paymentMethod: string;
  category:
    | "course"
    | "subscription"
    | "consultancy"
    | "withdrawal"
    | "refund"
    | "fee";
  courseId?: number;
  courseName?: string;
  projectId?: number;
  projectName?: string;
  invoiceId?: string;
}

interface RevenueData {
  month: string;
  courses: number;
  consultancy: number;
  subscriptions: number;
  total: number;
}

interface CategoryBreakdown {
  category: string;
  amount: number;
  percentage: number;
  color: string;
}

// State variables
const isLoading = ref(true);
const dateRange = ref("month");
const statusFilter = ref("all");
const categoryFilter = ref("all");
const searchQuery = ref("");
const sortBy = ref("date-desc");
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Mock data - in a real app, this would come from an API
const transactions = ref<Transaction[]>([]);
const revenueData = ref<RevenueData[]>([]);

// Fetch data (mock)
const fetchData = async () => {
  isLoading.value = true;

  try {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mock transactions
    transactions.value = [
      {
        id: 1001,
        date: "2025-04-15T14:30:00",
        user: {
          id: 101,
          name: "John Doe",
          email: "john.doe@example.com",
          type: "student",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Course purchase: "Advanced Web Development"',
        amount: 89.99,
        status: "completed",
        paymentMethod: "Credit Card",
        category: "course",
        courseId: 1,
        courseName: "Advanced Web Development",
      },
      {
        id: 1002,
        date: "2025-04-14T10:15:00",
        user: {
          id: 102,
          name: "Emma Wilson",
          email: "emma.wilson@example.com",
          type: "client",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Consultancy services for project "E-commerce Platform"',
        amount: 1250.0,
        status: "completed",
        paymentMethod: "Bank Transfer",
        category: "consultancy",
        projectId: 201,
        projectName: "E-commerce Platform",
        invoiceId: "INV-2025-1002",
      },
      {
        id: 1003,
        date: "2025-04-14T08:45:00",
        user: {
          id: 103,
          name: "Alex Johnson",
          email: "alex.johnson@example.com",
          type: "instructor",
          avatar: "/api/placeholder/40/40",
        },
        description: "Instructor payout for course sales",
        amount: -450.25,
        status: "completed",
        paymentMethod: "Direct Deposit",
        category: "withdrawal",
        courseId: 3,
        courseName: "UI/UX Design Masterclass",
      },
      {
        id: 1004,
        date: "2025-04-13T16:20:00",
        user: {
          id: 104,
          name: "Sarah Parker",
          email: "sarah.parker@example.com",
          type: "consultant",
          avatar: "/api/placeholder/40/40",
        },
        description: "Consultant payout for project completion",
        amount: -850.75,
        status: "pending",
        paymentMethod: "PayPal",
        category: "withdrawal",
        projectId: 202,
        projectName: "CRM Integration",
        invoiceId: "INV-2025-1004",
      },
      {
        id: 1005,
        date: "2025-04-12T09:30:00",
        user: {
          id: 105,
          name: "Michael Roberts",
          email: "michael.roberts@example.com",
          type: "client",
          avatar: "/api/placeholder/40/40",
        },
        description: "Monthly subscription - Premium plan",
        amount: 49.99,
        status: "completed",
        paymentMethod: "Credit Card",
        category: "subscription",
      },
      {
        id: 1006,
        date: "2025-04-11T14:10:00",
        user: {
          id: 106,
          name: "Lisa Thompson",
          email: "lisa.thompson@example.com",
          type: "student",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Refund for course "Python Programming"',
        amount: -79.99,
        status: "completed",
        paymentMethod: "Credit Card Refund",
        category: "refund",
        courseId: 5,
        courseName: "Python Programming",
      },
      {
        id: 1007,
        date: "2025-04-10T11:05:00",
        user: {
          id: 107,
          name: "David Wilson",
          email: "david.wilson@example.com",
          type: "student",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Course purchase: "Data Science Fundamentals"',
        amount: 99.99,
        status: "failed",
        paymentMethod: "Credit Card",
        category: "course",
        courseId: 6,
        courseName: "Data Science Fundamentals",
      },
      {
        id: 1008,
        date: "2025-04-10T09:45:00",
        user: {
          id: 108,
          name: "Jessica Baker",
          email: "jessica.baker@example.com",
          type: "student",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Course purchase: "Mobile App Development"',
        amount: 79.99,
        status: "completed",
        paymentMethod: "PayPal",
        category: "course",
        courseId: 7,
        courseName: "Mobile App Development",
      },
      {
        id: 1009,
        date: "2025-04-09T16:30:00",
        user: {
          id: 109,
          name: "Robert Chen",
          email: "robert.chen@example.com",
          type: "client",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Consultancy services for project "Data Migration"',
        amount: 1800.0,
        status: "completed",
        paymentMethod: "Bank Transfer",
        category: "consultancy",
        projectId: 203,
        projectName: "Data Migration",
        invoiceId: "INV-2025-1009",
      },
      {
        id: 1010,
        date: "2025-04-08T14:20:00",
        user: {
          id: 110,
          name: "Amanda Lewis",
          email: "amanda.lewis@example.com",
          type: "consultant",
          avatar: "/api/placeholder/40/40",
        },
        description: "Platform fee for consultant services",
        amount: 195.0,
        status: "completed",
        paymentMethod: "Automatic",
        category: "fee",
        projectId: 204,
        projectName: "Web Application Redesign",
      },
      {
        id: 1011,
        date: "2025-04-08T10:15:00",
        user: {
          id: 111,
          name: "Chris Martin",
          email: "chris.martin@example.com",
          type: "instructor",
          avatar: "/api/placeholder/40/40",
        },
        description: "Platform fee for course sales",
        amount: 120.5,
        status: "completed",
        paymentMethod: "Automatic",
        category: "fee",
        courseId: 8,
        courseName: "Cloud Computing Essentials",
      },
      {
        id: 1012,
        date: "2025-04-07T13:40:00",
        user: {
          id: 112,
          name: "Thomas Brown",
          email: "thomas.brown@example.com",
          type: "student",
          avatar: "/api/placeholder/40/40",
        },
        description: 'Course purchase: "Digital Marketing Strategies"',
        amount: 69.99,
        status: "completed",
        paymentMethod: "Credit Card",
        category: "course",
        courseId: 9,
        courseName: "Digital Marketing Strategies",
      },
    ];

    // Mock monthly revenue data
    revenueData.value = [
      {
        month: "Jan",
        courses: 42500,
        consultancy: 38750,
        subscriptions: 7500,
        total: 88750,
      },
      {
        month: "Feb",
        courses: 38750,
        consultancy: 45000,
        subscriptions: 8250,
        total: 92000,
      },
      {
        month: "Mar",
        courses: 46250,
        consultancy: 41250,
        subscriptions: 9000,
        total: 96500,
      },
      {
        month: "Apr",
        courses: 35000,
        consultancy: 52500,
        subscriptions: 8750,
        total: 96250,
      },
      {
        month: "May",
        courses: 41250,
        consultancy: 47500,
        subscriptions: 9250,
        total: 98000,
      },
      {
        month: "Jun",
        courses: 47500,
        consultancy: 42500,
        subscriptions: 9500,
        total: 99500,
      },
      {
        month: "Jul",
        courses: 51250,
        consultancy: 48750,
        subscriptions: 10000,
        total: 110000,
      },
      {
        month: "Aug",
        courses: 53750,
        consultancy: 46250,
        subscriptions: 10250,
        total: 110250,
      },
      {
        month: "Sep",
        courses: 48750,
        consultancy: 51250,
        subscriptions: 10500,
        total: 110500,
      },
      {
        month: "Oct",
        courses: 52500,
        consultancy: 57500,
        subscriptions: 11000,
        total: 121000,
      },
      {
        month: "Nov",
        courses: 47500,
        consultancy: 53750,
        subscriptions: 10750,
        total: 112000,
      },
      {
        month: "Dec",
        courses: 45000,
        consultancy: 48750,
        subscriptions: 10500,
        total: 104250,
      },
    ];
  } catch (error) {
    console.error("Error fetching finance data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Computed properties
const filteredTransactions = computed(() => {
  let result = [...transactions.value];

  // Apply status filter
  if (statusFilter.value !== "all") {
    result = result.filter(
      (transaction) => transaction.status === statusFilter.value
    );
  }

  // Apply category filter
  if (categoryFilter.value !== "all") {
    result = result.filter(
      (transaction) => transaction.category === categoryFilter.value
    );
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (transaction) =>
        transaction.description.toLowerCase().includes(query) ||
        transaction.user.name.toLowerCase().includes(query) ||
        transaction.user.email.toLowerCase().includes(query) ||
        (transaction.invoiceId &&
          transaction.invoiceId.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  switch (sortBy.value) {
    case "date-desc":
      result.sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
      );
      break;
    case "date-asc":
      result.sort(
        (a, b) => new Date(a.date).getTime() - new Date(b.date).getTime()
      );
      break;
    case "amount-desc":
      result.sort((a, b) => b.amount - a.amount);
      break;
    case "amount-asc":
      result.sort((a, b) => a.amount - b.amount);
      break;
    default:
      result.sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
      );
  }

  return result;
});

// Pagination
const paginatedTransactions = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  return filteredTransactions.value.slice(
    startIndex,
    startIndex + itemsPerPage.value
  );
});

const totalPages = computed(() => {
  return Math.ceil(filteredTransactions.value.length / itemsPerPage.value);
});

// Financial metrics
const financialMetrics = computed(() => {
  // Calculate total revenue (positive transactions)
  const totalRevenue = transactions.value
    .filter((t) => t.amount > 0)
    .reduce((sum, t) => sum + t.amount, 0);

  // Calculate total payouts (negative transactions)
  const totalPayouts = transactions.value
    .filter((t) => t.amount < 0 && t.category === "withdrawal")
    .reduce((sum, t) => sum + Math.abs(t.amount), 0);

  // Calculate total refunds (negative transactions)
  const totalRefunds = transactions.value
    .filter((t) => t.amount < 0 && t.category === "refund")
    .reduce((sum, t) => sum + Math.abs(t.amount), 0);

  // Calculate fees collected
  const totalFees = transactions.value
    .filter((t) => t.category === "fee")
    .reduce((sum, t) => sum + t.amount, 0);

  // Calculate net revenue
  const netRevenue = totalRevenue - totalPayouts - totalRefunds;

  return {
    totalRevenue,
    totalPayouts,
    totalRefunds,
    totalFees,
    netRevenue,
    transactionCount: transactions.value.length,
  };
});

// Category breakdown for pie chart
const categoryBreakdown = computed((): CategoryBreakdown[] => {
  const positiveTransactions = transactions.value.filter((t) => t.amount > 0);
  const totalAmount = positiveTransactions.reduce(
    (sum, t) => sum + t.amount,
    0
  );

  const categories = {
    course: { amount: 0, color: "bg-blue-500" },
    consultancy: { amount: 0, color: "bg-purple-500" },
    subscription: { amount: 0, color: "bg-green-500" },
    fee: { amount: 0, color: "bg-amber-500" },
  };

  // Calculate amount per category
  positiveTransactions.forEach((t) => {
    if (t.category in categories) {
      categories[t.category as keyof typeof categories].amount += t.amount;
    }
  });

  // Convert to array with percentages
  return Object.entries(categories)
    .map(([category, data]) => {
      return {
        category: getCategoryLabel(category),
        amount: data.amount,
        percentage: totalAmount > 0 ? (data.amount / totalAmount) * 100 : 0,
        color: data.color,
      };
    })
    .sort((a, b) => b.amount - a.amount);
});

// Helper functions
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(amount);
};

const formatPercentage = (value: number) => {
  return value.toFixed(1) + "%";
};

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case "completed":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "failed":
      return "bg-red-100 text-red-800";
    case "refunded":
      return "bg-gray-100 text-gray-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

const getCategoryLabel = (category: string) => {
  switch (category) {
    case "course":
      return "Course Sales";
    case "consultancy":
      return "Consultancy Services";
    case "subscription":
      return "Subscriptions";
    case "withdrawal":
      return "Payouts";
    case "refund":
      return "Refunds";
    case "fee":
      return "Platform Fees";
    default:
      return category.charAt(0).toUpperCase() + category.slice(1);
  }
};

const getUserTypeLabel = (type: string) => {
  switch (type) {
    case "student":
      return "Student";
    case "instructor":
      return "Instructor";
    case "client":
      return "Client";
    case "consultant":
      return "Consultant";
    default:
      return type.charAt(0).toUpperCase() + type.slice(1);
  }
};

const getUserTypeBadgeClass = (type: string) => {
  switch (type) {
    case "student":
      return "bg-blue-100 text-blue-800";
    case "instructor":
      return "bg-purple-100 text-purple-800";
    case "client":
      return "bg-green-100 text-green-800";
    case "consultant":
      return "bg-amber-100 text-amber-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Change page in pagination
const changePage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Reset filters
const resetFilters = () => {
  statusFilter.value = "all";
  categoryFilter.value = "all";
  searchQuery.value = "";
  sortBy.value = "date-desc";
  currentPage.value = 1;
};

// Export financial data
const exportData = (format: "csv" | "pdf" | "excel") => {
  // In a real application, this would trigger a download
  // For now, just show a message
  alert(`Exporting data in ${format.toUpperCase()} format...`);
};

// Generate a financial report
const generateReport = () => {
  // In a real application, this would generate a report
  // For now, just show a message
  alert("Generating financial report...");
};

// Fetch data when component mounts
onMounted(() => {
  fetchData();
});
</script>

<template>
  <div>
    <!-- Page header with summary cards -->
    <div class="mb-6">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Financial Management
          </h1>
          <p class="text-gray-600">
            Manage platform finances, review transactions, and generate reports
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
          <div class="relative">
            <button
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
              @click="generateReport"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Generate Report</span>
            </button>
          </div>
          <div class="relative">
            <button
              class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Export</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
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
              class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10"
            >
              <div class="py-1">
                <button
                  @click="exportData('csv')"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Export as CSV
                </button>
                <button
                  @click="exportData('excel')"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Export as Excel
                </button>
                <button
                  @click="exportData('pdf')"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Export as PDF
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial summary cards -->
      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4"
      >
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Revenue</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(financialMetrics.totalRevenue) }}
              </p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-blue-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                />
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Net Revenue -->
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Net Revenue</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(financialMetrics.netRevenue) }}
              </p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-green-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Payouts -->
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Payouts</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(financialMetrics.totalPayouts) }}
              </p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-purple-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
                <path
                  fill-rule="evenodd"
                  d="M3 6a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 14a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Refunds -->
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Refunds</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(financialMetrics.totalRefunds) }}
              </p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-red-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Platform Fees -->
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500 font-medium">Platform Fees</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(financialMetrics.totalFees) }}
              </p>
            </div>
            <div class="bg-amber-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-amber-600"
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
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue chart and breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Revenue Chart -->
      <div
        class="lg:col-span-2 bg-white rounded-lg shadow border border-gray-200 p-6"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Revenue Overview</h3>
          <div class="flex space-x-2">
            <button
              @click="dateRange = 'month'"
              class="px-3 py-1 text-sm font-medium rounded-md"
              :class="
                dateRange === 'month'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100'
              "
            >
              Monthly
            </button>
            <button
              @click="dateRange = 'quarter'"
              class="px-3 py-1 text-sm font-medium rounded-md"
              :class="
                dateRange === 'quarter'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100'
              "
            >
              Quarterly
            </button>
            <button
              @click="dateRange = 'year'"
              class="px-3 py-1 text-sm font-medium rounded-md"
              :class="
                dateRange === 'year'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100'
              "
            >
              Yearly
            </button>
          </div>
        </div>

        <!-- Chart placeholder - In a real app, this would be a chart component -->
        <div
          class="relative h-80 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center"
        >
          <!-- Placeholder bars for the chart -->
          <div class="flex items-end h-64 w-full px-6 space-x-4">
            <div
              v-for="(data, index) in revenueData"
              :key="index"
              class="flex-1 flex flex-col items-center"
            >
              <div class="w-full flex flex-col items-center space-y-0.5">
                <!-- Courses bar -->
                <div
                  class="w-full bg-blue-500 rounded-t"
                  :style="{ height: `${(data.courses / data.total) * 160}px` }"
                ></div>

                <!-- Consultancy bar -->
                <div
                  class="w-full bg-purple-500 rounded-t"
                  :style="{
                    height: `${(data.consultancy / data.total) * 160}px`,
                  }"
                ></div>

                <!-- Subscriptions bar -->
                <div
                  class="w-full bg-green-500 rounded-t"
                  :style="{
                    height: `${(data.subscriptions / data.total) * 160}px`,
                  }"
                ></div>
              </div>

              <!-- Month label -->
              <div class="text-xs text-gray-600 mt-2">{{ data.month }}</div>
            </div>
          </div>

          <!-- Overlay with loading message when loading -->
          <div
            v-if="isLoading"
            class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center"
          >
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
        </div>

        <!-- Chart legend -->
        <div class="flex items-center justify-center mt-4 space-x-6">
          <div class="flex items-center">
            <span class="h-3 w-3 bg-blue-500 rounded-sm mr-1"></span>
            <span class="text-sm text-gray-600">Courses</span>
          </div>
          <div class="flex items-center">
            <span class="h-3 w-3 bg-purple-500 rounded-sm mr-1"></span>
            <span class="text-sm text-gray-600">Consultancy</span>
          </div>
          <div class="flex items-center">
            <span class="h-3 w-3 bg-green-500 rounded-sm mr-1"></span>
            <span class="text-sm text-gray-600">Subscriptions</span>
          </div>
        </div>
      </div>

      <!-- Revenue Breakdown -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          Revenue Breakdown
        </h3>

        <!-- Pie chart placeholder -->
        <div class="relative h-48 mb-4">
          <!-- Simple pie chart visualization -->
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="relative w-32 h-32">
              <svg viewBox="0 0 100 100" class="w-full h-full">
                <!-- Create pie chart segments using SVG strokes -->
                <circle
                  cx="50"
                  cy="50"
                  r="40"
                  stroke-width="20"
                  stroke="currentColor"
                  stroke-dasharray="0 100"
                  stroke-dashoffset="25"
                  fill="none"
                  class="text-gray-200"
                ></circle>

                <!-- We'll use these percentages for the pie slices -->
                <circle
                  v-for="(category, index) in categoryBreakdown"
                  :key="index"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke-width="20"
                  :stroke="category.color.replace('bg-', 'text-')"
                  :stroke-dasharray="`${category.percentage} ${
                    100 - category.percentage
                  }`"
                  :stroke-dashoffset="
                    -1 *
                    categoryBreakdown
                      .slice(0, index)
                      .reduce((acc, cat) => acc + cat.percentage, 25)
                  "
                  fill="none"
                  class="transition-all duration-1000"
                ></circle>
              </svg>

              <!-- Center number showing total -->
              <div
                class="absolute inset-0 flex flex-col items-center justify-center"
              >
                <span class="text-2xl font-bold text-gray-700">
                  {{ formatCurrency(financialMetrics.totalRevenue) }}
                </span>
                <span class="text-xs text-gray-500">Total Revenue</span>
              </div>
            </div>
          </div>

          <!-- Overlay with loading message when loading -->
          <div
            v-if="isLoading"
            class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center"
          >
            <div
              class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
        </div>

        <!-- Category breakdown list -->
        <div class="space-y-3">
          <div
            v-for="(category, index) in categoryBreakdown"
            :key="index"
            class="flex items-center justify-between"
          >
            <div class="flex items-center">
              <span
                class="h-3 w-3 rounded-sm mr-2"
                :class="category.color"
              ></span>
              <span class="text-sm text-gray-600">{{ category.category }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="text-sm font-medium">{{
                formatCurrency(category.amount)
              }}</span>
              <span class="text-xs text-gray-500"
                >({{ formatPercentage(category.percentage) }})</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Transaction history -->
    <div
      class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
    >
      <div class="p-6 border-b border-gray-200">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between"
        >
          <h3 class="text-lg font-semibold text-gray-900 mb-2 md:mb-0">
            Transaction History
          </h3>
          <div
            class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2"
          >
            <!-- Search transactions -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search transactions..."
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full sm:w-auto"
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

            <!-- Status filter -->
            <select
              v-model="statusFilter"
              class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Statuses</option>
              <option value="completed">Completed</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
              <option value="refunded">Refunded</option>
            </select>

            <!-- Category filter -->
            <select
              v-model="categoryFilter"
              class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Categories</option>
              <option value="course">Course Sales</option>
              <option value="consultancy">Consultancy</option>
              <option value="subscription">Subscriptions</option>
              <option value="withdrawal">Payouts</option>
              <option value="refund">Refunds</option>
              <option value="fee">Platform Fees</option>
            </select>

            <!-- Reset filters button -->
            <button
              @click="resetFilters"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Transactions table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Date
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                User
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Description
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Amount
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
                Category
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
            <tr v-if="isLoading" class="animate-pulse">
              <td colspan="7" class="px-6 py-4">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
              </td>
            </tr>
            <tr v-else-if="paginatedTransactions.length === 0">
              <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                No transactions found. Try adjusting your filters.
              </td>
            </tr>
            <tr
              v-for="transaction in paginatedTransactions"
              :key="transaction.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <!-- Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(transaction.date) }}
              </td>

              <!-- User -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <img
                      :src="transaction.user.avatar"
                      :alt="transaction.user.name"
                      class="h-8 w-8 rounded-full object-cover"
                    />
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                      {{ transaction.user.name }}
                    </div>
                    <div class="flex items-center">
                      <div class="text-xs text-gray-500">
                        {{ transaction.user.email }}
                      </div>
                      <span
                        class="ml-1 px-1.5 py-0.5 text-xs rounded-full"
                        :class="getUserTypeBadgeClass(transaction.user.type)"
                      >
                        {{ getUserTypeLabel(transaction.user.type) }}
                      </span>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Description -->
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900">
                  {{ transaction.description }}
                </div>
                <div v-if="transaction.invoiceId" class="text-xs text-gray-500">
                  Invoice: {{ transaction.invoiceId }}
                </div>
              </td>

              <!-- Amount -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div
                  class="text-sm font-medium"
                  :class="
                    transaction.amount > 0 ? 'text-green-600' : 'text-red-600'
                  "
                >
                  {{ formatCurrency(transaction.amount) }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ transaction.paymentMethod }}
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 text-xs rounded-full"
                  :class="getStatusBadgeClass(transaction.status)"
                >
                  {{
                    transaction.status.charAt(0).toUpperCase() +
                    transaction.status.slice(1)
                  }}
                </span>
              </td>

              <!-- Category -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ getCategoryLabel(transaction.category) }}
              </td>

              <!-- Actions -->
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <button
                  class="text-indigo-600 hover:text-indigo-900 focus:outline-none"
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
        class="px-6 py-3 border-t border-gray-200 flex items-center justify-between"
      >
        <div class="text-sm text-gray-700 hidden sm:block">
          Showing
          <span class="font-medium">{{
            (currentPage - 1) * itemsPerPage + 1
          }}</span>
          to
          <span class="font-medium">{{
            Math.min(currentPage * itemsPerPage, filteredTransactions.length)
          }}</span>
          of
          <span class="font-medium">{{ filteredTransactions.length }}</span>
          results
        </div>
        <div class="flex space-x-2">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            :class="[
              'px-3 py-1 rounded-md',
              currentPage === 1
                ? 'text-gray-400 cursor-not-allowed'
                : 'text-gray-700 hover:bg-gray-100',
            ]"
          >
            Previous
          </button>

          <div class="hidden sm:flex">
            <template v-for="page in totalPages" :key="page">
              <button
                v-if="
                  page === 1 ||
                  page === totalPages ||
                  (page >= currentPage - 1 && page <= currentPage + 1)
                "
                @click="changePage(page)"
                :class="[
                  'px-3 py-1 rounded-md',
                  currentPage === page
                    ? 'bg-indigo-100 text-indigo-700 font-medium'
                    : 'text-gray-700 hover:bg-gray-100',
                ]"
              >
                {{ page }}
              </button>
              <span
                v-else-if="
                  (page === 2 && currentPage > 3) ||
                  (page === totalPages - 1 && currentPage < totalPages - 2)
                "
                class="px-3 py-1 text-gray-500"
              >
                ...
              </span>
            </template>
          </div>

          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            :class="[
              'px-3 py-1 rounded-md',
              currentPage === totalPages
                ? 'text-gray-400 cursor-not-allowed'
                : 'text-gray-700 hover:bg-gray-100',
            ]"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Bar chart colors */
.bg-blue-500 {
  background-color: #3b82f6;
}

.bg-purple-500 {
  background-color: #8b5cf6;
}

.bg-green-500 {
  background-color: #10b981;
}

/* Transform text to use those colors */
.text-blue-500 {
  color: #3b82f6;
}

.text-purple-500 {
  color: #8b5cf6;
}

.text-green-500 {
  color: #10b981;
}

.text-amber-500 {
  color: #f59e0b;
}
</style>
