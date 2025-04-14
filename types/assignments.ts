// types/dashboard.ts

// Assignment-related types
export type AssignmentStatus =
  | "all"
  | "pending"
  | "submitted"
  | "graded"
  | "late";
export type AssignmentType = "all" | "project" | "assignment" | "quiz";
export type SortOption =
  | "dueDate-asc"
  | "dueDate-desc"
  | "title-asc"
  | "title-desc"
  | "course-asc"
  | "course-desc";

export interface FileAttachment {
  name: string;
  url: string;
  type: string;
}

export interface SubmittedFile {
  name: string;
  url: string;
  uploadDate: string;
}

export interface Assignment {
  id: number;
  title: string;
  courseId: number;
  courseName: string;
  dueDate: string;
  status: "pending" | "submitted" | "graded" | "late";
  grade?: number;
  maxGrade?: number;
  feedback?: string;
  type: "project" | "assignment" | "quiz";
  description?: string;
  attachments?: FileAttachment[];
  submittedFiles?: SubmittedFile[];
}

export interface Course {
  id: number;
  title: string;
}

// Utility functions shared across dashboard components
export const formatDate = (dateString: string): string => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

export const formatTime = (dateString: string): string => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
  });
};

export const getTimeRemaining = (dateString: string): string => {
  const now = new Date();
  const dueDate = new Date(dateString);
  const diffMs = dueDate.getTime() - now.getTime();

  if (diffMs <= 0) return "Overdue";

  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor(
    (diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );

  if (diffDays > 0) {
    return `${diffDays}d ${diffHours}h remaining`;
  }
  return `${diffHours}h remaining`;
};

export const getStatusColor = (status: string): string => {
  switch (status) {
    case "pending":
      return "bg-yellow-100 text-yellow-800";
    case "submitted":
      return "bg-blue-100 text-blue-800";
    case "graded":
      return "bg-green-100 text-green-800";
    case "late":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

export const getTypeIcon = (type: string): string => {
  switch (type) {
    case "project":
      return "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6";
    case "assignment":
      return "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01";
    case "quiz":
      return "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4";
    default:
      return "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z";
  }
};

export const getFileIcon = (fileType: string): string => {
  switch (fileType) {
    case "pdf":
      return "M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z";
    case "figma":
      return "M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z";
    case "zip":
      return "M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4";
    default:
      return "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z";
  }
};
