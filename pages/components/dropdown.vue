<script setup lang="ts">
import { ref } from "vue";

// SVG icons defined as constants to avoid repetition
const ICONS = {
  user: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
  </svg>`,
  settings: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
  </svg>`,
  logout: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
  </svg>`,
  add: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
  </svg>`,
  notification: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
  </svg>`,
  edit: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
  </svg>`,
  share: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
  </svg>`,
  delete: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
  </svg>`,
  arrowDown: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
  </svg>`,
};

// Sample data
const courses = [
  { id: 1, title: "Vue 3 Fundamentals" },
  { id: 2, title: "TypeScript Deep Dive" },
  { id: 3, title: "Nuxt.js for Production" },
  { id: 4, title: "Advanced CSS Techniques" },
];

const projects = [
  { id: 1, title: "E-Learning Platform" },
  { id: 2, title: "Corporate Website Redesign" },
  { id: 3, title: "Mobile App Development" },
];

// Demo configuration data for looping
const variants = [
  { name: "Primary", value: "primary" as const },
  { name: "Secondary", value: "secondary" as const },
  { name: "Outline", value: "outline" as const },
  { name: "Ghost", value: "ghost" as const },
];

const sizes = [
  { name: "Small", value: "sm" as const },
  { name: "Medium", value: "md" as const },
  { name: "Large", value: "lg" as const },
];

const positions = [
  { name: "Left", value: "left" as const },
  { name: "Center", value: "center" as const },
  { name: "Right", value: "right" as const },
];

type IconType = keyof typeof ICONS;

const iconDropdowns = [
  {
    icon: "settings" as IconType,
    variant: "outline" as const,
    items: ["Settings", "Preferences", "Help"],
  },
  {
    icon: "add" as IconType,
    variant: "primary" as const,
    items: ["Add Course", "Add Project", "Add User"],
  },
  {
    icon: "notification" as IconType,
    variant: "ghost" as const,
    items: ["You have 3 notifications", "View all"],
  },
];

const customChevrons = [
  { label: "Edit", icon: "edit" as IconType },
  { label: "Share", icon: "share" as IconType },
  { label: "Delete", icon: "delete" as IconType },
];

// Demo state
const selectedOption = ref("Select an option");
const controlledDropdown = ref(false);

// Methods
const handleOptionSelect = (option: string) => {
  selectedOption.value = option;
};

const showNotification = (message: string) => {
  alert(message);
};

// Page meta
definePageMeta({
  layout: "components",
});

useHead({
  title: "Dropdown Component Demo - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Interactive demo of the dropdown component for LearnConsult platform",
    },
  ],
});
</script>

<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="min-h-screen transition-colors">
    <div class="max-w-7xl mx-auto p-6">
      <!-- Header with demo controls -->
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Dropdown Component Demo
        </h1>

        <div class="flex items-center space-x-4">
          <!-- User Menu Dropdown -->
          <UiDropdown
            label="John Doe"
            variant="outline"
            position="right"
            icon-position="left"
          >
            <template #icon>
              <div v-html="ICONS.user" />
            </template>
            <UiDropdownItem>
              <template #icon>
                <div v-html="ICONS.user" />
              </template>
              My Profile
            </UiDropdownItem>
            <UiDropdownItem>
              <template #icon>
                <div v-html="ICONS.settings" />
              </template>
              Account Settings
            </UiDropdownItem>
            <div class="dropdown-divider" />
            <UiDropdownItem danger>
              <template #icon>
                <div v-html="ICONS.logout" />
              </template>
              Logout
            </UiDropdownItem>
          </UiDropdown>

          <!-- Notification Icon Dropdown -->
          <UiDropdown icon-only variant="ghost">
            <template #icon>
              <div v-html="ICONS.notification" />
            </template>
            <div
              class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase"
            >
              Notifications
            </div>
            <UiDropdownItem @click="showNotification('New message received')">
              <div class="flex flex-col">
                <span class="font-medium">New message</span>
                <span class="text-xs text-gray-500"
                  >You have a new message from Sarah</span
                >
              </div>
            </UiDropdownItem>
            <UiDropdownItem
              @click="showNotification('Course update notification')"
            >
              <div class="flex flex-col">
                <span class="font-medium">Course update</span>
                <span class="text-xs text-gray-500"
                  >Vue 3 course has been updated</span
                >
              </div>
            </UiDropdownItem>
            <div class="dropdown-divider" />
            <UiDropdownItem>View all notifications</UiDropdownItem>
          </UiDropdown>
        </div>
      </div>

      <!-- Demo Sections Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- SECTION 1: Basic Variants -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Basic Variants
          </h2>
          <div class="flex flex-wrap gap-4">
            <UiDropdown
              v-for="variant in variants"
              :key="variant.value"
              :label="variant.name"
              :variant="variant.value"
            >
              <UiDropdownItem
                v-for="n in 3"
                :key="n"
                @click="
                  showNotification(`${variant.name} Option ${n} selected`)
                "
              >
                Option {{ n }}
              </UiDropdownItem>
            </UiDropdown>
          </div>
        </section>

        <!-- SECTION 2: Dropdown Sizes -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Dropdown Sizes
          </h2>
          <div class="flex flex-wrap items-center gap-4">
            <UiDropdown
              v-for="size in sizes"
              :key="size.value"
              :label="size.name"
              :size="size.value"
              variant="outline"
            >
              <UiDropdownItem v-for="n in 3" :key="n"
                >Option {{ n }}</UiDropdownItem
              >
            </UiDropdown>
          </div>
        </section>

        <!-- SECTION 3: Menu Positioning -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Menu Positioning
          </h2>
          <div class="flex flex-wrap justify-between">
            <UiDropdown
              v-for="position in positions"
              :key="position.value"
              :label="position.name"
              :position="position.value"
              variant="outline"
            >
              <UiDropdownItem>{{ position.name }} aligned menu</UiDropdownItem>
              <UiDropdownItem>Another item</UiDropdownItem>
            </UiDropdown>
          </div>
        </section>

        <!-- SECTION 4: Interactive Selection -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Selection Dropdown
          </h2>
          <p class="mb-4 text-gray-700">
            Selected: <span class="font-medium">{{ selectedOption }}</span>
          </p>

          <UiDropdown :label="selectedOption" variant="outline" width="200px">
            <UiDropdownItem
              v-for="option in ['Option 1', 'Option 2', 'Option 3', 'Option 4']"
              :key="option"
              :active="selectedOption === option"
              @click="handleOptionSelect(option)"
            >
              {{ option }}
            </UiDropdownItem>
          </UiDropdown>
        </section>

        <!-- SECTION 5: Course Selection Example -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Course Selection
          </h2>
          <UiDropdown label="Select a Course" variant="primary" width="250px">
            <div class="dropdown-header">Available Courses</div>
            <UiDropdownItem
              v-for="course in courses"
              :key="course.id"
              @click="showNotification(`Selected course: ${course.title}`)"
            >
              {{ course.title }}
            </UiDropdownItem>
            <div class="dropdown-divider" />
            <UiDropdownItem>
              <template #icon>
                <div v-html="ICONS.add" />
              </template>
              Create New Course
            </UiDropdownItem>
          </UiDropdown>
        </section>

        <!-- SECTION 6: Icon and Danger Items -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Icon and Danger Items
          </h2>
          <UiDropdown label="Project Actions" variant="outline" width="240px">
            <div class="dropdown-header">Project Options</div>
            <UiDropdownItem>
              <template #icon>
                <div v-html="ICONS.add" />
              </template>
              Create New Project
            </UiDropdownItem>
            <UiDropdownItem>
              <template #icon>
                <div v-html="ICONS.settings" />
              </template>
              Project Settings
            </UiDropdownItem>
            <div class="dropdown-divider" />
            <UiDropdownItem
              v-for="project in projects"
              :key="project.id"
              show-chevron
            >
              {{ project.title }}
            </UiDropdownItem>
            <div class="dropdown-divider" />
            <UiDropdownItem danger>
              <template #icon>
                <div v-html="ICONS.logout" />
              </template>
              Delete Project
            </UiDropdownItem>
          </UiDropdown>
        </section>

        <!-- SECTION 7: Controlled Dropdown -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Controlled Dropdown
          </h2>
          <div class="mb-4">
            <button
              class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors"
              @click="controlledDropdown = !controlledDropdown"
            >
              {{ controlledDropdown ? "Close Dropdown" : "Open Dropdown" }}
            </button>
          </div>

          <UiDropdown
            v-model:open="controlledDropdown"
            label="Controlled Example"
            variant="outline"
          >
            <UiDropdownItem>This dropdown is controlled</UiDropdownItem>
            <UiDropdownItem>Click the button above to toggle</UiDropdownItem>
            <UiDropdownItem
              >Selecting this won't close the dropdown</UiDropdownItem
            >
          </UiDropdown>
        </section>

        <!-- SECTION 8: Icon-Only Dropdowns -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Icon-Only Dropdowns
          </h2>
          <div class="flex space-x-4">
            <UiDropdown
              v-for="(dropdown, index) in iconDropdowns"
              :key="index"
              icon-only
              :variant="dropdown.variant"
            >
              <template #icon>
                <div v-html="ICONS[dropdown.icon]" />
              </template>
              <UiDropdownItem v-for="(item, i) in dropdown.items" :key="i">
                {{ item }}
              </UiDropdownItem>
            </UiDropdown>
          </div>
        </section>

        <!-- SECTION 9: Disabled States -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Disabled State
          </h2>
          <div class="flex space-x-4">
            <UiDropdown label="Disabled Dropdown" disabled>
              <UiDropdownItem>You won't see this</UiDropdownItem>
            </UiDropdown>

            <UiDropdown label="With Disabled Items" variant="outline">
              <UiDropdownItem>Enabled Item</UiDropdownItem>
              <UiDropdownItem disabled>Disabled Item</UiDropdownItem>
              <UiDropdownItem>Enabled Item</UiDropdownItem>
            </UiDropdown>
          </div>
        </section>

        <!-- SECTION 10: Icon Positions -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Icon Position
          </h2>
          <div class="flex flex-wrap gap-4">
            <UiDropdown
              v-for="position in ['left', 'right']"
              :key="position"
              :label="`${
                position.charAt(0).toUpperCase() + position.slice(1)
              } Icon`"
              variant="outline"
              :icon-position="position"
            >
              <template #icon>
                <div v-html="ICONS.user" />
              </template>
              <UiDropdownItem>Option 1</UiDropdownItem>
              <UiDropdownItem>Option 2</UiDropdownItem>
            </UiDropdown>
          </div>
        </section>

        <!-- SECTION 11: Custom Chevrons -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Custom Chevrons
          </h2>
          <UiDropdown label="Actions" variant="primary">
            <UiDropdownItem
              v-for="chevron in customChevrons"
              :key="chevron.label"
              show-chevron
            >
              {{ chevron.label }}
              <template #chevron>
                <div v-html="ICONS[chevron.icon]" />
              </template>
            </UiDropdownItem>
          </UiDropdown>
        </section>

        <!-- SECTION 12: Custom Dropdown Arrow -->
        <section class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-gray-900">
            Custom Dropdown Arrow
          </h2>
          <UiDropdown label="More Options" variant="outline">
            <template #dropdown-arrow>
              <div v-html="ICONS.arrowDown" />
            </template>
            <UiDropdownItem v-for="n in 3" :key="n"
              >Option {{ n }}</UiDropdownItem
            >
          </UiDropdown>
        </section>
      </div>

      <!-- Footer -->
      <div class="mt-12 pt-8 border-t border-gray-200">
        <p class="text-center text-gray-500">
          This demo showcases the Dropdown component for the LearnConsult
          platform.
        </p>
      </div>
    </div>
  </div>
</template>

<style>
/* Dark mode dropdown overrides */
.dark :deep(.dropdown-header) {
  color: rgb(156, 163, 175); /* text-gray-400 */
}

.dark :deep([role="menuitem"]) {
  color: rgb(229, 231, 235); /* text-gray-200 */
}

.dark :deep([role="menuitem"]:hover) {
  background-color: rgb(55, 65, 81); /* bg-gray-700 */
}

.dark :deep([role="menuitem"].active) {
  background-color: rgb(67, 56, 202, 0.2); /* bg-indigo-700 with opacity */
  color: rgb(199, 210, 254); /* text-indigo-200 */
}

.dark :deep(.dropdown-divider) {
  border-color: rgb(75, 85, 99); /* border-gray-600 */
}

.dark :deep(.dropdown [role="menu"]) {
  background-color: rgb(31, 41, 55); /* bg-gray-800 */
  border-color: rgb(75, 85, 99); /* border-gray-600 */
}
</style>
