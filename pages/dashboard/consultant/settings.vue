<!-- pages/dashboard/consultant/settings/index.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Settings - Consultant Dashboard",
});

// User profile data
interface ConsultantProfile {
  name: string;
  email: string;
  phone: string;
  title: string;
  specialization: string;
  bio: string;
  yearsExperience: number;
  hourlyRate: number;
  availability: string[];
  skills: string[];
  languages: string[];
  education: Array<{
    id: number;
    degree: string;
    institution: string;
    year: string;
  }>;
  certifications: Array<{
    id: number;
    name: string;
    issuer: string;
    year: string;
    expiration?: string;
  }>;
  socialLinks: {
    linkedin: string;
    twitter: string;
    github: string;
    website: string;
  };
  notificationPreferences: {
    email: boolean;
    push: boolean;
    sms: boolean;
    newRequests: boolean;
    appointmentReminders: boolean;
    paymentReceipts: boolean;
    platformUpdates: boolean;
  };
}

// States for different settings
const activeTab = ref("profile");
const isLoading = ref(true);
const isSaving = ref(false);
const saveSuccess = ref(false);
const saveError = ref(false);
const confirmPasswordVisible = ref(false);
const showDeleteAccountModal = ref(false);
const deleteAccountReason = ref("");
const deleteAccountConfirmation = ref("");
const passwordFieldType = ref("password");

// Availability options
const availabilityOptions = [
  "Weekdays 9am-5pm",
  "Weekends",
  "Evenings",
  "Early mornings",
  "Full-time",
  "Part-time",
  "Custom schedule",
];

// Skills options (for autocomplete)
const skillsOptions = [
  "JavaScript",
  "TypeScript",
  "Vue.js",
  "Nuxt.js",
  "React",
  "Angular",
  "Node.js",
  "Express",
  "MongoDB",
  "PostgreSQL",
  "MySQL",
  "AWS",
  "Azure",
  "Google Cloud",
  "Docker",
  "Kubernetes",
  "CI/CD",
  "UI/UX Design",
  "Project Management",
  "Agile/Scrum",
  "DevOps",
  "System Architecture",
  "Technical Writing",
  "Mobile Development",
  "iOS Development",
  "Android Development",
  "React Native",
  "Flutter",
  "GraphQL",
  "REST API Design",
  "Python",
  "Data Science",
  "Machine Learning",
  "Artificial Intelligence",
  "SEO",
  "Digital Marketing",
  "Content Strategy",
  "E-commerce",
  "Blockchain",
  "Cybersecurity",
];

// Language options
const languageOptions = [
  "English",
  "Spanish",
  "French",
  "German",
  "Chinese",
  "Japanese",
  "Russian",
  "Portuguese",
  "Italian",
  "Dutch",
  "Korean",
  "Arabic",
  "Hindi",
  "Swedish",
  "Norwegian",
  "Finnish",
  "Danish",
  "Polish",
  "Turkish",
  "Hebrew",
  "Thai",
  "Vietnamese",
];

// Consultant profile data
const profile = ref<ConsultantProfile>({
  name: "Sarah Parker",
  email: "sarah.parker@example.com",
  phone: "+1 (555) 123-4567",
  title: "Senior Software Consultant",
  specialization: "Full-Stack Development",
  bio: "Experienced software consultant with 8+ years of experience in developing enterprise applications. Specialized in JavaScript frameworks, particularly Vue.js and Node.js. Passionate about creating scalable, maintainable, and user-friendly applications.",
  yearsExperience: 8,
  hourlyRate: 150,
  availability: ["Weekdays 9am-5pm", "Evenings"],
  skills: [
    "JavaScript",
    "TypeScript",
    "Vue.js",
    "Nuxt.js",
    "Node.js",
    "Express",
    "MongoDB",
    "REST API Design",
    "AWS",
  ],
  languages: ["English", "Spanish"],
  education: [
    {
      id: 1,
      degree: "Master of Computer Science",
      institution: "Stanford University",
      year: "2017",
    },
    {
      id: 2,
      degree: "Bachelor of Science in Software Engineering",
      institution: "University of California, Berkeley",
      year: "2015",
    },
  ],
  certifications: [
    {
      id: 1,
      name: "AWS Certified Solutions Architect",
      issuer: "Amazon Web Services",
      year: "2022",
      expiration: "2025",
    },
    {
      id: 2,
      name: "Professional Scrum Master I (PSM I)",
      issuer: "Scrum.org",
      year: "2021",
    },
  ],
  socialLinks: {
    linkedin: "https://linkedin.com/in/sarahparker",
    twitter: "https://twitter.com/sarahparker",
    github: "https://github.com/sarahparker",
    website: "https://sarahparker.com",
  },
  notificationPreferences: {
    email: true,
    push: true,
    sms: false,
    newRequests: true,
    appointmentReminders: true,
    paymentReceipts: true,
    platformUpdates: false,
  },
});

