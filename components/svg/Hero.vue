<script lang="ts" setup></script>

<template>
  <svg
    width="800"
    height="500"
    viewBox="0 0 800 500"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <!-- Dashboard background -->
    <rect
      x="50"
      y="50"
      width="700"
      height="400"
      rx="16"
      fill="#F9FAFB"
      stroke="#E5E7EB"
      stroke-width="2"
    />

    <!-- Top navigation bar -->
    <rect
      x="50"
      y="50"
      width="700"
      height="60"
      rx="16"
      fill="#4F46E5"
      stroke="#4338CA"
      stroke-width="2"
    />

    <!-- Logo and navigation items -->
    <circle cx="90" cy="80" r="15" fill="#818CF8" />
    <rect x="120" y="75" width="80" height="10" rx="5" fill="white" />
    <rect x="220" y="75" width="60" height="10" rx="5" fill="#C7D2FE" />
    <rect x="300" y="75" width="60" height="10" rx="5" fill="#C7D2FE" />
    <rect x="380" y="75" width="60" height="10" rx="5" fill="#C7D2FE" />

    <!-- User profile -->
    <circle cx="720" cy="80" r="15" fill="#C7D2FE" />
    <rect x="670" y="75" width="40" height="10" rx="5" fill="white" />

    <!-- Sidebar -->
    <rect x="50" y="110" width="180" height="340" fill="#F3F4F6" />

    <!-- Sidebar menu items -->
    <rect x="70" y="140" width="140" height="36" rx="8" fill="#4F46E5" />
    <rect x="85" y="153" width="90" height="10" rx="5" fill="white" />
    <circle cx="80" cy="158" r="6" fill="white" />

    <rect x="70" y="186" width="140" height="36" rx="8" fill="#F3F4F6" />
    <rect x="85" y="199" width="90" height="10" rx="5" fill="#6B7280" />
    <circle cx="80" cy="204" r="6" fill="#6B7280" />

    <rect x="70" y="232" width="140" height="36" rx="8" fill="#F3F4F6" />
    <rect x="85" y="245" width="90" height="10" rx="5" fill="#6B7280" />
    <circle cx="80" cy="250" r="6" fill="#6B7280" />

    <rect x="70" y="278" width="140" height="36" rx="8" fill="#F3F4F6" />
    <rect x="85" y="291" width="90" height="10" rx="5" fill="#6B7280" />
    <circle cx="80" cy="296" r="6" fill="#6B7280" />

    <!-- Main content area -->
    <rect
      x="260"
      y="140"
      width="460"
      height="120"
      rx="8"
      fill="white"
      stroke="#E5E7EB"
      stroke-width="2"
    />
    <rect x="280" y="160" width="160" height="16" rx="8" fill="#111827" />
    <rect x="280" y="186" width="120" height="10" rx="5" fill="#6B7280" />
    <rect x="280" y="206" width="420" height="10" rx="5" fill="#D1D5DB" />
    <rect x="280" y="226" width="380" height="10" rx="5" fill="#D1D5DB" />

    <!-- Stats cards -->
    <rect
      x="260"
      y="280"
      width="220"
      height="100"
      rx="8"
      fill="white"
      stroke="#E5E7EB"
      stroke-width="2"
    />
    <rect x="280" y="300" width="80" height="16" rx="8" fill="#111827" />
    <rect x="280" y="326" width="40" height="10" rx="5" fill="#6B7280" />
    <rect x="280" y="346" width="180" height="10" rx="5" fill="#D1D5DB" />
    <circle cx="440" cy="330" r="30" fill="#C7D2FE" />
    <path d="M450 330 L440 345 L430 330 L440 315 Z" fill="#4F46E5" />

    <rect
      x="500"
      y="280"
      width="220"
      height="100"
      rx="8"
      fill="white"
      stroke="#E5E7EB"
      stroke-width="2"
    />
    <rect x="520" y="300" width="80" height="16" rx="8" fill="#111827" />
    <rect x="520" y="326" width="40" height="10" rx="5" fill="#6B7280" />
    <rect x="520" y="346" width="180" height="10" rx="5" fill="#D1D5DB" />
    <circle cx="680" cy="330" r="30" fill="#FBCFE8" />
    <path d="M690 330 L680 345 L670 330 L680 315 Z" fill="#EC4899" />

    <!-- Progress section -->
    <rect
      x="260"
      y="400"
      width="460"
      height="50"
      rx="8"
      fill="white"
      stroke="#E5E7EB"
      stroke-width="2"
    />
    <rect x="280" y="420" width="120" height="10" rx="5" fill="#111827" />
    <rect x="440" y="420" width="260" height="10" rx="5" fill="#E5E7EB" />
    <rect x="440" y="420" width="180" height="10" rx="5" fill="#4F46E5" />

    <!-- Animation elements -->
    <circle cx="440" cy="330" r="30" fill="#C7D2FE" opacity="0.7">
      <animate
        attributeName="r"
        values="30;32;30"
        dur="4s"
        repeatCount="indefinite"
      />
    </circle>

    <circle cx="680" cy="330" r="30" fill="#FBCFE8" opacity="0.7">
      <animate
        attributeName="r"
        values="30;32;30"
        dur="4s"
        begin="1s"
        repeatCount="indefinite"
      />
    </circle>

    <!-- Animated highlight pulse -->
    <rect
      x="70"
      y="140"
      width="140"
      height="36"
      rx="8"
      fill="#4F46E5"
      opacity="0.8"
    >
      <animate
        attributeName="opacity"
        values="0.8;1;0.8"
        dur="3s"
        repeatCount="indefinite"
      />
    </rect>

    <!-- Live users dot pulse -->
    <circle cx="500" cy="210" r="4" fill="#10B981">
      <animate
        attributeName="opacity"
        values="1;0.5;1"
        dur="1.5s"
        repeatCount="indefinite"
      />
    </circle>
    <rect
      x="510"
      y="206"
      width="50"
      height="8"
      rx="4"
      fill="#10B981"
      opacity="0.8"
    />
  </svg>
</template>
