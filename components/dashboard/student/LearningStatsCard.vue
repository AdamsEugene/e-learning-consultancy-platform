<!-- components/dashboard/LearningStatsCard.vue -->
<script setup lang="ts">
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from "chart.js";

// Register ChartJS components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

interface ChartData {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
    backgroundColor: string;
    borderColor: string;
    borderWidth: number;
    tension: number;
  }[];
}

interface Stats {
  daily: ChartData;
  weekly: ChartData;
  monthly: ChartData;
}

interface Props {
  stats: Stats;
  animationDelay?: number;
  isLoaded?: boolean;
}

const props = defineProps<Props>();

// Active time range
const activeRange = ref<"daily" | "weekly" | "monthly">("daily");

// Format chart data for Chart.js
const chartData = computed(() => {
  const data = props.stats[activeRange.value];
  return {
    labels: data.labels,
    datasets: [
      {
        label: "Hours Spent",
        data: data.datasets[0].data,
        backgroundColor: "rgba(99, 102, 241, 0.2)", // Indigo with opacity
        borderColor: "rgba(99, 102, 241, 1)", // Solid indigo
        borderWidth: 2,
        tension: 0.4, // Smooth curve
        fill: true, // Fill area under the line
        pointBackgroundColor: "#ffffff",
        pointBorderColor: "rgba(99, 102, 241, 1)",
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointHoverBackgroundColor: "rgba(99, 102, 241, 1)",
        pointHoverBorderColor: "#ffffff",
      },
    ],
  };
});

// Configure chart options
const chartOptions = computed(() => {
  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false, // Hide legend since we only have one dataset
      },
      tooltip: {
        backgroundColor: "rgba(255, 255, 255, 0.9)",
        titleColor: "#1f2937", // gray-800
        bodyColor: "#6366f1", // indigo-600
        titleFont: {
          size: 13,
          weight: "bold",
        },
        bodyFont: {
          size: 13,
        },
        padding: 12,
        borderColor: "rgba(229, 231, 235, 1)", // gray-200
        borderWidth: 1,
        displayColors: false,
        callbacks: {
          label: function (context) {
            return `${context.raw.toFixed(1)} hours`;
          },
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: "rgba(243, 244, 246, 1)", // gray-100
        },
        border: {
          display: true,
          color: "rgba(229, 231, 235, 1)", // gray-200
        },
        ticks: {
          color: "#6b7280", // gray-500
          font: {
            size: 11,
          },
          callback: function (value) {
            return `${value}h`;
          },
        },
      },
      x: {
        grid: {
          color: "rgba(243, 244, 246, 1)", // gray-100
          display: false,
        },
        border: {
          display: true,
          color: "rgba(229, 231, 235, 1)", // gray-200
        },
        ticks: {
          color: "#6b7280", // gray-500
          font: {
            size: 11,
          },
        },
      },
    },
    elements: {
      line: {
        tension: 0.4, // Smooth curves
      },
    },
    interaction: {
      mode: "index",
      intersect: false,
    },
    animations: {
      tension: {
        duration: 1000,
        easing: "easeOutQuart",
        from: 0.8,
        to: 0.4,
        loop: false,
      },
    },
  };
});

// Calculate total hours for the selected period
const totalHours = computed(() => {
  const data = props.stats[activeRange.value].datasets[0].data;
  return data.reduce((sum, value) => sum + value, 0);
});

// Switch time range
const switchTimeRange = (range: "daily" | "weekly" | "monthly") => {
  activeRange.value = range;
};
</script>

<template>
  <div
    class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 fade-in-up"
    :class="{ loaded: isLoaded }"
    :style="{ 'transition-delay': `${animationDelay}ms` }"
  >
    <div class="px-6 py-5 border-b border-gray-100">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900">Learning Activity</h3>

        <!-- Time range selector -->
        <div class="flex rounded-lg border border-gray-200 bg-gray-50 p-1">
          <button
            @click="switchTimeRange('daily')"
            class="px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
            :class="
              activeRange === 'daily'
                ? 'bg-white text-indigo-600 shadow-sm'
                : 'text-gray-600 hover:text-indigo-600'
            "
          >
            Daily
          </button>
          <button
            @click="switchTimeRange('weekly')"
            class="px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
            :class="
              activeRange === 'weekly'
                ? 'bg-white text-indigo-600 shadow-sm'
                : 'text-gray-600 hover:text-indigo-600'
            "
          >
            Weekly
          </button>
          <button
            @click="switchTimeRange('monthly')"
            class="px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200"
            :class="
              activeRange === 'monthly'
                ? 'bg-white text-indigo-600 shadow-sm'
                : 'text-gray-600 hover:text-indigo-600'
            "
          >
            Monthly
          </button>
        </div>
      </div>
    </div>

    <div class="p-6">
      <!-- Total hours summary -->
      <div class="mb-6 text-center">
        <p class="text-sm text-gray-500 mb-1">TOTAL HOURS</p>
        <h4 class="text-3xl font-bold text-gray-900">
          {{ totalHours.toFixed(1) }}
        </h4>
        <p class="text-gray-600 text-sm">
          <span v-if="activeRange === 'daily'">this week</span>
          <span v-else-if="activeRange === 'weekly'">this month</span>
          <span v-else>last 6 months</span>
        </p>
      </div>

      <!-- Chart -->
      <div class="h-60">
        <Line
          :data="chartData"
          :options="chartOptions"
          class="chart-animation"
        />
      </div>

      <!-- Learning insights -->
      <div class="mt-6">
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-indigo-600"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <h4 class="text-sm font-medium text-indigo-800">
                Learning Insight
              </h4>
              <div class="mt-1 text-sm text-indigo-700">
                <p v-if="activeRange === 'daily'">
                  You're most productive on weekends, with Saturday being your
                  peak learning day.
                </p>
                <p v-else-if="activeRange === 'weekly'">
                  Your learning consistency has improved by 18% compared to last
                  month.
                </p>
                <p v-else>
                  You've increased your monthly learning time by 8.5 hours since
                  you started.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-in-up {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-up.loaded {
  opacity: 1;
  transform: translateY(0);
}

.chart-animation {
  animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}
</style>
