<script lang="ts" setup>
interface Props {
  parallaxOffset: number;
  visibleSections: {
    features: boolean;
    courses: boolean;
    consultancy: boolean;
    testimonials: boolean;
    cta: boolean;
  };
}
defineProps<Props>();

// Trust badges with enhanced metadata
const trustBadges = [
  {
    icon: "lock",
    title: "Secure Platform",
    description: "Industry-standard security",
    animationDelay: "delay-0",
    gradient: "from-indigo-400 to-indigo-600",
  },
  {
    icon: "shield",
    title: "Money-back Guarantee",
    description: "30-day satisfaction guarantee",
    animationDelay: "delay-200",
    gradient: "from-purple-400 to-purple-600",
  },
  {
    icon: "clock",
    title: "Lifetime Access",
    description: "Learn at your own pace",
    animationDelay: "delay-400",
    gradient: "from-blue-400 to-blue-600",
  },
  {
    icon: "support",
    title: "24/7 Support",
    description: "Always here to help",
    animationDelay: "delay-600",
    gradient: "from-pink-400 to-pink-600",
  },
];
</script>

<template>
  <section
    id="cta"
    class="py-24 px-4 bg-gradient-to-br from-indigo-600 via-purple-600 to-violet-700 text-white relative overflow-hidden"
  >
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Parallax circles -->
      <div
        class="absolute -right-64 -top-64 w-2/3 h-2/3 bg-gradient-to-r from-white to-indigo-300 opacity-10 rounded-full blur-3xl transform -rotate-45"
        :style="{
          transform: `translateY(${parallaxOffset * 0.3}px) rotate(-45deg)`,
        }"
      />
      <div
        class="absolute -left-64 -bottom-64 w-2/3 h-2/3 bg-gradient-to-r from-white to-purple-300 opacity-10 rounded-full blur-3xl transform rotate-45"
        :style="{
          transform: `translateY(${parallaxOffset * -0.3}px) rotate(45deg)`,
        }"
      />

      <!-- Animated particles -->
      <div
        v-for="i in 20"
        :key="i"
        class="cta-particle absolute w-1 h-1 rounded-full bg-white"
      />

      <!-- Subtle grid overlay -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto max-w-5xl relative z-10">
      <!-- Main content with staggered animations -->
      <div class="text-center">
        <!-- Label badge -->
        <div class="mb-6 inline-block animate-fade-in">
          <span
            class="bg-white bg-opacity-10 backdrop-blur-sm text-black text-sm font-bold px-4 py-2 rounded-full shadow-glow"
          >
            Get Started Today
          </span>
        </div>


        <!-- Subheading -->
        <p
          class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto text-indigo-100 leading-relaxed animate-fade-in"
          style="animation-delay: 400ms"
        >
          Whether you're looking to learn new skills or need expert consultancy,
          we've got you covered. Join our platform today!
        </p>

        <!-- CTA buttons -->
        <div
          class="flex flex-col sm:flex-row justify-center gap-6 mb-16 animate-fade-in"
          style="animation-delay: 600ms"
        >
          <!-- Create account button -->
          <a
            href="/auth/register"
            class="bg-white text-indigo-600 font-bold py-5 px-10 rounded-xl hover:bg-opacity-95 transition-all duration-300 transform hover:scale-105 hover:shadow-xl shadow-lg text-lg relative overflow-hidden group"
          >
            <span class="relative z-10 flex items-center justify-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-2"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                />
              </svg>
              Create Free Account
            </span>
            <div
              class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-10 transition-opacity duration-300"
            />
            <div
              class="absolute top-0 left-0 w-full h-full bg-white opacity-0 group-hover:opacity-20 transform -translate-x-full group-hover:translate-x-full transition-all duration-1000"
            />
          </a>

          <!-- Request consultancy button -->
          <a
            href="/consultancy/request"
            class="border-2 border-white text-white font-bold py-5 px-10 rounded-xl hover:bg-white hover:text-indigo-600 transition-all duration-300 shadow-lg hover:shadow-xl text-lg flex items-center justify-center group"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 transition-transform duration-300 group-hover:scale-110"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                clip-rule="evenodd"
              />
            </svg>
            Request Consultancy
          </a>
        </div>

        <!-- Divider with animated gradient -->
        <div
          class="relative h-0.5 max-w-lg mx-auto mb-16 overflow-hidden rounded-full animate-fade-in opacity-60"
          style="animation-delay: 700ms"
        >
          <div
            class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent animate-shimmer"
          />
        </div>

        <!-- Trust badges -->
        <div
          class="grid grid-cols-2 md:grid-cols-4 gap-8 animate-fade-in"
          style="animation-delay: 800ms"
        >
          <div
            v-for="(badge, index) in trustBadges"
            :key="index"
            class="badge-card animate-float"
            :style="`animation-delay: ${index * 0.2}s`"
          >
            <div
              class="flex flex-col items-center transform transition-transform duration-300 hover:scale-105"
            >
              <!-- Icon container with hover effect -->
              <div class="p-4 rounded-full mb-4 relative group">
                <!-- Background with gradient that changes on hover -->
                <div
                  class="absolute inset-0 rounded-full bg-white bg-opacity-20 group-hover:bg-opacity-30 transition-all duration-300 backdrop-filter backdrop-blur-sm"
                />

                <!-- Icon -->
                <div class="relative z-10">
                  <!-- Lock icon (Security) -->
                  <svg
                    v-if="badge.icon === 'lock'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                    />
                  </svg>

                  <!-- Shield icon (Guarantee) -->
                  <svg
                    v-if="badge.icon === 'shield'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                    />
                  </svg>

                  <!-- Clock icon (Lifetime access) -->
                  <svg
                    v-if="badge.icon === 'clock'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>

                  <!-- Support icon (24/7 Support) -->
                  <svg
                    v-if="badge.icon === 'support'"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                  </svg>
                </div>

                <!-- Pulsing effect -->
                <div
                  class="absolute inset-0 rounded-full bg-gradient-to-r opacity-0 group-hover:opacity-40 blur-sm animate-pulse-slow"
                  :class="badge.gradient"
                />
              </div>

              <!-- Badge text -->
              <div class="font-semibold mb-1 text-lg">{{ badge.title }}</div>
              <div class="text-sm opacity-80">{{ badge.description }}</div>
            </div>
          </div>
        </div>

        <!-- Students counter -->
        <div
          class="mt-16 flex flex-col md:flex-row items-center justify-center gap-6 text-indigo-100 animate-fade-in"
          style="animation-delay: 1000ms"
        >
          <!-- Student avatars -->
          <div class="flex -space-x-3">
            <div
              class="w-10 h-10 rounded-full bg-gradient-to-r from-pink-400 to-red-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold"
            >
              JS
            </div>
            <div
              class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-indigo-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold"
            >
              KP
            </div>
            <div
              class="w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-emerald-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold"
            >
              AR
            </div>
            <div
              class="w-10 h-10 rounded-full bg-gradient-to-r from-yellow-400 to-amber-400 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold"
            >
              MT
            </div>
            <div
              class="w-10 h-10 rounded-full bg-indigo-800 border-2 border-indigo-600 flex items-center justify-center text-xs font-bold"
            >
              +10K
            </div>
          </div>

          <!-- Counter text -->
          <div class="text-center md:text-left">
            <span class="opacity-80"> Join over </span>
            <span class="font-bold text-white counter-animate"> 15,000+ </span>
            <span class="opacity-80"
              > students who have already transformed their careers with us </span
            >
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

