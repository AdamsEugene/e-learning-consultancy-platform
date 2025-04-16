<!-- pages/dashboard/consultant/team/index.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Define page layout
definePageMeta({
  layout: "dashboard-consultant",
});

// Page meta
useHead({
  title: "Team Management - Consultant Dashboard",
});

// Define team member types and interfaces
type MemberRole =
  | "developer"
  | "designer"
  | "project_manager"
  | "qa_engineer"
  | "devops"
  | "marketing"
  | "content_writer";
type MemberStatus = "active" | "on_leave" | "busy" | "available";

interface TeamMember {
  id: number;
  name: string;
  avatar: string;
  role: MemberRole;
  title: string;
  email: string;
  phone?: string;
  status: MemberStatus;
  skills: string[];
  bio?: string;
  dateJoined: string;
  currentProjects: number[];
  rating: number;
  completedProjects: number;
  availability: number; // percentage of time available
}

interface Project {
  id: number;
  name: string;
  client: string;
  startDate: string;
  endDate?: string;
  status: "active" | "completed" | "on_hold" | "planning";
}

// State
const teamMembers = ref<TeamMember[]>([]);
const projects = ref<Project[]>([]);
const isLoading = ref(true);
const searchQuery = ref("");
const activeFilter = ref<MemberStatus | "all">("all");
const roleFilter = ref<MemberRole | "all">("all");
const sortBy = ref("name");
const sortDirection = ref<"asc" | "desc">("asc");
const selectedMember = ref<TeamMember | null>(null);
const showMemberDetailModal = ref(false);
const showAddMemberModal = ref(false);
const showInviteModal = ref(false);
const inviteEmail = ref("");
const inviteRole = ref<MemberRole>("developer");
const isInviteSending = ref(false);

// New member form data
const newMember = ref({
  name: "",
  email: "",
  role: "developer" as MemberRole,
  title: "",
  skills: [] as string[],
});

// Available skills for the form
const availableSkills = [
  "JavaScript",
  "TypeScript",
  "Vue.js",
  "React",
  "Node.js",
  "Express",
  "HTML/CSS",
  "UI Design",
  "UX Research",
  "Figma",
  "Adobe XD",
  "Project Management",
  "Agile",
  "Scrum",
  "DevOps",
  "AWS",
  "Docker",
  "Kubernetes",
  "CI/CD",
  "Testing",
  "Python",
  "Content Strategy",
  "Copywriting",
  "SEO",
  "Social Media",
];

