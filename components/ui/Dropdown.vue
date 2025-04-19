<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<script setup lang="ts">
export interface FormDropdownItem {
  id: string | number;
  name: string;
  email?: string;
  disabled?: boolean;
  clickable?: boolean;
  [key: string]: any;
}

type SelectHandler = (item: FormDropdownItem | FormDropdownItem[]) => void;

interface Props {
  label?: string;
  placeholder?: string;
  withSearch?: boolean;
  items?: FormDropdownItem[];
  selectedItem?: FormDropdownItem | FormDropdownItem[] | null;
  multiple?: boolean;
  selectItem?: SelectHandler;
  placement?: "top" | "bottom";
  noListIcon?: boolean;
  disabled?: boolean;
  marginTop?: string;
  userEmail?: string;
  userName?: string;
  showProfile?: boolean;
  showAddButton?: boolean;
  className?: string;
  forProfile?: boolean;
  isCollapsed?: boolean;
  error?: string;
  errorClass?: string;
  loading?: boolean;
  matchMode?: "contains" | "startsWith";
}

const props = withDefaults(defineProps<Props>(), {
  label: "",
  placeholder: "Select an option",
  withSearch: false,
  selectedItem: null,
  items: undefined,
  multiple: false,
  selectItem: () => {},
  placement: "bottom",
  noListIcon: false,
  disabled: false,
  marginTop: "3",
  userEmail: "",
  userName: "",
  showProfile: false,
  showAddButton: false,
  className: "",
  forProfile: false,
  isCollapsed: false,
  error: undefined,
  errorClass: "",
  loading: false,
  matchMode: "startsWith",
});

const emit = defineEmits<{
  (e: "on-search", searchString: string): void;
  (e: "update:isOpen", state: boolean): void;
  (e: "add-item"): void;
}>();

// Internal state
const isOpen = ref(false);
const searchQuery = ref("");

// Helper to display selected items in button
const displayValue = computed(() => {
  if (!props.selectedItem) return props.placeholder;

  if (!props.multiple) {
    return (props.selectedItem as FormDropdownItem)?.name || props.placeholder;
  }

  // For multiple selection
  const items = props.selectedItem as FormDropdownItem[];
  if (items.length === 0) return props.placeholder;
  if (items.length === 1) return items[0].name;
  return `${items[0].name} +${items.length - 1} more`;
});

// Computed property to check if we have selected items for multiple select
const hasSelectedItems = computed(() => {
  if (!props.selectedItem) return false;
  if (Array.isArray(props.selectedItem)) {
    return props.selectedItem.length > 0;
  }
  return true;
});

// Get selected items array for rendering tags
const selectedItems = computed(() => {
  if (!props.selectedItem || !Array.isArray(props.selectedItem)) return [];
  return props.selectedItem;
});

const filteredItems = computed(() => {
  if (!props.items) return [];
  if (!searchQuery.value) return props.items;

  const query = searchQuery.value.toLowerCase().trim();

  // Create a copy of the items array to avoid mutating the original
  const results = [...props.items];

  // Sort the results to prioritize items that start with the query
  results.sort((a, b) => {
    const aNameStartsWith = a?.name?.toLowerCase().startsWith(query) || false;
    const bNameStartsWith = b?.name?.toLowerCase().startsWith(query) || false;
    const aNameIncludes = a?.name?.toLowerCase().includes(query) || false;
    const bNameIncludes = b?.name?.toLowerCase().includes(query) || false;

    // First priority: items that start with the query
    if (aNameStartsWith && !bNameStartsWith) return -1;
    if (!aNameStartsWith && bNameStartsWith) return 1;

    // Second priority: items that contain the query
    if (aNameIncludes && !bNameIncludes) return -1;
    if (!aNameIncludes && bNameIncludes) return 1;

    // If both have the same priority, maintain original order
    return 0;
  });

  // Filter out items that don't match at all
  return results.filter(
    (item) =>
      item?.name?.toLowerCase().includes(query) ||
      item?.email?.toLowerCase().includes(query)
  );
});

const isSelected = (item: FormDropdownItem) => {
  if (!props.selectedItem) return false;

  if (Array.isArray(props.selectedItem)) {
    return props.selectedItem.some((selected) => selected.id === item.id);
  }

  return props.selectedItem.id === item.id;
};

// Event handlers
const handleSelectItem = (item: FormDropdownItem) => {
  if (item.disabled || item.clickable === false) return;

  if (props.multiple) {
    let newSelection: FormDropdownItem[];

    if (Array.isArray(props.selectedItem)) {
      if (isSelected(item)) {
        newSelection = props.selectedItem.filter((i) => i.id !== item.id);
      } else {
        newSelection = [...props.selectedItem, item];
      }
    } else {
      newSelection = props.selectedItem
        ? [props.selectedItem as FormDropdownItem, item]
        : [item];
    }

    props.selectItem?.(newSelection);
  } else {
    props.selectItem?.(item);
    closeDropdown();
  }
};

