<!-- components/courses/NotesPanel.vue -->
<script setup lang="ts">
import {
  ref,
  computed,
  watch,
  nextTick,
  onMounted,
  onBeforeUnmount,
} from "vue";
import type { Note } from "~/types/courseTemp";

// Define Note interface if not imported from types

interface Props {
  isOpen?: boolean;
  notes?: Note[];
  noteContent?: string;
  notesSaved?: boolean;
  courseId: number | string;
  lessonId: number | string;
  width?: number; // Configurable panel width
  position?: "left" | "right"; // Panel position
  theme?: "light" | "dark" | "indigo"; // Theme options
}

// Props with defaults
const props = withDefaults(defineProps<Props>(), {
  isOpen: false,
  notes: () => [],
  noteContent: "",
  notesSaved: false,
  width: 384, // Default width (96 * 4 = 384px)
  position: "right", // Default position
  theme: "indigo", // Default theme
});

// Emits with proper typing
const emit = defineEmits<{
  (e: "close" | "save"): void;
  (e: "delete" | "update:noteContent", payload: string): void;
  (e: "resize", width: number): void;
}>();

// Local state for note content
const localNoteContent = ref(props.noteContent);

// UI state variables
const isEditorFocused = ref(false);
const isResizing = ref(false);
const startX = ref(0);
const startWidth = ref(props.width);
const panelWidth = ref(props.width);
const noteSavedAnimation = ref(false);
const deleteConfirmation = ref<string | null>(null);
const searchQuery = ref("");
const isSorting = ref(false);
const sortBy = ref<"newest" | "oldest">("newest");
const isSearchFocused = ref(false);
const panelMaximized = ref(false);
const theme = ref(props.theme);

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

    // Clear text area after saving if needed
    // localNoteContent.value = '';
    // emit('update:noteContent', '');
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
};

