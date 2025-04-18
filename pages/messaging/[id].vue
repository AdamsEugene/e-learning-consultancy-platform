<script setup lang="ts">
import { useRoute, useRouter } from "vue-router";
import ChatWindow from "~/components/messaging/ChatWindow.vue";

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

// Mock data - replace with actual API calls
const currentUserId = "user1";

const contacts: Contact[] = [
  {
    id: "user2",
    name: "John Doe",
    avatar: "https://randomuser.me/api/portraits/men/1.jpg",
    lastMessage: "Hey, how are you?",
    lastMessageTime: new Date(),
    unreadCount: 2,
    isOnline: true,
    role: "student",
  },
  {
    id: "user3",
    name: "Jane Smith",
    avatar: "https://randomuser.me/api/portraits/women/1.jpg",
    lastMessage: "Can we schedule a meeting?",
    lastMessageTime: new Date(Date.now() - 3600000),
    unreadCount: 0,
    isOnline: false,
    role: "instructor",
  },
  {
    id: "user4",
    name: "Robert Johnson",
    avatar: "https://randomuser.me/api/portraits/men/2.jpg",
    lastMessage: "I've reviewed your project proposal",
    lastMessageTime: new Date(Date.now() - 86400000),
    unreadCount: 0,
    isOnline: false,
    role: "consultant",
  },
  {
    id: "user5",
    name: "Emily Davis",
    avatar: "https://randomuser.me/api/portraits/women/2.jpg",
    lastMessage: "The course materials are ready",
    lastMessageTime: new Date(Date.now() - 172800000),
    unreadCount: 1,
    isOnline: true,
    role: "instructor",
  },
];

const messages: Record<string, Message[]> = {
  user2: [
    {
      id: "1",
      content: "Hi there!",
      senderId: "user2",
      timestamp: new Date(Date.now() - 86400000),
      status: "read",
      type: "text",
    },
    {
      id: "2",
      content: "Hey, how are you?",
      senderId: "user2",
      timestamp: new Date(Date.now() - 3600000),
      status: "read",
      type: "text",
    },
  ],
  user3: [
    {
      id: "3",
      content: "Hello!",
      senderId: "user3",
      timestamp: new Date(Date.now() - 7200000),
      status: "read",
      type: "text",
    },
    {
      id: "4",
      content: "Can we schedule a meeting?",
      senderId: "user3",
      timestamp: new Date(Date.now() - 3600000),
      status: "read",
      type: "text",
    },
  ],
  user4: [
    {
      id: "5",
      content: "I've reviewed your project proposal",
      senderId: "user4",
      timestamp: new Date(Date.now() - 86400000),
      status: "read",
      type: "text",
    },
    {
      id: "6",
      content: "It looks good, but we need to make some adjustments",
      senderId: "user1",
      timestamp: new Date(Date.now() - 82800000),
      status: "read",
      type: "text",
    },
  ],
  user5: [
    {
      id: "7",
      content: "The course materials are ready",
      senderId: "user5",
      timestamp: new Date(Date.now() - 172800000),
      status: "read",
      type: "text",
    },
    {
      id: "8",
      content: "Great! When will they be available?",
      senderId: "user1",
      timestamp: new Date(Date.now() - 169200000),
      status: "read",
      type: "text",
    },
  ],
};

const route = useRoute();
const router = useRouter();
const contactId = route.params.id as string;

// Set initial selected contact based on route parameter
const initialSelectedContactId = contacts.some(
  (contact) => contact.id === contactId
)
  ? contactId
  : "";

// Find the current contact for the header
const currentContact = contacts.find((contact) => contact.id === contactId);

// Set page metadata
useHead({
  title: currentContact
    ? `${currentContact.name} - Messages - LearnConsult`
    : "Messages - LearnConsult",
  meta: [
    {
      name: "description",
      content: currentContact
        ? `Chat with ${currentContact.name}`
        : "Chat with your instructors, consultants, and peers",
    },
  ],
});

const handleSendMessage = (data: {
  contactId: string;
  message: Message;
  file?: File;
}) => {
  console.log("Sending message:", data);
  // Implement actual message sending logic here
};

const goBack = () => {
  router.push("/messaging");
};
</script>

<template>
  <div class="h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <button
              @click="goBack"
              class="mr-4 p-2 rounded-full hover:bg-gray-100 focus:outline-none"
            >
              <svg
                class="h-6 w-6 text-gray-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"
                />
              </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-900">
              {{ currentContact ? currentContact.name : "Messages" }}
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <button
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg
                class="h-5 w-5 mr-2"
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
              New Message
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="bg-white shadow rounded-lg h-full">
          <ChatWindow
            :current-user-id="currentUserId"
            :contacts="contacts"
            :messages="messages"
            :initial-selected-contact-id="initialSelectedContactId"
            @send="handleSendMessage"
          />
        </div>
      </div>
    </main>
  </div>
</template>
