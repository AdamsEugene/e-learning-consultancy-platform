<!-- components/courses/CertificateTemplate.vue -->
<script setup lang="ts">
interface CertificateTemplateProps {
  studentName: string;
  courseName: string;
  instructorName: string;
  completionDate?: Date;
  certificateId?: string;
  certificateType?: "completion" | "achievement" | "excellence";
  companyLogo?: string;
  signatureImageUrl?: string;
  certificateStyle?: "classic" | "modern" | "minimalist";
  isPreview?: boolean;
}

const props = withDefaults(defineProps<CertificateTemplateProps>(), {
  completionDate: () => new Date(),
  certificateId: () => "",
  certificateType: "completion",
  companyLogo: "/api/placeholder/120/60",
  signatureImageUrl: "/api/placeholder/150/60",
  certificateStyle: "classic",
  isPreview: false,
});

const emit = defineEmits<{
  (e: "download", format: "pdf" | "png"): void;
  (e: "share", platform: "linkedin" | "twitter" | "facebook" | "email"): void;
  (e: "print"): void;
}>();

// Certificate container ref for printing/downloading
const certificateRef = ref<HTMLElement | null>(null);
const _certificateId = ref<string>(props.certificateId || "");

onMounted(() => {
  // Generate a random certificate ID if not provided
  if (!props.certificateId) {
    _certificateId.value = generateCertificateId();
  }
});

