<!-- components/courses/NotesPanel.vue -->
<script setup lang="ts">
import type { Note } from "~/types/courseTemp";

// =============================================
// INTERFACES
// =============================================
interface Props {
  isOpen?: boolean;
  notes?: Note[];
  noteContent?: string;
  notesSaved?: boolean;
  courseId: number | string;
  lessonId: number | string;
  width?: string; // Configurable panel width
  position?: "left" | "right"; // Panel position
  theme?: "light" | "dark" | "indigo"; // Theme options
}

// =============================================
// PROPS
// =============================================
const props = withDefaults(defineProps<Props>(), {
  isOpen: false,
  notes: () => [],
  noteContent: "",
  notesSaved: false,
  width: "384px", // Default width (96 * 4 = 384px)
  position: "right", // Default position
  theme: "indigo", // Default theme
});

// =============================================
// EMITS
// =============================================
const emit = defineEmits<{
  (e: "close" | "save"): void;
  (e: "delete" | "update:noteContent" | "resize", payload: string): void;
  (e: "update:isOpen", payload: boolean): void;
}>();

// =============================================
// STATES
// =============================================
// Local state for note content
const localNoteContent = ref(props.noteContent);

// UI state variables
const isEditorFocused = ref(false);
const noteSavedAnimation = ref(false);
const deleteConfirmation = ref<string | null>(null);
const searchQuery = ref("");
const isSorting = ref(false);
const sortBy = ref<"newest" | "oldest">("newest");
const isSearchFocused = ref(false);
const theme = ref(props.theme);
const drawerWidth = ref("400px");

// =============================================
// COMPUTED PROPERTIES
// =============================================
// Computed property for v-model binding
const isOpenComputed = computed({
  get: () => props.isOpen,
  set: (value) => {
    console.log("Setting isOpen to:", value);
    emit("update:isOpen", value);
  },
});

// Filtered and sorted notes
const filteredNotes = computed(() => {
  let filtered = [...props.notes];

  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter((note) =>
      note.text.toLowerCase().includes(query)
    );
  }

  // Sort by timestamp
  filtered.sort((a, b) => {
    if (sortBy.value === "newest") {
      return b.timestamp - a.timestamp;
    } else {
      return a.timestamp - b.timestamp;
    }
  });

  return filtered;
});

// Check if we can save the note (non-empty content)
const canSaveNote = computed(() => {
  return localNoteContent.value.trim().length > 0;
});

// Word count
const wordCount = computed(() => {
  return localNoteContent.value.trim()
    ? localNoteContent.value.trim().split(/\s+/).length
    : 0;
});

// Theme classes
const themeClasses = computed(() => {
  switch (theme.value) {
    case "dark":
      return {
        header: "bg-gray-900 text-white",
        button: "bg-gray-800 hover:bg-gray-700 text-white",
        panel: "bg-gray-800 text-white border-gray-700",
        input: "bg-gray-700 border-gray-600 text-white placeholder-gray-400",
        noteCard: "border-gray-700 bg-gray-800 hover:bg-gray-700",
        noteText: "text-gray-200",
        icon: "text-gray-400 hover:text-gray-200",
        divider: "border-gray-700",
      };
    case "indigo":
      return {
        header: "bg-indigo-600 text-white",
        button: "bg-indigo-600 hover:bg-indigo-700 text-white",
        panel: "bg-white text-gray-800 border-gray-200",
        input: "bg-white border-gray-300 text-gray-800 placeholder-gray-400",
        noteCard: "border-gray-200 hover:border-indigo-200 hover:bg-indigo-50",
        noteText: "text-gray-800",
        icon: "text-gray-400 hover:text-indigo-500",
        divider: "border-gray-200",
      };
    default: // light
      return {
        header: "bg-gray-100 text-gray-800",
        button: "bg-gray-200 hover:bg-gray-300 text-gray-800",
        panel: "bg-white text-gray-800 border-gray-200",
        input: "bg-white border-gray-300 text-gray-800 placeholder-gray-400",
        noteCard: "border-gray-200 hover:border-gray-300 hover:bg-gray-50",
        noteText: "text-gray-800",
        icon: "text-gray-400 hover:text-gray-700",
        divider: "border-gray-200",
      };
  }
});

// =============================================
// METHODS
// =============================================
// Handle note content update
const updateNoteContent = (event: Event) => {
  const target = event.target as HTMLTextAreaElement;
  localNoteContent.value = target.value;
  emit("update:noteContent", target.value);
};

// Save the current note
const saveNote = () => {
  if (canSaveNote.value) {
    emit("save");
  }
};

// Delete a note with confirmation
const confirmDelete = (noteId: string) => {
  deleteConfirmation.value = noteId;
};

const cancelDelete = () => {
  deleteConfirmation.value = null;
};

const deleteNote = (noteId: string) => {
  deleteConfirmation.value = null;
  emit("delete", noteId);
};

// Handle keyboard shortcuts
const handleKeyDown = (event: KeyboardEvent) => {
  // Save on Ctrl+Enter or Cmd+Enter
  if ((event.ctrlKey || event.metaKey) && event.key === "Enter") {
    event.preventDefault();
    saveNote();
  }
};

