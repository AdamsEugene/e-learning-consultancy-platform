<!-- pages/courses/[id]/learn/live/[sessionId].vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

// Types
interface LiveSession {
  id: number;
  title: string;
  description: string;
  startTime: string;
  endTime: string;
  instructorId: number;
  instructorName: string;
  instructorAvatar: string;
  status: "scheduled" | "live" | "ended";
  attendees: number;
  courseId: number;
  lessonId?: number;
  roomUrl?: string;
  recordingUrl?: string;
}

interface ChatMessage {
  id: number;
  userId: number;
  userName: string;
  userAvatar: string;
  message: string;
  timestamp: number;
  isInstructor: boolean;
}

interface PollOption {
  id: number;
  text: string;
  votes: number;
}

interface Poll {
  id: number;
  question: string;
  options: PollOption[];
  totalVotes: number;
  userVoted: boolean;
  createdAt: number;
}

// Route params
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id));
const sessionId = computed(() => Number(route.params.sessionId));

// State
const session = ref<LiveSession | null>(null);
const loading = ref(true);
const error = ref(false);
const connected = ref(false);
const connecting = ref(false);
const chatMessages = ref<ChatMessage[]>([]);
const newMessage = ref("");
const activeTab = ref<"chat" | "participants" | "polls" | "resources">("chat");
const activePoll = ref<Poll | null>(null);
const resources = ref<
  { id: number; title: string; url: string; type: string }[]
>([]);
const participants = ref<
  { id: number; name: string; avatar: string; isInstructor: boolean }[]
>([]);
const showInfo = ref(true);
const isMobileView = ref(false);

// Mock API call to fetch session data
const fetchSessionData = async () => {
  loading.value = true;
  error.value = false;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    session.value = {
      id: sessionId.value,
      title: "Advanced CSS Layout Techniques",
      description:
        "In this live session, we'll explore advanced CSS layout techniques including CSS Grid, Flexbox, and modern responsive design patterns. We'll build several real-world examples together and address common challenges.",
      startTime: "2023-11-15T18:00:00Z",
      endTime: "2023-11-15T19:30:00Z",
      instructorId: 123,
      instructorName: "Sarah Johnson",
      instructorAvatar: "/images/instructors/i1.jpg",
      status: "live",
      attendees: 87,
      courseId: courseId.value,
      lessonId: 305,
      roomUrl:
        "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
    };

    // Mock chat messages
    chatMessages.value = [
      {
        id: 1,
        userId: 456,
        userName: "System",
        userAvatar: "/images/instructors/i1.jpg",
        message:
          "Welcome to the live session! The instructor will begin shortly.",
        timestamp: Date.now() - 600000,
        isInstructor: false,
      },
      {
        id: 2,
        userId: 123,
        userName: "Sarah Johnson",
        userAvatar: "/images/instructors/i1.jpg",
        message:
          "Hello everyone! I'm excited to share these advanced CSS techniques with you today. Please feel free to ask questions as we go along.",
        timestamp: Date.now() - 300000,
        isInstructor: true,
      },
      {
        id: 3,
        userId: 789,
        userName: "Alex Chen",
        userAvatar: "/images/instructors/i1.jpg",
        message:
          "Looking forward to learning about CSS Grid. I've been struggling with complex layouts.",
        timestamp: Date.now() - 240000,
        isInstructor: false,
      },
      {
        id: 4,
        userId: 123,
        userName: "Sarah Johnson",
        userAvatar: "/images/instructors/i1.jpg",
        message:
          "We'll definitely cover that, Alex! I'll share some practical examples that you can use in your projects.",
        timestamp: Date.now() - 180000,
        isInstructor: true,
      },
    ];

    // Mock resources
    resources.value = [
      {
        id: 1,
        title: "Session Slides",
        url: "#",
        type: "pdf",
      },
      {
        id: 2,
        title: "CSS Grid Cheat Sheet",
        url: "#",
        type: "pdf",
      },
      {
        id: 3,
        title: "Code Examples",
        url: "#",
        type: "github",
      },
    ];

    // Mock poll
    activePoll.value = {
      id: 1,
      question: "Which CSS layout technique do you find most challenging?",
      options: [
        { id: 1, text: "Flexbox", votes: 12 },
        { id: 2, text: "CSS Grid", votes: 24 },
        { id: 3, text: "Positioning (absolute, relative, etc.)", votes: 35 },
        { id: 4, text: "Multi-column layouts", votes: 8 },
      ],
      totalVotes: 79,
      userVoted: false,
      createdAt: Date.now() - 120000,
    };

    // Mock participants
    participants.value = [
      {
        id: 123,
        name: "Sarah Johnson",
        avatar: "/images/instructors/i1.jpg",
        isInstructor: true,
      },
    ];

    // Add 40 mock participants
    for (let i = 1; i <= 40; i++) {
      participants.value.push({
        id: 1000 + i,
        name: `Student ${i}`,
        avatar: `/images/instructors/i1.jpg`,
        isInstructor: false,
      });
    }

    checkMobileView();
  } catch (err) {
    console.error("Error fetching session data:", err);
    error.value = true;
  } finally {
    loading.value = false;
  }
};

