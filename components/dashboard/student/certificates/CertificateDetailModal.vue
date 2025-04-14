<!-- components/dashboard/certificates/CertificateDetailModal.vue -->
<script setup lang="ts">
import { ref } from "vue";

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
  certificate: Certificate | null;
  showConfetti: boolean;
  formatDate: (date: string) => string;
}

const props = defineProps<Props>();
const emit = defineEmits(["close", "view-template"]);

// Certificate sharing options
const sharingOptions = [
  { name: "LinkedIn", icon: "linkedin", color: "bg-blue-600" },
  { name: "Twitter", icon: "twitter", color: "bg-blue-400" },
  { name: "Facebook", icon: "facebook", color: "bg-blue-800" },
  { name: "Email", icon: "mail", color: "bg-gray-600" },
  { name: "Download", icon: "download", color: "bg-green-600" },
];

// Share certificate
const shareCertificate = (platform: string) => {
  // In a real app, this would implement actual sharing functionality
  console.log(
    `Sharing certificate "${props.certificate?.title}" on ${platform}`
  );

  // Show success message
  alert(`Certificate shared on ${platform}!`);
};

// Download certificate as PDF
const downloadCertificate = () => {
  if (!props.certificate) return;

  // In a real app, this would trigger a PDF download
  console.log(`Downloading certificate "${props.certificate.title}"`);

  // Show success message
  alert("Certificate download started!");
};

// Copy credential ID to clipboard
const copyCredentialId = () => {
  if (!props.certificate?.credential.id) return;

  navigator.clipboard
    .writeText(props.certificate.credential.id)
    .then(() => {
      alert("Credential ID copied to clipboard!");
    })
    .catch((err) => {
      console.error("Failed to copy credential ID:", err);
    });
};

// View certificate template
const viewTemplate = () => {
  emit("view-template");
};

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

