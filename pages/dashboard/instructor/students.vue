<!-- pages/dashboard/instructor/students/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Types
interface Student {
  id: number;
  name: string;
  email: string;
  avatar: string;
  enrollmentDate: string;
  lastActive: string;
  coursesEnrolled: number[];
  coursesCompleted: number;
  averageProgress: number;
  averageGrade?: number;
  hasCommunicated: boolean;
}

interface StudentFilter {
  enrollmentPeriod: string;
  activity: string;
  courseId: number | null;
  progress: string;
}

interface Course {
  id: number;
  title: string;
  category: string;
  students: number;
}

// Set the page layout to dashboard-instructor
definePageMeta({
  layout: "dashboard-instructor",
});

// Page meta
useHead({
  title: "My Students - Instructor Dashboard",
});

// State
const students = ref<Student[]>([]);
const courses = ref<Course[]>([]);
const isLoading = ref(true);
const selectedStudents = ref<number[]>([]);
const selectAll = ref(false);
const searchQuery = ref("");
const sortBy = ref("recently_active");
const showFilters = ref(false);
const showBulkActions = ref(false);
const showStudentDetails = ref(false);
const selectedStudent = ref<Student | null>(null);
const showSendMessageModal = ref(false);
const messageSubject = ref("");
const messageContent = ref("");
const isSendingMessage = ref(false);
const isExporting = ref(false);
const filters = ref<StudentFilter>({
  enrollmentPeriod: "all",
  activity: "all",
  courseId: null,
  progress: "all",
});

// Stats summary
const studentStats = ref({
  totalStudents: 0,
  activeStudents: 0,
  completionRate: 0,
  averageProgress: 0,
});

