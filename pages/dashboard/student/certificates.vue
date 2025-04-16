<!-- pages/dashboard/certificates.vue -->
<script setup lang="ts">
// Set page layout
definePageMeta({
  layout: "dashboard-student",
});

// Certificate interface
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
  progress?: number; // For in-progress certificates
  templateId?: string; // Reference to certificate template
}

// State
const isLoading = ref(true);
const isLoaded = ref(false);
const activeTab = ref<"all" | "featured" | "in-progress">("all");
const selectedStatus = ref<"all" | "verified" | "pending" | "expired">("all");
const searchQuery = ref("");
const showConfetti = ref(false);
const currentCertificate = ref<Certificate | null>(null);
const showCertificateDetail = ref(false);
const showTemplateViewer = ref(false);

// Mock certificates data
const allCertificates = ref<Certificate[]>([
  {
    id: 1,
    title: "Web Development Masterclass",
    courseId: 1,
    courseName: "Complete Web Development Bootcamp",
    issueDate: "2023-09-15T10:30:00",
    instructor: "Alex Johnson",
    credential: {
      id: "WD-CERT-2023-78934",
      url: "/certificates/web-dev-masterclass",
    },
    skills: ["HTML5", "CSS3", "JavaScript", "React", "Node.js"],
    image: "/images/courses/c3.jpg",
    featured: true,
    verificationStatus: "verified",
    templateId: "template-1",
  },
  {
    id: 2,
    title: "UX/UI Design Fundamentals",
    courseId: 2,
    courseName: "UI/UX Design Masterclass",
    issueDate: "2023-07-22T14:45:00",
    instructor: "Sarah Chen",
    credential: {
      id: "UXUI-CERT-2023-45672",
      url: "/certificates/uxui-design-fundamentals",
    },
    skills: [
      "User Research",
      "Wireframing",
      "Prototyping",
      "Figma",
      "Design Systems",
    ],
    image: "/images/courses/c3.jpg",
    verificationStatus: "verified",
    templateId: "template-2",
  },
  {
    id: 3,
    title: "Advanced JavaScript Programming",
    courseId: 3,
    courseName: "JavaScript Algorithms and Data Structures",
    issueDate: "2023-05-10T09:15:00",
    expiryDate: "2025-05-10T09:15:00",
    instructor: "Michael Brown",
    credential: {
      id: "JS-ADV-2023-23456",
      url: "/certificates/advanced-javascript",
    },
    skills: [
      "ES6+",
      "Data Structures",
      "Algorithms",
      "Performance Optimization",
      "Async Programming",
    ],
    image: "/images/courses/c3.jpg",
    verificationStatus: "verified",
    templateId: "template-3",
  },
  {
    id: 4,
    title: "Mobile Application Development",
    courseId: 4,
    courseName: "Mobile App Development with React Native",
    issueDate: "2023-10-05T11:20:00",
    instructor: "Emma Wilson",
    credential: {
      id: "MOBILE-CERT-2023-89012",
      url: "/certificates/mobile-app-development",
    },
    skills: [
      "React Native",
      "Mobile UI",
      "App Architecture",
      "API Integration",
      "App Store Deployment",
    ],
    image: "/images/courses/c3.jpg",
    featured: true,
    verificationStatus: "verified",
    templateId: "template-1",
  },
  {
    id: 5,
    title: "Cybersecurity Fundamentals",
    courseId: 5,
    courseName: "Introduction to Cybersecurity",
    issueDate: "2022-11-18T15:30:00",
    expiryDate: "2023-11-18T15:30:00",
    instructor: "James Wilson",
    credential: {
      id: "CYBER-CERT-2022-56789",
      url: "/certificates/cybersecurity-fundamentals",
    },
    skills: [
      "Network Security",
      "Encryption",
      "Threat Assessment",
      "Security Protocols",
      "Ethical Hacking",
    ],
    image: "/images/courses/c3.jpg",
    verificationStatus: "expired",
    templateId: "template-2",
  },
  {
    id: 6,
    title: "Data Science Essentials",
    courseId: 6,
    courseName: "Data Science and Machine Learning",
    issueDate: "2023-01-30T13:45:00",
    instructor: "David Zhang",
    credential: {
      id: "DATA-CERT-2023-34567",
      url: "/certificates/data-science-essentials",
    },
    skills: [
      "Python",
      "Data Analysis",
      "Statistical Methods",
      "Visualization",
      "Machine Learning Basics",
    ],
    image: "/images/courses/c3.jpg",
    progress: 85,
    verificationStatus: "pending",
    templateId: "template-3",
  },
  {
    id: 7,
    title: "Cloud Computing Architecture",
    courseId: 7,
    courseName: "AWS Cloud Architect Certification",
    issueDate: "",
    instructor: "Robert Miller",
    credential: {
      id: "CLOUD-CERT-2023-00000",
      url: "/certificates/cloud-computing",
    },
    skills: [
      "AWS",
      "Cloud Infrastructure",
      "Serverless Computing",
      "Microservices",
      "DevOps",
    ],
    image: "/images/courses/c3.jpg",
    progress: 60,
    verificationStatus: "pending",
    templateId: "template-1",
  },
  {
    id: 8,
    title: "Digital Marketing Specialist",
    courseId: 8,
    courseName: "Digital Marketing Masterclass",
    issueDate: "",
    instructor: "Lisa Johnson",
    credential: {
      id: "MKTG-CERT-2023-00000",
      url: "/certificates/digital-marketing",
    },
    skills: [
      "SEO",
      "Social Media Marketing",
      "Content Strategy",
      "Analytics",
      "Email Campaigns",
    ],
    image: "/images/courses/c3.jpg",
    progress: 35,
    verificationStatus: "pending",
    templateId: "template-2",
  },
]);

