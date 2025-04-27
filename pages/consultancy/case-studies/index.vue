<!-- pages/consultancy/case-studies/index.vue -->
<script setup lang="ts">
import ConsultancyProjectCard from "@/components/consultancy/ConsultancyProjectCard.vue";

// Types
interface Stat {
  label: string;
  value: string;
}

interface CaseStudy {
  id: number;
  title: string;
  client: string;
  description: string;
  image: string;
  stats: Stat[];
  tags?: string[];
  featured?: boolean;
  industry?: string;
  service?: string;
  year?: number;
}

interface FilterOptions {
  industries: string[];
  services: string[];
  years: number[];
}

interface Filters {
  industry: string;
  service: string;
  year: number | null;
  search: string;
}

// State
const caseStudies = ref<CaseStudy[]>([
  {
    id: 1,
    title: "E-commerce Platform Migration",
    client: "RetailGiant Inc.",
    description:
      "Helped transition legacy system to a modern microservices architecture, resulting in 60% faster page loads and a 37% increase in conversion rates.",
    image: "/images/courses/c2.jpg",
    stats: [
      { label: "Increase in Sales", value: "37%" },
      { label: "Page Load Time", value: "0.8s" },
      { label: "Conversion Rate", value: "+28%" },
    ],
    tags: ["E-commerce", "Microservices", "Cloud Migration"],
    featured: true,
    industry: "Retail",
    service: "Digital Transformation",
    year: 2023,
  },
  {
    id: 2,
    title: "FinTech Mobile App Development",
    client: "PayEasy Solutions",
    description:
      "Designed and developed a secure, high-performance payment application with biometric authentication, resulting in 125K+ downloads in the first quarter.",
    image: "/images/courses/c2.jpg",
    stats: [
      { label: "User Acquisition", value: "125K" },
      { label: "Transaction Time", value: "1.2s" },
      { label: "App Store Rating", value: "4.9" },
    ],
    tags: ["FinTech", "Mobile App", "Secure Payments"],
    industry: "Finance",
    service: "Custom Software Development",
    year: 2023,
  },
  {
    id: 3,
    title: "Healthcare Analytics Dashboard",
    client: "MediCorp Health Systems",
    description:
      "Created a comprehensive analytics solution that helped the client reduce operational costs by 24% while improving patient care metrics across 12 facilities.",
    image: "/images/courses/c2.jpg",
    stats: [
      { label: "Cost Reduction", value: "24%" },
      { label: "Data Sources", value: "15+" },
      { label: "ROI", value: "320%" },
    ],
    tags: ["Healthcare", "Analytics", "Dashboard"],
    industry: "Healthcare",
    service: "Data Analytics & BI",
    year: 2022,
  },
  {
    id: 4,
    title: "Supply Chain Optimization",
    client: "GlobalLogistics Ltd.",
    description:
      "Implemented an AI-powered supply chain optimization system that reduced delivery times by 32% and improved inventory forecasting accuracy to 94%.",
    image: "/images/courses/c2.jpg",
    stats: [
      { label: "Delivery Time", value: "-32%" },
      { label: "Forecast Accuracy", value: "94%" },
      { label: "Inventory Costs", value: "-18%" },
    ],
    tags: ["Supply Chain", "AI", "Logistics"],
    industry: "Logistics",
    service: "Data Analytics & BI",
    year: 2022,
  },
  {
    id: 5,
    title: "Corporate Website Redesign",
    client: "Pinnacle Insurance Group",
    description:
      "Completely redesigned the client's website with a focus on user experience, resulting in a 45% increase in lead generation and 65% improvement in user engagement.",
    image: "/images/courses/c2.jpg",
    stats: [
      { label: "Lead Generation", value: "+45%" },
      { label: "Bounce Rate", value: "-35%" },
      { label: "User Engagement", value: "+65%" },
    ],
    tags: ["UX/UI Design", "Web Development", "Insurance"],
    industry: "Insurance",
    service: "UX/UI Design",
    year: 2023,
  },
  {
    id: 6,
    title: "Cloud Migration for Manufacturing",
    client: "Industrial Dynamics Corp",
    description:
      "Migrated on-premises infrastructure to a secure cloud environment, reducing IT costs by 38% and improving system reliability with 99.99% uptime.",
    image: "/images/courses/c2.jpg",
    stats: [
      { label: "Cost Reduction", value: "38%" },
      { label: "System Uptime", value: "99.99%" },
      { label: "Scalability", value: "+300%" },
    ],
    tags: ["Manufacturing", "Cloud Migration", "DevOps"],
    featured: true,
    industry: "Manufacturing",
    service: "Cloud Migration & DevOps",
    year: 2022,
  },
]);

// UI State
const isLoading = ref(true);
const isPageVisible = ref(false);
const isMobileFiltersOpen = ref(false);
const selectedCaseStudy = ref<CaseStudy | null>(null);
const isModalOpen = ref(false);

