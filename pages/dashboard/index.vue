<!-- pages/dashboard/index.vue -->
<script setup lang="ts">
// Set page layout
definePageMeta({
  layout: "dashboard",
});

// User data - in a real app, this would be fetched from an API
const user = ref({
  name: "Emily Johnson",
  avatar: "/images/instructors/i1.jpg",
  role: "student",
  enrolledCourses: 4,
  completedCourses: 2,
  totalPoints: 1250,
  streak: 7,
  hoursLearned: 48,
  level: "Intermediate",
  badges: [
    { name: "Fast Learner", icon: "🚀", date: "2023-10-15" },
    { name: "Perfect Attendance", icon: "🎯", date: "2023-09-30" },
    { name: "7-Day Streak", icon: "🔥", date: "2023-10-20" },
  ],
});

// Enrolled courses data
const enrolledCourses = ref([
  {
    id: 1,
    title: "Complete Web Development Bootcamp",
    instructor: "Alex Johnson",
    progress: 68,
    lastAccessed: "2023-10-20T14:30:00",
    thumbnail: "/images/courses/c3.jpg",
    nextLesson: "CSS Flexbox Layout",
    totalLessons: 42,
    completedLessons: 28,
  },
  {
    id: 2,
    title: "UI/UX Design Masterclass",
    instructor: "Sarah Chen",
    progress: 35,
    lastAccessed: "2023-10-19T09:15:00",
    thumbnail: "/images/courses/c3.jpg",
    nextLesson: "User Research Methods",
    totalLessons: 36,
    completedLessons: 12,
  },
  {
    id: 3,
    title: "JavaScript Algorithms and Data Structures",
    instructor: "Michael Brown",
    progress: 92,
    lastAccessed: "2023-10-21T16:45:00",
    thumbnail: "/images/courses/c3.jpg",
    nextLesson: "Dynamic Programming Advanced",
    totalLessons: 48,
    completedLessons: 44,
  },
  {
    id: 4,
    title: "Mobile App Development with React Native",
    instructor: "Emma Wilson",
    progress: 12,
    lastAccessed: "2023-10-18T11:20:00",
    thumbnail: "/images/courses/c3.jpg",
    nextLesson: "Setting Up Your Development Environment",
    totalLessons: 38,
    completedLessons: 4,
  },
]);

// Sort courses by last accessed for "Continue Learning" section
const continueLearningCourses = computed(() => {
  return [...enrolledCourses.value]
    .sort(
      (a, b) =>
        new Date(b.lastAccessed).getTime() - new Date(a.lastAccessed).getTime()
    )
    .slice(0, 2);
});

// Upcoming classes data
const upcomingClasses = ref([
  {
    id: 1,
    title: "Advanced CSS Techniques",
    instructor: "Alex Johnson",
    date: "2023-10-23T13:00:00",
    duration: 60, // minutes
    courseId: 1,
    meetingUrl: "https://meet.example.com/css-advanced",
  },
  {
    id: 2,
    title: "User Testing Workshop",
    instructor: "Sarah Chen",
    date: "2023-10-24T15:30:00",
    duration: 90, // minutes
    courseId: 2,
    meetingUrl: "https://meet.example.com/user-testing",
  },
  {
    id: 3,
    title: "Solving Complex Algorithms",
    instructor: "Michael Brown",
    date: "2023-10-25T11:00:00",
    duration: 60, // minutes
    courseId: 3,
    meetingUrl: "https://meet.example.com/algorithms",
  },
]);

// Upcoming assignments data
const upcomingAssignments = ref([
  {
    id: 1,
    title: "Responsive Portfolio Project",
    courseId: 1,
    courseName: "Complete Web Development Bootcamp",
    dueDate: "2023-10-27T23:59:59",
    status: "pending", // pending, submitted, graded
    type: "project",
  },
  {
    id: 2,
    title: "User Interface Design Challenge",
    courseId: 2,
    courseName: "UI/UX Design Masterclass",
    dueDate: "2023-10-25T23:59:59",
    status: "pending",
    type: "assignment",
  },
  {
    id: 3,
    title: "Algorithm Efficiency Quiz",
    courseId: 3,
    courseName: "JavaScript Algorithms and Data Structures",
    dueDate: "2023-10-24T23:59:59",
    status: "pending",
    type: "quiz",
  },
]);

