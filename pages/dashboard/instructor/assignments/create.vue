<!-- pages/dashboard/instructor/assignments/create.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";

// Define page meta for layout
definePageMeta({
  layout: "dashboard-instructor",
});

// Set page title
useHead({
  title: "Create Assignment - Instructor Dashboard",
});

// Router for navigation
const router = useRouter();

// Types
interface Course {
  id: number;
  title: string;
  code: string;
  students: number;
}

interface FileItem {
  id: string;
  name: string;
  type: string;
  size: number;
  progress: number;
}

// State
const isLoading = ref(false);
const isSubmitting = ref(false);
const courses = ref<Course[]>([]);
const selectedCourseId = ref<number | null>(null);
const showDatepicker = ref(false);
const showTimepicker = ref(false);
const showPreview = ref(false);
const currentStep = ref(1);
const totalSteps = 3;
const uploadedFiles = ref<FileItem[]>([]);
const showSuccessMessage = ref(false);

// Assignment form data
const assignmentForm = ref({
  title: "",
  description: "",
  type: "practical", // Default type
  maxPoints: 100,
  dueDate: "",
  dueTime: "23:59",
  instructions: "",
  allowLateSubmissions: true,
  gradingType: "points",
  allowAttachments: true,
  maxFileSize: 10, // In MB
  allowedFileTypes: ["pdf", "doc", "docx", "zip", "jpg", "png"],
  visibleToStudents: true,
  sendNotifications: true,
  rubric: [
    { criteria: "Quality of work", maxPoints: 40, description: "" },
    { criteria: "Completeness", maxPoints: 30, description: "" },
    { criteria: "Following instructions", maxPoints: 30, description: "" },
  ],
});

// Assignment types
const assignmentTypes = [
  { value: "practical", label: "Practical Assignment", icon: "code" },
  { value: "quiz", label: "Quiz", icon: "question-mark-circle" },
  { value: "essay", label: "Essay/Written Assignment", icon: "document-text" },
  { value: "project", label: "Project", icon: "collection" },
];

// Computed properties
const selectedCourse = computed(() => {
  if (!selectedCourseId.value) return null;
  return (
    courses.value.find((course) => course.id === selectedCourseId.value) || null
  );
});

const isFormValid = computed(() => {
  return (
    assignmentForm.value.title.trim() !== "" &&
    selectedCourseId.value !== null &&
    assignmentForm.value.dueDate.trim() !== "" &&
    assignmentForm.value.dueTime.trim() !== "" &&
    assignmentForm.value.maxPoints > 0
  );
});

const formattedDueDate = computed(() => {
  if (!assignmentForm.value.dueDate || !assignmentForm.value.dueTime) return "";

  try {
    const date = new Date(
      `${assignmentForm.value.dueDate}T${assignmentForm.value.dueTime}:00`
    );
    return date.toLocaleString("en-US", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "numeric",
      minute: "numeric",
    });
  } catch (e) {
    return "Invalid date";
  }
});

// Initialize with mock data
const fetchCourses = async () => {
  isLoading.value = true;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data
    courses.value = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        code: "WD101",
        students: 8,
      },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Mastery",
        code: "JS201",
        students: 6,
      },
      { id: 3, title: "UI/UX Design Principles", code: "UI101", students: 4 },
      {
        id: 4,
        title: "React Native for Mobile Development",
        code: "RN202",
        students: 6,
      },
      { id: 5, title: "Data Science with Python", code: "DS101", students: 5 },
    ];
  } catch (error) {
    console.error("Error fetching courses:", error);
  } finally {
    isLoading.value = false;
  }
};

// Format date for input
const formatDateForInput = (date: Date) => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
};

// Set default due date to 2 weeks from now
const setDefaultDueDate = () => {
  const twoWeeksFromNow = new Date();
  twoWeeksFromNow.setDate(twoWeeksFromNow.getDate() + 14);
  assignmentForm.value.dueDate = formatDateForInput(twoWeeksFromNow);
};

// Add a new rubric item
const addRubricItem = () => {
  assignmentForm.value.rubric.push({
    criteria: "",
    maxPoints: 0,
    description: "",
  });
};

