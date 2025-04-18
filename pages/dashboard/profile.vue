<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "dashboard",
});

useHead({
  title: "Profile Settings - LearnConsult",
});

// Profile form state
const profileForm = ref({
  firstName: "John",
  lastName: "Doe",
  email: "john.doe@example.com",
  phone: "+44 20 1234 5678",
  bio: "E-learning enthusiast and technology consultant with 5+ years of experience.",
  company: "Tech Solutions Ltd",
  jobTitle: "Senior Consultant",
  location: "London, UK",
  website: "https://johndoe.com",
  twitter: "@johndoe",
  linkedin: "linkedin.com/in/johndoe",
});

// Account settings state
const accountSettings = ref({
  language: "en",
  timezone: "Europe/London",
  emailNotifications: true,
  marketingEmails: false,
  courseUpdates: true,
  consultancyUpdates: true,
  twoFactorEnabled: false,
});

// Profile picture state
const profilePicture = ref(null);
const profilePicturePreview = ref("/images/default-avatar.png");

// Handle profile picture change
const handleProfilePictureChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    const file = input.files[0];
    profilePicture.value = file;

    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      profilePicturePreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

// Handle profile form submission
const handleProfileSubmit = async (e: Event) => {
  e.preventDefault();
  // TODO: Implement profile update logic
  console.log("Profile updated:", profileForm.value);
};

// Handle account settings submission
const handleAccountSettingsSubmit = async (e: Event) => {
  e.preventDefault();
  // TODO: Implement account settings update logic
  console.log("Account settings updated:", accountSettings.value);
};

// Handle two-factor authentication toggle
const toggleTwoFactor = async () => {
  accountSettings.value.twoFactorEnabled =
    !accountSettings.value.twoFactorEnabled;
  // TODO: Implement 2FA setup/disable logic
};

// Available languages
const languages = [
  { code: "en", name: "English" },
  { code: "es", name: "Spanish" },
  { code: "fr", name: "French" },
  { code: "de", name: "German" },
  { code: "it", name: "Italian" },
  { code: "pt", name: "Portuguese" },
  { code: "ru", name: "Russian" },
  { code: "zh", name: "Chinese" },
  { code: "ja", name: "Japanese" },
];

// Available timezones
const timezones = [
  "UTC",
  "Europe/London",
  "America/New_York",
  "America/Los_Angeles",
  "Asia/Tokyo",
  "Australia/Sydney",
];
</script>

