import { ref, computed } from "vue";
import type {
  Course,
  CourseSection,
  CourseLesson,
  CourseInstructor,
  CourseReview,
} from "~/types/course";

// Extended types for the course details page
interface ExtendedCourseLesson extends CourseLesson {
  isPreview?: boolean;
}

interface ExtendedCourseSection extends CourseSection {
  totalDuration: string;
  lessons: ExtendedCourseLesson[];
}

interface ExtendedCourseInstructor extends CourseInstructor {
  role: string;
}

interface ExtendedCourseReview extends CourseReview {
  user: {
    name: string;
    avatar: string;
  };
}

interface ExtendedCourse extends Course {
  subtitle: string;
  subCategory: string;
  originalPrice: number;
  price: number;
  image: string;
  reviewCount: number;
  enrollmentCount: number;
  createdAt: string;
  lastUpdated: string;
  language: string;
  features: string[];
  whatYouWillLearn: string[];
  sections: ExtendedCourseSection[];
  instructors: ExtendedCourseInstructor[];
  reviews: ExtendedCourseReview[];
  badges: string[];
  isEnrolled: boolean;
  totalLessons: number;
  progress: {
    completed: number;
    total: number;
    percentage: number;
    lastLesson?: {
      id: number;
      title: string;
    };
  };
  studentsCount: number;
  updatedAt: string;
  objectives: string[];
  requirements: string[];
  instructor: {
    name: string;
    title: string;
    avatar: string;
    bio: string;
  };
  tags: string[];
  totalDuration: string;
  rating: number;
}

