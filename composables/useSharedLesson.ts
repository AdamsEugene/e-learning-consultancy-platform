import { ref } from "vue";
import { detailedCoursesData } from "~/constants/coursesData";

// Define the lesson type
export interface CourseLesson {
  id: number;
  title: string;
  description?: string;
  duration: string;
  videoUrl?: string;
  content?: string;
  type: "video" | "quiz" | "assignment" | "resource";
  isCompleted?: boolean;
  order: number;
}

export interface CourseSection {
  id: number;
  title: string;
  description?: string;
  order: number;
  lessons: CourseLesson[];
}

export interface Course {
  id: number;
  title: string;
  instructor?: {
    name: string;
    avatar: string;
    title: string;
    bio: string;
  };
  sections: CourseSection[];
}

// Create shared state
const currentCourse = ref<Course | null>(null);
const currentLesson = ref<CourseLesson | null>(null);
const currentSection = ref<CourseSection | null>(null);
const isLoading = ref(true);
const hasError = ref(false);
// Store multiple courses by ID for caching
const coursesData = ref<Record<number, Course>>({});

// Export the composable
export const useSharedLesson = () => {
  // Set the current lesson and find its section
  const setCurrentLesson = (lesson: CourseLesson) => {
    currentLesson.value = lesson;

    // Find the section containing this lesson
    if (currentCourse.value) {
      for (const section of currentCourse.value.sections) {
        if (section.lessons.some((l) => l.id === lesson.id)) {
          currentSection.value = section;
          break;
        }
      }
    }
  };

  // Set the current course
  const setCurrentCourse = (course: Course) => {
    currentCourse.value = course;
    // Also cache the course data
    coursesData.value[course.id] = course;
  };

  // Get a course by ID from cache
  const getCourseById = (courseId: number): Course | null => {
    return coursesData.value[courseId] || null;
  };

  // Set loading state
  const setLoading = (loading: boolean) => {
    isLoading.value = loading;
  };

  // Set error state
  const setError = (error: boolean) => {
    hasError.value = error;
  };

  // Mark a lesson as completed
  const markLessonAsCompleted = (lessonId: number) => {
    if (!currentCourse.value) return;

    // Update in course data
    for (const section of currentCourse.value.sections) {
      for (const lesson of section.lessons) {
        if (lesson.id === lessonId) {
          lesson.isCompleted = true;

          // Also update current lesson if it's the same
          if (currentLesson.value && currentLesson.value.id === lessonId) {
            currentLesson.value.isCompleted = true;
          }

          // Update in cached data
          if (coursesData.value[currentCourse.value.id]) {
            coursesData.value[currentCourse.value.id] = {
              ...currentCourse.value,
            };
          }

          return;
        }
      }
    }
  };

  // Get course progress percentage
  const getCourseProgress = (): number => {
    if (!currentCourse.value) return 0;

    let totalLessons = 0;
    let completedLessons = 0;

    currentCourse.value.sections.forEach((section) => {
      section.lessons.forEach((lesson) => {
        totalLessons++;
        if (lesson.isCompleted) {
          completedLessons++;
        }
      });
    });

    return totalLessons > 0
      ? Math.round((completedLessons / totalLessons) * 100)
      : 0;
  };

  // Find a lesson by ID
  const findLessonById = (lessonId: number): CourseLesson | null => {
    if (!currentCourse.value) return null;

    for (const section of currentCourse.value.sections) {
      for (const lesson of section.lessons) {
        if (lesson.id === lessonId) {
          return lesson;
        }
      }
    }

    return null;
  };

  // Get next lesson
  const getNextLesson = (): CourseLesson | null => {
    if (!currentCourse.value || !currentLesson.value) return null;

    // Flatten all lessons and sort them by section order and lesson order
    const allLessons = currentCourse.value.sections
      .flatMap((section) =>
        section.lessons.map((lesson) => ({
          ...lesson,
          sectionOrder: section.order,
        }))
      )
      .sort((a, b) => {
        if (a.sectionOrder !== b.sectionOrder) {
          return a.sectionOrder - b.sectionOrder;
        }
        return a.order - b.order;
      });

    // Find current lesson index
    const currentIndex = allLessons.findIndex(
      (lesson) => lesson.id === currentLesson.value!.id
    );
    if (currentIndex === -1 || currentIndex >= allLessons.length - 1)
      return null;

    return allLessons[currentIndex + 1];
  };

  // Get previous lesson
  const getPreviousLesson = (): CourseLesson | null => {
    if (!currentCourse.value || !currentLesson.value) return null;

    // Flatten all lessons and sort them by section order and lesson order
    const allLessons = currentCourse.value.sections
      .flatMap((section) =>
        section.lessons.map((lesson) => ({
          ...lesson,
          sectionOrder: section.order,
        }))
      )
      .sort((a, b) => {
        if (a.sectionOrder !== b.sectionOrder) {
          return a.sectionOrder - b.sectionOrder;
        }
        return a.order - b.order;
      });

    // Find current lesson index
    const currentIndex = allLessons.findIndex(
      (lesson) => lesson.id === currentLesson.value!.id
    );
    if (currentIndex <= 0) return null;

    return allLessons[currentIndex - 1];
  };

  // Fetch course data
  const fetchCourseData = async (courseId: number): Promise<Course | null> => {
    // If we already have the course data in cache, return it
    if (coursesData.value[courseId]) {
      return coursesData.value[courseId];
    }

    // Set loading state
    setLoading(true);
    setError(false);

    try {
      // In a real app, this would be an API call
      // For now, we simulate a delay and get data from constants
      await new Promise((resolve) => setTimeout(resolve, 500));

      // Check if we have the course in our constants
      const courseData = detailedCoursesData[courseId];

      if (!courseData) {
        throw new Error(`Course with ID ${courseId} not found`);
      }

      // Set the course in shared state and cache
      setCurrentCourse(courseData as Course);

      return courseData as Course;
    } catch (err) {
      console.error("Error fetching course data:", err);
      setError(true);
      return null;
    } finally {
      setLoading(false);
    }
  };

  return {
    // State
    currentCourse,
    currentLesson,
    currentSection,
    isLoading,
    hasError,
    coursesData,

    // Methods
    setCurrentLesson,
    setCurrentCourse,
    getCourseById,
    setLoading,
    setError,
    markLessonAsCompleted,
    getCourseProgress,
    findLessonById,
    getNextLesson,
    getPreviousLesson,
    fetchCourseData,
  };
};
