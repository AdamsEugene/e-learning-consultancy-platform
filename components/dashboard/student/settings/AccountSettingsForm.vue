<!-- components/dashboard/settings/AccountSettingsForm.vue -->
<script setup lang="ts">
import { ref, reactive } from "vue";
import SettingsHeader from "./SettingsHeader.vue";

const emit = defineEmits<{
  (e: "update-message", type: string, message: string): void;
}>();

// Account settings state
const accountSettings = reactive({
  language: "English",
  darkMode: false,
  receiveEmails: true,
});

// Form state
const isSubmitting = ref(false);

// Mock linked accounts data
const linkedAccounts = ref([
  {
    id: 1,
    provider: "google",
    name: "Google",
    connected: false,
    email: null,
    icon: `<svg class="h-8 w-8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
        <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"></path>
        <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"></path>
        <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"></path>
        <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"></path>
      </g>
    </svg>`,
  },
  {
    id: 2,
    provider: "facebook",
    name: "Facebook",
    connected: false,
    email: null,
    icon: `<svg class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"></path>
    </svg>`,
  },
  {
    id: 3,
    provider: "linkedin",
    name: "LinkedIn",
    connected: false,
    email: null,
    icon: `<svg class="h-8 w-8 text-blue-700" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
    </svg>`,
  },
  {
    id: 4,
    provider: "github",
    name: "GitHub",
    connected: false,
    email: null,
    icon: `<svg class="h-8 w-8 text-gray-900" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"></path>
    </svg>`,
  },
]);

// Mock payment methods data
const paymentMethods = ref([
  {
    id: 1,
    type: "card",
    last4: "4242",
    expiry: "12/2025",
    brand: "visa",
    isDefault: true,
    icon: `<svg class="h-8 w-8 text-blue-700" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path d="M10.8456 7.48875H8.1321L6.5 16.5112H9.2135L10.8456 7.48875Z" />
      <path d="M17.2796 7.67773C16.5895 7.42263 15.5347 7.14844 14.2246 7.14844C11.5112 7.14844 9.6 8.49334 9.5 10.4233C9.4 11.8638 10.8231 12.6674 11.9 13.1489C13.0001 13.6421 13.4 13.9657 13.4 14.4005C13.4 15.0772 12.5447 15.3889 11.7545 15.3889C10.5656 15.3889 9.9328 15.1994 8.9554 14.7854L8.5 14.5959L8 16.8048C8.8 17.1401 10.1991 17.4399 11.6544 17.4399C14.5231 17.4399 16.4 16.1186 16.4 14.0408C16.4 12.941 15.6436 12.0887 14.1 11.4001C13.1446 10.9661 12.5446 10.6664 12.5446 10.1971C12.5446 9.77205 13.0348 9.34591 14.1449 9.34591C15.0547 9.33591 15.7452 9.56192 16.2909 9.81702L16.6 9.97398L17.2796 7.67773Z" />
      <path d="M21.9999 7.48926H19.857C19.157 7.48926 18.6455 7.70404 18.3464 8.42078L15.3999 16.5115H18.2578C18.2578 16.5115 18.6007 15.5018 18.6897 15.2411C18.9564 15.2411 21.2464 15.2411 21.5798 15.2411C21.6464 15.5582 21.842 16.5115 21.842 16.5115H24.3996L21.9999 7.48926ZM19.3782 13.1708C19.5664 12.6777 20.3445 10.4231 20.3445 10.4231C20.3334 10.4458 20.5556 9.85841 20.6778 9.49922L20.8443 10.3635C20.8443 10.3635 21.3254 12.7554 21.4132 13.1708H19.3782Z" />
      <path d="M0 7.48926L0.0211 7.60393C0.0211 7.60393 1.7991 14.1519 2.1325 15.2639C2.4103 15.2639 4.5782 15.2639 5.189 15.2639C6.6223 10.7556 7.6 7.48926 7.6 7.48926H4.8L2.45 13.0107C2.3621 12.7384 2.0399 11.7627 1.6732 10.6507C1.1164 8.94 0.6621 7.48926 0.6621 7.48926H0Z" />
    </svg>`,
  },
  {
    id: 2,
    type: "paypal",
    email: "emily.johnson@example.com",
    isDefault: false,
    icon: `<svg class="h-8 w-8 text-blue-800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
      <path d="M9.145 4.01C8.434 4.01 7.824 4.551 7.673 5.362C6.121 12.563 6.218 12.12 6.218 12.861C6.218 13.854 6.972 14.61 7.961 14.61H9.789C10.5 14.61 11.11 14.069 11.262 13.258C11.628 11.425 11.75 10.869 11.75 10.128C11.75 9.135 10.995 8.379 10.006 8.379H9.145C8.837 8.379 8.589 8.131 8.589 7.823C8.589 7.515 8.837 7.267 9.145 7.267H10.875C11.586 7.267 12.196 6.726 12.348 5.915C12.483 5.181 12.54 4.83 12.541 4.417C12.541 4.19 12.36 4.01 12.134 4.01H9.145Z" />
      <path d="M13.973 8.379C13.264 8.379 12.653 8.92 12.501 9.731C12.135 11.564 12.013 12.12 12.013 12.861C12.013 13.854 12.768 14.61 13.757 14.61H15.586C16.295 14.61 16.905 14.069 17.057 13.258C17.191 12.524 17.249 12.174 17.25 11.76C17.25 11.533 17.069 11.352 16.842 11.352H14.962C14.654 11.352 14.406 11.104 14.406 10.796C14.406 10.488 14.654 10.239 14.962 10.239H16.694C17.405 10.239 18.015 9.699 18.167 8.888C18.302 8.153 18.358 7.803 18.36 7.39C18.36 7.163 18.179 6.982 17.952 6.982H15.824C15.115 6.982 14.505 7.523 14.353 8.334C14.309 8.557 14.27 8.762 14.234 8.95H14.835C15.143 8.95 15.391 9.198 15.391 9.506C15.391 9.814 15.143 10.062 14.835 10.062H13.973C13.665 10.062 13.417 9.814 13.417 9.506C13.417 9.198 13.665 8.95 13.973 8.95V8.379Z" />
      <path d="M19.954 4.58C19.859 4.26 19.548 4.01 19.201 4.01C17.408 4.01 5.779 4.01 4.799 4.01C4.452 4.01 4.141 4.261 4.047 4.582C3.679 5.755 3.017 8.989 3.017 12.5C3.017 16.011 3.679 19.245 4.046 20.415C4.141 20.737 4.453 20.988 4.8 20.988C5.806 20.988 18.298 20.988 19.2 20.988C19.547 20.988 19.858 20.737 19.953 20.416C20.321 19.247 20.983 16.013 20.983 12.502C20.983 8.991 20.321 5.758 19.954 4.58ZM4.817 5.128H19.183C19.489 6.148 20.102 9.132 20.102 12.502C20.102 15.871 19.489 18.855 19.183 19.872H4.817C4.511 18.853 3.898 15.869 3.898 12.499C3.898 9.129 4.511 6.146 4.817 5.128Z" />
    </svg>`,
  },
]);

