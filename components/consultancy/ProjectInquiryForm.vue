<script setup lang="ts">
import { ref, reactive } from "vue";

interface ProjectInquiryFormData {
  projectType: string;
  fullName: string;
  company: string;
  email: string;
  phone: string;
  projectTitle: string;
  projectDescription: string;
  budget: string;
  timeline: string;
  files: File[];
  privacyPolicy: boolean;
}

interface Props {
  title?: string;
  subtitle?: string;
  description?: string;
}

// Props with defaults
withDefaults(defineProps<Props>(), {
  title: "Project Inquiry Form",
  subtitle: "Get in Touch",
  description:
    "Fill out the form below with your project details, and our team will get back to you to discuss how we can bring your vision to life.",
});

// Define emit events
const emit = defineEmits<{
  (e: "submit", formData: ProjectInquiryFormData): void;
}>();

// Form data with type
const formData = reactive<ProjectInquiryFormData>({
  projectType: "",
  fullName: "",
  company: "",
  email: "",
  phone: "",
  projectTitle: "",
  projectDescription: "",
  budget: "",
  timeline: "",
  files: [],
  privacyPolicy: false,
});

// Form state
const errors = reactive<Record<string, string>>({});
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

// Options for select fields
const projectTypes = [
  { value: "web_development", label: "Web Development" },
  { value: "mobile_app", label: "Mobile App Development" },
  { value: "custom_software", label: "Custom Software Development" },
  { value: "ui_ux_design", label: "UI/UX Design" },
  { value: "data_analytics", label: "Data Analytics & BI" },
  { value: "cloud_services", label: "Cloud Services & Migration" },
  { value: "security", label: "Cybersecurity Consulting" },
  { value: "other", label: "Other / Not Sure" },
];

const budgetRanges = [
  { value: "under_5k", label: "Under $5,000" },
  { value: "5k_15k", label: "$5,000 - $15,000" },
  { value: "15k_30k", label: "$15,000 - $30,000" },
  { value: "30k_50k", label: "$30,000 - $50,000" },
  { value: "50k_100k", label: "$50,000 - $100,000" },
  { value: "over_100k", label: "Over $100,000" },
  { value: "not_sure", label: "Not sure / To be discussed" },
];

const timelineOptions = [
  { value: "urgent", label: "Urgent (ASAP)" },
  { value: "under_1_month", label: "Less than 1 month" },
  { value: "1_3_months", label: "1-3 months" },
  { value: "3_6_months", label: "3-6 months" },
  { value: "over_6_months", label: "Over 6 months" },
  { value: "flexible", label: "Flexible / Not sure" },
];

// Handle file uploads
const handleFileUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    formData.files = Array.from(input.files);
  }
};

// Form validation - fixed to avoid TypeScript errors
const validateForm = () => {
  // Reset errors with a new object instead of deleting properties
  const newErrors: Record<string, string> = {};

  let isValid = true;

  // Required fields
  if (!formData.fullName.trim()) {
    newErrors.fullName = "Full name is required";
    isValid = false;
  }

  if (!formData.email.trim()) {
    newErrors.email = "Email is required";
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
    newErrors.email = "Please enter a valid email address";
    isValid = false;
  }

  if (!formData.projectType) {
    newErrors.projectType = "Please select a project type";
    isValid = false;
  }

  if (!formData.projectTitle.trim()) {
    newErrors.projectTitle = "Project title is required";
    isValid = false;
  }

  if (!formData.projectDescription.trim()) {
    newErrors.projectDescription = "Project description is required";
    isValid = false;
  }

  if (!formData.privacyPolicy) {
    newErrors.privacyPolicy = "You must agree to the privacy policy";
    isValid = false;
  }

  // Replace errors object with new one
  Object.assign(errors, newErrors);

  return isValid;
};

// Form submission
const submitForm = async () => {
  if (!validateForm()) {
    // Scroll to first error
    const firstErrorField = document.querySelector(".is-invalid");
    if (firstErrorField) {
      firstErrorField.scrollIntoView({ behavior: "smooth", block: "center" });
    }
    return;
  }

  isSubmitting.value = true;

  try {
    // Emit form data to parent component
    emit("submit", formData);

    // Show success message
    submitSuccess.value = true;

    // Reset form after a delay would typically be handled by parent
  } catch (error) {
    // Handle error
    console.error("Error submitting form:", error);
    alert(
      "There was an error submitting your inquiry. Please try again later."
    );
  } finally {
    isSubmitting.value = false;
  }
};

// Reset form with type-safe approach
const resetForm = () => {
  formData.projectType = "";
  formData.fullName = "";
  formData.company = "";
  formData.email = "";
  formData.phone = "";
  formData.projectTitle = "";
  formData.projectDescription = "";
  formData.budget = "";
  formData.timeline = "";
  formData.files = [];
  formData.privacyPolicy = false;

  if (fileInput.value) {
    fileInput.value.value = "";
  }

  submitSuccess.value = false;
};

// Expose methods for parent component to use
defineExpose({
  resetForm,
  submitSuccess,
});
</script>

