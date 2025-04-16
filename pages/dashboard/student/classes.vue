<!-- pages/dashboard/classes.vue -->
<script setup lang="ts">
// Set page layout
definePageMeta({
  layout: "dashboard-student",
});

// Page meta
useHead({
  title: "Upcoming Classes - LearnConsult Dashboard",
  meta: [
    {
      name: "description",
      content: "View and manage your upcoming live classes and webinars.",
    },
  ],
});

// Class data - in a real app, this would be fetched from an API
const upcomingClasses = ref([
  {
    id: 1,
    title: "Advanced CSS Techniques",
    instructor: {
      name: "Alex Johnson",
      avatar: "/images/courses/c3.jpg",
      bio: "Senior Frontend Developer at TechCorp with 10+ years of experience in web design and development.",
    },
    course: {
      id: 101,
      title: "Complete Web Development Bootcamp",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-23T13:00:00",
    endDate: "2023-10-23T14:00:00",
    duration: 60, // minutes
    meetingUrl: "https://meet.example.com/css-advanced",
    description:
      "In this workshop, we'll dive deep into advanced CSS techniques including CSS Grid, custom properties, and advanced animations. We'll build complex layouts and discuss performance optimization.",
    materials: [
      {
        type: "pdf",
        name: "CSS Advanced Techniques Handbook",
        url: "#",
        size: "2.4 MB",
      },
      {
        type: "code",
        name: "Workshop Starter Files",
        url: "#",
        size: "1.1 MB",
      },
    ],
    status: "upcoming",
    attendees: 87,
    maxAttendees: 100,
    isRegistered: true,
    hasReminder: true,
    tags: ["CSS", "Frontend", "Web Design"],
    recordingAvailable: false,
  },
  {
    id: 2,
    title: "User Testing Workshop",
    instructor: {
      name: "Sarah Chen",
      avatar: "/images/courses/c3.jpg",
      bio: "UX Research Lead with expertise in user testing methodologies and design thinking.",
    },
    course: {
      id: 102,
      title: "UI/UX Design Masterclass",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-24T15:30:00",
    endDate: "2023-10-24T17:00:00",
    duration: 90, // minutes
    meetingUrl: "https://meet.example.com/user-testing",
    description:
      "Learn the fundamentals of user testing from planning to execution. We'll cover different testing methodologies, creating test plans, analyzing results, and implementing changes based on feedback.",
    materials: [
      { type: "pdf", name: "User Testing Guide", url: "#", size: "3.2 MB" },
      { type: "doc", name: "Template Test Plan", url: "#", size: "820 KB" },
    ],
    status: "upcoming",
    attendees: 42,
    maxAttendees: 50,
    isRegistered: true,
    hasReminder: false,
    tags: ["UX", "User Testing", "Research"],
    recordingAvailable: false,
  },
  {
    id: 3,
    title: "Solving Complex Algorithms",
    instructor: {
      name: "Michael Brown",
      avatar: "/images/courses/c3.jpg",
      bio: "Software Engineer specializing in algorithms and data structures with experience at leading tech companies.",
    },
    course: {
      id: 103,
      title: "JavaScript Algorithms and Data Structures",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-25T11:00:00",
    endDate: "2023-10-25T12:00:00",
    duration: 60, // minutes
    meetingUrl: "https://meet.example.com/algorithms",
    description:
      "This interactive session focuses on tackling complex algorithmic challenges. We'll analyze problems, develop efficient solutions, and optimize for performance.",
    materials: [
      {
        type: "pdf",
        name: "Algorithm Challenges Handbook",
        url: "#",
        size: "1.8 MB",
      },
      { type: "code", name: "Practice Problems", url: "#", size: "450 KB" },
    ],
    status: "upcoming",
    attendees: 65,
    maxAttendees: 80,
    isRegistered: false,
    hasReminder: false,
    tags: ["Algorithms", "JavaScript", "Problem Solving"],
    recordingAvailable: false,
  },
  {
    id: 4,
    title: "Introduction to React Hooks",
    instructor: {
      name: "Emma Wilson",
      avatar: "/images/courses/c3.jpg",
      bio: "Frontend Developer and instructor specializing in React and modern JavaScript frameworks.",
    },
    course: {
      id: 104,
      title: "Modern React Development",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-26T18:00:00",
    endDate: "2023-10-26T19:30:00",
    duration: 90, // minutes
    meetingUrl: "https://meet.example.com/react-hooks",
    description:
      "A comprehensive introduction to React Hooks. We'll cover useState, useEffect, useContext, and custom hooks while building practical components.",
    materials: [
      { type: "pdf", name: "React Hooks Cheatsheet", url: "#", size: "1.5 MB" },
      { type: "code", name: "Demo Project", url: "#", size: "2.3 MB" },
    ],
    status: "upcoming",
    attendees: 110,
    maxAttendees: 150,
    isRegistered: true,
    hasReminder: true,
    tags: ["React", "JavaScript", "Frontend"],
    recordingAvailable: false,
  },
  {
    id: 5,
    title: "Database Design Principles",
    instructor: {
      name: "David Miller",
      avatar: "/images/courses/c3.jpg",
      bio: "Database architect with 15+ years of experience designing scalable database solutions.",
    },
    course: {
      id: 105,
      title: "Database Management Systems",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-28T14:00:00",
    endDate: "2023-10-28T16:00:00",
    duration: 120, // minutes
    meetingUrl: "https://meet.example.com/database-design",
    description:
      "An in-depth session on database design principles including normalization, indexing strategies, and query optimization techniques for various database systems.",
    materials: [
      { type: "pdf", name: "Database Design Guide", url: "#", size: "4.2 MB" },
      { type: "sql", name: "Example Schemas", url: "#", size: "320 KB" },
    ],
    status: "upcoming",
    attendees: 58,
    maxAttendees: 75,
    isRegistered: false,
    hasReminder: false,
    tags: ["Databases", "SQL", "Design"],
    recordingAvailable: false,
  },
]);

// Past classes data
const pastClasses = ref([
  {
    id: 101,
    title: "Introduction to CSS Grid",
    instructor: {
      name: "Alex Johnson",
      avatar: "/images/courses/c3.jpg",
    },
    course: {
      id: 101,
      title: "Complete Web Development Bootcamp",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-16T13:00:00",
    duration: 60, // minutes
    status: "completed",
    attendees: 92,
    maxAttendees: 100,
    tags: ["CSS", "Frontend", "Web Design"],
    recordingAvailable: true,
    recordingUrl: "https://example.com/recordings/css-grid",
    isWatched: false,
  },
  {
    id: 102,
    title: "JavaScript Async/Await Deep Dive",
    instructor: {
      name: "Michael Brown",
      avatar: "/images/courses/c3.jpg",
    },
    course: {
      id: 103,
      title: "JavaScript Algorithms and Data Structures",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-18T11:00:00",
    duration: 90, // minutes
    status: "completed",
    attendees: 78,
    maxAttendees: 80,
    tags: ["JavaScript", "Async", "Programming"],
    recordingAvailable: true,
    recordingUrl: "https://example.com/recordings/async-await",
    isWatched: true,
  },
  {
    id: 103,
    title: "Mobile-First Design Approach",
    instructor: {
      name: "Sarah Chen",
      avatar: "/images/courses/c3.jpg",
    },
    course: {
      id: 102,
      title: "UI/UX Design Masterclass",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-10-19T15:30:00",
    duration: 60, // minutes
    status: "completed",
    attendees: 47,
    maxAttendees: 50,
    tags: ["UI/UX", "Mobile", "Responsive"],
    recordingAvailable: true,
    recordingUrl: "https://example.com/recordings/mobile-first",
    isWatched: false,
  },
]);

// Recommended classes based on enrolled courses
const recommendedClasses = ref([
  {
    id: 201,
    title: "The Future of Web Animation",
    instructor: {
      name: "Lisa Zhang",
      avatar: "/images/courses/c3.jpg",
    },
    course: {
      id: 106,
      title: "Advanced Frontend Animation",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-11-02T16:00:00",
    duration: 75, // minutes
    tags: ["Animation", "CSS", "Web Design"],
    matchScore: 98,
    isRegistered: false,
  },
  {
    id: 202,
    title: "State Management in Modern Applications",
    instructor: {
      name: "James Wilson",
      avatar: "/images/courses/c3.jpg",
    },
    course: {
      id: 107,
      title: "Advanced Frontend Architecture",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-11-05T14:30:00",
    duration: 90, // minutes
    tags: ["JavaScript", "React", "State Management"],
    matchScore: 95,
    isRegistered: false,
  },
  {
    id: 203,
    title: "Accessibility in Web Design",
    instructor: {
      name: "Rachel Harris",
      avatar: "/images/courses/c3.jpg",
    },
    course: {
      id: 108,
      title: "Inclusive Web Development",
      thumbnail: "/images/courses/c3.jpg",
    },
    date: "2023-11-08T11:00:00",
    duration: 60, // minutes
    tags: ["Accessibility", "HTML", "Web Design"],
    matchScore: 92,
    isRegistered: false,
  },
]);

// Filter states
const activeTab = ref("upcoming");
const showClassDetails = ref<number | null>(null);
const searchQuery = ref("");
const selectedTag = ref("");

// Computed properties
const filteredUpcomingClasses = computed(() => {
  let filtered = upcomingClasses.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (cls) =>
        cls.title.toLowerCase().includes(query) ||
        cls.instructor.name.toLowerCase().includes(query) ||
        cls.course.title.toLowerCase().includes(query) ||
        cls.tags.some((tag) => tag.toLowerCase().includes(query))
    );
  }

  if (selectedTag.value) {
    filtered = filtered.filter((cls) =>
      cls.tags.some(
        (tag) => tag.toLowerCase() === selectedTag.value.toLowerCase()
      )
    );
  }

  return filtered;
});

const filteredPastClasses = computed(() => {
  let filtered = pastClasses.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (cls) =>
        cls.title.toLowerCase().includes(query) ||
        cls.instructor.name.toLowerCase().includes(query) ||
        cls.course.title.toLowerCase().includes(query) ||
        cls.tags.some((tag) => tag.toLowerCase().includes(query))
    );
  }

  if (selectedTag.value) {
    filtered = filtered.filter((cls) =>
      cls.tags.some(
        (tag) => tag.toLowerCase() === selectedTag.value.toLowerCase()
      )
    );
  }

  return filtered;
});

// All unique tags for filter
const allTags = computed(() => {
  const tags = new Set<string>();

  upcomingClasses.value.forEach((cls) => {
    cls.tags.forEach((tag) => tags.add(tag));
  });

  pastClasses.value.forEach((cls) => {
    cls.tags.forEach((tag) => tags.add(tag));
  });

  return Array.from(tags).sort();
});

// Toggle reminder status
const toggleReminder = (classId: number) => {
  const classIndex = upcomingClasses.value.findIndex(
    (cls) => cls.id === classId
  );
  if (classIndex !== -1) {
    upcomingClasses.value[classIndex].hasReminder =
      !upcomingClasses.value[classIndex].hasReminder;
  }
};

// Register for a class
const registerForClass = (classId: number) => {
  // Upcoming classes
  const upcomingIndex = upcomingClasses.value.findIndex(
    (cls) => cls.id === classId
  );
  if (upcomingIndex !== -1) {
    upcomingClasses.value[upcomingIndex].isRegistered = true;
    upcomingClasses.value[upcomingIndex].attendees += 1;
    return;
  }

  // Recommended classes
  const recommendedIndex = recommendedClasses.value.findIndex(
    (cls) => cls.id === classId
  );
  if (recommendedIndex !== -1) {
    recommendedClasses.value[recommendedIndex].isRegistered = true;
  }
};

// Watch recording
const watchRecording = (classId: number) => {
  console.log("Watching recording for class:", classId);
  // In a real app, this would navigate to the recording or open a player
  // router.push(`/dashboard/recordings/${classId}`);
};

// Update selected tag for filtering
const handleTagSelect = (tag: string) => {
  selectedTag.value = tag;
};

// View class details
const viewClassDetails = (classId: number) => {
  showClassDetails.value = classId === showClassDetails.value ? null : classId;
};

// Animation flags for staggered entrance
const isLoaded = ref(false);
const pageEntered = ref(false);

// Set up animation
onMounted(() => {
  // Initial page entrance animation
  setTimeout(() => {
    pageEntered.value = true;
  }, 100);

  // Components animations trigger
  setTimeout(() => {
    isLoaded.value = true;
  }, 300);
});
</script>

<template>
  <div class="page-container" :class="{ 'page-entered': pageEntered }">
    <div class="page-header">
      <h1 class="text-3xl font-bold text-gray-800 mb-2 header-title">
        Upcoming Classes
      </h1>
      <p class="text-gray-600 header-subtitle">
        Join interactive live classes led by expert instructors
      </p>
    </div>

    <!-- Search and filters bar -->
    <div
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6 filter-bar"
      :class="{ 'filter-bar-visible': pageEntered }"
    >
      <div
        class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4"
      >
        <!-- Search box -->
        <div class="flex-grow relative search-container">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search classes by title, instructor, or course..."
            class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent search-input"
          />
          <div class="absolute left-3 top-3 text-gray-400 search-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>

        <!-- Tag filter -->
        <div class="min-w-[200px] tag-filter">
          <select
            v-model="selectedTag"
            class="appearance-none w-full bg-white border border-gray-300 rounded-lg py-3 px-4 pr-8 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="">All Topics</option>
            <option v-for="tag in allTags" :key="tag" :value="tag">
              {{ tag }}
            </option>
          </select>
        </div>

        <!-- Tab buttons -->
        <div class="flex space-x-2 tab-buttons">
          <button
            @click="activeTab = 'upcoming'"
            class="px-5 py-3 rounded-lg font-medium transition-all duration-500 transform tab-button"
            :class="
              activeTab === 'upcoming'
                ? 'bg-indigo-600 text-white scale-105 shadow-lg'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            "
          >
            Upcoming
          </button>
          <button
            @click="activeTab = 'past'"
            class="px-5 py-3 rounded-lg font-medium transition-all duration-500 transform tab-button"
            :class="
              activeTab === 'past'
                ? 'bg-indigo-600 text-white scale-105 shadow-lg'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            "
          >
            Past
          </button>
        </div>
      </div>
    </div>

    <!-- Classes content based on active tab -->
    <div v-if="activeTab === 'upcoming'">
      <!-- Upcoming classes grid -->
      <div
        v-if="filteredUpcomingClasses.length > 0"
        class="grid grid-cols-1 gap-6 classes-grid"
      >
        <!-- Class cards using the component -->
        <DashboardStudentClassesClassCard
          v-for="(cls, index) in filteredUpcomingClasses"
          :key="cls.id"
          :class-data="cls"
          :is-animation-loaded="isLoaded"
          :animation-delay="200"
          :index="index"
          @view-details="viewClassDetails"
          @toggle-reminder="toggleReminder"
          @register="registerForClass"
          @select-tag="handleTagSelect"
        />
      </div>

      <!-- Empty state for no upcoming classes matching filters -->
      <div
        v-else
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center empty-state"
        :class="{ 'empty-state-visible': isLoaded }"
      >
        <div class="empty-state-icon">
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
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2 empty-state-title">
          No upcoming classes found
        </h3>
        <p class="text-gray-600 mb-6 empty-state-description">
          There are no upcoming classes matching your current filters. Try
          adjusting your search or check back later for new classes.
        </p>
        <button
          @click="
            searchQuery = '';
            selectedTag = '';
          "
          class="inline-flex items-center px-5 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg empty-state-button"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
              clip-rule="evenodd"
            />
          </svg>
          Reset Filters
        </button>
      </div>

      <!-- Recommended classes section -->
      <div class="mt-10 recommended-section">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 recommended-title">
          Recommended for You
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 recommended-grid">
          <DashboardStudentClassesRecommendedClassCard
            v-for="(cls, index) in recommendedClasses"
            :key="cls.id"
            :class-data="cls"
            :is-animation-loaded="isLoaded"
            :animation-delay="500"
            :index="index"
            @register="registerForClass"
          />
        </div>
      </div>
    </div>

    <!-- Past classes tab -->
    <div v-else-if="activeTab === 'past'">
      <!-- Past classes table using the component -->
      <DashboardStudentClassesPastClassesTable
        :classes="filteredPastClasses"
        :is-animation-loaded="isLoaded"
        :animation-delay="200"
        @select-tag="handleTagSelect"
        @watch-recording="watchRecording"
      />
    </div>
  </div>
</template>

<style scoped>
/* Page container animations */
.page-container {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s ease, transform 0.8s ease;
}

.page-entered {
  opacity: 1;
  transform: translateY(0);
}

/* Header animations */
.page-header {
  margin-bottom: 1.5rem;
}

.header-title {
  position: relative;
  display: inline-block;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  -webkit-background-clip: text;
  color: transparent;
  background-size: 200% 100%;
  animation: gradientMove 6s infinite linear;
}

.header-title::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  height: 3px;
  width: 60px;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  border-radius: 3px;
  animation: expandWidth 1.5s forwards ease-out;
}

@keyframes expandWidth {
  from {
    width: 0;
  }
  to {
    width: 60px;
  }
}

.header-subtitle {
  opacity: 0;
  transform: translateY(10px);
  animation: fadeInUp 0.8s forwards;
  animation-delay: 0.3s;
}

/* Filter bar animations */
.filter-bar {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease, transform 0.6s ease;
  transition-delay: 0.2s;
  position: relative;
  overflow: hidden;
}

.filter-bar::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0),
    rgba(79, 70, 229, 0.1),
    rgba(255, 255, 255, 0)
  );
  transform: translateX(-100%);
}

.filter-bar-visible {
  opacity: 1;
  transform: translateY(0);
}

.filter-bar-visible::before {
  animation: shimmer 3s infinite;
}

.search-container {
  position: relative;
}

.search-input {
  transition: box-shadow 0.3s, border-color 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
}

.search-icon {
  transition: transform 0.3s, color 0.3s;
}

.search-input:focus + .search-icon {
  transform: scale(1.1);
  color: #4f46e5;
}

.tag-filter select {
  transition: box-shadow 0.3s, border-color 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.tag-filter select:focus {
  box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
}

.tab-buttons {
  position: relative;
  z-index: 1;
}

.tab-button {
  position: relative;
  overflow: hidden;
}

.tab-button::after {
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
  transition: transform 0.7s;
}

.tab-button:hover::after {
  transform: rotate(30deg) translate(100%, 0);
}

/* Empty state animations */
.empty-state {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.7s, transform 0.7s;
}

.empty-state-visible {
  opacity: 1;
  transform: translateY(0);
}

.empty-state-icon {
  transform: scale(0.8);
  opacity: 0;
  animation: scaleIn 0.5s forwards;
  animation-delay: 0.5s;
}

.empty-state-title {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.5s forwards;
  animation-delay: 0.7s;
}

.empty-state-description {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.5s forwards;
  animation-delay: 0.9s;
}

.empty-state-button {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.5s forwards;
  animation-delay: 1.1s;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
}

.empty-state-button:hover {
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
}

/* Recommended section animations */
.recommended-section {
  margin-top: 4rem;
}

.recommended-title {
  position: relative;
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.7s forwards;
  animation-delay: 0.3s;
}

.recommended-title::after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 0;
  height: 3px;
  width: 0;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
  border-radius: 3px;
  animation: expandWidth 1.5s forwards ease-out;
  animation-delay: 0.8s;
}

/* General animations */
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
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

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
