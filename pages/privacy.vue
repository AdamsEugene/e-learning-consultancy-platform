<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Privacy Policy - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Learn about how LearnConsult collects, uses, and protects your personal information.",
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
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Privacy Policy</h1>

        <div class="prose max-w-none">
          <p class="text-gray-600 mb-6">Last updated: {{ currentDate }}</p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            1. Introduction
          </h2>
          <p class="text-gray-600 mb-4">
            LearnConsult ("we", "our", or "us") is committed to protecting and
            respecting your privacy. This Privacy Policy explains how we
            collect, use, disclose, and safeguard your information when you
            visit our website or use our services.
          </p>
          <p class="text-gray-600 mb-4">
            Please read this Privacy Policy carefully. By accessing or using our
            website or services, you acknowledge that you have read, understood,
            and agree to be bound by all the terms of this Privacy Policy.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            2. Information We Collect
          </h2>
          <p class="text-gray-600 mb-4">
            We collect several types of information from and about users of our
            website and services, including:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              <strong>Personal Information:</strong> This may include your name,
              email address, phone number, billing address, payment information,
              and any other information you provide to us when you register,
              create an account, or communicate with us.
            </li>
            <li class="mb-2">
              <strong>Usage Information:</strong> We collect information about
              how you use our website and services, including the pages you
              visit, the time spent on those pages, the links you click, and
              other actions you take.
            </li>
            <li class="mb-2">
              <strong>Device Information:</strong> We may collect information
              about the device you use to access our website, including the
              hardware model, operating system, unique device identifiers, and
              mobile network information.
            </li>
            <li class="mb-2">
              <strong>Location Information:</strong> We may collect information
              about your location if you provide it or if your device settings
              allow us to access it.
            </li>
            <li class="mb-2">
              <strong>Cookies and Similar Technologies:</strong> We use cookies
              and similar tracking technologies to track activity on our website
              and hold certain information. You can instruct your browser to
              refuse all cookies or to indicate when a cookie is being sent.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            3. How We Use Your Information
          </h2>
          <p class="text-gray-600 mb-4">
            We use the information we collect for various purposes, including
            to:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              Provide, maintain, and improve our website and services
            </li>
            <li class="mb-2">
              Process your transactions and send you related information
            </li>
            <li class="mb-2">
              Send you technical notices, updates, security alerts, and support
              messages
            </li>
            <li class="mb-2">
              Respond to your comments, questions, and requests
            </li>
            <li class="mb-2">
              Communicate with you about products, services, offers, and events
            </li>
            <li class="mb-2">
              Monitor and analyze trends, usage, and activities in connection
              with our website and services
            </li>
            <li class="mb-2">
              Detect, investigate, and prevent fraudulent transactions and other
              illegal activities
            </li>
            <li class="mb-2">Personalize your experience on our website</li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            4. Sharing Your Information
          </h2>
          <p class="text-gray-600 mb-4">
            We may share your information in the following circumstances:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              <strong>With Service Providers:</strong> We may share your
              information with third-party vendors, service providers,
              contractors, or agents who perform services on our behalf.
            </li>
            <li class="mb-2">
              <strong>For Business Transfers:</strong> We may share or transfer
              your information in connection with, or during negotiations of,
              any merger, sale of company assets, financing, or acquisition of
              all or a portion of our business to another company.
            </li>
            <li class="mb-2">
              <strong>With Your Consent:</strong> We may share your information
              with third parties when you give us consent to do so.
            </li>
            <li class="mb-2">
              <strong>For Legal Purposes:</strong> We may disclose your
              information if required to do so by law or in response to valid
              requests by public authorities.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            5. Data Security
          </h2>
          <p class="text-gray-600 mb-4">
            We have implemented measures designed to secure your personal
            information from accidental loss and from unauthorized access, use,
            alteration, and disclosure. However, the transmission of information
            via the internet is not completely secure. Although we do our best
            to protect your personal information, we cannot guarantee the
            security of your personal information transmitted to our website.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            6. Your Choices
          </h2>
          <p class="text-gray-600 mb-4">
            You have certain choices regarding the information we collect and
            how it is used:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              <strong>Account Information:</strong> You may update, correct, or
              delete your account information at any time by logging into your
              account settings or contacting us.
            </li>
            <li class="mb-2">
              <strong>Cookies:</strong> Most web browsers are set to accept
              cookies by default. You can usually choose to set your browser to
              remove or reject cookies.
            </li>
            <li class="mb-2">
              <strong>Promotional Communications:</strong> You may opt out of
              receiving promotional emails from us by following the instructions
              in those emails. If you opt out, we may still send you
              non-promotional emails, such as those about your account or our
              ongoing business relations.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            7. Children's Privacy
          </h2>
          <p class="text-gray-600 mb-4">
            Our website and services are not intended for children under 13
            years of age. We do not knowingly collect personal information from
            children under 13. If you are a parent or guardian and believe that
            your child has provided us with personal information, please contact
            us.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            8. International Data Transfers
          </h2>
          <p class="text-gray-600 mb-4">
            Your information may be transferred to, and maintained on, computers
            located outside of your state, province, country, or other
            governmental jurisdiction where the data protection laws may differ
            from those in your jurisdiction. If you are located outside the
            United States and choose to provide information to us, please note
            that we transfer the information to the United States and process it
            there.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            9. Changes to This Privacy Policy
          </h2>
          <p class="text-gray-600 mb-4">
            We may update our Privacy Policy from time to time. We will notify
            you of any changes by posting the new Privacy Policy on this page
            and updating the "Last updated" date at the top of this Privacy
            Policy.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            10. Contact Us
          </h2>
          <p class="text-gray-600 mb-4">
            If you have any questions about this Privacy Policy, please contact
            us at:
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
