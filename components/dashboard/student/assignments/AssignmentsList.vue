<!-- components/dashboard/DashboardAssignmentsList.vue -->
<script setup lang="ts">
import type { Assignment } from "~/types/assignments";
import {
  formatDate,
  formatTime,
  getTimeRemaining,
  getStatusColor,
} from "~/types/assignments";

// Props
const props = defineProps<{
  assignments: Assignment[];
  isLoading: boolean;
  isLoaded: boolean;
}>();

// Emits
const emit = defineEmits<{
  (e: "view-details", assignment: Assignment): void;
}>();

// Calculate time status for visual indicators
const getTimeStatus = (assignment: Assignment): string => {
  // Don't show warnings for completed assignments
  if (assignment.status === "graded" || assignment.status === "submitted") {
    return "completed";
  }

  const remaining = getTimeRemaining(assignment.dueDate);

  if (remaining === "Overdue") {
    return "overdue";
  }

  if (remaining.includes("h") && !remaining.includes("d")) {
    return "urgent"; // Only hours remaining
  }

  const days = parseInt(remaining.split("d")[0]);
  if (days <= 2) {
    return "warning"; // 2 or fewer days
  }

  return "normal";
};

// Determine progress indicator
const getProgressIndicator = (assignment: Assignment) => {
  switch (assignment.status) {
    case "pending":
      return { color: "bg-yellow-500", percentage: 0 };
    case "late":
      return { color: "bg-red-500", percentage: 0 };
    case "submitted":
      return { color: "bg-blue-500", percentage: 65 };
    case "graded":
      return { color: "bg-green-500", percentage: 100 };
    default:
      return { color: "bg-gray-300", percentage: 0 };
  }
};

// Open assignment details modal
const viewDetails = (assignment: Assignment) => {
  emit("view-details", assignment);
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transform transition-all duration-500"
    :class="isLoaded ? 'animate-rise-up' : 'opacity-0 translate-y-8'"
  >
    <!-- Loading state -->
    <div v-if="isLoading" class="p-8 flex justify-center">
      <div class="loader"></div>
    </div>

    <!-- Empty state -->
    <div v-else-if="assignments.length === 0" class="p-8 text-center">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-16 w-16 mx-auto text-gray-400 mb-4"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
        />
      </svg>
      <h3 class="text-xl font-bold text-gray-900 mb-2">No assignments found</h3>
      <p class="text-gray-600">Try adjusting your filters or search query</p>
    </div>

    <!-- Assignments table -->
    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Assignment
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Course
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Due Date
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Type
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Time Remaining
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Grade
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(assignment, index) in assignments"
            :key="assignment.id"
            class="hover:bg-gray-50 transition-colors cursor-pointer assignment-row"
            :class="{ 'animate-row-fade-in': isLoaded }"
            :style="`--delay: ${index * 30}ms`"
            @click="viewDetails(assignment)"
          >
            <!-- Assignment Title -->
            <td class="px-6 py-4">
              <div class="flex items-center">
                <!-- Progress indicator -->
                <div
                  class="mr-3 relative h-8 w-8 rounded-full border-2 border-gray-200 flex items-center justify-center"
                >
                  <svg
                    class="absolute top-0 left-0 h-full w-full"
                    viewBox="0 0 36 36"
                  >
                    <path
                      class="progress-circle"
                      :class="getProgressIndicator(assignment).color"
                      :style="{
                        strokeDasharray: `${
                          getProgressIndicator(assignment).percentage
                        }, 100`,
                      }"
                      d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                      fill="none"
                      stroke-width="3"
                      stroke-linecap="round"
                    />
                  </svg>

                  <!-- Assignment type icon -->
                  <svg
                    v-if="assignment.type === 'project'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                  </svg>

                  <svg
                    v-else-if="assignment.type === 'quiz'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-purple-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                    />
                  </svg>

                  <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-blue-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                    />
                  </svg>
                </div>

                <div>
                  <div class="font-medium text-gray-900 line-clamp-1">
                    {{ assignment.title }}
                  </div>
                  <div
                    v-if="
                      assignment.attachments &&
                      assignment.attachments.length > 0
                    "
                    class="text-xs text-gray-500 mt-0.5"
                  >
                    {{ assignment.attachments.length }} attachment{{
                      assignment.attachments.length > 1 ? "s" : ""
                    }}
                  </div>
                </div>
              </div>
            </td>

            <!-- Course -->
            <td class="px-6 py-4">
              <div class="text-sm text-gray-600 line-clamp-1">
                {{ assignment.courseName }}
              </div>
            </td>

            <!-- Due Date -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-600">
                {{ formatDate(assignment.dueDate) }}
              </div>
              <div class="text-xs text-gray-500">
                {{ formatTime(assignment.dueDate) }}
              </div>
            </td>

            <!-- Status Badge -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(
                  assignment.status
                )}`"
              >
                {{
                  assignment.status.charAt(0).toUpperCase() +
                  assignment.status.slice(1)
                }}
              </span>
            </td>

            <!-- Assignment Type -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-600 capitalize">
                {{ assignment.type }}
              </div>
            </td>

            <!-- Time Remaining -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div
                class="text-sm flex items-center"
                :class="{
                  'text-red-600 font-semibold':
                    getTimeStatus(assignment) === 'overdue',
                  'text-yellow-600': getTimeStatus(assignment) === 'urgent',
                  'text-orange-500': getTimeStatus(assignment) === 'warning',
                  'text-green-600': getTimeStatus(assignment) === 'normal',
                  'text-gray-600': getTimeStatus(assignment) === 'completed',
                }"
              >
                <!-- Time status icons -->
                <svg
                  v-if="getTimeStatus(assignment) === 'overdue'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1 animate-pulse"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>

                <svg
                  v-else-if="getTimeStatus(assignment) === 'urgent'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1 animate-pulse"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>

                <svg
                  v-else-if="getTimeStatus(assignment) === 'warning'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"
                  />
                </svg>

                <svg
                  v-else-if="getTimeStatus(assignment) === 'completed'"
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

                {{
                  getTimeStatus(assignment) === "completed"
                    ? "Completed"
                    : getTimeRemaining(assignment.dueDate)
                }}
              </div>
            </td>

            <!-- Grade -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div
                v-if="assignment.grade !== undefined"
                class="text-sm font-medium text-gray-900"
              >
                {{ assignment.grade }}/{{ assignment.maxGrade }}
              </div>
              <div
                v-else-if="assignment.status === 'submitted'"
                class="text-sm text-gray-500 italic"
              >
                Pending
              </div>
              <div v-else class="text-sm text-gray-500">—</div>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <button
                @click.stop="viewDetails(assignment)"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium transition-colors inline-flex items-center"
              >
                <span>View</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
/* Progress circle animation */
.progress-circle {
  stroke-dasharray: 0, 100;
  transition: stroke-dasharray 1s ease;
}

/* Assignment row animations */
.assignment-row {
  transition: all 0.2s;
}

.assignment-row:hover {
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
  transform: translateY(-1px);
}

.animate-row-fade-in {
  animation: rowFadeIn 0.5s ease-in-out forwards;
  opacity: 0;
  animation-delay: var(--delay, 0ms);
}

@keyframes rowFadeIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-rise-up {
  animation: riseUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes riseUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Loading animation */
.loader {
  border: 4px solid rgba(99, 102, 241, 0.1);
  border-radius: 50%;
  border-top: 4px solid #6366f1;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Line clamping for text overflow */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Pulse animation for urgent timestamps */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
