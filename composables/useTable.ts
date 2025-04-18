import type {
  TableColumn,
  SortConfig,
  FilterConfig,
  PaginationConfig,
  TableOptions,
  TableRow,
} from "~/types/table";
import { ref, computed, watch } from "vue";
import type { Ref } from "vue";

export function useTable<T extends TableRow>(
  options: TableOptions<T> & {
    rowKey?: keyof T | string;
    striped?: boolean;
    hover?: boolean;
  }
) {
  // State
  const data = ref<T[]>([]) as Ref<T[]>;
  const loading = ref(false);
  const error = ref<Error | null>(null);
  const columns = ref<TableColumn[]>(options.columns);
  const sortConfig = ref<SortConfig>(
    options.defaultSort || { key: "", direction: null }
  );
  const filters = ref<FilterConfig[]>(options.defaultFilters || []);
  const pagination = ref<PaginationConfig>({
    page: options.defaultPagination?.page || 1,
    perPage: options.defaultPagination?.perPage || 10,
    total: 0,
  });
  const columnWidths = ref<Record<string, number>>({});
  const resizingColumn = ref<string | null>(null);
  const resizingStartX = ref<number | null>(null);
  const resizingStartWidth = ref<number | null>(null);
  const selectedRows = ref<T[]>([]) as Ref<T[]>;

  // Computed
  const visibleColumns = computed(() => {
    return columns.value.filter((column) => !column.hidden);
  });

  const fixedLeftColumns = computed(() => {
    return visibleColumns.value.filter((column) => column.fixed === "left");
  });

  const fixedRightColumns = computed(() => {
    return visibleColumns.value.filter((column) => column.fixed === "right");
  });

  const scrollableColumns = computed(() => {
    return visibleColumns.value.filter((column) => !column.fixed);
  });

  const activeFilters = computed(() => {
    return filters.value.filter((filter) => filter.value.trim() !== "");
  });

  const allSelected = computed(() => {
    return (
      data.value.length > 0 && selectedRows.value.length === data.value.length
    );
  });

  const sortedData = computed(() => {
    if (!sortConfig.value.key || !sortConfig.value.direction) {
      return data.value;
    }

    const key = sortConfig.value.key;
    const direction = sortConfig.value.direction;

    return [...data.value].sort((a, b) => {
      const aValue = a[key];
      const bValue = b[key];

      if (aValue === bValue) return 0;

      // Handle different types of values
      if (typeof aValue === "string" && typeof bValue === "string") {
        return direction === "asc"
          ? aValue.localeCompare(bValue)
          : bValue.localeCompare(aValue);
      }

      if (typeof aValue === "number" && typeof bValue === "number") {
        return direction === "asc" ? aValue - bValue : bValue - aValue;
      }

      // Convert to strings for comparison if types don't match
      const aStr = String(aValue ?? "");
      const bStr = String(bValue ?? "");
      return direction === "asc"
        ? aStr.localeCompare(bStr)
        : bStr.localeCompare(aStr);
    });
  });

  // Type guard for checking if a value is a valid row key
  const isValidRowKey = (value: unknown): value is string | number => {
    return typeof value === "string" || typeof value === "number";
  };

  // Get the unique key for a row
  const getRowKey = (row: T): string | number => {
    const key = options.rowKey || "id";
    const value = row[key];

    if (isValidRowKey(value)) {
      return value;
    }

    console.warn(
      `Invalid row key: ${String(
        key
      )}. Row must have a string or number key. Using fallback.`
    );
    return JSON.stringify(row);
  };

  const isSelected = (row: T) => {
    return selectedRows.value.some(
      (selectedRow) => getRowKey(selectedRow) === getRowKey(row)
    );
  };

  // Methods
  const fetchData = async () => {
    loading.value = true;
    error.value = null;

    try {
      const params = {
        page: pagination.value.page,
        perPage: pagination.value.perPage,
        sortBy: sortConfig.value.key || undefined,
        sortDirection:
          sortConfig.value.direction === null
            ? undefined
            : sortConfig.value.direction,
        filters: activeFilters.value.reduce((acc, filter) => {
          acc[filter.key] = filter.value;
          return acc;
        }, {} as Record<string, string>),
      };

      if (options.fetchData) {
        const result = await options.fetchData(params);
        data.value = result.data;
        pagination.value.total = result.total;
      } else {
        data.value = (options.initialData || []) as T[];
        pagination.value.total = data.value.length;
      }
    } catch (err) {
      error.value =
        err instanceof Error ? err : new Error("Unknown error occurred");
      console.error("Error fetching table data:", err);
    } finally {
      loading.value = false;
    }
  };

  const toggleSort = (column: TableColumn) => {
    if (!column.sortable) return;

    if (sortConfig.value.key === column.key) {
      if (sortConfig.value.direction === "asc") {
        sortConfig.value.direction = "desc";
      } else if (sortConfig.value.direction === "desc") {
        sortConfig.value.direction = null;
        sortConfig.value.key = "";
      }
    } else {
      sortConfig.value.key = column.key;
      sortConfig.value.direction = "asc";
    }

    if (options.fetchData) {
      fetchData();
    }
  };

  const setFilter = (key: string, value: string) => {
    const existingFilter = filters.value.find((filter) => filter.key === key);
    if (existingFilter) {
      existingFilter.value = value;
    } else {
      filters.value.push({ key, value });
    }
  };

  const clearFilter = (key: string) => {
    filters.value = filters.value.filter((filter) => filter.key !== key);
  };

  const clearAllFilters = () => {
    filters.value = [];
  };

  const setPage = (page: number) => {
    pagination.value.page = page;
  };

  const setPerPage = (perPage: number) => {
    pagination.value.perPage = perPage;
    pagination.value.page = 1;
  };

  const toggleColumnVisibility = (key: string) => {
    const column = columns.value.find((col) => col.key === key);
    if (column) {
      column.hidden = !column.hidden;
    }
  };

  const startResizing = (key: string, event: MouseEvent) => {
    const column = columns.value.find((col) => col.key === key);
    if (!column?.resizable) return;

    resizingColumn.value = key;
    resizingStartX.value = event.clientX;
    resizingStartWidth.value = columnWidths.value[key] || column.width || 100;

    document.addEventListener("mousemove", handleResizing);
    document.addEventListener("mouseup", stopResizing);
  };

  const handleResizing = (event: MouseEvent) => {
    if (
      !resizingColumn.value ||
      resizingStartX.value === null ||
      resizingStartWidth.value === null
    )
      return;

    const column = columns.value.find(
      (col) => col.key === resizingColumn.value
    );
    if (!column) return;

    const diff = event.clientX - resizingStartX.value;
    let newWidth = resizingStartWidth.value + diff;

    if (column.minWidth) newWidth = Math.max(newWidth, column.minWidth);
    if (column.maxWidth) newWidth = Math.min(newWidth, column.maxWidth);

    columnWidths.value[resizingColumn.value] = newWidth;
  };

  const stopResizing = () => {
    resizingColumn.value = null;
    resizingStartX.value = null;
    resizingStartWidth.value = null;

    document.removeEventListener("mousemove", handleResizing);
    document.removeEventListener("mouseup", stopResizing);
  };

  const initializeColumnWidths = () => {
    columns.value.forEach((column) => {
      if (column.width) {
        columnWidths.value[column.key] = column.width;
      }
    });
  };

  const toggleRowSelection = (row: T) => {
    const index = selectedRows.value.findIndex(
      (selectedRow) => getRowKey(selectedRow) === getRowKey(row)
    );

    if (index === -1) {
      selectedRows.value = [...selectedRows.value, row];
    } else {
      const newSelectedRows = [...selectedRows.value];
      newSelectedRows.splice(index, 1);
      selectedRows.value = newSelectedRows;
    }
  };

  const toggleAllRows = () => {
    selectedRows.value =
      selectedRows.value.length === data.value.length ? [] : [...data.value];
  };

  // Watch for changes that should trigger a data refresh
  watch(
    [sortConfig, filters, pagination],
    () => {
      fetchData();
    },
    { deep: true }
  );

  // Initialize
  initializeColumnWidths();
  fetchData();

  return {
    // State
    data: sortedData,
    loading,
    error,
    columns,
    sortConfig,
    filters,
    pagination,
    columnWidths,
    resizingColumn,
    selectedRows,
    striped: options.striped,
    hover: options.hover,

    // Computed
    visibleColumns,
    fixedLeftColumns,
    fixedRightColumns,
    scrollableColumns,
    activeFilters,
    allSelected,
    isSelected,

    // Methods
    fetchData,
    toggleSort,
    setFilter,
    clearFilter,
    clearAllFilters,
    setPage,
    setPerPage,
    toggleColumnVisibility,
    startResizing,
    handleResizing,
    stopResizing,
    toggleRowSelection,
    toggleAllRows,
    getRowKey,
  };
}
