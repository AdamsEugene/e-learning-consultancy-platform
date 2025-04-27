<!-- components/consultancy/ConsultancyHero.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

interface Props {
  title?: string;
  subtitle?: string;
  description?: string;
  buttonText?: string;
  buttonLink?: string;
  secondaryButtonText?: string;
  secondaryButtonLink?: string;
  background?: "gradient" | "image" | "pattern";
  imageUrl?: string;
  showParticles?: boolean;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  title: "Expert Consultancy Services",
  subtitle: "Tailored Solutions for Your Business",
  description:
    "Our team of expert consultants can help your business overcome challenges and achieve measurable growth with customized solutions.",
  buttonText: "Request Consultation",
  buttonLink: "/consultancy/request",
  secondaryButtonText: "View Services",
  secondaryButtonLink: "/consultancy#services",
  background: "gradient",
  imageUrl: "",
  showParticles: true,
});

// State
const isVisible = ref(false);
const particles = ref<
  {
    id: number;
    x: number;
    y: number;
    size: number;
    speed: number;
    opacity: number;
  }[]
>([]);
const particlesCount = 20;

// Generate random particles
const generateParticles = () => {
  const newParticles = [];
  for (let i = 0; i < particlesCount; i++) {
    newParticles.push({
      id: i,
      x: Math.random() * 100,
      y: Math.random() * 100,
      size: 1 + Math.random() * 3,
      speed: 15 + Math.random() * 40,
      opacity: 0.1 + Math.random() * 0.4,
    });
  }
  particles.value = newParticles;
};

// Lifecycle
onMounted(() => {
  // Generate particles
  if (props.showParticles) {
    generateParticles();
  }

  // Trigger animations
  setTimeout(() => {
    isVisible.value = true;
  }, 100);
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    class="consultancy-hero relative py-20 md:py-28 overflow-hidden"
    :class="{
      'bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800':
        background === 'gradient',
      'bg-gray-900': background === 'image' || background === 'pattern',
    }"
  >
    <!-- Background image if specified -->
    <div v-if="background === 'image' && imageUrl" class="absolute inset-0 z-0">
      <img
        :src="imageUrl"
        alt="Background"
        class="w-full h-full object-cover opacity-50"
      />
      <div
        class="absolute inset-0 bg-indigo-900 opacity-60 mix-blend-multiply"
      />
    </div>

    <!-- Background pattern -->
    <div
      v-if="background === 'pattern'"
      class="absolute inset-0 z-0 opacity-10"
    >
      <div
        class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800"
      />
      <div
        class="h-full w-full"
        style="
          background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.2\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        "
      />
    </div>

    <!-- Animated particles -->
    <div v-if="showParticles" class="absolute inset-0 z-0 overflow-hidden">
      <div
        v-for="particle in particles"
        :key="particle.id"
        class="absolute rounded-full bg-white"
        :style="{
          left: `${particle.x}%`,
          top: `${particle.y}%`,
          width: `${particle.size}px`,
          height: `${particle.size}px`,
          opacity: particle.opacity,
          animation: `float ${particle.speed}s infinite linear`,
        }"
      />
    </div>

    <!-- Decorative elements -->
    <div
      class="absolute top-0 right-0 w-96 h-96 bg-indigo-500 rounded-full filter blur-3xl opacity-20 -mr-40 -mt-40"
    />
    <div
      class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500 rounded-full filter blur-3xl opacity-20 -ml-40 -mb-40"
    />

    <!-- Content container -->
    <div class="container mx-auto px-4 relative z-10">
      <div class="max-w-3xl mx-auto text-center text-white">
        <!-- Subtitle with animated entrance -->
        <div
          class="inline-block mb-4 px-4 py-1 rounded-full bg-white text-black bg-opacity-20 backdrop-blur-sm transform transition-all duration-1000"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
        >
          <span class="text-sm font-bold">{{ subtitle }}</span>
        </div>

        <!-- Main title with animated entrance -->
        <h1
          class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 transform transition-all duration-1000 leading-tight"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 200ms"
        >
          {{ title }}
        </h1>

        <!-- Description with animated entrance -->
        <p
          class="text-lg md:text-xl text-indigo-100 mb-8 max-w-2xl mx-auto transform transition-all duration-1000"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 400ms"
        >
          {{ description }}
        </p>

        <!-- Action buttons with animated entrance -->
        <div
          class="flex flex-wrap justify-center gap-4 transform transition-all duration-1000"
          :class="
            isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'
          "
          style="transition-delay: 600ms"
        >
          <!-- Primary CTA button -->
          <NuxtLink
            :to="buttonLink"
            class="primary-button bg-white text-indigo-700 font-bold py-3 px-8 rounded-lg hover:bg-opacity-90 transition-all shadow-xl transform hover:scale-105 hover:translate-y-1 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50"
          >
            {{ buttonText }}
          </NuxtLink>

          <!-- Secondary CTA button -->
          <NuxtLink
            :to="secondaryButtonLink"
            class="border-2 border-white text-white font-bold py-3 px-8 rounded-lg hover:scale-105 hover:translate-y-1  hover:bg-opacity-20 transition-all focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50"
          >
            {{ secondaryButtonText }}
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
</template>

<style scoped>
/* Custom animations for hero section */
@keyframes float {
  from {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(0) translateX(20px);
  }
  75% {
    transform: translateY(20px) translateX(10px);
  }
  to {
    transform: translateY(0) translateX(0);
  }
}

.primary-button {
  position: relative;
  overflow: hidden;
}

.primary-button::after {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.3) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: rotate(30deg);
  animation: shine 6s infinite;
}

@keyframes shine {
  0% {
    transform: translateX(-100%) rotate(30deg);
  }
  100% {
    transform: translateX(100%) rotate(30deg);
  }
}
</style>
