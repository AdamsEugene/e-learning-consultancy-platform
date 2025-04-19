<script setup lang="ts">
import type { FormDropdownItem } from "~/components/ui/Dropdown.vue";

definePageMeta({
  layout: "components",
});

// Simple dropdown example data
const simpleItems = ref<FormDropdownItem[]>([
  { id: 1, name: "Option 1" },
  { id: 2, name: "Option 2" },
  { id: 3, name: "Option 3" },
  { id: 4, name: "Option 4", disabled: true },
]);
const selectedSimpleItem = ref<FormDropdownItem | null>(null);

// Countries dropdown example data
const countries = ref<FormDropdownItem[]>([
  { id: "us", name: "United States" },
  { id: "ca", name: "Canada" },
  { id: "mx", name: "Mexico" },
  { id: "uk", name: "United Kingdom" },
  { id: "fr", name: "France" },
  { id: "de", name: "Germany" },
  { id: "jp", name: "Japan" },
  { id: "au", name: "Australia" },
  { id: "br", name: "Brazil" },
  { id: "in", name: "India" },
  { id: "cn", name: "China" },
]);
const selectedCountry = ref<FormDropdownItem | null>(null);

// Tags multi-select example data
const tags = ref<FormDropdownItem[]>([
  { id: 1, name: "Frontend" },
  { id: 2, name: "Backend" },
  { id: 3, name: "Design" },
  { id: 4, name: "DevOps" },
  { id: 5, name: "Mobile" },
  { id: 6, name: "AI/ML" },
  { id: 7, name: "Database" },
  { id: 8, name: "Security" },
]);
const selectedTags = ref<FormDropdownItem[]>([]);

// Required field example
const selectedRequired = ref<FormDropdownItem | null>(null);

// Projects example with add button
const projects = ref<FormDropdownItem[]>([
  { id: 1, name: "Website Redesign" },
  { id: 2, name: "Mobile App Development" },
  { id: 3, name: "E-commerce Platform" },
]);
const selectedProject = ref<FormDropdownItem | null>(null);

// User profile dropdown
const userOptions = ref<FormDropdownItem[]>([
  { id: "settings", name: "Account Settings" },
  { id: "billing", name: "Billing & Payments" },
  { id: "teams", name: "Teams & Organizations" },
  { id: "help", name: "Help & Support" },
]);

// Helper functions
const removeTag = (tag: FormDropdownItem) => {
  selectedTags.value = selectedTags.value.filter((t) => t.id !== tag.id);
};

const handleAddProject = () => {
  alert("Add new project clicked!");
  // In a real app, you might open a modal here
};
</script>

<template>
  <div class="space-y-8 p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900">Dropdown Examples</h1>

    <!-- Basic dropdown -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">Basic Dropdown</h2>
      <UiDropdown
        label="Select an option"
        :items="simpleItems"
        :selected-item="selectedSimpleItem"
        :select-item="(item: any) => (selectedSimpleItem = item)"
      />
    </div>

    <!-- Dropdown with search -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">With Search</h2>
      <UiDropdown
        label="Country"
        placeholder="Select a country"
        :items="countries"
        :selected-item="selectedCountry"
        :select-item="(item: any) => (selectedCountry = item)"
        with-search
      />
    </div>

    <!-- Multiple selection dropdown -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">Multiple Selection</h2>
      <UiDropdown
        label="Select tags"
        placeholder="Select one or more tags"
        :items="tags"
        :selected-item="selectedTags"
        :select-item="(items: any) => (selectedTags = items)"
        multiple
        with-search
      />
      <div v-if="selectedTags.length > 0" class="mt-2 flex flex-wrap gap-2">
        <div
          v-for="tag in selectedTags"
          :key="tag.id"
          class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full flex items-center"
        >
          {{ tag.name }}
          <button
            class="ml-1 text-indigo-600 hover:text-indigo-800"
            @click="removeTag(tag)"
          >
            ×
          </button>
        </div>
      </div>
    </div>

    <!-- Custom styles and error state -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">With Error State</h2>
      <UiDropdown
        label="Required field"
        placeholder="Please select an option"
        :items="simpleItems"
        :selected-item="selectedRequired"
        :select-item="(item: any) => (selectedRequired = item)"
        error="This field is required"
      />
    </div>

    <!-- Disabled state -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">Disabled State</h2>
      <UiDropdown
        label="Unavailable options"
        placeholder="Currently unavailable"
        :items="simpleItems"
        :selected-item="null"
        disabled
      />
    </div>

    <!-- Loading state -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">Loading State</h2>
      <UiDropdown
        label="Loading items..."
        placeholder="Please wait..."
        :items="[]"
        :selected-item="null"
        loading
      />
    </div>

    <!-- With action button -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">With Add Button</h2>
      <UiDropdown
        label="Projects"
        placeholder="Select a project"
        :items="projects"
        :selected-item="selectedProject"
        :select-item="(item: any) => (selectedProject = item)"
        with-search
        show-add-button
        @add-item="handleAddProject"
      />
    </div>

    <!-- User profile dropdown -->
    <div class="mb-6">
      <h2 class="text-lg font-medium mb-3">Profile Dropdown</h2>
      <div class="flex justify-end">
        <UiDropdown
          :items="userOptions"
          user-name="Jane Smith"
          user-email="jane.smith@example.com"
          show-profile
          for-profile
          class-name="w-60"
        />
      </div>
    </div>
  </div>
</template>
