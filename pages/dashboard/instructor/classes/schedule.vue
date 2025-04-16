<!-- pages/dashboard/instructor/classes/schedule.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import {
  format,
  addDays,
  addMonths,
  subMonths,
  setHours,
  setMinutes,
  parseISO,
  isSameDay,
} from "date-fns";

// Page meta
definePageMeta({
  layout: "dashboard-instructor",
});

useHead({
  title: "Schedule Class - Instructor Dashboard",
});

// Types
interface Course {
  id: number;
  title: string;
  category: string;
  thumbnail: string;
  status: "published" | "draft" | "under_review" | "archived";
}

interface Student {
  id: number;
  name: string;
  email: string;
  avatar: string;
  isEnrolled: boolean;
}

interface ClassSchedule {
  id?: number;
  title: string;
  description: string;
  courseId: number | null;
  date: string; // ISO string format
  startTime: string; // ISO string format
  endTime: string; // ISO string format
  type: "lecture" | "workshop" | "qa" | "office_hours" | "assessment";
  meetingType: "video" | "in_person";
  meetingUrl?: string;
  location?: string;
  maxAttendees: number | null;
  isRecurring: boolean;
  recurringPattern?: "daily" | "weekly" | "biweekly" | "monthly";
  recurringEndDate?: string;
  notifyStudents: boolean;
  materials: string[];
}

// State
const isLoading = ref(false);
const isSaving = ref(false);
const showConfirmationModal = ref(false);
const showDatePicker = ref(false);
const showTimePicker = ref(false);
const timePickerType = ref<"start" | "end">("start");
const currentViewDate = ref(new Date());
const selectedDate = ref(new Date());
const courses = ref<Course[]>([]);
const students = ref<Student[]>([]);
const selectedStudents = ref<number[]>([]);
const searchStudentQuery = ref("");
const addingMaterial = ref(false);
const newMaterialUrl = ref("");
const temporarySchedules = ref<ClassSchedule[]>([]);

// Form data
const classSchedule = ref<ClassSchedule>({
  title: "",
  description: "",
  courseId: null,
  date: format(new Date(), "yyyy-MM-dd"),
  startTime: format(
    setHours(setMinutes(new Date(), 0), 9),
    "yyyy-MM-dd'T'HH:mm"
  ),
  endTime: format(
    setHours(setMinutes(new Date(), 0), 10),
    "yyyy-MM-dd'T'HH:mm"
  ),
  type: "lecture",
  meetingType: "video",
  meetingUrl: "",
  location: "",
  maxAttendees: null,
  isRecurring: false,
  notifyStudents: true,
  materials: [],
});

// Form validation state
const formErrors = ref<Record<string, string>>({});

