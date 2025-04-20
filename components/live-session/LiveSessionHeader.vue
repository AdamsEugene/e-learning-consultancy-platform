<script setup lang="ts">
interface Props {
  session: {
    title: string;
    instructorName: string;
    instructorAvatar: string;
    attendees: number;
    startTime: string;
    endTime: string;
  };
  isConnecting: boolean;
  hasEnded: boolean;
  isLive: boolean;
}

defineProps<Props>();
const emit = defineEmits<{
  (e: "join" | "leave" | "back"): void;
}>();
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="py-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <img
            :src="session.instructorAvatar"
            :alt="session.instructorName"
            class="h-10 w-10 rounded-full"
          />
          <div>
            <h1 class="text-xl font-semibold text-gray-900">
              {{ session.title }}
            </h1>
            <div class="flex items-center text-sm text-gray-500">
              <span>{{ session.instructorName }}</span>
              <span class="mx-2">•</span>
              <span>{{ session.attendees }} attending</span>
              <span class="mx-2">•</span>
              <span>{{ session.startTime }} - {{ session.endTime }}</span>
            </div>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <UiButton
            v-if="!isConnecting && !hasEnded && isLive"
            variant="danger"
            state="default"
            @click="emit('leave')"
          >
            Leave Session
          </UiButton>

          <UiButton
            v-else-if="!isConnecting && !hasEnded && !isLive"
            variant="primary"
            state="default"
            @click="emit('join')"
          >
            Join Session
          </UiButton>

          <UiButton v-else-if="isConnecting" variant="primary" state="default" :loading="true">
            Connecting...
          </UiButton>

          <UiButton
            v-else-if="hasEnded"
            variant="ghost"
            state="default"
            @click="emit('back')"
          >
            Watch Recording
          </UiButton>

          <div
            v-else-if="!isLive"
            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-medium inline-flex items-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Session Not Started
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