<template>
  <!-- Success message -->
  <div
    v-if="submitSuccess"
    class="text-center py-12 bg-white rounded-xl shadow-xl border border-gray-200 hover:shadow-2xl transition-all duration-300"
  >
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
      Your inquiry has been successfully submitted. One of our consultants will
      contact you within 24 hours.
    </p>
    <div class="animate-pulse text-indigo-600 text-sm">
      Redirecting you shortly...
    </div>
  </div>

  <!-- Form container -->
  <div
    v-else
    class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 hover:shadow-2xl transition-all duration-300"
  >
    <!-- Form header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-8">
      <span
        class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-white text-black bg-opacity-20 backdrop-blur-sm mb-4"
      >
        {{ subtitle }}
      </span>
      <h2 class="text-2xl md:text-3xl font-bold mb-3">{{ title }}</h2>
      <p class="text-indigo-100">{{ description }}</p>
    </div>

    <!-- Form content -->
    <div class="p-8">
      <form @submit.prevent="submitForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <!-- Project Type -->
          <div class="col-span-2">
            <label
              for="projectType"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              What type of project do you need help with?
              <span class="text-red-500">*</span>
            </label>
            <select
              id="projectType"
              v-model="formData.projectType"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              :class="
                errors.projectType
                  ? 'border-red-300 bg-red-50'
                  : 'border-gray-300'
              "
            >
              <option value="" disabled selected>Select project type</option>
              <option
                v-for="type in projectTypes"
                :key="type.value"
                :value="type.value"
              >
                {{ type.label }}
              </option>
            </select>
            <p v-if="errors.projectType" class="mt-1 text-sm text-red-600">
              {{ errors.projectType }}
            </p>
          </div>

          <!-- Name field with hover effect -->
          <div class="col-span-1">
            <label
              for="fullName"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Full Name <span class="text-red-500">*</span>
            </label>
            <input
              id="fullName"
              v-model="formData.fullName"
              type="text"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              :class="
                errors.fullName ? 'border-red-300 bg-red-50' : 'border-gray-300'
              "
              placeholder="Your full name"
            />
            <p v-if="errors.fullName" class="mt-1 text-sm text-red-600">
              {{ errors.fullName }}
            </p>
          </div>

          <!-- Company field with hover effect -->
          <div class="col-span-1">
            <label
              for="company"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Company / Organization
            </label>
            <input
              id="company"
              v-model="formData.company"
              type="text"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              placeholder="Your company name"
            />
          </div>

          <!-- Email field with hover effect -->
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
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              :class="
                errors.email ? 'border-red-300 bg-red-50' : 'border-gray-300'
              "
              placeholder="you@example.com"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
            </p>
          </div>

          <!-- Phone field with hover effect -->
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
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              placeholder="Your phone number"
            />
          </div>

          <!-- Project Title with hover effect -->
          <div class="col-span-2">
            <label
              for="projectTitle"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Project Title <span class="text-red-500">*</span>
            </label>
            <input
              id="projectTitle"
              v-model="formData.projectTitle"
              type="text"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              :class="
                errors.projectTitle
                  ? 'border-red-300 bg-red-50'
                  : 'border-gray-300'
              "
              placeholder="A brief title for your project"
            />
            <p v-if="errors.projectTitle" class="mt-1 text-sm text-red-600">
              {{ errors.projectTitle }}
            </p>
          </div>

          <!-- Project Description with hover effect -->
          <div class="col-span-2">
            <label
              for="projectDescription"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Project Description
              <span class="text-red-500">*</span>
            </label>
            <textarea
              id="projectDescription"
              v-model="formData.projectDescription"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              :class="
                errors.projectDescription
                  ? 'border-red-300 bg-red-50'
                  : 'border-gray-300'
              "
              rows="5"
              placeholder="Please describe your project, goals, and any specific requirements"
            ></textarea>
            <p
              v-if="errors.projectDescription"
              class="mt-1 text-sm text-red-600"
            >
              {{ errors.projectDescription }}
            </p>
          </div>

          <!-- Budget field with hover effect -->
          <div class="col-span-1">
            <label
              for="budget"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Estimated Budget
            </label>
            <select
              id="budget"
              v-model="formData.budget"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
            >
              <option value="" disabled selected>Select budget range</option>
              <option
                v-for="budget in budgetRanges"
                :key="budget.value"
                :value="budget.value"
              >
                {{ budget.label }}
              </option>
            </select>
          </div>

          <!-- Timeline field with hover effect -->
          <div class="col-span-1">
            <label
              for="timeline"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Timeline
            </label>
            <select
              id="timeline"
              v-model="formData.timeline"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
            >
              <option value="" disabled selected>Select timeline</option>
              <option
                v-for="timeline in timelineOptions"
                :key="timeline.value"
                :value="timeline.value"
              >
                {{ timeline.label }}
              </option>
            </select>
          </div>

          <!-- Attachments with hover effect -->
          <div class="col-span-2">
            <label
              for="attachments"
              class="block text-sm font-medium text-gray-700 mb-1"
            >
              Attachments (optional)
            </label>
            <input
              id="attachments"
              ref="fileInput"
              type="file"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
              multiple
              @change="handleFileUpload"
            />
            <p class="text-xs text-gray-500 mt-1">
              You can upload relevant files such as project briefs, wireframes,
              or examples.
            </p>
          </div>

          <!-- Privacy policy -->
          <div class="col-span-2 mt-2">
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input
                  id="privacyPolicy"
                  v-model="formData.privacyPolicy"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  :class="{
                    'border-red-300': errors.privacyPolicy,
                  }"
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="privacyPolicy" class="text-gray-700">
                  I agree to the
                  <NuxtLink
                    to="/privacy"
                    class="text-indigo-600 hover:text-indigo-800"
                    >privacy policy</NuxtLink
                  >
                  and consent to being contacted about my inquiry.
                </label>
                <p v-if="errors.privacyPolicy" class="text-red-600 mt-1">
                  {{ errors.privacyPolicy }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 hover:from-indigo-700 hover:to-purple-700 transform hover:-translate-y-0.5"
            :disabled="isSubmitting"
          >
            <div class="flex items-center">
              <span v-if="isSubmitting" class="mr-2">
                <svg
                  class="animate-spin h-5 w-5 text-white"
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
              </span>
              {{ isSubmitting ? "Submitting..." : "Submit Project Inquiry" }}
            </div>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
