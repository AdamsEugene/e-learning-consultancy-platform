<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import ContactList from "./ContactList.vue";
import MessageList from "./MessageList.vue";
import MessageInput from "./MessageInput.vue";

interface Contact {
  id: string;
  name: string;
  avatar: string;
  lastMessage: string;
  lastMessageTime: Date;
  unreadCount: number;
  isOnline: boolean;
  role: "student" | "instructor" | "consultant" | "admin";
}

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
  currentUserId: string;
  contacts: Contact[];
  messages: Record<string, Message[]>;
  initialSelectedContactId?: string;
}>();

const selectedContactId = ref<string>(props.initialSelectedContactId || "");

// Computed properties
const selectedContact = computed(() => {
  if (!selectedContactId.value) return null;
  return props.contacts.find((c) => c.id === selectedContactId.value);
});

const currentMessages = computed(() => {
  if (!selectedContactId.value) return [];
  return props.messages[selectedContactId.value] || [];
});

// Methods
const handleContactSelect = (contactId: string) => {
  selectedContactId.value = contactId;
};

const handleSendMessage = (content: string) => {
  if (!selectedContactId.value) return;

  const newMessage: Message = {
    id: Date.now().toString(),
    content,
    senderId: props.currentUserId,
    timestamp: new Date(),
    status: "sending",
    type: "text",
  };

  // Emit event to parent to handle message sending
  emit("send", {
    contactId: selectedContactId.value,
    message: newMessage,
  });
};

const handleFileAttach = (file: File) => {
  if (!selectedContactId.value) return;

  const newMessage: Message = {
    id: Date.now().toString(),
    content: "",
    senderId: props.currentUserId,
    timestamp: new Date(),
    status: "sending",
    type: file.type.startsWith("image/") ? "image" : "file",
    fileName: file.name,
    fileSize: file.size,
    // fileUrl will be set after upload
  };

  emit("send", {
    contactId: selectedContactId.value,
    message: newMessage,
    file,
  });
};

const emit = defineEmits<{
  (e: "send", data: { contactId: string; message: Message; file?: File }): void;
}>();

// Watch for changes in initialSelectedContactId
onMounted(() => {
  if (props.initialSelectedContactId) {
    selectedContactId.value = props.initialSelectedContactId;
  }
});
</script>

<template>
  <div class="h-full flex bg-white">
    <!-- Contact List Sidebar -->
    <div class="w-80 border-r border-gray-200">
      <ContactList
        :contacts="contacts"
        :selected-contact-id="selectedContactId"
        @select="handleContactSelect"
      />
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
      <!-- Chat Header -->
      <div
        v-if="selectedContact"
        class="h-16 border-b border-gray-200 px-4 flex items-center justify-between bg-white"
      >
        <div class="flex items-center space-x-3">
          <img
            :src="selectedContact.avatar"
            :alt="selectedContact.name"
            class="h-10 w-10 rounded-full"
          />
          <div>
            <h3 class="text-sm font-medium text-gray-900">
              {{ selectedContact.name }}
            </h3>
            <p class="text-xs text-gray-500">
              {{ selectedContact.isOnline ? "Online" : "Offline" }}
            </p>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex-1 flex items-center justify-center">
        <div class="text-center">
          <svg
            class="mx-auto h-12 w-12 text-gray-400"
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
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            No conversation selected
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            Select a contact to start chatting
          </p>
        </div>
      </div>

      <!-- Message List -->
      <MessageList
        v-if="selectedContact"
        :messages="currentMessages"
        :current-user-id="currentUserId"
      />

      <!-- Message Input -->
      <MessageInput
        v-if="selectedContact"
        :on-send-message="handleSendMessage"
        :on-send-file="handleFileAttach"
      />
    </div>
  </div>
</template>