// Generate a random certificate ID if not provided
function generateCertificateId(): string {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let id = "";
  for (let i = 0; i < 8; i++) {
    id += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return id;
}

// Format the completion date
const formattedDate = computed(() => {
  return new Intl.DateTimeFormat("en-US", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(props.completionDate);
});

// Certificate background pattern and colors based on style
const certificateTheme = computed(() => {
  switch (props.certificateStyle) {
    case "modern":
      return {
        borderColor: "border-indigo-500",
        bgGradient: "bg-gradient-to-br from-indigo-50 to-purple-50",
        accentClass: "text-indigo-600 border-indigo-200 bg-indigo-50",
        titleClass: "text-indigo-800",
        patternOpacity: "opacity-5",
        borderWidth: "border-2",
        fontTitle: "font-sans",
        fontBody: "font-sans",
      };
    case "minimalist":
      return {
        borderColor: "border-gray-300",
        bgGradient: "bg-white",
        accentClass: "text-gray-800 border-gray-200 bg-gray-50",
        titleClass: "text-gray-900",
        patternOpacity: "opacity-[0.02]",
        borderWidth: "border",
        fontTitle: "font-sans",
        fontBody: "font-sans",
      };
    case "classic":
    default:
      return {
        borderColor: "border-amber-500",
        bgGradient: "bg-gradient-to-br from-amber-50 to-yellow-50",
        accentClass: "text-amber-800 border-amber-200 bg-amber-50",
        titleClass: "text-amber-900",
        patternOpacity: "opacity-5",
        borderWidth: "border-4",
        fontTitle: "font-serif",
        fontBody: "font-serif",
      };
  }
});

// Certificate title based on certificate type
const certificateTitle = computed(() => {
  switch (props.certificateType) {
    case "achievement":
      return "Certificate of Achievement";
    case "excellence":
      return "Certificate of Excellence";
    case "completion":
    default:
      return "Certificate of Completion";
  }
});

// Generate a printer-friendly version
const printCertificate = () => {
  emit("print");
  window.print();
};

// Share certificate on social media
const shareCertificate = (
  platform: "linkedin" | "twitter" | "facebook" | "email"
) => {
  emit("share", platform);

  const shareText = `I've completed the "${props.courseName}" course!`;
  const shareUrl = window.location.href;

  switch (platform) {
    case "linkedin":
      window.open(
        `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(
          shareUrl
        )}`,
        "_blank"
      );
      break;
    case "twitter":
      window.open(
        `https://twitter.com/intent/tweet?text=${encodeURIComponent(
          shareText
        )}&url=${encodeURIComponent(shareUrl)}`,
        "_blank"
      );
      break;
    case "facebook":
      window.open(
        `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
          shareUrl
        )}`,
        "_blank"
      );
      break;
    case "email":
      window.open(
        `mailto:?subject=${encodeURIComponent(
          `Certificate for ${props.courseName}`
        )}&body=${encodeURIComponent(`${shareText}\n\n${shareUrl}`)}`,
        "_blank"
      );
      break;
  }
};

// Download certificate as PDF or PNG
const downloadCertificate = (format: "pdf" | "png") => {
  emit("download", format);

  // In a real implementation, this would trigger a server-side PDF generation API
  // or use a library like html2canvas + jsPDF, but for demo purposes we'll use a mock
  alert(`Downloading certificate as ${format.toUpperCase()}...`);
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="certificate-container">
    <!-- Certificate preview -->
    <div
      ref="certificateRef"
      :class="[
        'certificate relative p-10 mx-auto max-w-3xl rounded-lg shadow-lg overflow-hidden',
        certificateTheme.bgGradient,
        certificateTheme.borderWidth,
        certificateTheme.borderColor,
        certificateTheme.fontBody,
      ]"
      style="
        aspect-ratio: 1.414;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
      "
    >
      <!-- Background pattern -->
      <div
        :class="[
          'absolute inset-0 pattern-texture',
          certificateTheme.patternOpacity,
        ]"
        aria-hidden="true"
      />

      <!-- Certificate content -->
      <div
        class="relative z-10 h-full flex flex-col justify-between items-center text-center"
      >
        <!-- Header with logo -->
        <div class="w-full flex justify-between items-center mb-4">
          <div class="flex-1">
            <img src="" alt="Company Logo" class="h-10 object-contain" />
          </div>
          <div class="flex-1 text-right text-xs text-gray-500">
            Certificate ID: {{ _certificateId }}
          </div>
        </div>

        <!-- Certificate title -->
        <div class="my-6">
          <div
            :class="[
              'text-sm uppercase tracking-wider mb-2',
              certificateTheme.accentClass,
            ]"
          >
            LearnConsult Presents
          </div>
          <h1
            :class="[
              'text-4xl mb-2 font-bold',
              certificateTheme.titleClass,
              certificateTheme.fontTitle,
            ]"
          >
            {{ certificateTitle }}
          </h1>
          <div
            class="w-24 h-1 mx-auto rounded bg-current mt-1 mb-6"
            :class="certificateTheme.accentClass"
          />
          <div class="text-lg text-gray-800">This certifies that</div>
        </div>

        <!-- Student name -->
        <div class="my-4">
          <h2
            class="text-3xl font-bold mb-2 text-gray-900 border-b-2 border-gray-300 pb-1 px-12"
          >
            {{ studentName }}
          </h2>
          <div class="text-lg text-gray-800 mt-4">
            has successfully completed the course
          </div>
        </div>

        <!-- Course name -->
        <div class="my-4">
          <h3
            :class="[
              'text-2xl font-bold text-gray-900 border-2 px-8 py-3 rounded-lg',
              certificateTheme.accentClass,
            ]"
          >
            {{ courseName }}
          </h3>
        </div>

        <!-- Date and signatures -->
        <div class="mt-auto w-full">
          <div class="flex justify-between items-end">
            <div class="text-center w-1/2">
              <div class="border-t-2 border-gray-400 pt-1 max-w-xs mx-auto">
                <div class="text-sm text-gray-700">{{ formattedDate }}</div>
                <div class="text-xs text-gray-500">Date of Completion</div>
              </div>
            </div>

            <div class="text-center w-1/2">
              <div>
                <img
                  src=""
                  alt="Instructor Signature"
                  class="h-10 object-contain mx-auto mb-1"
                />
                <div class="border-t-2 border-gray-400 pt-1 max-w-xs mx-auto">
                  <div class="text-sm text-gray-700">{{ instructorName }}</div>
                  <div class="text-xs text-gray-500">Course Instructor</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Certificate actions (hidden in print view) -->
    <div
      class="certificate-actions mt-6 flex flex-wrap justify-center gap-4"
      :class="{ hidden: !isPreview }"
    >
      <!-- Print certificate -->
      <button
        class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
        @click="printCertificate"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 text-gray-500"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"
          />
        </svg>
        Print
      </button>

      <!-- Download as PDF -->
      <button
        class="flex items-center px-4 py-2 bg-indigo-600 rounded-lg text-white hover:bg-indigo-700 transition-colors"
        @click="downloadCertificate('pdf')"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
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
        Download PDF
      </button>

      <!-- Download as PNG -->
      <button
        class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
        @click="downloadCertificate('png')"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 text-gray-500"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        Download PNG
      </button>

      <!-- Share options -->
      <div class="relative group">
        <button
          class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2 text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
            />
          </svg>
          Share
        </button>

        <!-- Share dropdown -->
        <div
          class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg p-1 hidden group-hover:block z-10"
        >
          <button
            class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-md"
            @click="shareCertificate('linkedin')"
          >
            <svg
              class="h-5 w-5 mr-2 text-blue-600"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
              />
            </svg>
            LinkedIn
          </button>

          <button
            class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-md"
            @click="shareCertificate('twitter')"
          >
            <svg
              class="h-5 w-5 mr-2 text-blue-400"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
              />
            </svg>
            Twitter
          </button>

          <button
            class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-md"
            @click="shareCertificate('facebook')"
          >
            <svg
              class="h-5 w-5 mr-2 text-blue-600"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"
              />
            </svg>
            Facebook
          </button>

          <button
            class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 rounded-md"
            @click="shareCertificate('email')"
          >
            <svg
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
              />
              <polyline points="22,6 12,13 2,6" />
            </svg>
            Email
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Certificate pattern texture */
.pattern-texture {
  background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23000000' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
}

/* Print styles */
@media print {
  body {
    background: #fff;
  }

  .certificate-container {
    padding: 0;
    margin: 0;
  }

  .certificate {
    box-shadow: none;
    margin: 0;
    padding: 10mm;
    max-width: none;
    width: 100%;
    height: 100%;
  }

  .certificate-actions {
    display: none !important;
  }
}
</style>
