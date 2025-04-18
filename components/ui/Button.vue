<script setup lang="ts">
interface Props {
  // Core props
  type?: "button" | "submit" | "reset";
  variant?: "solid" | "outline" | "ghost" | "link" | "icon" | "text" | "subtle";
  color?:
    | "primary"
    | "secondary"
    | "success"
    | "danger"
    | "warning"
    | "info"
    | "gray"
    | "white";
  size?: "xs" | "sm" | "md" | "lg" | "xl";

  // State props
  loading?: boolean;
  disabled?: boolean;
  active?: boolean;

  // Icon props
  leftIcon?: string;
  rightIcon?: string;
  iconOnly?: boolean;

  // Animation props
  animated?: boolean;
  ripple?: boolean;

  // Additional styling
  rounded?: boolean;
  block?: boolean;
  glass?: boolean;
  elevated?: boolean;
  noPadding?: boolean;
  customClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: "button",
  variant: "solid",
  color: "primary",
  size: "md",
  loading: false,
  disabled: false,
  active: false,
  leftIcon: "",
  rightIcon: "",
  iconOnly: false,
  animated: true,
  ripple: true,
  rounded: true,
  block: false,
  glass: false,
  elevated: false,
  noPadding: false,
  customClass: "",
});

// Emit click event
const emit = defineEmits<{
  (e: "click", event: MouseEvent): void;
}>();

// Handle click with ripple effect
const buttonRef = ref<HTMLButtonElement | null>(null);

interface RippleEffect {
  x: number;
  y: number;
  size: number;
  alpha: number;
  scale: number;
}

const ripples = ref<RippleEffect[]>([]);
const rippleContainer = ref<HTMLElement | null>(null);

const handleClick = (event: MouseEvent) => {
  if (props.disabled || props.loading) return;

  if (props.ripple) {
    const button = buttonRef.value;
    if (!button) return;

    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    const newRipple: RippleEffect = {
      x,
      y,
      size,
      alpha: 0.32,
      scale: 0,
    };

    ripples.value.push(newRipple);

    // Trigger animation
    requestAnimationFrame(() => {
      newRipple.scale = 1;
      newRipple.alpha = 0;

      // Remove ripple after animation
      setTimeout(() => {
        const index = ripples.value.indexOf(newRipple);
        if (index > -1) {
          ripples.value.splice(index, 1);
        }
      }, 550);
    });
  }

  emit("click", event);
};

const handleMouseDown = (event: MouseEvent) => {
  if (!props.ripple || props.disabled || props.loading) return;

  const container = rippleContainer.value;
  if (!container) return;

  const rect = container.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;
  const size = Math.max(rect.width, rect.height) * 2;

  const newRipple: RippleEffect = {
    x,
    y,
    size,
    alpha: 0.32,
    scale: 0,
  };

  ripples.value.push(newRipple);

  // Trigger animation
  requestAnimationFrame(() => {
    newRipple.scale = 1;
    newRipple.alpha = 0;

    // Remove ripple after animation
    setTimeout(() => {
      const index = ripples.value.indexOf(newRipple);
      if (index > -1) {
        ripples.value.splice(index, 1);
      }
    }, 550);
  });
};

// Computed classes
const buttonClasses = computed(() => {
  const classes = [
    // Base classes
    "relative inline-flex items-center justify-center font-medium transition-all duration-200 select-none",
    !props.noPadding && "rounded-lg",

    // Size classes
    !props.noPadding && sizeClasses.value,

    // Variant and color classes
    variantClasses.value,

    // State classes
    {
      "opacity-60 cursor-not-allowed": props.disabled,
      "cursor-wait": props.loading,
      "scale-95": props.active,
    },

    // Additional styling
    {
      "w-full": props.block,
      "rounded-full": props.rounded,
      "backdrop-blur-sm bg-opacity-80": props.glass,
      "p-0 aspect-square": props.iconOnly,
    },

    // Animation classes
    {
      "transform hover:scale-105 active:scale-95":
        props.animated && !props.disabled && !props.loading,
      "overflow-hidden": props.ripple,
    },
  ];

  return classes;
});

const sizeClasses = computed(() => {
  if (props.iconOnly) {
    return {
      xs: "w-6 h-6 text-xs",
      sm: "w-8 h-8 text-sm",
      md: "w-10 h-10 text-base",
      lg: "w-12 h-12 text-lg",
      xl: "w-14 h-14 text-xl",
    }[props.size];
  }

  return {
    xs: "px-2 py-1 text-xs gap-2",
    sm: "px-3 py-1.5 text-sm gap-2.5",
    md: "px-4 py-2 text-base gap-3",
    lg: "px-5 py-2.5 text-lg gap-3.5",
    xl: "px-6 py-3 text-xl gap-4",
  }[props.size];
});

