<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Account & Profile - LearnConsult Help",
  meta: [
    {
      name: "description",
      content:
        "Learn how to manage your LearnConsult account settings, update your profile, and control your privacy preferences.",
    },
  ],
});

// Account sections
const sections = [
  {
    title: "Managing Your Account",
    content:
      "Your LearnConsult account gives you access to all platform features. You can manage your account settings by clicking on your profile picture in the top right corner and selecting 'Account Settings' from the dropdown menu.",
  },
  {
    title: "Updating Your Profile",
    content:
      "To update your profile information, go to Account Settings and click on the 'Profile' tab. Here you can change your name, email address, profile picture, and add a bio. Your profile is visible to other users on the platform.",
  },
  {
    title: "Changing Your Password",
    content:
      "To change your password, go to Account Settings and click on the 'Security' tab. Enter your current password and then your new password. For security reasons, we recommend using a strong password with a mix of letters, numbers, and special characters.",
  },
  {
    title: "Email Preferences",
    content:
      "You can control which emails you receive from LearnConsult in the 'Notifications' tab of your Account Settings. Here you can choose to receive updates about new courses, promotions, and platform announcements.",
  },
  {
    title: "Privacy Settings",
    content:
      "In the 'Privacy' tab of your Account Settings, you can control who can see your profile, your course progress, and your activity on the platform. You can also choose whether to allow other users to contact you.",
  },
  {
    title: "Connected Accounts",
    content:
      "LearnConsult allows you to connect your account with other services like Google, Facebook, or LinkedIn. You can manage these connections in the 'Connected Accounts' tab of your Account Settings.",
  },
  {
    title: "Deleting Your Account",
    content:
      "If you wish to delete your account, go to Account Settings and scroll to the bottom of the page. Click on 'Delete Account' and follow the prompts. Please note that this action is permanent and will remove all your data from our platform.",
  },
  {
    title: "Two-Factor Authentication",
    content:
      "For added security, you can enable two-factor authentication in the 'Security' tab of your Account Settings. This requires you to enter a verification code sent to your phone or email when logging in from a new device.",
  },
];
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <NuxtLink to="/" class="flex items-center">
              <span class="text-2xl font-bold text-indigo-600"
                >LearnConsult</span
              >
            </NuxtLink>
          </div>
          <nav class="hidden md:flex space-x-8">
            <NuxtLink to="/" class="text-gray-500 hover:text-gray-900"
              >Home</NuxtLink
            >
            <NuxtLink to="/courses" class="text-gray-500 hover:text-gray-900"
              >Courses</NuxtLink
            >
            <NuxtLink
              to="/consultancy"
              class="text-gray-500 hover:text-gray-900"
              >Consultancy</NuxtLink
            >
            <NuxtLink
              to="/auth/login"
              class="text-indigo-600 hover:text-indigo-800"
              >Login</NuxtLink
            >
            <NuxtLink
              to="/auth/register"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
              >Sign Up</NuxtLink
            >
          </nav>
        </div>
      </div>
    </header>

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
          <li>
            <div>
              <NuxtLink to="/help" class="text-gray-400 hover:text-gray-500">
                <svg
                  class="flex-shrink-0 h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                  />
                </svg>
                <span class="sr-only">Help</span>
              </NuxtLink>
            </div>
          </li>
          <li>
            <div class="flex items-center">
              <svg
                class="flex-shrink-0 h-5 w-5 text-gray-300"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
              >
                <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
              </svg>
              <span class="ml-4 text-sm font-medium text-gray-500"
                >Account & Profile</span
              >
            </div>
          </li>
        </ol>
      </nav>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h1 class="text-2xl font-bold text-gray-900">
            Account & Profile Settings
          </h1>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Learn how to manage your account settings, update your profile, and
            control your privacy.
          </p>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
          <div class="prose max-w-none">
            <div v-for="(section, index) in sections" :key="index" class="mb-8">
              <h2 class="text-xl font-semibold text-gray-900 mb-2">
                {{ section.title }}
              </h2>
              <p class="text-gray-600">{{ section.content }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Articles -->
      <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <NuxtLink
            to="/help/articles/reset-password"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">
              How to Reset Your Password
            </h3>
            <p class="text-gray-600 text-sm mt-1">
              Step-by-step guide to resetting your password if you've forgotten
              it.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/update-profile"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Updating Your Profile</h3>
            <p class="text-gray-600 text-sm mt-1">
              Learn how to update your profile information and make it stand
              out.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/privacy-settings"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">Privacy Settings</h3>
            <p class="text-gray-600 text-sm mt-1">
              How to control who can see your information on the platform.
            </p>
          </NuxtLink>
          <NuxtLink
            to="/help/articles/two-factor-auth"
            class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-300"
          >
            <h3 class="font-medium text-indigo-600">
              Two-Factor Authentication
            </h3>
            <p class="text-gray-600 text-sm mt-1">
              Enhance your account security with two-factor authentication.
            </p>
          </NuxtLink>
        </div>
      </div>

      <!-- Still Need Help -->
      <div class="mt-12 bg-indigo-50 rounded-lg p-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Still need help?</h2>
        <p class="text-gray-600 mb-4">
          Our support team is available to assist you with any questions.
        </p>
        <div
          class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4"
        >
          <NuxtLink
            to="/help"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Return to Help Center
          </NuxtLink>
          <a
            href="mailto:support@learnconsult.com"
            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Contact Support
          </a>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              LearnConsult
            </h3>
            <p class="text-gray-500 text-sm">
              Empowering learners and organizations with quality education and
              expert consultancy services.
            </p>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Quick Links
            </h3>
            <ul class="space-y-2">
              <li>
                <NuxtLink
                  to="/"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Home</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/courses"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Courses</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/consultancy"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Consultancy</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/about"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >About Us</NuxtLink
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legal</h3>
            <ul class="space-y-2">
              <li>
                <NuxtLink
                  to="/privacy"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Privacy Policy</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/cookie"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Cookie Policy</NuxtLink
                >
              </li>
              <li>
                <NuxtLink
                  to="/terms"
                  class="text-gray-500 hover:text-gray-900 text-sm"
                  >Terms of Service</NuxtLink
                >
              </li>
            </ul>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
            <ul class="space-y-2">
              <li class="text-gray-500 text-sm">
                Email: info@learnconsult.com
              </li>
              <li class="text-gray-500 text-sm">Phone: +1 (123) 456-7890</li>
              <li class="text-gray-500 text-sm">
                Address: 123 Learning Street, Education City, EC 12345
              </li>
            </ul>
          </div>
        </div>
        <div
          class="border-t border-gray-200 mt-8 pt-8 text-center text-gray-500 text-sm"
        >
          &copy; {{ new Date().getFullYear() }} LearnConsult. All rights
          reserved.
        </div>
      </div>
    </footer>
  </div>
</template>
