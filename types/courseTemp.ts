// types/course.ts

export interface CourseLesson {
  id: number;
  title: string;
  duration: string;
  videoUrl?: string;
  content?: string;
  type: "video" | "quiz" | "assignment" | "resource";
  isCompleted?: boolean;
  order: number;
  description?: string;
}

export interface CourseSection {
  id: number;
  title: string;
  description?: string;
  lessons: CourseLesson[];
  order: number;
}

export interface CourseInstructor {
  id?: string;
  name: string;
  avatar: string;
  title: string;
  bio: string;
}

export interface Course {
  id: number;
  title: string;
  sections: CourseSection[];
  instructor?: CourseInstructor;
  description?: string;
  category?: string;
  level?: string;
  duration?: string;
  price?: number;
  rating?: number;
  studentsCount?: number;
  thumbnail?: string;
}

export interface Note {
  id: string;
  text: string;
  timestamp: number;
  lessonId: number | string;
}

export interface QuizQuestion {
  id: number;
  text: string;
  options: {
    id: string;
    text: string;
  }[];
  correctAnswer: string;
  explanation?: string;
}

export interface AssignmentSubmission {
  id: string;
  lessonId: number | string;
  fileName: string;
  comments: string;
  challengesFaced?: string;
  solutionsImplemented?: string;
  submittedAt: number;
  status: "pending" | "reviewed" | "approved" | "rejected";
  feedback?: string;
}

export interface Discussion {
  id: number;
  user: {
    id: string;
    name: string;
    avatar: string;
    role: string;
  };
  content: string;
  timestamp: string | number;
  upvotes: number;
  replies: {
    id: number;
    user: {
      id: string;
      name: string;
      avatar: string;
      role: string;
    };
    content: string;
    timestamp: string | number;
  }[];
}

export interface Resource {
  id: number;
  title: string;
  description: string;
  type: "pdf" | "code" | "exercises" | "video" | "link";
  url: string;
  size?: string;
  isDownloadable?: boolean;
}