// Close the panel
const closePanel = () => {
  emit("close");
  emit("update:isOpen", false);
};

// Handle drawer resize
const handleResize = (width: string) => {
  drawerWidth.value = width;
  emit("resize", width);
};

// Theme toggling
const toggleTheme = () => {
  if (theme.value === "light") {
    theme.value = "dark";
  } else if (theme.value === "dark") {
    theme.value = "indigo";
  } else {
    theme.value = "light";
  }
};

// Toggle sort order
const toggleSortOrder = () => {
  isSorting.value = true;
  sortBy.value = sortBy.value === "newest" ? "oldest" : "newest";

  setTimeout(() => {
    isSorting.value = false;
  }, 300);
};

// Format timestamp to readable date
const formatTimestamp = (timestamp: number): string => {
  const date = new Date(timestamp);

  // Get today's date without time
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  // Get yesterday's date
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  // Check if date is today
  if (date.setHours(0, 0, 0, 0) === today.getTime()) {
    return `Today at ${date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })}`;
  }

  // Check if date is yesterday
  if (date.setHours(0, 0, 0, 0) === yesterday.getTime()) {
    return `Yesterday at ${date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })}`;
  }

  // Otherwise show full date
  return date.toLocaleDateString([], {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// =============================================
// EFFECTS
// =============================================
// Focus the editor on mount if panel is open
onMounted(() => {
  if (props.isOpen) {
    nextTick(() => {
      const textarea = document.getElementById("note-input");
      if (textarea) {
        textarea.focus();
      }
    });
  }
});

// =============================================
// WATCHERS
// =============================================
// Watch for changes in props
watch(
  () => props.noteContent,
  (newValue) => {
    localNoteContent.value = newValue;
  }
);

watch(
  () => props.notesSaved,
  (newValue) => {
    if (newValue) {
      noteSavedAnimation.value = true;
      setTimeout(() => {
        noteSavedAnimation.value = false;
      }, 3000);
    }
  }
);
</script>

<!-- eslint-disable vue/no-v-html -->
<template>
  <UiDrawer
    v-model="isOpenComputed"
    v-scroll-lock="isOpenComputed"
    :width="drawerWidth"
    resizable
    position="right"
    @resize="handleResize"
    @close="closePanel"
  >
    <!-- Header slot -->
    <template #header>
      <div class="flex items-center space-x-2">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
          />
          <path
            fill-rule="evenodd"
            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
            clip-rule="evenodd"
          />
        </svg>
        <h3 class="font-bold">Lesson Notes</h3>
      </div>
    </template>

    <!-- Header actions slot -->
    <template #header-actions>
      <!-- Theme toggle -->
      <UiButton
        variant="icon"
        :color="theme === 'dark' ? 'secondary' : 'primary'"
        size="sm"
        :icon-only="true"
        :left-icon="
          theme === 'light'
            ? 'ph:moon-fill'
            : theme === 'dark'
            ? 'ph:sun-fill'
            : 'ph:paint-brush-fill'
        "
        @click="toggleTheme"
      />
    </template>

    <!-- Default slot (body content) -->
    <div class="flex flex-col h-full">
      <!-- Search bar -->
      <div class="p-3 border-b" :class="themeClasses.divider">
        <UiInput
          v-model="searchQuery"
          type="search"
          placeholder="Search notes..."
          :color="theme === 'dark' ? 'secondary' : 'primary'"
          :variant="theme === 'dark' ? 'filled' : 'outlined'"
          right-icon="ic:baseline-search"
          :class="isSearchFocused ? 'ring-2 ring-indigo-300' : ''"
          @focus="isSearchFocused = true"
          @blur="isSearchFocused = false"
        />
      </div>

      <!-- Editor area -->
      <div class="p-4 border-b" :class="themeClasses.divider">
        <div class="mb-3 flex items-center justify-between">
          <div class="flex items-center">
            <h4 class="font-medium" :class="themeClasses.noteText">New Note</h4>
            <!-- Character counter -->
            <span
              class="ml-2 text-xs opacity-70"
              :class="themeClasses.noteText"
            >
              {{ localNoteContent.length }} chars · {{ wordCount }} words
            </span>
          </div>

          <!-- Saved notification -->
          <div
            class="flex items-center text-green-500 transition-all duration-500"
            :class="{
              'opacity-0': !noteSavedAnimation,
              'opacity-100': noteSavedAnimation,
            }"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            <span class="text-xs animate-pulse">Saved</span>
          </div>
        </div>

        <div class="relative">
          <UiTextarea
            id="note-input"
            v-model="localNoteContent"
            placeholder="Type your notes here..."
            :color="theme === 'dark' ? 'secondary' : 'primary'"
            :variant="theme === 'dark' ? 'filled' : 'outlined'"
            :rows="6"
            :resize="'vertical'"
            :class="[canSaveNote ? 'border-green-500' : '', 'focus:ring-0']"
            @input="updateNoteContent"
            @focus="isEditorFocused = true"
            @blur="isEditorFocused = false"
            @keydown="handleKeyDown"
          />

          <!-- Focus indicator line -->
          <div
            class="absolute top-0 left-0 w-1 h-[calc(100%-6px)] rounded-l-lg bg-indigo-500 transition-all duration-200"
            :class="{
              'opacity-100': isEditorFocused,
              'opacity-0': !isEditorFocused,
            }"
          />
        </div>

        <div class="mt-3 flex justify-between items-center">
          <div class="text-xs opacity-70" :class="themeClasses.noteText">
            Press Ctrl+Enter to save
          </div>
          <UiButton
            :disabled="!canSaveNote"
            :color="theme === 'dark' ? 'secondary' : 'primary'"
            :variant="theme === 'dark' ? 'ghost' : 'solid'"
            size="md"
            left-icon="ph:floppy-disk-fill"
            @click="saveNote"
          >
            Save Note
          </UiButton>
        </div>
      </div>

      <!-- Notes list with header -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <div
          class="px-4 py-2 flex justify-between items-center"
          :class="themeClasses.divider"
        >
          <h4 class="font-medium" :class="themeClasses.noteText">
            {{ filteredNotes.length }} Note{{
              filteredNotes.length !== 1 ? "s" : ""
            }}
          </h4>

          <div class="flex items-center">
            <UiButton
              variant="ghost"
              :color="theme === 'dark' ? 'secondary' : 'primary'"
              size="sm"
              :left-icon="
                sortBy === 'newest'
                  ? 'ph:sort-ascending-fill'
                  : 'ph:sort-descending-fill'
              "
              @click="toggleSortOrder"
            >
              Sort: {{ sortBy === "newest" ? "Newest first" : "Oldest first" }}
            </UiButton>
          </div>
        </div>

        <!-- Empty state -->
        <div
          v-if="filteredNotes.length === 0"
          class="flex-1 flex items-center justify-center p-4"
        >
          <div class="text-center py-8 max-w-md">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-16 w-16 mx-auto mb-4"
              :class="themeClasses.icon"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
              />
            </svg>
            <h5 class="text-lg font-bold mb-2" :class="themeClasses.noteText">
              {{ searchQuery ? "No matching notes found" : "No notes yet" }}
            </h5>
            <p class="opacity-70" :class="themeClasses.noteText">
              {{
                searchQuery
                  ? "Try adjusting your search term to find what you're looking for."
                  : "Your notes for this lesson will appear here. Add your first note to get started."
              }}
            </p>
          </div>
        </div>

        <!-- Notes list -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
          <transition-group name="note-transition">
            <div
              v-for="note in filteredNotes"
              :key="note.id"
              class="p-4 border rounded-lg transition-all duration-200 transform hover:-translate-y-1 hover:shadow-md note-card"
              :class="themeClasses.noteCard"
            >
              <div class="flex justify-between items-start">
                <div
                  class="text-xs opacity-70 mb-2"
                  :class="themeClasses.noteText"
                >
                  {{ formatTimestamp(note.timestamp) }}
                </div>

                <!-- Delete button / confirmation -->
                <div
                  v-if="deleteConfirmation === note.id"
                  class="flex items-center space-x-2"
                >
                  <UiButton
                    variant="solid"
                    color="danger"
                    size="xs"
                    @click="deleteNote(note.id)"
                  >
                    Confirm
                  </UiButton>
                  <UiButton
                    variant="ghost"
                    color="secondary"
                    size="xs"
                    @click="cancelDelete"
                  >
                    Cancel
                  </UiButton>
                </div>

                <UiButton
                  v-else
                  variant="icon"
                  color="danger"
                  size="xs"
                  :icon-only="true"
                  left-icon="ph:trash-fill"
                  aria-label="Delete note"
                  @click="confirmDelete(note.id)"
                />
              </div>

              <!-- Note text with highlight for search terms -->
              <p
                v-if="searchQuery.trim()"
                class="whitespace-pre-wrap break-words"
                :class="themeClasses.noteText"
                v-html="
                  note.text.replace(
                    new RegExp(`(${searchQuery.trim()})`, 'gi'),
                    '<mark class=\'bg-yellow-200 text-gray-900 rounded px-0.5\'>$1</mark>'
                  )
                "
              />

              <p
                v-else
                class="whitespace-pre-wrap break-words"
                :class="themeClasses.noteText"
              >
                {{ note.text }}
              </p>
            </div>
          </transition-group>
        </div>
      </div>
    </div>
  </UiDrawer>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Dark mode scrollbars */
.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(20px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slideOutLeft {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(-20px);
    opacity: 0;
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

/* Transition group animations for notes */
.note-transition-enter-active {
  animation: slideInRight 0.3s ease-out;
}

.note-transition-leave-active {
  animation: slideOutLeft 0.3s ease-in;
  position: absolute;
  width: calc(100% - 2rem);
}

.note-transition-move {
  transition: transform 0.5s ease-out;
}

/* Pulse animation for the saved notification */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

.animate-pulse {
  animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
