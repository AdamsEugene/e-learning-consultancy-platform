<!-- components/dashboard/classes/ClassCard.vue -->
<script setup lang="ts">
import { ref, computed } from "vue";

// Props
interface ClassInstructor {
  name: string;
  avatar: string;
  bio?: string;
}

interface ClassCourse {
  id: number;
  title: string;
  thumbnail: string;
}

interface ClassMaterial {
  type: string;
  name: string;
  url: string;
  size: string;
}

interface ClassData {
  id: number;
  title: string;
  instructor: ClassInstructor;
  course: ClassCourse;
  date: string;
  endDate?: string;
  duration: number;
  description?: string;
  meetingUrl?: string;
  materials?: ClassMaterial[];
  status: string;
  attendees: number;
  maxAttendees: number;
  isRegistered: boolean;
  hasReminder?: boolean;
  tags: string[];
  recordingAvailable?: boolean;
  recordingUrl?: string;
}

interface Props {
  classData: ClassData;
  isAnimationLoaded: boolean;
  animationDelay?: number;
  index?: number;
}

const props = withDefaults(defineProps<Props>(), {
  animationDelay: 0,
  index: 0,
});

// Emit events
const emit = defineEmits([
  "toggle-reminder",
  "register",
  "select-tag",
  "view-details",
]);

// Local state
const isExpanded = ref(false);
const isHovered = ref(false);
const showAttendeeAnimation = ref(false);
const showTimeAnimation = ref(false);

// Animations for entrance
const baseAnimationDuration = 500; // ms
const staggerDelay = computed(() => props.animationDelay + props.index * 70);

// Toggle details expansion
const toggleDetails = () => {
  isExpanded.value = !isExpanded.value;
  emit("view-details", props.classData.id);
};

// Register for class
const registerForClass = () => {
  emit("register", props.classData.id);
};

// Toggle reminder
const toggleReminder = (event: Event) => {
  event.stopPropagation();
  emit("toggle-reminder", props.classData.id);
};

// Select tag for filtering
const selectTag = (tag: string, event: Event) => {
  event.stopPropagation();
  emit("select-tag", tag);
};

// Format date helper - more readable format for display
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

