<!-- pages/consultancy/case-studies/[id].vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

// Type definitions
interface Stat {
  label: string;
  value: string;
}

interface Testimonial {
  content: string;
  author: string;
  role: string;
  company: string;
}

interface RelatedCaseStudy {
  id: number;
  title: string;
  image: string;
  industry: string;
}

interface CaseStudy {
  id: number;
  title: string;
  client: string;
  industry: string;
  service: string;
  duration: string;
  year: number;
  description: string;
  challenge: string;
  approach: string;
  solution: string;
  results: string;
  image: string;
  gallery?: string[];
  stats: Stat[];
  testimonial?: Testimonial;
  tags?: string[];
  related?: RelatedCaseStudy[];
}

// State
const isLoading = ref(true);
const isError = ref(false);
const isPageVisible = ref(false);
const caseStudy = ref<CaseStudy | null>(null);

// Route
const route = useRoute();
const router = useRouter();
const caseStudyId = computed(() => parseInt(route.params.id as string));

// Mock data for this case study
const mockCaseStudyData: Record<number, CaseStudy> = {
  1: {
    id: 1,
    title: "E-commerce Platform Migration",
    client: "RetailGiant Inc.",
    industry: "Retail",
    service: "Digital Transformation",
    duration: "6 months",
    year: 2023,
    description:
      "We helped RetailGiant transition their legacy e-commerce platform to a modern microservices architecture, resulting in significantly faster page loads and increased conversion rates.",
    challenge:
      "RetailGiant, a major retail chain with over 500 physical stores, was struggling with an outdated e-commerce platform that was slow, difficult to maintain, and couldn't handle peak shopping periods. Their legacy monolithic system was causing cart abandonment rates to soar and was limiting their ability to implement new features and promotions quickly.",
    approach:
      "After a comprehensive analysis of their existing system and business requirements, we proposed a phased migration to a microservices architecture. We implemented a strangler pattern approach to gradually replace components of the old system while ensuring continuous operation. Our team worked closely with RetailGiant's internal developers to ensure knowledge transfer throughout the process.",
    solution:
      "Our solution included:\n\n- Breaking down the monolithic application into independent, scalable microservices\n- Implementing a container orchestration system with Kubernetes for improved scalability\n- Developing a new API gateway to manage service communication\n- Creating a robust CI/CD pipeline for faster deployment\n- Implementing advanced caching strategies\n- Building a responsive, mobile-first frontend using modern JavaScript frameworks\n- Setting up comprehensive monitoring and alerting systems",
    results:
      "The new platform launched on schedule and immediately showed impressive improvements. Page load times decreased by 60%, and the system easily handled a Black Friday sale that generated 3x the traffic of their previous record without any performance issues. Most importantly, the conversion rate increased by 37% and cart abandonment dropped by 25%.",
    image: "/api/placeholder/800/400",
    gallery: [
      "/api/placeholder/600/400",
      "/api/placeholder/600/400",
      "/api/placeholder/600/400",
    ],
    stats: [
      { label: "Increase in Sales", value: "37%" },
      { label: "Page Load Time", value: "0.8s" },
      { label: "Conversion Rate", value: "+28%" },
      { label: "Mobile Traffic", value: "+45%" },
    ],
    testimonial: {
      content:
        "The team's expertise in modern architecture patterns transformed our e-commerce platform completely. We can now innovate faster, scale effortlessly during peak seasons, and provide a much better experience for our customers. The results speak for themselves with increased conversion rates and higher average order values.",
      author: "Jennifer Martinez",
      role: "CTO",
      company: "RetailGiant Inc.",
    },
    tags: [
      "E-commerce",
      "Microservices",
      "Cloud Migration",
      "Performance Optimization",
    ],
    related: [
      {
        id: 2,
        title: "FinTech Mobile App Development",
        image: "/api/placeholder/400/300",
        industry: "Finance",
      },
      {
        id: 5,
        title: "Corporate Website Redesign",
        image: "/api/placeholder/400/300",
        industry: "Insurance",
      },
    ],
  },
  2: {
    id: 2,
    title: "FinTech Mobile App Development",
    client: "PayEasy Solutions",
    industry: "Finance",
    service: "Custom Software Development",
    duration: "8 months",
    year: 2023,
    description:
      "Designed and developed a secure, high-performance payment application with biometric authentication, resulting in 125K+ downloads in the first quarter.",
    challenge:
      "PayEasy Solutions needed a modern, secure mobile payment platform to compete with established players in the financial technology space. They required an application that would prioritize security while maintaining an intuitive user experience.",
    approach:
      "We adopted a security-first development approach, working closely with financial security experts to ensure compliance with industry standards. User experience research was conducted to map out the most intuitive payment flows.",
    solution:
      "We developed a cross-platform mobile application with advanced security features including biometric authentication, real-time fraud detection, and end-to-end encryption. The app integrated with multiple payment processors and banking systems while maintaining a clean, simple user interface.",
    results:
      "The application gained immediate traction upon release, achieving over a quarter-million users within the first six months. The platform processes transactions securely in 1.2 seconds on average, and has maintained a near-perfect rating on app stores.",
    image: "/api/placeholder/800/400",
    gallery: [
      "/api/placeholder/600/400",
      "/api/placeholder/600/400",
      "/api/placeholder/600/400",
    ],
    stats: [
      { label: "User Acquisition", value: "125K" },
      { label: "Transaction Time", value: "1.2s" },
      { label: "App Store Rating", value: "4.9" },
      { label: "Fraud Prevention", value: "99.7%" },
    ],
    testimonial: {
      content:
        "Working with the team was a game-changer for our business. They delivered a secure, user-friendly payment application that exceeded our expectations and has been instrumental in our rapid market growth.",
      author: "Michael Chen",
      role: "CEO",
      company: "PayEasy Solutions",
    },
    tags: [
      "FinTech",
      "Mobile App",
      "Secure Payments",
      "Biometric Authentication",
    ],
    related: [
      {
        id: 1,
        title: "E-commerce Platform Migration",
        image: "/api/placeholder/400/300",
        industry: "Retail",
      },
      {
        id: 3,
        title: "Healthcare Analytics Dashboard",
        image: "/api/placeholder/400/300",
        industry: "Healthcare",
      },
    ],
  },
};

