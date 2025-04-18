<!-- components/courses/LessonTabs.vue -->
<script setup lang="ts">
import type { CourseInstructor } from "~/types/courseTemp";

type TabType = "content" | "resources" | "notes" | "discussions";

interface Props {
  activeTab: TabType;
  lessonContent?: string;
  instructor?: CourseInstructor | null;
}

// Props
withDefaults(defineProps<Props>(), {
  lessonContent: "",
  instructor: null,
});

const emit = defineEmits(["update:active-tab", "toggle-notes"]);

const tabs = [
  { id: "content", label: "Lesson Content", icon: "document-text" },
  { id: "resources", label: "Resources", icon: "download" },
  { id: "notes", label: "Notes", icon: "pencil-alt" },
  { id: "discussions", label: "Discussions", icon: "chat" },
];

const changeTab = (tabId: string) => {
  emit("update:active-tab", tabId);
};

// Sample resources for demonstration
const resources = [
  {
    id: 1,
    title: "Lesson Slides",
    description: "Download the presentation slides from this lesson",
    type: "pdf",
    url: "#",
  },
  {
    id: 2,
    title: "Code Examples",
    description: "Source code from the examples shown in this lesson",
    type: "code",
    url: "#",
  },
  {
    id: 3,
    title: "Practice Exercises",
    description: "Additional exercises to reinforce your learning",
    type: "exercises",
    url: "#",
  },
];

// Sample discussion threads for demonstration
const discussions = [
  {
    id: 1,
    user: {
      name: "Alex Thompson",
      avatar: "/images/courses/c3.jpg",
      role: "Student",
    },
    content:
      "Can someone explain the difference between inline and block elements in more detail? The lesson touched on it, but I'm still a bit confused.",
    timestamp: "2 days ago",
    upvotes: 3,
    replies: [
      {
        id: 101,
        user: {
          name: "Sarah Johnson",
          avatar: "/images/courses/c3.jpg",
          role: "Instructor",
        },
        content:
          "Great question! The main difference is how they interact with other elements in the document flow. Block elements always start on a new line and take up the full width available, while inline elements only take up as much width as necessary and don't force a new line. Examples of block elements include div, p, and h1-h6. Inline elements include span, a, and strong.",
        timestamp: "1 day ago",
      },
    ],
  },
];

