<!-- Example of using the Modal component -->
<script setup lang="ts">
// Page meta
definePageMeta({
  layout: "components",
});

// Basic modal state
const isBasicModalOpen = ref(false);

// Form modal state
const isFormModalOpen = ref(false);
const formData = ref({
  name: "",
  email: "",
  message: "",
});

// Confirmation modal state
const isConfirmModalOpen = ref(false);
const confirmAction = ref(() => {
  // Default action
  console.log("Confirmed action");
  isConfirmModalOpen.value = false;
});

// Side panel modal state
const isSidePanelOpen = ref(false);

// Image preview modal state
const isImageModalOpen = ref(false);
const currentImage = ref("/api/placeholder/800/600");

// Fullscreen modal state
const isFullscreenModalOpen = ref(false);

// Example functions
const openConfirmModal = (action: () => void) => {
  confirmAction.value = action;
  isConfirmModalOpen.value = true;
};

const handleDelete = () => {
  openConfirmModal(() => {
    console.log("Deleting item...");
    // Simulate API call
    setTimeout(() => {
      console.log("Item deleted");
      isConfirmModalOpen.value = false;
    }, 1000);
  });
};

const handleFormSubmit = () => {
  console.log("Form submitted:", formData.value);
  isFormModalOpen.value = false;

  // Reset form
  formData.value = {
    name: "",
    email: "",
    message: "",
  };
};
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Modal Component Examples</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
      <!-- Basic Modal Button -->
      <button
        class="p-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
        @click="isBasicModalOpen = true"
      >
        Open Basic Modal
      </button>

      <!-- Form Modal Button -->
      <button
        class="p-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
        @click="isFormModalOpen = true"
      >
        Open Form Modal
      </button>

      <!-- Confirmation Modal Button -->
      <button
        class="p-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
        @click="handleDelete"
      >
        Open Confirmation Modal
      </button>

      <!-- Side Panel Modal Button -->
      <button
        class="p-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
        @click="isSidePanelOpen = true"
      >
        Open Side Panel
      </button>

      <!-- Image Modal Button -->
      <button
        class="p-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        @click="isImageModalOpen = true"
      >
        Open Image Preview
      </button>

      <!-- Fullscreen Modal Button -->
      <button
        class="p-4 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors"
        @click="isFullscreenModalOpen = true"
      >
        Open Fullscreen Modal
      </button>
    </div>

    <!-- Basic Modal -->
    <CommonModalsModal
      v-model="isBasicModalOpen"
      title="Basic Modal"
      :has-footer="true"
    >
      <p>This is a basic modal with default configuration.</p>
      <p class="mt-2">You can close it by:</p>
      <ul class="list-disc list-inside mt-2">
        <li>Clicking the X button</li>
        <li>Clicking outside the modal</li>
        <li>Pressing the ESC key</li>
      </ul>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            @click="isBasicModalOpen = false"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            @click="isBasicModalOpen = false"
          >
            Confirm
          </button>
        </div>
      </template>
    </CommonModalsModal>

    <!-- Form Modal -->
    <CommonModalsModal
      v-model="isFormModalOpen"
      title="Contact Form"
      width="600px"
      :has-footer="true"
    >
      <form class="space-y-4" @submit.prevent="handleFormSubmit">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1"
            >Name</label
          >
          <input
            id="name"
            v-model="formData.name"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"
            required
          />
        </div>

        <div>
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Email</label
          >
          <input
            id="email"
            v-model="formData.email"
            type="email"
            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"
            required
          />
        </div>

        <div>
          <label
            for="message"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Message</label
          >
          <textarea
            id="message"
            v-model="formData.message"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"
            required
          />
        </div>
      </form>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            @click="isFormModalOpen = false"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            @click="handleFormSubmit"
          >
            Submit
          </button>
        </div>
      </template>
    </CommonModalsModal>

    <!-- Confirmation Modal -->
    <CommonModalsModal
      v-model="isConfirmModalOpen"
      :title="'Confirm Action'"
      width="400px"
      :has-footer="true"
    >
      <div class="flex items-center space-x-4">
        <div class="flex-shrink-0">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-red-500"
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
        <div>
          <h3 class="text-lg font-semibold">Are you sure?</h3>
          <p class="mt-1 text-gray-500">This action cannot be undone.</p>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            @click="isConfirmModalOpen = false"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            @click="confirmAction"
          >
            Confirm
          </button>
        </div>
      </template>
    </CommonModalsModal>

    <!-- Side Panel Modal -->
    <CommonModalsModal
      v-model="isSidePanelOpen"
      title="Side Panel"
      position="right"
      width="400px"
      height="100vh"
      :max-width="'400px'"
    >
      <div class="space-y-4">
        <h3 class="font-semibold text-lg">Side Panel Content</h3>
        <p>
          This modal is positioned on the right side of the screen and has a
          fixed width.
        </p>
        <p>
          It's perfect for side panels, drawers, or any content that should
          appear from the side.
        </p>

        <div class="pt-4">
          <h4 class="font-medium mb-2">Navigation</h4>
          <nav class="space-y-2">
            <a href="#" class="block p-2 bg-gray-100 rounded-lg">Dashboard</a>
            <a href="#" class="block p-2 bg-gray-100 rounded-lg">Profile</a>
            <a href="#" class="block p-2 bg-gray-100 rounded-lg">Settings</a>
            <a href="#" class="block p-2 bg-gray-100 rounded-lg">Help</a>
          </nav>
        </div>
      </div>
    </CommonModalsModal>

    <!-- Image Preview Modal -->
    <CommonModalsModal
      v-model="isImageModalOpen"
      title="Image Preview"
      width="auto"
      height="auto"
      :max-width="'90vw'"
      :max-height="'90vh'"
      :has-footer="true"
      content-class="overflow-hidden"
    >
      <div class="flex justify-center">
        <img
          :src="currentImage"
          alt="Preview"
          class="max-w-full h-auto max-h-[70vh] rounded-lg"
        />
      </div>

      <template #footer>
        <div class="flex justify-between">
          <div class="text-sm text-gray-500">Sample image preview</div>
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            @click="isImageModalOpen = false"
          >
            Close
          </button>
        </div>
      </template>
    </CommonModalsModal>

    <!-- Fullscreen Modal -->
    <CommonModalsModal
      v-model="isFullscreenModalOpen"
      title="Fullscreen Modal"
      :fullscreen="true"
      :has-footer="true"
    >
      <template #header>
        <div class="flex items-center space-x-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-gray-900"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"
            />
          </svg>
          <h3 class="text-lg font-semibold text-gray-900">
            Fullscreen Content
          </h3>
        </div>
      </template>

      <div class="h-full flex flex-col items-center justify-center">
        <div class="max-w-2xl text-center">
          <h2 class="text-3xl font-bold mb-4">Fullscreen Experience</h2>
          <p class="mb-6">
            This modal takes up the entire screen, perfect for immersive content
            or complex interactions that need more space.
          </p>
          <div class="bg-gray-100 p-8 rounded-lg">
            <p>You can put any content here, such as:</p>
            <ul class="list-disc list-inside mt-2 text-left">
              <li>Image galleries</li>
              <li>Data visualizations</li>
              <li>Complex forms</li>
              <li>Document viewers</li>
              <li>Code editors</li>
            </ul>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end">
          <button
            class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors"
            @click="isFullscreenModalOpen = false"
          >
            Exit Fullscreen
          </button>
        </div>
      </template>
    </CommonModalsModal>
  </div>
</template>