// Fetch the case study data
const fetchCaseStudy = async () => {
  isLoading.value = true;
  isError.value = false;

  try {
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 800)); // Simulate API delay

    const data = mockCaseStudyData[caseStudyId.value];

    if (data) {
      caseStudy.value = data;
    } else {
      isError.value = true;
      console.error(`Case study with ID ${caseStudyId.value} not found`);
    }
  } catch (error) {
    isError.value = true;
    console.error("Error fetching case study:", error);
  } finally {
    isLoading.value = false;

    // Trigger animations after a short delay
    setTimeout(() => {
      isPageVisible.value = true;
    }, 100);
  }
};

// Navigate to a related case study
const viewRelatedCaseStudy = (id: number) => {
  router.push(`/consultancy/case-studies/${id}`);
};

// Navigate back to case studies
const navigateBack = () => {
  router.push("/consultancy/case-studies");
};

// Process content with line breaks
const formatContent = (content: string) => {
  return content.replace(/\n\n/g, "<br><br>");
};

// Lifecycle hooks
onMounted(() => {
  fetchCaseStudy();

  // Scroll to top on navigation
  window.scrollTo(0, 0);
});

// Update page metadata
useHead(() => ({
  title: caseStudy.value
    ? `${caseStudy.value.title} | Case Study | LearnConsult`
    : "Case Study | LearnConsult",
  meta: [
    {
      name: "description",
      content: caseStudy.value
        ? `Learn how we helped ${
            caseStudy.value.client
          } with ${caseStudy.value.service.toLowerCase()} services, resulting in impressive business outcomes.`
        : "Explore our detailed case studies showing real results from our consultancy services.",
    },
  ],
}));
</script>