// Methods
const saveSettings = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    emit("update-message", "success", "Account settings updated successfully!");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem updating your account settings."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const toggleDarkMode = async () => {
  accountSettings.darkMode = !accountSettings.darkMode;
  await saveSettings();
};

const connectAccount = async (provider: string) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Find the account and update it
    const account = linkedAccounts.value.find(
      (acc) => acc.provider === provider
    );
    if (account) {
      account.connected = true;
      account.email = "emily.johnson@example.com";
    }

    emit(
      "update-message",
      "success",
      `Connected to ${
        provider.charAt(0).toUpperCase() + provider.slice(1)
      } successfully.`
    );
  } catch (error) {
    emit(
      "update-message",
      "error",
      `There was a problem connecting to ${
        provider.charAt(0).toUpperCase() + provider.slice(1)
      }.`
    );
  } finally {
    isSubmitting.value = false;
  }
};

const disconnectAccount = async (provider: string) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Find the account and update it
    const account = linkedAccounts.value.find(
      (acc) => acc.provider === provider
    );
    if (account) {
      account.connected = false;
      account.email = null;
    }

    emit(
      "update-message",
      "success",
      `Disconnected from ${
        provider.charAt(0).toUpperCase() + provider.slice(1)
      } successfully.`
    );
  } catch (error) {
    emit(
      "update-message",
      "error",
      `There was a problem disconnecting from ${
        provider.charAt(0).toUpperCase() + provider.slice(1)
      }.`
    );
  } finally {
    isSubmitting.value = false;
  }
};

const removePaymentMethod = async (id: number) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Remove the payment method
    paymentMethods.value = paymentMethods.value.filter(
      (method) => method.id !== id
    );

    emit("update-message", "success", "Payment method removed successfully.");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem removing the payment method."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const setDefaultPaymentMethod = async (id: number) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Update all payment methods
    paymentMethods.value.forEach((method) => {
      method.isDefault = method.id === id;
    });

    emit(
      "update-message",
      "success",
      "Default payment method updated successfully."
    );
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem updating the default payment method."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const addPaymentMethod = async () => {
  emit(
    "update-message",
    "info",
    "Payment method functionality would be implemented with a real payment processor like Stripe."
  );
};

