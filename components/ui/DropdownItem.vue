<script setup lang="ts">
interface DropdownItemProps {
  /** Is the item a link */
  href?: string;
  /** Is the item active */
  active?: boolean;
  /** Disable the menu item */
  disabled?: boolean;
  /** Danger style */
  danger?: boolean;
  /** Show chevron icon at the end */
  showChevron?: boolean;
  /** Additional CSS classes */
  class?: string;
}

const props = withDefaults(defineProps<DropdownItemProps>(), {
  href: "",
  active: false,
  disabled: false,
  danger: false,
  showChevron: false,
  class: "",
});

// Computed styles
const itemClasses = computed(() => [
  "flex items-center w-full px-4 py-2 text-sm transition-colors",
  props.disabled ? "text-gray-400 cursor-not-allowed" : "cursor-pointer",
  props.active
    ? "bg-indigo-50 text-indigo-700 font-medium"
    : "text-gray-700 hover:bg-gray-50",
  props.danger && !props.disabled ? "text-red-600 hover:bg-red-50" : "",
  props.class,
]);

// Emit events
const emit = defineEmits(["click"]);

const handleClick = (event: MouseEvent) => {
  if (props.disabled) {
    event.preventDefault();
    return;
  }

  emit("click", event);
};
</script>

<template>
  <!-- Render as anchor if href is provided -->
  <a
    v-if="href && !disabled"
    :href="href"
    role="menuitem"
    :class="itemClasses"
    @click="handleClick"
  >
    <!-- Icon slot (left) -->
    <span v-if="$slots.icon" class="mr-2">
      <slot name="icon"/>
    </span>

    <!-- Main content -->
    <span class="flex-grow">
      <slot />
    </span>

    <!-- Chevron icon on the right -->
    <span v-if="showChevron || $slots.chevron" class="ml-2">
      <slot name="chevron">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"
          />
        </svg>
      </slot>
    </span>
  </a>

  <!-- Render as button otherwise -->
  <button
    v-else
    type="button"
    role="menuitem"
    :class="itemClasses"
    :disabled="disabled"
    @click="handleClick"
  >
    <div class="flex items-center w-full">
      <!-- Icon slot (left) -->
      <span v-if="$slots.icon" class="mr-2">
        <slot name="icon"/>
      </span>

      <!-- Main content -->
      <span class="flex-grow">
        <slot />
      </span>

      <!-- Chevron icon on the right -->
      <span v-if="showChevron || $slots.chevron" class="ml-2">
        <slot name="chevron">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </slot>
      </span>
    </div>
  </button>
</template>
