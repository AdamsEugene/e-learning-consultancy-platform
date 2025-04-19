<script setup lang="ts">
import type {
  TableColumn,
  TableRow,
  TableProps,
  SortConfig,
  FilterConfig,
  PaginationConfig,
} from "~/types/table";
import { ref, computed, watch, inject } from "vue";
import type { Ref } from "vue";

interface TableInstance {
  data: Ref<TableRow[]>;
  loading: Ref<boolean>;
  error: Ref<Error | null>;
  columns: Ref<TableColumn[]>;
  sortConfig: Ref<SortConfig>;
  filters: Ref<FilterConfig[]>;
  pagination: Ref<PaginationConfig>;
  selectedRows: Ref<TableRow[]>;
  filterPopupState: Ref<{
    isOpen: boolean;
    column: TableColumn | null;
    position: { top: number; left: number } | null;
  }>;
  visibleColumns: Ref<TableColumn[]>;
  allSelected: Ref<boolean>;
  setFilter: (key: string, value: string) => void;
  clearFilter: (key: string) => void;
  clearAllFilters: () => void;
  closeFilterPopup: () => void;
  hasActiveFilter: (key: string) => boolean;
  openFilterPopup: (column: TableColumn, event: MouseEvent) => void;
  toggleSort: (column: TableColumn) => void;
  toggleRowSelection: (row: TableRow) => void;
  toggleAllRows: () => void;
  getRowKey: (row: TableRow) => string | number;
  isSelected: (row: TableRow) => boolean;
  setPage: (page: number) => void;
}

const props = withDefaults(defineProps<TableProps>(), {
  modelValue: () => [],
  columns: () => [],
  loading: false,
  selectable: false,
  resizable: false,
  sortable: false,
  filterable: false,
  pagination: false,
  perPage: 10,
  page: 1,
  total: 0,
  bordered: false,
  striped: false,
  hover: false,
  compact: false,
  responsive: false,
  stickyHeader: false,
  stickyFooter: false,
  showHeader: true,
  showFooter: true,
  showPagination: true,
  showFilters: false,
  showColumnVisibilityToggle: false,
  showResizeHandles: false,
  rowKey: "id",
  loadingMessage: "Loading...",
  errorMessage: "An error occurred while loading data.",
  emptyMessage: "No data available.",
});

const emit = defineEmits<{
  (e: "update:modelValue" | "selection-change", value: TableRow[]): void;
  (e: "sort-change", config: SortConfig): void;
  (e: "filter-change", filters: FilterConfig[]): void;
  (e: "pagination-change", config: PaginationConfig): void;
  (
    e: "row-click" | "row-dblclick" | "row-contextmenu",
    row: TableRow,
    event: MouseEvent
  ): void;
}>();

// Create a mutable ref for the data
const localData = ref<TableRow[]>(props.modelValue as TableRow[]);

// Filter popup state
const activeFilterColumn = ref<string | null>(null);
const filterPopupPosition = ref({ top: 0, left: 0 });
const filterValues = ref<Record<string, string>>({});

// Initialize filter values from existing filters
watch(
  () => props.options?.defaultFilters,
  (filters) => {
    if (filters) {
      filters.forEach((filter) => {
        filterValues.value[filter.key] = filter.value;
      });
    }
  },
  { immediate: true }
);

const table = inject<TableInstance>("table");

