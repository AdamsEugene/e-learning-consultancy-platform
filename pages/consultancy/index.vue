<!-- pages/consultancy/index.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

// Components
import ConsultancyHero from "@/components/consultancy/ConsultancyHero.vue";
import ConsultancyServiceCard from "@/components/consultancy/ConsultancyServiceCard.vue";
import ConsultancyProcessSection from "@/components/consultancy/ConsultancyProcessSection.vue";
import ConsultancyProjectCard from "@/components/consultancy/ConsultancyProjectCard.vue";
import ConsultancyTeamSection from "@/components/consultancy/ConsultancyTeamSection.vue";
import ConsultancyTestimonialsSection from "@/components/consultancy/ConsultancyTestimonialsSection.vue";

// Services data
const services = ref([
  {
    id: 1,
    title: "Custom Software Development",
    description:
      "From concept to launch, we build tailored solutions to solve your business challenges.",
    icon: "code",
    color: "indigo",
    features: [
      "Web & Mobile Applications",
      "API Development & Integration",
      "Custom CRM & ERP Solutions",
      "Legacy System Modernization",
    ],
    popular: true,
  },
  {
    id: 2,
    title: "Digital Transformation",
    description:
      "Transform your business processes with cutting-edge digital solutions and strategies.",
    icon: "refresh-cw",
    color: "purple",
    features: [
      "Digital Strategy Development",
      "Process Automation",
      "Change Management",
      "Digital Adoption Planning",
    ],
  },
  {
    id: 3,
    title: "UX/UI Design",
    description:
      "Create intuitive, engaging, and accessible user experiences that convert and delight.",
    icon: "layout",
    color: "blue",
    features: [
      "User Research & Testing",
      "Information Architecture",
      "Wireframing & Prototyping",
      "Visual Design & Branding",
    ],
  },
  {
    id: 4,
    title: "Technical Consulting",
    description:
      "Expert advice on architecture, technology stack, and best practices for your projects.",
    icon: "users",
    color: "teal",
    features: [
      "Technology Assessment",
      "Architecture Design",
      "Tech Stack Optimization",
      "Performance Optimization",
    ],
  },
  {
    id: 5,
    title: "Data Analytics & BI",
    description:
      "Turn your data into actionable insights that drive business growth and innovation.",
    icon: "trending-up",
    color: "green",
    features: [
      "Data Strategy Development",
      "BI Dashboard Creation",
      "Predictive Analytics",
      "Data Visualization",
    ],
  },
  {
    id: 6,
    title: "Cloud Migration & DevOps",
    description:
      "Optimize your infrastructure with cloud solutions and modern DevOps practices.",
    icon: "database",
    color: "red",
    features: [
      "Cloud Strategy & Migration",
      "CI/CD Pipeline Implementation",
      "Infrastructure as Code",
      "Microservices Architecture",
    ],
  },
]);

// Case studies data
const caseStudies = ref([
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
  },
]);

// Animation state for scroll reveals
const isServicesVisible = ref(false);
const isCaseStudiesVisible = ref(false);

// Scroll observer for animations
onMounted(() => {
  if (typeof window !== "undefined" && "IntersectionObserver" in window) {
    const options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    };

    const servicesObserver = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        isServicesVisible.value = true;
        servicesObserver.disconnect();
      }
    }, options);

    const caseStudiesObserver = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        isCaseStudiesVisible.value = true;
        caseStudiesObserver.disconnect();
      }
    }, options);

    const servicesSection = document.querySelector("#services-section");
    const caseStudiesSection = document.querySelector("#case-studies-section");

    if (servicesSection) servicesObserver.observe(servicesSection);
    if (caseStudiesSection) caseStudiesObserver.observe(caseStudiesSection);
  } else {
    // Fallback for browsers without intersection observer
    isServicesVisible.value = true;
    isCaseStudiesVisible.value = true;
  }
});

// Methods
const viewCaseStudy = (id: number) => {
  console.log("View case study:", id);
  // In a real app, this would navigate to the case study detail page
  // router.push(`/consultancy/case-studies/${id}`);
};

// Page meta
useHead({
  title: "Professional Consultancy Services | LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Expert consultancy services for businesses of all sizes. From digital transformation to custom software development, our team delivers tailored solutions.",
    },
  ],
});
</script>

