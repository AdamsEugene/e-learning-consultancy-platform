<!-- components/dashboard/certificates/CertificateTemplateViewer.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

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
  formatDate: (date: string) => string;
}

const props = defineProps<Props>();
const emit = defineEmits(["close"]);

// Load template data based on templateId
const template = computed(() => {
  if (!props.certificate?.templateId) return templates.value[0];

  const found = templates.value.find(
    (t) => t.id === props.certificate?.templateId
  );
  return found || templates.value[0];
});

// User name (in a real app this would come from user data)
const userName = ref("Emily Johnson");

// Scale transformation for certificate zoom
const scale = ref(1);
const isFullscreen = ref(false);

// Certificate templates (in a real app these would be loaded from the server)
const templates = ref([
  {
    id: "template-1",
    name: "Professional Blue",
    background: "linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)",
    borderColor: "#4338ca",
    textColor: "#1e40af",
    accentColor: "#3b82f6",
    font: "Helvetica, Arial, sans-serif",
    logo: "/images/courses/c3.jpg",
  },
  {
    id: "template-2",
    name: "Elegant Green",
    background: "linear-gradient(135deg, #e6fffa 0%, #d1fae5 100%)",
    borderColor: "#047857",
    textColor: "#065f46",
    accentColor: "#10b981",
    font: "Georgia, serif",
    logo: "/images/courses/c3.jpg",
  },
  {
    id: "template-3",
    name: "Modern Purple",
    background: "linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%)",
    borderColor: "#7e22ce",
    textColor: "#6b21a8",
    accentColor: "#8b5cf6",
    font: "Montserrat, sans-serif",
    logo: "/images/courses/c3.jpg",
  },
]);

// Download certificate as PDF
const downloadCertificate = () => {
  if (!props.certificate) return;

  // In a real app, this would trigger a PDF download
  console.log(`Downloading certificate "${props.certificate.title}"`);

  // Show success message
  alert("Certificate download started!");
};

// Print certificate
const printCertificate = () => {
  window.print();
};

// Zoom in/out
const zoomIn = () => {
  if (scale.value < 2) {
    scale.value += 0.1;
  }
};

const zoomOut = () => {
  if (scale.value > 0.5) {
    scale.value -= 0.1;
  }
};

const resetZoom = () => {
  scale.value = 1;
};

// Toggle fullscreen
const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value;

  if (isFullscreen.value) {
    resetZoom();
  }
};

// Format certificate date nicely
const formattedDate = computed(() => {
  if (!props.certificate?.issueDate) return "";

  const date = new Date(props.certificate.issueDate);
  return date.toLocaleDateString("en-US", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
});

// Generate random signature for instructor
const generateSignature = (name: string) => {
  // This is a placeholder - in a real app you'd use an actual signature image
  return `${name.split(" ")[0][0]}${name.split(" ")[1][0]}`;
};

// Keyboard shortcuts for zoom
onMounted(() => {
  const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === "Escape") {
      emit("close");
    } else if (event.key === "+" || event.key === "=") {
      zoomIn();
    } else if (event.key === "-") {
      zoomOut();
    } else if (event.key === "0") {
      resetZoom();
    } else if (event.key === "f") {
      toggleFullscreen();
    }
  };

  window.addEventListener("keydown", handleKeyDown);

  return () => {
    window.removeEventListener("keydown", handleKeyDown);
  };
});
</script>

