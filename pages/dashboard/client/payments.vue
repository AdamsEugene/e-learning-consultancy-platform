<!-- pages/dashboard/client/payments/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta using the dashboard-client layout
definePageMeta({
  layout: "dashboard-client",
});

// Set page title and meta description
useHead({
  title: "Payments - Client Dashboard",
  meta: [
    {
      name: "description",
      content: "Manage invoices, payment methods and transactions",
    },
  ],
});

// Types for payment data
interface Invoice {
  id: string;
  invoiceNumber: string;
  projectId: string;
  projectName: string;
  amount: number;
  currency: string;
  issueDate: string;
  dueDate: string;
  status: "paid" | "pending" | "overdue" | "cancelled";
  consultant: {
    id: string;
    name: string;
    avatar: string;
  };
  items: Array<{
    description: string;
    quantity: number;
    rate: number;
    amount: number;
  }>;
}

interface Transaction {
  id: string;
  invoiceId: string;
  invoiceNumber: string;
  amount: number;
  currency: string;
  date: string;
  paymentMethod: string;
  status: "completed" | "processing" | "failed";
  transactionId: string;
}

interface PaymentMethod {
  id: string;
  type: "credit_card" | "bank_account" | "paypal";
  name: string;
  lastFour: string;
  expiryDate?: string;
  isDefault: boolean;
  brand?: string;
}

// State variables
const isLoading = ref(true);
const invoices = ref<Invoice[]>([]);
const transactions = ref<Transaction[]>([]);
const paymentMethods = ref<PaymentMethod[]>([]);
const filteredInvoices = ref<Invoice[]>([]);
const activeTab = ref("all");
const searchQuery = ref("");
const sortBy = ref("date");
const sortDirection = ref("desc");
const selectedInvoice = ref<Invoice | null>(null);
const showInvoiceModal = ref(false);
const showAddPaymentModal = ref(false);
const showAddPaymentMethodModal = ref(false);
const selectedPaymentMethod = ref<string>("");
const isProcessingPayment = ref(false);