// Fetch students data (mock)
const fetchStudentData = async () => {
  try {
    isLoading.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Fetch courses first (mock data)
    courses.value = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        category: "Web Development",
        students: 1245,
      },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Mastery",
        category: "Web Development",
        students: 968,
      },
      {
        id: 3,
        title: "UI/UX Design Principles",
        category: "Design",
        students: 756,
      },
      {
        id: 8,
        title: "Responsive Web Design Masterclass",
        category: "Web Development",
        students: 523,
      },
    ];

    // Mock student data
    students.value = [
      {
        id: 1,
        name: "Rachel Chen",
        email: "rachel.chen@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-01-15T08:30:00Z",
        lastActive: "2025-04-12T14:25:00Z",
        coursesEnrolled: [1, 3],
        coursesCompleted: 1,
        averageProgress: 85,
        averageGrade: 92,
        hasCommunicated: true,
      },
      {
        id: 2,
        name: "Michael Rodriguez",
        email: "michael.rodriguez@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-02-03T10:15:00Z",
        lastActive: "2025-04-14T09:10:00Z",
        coursesEnrolled: [1, 2],
        coursesCompleted: 0,
        averageProgress: 62,
        averageGrade: 78,
        hasCommunicated: true,
      },
      {
        id: 3,
        name: "Jessica Taylor",
        email: "jessica.taylor@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2024-11-20T15:40:00Z",
        lastActive: "2025-04-10T11:05:00Z",
        coursesEnrolled: [1],
        coursesCompleted: 0,
        averageProgress: 45,
        averageGrade: 81,
        hasCommunicated: false,
      },
      {
        id: 4,
        name: "David Nguyen",
        email: "david.nguyen@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-03-05T09:20:00Z",
        lastActive: "2025-04-13T16:30:00Z",
        coursesEnrolled: [1, 2, 3],
        coursesCompleted: 1,
        averageProgress: 70,
        averageGrade: 85,
        hasCommunicated: true,
      },
      {
        id: 5,
        name: "Sarah Johnson",
        email: "sarah.johnson@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2024-12-10T11:30:00Z",
        lastActive: "2025-04-01T10:15:00Z",
        coursesEnrolled: [1, 8],
        coursesCompleted: 1,
        averageProgress: 90,
        averageGrade: 94,
        hasCommunicated: true,
      },
      {
        id: 6,
        name: "James Wilson",
        email: "james.wilson@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-01-22T14:10:00Z",
        lastActive: "2025-04-11T17:45:00Z",
        coursesEnrolled: [1, 2],
        coursesCompleted: 0,
        averageProgress: 35,
        averageGrade: 72,
        hasCommunicated: false,
      },
      {
        id: 7,
        name: "Emily Martinez",
        email: "emily.martinez@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-02-18T13:25:00Z",
        lastActive: "2025-04-14T12:30:00Z",
        coursesEnrolled: [3, 8],
        coursesCompleted: 1,
        averageProgress: 95,
        averageGrade: 97,
        hasCommunicated: true,
      },
      {
        id: 8,
        name: "Daniel Thompson",
        email: "daniel.thompson@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-03-10T09:50:00Z",
        lastActive: "2025-03-25T15:10:00Z",
        coursesEnrolled: [1],
        coursesCompleted: 0,
        averageProgress: 15,
        averageGrade: 65,
        hasCommunicated: false,
      },
      {
        id: 9,
        name: "Olivia Brown",
        email: "olivia.brown@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2024-12-05T10:40:00Z",
        lastActive: "2025-04-09T14:20:00Z",
        coursesEnrolled: [2, 8],
        coursesCompleted: 1,
        averageProgress: 88,
        averageGrade: 90,
        hasCommunicated: true,
      },
      {
        id: 10,
        name: "Ethan Clark",
        email: "ethan.clark@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-01-30T11:15:00Z",
        lastActive: "2025-04-05T10:45:00Z",
        coursesEnrolled: [1, 3],
        coursesCompleted: 0,
        averageProgress: 40,
        averageGrade: 75,
        hasCommunicated: false,
      },
      {
        id: 11,
        name: "Sophia Lee",
        email: "sophia.lee@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-02-22T16:30:00Z",
        lastActive: "2025-04-13T09:15:00Z",
        coursesEnrolled: [2, 3, 8],
        coursesCompleted: 1,
        averageProgress: 75,
        averageGrade: 88,
        hasCommunicated: true,
      },
      {
        id: 12,
        name: "Benjamin White",
        email: "benjamin.white@example.com",
        avatar: "/api/placeholder/40/40",
        enrollmentDate: "2025-03-15T13:45:00Z",
        lastActive: "2025-04-12T16:50:00Z",
        coursesEnrolled: [1, 2],
        coursesCompleted: 0,
        averageProgress: 55,
        averageGrade: 80,
        hasCommunicated: false,
      },
    ];

    // Calculate stats
    updateStudentStats();
  } catch (error) {
    console.error("Error fetching student data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Update student statistics
const updateStudentStats = () => {
  const now = new Date();
  const thirtyDaysAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);

  const activeStudents = students.value.filter((student) => {
    const lastActive = new Date(student.lastActive);
    return lastActive >= thirtyDaysAgo;
  });

  const totalCompletedCourses = students.value.reduce(
    (sum, student) => sum + student.coursesCompleted,
    0
  );
  const totalEnrolledCourses = students.value.reduce(
    (sum, student) => sum + student.coursesEnrolled.length,
    0
  );

  studentStats.value = {
    totalStudents: students.value.length,
    activeStudents: activeStudents.length,
    completionRate: totalEnrolledCourses
      ? Math.round((totalCompletedCourses / totalEnrolledCourses) * 100)
      : 0,
    averageProgress: students.value.length
      ? Math.round(
          students.value.reduce(
            (sum, student) => sum + student.averageProgress,
            0
          ) / students.value.length
        )
      : 0,
  };
};

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Calculate days since last activity
const getDaysSinceActive = (lastActiveDate: string) => {
  const now = new Date();
  const lastActive = new Date(lastActiveDate);
  const differenceInTime = now.getTime() - lastActive.getTime();
  const differenceInDays = Math.floor(differenceInTime / (1000 * 3600 * 24));

  return differenceInDays;
};

// Get activity status class
const getActivityStatusClass = (lastActiveDate: string) => {
  const daysSinceActive = getDaysSinceActive(lastActiveDate);

  if (daysSinceActive <= 3) {
    return "bg-green-100 text-green-800"; // Recently active
  } else if (daysSinceActive <= 14) {
    return "bg-blue-100 text-blue-800"; // Active
  } else if (daysSinceActive <= 30) {
    return "bg-yellow-100 text-yellow-800"; // Less active
  } else {
    return "bg-red-100 text-red-800"; // Inactive
  }
};

// Get activity status text
const getActivityStatus = (lastActiveDate: string) => {
  const daysSinceActive = getDaysSinceActive(lastActiveDate);

  if (daysSinceActive === 0) {
    return "Today";
  } else if (daysSinceActive === 1) {
    return "Yesterday";
  } else if (daysSinceActive <= 3) {
    return `${daysSinceActive} days ago`;
  } else if (daysSinceActive <= 14) {
    return "Active";
  } else if (daysSinceActive <= 30) {
    return "Less active";
  } else {
    return "Inactive";
  }
};

// Get progress color class
const getProgressColorClass = (progress: number) => {
  if (progress >= 75) {
    return "bg-green-500";
  } else if (progress >= 50) {
    return "bg-blue-500";
  } else if (progress >= 25) {
    return "bg-yellow-500";
  } else {
    return "bg-red-500";
  }
};

// Find course by ID
const getCourseById = (courseId: number) => {
  return courses.value.find((course) => course.id === courseId);
};

// Check if a student is enrolled in a specific course
const isEnrolledInCourse = (student: Student, courseId: number) => {
  return student.coursesEnrolled.includes(courseId);
};

// Filter and sort students
const filteredStudents = computed(() => {
  let result = [...students.value];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (student) =>
        student.name.toLowerCase().includes(query) ||
        student.email.toLowerCase().includes(query)
    );
  }

  // Apply enrollment period filter
  if (filters.value.enrollmentPeriod !== "all") {
    const now = new Date();

    if (filters.value.enrollmentPeriod === "last_30_days") {
      const thirtyDaysAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
      result = result.filter(
        (student) => new Date(student.enrollmentDate) >= thirtyDaysAgo
      );
    } else if (filters.value.enrollmentPeriod === "last_90_days") {
      const ninetyDaysAgo = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
      result = result.filter(
        (student) => new Date(student.enrollmentDate) >= ninetyDaysAgo
      );
    } else if (filters.value.enrollmentPeriod === "last_year") {
      const oneYearAgo = new Date(
        now.getFullYear() - 1,
        now.getMonth(),
        now.getDate()
      );
      result = result.filter(
        (student) => new Date(student.enrollmentDate) >= oneYearAgo
      );
    }
  }

  // Apply activity filter
  if (filters.value.activity !== "all") {
    const now = new Date();

    if (filters.value.activity === "active") {
      const fourteenDaysAgo = new Date(
        now.getTime() - 14 * 24 * 60 * 60 * 1000
      );
      result = result.filter(
        (student) => new Date(student.lastActive) >= fourteenDaysAgo
      );
    } else if (filters.value.activity === "inactive") {
      const thirtyDaysAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
      result = result.filter(
        (student) => new Date(student.lastActive) < thirtyDaysAgo
      );
    }
  }

  // Apply course filter
  if (filters.value.courseId !== null) {
    result = result.filter((student) =>
      isEnrolledInCourse(student, filters.value.courseId as number)
    );
  }

  // Apply progress filter
  if (filters.value.progress !== "all") {
    if (filters.value.progress === "high") {
      result = result.filter((student) => student.averageProgress >= 75);
    } else if (filters.value.progress === "medium") {
      result = result.filter(
        (student) =>
          student.averageProgress >= 40 && student.averageProgress < 75
      );
    } else if (filters.value.progress === "low") {
      result = result.filter((student) => student.averageProgress < 40);
    }
  }

  // Apply sorting
  if (sortBy.value === "recently_active") {
    result.sort(
      (a, b) =>
        new Date(b.lastActive).getTime() - new Date(a.lastActive).getTime()
    );
  } else if (sortBy.value === "name_a_to_z") {
    result.sort((a, b) => a.name.localeCompare(b.name));
  } else if (sortBy.value === "name_z_to_a") {
    result.sort((a, b) => b.name.localeCompare(a.name));
  } else if (sortBy.value === "enrollment_newest") {
    result.sort(
      (a, b) =>
        new Date(b.enrollmentDate).getTime() -
        new Date(a.enrollmentDate).getTime()
    );
  } else if (sortBy.value === "enrollment_oldest") {
    result.sort(
      (a, b) =>
        new Date(a.enrollmentDate).getTime() -
        new Date(b.enrollmentDate).getTime()
    );
  } else if (sortBy.value === "progress_highest") {
    result.sort((a, b) => b.averageProgress - a.averageProgress);
  } else if (sortBy.value === "progress_lowest") {
    result.sort((a, b) => a.averageProgress - b.averageProgress);
  }

  return result;
});