// Form for password change
const passwordForm = ref({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

// Toggle password visibility
const togglePasswordVisibility = () => {
  passwordFieldType.value =
    passwordFieldType.value === "password" ? "text" : "password";
};

// Fetch profile data (simulated)
const fetchProfileData = async () => {
  try {
    isLoading.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));
    // Data already initialized in the profile ref
  } catch (error) {
    console.error("Error fetching profile data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Save profile data
const saveProfileData = async () => {
  try {
    isSaving.value = true;
    saveSuccess.value = false;
    saveError.value = false;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1200));

    // Simulate success
    saveSuccess.value = true;

    // Reset success message after 3 seconds
    setTimeout(() => {
      saveSuccess.value = false;
    }, 3000);
  } catch (error) {
    console.error("Error saving profile data:", error);
    saveError.value = true;
  } finally {
    isSaving.value = false;
  }
};

// Change password
const changePassword = async () => {
  try {
    // Validate password match
    if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
      saveError.value = true;
      return;
    }

    isSaving.value = true;
    saveSuccess.value = false;
    saveError.value = false;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Simulate success
    saveSuccess.value = true;

    // Reset form
    passwordForm.value = {
      currentPassword: "",
      newPassword: "",
      confirmPassword: "",
    };

    // Reset success message after 3 seconds
    setTimeout(() => {
      saveSuccess.value = false;
    }, 3000);
  } catch (error) {
    console.error("Error changing password:", error);
    saveError.value = true;
  } finally {
    isSaving.value = false;
  }
};

// Add education
const addEducation = () => {
  const newId =
    profile.value.education.length > 0
      ? Math.max(...profile.value.education.map((e) => e.id)) + 1
      : 1;

  profile.value.education.push({
    id: newId,
    degree: "",
    institution: "",
    year: "",
  });
};

// Remove education
const removeEducation = (id: number) => {
  profile.value.education = profile.value.education.filter((e) => e.id !== id);
};

// Add certification
const addCertification = () => {
  const newId =
    profile.value.certifications.length > 0
      ? Math.max(...profile.value.certifications.map((c) => c.id)) + 1
      : 1;

  profile.value.certifications.push({
    id: newId,
    name: "",
    issuer: "",
    year: "",
  });
};

// Remove certification
const removeCertification = (id: number) => {
  profile.value.certifications = profile.value.certifications.filter(
    (c) => c.id !== id
  );
};

// Add skill
const addSkill = (skill: string) => {
  if (skill && !profile.value.skills.includes(skill)) {
    profile.value.skills.push(skill);
  }
};

// Remove skill
const removeSkill = (skill: string) => {
  profile.value.skills = profile.value.skills.filter((s) => s !== skill);
};

// Toggle availability
const toggleAvailability = (availability: string) => {
  if (profile.value.availability.includes(availability)) {
    profile.value.availability = profile.value.availability.filter(
      (a) => a !== availability
    );
  } else {
    profile.value.availability.push(availability);
  }
};

// Delete account
const deleteAccount = async () => {
  try {
    if (deleteAccountConfirmation !== "DELETE") {
      return;
    }

    isSaving.value = true;

    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // Redirect to logout (in a real app)
    // window.location.href = '/logout';

    // For demo, just close the modal
    showDeleteAccountModal.value = false;
    deleteAccountReason.value = "";
    deleteAccountConfirmation.value = "";
  } catch (error) {
    console.error("Error deleting account:", error);
  } finally {
    isSaving.value = false;
  }
};