/* Animation keyframes */
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

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
    opacity: 0.5;
  }
  100% {
    transform: translateX(100%);
    opacity: 0.5;
  }
}

@keyframes gradientText {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

@keyframes pulseSlow {
  0%,
  100% {
    opacity: 0.3;
  }
  50% {
    opacity: 0.6;
  }
}

@keyframes floatParticle {
  0% {
    transform: translateY(0);
    opacity: 0.3;
  }
  100% {
    transform: translateY(-100vh);
    opacity: 0;
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
  animation: fadeIn 0.8s forwards;
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

.animate-pulse-slow {
  animation: pulseSlow 3s infinite;
}

.animate-gradient-text {
  background-size: 300% 300%;
  background-image: linear-gradient(
    -45deg,
    rgba(255, 255, 255, 1) 0%,
    rgba(255, 255, 255, 0.8) 25%,
    rgba(255, 255, 255, 1) 50%,
    rgba(255, 255, 255, 0.8) 75%,
    rgba(255, 255, 255, 1) 100%
  );
  color: transparent;
  background-clip: text;
  -webkit-background-clip: text;
  animation: gradientText 6s ease infinite;
}

.counter-animate {
  opacity: 0;
  animation: countUp 0.8s forwards;
  animation-delay: 0.3s;
}

/* CTA particles */
.cta-particle {
  position: absolute;
  opacity: 0.3;
  animation: floatParticle 15s linear infinite;
}

/* Position and delay for each particle */
.cta-particle:nth-child(1) {
  left: 5%;
  bottom: 0;
  animation-duration: 20s;
}
.cta-particle:nth-child(2) {
  left: 10%;
  bottom: 0;
  animation-duration: 18s;
  animation-delay: 1s;
}
.cta-particle:nth-child(3) {
  left: 15%;
  bottom: 0;
  animation-duration: 15s;
  animation-delay: 2s;
}
.cta-particle:nth-child(4) {
  left: 20%;
  bottom: 0;
  animation-duration: 17s;
  animation-delay: 3s;
}
.cta-particle:nth-child(5) {
  left: 25%;
  bottom: 0;
  animation-duration: 19s;
  animation-delay: 4s;
}
.cta-particle:nth-child(6) {
  left: 30%;
  bottom: 0;
  animation-duration: 14s;
  animation-delay: 5s;
}
.cta-particle:nth-child(7) {
  left: 35%;
  bottom: 0;
  animation-duration: 16s;
  animation-delay: 6s;
}
.cta-particle:nth-child(8) {
  left: 40%;
  bottom: 0;
  animation-duration: 18s;
  animation-delay: 7s;
}
.cta-particle:nth-child(9) {
  left: 45%;
  bottom: 0;
  animation-duration: 20s;
  animation-delay: 8s;
}
.cta-particle:nth-child(10) {
  left: 50%;
  bottom: 0;
  animation-duration: 15s;
  animation-delay: 9s;
}
.cta-particle:nth-child(11) {
  left: 55%;
  bottom: 0;
  animation-duration: 17s;
  animation-delay: 0.5s;
}
.cta-particle:nth-child(12) {
  left: 60%;
  bottom: 0;
  animation-duration: 19s;
  animation-delay: 1.5s;
}
.cta-particle:nth-child(13) {
  left: 65%;
  bottom: 0;
  animation-duration: 21s;
  animation-delay: 2.5s;
}
.cta-particle:nth-child(14) {
  left: 70%;
  bottom: 0;
  animation-duration: 16s;
  animation-delay: 3.5s;
}
.cta-particle:nth-child(15) {
  left: 75%;
  bottom: 0;
  animation-duration: 18s;
  animation-delay: 4.5s;
}
.cta-particle:nth-child(16) {
  left: 80%;
  bottom: 0;
  animation-duration: 20s;
  animation-delay: 5.5s;
}
.cta-particle:nth-child(17) {
  left: 85%;
  bottom: 0;
  animation-duration: 22s;
  animation-delay: 6.5s;
}
.cta-particle:nth-child(18) {
  left: 90%;
  bottom: 0;
  animation-duration: 17s;
  animation-delay: 7.5s;
}
.cta-particle:nth-child(19) {
  left: 95%;
  bottom: 0;
  animation-duration: 19s;
  animation-delay: 8.5s;
}
.cta-particle:nth-child(20) {
  left: 98%;
  bottom: 0;
  animation-duration: 21s;
  animation-delay: 9.5s;
}

/* Glowing shadow effect */
.shadow-glow {
  box-shadow: 0 0 15px 2px rgba(255, 255, 255, 0.3);
}

/* Animation delay classes */
.delay-0 {
  animation-delay: 0ms;
}

.delay-200 {
  animation-delay: 200ms;
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
</style>
