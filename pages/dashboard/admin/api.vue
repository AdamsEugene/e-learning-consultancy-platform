<!-- pages/dashboard/admin/api/index.vue -->
<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";

// Set page metadata
definePageMeta({
  layout: "dashboard-admin",
});

// Set page title
useHead({
  title: "API Management - Admin Dashboard",
});

// API Keys data structure
interface ApiKey {
  id: string;
  name: string;
  key: string;
  created: string;
  expires: string | null;
  lastUsed: string | null;
  status: "active" | "revoked" | "expired";
  owner: {
    id: number;
    name: string;
    email: string;
    role: string;
  };
  scopes: string[];
  usageLimit: number;
  usageCount: number;
  ipRestrictions: string[] | null;
}

// API Endpoint data structure
interface ApiEndpoint {
  id: string;
  path: string;
  method: "GET" | "POST" | "PUT" | "DELETE" | "PATCH";
  description: string;
  totalCalls: number;
  averageResponseTime: number; // in ms
  errorRate: number; // percentage 0-100
  lastError: string | null;
  status: "online" | "degraded" | "offline";
  rateLimit: {
    limit: number;
    period: string; // "second", "minute", "hour", "day"
  };
  latency: number[]; // last 24 hours data points
}

// Usage log entry data structure
interface UsageLog {
  id: string;
  timestamp: string;
  endpoint: string;
  method: string;
  keyId: string;
  keyOwner: string;
  status: number;
  responseTime: number;
  ipAddress: string;
  userAgent: string;
  errorMessage: string | null;
}

// State variables
const apiKeys = ref<ApiKey[]>([]);
const apiEndpoints = ref<ApiEndpoint[]>([]);
const usageLogs = ref<UsageLog[]>([]);
const activeTab = ref("keys");
const searchQuery = ref("");
const isLoading = ref(true);
const showNewKeyForm = ref(false);
const showKeyDetails = ref(false);
const selectedKey = ref<ApiKey | null>(null);
const showDeleteConfirm = ref(false);
const keyToDelete = ref<ApiKey | null>(null);
const dateRange = ref<{ start: string; end: string }>({
  start: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
    .toISOString()
    .split("T")[0], // 7 days ago
  end: new Date().toISOString().split("T")[0], // today
});
const rateLimitPeriods = ["second", "minute", "hour", "day"];

// New key form data
const newKey = ref({
  name: "",
  owner: {
    id: 0,
    name: "",
    email: "",
    role: "",
  },
  scopes: [] as string[],
  expiresIn: "30", // days
  usageLimit: 1000,
  ipRestrictions: "",
});

// Users data (for dropdowns)
const users = ref([
  {
    id: 1,
    name: "Sarah Parker",
    email: "sarah@example.com",
    role: "consultant",
  },
  {
    id: 2,
    name: "Michael Roberts",
    email: "michael@example.com",
    role: "client",
  },
  {
    id: 3,
    name: "Dr. Alex Thompson",
    email: "alex@example.com",
    role: "instructor",
  },
  { id: 4, name: "Admin User", email: "admin@example.com", role: "admin" },
]);

// API scopes
const availableScopes = [
  {
    id: "read:courses",
    name: "Read Courses",
    description: "View course information",
  },
  {
    id: "write:courses",
    name: "Write Courses",
    description: "Create and update courses",
  },
  {
    id: "read:users",
    name: "Read Users",
    description: "View user information",
  },
  {
    id: "write:users",
    name: "Write Users",
    description: "Create and update users",
  },
  {
    id: "read:projects",
    name: "Read Projects",
    description: "View project information",
  },
  {
    id: "write:projects",
    name: "Write Projects",
    description: "Create and update projects",
  },
  {
    id: "read:financial",
    name: "Read Financial",
    description: "View financial information",
  },
  {
    id: "write:financial",
    name: "Write Financial",
    description: "Process payments and refunds",
  },
];