<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900">Profile Settings</h1>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
      <div class="py-4">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Profile Picture Section -->
          <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6">
              <h2 class="text-lg font-medium text-gray-900 mb-4">
                Profile Picture
              </h2>
              <div class="flex flex-col items-center">
                <div class="mb-4">
                  <img
                    :src="profilePicturePreview"
                    alt="Profile picture"
                    class="h-32 w-32 rounded-full object-cover"
                  />
                </div>
                <div class="w-full">
                  <label
                    class="block w-full px-4 py-2 text-sm font-medium text-center text-white bg-indigo-600 rounded-md hover:bg-indigo-700 cursor-pointer"
                  >
                    Change Photo
                    <input
                      type="file"
                      class="hidden"
                      accept="image/*"
                      @change="handleProfilePictureChange"
                    />
                  </label>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                  JPG, GIF or PNG. Max size of 2MB.
                </p>
              </div>
            </div>
          </div>

          <!-- Profile Information Section -->
          <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                  Profile Information
                </h2>
                <form @submit="handleProfileSubmit" class="space-y-6">
                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                      <label
                        for="firstName"
                        class="block text-sm font-medium text-gray-700"
                      >
                        First Name
                      </label>
                      <input
                        id="firstName"
                        v-model="profileForm.firstName"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label
                        for="lastName"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Last Name
                      </label>
                      <input
                        id="lastName"
                        v-model="profileForm.lastName"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                  </div>

                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                      <label
                        for="email"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Email
                      </label>
                      <input
                        id="email"
                        v-model="profileForm.email"
                        type="email"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label
                        for="phone"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Phone
                      </label>
                      <input
                        id="phone"
                        v-model="profileForm.phone"
                        type="tel"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                  </div>

                  <div>
                    <label
                      for="bio"
                      class="block text-sm font-medium text-gray-700"
                    >
                      Bio
                    </label>
                    <textarea
                      id="bio"
                      v-model="profileForm.bio"
                      rows="3"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    ></textarea>
                  </div>

                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                      <label
                        for="company"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Company
                      </label>
                      <input
                        id="company"
                        v-model="profileForm.company"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label
                        for="jobTitle"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Job Title
                      </label>
                      <input
                        id="jobTitle"
                        v-model="profileForm.jobTitle"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                  </div>

                  <div>
                    <label
                      for="location"
                      class="block text-sm font-medium text-gray-700"
                    >
                      Location
                    </label>
                    <input
                      id="location"
                      v-model="profileForm.location"
                      type="text"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>

                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                      <label
                        for="website"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Website
                      </label>
                      <input
                        id="website"
                        v-model="profileForm.website"
                        type="url"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label
                        for="twitter"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Twitter
                      </label>
                      <input
                        id="twitter"
                        v-model="profileForm.twitter"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                    <div>
                      <label
                        for="linkedin"
                        class="block text-sm font-medium text-gray-700"
                      >
                        LinkedIn
                      </label>
                      <input
                        id="linkedin"
                        v-model="profileForm.linkedin"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      />
                    </div>
                  </div>

                  <div class="flex justify-end">
                    <button
                      type="submit"
                      class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      Save Profile
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Account Settings Section -->
      <div class="py-4">
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
              Account Settings
            </h2>
            <form @submit="handleAccountSettingsSubmit" class="space-y-6">
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label
                    for="language"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Language
                  </label>
                  <select
                    id="language"
                    v-model="accountSettings.language"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  >
                    <option
                      v-for="lang in languages"
                      :key="lang.code"
                      :value="lang.code"
                    >
                      {{ lang.name }}
                    </option>
                  </select>
                </div>
                <div>
                  <label
                    for="timezone"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Timezone
                  </label>
                  <select
                    id="timezone"
                    v-model="accountSettings.timezone"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                  >
                    <option v-for="tz in timezones" :key="tz" :value="tz">
                      {{ tz }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">
                  Notification Preferences
                </h3>
                <div class="space-y-4">
                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="emailNotifications"
                        v-model="accountSettings.emailNotifications"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="emailNotifications"
                        class="font-medium text-gray-700"
                      >
                        Email Notifications
                      </label>
                      <p class="text-gray-500">
                        Receive email notifications about your account activity.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="marketingEmails"
                        v-model="accountSettings.marketingEmails"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="marketingEmails"
                        class="font-medium text-gray-700"
                      >
                        Marketing Emails
                      </label>
                      <p class="text-gray-500">
                        Receive emails about new features, promotions, and
                        updates.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="courseUpdates"
                        v-model="accountSettings.courseUpdates"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="courseUpdates"
                        class="font-medium text-gray-700"
                      >
                        Course Updates
                      </label>
                      <p class="text-gray-500">
                        Receive notifications about updates to your enrolled
                        courses.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start">
                    <div class="flex items-center h-5">
                      <input
                        id="consultancyUpdates"
                        v-model="accountSettings.consultancyUpdates"
                        type="checkbox"
                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <label
                        for="consultancyUpdates"
                        class="font-medium text-gray-700"
                      >
                        Consultancy Updates
                      </label>
                      <p class="text-gray-500">
                        Receive notifications about your consultancy projects.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Security</h3>
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="twoFactorEnabled"
                      v-model="accountSettings.twoFactorEnabled"
                      type="checkbox"
                      class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                      @change="toggleTwoFactor"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label
                      for="twoFactorEnabled"
                      class="font-medium text-gray-700"
                    >
                      Two-Factor Authentication
                    </label>
                    <p class="text-gray-500">
                      Add an extra layer of security to your account by enabling
                      two-factor authentication.
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Save Settings
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
