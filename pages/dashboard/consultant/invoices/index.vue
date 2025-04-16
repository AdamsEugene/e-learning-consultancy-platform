<!-- pages/dashboard/consultant/invoices/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Invoices - Consultant Dashboard",
});

// Router instance
const router = useRouter();

// Define invoice types
type InvoiceStatus = "draft" | "sent" | "paid" | "overdue" | "cancelled";

interface InvoiceItem {
  id: number;
  description: string;
  quantity: number;
  rate: number;
  amount: number;
}

interface Invoice {
  id: number;
  invoiceNumber: string;
  clientName: string;
  clientCompany?: string;
  clientEmail: string;
  clientAvatar: string;
  issueDate: string;
  dueDate: string;
  items: InvoiceItem[];
  subtotal: number;
  taxRate: number;
  taxAmount: number;
  total: number;
  status: InvoiceStatus;
  notes?: string;
  paymentMethod?: string;
  projectId?: number;
  projectName?: string;
  paymentDate?: string;
}

// State for invoices
const invoices = ref<Invoice[]>([]);
const isLoading = ref(true);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("recent");
const selectedInvoice = ref<Invoice | null>(null);
const showInvoiceDetailModal = ref(false);
const showDeleteModal = ref(false);
const invoiceToDelete = ref<Invoice | null>(null);
const showDuplicateModal = ref(false);
const invoiceToDuplicate = ref<Invoice | null>(null);
const showRemindModal = ref(false);
const invoiceToRemind = ref<Invoice | null>(null);
const remindMessage = ref("");