// Join the live session
const joinSession = async () => {
  if (!session.value || connected.value) return;

  connecting.value = true;

  try {
    // Simulate connection process
    await new Promise((resolve) => setTimeout(resolve, 2000));

    connected.value = true;
    showInfo.value = false;

    // Add system message
    chatMessages.value.push({
      id: Date.now(),
      userId: 0,
      userName: "System",
      userAvatar: "/images/instructors/i1.jpg",
      message: "You have joined the live session.",
      timestamp: Date.now(),
      isInstructor: false,
    });

    // Start simulating new messages
    startMessageSimulation();
  } catch (error) {
    console.error("Failed to connect:", error);
  } finally {
    connecting.value = false;
  }
};

// Leave the session
const leaveSession = () => {
  connected.value = false;
  showInfo.value = true;
  stopMessageSimulation();

  // Add system message
  if (chatMessages.value.length > 0) {
    chatMessages.value.push({
      id: Date.now(),
      userId: 0,
      userName: "System",
      userAvatar: "/images/instructors/i1.jpg",
      message: "You have left the live session.",
      timestamp: Date.now(),
      isInstructor: false,
    });
  }
};

// Send a chat message
const sendMessage = () => {
  if (!newMessage.value.trim() || !connected.value) return;

  // Add user message
  chatMessages.value.push({
    id: Date.now(),
    userId: 999, // Current user ID (mock)
    userName: "You", // Current user name
    userAvatar: "/images/instructors/i1.jpg",
    message: newMessage.value.trim(),
    timestamp: Date.now(),
    isInstructor: false,
  });

  // Clear input
  newMessage.value = "";

  // Auto-scroll to latest message
  setTimeout(scrollToLatestMessage, 50);
};

// Vote in a poll
const votePoll = (optionId: number) => {
  if (!activePoll.value || activePoll.value.userVoted) return;

  // Update poll data
  activePoll.value.options = activePoll.value.options.map((option) => {
    if (option.id === optionId) {
      return { ...option, votes: option.votes + 1 };
    }
    return option;
  });

  activePoll.value.totalVotes++;
  activePoll.value.userVoted = true;
};

// Format timestamp
const formatTime = (timestamp: number): string => {
  return new Date(timestamp).toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Get percentage for poll options
const getVotePercentage = (votes: number, total: number): number => {
  return Math.round((votes / Math.max(total, 1)) * 100);
};

// Format session time
const formatSessionTime = (dateString: string): string => {
  return new Date(dateString).toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });
};

// Format session date
const formatSessionDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString([], {
    weekday: "long",
    month: "long",
    day: "numeric",
    year: "numeric",
  });
};

// Check if session is currently live
const isLive = computed(() => {
  if (!session.value) return false;
  return session.value.status === "live";
});

// Check if session has ended
const hasEnded = computed(() => {
  if (!session.value) return false;
  return session.value.status === "ended";
});

// Check if the student can join the session
const canJoin = computed(() => {
  if (!session.value) return false;
  return session.value.status === "live" && !connected.value;
});

// Back to course
const backToCourse = () => {
  router.push(`/courses/${courseId.value}/learn`);
};

// Scroll to the latest message
const scrollToLatestMessage = () => {
  const chatContainer = document.getElementById("chat-messages");
  if (chatContainer) {
    chatContainer.scrollTop = chatContainer.scrollHeight;
  }
};

// Message simulation (for demo purposes)
let messageSimulationInterval: number | undefined;

