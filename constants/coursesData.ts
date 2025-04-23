
export const coursesData = [
  {
    id: 1,
    title: "Complete Web Development Bootcamp",
    category: "Web Development",
    image: "/images/courses/c1.jpg",
    instructor: "Alex Johnson",
    rating: 4.8,
    reviewCount: 1245,
    price: 89.99,
    originalPrice: 199.99,
    level: "All Levels",
    duration: "42h 30m",
    badges: ["Bestseller", "Top Rated"],
  },
  {
    id: 2,
    title: "Advanced JavaScript: From Fundamentals to Functional JS",
    category: "JavaScript",
    image: "/images/courses/c2.jpg",
    instructor: "Sarah Chen",
    rating: 4.9,
    reviewCount: 982,
    price: 79.99,
    originalPrice: 149.99,
    level: "Intermediate",
    duration: "28h 15m",
    badges: ["Hot & New"],
  },
  {
    id: 3,
    title: "Flutter & Dart: The Complete Guide",
    category: "Mobile Development",
    image: "/images/courses/c3.jpg",
    instructor: "Mark Williams",
    rating: 4.7,
    reviewCount: 843,
    price: 94.99,
    originalPrice: 189.99,
    level: "Beginner",
    duration: "32h 45m",
    badges: ["Bestseller"],
  },
  {
    id: 4,
    title: "Machine Learning A-Z: Hands-On Python & R",
    category: "Data Science",
    image: "/images/courses/c1.jpg",
    instructor: "Jessica Taylor",
    rating: 4.6,
    reviewCount: 1532,
    price: 129.99,
    originalPrice: 199.99,
    level: "Advanced",
    duration: "45h 20m",
    badges: ["Highest Rated"],
  },
  {
    id: 5,
    title: "The Complete React Developer Course",
    category: "Web Development",
    image: "/images/courses/c2.jpg",
    instructor: "Michael Brown",
    rating: 4.8,
    reviewCount: 1089,
    price: 84.99,
    originalPrice: 169.99,
    level: "Intermediate",
    duration: "30h 10m",
    badges: ["Bestseller"],
  },
  {
    id: 6,
    title: "UX & UI Design Masterclass",
    category: "Design",
    image: "/images/courses/c3.jpg",
    instructor: "Emma Wilson",
    rating: 4.7,
    reviewCount: 756,
    price: 69.99,
    originalPrice: 139.99,
    level: "All Levels",
    duration: "24h 30m",
  },
  {
    id: 7,
    title: "AWS Certified Solutions Architect",
    category: "Cloud Computing",
    image: "/images/courses/c1.jpg",
    instructor: "David Miller",
    rating: 4.9,
    reviewCount: 1342,
    price: 119.99,
    originalPrice: 229.99,
    level: "Advanced",
    duration: "38h 15m",
    badges: ["Highest Rated", "Bestseller"],
  },
  {
    id: 8,
    title: "Python for Data Science and Machine Learning",
    category: "Data Science",
    image: "/images/courses/c2.jpg",
    instructor: "Rebecca Lee",
    rating: 4.8,
    reviewCount: 982,
    price: 89.99,
    originalPrice: 179.99,
    level: "Intermediate",
    duration: "36h 45m",
    badges: ["Top Rated"],
  },
  {
    id: 9,
    title: "The Complete Digital Marketing Course",
    category: "Marketing",
    image: "/images/courses/c3.jpg",
    instructor: "Thomas Clark",
    rating: 4.6,
    reviewCount: 1120,
    price: 94.99,
    originalPrice: 189.99,
    level: "Beginner",
    duration: "32h 15m",
  },
  {
    id: 10,
    title: "iOS App Development with Swift",
    category: "Mobile Development",
    image: "/images/courses/c1.jpg",
    instructor: "Jennifer Roberts",
    rating: 4.7,
    reviewCount: 876,
    price: 99.99,
    originalPrice: 199.99,
    level: "Intermediate",
    duration: "34h 20m",
  },
  {
    id: 11,
    title: "Advanced CSS and Sass: Flexbox, Grid, Animations",
    category: "Web Development",
    image: "/images/courses/c2.jpg",
    instructor: "Alex Johnson",
    rating: 4.9,
    reviewCount: 1245,
    price: 74.99,
    originalPrice: 149.99,
    level: "Intermediate",
    duration: "28h 30m",
    badges: ["Top Rated"],
  },
  {
    id: 12,
    title: "Full-Stack JavaScript: Node.js, Express, MongoDB",
    category: "Web Development",
    image: "/images/courses/c3.jpg",
    instructor: "Michael Brown",
    rating: 4.8,
    reviewCount: 932,
    price: 89.99,
    originalPrice: 179.99,
    level: "Intermediate",
    duration: "32h 45m",
    badges: ["Bestseller"],
  },
];

