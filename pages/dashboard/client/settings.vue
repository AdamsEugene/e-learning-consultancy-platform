<!-- pages/dashboard/client/settings.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Define page meta to use dashboard-client layout
definePageMeta({
  layout: "dashboard-client",
});

// Set page title
useHead({
  title: "Account Settings - Client Dashboard",
});

// User profile data state
const profileForm = ref({
  firstName: "Michael",
  lastName: "Roberts",
  email: "michael.roberts@techinnovate.com",
  phone: "+1 (555) 123-4567",
  position: "Product Director",
  company: "TechInnovate Solutions",
  bio: "Experienced product director with over 10 years in the tech industry. Passionate about creating innovative solutions that solve real business problems.",
  avatarUrl: "/api/placeholder/100/100",
});

// Notification preferences
const notificationPrefs = ref({
  emailUpdates: true,
  projectUpdates: true,
  meetingReminders: true,
  resourceSharing: true,
  newsletterUpdates: false,
  marketingEmails: false,
});

// Security settings
const securitySettings = ref({
  twoFactorEnabled: false,
  loginAlerts: true,
  sessionTimeout: 30,
});

// Privacy settings
const privacySettings = ref({
  profileVisibility: "public", // 'public', 'consultants', 'private'
  shareActivityData: true,
  allowMessaging: true,
  showOnlineStatus: true,
});

// Integration settings
const integrationSettings = ref({
  googleCalendar: true,
  outlookCalendar: false,
  slack: true,
  zoom: true,
  microsoft365: false,
  github: false,
});

// Billing information
const billingInfo = ref({
  planType: "Premium Client",
  billingCycle: "Monthly",
  nextBillingDate: "2025-05-15",
  paymentMethod: "Visa ending in 4242",
  autoRenew: true,
});

// Active tab
const activeTab = ref("profile");

// Form states
const isSaving = ref(false);
const isSaved = ref(false);
const saveError = ref(false);
const isUploading = ref(false);
const passwordForm = ref({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

// Handle form submission
const saveSettings = async (section: string) => {
  isSaving.value = true;
  saveError.value = false;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Show success state
    isSaved.value = true;
    setTimeout(() => {
      isSaved.value = false;
    }, 3000);
  } catch (error) {
    console.error(`Error saving ${section} settings:`, error);
    saveError.value = true;
  } finally {
    isSaving.value = false;
  }
};

// Handle password change
const changePassword = async () => {
  // Validate passwords
  if (
    !passwordForm.value.currentPassword ||
    !passwordForm.value.newPassword ||
    !passwordForm.value.confirmPassword
  ) {
    saveError.value = true;
    return;
  }

  if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
    saveError.value = true;
    return;
  }

  // Handle password change
  await saveSettings("security");

  // Reset form
  passwordForm.value = {
    currentPassword: "",
    newPassword: "",
    confirmPassword: "",
  };
};

// Handle avatar upload
const handleAvatarUpload = () => {
  isUploading.value = true;

  // Simulate upload
  setTimeout(() => {
    isUploading.value = false;
    // In a real app, you would update the avatar URL here
  }, 1500);
};

// Session history data
const sessionHistory = ref([
  {
    device: "MacBook Pro",
    location: "San Francisco, CA",
    browser: "Chrome",
    date: "2025-04-14 09:23 AM",
    status: "Current Session",
  },
  {
    device: "iPhone 15",
    location: "San Francisco, CA",
    browser: "Safari",
    date: "2025-04-12 03:45 PM",
    status: "Active",
  },
  {
    device: "Windows PC",
    location: "New York, NY",
    browser: "Firefox",
    date: "2025-04-10 11:12 AM",
    status: "Active",
  },
  {
    device: "iPad Pro",
    location: "Chicago, IL",
    browser: "Safari",
    date: "2025-04-08 10:30 AM",
    status: "Expired",
  },
  {
    device: "Android Phone",
    location: "San Francisco, CA",
    browser: "Chrome",
    date: "2025-04-05 08:15 PM",
    status: "Expired",
  },
]);

