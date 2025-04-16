<!-- pages/dashboard/instructor/courses/[id].vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

// Types
interface Module {
  id: number;
  title: string;
  description: string;
  position: number;
  isPublished: boolean;
  isExpanded?: boolean;
  lessons: Lesson[];
}

interface Lesson {
  id: number;
  title: string;
  type: "video" | "quiz" | "assignment" | "text" | "downloadable";
  duration: number; // in minutes
  isPublished: boolean;
  status: "draft" | "complete" | "in_progress";
  position: number;
}

interface Course {
  id: number;
  title: string;
  category: string;
  subcategory?: string;
  thumbnail: string;
  status: "draft" | "published" | "archived" | "under_review";
  students: number;
  rating: number;
  price: number;
  revenue: number;
  completion: number;
  lastUpdated: string;
  isPublished: boolean;
  description: string;
  shortDescription: string;
  prerequisites: string[];
  objectives: string[];
  level: "beginner" | "intermediate" | "advanced" | "all_levels";
  language: string;
  modules: Module[];
  totalDuration: number;
  totalLessons: number;
}

// Set the page layout to dashboard-instructor
definePageMeta({
  layout: "dashboard-instructor",
});

// Page meta
useHead({
  title: "Course Details - Instructor Dashboard",
});

// Route and router
const route = useRoute();
const router = useRouter();
const courseId = computed(() => Number(route.params.id) || 7);

// State for course data and UI controls
const course = ref<Course | null>(null);
const isLoading = ref(true);
const activeTab = ref("content");
const editMode = ref(false);
const savingChanges = ref(false);
const showPublishModal = ref(false);
const deleteConfirmText = ref("");
const showDeleteModal = ref(false);
const uploadingContent = ref(false);
const uploadProgress = ref(0);
const previewMode = ref(false);

// State for form data during edit
const formData = ref({
  title: "",
  shortDescription: "",
  description: "",
  price: 0,
  category: "",
  subcategory: "",
  level: "beginner",
  prerequisites: [] as string[],
  objectives: [] as string[],
});

