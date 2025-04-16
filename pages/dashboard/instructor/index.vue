<!-- pages/instructor/dashboard/index.vue -->
<script setup lang="ts">
import { ref, onMounted, computed } from "vue";

// Set layout
definePageMeta({
  layout: "dashboard-instructor",
});

// Dashboard statistics
const stats = ref({
  earnings: {
    total: 24582.65,
    trend: +12.5, // positive percentage change
    currency: "$",
  },
  students: {
    total: 1247,
    trend: +8.2,
    new: 58,
  },
  courses: {
    total: 8,
    active: 6,
    draft: 2,
    avgRating: 4.9,
  },
  completionRate: {
    value: 94,
    trend: +3.7,
  },
});

// Upcoming schedule items
const upcomingSchedule = ref([
  {
    id: 1,
    title: "Advanced JavaScript Concepts",
    type: "live-class",
    date: "2025-04-16T14:30:00",
    duration: 90, // minutes
    students: 38,
    location: "Virtual Classroom 3",
    url: "/instructor/classes/123",
    color: "bg-indigo-500",
  },
  {
    id: 2,
    title: "Frontend Framework Comparison",
    type: "consultation",
    date: "2025-04-16T17:00:00",
    duration: 60,
    students: 1,
    studentName: "Emily Parker",
    location: "Meeting Room A",
    url: "/instructor/consultations/456",
    color: "bg-purple-500",
  },
  {
    id: 3,
    title: "React Course Content Review",
    type: "task",
    date: "2025-04-17T10:00:00",
    duration: 120,
    deadline: true,
    url: "/instructor/courses/edit/789",
    color: "bg-amber-500",
  },
  {
    id: 4,
    title: "Node.js API Development",
    type: "live-class",
    date: "2025-04-17T15:00:00",
    duration: 90,
    students: 42,
    location: "Virtual Classroom 1",
    url: "/instructor/classes/101",
    color: "bg-blue-500",
  },
]);

// Recent student activity
const recentActivity = ref([
  {
    id: 1,
    student: {
      name: "James Wilson",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/101",
    },
    action: "completed",
    course: {
      title: "Full-Stack JavaScript Masterclass",
      url: "/instructor/courses/2",
    },
    timestamp: "2025-04-15T09:45:00",
  },
  {
    id: 2,
    student: {
      name: "Maria Rodriguez",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/213",
    },
    action: "enrolled",
    course: {
      title: "Advanced CSS and Sass",
      url: "/instructor/courses/5",
    },
    timestamp: "2025-04-15T08:30:00",
  },
  {
    id: 3,
    student: {
      name: "David Chen",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/159",
    },
    action: "submitted",
    course: {
      title: "React Hooks and Context API",
      url: "/instructor/courses/3",
    },
    item: "Final Project",
    url: "/instructor/assignments/789",
    timestamp: "2025-04-14T18:22:00",
  },
  {
    id: 4,
    student: {
      name: "Sarah Johnson",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/87",
    },
    action: "asked",
    course: {
      title: "Node.js API Development",
      url: "/instructor/courses/4",
    },
    item: "Question about authentication",
    url: "/instructor/messages/456",
    timestamp: "2025-04-14T15:10:00",
  },
  {
    id: 5,
    student: {
      name: "Michael Brown",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/132",
    },
    action: "rated",
    course: {
      title: "Full-Stack JavaScript Masterclass",
      url: "/instructor/courses/2",
    },
    rating: 5,
    timestamp: "2025-04-14T11:45:00",
  },
]);

// Popular courses data
const popularCourses = ref([
  {
    id: 1,
    title: "Full-Stack JavaScript Masterclass",
    students: 482,
    completion: 87,
    revenue: 28924,
    rating: 4.9,
    image: "/api/placeholder/80/45",
    trend: "+12%",
    url: "/instructor/courses/2",
  },
  {
    id: 2,
    title: "React Hooks and Context API",
    students: 351,
    completion: 92,
    revenue: 19872,
    rating: 4.8,
    image: "/api/placeholder/80/45",
    trend: "+8%",
    url: "/instructor/courses/3",
  },
  {
    id: 3,
    title: "Node.js API Development",
    students: 298,
    completion: 79,
    revenue: 16540,
    rating: 4.7,
    image: "/api/placeholder/80/45",
    trend: "+15%",
    url: "/instructor/courses/4",
  },
]);

