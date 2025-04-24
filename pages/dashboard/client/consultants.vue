<!-- pages/dashboard/client/consultants/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define TypeScript interfaces
interface Consultant {
  id: number;
  name: string;
  avatar: string;
  title: string;
  specialization: string;
  skills: string[];
  rating: number;
  reviews: number;
  hourlyRate: number;
  availability: "available" | "limited" | "unavailable";
  description: string;
  projects: number;
  experience: number;
  lastActive: string;
  featured: boolean;
  verified: boolean;
}

interface FilterOptions {
  search: string;
  specialization: string;
  availability: string;
  minRating: number;
  priceRange: [number, number];
  skills: string[];
}

// Define page metadata
definePageMeta({
  layout: "dashboard-client",
});

// Page title
useHead({
  title: "Find Consultants - Client Dashboard",
});

// State variables
const consultants = ref<Consultant[]>([]);
const loading = ref(true);
const selectedConsultant = ref<Consultant | null>(null);
const showConsultantModal = ref(false);
const specializations = ref<string[]>([]);
const availableSkills = ref<string[]>([]);

// Filter state
const filters = ref<FilterOptions>({
  search: "",
  specialization: "",
  availability: "",
  minRating: 0,
  priceRange: [0, 500],
  skills: [],
});

// Sorting state
const sortBy = ref("rating");
const sortOrder = ref("desc");

// Selected skills for filtering
const selectedSkills = ref<string[]>([]);

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(6);

// Initialize with mock data
onMounted(async () => {
  await fetchConsultants();
  extractFilterOptions();
});

