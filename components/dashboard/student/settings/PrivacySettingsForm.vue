<!-- components/dashboard/settings/PrivacySettingsForm.vue -->
<script setup lang="ts">
import { ref, reactive } from "vue";
import SettingsHeader from "./SettingsHeader.vue";

const emit = defineEmits<{
  (e: "update-message", type: string, message: string): void;
}>();

// Privacy settings state
const privacySettings = reactive({
  profileVisibility: "registered", // public, registered, private
  showEnrolledCourses: true,
  showCompletedCourses: true,
  showAchievements: true,
  shareProgress: true,
  allowMessaging: true,
  allowTagging: true,
  dataCollection: true,
  cookiePreferences: "essential", // all, essential, minimum
});

// Form state
const isSubmitting = ref(false);

// Save privacy settings
const saveSettings = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    emit("update-message", "success", "Privacy settings updated successfully!");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem updating your privacy settings."
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
      title="Privacy Settings"
      description="Control your privacy and data preferences"
    />

    <!-- Form content -->
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <!-- Profile Visibility -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Profile Visibility
          </h3>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"
                  >Who can see your profile?</label
                >
                <select
                  v-model="privacySettings.profileVisibility"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="public">Everyone (Public)</option>
                  <option value="registered">Registered Users Only</option>
                  <option value="private">Only Me (Private)</option>
                </select>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-3">
                  Control what information is visible on your profile
                </p>
                <div class="space-y-3">
                  <div class="flex items-center">
                    <input
                      id="showEnrolledCourses"
                      type="checkbox"
                      v-model="privacySettings.showEnrolledCourses"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label
                      for="showEnrolledCourses"
                      class="ml-3 block text-sm text-gray-700"
                    >
                      Show enrolled courses on my profile
                    </label>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="showCompletedCourses"
                      type="checkbox"
                      v-model="privacySettings.showCompletedCourses"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label
                      for="showCompletedCourses"
                      class="ml-3 block text-sm text-gray-700"
                    >
                      Show completed courses and certificates
                    </label>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="showAchievements"
                      type="checkbox"
                      v-model="privacySettings.showAchievements"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label
                      for="showAchievements"
                      class="ml-3 block text-sm text-gray-700"
                    >
                      Show badges and achievements
                    </label>
                  </div>

                  <div class="flex items-center">
                    <input
                      id="shareProgress"
                      type="checkbox"
                      v-model="privacySettings.shareProgress"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label
                      for="shareProgress"
                      class="ml-3 block text-sm text-gray-700"
                    >
                      Share my learning progress with classmates
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Communication Settings -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Communication Settings
          </h3>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="space-y-4">
              <div class="flex items-center">
                <input
                  id="allowMessaging"
                  type="checkbox"
                  v-model="privacySettings.allowMessaging"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="allowMessaging" class="ml-3">
                  <span class="block text-sm font-medium text-gray-700"
                    >Allow direct messages</span
                  >
                  <span class="block text-sm text-gray-500"
                    >Let other students and instructors send you messages</span
                  >
                </label>
              </div>

              <div class="flex items-center">
                <input
                  id="allowTagging"
                  type="checkbox"
                  v-model="privacySettings.allowTagging"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="allowTagging" class="ml-3">
                  <span class="block text-sm font-medium text-gray-700"
                    >Allow tagging in posts</span
                  >
                  <span class="block text-sm text-gray-500"
                    >Let others tag you in their forum posts and comments</span
                  >
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Data and Privacy -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Data Collection & Privacy
          </h3>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="space-y-4">
              <div class="flex items-center">
                <input
                  id="dataCollection"
                  type="checkbox"
                  v-model="privacySettings.dataCollection"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="dataCollection" class="ml-3">
                  <span class="block text-sm font-medium text-gray-700"
                    >Learning data collection</span
                  >
                  <span class="block text-sm text-gray-500"
                    >Allow us to collect data about your learning patterns to
                    improve your experience (recommended)</span
                  >
                </label>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                  >Cookie Preferences</label
                >
                <select
                  v-model="privacySettings.cookiePreferences"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="all">Accept All Cookies</option>
                  <option value="essential">Essential Cookies Only</option>
                  <option value="minimum">
                    Minimum Cookies (Functionality Limited)
                  </option>
                </select>
                <p class="mt-2 text-sm text-gray-500">
                  Manage which cookies you want to allow.
                  <a href="#" class="text-indigo-600 hover:text-indigo-800"
                    >Learn more</a
                  >
                  about cookies and how we use them.
                </p>
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
            {{ isSubmitting ? "Saving..." : "Save Settings" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