// Fetch course data
const fetchCourseData = async () => {
  try {
    isLoading.value = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock data specific to course ID 7 (Cloud Architecture Fundamentals)
    const mockCourse: Course = {
      id: 7,
      title: "Cloud Architecture Fundamentals",
      category: "Cloud Computing",
      subcategory: "Architecture",
      thumbnail: "/api/placeholder/400/225",
      status: "draft",
      students: 0,
      rating: 0,
      price: 99.99,
      revenue: 0,
      completion: 30,
      lastUpdated: "2025-04-05T13:40:00Z",
      isPublished: false,
      description:
        "This comprehensive course covers the fundamentals of cloud architecture with hands-on projects and real-world case studies. Students will learn how to design scalable, resilient and cost-effective cloud solutions using best practices and industry-standard patterns.",
      shortDescription:
        "Learn to design and implement scalable cloud architecture solutions",
      prerequisites: [
        "Basic understanding of networking concepts",
        "Familiarity with at least one cloud provider (AWS, Azure, or GCP)",
        "Basic programming knowledge",
      ],
      objectives: [
        "Design and implement scalable cloud architectures",
        "Apply security best practices in cloud environments",
        "Optimize costs for cloud deployments",
        "Implement proper disaster recovery strategies",
        "Use infrastructure as code to deploy cloud resources",
      ],
      level: "intermediate",
      language: "English",
      totalDuration: 420, // 7 hours total (planned)
      totalLessons: 35, // Total planned lessons
      modules: [
        {
          id: 1,
          title: "Introduction to Cloud Architecture",
          description:
            "Foundational concepts and principles in cloud architecture",
          position: 1,
          isPublished: false,
          isExpanded: true,
          lessons: [
            {
              id: 101,
              title: "What is Cloud Architecture?",
              type: "video",
              duration: 15,
              isPublished: false,
              status: "complete",
              position: 1,
            },
            {
              id: 102,
              title: "Cloud Service Models: IaaS, PaaS, SaaS",
              type: "video",
              duration: 20,
              isPublished: false,
              status: "complete",
              position: 2,
            },
            {
              id: 103,
              title: "Cloud Deployment Models",
              type: "video",
              duration: 18,
              isPublished: false,
              status: "complete",
              position: 3,
            },
            {
              id: 104,
              title: "Key Architecture Considerations",
              type: "video",
              duration: 25,
              isPublished: false,
              status: "draft",
              position: 4,
            },
            {
              id: 105,
              title: "Module 1 Quiz",
              type: "quiz",
              duration: 15,
              isPublished: false,
              status: "draft",
              position: 5,
            },
          ],
        },
        {
          id: 2,
          title: "Designing for Scalability and High Availability",
          description:
            "Learn strategies for building resilient and scalable cloud systems",
          position: 2,
          isPublished: false,
          isExpanded: false,
          lessons: [
            {
              id: 201,
              title: "Scalability Patterns",
              type: "video",
              duration: 22,
              isPublished: false,
              status: "in_progress",
              position: 1,
            },
            {
              id: 202,
              title: "Load Balancing Strategies",
              type: "video",
              duration: 18,
              isPublished: false,
              status: "draft",
              position: 2,
            },
            {
              id: 203,
              title: "Designing for High Availability",
              type: "video",
              duration: 25,
              isPublished: false,
              status: "draft",
              position: 3,
            },
            {
              id: 204,
              title: "Fault Tolerance and Disaster Recovery",
              type: "video",
              duration: 28,
              isPublished: false,
              status: "draft",
              position: 4,
            },
            {
              id: 205,
              title: "Hands-on Lab: Building a Scalable Architecture",
              type: "assignment",
              duration: 45,
              isPublished: false,
              status: "draft",
              position: 5,
            },
          ],
        },
        {
          id: 3,
          title: "Security in Cloud Architecture",
          description: "Essential security practices for cloud environments",
          position: 3,
          isPublished: false,
          isExpanded: false,
          lessons: [
            {
              id: 301,
              title: "Cloud Security Fundamentals",
              type: "video",
              duration: 20,
              isPublished: false,
              status: "draft",
              position: 1,
            },
            {
              id: 302,
              title: "Identity and Access Management",
              type: "video",
              duration: 25,
              isPublished: false,
              status: "draft",
              position: 2,
            },
            {
              id: 303,
              title: "Network Security in the Cloud",
              type: "video",
              duration: 22,
              isPublished: false,
              status: "draft",
              position: 3,
            },
          ],
        },
        {
          id: 4,
          title: "Cost Optimization and Management",
          description: "Strategies for optimizing costs in cloud deployments",
          position: 4,
          isPublished: false,
          isExpanded: false,
          lessons: [],
        },
        {
          id: 5,
          title: "Infrastructure as Code",
          description: "Automating infrastructure deployment using code",
          position: 5,
          isPublished: false,
          isExpanded: false,
          lessons: [],
        },
      ],
    };

    course.value = mockCourse;

    // Initialize form data with course values
    formData.value = {
      title: mockCourse.title,
      shortDescription: mockCourse.shortDescription,
      description: mockCourse.description,
      price: mockCourse.price,
      category: mockCourse.category,
      subcategory: mockCourse.subcategory || "",
      level: mockCourse.level,
      prerequisites: [...mockCourse.prerequisites],
      objectives: [...mockCourse.objectives],
    };
  } catch (error) {
    console.error("Error fetching course data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Toggle the expanded state of a module
const toggleModuleExpanded = (moduleId: number) => {
  if (!course.value) return;

  course.value.modules = course.value.modules.map((module) => {
    if (module.id === moduleId) {
      return { ...module, isExpanded: !module.isExpanded };
    }
    return module;
  });
};

// Calculate the completion status of the course
const calculateCompletionPercentage = computed(() => {
  if (!course.value) return 0;

  const totalLessons = course.value.modules.reduce(
    (acc, module) => acc + module.lessons.length,
    0
  );
  if (totalLessons === 0) return 0;

  const completedLessons = course.value.modules.reduce((acc, module) => {
    return (
      acc +
      module.lessons.filter((lesson) => lesson.status === "complete").length
    );
  }, 0);

  return Math.round((completedLessons / totalLessons) * 100);
});

// Check if the course is ready to publish
const isReadyToPublish = computed(() => {
  if (!course.value) return false;

  // Course must have at least one module with lessons
  const hasContent = course.value.modules.some(
    (module) => module.lessons.length > 0
  );

  // Basic details must be filled out
  const hasDetails =
    course.value.title.trim() !== "" &&
    course.value.description.trim() !== "" &&
    course.value.price > 0;

  // At least 80% of planned content should be ready
  const isContentReady = calculateCompletionPercentage.value >= 80;

  return hasContent && hasDetails && isContentReady;
});

// Format time in hours and minutes
const formatDuration = (minutes: number) => {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;

  if (hours === 0) {
    return `${mins}m`;
  } else if (mins === 0) {
    return `${hours}h`;
  } else {
    return `${hours}h ${mins}m`;
  }
};

// Format date
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

// Save course changes
const saveChanges = async () => {
  if (!course.value) return;

  try {
    savingChanges.value = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1200));

    // Update course with form data
    course.value = {
      ...course.value,
      title: formData.value.title,
      shortDescription: formData.value.shortDescription,
      description: formData.value.description,
      price: formData.value.price,
      category: formData.value.category,
      subcategory: formData.value.subcategory,
      level: formData.value.level as
        | "beginner"
        | "intermediate"
        | "advanced"
        | "all_levels",
      prerequisites: [...formData.value.prerequisites],
      objectives: [...formData.value.objectives],
      lastUpdated: new Date().toISOString(),
    };

    // Exit edit mode
    editMode.value = false;
  } catch (error) {
    console.error("Error saving course data:", error);
  } finally {
    savingChanges.value = false;
  }
};

// Toggle the edit mode
const toggleEditMode = () => {
  if (editMode.value) {
    // Discard changes and reset form data
    formData.value = {
      title: course.value?.title || "",
      shortDescription: course.value?.shortDescription || "",
      description: course.value?.description || "",
      price: course.value?.price || 0,
      category: course.value?.category || "",
      subcategory: course.value?.subcategory || "",
      level: course.value?.level || "beginner",
      prerequisites: [...(course.value?.prerequisites || [])],
      objectives: [...(course.value?.objectives || [])],
    };
  }

  editMode.value = !editMode.value;
};

// Publish course
const publishCourse = async () => {
  if (!course.value) return;

  try {
    savingChanges.value = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // Update course status
    course.value = {
      ...course.value,
      status: "under_review",
      lastUpdated: new Date().toISOString(),
    };

    // Close the modal
    showPublishModal.value = false;
  } catch (error) {
    console.error("Error publishing course:", error);
  } finally {
    savingChanges.value = false;
  }
};

// Add prerequisite
const addPrerequisite = () => {
  formData.value.prerequisites.push("");
};

// Remove prerequisite
const removePrerequisite = (index: number) => {
  formData.value.prerequisites.splice(index, 1);
};

// Add objective
const addObjective = () => {
  formData.value.objectives.push("");
};

// Remove objective
const removeObjective = (index: number) => {
  formData.value.objectives.splice(index, 1);
};

// Simulate uploading content
const simulateContentUpload = async () => {
  if (!course.value) return;

  uploadingContent.value = true;
  uploadProgress.value = 0;

  // Simulate upload progress
  const interval = setInterval(() => {
    uploadProgress.value += Math.floor(Math.random() * 10) + 1;

    if (uploadProgress.value >= 100) {
      uploadProgress.value = 100;
      clearInterval(interval);

      setTimeout(() => {
        uploadingContent.value = false;
        uploadProgress.value = 0;

        // Add a new lesson to the first module
        if (course.value && course.value.modules.length > 0) {
          const firstModule = course.value.modules[0];
          const newLessonId =
            Math.max(...firstModule.lessons.map((l) => l.id), 0) + 1;

          firstModule.lessons.push({
            id: newLessonId,
            title: "New Uploaded Content",
            type: "video",
            duration: 22,
            isPublished: false,
            status: "draft",
            position: firstModule.lessons.length + 1,
          });
        }
      }, 1000);
    }
  }, 200);
};

// Calculate total lessons and completed lessons
const totalLessonsCount = computed(() => {
  if (!course.value) return 0;

  return course.value.modules.reduce(
    (acc, module) => acc + module.lessons.length,
    0
  );
});

const completedLessonsCount = computed(() => {
  if (!course.value) return 0;

  return course.value.modules.reduce((acc, module) => {
    return (
      acc +
      module.lessons.filter((lesson) => lesson.status === "complete").length
    );
  }, 0);
});

// Calculate total course duration
const totalCourseDuration = computed(() => {
  if (!course.value) return 0;

  return course.value.modules.reduce((acc, module) => {
    return (
      acc +
      module.lessons.reduce(
        (lessonAcc, lesson) => lessonAcc + lesson.duration,
        0
      )
    );
  }, 0);
});

// Calculated course status for display
const courseStatusDisplay = computed(() => {
  if (!course.value) return {};

  switch (course.value.status) {
    case "draft":
      return { text: "Draft", class: "bg-yellow-100 text-yellow-800" };
    case "published":
      return { text: "Published", class: "bg-green-100 text-green-800" };
    case "under_review":
      return { text: "Under Review", class: "bg-blue-100 text-blue-800" };
    case "archived":
      return { text: "Archived", class: "bg-gray-100 text-gray-800" };
    default:
      return { text: "Unknown", class: "bg-gray-100 text-gray-800" };
  }
});

// Load data when component mounts
onMounted(() => {
  fetchCourseData();
});
</script>

<template>
  <div>
    <!-- Loading skeleton -->
    <div v-if="isLoading" class="animate-pulse">
      <div class="bg-white rounded-lg shadow p-6 mb-6 border border-gray-200">
        <div class="h-8 bg-gray-200 rounded-md w-1/3 mb-4"></div>
        <div class="h-4 bg-gray-200 rounded-md w-1/2 mb-6"></div>
        <div class="flex space-x-4">
          <div class="h-8 bg-gray-200 rounded-md w-24"></div>
          <div class="h-8 bg-gray-200 rounded-md w-24"></div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="bg-white rounded-lg shadow p-6 border border-gray-200 md:col-span-2"
        >
          <div class="h-6 bg-gray-200 rounded-md w-1/4 mb-4"></div>
          <div class="h-4 bg-gray-200 rounded-md w-full mb-2"></div>
          <div class="h-4 bg-gray-200 rounded-md w-full mb-2"></div>
          <div class="h-4 bg-gray-200 rounded-md w-3/4"></div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="h-6 bg-gray-200 rounded-md w-1/2 mb-4"></div>
          <div class="h-32 bg-gray-200 rounded-md mb-4"></div>
          <div class="h-10 bg-gray-200 rounded-md w-full"></div>
        </div>
      </div>
    </div>

    <!-- Actual content -->
    <div v-else>
      <!-- Course Header -->
      <div class="bg-white rounded-lg shadow p-6 mb-6 border border-gray-200">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h1 v-if="!editMode" class="text-2xl font-bold text-gray-900 mb-1">
              {{ course?.title }}
            </h1>
            <input
              v-else
              v-model="formData.title"
              type="text"
              class="text-2xl font-bold text-gray-900 border border-gray-300 rounded-md px-3 py-1 mb-1 w-full md:w-auto"
              placeholder="Course Title"
            />

            <div class="flex items-center flex-wrap gap-2 text-sm">
              <span
                class="capitalize px-2 py-0.5 rounded-full text-xs font-medium"
                :class="courseStatusDisplay.class"
              >
                {{ courseStatusDisplay.text }}
              </span>
              <span class="text-gray-500">
                Last updated: {{ formatDate(course?.lastUpdated || "") }}
              </span>
              <span class="text-gray-500">
                Completion: {{ calculateCompletionPercentage }}%
              </span>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <button
              v-if="!editMode"
              @click="toggleEditMode"
              class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                />
              </svg>
              Edit Details
            </button>

            <template v-if="editMode">
              <button
                @click="saveChanges"
                class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                :disabled="savingChanges"
              >
                <span v-if="!savingChanges">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Save Changes
                </span>
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
              </button>

              <button
                @click="toggleEditMode"
                class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                :disabled="savingChanges"
              >
                Cancel
              </button>
            </template>

            <template v-else>
              <button
                @click="showPublishModal = true"
                class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                :disabled="!isReadyToPublish"
                :class="{ 'opacity-50 cursor-not-allowed': !isReadyToPublish }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                Submit for Review
              </button>

              <button
                @click="previewMode = !previewMode"
                class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors"
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
                {{ previewMode ? "Exit Preview" : "Preview Course" }}
              </button>
            </template>
          </div>
        </div>
      </div>

      <!-- Course tabs + content -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main content area -->
        <div class="md:col-span-2 space-y-6">
          <!-- Tabs navigation -->
          <div
            class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
          >
            <div class="flex border-b border-gray-200 overflow-x-auto">
              <button
                v-for="tab in ['content', 'curriculum', 'settings', 'pricing']"
                :key="tab"
                @click="activeTab = tab"
                class="px-4 py-3 text-sm font-medium transition-colors whitespace-nowrap"
                :class="
                  activeTab === tab
                    ? 'border-b-2 border-indigo-500 text-indigo-600'
                    : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                "
              >
                {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
              </button>
            </div>

            <!-- Tab content -->
            <div class="p-6">
              <!-- Content Tab -->
              <div v-if="activeTab === 'content'" class="space-y-6">
                <div
                  v-if="previewMode"
                  class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4"
                >
                  <div class="flex items-start">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-yellow-400 mt-0.5 mr-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <div>
                      <h3 class="font-medium text-yellow-800">Preview Mode</h3>
                      <p class="text-yellow-700 text-sm">
                        You are viewing the course as students will see it. Some
                        instructor features are hidden in this mode.
                      </p>
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <h2 class="text-xl font-bold text-gray-900">
                    Course Overview
                  </h2>

                  <div v-if="!editMode" class="prose max-w-none">
                    <p>{{ course?.description }}</p>
                  </div>

                  <div v-else class="space-y-4">
                    <div>
                      <label
                        for="shortDescription"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Short Description</label
                      >
                      <input
                        id="shortDescription"
                        v-model="formData.shortDescription"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Brief description that appears in course cards"
                      />
                    </div>

                    <div>
                      <label
                        for="description"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >Full Description</label
                      >
                      <textarea
                        id="description"
                        v-model="formData.description"
                        rows="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Detailed course description"
                      ></textarea>
                    </div>
                  </div>

                  <div
                    v-if="!editMode"
                    class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6"
                  >
                    <div>
                      <h3 class="text-lg font-semibold mb-2">
                        What You'll Learn
                      </h3>
                      <ul class="space-y-2">
                        <li
                          v-for="(objective, index) in course?.objectives"
                          :key="`objective-${index}`"
                          class="flex items-start"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-green-500 mr-2 flex-shrink-0"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <span>{{ objective }}</span>
                        </li>
                      </ul>
                    </div>

                    <div>
                      <h3 class="text-lg font-semibold mb-2">Prerequisites</h3>
                      <ul class="space-y-2">
                        <li
                          v-for="(prerequisite, index) in course?.prerequisites"
                          :key="`prerequisite-${index}`"
                          class="flex items-start"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-indigo-500 mr-2 flex-shrink-0"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <span>{{ prerequisite }}</span>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <div
                    v-if="editMode"
                    class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6"
                  >
                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >What You'll Learn</label
                      >
                      <div class="space-y-2">
                        <div
                          v-for="(objective, index) in formData.objectives"
                          :key="`objective-edit-${index}`"
                          class="flex items-center"
                        >
                          <input
                            v-model="formData.objectives[index]"
                            type="text"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            :placeholder="`Objective ${index + 1}`"
                          />
                          <button
                            @click="removeObjective(index)"
                            class="ml-2 p-1 text-gray-500 hover:text-red-500 focus:outline-none"
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
                        <button
                          @click="addObjective"
                          class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700"
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
                          Add Learning Objective
                        </button>
                      </div>
                    </div>

                    <div>
                      <label
                        class="block text-sm font-medium text-gray-700 mb-2"
                        >Prerequisites</label
                      >
                      <div class="space-y-2">
                        <div
                          v-for="(
                            prerequisite, index
                          ) in formData.prerequisites"
                          :key="`prerequisite-edit-${index}`"
                          class="flex items-center"
                        >
                          <input
                            v-model="formData.prerequisites[index]"
                            type="text"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            :placeholder="`Prerequisite ${index + 1}`"
                          />
                          <button
                            @click="removePrerequisite(index)"
                            class="ml-2 p-1 text-gray-500 hover:text-red-500 focus:outline-none"
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
                        <button
                          @click="addPrerequisite"
                          class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700"
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
                          Add Prerequisite
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                  <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Course Curriculum
                  </h2>

                  <div
                    v-if="course?.modules.length === 0"
                    class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-12 w-12 text-gray-400 mx-auto mb-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                      />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                      No content yet
                    </h3>
                    <p class="text-gray-600 mb-4">
                      Add modules and lessons to your course curriculum.
                    </p>
                    <button
                      class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                      Add Your First Module
                    </button>
                  </div>

                  <div v-else class="space-y-4">
                    <div
                      v-for="module in course?.modules"
                      :key="`module-${module.id}`"
                      class="border border-gray-200 rounded-lg overflow-hidden"
                    >
                      <!-- Module header -->
                      <div
                        @click="toggleModuleExpanded(module.id)"
                        class="flex items-center justify-between p-4 bg-gray-50 border-b border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors"
                      >
                        <div class="flex items-center">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2 text-gray-500 transform transition-transform"
                            :class="module.isExpanded ? 'rotate-90' : ''"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          <h3 class="font-medium">{{ module.title }}</h3>
                        </div>

                        <div class="flex items-center text-sm text-gray-500">
                          <span>{{ module.lessons.length }} lessons</span>
                          <span class="mx-2">•</span>
                          <span>
                            {{
                              formatDuration(
                                module.lessons.reduce(
                                  (acc, lesson) => acc + lesson.duration,
                                  0
                                )
                              )
                            }}
                          </span>
                        </div>
                      </div>

                      <!-- Module lessons -->
                      <div
                        v-if="module.isExpanded"
                        class="divide-y divide-gray-200"
                      >
                        <div
                          v-for="lesson in module.lessons"
                          :key="`lesson-${lesson.id}`"
                          class="flex items-center p-4 hover:bg-gray-50 transition-colors"
                        >
                          <!-- Lesson icon based on type -->
                          <div class="mr-3 flex-shrink-0">
                            <svg
                              v-if="lesson.type === 'video'"
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5 text-blue-500"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                              />
                            </svg>
                            <svg
                              v-else-if="lesson.type === 'quiz'"
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5 text-purple-500"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                              <path
                                fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            <svg
                              v-else-if="lesson.type === 'assignment'"
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5 text-amber-500"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                              />
                            </svg>
                            <svg
                              v-else-if="lesson.type === 'text'"
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5 text-green-500"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                clip-rule="evenodd"
                              />
                            </svg>
                            <svg
                              v-else
                              xmlns="http://www.w3.org/2000/svg"
                              class="h-5 w-5 text-gray-500"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                            >
                              <path
                                fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                              />
                            </svg>
                          </div>

                          <!-- Lesson info -->
                          <div class="flex-grow min-w-0">
                            <div class="flex items-center">
                              <h4 class="font-medium text-gray-900 truncate">
                                {{ lesson.title }}
                              </h4>
                              <span
                                v-if="!previewMode"
                                class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                                :class="{
                                  'bg-green-100 text-green-800':
                                    lesson.status === 'complete',
                                  'bg-yellow-100 text-yellow-800':
                                    lesson.status === 'in_progress',
                                  'bg-gray-100 text-gray-800':
                                    lesson.status === 'draft',
                                }"
                              >
                                {{ lesson.status.replace("_", " ") }}
                              </span>
                            </div>
                            <div class="text-sm text-gray-500">
                              <span class="capitalize">{{ lesson.type }}</span>
                              <span class="mx-1">•</span>
                              <span>{{ formatDuration(lesson.duration) }}</span>
                            </div>
                          </div>

                          <!-- Lesson actions -->
                          <div
                            v-if="!previewMode"
                            class="flex items-center ml-4 space-x-2"
                          >
                            <button
                              class="p-1 text-gray-500 hover:text-gray-700 transition-colors"
                            >
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                              >
                                <path
                                  d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                />
                              </svg>
                            </button>
                            <button
                              class="p-1 text-gray-500 hover:text-red-500 transition-colors"
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
                          </div>
                        </div>

                        <!-- Add lesson button -->
                        <button
                          class="w-full py-2 px-3 border border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors flex items-center justify-center"
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
                          Add Lesson
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Settings Tab -->
              <div v-else-if="activeTab === 'settings'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                      Basic Information
                    </h2>

                    <div class="space-y-4">
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Course Title</label
                        >
                        <input
                          type="text"
                          v-model="formData.title"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                      </div>

                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Category</label
                        >
                        <select
                          v-model="formData.category"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                          <option value="Web Development">
                            Web Development
                          </option>
                          <option value="Mobile Development">
                            Mobile Development
                          </option>
                          <option value="Cloud Computing">
                            Cloud Computing
                          </option>
                          <option value="Data Science">Data Science</option>
                          <option value="Design">Design</option>
                          <option value="Business">Business</option>
                        </select>
                      </div>

                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Subcategory</label
                        >
                        <input
                          type="text"
                          v-model="formData.subcategory"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                      </div>

                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Difficulty Level</label
                        >
                        <select
                          v-model="formData.level"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                          <option value="beginner">Beginner</option>
                          <option value="intermediate">Intermediate</option>
                          <option value="advanced">Advanced</option>
                          <option value="all_levels">All Levels</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                      Media & Appearance
                    </h2>

                    <div class="space-y-4">
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Course Thumbnail</label
                        >
                        <div class="flex items-center space-x-4">
                          <img
                            :src="course?.thumbnail"
                            class="h-24 w-32 object-cover rounded-lg border border-gray-200"
                          />
                          <div>
                            <button
                              class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
                            >
                              Change Thumbnail
                            </button>
                            <p class="text-xs text-gray-500 mt-1">
                              Recommended size: 1280x720 pixels (16:9 ratio)
                            </p>
                          </div>
                        </div>
                      </div>

                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Promotional Video</label
                        >
                        <button
                          class="flex items-center px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 mr-1"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                              clip-rule="evenodd"
                            />
                          </svg>
                          Add Promotional Video
                        </button>
                        <p class="text-xs text-gray-500 mt-1">
                          A short video (2-3 minutes) showcasing your course
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                  <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Additional Settings
                  </h2>

                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <h3 class="font-medium text-gray-900">
                          Course Language
                        </h3>
                        <p class="text-sm text-gray-500">
                          Set the primary language for your course content
                        </p>
                      </div>
                      <select
                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      >
                        <option>English</option>
                        <option>Spanish</option>
                        <option>French</option>
                        <option>German</option>
                        <option>Chinese</option>
                        <option>Japanese</option>
                      </select>
                    </div>

                    <div class="flex items-center justify-between">
                      <div>
                        <h3 class="font-medium text-gray-900">
                          Course Visibility
                        </h3>
                        <p class="text-sm text-gray-500">
                          Control who can see your course in the catalog
                        </p>
                      </div>
                      <select
                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      >
                        <option>Public</option>
                        <option>Private</option>
                        <option>Password Protected</option>
                      </select>
                    </div>

                    <div class="flex items-start justify-between">
                      <div>
                        <h3 class="font-medium text-gray-900">
                          Student Discussions
                        </h3>
                        <p class="text-sm text-gray-500">
                          Allow students to participate in course discussions
                        </p>
                      </div>
                      <div class="relative inline-block w-10 mr-2 align-middle">
                        <input
                          type="checkbox"
                          id="toggle-discussions"
                          class="sr-only"
                          checked
                        />
                        <label
                          for="toggle-discussions"
                          class="block h-6 w-10 rounded-full bg-indigo-600 cursor-pointer"
                        ></label>
                      </div>
                    </div>

                    <div class="flex items-start justify-between">
                      <div>
                        <h3 class="font-medium text-gray-900">
                          Certificate of Completion
                        </h3>
                        <p class="text-sm text-gray-500">
                          Issue certificates to students who complete the course
                        </p>
                      </div>
                      <div class="relative inline-block w-10 mr-2 align-middle">
                        <input
                          type="checkbox"
                          id="toggle-certificates"
                          class="sr-only"
                          checked
                        />
                        <label
                          for="toggle-certificates"
                          class="block h-6 w-10 rounded-full bg-indigo-600 cursor-pointer"
                        ></label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="border-t border-gray-200 pt-6 text-right">
                  <button
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                  >
                    Save Settings
                  </button>
                </div>
              </div>

              <!-- Pricing Tab -->
              <div v-else-if="activeTab === 'pricing'" class="space-y-6">
                <div
                  class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6"
                >
                  <div class="flex items-start">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6 text-yellow-500 mr-3 flex-shrink-0"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <div>
                      <h3 class="font-medium text-yellow-800 mb-1">
                        Pricing Information
                      </h3>
                      <p class="text-yellow-700 text-sm">
                        Set the price for your course. You can also create
                        coupon codes and promotional offers. The course will not
                        be visible to students until it is published.
                      </p>
                    </div>
                  </div>
                </div>

                <div class="space-y-6">
                  <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                      Course Pricing
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Base Price ($)</label
                        >
                        <div class="relative">
                          <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                          >
                            <span class="text-gray-500 sm:text-sm">$</span>
                          </div>
                          <input
                            type="number"
                            v-model="formData.price"
                            class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                          />
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                          The standard price for your course. You earn 75% of
                          the course price after platform fees.
                        </p>
                      </div>

                      <div>
                        <label
                          class="block text-sm font-medium text-gray-700 mb-1"
                          >Tier</label
                        >
                        <select
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                          <option>Standard</option>
                          <option>Premium</option>
                          <option>Basic</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">
                          The tier affects course placement in search results
                          and recommendations.
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                      Promotions
                    </h2>

                    <div class="space-y-4">
                      <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200"
                      >
                        <div>
                          <h3 class="font-medium text-gray-900">
                            Course Coupons
                          </h3>
                          <p class="text-sm text-gray-500">
                            Create discount coupons for your course
                          </p>
                        </div>
                        <button
                          class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
                        >
                          Create Coupon
                        </button>
                      </div>

                      <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200"
                      >
                        <div>
                          <h3 class="font-medium text-gray-900">
                            Promotional Price
                          </h3>
                          <p class="text-sm text-gray-500">
                            Set a temporary discounted price for your course
                          </p>
                        </div>
                        <button
                          class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
                        >
                          Set Promo Price
                        </button>
                      </div>

                      <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200"
                      >
                        <div>
                          <h3 class="font-medium text-gray-900">
                            Bulk Enrollment
                          </h3>
                          <p class="text-sm text-gray-500">
                            Special pricing for organizations enrolling multiple
                            students
                          </p>
                        </div>
                        <button
                          class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
                        >
                          Enable Bulk Pricing
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                      Subscription
                    </h2>

                    <div class="flex items-start">
                      <div>
                        <div class="flex h-5 items-center">
                          <input
                            id="subscription"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                          />
                        </div>
                      </div>
                      <div class="ml-3 text-sm">
                        <label
                          for="subscription"
                          class="font-medium text-gray-700"
                          >Include in subscription plans</label
                        >
                        <p class="text-gray-500">
                          Make this course available as part of subscription
                          plans. You'll earn revenue based on student
                          engagement.
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="border-t border-gray-200 pt-6 text-right">
                    <button
                      class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                      Save Pricing
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar with course stats and actions -->
        <div class="space-y-6">
          <!-- Course status card -->
          <div
            class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
          >
            <div class="p-4 bg-indigo-50 border-b border-gray-200">
              <h2 class="font-bold text-gray-900">Course Status</h2>
            </div>

            <div class="p-4">
              <div class="flex justify-between items-center mb-4">
                <div class="text-gray-500 text-sm">Completion</div>
                <div class="font-semibold text-sm">
                  {{ calculateCompletionPercentage }}%
                </div>
              </div>

              <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                <div
                  class="bg-indigo-600 h-2.5 rounded-full"
                  :style="{ width: `${calculateCompletionPercentage}%` }"
                ></div>
              </div>

              <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-500">Current Status</span>
                  <span class="font-semibold capitalize">{{
                    course?.status.replace("_", " ")
                  }}</span>
                </div>

                <div class="flex justify-between">
                  <span class="text-gray-500">Last Updated</span>
                  <span class="font-semibold">{{
                    formatDate(course?.lastUpdated || "")
                  }}</span>
                </div>

                <div class="flex justify-between">
                  <span class="text-gray-500">Modules</span>
                  <span class="font-semibold">{{
                    course?.modules.length
                  }}</span>
                </div>

                <div class="flex justify-between">
                  <span class="text-gray-500">Lessons</span>
                  <span class="font-semibold"
                    >{{ completedLessonsCount }}/{{ totalLessonsCount }}</span
                  >
                </div>

                <div class="flex justify-between">
                  <span class="text-gray-500">Duration</span>
                  <span class="font-semibold">{{
                    formatDuration(totalCourseDuration)
                  }}</span>
                </div>
              </div>

              <div class="mt-6" v-if="!isReadyToPublish">
                <div class="text-sm font-medium text-amber-800 mb-2">
                  Publishing Requirements
                </div>
                <ul class="text-sm space-y-2">
                  <li class="flex items-start">
                    <svg
                      v-if="totalLessonsCount > 0"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-green-500 mr-1.5 flex-shrink-0"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-amber-500 mr-1.5 flex-shrink-0"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>Add at least one module with lessons</span>
                  </li>
                  <li class="flex items-start">
                    <svg
                      v-if="
                        course?.title &&
                        course?.description &&
                        course?.price > 0
                      "
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-green-500 mr-1.5 flex-shrink-0"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-amber-500 mr-1.5 flex-shrink-0"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>Complete all basic course details</span>
                  </li>
                  <li class="flex items-start">
                    <svg
                      v-if="calculateCompletionPercentage >= 80"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-green-500 mr-1.5 flex-shrink-0"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-amber-500 mr-1.5 flex-shrink-0"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span>Complete at least 80% of planned content</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Course actions -->
          <div
            class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
          >
            <div class="p-4 bg-indigo-50 border-b border-gray-200">
              <h2 class="font-bold text-gray-900">Course Actions</h2>
            </div>

            <div class="p-4 space-y-3">
              <a
                href="#"
                class="block w-full py-2 px-3 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 inline-block mr-1"
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
                Preview as Student
              </a>

              <button
                class="w-full py-2 px-3 border border-gray-300 text-gray-700 text-center rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 inline-block mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"
                  />
                  <path
                    d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"
                  />
                </svg>
                Share Link
              </button>

              <button
                class="w-full py-2 px-3 border border-gray-300 text-gray-700 text-center rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 inline-block mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z"
                  />
                  <path
                    d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z"
                  />
                </svg>
                Duplicate Course
              </button>

              <button
                class="w-full py-2 px-3 border border-red-300 text-red-700 text-center rounded-lg hover:bg-red-50 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 inline-block mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
                Delete Course
              </button>
            </div>
          </div>

          <!-- Quick Help -->
          <div
            class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
          >
            <div class="p-4 bg-indigo-50 border-b border-gray-200">
              <h2 class="font-bold text-gray-900">Quick Help</h2>
            </div>

            <div class="p-4">
              <ul class="space-y-3 text-sm">
                <li>
                  <a
                    href="#"
                    class="flex items-center text-indigo-600 hover:text-indigo-800 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Course Creation Guide
                  </a>
                </li>
                <li>
                  <a
                    href="#"
                    class="flex items-center text-indigo-600 hover:text-indigo-800 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                      />
                    </svg>
                    Video Recording Tips
                  </a>
                </li>
                <li>
                  <a
                    href="#"
                    class="flex items-center text-indigo-600 hover:text-indigo-800 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Instructor Community
                  </a>
                </li>
                <li>
                  <a
                    href="#"
                    class="flex items-center text-indigo-600 hover:text-indigo-800 transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 mr-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    Contact Support
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Publish confirmation modal -->
    <div
      v-if="showPublishModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
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
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Submit for Review
          </h3>
          <p class="text-sm text-gray-600">
            Your course will be submitted for review by our team. This typically
            takes 1-3 business days. Once approved, your course will be
            published and available to students.
          </p>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
          <h4 class="font-medium text-gray-900 mb-2">Course Checklist</h4>
          <ul class="space-y-2 text-sm">
            <li class="flex items-center text-green-700">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2 text-green-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
              Course content is complete ({{ calculateCompletionPercentage }}%)
            </li>
            <li class="flex items-center text-green-700">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2 text-green-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
              Course pricing is set (${{ course?.price }})
            </li>
            <li class="flex items-center text-green-700">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2 text-green-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
              Course details are filled out
            </li>
          </ul>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showPublishModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="publishCourse"
            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            :disabled="savingChanges"
          >
            <span v-if="!savingChanges">Submit for Review</span>
            <svg
              v-else
              class="animate-spin h-5 w-5 mx-auto"
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
          </button>
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

/* Custom toggle switch styling */
input[type="checkbox"] + label {
  position: relative;
}

input[type="checkbox"] + label:before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 2.5rem;
  height: 1.5rem;
  border-radius: 1.5rem;
  transition: all 0.2s ease-in-out;
}

input[type="checkbox"] + label:after {
  content: "";
  position: absolute;
  top: 0.25rem;
  left: 0.25rem;
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
  background-color: white;
  transition: all 0.2s ease-in-out;
}

input[type="checkbox"]:checked + label:before {
  background-color: rgb(79, 70, 229);
}

input[type="checkbox"]:checked + label:after {
  transform: translateX(1rem);
}

/* Tabs animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
