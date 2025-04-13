<!-- components/consultancy/ConsultancyTestimonialsSection.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

interface Testimonial {
  id: number;
  name: string;
  role: string;
  company: string;
  content: string;
  image?: string;
  rating?: number;
  highlight?: boolean;
}

interface Props {
  title?: string;
  subtitle?: string;
  description?: string;
  testimonials?: Testimonial[];
  autoplay?: boolean;
  interval?: number;
}

// Props
const props = withDefaults(defineProps<Props>(), {
  title: "What Our Clients Say",
  subtitle: "Client Success Stories",
  description:
    "Don't just take our word for it. See what our clients have to say about their experience working with our consultancy team.",
  testimonials: () => [
    {
      id: 1,
      name: "Emily Thompson",
      role: "CTO",
      company: "TechInnovate Solutions",
      content:
        "Working with this consultancy team transformed our digital strategy. Their expert guidance helped us identify opportunities we had overlooked and implement solutions that improved efficiency by 45%. The team's technical knowledge combined with their business acumen made them invaluable partners.",
      image: "/api/placeholder/100/100",
      rating: 5,
      highlight: true,
    },
    {
      id: 2,
      name: "Marcus Johnson",
      role: "Marketing Director",
      company: "GrowthFocus Media",
      content:
        "The strategic insights provided by the consultancy team revolutionized our marketing approach. They not only identified our challenges but delivered practical solutions that generated a 60% increase in qualified leads within three months. Their data-driven methodology exceeded our expectations.",
      image: "/api/placeholder/100/100",
      rating: 5,
    },
    {
      id: 3,
      name: "Sophia Chen",
      role: "Operations Manager",
      company: "GlobalTrade Systems",
      content:
        "We engaged the consultancy team to optimize our operational workflows, and the results were impressive. They quickly identified bottlenecks in our processes and implemented solutions that reduced processing time by 35%. Their practical approach and clear communication made the entire project seamless.",
      image: "/api/placeholder/100/100",
      rating: 4,
    },
    {
      id: 4,
      name: "Robert Patel",
      role: "CEO",
      company: "FutureBuild Construction",
      content:
        "The consultancy team delivered exceptional value to our business transformation initiative. Their methodical approach to analyzing our challenges and implementing tailored solutions helped us navigate a complex industry transition. The ROI on their services has been substantial.",
      image: "/api/placeholder/100/100",
      rating: 5,
    },
  ],
  autoplay: true,
  interval: 5000,
});

// State
const activeIndex = ref(0);
const isPlaying = ref(props.autoplay);
let autoplayTimer: number | null = null;

// Calculate the modulo of total testimonials
const totalTestimonials = computed(() => props.testimonials.length);
const activeTestimonial = computed(() => props.testimonials[activeIndex.value]);

// Mouse interaction state
const isDragging = ref(false);
const startX = ref(0);
const currentTranslate = ref(0);
const prevTranslate = ref(0);

// Navigation methods
const goToPrev = () => {
  activeIndex.value =
    (activeIndex.value - 1 + totalTestimonials.value) % totalTestimonials.value;
  resetAutoplay();
};

const goToNext = () => {
  activeIndex.value = (activeIndex.value + 1) % totalTestimonials.value;
  resetAutoplay();
};

const goToSlide = (index: number) => {
  activeIndex.value = index;
  resetAutoplay();
};

// Autoplay methods
const startAutoplay = () => {
  if (props.autoplay && !autoplayTimer) {
    autoplayTimer = window.setInterval(() => {
      goToNext();
    }, props.interval);
    isPlaying.value = true;
  }
};

const stopAutoplay = () => {
  if (autoplayTimer) {
    clearInterval(autoplayTimer);
    autoplayTimer = null;
    isPlaying.value = false;
  }
};

const resetAutoplay = () => {
  stopAutoplay();
  if (props.autoplay) {
    startAutoplay();
  }
};

const toggleAutoplay = () => {
  if (isPlaying.value) {
    stopAutoplay();
  } else {
    startAutoplay();
  }
};

// Touch/mouse event handlers
const handleDragStart = (e: MouseEvent | TouchEvent) => {
  isDragging.value = true;
  if (e instanceof MouseEvent) {
    startX.value = e.clientX;
  } else {
    startX.value = e.touches[0].clientX;
  }
  stopAutoplay();
};

