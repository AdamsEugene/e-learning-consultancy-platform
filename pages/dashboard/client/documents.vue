<!-- pages/dashboard/client/documents/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define types for document data
interface Document {
  id: string;
  name: string;
  type: string;
  size: number;
  uploadedBy: string;
  project?: string;
  category:
    | "contract"
    | "report"
    | "proposal"
    | "invoice"
    | "resource"
    | "other";
  lastModified: string;
  shared: boolean;
  starred: boolean;
  thumbnail?: string;
}

interface Project {
  id: string;
  name: string;
}

interface Folder {
  id: string;
  name: string;
  documents: number;
  icon: string;
}

// Page meta
definePageMeta({
  layout: "dashboard-client",
});

useHead({
  title: "Documents - Client Dashboard",
});

// State variables
const isLoading = ref(true);
const documents = ref<Document[]>([]);
const projects = ref<Project[]>([]);
const folders = ref<Folder[]>([]);
const searchQuery = ref("");
const currentView = ref<"grid" | "list">("grid");
const showUploadModal = ref(false);
const selectedCategory = ref<string>("all");
const selectedFilter = ref<string>("all");
const sortBy = ref("recent");
const selectedDocuments = ref<string[]>([]);
const selectAll = ref(false);
const showShareModal = ref(false);
const showDeleteModal = ref(false);
const docToShare = ref<Document | null>(null);
const uploadProgress = ref(0);
const isUploading = ref(false);
const dragActive = ref(false);

