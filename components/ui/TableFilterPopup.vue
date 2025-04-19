<!-- components/ui/TableFilterPopup.vue -->
<script setup lang="ts">
import { ref, watch } from "vue";
import type { TableColumn } from "~/types/table";

const props = defineProps<{
  column: TableColumn;
  modelValue: string;
  position: { top: number; left: number };
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
  (e: "close"): void;
}>();

const filterValue = ref(props.modelValue);

watch(
  () => props.modelValue,
  (newValue) => {
    filterValue.value = newValue;
  }
);

const handleInput = (value: string) => {
  filterValue.value = value;
  emit("update:modelValue", value);
};

const handleClear = () => {
  filterValue.value = "";
  emit("update:modelValue", "");
};

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  if (!target.closest(".ui-table-filter-popup")) {
    emit("close");
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
  <div
    class="ui-table-filter-popup"
    :style="{
      position: 'absolute',
      top: `${position.top}px`,
      left: `${position.left}px`,
      zIndex: 50,
    }"
  >
    <div
      class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 min-w-[200px]"
    >
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-medium text-gray-700">
          Filter: {{ column.label }}
        </h3>
        <button
          type="button"
          class="text-gray-400 hover:text-gray-500"
          @click="emit('close')"
        >
          <span class="sr-only">Close</span>
          <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>

      <!-- Text filter -->
      <template v-if="!column.type || column.type === 'text'">
        <input
          type="text"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          :value="filterValue"
          placeholder="Filter text..."
          @input="handleInput(($event.target as HTMLInputElement).value)"
        />
      </template>

      <!-- Number filter -->
      <template v-else-if="column.type === 'number'">
        <input
          type="number"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          :value="filterValue"
          placeholder="Filter number..."
          @input="handleInput(($event.target as HTMLInputElement).value)"
        />
      </template>

      <!-- Select filter -->
      <template v-else-if="column.type === 'select' && column.options">
        <select
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          :value="filterValue"
          @change="handleInput(($event.target as HTMLSelectElement).value)"
        >
          <option value="">All</option>
          <option
            v-for="option in column.options"
            :key="option"
            :value="option"
          >
            {{ option }}
          </option>
        </select>
      </template>

      <!-- Date filter -->
      <template v-else-if="column.type === 'date'">
        <input
          type="date"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          :value="filterValue"
          @input="handleInput(($event.target as HTMLInputElement).value)"
        />
      </template>

      <div class="mt-3 flex justify-end space-x-2">
        <button
          type="button"
          class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          @click="handleClear"
        >
          Clear
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ui-table-filter-popup {
  animation: popup 0.2s ease-out;
}

@keyframes popup {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
