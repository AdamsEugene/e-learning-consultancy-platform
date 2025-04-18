import { ref, computed } from "vue";
import type { Course } from "~/types/course";

export const useCourse = (courseId: number) => {
  // State
  const course = ref<Course | null>(null);
  const loading = ref(true);
  const error = ref(false);
  const activeTab = ref<"overview" | "curriculum" | "instructor" | "reviews">(
    "curriculum"
  );
  const expandedSections = ref<number[]>([]);
  const selectedSection = ref<number | null>(null);

  // FAQ data
  const faqs = [
    {
      question: "Do I need any prior programming experience for this course?",
      answer:
        "No, this course is designed for complete beginners. We start from the very basics and gradually build up your knowledge and skills. The only requirements are basic computer skills and a willingness to learn.",
    },
    {
      question: "How long do I have access to the course?",
      answer:
        "Once you enroll, you have lifetime access to the course. This includes all future updates and additional content that may be added over time.",
    },
    {
      question: "Will I get a certificate upon completion?",
      answer:
        "Yes, upon completing all the course modules and assignments, you'll receive a certificate of completion that you can add to your LinkedIn profile or resume.",
    },
    {
      question: "Is there support available if I get stuck?",
      answer:
        "Absolutely! You can post your questions in the course discussion forums where both the instructor and other students can help. The instructor typically responds to questions within 24-48 hours.",
    },
  ];

  // Mock API call to fetch course data
  const fetchCourseData = async () => {
    loading.value = true;
    error.value = false;

    try {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 800));

      // Mock data
      course.value = {
        id: courseId,
        title: "Complete Web Development Bootcamp",
        description:
          "Learn web development from scratch and build real-world projects with the latest technologies including HTML, CSS, JavaScript, React, Node.js, and more.",
        instructor: {
          name: "Sarah Johnson",
          avatar: "/images/instructors/i2.jpg",
          title: "Senior Web Developer & Educator",
          bio: "Sarah has over 12 years of experience in web development, working with Fortune 500 companies and tech startups. She's passionate about teaching and has helped more than 25,000 students master web development skills.",
        },
        level: "Beginner to Intermediate",
        category: "Web Development",
        tags: ["HTML", "CSS", "JavaScript", "React", "Node.js", "MongoDB"],
        totalDuration: "42h 15m",
        rating: 4.8,
        studentsCount: 15432,
        updatedAt: "2023-09-15",
        objectives: [
          "Build responsive and dynamic websites from scratch",
          "Understand front-end and back-end web development principles",
          "Create full-stack applications with modern JavaScript frameworks",
          "Deploy web applications to production environments",
          "Implement authentication, database storage, and API integrations",
        ],
        requirements: [
          "Basic computer skills and familiarity with using the internet",
          "No prior programming experience required - we'll start from the basics",
          "A computer with internet access (Windows, Mac, or Linux)",
          "Enthusiasm and willingness to practice what you learn",
        ],
        sections: [
          {
            id: 1,
            title: "Introduction to Web Development",
            description:
              "Get started with the fundamentals of web development and set up your development environment.",
            estimatedCompletionTime: "3 hours",
            order: 1,
            lessons: [
              {
                id: 101,
                title: "Course Overview and Setup",
                description:
                  "Introduction to the course structure, learning pathway, and how to set up your development environment for optimal learning.",
                duration: "15m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: true,
                order: 1,
                learningObjectives: [
                  "Understand the course structure and learning path",
                  "Set up your development environment with VS Code",
                  "Learn how to use course resources effectively",
                ],
                requirements: [],
              },
              {
                id: 102,
                title: "How the Web Works: A High-Level Overview",
                description:
                  "A comprehensive overview of how the internet works, including browsers, servers, HTTP protocols, and the client-server architecture.",
                duration: "25m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 2,
                learningObjectives: [
                  "Understand the client-server model",
                  "Learn how HTTP requests and responses work",
                  "Comprehend the role of DNS in web navigation",
                ],
                requirements: [],
              },
              {
                id: 103,
                title: "Setting Up Your Development Environment",
                description:
                  "A step-by-step guide to installing and configuring all the necessary tools for web development, including code editors, browsers, and developer tools.",
                duration: "30m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 3,
                learningObjectives: [
                  "Install and configure VS Code for web development",
                  "Set up browser developer tools",
                  "Install essential VS Code extensions for web development",
                ],
                requirements: [],
              },
              {
                id: 104,
                title: "Web Developer Tools and Resources",
                description:
                  "Introduction to essential developer tools, documentation websites, and resources that will help you throughout your web development journey.",
                duration: "45m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 4,
                learningObjectives: [
                  "Master Chrome DevTools for debugging",
                  "Discover key web development resources",
                  "Learn how to read and use documentation effectively",
                ],
                requirements: [],
              },
              {
                id: 105,
                title: "Introduction Quiz",
                description:
                  "Test your understanding of the key concepts covered in the introduction section with this comprehensive quiz.",
                duration: "20m",
                content:
                  "This would be a quiz component in a real application.",
                type: "quiz",
                isCompleted: false,
                order: 5,
                learningObjectives: [
                  "Validate your understanding of basic web concepts",
                  "Test your knowledge of the development environment setup",
                  "Ensure you're ready to move to the next section",
                ],
                requirements: ["Complete all previous lessons in this section"],
              },
            ],
          },
          {
            id: 2,
            title: "HTML Fundamentals",
            description:
              "Learn the core HTML concepts to structure web pages effectively and create semantic, accessible content.",
            estimatedCompletionTime: "6 hours",
            order: 2,
            lessons: [
              {
                id: 201,
                title: "HTML Basics and Document Structure",
                description:
                  "Learn the fundamental structure of HTML documents, including doctype declarations, head and body elements, and semantic page structure.",
                duration: "40m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 1,
                learningObjectives: [
                  "Understand HTML document structure",
                  "Create properly structured HTML pages",
                  "Learn about HTML5 semantic elements",
                ],
                requirements: ["Complete the Introduction section"],
              },
              {
                id: 202,
                title: "Working with Text Elements",
                description:
                  "Master HTML text formatting elements to properly structure and style your content for better readability and SEO.",
                duration: "35m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 2,
                learningObjectives: [
                  "Use heading tags appropriately",
                  "Structure content with paragraphs and lists",
                  "Format text with semantic HTML elements",
                ],
                requirements: [],
              },
              {
                id: 203,
                title: "Links, Images, and Multimedia",
                description:
                  "Learn how to incorporate links, images, audio, and video into your HTML documents with proper attributes and best practices.",
                duration: "50m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 3,
                learningObjectives: [
                  "Create links between pages and sections",
                  "Add responsive images with appropriate attributes",
                  "Embed video and audio content",
                ],
                requirements: [],
              },
              {
                id: 204,
                title: "HTML Forms and Input Elements",
                description:
                  "Comprehensive guide to creating forms for user input, including different input types, validation, and form submission methods.",
                duration: "55m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 4,
                learningObjectives: [
                  "Structure forms with proper elements",
                  "Use various input types for different data",
                  "Implement basic client-side validation",
                ],
                requirements: [],
              },
              {
                id: 205,
                title: "Tables and Semantic HTML",
                description:
                  "Learn when and how to use tables for tabular data, and dive deeper into semantic HTML elements for better page structure.",
                duration: "45m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 5,
                learningObjectives: [
                  "Create properly structured tables",
                  "Apply semantic HTML for page organization",
                  "Improve accessibility with ARIA attributes",
                ],
                requirements: [],
              },
              {
                id: 206,
                title: "HTML Project: Building Your First Webpage",
                description:
                  "Apply your HTML knowledge to build a complete webpage from scratch, incorporating all the elements you've learned.",
                duration: "1h 20m",
                content:
                  "This would be an assignment component in a real application.",
                type: "assignment",
                isCompleted: false,
                order: 6,
                learningObjectives: [
                  "Apply HTML concepts in a real project",
                  "Structure a complete webpage properly",
                  "Implement navigation, sections, and forms",
                ],
                requirements: ["Complete all previous HTML lessons"],
              },
              {
                id: 207,
                title: "HTML Section Assessment",
                description:
                  "Comprehensive assessment covering all HTML topics to ensure you understand the core concepts before moving on.",
                duration: "30m",
                content:
                  "This would be a quiz component in a real application.",
                type: "quiz",
                isCompleted: false,
                order: 7,
                learningObjectives: [
                  "Demonstrate mastery of HTML fundamentals",
                  "Test your knowledge of semantic HTML",
                  "Validate your understanding of forms and multimedia",
                ],
                requirements: ["Complete all HTML lessons and the project"],
              },
            ],
          },
          {
            id: 3,
            title: "CSS Fundamentals",
            description:
              "Master the fundamentals of CSS to style web pages, create responsive layouts, and design beautiful user interfaces.",
            estimatedCompletionTime: "8 hours",
            order: 3,
            lessons: [
              {
                id: 301,
                title: "Introduction to CSS",
                description:
                  "Learn the basics of CSS including selectors, properties, and values to begin styling your HTML documents.",
                duration: "40m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 1,
                learningObjectives: [
                  "Understand CSS syntax and structure",
                  "Apply CSS to HTML documents",
                  "Use different types of CSS selectors",
                ],
                requirements: ["Complete the HTML Fundamentals section"],
              },
              {
                id: 302,
                title: "CSS Box Model and Layout",
                description:
                  "Deep dive into the CSS box model, layout techniques, and positioning to control the placement of elements on the page.",
                duration: "55m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 2,
                learningObjectives: [
                  "Master the CSS box model concept",
                  "Use margin, padding, and borders effectively",
                  "Apply different positioning techniques",
                ],
                requirements: [],
              },
            ],
          },
          {
            id: 4,
            title: "JavaScript Basics",
            description:
              "Introduction to programming with JavaScript, covering core concepts, DOM manipulation, and interactive web elements.",
            estimatedCompletionTime: "10 hours",
            order: 4,
            lessons: [
              {
                id: 401,
                title: "Introduction to JavaScript",
                description:
                  "Get started with JavaScript programming, learning about variables, data types, and basic syntax.",
                duration: "50m",
                videoUrl:
                  "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
                type: "video",
                isCompleted: false,
                order: 1,
                learningObjectives: [
                  "Understand JavaScript's role in web development",
                  "Write basic JavaScript statements",
                  "Use variables and data types",
                ],
                requirements: ["Complete HTML and CSS sections"],
              },
            ],
          },
          {
            id: 5,
            title: "Advanced JavaScript",
            description:
              "Dive deeper into JavaScript with advanced concepts, asynchronous programming, and modern ES6+ features.",
            estimatedCompletionTime: "12 hours",
            order: 5,
            lessons: [],
          },
          {
            id: 6,
            title: "React Fundamentals",
            description:
              "Learn to build interactive user interfaces with React, the popular JavaScript library for UI development.",
            estimatedCompletionTime: "15 hours",
            order: 6,
            lessons: [],
          },
          {
            id: 7,
            title: "Node.js and Server-side Development",
            description:
              "Introduction to server-side programming with Node.js, Express, and MongoDB.",
            estimatedCompletionTime: "14 hours",
            order: 7,
            lessons: [],
          },
          {
            id: 8,
            title: "Final Project: Full-Stack Application",
            description:
              "Apply everything you've learned to create a complete full-stack web application from scratch.",
            estimatedCompletionTime: "20 hours",
            order: 8,
            lessons: [],
          },
        ],
        progress: {
          completed: 1,
          total: 30,
          percentage: 3,
          lastLesson: {
            id: 101,
            title: "Course Overview and Setup",
          },
        },
      };

      // Auto expand the first section
      if (course.value?.sections.length) {
        expandedSections.value = [course.value.sections[0].id];
        selectedSection.value = course.value.sections[0].id;
      }
    } catch (err) {
      console.error("Error fetching course data:", err);
      error.value = true;
    } finally {
      loading.value = false;
    }
  };

  // Calculate the total number of lessons
  const totalLessons = computed(() => {
    if (!course.value) return 0;

    return course.value.sections.reduce((acc, section) => {
      return acc + section.lessons.length;
    }, 0);
  });

  // Calculate the total time for the course
  const formatTotalDuration = computed(() => {
    return course.value?.totalDuration || "0h 0m";
  });

  // Toggle section expansion
  const toggleSection = (sectionId: number) => {
    const index = expandedSections.value.indexOf(sectionId);
    if (index === -1) {
      expandedSections.value.push(sectionId);
    } else {
      expandedSections.value.splice(index, 1);
    }
  };

  // Check if a section is expanded
  const isSectionExpanded = (sectionId: number) => {
    return expandedSections.value.includes(sectionId);
  };

  // Get the first lesson of the course
  const getFirstLesson = () => {
    if (!course.value || !course.value.sections.length) return null;

    const firstSection = course.value.sections.sort(
      (a, b) => a.order - b.order
    )[0];
    if (!firstSection.lessons.length) return null;

    return firstSection.lessons.sort((a, b) => a.order - b.order)[0];
  };

  // Get all lessons of the course in order
  const getAllLessons = () => {
    if (!course.value) return [];

    const allLessons = [];
    const sortedSections = [...course.value.sections].sort(
      (a, b) => a.order - b.order
    );

    for (const section of sortedSections) {
      const sortedLessons = [...section.lessons].sort(
        (a, b) => a.order - b.order
      );
      allLessons.push(...sortedLessons);
    }

    return allLessons;
  };

  // Format the lesson type for display
  const formatLessonType = (type: string) => {
    return type.charAt(0).toUpperCase() + type.slice(1);
  };

  // Select a section to show in the roadmap
  const selectSection = (sectionId: number) => {
    selectedSection.value = sectionId;

    // Make sure the section is expanded
    if (!expandedSections.value.includes(sectionId)) {
      expandedSections.value.push(sectionId);
    }

    // Scroll to the section in the curriculum tab
    setTimeout(() => {
      const element = document.getElementById(`section-${sectionId}`);
      if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }, 100);
  };

  // Get the selected section data
  const selectedSectionData = computed(() => {
    if (!course.value || !selectedSection.value) return null;

    return (
      course.value.sections.find(
        (section) => section.id === selectedSection.value
      ) || null
    );
  });

  // Format the course update date
  const formattedUpdateDate = computed(() => {
    if (!course.value?.updatedAt) return "";

    return new Date(course.value.updatedAt).toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  });

  const startCourse = () => {
    if (!course.value) return;
    // Navigate to the first lesson
    const firstLesson = getFirstLesson();
    if (firstLesson) {
      navigateTo(`/courses/${courseId}/learn/${firstLesson.id}`);
    }
  };

  const continueCourse = () => {
    if (!course.value) return;
    // Find the first incomplete lesson
    const firstIncompleteLesson = course.value.sections
      .flatMap((section) => section.lessons)
      .find((lesson) => !lesson.isCompleted);

    if (firstIncompleteLesson) {
      navigateTo(`/courses/${courseId}/learn/${firstIncompleteLesson.id}`);
    }
  };

  const startLesson = (lessonId: number) => {
    if (!course.value) return;
    navigateTo(`/courses/${courseId}/learn/${lessonId}`);
  };

  return {
    // State
    course,
    loading,
    error,
    activeTab,
    expandedSections,
    selectedSection,
    faqs,

    // Computed
    totalLessons,
    formatTotalDuration,
    formattedUpdateDate,
    selectedSectionData,

    // Methods
    fetchCourseData,
    toggleSection,
    isSectionExpanded,
    getFirstLesson,
    getAllLessons,
    formatLessonType,
    selectSection,
    startCourse,
    continueCourse,
    startLesson,
  };
};