// Filters
const filters = ref<Filters>({
  industry: "",
  service: "",
  year: null,
  search: "",
});

// Derived state
const filterOptions = computed<FilterOptions>(() => {
  return {
    industries: [
      ...new Set(caseStudies.value.map((cs) => cs.industry || "")),
    ].filter(Boolean) as string[],
    services: [
      ...new Set(caseStudies.value.map((cs) => cs.service || "")),
    ].filter(Boolean) as string[],
    years: [...new Set(caseStudies.value.map((cs) => cs.year || null))].filter(
      Boolean
    ) as number[],
  };
});

const hasActiveFilters = computed(() => {
  return (
    filters.value.industry !== "" ||
    filters.value.service !== "" ||
    filters.value.year !== null ||
    filters.value.search !== ""
  );
});

const filteredCaseStudies = computed(() => {
  let result = [...caseStudies.value];

  // Filter by industry
  if (filters.value.industry) {
    result = result.filter((cs) => cs.industry === filters.value.industry);
  }

  // Filter by service
  if (filters.value.service) {
    result = result.filter((cs) => cs.service === filters.value.service);
  }

  // Filter by year
  if (filters.value.year) {
    result = result.filter((cs) => cs.year === filters.value.year);
  }

  // Filter by search term
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    result = result.filter(
      (cs) =>
        cs.title.toLowerCase().includes(searchTerm) ||
        cs.client.toLowerCase().includes(searchTerm) ||
        cs.description.toLowerCase().includes(searchTerm) ||
        (cs.tags &&
          cs.tags.some((tag) => tag.toLowerCase().includes(searchTerm)))
    );
  }

  return result;
});

// Methods
const resetFilters = () => {
  filters.value = {
    industry: "",
    service: "",
    year: null,
    search: "",
  };
};

const toggleMobileFilters = () => {
  isMobileFiltersOpen.value = !isMobileFiltersOpen.value;
};

const viewCaseStudy = (id: number) => {
  const caseStudy = caseStudies.value.find((cs) => cs.id === id);
  if (caseStudy) {
    selectedCaseStudy.value = caseStudy;
    isModalOpen.value = true;

    // Prevent body scrolling when modal is open
    document.body.style.overflow = "hidden";
  }
};

const closeModal = () => {
  isModalOpen.value = false;

  // Re-enable body scrolling
  document.body.style.overflow = "";

  // Reset selected case study after animation completes
  setTimeout(() => {
    selectedCaseStudy.value = null;
  }, 300);
};

// Lifecycle
onMounted(() => {
  // Simulate data loading
  setTimeout(() => {
    isLoading.value = false;

    // Trigger entrance animations
    setTimeout(() => {
      isPageVisible.value = true;
    }, 100);
  }, 800);

  // Listen for escape key to close modal
  window.addEventListener("keydown", (e: KeyboardEvent) => {
    if (e.key === "Escape" && isModalOpen.value) {
      closeModal();
    }
  });
});

onBeforeUnmount(() => {
  // Cleanup
  document.body.style.overflow = "";
  window.removeEventListener("keydown", (e: KeyboardEvent) => {
    if (e.key === "Escape" && isModalOpen.value) {
      closeModal();
    }
  });
});

// Set page metadata
useHead({
  title: "Case Studies | LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Explore our success stories across various industries. See how our consultancy services have helped businesses achieve significant results.",
    },
  ],
});
</script>

