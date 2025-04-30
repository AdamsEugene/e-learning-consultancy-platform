<!-- components/courses/CourseCard.vue -->
<script setup lang="ts">
interface CourseCardProps {
  id: number;
  title: string;
  category: string;
  image: string;
  instructor: string;
  rating: number;
  reviewCount: number;
  price: number;
  originalPrice?: number;
  level: string;
  duration: string;
  badges?: string[];
  isEnrolled?: boolean;
  progress?: number;
}

const props = defineProps<CourseCardProps>();

// Hover state for enhanced effects
const isHovered = ref(false);

const _isEnrolled = computed(() => {
  return props.isEnrolled && typeof props.progress !== "undefined";
});

// Compute discount percentage if there's an original price
const discountPercentage = computed(() => {
  if (!props.originalPrice || props.originalPrice <= props.price) return null;
  const discount = Math.round(
    ((props.originalPrice - props.price) / props.originalPrice) * 100
  );
  return discount;
});

// Calculate whole stars, partial stars, and empty stars
const starRating = computed(() => {
  const wholeStars = Math.floor(props.rating);
  const hasPartialStar = props.rating % 1 >= 0.5;
  const emptyStars = 5 - wholeStars - (hasPartialStar ? 1 : 0);

  return {
    wholeStars,
    hasPartialStar,
    emptyStars,
  };
});

// Format price as currency
const formattedPrice = computed(() => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(props.price);
});

const formattedOriginalPrice = computed(() => {
  if (!props.originalPrice) return null;
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(props.originalPrice);
});

// Format review count with appropriate abbreviation
const formattedReviewCount = computed(() => {
  if (props.reviewCount >= 1000) {
    return `${(props.reviewCount / 1000).toFixed(1)}k`;
  }
  return props.reviewCount.toLocaleString();
});

// Level badge color and icon
const levelInfo = computed(() => {
  switch (props.level.toLowerCase()) {
    case "beginner":
      return {
        color: "bg-green-100 text-green-800 border border-green-200",
        icon: `<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />`,
      };
    case "intermediate":
      return {
        color: "bg-blue-100 text-blue-800 border border-blue-200",
        icon: `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />`,
      };
    case "advanced":
      return {
        color: "bg-purple-100 text-purple-800 border border-purple-200",
        icon: `<path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />`,
      };
    case "all levels":
    default:
      return {
        color: "bg-gray-100 text-gray-800 border border-gray-200",
        icon: `<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />`,
      };
  }
});

// Badge styles with gradient and icon
const getBadgeStyle = (badge: string) => {
  switch (badge.toLowerCase()) {
    case "bestseller":
      return {
        class: "bg-gradient-to-r from-yellow-500 to-amber-500 text-white",
        icon: `<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />`,
        label: "Bestseller",
      };
    case "hot":
    case "hot & new":
      return {
        class: "bg-gradient-to-r from-red-500 to-rose-500 text-white",
        icon: `<path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />`,
        label: "Hot & New",
      };
    case "new":
      return {
        class: "bg-gradient-to-r from-green-500 to-emerald-500 text-white",
        icon: `<path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />`,
        label: "New",
      };
    case "top rated":
      return {
        class: "bg-gradient-to-r from-purple-500 to-violet-500 text-white",
        icon: `<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />`,
        label: "Top Rated",
      };
    case "popular":
      return {
        class: "bg-gradient-to-r from-blue-500 to-cyan-500 text-white",
        icon: `<path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />`,
        label: "Popular",
      };
    default:
      return {
        class: "bg-gray-700 text-white",
        icon: "",
        label: badge,
      };
  }
};
</script>

