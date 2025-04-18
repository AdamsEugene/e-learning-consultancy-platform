export interface LiveSession {
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

export interface ChatMessage {
  id: number;
  userId: number;
  userName: string;
  userAvatar: string;
  message: string;
  timestamp: number;
  isInstructor: boolean;
}

export interface PollOption {
  id: number;
  text: string;
  votes: number;
}

export interface Poll {
  id: number;
  question: string;
  options: PollOption[];
  totalVotes: number;
  userVoted: boolean;
  createdAt: number;
}

export interface Resource {
  id: number;
  title: string;
  url: string;
  type: string;
}

export interface Participant {
  id: number;
  name: string;
  avatar: string;
  isInstructor: boolean;
}
