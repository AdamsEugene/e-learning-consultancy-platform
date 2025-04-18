<script setup lang="ts">
import { ref, onMounted, watch } from "vue";

interface Message {
  id: string;
  content: string;
  senderId: string;
  timestamp: Date;
  status: "sending" | "sent" | "delivered" | "read";
  type: "text" | "image" | "file";
  fileUrl?: string;
  fileName?: string;
  fileSize?: number;
}

const props = defineProps<{
  messages: Message[];
  currentUserId: string;
}>();

const messagesContainerRef = ref<HTMLElement | null>(null);

const scrollToBottom = () => {
  if (messagesContainerRef.value) {
    messagesContainerRef.value.scrollTop =
      messagesContainerRef.value.scrollHeight;
  }
};

// Watch for new messages and scroll to bottom
watch(
  () => props.messages,
  () => {
    setTimeout(scrollToBottom, 100);
  },
  { deep: true }
);

onMounted(() => {
  scrollToBottom();
});

const formatTime = (date: Date) => {
  return new Date(date).toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
  });
};

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};
</script>

<template>
  <div ref="messagesContainerRef" class="flex-1 overflow-y-auto p-4 space-y-4">
    <template v-for="message in messages" :key="message.id">
      <!-- Message Container -->
      <div
        class="flex"
        :class="
          message.senderId === currentUserId ? 'justify-end' : 'justify-start'
        "
      >
        <!-- Message Bubble -->
        <div
          class="max-w-[70%] rounded-lg px-4 py-2"
          :class="[
            message.senderId === currentUserId
              ? 'bg-indigo-600 text-white rounded-br-none'
              : 'bg-gray-100 text-gray-900 rounded-bl-none',
          ]"
        >
          <!-- Text Message -->
          <template v-if="message.type === 'text'">
            <p class="text-sm whitespace-pre-wrap">{{ message.content }}</p>
          </template>

          <!-- Image Message -->
          <template v-else-if="message.type === 'image'">
            <img
              :src="message.fileUrl"
              :alt="message.fileName"
              class="max-w-full rounded-lg"
            />
          </template>

          <!-- File Message -->
          <template v-else-if="message.type === 'file'">
            <div class="flex items-center space-x-2">
              <svg
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                />
              </svg>
              <div class="flex-1 min-w-0">
                <p class="text-sm truncate">{{ message.fileName }}</p>
                <p class="text-xs opacity-75">
                  {{ formatFileSize(message.fileSize || 0) }}
                </p>
              </div>
              <a
                :href="message.fileUrl"
                download
                class="p-1 hover:bg-black/10 rounded-full transition-colors"
              >
                <svg
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                  />
                </svg>
              </a>
            </div>
          </template>

          <!-- Message Status -->
          <div class="flex items-center justify-end space-x-1 mt-1">
            <span class="text-xs opacity-75">{{
              formatTime(message.timestamp)
            }}</span>
            <template v-if="message.senderId === currentUserId">
              <svg
                v-if="message.status === 'sending'"
                class="h-4 w-4 animate-spin"
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
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
              </svg>
              <svg
                v-else-if="message.status === 'sent'"
                class="h-4 w-4"
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
                v-else-if="message.status === 'delivered'"
                class="h-4 w-4"
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
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
              <svg
                v-else-if="message.status === 'read'"
                class="h-4 w-4 text-blue-400"
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
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
            </template>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
