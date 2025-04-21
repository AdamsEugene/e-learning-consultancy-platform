<!-- components/common/Modal.vue -->
<script setup lang="ts">
import {
  ref,
  watch,
  onMounted,
  onBeforeUnmount,
  nextTick,
  computed,
} from "vue";

/**
 * Props for the Modal component
 */
interface Props {
  /** Controls if the modal is visible */
  modelValue?: boolean;
  /** Title for the modal (can be overridden with header slot) */
  title?: string;
  /** Width of the modal (px, rem, %, etc.) */
  width?: string;
  /** Maximum width of the modal (px, rem, %, etc.) */
  maxWidth?: string;
  /** Height of the modal (px, rem, %, etc.) */
  height?: string;
  /** Maximum height of the modal (px, rem, %, etc.) */
  maxHeight?: string;
  /** Whether to close modal on Escape key press */
  closeOnEsc?: boolean;
  /** Whether to close modal when clicking outside */
  closeOnClickOutside?: boolean;
  /** Whether to show close button in header */
  showCloseButton?: boolean;
  /** Position of the modal */
  position?:
    | "center"
    | "top"
    | "right"
    | "bottom"
    | "left"
    | "top-right"
    | "top-left"
    | "bottom-right"
    | "bottom-left";
  /** Additional CSS classes for the modal container */
  modalClass?: string;
  /** Additional CSS classes for the modal content */
  contentClass?: string;
  /** Transition duration in ms */
  transitionDuration?: number;
  /** Whether to scroll content or body */
  scrollable?: boolean;
  /** Whether the modal has a footer */
  hasFooter?: boolean;
  /** Whether to apply backdrop blur effect */
  backdropBlur?: boolean;
  /** z-index value for the modal */
  zIndex?: number;
  /** Whether to disable animation on initial mount */
  disableMountAnimation?: boolean;
  /** Whether the modal is fullscreen */
  fullscreen?: boolean;
}

/**
 * Default props
 */
const props = withDefaults(defineProps<Props>(), {
  modelValue: false,
  title: "",
  width: "500px",
  maxWidth: "90vw",
  height: "auto",
  maxHeight: "90vh",
  closeOnEsc: true,
  closeOnClickOutside: true,
  showCloseButton: true,
  position: "center",
  modalClass: "",
  contentClass: "",
  transitionDuration: 300,
  scrollable: true,
  hasFooter: false,
  backdropBlur: true,
  zIndex: 1000,
  disableMountAnimation: false,
  fullscreen: false,
});

/**
 * Emitted events
 */
const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "open" | "close" | "beforeOpen" | "beforeClose"): void;
}>();

// State variables
const modalRef = ref<HTMLElement | null>(null);
const modalContentRef = ref<HTMLElement | null>(null);
const isVisible = ref(false);
const isAnimating = ref(false);
const initialTouchY = ref(0);
const currentTouchY = ref(0);
const isTouching = ref(false);
const hasRendered = ref(false);

