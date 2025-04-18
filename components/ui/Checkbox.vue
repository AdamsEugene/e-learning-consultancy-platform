<script setup lang="ts">
interface Props {
  modelValue: boolean;
  label?: string;
  disabled?: boolean;
  required?: boolean;
  name?: string;
  id?: string;
  size?: "sm" | "md" | "lg";
  color?: "primary" | "secondary" | "success" | "danger" | "warning" | "info";
  indeterminate?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  label: "",
  disabled: false,
  required: false,
  name: "",
  id: "",
  size: "md",
  color: "primary",
  indeterminate: false,
});

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "change", event: Event): void;
}>();

const checkboxRef = ref<HTMLInputElement | null>(null);
const isHovered = ref(false);
const isFocused = ref(false);

// Watch for indeterminate state changes
watch(
  () => props.indeterminate,
  (value) => {
    if (checkboxRef.value) {
      checkboxRef.value.indeterminate = value;
    }
  }
);

// Set initial indeterminate state
onMounted(() => {
  if (checkboxRef.value && props.indeterminate) {
    checkboxRef.value.indeterminate = true;
  }
});

// Computed classes for the checkbox wrapper
const wrapperClasses = computed(() => {
  const baseClasses = "inline-flex items-center";
  const sizeClasses = {
    sm: "gap-1.5",
    md: "gap-2",
    lg: "gap-2.5",
  };
  const stateClasses = props.disabled
    ? "cursor-not-allowed opacity-50"
    : "cursor-pointer";

  return [baseClasses, sizeClasses[props.size], stateClasses]
    .filter(Boolean)
    .join(" ");
});

// Computed classes for the checkbox
const checkboxClasses = computed(() => {
  const baseClasses =
    "relative shrink-0 border rounded transition-all duration-200";

  const sizeClasses = {
    sm: "h-3.5 w-3.5",
    md: "h-4 w-4",
    lg: "h-5 w-5",
  };

  const colorClasses = {
    primary: {
      border: props.modelValue ? "border-indigo-600" : "border-gray-300",
      bg: props.modelValue ? "bg-indigo-600" : "bg-white",
      hover:
        !props.disabled && !props.modelValue ? "hover:border-indigo-500" : "",
    },
    secondary: {
      border: props.modelValue ? "border-gray-600" : "border-gray-300",
      bg: props.modelValue ? "bg-gray-600" : "bg-white",
      hover:
        !props.disabled && !props.modelValue ? "hover:border-gray-500" : "",
    },
    success: {
      border: props.modelValue ? "border-green-600" : "border-gray-300",
      bg: props.modelValue ? "bg-green-600" : "bg-white",
      hover:
        !props.disabled && !props.modelValue ? "hover:border-green-500" : "",
    },
    danger: {
      border: props.modelValue ? "border-red-600" : "border-gray-300",
      bg: props.modelValue ? "bg-red-600" : "bg-white",
      hover: !props.disabled && !props.modelValue ? "hover:border-red-500" : "",
    },
    warning: {
      border: props.modelValue ? "border-yellow-600" : "border-gray-300",
      bg: props.modelValue ? "bg-yellow-600" : "bg-white",
      hover:
        !props.disabled && !props.modelValue ? "hover:border-yellow-500" : "",
    },
    info: {
      border: props.modelValue ? "border-blue-600" : "border-gray-300",
      bg: props.modelValue ? "bg-blue-600" : "bg-white",
      hover:
        !props.disabled && !props.modelValue ? "hover:border-blue-500" : "",
    },
  };

  const focusClasses = isFocused.value ? "ring-2 ring-offset-2" : "";
  const ringColorClasses = {
    primary: "ring-indigo-200",
    secondary: "ring-gray-200",
    success: "ring-green-200",
    danger: "ring-red-200",
    warning: "ring-yellow-200",
    info: "ring-blue-200",
  };

  return [
    baseClasses,
    sizeClasses[props.size],
    colorClasses[props.color].border,
    colorClasses[props.color].bg,
    colorClasses[props.color].hover,
    focusClasses,
    ringColorClasses[props.color],
  ]
    .filter(Boolean)
    .join(" ");
});

// Computed classes for the label
const labelClasses = computed(() => {
  const baseClasses = "select-none";
  const sizeClasses = {
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
  };
  const stateClasses = props.disabled ? "text-gray-400" : "text-gray-700";

  return [baseClasses, sizeClasses[props.size], stateClasses]
    .filter(Boolean)
    .join(" ");
});

// Handle checkbox change
const handleChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  emit("update:modelValue", target.checked);
  emit("change", event);
};
</script>

<!-- eslint-disable vue/html-self-closing -->

<template>
  <label
    :class="wrapperClasses"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <div class="relative">
      <input
        :id="id"
        ref="checkboxRef"
        type="checkbox"
        :checked="modelValue"
        :disabled="disabled"
        :required="required"
        :name="name"
        class="sr-only peer"
        @change="handleChange"
        @focus="isFocused = true"
        @blur="isFocused = false"
      />

      <div :class="checkboxClasses">
        <!-- Checkmark icon -->
        <svg
          v-if="modelValue && !indeterminate"
          class="absolute inset-0 h-full w-full stroke-white p-0.5"
          viewBox="0 0 20 20"
          fill="none"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <polyline points="4 10 8 14 16 6" />
        </svg>

        <!-- Indeterminate icon -->
        <svg
          v-if="indeterminate"
          class="absolute inset-0 h-full w-full stroke-white p-0.5"
          viewBox="0 0 20 20"
          fill="none"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <line x1="5" y1="10" x2="15" y2="10" />
        </svg>

        <!-- Hover/Focus ripple effect -->
        <div
          v-if="isHovered || isFocused"
          class="absolute -inset-2 transition-opacity"
          :class="[
            `bg-${props.color}-500/10`,
            'rounded-full',
            isHovered ? 'opacity-100' : 'opacity-0',
          ]"
        />
      </div>
    </div>

    <span v-if="label" :class="labelClasses">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </span>

    <!-- Pass through any default slot content -->
    <slot />
  </label>
</template>

<style scoped>
.peer:focus-visible + div {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0
    var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0
    calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow),
    var(--tw-shadow, 0 0 #0000);
  --tw-ring-offset-width: 2px;
}

/* Smooth transitions */
.peer:checked + div svg {
  transform: scale(1);
  transition: transform 0.15s cubic-bezier(0.2, 0, 0.13, 1.5);
}

.peer:not(:checked) + div svg {
  transform: scale(0);
  transition: transform 0.15s cubic-bezier(0.4, 0, 0.23, 1);
}
</style>
