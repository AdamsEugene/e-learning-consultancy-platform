<!-- pages/dashboard/instructor/assignments/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta for layout
definePageMeta({
  layout: "dashboard-instructor",
});

// Set page title
useHead({
  title: "Assignments - Instructor Dashboard",
});

// Types
interface Student {
  id: number;
  name: string;
  avatar: string;
  email: string;
}

interface Course {
  id: number;
  title: string;
  code: string;
}

interface Assignment {
  id: number;
  title: string;
  courseId: number;
  courseName: string;
  dueDate: string;
  status: "pending" | "graded" | "late" | "upcoming";
  submissionsCount: number;
  totalStudents: number;
  averageScore?: number;
  maxPoints: number;
  type: "practical" | "quiz" | "essay" | "project";
  createdAt: string;
}

interface Submission {
  id: number;
  assignmentId: number;
  studentId: number;
  student: Student;
  submittedAt: string;
  status: "pending" | "graded" | "late";
  score?: number;
  feedback?: string;
  attachments?: string[];
}

// State
const assignments = ref<Assignment[]>([]);
const submissions = ref<Submission[]>([]);
const students = ref<Student[]>([]);
const courses = ref<Course[]>([]);

const isLoading = ref(true);
const activeTab = ref("pending");
const searchQuery = ref("");
const selectedCourse = ref<number | null>(null);
const selectedAssignment = ref<Assignment | null>(null);
const selectedSubmission = ref<Submission | null>(null);
const showGradeModal = ref(false);
const showAssignmentDetailsModal = ref(false);
const sortBy = ref("recent");
const currentPage = ref(1);
const perPage = 10;

// Grading form state
const gradeForm = ref({
  score: 0,
  feedback: "",
  saving: false,
});

