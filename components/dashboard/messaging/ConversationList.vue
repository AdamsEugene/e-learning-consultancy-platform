<!-- components/messaging/ConversationList.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";
import type { User, Conversation } from "~/types/messaging";

const props = defineProps<{
  conversations: Conversation[];
  loading: boolean;
  currentUser: User;
  activeConversation: Conversation | null;
}>();

const emit = defineEmits<{
  (e: "selectConversation", conversation: Conversation): void;
  (e: "newConversation"): void;
}>();

// Local state
const searchQuery = ref("");

// Filtered conversations based on search
const filteredConversations = computed(() => {
  if (!searchQuery.value.trim()) return props.conversations;

  const query = searchQuery.value.toLowerCase();
  return props.conversations.filter((conversation) => {
    // For group chats, search in group name
    if (conversation.isGroup && conversation.groupName) {
      return conversation.groupName.toLowerCase().includes(query);
    }

    // For direct messages, search in participant names
    return conversation.participants.some(
      (participant) =>
        participant.id !== props.currentUser.id &&
        participant.name.toLowerCase().includes(query)
    );
  });
});

// Get conversation name (either group name or other participant's name)
const getConversationName = (conversation: Conversation): string => {
  if (conversation.isGroup && conversation.groupName) {
    return conversation.groupName;
  }

  const otherParticipant = conversation.participants.find(
    (participant) => participant.id !== props.currentUser.id
  );

  return otherParticipant?.name || "Unknown Contact";
};

// Get conversation avatar (either group avatar or other participant's avatar)
const getConversationAvatar = (conversation: Conversation): string => {
  if (conversation.isGroup && conversation.groupAvatar) {
    return conversation.groupAvatar;
  }

  const otherParticipant = conversation.participants.find(
    (participant) => participant.id !== props.currentUser.id
  );

  return otherParticipant?.avatar || "/images/instructors/i1.jpg";
};

// Format timestamp for display
const formatMessageTime = (timestamp: string): string => {
  const date = new Date(timestamp);
  const now = new Date();
  const diffDays = Math.floor(
    (now.getTime() - date.getTime()) / (1000 * 60 * 60 * 24)
  );

  if (diffDays === 0) {
    // Today - show time only
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  } else if (diffDays === 1) {
    // Yesterday
    return "Yesterday";
  } else if (diffDays < 7) {
    // This week - show day name
    return date.toLocaleDateString([], { weekday: "short" });
  } else {
    // Older - show date
    return date.toLocaleDateString([], { month: "short", day: "numeric" });
  }
};

// Handle conversation click
const handleConversationClick = (conversation: Conversation) => {
  emit("selectConversation", conversation);
};
</script>