// Remove a rubric item
const removeRubricItem = (index: number) => {
  assignmentForm.value.rubric.splice(index, 1);
};

// Move to next step
const nextStep = () => {
  if (currentStep.value < totalSteps) {
    currentStep.value++;
    window.scrollTo(0, 0);
  }
};

// Move to previous step
const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
    window.scrollTo(0, 0);
  }
};

// Toggle file type selection
const toggleFileType = (fileType: string) => {
  const index = assignmentForm.value.allowedFileTypes.indexOf(fileType);
  if (index > -1) {
    assignmentForm.value.allowedFileTypes.splice(index, 1);
  } else {
    assignmentForm.value.allowedFileTypes.push(fileType);
  }
};

// Handle file upload
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (!input.files?.length) return;

  // Process each file
  Array.from(input.files).forEach((file) => {
    // Generate a unique ID
    const fileId = Date.now() + Math.random().toString(36).substring(2, 9);

    // Add to uploaded files
    uploadedFiles.value.push({
      id: fileId,
      name: file.name,
      type: file.type,
      size: file.size,
      progress: 0,
    });

    // Simulate upload progress
    simulateFileUpload(fileId);
  });

  // Reset input
  input.value = "";
};

// Simulate file upload progress
const simulateFileUpload = (fileId: string) => {
  let progress = 0;
  const interval = setInterval(() => {
    progress += Math.random() * 10;

    // Update progress
    const fileIndex = uploadedFiles.value.findIndex(
      (file) => file.id === fileId
    );
    if (fileIndex > -1) {
      uploadedFiles.value[fileIndex].progress = Math.min(progress, 100);

      // If complete, clear interval
      if (progress >= 100) {
        clearInterval(interval);
      }
    } else {
      clearInterval(interval);
    }
  }, 300);
};

// Remove file
const removeFile = (fileId: string) => {
  const fileIndex = uploadedFiles.value.findIndex((file) => file.id === fileId);
  if (fileIndex > -1) {
    uploadedFiles.value.splice(fileIndex, 1);
  }
};

// Format file size
const formatFileSize = (bytes: number) => {
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / 1048576).toFixed(1) + " MB";
};

// Submit the form
const submitAssignment = async () => {
  if (!isFormValid.value) return;

  isSubmitting.value = true;

  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // Show success message
    showSuccessMessage.value = true;

    // Redirect after a short delay
    setTimeout(() => {
      router.push("/dashboard/instructor/assignments");
    }, 2000);
  } catch (error) {
    console.error("Error submitting assignment:", error);
  } finally {
    isSubmitting.value = false;
  }
};

