<!-- components/messaging/MessageArea.vue -->
<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import type { User, Conversation, Message } from "~/types/messaging";

const props = defineProps<{
  conversation: Conversation | null;
  messages: Message[];
  currentUser: User;
  loading: boolean;
  isMobile: boolean;
  showUserInfo: boolean;
  isMessageSent: boolean;
}>();

const emit = defineEmits<{
  (e: "back" | "toggleUserInfo" | "newConversation"): void;
  (e: "sendMessage", content: string, attachments?: File[]): void;
}>();

// Message selection
const isSelectionMode = ref(false);
const selectedMessages = ref<number[]>([]);

// Group messages by date
const groupedMessages = computed(() => {
  if (!props.messages.length) return {};

  return props.messages.reduce((groups: Record<string, Message[]>, message) => {
    const date = new Date(message.timestamp).toLocaleDateString();
    if (!groups[date]) {
      groups[date] = [];
    }
    groups[date].push(message);
    return groups;
  }, {});
});

// Get conversation name (either group name or other participant's name)
const conversationName = computed((): string => {
  if (!props.conversation) return "";

  if (props.conversation.isGroup && props.conversation.groupName) {
    return props.conversation.groupName;
  }

  const otherParticipant = props.conversation.participants.find(
    (participant) => participant.id !== props.currentUser.id
  );

  return otherParticipant?.name || "Unknown Contact";
});

// Get conversation avatar (either group avatar or other participant's avatar)
const conversationAvatar = computed((): string => {
  if (!props.conversation) return "";

  if (props.conversation.isGroup && props.conversation.groupAvatar) {
    return props.conversation.groupAvatar;
  }

  const otherParticipant = props.conversation.participants.find(
    (participant) => participant.id !== props.currentUser.id
  );

  return otherParticipant?.avatar || "/images/instructors/i1.jpg";
});

// Check if online
const isOnline = computed((): boolean => {
  if (!props.conversation || props.conversation.isGroup) return false;

  const otherParticipant = props.conversation.participants.find(
    (participant) => participant.id !== props.currentUser.id
  );

  return !!otherParticipant?.online;
});

// Toggle message selection
const toggleMessageSelection = (messageId: number) => {
  if (!isSelectionMode.value) {
    isSelectionMode.value = true;
    selectedMessages.value = [messageId];
  } else {
    const index = selectedMessages.value.indexOf(messageId);
    if (index > -1) {
      selectedMessages.value.splice(index, 1);
      // Exit selection mode if no messages selected
      if (selectedMessages.value.length === 0) {
        isSelectionMode.value = false;
      }
    } else {
      selectedMessages.value.push(messageId);
    }
  }
};

// Cancel selection mode
const cancelSelection = () => {
  isSelectionMode.value = false;
  selectedMessages.value = [];
};

// Delete selected messages
const deleteSelectedMessages = () => {
  // In a real app, you would emit an event to delete the messages
  cancelSelection();
};

// Format date for display
const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString([], {
    weekday: "long",
    month: "long",
    day: "numeric",
  });
};

// Typing indicator (to simulate the other person typing)
const isTyping = ref(false);
const typingTimeout = ref<number | null>(null);

// Simulate typing effect in response to user messages
watch(
  () => props.messages,
  (newMessages, oldMessages) => {
    if (
      newMessages.length > oldMessages.length &&
      newMessages[newMessages.length - 1].senderId === props.currentUser.id
    ) {
      // User sent a message, simulate typing response
      setTimeout(() => {
        isTyping.value = true;

        // Clear any existing timeout
        if (typingTimeout.value) {
          clearTimeout(typingTimeout.value);
        }

        // Set a timeout to hide the typing indicator after 2-4 seconds
        typingTimeout.value = window.setTimeout(() => {
          isTyping.value = false;
        }, Math.random() * 2000 + 2000);
      }, 1000);
    }
  },
  { deep: true }
);

// Handle send message
const handleSendMessage = (content: string, attachments?: File[]) => {
  emit("sendMessage", content, attachments);
};

// Focus message input on conversation change
watch(
  () => props.conversation,
  () => {
    nextTick(() => {
      const inputEl = document.querySelector(".message-input textarea");
      if (inputEl) {
        (inputEl as HTMLTextAreaElement).focus();
      }
    });
  }
);
</script>

