<!-- pages/consultancy/contact.vue -->
<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";

// Define proper types for the form
interface ContactForm {
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

// Form data with type
const contactForm = reactive<ContactForm>({
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
const fileInput = ref<HTMLInputElement | null>(null);
const submitSuccess = ref(false);

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
    contactForm.files = Array.from(input.files);
  }
};

// Form validation - fixed to avoid TypeScript errors
const validateForm = () => {
  // Reset errors with a new object instead of deleting properties
  const newErrors: Record<string, string> = {};

  let isValid = true;

  // Required fields
  if (!contactForm.fullName.trim()) {
    newErrors.fullName = "Full name is required";
    isValid = false;
  }

  if (!contactForm.email.trim()) {
    newErrors.email = "Email is required";
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactForm.email)) {
    newErrors.email = "Please enter a valid email address";
    isValid = false;
  }

  if (!contactForm.projectType) {
    newErrors.projectType = "Please select a project type";
    isValid = false;
  }

  if (!contactForm.projectTitle.trim()) {
    newErrors.projectTitle = "Project title is required";
    isValid = false;
  }

  if (!contactForm.projectDescription.trim()) {
    newErrors.projectDescription = "Project description is required";
    isValid = false;
  }

  if (!contactForm.privacyPolicy) {
    newErrors.privacyPolicy = "You must agree to the privacy policy";
    isValid = false;
  }

  // Replace errors object with new one
  Object.assign(errors, newErrors);

  return isValid;
};

// Form submission
const submitContactForm = async () => {
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
    // TODO: Replace with actual API call
    await new Promise((resolve) => setTimeout(resolve, 1500)); // Simulate API call

    // Success - show notification or redirect
    submitSuccess.value = true;

    // Reset form after a delay
    setTimeout(() => {
      // Reset form with type-safe approach
      contactForm.projectType = "";
      contactForm.fullName = "";
      contactForm.company = "";
      contactForm.email = "";
      contactForm.phone = "";
      contactForm.projectTitle = "";
      contactForm.projectDescription = "";
      contactForm.budget = "";
      contactForm.timeline = "";
      contactForm.files = [];
      contactForm.privacyPolicy = false;

      if (fileInput.value) {
        fileInput.value.value = "";
      }

      submitSuccess.value = false;
    }, 3000);
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

// Animation state
const isPageLoaded = ref(false);
const visibleSections = ref<string[]>([]);

// Set up intersection observer for animation on scroll
onMounted(() => {
  isPageLoaded.value = true;

  // Add form section to visible sections by default
  visibleSections.value = ["form-section"];

  if (typeof window !== "undefined" && "IntersectionObserver" in window) {
    const options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const sectionId = entry.target.id;
          if (sectionId && !visibleSections.value.includes(sectionId)) {
            visibleSections.value.push(sectionId);
          }
        }
      });
    }, options);

    // Observe each section
    document.querySelectorAll("[id]").forEach((element) => {
      observer.observe(element);
    });
  } else {
    // Fallback for browsers without IntersectionObserver
    visibleSections.value = ["form-section", "contact-info-section"];
  }
});

// Page meta
useHead({
  title: "Contact Our Consultancy Team | LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Contact our consultancy team to discuss your project needs. We're here to help you bring your ideas to life.",
    },
  ],
});

// Contact information
const contactInfo = {
  address: {
    street: "123 Innovation Street",
    city: "Tech Hub, CA 94103",
  },
  email: "consultancy@example.com",
  phone: "+1 (555) 123-4567",
  businessHours: {
    weekdays: "9:00 AM - 6:00 PM",
    weekend: "Closed",
  },
};
</script>