// Create mock data
const generateMockInvoices = () => {
  const mockInvoices: Invoice[] = [
    {
      id: 1,
      invoiceNumber: "INV-2025-001",
      clientName: "John Smith",
      clientCompany: "Acme Corporation",
      clientEmail: "john.smith@acmecorp.com",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-04-01",
      dueDate: "2025-04-15",
      items: [
        {
          id: 1,
          description: "Web Development Consultancy",
          quantity: 20,
          rate: 150,
          amount: 3000,
        },
        {
          id: 2,
          description: "UI/UX Design Services",
          quantity: 10,
          rate: 120,
          amount: 1200,
        },
      ],
      subtotal: 4200,
      taxRate: 10,
      taxAmount: 420,
      total: 4620,
      status: "paid",
      paymentMethod: "Bank Transfer",
      projectId: 101,
      projectName: "Acme Website Redesign",
      paymentDate: "2025-04-10",
    },
    {
      id: 2,
      invoiceNumber: "INV-2025-002",
      clientName: "Sarah Johnson",
      clientCompany: "TechStart Inc.",
      clientEmail: "sarah.j@techstart.io",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-04-05",
      dueDate: "2025-04-19",
      items: [
        {
          id: 3,
          description: "API Development",
          quantity: 30,
          rate: 175,
          amount: 5250,
        },
        {
          id: 4,
          description: "Technical Documentation",
          quantity: 5,
          rate: 100,
          amount: 500,
        },
      ],
      subtotal: 5750,
      taxRate: 10,
      taxAmount: 575,
      total: 6325,
      status: "sent",
      projectId: 102,
      projectName: "TechStart Mobile App",
    },
    {
      id: 3,
      invoiceNumber: "INV-2025-003",
      clientName: "Michael Chen",
      clientCompany: "Innovate Solutions",
      clientEmail: "michael@innovatesolutions.com",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-04-10",
      dueDate: "2025-04-24",
      items: [
        {
          id: 5,
          description: "Database Architecture Consulting",
          quantity: 15,
          rate: 200,
          amount: 3000,
        },
      ],
      subtotal: 3000,
      taxRate: 10,
      taxAmount: 300,
      total: 3300,
      status: "draft",
      projectId: 103,
      projectName: "Database Migration Project",
    },
    {
      id: 4,
      invoiceNumber: "INV-2025-004",
      clientName: "Emily Williams",
      clientCompany: "Design Masters",
      clientEmail: "emily@designmasters.co",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-03-25",
      dueDate: "2025-04-08",
      items: [
        {
          id: 6,
          description: "UI Component Development",
          quantity: 25,
          rate: 160,
          amount: 4000,
        },
        {
          id: 7,
          description: "UX Research & Analysis",
          quantity: 8,
          rate: 140,
          amount: 1120,
        },
      ],
      subtotal: 5120,
      taxRate: 10,
      taxAmount: 512,
      total: 5632,
      status: "overdue",
      projectId: 104,
      projectName: "E-commerce Redesign",
    },
    {
      id: 5,
      invoiceNumber: "INV-2025-005",
      clientName: "Robert Taylor",
      clientCompany: "Global Retail",
      clientEmail: "rtaylor@globalretail.com",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-03-15",
      dueDate: "2025-03-29",
      items: [
        {
          id: 8,
          description: "E-commerce Integration",
          quantity: 40,
          rate: 165,
          amount: 6600,
        },
        {
          id: 9,
          description: "Performance Optimization",
          quantity: 10,
          rate: 175,
          amount: 1750,
        },
      ],
      subtotal: 8350,
      taxRate: 10,
      taxAmount: 835,
      total: 9185,
      status: "paid",
      paymentMethod: "Credit Card",
      projectId: 105,
      projectName: "Online Store Migration",
      paymentDate: "2025-03-27",
    },
    {
      id: 6,
      invoiceNumber: "INV-2025-006",
      clientName: "Jessica Lee",
      clientCompany: "Health Solutions",
      clientEmail: "jessica@healthsolutions.org",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-04-12",
      dueDate: "2025-04-26",
      items: [
        {
          id: 10,
          description: "Mobile App Development",
          quantity: 35,
          rate: 180,
          amount: 6300,
        },
      ],
      subtotal: 6300,
      taxRate: 10,
      taxAmount: 630,
      total: 6930,
      status: "draft",
      projectId: 106,
      projectName: "Health Tracking App",
    },
    {
      id: 7,
      invoiceNumber: "INV-2025-007",
      clientName: "David Brown",
      clientCompany: "Finance Pro",
      clientEmail: "david@financepro.com",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-03-20",
      dueDate: "2025-04-03",
      items: [
        {
          id: 11,
          description: "Security Audit & Implementation",
          quantity: 20,
          rate: 220,
          amount: 4400,
        },
        {
          id: 12,
          description: "System Architecture Review",
          quantity: 8,
          rate: 200,
          amount: 1600,
        },
      ],
      subtotal: 6000,
      taxRate: 10,
      taxAmount: 600,
      total: 6600,
      status: "cancelled",
      notes: "Project cancelled by client",
      projectId: 107,
      projectName: "Financial System Security",
    },
    {
      id: 8,
      invoiceNumber: "INV-2025-008",
      clientName: "Amanda Harris",
      clientCompany: "Marketing Experts",
      clientEmail: "amanda@marketingexperts.com",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-04-02",
      dueDate: "2025-04-16",
      items: [
        {
          id: 13,
          description: "Marketing Automation Implementation",
          quantity: 25,
          rate: 175,
          amount: 4375,
        },
        {
          id: 14,
          description: "Custom Integration Development",
          quantity: 15,
          rate: 190,
          amount: 2850,
        },
      ],
      subtotal: 7225,
      taxRate: 10,
      taxAmount: 722.5,
      total: 7947.5,
      status: "sent",
      projectId: 108,
      projectName: "Marketing Automation System",
    },
    {
      id: 9,
      invoiceNumber: "INV-2025-009",
      clientName: "Thomas Wilson",
      clientCompany: "Wilson & Associates",
      clientEmail: "thomas@wilsonassociates.com",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-03-10",
      dueDate: "2025-03-24",
      items: [
        {
          id: 15,
          description: "Legal Document Management System",
          quantity: 45,
          rate: 185,
          amount: 8325,
        },
      ],
      subtotal: 8325,
      taxRate: 10,
      taxAmount: 832.5,
      total: 9157.5,
      status: "paid",
      paymentMethod: "Bank Transfer",
      projectId: 109,
      projectName: "Legal Document System",
      paymentDate: "2025-03-22",
    },
    {
      id: 10,
      invoiceNumber: "INV-2025-010",
      clientName: "Lisa Brown",
      clientCompany: "Education First",
      clientEmail: "lisa@educationfirst.edu",
      clientAvatar: "/api/placeholder/40/40",
      issueDate: "2025-03-05",
      dueDate: "2025-03-19",
      items: [
        {
          id: 16,
          description: "E-Learning Platform Development",
          quantity: 60,
          rate: 170,
          amount: 10200,
        },
        {
          id: 17,
          description: "Learning Material Integration",
          quantity: 15,
          rate: 140,
          amount: 2100,
        },
      ],
      subtotal: 12300,
      taxRate: 10,
      taxAmount: 1230,
      total: 13530,
      status: "overdue",
      projectId: 110,
      projectName: "E-Learning Platform",
    },
  ];

  return mockInvoices;
};

