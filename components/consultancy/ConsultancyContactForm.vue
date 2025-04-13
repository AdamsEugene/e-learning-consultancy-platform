<!-- components/consultancy/ConsultancyContactForm.vue -->
<script setup lang="ts">
import { ref, reactive } from "vue";

interface FormData {
  name: string;
  email: string;
  company: string;
  phone: string;
  serviceType: string;
  budget: string;
  timeframe: string;
  message: string;
  attachments: File[];
  acceptTerms: boolean;
}

interface Props {
  title?: string;
  subtitle?: string;
  description?: string;
  showBudgetField?: boolean;
  showTimeframeField?: boolean;
  showAttachments?: boolean;
  serviceOptions?: string[];
  budgetOptions?: { label: string; value: string }[];
  timeframeOptions?: { label: string; value: string }[];
}

// Props with defaults
const props = withDefaults(defineProps<Props>(), {
  title: "Request a Consultation",
  subtitle: "Get Started Today",
  description:
    "Fill out the form below to request a consultation with our team of experts. We'll get back to you within 24 hours to discuss your project needs.",
  showBudgetField: true,
  showTimeframeField: true,
  showAttachments: true,
  serviceOptions: () => [
    "Digital Transformation",
    "Software Development",
    "UX/UI Design",
    "Data Analytics",
    "Cloud Migration",
    "IT Strategy",
    "Other",
  ],
  budgetOptions: () => [
    { label: "Under $5,000", value: "under_5k" },
    { label: "$5,000 - $10,000", value: "5k_10k" },
    { label: "$10,000 - $25,000", value: "10k_25k" },
    { label: "$25,000 - $50,000", value: "25k_50k" },
    { label: "$50,000+", value: "over_50k" },
    { label: "Not sure yet", value: "undefined" },
  ],
  timeframeOptions: () => [
    { label: "As soon as possible", value: "immediate" },
    { label: "Within 1 month", value: "1_month" },
    { label: "Within 3 months", value: "3_months" },
    { label: "Within 6 months", value: "6_months" },
    { label: "Flexible", value: "flexible" },
  ],
});

// Form state
const formData = reactive<FormData>({
  name: "",
  email: "",
  company: "",
  phone: "",
  serviceType: "",
  budget: "",
  timeframe: "",
  message: "",
  attachments: [],
  acceptTerms: false,
});

// UI state
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref(false);
const errorMessage = ref("");
const formSteps = ref<"details" | "project" | "confirm">("details");
const attachmentPreview = ref<{ name: string; size: string; type: string }[]>(
  []
);

// File input reference
const fileInput = ref<HTMLInputElement | null>(null);

// Form validation
const errors = reactive<Record<string, string>>({});

const validateForm = (): boolean => {
  errors.name = formData.name ? "" : "Name is required";
  errors.email = formData.email ? "" : "Email is required";

  if (formData.email && !/^\S+@\S+\.\S+$/.test(formData.email)) {
    errors.email = "Please enter a valid email address";
  }

  errors.serviceType = formData.serviceType
    ? ""
    : "Please select a service type";

  if (formSteps.value === "project") {
    if (props.showBudgetField) {
      errors.budget = formData.budget ? "" : "Please select a budget range";
    }

    if (props.showTimeframeField) {
      errors.timeframe = formData.timeframe ? "" : "Please select a timeframe";
    }

    errors.message = formData.message
      ? ""
      : "Please provide some details about your project";
  }

  if (formSteps.value === "confirm") {
    errors.acceptTerms = formData.acceptTerms
      ? ""
      : "You must accept the terms and conditions";
  }

  // Check if there are any errors
  return Object.values(errors).every((error) => error === "");
};

// Reset file input
const resetFileInput = () => {
  if (fileInput.value) {
    fileInput.value.value = "";
  }
};

// Handle file uploads
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    const files = Array.from(input.files);
    formData.attachments = [...formData.attachments, ...files];

    // Create preview data
    files.forEach((file) => {
      const size = formatFileSize(file.size);
      attachmentPreview.value.push({
        name: file.name,
        size,
        type: file.type,
      });
    });

    // Reset file input
    resetFileInput();
  }
};

// Format file size
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return "0 Bytes";

  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

// Remove attachment
const removeAttachment = (index: number) => {
  formData.attachments.splice(index, 1);
  attachmentPreview.value.splice(index, 1);
};

