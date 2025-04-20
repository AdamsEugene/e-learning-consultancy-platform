<!-- components/courses/AssignmentComponent.vue -->
<script setup lang="ts">
import { marked } from "marked"; // Import the markdown parser

// TypeScript interfaces
interface Props {
  title: string;
  content?: string;
  dueDate?: string; // Optional due date for the assignment
}

interface FormData {
  file: File | null;
  fileName: string;
  comments: string;
  challengesFaced: string;
  solutionsImplemented: string;
  additionalResources: boolean;
}

interface SubmissionStatus {
  submitted: boolean;
  uploading: boolean;
  error: boolean;
  completed: boolean;
  progress: number; // Upload progress percentage
}

// Props with defaults
withDefaults(defineProps<Props>(), {
  content: "",
  dueDate: "",
});

// Emits with type definitions
const emit = defineEmits<{
  (e: "complete"): void;
  (e: "progress", step: number): void;
}>();

// State management
const activeStep = ref<number>(1);
const assignmentDescription = ref<string>(`
# Assignment: Building Your First Webpage

In this assignment, you will apply what you've learned to build a simple webpage from scratch using HTML. 

## Instructions

1. Create an HTML file named "index.html".
2. Use proper HTML document structure (doctype, html, head, body tags).
3. Add a page title and appropriate metadata in the head section.
4. In the body, include:
   - A main heading (h1) with your name
   - A subheading (h2) describing the purpose of your webpage
   - At least one paragraph of text
   - An ordered or unordered list with at least 3 items
   - An image (you can use a placeholder image)
   - A hyperlink to another website
   - A simple table with at least 2 rows and 2 columns
5. Use appropriate semantic HTML elements where possible.

## Submission Guidelines

- Submit your completed HTML file through the form below.
- Include any challenges you faced and how you resolved them in the comments section.
- Ensure your code is well-indented and follows HTML best practices.
`);

// File input reference
const fileInput = ref<HTMLInputElement | null>(null) as {
  value: HTMLInputElement | null;
};

// Form data with proper typing
const formData = ref<FormData>({
  file: null,
  fileName: "",
  comments: "",
  challengesFaced: "",
  solutionsImplemented: "",
  additionalResources: false,
});

// Submission status with proper typing
const submissionStatus = ref<SubmissionStatus>({
  submitted: false,
  uploading: false,
  error: false,
  completed: false,
  progress: 0,
});

// Animation states
const isTransitioning = ref<boolean>(false);
const isFormFocused = ref<{ [key: string]: boolean }>({
  comments: false,
  challengesFaced: false,
  solutionsImplemented: false,
});
// const fileHover = ref<boolean>(false);
const buttonHover = ref<boolean>(false);
const confetti = ref<boolean>(false);

// Computed properties
const isFormValid = computed<boolean>(() => {
  return formData.value.file !== null && formData.value.comments.trim() !== "";
});

// const isLastStep = computed<boolean>(() => {
//   return activeStep.value === 3;
// });

// For drag and drop functionality
const isDragging = ref<boolean>(false);

// Methods
const handleFileChange = (event: Event): void => {
  const target = event.target as HTMLInputElement;
  if (!target.files) return;

  const file = target.files[0];
  if (file) {
    formData.value.file = file;
    formData.value.fileName = file.name;

    // Animate the file upload success
    setTimeout(() => {
      const uploadSuccess = document.querySelector(".upload-success");
      if (uploadSuccess) {
        uploadSuccess.classList.add("animate-bounce-once");
      }
    }, 100);
  }
};

const handleDragOver = (event: DragEvent): void => {
  event.preventDefault();
  isDragging.value = true;
};

const handleDragLeave = (event: DragEvent): void => {
  event.preventDefault();
  isDragging.value = false;
};

const handleDrop = (event: DragEvent): void => {
  event.preventDefault();
  isDragging.value = false;

  if (!event.dataTransfer) return;

  const file = event.dataTransfer.files[0];
  if (file && (file.name.endsWith(".html") || file.name.endsWith(".htm"))) {
    formData.value.file = file;
    formData.value.fileName = file.name;

    // Animate the file upload success
    setTimeout(() => {
      const uploadSuccess = document.querySelector(".upload-success");
      if (uploadSuccess) {
        uploadSuccess.classList.add("animate-bounce-once");
      }
    }, 100);
  }
};

