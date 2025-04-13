// types/course.ts

export type LessonType = "video" | "quiz" | "assignment" | "resource";

export interface CourseLesson {
  id: number;
  title: string;
  duration: string;
  videoUrl?: string;
  type: LessonType;
  isPreview?: boolean;
  isCompleted?: boolean;
}

export interface CourseSection {
  id: number;
  title: string;
  lessons: CourseLesson[];
  totalDuration: string;
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
  subtitle: string;
  description: string;
  category: string;
  subCategory?: string;
  level: string;
  language: string;
  price: number;
  originalPrice?: number;
  image: string;
  rating: number;
  reviewCount: number;
  enrollmentCount: number;
  createdAt: string;
  lastUpdated: string;
  totalDuration: string;
  totalLessons: number;
  features: string[];
  requirements: string[];
  whatYouWillLearn: string[];
  sections: CourseSection[];
  instructors: CourseInstructor[];
  reviews: CourseReview[];
  isEnrolled?: boolean;
  progress?: number;
  badges?: string[];
}

export interface Note {
  id: string;
  text: string;
  timestamp: number;
  lessonId: number | string;
}
