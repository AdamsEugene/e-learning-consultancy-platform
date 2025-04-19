<script setup lang="ts">
import type { DropdownMenuItem } from "#ui/types";

definePageMeta({
  layout: "components",
});

// Basic dropdown items
const basicItems: DropdownMenuItem[] = [
  { label: "Edit", icon: "i-heroicons-pencil" },
  { label: "Delete", icon: "i-heroicons-trash", color: "error" },
  { type: "separator" },
  { label: "View Details", href: "/details" },
];

// Variants
const variants = ["solid", "outline", "ghost", "link"] as const;
const variantItems = variants.map((variant) => ({
  label: variant.charAt(0).toUpperCase() + variant.slice(1),
  icon: "i-heroicons-check",
}));

// Colors
const colors = [
  "primary",
  "secondary",
  "success",
  "error",
  "warning",
  "info",
  "neutral",
] as const;
const colorItems: DropdownMenuItem[] = colors.map((color) => ({
  label: color.charAt(0).toUpperCase() + color.slice(1),
  icon: "i-heroicons-check",
  color,
}));

// Sizes
const sizes = ["sm", "md", "lg"] as const;
const sizeItems: DropdownMenuItem[] = sizes.map((size) => ({
  label: size.toUpperCase(),
  icon: "i-heroicons-check",
}));

// Disabled items
const disabledItems: DropdownMenuItem[] = [
  { label: "Enabled Item", icon: "i-heroicons-check" },
  { label: "Disabled Item", icon: "i-heroicons-x-mark", disabled: true },
];

// Placements
const placements = ["top", "right", "bottom", "left"] as const;
const placementItems: DropdownMenuItem[] = placements.map((placement) => ({
  label: placement.charAt(0).toUpperCase() + placement.slice(1),
  icon: "i-heroicons-check",
}));
</script>

<template>
  <div class="space-y-8">
    <div class="space-y-4">
      <h2 class="text-2xl font-bold">Dropdown</h2>
      <p class="text-gray-600 dark:text-gray-400">
        A dropdown menu that appears when clicking a trigger button.
      </p>
    </div>

    <!-- Basic Dropdown -->
    <div class="space-y-4">
      <h3 class="text-lg font-medium">Basic</h3>
      <div class="flex items-center gap-4">
        <UDropdownMenu :items="basicItems">
          <UButton>Actions</UButton>
        </UDropdownMenu>
      </div>
    </div>

    <!-- Variants -->
    <div class="space-y-4">
      <h3 class="text-lg font-medium">Variants</h3>
      <div class="flex items-center gap-4">
        <UDropdownMenu
          v-for="variant in variants"
          :key="variant"
          :items="variantItems"
        >
          <UButton :variant="variant">{{ variant }}</UButton>
        </UDropdownMenu>
      </div>
    </div>

    <!-- Colors -->
    <div class="space-y-4">
      <h3 class="text-lg font-medium">Colors</h3>
      <div class="flex items-center gap-4">
        <UDropdownMenu v-for="color in colors" :key="color" :items="colorItems">
          <UButton :color="color">{{ color }}</UButton>
        </UDropdownMenu>
      </div>
    </div>

    <!-- Sizes -->
    <div class="space-y-4">
      <h3 class="text-lg font-medium">Sizes</h3>
      <div class="flex items-center gap-4">
        <UDropdownMenu v-for="size in sizes" :key="size" :items="sizeItems">
          <UButton :size="size">{{ size }}</UButton>
        </UDropdownMenu>
      </div>
    </div>

    <!-- Disabled Items -->
    <div class="space-y-4">
      <h3 class="text-lg font-medium">Disabled Items</h3>
      <div class="flex items-center gap-4">
        <UDropdownMenu :items="disabledItems">
          <UButton>Disabled Items</UButton>
        </UDropdownMenu>
      </div>
    </div>

    <!-- Placements -->
    <div class="space-y-4">
      <h3 class="text-lg font-medium">Placements</h3>
      <div class="flex items-center gap-4">
        <UDropdownMenu
          v-for="placement in placements"
          :key="placement"
          :items="placementItems"
          :content="{ side: placement }"
        >
          <UButton>{{ placement }}</UButton>
        </UDropdownMenu>
      </div>
    </div>
  </div>
</template>