// Styles for positioning the modal
const modalStyles = computed(() => {
  const styles: Record<string, string> = {
    width: props.fullscreen ? "100vw" : props.width,
    maxWidth: props.fullscreen ? "100vw" : props.maxWidth,
    height: props.fullscreen ? "100vh" : props.height,
    maxHeight: props.fullscreen ? "100vh" : props.maxHeight,
    zIndex: `${props.zIndex}`,
    transition: `opacity ${props.transitionDuration}ms, transform ${props.transitionDuration}ms`,
  };

  // Positioning styles
  if (!props.fullscreen) {
    switch (props.position) {
      case "top":
        styles.marginTop = "1.5rem";
        styles.marginBottom = "auto";
        styles.marginLeft = "auto";
        styles.marginRight = "auto";
        break;
      case "right":
        styles.marginTop = "auto";
        styles.marginBottom = "auto";
        styles.marginLeft = "auto";
        styles.marginRight = "1.5rem";
        break;
      case "bottom":
        styles.marginTop = "auto";
        styles.marginBottom = "1.5rem";
        styles.marginLeft = "auto";
        styles.marginRight = "auto";
        break;
      case "left":
        styles.marginTop = "auto";
        styles.marginBottom = "auto";
        styles.marginLeft = "1.5rem";
        styles.marginRight = "auto";
        break;
      case "top-right":
        styles.marginTop = "1.5rem";
        styles.marginBottom = "auto";
        styles.marginLeft = "auto";
        styles.marginRight = "1.5rem";
        break;
      case "top-left":
        styles.marginTop = "1.5rem";
        styles.marginBottom = "auto";
        styles.marginLeft = "1.5rem";
        styles.marginRight = "auto";
        break;
      case "bottom-right":
        styles.marginTop = "auto";
        styles.marginBottom = "1.5rem";
        styles.marginLeft = "auto";
        styles.marginRight = "1.5rem";
        break;
      case "bottom-left":
        styles.marginTop = "auto";
        styles.marginBottom = "1.5rem";
        styles.marginLeft = "1.5rem";
        styles.marginRight = "auto";
        break;
      default: // center
        styles.marginTop = "auto";
        styles.marginBottom = "auto";
        styles.marginLeft = "auto";
        styles.marginRight = "auto";
        break;
    }
  }

  return styles;
});

/**
 * Animation classes based on position
 */
const getAnimationClasses = () => {
  if (props.fullscreen) return "";

  switch (props.position) {
    case "top":
      return "animate-slide-in-down";
    case "right":
      return "animate-slide-in-right";
    case "bottom":
      return "animate-slide-in-up";
    case "left":
      return "animate-slide-in-left";
    case "top-right":
      return "animate-slide-in-top-right";
    case "top-left":
      return "animate-slide-in-top-left";
    case "bottom-right":
      return "animate-slide-in-bottom-right";
    case "bottom-left":
      return "animate-slide-in-bottom-left";
    default:
      return "animate-scale-up";
  }
};

/**
 * Get initial transform based on position for animation
 */
const getInitialTransform = () => {
  if (props.fullscreen) return "none";

  switch (props.position) {
    case "top":
      return "translateY(-20px)";
    case "right":
      return "translateX(20px)";
    case "bottom":
      return "translateY(20px)";
    case "left":
      return "translateX(-20px)";
    case "top-right":
      return "translate(20px, -20px)";
    case "top-left":
      return "translate(-20px, -20px)";
    case "bottom-right":
      return "translate(20px, 20px)";
    case "bottom-left":
      return "translate(-20px, 20px)";
    default:
      return "scale(0.95)";
  }
};

// Transition classes
const modalTransitionClasses = computed(() => {
  return {
    "opacity-0": !isVisible.value || isAnimating.value,
    "opacity-100": isVisible.value && !isAnimating.value,
    "pointer-events-none": !isVisible.value,
  };
});

// Content transition styles
const contentTransitionStyles = computed(() => {
  if (!hasRendered.value && props.disableMountAnimation) {
    return {};
  }

  return {
    transform: isVisible.value ? "none" : getInitialTransform(),
    opacity: isVisible.value ? "1" : "0",
    transition: `transform ${props.transitionDuration}ms, opacity ${props.transitionDuration}ms`,
  };
});

// Backdrop styles
const backdropStyles = computed(() => {
  return {
    backdropFilter: props.backdropBlur ? "blur(5px)" : "none",
    transition: `opacity ${props.transitionDuration}ms`,
  };
});

/**
 * Opens the modal
 */
const open = async () => {
  if (isVisible.value || isAnimating.value) return;

  emit("beforeOpen");
  isAnimating.value = true;

  document.body.style.overflow = "hidden";
  isVisible.value = true;

  await nextTick();
  // Focus first focusable element
  focusFirstElement();

  setTimeout(() => {
    isAnimating.value = false;
    hasRendered.value = true;
    emit("open");
  }, props.transitionDuration);
};