// Certificate stats
const certificateStats = computed(() => {
  return {
    completed: allCertificates.value.filter(
      (cert) => cert.verificationStatus === "verified"
    ).length,
    inProgress: allCertificates.value.filter(
      (cert) => cert.progress !== undefined
    ).length,
    featured: allCertificates.value.filter((cert) => cert.featured).length,
    expired: allCertificates.value.filter(
      (cert) => cert.verificationStatus === "expired"
    ).length,
  };
});

// Filtered certificates
const filteredCertificates = computed(() => {
  let certificates = [...allCertificates.value];

  // Filter by tab
  if (activeTab.value === "featured") {
    certificates = certificates.filter((cert) => cert.featured);
  } else if (activeTab.value === "in-progress") {
    certificates = certificates.filter((cert) => cert.progress !== undefined);
  }

  // Filter by status
  if (selectedStatus.value !== "all") {
    certificates = certificates.filter(
      (cert) => cert.verificationStatus === selectedStatus.value
    );
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    certificates = certificates.filter(
      (cert) =>
        cert.title.toLowerCase().includes(query) ||
        cert.courseName.toLowerCase().includes(query) ||
        cert.instructor.toLowerCase().includes(query) ||
        cert.skills.some((skill) => skill.toLowerCase().includes(query))
    );
  }

  return certificates;
});

// Format date utility function
const formatDate = (dateString: string) => {
  if (!dateString) return "In Progress";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  });
};

// Handle tab change
const handleTabChange = (tab: "all" | "featured" | "in-progress") => {
  activeTab.value = tab;
};

// Handle filter change
const handleFilterChange = (
  status: "all" | "verified" | "pending" | "expired",
  query: string
) => {
  selectedStatus.value = status;
  searchQuery.value = query;
};

// View certificate details
const viewCertificateDetails = (certificate: Certificate) => {
  currentCertificate.value = certificate;
  showCertificateDetail.value = true;

  // Show confetti animation for verified certificates
  if (certificate.verificationStatus === "verified") {
    setTimeout(() => {
      showConfetti.value = true;
      setTimeout(() => {
        showConfetti.value = false;
      }, 3000);
    }, 300);
  }
};

// Close certificate modal
const closeCertificateModal = () => {
  showCertificateDetail.value = false;
  setTimeout(() => {
    currentCertificate.value = null;
    showConfetti.value = false;
  }, 300);
};

// View certificate template
const viewCertificateTemplate = (certificate: Certificate) => {
  currentCertificate.value = certificate;
  showTemplateViewer.value = true;
};

// Close template viewer
const closeTemplateViewer = () => {
  showTemplateViewer.value = false;
};

// Reset all filters
const resetFilters = () => {
  selectedStatus.value = "all";
  searchQuery.value = "";
  activeTab.value = "all";
};

// Load data with simulated delay
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;

    // Small delay for loading animation
    setTimeout(() => {
      isLoaded.value = true;
    }, 100);
  }, 800);
});
</script>

<template>
  <div>
    <!-- Certificates Header with Statistics -->
    <DashboardStudentCertificatesHeader
      :stats="certificateStats"
      :is-loaded="isLoaded"
    />

    <!-- Tabs and Filter Controls -->
    <DashboardStudentCertificatesFilter
      :active-tab="activeTab"
      :selected-status="selectedStatus"
      :search-query="searchQuery"
      :is-loaded="isLoaded"
      @tab-change="handleTabChange"
      @filter-change="handleFilterChange"
    />

    <!-- Certificates Grid -->
    <div
      class="transform transition-all duration-500"
      :class="
        isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
      "
      style="transition-delay: 300ms"
    >
      <!-- Loading state -->
      <div
        v-if="isLoading"
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 flex justify-center"
      >
        <div
          class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
        ></div>
      </div>

      <!-- Empty state -->
      <div
        v-else-if="filteredCertificates.length === 0"
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center"
      >
        <div
          class="bg-indigo-100 rounded-full p-4 w-16 h-16 flex items-center justify-center mx-auto mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10 text-indigo-600"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"
            />
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">
          No certificates found
        </h3>
        <p class="text-gray-600 mb-6">
          We couldn't find any certificates that match your current filters.
        </p>
        <button
          @click="resetFilters"
          class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition-colors"
        >
          Clear Filters
        </button>
      </div>

      <!-- Certificates grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <DashboardStudentCertificatesCertificateCard
          v-for="(certificate, index) in filteredCertificates"
          :key="certificate.id"
          :certificate="certificate"
          :index="index"
          :format-date="formatDate"
          @view-details="viewCertificateDetails(certificate)"
          @view-template="viewCertificateTemplate(certificate)"
        />
      </div>
    </div>

    <!-- Certificate Detail Modal -->
    <DashboardStudentCertificatesCertificateDetailModal
      v-if="showCertificateDetail"
      :certificate="currentCertificate"
      :show-confetti="showConfetti"
      :format-date="formatDate"
      @close="closeCertificateModal"
      @view-template="viewCertificateTemplate(currentCertificate!)"
    />

    <!-- Certificate Template Viewer -->
    <DashboardStudentCertificatesCertificateTemplateViewer
      v-if="showTemplateViewer"
      :certificate="currentCertificate"
      :format-date="formatDate"
      @close="closeTemplateViewer"
    />
  </div>
</template>

<style scoped>
/* Certificate animation */
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

/* Staggered animation delays */
[style*="animation-delay"] {
  animation: fadeInUp 0.5s ease-out forwards;
  opacity: 0;
}
</style>
