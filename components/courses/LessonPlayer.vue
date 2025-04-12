<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<!-- components/courses/LessonPlayer.vue -->
<script setup lang="ts">
interface LessonPlayerProps {
  videoUrl: string;
  posterUrl?: string;
  autoplay?: boolean;
  allowFullscreen?: boolean;
  startTime?: number; // in seconds
  onComplete?: () => void;
  saveProgress?: boolean;
}

const props = withDefaults(defineProps<LessonPlayerProps>(), {
  posterUrl: "",
  autoplay: false,
  allowFullscreen: true,
  startTime: 0,
  onComplete: () => {},
  saveProgress: true,
});

// Emit events
const emit = defineEmits<{
  (e: "timeupdate", time: number): void;
  (e: "ready" | "ended" | "playing" | "paused" | "loaded"): void;
  (e: "error", error: string): void;
}>();

// Video player state
const videoRef = ref<HTMLVideoElement | null>(null);
const containerRef = ref<HTMLElement | null>(null);
const isPlaying = ref(false);
const isFullscreen = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const progress = ref(0);
const volume = ref(1);
const isMuted = ref(false);
const isPipActive = ref(false);
const isLoading = ref(true);
const showControls = ref(true);
const error = ref("");
const controlsTimeout = ref<number | null>(null);

// Player settings
const playbackRates = [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2];
const playbackRate = ref(1);

// Handle video loading
const handleVideoLoaded = () => {
  if (!videoRef.value) return;

  isLoading.value = false;
  duration.value = videoRef.value.duration;

  // Set start time if provided
  if (props.startTime > 0 && props.startTime < duration.value) {
    videoRef.value.currentTime = props.startTime;
    currentTime.value = props.startTime;
  }

  // Autoplay if enabled
  if (props.autoplay) {
    play();
  }

  emit("loaded");
  emit("ready");
};

// Play/Pause functionality
const togglePlay = () => {
  if (isPlaying.value) {
    pause();
  } else {
    play();
  }
};

const play = async () => {
  if (!videoRef.value) return;

  try {
    await videoRef.value.play();
    isPlaying.value = true;
    emit("playing");
  } catch (e) {
    console.error("Error playing video:", e);
    error.value = "Failed to play video. Please try again.";
    emit("error", "Failed to play video");
  }
};

const pause = () => {
  if (!videoRef.value) return;

  videoRef.value.pause();
  isPlaying.value = false;
  emit("paused");
};

// Time/progress handling
const handleTimeUpdate = () => {
  if (!videoRef.value) return;

  currentTime.value = videoRef.value.currentTime;
  progress.value = (currentTime.value / duration.value) * 100;

  // Save progress every 5 seconds if enabled
  if (props.saveProgress && currentTime.value % 5 < 0.1) {
    saveVideoProgress();
  }

  emit("timeupdate", currentTime.value);
};

const saveVideoProgress = () => {
  // Get courseId and lessonId from URL or props if implemented
  const courseId = "course-id"; // replace with actual implementation
  const lessonId = "lesson-id"; // replace with actual implementation

  // Save to localStorage as an example (in a real app, this would be sent to an API)
  if (courseId && lessonId) {
    localStorage.setItem(
      `course_progress_${courseId}_${lessonId}`,
      JSON.stringify({
        position: currentTime.value,
        duration: duration.value,
        progress: progress.value,
        lastUpdated: new Date().toISOString(),
      })
    );
  }
};

// Format time (seconds to MM:SS format)
const formatTime = (seconds: number): string => {
  if (isNaN(seconds)) return "00:00";

  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = Math.floor(seconds % 60);

  return `${minutes.toString().padStart(2, "0")}:${remainingSeconds
    .toString()
    .padStart(2, "0")}`;
};

// Volume controls
const toggleMute = () => {
  if (!videoRef.value) return;

  videoRef.value.muted = !videoRef.value.muted;
  isMuted.value = videoRef.value.muted;
};

const updateVolume = (newVolume: number) => {
  if (!videoRef.value) return;

  volume.value = newVolume;
  videoRef.value.volume = newVolume;
  isMuted.value = newVolume === 0;
};

