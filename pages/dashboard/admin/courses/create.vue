<!-- pages/dashboard/admin/courses/create.vue -->
<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

// Set the layout to dashboard-admin
definePageMeta({
  layout: "dashboard-admin",
});

// Set page metadata
useHead({
  title: "Create New Course - Admin Dashboard",
  meta: [
    {
      name: "description",
      content: "Create and publish new courses on the LearnConsult platform",
    },
  ],
});

// Interfaces for course data
interface CourseSection {
  id: number;
  title: string;
  description: string;
  lessons: CourseLesson[];
}

interface CourseLesson {
  id: number;
  title: string;
  contentType: "video" | "article" | "quiz" | "assignment";
  duration?: number; // in minutes
  description?: string;
}

interface CourseInstructor {
  id: number;
  name: string;
  avatar: string;
  bio: string;
  expertise: string[];
}

interface CoursePrice {
  amount: number;
  currency: string;
  discountPercentage?: number;
}

// State for form data
const course = ref({
  title: "",
  subtitle: "",
  category: "",
  subcategory: "",
  level: "beginner",
  description: "",
  learningOutcomes: [""],
  prerequisites: [""],
  thumbnail: "",
  previewVideo: "",
  status: "draft",
  price: {
    amount: 0,
    currency: "USD",
    discountPercentage: 0,
  } as CoursePrice,
  estimatedDuration: 0, // in hours
  language: "English",
  tags: [""],
  isFeatured: false,
  hasCertificate: false,
  publishDate: "",
  requirements: [""],
  sections: [
    {
      id: 1,
      title: "Introduction",
      description: "An overview of the course",
      lessons: [
        {
          id: 1,
          title: "Welcome to the Course",
          contentType: "video",
          duration: 5,
          description: "Introduction to the course content and structure",
        },
      ],
    },
  ] as CourseSection[],
  selectedInstructors: [] as number[],
});

// List of available categories
const categories = ref([
  "Web Development",
  "Mobile Development",
  "Data Science",
  "Machine Learning",
  "DevOps",
  "Cloud Computing",
  "UX/UI Design",
  "Digital Marketing",
  "Business",
  "Finance",
  "Leadership",
  "Personal Development",
]);

// Subcategories based on selected category
const subcategories = computed(() => {
  switch (course.value.category) {
    case "Web Development":
      return [
        "HTML/CSS",
        "JavaScript",
        "React",
        "Vue.js",
        "Angular",
        "Node.js",
        "PHP",
        "Python",
        "Ruby on Rails",
      ];
    case "Mobile Development":
      return [
        "React Native",
        "Flutter",
        "iOS Development",
        "Android Development",
        "Xamarin",
        "Kotlin",
        "Swift",
      ];
    case "Data Science":
      return [
        "Python",
        "R Programming",
        "SQL",
        "Tableau",
        "Power BI",
        "Data Visualization",
        "Big Data",
        "Statistics",
      ];
    case "Machine Learning":
      return [
        "TensorFlow",
        "PyTorch",
        "NLP",
        "Computer Vision",
        "Deep Learning",
        "Reinforcement Learning",
      ];
    case "DevOps":
      return [
        "Docker",
        "Kubernetes",
        "CI/CD",
        "Jenkins",
        "Git",
        "Infrastructure as Code",
        "AWS DevOps",
      ];
    case "Cloud Computing":
      return [
        "AWS",
        "Azure",
        "Google Cloud",
        "Cloud Security",
        "Serverless",
        "Cloud Architecture",
      ];
    case "UX/UI Design":
      return [
        "Figma",
        "Adobe XD",
        "Sketch",
        "User Research",
        "Usability Testing",
        "Interaction Design",
        "Wireframing",
      ];
    case "Digital Marketing":
      return [
        "SEO",
        "Social Media Marketing",
        "Content Marketing",
        "Email Marketing",
        "Google Analytics",
        "PPC",
      ];
    default:
      return [];
  }
});

