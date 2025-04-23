<script lang="ts" setup>
interface Props {
  stats: {
    students: number;
    courses: number;
    instructors: number;
    satisfaction: number;
  };
}
defineProps<Props>();

// Stats metadata with enhanced styling and animation classes
const statItems = [
  {
    key: "students",
    label: "Students",
    suffix: "+",
    icon: "user-graduate",
    classes: {
      bg: "bg-gradient-to-br from-indigo-500 to-indigo-700",
      text: "text-indigo-600",
      hoverText:
        "group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-indigo-600 group-hover:to-indigo-400",
      hoverBg: "hover:bg-indigo-50",
      iconBg: "bg-indigo-100",
      accent: "border-indigo-500",
      shadow: "shadow-indigo-200",
      ring: "ring-indigo-500/30",
    },
    description: "Active learners worldwide",
    animationDelay: "delay-0",
  },
  {
    key: "courses",
    label: "Courses",
    suffix: "+",
    icon: "books",
    classes: {
      bg: "bg-gradient-to-br from-blue-500 to-blue-700",
      text: "text-blue-600",
      hoverText:
        "group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-blue-400",
      hoverBg: "hover:bg-blue-50",
      iconBg: "bg-blue-100",
      accent: "border-blue-500",
      shadow: "shadow-blue-200",
      ring: "ring-blue-500/30",
    },
    description: "Expert-created courses",
    animationDelay: "delay-100",
  },
  {
    key: "instructors",
    label: "Expert Instructors",
    suffix: "",
    icon: "chalkboard-teacher",
    classes: {
      bg: "bg-gradient-to-br from-purple-500 to-purple-700",
      text: "text-purple-600",
      hoverText:
        "group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-purple-400",
      hoverBg: "hover:bg-purple-50",
      iconBg: "bg-purple-100",
      accent: "border-purple-500",
      shadow: "shadow-purple-200",
      ring: "ring-purple-500/30",
    },
    description: "Industry professionals",
    animationDelay: "delay-200",
  },
  {
    key: "satisfaction",
    label: "Satisfaction Rate",
    suffix: "%",
    icon: "star",
    classes: {
      bg: "bg-gradient-to-br from-amber-500 to-amber-700",
      text: "text-amber-600",
      hoverText:
        "group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-amber-600 group-hover:to-amber-400",
      hoverBg: "hover:bg-amber-50",
      iconBg: "bg-amber-100",
      accent: "border-amber-500",
      shadow: "shadow-amber-200",
      ring: "ring-amber-500/30",
    },
    description: "From verified reviews",
    animationDelay: "delay-300",
  },
];
</script>

