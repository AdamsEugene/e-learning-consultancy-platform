<!-- components/dashboard/settings/LinkedAccountsList.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Define props
defineProps({
  // If needed, add props here
});

// Define emits for parent communication
const emit = defineEmits(["update-message"]);

// State for linked accounts
const linkedAccounts = ref([
  {
    id: "google",
    name: "Google",
    description: "Login and synchronize with Google services",
    icon: "google",
    connected: false,
    username: "",
    lastUsed: null,
  },
  {
    id: "facebook",
    name: "Facebook",
    description: "Login with Facebook account",
    icon: "facebook",
    connected: false,
    username: "",
    lastUsed: null,
  },
  {
    id: "github",
    name: "GitHub",
    description: "Connect to access coding courses and projects",
    icon: "github",
    connected: true,
    username: "emilyjohnson",
    lastUsed: "2023-12-15T18:30:00Z",
  },
  {
    id: "linkedin",
    name: "LinkedIn",
    description: "Display certificates on your LinkedIn profile",
    icon: "linkedin",
    connected: true,
    username: "emily-johnson-dev",
    lastUsed: "2023-11-05T09:45:00Z",
  },
  {
    id: "twitter",
    name: "Twitter",
    description: "Share your achievements on Twitter",
    icon: "twitter",
    connected: false,
    username: "",
    lastUsed: null,
  },
  {
    id: "apple",
    name: "Apple",
    description: "Sign in with your Apple ID",
    icon: "apple",
    connected: false,
    username: "",
    lastUsed: null,
  },
  {
    id: "discord",
    name: "Discord",
    description: "Connect to join course discussion channels",
    icon: "discord",
    connected: false,
    username: "",
    lastUsed: null,
  },
  {
    id: "slack",
    name: "Slack",
    description: "Get notifications in your Slack workspace",
    icon: "slack",
    connected: false,
    username: "",
    lastUsed: null,
  },
]);

// Loading states
const isLoading = ref(false);
const processingAccountId = ref<string | null>(null);

// Confirmation modal
const showDisconnectModal = ref(false);
const accountToDisconnect = ref<string | null>(null);

// Connect an account
const connectAccount = async (id: string) => {
  processingAccountId.value = id;

  try {
    // Simulate API call to connect account
    // In a real app, this would redirect to OAuth flow
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // Find the account and update its status
    const account = linkedAccounts.value.find((acc) => acc.id === id);
    if (account) {
      account.connected = true;

      // For demo purposes, set a username based on the service
      switch (id) {
        case "google":
          account.username = "emily.johnson@gmail.com";
          break;
        case "facebook":
          account.username = "Emily Johnson";
          break;
        case "twitter":
          account.username = "emilycodes";
          break;
        case "apple":
          account.username = "emily.johnson@icloud.com";
          break;
        case "discord":
          account.username = "emilycodes#1234";
          break;
        case "slack":
          account.username = "emily.johnson";
          break;
        default:
          account.username = "emilyjohnson";
      }

      account.lastUsed = new Date().toISOString();

      emit(
        "update-message",
        "success",
        `Your ${account.name} account has been connected successfully`
      );
    }
  } catch (error) {
    emit(
      "update-message",
      "error",
      `Failed to connect ${id}. Please try again.`
    );
  } finally {
    processingAccountId.value = null;
  }
};

// Disconnect an account - confirm first
const confirmDisconnect = (id: string) => {
  accountToDisconnect.value = id;
  showDisconnectModal.value = true;
};

// Cancel disconnection
const cancelDisconnect = () => {
  accountToDisconnect.value = null;
  showDisconnectModal.value = false;
};

// Proceed with account disconnection
const disconnectAccount = async () => {
  if (!accountToDisconnect.value) return;

  const id = accountToDisconnect.value;
  processingAccountId.value = id;

  try {
    // Simulate API call to disconnect
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Find and update the account
    const account = linkedAccounts.value.find((acc) => acc.id === id);
    if (account) {
      account.connected = false;
      account.username = "";
      account.lastUsed = null;

      emit(
        "update-message",
        "success",
        `Your ${account.name} account has been disconnected`
      );
    }
  } catch (error) {
    emit(
      "update-message",
      "error",
      `Failed to disconnect ${id}. Please try again.`
    );
  } finally {
    processingAccountId.value = null;
    showDisconnectModal.value = false;
    accountToDisconnect.value = null;
  }
};