// Fetch data (mock data for demonstration)
const fetchData = async () => {
  try {
    isLoading.value = true;
    // Simulate API calls
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    courses.value = [
      { id: 1, title: "Complete Web Development Bootcamp", code: "WD101" },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Mastery",
        code: "JS201",
      },
      { id: 3, title: "UI/UX Design Principles", code: "UI101" },
      { id: 4, title: "React Native for Mobile Development", code: "RN202" },
      { id: 5, title: "Data Science with Python", code: "DS101" },
    ];

    students.value = [
      {
        id: 1,
        name: "James Wilson",
        avatar: "/api/placeholder/80/80",
        email: "james.wilson@example.com",
      },
      {
        id: 2,
        name: "Emma Thompson",
        avatar: "/api/placeholder/80/80",
        email: "emma.thompson@example.com",
      },
      {
        id: 3,
        name: "Lucas Rodriguez",
        avatar: "/api/placeholder/80/80",
        email: "lucas.rodriguez@example.com",
      },
      {
        id: 4,
        name: "Mia Johnson",
        avatar: "/api/placeholder/80/80",
        email: "mia.johnson@example.com",
      },
      {
        id: 5,
        name: "Noah Williams",
        avatar: "/api/placeholder/80/80",
        email: "noah.williams@example.com",
      },
      {
        id: 6,
        name: "Olivia Brown",
        avatar: "/api/placeholder/80/80",
        email: "olivia.brown@example.com",
      },
      {
        id: 7,
        name: "Liam Davis",
        avatar: "/api/placeholder/80/80",
        email: "liam.davis@example.com",
      },
      {
        id: 8,
        name: "Sophia Martinez",
        avatar: "/api/placeholder/80/80",
        email: "sophia.martinez@example.com",
      },
    ];

    assignments.value = [
      {
        id: 1,
        title: "Final Project: Personal Portfolio Website",
        courseId: 1,
        courseName: "Complete Web Development Bootcamp",
        dueDate: "2025-04-20T23:59:59Z",
        status: "pending",
        submissionsCount: 5,
        totalStudents: 8,
        maxPoints: 100,
        type: "project",
        createdAt: "2025-03-20T10:30:00Z",
      },
      {
        id: 2,
        title: "JavaScript Advanced Concepts Quiz",
        courseId: 2,
        courseName: "Advanced JavaScript: From Fundamentals to Mastery",
        dueDate: "2025-04-15T23:59:59Z",
        status: "pending",
        submissionsCount: 6,
        totalStudents: 6,
        maxPoints: 50,
        type: "quiz",
        createdAt: "2025-03-25T14:45:00Z",
      },
      {
        id: 3,
        title: "UI/UX Case Study Analysis",
        courseId: 3,
        courseName: "UI/UX Design Principles",
        dueDate: "2025-04-10T23:59:59Z",
        status: "graded",
        submissionsCount: 4,
        totalStudents: 4,
        averageScore: 88,
        maxPoints: 100,
        type: "essay",
        createdAt: "2025-03-15T09:15:00Z",
      },
      {
        id: 4,
        title: "Build a React Native Weather App",
        courseId: 4,
        courseName: "React Native for Mobile Development",
        dueDate: "2025-04-22T23:59:59Z",
        status: "upcoming",
        submissionsCount: 0,
        totalStudents: 6,
        maxPoints: 100,
        type: "practical",
        createdAt: "2025-04-01T11:20:00Z",
      },
      {
        id: 5,
        title: "Data Visualization Project",
        courseId: 5,
        courseName: "Data Science with Python",
        dueDate: "2025-04-05T23:59:59Z",
        status: "late",
        submissionsCount: 4,
        totalStudents: 5,
        maxPoints: 75,
        type: "project",
        createdAt: "2025-03-10T16:50:00Z",
      },
      {
        id: 6,
        title: "Responsive Design Challenge",
        courseId: 1,
        courseName: "Complete Web Development Bootcamp",
        dueDate: "2025-03-30T23:59:59Z",
        status: "graded",
        submissionsCount: 8,
        totalStudents: 8,
        averageScore: 92,
        maxPoints: 50,
        type: "practical",
        createdAt: "2025-03-05T08:30:00Z",
      },
    ];

    submissions.value = [
      {
        id: 1,
        assignmentId: 1,
        studentId: 1,
        student: students.value.find((s) => s.id === 1)!,
        submittedAt: "2025-04-18T15:30:00Z",
        status: "pending",
        attachments: ["portfolio_project.zip", "documentation.pdf"],
      },
      {
        id: 2,
        assignmentId: 1,
        studentId: 2,
        student: students.value.find((s) => s.id === 2)!,
        submittedAt: "2025-04-19T10:45:00Z",
        status: "pending",
        attachments: ["portfolio_files.zip", "readme.md"],
      },
      {
        id: 3,
        assignmentId: 1,
        studentId: 3,
        student: students.value.find((s) => s.id === 3)!,
        submittedAt: "2025-04-17T22:15:00Z",
        status: "pending",
        attachments: ["project_submission.zip"],
      },
      {
        id: 4,
        assignmentId: 2,
        studentId: 4,
        student: students.value.find((s) => s.id === 4)!,
        submittedAt: "2025-04-14T16:20:00Z",
        status: "pending",
        attachments: ["quiz_answers.pdf"],
      },
      {
        id: 5,
        assignmentId: 2,
        studentId: 5,
        student: students.value.find((s) => s.id === 5)!,
        submittedAt: "2025-04-14T14:10:00Z",
        status: "pending",
        attachments: ["quiz_solutions.docx"],
      },
      {
        id: 6,
        assignmentId: 3,
        studentId: 6,
        student: students.value.find((s) => s.id === 6)!,
        submittedAt: "2025-04-09T11:30:00Z",
        status: "graded",
        score: 92,
        feedback:
          "Excellent analysis with thoughtful insights. Your recommendations were well-supported by research.",
        attachments: ["case_study_analysis.pdf"],
      },
      {
        id: 7,
        assignmentId: 5,
        studentId: 7,
        student: students.value.find((s) => s.id === 7)!,
        submittedAt: "2025-04-06T09:45:00Z",
        status: "late",
        attachments: ["data_viz_project.zip", "presentation.pptx"],
      },
      {
        id: 8,
        assignmentId: 6,
        studentId: 8,
        student: students.value.find((s) => s.id === 8)!,
        submittedAt: "2025-03-29T20:15:00Z",
        status: "graded",
        score: 95,
        feedback:
          "Outstanding work on the responsive design. Your solution works flawlessly across all device sizes.",
        attachments: ["responsive_challenge.zip"],
      },
    ];
  } catch (error) {
    console.error("Error fetching data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter assignments based on active tab, search query, and course filter
const filteredAssignments = computed(() => {
  let result = [...assignments.value];

  // Apply tab filter
  if (activeTab.value !== "all") {
    result = result.filter(
      (assignment) => assignment.status === activeTab.value
    );
  }

  // Apply course filter
  if (selectedCourse.value) {
    result = result.filter(
      (assignment) => assignment.courseId === selectedCourse.value
    );
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (assignment) =>
        assignment.title.toLowerCase().includes(query) ||
        assignment.courseName.toLowerCase().includes(query)
    );
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
    );
  } else if (sortBy.value === "due_date") {
    result.sort(
      (a, b) => new Date(a.dueDate).getTime() - new Date(b.dueDate).getTime()
    );
  } else if (sortBy.value === "title") {
    result.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sortBy.value === "submission_rate") {
    result.sort(
      (a, b) =>
        b.submissionsCount / b.totalStudents -
        a.submissionsCount / a.totalStudents
    );
  }

  return result;
});