// Upcoming plan features
const upcomingFeatures = ref([
  { name: "Advanced Project Analytics", available: "2025-06-01" },
  { name: "Custom Integration API", available: "2025-07-15" },
  { name: "Team Collaboration Tools", available: "2025-05-20" },
]);

// Billing history
const billingHistory = ref([
  {
    date: "2025-04-01",
    amount: "$199.00",
    status: "Paid",
    invoice: "INV-2025-0401",
  },
  {
    date: "2025-03-01",
    amount: "$199.00",
    status: "Paid",
    invoice: "INV-2025-0301",
  },
  {
    date: "2025-02-01",
    amount: "$199.00",
    status: "Paid",
    invoice: "INV-2025-0201",
  },
  {
    date: "2025-01-01",
    amount: "$199.00",
    status: "Paid",
    invoice: "INV-2025-0101",
  },
]);

onMounted(() => {
  // Check URL hash for active tab
  const hash = window.location.hash;
  if (hash) {
    const tab = hash.substring(1);
    if (
      [
        "profile",
        "security",
        "notifications",
        "billing",
        "privacy",
        "integrations",
      ].includes(tab)
    ) {
      activeTab.value = tab;
    }
  }
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
      <p class="text-gray-600 mt-1">
        Manage your profile, preferences, and account settings
      </p>
    </div>

    <!-- Settings navigation and content -->
    <div
      class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
    >
      <!-- Tab navigation -->
      <div
        class="bg-gray-50 border-b border-gray-200 px-4 py-2 sm:px-6 flex flex-wrap overflow-x-auto"
      >
        <button
          v-for="tab in [
            'profile',
            'security',
            'notifications',
            'billing',
            'privacy',
            'integrations',
          ]"
          :key="tab"
          @click="
            activeTab = tab;
            window.location.hash = tab;
          "
          class="px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap transition-colors mr-2 mb-2"
          :class="
            activeTab === tab
              ? 'bg-indigo-100 text-indigo-700 shadow-sm'
              : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          "
        >
          <span v-if="tab === 'profile'">Profile</span>
          <span v-else-if="tab === 'security'">Security</span>
          <span v-else-if="tab === 'notifications'">Notifications</span>
          <span v-else-if="tab === 'billing'">Billing & Plan</span>
          <span v-else-if="tab === 'privacy'">Privacy</span>
          <span v-else-if="tab === 'integrations'">Integrations</span>
        </button>
      </div>

      <!-- Profile Settings -->
      <div v-if="activeTab === 'profile'" class="p-6">
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Personal Information
          </h2>

          <!-- Avatar Upload -->
          <div class="flex flex-col sm:flex-row sm:items-center mb-6">
            <div class="relative mb-4 sm:mb-0 sm:mr-6">
              <img
                :src="profileForm.avatarUrl"
                alt="Avatar"
                class="w-24 h-24 rounded-full object-cover border-4 border-gray-200"
              />
              <div
                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 hover:opacity-100 transition-opacity"
              >
                <button
                  @click="handleAvatarUpload"
                  class="text-white text-sm font-medium"
                  :disabled="isUploading"
                >
                  {{ isUploading ? "Uploading..." : "Change" }}
                </button>
              </div>

              <div class="border-t border-gray-200 pt-6 flex justify-end">
                <button
                  @click="saveSettings('integrations')"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
                  :disabled="isSaving"
                >
                  <svg
                    v-if="isSaving"
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
                  <span v-if="isSaved" class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Saved
                  </span>
                  <span v-else-if="isSaving">Saving...</span>
                  <span v-else>Save Changes</span>
                </button>
              </div>
            </div>
            <div>
              <h3 class="text-base font-medium text-gray-900">
                Profile Picture
              </h3>
              <p class="text-sm text-gray-500 mt-1">
                Upload a clear headshot for your profile. JPG, GIF or PNG. Max
                5MB.
              </p>
              <div class="mt-2">
                <label
                  class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded text-sm font-medium inline-block cursor-pointer transition-colors"
                >
                  Upload New Photo
                  <input type="file" class="hidden" accept="image/*" />
                </label>
              </div>
            </div>
          </div>

          <!-- Profile Form -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="firstName"
                class="block text-sm font-medium text-gray-700 mb-1"
                >First Name</label
              >
              <input
                id="firstName"
                v-model="profileForm.firstName"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="lastName"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Last Name</label
              >
              <input
                id="lastName"
                v-model="profileForm.lastName"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="email"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Email Address</label
              >
              <input
                id="email"
                v-model="profileForm.email"
                type="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="phone"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Phone Number</label
              >
              <input
                id="phone"
                v-model="profileForm.phone"
                type="tel"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="position"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Position/Title</label
              >
              <input
                id="position"
                v-model="profileForm.position"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="company"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Company</label
              >
              <input
                id="company"
                v-model="profileForm.company"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div class="md:col-span-2">
              <label
                for="bio"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Professional Bio</label
              >
              <textarea
                id="bio"
                v-model="profileForm.bio"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              ></textarea>
              <p class="mt-1 text-sm text-gray-500">
                Brief description for your profile. URLs are hyperlinked.
              </p>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-end">
          <button
            @click="saveSettings('profile')"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSaving"
          >
            <svg
              v-if="isSaving"
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
            <span v-if="isSaved" class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              Saved
            </span>
            <span v-else-if="isSaving">Saving...</span>
            <span v-else>Save Changes</span>
          </button>
        </div>
      </div>

      <!-- Security Settings -->
      <div v-if="activeTab === 'security'" class="p-6">
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Security Settings
          </h2>

          <!-- Change Password Section -->
          <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="text-base font-medium text-gray-900 mb-3">
              Change Password
            </h3>

            <div class="space-y-4">
              <div>
                <label
                  for="currentPassword"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Current Password</label
                >
                <input
                  id="currentPassword"
                  v-model="passwordForm.currentPassword"
                  type="password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>

              <div>
                <label
                  for="newPassword"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >New Password</label
                >
                <input
                  id="newPassword"
                  v-model="passwordForm.newPassword"
                  type="password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
                <p class="mt-1 text-sm text-gray-500">
                  Password must be at least 8 characters long with at least one
                  number and one special character.
                </p>
              </div>

              <div>
                <label
                  for="confirmPassword"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Confirm New Password</label
                >
                <input
                  id="confirmPassword"
                  v-model="passwordForm.confirmPassword"
                  type="password"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>

              <div class="pt-2">
                <button
                  @click="changePassword"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="isSaving"
                >
                  Update Password
                </button>
              </div>
            </div>
          </div>

          <!-- Two-Factor Authentication -->
          <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-base font-medium text-gray-900">
                  Two-Factor Authentication
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                  Add an extra layer of security to your account by requiring
                  both your password and a verification code.
                </p>
              </div>
              <div class="flex items-center">
                <button
                  v-if="!securitySettings.twoFactorEnabled"
                  @click="
                    securitySettings.twoFactorEnabled = true;
                    saveSettings('security');
                  "
                  class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md font-medium hover:bg-indigo-700 transition-colors"
                >
                  Enable
                </button>
                <span
                  v-else
                  class="bg-green-100 text-green-800 px-3 py-1 text-sm rounded-full font-medium flex items-center"
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
                  Enabled
                </span>
              </div>
            </div>
          </div>

          <!-- Login Notifications -->
          <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-base font-medium text-gray-900">
                  Login Notifications
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                  Receive email notifications when someone logs in to your
                  account from a new device or location.
                </p>
              </div>
              <div
                class="relative inline-block w-10 mr-2 align-middle select-none"
              >
                <input
                  type="checkbox"
                  id="toggle-login-alerts"
                  v-model="securitySettings.loginAlerts"
                  class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                />
                <label
                  for="toggle-login-alerts"
                  class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                  :class="
                    securitySettings.loginAlerts
                      ? 'bg-indigo-600'
                      : 'bg-gray-300'
                  "
                ></label>
              </div>
            </div>
          </div>

          <!-- Session Timeout -->
          <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="text-base font-medium text-gray-900 mb-3">
              Session Timeout
            </h3>
            <p class="text-sm text-gray-500 mb-3">
              Automatically log out after a period of inactivity for security.
            </p>

            <div class="flex items-center space-x-4">
              <select
                v-model="securitySettings.sessionTimeout"
                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="15">15 minutes</option>
                <option value="30">30 minutes</option>
                <option value="60">1 hour</option>
                <option value="120">2 hours</option>
                <option value="240">4 hours</option>
              </select>

              <button
                @click="saveSettings('security')"
                class="bg-indigo-600 text-white px-3 py-2 text-sm rounded-md font-medium hover:bg-indigo-700 transition-colors"
              >
                Save
              </button>
            </div>
          </div>

          <!-- Active Sessions -->
          <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-base font-medium text-gray-900 mb-3">
              Active Sessions
            </h3>
            <p class="text-sm text-gray-500 mb-3">
              These devices are currently logged in to your account. You can log
              out of any session that you don't recognize.
            </p>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Device
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Location
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Browser
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
                      Status
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Action
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(session, index) in sessionHistory"
                    :key="index"
                    class="hover:bg-gray-50"
                  >
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      {{ session.device }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ session.location }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ session.browser }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      {{ session.date }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="{
                          'bg-green-100 text-green-800':
                            session.status === 'Current Session',
                          'bg-blue-100 text-blue-800':
                            session.status === 'Active',
                          'bg-gray-100 text-gray-800':
                            session.status === 'Expired',
                        }"
                      >
                        {{ session.status }}
                      </span>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      <button
                        v-if="
                          session.status !== 'Current Session' &&
                          session.status !== 'Expired'
                        "
                        class="text-red-600 hover:text-red-900 font-medium transition-colors"
                      >
                        Revoke
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Notification Settings -->
      <div v-if="activeTab === 'notifications'" class="p-6">
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Notification Settings
          </h2>
          <p class="text-sm text-gray-500 mb-6">
            Configure how and when you receive notifications from the platform.
          </p>

          <!-- Email Notifications -->
          <div class="space-y-6">
            <div class="border border-gray-200 rounded-lg p-4">
              <h3 class="text-base font-medium text-gray-900 mb-4">
                Email Notifications
              </h3>

              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Project Updates
                    </h4>
                    <p class="text-xs text-gray-500">
                      Receive updates about changes to your projects
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-project-updates"
                      v-model="notificationPrefs.projectUpdates"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-project-updates"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        notificationPrefs.projectUpdates
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Meeting Reminders
                    </h4>
                    <p class="text-xs text-gray-500">
                      Get reminders about upcoming meetings and appointments
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-meeting-reminders"
                      v-model="notificationPrefs.meetingReminders"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-meeting-reminders"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        notificationPrefs.meetingReminders
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Resource Sharing
                    </h4>
                    <p class="text-xs text-gray-500">
                      Notifications when consultants share resources with you
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-resource-sharing"
                      v-model="notificationPrefs.resourceSharing"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-resource-sharing"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        notificationPrefs.resourceSharing
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Platform Updates
                    </h4>
                    <p class="text-xs text-gray-500">
                      Stay informed about new features and platform changes
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-platform-updates"
                      v-model="notificationPrefs.emailUpdates"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-platform-updates"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        notificationPrefs.emailUpdates
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Marketing Communications -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h3 class="text-base font-medium text-gray-900 mb-4">
                Marketing Communications
              </h3>

              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Newsletter Updates
                    </h4>
                    <p class="text-xs text-gray-500">
                      Receive our monthly newsletter with industry insights
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-newsletter"
                      v-model="notificationPrefs.newsletterUpdates"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-newsletter"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        notificationPrefs.newsletterUpdates
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Promotional Emails
                    </h4>
                    <p class="text-xs text-gray-500">
                      Receive emails about special offers and promotions
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-marketing"
                      v-model="notificationPrefs.marketingEmails"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-marketing"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        notificationPrefs.marketingEmails
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-end">
          <button
            @click="saveSettings('notifications')"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSaving"
          >
            <svg
              v-if="isSaving"
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
            <span v-if="isSaved" class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              Saved
            </span>
            <span v-else-if="isSaving">Saving...</span>
            <span v-else>Save Preferences</span>
          </button>
        </div>
      </div>

      <!-- Billing Settings -->
      <div v-if="activeTab === 'billing'" class="p-6">
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Billing & Plan Information
          </h2>

          <!-- Current Plan -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <div
              class="flex flex-col md:flex-row md:items-center md:justify-between"
            >
              <div>
                <h3 class="text-xl font-semibold text-gray-900">
                  {{ billingInfo.planType }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                  Billed {{ billingInfo.billingCycle.toLowerCase() }}
                </p>
                <div class="mt-2">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Active
                  </span>
                </div>
              </div>

              <div class="mt-4 md:mt-0">
                <button
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Upgrade Plan
                </button>
              </div>
            </div>

            <div class="mt-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">
                Plan Features
              </h4>
              <ul class="space-y-2">
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-600"
                    >Up to 10 active projects</span
                  >
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-600"
                    >Full access to consultant network</span
                  >
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-600">Priority support</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-600"
                    >Advanced analytics and reporting</span
                  >
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-600"
                    >20GB document storage</span
                  >
                </li>
              </ul>
            </div>

            <!-- Upcoming features -->
            <div class="mt-6 border-t border-gray-200 pt-4">
              <h4 class="text-sm font-medium text-gray-900 mb-3">
                Coming Soon to Your Plan
              </h4>
              <ul class="space-y-2">
                <li
                  v-for="feature in upcomingFeatures"
                  :key="feature.name"
                  class="flex items-start"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-indigo-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-sm text-gray-600"
                    >{{ feature.name }}
                    <span class="text-xs text-indigo-600"
                      >(Available {{ feature.available }})</span
                    ></span
                  >
                </li>
              </ul>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-base font-medium text-gray-900">
                Payment Method
              </h3>
              <button
                class="text-indigo-600 text-sm font-medium hover:text-indigo-700 transition-colors focus:outline-none"
              >
                Add Payment Method
              </button>
            </div>

            <div
              class="bg-gray-50 rounded-lg p-3 flex items-center justify-between"
            >
              <div class="flex items-center">
                <div class="bg-blue-500 rounded p-2 mr-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                    />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ billingInfo.paymentMethod }}
                  </p>
                  <p class="text-xs text-gray-500">Expires 09/2027</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  class="text-gray-600 hover:text-gray-900 transition-colors"
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
                  class="text-gray-600 hover:text-gray-900 transition-colors"
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

            <div class="mt-4 flex items-center">
              <input
                type="checkbox"
                id="auto-renew"
                v-model="billingInfo.autoRenew"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="auto-renew" class="ml-2 block text-sm text-gray-900">
                Auto-renew subscription (next billing date:
                {{ billingInfo.nextBillingDate }})
              </label>
            </div>
          </div>

          <!-- Billing History -->
          <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-base font-medium text-gray-900">
                Billing History
              </h3>
              <button
                class="text-indigo-600 text-sm font-medium hover:text-indigo-700 transition-colors focus:outline-none"
              >
                View All
              </button>
            </div>

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
                      Invoice
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(invoice, index) in billingHistory"
                    :key="index"
                    class="hover:bg-gray-50"
                  >
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      {{ invoice.date }}
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      {{ invoice.amount }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                      >
                        {{ invoice.status }}
                      </span>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900"
                    >
                      <a href="#" class="font-medium">Download</a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-end">
          <button
            @click="saveSettings('billing')"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSaving"
          >
            <svg
              v-if="isSaving"
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
            <span v-if="isSaved" class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              Saved
            </span>
            <span v-else-if="isSaving">Saving...</span>
            <span v-else>Save Changes</span>
          </button>
        </div>
      </div>

      <!-- Privacy Settings -->
      <div v-if="activeTab === 'privacy'" class="p-6">
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Privacy Settings
          </h2>
          <p class="text-sm text-gray-500 mb-6">
            Control who can see your information and how your data is used.
          </p>

          <div class="space-y-6">
            <!-- Profile Visibility -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h3 class="text-base font-medium text-gray-900 mb-4">
                Profile Visibility
              </h3>
              <p class="text-sm text-gray-500 mb-4">
                Choose who can view your profile and professional information on
                the platform.
              </p>

              <div class="space-y-3">
                <div class="flex items-center">
                  <input
                    type="radio"
                    id="visibility-public"
                    name="profile-visibility"
                    value="public"
                    v-model="privacySettings.profileVisibility"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label for="visibility-public" class="ml-3">
                    <span class="block text-sm font-medium text-gray-900"
                      >Public</span
                    >
                    <span class="block text-sm text-gray-500"
                      >Your profile is visible to everyone on the platform</span
                    >
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    type="radio"
                    id="visibility-consultants"
                    name="profile-visibility"
                    value="consultants"
                    v-model="privacySettings.profileVisibility"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label for="visibility-consultants" class="ml-3">
                    <span class="block text-sm font-medium text-gray-900"
                      >Consultants Only</span
                    >
                    <span class="block text-sm text-gray-500"
                      >Only verified consultants can view your full
                      profile</span
                    >
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    type="radio"
                    id="visibility-private"
                    name="profile-visibility"
                    value="private"
                    v-model="privacySettings.profileVisibility"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <label for="visibility-private" class="ml-3">
                    <span class="block text-sm font-medium text-gray-900"
                      >Private</span
                    >
                    <span class="block text-sm text-gray-500"
                      >Only consultants you're actively working with can view
                      your profile</span
                    >
                  </label>
                </div>
              </div>
            </div>

            <!-- Additional Privacy Settings -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h3 class="text-base font-medium text-gray-900 mb-4">
                Additional Privacy Controls
              </h3>

              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Share Activity Data
                    </h4>
                    <p class="text-xs text-gray-500">
                      Allow the platform to use your activity data to improve
                      recommendations
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-activity-data"
                      v-model="privacySettings.shareActivityData"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-activity-data"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        privacySettings.shareActivityData
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Direct Messaging
                    </h4>
                    <p class="text-xs text-gray-500">
                      Allow consultants to send you direct messages
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-messaging"
                      v-model="privacySettings.allowMessaging"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-messaging"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        privacySettings.allowMessaging
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Online Status
                    </h4>
                    <p class="text-xs text-gray-500">
                      Show when you're active on the platform
                    </p>
                  </div>
                  <div
                    class="relative inline-block w-10 mr-2 align-middle select-none"
                  >
                    <input
                      type="checkbox"
                      id="toggle-online-status"
                      v-model="privacySettings.showOnlineStatus"
                      class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                    />
                    <label
                      for="toggle-online-status"
                      class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"
                      :class="
                        privacySettings.showOnlineStatus
                          ? 'bg-indigo-600'
                          : 'bg-gray-300'
                      "
                    ></label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Data Management -->
            <div class="border border-gray-200 rounded-lg p-4">
              <h3 class="text-base font-medium text-gray-900 mb-4">
                Data Management
              </h3>

              <div class="space-y-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-900">
                    Download Your Data
                  </h4>
                  <p class="text-xs text-gray-500 mb-2">
                    Request a copy of all your personal data that we store.
                  </p>
                  <button
                    class="bg-indigo-600 text-white px-3 py-1.5 text-sm rounded-md font-medium hover:bg-indigo-700 transition-colors"
                  >
                    Request Data Export
                  </button>
                </div>

                <div class="pt-4 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 text-red-600">
                    Delete Account
                  </h4>
                  <p class="text-xs text-gray-500 mb-2">
                    Permanently delete your account and all associated data.
                  </p>
                  <button
                    class="bg-red-600 text-white px-3 py-1.5 text-sm rounded-md font-medium hover:bg-red-700 transition-colors"
                  >
                    Delete Account
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-end">
          <button
            @click="saveSettings('privacy')"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSaving"
          >
            <svg
              v-if="isSaving"
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
            <span v-if="isSaved" class="flex items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
              Saved
            </span>
            <span v-else-if="isSaving">Saving...</span>
            <span v-else>Save Changes</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
