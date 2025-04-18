<!-- components/messaging/ContactList.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

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

const props = defineProps<{
  contacts: Contact[];
  selectedContactId?: string;
}>();

const emit = defineEmits<{
  (e: "select", contactId: string): void;
}>();

const searchQuery = ref("");

const filteredContacts = computed(() => {
  return props.contacts.filter((contact) =>
    contact.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});
</script>

<template>
  <div class="h-full flex flex-col bg-white">
    <!-- Search Bar -->
    <div class="p-4 border-b border-gray-200">
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search contacts..."
          class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200"
        />
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <svg
            class="h-5 w-5 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </div>
      </div>
    </div>

    <!-- Contacts List -->
    <div class="flex-1 overflow-y-auto">
      <div class="divide-y divide-gray-200">
        <button
          v-for="contact in filteredContacts"
          :key="contact.id"
          @click="emit('select', contact.id)"
          class="w-full p-4 flex items-center space-x-4 hover:bg-gray-50 transition duration-200"
          :class="{
            'bg-indigo-50': contact.id === selectedContactId,
          }"
        >
          <!-- Avatar -->
          <div class="relative">
            <img
              :src="contact.avatar"
              :alt="contact.name"
              class="h-12 w-12 rounded-full object-cover"
            />
            <span
              class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white"
              :class="contact.isOnline ? 'bg-green-400' : 'bg-gray-400'"
            />
          </div>

          <!-- Contact Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-gray-900 truncate">
                {{ contact.name }}
              </p>
              <p class="text-xs text-gray-500">
                {{
                  new Date(contact.lastMessageTime).toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                  })
                }}
              </p>
            </div>
            <div class="flex items-center justify-between">
              <p class="text-sm text-gray-500 truncate">
                {{ contact.lastMessage }}
              </p>
              <div v-if="contact.unreadCount > 0" class="ml-2 flex-shrink-0">
                <span
                  class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-indigo-600 text-xs font-medium text-white"
                >
                  {{ contact.unreadCount }}
                </span>
              </div>
            </div>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>