// Fetch instructor's courses
const fetchCourses = async () => {
  try {
    isLoading.value = true;
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 600));

    // Mock data
    courses.value = [
      {
        id: 1,
        title: "Complete Web Development Bootcamp",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
      },
      {
        id: 2,
        title: "Advanced JavaScript: From Fundamentals to Mastery",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
      },
      {
        id: 3,
        title: "UI/UX Design Principles",
        category: "Design",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
      },
      {
        id: 8,
        title: "Responsive Web Design Masterclass",
        category: "Web Development",
        thumbnail: "/api/placeholder/400/225",
        status: "published",
      },
    ];
  } catch (error) {
    console.error("Error fetching courses:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch enrolled students
const fetchStudents = async () => {
  try {
    isLoading.value = true;
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 600));

    // Mock data
    students.value = [
      {
        id: 1,
        name: "Emma Wilson",
        email: "emma.wilson@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 2,
        name: "James Brown",
        email: "james.brown@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 3,
        name: "Olivia Smith",
        email: "olivia.smith@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 4,
        name: "William Johnson",
        email: "william.johnson@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 5,
        name: "Sophia Martinez",
        email: "sophia.martinez@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 6,
        name: "Michael Davis",
        email: "michael.davis@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 7,
        name: "Isabella Rodriguez",
        email: "isabella.rodriguez@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
      {
        id: 8,
        name: "Ethan Wilson",
        email: "ethan.wilson@example.com",
        avatar: "/api/placeholder/40/40",
        isEnrolled: true,
      },
    ];
  } catch (error) {
    console.error("Error fetching students:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch existing schedule
const fetchExistingSchedule = async () => {
  try {
    isLoading.value = true;
    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 600));

    // Mock data - some existing scheduled classes
    temporarySchedules.value = [
      {
        id: 1,
        title: "Introduction to HTML & CSS",
        description: "Fundamentals of HTML and CSS for beginners.",
        courseId: 1,
        date: format(new Date(), "yyyy-MM-dd"),
        startTime: format(
          setHours(setMinutes(new Date(), 0), 14),
          "yyyy-MM-dd'T'HH:mm"
        ),
        endTime: format(
          setHours(setMinutes(new Date(), 0), 16),
          "yyyy-MM-dd'T'HH:mm"
        ),
        type: "lecture",
        meetingType: "video",
        meetingUrl: "https://zoom.us/j/example",
        maxAttendees: 30,
        isRecurring: true,
        recurringPattern: "weekly",
        notifyStudents: true,
        materials: ["https://example.com/slides.pdf"],
      },
      {
        id: 2,
        title: "JavaScript Q&A Session",
        description: "Live Q&A session for JavaScript module.",
        courseId: 2,
        date: format(addDays(new Date(), 2), "yyyy-MM-dd"),
        startTime: format(
          setHours(setMinutes(addDays(new Date(), 2), 0), 10),
          "yyyy-MM-dd'T'HH:mm"
        ),
        endTime: format(
          setHours(setMinutes(addDays(new Date(), 2), 0), 11),
          "yyyy-MM-dd'T'HH:mm"
        ),
        type: "qa",
        meetingType: "video",
        meetingUrl: "https://meet.google.com/example",
        maxAttendees: null,
        isRecurring: false,
        notifyStudents: true,
        materials: [],
      },
    ];
  } catch (error) {
    console.error("Error fetching schedule:", error);
  } finally {
    isLoading.value = false;
  }
};

// Filtered students based on search query
const filteredStudents = computed(() => {
  if (!searchStudentQuery.value) return students.value;

  const query = searchStudentQuery.value.toLowerCase();
  return students.value.filter(
    (student) =>
      student.name.toLowerCase().includes(query) ||
      student.email.toLowerCase().includes(query)
  );
});

// Scheduled classes for the selected date
const scheduledClassesForSelectedDate = computed(() => {
  return temporarySchedules.value.filter((schedule) =>
    isSameDay(parseISO(schedule.date), selectedDate.value)
  );
});

// Calendar date navigation
const previousMonth = () => {
  currentViewDate.value = subMonths(currentViewDate.value, 1);
};

const nextMonth = () => {
  currentViewDate.value = addMonths(currentViewDate.value, 1);
};

const currentMonthName = computed(() => {
  return format(currentViewDate.value, "MMMM yyyy");
});

const goToToday = () => {
  currentViewDate.value = new Date();
  selectedDate.value = new Date();
  classSchedule.value.date = format(new Date(), "yyyy-MM-dd");
};

// Calendar days generation
const calendarDays = computed(() => {
  // Get the first day of the month
  const firstDay = new Date(
    currentViewDate.value.getFullYear(),
    currentViewDate.value.getMonth(),
    1
  );

  // Get the day of the week for the first day (0 = Sunday, 1 = Monday, etc.)
  const firstDayOfWeek = firstDay.getDay();

  // Calculate days to include from previous month
  const daysFromPreviousMonth = firstDayOfWeek;
  const daysInPreviousMonth = new Date(
    currentViewDate.value.getFullYear(),
    currentViewDate.value.getMonth(),
    0
  ).getDate();

  // Calculate days to include from current month
  const daysInCurrentMonth = new Date(
    currentViewDate.value.getFullYear(),
    currentViewDate.value.getMonth() + 1,
    0
  ).getDate();

  // Initialize calendar array
  const days = [];

  // Add days from previous month
  for (
    let i = daysInPreviousMonth - daysFromPreviousMonth + 1;
    i <= daysInPreviousMonth;
    i++
  ) {
    const date = new Date(
      currentViewDate.value.getFullYear(),
      currentViewDate.value.getMonth() - 1,
      i
    );
    days.push({
      date,
      dayOfMonth: i,
      isCurrentMonth: false,
      hasSchedule: temporarySchedules.value.some((schedule) =>
        isSameDay(parseISO(schedule.date), date)
      ),
    });
  }

  // Add days from current month
  for (let i = 1; i <= daysInCurrentMonth; i++) {
    const date = new Date(
      currentViewDate.value.getFullYear(),
      currentViewDate.value.getMonth(),
      i
    );
    days.push({
      date,
      dayOfMonth: i,
      isCurrentMonth: true,
      hasSchedule: temporarySchedules.value.some((schedule) =>
        isSameDay(parseISO(schedule.date), date)
      ),
    });
  }

  // Add days from next month to complete the grid (up to 42 total days for a 6-week view)
  const totalDaysNeeded = 42;
  const daysToAdd = totalDaysNeeded - days.length;

  for (let i = 1; i <= daysToAdd; i++) {
    const date = new Date(
      currentViewDate.value.getFullYear(),
      currentViewDate.value.getMonth() + 1,
      i
    );
    days.push({
      date,
      dayOfMonth: i,
      isCurrentMonth: false,
      hasSchedule: temporarySchedules.value.some((schedule) =>
        isSameDay(parseISO(schedule.date), date)
      ),
    });
  }

  // Group days into weeks
  const weeks = [];
  for (let i = 0; i < days.length; i += 7) {
    weeks.push(days.slice(i, i + 7));
  }

  return weeks;
});

// Select a date from the calendar
const selectDate = (day: { date: Date; isCurrentMonth: boolean }) => {
  selectedDate.value = day.date;
  classSchedule.value.date = format(day.date, "yyyy-MM-dd");

  // Update start and end times to maintain the same time but on the new date
  const startDate = parseISO(classSchedule.value.startTime);
  const endDate = parseISO(classSchedule.value.endTime);

  classSchedule.value.startTime = format(
    new Date(
      day.date.getFullYear(),
      day.date.getMonth(),
      day.date.getDate(),
      startDate.getHours(),
      startDate.getMinutes()
    ),
    "yyyy-MM-dd'T'HH:mm"
  );

  classSchedule.value.endTime = format(
    new Date(
      day.date.getFullYear(),
      day.date.getMonth(),
      day.date.getDate(),
      endDate.getHours(),
      endDate.getMinutes()
    ),
    "yyyy-MM-dd'T'HH:mm"
  );

  showDatePicker.value = false;
};

// Check if a date is today
const isToday = (date: Date) => {
  const today = new Date();
  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  );
};

// Check if a date is the selected date
const isSelectedDate = (date: Date) => {
  return (
    date.getDate() === selectedDate.value.getDate() &&
    date.getMonth() === selectedDate.value.getMonth() &&
    date.getFullYear() === selectedDate.value.getFullYear()
  );
};

// Format date for display
const formatDate = (date: Date, formatStr: string = "EEEE, MMMM d, yyyy") => {
  return format(date, formatStr);
};

// Handle time picker changes
const openTimePicker = (type: "start" | "end") => {
  timePickerType.value = type;
  showTimePicker.value = true;
};

const setTime = (hours: number, minutes: number) => {
  const date = parseISO(
    timePickerType.value === "start"
      ? classSchedule.value.startTime
      : classSchedule.value.endTime
  );
  const newDate = new Date(
    date.getFullYear(),
    date.getMonth(),
    date.getDate(),
    hours,
    minutes
  );

  if (timePickerType.value === "start") {
    classSchedule.value.startTime = format(newDate, "yyyy-MM-dd'T'HH:mm");

    // Ensure end time is at least 30 minutes after start time
    const endDate = parseISO(classSchedule.value.endTime);
    if (
      newDate >= endDate ||
      endDate.getTime() - newDate.getTime() < 30 * 60 * 1000
    ) {
      const adjustedEndTime = new Date(newDate.getTime() + 60 * 60 * 1000); // 1 hour later
      classSchedule.value.endTime = format(
        adjustedEndTime,
        "yyyy-MM-dd'T'HH:mm"
      );
    }
  } else {
    const startDate = parseISO(classSchedule.value.startTime);
    if (
      newDate <= startDate ||
      newDate.getTime() - startDate.getTime() < 30 * 60 * 1000
    ) {
      // End time must be after start time by at least 30 minutes
      formErrors.value.endTime =
        "End time must be at least 30 minutes after start time";
    } else {
      classSchedule.value.endTime = format(newDate, "yyyy-MM-dd'T'HH:mm");
      delete formErrors.value.endTime;
    }
  }

  showTimePicker.value = false;
};

// Format time for display
const formatTime = (timeString: string) => {
  const date = parseISO(timeString);
  return format(date, "h:mm a");
};

// Toggle student selection
const toggleStudentSelection = (studentId: number) => {
  const index = selectedStudents.value.indexOf(studentId);
  if (index === -1) {
    selectedStudents.value.push(studentId);
  } else {
    selectedStudents.value.splice(index, 1);
  }
};

// Add material to class
const addMaterial = () => {
  if (!newMaterialUrl.value) return;

  classSchedule.value.materials.push(newMaterialUrl.value);
  newMaterialUrl.value = "";
  addingMaterial.value = false;
};

// Remove material from class
const removeMaterial = (index: number) => {
  classSchedule.value.materials.splice(index, 1);
};

// Validate form
const validateForm = () => {
  formErrors.value = {};

  if (!classSchedule.value.title.trim()) {
    formErrors.value.title = "Title is required";
  }

  if (!classSchedule.value.courseId) {
    formErrors.value.courseId = "Please select a course";
  }

  const startTime = parseISO(classSchedule.value.startTime);
  const endTime = parseISO(classSchedule.value.endTime);

  if (startTime >= endTime) {
    formErrors.value.endTime = "End time must be after start time";
  }

  if (
    classSchedule.value.meetingType === "video" &&
    !classSchedule.value.meetingUrl
  ) {
    formErrors.value.meetingUrl = "Meeting URL is required for video classes";
  }

  if (
    classSchedule.value.meetingType === "in_person" &&
    !classSchedule.value.location
  ) {
    formErrors.value.location = "Location is required for in-person classes";
  }

  if (
    classSchedule.value.isRecurring &&
    !classSchedule.value.recurringPattern
  ) {
    formErrors.value.recurringPattern = "Please select a recurring pattern";
  }

  return Object.keys(formErrors.value).length === 0;
};

// Preview recurrence pattern
const getRecurrencePreview = () => {
  if (
    !classSchedule.value.isRecurring ||
    !classSchedule.value.recurringPattern
  ) {
    return "One-time class";
  }

  const date = parseISO(classSchedule.value.date);

  switch (classSchedule.value.recurringPattern) {
    case "daily":
      return "Daily at " + formatTime(classSchedule.value.startTime);
    case "weekly":
      return `Weekly on ${format(date, "EEEE")}s at ${formatTime(
        classSchedule.value.startTime
      )}`;
    case "biweekly":
      return `Every two weeks on ${format(date, "EEEE")}s at ${formatTime(
        classSchedule.value.startTime
      )}`;
    case "monthly":
      return `Monthly on the ${format(date, "do")} at ${formatTime(
        classSchedule.value.startTime
      )}`;
    default:
      return "One-time class";
  }
};

// Generate recurring classes
const generateRecurringClasses = () => {
  // This would be handled server-side in a real application
  // Here we're just showing a confirmation modal
  showConfirmationModal.value = true;
};

// Schedule the class
const scheduleClass = async () => {
  if (!validateForm()) {
    // Scroll to first error
    const firstErrorField = Object.keys(formErrors.value)[0];
    const element = document.getElementById(firstErrorField);
    if (element) {
      element.scrollIntoView({ behavior: "smooth", block: "center" });
      element.focus();
    }
    return;
  }

  try {
    isSaving.value = true;

    // In a real app, this would be an API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    if (classSchedule.value.isRecurring) {
      generateRecurringClasses();
    } else {
      // Add the scheduled class to our temporary array
      temporarySchedules.value.push({
        ...classSchedule.value,
        id: Date.now(), // Generate a temporary ID
      });

      // Reset form
      classSchedule.value = {
        title: "",
        description: "",
        courseId: null,
        date: format(selectedDate.value, "yyyy-MM-dd"),
        startTime: format(
          setHours(setMinutes(selectedDate.value, 0), 9),
          "yyyy-MM-dd'T'HH:mm"
        ),
        endTime: format(
          setHours(setMinutes(selectedDate.value, 0), 10),
          "yyyy-MM-dd'T'HH:mm"
        ),
        type: "lecture",
        meetingType: "video",
        meetingUrl: "",
        location: "",
        maxAttendees: null,
        isRecurring: false,
        notifyStudents: true,
        materials: [],
      };

      // Simulate navigation back to calendar
      setTimeout(() => {
        window.location.href = "/dashboard/instructor/classes";
      }, 1000);
    }
  } catch (error) {
    console.error("Error scheduling class:", error);
  } finally {
    isSaving.value = false;
  }
};