<template>
  <div
    class="w-full md:w-80 xl:w-96 border-r border-gray-200 flex flex-col h-full"
  >
    <!-- Header with search -->
    <div class="p-4 border-b border-gray-200">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-gray-900">Messages</h2>
        <button
          @click="emit('newConversation')"
          class="text-indigo-600 hover:text-indigo-800 transition-colors p-2 rounded-full hover:bg-indigo-50 transform hover:rotate-90 duration-300"
          title="New Message"
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
              d="M12 4v16m8-8H4"
            />
          </svg>
        </button>
      </div>

      <!-- Search bar -->
      <div class="relative">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search messages..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
        />
        <div class="absolute left-3 top-2.5 text-gray-400">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </div>
    </div>

    <!-- Conversations list -->
    <div class="flex-grow overflow-y-auto">
      <!-- Loading spinner -->
      <div v-if="loading" class="flex items-center justify-center h-full">
        <div
          class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-600"
        ></div>
      </div>

      <!-- Empty state -->
      <div
        v-else-if="filteredConversations.length === 0"
        class="flex flex-col items-center justify-center h-full p-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-gray-400 mb-4"
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
        <p class="text-gray-600 text-center">No conversations found</p>
        <p class="text-gray-500 text-sm text-center mt-2">
          Try a different search term or start a new conversation
        </p>
        <button
          @click="emit('newConversation')"
          class="mt-4 py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors transform hover:scale-105 duration-200"
        >
          Start a conversation
        </button>
      </div>

      <!-- Conversations -->
      <div v-else>
        <div v-if="searchQuery" class="p-3 text-sm text-gray-500">
          Results for "{{ searchQuery }}"
        </div>

        <!-- Pinned conversations -->
        <div v-if="filteredConversations.some((c) => c.isPinned)">
          <div
            class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Pinned
          </div>
          <div
            v-for="conversation in filteredConversations.filter(
              (c) => c.isPinned
            )"
            :key="conversation.id"
            @click="handleConversationClick(conversation)"
            class="p-3 flex items-center hover:bg-gray-50 cursor-pointer transition-colors relative conversation-item"
            :class="{
              'bg-indigo-50': activeConversation?.id === conversation.id,
            }"
          >
            <!-- Avatar with online indicator -->
            <div class="relative">
              <img
                :src="getConversationAvatar(conversation)"
                :alt="getConversationName(conversation)"
                class="w-12 h-12 rounded-full object-cover border border-gray-200"
              />
              <!-- Online indicator for direct messages -->
              <div
                v-if="
                  !conversation.isGroup &&
                  conversation.participants.find((p) => p.id !== currentUser.id)
                    ?.online
                "
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

            <!-- Conversation info -->
            <div class="ml-3 flex-grow min-w-0">
              <div class="flex justify-between items-baseline">
                <span class="font-medium text-gray-900 truncate">{{
                  getConversationName(conversation)
                }}</span>
                <span class="text-xs text-gray-500">{{
                  conversation.lastMessage
                    ? formatMessageTime(conversation.lastMessage.timestamp)
                    : ""
                }}</span>
              </div>
              <div class="flex justify-between items-center mt-1">
                <p class="text-sm text-gray-600 truncate max-w-[180px]">
                  {{
                    conversation.lastMessage
                      ? (conversation.lastMessage.senderId === currentUser.id
                          ? "You: "
                          : "") + conversation.lastMessage.content
                      : "No messages yet"
                  }}
                </p>
                <!-- Unread count badge -->
                <div
                  v-if="conversation.unreadCount > 0"
                  class="ml-2 bg-indigo-600 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full animate-bounce-subtle"
                >
                  {{ conversation.unreadCount }}
                </div>
              </div>
            </div>

            <!-- Pinned indicator -->
            <div class="absolute top-1 right-1 text-indigo-600">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M5 5a1 1 0 011-1h8a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1V5z"
                />
                <path d="M10 14a1 1 0 100 2 1 1 0 000-2z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Other conversations -->
        <div>
          <div
            class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Conversations
          </div>
          <div
            v-for="(conversation, index) in filteredConversations.filter(
              (c) => !c.isPinned
            )"
            :key="conversation.id"
            @click="handleConversationClick(conversation)"
            class="p-3 flex items-center hover:bg-gray-50 cursor-pointer transition-colors conversation-item"
            :class="[
              { 'bg-indigo-50': activeConversation?.id === conversation.id },
              'animate-fade-in',
            ]"
            :style="{ animationDelay: `${index * 50}ms` }"
          >
            <!-- Avatar with online indicator -->
            <div class="relative">
              <img
                :src="getConversationAvatar(conversation)"
                :alt="getConversationName(conversation)"
                class="w-12 h-12 rounded-full object-cover border border-gray-200"
              />
              <!-- Online indicator for direct messages -->
              <div
                v-if="
                  !conversation.isGroup &&
                  conversation.participants.find((p) => p.id !== currentUser.id)
                    ?.online
                "
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

            <!-- Conversation info -->
            <div class="ml-3 flex-grow min-w-0">
              <div class="flex justify-between items-baseline">
                <span class="font-medium text-gray-900 truncate">{{
                  getConversationName(conversation)
                }}</span>
                <span class="text-xs text-gray-500">{{
                  conversation.lastMessage
                    ? formatMessageTime(conversation.lastMessage.timestamp)
                    : ""
                }}</span>
              </div>
              <div class="flex justify-between items-center mt-1">
                <p class="text-sm text-gray-600 truncate max-w-[180px]">
                  {{
                    conversation.lastMessage
                      ? (conversation.lastMessage.senderId === currentUser.id
                          ? "You: "
                          : "") + conversation.lastMessage.content
                      : "No messages yet"
                  }}
                </p>
                <!-- Unread count badge -->
                <div
                  v-if="conversation.unreadCount > 0"
                  class="ml-2 bg-indigo-600 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full animate-bounce-subtle"
                >
                  {{ conversation.unreadCount }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation keyframes */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce-subtle {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

.animate-bounce-subtle {
  animation: bounce-subtle 2s infinite ease-in-out;
}

.conversation-item {
  transition: all 0.2s ease;
}

.conversation-item:hover {
  transform: translateX(3px);
}

/* Hide scrollbar for Chrome, Safari and Opera */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>
