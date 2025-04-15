<!-- components/ui/SupportChat.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

// Props
interface Props {
  brandName?: string;
  agentName?: string;
  agentAvatar?: string;
  brandColor?: string;
  position?: "bottom-right" | "bottom-left";
  greetingMessage?: string;
  responseTime?: string;
}

const props = withDefaults(defineProps<Props>(), {
  brandName: "LearnConsult",
  agentName: "Support Team",
  agentAvatar: "/api/placeholder/100/100",
  brandColor: "indigo",
  position: "bottom-right",
  greetingMessage: "Hello 👋 How can we help you today?",
  responseTime: "We typically reply within a few hours",
});

// State
const isOpen = ref(false);
const isMinimized = ref(false);
const message = ref("");
const messages = ref<
  Array<{
    id: number;
    content: string;
    sender: "user" | "agent";
    timestamp: Date;
    status?: "sending" | "sent" | "read";
  }>
>([]);
const isTyping = ref(false);
const chatContainerRef = ref<HTMLElement | null>(null);
const badgeCount = ref(0);
const hasNewMessages = ref(false);
const hasOpenedBefore = ref(false);
const showFurtherQuestionsPanel = ref(false);
const showMobileView = ref(false);

// Frequently asked questions
const commonQuestions = [
  "How do I reset my password?",
  "Where can I find my certificate?",
  "I need help with a payment issue",
  "I can't access my course materials",
  "How do I contact my instructor?",
  "When will I get a response?",
];

// Calculate dynamic colors based on brand color
const colorClasses = computed(() => {
  const color = props.brandColor;
  return {
    bgGradient: `bg-gradient-to-br from-${color}-500 to-${color}-600`,
    bgSolid: `bg-${color}-600`,
    bgLight: `bg-${color}-50`,
    bgMedium: `bg-${color}-100`,
    textColor: `text-${color}-600`,
    textLight: `text-${color}-500`,
    borderColor: `border-${color}-600`,
    hoverBg: `hover:bg-${color}-50`,
    focusRing: `focus:ring-${color}-500`,
    fillCurrent: `fill-${color}-600`,
    lighterBg: `bg-${color}-400/10`,
  };
});

// Calculated position classes
const positionClasses = computed(() => {
  if (props.position === "bottom-left") {
    return {
      button: "left-4 sm:left-6",
      window: "left-4 sm:left-6 origin-bottom-left",
      panel: "right-4 sm:right-auto sm:left-6",
    };
  }

  return {
    button: "right-4 sm:right-6",
    window: "right-4 sm:right-6 origin-bottom-right",
    panel: "right-4 sm:right-6",
  };
});

// Methods
const toggleChat = () => {
  // Handle the unread messages badge
  if (!isOpen.value && badgeCount.value > 0) {
    badgeCount.value = 0;
    hasNewMessages.value = false;
  }

  isOpen.value = !isOpen.value;

  if (isOpen.value) {
    isMinimized.value = false;
    hasOpenedBefore.value = true;
    showFurtherQuestionsPanel.value = false;

    // If this is the first time opening, show the typing indicator and send an automatic message
    if (!hasOpenedBefore.value) {
      simulateAgentResponse();
    }

    // Focus the input field when chat is opened
    setTimeout(() => {
      const inputField = document.getElementById("chat-input");
      if (inputField) {
        inputField.focus();
      }
    }, 300);
  }
};

const minimizeChat = () => {
  isMinimized.value = true;
};

const restoreChat = () => {
  isMinimized.value = false;
};

const sendMessage = () => {
  if (!message.value.trim()) return;

  // Add user message
  const userMessage = {
    id: Date.now(),
    content: message.value,
    sender: "user" as const,
    timestamp: new Date(),
    status: "sending" as const,
  };

  messages.value.push(userMessage);

  // Clear input and scroll to bottom
  message.value = "";
  scrollToBottom();

  // Update status after a delay
  setTimeout(() => {
    const messageIndex = messages.value.findIndex(
      (m) => m.id === userMessage.id
    );
    if (messageIndex !== -1) {
      messages.value[messageIndex].status = "sent";
    }
  }, 500);

  // Simulate agent typing response
  simulateAgentResponse();
};

const simulateAgentResponse = () => {
  // Show the typing indicator
  isTyping.value = true;
  scrollToBottom();

  // Simulate delay before response
  setTimeout(() => {
    isTyping.value = false;

    // Add agent response
    if (messages.value.length === 0) {
      // First message
      messages.value.push({
        id: Date.now(),
        content: props.greetingMessage,
        sender: "agent",
        timestamp: new Date(),
      });
    } else {
      // Random responses based on user message
      const responses = [
        "Thank you for your message! Our team will look into this shortly.",
        "I appreciate you reaching out. Let me check this for you and get back to you soon.",
        "Thanks for contacting support. We'll investigate and respond as soon as possible.",
        "Got it! We'll review your request and follow up with more information.",
        "Thanks for letting us know. A member of our team will assist you shortly.",
      ];

      const randomResponse =
        responses[Math.floor(Math.random() * responses.length)];

      messages.value.push({
        id: Date.now(),
        content: randomResponse,
        sender: "agent",
        timestamp: new Date(),
      });

      // Show a badge if the chat is closed
      if (!isOpen.value) {
        badgeCount.value += 1;
        hasNewMessages.value = true;
      }
    }

    scrollToBottom();

    // Show further questions panel after agent responds
    setTimeout(() => {
      showFurtherQuestionsPanel.value = true;
      scrollToBottom();
    }, 1000);
  }, 1500);
};