// Toggle select all students
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedStudents.value = filteredStudents.value.map(
      (student) => student.id
    );
  } else {
    selectedStudents.value = [];
  }
};

// Toggle select individual student
const toggleSelectStudent = (studentId: number) => {
  const index = selectedStudents.value.indexOf(studentId);
  if (index === -1) {
    selectedStudents.value.push(studentId);
  } else {
    selectedStudents.value.splice(index, 1);
  }

  // Update selectAll state
  selectAll.value =
    selectedStudents.value.length === filteredStudents.value.length;
};

// Reset all filters
const resetFilters = () => {
  filters.value = {
    enrollmentPeriod: "all",
    activity: "all",
    courseId: null,
    progress: "all",
  };
  searchQuery.value = "";
};

// View student details
const viewStudentDetails = (student: Student) => {
  selectedStudent.value = student;
  showStudentDetails.value = true;
};

// Show send message modal
const openSendMessageModal = (student?: Student) => {
  if (student) {
    // Send to a single student
    selectedStudents.value = [student.id];
  }

  showSendMessageModal.value = true;
  messageSubject.value = "";
  messageContent.value = "";
};

// Send a message to selected students
const sendMessage = async () => {
  if (!messageSubject.value || !messageContent.value) {
    return;
  }

  isSendingMessage.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mark students as communicated with
    students.value = students.value.map((student) => {
      if (selectedStudents.value.includes(student.id)) {
        return { ...student, hasCommunicated: true };
      }
      return student;
    });

    // Close modal
    showSendMessageModal.value = false;
    messageSubject.value = "";
    messageContent.value = "";

    // TODO: Show success message
  } catch (error) {
    console.error("Error sending message:", error);
    // TODO: Show error message
  } finally {
    isSendingMessage.value = false;
  }
};