const handleAddItem = () => {
  emit("add-item");
  closeDropdown();
};

// Update the handleSearch function to work with watch instead
watch(searchQuery, (newValue) => {
  emit("on-search", newValue);
});

// Dropdown state management
const openDropdown = () => {
  if (!props.disabled) {
    isOpen.value = true;
    emit("update:isOpen", true);
  }
};

const closeDropdown = () => {
  isOpen.value = false;
  emit("update:isOpen", false);
  // Reset search when closing
  setTimeout(() => {
    searchQuery.value = "";
  }, 300);
};

const toggleDropdown = () => {
  if (!props.disabled) {
    isOpen.value = !isOpen.value;
    emit("update:isOpen", isOpen.value);
  }
};

// Clear selection method for external use
const clearSelection = () => {
  if (props.multiple) {
    props.selectItem?.([]);
  } else {
    props.selectItem?.(null as any);
  }
};

defineExpose({
  openDropdown,
  closeDropdown,
  clearSelection,
});
</script>

<template>
  <div
    v-click-outside="closeDropdown"
    class="relative flex flex-col items-start gap-2 w-full"
  >
    <!-- Label -->
    <label
      v-if="label && !isCollapsed"
      class="text-gray-700 text-sm font-medium leading-5 cursor-pointer"
      @click="openDropdown"
    >
      {{ label }}
    </label>

    <!-- Select Button -->
    <UiButton
      variant="primary"
      state="default"
      :class-name="{
        '!text-start': true,
        '!w-full': true,
        '!border-red-500': error,
        '!cursor-not-allowed': disabled,
        '!bg-gray-50': disabled,
      }"
      inner-class-name="!w-full !justify-between"
      :disabled="disabled"
      @click="toggleDropdown"
    >
      <div class="flex-1 w-full">
        <div class="flex items-center gap-2 min-w-fit w-full">
          <!-- Show multiple selections as a comma-separated list -->
          <template v-if="multiple && hasSelectedItems">
            <span
              class="truncate text-[#414651] font-inter text-[14px] font-semibold leading-5 w-full"
            >
              {{ selectedItems.map((item) => item.name).join(", ") }}
            </span>
          </template>
          <!-- Show single selection or placeholder -->
          <span
            v-else
            class="truncate block font-inter text-[14px] leading-5 text-[#414651]"
            :class="[
              hasSelectedItems && !multiple
                ? 'font-semibold' // Selected state
                : 'font-normal text-gray-500', // Unselected/default state
            ]"
          >
            {{ displayValue }}
          </span>
        </div>
      </div>

      <!-- Left Icon Slot -->
      <template #icon>
        <div v-if="$slots['pre-icon']" class="w-5 h-5 flex-shrink-0">
          <slot name="pre-icon" />
        </div>
      </template>

      <!-- Right Icon (Chevron) -->
      <template v-if="!isCollapsed" #icon-right>
        <div
          class="w-5 h-5 text-gray-500 flex-shrink-0 transition-transform duration-300"
          :class="{ 'rotate-180': isOpen }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </template>
    </UiButton>

    <!-- Error Message -->
    <p
      v-if="error"
      class="text-red-500 text-sm mt-1 ml-2 truncate"
      :class="[errorClass]"
    >
      {{ error }}
    </p>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-1 scale-95"
    >
      <div
        v-if="isOpen"
        class="w-full min-w-[240px] absolute z-[1000] rounded-[12px] border border-[#D5D7DA] bg-white shadow-[0px_20px_24px_-4px_rgba(10,13,18,0.08),0px_8px_16px_-4px_rgba(10,13,18,0.03),0px_3px_3px_-1.5px_rgba(10,13,18,0.04)]"
        :class="[
          placement === 'top' ? 'bottom-full mb-2' : `mt-${marginTop} top-full`,
          className,
        ]"
        style="
          max-height: 350px;
          overflow: hidden;
          display: flex;
          flex-direction: column;
        "
      >
        <!-- Search Bar -->
        <div v-if="withSearch" class="p-3 border-b border-gray-100">
          <div class="relative">
            <UiInput
              v-model="searchQuery"
              type="text"
              placeholder="Search..."
              class="w-full px-3 py-2 pr-10 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
            />
            <div
              class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-gray-400"
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
          </div>
        </div>

        <!-- User Info (for profile dropdown) -->
        <div
          v-if="userName || userEmail"
          class="px-4 py-3 border-b border-gray-100"
        >
          <div
            v-if="userName"
            class="text-black font-inter text-base font-semibold leading-5"
          >
            {{ userName }}
          </div>
          <div
            v-if="userEmail"
            class="text-black font-inter text-[14px] font-normal leading-5 mt-1"
          >
            {{ userEmail }}
          </div>
          <div
            v-if="showProfile"
            class="mt-2 flex justify-between items-center"
          >
            <NuxtLink to="/settings">
              <button
                class="flex items-center gap-2 px-0 py-[2px] rounded-[12px] text-[#079455] font-inter text-[14px] font-semibold leading-5 hover:opacity-80 transition-opacity duration-200"
              >
                Profile →
              </button>
            </NuxtLink>
          </div>
        </div>

        <!-- Item List -->
        <div class="overflow-y-auto p-2" style="max-height: 250px">
          <div v-if="loading" class="flex justify-center items-center py-4">
            <div
              class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-500"
            />
          </div>

          <div
            v-else-if="filteredItems && filteredItems.length > 0"
            class="space-y-1"
          >
            <button
              v-for="item in filteredItems"
              :key="item.id"
              :disabled="item.disabled || item.clickable === false"
              class="w-full px-3 py-2 rounded-lg text-left transition-colors duration-200 flex items-center gap-3"
              :class="[
                isSelected(item)
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'hover:bg-gray-50 text-gray-700',
                item.disabled || item.clickable === false
                  ? 'opacity-50 cursor-not-allowed'
                  : 'cursor-pointer',
              ]"
              @click="handleSelectItem(item)"
            >
              <!-- Checkbox for multiple selection -->
              <div v-if="multiple" class="flex-shrink-0">
                <div
                  class="w-4 h-4 border rounded flex items-center justify-center transition-colors"
                  :class="[
                    isSelected(item)
                      ? 'bg-indigo-500 border-indigo-500'
                      : 'border-gray-300',
                  ]"
                >
                  <svg
                    v-if="isSelected(item)"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 text-white"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>

              <!-- Item icon (if enabled) -->
              <div
                v-if="!noListIcon"
                class="w-5 h-5 flex-shrink-0 text-gray-500"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 20 20"
                  fill="none"
                >
                  <path
                    d="M2.5 7.5H17.5M7.5 7.5L7.5 17.5M6.5 2.5H13.5C14.9001 2.5 15.6002 2.5 16.135 2.77248C16.6054 3.01217 16.9878 3.39462 17.2275 3.86502C17.5 4.3998 17.5 5.09987 17.5 6.5V13.5C17.5 14.9001 17.5 15.6002 17.2275 16.135C16.9878 16.6054 16.6054 16.9878 16.135 17.2275C15.6002 17.5 14.9001 17.5 13.5 17.5H6.5C5.09987 17.5 4.3998 17.5 3.86502 17.2275C3.39462 16.9878 3.01217 16.6054 2.77248 16.135C2.5 15.6002 2.5 14.9001 2.5 13.5V6.5C2.5 5.09987 2.5 4.3998 2.77248 3.86502C3.01217 3.39462 3.39462 3.01217 3.86502 2.77248C4.3998 2.5 5.09987 2.5 6.5 2.5Z"
                    stroke="#079455"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </div>

              <!-- Item name -->
              <span
                class="truncate block font-inter text-[14px] leading-5"
                :class="isSelected(item) ? 'font-semibold' : 'font-normal'"
              >
                {{ item.name }}
              </span>

              <!-- Item email (if available) -->
              <span
                v-if="item.email"
                class="ml-auto text-xs text-gray-500 truncate"
              >
                {{ item.email }}
              </span>
            </button>
          </div>

          <!-- Empty state -->
          <div
            v-else-if="filteredItems && filteredItems.length === 0"
            class="py-8 text-center"
          >
            <div class="text-gray-400 mb-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-10 w-10 mx-auto"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"
                />
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">No results found</p>
            <p v-if="searchQuery" class="text-xs text-gray-500 mt-1">
              No matches for "{{ searchQuery }}"
            </p>
          </div>

          <!-- No items state -->
          <div v-else class="py-8 text-center">
            <div class="text-gray-400 mb-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-10 w-10 mx-auto"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                />
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">List is empty</p>
            <p class="text-xs text-gray-500 mt-1">Add items to the list</p>
          </div>
        </div>

        <!-- Footer actions -->
        <div
          v-if="showAddButton || forProfile"
          class="border-t border-gray-100 p-2"
        >
          <!-- Add button -->
          <UiButton
            v-if="showAddButton"
            variant="primary"
            state="default"
            class-name="!w-full !items-center !justify-center"
            @click="handleAddItem"
          >
            Add a website
            <template #icon-right>
              <div class="w-5 h-5 text-white">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </template>
          </UiButton>

          <!-- Logout button (for profile) -->
          <UiButton
            v-if="forProfile"
            variant="primary"
            state="default"
            class-name="!w-full !items-center !justify-center mt-2"
          >
            Logout
            <template #icon-right>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 16 16"
                fill="none"
              >
                <path
                  d="M11.3333 12.1667L15.5 8M15.5 8L11.3333 3.83333M15.5 8H5.5M5.5 0.5H4.5C3.09987 0.5 2.3998 0.5 1.86502 0.772484C1.39462 1.01217 1.01217 1.39462 0.772484 1.86502C0.5 2.3998 0.5 3.09987 0.5 4.5V11.5C0.5 12.9001 0.5 13.6002 0.772484 14.135C1.01217 14.6054 1.39462 14.9878 1.86502 15.2275C2.3998 15.5 3.09987 15.5 4.5 15.5H5.5"
                  stroke="#414651"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </template>
          </UiButton>
        </div>
      </div>
    </Transition>
  </div>
