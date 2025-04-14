<!-- components/dashboard/classes/PastClassesTable.vue -->
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
interface PastClass {
  id: number;
  title: string;
  instructor: ClassInstructor;
  course: ClassCourse;
  date: string;
  duration: number;
  status: string;
  attendees?: number;
  maxAttendees?: number;
  tags: string[];
  recordingAvailable: boolean;
  recordingUrl?: string;
  isWatched?: boolean;
}
interface Props {
  classes: PastClass[];
  isAnimationLoaded: boolean;
  animationDelay?: number;
}
const props = withDefaults(defineProps<Props>(), {
  animationDelay: 0,
});
// Emit events
const emit = defineEmits(["select-tag", "watch-recording"]);
// Local state
const hoveredRow = ref<number | null>(null);
const playingPreview = ref<number | null>(null);
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
// Handle watching recording
const watchRecording = (classId: number) => {
  emit("watch-recording", classId);
};
// Select tag for filtering
const selectTag = (tag: string, event: Event) => {
  event.stopPropagation();
  emit("select-tag", tag);
};
// Toggle row preview
const togglePreview = (classId: number) => {
  if (playingPreview.value === classId) {
    playingPreview.value = null;
  } else {
    playingPreview.value = classId;
  }
};
// Check if a row is being hovered
const isRowHovered = (classId: number) => {
  return hoveredRow.value === classId;
};
// Calculate animation delay for each row
const getRowDelay = (index: number) => {
  return props.animationDelay + index * 50;
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-500 transform past-classes-container"
    :class="{
      'translate-y-0 opacity-100': isAnimationLoaded,
      'translate-y-4 opacity-0': !isAnimationLoaded,
    }"
    :style="{ transition: `all 0.5s ease ${animationDelay}ms` }"
  >
    <div class="overflow-x-auto">
      <table class="min-w-full table-container">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-header"
            >
              Class
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-header"
            >
              Course
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-header"
            >
              Date
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-header"
            >
              Duration
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider table-header"
            >
              Recording
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 table-body">
          <tr
            v-for="(cls, index) in classes"
            :key="cls.id"
            class="table-row-container"
            :class="{ 'row-hovered': isRowHovered(cls.id) }"
            @mouseenter="hoveredRow = cls.id"
            @mouseleave="hoveredRow = null"
            :style="{ '--row-delay': `${getRowDelay(index)}ms` }"
          >
            <!-- Class info cell -->
            <td class="px-6 py-4 class-cell">
              <div class="flex items-center">
                <div class="flex-shrink-0 class-image-container">
                  <img
                    :src="cls.course.thumbnail"
                    :alt="cls.course.title"
                    class="h-12 w-20 rounded object-cover mr-3 class-image"
                  />

                  <!-- Play preview overlay -->
                  <div
                    v-if="cls.recordingAvailable"
                    class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 play-overlay"
                    @click.stop="togglePreview(cls.id)"
                  >
                    <div
                      class="w-8 h-8 rounded-full bg-white flex items-center justify-center play-button"
                    >
                      <svg
                        v-if="playingPreview !== cls.id"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-indigo-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-indigo-600"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 00-1 1v2a1 1 0 001 1h6a1 1 0 001-1V9a1 1 0 00-1-1H7z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <div class="class-text-container">
                  <div class="font-medium text-gray-900 class-title">
                    {{ cls.title }}
                  </div>
                  <div
                    class="flex items-center text-sm text-gray-500 instructor-info"
                  >
                    <div class="instructor-image-container">
                      <img
                        :src="cls.instructor.avatar"
                        :alt="cls.instructor.name"
                        class="w-5 h-5 rounded-full mr-2 instructor-image"
                      />
                    </div>
                    <span>{{ cls.instructor.name }}</span>
                  </div>

                  <!-- Tag pills -->
                  <div class="flex flex-wrap gap-1 mt-1 tags-container">
                    <span
                      v-for="(tag, tagIndex) in cls.tags.slice(0, 2)"
                      :key="tag"
                      class="inline-flex items-center px-2 py-0.5 text-xs rounded-full tag-pill"
                      :class="
                        tagIndex === 0
                          ? 'bg-indigo-100 text-indigo-700'
                          : 'bg-purple-100 text-purple-700'
                      "
                      :style="{ '--tag-index': tagIndex }"
                      @click="selectTag(tag, $event)"
                    >
                      {{ tag }}
                    </span>
                    <span
                      v-if="cls.tags.length > 2"
                      class="inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700 more-tags"
                    >
                      +{{ cls.tags.length - 2 }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Video preview (expanded) -->
              <div
                v-if="playingPreview === cls.id"
                class="mt-3 bg-gray-900 rounded overflow-hidden aspect-w-16 aspect-h-9 video-preview"
              >
                <!-- This would be a real video player in production -->
                <div
                  class="bg-gradient-to-r from-gray-800 to-gray-900 h-full w-full flex items-center justify-center p-4"
                >
                  <div class="text-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-10 w-10 mx-auto text-indigo-400 animate-pulse"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <p class="text-white text-sm mt-2">
                      Preview: {{ cls.title }}
                    </p>
                    <button
                      @click="watchRecording(cls.id)"
                      class="mt-2 px-3 py-1 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                      Watch Full Recording
                    </button>
                  </div>
                </div>
              </div>
            </td>

            <!-- Course name -->
            <td class="px-6 py-4 text-sm text-gray-600 course-cell">
              <div class="course-name">{{ cls.course.title }}</div>
            </td>

            <!-- Date -->
            <td class="px-6 py-4 text-sm text-gray-600 date-cell">
              <div class="date-text">{{ formatDate(cls.date) }}</div>
            </td>

            <!-- Duration -->
            <td class="px-6 py-4 text-sm text-gray-600 duration-cell">
              <div class="duration-text">{{ cls.duration }} min</div>
            </td>

            <!-- Recording button -->
            <td class="px-6 py-4 recording-cell">
              <a
                v-if="cls.recordingAvailable"
                :href="cls.recordingUrl"
                @click.prevent="watchRecording(cls.id)"
                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 recording-button"
                :class="[
                  cls.isWatched
                    ? 'bg-purple-100 text-purple-700 hover:bg-purple-200'
                    : 'bg-indigo-600 text-white hover:bg-indigo-700',
                ]"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ cls.isWatched ? "Rewatch" : "Watch" }}
              </a>
              <span v-else class="text-sm text-gray-500 not-available"
                >Not available</span
              >
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
/* Container animations */
.past-classes-container {
  position: relative;
  overflow: hidden;
}