// Provide default values for table instance
const tableInstance = {
  data: ref<TableRow[]>(props.modelValue as TableRow[]),
  loading: ref(false),
  error: ref<Error | null>(null),
  columns: ref<TableColumn[]>(props.columns),
  sortConfig: ref<SortConfig>({
    key: "",
    direction: "asc" as "asc" | "desc" | null,
  }),
  filters: ref<FilterConfig[]>([]),
  pagination: ref<PaginationConfig>({
    page: props.page,
    perPage: props.perPage,
    total: props.total,
  }),
  selectedRows: ref<TableRow[]>([]),
  visibleColumns: ref<TableColumn[]>(props.columns),
  allSelected: ref(false),
  setFilter: (key: string, value: string) => {
    const filters = tableInstance.filters.value;
    const existingFilter = filters.find((f) => f.key === key);
    if (existingFilter) {
      existingFilter.value = value;
    } else {
      filters.push({ key, value });
    }
  },
  clearFilter: (key: string) => {
    const filters = tableInstance.filters.value;
    const index = filters.findIndex((f) => f.key === key);
    if (index !== -1) {
      filters.splice(index, 1);
    }
  },
  clearAllFilters: () => {
    tableInstance.filters.value = [];
  },
  toggleSort: (column: TableColumn) => {
    const currentSort = tableInstance.sortConfig.value;
    if (currentSort.key === column.key) {
      currentSort.direction = currentSort.direction === "asc" ? "desc" : "asc";
    } else {
      currentSort.key = column.key;
      currentSort.direction = "asc";
    }
  },
  toggleRowSelection: (row: TableRow) => {
    const selectedRows = tableInstance.selectedRows.value;
    const index = selectedRows.findIndex(
      (r) => tableInstance.getRowKey(r) === tableInstance.getRowKey(row)
    );
    if (index === -1) {
      selectedRows.push(row);
    } else {
      selectedRows.splice(index, 1);
    }
  },
  toggleAllRows: () => {
    const allSelected = tableInstance.allSelected.value;
    if (allSelected) {
      tableInstance.selectedRows.value = [];
    } else {
      tableInstance.selectedRows.value = [...tableInstance.data.value];
    }
    tableInstance.allSelected.value = !allSelected;
  },
  getRowKey: (row: TableRow) => {
    return row[props.rowKey] as string | number;
  },
  isSelected: (row: TableRow) => {
    return tableInstance.selectedRows.value.some(
      (r) => tableInstance.getRowKey(r) === tableInstance.getRowKey(row)
    );
  },
  setPage: (page: number) => {
    tableInstance.pagination.value.page = page;
  },
};

// Use the provided table instance or fall back to our default one
const tableRef = table || tableInstance;

const tableData = computed(() => {
  let currentData = props.options?.fetchData
    ? tableRef.data.value
    : localData.value;

  // Apply filters
  if (tableRef.filters.value.length > 0) {
    currentData = currentData.filter((row) => {
      return tableRef.filters.value.every((filter) => {
        const value = String(row[filter.key]).toLowerCase();
        const filterValue = filter.value.toLowerCase();
        return value.includes(filterValue);
      });
    });
  }

  // Apply sorting
  if (tableRef.sortConfig.value.key && tableRef.sortConfig.value.direction) {
    const key = tableRef.sortConfig.value.key;
    const direction = tableRef.sortConfig.value.direction;

    return [...currentData].sort((a, b) => {
      const aValue = a[key];
      const bValue = b[key];

      // Handle undefined or null values
      if (aValue == null && bValue == null) return 0;
      if (aValue == null) return 1;
      if (bValue == null) return -1;

      // Compare based on value types
      if (typeof aValue === "string" && typeof bValue === "string") {
        return direction === "asc"
          ? aValue.localeCompare(bValue)
          : bValue.localeCompare(aValue);
      }

      if (typeof aValue === "number" && typeof bValue === "number") {
        return direction === "asc" ? aValue - bValue : bValue - aValue;
      }

      // Convert to strings for comparison if types don't match
      const aStr = String(aValue);
      const bStr = String(bValue);
      return direction === "asc"
        ? aStr.localeCompare(bStr)
        : bStr.localeCompare(aStr);
    });
  }

  return currentData;
});

const visibleColumns = computed(() => tableRef.visibleColumns.value);
const isAllSelected = computed(() => tableRef.allSelected.value);
const hasError = computed(() => tableRef.error.value !== null);
const paginationInfo = computed(() => tableRef.pagination.value);
const sortingConfig = computed(() => tableRef.sortConfig.value);
const selectedRowsCount = computed(() => tableRef.selectedRows.value.length);
const activeFilters = computed(() => tableRef.filters.value);

// Watch for external changes
watch(
  () => props.modelValue,
  (newValue) => {
    if (!props.options?.fetchData) {
      localData.value = newValue as TableRow[];
    }
  }
);

// Watch for selection changes
watch(
  () => tableRef.selectedRows.value,
  (newValue) => {
    emit("selection-change", newValue);
  }
);

// Emit events when internal state changes
watch(sortingConfig, (newValue) => {
  emit("sort-change", newValue);
});

watch(tableRef.filters, (newValue) => {
  emit("filter-change", newValue);
});

watch(paginationInfo, (newValue) => {
  emit("pagination-change", newValue);
});

