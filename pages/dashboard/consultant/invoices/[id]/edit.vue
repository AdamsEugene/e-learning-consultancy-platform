<!-- pages/dashboard/consultant/invoices/[id]/edit.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Edit Invoice - Consultant Dashboard",
});

// Router and route instance
const router = useRouter();
const route = useRoute();
const invoiceId = computed(() => Number(route.params.id));

// Invoice Types
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
  termsAndConditions?: string;
  paymentMethod?: string;
  projectId?: number;
  projectName?: string;
  paymentDate?: string;
}

interface Client {
  id: number;
  name: string;
  company?: string;
  email: string;
  avatar: string;
}

interface Project {
  id: number;
  name: string;
  clientId: number;
}

// Form state
const formStatus = ref<"idle" | "loading" | "saving" | "success" | "error">(
  "loading"
);
const errorMessage = ref("");
const showPreview = ref(false);
const originalInvoice = ref<Invoice | null>(null);

// Form data
const invoiceData = ref({
  invoiceNumber: "",
  clientId: "",
  projectId: "",
  issueDate: "",
  dueDate: "",
  taxRate: 10,
  notes: "",
  termsAndConditions:
    "Payment is due within 14 days of invoice date. Please make payment via bank transfer or credit card. Late payments may be subject to a 2% monthly fee.",
});

// Invoice items
const invoiceItems = ref<InvoiceItem[]>([]);

// Mock data for clients and projects
const clients = ref<Client[]>([
  {
    id: 1,
    name: "John Smith",
    company: "Acme Corporation",
    email: "john.smith@acmecorp.com",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 2,
    name: "Sarah Johnson",
    company: "TechStart Inc.",
    email: "sarah.j@techstart.io",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 3,
    name: "Michael Chen",
    company: "Innovate Solutions",
    email: "michael@innovatesolutions.com",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 4,
    name: "Emily Williams",
    company: "Design Masters",
    email: "emily@designmasters.co",
    avatar: "/api/placeholder/40/40",
  },
  {
    id: 5,
    name: "Robert Taylor",
    company: "Global Retail",
    email: "rtaylor@globalretail.com",
    avatar: "/api/placeholder/40/40",
  },
]);

const projects = ref<Project[]>([
  {
    id: 101,
    name: "Website Redesign",
    clientId: 1,
  },
  {
    id: 102,
    name: "Mobile App Development",
    clientId: 2,
  },
  {
    id: 103,
    name: "Database Migration",
    clientId: 3,
  },
  {
    id: 104,
    name: "E-commerce Platform",
    clientId: 4,
  },
  {
    id: 105,
    name: "CRM Implementation",
    clientId: 5,
  },
  {
    id: 106,
    name: "Marketing Automation",
    clientId: 1,
  },
]);

// Filtered projects based on selected client
const filteredProjects = computed(() => {
  if (!invoiceData.value.clientId) return [];

  const clientId = parseInt(invoiceData.value.clientId);
  return projects.value.filter((project) => project.clientId === clientId);
});

// Selected client
const selectedClient = computed(() => {
  if (!invoiceData.value.clientId) return null;

  const clientId = parseInt(invoiceData.value.clientId);
  return clients.value.find((client) => client.id === clientId) || null;
});

// Calculate item amount based on quantity and rate
const calculateItemAmount = (item: InvoiceItem) => {
  item.amount = parseFloat((item.quantity * item.rate).toFixed(2));
  return item;
};

// Watch for changes in quantity or rate to update amounts
watch(
  invoiceItems,
  () => {
    invoiceItems.value.forEach((item) => {
      calculateItemAmount(item);
    });
  },
  { deep: true }
);

// Add new invoice item
const addInvoiceItem = () => {
  const newId = Math.max(0, ...invoiceItems.value.map((item) => item.id)) + 1;
  invoiceItems.value.push({
    id: newId,
    description: "",
    quantity: 1,
    rate: 0,
    amount: 0,
  });
};

// Remove invoice item
const removeInvoiceItem = (index: number) => {
  if (invoiceItems.value.length > 1) {
    invoiceItems.value.splice(index, 1);
  }
};