// Format date for display
const formatDate = (dateString: string | null): string => {
  if (!dateString) return "Never";

  const date = new Date(dateString);

  // Check if date is valid
  if (isNaN(date.getTime())) return "Unknown";

  // Format the date - e.g. "Dec 15, 2023"
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format time elapsed since last use
const getTimeElapsed = (dateString: string | null): string => {
  if (!dateString) return "";

  const date = new Date(dateString);
  const now = new Date();

  // Check if date is valid
  if (isNaN(date.getTime())) return "";

  const diffMs = now.getTime() - date.getTime();
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  if (diffDays === 0) {
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    if (diffHours === 0) {
      const diffMinutes = Math.floor(diffMs / (1000 * 60));
      return `${diffMinutes} minute${diffMinutes !== 1 ? "s" : ""} ago`;
    }
    return `${diffHours} hour${diffHours !== 1 ? "s" : ""} ago`;
  } else if (diffDays < 30) {
    return `${diffDays} day${diffDays !== 1 ? "s" : ""} ago`;
  } else if (diffDays < 365) {
    const diffMonths = Math.floor(diffDays / 30);
    return `${diffMonths} month${diffMonths !== 1 ? "s" : ""} ago`;
  } else {
    const diffYears = Math.floor(diffDays / 365);
    return `${diffYears} year${diffYears !== 1 ? "s" : ""} ago`;
  }
};

// Helper function to render correct SVG icon
const renderIcon = (id: string): string => {
  switch (id) {
    case "google":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z" fill="#4285F4"/>
        <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z" fill="#4285F4"/>
      </svg>`;
    case "facebook":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
      </svg>`;
    case "github":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" fill="#181717"/>
      </svg>`;
    case "linkedin":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="#0A66C2"/>
      </svg>`;
    case "twitter":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" fill="#1DA1F2"/>
      </svg>`;
    case "apple":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M11.624 7.222c-.47 0-1.347-.725-2.958-.695-1.53.03-2.937.885-3.722 2.252-1.587 2.756-.407 6.82 1.14 9.058.75 1.095 1.647 2.312 2.827 2.27 1.138-.047 1.567-.734 2.94-.734 1.374 0 1.764.734 2.958.734 1.222-.018 1.998-1.11 2.748-2.203.865-1.263 1.22-2.485 1.24-2.55-.026-.025-2.395-.934-2.417-3.68-.023-2.29 1.878-3.39 1.965-3.45-.5-.789-1.793-1.64-3.306-1.64-1.508 0-2.945.82-3.415.82zm2.75-2.404c.779-.942 1.307-2.253 1.164-3.562-1.125.048-2.484.752-3.29 1.694-.724.834-1.35 2.182-1.184 3.469 1.26.093 2.54-.641 3.31-1.6z" fill="#000000"/>
      </svg>`;
    case "discord":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189Z" fill="#5865F2"/>
      </svg>`;
    case "slack":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z" fill="#E01E5A"/>
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="12" fill="#9CA3AF"/>
        <path d="M12 6v6m0 4h.01" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`;
  }
};

// Fetch linked accounts
const fetchLinkedAccounts = async () => {
  isLoading.value = true;

  try {
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 500));

    // In our demo, we already have the accounts data pre-populated
    // This would be where we get data from the API
  } catch (error) {
    emit("update-message", "error", "Failed to load linked accounts");
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchLinkedAccounts();
});
</script>

<template>
  <div class="linked-accounts p-6">
    <!-- Section header -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold text-gray-900">Linked Accounts</h2>
      <p class="mt-1 text-sm text-gray-500">
        Connect third-party accounts to enable single sign-on and additional
        features
      </p>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="py-12 flex justify-center">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"
      ></div>
    </div>

    <!-- Accounts list -->
    <div v-else class="divide-y divide-gray-200">
      <div
        v-for="account in linkedAccounts"
        :key="account.id"
        class="py-5 first:pt-0 last:pb-0 transition-colors hover:bg-gray-50"
      >
        <div class="flex items-center justify-between">
          <!-- Account info -->
          <div class="flex items-center">
            <!-- Icon -->
            <div
              class="flex-shrink-0 mr-4 w-12 h-12 flex items-center justify-center"
              v-html="renderIcon(account.id)"
            ></div>

            <!-- Account details -->
            <div>
              <h3 class="text-md font-medium text-gray-900">
                {{ account.name }}
              </h3>
              <div class="text-sm text-gray-500">{{ account.description }}</div>

              <!-- Connected details, if any -->
              <div
                v-if="account.connected"
                class="mt-1 text-sm text-indigo-600 flex items-center"
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
                <span>
                  Connected as
                  <span class="font-medium">{{ account.username }}</span>
                  <span v-if="account.lastUsed" class="ml-1 text-gray-500"
                    >(Last used {{ getTimeElapsed(account.lastUsed) }})</span
                  >
                </span>
              </div>
            </div>
          </div>

          <!-- Connect/Disconnect button -->
          <div class="ml-4">
            <button
              v-if="!account.connected"
              @click="connectAccount(account.id)"
              class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
              :disabled="processingAccountId === account.id"
            >
              <svg
                v-if="processingAccountId === account.id"
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
                processingAccountId === account.id ? "Connecting..." : "Connect"
              }}</span>
            </button>

            <button
              v-else
              @click="confirmDisconnect(account.id)"
              class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
              :disabled="processingAccountId === account.id"
            >
              <svg
                v-if="processingAccountId === account.id"
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
              <span>{{
                processingAccountId === account.id
                  ? "Disconnecting..."
                  : "Disconnect"
              }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-if="linkedAccounts.length === 0 && !isLoading"
      class="bg-gray-50 rounded-lg p-8 text-center my-4"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-12 w-12 mx-auto text-gray-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
        />
      </svg>
      <h3 class="mt-2 text-lg font-medium text-gray-900">
        No accounts to link
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        There are currently no third-party accounts available for linking.
      </p>
    </div>

    <!-- Disconnect confirmation modal -->
    <div
      v-if="showDisconnectModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
        ></div>

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
          <div>
            <div
              class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-yellow-600"
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
            <div class="mt-3 text-center sm:mt-5">
              <h3
                class="text-lg leading-6 font-medium text-gray-900"
                id="modal-title"
              >
                Disconnect Account
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to disconnect this account? You will
                  need to reconnect later if you want to use it again.
                </p>
              </div>
            </div>
          </div>
          <div
            class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
          >
            <button
              type="button"
              @click="disconnectAccount"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm"
              :disabled="processingAccountId !== null"
            >
              <svg
                v-if="processingAccountId !== null"
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
              {{
                processingAccountId !== null ? "Disconnecting..." : "Disconnect"
              }}
            </button>
            <button
              type="button"
              @click="cancelDisconnect"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
              :disabled="processingAccountId !== null"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation for account items */
.linked-accounts-enter-active,
.linked-accounts-leave-active {
  transition: all 0.3s ease;
}

.linked-accounts-enter-from,
.linked-accounts-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
