<!-- pages/dashboard/admin/settings/index.vue -->
<script setup lang="ts">
import { ref, reactive, watch, onMounted } from "vue";

// Define page meta
definePageMeta({
  layout: "dashboard-admin",
});

// Page title and meta
useHead({
  title: "System Settings - Admin Dashboard",
  meta: [
    {
      name: "description",
      content: "Configure global system settings for the platform",
    },
  ],
});

// Settings categories
const categories = [
  { id: "general", label: "General Settings", icon: "cog" },
  { id: "security", label: "Security & Privacy", icon: "shield-check" },
  { id: "users", label: "User Management", icon: "users" },
  { id: "courses", label: "Course Settings", icon: "book-open" },
  { id: "consultancy", label: "Consultancy Settings", icon: "briefcase" },
  { id: "payments", label: "Payment Gateway", icon: "credit-card" },
  { id: "email", label: "Email Templates", icon: "mail" },
  { id: "integrations", label: "Integrations", icon: "link" },
  { id: "maintenance", label: "Maintenance", icon: "tool" },
];

// Active category
const activeCategory = ref("general");

// Settings data (mock data for now)
const settings = reactive({
  general: {
    siteName: "LearnConsult",
    siteDescription: "E-Learning & Consultancy Platform",
    logo: "/api/placeholder/100/100",
    favicon: "/api/placeholder/32/32",
    timezone: "UTC",
    dateFormat: "MM/DD/YYYY",
    timeFormat: "12h",
    defaultLanguage: "en",
    supportEmail: "support@learnconsult.com",
    contactPhone: "+1 (555) 123-4567",
    enableMaintenance: false,
    maintenanceMessage:
      "We are currently performing scheduled maintenance. Please check back soon.",
  },
  security: {
    passwordMinLength: 8,
    passwordRequireUppercase: true,
    passwordRequireNumber: true,
    passwordRequireSpecial: true,
    sessionTimeout: 60,
    failedLoginAttempts: 5,
    accountLockoutDuration: 30,
    twoFactorAuth: false,
    twoFactorAuthMethods: ["email", "authenticator"],
    ipBlacklist: "",
    adminAccessIPs: "",
    dataRetentionDays: 90,
    enforceSsl: true,
    privacyPolicyLastUpdated: "2025-03-15",
    termsLastUpdated: "2025-03-15",
  },
  users: {
    allowSignup: true,
    requireEmailVerification: true,
    autoApproveUsers: false,
    defaultUserRole: "student",
    allowMultipleRoles: false,
    allowProfilePhotos: true,
    allowSocialLogin: true,
    allowDeleteAccount: true,
    inactiveUserDays: 365,
    instructorApplicationRequiresApproval: true,
    consultantApplicationRequiresApproval: true,
  },
  courses: {
    autoPublishCourses: false,
    requireCourseApproval: true,
    maxCourseSizeMB: 5000,
    maxVideoSizeMB: 500,
    maxAttachmentSizeMB: 50,
    maxCategoriesPerCourse: 3,
    allowFreeCourses: true,
    minimumCoursePrice: 4.99,
    courseCommissionPercentage: 15,
    instructorPaymentThreshold: 50,
    paymentSchedule: "monthly",
    certificatesEnabled: true,
    certificateTemplate: "default",
  },
  consultancy: {
    minConsultancyRate: 25,
    maxConsultancyRate: 500,
    consultancyCommissionPercentage: 10,
    allowInstantBooking: true,
    minAdvanceBookingHours: 24,
    maxCancellationWindowHours: 48,
    refundPolicy: "full",
    consultantPaymentThreshold: 100,
    consultantPaymentSchedule: "bi-weekly",
    requireContractSigning: true,
    autoCancelNoShowAppointments: true,
  },
  payments: {
    currency: "USD",
    currencySymbol: "$",
    decimals: 2,
    paymentGateways: ["stripe", "paypal"],
    stripeEnabled: true,
    stripePublicKey: "pk_test_123456789",
    stripeSecretKey: "••••••••••••••••••••••••",
    paypalEnabled: true,
    paypalClientId: "client_id_123456789",
    paypalSecret: "••••••••••••••••••••",
    taxRate: 0,
    collectTaxInfo: false,
    invoicePrefix: "INV-",
  },
  email: {
    emailProvider: "smtp",
    smtpHost: "smtp.example.com",
    smtpPort: 587,
    smtpUsername: "noreply@learnconsult.com",
    smtpPassword: "••••••••••••",
    smtpEncryption: "tls",
    senderEmail: "noreply@learnconsult.com",
    senderName: "LearnConsult Platform",
    emailFooter: "© 2025 LearnConsult. All rights reserved.",
    welcomeEmailTemplate: "<p>Welcome to LearnConsult!</p>",
    passwordResetTemplate:
      "<p>Click the link below to reset your password:</p>",
    verificationEmailTemplate: "<p>Please verify your email address:</p>",
    courseEnrollmentTemplate:
      "<p>You have successfully enrolled in a course!</p>",
    appointmentBookingTemplate:
      "<p>Your consultation appointment has been booked.</p>",
  },
  integrations: {
    googleAnalyticsId: "UA-123456789-1",
    googleMapsApiKey: "••••••••••••••••••••••••",
    zoomIntegration: true,
    zoomApiKey: "••••••••••••••••••••••••",
    zoomApiSecret: "••••••••••••••••••••••••",
    slackWebhookUrl: "https://hooks.slack.com/services/XXXXXX/XXXXXX/XXXXXX",
    mailchimpApiKey: "••••••••••••••••••••••••",
    mailchimpListId: "abc123def456",
    githubIntegration: false,
    githubClientId: "",
    githubClientSecret: "",
  },
  maintenance: {
    enableAutoBackup: true,
    backupFrequency: "daily",
    backupRetentionDays: 30,
    logLevel: "error",
    errorReportingEmail: "errors@learnconsult.com",
    purgeInactiveAccounts: false,
    purgeInactiveAccountsDays: 730,
    purgeDeletedContent: true,
    purgeDeletedContentDays: 30,
    autoUpdateSystem: false,
    maintenanceWindow: "sunday",
    maintenanceTimeStart: "02:00",
    maintenanceTimeEnd: "04:00",
  },
});

// Password field states
const showPassword = reactive({
  stripeSecretKey: false,
  paypalSecret: false,
  smtpPassword: false,
  googleMapsApiKey: false,
  zoomApiKey: false,
  zoomApiSecret: false,
  mailchimpApiKey: false,
  githubClientSecret: false,
});

// Form states
const isSubmitting = ref(false);
const hasChanges = ref(false);
const showResetConfirm = ref(false);
const originalSettings = JSON.stringify(settings);

// Toast notification state
const toast = reactive({
  show: false,
  message: "",
  type: "success",
});

// Track if settings have changed
watch(
  settings,
  () => {
    hasChanges.value = JSON.stringify(settings) !== originalSettings;
  },
  { deep: true }
);

// Method to show toast message
const showToast = (
  message: string,
  type: "success" | "error" | "warning" = "success"
) => {
  toast.message = message;
  toast.type = type;
  toast.show = true;

  // Auto-hide toast after 5 seconds
  setTimeout(() => {
    toast.show = false;
  }, 5000);
};

// Method to save settings
const saveSettings = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Update original settings after save
    originalSettings = JSON.stringify(settings);
    hasChanges.value = false;

    showToast("Settings saved successfully");
  } catch (error) {
    showToast("Failed to save settings. Please try again.", "error");
    console.error("Error saving settings:", error);
  } finally {
    isSubmitting.value = false;
  }
};

