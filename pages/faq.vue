<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Frequently Asked Questions - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Find answers to common questions about LearnConsult's e-learning and consultancy services.",
    },
  ],
});

// FAQ data
const faqs = [
  {
    category: "General",
    questions: [
      {
        question: "What is LearnConsult?",
        answer:
          "LearnConsult is an e-learning and consultancy platform that provides high-quality educational courses and professional consultancy services. We help individuals and organizations enhance their skills, knowledge, and capabilities through expert-led courses and personalized consultancy.",
      },
      {
        question: "How do I create an account?",
        answer:
          "To create an account, click on the 'Sign Up' button in the top right corner of our website. You'll need to provide your name, email address, and create a password. Once registered, you can access our courses and consultancy services.",
      },
      {
        question: "What types of courses do you offer?",
        answer:
          "We offer a wide range of courses across various disciplines including technology, business, design, marketing, and more. Our courses are designed for different skill levels, from beginners to advanced professionals.",
      },
    ],
  },
  {
    category: "Courses",
    questions: [
      {
        question: "How do I enroll in a course?",
        answer:
          "Browse our course catalog, select a course you're interested in, and click on the 'Enroll' button. You may need to complete payment if the course is not free. Once enrolled, you'll have immediate access to the course materials.",
      },
      {
        question: "Are your courses free?",
        answer:
          "We offer both free and paid courses. Free courses provide access to basic content, while paid courses offer comprehensive materials, assessments, certificates, and sometimes one-on-one mentoring.",
      },
      {
        question: "Do you offer certificates upon completion?",
        answer:
          "Yes, most of our paid courses offer certificates upon successful completion. These certificates can be downloaded and shared on your professional profiles.",
      },
      {
        question: "Can I access course materials offline?",
        answer:
          "Yes, many of our courses allow you to download materials for offline viewing. Look for the download icon next to course content. Note that some interactive elements may require an internet connection.",
      },
      {
        question: "What happens if I don't complete a course?",
        answer:
          "You can access your enrolled courses indefinitely. If you don't complete a course, you won't receive a certificate, but you can return to it at any time to continue learning.",
      },
    ],
  },
  {
    category: "Consultancy Services",
    questions: [
      {
        question: "What consultancy services do you offer?",
        answer:
          "We offer a range of consultancy services including project management, technology implementation, business strategy, digital transformation, and more. Our services are tailored to meet the specific needs of organizations and individuals.",
      },
      {
        question: "How do I request consultancy services?",
        answer:
          "Visit our Consultancy page and fill out the project request form. Provide details about your project, requirements, and timeline. Our team will review your request and get back to you within 2 business days.",
      },
      {
        question: "How are consultancy fees structured?",
        answer:
          "Consultancy fees vary based on the scope, complexity, and duration of the project. We offer different pricing models including fixed-price, time-based, and retainer arrangements. You'll receive a detailed quote after your initial consultation.",
      },
      {
        question: "Do you offer ongoing support after project completion?",
        answer:
          "Yes, we offer maintenance and support packages after project completion. These can be tailored to your specific needs and can include regular check-ins, updates, and technical support.",
      },
    ],
  },
  {
    category: "Payment & Refunds",
    questions: [
      {
        question: "What payment methods do you accept?",
        answer:
          "We accept major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers. For enterprise clients, we also offer invoicing options.",
      },
      {
        question: "Do you offer refunds?",
        answer:
          "For courses, we offer a 30-day money-back guarantee if you're not satisfied with your purchase. For consultancy services, refund policies are outlined in your specific service agreement.",
      },
      {
        question: "Are there any hidden fees?",
        answer:
          "No, all fees are clearly displayed before you make a purchase. For courses, the price shown includes all course materials. For consultancy services, any additional costs will be discussed and agreed upon before the project begins.",
      },
    ],
  },
  {
    category: "Technical Support",
    questions: [
      {
        question: "What should I do if I encounter technical issues?",
        answer:
          "If you encounter technical issues, first try refreshing your browser or clearing your cache. If the problem persists, contact our support team through the 'Help' section or email support@learnconsult.com.",
      },
      {
        question: "What are the system requirements for your platform?",
        answer:
          "Our platform works on most modern web browsers including Chrome, Firefox, Safari, and Edge. We recommend using the latest version of these browsers for the best experience. For mobile access, our platform is responsive and works on most smartphones and tablets.",
      },
      {
        question: "Do you offer mobile apps?",
        answer:
          "Yes, we offer mobile apps for iOS and Android devices. You can download them from the App Store or Google Play Store. The apps allow you to access your courses and materials on the go.",
      },
    ],
  },
];
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-white shadow rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">
          Frequently Asked Questions
        </h1>

        <div class="prose max-w-none">
          <!-- FAQ Categories -->
          <div v-for="(category, index) in faqs" :key="index" class="mb-12">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">
              {{ category.category }}
            </h2>
            <div class="space-y-6">
              <div
                v-for="(item, qIndex) in category.questions"
                :key="qIndex"
                class="border-b border-gray-200 pb-6"
              >
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                  {{ item.question }}
                </h3>
                <p class="text-gray-600">{{ item.answer }}</p>
              </div>
            </div>
          </div>

          <!-- Contact Section -->
          <div class="mt-12 pt-8 border-t border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">
              Still Have Questions?
            </h2>
            <p class="text-gray-600 mb-6">
              If you couldn't find the answer you're looking for, please contact
              our support team. We're here to help!
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <NuxtLink
                to="/contact"
                class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
              >
                Contact Support
              </NuxtLink>
              <a
                href="mailto:support@learnconsult.com"
                class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Email Us
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
