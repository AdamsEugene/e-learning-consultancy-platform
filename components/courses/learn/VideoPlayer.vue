<!-- components/courses/VideoPlayer.vue -->
<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from "vue";

interface Props {
  videoUrl: string;
  poster?: string;
  progress?: number;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  poster: "/images/courses/c3.jpg",
  progress: 0,
});

const emit = defineEmits(["time-update", "ended"]);

const videoRef = ref<HTMLVideoElement | null>(null);
const progressBarRef = ref<HTMLDivElement | null>(null);
const isDragging = ref(false);
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const volume = ref(1);
const isMuted = ref(false);
const isFullScreen = ref(false);
const showControls = ref(true);
const hideControlsTimeout = ref<number | null>(null);

// Format time in mm:ss
const formatTime = (seconds: number): string => {
  if (isNaN(seconds)) return "00:00";

  const mins = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  return `${mins.toString().padStart(2, "0")}:${secs
    .toString()
    .padStart(2, "0")}`;
};

// Computed values for display
const formattedCurrentTime = computed(() => formatTime(currentTime.value));
const formattedDuration = computed(() => formatTime(duration.value));
const progressPercent = computed(() => {
  if (duration.value === 0) return 0;
  return (currentTime.value / duration.value) * 100;
});

// Video control functions
const togglePlay = () => {
  if (!videoRef.value) return;

  if (isPlaying.value) {
    videoRef.value.pause();
  } else {
    videoRef.value.play();
  }
};

const toggleMute = () => {
  if (!videoRef.value) return;

  isMuted.value = !isMuted.value;
  videoRef.value.muted = isMuted.value;
};

const changeVolume = (e: Event) => {
  if (!videoRef.value) return;

  const input = e.target as HTMLInputElement;
  volume.value = parseFloat(input.value);
  videoRef.value.volume = volume.value;

  if (volume.value === 0) {
    isMuted.value = true;
  } else {
    isMuted.value = false;
  }
};

const seek = (e: MouseEvent) => {
  if (!videoRef.value || !progressBarRef.value) return;

  const rect = progressBarRef.value.getBoundingClientRect();
  const pos = (e.clientX - rect.left) / rect.width;
  videoRef.value.currentTime = pos * duration.value;
  currentTime.value = videoRef.value.currentTime;
};

const startDrag = () => {
  isDragging.value = true;
};

const stopDrag = () => {
  isDragging.value = false;
};

const dragSeek = (e: MouseEvent) => {
  if (!isDragging.value) return;
  seek(e);
};

const toggleFullScreen = () => {
  if (!videoRef.value) return;

  if (!document.fullscreenElement) {
    videoRef.value
      .requestFullscreen()
      .then(() => {
        isFullScreen.value = true;
      })
      .catch((err) => {
        console.error(`Error attempting to enable fullscreen: ${err.message}`);
      });
  } else {
    document.exitFullscreen();
    isFullScreen.value = false;
  }
};

// Event handlers
const onTimeUpdate = () => {
  if (!videoRef.value) return;

  currentTime.value = videoRef.value.currentTime;
  emit("time-update", currentTime.value);
};

const onLoadedMetadata = () => {
  if (!videoRef.value) return;

  duration.value = videoRef.value.duration;
};

const onPlay = () => {
  isPlaying.value = true;
  startControlsTimer();
};

const onPause = () => {
  isPlaying.value = false;
  clearControlsTimer();
  showControls.value = true;
};

const onEnded = () => {
  isPlaying.value = false;
  emit("ended");
};

// Controls visibility
const startControlsTimer = () => {
  clearControlsTimer();
  hideControlsTimeout.value = window.setTimeout(() => {
    if (isPlaying.value) {
      showControls.value = false;
    }
  }, 3000);
};

const clearControlsTimer = () => {
  if (hideControlsTimeout.value) {
    clearTimeout(hideControlsTimeout.value);
    hideControlsTimeout.value = null;
  }
};

const handleMouseMove = () => {
  showControls.value = true;
  startControlsTimer();
};

const handleMouseLeave = () => {
  if (isPlaying.value) {
    showControls.value = false;
  }
};

// Skip forward/backward
const skipForward = () => {
  if (!videoRef.value) return;
  videoRef.value.currentTime = Math.min(
    videoRef.value.duration,
    videoRef.value.currentTime + 10
  );
};

const skipBackward = () => {
  if (!videoRef.value) return;
  videoRef.value.currentTime = Math.max(0, videoRef.value.currentTime - 10);
};

// Keyboard shortcuts
const handleKeyDown = (e: KeyboardEvent) => {
  if (!videoRef.value) return;

  switch (e.key.toLowerCase()) {
    case " ":
    case "k":
      e.preventDefault();
      togglePlay();
      break;
    case "arrowright":
      e.preventDefault();
      skipForward();
      break;
    case "arrowleft":
      e.preventDefault();
      skipBackward();
      break;
    case "m":
      e.preventDefault();
      toggleMute();
      break;
    case "f":
      e.preventDefault();
      toggleFullScreen();
      break;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener("keydown", handleKeyDown);
  document.addEventListener("mouseup", stopDrag);
  document.addEventListener("mousemove", dragSeek);

  // Set initial volume
  if (videoRef.value) {
    videoRef.value.volume = volume.value;
  }
});

