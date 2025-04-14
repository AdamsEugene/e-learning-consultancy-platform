<!-- components/dashboard/DashboardAssignmentDetail.vue -->
<script setup lang="ts">
import { ref, watch, computed } from "vue";
import type { Assignment } from "~/types/assignments";
import {
  formatDate,
  formatTime,
  getStatusColor,
  getFileIcon,
} from "~/types/assignments";

// Props
const props = defineProps<{
  assignment: Assignment;
  show: boolean;
}>();

// Emits
const emit = defineEmits<{
  (e: "close"): void;
  (e: "submit", assignmentId: number, files: File[]): void;
}>();

// File upload states
const uploadedFiles = ref<File[]>([]);
const isUploading = ref(false);
const uploadProgress = ref(0);
const uploadError = ref("");

// Modal animation state
const isVisible = ref(false);

// Watch for show prop changes to animate modal
watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      isVisible.value = true;
    } else {
      isVisible.value = false;
    }
  },
  { immediate: true }
);

// Reset state when modal closes
watch(
  () => props.show,
  (newVal) => {
    if (!newVal) {
      // Reset after animation completes
      setTimeout(() => {
        uploadedFiles.value = [];
        isUploading.value = false;
        uploadProgress.value = 0;
        uploadError.value = "";
      }, 300);
    }
  }
);

// Close modal
const closeModal = () => {
  emit("close");
};

// Handle file upload
const handleFileUpload = (event: Event) => {
  const fileInput = event.target as HTMLInputElement;
  if (fileInput.files && fileInput.files.length > 0) {
    uploadError.value = "";

    // Add files to our list
    for (let i = 0; i < fileInput.files.length; i++) {
      const file = fileInput.files[i];

      // Basic validation - 50MB max size
      if (file.size > 50 * 1024 * 1024) {
        uploadError.value = `File "${file.name}" exceeds the 50MB size limit.`;
        continue;
      }

      uploadedFiles.value.push(file);
    }
  }
};

// Remove uploaded file
const removeFile = (index: number) => {
  uploadedFiles.value.splice(index, 1);
};

// Submit assignment
const submitAssignment = () => {
  if (uploadedFiles.value.length === 0) {
    uploadError.value = "Please upload at least one file.";
    return;
  }

  isUploading.value = true;
  uploadError.value = "";

  // Simulate upload progress
  let progress = 0;
  const progressInterval = setInterval(() => {
    progress += 5;
    uploadProgress.value = progress;

    if (progress >= 100) {
      clearInterval(progressInterval);

      // Complete the submission
      emit("submit", props.assignment.id, uploadedFiles.value);

      // Reset state
      isUploading.value = false;
      uploadProgress.value = 0;
      uploadedFiles.value = [];
    }
  }, 100);
};

// Calculate submission status
const submissionStatus = computed(() => {
  if (!props.assignment) return null;

  switch (props.assignment.status) {
    case "pending":
    case "late":
      return {
        canSubmit: true,
        label:
          props.assignment.status === "late"
            ? "Late Submission"
            : "Ready for Submission",
        color:
          props.assignment.status === "late"
            ? "text-red-600"
            : "text-green-600",
      };
    case "submitted":
      return {
        canSubmit: false,
        label: "Submitted - Awaiting Grade",
        color: "text-blue-600",
      };
    case "graded":
      return {
        canSubmit: false,
        label: "Graded",
        color: "text-green-600",
      };
    default:
      return {
        canSubmit: false,
        label: "Unknown Status",
        color: "text-gray-600",
      };
  }
});

// Calculate grade percentage
const gradePercentage = computed(() => {
  if (!props.assignment?.grade || !props.assignment?.maxGrade) return 0;
  return Math.round((props.assignment.grade / props.assignment.maxGrade) * 100);
});

// Format file size
const formatFileSize = (bytes: number): string => {
  if (bytes < 1024) return bytes + " B";
  else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
  else return (bytes / 1048576).toFixed(1) + " MB";
};
</script>

