<script setup lang="ts">
interface Props {
  /**
   * Button variant
   * @default 'primary'
   */
  variant?:
    | "primary"
    | "secondary"
    | "tertiary"
    | "danger"
    | "success"
    | "warning"
    | "info"
    | "ghost";

  /**
   * Button size
   * @default 'md'
   */
  size?: "sm" | "md" | "lg" | "xl";

  /**
   * Icon position (when using icon + text)
   * @default 'left'
   */
  iconPosition?: "left" | "right" | "only";

  /**
   * Button visual state
   * @default 'default'
   */
  state?: "default" | "hover" | "active" | "disabled";

  /**
   * Badge text or count to display
   */
  badge?: string | number;

  /**
   * Badge color variant
   * @default 'primary'
   */
  badgeVariant?: "primary" | "success" | "warning" | "danger" | "info";

  /**
   * Button type attribute
   * @default 'button'
   */
  type?: "button" | "submit" | "reset";

  /**
   * Whether the button is disabled
   * @default false
   */
  disabled?: boolean;

  /**
   * Whether the button is in loading state
   * @default false
   */
  loading?: boolean;

  /**
   * Whether the button is currently active (selected)
   * @default false
   */
  active?: boolean;

  /**
   * Whether the button should be full width
   * @default false
   */
  fullWidth?: boolean;

  /**
   * Whether the button has a ripple effect on click
   * @default true
   */
  ripple?: boolean;

  /**
   * Whether the button is elevated (has shadow)
   * @default false
   */
  elevated?: boolean;

  /**
   * Whether the button is outlined
   * @default false
   */
  outlined?: boolean;

  /**
   * Whether the button is rounded (pill shape)
   * @default false
   */
  rounded?: boolean;

  /**
   * Custom classes to add to the button
   */
  customClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: "primary",
  size: "md",
  iconPosition: "left",
  state: "default",
  badgeVariant: "primary",
  type: "button",
  disabled: false,
  loading: false,
  active: false,
  fullWidth: false,
  ripple: false,
  elevated: false,
  outlined: false,
  rounded: false,
  badge: undefined,
  customClass: undefined,
});

const emit = defineEmits<{
  (e: "click", event: MouseEvent): void;
}>();

// Create ripple effect
const createRipple = (event: MouseEvent) => {
  if (!props.ripple || props.disabled || props.loading) return;

  const button = event.currentTarget as HTMLElement;
  const circle = document.createElement("span");
  const diameter = Math.max(button.clientWidth, button.clientHeight);
  const radius = diameter / 2;

  // Position the ripple
  circle.style.width = circle.style.height = `${diameter}px`;
  circle.style.left = `${
    event.clientX - (button.getBoundingClientRect().left + radius)
  }px`;
  circle.style.top = `${
    event.clientY - (button.getBoundingClientRect().top + radius)
  }px`;
  circle.classList.add("ripple");

  // Remove existing ripples
  const ripple = button.querySelector(".ripple");
  if (ripple) {
    ripple.remove();
  }

  // Add the new ripple
  button.appendChild(circle);

  // Remove the ripple after animation completes
  setTimeout(() => {
    if (circle) {
      circle.remove();
    }
  }, 600);
};

// Handle button click
const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    createRipple(event);
    emit("click", event);
  }
};

// Helper function for generating size classes
const sizeClasses = computed(() => {
  switch (props.size) {
    case "sm":
      return "h-8 text-xs py-1 px-3 gap-1.5";
    case "md":
      return "h-10 text-sm py-2 px-4 gap-2";
    case "lg":
      return "h-12 text-base py-2.5 px-5 gap-2.5";
    case "xl":
      return "h-14 text-lg py-3 px-6 gap-3";
    default:
      return "h-10 text-sm py-2 px-4 gap-2";
  }
});

// Helper function for generating icon-only size classes
const iconOnlyClasses = computed(() => {
  if (props.iconPosition !== "only") return "";

  switch (props.size) {
    case "sm":
      return "h-8 w-8 p-1";
    case "md":
      return "h-10 w-10 p-2";
    case "lg":
      return "h-12 w-12 p-3";
    case "xl":
      return "h-14 w-14 p-3.5";
    default:
      return "h-10 w-10 p-2";
  }
});