<template>
  <div>
    <!-- Hero Section -->
    <section
      class="relative bg-gradient-to-br from-indigo-900 to-purple-900 text-white py-20 overflow-hidden"
    >
      <!-- Background elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div
          class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full opacity-20 -mr-40 -mt-40"
        ></div>
        <div
          class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 rounded-full opacity-20 -ml-40 -mb-40"
        ></div>

        <!-- Grid pattern -->
        <div
          class="absolute inset-0 opacity-10"
          style="
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
          "
        ></div>
      </div>

      <!-- Content -->
      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
          <div
            class="inline-block mb-4 px-4 py-1 rounded-full bg-white text-black bg-opacity-20 backdrop-blur-sm transform transition-all duration-1000"
            :class="
              isPageVisible
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
          >
            <span class="text-sm font-bold">Success Stories</span>
          </div>

          <h1
            class="text-4xl md:text-5xl font-bold mb-6 transform transition-all duration-1000 "
            :class="
              isPageVisible
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
            style="transition-delay: 200ms"
          >
            Our Case Studies
          </h1>

          <p
            class="text-xl text-indigo-100 mb-6 max-w-2xl mx-auto transform transition-all duration-1000"
            :class="
              isPageVisible
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
            style="transition-delay: 400ms"
          >
            Explore how we've helped businesses across various industries
            achieve their goals through our consultancy services.
          </p>
        </div>
      </div>
    </section>

    <!-- Loading State -->
    <div v-if="isLoading" class="py-20 flex justify-center items-center">
      <div
        class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"
      ></div>
    </div>

    <!-- Main Content -->
    <section v-else class="py-12 bg-gray-50">
      <div class="container mx-auto px-4">
        <!-- Filters -->
        <div
          class="bg-white rounded-xl shadow-lg p-6 mb-8 transform transition-all duration-1000"
          :class="
            isPageVisible
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 300ms"
        >
          <div
            class="flex flex-col md:flex-row justify-between items-center gap-4"
          >
            <!-- Search Input -->
            <div class="w-full md:w-1/3">
              <div class="relative">
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search case studies..."
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                />
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </div>
            </div>

            <!-- Desktop Filters -->
            <div class="hidden md:flex items-center space-x-4">
              <!-- Industry Filter -->
              <div class="relative">
                <select
                  v-model="filters.industry"
                  class="pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                >
                  <option value="">All Industries</option>
                  <option
                    v-for="industry in filterOptions.industries"
                    :key="industry"
                    :value="industry"
                  >
                    {{ industry }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
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

              <!-- Service Filter -->
              <div class="relative">
                <select
                  v-model="filters.service"
                  class="pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                >
                  <option value="">All Services</option>
                  <option
                    v-for="service in filterOptions.services"
                    :key="service"
                    :value="service"
                  >
                    {{ service }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
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

              <!-- Year Filter -->
              <div class="relative">
                <select
                  v-model="filters.year"
                  class="pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                >
                  <option :value="null">All Years</option>
                  <option
                    v-for="year in filterOptions.years"
                    :key="year"
                    :value="year"
                  >
                    {{ year }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
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

              <!-- Reset Button -->
              <button
                v-if="hasActiveFilters"
                @click="resetFilters"
                class="flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
                Clear Filters
              </button>
            </div>

            <!-- Mobile Filter Toggle -->
            <button
              class="md:hidden w-full bg-indigo-600 text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2 hover:bg-indigo-700 transition-colors"
              @click="toggleMobileFilters"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                  clip-rule="evenodd"
                />
              </svg>
              <span>{{
                isMobileFiltersOpen ? "Hide Filters" : "Show Filters"
              }}</span>

              <!-- Badge with count -->
              <span
                v-if="hasActiveFilters"
                class="inline-flex items-center justify-center bg-white text-indigo-600 w-5 h-5 rounded-full text-xs font-bold"
              >
                {{
                  (filters.industry ? 1 : 0) +
                  (filters.service ? 1 : 0) +
                  (filters.year ? 1 : 0)
                }}
              </span>
            </button>
          </div>

          <!-- Mobile Filters -->
          <div v-if="isMobileFiltersOpen" class="md:hidden mt-4 space-y-4">
            <!-- Industry Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Industry</label
              >
              <select
                v-model="filters.industry"
                class="w-full pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">All Industries</option>
                <option
                  v-for="industry in filterOptions.industries"
                  :key="industry"
                  :value="industry"
                >
                  {{ industry }}
                </option>
              </select>
            </div>

            <!-- Service Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Service</label
              >
              <select
                v-model="filters.service"
                class="w-full pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">All Services</option>
                <option
                  v-for="service in filterOptions.services"
                  :key="service"
                  :value="service"
                >
                  {{ service }}
                </option>
              </select>
            </div>

            <!-- Year Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Year</label
              >
              <select
                v-model="filters.year"
                class="w-full pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option :value="null">All Years</option>
                <option
                  v-for="year in filterOptions.years"
                  :key="year"
                  :value="year"
                >
                  {{ year }}
                </option>
              </select>
            </div>

            <!-- Reset Button -->
            <div v-if="hasActiveFilters">
              <button
                @click="resetFilters"
                class="w-full flex items-center justify-center py-3 text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
                Clear All Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Results Count -->
        <div
          class="mb-6 flex items-center justify-between transform transition-all duration-1000"
          :class="
            isPageVisible
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 400ms"
        >
          <h2 class="text-2xl font-bold text-gray-900">
            {{ filteredCaseStudies.length }}
            {{
              filteredCaseStudies.length === 1 ? "Case Study" : "Case Studies"
            }}
            <span
              v-if="hasActiveFilters"
              class="text-lg font-normal text-gray-600"
            >
              matched your filters
            </span>
          </h2>
        </div>

        <!-- Empty State -->
        <div
          v-if="filteredCaseStudies.length === 0"
          class="bg-white rounded-xl shadow p-8 text-center transform transition-all duration-1000"
          :class="
            isPageVisible
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 500ms"
        >
          <div
            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 mb-6"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
          <h3 class="text-xl font-bold mb-2">No case studies found</h3>
          <p class="text-gray-600 mb-6">
            We couldn't find any case studies matching your current filters. Try
            adjusting your search criteria.
          </p>
          <button
            @click="resetFilters"
            class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors"
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

        <!-- Case Studies Grid -->
        <div
          v-else
          class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 transform transition-all duration-1000"
          :class="
            isPageVisible
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 500ms"
        >
          <div
            v-for="(caseStudy, index) in filteredCaseStudies"
            :key="caseStudy.id"
            class="transform transition-opacity duration-500"
            :style="`transition-delay: ${100 + index * 100}ms;`"
          >
            <ConsultancyProjectCard
              :project="caseStudy"
              :animated="true"
              :delay="index * 100"
              @view-project="viewCaseStudy"
            />
          </div>
        </div>

        <!-- CTA Section -->
        <div
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl p-8 text-center transform transition-all duration-1000"
          :class="
            isPageVisible
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 600ms"
        >
          <h2 class="text-2xl md:text-3xl font-bold mb-4">
            Ready to Transform Your Business?
          </h2>
          <p class="text-indigo-100 mb-6 max-w-2xl mx-auto">
            Our team of experts is ready to help you achieve similar results.
            Let's discuss how we can address your unique business challenges.
          </p>
          <div class="flex flex-wrap justify-center gap-4">
            <NuxtLink
              to="/consultancy/request"
              class="bg-white text-indigo-700 font-bold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all transform hover:scale-105 shadow-lg"
            >
              Schedule a Consultation
            </NuxtLink>
            <NuxtLink
              to="/consultancy"
              class="border-2 border-white text-white  font-bold py-3 px-6 rounded-lg hover:bg-white hover:text-indigo-700 hover:bg-opacity-20 transition-all transform hover:scale-105"
            >
              Explore All Services
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Case Study Detail Modal -->
    <Transition name="modal">
      <div
        v-if="isModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="closeModal"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity"
        ></div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
          <div
            class="relative bg-white w-full max-w-4xl rounded-xl shadow-2xl transition-all transform"
            :class="
              isModalOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95'
            "
            @click.stop
          >
            <!-- Close button -->
            <button
              class="absolute top-4 right-4 z-10 bg-white rounded-full p-2 shadow-lg text-gray-500 hover:text-gray-800 transition-colors"
              @click.stop="closeModal"
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

            <div v-if="selectedCaseStudy">
              <!-- Case study image -->
              <div class="relative h-64 md:h-80">
                <img
                  :src="selectedCaseStudy.image"
                  :alt="selectedCaseStudy.title"
                  class="w-full h-full object-cover rounded-t-xl"
                />
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-70"
                ></div>
                <div class="absolute bottom-0 left-0 p-6 text-white">
                  <div class="mb-2 text-sm font-medium">
                    {{ selectedCaseStudy.client }}
                  </div>
                  <h3 class="text-2xl md:text-3xl font-bold">
                    {{ selectedCaseStudy.title }}
                  </h3>
                </div>
              </div>

              <!-- Case study content -->
              <div class="p-6 md:p-8">
                <!-- Tags and metadata -->
                <div class="flex flex-wrap gap-2 mb-6">
                  <span
                    v-for="tag in selectedCaseStudy.tags"
                    :key="tag"
                    class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full"
                  >
                    {{ tag }}
                  </span>

                  <span
                    class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full"
                  >
                    {{ selectedCaseStudy.industry }}
                  </span>

                  <span
                    class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full"
                  >
                    {{ selectedCaseStudy.service }}
                  </span>

                  <span
                    class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full"
                  >
                    {{ selectedCaseStudy.year }}
                  </span>
                </div>

                <!-- Description -->
                <div class="mb-8">
                  <h4 class="text-lg font-semibold mb-3">
                    Challenge & Solution
                  </h4>
                  <p class="text-gray-700">
                    {{ selectedCaseStudy.description }}
                  </p>
                </div>

                <!-- Results -->
                <div>
                  <h4 class="text-lg font-semibold mb-4">Results</h4>
                  <div class="grid grid-cols-3 gap-4">
                    <div
                      v-for="(stat, index) in selectedCaseStudy.stats"
                      :key="index"
                      class="bg-gray-50 rounded-lg p-4 text-center"
                    >
                      <div class="text-2xl font-bold text-indigo-600 mb-1">
                        {{ stat.value }}
                      </div>
                      <div class="text-sm text-gray-500">{{ stat.label }}</div>
                    </div>
                  </div>
                </div>

                <!-- Call to action -->
                <div
                  class="mt-8 border-t border-gray-200 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4"
                >
                  <span class="text-gray-600"
                    >Interested in similar results for your business?</span
                  >
                  <NuxtLink
                    to="/consultancy/request"
                    class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
                  >
                    Request a Consultation
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 ml-2"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
/* Transition animations */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease-out;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>