export function useCourseDetails(courseId: number) {
  // State
  const course = ref<ExtendedCourse | null>(null);
  const loading = ref(true);
  const error = ref(false);
  const activeTab = ref("overview");

  // Fetch course data
  const fetchCourseData = async () => {
    loading.value = true;
    error.value = false;

    try {
      // Simulating API call with timeout
      await new Promise((resolve) => setTimeout(resolve, 1000));

      // Mock course data
      course.value = {
        id: courseId,
        title: "Advanced Web Development",
        subtitle: "Master modern web development techniques",
        description:
          "Learn advanced web development techniques including modern frameworks, state management, and deployment strategies.",
        category: "Web Development",
        subCategory: "Full-Stack",
        level: "Intermediate",
        language: "English",
        originalPrice: 199.99,
        price: 149.99,
        image: "/images/courses/c3.jpg",
        reviewCount: 128,
        enrollmentCount: 1250,
        createdAt: "2024-01-15",
        lastUpdated: "2024-03-01",
        studentsCount: 1250,
        updatedAt: "2024-03-01",
        objectives: [
          "Master advanced Vue.js concepts",
          "Build scalable applications",
          "Implement best practices",
          "Learn state management with Pinia",
          "Master TypeScript in Vue applications",
          "Understand component composition patterns",
          "Implement advanced routing techniques",
          "Build reusable component libraries",
        ],
        features: [
          "Lifetime access",
          "Certificate of completion",
          "30-day money-back guarantee",
          "Access on mobile and TV",
          "Downloadable resources",
          "Live coding sessions",
          "1-on-1 mentoring sessions",
          "Private Discord community",
          "Code reviews",
          "Project feedback",
          "Resume review",
          "Interview preparation",
          "Career guidance",
        ],
        instructor: {
          name: "John Doe",
          title: "Senior Web Developer",
          avatar: "/images/instructors/i2.jpg",
          bio: "10+ years of experience in web development",
        },
        tags: ["JavaScript", "Vue.js", "Node.js", "Web Development"],
        totalDuration: "25h 30m",
        rating: 4.8,
        requirements: [
          "Basic knowledge of HTML, CSS, and JavaScript",
          "Familiarity with web development concepts",
          "A computer with internet access",
          "Understanding of basic Vue.js concepts",
          "Node.js installed on your computer",
          "Git version control basics",
          "Basic command line knowledge",
          "Text editor or IDE installed",
        ],
        whatYouWillLearn: [
          "Build responsive web applications",
          "Implement modern JavaScript features",
          "Work with REST APIs",
          "Use state management solutions",
          "Deploy applications to production",
          "Write clean, maintainable code",
          "Implement automated testing",
          "Use Git for version control",
          "Work with databases",
          "Implement authentication and authorization",
          "Handle form validation and submission",
          "Optimize application performance",
          "Implement real-time features with WebSockets",
          "Create progressive web applications",
          "Use modern build tools and bundlers",
        ],
        sections: [
          {
            id: 1,
            title: "Introduction to Web Development",
            description:
              "Get started with the fundamentals of web development and set up your development environment.",
            totalDuration: "3h 15m",
            order: 1,
            estimatedCompletionTime: "3 hours",
            lessons: [
              {
                id: 101,
                title: "Course Overview and Setup",
                duration: "15m",
                type: "video",
                isPreview: true,
                isCompleted: false,
                order: 1,
                description:
                  "Introduction to the course structure and learning pathway",
                learningObjectives: [
                  "Understand course structure",
                  "Set up development environment",
                ],
                requirements: [],
              },
              {
                id: 102,
                title: "How the Web Works: A High-Level Overview",
                duration: "25m",
                type: "video",
                isPreview: true,
                isCompleted: false,
                order: 2,
                description:
                  "A comprehensive overview of how the internet works",
                learningObjectives: [
                  "Understand client-server model",
                  "Learn HTTP basics",
                ],
                requirements: [],
              },
              {
                id: 103,
                title: "Setting Up Your Development Environment",
                duration: "30m",
                type: "video",
                isCompleted: false,
                order: 3,
                description:
                  "A step-by-step guide to installing and configuring development tools",
                learningObjectives: [
                  "Install VS Code",
                  "Set up browser developer tools",
                ],
                requirements: [],
              },
              {
                id: 104,
                title: "Web Developer Tools and Resources",
                duration: "45m",
                type: "video",
                isCompleted: false,
                order: 4,
                description:
                  "Introduction to essential developer tools and resources",
                learningObjectives: [
                  "Master Chrome DevTools",
                  "Discover key web development resources",
                ],
                requirements: [],
              },
              {
                id: 105,
                title: "Introduction Quiz",
                duration: "20m",
                type: "quiz",
                isCompleted: false,
                order: 5,
                description:
                  "Test your understanding of the key concepts covered in the introduction section",
                content:
                  "This would be a quiz component in a real application.",
                learningObjectives: [
                  "Validate understanding of basic web concepts",
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
            totalDuration: "5h 45m",
            order: 2,
            estimatedCompletionTime: "6 hours",
            lessons: [
              {
                id: 201,
                title: "HTML Basics and Document Structure",
                duration: "40m",
                type: "video",
                isPreview: true,
                isCompleted: false,
                order: 1,
                description:
                  "Learn the fundamental structure of HTML documents",
                learningObjectives: [
                  "Understand HTML document structure",
                  "Learn semantic elements",
                ],
                requirements: [],
              },
              {
                id: 202,
                title: "Working with Text Elements",
                duration: "35m",
                type: "video",
                isCompleted: false,
                order: 2,
                description: "Learn how to work with text elements in HTML",
                learningObjectives: [
                  "Format text with HTML elements",
                  "Create headings and paragraphs",
                ],
                requirements: [],
              },
              {
                id: 203,
                title: "Links, Images, and Multimedia",
                duration: "50m",
                type: "video",
                isCompleted: false,
                order: 3,
                description:
                  "Learn how to add links, images, and multimedia to your web pages",
                learningObjectives: [
                  "Create hyperlinks",
                  "Add images and media",
                ],
                requirements: [],
              },
              {
                id: 204,
                title: "HTML Forms and Input Elements",
                duration: "55m",
                type: "video",
                isCompleted: false,
                order: 4,
                description:
                  "Learn how to create forms and input elements in HTML",
                learningObjectives: ["Create forms", "Use various input types"],
                requirements: [],
              },
              {
                id: 205,
                title: "Tables and Semantic HTML",
                duration: "45m",
                type: "video",
                isCompleted: false,
                order: 5,
                description:
                  "Learn how to create tables and use semantic HTML elements",
                learningObjectives: ["Create tables", "Use semantic HTML"],
                requirements: [],
              },
              {
                id: 206,
                title: "HTML Project: Building Your First Webpage",
                duration: "1h 20m",
                type: "assignment",
                isCompleted: false,
                order: 6,
                description:
                  "Apply what you've learned to build your first webpage",
                learningObjectives: [
                  "Build a complete webpage",
                  "Apply HTML concepts",
                ],
                requirements: ["Complete all previous lessons in this section"],
              },
              {
                id: 207,
                title: "HTML Section Assessment",
                duration: "30m",
                type: "quiz",
                isCompleted: false,
                order: 7,
                description: "Test your understanding of HTML concepts",
                learningObjectives: ["Validate HTML knowledge"],
                requirements: ["Complete all previous lessons in this section"],
              },
            ],
          },
          {
            id: 3,
            title: "CSS Styling",
            description:
              "Learn how to style web pages with CSS to create beautiful, responsive layouts.",
            totalDuration: "8h 20m",
            order: 3,
            estimatedCompletionTime: "8 hours",
            lessons: [
              {
                id: 301,
                title: "CSS Basics and Selectors",
                duration: "45m",
                type: "video",
                isCompleted: false,
                order: 1,
                description: "Learn the basics of CSS and how to use selectors",
                learningObjectives: [
                  "Understand CSS syntax",
                  "Use various selectors",
                ],
                requirements: [],
              },
              {
                id: 302,
                title: "Colors, Typography, and Text Styling",
                duration: "40m",
                type: "video",
                isCompleted: false,
                order: 2,
                description: "Learn how to style text and use colors in CSS",
                learningObjectives: ["Style text with CSS", "Work with colors"],
                requirements: [],
              },
              {
                id: 303,
                title: "The Box Model and Layout",
                duration: "55m",
                type: "video",
                isCompleted: false,
                order: 3,
                description:
                  "Learn about the box model and basic layout techniques",
                learningObjectives: [
                  "Understand the box model",
                  "Create basic layouts",
                ],
                requirements: [],
              },
              {
                id: 304,
                title: "Flexbox and Grid Systems",
                duration: "1h 15m",
                type: "video",
                isCompleted: false,
                order: 4,
                description:
                  "Learn how to use Flexbox and Grid for advanced layouts",
                learningObjectives: [
                  "Use Flexbox for layouts",
                  "Create Grid layouts",
                ],
                requirements: [],
              },
              {
                id: 305,
                title: "CSS Transitions and Animations",
                duration: "50m",
                type: "video",
                isCompleted: false,
                order: 5,
                description:
                  "Learn how to create transitions and animations with CSS",
                learningObjectives: ["Create transitions", "Build animations"],
                requirements: [],
              },
              {
                id: 306,
                title: "Responsive Design and Media Queries",
                duration: "1h",
                type: "video",
                isCompleted: false,
                order: 6,
                description:
                  "Learn how to create responsive designs with media queries",
                learningObjectives: [
                  "Create responsive layouts",
                  "Use media queries",
                ],
                requirements: [],
              },
              {
                id: 307,
                title: "CSS Frameworks: Introduction to Bootstrap and Tailwind",
                duration: "1h 30m",
                type: "video",
                isCompleted: false,
                order: 7,
                description: "Learn how to use popular CSS frameworks",
                learningObjectives: ["Use Bootstrap", "Work with Tailwind CSS"],
                requirements: [],
              },
              {
                id: 308,
                title: "CSS Project: Styling Your Portfolio Website",
                duration: "1h 25m",
                type: "assignment",
                isCompleted: false,
                order: 8,
                description:
                  "Apply what you've learned to style a portfolio website",
                learningObjectives: [
                  "Style a complete website",
                  "Apply CSS concepts",
                ],
                requirements: ["Complete all previous lessons in this section"],
              },
              {
                id: 309,
                title: "CSS Mastery Quiz",
                duration: "30m",
                type: "quiz",
                isCompleted: false,
                order: 9,
                description: "Test your understanding of CSS concepts",
                learningObjectives: ["Validate CSS knowledge"],
                requirements: ["Complete all previous lessons in this section"],
              },
            ],
          },
          {
            id: 4,
            title: "JavaScript Fundamentals",
            description:
              "Master core JavaScript concepts and modern ES6+ features.",
            totalDuration: "10h 30m",
            order: 4,
            estimatedCompletionTime: "11 hours",
            lessons: [
              {
                id: 401,
                title: "JavaScript Basics and Variables",
                duration: "45m",
                type: "video",
                isCompleted: false,
                order: 1,
                description: "Learn the fundamentals of JavaScript programming",
                learningObjectives: [
                  "Understand variables",
                  "Work with data types",
                ],
                requirements: [],
              },
              {
                id: 402,
                title: "Functions and Scope",
                duration: "1h",
                type: "video",
                isCompleted: false,
                order: 2,
                description:
                  "Learn about functions and variable scope in JavaScript",
                learningObjectives: ["Create functions", "Understand scope"],
                requirements: [],
              },
              {
                id: 403,
                title: "Arrays and Objects",
                duration: "1h 15m",
                type: "video",
                isCompleted: false,
                order: 3,
                description: "Work with complex data structures in JavaScript",
                learningObjectives: ["Manipulate arrays", "Work with objects"],
                requirements: [],
              },
              {
                id: 404,
                title: "ES6+ Features",
                duration: "1h 30m",
                type: "video",
                isCompleted: false,
                order: 4,
                description: "Learn modern JavaScript features",
                learningObjectives: [
                  "Use arrow functions",
                  "Work with destructuring",
                ],
                requirements: [],
              },
            ],
          },
          {
            id: 5,
            title: "Vue.js Fundamentals",
            description: "Learn the core concepts of Vue.js framework",
            totalDuration: "12h 45m",
            order: 5,
            estimatedCompletionTime: "13 hours",
            lessons: [
              {
                id: 501,
                title: "Introduction to Vue.js",
                duration: "1h",
                type: "video",
                isCompleted: false,
                order: 1,
                description: "Get started with Vue.js framework",
                learningObjectives: [
                  "Understand Vue.js basics",
                  "Create first app",
                ],
                requirements: [],
              },
              {
                id: 502,
                title: "Components and Props",
                duration: "1h 30m",
                type: "video",
                isCompleted: false,
                order: 2,
                description: "Learn component-based development in Vue.js",
                learningObjectives: ["Create components", "Work with props"],
                requirements: [],
              },
              {
                id: 503,
                title: "State Management",
                duration: "2h",
                type: "video",
                isCompleted: false,
                order: 3,
                description: "Master state management in Vue.js applications",
                learningObjectives: ["Use Pinia", "Manage application state"],
                requirements: [],
              },
            ],
          },
          {
            id: 6,
            title: "Advanced Vue.js Concepts",
            description: "Dive deep into advanced Vue.js features and patterns",
            totalDuration: "15h 20m",
            order: 6,
            estimatedCompletionTime: "16 hours",
            lessons: [
              {
                id: 601,
                title: "Composition API",
                duration: "2h",
                type: "video",
                isCompleted: false,
                order: 1,
                description: "Master the Composition API in Vue.js",
                learningObjectives: [
                  "Use composables",
                  "Understand reactivity",
                ],
                requirements: [],
              },
              {
                id: 602,
                title: "TypeScript Integration",
                duration: "1h 45m",
                type: "video",
                isCompleted: false,
                order: 2,
                description: "Learn how to use TypeScript with Vue.js",
                learningObjectives: [
                  "Add type safety",
                  "Use TypeScript features",
                ],
                requirements: [],
              },
              {
                id: 603,
                title: "Performance Optimization",
                duration: "1h 30m",
                type: "video",
                isCompleted: false,
                order: 3,
                description: "Optimize Vue.js application performance",
                learningObjectives: [
                  "Improve performance",
                  "Implement best practices",
                ],
                requirements: [],
              },
            ],
          },
        ],
        instructors: [
          {
            id: 1,
            name: "Alex Johnson",
            avatar: "/images/instructors/i1.jpg",
            role: "Senior Web Developer & Instructor",
            bio: "Alex has over 10 years of experience in web development and has worked with companies like Google and Microsoft. He specializes in modern JavaScript frameworks and has helped over 50,000 students master web development skills.",
            courses: 12,
            students: 58750,
            rating: 4.9,
          },
          {
            id: 2,
            name: "Sarah Chen",
            avatar: "/images/instructors/i3.jpg",
            role: "UX Designer & Frontend Developer",
            bio: "Sarah combines her design background with frontend development expertise to teach the principles of beautiful, functional web design. She has worked with numerous startups and Fortune 500 companies to create engaging user experiences.",
            courses: 8,
            students: 32450,
            rating: 4.8,
          },
          {
            id: 3,
            name: "Marcus Rodriguez",
            avatar: "/images/instructors/i4.jpg",
            role: "Full Stack Developer & DevOps Engineer",
            bio: "Marcus specializes in full-stack development and DevOps practices. With 8 years of experience at Amazon and Netflix, he brings real-world expertise in building and deploying scalable applications.",
            courses: 5,
            students: 28900,
            rating: 4.9,
          },
          {
            id: 4,
            name: "Emily Zhang",
            avatar: "/images/instructors/i5.jpg",
            role: "Software Architect & Tech Lead",
            bio: "Emily is a software architect with extensive experience in designing large-scale applications. She has led development teams at Facebook and Stripe, focusing on performance optimization and scalable architecture.",
            courses: 6,
            students: 35600,
            rating: 4.7,
          },
        ],
        reviews: [
          {
            id: 1,
            user: {
              name: "Michael P.",
              avatar: "/images/instructors/i3.jpg",
            },
            rating: 5,
            date: "2024-02-15",
            content:
              "This course exceeded my expectations! I had zero coding experience, and now I'm building full-stack applications. The projects are practical and relevant to real-world scenarios. Highly recommend!",
            helpfulCount: 42,
          },
          {
            id: 2,
            user: {
              name: "Jennifer K.",
              avatar: "/images/instructors/i2.jpg",
            },
            rating: 4,
            date: "2024-01-28",
            content:
              "Very comprehensive coverage of web development technologies. The sections on JavaScript were particularly helpful. I only wish there was a bit more on testing frameworks, but otherwise excellent.",
            helpfulCount: 18,
          },
          {
            id: 3,
            user: {
              name: "David T.",
              avatar: "/images/instructors/i1.jpg",
            },
            rating: 5,
            date: "2023-12-10",
            content:
              "I've taken several coding courses online, and this is by far the best one. The instructors explain complex concepts in an easy-to-understand way, and the hands-on projects really cement your learning.",
            helpfulCount: 35,
          },
          {
            id: 4,
            user: {
              name: "Rachel S.",
              avatar: "/images/instructors/i4.jpg",
            },
            rating: 5,
            date: "2024-03-01",
            content:
              "The course structure is excellent, and the progression from basic to advanced topics is well thought out. The instructors are clearly experts in their field and provide great insights from their real-world experience.",
            helpfulCount: 28,
          },
          {
            id: 5,
            user: {
              name: "Thomas H.",
              avatar: "/images/instructors/i5.jpg",
            },
            rating: 4,
            date: "2024-02-20",
            content:
              "Great course with lots of practical examples. The section on state management and component composition was particularly enlightening. Would love to see more advanced TypeScript patterns covered.",
            helpfulCount: 22,
          },
          {
            id: 6,
            user: {
              name: "Lisa M.",
              avatar: "/images/instructors/i6.jpg",
            },
            rating: 5,
            date: "2024-01-15",
            content:
              "The project-based learning approach really works well. Building real applications while learning new concepts helped me understand how everything fits together. The code reviews and feedback were invaluable.",
            helpfulCount: 31,
          },
        ],
        badges: ["Best Seller", "Hot & New"],
        isEnrolled: false,
        totalLessons: 45,
        progress: {
          completed: 0,
          total: 45,
          percentage: 0,
        },
      };
    } catch (err) {
      console.error("Error fetching course data:", err);
      error.value = true;
    } finally {
      loading.value = false;
    }
  };

  // Handle tab switching
  const setActiveTab = (tab: string) => {
    activeTab.value = tab;
  };

  // Enrollment handling
  const enrollInCourse = () => {
    if (!course.value) return;

    // In a real app, this would make an API call to enroll
    course.value.isEnrolled = true;
    course.value.progress = {
      completed: 0,
      total: course.value.totalLessons,
      percentage: 0,
    };

    // Redirect to course content or first lesson
    // navigateTo(`/courses/${course.value.id}/learn`)
  };

  // Computed properties
  const discountPercentage = computed(() => {
    if (!course.value) return 0;
    return Math.round(
      ((course.value.originalPrice - course.value.price) /
        course.value.originalPrice) *
        100
    );
  });

  const formattedPrice = computed(() => {
    if (!course.value) return "$0.00";
    return `$${course.value.price.toFixed(2)}`;
  });

  const formattedOriginalPrice = computed(() => {
    if (!course.value) return "$0.00";
    return `$${course.value.originalPrice.toFixed(2)}`;
  });

  const formattedRating = computed(() => {
    if (!course.value) return "0.0";
    return course.value.rating.toFixed(1);
  });

  const formattedReviewCount = computed(() => {
    if (!course.value) return "0";
    return course.value.reviewCount.toLocaleString();
  });

  const formattedEnrollmentCount = computed(() => {
    if (!course.value) return "0";
    return course.value.enrollmentCount.toLocaleString();
  });

  const formattedLastUpdated = computed(() => {
    if (!course.value) return "";
    return new Date(course.value.lastUpdated).toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  });

  return {
    // State
    course,
    loading,
    error,
    activeTab,

    // Methods
    fetchCourseData,
    setActiveTab,
    enrollInCourse,

    // Computed
    discountPercentage,
    formattedPrice,
    formattedOriginalPrice,
    formattedRating,
    formattedReviewCount,
    formattedEnrollmentCount,
    formattedLastUpdated,
  };
}