const nextStep = (): void => {
  if (activeStep.value < 3) {
    isTransitioning.value = true;

    setTimeout(() => {
      activeStep.value++;
      isTransitioning.value = false;

      // Emit progress update
      emit("progress", (activeStep.value / 3) * 100);
    }, 300);
  }
};

const prevStep = (): void => {
  if (activeStep.value > 1) {
    isTransitioning.value = true;

    setTimeout(() => {
      activeStep.value--;
      isTransitioning.value = false;

      // Emit progress update
      emit("progress", (activeStep.value / 3) * 100);
    }, 300);
  }
};

const submitAssignment = (): void => {
  if (!isFormValid.value) return;

  // Simulate file upload with progress
  submissionStatus.value.uploading = true;
  submissionStatus.value.error = false;
  submissionStatus.value.progress = 0;

  // Fake progress updates
  const progressInterval = setInterval(() => {
    if (submissionStatus.value.progress < 100) {
      submissionStatus.value.progress += Math.floor(Math.random() * 15) + 5;
      if (submissionStatus.value.progress > 100) {
        submissionStatus.value.progress = 100;
      }
    } else {
      clearInterval(progressInterval);
    }
  }, 300);

  setTimeout(() => {
    clearInterval(progressInterval);
    submissionStatus.value.uploading = false;
    submissionStatus.value.submitted = true;
    submissionStatus.value.progress = 100;

    // Simulate processing time
    setTimeout(() => {
      submissionStatus.value.completed = true;

      // Show confetti animation
      confetti.value = true;
      setTimeout(() => {
        confetti.value = false;
      }, 3000);
    }, 1500);
  }, 3000);
};

const completeAssignment = (): void => {
  emit("complete");
};

// const resetForm = (): void => {
//   formData.value = {
//     file: null,
//     fileName: "",
//     comments: "",
//     challengesFaced: "",
//     solutionsImplemented: "",
//     additionalResources: false,
//   };
//   submissionStatus.value = {
//     submitted: false,
//     uploading: false,
//     error: false,
//     completed: false,
//     progress: 0,
//   };
//   activeStep.value = 1;
// };

// Focus handling for form fields
const setFocus = (field: string, focused: boolean): void => {
  isFormFocused.value[field] = focused;
};

