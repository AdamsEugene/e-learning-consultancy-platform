<script setup lang="ts">
import { useTable } from "~/composables/useTable";
import type {
  TableColumn,
  TableRow,
  TableProps,
  SortConfig,
  FilterConfig,
  PaginationConfig,
} from "~/types/table";
import { ref, computed, watch } from "vue";

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

const table = useTable({
  columns: props.columns,
  rowKey: props.rowKey,
  initialData: localData.value,
  fetchData: async (params) => {
    // If no options provided, use local data
    if (!props.options?.fetchData) {
      return {
        data: localData.value,
        total: localData.value.length,
      };
    }
    return props.options.fetchData(params);
  },
  defaultSort: props.options?.defaultSort,
  defaultFilters: props.options?.defaultFilters,
  defaultPagination: {
    page: props.page,
    perPage: props.perPage,
  },
});

// Computed properties for reactive data
const tableData = computed(() => {
  const currentData = props.options?.fetchData
    ? table.data.value
    : localData.value;
  if (!sortingConfig.value.key || !sortingConfig.value.direction) {
    return currentData;
  }

  const key = sortingConfig.value.key;
  const direction = sortingConfig.value.direction;

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
});

const visibleColumns = computed(() => table.visibleColumns.value);
const isAllSelected = computed(() => table.allSelected.value);
const hasError = computed(() => table.error.value !== null);
const paginationInfo = computed(() => table.pagination.value);
const sortingConfig = computed(() => table.sortConfig.value);
const selectedRowsCount = computed(() => table.selectedRows.value.length);

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
  () => table.selectedRows.value,
  (newValue) => {
    emit("selection-change", newValue);
  }
);

// Emit events when internal state changes
watch(sortingConfig, (newValue) => {
  emit("sort-change", newValue);
});

watch(table.filters, (newValue) => {
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

// Helper functions
const getColumnClasses = (column: TableColumn) => ({
  "ui-table__cell--sortable": column.sortable && props.sortable,
  "ui-table__cell--resizable": column.resizable && props.resizable,
  [`ui-table__cell--align-${column.align || "left"}`]: true,
});

const getRowClasses = (row: TableRow) => ({
  "ui-table__row": true,
  "ui-table__row--selected": table.isSelected(row),
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
                  @update:model-value="table.toggleAllRows"
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
              @click="column.sortable && sortable && table.toggleSort(column)"
            >
              <div class="ui-table__column-header">
                <slot :name="`header-${column.key}`" :column="column">
                  {{ column.label }}
                </slot>

                <!-- Sort indicator -->
                <span
                  v-if="column.sortable && sortable"
                  class="ui-table__sort-indicator"
                  :class="{
                    'ui-table__sort-indicator--asc':
                      sortingConfig.key === column.key &&
                      sortingConfig.direction === 'asc',
                    'ui-table__sort-indicator--desc':
                      sortingConfig.key === column.key &&
                      sortingConfig.direction === 'desc',
                  }"
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
            :key="table.getRowKey(row)"
            :class="getRowClasses(row)"
            @click="
              selectable
                ? table.toggleRowSelection(row)
                : handleRowClick(row, $event)
            "
            @dblclick="handleRowDoubleClick(row, $event)"
            @contextmenu="handleRowContextMenu(row, $event)"
          >
            <!-- Selection checkbox -->
            <td v-if="selectable" class="ui-table__checkbox-cell">
              <div class="inline-flex items-center justify-center">
                <UiCheckbox
                  :model-value="table.isSelected(row)"
                  size="sm"
                  color="primary"
                  @update:model-value="table.toggleRowSelection(row)"
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
                      @click="table.setPage(paginationInfo.page - 1)"
                    >
                      Previous
                    </button>
                    <button
                      :disabled="
                        paginationInfo.page >=
                        Math.ceil(paginationInfo.total / paginationInfo.perPage)
                      "
                      @click="table.setPage(paginationInfo.page + 1)"
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
</style>
