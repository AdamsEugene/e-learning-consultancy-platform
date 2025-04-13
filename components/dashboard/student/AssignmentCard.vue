<!-- components/dashboard/AssignmentCard.vue -->
<script setup lang="ts">
interface Assignment {
  id: number;
  title: string;
  courseId: number;
  courseName: string;
  dueDate: string;
  status: "pending" | "submitted" | "graded";
  type: "assignment" | "quiz" | "project";
}

interface Props {
  assignments: Assignment[];
  getTimeRemaining: (dateString: string) => string;
  animationDelay?: number;
  isLoaded?: boolean;
}

defineProps<Props>();

// Get icon based on assignment type
const getTypeIcon = (type: string): string => {
  switch (type) {
    case "assignment":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
      </svg>`;
    case "quiz":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
      </svg>`;
    case "project":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get status badge class
const getStatusClass = (status: string, dueDate: string): string => {
  if (status === "submitted") return "bg-yellow-100 text-yellow-800";
  if (status === "graded") return "bg-green-100 text-green-800";

  // Check if overdue
  const now = new Date();
  const due = new Date(dueDate);

  if (now > due) return "bg-red-100 text-red-800";

  return "bg-blue-100 text-blue-800";
};

// Get status label
const getStatusLabel = (status: string, dueDate: string): string => {
  if (status === "submitted") return "Submitted";
  if (status === "graded") return "Graded";

  // Check if overdue
  const now = new Date();
  const due = new Date(dueDate);

  if (now > due) return "Overdue";

  return "Pending";
};

// Format date
const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  });
};

// Get priority level based on due date
const getPriorityLevel = (
  dueDate: string
): { label: string; class: string } => {
  const now = new Date();
  const due = new Date(dueDate);
  const diffMs = due.getTime() - now.getTime();
  const diffHours = diffMs / (1000 * 60 * 60);

  if (diffHours < 0) {
    return { label: "Overdue", class: "text-red-600" };
  }

  if (diffHours < 24) {
    return { label: "Urgent", class: "text-red-600" };
  }

  if (diffHours < 72) {
    return { label: "High", class: "text-orange-500" };
  }

  return { label: "Normal", class: "text-blue-600" };
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Upcoming Assignments</h3>
        <a
          href="/dashboard/assignments"
          class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center transition-colors"
        >
          View All
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </a>
      </div>
    </div>

    <div class="divide-y divide-gray-100">
      <!-- Assignment item -->
      <div
        v-for="(assignment, index) in assignments"
        :key="assignment.id"
        class="p-4 md:p-6 transition-all duration-200 hover:bg-gray-50 flex flex-col sm:flex-row sm:items-start gap-4"
      >
        <!-- Type icon -->
        <div class="flex-shrink-0">
          <div
            class="w-10 h-10 rounded-lg flex items-center justify-center"
            :class="{
              'bg-indigo-100 text-indigo-600': assignment.type === 'assignment',
              'bg-green-100 text-green-600': assignment.type === 'quiz',
              'bg-blue-100 text-blue-600': assignment.type === 'project',
            }"
            v-html="getTypeIcon(assignment.type)"
          ></div>
        </div>

        <!-- Assignment info -->
        <div class="flex-1">
          <div
            class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2"
          >
            <div>
              <h4 class="text-lg font-bold text-gray-900">
                {{ assignment.title }}
              </h4>
              <p class="text-gray-600 text-sm mb-1">
                {{ assignment.courseName }}
              </p>
              <div class="flex items-center mt-1">
                <span
                  class="px-2 py-1 rounded-full text-xs font-medium mr-2"
                  :class="getStatusClass(assignment.status, assignment.dueDate)"
                >
                  {{ getStatusLabel(assignment.status, assignment.dueDate) }}
                </span>
                <span class="text-gray-500 text-sm flex items-center">
                  <svg
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
                  Due: {{ formatDate(assignment.dueDate) }}
                </span>
              </div>
            </div>

            <div class="flex flex-col sm:items-end">
              <!-- Priority indicator -->
              <span
                class="text-sm font-medium mb-3"
                :class="getPriorityLevel(assignment.dueDate).class"
              >
                {{ getPriorityLevel(assignment.dueDate).label }} Priority
              </span>

              <!-- Time remaining -->
              <div
                class="text-sm font-medium"
                :class="{
                  'text-red-600 animate-pulse':
                    getTimeRemaining(assignment.dueDate) === 'Overdue' ||
                    getTimeRemaining(assignment.dueDate).includes('hours'),
                }"
              >
                {{ getTimeRemaining(assignment.dueDate) }}
              </div>

              <!-- Action button -->
              <a
                :href="`/dashboard/assignments/${assignment.id}`"
                class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 inline-flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{
                  assignment.status === "pending" ? "Start Now" : "View Details"
                }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- If no assignments -->
    <div v-if="assignments.length === 0" class="p-8 text-center">
      <div
        class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-8 w-8 text-indigo-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
          <path
            fill-rule="evenodd"
            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <h4 class="text-lg font-bold text-gray-900 mb-2">All caught up!</h4>
      <p class="text-gray-600">You don't have any pending assignments.</p>
    </div>
  </div>
</template>

<style scoped>
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}

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