// Row event handlers
const handleRowClick = (row: TableRow, event: MouseEvent) => {
  if (props.selectable) {
    // Don't emit click event when clicking for selection
    return;
  }
  emit("row-click", row, event);
};

const handleRowDoubleClick = (row: TableRow, event: MouseEvent) => {
  if (props.selectable) {
    // Don't emit double click event when clicking for selection
    return;
  }
  emit("row-dblclick", row, event);
};

const handleRowContextMenu = (row: TableRow, event: MouseEvent) => {
  emit("row-contextmenu", row, event);
};

// Filter methods
const openFilterPopup = (column: TableColumn, event: MouseEvent) => {
  event.stopPropagation(); // Prevent sort toggle

  const target = event.currentTarget as HTMLElement;
  const rect = target.getBoundingClientRect();

  activeFilterColumn.value = column.key;
  filterPopupPosition.value = {
    top: rect.bottom + window.scrollY,
    left: rect.left + window.scrollX,
  };
};

const closeFilterPopup = () => {
  activeFilterColumn.value = null;
};

const applyFilter = (column: TableColumn) => {
  const value = filterValues.value[column.key] || "";
  if (value.trim()) {
    tableRef.setFilter(column.key, value.trim());
  } else {
    tableRef.clearFilter(column.key);
  }
  closeFilterPopup();
};

const clearFilter = (column: TableColumn) => {
  tableRef.clearFilter(column.key);
  filterValues.value[column.key] = "";
  closeFilterPopup();
};

const clearAllFilters = () => {
  tableRef.clearAllFilters();
  filterValues.value = {};
};

// Helper functions
const getColumnClasses = (column: TableColumn) => ({
  "ui-table__cell--sortable": column.sortable && props.sortable,
  "ui-table__cell--resizable": column.resizable && props.resizable,
  [`ui-table__cell--align-${column.align || "left"}`]: true,
});

const getRowClasses = (row: TableRow) => ({
  "ui-table__row": true,
  "ui-table__row--selected": tableRef.isSelected(row),
  "ui-table__row--selectable": props.selectable,
  [`${props.rowClass}`]: props.rowClass,
});

interface ResizeState {
  active: boolean;
  columnKey: string;
  startX: number;
  startWidth: number;
}

// Add resize state
const resizing = ref<ResizeState | null>(null);

// Add columnWidths state if not already present
const columnWidths = ref<Record<string, number>>({});

// Add resize methods
const startResize = (columnKey: string, event: MouseEvent) => {
  const th = (event.target as HTMLElement).closest("th");
  if (!th) return;

  event.preventDefault();
  const startWidth = th.offsetWidth;

  resizing.value = {
    active: true,
    columnKey,
    startX: event.pageX,
    startWidth,
  };

  // Store initial width
  columnWidths.value[columnKey] = startWidth;

  // Add event listeners
  document.addEventListener("mousemove", handleResize);
  document.addEventListener("mouseup", stopResize);

  // Prevent text selection
  document.body.style.userSelect = "none";
};

const handleResize = (event: MouseEvent) => {
  if (!resizing.value?.active) return;

  const { columnKey, startX, startWidth } = resizing.value;
  const delta = event.pageX - startX;
  const newWidth = Math.max(50, startWidth + delta); // Minimum width of 50px

  columnWidths.value[columnKey] = newWidth;
};

const stopResize = () => {
  if (!resizing.value) return;

  document.removeEventListener("mousemove", handleResize);
  document.removeEventListener("mouseup", stopResize);
  document.body.style.userSelect = "";

  resizing.value = null;
};

// Helper function to check if a column has an active filter
const hasActiveFilter = (columnKey: string): boolean => {
  return activeFilters.value.some(
    (filter: FilterConfig) => filter.key === columnKey && filter.value
  );
};
</script>

