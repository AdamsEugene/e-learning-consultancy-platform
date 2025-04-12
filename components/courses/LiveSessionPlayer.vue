<!-- components/courses/LiveSessionPlayer.vue -->
<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps<{
  videoUrl: string;
  posterUrl?: string;
  isLive: boolean;
  autoPlay?: boolean;
}>();

const emit = defineEmits<{
  (e: 'volume-change', volume: number): void;
  (e: 'fullscreen-toggle', isFullscreen: boolean): void;
  (e: 'error', message: string): void;
}>();

// Refs
const videoRef = ref<HTMLVideoElement | null>(null);
const containerRef = ref<HTMLDivElement | null>(null);
const isPlaying = ref(false);
const isMuted = ref(false);
const volume = ref(1);
const isFullscreen = ref(false);
const showControls = ref(true);
const controlsTimeout = ref<number | null>(null);
const loadError = ref(false);
const errorMessage = ref('');

// Video related state
const currentTime = ref(0);
const duration = ref(0);
const buffered = ref(0);
const connectionQuality = ref<'good' | 'medium' | 'poor'>('good');

// Handle play/pause
const togglePlay = () => {
  if (!videoRef.value) return;
  
  if (videoRef.value.paused) {
    videoRef.value.play()
      .then(() => {
        isPlaying.value = true;
      })
      .catch(err => {
        console.error('Error playing video:', err);
        errorMessage.value = 'Unable to play video. Please try again.';
        loadError.value = true;
        emit('error', errorMessage.value);
      });
  } else {
    videoRef.value.pause();
    isPlaying.value = false;
  }
};

// Handle mute/unmute
const toggleMute = () => {
  if (!videoRef.value) return;
  
  isMuted.value = !isMuted.value;
  videoRef.value.muted = isMuted.value;
};

// Handle volume change
const handleVolumeChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (!videoRef.value || !target) return;
  
  const newVolume = parseFloat(target.value);
  volume.value = newVolume;
  videoRef.value.volume = newVolume;
  
  // If volume is 0, mute the video
  if (newVolume === 0) {
    isMuted.value = true;
    videoRef.value.muted = true;
  } else if (isMuted.value) {
    // If volume is raised from 0, unmute
    isMuted.value = false;
    videoRef.value.muted = false;
  }
  
  emit('volume-change', newVolume);
};

// Handle fullscreen toggle
const toggleFullscreen = () => {
  if (!containerRef.value) return;
  
  if (!document.fullscreenElement) {
    containerRef.value.requestFullscreen()
      .then(() => {
        isFullscreen.value = true;
        emit('fullscreen-toggle', true);
      })
      .catch(err => {
        console.error('Error attempting to enable fullscreen:', err);
      });
  } else {
    document.exitFullscreen()
      .then(() => {
        isFullscreen.value = false;
        emit('fullscreen-toggle', false);
      })
      .catch(err => {
        console.error('Error attempting to exit fullscreen:', err);
      });
  }
};

// Format time in MM:SS format
const formatTime = (seconds: number): string => {
  if (isNaN(seconds)) return '00:00';
  
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = Math.floor(seconds % 60);
  
  return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
};

// Show/hide controls based on mouse movement
const showControlsTemporarily = () => {
  showControls.value = true;
  
  // Clear any existing timeout
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
  }
  
  // Set a new timeout to hide controls after 3 seconds
  controlsTimeout.value = window.setTimeout(() => {
    if (isPlaying.value) {
      showControls.value = false;
    }
  }, 3000);
};

// Simulate network conditions (for demo purposes)
const simulateNetworkConditions = () => {
  // Randomly select network quality every 20 seconds
  setInterval(() => {
    const random = Math.random();
    if (random < 0.7) {
      connectionQuality.value = 'good';
    } else if (random < 0.9) {
      connectionQuality.value = 'medium';
    } else {
      connectionQuality.value = 'poor';
    }
  }, 20000);
};

// Video event handlers
const handleTimeUpdate = () => {
  if (!videoRef.value) return;
  currentTime.value = videoRef.value.currentTime;
};

const handleDurationChange = () => {
  if (!videoRef.value) return;
  duration.value = videoRef.value.duration;
};

const handleProgress = () => {
  if (!videoRef.value || videoRef.value.buffered.length === 0) return;
  buffered.value = videoRef.value.buffered.end(videoRef.value.buffered.length - 1);
};

