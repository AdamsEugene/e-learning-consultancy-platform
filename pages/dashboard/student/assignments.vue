<!-- pages/dashboard/assignments.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type {
  Assignment,
  AssignmentStatus,
  AssignmentType,
  SortOption,
  Course,
} from "~/types/assignments";

// Set page layout
definePageMeta({
  layout: "dashboard-student",
});

// State
const isLoading = ref(true);
const isLoaded = ref(false);
const selectedAssignment = ref<Assignment | null>(null);
const showAssignmentDetail = ref(false);

// Filter and sort state
const filterState = ref({
  status: "all" as AssignmentStatus,
  type: "all" as AssignmentType,
  course: "all" as number | "all",
  search: "",
  sort: "dueDate-asc" as SortOption,
  showCompleted: true,
});

// Mock data - in a real app, this would come from an API
const courses = ref<Course[]>([
  { id: 1, title: "Complete Web Development Bootcamp" },
  { id: 2, title: "UI/UX Design Masterclass" },
  { id: 3, title: "JavaScript Algorithms and Data Structures" },
  { id: 4, title: "Mobile App Development with React Native" },
]);

// Mock assignments data
const allAssignments = ref<Assignment[]>([
  {
    id: 1,
    title: "Responsive Portfolio Project",
    courseId: 1,
    courseName: "Complete Web Development Bootcamp",
    dueDate: "2023-10-27T23:59:59",
    status: "pending",
    type: "project",
    description:
      "Create a responsive portfolio website that showcases your skills and projects. Use HTML, CSS, and JavaScript. Make sure it works well on mobile devices.",
    attachments: [
      { name: "Project Requirements.pdf", url: "#", type: "pdf" },
      { name: "Design Mockup.fig", url: "#", type: "figma" },
    ],
  },
  {
    id: 2,
    title: "User Interface Design Challenge",
    courseId: 2,
    courseName: "UI/UX Design Masterclass",
    dueDate: "2023-10-25T23:59:59",
    status: "pending",
    type: "assignment",
    description:
      "Redesign the homepage of a website of your choice. Identify UX problems and provide solutions in your redesign. Submit both the analysis and the redesign files.",
    attachments: [
      { name: "Assignment Instructions.pdf", url: "#", type: "pdf" },
    ],
  },
  {
    id: 3,
    title: "Algorithm Efficiency Quiz",
    courseId: 3,
    courseName: "JavaScript Algorithms and Data Structures",
    dueDate: "2023-10-24T23:59:59",
    status: "late",
    type: "quiz",
    description:
      "Complete the online quiz on Big O Notation and algorithm efficiency. You will have 60 minutes to complete the quiz once started.",
    grade: 0,
    maxGrade: 100,
  },
  {
    id: 4,
    title: "CSS Grid Layout Exercise",
    courseId: 1,
    courseName: "Complete Web Development Bootcamp",
    dueDate: "2023-10-15T23:59:59",
    status: "submitted",
    submittedFiles: [
      { name: "grid-layout.zip", url: "#", uploadDate: "2023-10-14T18:30:00" },
    ],
    type: "assignment",
    description:
      "Build a complex layout using CSS Grid. Implement responsive behavior and ensure good browser compatibility.",
  },
  {
    id: 5,
    title: "Persona Development",
    courseId: 2,
    courseName: "UI/UX Design Masterclass",
    dueDate: "2023-10-10T23:59:59",
    status: "graded",
    grade: 92,
    maxGrade: 100,
    feedback:
      "Excellent work on your personas! They are well-researched and showcase different user needs. Consider adding more specific demographics and technical proficiency details in future work.",
    type: "assignment",
    description:
      "Create three user personas for a mobile banking app. Include user goals, frustrations, and scenarios where they would use the app.",
    submittedFiles: [
      {
        name: "banking-app-personas.pdf",
        url: "#",
        uploadDate: "2023-10-09T12:15:00",
      },
    ],
  },
  {
    id: 6,
    title: "React Native Navigation Implementation",
    courseId: 4,
    courseName: "Mobile App Development with React Native",
    dueDate: "2023-10-30T23:59:59",
    status: "pending",
    type: "project",
    description:
      "Implement a navigation system in a React Native app using React Navigation. Create a tab navigator and a stack navigator with at least 5 screens.",
    attachments: [
      { name: "Navigation Requirements.pdf", url: "#", type: "pdf" },
      { name: "Starter Code.zip", url: "#", type: "zip" },
    ],
  },
  {
    id: 7,
    title: "Linked List Implementation",
    courseId: 3,
    courseName: "JavaScript Algorithms and Data Structures",
    dueDate: "2023-10-05T23:59:59",
    status: "graded",
    grade: 88,
    maxGrade: 100,
    feedback:
      "Your linked list implementation works correctly and includes all required methods. Good job on handling edge cases. There's room for optimization in the 'reverse' method.",
    type: "assignment",
    description:
      "Implement a doubly linked list in JavaScript with methods for insertion, deletion, and traversal.",
    submittedFiles: [
      { name: "linked-list.js", url: "#", uploadDate: "2023-10-05T10:20:00" },
    ],
  },
  {
    id: 8,
    title: "Mobile App Wireframing",
    courseId: 2,
    courseName: "UI/UX Design Masterclass",
    dueDate: "2023-11-05T23:59:59",
    status: "pending",
    type: "project",
    description:
      "Create wireframes for a fitness tracking mobile app. Include at least 10 screens covering the main user flows.",
    attachments: [
      { name: "Wireframing Guidelines.pdf", url: "#", type: "pdf" },
    ],
  },
  {
    id: 9,
    title: "API Integration Quiz",
    courseId: 1,
    courseName: "Complete Web Development Bootcamp",
    dueDate: "2023-09-28T23:59:59",
    status: "graded",
    grade: 95,
    maxGrade: 100,
    feedback:
      "Outstanding work! You demonstrated excellent understanding of RESTful API concepts and implementation details.",
    type: "quiz",
    description:
      "A quiz on RESTful API concepts, fetch API, and handling asynchronous operations in JavaScript.",
  },
  {
    id: 10,
    title: "React Component Optimization",
    courseId: 4,
    courseName: "Mobile App Development with React Native",
    dueDate: "2023-11-10T23:59:59",
    status: "pending",
    type: "assignment",
    description:
      "Optimize the performance of provided React Native components using memoization, useCallback, and proper state management.",
    attachments: [
      { name: "Unoptimized Components.zip", url: "#", type: "zip" },
      { name: "Performance Requirements.pdf", url: "#", type: "pdf" },
    ],
  },
]);

