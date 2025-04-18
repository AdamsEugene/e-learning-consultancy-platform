<script setup lang="ts">
import { useRouter } from "vue-router";
import ContactList from "~/components/messaging/ContactList.vue";

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

const router = useRouter();

const handleContactSelect = (contactId: string) => {
  router.push(`/messaging/${contactId}`);
};

// Set page metadata
useHead({
  title: "Messages - LearnConsult",
  meta: [
    {
      name: "description",
      content: "Chat with your instructors, consultants, and peers",
    },
  ],
});
</script>

<template>
  <div class="h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
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
          <ContactList
            :current-user-id="currentUserId"
            :contacts="contacts"
            @select="handleContactSelect"
          />
        </div>
      </div>
    </main>
  </div>
</template>