// Fetch API keys (mock data for now)
const fetchApiKeys = async () => {
  isLoading.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    apiKeys.value = [
      {
        id: "key_1a2b3c4d5e",
        name: "Course Integration API Key",
        key: "lc_live_" + generateRandomString(24),
        created: "2025-03-15T10:30:00Z",
        expires: "2026-03-15T10:30:00Z",
        lastUsed: "2025-04-15T14:23:10Z",
        status: "active",
        owner: {
          id: 1,
          name: "Sarah Parker",
          email: "sarah@example.com",
          role: "consultant",
        },
        scopes: ["read:courses", "write:courses"],
        usageLimit: 10000,
        usageCount: 3456,
        ipRestrictions: ["192.168.1.1", "10.0.0.1/24"],
      },
      {
        id: "key_2b3c4d5e6f",
        name: "User Management API",
        key: "lc_live_" + generateRandomString(24),
        created: "2025-02-20T08:15:00Z",
        expires: null,
        lastUsed: "2025-04-15T18:42:33Z",
        status: "active",
        owner: {
          id: 2,
          name: "Michael Roberts",
          email: "michael@example.com",
          role: "client",
        },
        scopes: ["read:users"],
        usageLimit: 5000,
        usageCount: 2178,
        ipRestrictions: null,
      },
      {
        id: "key_3c4d5e6f7g",
        name: "Legacy System Integration",
        key: "lc_live_" + generateRandomString(24),
        created: "2024-11-10T14:22:00Z",
        expires: "2025-05-10T14:22:00Z",
        lastUsed: "2025-03-28T09:11:45Z",
        status: "revoked",
        owner: {
          id: 3,
          name: "Dr. Alex Thompson",
          email: "alex@example.com",
          role: "instructor",
        },
        scopes: ["read:courses", "read:users", "read:projects"],
        usageLimit: 20000,
        usageCount: 15680,
        ipRestrictions: ["203.0.113.0/24"],
      },
      {
        id: "key_4d5e6f7g8h",
        name: "Financial Dashboard",
        key: "lc_live_" + generateRandomString(24),
        created: "2025-01-05T11:45:00Z",
        expires: "2025-01-20T11:45:00Z",
        lastUsed: "2025-01-18T16:37:22Z",
        status: "expired",
        owner: {
          id: 4,
          name: "Admin User",
          email: "admin@example.com",
          role: "admin",
        },
        scopes: ["read:financial"],
        usageLimit: 1000,
        usageCount: 987,
        ipRestrictions: null,
      },
      {
        id: "key_5e6f7g8h9i",
        name: "Platform Monitoring",
        key: "lc_live_" + generateRandomString(24),
        created: "2025-03-30T09:20:00Z",
        expires: "2026-03-30T09:20:00Z",
        lastUsed: "2025-04-15T23:59:01Z",
        status: "active",
        owner: {
          id: 4,
          name: "Admin User",
          email: "admin@example.com",
          role: "admin",
        },
        scopes: [
          "read:courses",
          "read:users",
          "read:projects",
          "read:financial",
        ],
        usageLimit: 50000,
        usageCount: 12468,
        ipRestrictions: null,
      },
    ];
  } catch (error) {
    console.error("Error fetching API keys:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch API endpoints (mock data for now)
const fetchApiEndpoints = async () => {
  isLoading.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    apiEndpoints.value = [
      {
        id: "ep_1a2b3c4d",
        path: "/api/v1/courses",
        method: "GET",
        description: "Get all courses",
        totalCalls: 24879,
        averageResponseTime: 145,
        errorRate: 0.3,
        lastError: null,
        status: "online",
        rateLimit: {
          limit: 100,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
      {
        id: "ep_2b3c4d5e",
        path: "/api/v1/courses/:id",
        method: "GET",
        description: "Get course by ID",
        totalCalls: 36542,
        averageResponseTime: 125,
        errorRate: 1.2,
        lastError: "Course not found (404)",
        status: "online",
        rateLimit: {
          limit: 200,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
      {
        id: "ep_3c4d5e6f",
        path: "/api/v1/courses",
        method: "POST",
        description: "Create a new course",
        totalCalls: 5678,
        averageResponseTime: 310,
        errorRate: 2.8,
        lastError: "Validation failed (422)",
        status: "online",
        rateLimit: {
          limit: 50,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
      {
        id: "ep_4d5e6f7g",
        path: "/api/v1/users",
        method: "GET",
        description: "Get all users",
        totalCalls: 18765,
        averageResponseTime: 220,
        errorRate: 0.5,
        lastError: null,
        status: "online",
        rateLimit: {
          limit: 60,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
      {
        id: "ep_5e6f7g8h",
        path: "/api/v1/projects",
        method: "GET",
        description: "Get all projects",
        totalCalls: 9876,
        averageResponseTime: 285,
        errorRate: 0.8,
        lastError: null,
        status: "online",
        rateLimit: {
          limit: 80,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
      {
        id: "ep_6f7g8h9i",
        path: "/api/v1/analytics/dashboard",
        method: "GET",
        description: "Get analytics dashboard data",
        totalCalls: 4321,
        averageResponseTime: 680,
        errorRate: 5.2,
        lastError: "Timeout (504)",
        status: "degraded",
        rateLimit: {
          limit: 30,
          period: "minute",
        },
        latency: generateRandomLatencyData(true),
      },
      {
        id: "ep_7g8h9i0j",
        path: "/api/v1/payments/process",
        method: "POST",
        description: "Process payment",
        totalCalls: 3210,
        averageResponseTime: 520,
        errorRate: 1.8,
        lastError: "Payment gateway error (502)",
        status: "online",
        rateLimit: {
          limit: 20,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
      {
        id: "ep_8h9i0j1k",
        path: "/api/v1/webhooks/stripe",
        method: "POST",
        description: "Stripe webhook endpoint",
        totalCalls: 2345,
        averageResponseTime: 180,
        errorRate: 0.6,
        lastError: null,
        status: "online",
        rateLimit: {
          limit: 500,
          period: "minute",
        },
        latency: generateRandomLatencyData(),
      },
    ];
  } catch (error) {
    console.error("Error fetching API endpoints:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch usage logs (mock data for now)
const fetchUsageLogs = async () => {
  isLoading.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 800));

    // Generate mock logs
    const logs: UsageLog[] = [];

    for (let i = 0; i < 50; i++) {
      const timestamp = new Date(
        Date.now() - Math.random() * 7 * 24 * 60 * 60 * 1000
      );
      const endpoint =
        apiEndpoints.value[
          Math.floor(Math.random() * apiEndpoints.value.length)
        ];
      const key =
        apiKeys.value[Math.floor(Math.random() * apiKeys.value.length)];
      const status =
        Math.random() > 0.9 ? (Math.random() > 0.5 ? 404 : 500) : 200;
      const responseTime = Math.floor(
        endpoint.averageResponseTime * (0.7 + Math.random() * 0.6)
      );

      logs.push({
        id: `log_${generateRandomString(10)}`,
        timestamp: timestamp.toISOString(),
        endpoint: endpoint.path,
        method: endpoint.method,
        keyId: key.id,
        keyOwner: key.owner.name,
        status,
        responseTime,
        ipAddress: generateRandomIp(),
        userAgent: generateRandomUserAgent(),
        errorMessage: status !== 200 ? generateRandomError(status) : null,
      });
    }

    // Sort by timestamp, newest first
    logs.sort(
      (a, b) =>
        new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
    );

    usageLogs.value = logs;
  } catch (error) {
    console.error("Error fetching usage logs:", error);
  } finally {
    isLoading.value = false;
  }
};

// Generate a random string of specified length
function generateRandomString(length: number) {
  const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
  let result = "";
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}

// Generate random latency data points for the last 24 hours
function generateRandomLatencyData(degraded = false) {
  const latency = [];
  const baseLatency = degraded ? 600 : 200;
  const variance = degraded ? 400 : 100;

  for (let i = 0; i < 24; i++) {
    latency.push(baseLatency + Math.floor(Math.random() * variance));
  }

  if (degraded) {
    // Add some spikes for degraded services
    for (let i = 0; i < 5; i++) {
      const index = Math.floor(Math.random() * 24);
      latency[index] =
        baseLatency + variance + Math.floor(Math.random() * 1000);
    }
  }

  return latency;
}

// Generate a random IP address
function generateRandomIp() {
  return `${Math.floor(Math.random() * 256)}.${Math.floor(
    Math.random() * 256
  )}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}`;
}

// Generate a random user agent
function generateRandomUserAgent() {
  const agents = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Safari/605.1.15",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36",
    "PostmanRuntime/7.31.1",
    "node-fetch/1.0 (+https://github.com/bitinn/node-fetch)",
    "axios/1.3.4",
    "curl/7.68.0",
    "Custom SDK v2.5.1 (Node.js v18.12.1)",
  ];

  return agents[Math.floor(Math.random() * agents.length)];
}

// Generate a random error message based on status code
function generateRandomError(status: number) {
  if (status === 404) {
    return "Resource not found";
  } else if (status === 400) {
    return "Invalid request parameters";
  } else if (status === 401) {
    return "Unauthorized access";
  } else if (status === 403) {
    return "Forbidden - insufficient permissions";
  } else if (status === 429) {
    return "Rate limit exceeded";
  } else if (status === 500) {
    return "Internal server error";
  } else if (status === 502) {
    return "Bad gateway";
  } else if (status === 503) {
    return "Service unavailable";
  } else if (status === 504) {
    return "Gateway timeout";
  }

  return "Unknown error";
}

// Filter API keys based on search query
const filteredApiKeys = computed(() => {
  if (!searchQuery.value) {
    return apiKeys.value;
  }

  const query = searchQuery.value.toLowerCase();
  return apiKeys.value.filter(
    (key) =>
      key.name.toLowerCase().includes(query) ||
      key.key.toLowerCase().includes(query) ||
      key.owner.name.toLowerCase().includes(query) ||
      key.owner.email.toLowerCase().includes(query)
  );
});

// Filter API endpoints based on search query
const filteredApiEndpoints = computed(() => {
  if (!searchQuery.value) {
    return apiEndpoints.value;
  }

  const query = searchQuery.value.toLowerCase();
  return apiEndpoints.value.filter(
    (endpoint) =>
      endpoint.path.toLowerCase().includes(query) ||
      endpoint.description.toLowerCase().includes(query) ||
      endpoint.method.toLowerCase().includes(query)
  );
});

// Filter usage logs based on search query and date range
const filteredUsageLogs = computed(() => {
  let filtered = usageLogs.value;

  // Apply date range filter
  const startDate = new Date(dateRange.value.start);
  const endDate = new Date(dateRange.value.end);
  endDate.setHours(23, 59, 59, 999); // End of day

  filtered = filtered.filter((log) => {
    const logDate = new Date(log.timestamp);
    return logDate >= startDate && logDate <= endDate;
  });

  // Apply search query if present
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (log) =>
        log.endpoint.toLowerCase().includes(query) ||
        log.keyOwner.toLowerCase().includes(query) ||
        log.ipAddress.includes(query) ||
        (log.errorMessage && log.errorMessage.toLowerCase().includes(query))
    );
  }

  return filtered;
});

// Format datetime string to readable format
const formatDateTime = (dateString: string) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Format date string to readable format (without time)
const formatDate = (dateString: string) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Create a new API key
const createApiKey = () => {
  // Validate form
  if (
    !newKey.value.name ||
    !newKey.value.owner.id ||
    !newKey.value.scopes.length
  ) {
    alert("Please fill in all required fields");
    return;
  }

  // In a real app, this would be an API call
  const ownerInfo = users.value.find((u) => u.id === newKey.value.owner.id);

  if (!ownerInfo) {
    alert("Invalid owner selected");
    return;
  }

  // Create new key object
  const expiresDate =
    newKey.value.expiresIn === "never"
      ? null
      : new Date(
          Date.now() + parseInt(newKey.value.expiresIn) * 24 * 60 * 60 * 1000
        ).toISOString();

  const ipArray = newKey.value.ipRestrictions
    ? newKey.value.ipRestrictions
        .split(",")
        .map((ip) => ip.trim())
        .filter((ip) => ip)
    : null;

  const keyString = "lc_live_" + generateRandomString(24);

  const newKeyObj: ApiKey = {
    id: "key_" + generateRandomString(10),
    name: newKey.value.name,
    key: keyString,
    created: new Date().toISOString(),
    expires: expiresDate,
    lastUsed: null,
    status: "active",
    owner: {
      id: ownerInfo.id,
      name: ownerInfo.name,
      email: ownerInfo.email,
      role: ownerInfo.role,
    },
    scopes: [...newKey.value.scopes],
    usageLimit: newKey.value.usageLimit,
    usageCount: 0,
    ipRestrictions: ipArray,
  };

  // Add to keys array
  apiKeys.value.unshift(newKeyObj);

  // Reset form and close modal
  resetNewKeyForm();
  showNewKeyForm.value = false;

  // Select the new key to display details
  selectedKey.value = newKeyObj;
  showKeyDetails.value = true;
};

// Reset new key form
const resetNewKeyForm = () => {
  newKey.value = {
    name: "",
    owner: {
      id: 0,
      name: "",
      email: "",
      role: "",
    },
    scopes: [],
    expiresIn: "30",
    usageLimit: 1000,
    ipRestrictions: "",
  };
};

// Set user on owner selection
const onOwnerSelect = (userId: number) => {
  const user = users.value.find((u) => u.id === userId);
  if (user) {
    newKey.value.owner = { ...user };
  }
};

// Delete API key
const deleteApiKey = () => {
  if (!keyToDelete.value) return;

  // In a real app, this would be an API call
  apiKeys.value = apiKeys.value.filter(
    (key) => key.id !== keyToDelete.value?.id
  );

  // Reset state
  keyToDelete.value = null;
  showDeleteConfirm.value = false;

  // If we were viewing this key, close details
  if (selectedKey.value && selectedKey.value.id === keyToDelete.value?.id) {
    showKeyDetails.value = false;
    selectedKey.value = null;
  }
};

// Revoke API key
const revokeApiKey = (key: ApiKey) => {
  // In a real app, this would be an API call
  const keyIndex = apiKeys.value.findIndex((k) => k.id === key.id);
  if (keyIndex >= 0) {
    const updatedKey = {
      ...apiKeys.value[keyIndex],
      status: "revoked" as const,
    };
    apiKeys.value.splice(keyIndex, 1, updatedKey);

    // Update selected key if we're viewing it
    if (selectedKey.value && selectedKey.value.id === key.id) {
      selectedKey.value = updatedKey;
    }
  }
};

// View API key details
const viewKeyDetails = (key: ApiKey) => {
  selectedKey.value = key;
  showKeyDetails.value = true;
};

// Get badge color based on key status
const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case "active":
      return "bg-green-100 text-green-800";
    case "expired":
      return "bg-gray-100 text-gray-800";
    case "revoked":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get endpoint status badge class
const getEndpointStatusBadgeClass = (status: string) => {
  switch (status) {
    case "online":
      return "bg-green-100 text-green-800";
    case "degraded":
      return "bg-yellow-100 text-yellow-800";
    case "offline":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Get log status badge class
const getLogStatusBadgeClass = (status: number) => {
  if (status >= 200 && status < 300) {
    return "bg-green-100 text-green-800";
  } else if (status >= 400 && status < 500) {
    return "bg-yellow-100 text-yellow-800";
  } else if (status >= 500) {
    return "bg-red-100 text-red-800";
  }
  return "bg-gray-100 text-gray-800";
};

// Get scope badge color based on scope type
const getScopeBadgeClass = (scope: string) => {
  if (scope.startsWith("read:")) {
    return "bg-blue-100 text-blue-800";
  } else if (scope.startsWith("write:")) {
    return "bg-purple-100 text-purple-800";
  }
  return "bg-gray-100 text-gray-800";
};

// Copy API key to clipboard
const copyApiKey = (key: string) => {
  navigator.clipboard
    .writeText(key)
    .then(() => {
      alert("API key copied to clipboard!");
    })
    .catch((err) => {
      console.error("Could not copy API key: ", err);
    });
};

// Copy curl command to clipboard
const copyCurlCommand = (endpoint: ApiEndpoint) => {
  const command = `curl -X ${endpoint.method} \\
  -H "Authorization: Bearer YOUR_API_KEY" \\
  -H "Content-Type: application/json" \\
  https://api.learnconsult.com${endpoint.path}`;

  navigator.clipboard
    .writeText(command)
    .then(() => {
      alert("curl command copied to clipboard!");
    })
    .catch((err) => {
      console.error("Could not copy command: ", err);
    });
};

// Update date range
const updateDateRange = () => {
  // Refresh logs with date filter
  fetchUsageLogs();
};

// Fetch data on component mount
onMounted(async () => {
  await Promise.all([fetchApiKeys(), fetchApiEndpoints(), fetchUsageLogs()]);
});

// Watch search query for changes
watch(searchQuery, () => {
  // If we're in logs tab and have a date range, we might need to refetch
  if (activeTab.value === "logs") {
    fetchUsageLogs();
  }
});

// Format usage progress percentage
const getUsagePercentage = (used: number, limit: number) => {
  return Math.min(Math.round((used / limit) * 100), 100);
};

// Get progress bar color based on usage percentage
const getUsageColor = (percentage: number) => {
  if (percentage < 60) {
    return "bg-green-500";
  } else if (percentage < 80) {
    return "bg-yellow-500";
  } else {
    return "bg-red-500";
  }
};

// Get uptime color based on error rate
const getUptimeColor = (errorRate: number) => {
  if (errorRate < 1) {
    return "text-green-600";
  } else if (errorRate < 5) {
    return "text-yellow-600";
  } else {
    return "text-red-600";
  }
};

// Format latency data for small charts
const formatLatencyData = (latency: number[]) => {
  // Return points for a simple sparkline
  return latency.map((y, x) => ({ x, y }));
};

// Get scope description by ID
const getScopeDescription = (scopeId: string) => {
  const scope = availableScopes.find((s) => s.id === scopeId);
  return scope ? scope.description : "";
};

// Calculate usage statistics
const logStats = computed(() => {
  const total = filteredUsageLogs.value.length;
  const successful = filteredUsageLogs.value.filter(
    (log) => log.status >= 200 && log.status < 300
  ).length;
  const clientErrors = filteredUsageLogs.value.filter(
    (log) => log.status >= 400 && log.status < 500
  ).length;
  const serverErrors = filteredUsageLogs.value.filter(
    (log) => log.status >= 500
  ).length;

  const avgResponseTime =
    filteredUsageLogs.value.reduce((sum, log) => sum + log.responseTime, 0) /
      total || 0;

  return {
    total,
    successful,
    clientErrors,
    serverErrors,
    successRate: total ? ((successful / total) * 100).toFixed(1) : "0",
    avgResponseTime: avgResponseTime.toFixed(0),
  };
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">API Management</h1>
          <p class="text-gray-600">
            Manage API keys, monitor usage, and configure endpoints
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-4">
          <button
            @click="showNewKeyForm = true"
            class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Create API Key</span>
          </button>

          <a
            href="#"
            target="_blank"
            class="inline-flex items-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors shadow-sm"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-1"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h2a1 1 0 100-2H9z"
                clip-rule="evenodd"
              />
            </svg>
            <span>API Documentation</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-indigo-100 rounded-full p-3 mr-4">
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
                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Active API Keys</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ apiKeys.filter((k) => k.status === "active").length }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-green-100 rounded-full p-3 mr-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-green-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">API Endpoints</p>
            <p class="text-2xl font-bold text-gray-900">
              {{ apiEndpoints.length }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-blue-100 rounded-full p-3 mr-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-blue-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">
              Total API Calls Today
            </p>
            <p class="text-2xl font-bold text-gray-900">12,456</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
        <div class="flex items-center">
          <div class="bg-purple-100 rounded-full p-3 mr-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-purple-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z"
              />
            </svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 font-medium">Avg. Response Time</p>
            <p class="text-2xl font-bold text-gray-900">187ms</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs and search bar -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Tab navigation -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            @click="activeTab = 'keys'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'keys'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            API Keys
          </button>
          <button
            @click="activeTab = 'endpoints'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'endpoints'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            API Endpoints
          </button>
          <button
            @click="activeTab = 'logs'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeTab === 'logs'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Usage Logs
          </button>
        </div>

        <!-- Search bar -->
        <div class="relative w-full md:w-auto">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search..."
            class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          />
          <div class="absolute left-3 top-2.5 text-gray-400">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div
      v-if="isLoading"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div class="p-4 border-b border-gray-200">
        <div class="animate-pulse flex space-x-4">
          <div class="flex-1 space-y-4 py-1">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="space-y-2">
              <div class="h-4 bg-gray-200 rounded"></div>
              <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-for="i in 3"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- API Keys Tab -->
    <div
      v-else-if="activeTab === 'keys'"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div v-if="filteredApiKeys.length === 0" class="p-8 text-center">
        <div
          class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          No API keys found
        </h3>
        <p class="text-gray-600 mb-6">
          {{
            searchQuery
              ? "No API keys match your search criteria."
              : "You haven't created any API keys yet."
          }}
        </p>
        <button
          @click="showNewKeyForm = true"
          class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-1"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
              clip-rule="evenodd"
            />
          </svg>
          <span>Create API Key</span>
        </button>
      </div>

      <div v-else>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Name / Key
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Owner
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Created / Expires
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Usage
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="key in filteredApiKeys"
              :key="key.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <div class="text-sm font-medium text-gray-900">
                    {{ key.name }}
                  </div>
                  <div
                    class="text-sm text-gray-500 font-mono truncate max-w-xs"
                  >
                    {{ key.key }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <div class="text-sm font-medium text-gray-900">
                    {{ key.owner.name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ key.owner.email }}</div>
                  <div class="text-xs text-gray-500 mt-1 capitalize">
                    {{ key.owner.role }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                  :class="getStatusBadgeClass(key.status)"
                >
                  {{ key.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900">
                  {{ formatDate(key.created) }}
                </div>
                <div class="text-sm text-gray-500">
                  <span v-if="key.expires"
                    >Expires: {{ formatDate(key.expires) }}</span
                  >
                  <span v-else>Never expires</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-500 mb-1">
                  {{ key.usageCount.toLocaleString() }} /
                  {{ key.usageLimit.toLocaleString() }}
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div
                    class="h-2.5 rounded-full transition-all duration-500"
                    :class="
                      getUsageColor(
                        getUsagePercentage(key.usageCount, key.usageLimit)
                      )
                    "
                    :style="{
                      width: `${getUsagePercentage(
                        key.usageCount,
                        key.usageLimit
                      )}%`,
                    }"
                  ></div>
                </div>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
              >
                <div class="flex space-x-2 justify-end">
                  <button
                    @click="viewKeyDetails(key)"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                    title="View details"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                      <path
                        fill-rule="evenodd"
                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>

                  <button
                    v-if="key.status === 'active'"
                    @click="revokeApiKey(key)"
                    class="p-2 rounded-lg text-gray-500 hover:bg-yellow-100 hover:text-yellow-700 transition-colors"
                    title="Revoke key"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>

                  <button
                    @click="
                      keyToDelete = key;
                      showDeleteConfirm = true;
                    "
                    class="p-2 rounded-lg text-gray-500 hover:bg-red-100 hover:text-red-700 transition-colors"
                    title="Delete key"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- API Endpoints Tab -->
    <div
      v-else-if="activeTab === 'endpoints'"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <div v-if="filteredApiEndpoints.length === 0" class="p-8 text-center">
        <div
          class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          No API endpoints found
        </h3>
        <p class="text-gray-600 mb-6">
          {{
            searchQuery
              ? "No API endpoints match your search criteria."
              : "There are no API endpoints configured yet."
          }}
        </p>
      </div>

      <div v-else>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4">
          <div
            v-for="endpoint in filteredApiEndpoints"
            :key="endpoint.id"
            class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between mb-2">
              <div>
                <div class="flex items-center space-x-2">
                  <span
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase"
                    :class="{
                      'bg-green-100 text-green-800': endpoint.method === 'GET',
                      'bg-blue-100 text-blue-800': endpoint.method === 'POST',
                      'bg-yellow-100 text-yellow-800':
                        endpoint.method === 'PUT',
                      'bg-red-100 text-red-800': endpoint.method === 'DELETE',
                      'bg-purple-100 text-purple-800':
                        endpoint.method === 'PATCH',
                    }"
                  >
                    {{ endpoint.method }}
                  </span>
                  <span
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                    :class="getEndpointStatusBadgeClass(endpoint.status)"
                  >
                    {{ endpoint.status }}
                  </span>
                </div>
                <h3 class="text-lg font-mono mt-2 text-gray-900">
                  {{ endpoint.path }}
                </h3>
              </div>
              <button
                @click="copyCurlCommand(endpoint)"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                title="Copy curl command"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                  <path
                    d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"
                  />
                </svg>
              </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">{{ endpoint.description }}</p>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
              <div>
                <div class="text-xs text-gray-500 mb-1">Total Calls</div>
                <div class="text-lg font-semibold text-gray-800">
                  {{ endpoint.totalCalls.toLocaleString() }}
                </div>
              </div>

              <div>
                <div class="text-xs text-gray-500 mb-1">Avg. Response Time</div>
                <div class="text-lg font-semibold text-gray-800">
                  {{ endpoint.averageResponseTime }}ms
                </div>
              </div>

              <div>
                <div class="text-xs text-gray-500 mb-1">Uptime</div>
                <div
                  class="text-lg font-semibold"
                  :class="getUptimeColor(endpoint.errorRate)"
                >
                  {{ (100 - endpoint.errorRate).toFixed(1) }}%
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-600">
                <span class="font-medium">Rate Limit:</span>
                {{ endpoint.rateLimit.limit }} per
                {{ endpoint.rateLimit.period }}
              </div>

              <div v-if="endpoint.lastError" class="text-sm text-red-600">
                Last Error: {{ endpoint.lastError }}
              </div>
            </div>

            <!-- Simple sparkline chart for response times -->
            <div class="h-8 mt-2">
              <div class="flex h-full items-end space-x-1">
                <div
                  v-for="(dataPoint, index) in endpoint.latency"
                  :key="`${endpoint.id}-point-${index}`"
                  class="w-2 transition-all duration-300"
                  :class="{
                    'bg-green-500': dataPoint < endpoint.averageResponseTime,
                    'bg-yellow-500':
                      dataPoint >= endpoint.averageResponseTime &&
                      dataPoint < endpoint.averageResponseTime * 2,
                    'bg-red-500': dataPoint >= endpoint.averageResponseTime * 2,
                  }"
                  :style="{
                    height: `${Math.min(Math.max(dataPoint / 10, 4), 32)}px`,
                  }"
                ></div>
              </div>
            </div>
            <div class="text-xs text-gray-500 mt-1">
              Response time (last 24 hours)
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Usage Logs Tab -->
    <div
      v-else-if="activeTab === 'logs'"
      class="bg-white rounded-lg shadow border border-gray-200"
    >
      <!-- Date range picker and stats -->
      <div class="p-4 border-b border-gray-200">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between mb-4"
        >
          <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <div>
              <label
                for="start-date"
                class="block text-sm font-medium text-gray-700"
                >Start Date</label
              >
              <input
                id="start-date"
                v-model="dateRange.start"
                type="date"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <div>
              <label
                for="end-date"
                class="block text-sm font-medium text-gray-700"
                >End Date</label
              >
              <input
                id="end-date"
                v-model="dateRange.end"
                type="date"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>
            <div class="mt-4">
              <button
                @click="updateDateRange"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
              >
                Apply
              </button>
            </div>
          </div>

          <div class="flex space-x-2 text-center">
            <div class="px-4 py-2 bg-gray-50 rounded-lg">
              <div class="text-sm text-gray-500">Total</div>
              <div class="text-lg font-semibold text-gray-900">
                {{ logStats.total }}
              </div>
            </div>

            <div class="px-4 py-2 bg-green-50 rounded-lg">
              <div class="text-sm text-gray-500">Success</div>
              <div class="text-lg font-semibold text-green-600">
                {{ logStats.successful }}
              </div>
            </div>

            <div class="px-4 py-2 bg-yellow-50 rounded-lg">
              <div class="text-sm text-gray-500">Client Errors</div>
              <div class="text-lg font-semibold text-yellow-600">
                {{ logStats.clientErrors }}
              </div>
            </div>

            <div class="px-4 py-2 bg-red-50 rounded-lg">
              <div class="text-sm text-gray-500">Server Errors</div>
              <div class="text-lg font-semibold text-red-600">
                {{ logStats.serverErrors }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredUsageLogs.length === 0" class="p-8 text-center">
        <div
          class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          No usage logs found
        </h3>
        <p class="text-gray-600 mb-6">
          {{
            searchQuery
              ? "No logs match your search criteria."
              : "There are no API usage logs for the selected period."
          }}
        </p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Timestamp
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Endpoint
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Key Owner
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Response Time
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                IP Address
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Error
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="log in filteredUsageLogs"
              :key="log.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ formatDateTime(log.timestamp) }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <span
                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase mr-2"
                    :class="{
                      'bg-green-100 text-green-800': log.method === 'GET',
                      'bg-blue-100 text-blue-800': log.method === 'POST',
                      'bg-yellow-100 text-yellow-800': log.method === 'PUT',
                      'bg-red-100 text-red-800': log.method === 'DELETE',
                      'bg-purple-100 text-purple-800': log.method === 'PATCH',
                    }"
                  >
                    {{ log.method }}
                  </span>
                  <span class="text-sm text-gray-900 font-mono">{{
                    log.endpoint
                  }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ log.keyOwner }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="getLogStatusBadgeClass(log.status)"
                >
                  {{ log.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ log.responseTime }}ms
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 font-mono">
                  {{ log.ipAddress }}
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-red-600">
                  {{ log.errorMessage || "-" }}
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create New API Key Modal -->
    <div
      v-if="showNewKeyForm"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-3xl w-full p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Create New API Key</h3>
          <button
            @click="showNewKeyForm = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <svg
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-4">
          <div>
            <label
              for="key-name"
              class="block text-sm font-medium text-gray-700"
              >Key Name</label
            >
            <input
              id="key-name"
              v-model="newKey.name"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="e.g., Production API Key"
            />
            <p class="mt-1 text-sm text-gray-500">
              A descriptive name to identify this API key
            </p>
          </div>

          <div>
            <label
              for="key-owner"
              class="block text-sm font-medium text-gray-700"
              >Key Owner</label
            >
            <select
              id="key-owner"
              v-model="newKey.owner.id"
              @change="onOwnerSelect(newKey.owner.id)"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option value="0" disabled>Select owner</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
            <p class="mt-1 text-sm text-gray-500">
              The user who will be responsible for this API key
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700"
              >Scopes</label
            >
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
              <div
                v-for="scope in availableScopes"
                :key="scope.id"
                class="flex items-start"
              >
                <div class="flex items-center h-5">
                  <input
                    :id="`scope-${scope.id}`"
                    v-model="newKey.scopes"
                    :value="scope.id"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  />
                </div>
                <div class="ml-3 text-sm">
                  <label
                    :for="`scope-${scope.id}`"
                    class="font-medium text-gray-700"
                    >{{ scope.name }}</label
                  >
                  <p class="text-gray-500">{{ scope.description }}</p>
                </div>
              </div>
            </div>
            <p class="mt-1 text-sm text-gray-500">
              Select the permissions this API key will have
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label
                for="key-expires"
                class="block text-sm font-medium text-gray-700"
                >Expires In</label
              >
              <div class="mt-1 flex">
                <select
                  id="key-expires"
                  v-model="newKey.expiresIn"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="7">7 days</option>
                  <option value="30">30 days</option>
                  <option value="90">90 days</option>
                  <option value="180">180 days</option>
                  <option value="365">365 days</option>
                  <option value="never">Never</option>
                </select>
              </div>
              <p class="mt-1 text-sm text-gray-500">
                When this API key will expire
              </p>
            </div>

            <div>
              <label
                for="key-limit"
                class="block text-sm font-medium text-gray-700"
                >Usage Limit</label
              >
              <input
                id="key-limit"
                v-model.number="newKey.usageLimit"
                type="number"
                min="1"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
              <p class="mt-1 text-sm text-gray-500">
                Maximum number of requests allowed
              </p>
            </div>
          </div>

          <div>
            <label for="key-ip" class="block text-sm font-medium text-gray-700"
              >IP Restrictions</label
            >
            <input
              id="key-ip"
              v-model="newKey.ipRestrictions"
              type="text"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="e.g., 192.168.1.1, 10.0.0.1/24"
            />
            <p class="mt-1 text-sm text-gray-500">
              Optional comma-separated list of IP addresses or CIDR ranges
            </p>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            @click="showNewKeyForm = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            @click="createApiKey"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Create API Key
          </button>
        </div>
      </div>
    </div>

    <!-- API Key Details Modal -->
    <div
      v-if="showKeyDetails && selectedKey"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-3xl w-full p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">API Key Details</h3>
          <button
            @click="showKeyDetails = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <svg
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <div class="mb-6">
          <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500">API Key</div>
              <button
                @click="copyApiKey(selectedKey.key)"
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
              >
                Copy
              </button>
            </div>
            <div
              class="mt-1 text-lg font-mono bg-gray-100 p-2 rounded break-all"
            >
              {{ selectedKey.key }}
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500">Key Details</h4>
              <dl class="mt-2 space-y-1">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Name:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ selectedKey.name }}
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Status:</dt>
                  <dd class="text-sm sm:col-span-2">
                    <span
                      class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="getStatusBadgeClass(selectedKey.status)"
                    >
                      {{ selectedKey.status }}
                    </span>
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Created:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ formatDateTime(selectedKey.created) }}
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Expires:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{
                      selectedKey.expires
                        ? formatDateTime(selectedKey.expires)
                        : "Never expires"
                    }}
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Last Used:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{
                      selectedKey.lastUsed
                        ? formatDateTime(selectedKey.lastUsed)
                        : "Never used"
                    }}
                  </dd>
                </div>
              </dl>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500">
                Owner Information
              </h4>
              <dl class="mt-2 space-y-1">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Name:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ selectedKey.owner.name }}
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Email:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ selectedKey.owner.email }}
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Role:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2 capitalize">
                    {{ selectedKey.owner.role }}
                  </dd>
                </div>
              </dl>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500">
                Usage Information
              </h4>
              <dl class="mt-2 space-y-1">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Usage:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    {{ selectedKey.usageCount.toLocaleString() }} /
                    {{ selectedKey.usageLimit.toLocaleString() }}
                  </dd>
                </div>
                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                  <dt class="text-sm font-medium text-gray-500">Progress:</dt>
                  <dd class="text-sm text-gray-900 sm:col-span-2">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                      <div
                        class="h-2.5 rounded-full transition-all duration-500"
                        :class="
                          getUsageColor(
                            getUsagePercentage(
                              selectedKey.usageCount,
                              selectedKey.usageLimit
                            )
                          )
                        "
                        :style="{
                          width: `${getUsagePercentage(
                            selectedKey.usageCount,
                            selectedKey.usageLimit
                          )}%`,
                        }"
                      ></div>
                    </div>
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- IP Restrictions -->
          <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500">IP Restrictions</h4>
            <div
              v-if="
                selectedKey.ipRestrictions &&
                selectedKey.ipRestrictions.length > 0
              "
              class="mt-2"
            >
              <ul class="space-y-1">
                <li
                  v-for="(ip, index) in selectedKey.ipRestrictions"
                  :key="`ip-${index}`"
                  class="text-sm text-gray-900 font-mono"
                >
                  {{ ip }}
                </li>
              </ul>
            </div>
            <div v-else class="mt-2 text-sm text-gray-600">
              No IP restrictions. This key can be used from any IP address.
            </div>
          </div>

          <!-- Scopes -->
          <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500">API Scopes</h4>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="scope in selectedKey.scopes"
                :key="scope"
                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="getScopeBadgeClass(scope)"
              >
                {{ scope }}
              </span>
            </div>
            <div class="mt-2 text-sm text-gray-700">
              <div
                v-for="scope in selectedKey.scopes"
                :key="`desc-${scope}`"
                class="mt-1"
              >
                <span class="font-medium">{{ scope }}:</span>
                {{ getScopeDescription(scope) }}
              </div>
            </div>
          </div>
        </div>

        <div
          class="flex justify-between space-x-3 pt-4 border-t border-gray-200"
        >
          <div>
            <button
              v-if="selectedKey.status === 'active'"
              @click="revokeApiKey(selectedKey)"
              class="px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-md shadow-sm hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
            >
              Revoke Key
            </button>
            <button
              @click="
                keyToDelete = selectedKey;
                showDeleteConfirm = true;
                showKeyDetails = false;
              "
              class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-200 rounded-md shadow-sm hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 ml-2"
            >
              Delete Key
            </button>
          </div>
          <button
            @click="showKeyDetails = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-red-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Delete API Key
          </h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete the API key "{{
              keyToDelete?.name
            }}"? This action cannot be undone.
          </p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            @click="showDeleteConfirm = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </button>
          <button
            @click="deleteApiKey"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          >
            Delete API Key
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
