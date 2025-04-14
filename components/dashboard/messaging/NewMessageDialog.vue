<!-- components/messaging/NewMessageDialog.vue -->
<script setup lang="ts">
import { ref, watch } from "vue";
import type { User } from "~/types/messaging";

interface Props {
  isOpen: boolean;
  availableUsers: User[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "close"): void;
  (e: "create", users: User[], isGroup: boolean, groupName?: string): void;
}>();

// State
const selectedUsers = ref<User[]>([]);
const isCreatingGroup = ref(false);
const newGroupName = ref("");
const searchQuery = ref("");

// Clear state when dialog is closed
watch(
  () => props.isOpen,
  (isOpen) => {
    if (!isOpen) {
      // Reset state after dialog closes
      setTimeout(() => {
        selectedUsers.value = [];
        isCreatingGroup.value = false;
        newGroupName.value = "";
        searchQuery.value = "";
      }, 300);
    }
  }
);

// Filtered users based on search
const filteredUsers = computed(() => {
  if (!searchQuery.value.trim()) return props.availableUsers;

  const query = searchQuery.value.toLowerCase();
  return props.availableUsers.filter(
    (user) =>
      user.name.toLowerCase().includes(query) ||
      user.role.toLowerCase().includes(query)
  );
});

// Toggle user selection
const toggleUserSelection = (user: User) => {
  const index = selectedUsers.value.findIndex((u) => u.id === user.id);
  if (index > -1) {
    selectedUsers.value.splice(index, 1);
  } else {
    selectedUsers.value.push(user);
  }
};

// Create conversation
const createConversation = () => {
  if (selectedUsers.value.length === 0) return;

  emit(
    "create",
    selectedUsers.value,
    isCreatingGroup.value || selectedUsers.value.length > 1,
    newGroupName.value.trim() || undefined
  );
};

// Close the dialog
const closeDialog = () => {
  emit("close");
};
</script>

<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 transition-opacity duration-300"
    @click="closeDialog"
  >
    <div
      class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-hidden transform transition-transform duration-300"
      :class="isOpen ? 'scale-100' : 'scale-95'"
      @click.stop
    >
      <!-- Dialog header -->
      <div
        class="px-6 py-4 border-b border-gray-200 flex items-center justify-between"
      >
        <h3 class="text-lg font-bold text-gray-900">New Message</h3>
        <button
          @click="closeDialog"
          class="p-1 text-gray-400 hover:text-gray-600 rounded-full transition-colors"
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
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Dialog content -->
      <div class="p-6">
        <!-- Group chat toggle -->
        <div class="flex items-center mb-4">
          <span class="mr-3 text-gray-700">Create group chat</span>
          <label class="inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              v-model="isCreatingGroup"
              class="sr-only peer"
            />
            <div
              class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
            ></div>
          </label>
        </div>

        <!-- Group name input (if creating group) -->
        <div v-if="isCreatingGroup || selectedUsers.length > 1" class="mb-4">
          <label
            for="group-name"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Group Name</label
          >
          <input
            id="group-name"
            v-model="newGroupName"
            type="text"
            placeholder="Enter group name"
            class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
        </div>

        <!-- User search -->
        <div class="mb-4">
          <label
            for="user-search"
            class="block text-sm font-medium text-gray-700 mb-1"
            >To:</label
          >
          <div class="relative">
            <input
              id="user-search"
              v-model="searchQuery"
              type="text"
              placeholder="Search users..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

        <!-- Selected users pills -->
        <div v-if="selectedUsers.length > 0" class="mb-4 flex flex-wrap gap-2">
          <div
            v-for="user in selectedUsers"
            :key="user.id"
            class="flex items-center bg-indigo-50 text-indigo-800 px-3 py-1 rounded-full text-sm"
          >
            <img
              :src="user.avatar"
              :alt="user.name"
              class="w-5 h-5 rounded-full object-cover mr-1"
            />
            <span>{{ user.name }}</span>
            <button
              @click="toggleUserSelection(user)"
              class="ml-1 text-indigo-400 hover:text-indigo-600"
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

        <!-- Available users list -->
        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
          <div
            v-if="filteredUsers.length === 0"
            class="p-4 text-center text-gray-500"
          >
            No users found
          </div>

          <div
            v-for="user in filteredUsers"
            :key="user.id"
            @click="toggleUserSelection(user)"
            class="flex items-center p-3 hover:bg-gray-50 cursor-pointer transition-colors"
            :class="{
              'bg-indigo-50': selectedUsers.some((u) => u.id === user.id),
            }"
          >
            <div class="relative">
              <img
                :src="user.avatar"
                :alt="user.name"
                class="w-10 h-10 rounded-full object-cover"
              />
              <div
                v-if="user.online"
                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"
              ></div>
            </div>
            <div class="ml-3 flex-grow">
              <div class="font-medium text-gray-900">{{ user.name }}</div>
              <div class="text-xs text-gray-500 capitalize">
                {{ user.role }}
              </div>
            </div>
            <div
              v-if="selectedUsers.some((u) => u.id === user.id)"
              class="w-5 h-5 bg-indigo-600 rounded-full flex items-center justify-center"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-3 w-3 text-white"
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
          </div>
        </div>
      </div>

      <!-- Dialog footer -->
      <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
        <button
          @click="closeDialog"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg mr-2 hover:bg-gray-50 transition-colors"
        >
          Cancel
        </button>
        <button
          @click="createConversation"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          :class="{
            'opacity-50 cursor-not-allowed': selectedUsers.length === 0,
          }"
          :disabled="selectedUsers.length === 0"
        >
          {{
            isCreatingGroup || selectedUsers.length > 1
              ? "Create Group"
              : "Start Conversation"
          }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Dialog animations */
.scale-95 {
  transform: scale(0.95);
}

.scale-100 {
  transform: scale(1);
}

/* Scrollbar styling */
.max-h-60::-webkit-scrollbar {
  width: 4px;
}

.max-h-60::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.max-h-60::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>