const startMessageSimulation = () => {
  // Clear any existing interval
  if (messageSimulationInterval) {
    clearInterval(messageSimulationInterval);
  }

  // Set up new interval
  messageSimulationInterval = window.setInterval(() => {
    // Only 20% chance of new message to prevent spam
    if (Math.random() > 0.8) {
      simulateNewMessage();
    }
  }, 15000); // Every 15 seconds
};

const stopMessageSimulation = () => {
  if (messageSimulationInterval) {
    clearInterval(messageSimulationInterval);
    messageSimulationInterval = undefined;
  }
};

const simulateNewMessage = () => {
  // Sample messages to randomly choose from
  const sampleMessages = [
    "Could you explain the difference between grid-template-columns and grid-auto-columns?",
    "How do you handle browser compatibility with these newer CSS features?",
    "Is it better to use Flexbox or Grid for responsive designs?",
    "Can you show an example of a nested grid?",
    "What's the best way to create a masonry layout with CSS Grid?",
    "How do media queries work with Grid layouts?",
    "I'm having trouble with the align-items property in Flexbox. Any tips?",
    "Are there performance concerns when using these modern CSS techniques?",
    "This is really helpful, thank you!",
    "Could you share that code example again? I missed it.",
  ];

  // Sample instructor responses
  const instructorResponses = [
    "Great question! Let me explain that in more detail...",
    "That's something many developers struggle with. The key is to understand...",
    "Let me show you a practical example of how this works...",
    "I'd recommend using Grid for that specific use case because...",
    "Yes, browser support is excellent now, but you might want to consider...",
    "That's a common misconception. Actually, the way it works is...",
    "Let's break that down step by step...",
    "I'll cover that in more detail in a few minutes!",
    "Does anyone else have experience with this issue? Feel free to share.",
    "Excellent point! I'd like to add that...",
  ];

  // Random user names
  const userNames = [
    "Alex Chen",
    "Taylor Kim",
    "Jordan Smith",
    "Casey Jones",
    "Morgan Miller",
    "Jamie Wilson",
    "Avery Thomas",
    "Riley Jackson",
  ];

  // Decide if this will be a student or instructor message
  const isInstructorMessage = Math.random() < 0.3; // 30% chance of instructor message

  if (isInstructorMessage) {
    // Add instructor message
    chatMessages.value.push({
      id: Date.now(),
      userId: 123,
      userName: "Sarah Johnson",
      userAvatar: "/images/instructors/i1.jpg",
      message:
        instructorResponses[
          Math.floor(Math.random() * instructorResponses.length)
        ],
      timestamp: Date.now(),
      isInstructor: true,
    });
  } else {
    // Add student message
    const randomName = userNames[Math.floor(Math.random() * userNames.length)];
    chatMessages.value.push({
      id: Date.now(),
      userId: Math.floor(Math.random() * 1000) + 1000,
      userName: randomName,
      userAvatar: "/images/instructors/i1.jpg",
      message:
        sampleMessages[Math.floor(Math.random() * sampleMessages.length)],
      timestamp: Date.now(),
      isInstructor: false,
    });
  }

  // Auto-scroll to latest message
  setTimeout(scrollToLatestMessage, 50);
};

// Check if we're on mobile view
const checkMobileView = () => {
  isMobileView.value = window.innerWidth < 768;
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
  stopMessageSimulation();

  // Ensure we disconnect from the session when leaving
  if (connected.value) {
    leaveSession();
  }
});
</script>