// Format time remaining helper
const getTimeRemaining = (dateString: string) => {
  const now = new Date();
  const classDate = new Date(dateString);
  const diffMs = classDate.getTime() - now.getTime();

  if (diffMs <= 0) return "Starting soon";

  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor(
    (diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );

  if (diffDays > 0) {
    return `in ${diffDays}d ${diffHours}h`;
  }

  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

  if (diffHours > 0) {
    return `in ${diffHours}h ${diffMinutes}m`;
  }

  return `in ${diffMinutes}m`;
};

// Get class status with specific styling
const getClassStatus = (cls) => {
  if (cls.status === "completed") {
    return {
      text: "Completed",
      bgColor: "bg-gray-100",
      textColor: "text-gray-700",
    };
  }

  const now = new Date();
  const classDate = new Date(cls.date);
  const diffMs = classDate.getTime() - now.getTime();

  if (diffMs <= 0 && diffMs > -cls.duration * 60 * 1000) {
    return {
      text: "Live now",
      bgColor: "bg-red-100",
      textColor: "text-red-700",
    };
  }

  if (diffMs <= 15 * 60 * 1000 && diffMs > 0) {
    return {
      text: "Starting soon",
      bgColor: "bg-yellow-100",
      textColor: "text-yellow-700",
    };
  }

  if (cls.isRegistered) {
    return {
      text: "Registered",
      bgColor: "bg-green-100",
      textColor: "text-green-700",
    };
  }

  return {
    text: "Open",
    bgColor: "bg-blue-100",
    textColor: "text-blue-700",
  };
};

// Animation triggers
setTimeout(() => {
  showAttendeeAnimation.value = true;
}, staggerDelay.value + 400);

setTimeout(() => {
  showTimeAnimation.value = true;
}, staggerDelay.value + 600);
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-700 relative class-card-container"
    :class="{
      'border-indigo-300 shadow-md': isExpanded,
      'transform-active': props.isAnimationLoaded,
      'hover-effect': isHovered,
    }"
    :style="{
      '--delay': `${staggerDelay}ms`,
      '--index': props.index,
    }"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Animated background gradient -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-indigo-50/40 via-purple-50/40 to-indigo-50/40 background-shine"
    ></div>

    <!-- Class card header -->
    <div class="p-4 cursor-pointer relative z-10" @click="toggleDetails">
      <div class="flex flex-col sm:flex-row sm:items-center">
        <!-- Left: Course image and info -->
        <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-4">
          <div class="relative course-image-container overflow-hidden">
            <img
              :src="classData.course.thumbnail"
              :alt="classData.course.title"
              class="w-full sm:w-40 h-24 object-cover rounded-lg shadow-sm"
            />
            <div
              class="absolute inset-0 bg-gradient-to-tr from-indigo-900/20 to-transparent"
            ></div>
            <div
              class="absolute bottom-2 right-2 px-2 py-1 bg-black bg-opacity-70 rounded text-xs text-white font-medium duration-badge"
            >
              {{ classData.duration }} min
            </div>

            <!-- Animated overlay on hover -->
            <div
              class="absolute inset-0 bg-indigo-600/20 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"
            >
              <span
                class="bg-white/90 text-indigo-600 text-xs font-bold py-1 px-3 rounded-full transform hover:scale-105 transition-transform"
              >
                View details
              </span>
            </div>
          </div>
        </div>

        <!-- Middle: Class info -->
        <div class="flex-grow">
          <div class="flex flex-wrap justify-between items-start">
            <div>
              <div class="flex items-center mb-1">
                <!-- Status badge -->
                <span
                  :class="[
                    getClassStatus(classData).bgColor,
                    getClassStatus(classData).textColor,
                    'text-xs font-bold px-2 py-0.5 rounded-full mr-2 status-badge',
                  ]"
                >
                  {{ getClassStatus(classData).text }}
                </span>

                <!-- Date and time -->
                <span
                  class="text-sm text-gray-600 time-info"
                  :class="{ 'time-info-animated': showTimeAnimation }"
                >
                  {{ formatDate(classData.date) }} ·
                  {{ getTimeRemaining(classData.date) }}
                </span>
              </div>

              <!-- Class title with animation -->
              <h3 class="text-lg font-bold text-gray-900 mb-1 class-title">
                {{ classData.title }}
              </h3>

              <!-- Course name -->
              <p class="text-sm text-gray-600 mb-2 course-name">
                {{ classData.course.title }}
              </p>

              <!-- Instructor info -->
              <div class="flex items-center instructor-info">
                <div class="instructor-avatar-container">
                  <img
                    :src="classData.instructor.avatar"
                    :alt="classData.instructor.name"
                    class="w-6 h-6 rounded-full object-cover mr-2 instructor-avatar"
                  />
                </div>
                <span class="text-sm font-medium text-gray-700">{{
                  classData.instructor.name
                }}</span>
              </div>
            </div>

            <!-- Registration info and capacity -->
            <div class="mt-2 sm:mt-0 flex flex-col items-end attendance-info">
              <div
                class="text-sm text-gray-600 mb-1 attendance-count"
                :class="{ 'count-animated': showAttendeeAnimation }"
              >
                {{ classData.attendees }}/{{
                  classData.maxAttendees
                }}
                registered
              </div>

              <!-- Progress bar with animation -->
              <div
                class="w-28 h-2 bg-gray-200 rounded-full overflow-hidden progress-container"
              >
                <div
                  class="h-full rounded-full progress-bar"
                  :class="{ 'progress-bar-animated': props.isAnimationLoaded }"
                  :style="{
                    '--progress': `${
                      (classData.attendees / classData.maxAttendees) * 100
                    }%`,
                    '--progress-delay': `${staggerDelay + 300}ms`,
                  }"
                ></div>
              </div>

              <!-- Expand/collapse indicator -->
              <div
                class="mt-3 text-sm font-medium text-indigo-600 flex items-center expand-indicator"
              >
                {{ isExpanded ? "Show less" : "Show details" }}
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1 transition-transform duration-300"
                  :class="{ 'rotate-180': isExpanded }"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Class details section (expanded view) -->
    <div
      v-if="isExpanded"
      class="p-4 border-t border-gray-200 bg-gray-50 details-container"
    >
      <div class="mb-4 details-section">
        <h4 class="text-sm font-bold text-gray-700 mb-2 details-header">
          Description
        </h4>
        <p class="text-sm text-gray-600 details-text">
          {{ classData.description }}
        </p>
      </div>

      <!-- Topic tags -->
      <div class="mb-4 details-section">
        <h4 class="text-sm font-bold text-gray-700 mb-2 details-header">
          Topics
        </h4>
        <div class="flex flex-wrap gap-2 tags-container">
          <span
            v-for="(tag, tagIndex) in classData.tags"
            :key="tag"
            class="px-3 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200 transition-colors cursor-pointer tag-item"
            :style="{ '--tag-index': tagIndex }"
            @click="selectTag(tag, $event)"
          >
            {{ tag }}
          </span>
        </div>
      </div>

      <!-- Class materials -->
      <div
        class="mb-4 details-section"
        v-if="classData.materials && classData.materials.length"
      >
        <h4 class="text-sm font-bold text-gray-700 mb-2 details-header">
          Materials
        </h4>
        <div class="space-y-2 materials-container">
          <div
            v-for="(material, materialIndex) in classData.materials"
            :key="material.name"
            class="flex items-center p-2 bg-white rounded border border-gray-200 text-sm material-item"
            :style="{ '--material-index': materialIndex }"
          >
            <!-- File type icon -->
            <span
              class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-indigo-100 text-indigo-600 mr-3 material-icon"
            >
              <svg
                v-if="material.type === 'pdf'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else-if="material.type === 'code'"
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>

            <!-- File info -->
            <div class="flex-grow">
              <div class="font-medium text-gray-800">{{ material.name }}</div>
              <div class="text-xs text-gray-500">{{ material.size }}</div>
            </div>

            <!-- Download button -->
            <a
              :href="material.url"
              class="ml-2 flex-shrink-0 p-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors material-download"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Instructor info -->
      <div class="mb-4 details-section">
        <h4 class="text-sm font-bold text-gray-700 mb-2 details-header">
          About the Instructor
        </h4>
        <div class="flex instructor-profile">
          <img
            :src="classData.instructor.avatar"
            :alt="classData.instructor.name"
            class="w-10 h-10 rounded-full object-cover mr-3 instructor-profile-avatar"
          />
          <div>
            <div class="font-medium text-gray-800">
              {{ classData.instructor.name }}
            </div>
            <p class="text-sm text-gray-600">{{ classData.instructor.bio }}</p>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="flex flex-wrap gap-2 mt-4 action-buttons">
        <!-- Join button (if registered) -->
        <button
          v-if="classData.isRegistered"
          class="px-4 py-2 font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center action-button join-button"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
            />
          </svg>
          Join Class
        </button>

        <!-- Register button (if not registered) -->
        <button
          v-else
          class="px-4 py-2 font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors action-button register-button"
          @click="registerForClass"
        >
          Register
        </button>

        <!-- Reminder toggle button -->
        <button
          v-if="classData.isRegistered"
          class="px-4 py-2 font-medium rounded-lg border transition-colors flex items-center action-button reminder-button"
          :class="
            classData.hasReminder
              ? 'bg-yellow-100 text-yellow-700 border-yellow-300 hover:bg-yellow-200'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
          "
          @click="toggleReminder"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              v-if="classData.hasReminder"
              d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
            />
            <path
              v-else
              fill-rule="evenodd"
              d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
              clip-rule="evenodd"
            />
          </svg>
          {{ classData.hasReminder ? "Reminder Set" : "Set Reminder" }}
        </button>

        <!-- Share button -->
        <button
          class="px-4 py-2 font-medium bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition-colors flex items-center action-button share-button"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
            />
          </svg>
          Share
        </button>

        <!-- Add to calendar -->
        <button
          v-if="classData.isRegistered"
          class="px-4 py-2 font-medium bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-100 transition-colors flex items-center action-button calendar-button"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
              clip-rule="evenodd"
            />
          </svg>
          Add to Calendar
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Base card animations */
.class-card-container {
  transform: translateY(30px);
  opacity: 0;
}

