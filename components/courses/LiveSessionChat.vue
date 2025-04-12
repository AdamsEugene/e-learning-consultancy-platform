<!-- components/courses/LiveSessionChat.vue -->
<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from "vue";

interface ChatMessage {
  id: number;
  userId: number;
  userName: string;
  userAvatar: string;
  message: string;
  timestamp: number;
  isInstructor: boolean;
}

const props = defineProps<{
  messages: ChatMessage[];
  connected: boolean;
}>();

const emit = defineEmits<{
  (e: "send-message", message: string): void;
}>();

const newMessage = ref("");
const chatContainer = ref<HTMLElement | null>(null);

// Send a new message
const sendMessage = () => {
  if (!newMessage.value.trim() || !props.connected) return;

  emit("send-message", newMessage.value.trim());
  newMessage.value = "";
};

// Format timestamp to readable time format
const formatTime = (timestamp: number): string => {
  return new Date(timestamp).toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Auto-scroll to the latest message when new messages are added
watch(
  () => props.messages.length,
  async () => {
    await nextTick();
    scrollToBottom();
  }
);

// Scroll to the bottom of the chat container
const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  }
};

// Focus on the input field when component is mounted
onMounted(() => {
  scrollToBottom();
});
</script>

<template>
  <div class="h-full flex flex-col">
    <!-- Chat messages -->
    <div ref="chatContainer" class="flex-grow overflow-y-auto p-4 space-y-4">
      <div
        v-for="message in messages"
        :key="message.id"
        :class="['flex', message.userId === 0 ? 'justify-center' : '']"
      >
        <!-- System message -->
        <div
          v-if="message.userId === 0"
          class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-sm inline-block"
        >
          {{ message.message }}
        </div>

        <!-- User message -->
        <div v-else class="flex max-w-xs sm:max-w-md">
          <img
            :src="message.userAvatar"
            class="h-8 w-8 rounded-full mr-2"
            :alt="message.userName"
          >
          <div>
            <div class="flex items-center">
              <span class="font-medium text-sm">{{ message.userName }}</span>
              <span
                v-if="message.isInstructor"
                class="ml-1 text-xs bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded-full"
              >
                Instructor
              </span>
            </div>
            <div class="bg-white rounded-lg p-2 mt-1 shadow-sm">
              <p class="text-gray-800">{{ message.message }}</p>
            </div>
            <div class="text-xs text-gray-500 mt-1">
              {{ formatTime(message.timestamp) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="messages.length === 0" class="text-center py-8">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12 text-gray-300 mx-auto mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
          />
        </svg>
        <p class="text-gray-500">No messages yet. Start the conversation!</p>
      </div>
    </div>

    <!-- Chat input -->
    <div class="p-4 bg-white border-t border-gray-200">
      <form class="flex" @submit.prevent="sendMessage">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Type your message..."
          :disabled="!connected"
          class="flex-grow rounded-l-lg border-l border-t border-b border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 disabled:bg-gray-100 disabled:text-gray-400"
        >
        <button
          type="submit"
          :disabled="!newMessage.trim() || !connected"
          class="bg-indigo-600 text-white rounded-r-lg px-4 py-2 disabled:bg-indigo-300"
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
      </form>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9; /* gray-100 */
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1; /* gray-300 */
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1; /* indigo-500 */
}
</style>
