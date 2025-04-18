<script setup lang="ts">
interface Props {
  modelValue: string;
  label?: string;
  placeholder?: string;
  helperText?: string;
  errorMessage?: string;
  required?: boolean;
  disabled?: boolean;
  readonly?: boolean;
  autocomplete?: string;
  name?: string;
  id?: string;
  size?: "sm" | "md" | "lg";
  variant?: "outlined" | "filled" | "flushed" | "unstyled";
  color?: "primary" | "secondary" | "success" | "danger" | "warning" | "info";
  rows?: number;
  maxLength?: number;
  minLength?: number;
  resize?: "none" | "vertical" | "horizontal" | "both";
  autofocus?: boolean;
  tabindex?: number;
}

const props = withDefaults(defineProps<Props>(), {
  //   modelValue: "",
  label: "",
  placeholder: "",
  helperText: "",
  errorMessage: "",
  required: false,
  disabled: false,
  readonly: false,
  autocomplete: "off",
  name: "",
  id: "",
  size: "md",
  variant: "outlined",
  color: "primary",
  rows: 4,
  maxLength: undefined,
  minLength: undefined,
  resize: "vertical",
  autofocus: false,
  tabindex: undefined,
});

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
  (e: "focus" | "blur", event: FocusEvent): void;
  (e: "input" | "change", event: Event): void;
  (e: "keydown" | "keyup" | "keypress", event: KeyboardEvent): void;
}>();

const textareaRef = ref<HTMLTextAreaElement | null>(null);
const isFocused = ref(false);
const internalValue = ref(props.modelValue);

// Watch for modelValue changes
watch(
  () => props.modelValue,
  (newValue) => {
    internalValue.value = newValue;
  }
);

// Watch for internal value changes
watch(
  () => internalValue.value,
  (newValue) => {
    emit("update:modelValue", newValue);
  }
);

// Computed classes for the textarea
const textareaClasses = computed(() => {
  const baseClasses =
    "w-full transition-all duration-200 ease-in-out outline-none border";

  // Size classes
  const sizeClasses = {
    sm: "px-3 py-1.5 text-sm",
    md: "px-4 py-2 text-base",
    lg: "px-5 py-3 text-lg",
  };

  // Variant classes
  const variantClasses = {
    outlined: "rounded-md bg-white",
    filled: "rounded-md bg-gray-50 border-transparent",
    flushed: "rounded-none bg-transparent border-t-0 border-x-0 px-0",
    unstyled: "border-none bg-transparent px-0",
  };

  // Color classes
  const colorClasses = {
    primary: {
      border: "border-gray-200 hover:border-indigo-500",
      focus: "focus:border-indigo-500 focus:ring-0",
      error: "border-red-500 focus:border-red-500 focus:ring-0",
    },
    secondary: {
      border: "border-gray-200 hover:border-gray-500",
      focus: "focus:border-gray-500 focus:ring-0",
      error: "border-red-500 focus:border-red-500 focus:ring-0",
    },
    success: {
      border: "border-gray-200 hover:border-green-500",
      focus: "focus:border-green-500 focus:ring-0",
      error: "border-red-500 focus:border-red-500 focus:ring-0",
    },
    danger: {
      border: "border-gray-200 hover:border-red-500",
      focus: "focus:border-red-500 focus:ring-0",
      error: "border-red-500 focus:border-red-500 focus:ring-0",
    },
    warning: {
      border: "border-gray-200 hover:border-yellow-500",
      focus: "focus:border-yellow-500 focus:ring-0",
      error: "border-red-500 focus:border-red-500 focus:ring-0",
    },
    info: {
      border: "border-gray-200 hover:border-blue-500",
      focus: "focus:border-blue-500 focus:ring-0",
      error: "border-red-500 focus:border-red-500 focus:ring-0",
    },
  };

  // State classes
  const stateClasses = {
    disabled: "opacity-60 cursor-not-allowed",
    readonly: "bg-gray-50 cursor-default",
    error: "text-red-900 placeholder-red-300",
  };

  // Resize classes
  const resizeClasses = {
    none: "resize-none",
    vertical: "resize-y",
    horizontal: "resize-x",
    both: "resize",
  };

  // Combine all classes
  return [
    baseClasses,
    sizeClasses[props.size],
    variantClasses[props.variant],
    colorClasses[props.color].border,
    props.errorMessage
      ? colorClasses[props.color].error
      : colorClasses[props.color].focus,
    props.disabled ? stateClasses.disabled : "",
    props.readonly ? stateClasses.readonly : "",
    props.errorMessage ? stateClasses.error : "",
    isFocused.value ? "ring-1" : "",
    resizeClasses[props.resize],
  ]
    .filter(Boolean)
    .join(" ");
});

// Computed classes for the label
const labelClasses = computed(() => {
  const baseClasses = "block font-medium mb-1";

  // Size classes
  const sizeClasses = {
    sm: "text-xs",
    md: "text-sm",
    lg: "text-base",
  };

  // Color classes
  const colorClasses = {
    primary: "text-gray-700",
    secondary: "text-gray-700",
    success: "text-gray-700",
    danger: "text-gray-700",
    warning: "text-gray-700",
    info: "text-gray-700",
  };

  // Error state
  const errorClass = props.errorMessage ? "text-red-600" : "";

  return [
    baseClasses,
    sizeClasses[props.size],
    colorClasses[props.color],
    errorClass,
  ]
    .filter(Boolean)
    .join(" ");
});