onBeforeUnmount(() => {
  document.removeEventListener("keydown", handleKeyDown);
  document.removeEventListener("mouseup", stopDrag);
  document.removeEventListener("mousemove", dragSeek);
  clearControlsTimer();
});

// Watch for external progress changes
watch(
  () => props.progress,
  (newProgress) => {
    if (!videoRef.value || isDragging.value) return;

    const targetTime = (newProgress / 100) * duration.value;
    if (Math.abs(targetTime - currentTime.value) > 1) {
      videoRef.value.currentTime = targetTime;
    }
  }
);
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div
    class="w-full relative bg-black overflow-hidden group"
    @mousemove="handleMouseMove"
    @mouseleave="handleMouseLeave"
  >
    <!-- Video element -->
    <video
      ref="videoRef"
      :src="videoUrl"
      :poster="poster"
      class="w-full h-auto"
      preload="metadata"
      @timeupdate="onTimeUpdate"
      @loadedmetadata="onLoadedMetadata"
      @play="onPlay"
      @pause="onPause"
      @ended="onEnded"
      @click="togglePlay"
    />

    <!-- Play/Pause large center button (visible when paused) -->
    <div
      v-if="!isPlaying"
      class="absolute inset-0 flex items-center justify-center"
    >
      <button
        class="bg-indigo-600 bg-opacity-75 hover:bg-opacity-100 text-white rounded-full p-5 transition-all duration-200 transform hover:scale-110"
        @click="togglePlay"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12"
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
    </div>

    <!-- Video controls bar -->
    <div
      class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black to-transparent bg-opacity-50 text-white p-3 transition-opacity duration-300"
      :class="{
        'opacity-0': !showControls && isPlaying,
        'opacity-100': showControls || !isPlaying,
      }"
    >
      <!-- Progress bar -->
      <div
        ref="progressBarRef"
        class="w-full h-2 bg-gray-600 rounded-full mb-2 cursor-pointer relative"
        @click="seek"
        @mousedown="startDrag"
      >
        <div
          class="absolute top-0 left-0 h-full bg-indigo-600 rounded-full"
          :style="{ width: `${progressPercent}%` }"
        />
        <!-- Progress handle -->
        <div
          class="absolute top-1/2 transform -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-md cursor-pointer"
          :style="{ left: `calc(${progressPercent}% - 8px)` }"
          @mousedown.stop="startDrag"
        />
      </div>

      <!-- Controls row -->
      <div class="flex items-center justify-between">
        <!-- Left controls -->
        <div class="flex items-center space-x-2">
          <!-- Play/Pause -->
          <button class="p-1 hover:text-indigo-300" @click="togglePlay">
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

          <!-- Skip back 10s -->
          <button class="p-1 hover:text-indigo-300" @click="skipBackward">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z"
                clip-rule="evenodd"
              />
            </svg>
          </button>

          <!-- Skip forward 10s -->
          <button class="p-1 hover:text-indigo-300" @click="skipForward" />
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
              clip-rule="evenodd"
            />
          </svg>
          <!-- Volume slider -->
          <input
            type="range"
            min="0"
            max="1"
            step="0.1"
            :value="volume"
            class="w-0 md:w-20 appearance-none bg-gray-400 h-1 rounded-full opacity-0 md:group-hover:opacity-100 transition-all duration-300"
            @input="changeVolume"
          />
        </div>

        <!-- Time display -->
        <span class="text-sm hidden md:inline-block"
          >{{ formattedCurrentTime }} / {{ formattedDuration }}</span
        >
      </div>

      <!-- Right controls -->
      <div class="flex items-center space-x-3">
        <!-- Fullscreen toggle -->
        <button class="p-1 hover:text-indigo-300" @click="toggleFullScreen">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 110-2h4a1 1 0 011 1v4a1 1 0 11-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 112 0v1.586l2.293-2.293a1 1 0 011.414 1.414L6.414 15H8a1 1 0 110 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 110-2h1.586l-2.293-2.293a1 1 0 011.414-1.414L15 13.586V12a1 1 0 011-1z"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Video progress section for mobile -->
  <div
    class="absolute bottom-0 left-0 right-0 px-3 pb-16 md:hidden"
    :class="{
      'opacity-0': !showControls && isPlaying,
      'opacity-100': showControls || !isPlaying,
    }"
  >
    <div class="text-white text-xs flex justify-between">
      <span>{{ formattedCurrentTime }}</span>
      <span>{{ formattedDuration }}</span>
    </div>
  </div>

  <!-- Video progress display below player -->
  <div class="p-3 border-t border-gray-100">
    <div class="flex justify-between text-sm text-gray-600 mb-1">
      <span>Progress</span>
      <span>{{ Math.round(progressPercent) }}%</span>
    </div>
    <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
      <div
        class="h-full bg-indigo-600 transition-all duration-300"
        :style="{ width: `${progressPercent}%` }"
      />
    </div>
  </div>
</template>

<style scoped>
input[type="range"] {
  -webkit-appearance: none;
  appearance: none;
  height: 4px;
  background: #d1d5db;
  border-radius: 4px;
  outline: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  cursor: pointer;
  border: none;
}
</style>
