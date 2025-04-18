<script setup lang="ts">
// Set page metadata
definePageMeta({
  layout: "public",
});

useHead({
  title: "Terms of Service - LearnConsult",
  meta: [
    {
      name: "description",
      content:
        "Read the Terms of Service for using LearnConsult's e-learning and consultancy platform.",
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
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Terms of Service</h1>

        <div class="prose max-w-none">
          <p class="text-gray-600 mb-6">Last updated: {{ currentDate }}</p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            1. Introduction
          </h2>
          <p class="text-gray-600 mb-4">
            Welcome to LearnConsult. These Terms of Service ("Terms") govern
            your access to and use of the LearnConsult website, mobile
            applications, and services (collectively, the "Services"). By
            accessing or using the Services, you agree to be bound by these
            Terms.
          </p>
          <p class="text-gray-600 mb-4">
            If you are using the Services on behalf of an organization, you are
            agreeing to these Terms for that organization and promising that you
            have the authority to bind that organization to these Terms. In that
            case, "you" and "your" will refer to that organization.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            2. Definitions
          </h2>
          <p class="text-gray-600 mb-4">For the purposes of these Terms:</p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              <strong>"Content"</strong> means text, graphics, images, music,
              software, audio, video, information, or other materials.
            </li>
            <li class="mb-2">
              <strong>"User Content"</strong> means Content that you or other
              users submit, post, or display on or through the Services.
            </li>
            <li class="mb-2">
              <strong>"LearnConsult Content"</strong> means Content that we
              create and make available on or through the Services.
            </li>
            <li class="mb-2">
              <strong>"Third-Party Content"</strong> means Content that
              originates from parties other than LearnConsult or its users.
            </li>
            <li class="mb-2">
              <strong>"Services"</strong> means the LearnConsult website, mobile
              applications, and any other services offered by LearnConsult.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            3. Account Registration and Security
          </h2>
          <p class="text-gray-600 mb-4">
            To access certain features of the Services, you may be required to
            register for an account. You agree to provide accurate, current, and
            complete information during the registration process and to update
            such information to keep it accurate, current, and complete.
          </p>
          <p class="text-gray-600 mb-4">
            You are responsible for safeguarding your password and for all
            activities that occur under your account. You agree to notify us
            immediately of any unauthorized use of your account or any other
            breach of security.
          </p>
          <p class="text-gray-600 mb-4">
            We reserve the right to disable any user account at any time if, in
            our opinion, you have failed to comply with any provision of these
            Terms.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            4. User Conduct
          </h2>
          <p class="text-gray-600 mb-4">You agree not to:</p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              Use the Services in any way that violates any applicable law or
              regulation.
            </li>
            <li class="mb-2">
              Use the Services to transmit, or procure the sending of, any
              advertising or promotional material, including any "junk mail,"
              "chain letter," "spam," or any other similar solicitation.
            </li>
            <li class="mb-2">
              Impersonate or attempt to impersonate LearnConsult, a LearnConsult
              employee, another user, or any other person or entity.
            </li>
            <li class="mb-2">
              Engage in any other conduct that restricts or inhibits anyone's
              use or enjoyment of the Services, or which, as determined by us,
              may harm LearnConsult or users of the Services or expose them to
              liability.
            </li>
            <li class="mb-2">
              Use the Services in any manner that could disable, overburden,
              damage, or impair the Services.
            </li>
            <li class="mb-2">
              Use any robot, spider, or other automatic device, process, or
              means to access the Services for any purpose, including monitoring
              or copying any of the material on the Services.
            </li>
            <li class="mb-2">
              Use any manual process to monitor or copy any of the material on
              the Services or for any other unauthorized purpose without our
              prior written consent.
            </li>
            <li class="mb-2">
              Use any device, software, or routine that interferes with the
              proper working of the Services.
            </li>
            <li class="mb-2">
              Introduce any viruses, Trojan horses, worms, logic bombs, or other
              material that is malicious or technologically harmful.
            </li>
            <li class="mb-2">
              Attempt to gain unauthorized access to, interfere with, damage, or
              disrupt any parts of the Services, the server on which the
              Services are stored, or any server, computer, or database
              connected to the Services.
            </li>
            <li class="mb-2">
              Attack the Services via a denial-of-service attack or a
              distributed denial-of-service attack.
            </li>
            <li class="mb-2">
              Otherwise attempt to interfere with the proper working of the
              Services.
            </li>
          </ul>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            5. User Content
          </h2>
          <p class="text-gray-600 mb-4">
            The Services may allow you to create, post, store, and share
            content, including messages, text, photos, videos, software, code,
            and other materials (collectively, "User Content").
          </p>
          <p class="text-gray-600 mb-4">
            You retain all rights in and to your User Content. By creating,
            posting, or sharing User Content on or through the Services, you
            grant LearnConsult a non-exclusive, transferable, sub-licensable,
            royalty-free, worldwide license to use, modify, publicly perform,
            publicly display, reproduce, and distribute such User Content on and
            through the Services.
          </p>
          <p class="text-gray-600 mb-4">You represent and warrant that:</p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              You own or have the necessary rights to use and authorize
              LearnConsult to use your User Content.
            </li>
            <li class="mb-2">
              Your User Content does not violate the privacy rights, publicity
              rights, copyrights, contract rights, or any other rights of any
              person or entity.
            </li>
            <li class="mb-2">
              Your User Content does not contain any libelous or otherwise
              unlawful, abusive, or obscene material.
            </li>
            <li class="mb-2">
              Your User Content does not contain any viruses, Trojan horses,
              worms, logic bombs, or other material that is malicious or
              technologically harmful.
            </li>
          </ul>
          <p class="text-gray-600 mb-4">
            We do not endorse or guarantee the accuracy, quality, or
            appropriateness of any User Content. We are not responsible for any
            User Content posted by users on the Services.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            6. Intellectual Property
          </h2>
          <p class="text-gray-600 mb-4">
            The Services and their entire contents, features, and functionality,
            including but not limited to all information, software, text,
            displays, images, video, and audio, and the design, selection, and
            arrangement thereof, are owned by LearnConsult, its licensors, or
            other providers of such material and are protected by copyright,
            trademark, patent, trade secret, and other intellectual property or
            proprietary rights laws.
          </p>
          <p class="text-gray-600 mb-4">
            These Terms do not grant you any right, title, or interest in the
            Services or any Content. You may not reproduce, distribute, modify,
            create derivative works of, publicly display, publicly perform,
            republish, download, store, or transmit any of the material on our
            Services, except as follows:
          </p>
          <ul class="list-disc pl-6 mb-6 text-gray-600">
            <li class="mb-2">
              Your computer may temporarily store copies of such materials in
              RAM incidental to your accessing and viewing those materials.
            </li>
            <li class="mb-2">
              You may store files that are automatically cached by your web
              browser for display enhancement purposes.
            </li>
            <li class="mb-2">
              You may print or download one copy of a reasonable number of pages
              of the website for your own personal, non-commercial use and not
              for further reproduction, publication, or distribution.
            </li>
          </ul>
          <p class="text-gray-600 mb-4">
            If you print, copy, modify, download, or otherwise use or provide
            any other person with access to any part of the Services in breach
            of the Terms, your right to use the Services will stop immediately
            and you must, at our option, return or destroy any copies of the
            materials you have made.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            7. Payment Terms
          </h2>
          <p class="text-gray-600 mb-4">
            Some of our Services require payment. If you choose to purchase a
            paid Service, you agree to pay all fees in accordance with the
            pricing and payment terms presented to you for the Service.
          </p>
          <p class="text-gray-600 mb-4">
            All payments are non-refundable, except as required by law or as
            explicitly stated in these Terms. If you believe that LearnConsult
            has charged you in error, you must contact LearnConsult within 90
            days of such charge. No refunds will be given for any charges more
            than 90 days old.
          </p>
          <p class="text-gray-600 mb-4">
            We may change our prices at any time. If you are on a subscription
            plan, we will notify you of any price changes and give you an
            opportunity to cancel your subscription before the new price takes
            effect.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            8. Disclaimer of Warranties
          </h2>
          <p class="text-gray-600 mb-4">
            THE SERVICES ARE PROVIDED "AS IS" AND "AS AVAILABLE," WITHOUT
            WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED. WITHOUT LIMITING
            THE FOREGOING, WE EXPLICITLY DISCLAIM ANY WARRANTIES OF
            MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT,
            OR NON-INFRINGEMENT, AND ANY WARRANTIES ARISING OUT OF COURSE OF
            DEALING OR USAGE OF TRADE.
          </p>
          <p class="text-gray-600 mb-4">
            We make no warranty that the Services will meet your requirements or
            be available on an uninterrupted, secure, or error-free basis. We
            make no warranty regarding the quality, accuracy, timeliness,
            truthfulness, completeness, or reliability of any Content.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            9. Limitation of Liability
          </h2>
          <p class="text-gray-600 mb-4">
            TO THE MAXIMUM EXTENT PERMITTED BY LAW, IN NO EVENT SHALL
            LEARNCONSULT, ITS AFFILIATES, AGENTS, DIRECTORS, EMPLOYEES,
            SUPPLIERS, OR LICENSORS BE LIABLE FOR ANY INDIRECT, PUNITIVE,
            INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR EXEMPLARY DAMAGES, INCLUDING
            WITHOUT LIMITATION DAMAGES FOR LOSS OF PROFITS, GOODWILL, USE, DATA,
            OR OTHER INTANGIBLE LOSSES, THAT RESULT FROM THE USE OF, OR
            INABILITY TO USE, THE SERVICES.
          </p>
          <p class="text-gray-600 mb-4">
            UNDER NO CIRCUMSTANCES WILL LEARNCONSULT'S AGGREGATE LIABILITY TO
            YOU FOR ALL CLAIMS RELATING TO THE SERVICES EXCEED THE AMOUNT YOU
            PAID TO LEARNCONSULT, IF ANY, IN THE PAST 12 MONTHS.
          </p>
          <p class="text-gray-600 mb-4">
            SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OR LIMITATION OF
            LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL DAMAGES, SO THE ABOVE
            LIMITATION MAY NOT APPLY TO YOU.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            10. Indemnification
          </h2>
          <p class="text-gray-600 mb-4">
            You agree to defend, indemnify, and hold harmless LearnConsult, its
            affiliates, licensors, and service providers, and its and their
            respective officers, directors, employees, contractors, agents,
            licensors, suppliers, successors, and assigns from and against any
            claims, liabilities, damages, judgments, awards, losses, costs,
            expenses, or fees (including reasonable attorneys' fees) arising out
            of or relating to your violation of these Terms or your use of the
            Services.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            11. Termination
          </h2>
          <p class="text-gray-600 mb-4">
            We may terminate or suspend your access to the Services immediately,
            without prior notice or liability, for any reason whatsoever,
            including without limitation if you breach the Terms.
          </p>
          <p class="text-gray-600 mb-4">
            Upon termination, your right to use the Services will immediately
            cease. If you wish to terminate your account, you may simply
            discontinue using the Services or contact us to request account
            deletion.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            12. Governing Law
          </h2>
          <p class="text-gray-600 mb-4">
            These Terms and any action related thereto will be governed by the
            laws of the State of California, United States, without regard to
            its conflict of law provisions.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            13. Changes to Terms
          </h2>
          <p class="text-gray-600 mb-4">
            We may revise and update these Terms from time to time in our sole
            discretion. All changes are effective immediately when we post them
            and apply to all access to and use of the Services thereafter.
          </p>
          <p class="text-gray-600 mb-4">
            Your continued use of the Services following the posting of revised
            Terms means that you accept and agree to the changes. You are
            expected to check this page frequently so you are aware of any
            changes, as they are binding on you.
          </p>

          <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            14. Contact Us
          </h2>
          <p class="text-gray-600 mb-4">
            If you have any questions about these Terms, please contact us at:
          </p>
          <p class="text-gray-600 mb-4">
            Email:
            <a
              href="mailto:legal@learnconsult.com"
              class="text-indigo-600 hover:text-indigo-800"
              >legal@learnconsult.com</a
            ><br />
            Address: 123 Learning Street, Education City, EC 12345
          </p>
        </div>
      </div>
    </main>
  </div>
</template>