// Export student data as CSV
const exportStudentData = async () => {
  isExporting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // In a real application, this would generate and download a CSV file
    // Here we're just simulating the action
    console.log("Exporting student data...");

    // TODO: Show success message
  } catch (error) {
    console.error("Error exporting student data:", error);
    // TODO: Show error message
  } finally {
    isExporting.value = false;
  }
};

// Load data on mount
onMounted(fetchStudentData);

// Watch for changes in selection and update UI
watch(selectedStudents, (newVal) => {
  showBulkActions.value = newVal.length > 0;
});
</script>

<template>
  <div>
    <!-- Page header with stats -->
    <div class="mb-8">
      <div
        class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">My Students</h1>
          <p class="text-gray-600">
            Manage your students, track progress, and provide support
          </p>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3">
          <button
            @click="exportStudentData"
            class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors shadow-sm"
            :disabled="isExporting"
          >
            <svg
              v-if="!isExporting"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else
              class="animate-spin h-5 w-5 mr-1"
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
            <span>{{ isExporting ? "Exporting..." : "Export Data" }}</span>
          </button>
          <button
            @click="openSendMessageModal()"
            :disabled="selectedStudents.length === 0"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
              />
              <path
                d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
              />
            </svg>
            <span>Message Students</span>
          </button>
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
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Total Students</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ studentStats.totalStudents }}
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
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Students</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ studentStats.activeStudents }}
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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Completion Rate</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ studentStats.completionRate }}%
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div class="bg-purple-100 rounded-full p-3 mr-4">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-purple-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Average Progress</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ studentStats.averageProgress }}%
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and search -->
    <div class="bg-white rounded-lg shadow mb-6 border border-gray-200">
      <div class="p-4 border-b border-gray-200">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
        >
          <!-- Search bar -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search students..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full md:w-80"
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

          <!-- Controls -->
          <div class="flex items-center space-x-2">
            <button
              @click="showFilters = !showFilters"
              class="px-3 py-2 border border-gray-300 rounded-lg flex items-center text-gray-700 hover:bg-gray-50 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>Filters</span>
              <span
                v-if="
                  filters.enrollmentPeriod !== 'all' ||
                  filters.activity !== 'all' ||
                  filters.courseId !== null ||
                  filters.progress !== 'all'
                "
                class="ml-1 bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded-full"
              >
                Active
              </span>
            </button>

            <div class="relative">
              <select
                v-model="sortBy"
                class="pl-3 pr-8 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="recently_active">Recently Active</option>
                <option value="name_a_to_z">Name (A-Z)</option>
                <option value="name_z_to_a">Name (Z-A)</option>
                <option value="enrollment_newest">Newest Students</option>
                <option value="enrollment_oldest">Oldest Students</option>
                <option value="progress_highest">Highest Progress</option>
                <option value="progress_lowest">Lowest Progress</option>
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

      <!-- Filter options (collapsible) -->
      <div v-if="showFilters" class="p-4 border-b border-gray-200 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Enrollment period filter -->
          <div>
            <label
              for="enrollment-period"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Enrollment Period</label
            >
            <select
              id="enrollment-period"
              v-model="filters.enrollmentPeriod"
              class="w-full pl-3 pr-8 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Time</option>
              <option value="last_30_days">Last 30 Days</option>
              <option value="last_90_days">Last 90 Days</option>
              <option value="last_year">Last Year</option>
            </select>
          </div>

          <!-- Activity filter -->
          <div>
            <label
              for="activity"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Activity Status</label
            >
            <select
              id="activity"
              v-model="filters.activity"
              class="w-full pl-3 pr-8 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Activity</option>
              <option value="active">Active (Last 14 Days)</option>
              <option value="inactive">Inactive (> 30 Days)</option>
            </select>
          </div>

          <!-- Course filter -->
          <div>
            <label
              for="course"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Enrolled Course</label
            >
            <select
              id="course"
              v-model="filters.courseId"
              class="w-full pl-3 pr-8 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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
          </div>

          <!-- Progress filter -->
          <div>
            <label
              for="progress"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Progress Level</label
            >
            <select
              id="progress"
              v-model="filters.progress"
              class="w-full pl-3 pr-8 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Progress</option>
              <option value="high">High (> 75%)</option>
              <option value="medium">Medium (40% - 75%)</option>
              <option value="low">Low (< 40%)</option>
            </select>
          </div>
        </div>

        <!-- Filter actions -->
        <div class="flex justify-end mt-4">
          <button
            @click="resetFilters"
            class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>

      <!-- Bulk actions (visible when students are selected) -->
      <div
        v-if="showBulkActions"
        class="p-4 border-b border-gray-200 bg-indigo-50 flex flex-wrap items-center justify-between"
      >
        <div class="flex items-center mb-2 md:mb-0">
          <span class="text-indigo-700 font-medium"
            >{{ selectedStudents.length }}
            {{ selectedStudents.length === 1 ? "student" : "students" }}
            selected</span
          >
        </div>
        <div class="flex flex-wrap items-center space-x-3">
          <button
            @click="openSendMessageModal()"
            class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
              />
              <path
                d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
              />
            </svg>
            <span>Message</span>
          </button>
          <button
            class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium flex items-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
              />
            </svg>
            <span>Assign to Group</span>
          </button>
          <button
            @click="selectedStudents = []"
            class="px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-sm font-medium"
          >
            Clear Selection
          </button>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="p-4 border-b border-gray-200">
        <div class="flex items-center">
          <div class="h-5 w-5 rounded bg-gray-200 mr-2"></div>
          <div class="h-5 w-32 bg-gray-200 rounded"></div>
        </div>
      </div>
      <div
        v-for="i in 5"
        :key="`skeleton-${i}`"
        class="border-b border-gray-200"
      >
        <div class="p-4 animate-pulse">
          <div class="flex items-center space-x-4">
            <div class="h-5 w-5 rounded bg-gray-200"></div>
            <div class="h-10 w-10 rounded-full bg-gray-200"></div>
            <div class="flex-1">
              <div class="h-5 bg-gray-200 rounded w-48 mb-2"></div>
              <div class="h-4 bg-gray-200 rounded w-32"></div>
            </div>
            <div class="h-8 w-24 bg-gray-200 rounded"></div>
            <div class="h-6 w-32 bg-gray-200 rounded"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Students table -->
    <div
      v-else-if="filteredStudents.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <!-- Table header -->
      <div
        class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center"
      >
        <div class="flex items-center w-full">
          <div class="w-12 flex-shrink-0">
            <input
              type="checkbox"
              :checked="selectAll"
              @change="toggleSelectAll"
              class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
          </div>
          <div class="flex flex-grow items-center">
            <div class="w-12 flex-shrink-0"></div>
            <div class="flex-grow">
              <span class="font-medium text-gray-700">Name</span>
            </div>
            <div class="w-40 flex-shrink-0 hidden md:block">
              <span class="font-medium text-gray-700">Enrollment</span>
            </div>
            <div class="w-32 flex-shrink-0 hidden lg:block">
              <span class="font-medium text-gray-700">Last Active</span>
            </div>
            <div class="w-32 hidden lg:block">
              <span class="font-medium text-gray-700">Progress</span>
            </div>
            <div class="w-12 flex-shrink-0"></div>
          </div>
        </div>
      </div>

      <!-- Table rows -->
      <div
        v-for="student in filteredStudents"
        :key="student.id"
        class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
      >
        <div class="px-4 py-3 flex items-center">
          <div class="w-12 flex-shrink-0">
            <input
              type="checkbox"
              :checked="selectedStudents.includes(student.id)"
              @change="toggleSelectStudent(student.id)"
              class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
          </div>
          <div class="flex flex-grow items-center">
            <!-- Avatar -->
            <div class="w-12 flex-shrink-0">
              <img
                :src="student.avatar"
                :alt="`${student.name}'s avatar`"
                class="h-10 w-10 rounded-full object-cover border border-gray-200"
              />
            </div>
            <!-- Name and email -->
            <div class="flex-grow min-w-0">
              <div class="font-medium text-gray-900">{{ student.name }}</div>
              <div class="text-sm text-gray-500 truncate">
                {{ student.email }}
              </div>
            </div>
            <!-- Enrollment date -->
            <div class="w-40 flex-shrink-0 hidden md:block">
              <div class="text-sm text-gray-700">
                {{ formatDate(student.enrollmentDate) }}
              </div>
              <div class="text-xs text-gray-500">
                {{ student.coursesEnrolled.length }}
                {{
                  student.coursesEnrolled.length === 1 ? "course" : "courses"
                }}
              </div>
            </div>
            <!-- Last active -->
            <div class="w-32 flex-shrink-0 hidden lg:block">
              <div class="text-sm">
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap"
                  :class="getActivityStatusClass(student.lastActive)"
                >
                  {{ getActivityStatus(student.lastActive) }}
                </span>
              </div>
            </div>
            <!-- Progress bar -->
            <div class="w-32 hidden lg:block">
              <div class="flex items-center">
                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                  <div
                    class="h-2 rounded-full"
                    :class="getProgressColorClass(student.averageProgress)"
                    :style="{ width: `${student.averageProgress}%` }"
                  ></div>
                </div>
                <span class="text-xs font-medium text-gray-700"
                  >{{ student.averageProgress }}%</span
                >
              </div>
            </div>
            <!-- Actions -->
            <div class="w-12 flex-shrink-0">
              <div class="relative">
                <button
                  @click="viewStudentDetails(student)"
                  class="p-2 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
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
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No students found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery ||
          filters.enrollmentPeriod !== "all" ||
          filters.activity !== "all" ||
          filters.courseId !== null ||
          filters.progress !== "all"
            ? "No students match your search criteria or filters."
            : "You don't have any students enrolled in your courses yet."
        }}
      </p>
      <button
        @click="resetFilters"
        v-if="
          searchQuery ||
          filters.enrollmentPeriod !== 'all' ||
          filters.activity !== 'all' ||
          filters.courseId !== null ||
          filters.progress !== 'all'
        "
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Clear Filters
      </button>
    </div>

    <!-- Pagination -->
    <div
      v-if="filteredStudents.length > 0"
      class="mt-6 flex justify-between items-center"
    >
      <div class="text-sm text-gray-700">
        Showing <span class="font-medium">{{ filteredStudents.length }}</span>
        {{ filteredStudents.length === 1 ? "student" : "students" }}
      </div>

      <!-- Pagination controls (just a mockup) -->
      <div class="flex space-x-1">
        <button
          disabled
          class="relative inline-flex items-center px-2 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        <button
          class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600 hover:bg-indigo-100"
        >
          1
        </button>
        <button
          disabled
          class="relative inline-flex items-center px-2 py-2 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Student Details Modal -->
    <div
      v-if="showStudentDetails && selectedStudent"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-4xl w-full p-6 shadow-xl overflow-hidden"
      >
        <!-- Modal header -->
        <div class="flex justify-between items-start mb-6">
          <div class="flex items-center">
            <img
              :src="selectedStudent.avatar"
              :alt="`${selectedStudent.name}'s avatar`"
              class="h-16 w-16 rounded-full object-cover border-2 border-indigo-200 mr-4"
            />
            <div>
              <h3 class="text-xl font-bold text-gray-900">
                {{ selectedStudent.name }}
              </h3>
              <p class="text-gray-600">{{ selectedStudent.email }}</p>
            </div>
          </div>
          <button
            @click="showStudentDetails = false"
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
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

        <!-- Modal content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Left column: Student info -->
          <div class="space-y-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">Status</h4>
              <span
                class="px-2 py-1 rounded-full text-xs font-medium"
                :class="getActivityStatusClass(selectedStudent.lastActive)"
              >
                {{ getActivityStatus(selectedStudent.lastActive) }}
              </span>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                Enrollment Date
              </h4>
              <p class="text-sm text-gray-700">
                {{ formatDate(selectedStudent.enrollmentDate) }}
              </p>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                Last Active
              </h4>
              <p class="text-sm text-gray-700">
                {{ formatDate(selectedStudent.lastActive) }}
              </p>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">
                Communication History
              </h4>
              <p
                v-if="selectedStudent.hasCommunicated"
                class="text-sm text-gray-700 flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-green-500 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                Recent communication
              </p>
              <p v-else class="text-sm text-gray-700 flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-yellow-500 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
                No recent communication
              </p>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">Actions</h4>
              <div class="space-y-2">
                <button
                  @click="openSendMessageModal(selectedStudent)"
                  class="w-full px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors flex items-center justify-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                    />
                    <path
                      d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                    />
                  </svg>
                  Send Message
                </button>
                <button
                  class="w-full px-3 py-2 text-sm border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors flex items-center justify-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                    />
                  </svg>
                  View Report
                </button>
              </div>
            </div>
          </div>

          <!-- Actions footer -->
          <div class="col-span-3 pt-4 border-t border-gray-200 mt-4">
            <div class="flex justify-end space-x-3">
              <button
                @click="showStudentDetails = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Send Message Modal -->
    <div
      v-if="showSendMessageModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full p-6 shadow-xl">
        <!-- Modal header -->
        <div class="flex justify-between items-start mb-6">
          <h3 class="text-lg font-bold text-gray-900">
            Send Message to
            {{
              selectedStudents.length === 1
                ? "1 Student"
                : selectedStudents.length + " Students"
            }}
          </h3>
          <button
            @click="showSendMessageModal = false"
            class="text-gray-400 hover:text-gray-500 focus:outline-none"
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

        <!-- Modal content -->
        <div class="space-y-4">
          <div>
            <label
              for="message-subject"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Subject</label
            >
            <input
              id="message-subject"
              v-model="messageSubject"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Enter message subject"
            />
          </div>

          <div>
            <label
              for="message-content"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Message</label
            >
            <textarea
              id="message-content"
              v-model="messageContent"
              rows="6"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Type your message here..."
            ></textarea>
          </div>

          <div class="flex items-center">
            <input
              id="send-email-copy"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
            />
            <label for="send-email-copy" class="ml-2 text-sm text-gray-700">
              Also send as email
            </label>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="mt-6 flex justify-end space-x-3">
          <button
            @click="showSendMessageModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none"
          >
            Cancel
          </button>
          <button
            @click="sendMessage"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
            :disabled="!messageSubject || !messageContent || isSendingMessage"
          >
            <svg
              v-if="isSendingMessage"
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
            <span>{{ isSendingMessage ? "Sending..." : "Send Message" }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Hover effect for student rows */
.student-row:hover {
  background-color: rgba(79, 70, 229, 0.05);
}
</style>
