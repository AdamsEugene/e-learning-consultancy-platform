<script setup lang="ts">
// Animation states
const isFloating = ref(false);
const visibleSections = reactive({
  features: false,
  courses: false,
  consultancy: false,
  testimonials: false,
  cta: false,
});

// Handle typing completed event from hero component
const onTypingCompleted = () => {
  // Start the floating animation after typing is complete
  isFloating.value = true;
};

// Stats counter animation
const stats = reactive({
  students: 0,
  courses: 0,
  instructors: 0,
  satisfaction: 0,
});

const finalStats = {
  students: 15000,
  courses: 150,
  instructors: 48,
  satisfaction: 98,
};

let statsInterval: number | null = null;

const animateStats = () => {
  const duration = 2000; // 2 seconds
  const steps = 40;
  const interval = duration / steps;

  let step = 0;

  statsInterval = window.setInterval(() => {
    step++;
    const progress = step / steps;

    stats.students = Math.round(progress * finalStats.students);
    stats.courses = Math.round(progress * finalStats.courses);
    stats.instructors = Math.round(progress * finalStats.instructors);
    stats.satisfaction = Math.round(progress * finalStats.satisfaction);

    if (step >= steps) {
      clearInterval(statsInterval as number);
    }
  }, interval);
};

const setTestimonial = (index: number) => {
  activeTestimonial.value = index;
  if (testimonialInterval) {
    clearInterval(testimonialInterval);
    rotateTestimonials();
  }
};

// Parallax effect on scroll
const parallaxOffset = ref(0);
const updateParallax = () => {
  parallaxOffset.value = window.scrollY * 0.3;
};

// Scroll animations
const checkScroll = () => {
  const sections = document.querySelectorAll("section");

  // Check if stats section is visible to start counter
  const statsSection = document.getElementById("stats");
  if (statsSection) {
    const rect = statsSection.getBoundingClientRect();
    const isVisible =
      rect.top <= window.innerHeight * 0.8 &&
      rect.bottom >= window.innerHeight * 0.2;

    if (isVisible && stats.students === 0) {
      animateStats();
    }
  }

  sections.forEach((section) => {
    const rect = section.getBoundingClientRect();
    const isVisible =
      rect.top <= window.innerHeight * 0.75 &&
      rect.bottom >= window.innerHeight * 0.25;

    if (isVisible) {
      const id = section.id;
      if (id && Object.hasOwn(visibleSections, id)) {
        visibleSections[id as keyof typeof visibleSections] = true;
      }
    }
  });
};

// Testimonials carousel
const testimonials = ref([
  {
    id: 1,
    name: "Sarah Johnson",
    role: "Frontend Developer",
    image: "/api/placeholder/100/100",
    text: "The courses on this platform helped me transition from a junior to a senior developer in just 8 months. The instructors are incredibly knowledgeable and supportive.",
    rating: 5,
  },
  {
    id: 2,
    name: "Michael Chen",
    role: "CTO at TechStart",
    image: "/api/placeholder/100/100",
    text: "We've used the consultancy services to optimize our tech stack and the results have been outstanding. Our development velocity increased by 40%.",
    rating: 5,
  },
  {
    id: 3,
    name: "Emily Garcia",
    role: "UI/UX Designer",
    image: "/api/placeholder/100/100",
    text: "The design courses are comprehensive and up-to-date. I particularly loved the practical projects that now enhance my portfolio.",
    rating: 4,
  },
]);

const activeTestimonial = ref(0);
let testimonialInterval: number | null = null;

const rotateTestimonials = () => {
  testimonialInterval = window.setInterval(() => {
    activeTestimonial.value =
      (activeTestimonial.value + 1) % testimonials.value.length;
  }, 5000);
};

// Monitor scroll position for various effects
const scrollY = ref(0);

const updateScrollPosition = () => {
  scrollY.value = window.scrollY;
  updateParallax();
};

onMounted(() => {
  window.addEventListener("scroll", checkScroll);
  window.addEventListener("scroll", updateScrollPosition);
  rotateTestimonials();
  // Trigger once to check initial visibility
  checkScroll();
  updateScrollPosition();
});

onBeforeUnmount(() => {
  if (statsInterval) clearInterval(statsInterval);
  if (testimonialInterval) clearInterval(testimonialInterval);
  window.removeEventListener("scroll", checkScroll);
  window.removeEventListener("scroll", updateScrollPosition);
});
</script>

<template>
  <div class="landing-page">
    <!-- Hero Section with dynamic typing for multiple phrases -->
    <LandingHeroSection
      :is-floating="isFloating"
      :parallax-offset="parallaxOffset"
      @typing-completed="onTypingCompleted"
    />

    <!-- Stats Section -->
    <LandingStatsSection :stats="stats" />

    <!-- Features Section -->
    <LandingFeaturesSection :visible-sections="visibleSections" />

    <!-- Course Catalog Preview -->
    <LandingCourseCatalogPreview :visible-sections="visibleSections" />

    <!-- Testimonials Section -->
    <LandingTestimonialsSection
      :testimonials="testimonials"
      :active-testimonial="activeTestimonial"
      :set-testimonial="setTestimonial"
    />

    <!-- Consultancy Services -->
    <LandingConsultancyServices :visible-sections="visibleSections" />

    <!-- FAQ Section -->
    <LandingFAQSection />

    <!-- Call to Action -->
    <LandingCallToAction
      :visible-sections="visibleSections"
      :parallax-offset="parallaxOffset"
    />

    <!-- Newsletter Section -->
    <LandingNewsletterSection />
  </div>
</template>

<style scoped>
.cursor {
  display: inline-block;
  animation: blink 1s infinite;
}

.cursor.typing {
  animation: none;
}

@keyframes blink {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
}

.animate-fade-in {
  animation: fadeIn 1s forwards;
}

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

.feature-card,
.course-card,
.service-card,
.case-study {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.5s, transform 0.5s;
}

.feature-card.animate-fade-in,
.course-card.animate-fade-in,
.service-card.animate-fade-in,
.case-study.animate-fade-in {
  opacity: 1;
  transform: translateY(0);
}

/* Apply transition delays to create staggered animation effect */
.feature-card:nth-child(2),
.course-card:nth-child(2),
.service-card:nth-child(2) {
  transition-delay: 0.2s;
}

.feature-card:nth-child(3),
.course-card:nth-child(3),
.service-card:nth-child(3) {
  transition-delay: 0.4s;
}

.service-card:nth-child(4) {
  transition-delay: 0.6s;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}

.animate-pulse {
  animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
