<!-- components/messaging/MessageBubble.vue -->
<script setup lang="ts">
import { computed } from "vue";
import type { User, Conversation, Message } from "~/types/messaging";

interface Props {
  message: Message;
  currentUser: User;
  conversation: Conversation;
  isSelectionMode: boolean;
  isSelected: boolean;
  isMessageSent: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "toggleSelection", messageId: number): void;
}>();

// Check if the message is from the current user
const isFromCurrentUser = computed(() => {
  return props.message.senderId === props.currentUser.id;
});

// Get sender (for showing name in group chats)
const sender = computed(() => {
  if (isFromCurrentUser.value) {
    return props.currentUser;
  }

  return props.conversation.participants.find(
    (p) => p.id === props.message.senderId
  );
});

// Format timestamp for message bubbles
const formattedTime = computed((): string => {
  const date = new Date(props.message.timestamp);
  const now = new Date();
  const diffDays = Math.floor(
    (now.getTime() - date.getTime()) / (1000 * 60 * 60 * 24)
  );

  if (diffDays === 0) {
    // Today - show time only
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  } else if (diffDays === 1) {
    // Yesterday
    return `Yesterday at ${date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })}`;
  } else if (diffDays < 7) {
    // This week - show day name and time
    return `${date.toLocaleDateString([], {
      weekday: "long",
    })} at ${date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })}`;
  } else {
    // Older - show full date and time
    return date.toLocaleDateString([], {
      month: "short",
      day: "numeric",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }
});

// Format file size
const formatFileSize = (bytes?: number): string => {
  if (!bytes) return "";

  if (bytes < 1024) {
    return bytes + " B";
  } else if (bytes < 1024 * 1024) {
    return (bytes / 1024).toFixed(1) + " KB";
  } else if (bytes < 1024 * 1024 * 1024) {
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
  } else {
    return (bytes / (1024 * 1024 * 1024)).toFixed(1) + " GB";
  }
};

// Color for sender name in group chat
const senderColor = computed(() => {
  if (!props.conversation.isGroup || isFromCurrentUser.value) return "";

  // Simple color assignment based on user ID
  const colors = [
    "text-indigo-600",
    "text-purple-600",
    "text-green-600",
    "text-blue-600",
    "text-red-600",
    "text-yellow-600",
  ];
  const colorIndex = props.message.senderId % colors.length;
  return colors[colorIndex];
});
</script>

<template>
  <div class="group">
    <!-- Different styling for sent vs received messages -->
    <div
      class="flex items-start group relative message-bubble"
      :class="isFromCurrentUser ? 'justify-end' : 'justify-start'"
    >
      <!-- Sender avatar (only for received messages or group chats) -->
      <div
        v-if="!isFromCurrentUser || conversation.isGroup"
        class="flex-shrink-0 mr-2"
      >
        <img
          :src="sender?.avatar || '/images/instructors/i1.jpg'"
          :alt="sender?.name || 'Unknown'"
          class="w-8 h-8 rounded-full object-cover mt-1"
        />
      </div>

      <!-- Message content -->
      <div
        class="max-w-[70%] group message-content"
        @click="!isSelectionMode ? null : emit('toggleSelection', message.id)"
        :class="{ 'opacity-70': isSelectionMode && !isSelected }"
      >
        <!-- Sender name for group chats -->
        <div
          v-if="conversation.isGroup && !isFromCurrentUser"
          class="text-xs font-medium mb-1 pl-0.5"
          :class="senderColor"
        >
          {{ sender?.name || "Unknown" }}
        </div>

        <!-- Message bubble -->
        <div
          class="rounded-2xl p-3 relative message-bubble"
          :class="[
            isFromCurrentUser
              ? 'bg-indigo-600 text-white rounded-tr-none'
              : 'bg-gray-100 text-gray-800 rounded-tl-none',
            isSelected ? 'ring-2 ring-offset-2 ring-indigo-500' : '',
            conversation.isGroup && isFromCurrentUser ? 'mr-2' : '',
            isMessageSent ? 'message-sending' : '',
          ]"
        >
          <!-- Selection indicator -->
          <div
            v-if="isSelectionMode && isSelected"
            class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-3 w-3"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </div>

          <!-- Text content -->
          <p class="whitespace-pre-wrap break-words message-text">
            {{ message.content }}
          </p>

          <!-- Attachments -->
          <div
            v-if="message.attachments && message.attachments.length > 0"
            class="mt-2 space-y-2 message-attachments"
          >
            <div
              v-for="(file, fileIndex) in message.attachments"
              :key="fileIndex"
              class="flex items-center p-2 rounded"
              :class="isFromCurrentUser ? 'bg-indigo-700' : 'bg-gray-200'"
            >
              <!-- File icon based on type -->
              <div
                :class="isFromCurrentUser ? 'text-indigo-300' : 'text-gray-500'"
              >
                <svg
                  v-if="file.type === 'pdf'"
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
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                  />
                </svg>
                <svg
                  v-else
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
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>

              <!-- File info -->
              <div class="ml-2 flex-grow overflow-hidden">
                <div
                  class="font-medium text-sm truncate"
                  :class="isFromCurrentUser ? 'text-white' : 'text-gray-800'"
                >
                  {{ file.name }}
                </div>
                <div
                  v-if="file.size"
                  class="text-xs"
                  :class="
                    isFromCurrentUser ? 'text-indigo-200' : 'text-gray-500'
                  "
                >
                  {{ formatFileSize(file.size) }}
                </div>
              </div>

              <!-- Download button -->
              <a
                :href="file.url"
                class="ml-2 p-1 rounded-full transition-colors"
                :class="
                  isFromCurrentUser
                    ? 'text-indigo-300 hover:text-white hover:bg-indigo-800'
                    : 'text-gray-500 hover:text-gray-800 hover:bg-gray-300'
                "
                download
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
            </div>
          </div>
        </div>

        <!-- Message timestamp and status -->
        <div
          class="text-xs mt-1 px-1 message-meta"
          :class="
            isFromCurrentUser ? 'text-right text-gray-500' : 'text-gray-500'
          "
        >
          {{ formattedTime }}
          <span v-if="isFromCurrentUser" class="ml-1">
            <!-- Read indicator -->
            <svg
              v-if="message.read"
              xmlns="http://www.w3.org/2000/svg"
              class="h-3 w-3 inline-block text-blue-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            <!-- Unread indicator -->
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-3 w-3 inline-block text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
          </span>
        </div>
      </div>

      <!-- Message actions -->
      <div
        class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 ml-2 self-center"
        v-if="!isSelectionMode"
      >
        <button
          @click="emit('toggleSelection', message.id)"
          class="p-1 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Message bubble animations */
.message-bubble {
  transition: all 0.3s ease;
}

.message-sending {
  animation: message-send-animation 0.5s ease-out;
}

@keyframes message-send-animation {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hover animations */
.message-content:hover .message-bubble {
  filter: brightness(97%);
}

/* File attachment hover effect */
.message-attachments a {
  transition: all 0.2s ease;
}

.message-attachments a:hover {
  transform: scale(1.2);
}
</style>