// Computed classes for the helper text
const helperTextClasses = computed(() => {
  const baseClasses = "mt-1";

  // Size classes
  const sizeClasses = {
    sm: "text-xs",
    md: "text-sm",
    lg: "text-base",
  };

  // Color classes
  const colorClasses = {
    primary: "text-gray-500",
    secondary: "text-gray-500",
    success: "text-gray-500",
    danger: "text-gray-500",
    warning: "text-gray-500",
    info: "text-gray-500",
  };

  // Error state
  const errorClass = props.errorMessage ? "text-red-500" : "";

  return [
    baseClasses,
    sizeClasses[props.size],
    colorClasses[props.color],
    errorClass,
  ]
    .filter(Boolean)
    .join(" ");
});

// Computed classes for the error message
const errorMessageClasses = computed(() => {
  const baseClasses = "mt-1";

  // Size classes
  const sizeClasses = {
    sm: "text-xs",
    md: "text-sm",
    lg: "text-base",
  };

  return [baseClasses, sizeClasses[props.size], "text-red-500"]
    .filter(Boolean)
    .join(" ");
});

// Methods
const handleFocus = (event: FocusEvent) => {
  isFocused.value = true;
  emit("focus", event);
};

const handleBlur = (event: FocusEvent) => {
  isFocused.value = false;
  emit("blur", event);
};

const handleInput = (event: Event) => {
  emit("input", event);
};

const handleChange = (event: Event) => {
  emit("change", event);
};

const handleKeydown = (event: KeyboardEvent) => {
  emit("keydown", event);
};

const handleKeyup = (event: KeyboardEvent) => {
  emit("keyup", event);
};

const handleKeypress = (event: KeyboardEvent) => {
  emit("keypress", event);
};

// Focus method
const focus = () => {
  textareaRef.value?.focus();
};

// Blur method
const blur = () => {
  textareaRef.value?.blur();
};

// Select method
const select = () => {
  textareaRef.value?.select();
};

// Expose methods
defineExpose({
  focus,
  blur,
  select,
});
</script>

<template>
  <div class="w-full">
    <!-- Label -->
    <label v-if="label" :for="id || name" :class="labelClasses">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Textarea element -->
    <textarea
      :id="id || name"
      ref="textareaRef"
      :name="name"
      :value="internalValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :readonly="readonly"
      :autocomplete="autocomplete"
      :maxlength="maxLength"
      :minlength="minLength"
      :rows="rows"
      :autofocus="autofocus"
      :tabindex="tabindex"
      :class="textareaClasses"
      @input="handleInput"
      @change="handleChange"
      @focus="handleFocus"
      @blur="handleBlur"
      @keydown="handleKeydown"
      @keyup="handleKeyup"
      @keypress="handleKeypress"
    />

    <!-- Helper text -->
    <p v-if="helperText && !errorMessage" :class="helperTextClasses">
      {{ helperText }}
    </p>

    <!-- Error message -->
    <p v-if="errorMessage" :class="errorMessageClasses">
      {{ errorMessage }}
    </p>
  </div>
</template>

<style scoped>
/* Focus animation */
textarea:focus {
  animation: focus-ring 0.3s ease-in-out;
}

@keyframes focus-ring {
  0% {
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
  }
  70% {
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
  }
}

/* Hover effect for filled variant */
textarea.filled:hover:not(:disabled):not(:readonly) {
  background-color: rgb(243, 244, 246);
}

/* Transition for the textarea */
textarea {
  transition: all 0.2s ease-in-out;
  outline: none;
}

/* Custom styling for disabled state */
textarea:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* Custom styling for readonly state */
textarea:read-only {
  cursor: default;
  background-color: rgb(249, 250, 251);
}

/* Custom styling for error state */
textarea.error {
  border-color: rgb(239, 68, 68);
  color: rgb(127, 29, 29);
}

textarea.error::placeholder {
  color: rgb(252, 165, 165);
}

/* Custom styling for success state */
textarea.success {
  border-color: rgb(34, 197, 94);
  color: rgb(22, 101, 52);
}

textarea.success::placeholder {
  color: rgb(187, 247, 208);
}

/* Custom styling for warning state */
textarea.warning {
  border-color: rgb(234, 179, 8);
  color: rgb(133, 77, 14);
}

textarea.warning::placeholder {
  color: rgb(254, 240, 138);
}

/* Custom styling for info state */
textarea.info {
  border-color: rgb(59, 130, 246);
  color: rgb(30, 64, 175);
}

textarea.info::placeholder {
  color: rgb(191, 219, 254);
}

/* Custom styling for primary state */
textarea.primary {
  border-color: rgb(99, 102, 241);
  color: rgb(30, 27, 75);
}

textarea.primary::placeholder {
  color: rgb(199, 210, 254);
}

/* Custom styling for secondary state */
textarea.secondary {
  border-color: rgb(107, 114, 128);
  color: rgb(17, 24, 39);
}

textarea.secondary::placeholder {
  color: rgb(209, 213, 219);
}

/* Custom styling for small size */
textarea.sm {
  font-size: 0.875rem;
}

/* Custom styling for medium size */
textarea.md {
  font-size: 1rem;
}

/* Custom styling for large size */
textarea.lg {
  font-size: 1.125rem;
}

/* Custom styling for outlined variant */
textarea.outlined {
  border: 1px solid;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Custom styling for filled variant */
textarea.filled {
  border: 1px solid transparent;
  border-radius: 0.375rem;
  background-color: rgb(243, 244, 246);
}

/* Custom styling for flushed variant */
textarea.flushed {
  border-bottom: 1px solid;
  border-radius: 0;
  background-color: transparent;
}

/* Custom styling for unstyled variant */
textarea.unstyled {
  border: none;
  background-color: transparent;
}
</style>