/**
 * Closes the modal
 */
const close = async () => {
  if (!isVisible.value || isAnimating.value) return;

  emit("beforeClose");
  isAnimating.value = true;
  isVisible.value = false;

  setTimeout(() => {
    document.body.style.overflow = "";
    isAnimating.value = false;
    emit("close");
  }, props.transitionDuration);
};

/**
 * Focus the first focusable element in the modal
 */
const focusFirstElement = () => {
  if (!modalContentRef.value) return;

  const focusableElements = modalContentRef.value.querySelectorAll(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
  );

  if (focusableElements.length > 0) {
    (focusableElements[0] as HTMLElement).focus();
  }
};

/**
 * Handle click outside the modal content
 */
const handleClickOutside = (e: MouseEvent) => {
  if (!props.closeOnClickOutside) return;

  const target = e.target as HTMLElement;

  if (modalRef.value === target) {
    close();
  }
};

/**
 * Handle keyboard events
 */
const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === "Escape" && props.closeOnEsc) {
    close();
  }

  // Trap focus within modal
  if (e.key === "Tab" && modalContentRef.value) {
    const focusableElements = Array.from(
      modalContentRef.value.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      )
    ) as HTMLElement[];

    if (focusableElements.length === 0) return;

    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (e.shiftKey && document.activeElement === firstElement) {
      lastElement.focus();
      e.preventDefault();
    } else if (!e.shiftKey && document.activeElement === lastElement) {
      firstElement.focus();
      e.preventDefault();
    }
  }
};

/**
 * Touch event handlers for mobile swipe support
 */
const handleTouchStart = (e: TouchEvent) => {
  initialTouchY.value = e.touches[0].clientY;
  currentTouchY.value = initialTouchY.value;
  isTouching.value = true;
};

const handleTouchMove = (e: TouchEvent) => {
  if (!isTouching.value) return;

  currentTouchY.value = e.touches[0].clientY;

  // Only allow swiping down when the modal is at the top (position === 'top' or position === 'center')
  // or swiping up when the modal is at the bottom
  if (
    props.position === "bottom" ||
    props.position === "bottom-left" ||
    props.position === "bottom-right"
  ) {
    if (currentTouchY.value < initialTouchY.value) {
      const diff = initialTouchY.value - currentTouchY.value;
      if (diff > 100) {
        close();
        isTouching.value = false;
      }
    }
  } else if (
    props.position === "top" ||
    props.position === "top-left" ||
    props.position === "top-right" ||
    props.position === "center"
  ) {
    if (currentTouchY.value > initialTouchY.value) {
      const diff = currentTouchY.value - initialTouchY.value;
      if (diff > 100) {
        close();
        isTouching.value = false;
      }
    }
  }
};

const handleTouchEnd = () => {
  isTouching.value = false;
};

/**
 * Watch for changes in modelValue prop
 */
watch(
  () => props.modelValue,
  (value) => {
    if (value) {
      open();
    } else {
      close();
    }
  }
);

/**
 * Detect dark mode
 */

// Lifecycle hooks
onMounted(() => {
  window.addEventListener("keydown", handleKeyDown);

  // Listen for dark mode changes

  // Open the modal if modelValue is true on mount
  if (props.modelValue) {
    if (props.disableMountAnimation) {
      isVisible.value = true;
      document.body.style.overflow = "hidden";
      hasRendered.value = true;
      emit("open");
    } else {
      open();
    }
  }
});

onBeforeUnmount(() => {
  window.removeEventListener("keydown", handleKeyDown);

  // Reset body overflow
  if (isVisible.value) {
    document.body.style.overflow = "";
  }
});
</script>