// Seek functionality
const handleSeek = (event: MouseEvent) => {
  if (!videoRef.value || !containerRef.value) return;

  const progressElement = event.currentTarget as HTMLElement;
  const rect = progressElement.getBoundingClientRect();
  const seekPosition = (event.clientX - rect.left) / rect.width;

  const seekTime = duration.value * seekPosition;
  videoRef.value.currentTime = seekTime;
  currentTime.value = seekTime;
};

// Playback rate
const changePlaybackRate = (rate: number) => {
  if (!videoRef.value) return;

  playbackRate.value = rate;
  videoRef.value.playbackRate = rate;
};

// Handle fullscreen
const toggleFullscreen = async () => {
  if (!containerRef.value) return;

  if (!isFullscreen.value) {
    try {
      if (containerRef.value.requestFullscreen) {
        await containerRef.value.requestFullscreen();
      } else if ((containerRef.value as any).webkitRequestFullscreen) {
        await (containerRef.value as any).webkitRequestFullscreen();
      } else if ((containerRef.value as any).msRequestFullscreen) {
        await (containerRef.value as any).msRequestFullscreen();
      }
      isFullscreen.value = true;
    } catch (e) {
      console.error("Error entering fullscreen:", e);
    }
  } else {
    try {
      if (document.exitFullscreen) {
        await document.exitFullscreen();
      } else if ((document as any).webkitExitFullscreen) {
        await (document as any).webkitExitFullscreen();
      } else if ((document as any).msExitFullscreen) {
        await (document as any).msExitFullscreen();
      }
      isFullscreen.value = false;
    } catch (e) {
      console.error("Error exiting fullscreen:", e);
    }
  }
};

// Handle Picture-in-Picture
const togglePip = async () => {
  if (!videoRef.value) return;

  try {
    if (!isPipActive.value) {
      if (document.pictureInPictureElement) {
        await document.exitPictureInPicture();
      }

      await videoRef.value.requestPictureInPicture();
      isPipActive.value = true;
    } else {
      await document.exitPictureInPicture();
      isPipActive.value = false;
    }
  } catch (e) {
    console.error("Error toggling Picture-in-Picture:", e);
  }
};

// Auto-hide controls
const startControlsTimer = () => {
  clearControlsTimeout();

  controlsTimeout.value = window.setTimeout(() => {
    if (isPlaying.value) {
      showControls.value = false;
    }
  }, 3000);
};

const clearControlsTimeout = () => {
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
    controlsTimeout.value = null;
  }
};

const handleMouseMove = () => {
  showControls.value = true;
  startControlsTimer();
};

// Handle video completion
const handleVideoEnded = () => {
  isPlaying.value = false;

  // Execute completion callback
  if (props.onComplete) {
    props.onComplete();
  }

  emit("ended");
};

// Keyboard shortcuts
const handleKeyDown = (event: KeyboardEvent) => {
  // Only handle shortcuts if the video player is focused
  if (!containerRef.value?.contains(document.activeElement)) return;

  switch (event.key) {
    case " ":
    case "k":
      event.preventDefault();
      togglePlay();
      break;
    case "ArrowRight":
      event.preventDefault();
      if (videoRef.value) {
        videoRef.value.currentTime = Math.min(
          videoRef.value.currentTime + 10,
          duration.value
        );
      }
      break;
    case "ArrowLeft":
      event.preventDefault();
      if (videoRef.value) {
        videoRef.value.currentTime = Math.max(
          videoRef.value.currentTime - 10,
          0
        );
      }
      break;
    case "f":
      event.preventDefault();
      if (props.allowFullscreen) {
        toggleFullscreen();
      }
      break;
    case "m":
      event.preventDefault();
      toggleMute();
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
    case "9":
      event.preventDefault();
      if (videoRef.value) {
        const percent = parseInt(event.key) * 10;
        videoRef.value.currentTime = (duration.value * percent) / 100;
      }
      break;
  }
};

// Fullscreen change event handler
const handleFullscreenChange = () => {
  isFullscreen.value = Boolean(
    document.fullscreenElement ||
      (document as any).webkitFullscreenElement ||
      (document as any).msFullscreenElement
  );
};

// Picture-in-Picture change event handler
const handlePipChange = () => {
  isPipActive.value = document.pictureInPictureElement === videoRef.value;
};

