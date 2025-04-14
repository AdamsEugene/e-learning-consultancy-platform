<!-- pages/dashboard/messages.vue -->
<script setup lang="ts">
import type { User, Conversation, Message } from "~/types/messaging";

// Set page layout
definePageMeta({
  layout: "dashboard",
});

// States
const conversations = ref<Conversation[]>([]);
const activeConversation = ref<Conversation | null>(null);
const messages = ref<Message[]>([]);
const isLoading = ref(true);
const conversationsLoading = ref(true);
const messagesLoading = ref(false);
const showUserInfo = ref(false);
const showNewMessageDialog = ref(false);
const availableUsers = ref<User[]>([]);
const isMobileView = ref(false);
const showConversationList = ref(true);

// Current user (you)
const currentUser = ref<User>({
  id: 1,
  name: "Emily Johnson",
  avatar: "/images/instructors/i1.jpg",
  role: "student",
  online: true,
});

// Animation flags
const isLoaded = ref(false);
const isMessageSent = ref(false);

// Mock conversation data
const mockConversations: Conversation[] = [
  {
    id: 101,
    participants: [
      currentUser.value,
      {
        id: 201,
        name: "Alex Johnson",
        avatar: "/images/instructors/i1.jpg",
        role: "instructor",
        online: true,
      },
    ],
    lastMessage: {
      id: 10001,
      senderId: 201,
      receiverId: 1,
      content:
        "Great progress on your latest assignment! Let me know if you have any questions about the feedback.",
      timestamp: "2023-10-22T14:30:00",
      read: false,
    },
    unreadCount: 3,
    isPinned: true,
  },
  {
    id: 102,
    participants: [
      currentUser.value,
      {
        id: 202,
        name: "Sarah Chen",
        avatar: "/images/instructors/i1.jpg",
        role: "instructor",
        online: false,
        lastActive: "2023-10-22T09:45:00",
      },
    ],
    lastMessage: {
      id: 10002,
      senderId: 1,
      receiverId: 202,
      content:
        "Thank you for the detailed feedback on my design project. I'll work on the suggested improvements.",
      timestamp: "2023-10-21T16:20:00",
      read: true,
    },
    unreadCount: 0,
  },
  {
    id: 103,
    participants: [
      currentUser.value,
      {
        id: 203,
        name: "Michael Brown",
        avatar: "/images/instructors/i1.jpg",
        role: "instructor",
        online: true,
      },
    ],
    lastMessage: {
      id: 10003,
      senderId: 203,
      receiverId: 1,
      content:
        "Don't forget about our algorithm study group meeting tomorrow at 7 PM!",
      timestamp: "2023-10-22T11:05:00",
      read: true,
    },
    unreadCount: 0,
  },
  {
    id: 104,
    isGroup: true,
    groupName: "Web Development Cohort",
    groupAvatar: "/images/instructors/i1.jpg",
    participants: [
      currentUser.value,
      {
        id: 201,
        name: "Alex Johnson",
        avatar: "/images/instructors/i1.jpg",
        role: "instructor",
        online: true,
      },
      {
        id: 204,
        name: "Jessica Lee",
        avatar: "/images/instructors/i1.jpg",
        role: "student",
        online: false,
        lastActive: "2023-10-22T12:33:00",
      },
      {
        id: 205,
        name: "David Kim",
        avatar: "/images/instructors/i1.jpg",
        role: "student",
        online: true,
      },
    ],
    lastMessage: {
      id: 10004,
      senderId: 201,
      receiverId: 0, // 0 indicates group message
      content:
        "Everyone, I've shared additional resources for tomorrow's class in our shared folder.",
      timestamp: "2023-10-21T13:15:00",
      read: false,
    },
    unreadCount: 2,
  },
  {
    id: 105,
    participants: [
      currentUser.value,
      {
        id: 206,
        name: "Emma Wilson",
        avatar: "/images/instructors/i1.jpg",
        role: "instructor",
        online: false,
        lastActive: "2023-10-20T17:20:00",
      },
    ],
    lastMessage: {
      id: 10005,
      senderId: 206,
      receiverId: 1,
      content:
        "Your React Native assignment has been graded. Check it out when you have a chance.",
      timestamp: "2023-10-20T17:18:00",
      read: true,
    },
    unreadCount: 0,
  },
  {
    id: 106,
    isGroup: true,
    groupName: "Study Buddies - Algorithms",
    groupAvatar: "/images/instructors/i1.jpg",
    participants: [
      currentUser.value,
      {
        id: 203,
        name: "Michael Brown",
        avatar: "/images/instructors/i1.jpg",
        role: "instructor",
        online: true,
      },
      {
        id: 207,
        name: "Ryan Garcia",
        avatar: "/images/instructors/i1.jpg",
        role: "student",
        online: false,
        lastActive: "2023-10-22T08:40:00",
      },
      {
        id: 208,
        name: "Sophia Martinez",
        avatar: "/images/instructors/i1.jpg",
        role: "student",
        online: true,
      },
    ],
    lastMessage: {
      id: 10006,
      senderId: 207,
      receiverId: 0,
      content:
        "Can someone help me with the dynamic programming problem from last week?",
      timestamp: "2023-10-19T20:05:00",
      read: true,
    },
    unreadCount: 0,
  },
];