// Fetch consultants (mock data for demo)
const fetchConsultants = async () => {
  loading.value = true;

  // Simulate API delay
  await new Promise((resolve) => setTimeout(resolve, 800));

  // Mock data
  consultants.value = [
    {
      id: 1,
      name: "Alex Thompson",
      avatar: "/api/placeholder/100/100",
      title: "Senior Full-Stack Developer",
      specialization: "Web Development",
      skills: ["JavaScript", "Vue.js", "Node.js", "TypeScript", "MongoDB"],
      rating: 4.9,
      reviews: 127,
      hourlyRate: 120,
      availability: "available",
      description:
        "Full-stack developer with 8+ years of experience specializing in Vue.js and Node.js. I help businesses build scalable web applications with a focus on performance and user experience.",
      projects: 47,
      experience: 8,
      lastActive: "2025-04-12T14:30:00Z",
      featured: true,
      verified: true,
    },
    {
      id: 2,
      name: "Sarah Chen",
      avatar: "/api/placeholder/100/100",
      title: "UI/UX Design Specialist",
      specialization: "Design",
      skills: ["UI Design", "UX Research", "Figma", "Adobe XD", "Prototyping"],
      rating: 5.0,
      reviews: 86,
      hourlyRate: 110,
      availability: "limited",
      description:
        "Award-winning UI/UX designer with a proven track record of creating intuitive and engaging user experiences. I specialize in user-centered design and creating design systems that scale.",
      projects: 34,
      experience: 6,
      lastActive: "2025-04-14T09:45:00Z",
      featured: true,
      verified: true,
    },
    {
      id: 3,
      name: "James Wilson",
      avatar: "/api/placeholder/100/100",
      title: "DevOps Engineer",
      specialization: "DevOps",
      skills: ["AWS", "Docker", "Kubernetes", "CI/CD", "Terraform"],
      rating: 4.8,
      reviews: 59,
      hourlyRate: 140,
      availability: "available",
      description:
        "DevOps engineer with extensive experience in cloud infrastructure and automation. I help teams implement robust CI/CD pipelines and cloud infrastructure using best practices.",
      projects: 29,
      experience: 7,
      lastActive: "2025-04-15T16:20:00Z",
      featured: false,
      verified: true,
    },
    {
      id: 4,
      name: "Maria Rodriguez",
      avatar: "/api/placeholder/100/100",
      title: "Data Scientist",
      specialization: "Data Science",
      skills: [
        "Python",
        "Machine Learning",
        "Data Visualization",
        "SQL",
        "Statistics",
      ],
      rating: 4.7,
      reviews: 42,
      hourlyRate: 130,
      availability: "available",
      description:
        "Data scientist with a background in machine learning and statistical analysis. I help businesses leverage their data to gain insights and make data-driven decisions.",
      projects: 23,
      experience: 5,
      lastActive: "2025-04-13T11:15:00Z",
      featured: false,
      verified: true,
    },
    {
      id: 5,
      name: "David Kim",
      avatar: "/api/placeholder/100/100",
      title: "Mobile App Developer",
      specialization: "Mobile Development",
      skills: ["React Native", "Swift", "Firebase", "Redux", "UI Design"],
      rating: 4.9,
      reviews: 78,
      hourlyRate: 125,
      availability: "limited",
      description:
        "Mobile app developer specializing in cross-platform development using React Native. I've helped startups and enterprises launch successful mobile applications across iOS and Android.",
      projects: 36,
      experience: 6,
      lastActive: "2025-04-11T13:40:00Z",
      featured: true,
      verified: true,
    },
    {
      id: 6,
      name: "Emily Johnson",
      avatar: "/api/placeholder/100/100",
      title: "Project Manager",
      specialization: "Project Management",
      skills: [
        "Agile",
        "Scrum",
        "JIRA",
        "Risk Management",
        "Stakeholder Management",
      ],
      rating: 4.9,
      reviews: 64,
      hourlyRate: 115,
      availability: "unavailable",
      description:
        "Certified project manager with experience in leading complex software development projects. I specialize in Agile methodologies and have a track record of delivering projects on time and within budget.",
      projects: 31,
      experience: 9,
      lastActive: "2025-04-10T15:30:00Z",
      featured: false,
      verified: true,
    },
    {
      id: 7,
      name: "Robert Brown",
      avatar: "/api/placeholder/100/100",
      title: "Cybersecurity Specialist",
      specialization: "Cybersecurity",
      skills: [
        "Penetration Testing",
        "Security Auditing",
        "Network Security",
        "Encryption",
        "Risk Assessment",
      ],
      rating: 4.8,
      reviews: 47,
      hourlyRate: 150,
      availability: "available",
      description:
        "Cybersecurity expert with certifications in information security. I help organizations identify vulnerabilities, implement security best practices, and protect their digital assets.",
      projects: 26,
      experience: 8,
      lastActive: "2025-04-14T10:20:00Z",
      featured: false,
      verified: true,
    },
    {
      id: 8,
      name: "Olivia Martinez",
      avatar: "/api/placeholder/100/100",
      title: "Content Strategist",
      specialization: "Digital Marketing",
      skills: [
        "Content Strategy",
        "SEO",
        "Brand Messaging",
        "Social Media",
        "Analytics",
      ],
      rating: 4.7,
      reviews: 39,
      hourlyRate: 95,
      availability: "available",
      description:
        "Content strategist and digital marketing expert who helps businesses tell their story and reach their target audience. I specialize in creating content that drives engagement and conversions.",
      projects: 41,
      experience: 7,
      lastActive: "2025-04-15T14:50:00Z",
      featured: false,
      verified: true,
    },
    {
      id: 9,
      name: "Michael Clark",
      avatar: "/api/placeholder/100/100",
      title: "Blockchain Developer",
      specialization: "Blockchain",
      skills: ["Solidity", "Ethereum", "Smart Contracts", "Web3.js", "DeFi"],
      rating: 4.8,
      reviews: 31,
      hourlyRate: 160,
      availability: "limited",
      description:
        "Blockchain developer specializing in Ethereum and smart contract development. I help businesses leverage blockchain technology to create secure, transparent, and efficient systems.",
      projects: 19,
      experience: 5,
      lastActive: "2025-04-13T09:10:00Z",
      featured: true,
      verified: true,
    },
    {
      id: 10,
      name: "Jennifer Lee",
      avatar: "/api/placeholder/100/100",
      title: "AI Engineer",
      specialization: "Artificial Intelligence",
      skills: [
        "Machine Learning",
        "TensorFlow",
        "Natural Language Processing",
        "Computer Vision",
        "Python",
      ],
      rating: 4.9,
      reviews: 52,
      hourlyRate: 145,
      availability: "available",
      description:
        "AI engineer with expertise in machine learning and natural language processing. I help businesses implement AI solutions that automate processes and provide valuable insights.",
      projects: 24,
      experience: 6,
      lastActive: "2025-04-12T16:40:00Z",
      featured: false,
      verified: true,
    },
    {
      id: 11,
      name: "Thomas Wright",
      avatar: "/api/placeholder/100/100",
      title: "Cloud Solutions Architect",
      specialization: "Cloud Computing",
      skills: [
        "AWS",
        "Azure",
        "Cloud Migration",
        "Serverless",
        "Microservices",
      ],
      rating: 4.9,
      reviews: 73,
      hourlyRate: 135,
      availability: "available",
      description:
        "Cloud solutions architect with expertise in designing and implementing scalable and cost-effective cloud infrastructure. I help businesses leverage the cloud to improve efficiency and reduce costs.",
      projects: 39,
      experience: 9,
      lastActive: "2025-04-15T11:30:00Z",
      featured: true,
      verified: true,
    },
    {
      id: 12,
      name: "Sophia Garcia",
      avatar: "/api/placeholder/100/100",
      title: "E-commerce Specialist",
      specialization: "E-commerce",
      skills: [
        "Shopify",
        "WooCommerce",
        "Payment Integration",
        "Customer Experience",
        "Analytics",
      ],
      rating: 4.7,
      reviews: 48,
      hourlyRate: 105,
      availability: "limited",
      description:
        "E-commerce specialist with experience in setting up and optimizing online stores. I help businesses increase their online sales through improved user experience and conversion optimization.",
      projects: 32,
      experience: 7,
      lastActive: "2025-04-11T14:15:00Z",
      featured: false,
      verified: true,
    },
  ];

  loading.value = false;
};

