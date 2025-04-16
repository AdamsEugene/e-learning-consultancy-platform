<!-- pages/dashboard/instructor/settings.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page meta with dashboard-instructor layout
definePageMeta({
  layout: "dashboard-instructor",
});

// Page meta
useHead({
  title: "Account Settings - Instructor Dashboard",
});

// Define types
interface UserProfile {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  bio: string;
  website: string;
  socialLinks: {
    twitter: string;
    linkedin: string;
    youtube: string;
    facebook: string;
  };
  jobTitle: string;
  specialization: string;
  profileImage: string;
  coverImage: string;
  expertise: string[];
  languagesSpoken: string[];
}

interface NotificationSettings {
  emailNotifications: {
    courseEnrollments: boolean;
    courseReviews: boolean;
    courseQuestions: boolean;
    messages: boolean;
    promotions: boolean;
    accountUpdates: boolean;
    newsletter: boolean;
  };
  pushNotifications: {
    courseEnrollments: boolean;
    courseReviews: boolean;
    courseQuestions: boolean;
    messages: boolean;
    promotions: boolean;
  };
}

interface PaymentSettings {
  paymentMethod: "paypal" | "bank" | "stripe";
  paypalEmail: string;
  bankInfo: {
    accountName: string;
    accountNumber: string;
    bankName: string;
    routingNumber: string;
    swiftCode: string;
  };
  taxInfo: {
    country: string;
    taxIdType: string;
    taxIdNumber: string;
    addressLine1: string;
    addressLine2: string;
    city: string;
    state: string;
    zipCode: string;
  };
  paymentThreshold: number;
}

interface PrivacySettings {
  profileVisibility: "public" | "students" | "private";
  showCourseAnalytics: boolean;
  showRevenue: boolean;
  shareActivity: boolean;
}

interface AccountSettings {
  email: string;
  password: string;
  twoFactorEnabled: boolean;
  language: string;
  timezone: string;
}

// State
const activeTab = ref("profile");
const profileForm = ref<UserProfile>({
  id: "12345",
  firstName: "Alex",
  lastName: "Thompson",
  email: "alex.thompson@example.com",
  phone: "+1 (555) 123-4567",
  bio: "Senior web development instructor with over 10 years of industry experience. Specialized in full-stack JavaScript development and modern web frameworks.",
  website: "https://alexthompson.dev",
  socialLinks: {
    twitter: "https://twitter.com/alexthompsondev",
    linkedin: "https://linkedin.com/in/alexthompsondev",
    youtube: "https://youtube.com/c/alexthompsondev",
    facebook: "",
  },
  jobTitle: "Senior Web Development Instructor",
  specialization: "Full-Stack JavaScript Development",
  profileImage: "/api/placeholder/200/200",
  coverImage: "/api/placeholder/1200/300",
  expertise: [
    "JavaScript",
    "React",
    "Node.js",
    "TypeScript",
    "Vue.js",
    "MongoDB",
    "Web Development",
    "API Design",
  ],
  languagesSpoken: ["English", "Spanish"],
});

const notificationSettings = ref<NotificationSettings>({
  emailNotifications: {
    courseEnrollments: true,
    courseReviews: true,
    courseQuestions: true,
    messages: true,
    promotions: false,
    accountUpdates: true,
    newsletter: false,
  },
  pushNotifications: {
    courseEnrollments: true,
    courseReviews: true,
    courseQuestions: true,
    messages: true,
    promotions: false,
  },
});

const paymentSettings = ref<PaymentSettings>({
  paymentMethod: "paypal",
  paypalEmail: "alex.thompson@example.com",
  bankInfo: {
    accountName: "Alex Thompson",
    accountNumber: "XXXX-XXXX-XXXX-4567",
    bankName: "First National Bank",
    routingNumber: "XXX-XXX-XXX",
    swiftCode: "FNBUS12",
  },
  taxInfo: {
    country: "United States",
    taxIdType: "SSN",
    taxIdNumber: "XXX-XX-XXXX",
    addressLine1: "123 Main Street",
    addressLine2: "Apt 4B",
    city: "San Francisco",
    state: "CA",
    zipCode: "94105",
  },
  paymentThreshold: 50,
});

const privacySettings = ref<PrivacySettings>({
  profileVisibility: "public",
  showCourseAnalytics: true,
  showRevenue: false,
  shareActivity: true,
});

const accountSettings = ref<AccountSettings>({
  email: "alex.thompson@example.com",
  password: "••••••••••••",
  twoFactorEnabled: true,
  language: "en",
  timezone: "America/Los_Angeles",
});

