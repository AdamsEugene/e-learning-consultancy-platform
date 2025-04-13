<!-- components/dashboard/CommunityCard.vue -->
<script setup lang="ts">
interface Author {
  name: string;
  avatar: string;
}

interface Update {
  id: number;
  type: "forum" | "announcement" | "study_group";
  title: string;
  author: Author;
  course: string;
  lastActivity?: string;
  replies?: number;
  members?: number;
  nextMeeting?: string;
  liked: boolean;
  saved: boolean;
}

interface Props {
  updates: Update[];
  formatDate: (dateString: string) => string;
  animationDelay?: number;
  isLoaded?: boolean;
}

const props = defineProps<Props>();

// Emits
const emit = defineEmits(["like", "save", "view"]);

// Local state for UI interactions (in a real app, this would sync with backend)
const likedPosts = ref(
  new Set(props.updates.filter((u) => u.liked).map((u) => u.id))
);
const savedPosts = ref(
  new Set(props.updates.filter((u) => u.saved).map((u) => u.id))
);

// Toggle like status
const toggleLike = (updateId: number) => {
  if (likedPosts.value.has(updateId)) {
    likedPosts.value.delete(updateId);
  } else {
    likedPosts.value.add(updateId);
  }

  // Emit event for parent component
  emit("like", updateId, likedPosts.value.has(updateId));
};

// Toggle save status
const toggleSave = (updateId: number) => {
  if (savedPosts.value.has(updateId)) {
    savedPosts.value.delete(updateId);
  } else {
    savedPosts.value.add(updateId);
  }

  // Emit event for parent component
  emit("save", updateId, savedPosts.value.has(updateId));
};

// Handle view event
const viewUpdate = (updateId: number, type: string) => {
  emit("view", updateId, type);
};

// Get icon for update type
const getTypeIcon = (type: string): string => {
  switch (type) {
    case "forum":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
      </svg>`;
    case "announcement":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd" />
      </svg>`;
    case "study_group":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
      </svg>`;
  }
};

// Get type label
const getTypeLabel = (type: string): string => {
  switch (type) {
    case "forum":
      return "Discussion";
    case "announcement":
      return "Announcement";
    case "study_group":
      return "Study Group";
    default:
      return "Update";
  }
};

// Get type color
const getTypeColor = (type: string): string => {
  switch (type) {
    case "forum":
      return "bg-indigo-100 text-indigo-800";
    case "announcement":
      return "bg-red-100 text-red-800";
    case "study_group":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Community Updates</h3>
        <a
          href="/dashboard/community"
          class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center transition-colors"
        >
          View All
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </a>
      </div>
    </div>

    <div class="divide-y divide-gray-100">
      <!-- Update items -->
      <div
        v-for="(update, index) in updates"
        :key="update.id"
        class="p-4 md:p-6 hover:bg-gray-50 transition-all duration-200"
      >
        <div class="flex">
          <!-- Type icon -->
          <div
            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-4"
            :class="getTypeColor(update.type)"
            v-html="getTypeIcon(update.type)"
          ></div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
              <div>
                <!-- Type badge -->
                <div
                  class="text-xs font-medium rounded-full px-2 py-0.5 inline-block mb-1"
                  :class="getTypeColor(update.type)"
                >
                  {{ getTypeLabel(update.type) }}
                </div>

                <!-- Title -->
                <h4
                  class="text-lg font-bold text-gray-900 cursor-pointer hover:text-indigo-600 transition-colors"
                  @click="viewUpdate(update.id, update.type)"
                >
                  {{ update.title }}
                </h4>

                <!-- Course name -->
                <p class="text-gray-600 text-sm">{{ update.course }}</p>

                <!-- Meta information based on type -->
                <div class="flex items-center text-sm text-gray-500 mt-2">
                  <!-- Author info -->
                  <div class="flex items-center mr-4">
                    <img
                      :src="update.author.avatar"
                      :alt="update.author.name"
                      class="w-5 h-5 rounded-full mr-1.5"
                    />
                    <span>{{ update.author.name }}</span>
                  </div>

                  <!-- Forum replies -->
                  <div
                    v-if="
                      update.type === 'forum' && update.replies !== undefined
                    "
                    class="flex items-center mr-4"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                      />
                    </svg>
                    <span
                      >{{ update.replies }}
                      {{ update.replies === 1 ? "reply" : "replies" }}</span
                    >
                  </div>

                  <!-- Study group members -->
                  <div
                    v-if="
                      update.type === 'study_group' &&
                      update.members !== undefined
                    "
                    class="flex items-center mr-4"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                      />
                    </svg>
                    <span
                      >{{ update.members }}
                      {{ update.members === 1 ? "member" : "members" }}</span
                    >
                  </div>

                  <!-- Last activity or meeting time -->
                  <div class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
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
                    <span v-if="update.lastActivity">
                      Last activity: {{ formatDate(update.lastActivity) }}
                    </span>
                    <span v-else-if="update.nextMeeting">
                      Next meeting: {{ formatDate(update.nextMeeting) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action buttons -->
              <div class="flex space-x-2">
                <!-- Like button -->
                <button
                  class="text-gray-400 hover:text-indigo-600 transition-colors rounded-full p-1 hover:bg-indigo-50"
                  :class="{ 'text-indigo-600': likedPosts.has(update.id) }"
                  @click="toggleLike(update.id)"
                  aria-label="Like"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"
                    />
                  </svg>
                </button>

                <!-- Save button -->
                <button
                  class="text-gray-400 hover:text-indigo-600 transition-colors rounded-full p-1 hover:bg-indigo-50"
                  :class="{ 'text-indigo-600': savedPosts.has(update.id) }"
                  @click="toggleSave(update.id)"
                  aria-label="Save"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer - Join community section -->
    <div
      class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-t border-gray-100"
    >
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-lg font-bold text-gray-900">Join the Community</h4>
          <p class="text-gray-600 text-sm">
            Connect with other learners, join study groups, and get help with
            your courses.
          </p>
        </div>
        <a
          href="/dashboard/community/join"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 inline-flex items-center whitespace-nowrap"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
            />
          </svg>
          Join Now
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}
</style>