// Handle form navigation
const nextStep = () => {
  if (formSteps.value === "details") {
    // Validate only the fields in the first step
    errors.name = formData.name ? "" : "Name is required";
    errors.email = formData.email ? "" : "Email is required";

    if (formData.email && !/^\S+@\S+\.\S+$/.test(formData.email)) {
      errors.email = "Please enter a valid email address";
    }

    errors.serviceType = formData.serviceType
      ? ""
      : "Please select a service type";

    if (!errors.name && !errors.email && !errors.serviceType) {
      formSteps.value = "project";
    }
  } else if (formSteps.value === "project") {
    // Validate only the fields in the second step
    if (props.showBudgetField) {
      errors.budget = formData.budget ? "" : "Please select a budget range";
    }

    if (props.showTimeframeField) {
      errors.timeframe = formData.timeframe ? "" : "Please select a timeframe";
    }

    errors.message = formData.message
      ? ""
      : "Please provide some details about your project";

    if (
      (!props.showBudgetField || !errors.budget) &&
      (!props.showTimeframeField || !errors.timeframe) &&
      !errors.message
    ) {
      formSteps.value = "confirm";
    }
  }
};

const prevStep = () => {
  if (formSteps.value === "project") {
    formSteps.value = "details";
  } else if (formSteps.value === "confirm") {
    formSteps.value = "project";
  }
};

// Submit form
const submitForm = async () => {
  // Validate all fields
  const isValid = validateForm();

  if (!isValid) {
    return;
  }

  isSubmitting.value = true;
  submitError.value = false;
  errorMessage.value = "";

  try {
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // Simulate successful form submission
    submitSuccess.value = true;
    console.log("Form data submitted:", formData);

    // Reset form after successful submission
    setTimeout(() => {
      formData.name = "";
      formData.email = "";
      formData.company = "";
      formData.phone = "";
      formData.serviceType = "";
      formData.budget = "";
      formData.timeframe = "";
      formData.message = "";
      formData.attachments = [];
      formData.acceptTerms = false;
      attachmentPreview.value = [];
      formSteps.value = "details";
      submitSuccess.value = false;
    }, 3000);
  } catch (error) {
    submitError.value = true;
    errorMessage.value =
      "There was an error submitting your request. Please try again.";
    console.error("Form submission error:", error);
  } finally {
    isSubmitting.value = false;
  }
};

// Trigger file input click
const triggerFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

// Check if field has an error
const hasError = (field: string): boolean => {
  return !!errors[field];
};

