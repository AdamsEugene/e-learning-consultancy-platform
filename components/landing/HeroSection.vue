<script lang="ts" setup>
// Typing animation configuration
const phrases = [
  "Transform your skills with expert courses",
  "Get personalized expert consultancy",
  "Learn and grow with industry experts",
  "Master new skills at your pace",
];

// Animation props
interface Props {
  isFloating: boolean;
  parallaxOffset: number;
}

defineProps<Props>();

// Animation state
const currentPhraseIndex = ref(0);
const typedText = ref("");
const isTyping = ref(true); // True when typing, false when deleting
const isDeleting = ref(false);
const isPaused = ref(false);

// Animation timing (in milliseconds)
const typingSpeed = 50; // Typing speed
const deletingSpeed = 25; // Deleting speed
const pauseBeforeDelete = 3000; // Pause time before showing completed text
const pauseBeforeNextPhrase = 1000; // Pause time before typing the next phrase

// Animation control
let typingInterval: number | null = null;
let currentIndex = 0;

const runTypingAnimation = () => {
  // Clear any existing interval
  if (typingInterval) {
    clearInterval(typingInterval);
  }

  typingInterval = window.setInterval(
    () => {
      // Get the current phrase each time to ensure we're using the updated index
      const currentPhrase = phrases[currentPhraseIndex.value];

      // If we're paused, do nothing
      if (isPaused.value) return;

      // Handle typing phase
      if (!isDeleting.value) {
        // Still typing current phrase
        if (currentIndex < currentPhrase.length) {
          typedText.value += currentPhrase.charAt(currentIndex);
          currentIndex++;
          isTyping.value = true;
        }
        // Finished typing, pause before deleting
        else {
          isTyping.value = false;
          isPaused.value = true;

          console.log(`Completed typing phrase: "${currentPhrase}"`);

          setTimeout(() => {
            isPaused.value = false;
            isDeleting.value = true;
          }, pauseBeforeDelete);
        }
      }
      // Handle deleting phase
      else {
        // Still have text to delete
        if (typedText.value.length > 0) {
          typedText.value = typedText.value.substring(
            0,
            typedText.value.length - 1
          );
          isTyping.value = true;
        }
        // Done deleting, move to next phrase
        else {
          isTyping.value = false;
          isDeleting.value = false;
          isPaused.value = true;

          // Move to next phrase
          const oldIndex = currentPhraseIndex.value;
          currentPhraseIndex.value =
            (currentPhraseIndex.value + 1) % phrases.length;
          currentIndex = 0;

          console.log(
            `Moving from phrase ${oldIndex} to phrase ${currentPhraseIndex.value}`
          );

          // Pause before starting next phrase
          setTimeout(() => {
            isPaused.value = false;
          }, pauseBeforeNextPhrase);
        }
      }
    },
    isDeleting.value ? deletingSpeed : typingSpeed
  );
};

onMounted(() => {
  runTypingAnimation();
});

onBeforeUnmount(() => {
  if (typingInterval) {
    clearInterval(typingInterval);
  }
});

// Emit animation completed event when first phrase is fully typed
watch([isTyping, currentPhraseIndex], ([newIsTyping, phraseIndex]) => {
  // Emit when we finish typing the first phrase
  if (!newIsTyping && !isDeleting.value && phraseIndex === 0) {
    console.log("Emitting typingCompleted event");
    setTimeout(() => {
      emit("typingCompleted");
    }, pauseBeforeDelete / 2);
  }
});

// Define events
const emit = defineEmits(["typingCompleted"]);

