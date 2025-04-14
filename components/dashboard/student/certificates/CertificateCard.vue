<!-- components/dashboard/certificates/CertificateCard.vue -->
<script setup lang="ts">
interface Certificate {
  id: number;
  title: string;
  courseId: number;
  courseName: string;
  issueDate: string;
  expiryDate?: string;
  instructor: string;
  credential: {
    id: string;
    url: string;
  };
  skills: string[];
  image: string;
  featured?: boolean;
  verificationStatus: "verified" | "pending" | "expired";
  progress?: number;
  templateId?: string;
}

interface Props {
  certificate: Certificate;
  index: number;
  formatDate: (date: string) => string;
}

const props = defineProps<Props>();
const emit = defineEmits(["view-details", "view-template"]);

// Get status badge color
const getStatusColor = (status: string) => {
  switch (status) {
    case "verified":
      return "bg-green-100 text-green-800 border-green-200";
    case "pending":
      return "bg-yellow-100 text-yellow-800 border-yellow-200";
    case "expired":
      return "bg-red-100 text-red-800 border-red-200";
    default:
      return "bg-gray-100 text-gray-800 border-gray-200";
  }
};

// View certificate details
const viewDetails = () => {
  emit("view-details");
};

// View certificate template
const viewTemplate = (event: Event) => {
  event.stopPropagation(); // Prevent triggering the card click
  emit("view-template");
};
</script>

<template>
  <div
    class="group relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1 cursor-pointer"
    :style="`animation-delay: ${index * 100 + 400}ms`"
    @click="viewDetails"
  >
    <!-- Certificate image with overlay -->
    <div class="relative h-48 overflow-hidden">
      <img
        :src="certificate.image"
        :alt="certificate.title"
        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
      />

      <!-- Overlay gradient -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent opacity-60"
      ></div>

      <!-- Status badge -->
      <div
        class="absolute top-4 right-4 py-1 px-3 rounded-full text-xs font-bold border"
        :class="getStatusColor(certificate.verificationStatus)"
      >
        <div class="flex items-center space-x-1">
          <span
            v-if="certificate.verificationStatus === 'verified'"
            class="w-2 h-2 bg-green-500 rounded-full"
          ></span>
          <span
            v-else-if="certificate.verificationStatus === 'pending'"
            class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"
          ></span>
          <span v-else class="w-2 h-2 bg-red-500 rounded-full"></span>
          <span>{{
            certificate.verificationStatus.charAt(0).toUpperCase() +
            certificate.verificationStatus.slice(1)
          }}</span>
        </div>
      </div>

      <!-- Featured badge -->
      <div
        v-if="certificate.featured"
        class="absolute top-4 left-4 bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg"
      >
        Featured
      </div>

      <!-- View template button -->
      <button
        class="absolute bottom-4 right-4 bg-white/20 backdrop-blur-sm text-white py-1 px-3 rounded-lg text-xs font-medium hover:bg-white/30 transition-colors opacity-0 group-hover:opacity-100"
        @click="viewTemplate"
      >
        <div class="flex items-center space-x-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
            <path
              fill-rule="evenodd"
              d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
              clip-rule="evenodd"
            />
          </svg>
          <span>View Template</span>
        </div>
      </button>

      <!-- Progress bar for in-progress certificates -->
      <div
        v-if="certificate.progress !== undefined"
        class="absolute bottom-0 left-0 right-0 h-2 bg-gray-200"
      >
        <div
          class="h-full bg-indigo-600 transition-all duration-1000"
          :style="`width: ${certificate.progress}%`"
        ></div>
      </div>
    </div>

    <!-- Certificate info -->
    <div class="p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-2">
        {{ certificate.title }}
      </h3>
      <p class="text-gray-600 text-sm mb-4">{{ certificate.courseName }}</p>

      <!-- Skills pills -->
      <div class="flex flex-wrap gap-2 mb-4">
        <span
          v-for="(skill, idx) in certificate.skills.slice(0, 3)"
          :key="idx"
          class="inline-block bg-indigo-100 text-indigo-800 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap"
        >
          {{ skill }}
        </span>
        <span
          v-if="certificate.skills.length > 3"
          class="inline-block bg-gray-100 text-gray-800 rounded-full px-2.5 py-0.5 text-xs font-medium"
        >
          +{{ certificate.skills.length - 3 }} more
        </span>
      </div>

      <!-- Issue date & Instructor -->
      <div class="flex items-center justify-between text-sm">
        <div class="text-gray-500">
          <span v-if="certificate.issueDate"
            >Issued: {{ formatDate(certificate.issueDate) }}</span
          >
          <span v-else class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1 text-yellow-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd"
              />
            </svg>
            In Progress
          </span>
        </div>

        <!-- View details button -->
        <button
          class="text-indigo-600 font-medium text-sm hover:text-indigo-800 transition-colors flex items-center group"
        >
          View Details
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 ml-1 transition-transform duration-300 transform group-hover:translate-x-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hover animation for view template button */
.opacity-0 {
  opacity: 0;
}

.group-hover\:opacity-100:hover {
  opacity: 1;
}

/* Pulsating animation for pending status */
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
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Staggered animation delays */
[style*="animation-delay"] {
  animation: fadeInUp 0.5s ease-out forwards;
  opacity: 0;
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
</style>