// Discussion form state
const discussionText = ref("");
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
    <!-- Tab navigation -->
    <div class="border-b">
      <div class="flex overflow-x-auto">
        <UiButton
          v-for="tab in tabs"
          :key="tab.id"
          variant="ghost"
          size="md"
          class="px-4 py-3 text-sm font-medium transition-colors flex items-center whitespace-nowrap"
          :class="
            activeTab === tab.id
              ? 'border-b-2 border-indigo-600 text-indigo-600'
              : 'text-gray-500 hover:text-gray-700'
          "
          @click="changeTab(tab.id)"
        >
          <!-- Tab icons -->
          <template #prefix>
            <svg
              v-if="tab.icon === 'document-text'"
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else-if="tab.icon === 'download'"
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else-if="tab.icon === 'pencil-alt'"
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
              />
              <path
                fill-rule="evenodd"
                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else-if="tab.icon === 'chat'"
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
              />
              <path
                d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
              />
            </svg>
          </template>
          {{ tab.label }}
        </UiButton>
      </div>
    </div>

    <!-- Tab content -->
    <div class="p-6">
      <!-- Lesson content tab -->
      <div v-if="activeTab === 'content'" v-html="lessonContent" />

      <!-- Resources tab -->
      <div v-else-if="activeTab === 'resources'">
        <h3 class="text-lg font-medium mb-4">Lesson Resources</h3>
        <ul class="space-y-4">
          <li
            v-for="resource in resources"
            :key="resource.id"
            class="flex items-start p-4 border border-gray-100 rounded-lg hover:border-indigo-100 hover:bg-indigo-50 transition-colors"
          >
            <div
              class="flex-shrink-0 h-10 w-10 mt-1 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600"
            >
              <!-- PDF icon -->
              <svg
                v-if="resource.type === 'pdf'"
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
              <!-- Code icon -->
              <svg
                v-else-if="resource.type === 'code'"
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
              <!-- Exercise icon -->
              <svg
                v-else-if="resource.type === 'exercises'"
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
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                />
              </svg>
            </div>
            <div class="ml-4 flex-1">
              <h4 class="text-base font-medium">{{ resource.title }}</h4>
              <p class="text-sm text-gray-500 mb-2">
                {{ resource.description }}
              </p>
              <a
                :href="resource.url"
                class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium"
              >
                Download
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="ml-1 h-4 w-4"
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
          </li>
        </ul>
      </div>

      <!-- Notes tab -->
      <div v-else-if="activeTab === 'notes'">
        <div class="p-8 text-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-gray-300 mx-auto mb-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            />
          </svg>
          <h4 class="text-xl font-bold text-gray-700 mb-2">Take Notes</h4>
          <p class="text-gray-500 mb-4">
            Click the notes button in the bottom corner to open the notes panel
            and start taking notes for this lesson.
          </p>
          <UiButton
            variant="solid"
            color="primary"
            size="md"
            @click="
              changeTab('content');
              $emit('toggle-notes', true);
            "
          >
            <template #prefix>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                />
                <path
                  fill-rule="evenodd"
                  d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                  clip-rule="evenodd"
                />
              </svg>
            </template>
            Open Notes Panel
          </UiButton>
        </div>
      </div>

      <!-- Discussions tab -->
      <div v-else-if="activeTab === 'discussions'">
        <h3 class="text-lg font-medium mb-4">Lesson Discussions</h3>

        <!-- Discussion form -->
        <div class="mb-6 p-4 border border-gray-200 rounded-lg">
          <UiTextarea
            v-model="discussionText"
            placeholder="Ask a question or share your thoughts..."
            :rows="3"
          />
          <div class="mt-2 flex justify-end">
            <UiButton variant="solid" color="primary" size="md">
              Post
            </UiButton>
          </div>
        </div>

        <!-- Discussion threads -->
        <div class="space-y-6">
          <div
            v-for="thread in discussions"
            :key="thread.id"
            class="p-4 border border-gray-200 rounded-lg"
          >
            <div class="flex items-start">
              <img
                :src="thread.user.avatar"
                :alt="thread.user.name"
                class="w-10 h-10 rounded-full mr-3 object-cover"
              />
              <div class="flex-1">
                <div class="flex justify-between">
                  <div>
                    <h4 class="font-medium">{{ thread.user.name }}</h4>
                    <p class="text-sm text-gray-500">{{ thread.timestamp }}</p>
                  </div>
                  <UiButton variant="ghost" size="sm" color="gray">
                    <template #prefix>
                      <svg
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
                          d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                        />
                      </svg>
                    </template>
                  </UiButton>
                </div>
                <p class="mt-2">{{ thread.content }}</p>
                <div class="mt-3 flex items-center space-x-4">
                  <UiButton variant="ghost" size="sm" color="gray">
                    <template #prefix>
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M7 11l5-5m0 0l5 5m-5-5v12"
                        />
                      </svg>
                    </template>
                    Upvote ({{ thread.upvotes }})
                  </UiButton>
                  <UiButton variant="ghost" size="sm" color="gray">
                    <template #prefix>
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                        />
                      </svg>
                    </template>
                    Reply ({{ thread.replies.length }})
                  </UiButton>
                </div>
              </div>
            </div>

            <!-- Replies -->
            <div
              v-for="reply in thread.replies"
              :key="reply.id"
              class="ml-14 mt-4 pt-4 border-t border-gray-100"
            >
              <div class="flex items-start">
                <img
                  :src="reply.user.avatar"
                  :alt="reply.user.name"
                  class="w-9 h-9 rounded-full mr-3 object-cover"
                />
                <div class="flex-1">
                  <div class="flex items-center">
                    <h4 class="font-medium">{{ reply.user.name }}</h4>
                    <div
                      v-if="reply.user.role === 'Instructor'"
                      class="ml-2 text-xs text-white bg-indigo-600 rounded-full px-2 py-0.5 inline-block"
                    >
                      Instructor
                    </div>
                  </div>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ reply.timestamp }}
                  </p>
                  <p class="mt-2">{{ reply.content }}</p>
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
/* Tab transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
