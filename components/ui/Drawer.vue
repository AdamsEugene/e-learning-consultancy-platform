<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, computed } from "vue";

interface Props {
  modelValue: boolean;
  title?: string;
  width?: string;
  position?: "left" | "right";
  closeOnClickOutside?: boolean;
  closeOnEscape?: boolean;
  preventScroll?: boolean;
  zIndex?: number;
  resizable?: boolean;
  minWidth?: string;
  maxWidth?: string;
}

const props = withDefaults(defineProps<Props>(), {
  title: "",
  width: "400px",
  position: "right",
  closeOnClickOutside: true,
  closeOnEscape: true,
  preventScroll: true,
  zIndex: 50,
  resizable: false,
  minWidth: "300px",
  maxWidth: "800px",
});

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "close" | "open"): void;
  (e: "resize", width: string): void;
}>();

const isOpen = ref(props.modelValue);
const drawerRef = ref<HTMLElement | null>(null);
const bodyScrollLocked = ref(false);
const isResizing = ref(false);
const startX = ref(0);
const startWidth = ref(0);
const currentWidth = ref(props.width);

// Computed property for the drawer width
const drawerWidth = computed(() => {
  return isResizing.value ? currentWidth.value : props.width;
});

// Watch for modelValue changes
watch(
  () => props.modelValue,
  (newValue) => {
    isOpen.value = newValue;
    if (newValue) {
      emit("open");
      if (props.preventScroll) {
        lockBodyScroll();
      }
    } else {
      emit("close");
      if (props.preventScroll) {
        unlockBodyScroll();
      }
    }
  }
);

// Watch for isOpen changes to emit update
watch(
  () => isOpen.value,
  (newValue) => {
    emit("update:modelValue", newValue);
  }
);

// Handle escape key
const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === "Escape" && props.closeOnEscape && isOpen.value) {
    closeDrawer();
  }
};

// Handle click outside
const handleClickOutside = (e: MouseEvent) => {
  if (
    props.closeOnClickOutside &&
    drawerRef.value &&
    !drawerRef.value.contains(e.target as Node) &&
    isOpen.value
  ) {
    closeDrawer();
  }
};

// Close drawer
const closeDrawer = () => {
  isOpen.value = false;
};

// Lock body scroll
const lockBodyScroll = () => {
  if (!bodyScrollLocked.value) {
    document.body.style.overflow = "hidden";
    bodyScrollLocked.value = true;
  }
};

// Unlock body scroll
const unlockBodyScroll = () => {
  if (bodyScrollLocked.value) {
    document.body.style.overflow = "";
    bodyScrollLocked.value = false;
  }
};

// Start resizing
const startResize = (e: MouseEvent) => {
  if (!props.resizable) return;

  isResizing.value = true;
  startX.value = e.clientX;

  // Get the current width of the drawer
  if (drawerRef.value) {
    const rect = drawerRef.value.getBoundingClientRect();
    startWidth.value = rect.width;
    currentWidth.value = `${startWidth.value}px`;
  }

  // Add event listeners for mouse move and mouse up
  document.addEventListener("mousemove", handleResize);
  document.addEventListener("mouseup", stopResize);

  // Prevent text selection during resize
  document.body.style.userSelect = "none";
};

// Handle resize
const handleResize = (e: MouseEvent) => {
  if (!isResizing.value) return;

  const deltaX =
    props.position === "right"
      ? startX.value - e.clientX
      : e.clientX - startX.value;

  let newWidth = startWidth.value + deltaX;

  // Apply min and max constraints
  const minWidth = parseInt(props.minWidth);
  const maxWidth = parseInt(props.maxWidth);

  if (newWidth < minWidth) newWidth = minWidth;
  if (newWidth > maxWidth) newWidth = maxWidth;

  currentWidth.value = `${newWidth}px`;
  emit("resize", currentWidth.value);
};

// Stop resizing
const stopResize = () => {
  isResizing.value = false;

  // Remove event listeners
  document.removeEventListener("mousemove", handleResize);
  document.removeEventListener("mouseup", stopResize);

  // Restore text selection
  document.body.style.userSelect = "";
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener("keydown", handleKeydown);
  document.addEventListener("mousedown", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("keydown", handleKeydown);
  document.removeEventListener("mousedown", handleClickOutside);
  document.removeEventListener("mousemove", handleResize);
  document.removeEventListener("mouseup", stopResize);
  if (bodyScrollLocked.value) {
    unlockBodyScroll();
  }
});
</script>

<template>
  <Teleport to="body">
    <Transition name="drawer-fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 backdrop-blur-sm bg-black/30 z-40"
        :style="{ zIndex: zIndex }"
        @click="closeOnClickOutside && closeDrawer()"
      />
    </Transition>

    <Transition
      :name="position === 'right' ? 'drawer-slide-right' : 'drawer-slide-left'"
    >
      <div
        v-if="isOpen"
        ref="drawerRef"
        class="fixed top-0 h-full bg-white/90 backdrop-blur-md shadow-xl z-50 overflow-hidden flex flex-col"
        :class="[
          position === 'right' ? 'right-0' : 'left-0',
          'w-full sm:w-auto',
        ]"
        :style="{
          width: drawerWidth,
          zIndex: zIndex + 1,
        }"
      >
        <!-- Resize handle -->
        <div
          v-if="resizable"
          class="absolute top-0 bottom-0 w-1 cursor-col-resize bg-gray-200/50 hover:bg-indigo-300 transition-colors"
          :class="[position === 'right' ? 'left-0' : 'right-0']"
          @mousedown="startResize"
        />

        <!-- Header -->
        <div
          class="flex items-center justify-between p-4 border-b border-gray-200/50"
        >
          <div class="flex-1">
            <slot name="header">
              <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
            </slot>
          </div>

          <div class="flex items-center space-x-2">
            <slot name="header-actions" />

            <button
              v-if="$slots['header-actions'] || title"
              class="text-gray-400 hover:text-gray-500 focus:outline-none"
              aria-label="Close drawer"
              @click="closeDrawer"
            >
              <svg
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4">
          <slot />
        </div>

        <!-- Footer -->
        <div v-if="$slots.footer" class="border-t border-gray-200/50 p-4">
          <slot name="footer">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <slot name="footer-actions">
                  <button
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    @click="closeDrawer"
                  >
                    Close
                  </button>
                </slot>
              </div>
            </div>
          </slot>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.drawer-fade-enter-active,
.drawer-fade-leave-active {
  transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.drawer-fade-enter-from,
.drawer-fade-leave-to {
  opacity: 0;
}

.drawer-slide-right-enter-active,
.drawer-slide-right-leave-active,
.drawer-slide-left-enter-active,
.drawer-slide-left-leave-active {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
    opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.drawer-slide-right-enter-from,
.drawer-slide-left-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.drawer-slide-right-leave-to,
.drawer-slide-left-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

.drawer-slide-left-enter-from {
  transform: translateX(-100%);
}

.drawer-slide-left-leave-to {
  transform: translateX(-100%);
}

/* Add glass effect to the drawer */
.drawer-slide-right-enter-active div,
.drawer-slide-left-enter-active div {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Add subtle shadow animation */
@keyframes shadowPulse {
  0% {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
      0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
  50% {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
      0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }
  100% {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
      0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
}

.drawer-slide-right-enter-active div,
.drawer-slide-left-enter-active div {
  animation: shadowPulse 2s infinite;
}
</style>