const variantClasses = computed(() => {
  const colorMap = {
    primary: {
      solid: "bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm",
      outline: "border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50",
      ghost: "text-indigo-600 hover:bg-indigo-50",
      link: "text-indigo-600 hover:underline",
      text: "text-indigo-600 hover:text-indigo-700",
      subtle: "bg-indigo-50 text-indigo-700 hover:bg-indigo-100",
    },
    secondary: {
      solid: "bg-gray-600 text-white hover:bg-gray-700 shadow-sm",
      outline: "border-2 border-gray-600 text-gray-600 hover:bg-gray-50",
      ghost: "text-gray-600 hover:bg-gray-50",
      link: "text-gray-600 hover:underline",
      text: "text-gray-600 hover:text-gray-700",
      subtle: "bg-gray-50 text-gray-700 hover:bg-gray-100",
    },
    success: {
      solid: "bg-green-600 text-white hover:bg-green-700 shadow-sm",
      outline: "border-2 border-green-600 text-green-600 hover:bg-green-50",
      ghost: "text-green-600 hover:bg-green-50",
      link: "text-green-600 hover:underline",
      text: "text-green-600 hover:text-green-700",
      subtle: "bg-green-50 text-green-700 hover:bg-green-100",
    },
    danger: {
      solid: "bg-red-600 text-white hover:bg-red-700 shadow-sm",
      outline: "border-2 border-red-600 text-red-600 hover:bg-red-50",
      ghost: "text-red-600 hover:bg-red-50",
      link: "text-red-600 hover:underline",
      text: "text-red-600 hover:text-red-700",
      subtle: "bg-red-50 text-red-700 hover:bg-red-100",
    },
    warning: {
      solid: "bg-yellow-500 text-white hover:bg-yellow-600 shadow-sm",
      outline: "border-2 border-yellow-500 text-yellow-500 hover:bg-yellow-50",
      ghost: "text-yellow-500 hover:bg-yellow-50",
      link: "text-yellow-500 hover:underline",
      text: "text-yellow-500 hover:text-yellow-600",
      subtle: "bg-yellow-50 text-yellow-700 hover:bg-yellow-100",
    },
    info: {
      solid: "bg-blue-600 text-white hover:bg-blue-700 shadow-sm",
      outline: "border-2 border-blue-600 text-blue-600 hover:bg-blue-50",
      ghost: "text-blue-600 hover:bg-blue-50",
      link: "text-blue-600 hover:underline",
      text: "text-blue-600 hover:text-blue-700",
      subtle: "bg-blue-50 text-blue-700 hover:bg-blue-100",
    },
    gray: {
      solid: "bg-gray-500 text-white hover:bg-gray-600 shadow-sm",
      outline: "border-2 border-gray-500 text-gray-500 hover:bg-gray-50",
      ghost: "text-gray-500 hover:bg-gray-50",
      link: "text-gray-500 hover:underline",
      text: "text-gray-500 hover:text-gray-600",
      subtle: "bg-gray-50 text-gray-700 hover:bg-gray-100",
    },
    white: {
      solid: "bg-white text-gray-900 hover:bg-gray-50 shadow-sm",
      outline: "border-2 border-white text-white hover:bg-white/10",
      ghost: "text-white hover:bg-white/10",
      link: "text-white hover:underline",
      text: "text-white hover:text-gray-200",
      subtle: "bg-white/10 text-white hover:bg-white/20",
    },
  };

  const iconOnlyClasses = props.iconOnly
    ? {
        solid: "hover:opacity-80",
        outline: "hover:bg-opacity-10",
        ghost: "hover:bg-opacity-10",
        link: "",
        text: "hover:opacity-80",
        subtle: "hover:bg-opacity-20",
      }
    : {};

  const elevatedClasses = props.elevated ? "shadow-md hover:shadow-lg" : "";
  const customClasses = props.customClass || "";

  return [
    colorMap[props.color][props.variant === "icon" ? "ghost" : props.variant],
    iconOnlyClasses[props.variant === "icon" ? "ghost" : props.variant],
    elevatedClasses,
    customClasses,
  ];
});

const spinnerColorClass = computed(() => {
  return `spinner-${props.color}`;
});
</script>

<template>
  <button
    ref="buttonRef"
    :class="[buttonClasses]"
    :disabled="disabled || loading"
    @click="handleClick"
    @mousedown="handleMouseDown"
  >
    <!-- Loading Spinner -->
    <div
      v-if="loading"
      class="absolute inset-0 flex items-center justify-center"
    >
      <div class="loading-spinner" :class="spinnerColorClass" />
    </div>

    <!-- Button Content -->
    <div
      :class="{ 'opacity-0': loading }"
      class="relative flex items-center justify-center gap-2 transition-opacity duration-200"
    >
      <slot />
    </div>

    <!-- Ripple Container -->
    <div
      v-if="ripple && !disabled && !loading"
      ref="rippleContainer"
      class="absolute inset-0 overflow-hidden rounded-[inherit]"
    >
      <div
        v-for="(_ripple, index) in ripples"
        :key="index"
        class="absolute rounded-full bg-current"
        :style="{
          width: `${_ripple.size}px`,
          height: `${_ripple.size}px`,
          left: `${_ripple.x - _ripple.size / 2}px`,
          top: `${_ripple.y - _ripple.size / 2}px`,
          transform: `scale(${_ripple.scale})`,
          opacity: _ripple.alpha,
          transition:
            'transform 550ms cubic-bezier(0.4, 0, 0.2, 1), opacity 550ms cubic-bezier(0.4, 0, 0.2, 1)',
        }"
      />
    </div>
  </button>
</template>

<style scoped>
.loading-spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid currentColor;
  border-bottom-color: transparent;
  border-radius: 50%;
  display: inline-block;
  animation: rotation 1s linear infinite;
}

@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Color-specific spinner styles */
.spinner-primary {
  border-color: rgb(var(--color-primary-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-secondary {
  border-color: rgb(var(--color-secondary-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-success {
  border-color: rgb(var(--color-success-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-danger {
  border-color: rgb(var(--color-danger-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-warning {
  border-color: rgb(var(--color-warning-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-info {
  border-color: rgb(var(--color-info-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-gray {
  border-color: rgb(var(--color-gray-500) / 0.5);
  border-bottom-color: transparent;
}

.spinner-white {
  border-color: rgba(255, 255, 255, 0.5);
  border-bottom-color: transparent;
}

/* Glass effect */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

/* Hover animations */
.transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Active state animation */
.scale-95 {
  transform: scale(0.95);
}
</style>
