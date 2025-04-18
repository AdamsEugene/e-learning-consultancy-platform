<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "About Us - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Learn about LearnConsult's mission, team, and values. Discover how we're transforming education and consultancy services.",
    },
  ],
});

// Team members
const teamMembers = [
  {
    name: "Dr. Sarah Johnson",
    role: "CEO & Founder",
    bio: "With over 15 years of experience in education technology, Sarah founded LearnConsult to revolutionize online learning and professional development.",
    image: "/images/team/sarah.jpg",
    linkedin: "https://linkedin.com/in/sarahjohnson",
  },
  {
    name: "Michael Chen",
    role: "Head of E-Learning",
    bio: "Michael leads our e-learning initiatives, bringing expertise in instructional design and educational technology to create engaging learning experiences.",
    image: "/images/team/michael.jpg",
    linkedin: "https://linkedin.com/in/michaelchen",
  },
  {
    name: "Dr. Emily Rodriguez",
    role: "Head of Research & Innovation",
    bio: "Emily drives our research initiatives, exploring cutting-edge technologies like AI and adaptive learning to enhance educational outcomes.",
    image: "/images/team/emily.jpg",
    linkedin: "https://linkedin.com/in/emilyrodriguez",
  },
  {
    name: "James Wilson",
    role: "Head of Consultancy",
    bio: "James oversees our consultancy services, helping organizations implement effective learning strategies and transform their training programs.",
    image: "/images/team/james.jpg",
    linkedin: "https://linkedin.com/in/jameswilson",
  },
  {
    name: "Lisa Thompson",
    role: "Learning Experience Designer",
    bio: "Lisa specializes in creating immersive and interactive learning experiences that engage learners and promote knowledge retention.",
    image: "/images/team/lisa.jpg",
    linkedin: "https://linkedin.com/in/lisathompson",
  },
  {
    name: "David Kim",
    role: "Technical Director",
    bio: "David ensures our platform remains cutting-edge, implementing the latest technologies to provide a seamless learning experience.",
    image: "/images/team/david.jpg",
    linkedin: "https://linkedin.com/in/davidkim",
  },
];

// Values
const values = [
  {
    title: "Excellence",
    description:
      "We strive for excellence in everything we do, from course content to customer service.",
    icon: "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z",
  },
  {
    title: "Innovation",
    description:
      "We continuously innovate to provide cutting-edge learning solutions and stay ahead of industry trends.",
    icon: "M13 10V3L4 14h7v7l9-11h-7z",
  },
  {
    title: "Accessibility",
    description:
      "We believe education should be accessible to everyone, regardless of their background or circumstances.",
    icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
  },
  {
    title: "Collaboration",
    description:
      "We foster collaboration between learners, educators, and organizations to create a supportive learning community.",
    icon: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
  },
];

// Stats
const stats = [
  { label: "Active Learners", value: "10,000+" },
  { label: "Courses Available", value: "500+" },
  { label: "Expert Instructors", value: "100+" },
  { label: "Client Organizations", value: "250+" },
];
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main>
      <!-- Hero Section -->
      <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
          <div class="text-center">
            <h1
              class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl"
            >
              About LearnConsult
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-500">
              We're on a mission to transform education and professional
              development through innovative e-learning solutions and expert
              consultancy services.
            </p>
          </div>
        </div>
      </section>

      <!-- Mission Section -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
            <div>
              <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Our Mission
              </h2>
              <p class="mt-3 max-w-3xl text-lg text-gray-500">
                At LearnConsult, we believe that education should be accessible,
                engaging, and effective. Our mission is to empower individuals
                and organizations with the knowledge and skills they need to
                succeed in today's rapidly evolving world.
              </p>
              <div class="mt-8 space-y-4">
                <p class="text-gray-500">
                  We combine cutting-edge technology with proven educational
                  methodologies to create learning experiences that are not only
                  informative but also inspiring and transformative.
                </p>
                <p class="text-gray-500">
                  Through our consultancy services, we help organizations
                  develop and implement effective learning strategies that drive
                  growth, innovation, and success.
                </p>
              </div>
            </div>
            <div class="mt-10 lg:mt-0">
              <img
                class="rounded-lg shadow-xl"
                src="/images/courses/c2.jpg"
                alt="LearnConsult Mission"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- Values Section -->
      <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
              Our Values
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
              These core values guide everything we do at LearnConsult.
            </p>
          </div>

          <div
            class="mt-12 grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-4"
          >
            <div
              v-for="(value, index) in values"
              :key="index"
              class="bg-gray-50 rounded-lg p-6 text-center"
            >
              <div
                class="w-12 h-12 mx-auto bg-indigo-100 rounded-full flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-indigo-600"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    :d="value.icon"
                  />
                </svg>
              </div>
              <h3 class="mt-4 text-lg font-medium text-gray-900">
                {{ value.title }}
              </h3>
              <p class="mt-2 text-base text-gray-500">
                {{ value.description }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Section -->
      <section class="py-16 bg-indigo-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
            <div
              v-for="(stat, index) in stats"
              :key="index"
              class="text-center"
            >
              <div class="text-4xl font-extrabold text-white">
                {{ stat.value }}
              </div>
              <div class="mt-2 text-lg font-medium text-indigo-100">
                {{ stat.label }}
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Team Section -->
      <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
              Meet Our Team
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
              Our diverse team of experts brings together decades of experience
              in education, technology, and consultancy.
            </p>
          </div>

          <div
            class="mt-12 grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3"
          >
            <div
              v-for="(member, index) in teamMembers"
              :key="index"
              class="bg-gray-50 rounded-lg overflow-hidden shadow-lg"
            >
              <div class="relative pb-48">
                <img
                  class="absolute h-full w-full object-cover"
                  :src="member.image"
                  :alt="member.name"
                />
              </div>
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ member.name }}
                </h3>
                <p class="text-sm text-indigo-600">{{ member.role }}</p>
                <p class="mt-3 text-base text-gray-500">{{ member.bio }}</p>
                <div class="mt-4">
                  <a
                    :href="member.linkedin"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-indigo-600 hover:text-indigo-800"
                  >
                    <span class="sr-only">LinkedIn</span>
                    <svg
                      class="h-5 w-5"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      aria-hidden="true"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-indigo-700 rounded-lg shadow-xl overflow-hidden">
            <div class="px-6 py-12 sm:px-12 lg:py-16">
              <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                <div>
                  <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Ready to transform your learning experience?
                  </h2>
                  <p class="mt-4 text-lg text-indigo-100">
                    Join thousands of learners and organizations who have
                    already benefited from our courses and consultancy services.
                  </p>
                </div>
                <div class="mt-8 lg:mt-0 lg:ml-8">
                  <div class="space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                    <NuxtLink
                      to="/courses"
                      class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50"
                    >
                      Browse Courses
                    </NuxtLink>
                    <NuxtLink
                      to="/consultancy"
                      class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-600"
                    >
                      Consultancy Services
                    </NuxtLink>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>
