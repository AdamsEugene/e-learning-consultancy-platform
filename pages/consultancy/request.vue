<!-- pages/consultancy/request.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";
import ConsultancyContactForm from "@/components/consultancy/ConsultancyContactForm.vue";

// Data for FAQ section
const faqs = ref([
  {
    question: "What happens after I submit my consultation request?",
    answer:
      "After submitting your request, you'll receive an email confirmation immediately. Within 24 hours, one of our consultants will contact you to schedule an initial discovery call to discuss your project in more detail.",
    isOpen: false,
  },
  {
    question: "Do you offer free initial consultations?",
    answer:
      "Yes, we offer a complimentary 30-minute initial consultation to understand your needs and determine how we can best assist you. During this call, we'll discuss your business challenges, goals, and potential solutions.",
    isOpen: false,
  },
  {
    question: "How do you determine pricing for your consultancy services?",
    answer:
      "Our pricing is typically based on the scope, complexity, and duration of your project. We offer both fixed-price arrangements for well-defined projects and time-and-materials pricing for more flexible engagements. After our initial consultation, we'll provide a detailed proposal including pricing options.",
    isOpen: false,
  },
  {
    question: "What information should I prepare before our first meeting?",
    answer:
      "To make the most of our initial consultation, it's helpful to have a clear understanding of your business challenge, any relevant background information, your project goals, timeline expectations, and budget constraints. The more information you can provide, the more tailored our recommendations will be.",
    isOpen: false,
  },
  {
    question: "Can you sign an NDA before I share sensitive information?",
    answer:
      "Absolutely. We understand that consultancy projects often involve sensitive business information. We're happy to sign a mutual Non-Disclosure Agreement (NDA) before you share any confidential details about your business or project.",
    isOpen: false,
  },
  {
    question: "Do you work with clients remotely or only locally?",
    answer:
      "We work with clients both locally and remotely. Our tools and processes are designed to provide a seamless experience regardless of location. We use secure video conferencing, project management, and collaboration tools to ensure effective communication throughout your project.",
    isOpen: false,
  },
]);

// Toggle FAQ item
const toggleFaq = (index: number) => {
  faqs.value[index].isOpen = !faqs.value[index].isOpen;
};

// Benefits list
const benefits = ref([
  {
    title: "Expert Guidance",
    description:
      "Access to experienced consultants with deep industry knowledge",
    icon: "lightbulb",
    color: "indigo",
  },
  {
    title: "Tailored Solutions",
    description:
      "Custom strategies designed specifically for your business needs",
    icon: "puzzle",
    color: "purple",
  },
  {
    title: "Measurable Results",
    description:
      "Data-driven approaches with clear KPIs and measurable outcomes",
    icon: "chart",
    color: "blue",
  },
  {
    title: "Implementation Support",
    description:
      "Hands-on assistance throughout the execution of your strategy",
    icon: "tools",
    color: "teal",
  },
]);

// Animation state
const isPageLoaded = ref(false);
const visibleSections = ref<string[]>([]);

// Set up intersection observer for animation on scroll
onMounted(() => {
  isPageLoaded.value = true;
  
  // Add benefits section to visible sections by default
  visibleSections.value = ["benefits-section"];
  
  console.log("Initial visible sections:", visibleSections.value);

  if (typeof window !== "undefined" && "IntersectionObserver" in window) {
    const options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const sectionId = entry.target.id;
          console.log("Intersection detected for:", sectionId);
          if (sectionId && !visibleSections.value.includes(sectionId)) {
            visibleSections.value.push(sectionId);
            console.log("Updated visible sections:", visibleSections.value);
          }
        }
      });
    }, options);

    // Observe each section
    document.querySelectorAll("[id]").forEach((element) => {
      observer.observe(element);
    });
  } else {
    // Fallback for browsers without IntersectionObserver
    visibleSections.value = ["benefits-section", "form-section", "faq-section"];
  }
});

// Page meta
useHead({
  title: "Request a Consultation | LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Submit your consultancy request and connect with our expert team. We offer tailored solutions to help your business overcome challenges and achieve growth.",
    },
  ],
});
</script>

