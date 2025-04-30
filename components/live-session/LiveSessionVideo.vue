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
      class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800"
    >
      <div class="text-center p-8">
        <div
          class="w-20 h-20 mx-auto mb-6 rounded-full bg-indigo-500/10 flex items-center justify-center"
        >
          <svg
            class="w-10 h-10 text-indigo-400"
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
        <h3 class="text-xl font-semibold text-white mb-3">
          Session hasn't started yet
        </h3>
        <p class="text-gray-400 mb-6 max-w-sm mx-auto">
          Join when the session begins to participate live
        </p>
        <UiButton
          v-if="!isConnecting"
          variant="primary"
          state="default"
          size="lg"
          class="px-8"
          @click="emit('join')"
        >
          Join Session
        </UiButton>
        <UiButton
          v-else
          variant="primary"
          state="default"
          size="lg"
          loading
          class="px-8"
        >
          Connecting...
        </UiButton>
      </div>
    </div>

    <!-- Live Video -->
    <div v-else-if="isLive" class="absolute inset-0">
      <!-- Live indicator -->
      <div
        class="absolute top-4 left-4 z-10 flex items-center space-x-2 bg-black/50 backdrop-blur-sm px-3 py-1.5 rounded-full"
      >
        <span class="flex h-2.5 w-2.5">
          <span
            class="animate-ping absolute inline-flex h-2.5 w-2.5 rounded-full bg-red-400 opacity-75"
          />
          <span
            class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"
          />
        </span>
        <span class="text-sm font-medium text-white">LIVE</span>
      </div>

      <!-- Video content -->
      <div
        class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800"
      >
        <div class="text-white text-center">
          <div
            class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-500/10 flex items-center justify-center"
          >
            <svg
              class="w-8 h-8 text-indigo-400"
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
          <p class="text-lg text-gray-300">Video stream will appear here</p>
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
      class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800"
    >
      <div class="text-center p-8">
        <div
          class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-800/50 flex items-center justify-center"
        >
          <svg
            class="w-10 h-10 text-gray-400"
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
        <h3 class="text-xl font-semibold text-white mb-3">Session has ended</h3>
        <p class="text-gray-400">Recording will be available soon</p>
      </div>
    </div>
  </div>
</template>
