<script setup lang="ts">
import { ref } from "vue";

const props = defineProps<{
  onSendMessage: (message: string) => void;
  onSendFile?: (file: File) => void;
}>();

const message = ref("");
const fileInput = ref<HTMLInputElement | null>(null);

const handleSubmit = () => {
  if (message.value.trim()) {
    props.onSendMessage(message.value);
    message.value = "";
  }
};

const handleKeyPress = (event: KeyboardEvent) => {
  if (event.key === "Enter" && !event.shiftKey) {
    event.preventDefault();
    handleSubmit();
  }
};

const handleFileSelect = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files.length > 0 && props.onSendFile) {
    props.onSendFile(input.files[0]);
  }
};
</script>

<template>
  <div class="border-t p-4">
    <form @submit.prevent="handleSubmit" class="flex items-end space-x-4">
      <!-- File Upload Button -->
      <button
        type="button"
        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors"
        @click="fileInput?.click()"
      >
        <svg
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
          />
        </svg>
      </button>

      <!-- Hidden File Input -->
      <input
        ref="fileInput"
        type="file"
        class="hidden"
        @change="handleFileSelect"
      />

      <!-- Message Input -->
      <div class="flex-1">
        <textarea
          v-model="message"
          rows="1"
          class="w-full resize-none rounded-lg border border-gray-300 p-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
          placeholder="Type a message..."
          @keypress="handleKeyPress"
        ></textarea>
      </div>

      <!-- Send Button -->
      <button
        type="submit"
        class="rounded-full bg-indigo-600 p-3 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        :disabled="!message.trim()"
      >
        <svg
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
          />
        </svg>
      </button>
    </form>
  </div>
</template>