<template>
  <section
    id="stats"
    class="py-16 relative overflow-hidden bg-gradient-to-b from-white to-indigo-50"
  >
    <!-- Background decoration with animated particles -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Decorative blurred circles -->
      <div
        class="absolute -right-64 top-0 w-96 h-96 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-full opacity-30 blur-2xl"
      />
      <div
        class="absolute -left-32 -bottom-32 w-80 h-80 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full opacity-30 blur-2xl"
      />

      <!-- Animated particles -->
      <div
        v-for="i in 20"
        :key="i"
        class="particle absolute rounded-full bg-white opacity-60"
      />

      <!-- Decorative grid pattern -->
      <div class="absolute inset-0 bg-grid-pattern opacity-5" />
    </div>

    <div class="container mx-auto px-4 relative z-10">
      <!-- Section title with animated underline -->
      <div class="mb-12 text-center relative">
        <div
          class="inline-block text-sm font-bold px-3 py-1 rounded-full mb-4 bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 shadow-sm"
        >
          Our Numbers
        </div>

        <h2
          class="text-3xl md:text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600"
        >
          Platform Impact
        </h2>

        <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-6">
          See what we've accomplished together with our growing community
        </p>

        <!-- Animated underline -->
        <div
          class="h-1 w-24 bg-gradient-to-r from-indigo-500 to-purple-500 mx-auto rounded-full relative overflow-hidden"
        >
          <div class="absolute inset-0 bg-white animate-shimmer" />
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <div v-for="item in statItems" :key="item.key" class="stat-item">
          <div
            class="bg-white rounded-lg p-6 h-full flex flex-col items-center justify-between transition-all duration-300 hover:shadow-lg border-l-4 border-transparent hover:border-l-4"
            :class="[item.classes.accent]"
          >
            <div class="w-12 h-12 mb-4 flex items-center justify-center rounded-full" :class="item.classes.iconBg">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                :class="item.classes.text"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <!-- User Graduate icon -->
                <path
                  v-if="item.icon === 'user-graduate'"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 14l9-5-9-5-9 5 9 5m0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
                />
                <!-- Books icon -->
                <path
                  v-if="item.icon === 'books'"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                />
                <!-- Teacher icon -->
                <path
                  v-if="item.icon === 'chalkboard-teacher'"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"
                />
                <!-- Star icon -->
                <path
                  v-if="item.icon === 'star'"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                />
              </svg>
            </div>
            
            <div class="text-center w-full">
              <div class="text-4xl font-bold mb-1" :class="item.classes.text">
                {{ stats[item.key as keyof typeof stats].toLocaleString() }}{{ item.suffix }}
              </div>
              <div class="font-medium text-gray-800 mb-1">
                {{ item.label }}
              </div>
              <div class="text-xs text-gray-500">
                {{ item.description }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced context section with timeline -->
      <div
        class="mt-16 text-center max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-gray-100 transform hover:shadow-2xl transition-all duration-500"
      >
        <p class="text-gray-600 mb-6 leading-relaxed">
          Our platform continues to grow with thousands of new students joining
          monthly. With instructor talent from top companies and universities,
          we deliver industry-relevant content that helps our students succeed
          in their careers.
        </p>

        <!-- Clean modern timeline with progressive animation -->
        <div class="mt-10 max-w-md mx-auto">
          <div class="relative flex items-center justify-between">
            <!-- Animated progress line -->
            <div class="absolute left-0 right-0 h-1 bg-indigo-50"></div>
            <div class="absolute left-0 h-1 bg-indigo-500 timeline-progress-animate"></div>
            
            <!-- Timeline points with sequential animation -->
            <div class="relative flex flex-col items-center timeline-point-animate delay-0">
              <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 transition-all duration-500">
                <span class="text-xs font-medium text-indigo-600">21</span>
              </div>
              <span class="absolute top-10 text-xs text-gray-500">2021</span>
            </div>
            
            <div class="relative flex flex-col items-center timeline-point-animate delay-500">
              <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 transition-all duration-500">
                <span class="text-xs font-medium text-indigo-600">22</span>
              </div>
              <span class="absolute top-10 text-xs text-gray-500">2022</span>
            </div>
            
            <div class="relative flex flex-col items-center timeline-point-animate delay-1000">
              <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 transition-all duration-500">
                <span class="text-xs font-medium text-indigo-600">23</span>
              </div>
              <span class="absolute top-10 text-xs text-gray-500">2023</span>
            </div>
            
            <div class="relative flex flex-col items-center timeline-point-animate delay-1500">
              <div class="z-10 flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 animate-pulse-delayed">
                <span class="text-xs font-bold text-white">NOW</span>
              </div>
              <span class="absolute top-12 text-xs text-gray-500">2024</span>
            </div>
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
      rgba(0, 0, 0, 0.05) 1px,
      transparent 1px
    ),
    linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Animation keyframes */
@keyframes countUp {
  0% {
    transform: translateY(100%);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-6px);
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

@keyframes ping {
  0% {
    transform: scale(0.8);
    opacity: 1;
  }
  75%,
  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

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

@keyframes particle-float {
  0%,
  100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-30px) translateX(10px);
  }
  50% {
    transform: translateY(0) translateX(20px);
  }
  75% {
    transform: translateY(30px) translateX(10px);
  }
}

/* Applied animations */
.animate-fade-in {
  animation: fadeInUp 1s forwards;
}

.animate-float {
  animation: float 5s ease-in-out infinite;
}

.animate-shimmer {
  animation: shimmer 3s infinite;
}