// Validation states
const isProfileSaving = ref(false);
const isNotificationSaving = ref(false);
const isPaymentSaving = ref(false);
const isPrivacySaving = ref(false);
const isAccountSaving = ref(false);
const isDeactivating = ref(false);
const showDeactivateModal = ref(false);
const deactivateConfirmation = ref("");
const successMessage = ref("");
const errorMessage = ref("");

// Available timezones
const availableTimezones = [
  { value: "America/Los_Angeles", label: "Pacific Time (US & Canada)" },
  { value: "America/Denver", label: "Mountain Time (US & Canada)" },
  { value: "America/Chicago", label: "Central Time (US & Canada)" },
  { value: "America/New_York", label: "Eastern Time (US & Canada)" },
  { value: "Europe/London", label: "London" },
  { value: "Europe/Paris", label: "Paris" },
  { value: "Asia/Tokyo", label: "Tokyo" },
  { value: "Australia/Sydney", label: "Sydney" },
];

// Available languages
const availableLanguages = [
  { value: "en", label: "English" },
  { value: "es", label: "Spanish" },
  { value: "fr", label: "French" },
  { value: "de", label: "German" },
  { value: "zh", label: "Chinese" },
  { value: "ja", label: "Japanese" },
  { value: "pt", label: "Portuguese" },
  { value: "ru", label: "Russian" },
];

// Available expertise areas
const availableExpertise = [
  "JavaScript",
  "Python",
  "Java",
  "C#",
  "PHP",
  "HTML/CSS",
  "React",
  "Angular",
  "Vue.js",
  "Node.js",
  "Express",
  "Django",
  "Ruby on Rails",
  "Flutter",
  "Swift",
  "Kotlin",
  "Android",
  "iOS",
  "UI/UX Design",
  "Database Design",
  "SQL",
  "NoSQL",
  "MongoDB",
  "PostgreSQL",
  "MySQL",
  "Cloud Computing",
  "AWS",
  "Azure",
  "Google Cloud",
  "DevOps",
  "Docker",
  "Kubernetes",
  "CI/CD",
  "Machine Learning",
  "Data Science",
  "Web Development",
  "Mobile Development",
  "Game Development",
  "Blockchain",
  "Cybersecurity",
  "API Design",
  "GraphQL",
  "TypeScript",
];

// Available languages spoken
const availableLanguagesSpoken = [
  "English",
  "Spanish",
  "French",
  "German",
  "Chinese (Mandarin)",
  "Japanese",
  "Portuguese",
  "Russian",
  "Arabic",
  "Hindi",
  "Italian",
  "Korean",
  "Dutch",
  "Turkish",
  "Polish",
  "Swedish",
];

// Methods
const updateTab = (tab: string) => {
  activeTab.value = tab;
};

const saveProfile = async () => {
  try {
    isProfileSaving.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    successMessage.value = "Profile information updated successfully!";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error) {
    console.error("Error saving profile:", error);
    errorMessage.value =
      "Failed to save profile information. Please try again.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  } finally {
    isProfileSaving.value = false;
  }
};

const saveNotificationSettings = async () => {
  try {
    isNotificationSaving.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    successMessage.value = "Notification preferences updated successfully!";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error) {
    console.error("Error saving notification settings:", error);
    errorMessage.value =
      "Failed to save notification preferences. Please try again.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  } finally {
    isNotificationSaving.value = false;
  }
};

const savePaymentSettings = async () => {
  try {
    isPaymentSaving.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    successMessage.value = "Payment settings updated successfully!";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error) {
    console.error("Error saving payment settings:", error);
    errorMessage.value = "Failed to save payment settings. Please try again.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  } finally {
    isPaymentSaving.value = false;
  }
};

const savePrivacySettings = async () => {
  try {
    isPrivacySaving.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    successMessage.value = "Privacy settings updated successfully!";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error) {
    console.error("Error saving privacy settings:", error);
    errorMessage.value = "Failed to save privacy settings. Please try again.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  } finally {
    isPrivacySaving.value = false;
  }
};

const saveAccountSettings = async () => {
  try {
    isAccountSaving.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    successMessage.value = "Account settings updated successfully!";
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (error) {
    console.error("Error saving account settings:", error);
    errorMessage.value = "Failed to save account settings. Please try again.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  } finally {
    isAccountSaving.value = false;
  }
};

const deactivateAccount = async () => {
  try {
    if (deactivateConfirmation.value !== "DEACTIVATE") {
      return;
    }

    isDeactivating.value = true;
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1500));

    // This would normally redirect to a logout or confirmation page
    showDeactivateModal.value = false;
    successMessage.value =
      "Account deactivation initiated. You will be logged out.";
  } catch (error) {
    console.error("Error deactivating account:", error);
    errorMessage.value = "Failed to deactivate account. Please try again.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  } finally {
    isDeactivating.value = false;
  }
};

