<!-- components/consultancy/ConsultancyTeamSection.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";

interface TeamMember {
  id: number;
  name: string;
  role: string;
  bio: string;
  image: string;
  socials?: {
    linkedin?: string;
    twitter?: string;
    email?: string;
  };
  expertise?: string[];
}

interface Props {
  title?: string;
  subtitle?: string;
  description?: string;
  team?: TeamMember[];
}

// Props
const props = withDefaults(defineProps<Props>(), {
  title: "Meet Our Expert Consultants",
  subtitle: "The Team Behind Our Success",
  description:
    "Our team of experienced consultants brings diverse expertise and proven track records across various industries. We're dedicated to delivering exceptional results for your business.",
  team: () => [
    {
      id: 1,
      name: "Sarah Johnson",
      role: "Senior Strategy Consultant",
      bio: "With over 15 years of experience in business strategy and digital transformation, Sarah has helped numerous Fortune 500 companies achieve sustainable growth and competitive advantage.",
      image: "/api/placeholder/300/300",
      socials: {
        linkedin: "#",
        twitter: "#",
        email: "sarah@example.com",
      },
      expertise: [
        "Digital Transformation",
        "Strategic Planning",
        "Change Management",
      ],
    },
    {
      id: 2,
      name: "Michael Chen",
      role: "Technical Solutions Architect",
      bio: "Michael combines deep technical knowledge with business acumen to architect scalable solutions that drive efficiency and innovation. He specializes in cloud architecture and enterprise systems.",
      image: "/api/placeholder/300/300",
      socials: {
        linkedin: "#",
        twitter: "#",
        email: "michael@example.com",
      },
      expertise: [
        "Cloud Architecture",
        "Systems Integration",
        "Technical Strategy",
      ],
    },
    {
      id: 3,
      name: "Amara Okafor",
      role: "Data Analytics Consultant",
      bio: "Amara is passionate about turning data into actionable insights. With expertise in advanced analytics and machine learning, she helps businesses make data-driven decisions that deliver measurable results.",
      image: "/api/placeholder/300/300",
      socials: {
        linkedin: "#",
        email: "amara@example.com",
      },
      expertise: [
        "Data Analytics",
        "Machine Learning",
        "Business Intelligence",
      ],
    },
    {
      id: 4,
      name: "David Rodriguez",
      role: "UX/UI Design Consultant",
      bio: "David creates exceptional user experiences that balance business goals with user needs. His human-centered design approach has helped numerous clients improve conversion rates and customer satisfaction.",
      image: "/api/placeholder/300/300",
      socials: {
        linkedin: "#",
        twitter: "#",
        email: "david@example.com",
      },
      expertise: ["User Experience", "Interface Design", "Design Thinking"],
    },
  ],
});

// Animation and interaction state
const visibleMembers = ref<number[]>([]);
const activeMemberId = ref<number | null>(null);

// Check if member is visible (for animation)
const isMemberVisible = (memberId: number) => {
  return visibleMembers.value.includes(memberId);
};

// Toggle member details
const toggleMemberDetails = (memberId: number) => {
  if (activeMemberId.value === memberId) {
    activeMemberId.value = null;
  } else {
    activeMemberId.value = memberId;
  }
};

// Observe elements for animation
onMounted(() => {
  if (typeof window !== "undefined" && "IntersectionObserver" in window) {
    const options = {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const memberId = parseInt(
            entry.target.getAttribute("data-member-id") || "0"
          );
          if (memberId && !visibleMembers.value.includes(memberId)) {
            visibleMembers.value.push(memberId);
          }
        }
      });
    }, options);

    // Observe each team member card
    document.querySelectorAll(".team-member-card").forEach((member) => {
      observer.observe(member);
    });
  } else {
    // Fallback for browsers without IntersectionObserver
    props.team.forEach((member) => {
      visibleMembers.value.push(member.id);
    });
  }
});
</script>

