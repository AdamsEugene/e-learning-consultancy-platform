<!-- components/dashboard/settings/SecuritySettingsForm.vue -->
<script setup lang="ts">
import { ref, reactive, computed } from "vue";
import SessionsList from "./SessionsList.vue";

const emit = defineEmits<{
  (e: "update-message", type: string, message: string): void;
}>();

// Account settings state
const accountSettings = reactive({
  twoFactorAuth: false,
});

// Form states
const isSubmitting = ref(false);
const isChangingPassword = ref(false);

// Password form
const passwordForm = reactive({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

// Password strength indicators
const passwordStrength = computed(() => {
  const password = passwordForm.newPassword;
  if (!password) return { score: 0, label: "None", color: "gray-300" };

  let score = 0;
  if (password.length >= 8) score += 1;
  if (/[A-Z]/.test(password)) score += 1;
  if (/[a-z]/.test(password)) score += 1;
  if (/[0-9]/.test(password)) score += 1;
  if (/[^A-Za-z0-9]/.test(password)) score += 1;

  const scoreMappings = [
    { score: 0, label: "Very Weak", color: "red-500" },
    { score: 1, label: "Weak", color: "red-500" },
    { score: 2, label: "Fair", color: "yellow-500" },
    { score: 3, label: "Good", color: "green-400" },
    { score: 4, label: "Strong", color: "green-500" },
    { score: 5, label: "Very Strong", color: "green-600" },
  ];

  return scoreMappings[score];
});

const passwordsMatch = computed(() => {
  return (
    passwordForm.newPassword &&
    passwordForm.newPassword === passwordForm.confirmPassword
  );
});

// Mock sessions data
const activeSessions = ref([
  {
    id: 1,
    deviceName: "Chrome on macOS",
    location: "San Francisco, CA, USA",
    time: "Active now",
    lastActive: new Date(),
    isCurrent: true,
    deviceType: "desktop",
  },
  {
    id: 2,
    deviceName: "Safari on iPhone",
    location: "San Francisco, CA, USA",
    time: "April 13, 2025, 6:45 PM",
    lastActive: new Date(2025, 3, 13, 18, 45),
    isCurrent: false,
    deviceType: "mobile",
  },
  {
    id: 3,
    deviceName: "Firefox on Windows",
    location: "Portland, OR, USA",
    time: "April 12, 2025, 11:20 AM",
    lastActive: new Date(2025, 3, 12, 11, 20),
    isCurrent: false,
    deviceType: "desktop",
  },
]);

// Methods
const changePassword = async () => {
  if (!passwordsMatch.value) {
    emit("update-message", "error", "New passwords do not match.");
    return;
  }

  if (passwordStrength.value.score < 3) {
    emit("update-message", "error", "Please choose a stronger password.");
    return;
  }

  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Reset form after successful password change
    passwordForm.currentPassword = "";
    passwordForm.newPassword = "";
    passwordForm.confirmPassword = "";
    isChangingPassword.value = false;

    emit("update-message", "success", "Password changed successfully!");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem changing your password. Please verify your current password is correct."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const toggleTwoFactor = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    const status = accountSettings.twoFactorAuth ? "enabled" : "disabled";
    emit(
      "update-message",
      "success",
      `Two-factor authentication ${status} successfully.`
    );
  } catch (error) {
    // Revert the toggle if the API call fails
    accountSettings.twoFactorAuth = !accountSettings.twoFactorAuth;
    emit(
      "update-message",
      "error",
      "There was a problem updating two-factor authentication settings."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const setupTwoFactor = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    emit(
      "update-message",
      "success",
      "Two-factor authentication setup initiated. Check your email for further instructions."
    );
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem setting up two-factor authentication."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const revokeSession = async (sessionId: number) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Remove the session from the list
    activeSessions.value = activeSessions.value.filter(
      (session) => session.id !== sessionId
    );

    emit("update-message", "success", "Session revoked successfully.");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem revoking the session."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const signOutAllDevices = async () => {
  if (
    !confirm(
      "Are you sure you want to sign out of all other devices? You will remain logged in on this device."
    )
  ) {
    return;
  }

  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Remove all sessions except the current one
    activeSessions.value = activeSessions.value.filter(
      (session) => session.isCurrent
    );

    emit(
      "update-message",
      "success",
      "Signed out from all other devices successfully."
    );
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem signing out from other devices."
    );
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div>
    <!-- Section header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <h2 class="text-xl font-semibold text-gray-800">Security Settings</h2>
      <p class="text-gray-600 text-sm mt-1">
        Manage your password, two-factor authentication, and login sessions
      </p>
    </div>

    <!-- Form content -->
    <div class="p-6 space-y-8">
      <div class="max-w-3xl mx-auto">
        <!-- Password Settings -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Password</h3>
          <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <div
              v-if="!isChangingPassword"
              class="flex items-center justify-between"
            >
              <div>
                <h4 class="text-base font-medium text-gray-800">
                  Change Your Password
                </h4>
                <p class="text-sm text-gray-500 mt-1">
                  It's a good idea to use a strong password that you don't use
                  elsewhere
                </p>
              </div>
              <button
                @click="isChangingPassword = true"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Change Password
              </button>
            </div>

            <div v-else class="space-y-6">
              <div>
                <label
                  for="currentPassword"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Current Password</label
                >
                <div class="relative">
                  <input
                    id="currentPassword"
                    v-model="passwordForm.currentPassword"
                    type="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter your current password"
                  />
                </div>
              </div>

              <div>
                <label
                  for="newPassword"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >New Password</label
                >
                <div class="relative">
                  <input
                    id="newPassword"
                    v-model="passwordForm.newPassword"
                    type="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter your new password"
                  />
                </div>
              </div>

              <!-- Password strength meter -->
              <div v-if="passwordForm.newPassword">
                <div class="flex items-center mb-2">
                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div
                      class="h-2.5 rounded-full transition-all duration-300"
                      :style="{ width: `${passwordStrength.score * 20}%` }"
                      :class="`bg-${passwordStrength.color}`"
                    ></div>
                  </div>
                  <span class="ml-2 text-sm text-gray-500">{{
                    passwordStrength.label
                  }}</span>
                </div>
                <p class="text-xs text-gray-500">
                  Use at least 8 characters with a mix of uppercase, lowercase,
                  numbers, and special characters.
                </p>
              </div>

              <div>
                <label
                  for="confirmPassword"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Confirm New Password</label
                >
                <div class="relative">
                  <input
                    id="confirmPassword"
                    v-model="passwordForm.confirmPassword"
                    type="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Confirm your new password"
                  />
                </div>
                <div
                  v-if="passwordForm.confirmPassword && !passwordsMatch"
                  class="mt-1 text-xs text-red-500"
                >
                  Passwords do not match
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="button"
                  @click="isChangingPassword = false"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  @click="changePassword"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
                  :disabled="
                    isSubmitting ||
                    !passwordForm.currentPassword ||
                    !passwordForm.newPassword ||
                    !passwordForm.confirmPassword ||
                    !passwordsMatch
                  "
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
                  {{ isSubmitting ? "Updating..." : "Update Password" }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Two-Factor Authentication -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Two-Factor Authentication
          </h3>
          <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-base font-medium text-gray-800">
                  Two-Factor Authentication
                </h4>
                <p class="text-sm text-gray-500 mt-1">
                  Add an extra layer of security to your account
                </p>
              </div>
              <div class="ml-3 flex items-center">
                <label class="switch">
                  <input
                    type="checkbox"
                    v-model="accountSettings.twoFactorAuth"
                    @change="toggleTwoFactor"
                  />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div
              v-if="accountSettings.twoFactorAuth"
              class="mt-6 pt-6 border-t border-gray-200"
            >
              <p class="text-sm text-gray-600 mb-4">
                You'll need to download an authenticator app like Google
                Authenticator, Authy, or Microsoft Authenticator to complete
                setup.
              </p>
              <button
                type="button"
                @click="setupTwoFactor"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="isSubmitting"
              >
                <svg
                  v-if="isSubmitting"
                  class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-white"
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
                Set Up Two-Factor Authentication
              </button>
            </div>
          </div>
        </div>

        <!-- Login Sessions -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Login Sessions</h3>
            <button
              type="button"
              class="px-3 py-1 bg-red-50 text-red-700 text-sm font-medium rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              @click="signOutAllDevices"
              :disabled="isSubmitting || activeSessions.length <= 1"
            >
              <svg
                v-if="isSubmitting"
                class="animate-spin -ml-1 mr-1 h-3 w-3 inline-block text-red-700"
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
              Sign Out All Devices
            </button>
          </div>

          <!-- Sessions List Component -->
          <SessionsList
            :sessions="activeSessions"
            :is-submitting="isSubmitting"
            @revoke="revokeSession"
          />
        </div>
      </div>
    </div>
  </div>
</template>