// Mock data generation
const generateMockTeamMembers = (): TeamMember[] => {
  return [
    {
      id: 1,
      name: "Alex Johnson",
      avatar: "/api/placeholder/100/100",
      role: "developer",
      title: "Senior Frontend Developer",
      email: "alex.johnson@example.com",
      phone: "+1 (555) 123-4567",
      status: "active",
      skills: ["JavaScript", "TypeScript", "Vue.js", "React", "HTML/CSS"],
      bio: "Experienced frontend developer with 8+ years of experience building responsive web applications.",
      dateJoined: "2023-05-15",
      currentProjects: [1, 3],
      rating: 4.8,
      completedProjects: 24,
      availability: 20,
    },
    {
      id: 2,
      name: "Samantha Lee",
      avatar: "/api/placeholder/100/100",
      role: "designer",
      title: "UX/UI Designer",
      email: "samantha.lee@example.com",
      phone: "+1 (555) 987-6543",
      status: "active",
      skills: ["UI Design", "UX Research", "Figma", "Adobe XD", "Wireframing"],
      bio: "Creative designer focused on creating intuitive and engaging user experiences.",
      dateJoined: "2023-08-10",
      currentProjects: [1, 2],
      rating: 4.9,
      completedProjects: 18,
      availability: 10,
    },
    {
      id: 3,
      name: "Michael Chen",
      avatar: "/api/placeholder/100/100",
      role: "project_manager",
      title: "Senior Project Manager",
      email: "michael.chen@example.com",
      status: "available",
      skills: [
        "Project Management",
        "Agile",
        "Scrum",
        "Client Communication",
        "Budgeting",
      ],
      dateJoined: "2023-04-02",
      currentProjects: [2],
      rating: 4.7,
      completedProjects: 31,
      availability: 60,
    },
    {
      id: 4,
      name: "Jessica Williams",
      avatar: "/api/placeholder/100/100",
      role: "developer",
      title: "Backend Developer",
      email: "jessica.williams@example.com",
      status: "busy",
      skills: ["Node.js", "Express", "MongoDB", "SQL", "REST APIs"],
      dateJoined: "2024-01-15",
      currentProjects: [3, 4],
      rating: 4.6,
      completedProjects: 12,
      availability: 0,
    },
    {
      id: 5,
      name: "David Rodriguez",
      avatar: "/api/placeholder/100/100",
      role: "qa_engineer",
      title: "QA Engineer",
      email: "david.rodriguez@example.com",
      phone: "+1 (555) 234-5678",
      status: "on_leave",
      skills: [
        "Testing",
        "Automation",
        "Test Planning",
        "Bug Tracking",
        "Quality Assurance",
      ],
      bio: "Detail-oriented QA engineer with a passion for delivering high-quality software products.",
      dateJoined: "2023-11-05",
      currentProjects: [],
      rating: 4.5,
      completedProjects: 16,
      availability: 0,
    },
    {
      id: 6,
      name: "Emily Taylor",
      avatar: "/api/placeholder/100/100",
      role: "content_writer",
      title: "Content Strategist",
      email: "emily.taylor@example.com",
      status: "active",
      skills: [
        "Content Strategy",
        "Copywriting",
        "SEO",
        "Content Marketing",
        "Social Media",
      ],
      dateJoined: "2023-09-20",
      currentProjects: [5],
      rating: 4.8,
      completedProjects: 22,
      availability: 30,
    },
    {
      id: 7,
      name: "Thomas Wilson",
      avatar: "/api/placeholder/100/100",
      role: "devops",
      title: "DevOps Engineer",
      email: "thomas.wilson@example.com",
      status: "active",
      skills: ["DevOps", "AWS", "Docker", "Kubernetes", "CI/CD"],
      dateJoined: "2023-06-12",
      currentProjects: [2, 4],
      rating: 4.7,
      completedProjects: 14,
      availability: 25,
    },
  ];
};

const generateMockProjects = (): Project[] => {
  return [
    {
      id: 1,
      name: "E-commerce Website Redesign",
      client: "Fashion Forward Inc.",
      startDate: "2025-01-15",
      status: "active",
    },
    {
      id: 2,
      name: "Mobile Banking App",
      client: "Secure Financial Services",
      startDate: "2025-02-10",
      status: "active",
    },
    {
      id: 3,
      name: "CRM Implementation",
      client: "Global Tech Solutions",
      startDate: "2025-03-01",
      status: "active",
    },
    {
      id: 4,
      name: "Cloud Migration Strategy",
      client: "Manufacturing Excellence",
      startDate: "2025-03-20",
      status: "planning",
    },
    {
      id: 5,
      name: "Marketing Campaign Management",
      client: "Retail Chain Stores",
      startDate: "2025-02-15",
      status: "active",
    },
  ];
};