// Emit initial progress on mount
onMounted(() => {
  emit("progress", (activeStep.value / 3) * 100);
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="p-6 assignment-container">
    <div class="flex justify-between items-start mb-4">
      <div>
        <h2 class="text-xl font-bold mb-1">Assignment: {{ title }}</h2>
        <p v-if="content" class="text-gray-600">{{ content }}</p>
      </div>

      <!-- Due date badge if provided -->
      <div
        v-if="dueDate"
        class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full flex items-center text-sm font-medium"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4 mr-1"
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
        Due: {{ dueDate }}
      </div>
    </div>

    <!-- Assignment steps progress -->
    <div class="mb-8">
      <div class="flex justify-between mb-2">
        <div
          v-for="step in 3"
          :key="step"
          class="flex flex-col items-center relative flex-1"
        >
          <div
            class="h-10 w-10 rounded-full flex items-center justify-center font-medium text-white mb-2 z-10 transition-all duration-500 transform"
            :class="[
              step < activeStep
                ? 'bg-green-500 scale-110'
                : step === activeStep
                ? 'bg-indigo-600 scale-110 shadow-md'
                : 'bg-gray-300',
            ]"
          >
            <svg
              v-if="step < activeStep"
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 animate-scale-in"
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
            <span v-else>{{ step }}</span>
          </div>
          <div
            class="text-sm text-center font-medium transition-colors duration-300"
            :class="step === activeStep ? 'text-indigo-600' : 'text-gray-500'"
          >
            {{
              step === 1
                ? "Instructions"
                : step === 2
                ? "Submit Work"
                : "Complete"
            }}
          </div>
          <!-- Connector line with animation -->
          <div
            v-if="step < 3"
            class="absolute top-5 left-1/2 w-full h-[2px] transition-all duration-700"
            :class="[
              step < activeStep
                ? 'bg-green-500'
                : step === activeStep
                ? 'bg-gradient-to-r from-green-500 via-indigo-400 to-gray-300'
                : 'bg-gray-300',
            ]"
          />
        </div>
      </div>
    </div>

    <!-- Content for each step with transitions -->
    <div
      class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300"
    >
      <!-- Step 1: Instructions -->
      <transition name="fade-slide" mode="out-in">
        <div v-if="activeStep === 1" key="step-1" class="step-content">
          <div class="prose prose-indigo max-w-none">
            <div class="markdown-body" v-html="marked(assignmentDescription)" />
          </div>

          <div class="mt-8 flex justify-between">
            <div />
            <UiButton
              variant="primary"
              state="default"
              size="lg"
              @click="nextStep"
              @mouseenter="buttonHover = true"
              @mouseleave="buttonHover = false"
            >
              Continue to Submission
              <template #suffix>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transition-transform duration-300"
                  :class="{ 'translate-x-1': buttonHover }"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M14 5l7 7m0 0l-7 7m7-7H3"
                  />
                </svg>
              </template>
            </UiButton>
          </div>
        </div>

        <!-- Step 2: Submission form -->
        <div v-else-if="activeStep === 2" key="step-2" class="step-content">
          <h3 class="text-lg font-bold mb-6 text-gray-800 border-b pb-3">
            Submit Your Assignment
          </h3>

          <!-- File upload with drag and drop -->
          <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-medium"
              >HTML File <span class="text-red-500">*</span></label
            >
            <div
              class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-all duration-300"
              :class="{
                'border-indigo-300 bg-indigo-50': formData.file || isDragging,
                'hover:bg-gray-50 hover:border-gray-400':
                  !formData.file && !isDragging,
                'border-indigo-400 bg-indigo-100': isDragging,
              }"
              @click="fileInput.value?.click()"
              @dragover="handleDragOver"
              @dragleave="handleDragLeave"
              @drop="handleDrop"
            >
              <UiInput
                ref="fileInput"
                v-model="formData.fileName"
                type="file"
                accept=".html,.htm"
                class="hidden"
                @change="handleFileChange"
              />

              <div v-if="!formData.file" class="py-4">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-12 w-12 mx-auto text-gray-400 mb-3"
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
                <p class="text-gray-600 mb-1">
                  Click to upload or drag and drop
                </p>
                <p class="text-gray-500 text-sm">
                  HTML files only (.html, .htm)
                </p>
              </div>

              <div
                v-else
                class="flex items-center justify-center upload-success py-4"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-8 w-8 text-indigo-500 mr-3"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <div class="text-left">
                  <p class="text-indigo-600 font-medium">
                    File uploaded successfully
                  </p>
                  <p class="text-gray-600 text-sm">{{ formData.fileName }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Comments section -->
          <div class="mb-6">
            <UiTextarea
              v-model="formData.comments"
              label="Comments"
              required
              :rows="4"
              placeholder="Share your thoughts about the assignment and the process you followed..."
              @focus="setFocus('comments', true)"
              @blur="setFocus('comments', false)"
            />
          </div>

          <!-- Challenges faced -->
          <div class="mb-6">
            <UiTextarea
              v-model="formData.challengesFaced"
              label="Challenges Faced"
              optional
              :rows="3"
              placeholder="Describe any challenges you encountered while completing the assignment..."
              @focus="setFocus('challengesFaced', true)"
              @blur="setFocus('challengesFaced', false)"
            />
          </div>

          <!-- Solutions implemented -->
          <div class="mb-6">
            <UiTextarea
              v-model="formData.solutionsImplemented"
              label="Solutions Implemented"
              optional
              :rows="3"
              placeholder="Explain how you solved the challenges mentioned above..."
              @focus="setFocus('solutionsImplemented', true)"
              @blur="setFocus('solutionsImplemented', false)"
            />
          </div>

          <!-- Additional resources checkbox with custom styling -->
          <div class="mb-8">
            <label class="flex items-center group cursor-pointer">
              <div class="relative flex items-center">
                <UiCheckbox
                  v-model="formData.additionalResources"
                  type="checkbox"
                  class="sr-only"
                />
                <div
                  class="h-5 w-5 rounded border transition-all duration-300 flex items-center justify-center"
                  :class="
                    formData.additionalResources
                      ? 'bg-indigo-600 border-transparent'
                      : 'border-gray-400 group-hover:border-indigo-500'
                  "
                >
                  <svg
                    v-if="formData.additionalResources"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 text-white animate-scale-in"
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
              </div>
              <span
                class="ml-2 text-gray-700 group-hover:text-indigo-700 transition-colors duration-300"
                >I'd like to receive additional resources on this topic</span
              >
            </label>
          </div>

          <!-- Form navigation -->
          <div class="flex justify-between">
            <UiButton
              variant="ghost"
              state="default"
              size="md"
              @click="prevStep"
            >
              <template #prefix>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </template>
              Back
            </UiButton>

            <UiButton
              variant="primary"
              state="default"
              size="md"
              :disabled="!isFormValid"
              @click="submitAssignment"
            >
              Submit Assignment
              <template #suffix>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </template>
            </UiButton>
          </div>
        </div>

        <!-- Step 3: Completion -->
        <div v-else-if="activeStep === 3" key="step-3" class="step-content">
          <div class="text-center py-6">
            <!-- Loading state with progress -->
            <div v-if="submissionStatus.uploading">
              <div class="w-20 h-20 mx-auto mb-4 relative">
                <svg
                  class="animate-spin h-20 w-20 text-indigo-200"
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
                  />
                  <path
                    class="opacity-75"
                    stroke="currentColor"
                    stroke-width="4"
                    stroke-linecap="round"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                  />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                  <span class="text-indigo-700 font-bold"
                    >{{ submissionStatus.progress }}%</span
                  >
                </div>
              </div>
              <h3 class="text-xl font-bold mb-2">Uploading Your Assignment</h3>
              <p class="text-gray-600">
                Please wait while we upload your file...
              </p>

              <!-- Progress bar -->
              <div
                class="max-w-md mx-auto mt-4 bg-gray-200 rounded-full h-2.5 overflow-hidden"
              >
                <div
                  class="bg-indigo-600 h-full transition-all duration-300 ease-out"
                  :style="{ width: `${submissionStatus.progress}%` }"
                />
              </div>
            </div>

            <!-- Submitted state -->
            <div
              v-else-if="
                submissionStatus.submitted && !submissionStatus.completed
              "
              class="animate-pulse"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-16 w-16 text-indigo-600 mx-auto mb-4"
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
              <h3 class="text-xl font-bold mb-2">Processing Your Submission</h3>
              <p class="text-gray-600">Your assignment is being processed...</p>

              <!-- Processing animation dots -->
              <div class="flex justify-center mt-4">
                <div class="dot-animation">
                  <span />
                  <span />
                  <span />
                </div>
              </div>
            </div>

            <!-- Completed state -->
            <div v-else-if="submissionStatus.completed" class="text-center">
              <div
                class="w-20 h-20 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-12 w-12 text-green-500"
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
              <h3 class="text-2xl font-bold mb-2">Assignment Submitted!</h3>
              <p class="text-gray-600 mb-8">
                Your work has been successfully submitted for review.
              </p>

              <UiButton
                variant="primary"
                state="default"
                size="lg"
                @click="completeAssignment"
              >
                Continue to Next Lesson
                <template #suffix>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </template>
              </UiButton>
            </div>

            <!-- Error state -->
            <div v-else-if="submissionStatus.error" class="animate-fade-in">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-16 w-16 text-red-500 mx-auto mb-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
              <h3 class="text-xl font-bold mb-2">Submission Failed</h3>
              <p class="text-gray-600 mb-6">
                There was an error submitting your assignment. Please try again.
              </p>
              <div class="flex justify-center space-x-4">
                <UiButton
                  class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center"
                  @click="prevStep"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Back
                </UiButton>
                <UiButton
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm flex items-center"
                  @click="submitAssignment"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Try Again
                </UiButton>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<style scoped>
.assignment-container {
  --anim-duration: 0.3s;
  --primary: #4f46e5; /* indigo-600 */
  --primary-hover: #4338ca; /* indigo-700 */
  --primary-light: #eef2ff; /* indigo-50 */
  --success: #22c55e; /* green-500 */
  --error: #ef4444; /* red-500 */
  --warning: #f59e0b; /* amber-500 */
}

/* Step transitions */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity var(--anim-duration), transform var(--anim-duration);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(20px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

/* Scale animation for checkmarks */
@keyframes scale-in {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  70% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.animate-scale-in {
  animation: scale-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

/* Bounce animation */
@keyframes bounce-once {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-15px);
  }
  70% {
    transform: translateY(-7px);
  }
}

.animate-bounce-once {
  animation: bounce-once 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* Fade in animation */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}
</style>
