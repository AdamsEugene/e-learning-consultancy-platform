<!-- components/dashboard/RecommendedCoursesCard.vue -->
<script setup lang="ts">
interface Course {
  id: number;
  title: string;
  instructor: string;
  rating: number;
  students: number;
  thumbnail: string;
  price: number;
  level: string;
  tags: string[];
  relevanceScore: number;
}

interface Props {
  courses: Course[];
  animationDelay?: number;
  isLoaded?: boolean;
}

const props = defineProps<Props>();

// Format student count
const formatStudentCount = (count: number): string => {
  if (count >= 1000) {
    return (count / 1000).toFixed(1) + 'k';
  }
  return count.toString();
};

// Format percentage for match score
const formatMatchScore = (score: number): string => {
  return score + '%';
};

// Render stars for rating
const renderStars = (rating: number): string => {
  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5;
  const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
  
  let stars = '';
  
  // Full stars
  for (let i = 0; i < fullStars; i++) {
    stars += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
  }
  
  // Half star
  if (halfStar) {
    stars += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
  }
  
  // Empty stars
  for (let i = 0; i < emptyStars; i++) {
    stars += '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
  }
  
  return stars;
};
</script>

<template>
  <div 
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ 'loaded': isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Recommended For You</h3>
        <a href="/courses/recommended" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center transition-colors">
          View All
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </a>
      </div>
    </div>
    
    <div class="divide-y divide-gray-100">
      <!-- Course item -->
      <div 
        v-for="(course, index) in courses" 
        :key="course.id"
        class="p-4 transition-all duration-200 hover:bg-gray-50 group"
      >
        <div class="flex gap-3">
          <!-- Course thumbnail -->
          <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden">
            <img 
              :src="course.thumbnail" 
              :alt="course.title" 
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
            />
          </div>
          
          <!-- Course info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
              <!-- Title and basic info -->
              <div class="min-w-0">
                <h4 class="text-base font-bold text-gray-900 truncate">{{ course.title }}</h4>
                <p class="text-sm text-gray-600 truncate">{{ course.instructor }}</p>
                
                <!-- Rating -->
                <div class="flex items-center mt-1">
                  <div class="flex" v-html="renderStars(course.rating)"></div>
                  <span class="ml-1 text-sm text-gray-600">{{ course.rating }} ({{ formatStudentCount(course.students) }})</span>
                </div>
              </div>
              
              <!-- Match score -->
              <div class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full flex items-center whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ formatMatchScore(course.relevanceScore) }} match
              </div>
            </div>
            
            <!-- Tags and price -->
            <div class="flex justify-between items-end mt-2">
              <!-- Tags -->
              <div class="flex flex-wrap gap-1">
                <span 
                  v-for="(tag, tagIndex) in course.tags.slice(0, 2)" 
                  :key="tagIndex"
                  class="bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded"
                >
                  {{ tag }}
                </span>
                <span 
                  v-if="course.tags.length > 2" 
                  class="text-gray-500 text-xs"
                >
                  +{{ course.tags.length - 2 }} more
                </span>
              </div>
              
              <!-- Price -->
              <div class="text-right">
                <div class="text-sm font-bold text-indigo-600">${{ course.price.toFixed(2) }}</div>
                <div class="text-xs text-gray-500">{{ course.level }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View more button at bottom -->
    <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
      <a 
        href="/courses/recommended" 
        class="inline-flex items-center justify-center text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors"
      >
        <span>See more personalized recommendations</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </a>
    </div>
  </div>
</template>

<style scoped>
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>