// Mock users for new conversations
const mockUsers: User[] = [
  {
    id: 201,
    name: "Alex Johnson",
    avatar: "/images/instructors/i1.jpg",
    role: "instructor",
    online: true,
  },
  {
    id: 202,
    name: "Sarah Chen",
    avatar: "/images/instructors/i1.jpg",
    role: "instructor",
    online: false,
  },
  {
    id: 203,
    name: "Michael Brown",
    avatar: "/images/instructors/i1.jpg",
    role: "instructor",
    online: true,
  },
  {
    id: 204,
    name: "Jessica Lee",
    avatar: "/images/instructors/i1.jpg",
    role: "student",
    online: false,
  },
  {
    id: 205,
    name: "David Kim",
    avatar: "/images/instructors/i1.jpg",
    role: "student",
    online: true,
  },
  {
    id: 206,
    name: "Emma Wilson",
    avatar: "/images/instructors/i1.jpg",
    role: "instructor",
    online: false,
  },
  {
    id: 207,
    name: "Ryan Garcia",
    avatar: "/images/instructors/i1.jpg",
    role: "student",
    online: true,
  },
  {
    id: 208,
    name: "Sophia Martinez",
    avatar: "/images/instructors/i1.jpg",
    role: "student",
    online: true,
  },
  {
    id: 209,
    name: "James Wilson",
    avatar: "/images/instructors/i1.jpg",
    role: "student",
    online: false,
  },
  {
    id: 210,
    name: "Olivia Taylor",
    avatar: "/images/instructors/i1.jpg",
    role: "student",
    online: true,
  },
];

