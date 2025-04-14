<!-- components/dashboard/settings/AccessibilitySettingsForm.vue -->
<script setup lang="ts">
import { ref, reactive } from "vue";
import SettingsHeader from "./SettingsHeader.vue";

const emit = defineEmits<{
  (e: "update-message", type: string, message: string): void;
}>();

// Accessibility settings state
const accessibilitySettings = reactive({
  fontSize: "medium", // small, medium, large
  contrastMode: false,
  reducedMotion: false,
  screenReaderOptimization: false,
  autoplayVideos: true,
  subtitlesDefault: false,
  keyboardNavigation: false,
});

// Form state
const isSubmitting = ref(false);

// Convert slider value to fontSize setting
const getFontSizeValue = () => {
  if (accessibilitySettings.fontSize === "small") return 0;
  if (accessibilitySettings.fontSize === "medium") return 1;
  return 2; // large
};

const fontSizeValue = ref(getFontSizeValue());

// Update fontSize when slider changes
const updateFontSize = (event: Event) => {
  const value = parseInt((event.target as HTMLInputElement).value);
  if (value === 0) accessibilitySettings.fontSize = "small";
  else if (value === 1) accessibilitySettings.fontSize = "medium";
  else accessibilitySettings.fontSize = "large";
};

// Save accessibility settings
const saveSettings = async () => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    emit("update-message", "success", "Accessibility settings updated!");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem updating your accessibility settings."
    );
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div>
    <!-- Section header -->
    <SettingsHeader
      title="Accessibility Settings"
      description="Customize your learning experience for better accessibility"
    />

    <!-- Form content -->
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <!-- Display Settings -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Display Settings
          </h3>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"
                  >Font Size</label
                >
                <div class="flex items-center space-x-4">
                  <div class="flex-grow">
                    <input
                      type="range"
                      min="0"
                      max="2"
                      step="1"
                      v-model="fontSizeValue"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                      @input="updateFontSize($event)"
                    />
                  </div>
                  <span
                    class="text-sm font-medium text-gray-700 w-16 text-right"
                  >
                    {{
                      accessibilitySettings.fontSize.charAt(0).toUpperCase() +
                      accessibilitySettings.fontSize.slice(1)
                    }}
                  </span>
                </div>
                <div class="mt-2 flex justify-between text-xs text-gray-500">
                  <span>Small</span>
                  <span>Medium</span>
                  <span>Large</span>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      High Contrast Mode
                    </h4>
                    <p class="text-sm text-gray-500">
                      Improve text visibility and reduce eye strain
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accessibilitySettings.contrastMode"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Reduced Motion
                    </h4>
                    <p class="text-sm text-gray-500">
                      Minimize animations throughout the platform
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accessibilitySettings.reducedMotion"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Accessibility -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Content Accessibility
          </h3>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="space-y-6">
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="text-sm font-medium text-gray-900">
                    Screen Reader Optimization
                  </h4>
                  <p class="text-sm text-gray-500">
                    Enhance compatibility with screen readers
                  </p>
                </div>
                <div class="ml-3 flex items-center">
                  <label class="switch">
                    <input
                      type="checkbox"
                      v-model="accessibilitySettings.screenReaderOptimization"
                    />
                    <span class="slider round"></span>
                  </label>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Autoplay Videos
                    </h4>
                    <p class="text-sm text-gray-500">
                      Automatically play videos when available
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accessibilitySettings.autoplayVideos"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Always Show Subtitles
                    </h4>
                    <p class="text-sm text-gray-500">
                      Enable subtitles by default in all videos
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accessibilitySettings.subtitlesDefault"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">
                      Enhanced Keyboard Navigation
                    </h4>
                    <p class="text-sm text-gray-500">
                      Improve navigation using keyboard shortcuts
                    </p>
                  </div>
                  <div class="ml-3 flex items-center">
                    <label class="switch">
                      <input
                        type="checkbox"
                        v-model="accessibilitySettings.keyboardNavigation"
                      />
                      <span class="slider round"></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action buttons -->
        <div class="flex justify-end">
          <button
            type="button"
            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="saveSettings"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSubmitting"
          >
            <svg
              v-if="isSubmitting"
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{ isSubmitting ? "Saving..." : "Save Settings" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Toggle switch styles */
.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: 0.3s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
}

input:checked + .slider {
  background-color: #4f46e5;
}

input:focus + .slider {
  box-shadow: 0 0 1px #4f46e5;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

/* Custom range input styling */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  height: 8px;
  border-radius: 5px;
  background: #e5e7eb;
  outline: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #4f46e5;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #4f46e5;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
}

input[type="range"]:focus {
  outline: none;
}

input[type="range"]:focus::-webkit-slider-thumb {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
}

input[type="range"]:focus::-moz-range-thumb {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
}
</style>