<template>
  <teleport to="body">
    <transition :css="false">
      <div
        v-if="modelValue"
        ref="modalRef"
        :class="[
          'fixed inset-0 flex z-[1000] overflow-auto',
          modalClass,
          modalTransitionClasses,
          {
            'items-center justify-center': position === 'center',
            'items-start justify-center': position === 'top',
            'items-center justify-end': position === 'right',
            'items-end justify-center': position === 'bottom',
            'items-center justify-start': position === 'left',
            'items-start justify-end': position === 'top-right',
            'items-start justify-start': position === 'top-left',
            'items-end justify-end': position === 'bottom-right',
            'items-end justify-start': position === 'bottom-left',
          },
        ]"
        :style="{ zIndex }"
        @click="handleClickOutside"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="handleTouchEnd"
      >
        <!-- Backdrop -->
        <div
          class="absolute inset-0 transition-opacity duration-300 bg-black/50"
          :class="{
            'opacity-0': !isVisible,
            'opacity-100': isVisible,
          }"
          :style="backdropStyles"
        />

        <!-- Modal Container -->
        <div
          ref="modalContentRef"
          class="relative m-4 flex flex-col rounded-lg shadow-xl transition-all duration-300 bg-white text-gray-900"
          :class="[
            contentClass,
            {
              'overflow-hidden': !scrollable,
              'w-full h-full': fullscreen,
            },
          ]"
          :style="[modalStyles, contentTransitionStyles]"
          @click.stop
        >
          <!-- Header Slot -->
          <div
            class="flex items-center justify-between p-4 border-b border-gray-200"
          >
            <slot name="header">
              <h3 class="text-lg font-semibold text-gray-900">
                {{ title }}
              </h3>
            </slot>

            <div class="flex items-center">
              <slot name="header-actions" />

              <button
                v-if="showCloseButton"
                type="button"
                class="ml-2 p-1 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-gray-200 focus:ring-indigo-500 text-gray-500"
                aria-label="Close"
                @click="close"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>

          <!-- Content Slot -->
          <div class="flex-1 p-4" :class="{ 'overflow-y-auto': scrollable }">
            <slot />
          </div>

          <!-- Footer Slot -->
          <div
            v-if="hasFooter || $slots.footer"
            class="p-4 border-t border-gray-200"
          >
            <slot name="footer" />
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<style scoped>
/* Animation classes */
.animate-scale-up {
  animation: scale-up 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-down {
  animation: slide-in-down 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-up {
  animation: slide-in-up 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-left {
  animation: slide-in-left 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-right {
  animation: slide-in-right 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-top-right {
  animation: slide-in-top-right 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-top-left {
  animation: slide-in-top-left 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-bottom-right {
  animation: slide-in-bottom-right 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.animate-slide-in-bottom-left {
  animation: slide-in-bottom-left 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Keyframes for animations */
@keyframes scale-up {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes slide-in-down {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slide-in-up {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slide-in-left {
  from {
    transform: translateX(-20px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slide-in-right {
  from {
    transform: translateX(20px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slide-in-top-right {
  from {
    transform: translate(20px, -20px);
    opacity: 0;
  }
  to {
    transform: translate(0, 0);
    opacity: 1;
  }
}

@keyframes slide-in-top-left {
  from {
    transform: translate(-20px, -20px);
    opacity: 0;
  }
  to {
    transform: translate(0, 0);
    opacity: 1;
  }
}

@keyframes slide-in-bottom-right {
  from {
    transform: translate(20px, 20px);
    opacity: 0;
  }
  to {
    transform: translate(0, 0);
    opacity: 1;
  }
}

@keyframes slide-in-bottom-left {
  from {
    transform: translate(-20px, 20px);
    opacity: 0;
  }
  to {
    transform: translate(0, 0);
    opacity: 1;
  }
}

/* Fix for iOS scrolling */
@supports (-webkit-touch-callout: none) {
  .overflow-y-auto {
    -webkit-overflow-scrolling: touch;
  }
}
</style>
