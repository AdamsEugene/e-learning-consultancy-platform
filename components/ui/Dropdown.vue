<script setup lang="ts">
import {
  ref,
  computed,
  watch,
  nextTick,
  onMounted,
  onBeforeUnmount,
  Teleport,
} from "vue";
import type { Placement } from "@floating-ui/dom";
import type { CSSProperties } from "vue";
import UiButton from "./Button.vue";
import clickOutside from "@/directives/click-outside";

defineOptions({
  name: "UiDropdown",
});

interface DropdownItem {
  label: string;
  icon?: string;
  href?: string;
  onClick?: () => void;
  disabled?: boolean;
  divider?: boolean;
  danger?: boolean;
  class?: string;
  style?: Record<string, string>;
  data?: Record<string, unknown>;
}

interface Props {
  // Core props
  modelValue?: boolean;
  placement?: Placement;
  offset?: number;
  width?: string | number;
  items?: DropdownItem[];

  // Trigger button props
  label?: string;
  icon?: string;
  variant?: "solid" | "outline" | "ghost" | "link";
  color?: "primary" | "secondary" | "success" | "danger" | "warning" | "info";
  size?: "xs" | "sm" | "md" | "lg" | "xl";

  // Additional options
  closeOnClick?: boolean;
  closeOnScroll?: boolean;
  appendTo?: string | HTMLElement;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: false,
  placement: "bottom-start",
  offset: 8,
  width: "trigger",
  label: "",
  variant: "solid",
  color: "primary",
  size: "md",
  closeOnClick: true,
  closeOnScroll: true,
  appendTo: "body",
  icon: undefined,
  items: () => [],
});

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
}>();

// Refs
const triggerRef = ref<HTMLElement | null>(null);
const dropdownRef = ref<HTMLElement | null>(null);
const isOpen = ref(props.modelValue);
const dropdownWidth = ref(0);
const position = ref({ x: 0, y: 0 });
const arrowPosition = ref({ x: 0, y: 0 });
const currentPlacement = ref(props.placement);

// Computed
const dropdownStyle = computed<CSSProperties>(() => {
  return {
    position: "fixed",
    left: `${position.value.x}px`,
    top: `${position.value.y}px`,
    width: props.width === "trigger" ? `${dropdownWidth.value}px` : props.width,
    zIndex: 50,
  };
});

const arrowStyle = computed<CSSProperties>(() => {
  const isTop = currentPlacement.value.includes("top");
  const isBottom = currentPlacement.value.includes("bottom");
  const isStart = currentPlacement.value.includes("start");
  const isEnd = currentPlacement.value.includes("end");

  return {
    position: "absolute",
    width: "8px",
    height: "8px",
    background: "inherit",
    visibility: isOpen.value ? "visible" : "hidden",
    transform: "rotate(45deg)",
    top: isTop ? "100%" : isBottom ? "-4px" : "50%",
    left: isStart ? "12px" : isEnd ? "auto" : "50%",
    right: isEnd ? "12px" : "auto",
    marginTop: isTop ? "-4px" : "",
    marginLeft: !isStart && !isEnd ? "-4px" : "",
  };
});

// Methods
const updatePosition = async () => {
  if (!triggerRef.value || !dropdownRef.value) return;

  const triggerRect = triggerRef.value.getBoundingClientRect();
  const dropdownRect = dropdownRef.value.getBoundingClientRect();
  const viewportWidth = window.innerWidth;
  const viewportHeight = window.innerHeight;

  // Store trigger width for the dropdown
  dropdownWidth.value = triggerRect.width;

  // Calculate initial position based on placement
  let x = triggerRect.left;
  let y = triggerRect.bottom + props.offset;
  let placement = props.placement;

  // Check if dropdown would overflow right side
  if (x + dropdownRect.width > viewportWidth) {
    x = triggerRect.right - dropdownRect.width;
    placement = placement.replace("start", "end") as Placement;
  }

  // Check if dropdown would overflow left side
  if (x < 0) {
    x = triggerRect.left;
    placement = placement.replace("end", "start") as Placement;
  }

  // Check if dropdown would overflow bottom
  if (y + dropdownRect.height > viewportHeight) {
    y = triggerRect.top - dropdownRect.height - props.offset;
    placement = placement.replace("bottom", "top") as Placement;
  }

  // Check if dropdown would overflow top
  if (y < 0) {
    y = triggerRect.bottom + props.offset;
    placement = placement.replace("top", "bottom") as Placement;
  }

  // Update positions
  position.value = { x, y };
  currentPlacement.value = placement;

  // Update arrow position
  const isTop = placement.includes("top");
  const isStart = placement.includes("start");
  const isEnd = placement.includes("end");

  arrowPosition.value = {
    x: isStart ? 12 : isEnd ? dropdownRect.width - 20 : dropdownRect.width / 2,
    y: isTop ? dropdownRect.height : 0,
  };
};