export const popularCategoriesData = [
  {
    name: "Web Development",
    count: 425,
    image: "/images/courses/c1.jpg",
  },
  {
    name: "Data Science",
    count: 320,
    image: "/images/courses/c2.jpg",
  },
  {
    name: "Mobile Development",
    count: 275,
    image: "/images/courses/c3.jpg",
  },
  {
    name: "Design",
    count: 245,
    image: "/images/courses/c1.jpg",
  },
  {
    name: "Marketing",
    count: 180,
    image: "/images/courses/c2.jpg",
  },
  {
    name: "Cloud Computing",
    count: 165,
    image: "/images/courses/c3.jpg",
  },
];

export const allCoursesData = [
  {
    id: 1,
    title: "Complete Web Development Bootcamp",
    category: "Web Development",
    subcategory: "Full-Stack",
    image: "/images/courses/c3.jpg",
    instructor: "Alex Johnson",
    rating: 4.8,
    reviewCount: 1245,
    price: 89.99,
    originalPrice: 199.99,
    level: "All Levels",
    duration: "42h 30m",
    badges: ["Bestseller", "Top Rated"],
    description:
      "A comprehensive bootcamp covering HTML, CSS, JavaScript, Node.js, and more to turn you into a full-stack web developer.",
  },
  {
    id: 5,
    title: "The Complete React Developer Course",
    category: "Web Development",
    subcategory: "Frontend",
    image: "/images/courses/c2.jpg",
    instructor: "Michael Brown",
    rating: 4.8,
    reviewCount: 1089,
    price: 84.99,
    originalPrice: 169.99,
    level: "Intermediate",
    duration: "30h 10m",
    badges: ["Bestseller"],
    description:
      "Learn React by building real projects. Master modern React features including hooks, context API, and Redux.",
  },
  {
    id: 11,
    title: "Advanced CSS and Sass: Flexbox, Grid, Animations",
    category: "Web Development",
    subcategory: "Frontend",
    image: "/images/courses/c1.jpg",
    instructor: "Alex Johnson",
    rating: 4.9,
    reviewCount: 1245,
    price: 74.99,
    originalPrice: 149.99,
    level: "Intermediate",
    duration: "28h 30m",
    badges: ["Top Rated"],
    description:
      "Take your CSS skills to the next level. Master flexbox, CSS Grid, responsive design, and advanced animations.",
  },
  {
    id: 12,
    title: "Full-Stack JavaScript: Node.js, Express, MongoDB",
    category: "Web Development",
    subcategory: "Backend",
    image: "/images/courses/c3.jpg",
    instructor: "Michael Brown",
    rating: 4.8,
    reviewCount: 932,
    price: 89.99,
    originalPrice: 179.99,
    level: "Intermediate",
    duration: "32h 45m",
    badges: ["Bestseller"],
    description:
      "Build complete backend applications with Node.js, Express, and MongoDB. Includes authentication, API design, and deployment.",
  },
  {
    id: 13,
    title: "Vue.js - The Complete Guide",
    category: "Web Development",
    subcategory: "Frontend",
    image: "/images/courses/c2.jpg",
    instructor: "Sarah Chen",
    rating: 4.9,
    reviewCount: 876,
    price: 79.99,
    originalPrice: 159.99,
    level: "All Levels",
    duration: "26h 15m",
    badges: ["Bestseller"],
    description:
      "Master Vue.js from the ground up. Learn Vue 3, Vuex, Vue Router, and how to build single-page applications.",
  },
  {
    id: 14,
    title: "PHP & MySQL - Complete Web Development Course",
    category: "Web Development",
    subcategory: "Backend",
    image: "/images/courses/c1.jpg",
    instructor: "David Miller",
    rating: 4.6,
    reviewCount: 732,
    price: 69.99,
    originalPrice: 139.99,
    level: "Beginner",
    duration: "34h 20m",
    badges: [],
    description:
      "Learn PHP and MySQL to build dynamic, database-driven websites. Includes practical projects and security best practices.",
  },
  {
    id: 15,
    title: "Responsive Web Design Masterclass",
    category: "Web Development",
    subcategory: "Frontend",
    image: "/images/courses/c3.jpg",
    instructor: "Emma Wilson",
    rating: 4.7,
    reviewCount: 654,
    price: 59.99,
    originalPrice: 119.99,
    level: "All Levels",
    duration: "18h 45m",
    badges: [],
    description:
      "Create websites that look great on any device. Master media queries, flexbox, CSS Grid, and responsive images.",
  },
  {
    id: 16,
    title: "Angular - The Complete Guide",
    category: "Web Development",
    subcategory: "Frontend",
    image: "/images/courses/c2.jpg",
    instructor: "Thomas Clark",
    rating: 4.8,
    reviewCount: 921,
    price: 84.99,
    originalPrice: 169.99,
    level: "Intermediate",
    duration: "33h 30m",
    badges: ["Top Rated"],
    description:
      "Comprehensive course on Angular framework. Learn to build sophisticated SPAs with TypeScript and Angular.",
  },
  {
    id: 17,
    title: "Modern JavaScript From The Beginning",
    category: "Web Development",
    subcategory: "JavaScript",
    image: "/images/courses/c1.jpg",
    instructor: "Alex Johnson",
    rating: 4.9,
    reviewCount: 1184,
    price: 69.99,
    originalPrice: 139.99,
    level: "All Levels",
    duration: "21h 50m",
    badges: ["Bestseller"],
    description:
      "Learn modern JavaScript from the fundamentals to advanced topics like ES6+, asynchronous JS, and the DOM.",
  },
  {
    id: 18,
    title: "GraphQL with Node.js - The Complete Guide",
    category: "Web Development",
    subcategory: "Backend",
    image: "/images/courses/c3.jpg",
    instructor: "Sarah Chen",
    rating: 4.7,
    reviewCount: 687,
    price: 79.99,
    originalPrice: 159.99,
    level: "Intermediate",
    duration: "24h 15m",
    badges: ["Hot & New"],
    description:
      "Master GraphQL API development with Node.js. Learn schema design, resolvers, authentication, and performance optimization.",
  },
  {
    id: 19,
    title: "Web Accessibility - Build Inclusive Websites",
    category: "Web Development",
    subcategory: "Frontend",
    image: "/images/courses/c2.jpg",
    instructor: "Emma Wilson",
    rating: 4.8,
    reviewCount: 532,
    price: 64.99,
    originalPrice: 129.99,
    level: "Intermediate",
    duration: "16h 20m",
    badges: [],
    description:
      "Learn how to make websites accessible to everyone. Master WCAG guidelines, ARIA, keyboard navigation, and testing.",
  },
  {
    id: 20,
    title: "TypeScript for Web Developers",
    category: "Web Development",
    subcategory: "JavaScript",
    image: "/images/courses/c1.jpg",
    instructor: "Michael Brown",
    rating: 4.9,
    reviewCount: 743,
    price: 74.99,
    originalPrice: 149.99,
    level: "Intermediate",
    duration: "19h 45m",
    badges: ["Top Rated"],
    description:
      "Comprehensive TypeScript course for web developers. Learn type safety, interfaces, generics, and integration with frameworks.",
  },
];

