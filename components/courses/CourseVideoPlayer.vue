<!-- components/courses/CourseVideoPlayer.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";

// Types
interface VideoPlayerProps {
  videoUrl: string;
  title: string;
  poster?: string;
  autoplay?: boolean;
  showControls?: boolean;
  aspectRatio?: "16:9" | "4:3" | "1:1";
  onTimeUpdate?: (time: number) => void;
  onEnded?: () => void;
  startTime?: number;
}

interface PlayerState {
  currentTime: number;
  duration: number;
  isPlaying: boolean;
  isFullscreen: boolean;
  volume: number;
  isMuted: boolean;
  playbackRate: number;
  isLoading: boolean;
  progress: number;
  buffered: number;
  error: string | null;
}

// Props
const props = withDefaults(defineProps<VideoPlayerProps>(), {
  poster: "",
  autoplay: false,
  showControls: true,
  aspectRatio: "16:9",
  startTime: 0,
  onTimeUpdate: () => {}, // Default to a no-op function
  onEnded: () => {}, // Default to a no-op function
});

// Emits
const emit = defineEmits<{
  (e: "timeUpdate", time: number): void;
  (e: "ended" | "ready"): void;
  (e: "error", errorMessage: string): void;
}>();

// Refs
const videoRef = ref<HTMLVideoElement | null>(null);
const containerRef = ref<HTMLDivElement | null>(null);
const controlsVisible = ref(true);
const controlsTimeout = ref<number | null>(null);
const seekBarRef = ref<HTMLDivElement | null>(null);
const isDragging = ref(false);

// Player state
const playerState = ref<PlayerState>({
  currentTime: 0,
  duration: 0,
  isPlaying: false,
  isFullscreen: false,
  volume: 1,
  isMuted: false,
  playbackRate: 1,
  isLoading: true,
  progress: 0,
  buffered: 0,
  error: null,
});

// Formatted time displays
const formattedCurrentTime = computed(() => {
  return formatTime(playerState.value.currentTime);
});

const formattedDuration = computed(() => {
  return formatTime(playerState.value.duration);
});

// const formattedTimeRemaining = computed(() => {
//   return formatTime(playerState.value.duration - playerState.value.currentTime);
// });

// Classes for aspect ratio
const aspectRatioClass = computed(() => {
  switch (props.aspectRatio) {
    case "16:9":
      return "aspect-w-16 aspect-h-9";
    case "4:3":
      return "aspect-w-4 aspect-h-3";
    case "1:1":
      return "aspect-w-1 aspect-h-1";
    default:
      return "aspect-w-16 aspect-h-9";
  }
});

// Video player methods
const play = () => {
  if (videoRef.value) {
    videoRef.value
      .play()
      .then(() => {
        playerState.value.isPlaying = true;
      })
      .catch((error) => {
        if (error.name === "NotAllowedError") {
          // Handle autoplay restriction
          playerState.value.isPlaying = false;
        } else {
          playerState.value.error = error.message;
          emit("error", error.message);
        }
      });
  }
};

const pause = () => {
  if (videoRef.value) {
    videoRef.value.pause();
    playerState.value.isPlaying = false;
  }
};

const togglePlay = () => {
  if (playerState.value.isPlaying) {
    pause();
  } else {
    play();
  }
};

const toggleMute = () => {
  if (videoRef.value) {
    videoRef.value.muted = !videoRef.value.muted;
    playerState.value.isMuted = videoRef.value.muted;
  }
};

const setVolume = (value: number) => {
  if (videoRef.value) {
    const volume = Math.max(0, Math.min(1, value));
    videoRef.value.volume = volume;
    playerState.value.volume = volume;
    playerState.value.isMuted = volume === 0;
  }
};

const setPlaybackRate = (rate: number) => {
  if (videoRef.value) {
    videoRef.value.playbackRate = rate;
    playerState.value.playbackRate = rate;
  }
};

const seek = (time: number) => {
  if (videoRef.value) {
    // Ensure time is within valid range
    const seekTime = Math.max(0, Math.min(time, playerState.value.duration));
    videoRef.value.currentTime = seekTime;
    playerState.value.currentTime = seekTime;
  }
};

const seekByPercentage = (percentage: number) => {
  if (videoRef.value && playerState.value.duration) {
    const seekTime = (percentage / 100) * playerState.value.duration;
    seek(seekTime);
  }
};

