<script setup lang="ts">
import type { TableColumn, TableRow } from "~/types/table";

definePageMeta({
  layout: "components",
});

// Basic table data
const basicColumns: TableColumn[] = [
  { key: "id", label: "ID", sortable: false },
  { key: "name", label: "Name", sortable: false },
  { key: "email", label: "Email", sortable: false },
  { key: "role", label: "Role", sortable: false },
];

const basicRows: TableRow[] = [
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Editor" },
  { id: 4, name: "Alice Brown", email: "alice@example.com", role: "User" },
];

// Sortable table data
const sortableColumns: TableColumn[] = [
  { key: "id", label: "ID", sortable: true },
  { key: "name", label: "Name", sortable: true },
  { key: "email", label: "Email", sortable: true },
  { key: "role", label: "Role", sortable: true },
];

const sortableRows: TableRow[] = [
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Editor" },
  { id: 4, name: "Alice Brown", email: "alice@example.com", role: "User" },
  { id: 5, name: "Charlie Wilson", email: "charlie@example.com", role: "User" },
];

const sortBy = ref("id");
const sortDirection = ref("asc");

// Pagination table data
const paginationColumns: TableColumn[] = [
  { key: "id", label: "ID", sortable: false },
  { key: "name", label: "Name", sortable: false },
  { key: "email", label: "Email", sortable: false },
  { key: "role", label: "Role", sortable: false },
];

const paginatedRows: TableRow[] = [
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Editor" },
  { id: 4, name: "Alice Brown", email: "alice@example.com", role: "User" },
  { id: 5, name: "Charlie Wilson", email: "charlie@example.com", role: "User" },
];

const currentPage = ref(1);

// Selection table data
const selectionColumns: TableColumn[] = [
  { key: "id", label: "ID", sortable: false, resizable: true },
  { key: "name", label: "Name", sortable: false, resizable: true },
  { key: "email", label: "Email", sortable: false, resizable: true },
  { key: "role", label: "Role", sortable: false, resizable: true },
];

const selectionRows: TableRow[] = [
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Editor" },
  { id: 4, name: "Alice Brown", email: "alice@example.com", role: "User" },
];

const selectedRows = ref<number[]>([]);

// Loading table data
const loadingColumns: TableColumn[] = [
  { key: "id", label: "ID", sortable: false },
  { key: "name", label: "Name", sortable: false },
  { key: "email", label: "Email", sortable: false },
  { key: "role", label: "Role", sortable: false },
];

const loadingRows: TableRow[] = [
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Editor" },
];

const isLoading = ref(false);

const toggleLoading = () => {
  isLoading.value = !isLoading.value;
};

// Error table data
const errorColumns: TableColumn[] = [
  { key: "id", label: "ID", sortable: false },
  { key: "name", label: "Name", sortable: false },
  { key: "email", label: "Email", sortable: false },
  { key: "role", label: "Role", sortable: false },
];