// Fetch payments data (mock data for now)
const fetchPaymentsData = async () => {
  isLoading.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data for invoices
    invoices.value = [
      {
        id: "inv-001",
        invoiceNumber: "INV-2025-001",
        projectId: "p1",
        projectName: "Website Redesign",
        amount: 2500.0,
        currency: "USD",
        issueDate: "2025-03-15T00:00:00Z",
        dueDate: "2025-04-15T00:00:00Z",
        status: "paid",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
        },
        items: [
          {
            description: "UI/UX Design Services",
            quantity: 25,
            rate: 100,
            amount: 2500,
          },
        ],
      },
      {
        id: "inv-002",
        invoiceNumber: "INV-2025-002",
        projectId: "p2",
        projectName: "Mobile App Development",
        amount: 4000.0,
        currency: "USD",
        issueDate: "2025-03-25T00:00:00Z",
        dueDate: "2025-04-25T00:00:00Z",
        status: "pending",
        consultant: {
          id: "c3",
          name: "David Chen",
          avatar: "/api/placeholder/100/100",
        },
        items: [
          {
            description: "Backend API Development",
            quantity: 20,
            rate: 150,
            amount: 3000,
          },
          {
            description: "Project Management",
            quantity: 10,
            rate: 100,
            amount: 1000,
          },
        ],
      },
      {
        id: "inv-003",
        invoiceNumber: "INV-2025-003",
        projectId: "p1",
        projectName: "Website Redesign",
        amount: 1500.0,
        currency: "USD",
        issueDate: "2025-04-01T00:00:00Z",
        dueDate: "2025-05-01T00:00:00Z",
        status: "pending",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
        },
        items: [
          {
            description: "Frontend Development",
            quantity: 15,
            rate: 100,
            amount: 1500,
          },
        ],
      },
      {
        id: "inv-004",
        invoiceNumber: "INV-2025-004",
        projectId: "p3",
        projectName: "E-commerce Integration",
        amount: 3200.0,
        currency: "USD",
        issueDate: "2025-02-15T00:00:00Z",
        dueDate: "2025-03-15T00:00:00Z",
        status: "overdue",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
        },
        items: [
          {
            description: "E-commerce Platform Integration",
            quantity: 20,
            rate: 120,
            amount: 2400,
          },
          {
            description: "Payment Gateway Setup",
            quantity: 8,
            rate: 100,
            amount: 800,
          },
        ],
      },
      {
        id: "inv-005",
        invoiceNumber: "INV-2025-005",
        projectId: "p3",
        projectName: "E-commerce Integration",
        amount: 1000.0,
        currency: "USD",
        issueDate: "2025-01-20T00:00:00Z",
        dueDate: "2025-02-20T00:00:00Z",
        status: "cancelled",
        consultant: {
          id: "c1",
          name: "Sarah Parker",
          avatar: "/api/placeholder/100/100",
        },
        items: [
          {
            description: "Initial Consultation & Planning",
            quantity: 10,
            rate: 100,
            amount: 1000,
          },
        ],
      },
    ];

    // Mock data for transactions
    transactions.value = [
      {
        id: "txn-001",
        invoiceId: "inv-001",
        invoiceNumber: "INV-2025-001",
        amount: 2500.0,
        currency: "USD",
        date: "2025-03-20T14:30:00Z",
        paymentMethod: "Visa ending in 4242",
        status: "completed",
        transactionId: "ch_3OqL5J2eZvKYlo2C1M7khFPs",
      },
      {
        id: "txn-002",
        invoiceId: "inv-005",
        invoiceNumber: "INV-2025-005",
        amount: 1000.0,
        currency: "USD",
        date: "2025-01-25T10:15:00Z",
        paymentMethod: "Bank Transfer",
        status: "failed",
        transactionId: "ch_3OqL5J2eZvKYlo2C1M7khFQr",
      },
    ];

    // Mock data for payment methods
    paymentMethods.value = [
      {
        id: "pm-001",
        type: "credit_card",
        name: "Visa",
        lastFour: "4242",
        expiryDate: "04/2028",
        isDefault: true,
        brand: "visa",
      },
      {
        id: "pm-002",
        type: "credit_card",
        name: "Mastercard",
        lastFour: "5555",
        expiryDate: "09/2026",
        isDefault: false,
        brand: "mastercard",
      },
      {
        id: "pm-003",
        type: "bank_account",
        name: "Wells Fargo Checking",
        lastFour: "3456",
        isDefault: false,
      },
    ];

    filterInvoices();
  } catch (error) {
    console.error("Failed to fetch payments data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter invoices based on active tab and search query
const filterInvoices = () => {
  let filtered = [...invoices.value];

  // Filter by tab
  if (activeTab.value === "pending") {
    filtered = filtered.filter((invoice) => invoice.status === "pending");
  } else if (activeTab.value === "paid") {
    filtered = filtered.filter((invoice) => invoice.status === "paid");
  } else if (activeTab.value === "overdue") {
    filtered = filtered.filter((invoice) => invoice.status === "overdue");
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (invoice) =>
        invoice.invoiceNumber.toLowerCase().includes(query) ||
        invoice.projectName.toLowerCase().includes(query) ||
        invoice.consultant.name.toLowerCase().includes(query)
    );
  }

  // Apply sorting
  if (sortBy.value === "date") {
    filtered.sort((a, b) => {
      const dateA = new Date(a.issueDate).getTime();
      const dateB = new Date(b.issueDate).getTime();
      return sortDirection.value === "desc" ? dateB - dateA : dateA - dateB;
    });
  } else if (sortBy.value === "amount") {
    filtered.sort((a, b) => {
      return sortDirection.value === "desc"
        ? b.amount - a.amount
        : a.amount - b.amount;
    });
  } else if (sortBy.value === "dueDate") {
    filtered.sort((a, b) => {
      const dateA = new Date(a.dueDate).getTime();
      const dateB = new Date(b.dueDate).getTime();
      return sortDirection.value === "desc" ? dateB - dateA : dateA - dateB;
    });
  }

  filteredInvoices.value = filtered;
};

// Watch for changes in activeTab, searchQuery, sortBy, or sortDirection
watch(() => activeTab.value, filterInvoices);
watch(() => searchQuery.value, filterInvoices);
watch(() => sortBy.value, filterInvoices);
watch(() => sortDirection.value, filterInvoices);

// Format date for display
const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = {
    year: "numeric",
    month: "short",
    day: "numeric",
  };

  return new Date(dateString).toLocaleDateString("en-US", options);
};

