<script setup lang="ts">
interface Props {
  activeTab: "overview" | "curriculum" | "instructor" | "reviews";
}

interface Emits {
  (
    e: "update:activeTab",
    tab: "overview" | "curriculum" | "instructor" | "reviews"
  ): void;
}

defineProps<Props>();
defineEmits<Emits>();

const tabs = ["curriculum", "overview", "instructor", "reviews"] as const;
</script>

<template>
  <div class="border-b border-gray-200 mb-8">
    <nav class="-mb-px flex space-x-8">
      <button
        v-for="tab in tabs"
        :key="tab"
        class="py-4 px-1 border-b-2 font-medium text-sm"
        :class="[
          activeTab === tab
            ? 'border-indigo-500 text-indigo-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
        ]"
        @click="$emit('update:activeTab', tab)"
      >
        {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
      </button>
    </nav>
  </div>
</template>