// Extract unique specializations and skills for filter options
const extractFilterOptions = () => {
  const specs = new Set<string>();
  const skills = new Set<string>();

  consultants.value.forEach((consultant) => {
    specs.add(consultant.specialization);
    consultant.skills.forEach((skill) => skills.add(skill));
  });

  specializations.value = Array.from(specs);
  availableSkills.value = Array.from(skills);
};

// Filtered and sorted consultants
const filteredConsultants = computed(() => {
  let result = [...consultants.value];

  // Apply search filter
  if (filters.value.search) {
    const searchQuery = filters.value.search.toLowerCase();
    result = result.filter(
      (consultant) =>
        consultant.name.toLowerCase().includes(searchQuery) ||
        consultant.title.toLowerCase().includes(searchQuery) ||
        consultant.specialization.toLowerCase().includes(searchQuery) ||
        consultant.skills.some((skill) =>
          skill.toLowerCase().includes(searchQuery)
        )
    );
  }

  // Apply specialization filter
  if (filters.value.specialization) {
    result = result.filter(
      (consultant) => consultant.specialization === filters.value.specialization
    );
  }

  // Apply availability filter
  if (filters.value.availability) {
    result = result.filter(
      (consultant) => consultant.availability === filters.value.availability
    );
  }

  // Apply rating filter
  if (filters.value.minRating > 0) {
    result = result.filter(
      (consultant) => consultant.rating >= filters.value.minRating
    );
  }

  // Apply price range filter
  result = result.filter(
    (consultant) =>
      consultant.hourlyRate >= filters.value.priceRange[0] &&
      consultant.hourlyRate <= filters.value.priceRange[1]
  );

  // Apply skills filter
  if (selectedSkills.value.length > 0) {
    result = result.filter((consultant) =>
      selectedSkills.value.every((skill) => consultant.skills.includes(skill))
    );
  }

  // Apply sorting
  result.sort((a, b) => {
    let comparison = 0;

    switch (sortBy.value) {
      case "rating":
        comparison = b.rating - a.rating;
        break;
      case "price":
        comparison = a.hourlyRate - b.hourlyRate;
        break;
      case "experience":
        comparison = b.experience - a.experience;
        break;
      case "projects":
        comparison = b.projects - a.projects;
        break;
      case "name":
        comparison = a.name.localeCompare(b.name);
        break;
      default:
        comparison = b.rating - a.rating;
    }

    return sortOrder.value === "asc" ? -comparison : comparison;
  });

  return result;
});

// Computed property for pagination
const paginatedConsultants = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return filteredConsultants.value.slice(startIndex, endIndex);
});

// Total pages for pagination
const totalPages = computed(() => {
  return Math.ceil(filteredConsultants.value.length / itemsPerPage.value);
});