// Get icon based on name
const getIcon = (iconName: string) => {
  switch (iconName) {
    case "code":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>`;
    case "question-mark-circle":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
    case "document-text":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "collection":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Initialize data on component mount
onMounted(() => {
  fetchCourses();
  setDefaultDueDate();
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
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Create Assignment
          </h1>
          <p class="text-gray-600">Create a new assignment for your students</p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/dashboard/instructor/assignments"
            class="inline-flex items-center text-gray-700 bg-white px-4 py-2 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Back to Assignments</span>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Success message -->
    <div
      v-if="showSuccessMessage"
      class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50"
    >
      <div class="bg-white rounded-lg shadow-lg p-6 max-w-md mx-auto">
        <div class="text-center">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4"
          >
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
                d="M5 13l4 4L19 7"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Assignment Created!
          </h3>
          <p class="text-sm text-gray-500 mb-4">
            Your assignment has been created successfully. Redirecting to
            assignments list...
          </p>
          <div class="flex justify-center">
            <div
              class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-indigo-500"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
      <!-- Progress steps -->
      <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Assignment Details
          </h3>
          <div class="flex items-center">
            <span class="text-sm text-gray-500"
              >Step {{ currentStep }} of {{ totalSteps }}</span
            >
          </div>
        </div>
        <div class="mt-4 relative">
          <div class="h-2 bg-gray-200 rounded overflow-hidden">
            <div
              class="h-full bg-indigo-600 transition-all duration-300"
              :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
            ></div>
          </div>
          <div class="flex justify-between mt-2">
            <div
              class="flex flex-col items-center"
              :class="currentStep >= 1 ? 'text-indigo-600' : 'text-gray-500'"
            >
              <div
                class="w-6 h-6 rounded-full flex items-center justify-center text-xs transition-colors duration-300"
                :class="
                  currentStep >= 1
                    ? 'bg-indigo-100 text-indigo-600'
                    : 'bg-gray-100 text-gray-500'
                "
              >
                1
              </div>
              <span class="text-xs mt-1 hidden sm:block">Basic Info</span>
            </div>
            <div
              class="flex flex-col items-center"
              :class="currentStep >= 2 ? 'text-indigo-600' : 'text-gray-500'"
            >
              <div
                class="w-6 h-6 rounded-full flex items-center justify-center text-xs transition-colors duration-300"
                :class="
                  currentStep >= 2
                    ? 'bg-indigo-100 text-indigo-600'
                    : 'bg-gray-100 text-gray-500'
                "
              >
                2
              </div>
              <span class="text-xs mt-1 hidden sm:block">Settings</span>
            </div>
            <div
              class="flex flex-col items-center"
              :class="currentStep >= 3 ? 'text-indigo-600' : 'text-gray-500'"
            >
              <div
                class="w-6 h-6 rounded-full flex items-center justify-center text-xs transition-colors duration-300"
                :class="
                  currentStep >= 3
                    ? 'bg-indigo-100 text-indigo-600'
                    : 'bg-gray-100 text-gray-500'
                "
              >
                3
              </div>
              <span class="text-xs mt-1 hidden sm:block">Review</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 1: Basic Information -->
      <div v-if="currentStep === 1" class="p-6">
        <div class="space-y-6">
          <!-- Title -->
          <div>
            <label
              for="title"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Assignment Title <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              id="title"
              v-model="assignmentForm.title"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Enter a descriptive title"
              required
            />
          </div>

          <!-- Course Selection -->
          <div>
            <label
              for="course"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Course <span class="text-red-500">*</span>
            </label>
            <div v-if="isLoading" class="flex items-center space-x-2">
              <div
                class="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-indigo-500"
              ></div>
              <span class="text-sm text-gray-500">Loading courses...</span>
            </div>
            <div v-else>
              <div class="relative">
                <select
                  id="course"
                  v-model="selectedCourseId"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  required
                >
                  <option value="" disabled selected>Select a course</option>
                  <option
                    v-for="course in courses"
                    :key="course.id"
                    :value="course.id"
                  >
                    {{ course.title }} ({{ course.code }})
                  </option>
                </select>
              </div>
              <p v-if="selectedCourse" class="mt-2 text-sm text-gray-500">
                This course has {{ selectedCourse.students }} enrolled students.
              </p>
            </div>
          </div>

          <!-- Assignment Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3"
              >Assignment Type</label
            >
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
              <div
                v-for="type in assignmentTypes"
                :key="type.value"
                class="relative border rounded-lg p-4 transition-all duration-200 cursor-pointer"
                :class="
                  assignmentForm.type === type.value
                    ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200'
                    : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                "
                @click="assignmentForm.type = type.value"
              >
                <div
                  class="absolute top-2 right-2 w-4 h-4 rounded-full"
                  :class="
                    assignmentForm.type === type.value
                      ? 'bg-indigo-500'
                      : 'border border-gray-300'
                  "
                >
                  <svg
                    v-if="assignmentForm.type === type.value"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-white"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="flex flex-col items-center text-center">
                  <div
                    class="w-12 h-12 rounded-full flex items-center justify-center mb-3"
                    :class="
                      assignmentForm.type === type.value
                        ? 'bg-indigo-100 text-indigo-600'
                        : 'bg-gray-100 text-gray-600'
                    "
                  >
                    <div v-html="getIcon(type.icon)"></div>
                  </div>
                  <div
                    class="font-medium"
                    :class="
                      assignmentForm.type === type.value
                        ? 'text-indigo-700'
                        : 'text-gray-700'
                    "
                  >
                    {{ type.label }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Due Date and Time -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="dueDate"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Due Date <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  type="date"
                  id="dueDate"
                  v-model="assignmentForm.dueDate"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  required
                />
              </div>
            </div>
            <div>
              <label
                for="dueTime"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Due Time <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  type="time"
                  id="dueTime"
                  v-model="assignmentForm.dueTime"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  required
                />
              </div>
            </div>
          </div>

          <!-- Points -->
          <div>
            <label
              for="maxPoints"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Maximum Points <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-1 rounded-md shadow-sm max-w-xs">
              <input
                type="number"
                id="maxPoints"
                v-model.number="assignmentForm.maxPoints"
                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                placeholder="Enter maximum points"
                min="1"
                max="1000"
                required
              />
              <div
                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
              >
                <span class="text-gray-500 sm:text-sm">points</span>
              </div>
            </div>
          </div>

          <!-- Instructions -->
          <div>
            <label
              for="instructions"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Instructions
            </label>
            <textarea
              id="instructions"
              v-model="assignmentForm.instructions"
              rows="4"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Provide detailed instructions for the assignment..."
            ></textarea>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-8 flex justify-end">
          <button
            @click="nextStep"
            :disabled="
              !assignmentForm.title ||
              !selectedCourseId ||
              !assignmentForm.dueDate
            "
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            Next
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 ml-1"
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
        </div>
      </div>

      <!-- Step 2: Settings -->
      <div v-if="currentStep === 2" class="p-6">
        <div class="space-y-6">
          <!-- Settings Sections -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Submission Settings -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="font-medium text-gray-900 mb-4">
                Submission Settings
              </h4>

              <div class="space-y-4">
                <!-- Allow Late Submissions -->
                <div class="flex items-center">
                  <input
                    id="allowLateSubmissions"
                    type="checkbox"
                    v-model="assignmentForm.allowLateSubmissions"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label
                    for="allowLateSubmissions"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    Allow late submissions
                  </label>
                </div>

                <!-- Allow Attachments -->
                <div class="flex items-center">
                  <input
                    id="allowAttachments"
                    type="checkbox"
                    v-model="assignmentForm.allowAttachments"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label
                    for="allowAttachments"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    Allow file attachments
                  </label>
                </div>

                <!-- File Size Limit (if attachments allowed) -->
                <div v-if="assignmentForm.allowAttachments" class="pl-6">
                  <label
                    for="maxFileSize"
                    class="block text-sm text-gray-700 mb-1"
                  >
                    Maximum file size (MB)
                  </label>
                  <input
                    id="maxFileSize"
                    type="number"
                    v-model.number="assignmentForm.maxFileSize"
                    min="1"
                    max="100"
                    class="max-w-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                  />
                </div>

                <!-- Allowed File Types (if attachments allowed) -->
                <div v-if="assignmentForm.allowAttachments" class="pl-6">
                  <label class="block text-sm text-gray-700 mb-2">
                    Allowed file types
                  </label>
                  <div class="flex flex-wrap gap-2">
                    <button
                      v-for="fileType in [
                        'pdf',
                        'doc',
                        'docx',
                        'txt',
                        'jpg',
                        'png',
                        'zip',
                        'ppt',
                        'pptx',
                        'xls',
                        'xlsx',
                      ]"
                      :key="fileType"
                      @click="toggleFileType(fileType)"
                      class="px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200"
                      :class="
                        assignmentForm.allowedFileTypes.includes(fileType)
                          ? 'bg-indigo-100 text-indigo-800'
                          : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                      "
                    >
                      {{ fileType.toUpperCase() }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Visibility Settings -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="font-medium text-gray-900 mb-4">
                Visibility Settings
              </h4>

              <div class="space-y-4">
                <!-- Visible to Students -->
                <div class="flex items-center">
                  <input
                    id="visibleToStudents"
                    type="checkbox"
                    v-model="assignmentForm.visibleToStudents"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label
                    for="visibleToStudents"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    Visible to students
                  </label>
                </div>
                <p class="text-xs text-gray-500">
                  If unchecked, assignment will be saved as draft and not
                  visible to students.
                </p>

                <!-- Send Notifications -->
                <div class="flex items-center">
                  <input
                    id="sendNotifications"
                    type="checkbox"
                    v-model="assignmentForm.sendNotifications"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  <label
                    for="sendNotifications"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    Send notifications to students
                  </label>
                </div>
                <p class="text-xs text-gray-500">
                  Students will receive an email notification about this
                  assignment.
                </p>
              </div>
            </div>
          </div>

          <!-- Grading Settings -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-4">Grading Settings</h4>

            <!-- Grading Type -->
            <div class="mb-4">
              <label class="block text-sm text-gray-700 mb-2">
                Grading Method
              </label>
              <div class="flex space-x-4">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    v-model="assignmentForm.gradingType"
                    value="points"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Points-based</span>
                </label>
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    v-model="assignmentForm.gradingType"
                    value="rubric"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                  />
                  <span class="ml-2 text-sm text-gray-700">Rubric-based</span>
                </label>
              </div>
            </div>

            <!-- Rubric Editor (if rubric grading is selected) -->
            <div v-if="assignmentForm.gradingType === 'rubric'" class="mt-6">
              <div class="flex justify-between items-center mb-3">
                <h5 class="text-sm font-medium text-gray-700">
                  Grading Rubric
                </h5>
                <button
                  @click="addRubricItem"
                  type="button"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Add Criteria
                </button>
              </div>

              <!-- Rubric Table -->
              <div
                class="mt-2 border border-gray-200 rounded-md overflow-hidden"
              >
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th
                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Criteria
                      </th>
                      <th
                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Points
                      </th>
                      <th
                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Description
                      </th>
                      <th
                        class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Action
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="(item, index) in assignmentForm.rubric"
                      :key="index"
                    >
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input
                          type="text"
                          v-model="item.criteria"
                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                          placeholder="Criteria name"
                        />
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input
                          type="number"
                          v-model.number="item.maxPoints"
                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-20 sm:text-sm border-gray-300 rounded-md"
                          min="0"
                          :max="assignmentForm.maxPoints"
                        />
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input
                          type="text"
                          v-model="item.description"
                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                          placeholder="Description"
                        />
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap text-right">
                        <button
                          @click="removeRubricItem(index)"
                          type="button"
                          class="text-red-600 hover:text-red-800"
                          :disabled="assignmentForm.rubric.length <= 1"
                          :class="
                            assignmentForm.rubric.length <= 1
                              ? 'opacity-50 cursor-not-allowed'
                              : ''
                          "
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Rubric Total -->
              <div class="mt-2 text-right text-sm text-gray-500">
                Total Points:
                <span
                  class="font-medium"
                  :class="
                    assignmentForm.rubric.reduce(
                      (sum, item) => sum + item.maxPoints,
                      0
                    ) === assignmentForm.maxPoints
                      ? 'text-green-600'
                      : 'text-red-600'
                  "
                >
                  {{
                    assignmentForm.rubric.reduce(
                      (sum, item) => sum + item.maxPoints,
                      0
                    )
                  }}
                  / {{ assignmentForm.maxPoints }}
                </span>
              </div>
              <div
                v-if="
                  assignmentForm.rubric.reduce(
                    (sum, item) => sum + item.maxPoints,
                    0
                  ) !== assignmentForm.maxPoints
                "
                class="mt-1 text-right text-xs text-red-600"
              >
                The total points in your rubric should equal the maximum points
                ({{ assignmentForm.maxPoints }}).
              </div>
            </div>
          </div>

          <!-- Assignment Materials -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-4">
              Assignment Materials (Optional)
            </h4>

            <p class="text-sm text-gray-600 mb-4">
              Upload any files that students will need to complete this
              assignment.
            </p>

            <!-- File Upload -->
            <div class="flex items-center justify-center w-full">
              <label
                for="fileUpload"
                class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
              >
                <div
                  class="flex flex-col items-center justify-center pt-5 pb-6"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-10 h-10 mb-3 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                    />
                  </svg>
                  <p class="mb-2 text-sm text-gray-500">
                    <span class="font-semibold">Click to upload</span> or drag
                    and drop
                  </p>
                  <p class="text-xs text-gray-500">
                    PDF, DOC, PPT, ZIP (Max. {{ assignmentForm.maxFileSize }}MB)
                  </p>
                </div>
                <input
                  id="fileUpload"
                  type="file"
                  class="hidden"
                  multiple
                  @change="handleFileUpload"
                />
              </label>
            </div>

            <!-- Uploaded Files List -->
            <div v-if="uploadedFiles.length > 0" class="mt-4">
              <h5 class="text-sm font-medium text-gray-700 mb-2">
                Uploaded Files
              </h5>
              <ul
                class="divide-y divide-gray-200 border border-gray-200 rounded-md overflow-hidden"
              >
                <li
                  v-for="file in uploadedFiles"
                  :key="file.id"
                  class="px-4 py-3 flex items-center justify-between text-sm"
                >
                  <div class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                      />
                    </svg>
                    <div>
                      <p class="text-gray-900 truncate">{{ file.name }}</p>
                      <p class="text-gray-500 text-xs">
                        {{ formatFileSize(file.size) }}
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <!-- Progress bar for upload simulation -->
                    <div
                      v-if="file.progress < 100"
                      class="w-20 bg-gray-200 rounded-full h-2 mr-2"
                    >
                      <div
                        class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${file.progress}%` }"
                      ></div>
                    </div>
                    <button
                      @click="removeFile(file.id)"
                      class="text-red-600 hover:text-red-800"
                      :disabled="file.progress < 100"
                      :class="
                        file.progress < 100
                          ? 'opacity-50 cursor-not-allowed'
                          : ''
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
                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-8 flex justify-between">
          <button
            @click="prevStep"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            Back
          </button>
          <button
            @click="nextStep"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Next
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 ml-1"
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
        </div>
      </div>

      <!-- Step 3: Review and Submit -->
      <div v-if="currentStep === 3" class="p-6">
        <!-- Preview toggle -->
        <div class="mb-6 flex justify-end">
          <button
            @click="showPreview = !showPreview"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
              <path
                fill-rule="evenodd"
                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                clip-rule="evenodd"
              />
            </svg>
            {{ showPreview ? "Hide Preview" : "Show Preview" }}
          </button>
        </div>

        <!-- Review Summary -->
        <div v-if="!showPreview" class="space-y-6">
          <h4 class="text-lg font-medium text-gray-900 mb-4">
            Review Your Assignment
          </h4>

          <div class="bg-gray-50 p-4 rounded-lg space-y-4">
            <!-- Basic Info Review -->
            <div>
              <h5 class="text-sm font-semibold text-gray-700 mb-3">
                Basic Information
              </h5>
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Title</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ assignmentForm.title || "Not specified" }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Course</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ selectedCourse?.title || "Not selected" }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    Assignment Type
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 capitalize">
                    {{ assignmentForm.type }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ formattedDueDate || "Not specified" }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    Maximum Points
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ assignmentForm.maxPoints }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    Grading Method
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 capitalize">
                    {{
                      assignmentForm.gradingType === "points"
                        ? "Points-based"
                        : "Rubric-based"
                    }}
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Submission Settings Review -->
            <div>
              <h5 class="text-sm font-semibold text-gray-700 mb-3">
                Submission Settings
              </h5>
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    Late Submissions
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{
                      assignmentForm.allowLateSubmissions
                        ? "Allowed"
                        : "Not allowed"
                    }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    File Attachments
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{
                      assignmentForm.allowAttachments
                        ? "Allowed"
                        : "Not allowed"
                    }}
                  </dd>
                </div>
                <div v-if="assignmentForm.allowAttachments">
                  <dt class="text-sm font-medium text-gray-500">
                    Max File Size
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ assignmentForm.maxFileSize }} MB
                  </dd>
                </div>
                <div v-if="assignmentForm.allowAttachments">
                  <dt class="text-sm font-medium text-gray-500">
                    Allowed File Types
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="fileType in assignmentForm.allowedFileTypes"
                        :key="fileType"
                        class="inline-block px-2 py-0.5 bg-gray-100 text-gray-800 text-xs rounded"
                      >
                        {{ fileType.toUpperCase() }}
                      </span>
                    </div>
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Visibility Settings Review -->
            <div>
              <h5 class="text-sm font-semibold text-gray-700 mb-3">
                Visibility Settings
              </h5>
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    Visible to Students
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{
                      assignmentForm.visibleToStudents ? "Yes" : "No (Draft)"
                    }}
                  </dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">
                    Send Notifications
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ assignmentForm.sendNotifications ? "Yes" : "No" }}
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Rubric Review (if applicable) -->
            <div v-if="assignmentForm.gradingType === 'rubric'">
              <h5 class="text-sm font-semibold text-gray-700 mb-3">
                Grading Rubric
              </h5>
              <div class="border border-gray-200 rounded-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th
                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Criteria
                      </th>
                      <th
                        class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Points
                      </th>
                      <th
                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Description
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="(item, index) in assignmentForm.rubric"
                      :key="index"
                    >
                      <td
                        class="px-3 py-2 whitespace-nowrap text-sm text-gray-900"
                      >
                        {{ item.criteria || "Not specified" }}
                      </td>
                      <td
                        class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right"
                      >
                        {{ item.maxPoints }}
                      </td>
                      <td class="px-3 py-2 text-sm text-gray-900">
                        {{ item.description || "No description" }}
                      </td>
                    </tr>
                    <tr class="bg-gray-50">
                      <td
                        class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900"
                      >
                        Total
                      </td>
                      <td
                        class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 text-right"
                      >
                        {{
                          assignmentForm.rubric.reduce(
                            (sum, item) => sum + item.maxPoints,
                            0
                          )
                        }}
                      </td>
                      <td class="px-3 py-2"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Assignment Materials Review -->
            <div v-if="uploadedFiles.length > 0">
              <h5 class="text-sm font-semibold text-gray-700 mb-3">
                Assignment Materials
              </h5>
              <ul
                class="divide-y divide-gray-200 border border-gray-200 rounded-md overflow-hidden"
              >
                <li
                  v-for="file in uploadedFiles"
                  :key="file.id"
                  class="px-4 py-3 flex items-center text-sm"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                    />
                  </svg>
                  <div>
                    <p class="text-gray-900">{{ file.name }}</p>
                    <p class="text-gray-500 text-xs">
                      {{ formatFileSize(file.size) }}
                    </p>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Instructions Review -->
            <div v-if="assignmentForm.instructions">
              <h5 class="text-sm font-semibold text-gray-700 mb-3">
                Instructions
              </h5>
              <div
                class="bg-white border border-gray-200 rounded-md p-3 text-sm text-gray-900"
              >
                {{ assignmentForm.instructions }}
              </div>
            </div>
          </div>
        </div>

        <!-- Student Preview -->
        <div
          v-if="showPreview"
          class="bg-gray-50 p-6 rounded-lg border border-gray-300"
        >
          <div class="mb-4 flex items-center justify-between">
            <h4 class="text-lg font-medium text-gray-900">Student View</h4>
            <div
              class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full"
            >
              Preview Mode
            </div>
          </div>

          <div class="bg-white shadow rounded-lg overflow-hidden">
            <!-- Assignment Header -->
            <div class="p-4 border-b border-gray-200">
              <div class="flex items-start justify-between">
                <div>
                  <h1 class="text-xl font-bold text-gray-900">
                    {{ assignmentForm.title }}
                  </h1>
                  <p class="text-sm text-gray-500">
                    {{ selectedCourse?.title }}
                  </p>
                </div>
                <div class="text-right">
                  <div class="text-sm font-medium text-gray-900">Due:</div>
                  <div class="text-sm text-gray-500">
                    {{ formattedDueDate }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Assignment Content -->
            <div class="p-4">
              <!-- Type Badge -->
              <div
                class="inline-block px-2 py-1 mb-4 text-xs font-medium rounded-full"
                :class="{
                  'bg-indigo-100 text-indigo-800':
                    assignmentForm.type === 'practical',
                  'bg-blue-100 text-blue-800': assignmentForm.type === 'quiz',
                  'bg-green-100 text-green-800':
                    assignmentForm.type === 'essay',
                  'bg-purple-100 text-purple-800':
                    assignmentForm.type === 'project',
                }"
              >
                {{
                  assignmentForm.type.charAt(0).toUpperCase() +
                  assignmentForm.type.slice(1)
                }}
              </div>

              <!-- Points -->
              <div class="mb-4">
                <span class="text-sm font-medium text-gray-500">Points:</span>
                <span class="ml-1 text-sm text-gray-900">{{
                  assignmentForm.maxPoints
                }}</span>
              </div>

              <!-- Instructions -->
              <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-2">
                  Instructions:
                </h3>
                <div
                  class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md border border-gray-200"
                >
                  {{
                    assignmentForm.instructions || "No instructions provided."
                  }}
                </div>
              </div>

              <!-- Attachments -->
              <div v-if="uploadedFiles.length > 0" class="mb-6">
                <h3 class="text-sm font-medium text-gray-900 mb-2">
                  Assignment Materials:
                </h3>
                <ul
                  class="divide-y divide-gray-200 border border-gray-200 rounded-md overflow-hidden"
                >
                  <li
                    v-for="file in uploadedFiles"
                    :key="file.id"
                    class="px-4 py-3 flex items-center bg-white hover:bg-gray-50 text-sm"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400 mr-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                      />
                    </svg>
                    <div>
                      <p class="text-indigo-600 hover:text-indigo-800">
                        {{ file.name }}
                      </p>
                      <p class="text-gray-500 text-xs">
                        {{ formatFileSize(file.size) }}
                      </p>
                    </div>
                  </li>
                </ul>
              </div>

              <!-- Submission Section -->
              <div class="mt-8">
                <h3 class="text-sm font-medium text-gray-900 mb-4">
                  Your Submission:
                </h3>

                <div v-if="assignmentForm.allowAttachments" class="mb-4">
                  <div class="flex items-center justify-center w-full">
                    <label
                      class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                      <div
                        class="flex flex-col items-center justify-center pt-5 pb-6"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="w-8 h-8 mb-3 text-gray-400"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                          />
                        </svg>
                        <p class="mb-2 text-sm text-gray-500">
                          <span class="font-semibold">Click to upload</span> or
                          drag and drop
                        </p>
                        <p class="text-xs text-gray-500">
                          {{
                            assignmentForm.allowedFileTypes
                              .map((type) => type.toUpperCase())
                              .join(", ")
                          }}
                          (Max. {{ assignmentForm.maxFileSize }}MB)
                        </p>
                      </div>
                    </label>
                  </div>
                </div>

                <div class="mb-4">
                  <label
                    for="comment"
                    class="block text-sm font-medium text-gray-700 mb-1"
                  >
                    Comments (optional):
                  </label>
                  <textarea
                    id="comment"
                    rows="3"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="Add any comments about your submission here..."
                  ></textarea>
                </div>

                <div class="text-right">
                  <button
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Submit Assignment
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation and Submit Buttons -->
        <div class="mt-8 flex justify-between">
          <button
            @click="prevStep"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            Back
          </button>
          <div>
            <button
              @click="submitAssignment"
              :disabled="!isFormValid || isSubmitting"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
            >
              <div v-if="isSubmitting" class="flex items-center">
                <svg
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
                Creating Assignment...
              </div>
              <span v-else>Create Assignment</span>
            </button>
            <button
              v-if="!isSubmitting"
              @click="
                assignmentForm.visibleToStudents = false;
                submitAssignment();
              "
              :disabled="!isFormValid"
              class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
            >
              Save as Draft
            </button>
          </div>
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

/* Animated elements */
.fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom height for small textareas */
.h-32 {
  height: 8rem;
}

/* Custom focus and disabled states */
.focus\:ring-indigo-500:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(99, 102, 241, var(--tw-ring-opacity));
}

.disabled\:opacity-50:disabled {
  opacity: 0.5;
}
</style>