// Generate variant-based classes
const variantClasses = computed(() => {
  // Base classes common to all variants
  const baseClasses =
    "relative overflow-hidden transition-all duration-200 focus:outline-none focus:ring-2";

  // Classes for disabled state (override all other states)
  if (props.disabled) {
    return `${baseClasses} opacity-50 cursor-not-allowed bg-gray-100 text-gray-400 border border-gray-200`;
  }

  // Classes for when button is loading
  if (props.loading) {
    return `${baseClasses} cursor-wait`;
  }

  // Apply variant-specific classes
  switch (props.variant) {
    case "primary":
      if (props.outlined) {
        return `${baseClasses} bg-transparent text-indigo-600 border border-indigo-600 hover:bg-indigo-50 active:bg-indigo-100 focus:ring-indigo-500`;
      }
      return `${baseClasses} bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 focus:ring-indigo-500 ${
        props.elevated ? "shadow-md hover:shadow-lg" : ""
      }`;

    case "secondary":
      if (props.outlined) {
        return `${baseClasses} bg-transparent text-gray-700 border border-gray-300 hover:bg-gray-50 active:bg-gray-100 focus:ring-gray-500`;
      }
      return `${baseClasses} bg-gray-200 text-gray-800 hover:bg-gray-300 active:bg-gray-400 focus:ring-gray-500 ${
        props.elevated ? "shadow-md hover:shadow-lg" : ""
      }`;

    case "tertiary":
      return `${baseClasses} bg-transparent text-gray-700 hover:bg-gray-100 active:bg-gray-200 focus:ring-gray-500`;

    case "success":
      if (props.outlined) {
        return `${baseClasses} bg-transparent text-green-600 border border-green-600 hover:bg-green-50 active:bg-green-100 focus:ring-green-500`;
      }
      return `${baseClasses} bg-green-600 text-white hover:bg-green-700 active:bg-green-800 focus:ring-green-500 ${
        props.elevated ? "shadow-md hover:shadow-lg" : ""
      }`;

    case "danger":
      if (props.outlined) {
        return `${baseClasses} bg-transparent text-red-600 border border-red-600 hover:bg-red-50 active:bg-red-100 focus:ring-red-500`;
      }
      return `${baseClasses} bg-red-600 text-white hover:bg-red-700 active:bg-red-800 focus:ring-red-500 ${
        props.elevated ? "shadow-md hover:shadow-lg" : ""
      }`;

    case "warning":
      if (props.outlined) {
        return `${baseClasses} bg-transparent text-amber-600 border border-amber-600 hover:bg-amber-50 active:bg-amber-100 focus:ring-amber-500`;
      }
      return `${baseClasses} bg-amber-500 text-white hover:bg-amber-600 active:bg-amber-700 focus:ring-amber-500 ${
        props.elevated ? "shadow-md hover:shadow-lg" : ""
      }`;

    case "info":
      if (props.outlined) {
        return `${baseClasses} bg-transparent text-blue-600 border border-blue-600 hover:bg-blue-50 active:bg-blue-100 focus:ring-blue-500`;
      }
      return `${baseClasses} bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 focus:ring-blue-500 ${
        props.elevated ? "shadow-md hover:shadow-lg" : ""
      }`;

    case "ghost":
      return `${baseClasses} bg-transparent text-gray-700 hover:bg-gray-100 active:bg-gray-200 focus:ring-gray-500`;

    default:
      return `${baseClasses} bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 focus:ring-indigo-500`;
  }
});

// Generate badge classes based on variant
const badgeClasses = computed(() => {
  const baseBadgeClasses =
    "absolute -top-2 -right-2 text-xs font-bold px-1.5 py-0.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full";

  switch (props.badgeVariant) {
    case "primary":
      return `${baseBadgeClasses} bg-indigo-600 text-white`;
    case "success":
      return `${baseBadgeClasses} bg-green-600 text-white`;
    case "warning":
      return `${baseBadgeClasses} bg-amber-500 text-white`;
    case "danger":
      return `${baseBadgeClasses} bg-red-600 text-white`;
    case "info":
      return `${baseBadgeClasses} bg-blue-600 text-white`;
    default:
      return `${baseBadgeClasses} bg-indigo-600 text-white`;
  }
});

// Combined classes for the button
const buttonClasses = computed(() => {
  return [
    // Core styles
    "inline-flex items-center justify-center font-medium transition-all",

    // Size classes
    props.iconPosition === "only" ? iconOnlyClasses.value : sizeClasses.value,

    // Variant classes
    variantClasses.value,

    // Full width
    props.fullWidth ? "w-full" : "",

    // Rounded pill style
    props.rounded ? "rounded-full" : "rounded-lg",

    // Active state
    props.active ? "!ring-2 !ring-offset-2" : "",

    // Custom classes
    props.customClass || "",
  ];
});
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="buttonClasses"
    role="button"
    @click="handleClick"
  >
    <!-- Loading spinner -->
    <span
      v-if="loading"
      class="flex items-center justify-center absolute inset-0 bg-inherit"
    >
      <svg
        class="animate-spin h-5 w-5"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
      </svg>
    </span>

    <!-- Button content (hidden during loading) -->
    <span
      :class="{ 'opacity-0': loading, 'flex items-center': true }"
      class="flex items-center"
    >
      <!-- Left icon slot -->
      <slot v-if="$slots['icon-left'] || $slots['icon']" name="icon-left" />

      <!-- Text content (hidden for icon-only buttons) -->
      <span
        :class="{
          'ml-2': $slots['icon-left'] && iconPosition !== 'only',
          'mr-2': $slots['icon-right'] && iconPosition !== 'only',
        }"
      >
        <slot />
      </span>

      <!-- <slot/> -->

      <!-- Right icon slot -->
      <slot v-if="$slots['icon-right'] || $slots['icon']" name="icon-right">
        <slot name="icon" />
      </slot>
    </span>

    <!-- Badge (if provided) -->
    <span v-if="badge" :class="badgeClasses">{{ badge }}</span>
  </button>
</template>

<style scoped>
.ripple {
  position: absolute;
  border-radius: 50%;
  transform: scale(0);
  animation: ripple 0.6s linear;
  background-color: rgba(255, 255, 255, 0.3);
  pointer-events: none;
}

@keyframes ripple {
  to {
    transform: scale(4);
    opacity: 0;
  }
}

/* Button hover animation */
button {
  transform: translateY(0);
  transition: transform 0.15s ease;
}

button:not(:disabled):not(.cursor-wait):hover {
  transform: translateY(-1px);
}

button:not(:disabled):not(.cursor-wait):active {
  transform: translateY(0);
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