// Fetch team members (mock data)
const fetchTeamData = async () => {
  try {
    isLoading.value = true;
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800));

    teamMembers.value = generateMockTeamMembers();
    projects.value = generateMockProjects();
  } catch (error) {
    console.error("Error fetching team data:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filter team members based on search query and filters
const filteredTeamMembers = computed(() => {
  let result = [...teamMembers.value];

  // Apply status filter
  if (activeFilter.value !== "all") {
    result = result.filter((member) => member.status === activeFilter.value);
  }

  // Apply role filter
  if (roleFilter.value !== "all") {
    result = result.filter((member) => member.role === roleFilter.value);
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (member) =>
        member.name.toLowerCase().includes(query) ||
        member.title.toLowerCase().includes(query) ||
        member.email.toLowerCase().includes(query) ||
        member.skills.some((skill) => skill.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  result.sort((a, b) => {
    let comparison = 0;

    if (sortBy.value === "name") {
      comparison = a.name.localeCompare(b.name);
    } else if (sortBy.value === "role") {
      comparison = a.role.localeCompare(b.role);
    } else if (sortBy.value === "rating") {
      comparison = b.rating - a.rating;
    } else if (sortBy.value === "availability") {
      comparison = b.availability - a.availability;
    } else if (sortBy.value === "projects") {
      comparison = b.currentProjects.length - a.currentProjects.length;
    }

    return sortDirection.value === "asc" ? comparison : -comparison;
  });

  return result;
});

// Team statistics
const teamStats = computed(() => {
  const total = teamMembers.value.length;
  const active = teamMembers.value.filter((m) => m.status === "active").length;
  const available = teamMembers.value.filter(
    (m) => m.status === "available"
  ).length;
  const onLeave = teamMembers.value.filter(
    (m) => m.status === "on_leave"
  ).length;
  const busy = teamMembers.value.filter((m) => m.status === "busy").length;

  const avgRating =
    teamMembers.value.reduce((sum, member) => sum + member.rating, 0) / total;
  const totalCompleted = teamMembers.value.reduce(
    (sum, member) => sum + member.completedProjects,
    0
  );

  return {
    total,
    active,
    available,
    onLeave,
    busy,
    avgRating: avgRating.toFixed(1),
    totalCompleted,
    activeProjects: projects.value.filter((p) => p.status === "active").length,
  };
});

// Format date to readable string
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Get member status badge classes
const getMemberStatusClasses = (status: MemberStatus) => {
  switch (status) {
    case "active":
      return "bg-green-100 text-green-800";
    case "available":
      return "bg-blue-100 text-blue-800";
    case "busy":
      return "bg-red-100 text-red-800";
    case "on_leave":
      return "bg-amber-100 text-amber-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

// Format member role for display
const formatMemberRole = (role: MemberRole) => {
  return role.replace("_", " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

// Get member role icon
const getMemberRoleIcon = (role: MemberRole) => {
  switch (role) {
    case "developer":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>`;
    case "designer":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd" />
      </svg>`;
    case "project_manager":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" />
        <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z" />
        <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" />
      </svg>`;
    case "qa_engineer":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>`;
    case "devops":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
      </svg>`;
    case "marketing":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" />
      </svg>`;
    case "content_writer":
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
      </svg>`;
    default:
      return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
      </svg>`;
  }
};

// View team member details
const viewMemberDetails = (member: TeamMember) => {
  selectedMember.value = member;
  showMemberDetailModal.value = true;
};

// Get projects for a specific team member
const getMemberProjects = (memberId: number) => {
  const projectIds =
    teamMembers.value.find((m) => m.id === memberId)?.currentProjects || [];
  return projects.value.filter((project) => projectIds.includes(project.id));
};

// Add new team member
const addNewMember = () => {
  // Validate form data
  if (
    !newMember.value.name ||
    !newMember.value.email ||
    !newMember.value.title
  ) {
    // Show validation error
    return;
  }

  // In a real app, this would make an API call
  const newId = Math.max(...teamMembers.value.map((m) => m.id)) + 1;

  // Create new team member
  const member: TeamMember = {
    id: newId,
    name: newMember.value.name,
    avatar: "/api/placeholder/100/100", // Default avatar
    role: newMember.value.role,
    title: newMember.value.title,
    email: newMember.value.email,
    status: "active",
    skills: newMember.value.skills,
    dateJoined: new Date().toISOString().split("T")[0],
    currentProjects: [],
    rating: 0,
    completedProjects: 0,
    availability: 100,
  };

  teamMembers.value.push(member);

  // Reset form and close modal
  newMember.value = {
    name: "",
    email: "",
    role: "developer",
    title: "",
    skills: [],
  };

  showAddMemberModal.value = false;
};

// Send invitation to new team member
const sendInvitation = () => {
  if (!inviteEmail.value || !inviteRole) {
    return;
  }

  isInviteSending.value = true;

  // Simulate API call delay
  setTimeout(() => {
    isInviteSending.value = false;
    inviteEmail.value = "";
    showInviteModal.value = false;

    // Show success message
    alert("Invitation sent successfully!");
  }, 1500);
};

// Toggle sort direction
const toggleSort = (column: string) => {
  if (sortBy.value === column) {
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    sortBy.value = column;
    sortDirection.value = "asc";
  }
};

// Load data when component mounts
onMounted(() => {
  fetchTeamData();
});
</script>

<template>
  <div>
    <!-- Page Header with Stats -->
    <div class="mb-8">
      <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
      >
        <div>
          <h1 class="text-2xl font-bold text-gray-900 mb-1">Team Management</h1>
          <p class="text-gray-600">
            Manage your team members, track workload, and monitor availability
          </p>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
          <button
            @click="showInviteModal = true"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
              />
              <path
                d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
              />
            </svg>
            Invite Team Member
          </button>
          <button
            @click="showAddMemberModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 mr-2"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
              />
            </svg>
            Add Team Member
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div
              class="flex items-center justify-center rounded-full w-12 h-12 bg-indigo-100 text-indigo-600 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Team Size</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ teamStats.total }}
              </p>
              <p class="text-xs text-gray-500">
                {{ teamStats.active }} active members
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div
              class="flex items-center justify-center rounded-full w-12 h-12 bg-green-100 text-green-600 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Available Members</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ teamStats.available }}
              </p>
              <p class="text-xs text-gray-500">
                {{ teamStats.busy }} busy, {{ teamStats.onLeave }} on leave
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div
              class="flex items-center justify-center rounded-full w-12 h-12 bg-blue-100 text-blue-600 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Active Projects</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ teamStats.activeProjects }}
              </p>
              <p class="text-xs text-gray-500">
                {{ teamStats.totalCompleted }} projects completed
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
          <div class="flex items-center">
            <div
              class="flex items-center justify-center rounded-full w-12 h-12 bg-amber-100 text-amber-600 mr-4"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Average Rating</p>
              <p class="text-2xl font-bold text-gray-900">
                {{ teamStats.avgRating }}/5.0
              </p>
              <p class="text-xs text-gray-500">Based on client feedback</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and controls -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-200">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0"
      >
        <!-- Status Filters -->
        <div class="flex space-x-1 overflow-x-auto">
          <button
            @click="activeFilter = 'all'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeFilter === 'all'
                ? 'bg-indigo-100 text-indigo-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            All
          </button>
          <button
            @click="activeFilter = 'active'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeFilter === 'active'
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Active
          </button>
          <button
            @click="activeFilter = 'available'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeFilter === 'available'
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Available
          </button>
          <button
            @click="activeFilter = 'busy'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeFilter === 'busy'
                ? 'bg-red-100 text-red-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            Busy
          </button>
          <button
            @click="activeFilter = 'on_leave'"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
            :class="
              activeFilter === 'on_leave'
                ? 'bg-amber-100 text-amber-700'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            On Leave
          </button>
        </div>

        <!-- Search input -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search team members..."
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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

        <!-- Role Filter -->
        <div class="relative">
          <select
            v-model="roleFilter"
            class="pl-4 pr-10 py-2 bg-white border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
            <option value="all">All Roles</option>
            <option value="developer">Developer</option>
            <option value="designer">Designer</option>
            <option value="project_manager">Project Manager</option>
            <option value="qa_engineer">QA Engineer</option>
            <option value="devops">DevOps Engineer</option>
            <option value="content_writer">Content Writer</option>
            <option value="marketing">Marketing</option>
          </select>
          <div
            class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
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
      <div
        v-for="i in 3"
        :key="`skeleton-${i}`"
        class="animate-pulse p-4 border-b border-gray-200"
      >
        <div class="flex items-center space-x-4">
          <div class="rounded-full bg-gray-200 h-12 w-12"></div>
          <div class="flex-1">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
          </div>
          <div class="flex space-x-2">
            <div class="h-8 w-20 bg-gray-200 rounded-md"></div>
            <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Team Members List -->
    <div v-else-if="filteredTeamMembers.length > 0">
      <!-- Table Header -->
      <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div
                    class="flex items-center cursor-pointer"
                    @click="toggleSort('name')"
                  >
                    <span>Member</span>
                    <svg
                      v-if="sortBy === 'name'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="ml-1 h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        v-if="sortDirection === 'asc'"
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div
                    class="flex items-center cursor-pointer"
                    @click="toggleSort('role')"
                  >
                    <span>Role</span>
                    <svg
                      v-if="sortBy === 'role'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="ml-1 h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        v-if="sortDirection === 'asc'"
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div
                    class="flex items-center cursor-pointer"
                    @click="toggleSort('availability')"
                  >
                    <span>Availability</span>
                    <svg
                      v-if="sortBy === 'availability'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="ml-1 h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        v-if="sortDirection === 'asc'"
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div
                    class="flex items-center cursor-pointer"
                    @click="toggleSort('projects')"
                  >
                    <span>Current Projects</span>
                    <svg
                      v-if="sortBy === 'projects'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="ml-1 h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        v-if="sortDirection === 'asc'"
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  <div
                    class="flex items-center cursor-pointer"
                    @click="toggleSort('rating')"
                  >
                    <span>Rating</span>
                    <svg
                      v-if="sortBy === 'rating'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="ml-1 h-4 w-4"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        v-if="sortDirection === 'asc'"
                        fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                      />
                      <path
                        v-else
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </div>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="member in filteredTeamMembers"
                :key="member.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <!-- Member info -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img
                        :src="member.avatar"
                        :alt="member.name"
                        class="h-10 w-10 rounded-full object-cover"
                      />
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ member.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ member.email }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Role -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div
                      class="mr-2 text-gray-500"
                      v-html="getMemberRoleIcon(member.role)"
                    ></div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatMemberRole(member.role) }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ member.title }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Availability -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="w-32">
                    <div class="text-sm text-gray-900 mb-1">
                      {{ member.availability }}% Available
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full"
                        :class="{
                          'bg-green-500': member.availability > 60,
                          'bg-yellow-500':
                            member.availability > 30 &&
                            member.availability <= 60,
                          'bg-red-500': member.availability <= 30,
                        }"
                        :style="{ width: `${member.availability}%` }"
                      ></div>
                    </div>
                  </div>
                </td>

                <!-- Current Projects -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ member.currentProjects.length }} active project(s)
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ member.completedProjects }} completed
                  </div>
                </td>

                <!-- Rating -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-900 mr-1">
                      {{ member.rating.toFixed(1) }}
                    </div>
                    <div class="flex text-yellow-400">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </div>
                  </div>
                </td>

                <!-- Status Badge -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                    :class="getMemberStatusClasses(member.status)"
                  >
                    {{ member.status.replace("_", " ") }}
                  </span>
                </td>

                <!-- Actions -->
                <td
                  class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                >
                  <div class="flex justify-end space-x-2">
                    <button
                      @click="viewMemberDetails(member)"
                      class="text-indigo-600 hover:text-indigo-900 p-1"
                      title="View Profile"
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
                      class="text-gray-600 hover:text-indigo-900 p-1"
                      title="Edit Member"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        />
                      </svg>
                    </button>
                    <button
                      class="text-gray-600 hover:text-red-900 p-1"
                      title="Remove Member"
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
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="bg-white rounded-lg shadow p-8 text-center border border-gray-200"
    >
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
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">
        No team members found
      </h3>
      <p class="text-gray-600 mb-6">
        {{
          searchQuery || activeFilter !== "all"
            ? "No team members match your search criteria."
            : "You haven't added any team members yet."
        }}
      </p>
      <button
        @click="showAddMemberModal = true"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
          />
        </svg>
        Add Your First Team Member
      </button>
    </div>

    <!-- Pagination (simplified) -->
    <div v-if="filteredTeamMembers.length > 5" class="mt-6 flex justify-center">
      <div
        class="bg-white px-4 py-3 flex items-center justify-between border border-gray-200 rounded-lg shadow sm:px-6"
      >
        <div class="flex-1 flex justify-between sm:hidden">
          <a
            href="#"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </a>
          <a
            href="#"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </a>
        </div>
        <div
          class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
        >
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">1</span> to
              <span class="font-medium">{{
                Math.min(5, filteredTeamMembers.length)
              }}</span>
              of
              <span class="font-medium">{{ filteredTeamMembers.length }}</span>
              results
            </p>
          </div>
          <div>
            <nav
              class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
              aria-label="Pagination"
            >
              <a
                href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                1
              </a>
              <span
                class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600"
              >
                2
              </span>
              <a
                href="#"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                3
              </a>
              <a
                href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Member Detail Modal -->
    <div
      v-if="showMemberDetailModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-xl transform transition-all"
      >
        <div class="relative">
          <!-- Header -->
          <div
            class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10"
          >
            <div class="flex justify-between items-start">
              <h3 class="text-lg font-bold text-gray-900 flex items-center">
                Team Member Profile
                <span
                  class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="
                    selectedMember
                      ? getMemberStatusClasses(selectedMember.status)
                      : ''
                  "
                >
                  {{ selectedMember?.status.replace("_", " ") }}
                </span>
              </h3>
              <button
                @click="showMemberDetailModal = false"
                class="text-gray-400 hover:text-gray-500"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
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
          </div>

          <!-- Content -->
          <div class="p-6">
            <div class="flex flex-col md:flex-row md:space-x-6">
              <!-- Left column - Member info -->
              <div class="md:w-1/3 mb-6 md:mb-0">
                <div class="text-center mb-6">
                  <img
                    :src="selectedMember?.avatar"
                    :alt="selectedMember?.name"
                    class="h-32 w-32 rounded-full mx-auto object-cover border-4"
                    :class="{
                      'border-green-200':
                        selectedMember?.status === 'active' ||
                        selectedMember?.status === 'available',
                      'border-red-200': selectedMember?.status === 'busy',
                      'border-amber-200': selectedMember?.status === 'on_leave',
                    }"
                  />
                  <h4 class="text-xl font-medium text-gray-900 mt-4">
                    {{ selectedMember?.name }}
                  </h4>
                  <p class="text-gray-500">{{ selectedMember?.title }}</p>

                  <div
                    class="flex items-center justify-center mt-2 text-yellow-400"
                  >
                    <div v-for="i in 5" :key="i" class="flex">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        :class="
                          i <= Math.round(selectedMember?.rating || 0)
                            ? 'text-yellow-400'
                            : 'text-gray-300'
                        "
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                      </svg>
                    </div>
                    <span class="ml-1 text-sm text-gray-600"
                      >{{ selectedMember?.rating.toFixed(1) }}/5.0</span
                    >
                  </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Contact Information
                  </h5>
                  <div class="space-y-2">
                    <div class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400 mr-3"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                        />
                        <path
                          d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                        />
                      </svg>
                      <a
                        href="#"
                        class="text-indigo-600 hover:text-indigo-800"
                        >{{ selectedMember?.email }}</a
                      >
                    </div>
                    <div class="flex items-center" v-if="selectedMember?.phone">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400 mr-3"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                        />
                      </svg>
                      <span class="text-gray-700">{{
                        selectedMember?.phone
                      }}</span>
                    </div>
                    <div class="flex items-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400 mr-3"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      <span class="text-gray-700"
                        >Joined
                        {{ formatDate(selectedMember?.dateJoined || "") }}</span
                      >
                    </div>
                  </div>
                </div>

                <!-- Availability -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Availability
                  </h5>
                  <div class="flex items-center mb-2">
                    <div class="text-lg font-medium text-gray-900">
                      {{ selectedMember?.availability }}%
                    </div>
                    <div class="text-sm text-gray-500 ml-2">available time</div>
                  </div>
                  <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div
                      class="h-full rounded-full"
                      :class="{
                        'bg-green-500': selectedMember?.availability! > 60,
                        'bg-yellow-500': selectedMember?.availability! > 30 && selectedMember?.availability! <= 60,
                        'bg-red-500': selectedMember?.availability! <= 30
                      }"
                      :style="{ width: `${selectedMember?.availability}%` }"
                    ></div>
                  </div>
                </div>

                <!-- Role Info -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Role Information
                  </h5>
                  <div class="flex items-center mb-3">
                    <div
                      class="text-gray-500 mr-2"
                      v-html="
                        selectedMember
                          ? getMemberRoleIcon(selectedMember.role)
                          : ''
                      "
                    ></div>
                    <div class="text-lg font-medium text-gray-900">
                      {{
                        selectedMember
                          ? formatMemberRole(selectedMember.role)
                          : ""
                      }}
                    </div>
                  </div>
                  <div class="text-gray-700">{{ selectedMember?.title }}</div>
                </div>

                <!-- Actions -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Actions
                  </h5>
                  <div class="flex flex-wrap gap-2">
                    <button
                      class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1.5 text-gray-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                        />
                      </svg>
                      Edit
                    </button>
                    <button
                      class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1.5 text-gray-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Schedule
                    </button>
                    <button
                      class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 mr-1.5 text-gray-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                          clip-rule="evenodd"
                        />
                      </svg>
                      Message
                    </button>
                  </div>
                </div>
              </div>

              <!-- Right column - Projects, Skills, Bio -->
              <div class="md:w-2/3">
                <!-- Projects -->
                <div class="mb-6">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Current Projects
                  </h5>
                  <div
                    v-if="
                      selectedMember &&
                      getMemberProjects(selectedMember.id).length === 0
                    "
                    class="text-gray-500 italic"
                  >
                    Not assigned to any active projects.
                  </div>
                  <div v-else class="space-y-3">
                    <div
                      v-for="project in getMemberProjects(
                        selectedMember?.id || 0
                      )"
                      :key="project.id"
                      class="bg-white border border-gray-200 rounded-md p-4 hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors"
                    >
                      <div class="flex justify-between items-start">
                        <div>
                          <h6 class="text-base font-medium text-gray-900">
                            {{ project.name }}
                          </h6>
                          <div class="text-sm text-gray-500">
                            {{ project.client }}
                          </div>
                          <div class="text-xs text-gray-500 mt-1">
                            Started {{ formatDate(project.startDate) }}
                          </div>
                        </div>
                        <span
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                          :class="{
                            'bg-green-100 text-green-800':
                              project.status === 'active',
                            'bg-yellow-100 text-yellow-800':
                              project.status === 'planning',
                            'bg-blue-100 text-blue-800':
                              project.status === 'completed',
                            'bg-red-100 text-red-800':
                              project.status === 'on_hold',
                          }"
                        >
                          {{ project.status }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Skills -->
                <div class="mb-6">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Skills
                  </h5>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(skill, index) in selectedMember?.skills"
                      :key="index"
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
                    >
                      {{ skill }}
                    </span>
                  </div>
                </div>

                <!-- Bio -->
                <div v-if="selectedMember?.bio" class="mb-6">
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Biography
                  </h5>
                  <p class="text-gray-700">{{ selectedMember.bio }}</p>
                </div>

                <!-- Completed Projects -->
                <div>
                  <h5
                    class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3"
                  >
                    Project History
                  </h5>
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <span class="text-lg font-medium text-gray-900">{{
                        selectedMember?.completedProjects
                      }}</span>
                      <span class="text-sm text-gray-500 ml-1"
                        >completed projects</span
                      >
                    </div>
                    <button
                      class="text-sm text-indigo-600 hover:text-indigo-800"
                    >
                      View All
                    </button>
                  </div>

                  <!-- Chart placeholder -->
                  <div
                    class="bg-gray-100 rounded-lg h-40 flex items-center justify-center"
                  >
                    <p class="text-gray-500">
                      Project history chart would appear here
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Member Modal -->
    <div
      v-if="showAddMemberModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full shadow-xl transform transition-all"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Add Team Member</h3>
            <button
              @click="showAddMemberModal = false"
              class="text-gray-400 hover:text-gray-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
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
        </div>

        <div class="p-6">
          <form @submit.prevent="addNewMember">
            <div class="space-y-4">
              <!-- Name -->
              <div>
                <label
                  for="name"
                  class="block text-sm font-medium text-gray-700"
                  >Full Name</label
                >
                <input
                  type="text"
                  id="name"
                  v-model="newMember.name"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Jane Doe"
                  required
                />
              </div>

              <!-- Email -->
              <div>
                <label
                  for="email"
                  class="block text-sm font-medium text-gray-700"
                  >Email Address</label
                >
                <input
                  type="email"
                  id="email"
                  v-model="newMember.email"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="jane.doe@example.com"
                  required
                />
              </div>

              <!-- Role -->
              <div>
                <label
                  for="role"
                  class="block text-sm font-medium text-gray-700"
                  >Role</label
                >
                <select
                  id="role"
                  v-model="newMember.role"
                  class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="developer">Developer</option>
                  <option value="designer">Designer</option>
                  <option value="project_manager">Project Manager</option>
                  <option value="qa_engineer">QA Engineer</option>
                  <option value="devops">DevOps Engineer</option>
                  <option value="content_writer">Content Writer</option>
                  <option value="marketing">Marketing</option>
                </select>
              </div>

              <!-- Title -->
              <div>
                <label
                  for="title"
                  class="block text-sm font-medium text-gray-700"
                  >Job Title</label
                >
                <input
                  type="text"
                  id="title"
                  v-model="newMember.title"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="Senior Frontend Developer"
                  required
                />
              </div>

              <!-- Skills -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Skills</label
                >
                <div
                  class="border border-gray-300 rounded-md p-2 max-h-40 overflow-y-auto"
                >
                  <div class="grid grid-cols-2 gap-2">
                    <div
                      v-for="(skill, index) in availableSkills"
                      :key="index"
                      class="flex items-center"
                    >
                      <input
                        type="checkbox"
                        :id="`skill-${index}`"
                        :value="skill"
                        v-model="newMember.skills"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <label
                        :for="`skill-${index}`"
                        class="ml-2 block text-sm text-gray-700"
                      >
                        {{ skill }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
              <button
                type="button"
                @click="showAddMemberModal = false"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Add Member
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Invite Modal -->
    <div
      v-if="showInviteModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg max-w-md w-full shadow-xl transform transition-all"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Invite Team Member</h3>
            <button
              @click="showInviteModal = false"
              class="text-gray-400 hover:text-gray-500"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
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
        </div>

        <div class="p-6">
          <form @submit.prevent="sendInvitation">
            <div class="mb-6 text-center">
              <div
                class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-8 w-8 text-indigo-600"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"
                  />
                </svg>
              </div>
              <p class="text-gray-600 mb-1">
                Invite a team member to join your workspace.
              </p>
              <p class="text-sm text-gray-500">
                They will receive an email with instructions to join.
              </p>
            </div>

            <!-- Email -->
            <div class="space-y-4">
              <div>
                <label
                  for="inviteEmail"
                  class="block text-sm font-medium text-gray-700"
                  >Email Address</label
                >
                <input
                  type="email"
                  id="inviteEmail"
                  v-model="inviteEmail"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="colleague@example.com"
                  required
                />
              </div>

              <!-- Role -->
              <div>
                <label
                  for="inviteRole"
                  class="block text-sm font-medium text-gray-700"
                  >Role</label
                >
                <select
                  id="inviteRole"
                  v-model="inviteRole"
                  class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option value="developer">Developer</option>
                  <option value="designer">Designer</option>
                  <option value="project_manager">Project Manager</option>
                  <option value="qa_engineer">QA Engineer</option>
                  <option value="devops">DevOps Engineer</option>
                  <option value="content_writer">Content Writer</option>
                  <option value="marketing">Marketing</option>
                </select>
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
              <button
                type="button"
                @click="showInviteModal = false"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :disabled="isInviteSending"
              >
                <svg
                  v-if="isInviteSending"
                  class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                {{ isInviteSending ? "Sending..." : "Send Invitation" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 4px;
  height: 4px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #6366f1;
}
</style>
