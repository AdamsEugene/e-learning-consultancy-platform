<!-- components/dashboard/settings/NotificationSettingsForm.vue -->
<script setup lang="ts">
import { ref, reactive } from "vue";
import SettingsHeader from "./SettingsHeader.vue";

const emit = defineEmits<{
  (e: "update-message", type: string, message: string): void;
}>();

// Notification settings state
const notificationSettings = reactive({
  emailNotifications: true,
  courseUpdates: true,
  upcomingClasses: true,
  assignmentReminders: true,
  dueDateReminders: true,
  promotions: false,
  newsletter: true,
  newMessages: true,
  newReplies: true,
  studyGroupUpdates: true,
  mobileNotifications: true,
  pushEnabled: true,
});

// Form state
const isSubmitting = ref(false);

// Methods
const saveSettings = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    emit("update-message", "success", "Notification preferences saved!");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem saving your notification preferences."
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
      title="Notification Settings"
      description="Manage how and when you receive notifications"
    />

    <!-- Form content -->
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <!-- Email Notifications -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Email Notifications
          </h3>
          <div
            class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">
                  Email Notifications
                </h4>
                <p class="text-sm text-gray-500">
                  Receive email notifications for important updates
                </p>
              </div>
              <div class="ml-3 flex items-center">
                <label class="switch">
                  <input
                    type="checkbox"
                    v-model="notificationSettings.emailNotifications"
                  />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
              <div class="space-y-4">
                <div class="flex items-center">
                  <input
                    id="courseUpdates"
                    type="checkbox"
                    v-model="notificationSettings.courseUpdates"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="courseUpdates"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Course content updates
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="upcomingClasses"
                    type="checkbox"
                    v-model="notificationSettings.upcomingClasses"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="upcomingClasses"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Upcoming live classes
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="assignmentReminders"
                    type="checkbox"
                    v-model="notificationSettings.assignmentReminders"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="assignmentReminders"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Assignment reminders
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="dueDateReminders"
                    type="checkbox"
                    v-model="notificationSettings.dueDateReminders"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="dueDateReminders"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Due date reminders
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="newMessages"
                    type="checkbox"
                    v-model="notificationSettings.newMessages"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="newMessages"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    New messages
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="newReplies"
                    type="checkbox"
                    v-model="notificationSettings.newReplies"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="newReplies"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Replies to your posts
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    id="studyGroupUpdates"
                    type="checkbox"
                    v-model="notificationSettings.studyGroupUpdates"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.emailNotifications"
                  />
                  <label
                    for="studyGroupUpdates"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.emailNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Study group updates
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Marketing Communications -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Marketing Communications
          </h3>
          <div
            class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-4"
          >
            <div class="flex items-center">
              <input
                id="promotions"
                type="checkbox"
                v-model="notificationSettings.promotions"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="promotions" class="ml-3">
                <span class="block text-sm font-medium text-gray-700"
                  >Promotions and Discounts</span
                >
                <span class="block text-sm text-gray-500"
                  >Receive notifications about special offers and
                  discounts</span
                >
              </label>
            </div>

            <div class="flex items-center">
              <input
                id="newsletter"
                type="checkbox"
                v-model="notificationSettings.newsletter"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="newsletter" class="ml-3">
                <span class="block text-sm font-medium text-gray-700"
                  >Educational Newsletter</span
                >
                <span class="block text-sm text-gray-500"
                  >Monthly updates with educational content and learning
                  tips</span
                >
              </label>
            </div>
          </div>
        </div>

        <!-- Mobile Notifications -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Mobile Notifications
          </h3>
          <div
            class="bg-gray-50 rounded-lg p-4 border border-gray-200 space-y-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">
                  Push Notifications
                </h4>
                <p class="text-sm text-gray-500">
                  Get push notifications on your mobile device
                </p>
              </div>
              <div class="ml-3 flex items-center">
                <label class="switch">
                  <input
                    type="checkbox"
                    v-model="notificationSettings.mobileNotifications"
                  />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
              <p class="text-sm text-gray-500 mb-3">
                Control which notifications you receive on your mobile device
                when the app is installed.
              </p>
              <div class="space-y-2">
                <div class="flex items-center">
                  <input
                    id="mobilePushEnabled"
                    type="checkbox"
                    v-model="notificationSettings.pushEnabled"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    :disabled="!notificationSettings.mobileNotifications"
                  />
                  <label
                    for="mobilePushEnabled"
                    class="ml-3 block text-sm"
                    :class="
                      notificationSettings.mobileNotifications
                        ? 'text-gray-700'
                        : 'text-gray-400'
                    "
                  >
                    Enable push notifications
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action buttons -->
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
            {{ isSubmitting ? "Saving..." : "Save Preferences" }}
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
