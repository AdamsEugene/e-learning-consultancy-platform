<script setup lang="ts">
import {
  ref,
  computed,
  onMounted,
  onBeforeUnmount,
  watch,
  nextTick,
} from "vue";

// Define props
interface DropdownProps {
  /** Button label text */
  label?: string;
  /** Button variant */
  variant?: "primary" | "secondary" | "outline" | "ghost" | "link";
  /** Button size */
  size?: "sm" | "md" | "lg";
  /** Initial open state */
  initialOpen?: boolean;
  /** Position of dropdown menu */
  position?: "left" | "right" | "center";
  /** Add width style to dropdown menu */
  width?: string;
  /** Disable the dropdown */
  disabled?: boolean;
  /** Custom button class */
  buttonClass?: string;
  /** Show only icon (no label) */
  iconOnly?: boolean;
  /** Add border to dropdown menu */
  bordered?: boolean;
  /** Show shadow on dropdown menu */
  shadow?: boolean;
  /** Offset the menu by Y pixels */
  offsetY?: number;
  /** Force close when item is clicked */
  closeOnSelect?: boolean;
  /** Control dropdown programmatically */
  open?: boolean;
  /** Icon position */
  iconPosition?: "left" | "right";
}

const props = withDefaults(defineProps<DropdownProps>(), {
  label: "Dropdown",
  variant: "primary",
  size: "md",
  initialOpen: false,
  position: "left",
  width: "auto",
  disabled: false,
  buttonClass: "",
  iconOnly: false,
  bordered: true,
  shadow: true,
  offsetY: 4,
  closeOnSelect: true,
  open: undefined,
  iconPosition: "left",
});

// Define emits
const emit = defineEmits<{
  (e: "open" | "close"): void;
  (e: "toggle" | "update:open", isOpen: boolean): void;
}>();

// State
const isOpen = ref(props.initialOpen);
const dropdownRef = ref<HTMLElement | null>(null);
const triggerRef = ref<HTMLElement | null>(null);
const menuRef = ref<HTMLElement | null>(null);
const menuItems = ref<HTMLElement[]>([]);
const activeItemIndex = ref(-1);

// Watch external open prop for two-way binding
watch(
  () => props.open,
  (newVal) => {
    if (newVal !== undefined) {
      isOpen.value = newVal;
    }
  },
  { immediate: true }
);

// Watch isOpen and emit events
watch(isOpen, (newVal) => {
  emit("toggle", newVal);
  emit("update:open", newVal);

  if (newVal) {
    emit("open");
    nextTick(() => {
      focusTrap();
      updateMenuItemsArray();
    });
  } else {
    emit("close");
    activeItemIndex.value = -1;
  }
});

// Computed styles and classes
const buttonSizeClass = computed(() => {
  switch (props.size) {
    case "sm":
      return "px-2.5 py-1.5 text-xs";
    case "lg":
      return "px-6 py-3 text-base";
    default:
      return "px-4 py-2 text-sm";
  }
});

const buttonVariantClass = computed(() => {
  switch (props.variant) {
    case "primary":
      return "bg-indigo-600 hover:bg-indigo-700 text-white font-medium focus:ring-indigo-500";
    case "secondary":
      return "bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium focus:ring-gray-500";
    case "outline":
      return "bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium focus:ring-indigo-500";
    case "ghost":
      return "bg-transparent hover:bg-gray-100 text-gray-700 font-medium focus:ring-gray-500";
    case "link":
      return "bg-transparent hover:underline text-indigo-600 font-medium focus:ring-indigo-500 p-0";
    default:
      return "bg-indigo-600 hover:bg-indigo-700 text-white font-medium focus:ring-indigo-500";
  }
});

const menuPositionClass = computed(() => {
  switch (props.position) {
    case "right":
      return "right-0";
    case "center":
      return "left-1/2 -translate-x-1/2";
    default:
      return "left-0";
  }
});

// Toggle dropdown
const toggleDropdown = () => {
  if (props.disabled) return;
  isOpen.value = !isOpen.value;
};

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    isOpen.value = false;
  }
};

// Handle keyboard navigation
const handleKeyDown = (event: KeyboardEvent) => {
  if (!isOpen.value) return;

  switch (event.key) {
    case "Escape":
      isOpen.value = false;
      triggerRef.value?.focus();
      break;
    case "ArrowDown":
      event.preventDefault();
      navigateMenu(1);
      break;
    case "ArrowUp":
      event.preventDefault();
      navigateMenu(-1);
      break;
    case "Home":
      event.preventDefault();
      activeItemIndex.value = 0;
      focusMenuItem();
      break;
    case "End":
      event.preventDefault();
      activeItemIndex.value = menuItems.value.length - 1;
      focusMenuItem();
      break;
    case "Enter":
    case " ":
      if (activeItemIndex.value >= 0) {
        event.preventDefault();
        menuItems.value[activeItemIndex.value].click();
      }
      break;
  }
};

