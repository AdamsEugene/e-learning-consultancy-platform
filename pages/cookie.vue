<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Cookie Policy - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Learn about how LearnConsult uses cookies and similar technologies to improve your experience on our platform.",
    },
  ],
});

// Format the current date
const currentDate = new Date().toLocaleDateString("en-US", {
  year: "numeric",
  month: "long",
  day: "numeric",
});
</script>

<template>
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-white shadow rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Cookie Policy</h1>

        <div class="prose max-w-none">
          <p class="text-gray-600 mb-6">Last updated: {{ currentDate }}</p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            1. What Are Cookies
          </h2>
          <p class="text-gray-600 mb-4">
            Cookies are small text files that are placed on your computer or
            mobile device when you visit a website. They are widely used to make
            websites work more efficiently and provide a better user experience.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            2. How We Use Cookies
          </h2>
          <p class="text-gray-600 mb-4">
            LearnConsult uses cookies for various purposes, including:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              <strong>Essential Cookies:</strong> These cookies are necessary
              for the website to function properly. They enable basic functions
              like page navigation and access to secure areas of the website.
            </li>
            <li class="mb-2">
              <strong>Performance Cookies:</strong> These cookies help us
              understand how visitors interact with our website by collecting
              and reporting information anonymously.
            </li>
            <li class="mb-2">
              <strong>Functionality Cookies:</strong> These cookies allow the
              website to remember choices you make and provide enhanced, more
              personal features.
            </li>
            <li class="mb-2">
              <strong>Targeting Cookies:</strong> These cookies are used to
              deliver advertisements more relevant to you and your interests.
              They are also used to limit the number of times you see an
              advertisement and help measure the effectiveness of advertising
              campaigns.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            3. Types of Cookies We Use
          </h2>
          <p class="text-gray-600 mb-4">
            We use both session cookies and persistent cookies on our website:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              <strong>Session Cookies:</strong> These are temporary cookies that
              expire when you close your browser. They are used to remember your
              preferences during your visit to our website.
            </li>
            <li class="mb-2">
              <strong>Persistent Cookies:</strong> These cookies remain on your
              device for a set period of time or until you delete them. They are
              used to remember your preferences across multiple visits to our
              website.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            4. Third-Party Cookies
          </h2>
          <p class="text-gray-600 mb-4">
            Some cookies are placed by third-party services that appear on our
            pages. We use various third-party services that may set cookies on
            your device, including:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">Analytics services (e.g., Google Analytics)</li>
            <li class="mb-2">Advertising networks</li>
            <li class="mb-2">Social media platforms</li>
            <li class="mb-2">Payment processors</li>
          </ul>
          <p class="text-gray-600 mb-4">
            We do not control the use of these cookies and cannot access them
            due to the way that cookies work, as cookies can only be accessed by
            the party who originally set them. Please check the third-party
            websites for more information about their cookies.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            5. How to Control Cookies
          </h2>
          <p class="text-gray-600 mb-4">
            Most web browsers allow you to control cookies through their
            settings preferences. However, if you limit the ability of websites
            to set cookies, you may worsen your overall user experience, since
            it will no longer be personalized to you.
          </p>
          <p class="text-gray-600 mb-4">
            You can manage your cookie preferences by:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              Adjusting your browser settings to refuse all or some cookies
            </li>
            <li class="mb-2">
              Using our cookie consent tool to manage your preferences
            </li>
            <li class="mb-2">
              Deleting cookies that have already been set on your device
            </li>
          </ul>
          <p class="text-gray-600 mb-4">
            Please note that if you choose to block or delete cookies, some
            features of our website may not function correctly.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            6. Changes to This Cookie Policy
          </h2>
          <p class="text-gray-600 mb-4">
            We may update our Cookie Policy from time to time. We will notify
            you of any changes by posting the new Cookie Policy on this page and
            updating the "Last updated" date at the top of this Cookie Policy.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            7. Contact Us
          </h2>
          <p class="text-gray-600 mb-4">
            If you have any questions about our Cookie Policy, please contact us
            at:
          </p>
          <p class="text-gray-600 mb-4">
            Email:
            <a
              href="mailto:privacy@learnconsult.com"
              class="text-indigo-600 hover:text-indigo-800"
              >privacy@learnconsult.com</a
            ><br />
            Address: 123 Learning Street, Education City, EC 12345
          </p>
        </div>
      </div>
    </main>
  </div>
</template>
