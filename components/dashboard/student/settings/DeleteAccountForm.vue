<!-- components/dashboard/settings/DeleteAccountForm.vue -->
<script setup lang="ts">
import { ref } from "vue";

interface Props {
  isSubmitting: boolean;
}

defineProps<Props>();

const emit = defineEmits<{
  (e: "delete", reason: string): void;
}>();

const showConfirmation = ref(false);
const reason = ref("");
const confirmText = ref("");
const confirmationRequired = "delete my account";

const reasons = [
  { value: "not-useful", label: "I don't find the platform useful" },
  { value: "too-expensive", label: "The subscription is too expensive" },
  { value: "found-alternative", label: "I found an alternative service" },
  { value: "technical-issues", label: "I'm experiencing technical issues" },
  { value: "privacy-concerns", label: "I have privacy concerns" },
  { value: "temporary", label: "I'm taking a break but will return later" },
  { value: "other", label: "Other" },
];

const canProceed = computed(() => {
  return (
    reason.value &&
    (!showConfirmation.value ||
      confirmText.value.toLowerCase() === confirmationRequired)
  );
});

const handleInitiateDelete = () => {
  showConfirmation.value = true;
};

const handleConfirmDelete = () => {
  if (canProceed.value) {
    emit("delete", reason.value);
  }
};

const handleCancel = () => {
  showConfirmation.value = false;
  confirmText.value = "";
};
</script>

<template>
  <div class="bg-red-50 rounded-lg p-6 border border-red-200">
    <div v-if="!showConfirmation">
      <div class="mb-6">
        <h4 class="text-base font-medium text-red-800">
          Permanently Delete Your Account
        </h4>
        <p class="text-sm text-red-700 mt-1">
          Once you delete your account, there is no going back. Please be
          certain.
        </p>
      </div>

      <div class="mb-6">
        <label
          for="delete-reason"
          class="block text-sm font-medium text-gray-700 mb-1"
        >
          Please tell us why you want to delete your account
        </label>
        <select
          id="delete-reason"
          v-model="reason"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
        >
          <option value="">Select a reason</option>
          <option
            v-for="option in reasons"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>
      </div>

      <div class="flex justify-end">
        <button
          type="button"
          @click="handleInitiateDelete"
          class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
          :disabled="!reason"
        >
          Continue to Account Deletion
        </button>
      </div>
    </div>

    <div v-else>
      <div class="mb-6">
        <div class="flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-red-600 mr-2"
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
          <h4 class="text-base font-medium text-red-800">
            Confirm Account Deletion
          </h4>
        </div>
        <div class="mt-2 p-4 bg-red-100 rounded-md">
          <p class="text-sm text-red-800 font-medium">
            This action cannot be undone. This will:
          </p>
          <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
            <li>Permanently delete your personal account</li>
            <li>Remove all your courses, progress, and certificates</li>
            <li>Cancel any active subscriptions</li>
            <li>Delete all your data and content</li>
          </ul>
        </div>
      </div>

      <div class="mb-6">
        <label
          for="confirm-text"
          class="block text-sm font-medium text-gray-700 mb-1"
        >
          Please type
          <span class="font-mono bg-gray-100 px-1">{{
            confirmationRequired
          }}</span>
          to confirm
        </label>
        <input
          id="confirm-text"
          type="text"
          v-model="confirmText"
          autocomplete="off"
          spellcheck="false"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
          placeholder="Type the confirmation text"
        />
      </div>

      <div class="flex justify-end space-x-3">
        <button
          type="button"
          @click="handleCancel"
          class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
        >
          Cancel
        </button>
        <button
          type="button"
          @click="handleConfirmDelete"
          class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center transition-colors"
          :disabled="!canProceed || isSubmitting"
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
          {{ isSubmitting ? "Processing..." : "Delete Account Permanently" }}
        </button>
      </div>
    </div>
  </div>
</template>
