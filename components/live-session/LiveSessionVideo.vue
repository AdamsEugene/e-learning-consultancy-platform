<script setup lang="ts">
interface Props {
  isConnecting: boolean;
  hasEnded: boolean;
  isLive: boolean;
  recordingUrl?: string;
}

defineProps<Props>();
const emit = defineEmits<{
  (e: "join" | "leave"): void;
}>();
</script>

<template>
  <div class="relative aspect-video bg-gray-900 rounded-lg overflow-hidden">
    <!-- Video Placeholder -->
    <div
      v-if="!isLive && !hasEnded"
      class="absolute inset-0 flex items-center justify-center"
    >
      <div class="text-center">
        <div
          class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-100 flex items-center justify-center"
        >
          <svg
            class="w-8 h-8 text-indigo-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-white mb-2">
          Session hasn't started yet
        </h3>
        <p class="text-gray-400 mb-4">
          Join when the session begins to participate live
        </p>
        <UiButton
          v-if="!isConnecting"
          color="primary"
          size="lg"
          @click="emit('join')"
        >
          Join Session
        </UiButton>
        <UiButton v-else color="primary" size="lg" loading>
          Connecting...
        </UiButton>
      </div>
    </div>

    <!-- Live Video -->
    <div v-else-if="isLive" class="absolute inset-0">
      <div class="absolute top-4 left-4 flex items-center space-x-2">
        <span class="flex h-3 w-3">
          <span
            class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-red-400 opacity-75"
          />
          <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500" />
        </span>
        <span class="text-sm font-medium text-white">LIVE</span>
      </div>
      <!-- Video content will be injected here -->
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="text-white text-center">
          <p class="text-lg">Video stream will appear here</p>
        </div>
      </div>
    </div>

    <!-- Recording -->
    <div v-else-if="hasEnded && recordingUrl" class="absolute inset-0">
      <video class="w-full h-full" controls :src="recordingUrl">
        Your browser does not support the video tag.
      </video>
    </div>

    <!-- No Recording Available -->
    <div
      v-else-if="hasEnded"
      class="absolute inset-0 flex items-center justify-center"
    >
      <div class="text-center">
        <div
          class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-800 flex items-center justify-center"
        >
          <svg
            class="w-8 h-8 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-white mb-2">Session has ended</h3>
        <p class="text-gray-400">Recording will be available soon</p>
      </div>
    </div>
  </div>
</template>
