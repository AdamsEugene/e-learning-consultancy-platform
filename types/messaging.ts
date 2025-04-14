// types.ts - Shared types for the messaging system

export interface User {
  id: number;
  name: string;
  avatar: string;
  role: string;
  online?: boolean;
  lastActive?: string;
}

export interface Message {
  id: number;
  senderId: number;
  receiverId: number;
  content: string;
  timestamp: string;
  read: boolean;
  attachments?: Attachment[];
}

export interface Attachment {
  id: number;
  name: string;
  type: string;
  url: string;
  size?: number;
}

export interface Conversation {
  id: number;
  participants: User[];
  lastMessage?: Message;
  unreadCount: number;
  isPinned?: boolean;
  isGroup?: boolean;
  groupName?: string;
  groupAvatar?: string;
}

// For filtering and sorting
export type SortOption = "recent" | "unread" | "alphabetical";
export type FilterType = "all" | "unread" | "groups" | "direct";

// Animation states
export interface AnimationState {
  isLoaded: boolean;
  newMessageAnimation: boolean;
  isMessageSent: boolean;
}
