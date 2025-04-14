<!-- pages/dashboard/settings.vue -->
<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

// Page meta
definePageMeta({
  layout: "dashboard",
});

useHead({
  title: "Account Settings - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Manage your account settings, preferences, and profile information",
    },
  ],
});

// Router and route handling for active section
const route = useRoute();
const router = useRouter();

const activeSection = ref("profile");

// Get active section from route query parameter or default to 'profile'
onMounted(() => {
  const section = route.query.section as string;
  if (
    section &&
    [
      "profile",
      "account",
      "security",
      "notifications",
      "privacy",
      "accessibility",
    ].includes(section)
  ) {
    activeSection.value = section;
  } else if (!section) {
    // Update URL to include default section
    router.replace({ query: { ...route.query, section: "profile" } });
  }
});

// Change active section
const changeSection = (section: string) => {
  activeSection.value = section;
  router.replace({ query: { ...route.query, section } });
};

// Form message state used by child components
const formMessage = reactive({
  type: "",
  message: "",
});

// Update form message
const updateFormMessage = (type: string, message: string) => {
  formMessage.type = type;
  formMessage.message = message;

  // Auto-clear success messages after 5 seconds
  if (type === "success") {
    setTimeout(() => {
      if (formMessage.type === "success" && formMessage.message === message) {
        formMessage.type = "";
        formMessage.message = "";
      }
    }, 5000);
  }
};

// Sidebar navigation items
const sidebarItems = [
  {
    id: "profile",
    label: "Profile Information",
    icon: "profile",
    description: "Manage your personal information and social media profiles",
  },
  {
    id: "account",
    label: "Account Preferences",
    icon: "cog",
    description: "Modify your account settings and preferences",
  },
  {
    id: "payment",
    label: "Payment Methods",
    icon: "credit-card",
    description: "Manage your payment methods and billing information",
  },
  {
    id: "linked-accounts",
    label: "Linked Accounts",
    icon: "link",
    description: "Connect and manage third-party account integrations",
  },
  {
    id: "security",
    label: "Security",
    icon: "shield",
    description: "Update password and security options",
  },
  {
    id: "notifications",
    label: "Notifications",
    icon: "bell",
    description: "Control email and push notification preferences",
  },
  {
    id: "privacy",
    label: "Privacy & Data",
    icon: "lock",
    description: "Manage your privacy settings and data preferences",
  },
  {
    id: "accessibility",
    label: "Accessibility",
    icon: "accessibility",
    description: "Customize appearance and accessibility features",
  },
  {
    id: "delete-account",
    label: "Delete Account",
    icon: "trash",
    description: "Permanently delete your account and data",
  },
];

// Animation state
const isLoaded = ref(false);