// Fetch documents (mock data)
const fetchDocuments = async () => {
  try {
    isLoading.value = true;

    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Mock projects data
    projects.value = [
      { id: "proj-1", name: "Website Redesign" },
      { id: "proj-2", name: "Mobile App Development" },
      { id: "proj-3", name: "E-commerce Integration" },
    ];

    // Mock folders data
    folders.value = [
      {
        id: "contracts",
        name: "Contracts",
        documents: 5,
        icon: "document-text",
      },
      { id: "invoices", name: "Invoices", documents: 8, icon: "cash" },
      { id: "reports", name: "Reports", documents: 12, icon: "chart-bar" },
      {
        id: "resources",
        name: "Learning Resources",
        documents: 15,
        icon: "academic-cap",
      },
    ];

    // Mock documents data
    documents.value = [
      {
        id: "doc-1",
        name: "Website_Redesign_Contract.pdf",
        type: "PDF",
        size: 2500000,
        uploadedBy: "Sarah Parker",
        project: "Website Redesign",
        category: "contract",
        lastModified: "2025-04-10T14:48:00",
        shared: true,
        starred: true,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-2",
        name: "Mobile_App_Proposal.docx",
        type: "DOCX",
        size: 1800000,
        uploadedBy: "Michael Roberts",
        project: "Mobile App Development",
        category: "proposal",
        lastModified: "2025-04-09T09:30:00",
        shared: false,
        starred: true,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-3",
        name: "Project_Timeline.xlsx",
        type: "XLSX",
        size: 950000,
        uploadedBy: "Sarah Parker",
        project: "Website Redesign",
        category: "report",
        lastModified: "2025-04-08T16:15:00",
        shared: true,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-4",
        name: "Invoice_March_2025.pdf",
        type: "PDF",
        size: 1200000,
        uploadedBy: "Alex Thompson",
        project: "E-commerce Integration",
        category: "invoice",
        lastModified: "2025-04-05T11:20:00",
        shared: false,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-5",
        name: "UI_Design_Guidelines.pdf",
        type: "PDF",
        size: 4500000,
        uploadedBy: "Sarah Parker",
        project: "Mobile App Development",
        category: "resource",
        lastModified: "2025-04-02T13:45:00",
        shared: true,
        starred: true,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-6",
        name: "Technical_Requirements.docx",
        type: "DOCX",
        size: 1600000,
        uploadedBy: "Michael Roberts",
        project: "E-commerce Integration",
        category: "report",
        lastModified: "2025-04-01T15:30:00",
        shared: false,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-7",
        name: "Marketing_Strategy.pptx",
        type: "PPTX",
        size: 3100000,
        uploadedBy: "Sarah Parker",
        project: "Website Redesign",
        category: "report",
        lastModified: "2025-03-30T10:15:00",
        shared: true,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-8",
        name: "Budget_Allocation.xlsx",
        type: "XLSX",
        size: 980000,
        uploadedBy: "Michael Roberts",
        project: "Mobile App Development",
        category: "report",
        lastModified: "2025-03-28T09:20:00",
        shared: false,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-9",
        name: "API_Documentation.pdf",
        type: "PDF",
        size: 2200000,
        uploadedBy: "Alex Thompson",
        project: "E-commerce Integration",
        category: "resource",
        lastModified: "2025-03-25T16:40:00",
        shared: true,
        starred: true,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-10",
        name: "Project_Scope.docx",
        type: "DOCX",
        size: 1400000,
        uploadedBy: "Sarah Parker",
        project: "Website Redesign",
        category: "contract",
        lastModified: "2025-03-22T14:10:00",
        shared: true,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-11",
        name: "Invoice_February_2025.pdf",
        type: "PDF",
        size: 1250000,
        uploadedBy: "Alex Thompson",
        project: "Mobile App Development",
        category: "invoice",
        lastModified: "2025-03-20T11:30:00",
        shared: false,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
      {
        id: "doc-12",
        name: "Meeting_Notes.docx",
        type: "DOCX",
        size: 850000,
        uploadedBy: "Michael Roberts",
        project: "E-commerce Integration",
        category: "report",
        lastModified: "2025-03-18T15:50:00",
        shared: false,
        starred: false,
        thumbnail: "/api/placeholder/100/130",
      },
    ];
  } catch (error) {
    console.error("Error fetching documents:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter documents based on search, category, and filters
const filteredDocuments = computed(() => {
  let result = [...documents.value];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (doc) =>
        doc.name.toLowerCase().includes(query) ||
        (doc.project && doc.project.toLowerCase().includes(query))
    );
  }

  // Apply category filter
  if (selectedCategory.value !== "all") {
    result = result.filter((doc) => doc.category === selectedCategory.value);
  }

  // Apply additional filters
  if (selectedFilter.value === "starred") {
    result = result.filter((doc) => doc.starred);
  } else if (selectedFilter.value === "shared") {
    result = result.filter((doc) => doc.shared);
  }

  // Apply sorting
  if (sortBy.value === "recent") {
    result.sort(
      (a, b) =>
        new Date(b.lastModified).getTime() - new Date(a.lastModified).getTime()
    );
  } else if (sortBy.value === "name") {
    result.sort((a, b) => a.name.localeCompare(b.name));
  } else if (sortBy.value === "size") {
    result.sort((a, b) => b.size - a.size);
  }

  return result;
});

// Document statistics
const stats = computed(() => {
  return {
    total: documents.value.length,
    shared: documents.value.filter((doc) => doc.shared).length,
    starred: documents.value.filter((doc) => doc.starred).length,
  };
});

// Toggle select all documents
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedDocuments.value = filteredDocuments.value.map((doc) => doc.id);
  } else {
    selectedDocuments.value = [];
  }
};

// Toggle document selection
const toggleDocumentSelection = (docId: string) => {
  const index = selectedDocuments.value.indexOf(docId);
  if (index === -1) {
    selectedDocuments.value.push(docId);
  } else {
    selectedDocuments.value.splice(index, 1);
  }

  // Update selectAll if all documents are selected
  selectAll.value =
    selectedDocuments.value.length === filteredDocuments.value.length;
};

// Format file size to human-readable format
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return "0 Bytes";

  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

// Format date to relative time (today, yesterday, or date)
const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();

  // Check if it's today
  if (date.toDateString() === now.toDateString()) {
    return `Today at ${date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })}`;
  }

  // Check if it's yesterday
  const yesterday = new Date(now);
  yesterday.setDate(now.getDate() - 1);
  if (date.toDateString() === yesterday.toDateString()) {
    return `Yesterday at ${date.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    })}`;
  }

  // Regular date formatting
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Get document icon based on file type
const getDocumentIcon = (type: string): string => {
  switch (type.toLowerCase()) {
    case "pdf":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "docx":
    case "doc":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "xlsx":
    case "xls":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5zm9 7a1 1 0 00-1-1h-3v3a1 1 0 001 1h2a1 1 0 001-1v-2zm-6 0a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm7-3a1 1 0 00-1-1h-6a1 1 0 110-2h5a2 2 0 012 2v1z" clip-rule="evenodd" />
      </svg>`;
    case "pptx":
    case "ppt":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "jpg":
    case "jpeg":
    case "png":
    case "gif":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
      </svg>`;
  }
};