// Watch for video URL changes
watch(
  () => props.videoUrl,
  () => {
    isLoading.value = true;
    currentTime.value = 0;
    progress.value = 0;
    if (isPlaying.value) {
      pause();
    }
  }
);

// Component lifecycle
onMounted(() => {
  // Set up event listeners
  document.addEventListener("fullscreenchange", handleFullscreenChange);
  document.addEventListener("webkitfullscreenchange", handleFullscreenChange);
  document.addEventListener("msfullscreenchange", handleFullscreenChange);
  document.addEventListener("enterpictureinpicture", handlePipChange);
  document.addEventListener("leavepictureinpicture", handlePipChange);
  document.addEventListener("keydown", handleKeyDown);

  startControlsTimer();
});

onBeforeUnmount(() => {
  // Clean up event listeners
  document.removeEventListener("fullscreenchange", handleFullscreenChange);
  document.removeEventListener(
    "webkitfullscreenchange",
    handleFullscreenChange
  );
  document.removeEventListener("msfullscreenchange", handleFullscreenChange);
  document.removeEventListener("enterpictureinpicture", handlePipChange);
  document.removeEventListener("leavepictureinpicture", handlePipChange);
  document.removeEventListener("keydown", handleKeyDown);

  clearControlsTimeout();

  // Save final progress on unmount
  if (props.saveProgress && videoRef.value) {
    saveVideoProgress();
  }
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    ref="containerRef"
    class="video-player relative w-full h-0 pb-[56.25%] bg-black rounded-lg overflow-hidden focus:outline-none group"
    tabindex="0"
    @mousemove="handleMouseMove"
    @mouseleave="showControls = false"
  >
    <!-- Video element -->
    <video
      ref="videoRef"
      :src="videoUrl"
      :poster="posterUrl"
      class="absolute top-0 left-0 w-full h-full outline-none"
      preload="metadata"
      playsinline
      @timeupdate="handleTimeUpdate"
      @loadedmetadata="handleVideoLoaded"
      @ended="handleVideoEnded"
      @click="togglePlay"
      @dblclick="props.allowFullscreen && toggleFullscreen"
    />

    <!-- Loading overlay -->
    <div
      v-if="isLoading"
      class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-10"
    >
      <div
        class="w-12 h-12 border-4 border-t-indigo-600 border-r-transparent border-b-transparent border-l-transparent rounded-full animate-spin"
      />
    </div>

    <!-- Error message -->
    <div
      v-if="error"
      class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 z-20"
    >
      <div class="text-white text-center p-4">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-10 w-10 mx-auto mb-2 text-red-500"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
          />
        </svg>
        <p class="text-lg font-medium">{{ error }}</p>
        <button
          class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
          @click="
            error = '';
            isLoading = true;
            handleVideoLoaded();
          "
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- Centered play button -->
    <button
      v-if="!isPlaying && !isLoading && !error && showControls"
      class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 w-20 h-20 flex items-center justify-center bg-indigo-600 bg-opacity-80 hover:bg-opacity-100 text-white rounded-full transition-all duration-200 focus:outline-none"
      @click="play"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-10 w-10"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <!-- Video controls -->
    <div
      :class="[
        'absolute bottom-0 left-0 right-0 px-4 py-2 bg-gradient-to-t from-black to-transparent transition-opacity duration-300 z-10',
        showControls || !isPlaying
          ? 'opacity-100'
          : 'opacity-0 pointer-events-none',
      ]"
    >
      <!-- Progress bar -->
      <div
        class="progress-bar relative w-full h-2 bg-gray-700 rounded-full mb-2 cursor-pointer group"
        @click="handleSeek"
      >
        <div
          class="absolute top-0 left-0 h-full bg-indigo-600 rounded-full transition-all duration-100"
          :style="{ width: `${progress}%` }"
        />

        <!-- Hover preview (can be enhanced with thumbnail previews) -->
        <div
          class="absolute top-0 left-0 h-full w-full opacity-0 group-hover:opacity-100"
        >
          <div
            class="absolute -top-8 px-2 py-1 bg-black rounded text-white text-xs transform -translate-x-1/2 opacity-0 group-hover:opacity-100 pointer-events-none"
            :style="{ left: `${progress}%` }"
          >
            {{ formatTime(currentTime) }}
          </div>
        </div>
      </div>

      <!-- Controls row -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <!-- Play/Pause button -->
          <button
            class="text-white mr-4 focus:outline-none hover:text-indigo-400 transition-colors"
            @click="togglePlay"
          >
            <svg
              v-if="isPlaying"
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- Volume control -->
          <div class="relative mr-4 group">
            <button
              class="text-white hover:text-indigo-400 transition-colors focus:outline-none"
              @click="toggleMute"
            >
              <svg
                v-if="isMuted || volume === 0"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else-if="volume < 0.5"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.5 10a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>

            <!-- Volume slider (appears on hover) -->
            <div
              class="absolute left-0 bottom-full mb-2 hidden group-hover:block bg-black bg-opacity-70 rounded p-2"
            >
              <input
                v-model="volume"
                type="range"
                min="0"
                max="1"
                step="0.01"
                class="w-24 accent-indigo-600"
                @input="updateVolume(parseFloat($event.target.value))"
              />
            </div>
          </div>

          <!-- Time display -->
          <div class="text-white text-sm mr-4">
            <span>{{ formatTime(currentTime) }}</span>
            <span class="mx-1">/</span>
            <span>{{ formatTime(duration) }}</span>
          </div>

          <!-- Playback rate -->
          <div class="relative group">
            <button
              class="text-white text-sm px-2 py-1 rounded hover:bg-white hover:bg-opacity-20 transition-colors focus:outline-none"
            >
              {{ playbackRate }}x
            </button>

            <!-- Playback rate options (dropdown) -->
            <div
              class="absolute bottom-full left-0 mb-2 hidden group-hover:block bg-black bg-opacity-70 rounded py-1 w-20"
            >
              <button
                v-for="rate in playbackRates"
                :key="rate"
                class="block w-full text-left px-3 py-1 text-sm text-white hover:bg-indigo-600 transition-colors focus:outline-none"
                :class="{ 'font-bold': playbackRate === rate }"
                @click="changePlaybackRate(rate)"
              >
                {{ rate }}x
              </button>
            </div>
          </div>
        </div>

        <!-- Right side controls -->
        <div class="flex items-center">
          <!-- PiP button -->
          <ClientOnly>
            <button
              v-if="'pictureInPictureEnabled' in document"
              class="text-white mr-3 hover:text-indigo-400 transition-colors focus:outline-none"
              :class="{ 'text-indigo-400': isPipActive }"
              @click="togglePip"
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
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </button>
          </ClientOnly>

          <!-- Fullscreen button -->
          <button
            v-if="props.allowFullscreen"
            class="text-white hover:text-indigo-400 transition-colors focus:outline-none"
            @click="toggleFullscreen"
          >
            <svg
              v-if="isFullscreen"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5 5a1 1 0 00-1 1v1h-.5a.5.5 0 00-.5.5v1a.5.5 0 00.5.5H5v1a1 1 0 002 0V9h1.5a.5.5 0 00.5-.5v-1a.5.5 0 00-.5-.5H7V6a1 1 0 00-1-1zm6 0a1 1 0 00-1 1v1h1.5a.5.5 0 01.5.5v1a.5.5 0 01-.5.5H10v1a1 1 0 102 0V9h.5a.5.5 0 00.5-.5v-1a.5.5 0 00-.5-.5H12V6a1 1 0 00-1-1zm-6 6a1 1 0 100 2h.5v1a1 1 0 102 0v-1h1.5a.5.5 0 00.5-.5v-1a.5.5 0 00-.5-.5H8v-.5a1 1 0 00-1-1zm6 0a1 1 0 100 2h.5v1a1 1 0 102 0v-1h1.5a.5.5 0 00.5-.5v-1a.5.5 0 00-.5-.5H16v-.5a1 1 0 00-1-1z"
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
                d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 011.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 011.414-1.414L15 13.586V12a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom video player styling */
.video-player:hover .progress-bar {
  height: 4px;
}

.progress-bar {
  transition: height 0.2s ease;
}

/* Custom range input styling */
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  border-radius: 3px;
  background: rgba(255, 255, 255, 0.3);
  outline: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  /* background: theme("colors.indigo.600"); */
  cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  /* background: theme("colors.indigo.600"); */
  cursor: pointer;
  border: none;
}
</style>