// Method to reset settings to default
const resetSettings = () => {
  showResetConfirm.value = true;
};

// Confirm reset settings
const confirmResetSettings = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Reset settings to default (would be from API in real app)
    // For now, just show success message
    showToast("Settings reset to default values");
    showResetConfirm.value = false;
  } catch (error) {
    showToast("Failed to reset settings. Please try again.", "error");
    console.error("Error resetting settings:", error);
  } finally {
    isSubmitting.value = false;
  }
};

// For demo: refresh API keys
const refreshApiKey = async (key: string) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    showToast(`${key} API key refreshed successfully`);
    hasChanges.value = true;
  } catch (error) {
    showToast(`Failed to refresh ${key} API key`, "error");
  } finally {
    isSubmitting.value = false;
  }
};

// Test email configuration
const testEmailConfig = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    showToast("Test email sent successfully");
  } catch (error) {
    showToast(
      "Failed to send test email. Please check your configuration.",
      "error"
    );
  } finally {
    isSubmitting.value = false;
  }
};

// Method to toggle password visibility
const togglePasswordVisibility = (key: string) => {
  showPassword[key] = !showPassword[key];
};

// Method to get icon HTML based on icon name
const getIcon = (iconName: string): string => {
  switch (iconName) {
    case "cog":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>`;
    case "shield-check":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "users":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
      </svg>`;
    case "book-open":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
      </svg>`;
    case "briefcase":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
        <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
      </svg>`;
    case "credit-card":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
      </svg>`;
    case "mail":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
      </svg>`;
    case "link":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
      </svg>`;
    case "tool":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
          <p class="mt-1 text-sm text-gray-500">
            Configure global settings for the platform
          </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <button
            v-if="hasChanges"
            @click="resetSettings"
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="isSubmitting"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1.5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
            Reset Changes
          </button>
          <button
            @click="saveSettings"
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="isSubmitting || !hasChanges"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1.5"
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
            Save Settings
          </button>
        </div>
      </div>
    </div>

    <!-- Settings Panel -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="md:grid md:grid-cols-4">
        <!-- Sidebar (Category Navigation) -->
        <div class="md:col-span-1 border-r border-gray-200">
          <nav class="py-4">
            <div class="px-4 mb-4">
              <h3
                class="text-xs font-semibold text-gray-500 uppercase tracking-wider"
              >
                Settings Categories
              </h3>
            </div>
            <ul>
              <li
                v-for="category in categories"
                :key="category.id"
                class="mt-1"
              >
                <button
                  @click="activeCategory = category.id"
                  class="flex items-center px-6 py-3 w-full text-left"
                  :class="
                    activeCategory === category.id
                      ? 'bg-indigo-50 text-indigo-600 border-l-4 border-indigo-600'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                  "
                >
                  <span class="mr-3" v-html="getIcon(category.icon)"></span>
                  <span class="text-sm font-medium">{{ category.label }}</span>
                </button>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Settings Content Area -->
        <div class="md:col-span-3 p-6">
          <!-- General Settings -->
          <div v-if="activeCategory === 'general'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              General Settings
            </h2>

            <div class="space-y-8">
              <!-- Platform Identity Card -->
              <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                  <h3 class="text-lg font-medium leading-6 text-gray-900">
                    Platform Identity
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Main information displayed across the platform
                  </p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <!-- Logo & Branding -->
                    <div class="col-span-1 md:col-span-2">
                      <label
                        class="block text-sm font-medium text-gray-700 mb-4"
                        >Platform Logo</label
                      >
                      <div class="flex items-center">
                        <div
                          class="w-20 h-20 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center"
                        >
                          <img
                            :src="settings.general.logo"
                            alt="Site Logo"
                            class="max-h-full max-w-full"
                          />
                        </div>
                        <div class="ml-5 space-y-2">
                          <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4 mr-2"
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
                            Change Logo
                          </button>

                          <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-4 w-4 mr-2"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke="currentColor"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                              />
                            </svg>
                            Upload Favicon
                          </button>
                        </div>
                      </div>
                      <div class="mt-4 flex items-center">
                        <div
                          class="w-8 h-8 overflow-hidden rounded border border-gray-200 bg-gray-50 flex items-center justify-center"
                        >
                          <img
                            :src="settings.general.favicon"
                            alt="Favicon"
                            class="max-h-full max-w-full"
                          />
                        </div>
                        <span class="ml-2 text-xs text-gray-500"
                          >Current favicon</span
                        >
                      </div>
                    </div>

                    <!-- Platform Information -->
                    <div class="col-span-1 md:col-span-4 space-y-6">
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                          <label
                            for="siteName"
                            class="block text-sm font-medium text-gray-700"
                            >Platform Name</label
                          >
                          <input
                            id="siteName"
                            v-model="settings.general.siteName"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          />
                          <p class="mt-1 text-xs text-gray-500">
                            Displayed in browser tabs and across the platform
                          </p>
                        </div>

                        <div>
                          <label
                            for="siteDescription"
                            class="block text-sm font-medium text-gray-700"
                            >Platform Description</label
                          >
                          <input
                            id="siteDescription"
                            v-model="settings.general.siteDescription"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          />
                          <p class="mt-1 text-xs text-gray-500">
                            Used for SEO and platform descriptions
                          </p>
                        </div>
                      </div>

                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                          <label
                            for="supportEmail"
                            class="block text-sm font-medium text-gray-700"
                            >Support Email</label
                          >
                          <div class="mt-1 relative rounded-md shadow-sm">
                            <div
                              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                              </svg>
                            </div>
                            <input
                              id="supportEmail"
                              v-model="settings.general.supportEmail"
                              type="email"
                              class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                          </div>
                          <p class="mt-1 text-xs text-gray-500">
                            Shown in contact forms and support sections
                          </p>
                        </div>

                        <div>
                          <label
                            for="contactPhone"
                            class="block text-sm font-medium text-gray-700"
                            >Contact Phone</label
                          >
                          <div class="mt-1 relative rounded-md shadow-sm">
                            <div
                              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                />
                              </svg>
                            </div>
                            <input
                              id="contactPhone"
                              v-model="settings.general.contactPhone"
                              type="text"
                              class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                          </div>
                          <p class="mt-1 text-xs text-gray-500">
                            Display phone for direct contact
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Localization Settings Card -->
              <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                  <h3 class="text-lg font-medium leading-6 text-gray-900">
                    Localization Settings
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Regional settings for dates, times, and language
                  </p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                      <label
                        for="timezone"
                        class="block text-sm font-medium text-gray-700"
                        >Default Timezone</label
                      >
                      <select
                        id="timezone"
                        v-model="settings.general.timezone"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      >
                        <option value="UTC">
                          UTC (Coordinated Universal Time)
                        </option>
                        <option value="America/New_York">
                          Eastern Time (ET) - New York
                        </option>
                        <option value="America/Chicago">
                          Central Time (CT) - Chicago
                        </option>
                        <option value="America/Denver">
                          Mountain Time (MT) - Denver
                        </option>
                        <option value="America/Los_Angeles">
                          Pacific Time (PT) - Los Angeles
                        </option>
                        <option value="Europe/London">London (GMT/BST)</option>
                        <option value="Europe/Paris">
                          Central European (CET/CEST) - Paris
                        </option>
                        <option value="Asia/Tokyo">Japan (JST) - Tokyo</option>
                        <option value="Australia/Sydney">
                          Australia Eastern - Sydney
                        </option>
                      </select>
                      <p class="mt-1 text-xs text-gray-500">
                        System default timezone for users
                      </p>
                    </div>

                    <div>
                      <label
                        for="dateFormat"
                        class="block text-sm font-medium text-gray-700"
                        >Date Format</label
                      >
                      <div class="mt-1 relative">
                        <select
                          id="dateFormat"
                          v-model="settings.general.dateFormat"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                          <option value="MM/DD/YYYY">
                            MM/DD/YYYY (12/31/2025)
                          </option>
                          <option value="DD/MM/YYYY">
                            DD/MM/YYYY (31/12/2025)
                          </option>
                          <option value="YYYY-MM-DD">
                            YYYY-MM-DD (2025-12-31)
                          </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                          How dates will be displayed throughout the platform
                        </p>
                      </div>
                    </div>

                    <div>
                      <label
                        for="timeFormat"
                        class="block text-sm font-medium text-gray-700"
                        >Time Format</label
                      >
                      <div class="mt-1 relative">
                        <select
                          id="timeFormat"
                          v-model="settings.general.timeFormat"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                          <option value="12h">12-hour (2:30 PM)</option>
                          <option value="24h">24-hour (14:30)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                          How times will be displayed throughout the platform
                        </p>
                      </div>
                    </div>

                    <div>
                      <label
                        for="defaultLanguage"
                        class="block text-sm font-medium text-gray-700"
                        >Default Language</label
                      >
                      <div class="mt-1 relative">
                        <select
                          id="defaultLanguage"
                          v-model="settings.general.defaultLanguage"
                          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                          <option value="en">English</option>
                          <option value="es">Spanish (Español)</option>
                          <option value="fr">French (Français)</option>
                          <option value="de">German (Deutsch)</option>
                          <option value="zh">Chinese (中文)</option>
                          <option value="ja">Japanese (日本語)</option>
                          <option value="ar">Arabic (العربية)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                          Platform default language for new users
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Maintenance Mode Card -->
              <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                  <h3 class="text-lg font-medium leading-6 text-gray-900">
                    Maintenance Mode
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Control platform availability for maintenance
                  </p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="enableMaintenance"
                        v-model="settings.general.enableMaintenance"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="enableMaintenance"
                        class="font-medium text-gray-700"
                        >Enable Maintenance Mode</label
                      >
                      <p class="text-gray-500">
                        When enabled, the site will be unavailable to regular
                        users and will display the maintenance message.
                      </p>
                    </div>
                  </div>

                  <div v-if="settings.general.enableMaintenance" class="mt-5">
                    <div>
                      <label
                        for="maintenanceMessage"
                        class="block text-sm font-medium text-gray-700"
                        >Maintenance Message</label
                      >
                      <div class="mt-1">
                        <textarea
                          id="maintenanceMessage"
                          v-model="settings.general.maintenanceMessage"
                          rows="4"
                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                          placeholder="We are currently performing scheduled maintenance. Please check back soon."
                        ></textarea>
                      </div>
                      <p class="mt-1 text-sm text-gray-500">
                        This message will be displayed to users during
                        maintenance.
                      </p>
                    </div>

                    <div
                      class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4"
                    >
                      <div class="flex">
                        <div class="flex-shrink-0">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-yellow-400"
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
                        <div class="ml-3">
                          <p class="text-sm text-yellow-700">
                            <strong>Note:</strong> Maintenance mode will make
                            the site inaccessible to all regular users. Only
                            administrators will be able to access the platform
                            during maintenance.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Settings -->
          <div v-if="activeCategory === 'security'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Security & Privacy Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Password Requirements
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="passwordMinLength"
                    class="block text-sm font-medium text-gray-700"
                    >Minimum Password Length</label
                  >
                  <input
                    id="passwordMinLength"
                    v-model.number="settings.security.passwordMinLength"
                    type="number"
                    min="6"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div class="flex flex-col justify-end space-y-2">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="passwordRequireUppercase"
                        v-model="settings.security.passwordRequireUppercase"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="passwordRequireUppercase"
                        class="font-medium text-gray-700"
                        >Require Uppercase Letter</label
                      >
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="passwordRequireNumber"
                        v-model="settings.security.passwordRequireNumber"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="passwordRequireNumber"
                        class="font-medium text-gray-700"
                        >Require Number</label
                      >
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="passwordRequireSpecial"
                        v-model="settings.security.passwordRequireSpecial"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="passwordRequireSpecial"
                        class="font-medium text-gray-700"
                        >Require Special Character</label
                      >
                    </div>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Session & Login Settings
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label
                    for="sessionTimeout"
                    class="block text-sm font-medium text-gray-700"
                    >Session Timeout (minutes)</label
                  >
                  <input
                    id="sessionTimeout"
                    v-model.number="settings.security.sessionTimeout"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="failedLoginAttempts"
                    class="block text-sm font-medium text-gray-700"
                    >Failed Login Attempts</label
                  >
                  <input
                    id="failedLoginAttempts"
                    v-model.number="settings.security.failedLoginAttempts"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Number of failed attempts before account lockout
                  </p>
                </div>

                <div>
                  <label
                    for="accountLockoutDuration"
                    class="block text-sm font-medium text-gray-700"
                    >Account Lockout Duration (minutes)</label
                  >
                  <input
                    id="accountLockoutDuration"
                    v-model.number="settings.security.accountLockoutDuration"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
              </div>

              <div class="flex items-start mt-4">
                <div class="flex items-center h-5">
                  <input
                    id="twoFactorAuth"
                    v-model="settings.security.twoFactorAuth"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="twoFactorAuth" class="font-medium text-gray-700"
                    >Enable Two-Factor Authentication</label
                  >
                  <p class="text-gray-500">
                    Require users to verify their identity through a second
                    method when signing in.
                  </p>
                </div>
              </div>

              <div v-if="settings.security.twoFactorAuth" class="pl-7 mt-2">
                <fieldset>
                  <legend class="text-sm font-medium text-gray-700">
                    Two-Factor Authentication Methods
                  </legend>
                  <div class="mt-2 space-y-2">
                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input
                          id="twoFactorAuthEmail"
                          v-model="settings.security.twoFactorAuthMethods"
                          type="checkbox"
                          value="email"
                          class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        />
                      </div>
                      <div class="ml-3 text-sm">
                        <label
                          for="twoFactorAuthEmail"
                          class="font-medium text-gray-700"
                          >Email</label
                        >
                      </div>
                    </div>

                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input
                          id="twoFactorAuthAuthenticator"
                          v-model="settings.security.twoFactorAuthMethods"
                          type="checkbox"
                          value="authenticator"
                          class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        />
                      </div>
                      <div class="ml-3 text-sm">
                        <label
                          for="twoFactorAuthAuthenticator"
                          class="font-medium text-gray-700"
                          >Authenticator App</label
                        >
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Access Restrictions
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="ipBlacklist"
                    class="block text-sm font-medium text-gray-700"
                    >IP Blacklist</label
                  >
                  <textarea
                    id="ipBlacklist"
                    v-model="settings.security.ipBlacklist"
                    rows="3"
                    placeholder="Enter IP addresses, one per line"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  ></textarea>
                  <p class="mt-1 text-xs text-gray-500">
                    IP addresses that will be blocked from accessing the
                    platform
                  </p>
                </div>

                <div>
                  <label
                    for="adminAccessIPs"
                    class="block text-sm font-medium text-gray-700"
                    >Admin Access IPs (Optional)</label
                  >
                  <textarea
                    id="adminAccessIPs"
                    v-model="settings.security.adminAccessIPs"
                    rows="3"
                    placeholder="Enter IP addresses, one per line"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  ></textarea>
                  <p class="mt-1 text-xs text-gray-500">
                    If provided, admin access will be restricted to these IP
                    addresses only
                  </p>
                </div>
              </div>

              <div class="flex items-start mt-4">
                <div class="flex items-center h-5">
                  <input
                    id="enforceSsl"
                    v-model="settings.security.enforceSsl"
                    type="checkbox"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label for="enforceSsl" class="font-medium text-gray-700"
                    >Enforce SSL</label
                  >
                  <p class="text-gray-500">
                    Always redirect users to the secure HTTPS version of the
                    site.
                  </p>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Data Privacy
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="dataRetentionDays"
                    class="block text-sm font-medium text-gray-700"
                    >Data Retention Period (days)</label
                  >
                  <input
                    id="dataRetentionDays"
                    v-model.number="settings.security.dataRetentionDays"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    How long to keep deleted user data before permanent removal
                  </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label
                      for="privacyPolicyLastUpdated"
                      class="block text-sm font-medium text-gray-700"
                      >Privacy Policy Last Updated</label
                    >
                    <input
                      id="privacyPolicyLastUpdated"
                      v-model="settings.security.privacyPolicyLastUpdated"
                      type="date"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="termsLastUpdated"
                      class="block text-sm font-medium text-gray-700"
                      >Terms of Service Last Updated</label
                    >
                    <input
                      id="termsLastUpdated"
                      v-model="settings.security.termsLastUpdated"
                      type="date"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- User Management Settings -->
          <div v-if="activeCategory === 'users'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              User Management Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Registration & Account Settings
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="allowSignup"
                      v-model="settings.users.allowSignup"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="allowSignup" class="font-medium text-gray-700"
                      >Allow Public Signup</label
                    >
                    <p class="text-gray-500">
                      When disabled, only admins can create new user accounts.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="requireEmailVerification"
                      v-model="settings.users.requireEmailVerification"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="requireEmailVerification"
                      class="font-medium text-gray-700"
                      >Require Email Verification</label
                    >
                    <p class="text-gray-500">
                      New users must verify their email address before accessing
                      the platform.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="autoApproveUsers"
                      v-model="settings.users.autoApproveUsers"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="autoApproveUsers"
                      class="font-medium text-gray-700"
                      >Auto-Approve New Users</label
                    >
                    <p class="text-gray-500">
                      When disabled, new users require admin approval before
                      accessing the platform.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="allowDeleteAccount"
                      v-model="settings.users.allowDeleteAccount"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="allowDeleteAccount"
                      class="font-medium text-gray-700"
                      >Allow Users to Delete Account</label
                    >
                    <p class="text-gray-500">
                      Users can delete their own accounts.
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label
                      for="defaultUserRole"
                      class="block text-sm font-medium text-gray-700"
                      >Default User Role</label
                    >
                    <select
                      id="defaultUserRole"
                      v-model="settings.users.defaultUserRole"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                      <option value="student">Student</option>
                      <option value="client">Client</option>
                    </select>
                  </div>

                  <div>
                    <label
                      for="inactiveUserDays"
                      class="block text-sm font-medium text-gray-700"
                      >Inactive User Period (days)</label
                    >
                    <input
                      id="inactiveUserDays"
                      v-model.number="settings.users.inactiveUserDays"
                      type="number"
                      min="1"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                      Number of days after which users are considered inactive
                    </p>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Role Applications
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="instructorApplicationRequiresApproval"
                      v-model="
                        settings.users.instructorApplicationRequiresApproval
                      "
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="instructorApplicationRequiresApproval"
                      class="font-medium text-gray-700"
                      >Instructor Applications Require Approval</label
                    >
                    <p class="text-gray-500">
                      Users must be approved before becoming instructors.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="consultantApplicationRequiresApproval"
                      v-model="
                        settings.users.consultantApplicationRequiresApproval
                      "
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="consultantApplicationRequiresApproval"
                      class="font-medium text-gray-700"
                      >Consultant Applications Require Approval</label
                    >
                    <p class="text-gray-500">
                      Users must be approved before becoming consultants.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="allowMultipleRoles"
                      v-model="settings.users.allowMultipleRoles"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="allowMultipleRoles"
                      class="font-medium text-gray-700"
                      >Allow Multiple Roles</label
                    >
                    <p class="text-gray-500">
                      Users can have more than one role (e.g., both student and
                      client).
                    </p>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Profile Settings
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="allowProfilePhotos"
                      v-model="settings.users.allowProfilePhotos"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="allowProfilePhotos"
                      class="font-medium text-gray-700"
                      >Allow Profile Photos</label
                    >
                    <p class="text-gray-500">
                      Users can upload profile photos.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="allowSocialLogin"
                      v-model="settings.users.allowSocialLogin"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="allowSocialLogin"
                      class="font-medium text-gray-700"
                      >Allow Social Login</label
                    >
                    <p class="text-gray-500">
                      Users can sign in using social providers (Google,
                      Facebook, etc.)
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Course Settings -->
          <div v-if="activeCategory === 'courses'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Course Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Publication Settings
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="autoPublishCourses"
                      v-model="settings.courses.autoPublishCourses"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="autoPublishCourses"
                      class="font-medium text-gray-700"
                      >Auto-Publish Courses</label
                    >
                    <p class="text-gray-500">
                      Courses are automatically published once submitted.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="requireCourseApproval"
                      v-model="settings.courses.requireCourseApproval"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      :disabled="settings.courses.autoPublishCourses"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="requireCourseApproval"
                      class="font-medium text-gray-700"
                      >Require Course Approval</label
                    >
                    <p class="text-gray-500">
                      Courses must be reviewed and approved by an admin before
                      publishing.
                    </p>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Content Limits
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label
                    for="maxCourseSizeMB"
                    class="block text-sm font-medium text-gray-700"
                    >Max Course Size (MB)</label
                  >
                  <input
                    id="maxCourseSizeMB"
                    v-model.number="settings.courses.maxCourseSizeMB"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="maxVideoSizeMB"
                    class="block text-sm font-medium text-gray-700"
                    >Max Video Size (MB)</label
                  >
                  <input
                    id="maxVideoSizeMB"
                    v-model.number="settings.courses.maxVideoSizeMB"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="maxAttachmentSizeMB"
                    class="block text-sm font-medium text-gray-700"
                    >Max Attachment Size (MB)</label
                  >
                  <input
                    id="maxAttachmentSizeMB"
                    v-model.number="settings.courses.maxAttachmentSizeMB"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="maxCategoriesPerCourse"
                    class="block text-sm font-medium text-gray-700"
                    >Max Categories Per Course</label
                  >
                  <input
                    id="maxCategoriesPerCourse"
                    v-model.number="settings.courses.maxCategoriesPerCourse"
                    type="number"
                    min="1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Pricing Settings
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="flex items-start mb-4">
                    <div class="flex items-center h-5">
                      <input
                        id="allowFreeCourses"
                        v-model="settings.courses.allowFreeCourses"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="allowFreeCourses"
                        class="font-medium text-gray-700"
                        >Allow Free Courses</label
                      >
                      <p class="text-gray-500">
                        Instructors can offer courses at no cost.
                      </p>
                    </div>
                  </div>

                  <div>
                    <label
                      for="minimumCoursePrice"
                      class="block text-sm font-medium text-gray-700"
                      >Minimum Course Price</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                      >
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        id="minimumCoursePrice"
                        v-model.number="settings.courses.minimumCoursePrice"
                        type="number"
                        min="0"
                        step="0.01"
                        class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :disabled="settings.courses.allowFreeCourses"
                      />
                    </div>
                  </div>
                </div>

                <div>
                  <label
                    for="courseCommissionPercentage"
                    class="block text-sm font-medium text-gray-700"
                    >Course Commission Percentage</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                      id="courseCommissionPercentage"
                      v-model.number="
                        settings.courses.courseCommissionPercentage
                      "
                      type="number"
                      min="0"
                      max="100"
                      class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <div
                      class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    The platform's percentage of each course sale
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                  <label
                    for="instructorPaymentThreshold"
                    class="block text-sm font-medium text-gray-700"
                    >Instructor Payment Threshold</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div
                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input
                      id="instructorPaymentThreshold"
                      v-model.number="
                        settings.courses.instructorPaymentThreshold
                      "
                      type="number"
                      min="0"
                      class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Minimum amount before payments are issued
                  </p>
                </div>

                <div>
                  <label
                    for="paymentSchedule"
                    class="block text-sm font-medium text-gray-700"
                    >Payment Schedule</label
                  >
                  <select
                    id="paymentSchedule"
                    v-model="settings.courses.paymentSchedule"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="weekly">Weekly</option>
                    <option value="bi-weekly">Bi-Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                  </select>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Certificate Settings
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="certificatesEnabled"
                        v-model="settings.courses.certificatesEnabled"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="certificatesEnabled"
                        class="font-medium text-gray-700"
                        >Enable Course Completion Certificates</label
                      >
                      <p class="text-gray-500">
                        Students can earn certificates upon course completion.
                      </p>
                    </div>
                  </div>
                </div>

                <div v-if="settings.courses.certificatesEnabled">
                  <label
                    for="certificateTemplate"
                    class="block text-sm font-medium text-gray-700"
                    >Certificate Template</label
                  >
                  <select
                    id="certificateTemplate"
                    v-model="settings.courses.certificateTemplate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="default">Default</option>
                    <option value="professional">Professional</option>
                    <option value="academic">Academic</option>
                    <option value="minimal">Minimal</option>
                    <option value="custom">Custom</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Consultancy Settings Section -->
          <div v-if="activeCategory === 'consultancy'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Consultancy Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Pricing & Commissions
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="minConsultancyRate"
                    class="block text-sm font-medium text-gray-700"
                    >Minimum Hourly Rate ($)</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div
                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input
                      id="minConsultancyRate"
                      v-model.number="settings.consultancy.minConsultancyRate"
                      type="number"
                      min="0"
                      class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Minimum hourly rate consultants can charge
                  </p>
                </div>

                <div>
                  <label
                    for="maxConsultancyRate"
                    class="block text-sm font-medium text-gray-700"
                    >Maximum Hourly Rate ($)</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div
                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input
                      id="maxConsultancyRate"
                      v-model.number="settings.consultancy.maxConsultancyRate"
                      type="number"
                      min="0"
                      class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Maximum hourly rate consultants can charge
                  </p>
                </div>

                <div>
                  <label
                    for="consultancyCommissionPercentage"
                    class="block text-sm font-medium text-gray-700"
                    >Platform Commission (%)</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                      id="consultancyCommissionPercentage"
                      v-model.number="
                        settings.consultancy.consultancyCommissionPercentage
                      "
                      type="number"
                      min="0"
                      max="100"
                      class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <div
                      class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    The platform's percentage of each consultation
                  </p>
                </div>

                <div>
                  <label
                    for="consultantPaymentThreshold"
                    class="block text-sm font-medium text-gray-700"
                    >Payment Threshold ($)</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div
                      class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input
                      id="consultantPaymentThreshold"
                      v-model.number="
                        settings.consultancy.consultantPaymentThreshold
                      "
                      type="number"
                      min="0"
                      class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Minimum amount before payments are issued to consultants
                  </p>
                </div>

                <div>
                  <label
                    for="consultantPaymentSchedule"
                    class="block text-sm font-medium text-gray-700"
                    >Payment Schedule</label
                  >
                  <select
                    id="consultantPaymentSchedule"
                    v-model="settings.consultancy.consultantPaymentSchedule"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="weekly">Weekly</option>
                    <option value="bi-weekly">Bi-Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                  </select>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Booking Settings
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="allowInstantBooking"
                        v-model="settings.consultancy.allowInstantBooking"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="allowInstantBooking"
                        class="font-medium text-gray-700"
                        >Allow Instant Booking</label
                      >
                      <p class="text-gray-500">
                        Clients can instantly book available time slots without
                        approval.
                      </p>
                    </div>
                  </div>
                </div>

                <div>
                  <label
                    for="minAdvanceBookingHours"
                    class="block text-sm font-medium text-gray-700"
                    >Minimum Advance Booking (hours)</label
                  >
                  <input
                    id="minAdvanceBookingHours"
                    v-model.number="settings.consultancy.minAdvanceBookingHours"
                    type="number"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    How far in advance clients must book consultations
                  </p>
                </div>

                <div>
                  <label
                    for="maxCancellationWindowHours"
                    class="block text-sm font-medium text-gray-700"
                    >Cancellation Window (hours)</label
                  >
                  <input
                    id="maxCancellationWindowHours"
                    v-model.number="
                      settings.consultancy.maxCancellationWindowHours
                    "
                    type="number"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Maximum time before a consultation when cancellations are
                    allowed
                  </p>
                </div>

                <div>
                  <label
                    for="refundPolicy"
                    class="block text-sm font-medium text-gray-700"
                    >Refund Policy</label
                  >
                  <select
                    id="refundPolicy"
                    v-model="settings.consultancy.refundPolicy"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="full">Full Refund</option>
                    <option value="partial">Partial Refund</option>
                    <option value="none">No Refund</option>
                    <option value="credits">Platform Credits</option>
                  </select>
                  <p class="mt-1 text-xs text-gray-500">
                    Refund policy for cancelled bookings
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-6 mt-4">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="requireContractSigning"
                      v-model="settings.consultancy.requireContractSigning"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="requireContractSigning"
                      class="font-medium text-gray-700"
                      >Require Contract Signing</label
                    >
                    <p class="text-gray-500">
                      Clients and consultants must sign a contract before
                      beginning work.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="autoCancelNoShowAppointments"
                      v-model="
                        settings.consultancy.autoCancelNoShowAppointments
                      "
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="autoCancelNoShowAppointments"
                      class="font-medium text-gray-700"
                      >Auto-Cancel No-Shows</label
                    >
                    <p class="text-gray-500">
                      Automatically cancel appointments when clients don't show
                      up.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Gateway Settings Section -->
          <div v-if="activeCategory === 'payments'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Payment Gateway Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Currency Settings
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label
                    for="currency"
                    class="block text-sm font-medium text-gray-700"
                    >Default Currency</label
                  >
                  <select
                    id="currency"
                    v-model="settings.payments.currency"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="USD">US Dollar (USD)</option>
                    <option value="EUR">Euro (EUR)</option>
                    <option value="GBP">British Pound (GBP)</option>
                    <option value="CAD">Canadian Dollar (CAD)</option>
                    <option value="AUD">Australian Dollar (AUD)</option>
                    <option value="JPY">Japanese Yen (JPY)</option>
                  </select>
                </div>

                <div>
                  <label
                    for="currencySymbol"
                    class="block text-sm font-medium text-gray-700"
                    >Currency Symbol</label
                  >
                  <input
                    id="currencySymbol"
                    v-model="settings.payments.currencySymbol"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="decimals"
                    class="block text-sm font-medium text-gray-700"
                    >Decimal Places</label
                  >
                  <select
                    id="decimals"
                    v-model.number="settings.payments.decimals"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option :value="0">0</option>
                    <option :value="1">1</option>
                    <option :value="2">2</option>
                    <option :value="3">3</option>
                  </select>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Payment Gateways
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="stripeEnabled"
                      v-model="settings.payments.stripeEnabled"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="stripeEnabled" class="font-medium text-gray-700"
                      >Enable Stripe</label
                    >
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="paypalEnabled"
                      v-model="settings.payments.paypalEnabled"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="paypalEnabled" class="font-medium text-gray-700"
                      >Enable PayPal</label
                    >
                  </div>
                </div>
              </div>

              <!-- Stripe Settings -->
              <div
                v-if="settings.payments.stripeEnabled"
                class="border rounded-lg p-4 bg-gray-50"
              >
                <h4 class="text-sm font-medium text-gray-700 mb-4">
                  Stripe Configuration
                </h4>

                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <label
                      for="stripePublicKey"
                      class="block text-sm font-medium text-gray-700"
                      >Stripe Public Key</label
                    >
                    <input
                      id="stripePublicKey"
                      v-model="settings.payments.stripePublicKey"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="stripeSecretKey"
                      class="block text-sm font-medium text-gray-700"
                      >Stripe Secret Key</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="stripeSecretKey"
                        v-model="settings.payments.stripeSecretKey"
                        :type="
                          showPassword.stripeSecretKey ? 'text' : 'password'
                        "
                        class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="togglePasswordVisibility('stripeSecretKey')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            v-if="showPassword.stripeSecretKey"
                            fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-if="showPassword.stripeSecretKey"
                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                          />
                          <path
                            v-if="!showPassword.stripeSecretKey"
                            fill-rule="evenodd"
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mt-4">
                  <button
                    type="button"
                    @click="refreshApiKey('Stripe')"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                      />
                    </svg>
                    Refresh API Keys
                  </button>
                </div>
              </div>

              <!-- PayPal Settings -->
              <div
                v-if="settings.payments.paypalEnabled"
                class="border rounded-lg p-4 bg-gray-50"
              >
                <h4 class="text-sm font-medium text-gray-700 mb-4">
                  PayPal Configuration
                </h4>

                <div class="grid grid-cols-1 gap-4">
                  <div>
                    <label
                      for="paypalClientId"
                      class="block text-sm font-medium text-gray-700"
                      >PayPal Client ID</label
                    >
                    <input
                      id="paypalClientId"
                      v-model="settings.payments.paypalClientId"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="paypalSecret"
                      class="block text-sm font-medium text-gray-700"
                      >PayPal Secret</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="paypalSecret"
                        v-model="settings.payments.paypalSecret"
                        :type="showPassword.paypalSecret ? 'text' : 'password'"
                        class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="togglePasswordVisibility('paypalSecret')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            v-if="showPassword.paypalSecret"
                            fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-if="showPassword.paypalSecret"
                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                          />
                          <path
                            v-if="!showPassword.paypalSecret"
                            fill-rule="evenodd"
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mt-4">
                  <button
                    type="button"
                    @click="refreshApiKey('PayPal')"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                      />
                    </svg>
                    Refresh API Keys
                  </button>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Tax Settings
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="taxRate"
                    class="block text-sm font-medium text-gray-700"
                    >Default Tax Rate (%)</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                      id="taxRate"
                      v-model.number="settings.payments.taxRate"
                      type="number"
                      min="0"
                      max="100"
                      step="0.01"
                      class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <div
                      class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                    >
                      <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                  </div>
                </div>

                <div class="flex items-start self-center">
                  <div class="flex items-center h-5">
                    <input
                      id="collectTaxInfo"
                      v-model="settings.payments.collectTaxInfo"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="collectTaxInfo"
                      class="font-medium text-gray-700"
                      >Collect Tax Information</label
                    >
                    <p class="text-gray-500">
                      Collect tax information from users for reporting purposes.
                    </p>
                  </div>
                </div>

                <div>
                  <label
                    for="invoicePrefix"
                    class="block text-sm font-medium text-gray-700"
                    >Invoice Prefix</label
                  >
                  <input
                    id="invoicePrefix"
                    v-model="settings.payments.invoicePrefix"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Prefix added to all invoice numbers
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Email Settings Section -->
          <div v-if="activeCategory === 'email'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Email Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Email Provider Configuration
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="emailProvider"
                    class="block text-sm font-medium text-gray-700"
                    >Email Provider</label
                  >
                  <select
                    id="emailProvider"
                    v-model="settings.email.emailProvider"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="smtp">SMTP Server</option>
                    <option value="sendgrid">SendGrid</option>
                    <option value="mailchimp">Mailchimp</option>
                    <option value="amazon-ses">Amazon SES</option>
                  </select>
                </div>

                <div>
                  <label
                    for="senderEmail"
                    class="block text-sm font-medium text-gray-700"
                    >Sender Email Address</label
                  >
                  <input
                    id="senderEmail"
                    v-model="settings.email.senderEmail"
                    type="email"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="senderName"
                    class="block text-sm font-medium text-gray-700"
                    >Sender Name</label
                  >
                  <input
                    id="senderName"
                    v-model="settings.email.senderName"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                </div>
              </div>

              <div
                v-if="settings.email.emailProvider === 'smtp'"
                class="border rounded-lg p-4 bg-gray-50"
              >
                <h4 class="text-sm font-medium text-gray-700 mb-4">
                  SMTP Configuration
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label
                      for="smtpHost"
                      class="block text-sm font-medium text-gray-700"
                      >SMTP Host</label
                    >
                    <input
                      id="smtpHost"
                      v-model="settings.email.smtpHost"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="smtpPort"
                      class="block text-sm font-medium text-gray-700"
                      >SMTP Port</label
                    >
                    <input
                      id="smtpPort"
                      v-model.number="settings.email.smtpPort"
                      type="number"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="smtpUsername"
                      class="block text-sm font-medium text-gray-700"
                      >SMTP Username</label
                    >
                    <input
                      id="smtpUsername"
                      v-model="settings.email.smtpUsername"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="smtpPassword"
                      class="block text-sm font-medium text-gray-700"
                      >SMTP Password</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="smtpPassword"
                        v-model="settings.email.smtpPassword"
                        :type="showPassword.smtpPassword ? 'text' : 'password'"
                        class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="togglePasswordVisibility('smtpPassword')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            v-if="showPassword.smtpPassword"
                            fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-if="showPassword.smtpPassword"
                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                          />
                          <path
                            v-if="!showPassword.smtpPassword"
                            fill-rule="evenodd"
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div>
                    <label
                      for="smtpEncryption"
                      class="block text-sm font-medium text-gray-700"
                      >Encryption</label
                    >
                    <select
                      id="smtpEncryption"
                      v-model="settings.email.smtpEncryption"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                      <option value="none">None</option>
                      <option value="ssl">SSL</option>
                      <option value="tls">TLS</option>
                    </select>
                  </div>

                  <div class="md:col-span-2">
                    <button
                      type="button"
                      @click="testEmailConfig"
                      class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                      </svg>
                      Send Test Email
                    </button>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Email Templates
              </h3>

              <div>
                <label
                  for="emailFooter"
                  class="block text-sm font-medium text-gray-700"
                  >Email Footer</label
                >
                <textarea
                  id="emailFooter"
                  v-model="settings.email.emailFooter"
                  rows="2"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">
                  This text will be added to the bottom of all emails
                </p>
              </div>

              <div class="mt-6">
                <div class="bg-white shadow rounded-lg">
                  <div class="border-b border-gray-200">
                    <nav class="-mb-px flex">
                      <button
                        class="py-4 px-6 text-sm font-medium border-b-2 focus:outline-none"
                        :class="[
                          activeEmailTemplate === 'welcome'
                            ? 'border-indigo-500 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        ]"
                        @click="activeEmailTemplate = 'welcome'"
                      >
                        Welcome Email
                      </button>
                      <button
                        class="py-4 px-6 text-sm font-medium border-b-2 focus:outline-none"
                        :class="[
                          activeEmailTemplate === 'reset'
                            ? 'border-indigo-500 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        ]"
                        @click="activeEmailTemplate = 'reset'"
                      >
                        Password Reset
                      </button>
                      <button
                        class="py-4 px-6 text-sm font-medium border-b-2 focus:outline-none"
                        :class="[
                          activeEmailTemplate === 'verification'
                            ? 'border-indigo-500 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        ]"
                        @click="activeEmailTemplate = 'verification'"
                      >
                        Email Verification
                      </button>
                    </nav>
                  </div>

                  <div class="p-4">
                    <div v-if="activeEmailTemplate === 'welcome'">
                      <h4 class="text-sm font-medium text-gray-900 mb-2">
                        Welcome Email Template
                      </h4>
                      <textarea
                        v-model="settings.email.welcomeEmailTemplate"
                        rows="6"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      ></textarea>
                      <p class="mt-1 text-xs text-gray-500">
                        Available variables: {name}, {email}, {loginUrl},
                        {siteName}
                      </p>
                    </div>

                    <div v-if="activeEmailTemplate === 'reset'">
                      <h4 class="text-sm font-medium text-gray-900 mb-2">
                        Password Reset Template
                      </h4>
                      <textarea
                        v-model="settings.email.passwordResetTemplate"
                        rows="6"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      ></textarea>
                      <p class="mt-1 text-xs text-gray-500">
                        Available variables: {name}, {email}, {resetLink},
                        {siteName}, {expiryTime}
                      </p>
                    </div>

                    <div v-if="activeEmailTemplate === 'verification'">
                      <h4 class="text-sm font-medium text-gray-900 mb-2">
                        Email Verification Template
                      </h4>
                      <textarea
                        v-model="settings.email.verificationEmailTemplate"
                        rows="6"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      ></textarea>
                      <p class="mt-1 text-xs text-gray-500">
                        Available variables: {name}, {email},
                        {verificationLink}, {siteName}, {expiryTime}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Integrations Settings Section -->
          <div v-if="activeCategory === 'integrations'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Integrations Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Analytics Integration
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="googleAnalyticsId"
                    class="block text-sm font-medium text-gray-700"
                    >Google Analytics ID</label
                  >
                  <input
                    id="googleAnalyticsId"
                    v-model="settings.integrations.googleAnalyticsId"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="UA-XXXXXXXXX-X or G-XXXXXXXXXX"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Your Google Analytics tracking ID
                  </p>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Maps Integration
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="googleMapsApiKey"
                    class="block text-sm font-medium text-gray-700"
                    >Google Maps API Key</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                      id="googleMapsApiKey"
                      v-model="settings.integrations.googleMapsApiKey"
                      :type="
                        showPassword.googleMapsApiKey ? 'text' : 'password'
                      "
                      class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <button
                      type="button"
                      @click="togglePasswordVisibility('googleMapsApiKey')"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          v-if="showPassword.googleMapsApiKey"
                          fill-rule="evenodd"
                          d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                          clip-rule="evenodd"
                        />
                        <path
                          v-if="showPassword.googleMapsApiKey"
                          d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                        />
                        <path
                          v-if="!showPassword.googleMapsApiKey"
                          fill-rule="evenodd"
                          d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                  </div>
                  <div class="mt-2">
                    <button
                      type="button"
                      @click="refreshApiKey('Google Maps')"
                      class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                      </svg>
                      Refresh API Key
                    </button>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Video Conferencing
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="zoomIntegration"
                      v-model="settings.integrations.zoomIntegration"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="zoomIntegration"
                      class="font-medium text-gray-700"
                      >Enable Zoom Integration</label
                    >
                    <p class="text-gray-500">
                      Allow users to schedule and join Zoom meetings directly
                      from the platform.
                    </p>
                  </div>
                </div>

                <div
                  v-if="settings.integrations.zoomIntegration"
                  class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-7"
                >
                  <div>
                    <label
                      for="zoomApiKey"
                      class="block text-sm font-medium text-gray-700"
                      >Zoom API Key</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="zoomApiKey"
                        v-model="settings.integrations.zoomApiKey"
                        :type="showPassword.zoomApiKey ? 'text' : 'password'"
                        class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="togglePasswordVisibility('zoomApiKey')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            v-if="showPassword.zoomApiKey"
                            fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-if="showPassword.zoomApiKey"
                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                          />
                          <path
                            v-if="!showPassword.zoomApiKey"
                            fill-rule="evenodd"
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div>
                    <label
                      for="zoomApiSecret"
                      class="block text-sm font-medium text-gray-700"
                      >Zoom API Secret</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="zoomApiSecret"
                        v-model="settings.integrations.zoomApiSecret"
                        :type="showPassword.zoomApiSecret ? 'text' : 'password'"
                        class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="togglePasswordVisibility('zoomApiSecret')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            v-if="showPassword.zoomApiSecret"
                            fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-if="showPassword.zoomApiSecret"
                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                          />
                          <path
                            v-if="!showPassword.zoomApiSecret"
                            fill-rule="evenodd"
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div class="md:col-span-2">
                    <button
                      type="button"
                      @click="refreshApiKey('Zoom')"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                      </svg>
                      Refresh API Keys
                    </button>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Notification Integrations
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="slackWebhookUrl"
                    class="block text-sm font-medium text-gray-700"
                    >Slack Webhook URL</label
                  >
                  <input
                    id="slackWebhookUrl"
                    v-model="settings.integrations.slackWebhookUrl"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="https://hooks.slack.com/services/..."
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    For sending important platform notifications to Slack
                  </p>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Email Marketing
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="mailchimpApiKey"
                    class="block text-sm font-medium text-gray-700"
                    >Mailchimp API Key</label
                  >
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                      id="mailchimpApiKey"
                      v-model="settings.integrations.mailchimpApiKey"
                      :type="showPassword.mailchimpApiKey ? 'text' : 'password'"
                      class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <button
                      type="button"
                      @click="togglePasswordVisibility('mailchimpApiKey')"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          v-if="showPassword.mailchimpApiKey"
                          fill-rule="evenodd"
                          d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                          clip-rule="evenodd"
                        />
                        <path
                          v-if="showPassword.mailchimpApiKey"
                          d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                        />
                        <path
                          v-if="!showPassword.mailchimpApiKey"
                          fill-rule="evenodd"
                          d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label
                    for="mailchimpListId"
                    class="block text-sm font-medium text-gray-700"
                    >Mailchimp List ID</label
                  >
                  <input
                    id="mailchimpListId"
                    v-model="settings.integrations.mailchimpListId"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    The default list to add new users to
                  </p>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Version Control
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="githubIntegration"
                      v-model="settings.integrations.githubIntegration"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="githubIntegration"
                      class="font-medium text-gray-700"
                      >Enable GitHub Integration</label
                    >
                    <p class="text-gray-500">
                      Allow users to connect their GitHub accounts for project
                      collaborations.
                    </p>
                  </div>
                </div>

                <div
                  v-if="settings.integrations.githubIntegration"
                  class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-7"
                >
                  <div>
                    <label
                      for="githubClientId"
                      class="block text-sm font-medium text-gray-700"
                      >GitHub Client ID</label
                    >
                    <input
                      id="githubClientId"
                      v-model="settings.integrations.githubClientId"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="githubClientSecret"
                      class="block text-sm font-medium text-gray-700"
                      >GitHub Client Secret</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="githubClientSecret"
                        v-model="settings.integrations.githubClientSecret"
                        :type="
                          showPassword.githubClientSecret ? 'text' : 'password'
                        "
                        class="block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                      <button
                        type="button"
                        @click="togglePasswordVisibility('githubClientSecret')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            v-if="showPassword.githubClientSecret"
                            fill-rule="evenodd"
                            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                            clip-rule="evenodd"
                          />
                          <path
                            v-if="showPassword.githubClientSecret"
                            d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                          />
                          <path
                            v-if="!showPassword.githubClientSecret"
                            fill-rule="evenodd"
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1116 0 8 8 0 01-16 0zm8-3a1 1 0 00-1 1v1.586l-.293-.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414l-.293.293V8a1 1 0 00-1-1z"
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

          <!-- Maintenance Settings Section -->
          <div v-if="activeCategory === 'maintenance'">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
              Maintenance Settings
            </h2>

            <div class="space-y-6">
              <h3 class="text-sm font-medium text-gray-900 border-b pb-2">
                Backup Settings
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="enableAutoBackup"
                      v-model="settings.maintenance.enableAutoBackup"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="enableAutoBackup"
                      class="font-medium text-gray-700"
                      >Enable Automatic Backups</label
                    >
                    <p class="text-gray-500">
                      Automatically backup the database and files according to
                      the schedule.
                    </p>
                  </div>
                </div>

                <div
                  v-if="settings.maintenance.enableAutoBackup"
                  class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-7"
                >
                  <div>
                    <label
                      for="backupFrequency"
                      class="block text-sm font-medium text-gray-700"
                      >Backup Frequency</label
                    >
                    <select
                      id="backupFrequency"
                      v-model="settings.maintenance.backupFrequency"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                      <option value="hourly">Hourly</option>
                      <option value="daily">Daily</option>
                      <option value="weekly">Weekly</option>
                      <option value="monthly">Monthly</option>
                    </select>
                  </div>

                  <div>
                    <label
                      for="backupRetentionDays"
                      class="block text-sm font-medium text-gray-700"
                      >Retention Period (days)</label
                    >
                    <input
                      id="backupRetentionDays"
                      v-model.number="settings.maintenance.backupRetentionDays"
                      type="number"
                      min="1"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                      Number of days to keep backups before deletion
                    </p>
                  </div>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Logging & Error Reporting
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="logLevel"
                    class="block text-sm font-medium text-gray-700"
                    >Log Level</label
                  >
                  <select
                    id="logLevel"
                    v-model="settings.maintenance.logLevel"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                    <option value="debug">Debug (All Messages)</option>
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                    <option value="critical">Critical</option>
                  </select>
                  <p class="mt-1 text-xs text-gray-500">
                    Determines which types of messages are recorded in the logs
                  </p>
                </div>

                <div>
                  <label
                    for="errorReportingEmail"
                    class="block text-sm font-medium text-gray-700"
                    >Error Reporting Email</label
                  >
                  <input
                    id="errorReportingEmail"
                    v-model="settings.maintenance.errorReportingEmail"
                    type="email"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Email address to receive critical error notifications
                  </p>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                Data Cleanup
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="purgeInactiveAccounts"
                      v-model="settings.maintenance.purgeInactiveAccounts"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="purgeInactiveAccounts"
                      class="font-medium text-gray-700"
                      >Auto-Purge Inactive Accounts</label
                    >
                    <p class="text-gray-500">
                      Automatically delete accounts that have been inactive for
                      the specified period.
                    </p>
                  </div>
                </div>

                <div
                  v-if="settings.maintenance.purgeInactiveAccounts"
                  class="pl-7"
                >
                  <label
                    for="purgeInactiveAccountsDays"
                    class="block text-sm font-medium text-gray-700"
                    >Inactive Period (days)</label
                  >
                  <input
                    id="purgeInactiveAccountsDays"
                    v-model.number="
                      settings.maintenance.purgeInactiveAccountsDays
                    "
                    type="number"
                    min="1"
                    class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Number of days of inactivity before account deletion
                  </p>
                </div>

                <div class="flex items-start mt-4">
                  <div class="flex items-center h-5">
                    <input
                      id="purgeDeletedContent"
                      v-model="settings.maintenance.purgeDeletedContent"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="purgeDeletedContent"
                      class="font-medium text-gray-700"
                      >Permanently Remove Deleted Content</label
                    >
                    <p class="text-gray-500">
                      Permanently delete content that has been marked for
                      deletion after the specified period.
                    </p>
                  </div>
                </div>

                <div
                  v-if="settings.maintenance.purgeDeletedContent"
                  class="pl-7"
                >
                  <label
                    for="purgeDeletedContentDays"
                    class="block text-sm font-medium text-gray-700"
                    >Retention Period (days)</label
                  >
                  <input
                    id="purgeDeletedContentDays"
                    v-model.number="
                      settings.maintenance.purgeDeletedContentDays
                    "
                    type="number"
                    min="1"
                    class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Number of days to keep deleted content before permanent
                    removal
                  </p>
                </div>
              </div>

              <h3 class="text-sm font-medium text-gray-900 border-b pb-2 mt-8">
                System Updates
              </h3>

              <div class="grid grid-cols-1 gap-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="autoUpdateSystem"
                      v-model="settings.maintenance.autoUpdateSystem"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="autoUpdateSystem"
                      class="font-medium text-gray-700"
                      >Automatic System Updates</label
                    >
                    <p class="text-gray-500">
                      Automatically install system updates during scheduled
                      maintenance windows.
                    </p>
                  </div>
                </div>

                <div
                  v-if="settings.maintenance.autoUpdateSystem"
                  class="grid grid-cols-1 md:grid-cols-3 gap-6 pl-7"
                >
                  <div>
                    <label
                      for="maintenanceWindow"
                      class="block text-sm font-medium text-gray-700"
                      >Maintenance Day</label
                    >
                    <select
                      id="maintenanceWindow"
                      v-model="settings.maintenance.maintenanceWindow"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                      <option value="monday">Monday</option>
                      <option value="tuesday">Tuesday</option>
                      <option value="wednesday">Wednesday</option>
                      <option value="thursday">Thursday</option>
                      <option value="friday">Friday</option>
                      <option value="saturday">Saturday</option>
                      <option value="sunday">Sunday</option>
                    </select>
                  </div>

                  <div>
                    <label
                      for="maintenanceTimeStart"
                      class="block text-sm font-medium text-gray-700"
                      >Start Time</label
                    >
                    <input
                      id="maintenanceTimeStart"
                      v-model="settings.maintenance.maintenanceTimeStart"
                      type="time"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div>
                    <label
                      for="maintenanceTimeEnd"
                      class="block text-sm font-medium text-gray-700"
                      >End Time</label
                    >
                    <input
                      id="maintenanceTimeEnd"
                      v-model="settings.maintenance.maintenanceTimeEnd"
                      type="time"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>

              <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-yellow-400"
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
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                      Maintenance Mode
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                      <p>
                        When enabled, maintenance mode will make the platform
                        inaccessible to regular users. Only administrators will
                        be able to access the system during maintenance.
                      </p>
                    </div>
                    <div class="mt-4">
                      <div class="flex items-center">
                        <button
                          type="button"
                          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                        >
                          Enable Maintenance Mode
                        </button>
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
</template>