const errorRows: TableRow[] = [
  { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
  { id: 2, name: "Jane Smith", email: "jane@example.com", role: "User" },
  { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Editor" },
];

const tableError = ref<string | null>(null);

const toggleError = () => {
  tableError.value = tableError.value
    ? null
    : "Failed to load data. Please try again later.";
};

interface StudentRow extends TableRow {
  id: string;
  avatar: string;
  name: string;
  email: string;
  enrollmentDate: string;
  coursesCount: number;
  status: "Active" | "Pending" | "Inactive";
  progress: number;
}

// Student table data
const studentColumns: TableColumn[] = [
  { key: "select", label: "", width: 40 },
  { key: "avatar", label: "", width: 40 },
  { key: "name", label: "Name", sortable: true },
  { key: "email", label: "Email", sortable: true },
  { key: "enrollmentDate", label: "Enrollment Date", sortable: true },
  { key: "coursesCount", label: "Courses", sortable: true },
  { key: "status", label: "Status", sortable: true },
  { key: "progress", label: "Progress", sortable: true },
  { key: "actions", label: "", width: 40 },
];

const studentRows: StudentRow[] = [
  {
    id: "1",
    avatar:
      "https://ui-avatars.com/api/?name=Emily+Martinez&background=6366f1&color=fff",
    name: "Emily Martinez",
    email: "emily.martinez@example.com",
    enrollmentDate: "Feb 18, 2025",
    coursesCount: 2,
    status: "Active",
    progress: 95,
  },
  {
    id: "2",
    avatar:
      "https://ui-avatars.com/api/?name=Michael+Rodriguez&background=6366f1&color=fff",
    name: "Michael Rodriguez",
    email: "michael.rodriguez@example.com",
    enrollmentDate: "Feb 3, 2025",
    coursesCount: 2,
    status: "Active",
    progress: 62,
  },
  {
    id: "3",
    avatar:
      "https://ui-avatars.com/api/?name=David+Nguyen&background=6366f1&color=fff",
    name: "David Nguyen",
    email: "david.nguyen@example.com",
    enrollmentDate: "Mar 5, 2025",
    coursesCount: 3,
    status: "Active",
    progress: 70,
  },
  {
    id: "4",
    avatar:
      "https://ui-avatars.com/api/?name=Sophia+Lee&background=6366f1&color=fff",
    name: "Sophia Lee",
    email: "sophia.lee@example.com",
    enrollmentDate: "Feb 22, 2025",
    coursesCount: 3,
    status: "Active",
    progress: 75,
  },
];
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Table Component</h1>
    <p class="text-gray-600 mb-8">
      A versatile table component for displaying structured data with sorting,
      filtering, and pagination capabilities.
    </p>

    <!-- Basic Table -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Table</h2>
      <p class="text-gray-600 mb-6">A simple table with basic functionality.</p>
      <UiTable
        v-model="basicRows"
        :columns="basicColumns"
        :loading="false"
        :error="null"
        hover
        striped
      />
    </div>

    <!-- Table with Sorting -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Table with Sorting
      </h2>
      <p class="text-gray-600 mb-6">
        Tables can be sorted by clicking on column headers.
      </p>
      <UiTable
        v-model="sortableRows"
        :columns="sortableColumns"
        :loading="false"
        :error="null"
        :sortable="true"
        :sort-by="sortBy"
        :sort-direction="sortDirection"
        hover
        striped
        @update:sort-by="sortBy = $event"
        @update:sort-direction="sortDirection = $event"
      />
    </div>

    <!-- Table with Pagination -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Table with Pagination
      </h2>
      <p class="text-gray-600 mb-6">
        Tables can include pagination for handling large datasets.
      </p>
      <UiTable
        v-model="paginatedRows"
        :columns="paginationColumns"
        :loading="false"
        :error="null"
        :pagination="true"
        :total="100"
        :per-page="10"
        :page="currentPage"
        hover
        striped
        @update:page="currentPage = $event"
      />
    </div>

    <!-- Table with Row Selection -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Table with Row Selection
      </h2>
      <p class="text-gray-600 mb-6">
        Tables can allow users to select rows for bulk actions.
      </p>
      <UiTable
        v-model="selectionRows"
        :columns="selectionColumns"
        :loading="false"
        :error="null"
        :selectable="true"
        :selected-rows="selectedRows"
        hover
        striped
        @update:selected-rows="selectedRows = $event"
      />
      <div class="mt-4 text-sm text-gray-600">
        Selected rows: {{ selectedRows.length }}
      </div>
    </div>

    <!-- Table with Row Resize -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Table with Row Resize
      </h2>
      <p class="text-gray-600 mb-6">Tables can allow users to resize rows.</p>
      <UiTable
        v-model="selectionRows"
        :columns="selectionColumns"
        :loading="false"
        :error="null"
        resizable
        :selected-rows="selectedRows"
        hover
        striped
        @update:selected-rows="selectedRows = $event"
      />
      <div class="mt-4 text-sm text-gray-600">
        Selected rows: {{ selectedRows.length }}
      </div>
    </div>

    <!-- Table with Loading State -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Table with Loading State
      </h2>
      <p class="text-gray-600 mb-6">
        Tables can display a loading state while data is being fetched.
      </p>
      <UiTable
        v-model="loadingRows"
        :columns="loadingColumns"
        :loading="isLoading"
        :error="null"
        hover
        striped
      />
      <div class="mt-4">
        <UiButton @click="toggleLoading">
          {{ isLoading ? "Hide Loading" : "Show Loading" }}
        </UiButton>
      </div>
    </div>

    <!-- Table with Error State -->
    <div class="bg-white rounded-lg shadow-sm p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Table with Error State
      </h2>
      <p class="text-gray-600 mb-6">
        Tables can display an error message when data fetching fails.
      </p>
      <UiTable
        v-model="errorRows"
        :columns="errorColumns"
        :loading="false"
        :error="tableError"
        hover
        striped
      />
      <div class="mt-4">
        <UiButton @click="toggleError">
          {{ tableError ? "Hide Error" : "Show Error" }}
        </UiButton>
      </div>
    </div>

    <!-- Student List Table -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Student List</h2>
      <p class="text-gray-600 mb-6">
        A table displaying student information with custom cell rendering.
      </p>
      <UiTable
        v-model="studentRows"
        :columns="studentColumns"
        :loading="false"
        :error="null"
        hover
        striped
        selectable
      >
        <template #cell-avatar="{ row }">
          <div class="w-8 h-8 rounded-full overflow-hidden bg-indigo-100">
            <img
              :src="(row as unknown as StudentRow).avatar"
              :alt="(row as unknown as StudentRow).name"
              class="w-full h-full object-cover"
              @error="(e: Event) => { 
                const target = e.target as HTMLImageElement;
                if (target) {
                  target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent((row as unknown as StudentRow).name)}&background=6366f1&color=fff`;
                }
              }"
            />
          </div>
        </template>
        <template #cell-name="{ row }">
          <div class="font-medium text-gray-900">
            {{ (row as unknown as StudentRow).name }}
          </div>
        </template>
        <template #cell-email="{ row }">
          <div class="text-gray-500">
            {{ (row as unknown as StudentRow).email }}
          </div>
        </template>
        <template #cell-enrollmentDate="{ row }">
          <div class="text-gray-600">
            {{ (row as unknown as StudentRow).enrollmentDate }}
          </div>
        </template>
        <template #cell-coursesCount="{ row }">
          <div class="text-gray-600">
            {{ (row as unknown as StudentRow).coursesCount }}
          </div>
        </template>
        <template #cell-status="{ row }">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            :class="{
              'bg-green-100 text-green-800': (row as unknown as StudentRow).status === 'Active',
              'bg-yellow-100 text-yellow-800': (row as unknown as StudentRow).status === 'Pending',
              'bg-red-100 text-red-800': (row as unknown as StudentRow).status === 'Inactive'
            }"
          >
            {{ (row as unknown as StudentRow).status }}
          </span>
        </template>
        <template #cell-progress="{ row }">
          <div class="flex items-center">
            <div class="flex-grow">
              <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-300"
                  :class="{
                    'bg-green-500': (row as unknown as StudentRow).progress >= 80,
                    'bg-blue-500': (row as unknown as StudentRow).progress >= 60 && (row as unknown as StudentRow).progress < 80,
                    'bg-yellow-500': (row as unknown as StudentRow).progress < 60
                  }"
                  :style="{ width: `${(row as unknown as StudentRow).progress}%` }"
                />
              </div>
            </div>
            <span class="ml-2 text-sm text-gray-600"
              >{{ (row as unknown as StudentRow).progress }}%</span
            >
          </div>
        </template>
        <template #cell-actions>
          <div class="flex justify-end">
            <button
              type="button"
              class="text-gray-400 hover:text-gray-600 focus:outline-none"
              @click="
                () => {
                  /* Handle action menu click */
                }
              "
            >
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                />
              </svg>
            </button>
          </div>
        </template>
      </UiTable>
    </div>
  </div>
</template>