// Learning stats data for the chart
const learningStats = ref({
  daily: {
    labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    datasets: [
      {
        label: "Hours Spent",
        data: [1.5, 2.3, 1.8, 2.7, 2.2, 3.5, 1.9],
        backgroundColor: "rgba(99, 102, 241, 0.2)",
        borderColor: "rgba(99, 102, 241, 1)",
        borderWidth: 2,
        tension: 0.4,
      },
    ],
  },
  weekly: {
    labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
    datasets: [
      {
        label: "Hours Spent",
        data: [12.5, 15.3, 10.8, 9.2],
        backgroundColor: "rgba(99, 102, 241, 0.2)",
        borderColor: "rgba(99, 102, 241, 1)",
        borderWidth: 2,
        tension: 0.4,
      },
    ],
  },
  monthly: {
    labels: ["May", "Jun", "Jul", "Aug", "Sep", "Oct"],
    datasets: [
      {
        label: "Hours Spent",
        data: [42.5, 38.2, 51.7, 48.3, 55.2, 48.0],
        backgroundColor: "rgba(99, 102, 241, 0.2)",
        borderColor: "rgba(99, 102, 241, 1)",
        borderWidth: 2,
        tension: 0.4,
      },
    ],
  },
});

// Recommended courses
const recommendedCourses = ref([
  {
    id: 101,
    title: "Advanced React Patterns and Performance",
    instructor: "Michael Chen",
    rating: 4.9,
    students: 2547,
    thumbnail: "/images/courses/c3.jpg",
    price: 89.99,
    level: "Advanced",
    tags: ["React", "Frontend", "Performance"],
    relevanceScore: 98,
  },
  {
    id: 102,
    title: "Node.js Microservices Architecture",
    instructor: "David Wilson",
    rating: 4.8,
    students: 1872,
    thumbnail: "/images/courses/c3.jpg",
    price: 94.99,
    level: "Intermediate",
    tags: ["Node.js", "Backend", "Microservices"],
    relevanceScore: 92,
  },
  {
    id: 103,
    title: "Full-Stack TypeScript Development",
    instructor: "Emma Davis",
    rating: 4.7,
    students: 3105,
    thumbnail: "/images/courses/c3.jpg",
    price: 79.99,
    level: "Intermediate",
    tags: ["TypeScript", "Full-Stack", "React"],
    relevanceScore: 95,
  },
]);

// Learning path data
const learningPath = ref({
  title: "Full-Stack Web Developer",
  progress: 42,
  milestones: [
    {
      id: 1,
      title: "HTML & CSS Fundamentals",
      status: "completed",
      courses: [{ id: 201, title: "HTML5 & CSS3 Essentials" }],
    },
    {
      id: 2,
      title: "JavaScript Fundamentals",
      status: "completed",
      courses: [{ id: 202, title: "JavaScript for Beginners" }],
    },
    {
      id: 3,
      title: "Frontend Frameworks",
      status: "in-progress",
      courses: [
        { id: 1, title: "Complete Web Development Bootcamp" },
        { id: 203, title: "React.js: Zero to Hero" },
      ],
    },
    {
      id: 4,
      title: "Backend Development",
      status: "not-started",
      courses: [{ id: 204, title: "Node.js & Express Masterclass" }],
    },
    {
      id: 5,
      title: "Database Management",
      status: "not-started",
      courses: [{ id: 205, title: "MongoDB Complete Guide" }],
    },
    {
      id: 6,
      title: "Deployment & DevOps",
      status: "not-started",
      courses: [{ id: 206, title: "DevOps for Web Developers" }],
    },
  ],
});