// Handle pagination
const changePage = (page: number) => {
  currentPage.value = page;
  // Scroll to top of results
  const resultsElement = document.getElementById("results-section");
  if (resultsElement) {
    resultsElement.scrollIntoView({ behavior: "smooth" });
  }
};

// Toggle skill selection for filtering
const toggleSkill = (skill: string) => {
  const index = selectedSkills.value.indexOf(skill);
  if (index === -1) {
    selectedSkills.value.push(skill);
  } else {
    selectedSkills.value.splice(index, 1);
  }
};

// Reset all filters
const resetFilters = () => {
  filters.value = {
    search: "",
    specialization: "",
    availability: "",
    minRating: 0,
    priceRange: [0, 500],
    skills: [],
  };
  selectedSkills.value = [];
  currentPage.value = 1;
};

// Toggle sort order
const toggleSortOrder = () => {
  sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
};

// View consultant details
const viewConsultantDetails = (consultant: Consultant) => {
  selectedConsultant.value = consultant;
  showConsultantModal.value = true;
};

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Get availability badge class
const getAvailabilityBadgeClass = (availability: string) => {
  switch (availability) {
    case "available":
      return "bg-green-100 text-green-800";
    case "limited":
      return "bg-yellow-100 text-yellow-800";
    case "unavailable":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format price with currency symbol
const formatPrice = (price: number) => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  }).format(price);
};
</script>