// Date helper functions
const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = {
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  return new Date(dateString).toLocaleDateString("en-US", options);
};

const isOverdue = (invoice: Invoice) => {
  if (invoice.status === "paid" || invoice.status === "cancelled") return false;

  const today = new Date();
  const dueDate = new Date(invoice.dueDate);
  return today > dueDate;
};

// Fetch invoices (mock)
const fetchInvoices = async () => {
  try {
    isLoading.value = true;
    // Simulating API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    invoices.value = generateMockInvoices();

    // Check for overdue invoices and update status
    invoices.value.forEach((invoice) => {
      if (
        invoice.status !== "paid" &&
        invoice.status !== "cancelled" &&
        isOverdue(invoice)
      ) {
        invoice.status = "overdue";
      }
    });
  } catch (error) {
    console.error("Error fetching invoices:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter invoices based on active tab and search query
const filteredInvoices = computed(() => {
  let result = [...invoices.value];

  // Apply status filter based on active tab
  if (activeTab.value !== "all") {
    result = result.filter((invoice) => invoice.status === activeTab.value);
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (invoice) =>
        invoice.invoiceNumber.toLowerCase().includes(query) ||
        invoice.clientName.toLowerCase().includes(query) ||
        (invoice.clientCompany &&
          invoice.clientCompany.toLowerCase().includes(query)) ||
        (invoice.projectName &&
          invoice.projectName.toLowerCase().includes(query))
    );
  }

  // Apply sort
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.issueDate).getTime() - new Date(a.issueDate).getTime()
    );
  } else if (sortBy.value === "oldest") {
    result.sort(
      (a, b) =>
        new Date(a.issueDate).getTime() - new Date(b.issueDate).getTime()
    );
  } else if (sortBy.value === "due-date") {
    result.sort(
      (a, b) => new Date(a.dueDate).getTime() - new Date(b.dueDate).getTime()
    );
  } else if (sortBy.value === "amount-high") {
    result.sort((a, b) => b.total - a.total);
  } else if (sortBy.value === "amount-low") {
    result.sort((a, b) => a.total - b.total);
  } else if (sortBy.value === "client") {
    result.sort((a, b) => a.clientName.localeCompare(b.clientName));
  }

  return result;
});