.class-card-container.transform-active {
  transform: translateY(0);
  opacity: 1;
  transition-delay: var(--delay);
}

/* Hover effects */
.class-card-container.hover-effect {
  box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1),
    0 4px 6px -2px rgba(99, 102, 241, 0.05);
  border-color: rgba(99, 102, 241, 0.3);
}

/* Animated background gradient */
.background-shine {
  background-size: 200% 200%;
  animation: gradientMove 15s ease infinite;
}

@keyframes gradientMove {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

/* Course image animations */
.course-image-container {
  transform: scale(1);
  transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.hover-effect .course-image-container {
  transform: scale(1.02);
}

.duration-badge {
  transition: transform 0.3s, background-color 0.3s;
}

.hover-effect .duration-badge {
  transform: translateY(-2px);
  background-color: rgba(67, 56, 202, 0.8);
}

/* Status badge animation */
.status-badge {
  animation: fadeInUp 0.5s forwards;
  animation-delay: calc(var(--delay) + 200ms);
  opacity: 0;
  transform: translateY(10px);
}

/* Time info animation */
.time-info {
  transition: color 0.3s;
}

.hover-effect .time-info {
  color: rgba(79, 70, 229, 0.8);
}

.time-info-animated {
  animation: pulseText 2s infinite;
}

@keyframes pulseText {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

/* Title animations */
.class-title {
  transition: color 0.3s, transform 0.3s;
  transform-origin: left;
}

.hover-effect .class-title {
  color: #4f46e5;
  transform: translateX(2px);
}

/* Course name animation */
.course-name {
  transition: transform 0.3s;
}

.hover-effect .course-name {
  transform: translateX(2px);
}

/* Instructor info animations */
.instructor-info {
  transition: transform 0.3s;
}

.hover-effect .instructor-info {
  transform: translateX(2px);
}

.instructor-avatar-container {
  overflow: hidden;
  border-radius: 9999px;
}

.instructor-avatar {
  transition: transform 0.5s;
}

.hover-effect .instructor-avatar {
  transform: scale(1.1);
}

/* Attendance info animations */
.attendance-count {
  opacity: 0;
  transform: translateY(10px);
  transition: opacity 0.3s, transform 0.3s;
}

.count-animated {
  animation: countSlideIn 0.5s forwards;
}

@keyframes countSlideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Progress bar animation */
.progress-container {
  position: relative;
  overflow: hidden;
}

.progress-bar {
  width: 0;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  transition: width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.progress-bar-animated {
  width: var(--progress);
  transition-delay: var(--progress-delay);
}

.progress-container::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.4),
    transparent
  );
  transform: translateX(-100%);
}

.hover-effect .progress-container::after {
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

/* Expand indicator animation */
.expand-indicator {
  transition: transform 0.3s;
}

.hover-effect .expand-indicator {
  transform: translateX(3px);
}

/* Details container animations */
.details-container {
  animation: slideDown 0.4s forwards;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Section animations */
.details-section {
  opacity: 0;
  animation: fadeIn 0.5s forwards;
}

.details-section:nth-child(1) {
  animation-delay: 0.1s;
}
.details-section:nth-child(2) {
  animation-delay: 0.2s;
}
.details-section:nth-child(3) {
  animation-delay: 0.3s;
}
.details-section:nth-child(4) {
  animation-delay: 0.4s;
}

/* Header animations */
.details-header {
  position: relative;
  display: inline-block;
}

.details-header::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 2px;
  background-color: #4f46e5;
  transition: width 0.4s ease;
}

.details-section:hover .details-header::after {
  width: 100%;
}

/* Tag animations */
.tag-item {
  opacity: 0;
  transform: translateY(10px);
  animation: tagSlideIn 0.4s forwards;
  animation-delay: calc(0.1s + (var(--tag-index) * 0.05s));
  background-position: 200% 0;
  background-size: 200% 100%;
  transition: all 0.3s ease;
}

.tag-item:hover {
  background-image: linear-gradient(90deg, #e0e7ff, #c7d2fe, #e0e7ff);
  background-position: 0% 0;
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(99, 102, 241, 0.2);
}

@keyframes tagSlideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Material item animations */
.material-item {
  opacity: 0;
  transform: translateX(-10px);
  animation: materialSlideIn 0.4s forwards;
  animation-delay: calc(0.2s + (var(--material-index) * 0.1s));
  transition: transform 0.3s, box-shadow 0.3s;
}

.material-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

@keyframes materialSlideIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Material icon animations */
.material-icon {
  transition: transform 0.3s, background-color 0.3s;
}

.material-item:hover .material-icon {
  transform: scale(1.1);
  background-color: #e0e7ff;
}

/* Material download animation */
.material-download {
  transition: transform 0.3s, background-color 0.3s;
}

.material-download:hover {
  transform: translateY(-2px);
  background-color: #e0e7ff;
}

/* Instructor profile animations */
.instructor-profile {
  transition: transform 0.3s;
}

.instructor-profile:hover {
  transform: translateX(5px);
}

.instructor-profile-avatar {
  transition: transform 0.5s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.instructor-profile:hover .instructor-profile-avatar {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Action button animations */
.action-button {
  position: relative;
  overflow: hidden;
  opacity: 0;
  transform: translateY(10px);
  animation: actionButtonSlideIn 0.4s forwards;
}

.join-button {
  animation-delay: 0.1s;
}
.register-button {
  animation-delay: 0.1s;
}
.reminder-button {
  animation-delay: 0.2s;
}
.share-button {
  animation-delay: 0.3s;
}
.calendar-button {
  animation-delay: 0.4s;
}

@keyframes actionButtonSlideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.action-button::after {
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
  transform: rotate(30deg);
  opacity: 0;
  transition: opacity 0.3s;
}

.action-button:hover::after {
  animation: buttonShine 1s forwards;
}

@keyframes buttonShine {
  0% {
    opacity: 0.5;
    transform: translateX(-100%) rotate(30deg);
  }
  100% {
    opacity: 0;
    transform: translateX(100%) rotate(30deg);
  }
}

/* General animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
