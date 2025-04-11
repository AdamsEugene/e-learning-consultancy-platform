<script lang="ts" setup>
interface Props {
  visibleSections: {
    features: boolean;
    courses: boolean;
    consultancy: boolean;
    testimonials: boolean;
    cta: boolean;
  };
}

defineProps<Props>();

// Consultancy services with enhanced metadata
const consultancyServices = ref([
  {
    id: 1,
    title: "Custom Software Development",
    description:
      "From concept to launch, we build tailored solutions to solve your business challenges.",
    icon: "code",
    delay: "delay-0",
    gradient: "from-indigo-400 to-blue-500",
    glowColor: "blue",
  },
  {
    id: 2,
    title: "Digital Transformation",
    description:
      "Transform your business processes with cutting-edge digital solutions.",
    icon: "refresh-cw",
    delay: "delay-200",
    gradient: "from-purple-400 to-pink-500",
    glowColor: "purple",
  },
  {
    id: 3,
    title: "UX/UI Design",
    description: "Create intuitive, engaging, and accessible user experiences.",
    icon: "layout",
    delay: "delay-400",
    gradient: "from-pink-400 to-rose-500",
    glowColor: "pink",
  },
  {
    id: 4,
    title: "Technical Consulting",
    description:
      "Expert advice on architecture, technology stack, and best practices.",
    icon: "users",
    delay: "delay-600",
    gradient: "from-blue-400 to-cyan-500",
    glowColor: "cyan",
  },
]);