// Assignments to review
const pendingAssignments = ref([
  {
    id: 1,
    title: "Final Project: E-commerce Site",
    course: {
      title: "Full-Stack JavaScript Masterclass",
      url: "/instructor/courses/2",
    },
    student: {
      name: "Emma Thompson",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/45",
    },
    submittedAt: "2025-04-14T14:30:00",
    dueBy: "2025-04-17T23:59:59",
    url: "/instructor/assignments/123",
  },
  {
    id: 2,
    title: "REST API Implementation",
    course: {
      title: "Node.js API Development",
      url: "/instructor/courses/4",
    },
    student: {
      name: "Daniel Kim",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/78",
    },
    submittedAt: "2025-04-15T10:15:00",
    dueBy: "2025-04-18T23:59:59",
    url: "/instructor/assignments/124",
  },
  {
    id: 3,
    title: "State Management Solution",
    course: {
      title: "React Hooks and Context API",
      url: "/instructor/courses/3",
    },
    student: {
      name: "Sophia Martinez",
      avatar: "/api/placeholder/32/32",
      url: "/instructor/students/92",
    },
    submittedAt: "2025-04-13T19:45:00",
    dueBy: "2025-04-16T23:59:59",
    url: "/instructor/assignments/125",
    urgent: true,
  },
]);

// Course Analytics Data
const courseAnalytics = ref({
  labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
  datasets: [
    {
      label: "Course Enrollments",
      data: [65, 78, 90, 105, 125, 142],
      borderColor: "#4f46e5",
      backgroundColor: "rgba(79, 70, 229, 0.1)",
      fill: true,
    },
    {
      label: "Course Completions",
      data: [45, 60, 75, 85, 95, 105],
      borderColor: "#10b981",
      backgroundColor: "rgba(16, 185, 129, 0.1)",
      fill: true,
    },
  ],
});

// Earnings Analytics Data
const earningsAnalytics = ref({
  labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
  datasets: [
    {
      label: "Monthly Earnings ($)",
      data: [3200, 3800, 4100, 4300, 4650, 5100],
      borderColor: "#8b5cf6",
      backgroundColor: "rgba(139, 92, 246, 0.1)",
      fill: true,
    },
  ],
});

// Format date helper
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
  });
};

// Format time helper
const formatTime = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Format currency helper
const formatCurrency = (amount: number, currency: string = "$") => {
  return `${currency}${amount.toLocaleString("en-US", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  })}`;
};

// Check if date is today
const isToday = (dateString: string) => {
  const today = new Date();
  const date = new Date(dateString);
  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  );
};

// Check if date is tomorrow
const isTomorrow = (dateString: string) => {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const date = new Date(dateString);
  return (
    date.getDate() === tomorrow.getDate() &&
    date.getMonth() === tomorrow.getMonth() &&
    date.getFullYear() === tomorrow.getFullYear()
  );
};

// Get relative time label
const getRelativeTimeLabel = (dateString: string) => {
  if (isToday(dateString)) return "Today";
  if (isTomorrow(dateString)) return "Tomorrow";
  return formatDate(dateString);
};

// Get classes for today count - utilizing the isToday function
const todayClassesCount = computed(() => {
  return upcomingSchedule.value.filter((item) => isToday(item.date)).length;
});

// Get earnings trend class based on value
const getEarningsTrendClass = computed(() => {
  return stats.value.earnings.trend >= 0
    ? "text-green-600 bg-green-100"
    : "text-red-600 bg-red-100";
});

// Get completion trend class based on value
const getCompletionTrendClass = computed(() => {
  return stats.value.completionRate.trend >= 0
    ? "text-green-600 bg-green-100"
    : "text-red-600 bg-red-100";
});

// Determine if we should highlight an assignment as urgent based on due date
const isAssignmentUrgent = (dueBy: string) => {
  const dueDate = new Date(dueBy);
  const today = new Date();
  const diffTime = dueDate.getTime() - today.getTime();
  const diffDays = diffTime / (1000 * 3600 * 24);
  return diffDays <= 1; // Consider urgent if due within 24 hours
};