// All available instructors list (in a real app, this would come from an API)
const instructors = ref<CourseInstructor[]>([
  {
    id: 1,
    name: "Dr. Alex Thompson",
    avatar: "/api/placeholder/100/100",
    bio: "Senior Web Development Instructor with 8 years of teaching experience",
    expertise: ["Web Development", "JavaScript", "Vue.js"],
  },
  {
    id: 2,
    name: "Sarah Parker",
    avatar: "/api/placeholder/100/100",
    bio: "Full-Stack Development Consultant and Educator",
    expertise: ["Full-Stack Development", "React", "Node.js"],
  },
  {
    id: 3,
    name: "Michael Chen",
    avatar: "/api/placeholder/100/100",
    bio: "Data Scientist and Machine Learning Expert",
    expertise: ["Data Science", "Python", "TensorFlow"],
  },
  {
    id: 4,
    name: "Emily Rodriguez",
    avatar: "/api/placeholder/100/100",
    bio: "UX/UI Design Specialist with background in psychology",
    expertise: ["UX/UI Design", "User Research", "Figma"],
  },
]);

// State for form validation
const isSubmitting = ref(false);
const formErrors = ref<{ [key: string]: string }>({});
const formSuccess = ref(false);

// Filter instructors based on course category
const filteredInstructors = computed(() => {
  if (!course.value.category) return instructors.value;

  return instructors.value.filter(
    (instructor) =>
      instructor.expertise.includes(course.value.category) ||
      (course.value.subcategory &&
        instructor.expertise.includes(course.value.subcategory))
  );
});

// Methods for handling form arrays
const addLearningOutcome = () => {
  course.value.learningOutcomes.push("");
};

const removeLearningOutcome = (index: number) => {
  course.value.learningOutcomes.splice(index, 1);
};

const addPrerequisite = () => {
  course.value.prerequisites.push("");
};

const removePrerequisite = (index: number) => {
  course.value.prerequisites.splice(index, 1);
};

const addTag = () => {
  course.value.tags.push("");
};

const removeTag = (index: number) => {
  course.value.tags.splice(index, 1);
};

const addRequirement = () => {
  course.value.requirements.push("");
};

const removeRequirement = (index: number) => {
  course.value.requirements.splice(index, 1);
};

// Section and Lesson Management
const addSection = () => {
  const newId =
    course.value.sections.length > 0
      ? Math.max(...course.value.sections.map((s) => s.id)) + 1
      : 1;

  course.value.sections.push({
    id: newId,
    title: `Section ${newId}`,
    description: "",
    lessons: [],
  });
};

const removeSection = (index: number) => {
  course.value.sections.splice(index, 1);
};

const addLesson = (sectionIndex: number) => {
  const section = course.value.sections[sectionIndex];
  const newId =
    section.lessons.length > 0
      ? Math.max(...section.lessons.map((l) => l.id)) + 1
      : 1;

  section.lessons.push({
    id: newId,
    title: `Lesson ${newId}`,
    contentType: "video",
    duration: 0,
    description: "",
  });
};

const removeLesson = (sectionIndex: number, lessonIndex: number) => {
  course.value.sections[sectionIndex].lessons.splice(lessonIndex, 1);
};

// Toggle instructor selection
const toggleInstructor = (instructorId: number) => {
  const index = course.value.selectedInstructors.indexOf(instructorId);
  if (index === -1) {
    course.value.selectedInstructors.push(instructorId);
  } else {
    course.value.selectedInstructors.splice(index, 1);
  }
};

// Calculate estimated course duration based on lessons
const updateEstimatedDuration = () => {
  let totalMinutes = 0;
  course.value.sections.forEach((section) => {
    section.lessons.forEach((lesson) => {
      totalMinutes += lesson.duration || 0;
    });
  });

  course.value.estimatedDuration = Math.round((totalMinutes / 60) * 10) / 10; // Convert to hours with 1 decimal place
};