const toggle = () => {
  isOpen.value = !isOpen.value;
  emit("update:modelValue", isOpen.value);
  if (isOpen.value) {
    nextTick(() => {
      updatePosition();
    });
  }
};

const close = () => {
  isOpen.value = false;
  emit("update:modelValue", false);
};

const handleScroll = () => {
  if (props.closeOnScroll && isOpen.value) {
    close();
  } else if (isOpen.value) {
    updatePosition();
  }
};

const handleResize = () => {
  if (isOpen.value) {
    updatePosition();
  }
};

const handleItemClick = (item: DropdownItem) => {
  if (item.disabled) return;

  if (item.onClick) {
    item.onClick();
  }

  if (props.closeOnClick) {
    close();
  }
};

// Watchers
watch(
  () => props.modelValue,
  (value) => {
    isOpen.value = value;
    if (value) {
      nextTick(() => {
        updatePosition();
      });
    }
  }
);

// Lifecycle
onMounted(() => {
  window.addEventListener("scroll", handleScroll, true);
  window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", handleScroll, true);
  window.removeEventListener("resize", handleResize);
});
</script>

<template>
  <div class="relative inline-block">
    <!-- Trigger button -->
    <div ref="triggerRef">
      <UiButton
        v-if="!$slots.trigger"
        :variant="variant"
        :color="color"
        :size="size"
        :left-icon="icon"
        @click="toggle"
      >
        {{ label }}
      </UiButton>
      <div v-else @click="toggle">
        <slot name="trigger" :open="isOpen" />
      </div>
    </div>

    <!-- Dropdown menu -->
    <Teleport :to="appendTo">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div
          v-if="isOpen"
          ref="dropdownRef"
          v-click-outside="close"
          :style="dropdownStyle"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
          <!-- Arrow -->
          <div
            :style="arrowStyle"
            class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700"
          />

          <!-- Default slot for custom content -->
          <slot />

          <!-- Items list -->
          <template v-if="items.length > 0">
            <div class="py-1">
              <template v-for="(item, index) in items" :key="index">
                <!-- Divider -->
                <div
                  v-if="item.divider"
                  class="my-1 border-t border-gray-200 dark:border-gray-700"
                />

                <!-- Item -->
                <template v-else>
                  <a
                    v-if="item.href"
                    :href="item.href"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="{
                      'opacity-50 cursor-not-allowed': item.disabled,
                      'text-red-600 dark:text-red-400': item.danger,
                    }"
                    @click="handleItemClick(item)"
                  >
                    <span v-if="item.icon" class="mr-2">
                      <slot :name="`icon-${item.icon}`">
                        <i :class="item.icon" />
                      </slot>
                    </span>
                    {{ item.label }}
                  </a>
                  <button
                    v-else
                    type="button"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="{
                      'opacity-50 cursor-not-allowed': item.disabled,
                      'text-red-600 dark:text-red-400': item.danger,
                    }"
                    :disabled="item.disabled"
                    @click="handleItemClick(item)"
                  >
                    <span v-if="item.icon" class="mr-2">
                      <slot :name="`icon-${item.icon}`">
                        <i :class="item.icon" />
                      </slot>
                    </span>
                    {{ item.label }}
                  </button>
                </template>
              </template>
            </div>
          </template>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Dark mode support */
:deep(.dark) {
  --tw-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3),
    0 4px 6px -2px rgba(0, 0, 0, 0.2);
  --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color),
    0 4px 6px -2px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),
    var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}
</style>