<template>
  <div class="w-full h-full flex flex-col">
    <!-- Empty state when no conversation selected -->
    <div
      v-if="!conversation"
      class="h-full flex items-center justify-center p-4"
    >
      <div class="text-center max-w-md">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 mx-auto text-gray-400 mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
          />
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">
          No conversation selected
        </h3>
        <p class="text-gray-600 mb-4">
          Select a conversation or start a new one to begin messaging
        </p>
        <button
          @click="emit('newConversation')"
          class="py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors transform hover:scale-105 duration-200"
        >
          Start a new conversation
        </button>
      </div>
    </div>

    <!-- Conversation content -->
    <template v-else>
      <!-- Conversation header -->
      <div
        class="px-4 py-3 border-b border-gray-200 flex items-center justify-between conversation-header"
      >
        <!-- Back button (mobile only) -->
        <button
          v-if="isMobile"
          @click="emit('back')"
          class="mr-2 text-gray-600 hover:text-gray-900 transition-colors"
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
              d="M15 19l-7-7 7-7"
            />
          </svg>
        </button>

        <!-- Conversation info -->
        <div class="flex items-center flex-grow">
          <div class="relative">
            <img
              :src="conversationAvatar"
              :alt="conversationName"
              class="w-10 h-10 rounded-full object-cover border border-gray-200"
            />
            <!-- Online indicator for direct messages -->
            <div
              v-if="isOnline"
              class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"
            ></div>
            <!-- Group indicator -->
            <div
              v-if="conversation.isGroup"
              class="absolute bottom-0 right-0 w-5 h-5 bg-indigo-600 rounded-full border-2 border-white flex items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-3 w-3 text-white"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                />
              </svg>
            </div>
          </div>
          <div class="ml-3">
            <div class="font-medium text-gray-900">{{ conversationName }}</div>
            <div class="text-xs text-gray-500">
              <template v-if="conversation.isGroup">
                {{ conversation.participants.length }} members
              </template>
              <template v-else-if="isOnline"> Online </template>
              <template v-else> Offline </template>
            </div>
          </div>
        </div>

        <!-- Header actions -->
        <div class="flex items-center space-x-2">
          <!-- Info button -->
          <button
            @click="emit('toggleUserInfo')"
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors"
            :class="{ 'bg-indigo-50 text-indigo-600': showUserInfo }"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- More options button -->
          <button
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Selection mode toolbar -->
      <div
        v-if="isSelectionMode"
        class="px-4 py-2 bg-indigo-600 text-white flex items-center justify-between selection-toolbar"
      >
        <div class="flex items-center">
          <button
            @click="cancelSelection"
            class="p-2 text-white hover:bg-indigo-700 rounded-full transition-colors mr-2"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
          <span>{{ selectedMessages.length }} selected</span>
        </div>

        <div class="flex space-x-2">
          <!-- Copy button -->
          <button
            class="p-2 text-white hover:bg-indigo-700 rounded-full transition-colors"
            :disabled="selectedMessages.length !== 1"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z" />
              <path
                d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"
              />
            </svg>
          </button>

          <!-- Forward button -->
          <button
            class="p-2 text-white hover:bg-indigo-700 rounded-full transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- Delete button -->
          <button
            @click="deleteSelectedMessages"
            class="p-2 text-white hover:bg-red-600 rounded-full transition-colors"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Messages container -->
      <div
        id="messages-container"
        class="flex-grow p-4 overflow-y-auto messages-container"
        :class="{ 'md:pr-80': showUserInfo }"
      >
        <!-- Loading state -->
        <div v-if="loading" class="h-full flex items-center justify-center">
          <div
            class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-indigo-600"
          ></div>
        </div>

        <!-- Messages list -->
        <div v-else class="space-y-4">
          <!-- Empty state when no messages -->
          <div v-if="messages.length === 0" class="text-center py-8">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-12 w-12 mx-auto text-gray-400 mb-4"
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
            <p class="text-gray-600">No messages yet</p>
            <p class="text-gray-500 text-sm mt-2">
              Send a message to start the conversation
            </p>
          </div>

          <!-- Typing indicator -->
          <div
            v-if="isTyping && conversation && !conversation.isGroup"
            class="flex items-start ml-12"
          >
            <div
              class="bg-gray-100 rounded-2xl p-3 rounded-tl-none inline-flex items-center"
            >
              <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-500 rounded-full typing-dot"></div>
                <div class="w-2 h-2 bg-gray-500 rounded-full typing-dot"></div>
                <div class="w-2 h-2 bg-gray-500 rounded-full typing-dot"></div>
              </div>
            </div>
          </div>

          <!-- Group messages by date -->
          <template v-for="(messageGroup, date) in groupedMessages" :key="date">
            <!-- Date separator -->
            <div class="flex justify-center mb-4 mt-6 first:mt-0">
              <div
                class="bg-gray-200 text-gray-700 rounded-full px-3 py-1 text-xs font-medium"
              >
                {{ formatDate(date) }}
              </div>
            </div>

            <!-- Messages in group -->
            <div class="space-y-4">
              <DashboardMessagingMessageBubble
                v-for="message in messageGroup"
                :key="message.id"
                :message="message"
                :current-user="currentUser"
                :conversation="conversation"
                :is-selection-mode="isSelectionMode"
                :is-selected="selectedMessages.includes(message.id)"
                :is-message-sent="
                  isMessageSent && message === messages[messages.length - 1]
                "
                @toggle-selection="toggleMessageSelection(message.id)"
              />
            </div>
          </template>
        </div>
      </div>

      <!-- Message input area -->
      <DashboardMessagingMessageInput
        @send-message="handleSendMessage"
        class="border-t border-gray-200"
      />
    </template>
  </div>
</template>

<style scoped>
/* Messages container custom scrollbar */
.messages-container::-webkit-scrollbar {
  width: 6px;
}

.messages-container::-webkit-scrollbar-track {
  background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Typing indicator animation */
.typing-dot {
  animation: typing-animation 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(1) {
  animation-delay: 0s;
}

.typing-dot:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes typing-animation {
  0%,
  80%,
  100% {
    transform: scale(0.6);
    opacity: 0.6;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
