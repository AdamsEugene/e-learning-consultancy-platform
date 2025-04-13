<!-- components/dashboard/LearningPathCard.vue -->
<script setup lang="ts">
interface Course {
  id: number;
  title: string;
}

interface Milestone {
  id: number;
  title: string;
  status: "not-started" | "in-progress" | "completed";
  courses: Course[];
}

interface LearningPath {
  title: string;
  progress: number;
  milestones: Milestone[];
}

interface Props {
  learningPath: LearningPath;
  animationDelay?: number;
  isLoaded?: boolean;
}

const props = defineProps<Props>();

// Get milestone status icon
const getMilestoneIcon = (status: string): string => {
  switch (status) {
    case "completed":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "in-progress":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get milestone status class
const getMilestoneClass = (status: string): string => {
  switch (status) {
    case "completed":
      return "bg-green-100 border-green-200 text-green-800";
    case "in-progress":
      return "bg-indigo-100 border-indigo-200 text-indigo-800";
    default:
      return "bg-gray-100 border-gray-200 text-gray-600";
  }
};

// Get course status class
const getCourseClass = (milestoneStatus: string): string => {
  switch (milestoneStatus) {
    case "completed":
      return "text-green-600";
    case "in-progress":
      return "text-indigo-600";
    default:
      return "text-gray-500";
  }
};

// Get the total number of completed milestones
const completedMilestones = computed(() => {
  return props.learningPath.milestones.filter((m) => m.status === "completed")
    .length;
});

// Button state and action based on progress
const buttonState = computed(() => {
  const inProgressMilestone = props.learningPath.milestones.find(
    (m) => m.status === "in-progress"
  );

  if (inProgressMilestone) {
    const currentCourse = inProgressMilestone.courses[0]; // Simplification - could be more complex logic
    return {
      text: `Continue ${inProgressMilestone.title}`,
      link: `/courses/${currentCourse.id}/learn`,
    };
  }

  const nextMilestone = props.learningPath.milestones.find(
    (m) => m.status === "not-started"
  );
  if (nextMilestone) {
    return {
      text: `Start ${nextMilestone.title}`,
      link: `/learning-paths/${props.learningPath.title
        .toLowerCase()
        .replace(/\s+/g, "-")}/milestones/${nextMilestone.id}`,
    };
  }

  return {
    text: "View Certificate",
    link: "/dashboard/certificates",
  };
});
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Learning Path</h3>
        <a
          href="/learning-paths"
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

    <div class="p-6">
      <!-- Path title and progress -->
      <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
          <h4 class="text-xl font-bold text-gray-900">
            {{ learningPath.title }}
          </h4>
          <span class="text-indigo-600 font-medium"
            >{{ learningPath.progress }}% Complete</span
          >
        </div>

        <!-- Progress bar -->
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
          <div
            class="bg-indigo-600 h-2.5 rounded-full transition-all duration-1000 ease-out"
            :style="{ width: `${learningPath.progress}%` }"
          ></div>
        </div>

        <!-- Milestone stats -->
        <div class="text-sm text-gray-600">
          {{ completedMilestones }} of
          {{ learningPath.milestones.length }} milestones completed
        </div>
      </div>

      <!-- Milestone timeline -->
      <div class="space-y-4 relative">
        <!-- Connecting line -->
        <div
          class="absolute top-0 bottom-0 left-2.5 w-0.5 bg-gray-200 z-0"
        ></div>

        <!-- Milestone items -->
        <div
          v-for="(milestone, index) in learningPath.milestones"
          :key="milestone.id"
          class="relative z-10 flex items-start"
          :class="{ 'opacity-50': milestone.status === 'not-started' }"
        >
          <!-- Status icon -->
          <div
            class="flex-shrink-0 w-5 h-5 rounded-full z-10"
            v-html="getMilestoneIcon(milestone.status)"
          ></div>

          <!-- Milestone content -->
          <div class="ml-4 flex-1">
            <div
              class="rounded-lg p-3 border transition-all duration-200"
              :class="getMilestoneClass(milestone.status)"
            >
              <div class="font-medium">{{ milestone.title }}</div>

              <!-- Course list for this milestone -->
              <div
                v-if="milestone.courses.length > 0"
                class="mt-2 text-sm space-y-1"
              >
                <div
                  v-for="course in milestone.courses"
                  :key="course.id"
                  class="flex items-center"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full mr-2"
                    :class="getCourseClass(milestone.status)"
                  ></span>
                  <a
                    :href="`/courses/${course.id}`"
                    class="hover:underline transition-colors"
                    :class="getCourseClass(milestone.status)"
                  >
                    {{ course.title }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action button -->
      <div class="mt-6 text-center">
        <a
          :href="buttonState.link"
          class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
              clip-rule="evenodd"
            />
          </svg>
          {{ buttonState.text }}
        </a>
      </div>
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
</style>