// Mock messages for a conversation
const generateMockMessages = (conversationId: number): Message[] => {
  const conversation = conversations.value.find((c) => c.id === conversationId);
  if (!conversation) return [];

  const isGroupChat = conversation.isGroup;

  // Mock message data specific to the conversation
  switch (conversationId) {
    case 101: // Alex Johnson
      return [
        {
          id: 1001,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "Hi Emily, how's your progress on the responsive portfolio project?",
          timestamp: "2023-10-21T09:30:00",
          read: true,
        },
        {
          id: 1002,
          senderId: 1, // Emily
          receiverId: 201, // Alex
          content:
            "Hello Alex! I've completed the HTML structure and working on the CSS layouts now.",
          timestamp: "2023-10-21T09:45:00",
          read: true,
        },
        {
          id: 1003,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "Sounds good! Are you using Flexbox or Grid for the layouts?",
          timestamp: "2023-10-21T10:00:00",
          read: true,
        },
        {
          id: 1004,
          senderId: 1, // Emily
          receiverId: 201, // Alex
          content:
            "I'm using a combination of both. Grid for the overall page structure and Flexbox for component layouts.",
          timestamp: "2023-10-21T10:15:00",
          read: true,
        },
        {
          id: 1005,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "Perfect approach! Don't forget to make it responsive for mobile devices too.",
          timestamp: "2023-10-21T10:20:00",
          read: true,
        },
        {
          id: 1006,
          senderId: 1, // Emily
          receiverId: 201, // Alex
          content:
            "Yes, I'm using media queries for that. Should I prioritize mobile-first or desktop-first approach?",
          timestamp: "2023-10-21T10:30:00",
          read: true,
        },
        {
          id: 1007,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "I recommend mobile-first. It's generally better for performance and progressive enhancement.",
          timestamp: "2023-10-21T10:35:00",
          read: true,
          attachments: [
            {
              id: 1,
              name: "responsive-design-guide.pdf",
              type: "pdf",
              url: "#",
              size: 2540000,
            },
          ],
        },
        {
          id: 1008,
          senderId: 1, // Emily
          receiverId: 201, // Alex
          content:
            "Thanks for the advice and the guide! I'll switch to mobile-first approach then.",
          timestamp: "2023-10-21T10:45:00",
          read: true,
        },
        {
          id: 1009,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "You're welcome! Let me know if you have any other questions.",
          timestamp: "2023-10-22T14:15:00",
          read: true,
        },
        {
          id: 10001,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "Great progress on your latest assignment! Let me know if you have any questions about the feedback.",
          timestamp: "2023-10-22T14:30:00",
          read: false,
        },
        {
          id: 10011,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "Also, could you share your portfolio once it's ready? I'd like to feature it in our showcase.",
          timestamp: "2023-10-22T14:32:00",
          read: false,
        },
        {
          id: 10012,
          senderId: 201, // Alex
          receiverId: 1, // Emily
          content:
            "The deadline is next Friday, but earlier submissions are always appreciated!",
          timestamp: "2023-10-22T14:35:00",
          read: false,
        },
      ];
    case 102: // Sarah Chen
      return [
        {
          id: 2001,
          senderId: 202, // Sarah
          receiverId: 1, // Emily
          content: "Emily, your UI design project was very creative!",
          timestamp: "2023-10-20T13:10:00",
          read: true,
        },
        {
          id: 2002,
          senderId: 202, // Sarah
          receiverId: 1, // Emily
          content:
            "I especially liked your color scheme and typography choices. Very elegant and professional.",
          timestamp: "2023-10-20T13:12:00",
          read: true,
        },
        {
          id: 2003,
          senderId: 202, // Sarah
          receiverId: 1, // Emily
          content:
            "Here's your detailed feedback with suggestions for improvement:",
          timestamp: "2023-10-20T13:15:00",
          read: true,
          attachments: [
            {
              id: 2,
              name: "design-feedback.pdf",
              type: "pdf",
              url: "#",
              size: 1820000,
            },
          ],
        },
        {
          id: 2004,
          senderId: 1, // Emily
          receiverId: 202, // Sarah
          content:
            "Thank you so much, Sarah! I really enjoyed working on this project.",
          timestamp: "2023-10-20T14:30:00",
          read: true,
        },
        {
          id: 2005,
          senderId: 1, // Emily
          receiverId: 202, // Sarah
          content: "I'll review your feedback and incorporate the suggestions.",
          timestamp: "2023-10-20T14:32:00",
          read: true,
        },
        {
          id: 2006,
          senderId: 202, // Sarah
          receiverId: 1, // Emily
          content:
            "Great! Let me know if anything isn't clear or if you have questions.",
          timestamp: "2023-10-20T14:45:00",
          read: true,
        },
        {
          id: 2007,
          senderId: 1, // Emily
          receiverId: 202, // Sarah
          content:
            "I actually do have a question about the navigation redesign you suggested.",
          timestamp: "2023-10-21T10:20:00",
          read: true,
        },
        {
          id: 2008,
          senderId: 202, // Sarah
          receiverId: 1, // Emily
          content: "Sure, what's your question?",
          timestamp: "2023-10-21T11:05:00",
          read: true,
        },
        {
          id: 2009,
          senderId: 1, // Emily
          receiverId: 202, // Sarah
          content:
            "You mentioned simplifying the navigation by using dropdown menus instead of the mega menu I designed. I'm concerned about discovery - won't important options be hidden?",
          timestamp: "2023-10-21T11:30:00",
          read: true,
        },
        {
          id: 2010,
          senderId: 202, // Sarah
          receiverId: 1, // Emily
          content:
            "That's a good concern! In this specific case, the user testing showed that most users were overwhelmed by the mega menu. A simpler navigation with well-labeled dropdowns could improve usability. But you could design a hybrid approach if you prefer!",
          timestamp: "2023-10-21T15:15:00",
          read: true,
        },
        {
          id: 10002,
          senderId: 1, // Emily
          receiverId: 202, // Sarah
          content:
            "Thank you for the detailed feedback on my design project. I'll work on the suggested improvements.",
          timestamp: "2023-10-21T16:20:00",
          read: true,
        },
      ];
    default: {
      // Generate generic messages for other conversations
      const messages: Message[] = [];
      const otherUser = isGroupChat
        ? null
        : conversation.participants.find((p) => p.id !== currentUser.value.id);
      const baseTimestamp = new Date("2023-10-20T10:00:00");

      // Generate 10 messages
      for (let i = 0; i < 10; i++) {
        const isFromCurrentUser = i % 2 === 0;
        const messageTime = new Date(baseTimestamp);
        messageTime.setHours(messageTime.getHours() + i);

        if (isGroupChat) {
          // For group chats, rotate between participants
          const participantIndex = i % conversation.participants.length;
          const sender = conversation.participants[participantIndex];

          messages.push({
            id: 3000 + i,
            senderId: sender.id,
            receiverId: 0, // 0 for group message
            content: `This is a sample message ${
              i + 1
            } in the group chat from ${sender.name}.`,
            timestamp: messageTime.toISOString(),
            read: true,
          });
        } else {
          messages.push({
            id: 3000 + i,
            senderId: isFromCurrentUser
              ? currentUser.value.id
              : otherUser?.id || 0,
            receiverId: isFromCurrentUser
              ? otherUser?.id || 0
              : currentUser.value.id,
            content: `This is a sample message ${i + 1} ${
              isFromCurrentUser ? "from you" : "to you"
            }.`,
            timestamp: messageTime.toISOString(),
            read: true,
          });
        }
      }

      // Add the last message from the conversation data
      if (conversation.lastMessage) {
        messages.push({
          ...conversation.lastMessage,
          id: 3100,
        });
      }

      return messages.sort(
        (a, b) =>
          new Date(a.timestamp).getTime() - new Date(b.timestamp).getTime()
      );
    }
  }
};

