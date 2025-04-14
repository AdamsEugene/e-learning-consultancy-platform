<!-- components/messaging/UserProfileSidebar.vue -->
<script setup lang="ts">
import { computed } from "vue";
import type { User, Conversation, Message } from "~/types/messaging";

interface Props {
  conversation: Conversation | null;
  currentUser: User;
  messages: Message[];
  isMobile: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: "close"): void;
}>();

// Get conversation info
const isGroupChat = computed(() => {
  return props.conversation?.isGroup || false;
});

// Get other participant for direct conversations
const otherParticipant = computed(() => {
  if (!props.conversation || isGroupChat.value) return null;

  return props.conversation.participants.find(
    (participant) => participant.id !== props.currentUser.id
  );
});

// Filter shared media and files from messages
const sharedMedia = computed(() => {
  if (!props.messages || props.messages.length === 0) return [];

  return props.messages
    .filter((message) => message.attachments && message.attachments.length > 0)
    .flatMap((message) => message.attachments || [])
    .filter((file) => file.type.includes("image"))
    .slice(0, 6);
});

const sharedFiles = computed(() => {
  if (!props.messages || props.messages.length === 0) return [];

  return props.messages
    .filter((message) => message.attachments && message.attachments.length > 0)
    .flatMap((message) => message.attachments || [])
    .filter((file) => !file.type.includes("image"))
    .slice(0, 5);
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

// Format last seen time (for direct messages)
const formattedLastSeen = computed(() => {
  if (!otherParticipant.value || otherParticipant.value.online) return "";

  if (!otherParticipant.value.lastActive) return "Offline";

  const lastActive = new Date(otherParticipant.value.lastActive);
  const now = new Date();
  const diffMs = now.getTime() - lastActive.getTime();
  const diffMins = Math.floor(diffMs / (1000 * 60));

  if (diffMins < 1) return "Just now";
  if (diffMins < 60) return `${diffMins}m ago`;

  const diffHours = Math.floor(diffMins / 60);
  if (diffHours < 24) return `${diffHours}h ago`;

  const diffDays = Math.floor(diffHours / 24);
  if (diffDays === 1) return "Yesterday";

  return lastActive.toLocaleDateString();
});

// Get course info based on user role (simplified - would fetch from API in real app)
const courseInfo = computed(() => {
  if (isGroupChat.value) return [];

  // Mock data for demo - would be fetched from API in real app
  if (otherParticipant.value?.role === "instructor") {
    if (otherParticipant.value.id === 201) {
      return [
        {
          title: "Complete Web Development Bootcamp",
          role: "Instructor",
          icon: "code",
          color: "indigo",
        },
      ];
    } else if (otherParticipant.value.id === 202) {
      return [
        {
          title: "UI/UX Design Masterclass",
          role: "Instructor",
          icon: "layout",
          color: "purple",
        },
      ];
    } else if (otherParticipant.value.id === 203) {
      return [
        {
          title: "JavaScript Algorithms and Data Structures",
          role: "Instructor",
          icon: "file-text",
          color: "green",
        },
      ];
    } else {
      return [
        {
          title: "Mobile App Development",
          role: "Instructor",
          icon: "smartphone",
          color: "blue",
        },
      ];
    }
  } else {
    return [];
  }
});

// Get about text based on user role
const aboutText = computed(() => {
  if (isGroupChat.value)
    return "A group chat for discussing topics related to this course.";

  // Mock data for demo - would be fetched from API in real app
  if (otherParticipant.value?.role === "instructor") {
    if (otherParticipant.value.id === 201) {
      return "Web Development instructor specializing in responsive design, CSS, and frontend frameworks.";
    } else if (otherParticipant.value.id === 202) {
      return "UI/UX Design instructor with 8+ years of industry experience in digital product design.";
    } else if (otherParticipant.value.id === 203) {
      return "Algorithms and data structures expert. PhD in Computer Science with focus on performance optimization.";
    } else {
      return "Mobile app development instructor specializing in React Native and cross-platform technologies.";
    }
  } else {
    return "No information available";
  }
});
</script>

<template>
  <div
    class="w-80 border-l border-gray-200 bg-gray-50 h-full overflow-y-auto sidebar-container"
  >
    <!-- Info container -->
    <div class="p-4">
      <!-- Close button (mobile only) -->
      <button
        v-if="isMobile"
        class="md:hidden absolute top-4 right-4 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-full transition-colors"
        @click="emit('close')"
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

      <!-- Content depends on group or individual -->
      <template v-if="isGroupChat">
        <!-- Group info -->
        <div class="text-center mb-6">
          <img
            :src="conversation?.groupAvatar || '/images/instructors/i1.jpg'"
            :alt="conversation?.groupName"
            class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border border-gray-200"
          />
          <h3 class="text-xl font-bold text-gray-900">
            {{ conversation?.groupName }}
          </h3>
          <p class="text-gray-500">
            {{ conversation?.participants.length }} members
          </p>
        </div>

        <!-- Group actions -->
        <div class="flex justify-center space-x-2 mb-6">
          <button
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors tooltip-container"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
              />
            </svg>
            <span class="tooltip">Edit Group</span>
          </button>
          <button
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors tooltip-container"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                clip-rule="evenodd"
              />
            </svg>
            <span class="tooltip">Privacy Settings</span>
          </button>
          <button
            class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors tooltip-container"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
            <span class="tooltip">Leave Group</span>
          </button>
        </div>

        <!-- About section for groups -->
        <div class="mb-6">
          <h4 class="text-sm font-medium text-gray-500 mb-2">ABOUT</h4>
          <p class="text-gray-700 p-3 bg-white rounded-lg">
            {{ aboutText }}
          </p>
        </div>

        <!-- Members list -->
        <div>
          <h4 class="text-sm font-medium text-gray-500 mb-2">MEMBERS</h4>
          <div class="space-y-3">
            <div
              v-for="participant in conversation?.participants"
              :key="participant.id"
              class="flex items-center p-2 hover:bg-white rounded-lg transition-colors"
            >
              <div class="relative">
                <img
                  :src="participant.avatar"
                  :alt="participant.name"
                  class="w-10 h-10 rounded-full object-cover"
                />
                <div
                  v-if="participant.online"
                  class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"
                ></div>
              </div>
              <div class="ml-3">
                <div class="font-medium text-gray-900">
                  {{ participant.name }}
                  <span
                    v-if="participant.id === currentUser.id"
                    class="text-gray-500 text-sm"
                    >(You)</span
                  >
                </div>
                <div class="text-xs text-gray-500 capitalize">
                  {{ participant.role }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>

      <template v-else>
        <!-- Individual contact info -->
        <div class="text-center mb-6">
          <img
            :src="otherParticipant?.avatar || '/images/instructors/i1.jpg'"
            :alt="otherParticipant?.name"
            class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border border-gray-200"
          />
          <h3 class="text-xl font-bold text-gray-900">
            {{ otherParticipant?.name }}
          </h3>

          <p
            class="text-sm"
            :class="
              otherParticipant?.online ? 'text-green-600' : 'text-gray-500'
            "
          >
            <span v-if="otherParticipant?.online"> Online </span>
            <span v-else>
              {{ formattedLastSeen }}
            </span>
          </p>

          <p class="text-sm text-gray-500 capitalize">
            {{ otherParticipant?.role || "User" }}
          </p>
        </div>

        <!-- Contact actions -->
        <div class="flex justify-center space-x-2 mb-6">
          <button
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors tooltip-container"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                clip-rule="evenodd"
              />
            </svg>
            <span class="tooltip">Chat Options</span>
          </button>
          <button
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors tooltip-container"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
              />
            </svg>
            <span class="tooltip">Call</span>
          </button>
          <button
            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-colors tooltip-container"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
              />
            </svg>
            <span class="tooltip">Video Call</span>
          </button>
        </div>

        <!-- Contact info sections -->
        <div class="space-y-6">
          <!-- About section -->
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-2">ABOUT</h4>
            <p class="text-gray-700 p-3 bg-white rounded-lg">
              {{ aboutText }}
            </p>
          </div>

          <!-- Shared courses -->
          <div v-if="courseInfo.length > 0">
            <h4 class="text-sm font-medium text-gray-500 mb-2">
              SHARED COURSES
            </h4>
            <div class="space-y-2">
              <div
                v-for="(course, index) in courseInfo"
                :key="index"
                class="p-3 bg-white rounded-lg flex items-center"
              >
                <div
                  class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                  :class="`bg-${course.color}-100 text-${course.color}-600`"
                >
                  <!-- Code icon -->
                  <svg
                    v-if="course.icon === 'code'"
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
                      d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                    />
                  </svg>

                  <!-- Layout icon -->
                  <svg
                    v-else-if="course.icon === 'layout'"
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
                      d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"
                    />
                  </svg>

                  <!-- File text icon -->
                  <svg
                    v-else-if="course.icon === 'file-text'"
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
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>

                  <!-- Smartphone icon -->
                  <svg
                    v-else-if="course.icon === 'smartphone'"
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
                      d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
                    />
                  </svg>
                </div>
                <div class="ml-3">
                  <div class="font-medium text-gray-900 text-sm">
                    {{ course.title }}
                  </div>
                  <div class="text-xs text-gray-500">{{ course.role }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Media files -->
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-2">SHARED MEDIA</h4>
            <div class="p-3 bg-white rounded-lg">
              <div v-if="sharedMedia.length > 0" class="grid grid-cols-3 gap-2">
                <div
                  v-for="(file, index) in sharedMedia"
                  :key="index"
                  class="aspect-square rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden relative"
                >
                  <!-- Image preview (placeholder) -->
                  <img
                    :src="`/images/courses/c3.jpg`"
                    alt="Media preview"
                    class="w-full h-full object-cover"
                  />

                  <!-- Hover overlay with download -->
                  <div
                    class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center"
                  >
                    <a
                      :href="file.url"
                      download
                      class="p-1 bg-white rounded-full text-gray-800 hover:text-indigo-600"
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
              <div v-else class="text-gray-500 text-center text-sm">
                No shared media
              </div>

              <!-- View all link -->
              <div v-if="sharedMedia.length > 0" class="mt-2 text-center">
                <a
                  href="#"
                  class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                >
                  View All Media
                </a>
              </div>
            </div>
          </div>

          <!-- Shared files -->
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-2">SHARED FILES</h4>
            <div class="p-3 bg-white rounded-lg space-y-2">
              <div v-if="sharedFiles.length > 0">
                <div
                  v-for="(file, index) in sharedFiles"
                  :key="index"
                  class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors"
                >
                  <!-- File type icon -->
                  <div class="text-gray-500">
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
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                      />
                    </svg>
                  </div>

                  <!-- File info -->
                  <div class="ml-2 flex-grow overflow-hidden">
                    <div class="font-medium text-sm truncate text-gray-900">
                      {{ file.name }}
                    </div>
                    <div v-if="file.size" class="text-xs text-gray-500">
                      {{ formatFileSize(file.size) }}
                    </div>
                  </div>

                  <!-- Download button -->
                  <a
                    :href="file.url"
                    download
                    class="p-1 rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50"
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
              <div v-else class="text-gray-500 text-center text-sm">
                No shared files
              </div>

              <!-- View all link -->
              <div v-if="sharedFiles.length > 0" class="mt-2 text-center">
                <a
                  href="#"
                  class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                >
                  View All Files
                </a>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
/* Tooltip styles */
.tooltip-container {
  position: relative;
}

.tooltip {
  position: absolute;
  bottom: -30px;
  left: 50%;
  transform: translateX(-50%);
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s, visibility 0.2s;
  z-index: 10;
}

.tooltip-container:hover .tooltip {
  opacity: 1;
  visibility: visible;
}

/* Scrollbar styling */
.sidebar-container::-webkit-scrollbar {
  width: 4px;
}

.sidebar-container::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.sidebar-container::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}

.sidebar-container::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>