<template>
  <div class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header
      class="bg-white border-b border-gray-200 py-2 px-4 shadow-sm sticky top-0 z-10"
    >
      <div class="container mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <!-- Back to course button -->
          <button
            class="text-gray-600 hover:text-indigo-600 transition-colors flex items-center"
            @click="backToCourse"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            Back to Course
          </button>

          <!-- Session title -->
          <span class="text-lg font-medium truncate hidden md:inline">
            {{ session?.title }}
          </span>
        </div>

        <div class="flex items-center space-x-2">
          <!-- Status indicator -->
          <div v-if="session" class="flex items-center">
            <span
              v-if="isLive"
              class="flex items-center text-green-600 bg-green-100 px-2 py-1 rounded-full text-xs font-medium"
            >
              <span
                class="animate-pulse inline-block h-2 w-2 rounded-full bg-green-600 mr-1"
              />
              Live Now
            </span>
            <span
              v-else-if="hasEnded"
              class="flex items-center text-gray-600 bg-gray-100 px-2 py-1 rounded-full text-xs font-medium"
            >
              Ended
            </span>
            <span
              v-else
              class="flex items-center text-orange-600 bg-orange-100 px-2 py-1 rounded-full text-xs font-medium"
            >
              Scheduled
            </span>
          </div>

          <!-- Join/Leave session button -->
          <button
            v-if="canJoin && !connecting"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors"
            @click="joinSession"
          >
            Join Session
          </button>

          <button
            v-else-if="connecting"
            class="bg-indigo-400 text-white px-3 py-1 rounded-lg text-sm font-medium cursor-wait"
          >
            <span class="animate-pulse">Connecting...</span>
          </button>

          <button
            v-else-if="connected"
            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm font-medium transition-colors"
            @click="leaveSession"
          >
            Leave Session
          </button>
        </div>
      </div>
    </header>

    <!-- Loading state -->
    <div v-if="loading" class="flex-grow flex items-center justify-center">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      />
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="flex-grow flex items-center justify-center">
      <div class="text-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-16 w-16 text-red-500 mx-auto mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
        <h3 class="text-xl font-bold mb-2">Failed to load session</h3>
        <p class="text-gray-600 mb-4">
          There was an error loading the live session. Please try again.
        </p>
        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          @click="fetchSessionData"
        >
          Retry
        </button>
      </div>
    </div>

    <!-- Session Information (shown before joining) -->
    <div v-else-if="showInfo" class="container mx-auto px-4 py-8 flex-grow">
      <div
        class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden"
      >
        <div class="md:flex">
          <div class="md:flex-shrink-0">
            <img
              class="h-48 w-full object-cover md:w-48"
              src="/images/instructors/i1.jpg"
              alt="Live Session"
            >
          </div>
          <div class="p-8">
            <div class="flex items-center">
              <div
                class="uppercase tracking-wide text-sm text-indigo-500 font-semibold"
              >
                Live Session
              </div>
              <div
                v-if="isLive"
                class="ml-2 flex items-center text-green-600 bg-green-100 px-2 py-0.5 rounded-full text-xs font-medium"
              >
                <span
                  class="animate-pulse inline-block h-2 w-2 rounded-full bg-green-600 mr-1"
                />
                Live Now
              </div>
            </div>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">
              {{ session?.title }}
            </h1>
            <p class="mt-2 text-gray-600">{{ session?.description }}</p>

            <div class="mt-4 flex items-center">
              <img
                class="h-10 w-10 rounded-full mr-2"
                :src="session?.instructorAvatar"
                alt="Instructor"
              >
              <div>
                <p class="text-sm font-medium text-gray-900">
                  {{ session?.instructorName }}
                </p>
                <p class="text-sm text-gray-500">Instructor</p>
              </div>
            </div>

            <div
              class="mt-4 flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4"
            >
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
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
                <span class="text-gray-600">{{
                  formatSessionDate(session?.startTime || "")
                }}</span>
              </div>
              <div class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 mr-1"
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
                <span class="text-gray-600">
                  {{ formatSessionTime(session?.startTime || "") }} -
                  {{ formatSessionTime(session?.endTime || "") }}
                </span>
              </div>
            </div>

            <div class="flex items-center mt-6">
              <span class="text-gray-600 mr-2"
                >{{ session?.attendees }} people attending</span
              >
              <button
                v-if="canJoin && !connecting"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                @click="joinSession"
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
                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
                Join Live Session
              </button>

              <button
                v-else-if="connecting"
                class="bg-indigo-400 text-white px-4 py-2 rounded-lg font-medium cursor-wait inline-flex items-center"
              >
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
                Connecting...
              </button>

              <button
                v-else-if="hasEnded"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center"
                @click="backToCourse"
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
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                Watch Recording
              </button>

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
    </div>

    <!-- Live Session Interface -->
    <div
      v-else-if="connected"
      class="flex-grow flex flex-col md:flex-row overflow-hidden"
    >
      <!-- Main video area -->
      <div class="flex-grow flex flex-col overflow-hidden">
        <!-- Video player -->
        <div class="bg-black relative">
          <video
            v-if="session?.roomUrl"
            :src="session.roomUrl"
            controls
            autoplay
            class="w-full aspect-video"
          />

          <!-- Video overlay controls (for mobile) -->
          <div class="absolute bottom-4 left-4 md:hidden flex space-x-2">
            <button
              class="bg-gray-800 bg-opacity-70 text-white p-2 rounded-full hover:bg-opacity-90"
            >
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
                  d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 017.072 0m-9.9-2.828a9 9 0 0112.728 0"
                />
              </svg>
            </button>
            <button
              class="bg-gray-800 bg-opacity-70 text-white p-2 rounded-full hover:bg-opacity-90"
            >
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
                  d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Session info under video (mobile only) -->
        <div class="md:hidden bg-white border-b border-gray-200 p-4">
          <h2 class="font-bold text-lg">{{ session?.title }}</h2>
          <div class="flex items-center text-sm text-gray-600 mt-1">
            <span class="flex items-center">
              <img
                :src="session?.instructorAvatar"
                alt="Instructor"
                class="w-4 h-4 rounded-full mr-1"
              >
              {{ session?.instructorName }}
            </span>
            <span class="mx-2">•</span>
            <span>{{ session?.attendees }} attending</span>
          </div>
        </div>

        <!-- Mobile tab navigation -->
        <div class="md:hidden bg-white border-b border-gray-200">
          <div class="flex overflow-x-auto">
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'chat'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500'
              "
              @click="activeTab = 'chat'"
            >
              Chat
            </button>
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'participants'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500'
              "
              @click="activeTab = 'participants'"
            >
              Participants
            </button>
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'polls'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500'
              "
              @click="activeTab = 'polls'"
            >
              Polls
            </button>
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'resources'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500'
              "
              @click="activeTab = 'resources'"
            >
              Resources
            </button>
          </div>
        </div>

        <!-- Mobile tab content -->
        <div class="md:hidden flex-grow overflow-y-auto bg-gray-50">
          <!-- Chat tab -->
          <div v-if="activeTab === 'chat'" class="h-full flex flex-col">
            <!-- Chat messages -->
            <div
              id="chat-messages"
              class="flex-grow overflow-y-auto p-4 space-y-4"
            >
              <div
                v-for="message in chatMessages"
                :key="message.id"
                :class="['flex', message.userId === 0 ? 'justify-center' : '']"
              >
                <!-- System message -->
                <div
                  v-if="message.userId === 0"
                  class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-sm inline-block"
                >
                  {{ message.message }}
                </div>

                <!-- User message -->
                <div v-else class="flex max-w-xs sm:max-w-md">
                  <img
                    :src="message.userAvatar"
                    class="h-8 w-8 rounded-full mr-2"
                    :alt="message.userName"
                  >
                  <div>
                    <div class="flex items-center">
                      <span class="font-medium text-sm">{{
                        message.userName
                      }}</span>
                      <span
                        v-if="message.isInstructor"
                        class="ml-1 text-xs bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded-full"
                      >
                        Instructor
                      </span>
                    </div>
                    <div class="bg-white rounded-lg p-2 mt-1 shadow-sm">
                      <p class="text-gray-800">{{ message.message }}</p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                      {{ formatTime(message.timestamp) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat input -->
            <div class="p-4 bg-white border-t border-gray-200">
              <form class="flex" @submit.prevent="sendMessage">
                <input
                  v-model="newMessage"
                  type="text"
                  placeholder="Type your message..."
                  class="flex-grow rounded-l-lg border-l border-t border-b border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2"
                >
                <button
                  type="submit"
                  :disabled="!newMessage.trim()"
                  class="bg-indigo-600 text-white rounded-r-lg px-4 py-2 disabled:bg-indigo-300"
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
              </form>
            </div>
          </div>

          <!-- Participants tab -->
          <div v-else-if="activeTab === 'participants'" class="p-4">
            <h3 class="font-medium text-gray-900 mb-4">
              Participants ({{ participants.length }})
            </h3>
            <div class="space-y-3">
              <div
                v-for="participant in participants"
                :key="participant.id"
                class="flex items-center"
              >
                <img
                  :src="participant.avatar"
                  alt="User avatar"
                  class="w-8 h-8 rounded-full mr-3"
                >
                <div>
                  <div class="flex items-center">
                    <span class="font-medium">{{ participant.name }}</span>
                    <span
                      v-if="participant.isInstructor"
                      class="ml-2 text-xs bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded-full"
                    >
                      Instructor
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Polls tab -->
          <div v-else-if="activeTab === 'polls'" class="p-4">
            <div
              v-if="activePoll"
              class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
            >
              <h3 class="font-medium text-gray-900 mb-2">
                {{ activePoll.question }}
              </h3>
              <div class="space-y-2 mb-4">
                <div
                  v-for="option in activePoll.options"
                  :key="option.id"
                  class="border rounded-lg p-3 cursor-pointer transition-colors"
                  :class="[
                    activePoll.userVoted
                      ? 'border-gray-200'
                      : 'border-gray-300 hover:border-indigo-500',
                  ]"
                  @click="!activePoll.userVoted && votePoll(option.id)"
                >
                  <div class="flex justify-between mb-1">
                    <span>{{ option.text }}</span>
                    <span>{{ option.votes }} votes</span>
                  </div>
                  <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div
                      class="h-full rounded-full"
                      :class="
                        activePoll.userVoted ? 'bg-indigo-600' : 'bg-gray-400'
                      "
                      :style="{
                        width: `${getVotePercentage(
                          option.votes,
                          activePoll.totalVotes
                        )}%`,
                      }"
                    />
                  </div>
                  <div class="mt-1 text-xs text-gray-500 text-right">
                    {{
                      getVotePercentage(option.votes, activePoll.totalVotes)
                    }}%
                  </div>
                </div>
              </div>
              <div class="text-sm text-gray-500">
                Total votes: {{ activePoll.totalVotes }}
              </div>
            </div>

            <div v-else class="text-center py-8">
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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
              <p class="text-gray-500">No active polls at the moment.</p>
            </div>
          </div>

          <!-- Resources tab -->
          <div v-else-if="activeTab === 'resources'" class="p-4">
            <h3 class="font-medium text-gray-900 mb-4">Resources</h3>
            <div class="space-y-3">
              <a
                v-for="resource in resources"
                :key="resource.id"
                :href="resource.url"
                class="block bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-500 transition-colors"
              >
                <div class="flex items-start">
                  <div
                    class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                    :class="{
                      'bg-red-100 text-red-600': resource.type === 'pdf',
                      'bg-green-100 text-green-600':
                        resource.type === 'spreadsheet',
                      'bg-blue-100 text-blue-600': resource.type === 'document',
                      'bg-gray-100 text-gray-600': resource.type === 'github',
                    }"
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

                    <!-- GitHub icon -->
                    <svg
                      v-else-if="resource.type === 'github'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-900">
                      {{ resource.title }}
                    </h4>
                    <p class="text-sm text-gray-500">
                      {{ resource.type.toUpperCase() }} document
                    </p>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar for desktop view -->
      <div
        class="hidden md:block w-80 lg:w-96 border-l border-gray-200 bg-white flex-shrink-0 overflow-hidden"
      >
        <!-- Tabs navigation -->
        <div class="border-b border-gray-200">
          <div class="flex">
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'chat'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700'
              "
              @click="activeTab = 'chat'"
            >
              Chat
            </button>
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'participants'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700'
              "
              @click="activeTab = 'participants'"
            >
              Participants
            </button>
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'polls'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700'
              "
              @click="activeTab = 'polls'"
            >
              Polls
            </button>
            <button
              class="px-4 py-3 text-center flex-1 text-sm font-medium"
              :class="
                activeTab === 'resources'
                  ? 'border-b-2 border-indigo-600 text-indigo-600'
                  : 'text-gray-500 hover:text-gray-700'
              "
              @click="activeTab = 'resources'"
            >
              Resources
            </button>
          </div>
        </div>

        <!-- Tab content -->
        <div class="h-[calc(100vh-8.5rem)] flex flex-col">
          <!-- Chat tab -->
          <div v-if="activeTab === 'chat'" class="h-full flex flex-col">
            <!-- Chat messages -->
            <div
              id="chat-messages"
              class="flex-grow overflow-y-auto p-4 space-y-4"
            >
              <div
                v-for="message in chatMessages"
                :key="message.id"
                :class="['flex', message.userId === 0 ? 'justify-center' : '']"
              >
                <!-- System message -->
                <div
                  v-if="message.userId === 0"
                  class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-sm inline-block"
                >
                  {{ message.message }}
                </div>

                <!-- User message -->
                <div v-else class="flex max-w-xs">
                  <img
                    :src="message.userAvatar"
                    class="h-8 w-8 rounded-full mr-2"
                    :alt="message.userName"
                  >
                  <div>
                    <div class="flex items-center">
                      <span class="font-medium text-sm">{{
                        message.userName
                      }}</span>
                      <span
                        v-if="message.isInstructor"
                        class="ml-1 text-xs bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded-full"
                      >
                        Instructor
                      </span>
                    </div>
                    <div class="bg-white rounded-lg p-2 mt-1 shadow-sm">
                      <p class="text-gray-800">{{ message.message }}</p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                      {{ formatTime(message.timestamp) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat input -->
            <div class="p-4 bg-white border-t border-gray-200">
              <form class="flex" @submit.prevent="sendMessage">
                <input
                  v-model="newMessage"
                  type="text"
                  placeholder="Type your message..."
                  class="flex-grow rounded-l-lg border-l border-t border-b border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2"
                >
                <button
                  type="submit"
                  :disabled="!newMessage.trim()"
                  class="bg-indigo-600 text-white rounded-r-lg px-4 py-2 disabled:bg-indigo-300"
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
              </form>
            </div>
          </div>

          <!-- Participants tab -->
          <div
            v-else-if="activeTab === 'participants'"
            class="h-full overflow-y-auto p-4"
          >
            <h3 class="font-medium text-gray-900 mb-4">
              Participants ({{ participants.length }})
            </h3>
            <div class="space-y-3">
              <div
                v-for="participant in participants"
                :key="participant.id"
                class="flex items-center"
              >
                <img
                  :src="participant.avatar"
                  alt="User avatar"
                  class="w-8 h-8 rounded-full mr-3"
                >
                <div>
                  <div class="flex items-center">
                    <span class="font-medium">{{ participant.name }}</span>
                    <span
                      v-if="participant.isInstructor"
                      class="ml-2 text-xs bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded-full"
                    >
                      Instructor
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Polls tab -->
          <div
            v-else-if="activeTab === 'polls'"
            class="h-full overflow-y-auto p-4"
          >
            <div
              v-if="activePoll"
              class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
            >
              <h3 class="font-medium text-gray-900 mb-2">
                {{ activePoll.question }}
              </h3>
              <div class="space-y-2 mb-4">
                <div
                  v-for="option in activePoll.options"
                  :key="option.id"
                  class="border rounded-lg p-3 cursor-pointer transition-colors"
                  :class="[
                    activePoll.userVoted
                      ? 'border-gray-200'
                      : 'border-gray-300 hover:border-indigo-500',
                  ]"
                  @click="!activePoll.userVoted && votePoll(option.id)"
                >
                  <div class="flex justify-between mb-1">
                    <span>{{ option.text }}</span>
                    <span>{{ option.votes }} votes</span>
                  </div>
                  <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div
                      class="h-full rounded-full"
                      :class="
                        activePoll.userVoted ? 'bg-indigo-600' : 'bg-gray-400'
                      "
                      :style="{
                        width: `${getVotePercentage(
                          option.votes,
                          activePoll.totalVotes
                        )}%`,
                      }"
                    />
                  </div>
                  <div class="mt-1 text-xs text-gray-500 text-right">
                    {{
                      getVotePercentage(option.votes, activePoll.totalVotes)
                    }}%
                  </div>
                </div>
              </div>
              <div class="text-sm text-gray-500">
                Total votes: {{ activePoll.totalVotes }}
              </div>
            </div>

            <div v-else class="text-center py-8">
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
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
              <p class="text-gray-500">No active polls at the moment.</p>
            </div>
          </div>

          <!-- Resources tab -->
          <div
            v-else-if="activeTab === 'resources'"
            class="h-full overflow-y-auto p-4"
          >
            <h3 class="font-medium text-gray-900 mb-4">Resources</h3>
            <div class="space-y-3">
              <a
                v-for="resource in resources"
                :key="resource.id"
                :href="resource.url"
                class="block bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-500 transition-colors"
              >
                <div class="flex items-start">
                  <div
                    class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                    :class="{
                      'bg-red-100 text-red-600': resource.type === 'pdf',
                      'bg-green-100 text-green-600':
                        resource.type === 'spreadsheet',
                      'bg-blue-100 text-blue-600': resource.type === 'document',
                      'bg-gray-100 text-gray-600': resource.type === 'github',
                    }"
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

                    <!-- GitHub icon -->
                    <svg
                      v-else-if="resource.type === 'github'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                      />
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-900">
                      {{ resource.title }}
                    </h4>
                    <p class="text-sm text-gray-500">
                      {{ resource.type.toUpperCase() }} document
                    </p>
                  </div>
                </div>
              </a>
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