<template>
  <div>
    <!-- Hero section -->
    <section
      class="relative bg-gradient-to-br from-indigo-900 to-purple-900 text-white py-16 md:py-24 overflow-hidden"
    >
      <!-- Background decorative elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div
          class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full opacity-20 -mr-40 -mt-40"
        />
        <div
          class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600 rounded-full opacity-20 -ml-40 -mb-40"
        />

        <!-- Grid pattern -->
        <div
          class="absolute inset-0 opacity-10"
          style="
            background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
          "
        />
      </div>

      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
          <!-- Animated entrance for title -->
          <h1
            class="text-3xl md:text-5xl font-bold mb-6 transform transition-all duration-1000"
            :class="
              isPageLoaded
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
          >
            Request a Consultation
          </h1>

          <!-- Animated entrance for description -->
          <p
            class="text-lg md:text-xl text-indigo-100 mb-8 transform transition-all duration-1000"
            :class="
              isPageLoaded
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-8'
            "
            style="transition-delay: 200ms"
          >
            Ready to transform your business? Connect with our expert
            consultants today to discuss your challenges and goals. Fill out the
            form below, and we'll get back to you within 24 hours.
          </p>
        </div>
      </div>
    </section>

    <!-- Main content section with form and benefits -->
    <section id="form-section" class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:space-x-12">
          <!-- Consultation form col -->
          <div class="lg:w-2/3 mb-10 lg:mb-0">
            <div
              class="transform transition-all duration-1000"
              :class="
                visibleSections.includes('form-section')
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-12'
              "
            >
              <ConsultancyContactForm
                title="Get Started with a Consultation"
                subtitle="Request Your Consultation"
                description="Fill out the form below to request a consultation with our team of experts. We'll get back to you within 24 hours to discuss your project needs."
              />
            </div>
          </div>

          <!-- Benefits sidebar col -->
          <div class="lg:w-1/3">
            <div
              id="benefits-section"
              class="space-y-8 transform transition-all duration-1000"
              :class="
                visibleSections.includes('benefits-section')
                  ? 'opacity-100 translate-y-0'
                  : 'opacity-0 translate-y-12'
              "
              style="transition-delay: 200ms"
            >
              <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900">
                  Why Work With Us
                </h2>
                <p class="text-gray-600 mb-6">
                  Our consultancy team brings decades of experience across
                  multiple industries. We're committed to delivering exceptional
                  results that drive measurable business impact.
                </p>
              </div>

              <!-- Benefits cards -->
              <div class="space-y-4">
                <div
                  v-for="(benefit, index) in benefits"
                  :key="index"
                  class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1"
                  :style="`transition-delay: ${300 + index * 100}ms`"
                >
                  <div class="flex items-start">
                    <!-- Benefit icon -->
                    <div
                      :class="`bg-${benefit.color}-100 text-${benefit.color}-600 p-3 rounded-lg mr-4`"
                    >
                      <svg
                        v-if="benefit.icon === 'lightbulb'"
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
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                        />
                      </svg>
                      <svg
                        v-else-if="benefit.icon === 'puzzle'"
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
                          d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"
                        />
                      </svg>
                      <svg
                        v-else-if="benefit.icon === 'chart'"
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
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                        />
                      </svg>
                      <svg
                        v-else-if="benefit.icon === 'tools'"
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
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                      </svg>
                      <svg
                        v-else
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
                          d="M13 10V3L4 14h7v7l9-11h-7z"
                        />
                      </svg>
                    </div>

                    <!-- Benefit content -->
                    <div>
                      <h3 class="font-bold text-gray-900 mb-1">
                        {{ benefit.title }}
                      </h3>
                      <p class="text-gray-600 text-sm">
                        {{ benefit.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Testimonial quote -->
              <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl p-6 relative"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-10 w-10 absolute top-4 left-4 text-white opacity-20"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z"
                  />
                </svg>

                <div class="ml-8">
                  <p class="italic mb-4">
                    "The team's strategic insights and implementation support
                    transformed our business processes, resulting in a 40%
                    increase in efficiency."
                  </p>
                  <div class="font-bold">Sarah Thompson</div>
                  <div class="text-sm text-indigo-200">
                    CTO, TechInnovate Solutions
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq-section" class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
          <!-- Section heading -->
          <div
            class="text-center mb-12 transform transition-all duration-1000"
            :class="
              visibleSections.includes('faq-section')
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-12'
            "
          >
            <h2 class="text-3xl font-bold mb-4 text-gray-900">Frequently Asked Questions</h2>
            <p class="text-gray-600">
              Find answers to common questions about our consultancy services
              and process.
            </p>
          </div>

          <!-- FAQ accordion -->
          <div
            class="space-y-4 transform transition-all duration-1000"
            :class="
              visibleSections.includes('faq-section')
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-12'
            "
            style="transition-delay: 200ms"
          >
            <div
              v-for="(faq, index) in faqs"
              :key="index"
              class="border border-gray-200 rounded-lg overflow-hidden transition-all duration-300"
              :class="{ 'shadow-md': faq.isOpen }"
            >
              <button
                class="w-full flex justify-between items-center p-6 text-left font-semibold transition-colors duration-300 focus:outline-none"
                :class="
                  faq.isOpen
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'bg-white text-gray-800 hover:bg-gray-50'
                "
                @click="toggleFaq(index)"
              >
                <span>{{ faq.question }}</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transition-transform duration-300"
                  :class="
                    faq.isOpen
                      ? 'transform rotate-180 text-indigo-600'
                      : 'text-gray-500'
                  "
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <div
                class="transition-all duration-300 overflow-hidden"
                :style="{ maxHeight: faq.isOpen ? '500px' : '0px' }"
              >
                <div
                  class="p-6 pt-0 bg-white text-gray-600 border-t border-gray-100"
                >
                  {{ faq.answer }}
                </div>
              </div>
            </div>
          </div>

          <!-- Still have questions -->
          <div
            class="text-center mt-12 transform transition-all duration-1000"
            :class="
              visibleSections.includes('faq-section')
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-12'
            "
            style="transition-delay: 400ms"
          >
            <p class="text-gray-600 mb-4">Still have questions?</p>
            <NuxtLink
              to="/contact"
              class="inline-flex items-center text-indigo-600 font-bold hover:text-indigo-800 transition-colors"
            >
              Contact our support team
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 ml-1"
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

/* Pulse animation for testimonial quote */
@keyframes pulse-subtle {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
  }
  50% {
    box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
  }
}

.testimonial-quote {
  animation: pulse-subtle 3s infinite;
}

/* FAQ accordion animation */
.faq-accordion-enter-active,
.faq-accordion-leave-active {
  transition: max-height 0.3s ease, opacity 0.3s ease;
  max-height: 300px;
  overflow: hidden;
}

.faq-accordion-enter-from,
.faq-accordion-leave-to {
  max-height: 0;
  opacity: 0;
}
</style>