<template>
  <div>
    <!-- Hero section -->
    <ConsultancyHero
      title="Expert Consultancy Services"
      subtitle="Transform Your Business"
      description="Our team of industry experts delivers tailored solutions to help your business overcome challenges, optimize operations, and achieve sustainable growth."
      button-text="Request Consultation"
      button-link="/consultancy/request"
      secondary-button-text="Explore Services"
      secondary-button-link="#services-section"
      background="gradient"
    />

    <!-- Services section -->
    <section id="services-section" class="py-20 bg-white">
      <div class="container mx-auto px-4">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span
            class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 mb-4"
          >
            Our Expertise
          </span>
          <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Comprehensive Consultancy Services
          </h2>
          <p class="text-lg text-gray-600">
            We offer a wide range of consultancy services to help your business
            thrive in today's competitive landscape. Our team of experts is
            ready to deliver solutions tailored to your specific needs.
          </p>
        </div>

        <!-- Services grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="(service, index) in services"
            :key="service.id"
            class="transform transition-all duration-500"
            :class="[
              isServicesVisible
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-20',
            ]"
            :style="`transition-delay: ${index * 100}ms`"
          >
            <ConsultancyServiceCard
              :service="service"
              :animated="isServicesVisible"
              :delay="index * 100"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Process section -->
    <ConsultancyProcessSection />

    <!-- Case studies section -->
    <section id="case-studies-section" class="py-20 bg-gray-50">
      <div class="container mx-auto px-4">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span
            class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 mb-4"
          >
            Success Stories
          </span>
          <h2 class="text-3xl md:text-4xl font-bold mb-6">Our Case Studies</h2>
          <p class="text-lg text-gray-600">
            Explore how we've helped businesses across various industries
            achieve their goals through our consultancy services.
          </p>
        </div>

        <!-- Case studies grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
          <div
            v-for="(caseStudy, index) in caseStudies"
            :key="caseStudy.id"
            class="transform transition-all duration-500"
            :class="[
              isCaseStudiesVisible
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-20',
            ]"
            :style="`transition-delay: ${index * 100}ms`"
          >
            <ConsultancyProjectCard
              :project="caseStudy"
              :animated="isCaseStudiesVisible"
              :delay="index * 100"
              @view-project="viewCaseStudy(caseStudy.id)"
            />
          </div>
        </div>

        <!-- View all case studies button -->
        <div class="mt-12 text-center">
          <NuxtLink
            to="/consultancy/case-studies"
            class="inline-flex items-center py-3 px-6 bg-white border border-indigo-600 rounded-lg text-indigo-600 font-bold transition-all duration-300 hover:bg-indigo-50 hover:shadow-md"
          >
            View All Case Studies
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
    </section>

    <!-- Team section -->
    <ConsultancyTeamSection />

    <!-- Testimonials section -->
    <ConsultancyTestimonialsSection />

    <!-- CTA section -->
    <section
      class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600 text-white relative overflow-hidden"
    >
      <!-- Decorative elements -->
      <div
        class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-32 -mt-32"
      />
      <div
        class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full -ml-32 -mb-32"
      />

      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
          <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Ready to Transform Your Business?
          </h2>
          <p class="text-xl mb-8 text-indigo-100">
            Connect with our consultancy team today to discuss how we can help
            you achieve your business goals.
          </p>
          <div class="flex flex-wrap justify-center gap-4">
            <NuxtLink
              to="/consultancy/request"
              class="bg-white text-indigo-700 font-bold py-4 px-8 rounded-lg hover:bg-opacity-90 transition-all transform hover:scale-105 hover:shadow-lg"
            >
              Schedule a Consultation
            </NuxtLink>
            <NuxtLink
              to="/contact"
              class="border-2 border-white text-white font-bold py-4 px-8 rounded-lg hover:shadow-lg transition-all transform hover:scale-105 hover:bg-opacity-20"
            >
              Contact Us
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Bottom wave decoration -->
      <div class="absolute bottom-0 left-0 right-0 h-16 overflow-hidden">
        <svg
          class="absolute bottom-0 w-full h-full transform translate-y-1"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 1440 320"
        >
          <path
            fill="#ffffff"
            fill-opacity="1"
            d="M0,192L60,176C120,160,240,128,360,128C480,128,600,160,720,186.7C840,213,960,235,1080,229.3C1200,224,1320,192,1380,176L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"
          />
        </svg>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Animation for section reveals */
@keyframes fadeUpIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-up {
  animation: fadeUpIn 0.8s cubic-bezier(0.39, 0.575, 0.565, 1) both;
}

/* Staggered animation delays */
.stagger-delay-1 {
  animation-delay: 100ms;
}
.stagger-delay-2 {
  animation-delay: 200ms;
}
.stagger-delay-3 {
  animation-delay: 300ms;
}
.stagger-delay-4 {
  animation-delay: 400ms;
}
.stagger-delay-5 {
  animation-delay: 500ms;
}
.stagger-delay-6 {
  animation-delay: 600ms;
}
</style>
