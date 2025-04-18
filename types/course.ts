// types/course.ts

export type LessonType = "video" | "quiz" | "assignment" | "resource";

export interface CourseLesson {
  id: number;
  title: string;
  duration: string;
  description: string;
  videoUrl?: string;
  content?: string;
  type: "video" | "quiz" | "assignment" | "resource";
  isCompleted?: boolean;
  order: number;
  learningObjectives?: string[];
  requirements?: string[];
}

export interface CourseSection {
  id: number;
  title: string;
  description: string;
  lessons: CourseLesson[];
  order: number;
  estimatedCompletionTime?: string;
}

export interface CourseInstructor {
  id: number;
  name: string;
  avatar: string;
  role: string;
  bio: string;
  courses: number;
  students: number;
  rating: number;
}

export interface CourseReview {
  id: number;
  user: {
    name: string;
    avatar: string;
  };
  rating: number;
  date: string;
  content: string;
  helpfulCount: number;
}

export interface Course {
  id: number;
  title: string;
  description: string;
  instructor: {
    name: string;
    avatar: string;
    title: string;
    bio: string;
  };
  sections: CourseSection[];
  level: string;
  category: string;
  tags: string[];
  totalDuration: string;
  rating: number;
  studentsCount: number;
  updatedAt: string;
  objectives: string[];
  requirements: string[];
  progress?: {
    completed: number;
    total: number;
    percentage: number;
    lastLesson?: {
      id: number;
      title: string;
    };
  };
}

export interface Note {
  id: string;
  text: string;
  timestamp: number;
  lessonId: number | string;
}