// Helper function to add expertise tag
const addExpertise = (tag: string) => {
  if (!profileForm.value.expertise.includes(tag)) {
    profileForm.value.expertise.push(tag);
  }
};

// Helper function to remove expertise tag
const removeExpertise = (tag: string) => {
  profileForm.value.expertise = profileForm.value.expertise.filter(
    (t) => t !== tag
  );
};

// Helper function to add spoken language
const addLanguageSpoken = (language: string) => {
  if (!profileForm.value.languagesSpoken.includes(language)) {
    profileForm.value.languagesSpoken.push(language);
  }
};

// Helper function to remove spoken language
const removeLanguageSpoken = (language: string) => {
  profileForm.value.languagesSpoken = profileForm.value.languagesSpoken.filter(
    (l) => l !== language
  );
};

// Load user data
onMounted(() => {
  // In a real app, this would fetch the data from an API
  // For now, we're using pre-defined data
});
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 mb-1">Account Settings</h1>
      <p class="text-gray-600">
        Manage your profile, notifications, payments, and account settings
      </p>

      <!-- Success and error messages -->
      <div
        v-if="successMessage"
        class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <span>{{ successMessage }}</span>
      </div>

      <div
        v-if="errorMessage"
        class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>
    </div>

    <!-- Settings Layout -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Settings Navigation -->
      <div class="md:col-span-1">
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <div class="p-4 border-b border-gray-200">
            <h2 class="font-medium text-gray-800">Settings</h2>
          </div>
          <nav class="space-y-1 p-2">
            <button
              @click="updateTab('profile')"
              class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200"
              :class="
                activeTab === 'profile'
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-3"
                :class="
                  activeTab === 'profile' ? 'text-indigo-500' : 'text-gray-400'
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                  clip-rule="evenodd"
                />
              </svg>
              Profile Information
            </button>
            <button
              @click="updateTab('notifications')"
              class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200"
              :class="
                activeTab === 'notifications'
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-3"
                :class="
                  activeTab === 'notifications'
                    ? 'text-indigo-500'
                    : 'text-gray-400'
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                />
              </svg>
              Notification Preferences
            </button>
            <button
              @click="updateTab('payment')"
              class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200"
              :class="
                activeTab === 'payment'
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-3"
                :class="
                  activeTab === 'payment' ? 'text-indigo-500' : 'text-gray-400'
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path
                  fill-rule="evenodd"
                  d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                  clip-rule="evenodd"
                />
              </svg>
              Payment Settings
            </button>
            <button
              @click="updateTab('privacy')"
              class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200"
              :class="
                activeTab === 'privacy'
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-3"
                :class="
                  activeTab === 'privacy' ? 'text-indigo-500' : 'text-gray-400'
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                  clip-rule="evenodd"
                />
              </svg>
              Privacy Settings
            </button>
            <button
              @click="updateTab('account')"
              class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200"
              :class="
                activeTab === 'account'
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-3"
                :class="
                  activeTab === 'account' ? 'text-indigo-500' : 'text-gray-400'
                "
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                  clip-rule="evenodd"
                />
              </svg>
              Account Settings
            </button>
          </nav>
        </div>
      </div>

      <!-- Settings Content -->
      <div class="md:col-span-3">
        <div
          class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        >
          <!-- Profile Information -->
          <div v-if="activeTab === 'profile'" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Profile Information
            </h2>
            <p class="text-gray-600 mb-6">
              Update your instructor profile information to show students who
              you are and what you teach.
            </p>

            <form @submit.prevent="saveProfile">
              <!-- Profile & Cover Images -->
              <div class="mb-8">
                <div class="relative mb-6">
                  <div class="h-40 rounded-lg bg-gray-200 overflow-hidden">
                    <img
                      :src="profileForm.coverImage"
                      alt="Cover image"
                      class="w-full h-full object-cover"
                    />
                    <button
                      type="button"
                      class="absolute bottom-2 right-2 bg-white text-gray-700 p-2 rounded-full shadow hover:bg-gray-100 transition-colors"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                  </div>
                  <div
                    class="absolute left-6 bottom-0 transform translate-y-1/2"
                  >
                    <div class="relative">
                      <div
                        class="w-24 h-24 border-4 border-white rounded-full bg-gray-200 overflow-hidden"
                      >
                        <img
                          :src="profileForm.profileImage"
                          alt="Profile picture"
                          class="w-full h-full object-cover"
                        />
                      </div>
                      <button
                        type="button"
                        class="absolute bottom-0 right-0 bg-white text-gray-700 p-1.5 rounded-full shadow hover:bg-gray-100 transition-colors"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-4 w-4"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Basic Info -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label
                    for="firstName"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >First Name</label
                  >
                  <input
                    id="firstName"
                    v-model="profileForm.firstName"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label
                    for="lastName"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Last Name</label
                  >
                  <input
                    id="lastName"
                    v-model="profileForm.lastName"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label
                    for="email"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Email</label
                  >
                  <input
                    id="email"
                    v-model="profileForm.email"
                    type="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label
                    for="phone"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Phone</label
                  >
                  <input
                    id="phone"
                    v-model="profileForm.phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label
                    for="jobTitle"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Job Title</label
                  >
                  <input
                    id="jobTitle"
                    v-model="profileForm.jobTitle"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
                <div>
                  <label
                    for="specialization"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Specialization</label
                  >
                  <input
                    id="specialization"
                    v-model="profileForm.specialization"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <!-- Bio -->
              <div class="mb-6">
                <label
                  for="bio"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Biography</label
                >
                <textarea
                  id="bio"
                  v-model="profileForm.bio"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Tell students about yourself, your background, experience, and teaching style."
                ></textarea>
                <p class="mt-1 text-sm text-gray-500">
                  Write a compelling bio to attract students to your courses.
                </p>
              </div>

              <!-- Website -->
              <div class="mb-6">
                <label
                  for="website"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Website</label
                >
                <input
                  id="website"
                  v-model="profileForm.website"
                  type="url"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="https://yourwebsite.com"
                />
              </div>

              <!-- Social Links -->
              <div class="mb-8">
                <h3 class="text-sm font-medium text-gray-700 mb-3">
                  Social Media Profiles
                </h3>
                <div class="space-y-4">
                  <div class="flex items-center">
                    <div
                      class="w-10 h-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center mr-3"
                    >
                      <svg
                        class="h-5 w-5 text-blue-500"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                      >
                        <path
                          d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                        />
                      </svg>
                    </div>
                    <input
                      v-model="profileForm.socialLinks.twitter"
                      type="url"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="https://twitter.com/yourusername"
                    />
                  </div>
                  <div class="flex items-center">
                    <div
                      class="w-10 h-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center mr-3"
                    >
                      <svg
                        class="h-5 w-5 text-blue-700"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                      >
                        <path
                          d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                        />
                      </svg>
                    </div>
                    <input
                      v-model="profileForm.socialLinks.linkedin"
                      type="url"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="https://linkedin.com/in/yourusername"
                    />
                  </div>
                  <div class="flex items-center">
                    <div
                      class="w-10 h-10 flex-shrink-0 bg-red-100 rounded-full flex items-center justify-center mr-3"
                    >
                      <svg
                        class="h-5 w-5 text-red-600"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                      >
                        <path
                          d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                        />
                      </svg>
                    </div>
                    <input
                      v-model="profileForm.socialLinks.youtube"
                      type="url"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="https://youtube.com/c/yourusername"
                    />
                  </div>
                  <div class="flex items-center">
                    <div
                      class="w-10 h-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center mr-3"
                    >
                      <svg
                        class="h-5 w-5 text-blue-600"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                      >
                        <path
                          d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"
                        />
                      </svg>
                    </div>
                    <input
                      v-model="profileForm.socialLinks.facebook"
                      type="url"
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="https://facebook.com/yourusername"
                    />
                  </div>
                </div>
              </div>

              <!-- Expertise Tags -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Areas of Expertise</label
                >
                <div class="flex flex-wrap gap-2 mb-2">
                  <div
                    v-for="tag in profileForm.expertise"
                    :key="tag"
                    class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm flex items-center"
                  >
                    <span>{{ tag }}</span>
                    <button
                      type="button"
                      @click="removeExpertise(tag)"
                      class="ml-1.5 text-indigo-600 hover:text-indigo-800 focus:outline-none"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
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
                  <div class="flex">
                    <select
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      @change="
                        ($event) => {
                          if ($event.target.value) {
                            addExpertise($event.target.value);
                            $event.target.value = '';
                          }
                        }
                      "
                    >
                      <option value="">
                        Select expertise area or add your own
                      </option>
                      <option
                        v-for="skill in availableExpertise.filter(
                          (s) => !profileForm.expertise.includes(s)
                        )"
                        :key="skill"
                        :value="skill"
                      >
                        {{ skill }}
                      </option>
                    </select>
                  </div>
                  <p class="mt-1 text-sm text-gray-500">
                    Select your expertise areas to help students find your
                    courses.
                  </p>
                </div>
              </div>

              <!-- Languages -->
              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Languages You Speak</label
                >
                <div class="flex flex-wrap gap-2 mb-2">
                  <div
                    v-for="language in profileForm.languagesSpoken"
                    :key="language"
                    class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm flex items-center"
                  >
                    <span>{{ language }}</span>
                    <button
                      type="button"
                      @click="removeLanguageSpoken(language)"
                      class="ml-1.5 text-green-600 hover:text-green-800 focus:outline-none"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
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
                  <div class="flex">
                    <select
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      @change="
                        ($event) => {
                          if ($event.target.value) {
                            addLanguageSpoken($event.target.value);
                            $event.target.value = '';
                          }
                        }
                      "
                    >
                      <option value="">Select a language</option>
                      <option
                        v-for="language in availableLanguagesSpoken.filter(
                          (l) => !profileForm.languagesSpoken.includes(l)
                        )"
                        :key="language"
                        :value="language"
                      >
                        {{ language }}
                      </option>
                    </select>
                  </div>
                  <div
                    class="border rounded-lg p-4 cursor-pointer transition-colors"
                    :class="
                      paymentSettings.paymentMethod === 'stripe'
                        ? 'border-indigo-500 bg-indigo-50'
                        : 'border-gray-300 hover:border-gray-400'
                    "
                    @click="paymentSettings.paymentMethod = 'stripe'"
                  >
                    <div class="flex justify-center mb-2">
                      <svg
                        class="h-8"
                        viewBox="0 0 120 30"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          d="M50.334 9.982C44.165 9.982 39.5 13.896 39.5 19.491C39.5 25.087 44.165 29 50.334 29C56.502 29 61.167 25.087 61.167 19.491C61.167 13.896 56.502 9.982 50.334 9.982ZM50.334 24.764C47.121 24.764 44.822 22.631 44.822 19.491C44.822 16.352 47.121 14.219 50.334 14.219C53.545 14.219 55.845 16.352 55.845 19.491C55.845 22.631 53.545 24.764 50.334 24.764ZM68.919 10.473L65.983 10.473L65.983 5.5L60.661 5.5L60.661 10.473L58.411 10.473L58.411 14.583L60.661 14.583L60.661 22.359C60.661 26.363 63.051 28.891 67.28 28.891C68.456 28.891 69.587 28.714 70.33 28.423L70.33 24.277C69.821 24.404 69.036 24.513 68.456 24.513C67.025 24.513 65.983 24.059 65.983 22.141L65.983 14.583L70.33 14.583L70.33 10.473L68.919 10.473ZM80.218 10.036C77.19 10.036 75.124 11.405 73.97 13.06L73.97 10.473L68.649 10.473L68.649 28.51L74.016 28.51L74.016 17.587C74.016 15.508 75.401 14.128 77.328 14.128C79.163 14.128 80.45 15.399 80.45 17.369L80.45 28.51L85.816 28.51L85.816 16.57C85.816 12.675 83.246 10.036 80.218 10.036ZM87.645 28.51L93.012 28.51L93.012 10.473L87.645 10.473L87.645 28.51ZM90.328 5C88.585 5 87.185 6.38 87.185 8.103C87.185 9.828 88.585 11.207 90.328 11.207C92.072 11.207 93.472 9.828 93.472 8.103C93.472 6.38 92.072 5 90.328 5ZM106.526 10.036C103.174 10.036 101.062 11.642 100.188 14.128L104.932 15.835C105.302 14.674 106.203 14.037 107.45 14.037C108.882 14.037 109.969 14.964 109.969 16.352L109.969 16.679C109.326 16.406 108.102 16.06 106.573 16.06C102.896 16.06 100 18.031 100 21.541C100 24.787 102.456 26.977 105.53 26.977C107.566 26.977 108.928 26.086 109.937 24.678L109.937 28.51L115.069 28.51L115.069 16.788C115.069 12.485 111.622 10.036 106.526 10.036ZM107.08 23.084C105.993 23.084 104.815 22.468 104.815 21.287C104.815 20.033 106.156 19.4 107.751 19.4C108.837 19.4 109.507 19.637 109.969 19.909C109.783 21.796 108.511 23.084 107.08 23.084Z"
                          fill="#6366F1"
                        />
                        <path
                          d="M29.867 11.709C29.146 9.076 26.721 7 24.02 7H11.98C9.279 7 6.854 9.076 6.133 11.709C5.412 14.342 6.184 17.076 8.183 18.8L14.193 23.8C16.192 25.523 19.065 25.523 21.064 23.8L27.074 18.8C29.072 17.076 29.845 14.342 29.123 11.709H29.867H29.123C29.867 11.709 29.867 11.709 29.867 11.709Z"
                          fill="#6366F1"
                        />
                      </svg>
                    </div>
                    <div class="text-center mt-2">
                      <input
                        type="radio"
                        id="stripe"
                        name="paymentMethod"
                        value="stripe"
                        :checked="paymentSettings.paymentMethod === 'stripe'"
                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                      />
                      <label
                        for="stripe"
                        class="ml-2 text-sm font-medium text-gray-700"
                        >Stripe</label
                      >
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment Method Settings -->
              <div class="mb-8">
                <!-- PayPal Settings -->
                <div
                  v-if="paymentSettings.paymentMethod === 'paypal'"
                  class="space-y-4"
                >
                  <div>
                    <label
                      for="paypalEmail"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >PayPal Email</label
                    >
                    <input
                      id="paypalEmail"
                      v-model="paymentSettings.paypalEmail"
                      type="email"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="email@example.com"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      All payments will be sent to this PayPal account.
                    </p>
                  </div>
                </div>

                <!-- Bank Transfer Settings -->
                <div
                  v-if="paymentSettings.paymentMethod === 'bank'"
                  class="space-y-4"
                >
                  <div>
                    <label
                      for="accountName"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Account Holder Name</label
                    >
                    <input
                      id="accountName"
                      v-model="paymentSettings.bankInfo.accountName"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label
                      for="accountNumber"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Account Number</label
                    >
                    <input
                      id="accountNumber"
                      v-model="paymentSettings.bankInfo.accountNumber"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label
                      for="bankName"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Bank Name</label
                    >
                    <input
                      id="bankName"
                      v-model="paymentSettings.bankInfo.bankName"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label
                      for="routingNumber"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Routing Number</label
                    >
                    <input
                      id="routingNumber"
                      v-model="paymentSettings.bankInfo.routingNumber"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label
                      for="swiftCode"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >SWIFT Code (for international transfers)</label
                    >
                    <input
                      id="swiftCode"
                      v-model="paymentSettings.bankInfo.swiftCode"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>

                <!-- Stripe Settings -->
                <div
                  v-if="paymentSettings.paymentMethod === 'stripe'"
                  class="bg-gray-50 p-4 rounded-lg border border-gray-200"
                >
                  <div class="flex items-center mb-4">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-indigo-500 mr-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span class="text-sm text-gray-700"
                      >Stripe integration requires additional setup. Please
                      connect your Stripe account in the Connect tab.</span
                    >
                  </div>
                  <button
                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                  >
                    Connect Stripe Account
                  </button>
                </div>
              </div>

              <!-- Tax Information -->
              <div class="mb-8">
                <h3
                  class="text-sm font-medium text-gray-900 uppercase tracking-wider mb-4"
                >
                  Tax Information
                </h3>
                <div class="space-y-4">
                  <div>
                    <label
                      for="country"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Country</label
                    >
                    <select
                      id="country"
                      v-model="paymentSettings.taxInfo.country"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="United States">United States</option>
                      <option value="Canada">Canada</option>
                      <option value="United Kingdom">United Kingdom</option>
                      <option value="Australia">Australia</option>
                      <option value="Germany">Germany</option>
                      <option value="France">France</option>
                      <option value="Japan">Japan</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="taxIdType"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Tax ID Type</label
                    >
                    <select
                      id="taxIdType"
                      v-model="paymentSettings.taxInfo.taxIdType"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="SSN">Social Security Number (SSN)</option>
                      <option value="EIN">
                        Employer Identification Number (EIN)
                      </option>
                      <option value="VAT">VAT Number</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div>
                    <label
                      for="taxIdNumber"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Tax ID Number</label
                    >
                    <input
                      id="taxIdNumber"
                      v-model="paymentSettings.taxInfo.taxIdNumber"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>

                  <!-- Address -->
                  <div>
                    <label
                      for="addressLine1"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Address Line 1</label
                    >
                    <input
                      id="addressLine1"
                      v-model="paymentSettings.taxInfo.addressLine1"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div>
                    <label
                      for="addressLine2"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Address Line 2 (Optional)</label
                    >
                    <input
                      id="addressLine2"
                      v-model="paymentSettings.taxInfo.addressLine2"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label
                        for="city"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >City</label
                      >
                      <input
                        id="city"
                        v-model="paymentSettings.taxInfo.city"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      />
                    </div>
                    <div>
                      <label
                        for="state"
                        class="block text-sm font-medium text-gray-700 mb-1"
                        >State/Province</label
                      >
                      <input
                        id="state"
                        v-model="paymentSettings.taxInfo.state"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      />
                    </div>
                  </div>
                  <div>
                    <label
                      for="zipCode"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >ZIP/Postal Code</label
                    >
                    <input
                      id="zipCode"
                      v-model="paymentSettings.taxInfo.zipCode"
                      type="text"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>
              </div>

              <!-- Payment Threshold -->
              <div class="mb-8">
                <h3
                  class="text-sm font-medium text-gray-900 uppercase tracking-wider mb-4"
                >
                  Payment Settings
                </h3>
                <div class="space-y-4">
                  <div>
                    <label
                      for="paymentThreshold"
                      class="block text-sm font-medium text-gray-700 mb-1"
                      >Payment Threshold</label
                    >
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                      >
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        id="paymentThreshold"
                        v-model="paymentSettings.paymentThreshold"
                        type="number"
                        min="0"
                        class="pl-7 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      />
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                      We'll issue payments once your earnings reach this amount.
                      Minimum is $50.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Save Button -->
              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center"
                  :disabled="isProfileSaving"
                >
                  <svg
                    v-if="isProfileSaving"
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
                  {{
                    isProfileSaving ? "Saving..." : "Save Profile Information"
                  }}
                </button>
              </div>
            </form>
          </div>

          <!-- Notification Preferences -->
          <div v-if="activeTab === 'notifications'" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Notification Preferences
            </h2>
            <p class="text-gray-600 mb-6">
              Customize how and when you receive notifications about your
              courses and students.
            </p>

            <form @submit.prevent="saveNotificationSettings">
              <!-- Email Notifications -->
              <div class="mb-8">
                <h3
                  class="text-sm font-medium text-gray-900 uppercase tracking-wider mb-4"
                >
                  Email Notifications
                </h3>
                <div class="space-y-4">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="courseEnrollments"
                        v-model="
                          notificationSettings.emailNotifications
                            .courseEnrollments
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="courseEnrollments"
                        class="font-medium text-gray-700"
                        >Course Enrollments</label
                      >
                      <p class="text-gray-500">
                        Receive email notifications when a student enrolls in
                        your course.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="courseReviews"
                        v-model="
                          notificationSettings.emailNotifications.courseReviews
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="courseReviews"
                        class="font-medium text-gray-700"
                        >Course Reviews</label
                      >
                      <p class="text-gray-500">
                        Receive email notifications when a student leaves a
                        review on your course.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="courseQuestions"
                        v-model="
                          notificationSettings.emailNotifications
                            .courseQuestions
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="courseQuestions"
                        class="font-medium text-gray-700"
                        >Course Questions</label
                      >
                      <p class="text-gray-500">
                        Receive email notifications when a student asks a
                        question in your course.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="messages"
                        v-model="
                          notificationSettings.emailNotifications.messages
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="messages" class="font-medium text-gray-700"
                        >Messages</label
                      >
                      <p class="text-gray-500">
                        Receive email notifications when you get a new message.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="promotions"
                        v-model="
                          notificationSettings.emailNotifications.promotions
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="promotions" class="font-medium text-gray-700"
                        >Promotions & Marketing</label
                      >
                      <p class="text-gray-500">
                        Receive promotional emails about features and
                        opportunities for instructors.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="accountUpdates"
                        v-model="
                          notificationSettings.emailNotifications.accountUpdates
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="accountUpdates"
                        class="font-medium text-gray-700"
                        >Account Updates</label
                      >
                      <p class="text-gray-500">
                        Receive email notifications about changes to your
                        account, privacy policy updates, etc.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="newsletter"
                        v-model="
                          notificationSettings.emailNotifications.newsletter
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="newsletter" class="font-medium text-gray-700"
                        >Instructor Newsletter</label
                      >
                      <p class="text-gray-500">
                        Receive our monthly newsletter with tips and best
                        practices for instructors.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Push Notifications -->
              <div class="mb-8">
                <h3
                  class="text-sm font-medium text-gray-900 uppercase tracking-wider mb-4"
                >
                  Push Notifications
                </h3>
                <div class="space-y-4">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="pushCourseEnrollments"
                        v-model="
                          notificationSettings.pushNotifications
                            .courseEnrollments
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="pushCourseEnrollments"
                        class="font-medium text-gray-700"
                        >Course Enrollments</label
                      >
                      <p class="text-gray-500">
                        Receive push notifications when a student enrolls in
                        your course.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="pushCourseReviews"
                        v-model="
                          notificationSettings.pushNotifications.courseReviews
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="pushCourseReviews"
                        class="font-medium text-gray-700"
                        >Course Reviews</label
                      >
                      <p class="text-gray-500">
                        Receive push notifications when a student leaves a
                        review on your course.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="pushCourseQuestions"
                        v-model="
                          notificationSettings.pushNotifications.courseQuestions
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="pushCourseQuestions"
                        class="font-medium text-gray-700"
                        >Course Questions</label
                      >
                      <p class="text-gray-500">
                        Receive push notifications when a student asks a
                        question in your course.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="pushMessages"
                        v-model="
                          notificationSettings.pushNotifications.messages
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="pushMessages"
                        class="font-medium text-gray-700"
                        >Messages</label
                      >
                      <p class="text-gray-500">
                        Receive push notifications when you get a new message.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="pushPromotions"
                        v-model="
                          notificationSettings.pushNotifications.promotions
                        "
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="pushPromotions"
                        class="font-medium text-gray-700"
                        >Promotions & Marketing</label
                      >
                      <p class="text-gray-500">
                        Receive promotional push notifications about features
                        and opportunities.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Save Button -->
              <div class="flex justify-end">
                <button
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center"
                  :disabled="isNotificationSaving"
                >
                  <svg
                    v-if="isNotificationSaving"
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
                  {{
                    isNotificationSaving
                      ? "Saving..."
                      : "Save Notification Preferences"
                  }}
                </button>
              </div>
            </form>
          </div>

          <!-- Payment Settings -->
          <div v-if="activeTab === 'payment'" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Payment Settings
            </h2>
            <p class="text-gray-600 mb-6">
              Manage how you receive payments for your courses and consultancy
              services.
            </p>

            <form @submit.prevent="savePaymentSettings">
              <!-- Payment Method Selector -->
              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Payment Method</label
                >
                <div class="grid grid-cols-3 gap-4">
                  <div
                    class="border rounded-lg p-4 cursor-pointer transition-colors"
                    :class="
                      paymentSettings.paymentMethod === 'bank'
                        ? 'border-indigo-500 bg-indigo-50'
                        : 'border-gray-300 hover:border-gray-400'
                    "
                    @click="paymentSettings.paymentMethod = 'bank'"
                  >
                    <div class="flex justify-center mb-2">
                      <svg
                        class="h-8"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          d="M4 10V17H20V10H4Z"
                          stroke="#4F46E5"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                        <path
                          d="M12 7L22 7L12 3L2 7L12 7Z"
                          stroke="#4F46E5"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                        <path
                          d="M4 17H20V20H4V17Z"
                          stroke="#4F46E5"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                    </div>
                    <div class="text-center mt-2">
                      <input
                        type="radio"
                        id="bank"
                        name="paymentMethod"
                        value="bank"
                        :checked="paymentSettings.paymentMethod === 'bank'"
                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                      />
                      <label
                        for="bank"
                        class="ml-2 text-sm font-medium text-gray-700"
                        >Bank Transfer</label
                      >
                    </div>
                    <div class="flex justify-center mb-2">
                      <svg
                        class="h-8"
                        viewBox="0 0 124 33"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          d="M46.211 8.761h-6.839a.473.473 0 00-.467.399l-1.374 8.716a.283.283 0 00.28.327h3.306a.473.473 0 00.467-.399l.37-2.349a.473.473 0 01.467-.399h2.166c3.518 0 5.548-1.702 6.079-5.079.24-1.479.01-2.642-.683-3.456-.76-.895-2.112-1.367-3.896-1.367zm.617 5.014c-.292 1.916-1.756 1.916-3.169 1.916h-.805l.565-3.578a.284.284 0 01.28-.24h.369c.965 0 1.874 0 2.341.55.28.33.366.82.419 1.352zm13.257-.15h-3.327a.284.284 0 00-.28.24l-.072.455-.114-.165c-.352-.512-1.139-.683-1.924-.683-1.801 0-3.342 1.367-3.641 3.283-.155.955.065 1.87.606 2.507.496.585 1.205.829 2.05.829 1.45 0 2.253-.93 2.253-.93l-.073.45a.283.283 0 00.28.327h2.994a.473.473 0 00.466-.398l.878-5.561a.284.284 0 00-.28-.327zm-4.635 4.992c-.157.935-.9 1.562-1.842 1.562-.475 0-.853-.152-1.096-.44-.24-.288-.332-.698-.256-1.156.148-.931.909-1.583 1.83-1.583.465 0 .84.153 1.089.445.247.296.347.708.275 1.172zm17.58-4.992h-3.339a.471.471 0 00-.39.206l-2.246 3.303-1.1-3.165a.473.473 0 00-.448-.297h-3.28a.284.284 0 00-.267.376l1.952 5.724-1.834 2.588c-.144.204.002.484.243.484h3.335a.47.47 0 00.387-.204l5.895-8.512c.142-.204-.004-.483-.244-.483zm12.37.04h-3.326a.285.285 0 00-.28.241l-.072.455-.113-.165c-.352-.512-1.139-.683-1.925-.683-1.8 0-3.343 1.367-3.64 3.283-.156.955.065 1.87.605 2.507.496.585 1.205.829 2.05.829 1.45 0 2.253-.93 2.253-.93l-.072.45a.283.283 0 00.28.327h2.993a.473.473 0 00.467-.398l.877-5.561a.283.283 0 00-.28-.327l.001-.028zm-4.635 4.992c-.157.935-.899 1.562-1.842 1.562-.474 0-.853-.152-1.096-.44-.24-.288-.331-.698-.255-1.156.148-.931.908-1.583 1.83-1.583.464 0 .84.153 1.088.445.248.296.347.708.275 1.172z"
                          fill="#003087"
                        />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