const handleDragMove = (e: MouseEvent | TouchEvent) => {
  if (!isDragging.value) return;

  let currentX;
  if (e instanceof MouseEvent) {
    currentX = e.clientX;
  } else {
    currentX = e.touches[0].clientX;
  }

  const diff = currentX - startX.value;
  currentTranslate.value = prevTranslate.value + diff;
};

const handleDragEnd = () => {
  isDragging.value = false;
  const threshold = 100; // Minimum distance to trigger slide change

  if (currentTranslate.value - prevTranslate.value > threshold) {
    // Swiped right
    goToPrev();
  } else if (prevTranslate.value - currentTranslate.value > threshold) {
    // Swiped left
    goToNext();
  }

  prevTranslate.value = 0;
  currentTranslate.value = 0;

  if (props.autoplay) {
    startAutoplay();
  }
};

// Generate stars for ratings
const generateStars = (rating: number) => {
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5;
  const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

  return {
    full: fullStars,
    half: halfStar,
    empty: emptyStars,
  };
};

// Lifecycle hooks
onMounted(() => {
  if (props.autoplay) {
    startAutoplay();
  }

  const carouselElement = document.querySelector(".testimonial-carousel");
  if (carouselElement) {
    carouselElement.addEventListener(
      "touchstart",
      handleDragStart as EventListener,
      {
        passive: true,
      }
    );
    carouselElement.addEventListener(
      "touchmove",
      handleDragMove as EventListener,
      {
        passive: true,
      }
    );
    carouselElement.addEventListener("touchend", handleDragEnd);

    carouselElement.addEventListener(
      "mousedown",
      handleDragStart as EventListener,
      {
        passive: true,
      }
    );
    window.addEventListener("mousemove", handleDragMove);
    window.addEventListener("mouseup", handleDragEnd);
  }
});