// Get error message for a field
const getErrorMessage = (field: string): string => {
  return errors[field] || "";
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 transform transition-all duration-500 hover:shadow-2xl"
  >
    <!-- Form header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-8">
      <div class="max-w-3xl mx-auto">
        <span
          class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-white bg-opacity-20 backdrop-blur-sm mb-4"
        >
          {{ subtitle }}
        </span>
        <h2 class="text-2xl md:text-3xl font-bold mb-3">{{ title }}</h2>
        <p class="text-indigo-100">{{ description }}</p>
      </div>
    </div>

    <!-- Form content -->
    <div class="p-8">
      <div v-if="submitSuccess" class="text-center py-12">
        <div
          class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-6"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-8 w-8"
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
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h3>
        <p class="text-gray-600 mb-8">
          Your consultation request has been successfully submitted. One of our
          experts will contact you within 24 hours.
        </p>
        <div class="animate-pulse text-indigo-600 text-sm">
          Redirecting you shortly...
        </div>
      </div>

      <form v-else class="max-w-3xl mx-auto" @submit.prevent="submitForm">
        <!-- Step indicators -->
        <div class="flex justify-between items-center mb-8">
          <div
            v-for="(step, index) in [
              'Contact Details',
              'Project Information',
              'Confirm & Submit',
            ]"
            :key="step"
            class="flex items-center w-full"
          >
            <!-- Step circle -->
            <div
              class="flex items-center justify-center w-10 h-10 rounded-full border-2 font-bold relative z-10 transition-colors duration-300"
              :class="[
                index === 0 && formSteps === 'details'
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : '',
                index === 1 && formSteps === 'project'
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : '',
                index === 2 && formSteps === 'confirm'
                  ? 'bg-indigo-600 text-white border-indigo-600'
                  : '',
                index < ['details', 'project', 'confirm'].indexOf(formSteps) + 1
                  ? 'border-indigo-600 text-indigo-600'
                  : 'border-gray-300 text-gray-400',
                (index === 0 && formSteps === 'project') ||
                (index <= 1 && formSteps === 'confirm')
                  ? 'bg-indigo-100'
                  : '',
              ]"
            >
              {{ index + 1 }}

              <!-- Check mark for completed steps -->
              <svg
                v-if="
                  (index === 0 && formSteps !== 'details') ||
                  (index === 1 && formSteps === 'confirm')
                "
                xmlns="http://www.w3.org/2000/svg"
                class="absolute h-4 w-4 -right-1 -top-1 bg-green-500 text-white rounded-full p-0.5"
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

            <!-- Step label -->
            <span
              class="ml-3 text-sm font-medium hidden md:block transition-colors duration-300"
              :class="[
                index === 0 && formSteps === 'details' ? 'text-indigo-600' : '',
                index === 1 && formSteps === 'project' ? 'text-indigo-600' : '',
                index === 2 && formSteps === 'confirm' ? 'text-indigo-600' : '',
                index < ['details', 'project', 'confirm'].indexOf(formSteps) + 1
                  ? 'text-gray-900'
                  : 'text-gray-400',
              ]"
            >
              {{ step }}
            </span>

            <!-- Connector line -->
            <div
              v-if="index < 2"
              class="flex-grow border-t-2 mx-4 transition-colors duration-300"
              :class="[
                (index === 0 && formSteps !== 'details') ||
                (index === 1 && formSteps === 'confirm')
                  ? 'border-indigo-600'
                  : 'border-gray-300',
              ]"
            />
          </div>
        </div>

        <!-- Step 1: Contact Details -->
        <div v-if="formSteps === 'details'">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Name field -->
            <div class="col-span-1">
              <label
                for="name"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Full Name <span class="text-red-500">*</span>
              </label>
              <input
                id="name"
                v-model="formData.name"
                type="text"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300"
                :class="
                  hasError('name')
                    ? 'border-red-300 bg-red-50'
                    : 'border-gray-300'
                "
                placeholder="Your full name"
                @blur="validateForm"
              />
              <p v-if="hasError('name')" class="mt-1 text-sm text-red-600">
                {{ getErrorMessage("name") }}
              </p>
            </div>

            <!-- Email field -->
            <div class="col-span-1">
              <label
                for="email"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Email Address <span class="text-red-500">*</span>
              </label>
              <input
                id="email"
                v-model="formData.email"
                type="email"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300"
                :class="
                  hasError('email')
                    ? 'border-red-300 bg-red-50'
                    : 'border-gray-300'
                "
                placeholder="your.email@example.com"
                @blur="validateForm"
              />
              <p v-if="hasError('email')" class="mt-1 text-sm text-red-600">
                {{ getErrorMessage("email") }}
              </p>
            </div>

            <!-- Company field -->
            <div class="col-span-1">
              <label
                for="company"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Company Name
              </label>
              <input
                id="company"
                v-model="formData.company"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300"
                placeholder="Your company name"
              />
            </div>

            <!-- Phone field -->
            <div class="col-span-1">
              <label
                for="phone"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Phone Number
              </label>
              <input
                id="phone"
                v-model="formData.phone"
                type="tel"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300"
                placeholder="+1 (555) 123-4567"
              />
            </div>

            <!-- Service type field -->
            <div class="col-span-1 md:col-span-2">
              <label
                for="serviceType"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Service Type <span class="text-red-500">*</span>
              </label>
              <select
                id="serviceType"
                v-model="formData.serviceType"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300"
                :class="
                  hasError('serviceType')
                    ? 'border-red-300 bg-red-50'
                    : 'border-gray-300'
                "
                @blur="validateForm"
              >
                <option value="" disabled selected>
                  Select the type of service you need
                </option>
                <option
                  v-for="service in serviceOptions"
                  :key="service"
                  :value="service"
                >
                  {{ service }}
                </option>
              </select>
              <p
                v-if="hasError('serviceType')"
                class="mt-1 text-sm text-red-600"
              >
                {{ getErrorMessage("serviceType") }}
              </p>
            </div>
          </div>

          <!-- Step navigation -->
          <div class="flex justify-end">
            <button
              type="button"
              class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300 shadow-md"
              @click="nextStep"
            >
              Next Step: Project Details
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 ml-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Step 2: Project Information -->
        <div v-if="formSteps === 'project'">
          <div class="space-y-6 mb-8">
            <!-- Budget field -->
            <div v-if="showBudgetField">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Budget Range <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="option in budgetOptions"
                  :key="option.value"
                  class="col-span-1"
                >
                  <label
                    :for="`budget_${option.value}`"
                    class="block w-full cursor-pointer border rounded-lg px-4 py-3 transition-all duration-300"
                    :class="[
                      formData.budget === option.value
                        ? 'bg-indigo-50 border-indigo-500 text-indigo-700 shadow-sm'
                        : 'border-gray-300 hover:border-indigo-300 hover:bg-indigo-50/50',
                    ]"
                  >
                    <input
                      :id="`budget_${option.value}`"
                      v-model="formData.budget"
                      type="radio"
                      name="budget"
                      :value="option.value"
                      class="sr-only"
                    />
                    <span class="flex items-center">
                      <span
                        class="inline-block w-4 h-4 rounded-full mr-2 border transition-colors duration-300"
                        :class="[
                          formData.budget === option.value
                            ? 'bg-indigo-600 border-indigo-600'
                            : 'bg-white border-gray-300',
                        ]"
                      />
                      <span class="font-medium">{{ option.label }}</span>
                    </span>
                  </label>
                </div>
              </div>
              <p v-if="hasError('budget')" class="mt-1 text-sm text-red-600">
                {{ getErrorMessage("budget") }}
              </p>
            </div>

            <!-- Timeframe field -->
            <div v-if="showTimeframeField">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Project Timeframe <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="option in timeframeOptions"
                  :key="option.value"
                  class="col-span-1"
                >
                  <label
                    :for="`timeframe_${option.value}`"
                    class="block w-full cursor-pointer border rounded-lg px-4 py-3 transition-all duration-300"
                    :class="[
                      formData.timeframe === option.value
                        ? 'bg-indigo-50 border-indigo-500 text-indigo-700 shadow-sm'
                        : 'border-gray-300 hover:border-indigo-300 hover:bg-indigo-50/50',
                    ]"
                  >
                    <input
                      :id="`timeframe_${option.value}`"
                      v-model="formData.timeframe"
                      type="radio"
                      name="timeframe"
                      :value="option.value"
                      class="sr-only"
                    />
                    <span class="flex items-center">
                      <span
                        class="inline-block w-4 h-4 rounded-full mr-2 border transition-colors duration-300"
                        :class="[
                          formData.timeframe === option.value
                            ? 'bg-indigo-600 border-indigo-600'
                            : 'bg-white border-gray-300',
                        ]"
                      />
                      <span class="font-medium">{{ option.label }}</span>
                    </span>
                  </label>
                </div>
              </div>
              <p v-if="hasError('timeframe')" class="mt-1 text-sm text-red-600">
                {{ getErrorMessage("timeframe") }}
              </p>
            </div>

            <!-- Project details field -->
            <div>
              <label
                for="message"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Project Details <span class="text-red-500">*</span>
              </label>
              <textarea
                id="message"
                v-model="formData.message"
                rows="5"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-300"
                :class="
                  hasError('message')
                    ? 'border-red-300 bg-red-50'
                    : 'border-gray-300'
                "
                placeholder="Please describe your project and requirements in detail"
                @blur="validateForm"
              />
              <p v-if="hasError('message')" class="mt-1 text-sm text-red-600">
                {{ getErrorMessage("message") }}
              </p>
            </div>

            <!-- File attachments -->
            <div v-if="showAttachments">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Attachments (optional)
              </label>
              <div
                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-300 transition-colors duration-300 cursor-pointer"
                @click="triggerFileInput"
              >
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  class="hidden"
                  @change="handleFileUpload"
                />
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="mx-auto h-12 w-12 text-gray-400"
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
                <p class="mt-2 text-sm text-gray-600">
                  Drag and drop files here, or
                  <span class="text-indigo-600 font-medium"
                    >click to browse</span
                  >
                </p>
                <p class="mt-1 text-xs text-gray-500">
                  Max file size: 10MB each. Supported formats: PDF, DOC, JPG,
                  PNG
                </p>
              </div>

              <!-- Attachment preview -->
              <div v-if="attachmentPreview.length > 0" class="mt-4 space-y-2">
                <div
                  v-for="(file, index) in attachmentPreview"
                  :key="index"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200"
                >
                  <div class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-500 mr-2"
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
                    <div>
                      <p
                        class="text-sm font-medium text-gray-700 truncate"
                        :title="file.name"
                      >
                        {{ file.name }}
                      </p>
                      <p class="text-xs text-gray-500">{{ file.size }}</p>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="text-gray-400 hover:text-red-500 transition-colors duration-300"
                    @click.stop="removeAttachment(index)"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
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
              </div>
            </div>
          </div>

          <!-- Step navigation -->
          <div class="flex justify-between">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300"
              @click="prevStep"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
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
            </button>
            <button
              type="button"
              class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300 shadow-md"
              @click="nextStep"
            >
              Next Step: Review & Submit
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 ml-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Step 3: Confirm & Submit -->
        <div v-if="formSteps === 'confirm'">
          <div
            class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 mb-8"
          >
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Review Your Information
            </h3>

            <!-- Contact information -->
            <div class="mb-6">
              <h4
                class="text-sm font-bold text-indigo-600 uppercase tracking-wide mb-2"
              >
                Contact Details
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="flex">
                  <span class="font-medium text-gray-500 w-24">Name:</span>
                  <span class="text-gray-900">{{ formData.name }}</span>
                </div>
                <div class="flex">
                  <span class="font-medium text-gray-500 w-24">Email:</span>
                  <span class="text-gray-900">{{ formData.email }}</span>
                </div>
                <div class="flex">
                  <span class="font-medium text-gray-500 w-24">Company:</span>
                  <span class="text-gray-900">{{
                    formData.company || "Not provided"
                  }}</span>
                </div>
                <div class="flex">
                  <span class="font-medium text-gray-500 w-24">Phone:</span>
                  <span class="text-gray-900">{{
                    formData.phone || "Not provided"
                  }}</span>
                </div>
                <div class="flex md:col-span-2">
                  <span class="font-medium text-gray-500 w-24">Service:</span>
                  <span class="text-gray-900">{{ formData.serviceType }}</span>
                </div>
              </div>
            </div>

            <!-- Project information -->
            <div class="mb-6">
              <h4
                class="text-sm font-bold text-indigo-600 uppercase tracking-wide mb-2"
              >
                Project Details
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mb-3">
                <div v-if="showBudgetField" class="flex">
                  <span class="font-medium text-gray-500 w-24">Budget:</span>
                  <span class="text-gray-900">{{
                    budgetOptions.find((b) => b.value === formData.budget)
                      ?.label
                  }}</span>
                </div>
                <div v-if="showTimeframeField" class="flex">
                  <span class="font-medium text-gray-500 w-24">Timeframe:</span>
                  <span class="text-gray-900">{{
                    timeframeOptions.find((t) => t.value === formData.timeframe)
                      ?.label
                  }}</span>
                </div>
              </div>
              <div class="text-sm">
                <div class="font-medium text-gray-500 mb-1">
                  Project Description:
                </div>
                <div
                  class="bg-white border border-gray-200 rounded-lg p-3 text-gray-900 whitespace-pre-line"
                >
                  {{ formData.message }}
                </div>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="showAttachments && attachmentPreview.length > 0">
              <h4
                class="text-sm font-bold text-indigo-600 uppercase tracking-wide mb-2"
              >
                Attachments
              </h4>
              <div class="flex flex-wrap gap-2">
                <div
                  v-for="(file, index) in attachmentPreview"
                  :key="index"
                  class="flex items-center bg-white px-3 py-2 rounded-lg border border-gray-200 text-sm"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-gray-500 mr-1"
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
                  <span>{{ file.name }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Terms and conditions -->
          <div class="mb-8">
            <label class="flex items-start">
              <input
                v-model="formData.acceptTerms"
                type="checkbox"
                class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-colors duration-300"
                :class="hasError('acceptTerms') ? 'border-red-300' : ''"
              />
              <span class="ml-3 text-sm text-gray-600">
                By submitting this form, I agree to the
                <a
                  href="#"
                  class="text-indigo-600 hover:text-indigo-800 font-medium"
                  >Terms of Service</a
                >
                and acknowledge the
                <a
                  href="#"
                  class="text-indigo-600 hover:text-indigo-800 font-medium"
                  >Privacy Policy</a
                >
                of LearnConsult.
              </span>
            </label>
            <p
              v-if="hasError('acceptTerms')"
              class="mt-1 text-sm text-red-600 ml-7"
            >
              {{ getErrorMessage("acceptTerms") }}
            </p>
          </div>

          <!-- Error message -->
          <div
            v-if="submitError"
            class="bg-red-50 border-l-4 border-red-500 p-4 mb-6"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-red-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <!-- Step navigation -->
          <div class="flex justify-between">
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300"
              @click="prevStep"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
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
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300 shadow-md disabled:opacity-70 disabled:cursor-not-allowed"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting" class="flex items-center">
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
                  />
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  />
                </svg>
                Submitting...
              </span>
              <span v-else class="flex items-center">
                Submit Consultation Request
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 ml-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
