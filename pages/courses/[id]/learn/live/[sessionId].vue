<!-- pages/courses/[id]/learn/live/[sessionId].vue -->
<script setup lang="ts">
import { onMounted, onBeforeUnmount, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { LiveSessionHeader, LiveSessionVideo } from "#components";
import { useLiveSession } from "~/composables/useLiveSession";

// Route params
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));
const sessionId = computed(() => Number(route.params.sessionId));

// Use the live session composable
const {
  // State
  session,
  loading,
  error,
  connected,
  connecting,
  chatMessages,
  newMessage,
  activeTab,
  activePoll,
  resources,
  participants,
  showInfo,
  // isMobileView,

  // Computed
  isLive,
  hasEnded,
  canJoin,

  // Methods
  fetchSessionData,
  joinSession,
  leaveSession,
  sendMessage,
  // votePoll,
  formatTime,
  getVotePercentage,
  formatSessionTime,
  formatSessionDate,
  scrollToLatestMessage,
  checkMobileView,
} = useLiveSession(courseId.value, sessionId.value);

// Back to course
const backToCourse = () => {
  router.push(`/courses/${courseId.value}/learn`);
};

// Watch for tab changes
watch(activeTab, () => {
  if (activeTab.value === "chat") {
    // When switching to chat tab, scroll to the latest message
    setTimeout(scrollToLatestMessage, 100);
  }
});

// Lifecycle hooks
onMounted(() => {
  fetchSessionData();
  window.addEventListener("resize", checkMobileView);

  // Auto-scroll to bottom of chat
  setTimeout(scrollToLatestMessage, 500);
});