<!-- eslint-disable vue/html-self-closing -->
<template>
  <section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
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

      <!-- Team members grid -->
      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="(member, index) in team"
          :key="member.id"
          class="team-member-card"
          :data-member-id="member.id"
        >
          <!-- Team member card -->
          <div
            class="bg-white rounded-xl overflow-hidden border border-gray-200 shadow-lg transition-all duration-500 h-full transform"
            :class="[
              isMemberVisible(member.id)
                ? 'opacity-100 translate-y-0'
                : 'opacity-0 translate-y-10',
              activeMemberId === member.id
                ? 'scale-105 shadow-xl border-indigo-200'
                : 'hover:shadow-xl hover:-translate-y-2',
            ]"
            :style="`transition-delay: ${index * 100}ms`"
          >
            <!-- Member image container -->
            <div class="relative overflow-hidden h-64">
              <!-- Member image -->
              <img
                :src="member.image"
                :alt="member.name"
                class="w-full h-full object-cover transition-transform duration-700"
                :class="
                  activeMemberId === member.id ? 'scale-110' : 'scale-100'
                "
              />

              <!-- Gradient overlay -->
              <div
                class="absolute inset-0 bg-gradient-to-t from-indigo-900 to-transparent opacity-60"
              />

              <!-- Member info overlay -->
              <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                <h3 class="font-bold text-xl">{{ member.name }}</h3>
                <p class="text-indigo-200">{{ member.role }}</p>
              </div>
            </div>

            <!-- Member details container -->
            <div class="p-5">
              <!-- Expertise tags -->
              <div class="mb-4 flex flex-wrap gap-2">
                <span
                  v-for="skill in member.expertise"
                  :key="skill"
                  class="inline-block text-xs font-medium rounded-full px-2 py-1 bg-indigo-100 text-indigo-800"
                >
                  {{ skill }}
                </span>
              </div>

              <!-- Member bio -->
              <p
                class="text-gray-600 mb-4 transition-all duration-500"
                :class="
                  activeMemberId === member.id
                    ? 'line-clamp-none'
                    : 'line-clamp-3'
                "
              >
                {{ member.bio }}
              </p>

              <!-- Show more / less button -->
              <button
                class="text-indigo-600 font-medium flex items-center hover:text-indigo-800 transition-colors text-sm"
                @click="toggleMemberDetails(member.id)"
              >
                <span>{{
                  activeMemberId === member.id ? "Show less" : "Show more"
                }}</span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 ml-1 transition-transform"
                  :class="
                    activeMemberId === member.id ? 'transform rotate-180' : ''
                  "
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  />
                </svg>
              </button>
            </div>

            <!-- Social media links -->
            <div class="px-5 pb-5 flex space-x-3 mt-auto">
              <a
                v-if="member.socials?.linkedin"
                :href="member.socials.linkedin"
                class="text-gray-400 hover:text-indigo-600 transition-colors"
                target="_blank"
                rel="noopener noreferrer"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"
                  />
                </svg>
              </a>
              <a
                v-if="member.socials?.twitter"
                :href="member.socials.twitter"
                class="text-gray-400 hover:text-indigo-600 transition-colors"
                target="_blank"
                rel="noopener noreferrer"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                  />
                </svg>
              </a>
              <a
                v-if="member.socials?.email"
                :href="`mailto:${member.socials.email}`"
                class="text-gray-400 hover:text-indigo-600 transition-colors"
              >
                <svg
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
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Team CTA -->
      <div class="mt-16 text-center">
        <p class="text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
          Want to work with our team of consultants on your next project?
        </p>
        <NuxtLink
          to="/consultancy/contact"
          class="inline-flex items-center bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition-all transform hover:scale-105 shadow-lg"
        >
          Contact Our Team
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 ml-2"
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
  </section>
</template>

<style scoped>
/* Animation for the team cards */
.team-member-card {
  backface-visibility: hidden;
}

/* Line clamp for bio text */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-none {
  -webkit-line-clamp: unset;
  line-clamp: unset;
}

/* Expertise tags hover effect */
.team-member-card:hover .expertise-tag {
  transform: translateY(-2px);
}

/* Social icons hover animations */
.social-icon {
  transition: all 0.3s ease;
}

.social-icon:hover {
  transform: translateY(-3px);
}
</style>