// Select a conversation
const selectConversation = (conversation: Conversation) => {
  activeConversation.value = conversation;
  messagesLoading.value = true;

  // On mobile, hide the conversation list after selecting
  if (isMobileView.value) {
    showConversationList.value = false;
  }

  // Simulate fetching messages
  setTimeout(() => {
    messages.value = generateMockMessages(conversation.id);
    messagesLoading.value = false;

    // Scroll to bottom of messages
    nextTick(() => {
      scrollToBottom();
    });

    // Mark unread messages as read
    markConversationAsRead(conversation.id);
  }, 500);
};

// Scroll to bottom of messages
const scrollToBottom = () => {
  const messagesContainer = document.getElementById("messages-container");
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
};

// Mark conversation messages as read
const markConversationAsRead = (conversationId: number) => {
  const conversation = conversations.value.find((c) => c.id === conversationId);
  if (conversation) {
    conversation.unreadCount = 0;
  }
};

// Add a new message
const addMessage = (content: string, attachments?: any[]) => {
  if (!content.trim() && (!attachments || attachments.length === 0)) return;
  if (!activeConversation.value) return;

  // Show sending animation
  isMessageSent.value = true;

  // Create attachments from uploaded files
  const messageAttachments = attachments?.map((file, index) => ({
    id: Date.now() + index,
    name: file.name,
    type: file.name.split(".").pop() || "file",
    url: "#",
    size: file.size,
  }));

  // Create new message
  const newMsg: Message = {
    id: Date.now(),
    senderId: currentUser.value.id,
    receiverId: activeConversation.value.isGroup
      ? 0
      : activeConversation.value.participants.find(
          (p) => p.id !== currentUser.value.id
        )?.id || 0,
    content: content.trim(),
    timestamp: new Date().toISOString(),
    read: false,
    attachments: messageAttachments?.length ? messageAttachments : undefined,
  };

  // Add to messages
  messages.value.push(newMsg);

  // Update the conversation's last message
  const conversation = conversations.value.find(
    (c) => c.id === activeConversation.value?.id
  );
  if (conversation) {
    conversation.lastMessage = newMsg;
  }

  // Scroll to bottom
  nextTick(() => {
    scrollToBottom();

    // Reset the animation flag after a delay
    setTimeout(() => {
      isMessageSent.value = false;
    }, 1000);
  });

  return newMsg;
};

