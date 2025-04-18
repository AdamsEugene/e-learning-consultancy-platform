<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">UI Components</h1>
    <p class="text-gray-600 mb-8">
      A collection of reusable UI components designed for the e-learning and
      consultancy platform. These components follow a consistent design system
      and are built with accessibility in mind.
    </p>

    <!-- Component Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <!-- Basic Components -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
          Basic Components
        </h2>
        <p class="text-gray-600 mb-6">
          Essential building blocks for creating user interfaces.
        </p>
        <div class="space-y-4">
          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Button</h3>
              <p class="text-sm text-gray-600 mb-2">
                Versatile button component with multiple variants, sizes, and
                states.
              </p>
              <NuxtLink
                to="/components/button"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>

          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Table</h3>
              <p class="text-sm text-gray-600 mb-2">
                Data table component with sorting, filtering, and pagination
                capabilities.
              </p>
              <NuxtLink
                to="/components/table"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>

          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Form</h3>
              <p class="text-sm text-gray-600 mb-2">
                Form elements including inputs, selects, checkboxes, and
                validation.
              </p>
              <NuxtLink
                to="/components/form"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>

          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                <path
                  fill-rule="evenodd"
                  d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Card</h3>
              <p class="text-sm text-gray-600 mb-2">
                Container component for displaying content in a structured
                format.
              </p>
              <NuxtLink
                to="/components/card"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Advanced Components -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
          Advanced Components
        </h2>
        <p class="text-gray-600 mb-6">
          More complex components for specialized use cases.
        </p>
        <div class="space-y-4">
          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Video Player</h3>
              <p class="text-sm text-gray-600 mb-2">
                Custom video player with controls and playback features.
              </p>
              <NuxtLink
                to="/components/video-player"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>

          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Quiz</h3>
              <p class="text-sm text-gray-600 mb-2">
                Interactive quiz component for assessments and knowledge checks.
              </p>
              <NuxtLink
                to="/components/quiz"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>

          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"
                />
                <path
                  d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">Chat</h3>
              <p class="text-sm text-gray-600 mb-2">
                Real-time chat component for messaging and discussions.
              </p>
              <NuxtLink
                to="/components/chat"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>

          <div class="flex items-start">
            <div
              class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <h3 class="font-medium text-gray-900">File Upload</h3>
              <p class="text-sm text-gray-600 mb-2">
                File upload component with drag-and-drop and progress tracking.
              </p>
              <NuxtLink
                to="/components/file-upload"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >View component →</NuxtLink
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Design System -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Design System</h2>
      <p class="text-gray-600 mb-6">
        Our components are built on a consistent design system with standardized
        colors, typography, spacing, and interactions.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <h3 class="font-medium text-gray-900 mb-3">Colors</h3>
          <div class="space-y-2">
            <div class="flex items-center">
              <div class="w-6 h-6 rounded-full bg-indigo-600 mr-2"></div>
              <span class="text-sm">Primary</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 rounded-full bg-gray-600 mr-2"></div>
              <span class="text-sm">Secondary</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 rounded-full bg-green-600 mr-2"></div>
              <span class="text-sm">Success</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 rounded-full bg-red-600 mr-2"></div>
              <span class="text-sm">Danger</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 rounded-full bg-yellow-500 mr-2"></div>
              <span class="text-sm">Warning</span>
            </div>
            <div class="flex items-center">
              <div class="w-6 h-6 rounded-full bg-blue-500 mr-2"></div>
              <span class="text-sm">Info</span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="font-medium text-gray-900 mb-3">Typography</h3>
          <div class="space-y-2">
            <div>
              <h1 class="text-2xl font-bold">Heading 1</h1>
              <span class="text-xs text-gray-500">text-2xl font-bold</span>
            </div>
            <div>
              <h2 class="text-xl font-semibold">Heading 2</h2>
              <span class="text-xs text-gray-500">text-xl font-semibold</span>
            </div>
            <div>
              <h3 class="text-lg font-medium">Heading 3</h3>
              <span class="text-xs text-gray-500">text-lg font-medium</span>
            </div>
            <div>
              <p class="text-base">Body Text</p>
              <span class="text-xs text-gray-500">text-base</span>
            </div>
            <div>
              <p class="text-sm">Small Text</p>
              <span class="text-xs text-gray-500">text-sm</span>
            </div>
          </div>
        </div>

        <div>
          <h3 class="font-medium text-gray-900 mb-3">Spacing</h3>
          <div class="space-y-2">
            <div class="flex items-center">
              <div class="w-2 h-2 bg-gray-300 mr-2"></div>
              <span class="text-sm">xs (0.25rem)</span>
            </div>
            <div class="flex items-center">
              <div class="w-4 h-4 bg-gray-300 mr-2"></div>
              <span class="text-sm">sm (0.5rem)</span>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 bg-gray-300 mr-2"></div>
              <span class="text-sm">md (1rem)</span>
            </div>
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gray-300 mr-2"></div>
              <span class="text-sm">lg (1.5rem)</span>
            </div>
            <div class="flex items-center">
              <div class="w-16 h-16 bg-gray-300 mr-2"></div>
              <span class="text-sm">xl (2rem)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Usage Guidelines -->
    <div class="bg-white rounded-lg shadow-sm p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Usage Guidelines</h2>
      <p class="text-gray-600 mb-6">
        Follow these guidelines to ensure consistent implementation of our
        components.
      </p>

      <div class="space-y-4">
        <div class="flex items-start">
          <div
            class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mr-3 mt-0.5"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-green-600"
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
          <div>
            <h3 class="font-medium text-gray-900">Accessibility</h3>
            <p class="text-sm text-gray-600">
              All components are built with accessibility in mind, following
              WCAG 2.1 guidelines.
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <div
            class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mr-3 mt-0.5"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-green-600"
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
          <div>
            <h3 class="font-medium text-gray-900">Responsive Design</h3>
            <p class="text-sm text-gray-600">
              Components adapt to different screen sizes and devices.
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <div
            class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mr-3 mt-0.5"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-green-600"
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
          <div>
            <h3 class="font-medium text-gray-900">Dark Mode Support</h3>
            <p class="text-sm text-gray-600">
              Components automatically adapt to light and dark mode preferences.
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <div
            class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mr-3 mt-0.5"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-green-600"
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
          <div>
            <h3 class="font-medium text-gray-900">Internationalization</h3>
            <p class="text-sm text-gray-600">
              Components support multiple languages and right-to-left (RTL)
              layouts.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: "components",
});
</script>