const useQuickQuestion = (question: string) => {
  message.value = question;
  sendMessage();
  showFurtherQuestionsPanel.value = false;
};

const scrollToBottom = () => {
  // Wait for the DOM to update
  setTimeout(() => {
    if (chatContainerRef.value) {
      chatContainerRef.value.scrollTop = chatContainerRef.value.scrollHeight;
    }
  }, 50);
};

const formatTime = (date: Date) => {
  return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
};

// Handle clicks outside to close the chat
const handleClickOutside = (event: MouseEvent) => {
  const chatWindow = document.getElementById("support-chat-window");
  const chatToggle = document.getElementById("support-chat-toggle");

  if (
    isOpen.value &&
    chatWindow &&
    !chatWindow.contains(event.target as Node) &&
    chatToggle &&
    !chatToggle.contains(event.target as Node)
  ) {
    isOpen.value = false;
  }
};

// Check if mobile view
const checkMobileView = () => {
  showMobileView.value = window.innerWidth < 640;
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  window.addEventListener("resize", checkMobileView);
  checkMobileView();

  // Add initial agent greeting if there are no messages
  if (messages.value.length === 0) {
    setTimeout(() => {
      if (!isOpen.value) {
        badgeCount.value = 1;
        hasNewMessages.value = true;
      }
    }, 5000);
  }
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
  window.removeEventListener("resize", checkMobileView);
});
</script>