// Image paths
const imageFolder = "/svg/";
const waveSvg = `${imageFolder}wave.svg`;
const circlesSvg = `${imageFolder}circles.svg`;
const dashboardSvg = `${imageFolder}dashboard.svg`;
const educationSvg = `${imageFolder}education.svg`;
const consultancySvg = `${imageFolder}consultancy.svg`;
const badgesSvg = `${imageFolder}badges.svg`;
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section class="hero relative min-h-screen flex items-center overflow-hidden">
    <!-- Background gradient and animated patterns -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-800 z-0"
      :style="{ transform: `translateY(${parallaxOffset}px)` }"
    >
      <!-- Animated background patterns -->
      <div class="absolute inset-0">
        <!-- SVG Circles background -->
        <img
          :src="circlesSvg"
          class="absolute inset-0 w-full h-full object-cover"
          alt=""
        />

        <!-- Wave SVG -->
        <img :src="waveSvg" class="absolute bottom-0 left-0 w-full" alt="" />
      </div>

      <!-- Floating consultancy illustration -->
      <div
        class="absolute top-12 right-12 z-0 transform scale-50 origin-top-right opacity-0"
        :class="{ 'opacity-60 float': isFloating }"
        style="transition-delay: 600ms; animation-delay: 1s"
      >
        <img :src="consultancySvg" class="w-full" alt="" />
      </div>

      <!-- Floating Education SVG -->
      <div
        class="hidden lg:block absolute top-12 right-1/3 transform -translate-x-1/4 translate-y-1/4 opacity-50 pointer-events-none"
      >
        <img :src="educationSvg" class="w-64 h-64" alt="" />
      </div>
    </div>

    <div class="container mx-auto px-4 relative z-10 py-20">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="md:w-1/2 mb-12 md:mb-0 text-white">
          <div class="mb-6 inline-block">
            <span
              class="bg-white bg-opacity-20 backdrop-blur-sm text-indigo-900 text-sm font-bold px-4 py-2 rounded-full shadow-lg flex items-center"
            >
              <svg
                class="w-4 h-4 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                />
              </svg>
              Learn & Grow with Us
            </span>
          </div>
          <h1
            class="text-4xl md:text-5xl lg:text-6xl font-bold mb-8 leading-tight h-[5.5rem] flex items-center my-3"
          >
            <span class="typed-text">{{ typedText }}</span>
            <span class="cursor" :class="{ typing: isTyping }">|</span>
          </h1>
          <p class="text-xl mb-8 opacity-90 md:pr-12 leading-relaxed">
            Elevate your career with our dual-focused platform: Learn in-demand
            skills through interactive courses and get personalized consultancy
            from industry experts.
          </p>
          <div class="flex flex-wrap gap-4">
            <a
              href="#courses"
              class="bg-white text-indigo-800 font-bold py-4 px-8 rounded-lg hover:bg-opacity-90 transition-all transform hover:scale-105 shadow-xl flex items-center group"
            >
              <svg
                class="w-5 h-5 mr-2 group-hover:animate-pulse-fast"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
                />
              </svg>
              Explore Courses
            </a>
            <a
              href="#consultancy"
              class="border-2 border-white text-white font-bold py-4 px-8 rounded-lg hover:bg-white hover:text-indigo-800 transition-all duration-300 flex items-center group"
            >
              <svg
                class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"
                />
              </svg>
              Get Consultancy
            </a>
          </div>

          <!-- Trust indicators -->
          <div
            class="mt-10 flex items-center p-3 rounded-xl bg-white bg-opacity-10 backdrop-blur-md"
          >
            <div class="flex -space-x-2">
              <img
                v-for="i in 4"
                :key="i"
                :src="`/images/instructors/i${i}.jpg`"
                class="w-10 h-10 rounded-full border-2 border-indigo-500 object-cover"
              />
            </div>
            <div class="ml-4">
              <div class="text-yellow-400 flex">
                <svg
                  v-for="i in 5"
                  :key="i"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                  />
                </svg>
              </div>
              <p class="text-sm text-indigo-900">
                Trusted by over <span class="font-bold">15,000</span> satisfied
                students
              </p>
            </div>
          </div>
        </div>

        <div class="md:w-1/2 relative">
          <div
            class="relative rounded-xl overflow-hidden shadow-2xl transform transition-all duration-700"
            :class="{
              'hover:rotate-2': isFloating,
              'hover:scale-105': isFloating,
            }"
          >
            <!-- Dashboard SVG -->
            <img
              :src="dashboardSvg"
              alt="Learning Platform Dashboard"
              class="w-full h-auto relative z-10"
            />

            <!-- Floating badges -->
            <div
              class="absolute -top-8 -right-8 z-20 transform scale-50 origin-bottom-left"
            >
              <img :src="badgesSvg" class="w-full h-auto" alt="" />
            </div>

            <!-- Live user counter -->
            <div
              class="absolute bottom-4 left-4 bg-black bg-opacity-70 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium animate-pulse z-20"
            >
              <span
                class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"
              />
              <span class="inline-flex items-center">
                <span class="mr-1 font-bold">257</span> people learning right
                now
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.cursor {
  display: inline-block;
  animation: blink 1s infinite;
}

.cursor.typing {
  animation: none;
}

@keyframes blink {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
}

@keyframes float {
  0% {
    transform: translateY(0px) scale(0.5);
  }
  50% {
    transform: translateY(-15px) scale(0.5);
  }
  100% {
    transform: translateY(0px) scale(0.5);
  }
}

.float {
  animation: float 6s ease-in-out infinite;
}

@keyframes pulse-slow {
  0% {
    transform: scale(0.95);
    opacity: 0.7;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.9;
  }
  100% {
    transform: scale(0.95);
    opacity: 0.7;
  }
}

.animate-pulse-slow {
  animation: pulse-slow 8s infinite;
}

@keyframes pulse-fast {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse-fast {
  animation: pulse-fast 1s infinite;
}
</style>