<template>
  <div
    class="ui-table"
    :class="{
      'ui-table--bordered': bordered,
      'ui-table--striped': striped,
      'ui-table--hover': hover,
      'ui-table--compact': compact,
      'ui-table--responsive': responsive,
      'ui-table--sticky-header': stickyHeader,
      'ui-table--sticky-footer': stickyFooter,
      'ui-table--selectable': selectable,
    }"
  >
    <!-- Loading overlay -->
    <div v-if="loading" class="ui-table__loading-overlay">
      <div class="ui-table__loading-content">
        <div class="ui-table__loading-spinner" />
        <span>{{ loadingMessage }}</span>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="hasError" class="ui-table__error">
      {{ errorMessage }}
    </div>

    <!-- Empty state -->
    <div v-else-if="!tableData.length" class="ui-table__empty">
      {{ emptyMessage }}
    </div>

    <!-- Table content -->
    <div v-else class="ui-table__wrapper">
      <table>
        <!-- Header -->
        <thead v-if="showHeader">
          <tr>
            <!-- Selection checkbox -->
            <th v-if="selectable" class="ui-table__checkbox-cell">
              <div class="inline-flex items-center justify-center">
                <UiCheckbox
                  :model-value="isAllSelected"
                  :indeterminate="
                    tableData.length > 0 &&
                    !isAllSelected &&
                    selectedRowsCount > 0
                  "
                  size="sm"
                  color="primary"
                  @update:model-value="tableRef.toggleAllRows"
                />
              </div>
            </th>

            <!-- Column headers -->
            <th
              v-for="column in visibleColumns"
              :key="column.key"
              :class="[
                getColumnClasses(column),
                column.sortable && sortable && 'cursor-pointer select-none',
              ]"
              :style="
                columnWidths[column.key]
                  ? { width: `${columnWidths[column.key]}px` }
                  : {}
              "
            >
              <div class="ui-table__column-header">
                <div class="flex items-center justify-between w-full">
                  <div class="flex items-center">
                    <slot :name="`header-${column.key}`" :column="column">
                      {{ column.label }}
                    </slot>

                    <!-- Sort indicator -->
                    <span
                      v-if="column.sortable && sortable"
                      class="ui-table__sort-indicator cursor-pointer ml-1"
                      :class="{
                        'ui-table__sort-indicator--asc':
                          sortingConfig.key === column.key &&
                          sortingConfig.direction === 'asc',
                        'ui-table__sort-indicator--desc':
                          sortingConfig.key === column.key &&
                          sortingConfig.direction === 'desc',
                      }"
                      @click.stop="tableRef.toggleSort(column)"
                    >
                      <svg
                        v-if="sortingConfig.key === column.key"
                        class="h-4 w-4"
                        :class="{
                          'transform rotate-180':
                            sortingConfig.direction === 'desc',
                        }"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <svg
                        v-else
                        class="h-4 w-4 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </span>
                  </div>

                  <!-- Filter icon -->
                  <span
                    v-if="filterable && column.filterable !== false"
                    class="ui-table__filter-indicator cursor-pointer"
                    :class="{
                      'ui-table__filter-indicator--active': hasActiveFilter(
                        column.key
                      ),
                    }"
                    @click.stop="openFilterPopup(column, $event)"
                  >
                    <svg
                      class="h-4 w-4"
                      :class="{
                        'text-primary-600': hasActiveFilter(column.key),
                        'text-gray-400': !hasActiveFilter(column.key),
                      }"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </span>
                </div>

                <!-- Resize handle -->
                <div
                  v-if="column.resizable && resizable"
                  class="ui-table__resize-handle"
                  @mousedown.stop="startResize(column.key, $event)"
                />
              </div>
            </th>
          </tr>
        </thead>

        <!-- Body -->
        <tbody>
          <tr
            v-for="row in tableData"
            :key="tableRef.getRowKey(row)"
            :class="getRowClasses(row)"
            @click="
              selectable
                ? tableRef.toggleRowSelection(row)
                : handleRowClick(row, $event)
            "
            @dblclick="handleRowDoubleClick(row, $event)"
            @contextmenu="handleRowContextMenu(row, $event)"
          >
            <!-- Selection checkbox -->
            <td v-if="selectable" class="ui-table__checkbox-cell">
              <div class="inline-flex items-center justify-center">
                <UiCheckbox
                  :model-value="tableRef.isSelected(row)"
                  size="sm"
                  color="primary"
                  @update:model-value="tableRef.toggleRowSelection(row)"
                  @click.stop
                />
              </div>
            </td>

            <!-- Data cells -->
            <td
              v-for="column in visibleColumns"
              :key="column.key"
              :class="{
                [`ui-table__cell--align-${column.align || 'left'}`]: true,
              }"
            >
              <slot
                :name="`cell-${column.key}`"
                :row="row"
                :column="column"
                :value="row[column.key]"
              >
                {{
                  column.formatter
                    ? column.formatter(row[column.key], row)
                    : row[column.key]
                }}
              </slot>
            </td>
          </tr>
        </tbody>

        <!-- Footer -->
        <tfoot v-if="showFooter">
          <tr>
            <td :colspan="visibleColumns.length + (selectable ? 1 : 0)">
              <div class="ui-table__footer">
                <!-- Pagination -->
                <div
                  v-if="pagination && showPagination"
                  class="ui-table__pagination"
                >
                  <span class="ui-table__pagination-info">
                    Showing
                    {{ (paginationInfo.page - 1) * paginationInfo.perPage + 1 }}
                    to
                    {{
                      Math.min(
                        paginationInfo.page * paginationInfo.perPage,
                        paginationInfo.total
                      )
                    }}
                    of {{ paginationInfo.total }} entries
                  </span>

                  <div class="ui-table__pagination-controls">
                    <button
                      :disabled="paginationInfo.page === 1"
                      @click="tableRef.setPage(paginationInfo.page - 1)"
                    >
                      Previous
                    </button>
                    <button
                      :disabled="
                        paginationInfo.page >=
                        Math.ceil(paginationInfo.total / paginationInfo.perPage)
                      "
                      @click="tableRef.setPage(paginationInfo.page + 1)"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Filter popup -->
    <Teleport to="body">
      <div
        v-if="activeFilterColumn"
        class="ui-table__filter-popup"
        :style="{
          top: `${filterPopupPosition.top}px`,
          left: `${filterPopupPosition.left}px`,
        }"
      >
        <div class="ui-table__filter-popup-header">
          <span class="ui-table__filter-popup-title">
            Filter {{ activeFilterColumn }}
          </span>
          <button
            class="ui-table__filter-popup-close"
            @click="closeFilterPopup"
          >
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
        <div class="ui-table__filter-popup-content">
          <input
            v-model="filterValues[activeFilterColumn]"
            type="text"
            class="ui-table__filter-input"
            :placeholder="`Filter by ${activeFilterColumn}...`"
          />
        </div>
        <div class="ui-table__filter-popup-footer">
          <button
            class="ui-table__filter-popup-button ui-table__filter-popup-button--clear"
            @click="
              clearFilter(
                visibleColumns.find((col) => col.key === activeFilterColumn)!
              )
            "
          >
            Clear
          </button>
          <button
            class="ui-table__filter-popup-button ui-table__filter-popup-button--apply"
            @click="
              applyFilter(
                visibleColumns.find((col) => col.key === activeFilterColumn)!
              )
            "
          >
            Apply
          </button>
        </div>
      </div>
    </Teleport>

    <!-- Filter controls -->
    <div
      v-if="filterable && activeFilters.length > 0"
      class="ui-table__filter-controls"
    >
      <button class="ui-table__filter-clear-all" @click="clearAllFilters">
        Clear All Filters
      </button>
    </div>
  </div>
