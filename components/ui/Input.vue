<script setup lang="ts">
interface Props {
  modelValue: string | number;
  type?:
    | "text"
    | "password"
    | "email"
    | "number"
    | "tel"
    | "url"
    | "search"
    | "date"
    | "time"
    | "datetime-local"
    | "month"
    | "week"
    | "color"
    | "file";
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
  leftIcon?: string;
  rightIcon?: string;
  clearable?: boolean;
  maxLength?: number;
  minLength?: number;
  pattern?: string;
  min?: number;
  max?: number;
  step?: number;
  multiple?: boolean;
  accept?: string;
  autofocus?: boolean;
  tabindex?: number;
}

const props = withDefaults(defineProps<Props>(), {
  type: "text",
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
  leftIcon: "",
  rightIcon: "",
  clearable: false,
  maxLength: undefined,
  minLength: undefined,
  pattern: undefined,
  min: undefined,
  max: undefined,
  step: undefined,
  multiple: false,
  accept: undefined,
  autofocus: false,
  tabindex: undefined,
});

const emit = defineEmits<{
  (e: "update:modelValue", value: string | number): void;
  (e: "focus" | "blur", event: FocusEvent): void;
  (e: "input" | "change", event: Event): void;
  (e: "keydown" | "keyup" | "keypress", event: KeyboardEvent): void;
  (e: "clear"): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const showPassword = ref(false);
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

// Computed classes for the input
const inputClasses = computed(() => {
  const baseClasses =
    "w-full transition-all duration-200 ease-in-out outline-none";

  // Size classes
  const sizeClasses = {
    sm: "px-3 py-1.5 text-sm",
    md: "px-4 py-2 text-base",
    lg: "px-5 py-3 text-lg",
  };

  // Variant classes
  const variantClasses = {
    outlined: "border rounded-md bg-white shadow-sm",
    filled: "border border-transparent rounded-md bg-gray-50",
    flushed: "border-b rounded-none bg-transparent",
    unstyled: "border-none bg-transparent",
  };

  // Color classes
  const colorClasses = {
    primary: {
      border: "border-gray-200",
      focus: "focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500",
      error:
        "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500",
    },
    secondary: {
      border: "border-gray-200",
      focus: "focus:border-gray-500 focus:ring-1 focus:ring-gray-500",
      error:
        "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500",
    },
    success: {
      border: "border-gray-200",
      focus: "focus:border-green-500 focus:ring-1 focus:ring-green-500",
      error:
        "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500",
    },
    danger: {
      border: "border-gray-200",
      focus: "focus:border-red-500 focus:ring-1 focus:ring-red-500",
      error:
        "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500",
    },
    warning: {
      border: "border-gray-200",
      focus: "focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500",
      error:
        "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500",
    },
    info: {
      border: "border-gray-200",
      focus: "focus:border-blue-500 focus:ring-1 focus:ring-blue-500",
      error:
        "border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500",
    },
  };

  // State classes
  const stateClasses = {
    disabled: "opacity-60 cursor-not-allowed",
    readonly: "bg-gray-50 cursor-default",
    error: "text-red-900 placeholder-red-300",
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
    props.leftIcon ? "pl-10" : "",
    props.rightIcon || props.clearable || props.type === "password"
      ? "pr-10"
      : "",
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

// Computed classes for the icon container
const iconClasses = computed(() => {
  const baseClasses =
    "absolute inset-y-0 flex items-center pointer-events-none";

  // Size classes
  const sizeClasses = {
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
  };

  // Color classes
  const colorClasses = {
    primary: "text-gray-400",
    secondary: "text-gray-400",
    success: "text-gray-400",
    danger: "text-gray-400",
    warning: "text-gray-400",
    info: "text-gray-400",
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

// Computed classes for the right icon container
const rightIconClasses = computed(() => {
  const baseClasses = "absolute inset-y-0 right-0 flex items-center";

  // Size classes
  const sizeClasses = {
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
  };

  // Color classes
  const colorClasses = {
    primary: "text-gray-400",
    secondary: "text-gray-400",
    success: "text-gray-400",
    danger: "text-gray-400",
    warning: "text-gray-400",
    info: "text-gray-400",
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

// Computed classes for the clear button
const clearButtonClasses = computed(() => {
  return "p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500";
});

// Computed classes for the password toggle button
const passwordToggleClasses = computed(() => {
  return "p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500";
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

const handleClear = () => {
  internalValue.value = "";
  emit("clear");
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

// Focus method
const focus = () => {
  inputRef.value?.focus();
};

// Blur method
const blur = () => {
  inputRef.value?.blur();
};

// Select method
const select = () => {
  inputRef.value?.select();
};

// Expose methods
defineExpose({
  focus,
  blur,
  select,
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="w-full">
    <!-- Label -->
    <label v-if="label" :for="id || name" :class="labelClasses">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Input container -->
    <div class="relative">
      <!-- Left icon -->
      <div v-if="leftIcon" :class="[iconClasses, 'left-0 pl-3']">
        <Icon :name="leftIcon" />
      </div>

      <!-- Input element -->
      <input
        :id="id || name"
        ref="inputRef"
        :name="name"
        :type="type === 'password' && showPassword ? 'text' : type"
        :value="internalValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        :autocomplete="autocomplete"
        :maxlength="maxLength"
        :minlength="minLength"
        :pattern="pattern"
        :min="min"
        :max="max"
        :step="step"
        :multiple="multiple"
        :accept="accept"
        :autofocus="autofocus"
        :tabindex="tabindex"
        :class="inputClasses"
        @input="handleInput"
        @change="handleChange"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
        @keyup="handleKeyup"
        @keypress="handleKeypress"
      />

      <!-- Right icon or actions -->
      <div
        v-if="rightIcon || clearable || (type === 'password' && internalValue)"
        :class="[rightIconClasses, 'pr-3']"
      >
        <!-- Right icon -->
        <Icon v-if="rightIcon" :name="rightIcon" class="mr-1" />

        <!-- Clear button -->
        <button
          v-if="clearable && internalValue"
          type="button"
          :class="clearButtonClasses"
          @click="handleClear"
        >
          <Icon name="ic:baseline-close" />
        </button>

        <!-- Password toggle -->
        <button
          v-if="type === 'password' && internalValue"
          type="button"
          :class="passwordToggleClasses"
          @click="togglePasswordVisibility"
        >
          <Icon
            :name="
              showPassword
                ? 'ic:baseline-visibility-off'
                : 'ic:baseline-visibility'
            "
          />
        </button>
      </div>
    </div>

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
input:focus {
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
input.filled:hover:not(:disabled):not(:readonly) {
  background-color: rgb(243, 244, 246);
}

/* Transition for the input */
input {
  transition: all 0.2s ease-in-out;
  outline: none;
}

/* Custom styling for file input */
input[type="file"] {
  cursor: pointer;
}

/* Custom styling for number input */
input[type="number"] {
  appearance: none;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  appearance: none;
  margin: 0;
}

/* Custom styling for date input */
input[type="date"],
input[type="time"],
input[type="datetime-local"],
input[type="month"],
input[type="week"] {
  appearance: none;
}

/* Custom styling for color input */
input[type="color"] {
  height: 2.5rem;
  width: 2.5rem;
  padding: 0.25rem;
  border-radius: 0.375rem;
  cursor: pointer;
}

/* Custom styling for range input */
input[type="range"] {
  appearance: none;
  height: 0.5rem;
  background-color: rgb(229, 231, 235);
  border-radius: 0.5rem;
}

input[type="range"]::-webkit-slider-thumb {
  appearance: none;
  width: 1rem;
  height: 1rem;
  background-color: rgb(99, 102, 241);
  border-radius: 9999px;
  cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
  appearance: none;
  width: 1rem;
  height: 1rem;
  background-color: rgb(99, 102, 241);
  border-radius: 9999px;
  cursor: pointer;
  border: 0;
}

/* Custom styling for search input */
input[type="search"] {
  appearance: none;
}

input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button,
input[type="search"]::-webkit-search-results-button,
input[type="search"]::-webkit-search-results-decoration {
  appearance: none;
}

/* Custom styling for checkbox and radio */
input[type="checkbox"],
input[type="radio"] {
  appearance: none;
  width: 1rem;
  height: 1rem;
  border: 1px solid rgb(209, 213, 219);
  border-radius: 0.25rem;
}

input[type="checkbox"]:checked {
  background-color: rgb(99, 102, 241);
  border-color: rgb(99, 102, 241);
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}

input[type="radio"] {
  border-radius: 9999px;
}

input[type="radio"]:checked {
  background-color: rgb(99, 102, 241);
  border-color: rgb(99, 102, 241);
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
}

/* Custom styling for disabled state */
input:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* Custom styling for readonly state */
input:read-only {
  cursor: default;
  background-color: rgb(249, 250, 251);
}

/* Custom styling for error state */
input.error {
  border-color: rgb(239, 68, 68);
  color: rgb(127, 29, 29);
}

input.error::placeholder {
  color: rgb(252, 165, 165);
}

/* Custom styling for success state */
input.success {
  border-color: rgb(34, 197, 94);
  color: rgb(22, 101, 52);
}

input.success::placeholder {
  color: rgb(187, 247, 208);
}

/* Custom styling for warning state */
input.warning {
  border-color: rgb(234, 179, 8);
  color: rgb(133, 77, 14);
}

input.warning::placeholder {
  color: rgb(254, 240, 138);
}

/* Custom styling for info state */
input.info {
  border-color: rgb(59, 130, 246);
  color: rgb(30, 64, 175);
}

input.info::placeholder {
  color: rgb(191, 219, 254);
}

/* Custom styling for primary state */
input.primary {
  border-color: rgb(99, 102, 241);
  color: rgb(30, 27, 75);
}

input.primary::placeholder {
  color: rgb(199, 210, 254);
}

/* Custom styling for secondary state */
input.secondary {
  border-color: rgb(107, 114, 128);
  color: rgb(17, 24, 39);
}

input.secondary::placeholder {
  color: rgb(209, 213, 219);
}

/* Custom styling for small size */
input.sm {
  font-size: 0.875rem;
}

/* Custom styling for medium size */
input.md {
  font-size: 1rem;
}

/* Custom styling for large size */
input.lg {
  font-size: 1.125rem;
}

/* Custom styling for outlined variant */
input.outlined {
  border: 1px solid;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Custom styling for filled variant */
input.filled {
  border: 1px solid transparent;
  border-radius: 0.375rem;
  background-color: rgb(243, 244, 246);
}

/* Custom styling for flushed variant */
input.flushed {
  border-bottom: 1px solid;
  border-radius: 0;
  background-color: transparent;
}

/* Custom styling for unstyled variant */
input.unstyled {
  border: none;
  background-color: transparent;
}
</style>