onMounted(() => {
  // Small delay for loading animation
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

// Helper function to render the correct icon SVG based on ID
const renderIcon = (iconId: string) => {
  switch (iconId) {
    case "profile":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
      </svg>`;
    case "cog":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
      </svg>`;
    case "credit-card":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
      </svg>`;
    case "link":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
      </svg>`;
    case "shield":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "bell":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
      </svg>`;
    case "lock":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
      </svg>`;
    case "accessibility":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
      </svg>`;
    case "trash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};
</script>

<template>
  <div class="settings-page pb-12">
    <!-- Page header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Account Settings</h1>
      <p class="text-gray-600">
        Manage your profile, account preferences, and privacy settings
      </p>
    </div>

    <!-- Status message -->
    <div
      v-if="formMessage.message"
      class="mb-6 px-6 py-4 rounded-lg transition-all duration-300 transform"
      :class="{
        'bg-green-50 text-green-700 border border-green-200':
          formMessage.type === 'success',
        'bg-red-50 text-red-700 border border-red-200':
          formMessage.type === 'error',
      }"
    >
      <div class="flex items-center">
        <div v-if="formMessage.type === 'success'" class="flex-shrink-0">
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
        <div v-else-if="formMessage.type === 'error'" class="flex-shrink-0">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-red-500"
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
        <div class="ml-3">
          <p class="text-sm font-medium">{{ formMessage.message }}</p>
        </div>
      </div>
    </div>

    <!-- Settings container with sidebar -->
    <div
      class="settings-container flex flex-col lg:flex-row gap-8 transition-all duration-300 transform opacity-0 translate-y-4"
      :class="{ 'opacity-100 translate-y-0': isLoaded }"
    >
      <!-- Sidebar navigation -->
      <div class="lg:w-1/4 xl:w-1/5">
        <div
          class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 sticky top-6"
        >
          <nav class="divide-y divide-gray-200">
            <div
              v-for="item in sidebarItems"
              :key="item.id"
              class="relative"
              :class="{ 'bg-indigo-50': activeSection === item.id }"
            >
              <button
                @click="changeSection(item.id)"
                class="w-full px-4 py-3 flex items-center text-left transition-colors duration-200 hover:bg-gray-50"
                :class="
                  activeSection === item.id
                    ? 'text-indigo-600'
                    : 'text-gray-700'
                "
                :aria-current="activeSection === item.id ? 'page' : undefined"
              >
                <!-- Left border indicator for active section -->
                <span
                  v-if="activeSection === item.id"
                  class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r"
                  aria-hidden="true"
                ></span>

                <!-- Icon -->
                <span
                  class="flex-shrink-0 mr-3 transition-colors duration-200"
                  :class="
                    activeSection === item.id
                      ? 'text-indigo-600'
                      : 'text-gray-500'
                  "
                  v-html="renderIcon(item.icon)"
                ></span>

                <span class="flex-1">
                  <span
                    class="block text-sm font-medium"
                    :class="
                      activeSection === item.id
                        ? 'text-indigo-600'
                        : 'text-gray-900'
                    "
                  >
                    {{ item.label }}
                  </span>
                  <span
                    class="block text-xs mt-0.5"
                    :class="
                      activeSection === item.id
                        ? 'text-indigo-500'
                        : 'text-gray-500'
                    "
                  >
                    {{ item.description }}
                  </span>
                </span>

                <!-- Right arrow icon -->
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transform transition-transform duration-200"
                  :class="{ 'translate-x-1': activeSection === item.id }"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </nav>
        </div>
      </div>

      <!-- Settings content area -->
      <div class="flex-1">
        <div
          class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200"
        >
          <!-- Profile Settings -->
          <DashboardStudentSettingsProfileSettingsForm
            v-if="activeSection === 'profile'"
            @update-message="updateFormMessage"
          />

          <!-- Account Settings -->
          <DashboardStudentSettingsAccountSettingsForm
            v-else-if="activeSection === 'account'"
            @update-message="updateFormMessage"
          />

          <!-- Payment Methods -->
          <DashboardStudentSettingsPaymentMethodsList
            v-else-if="activeSection === 'payment'"
            @update-message="updateFormMessage"
          />

          <!-- Linked Accounts -->
          <DashboardStudentSettingsLinkedAccountsList
            v-else-if="activeSection === 'linked-accounts'"
            @update-message="updateFormMessage"
          />

          <!-- Security Settings -->
          <DashboardStudentSettingsSecuritySettingsForm
            v-else-if="activeSection === 'security'"
            @update-message="updateFormMessage"
          />

          <!-- Notification Settings -->
          <DashboardStudentSettingsNotificationSettingsForm
            v-else-if="activeSection === 'notifications'"
            @update-message="updateFormMessage"
          />

          <!-- Privacy Settings -->
          <DashboardStudentSettingsPrivacySettingsForm
            v-else-if="activeSection === 'privacy'"
            @update-message="updateFormMessage"
          />

          <!-- Accessibility Settings -->
          <DashboardStudentSettingsAccessibilitySettingsForm
            v-else-if="activeSection === 'accessibility'"
            @update-message="updateFormMessage"
          />

          <!-- Delete Account -->
          <DashboardStudentSettingsDeleteAccountForm
            v-else-if="activeSection === 'delete-account'"
            @update-message="updateFormMessage"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation classes */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.settings-container {
  animation: fadeIn 0.3s ease-out;
}
</style>