</template>

<!-- Style customizations for more visually refined dropdowns -->
<style>
/* Global CSS to enhance the FormDropdown component */

/* 1. Fancy scroll animation for dropdown items */
.dropdown-item-enter-active,
.dropdown-item-leave-active {
  transition: all 0.3s ease;
}

.dropdown-item-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.dropdown-item-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* 2. Staggered animation for dropdown items */
.dropdown-list-item {
  opacity: 0;
  transform: translateY(8px);
  transition: all 0.2s ease;
}

/* Apply different delays based on item index */
.dropdown-list-item:nth-child(1) {
  animation: fadeInUp 0.3s ease forwards;
}
.dropdown-list-item:nth-child(2) {
  animation: fadeInUp 0.3s 0.05s ease forwards;
}
.dropdown-list-item:nth-child(3) {
  animation: fadeInUp 0.3s 0.1s ease forwards;
}
.dropdown-list-item:nth-child(4) {
  animation: fadeInUp 0.3s 0.15s ease forwards;
}
.dropdown-list-item:nth-child(5) {
  animation: fadeInUp 0.3s 0.2s ease forwards;
}
.dropdown-list-item:nth-child(n + 6) {
  animation: fadeInUp 0.3s 0.25s ease forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* 3. Focus animation for search input */
.dropdown-search-input:focus {
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  animation: pulse-border 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse-border {
  0%,
  100% {
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
  }
  50% {
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
  }
}

/* 4. Hover effects for dropdown items */
.dropdown-item {
  position: relative;
  overflow: hidden;
}

.dropdown-item::after {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 0;
  background-color: rgba(99, 102, 241, 0.05);
  transition: width 0.3s ease;
  z-index: -1;
}

.dropdown-item:hover::after {
  width: 100%;
}

.dropdown-item.selected {
  background: linear-gradient(
    to right,
    rgba(99, 102, 241, 0.1),
    rgba(99, 102, 241, 0.05)
  );
}

/* 5. Animation for dropdown toggle button */
.dropdown-toggle-btn {
  position: relative;
  z-index: 1;
  overflow: hidden;
}

.dropdown-toggle-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background-color: rgba(99, 102, 241, 0.1);
  transition: transform 0.3s ease;
  z-index: -1;
  transform: skewX(-15deg);
}

.dropdown-toggle-btn:hover::before {
  transform: translateX(100%) skewX(-15deg);
}

/* 6. Custom styles for multiple selection pills */
.dropdown-pill {
  position: relative;
  transition: all 0.2s ease;
  cursor: pointer;
}

.dropdown-pill:hover {
  background-color: rgba(99, 102, 241, 0.2);
}

.dropdown-pill::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background-color: rgba(99, 102, 241, 0.4);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.dropdown-pill:hover::before {
  transform: scaleX(0.8);
}

/* 7. Badge animations */
.dropdown-badge {
  animation: pop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes pop {
  0% {
    transform: scale(0);
  }
  70% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}

/* 8. Clear button animation */
.dropdown-clear-btn {
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.2s ease;
}

.dropdown-wrapper:hover .dropdown-clear-btn {
  opacity: 1;
  transform: scale(1);
}

/* 9. Placeholder text fade effect */
.dropdown-placeholder {
  color: rgba(107, 114, 128, 0.8);
  transition: color 0.3s ease;
}

.dropdown-toggle-btn:hover .dropdown-placeholder {
  color: rgba(107, 114, 128, 1);
}

/* 10. Enhanced focus states */
.dropdown-focus-ring {
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  border-radius: inherit;
  border: 2px solid transparent;
  pointer-events: none;
  transition: all 0.2s ease;
}

.dropdown-toggle-btn:focus-visible .dropdown-focus-ring {
  border-color: rgba(99, 102, 241, 0.5);
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}
</style>