const toggleFullscreen = () => {
  if (!containerRef.value) return;

  if (!document.fullscreenElement) {
    containerRef.value
      .requestFullscreen()
      .then(() => {
        playerState.value.isFullscreen = true;
      })
      .catch((err) => {
        playerState.value.error = `Error attempting to enable fullscreen: ${err.message}`;
        emit("error", `Error attempting to enable fullscreen: ${err.message}`);
      });
  } else {
    document
      .exitFullscreen()
      .then(() => {
        playerState.value.isFullscreen = false;
      })
      .catch((err) => {
        playerState.value.error = `Error attempting to exit fullscreen: ${err.message}`;
        emit("error", `Error attempting to exit fullscreen: ${err.message}`);
      });
  }
};

const _showControls = () => {
  controlsVisible.value = true;

  // Clear any existing timeout
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
  }

  // Set a new timeout to hide controls
  if (playerState.value.isPlaying && !isDragging.value) {
    controlsTimeout.value = window.setTimeout(() => {
      controlsVisible.value = false;
    }, 3000);
  }
};

const handleSeekBarClick = (event: MouseEvent) => {
  if (!seekBarRef.value) return;

  const rect = seekBarRef.value.getBoundingClientRect();
  const offsetX = event.clientX - rect.left;
  const percentage = (offsetX / rect.width) * 100;

  seekByPercentage(Math.min(100, Math.max(0, percentage)));
};

const startDrag = (event: MouseEvent) => {
  isDragging.value = true;
  document.addEventListener("mousemove", handleDrag);
  document.addEventListener("mouseup", stopDrag);
  handleDrag(event);
};

const handleDrag = (event: MouseEvent) => {
  if (!seekBarRef.value) return;

  const rect = seekBarRef.value.getBoundingClientRect();
  const offsetX = Math.min(rect.width, Math.max(0, event.clientX - rect.left));
  const percentage = (offsetX / rect.width) * 100;

  playerState.value.progress = percentage;
};

const stopDrag = () => {
  if (isDragging.value) {
    seekByPercentage(playerState.value.progress);
    isDragging.value = false;
    document.removeEventListener("mousemove", handleDrag);
    document.removeEventListener("mouseup", stopDrag);
  }
};