// Community data
const communityUpdates = ref([
  {
    id: 1,
    type: "forum",
    title: "How to optimize React components?",
    author: {
      name: "Thomas Clark",
      avatar: "/images/courses/c3.jpg",
    },
    course: "Advanced React Patterns",
    replies: 12,
    lastActivity: "2023-10-21T08:45:00",
    liked: true,
    saved: false,
  },
  {
    id: 2,
    type: "announcement",
    title: "New CSS features workshop this Friday!",
    author: {
      name: "Alex Johnson",
      avatar: "/images/courses/c3.jpg",
    },
    course: "Complete Web Development Bootcamp",
    lastActivity: "2023-10-20T16:30:00",
    liked: false,
    saved: true,
  },
  {
    id: 3,
    type: "study_group",
    title: "Algorithm Study Group - Meeting #8",
    author: {
      name: "Michael Brown",
      avatar: "/images/courses/c3.jpg",
    },
    course: "JavaScript Algorithms and Data Structures",
    members: 15,
    nextMeeting: "2023-10-24T19:00:00",
    liked: false,
    saved: false,
  },
]);

// Greeting based on time of day
const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return "Good morning";
  if (hour < 18) return "Good afternoon";
  return "Good evening";
});

// Format date utility function
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    weekday: "short",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  });
};

// Format time remaining utility function
const getTimeRemaining = (dateString: string) => {
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

// Animation flags for staggered entrance
const isLoaded = ref(false);

// Set up animation
onMounted(() => {
  // Small delay for loading animation
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});
</script>

<template>
  <div>
    <!-- Main dashboard content -->
    <div class="space-y-6">
      <!-- Welcome Card -->
      <DashboardStudentDashboardWelcomeCard
        :greeting="greeting"
        :user-name="user.name"
        :streak="user.streak"
        :enrolled-courses="user.enrolledCourses"
        :hours-learned="user.hoursLearned"
        :total-points="user.totalPoints"
        :animation-delay="0"
        :is-loaded="isLoaded"
      />

      <!-- Main dashboard grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left column - Course Progress and Upcoming Classes -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Course Progress Card -->
          <DashboardStudentCourseProgressCard
            :courses="continueLearningCourses"
            :animation-delay="100"
            :is-loaded="isLoaded"
          />

          <!-- Upcoming Classes Card -->
          <DashboardStudentUpcomingClassesCard
            :classes="upcomingClasses"
            :format-date="formatDate"
            :animation-delay="200"
            :is-loaded="isLoaded"
          />

          <!-- Assignments Card -->
          <DashboardStudentAssignmentCard
            :assignments="upcomingAssignments"
            :get-time-remaining="getTimeRemaining"
            :animation-delay="300"
            :is-loaded="isLoaded"
          />
        </div>

        <!-- Right column - Stats, Recommendations, Path -->
        <div class="space-y-6">
          <!-- Learning Stats Card with Chart -->
          <DashboardStudentLearningStatsCard
            :stats="learningStats"
            :animation-delay="150"
            :is-loaded="isLoaded"
          />

          <!-- Recommended Courses Card -->
          <DashboardStudentRecommendedCoursesCard
            :courses="recommendedCourses"
            :animation-delay="250"
            :is-loaded="isLoaded"
          />

          <!-- Learning Path Card -->
          <DashboardStudentLearningPathCard
            :learning-path="learningPath"
            :animation-delay="350"
            :is-loaded="isLoaded"
          />
        </div>
      </div>

      <!-- Community Updates (full width) -->
      <DashboardStudentCommunityCard
        :updates="communityUpdates"
        :format-date="formatDate"
        :animation-delay="400"
        :is-loaded="isLoaded"
      />
    </div>
  </div>
</template>

<style scoped>
/* Animation class for staggered entrance */
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}
</style>