</template>

<style>
/* Base table styles */
.ui-table {
  position: relative;
  width: 100%;
}

.ui-table__wrapper {
  overflow-x: auto;
}

.ui-table table {
  width: 100%;
  border-collapse: collapse;
}

.ui-table th,
.ui-table td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.ui-table th {
  font-weight: 600;
  background-color: #f9fafb;
  color: #374151;
}

/* Bordered variant */
.ui-table--bordered th,
.ui-table--bordered td {
  border: 1px solid #e5e7eb;
}

/* Striped rows with enhanced styling */
.ui-table--striped tbody tr:nth-child(odd) {
  background-color: #ffffff;
}

.ui-table--striped tbody tr:nth-child(even) {
  background-color: rgb(249, 250, 251);
}

/* Hover effect with animation */
.ui-table--hover tbody tr {
  transition: all 0.2s ease-in-out;
}

.ui-table--hover tbody tr:hover {
  background-color: rgb(243, 244, 246);
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Compact variant */
.ui-table--compact th,
.ui-table--compact td {
  padding: 0.5rem;
}

/* Loading state */
.ui-table__loading-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.9);
}

.ui-table__loading-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.ui-table__loading-spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid #e5e7eb;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Empty and error states */
.ui-table__error,
.ui-table__empty {
  padding: 1rem;
  text-align: center;
  color: #6b7280;
}

/* Column header */
.ui-table__column-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Sort indicators */
.ui-table__sort-indicator {
  display: inline-flex;
  align-items: center;
  margin-left: 0.5rem;
  color: rgb(107, 114, 128);
  transition: color 0.15s ease-in-out;
}