export const categoryInfoData = {
  "web-development": {
    title: "Web Development Courses",
    description:
      "Master the skills needed to build modern, responsive websites and web applications. Learn frontend technologies like HTML, CSS, and JavaScript, as well as backend development with Node.js, Python, PHP, and more.",
    image: "/images/courses/c3.jpg",
    subcategories: [
      "Frontend",
      "Backend",
      "Full-Stack",
      "JavaScript",
      "React",
      "Vue.js",
      "Angular",
      "Node.js",
      "PHP",
    ],
    stats: [
      { label: "Students", value: "425K+" },
      { label: "Courses", value: 150 },
      { label: "Instructors", value: 48 },
      { label: "Job Growth", value: "23%" },
    ],
  },
  "data-science": {
    title: "Data Science Courses",
    description:
      "Learn to analyze complex data, build machine learning models, and derive meaningful insights. Courses cover Python, R, statistics, visualization, and AI techniques to help you excel in this high-demand field.",
    image: "/images/courses/c2.jpg",
    subcategories: [
      "Machine Learning",
      "Deep Learning",
      "Python",
      "R",
      "Data Analysis",
      "Data Visualization",
      "Statistics",
    ],
    stats: [
      { label: "Students", value: "320K+" },
      { label: "Courses", value: 120 },
      { label: "Instructors", value: 35 },
      { label: "Job Growth", value: "31%" },
    ],
  },
  "mobile-development": {
    title: "Mobile Development Courses",
    description:
      "Build native and cross-platform mobile applications for iOS and Android. Master frameworks like React Native, Flutter, Swift, and Kotlin to create engaging mobile experiences.",
    image: "/images/courses/c1.jpg",
    subcategories: [
      "iOS",
      "Android",
      "React Native",
      "Flutter",
      "Swift",
      "Kotlin",
    ],
    stats: [
      { label: "Students", value: "275K+" },
      { label: "Courses", value: 80 },
      { label: "Instructors", value: 28 },
      { label: "Job Growth", value: "20%" },
    ],
  },
  design: {
    title: "Design Courses",
    description:
      "Develop your creative skills and learn industry-standard design tools and principles. Courses cover UI/UX design, graphic design, web design, and more to help you create beautiful digital experiences.",
    image: "/images/courses/c3.jpg",
    subcategories: [
      "UI/UX",
      "Graphic Design",
      "Web Design",
      "Adobe Creative Suite",
      "Figma",
      "Sketch",
    ],
    stats: [
      { label: "Students", value: "245K+" },
      { label: "Courses", value: 95 },
      { label: "Instructors", value: 32 },
      { label: "Job Growth", value: "18%" },
    ],
  },
};