<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4 transition-all duration-300"
    :class="{ 'p-0': isFullscreen }"
    @click="$emit('close')"
  >
    <div
      class="bg-white rounded-xl shadow-2xl transition-all duration-300 relative flex flex-col"
      :class="
        isFullscreen
          ? 'w-full h-full rounded-none'
          : 'max-w-4xl w-full max-h-[90vh]'
      "
      @click.stop
    >
      <!-- Toolbar -->
      <div
        class="bg-gray-100 p-4 border-b border-gray-200 flex items-center justify-between"
      >
        <div class="flex items-center space-x-4">
          <h3 class="font-bold text-gray-800">Certificate Template</h3>
          <div class="text-sm text-gray-500">{{ template.name }}</div>
        </div>

        <div class="flex items-center space-x-3">
          <!-- Zoom controls -->
          <div
            class="flex items-center bg-white rounded-lg border border-gray-300 overflow-hidden"
          >
            <button
              @click="zoomOut"
              class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors"
              title="Zoom out (- key)"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>

            <div
              class="px-2 py-1 border-l border-r border-gray-300 min-w-[60px] text-center"
            >
              {{ Math.round(scale * 100) }}%
            </div>

            <button
              @click="zoomIn"
              class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors"
              title="Zoom in (+ key)"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>

          <!-- Reset zoom -->
          <button
            @click="resetZoom"
            class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors rounded-lg border border-gray-300"
            title="Reset zoom (0 key)"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
          </button>

          <!-- Fullscreen toggle -->
          <button
            @click="toggleFullscreen"
            class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors rounded-lg border border-gray-300"
            title="Toggle fullscreen (F key)"
          >
            <svg
              v-if="!isFullscreen"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>

          <!-- Download button -->
          <button
            @click="downloadCertificate"
            class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors rounded-lg border border-gray-300"
            title="Download certificate"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
              />
            </svg>
          </button>

          <!-- Print button -->
          <button
            @click="printCertificate"
            class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors rounded-lg border border-gray-300"
            title="Print certificate"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
              />
            </svg>
          </button>

          <!-- Close button -->
          <button
            @click="$emit('close')"
            class="p-1.5 text-gray-600 hover:bg-gray-100 transition-colors rounded-lg border border-gray-300"
            title="Close (Esc key)"
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
        </div>
      </div>

      <!-- Certificate viewer -->
      <div
        class="flex-grow overflow-auto p-8 flex items-center justify-center bg-gray-200 print:bg-white"
      >
        <!-- Certificate container with zoom applied -->
        <div
          class="certificate-container transition-transform duration-200 bg-white shadow-xl print:shadow-none"
          :style="{
            transform: `scale(${scale})`,
            transformOrigin: 'center center',
          }"
        >
          <!-- Certificate template -->
          <div
            class="certificate-template w-[800px] h-[600px] relative p-12 border-8"
            :style="{
              background: template.background,
              borderColor: template.borderColor,
              color: template.textColor,
              fontFamily: template.font,
            }"
          >
            <!-- Background design elements -->
            <div class="absolute inset-0 overflow-hidden">
              <!-- Decorative corner elements -->
              <div
                class="absolute top-4 left-4 w-32 h-32 rounded-full opacity-10"
                :style="{ background: template.accentColor }"
              ></div>
              <div
                class="absolute bottom-4 right-4 w-32 h-32 rounded-full opacity-10"
                :style="{ background: template.accentColor }"
              ></div>

              <!-- Decorative lines -->
              <div
                class="absolute top-20 left-20 right-20 h-0.5 opacity-10"
                :style="{ background: template.accentColor }"
              ></div>
              <div
                class="absolute bottom-20 left-20 right-20 h-0.5 opacity-10"
                :style="{ background: template.accentColor }"
              ></div>
            </div>

            <!-- Certificate content -->
            <div
              class="relative z-10 h-full flex flex-col items-center justify-between text-center"
            >
              <!-- Header with logo -->
              <div class="flex flex-col items-center">
                <img
                  :src="template.logo"
                  alt="Organization Logo"
                  class="w-16 h-16 object-contain mb-4"
                />
                <h1
                  class="text-3xl font-bold uppercase tracking-wide mb-1"
                  :style="{ color: template.textColor }"
                >
                  Certificate of Completion
                </h1>
                <div
                  class="w-32 h-1 rounded-full"
                  :style="{ background: template.accentColor }"
                ></div>
              </div>

              <!-- Main certificate content -->
              <div class="my-8 flex flex-col items-center">
                <p class="text-lg mb-6">This is to certify that</p>
                <h2
                  class="text-4xl font-bold mb-6 border-b-2 pb-2 px-8"
                  :style="{ borderColor: template.accentColor }"
                >
                  {{ userName }}
                </h2>
                <p class="text-lg mb-6">has successfully completed</p>
                <h3
                  class="text-3xl font-bold mb-4"
                  :style="{ color: template.accentColor }"
                >
                  {{ certificate?.title }}
                </h3>
                <p class="text-lg mb-2">in the course</p>
                <p class="text-xl font-medium mb-4">
                  {{ certificate?.courseName }}
                </p>
                <p class="text-lg">on {{ formattedDate }}</p>
              </div>

              <!-- Footer with signatures -->
              <div class="w-full flex justify-between items-end">
                <!-- Instructor signature -->
                <div class="text-center">
                  <div
                    class="w-32 h-16 mb-2 mx-auto flex items-center justify-center border-b"
                    :style="{ borderColor: template.borderColor }"
                  >
                    <span class="text-2xl font-bold italic">{{
                      generateSignature(certificate?.instructor || "")
                    }}</span>
                  </div>
                  <p class="text-sm font-medium">
                    {{ certificate?.instructor }}
                  </p>
                  <p class="text-xs">Instructor</p>
                </div>

                <!-- Certificate ID -->
                <div class="text-center">
                  <div
                    class="w-32 h-32 rounded-full mb-2 mx-auto border-2 flex items-center justify-center"
                    :style="{
                      borderColor: template.borderColor,
                      background: `${template.accentColor}10`,
                    }"
                  >
                    <div class="text-xs">
                      <div>Certificate ID</div>
                      <div class="font-mono font-bold mt-1">
                        {{ certificate?.credential.id }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Organization signature -->
                <div class="text-center">
                  <div
                    class="w-32 h-16 mb-2 mx-auto flex items-center justify-center border-b"
                    :style="{ borderColor: template.borderColor }"
                  >
                    <span class="text-2xl font-bold">LC</span>
                  </div>
                  <p class="text-sm font-medium">LearnConsult</p>
                  <p class="text-xs">Organization</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

/* Print styles */
@media print {
  .certificate-container {
    transform: none !important;
    box-shadow: none !important;
  }

  body * {
    visibility: hidden;
  }

  .certificate-template,
  .certificate-template * {
    visibility: visible;
  }

  .certificate-template {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
  }
}
</style>
