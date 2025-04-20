// composables/useModal.ts
import { ref, computed } from "vue";

/**
 * Composable for managing modal state
 *
 * @example
 * ```ts
 * const { isOpen, open, close, toggle } = useModal();
 *
 * // In your template:
 * // <Modal v-model="isOpen">...</Modal>
 * // <button @click="open">Open Modal</button>
 * ```
 *
 * @param initialState - initial state of the modal (default: false)
 * @returns Object with modal state and methods
 */
export function useModal(initialState = false) {
  // State
  const isModalOpen = ref(initialState);

  // Computed properties
  const isOpen = computed({
    get: () => isModalOpen.value,
    set: (value: boolean) => {
      isModalOpen.value = value;
    },
  });

  // Methods
  /**
   * Opens the modal
   */
  const open = () => {
    isModalOpen.value = true;
  };

  /**
   * Closes the modal
   */
  const close = () => {
    isModalOpen.value = false;
  };

  /**
   * Toggles the modal state
   */
  const toggle = () => {
    isModalOpen.value = !isModalOpen.value;
  };

  /**
   * Creates a handler function that will open the modal
   * Useful for binding to events directly
   */
  const createOpenHandler = () => {
    return () => open();
  };

  /**
   * Creates a handler function that will close the modal
   * Useful for binding to events directly
   */
  const createCloseHandler = () => {
    return () => close();
  };

  return {
    isOpen,
    open,
    close,
    toggle,
    createOpenHandler,
    createCloseHandler,
  };
}

/**
 * Composable for managing a confirmation modal
 *
 * @example
 * ```ts
 * const { confirm, isConfirming } = useConfirmModal();
 *
 * // Later in your code:
 * const handleDelete = async () => {
 *   const confirmed = await confirm('Are you sure you want to delete this item?');
 *   if (confirmed) {
 *     // Perform delete action
 *   }
 * };
 *
 * // In your template:
 * // <Modal v-model="isConfirming">...</Modal>
 * ```
 *
 * @returns Object with confirmation state and methods
 */
export function useConfirmModal() {
  // State
  const isConfirming = ref(false);
  const resolve = ref<((value: boolean) => void) | null>(null);
  const message = ref("");
  const title = ref("Confirm Action");

  /**
   * Show a confirmation modal and return a promise that resolves to
   * true if the user confirms, false otherwise
   *
   * @param confirmMessage - Message to display in the confirmation modal
   * @param confirmTitle - Title for the confirmation modal
   * @returns Promise that resolves when the user confirms or cancels
   */
  const confirm = (
    confirmMessage: string,
    confirmTitle = "Confirm Action"
  ): Promise<boolean> => {
    return new Promise<boolean>((res) => {
      message.value = confirmMessage;
      title.value = confirmTitle;
      isConfirming.value = true;
      resolve.value = res;
    });
  };

  /**
   * Handle the user confirming the action
   */
  const handleConfirm = () => {
    if (resolve.value) {
      resolve.value(true);
      resolve.value = null;
    }
    isConfirming.value = false;
  };

  /**
   * Handle the user cancelling the action
   */
  const handleCancel = () => {
    if (resolve.value) {
      resolve.value(false);
      resolve.value = null;
    }
    isConfirming.value = false;
  };

  return {
    isConfirming,
    message,
    title,
    confirm,
    handleConfirm,
    handleCancel,
  };
}

/**
 * Composable for implementing a queue of modals
 * This is useful when you need to show multiple modals in sequence
 *
 * @example
 * ```ts
 * const { currentModal, queueModal, clearModalQueue } = useModalQueue();
 *
 * // Queue some modals
 * queueModal('welcome');
 * queueModal('notification');
 *
 * // In your template:
 * // <Modal v-if="currentModal === 'welcome'" @close="nextModal">...</Modal>
 * // <Modal v-if="currentModal === 'notification'" @close="nextModal">...</Modal>
 * ```
 *
 * @returns Object with modal queue state and methods
 */
export function useModalQueue() {
  // Modal queue
  const modalQueue = ref<string[]>([]);
  const currentModal = ref<string | null>(null);

  /**
   * Add a modal to the queue and show it if nothing else is showing
   *
   * @param modalId - Identifier for the modal to queue
   */
  const queueModal = (modalId: string) => {
    // If no modal is currently showing, show this one immediately
    if (currentModal.value === null) {
      currentModal.value = modalId;
    } else {
      // Otherwise add it to the queue
      modalQueue.value.push(modalId);
    }
  };

  /**
   * Move to the next modal in the queue
   */
  const nextModal = () => {
    // If there are more modals in the queue, show the next one
    if (modalQueue.value.length > 0) {
      currentModal.value = modalQueue.value.shift() || null;
    } else {
      // Otherwise, clear the current modal
      currentModal.value = null;
    }
  };

  /**
   * Clear the modal queue and close the current modal
   */
  const clearModalQueue = () => {
    modalQueue.value = [];
    currentModal.value = null;
  };

  return {
    currentModal,
    queueModal,
    nextModal,
    clearModalQueue,
  };
}