export const detailedCoursesData: Record<number, Course> = {
  1: {
    id: 1,
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
            isCompleted: true,
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
            content: "This would be a quiz component in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 5,
          },
          {
            id: 106,
            title: "Final Assessment: Web Development Introduction",
            description:
              "Comprehensive assessment of your understanding of web development basics.",
            duration: "40m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 6,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "Which technology is responsible for the structure of a web page?",
            //       options: ["HTML", "CSS", "JavaScript", "PHP"],
            //       correctAnswer: 0,
            //     },
            //     {
            //       id: 2,
            //       text: "What does HTTP stand for?",
            //       options: [
            //         "HyperText Transfer Protocol",
            //         "High Tech Transfer Process",
            //         "Host Transfer Protocol",
            //         "HyperText Technical Process",
            //       ],
            //       correctAnswer: 0,
            //     },
            //     {
            //       id: 3,
            //       text: "Which tool is used for version control in web development?",
            //       options: [
            //         "Git",
            //         "Photoshop",
            //         "Visual Studio Code",
            //         "Chrome DevTools",
            //       ],
            //       correctAnswer: 0,
            //     },
            //   ],
            //   passingScore: 2,
            // },
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
            content: "This would be a quiz component in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 7,
          },
          {
            id: 208,
            title: "Final Assessment: HTML Mastery",
            description:
              "Comprehensive assessment of your HTML knowledge and skills.",
            duration: "45m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 8,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "Which HTML tag is used to create a hyperlink?",
            //       options: ["<link>", "<a>", "<href>", "<url>"],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 2,
            //       text: "What is the purpose of the <meta> tag in HTML?",
            //       options: [
            //         "To display text on the page",
            //         "To create tables",
            //         "To provide metadata about the HTML document",
            //         "To link to external JavaScript files",
            //       ],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 3,
            //       text: "Which HTML5 element represents a standalone section of content?",
            //       options: ["<div>", "<span>", "<section>", "<content>"],
            //       correctAnswer: 2,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
      {
        id: 3,
        title: "CSS Fundamentals",
        description: "Master the basics of CSS to style and layout web pages.",
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
          {
            id: 303,
            title: "CSS Selectors and Specificity",
            description:
              "Master CSS selectors and understand specificity for styling precision.",
            duration: "45m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 304,
            title: "CSS Layout Techniques",
            description:
              "Learn various CSS layout methods including Flexbox and Grid.",
            duration: "1h 10m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 4,
          },
          {
            id: 305,
            title: "CSS Positioning and Display",
            description:
              "Master CSS positioning and display properties for complex layouts.",
            duration: "50m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 5,
          },
          {
            id: 306,
            title: "CSS Best Practices",
            description:
              "Explore industry best practices for writing maintainable CSS.",
            duration: "40m",
            content:
              "This would be a document component in a real application.",
            type: "resource",
            isCompleted: false,
            order: 6,
          },
          {
            id: 307,
            title: "Final Assessment: CSS Proficiency",
            description:
              "Comprehensive assessment of your CSS styling and layout skills.",
            duration: "50m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 7,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "Which CSS property is used to change the text color?",
            //       options: ["text-color", "font-color", "color", "text-style"],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 2,
            //       text: "What does CSS stand for?",
            //       options: [
            //         "Creative Style Sheets",
            //         "Computer Style Sheets",
            //         "Cascading Style Sheets",
            //         "Colorful Style Sheets",
            //       ],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 3,
            //       text: "Which CSS layout model allows for flexible arrangement of elements?",
            //       options: ["Grid", "Float", "Flexbox", "Positioning"],
            //       correctAnswer: 2,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
    ],
  },
  2: {
    id: 2,
    title: "Advanced JavaScript: From Fundamentals to Functional JS",
    instructor: {
      name: "Sarah Chen",
      avatar: "/images/instructors/i1.jpg",
      title: "JavaScript Specialist",
      bio: "JavaScript expert with 10+ years of experience. Author of 'Mastering Modern JavaScript'.",
    },
    sections: [
      {
        id: 1,
        title: "JavaScript Basics Review",
        description:
          "Quick refresher on core JavaScript concepts and modern syntax.",
        order: 1,
        lessons: [
          {
            id: 101,
            title: "Modern JavaScript Syntax and Features",
            description:
              "Overview of ES6+ features that have transformed JavaScript development.",
            duration: "35m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 102,
            title: "Variable Scoping and Declarations",
            description:
              "Deep dive into let, const, and var differences and best practices.",
            duration: "28m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 103,
            title: "JavaScript Basics Assessment",
            description: "Test your understanding of core JavaScript concepts.",
            duration: "20m",
            content: "This would be a quiz component in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 3,
          },
          {
            id: 104,
            title: "Final Assessment: JavaScript Fundamentals",
            description:
              "Comprehensive assessment of your JavaScript basics knowledge.",
            duration: "35m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 4,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "Which keyword is used to declare variables in modern JavaScript?",
            //       options: ["var", "let", "const", "Both let and const"],
            //       correctAnswer: 3,
            //     },
            //     {
            //       id: 2,
            //       text: "What is the output of console.log(typeof [])?",
            //       options: ["array", "object", "list", "undefined"],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 3,
            //       text: "Which method is used to add elements to the end of an array?",
            //       options: ["push()", "append()", "add()", "insert()"],
            //       correctAnswer: 0,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
      {
        id: 2,
        title: "Functional Programming Concepts",
        description:
          "Learn the core principles of functional programming in JavaScript.",
        order: 2,
        lessons: [
          {
            id: 201,
            title: "First-Class Functions",
            description:
              "Understanding how functions work as first-class citizens in JavaScript.",
            duration: "40m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 202,
            title: "Higher-Order Functions",
            description:
              "Master functions that take functions as arguments or return functions.",
            duration: "45m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 203,
            title: "Pure Functions and Immutability",
            description:
              "Learn to write pure functions and understand the importance of immutability.",
            duration: "38m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 204,
            title: "Function Composition",
            description:
              "Create more complex functionality by composing simple functions.",
            duration: "42m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 4,
          },
          {
            id: 205,
            title: "Functional Programming Resources",
            description:
              "Comprehensive guide to functional programming reference materials.",
            duration: "15m",
            content:
              "This would be a resource component in a real application.",
            type: "resource",
            isCompleted: false,
            order: 5,
          },
          {
            id: 206,
            title: "Final Assessment: Functional Programming",
            description:
              "Comprehensive assessment of your functional programming skills.",
            duration: "40m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 6,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "What is a pure function?",
            //       options: [
            //         "A function that always returns the same output for the same input and has no side effects",
            //         "A function that only uses pure JavaScript syntax",
            //         "A function that only works on clean data",
            //         "A function written by experienced developers",
            //       ],
            //       correctAnswer: 0,
            //     },
            //     {
            //       id: 2,
            //       text: "What is immutability in functional programming?",
            //       options: [
            //         "The ability to run code without errors",
            //         "The concept that once a data structure is created, it cannot be changed",
            //         "A way to optimize function execution",
            //         "The process of compiling JavaScript code",
            //       ],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 3,
            //       text: "Which of these is a higher-order function?",
            //       options: [
            //         "A function that takes a long time to execute",
            //         "A function that is defined at the top of a file",
            //         "A function that returns a function or takes a function as an argument",
            //         "A function written by senior developers",
            //       ],
            //       correctAnswer: 2,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
      {
        id: 3,
        title: "Practical Functional JavaScript",
        description:
          "Apply functional programming principles to real-world JavaScript applications.",
        order: 3,
        lessons: [
          {
            id: 301,
            title: "Array Methods: map, filter, reduce",
            description:
              "Master the essential array methods for functional programming.",
            duration: "50m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 302,
            title: "Closures in Functional JavaScript",
            description:
              "Understand and leverage closures in functional programming.",
            duration: "35m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 303,
            title: "Functional Error Handling",
            description:
              "Learn techniques for handling errors in a functional way.",
            duration: "30m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 304,
            title: "Functional Data Transformation Project",
            description:
              "Build a data processing pipeline using functional programming principles.",
            duration: "1h 15m",
            content:
              "This would be an assignment component in a real application.",
            type: "assignment",
            isCompleted: false,
            order: 4,
          },
          {
            id: 305,
            title: "Final Assessment: Applied Functional JavaScript",
            description:
              "Comprehensive assessment of your ability to apply functional programming principles.",
            duration: "45m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 5,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "Which array method would you use to transform each element in an array?",
            //       options: ["forEach()", "map()", "filter()", "reduce()"],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 2,
            //       text: "What is function composition?",
            //       options: [
            //         "Writing functions with multiple parameters",
            //         "Combining two or more functions to create a new function",
            //         "Breaking down a function into smaller functions",
            //         "Reusing the same function multiple times",
            //       ],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 3,
            //       text: "Which functional programming pattern is used for handling potentially failing operations?",
            //       options: ["Functor", "Monad", "Closure", "Currying"],
            //       correctAnswer: 1,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
    ],
  },
  3: {
    id: 3,
    title: "Flutter & Dart: The Complete Guide",
    instructor: {
      name: "Mark Williams",
      avatar: "/images/instructors/i3.jpg",
      title: "Mobile Dev Expert",
      bio: "Former Google engineer specializing in cross-platform mobile development with Flutter.",
    },
    sections: [
      {
        id: 1,
        title: "Introduction to Flutter",
        description:
          "Get started with Flutter and the Dart programming language.",
        order: 1,
        lessons: [
          {
            id: 101,
            title: "What is Flutter?",
            description:
              "Introduction to Flutter and why it's revolutionizing mobile development.",
            duration: "22m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 102,
            title: "Setting Up Your Development Environment",
            description:
              "Step-by-step guide to setting up Flutter on your machine.",
            duration: "35m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 103,
            title: "Dart Programming Language Basics",
            description:
              "Learn the fundamentals of the Dart programming language.",
            duration: "45m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 104,
            title: "Your First Flutter App",
            description:
              "Build your first complete Flutter application from scratch.",
            duration: "1h 10m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 4,
          },
          {
            id: 105,
            title: "Flutter Resources",
            description:
              "Comprehensive collection of Flutter documentation and resources.",
            duration: "15m",
            content:
              "This would be a resource component in a real application.",
            type: "resource",
            isCompleted: false,
            order: 5,
          },
          {
            id: 106,
            title: "Final Assessment: Flutter Foundations",
            description:
              "Comprehensive assessment of your Flutter and Dart fundamentals.",
            duration: "30m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 6,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "What is Flutter?",
            //       options: [
            //         "A programming language",
            //         "A UI toolkit for building natively compiled applications",
            //         "A database management system",
            //         "A server-side framework",
            //       ],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 2,
            //       text: "Which programming language is used for Flutter development?",
            //       options: ["JavaScript", "Swift", "Dart", "Kotlin"],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 3,
            //       text: "What is a widget in Flutter?",
            //       options: [
            //         "A database object",
            //         "A UI component",
            //         "A function",
            //         "A file format",
            //       ],
            //       correctAnswer: 1,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
      {
        id: 2,
        title: "Flutter Widgets & UI",
        description:
          "Master Flutter's widget system and build beautiful user interfaces.",
        order: 2,
        lessons: [
          {
            id: 201,
            title: "Understanding Widget Tree",
            description:
              "Learn how the widget tree works in Flutter applications.",
            duration: "40m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 202,
            title: "Stateless vs Stateful Widgets",
            description:
              "Understand the key differences and when to use each type of widget.",
            duration: "38m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 203,
            title: "Layout Widgets in Flutter",
            description:
              "Master layout widgets like Row, Column, Stack, and Container.",
            duration: "55m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 204,
            title: "UI Challenge: Product Catalogue App",
            description: "Build a beautiful product catalogue UI in Flutter.",
            duration: "1h 20m",
            content:
              "This would be an assignment component in a real application.",
            type: "assignment",
            isCompleted: false,
            order: 4,
          },
          {
            id: 205,
            title: "Final Assessment: Flutter UI Mastery",
            description:
              "Comprehensive assessment of your Flutter UI development skills.",
            duration: "40m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 5,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "What is the difference between StatelessWidget and StatefulWidget?",
            //       options: [
            //         "StatelessWidget is faster",
            //         "StatefulWidget can be updated during runtime",
            //         "StatelessWidget is only used for UI elements",
            //         "StatefulWidget is only used for animations",
            //       ],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 2,
            //       text: "Which widget would you use to create a scrollable list of items?",
            //       options: ["Container", "Column", "ListView", "Row"],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 3,
            //       text: "How do you handle user input in Flutter?",
            //       options: [
            //         "Input widgets like TextField",
            //         "Only through HTTP requests",
            //         "Only using database queries",
            //         "Flutter doesn't support user input",
            //       ],
            //       correctAnswer: 0,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
    ],
  },
  4: {
    id: 4,
    title: "Machine Learning A-Z: Hands-On Python & R",
    instructor: {
      name: "Jessica Taylor",
      avatar: "/images/instructors/i4.jpg",
      title: "Data Science Lead",
      bio: "PhD in Computer Science with specialization in machine learning. 8+ years in industry.",
    },
    sections: [
      {
        id: 1,
        title: "Machine Learning Fundamentals",
        description: "Core concepts and terminology in machine learning.",
        order: 1,
        lessons: [
          {
            id: 101,
            title: "What is Machine Learning?",
            description:
              "Introduction to machine learning and its applications.",
            duration: "30m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 102,
            title: "Types of Machine Learning",
            description:
              "Overview of supervised, unsupervised, and reinforcement learning.",
            duration: "45m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 103,
            title: "Machine Learning Workflow",
            description:
              "The end-to-end process of building machine learning models.",
            duration: "40m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 104,
            title: "ML Fundamentals Quiz",
            description:
              "Test your understanding of machine learning fundamentals.",
            duration: "25m",
            content: "This would be a quiz component in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 4,
          },
          {
            id: 105,
            title: "Final Assessment: Machine Learning Foundations",
            description:
              "Comprehensive assessment of your machine learning fundamentals.",
            duration: "35m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 5,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "What is the difference between supervised and unsupervised learning?",
            //       options: [
            //         "Supervised learning requires a teacher, unsupervised doesn't",
            //         "Supervised learning uses labeled data, unsupervised uses unlabeled data",
            //         "Supervised learning is always more accurate",
            //         "Unsupervised learning can only be done in Python",
            //       ],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 2,
            //       text: "Which of these is an example of a classification problem?",
            //       options: [
            //         "Predicting house prices",
            //         "Estimating a patient's age",
            //         "Identifying if an email is spam or not",
            //         "Determining the optimal temperature for a process",
            //       ],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 3,
            //       text: "What is a model in machine learning?",
            //       options: [
            //         "A physical representation of a system",
            //         "A mathematical representation that generates predictions",
            //         "A database of training data",
            //         "A visualization of results",
            //       ],
            //       correctAnswer: 1,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
      {
        id: 2,
        title: "Data Preprocessing",
        description:
          "Essential techniques for preparing data for machine learning.",
        order: 2,
        lessons: [
          {
            id: 201,
            title: "Data Cleaning",
            description:
              "Learn techniques for handling missing data and outliers.",
            duration: "50m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 1,
          },
          {
            id: 202,
            title: "Feature Scaling",
            description: "Understanding normalization and standardization.",
            duration: "35m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 2,
          },
          {
            id: 203,
            title: "Feature Engineering",
            description: "Creating new features to improve model performance.",
            duration: "55m",
            videoUrl:
              "https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4",
            type: "video",
            isCompleted: false,
            order: 3,
          },
          {
            id: 204,
            title: "Data Preprocessing Project",
            description:
              "Real-world data preprocessing and feature engineering task.",
            duration: "1h 30m",
            content:
              "This would be an assignment component in a real application.",
            type: "assignment",
            isCompleted: false,
            order: 4,
          },
          {
            id: 205,
            title: "Final Assessment: Data Preprocessing",
            description:
              "Comprehensive assessment of your data preprocessing skills.",
            duration: "40m",
            content:
              "This would be a final assessment quiz in a real application.",
            type: "quiz",
            isCompleted: false,
            order: 5,
            // finalAssessment: {
            //   questions: [
            //     {
            //       id: 1,
            //       text: "Why is data preprocessing important in machine learning?",
            //       options: [
            //         "It makes the data easier to visualize",
            //         "It improves model performance and accuracy",
            //         "It reduces the time needed to train a model",
            //         "It's required by all machine learning libraries",
            //       ],
            //       correctAnswer: 1,
            //     },
            //     {
            //       id: 2,
            //       text: "What is feature scaling?",
            //       options: [
            //         "Adding more features to the dataset",
            //         "Reducing the number of features",
            //         "Transforming features to a similar scale",
            //         "Creating visualizations of features",
            //       ],
            //       correctAnswer: 2,
            //     },
            //     {
            //       id: 3,
            //       text: "Which technique would you use to handle missing values in a dataset?",
            //       options: [
            //         "Always delete rows with missing values",
            //         "Always use the mean value as a replacement",
            //         "Imputation (replacing with mean, median, or mode)",
            //         "Ignore the missing values during analysis",
            //       ],
            //       correctAnswer: 2,
            //     },
            //   ],
            //   passingScore: 2,
            // },
          },
        ],
      },
    ],
  },
};