// Invoice Statistics
const invoiceStats = computed(() => {
  const totalInvoices = invoices.value.length;
  const totalAmount = invoices.value.reduce(
    (sum, invoice) => sum + invoice.total,
    0
  );
  const paidAmount = invoices.value
    .filter((invoice) => invoice.status === "paid")
    .reduce((sum, invoice) => sum + invoice.total, 0);
  const outstandingAmount = invoices.value
    .filter((invoice) => ["sent", "overdue"].includes(invoice.status))
    .reduce((sum, invoice) => sum + invoice.total, 0);
  const overdueAmount = invoices.value
    .filter((invoice) => invoice.status === "overdue")
    .reduce((sum, invoice) => sum + invoice.total, 0);

  const paidInvoices = invoices.value.filter(
    (invoice) => invoice.status === "paid"
  ).length;
  const sentInvoices = invoices.value.filter(
    (invoice) => invoice.status === "sent"
  ).length;
  const draftInvoices = invoices.value.filter(
    (invoice) => invoice.status === "draft"
  ).length;
  const overdueInvoices = invoices.value.filter(
    (invoice) => invoice.status === "overdue"
  ).length;

  return {
    totalInvoices,
    totalAmount,
    paidAmount,
    outstandingAmount,
    overdueAmount,
    paidInvoices,
    sentInvoices,
    draftInvoices,
    overdueInvoices,
  };
});

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 2,
  }).format(amount);
};