// Get folder icon
const getFolderIcon = (iconName: string): string => {
  switch (iconName) {
    case "document-text":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    case "cash":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>`;
    case "chart-bar":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
      </svg>`;
    case "academic-cap":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
      </svg>`;
  }
};

// Get category label and color
const getCategoryInfo = (category: string) => {
  switch (category) {
    case "contract":
      return { label: "Contract", color: "bg-blue-100 text-blue-800" };
    case "proposal":
      return { label: "Proposal", color: "bg-indigo-100 text-indigo-800" };
    case "report":
      return { label: "Report", color: "bg-purple-100 text-purple-800" };
    case "invoice":
      return { label: "Invoice", color: "bg-amber-100 text-amber-800" };
    case "resource":
      return { label: "Resource", color: "bg-green-100 text-green-800" };
    default:
      return { label: "Other", color: "bg-gray-100 text-gray-800" };
  }
};

// Star a document
const toggleStar = (docId: string) => {
  const doc = documents.value.find((d) => d.id === docId);
  if (doc) {
    doc.starred = !doc.starred;
  }
};

// Share document
const shareDocument = (doc: Document) => {
  docToShare.value = doc;
  showShareModal.value = true;
};

// Confirm document sharing
const confirmShare = () => {
  if (docToShare.value) {
    docToShare.value.shared = true;
    showShareModal.value = false;
    docToShare.value = null;
  }
};

// Delete selected documents
const deleteSelectedDocuments = () => {
  documents.value = documents.value.filter(
    (doc) => !selectedDocuments.value.includes(doc.id)
  );
  selectedDocuments.value = [];
  selectAll.value = false;
  showDeleteModal.value = false;
};

// Simulate file upload
const simulateUpload = () => {
  isUploading.value = true;
  uploadProgress.value = 0;

  const interval = setInterval(() => {
    uploadProgress.value += 5;

    if (uploadProgress.value >= 100) {
      clearInterval(interval);
      setTimeout(() => {
        isUploading.value = false;
        showUploadModal.value = false;
        uploadProgress.value = 0;

        // Add a mock uploaded document
        const newDoc: Document = {
          id: `doc-${documents.value.length + 1}`,
          name: "New_Uploaded_Document.pdf",
          type: "PDF",
          size: 1650000,
          uploadedBy: "Michael Roberts",
          project: "Website Redesign",
          category: "report",
          lastModified: new Date().toISOString(),
          shared: false,
          starred: false,
          thumbnail: "/api/placeholder/100/130",
        };

        documents.value.unshift(newDoc);
      }, 500);
    }
  }, 100);
};

// Handle drag events
const onDragEnter = (e: DragEvent) => {
  e.preventDefault();
  dragActive.value = true;
};

const onDragLeave = (e: DragEvent) => {
  e.preventDefault();
  dragActive.value = false;
};

const onDragOver = (e: DragEvent) => {
  e.preventDefault();
};

const onDrop = (e: DragEvent) => {
  e.preventDefault();
  dragActive.value = false;

  // In a real app, you would handle file upload here
  // For now, just show the upload modal
  showUploadModal.value = true;
};

// Load data on mount
onMounted(() => {
  fetchDocuments();
});
</script>

<template>
  <div
    class="documents-page"
    @dragenter="onDragEnter"
    @dragleave="onDragLeave"
    @dragover="onDragOver"
    @drop="onDrop"
  >
    <!-- Drag overlay -->
    <div
      v-if="dragActive"
      class="fixed inset-0 bg-indigo-600 bg-opacity-20 backdrop-blur-sm flex items-center justify-center z-50"
    >
      <div class="bg-white p-8 rounded-xl shadow-2xl text-center">
        <div class="text-indigo-600 mb-4">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-16 w-16 mx-auto"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
            />
          </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Drop Files to Upload</h3>
        <p class="text-gray-600">Drop your files here to start uploading</p>
      </div>
    </div>

    <!-- Page header with title and buttons -->
    <div
      class="flex flex-col md:flex-row md:items-center md:justify-between mb-6"
    >
      <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Documents</h1>
        <p class="text-gray-600">
          Manage all your project documents in one place
        </p>
      </div>
      <div class="mt-4 md:mt-0 flex items-center space-x-3">
        <button
          @click="showUploadModal = true"
          class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
            />
          </svg>
          Upload
        </button>
      </div>
    </div>

    <!-- Document stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-indigo-100 rounded-full p-3 mr-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-indigo-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Total Documents</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-purple-100 rounded-full p-3 mr-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-purple-600"
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
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Shared Documents</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.shared }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-amber-100 rounded-full p-3 mr-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-amber-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Starred Documents</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.starred }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick access folders -->
    <div class="mb-8">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Access</h2>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div
          v-for="folder in folders"
          :key="folder.id"
          class="bg-white rounded-lg shadow border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer"
        >
          <div class="flex flex-col items-center text-center">
            <div
              class="mb-3 text-indigo-600"
              v-html="getFolderIcon(folder.icon)"
            ></div>
            <h3 class="font-medium text-gray-900 mb-1">{{ folder.name }}</h3>
            <p class="text-sm text-gray-500">
              {{ folder.documents }} documents
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6 p-4">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Left filters -->
        <div
          class="flex flex-wrap items-center space-x-2 space-y-2 sm:space-y-0"
        >
          <!-- Category filter -->
          <div class="flex space-x-1 overflow-x-auto">
            <button
              @click="selectedCategory = 'all'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
              :class="
                selectedCategory === 'all'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              All Documents
            </button>
            <button
              @click="selectedCategory = 'contract'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
              :class="
                selectedCategory === 'contract'
                  ? 'bg-blue-100 text-blue-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Contracts
            </button>
            <button
              @click="selectedCategory = 'proposal'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
              :class="
                selectedCategory === 'proposal'
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Proposals
            </button>
            <button
              @click="selectedCategory = 'report'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
              :class="
                selectedCategory === 'report'
                  ? 'bg-purple-100 text-purple-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Reports
            </button>
            <button
              @click="selectedCategory = 'invoice'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
              :class="
                selectedCategory === 'invoice'
                  ? 'bg-amber-100 text-amber-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Invoices
            </button>
            <button
              @click="selectedCategory = 'resource'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors whitespace-nowrap"
              :class="
                selectedCategory === 'resource'
                  ? 'bg-green-100 text-green-700'
                  : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
              "
            >
              Resources
            </button>
          </div>
        </div>

        <!-- Right controls -->
        <div class="flex flex-wrap items-center space-x-2">
          <!-- Search -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search documents..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <div class="absolute left-3 top-2.5 text-gray-400">
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

          <!-- Additional filters -->
          <div class="relative">
            <select
              v-model="selectedFilter"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="all">All Documents</option>
              <option value="starred">Starred</option>
              <option value="shared">Shared</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>

          <!-- Sort dropdown -->
          <div class="relative">
            <select
              v-model="sortBy"
              class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="recent">Recently Modified</option>
              <option value="name">Name (A-Z)</option>
              <option value="size">Size (Largest)</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>

          <!-- View toggle -->
          <div class="border border-gray-300 rounded-md overflow-hidden flex">
            <button
              @click="currentView = 'grid'"
              class="p-2 transition-colors"
              :class="
                currentView === 'grid'
                  ? 'bg-indigo-50 text-indigo-600'
                  : 'bg-white text-gray-500 hover:bg-gray-50'
              "
              title="Grid view"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                />
              </svg>
            </button>
            <button
              @click="currentView = 'list'"
              class="p-2 transition-colors"
              :class="
                currentView === 'list'
                  ? 'bg-indigo-50 text-indigo-600'
                  : 'bg-white text-gray-500 hover:bg-gray-50'
              "
              title="List view"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk actions bar (visible when documents are selected) -->
    <div
      v-if="selectedDocuments.length > 0"
      class="bg-indigo-50 border border-indigo-200 rounded-lg mb-6 p-4 flex items-center justify-between"
    >
      <div class="flex items-center">
        <input
          type="checkbox"
          id="select-all"
          v-model="selectAll"
          @change="toggleSelectAll"
          class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
        />
        <label for="select-all" class="ml-2 text-sm text-gray-700">
          {{ selectedDocuments.length }}
          {{
            selectedDocuments.length === 1 ? "document" : "documents"
          }}
          selected
        </label>
      </div>
      <div class="flex space-x-2">
        <button
          class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <span class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"
              />
              <path
                d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-5h2a1 1 0 00.97-.757l1-4A1 1 0 0014 4H3zm11 3a1 1 0 00-1-1h-1l-.117.529A1 1 0 0011 7.5v3.505a1.5 1.5 0 11-3 0V11H5.5v1.5a1.5 1.5 0 11-3 0V6a1 1 0 011-1h8.75l-.5 2H11a1 1 0 00-1 1v4a1.5 1.5 0 103 0V8.5h2.5l-.5 2H13a1 1 0 00-1 1v4.5h-1.05a2.5 2.5 0 00-4.9 0H5a1 1 0 01-1-1v-5a1 1 0 011-1h4.5a1.5 1.5 0 103 0V10a1 1 0 00-1-1H5a1 1 0 110-2h5a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v9.05a2.5 2.5 0 014.9 0H9a1 1 0 001-1v-5h2.5a1 1 0 00.97-.757l1-4A1 1 0 0014 4H3a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-5h2a1 1 0 00.97-.757l1-4A1 1 0 0014 4H3z"
              />
            </svg>
            Share
          </span>
        </button>
        <button
          class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <span class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
            Download
          </span>
        </button>
        <button
          @click="showDeleteModal = true"
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
          <span class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
              />
            </svg>
            Delete
          </span>
        </button>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div v-if="currentView === 'list'">
        <div
          v-for="i in 5"
          :key="`skeleton-list-${i}`"
          class="animate-pulse p-4 border-b border-gray-200"
        >
          <div class="flex items-center space-x-4">
            <div class="h-5 w-5 bg-gray-200 rounded"></div>
            <div class="h-12 w-12 bg-gray-200 rounded"></div>
            <div class="flex-1">
              <div class="h-4 bg-gray-200 rounded w-1/4 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-1/6"></div>
            </div>
            <div class="h-4 bg-gray-200 rounded w-20"></div>
            <div class="h-4 bg-gray-200 rounded w-24"></div>
            <div class="flex space-x-2">
              <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
              <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
              <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-else
        class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"
      >
        <div v-for="i in 10" :key="`skeleton-grid-${i}`" class="animate-pulse">
          <div class="bg-gray-200 h-40 w-full rounded-lg mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-2/4"></div>
        </div>
      </div>
    </div>

    <!-- Grid View -->
    <div
      v-else-if="currentView === 'grid' && filteredDocuments.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200 p-4"
    >
      <div
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"
      >
        <div
          v-for="doc in filteredDocuments"
          :key="doc.id"
          class="document-card bg-white border border-gray-200 rounded-lg overflow-hidden transition-all duration-200 hover:shadow-md"
        >
          <div class="relative">
            <!-- Document thumbnail -->
            <div class="h-40 bg-gray-100 flex items-center justify-center">
              <img
                v-if="doc.thumbnail"
                :src="doc.thumbnail"
                :alt="doc.name"
                class="h-full w-full object-cover"
              />
              <div
                v-else
                class="text-gray-400"
                v-html="getDocumentIcon(doc.type)"
              ></div>
            </div>

            <!-- Selection overlay -->
            <div class="absolute top-2 left-2">
              <input
                type="checkbox"
                :id="`select-doc-${doc.id}`"
                :checked="selectedDocuments.includes(doc.id)"
                @change="toggleDocumentSelection(doc.id)"
                class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
            </div>

            <!-- Star button -->
            <button
              @click="toggleStar(doc.id)"
              class="absolute top-2 right-2 bg-white bg-opacity-80 rounded-full p-1 hover:bg-opacity-100 transition-all"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                :class="doc.starred ? 'text-yellow-500' : 'text-gray-400'"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </button>

            <!-- File type badge -->
            <div
              class="absolute bottom-2 right-2 bg-gray-800 bg-opacity-75 text-white text-xs font-medium px-2 py-1 rounded"
            >
              {{ doc.type }}
            </div>
          </div>

          <div class="p-3">
            <!-- Document name -->
            <h3
              class="font-medium text-gray-900 text-sm mb-1 truncate"
              :title="doc.name"
            >
              {{ doc.name }}
            </h3>

            <!-- Document info -->
            <div class="flex items-center justify-between">
              <div class="text-xs text-gray-500">
                {{ formatFileSize(doc.size) }}
              </div>

              <div
                class="px-2 py-0.5 rounded-full text-xs font-medium"
                :class="getCategoryInfo(doc.category).color"
              >
                {{ getCategoryInfo(doc.category).label }}
              </div>
            </div>

            <!-- Document footer -->
            <div
              class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100"
            >
              <div class="text-xs text-gray-500">
                {{ formatDate(doc.lastModified) }}
              </div>

              <div class="flex space-x-1">
                <!-- Share button -->
                <button
                  v-if="!doc.shared"
                  @click="shareDocument(doc)"
                  class="p-1 text-gray-400 hover:text-indigo-600 transition-colors"
                  title="Share"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                    />
                  </svg>
                </button>

                <!-- Shared indicator -->
                <div v-else class="p-1 text-indigo-600" title="Shared">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                    />
                  </svg>
                </div>

                <!-- Download button -->
                <button
                  class="p-1 text-gray-400 hover:text-indigo-600 transition-colors"
                  title="Download"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div
      v-else-if="currentView === 'list' && filteredDocuments.length > 0"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10"
            >
              <input
                type="checkbox"
                id="select-all-list"
                v-model="selectAll"
                @change="toggleSelectAll"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Name
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Category
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Size
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Modified
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Project
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="doc in filteredDocuments"
            :key="`list-${doc.id}`"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-4 py-4 whitespace-nowrap">
              <input
                type="checkbox"
                :id="`select-list-doc-${doc.id}`"
                :checked="selectedDocuments.includes(doc.id)"
                @change="toggleDocumentSelection(doc.id)"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
            </td>
            <td class="px-4 py-4">
              <div class="flex items-center">
                <div
                  class="flex-shrink-0 h-10 w-10 flex items-center justify-center"
                  v-html="getDocumentIcon(doc.type)"
                ></div>
                <div class="ml-4">
                  <div
                    class="text-sm font-medium text-gray-900 flex items-center"
                  >
                    {{ doc.name }}
                    <button @click="toggleStar(doc.id)" class="ml-2">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        :class="
                          doc.starred ? 'text-yellow-500' : 'text-gray-300'
                        "
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </button>
                  </div>
                  <div class="text-sm text-gray-500">
                    Uploaded by {{ doc.uploadedBy }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
              <span
                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getCategoryInfo(doc.category).color"
              >
                {{ getCategoryInfo(doc.category).label }}
              </span>
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatFileSize(doc.size) }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(doc.lastModified) }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ doc.project || "—" }}
            </td>
            <td
              class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
            >
              <div class="flex justify-end space-x-2">
                <button
                  v-if="!doc.shared"
                  @click="shareDocument(doc)"
                  class="text-gray-400 hover:text-indigo-600 transition-colors"
                  title="Share"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                    />
                  </svg>
                </button>
                <div v-else class="text-indigo-600" title="Shared">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"
                    />
                  </svg>
                </div>
                <button
                  class="text-gray-400 hover:text-indigo-600 transition-colors"
                  title="Download"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
                <button
                  class="text-gray-400 hover:text-red-600 transition-colors"
                  title="Delete"
                  @click="
                    selectedDocuments = [doc.id];
                    showDeleteModal = true;
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!isLoading && filteredDocuments.length === 0"
      class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
    >
      <div
        class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-12 w-12 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery
            ? "No documents match your search criteria."
            : "You haven't uploaded any documents yet."
        }}
      </p>
      <button
        @click="showUploadModal = true"
        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
            clip-rule="evenodd"
          />
        </svg>
        <span>Upload Your First Document</span>
      </button>
    </div>

    <!-- Upload Modal -->
    <div
      v-if="showUploadModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Upload Document</h3>
          <button
            @click="showUploadModal = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
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
        </div>

        <div class="mb-6">
          <!-- Upload area -->
          <div
            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-500 transition-colors"
            :class="{ 'border-indigo-500': dragActive }"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="mx-auto h-12 w-12 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
              />
            </svg>
            <p class="mt-2 text-sm text-gray-600">
              Drag and drop files here, or
              <span
                class="text-indigo-600 font-medium hover:text-indigo-500 cursor-pointer"
                >browse</span
              >
            </p>
            <p class="mt-1 text-xs text-gray-500">
              Support for a single or bulk upload. Max file size 50MB.
            </p>
          </div>

          <!-- Upload progress -->
          <div v-if="isUploading" class="mt-4">
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium text-gray-700"
                >Uploading...</span
              >
              <span class="text-sm font-medium text-gray-700"
                >{{ uploadProgress }}%</span
              >
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-indigo-600 h-2 rounded-full"
                :style="`width: ${uploadProgress}%`"
              ></div>
            </div>
          </div>
        </div>

        <!-- Document details form -->
        <div v-if="!isUploading" class="space-y-4">
          <div>
            <label
              for="document-name"
              class="block text-sm font-medium text-gray-700"
              >Document Name</label
            >
            <input
              type="text"
              id="document-name"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Document name"
            />
          </div>

          <div>
            <label
              for="document-category"
              class="block text-sm font-medium text-gray-700"
              >Category</label
            >
            <select
              id="document-category"
              class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="contract">Contract</option>
              <option value="proposal">Proposal</option>
              <option value="report">Report</option>
              <option value="invoice">Invoice</option>
              <option value="resource">Resource</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label
              for="document-project"
              class="block text-sm font-medium text-gray-700"
              >Project</label
            >
            <select
              id="document-project"
              class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">None</option>
              <option
                v-for="project in projects"
                :key="project.id"
                :value="project.id"
              >
                {{ project.name }}
              </option>
            </select>
          </div>

          <div class="flex items-center">
            <input
              id="share-document"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label
              for="share-document"
              class="ml-2 block text-sm text-gray-900"
            >
              Share with project team
            </label>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            @click="showUploadModal = false"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            @click="simulateUpload"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="isUploading"
          >
            Upload
          </button>
        </div>
      </div>
    </div>

    <!-- Share Modal -->
    <div
      v-if="showShareModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-lg w-full p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Share Document</h3>
          <button
            @click="showShareModal = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
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
        </div>

        <div
          v-if="docToShare"
          class="bg-gray-50 p-4 rounded-lg mb-4 flex items-center"
        >
          <div
            class="flex-shrink-0 mr-4"
            v-html="getDocumentIcon(docToShare.type)"
          ></div>
          <div>
            <h4 class="font-medium text-gray-900">{{ docToShare.name }}</h4>
            <p class="text-sm text-gray-500">
              {{ formatFileSize(docToShare.size) }}
            </p>
          </div>
        </div>

        <div class="mb-4">
          <label
            for="share-access"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Share with</label
          >
          <select
            id="share-access"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="team">Project Team</option>
            <option value="company">Entire Company</option>
            <option value="specific">Specific People</option>
          </select>
        </div>

        <div class="mb-4">
          <label
            for="share-emails"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Email addresses</label
          >
          <input
            type="text"
            id="share-emails"
            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Enter email addresses"
          />
          <p class="mt-1 text-xs text-gray-500">
            Separate multiple emails with commas
          </p>
        </div>

        <div class="mb-4">
          <label
            for="share-message"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Message (optional)</label
          >
          <textarea
            id="share-message"
            rows="3"
            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Add a message"
          ></textarea>
        </div>

        <div class="mb-4">
          <div class="flex items-center">
            <input
              id="share-notify"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              checked
            />
            <label for="share-notify" class="ml-2 block text-sm text-gray-900">
              Notify people via email
            </label>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showShareModal = false"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            @click="confirmShare"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Share
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-red-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Delete {{ selectedDocuments.length > 1 ? "Documents" : "Document" }}
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete
            {{
              selectedDocuments.length > 1
                ? "these documents"
                : "this document"
            }}? This action cannot be undone.
          </p>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Cancel
          </button>
          <button
            @click="deleteSelectedDocuments"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c5c5c5;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