const handleError = () => {
  if (!videoRef.value) return;
  
  loadError.value = true;
  errorMessage.value = 'Error loading video. Please try again later.';
  emit('error', errorMessage.value);
};

const handleEnded = () => {
  isPlaying.value = false;
};

// Handle keyboard shortcuts
const handleKeydown = (event: KeyboardEvent) => {
  if (!videoRef.value) return;
  
  switch (event.key.toLowerCase()) {
    case ' ':
    case 'k':
      // Space or K to play/pause
      event.preventDefault();
      togglePlay();
      break;
    case 'f':
      // F for fullscreen
      event.preventDefault();
      toggleFullscreen();
      break;
    case 'm':
      // M for mute/unmute
      event.preventDefault();
      toggleMute();
      break;
    case 'arrowup':
      // Up arrow to increase volume
      event.preventDefault();
      if (videoRef.value.volume < 1) {
        volume.value = Math.min(1, videoRef.value.volume + 0.1);
        videoRef.value.volume = volume.value;
        if (isMuted.value) {
          isMuted.value = false;
          videoRef.value.muted = false;
        }
      }
      break;
    case 'arrowdown':
      // Down arrow to decrease volume
      event.preventDefault();
      if (videoRef.value.volume > 0) {
        volume.value = Math.max(0, videoRef.value.volume - 0.1);
        videoRef.value.volume = volume.value;
        if (volume.value === 0) {
          isMuted.value = true;
          videoRef.value.muted = true;
        }
      }
      break;
  }
};

// Reset video player
const resetPlayer = () => {
  if (!videoRef.value) return;
  
  // Reset state
  isPlaying.value = false;
  isMuted.value = false;
  volume.value = 1;
  currentTime.value = 0;
  duration.value = 0;
  buffered.value = 0;
  loadError.value = false;
  errorMessage.value = '';
  
  // Reset video properties
  videoRef.value.currentTime = 0;
  videoRef.value.volume = 1;
  videoRef.value.muted = false;
};

// Watch for videoUrl changes
watch(() => props.videoUrl, () => {
  resetPlayer();
  
  // If autoPlay is enabled, attempt to play the video
  if (props.autoPlay && videoRef.value) {
    videoRef.value.play()
      .then(() => {
        isPlaying.value = true;
      })
      .catch(err => {
        console.error('Error auto-playing video:', err);
      });
  }
});

// Lifecycle hooks
onMounted(() => {
  // Add keyboard event listener
  window.addEventListener('keydown', handleKeydown);
  
  // Initialize video
  if (videoRef.value) {
    videoRef.value.volume = volume.value;
    
    // If autoPlay is enabled, attempt to play the video
    if (props.autoPlay) {
      videoRef.value.play()
        .then(() => {
          isPlaying.value = true;
        })
        .catch(err => {
          console.error('Error auto-playing video:', err);
        });
    }
  }
  
  // For demo purposes, simulate varying network conditions
  if (props.isLive) {
    simulateNetworkConditions();
  }
});

onBeforeUnmount(() => {
  // Remove keyboard event listener
  window.removeEventListener('keydown', handleKeydown);
  
  // Clear any timeouts
  if (controlsTimeout.value) {
    clearTimeout(controlsTimeout.value);
  }
});
</script>