// Animation flags
const isPageLoaded = ref(false);

// Page initialization
onMounted(() => {
  // Use the charts to display analytics data
  renderCourseAnalyticsChart();
  renderEarningsAnalyticsChart();

  setTimeout(() => {
    isPageLoaded.value = true;
  }, 100);
});

// Mock function to render the course analytics chart
const renderCourseAnalyticsChart = () => {
  // In a real implementation, this would use a charting library like Chart.js
  console.log("Rendering course analytics with data:", courseAnalytics.value);
  // Example: new Chart(ctx, { type: 'line', data: courseAnalytics.value });
};

// Mock function to render the earnings analytics chart
const renderEarningsAnalyticsChart = () => {
  // In a real implementation, this would use a charting library like Chart.js
  console.log(
    "Rendering earnings analytics with data:",
    earningsAnalytics.value
  );
  // Example: new Chart(ctx, { type: 'line', data: earningsAnalytics.value });
};
</script>

<template>
  <div>
    <!-- Welcome Banner -->
    <div
      class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6 shadow-lg transform transition-all duration-500"
      :class="{
        'translate-y-0 opacity-100': isPageLoaded,
        'translate-y-4 opacity-0': !isPageLoaded,
      }"
    >
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold mb-2">Welcome Back, Dr. Thompson!</h1>
          <p class="opacity-90">
            You have
            <span class="font-bold">{{ pendingAssignments.length }}</span>
            assignments to review and
            <span class="font-bold">{{ todayClassesCount }}</span> classes
            today.
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
          <NuxtLink
            to="/instructor/courses/create"
            class="bg-white text-indigo-700 px-4 py-2 rounded-lg font-medium hover:bg-indigo-50 transition-colors shadow-md"
          >
            Create Course
          </NuxtLink>
          <NuxtLink
            to="/instructor/classes/schedule"
            class="bg-indigo-700 bg-opacity-20 hover:bg-opacity-30 border border-white border-opacity-20 text-white px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Schedule Class
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Stats & Schedule -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Stats Cards -->
        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 transform transition-all duration-500 delay-100"
          :class="{
            'translate-y-0 opacity-100': isPageLoaded,
            'translate-y-4 opacity-0': !isPageLoaded,
          }"
        >
          <!-- Earnings Card -->
          <div
            class="bg-white rounded-xl p-5 shadow-md border border-gray-200 transition-all duration-300 hover:shadow-lg"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-500 text-sm">Total Earnings</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                  {{ stats.earnings.currency
                  }}{{ stats.earnings.total.toLocaleString() }}
                </h3>
              </div>
              <div
                class="h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
            <div class="flex items-center mt-3">
              <span
                class="text-sm font-medium px-2 py-1 rounded-full"
                :class="getEarningsTrendClass"
              >
                {{ stats.earnings.trend > 0 ? "+" : ""
                }}{{ stats.earnings.trend }}%
              </span>
              <span class="text-xs text-gray-500 ml-2">from last month</span>
            </div>
          </div>

          <!-- Students Card -->
          <div
            class="bg-white rounded-xl p-5 shadow-md border border-gray-200 transition-all duration-300 hover:shadow-lg"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-500 text-sm">Total Students</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                  {{ stats.students.total }}
                </h3>
              </div>
              <div
                class="h-10 w-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path d="M12 14l9-5-9-5-9 5 9 5z" />
                  <path
                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
                  />
                </svg>
              </div>
            </div>
            <div class="flex items-center mt-3">
              <span
                class="text-sm font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full"
              >
                +{{ stats.students.new }}
              </span>
              <span class="text-xs text-gray-500 ml-2">new this week</span>
            </div>
          </div>

          <!-- Courses Card -->
          <div
            class="bg-white rounded-xl p-5 shadow-md border border-gray-200 transition-all duration-300 hover:shadow-lg"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-500 text-sm">Active Courses</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                  {{ stats.courses.active }}/{{ stats.courses.total }}
                </h3>
              </div>
              <div
                class="h-10 w-10 flex items-center justify-center rounded-full bg-purple-100 text-purple-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                  />
                </svg>
              </div>
            </div>
            <div class="flex items-center mt-3">
              <div class="flex text-yellow-400 mr-1">
                <svg
                  v-for="i in 5"
                  :key="i"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
              </div>
              <span class="text-xs text-gray-500"
                >{{ stats.courses.avgRating }} avg. rating</span
              >
            </div>
          </div>

          <!-- Completion Rate Card -->
          <div
            class="bg-white rounded-xl p-5 shadow-md border border-gray-200 transition-all duration-300 hover:shadow-lg"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-500 text-sm">Completion Rate</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">
                  {{ stats.completionRate.value }}%
                </h3>
              </div>
              <div
                class="h-10 w-10 flex items-center justify-center rounded-full bg-green-100 text-green-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>
            <div class="flex items-center mt-3">
              <span
                class="text-sm font-medium px-2 py-1 rounded-full"
                :class="getCompletionTrendClass"
              >
                {{ stats.completionRate.trend > 0 ? "+" : ""
                }}{{ stats.completionRate.trend }}%
              </span>
              <span class="text-xs text-gray-500 ml-2">from last month</span>
            </div>
          </div>
        </div>

        <!-- Upcoming Schedule Section -->
        <div
          class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transition-all duration-500 delay-200"
          :class="{
            'translate-y-0 opacity-100': isPageLoaded,
            'translate-y-4 opacity-0': !isPageLoaded,
          }"
        >
          <div
            class="p-5 border-b border-gray-200 flex justify-between items-center"
          >
            <h2 class="text-lg font-bold text-gray-900">Upcoming Schedule</h2>
            <NuxtLink
              to="/instructor/classes"
              class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
            >
              View All
            </NuxtLink>
          </div>
          <div class="divide-y divide-gray-200">
            <div
              v-for="item in upcomingSchedule"
              :key="item.id"
              class="p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start">
                <div
                  class="h-12 w-1 rounded-full mr-4"
                  :class="item.color"
                ></div>
                <div class="flex-1">
                  <div class="flex items-center justify-between">
                    <h3 class="font-medium text-gray-900">{{ item.title }}</h3>
                    <div class="flex items-center">
                      <span
                        class="text-xs font-medium rounded-full px-2 py-1 ml-2"
                        :class="{
                          'bg-indigo-100 text-indigo-700':
                            item.type === 'live-class',
                          'bg-purple-100 text-purple-700':
                            item.type === 'consultation',
                          'bg-amber-100 text-amber-700': item.type === 'task',
                        }"
                      >
                        {{
                          item.type === "live-class"
                            ? "Class"
                            : item.type === "consultation"
                            ? "Consultation"
                            : "Task"
                        }}
                      </span>
                      <span
                        v-if="item.deadline"
                        class="text-xs font-medium bg-red-100 text-red-700 rounded-full px-2 py-1 ml-2"
                      >
                        Deadline
                      </span>
                    </div>
                  </div>
                  <div
                    class="mt-1 flex flex-wrap items-center text-sm text-gray-500"
                  >
                    <div class="flex items-center mr-4">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span
                        >{{ getRelativeTimeLabel(item.date) }}
                        {{ formatTime(item.date) }}</span
                      >
                    </div>
                    <div v-if="item.duration" class="flex items-center mr-4">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span>{{ item.duration }} mins</span>
                    </div>
                    <div v-if="item.students" class="flex items-center mr-4">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                        />
                      </svg>
                      <span>
                        {{
                          item.students === 1 && item.studentName
                            ? item.studentName
                            : `${item.students} students`
                        }}
                      </span>
                    </div>
                    <div v-if="item.location" class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span>{{ item.location }}</span>
                    </div>
                  </div>
                  <div class="mt-2">
                    <NuxtLink
                      :to="item.url"
                      class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                    >
                      View Details →
                    </NuxtLink>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Analytics Charts -->
        <div
          class="grid grid-cols-1 md:grid-cols-2 gap-6 transform transition-all duration-500 delay-300"
          :class="{
            'translate-y-0 opacity-100': isPageLoaded,
            'translate-y-4 opacity-0': !isPageLoaded,
          }"
        >
          <!-- Course Analytics Chart -->
          <div
            class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden"
          >
            <div class="p-5 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Course Analytics</h2>
            </div>
            <div class="p-5">
              <div class="h-64 bg-gray-50 rounded flex flex-col">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex space-x-4">
                    <div class="flex items-center">
                      <div
                        class="h-3 w-3 bg-indigo-500 rounded-full mr-1"
                      ></div>
                      <span class="text-xs text-gray-600">Enrollments</span>
                    </div>
                    <div class="flex items-center">
                      <div class="h-3 w-3 bg-green-500 rounded-full mr-1"></div>
                      <span class="text-xs text-gray-600">Completions</span>
                    </div>
                  </div>
                  <div class="text-xs text-gray-500">Last 6 months</div>
                </div>

                <!-- Chart visualization (mock) -->
                <div class="flex-1 flex items-end">
                  <div
                    v-for="(value, index) in courseAnalytics.datasets[0].data"
                    :key="index"
                    class="flex flex-col items-center justify-end h-full flex-1"
                  >
                    <div class="relative w-full mx-1">
                      <!-- Enrollments bar -->
                      <div
                        class="absolute bottom-0 left-0 right-0 bg-indigo-500 rounded-t transition-all duration-500"
                        :style="`height: ${value / 2}px; opacity: ${
                          isPageLoaded ? 1 : 0
                        }`"
                      ></div>

                      <!-- Completions bar -->
                      <div
                        class="absolute bottom-0 left-0 right-0 bg-green-500 rounded-t transition-all duration-700"
                        :style="`height: ${
                          courseAnalytics.datasets[1].data[index] / 2
                        }px; opacity: ${isPageLoaded ? 0.85 : 0}`"
                      ></div>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">
                      {{ courseAnalytics.labels[index] }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 text-center">
                <span class="text-sm font-medium text-green-600"
                  >+{{
                    Math.round(
                      ((courseAnalytics.datasets[0].data[5] -
                        courseAnalytics.datasets[0].data[0]) /
                        courseAnalytics.datasets[0].data[0]) *
                        100
                    )
                  }}%</span
                >
                <span class="text-sm text-gray-500"> enrollment growth</span>
              </div>
            </div>
          </div>

          <!-- Earnings Analytics Chart -->
          <div
            class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden"
          >
            <div class="p-5 border-b border-gray-200">
              <h2 class="text-lg font-bold text-gray-900">Earnings Overview</h2>
            </div>
            <div class="p-5">
              <div class="h-64 bg-gray-50 rounded">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center">
                    <div class="h-3 w-3 bg-purple-500 rounded-full mr-1"></div>
                    <span class="text-xs text-gray-600">Monthly Earnings</span>
                  </div>
                  <div class="text-xs text-gray-500">Last 6 months</div>
                </div>

                <!-- Chart visualization (mock) -->
                <div class="relative h-48">
                  <!-- Area chart path -->
                  <svg class="w-full h-full" preserveAspectRatio="none">
                    <path
                      :d="`M0,${
                        48 - earningsAnalytics.datasets[0].data[0] / 100
                      } 
                           L${100 / 5},${
                        48 - earningsAnalytics.datasets[0].data[1] / 100
                      } 
                           L${(100 * 2) / 5},${
                        48 - earningsAnalytics.datasets[0].data[2] / 100
                      } 
                           L${(100 * 3) / 5},${
                        48 - earningsAnalytics.datasets[0].data[3] / 100
                      } 
                           L${(100 * 4) / 5},${
                        48 - earningsAnalytics.datasets[0].data[4] / 100
                      } 
                           L100,${
                             48 - earningsAnalytics.datasets[0].data[5] / 100
                           } 
                           L100,48 L0,48 Z`"
                      fill="rgba(139, 92, 246, 0.15)"
                      stroke="rgb(139, 92, 246)"
                      stroke-width="2"
                      class="transition-all duration-1000"
                      :style="`opacity: ${isPageLoaded ? 1 : 0}`"
                    />
                  </svg>

                  <!-- Data points -->
                  <div
                    v-for="(value, index) in earningsAnalytics.datasets[0].data"
                    :key="index"
                    class="absolute h-2 w-2 bg-purple-600 rounded-full transform transition-all duration-1000"
                    :style="`left: ${index * 20}%; bottom: ${
                      value / 100 - 1
                    }px; opacity: ${isPageLoaded ? 1 : 0}; transform: ${
                      isPageLoaded ? 'scale(1)' : 'scale(0)'
                    }`"
                  ></div>
                </div>

                <!-- X-axis labels -->
                <div class="flex justify-between mt-2">
                  <div
                    v-for="(label, index) in earningsAnalytics.labels"
                    :key="index"
                    class="text-xs text-gray-500"
                  >
                    {{ label }}
                  </div>
                </div>
              </div>
              <div class="mt-4 text-center">
                <span class="text-sm font-medium text-green-600">
                  +{{
                    Math.round(
                      ((earningsAnalytics.datasets[0].data[5] -
                        earningsAnalytics.datasets[0].data[0]) /
                        earningsAnalytics.datasets[0].data[0]) *
                        100
                    )
                  }}%
                </span>
                <span class="text-sm text-gray-500"> earnings growth</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Popular Courses, Student Activity, Assignments -->
      <div class="space-y-6">
        <!-- Popular Courses -->
        <div
          class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transform transition-all duration-500 delay-400"
          :class="{
            'translate-y-0 opacity-100': isPageLoaded,
            'translate-y-4 opacity-0': !isPageLoaded,
          }"
        >
          <div
            class="p-5 border-b border-gray-200 flex justify-between items-center"
          >
            <h2 class="text-lg font-bold text-gray-900">Popular Courses</h2>
            <NuxtLink
              to="/instructor/courses"
              class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
            >
              View All
            </NuxtLink>
          </div>
          <div class="divide-y divide-gray-200">
            <div
              v-for="course in popularCourses"
              :key="course.id"
              class="p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center">
                <img
                  :src="course.image"
                  alt="Course thumbnail"
                  class="w-20 h-12 object-cover rounded mr-4"
                />
                <div class="flex-1">
                  <h3 class="font-medium text-gray-900">{{ course.title }}</h3>
                  <div class="flex flex-wrap mt-1 text-sm text-gray-500">
                    <div class="flex items-center mr-4">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                        />
                      </svg>
                      <span>{{ course.students }} students</span>
                    </div>
                    <div class="flex items-center mr-4">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1 text-yellow-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                      <span>{{ course.rating }}</span>
                    </div>
                    <div class="flex items-center text-green-600 font-medium">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      {{ course.trend }}
                    </div>
                  </div>
                  <div
                    class="mt-2 text-sm text-gray-700 flex items-center justify-between"
                  >
                    <span>
                      <span class="font-medium">{{
                        formatCurrency(course.revenue)
                      }}</span>
                      revenue
                    </span>
                    <span class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1 text-green-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="text-gray-500"
                        >{{ course.completion }}% completion</span
                      >
                    </span>
                  </div>
                </div>
                <div>
                  <NuxtLink
                    :to="course.url"
                    class="text-indigo-600 hover:text-indigo-800 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Assignments to Review -->
        <div
          class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transform transition-all duration-500 delay-500"
          :class="{
            'translate-y-0 opacity-100': isPageLoaded,
            'translate-y-4 opacity-0': !isPageLoaded,
          }"
        >
          <div
            class="p-5 border-b border-gray-200 flex justify-between items-center"
          >
            <h2 class="text-lg font-bold text-gray-900">Pending Assignments</h2>
            <NuxtLink
              to="/instructor/assignments"
              class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
            >
              View All
            </NuxtLink>
          </div>
          <div class="divide-y divide-gray-200">
            <div
              v-for="assignment in pendingAssignments"
              :key="assignment.id"
              class="p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start">
                <div class="flex-1">
                  <div class="flex items-center">
                    <h3 class="font-medium text-gray-900">
                      {{ assignment.title }}
                    </h3>
                    <span
                      v-if="
                        assignment.urgent ||
                        isAssignmentUrgent(assignment.dueBy)
                      "
                      class="ml-2 px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full"
                    >
                      Urgent
                    </span>
                  </div>
                  <p class="mt-1 text-sm text-gray-500">
                    From
                    <NuxtLink
                      :to="assignment.student.url"
                      class="text-indigo-600 hover:text-indigo-800"
                      >{{ assignment.student.name }}</NuxtLink
                    >
                    in
                    <NuxtLink
                      :to="assignment.course.url"
                      class="text-indigo-600 hover:text-indigo-800"
                      >{{ assignment.course.title }}</NuxtLink
                    >
                  </p>
                  <div class="mt-2 flex items-center text-xs text-gray-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 mr-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span
                      >Submitted {{ formatDate(assignment.submittedAt) }} · Due
                      by {{ formatDate(assignment.dueBy) }}</span
                    >
                  </div>
                </div>
                <div class="flex items-center ml-4">
                  <img
                    :src="assignment.student.avatar"
                    alt="Student avatar"
                    class="w-8 h-8 rounded-full mr-2 object-cover border border-gray-200"
                  />
                  <NuxtLink
                    :to="assignment.url"
                    class="flex-shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-3 py-1 rounded transition-colors"
                  >
                    Review
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div
          class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transform transition-all duration-500 delay-600"
          :class="{
            'translate-y-0 opacity-100': isPageLoaded,
            'translate-y-4 opacity-0': !isPageLoaded,
          }"
        >
          <div class="p-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">
              Recent Student Activity
            </h2>
          </div>
          <div class="divide-y divide-gray-200">
            <div
              v-for="activity in recentActivity"
              :key="activity.id"
              class="p-4 hover:bg-gray-50 transition-colors"
            >
              <div class="flex">
                <img
                  :src="activity.student.avatar"
                  alt="Student avatar"
                  class="h-8 w-8 rounded-full mr-3"
                />
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-700">
                    <NuxtLink
                      :to="activity.student.url"
                      class="font-medium text-gray-900 hover:text-indigo-600"
                    >
                      {{ activity.student.name }}
                    </NuxtLink>

                    <span v-if="activity.action === 'completed'">
                      completed
                      <NuxtLink
                        :to="activity.course.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.course.title }}
                      </NuxtLink>
                    </span>

                    <span v-else-if="activity.action === 'enrolled'">
                      enrolled in
                      <NuxtLink
                        :to="activity.course.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.course.title }}
                      </NuxtLink>
                    </span>

                    <span v-else-if="activity.action === 'submitted'">
                      submitted
                      <NuxtLink
                        :to="activity.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.item }}
                      </NuxtLink>
                      for
                      <NuxtLink
                        :to="activity.course.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.course.title }}
                      </NuxtLink>
                    </span>

                    <span v-else-if="activity.action === 'asked'">
                      asked
                      <NuxtLink
                        :to="activity.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.item }}
                      </NuxtLink>
                      in
                      <NuxtLink
                        :to="activity.course.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.course.title }}
                      </NuxtLink>
                    </span>

                    <span v-else-if="activity.action === 'rated'">
                      rated
                      <NuxtLink
                        :to="activity.course.url"
                        class="text-indigo-600 hover:text-indigo-800"
                      >
                        {{ activity.course.title }}
                      </NuxtLink>
                      <span class="text-yellow-500 font-medium"
                        >{{ activity.rating }} stars</span
                      >
                    </span>
                  </p>
                  <div class="mt-1 text-xs text-gray-500">
                    {{ formatDate(activity.timestamp) }} ·
                    {{ formatTime(activity.timestamp) }}
                  </div>
                  <div v-if="isToday(activity.timestamp)" class="mt-1">
                    <span
                      class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full"
                      >Today</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom animation keyframes */
@keyframes slideInUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Apply animation to sections */
.animate-slide-in {
  animation: slideInUp 0.5s ease-out forwards;
}

/* Staggered animation delays */
.delay-100 {
  animation-delay: 100ms;
}
.delay-200 {
  animation-delay: 200ms;
}
.delay-300 {
  animation-delay: 300ms;
}
.delay-400 {
  animation-delay: 400ms;
}
.delay-500 {
  animation-delay: 500ms;
}
.delay-600 {
  animation-delay: 600ms;
}

/* Card hover effects */
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
