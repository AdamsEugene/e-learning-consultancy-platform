<!-- components/dashboard/settings/ProfileSettingsForm.vue -->
<script setup lang="ts">
import { ref, reactive } from "vue";
import SettingsHeader from "./SettingsHeader.vue";

const emit = defineEmits<{
  (e: "update-message", type: string, message: string): void;
}>();

// User profile data
const userProfile = reactive({
  name: "Emily Johnson",
  email: "emily.johnson@example.com",
  phone: "+1 (555) 123-4567",
  avatar: "/images/instructors/i3.jpg",
  bio: "Frontend developer passionate about creating intuitive user experiences. Currently learning React Native and UI/UX design principles.",
  jobTitle: "Frontend Developer",
  company: "TechStart Inc.",
  location: "San Francisco, CA",
  website: "https://emilyjohnson.dev",
  skills: ["JavaScript", "React", "CSS", "HTML", "UI/UX", "Responsive Design"],
  socialMedia: {
    github: "emilyjohnson",
    twitter: "emilycodes",
    linkedin: "emily-johnson-dev",
    instagram: "",
  },
  timezone: "America/Los_Angeles",
});

// Form state
const isSubmitting = ref(false);

// File upload state
const avatarFile = ref<File | null>(null);
const previewUrl = ref(userProfile.avatar);

// New skill input
const newSkill = ref("");

// Methods
const handleAvatarChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    avatarFile.value = input.files[0];
    previewUrl.value = URL.createObjectURL(input.files[0]);
  }
};

const saveProfile = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Upload avatar if provided
    if (avatarFile.value) {
      // In a real app: Upload file to server
      userProfile.avatar = previewUrl.value; // Just update with preview in this demo
    }

    emit("update-message", "success", "Profile updated successfully!");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem updating your profile. Please try again."
    );
  } finally {
    isSubmitting.value = false;
  }
};

const addSkill = () => {
  if (newSkill.value && !userProfile.skills.includes(newSkill.value)) {
    userProfile.skills.push(newSkill.value);
    newSkill.value = "";
  }
};

const removeSkill = (index: number) => {
  userProfile.skills.splice(index, 1);
};

const validateWebsite = (url: string): boolean => {
  if (!url) return true;

  try {
    new URL(url);
    return true;
  } catch (e) {
    return false;
  }
};
</script>

