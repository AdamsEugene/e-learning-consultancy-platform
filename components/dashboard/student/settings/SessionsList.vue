<!-- components/dashboard/settings/SessionsList.vue -->
<script setup lang="ts">
import { ref } from "vue";
import SettingsHeader from "./SettingsHeader.vue";

interface Session {
  id: string;
  device: string;
  browser: string;
  location: string;
  ip: string;
  lastActive: string;
  isCurrent: boolean;
}

interface Props {
  isSubmitting?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isSubmitting: false,
});

const emit = defineEmits<{
  (e: "revoke", id: string): void;
  (e: "revoke-all"): void;
  (e: "update-message", type: string, message: string): void;
}>();

// Mock sessions data
const sessions = ref<Session[]>([
  {
    id: "current-session",
    device: "macOS",
    browser: "Chrome",
    location: "San Francisco, CA, USA",
    ip: "192.168.1.1",
    lastActive: "Now",
    isCurrent: true,
  },
  {
    id: "session-1",
    device: "iPhone",
    browser: "Safari",
    location: "San Francisco, CA, USA",
    ip: "192.168.1.2",
    lastActive: "April 13, 2025, 6:45 PM",
    isCurrent: false,
  },
  {
    id: "session-2",
    device: "Windows",
    browser: "Firefox",
    location: "Portland, OR, USA",
    ip: "192.168.1.3",
    lastActive: "April 12, 2025, 11:20 AM",
    isCurrent: false,
  },
]);

const revokeSession = async (id: string) => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Remove the session
    sessions.value = sessions.value.filter((session) => session.id !== id);

    emit("update-message", "success", "Session revoked successfully.");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem revoking the session."
    );
  }
};

const revokeAllSessions = async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Keep only current session
    sessions.value = sessions.value.filter((session) => session.isCurrent);

    emit("update-message", "success", "All other sessions have been revoked.");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem revoking the sessions."
    );
  }
};
</script>

<template>
  <div>
    <!-- Section header -->
    <SettingsHeader
      title="Active Sessions"
      description="Manage your active login sessions across devices"
    />

    <!-- Form content -->
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Login Sessions</h3>
          <button
            v-if="sessions.filter((s) => !s.isCurrent).length > 0"
            type="button"
            @click="revokeAllSessions"
            :disabled="isSubmitting"
            class="px-3 py-1 bg-red-50 text-red-700 text-sm font-medium rounded-md hover:bg-red-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Sign Out All Devices
          </button>
        </div>
        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
          <div class="space-y-4">
            <!-- Current session -->
            <div
              v-for="session in sessions"
              :key="session.id"
              class="flex items-center justify-between p-4 rounded-lg border"
              :class="
                session.isCurrent
                  ? 'bg-green-50 border-green-100'
                  : 'border-gray-200 bg-white'
              "
            >
              <div class="flex items-center">
                <svg
                  v-if="session.isCurrent"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-green-600 mr-3"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-3"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"
                  />
                </svg>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">
                    {{ session.browser }} on {{ session.device }}
                    <span
                      v-if="session.isCurrent"
                      class="ml-2 text-xs font-medium text-green-700 bg-green-100 px-2 py-0.5 rounded-full"
                    >
                      Active Now
                    </span>
                  </h4>
                  <div
                    class="mt-1 flex items-center text-xs text-gray-500 space-x-2"
                  >
                    <span>{{ session.location }}</span>
                    <span
                      class="inline-block w-1 h-1 rounded-full bg-gray-400"
                    ></span>
                    <span>{{ session.lastActive }}</span>
                    <span
                      class="inline-block w-1 h-1 rounded-full bg-gray-400"
                    ></span>
                    <span>IP: {{ session.ip }}</span>
                  </div>
                </div>
              </div>
              <button
                v-if="!session.isCurrent"
                type="button"
                @click="revokeSession(session.id)"
                :disabled="isSubmitting"
                class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors"
              >
                Revoke
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation for status updates */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  animation: fadeIn 0.3s ease-out;
}
</style>
