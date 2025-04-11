<!-- layouts/auth.vue -->
<script setup lang="ts">
import { faker } from "@faker-js/faker";

// Define types
type ContentType = "e-learning" | "consultancy";

interface Particle {
  id: number;
  x: number;
  y: number;
  size: number;
  speed: number;
}

interface Testimonial {
  name: string;
  role: string;
  avatar: string;
  quote: string;
}

// Particles background
const particlesCount = 20;
const particles = ref<Particle[]>([]);

// Switch between different content focuses (e-learning vs consultancy)
const contentType = ref<ContentType>("e-learning");

// Timer to switch content
const switchContent = (): void => {
  contentType.value =
    contentType.value === "e-learning" ? "consultancy" : "e-learning";
};

// Set interval to switch content every 20 seconds
let contentInterval: number | undefined;
onMounted(() => {
  contentInterval = window.setInterval(switchContent, 20000);

  // Generate random particles for background
  for (let i = 0; i < particlesCount; i++) {
    particles.value.push({
      id: i,
      x: Math.random() * 100,
      y: Math.random() * 100,
      size: 1 + Math.random() * 3,
      speed: 15 + Math.random() * 40,
    });
  }
});

onUnmounted(() => {
  if (contentInterval) window.clearInterval(contentInterval);
});

// Generate testimonials based on content type
const generateTestimonials = (): Testimonial[] => {
  const testimonialsList: Testimonial[] = [];

  const eLearningQuotes: string[] = [
    "The courses on this platform are exceptional. We've trained our entire team and seen a 40% boost in productivity.",
    "The interactive learning modules and progress tracking make it easy to monitor our team's growth and development.",
    "Finding quality educational content was a challenge until we discovered this platform. It's transformed how we train our employees.",
    "The certification programs helped our team gain crucial skills while maintaining their regular work schedule.",
    "The personalized learning paths ensure everyone on our team develops exactly the skills they need for their role.",
  ];

  const consultancyQuotes: string[] = [
    "Their consultancy services transformed our business processes. Implementation was smooth and results were immediate.",
    "The expert guidance we received helped us navigate complex challenges during our digital transformation.",
    "Their consultants identified inefficiencies we hadn't noticed and implemented solutions that saved us thousands each month.",
    "Working with their team gave us access to expertise we couldn't have afforded to hire full-time.",
    "The strategic roadmap they developed has become our blueprint for growth over the next three years.",
  ];

  const eLearningRoles: string[] = [
    "Training Director",
    "Learning Officer",
    "HR Manager",
    "Team Lead",
    "Education Coordinator",
    "Development Director",
    "Talent Manager",
  ];

  const consultancyRoles: string[] = [
    "CEO",
    "Operations Director",
    "CTO",
    "Project Manager",
    "Business Analyst",
    "Strategy Director",
    "Digital Transformation Lead",
  ];

  const currentQuotes: string[] =
    contentType.value === "e-learning" ? eLearningQuotes : consultancyQuotes;
  const currentRoles: string[] =
    contentType.value === "e-learning" ? eLearningRoles : consultancyRoles;

  for (let i = 0; i < 3; i++) {
    testimonialsList.push({
      name: faker.person.fullName(),
      role: currentRoles[Math.floor(Math.random() * currentRoles.length)],
      avatar: faker.image.avatarLegacy(),
      quote: currentQuotes[i % currentQuotes.length],
    });
  }

  return testimonialsList;
};

// Computed text based on content type
const headlineText = computed<string>(() => {
  return contentType.value === "e-learning"
    ? "Learn today, lead tomorrow"
    : "Expert guidance when you need it most";
});

const subheadingText = computed<string>(() => {
  return contentType.value === "e-learning"
    ? "Access world-class courses and hands-on learning experiences designed to accelerate your skills and career."
    : "Our consultants work alongside your team to solve complex challenges and unlock opportunities for sustainable growth.";
});

const testimonialsTitle = computed<string>(() => {
  return contentType.value === "e-learning"
    ? "Trusted by leading education innovators"
    : "Trusted by top businesses worldwide";
});

// Generate testimonials
const testimonials = ref<Testimonial[]>(generateTestimonials());

// Update testimonials when content type changes
watch(contentType, (): void => {
  testimonials.value = generateTestimonials();
});