.translate-y-4 {
  transform: translateY(16px);
}

.translate-y-0 {
  transform: translateY(0);
}

.opacity-0 {
  opacity: 0;
}

.opacity-100 {
  opacity: 1;
}

/* Table general styles */
.table-container {
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
}

/* Header animations */
.table-header {
  position: relative;
  overflow: hidden;
  transition: color 0.3s;
}

.table-header::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  height: 2px;
  width: 0;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  transition: width 0.5s ease;
}

.table-header:hover {
  color: #4f46e5;
}

.table-header:hover::after {
  width: 100%;
}

/* Row animations */
.table-row-container {
  opacity: 0;
  transform: translateX(-20px);
  transition: background-color 0.3s, opacity 0.5s, transform 0.5s;
  animation: rowFadeIn 0.5s forwards;
  animation-delay: var(--row-delay);
}

@keyframes rowFadeIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.row-hovered {
  background-color: rgba(243, 244, 246, 0.7);
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.05) inset;
}

/* Class image container animations */
.class-image-container {
  position: relative;
  overflow: hidden;
  border-radius: 0.25rem;
  transition: transform 0.3s, box-shadow 0.3s;
}

.row-hovered .class-image-container {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.class-image {
  transition: transform 0.6s ease;
}

.row-hovered .class-image {
  transform: scale(1.1);
}

/* Play overlay animations */
.play-overlay {
  opacity: 0;
  transition: opacity 0.3s;
}

.class-image-container:hover .play-overlay {
  opacity: 1;
}

.play-button {
  transform: scale(0.8);
  transition: transform 0.3s, box-shadow 0.3s;
}

.play-overlay:hover .play-button {
  transform: scale(1);
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

/* Class text animations */
.class-text-container {
  transition: transform 0.3s;
}

.row-hovered .class-text-container {
  transform: translateX(5px);
}

.class-title {
  position: relative;
  display: inline-block;
  transition: color 0.3s;
}

.row-hovered .class-title {
  color: #4f46e5;
}

.class-title::after {
  content: "";
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 1px;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  transition: width 0.5s ease;
}

.row-hovered .class-title::after {
  width: 100%;
}

/* Instructor info animations */
.instructor-info {
  transition: opacity 0.3s;
}

.row-hovered .instructor-info {
  opacity: 0.8;
}

.instructor-image-container {
  overflow: hidden;
  border-radius: 9999px;
}

.instructor-image {
  transition: transform 0.5s;
}

.row-hovered .instructor-image {
  transform: scale(1.2);
}

/* Tags animations */
.tags-container {
  transition: transform 0.3s;
  transform-origin: left;
}

.row-hovered .tags-container {
  transform: scale(1.05);
}

.tag-pill {
  cursor: pointer;
  opacity: 0;
  transform: translateY(5px);
  animation: tagSlideIn 0.4s forwards;
  animation-delay: calc(var(--row-delay) + 100ms + (var(--tag-index) * 100ms));
  transition: transform 0.3s, box-shadow 0.3s;
}

@keyframes tagSlideIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.tag-pill:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
}

.more-tags {
  cursor: pointer;
  transition: transform 0.3s, background-color 0.3s, color 0.3s;
}

.more-tags:hover {
  transform: translateY(-2px);
  background-color: #e0e7ff;
  color: #4338ca;
}

/* Video preview animations */
.video-preview {
  max-height: 0;
  animation: expandPreview 0.5s forwards;
}

@keyframes expandPreview {
  from {
    max-height: 0;
    opacity: 0;
  }
  to {
    max-height: 200px;
    opacity: 1;
  }
}

/* Course name animations */
.course-name {
  position: relative;
  transition: transform 0.3s, color 0.3s;
}

.row-hovered .course-name {
  transform: translateX(5px);
  color: #4f46e5;
}

/* Date text animations */
.date-text {
  position: relative;
  transition: transform 0.3s;
}

.row-hovered .date-text {
  transform: translateX(5px);
}

/* Duration text animations */
.duration-text {
  position: relative;
  overflow: hidden;
  transition: transform 0.3s;
}

.row-hovered .duration-text {
  transform: translateX(5px);
}

.duration-text::after {
  content: "";
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 1px;
  background-color: #4f46e5;
  transition: width 0.3s ease;
}

.row-hovered .duration-text::after {
  width: 100%;
}

/* Recording button animations */
.recording-button {
  position: relative;
  overflow: hidden;
  transform: translateY(0);
  transition: transform 0.3s, box-shadow 0.3s;
}

.row-hovered .recording-button {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 4px 8px rgba(99, 102, 241, 0.2);
}

.recording-button::after {
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
  transition: opacity 0.3s, transform 0.3s;
}

.row-hovered .recording-button::after {
  opacity: 1;
  animation: buttonShine 1s forwards;
}

@keyframes buttonShine {
  0% {
    transform: rotate(30deg) translateX(-100%);
  }
  100% {
    transform: rotate(30deg) translateX(100%);
  }
}

/* Not available text animation */
.not-available {
  transition: opacity 0.3s;
}

.row-hovered .not-available {
  opacity: 0.6;
}

/* General animations */
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
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
