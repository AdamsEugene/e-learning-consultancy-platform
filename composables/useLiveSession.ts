import { ref, computed } from "vue";
import type {
  LiveSession,
  ChatMessage,
  Poll,
  Resource,
  Participant,
} from "~/types/live-session";

export const useLiveSession = (courseId: number, sessionId: number) => {
  // State
  const session = ref<LiveSession | null>(null);
  const loading = ref(true);
  const error = ref(false);
  const connected = ref(false);
  const connecting = ref(false);
  const chatMessages = ref<ChatMessage[]>([]);
  const newMessage = ref("");
  const activeTab = ref<"chat" | "participants" | "polls" | "resources">(
    "chat"
  );
  const activePoll = ref<Poll | null>(null);
  const resources = ref<Resource[]>([]);
  const participants = ref<Participant[]>([]);
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
        id: sessionId,
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
        courseId: courseId,
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
      const randomName =
        userNames[Math.floor(Math.random() * userNames.length)];
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

  return {
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
    isMobileView,

    // Computed
    isLive,
    hasEnded,
    canJoin,

    // Methods
    fetchSessionData,
    joinSession,
    leaveSession,
    sendMessage,
    votePoll,
    formatTime,
    getVotePercentage,
    formatSessionTime,
    formatSessionDate,
    scrollToLatestMessage,
    checkMobileView,
  };
};