.ui-table__sort-indicator--asc,
.ui-table__sort-indicator--desc {
  color: rgb(79, 70, 229);
}

/* Filter indicator */
.ui-table__filter-indicator {
  display: inline-flex;
  align-items: center;
  margin-left: 0.5rem;
  cursor: pointer;
  transition: color 0.15s ease-in-out;
}

.ui-table__filter-indicator--active {
  color: rgb(79, 70, 229);
}

/* Filter popup */
.ui-table__filter-popup {
  position: absolute;
  z-index: 50;
  width: 250px;
  background-color: white;
  border-radius: 0.375rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.ui-table__filter-popup-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.ui-table__filter-popup-title {
  font-weight: 500;
  color: #374151;
}

.ui-table__filter-popup-close {
  color: #6b7280;
  cursor: pointer;
}

.ui-table__filter-popup-content {
  padding: 1rem;
}

.ui-table__filter-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.ui-table__filter-popup-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background-color: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.ui-table__filter-popup-button {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.25rem;
  cursor: pointer;
}

.ui-table__filter-popup-button--clear {
  color: #6b7280;
  background-color: white;
  border: 1px solid #d1d5db;
}

.ui-table__filter-popup-button--apply {
  color: white;
  background-color: #4f46e5;
  border: 1px solid #4f46e5;
}

/* Resize handle */
.ui-table__resize-handle {
  position: absolute;
  top: 0;
  right: -3px;
  width: 6px;
  height: 100%;
  cursor: col-resize;
  background-color: rgba(79, 70, 229, 0.1);
  transition: background-color 0.2s;
  z-index: 1;
}

.ui-table__resize-handle:hover,
.ui-table__resize-handle:active {
  background-color: rgb(79, 70, 229);
}

/* Cell alignments */
.ui-table__cell--sortable {
  cursor: pointer;
}

.ui-table__cell--sortable:hover {
  background-color: #f3f4f6;
}

.ui-table__cell--resizable {
  position: relative;
}

.ui-table__cell--align-left {
  text-align: left;
}

.ui-table__cell--align-center {
  text-align: center;
}

.ui-table__cell--align-right {
  text-align: right;
}

/* Checkbox cell */
.ui-table__checkbox-cell {
  width: 3rem;
  padding: 0.75rem !important;
  vertical-align: middle;
}

/* Row selection styles */
.ui-table--selectable tbody tr {
  cursor: pointer;
}

.ui-table__row--selected {
  background-color: #eef2ff !important;
}

.ui-table__row--selected:hover {
  background-color: #e0e7ff !important;
}

/* Selected row count */
.ui-table__selected-count {
  font-size: 0.875rem;
  color: #6b7280;
  margin-left: 1rem;
}

/* Footer and pagination */
.ui-table__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background-color: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.ui-table__pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.ui-table__pagination-info {
  font-size: 0.875rem;
  color: #6b7280;
}

.ui-table__pagination-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.ui-table__pagination-controls button {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  transition: all 0.2s ease-in-out;
}

.ui-table__pagination-controls button:hover:not(:disabled) {
  background-color: #f9fafb;
  border-color: #d1d5db;
}

.ui-table__pagination-controls button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive styles */
@media (max-width: 640px) {
  .ui-table th,
  .ui-table td {
    padding: 0.75rem;
  }

  .ui-table__pagination {
    flex-direction: column;
    gap: 1rem;
  }

  .ui-table__pagination-controls {
    width: 100%;
    justify-content: space-between;
  }
}

/* Column header hover effect */
.ui-table th.cursor-pointer:hover {
  background-color: rgb(243, 244, 246);
}

/* Add styles for resizing state */
.ui-table--resizing {
  cursor: col-resize;
  user-select: none;
}

.ui-table--resizing .ui-table__resize-handle {
  background-color: rgb(79, 70, 229);
}

/* Ensure table cells respect column widths */
.ui-table th,
.ui-table td {
  position: relative;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 50px;
}

/* Add transition for smooth width changes */
.ui-table th {
  transition: width 0.1s ease-out;
}

/* Filter controls */
.ui-table__filter-controls {
  display: flex;
  justify-content: flex-end;
  padding: 0.5rem 1rem;
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.ui-table__filter-clear-all {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  background-color: white;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.ui-table__filter-clear-all:hover {
  background-color: #f3f4f6;
  border-color: #9ca3af;
}
</style>