<template>
  <div>
    <!-- Loading State -->
    <div v-if="isLoading" class="py-32 flex justify-center items-center">
      <div
        class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-indigo-600"
      ></div>
    </div>

    <!-- Error State -->
    <div v-else-if="isError" class="py-32 text-center">
      <div
        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-6"
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
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
      </div>
      <h2 class="text-2xl font-bold mb-4">Case Study Not Found</h2>
      <p class="text-gray-600 mb-8">
        We couldn't find the case study you're looking for.
      </p>
      <button
        @click="navigateBack"
        class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors"
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
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
        Back to Case Studies
      </button>
    </div>

    <!-- Case Study Content -->
    <div v-else-if="caseStudy">
      <!-- Hero Section -->
      <section
        class="relative bg-gradient-to-br from-indigo-900 to-purple-900 text-white overflow-hidden"
      >
        <!-- Back button -->
        <div class="absolute top-4 left-4 z-10">
          <button
            @click="navigateBack"
            class="flex items-center bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-lg py-2 px-4 hover:bg-opacity-30 transition-colors"
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
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            All Case Studies
          </button>
        </div>

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

        <div class="container mx-auto px-4 py-16 md:py-24 relative z-10">
          <div class="max-w-4xl mx-auto">
            <!-- Client and metadata -->
            <div
              class="mb-4 transform transition-all duration-1000"
              :class="
                isPageVisible
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-8'
              "
            >
              <span
                class="inline-block px-4 py-1 rounded-full bg-white bg-opacity-20 backdrop-blur-sm text-sm font-medium"
              >
                {{ caseStudy.client }}
              </span>
            </div>

            <!-- Title -->
            <h1
              class="text-3xl md:text-5xl font-bold mb-6 transform transition-all duration-1000"
              :class="
                isPageVisible
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-8'
              "
              style="transition-delay: 200ms"
            >
              {{ caseStudy.title }}
            </h1>

            <!-- Short description -->
            <p
              class="text-lg md:text-xl text-indigo-100 mb-8 transform transition-all duration-1000"
              :class="
                isPageVisible
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-8'
              "
              style="transition-delay: 400ms"
            >
              {{ caseStudy.description }}
            </p>

            <!-- Tags -->
            <div
              class="flex flex-wrap gap-2 mb-12 transform transition-all duration-1000"
              :class="
                isPageVisible
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-8'
              "
              style="transition-delay: 600ms"
            >
              <span
                v-for="tag in caseStudy.tags"
                :key="tag"
                class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-sm"
              >
                {{ tag }}
              </span>
            </div>

            <!-- Key info cards -->
            <div
              class="grid md:grid-cols-4 gap-4 mb-8 transform transition-all duration-1000"
              :class="
                isPageVisible
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-8'
              "
              style="transition-delay: 800ms"
            >
              <div
                class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4"
              >
                <div class="text-sm opacity-80 mb-1">Industry</div>
                <div class="font-semibold">{{ caseStudy.industry }}</div>
              </div>

              <div
                class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4"
              >
                <div class="text-sm opacity-80 mb-1">Service</div>
                <div class="font-semibold">{{ caseStudy.service }}</div>
              </div>

              <div
                class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4"
              >
                <div class="text-sm opacity-80 mb-1">Duration</div>
                <div class="font-semibold">{{ caseStudy.duration }}</div>
              </div>

              <div
                class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4"
              >
                <div class="text-sm opacity-80 mb-1">Year</div>
                <div class="font-semibold">{{ caseStudy.year }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Hero image -->
        <div
          class="transform transition-all duration-1000"
          :class="
            isPageVisible
              ? 'opacity-100 translate-y-0'
              : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 1000ms"
        >
          <div class="relative h-64 md:h-80 lg:h-96 overflow-hidden">
            <img
              :src="caseStudy.image"
              :alt="caseStudy.title"
              class="w-full h-full object-cover"
            />
            <div
              class="absolute inset-0 bg-gradient-to-b from-indigo-900 opacity-60"
            ></div>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
          <div class="max-w-4xl mx-auto">
            <!-- Results summary -->
            <div
              class="bg-indigo-50 rounded-xl p-8 mb-16 transform transition-all duration-1000 shadow-lg"
              :class="
                isPageVisible
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-8'
              "
              style="transition-delay: 200ms"
            >
              <h2 class="text-2xl font-bold mb-6 text-center">Key Results</h2>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div
                  v-for="(stat, index) in caseStudy.stats"
                  :key="index"
                  class="text-center transform transition-all duration-500"
                  :style="`transition-delay: ${200 + index * 100}ms;`"
                >
                  <div class="text-3xl font-bold text-indigo-600 mb-2">
                    {{ stat.value }}
                  </div>
                  <div class="text-sm text-gray-600">{{ stat.label }}</div>
                </div>
              </div>
            </div>

            <!-- Challenge, approach and solution -->
            <div class="space-y-16">
              <!-- Challenge section -->
              <div
                class="transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 400ms"
              >
                <h2 class="text-2xl font-bold mb-4">The Challenge</h2>
                <p
                  class="text-gray-700"
                  v-html="formatContent(caseStudy.challenge)"
                ></p>
              </div>

              <!-- Approach section -->
              <div
                class="transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 600ms"
              >
                <h2 class="text-2xl font-bold mb-4">Our Approach</h2>
                <p
                  class="text-gray-700"
                  v-html="formatContent(caseStudy.approach)"
                ></p>
              </div>

              <!-- Solution section -->
              <div
                class="transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 800ms"
              >
                <h2 class="text-2xl font-bold mb-4">The Solution</h2>
                <p
                  class="text-gray-700"
                  v-html="formatContent(caseStudy.solution)"
                ></p>
              </div>

              <!-- Gallery -->
              <div
                v-if="caseStudy.gallery && caseStudy.gallery.length > 0"
                class="transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 1000ms"
              >
                <h2 class="text-2xl font-bold mb-6">Project Gallery</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div
                    v-for="(image, index) in caseStudy.gallery"
                    :key="index"
                    class="rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300"
                  >
                    <img
                      :src="image"
                      :alt="`${caseStudy.title} image ${index + 1}`"
                      class="w-full h-48 object-cover"
                    />
                  </div>
                </div>
              </div>

              <!-- Results section -->
              <div
                class="transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 1200ms"
              >
                <h2 class="text-2xl font-bold mb-4">The Results</h2>
                <p
                  class="text-gray-700"
                  v-html="formatContent(caseStudy.results)"
                ></p>
              </div>

              <!-- Testimonial -->
              <div
                v-if="caseStudy.testimonial"
                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl p-8 shadow-lg transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 1400ms"
              >
                <div class="relative">
                  <!-- Quote marks -->
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 absolute -top-6 -left-6 text-white opacity-20"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z"
                    />
                  </svg>

                  <blockquote class="text-xl italic mb-6">
                    "{{ caseStudy.testimonial.content }}"
                  </blockquote>

                  <div class="flex items-center">
                    <div
                      class="bg-white rounded-full w-12 h-12 flex items-center justify-center text-indigo-700 font-bold text-xl mr-4"
                    >
                      {{ caseStudy.testimonial.author.charAt(0) }}
                    </div>
                    <div>
                      <div class="font-bold">
                        {{ caseStudy.testimonial.author }}
                      </div>
                      <div class="text-indigo-100">
                        {{ caseStudy.testimonial.role }},
                        {{ caseStudy.testimonial.company }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Related case studies -->
              <div
                v-if="caseStudy.related && caseStudy.related.length > 0"
                class="transform transition-all duration-1000"
                :class="
                  isPageVisible
                    ? 'opacity-100 translate-y-0'
                    : 'opacity-0 translate-y-8'
                "
                style="transition-delay: 1600ms"
              >
                <h2 class="text-2xl font-bold mb-6">Related Case Studies</h2>
                <div class="grid md:grid-cols-2 gap-6">
                  <div
                    v-for="related in caseStudy.related"
                    :key="related.id"
                    class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-2"
                    @click="viewRelatedCaseStudy(related.id)"
                  >
                    <div class="relative h-40">
                      <img
                        :src="related.image"
                        :alt="related.title"
                        class="w-full h-full object-cover"
                      />
                      <div
                        class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-60"
                      ></div>
                      <div class="absolute bottom-0 left-0 p-4 text-white">
                        <div class="mb-1 text-sm">{{ related.industry }}</div>
                        <h3 class="text-xl font-bold">{{ related.title }}</h3>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
          <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-6">
              Ready for Similar Results?
            </h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
              Our team of experts is ready to help your business achieve
              impressive outcomes like these. Let's discuss your specific needs
              and create a tailored solution.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
              <NuxtLink
                to="/consultancy/request"
                class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-indigo-700 transition-colors shadow-md"
              >
                Schedule a Consultation
              </NuxtLink>
              <NuxtLink
                to="/consultancy"
                class="border-2 border-indigo-600 text-indigo-600 font-bold py-3 px-8 rounded-lg hover:bg-indigo-50 transition-colors"
              >
                Explore All Services
              </NuxtLink>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
/* Animations */
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

/* Style for content with HTML */
:deep(br) {
  display: block;
  content: "";
  margin-top: 1rem;
}
</style>