// Watch for recurring option changes
watch(
  () => classSchedule.value.isRecurring,
  (isRecurring) => {
    if (isRecurring && !classSchedule.value.recurringPattern) {
      classSchedule.value.recurringPattern = "weekly";
    }
  }
);

// Get hours array for time picker
const getHours = () => {
  const hours = [];
  for (let i = 0; i < 24; i++) {
    hours.push({
      value: i,
      label:
        i === 0
          ? "12 AM"
          : i < 12
          ? `${i} AM`
          : i === 12
          ? "12 PM"
          : `${i - 12} PM`,
    });
  }
  return hours;
};

// Get minutes array for time picker
const getMinutes = () => {
  const minutes = [];
  for (let i = 0; i < 60; i += 5) {
    minutes.push({
      value: i,
      label: i < 10 ? `0${i}` : `${i}`,
    });
  }
  return minutes;
};

// Load initial data
onMounted(() => {
  fetchCourses();
  fetchStudents();
  fetchExistingSchedule();
});
</script>

<template>
  <div class="p-4 md:p-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Schedule a Class</h1>
      <p class="text-gray-600">
        Create a new class session for your course and invite students
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Class Details Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">
            Class Details
          </h2>

          <form @submit.prevent="scheduleClass">
            <!-- Class Title -->
            <div class="mb-4">
              <label
                for="title"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Class Title*</label
              >
              <input
                id="title"
                v-model="classSchedule.title"
                type="text"
                class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-300': formErrors.title }"
                placeholder="Enter class title"
                required
              />
              <p v-if="formErrors.title" class="mt-1 text-sm text-red-600">
                {{ formErrors.title }}
              </p>
            </div>

            <!-- Course Selection -->
            <div class="mb-4">
              <label
                for="courseId"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Select Course*</label
              >
              <select
                id="courseId"
                v-model="classSchedule.courseId"
                class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-300': formErrors.courseId }"
                required
              >
                <option :value="null" disabled>Select a course</option>
                <option
                  v-for="course in courses"
                  :key="course.id"
                  :value="course.id"
                >
                  {{ course.title }}
                </option>
              </select>
              <p v-if="formErrors.courseId" class="mt-1 text-sm text-red-600">
                {{ formErrors.courseId }}
              </p>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <label
                for="description"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <textarea
                id="description"
                v-model="classSchedule.description"
                rows="3"
                class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="What will you cover in this class?"
              ></textarea>
            </div>

            <!-- Class Type -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Class Type</label
              >
              <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <label
                  class="flex items-center space-x-2 bg-white border rounded-lg p-3 cursor-pointer transition-colors hover:bg-indigo-50"
                  :class="{
                    'border-indigo-500 bg-indigo-50':
                      classSchedule.type === 'lecture',
                  }"
                >
                  <input
                    type="radio"
                    v-model="classSchedule.type"
                    value="lecture"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span>Lecture</span>
                </label>
                <label
                  class="flex items-center space-x-2 bg-white border rounded-lg p-3 cursor-pointer transition-colors hover:bg-indigo-50"
                  :class="{
                    'border-indigo-500 bg-indigo-50':
                      classSchedule.type === 'workshop',
                  }"
                >
                  <input
                    type="radio"
                    v-model="classSchedule.type"
                    value="workshop"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span>Workshop</span>
                </label>
                <label
                  class="flex items-center space-x-2 bg-white border rounded-lg p-3 cursor-pointer transition-colors hover:bg-indigo-50"
                  :class="{
                    'border-indigo-500 bg-indigo-50':
                      classSchedule.type === 'qa',
                  }"
                >
                  <input
                    type="radio"
                    v-model="classSchedule.type"
                    value="qa"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span>Q&A</span>
                </label>
                <label
                  class="flex items-center space-x-2 bg-white border rounded-lg p-3 cursor-pointer transition-colors hover:bg-indigo-50"
                  :class="{
                    'border-indigo-500 bg-indigo-50':
                      classSchedule.type === 'office_hours',
                  }"
                >
                  <input
                    type="radio"
                    v-model="classSchedule.type"
                    value="office_hours"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span>Office Hours</span>
                </label>
                <label
                  class="flex items-center space-x-2 bg-white border rounded-lg p-3 cursor-pointer transition-colors hover:bg-indigo-50"
                  :class="{
                    'border-indigo-500 bg-indigo-50':
                      classSchedule.type === 'assessment',
                  }"
                >
                  <input
                    type="radio"
                    v-model="classSchedule.type"
                    value="assessment"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span>Assessment</span>
                </label>
              </div>
            </div>

            <!-- Date and Time -->
            <div class="mb-4">
              <h3 class="text-sm font-medium text-gray-700 mb-2">
                Date and Time*
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date selection -->
                <div class="relative">
                  <label for="date" class="block text-sm text-gray-600 mb-1"
                    >Date</label
                  >
                  <div class="relative">
                    <input
                      id="date"
                      :value="formatDate(selectedDate, 'EEE, MMM d, yyyy')"
                      class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer"
                      readonly
                      @click="showDatePicker = !showDatePicker"
                    />
                    <div
                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                      </svg>
                    </div>
                  </div>

                  <!-- Date picker popup -->
                  <div
                    v-if="showDatePicker"
                    class="absolute z-10 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 p-4 w-80"
                  >
                    <!-- Calendar header -->
                    <div class="flex justify-between items-center mb-4">
                      <button
                        @click="previousMonth"
                        class="p-1 rounded-full hover:bg-gray-100 text-gray-600"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                      <h3 class="text-md font-medium">
                        {{ currentMonthName }}
                      </h3>
                      <button
                        @click="nextMonth"
                        class="p-1 rounded-full hover:bg-gray-100 text-gray-600"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>

                    <!-- Day of week headers -->
                    <div class="grid grid-cols-7 gap-1 mb-2 text-center">
                      <div
                        v-for="day in [
                          'Su',
                          'Mo',
                          'Tu',
                          'We',
                          'Th',
                          'Fr',
                          'Sa',
                        ]"
                        :key="day"
                        class="text-xs font-medium text-gray-500"
                      >
                        {{ day }}
                      </div>
                    </div>

                    <!-- Calendar days -->
                    <div class="grid grid-cols-7 gap-1">
                      <button
                        v-for="(day, dayIndex) in calendarDays.flat()"
                        :key="dayIndex"
                        @click="selectDate(day)"
                        class="h-8 w-8 flex items-center justify-center text-sm rounded-full transition-colors"
                        :class="{
                          'text-gray-400': !day.isCurrentMonth,
                          'bg-indigo-600 text-white hover:bg-indigo-700':
                            isSelectedDate(day.date),
                          'bg-indigo-100 text-indigo-800 font-medium':
                            isToday(day.date) && !isSelectedDate(day.date),
                          'hover:bg-gray-100':
                            day.isCurrentMonth &&
                            !isSelectedDate(day.date) &&
                            !isToday(day.date),
                          relative: day.hasSchedule,
                        }"
                      >
                        {{ day.dayOfMonth }}
                        <!-- Indicator dot for days with scheduled classes -->
                        <span
                          v-if="day.hasSchedule && !isSelectedDate(day.date)"
                          class="absolute bottom-0.5 h-1 w-1 rounded-full bg-indigo-500"
                        ></span>
                      </button>
                    </div>

                    <!-- Today button -->
                    <div class="mt-4 text-center">
                      <button
                        @click="goToToday"
                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                      >
                        Go to Today
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Start time -->
                <div>
                  <label
                    for="startTime"
                    class="block text-sm text-gray-600 mb-1"
                    >Start Time</label
                  >
                  <div class="relative">
                    <input
                      id="startTime"
                      :value="formatTime(classSchedule.startTime)"
                      class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer"
                      readonly
                      @click="openTimePicker('start')"
                    />
                    <div
                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <!-- End time -->
                <div>
                  <label for="endTime" class="block text-sm text-gray-600 mb-1"
                    >End Time</label
                  >
                  <div class="relative">
                    <input
                      id="endTime"
                      :value="formatTime(classSchedule.endTime)"
                      class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer"
                      :class="{ 'border-red-300': formErrors.endTime }"
                      readonly
                      @click="openTimePicker('end')"
                    />
                    <div
                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                    </div>
                  </div>
                  <p
                    v-if="formErrors.endTime"
                    class="mt-1 text-sm text-red-600"
                  >
                    {{ formErrors.endTime }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Time picker popup -->
            <div
              v-if="showTimePicker"
              class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            >
              <div class="bg-white rounded-lg shadow-xl p-6 w-80">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                  {{
                    timePickerType === "start"
                      ? "Select Start Time"
                      : "Select End Time"
                  }}
                </h3>

                <div class="grid grid-cols-2 gap-4 mb-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                      >Hour</label
                    >
                    <div class="max-h-60 overflow-y-auto border rounded-lg">
                      <div class="grid grid-cols-4">
                        <button
                          v-for="hour in getHours()"
                          :key="hour.value"
                          @click="
                            setTime(
                              hour.value,
                              timePickerType === 'start'
                                ? parseISO(classSchedule.startTime).getMinutes()
                                : parseISO(classSchedule.endTime).getMinutes()
                            )
                          "
                          class="h-10 text-sm hover:bg-indigo-50 transition-colors"
                          :class="{
                            'bg-indigo-100 text-indigo-800 font-medium':
                              (timePickerType === 'start' &&
                                parseISO(classSchedule.startTime).getHours() ===
                                  hour.value) ||
                              (timePickerType === 'end' &&
                                parseISO(classSchedule.endTime).getHours() ===
                                  hour.value),
                          }"
                        >
                          {{ hour.label }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                      >Minute</label
                    >
                    <div class="max-h-60 overflow-y-auto border rounded-lg">
                      <div class="grid grid-cols-3">
                        <button
                          v-for="minute in getMinutes()"
                          :key="minute.value"
                          @click="
                            setTime(
                              timePickerType === 'start'
                                ? parseISO(classSchedule.startTime).getHours()
                                : parseISO(classSchedule.endTime).getHours(),
                              minute.value
                            )
                          "
                          class="h-10 text-sm hover:bg-indigo-50 transition-colors"
                          :class="{
                            'bg-indigo-100 text-indigo-800 font-medium':
                              (timePickerType === 'start' &&
                                parseISO(
                                  classSchedule.startTime
                                ).getMinutes() === minute.value) ||
                              (timePickerType === 'end' &&
                                parseISO(classSchedule.endTime).getMinutes() ===
                                  minute.value),
                          }"
                        >
                          {{ minute.label }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end space-x-2">
                  <button
                    @click="showTimePicker = false"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  >
                    Cancel
                  </button>
                </div>
              </div>
            </div>

            <!-- Meeting Details -->
            <div class="mb-4">
              <h3 class="text-sm font-medium text-gray-700 mb-2">
                Meeting Details
              </h3>

              <!-- Meeting Type -->
              <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1"
                  >Meeting Type</label
                >
                <div class="flex space-x-4">
                  <label class="flex items-center space-x-2">
                    <input
                      type="radio"
                      v-model="classSchedule.meetingType"
                      value="video"
                      class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span>Online (Video)</span>
                  </label>
                  <label class="flex items-center space-x-2">
                    <input
                      type="radio"
                      v-model="classSchedule.meetingType"
                      value="in_person"
                      class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span>In-Person</span>
                  </label>
                </div>
              </div>

              <!-- Meeting URL (for online classes) -->
              <div v-if="classSchedule.meetingType === 'video'" class="mb-4">
                <label for="meetingUrl" class="block text-sm text-gray-600 mb-1"
                  >Meeting URL*</label
                >
                <input
                  id="meetingUrl"
                  v-model="classSchedule.meetingUrl"
                  type="url"
                  class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': formErrors.meetingUrl }"
                  placeholder="e.g., https://zoom.us/j/123456789"
                />
                <p
                  v-if="formErrors.meetingUrl"
                  class="mt-1 text-sm text-red-600"
                >
                  {{ formErrors.meetingUrl }}
                </p>
                <p class="mt-1 text-xs text-gray-500">
                  Enter a Zoom, Google Meet, Microsoft Teams, or other video
                  conferencing link
                </p>
              </div>

              <!-- Location (for in-person classes) -->
              <div
                v-if="classSchedule.meetingType === 'in_person'"
                class="mb-4"
              >
                <label for="location" class="block text-sm text-gray-600 mb-1"
                  >Location*</label
                >
                <input
                  id="location"
                  v-model="classSchedule.location"
                  type="text"
                  class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': formErrors.location }"
                  placeholder="Building, Room Number, Address, etc."
                />
                <p v-if="formErrors.location" class="mt-1 text-sm text-red-600">
                  {{ formErrors.location }}
                </p>
              </div>

              <!-- Maximum Attendees -->
              <div class="mb-4">
                <label
                  for="maxAttendees"
                  class="block text-sm text-gray-600 mb-1"
                  >Maximum Attendees (Optional)</label
                >
                <input
                  id="maxAttendees"
                  v-model="classSchedule.maxAttendees"
                  type="number"
                  min="1"
                  class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Leave blank for unlimited"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Set a cap on the number of students who can attend
                </p>
              </div>
            </div>

            <!-- Recurring Options -->
            <div class="mb-4">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-700">
                  Recurring Class
                </h3>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    v-model="classSchedule.isRecurring"
                    class="sr-only peer"
                  />
                  <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"
                  ></div>
                </label>
              </div>

              <div
                v-if="classSchedule.isRecurring"
                class="bg-indigo-50 rounded-lg p-4"
              >
                <div class="mb-4">
                  <label class="block text-sm text-gray-700 mb-1"
                    >Repeat Pattern*</label
                  >
                  <select
                    v-model="classSchedule.recurringPattern"
                    class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-300': formErrors.recurringPattern }"
                  >
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="biweekly">Bi-weekly</option>
                    <option value="monthly">Monthly</option>
                  </select>
                  <p
                    v-if="formErrors.recurringPattern"
                    class="mt-1 text-sm text-red-600"
                  >
                    {{ formErrors.recurringPattern }}
                  </p>
                </div>

                <div class="mb-4">
                  <label
                    for="recurringEndDate"
                    class="block text-sm text-gray-700 mb-1"
                    >End Date (Optional)</label
                  >
                  <input
                    id="recurringEndDate"
                    v-model="classSchedule.recurringEndDate"
                    type="date"
                    class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p class="mt-1 text-xs text-gray-500">
                    Leave blank to keep recurring indefinitely
                  </p>
                </div>

                <div
                  class="text-sm text-gray-700 bg-white p-3 rounded-md border border-gray-200"
                >
                  <strong>Preview:</strong> {{ getRecurrencePreview() }}
                </div>
              </div>
            </div>

            <!-- Class Materials -->
            <div class="mb-4">
              <h3 class="text-sm font-medium text-gray-700 mb-2">
                Class Materials (Optional)
              </h3>

              <div class="bg-gray-50 rounded-lg p-4">
                <div v-if="classSchedule.materials.length > 0" class="mb-4">
                  <ul class="space-y-2">
                    <li
                      v-for="(material, index) in classSchedule.materials"
                      :key="index"
                      class="flex items-center justify-between bg-white p-2 rounded border border-gray-200"
                    >
                      <a
                        :href="material"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-indigo-600 hover:text-indigo-800 truncate"
                      >
                        {{ material }}
                      </a>
                      <button
                        @click="removeMaterial(index)"
                        class="text-gray-500 hover:text-red-500"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </li>
                  </ul>
                </div>

                <div v-if="addingMaterial">
                  <label
                    for="materialUrl"
                    class="block text-sm text-gray-600 mb-1"
                    >Material URL</label
                  >
                  <div class="flex space-x-2">
                    <input
                      id="materialUrl"
                      v-model="newMaterialUrl"
                      type="url"
                      class="flex-1 px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="https://example.com/material.pdf"
                    />
                    <button
                      @click="addMaterial"
                      class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                      :disabled="!newMaterialUrl"
                    >
                      Add
                    </button>
                    <button
                      @click="addingMaterial = false"
                      class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                      Cancel
                    </button>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Add links to slides, documents, or other resources
                  </p>
                </div>

                <button
                  v-if="!addingMaterial"
                  @click="addingMaterial = true"
                  class="flex items-center text-indigo-600 hover:text-indigo-800"
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
                  Add Material
                </button>
              </div>
            </div>

            <!-- Notify Students -->
            <div class="mb-6">
              <div class="flex items-center">
                <input
                  id="notifyStudents"
                  v-model="classSchedule.notifyStudents"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label
                  for="notifyStudents"
                  class="ml-2 block text-sm text-gray-700"
                >
                  Notify enrolled students about this class
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
              <NuxtLink
                to="/dashboard/instructor/classes"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
              >
                Cancel
              </NuxtLink>
              <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
                :disabled="isSaving"
              >
                <svg
                  v-if="isSaving"
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
                {{ isSaving ? "Scheduling..." : "Schedule Class" }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Sidebar: Calendar and Students -->
      <div class="lg:col-span-1">
        <!-- Calendar with Scheduled Classes -->
        <div
          class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-6"
        >
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Scheduled Classes for {{ formatDate(selectedDate, "MMMM d, yyyy") }}
          </h2>

          <div
            v-if="scheduledClassesForSelectedDate.length > 0"
            class="space-y-3 mb-4"
          >
            <div
              v-for="schedule in scheduledClassesForSelectedDate"
              :key="schedule.id"
              class="bg-indigo-50 rounded-lg p-3 border border-indigo-100"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium text-gray-900">
                    {{ schedule.title }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ formatTime(schedule.startTime) }} -
                    {{ formatTime(schedule.endTime) }}
                  </p>
                  <div class="mt-1">
                    <span
                      class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs font-medium rounded capitalize"
                    >
                      {{ schedule.type.replace("_", " ") }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-gray-500 text-sm italic mb-4">
            No classes scheduled for this date.
          </div>

          <div class="border-t border-gray-200 pt-4 mt-2">
            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-500">
                {{ formatDate(selectedDate, "EEEE, MMMM d") }}
              </div>
              <button
                @click="showDatePicker = true"
                class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 mr-1"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                    clip-rule="evenodd"
                  />
                </svg>
                View Calendar
              </button>
            </div>
          </div>
        </div>

        <!-- Course Students (Optional selection) -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Course Students
          </h2>

          <p class="text-sm text-gray-600 mb-4">
            By default, all enrolled students will be notified. Optionally, you
            can select specific students.
          </p>

          <div class="mb-4">
            <div class="relative">
              <input
                v-model="searchStudentQuery"
                type="text"
                placeholder="Search students..."
                class="w-full px-3 py-2 pl-10 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              />
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
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

          <div class="max-h-96 overflow-y-auto border rounded-lg">
            <ul class="divide-y divide-gray-200">
              <li
                v-for="student in filteredStudents"
                :key="student.id"
                class="p-3 hover:bg-gray-50 transition-colors flex items-center justify-between"
                :class="{
                  'bg-indigo-50': selectedStudents.includes(student.id),
                }"
              >
                <div class="flex items-center">
                  <img
                    :src="student.avatar"
                    :alt="student.name"
                    class="h-8 w-8 rounded-full object-cover border border-gray-200"
                  />
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">
                      {{ student.name }}
                    </p>
                    <p class="text-xs text-gray-500">{{ student.email }}</p>
                  </div>
                </div>

                <div>
                  <input
                    :id="`student-${student.id}`"
                    type="checkbox"
                    :value="student.id"
                    :checked="selectedStudents.includes(student.id)"
                    @change="toggleStudentSelection(student.id)"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                </div>
              </li>
            </ul>
          </div>

          <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">
              {{ selectedStudents.length }} students selected
            </div>
            <button
              @click="selectedStudents = students.map((s) => s.id)"
              class="text-sm text-indigo-600 hover:text-indigo-800"
            >
              Select All
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal for Recurring Classes -->
    <div
      v-if="showConfirmationModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg shadow-xl p-6 sm:p-8 max-w-md w-full">
        <div class="text-center mb-6">
          <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4"
          >
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
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            Schedule Recurring Classes
          </h3>
          <p class="text-sm text-gray-600">
            You are about to schedule a recurring
            {{ classSchedule.type.replace("_", " ") }}
            {{ getRecurrencePreview().toLowerCase() }}.
          </p>

          <div class="mt-4 p-3 bg-indigo-50 rounded-lg text-left">
            <h4 class="font-medium text-indigo-800">
              {{ classSchedule.title }}
            </h4>
            <div class="text-sm text-gray-600 mt-1">
              <p>
                First session:
                {{ formatDate(selectedDate, "EEEE, MMMM d, yyyy") }}
              </p>
              <p>
                Time: {{ formatTime(classSchedule.startTime) }} -
                {{ formatTime(classSchedule.endTime) }}
              </p>
              <p>Recurring pattern: {{ getRecurrencePreview() }}</p>
              <p v-if="classSchedule.recurringEndDate">
                Until:
                {{ formatDate(new Date(classSchedule.recurringEndDate)) }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="showConfirmationModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Back
          </button>
          <button
            @click="scheduleClass"
            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Confirm & Schedule
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
