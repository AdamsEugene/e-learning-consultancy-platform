<!-- components/messaging/MessageInput.vue -->
<script setup lang="ts">
import { ref, watch } from "vue";

// Emits
const emit = defineEmits<{
  (e: "sendMessage", content: string, attachments?: File[]): void;
}>();

// State
const newMessage = ref("");
const uploadedFiles = ref<File[]>([]);
const showAttachmentMenu = ref(false);
const showEmojiPicker = ref(false);
const isAnimating = ref(false);

// Send message
const sendMessage = () => {
  if (!newMessage.value.trim() && uploadedFiles.value.length === 0) return;

  emit(
    "sendMessage",
    newMessage.value.trim(),
    uploadedFiles.value.length > 0 ? uploadedFiles.value : undefined
  );

  // Clear input and files
  newMessage.value = "";
  uploadedFiles.value = [];
  showAttachmentMenu.value = false;
  showEmojiPicker.value = false;
};

// Handle file upload
const handleFileUpload = (event: Event) => {
  const fileInput = event.target as HTMLInputElement;
  if (fileInput.files && fileInput.files.length > 0) {
    for (let i = 0; i < fileInput.files.length; i++) {
      uploadedFiles.value.push(fileInput.files[i]);
    }
  }

  // Reset file input
  fileInput.value = "";

  // Hide attachment menu
  showAttachmentMenu.value = false;
};

// Remove uploaded file
const removeFile = (index: number) => {
  uploadedFiles.value.splice(index, 1);
};

// Emoji insertion (simplified - in a real app would use an emoji picker component)
const addEmoji = (emoji: string) => {
  newMessage.value += emoji;
  showEmojiPicker.value = false;
};

// Animation for new messages
watch(newMessage, (val) => {
  if (val.length > 0) {
    isAnimating.value = true;
  }
});

// Handle keyboard shortcuts
const handleKeydown = (event: KeyboardEvent) => {
  // Send message on Enter (but allow Shift+Enter for new line)
  if (event.key === "Enter" && !event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }

  // Escape closes menus
  if (event.key === "Escape") {
    showAttachmentMenu.value = false;
    showEmojiPicker.value = false;
  }
};
</script>

<template>
  <div class="p-4 bg-white message-input">
    <!-- Uploaded files preview -->
    <div
      v-if="uploadedFiles.length > 0"
      class="mb-3 flex flex-wrap gap-2 uploaded-files"
    >
      <div
        v-for="(file, index) in uploadedFiles"
        :key="index"
        class="flex items-center bg-indigo-50 text-indigo-800 px-3 py-1 rounded-full text-sm file-chip"
      >
        <span class="truncate max-w-[150px]">{{ file.name }}</span>
        <button
          @click="removeFile(index)"
          class="ml-1 text-indigo-400 hover:text-indigo-600 transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Input area with attachments -->
    <div class="flex items-end">
      <!-- Attachment button -->
      <div class="relative mr-2">
        <button
          @click="showAttachmentMenu = !showAttachmentMenu"
          class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors attachment-button"
          :class="{ 'bg-indigo-50 text-indigo-600': showAttachmentMenu }"
          aria-label="Add attachment"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
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

        <!-- Attachment menu popup -->
        <div
          v-if="showAttachmentMenu"
          class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden attachment-menu"
        >
          <ul>
            <!-- File upload -->
            <li>
              <label
                for="file-input"
                class="flex items-center px-4 py-3 hover:bg-indigo-50 cursor-pointer"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-indigo-600 mr-3"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-800">Upload file</span>
                <input
                  id="file-input"
                  type="file"
                  multiple
                  @change="handleFileUpload"
                  class="hidden"
                />
              </label>
            </li>

            <!-- Image upload -->
            <li>
              <label
                for="image-input"
                class="flex items-center px-4 py-3 hover:bg-indigo-50 cursor-pointer"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-indigo-600 mr-3"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-800">Share image</span>
                <input
                  id="image-input"
                  type="file"
                  accept="image/*"
                  @change="handleFileUpload"
                  class="hidden"
                />
              </label>
            </li>

            <!-- Document -->
            <li>
              <label
                for="document-input"
                class="flex items-center px-4 py-3 hover:bg-indigo-50 cursor-pointer"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-indigo-600 mr-3"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span class="text-gray-800">Share document</span>
                <input
                  id="document-input"
                  type="file"
                  accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                  @change="handleFileUpload"
                  class="hidden"
                />
              </label>
            </li>
          </ul>
        </div>
      </div>

      <!-- Emoji button -->
      <div class="relative mr-2">
        <button
          @click="showEmojiPicker = !showEmojiPicker"
          class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors emoji-button"
          :class="{ 'bg-indigo-50 text-indigo-600': showEmojiPicker }"
          aria-label="Add emoji"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </button>

        <!-- Simple emoji picker popup (in a real app, would use a full emoji picker component) -->
        <div
          v-if="showEmojiPicker"
          class="absolute bottom-full left-0 mb-2 p-2 bg-white rounded-lg shadow-lg border border-gray-200 emoji-picker"
        >
          <div class="grid grid-cols-5 gap-2">
            <button
              v-for="emoji in [
                '😊',
                '😂',
                '👍',
                '❤️',
                '🎉',
                '🔥',
                '👏',
                '💯',
                '🤔',
                '😍',
              ]"
              :key="emoji"
              @click="addEmoji(emoji)"
              class="w-8 h-8 text-lg hover:bg-gray-100 rounded flex items-center justify-center transition-colors"
            >
              {{ emoji }}
            </button>
          </div>
        </div>
      </div>

      <!-- Text input -->
      <div class="flex-grow relative">
        <textarea
          v-model="newMessage"
          placeholder="Type a message..."
          class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition-all"
          :class="[
            isAnimating ? 'animated-input' : '',
            'placeholder-gray-500',
            uploadedFiles.length > 0 ? 'h-12' : 'h-10',
          ]"
          rows="1"
          @keydown="handleKeydown"
        ></textarea>
      </div>

      <!-- Send button -->
      <button
        @click="sendMessage"
        class="ml-2 p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition-colors flex items-center justify-center send-button"
        :class="{
          'opacity-50 cursor-not-allowed':
            !newMessage.trim() && uploadedFiles.length === 0,
        }"
        :disabled="!newMessage.trim() && uploadedFiles.length === 0"
        aria-label="Send message"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Input animation */
.animated-input {
  animation: input-expand 0.3s ease forwards;
}

@keyframes input-expand {
  0% {
    height: 2.5rem;
  }
  100% {
    height: 3.5rem;
  }
}

/* File chip animation */
.file-chip {
  animation: file-chip-appear 0.3s ease forwards;
}

@keyframes file-chip-appear {
  0% {
    transform: translateY(10px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Menu animations */
.attachment-menu,
.emoji-picker {
  animation: menu-appear 0.2s ease forwards;
}

@keyframes menu-appear {
  0% {
    transform: translateY(5px) scale(0.95);
    opacity: 0;
  }
  100% {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}

/* Button hover effects */
.attachment-button:hover,
.emoji-button:hover,
.send-button:hover {
  transform: scale(1.1);
  transition: transform 0.2s ease;
}

.send-button:active {
  transform: scale(0.95);
}
</style>