<template>
  <div class="support-chat-container">
    <!-- Floating Button -->
    <button
      id="support-chat-toggle"
      @click="toggleChat"
      class="fixed bottom-4 z-50 flex items-center justify-center transition-all duration-300 ease-in-out shadow-lg"
      :class="[
        positionClasses.button,
        isOpen ? 'scale-0 opacity-0' : 'scale-100 opacity-100',
        !isOpen && hasNewMessages ? 'animate-bounce-gentle' : '',
        colorClasses.bgGradient,
        'rounded-full w-14 h-14 text-white',
      ]"
      aria-label="Open support chat"
    >
      <span class="sr-only">Open Support Chat</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-7 w-7"
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

      <!-- Badge counter -->
      <span
        v-if="badgeCount > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
      >
        {{ badgeCount }}
      </span>

      <!-- Pulse effect -->
      <span
        v-if="hasNewMessages"
        class="absolute inset-0 rounded-full animate-ping-slow opacity-25"
        :class="colorClasses.bgSolid"
      ></span>
    </button>

    <!-- Chat Window -->
    <div
      id="support-chat-window"
      v-show="isOpen"
      class="fixed z-50 bottom-4 sm:mb-0 transition-all duration-300 ease-in-out shadow-2xl w-full sm:w-96 max-h-[32rem] flex flex-col transform"
      :class="[
        positionClasses.window,
        isMinimized
          ? 'h-20'
          : showMobileView
          ? 'h-[calc(100%-2rem)]'
          : 'h-[32rem]',
        isOpen ? 'scale-100 opacity-100' : 'scale-90 opacity-0',
        'bg-white rounded-lg overflow-hidden',
      ]"
      :style="{
        maxHeight: showMobileView ? 'calc(100% - 2rem)' : '32rem',
        width: showMobileView ? 'calc(100% - 2rem)' : '',
      }"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between py-4 px-4 cursor-pointer"
        :class="colorClasses.bgGradient"
      >
        <div class="flex items-center">
          <img
            :src="agentAvatar"
            alt="Support Agent"
            class="w-10 h-10 rounded-full object-cover border-2 border-white"
          />
          <div class="ml-3 text-white">
            <h3 class="font-bold text-base">{{ brandName }} Support</h3>
            <p class="text-xs opacity-90">
              {{ isMinimized ? props.responseTime : agentName }}
            </p>
          </div>
        </div>
        <div class="flex space-x-1">
          <button
            v-if="!isMinimized"
            @click.stop="minimizeChat"
            class="text-white opacity-80 hover:opacity-100 p-1"
            aria-label="Minimize"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
          <button
            v-else
            @click.stop="restoreChat"
            class="text-white opacity-80 hover:opacity-100 p-1"
            aria-label="Restore"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
          <button
            @click.stop="toggleChat"
            class="text-white opacity-80 hover:opacity-100 p-1"
            aria-label="Close"
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
        </div>
      </div>

      <!-- Chat container (only visible when not minimized) -->
      <div v-if="!isMinimized" class="flex-grow flex flex-col">
        <!-- Messages area -->
        <div
          ref="chatContainerRef"
          class="flex-grow overflow-y-auto p-4 space-y-4"
          style="scroll-behavior: smooth"
        >
          <!-- Welcome message if no messages yet -->
          <div
            v-if="messages.length === 0 && !isTyping"
            class="flex flex-col items-center justify-center h-full text-center px-6"
          >
            <div
              class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
              :class="colorClasses.bgLight"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8"
                :class="colorClasses.textColor"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <h3 class="font-bold text-gray-700 text-lg mb-1">
              Welcome to {{ brandName }} Support
            </h3>
            <p class="text-gray-500 mb-4">{{ props.greetingMessage }}</p>
            <p class="text-sm text-gray-400">{{ props.responseTime }}</p>
          </div>

          <!-- Message bubbles -->
          <template v-for="(msg, index) in messages" :key="msg.id">
            <!-- Agent message -->
            <div
              v-if="msg.sender === 'agent'"
              class="flex items-start max-w-[85%]"
            >
              <img
                :src="agentAvatar"
                alt="Support Agent"
                class="w-8 h-8 rounded-full object-cover mr-2 mt-1"
              />
              <div>
                <div
                  class="p-3 rounded-lg rounded-tl-none shadow-sm"
                  :class="colorClasses.bgLight"
                >
                  <p class="text-gray-700 whitespace-pre-line">
                    {{ msg.content }}
                  </p>
                </div>
                <span class="text-xs text-gray-400 mt-1 inline-block">{{
                  formatTime(msg.timestamp)
                }}</span>
              </div>
            </div>

            <!-- User message -->
            <div
              v-else
              class="flex items-start flex-row-reverse max-w-[85%] ml-auto"
            >
              <div>
                <div
                  class="bg-gray-100 p-3 rounded-lg rounded-tr-none shadow-sm"
                >
                  <p class="text-gray-700 whitespace-pre-line">
                    {{ msg.content }}
                  </p>
                </div>
                <div class="flex items-center justify-end mt-1 space-x-1">
                  <span class="text-xs text-gray-400">{{
                    formatTime(msg.timestamp)
                  }}</span>
                  <span v-if="msg.status === 'sending'">
                    <svg
                      class="animate-spin h-3 w-3 text-gray-400"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                      ></circle>
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      ></path>
                    </svg>
                  </span>
                  <span v-else-if="msg.status === 'sent'">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </span>
                  <span v-else-if="msg.status === 'read'">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3 text-blue-500"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </template>

          <!-- Typing indicator -->
          <div v-if="isTyping" class="flex items-start max-w-[80%]">
            <img
              :src="agentAvatar"
              alt="Support Agent"
              class="w-8 h-8 rounded-full object-cover mr-2 mt-1"
            />
            <div
              class="p-3 rounded-lg rounded-tl-none shadow-sm"
              :class="colorClasses.bgLight"
            >
              <div class="flex space-x-1">
                <span
                  class="w-2 h-2 rounded-full bg-gray-400 animate-bounce"
                  style="animation-delay: 0ms"
                ></span>
                <span
                  class="w-2 h-2 rounded-full bg-gray-400 animate-bounce"
                  style="animation-delay: 200ms"
                ></span>
                <span
                  class="w-2 h-2 rounded-full bg-gray-400 animate-bounce"
                  style="animation-delay: 400ms"
                ></span>
              </div>
            </div>
          </div>

          <!-- Quick replies after agent message -->
          <div
            v-if="showFurtherQuestionsPanel && messages.length > 0"
            class="quick-replies py-2"
          >
            <p class="text-xs text-gray-500 mb-2">Common questions:</p>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="(question, index) in commonQuestions.slice(0, 3)"
                :key="index"
                @click="useQuickQuestion(question)"
                class="text-xs px-3 py-1.5 rounded-full transition-colors duration-200 text-gray-600 bg-gray-100 hover:bg-gray-200"
              >
                {{ question }}
              </button>
            </div>
          </div>
        </div>

        <!-- Input area -->
        <div
          class="p-3 border-t border-gray-200 flex items-center transition-all duration-300"
          :class="isTyping ? 'opacity-50' : 'opacity-100'"
        >
          <input
            id="chat-input"
            v-model="message"
            type="text"
            placeholder="Type your message here..."
            class="flex-grow px-4 py-2 border rounded-full focus:outline-none focus:ring-2 transition-all duration-200"
            :class="[colorClasses.focusRing, 'border-gray-300']"
            @keyup.enter="sendMessage"
            :disabled="isTyping"
          />
          <button
            @click="sendMessage"
            :disabled="isTyping || !message.trim()"
            class="ml-2 p-2 rounded-full transition-all duration-200 focus:outline-none focus:ring-2"
            :class="[
              colorClasses.bgSolid,
              colorClasses.focusRing,
              'text-white',
              !message.trim() || isTyping
                ? 'opacity-50 cursor-not-allowed'
                : 'hover:shadow-lg',
            ]"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes ping-slow {
  0% {
    transform: scale(1);
    opacity: 0.25;
  }
  50% {
    opacity: 0.12;
  }
  100% {
    transform: scale(1.8);
    opacity: 0;
  }
}

@keyframes bounce-gentle {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.animate-ping-slow {
  animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.animate-bounce-gentle {
  animation: bounce-gentle 2s ease-in-out infinite;
}
</style>
