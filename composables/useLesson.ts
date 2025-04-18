import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import type { Course, CourseLesson, CourseSection } from "~/types/course";

// Extended types for lesson-specific functionality
export interface Note {
  id: string;
  text: string;
  timestamp: number;
  lessonId: number;
}

export interface LessonState {
  course: Course | null;
  currentLesson: CourseLesson | null;
  currentSection: CourseSection | null;
  loading: boolean;
  error: boolean;
  isSidebarOpen: boolean;
  watchProgress: number;
  isLessonCompleted: boolean;
  activeTab: "content" | "resources" | "notes" | "discussions";
  courseProgress: number;
  isNotesPanelOpen: boolean;
  sidebarWidth: number;
  notes: Note[];
  noteContent: string;
  notesSaved: boolean;
  isSmallScreen: boolean;
}

export function useLesson(courseIdParam: number, lessonIdParam: number) {
  const route = useRoute();
  const router = useRouter();

  // State
  const course = ref<Course | null>(null);
  const currentLesson = ref<CourseLesson | null>(null);
  const currentSection = ref<CourseSection | null>(null);
  const loading = ref(true);
  const error = ref(false);
  const isSidebarOpen = ref(true);
  const watchProgress = ref(0); // Track video progress percentage
  const isLessonCompleted = ref(false);
  const activeTab = ref<"content" | "resources" | "notes" | "discussions">(
    "content"
  );
  const courseProgress = ref(0);
  const isNotesPanelOpen = ref(false);
  const sidebarWidth = ref(320); // Default sidebar width

  // Notes functionality
  const notes = ref<Note[]>([]);
  const noteContent = ref("");
  const notesSaved = ref(false);

  // Responsive state
  const isSmallScreen = ref(false);

  // Mock API call to fetch course data
  const fetchCourseData = async () => {
    loading.value = true;
    error.value = false;

    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 800));

      // Mock data
      course.value = {
        id: courseIdParam,
        title: "Complete Web Development Bootcamp",
        instructor: {
          name: "Sarah Johnson",
          avatar: "/images/instructors/i2.jpg",
          title: "Senior Web Developer",
          bio: "15+ years of experience in web development. Worked at Google and Microsoft.",
        },
        sections: [
          {
            id: 1,
            title: "Introduction to Web Development",
            description:
              "Get started with the fundamentals of web development and set up your environment.",
            order: 1,
            lessons: [
              {
                id: 101,
                title: "Course Overview and Setup",
                description:
                  "Introduction to the course structure and setup of your development environment.",
                duration: "15m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: true,
                order: 1,
              },
              {
                id: 102,
                title: "How the Web Works: A High-Level Overview",
                description:
                  "Understanding the fundamental concepts of how the web works.",
                duration: "25m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 2,
              },
              {
                id: 103,
                title: "Setting Up Your Development Environment",
                description:
                  "Step-by-step guide to setting up your development tools and environment.",
                duration: "30m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 3,
              },
              {
                id: 104,
                title: "Web Developer Tools and Resources",
                description:
                  "Essential tools and resources for effective web development.",
                duration: "45m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 4,
              },
              {
                id: 105,
                title: "Introduction Quiz",
                description:
                  "Test your understanding of web development fundamentals.",
                duration: "20m",
                content:
                  "This would be a quiz component in a real application.",
                type: "quiz",
                isCompleted: false,
                order: 5,
              },
            ],
          },
          {
            id: 2,
            title: "HTML Fundamentals",
            description:
              "Learn the core HTML concepts to structure web pages effectively.",
            order: 2,
            lessons: [
              {
                id: 201,
                title: "HTML Basics and Document Structure",
                description:
                  "Learn the fundamental structure of HTML documents and semantic elements.",
                duration: "40m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 1,
              },
              {
                id: 202,
                title: "Working with Text Elements",
                description:
                  "Format and structure text content using HTML elements.",
                duration: "35m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 2,
              },
              {
                id: 203,
                title: "Links, Images, and Multimedia",
                description:
                  "Add links, images, and multimedia content to your web pages.",
                duration: "50m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 3,
              },
              {
                id: 204,
                title: "HTML Forms and Input Elements",
                description:
                  "Create interactive forms with various input types and validation.",
                duration: "55m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 4,
              },
              {
                id: 205,
                title: "Tables and Semantic HTML",
                description:
                  "Organize tabular data and use semantic HTML elements for better accessibility.",
                duration: "45m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 5,
              },
              {
                id: 206,
                title: "HTML Project: Building Your First Webpage",
                description:
                  "Apply your HTML knowledge to build a complete webpage from scratch.",
                duration: "1h 20m",
                content:
                  "This would be an assignment component in a real application.",
                type: "assignment",
                isCompleted: false,
                order: 6,
              },
              {
                id: 207,
                title: "HTML Section Assessment",
                description:
                  "Test your understanding of HTML concepts and best practices.",
                duration: "30m",
                content:
                  "This would be a quiz component in a real application.",
                type: "quiz",
                isCompleted: false,
                order: 7,
              },
            ],
          },
          {
            id: 3,
            title: "CSS Fundamentals",
            description:
              "Master the basics of CSS to style and layout web pages.",
            order: 3,
            lessons: [
              {
                id: 301,
                title: "Introduction to CSS",
                description:
                  "Learn the basics of CSS including selectors, properties, and values.",
                duration: "40m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 1,
              },
              {
                id: 302,
                title: "CSS Box Model",
                description:
                  "Understand the CSS box model and how it affects layout.",
                duration: "35m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 2,
              },
            ],
          },
        ],
        description:
          "A comprehensive course covering web development from basics to advanced concepts.",
        level: "Beginner to Intermediate",
        category: "Web Development",
        tags: ["HTML", "CSS", "JavaScript", "Web Development"],
        totalDuration: "15h 30m",
        rating: 4.8,
        studentsCount: 12500,
        updatedAt: "2023-06-15",
        objectives: [
          "Build responsive websites from scratch",
          "Master HTML, CSS, and JavaScript fundamentals",
          "Understand web development best practices",
          "Create interactive web applications",
          "Deploy websites to production environments",
        ],
        requirements: [
          "Basic computer skills",
          "No prior programming experience required",
          "A computer with internet access",
          "Text editor (recommended: VS Code)",
        ],
      };

      // Find current lesson and section
      findCurrentLessonAndSection();

      // Load saved notes
      loadNotes();

      // Calculate course progress
      calculateCourseProgress();

      // Check screen size for responsive design
      checkScreenSize();
    } catch (err) {
      console.error("Error fetching course data:", err);
      error.value = true;
    } finally {
      loading.value = false;
    }
  };

  // Find the current lesson and section based on route params
  const findCurrentLessonAndSection = () => {
    if (!course.value) return;

    for (const section of course.value.sections) {
      for (const lesson of section.lessons) {
        if (lesson.id === lessonIdParam) {
          currentLesson.value = lesson;
          currentSection.value = section;
          isLessonCompleted.value = !!lesson.isCompleted;
          return;
        }
      }
    }
  };

  // Get all lessons flattened into an array
  const allLessons = computed(() => {
    if (!course.value) return [];

    return course.value.sections
      .flatMap((section) => section.lessons)
      .sort((a, b) => {
        // Sort by section order first, then by lesson order
        const sectionA = course.value!.sections.find((s) =>
          s.lessons.includes(a)
        )!;
        const sectionB = course.value!.sections.find((s) =>
          s.lessons.includes(b)
        )!;

        if (sectionA.order !== sectionB.order) {
          return sectionA.order - sectionB.order;
        }

        return a.order - b.order;
      });
  });

  // Get previous and next lessons
  const previousLesson = computed(() => {
    if (!currentLesson.value || allLessons.value.length === 0) return null;

    const currentIndex = allLessons.value.findIndex(
      (lesson) => lesson.id === currentLesson.value!.id
    );
    if (currentIndex <= 0) return null;

    return allLessons.value[currentIndex - 1];
  });

  const nextLesson = computed(() => {
    if (!currentLesson.value || allLessons.value.length === 0) return null;

    const currentIndex = allLessons.value.findIndex(
      (lesson) => lesson.id === currentLesson.value!.id
    );
    if (currentIndex === -1 || currentIndex >= allLessons.value.length - 1)
      return null;

    return allLessons.value[currentIndex + 1];
  });

  // Navigate to a lesson
  const navigateToLesson = (lesson: CourseLesson) => {
    if (lesson.id === currentLesson.value?.id) return;

    // Save current lesson's progress
    saveProgress();

    // Navigate to new lesson
    router.push(`/courses/${courseIdParam}/learn/${lesson.id}`);

    // Close mobile sidebar on small screens
    if (isSmallScreen.value) {
      toggleSidebar(false);
    }
  };

  // Toggle sidebar with optional force parameter
  const toggleSidebar = (forcedState?: boolean) => {
    if (typeof forcedState === "boolean") {
      isSidebarOpen.value = forcedState;
    } else {
      isSidebarOpen.value = !isSidebarOpen.value;
    }
  };

  // Toggle notes panel
  const toggleNotesPanel = (forcedState?: boolean) => {
    if (typeof forcedState === "boolean") {
      isNotesPanelOpen.value = forcedState;
    } else {
      isNotesPanelOpen.value = !isNotesPanelOpen.value;
    }
  };

  // Calculate course progress
  const calculateCourseProgress = () => {
    if (!course.value) return;

    let totalLessons = 0;
    let completedLessons = 0;

    course.value.sections.forEach((section) => {
      section.lessons.forEach((lesson) => {
        totalLessons++;
        if (lesson.isCompleted) {
          completedLessons++;
        }
      });
    });

    courseProgress.value =
      totalLessons > 0
        ? Math.round((completedLessons / totalLessons) * 100)
        : 0;
  };

  // Save note
  const saveNote = () => {
    if (!noteContent.value.trim() || !currentLesson.value) return;

    const newNote = {
      id: `note-${Date.now()}`,
      text: noteContent.value.trim(),
      timestamp: Date.now(),
      lessonId: lessonIdParam,
    };

    // Add to notes array
    notes.value.unshift(newNote);

    // Save to localStorage
    const savedNotes = JSON.parse(
      localStorage.getItem(`course_${courseIdParam}_notes`) || "[]"
    );
    savedNotes.push(newNote);
    localStorage.setItem(
      `course_${courseIdParam}_notes`,
      JSON.stringify(savedNotes)
    );

    // Clear input
    noteContent.value = "";

    // Show saved indicator
    notesSaved.value = true;
    setTimeout(() => {
      notesSaved.value = false;
    }, 2000);
  };

  // Load notes
  const loadNotes = () => {
    if (!courseIdParam) return;

    // Load from localStorage
    const savedNotes = JSON.parse(
      localStorage.getItem(`course_${courseIdParam}_notes`) || "[]"
    );

    // Filter notes for current lesson
    notes.value = savedNotes.filter(
      (note: Note) => Number(note.lessonId) === lessonIdParam
    );
  };

  // Delete note
  const deleteNote = (noteId: string) => {
    // Remove from current notes
    notes.value = notes.value.filter((note) => note.id !== noteId);

    // Update localStorage
    const savedNotes = JSON.parse(
      localStorage.getItem(`course_${courseIdParam}_notes`) || "[]"
    );
    const updatedNotes = savedNotes.filter(
      (note: { id: string }) => note.id !== noteId
    );
    localStorage.setItem(
      `course_${courseIdParam}_notes`,
      JSON.stringify(updatedNotes)
    );
  };

  // Mark lesson as complete
  const markLessonComplete = () => {
    if (!currentLesson.value) return;

    // In a real app, this would make an API call
    currentLesson.value.isCompleted = true;
    isLessonCompleted.value = true;

    // Recalculate course progress
    calculateCourseProgress();

    // If there's a next lesson, you might want to navigate to it
    if (nextLesson.value) {
      navigateToLesson(nextLesson.value);
    }
  };

  // Handle video progress
  const handleVideoProgress = (time: number) => {
    if (!currentLesson.value || currentLesson.value.type !== "video") return;

    // Calculate progress percentage (this would be more accurate with actual video duration)
    const duration = 600; // Mock duration in seconds
    watchProgress.value = Math.min(100, Math.round((time / duration) * 100));

    // Auto-mark as complete if watched >90%
    if (watchProgress.value > 90 && !isLessonCompleted.value) {
      markLessonComplete();
    }
  };

  // Handle video completion
  const handleVideoEnded = () => {
    if (!isLessonCompleted.value) {
      markLessonComplete();
    }
  };

  // Save progress before leaving
  const saveProgress = () => {
    // In a real app, this would make an API call to save progress
    console.log(
      "Saving progress for lesson",
      currentLesson.value?.id,
      "progress:",
      watchProgress.value
    );
  };

  // Mock lesson content for rich display
  const lessonContent = computed(() => {
    if (!currentLesson.value) return "";

    return `
      <div class="prose prose-indigo max-w-none">
        <h2>Lesson Overview</h2>
        <p>In this lesson, we'll learn about ${currentLesson.value.title}. This is crucial knowledge for becoming a proficient web developer.</p>
        <p>We'll cover the following topics:</p>
        <ul>
          <li>Core concepts and principles</li>
          <li>Best practices and common patterns</li>
          <li>Practical examples and demos</li>
          <li>Common pitfalls and how to avoid them</li>
        </ul>
        <h3>Learning Objectives</h3>
        <p>By the end of this lesson, you will be able to:</p>
        <ul>
          <li>Understand the fundamentals of ${currentLesson.value.title}</li>
          <li>Apply these concepts in your own projects</li>
          <li>Troubleshoot common issues</li>
        </ul>
        <h3>Additional Resources</h3>
        <p>The following resources may be helpful for further learning:</p>
        <ul>
          <li><a href="#" class="text-indigo-600">Official Documentation</a></li>
          <li><a href="#" class="text-indigo-600">Supplementary Reading Material</a></li>
          <li><a href="#" class="text-indigo-600">Practice Exercises</a></li>
        </ul>
      </div>
    `;
  });

  // Check screen size for responsive design
  const checkScreenSize = () => {
    isSmallScreen.value = window.innerWidth < 768;

    // Adjust sidebar visibility based on screen size
    isSidebarOpen.value = !isSmallScreen.value;
  };

  // Handle sidebar resize
  const handleSidebarResize = (dimensions: {
    width: number;
    height: number;
  }) => {
    sidebarWidth.value = dimensions.width;
  };

  // Watch for route changes
  watch(
    () => route.params.lessonId,
    (newId, oldId) => {
      if (newId !== oldId) {
        fetchCourseData();
        // Close notes panel on lesson change
        isNotesPanelOpen.value = false;
      }
    }
  );

  // Lifecycle hooks
  onMounted(() => {
    fetchCourseData();
    window.addEventListener("resize", checkScreenSize);
    window.addEventListener("beforeunload", saveProgress);
  });

  onBeforeUnmount(() => {
    saveProgress();
    window.removeEventListener("beforeunload", saveProgress);
    window.removeEventListener("resize", checkScreenSize);
  });

  return {
    // State
    course,
    currentLesson,
    currentSection,
    loading,
    error,
    isSidebarOpen,
    watchProgress,
    isLessonCompleted,
    activeTab,
    courseProgress,
    isNotesPanelOpen,
    sidebarWidth,
    notes,
    noteContent,
    notesSaved,
    isSmallScreen,

    // Computed
    allLessons,
    previousLesson,
    nextLesson,
    lessonContent,

    // Methods
    fetchCourseData,
    findCurrentLessonAndSection,
    navigateToLesson,
    toggleSidebar,
    toggleNotesPanel,
    calculateCourseProgress,
    saveNote,
    loadNotes,
    deleteNote,
    markLessonComplete,
    handleVideoProgress,
    handleVideoEnded,
    saveProgress,
    checkScreenSize,
    handleSidebarResize,
  };
}

// Export the return type
export type UseLessonReturn = ReturnType<typeof useLesson>;