// Toggle panel maximization
const toggleMaximize = () => {
  panelMaximized.value = !panelMaximized.value;
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

// Resize handlers
const startResize = (event: MouseEvent) => {
  isResizing.value = true;
  startX.value = event.clientX;
  startWidth.value = panelWidth.value;
  document.addEventListener("mousemove", handleResize);
  document.addEventListener("mouseup", stopResize);
};

const handleResize = (event: MouseEvent) => {
  if (!isResizing.value) return;

  const delta =
    props.position === "right"
      ? startX.value - event.clientX
      : event.clientX - startX.value;

  const newWidth = Math.max(320, Math.min(800, startWidth.value + delta));
  panelWidth.value = newWidth;
  emit("resize", newWidth);
};

const stopResize = () => {
  isResizing.value = false;
  document.removeEventListener("mousemove", handleResize);
  document.removeEventListener("mouseup", stopResize);
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

// Toggle sort order
const toggleSortOrder = () => {
  isSorting.value = true;
  sortBy.value = sortBy.value === "newest" ? "oldest" : "newest";

  setTimeout(() => {
    isSorting.value = false;
  }, 300);
};

// Computed styles based on the panel's state
const panelStyle = computed(() => {
  const width = panelMaximized.value ? "100%" : `${panelWidth.value}px`;
  const transform = props.isOpen
    ? "translateX(0)"
    : props.position === "right"
    ? "translateX(100%)"
    : "translateX(-100%)";

  return {
    width,
    transform,
    [props.position]: "0",
  };
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

// Clean up event listeners on unmount
onBeforeUnmount(() => {
  document.removeEventListener("mousemove", handleResize);
  document.removeEventListener("mouseup", stopResize);
});

// Focus the editor on mount if panel is open
onMounted(() => {
  if (props.isOpen) {
    nextTick(() => {
      const textarea = document.getElementById("note-textarea");
      if (textarea) {
        textarea.focus();
      }
    });
  }
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/no-v-html -->
<template>
  <div
    class="fixed top-0 bottom-0 overflow-hidden shadow-xl z-40 border transition-all duration-300 ease-in-out"
    :class="[themeClasses.panel, panelMaximized ? 'left-0 right-0' : '']"
    :style="panelStyle"
  >
    <!-- Resize handle -->
    <div
      v-if="!panelMaximized"
      class="absolute top-0 bottom-0 w-1 cursor-ew-resize z-50 hover:bg-indigo-500 transition-colors"
      :class="{ 'opacity-50': isResizing }"
      :style="{ [position === 'right' ? 'left' : 'right']: '0' }"
      @mousedown="startResize"
    />

    <!-- Panel header -->
    <div
      :class="[
        'px-4 py-3 flex justify-between items-center shadow-md',
        themeClasses.header,
      ]"
    >
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

      <div class="flex items-center space-x-2">
        <!-- Theme toggle -->
        <button
          class="p-1.5 rounded-full transition-colors focus:outline-none"
          :class="themeClasses.icon"
          @click="toggleTheme"
        >
          <svg
            v-if="theme === 'light'"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
            />
          </svg>
          <svg
            v-else-if="theme === 'dark'"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
              clip-rule="evenodd"
            />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z"
            />
          </svg>
        </button>

        <!-- Maximize/Minimize -->
        <button
          class="p-1.5 rounded-full transition-colors focus:outline-none"
          :class="themeClasses.icon"
          @click="toggleMaximize"
        >
          <svg
            v-if="panelMaximized"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 11-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z"
              clip-rule="evenodd"
            />
          </svg>
        </button>

        <!-- Close Button -->
        <button
          class="p-1.5 rounded-full transition-colors focus:outline-none"
          :class="themeClasses.icon"
          @click="closePanel"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
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

    <!-- Panel content -->
    <div class="flex flex-col h-[calc(100%-3.5rem)]">
      <!-- Search bar -->
      <div class="p-3 border-b" :class="themeClasses.divider">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search notes..."
            class="w-full px-3 py-2 pr-10 rounded-lg transition-all duration-200"
            :class="[
              themeClasses.input,
              isSearchFocused ? 'ring-2 ring-indigo-300' : '',
            ]"
            @focus="isSearchFocused = true"
            @blur="isSearchFocused = false"
          />
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 absolute right-3 top-2.5"
            :class="themeClasses.icon"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </div>
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
          <textarea
            id="note-textarea"
            v-model="localNoteContent"
            rows="6"
            placeholder="Type your notes here..."
            class="w-full px-3 py-2 rounded-lg resize-none transition-all duration-200"
            :class="[
              themeClasses.input,
              isEditorFocused ? 'ring-2 ring-indigo-300' : '',
              canSaveNote ? 'border-green-300' : '',
            ]"
            @input="updateNoteContent"
            @focus="isEditorFocused = true"
            @blur="isEditorFocused = false"
            @keydown="handleKeyDown"
          />

          <!-- Focus indicator line -->
          <div
            class="absolute top-0 left-0 w-1 h-full rounded-l-lg bg-indigo-500 transition-all duration-200"
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
          <button
            class="px-4 py-2 rounded-lg transition-all duration-200 flex items-center focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm"
            :class="[
              canSaveNote
                ? themeClasses.button +
                  ' transform hover:scale-105 hover:shadow'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed',
            ]"
            :disabled="!canSaveNote"
            @click="saveNote"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            Save Note
          </button>
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
            <button
              class="flex items-center text-xs p-1.5 rounded transition-colors"
              :class="themeClasses.icon"
              @click="toggleSortOrder"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  v-if="sortBy === 'newest'"
                  d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"
                />
                <path
                  v-else
                  d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"
                  transform="rotate(180, 12, 12)"
                />
              </svg>
              Sort: {{ sortBy === "newest" ? "Newest first" : "Oldest first" }}
            </button>
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
                  <button
                    class="text-xs px-2 py-1 rounded bg-red-500 text-white hover:bg-red-600 transition-colors"
                    @click="deleteNote(note.id)"
                  >
                    Confirm
                  </button>
                  <button
                    class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors"
                    @click="cancelDelete"
                  >
                    Cancel
                  </button>
                </div>

                <button
                  v-else
                  class="p-1 rounded-full transition-colors focus:outline-none"
                  :class="themeClasses.icon"
                  aria-label="Delete note"
                  @click="confirmDelete(note.id)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
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
  </div>
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