<template>
  <transition
    enter-active-class="transition-opacity duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-hidden"
      @click="closeModal"
    >
      <transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="isVisible"
          class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
          @click.stop
        >
          <!-- Modal header -->
          <div
            class="sticky top-0 z-10 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 rounded-t-xl flex items-center justify-between"
          >
            <div>
              <h3 class="text-xl font-bold">{{ assignment.title }}</h3>
              <div class="text-sm opacity-90">{{ assignment.courseName }}</div>
            </div>
            <button
              @click="closeModal"
              class="p-1 rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors"
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
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <!-- Modal content -->
          <div class="px-6 py-4">
            <!-- Assignment details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <div
                class="p-4 bg-gray-50 rounded-lg flex flex-col items-center justify-center border border-gray-100 shadow-sm"
              >
                <div class="text-sm font-medium text-gray-500 mb-2">Status</div>
                <div
                  :class="`inline-flex text-sm leading-5 font-semibold rounded-full px-3 py-1 ${getStatusColor(
                    assignment.status
                  )}`"
                >
                  {{
                    assignment.status.charAt(0).toUpperCase() +
                    assignment.status.slice(1)
                  }}
                </div>
              </div>

              <div
                class="p-4 bg-gray-50 rounded-lg flex flex-col items-center justify-center border border-gray-100 shadow-sm"
              >
                <div class="text-sm font-medium text-gray-500 mb-2">
                  Due Date
                </div>
                <div class="font-medium text-gray-900">
                  {{ formatDate(assignment.dueDate) }}
                </div>
                <div class="text-sm text-gray-600">
                  {{ formatTime(assignment.dueDate) }}
                </div>
              </div>

              <div
                class="p-4 bg-gray-50 rounded-lg flex flex-col items-center justify-center border border-gray-100 shadow-sm"
              >
                <div class="text-sm font-medium text-gray-500 mb-2">
                  Assignment Type
                </div>
                <div class="font-medium text-gray-900 capitalize">
                  {{ assignment.type }}
                </div>
              </div>
            </div>

            <!-- Assignment description -->
            <div
              class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm mb-6"
            >
              <h4
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 text-indigo-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"
                  />
                </svg>
                Assignment Description
              </h4>
              <div class="text-gray-700 prose">
                {{ assignment.description || "No description provided." }}
              </div>
            </div>

            <!-- File attachments -->
            <div
              v-if="assignment.attachments && assignment.attachments.length > 0"
              class="mb-6"
            >
              <h4
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 text-indigo-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                    clip-rule="evenodd"
                  />
                </svg>
                Assignment Materials
              </h4>
              <div
                class="bg-gray-50 rounded-xl border border-gray-200 p-3 shadow-sm"
              >
                <ul class="divide-y divide-gray-200">
                  <li
                    v-for="(file, index) in assignment.attachments"
                    :key="index"
                    class="flex items-center py-3 px-3 hover:bg-gray-100 rounded-lg transition-colors group"
                  >
                    <!-- File icon based on type -->
                    <div class="flex-shrink-0 mr-3 text-gray-400">
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
                          :d="getFileIcon(file.type)"
                        />
                      </svg>
                    </div>

                    <!-- File name and download link -->
                    <div class="flex-grow min-w-0">
                      <div class="text-sm font-medium text-gray-900 truncate">
                        {{ file.name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        {{ file.type.toUpperCase() }} file
                      </div>
                    </div>

                    <!-- Download button -->
                    <a
                      :href="file.url"
                      class="ml-4 flex-shrink-0 p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-full transition-colors"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Submitted files section -->
            <div
              v-if="
                assignment.submittedFiles &&
                assignment.submittedFiles.length > 0
              "
              class="mb-6"
            >
              <h4
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center"
              >
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
                Your Submission
              </h4>
              <div
                class="bg-green-50 rounded-xl border border-green-200 p-3 shadow-sm"
              >
                <ul class="divide-y divide-green-100">
                  <li
                    v-for="(file, index) in assignment.submittedFiles"
                    :key="index"
                    class="flex items-center py-3 px-3 hover:bg-green-100/50 rounded-lg transition-colors"
                  >
                    <!-- File icon -->
                    <div class="flex-shrink-0 mr-3 text-green-500">
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
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                      </svg>
                    </div>

                    <!-- File name and upload date -->
                    <div class="flex-grow min-w-0">
                      <div class="text-sm font-medium text-gray-900 truncate">
                        {{ file.name }}
                      </div>
                      <div class="text-xs text-gray-600">
                        Submitted on {{ formatDate(file.uploadDate) }} at
                        {{ formatTime(file.uploadDate) }}
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Grade and feedback section -->
            <div v-if="assignment.grade !== undefined" class="mb-6">
              <h4
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 text-blue-500"
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
                Grade & Feedback
              </h4>
              <div
                class="bg-blue-50 rounded-xl border border-blue-200 p-6 shadow-sm"
              >
                <!-- Grade display -->
                <div
                  class="flex flex-col md:flex-row items-center justify-between mb-6"
                >
                  <div class="flex items-center">
                    <div class="text-5xl font-bold text-blue-600">
                      {{ assignment.grade }}
                    </div>
                    <div class="ml-2 text-xl text-gray-600">
                      / {{ assignment.maxGrade }}
                    </div>
                  </div>

                  <!-- Grade percentage -->
                  <div
                    class="mt-4 md:mt-0 bg-white py-1 px-4 rounded-full shadow-sm border border-blue-100"
                  >
                    <div
                      class="text-xl font-bold"
                      :class="[
                        gradePercentage >= 90
                          ? 'text-green-600'
                          : gradePercentage >= 70
                          ? 'text-blue-600'
                          : gradePercentage >= 60
                          ? 'text-yellow-600'
                          : 'text-red-600',
                      ]"
                    >
                      {{ gradePercentage }}%
                    </div>
                  </div>
                </div>

                <!-- Progress bar -->
                <div
                  class="w-full h-3 bg-gray-200 rounded-full mb-6 overflow-hidden"
                >
                  <div
                    class="h-full rounded-full animate-progress-fill"
                    :class="[
                      gradePercentage >= 90
                        ? 'bg-green-500'
                        : gradePercentage >= 70
                        ? 'bg-blue-500'
                        : gradePercentage >= 60
                        ? 'bg-yellow-500'
                        : 'bg-red-500',
                    ]"
                    :style="{ width: `${gradePercentage}%` }"
                  ></div>
                </div>

                <!-- Feedback section -->
                <div v-if="assignment.feedback" class="mt-4">
                  <div class="text-sm font-medium text-gray-700 mb-2">
                    Instructor Feedback:
                  </div>
                  <div
                    class="p-4 bg-white rounded-lg border border-blue-100 text-gray-700 shadow-sm"
                  >
                    {{ assignment.feedback }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Upload section (if pending assignment) -->
            <div v-if="submissionStatus?.canSubmit" class="mb-6">
              <h4
                class="text-lg font-semibold text-gray-900 mb-4 flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2 text-indigo-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                Submit Your Work
                <span
                  :class="[submissionStatus.color, 'ml-2 text-sm font-normal']"
                >
                  ({{ submissionStatus.label }})
                </span>
              </h4>
              <div
                class="bg-indigo-50 rounded-xl border border-indigo-200 p-6 shadow-sm"
              >
                <!-- File upload area -->
                <div
                  class="border-2 border-dashed rounded-xl p-6 text-center transition-all duration-300 relative overflow-hidden"
                  :class="[
                    isUploading
                      ? 'border-indigo-300 bg-indigo-100/50 cursor-not-allowed'
                      : uploadedFiles.length > 0
                      ? 'border-indigo-300 bg-indigo-50 hover:bg-indigo-100/50'
                      : 'border-gray-300 hover:border-indigo-300 hover:bg-indigo-50',
                  ]"
                  @dragover.prevent
                  @drop.prevent="
                    (e) => {
                      if (!isUploading) {
                        handleFileUpload({
                          target: { files: e.dataTransfer.files },
                        });
                      }
                    }
                  "
                >
                  <input
                    type="file"
                    id="file-upload"
                    @change="handleFileUpload"
                    multiple
                    class="hidden"
                    :disabled="isUploading"
                  />
                  <label
                    for="file-upload"
                    class="cursor-pointer inline-flex flex-col items-center justify-center"
                    :class="{ 'opacity-70 cursor-not-allowed': isUploading }"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-16 w-16 text-indigo-400 mb-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                      />
                    </svg>

                    <div v-if="uploadedFiles.length === 0">
                      <div class="text-base font-medium text-gray-700 mb-1">
                        {{
                          isUploading
                            ? "Uploading files..."
                            : "Click to upload or drag and drop"
                        }}
                      </div>
                      <div class="text-sm text-gray-500">
                        Upload any files related to your assignment
                      </div>
                    </div>
                    <div v-else>
                      <div class="text-base font-medium text-gray-700 mb-1">
                        {{
                          isUploading
                            ? "Uploading files..."
                            : `${uploadedFiles.length} file(s) selected`
                        }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{
                          isUploading
                            ? "Please wait"
                            : "Click to add more files"
                        }}
                      </div>
                    </div>
                  </label>

                  <!-- Upload progress overlay -->
                  <div
                    v-if="isUploading"
                    class="absolute inset-0 bg-indigo-50 bg-opacity-70 flex flex-col items-center justify-center"
                  >
                    <div class="text-center mb-4">
                      <div class="text-indigo-600 font-medium">
                        Uploading: {{ uploadProgress }}%
                      </div>
                    </div>
                    <div
                      class="w-3/4 h-2 bg-indigo-100 rounded-full overflow-hidden"
                    >
                      <div
                        class="h-full bg-indigo-600 rounded-full transition-all duration-300"
                        :style="`width: ${uploadProgress}%`"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Error message -->
                <div
                  v-if="uploadError"
                  class="mt-3 text-red-600 text-sm bg-red-50 py-2 px-3 rounded-lg border border-red-200 flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2 flex-shrink-0"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  {{ uploadError }}
                </div>

                <!-- Uploaded files list -->
                <div v-if="uploadedFiles.length > 0" class="mt-4">
                  <div class="text-sm font-medium text-gray-700 mb-2">
                    Selected files:
                  </div>
                  <ul
                    class="divide-y divide-gray-200 bg-white rounded-lg border border-gray-200"
                  >
                    <li
                      v-for="(file, index) in uploadedFiles"
                      :key="index"
                      class="flex items-center py-3 px-4 group hover:bg-gray-50 transition-colors"
                    >
                      <!-- File icon based on extension -->
                      <div class="flex-shrink-0 mr-3 text-indigo-500">
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
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                          />
                        </svg>
                      </div>

                      <!-- File name and size -->
                      <div class="flex-grow min-w-0">
                        <div class="text-sm font-medium text-gray-900 truncate">
                          {{ file.name }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ formatFileSize(file.size) }}
                        </div>
                      </div>

                      <!-- Remove button -->
                      <button
                        @click="removeFile(index)"
                        class="ml-4 text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100"
                        :disabled="isUploading"
                        :class="{
                          'opacity-50 cursor-not-allowed': isUploading,
                        }"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </li>
                  </ul>
                </div>

                <!-- Submit button -->
                <div class="mt-6 flex justify-end">
                  <button
                    @click="submitAssignment"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition-colors relative overflow-hidden"
                    :disabled="uploadedFiles.length === 0 || isUploading"
                    :class="{
                      'opacity-60 cursor-not-allowed':
                        uploadedFiles.length === 0 || isUploading,
                      'submit-btn': !(
                        uploadedFiles.length === 0 || isUploading
                      ),
                    }"
                  >
                    <svg
                      v-if="isUploading"
                      class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
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
                    {{ isUploading ? "Uploading..." : "Submit Assignment" }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<style scoped>
/* Progress bar animation */
.animate-progress-fill {
  animation: progressFill 1.5s ease-out forwards;
  width: 0 !important;
}

@keyframes progressFill {
  from {
    width: 0;
  }
  to {
    width: v-bind("`${gradePercentage}%`");
  }
}

/* Submit button animation */
.submit-btn {
  position: relative;
  overflow: hidden;
}

.submit-btn:after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  border-radius: 100%;
  transform: scale(1, 1) translate(-50%);
  transform-origin: 50% 50%;
}

.submit-btn:focus:not(:active)::after {
  animation: ripple 1s ease-out;
}

@keyframes ripple {
  0% {
    transform: scale(0, 0);
    opacity: 0.5;
  }
  20% {
    transform: scale(25, 25);
    opacity: 0.3;
  }
  100% {
    opacity: 0;
    transform: scale(40, 40);
  }
}

/* Loading animation */
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