<template>
  <div 
    ref="containerRef" 
    class="live-session-player relative bg-black"
    @mousemove="showControlsTemporarily"
    @click="showControls = true"
  >
    <!-- Main video element -->
    <video
      ref="videoRef"
      :src="videoUrl"
      :poster="posterUrl"
      class="w-full h-full"
      @timeupdate="handleTimeUpdate"
      @durationchange="handleDurationChange"
      @progress="handleProgress"
      @error="handleError"
      @ended="handleEnded"
      @play="isPlaying = true"
      @pause="isPlaying = false"
    />
    
    <!-- Live indicator (red dot) -->
    <div v-if="isLive" class="absolute top-4 left-4 z-10 flex items-center space-x-2 bg-black bg-opacity-50 px-2 py-1 rounded-md">
      <span class="flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-red-400 opacity-75"/>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"/>
      </span>
      <span class="text-white text-sm font-medium">LIVE</span>
    </div>
    
    <!-- Connection quality indicator (for live streams) -->
    <div v-if="isLive" class="absolute top-4 right-4 z-10">
      <div class="flex items-center bg-black bg-opacity-50 px-2 py-1 rounded-md space-x-1">
        <div 
          class="h-2 w-2 rounded-full"
          :class="{
            'bg-green-500': connectionQuality === 'good',
            'bg-yellow-500': connectionQuality === 'medium',
            'bg-red-500': connectionQuality === 'poor'
          }"
        />
        <span class="text-white text-xs">
          {{ connectionQuality === 'good' ? 'Good' : connectionQuality === 'medium' ? 'Fair' : 'Poor' }}
        </span>
      </div>
    </div>
    
    <!-- Video controls -->
    <div 
      class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent px-4 py-2 transition-opacity duration-300"
      :class="{ 'opacity-0': !showControls && isPlaying, 'opacity-100': showControls || !isPlaying }"
    >
      <!-- Video progress bar -->
      <div class="relative w-full h-1 bg-gray-600 rounded-full cursor-pointer mb-2">
        <!-- Buffered progress -->
        <div 
          class="absolute h-full bg-gray-400 rounded-full"
          :style="{ width: `${(buffered / Math.max(duration, 1)) * 100}%` }"
        />
        
        <!-- Playback progress -->
        <div 
          class="absolute h-full bg-indigo-500 rounded-full"
          :style="{ width: `${(currentTime / Math.max(duration, 1)) * 100}%` }"
        />
        
        <!-- Seek handle -->
        <div 
          class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full -ml-1.5 shadow-md"
          :style="{ left: `${(currentTime / Math.max(duration, 1)) * 100}%` }"
        />
      </div>
      
      <!-- Controls row -->
      <div class="flex items-center justify-between">
        <!-- Left controls group -->
        <div class="flex items-center space-x-4">
          <!-- Play/Pause button -->
          <button 
            class="text-white focus:outline-none hover:text-indigo-300 transition-colors" 
            :aria-label="isPlaying ? 'Pause' : 'Play'"
            @click="togglePlay"
          >
            <!-- Play icon -->
            <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
            </svg>
            
            <!-- Pause icon -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
          
          <!-- Volume control -->
          <div class="flex items-center">
            <button 
              class="text-white focus:outline-none hover:text-indigo-300 transition-colors mr-2" 
              :aria-label="isMuted ? 'Unmute' : 'Mute'"
              @click="toggleMute"
            >
              <!-- Volume off icon -->
              <svg v-if="isMuted || volume === 0" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
              
              <!-- Volume low icon -->
              <svg v-else-if="volume < 0.5" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
              
              <!-- Volume high icon -->
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <!-- Volume slider (hidden on mobile) -->
            <input
              type="range"
              min="0"
              max="1"
              step="0.01"
              :value="volume"
              class="w-16 hidden sm:block"
              @input="handleVolumeChange"
            >
          </div>
          
          <!-- Time display -->
          <div class="text-white text-sm hidden sm:block">
            {{ formatTime(currentTime) }} / {{ isLive ? 'LIVE' : formatTime(duration) }}
          </div>
        </div>
        
        <!-- Right controls group -->
        <div class="flex items-center space-x-4">
          <!-- Fullscreen button -->
          <button 
            class="text-white focus:outline-none hover:text-indigo-300 transition-colors" 
            :aria-label="isFullscreen ? 'Exit Fullscreen' : 'Enter Fullscreen'"
            @click="toggleFullscreen"
          >
            <!-- Enter fullscreen icon -->
            <svg v-if="!isFullscreen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            
            <!-- Exit fullscreen icon -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 4a1 1 0 00-1 1v4a1 1 0 01-2 0V5a3 3 0 013-3h4a1 1 0 010 2H5zm10 8a1 1 0 001-1V7a1 1 0 112 0v4a3 3 0 01-3 3h-4a1 1 0 010-2h4z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Error overlay -->
    <div 
      v-if="loadError" 
      class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 z-20"
    >
      <div class="text-center p-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-white mb-4">{{ errorMessage }}</p>
        <button 
          class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors" 
          @click="resetPlayer"
        >
          Retry
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom styles for volume slider */
input[type="range"] {
  -webkit-appearance: none;
  height: 4px;
  background: rgba(255, 255, 255, 0.6);
  border-radius: 5px;
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

/* Keyboard shortcut hints */
.key-hint {
  display: inline-block;
  min-width: 20px;
  height: 20px;
  padding: 0 4px;
  font-size: 12px;
  line-height: 20px;
  text-align: center;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
  margin-left: 4px;
}
</style>