<!-- components/dashboard/classes/RecommendedClassCard.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

// Props
interface ClassInstructor {
  name: string;
  avatar: string;
}

interface ClassCourse {
  id: number;
  title: string;
  thumbnail: string;
}

interface RecommendedClass {
  id: number;
  title: string;
  instructor: ClassInstructor;
  course: ClassCourse;
  date: string;
  duration: number;
  tags: string[];
  matchScore: number;
  isRegistered: boolean;
}

interface Props {
  classData: RecommendedClass;
  isAnimationLoaded: boolean;
  animationDelay?: number;
  index?: number;
}

const props = withDefaults(defineProps<Props>(), {
  animationDelay: 0,
  index: 0,
});

// Emit events
const emit = defineEmits(["register"]);

// Local state
const isHovered = ref(false);
const isParticlesVisible = ref(false);
const particles = ref<
  {
    id: number;
    x: number;
    y: number;
    size: number;
    duration: number;
    color: string;
  }[]
>([]);

// Computed properties
const staggerDelay = computed(() => props.animationDelay + props.index * 100);
const matchColor = computed(() => {
  if (props.classData.matchScore >= 95) return "from-indigo-500 to-indigo-600";
  if (props.classData.matchScore >= 90) return "from-blue-500 to-blue-600";
  return "from-purple-500 to-purple-600";
});

// Format date helper
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", {
    weekday: "short",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  }).format(date);
};

// Register for class
const registerForClass = () => {
  emit("register", props.classData.id);
  generateParticles();
};

// Generate celebration particles on registration
const generateParticles = () => {
  const colors = ["#4f46e5", "#7c3aed", "#8b5cf6", "#2563eb"];
  const newParticles = [];

  // Create 30 particles with random properties
  for (let i = 0; i < 30; i++) {
    newParticles.push({
      id: i,
      x: 50 + (Math.random() * 30 - 15), // Centered with some randomness
      y: 50 + (Math.random() * 30 - 15), // Centered with some randomness
      size: 3 + Math.random() * 7,
      duration: 1 + Math.random() * 2,
      color: colors[Math.floor(Math.random() * colors.length)],
    });
  }

  particles.value = newParticles;
  isParticlesVisible.value = true;

  // Hide particles after animation completes
  setTimeout(() => {
    isParticlesVisible.value = false;
  }, 3000);
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-500 transform recommended-card"
    :class="{
      'transform-active': props.isAnimationLoaded,
      'hover-effect': isHovered,
    }"
    :style="{ '--delay': `${staggerDelay}ms` }"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Class image with floating elements -->
    <div class="relative overflow-hidden card-image-container">
      <!-- Main image -->
      <img
        :src="classData.course.thumbnail"
        :alt="classData.course.title"
        class="w-full h-40 object-cover card-image"
      />

      <!-- Animated gradient overlay -->
      <div class="absolute inset-0 image-overlay">
        <div
          class="absolute inset-0 bg-gradient-to-tr from-indigo-900/40 to-transparent"
        ></div>
      </div>

      <!-- Celebration particles on registration -->
      <div
        class="absolute inset-0 overflow-hidden pointer-events-none particles-container"
      >
        <div
          v-for="particle in particles"
          :key="particle.id"
          v-show="isParticlesVisible"
          class="absolute rounded-full particle"
          :style="{
            left: `${particle.x}%`,
            top: `${particle.y}%`,
            width: `${particle.size}px`,
            height: `${particle.size}px`,
            backgroundColor: particle.color,
            animationDuration: `${particle.duration}s`,
          }"
        ></div>
      </div>

      <!-- Match badge -->
      <div
        class="absolute top-3 right-3 px-2 py-1 bg-gradient-to-r text-white text-xs font-bold rounded-full flex items-center score-badge"
        :class="matchColor"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-3 w-3 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
          />
        </svg>
        <span class="score-text">{{ classData.matchScore }}% Match</span>
      </div>

      <!-- Duration badge -->
      <div
        class="absolute bottom-3 left-3 px-2 py-1 bg-black bg-opacity-70 text-white text-xs font-medium rounded duration-badge"
      >
        {{ classData.duration }} min
      </div>

      <!-- Floating icon elements -->
      <div class="absolute floating-elements pointer-events-none">
        <div class="floating-element element-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-indigo-100"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
            />
          </svg>
        </div>
        <div class="floating-element element-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-purple-100"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
            />
          </svg>
        </div>
        <div class="floating-element element-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 text-blue-100"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 10V3L4 14h7v7l9-11h-7z"
            />
          </svg>
        </div>
      </div>
    </div>

    <!-- Class content -->
    <div class="p-4 card-content">
      <!-- Date and time -->
      <div class="text-sm text-gray-600 mb-2 date-info">
        {{ formatDate(classData.date) }}
      </div>

      <!-- Class title -->
      <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 class-title">
        {{ classData.title }}
      </h3>

      <!-- Instructor -->
      <div class="flex items-center mb-3 instructor-info">
        <div class="avatar-container overflow-hidden rounded-full">
          <img
            :src="classData.instructor.avatar"
            :alt="classData.instructor.name"
            class="w-6 h-6 object-cover instructor-avatar"
          />
        </div>
        <span class="text-sm font-medium text-gray-700 ml-2">{{
          classData.instructor.name
        }}</span>
      </div>

      <!-- Tags -->
      <div class="flex flex-wrap gap-1 mb-4 tags-container">
        <span
          v-for="(tag, index) in classData.tags"
          :key="tag"
          class="px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-600 rounded-full tag-item"
          :style="{ '--tag-index': index }"
        >
          {{ tag }}
        </span>
      </div>

      <!-- Register button with animation -->
      <button
        v-if="!classData.isRegistered"
        @click="registerForClass"
        class="relative w-full py-2 text-white font-medium rounded-lg overflow-hidden register-button"
      >
        <div
          class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 button-bg"
        ></div>
        <span class="relative z-10">Register for Class</span>
      </button>
      <button
        v-else
        disabled
        class="w-full py-2 bg-green-100 text-green-700 font-medium rounded-lg cursor-default registered-button"
      >
        <span class="flex items-center justify-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
          Registered
        </span>
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Base card animations */
.recommended-card {
  transform: translateY(30px);
  opacity: 0;
}