<template>
  <div>
    <!-- Section header -->
    <SettingsHeader
      title="Profile Settings"
      description="Update your personal information and public profile"
    />

    <!-- Form content -->
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Avatar upload section -->
          <div class="md:col-span-1">
            <div class="text-center">
              <div class="mb-4">
                <div class="relative inline-block">
                  <img
                    :src="previewUrl"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md"
                    alt="Profile Picture"
                  />
                  <div class="absolute bottom-0 right-0">
                    <label
                      for="avatar-upload"
                      class="cursor-pointer p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition-colors shadow-md"
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
                      <input
                        id="avatar-upload"
                        type="file"
                        class="hidden"
                        accept="image/*"
                        @change="handleAvatarChange"
                      />
                    </label>
                  </div>
                </div>
              </div>
              <h3 class="text-lg font-medium">{{ userProfile.name }}</h3>
              <p class="text-gray-600 text-sm">{{ userProfile.jobTitle }}</p>
              <p v-if="userProfile.company" class="text-gray-500 text-sm">
                {{ userProfile.company }}
              </p>
            </div>
          </div>

          <!-- Profile details form -->
          <div class="md:col-span-2">
            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    for="fullName"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Full Name</label
                  >
                  <input
                    id="fullName"
                    type="text"
                    v-model="userProfile.name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Your full name"
                  />
                </div>
                <div>
                  <label
                    for="email"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Email Address</label
                  >
                  <input
                    id="email"
                    type="email"
                    v-model="userProfile.email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Your email address"
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
                    type="text"
                    v-model="userProfile.jobTitle"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Your job title"
                  />
                </div>
                <div>
                  <label
                    for="company"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Company/Organization</label
                  >
                  <input
                    id="company"
                    type="text"
                    v-model="userProfile.company"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Your company or organization"
                  />
                </div>
                <div>
                  <label
                    for="phone"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Phone Number (optional)</label
                  >
                  <input
                    id="phone"
                    type="tel"
                    v-model="userProfile.phone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Your phone number"
                  />
                </div>
                <div>
                  <label
                    for="location"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Location</label
                  >
                  <input
                    id="location"
                    type="text"
                    v-model="userProfile.location"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="City, Country"
                  />
                </div>
                <div>
                  <label
                    for="website"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Website (optional)</label
                  >
                  <input
                    id="website"
                    type="url"
                    v-model="userProfile.website"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Your website or portfolio"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        userProfile.website &&
                        !validateWebsite(userProfile.website),
                    }"
                  />
                  <p
                    v-if="
                      userProfile.website &&
                      !validateWebsite(userProfile.website)
                    "
                    class="mt-1 text-sm text-red-600"
                  >
                    Please enter a valid URL (e.g., https://example.com)
                  </p>
                </div>
                <div>
                  <label
                    for="timezone"
                    class="block text-sm font-medium text-gray-700 mb-1"
                    >Timezone</label
                  >
                  <select
                    id="timezone"
                    v-model="userProfile.timezone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="America/Los_Angeles">
                      Pacific Time (US & Canada)
                    </option>
                    <option value="America/New_York">
                      Eastern Time (US & Canada)
                    </option>
                    <option value="America/Chicago">
                      Central Time (US & Canada)
                    </option>
                    <option value="America/Denver">
                      Mountain Time (US & Canada)
                    </option>
                    <option value="Europe/London">London</option>
                    <option value="Europe/Paris">Paris</option>
                    <option value="Asia/Tokyo">Tokyo</option>
                    <option value="Australia/Sydney">Sydney</option>
                  </select>
                </div>
              </div>

              <div>
                <label
                  for="bio"
                  class="block text-sm font-medium text-gray-700 mb-1"
                  >Bio</label
                >
                <textarea
                  id="bio"
                  v-model="userProfile.bio"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Tell us about yourself..."
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Skills & Expertise</label
                >
                <div class="flex flex-wrap gap-2 mb-2">
                  <div
                    v-for="(skill, index) in userProfile.skills"
                    :key="index"
                    class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm flex items-center transition-all"
                  >
                    <span>{{ skill }}</span>
                    <button
                      type="button"
                      class="ml-1.5 text-indigo-500 hover:text-indigo-700 transition-colors focus:outline-none"
                      @click="removeSkill(index)"
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
                <div class="flex">
                  <input
                    id="skill-input"
                    type="text"
                    v-model="newSkill"
                    placeholder="Add a skill (e.g., JavaScript, Python)"
                    class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    @keyup.enter="addSkill"
                  />
                  <button
                    type="button"
                    @click="addSkill"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Add
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3"
                  >Social Media Links</label
                >
                <div class="space-y-4">
                  <div class="flex items-center">
                    <div class="w-10 text-gray-500">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                        />
                      </svg>
                    </div>
                    <div class="flex-grow ml-2">
                      <div class="flex">
                        <span
                          class="py-2 px-3 bg-gray-100 text-gray-500 rounded-l-md border-y border-l border-gray-300"
                          >github.com/</span
                        >
                        <input
                          v-model="userProfile.socialMedia.github"
                          type="text"
                          class="flex-grow px-3 py-2 border border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="username"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div class="w-10 text-gray-500">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                        />
                      </svg>
                    </div>
                    <div class="flex-grow ml-2">
                      <div class="flex">
                        <span
                          class="py-2 px-3 bg-gray-100 text-gray-500 rounded-l-md border-y border-l border-gray-300"
                          >twitter.com/</span
                        >
                        <input
                          v-model="userProfile.socialMedia.twitter"
                          type="text"
                          class="flex-grow px-3 py-2 border border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="username"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div class="w-10 text-gray-500">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"
                        />
                      </svg>
                    </div>
                    <div class="flex-grow ml-2">
                      <div class="flex">
                        <span
                          class="py-2 px-3 bg-gray-100 text-gray-500 rounded-l-md border-y border-l border-gray-300"
                          >linkedin.com/in/</span
                        >
                        <input
                          v-model="userProfile.socialMedia.linkedin"
                          type="text"
                          class="flex-grow px-3 py-2 border border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="username"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <div class="w-10 text-gray-500">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                        />
                      </svg>
                    </div>
                    <div class="flex-grow ml-2">
                      <div class="flex">
                        <span
                          class="py-2 px-3 bg-gray-100 text-gray-500 rounded-l-md border-y border-l border-gray-300"
                          >instagram.com/</span
                        >
                        <input
                          v-model="userProfile.socialMedia.instagram"
                          type="text"
                          class="flex-grow px-3 py-2 border border-gray-300 rounded-r-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="username"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="button"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  @click="saveProfile"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
                  :disabled="
                    isSubmitting ||
                    (userProfile.website &&
                      !validateWebsite(userProfile.website))
                  "
                >
                  <svg
                    v-if="isSubmitting"
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
                  {{ isSubmitting ? "Saving..." : "Save Changes" }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animation for hovering elements */
.filter-option {
  transition: all 0.2s ease;
}

.filter-option:hover {
  transform: translateX(2px);
}
</style>
