<!-- components/common/ConfirmationModal.vue -->
<script setup lang="ts">
import { computed } from "vue";

/**
 * Props for the ConfirmationModal component
 */
interface Props {
  /** Controls if the modal is visible */
  modelValue?: boolean;
  /** Title for the confirmation dialog */
  title?: string;
  /** Message or question to display */
  message?: string;
  /** Text for the confirm button */
  confirmText?: string;
  /** Text for the cancel button */
  cancelText?: string;
  /** Type/variant of the confirmation (affects colors) */
  type?: "info" | "success" | "warning" | "danger" | "default";
  /** Whether to show a cancel button */
  showCancel?: boolean;
  /** Width of the modal */
  width?: string;
  /** Whether to auto-focus the confirm button */
  autoFocusConfirm?: boolean;
  /** Whether to close modal on Escape key press */
  closeOnEsc?: boolean;
  /** Whether to close modal when clicking outside */
  closeOnClickOutside?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: false,
  title: "Confirm Action",
  message: "Are you sure you want to proceed?",
  confirmText: "Confirm",
  cancelText: "Cancel",
  type: "default",
  showCancel: true,
  width: "400px",
  autoFocusConfirm: false,
  closeOnEsc: true,
  closeOnClickOutside: true,
});

/**
 * Emitted events
 */
const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "confirm" | "cancel"): void;
}>();

// Close the modal
const close = () => {
  emit("update:modelValue", false);
};

// Handle confirm action
const handleConfirm = () => {
  emit("confirm");
  close();
};

// Handle cancel action
const handleCancel = () => {
  emit("cancel");
  close();
};

// Computed styles based on type
const typeStyles = computed(() => {
  switch (props.type) {
    case "success":
      return {
        icon: "check-circle",
        iconColor: "text-green-500",
        confirmClass: "bg-green-600 hover:bg-green-700 focus:ring-green-500",
      };
    case "warning":
      return {
        icon: "exclamation-triangle",
        iconColor: "text-yellow-500",
        confirmClass: "bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500",
      };
    case "danger":
      return {
        icon: "exclamation",
        iconColor: "text-red-500",
        confirmClass: "bg-red-600 hover:bg-red-700 focus:ring-red-500",
      };
    case "info":
      return {
        icon: "information-circle",
        iconColor: "text-blue-500",
        confirmClass: "bg-blue-600 hover:bg-blue-700 focus:ring-blue-500",
      };
    default:
      return {
        icon: "question-mark-circle",
        iconColor: "text-indigo-500",
        confirmClass: "bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500",
      };
  }
});
</script>

<template>
  <CommonModalsModal
    :model-value="modelValue"
    :title="title"
    :width="width"
    :close-on-esc="closeOnEsc"
    :close-on-click-outside="closeOnClickOutside"
    :has-footer="true"
    @update:model-value="(value) => emit('update:modelValue', value)"
  >
    <div class="flex items-start space-x-4">
      <!-- Icon based on type -->
      <div class="flex-shrink-0">
        <!-- Success Icon -->
        <svg
          v-if="typeStyles.icon === 'check-circle'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12"
          :class="typeStyles.iconColor"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>

        <!-- Warning Icon -->
        <svg
          v-else-if="typeStyles.icon === 'exclamation-triangle'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12"
          :class="typeStyles.iconColor"
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

        <!-- Danger Icon -->
        <svg
          v-else-if="typeStyles.icon === 'exclamation'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12"
          :class="typeStyles.iconColor"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>

        <!-- Info Icon -->
        <svg
          v-else-if="typeStyles.icon === 'information-circle'"
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12"
          :class="typeStyles.iconColor"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>

        <!-- Question Icon (Default) -->
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12"
          :class="typeStyles.iconColor"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
      </div>

      <!-- Message content -->
      <div>
        <p class="text-gray-700">{{ message }}</p>

        <!-- Additional content through slot -->
        <slot />
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end space-x-3">
        <button
          v-if="showCancel"
          class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
          @click="handleCancel"
        >
          {{ cancelText }}
        </button>

        <button
          :ref="(el) => { if (autoFocusConfirm && el) { (el as HTMLElement).focus(); } }"
          :class="[
            'px-4 py-2 text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
            typeStyles.confirmClass,
          ]"
          @click="handleConfirm"
        >
          {{ confirmText }}
        </button>
      </div>
    </template>
  </CommonModalsModal>
</template>
