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
              <li>
                <NuxtLink
                  to="/components"
                  class="block px-3 py-2 rounded-md text-sm font-medium"
                  :class="[
                    $route.path === '/components'
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-700 hover:bg-gray-100',
                  ]"
                >
                  Overview
                </NuxtLink>
              </li>
              <li>
                <NuxtLink
                  to="/components/table"
                  class="block px-3 py-2 rounded-md text-sm font-medium"
                  :class="[
                    $route.path === '/components/table'
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-700 hover:bg-gray-100',
                  ]"
                >
                  Table
                </NuxtLink>
              </li>
              <li>
                <NuxtLink
                  to="/components/button"
                  class="block px-3 py-2 rounded-md text-sm font-medium"
                  :class="[
                    $route.path === '/components/button'
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-700 hover:bg-gray-100',
                  ]"
                >
                  Button
                </NuxtLink>
              </li>
              <li>
                <NuxtLink
                  to="/components/form"
                  class="block px-3 py-2 rounded-md text-sm font-medium"
                  :class="[
                    $route.path === '/components/form'
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-700 hover:bg-gray-100',
                  ]"
                >
                  Form
                </NuxtLink>
              </li>
              <li>
                <NuxtLink
                  to="/components/card"
                  class="block px-3 py-2 rounded-md text-sm font-medium"
                  :class="[
                    $route.path === '/components/card'
                      ? 'bg-indigo-100 text-indigo-700'
                      : 'text-gray-700 hover:bg-gray-100',
                  ]"
                >
                  Card
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
// Layout for components pages
</script>