.animate-ping {
  animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Particle styling */
.particle {
  width: 4px;
  height: 4px;
  animation: particle-float calc(10s + (var(--random) * 15s)) infinite linear;
}

.particle:nth-child(1) {
  --random: 0.1;
  left: 10%;
  top: 20%;
}
.particle:nth-child(2) {
  --random: 0.2;
  left: 20%;
  top: 80%;
}
.particle:nth-child(3) {
  --random: 0.3;
  left: 30%;
  top: 30%;
}
.particle:nth-child(4) {
  --random: 0.4;
  left: 40%;
  top: 70%;
}
.particle:nth-child(5) {
  --random: 0.5;
  left: 50%;
  top: 40%;
}
.particle:nth-child(6) {
  --random: 0.6;
  left: 60%;
  top: 90%;
}
.particle:nth-child(7) {
  --random: 0.7;
  left: 70%;
  top: 10%;
}
.particle:nth-child(8) {
  --random: 0.8;
  left: 80%;
  top: 50%;
}
.particle:nth-child(9) {
  --random: 0.9;
  left: 90%;
  top: 30%;
}
.particle:nth-child(10) {
  --random: 1;
  left: 5%;
  top: 60%;
}
.particle:nth-child(11) {
  --random: 0.15;
  left: 15%;
  top: 50%;
}
.particle:nth-child(12) {
  --random: 0.25;
  left: 25%;
  top: 20%;
}
.particle:nth-child(13) {
  --random: 0.35;
  left: 35%;
  top: 80%;
}
.particle:nth-child(14) {
  --random: 0.45;
  left: 45%;
  top: 15%;
}
.particle:nth-child(15) {
  --random: 0.55;
  left: 55%;
  top: 70%;
}
.particle:nth-child(16) {
  --random: 0.65;
  left: 65%;
  top: 40%;
}
.particle:nth-child(17) {
  --random: 0.75;
  left: 75%;
  top: 60%;
}
.particle:nth-child(18) {
  --random: 0.85;
  left: 85%;
  top: 25%;
}
.particle:nth-child(19) {
  --random: 0.95;
  left: 95%;
  top: 75%;
}
.particle:nth-child(20) {
  --random: 0.12;
  left: 8%;
  top: 35%;
}

.counter-value span {
  display: inline-block;
  animation: countUp 1s cubic-bezier(0.32, 0.94, 0.6, 1) forwards;
}

.stat-item:nth-child(1) .counter-value span {
  animation-delay: 0.1s;
}

.stat-item:nth-child(2) .counter-value span {
  animation-delay: 0.2s;
}

.stat-item:nth-child(3) .counter-value span {
  animation-delay: 0.3s;
}

.stat-item:nth-child(4) .counter-value span {
  animation-delay: 0.4s;
}

.delay-0 {
  animation-delay: 0ms;
}

.delay-100 {
  animation-delay: 100ms;
}

.delay-200 {
  animation-delay: 200ms;
}

.delay-300 {
  animation-delay: 300ms;
}

/* Add these animations to the <style> section */
@keyframes timelineProgress {
  0% {
    width: 0%;
  }
  25% {
    width: 33%;
  }
  50% {
    width: 66%;
  }
  75% {
    width: 100%;
  }
  100% {
    width: 100%;
  }
}

@keyframes timelinePointAppear {
  0% {
    opacity: 0;
    transform: scale(0);
  }
  80% {
    opacity: 1;
    transform: scale(1.2);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes pulseDelayed {
  0%, 50% {
    opacity: 1;
  }
  75% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

.timeline-progress-animate {
  animation: timelineProgress 2s ease-out forwards;
}

.timeline-point-animate > div {
  opacity: 0;
  transform: scale(0);
  animation: timelinePointAppear 0.5s ease-out forwards;
}

.delay-0 > div {
  animation-delay: 0.2s;
}

.delay-500 > div {
  animation-delay: 0.7s;
}

.delay-1000 > div {
  animation-delay: 1.2s;
}

.delay-1500 > div {
  animation-delay: 1.7s;
}

.animate-pulse-delayed {
  animation: pulseDelayed 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  animation-delay: 1.7s;
}
</style>