<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/no-v-html -->
<template>
  <div
    class="course-card overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100 transition-all duration-500 hover:border-indigo-200 hover:shadow-xl transform hover:-translate-y-2"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <!-- Card image with badge and overlay -->
    <div class="relative overflow-hidden aspect-video">
      <!-- Course thumbnail -->
      <img
        :src="image"
        :alt="title"
        class="w-full h-full object-cover transform transition-transform duration-500"
        :class="{ 'scale-110': isHovered }"
      />

      <!-- Badges -->
      <div
        class="absolute top-0 left-0 right-0 p-3 flex justify-between items-start z-10"
      >
        <div class="flex flex-wrap gap-1.5">
          <div
            v-for="(badge, index) in badges"
            :key="index"
            class="badge relative overflow-hidden inline-flex items-center text-xs font-bold py-1 pl-1.5 pr-2 rounded shadow-md"
            :class="getBadgeStyle(badge).class"
          >
            <!-- Badge background shine animation -->
            <div
              class="absolute inset-0 w-full h-full bg-white opacity-0 hover:opacity-20 transition-opacity duration-500 shine-effect"
            />

            <!-- Badge icon -->
            <svg
              v-if="getBadgeStyle(badge).icon"
              xmlns="http://www.w3.org/2000/svg"
              class="h-3.5 w-3.5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
              v-html="getBadgeStyle(badge).icon"
            ></svg>

            <!-- Badge label -->
            <span>{{ getBadgeStyle(badge).label }}</span>
          </div>
        </div>

        <!-- Category badge in top right with nice gradient -->
        <span
          class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md transform transition-transform duration-300 hover:scale-105"
        >
          {{ category }}
        </span>
      </div>

      <!-- Gradient overlay with instructor info -->
      <div
        class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 transition-opacity duration-500"
        :class="{ 'opacity-100': isHovered }"
      >
        <div class="absolute bottom-0 left-0 p-4 text-white w-full">
          <div class="flex items-center mb-1">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 mr-1 text-indigo-300"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                clip-rule="evenodd"
              />
            </svg>
            <div class="text-sm font-bold">{{ instructor }}</div>
          </div>
          <div class="flex items-center text-xs text-gray-300">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-3.5 w-3.5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd"
              />
            </svg>
            {{ duration }}
          </div>
        </div>
      </div>

      <!-- Progress indicator for enrolled courses -->
      <div
        v-if="_isEnrolled"
        class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-200"
      >
        <div
          class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-300 relative"
          :style="{ width: `${progress}%` }"
        >
          <!-- Animated progress bar shine effect -->
          <div class="absolute inset-0 progress-shine" />
        </div>

        <!-- Progress percentage label -->
        <div
          v-if="progress && progress > 0"
          class="absolute right-2 bottom-2 bg-black/70 text-white text-xs py-0.5 px-1.5 rounded backdrop-blur-sm"
        >
          {{ Math.round(progress) }}%
        </div>
      </div>
    </div>

    <!-- Card content -->
    <div class="p-5">
      <!-- Course level and duration -->
      <div class="flex items-center justify-between mb-3 text-xs font-medium">
        <span
          :class="levelInfo.color"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-3.5 w-3.5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
            v-html="levelInfo.icon"
          />
          {{ level }}
        </span>

        <div class="flex items-center space-x-2">
          <!-- Duration pill -->
          <span class="inline-flex items-center text-gray-500">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-3.5 w-3.5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                clip-rule="evenodd"
              />
            </svg>
            {{ duration }}
          </span>
        </div>
      </div>

      <!-- Course title -->
      <NuxtLink :to="`/courses/${id}`">
        <h3
          class="text-lg font-bold mb-3 leading-tight line-clamp-2 transition-colors duration-300 hover:text-indigo-600 text-black"
        >
          {{ title }}
        </h3>
      </NuxtLink>

      <!-- Ratings -->
      <div class="flex items-center mb-4">
        <div class="flex text-yellow-400 mr-2">
          <!-- Whole stars -->
          <svg
            v-for="i in starRating.wholeStars"
            :key="`whole-${i}`"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            />
          </svg>

          <!-- Partial star -->
          <svg
            v-if="starRating.hasPartialStar"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            viewBox="0 0 20 20"
          >
            <defs>
              <linearGradient id="partialFill">
                <stop offset="50%" stop-color="currentColor" />
                <stop offset="50%" stop-color="#D1D5DB" />
              </linearGradient>
            </defs>
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
              fill="url(#partialFill)"
            />
          </svg>

          <!-- Empty stars -->
          <svg
            v-for="i in starRating.emptyStars"
            :key="`empty-${i}`"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 text-gray-300"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
            />
          </svg>
        </div>
        <span class="text-gray-600 text-xs flex items-center">
          <span class="font-semibold text-gray-700">{{
            rating.toFixed(1)
          }}</span>
          <span class="mx-1">•</span>
          <span>{{ formattedReviewCount }} reviews</span>
        </span>
      </div>

      <!-- Price and CTA -->
      <div class="border-t pt-4 flex justify-between items-center">
        <div class="flex items-center">
          <!-- Discount tag when applicable -->
          <div v-if="discountPercentage" class="relative group mr-3">
            <div class="discount-tag">
              <span class="text-sm font-bold text-white"
                >{{ discountPercentage }}%</span
              >
            </div>
          </div>

          <div class="flex flex-col">
            <!-- Original price (strikethrough) -->
            <span
              v-if="formattedOriginalPrice"
              class="text-sm line-through text-gray-400"
            >
              {{ formattedOriginalPrice }}
            </span>

            <!-- Current price -->
            <span class="text-xl font-bold text-indigo-600">
              {{ formattedPrice }}
            </span>
          </div>
        </div>

        <!-- Action button -->
        <NuxtLink
          :to="`/courses/${id}`"
          class="relative inline-flex items-center overflow-hidden px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 transform group"
          :class="
            isEnrolled
              ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white'
              : 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white'
          "
        >
          <!-- Button shine effect -->
          <span
            class="absolute inset-0 w-full h-full bg-white/30 translate-x-full transition-transform duration-700 ease-in-out group-hover:translate-x-0 group-hover:opacity-30 transform skew-x-[40deg]"
          />

          <!-- Button icon and text -->
          <svg
            v-if="isEnrolled"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
              clip-rule="evenodd"
            />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"
            />
          </svg>

          <span class="relative">{{
            isEnrolled ? "Continue" : "View Course"
          }}</span>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.aspect-video {
  aspect-ratio: 16 / 9;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes shine {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

.shine-effect {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.3) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: skewX(-25deg);
  animation: shine 3s infinite;
}

/* Progress bar shine animation */
@keyframes progressShine {
  0% {
    transform: translateX(-100%) skewX(-45deg);
  }
  100% {
    transform: translateX(200%) skewX(-45deg);
  }
}

.progress-shine {
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.4) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: skewX(-20deg);
  animation: progressShine 2s infinite;
}

/* Discount tag styling */
.discount-tag {
  position: relative;
  background: linear-gradient(135deg, #f43f5e, #e11d48);
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.discount-tag::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.3) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  transform: translateX(-100%) skewX(-15deg);
  animation: shine 3s infinite;
}

/* Hover and focus effects */
.course-card:hover {
  z-index: 10;
}
</style>
