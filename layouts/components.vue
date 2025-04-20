<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold text-gray-900">UI Components</h1>
          <NuxtLink to="/" class="text-indigo-600 hover:text-indigo-800">
            Back to Home
          </NuxtLink>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <div class="w-full md:w-64 flex-shrink-0">
          <nav class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Components</h2>
            <ul class="space-y-2">
              <li v-for="item in navigationItems" :key="item.path">
                <NuxtLink
                  :to="item.path"
                  class="block px-3 py-2 rounded-md text-sm font-medium"
                  :class="[
                    $route.path === item.path
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-700 hover:bg-gray-100',
                  ]"
                >
                  {{ item.label }}
                </NuxtLink>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface NavigationItem {
  label: string;
  path: string;
}

const navigationItems: NavigationItem[] = [
  { label: "Overview", path: "/components" },
  { label: "Table", path: "/components/table" },
  { label: "Button", path: "/components/button" },
  { label: "Dropdown", path: "/components/dropdown" },
  { label: "Form", path: "/components/form" },
  { label: "Card", path: "/components/card" },
  { label: "Modal", path: "/components/modal" },
  { label: "Confirm Modal", path: "/components/confirm-modal" },
];
</script>
