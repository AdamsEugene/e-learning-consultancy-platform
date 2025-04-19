/**
 * Table Component Types
 *
 * This file contains all the type definitions for the table component and related functionality.
 */

/**
 * Represents a column in a table
 */
export interface TableColumn {
  /** Unique identifier for the column */
  key: string;
  /** Display label for the column */
  label: string;
  /** Whether the column can be sorted */
  sortable?: boolean;
  /** Whether the column can be filtered */
  filterable?: boolean;
  /** Whether the column can be resized */
  resizable?: boolean;
  /** Fixed width of the column in pixels */
  width?: number;
  /** Minimum width of the column in pixels */
  minWidth?: number;
  /** Maximum width of the column in pixels */
  maxWidth?: number;
  /** Whether the column is hidden */
  hidden?: boolean;
  /** Function to format the cell value */
  formatter?: (value: unknown, row: TableRow) => string;
  /** Text alignment of the column */
  align?: "left" | "center" | "right";
  /** Whether the column is fixed to the left or right */
  fixed?: "left" | "right";
  /** Type of the column for filtering */
  type?: "text" | "number" | "select" | "date";
  /** Options for select type columns */
  options?: string[];
}

/**
 * Represents a row in a table
 */
export interface TableRow {
  [key: string]: unknown;
}

/**
 * Configuration for sorting
 */
export interface SortConfig {
  /** Column key to sort by */
  key: string;
  /** Sort direction */
  direction: "asc" | "desc" | null;
}

/**
 * Configuration for filtering
 */
export interface FilterConfig {
  /** Column key to filter by */
  key: string;
  /** Filter value */
  value: string;
}

/**
 * Configuration for pagination
 */
export interface PaginationConfig {
  /** Current page number */
  page: number;
  /** Number of items per page */
  perPage: number;
  /** Total number of items */
  total: number;
}

/**
 * Options for the table composable
 */
export interface TableOptions<T extends TableRow = TableRow> {
  /** Table columns */
  columns: TableColumn[];
  /** Initial data */
  initialData?: T[];
  /** Function to fetch data */
  fetchData?: (params: {
    page: number;
    perPage: number;
    sortBy?: string;
    sortDirection?: "asc" | "desc";
    filters?: Record<string, string>;
  }) => Promise<{
    data: T[];
    total: number;
  }>;
  /** Default sort configuration */
  defaultSort?: SortConfig;
  /** Default filter configuration */
  defaultFilters?: FilterConfig[];
  /** Default pagination configuration */
  defaultPagination?: Partial<PaginationConfig>;
  /** Debounce time for data fetching */
  debounceTime?: number;
}

/**
 * Props for the Table component
 */
export interface TableProps {
  /** Table data */
  modelValue: TableRow[];
  /** Table columns */
  columns: TableColumn[];
  /** CSS class for rows */
  rowClass?: string | ((row: TableRow) => string);
  /** Whether the table is loading */
  loading?: boolean;
  /** Whether rows can be selected */
  selectable?: boolean;
  /** Whether columns can be resized */
  resizable?: boolean;
  /** Whether columns can be sorted */
  sortable?: boolean;
  /** Whether columns can be filtered */
  filterable?: boolean;
  /** Whether pagination is enabled */
  pagination?: boolean;
  /** Number of items per page */
  perPage?: number;
  /** Current page number */
  page?: number;
  /** Total number of items */
  total?: number;
  /** Whether the table has borders */
  bordered?: boolean;
  /** Whether rows are striped */
  striped?: boolean;
  /** Whether rows have hover effect */
  hover?: boolean;
  /** Whether the table is compact */
  compact?: boolean;
  /** Whether the table is responsive */
  responsive?: boolean;
  /** Whether the header is sticky */
  stickyHeader?: boolean;
  /** Whether the footer is sticky */
  stickyFooter?: boolean;
  /** Maximum height of the table */
  maxHeight?: string;
  /** Whether to show the header */
  showHeader?: boolean;
  /** Whether to show the footer */
  showFooter?: boolean;
  /** Whether to show pagination */
  showPagination?: boolean;
  /** Whether to show filters */
  showFilters?: boolean;
  /** Whether to show column visibility toggle */
  showColumnVisibilityToggle?: boolean;
  /** Whether to show resize handles */
  showResizeHandles?: boolean;
  /** Key to use for row identification */
  rowKey?: string;
  /** Message to show when loading */
  loadingMessage?: string;
  /** Message to show when there's an error */
  errorMessage?: string;
  /** Message to show when there's no data */
  emptyMessage?: string;
  /** Table options for the composable */
  options?: TableOptions<TableRow>;
}

/**
 * Events emitted by the Table component
 */
export interface TableEmits {
  /** Row events */
  (
    e: "row-click" | "row-dblclick" | "row-contextmenu",
    row: TableRow,
    event: MouseEvent
  ): void;
  /** Sort change event */
  (e: "sort-change", config: SortConfig): void;
  /** Filter change event */
  (e: "filter-change", filters: FilterConfig[]): void;
  /** Pagination change event */
  (e: "pagination-change", config: PaginationConfig): void;
  /** Column resize event */
  (e: "column-resize", key: string, width: number): void;
  /** Column visibility change event */
  (e: "column-visibility-change", key: string, hidden: boolean): void;
}