<template>
  <div>
    <!-- Hero section -->
    <section
      class="relative bg-gradient-to-br from-indigo-900 to-purple-900 text-white py-16 md:py-24 overflow-hidden"
    >
      <!-- Background decorative elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div
          class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full opacity-20 -mr-40 -mt-40"
        />
        <div
          class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 rounded-full opacity-20 -ml-40 -mb-40"
        />

        <!-- Grid pattern -->
        <div
          class="absolute inset-0 opacity-10"
          style="
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
          "
        />
      </div>

      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
          <!-- Animated entrance for title -->
          <h1
            class="text-3xl md:text-5xl font-bold mb-6 transform transition-all duration-1000"
            :class="
              isPageLoaded
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
          >
            Contact Our Consultancy Team
          </h1>

          <!-- Animated entrance for description -->
          <p
            class="text-lg md:text-xl text-indigo-100 mb-8 transform transition-all duration-1000"
            :class="
              isPageLoaded
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
            style="transition-delay: 200ms"
          >
            Have a project in mind? Reach out to our team of experts and let's
            discuss how we can help.
          </p>
        </div>
      </div>
    </section>

    <!-- Main content section with form and contact info -->
    <section id="form-section" class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:space-x-12">
          <!-- Contact form col -->
          <div class="lg:w-2/3 mb-10 lg:mb-0">
            <div
              class="transform transition-all duration-1000"
              :class="
                visibleSections.includes('form-section')
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-12'
              "
            >
              <!-- Success message -->
              <div
                v-if="submitSuccess"
                class="text-center py-12 bg-white rounded-xl shadow-xl border border-gray-200"
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
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                  Thank You!
                </h3>
                <p class="text-gray-600 mb-8">
                  Your inquiry has been successfully submitted. One of our
                  consultants will contact you within 24 hours.
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
                <div
                  class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-8"
                >
                  <span
                    class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-white text-black bg-opacity-20 backdrop-blur-sm mb-4"
                  >
                    Get in Touch
                  </span>
                  <h2 class="text-2xl md:text-3xl font-bold mb-3">
                    Project Inquiry Form
                  </h2>
                  <p class="text-indigo-100">
                    Fill out the form below with your project details, and our
                    team will get back to you to discuss how we can bring your
                    vision to life.
                  </p>
                </div>

                <!-- Form content -->
                <div class="p-8">
                  <form @submit.prevent="submitContactForm">
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
                          v-model="contactForm.projectType"
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                          :class="
                            errors.projectType
                              ? 'border-red-300 bg-red-50'
                              : 'border-gray-300'
                          "
                        >
                          <option value="" disabled selected>
                            Select project type
                          </option>
                          <option
                            v-for="type in projectTypes"
                            :key="type.value"
                            :value="type.value"
                          >
                            {{ type.label }}
                          </option>
                        </select>
                        <p
                          v-if="errors.projectType"
                          class="mt-1 text-sm text-red-600"
                        >
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
                          v-model="contactForm.fullName"
                          type="text"
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                          :class="
                            errors.fullName
                              ? 'border-red-300 bg-red-50'
                              : 'border-gray-300'
                          "
                          placeholder="Your full name"
                        />
                        <p
                          v-if="errors.fullName"
                          class="mt-1 text-sm text-red-600"
                        >
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
                          v-model="contactForm.company"
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
                          v-model="contactForm.email"
                          type="email"
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                          :class="
                            errors.email
                              ? 'border-red-300 bg-red-50'
                              : 'border-gray-300'
                          "
                          placeholder="you@example.com"
                        />
                        <p
                          v-if="errors.email"
                          class="mt-1 text-sm text-red-600"
                        >
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
                          v-model="contactForm.phone"
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
                          v-model="contactForm.projectTitle"
                          type="text"
                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                          :class="
                            errors.projectTitle
                              ? 'border-red-300 bg-red-50'
                              : 'border-gray-300'
                          "
                          placeholder="A brief title for your project"
                        />
                        <p
                          v-if="errors.projectTitle"
                          class="mt-1 text-sm text-red-600"
                        >
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
                          v-model="contactForm.projectDescription"
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
                          v-model="contactForm.budget"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                        >
                          <option value="" disabled selected>
                            Select budget range
                          </option>
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
                          v-model="contactForm.timeline"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                        >
                          <option value="" disabled selected>
                            Select timeline
                          </option>
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
                          type="file"
                          id="attachments"
                          ref="fileInput"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 hover:border-indigo-300"
                          multiple
                          @change="handleFileUpload"
                        />
                        <p class="text-xs text-gray-500 mt-1">
                          You can upload relevant files such as project briefs,
                          wireframes, or examples.
                        </p>
                      </div>

                      <!-- Privacy policy -->
                      <div class="col-span-2 mt-2">
                        <div class="flex items-start">
                          <div class="flex items-center h-5">
                            <input
                              id="privacyPolicy"
                              v-model="contactForm.privacyPolicy"
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
                            <p
                              v-if="errors.privacyPolicy"
                              class="text-red-600 mt-1"
                            >
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
                          {{
                            isSubmitting
                              ? "Submitting..."
                              : "Submit Project Inquiry"
                          }}
                        </div>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact information sidebar -->
          <div id="contact-info-section" class="lg:w-1/3">
            <div
              class="space-y-6 transform transition-all duration-1000"
              :class="
                visibleSections.includes('contact-info-section') ||
                visibleSections.includes('form-section')
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-12'
              "
              style="transition-delay: 200ms"
            >
              <!-- Contact info card with hover effect -->
              <div
                class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
              >
                <div
                  class="bg-gradient-to-r from-indigo-600 to-purple-600 py-4 px-6"
                >
                  <h3 class="text-lg font-bold text-white">
                    Contact Information
                  </h3>
                </div>
                <div class="p-6">
                  <div class="space-y-4">
                    <!-- Address -->
                    <div class="flex">
                      <div
                        class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-indigo-600"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                          />
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                          />
                        </svg>
                      </div>
                      <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-900">
                          Address
                        </h4>
                        <p class="mt-1 text-sm text-gray-600">
                          {{ contactInfo.address.street }}<br />
                          {{ contactInfo.address.city }}
                        </p>
                      </div>
                    </div>

                    <!-- Email -->
                    <div class="flex">
                      <div
                        class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-indigo-600"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                          />
                        </svg>
                      </div>
                      <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-900">Email</h4>
                        <p class="mt-1 text-sm text-gray-600">
                          <a
                            href="mailto:consultancy@example.com"
                            class="text-indigo-600 hover:text-indigo-800"
                          >
                            {{ contactInfo.email }}
                          </a>
                        </p>
                      </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex">
                      <div
                        class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-indigo-600"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                          />
                        </svg>
                      </div>
                      <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-900">Phone</h4>
                        <p class="mt-1 text-sm text-gray-600">
                          <a
                            href="tel:+15551234567"
                            class="text-indigo-600 hover:text-indigo-800"
                          >
                            {{ contactInfo.phone }}
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Business hours card with hover effect -->
              <div
                class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
              >
                <div
                  class="bg-gradient-to-r from-indigo-600 to-purple-600 py-4 px-6"
                >
                  <h3 class="text-lg font-bold text-white">Business Hours</h3>
                </div>
                <div class="p-6">
                  <div class="space-y-3">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Monday - Friday:</span>
                      <span class="font-medium text-gray-900">{{
                        contactInfo.businessHours.weekdays
                      }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Saturday - Sunday:</span>
                      <span class="font-medium text-gray-900">{{
                        contactInfo.businessHours.weekend
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action card with hover effect -->
              <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl p-6 relative hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:from-indigo-700 hover:to-purple-700"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-10 w-10 absolute top-4 right-4 text-white opacity-20"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                  />
                </svg>

                <h3 class="text-xl font-bold mb-3">
                  Need Immediate Assistance?
                </h3>
                <p class="text-indigo-100 mb-4">
                  For urgent inquiries, please call our consultancy hotline for
                  immediate assistance with your project needs.
                </p>
                <NuxtLink
                  to="/consultancy"
                  class="inline-block px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-white font-medium transition duration-300"
                >
                  Learn More About Our Services
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Fade-in animation for sections */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  animation: fadeInUp 0.6s ease-out forwards;
}
</style>