// Load data on component mount
onMounted(() => {
  fetchProfileData();
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Account Settings</h1>
      <p class="text-gray-600">
        Manage your profile, preferences, and account settings
      </p>
    </div>

    <!-- Settings Navigation and Content -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Tabs Navigation -->
      <div class="bg-gray-50 border-b border-gray-200 flex overflow-x-auto">
        <button
          @click="activeTab = 'profile'"
          class="px-6 py-4 text-sm font-medium whitespace-nowrap focus:outline-none"
          :class="
            activeTab === 'profile'
              ? 'text-indigo-600 border-b-2 border-indigo-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Profile Information
        </button>
        <button
          @click="activeTab = 'expertise'"
          class="px-6 py-4 text-sm font-medium whitespace-nowrap focus:outline-none"
          :class="
            activeTab === 'expertise'
              ? 'text-indigo-600 border-b-2 border-indigo-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Expertise & Skills
        </button>
        <button
          @click="activeTab = 'availability'"
          class="px-6 py-4 text-sm font-medium whitespace-nowrap focus:outline-none"
          :class="
            activeTab === 'availability'
              ? 'text-indigo-600 border-b-2 border-indigo-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Availability
        </button>
        <button
          @click="activeTab = 'notifications'"
          class="px-6 py-4 text-sm font-medium whitespace-nowrap focus:outline-none"
          :class="
            activeTab === 'notifications'
              ? 'text-indigo-600 border-b-2 border-indigo-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Notifications
        </button>
        <button
          @click="activeTab = 'security'"
          class="px-6 py-4 text-sm font-medium whitespace-nowrap focus:outline-none"
          :class="
            activeTab === 'security'
              ? 'text-indigo-600 border-b-2 border-indigo-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Security
        </button>
        <button
          @click="activeTab = 'billing'"
          class="px-6 py-4 text-sm font-medium whitespace-nowrap focus:outline-none"
          :class="
            activeTab === 'billing'
              ? 'text-indigo-600 border-b-2 border-indigo-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          "
        >
          Billing
        </button>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="p-6 animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-1/4 mb-6"></div>
        <div class="h-32 bg-gray-200 rounded mb-6"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
      </div>

      <!-- Content -->
      <div v-else class="p-6">
        <!-- Profile Information Tab -->
        <div v-if="activeTab === 'profile'" class="space-y-6">
          <h2
            class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2"
          >
            Profile Information
          </h2>

          <!-- Avatar Upload -->
          <div class="flex items-center space-x-6">
            <div class="relative">
              <img
                src="/images/instructors/i1.jpg"
                alt="Profile avatar"
                class="w-24 h-24 rounded-full object-cover border-2 border-gray-200"
              />
              <button
                class="absolute bottom-0 right-0 bg-indigo-600 text-white rounded-full p-1 shadow-lg"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                  />
                </svg>
              </button>
            </div>
            <div>
              <div class="text-sm text-gray-500 mb-1">
                Upload a new profile photo
              </div>
              <label
                class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                Choose File
                <input type="file" class="hidden" accept="image/*" />
              </label>
            </div>
          </div>

          <!-- Basic Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="name"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Full Name</label
              >
              <input
                type="text"
                id="name"
                v-model="profile.name"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="email"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Email Address</label
              >
              <input
                type="email"
                id="email"
                v-model="profile.email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="phone"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Phone Number</label
              >
              <input
                type="tel"
                id="phone"
                v-model="profile.phone"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="title"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Professional Title</label
              >
              <input
                type="text"
                id="title"
                v-model="profile.title"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>

          <div>
            <label
              for="bio"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Professional Bio</label
            >
            <textarea
              id="bio"
              v-model="profile.bio"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            ></textarea>
            <p class="mt-1 text-sm text-gray-500">
              Brief description about your professional experience and
              expertise.
            </p>
          </div>

          <!-- Social Links -->
          <div class="space-y-4">
            <h3 class="text-md font-medium text-gray-900">
              Social Media Links
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label
                  for="linkedin"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >LinkedIn</label
                >
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                      />
                    </svg>
                  </div>
                  <input
                    type="url"
                    id="linkedin"
                    v-model="profile.socialLinks.linkedin"
                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="https://linkedin.com/in/yourusername"
                  />
                </div>
              </div>

              <div>
                <label
                  for="twitter"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Twitter</label
                >
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                      />
                    </svg>
                  </div>
                  <input
                    type="url"
                    id="twitter"
                    v-model="profile.socialLinks.twitter"
                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="https://twitter.com/yourusername"
                  />
                </div>
              </div>

              <div>
                <label
                  for="github"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >GitHub</label
                >
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                      />
                    </svg>
                  </div>
                  <input
                    type="url"
                    id="github"
                    v-model="profile.socialLinks.github"
                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="https://github.com/yourusername"
                  />
                </div>
              </div>

              <div>
                <label
                  for="website"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Personal Website</label
                >
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                  <input
                    type="url"
                    id="website"
                    v-model="profile.socialLinks.website"
                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="https://yourwebsite.com"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="pt-5 border-t border-gray-200 flex justify-end">
            <button
              @click="saveProfileData"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="isSaving"
            >
              <svg
                v-if="isSaving"
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
              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>

          <!-- Success/Error Messages -->
          <div v-if="saveSuccess" class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  Your profile has been updated successfully!
                </p>
              </div>
            </div>
          </div>

          <div v-if="saveError" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-red-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                  There was an error saving your profile. Please try again.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Expertise & Skills Tab -->
        <div v-else-if="activeTab === 'expertise'" class="space-y-6">
          <h2
            class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2"
          >
            Expertise & Skills
          </h2>

          <!-- Specialization and Years of Experience -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="specialization"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Primary Specialization</label
              >
              <input
                type="text"
                id="specialization"
                v-model="profile.specialization"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., Full-Stack Development"
              />
            </div>

            <div>
              <label
                for="yearsExperience"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Years of Experience</label
              >
              <input
                type="number"
                id="yearsExperience"
                v-model="profile.yearsExperience"
                min="0"
                max="50"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label
                for="hourlyRate"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Hourly Rate (USD)</label
              >
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <span class="text-gray-500">$</span>
                </div>
                <input
                  type="number"
                  id="hourlyRate"
                  v-model="profile.hourlyRate"
                  min="0"
                  class="w-full pl-7 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
            </div>
          </div>

          <!-- Skills -->
          <div>
            <label
              for="skills"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Skills</label
            >
            <div class="mb-2">
              <div class="flex flex-wrap gap-2 mb-3">
                <div
                  v-for="skill in profile.skills"
                  :key="skill"
                  class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded flex items-center"
                >
                  {{ skill }}
                  <button
                    @click="removeSkill(skill)"
                    class="ml-1 text-indigo-600 hover:text-indigo-800 focus:outline-none"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3.5 w-3.5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="relative">
                <input
                  type="text"
                  id="skills"
                  placeholder="Add a skill and press Enter"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  @keydown.enter.prevent="
                    addSkill($event.target.value);
                    $event.target.value = '';
                  "
                />
              </div>

              <!-- Skills suggestions -->
              <div class="mt-2">
                <h4 class="text-xs font-medium text-gray-500 mb-1.5">
                  Suggested skills:
                </h4>
                <div class="flex flex-wrap gap-1.5">
                  <button
                    v-for="skill in skillsOptions.slice(0, 10)"
                    :key="skill"
                    @click="addSkill(skill)"
                    class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors duration-200"
                    :class="{
                      'opacity-50 cursor-not-allowed':
                        profile.skills.includes(skill),
                    }"
                    :disabled="profile.skills.includes(skill)"
                  >
                    {{ skill }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Languages -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Languages</label
            >
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
              <div v-for="language in languageOptions" :key="language">
                <label class="inline-flex items-center">
                  <input
                    type="checkbox"
                    :value="language"
                    v-model="profile.languages"
                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ language }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Education -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm font-medium text-gray-700"
                >Education</label
              >
              <button
                @click="addEducation"
                class="inline-flex items-center p-1.5 border border-transparent rounded-md text-xs font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                Add Education
              </button>
            </div>

            <div class="space-y-3">
              <div
                v-for="education in profile.education"
                :key="education.id"
                class="p-3 border border-gray-200 rounded-md bg-gray-50"
              >
                <div class="flex justify-between items-start mb-3">
                  <h4 class="text-sm font-medium text-gray-900">
                    {{ education.degree || "New Education" }}
                  </h4>
                  <button
                    @click="removeEducation(education.id)"
                    class="text-gray-400 hover:text-red-600"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                  <div class="md:col-span-3">
                    <label
                      :for="`degree-${education.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Degree</label
                    >
                    <input
                      :id="`degree-${education.id}`"
                      v-model="education.degree"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., Bachelor of Science"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label
                      :for="`institution-${education.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Institution</label
                    >
                    <input
                      :id="`institution-${education.id}`"
                      v-model="education.institution"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., Stanford University"
                    />
                  </div>

                  <div class="md:col-span-1">
                    <label
                      :for="`year-${education.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Year</label
                    >
                    <input
                      :id="`year-${education.id}`"
                      v-model="education.year"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., 2020"
                    />
                  </div>
                </div>
              </div>

              <div
                v-if="profile.education.length === 0"
                class="text-center py-4 text-sm text-gray-500"
              >
                No education details added yet. Click the "Add Education" button
                above to add your educational background.
              </div>
            </div>
          </div>

          <!-- Certifications -->
          <div>
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm font-medium text-gray-700"
                >Certifications</label
              >
              <button
                @click="addCertification"
                class="inline-flex items-center p-1.5 border border-transparent rounded-md text-xs font-medium text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                Add Certification
              </button>
            </div>

            <div class="space-y-3">
              <div
                v-for="certification in profile.certifications"
                :key="certification.id"
                class="p-3 border border-gray-200 rounded-md bg-gray-50"
              >
                <div class="flex justify-between items-start mb-3">
                  <h4 class="text-sm font-medium text-gray-900">
                    {{ certification.name || "New Certification" }}
                  </h4>
                  <button
                    @click="removeCertification(certification.id)"
                    class="text-gray-400 hover:text-red-600"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-8 gap-3">
                  <div class="md:col-span-3">
                    <label
                      :for="`cert-name-${certification.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Certification Name</label
                    >
                    <input
                      :id="`cert-name-${certification.id}`"
                      v-model="certification.name"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., AWS Certified Solutions Architect"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label
                      :for="`cert-issuer-${certification.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Issuing Organization</label
                    >
                    <input
                      :id="`cert-issuer-${certification.id}`"
                      v-model="certification.issuer"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., Amazon Web Services"
                    />
                  </div>

                  <div class="md:col-span-1.5">
                    <label
                      :for="`cert-year-${certification.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Year</label
                    >
                    <input
                      :id="`cert-year-${certification.id}`"
                      v-model="certification.year"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., 2022"
                    />
                  </div>

                  <div class="md:col-span-1.5">
                    <label
                      :for="`cert-exp-${certification.id}`"
                      class="block text-xs font-medium text-gray-700 mb-1"
                      >Expiration (Optional)</label
                    >
                    <input
                      :id="`cert-exp-${certification.id}`"
                      v-model="certification.expiration"
                      class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="e.g., 2025"
                    />
                  </div>
                </div>
              </div>

              <div
                v-if="profile.certifications.length === 0"
                class="text-center py-4 text-sm text-gray-500"
              >
                No certifications added yet. Click the "Add Certification"
                button above to add your professional certifications.
              </div>
            </div>
          </div>

          <div class="pt-5 border-t border-gray-200 flex justify-end">
            <button
              @click="saveProfileData"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="isSaving"
            >
              <svg
                v-if="isSaving"
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
              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>

          <!-- Success/Error Messages -->
          <div v-if="saveSuccess" class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  Your expertise and skills have been updated successfully!
                </p>
              </div>
            </div>
          </div>

          <div v-if="saveError" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-red-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                  There was an error saving your changes. Please try again.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Availability Tab -->
        <div v-else-if="activeTab === 'availability'" class="space-y-6">
          <h2
            class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2"
          >
            Availability Settings
          </h2>

          <!-- General Availability -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3"
              >General Availability</label
            >

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div v-for="option in availabilityOptions" :key="option">
                <div
                  @click="toggleAvailability(option)"
                  class="p-3 border rounded-lg cursor-pointer transition-colors duration-200"
                  :class="
                    profile.availability.includes(option)
                      ? 'bg-indigo-50 border-indigo-300'
                      : 'bg-white border-gray-300 hover:bg-gray-50'
                  "
                >
                  <div class="flex items-center">
                    <div
                      class="h-5 w-5 rounded-full border flex items-center justify-center mr-3"
                      :class="
                        profile.availability.includes(option)
                          ? 'border-indigo-500 bg-indigo-500'
                          : 'border-gray-300'
                      "
                    >
                      <svg
                        v-if="profile.availability.includes(option)"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3 text-white"
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
                    <span
                      class="text-sm font-medium"
                      :class="
                        profile.availability.includes(option)
                          ? 'text-indigo-700'
                          : 'text-gray-700'
                      "
                    >
                      {{ option }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Weekly Schedule (Placeholder) -->
          <div>
            <h3 class="text-md font-medium text-gray-900 mb-2">
              Weekly Schedule
            </h3>
            <p class="text-sm text-gray-500 mb-4">
              Set your available hours for each day of the week.
            </p>

            <div class="space-y-4">
              <div
                v-for="day in [
                  'Monday',
                  'Tuesday',
                  'Wednesday',
                  'Thursday',
                  'Friday',
                  'Saturday',
                  'Sunday',
                ]"
                :key="day"
                class="flex items-center p-3 border border-gray-200 rounded-lg"
              >
                <div class="w-24 font-medium text-gray-700">{{ day }}</div>

                <div class="flex items-center">
                  <label class="block text-sm text-gray-600 mr-3">Start:</label>
                  <select
                    class="mr-5 w-24 p-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option>9:00 AM</option>
                    <option>10:00 AM</option>
                    <option>11:00 AM</option>
                    <option>12:00 PM</option>
                    <option>1:00 PM</option>
                    <option>Not Available</option>
                  </select>

                  <label class="block text-sm text-gray-600 mr-3">End:</label>
                  <select
                    class="w-24 p-1.5 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option>5:00 PM</option>
                    <option>6:00 PM</option>
                    <option>7:00 PM</option>
                    <option>8:00 PM</option>
                    <option>9:00 PM</option>
                    <option>Not Available</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Time zone setting -->
          <div>
            <label
              for="timezone"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Your Time Zone</label
            >
            <select
              id="timezone"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="UTC-8">
                (UTC-8:00) Pacific Time (US & Canada)
              </option>
              <option value="UTC-7">
                (UTC-7:00) Mountain Time (US & Canada)
              </option>
              <option value="UTC-6">
                (UTC-6:00) Central Time (US & Canada)
              </option>
              <option value="UTC-5">
                (UTC-5:00) Eastern Time (US & Canada)
              </option>
              <option value="UTC-4">(UTC-4:00) Atlantic Time (Canada)</option>
              <option value="UTC+0">(UTC+0:00) London, Edinburgh</option>
              <option value="UTC+1">(UTC+1:00) Berlin, Paris, Rome</option>
              <option value="UTC+2">
                (UTC+2:00) Athens, Istanbul, Helsinki
              </option>
              <option value="UTC+3">(UTC+3:00) Moscow, Baghdad</option>
              <option value="UTC+5:30">(UTC+5:30) New Delhi, Mumbai</option>
              <option value="UTC+8">
                (UTC+8:00) Beijing, Hong Kong, Singapore
              </option>
              <option value="UTC+9">(UTC+9:00) Tokyo, Seoul</option>
              <option value="UTC+10">(UTC+10:00) Sydney, Melbourne</option>
              <option value="UTC+12">(UTC+12:00) Auckland</option>
            </select>
          </div>

          <!-- Buffer time setting -->
          <div>
            <label
              for="bufferTime"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Buffer Time Between Appointments</label
            >
            <select
              id="bufferTime"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="0">No buffer</option>
              <option value="15">15 minutes</option>
              <option value="30">30 minutes</option>
              <option value="45">45 minutes</option>
              <option value="60">1 hour</option>
            </select>
          </div>

          <!-- Appointment duration presets -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1"
              >Default Appointment Durations</label
            >
            <p class="text-sm text-gray-500 mb-3">
              Select the appointment durations you'd like to offer clients.
            </p>

            <div class="flex flex-wrap gap-3">
              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-15"
                  class="peer absolute h-0 w-0 opacity-0"
                  checked
                />
                <label
                  for="duration-15"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">15 minutes</span>
                </label>
              </div>

              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-30"
                  class="peer absolute h-0 w-0 opacity-0"
                  checked
                />
                <label
                  for="duration-30"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">30 minutes</span>
                </label>
              </div>

              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-45"
                  class="peer absolute h-0 w-0 opacity-0"
                />
                <label
                  for="duration-45"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">45 minutes</span>
                </label>
              </div>

              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-60"
                  class="peer absolute h-0 w-0 opacity-0"
                  checked
                />
                <label
                  for="duration-60"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">1 hour</span>
                </label>
              </div>

              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-90"
                  class="peer absolute h-0 w-0 opacity-0"
                  checked
                />
                <label
                  for="duration-90"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">1.5 hours</span>
                </label>
              </div>

              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-120"
                  class="peer absolute h-0 w-0 opacity-0"
                />
                <label
                  for="duration-120"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">2 hours</span>
                </label>
              </div>

              <div class="relative inline-block">
                <input
                  type="checkbox"
                  id="duration-custom"
                  class="peer absolute h-0 w-0 opacity-0"
                />
                <label
                  for="duration-custom"
                  class="inline-flex items-center px-3 py-2 border rounded-md cursor-pointer transition-colors duration-200 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 border-gray-300 hover:bg-gray-50"
                >
                  <span class="text-sm font-medium">Custom</span>
                </label>
              </div>
            </div>
          </div>

          <div class="pt-5 border-t border-gray-200 flex justify-end">
            <button
              @click="saveProfileData"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="isSaving"
            >
              <svg
                v-if="isSaving"
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
              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>

          <!-- Success/Error Messages -->
          <div v-if="saveSuccess" class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  Your availability settings have been updated successfully!
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications Tab -->
        <div v-else-if="activeTab === 'notifications'" class="space-y-6">
          <h2
            class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2"
          >
            Notification Preferences
          </h2>

          <!-- Notification channels -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3"
              >Notification Channels</label
            >

            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-3"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                    />
                    <path
                      d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                    />
                  </svg>
                  <div>
                    <span class="text-sm font-medium text-gray-900"
                      >Email Notifications</span
                    >
                    <p class="text-xs text-gray-500">
                      Receive notifications via email
                    </p>
                  </div>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="profile.notificationPreferences.email"
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-3"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                    />
                  </svg>
                  <div>
                    <span class="text-sm font-medium text-gray-900"
                      >Push Notifications</span
                    >
                    <p class="text-xs text-gray-500">
                      Receive notifications in your browser
                    </p>
                  </div>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="profile.notificationPreferences.push"
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-3"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                    />
                  </svg>
                  <div>
                    <span class="text-sm font-medium text-gray-900"
                      >SMS Notifications</span
                    >
                    <p class="text-xs text-gray-500">
                      Receive notifications via text message
                    </p>
                  </div>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="profile.notificationPreferences.sms"
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>
            </div>
          </div>

          <!-- Notification types -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3"
              >Notification Types</label
            >

            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-gray-900"
                    >New Client Requests</span
                  >
                  <p class="text-xs text-gray-500">
                    When a new client requests your services
                  </p>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="profile.notificationPreferences.newRequests"
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-gray-900"
                    >Appointment Reminders</span
                  >
                  <p class="text-xs text-gray-500">
                    Reminders for upcoming appointments
                  </p>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="
                      profile.notificationPreferences.appointmentReminders
                    "
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-gray-900"
                    >Payment Receipts</span
                  >
                  <p class="text-xs text-gray-500">
                    When you receive payments for consultations
                  </p>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="profile.notificationPreferences.paymentReceipts"
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>

              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-gray-900"
                    >Platform Updates</span
                  >
                  <p class="text-xs text-gray-500">
                    News and updates about the platform
                  </p>
                </div>
                <label class="relative inline-flex items-center">
                  <input
                    type="checkbox"
                    v-model="profile.notificationPreferences.platformUpdates"
                    class="sr-only peer"
                  />
                  <div
                    class="h-6 w-11 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>
            </div>
          </div>

          <div class="pt-5 border-t border-gray-200 flex justify-end">
            <button
              @click="saveProfileData"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="isSaving"
            >
              <svg
                v-if="isSaving"
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
              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>

          <!-- Success message -->
          <div v-if="saveSuccess" class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  Your notification preferences have been updated successfully!
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Security Tab -->
        <div v-else-if="activeTab === 'security'" class="space-y-6">
          <h2
            class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2"
          >
            Security Settings
          </h2>

          <!-- Change Password -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-md font-medium text-gray-900 mb-4">
              Change Password
            </h3>

            <div class="space-y-4">
              <div>
                <label
                  for="current-password"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Current Password</label
                >
                <div class="relative">
                  <input
                    :type="passwordFieldType"
                    id="current-password"
                    v-model="passwordForm.currentPassword"
                    class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter your current password"
                  />
                  <button
                    type="button"
                    @click="togglePasswordVisibility"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <svg
                      v-if="passwordFieldType === 'password'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
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
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                        clip-rule="evenodd"
                      />
                      <path
                        d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                      />
                    </svg>
                  </button>
                </div>
              </div>

              <div>
                <label
                  for="new-password"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >New Password</label
                >
                <div class="relative">
                  <input
                    :type="passwordFieldType"
                    id="new-password"
                    v-model="passwordForm.newPassword"
                    class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Enter a new password"
                  />
                  <button
                    type="button"
                    @click="togglePasswordVisibility"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <svg
                      v-if="passwordFieldType === 'password'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
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
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                        clip-rule="evenodd"
                      />
                      <path
                        d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                      />
                    </svg>
                  </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                  Password must be at least 8 characters and include a number, a
                  lowercase letter, an uppercase letter, and a special
                  character.
                </p>
              </div>

              <div>
                <label
                  for="confirm-password"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Confirm New Password</label
                >
                <div class="relative">
                  <input
                    :type="passwordFieldType"
                    id="confirm-password"
                    v-model="passwordForm.confirmPassword"
                    class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Confirm your new password"
                  />
                  <button
                    type="button"
                    @click="togglePasswordVisibility"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <svg
                      v-if="passwordFieldType === 'password'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
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
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                        clip-rule="evenodd"
                      />
                      <path
                        d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                      />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Password match error -->
              <div
                v-if="
                  passwordForm.newPassword &&
                  passwordForm.confirmPassword &&
                  passwordForm.newPassword !== passwordForm.confirmPassword
                "
                class="text-sm text-red-600"
              >
                Passwords do not match
              </div>

              <div class="flex justify-end">
                <button
                  @click="changePassword"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="
                    isSaving ||
                    !passwordForm.currentPassword ||
                    !passwordForm.newPassword ||
                    !passwordForm.confirmPassword ||
                    passwordForm.newPassword !== passwordForm.confirmPassword
                  "
                >
                  <svg
                    v-if="isSaving"
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
                  {{ isSaving ? "Updating..." : "Update Password" }}
                </button>
              </div>
            </div>
          </div>

          <!-- Two-Factor Authentication -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-md font-medium text-gray-900 mb-1">
                  Two-Factor Authentication
                </h3>
                <p class="text-sm text-gray-500">
                  Add an extra layer of security to your account by enabling
                  two-factor authentication.
                </p>
              </div>
              <button
                class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Enable
              </button>
            </div>
          </div>

          <!-- Login Sessions -->
          <div>
            <h3 class="text-md font-medium text-gray-900 mb-3">
              Active Sessions
            </h3>

            <div class="space-y-3">
              <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                  <div class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-green-500 mr-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <div>
                      <div class="text-sm font-medium">Current Session</div>
                      <div class="text-xs text-gray-500">
                        Last active: Just now
                      </div>
                    </div>
                  </div>
                  <div class="text-xs text-gray-500">
                    <span class="font-medium">MacBook Pro</span> • San
                    Francisco, CA
                  </div>
                </div>
              </div>

              <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                  <div class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400 mr-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <div>
                      <div class="text-sm font-medium">iPhone 13 Pro</div>
                      <div class="text-xs text-gray-500">
                        Last active: 2 days ago
                      </div>
                    </div>
                  </div>
                  <button class="text-sm text-red-600 hover:text-red-800">
                    Logout
                  </button>
                </div>
              </div>

              <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                  <div class="flex items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-400 mr-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <div>
                      <div class="text-sm font-medium">Chrome - Windows</div>
                      <div class="text-xs text-gray-500">
                        Last active: 5 days ago
                      </div>
                    </div>
                  </div>
                  <button class="text-sm text-red-600 hover:text-red-800">
                    Logout
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <button
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
              >
                Logout of all sessions
              </button>
            </div>
          </div>

          <!-- Account Deletion -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-red-600 mb-3">
              Delete Account
            </h3>
            <p class="text-sm text-gray-500 mb-4">
              Permanently delete your account and all of your content.
            </p>

            <button
              @click="showDeleteAccountModal = true"
              class="inline-flex items-center px-4 py-2 border border-red-600 rounded-md shadow-sm text-sm font-medium text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Delete Account
            </button>
          </div>

          <!-- Success message -->
          <div v-if="saveSuccess" class="rounded-md bg-green-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-green-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  Your password has been updated successfully!
                </p>
              </div>
            </div>
          </div>

          <!-- Error message -->
          <div v-if="saveError" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg
                  class="h-5 w-5 text-red-400"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                  There was an error updating your password. Please make sure
                  your current password is correct.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Billing Tab -->
        <div v-else-if="activeTab === 'billing'" class="space-y-6">
          <h2
            class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2"
          >
            Billing & Payments
          </h2>

          <!-- Current Plan -->
          <div
            class="bg-white border border-gray-200 rounded-lg overflow-hidden"
          >
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
              <h3 class="text-md font-medium text-gray-900">Current Plan</h3>
            </div>

            <div class="p-6">
              <div class="flex justify-between items-center">
                <div>
                  <div class="flex items-center">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-4">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-indigo-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div>
                      <h4 class="text-lg font-medium text-gray-900">
                        Professional Plan
                      </h4>
                      <p class="text-sm text-gray-500">Billed annually</p>
                    </div>
                  </div>
                </div>

                <div class="text-right">
                  <div class="text-2xl font-bold text-gray-900">
                    $39<span class="text-sm font-normal text-gray-500"
                      >/month</span
                    >
                  </div>
                  <p class="text-sm text-gray-500">
                    Next billing date: May 16, 2025
                  </p>
                </div>
              </div>

              <div class="mt-6 border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">
                  Plan includes:
                </h4>

                <ul class="space-y-3">
                  <li class="flex items-start">
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
                    <span class="text-sm text-gray-700"
                      >Unlimited client consultations</span
                    >
                  </li>
                  <li class="flex items-start">
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
                    <span class="text-sm text-gray-700"
                      >Advanced scheduling features</span
                    >
                  </li>
                  <li class="flex items-start">
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
                    <span class="text-sm text-gray-700"
                      >Custom branding options</span
                    >
                  </li>
                  <li class="flex items-start">
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
                    <span class="text-sm text-gray-700"
                      >Priority customer support</span
                    >
                  </li>
                  <li class="flex items-start">
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
                    <span class="text-sm text-gray-700"
                      >Comprehensive analytics</span
                    >
                  </li>
                </ul>

                <div class="flex mt-6 space-x-4">
                  <button
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Change Plan
                  </button>
                  <button
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                  >
                    Cancel Plan
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div>
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-md font-medium text-gray-900">Payment Methods</h3>
              <button
                class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                Add Payment Method
              </button>
            </div>

            <div
              class="bg-white border border-gray-200 rounded-lg overflow-hidden"
            >
              <div class="divide-y divide-gray-200">
                <div class="p-4 flex justify-between items-center">
                  <div class="flex items-center">
                    <svg
                      class="h-8 w-8 text-blue-600 mr-3"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M3.5 0A1.5 1.5 0 002 1.5v3A1.5 1.5 0 003.5 6h15A1.5 1.5 0 0020 4.5v-3A1.5 1.5 0 0018.5 0h-15z"
                        fill="#2196F3"
                      />
                      <path
                        d="M3.5 8A1.5 1.5 0 002 9.5v3A1.5 1.5 0 003.5 14h15a1.5 1.5 0 001.5-1.5v-3A1.5 1.5 0 0018.5 8h-15z"
                        fill="#1976D2"
                      />
                      <path
                        d="M3.5 16A1.5 1.5 0 002 17.5v3A1.5 1.5 0 003.5 22h15a1.5 1.5 0 001.5-1.5v-3a1.5 1.5 0 00-1.5-1.5h-15z"
                        fill="#0D47A1"
                      />
                    </svg>
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        Visa ending in 4242
                      </div>
                      <div class="text-xs text-gray-500">Expires 12/2025</div>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      class="text-sm text-indigo-600 hover:text-indigo-800"
                    >
                      Edit
                    </button>
                    <button class="text-sm text-red-600 hover:text-red-800">
                      Remove
                    </button>
                  </div>
                </div>

                <div class="p-4 flex justify-between items-center">
                  <div class="flex items-center">
                    <svg
                      class="h-8 w-8 text-red-600 mr-3"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path d="M7 17h10v2H7v-2z" fill="#FF5722" />
                      <path d="M7 12h10v2H7v-2z" fill="#FF5722" />
                      <path d="M7 7h10v2H7V7z" fill="#FF5722" />
                      <path
                        d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2z"
                        fill="none"
                        stroke="#FF5722"
                        stroke-width="2"
                      />
                    </svg>
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        Mastercard ending in 8377
                      </div>
                      <div class="text-xs text-gray-500">Expires 08/2026</div>
                    </div>
                  </div>
                  <div class="flex space-x-2">
                    <button
                      class="text-sm text-indigo-600 hover:text-indigo-800"
                    >
                      Edit
                    </button>
                    <button class="text-sm text-red-600 hover:text-red-800">
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Billing History -->
          <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">
              Billing History
            </h3>

            <div
              class="bg-white border border-gray-200 rounded-lg overflow-hidden"
            >
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Date
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Description
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Amount
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th
                      scope="col"
                      class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Invoice
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      Apr 16, 2025
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      Professional Plan - Annual Subscription
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      $468.00
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                        >Paid</span
                      >
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                      <a href="#" class="text-indigo-600 hover:text-indigo-900"
                        >Download</a
                      >
                    </td>
                  </tr>
                  <tr>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      Mar 16, 2025
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      Professional Plan - Monthly Subscription
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      $39.00
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                        >Paid</span
                      >
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                      <a href="#" class="text-indigo-600 hover:text-indigo-900"
                        >Download</a
                      >
                    </td>
                  </tr>
                  <tr>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      Feb 16, 2025
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      Professional Plan - Monthly Subscription
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                      $39.00
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                        >Paid</span
                      >
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                      <a href="#" class="text-indigo-600 hover:text-indigo-900"
                        >Download</a
                      >
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-4 text-right">
              <a
                href="#"
                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >View All Payment History</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Account Modal -->
    <div
      v-if="showDeleteAccountModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
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
        ></div>

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
              >
                <svg
                  class="h-6 w-6 text-red-600"
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
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3
                  class="text-lg leading-6 font-medium text-gray-900"
                  id="modal-title"
                >
                  Delete Account
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete your account? All of your
                    data will be permanently removed from our servers forever.
                    This action cannot be undone.
                  </p>

                  <div class="mt-4">
                    <label
                      for="delete-reason"
                      class="block text-sm font-medium text-gray-700 mb-1"
                    >
                      Please tell us why you're deleting your account (optional)
                    </label>
                    <textarea
                      id="delete-reason"
                      v-model="deleteAccountReason"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                      placeholder="Your feedback helps us improve our service"
                    ></textarea>
                  </div>

                  <div class="mt-4">
                    <label
                      for="delete-confirmation"
                      class="block text-sm font-medium text-gray-700 mb-1"
                    >
                      Type <span class="font-semibold">DELETE</span> to confirm
                    </label>
                    <input
                      id="delete-confirmation"
                      v-model="deleteAccountConfirmation"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                      placeholder="DELETE"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="deleteAccount"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="deleteAccountConfirmation !== 'DELETE' || isSaving"
              :class="{
                'opacity-50 cursor-not-allowed':
                  deleteAccountConfirmation !== 'DELETE' || isSaving,
              }"
            >
              <svg
                v-if="isSaving"
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
              {{ isSaving ? "Deleting..." : "Delete Account" }}
            </button>
            <button
              @click="showDeleteAccountModal = false"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="isSaving"
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
/* Animate transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Loading animation */
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

/* Spinner animation */
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

/* Staggered animation for list items */
.delay-75 {
  transition-delay: 75ms;
}
.delay-150 {
  transition-delay: 150ms;
}
.delay-225 {
  transition-delay: 225ms;
}
.delay-300 {
  transition-delay: 300ms;
}

/* Custom focus styles for toggles */
.peer:focus ~ .peer-focus\:ring-4 {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0
    var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0
    calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow),
    var(--tw-shadow, 0 0 #0000);
}
</style>