// Get invoice status badge classes
const getInvoiceStatusClasses = (status: InvoiceStatus) => {
  switch (status) {
    case "draft":
      return "bg-gray-100 text-gray-800";
    case "sent":
      return "bg-blue-100 text-blue-800";
    case "paid":
      return "bg-green-100 text-green-800";
    case "overdue":
      return "bg-red-100 text-red-800";
    case "cancelled":
      return "bg-purple-100 text-purple-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// View invoice details
const viewInvoiceDetails = (invoice: Invoice) => {
  selectedInvoice.value = invoice;
  showInvoiceDetailModal.value = true;
};

// Confirm delete invoice
const confirmDeleteInvoice = (invoice: Invoice) => {
  invoiceToDelete.value = invoice;
  showDeleteModal.value = true;
};

// Delete invoice
const deleteInvoice = () => {
  if (!invoiceToDelete.value) return;

  // In a real app, this would make an API call
  invoices.value = invoices.value.filter(
    (invoice) => invoice.id !== invoiceToDelete.value?.id
  );

  showDeleteModal.value = false;
  invoiceToDelete.value = null;
};

// Confirm duplicate invoice
const confirmDuplicateInvoice = (invoice: Invoice) => {
  invoiceToDuplicate.value = invoice;
  showDuplicateModal.value = true;
};

// Duplicate invoice
const duplicateInvoice = () => {
  if (!invoiceToDuplicate.value) return;

  // In a real app, this would make an API call
  const newId = Math.max(...invoices.value.map((invoice) => invoice.id)) + 1;
  const newInvoiceNumber = `INV-2025-${String(newId).padStart(3, "0")}`;

  // Create a new date for the duplicated invoice (today + 15 days for due date)
  const today = new Date();
  const dueDate = new Date(today);
  dueDate.setDate(today.getDate() + 15);

  const newInvoice: Invoice = {
    ...invoiceToDuplicate.value,
    id: newId,
    invoiceNumber: newInvoiceNumber,
    issueDate: today.toISOString().split("T")[0],
    dueDate: dueDate.toISOString().split("T")[0],
    status: "draft",
    paymentDate: undefined,
  };

  invoices.value.push(newInvoice);

  showDuplicateModal.value = false;
  invoiceToDuplicate.value = null;
};

// Send payment reminder
const confirmSendReminder = (invoice: Invoice) => {
  invoiceToRemind.value = invoice;
  remindMessage.value = `Dear ${
    invoice.clientName
  },\n\nThis is a friendly reminder that invoice ${
    invoice.invoiceNumber
  } for ${formatCurrency(
    invoice.total
  )} is currently overdue. The payment was due on ${formatDate(
    invoice.dueDate
  )}.\n\nPlease make the payment at your earliest convenience.\n\nThank you,\nSarah Parker`;
  showRemindModal.value = true;
};

// Send reminder
const sendReminder = () => {
  if (!invoiceToRemind.value || !remindMessage.value) return;

  // In a real app, this would make an API call to send an email
  // For now, just close the modal and show a success message

  showRemindModal.value = false;
  invoiceToRemind.value = null;
  remindMessage.value = "";

  // Here you would show a success notification
};

// Mark invoice as paid
const markAsPaid = (invoice: Invoice) => {
  // In a real app, this would make an API call
  const invoiceToUpdate = invoices.value.find((inv) => inv.id === invoice.id);
  if (invoiceToUpdate) {
    invoiceToUpdate.status = "paid";
    invoiceToUpdate.paymentDate = new Date().toISOString().split("T")[0];
  }
};

// Navigate to new invoice page
const navigateToNewInvoice = () => {
  router.push("/dashboard/consultant/invoices/create");
};

// Load data on mount
onMounted(() => {
  fetchInvoices();
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
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Invoices</h1>
          <p class="text-gray-600">Manage your invoices and payments</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="navigateToNewInvoice"
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
            Create Invoice
          </button>
        </div>
      </div>
    </div>

    <!-- Invoice Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
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
                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Outstanding</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(invoiceStats.outstandingAmount) }}
            </p>
            <p class="text-xs text-gray-500">
              {{
                invoiceStats.sentInvoices + invoiceStats.overdueInvoices
              }}
              invoices
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
            <p class="text-sm text-gray-500 font-medium">Total Paid</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(invoiceStats.paidAmount) }}
            </p>
            <p class="text-xs text-gray-500">
              {{ invoiceStats.paidInvoices }} invoices
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-red-100 rounded-full p-3 mr-4">
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Overdue</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(invoiceStats.overdueAmount) }}
            </p>
            <p class="text-xs text-gray-500">
              {{ invoiceStats.overdueInvoices }} invoices
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-gray-100 rounded-full p-3 mr-4">
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
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Draft</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ invoiceStats.draftInvoices }}
            </p>
            <p class="text-xs text-gray-500">Invoices to send</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
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
            All
          </button>
          <button
            @click="activeTab = 'draft'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'draft'
                ? 'bg-gray-100 text-gray-800'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Draft
          </button>
          <button
            @click="activeTab = 'sent'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'sent'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Sent
          </button>
          <button
            @click="activeTab = 'paid'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'paid'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Paid
          </button>
          <button
            @click="activeTab = 'overdue'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'overdue'
                ? 'bg-red-100 text-red-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Overdue
          </button>
        </div>

        <div class="flex space-x-2">
          <!-- Search Input -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search invoices..."
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

          <!-- Sort Dropdown -->
          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-3 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="recent">Recent First</option>
              <option value="oldest">Oldest First</option>
              <option value="due-date">Due Date</option>
              <option value="amount-high">Amount (High-Low)</option>
              <option value="amount-low">Amount (Low-High)</option>
              <option value="client">Client Name</option>
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

    <!-- Loading Skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="i in 3"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="rounded-full bg-gray-200 h-12 w-12"></div>
          <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
          </div>
          <div class="h-6 bg-gray-200 rounded w-24 mb-2"></div>
          <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
        </div>
      </div>
    </div>

    <!-- Invoices List -->
    <div
      v-else-if="filteredInvoices.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <!-- Table Header -->
      <div
        class="hidden md:grid md:grid-cols-6 text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 p-4 border-b border-gray-200"
      >
        <div class="col-span-2">Invoice / Client</div>
        <div>Issue Date</div>
        <div>Due Date</div>
        <div class="text-right">Amount</div>
        <div class="text-right">Actions</div>
      </div>

      <!-- Invoices -->
      <div
        v-for="invoice in filteredInvoices"
        :key="`invoice-${invoice.id}`"
        class="border-b border-gray-200 hover:bg-gray-50 transition-colors p-4"
      >
        <!-- Mobile View (Collapsed) -->
        <div class="md:hidden flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <img
              :src="invoice.clientAvatar"
              :alt="invoice.clientName"
              class="h-10 w-10 rounded-full"
            />
            <div>
              <div class="font-medium text-gray-900">
                {{ invoice.invoiceNumber }}
              </div>
              <div class="text-sm text-gray-500">{{ invoice.clientName }}</div>
            </div>
          </div>
          <div class="text-right">
            <div class="font-medium text-gray-900">
              {{ formatCurrency(invoice.total) }}
            </div>
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
              :class="getInvoiceStatusClasses(invoice.status)"
            >
              {{ invoice.status }}
            </span>
          </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden md:grid md:grid-cols-6 md:items-center">
          <!-- Invoice & Client -->
          <div class="col-span-2 flex items-center">
            <img
              :src="invoice.clientAvatar"
              :alt="invoice.clientName"
              class="h-10 w-10 rounded-full mr-3"
            />
            <div>
              <div class="font-medium text-gray-900">
                {{ invoice.invoiceNumber }}
              </div>
              <div class="text-sm text-gray-500">{{ invoice.clientName }}</div>
              <div v-if="invoice.clientCompany" class="text-xs text-gray-400">
                {{ invoice.clientCompany }}
              </div>
            </div>
            <span
              class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
              :class="getInvoiceStatusClasses(invoice.status)"
            >
              {{ invoice.status }}
            </span>
          </div>

          <!-- Issue Date -->
          <div class="text-sm text-gray-500">
            {{ formatDate(invoice.issueDate) }}
          </div>

          <!-- Due Date -->
          <div
            class="text-sm"
            :class="
              isOverdue(invoice) && invoice.status !== 'paid'
                ? 'text-red-600 font-medium'
                : 'text-gray-500'
            "
          >
            {{ formatDate(invoice.dueDate) }}
            <div
              v-if="isOverdue(invoice) && invoice.status !== 'paid'"
              class="text-xs text-red-500"
            >
              Overdue
            </div>
          </div>

          <!-- Amount -->
          <div class="text-right text-sm font-medium text-gray-900">
            {{ formatCurrency(invoice.total) }}
            <div v-if="invoice.status === 'paid'" class="text-xs text-gray-500">
              Paid on {{ formatDate(invoice.paymentDate!) }}
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-1">
            <button
              @click="viewInvoiceDetails(invoice)"
              class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-indigo-600"
              title="View Invoice"
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

            <button
              v-if="invoice.status === 'draft'"
              @click="
                router.push(`/dashboard/consultant/invoices/${invoice.id}/edit`)
              "
              class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-blue-600"
              title="Edit Invoice"
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
            </button>

            <button
              v-if="invoice.status === 'sent' || invoice.status === 'overdue'"
              @click="markAsPaid(invoice)"
              class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-green-600"
              title="Mark as Paid"
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
            </button>

            <button
              v-if="invoice.status === 'overdue'"
              @click="confirmSendReminder(invoice)"
              class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-amber-600"
              title="Send Payment Reminder"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                />
              </svg>
            </button>

            <button
              @click="confirmDuplicateInvoice(invoice)"
              class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700"
              title="Duplicate Invoice"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z"
                />
                <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
              </svg>
            </button>

            <button
              v-if="invoice.status === 'draft'"
              @click="confirmDeleteInvoice(invoice)"
              class="p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-red-600"
              title="Delete Invoice"
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
      <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No invoices match your search criteria."
            : "You haven't created any invoices yet."
        }}
      </p>
      <button
        @click="navigateToNewInvoice"
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
        Create Your First Invoice
      </button>
    </div>

    <!-- Invoice Detail Modal -->
    <div
      v-if="showInvoiceDetailModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl transform transition-all"
      >
        <div class="relative">
          <!-- Header -->
          <div
            class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center rounded-t-lg"
          >
            <h3 class="text-lg font-bold">
              Invoice {{ selectedInvoice?.invoiceNumber }}
            </h3>
            <button
              @click="showInvoiceDetailModal = false"
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

          <!-- Content -->
          <div class="p-6">
            <!-- Invoice Status -->
            <div class="mb-6 flex justify-between items-center">
              <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
                :class="
                  selectedInvoice
                    ? getInvoiceStatusClasses(selectedInvoice.status)
                    : ''
                "
              >
                {{ selectedInvoice?.status }}
              </span>

              <div
                v-if="selectedInvoice?.status === 'paid'"
                class="text-sm text-gray-600"
              >
                Paid on {{ formatDate(selectedInvoice?.paymentDate || "") }}
                <span v-if="selectedInvoice?.paymentMethod" class="ml-1"
                  >({{ selectedInvoice?.paymentMethod }})</span
                >
              </div>
            </div>

            <!-- Invoice Info -->
            <div class="grid grid-cols-2 gap-6 mb-6">
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-1">
                  Invoice To
                </h4>
                <div class="text-sm">
                  <div class="font-medium">
                    {{ selectedInvoice?.clientName }}
                  </div>
                  <div v-if="selectedInvoice?.clientCompany">
                    {{ selectedInvoice?.clientCompany }}
                  </div>
                  <div class="text-gray-600">
                    {{ selectedInvoice?.clientEmail }}
                  </div>
                </div>
              </div>

              <div>
                <div class="text-sm mb-3">
                  <div class="font-medium text-gray-900 mb-1">
                    Invoice Details
                  </div>
                  <div>
                    <span class="text-gray-600">Issue Date:</span>
                    {{
                      selectedInvoice
                        ? formatDate(selectedInvoice.issueDate)
                        : ""
                    }}
                  </div>
                  <div>
                    <span class="text-gray-600">Due Date:</span>
                    {{
                      selectedInvoice ? formatDate(selectedInvoice.dueDate) : ""
                    }}
                  </div>
                  <div v-if="selectedInvoice?.projectName">
                    <span class="text-gray-600">Project:</span>
                    {{ selectedInvoice.projectName }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Invoice Items -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">
                Invoice Items
              </h4>
              <div class="bg-gray-50 rounded-lg overflow-hidden">
                <!-- Header -->
                <div
                  class="grid grid-cols-10 text-xs font-medium text-gray-500 uppercase bg-gray-100 p-3"
                >
                  <div class="col-span-5">Description</div>
                  <div class="col-span-1 text-right">Qty</div>
                  <div class="col-span-2 text-right">Rate</div>
                  <div class="col-span-2 text-right">Amount</div>
                </div>

                <!-- Items -->
                <div
                  v-for="item in selectedInvoice?.items"
                  :key="item.id"
                  class="grid grid-cols-10 p-3 border-t border-gray-200 text-sm"
                >
                  <div class="col-span-5">{{ item.description }}</div>
                  <div class="col-span-1 text-right">{{ item.quantity }}</div>
                  <div class="col-span-2 text-right">
                    {{ formatCurrency(item.rate) }}
                  </div>
                  <div class="col-span-2 text-right font-medium">
                    {{ formatCurrency(item.amount) }}
                  </div>
                </div>

                <!-- Subtotal, Tax, Total -->
                <div class="border-t border-gray-200 p-3">
                  <div class="grid grid-cols-10 text-sm mb-1">
                    <div class="col-span-8 text-right text-gray-600">
                      Subtotal:
                    </div>
                    <div class="col-span-2 text-right">
                      {{
                        selectedInvoice
                          ? formatCurrency(selectedInvoice.subtotal)
                          : ""
                      }}
                    </div>
                  </div>
                  <div class="grid grid-cols-10 text-sm mb-1">
                    <div class="col-span-8 text-right text-gray-600">
                      Tax ({{ selectedInvoice?.taxRate }}%):
                    </div>
                    <div class="col-span-2 text-right">
                      {{
                        selectedInvoice
                          ? formatCurrency(selectedInvoice.taxAmount)
                          : ""
                      }}
                    </div>
                  </div>
                  <div
                    class="grid grid-cols-10 text-sm font-bold border-t border-gray-200 pt-2 mt-2"
                  >
                    <div class="col-span-8 text-right">Total:</div>
                    <div class="col-span-2 text-right">
                      {{
                        selectedInvoice
                          ? formatCurrency(selectedInvoice.total)
                          : ""
                      }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="selectedInvoice?.notes" class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-1">Notes</h4>
              <p class="text-sm text-gray-600">{{ selectedInvoice.notes }}</p>
            </div>

            <!-- Action Buttons -->
            <div
              class="flex justify-end space-x-3 border-t border-gray-200 pt-4 mt-4"
            >
              <button
                v-if="selectedInvoice?.status === 'draft'"
                @click="
                  router.push(
                    `/dashboard/consultant/invoices/${selectedInvoice.id}/edit`
                  );
                  showInvoiceDetailModal = false;
                "
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                Edit Invoice
              </button>

              <button
                v-if="selectedInvoice?.status === 'draft'"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
              >
                Send Invoice
              </button>

              <button
                v-if="
                  selectedInvoice?.status === 'sent' ||
                  selectedInvoice?.status === 'overdue'
                "
                @click="
                  markAsPaid(selectedInvoice!);
                  showInvoiceDetailModal = false;
                "
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
              >
                Mark as Paid
              </button>

              <button
                v-if="selectedInvoice?.status === 'overdue'"
                @click="
                  confirmSendReminder(selectedInvoice!);
                  showInvoiceDetailModal = false;
                "
                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors"
              >
                Send Reminder
              </button>

              <button
                @click="showInvoiceDetailModal = false"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
              >
                Close
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
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="p-6">
          <div
            class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mx-auto mb-4"
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
          <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
            Delete Invoice
          </h3>
          <p class="text-sm text-gray-600 text-center mb-6">
            Are you sure you want to delete invoice
            <span class="font-medium">{{ invoiceToDelete?.invoiceNumber }}</span
            >? This action cannot be undone.
          </p>
          <div class="flex justify-center space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="deleteInvoice"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Duplicate Confirmation Modal -->
    <div
      v-if="showDuplicateModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div class="p-6">
          <div
            class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mx-auto mb-4"
          >
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
                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
              />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
            Duplicate Invoice
          </h3>
          <p class="text-sm text-gray-600 text-center mb-6">
            Are you sure you want to duplicate invoice
            <span class="font-medium">{{
              invoiceToDuplicate?.invoiceNumber
            }}</span
            >? This will create a new draft invoice with the same items and
            client.
          </p>
          <div class="flex justify-center space-x-3">
            <button
              @click="showDuplicateModal = false"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="duplicateInvoice"
              class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Duplicate
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Send Reminder Modal -->
    <div
      v-if="showRemindModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-2xl w-full overflow-hidden shadow-xl transform transition-all"
      >
        <div
          class="px-6 py-4 bg-amber-600 text-white flex justify-between items-center rounded-t-lg"
        >
          <h3 class="text-lg font-bold">
            Payment Reminder for Invoice {{ invoiceToRemind?.invoiceNumber }}
          </h3>
          <button
            @click="showRemindModal = false"
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

        <div class="p-6">
          <div class="mb-4">
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >To</label
                >
                <div class="bg-gray-100 p-2 rounded-lg">
                  {{ invoiceToRemind?.clientEmail }}
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Subject</label
                >
                <div class="bg-gray-100 p-2 rounded-lg">
                  Payment Reminder for Invoice
                  {{ invoiceToRemind?.invoiceNumber }}
                </div>
              </div>
            </div>

            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Message</label
            >
            <textarea
              v-model="remindMessage"
              rows="6"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            ></textarea>
          </div>

          <div class="bg-amber-50 p-4 rounded-lg border border-amber-200 mb-4">
            <div class="flex items-start">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-amber-500 mt-0.5 mr-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"
                />
              </svg>
              <p class="text-sm text-amber-700">
                This email will be sent directly to your client. It's a good
                practice to be polite while being clear about the payment due.
              </p>
            </div>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              @click="showRemindModal = false"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="sendReminder"
              class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors"
              :disabled="!remindMessage"
            >
              Send Reminder
            </button>
          </div>
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

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