// Get assignment counts by status
const assignmentCounts = computed(() => {
  return {
    pending: allAssignments.value.filter((a) => a.status === "pending").length,
    submitted: allAssignments.value.filter((a) => a.status === "submitted")
      .length,
    graded: allAssignments.value.filter((a) => a.status === "graded").length,
    late: allAssignments.value.filter((a) => a.status === "late").length,
  };
});

// Computed filtered and sorted assignments
const filteredAssignments = computed(() => {
  const { status, type, course, search, sort, showCompleted } =
    filterState.value;

  let filtered = [...allAssignments.value];

  // Only show completed (graded) if enabled
  if (!showCompleted && status !== "graded") {
    filtered = filtered.filter((a) => a.status !== "graded");
  }

  // Filter by status
  if (status !== "all") {
    filtered = filtered.filter((a) => a.status === status);
  }

  // Filter by type
  if (type !== "all") {
    filtered = filtered.filter((a) => a.type === type);
  }

  // Filter by course
  if (course !== "all") {
    filtered = filtered.filter((a) => a.courseId === course);
  }

  // Filter by search query
  if (search) {
    const query = search.toLowerCase();
    filtered = filtered.filter(
      (a) =>
        a.title.toLowerCase().includes(query) ||
        a.courseName.toLowerCase().includes(query) ||
        (a.description && a.description.toLowerCase().includes(query))
    );
  }

  // Sort assignments
  switch (sort) {
    case "dueDate-asc":
      filtered.sort(
        (a, b) => new Date(a.dueDate).getTime() - new Date(b.dueDate).getTime()
      );
      break;
    case "dueDate-desc":
      filtered.sort(
        (a, b) => new Date(b.dueDate).getTime() - new Date(a.dueDate).getTime()
      );
      break;
    case "title-asc":
      filtered.sort((a, b) => a.title.localeCompare(b.title));
      break;
    case "title-desc":
      filtered.sort((a, b) => b.title.localeCompare(a.title));
      break;
    case "course-asc":
      filtered.sort((a, b) => a.courseName.localeCompare(b.courseName));
      break;
    case "course-desc":
      filtered.sort((a, b) => b.courseName.localeCompare(a.courseName));
      break;
  }

  return filtered;
});

// View assignment details
const viewAssignmentDetails = (assignment: Assignment) => {
  selectedAssignment.value = assignment;
  showAssignmentDetail.value = true;
};

// Update filters
const updateFilters = (newFilters: Partial<typeof filterState.value>) => {
  filterState.value = { ...filterState.value, ...newFilters };
};

// Handle assignment submission
const handleAssignmentSubmitted = (assignmentId: number, files: File[]) => {
  // Find and update the assignment
  const assignment = allAssignments.value.find((a) => a.id === assignmentId);
  if (assignment) {
    assignment.status = "submitted";
    assignment.submittedFiles = files.map((file) => ({
      name: file.name,
      url: "#",
      uploadDate: new Date().toISOString(),
    }));
  }

  // Close detail view
  showAssignmentDetail.value = false;
  setTimeout(() => {
    selectedAssignment.value = null;
  }, 300);
};

// Load data with simulated delay
onMounted(() => {
  // Simulate API fetch
  setTimeout(() => {
    isLoading.value = false;

    // Small delay for loading animation
    setTimeout(() => {
      isLoaded.value = true;
    }, 100);
  }, 600);
});
</script>

<template>
  <div>
    <!-- Header section with stats -->
    <DashboardStudentAssignmentsHeader
      :counts="assignmentCounts"
      :is-loaded="isLoaded"
    />

    <!-- Filter controls -->
    <DashboardStudentAssignmentsFilter
      :courses="courses"
      :filter-state="filterState"
      :is-loaded="isLoaded"
      @update:filters="updateFilters"
    />

    <!-- Assignments list -->
    <DashboardStudentAssignmentsList
      :assignments="filteredAssignments"
      :is-loading="isLoading"
      :is-loaded="isLoaded"
      @view-details="viewAssignmentDetails"
    />

    <!-- Assignment detail modal -->
    <DashboardStudentAssignmentsAssignmentDetail
      v-if="selectedAssignment"
      :assignment="selectedAssignment"
      :show="showAssignmentDetail"
      @close="showAssignmentDetail = false"
      @submit="handleAssignmentSubmitted"
    />
  </div>
</template>

<style scoped>
/* Base styles */
/* The component-specific styles will be in their respective components */
</style>
