<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Contact Us - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Get in touch with LearnConsult. Contact our team for support, inquiries, or to learn more about our e-learning and consultancy services.",
    },
  ],
});

// Form state
const form = ref({
  name: "",
  email: "",
  subject: "",
  message: "",
});

const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref(false);

// Handle form submission
const handleSubmit = async (e: Event) => {
  e.preventDefault();
  isSubmitting.value = true;
  submitError.value = false;

  try {
    // TODO: Implement actual form submission
    await new Promise((resolve) => setTimeout(resolve, 1000));
    submitSuccess.value = true;
    form.value = {
      name: "",
      email: "",
      subject: "",
      message: "",
    };
  } catch (error) {
    submitError.value = true;
  } finally {
    isSubmitting.value = false;
  }
};

// Office locations
const offices = [
  {
    city: "London",
    address: "123 Tech Street, London EC1A 1BB",
    phone: "+44 20 1234 5678",
    email: "london@learnconsult.com",
  },
  {
    city: "New York",
    address: "456 Innovation Ave, New York, NY 10001",
    phone: "+1 212 123 4567",
    email: "newyork@learnconsult.com",
  },
  {
    city: "Singapore",
    address: "789 Digital Road, Singapore 018956",
    phone: "+65 6789 0123",
    email: "singapore@learnconsult.com",
  },
];

// Contact methods
const contactMethods = [
  {
    title: "Email",
    description: "Our team typically responds within 24 hours",
    value: "support@learnconsult.com",
    icon: "M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z",
  },
  {
    title: "Phone",
    description: "Available Monday to Friday, 9am to 5pm GMT",
    value: "+44 20 1234 5678",
    icon: "M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z",
  },
  {
    title: "Live Chat",
    description: "Chat with our support team in real-time",
    value: "Start Chat",
    icon: "M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z",
  },
];
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Hero Section -->
      <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Contact Us</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Have questions? We're here to help. Choose the best way to reach us
          below.
        </p>
      </div>

      <!-- Contact Methods -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div
          v-for="(method, index) in contactMethods"
          :key="index"
          class="bg-white rounded-lg shadow-md p-6 text-center"
        >
          <div class="flex justify-center mb-4">
            <div class="bg-indigo-100 rounded-full p-3">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-indigo-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  :d="method.icon"
                />
              </svg>
            </div>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            {{ method.title }}
          </h3>
          <p class="text-gray-600 mb-4">{{ method.description }}</p>
          <a
            :href="method.title === 'Email' ? `mailto:${method.value}` : '#'"
            class="text-indigo-600 hover:text-indigo-800 font-medium"
          >
            {{ method.value }}
          </a>
        </div>
      </div>

      <!-- Contact Form and Office Locations -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Send us a Message
          </h2>
          <form @submit="handleSubmit" class="space-y-6">
            <div>
              <label
                for="name"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Name
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
            <div>
              <label
                for="email"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Email
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
            <div>
              <label
                for="subject"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Subject
              </label>
              <input
                id="subject"
                v-model="form.subject"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
            <div>
              <label
                for="message"
                class="block text-sm font-medium text-gray-700 mb-1"
              >
                Message
              </label>
              <textarea
                id="message"
                v-model="form.message"
                rows="4"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
              ></textarea>
            </div>
            <div>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ isSubmitting ? "Sending..." : "Send Message" }}
              </button>
            </div>
            <div v-if="submitSuccess" class="text-green-600 text-center">
              Thank you for your message. We'll get back to you soon!
            </div>
            <div v-if="submitError" class="text-red-600 text-center">
              Sorry, there was an error sending your message. Please try again.
            </div>
          </form>
        </div>

        <!-- Office Locations -->
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Our Offices</h2>
          <div class="space-y-6">
            <div
              v-for="(office, index) in offices"
              :key="index"
              class="bg-white rounded-lg shadow-md p-6"
            >
              <h3 class="text-lg font-medium text-gray-900 mb-2">
                {{ office.city }}
              </h3>
              <p class="text-gray-600 mb-2">{{ office.address }}</p>
              <div class="flex items-center text-gray-600 mb-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                  />
                </svg>
                <a :href="`tel:${office.phone}`" class="hover:text-indigo-600">
                  {{ office.phone }}
                </a>
              </div>
              <div class="flex items-center text-gray-600">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 mr-2"
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
                <a
                  :href="`mailto:${office.email}`"
                  class="hover:text-indigo-600"
                >
                  {{ office.email }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
