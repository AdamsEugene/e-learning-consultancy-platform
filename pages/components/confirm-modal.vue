<!-- components/examples/ConfirmModalExample.vue -->
<script setup lang="ts">
import { ref } from "vue";
import { useConfirmModal } from "../../composables/useModal";

// Page meta
definePageMeta({
  layout: "components",
});

// Using the confirmation modal composable
const { isConfirming, message, title, confirm, handleConfirm, handleCancel } =
  useConfirmModal();

// Sample data
const items = ref([
  { id: 1, name: "Project A", status: "Active" },
  { id: 2, name: "Project B", status: "Pending" },
  { id: 3, name: "Project C", status: "Completed" },
  { id: 4, name: "Project D", status: "Active" },
  { id: 5, name: "Project E", status: "Archived" },
]);

// Loading state
const isLoading = ref(false);
const actionResult = ref("");
const actionSuccess = ref(false);

// Delete an item with confirmation
const deleteItem = async (id: number) => {
  const itemToDelete = items.value.find((item) => item.id === id);

  if (!itemToDelete) return;

  const confirmed = await confirm(
    `Are you sure you want to delete "${itemToDelete.name}"?`,
    "Confirm Deletion"
  );

  if (confirmed) {
    // Simulate API call
    isLoading.value = true;
    actionResult.value = "";

    setTimeout(() => {
      // Remove the item from the list
      items.value = items.value.filter((item) => item.id !== id);

      isLoading.value = false;
      actionResult.value = `Deleted "${itemToDelete.name}" successfully`;
      actionSuccess.value = true;
    }, 1000);
  }
};

// Archive an item with confirmation
const archiveItem = async (id: number) => {
  const itemToArchive = items.value.find((item) => item.id === id);

  if (!itemToArchive) return;

  const confirmed = await confirm(
    `Are you sure you want to archive "${itemToArchive.name}"?`,
    "Confirm Archive Action"
  );

  if (confirmed) {
    // Simulate API call
    isLoading.value = true;
    actionResult.value = "";

    setTimeout(() => {
      // Update the item's status
      const index = items.value.findIndex((item) => item.id === id);
      items.value[index].status = "Archived";

      isLoading.value = false;
      actionResult.value = `Archived "${itemToArchive.name}" successfully`;
      actionSuccess.value = true;
    }, 1000);
  }
};

// Reset an item with confirmation
const resetItem = async (id: number) => {
  const itemToReset = items.value.find((item) => item.id === id);

  if (!itemToReset) return;

  const confirmed = await confirm(
    `Do you want to reset "${itemToReset.name}" to Active status?`,
    "Confirm Reset"
  );

  if (confirmed) {
    // Simulate API call
    isLoading.value = true;
    actionResult.value = "";

    setTimeout(() => {
      // Update the item's status
      const index = items.value.findIndex((item) => item.id === id);
      items.value[index].status = "Active";

      isLoading.value = false;
      actionResult.value = `Reset "${itemToReset.name}" to Active status`;
      actionSuccess.value = true;
    }, 1000);
  }
};

// Clear result message
const clearResult = () => {
  actionResult.value = "";
};
</script>

<template>
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Confirmation Modal Example</h1>

    <!-- Action result message -->
    <div
      v-if="actionResult"
      :class="[
        'p-4 rounded-lg mb-6 flex items-center justify-between',
        actionSuccess
          ? 'bg-green-100 text-green-800'
          : 'bg-red-100 text-red-800',
      ]"
    >
      <div class="flex items-center">
        <svg
          v-if="actionSuccess"
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M5 13l4 4L19 7"
          />
        </svg>
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
        {{ actionResult }}
      </div>
      <button
        class="focus:outline-none"
        aria-label="Close"
        @click="clearResult"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>

    <!-- Items table -->
    <div
      class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200"
    >
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              ID
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Name
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ item.id }}
            </td>
            <td
              class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
            >
              {{ item.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="{
                  'bg-green-100 text-green-800': item.status === 'Active',
                  'bg-yellow-100 text-yellow-800': item.status === 'Pending',
                  'bg-blue-100 text-blue-800': item.status === 'Completed',
                  'bg-gray-100 text-gray-800': item.status === 'Archived',
                }"
              >
                {{ item.status }}
              </span>
            </td>
            <td
              class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
            >
              <!-- Action buttons based on status -->
              <template v-if="item.status !== 'Archived'">
                <button
                  class="text-indigo-600 hover:text-indigo-900"
                  :disabled="isLoading"
                  @click="archiveItem(item.id)"
                >
                  Archive
                </button>
              </template>

              <template v-if="item.status === 'Archived'">
                <button
                  class="text-green-600 hover:text-green-900"
                  :disabled="isLoading"
                  @click="resetItem(item.id)"
                >
                  Reset
                </button>
              </template>

              <button
                class="text-red-600 hover:text-red-900"
                :disabled="isLoading"
                @click="deleteItem(item.id)"
              >
                Delete
              </button>
            </td>
          </tr>

          <!-- Empty state -->
          <tr v-if="items.length === 0">
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
              No items found. All items have been deleted.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Loading overlay -->
    <div
      v-if="isLoading"
      class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50"
    >
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="flex items-center space-x-3">
          <div
            class="h-5 w-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"
          />
          <p class="text-gray-700">Processing...</p>
        </div>
      </div>
    </div>

    <!-- Confirmation modal using useConfirmModal composable -->
    <CommonModalsConfirmationModal
      v-model="isConfirming"
      :title="title"
      :message="message"
      type="danger"
      @confirm="handleConfirm"
      @cancel="handleCancel"
    />
  </div>
</template>