// Create new conversation
const createNewConversation = (users: User[], groupName?: string) => {
  if (users.length === 0) return;

  const isGroup = users.length > 1 || !!groupName;

  // Create conversation object
  const newConversation: Conversation = {
    id: Date.now(),
    participants: [currentUser.value, ...users],
    unreadCount: 0,
    isPinned: false,
  };

  if (isGroup) {
    newConversation.isGroup = true;
    newConversation.groupName =
      groupName ||
      `Group with ${users.map((u) => u.name.split(" ")[0]).join(", ")}`;
    newConversation.groupAvatar = "/images/instructors/i1.jpg";
  }

  // Add to conversations list
  conversations.value.unshift(newConversation);

  // Select the new conversation
  selectConversation(newConversation);

  // Reset state
  showNewMessageDialog.value = false;
};

// Show conversation list (for mobile back button)
const showConversations = () => {
  showConversationList.value = true;
};

// Toggle user info sidebar
const toggleUserInfo = () => {
  showUserInfo.value = !showUserInfo.value;
};

// Check screen size for mobile view
const checkMobileView = () => {
  isMobileView.value = window.innerWidth < 768;
  showConversationList.value = !isMobileView.value;
};

// Watch for changes in active conversation
watch(activeConversation, () => {
  showUserInfo.value = false;
});

// Load data
onMounted(() => {
  // Check for mobile view
  checkMobileView();
  window.addEventListener("resize", checkMobileView);

  // Load conversations
  setTimeout(() => {
    conversations.value = [...mockConversations];
    conversationsLoading.value = false;

    // Load available users for new conversations
    availableUsers.value = [...mockUsers];

    // Simulate loading complete
    isLoading.value = false;

    // Trigger animation
    setTimeout(() => {
      isLoaded.value = true;
    }, 100);

    // Select first conversation by default (for desktop)
    if (!isMobileView.value && conversations.value.length > 0) {
      selectConversation(conversations.value[0]);
    }
  }, 800);
});
</script>

<template>
  <div>
    <!-- Main messaging container -->
    <div
      class="h-[calc(100vh-10rem)] bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transform transition-all duration-500 flex"
      :class="
        isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
      "
    >
      <!-- Loading state -->
      <div
        v-if="isLoading"
        class="w-full h-full flex items-center justify-center"
      >
        <div class="flex flex-col items-center">
          <div
            class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mb-4"
          ></div>
          <p class="text-gray-600">Loading messages...</p>
        </div>
      </div>

      <!-- Main content when loaded -->
      <template v-else>
        <!-- Conversation list component -->
        <DashboardMessagingConversationList
          v-show="showConversationList"
          :conversations="conversations"
          :loading="conversationsLoading"
          :current-user="currentUser"
          :active-conversation="activeConversation"
          @select-conversation="selectConversation"
          @new-conversation="showNewMessageDialog = true"
        />

        <!-- Message area component -->
        <DashboardMessagingMessageArea
          v-show="!showConversationList || !isMobileView"
          :conversation="activeConversation"
          :messages="messages"
          :current-user="currentUser"
          :loading="messagesLoading"
          :is-mobile="isMobileView"
          :show-user-info="showUserInfo"
          :is-message-sent="isMessageSent"
          @back="showConversations"
          @toggle-user-info="toggleUserInfo"
          @send-message="addMessage"
          @new-conversation="showNewMessageDialog = true"
        />

        <!-- User profile sidebar -->
        <DashboardMessagingUserProfileSidebar
          v-if="showUserInfo && activeConversation"
          :conversation="activeConversation"
          :current-user="currentUser"
          :messages="messages"
          @close="showUserInfo = false"
        />
      </template>
    </div>

    <!-- New message dialog -->
    <DashboardMessagingNewMessageDialog
      v-if="showNewMessageDialog"
      :users="availableUsers"
      :current-user="currentUser"
      @close="showNewMessageDialog = false"
      @create="createNewConversation"
    />
  </div>
</template>

<style scoped>
/* Animation keyframes */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

/* Ensure the parent container has a defined height */
:deep(.message-container) {
  height: calc(100% - 130px);
  overflow-y: auto;
}

/* Custom scrollbar */
:deep(::-webkit-scrollbar) {
  width: 6px;
  height: 6px;
}

:deep(::-webkit-scrollbar-track) {
  background: #f1f5f9;
  border-radius: 4px;
}

:deep(::-webkit-scrollbar-thumb) {
  background: #cbd5e1;
  border-radius: 4px;
}

:deep(::-webkit-scrollbar-thumb:hover) {
  background: #6366f1;
}
</style>