// Helper function to format time in MM:SS or HH:MM:SS
const formatTime = (seconds: number): string => {
  if (isNaN(seconds) || !isFinite(seconds)) return "00:00";

  seconds = Math.floor(seconds);

  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  const secs = seconds % 60;

  if (hours > 0) {
    return `${hours.toString().padStart(2, "0")}:${minutes
      .toString()
      .padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
  } else {
    return `${minutes.toString().padStart(2, "0")}:${secs
      .toString()
      .padStart(2, "0")}`;
  }
};

// Video event handlers
const handleLoadedMetadata = () => {
  if (videoRef.value) {
    playerState.value.duration = videoRef.value.duration;
    playerState.value.isLoading = false;

    // Set initial time if provided
    if (props.startTime > 0 && props.startTime < playerState.value.duration) {
      videoRef.value.currentTime = props.startTime;
      playerState.value.currentTime = props.startTime;
    }

    // Start playback if autoplay is true
    if (props.autoplay) {
      play();
    }

    emit("ready");
  }
};

const handleTimeUpdate = () => {
  if (videoRef.value && !isDragging.value) {
    playerState.value.currentTime = videoRef.value.currentTime;
    playerState.value.progress =
      (videoRef.value.currentTime / playerState.value.duration) * 100;

    // Update buffered progress
    if (videoRef.value.buffered.length > 0) {
      const bufferedEnd = videoRef.value.buffered.end(
        videoRef.value.buffered.length - 1
      );
      playerState.value.buffered =
        (bufferedEnd / playerState.value.duration) * 100;
    }

    // Emit time update
    emit("timeUpdate", videoRef.value.currentTime);
  }
};

const handleEnded = () => {
  playerState.value.isPlaying = false;
  emit("ended");
};

const handleError = () => {
  playerState.value.isLoading = false;
  playerState.value.error = "An error occurred while loading the video.";
  emit("error", playerState.value.error);
};

const handleProgress = () => {
  if (videoRef.value && videoRef.value.buffered.length > 0) {
    const bufferedEnd = videoRef.value.buffered.end(
      videoRef.value.buffered.length - 1
    );
    playerState.value.buffered =
      (bufferedEnd / playerState.value.duration) * 100;
  }
};

const handleKeyDown = (event: KeyboardEvent) => {
  // Only handle keyboard shortcuts when the video player is in focus
  if (
    !containerRef.value?.contains(document.activeElement) &&
    document.activeElement !== containerRef.value
  ) {
    return;
  }

  switch (event.key.toLowerCase()) {
    case " ":
    case "k":
      // Space or K: Toggle play/pause
      event.preventDefault();
      togglePlay();
      break;
    case "f":
      // F: Toggle fullscreen
      event.preventDefault();
      toggleFullscreen();
      break;
    case "m":
      // M: Toggle mute
      event.preventDefault();
      toggleMute();
      break;
    case "arrowright":
      // Right arrow: Forward 5 seconds
      event.preventDefault();
      seek(playerState.value.currentTime + 5);
      break;
    case "arrowleft":
      // Left arrow: Rewind 5 seconds
      event.preventDefault();
      seek(playerState.value.currentTime - 5);
      break;
    case "arrowup":
      // Up arrow: Increase volume
      event.preventDefault();
      setVolume(playerState.value.volume + 0.1);
      break;
    case "arrowdown":
      // Down arrow: Decrease volume
      event.preventDefault();
      setVolume(playerState.value.volume - 0.1);
      break;
    case "0":
    case "1":
    case "2":
    case "3":
    case "4":
    case "5":
    case "6":
    case "7":
    case "8":
    case "9": {
      // Number keys 0-9: Seek to percentage of video
      event.preventDefault();
      const percent = parseInt(event.key) * 10;
      seekByPercentage(percent);
      break;
    }
  }
};

// Watch for external time update requests
watch(
  () => props.startTime,
  (newTime) => {
    if (newTime > 0 && videoRef.value) {
      seek(newTime);
    }
  }
);

// Lifecycle hooks
onMounted(() => {
  document.addEventListener("keydown", handleKeyDown);
  document.addEventListener("fullscreenchange", () => {
    playerState.value.isFullscreen = !!document.fullscreenElement;
  });

  // Initialize video with props
  if (videoRef.value) {
    videoRef.value.volume = playerState.value.volume;
    videoRef.value.muted = playerState.value.isMuted;
    videoRef.value.playbackRate = playerState.value.playbackRate;
  }
});

onBeforeUnmount(() => {
  document.removeEventListener("keydown", handleKeyDown);
  document.removeEventListener("fullscreenchange", () => {});

  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
  }

  // Clean up any event listeners
  document.removeEventListener("mousemove", handleDrag);
  document.removeEventListener("mouseup", stopDrag);
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    ref="containerRef"
    class="video-player relative overflow-hidden rounded-lg shadow-lg bg-black"
    :class="aspectRatioClass"
    @mousemove="_showControls"
    @mouseenter="_showControls"
  >
    <!-- Video element -->
    <video
      ref="videoRef"
      :src="videoUrl"
      :poster="poster"
      :muted="playerState.isMuted"
      :playbackRate="playerState.playbackRate"
      preload="metadata"
      class="w-full h-full object-contain"
      @loadedmetadata="handleLoadedMetadata"
      @timeupdate="handleTimeUpdate"
      @ended="handleEnded"
      @error="handleError"
      @progress="handleProgress"
      @click="togglePlay"
    />

    <!-- Loading spinner -->
    <div
      v-if="playerState.isLoading"
      class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div
        class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-white"
      />
    </div>

    <!-- Error message -->
    <div
      v-if="playerState.error"
      class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-75 p-4 text-white text-center"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-12 w-12 text-red-500 mb-2"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
        />
      </svg>
      <h3 class="text-xl font-bold mb-2">Video Error</h3>
      <p>{{ playerState.error }}</p>
      <button
        class="mt-4 px-4 py-2 bg-white text-black font-medium rounded hover:bg-gray-200 transition-colors"
        @click="videoRef?.load()"
      >
        Retry
      </button>
    </div>

    <!-- Play/pause overlay -->
    <div
      v-if="
        !playerState.isPlaying && !playerState.isLoading && !playerState.error
      "
      class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 cursor-pointer"
      @click="togglePlay"
    >
      <div
        class="bg-white bg-opacity-80 rounded-full p-4 transform transition-transform hover:scale-110"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-10 w-10 text-indigo-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
    </div>

    <!-- Video title overlay (visible on hover) -->
    <div
      class="absolute top-0 left-0 right-0 p-4 bg-gradient-to-b from-black/70 to-transparent transition-opacity duration-300"
      :class="{ 'opacity-0': !controlsVisible && playerState.isPlaying }"
    >
      <h3 class="text-white text-lg font-medium">{{ title }}</h3>
    </div>

    <!-- Video controls (visible on hover) -->
    <div
      v-if="showControls"
      class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 transition-opacity duration-300"
      :class="{ 'opacity-0': !controlsVisible && playerState.isPlaying }"
    >
      <!-- Progress bar -->
      <div
        ref="seekBarRef"
        class="seek-bar relative w-full h-2 bg-gray-500 bg-opacity-50 rounded-full overflow-hidden cursor-pointer mb-3"
        @click="handleSeekBarClick"
        @mousedown="startDrag"
      >
        <!-- Buffered progress -->
        <div
          class="absolute h-full bg-gray-300 bg-opacity-40"
          :style="{ width: `${playerState.buffered}%` }"
        />

        <!-- Playback progress -->
        <div
          class="absolute h-full bg-indigo-500 transition-all"
          :style="{ width: `${playerState.progress}%` }"
        />

        <!-- Seek handle -->
        <div
          class="absolute h-4 w-4 bg-white rounded-full shadow-md transform -translate-y-1/2 top-1/2 transition-all"
          :style="{ left: `calc(${playerState.progress}% - 8px)` }"
        />
      </div>

      <!-- Controls buttons -->
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <!-- Play/pause button -->
          <button
            class="text-white hover:text-indigo-300 transition-colors focus:outline-none"
            aria-label="Play/Pause"
            @click="togglePlay"
          >
            <svg
              v-if="playerState.isPlaying"
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </button>

          <!-- Volume control -->
          <div class="relative group inline-block">
            <button
              class="text-white hover:text-indigo-300 transition-colors focus:outline-none"
              aria-label="Toggle Mute"
              @click="toggleMute"
            >
              <svg
                v-if="playerState.isMuted"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
                  clip-rule="evenodd"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"
                />
              </svg>
              <svg
                v-else-if="playerState.volume < 0.5"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15.536 8.464a5 5 0 010 7.072"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15.536 8.464a5 5 0 010 7.072m3.536-10.608a9 9 0 010 14.144"
                />
              </svg>
            </button>

            <!-- Volume slider (appears on hover) -->
            <div
              class="absolute hidden group-hover:block bottom-full left-1/2 transform -translate-x-1/2 mb-2 p-2 bg-gray-800 rounded-lg shadow-lg"
            >
              <input
                type="range"
                min="0"
                max="1"
                step="0.01"
                :value="playerState.volume"
                class="w-24 accent-indigo-500"
                @input="setVolume(parseFloat($event.target.value))"
              />
            </div>
          </div>

          <!-- Time display -->
          <div class="text-white text-sm">
            {{ formattedCurrentTime }} / {{ formattedDuration }}
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <!-- Playback speed -->
          <div class="relative group inline-block">
            <button
              class="text-white hover:text-indigo-300 transition-colors focus:outline-none text-sm px-2 rounded"
              aria-label="Playback Speed"
            >
              {{ playerState.playbackRate }}x
            </button>

            <!-- Speed options (appears on hover) -->
            <div
              class="absolute hidden group-hover:block bottom-full right-0 transform mb-2 p-2 bg-gray-800 rounded-lg shadow-lg whitespace-nowrap"
            >
              <div class="flex flex-col space-y-1">
                <button
                  v-for="rate in [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]"
                  :key="rate"
                  class="px-3 py-1 text-sm text-white hover:bg-indigo-500 rounded transition-colors"
                  :class="{
                    'bg-indigo-600': playerState.playbackRate === rate,
                  }"
                  @click="setPlaybackRate(rate)"
                >
                  {{ rate }}x
                </button>
              </div>
            </div>
          </div>

          <!-- Fullscreen toggle -->
          <button
            class="text-white hover:text-indigo-300 transition-colors focus:outline-none"
            aria-label="Fullscreen"
            @click="toggleFullscreen"
          >
            <svg
              v-if="playerState.isFullscreen"
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 9V4.5M9 9H4.5M15 9H19.5M15 9V4.5M15 15v4.5M15 15H4.5M15 15h4.5M9 15v4.5"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Aspect ratio classes - fallback for browsers without full support */
.aspect-w-16.aspect-h-9 {
  position: relative;
  padding-bottom: 56.25%; /* 9/16 = 0.5625 */
}

.aspect-w-4.aspect-h-3 {
  position: relative;
  padding-bottom: 75%; /* 3/4 = 0.75 */
}

.aspect-w-1.aspect-h-1 {
  position: relative;
  padding-bottom: 100%; /* 1/1 = 1 */
}

.aspect-w-16.aspect-h-9 > video,
.aspect-w-4.aspect-h-3 > video,
.aspect-w-1.aspect-h-1 > video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Custom range input styling */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  background: transparent;
  cursor: pointer;
  width: 100%;
}

input[type="range"]::-webkit-slider-runnable-track {
  height: 0.5rem;
  background: #374151;
  border-radius: 0.25rem;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  margin-top: -0.25rem;
  background-color: #6366f1;
  height: 1rem;
  width: 1rem;
  border-radius: 50%;
}

input[type="range"]::-moz-range-track {
  height: 0.5rem;
  background: #374151;
  border-radius: 0.25rem;
}

input[type="range"]::-moz-range-thumb {
  border: none;
  background-color: #6366f1;
  height: 1rem;
  width: 1rem;
  border-radius: 50%;
}
</style>