const deleteAccount = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    emit(
      "update-message",
      "success",
      "Account deletion initiated. You will receive a confirmation email shortly."
    );
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem processing your account deletion request."
    );
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div>
    <!-- Section header -->
    <SettingsHeader
      title="Account Preferences"
      description="Manage your account settings, linked accounts, and payment methods"
    />

    <!-- Form content -->
    <div class="p-6 space-y-8">
      <div class="max-w-3xl mx-auto">
        <!-- Account Preferences -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Account Preferences
          </h3>
          <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <div class="space-y-6">
              <div>
                <label
                  for="language"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Language</label
                >
                <select
                  id="language"
                  v-model="accountSettings.language"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="English">English</option>
                  <option value="Spanish">Spanish</option>
                  <option value="French">French</option>
                  <option value="German">German</option>
                  <option value="Japanese">Japanese</option>
                  <option value="Chinese">Chinese (Simplified)</option>
                  <option value="Portuguese">Portuguese</option>
                  <option value="Arabic">Arabic</option>
                </select>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Dark Mode</h4>
                    <p class="text-sm text-gray-500">
                      Switch between light and dark themes
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accountSettings.darkMode"
                        @change="toggleDarkMode"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Email Communications
                    </h4>
                    <p class="text-sm text-gray-500">
                      Receive emails about your account
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accountSettings.receiveEmails"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Linked Accounts -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Linked Accounts
          </h3>
          <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <div class="space-y-4">
              <div
                v-for="account in linkedAccounts"
                :key="account.id"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <div class="mr-4" v-html="account.icon"></div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      {{ account.name }}
                    </h4>
                    <p v-if="account.connected" class="text-sm text-gray-500">
                      {{ account.email }}
                    </p>
                    <p v-else class="text-sm text-gray-500">Not connected</p>
                  </div>
                </div>
                <button
                  v-if="!account.connected"
                  type="button"
                  @click="connectAccount(account.provider)"
                  :disabled="isSubmitting"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <span
                    v-if="
                      isSubmitting &&
                      linkedAccounts.find(
                        (a) => a.provider === account.provider && !a.connected
                      )
                    "
                  >
                    Connecting...
                  </span>
                  <span v-else>Connect</span>
                </button>
                <button
                  v-else
                  type="button"
                  @click="disconnectAccount(account.provider)"
                  :disabled="isSubmitting"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <span
                    v-if="
                      isSubmitting &&
                      linkedAccounts.find(
                        (a) => a.provider === account.provider && a.connected
                      )
                    "
                  >
                    Disconnecting...
                  </span>
                  <span v-else>Disconnect</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Payment Methods</h3>
            <button
              type="button"
              @click="addPaymentMethod"
              class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
              Add Payment Method
            </button>
          </div>
          <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <div class="space-y-4">
              <div
                v-for="method in paymentMethods"
                :key="method.id"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <div class="mr-4" v-html="method.icon"></div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      {{
                        method.type === "card"
                          ? `${
                              method.brand.charAt(0).toUpperCase() +
                              method.brand.slice(1)
                            } ending in ${method.last4}`
                          : "PayPal"
                      }}
                      <span
                        v-if="method.isDefault"
                        class="ml-2 bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full"
                        >Default</span
                      >
                    </h4>
                    <p class="text-sm text-gray-500">
                      {{
                        method.type === "card"
                          ? `Expires ${method.expiry}`
                          : method.email
                      }}
                    </p>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button
                    v-if="!method.isDefault"
                    type="button"
                    @click="setDefaultPaymentMethod(method.id)"
                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
                  >
                    Make Default
                  </button>
                  <span v-if="!method.isDefault" class="text-gray-300">|</span>
                  <button
                    type="button"
                    @click="removePaymentMethod(method.id)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Delete Account -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Account</h3>
          <div class="bg-red-50 rounded-lg p-6 border border-red-200">
            <div class="space-y-4">
              <div>
                <h4 class="text-base font-medium text-red-800">
                  Permanently Delete Your Account
                </h4>
                <p class="text-sm text-red-700 mt-1">
                  Once you delete your account, there is no going back. Please
                  be certain.
                </p>
              </div>
              <button
                type="button"
                @click="deleteAccount"
                class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center"
                :disabled="isSubmitting"
              >
                <svg
                  v-if="isSubmitting"
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
                {{ isSubmitting ? "Processing..." : "Delete Account" }}
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            type="button"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="saveSettings"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSubmitting"
          >
            <svg
              v-if="isSubmitting"
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
            {{ isSubmitting ? "Saving..." : "Save Changes" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Toggle switch styles */
.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: 0.3s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
}

input:checked + .slider {
  background-color: #4f46e5;
}

input:focus + .slider {
  box-shadow: 0 0 1px #4f46e5;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