// Social media icon rendering
const renderSocialIcon = (iconName: string) => {
  switch (iconName) {
    case "linkedin":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
      </svg>`;
    case "twitter":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
      </svg>`;
    case "facebook":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
      </svg>`;
    case "mail":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
      </svg>`;
    case "download":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
      </svg>`;
  }
};

// Animation for confetti
const createConfetti = () => {
  return Array.from({ length: 100 }, (_, i) => ({
    left: `${Math.random() * 100}%`,
    animationDelay: `${Math.random() * 2}s`,
    backgroundColor: `hsl(${Math.random() * 360}, 100%, 50%)`,
    width: `${5 + Math.random() * 10}px`,
    height: `${5 + Math.random() * 10}px`,
    animationDuration: `${1 + Math.random() * 2}s`,
  }));
};
</script>

<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 transition-opacity duration-300"
    :class="{ 'opacity-100': true }"
    @click="$emit('close')"
  >
    <!-- Confetti animation for verified certificates -->
    <div
      v-if="showConfetti"
      class="confetti-container absolute inset-0 overflow-hidden pointer-events-none"
    >
      <div
        v-for="(confetti, i) in createConfetti()"
        :key="i"
        class="absolute w-2 h-6 opacity-70 confetti"
        :style="{
          left: confetti.left,
          top: '-20px',
          width: confetti.width,
          height: confetti.height,
          'background-color': confetti.backgroundColor,
          'animation-delay': confetti.animationDelay,
          'animation-duration': confetti.animationDuration,
        }"
      ></div>
    </div>

    <div
      class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-auto transition-transform duration-300 transform"
      :class="{ 'scale-100': true }"
      @click.stop
    >
      <!-- Certificate header with image -->
      <div class="relative">
        <img
          :src="certificate?.image"
          :alt="certificate?.title"
          class="w-full h-48 md:h-64 object-cover"
        />

        <!-- Overlay gradient -->
        <div
          class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"
        ></div>

        <!-- Close button -->
        <button
          @click="$emit('close')"
          class="absolute top-4 right-4 bg-white/10 backdrop-blur-sm text-white p-2 rounded-full hover:bg-white/20 transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>

        <!-- View template button -->
        <button
          @click="viewTemplate"
          class="absolute top-4 left-4 bg-white/10 backdrop-blur-sm text-white px-3 py-1.5 rounded-lg text-sm hover:bg-white/20 transition-colors flex items-center space-x-2"
        >
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
          <span>View Certificate Template</span>
        </button>

        <!-- Certificate title and course name -->
        <div class="absolute bottom-4 left-0 right-0 px-6 text-white">
          <h3 class="text-2xl font-bold mb-1">{{ certificate?.title }}</h3>
          <p class="text-gray-300">{{ certificate?.courseName }}</p>
        </div>
      </div>

      <!-- Certificate details -->
      <div class="p-6">
        <!-- Status, Date, Instructor -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm font-medium text-gray-500 mb-1">Status</div>
            <div
              class="inline-flex items-center py-1 px-3 rounded-full text-sm font-medium border"
              :class="
                certificate?.verificationStatus
                  ? getStatusColor(certificate.verificationStatus)
                  : ''
              "
            >
              <span
                v-if="certificate?.verificationStatus === 'verified'"
                class="w-2 h-2 bg-green-500 rounded-full mr-2"
              ></span>
              <span
                v-else-if="certificate?.verificationStatus === 'pending'"
                class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse mr-2"
              ></span>
              <span v-else class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
              {{ certificate?.verificationStatus?.charAt(0).toUpperCase()
              }}{{ certificate?.verificationStatus?.slice(1) }}
            </div>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm font-medium text-gray-500 mb-1">Issue Date</div>
            <div class="font-medium text-gray-900">
              {{
                certificate?.issueDate
                  ? formatDate(certificate.issueDate)
                  : "In Progress"
              }}
            </div>
            <div v-if="certificate?.expiryDate" class="text-sm text-gray-500">
              Expires: {{ formatDate(certificate.expiryDate) }}
            </div>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-sm font-medium text-gray-500 mb-1">Instructor</div>
            <div class="font-medium text-gray-900">
              {{ certificate?.instructor }}
            </div>
          </div>
        </div>

        <!-- Credential ID -->
        <div class="mb-6">
          <h4 class="text-sm font-medium text-gray-500 mb-2">Credential ID</h4>
          <div class="flex items-center bg-gray-50 p-4 rounded-lg">
            <div class="font-mono text-gray-900 mr-4">
              {{ certificate?.credential.id }}
            </div>
            <button
              class="text-indigo-600 hover:text-indigo-800 transition-colors"
              @click="copyCredentialId"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z" />
                <path
                  d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Skills -->
        <div class="mb-6">
          <h4 class="text-sm font-medium text-gray-500 mb-2">Skills</h4>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="(skill, idx) in certificate?.skills"
              :key="idx"
              class="inline-block bg-indigo-100 text-indigo-800 rounded-full px-3 py-1 text-sm font-medium whitespace-nowrap"
            >
              {{ skill }}
            </span>
          </div>
        </div>

        <!-- Progress bar for in-progress certificates -->
        <div v-if="certificate?.progress !== undefined" class="mb-6">
          <h4 class="text-sm font-medium text-gray-500 mb-2">
            Completion Progress
          </h4>
          <div class="flex items-center">
            <div class="flex-grow bg-gray-200 rounded-full h-3 mr-3">
              <div
                class="h-full bg-indigo-600 rounded-full transition-all duration-1000"
                :style="`width: ${certificate.progress}%`"
              ></div>
            </div>
            <div class="text-sm font-bold text-gray-700">
              {{ certificate.progress }}%
            </div>
          </div>
        </div>

        <!-- Share and download options -->
        <div class="mt-8">
          <h4 class="text-sm font-medium text-gray-500 mb-4">
            Share Your Achievement
          </h4>
          <div class="flex flex-wrap gap-3">
            <button
              v-for="option in sharingOptions"
              :key="option.name"
              class="flex items-center px-4 py-2 rounded-lg text-white transition-transform hover:scale-105 hover:shadow-md"
              :class="option.color"
              @click="
                option.name === 'Download'
                  ? downloadCertificate()
                  : shareCertificate(option.name)
              "
            >
              <span v-html="renderSocialIcon(option.icon)" class="mr-2"></span>
              <span>{{ option.name }}</span>
            </button>
          </div>
        </div>

        <!-- Certificate verification link -->
        <div class="mt-8 text-center">
          <a
            :href="certificate?.credential.url"
            target="_blank"
            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors flex items-center justify-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
            Verify this credential
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Confetti animation */
@keyframes confetti-fall {
  0% {
    transform: translateY(0) rotate(0deg);
  }
  100% {
    transform: translateY(calc(100vh + 20px)) rotate(360deg);
  }
}

.confetti {
  position: absolute;
  animation: confetti-fall linear forwards;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
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
</style>