// Format currency for display
const formatCurrency = (amount: number, currency: string = "USD") => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: currency,
  }).format(amount);
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "paid":
      return "bg-green-100 text-green-800";
    case "pending":
      return "bg-blue-100 text-blue-800";
    case "overdue":
      return "bg-red-100 text-red-800";
    case "cancelled":
      return "bg-gray-100 text-gray-800";
    case "completed":
      return "bg-green-100 text-green-800";
    case "processing":
      return "bg-yellow-100 text-yellow-800";
    case "failed":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get payment method icon
const getPaymentMethodIcon = (method: PaymentMethod) => {
  if (method.type === "credit_card") {
    if (method.brand === "visa") {
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="#1434CB">
        <path d="M9.586 16.212h-1.624l1.013-6.232h1.625l-1.014 6.232zm7.56-6.07c-.55-.208-1.392-.437-2.4-.437-2.644 0-4.512 1.33-4.524 3.237-.024 1.41 1.332 2.196 2.346 2.663 1.036.474 1.398.802 1.398 1.227-.012.661-.838.96-1.613.96-1.072 0-1.66-.154-2.531-.52l-.358-.154-.382 2.25c.634.285 1.822.52 3.045.532 2.859 0 4.72-1.318 4.745-3.347.012-1.125-.692-1.973-2.2-2.663-1-0.462-1.493-.724-1.493-1.213.012-.417.467-.849 1.493-.849 0.85-.024 1.48.154 1.947.344l0.24.106 0.36-2.141zm4.995 3.93c0.215-0.583 1.118-2.83 1.118-2.83-0.012 0.024 0.227-0.616 0.37-1.011l0.19 0.888s0.545 2.482 0.654 2.953h-2.332zm3.466-6.093h-1.264c-0.392 0-0.68 0.104-0.856 0.498l-2.429 5.734h2.716l0.346-0.936h2.099l0.19 0.936h2.396l-1.264-6.232h-1.934zm-16.204 0l-2.56 6.437h2.613l0.467-6.432h-0.52z"/>
      </svg>`;
    } else if (method.brand === "mastercard") {
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M11.343 18.031c-1.742 0-3.334-0.62-4.576-1.649l-0.009-0.008c-1.239-1.016-2.027-2.549-2.027-4.267 0-0 0-0.001 0-0.001v0c0-3.070 2.49-5.56 5.56-5.56l0.092 0c3.070 0 5.56 2.49 5.56 5.56v0c0 3.070-2.49 5.56-5.56 5.56h-0.040zM16.274 18.031c0.178 0 0.355-0.010 0.533-0.029-1.070-0.819-1.877-1.962-2.254-3.307l-0.010-0.043c-0.084-0.299-0.133-0.643-0.133-0.997 0-0.015 0-0.030 0-0.045l-0 0.002c0-0.025 0-0.055 0-0.084 0-0.773 0.16-1.509 0.449-2.178l-0.013 0.034c0.462-1.079 1.193-1.963 2.113-2.599l0.018-0.012c-1.343-1.088-3.076-1.753-4.966-1.76h-0.002c-4.351 0-7.879 3.528-7.879 7.879s3.528 7.879 7.879 7.879c1.496 0 2.896-0.416 4.090-1.141l-0.035 0.020c0.070-0.045 0.128-0.079 0.188-0.115l-0.038 0.021z" fill="#eb001b"/>
        <path d="M16.274 18.031c1.742 0 3.334-0.62 4.576-1.649l0.009-0.008c1.239-1.016 2.027-2.549 2.027-4.267 0-0 0-0.001 0-0.001v0c0-3.070-2.49-5.56-5.56-5.56l-0.092 0c-3.070 0-5.56 2.49-5.56 5.56v0c0 3.070 2.49 5.56 5.56 5.56h0.040zM11.343 18.031c-0.178 0-0.355-0.010-0.533-0.029 1.070-0.819 1.877-1.962 2.254-3.307l0.010-0.043c0.084-0.299 0.133-0.643 0.133-0.997 0-0.015-0-0.030-0-0.045l0 0.002c0-0.025 0-0.055 0-0.084 0-0.773-0.16-1.509-0.449-2.178l0.013 0.034c-0.462-1.079-1.193-1.963-2.113-2.599l-0.018-0.012c1.343-1.088 3.076-1.753 4.966-1.76h0.002c4.351 0 7.879 3.528 7.879 7.879s-3.528 7.879-7.879 7.879c-1.496 0-2.896-0.416-4.090-1.141l0.035 0.020c-0.070-0.045-0.128-0.079-0.188-0.115l0.038 0.021z" fill="#f79e1b"/>
        <path d="M16.008 12.108c0 1.074-0.871 1.944-1.944 1.944s-1.944-0.871-1.944-1.944c0-1.074 0.871-1.944 1.944-1.944v0c1.074 0 1.944 0.871 1.944 1.944v0z" fill="#ff5f00"/>
      </svg>`;
    } else {
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
      </svg>`;
    }
  } else if (method.type === "bank_account") {
    return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
    </svg>`;
  } else if (method.type === "paypal") {
    return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="#00457C">
      <path d="M7.076 21.337H2.47a0.641 0.641 0 0 1-0.633-0.74L4.944 3.978a0.764 0.764 0 0 1 0.752-0.641h5.576c2.899 0 4.923 0.863 5.609 3.183a6.611 6.611 0 0 1 0.232 1.81c0 3.96-3.097 6.063-7.317 6.063H7.987c-0.403 0-0.74 0.244-0.878 0.645l-1.43 7.547a0.147 0.147 0 0 1-0.145 0.122h-0.021l0.006 0.002 0.002 0.006 0.003 0.005 0.002 0.004 0.003 0.004 0.003 0.004 0.006 0.005h0.002l0.003 0.002 0.004 0.001h0.001l0.003 0.003h-0.003V21.34l0.002 0 0.002-0.001 0.003-0.001 0.002-0.001 0.003-0.001 0.002-0.001 0.003-0.001 0.002-0.001 0.007-0.006a0.15 0.15 0 0 1 0.056-0.008l0.003 0 0.004 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001z"/>
      <path d="M7.076 21.337H2.47a0.641 0.641 0 0 1-0.633-0.74L4.944 3.978a0.764 0.764 0 0 1 0.752-0.641h5.576c2.899 0 4.923 0.863 5.609 3.183a6.611 6.611 0 0 1 0.232 1.81c0 3.96-3.097 6.063-7.317 6.063H7.987c-0.403 0-0.74 0.244-0.878 0.645l-1.43 7.547a0.147 0.147 0 0 1-0.145 0.122h-0.021l0.006 0.002 0.002 0.006 0.003 0.005 0.002 0.004 0.003 0.004 0.003 0.004 0.006 0.005h0.002l0.003 0.002 0.004 0.001h0.001l0.003 0.003h-0.003V21.34l0.002 0 0.002-0.001 0.003-0.001 0.002-0.001 0.003-0.001 0.002-0.001 0.003-0.001 0.002-0.001 0.007-0.006a0.15 0.15 0 0 1 0.056-0.008l0.003 0 0.004 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001 0.003 0.001z" fill="#00457C"/>
      <path d="M16.146 7.291a6.907 6.907 0 0 0-0.214-1.107C15.124 3.437 12.392 2.708 9.355 2.708H2.866c-0.513 0-0.95 0.374-1.03 0.885L0.056 16.912c-0.097 0.627 0.392 1.181 1.03 1.181h3.132l0.869-5.546c0.020-0.094 0.051-0.183 0.091-0.268l-0.003-0.002c0.015-0.031 0.031-0.06 0.048-0.088 0.015-0.025 0.031-0.049 0.047-0.072 0.014-0.019 0.028-0.036 0.042-0.054 0.017-0.021 0.035-0.04 0.054-0.059 0.014-0.015 0.029-0.029 0.045-0.042 0.019-0.017 0.039-0.033 0.06-0.048 0.009-0.007 0.016-0.014 0.025-0.02l0.006-0.005c0.03-0.021 0.061-0.039 0.093-0.055 0.018-0.01 0.035-0.018 0.053-0.025 0.017-0.008 0.035-0.016 0.053-0.023 0.02-0.007 0.039-0.014 0.059-0.019 0.016-0.005 0.033-0.009 0.05-0.013 0.023-0.005 0.046-0.008 0.069-0.01 0.014-0.001 0.028-0.003 0.042-0.004 0.027-0.001 0.053-0.001 0.08-0.001h1.821c3.098 0 5.909-1.376 6.546-4.868 0.089-0.369 0.148-0.768 0.176-1.2 0.007-0.111 0.012-0.223 0.012-0.339 0-0.2-0.012-0.394-0.033-0.585z" fill="#0079C1"/>
    </svg>`;
  } else {
    return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
    </svg>`;
  }
};

// Open invoice details
const openInvoiceDetails = (invoice: Invoice) => {
  selectedInvoice.value = invoice;
  showInvoiceModal.value = true;
};

// Make payment for an invoice
const makePayment = () => {
  if (!selectedInvoice.value) return;

  isProcessingPayment.value = true;

  // Simulate API call to process payment
  setTimeout(() => {
    if (selectedInvoice.value) {
      // Update invoice status
      const index = invoices.value.findIndex(
        (inv) => inv.id === selectedInvoice.value?.id
      );
      if (index !== -1) {
        invoices.value[index].status = "paid";
      }

      // Create transaction record
      const newTransaction: Transaction = {
        id: `txn-${Date.now()}`,
        invoiceId: selectedInvoice.value.id,
        invoiceNumber: selectedInvoice.value.invoiceNumber,
        amount: selectedInvoice.value.amount,
        currency: selectedInvoice.value.currency,
        date: new Date().toISOString(),
        paymentMethod:
          paymentMethods.value.find(
            (pm) => pm.id === selectedPaymentMethod.value
          )?.name +
            " ending in " +
            paymentMethods.value.find(
              (pm) => pm.id === selectedPaymentMethod.value
            )?.lastFour || "Unknown",
        status: "completed",
        transactionId: `ch_${Math.random().toString(36).substring(2, 15)}`,
      };

      transactions.value.push(newTransaction);

      // Reset and close modals
      isProcessingPayment.value = false;
      showAddPaymentModal.value = false;
      showInvoiceModal.value = false;
      selectedPaymentMethod.value = "";
      filterInvoices();
    }
  }, 2000);
};

// Add new payment method
const addPaymentMethod = () => {
  // This would open a form to add a new payment method
  showAddPaymentMethodModal.value = true;
};

// Initialize component
onMounted(() => {
  fetchPaymentsData();
});

// Computed props
const totalPaid = computed(() => {
  return invoices.value
    .filter((invoice) => invoice.status === "paid")
    .reduce((sum, invoice) => sum + invoice.amount, 0);
});

const totalPending = computed(() => {
  return invoices.value
    .filter(
      (invoice) => invoice.status === "pending" || invoice.status === "overdue"
    )
    .reduce((sum, invoice) => sum + invoice.amount, 0);
});

const pendingInvoicesCount = computed(() => {
  return invoices.value.filter((invoice) => invoice.status === "pending")
    .length;
});

const overdueInvoicesCount = computed(() => {
  return invoices.value.filter((invoice) => invoice.status === "overdue")
    .length;
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Payments & Invoices
          </h1>
          <p class="text-gray-600">
            Manage your invoices, payment methods, and transaction history
          </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <button
            @click="addPaymentMethod"
            class="inline-flex items-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
              <path
                fill-rule="evenodd"
                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Add Payment Method</span>
          </button>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Paid</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(totalPaid) }}
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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Pending Invoices</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ pendingInvoicesCount }}
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
              <p class="text-sm text-gray-500 font-medium">Overdue Invoices</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ overdueInvoicesCount }}
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
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Amount Pending</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ formatCurrency(totalPending) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment methods section -->
    <div class="bg-white rounded-lg shadow mb-6 border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Payment Methods</h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="method in paymentMethods"
            :key="method.id"
            class="border rounded-lg p-4 relative"
          >
            <div class="absolute top-2 right-2" v-if="method.isDefault">
              <span
                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full"
                >Default</span
              >
            </div>
            <div class="flex items-center">
              <span
                class="mr-3 text-gray-500"
                v-html="getPaymentMethodIcon(method)"
              ></span>
              <div>
                <h3 class="font-medium text-gray-900">{{ method.name }}</h3>
                <p class="text-sm text-gray-600">
                  {{
                    method.type === "credit_card"
                      ? "Card"
                      : method.type === "bank_account"
                      ? "Bank Account"
                      : "PayPal"
                  }}
                  ending in {{ method.lastFour }}
                </p>
                <p v-if="method.expiryDate" class="text-sm text-gray-600">
                  Expires {{ method.expiryDate }}
                </p>
              </div>
            </div>
            <div class="mt-3 flex space-x-2">
              <button
                v-if="!method.isDefault"
                class="text-sm text-indigo-600 hover:text-indigo-800"
              >
                Set as Default
              </button>
              <button class="text-sm text-gray-600 hover:text-gray-800">
                Edit
              </button>
              <button
                v-if="!method.isDefault"
                class="text-sm text-red-600 hover:text-red-800"
              >
                Remove
              </button>
            </div>
          </div>

          <div
            class="border border-dashed rounded-lg p-4 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 cursor-pointer"
            @click="addPaymentMethod"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
              />
            </svg>
            <span>Add New Payment Method</span>
          </div>
        </div>
      </div>
    </div>

    <!-- View options and filters -->
    <div class="bg-white rounded-lg shadow mb-6 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between p-4 space-y-4 md:space-y-0"
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
            All Invoices
          </button>
          <button
            @click="activeTab = 'pending'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'pending'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Pending
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

        <!-- Search and sort controls -->
        <div class="flex items-center space-x-2">
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

          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="date">Issue Date</option>
              <option value="dueDate">Due Date</option>
              <option value="amount">Amount</option>
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

          <button
            @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
            class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
            :title="
              sortDirection === 'asc' ? 'Sort Descending' : 'Sort Ascending'
            "
          >
            <svg
              v-if="sortDirection === 'asc'"
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
            <svg
              v-else
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
          </button>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
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
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoices List -->
    <div
      v-else-if="filteredInvoices.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Invoice
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Project
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Consultant
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Issue Date
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Due Date
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
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="invoice in filteredInvoices"
              :key="invoice.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
                {{ invoice.invoiceNumber }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ invoice.projectName }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <img
                      :src="invoice.consultant.avatar"
                      alt=""
                      class="h-8 w-8 rounded-full"
                    />
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                      {{ invoice.consultant.name }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(invoice.issueDate) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(invoice.dueDate) }}
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
              >
                {{ formatCurrency(invoice.amount, invoice.currency) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                  :class="getStatusBadgeClasses(invoice.status)"
                >
                  {{ invoice.status }}
                </span>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <button
                  @click="openInvoiceDetails(invoice)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  View
                </button>
                <button
                  v-if="
                    invoice.status === 'pending' || invoice.status === 'overdue'
                  "
                  @click="
                    selectedInvoice = invoice;
                    showAddPaymentModal = true;
                  "
                  class="text-green-600 hover:text-green-900"
                >
                  Pay
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No invoices match your search criteria."
            : activeTab === "pending"
            ? "You don't have any pending invoices."
            : activeTab === "paid"
            ? "You don't have any paid invoices."
            : activeTab === "overdue"
            ? "You don't have any overdue invoices."
            : "You don't have any invoices yet."
        }}
      </p>
    </div>

    <!-- Transaction History -->
    <div class="mt-8 mb-12">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
      >
        <h2 class="text-xl font-bold text-gray-900">Transaction History</h2>
      </div>

      <div
        v-if="transactions.length > 0"
        class="bg-white rounded-lg shadow border border-gray-200"
      >
        <div class="overflow-hidden overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Transaction ID
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Invoice
                </th>
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
                  Amount
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Payment Method
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="transaction in transactions"
                :key="transaction.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  {{ transaction.transactionId }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ transaction.invoiceNumber }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(transaction.date) }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                >
                  {{ formatCurrency(transaction.amount, transaction.currency) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ transaction.paymentMethod }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                    :class="getStatusBadgeClasses(transaction.status)"
                  >
                    {{ transaction.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

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
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          No transactions yet
        </h3>
        <p class="text-gray-600">You haven't made any payments yet.</p>
      </div>
    </div>

    <!-- Invoice Details Modal -->
    <div
      v-if="showInvoiceModal && selectedInvoice"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="showInvoiceModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-4xl w-full shadow-xl overflow-hidden"
        @click.stop
      >
        <div
          class="p-6 border-b border-gray-200 flex justify-between items-center"
        >
          <h2 class="text-xl font-bold text-gray-900">
            Invoice {{ selectedInvoice.invoiceNumber }}
          </h2>
          <button
            @click="showInvoiceModal = false"
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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">
                Invoice Details
              </h3>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-500">Invoice Number:</span>
                  <span class="font-medium">{{
                    selectedInvoice.invoiceNumber
                  }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Project:</span>
                  <span class="font-medium">{{
                    selectedInvoice.projectName
                  }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Issue Date:</span>
                  <span class="font-medium">{{
                    formatDate(selectedInvoice.issueDate)
                  }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Due Date:</span>
                  <span class="font-medium">{{
                    formatDate(selectedInvoice.dueDate)
                  }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Status:</span>
                  <span
                    class="font-medium px-2 py-1 rounded-full text-xs inline-flex items-center capitalize"
                    :class="getStatusBadgeClasses(selectedInvoice.status)"
                  >
                    {{ selectedInvoice.status }}
                  </span>
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">Consultant</h3>
              <div class="flex items-center space-x-3 mb-4">
                <img
                  :src="selectedInvoice.consultant.avatar"
                  :alt="selectedInvoice.consultant.name"
                  class="h-10 w-10 rounded-full"
                />
                <div>
                  <div class="font-medium">
                    {{ selectedInvoice.consultant.name }}
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                  Amount Due
                </h3>
                <div class="text-3xl font-bold text-gray-900">
                  {{
                    formatCurrency(
                      selectedInvoice.amount,
                      selectedInvoice.currency
                    )
                  }}
                </div>

                <div
                  v-if="
                    selectedInvoice.status === 'pending' ||
                    selectedInvoice.status === 'overdue'
                  "
                  class="mt-4"
                >
                  <button
                    @click="
                      showAddPaymentModal = true;
                      showInvoiceModal = false;
                    "
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Pay Now
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Invoice Items
            </h3>
            <div class="border rounded-lg overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Description
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Quantity
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Rate
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Amount
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(item, index) in selectedInvoice.items"
                    :key="index"
                  >
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      {{ item.description }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                    >
                      {{ item.quantity }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right"
                    >
                      {{ formatCurrency(item.rate, selectedInvoice.currency) }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium"
                    >
                      {{
                        formatCurrency(item.amount, selectedInvoice.currency)
                      }}
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                  <tr>
                    <td
                      colspan="3"
                      class="px-6 py-4 text-right text-sm font-medium text-gray-900"
                    >
                      Total
                    </td>
                    <td
                      class="px-6 py-4 text-right text-sm font-bold text-gray-900"
                    >
                      {{
                        formatCurrency(
                          selectedInvoice.amount,
                          selectedInvoice.currency
                        )
                      }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
            >
              Download PDF
            </button>
            <button
              @click="showInvoiceModal = false"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Payment Modal -->
    <div
      v-if="showAddPaymentModal && selectedInvoice"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="showAddPaymentModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full shadow-xl overflow-hidden"
        @click.stop
      >
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">Make Payment</h2>
        </div>

        <div class="p-6">
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">
              Invoice Details
            </h3>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-500">Invoice Number:</span>
                <span class="font-medium">{{
                  selectedInvoice.invoiceNumber
                }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Project:</span>
                <span class="font-medium">{{
                  selectedInvoice.projectName
                }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Amount Due:</span>
                <span class="font-bold">{{
                  formatCurrency(
                    selectedInvoice.amount,
                    selectedInvoice.currency
                  )
                }}</span>
              </div>
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select Payment Method
            </label>
            <div class="space-y-2">
              <div
                v-for="method in paymentMethods"
                :key="method.id"
                class="border rounded-lg p-4 cursor-pointer transition-colors"
                :class="
                  selectedPaymentMethod === method.id
                    ? 'border-indigo-500 bg-indigo-50'
                    : 'border-gray-200 hover:bg-gray-50'
                "
                @click="selectedPaymentMethod = method.id"
              >
                <div class="flex items-center">
                  <input
                    type="radio"
                    :id="`payment-method-${method.id}`"
                    :value="method.id"
                    v-model="selectedPaymentMethod"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label
                    :for="`payment-method-${method.id}`"
                    class="ml-3 flex items-center cursor-pointer"
                  >
                    <span
                      class="mr-3 text-gray-500"
                      v-html="getPaymentMethodIcon(method)"
                    ></span>
                    <div>
                      <div class="font-medium text-gray-900">
                        {{ method.name }}
                      </div>
                      <div class="text-sm text-gray-600">
                        Ending in {{ method.lastFour }}
                        <span v-if="method.expiryDate">
                          • Expires {{ method.expiryDate }}</span
                        >
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <div
                class="border border-dashed rounded-lg p-4 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-50 cursor-pointer mt-2"
                @click="addPaymentMethod"
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
                <span>Add New Payment Method</span>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              @click="showAddPaymentModal = false"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
            >
              Cancel
            </button>
            <button
              @click="makePayment"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="!selectedPaymentMethod || isProcessingPayment"
              :class="{
                'opacity-50 cursor-not-allowed':
                  !selectedPaymentMethod || isProcessingPayment,
              }"
            >
              <svg
                v-if="isProcessingPayment"
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
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              <span>{{
                isProcessingPayment
                  ? "Processing..."
                  : `Pay ${formatCurrency(
                      selectedInvoice.amount,
                      selectedInvoice.currency
                    )}`
              }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Payment Method Modal -->
    <div
      v-if="showAddPaymentMethodModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4"
      @click="showAddPaymentMethodModal = false"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full shadow-xl overflow-hidden"
        @click.stop
      >
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-900">Add Payment Method</h2>
        </div>

        <div class="p-6">
          <!-- This would be a form to add a new payment method -->
          <p class="text-gray-600 mb-4">
            Please provide your payment information below:
          </p>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Card Number
              </label>
              <input
                type="text"
                placeholder="1234 1234 1234 1234"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Expiration Date
                </label>
                <input
                  type="text"
                  placeholder="MM/YY"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  CVC
                </label>
                <input
                  type="text"
                  placeholder="123"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Name on Card
              </label>
              <input
                type="text"
                placeholder="John Smith"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div class="flex items-center">
              <input
                type="checkbox"
                id="make-default"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label
                for="make-default"
                class="ml-2 block text-sm text-gray-900"
              >
                Set as default payment method
              </label>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              @click="showAddPaymentMethodModal = false"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
            >
              Cancel
            </button>
            <button
              @click="showAddPaymentMethodModal = false"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Save Payment Method
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