// Form validation
const validateForm = () => {
  const errors: { [key: string]: string } = {};

  if (!course.value.title.trim()) {
    errors.title = "Course title is required";
  }

  if (!course.value.category) {
    errors.category = "Category is required";
  }

  if (!course.value.description.trim()) {
    errors.description = "Description is required";
  }

  if (course.value.learningOutcomes.some((outcome) => !outcome.trim())) {
    errors.learningOutcomes = "All learning outcomes must be filled or removed";
  }

  if (course.value.prerequisites.some((prereq) => !prereq.trim())) {
    errors.prerequisites = "All prerequisites must be filled or removed";
  }

  if (course.value.tags.some((tag) => !tag.trim())) {
    errors.tags = "All tags must be filled or removed";
  }

  if (course.value.requirements.some((req) => !req.trim())) {
    errors.requirements = "All requirements must be filled or removed";
  }

  if (course.value.selectedInstructors.length === 0) {
    errors.instructors = "At least one instructor must be selected";
  }

  if (course.value.sections.some((section) => !section.title.trim())) {
    errors.sections = "All section titles are required";
  }

  if (
    course.value.sections.some((section) =>
      section.lessons.some((lesson) => !lesson.title.trim())
    )
  ) {
    errors.lessons = "All lesson titles are required";
  }

  return errors;
};

// Handle form submission
const submitForm = async () => {
  updateEstimatedDuration();

  const errors = validateForm();
  formErrors.value = errors;

  if (Object.keys(errors).length > 0) {
    // Scroll to the first error
    const firstError = document.querySelector(".error-message");
    if (firstError) {
      firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    }
    return;
  }

  isSubmitting.value = true;

  try {
    // In a real app, you would submit the data to an API
    await new Promise((resolve) => setTimeout(resolve, 1000)); // Simulate API call

    formSuccess.value = true;

    // Reset form or redirect
    setTimeout(() => {
      // In a real app, you might redirect to the course list or view page
      formSuccess.value = false;
    }, 3000);
  } catch (error) {
    console.error("Error submitting form:", error);
  } finally {
    isSubmitting.value = false;
  }
};

// Initialize component
onMounted(() => {
  // Set default publish date to tomorrow
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  course.value.publishDate = tomorrow.toISOString().split("T")[0];
});
</script>