onBeforeUnmount(() => {
  // Clean up
  window.removeEventListener("resize", checkMobileView);

  // Ensure we disconnect from the session when leaving
  if (connected.value) {
    leaveSession();
  }
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"
        />
        <p class="mt-4 text-gray-600">Loading session...</p>
      </div>
    </div>

    <!-- Error State -->
    <div
      v-else-if="error"
      class="min-h-screen flex items-center justify-center"
    >
      <div class="text-center">
        <div class="text-red-500 text-5xl mb-4">⚠️</div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">
          Something went wrong
        </h2>
        <p class="text-gray-600 mb-4">
          We couldn't load the session. Please try again.
        </p>
        <UiButton color="primary" @click="fetchSessionData">Retry</UiButton>
      </div>
    </div>

    <!-- Session Information (shown before joining) -->
    <div v-else-if="showInfo" class="container mx-auto px-4 py-12">
      <div
        class="relative max-w-2xl mx-auto bg-gradient-to-br from-indigo-100 via-white to-indigo-50 rounded-3xl shadow-2xl border border-indigo-100 overflow-visible"
      >
        <!-- Floating Avatar -->
        <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 z-10">
          <img
            class="h-32 w-32 rounded-full border-4 border-white shadow-xl object-cover bg-white"
            src="/images/instructors/i1.jpg"
            alt="Live Session"
          />
        </div>
        <div class="pt-20 pb-8 px-8">
          <div class="flex flex-col items-center text-center">
            <div class="flex items-center gap-2 mb-2">
              <span
                class="uppercase tracking-widest text-xs text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-full shadow"
              >
                Live Session
              </span>
              <span
                v-if="isLive"
                class="flex items-center text-green-600 bg-green-100 px-2 py-0.5 rounded-full text-xs font-semibold ml-2"
              >
                <span
                  class="animate-pulse inline-block h-2 w-2 rounded-full bg-green-600 mr-1"
                />
                Live Now
              </span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2 drop-shadow">
              {{ session?.title }}
            </h1>
            <p class="text-gray-700 mb-4 max-w-xl">
              {{ session?.description }}
            </p>
            <div class="flex items-center justify-center mb-6">
              <img
                class="h-12 w-12 rounded-full border-2 border-indigo-200 mr-3"
                :src="session?.instructorAvatar"
                alt="Instructor"
              />
              <div class="text-left">
                <p class="text-base font-semibold text-gray-900">
                  {{ session?.instructorName }}
                </p>
                <p class="text-xs text-gray-500">Instructor</p>
              </div>
            </div>
            <div class="flex flex-wrap justify-center gap-4 mb-6">
              <div
                class="flex items-center text-sm bg-white/80 px-4 py-2 rounded-xl shadow"
              >
                <svg
                  class="h-5 w-5 text-indigo-400 mr-1"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
                <span>{{ formatSessionDate(session?.startTime || "") }}</span>
              </div>
              <div
                class="flex items-center text-sm bg-white/80 px-4 py-2 rounded-xl shadow"
              >
                <svg
                  class="h-5 w-5 text-indigo-400 mr-1"
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
                <span>
                  {{ formatSessionTime(session?.startTime || "") }} -
                  {{ formatSessionTime(session?.endTime || "") }}
                </span>
              </div>
            </div>
            <div class="flex items-center justify-center mt-2 mb-4">
              <span class="text-gray-600 mr-3 text-sm font-medium">
                {{ session?.attendees }} attending
              </span>
              <!-- Buttons (unchanged) -->
              <UiButton
                v-if="canJoin && !connecting"
                color="primary"
                @click="joinSession"
              >
                <template #prefix>
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
                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                    />
                  </svg>
                </template>
                Join Live Session
              </UiButton>
              <UiButton v-else-if="connecting" color="primary" disabled>
                <template #prefix>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="animate-spin h-5 w-5 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                    />
                  </svg>
                </template>
                Connecting...
              </UiButton>
              <UiButton
                v-else-if="hasEnded"
                color="secondary"
                @click="backToCourse"
              >
                <template #prefix>
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
                      d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                </template>
                Watch Recording
              </UiButton>
              <div
                v-else-if="!isLive"
                class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-medium inline-flex items-center ml-2"
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
    </div>

    <!-- Live Session Interface -->
    <div v-else class="flex flex-col h-screen">
      <!-- Header -->
      <LiveSessionHeader
        :session="{
          title: session?.title || '',
          instructorName: session?.instructorName || '',
          instructorAvatar: session?.instructorAvatar || '',
          attendees: session?.attendees || 0,
          startTime: session?.startTime || '',
          endTime: session?.endTime || '',
        }"
        :is-connecting="connecting"
        :has-ended="hasEnded"
        :is-live="isLive"
        @join="joinSession"
        @leave="leaveSession"
        @back="backToCourse"
      />

      <!-- Main Content Area -->
      <div class="flex-1 overflow-hidden">
        <div class="h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="h-full flex flex-col lg:flex-row gap-6 py-6">
            <!-- Video Section -->
            <div class="flex-1 min-h-0">
              <LiveSessionVideo
                :is-connecting="connecting"
                :has-ended="hasEnded"
                :is-live="isLive"
                :recording-url="session?.recordingUrl"
                @join="joinSession"
                @leave="leaveSession"
              />
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 flex-shrink-0">
              <div class="bg-white rounded-lg shadow-sm h-full">
                <!-- Tabs -->
                <div class="border-b border-gray-200">
                  <nav class="flex -mb-px">
                    <button
                      v-for="tab in [
                        'chat',
                        'participants',
                        'polls',
                        'resources',
                      ]"
                      :key="tab"
                      class="flex-1 py-3 px-4 text-center text-sm font-medium"
                      :class="[
                        activeTab === tab
                          ? 'text-indigo-600 border-b-2 border-indigo-600'
                          : 'text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      ]"
                      @click="activeTab = tab as any"
                    >
                      {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                    </button>
                  </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-4 h-[calc(100%-3rem)] overflow-y-auto">
                  <!-- Chat Tab -->
                  <div v-if="activeTab === 'chat'" class="h-full flex flex-col">
                    <div class="flex-1 overflow-y-auto space-y-4">
                      <div
                        v-for="message in chatMessages"
                        :key="message.id"
                        class="flex items-start space-x-3"
                      >
                        <img
                          :src="message.userAvatar"
                          :alt="message.userName"
                          class="h-8 w-8 rounded-full"
                        />
                        <div class="flex-1">
                          <div class="flex items-center space-x-2">
                            <span
                              class="font-medium"
                              :class="
                                message.isInstructor
                                  ? 'text-indigo-600'
                                  : 'text-gray-900'
                              "
                            >
                              {{ message.userName }}
                            </span>
                            <span class="text-xs text-gray-500">
                              {{ formatTime(message.timestamp) }}
                            </span>
                          </div>
                          <p class="text-gray-600">{{ message.message }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Message Input -->
                    <div class="mt-4">
                      <div class="flex space-x-2">
                        <UiInput
                          v-model="newMessage"
                          type="text"
                          placeholder="Type a message..."
                          @keyup.enter="sendMessage"
                        />
                        <UiButton color="primary" @click="sendMessage"
                          >Send</UiButton
                        >
                      </div>
                    </div>
                  </div>

                  <!-- Participants Tab -->
                  <div
                    v-else-if="activeTab === 'participants'"
                    class="space-y-4"
                  >
                    <div
                      v-for="participant in participants"
                      :key="participant.id"
                      class="flex items-center space-x-3"
                    >
                      <img
                        :src="participant.avatar"
                        :alt="participant.name"
                        class="h-8 w-8 rounded-full"
                      />
                      <div class="flex-1">
                        <span class="font-medium text-gray-900">{{
                          participant.name
                        }}</span>
                        <span
                          v-if="participant.isInstructor"
                          class="ml-2 text-xs text-indigo-600 font-medium"
                        >
                          Instructor
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Polls Tab -->
                  <div v-else-if="activeTab === 'polls'" class="space-y-4">
                    <div v-if="activePoll" class="bg-gray-50 rounded-lg p-4">
                      <h3 class="font-medium text-gray-900 mb-4">
                        {{ activePoll.question }}
                      </h3>
                      <div class="space-y-3">
                        <div
                          v-for="option in activePoll.options"
                          :key="option.id"
                          class="relative"
                        >
                          <div
                            class="h-2 bg-gray-200 rounded-full overflow-hidden"
                            :style="{
                              width: `${
                                (option.votes / activePoll.totalVotes) * 100
                              }%`,
                            }"
                          >
                            <div class="h-full bg-indigo-600" />
                          </div>
                          <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-600">{{ option.text }}</span>
                            <span class="text-gray-500"
                              >{{ option.votes }} votes</span
                            >
                          </div>
                          <div class="mt-1 text-xs text-gray-500 text-right">
                            {{
                              getVotePercentage(
                                option.votes,
                                activePoll.totalVotes
                              )
                            }}%
                          </div>
                        </div>
                      </div>
                      <div class="mt-4 text-sm text-gray-500">
                        Total votes: {{ activePoll.totalVotes }}
                      </div>
                    </div>
                    <div v-else class="text-center text-gray-500">
                      No active polls at the moment
                    </div>
                  </div>

                  <!-- Resources Tab -->
                  <div v-else-if="activeTab === 'resources'" class="space-y-4">
                    <div
                      v-for="resource in resources"
                      :key="resource.id"
                      class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg"
                    >
                      <div
                        class="w-8 h-8 rounded-lg flex items-center justify-center"
                        :class="{
                          'bg-red-100 text-red-600': resource.type === 'pdf',
                          'bg-blue-100 text-blue-600':
                            resource.type === 'github',
                        }"
                      >
                        <svg
                          v-if="resource.type === 'pdf'"
                          class="w-5 h-5"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                          />
                        </svg>
                        <svg
                          v-else-if="resource.type === 'github'"
                          class="w-5 h-5"
                          fill="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </div>
                      <div class="flex-1">
                        <a
                          :href="resource.url"
                          class="text-sm font-medium text-gray-900 hover:text-indigo-600"
                        >
                          {{ resource.title }}
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9; /* gray-100 */
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1; /* gray-300 */
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1; /* indigo-500 */
}

/* Transitions for tabs */
.tab-fade-enter-active,
.tab-fade-leave-active {
  transition: opacity 0.2s;
}
.tab-fade-enter-from,
.tab-fade-leave-to {
  opacity: 0;
}
</style>