// Case studies with additional metadata
const caseStudies = ref([
  {
    id: 1,
    title: "E-commerce Platform Migration",
    client: "RetailGiant Inc.",
    description:
      "Helped transition legacy system to a modern microservices architecture.",
    image: "/api/placeholder/600/400",
    gradient: "from-indigo-500 to-blue-600",
    stats: [
      { label: "Increase in Sales", value: "37%", color: "text-blue-500" },
      { label: "Page Load Time", value: "0.8s", color: "text-green-500" },
      { label: "Conversion Rate", value: "+28%", color: "text-purple-500" },
    ],
    delay: "delay-0",
  },
  {
    id: 2,
    title: "FinTech Mobile App",
    client: "PayEasy Solutions",
    description:
      "Designed and developed a secure, high-performance payment application.",
    image: "/api/placeholder/600/400",
    gradient: "from-purple-500 to-pink-600",
    stats: [
      { label: "User Acquisition", value: "125K", color: "text-indigo-500" },
      { label: "Transaction Time", value: "1.2s", color: "text-green-500" },
      { label: "App Store Rating", value: "4.9", color: "text-amber-500" },
    ],
    delay: "delay-300",
  },
]);
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    id="consultancy"
    class="py-20 px-4 bg-gradient-to-br from-indigo-900 via-purple-900 to-violet-900 text-white relative overflow-hidden"
  >
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Glowing orbs -->
      <div
        class="absolute top-1/4 left-1/4 w-64 h-64 bg-blue-600 opacity-20 rounded-full filter blur-3xl animate-pulse-slow"
      />
      <div
        class="absolute bottom-1/3 right-1/4 w-72 h-72 bg-purple-500 opacity-20 rounded-full filter blur-3xl animate-pulse-slow"
        style="animation-delay: 1s"
      />
      <div
        class="absolute top-2/3 left-2/3 w-56 h-56 bg-pink-600 opacity-15 rounded-full filter blur-3xl animate-pulse-slow"
        style="animation-delay: 2s"
      />

      <!-- Animated particles -->
      <div
        v-for="i in 30"
        :key="i"
        class="consultant-particle absolute w-1 h-1 rounded-full bg-white opacity-60"
      />

      <!-- Subtle grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
      <!-- Section header with animated elements -->
      <div class="text-center mb-20">
        <div class="mb-3 animate-fade-in">
          <span
            class="bg-white bg-opacity-10 backdrop-blur-sm text-white text-sm font-bold px-4 py-2 rounded-full shadow-glow"
          >
            Expert Solutions
          </span>
        </div>
        <h2
          class="text-4xl md:text-5xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-300 via-indigo-200 to-purple-300 animate-fade-in"
          style="animation-delay: 200ms"
        >
          Professional Consultancy Services
        </h2>
        <p
          class="text-xl max-w-3xl mx-auto mb-10 text-indigo-100 animate-fade-in"
          style="animation-delay: 400ms"
        >
          Our team of experienced consultants is ready to help your business
          grow with tailored solutions for your specific challenges.
        </p>

        <!-- Animated divider -->
        <div
          class="h-1 w-24 bg-gradient-to-r from-blue-400 to-purple-400 mx-auto rounded-full relative overflow-hidden animate-fade-in"
          style="animation-delay: 600ms"
        >
          <div class="absolute inset-0 bg-white animate-shimmer" />
        </div>
      </div>

      <!-- Services grid with hover effects and animations -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
        <div
          v-for="service in consultancyServices"
          :key="service.id"
          class="animate-fade-in"
          :class="service.delay"
        >
          <div
            class="service-card relative h-full p-6 bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-xl border border-white border-opacity-20 hover:bg-opacity-15 transition-all duration-500 transform hover:-translate-y-2 hover:shadow-glow-sm group overflow-hidden"
          >
            <!-- Background gradient that appears on hover -->
            <div
              class="absolute -inset-0.5 bg-gradient-to-r opacity-0 group-hover:opacity-100 rounded-xl blur-sm transition-all duration-500"
              :class="service.gradient"
            />

            <!-- Card content -->
            <div class="relative z-10">
              <!-- Icon with pulse effect -->
              <div class="relative mb-6">
                <!-- Glowing background -->
                <div
                  class="absolute -inset-2 rounded-full opacity-0 group-hover:opacity-30 blur transition-all duration-500"
                  :class="`bg-${service.glowColor}-500`"
                />

                <!-- Icon container -->
                <div
                  class="relative z-10 bg-gradient-to-br p-3 rounded-xl inline-flex group-hover:shadow-glow-sm transition-all duration-500 text-white transform group-hover:scale-110"
                  :class="service.gradient"
                >
                  <!-- Dynamic icon based on service.icon -->
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      v-if="service.icon === 'code'"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                    />
                    <path
                      v-if="service.icon === 'refresh-cw'"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                    />
                    <path
                      v-if="service.icon === 'layout'"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"
                    />
                    <path
                      v-if="service.icon === 'users'"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                </div>
              </div>

              <!-- Service content -->
              <h3
                class="text-xl font-bold mb-3 text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r transition-all duration-500"
                :class="service.gradient"
              >
                {{ service.title }}
              </h3>

              <p
                class="text-indigo-100 group-hover:text-white transition-all duration-500 mb-4"
              >
                {{ service.description }}
              </p>

              <!-- Learn more link with animated arrow -->
              <a
                href="/consultancy/services"
                class="flex items-center text-indigo-300 group-hover:text-white text-sm font-semibold transition-colors"
              >
                <span class="relative">
                  Learn more
                  <span
                    class="absolute -bottom-1 left-0 w-0 h-0.5 bg-white transition-all duration-500 group-hover:w-full"
                  />
                </span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1 transform transition-transform duration-500 group-hover:translate-x-2"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Case Studies -->
      <div class="mt-24">
        <h3
          class="text-2xl md:text-3xl font-bold text-center mb-12 bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-purple-300 animate-fade-in"
        >
          Featured Case Studies
        </h3>

        <div class="grid md:grid-cols-2 gap-8">
          <div
            v-for="study in caseStudies"
            :key="study.id"
            class="case-study animate-fade-in"
            :class="study.delay"
          >
            <div
              class="bg-white text-gray-800 rounded-xl overflow-hidden shadow-xl transform hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 group relative"
            >
              <!-- Image section with overlay -->
              <div class="relative overflow-hidden h-56">
                <img
                  :src="study.image"
                  :alt="study.title"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />
                <!-- Gradient overlay -->
                <div
                  class="absolute inset-0 opacity-90 transition-opacity duration-500 group-hover:opacity-75 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"
                />

                <!-- Color accent at top -->
                <div
                  class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r transition-transform duration-500 origin-left scale-x-0 group-hover:scale-x-100"
                  :class="study.gradient"
                />

                <!-- Client and title info -->
                <div class="absolute bottom-0 left-0 p-6 text-white z-10">
                  <div
                    class="inline-block px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-xs font-semibold mb-2"
                  >
                    {{ study.client }}
                  </div>
                  <h4
                    class="text-xl font-bold mb-2 transform transition-all duration-500 group-hover:translate-x-2"
                  >
                    {{ study.title }}
                  </h4>
                </div>
              </div>

              <!-- Content area -->
              <div class="p-6">
                <p class="text-gray-600 mb-6">{{ study.description }}</p>

                <!-- Stats with animated counters -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                  <div
                    v-for="(stat, index) in study.stats"
                    :key="index"
                    class="text-center transform hover:scale-105 transition-transform duration-300"
                  >
                    <div
                      class="font-bold text-xl mb-1 counter-animate"
                      :class="stat.color"
                    >
                      {{ stat.value }}
                    </div>
                    <div class="text-gray-500 text-sm">{{ stat.label }}</div>
                  </div>
                </div>

                <!-- View case study button -->
                <div class="flex justify-between items-center">
                  <div class="text-sm text-gray-500">
                    <span
                      class="inline-block w-2 h-2 rounded-full bg-green-500 mr-1"
                    />
                    Completed
                  </div>
                  <a
                    href="#"
                    class="text-indigo-600 font-semibold flex items-center hover:text-indigo-800 transition-all group/link"
                  >
                    <span class="relative">
                      View Case Study
                      <span
                        class="absolute -bottom-0.5 left-0 w-0 h-0.5 bg-indigo-600 transition-all duration-300 group-hover/link:w-full"
                      />
                    </span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 ml-1 transform transition-transform duration-300 group-hover/link:translate-x-1"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CTA button with animated effects -->
        <div
          class="mt-16 text-center animate-fade-in"
          style="animation-delay: 800ms"
        >
          <div
            class="inline-block transform transition-transform hover:scale-105 relative group"
          >
            <div
              class="absolute -inset-1 opacity-0 group-hover:opacity-70 rounded-lg blur-sm bg-gradient-to-r from-blue-400 to-purple-400 transition-all duration-500"
            />
            <a
              href="/consultancy"
              class="relative bg-white text-indigo-800 font-bold py-4 px-10 rounded-lg hover:bg-gray-50 transition-colors shadow-lg inline-block z-10"
            >
              View All Case Studies
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Grid pattern background */
.bg-grid-pattern {
  background-image: linear-gradient(
      to right,
      rgba(255, 255, 255, 0.05) 1px,
      transparent 1px
    ),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulseSlow {
  0%,
  100% {
    opacity: 0.15;
    transform: scale(1);
  }
  50% {
    opacity: 0.3;
    transform: scale(1.1);
  }
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  20%,
  80% {
    opacity: 0.5;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}

@keyframes floatParticle {
  0%,
  100% {
    transform: translateY(0) rotate(0deg);
  }
  25% {
    transform: translateY(-15px) rotate(5deg);
  }
  50% {
    transform: translateY(-30px) rotate(0deg);
  }
  75% {
    transform: translateY(-15px) rotate(-5deg);
  }
}

@keyframes countUp {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Applied animations */
.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1s forwards;
}

.animate-pulse-slow {
  animation: pulseSlow 6s infinite;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

.counter-animate {
  animation: countUp 1s forwards;
  animation-delay: 0.5s;
}

/* Consultant particles */
.consultant-particle {
  opacity: 0.3;
  animation: floatParticle 20s infinite linear;
}

.consultant-particle:nth-child(1) {
  left: 5%;
  top: 10%;
  animation-delay: 0s;
}
.consultant-particle:nth-child(2) {
  left: 15%;
  top: 20%;
  animation-delay: 1s;
}
.consultant-particle:nth-child(3) {
  left: 25%;
  top: 15%;
  animation-delay: 2s;
}
.consultant-particle:nth-child(4) {
  left: 35%;
  top: 25%;
  animation-delay: 3s;
}
.consultant-particle:nth-child(5) {
  left: 45%;
  top: 5%;
  animation-delay: 4s;
}
.consultant-particle:nth-child(6) {
  left: 55%;
  top: 15%;
  animation-delay: 5s;
}
.consultant-particle:nth-child(7) {
  left: 65%;
  top: 20%;
  animation-delay: 6s;
}
.consultant-particle:nth-child(8) {
  left: 75%;
  top: 10%;
  animation-delay: 7s;
}
.consultant-particle:nth-child(9) {
  left: 85%;
  top: 25%;
  animation-delay: 8s;
}
.consultant-particle:nth-child(10) {
  left: 95%;
  top: 15%;
  animation-delay: 9s;
}
.consultant-particle:nth-child(11) {
  left: 10%;
  top: 30%;
  animation-delay: 10s;
}
.consultant-particle:nth-child(12) {
  left: 20%;
  top: 40%;
  animation-delay: 11s;
}
.consultant-particle:nth-child(13) {
  left: 30%;
  top: 35%;
  animation-delay: 12s;
}
.consultant-particle:nth-child(14) {
  left: 40%;
  top: 45%;
  animation-delay: 13s;
}
.consultant-particle:nth-child(15) {
  left: 50%;
  top: 35%;
  animation-delay: 14s;
}
.consultant-particle:nth-child(16) {
  left: 60%;
  top: 45%;
  animation-delay: 15s;
}
.consultant-particle:nth-child(17) {
  left: 70%;
  top: 40%;
  animation-delay: 16s;
}
.consultant-particle:nth-child(18) {
  left: 80%;
  top: 30%;
  animation-delay: 17s;
}
.consultant-particle:nth-child(19) {
  left: 90%;
  top: 35%;
  animation-delay: 18s;
}
.consultant-particle:nth-child(20) {
  left: 5%;
  top: 55%;
  animation-delay: 19s;
}
.consultant-particle:nth-child(21) {
  left: 15%;
  top: 65%;
  animation-delay: 1.5s;
}
.consultant-particle:nth-child(22) {
  left: 25%;
  top: 75%;
  animation-delay: 2.5s;
}
.consultant-particle:nth-child(23) {
  left: 35%;
  top: 85%;
  animation-delay: 3.5s;
}
.consultant-particle:nth-child(24) {
  left: 45%;
  top: 95%;
  animation-delay: 4.5s;
}
.consultant-particle:nth-child(25) {
  left: 55%;
  top: 85%;
  animation-delay: 5.5s;
}
.consultant-particle:nth-child(26) {
  left: 65%;
  top: 75%;
  animation-delay: 6.5s;
}
.consultant-particle:nth-child(27) {
  left: 75%;
  top: 65%;
  animation-delay: 7.5s;
}
.consultant-particle:nth-child(28) {
  left: 85%;
  top: 55%;
  animation-delay: 8.5s;
}
.consultant-particle:nth-child(29) {
  left: 95%;
  top: 65%;
  animation-delay: 9.5s;
}
.consultant-particle:nth-child(30) {
  left: 50%;
  top: 75%;
  animation-delay: 0.5s;
}

/* Delay classes */
.delay-0 {
  animation-delay: 0ms;
}

.delay-200 {
  animation-delay: 200ms;
}

.delay-300 {
  animation-delay: 300ms;
}

.delay-400 {
  animation-delay: 400ms;
}

.delay-600 {
  animation-delay: 600ms;
}

.delay-800 {
  animation-delay: 800ms;
}

/* Glowing effects */
.shadow-glow {
  box-shadow: 0 0 15px 2px rgba(168, 85, 247, 0.3);
}

.shadow-glow-sm {
  box-shadow: 0 0 10px 1px rgba(168, 85, 247, 0.25);
}
</style>