<template>
  <div>
    <!-- Page Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">
            Find Consultants
          </h1>
          <p class="text-gray-600">
            Discover skilled professionals for your next project
          </p>
        </div>
        <div class="mt-4 md:mt-0">
          <NuxtLink
            to="/dashboard/client/requests/create"
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
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Post a Project Request</span>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Filters Sidebar -->
      <div class="lg:col-span-1">
        <div
          class="bg-white rounded-lg shadow border border-gray-200 sticky top-4"
        >
          <!-- Filter Header -->
          <div
            class="p-4 border-b border-gray-200 flex justify-between items-center"
          >
            <h2 class="font-semibold text-lg text-gray-800">Filters</h2>
            <button
              class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors"
              @click="resetFilters"
            >
              Reset All
            </button>
          </div>

          <!-- Filter Body -->
          <div class="p-4 space-y-6">
            <!-- Search -->
            <div>
              <label
                for="search"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Search</label
              >
              <div class="relative">
                <input
                  id="search"
                  v-model="filters.search"
                  type="text"
                  placeholder="Search consultants..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
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
            </div>

            <!-- Specialization -->
            <div>
              <label
                for="specialization"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Specialization</label
              >
              <select
                id="specialization"
                v-model="filters.specialization"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">All Specializations</option>
                <option
                  v-for="spec in specializations"
                  :key="spec"
                  :value="spec"
                >
                  {{ spec }}
                </option>
              </select>
            </div>

            <!-- Availability -->
            <div>
              <label
                for="availability"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Availability</label
              >
              <select
                id="availability"
                v-model="filters.availability"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">Any Availability</option>
                <option value="available">Available Now</option>
                <option value="limited">Limited Availability</option>
                <option value="unavailable">Currently Unavailable</option>
              </select>
            </div>

            <!-- Rating -->
            <div>
              <label
                for="rating"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Minimum Rating</label
              >
              <div class="flex items-center space-x-2">
                <input
                  id="rating"
                  v-model="filters.minRating"
                  type="range"
                  min="0"
                  max="5"
                  step="0.5"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                >
                <span class="text-sm font-medium text-gray-700">{{
                  filters.minRating
                }}</span>
              </div>
              <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>Any</span>
                <span>5.0</span>
              </div>
            </div>

            <!-- Hourly Rate -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <label
                  for="hourly-rate"
                  class="text-sm font-medium text-gray-700"
                  >Hourly Rate</label
                >
                <span class="text-sm text-gray-600">
                  {{ formatPrice(filters.priceRange[0]) }} -
                  {{ formatPrice(filters.priceRange[1]) }}
                </span>
              </div>
              <input
                id="hourly-rate-min"
                v-model="filters.priceRange[0]"
                type="range"
                min="0"
                max="500"
                step="5"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mb-2"
              >
              <input
                id="hourly-rate-max"
                v-model="filters.priceRange[1]"
                type="range"
                min="0"
                max="500"
                step="5"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
              >
              <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>$0</span>
                <span>$500+</span>
              </div>
            </div>

            <!-- Skills -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >Skills</label
              >
              <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                <div
                  v-for="skill in availableSkills"
                  :key="skill"
                  class="flex items-center"
                >
                  <input
                    :id="`skill-${skill}`"
                    type="checkbox"
                    :checked="selectedSkills.includes(skill)"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    @change="toggleSkill(skill)"
                  >
                  <label
                    :for="`skill-${skill}`"
                    class="ml-2 block text-sm text-gray-700 cursor-pointer"
                  >
                    {{ skill }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Consultant Listings -->
      <div id="results-section" class="lg:col-span-3">
        <!-- Sorting & Results Count -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-6">
          <div
            class="flex flex-col md:flex-row md:items-center md:justify-between"
          >
            <div class="text-gray-700 mb-4 md:mb-0">
              <span v-if="loading">Finding consultants...</span>
              <span v-else
                >Found
                <strong>{{ filteredConsultants.length }}</strong>
                consultants</span
              >
            </div>
            <div class="flex items-center space-x-4">
              <div class="flex items-center">
              <!--   <label for="sort-by" class="mr-2 text-sm text-gray-700"
                  >Sort By:</label
                > -->
                <select
                  id="sort-by"
                  v-model="sortBy"
                  class="py-1 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                  <option value="rating">Rating</option>
                  <option value="price">Hourly Rate</option>
                  <option value="experience">Experience</option>
                  <option value="projects">Projects</option>
                  <option value="name">Name</option>
                </select>
              </div>
              <button
                class="p-1 rounded-md hover:bg-gray-100 transition-colors"
                :title="
                  sortOrder === 'asc' ? 'Sort Ascending' : 'Sort Descending'
                "
                @click="toggleSortOrder"
              >
                <svg
                  v-if="sortOrder === 'asc'"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L5.586 14.586V3a1 1 0 112 0v11.586l2.293-2.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="loading" class="space-y-6">
          <div
            v-for="i in 3"
            :key="`skeleton-${i}`"
            class="bg-white rounded-lg shadow border border-gray-200 p-6 animate-pulse"
          >
            <div class="flex space-x-4">
              <div class="rounded-full bg-gray-200 h-16 w-16"/>
              <div class="flex-1 space-y-4">
                <div class="h-4 bg-gray-200 rounded w-3/4"/>
                <div class="h-4 bg-gray-200 rounded w-1/2"/>
                <div class="h-4 bg-gray-200 rounded w-1/4"/>
              </div>
            </div>
            <div class="mt-4 space-y-3">
              <div class="h-4 bg-gray-200 rounded"/>
              <div class="h-4 bg-gray-200 rounded"/>
              <div class="grid grid-cols-3 gap-4 mt-4">
                <div class="h-8 bg-gray-200 rounded"/>
                <div class="h-8 bg-gray-200 rounded"/>
                <div class="h-8 bg-gray-200 rounded"/>
              </div>
            </div>
          </div>
        </div>

        <!-- Results Grid -->
        <div v-else-if="paginatedConsultants.length > 0" class="space-y-6">
          <div
            v-for="consultant in paginatedConsultants"
            :key="consultant.id"
            class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow duration-300"
          >
            <div class="p-6">
              <!-- Consultant Header -->
              <div class="flex items-start">
                <img
                  :src="consultant.avatar"
                  :alt="consultant.name"
                  class="h-16 w-16 rounded-full object-cover mr-4 border-2 border-indigo-100"
                >
                <div class="flex-1">
                  <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between"
                  >
                    <div>
                      <div class="flex items-center space-x-2">
                        <h3 class="text-xl font-bold text-gray-900">
                          {{ consultant.name }}
                        </h3>
                        <span
                          v-if="consultant.verified"
                          class="text-blue-500"
                          title="Verified Consultant"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </span>
                        <span
                          v-if="consultant.featured"
                          class="px-2 py-0.5 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded-full"
                        >
                          Featured
                        </span>
                      </div>
                      <p class="text-gray-600 text-sm">
                        {{ consultant.title }}
                      </p>
                    </div>
                    <div class="mt-2 md:mt-0">
                      <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                          <svg
                            v-for="i in 5"
                            :key="i"
                            xmlns="http://www.w3.org/2000/svg"
                            :class="[
                              'h-5 w-5',
                              i <= consultant.rating
                                ? 'text-yellow-400'
                                : 'text-gray-300',
                            ]"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                          </svg>
                        </div>
                        <span class="text-gray-700 font-medium">{{
                          consultant.rating.toFixed(1)
                        }}</span>
                        <span class="text-gray-500 text-sm"
                          >({{ consultant.reviews }} reviews)</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Availability & Hourly Rate -->
              <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-4 space-y-2 sm:space-y-0"
              >
                <div class="flex flex-wrap gap-2">
                  <span
                    class="px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getAvailabilityBadgeClass(consultant.availability)"
                  >
                    {{
                      consultant.availability === "available"
                        ? "Available Now"
                        : consultant.availability === "limited"
                        ? "Limited Availability"
                        : "Currently Unavailable"
                    }}
                  </span>
                  <span
                    class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full"
                  >
                    {{ consultant.specialization }}
                  </span>
                  <span
                    class="px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full"
                  >
                    {{ consultant.experience }} Years Experience
                  </span>
                  <span
                    class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full"
                  >
                    {{ consultant.projects }} Projects Completed
                  </span>
                </div>
                <div class="text-xl font-bold text-indigo-600">
                  {{ formatPrice(consultant.hourlyRate)
                  }}<span class="text-sm text-gray-500 font-normal">/hour</span>
                </div>
              </div>

              <!-- Short Description -->
              <p class="mt-4 text-gray-700">
                {{ consultant.description }}
              </p>

              <!-- Skills -->
              <div class="mt-4 flex flex-wrap gap-2">
                <span
                  v-for="skill in consultant.skills"
                  :key="`${consultant.id}-${skill}`"
                  class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-lg"
                >
                  {{ skill }}
                </span>
              </div>

              <!-- Actions -->
              <div class="mt-6 flex flex-wrap gap-3">
                <button
                  class="inline-flex items-center px-4 py-2 border border-indigo-500 text-indigo-600 font-medium rounded-lg hover:bg-indigo-50 transition-colors"
                  @click="viewConsultantDetails(consultant)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
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
                  View Profile
                </button>
                <NuxtLink
                  :to="`/dashboard/client/messages/new?consultant=${consultant.id}`"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Contact
                </NuxtLink>
                <NuxtLink
                  :to="`/dashboard/client/requests/create?consultant=${consultant.id}`"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Invite to Project
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
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
                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 13.5V15m-6 4h12a2 2 0 002-2v-12a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            No consultants found
          </h3>
          <p class="text-gray-600 mb-6">
            No consultants match your search criteria. Try adjusting your
            filters or search terms.
          </p>
          <button
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
            @click="resetFilters"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Reset Filters</span>
          </button>
        </div>

        <!-- Pagination -->
        <div
          v-if="filteredConsultants.length > itemsPerPage"
          class="mt-6 flex justify-center"
        >
          <div
            class="bg-white px-4 py-3 flex items-center justify-between border border-gray-200 rounded-lg shadow sm:px-6"
          >
            <div class="flex-1 flex justify-between">
              <button
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                :disabled="currentPage === 1"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                @click="changePage(currentPage - 1)"
              >
                Previous
              </button>
              <div class="hidden md:flex">
                <button
                  v-for="page in totalPages"
                  :key="page"
                  class="mx-1 px-4 py-2 border text-sm font-medium rounded-md transition-colors"
                  :class="
                    currentPage === page
                      ? 'border-indigo-500 bg-indigo-50 text-indigo-600'
                      : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                  "
                  @click="changePage(page)"
                >
                  {{ page }}
                </button>
              </div>
              <button
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                :disabled="currentPage === totalPages"
                :class="{
                  'opacity-50 cursor-not-allowed': currentPage === totalPages,
                }"
                @click="changePage(currentPage + 1)"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Consultant Details Modal -->
    <div
      v-if="showConsultantModal && selectedConsultant"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-lg max-w-3xl w-full shadow-xl">
        <!-- Modal Header -->
        <div
          class="flex justify-between items-start p-6 border-b border-gray-200"
        >
          <h3 class="text-xl font-semibold text-gray-900">
            Consultant Profile
          </h3>
          <button
            class="text-gray-400 hover:text-gray-500 transition-colors"
            @click="showConsultantModal = false"
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

        <!-- Modal Body -->
        <div class="p-6">
          <!-- Consultant Info -->
          <div class="flex flex-col md:flex-row md:items-start">
            <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
              <img
                :src="selectedConsultant.avatar"
                :alt="selectedConsultant.name"
                class="h-32 w-32 rounded-full object-cover border-4 border-indigo-100"
              >
            </div>
            <div class="flex-1">
              <div class="flex items-center mb-1">
                <h2 class="text-2xl font-bold text-gray-900 mr-2">
                  {{ selectedConsultant.name }}
                </h2>
                <span
                  v-if="selectedConsultant.verified"
                  class="text-blue-500"
                  title="Verified Consultant"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>
              </div>

              <p class="text-lg text-gray-700 mb-4">
                {{ selectedConsultant.title }}
              </p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                      clip-rule="evenodd"
                    />
                    <path
                      d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"
                    />
                  </svg>
                  <span class="text-gray-700">{{
                    selectedConsultant.specialization
                  }}</span>
                </div>

                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700"
                    >{{ selectedConsultant.experience }} years experience</span
                  >
                </div>

                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                    />
                  </svg>
                  <span class="text-gray-700"
                    >{{ selectedConsultant.projects }} completed projects</span
                  >
                </div>

                <div class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                    />
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span class="text-gray-700 font-bold"
                    >{{ formatPrice(selectedConsultant.hourlyRate) }}/hour</span
                  >
                </div>
              </div>

              <div class="mb-4">
                <div class="flex items-center mb-1">
                  <div class="flex items-center text-yellow-400 mr-2">
                    <svg
                      v-for="i in 5"
                      :key="i"
                      xmlns="http://www.w3.org/2000/svg"
                      :class="[
                        'h-5 w-5',
                        i <= selectedConsultant.rating
                          ? 'text-yellow-400'
                          : 'text-gray-300',
                      ]"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                  </div>
                  <span class="text-gray-800 font-medium"
                    >{{ selectedConsultant.rating.toFixed(1) }} out of 5</span
                  >
                  <span class="text-gray-600 ml-2"
                    >({{ selectedConsultant.reviews }} reviews)</span
                  >
                </div>
                <span
                  class="inline-block px-3 py-1 text-sm rounded-full mt-1"
                  :class="
                    getAvailabilityBadgeClass(selectedConsultant.availability)
                  "
                >
                  {{
                    selectedConsultant.availability === "available"
                      ? "Available Now"
                      : selectedConsultant.availability === "limited"
                      ? "Limited Availability"
                      : "Currently Unavailable"
                  }}
                </span>
              </div>

              <div class="mb-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">
                  About Me
                </h4>
                <p class="text-gray-700">
                  {{ selectedConsultant.description }}
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Skills</h4>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="skill in selectedConsultant.skills"
                    :key="`modal-${selectedConsultant.id}-${skill}`"
                    class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-lg"
                  >
                    {{ skill }}
                  </span>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-2">
                  Last Activity
                </h4>
                <p class="text-gray-700">
                  Last active: {{ formatDate(selectedConsultant.lastActive) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div
          class="p-6 border-t border-gray-200 flex flex-wrap gap-3 justify-end"
        >
          <button
            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors"
            @click="showConsultantModal = false"
          >
            Close
          </button>
          <NuxtLink
            :to="`/dashboard/client/messages/new?consultant=${selectedConsultant.id}`"
            class="px-4 py-2 border border-indigo-500 text-indigo-600 font-medium rounded-lg hover:bg-indigo-50 transition-colors"
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
                  d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                  clip-rule="evenodd"
                />
              </svg>
              Contact
            </span>
          </NuxtLink>
          <NuxtLink
            :to="`/dashboard/client/requests/create?consultant=${selectedConsultant.id}`"
            class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors"
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
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
              Invite to Project
            </span>
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Loading skeleton animation */
@keyframes pulse {
  0%,
  100% {
    opacity: 0.8;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover effects */
.consultant-card {
  transition: all 0.3s ease;
}

.consultant-card:hover {
  transform: translateY(-5px);
}

/* Custom scrollbar for skills list */
.skills-container::-webkit-scrollbar {
  width: 4px;
}

.skills-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.skills-container::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}

.skills-container::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>