// Navigate menu items
const navigateMenu = (direction: number) => {
  if (menuItems.value.length === 0) {
    updateMenuItemsArray();
    return;
  }

  if (activeItemIndex.value === -1) {
    activeItemIndex.value = direction > 0 ? 0 : menuItems.value.length - 1;
  } else {
    activeItemIndex.value =
      (activeItemIndex.value + direction + menuItems.value.length) %
      menuItems.value.length;
  }

  focusMenuItem();
};

// Focus the current menu item
const focusMenuItem = () => {
  if (
    activeItemIndex.value >= 0 &&
    activeItemIndex.value < menuItems.value.length
  ) {
    menuItems.value[activeItemIndex.value].focus();
  }
};

// Trap focus within menu
const focusTrap = () => {
  triggerRef.value?.blur();
  menuRef.value?.focus();
};

// Update menu items array for keyboard navigation
const updateMenuItemsArray = () => {
  if (!menuRef.value) return;

  // Get all interactive elements in the menu
  menuItems.value = Array.from(
    menuRef.value.querySelectorAll('a, button, [role="menuitem"]')
  ) as HTMLElement[];
};

// Handle menu item click
const handleMenuItemClick = () => {
  if (props.closeOnSelect) {
    isOpen.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  document.addEventListener("keydown", handleKeyDown);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
  document.removeEventListener("keydown", handleKeyDown);
});
</script>

<template>
  <div ref="dropdownRef" class="dropdown relative inline-block">
    <!-- Dropdown Trigger Button -->
    <button
      ref="triggerRef"
      :disabled="disabled"
      :aria-expanded="isOpen"
      aria-haspopup="true"
      :class="[
        buttonSizeClass,
        buttonVariantClass,
        iconOnly ? 'rounded-full p-2' : 'rounded-md',
        disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
        'inline-flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200',
        buttonClass,
      ]"
      @click="toggleDropdown"
    >
      <!-- Left Icon Slot -->
      <span
        v-if="$slots.icon && !iconOnly && iconPosition !== 'right'"
        class="mr-2"
      >
        <slot name="icon"></slot>
      </span>

      <!-- Icon Only Slot -->
      <slot v-if="iconOnly" name="icon"></slot>

      <!-- Label -->
      <span v-if="!iconOnly">{{ label }}</span>

      <!-- Right Icon Slot -->
      <span
        v-if="$slots.icon && !iconOnly && iconPosition === 'right'"
        class="ml-2"
      >
        <slot name="icon"></slot>
      </span>

      <!-- Default Dropdown Arrow (unless iconOnly) -->
      <span v-if="!iconOnly && !$slots.icon" class="ml-2 -mr-0.5">
        <slot name="dropdown-arrow">
          <svg
            class="h-4 w-4"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </slot>
      </span>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        ref="menuRef"
        role="menu"
        tabindex="-1"
        :class="[
          'absolute z-50 mt-1 rounded-md bg-white',
          menuPositionClass,
          bordered ? 'border border-gray-200' : '',
          shadow ? 'shadow-lg' : '',
        ]"
        :style="{
          width: width !== 'auto' ? width : '',
          marginTop: `${offsetY}px`,
        }"
        @click="handleMenuItemClick"
      >
        <div class="py-1">
          <slot />
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.dropdown {
  position: relative;
  display: inline-block;
}

/* Custom focus styles for menu items */
:deep([role="menuitem"]) {
  display: block;
  width: 100%;
  padding: 0.5rem 1rem;
  text-align: left;
  font-size: 0.875rem;
  color: #374151;
  transition: background-color 0.15s ease-in-out;
}

:deep([role="menuitem"]:hover) {
  background-color: #f3f4f6;
}

:deep([role="menuitem"]:focus) {
  outline: none;
  background-color: #f3f4f6;
}

:deep([role="menuitem"].active) {
  background-color: #eff6ff;
  color: #4f46e5;
}

:deep([role="menuitem"]:disabled) {
  color: #9ca3af;
  cursor: not-allowed;
}

/* Add dividers */
:deep(.dropdown-divider) {
  height: 0;
  margin: 0.5rem 0;
  overflow: hidden;
  border-top: 1px solid #e5e7eb;
}

/* Dropdown header */
:deep(.dropdown-header) {
  display: block;
  padding: 0.5rem 1rem;
  margin-bottom: 0;
  font-size: 0.75rem;
  color: #6b7280;
  white-space: nowrap;
  text-transform: uppercase;
  font-weight: 600;
}
</style>