// Invoice totals
const subtotal = computed(() => {
  return invoiceItems.value.reduce((sum, item) => sum + item.amount, 0);
});

const taxAmount = computed(() => {
  return parseFloat(
    (subtotal.value * (invoiceData.value.taxRate / 100)).toFixed(2)
  );
});

const totalAmount = computed(() => {
  return parseFloat((subtotal.value + taxAmount.value).toFixed(2));
});

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 2,
  }).format(amount);
};

// Format date
const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = {
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  return new Date(dateString).toLocaleDateString("en-US", options);
};

// Fetch invoice by ID
const fetchInvoice = async () => {
  formStatus.value = "loading";

  try {
    // In a real app, this would be an API call to fetch the invoice
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // For demonstration, we'll generate a mock invoice based on the ID
    const mockInvoices = [
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
        notes: "Database architecture planning and implementation guidance.",
        termsAndConditions:
          "Payment is due within 14 days of invoice date. Please make payment via bank transfer or credit card. Late payments may be subject to a 2% monthly fee.",
        projectId: 103,
        projectName: "Database Migration Project",
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
        notes: "Health tracking mobile app development services.",
        termsAndConditions:
          "Payment is due within 14 days of invoice date. Please make payment via bank transfer or credit card. Late payments may be subject to a 2% monthly fee.",
        projectId: 106,
        projectName: "Health Tracking App",
      },
    ];

    const invoice = mockInvoices.find((inv) => inv.id === invoiceId.value);

    if (!invoice) {
      throw new Error("Invoice not found");
    }

    originalInvoice.value = invoice;

    // Populate form data
    invoiceData.value.invoiceNumber = invoice.invoiceNumber;
    invoiceData.value.clientId =
      clients.value.find((c) => c.name === invoice.clientName)?.id.toString() ||
      "";
    invoiceData.value.projectId = invoice.projectId?.toString() || "";
    invoiceData.value.issueDate = invoice.issueDate;
    invoiceData.value.dueDate = invoice.dueDate;
    invoiceData.value.taxRate = invoice.taxRate;
    invoiceData.value.notes = invoice.notes || "";
    invoiceData.value.termsAndConditions = invoice.termsAndConditions || "";

    // Populate invoice items
    invoiceItems.value = [...invoice.items];

    formStatus.value = "idle";
  } catch (error) {
    console.error("Error fetching invoice:", error);
    formStatus.value = "error";
    errorMessage.value =
      "Failed to load invoice. Please try again or contact support.";
  }
};

// Validate form before submission
const validateForm = (): boolean => {
  // Basic validation
  if (!invoiceData.value.clientId) {
    errorMessage.value = "Please select a client";
    return false;
  }

  if (!invoiceData.value.issueDate) {
    errorMessage.value = "Please enter an issue date";
    return false;
  }

  if (!invoiceData.value.dueDate) {
    errorMessage.value = "Please enter a due date";
    return false;
  }

  // Validate invoice items
  if (invoiceItems.value.length === 0) {
    errorMessage.value = "Please add at least one invoice item";
    return false;
  }

  // Check if any item is missing description or has zero amount
  const invalidItems = invoiceItems.value.filter(
    (item) =>
      !item.description ||
      item.description.trim() === "" ||
      !item.quantity ||
      !item.rate
  );

  if (invalidItems.length > 0) {
    errorMessage.value = "Please fill in all item details with valid amounts";
    return false;
  }

  return true;
};

// Update invoice
const updateInvoice = async () => {
  if (!validateForm()) {
    return;
  }

  formStatus.value = "saving";

  try {
    // In a real app, this would make an API call to update the invoice
    await new Promise((resolve) => setTimeout(resolve, 1000)); // Simulated delay

    // Mock success
    formStatus.value = "success";

    // Navigate back to invoices list after a brief delay
    setTimeout(() => {
      router.push("/dashboard/consultant/invoices");
    }, 1500);
  } catch (error) {
    formStatus.value = "error";
    errorMessage.value =
      "An error occurred while updating the invoice. Please try again.";
    console.error("Error updating invoice:", error);
  }
};

