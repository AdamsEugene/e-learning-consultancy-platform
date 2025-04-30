<template>
  <div
    class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-green-900 flex flex-col"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between px-8 py-4 border-b border-gray-700 bg-gray-900/80"
    >
      <div class="flex items-center gap-3">
        <button class="text-gray-400 hover:text-white focus:outline-none">
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
        <span class="text-gray-200 text-lg font-semibold"
          >[Backlog 03] Audio Redesign Landing Page</span
        >
      </div>
      <div class="flex items-center gap-4">
        <span
          class="flex items-center gap-1 text-red-500 font-semibold text-sm bg-gray-800 px-2 py-1 rounded"
        >
          <span class="animate-pulse h-2 w-2 rounded-full bg-red-500"></span>
          LIVE
        </span>
        <span class="text-gray-400 text-xs">1:23</span>
        <img
          src="https://randomuser.me/api/portraits/women/44.jpg"
          class="h-8 w-8 rounded-full border-2 border-gray-700"
          alt="User"
        />
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Video Grid -->
      <div
        class="flex-1 flex flex-col justify-center items-center p-6 overflow-auto"
      >
        <div class="grid grid-cols-3 gap-4 w-full max-w-5xl">
          <template v-for="user in videoUsers" :key="user.id">
            <div
              class="relative bg-gray-800 rounded-xl overflow-hidden flex items-center justify-center aspect-video border-2 border-gray-700"
            >
              <img
                v-if="user.video"
                :src="user.video"
                class="object-cover w-full h-full"
              />
              <div
                v-else-if="user.avatar"
                class="flex flex-col items-center justify-center w-full h-full"
              >
                <img
                  :src="user.avatar"
                  class="h-20 w-20 rounded-full border-4 border-white shadow mb-2"
                />
                <span class="text-white font-semibold text-lg">{{
                  user.name
                }}</span>
              </div>
              <div
                v-else
                class="flex items-center justify-center w-full h-full bg-gray-700"
              >
                <span class="text-3xl font-bold text-white">{{
                  user.initials
                }}</span>
              </div>
              <span
                class="absolute left-2 bottom-2 bg-black/60 text-white text-xs px-2 py-0.5 rounded"
                >{{ user.name }}</span
              >
              <span
                v-if="user.muted"
                class="absolute top-2 right-2 bg-gray-900/80 rounded-full p-1"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-red-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 9v6h4l5 5V4l-5 5H9z"
                  />
                </svg>
              </span>
            </div>
          </template>
        </div>
        <!-- Controls -->
        <div class="flex gap-4 mt-6">
          <button
            class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-full focus:outline-none"
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
                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
              />
            </svg>
          </button>
          <button
            class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-full focus:outline-none"
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
          <button
            class="bg-red-600 hover:bg-red-700 text-white p-3 rounded-full focus:outline-none"
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
                d="M18.364 5.636l-1.414 1.414A9 9 0 105.636 18.364l1.414-1.414"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Sidebar -->
      <div
        class="w-full max-w-sm bg-gray-900 border-l border-gray-800 flex flex-col"
      >
        <!-- Tabs -->
        <div class="flex">
          <button
          :class="
            activeTab === 'chat'
              ? 'border-b-2 border-green-400 text-green-400'
              : 'text-gray-400'
          "
          class="flex-1 py-3 text-center font-semibold"
            @click="activeTab = 'chat'"
          >
            Chat
          </button>
          <button
          :class="
            activeTab === 'participants'
              ? 'border-b-2 border-green-400 text-green-400'
              : 'text-gray-400'
          "
          class="flex-1 py-3 text-center font-semibold"
            @click="activeTab = 'participants'"
          >
            Participants
          </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
          <!-- Chat Tab -->
          <div v-if="activeTab === 'chat'" class="flex flex-col h-full">
            <div class="flex-1 space-y-4 overflow-y-auto pr-2">
              <div
                v-for="msg in chatMessages"
                :key="msg.id"
                class="flex items-start gap-3"
              >
                <img
                  v-if="msg.avatar"
                  :src="msg.avatar"
                  class="h-9 w-9 rounded-full border-2 border-gray-800"
                />
                <div
                  v-else
                  class="h-9 w-9 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold"
                >
                  {{ msg.initials }}
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-200">{{
                      msg.name
                    }}</span>
                    <span class="text-xs text-gray-400">{{ msg.time }}</span>
                  </div>
                  <div
                    class="bg-gray-800 text-gray-100 rounded-xl px-4 py-2 mt-1 max-w-xs"
                  >
                    <span v-if="msg.image"
                      ><img :src="msg.image" class="rounded mb-2 w-32"
                    /></span>
                    {{ msg.text }}
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-4 flex gap-2">
              <input
                v-model="newMessage"
                type="text"
                placeholder="Send a message..."
                class="flex-1 bg-gray-800 text-gray-100 rounded-lg px-4 py-2 focus:outline-none"
                @keyup.enter="sendMessage"
              />
              <button
              class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold"
                @click="sendMessage"
              >
                Send
              </button>
            </div>
          </div>
          <!-- Participants Tab -->
          <div v-else class="space-y-4">
            <div
              v-for="user in videoUsers"
              :key="user.id"
              class="flex items-center gap-3"
            >
              <img
                v-if="user.avatar"
                :src="user.avatar"
                class="h-8 w-8 rounded-full border-2 border-gray-800"
              />
              <div
                v-else
                class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold"
              >
                {{ user.initials }}
              </div>
              <span class="text-gray-200 font-medium">{{ user.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

const activeTab = ref<"chat" | "participants">("chat");
const newMessage = ref("");

const videoUsers = [
  {
    id: 1,
    name: "James",
    avatar: "https://randomuser.me/api/portraits/men/32.jpg",
    video:
      "https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=400&h=400",
    muted: false,
    initials: "J",
  },
  {
    id: 2,
    name: "Mary B",
    avatar: "https://randomuser.me/api/portraits/women/65.jpg",
    video: "",
    muted: true,
    initials: "MB",
  },
  {
    id: 3,
    name: "Oliver I",
    avatar: "",
    video: "",
    muted: false,
    initials: "OI",
  },
  {
    id: 4,
    name: "John",
    avatar: "https://randomuser.me/api/portraits/men/45.jpg",
    video: "",
    muted: false,
    initials: "J",
  },
  {
    id: 5,
    name: "Jessica",
    avatar: "https://randomuser.me/api/portraits/women/68.jpg",
    video: "",
    muted: true,
    initials: "J",
  },
  {
    id: 6,
    name: "Simona V",
    avatar: "",
    video: "",
    muted: false,
    initials: "SV",
  },
  {
    id: 7,
    name: "Nina Williams",
    avatar: "https://randomuser.me/api/portraits/women/43.jpg",
    video: "",
    muted: false,
    initials: "NW",
  },
  {
    id: 8,
    name: "Inna Y",
    avatar: "",
    video: "",
    muted: false,
    initials: "IY",
  },
  {
    id: 9,
    name: "Zoe S (You)",
    avatar: "https://randomuser.me/api/portraits/women/50.jpg",
    video: "",
    muted: false,
    initials: "ZS",
  },
];

const chatMessages = ref([
  {
    id: 1,
    name: "Oliver I",
    avatar: "",
    initials: "OI",
    time: "12:48 PM",
    text: "When you starting a company you are thinking on how to cut expenses.",
    image:
      "https://static.wikia.nocookie.net/muppet/images/7/7c/Bert_and_Ernie.png",
  },
  {
    id: 2,
    name: "Kyle Peters",
    avatar: "",
    initials: "KP",
    time: "12:45 PM",
    text: "When you starting a company you are thinking on how to cut expenses. One of such options to cut the startup costs is a company logo design.",
  },
  {
    id: 3,
    name: "You",
    avatar: "",
    initials: "BA",
    time: "12:45 PM",
    text: "Nice!",
  },
]);

function sendMessage() {
  if (!newMessage.value.trim()) return;
  chatMessages.value.push({
    id: Date.now(),
    name: "You",
    avatar: "",
    initials: "BA",
    time: new Date().toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    }),
    text: newMessage.value,
  });
  newMessage.value = "";
}
</script>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #22c55e;
}
</style>