onBeforeUnmount(() => {
  stopAutoplay();

  const carouselElement = document.querySelector(".testimonial-carousel");
  if (carouselElement) {
    carouselElement.removeEventListener(
      "touchstart",
      handleDragStart as EventListener
    );
    carouselElement.removeEventListener(
      "touchmove",
      handleDragMove as EventListener
    );
    carouselElement.removeEventListener(
      "touchend",
      handleDragEnd as EventListener
    );

    carouselElement.removeEventListener(
      "mousedown",
      handleDragStart as EventListener
    );
    window.removeEventListener("mousemove", handleDragMove);
    window.removeEventListener("mouseup", handleDragEnd);
  }
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section
    class="py-20 bg-gradient-to-br from-indigo-50 to-indigo-100 overflow-hidden relative"
  >
    <!-- Decorative elements -->
    <div
      class="absolute top-0 right-0 w-64 h-64 bg-indigo-200 rounded-full opacity-20 -mr-32 -mt-32"
    />
    <div
      class="absolute bottom-0 left-0 w-64 h-64 bg-purple-200 rounded-full opacity-20 -ml-32 -mb-32"
    />

    <!-- Quote icons scattered around -->
    <div class="absolute top-1/4 left-1/6 text-indigo-200 opacity-30">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-16 w-16"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z"
        />
      </svg>
    </div>

    <div
      class="absolute bottom-1/3 right-1/6 text-purple-200 opacity-30 transform rotate-180"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-12 w-12"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z"
        />
      </svg>
    </div>

    <div class="container mx-auto px-4 relative z-10">
      <!-- Section header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span
          class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 mb-4"
        >
          {{ subtitle }}
        </span>
        <h2 class="text-3xl md:text-4xl font-bold mb-6">{{ title }}</h2>
        <p class="text-lg text-gray-600">{{ description }}</p>
      </div>

      <!-- Testimonials carousel -->
      <div
        class="testimonial-carousel max-w-5xl mx-auto relative"
        :class="{ 'cursor-grab': !isDragging, 'cursor-grabbing': isDragging }"
      >
        <!-- Main testimonial display -->
        <div class="overflow-hidden relative">
          <div
            class="relative transition-all duration-700"
            :style="`transform: translateX(-${activeIndex * 100}%)`"
          >
            <div class="flex">
              <div
                v-for="(testimonial, index) in testimonials"
                :key="testimonial.id"
                class="w-full flex-shrink-0 px-4"
              >
                <!-- Testimonial card -->
                <div
                  class="bg-white rounded-xl p-8 shadow-xl border border-gray-100 relative transform transition-all duration-500"
                  :class="[
                    activeIndex === index
                      ? 'scale-100 opacity-100'
                      : 'scale-95 opacity-60',
                    testimonial.highlight ? 'border-indigo-200' : '',
                  ]"
                >
                  <!-- Testimonial quote icon -->
                  <div
                    class="absolute -top-5 left-8 bg-indigo-600 rounded-full w-10 h-10 flex items-center justify-center shadow-lg"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-white"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z"
                      />
                    </svg>
                  </div>

                  <!-- Testimonial content -->
                  <div class="mb-8 pt-6">
                    <p class="text-gray-700 text-lg italic leading-relaxed">
                      "{{ testimonial.content }}"
                    </p>
                  </div>

                  <!-- Testimonial author info -->
                  <div class="flex items-center">
                    <div class="mr-4">
                      <img
                        :src="testimonial.image || '/api/placeholder/64/64'"
                        :alt="testimonial.name"
                        class="w-16 h-16 rounded-full object-cover border-2 border-indigo-100"
                      />
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900">
                        {{ testimonial.name }}
                      </h4>
                      <div class="text-gray-600 text-sm">
                        {{ testimonial.role }}, {{ testimonial.company }}
                      </div>

                      <!-- Rating stars -->
                      <div v-if="testimonial.rating" class="flex mt-1">
                        <template
                          v-for="i in generateStars(testimonial.rating).full"
                          :key="`full-${i}`"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-yellow-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                          </svg>
                        </template>
                        <template v-if="generateStars(testimonial.rating).half">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-yellow-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                          </svg>
                        </template>
                        <template
                          v-for="i in generateStars(testimonial.rating).empty"
                          :key="`empty-${i}`"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-gray-300"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                            />
                          </svg>
                        </template>
                      </div>
                    </div>
                  </div>

                  <!-- Featured testimonial badge -->
                  <div
                    v-if="testimonial.highlight"
                    class="absolute top-4 right-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md"
                  >
                    Featured
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation controls -->
        <div class="flex justify-between items-center mt-8">
          <!-- Previous button -->
          <button
            class="bg-white text-indigo-600 p-3 rounded-full shadow-md hover:bg-indigo-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transform hover:scale-110"
            aria-label="Previous testimonial"
            @click="goToPrev"
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
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>

          <!-- Indicators -->
          <div class="flex space-x-2 items-center">
            <button
              v-for="(_, index) in testimonials"
              :key="index"
              class="transition-all duration-300 focus:outline-none"
              :class="[
                activeIndex === index
                  ? 'w-10 bg-indigo-600'
                  : 'w-3 bg-indigo-300 hover:bg-indigo-400',
                'h-3 rounded-full',
              ]"
              :aria-label="`Go to testimonial ${index + 1}`"
              @click="goToSlide(index)"
            />

            <!-- Autoplay toggle -->
            <button
              class="ml-4 text-indigo-600 p-2 rounded-full hover:bg-indigo-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
              :aria-label="isPlaying ? 'Pause autoplay' : 'Start autoplay'"
              @click="toggleAutoplay"
            >
              <svg
                v-if="isPlaying"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </button>
          </div>

          <!-- Next button -->
          <button
            class="bg-white text-indigo-600 p-3 rounded-full shadow-md hover:bg-indigo-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transform hover:scale-110"
            aria-label="Next testimonial"
            @click="goToNext"
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
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Indicators animation */
@keyframes pulse-subtle {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
  }
  50% {
    box-shadow: 0 0 0 6px rgba(79, 70, 229, 0);
  }
}

.active-indicator {
  animation: pulse-subtle 2s infinite;
}

/* Custom cursor styles */
.cursor-grab {
  cursor: grab;
}

.cursor-grabbing {
  cursor: grabbing;
}

/* Prevent text selection during drag */
.testimonial-carousel {
  user-select: none;
}

/* Create a subtle motion effect for the decoration elements */
@keyframes float-slow {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-15px);
  }
}

.decoration-element {
  animation: float-slow 15s infinite ease-in-out;
}
</style>