// Send invoice (change status from draft to sent)
const sendInvoice = async () => {
  if (!validateForm()) {
    return;
  }

  formStatus.value = "saving";

  try {
    // In a real app, this would make an API call to send the invoice
    await new Promise((resolve) => setTimeout(resolve, 1500)); // Simulated delay

    // Mock success
    formStatus.value = "success";

    // Navigate back to invoices list after a brief delay
    setTimeout(() => {
      router.push("/dashboard/consultant/invoices");
    }, 1500);
  } catch (error) {
    formStatus.value = "error";
    errorMessage.value =
      "An error occurred while sending the invoice. Please try again.";
    console.error("Error sending invoice:", error);
  }
};

// Toggle invoice preview
const togglePreview = () => {
  if (!validateForm()) {
    return;
  }

  showPreview.value = !showPreview.value;
};

// Cancel and go back to invoices list
const cancelEdit = () => {
  router.push("/dashboard/consultant/invoices");
};

// Initialize form
onMounted(() => {
  fetchInvoice();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Edit Invoice {{ invoiceData.invoiceNumber }}
          </h1>
          <p class="text-gray-600">
            Update invoice details and send to your client
          </p>
        </div>
        <div>
          <button
            @click="cancelEdit"
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
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd"
              />
            </svg>
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="formStatus === 'loading'"
      class="flex items-center justify-center h-64"
    >
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      ></div>
    </div>

    <!-- Error Message -->
    <div
      v-else-if="formStatus === 'error' && errorMessage"
      class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6"
    >
      <div class="flex">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-red-500 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <div>
          <p class="font-medium">{{ errorMessage }}</p>
          <button
            @click="fetchInvoice"
            class="text-red-600 hover:text-red-800 underline text-sm mt-1"
          >
            Try Again
          </button>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div
      v-else-if="formStatus === 'success'"
      class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4"
    >
      <div class="flex">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-green-500 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <span>Invoice updated successfully. Redirecting...</span>
      </div>
    </div>

    <div v-else-if="!showPreview">
      <!-- Invoice Form -->
      <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Invoice Details
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Invoice Number -->
            <div>
              <label
                for="invoice-number"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Invoice Number</label
              >
              <input
                id="invoice-number"
                type="text"
                v-model="invoiceData.invoiceNumber"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                readonly
              />
            </div>

            <!-- Client Selection -->
            <div>
              <label
                for="client"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Client *</label
              >
              <select
                id="client"
                v-model="invoiceData.clientId"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">Select a client</option>
                <option
                  v-for="client in clients"
                  :key="client.id"
                  :value="client.id"
                >
                  {{ client.name }}
                  {{ client.company ? `(${client.company})` : "" }}
                </option>
              </select>
            </div>

            <!-- Project Selection -->
            <div>
              <label
                for="project"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Project</label
              >
              <select
                id="project"
                v-model="invoiceData.projectId"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                :disabled="!invoiceData.clientId"
              >
                <option value="">Select a project (optional)</option>
                <option
                  v-for="project in filteredProjects"
                  :key="project.id"
                  :value="project.id"
                >
                  {{ project.name }}
                </option>
              </select>
            </div>

            <!-- Issue Date -->
            <div>
              <label
                for="issue-date"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Issue Date *</label
              >
              <input
                id="issue-date"
                type="date"
                v-model="invoiceData.issueDate"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Due Date -->
            <div>
              <label
                for="due-date"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Due Date *</label
              >
              <input
                id="due-date"
                type="date"
                v-model="invoiceData.dueDate"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <!-- Tax Rate -->
            <div>
              <label
                for="tax-rate"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Tax Rate (%)</label
              >
              <input
                id="tax-rate"
                type="number"
                min="0"
                max="100"
                step="0.1"
                v-model="invoiceData.taxRate"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Invoice Items -->
      <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Invoice Items</h2>
            <button
              @click="addInvoiceItem"
              class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
              Add Item
            </button>
          </div>

          <!-- Items Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th
                    scope="col"
                    class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2"
                  >
                    Description *
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Quantity *
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Rate *
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Amount
                  </th>
                  <th
                    scope="col"
                    class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-10"
                  ></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="(item, index) in invoiceItems"
                  :key="item.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-3 py-2">
                    <input
                      type="text"
                      v-model="item.description"
                      placeholder="Item description"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    />
                  </td>
                  <td class="px-3 py-2">
                    <input
                      type="number"
                      v-model="item.quantity"
                      min="1"
                      step="0.01"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right"
                    />
                  </td>
                  <td class="px-3 py-2">
                    <input
                      type="number"
                      v-model="item.rate"
                      min="0"
                      step="0.01"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm text-right"
                    />
                  </td>
                  <td class="px-3 py-2 text-right font-medium">
                    {{ formatCurrency(item.amount || 0) }}
                  </td>
                  <td class="px-3 py-2 text-right">
                    <button
                      @click="removeInvoiceItem(index)"
                      class="text-red-600 hover:text-red-800"
                      :disabled="invoiceItems.length <= 1"
                      :class="{
                        'opacity-50 cursor-not-allowed':
                          invoiceItems.length <= 1,
                      }"
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
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="border-t-2 border-gray-200">
                  <td colspan="3" class="px-3 py-2 text-right font-medium">
                    Subtotal:
                  </td>
                  <td class="px-3 py-2 text-right font-medium">
                    {{ formatCurrency(subtotal) }}
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="3" class="px-3 py-2 text-right font-medium">
                    Tax ({{ invoiceData.taxRate }}%):
                  </td>
                  <td class="px-3 py-2 text-right font-medium">
                    {{ formatCurrency(taxAmount) }}
                  </td>
                  <td></td>
                </tr>
                <tr class="bg-gray-50">
                  <td colspan="3" class="px-3 py-2 text-right font-bold">
                    Total:
                  </td>
                  <td class="px-3 py-2 text-right font-bold">
                    {{ formatCurrency(totalAmount) }}
                  </td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- Additional Details -->
      <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Additional Details
          </h2>

          <div class="grid grid-cols-1 gap-6">
            <!-- Notes -->
            <div>
              <label
                for="notes"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Notes</label
              >
              <textarea
                id="notes"
                v-model="invoiceData.notes"
                rows="3"
                placeholder="Any additional information for the client..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              ></textarea>
            </div>

            <!-- Terms and Conditions -->
            <div>
              <label
                for="terms"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Terms and Conditions</label
              >
              <textarea
                id="terms"
                v-model="invoiceData.termsAndConditions"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="flex justify-end space-x-3">
        <button
          @click="updateInvoice"
          class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="formStatus === 'saving'"
        >
          <span v-if="formStatus !== 'saving'">Update Draft</span>
          <span v-else class="flex items-center">
            <svg
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700"
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
            Updating...
          </span>
        </button>

        <button
          @click="togglePreview"
          class="px-4 py-2 border border-indigo-600 shadow-sm text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Preview Invoice
        </button>

        <button
          @click="sendInvoice"
          class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="formStatus === 'saving'"
        >
          <span v-if="formStatus !== 'saving'">Update & Send</span>
          <span v-else class="flex items-center">
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
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Sending...
          </span>
        </button>
      </div>
    </div>

    <!-- Invoice Preview -->
    <div v-else class="relative">
      <!-- Back to Edit button -->
      <div class="absolute top-0 right-0">
        <button
          @click="togglePreview"
          class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <span class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
              />
            </svg>
            Back to Edit
          </span>
        </button>
      </div>

      <!-- Invoice Document -->
      <div
        class="bg-white rounded-lg shadow border border-gray-200 p-8 max-w-4xl mx-auto"
      >
        <!-- Invoice Header -->
        <div class="flex justify-between items-center mb-10">
          <div>
            <div class="text-3xl font-bold text-gray-900 mb-1">INVOICE</div>
            <div class="text-gray-500">{{ invoiceData.invoiceNumber }}</div>
          </div>
          <div class="text-right">
            <div class="text-xl font-bold text-indigo-600 mb-1">
              LearnConsult
            </div>
            <div class="text-gray-500">consultant@learnconsult.com</div>
            <div class="text-gray-500">+1 (555) 123-4567</div>
          </div>
        </div>

        <!-- Client and Invoice Info -->
        <div class="grid grid-cols-2 gap-8 mb-10">
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">BILL TO</div>
            <div class="font-medium">{{ selectedClient?.name }}</div>
            <div v-if="selectedClient?.company">
              {{ selectedClient?.company }}
            </div>
            <div>{{ selectedClient?.email }}</div>
          </div>

          <div class="text-right">
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div class="text-gray-500 font-medium">Issue Date:</div>
              <div>{{ formatDate(invoiceData.issueDate) }}</div>

              <div class="text-gray-500 font-medium">Due Date:</div>
              <div>{{ formatDate(invoiceData.dueDate) }}</div>

              <div class="text-gray-500 font-medium">Amount Due:</div>
              <div class="font-bold text-base">
                {{ formatCurrency(totalAmount) }}
              </div>

              <div
                v-if="invoiceData.projectId"
                class="text-gray-500 font-medium"
              >
                Project:
              </div>
              <div v-if="invoiceData.projectId">
                {{
                  (
                    filteredProjects.find(
                      (p) => p.id === parseInt(invoiceData.projectId)
                    ) || {}
                  ).name
                }}
              </div>
            </div>
          </div>
        </div>

        <!-- Invoice Items -->
        <div class="mb-8">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th
                  scope="col"
                  class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Description
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Quantity
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Rate
                </th>
                <th
                  scope="col"
                  class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Amount
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in invoiceItems" :key="item.id">
                <td class="px-4 py-3">
                  <div class="text-sm text-gray-900">
                    {{ item.description }}
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 text-right">
                  {{ item.quantity }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 text-right">
                  {{ formatCurrency(item.rate || 0) }}
                </td>
                <td
                  class="px-4 py-3 text-sm text-gray-900 text-right font-medium"
                >
                  {{ formatCurrency(item.amount || 0) }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td
                  colspan="3"
                  class="px-4 py-3 text-sm font-medium text-right border-t border-gray-200"
                >
                  Subtotal:
                </td>
                <td
                  class="px-4 py-3 text-sm font-medium text-right border-t border-gray-200"
                >
                  {{ formatCurrency(subtotal) }}
                </td>
              </tr>
              <tr>
                <td
                  colspan="3"
                  class="px-4 py-3 text-sm font-medium text-right"
                >
                  Tax ({{ invoiceData.taxRate }}%):
                </td>
                <td class="px-4 py-3 text-sm font-medium text-right">
                  {{ formatCurrency(taxAmount) }}
                </td>
              </tr>
              <tr class="bg-gray-50">
                <td
                  colspan="3"
                  class="px-4 py-3 text-base font-bold text-right"
                >
                  Total:
                </td>
                <td class="px-4 py-3 text-base font-bold text-right">
                  {{ formatCurrency(totalAmount) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Notes and Terms -->
        <div class="grid grid-cols-1 gap-6">
          <div v-if="invoiceData.notes">
            <div class="text-sm font-medium text-gray-700 mb-1">NOTES</div>
            <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded">
              {{ invoiceData.notes }}
            </div>
          </div>

          <div>
            <div class="text-sm font-medium text-gray-700 mb-1">
              TERMS AND CONDITIONS
            </div>
            <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded">
              {{ invoiceData.termsAndConditions }}
            </div>
          </div>
        </div>

        <!-- Thank You Note -->
        <div class="text-center text-gray-500 mt-10">
          Thank you for your business!
        </div>
      </div>

      <!-- Form Actions -->
      <div class="flex justify-center space-x-3 mt-8">
        <button
          @click="updateInvoice"
          class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="formStatus === 'saving'"
        >
          <span v-if="formStatus !== 'saving'">Update Draft</span>
          <span v-else class="flex items-center">
            <svg
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700"
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
            Updating...
          </span>
        </button>

        <button
          @click="sendInvoice"
          class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="formStatus === 'saving'"
        >
          <span v-if="formStatus !== 'saving'">Update & Send</span>
          <span v-else class="flex items-center">
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
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Sending...
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animations */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
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
</style>