.recommended-card.transform-active {
  transform: translateY(0);
  opacity: 1;
  transition: transform 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275),
    opacity 0.7s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  transition-delay: var(--delay);
}

.recommended-card.hover-effect {
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 15px 30px rgba(79, 70, 229, 0.15);
  border-color: rgba(99, 102, 241, 0.3);
}

/* Card image container and effects */
.card-image-container {
  position: relative;
  height: 160px;
}

.card-image {
  transition: transform 3s ease;
}

.hover-effect .card-image {
  transform: scale(1.1);
}

.image-overlay {
  transition: opacity 0.3s;
}

.hover-effect .image-overlay {
  opacity: 0.7;
}

/* Score badge animations */
.score-badge {
  transform: translateY(0);
  transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
    box-shadow 0.5s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.hover-effect .score-badge {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.score-text {
  display: inline-block;
  animation: scoreCount 2s forwards;
  animation-delay: calc(var(--delay) + 300ms);
}

@keyframes scoreCount {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

/* Duration badge animations */
.duration-badge {
  transform: translateY(0);
  transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
    background-color 0.3s ease;
}

.hover-effect .duration-badge {
  transform: translateY(-3px);
  background-color: rgba(79, 70, 229, 0.8);
}

/* Floating elements animations */
.floating-elements {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  opacity: 0;
  transition: opacity 0.5s;
}

.hover-effect .floating-elements {
  opacity: 1;
}

.floating-element {
  position: absolute;
  opacity: 0.7;
}

.element-1 {
  top: 25%;
  left: 15%;
  animation: float1 5s infinite ease-in-out;
}

.element-2 {
  top: 65%;
  right: 10%;
  animation: float2 7s infinite ease-in-out;
}

.element-3 {
  bottom: 15%;
  left: 35%;
  animation: float3 6s infinite ease-in-out;
}

@keyframes float1 {
  0%,
  100% {
    transform: translate(0, 0) rotate(0deg);
  }
  25% {
    transform: translate(-10px, 10px) rotate(-5deg);
  }
  50% {
    transform: translate(5px, -5px) rotate(5deg);
  }
  75% {
    transform: translate(10px, 5px) rotate(-3deg);
  }
}

@keyframes float2 {
  0%,
  100% {
    transform: translate(0, 0) rotate(0deg);
  }
  25% {
    transform: translate(10px, -5px) rotate(5deg);
  }
  50% {
    transform: translate(-5px, 10px) rotate(-5deg);
  }
  75% {
    transform: translate(-10px, -10px) rotate(8deg);
  }
}

@keyframes float3 {
  0%,
  100% {
    transform: translate(0, 0) rotate(0deg);
  }
  25% {
    transform: translate(5px, 10px) rotate(-8deg);
  }
  50% {
    transform: translate(-10px, -5px) rotate(5deg);
  }
  75% {
    transform: translate(10px, -10px) rotate(-3deg);
  }
}

/* Card content animations */
.card-content {
  position: relative;
  z-index: 1;
  background: rgba(255, 255, 255, 0.98);
  transition: transform 0.3s;
}

.hover-effect .card-content {
  transform: translateY(-2px);
}

/* Date info animations */
.date-info {
  position: relative;
  display: inline-block;
  transform: translateY(0);
  transition: transform 0.3s, color 0.3s;
}

.hover-effect .date-info {
  transform: translateY(-2px);
  color: #4f46e5;
}

.date-info::after {
  content: "";
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 1px;
  background-color: #4f46e5;
  transition: width 0.3s ease;
}

.hover-effect .date-info::after {
  width: 100%;
}

/* Class title animations */
.class-title {
  position: relative;
  transition: color 0.3s;
}

.hover-effect .class-title {
  color: #4f46e5;
}

/* Instructor info animations */
.instructor-info {
  transform: translateX(0);
  transition: transform 0.3s;
}

.hover-effect .instructor-info {
  transform: translateX(3px);
}

.avatar-container {
  overflow: hidden;
  transition: box-shadow 0.3s;
}

.hover-effect .avatar-container {
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.3);
}

.instructor-avatar {
  transition: transform 0.5s;
}

.hover-effect .instructor-avatar {
  transform: scale(1.1);
}

/* Tags animations */
.tags-container {
  transform: translateY(0);
  transition: transform 0.3s;
}

.hover-effect .tags-container {
  transform: translateY(-2px);
}

.tag-item {
  opacity: 0;
  transform: translateY(10px);
  animation: tagFadeIn 0.5s forwards;
  animation-delay: calc(var(--delay) + 200ms + (var(--tag-index) * 100ms));
  transition: transform 0.3s, background-color 0.3s, color 0.3s, box-shadow 0.3s;
}

.hover-effect .tag-item {
  transform: translateY(-1px);
  background-color: rgba(224, 231, 255, 0.6);
  color: #4338ca;
  box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

@keyframes tagFadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Register button animations */
.register-button {
  transform: translateY(0);
  transition: transform 0.3s, box-shadow 0.3s;
}

.hover-effect .register-button {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.button-bg {
  background-size: 200% 100%;
  transition: background-position 0.5s;
}

.hover-effect .button-bg {
  animation: gradientShift 2s infinite alternate;
}

@keyframes gradientShift {
  0% {
    background-position: 0% 50%;
  }
  100% {
    background-position: 100% 50%;
  }
}

.register-button::after {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.3) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: rotate(30deg) translateY(100%);
  opacity: 0;
  z-index: 10;
  transition: opacity 0.3s, transform 0.3s;
}

.hover-effect .register-button::after {
  opacity: 1;
  animation: buttonSweep 2s infinite;
}

@keyframes buttonSweep {
  0% {
    transform: rotate(30deg) translateY(100%);
  }
  100% {
    transform: rotate(30deg) translateY(-100%);
  }
}

/* Registered button animation */
.registered-button {
  position: relative;
  overflow: hidden;
  animation: successPulse 2s infinite;
}

@keyframes successPulse {
  0%,
  100% {
    background-color: rgba(220, 252, 231, 0.8);
  }
  50% {
    background-color: rgba(220, 252, 231, 1);
  }
}

/* Particles animation for celebration */
.particle {
  position: absolute;
  border-radius: 50%;
  opacity: 0;
  animation: particleExplode 2s ease-out forwards;
}

@keyframes particleExplode {
  0% {
    transform: translate(0, 0) scale(0);
    opacity: 1;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translate(
        calc((random(200) - 100) * 1px),
        calc((random(200) - 100) * 1px)
      )
      scale(1);
    opacity: 0;
  }
}

/* Line clamping for multiline text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
