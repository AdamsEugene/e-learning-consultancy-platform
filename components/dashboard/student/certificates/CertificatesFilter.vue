<!-- components/dashboard/certificates/CertificatesFilter.vue -->
<script setup lang="ts">
import { ref, watch } from "vue";

interface Props {
  activeTab: "all" | "featured" | "in-progress";
  selectedStatus: "all" | "verified" | "pending" | "expired";
  searchQuery: string;
  isLoaded: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(["tab-change", "filter-change"]);

// Local state for form inputs
const statusFilter = ref(props.selectedStatus);
const searchInput = ref(props.searchQuery);

// Watch for prop changes
watch(
  () => props.selectedStatus,
  (newVal) => {
    statusFilter.value = newVal;
  }
);

watch(
  () => props.searchQuery,
  (newVal) => {
    searchInput.value = newVal;
  }
);

// Handle tab change
const changeTab = (tab: "all" | "featured" | "in-progress") => {
  emit("tab-change", tab);
};

// Handle filter change
const applyFilters = () => {
  emit("filter-change", statusFilter.value, searchInput.value);
};

// Apply filters when status changes
watch(statusFilter, () => {
  applyFilters();
});

// Apply filters on enter key in search
const handleSearchKeyDown = (event: KeyboardEvent) => {
  if (event.key === "Enter") {
    applyFilters();
  }
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 transform transition-all duration-500"
    :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    style="transition-delay: 200ms"
  >
    <!-- Tabs -->
    <div class="flex flex-wrap border-b border-gray-200">
      <button
        class="mr-8 py-4 text-sm font-medium transition-colors relative"
        :class="
          activeTab === 'all'
            ? 'text-indigo-600'
            : 'text-gray-500 hover:text-gray-700'
        "
        @click="changeTab('all')"
      >
        All Certificates
        <span
          class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 transform transition-transform duration-300"
          :class="activeTab === 'all' ? 'scale-x-100' : 'scale-x-0'"
        ></span>
      </button>

      <button
        class="mr-8 py-4 text-sm font-medium transition-colors relative"
        :class="
          activeTab === 'featured'
            ? 'text-indigo-600'
            : 'text-gray-500 hover:text-gray-700'
        "
        @click="changeTab('featured')"
      >
        Featured
        <span
          class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 transform transition-transform duration-300"
          :class="activeTab === 'featured' ? 'scale-x-100' : 'scale-x-0'"
        ></span>
      </button>

      <button
        class="mr-8 py-4 text-sm font-medium transition-colors relative"
        :class="
          activeTab === 'in-progress'
            ? 'text-indigo-600'
            : 'text-gray-500 hover:text-gray-700'
        "
        @click="changeTab('in-progress')"
      >
        In Progress
        <span
          class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 transform transition-transform duration-300"
          :class="activeTab === 'in-progress' ? 'scale-x-100' : 'scale-x-0'"
        ></span>
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 items-center pt-4">
      <!-- Status filter -->
      <div class="w-full sm:w-auto">
        <label
          for="status-filter"
          class="block text-xs font-medium text-gray-500 mb-1"
          >Status</label
        >
        <select
          id="status-filter"
          v-model="statusFilter"
          class="w-full sm:w-auto border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
          <option value="all">All Statuses</option>
          <option value="verified">Verified</option>
          <option value="pending">Pending</option>
          <option value="expired">Expired</option>
        </select>
      </div>

      <!-- Search -->
      <div class="w-full sm:w-auto flex-grow relative">
        <label for="search" class="block text-xs font-medium text-gray-500 mb-1"
          >Search</label
        >
        <div class="relative">
          <input
            id="search"
            type="text"
            v-model="searchInput"
            placeholder="Search certificates, courses, skills..."
            class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            @keydown="handleSearchKeyDown"
          />
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <button
            class="absolute right-2 top-1.5 text-indigo-600 hover:text-indigo-800 p-1 rounded transition-colors"
            @click="applyFilters"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Underline animation for tabs */
.scale-x-0 {
  transform: scaleX(0);
}

.scale-x-100 {
  transform: scaleX(1);
}

/* Input focus state */
.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0
    var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0
    calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow),
    var(--tw-shadow, 0 0 #0000);
  --tw-ring-opacity: 1;
  --tw-ring-color: rgba(99, 102, 241, var(--tw-ring-opacity));
  outline: none;
  border-color: transparent;
}
</style>