<template>
  <div>
    <!-- Page header -->
    <div class="mb-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Create New Course</h1>
          <p class="mt-1 text-sm text-gray-500">
            Add a new course to the platform
          </p>
        </div>
        <div class="mt-4 sm:mt-0">
          <div class="flex items-center space-x-3">
            <select
              v-model="course.status"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
              <option value="draft">Save as Draft</option>
              <option value="published">Publish Immediately</option>
              <option value="scheduled">Schedule Publication</option>
              <option value="review">Submit for Review</option>
            </select>
            <button
              @click="submitForm"
              class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting">
                <svg
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
                Saving...
              </span>
              <span v-else>Save Course</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success message -->
    <div
      v-if="formSuccess"
      class="rounded-md bg-green-50 p-4 mb-6 success-animation"
    >
      <div class="flex">
        <div class="flex-shrink-0">
          <svg
            class="h-5 w-5 text-green-400"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-green-800">
            Course saved successfully
          </h3>
          <div class="mt-2 text-sm text-green-700">
            <p>The course has been created and saved with the chosen status.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Form content with tabs -->
    <div class="bg-white shadow rounded-lg">
      <div class="p-6">
        <!-- Basic Information Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Basic Information
          </h2>
          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <!-- Course Title -->
            <div class="sm:col-span-4">
              <label for="title" class="block text-sm font-medium text-gray-700"
                >Course Title <span class="text-red-500">*</span></label
              >
              <div class="mt-1">
                <input
                  type="text"
                  id="title"
                  v-model="course.title"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="e.g. Complete Web Development Bootcamp"
                  :class="{ 'border-red-500': formErrors.title }"
                />
                <p
                  v-if="formErrors.title"
                  class="mt-1 text-sm text-red-600 error-message"
                >
                  {{ formErrors.title }}
                </p>
              </div>
            </div>

            <!-- Course Subtitle -->
            <div class="sm:col-span-6">
              <label
                for="subtitle"
                class="block text-sm font-medium text-gray-700"
                >Subtitle</label
              >
              <div class="mt-1">
                <input
                  type="text"
                  id="subtitle"
                  v-model="course.subtitle"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="e.g. Learn HTML, CSS, JavaScript, React, and Node.js from scratch"
                />
              </div>
            </div>

            <!-- Category and Subcategory -->
            <div class="sm:col-span-3">
              <label
                for="category"
                class="block text-sm font-medium text-gray-700"
                >Category <span class="text-red-500">*</span></label
              >
              <div class="mt-1">
                <select
                  id="category"
                  v-model="course.category"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  :class="{ 'border-red-500': formErrors.category }"
                >
                  <option value="" disabled>Select a category</option>
                  <option
                    v-for="(category, index) in categories"
                    :key="index"
                    :value="category"
                  >
                    {{ category }}
                  </option>
                </select>
                <p
                  v-if="formErrors.category"
                  class="mt-1 text-sm text-red-600 error-message"
                >
                  {{ formErrors.category }}
                </p>
              </div>
            </div>

            <div class="sm:col-span-3">
              <label
                for="subcategory"
                class="block text-sm font-medium text-gray-700"
                >Subcategory</label
              >
              <div class="mt-1">
                <select
                  id="subcategory"
                  v-model="course.subcategory"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  :disabled="!course.category"
                >
                  <option value="" disabled>Select a subcategory</option>
                  <option
                    v-for="(subcategory, index) in subcategories"
                    :key="index"
                    :value="subcategory"
                  >
                    {{ subcategory }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Course Level -->
            <div class="sm:col-span-3">
              <label for="level" class="block text-sm font-medium text-gray-700"
                >Course Level</label
              >
              <div class="mt-1">
                <select
                  id="level"
                  v-model="course.level"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                  <option value="all-levels">All Levels</option>
                </select>
              </div>
            </div>

            <!-- Course Language -->
            <div class="sm:col-span-3">
              <label
                for="language"
                class="block text-sm font-medium text-gray-700"
                >Course Language</label
              >
              <div class="mt-1">
                <select
                  id="language"
                  v-model="course.language"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="English">English</option>
                  <option value="Spanish">Spanish</option>
                  <option value="French">French</option>
                  <option value="German">German</option>
                  <option value="Chinese">Chinese</option>
                  <option value="Japanese">Japanese</option>
                  <option value="Arabic">Arabic</option>
                  <option value="Russian">Russian</option>
                  <option value="Portuguese">Portuguese</option>
                  <option value="Hindi">Hindi</option>
                </select>
              </div>
            </div>

            <!-- Course Description -->
            <div class="sm:col-span-6">
              <label
                for="description"
                class="block text-sm font-medium text-gray-700"
                >Course Description <span class="text-red-500">*</span></label
              >
              <div class="mt-1">
                <textarea
                  id="description"
                  v-model="course.description"
                  rows="4"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Provide a detailed description of your course"
                  :class="{ 'border-red-500': formErrors.description }"
                ></textarea>
                <p
                  v-if="formErrors.description"
                  class="mt-1 text-sm text-red-600 error-message"
                >
                  {{ formErrors.description }}
                </p>
              </div>
            </div>

            <!-- Course Thumbnail -->
            <div class="sm:col-span-6">
              <label
                for="thumbnail"
                class="block text-sm font-medium text-gray-700"
                >Course Thumbnail URL</label
              >
              <div class="mt-1">
                <input
                  type="text"
                  id="thumbnail"
                  v-model="course.thumbnail"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="https://example.com/images/course-thumbnail.jpg"
                />
              </div>
              <p class="mt-2 text-sm text-gray-500">
                Recommended size: 1280x720 pixels. In a production app, there
                would be a file uploader here.
              </p>
            </div>

            <!-- Course Preview Video -->
            <div class="sm:col-span-6">
              <label
                for="previewVideo"
                class="block text-sm font-medium text-gray-700"
                >Course Preview Video URL</label
              >
              <div class="mt-1">
                <input
                  type="text"
                  id="previewVideo"
                  v-model="course.previewVideo"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="https://example.com/videos/course-preview.mp4"
                />
              </div>
              <p class="mt-2 text-sm text-gray-500">
                Provide a URL to your course preview video. In a production app,
                there would be a video uploader here.
              </p>
            </div>
          </div>
        </div>

        <!-- Learning Outcomes Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Learning Outcomes
          </h2>
          <p class="text-sm text-gray-500 mb-4">
            What will students learn in this course? List specific skills and
            knowledge they will gain.
          </p>

          <div class="space-y-3">
            <div
              v-for="(outcome, index) in course.learningOutcomes"
              :key="`outcome-${index}`"
              class="flex items-center"
            >
              <div class="flex-grow">
                <div class="flex items-center">
                  <span class="text-gray-500 mr-2">{{ index + 1 }}.</span>
                  <input
                    type="text"
                    v-model="course.learningOutcomes[index]"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :placeholder="`e.g. Build responsive websites using HTML, CSS and JavaScript`"
                    :class="{ 'border-red-500': formErrors.learningOutcomes }"
                  />
                </div>
              </div>
              <button
                type="button"
                @click="removeLearningOutcome(index)"
                class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700"
                :disabled="course.learningOutcomes.length === 1"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
            <button
              type="button"
              @click="addLearningOutcome"
              class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
              Add Learning Outcome
            </button>
            <p
              v-if="formErrors.learningOutcomes"
              class="mt-1 text-sm text-red-600 error-message"
            >
              {{ formErrors.learningOutcomes }}
            </p>
          </div>
        </div>

        <!-- Prerequisites Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Prerequisites</h2>
          <p class="text-sm text-gray-500 mb-4">
            What should students already know or have before taking this course?
          </p>

          <div class="space-y-3">
            <div
              v-for="(prerequisite, index) in course.prerequisites"
              :key="`prerequisite-${index}`"
              class="flex items-center"
            >
              <div class="flex-grow">
                <div class="flex items-center">
                  <span class="text-gray-500 mr-2">{{ index + 1 }}.</span>
                  <input
                    type="text"
                    v-model="course.prerequisites[index]"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :placeholder="`e.g. Basic understanding of HTML and CSS`"
                    :class="{ 'border-red-500': formErrors.prerequisites }"
                  />
                </div>
              </div>
              <button
                type="button"
                @click="removePrerequisite(index)"
                class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700"
                :disabled="course.prerequisites.length === 1"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
            <button
              type="button"
              @click="addPrerequisite"
              class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
              Add Prerequisite
            </button>
            <p
              v-if="formErrors.prerequisites"
              class="mt-1 text-sm text-red-600 error-message"
            >
              {{ formErrors.prerequisites }}
            </p>
          </div>
        </div>

        <!-- Requirements Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Course Requirements
          </h2>
          <p class="text-sm text-gray-500 mb-4">
            What tools, software, or hardware will students need to complete
            this course?
          </p>

          <div class="space-y-3">
            <div
              v-for="(requirement, index) in course.requirements"
              :key="`requirement-${index}`"
              class="flex items-center"
            >
              <div class="flex-grow">
                <div class="flex items-center">
                  <span class="text-gray-500 mr-2">{{ index + 1 }}.</span>
                  <input
                    type="text"
                    v-model="course.requirements[index]"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    :placeholder="`e.g. A computer with internet access`"
                    :class="{ 'border-red-500': formErrors.requirements }"
                  />
                </div>
              </div>
              <button
                type="button"
                @click="removeRequirement(index)"
                class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700"
                :disabled="course.requirements.length === 1"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
            <button
              type="button"
              @click="addRequirement"
              class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
              Add Requirement
            </button>
            <p
              v-if="formErrors.requirements"
              class="mt-1 text-sm text-red-600 error-message"
            >
              {{ formErrors.requirements }}
            </p>
          </div>
        </div>

        <!-- Tags Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Course Tags</h2>
          <p class="text-sm text-gray-500 mb-4">
            Add keywords that describe your course to improve discoverability.
          </p>

          <div class="space-y-3">
            <div
              v-for="(tag, index) in course.tags"
              :key="`tag-${index}`"
              class="flex items-center"
            >
              <div class="flex-grow">
                <div class="flex items-center">
                  <span class="text-gray-500 mr-2">#</span>
                  <input
                    type="text"
                    v-model="course.tags[index]"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="e.g. webdevelopment"
                    :class="{ 'border-red-500': formErrors.tags }"
                  />
                </div>
              </div>
              <button
                type="button"
                @click="removeTag(index)"
                class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700"
                :disabled="course.tags.length === 1"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
            <button
              type="button"
              @click="addTag"
              class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
              Add Tag
            </button>
            <p
              v-if="formErrors.tags"
              class="mt-1 text-sm text-red-600 error-message"
            >
              {{ formErrors.tags }}
            </p>
          </div>
        </div>

        <!-- Pricing Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Course Pricing</h2>
          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-2">
              <label for="price" class="block text-sm font-medium text-gray-700"
                >Price</label
              >
              <div class="mt-1 relative rounded-md shadow-sm">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                  type="number"
                  id="price"
                  v-model="course.price.amount"
                  class="block w-full pl-7 pr-12 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="0.00"
                  min="0"
                  step="0.01"
                />
                <div
                  class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                >
                  <span class="text-gray-500 sm:text-sm">USD</span>
                </div>
              </div>
            </div>

            <div class="sm:col-span-2">
              <label
                for="discount"
                class="block text-sm font-medium text-gray-700"
                >Discount (%)</label
              >
              <div class="mt-1 relative rounded-md shadow-sm">
                <input
                  type="number"
                  id="discount"
                  v-model="course.price.discountPercentage"
                  class="block w-full pr-12 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="0"
                  min="0"
                  max="100"
                />
                <div
                  class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                >
                  <span class="text-gray-500 sm:text-sm">%</span>
                </div>
              </div>
            </div>

            <div class="sm:col-span-2">
              <label
                for="currency"
                class="block text-sm font-medium text-gray-700"
                >Currency</label
              >
              <div class="mt-1">
                <select
                  id="currency"
                  v-model="course.price.currency"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="USD">USD - US Dollar</option>
                  <option value="EUR">EUR - Euro</option>
                  <option value="GBP">GBP - British Pound</option>
                  <option value="CAD">CAD - Canadian Dollar</option>
                  <option value="AUD">AUD - Australian Dollar</option>
                </select>
              </div>
            </div>

            <div class="sm:col-span-3">
              <div class="flex items-center h-6">
                <input
                  id="featured"
                  v-model="course.isFeatured"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="featured" class="ml-2 text-sm text-gray-700"
                  >Feature this course on the homepage</label
                >
              </div>
            </div>

            <div class="sm:col-span-3">
              <div class="flex items-center h-6">
                <input
                  id="certificate"
                  v-model="course.hasCertificate"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="certificate" class="ml-2 text-sm text-gray-700"
                  >Offer completion certificate</label
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Instructors Selection Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Assign Instructors
          </h2>
          <p class="text-sm text-gray-500 mb-4">
            Select the instructors who will be teaching this course.
            {{
              course.category
                ? "Showing instructors with relevant expertise."
                : ""
            }}
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="instructor in filteredInstructors"
              :key="instructor.id"
              class="border rounded-lg p-4 cursor-pointer transition-all duration-200"
              :class="{
                'border-indigo-500 bg-indigo-50':
                  course.selectedInstructors.includes(instructor.id),
                'border-gray-200 hover:border-indigo-300':
                  !course.selectedInstructors.includes(instructor.id),
              }"
              @click="toggleInstructor(instructor.id)"
            >
              <div class="flex items-center">
                <img
                  :src="instructor.avatar"
                  :alt="instructor.name"
                  class="h-12 w-12 rounded-full object-cover"
                />
                <div class="ml-4">
                  <h3 class="text-sm font-medium text-gray-900">
                    {{ instructor.name }}
                  </h3>
                  <p class="text-xs text-gray-500">{{ instructor.bio }}</p>
                  <div class="mt-1 flex flex-wrap">
                    <span
                      v-for="(expertise, index) in instructor.expertise"
                      :key="index"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 mr-1 mb-1"
                    >
                      {{ expertise }}
                    </span>
                  </div>
                </div>
                <div class="ml-auto">
                  <input
                    type="checkbox"
                    :checked="
                      course.selectedInstructors.includes(instructor.id)
                    "
                    class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    @change="toggleInstructor(instructor.id)"
                    @click.stop
                  />
                </div>
              </div>
            </div>
          </div>
          <p
            v-if="formErrors.instructors"
            class="mt-2 text-sm text-red-600 error-message"
          >
            {{ formErrors.instructors }}
          </p>
        </div>

        <!-- Course Content Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Course Content</h2>
          <p class="text-sm text-gray-500 mb-4">
            Organize your course into sections and lessons.
          </p>

          <div class="space-y-4">
            <div
              v-for="(section, sectionIndex) in course.sections"
              :key="`section-${section.id}`"
              class="border border-gray-200 rounded-lg overflow-hidden"
            >
              <!-- Section Header -->
              <div class="bg-gray-50 p-4 flex items-center justify-between">
                <div class="flex-grow">
                  <input
                    type="text"
                    v-model="section.title"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Section Title"
                    :class="{ 'border-red-500': formErrors.sections }"
                  />
                </div>
                <button
                  type="button"
                  @click="removeSection(sectionIndex)"
                  class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700"
                  :disabled="course.sections.length === 1"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>

              <!-- Section Description -->
              <div class="p-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Section Description</label
                >
                <textarea
                  v-model="section.description"
                  rows="2"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  placeholder="Provide a brief overview of this section"
                ></textarea>
              </div>

              <!-- Lessons -->
              <div class="p-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                  >Lessons</label
                >
                <div class="space-y-3">
                  <div
                    v-for="(lesson, lessonIndex) in section.lessons"
                    :key="`lesson-${section.id}-${lesson.id}`"
                    class="bg-white p-3 border border-gray-200 rounded-md"
                  >
                    <div class="flex items-center justify-between">
                      <div class="flex-grow">
                        <input
                          type="text"
                          v-model="lesson.title"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="Lesson Title"
                          :class="{ 'border-red-500': formErrors.lessons }"
                        />
                      </div>
                      <button
                        type="button"
                        @click="removeLesson(sectionIndex, lessonIndex)"
                        class="ml-2 flex-shrink-0 text-red-500 hover:text-red-700"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>

                    <div
                      class="mt-3 grid grid-cols-1 gap-y-3 gap-x-4 sm:grid-cols-6"
                    >
                      <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700"
                          >Content Type</label
                        >
                        <select
                          v-model="lesson.contentType"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                          <option value="video">Video</option>
                          <option value="article">Article</option>
                          <option value="quiz">Quiz</option>
                          <option value="assignment">Assignment</option>
                        </select>
                      </div>
                      <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700"
                          >Duration (minutes)</label
                        >
                        <input
                          type="number"
                          v-model="lesson.duration"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          min="0"
                        />
                      </div>
                      <div class="sm:col-span-6">
                        <label class="block text-xs font-medium text-gray-700"
                          >Description</label
                        >
                        <textarea
                          v-model="lesson.description"
                          rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="Brief description of this lesson"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <button
                  type="button"
                  @click="addLesson(sectionIndex)"
                  class="mt-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                  Add Lesson
                </button>
              </div>
            </div>
          </div>

          <button
            type="button"
            @click="addSection"
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
            Add New Section
          </button>
        </div>

        <!-- Publication Settings Section -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">
            Publication Settings
          </h2>
          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
              <label
                for="status"
                class="block text-sm font-medium text-gray-700"
                >Course Status</label
              >
              <div class="mt-1">
                <select
                  id="status"
                  v-model="course.status"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="scheduled">Scheduled</option>
                  <option value="review">Under Review</option>
                </select>
              </div>
            </div>

            <div class="sm:col-span-3" v-if="course.status === 'scheduled'">
              <label
                for="publishDate"
                class="block text-sm font-medium text-gray-700"
                >Publish Date</label
              >
              <div class="mt-1">
                <input
                  type="date"
                  id="publishDate"
                  v-model="course.publishDate"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Course Summary and Duration -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Course Summary</h2>

          <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">
                  Total Sections
                </h4>
                <p class="text-2xl font-bold text-indigo-600">
                  {{ course.sections.length }}
                </p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">
                  Total Lessons
                </h4>
                <p class="text-2xl font-bold text-indigo-600">
                  {{
                    course.sections.reduce(
                      (total, section) => total + section.lessons.length,
                      0
                    )
                  }}
                </p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">
                  Estimated Duration
                </h4>
                <p class="text-2xl font-bold text-indigo-600">
                  {{ course.estimatedDuration }} hours
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  Automatically calculated based on lesson durations
                </p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">
                  Selected Instructors
                </h4>
                <p class="text-2xl font-bold text-indigo-600">
                  {{ course.selectedInstructors.length }}
                </p>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">
                  Course Status
                </h4>
                <div>
                  <span
                    class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium"
                    :class="{
                      'bg-yellow-100 text-yellow-800':
                        course.status === 'draft',
                      'bg-green-100 text-green-800':
                        course.status === 'published',
                      'bg-blue-100 text-blue-800': course.status === 'review',
                      'bg-purple-100 text-purple-800':
                        course.status === 'scheduled',
                    }"
                  >
                    {{
                      course.status.charAt(0).toUpperCase() +
                      course.status.slice(1)
                    }}
                    <span v-if="course.status === 'scheduled'">
                      ({{ new Date(course.publishDate).toLocaleDateString() }})
                    </span>
                  </span>
                </div>
              </div>

              <div>
                <h4 class="text-sm font-medium text-gray-700 mb-1">Price</h4>
                <p class="text-2xl font-bold text-indigo-600">
                  ${{
                    (
                      course.price.amount *
                      (1 - course.price.discountPercentage / 100)
                    ).toFixed(2)
                  }}
                </p>
                <p
                  v-if="course.price.discountPercentage > 0"
                  class="text-xs text-gray-500 mt-1"
                >
                  <span class="line-through"
                    >${{ course.price.amount.toFixed(2) }}</span
                  >
                  <span class="text-red-600 ml-1"
                    >{{ course.price.discountPercentage }}% off</span
                  >
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action buttons -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            @click="$router.back()"
          >
            Cancel
          </button>
          <button
            type="button"
            class="rounded-md border border-transparent bg-indigo-100 py-2 px-4 text-sm font-medium text-indigo-700 shadow-sm hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            @click="updateEstimatedDuration"
          >
            Update Duration
          </button>
          <button
            type="button"
            class="rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            @click="submitForm"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting">
              <svg
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
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
              Saving...
            </span>
            <span v-else>Save Course</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Animation for success message */
@keyframes fadeInOut {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  10% {
    opacity: 1;
    transform: translateY(0);
  }
  90% {
    opacity: 1;
    transform: translateY(0);
  }
  100% {
    opacity: 0;
    transform: translateY(-10px);
  }
}

.success-animation {
  animation: fadeInOut 3s forwards;
}

/* Smooth transitions */
.transition-height {
  transition: max-height 0.3s ease-in-out;
  overflow: hidden;
}
</style>