// Handler for manual content type change
const setContentType = (type: ContentType): void => {
  contentType.value = type;
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="min-h-screen flex bg-gray-50">
    <!-- Left side - Illustration -->
    <div
      class="hidden lg:block lg:w-1/2 relative overflow-hidden transition-colors duration-1000"
      :class="{
        'bg-gradient-to-br from-indigo-600 via-purple-700 to-violet-900':
          contentType === 'e-learning',
        'bg-gradient-to-br from-emerald-600 via-teal-700 to-blue-900':
          contentType === 'consultancy',
      }"
    >
      <!-- Animated particles background -->
      <div class="absolute inset-0 overflow-hidden">
        <div
          v-for="particle in particles"
          :key="particle.id"
          class="absolute rounded-full bg-white opacity-60"
          :style="{
            left: `${particle.x}%`,
            top: `${particle.y}%`,
            width: `${particle.size}px`,
            height: `${particle.size}px`,
            animation: `float ${particle.speed}s infinite linear`,
          }"
        />
      </div>

      <div class="absolute inset-0 p-12 flex flex-col justify-between z-10">
        <div class="flex justify-between items-center">
          <SvgAppLogo variant="light" />

          <div
            class="bg-white bg-opacity-30 backdrop-blur-sm rounded-full px-4 py-1.5 flex space-x-2 items-center shadow-md"
          >
            <Icon
              :name="
                contentType === 'e-learning'
                  ? 'ic:baseline-school'
                  : 'ic:baseline-business-center'
              "
              class="text-indigo-800"
              size="16"
            />
            <span class="text-indigo-800 text-sm font-bold">{{
              contentType === "e-learning" ? "E-Learning" : "Consultancy"
            }}</span>
          </div>
        </div>

        <div class="">
          <Transition name="fade" mode="out-in" appear>
            <div :key="contentType">
              <div class="max-w-md">
                <div class="trusted-badge mb-6 pb-1">
                  <span
                    class="px-4 py-1.5 bg- bg-opacity-30 backdrop-blur-sm rounded-full text-xs font-extrabold text-white uppercase tracking-wide shadow-md border border-white border-opacity-20"
                  >
                    {{ testimonialsTitle }}
                  </span>
                </div>

                <h2
                  class="text-5xl font-bold text-white mb-4 leading-tight drop-shadow-md"
                >
                  {{ headlineText }}
                </h2>
                <p class="text-white text-opacity-90 text-lg">
                  {{ subheadingText }}
                </p>
              </div>

              <div class="mt-12 flex space-x-6">
                <AuthTestimonialBadge
                  v-for="(testimonial, index) in testimonials"
                  :key="index"
                  :name="testimonial.name"
                  :role="testimonial.role"
                  :avatar="testimonial.avatar"
                  :quote="testimonial.quote"
                />
              </div>
            </div>
          </Transition>
        </div>

        <div class="flex items-center justify-between">
          <SvgWaveSvg />

          <div class="flex space-x-2">
            <button
              :class="[
                'px-3 py-1 rounded-full flex items-center text-sm font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50',
                contentType === 'e-learning'
                  ? 'bg-white text-indigo-800'
                  : 'bg-indigo-700 bg-opacity-50 text-white hover:bg-opacity-70',
              ]"
              type="button"
              @click="setContentType('e-learning')"
            >
              <Icon
                name="ic:baseline-school"
                class="mr-1"
                size="16"
                :class="
                  contentType === 'e-learning'
                    ? 'text-indigo-800'
                    : 'text-white'
                "
              />
              Learning
            </button>
            <button
              :class="[
                'px-3 py-1 rounded-full flex items-center text-sm font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50',
                contentType === 'consultancy'
                  ? 'bg-white text-teal-800'
                  : 'bg-teal-700 bg-opacity-50 text-white hover:bg-opacity-70',
              ]"
              type="button"
              @click="setContentType('consultancy')"
            >
              <Icon
                name="ic:baseline-business-center"
                class="mr-1"
                size="16"
                :class="
                  contentType === 'consultancy' ? 'text-teal-800' : 'text-white'
                "
              />
              Consultancy
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right side - Content -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md">
        <div class="lg:hidden mb-8 flex justify-between items-center">
          <SvgAppLogo />

          <div
            class="rounded-full px-3 py-1 text-sm font-medium flex items-center space-x-1"
            :class="
              contentType === 'e-learning'
                ? 'bg-indigo-100 text-indigo-800'
                : 'bg-teal-100 text-teal-800'
            "
          >
            <Icon
              :name="
                contentType === 'e-learning'
                  ? 'ic:baseline-school'
                  : 'ic:baseline-business-center'
              "
              size="16"
            />
            <span>{{
              contentType === "e-learning" ? "E-Learning" : "Consultancy"
            }}</span>
          </div>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>

<style scoped>
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

.trusted-badge {
  position: relative;
  overflow: hidden;
  display: inline-block;
  z-index: 20;
}

.trusted-badge::after {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.5) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: rotate(30deg);
  animation: shine 6s infinite linear;
  z-index: 1;
}

@keyframes shine {
  0% {
    transform: translateX(-100%) rotate(30deg);
  }
  100% {
    transform: translateX(100%) rotate(30deg);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.fade-enter-to,
.fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>