// Calculate total for pagination
const totalPages = computed(() => {
  return Math.ceil(filteredAssignments.value.length / perPage);
});

// Get paginated assignments
const paginatedAssignments = computed(() => {
  const startIdx = (currentPage.value - 1) * perPage;
  const endIdx = startIdx + perPage;
  return filteredAssignments.value.slice(startIdx, endIdx);
});

// Get submissions for a specific assignment
const getSubmissionsForAssignment = (assignmentId: number) => {
  return submissions.value.filter(
    (submission) => submission.assignmentId === assignmentId
  );
};

// Get pending submissions count
const pendingSubmissionsCount = computed(() => {
  return submissions.value.filter(
    (submission) => submission.status === "pending"
  ).length;
});

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format date with time
const formatDateTime = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Calculate days remaining until due date
const getDaysRemaining = (dueDate: string) => {
  const now = new Date();
  const due = new Date(dueDate);
  const diffTime = due.getTime() - now.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays < 0) {
    return "Past due";
  } else if (diffDays === 0) {
    return "Due today";
  } else if (diffDays === 1) {
    return "1 day left";
  } else {
    return `${diffDays} days left`;
  }
};

// Get status badge classes
const getStatusBadgeClasses = (status: string) => {
  switch (status) {
    case "pending":
      return "bg-amber-100 text-amber-800";
    case "graded":
      return "bg-green-100 text-green-800";
    case "late":
      return "bg-red-100 text-red-800";
    case "upcoming":
      return "bg-blue-100 text-blue-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get assignment type badge classes
const getTypeBadgeClasses = (type: string) => {
  switch (type) {
    case "practical":
      return "bg-purple-100 text-purple-800";
    case "quiz":
      return "bg-blue-100 text-blue-800";
    case "essay":
      return "bg-indigo-100 text-indigo-800";
    case "project":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Assignment details and submissions
const viewAssignmentDetails = (assignment: Assignment) => {
  selectedAssignment.value = assignment;
  showAssignmentDetailsModal.value = true;
};

// Open grading modal
const openGradeSubmission = (submission: Submission) => {
  selectedSubmission.value = submission;

  // Reset form
  gradeForm.value = {
    score: submission.score || 0,
    feedback: submission.feedback || "",
    saving: false,
  };

  showGradeModal.value = true;
};

// Submit grade
const submitGrade = async () => {
  if (!selectedSubmission.value || !selectedAssignment.value) return;

  try {
    gradeForm.value.saving = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Update submission with grade data
    const submissionIndex = submissions.value.findIndex(
      (s) => s.id === selectedSubmission.value?.id
    );
    if (submissionIndex !== -1) {
      submissions.value[submissionIndex] = {
        ...submissions.value[submissionIndex],
        status: "graded",
        score: gradeForm.value.score,
        feedback: gradeForm.value.feedback,
      };
    }

    // Check if all submissions for this assignment are graded
    const assignmentSubmissions = submissions.value.filter(
      (s) => s.assignmentId === selectedAssignment.value?.id
    );
    const allGraded = assignmentSubmissions.every((s) => s.status === "graded");

    // Update assignment status if all submissions are graded
    if (allGraded) {
      const assignmentIndex = assignments.value.findIndex(
        (a) => a.id === selectedAssignment.value?.id
      );
      if (assignmentIndex !== -1) {
        assignments.value[assignmentIndex] = {
          ...assignments.value[assignmentIndex],
          status: "graded",
          averageScore:
            assignmentSubmissions.reduce((sum, s) => sum + (s.score || 0), 0) /
            assignmentSubmissions.length,
        };
      }
    }

    // Close modal
    showGradeModal.value = false;
  } catch (error) {
    console.error("Error submitting grade:", error);
  } finally {
    gradeForm.value.saving = false;
  }
};

// Pagination navigation
const goToPage = (page: number) => {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
};

// Initialize data on component mount
onMounted(() => {
  fetchData();
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Assignments</h1>
          <p class="text-gray-600">
            Create, manage, and grade student assignments
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
          <NuxtLink
            to="/dashboard/instructor/assignments/create"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Create Assignment</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Stats cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-indigo-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-indigo-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Assignments</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ assignments.length }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-amber-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-amber-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">
                Pending Submissions
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ pendingSubmissionsCount }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-green-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-green-600"
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
            <div>
              <p class="text-sm text-gray-500 font-medium">
                Graded Assignments
              </p>
              <p class="text-2xl font-bold text-gray-900">
                {{ assignments.filter((a) => a.status === "graded").length }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-blue-600"
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
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Courses</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ courses.length }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Tab navigation -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            @click="activeTab = 'all'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'all'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            All Assignments
          </button>
          <button
            @click="activeTab = 'pending'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'pending'
                ? 'bg-amber-100 text-amber-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            <span class="relative">
              Pending
              <span
                v-if="pendingSubmissionsCount > 0"
                class="absolute -top-2 -right-2 flex items-center justify-center bg-amber-500 text-white text-xs font-bold w-5 h-5 rounded-full"
              >
                {{ pendingSubmissionsCount }}
              </span>
            </span>
          </button>
          <button
            @click="activeTab = 'graded'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'graded'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Graded
          </button>
          <button
            @click="activeTab = 'late'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'late'
                ? 'bg-red-100 text-red-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Late
          </button>
          <button
            @click="activeTab = 'upcoming'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'upcoming'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Upcoming
          </button>
        </div>

        <!-- Search and filters -->
        <div
          class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-2"
        >
          <div class="relative w-full md:w-auto">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search assignments..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div class="absolute left-3 top-2.5 text-gray-400">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>

          <!-- Course filter -->
          <div class="relative w-full md:w-auto">
            <select
              v-model="selectedCourse"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg w-full md:w-56 appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option :value="null">All Courses</option>
              <option
                v-for="course in courses"
                :key="course.id"
                :value="course.id"
              >
                {{ course.title }}
              </option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>

          <!-- Sort options -->
          <div class="relative w-full md:w-auto">
            <select
              v-model="sortBy"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg w-full md:w-44 appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="recent">Recently Created</option>
              <option value="due_date">Due Date</option>
              <option value="title">Title A-Z</option>
              <option value="submission_rate">Submission Rate</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div
        v-for="i in 3"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="flex-1">
            <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-24 bg-gray-200 rounded"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Assignment list -->
    <div
      v-else-if="filteredAssignments.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Assignment
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Course
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Due Date
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Submissions
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="assignment in paginatedAssignments"
              :key="assignment.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ assignment.title }}
                    </div>
                    <div class="text-sm text-gray-500 flex items-center mt-1">
                      <span
                        class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium mr-2"
                        :class="getTypeBadgeClasses(assignment.type)"
                      >
                        {{
                          assignment.type.charAt(0).toUpperCase() +
                          assignment.type.slice(1)
                        }}
                      </span>
                      <span>{{ formatDate(assignment.createdAt) }}</span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ assignment.courseName }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full"
                  :class="getStatusBadgeClasses(assignment.status)"
                >
                  {{
                    assignment.status.charAt(0).toUpperCase() +
                    assignment.status.slice(1)
                  }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ formatDate(assignment.dueDate) }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ getDaysRemaining(assignment.dueDate) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ assignment.submissionsCount }} /
                  {{ assignment.totalStudents }}
                </div>
                <div
                  v-if="assignment.averageScore"
                  class="text-xs text-gray-500"
                >
                  Avg. Score: {{ assignment.averageScore.toFixed(1) }}%
                </div>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <button
                  @click="viewAssignmentDetails(assignment)"
                  class="text-indigo-600 hover:text-indigo-900 ml-2"
                >
                  View
                </button>
                <NuxtLink
                  :to="`/dashboard/instructor/assignments/${assignment.id}/edit`"
                  class="text-gray-600 hover:text-gray-900 ml-2"
                >
                  Edit
                </NuxtLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else
      class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
    >
      <div
        class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">
        No assignments found
      </h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No assignments match your search criteria."
            : "You haven't created any assignments yet."
        }}
      </p>
      <NuxtLink
        to="/dashboard/instructor/assignments/create"
        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
            clip-rule="evenodd"
          />
        </svg>
        <span>Create Your First Assignment</span>
      </NuxtLink>
    </div>

    <!-- Pagination -->
    <div v-if="filteredAssignments.length > perPage" class="mt-6">
      <nav class="flex justify-center">
        <ul class="flex items-center">
          <li>
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-1 rounded-md mr-2 border border-gray-300"
              :class="
                currentPage === 1
                  ? 'opacity-50 cursor-not-allowed'
                  : 'hover:bg-gray-100'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </li>
          <li v-for="page in totalPages" :key="`page-${page}`">
            <button
              @click="goToPage(page)"
              class="px-3 py-1 rounded-md mx-1 border"
              :class="
                currentPage === page
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : 'text-gray-700 border-gray-300 hover:bg-gray-100'
              "
            >
              {{ page }}
            </button>
          </li>
          <li>
            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 rounded-md ml-2 border border-gray-300"
              :class="
                currentPage === totalPages
                  ? 'opacity-50 cursor-not-allowed'
                  : 'hover:bg-gray-100'
              "
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
            </button>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Assignment Details Modal -->
    <div
      v-if="showAssignmentDetailsModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="assignment-modal"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="showAssignmentDetailsModal = false"
        ></div>

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full"
        >
          <div v-if="selectedAssignment" class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <div class="flex justify-between items-start">
              <div>
                <h3
                  class="text-lg leading-6 font-medium text-gray-900"
                  id="assignment-modal"
                >
                  {{ selectedAssignment.title }}
                </h3>
                <div class="mt-2 flex items-center flex-wrap gap-2">
                  <span class="text-sm text-gray-500">{{
                    selectedAssignment.courseName
                  }}</span>
                  <span
                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusBadgeClasses(selectedAssignment.status)"
                  >
                    {{
                      selectedAssignment.status.charAt(0).toUpperCase() +
                      selectedAssignment.status.slice(1)
                    }}
                  </span>
                  <span
                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="getTypeBadgeClasses(selectedAssignment.type)"
                  >
                    {{
                      selectedAssignment.type.charAt(0).toUpperCase() +
                      selectedAssignment.type.slice(1)
                    }}
                  </span>
                  <span class="text-sm text-gray-500">
                    Due: {{ formatDate(selectedAssignment.dueDate) }}
                  </span>
                </div>
              </div>
              <button
                @click="showAssignmentDetailsModal = false"
                class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
              >
                <svg
                  class="h-6 w-6"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <!-- Assignment details -->
            <div
              class="mt-4 border-t border-b border-gray-200 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4"
            >
              <div>
                <h4 class="text-sm font-medium text-gray-500">Points</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ selectedAssignment.maxPoints }}
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Submissions</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ selectedAssignment.submissionsCount }} /
                  {{ selectedAssignment.totalStudents }}
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Average Score</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{
                    selectedAssignment.averageScore
                      ? `${selectedAssignment.averageScore.toFixed(1)}%`
                      : "Not graded yet"
                  }}
                </p>
              </div>
            </div>

            <!-- Student submissions -->
            <div class="mt-6">
              <h4 class="text-base font-medium text-gray-900 mb-3">
                Student Submissions
              </h4>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Student
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Submitted
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Status
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Score
                      </th>
                      <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="submission in getSubmissionsForAssignment(
                        selectedAssignment.id
                      )"
                      :key="submission.id"
                    >
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img
                              :src="submission.student.avatar"
                              :alt="submission.student.name"
                              class="h-10 w-10 rounded-full"
                            />
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ submission.student.name }}
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ submission.student.email }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ formatDateTime(submission.submittedAt) }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full"
                          :class="getStatusBadgeClasses(submission.status)"
                        >
                          {{
                            submission.status.charAt(0).toUpperCase() +
                            submission.status.slice(1)
                          }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div
                          v-if="submission.score !== undefined"
                          class="text-sm text-gray-900"
                        >
                          {{ submission.score }} /
                          {{ selectedAssignment.maxPoints }}
                        </div>
                        <div v-else class="text-sm text-gray-500">
                          Not graded
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button
                          @click="openGradeSubmission(submission)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          {{
                            submission.status === "graded"
                              ? "Edit Grade"
                              : "Grade"
                          }}
                        </button>
                        <a
                          href="#"
                          class="text-gray-600 hover:text-gray-900 ml-3"
                          @click.prevent="showAssignmentDetailsModal = false"
                        >
                          View
                        </a>
                      </td>
                    </tr>
                    <!-- Show message when no submissions yet -->
                    <tr
                      v-if="
                        getSubmissionsForAssignment(selectedAssignment.id)
                          .length === 0
                      "
                    >
                      <td
                        colspan="5"
                        class="px-6 py-4 text-center text-sm text-gray-500"
                      >
                        No submissions yet for this assignment.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="showAssignmentDetailsModal = false"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Grade Submission Modal -->
    <div
      v-if="showGradeModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="grade-modal"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="showGradeModal = false"
        ></div>

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div
            v-if="selectedSubmission && selectedAssignment"
            class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"
          >
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3
                  class="text-lg leading-6 font-medium text-gray-900"
                  id="grade-modal"
                >
                  Grade Submission
                </h3>
                <div class="mt-2">
                  <div class="flex items-center mb-4">
                    <img
                      :src="selectedSubmission.student.avatar"
                      :alt="selectedSubmission.student.name"
                      class="h-10 w-10 rounded-full mr-3"
                    />
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ selectedSubmission.student.name }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ formatDateTime(selectedSubmission.submittedAt) }}
                      </p>
                    </div>
                  </div>

                  <!-- Attachments -->
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Attachments
                    </label>
                    <div class="flex flex-wrap gap-2">
                      <a
                        v-for="(
                          attachment, index
                        ) in selectedSubmission.attachments"
                        :key="index"
                        href="#"
                        class="inline-flex items-center px-3 py-1 rounded-md text-sm bg-gray-100 text-gray-800 hover:bg-gray-200"
                        @click.prevent
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4 mr-1"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                            clip-rule="evenodd"
                          />
                        </svg>
                        {{ attachment }}
                      </a>
                    </div>
                  </div>

                  <!-- Grade Form -->
                  <div class="mt-6">
                    <div class="mb-4">
                      <label
                        for="score"
                        class="block text-sm font-medium text-gray-700 mb-1"
                      >
                        Score (out of {{ selectedAssignment.maxPoints }})
                      </label>
                      <input
                        id="score"
                        v-model.number="gradeForm.score"
                        type="number"
                        min="0"
                        :max="selectedAssignment.maxPoints"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>
                    <div class="mb-4">
                      <label
                        for="feedback"
                        class="block text-sm font-medium text-gray-700 mb-1"
                      >
                        Feedback
                      </label>
                      <textarea
                        id="feedback"
                        v-model="gradeForm.feedback"
                        rows="4"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        placeholder="Provide feedback to the student..."
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
              @click="submitGrade"
              :disabled="gradeForm.saving"
            >
              <svg
                v-if="gradeForm.saving"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              {{ gradeForm.saving ? "Saving..." : "Save Grade" }}
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              @click="showGradeModal = false"
              :disabled="gradeForm.saving"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation for pulse effect */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Animation for spin effect */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
