<!-- components/courses/QuizComponent.vue -->
<script setup lang="ts">
// Type definitions
interface QuizOption {
  id: string;
  text: string;
}

interface QuizQuestion {
  id: number;
  text: string;
  options: QuizOption[];
  correctAnswer: string;
  explanation: string;
}

interface UserAnswers {
  [key: number]: string;
}

interface QuizScore {
  total: number;
  correct: number;
  percentage: number;
}

// Props definition with types
interface Props {
  title: string;
  content?: string;
  timeLimit?: number; // Time limit in minutes (optional)
}

// Props with defaults
const props = withDefaults(defineProps<Props>(), {
  content: "",
  timeLimit: 0,
});

// Emits with type safety
const emit = defineEmits<{
  (e: "complete"): void;
  (e: "progress", progress: number): void;
}>();

// Mock quiz data (in a real app this would come from API)
const questions = ref<QuizQuestion[]>([
  {
    id: 1,
    text: "What is HTML?",
    options: [
      { id: "a", text: "Hyper Text Markup Language" },
      { id: "b", text: "High Tech Machine Learning" },
      { id: "c", text: "Hyperlink and Text Markup Language" },
      { id: "d", text: "Home Tool Markup Language" },
    ],
    correctAnswer: "a",
    explanation:
      "HTML stands for Hyper Text Markup Language. It is the standard markup language for creating Web pages.",
  },
  {
    id: 2,
    text: "Which element is used to define a paragraph in HTML?",
    options: [
      { id: "a", text: "<paragraph>" },
      { id: "b", text: "<p>" },
      { id: "c", text: "<para>" },
      { id: "d", text: "<pg>" },
    ],
    correctAnswer: "b",
    explanation:
      "The <p> element defines a paragraph in HTML. It is a block-level element.",
  },
  {
    id: 3,
    text: "Which of the following is a valid HTML tag for the largest heading?",
    options: [
      { id: "a", text: "<heading>" },
      { id: "b", text: "<h6>" },
      { id: "c", text: "<head>" },
      { id: "d", text: "<h1>" },
    ],
    correctAnswer: "d",
    explanation:
      "The <h1> tag defines the largest and most important heading. <h6> is the smallest heading tag.",
  },
]);

// Timer functionality
const timeRemaining = ref<number>(props.timeLimit * 60); // Convert minutes to seconds
const timerActive = ref<boolean>(props.timeLimit > 0);
const timerInterval = ref<number | null>(null);

// Track user answers
const userAnswers = ref<UserAnswers>({});
const currentQuestionIndex = ref<number>(0);
const showResults = ref<boolean>(false);
const questionsSubmitted = ref<boolean>(false);

// Animation states
const optionSelected = ref<boolean>(false);
const questionTransition = ref<boolean>(false);
const resultsAnimated = ref<boolean>(false);
const optionAnimations = ref<{ [key: string]: boolean }>({});

// Handle answer selection
const selectAnswer = (questionId: number, optionId: string): void => {
  if (questionsSubmitted.value) return;

  // Reset previous animation flags
  Object.keys(optionAnimations.value).forEach((key) => {
    optionAnimations.value[key] = false;
  });

  // Set animation flag for the selected option
  const animationKey = `${questionId}-${optionId}`;
  optionAnimations.value[animationKey] = true;

  // Update selected answer
  userAnswers.value[questionId] = optionId;
  optionSelected.value = true;

  // Reset animation flag after a delay
  setTimeout(() => {
    optionSelected.value = false;
  }, 400);
};

// Move to next question with animation
const nextQuestion = (): void => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    questionTransition.value = true;

    setTimeout(() => {
      currentQuestionIndex.value++;
      questionTransition.value = false;
      // Report progress to parent
      emit(
        "progress",
        (currentQuestionIndex.value / questions.value.length) * 100
      );
    }, 300);
  } else {
    submitQuiz();
  }
};

// Move to previous question with animation
const previousQuestion = (): void => {
  if (currentQuestionIndex.value > 0) {
    questionTransition.value = true;

    setTimeout(() => {
      currentQuestionIndex.value--;
      questionTransition.value = false;
      // Report progress to parent
      emit(
        "progress",
        (currentQuestionIndex.value / questions.value.length) * 100
      );
    }, 300);
  }
};

// Submit the quiz
const submitQuiz = (): void => {
  questionsSubmitted.value = true;
  showResults.value = true;

  // Clear timer if active
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }

  // Trigger results animation after a short delay
  setTimeout(() => {
    resultsAnimated.value = true;
  }, 300);
};

// Calculate score
const calculateScore = (): QuizScore => {
  let correctCount = 0;
  questions.value.forEach((question) => {
    if (userAnswers.value[question.id] === question.correctAnswer) {
      correctCount++;
    }
  });
  return {
    total: questions.value.length,
    correct: correctCount,
    percentage: Math.round((correctCount / questions.value.length) * 100),
  };
};

// Check if current question is answered
const isCurrentQuestionAnswered = (): boolean => {
  const currentQuestion = questions.value[currentQuestionIndex.value];
  return userAnswers.value[currentQuestion.id] !== undefined;
};

// Mark quiz as complete
const completeQuiz = (): void => {
  emit("complete");
};

// Get the current question
const currentQuestion = computed<QuizQuestion>(() => {
  return questions.value[currentQuestionIndex.value];
});

// Check if an answer is correct
const isCorrectAnswer = (questionId: number, optionId: string): boolean => {
  const question = questions.value.find((q) => q.id === questionId);
  if (!question) return false;
  return question.correctAnswer === optionId;
};

// Check if all questions are answered
// const allQuestionsAnswered = computed<boolean>(() => {
//   return questions.value.every(
//     (question) => userAnswers.value[question.id] !== undefined
//   );
// });

// Score calculation after submission
const score = computed<QuizScore | null>(() => {
  if (!showResults.value) return null;
  return calculateScore();
});

// Formatted time remaining for the timer
const formattedTimeRemaining = computed<string>(() => {
  if (!timerActive.value || timeRemaining.value <= 0) return "00:00";

  const minutes = Math.floor(timeRemaining.value / 60);
  const seconds = timeRemaining.value % 60;

  return `${minutes.toString().padStart(2, "0")}:${seconds
    .toString()
    .padStart(2, "0")}`;
});

// Timer warning status
const timerWarning = computed<boolean>(() => {
  if (!timerActive.value) return false;
  return timeRemaining.value <= 60; // Warning when 1 minute or less
});

// Timer critical status
const timerCritical = computed<boolean>(() => {
  if (!timerActive.value) return false;
  return timeRemaining.value <= 30; // Critical when 30 seconds or less
});

// Start the timer
const startTimer = (): void => {
  if (!props.timeLimit || timerInterval.value) return;

  timerInterval.value = window.setInterval(() => {
    if (timeRemaining.value <= 0) {
      // Time's up - submit quiz
      if (timerInterval.value) {
        clearInterval(timerInterval.value);
        timerInterval.value = null;
      }
      submitQuiz();
      return;
    }

    timeRemaining.value--;
  }, 1000);
};

// Progress animation for the results gauge
const gaugeAnimation = ref<boolean>(false);

// Animate the gauge once results are shown
watch(showResults, (newValue) => {
  if (newValue) {
    setTimeout(() => {
      gaugeAnimation.value = true;
    }, 500);
  }
});

// Start timer when component is mounted
onMounted(() => {
  if (props.timeLimit > 0) {
    startTimer();
  }

  // Report initial progress to parent
  emit("progress", (currentQuestionIndex.value / questions.value.length) * 100);
});
</script>

<template>
  <div class="p-6 quiz-container">
    <div class="mb-4 flex justify-between items-center">
      <h2 class="text-xl font-bold">Quiz: {{ title }}</h2>

      <!-- Timer (if time limit is set) -->
      <div v-if="timerActive" class="flex items-center">
        <div
          class="px-3 py-1 rounded-full flex items-center transition-colors duration-300"
          :class="[
            timerCritical
              ? 'bg-red-100 text-red-700 animate-pulse'
              : timerWarning
              ? 'bg-yellow-100 text-yellow-700'
              : 'bg-gray-100 text-gray-700',
          ]"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 mr-1"
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
          <span class="font-medium">{{ formattedTimeRemaining }}</span>
        </div>
      </div>
    </div>

    <p v-if="content" class="mb-6 text-gray-600">{{ content }}</p>

    <!-- Quiz progress bar -->
    <div class="mb-6">
      <div class="flex justify-between text-sm text-gray-600 mb-1">
        <span>Quiz Progress</span>
        <span>{{ currentQuestionIndex + 1 }} of {{ questions.length }}</span>
      </div>
      <div class="bg-gray-200 rounded-full h-2.5 overflow-hidden">
        <div
          class="bg-indigo-600 h-full transition-all duration-700 ease-in-out"
          :style="{
            width: `${((currentQuestionIndex + 1) / questions.length) * 100}%`,
          }"
        />
      </div>
    </div>

    <!-- Results view -->
    <div
      v-if="showResults"
      class="bg-white rounded-lg border border-gray-200 p-6 transition-opacity duration-500"
      :class="{ 'opacity-0': !resultsAnimated, 'opacity-100': resultsAnimated }"
    >
      <div class="text-center mb-6">
        <div
          class="inline-block p-4 bg-indigo-100 rounded-full mb-4 animate-bounce-once"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 text-indigo-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>
        <h3 class="text-2xl font-bold mb-2 animate-fade-in">Quiz Completed!</h3>
        <p class="text-gray-600 mb-4 animate-fade-in-delayed">
          You scored {{ score?.correct }} out of {{ score?.total }} ({{
            score?.percentage
          }}%)
        </p>

        <!-- Score gauge -->
        <div class="w-36 h-36 mx-auto mb-4 relative">
          <svg viewBox="0 0 24 24" class="w-full h-full">
            <!-- Background circle -->
            <path
              class="stroke-current text-gray-200"
              fill="none"
              stroke-width="2.5"
              d="M12 2.0845
                a 9.9155 9.9155 0 0 1 0 19.831
                a 9.9155 9.9155 0 0 1 0 -19.831"
            />
            <!-- Foreground circle with animation -->
            <path
              class="stroke-current transition-all duration-1000 ease-out"
              :class="[
                (score?.percentage ?? 0) >= 70
                  ? 'text-green-500'
                  : (score?.percentage ?? 0) >= 40
                  ? 'text-yellow-500'
                  : 'text-red-500',
              ]"
              fill="none"
              stroke-width="2.5"
              stroke-linecap="round"
              :stroke-dasharray="
                gaugeAnimation ? `${score?.percentage}, 100` : '0, 100'
              "
              d="M12 2.0845
                a 9.9155 9.9155 0 0 1 0 19.831
                a 9.9155 9.9155 0 0 1 0 -19.831"
            />
            <!-- Percentage text with counter animation -->
            <text
              x="50%"
              y="50%"
              class="text-2xl font-bold"
              text-anchor="middle"
              dominant-baseline="middle"
              dy=".1em"
            >
              {{ gaugeAnimation ? score?.percentage : 0 }}%
            </text>
          </svg>
        </div>
      </div>

      <!-- Question review -->
      <div class="mt-8 space-y-6">
        <h4 class="font-bold text-lg">Review Questions</h4>
        <div
          v-for="(question, questionIndex) in questions"
          :key="question.id"
          class="p-4 border border-gray-200 rounded-lg transform transition-all duration-500"
          :class="{
            'translate-y-0 opacity-100': resultsAnimated,
            'translate-y-8 opacity-0': !resultsAnimated,
          }"
          :style="{
            transitionDelay: `${questionIndex * 150}ms`,
          }"
        >
          <div class="font-medium mb-3 flex items-start">
            <span
              class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-indigo-100 text-indigo-700 text-sm font-bold mr-2"
            >
              {{ questionIndex + 1 }}
            </span>
            <span>{{ question.text }}</span>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div
              v-for="option in question.options"
              :key="option.id"
              class="p-3 rounded-lg border transition-all duration-300"
              :class="[
                userAnswers[question.id] === option.id &&
                isCorrectAnswer(question.id, option.id)
                  ? 'bg-green-50 border-green-300 text-green-800'
                  : userAnswers[question.id] === option.id
                  ? 'bg-red-50 border-red-300 text-red-800'
                  : isCorrectAnswer(question.id, option.id)
                  ? 'bg-green-50 border-green-300 text-green-800'
                  : 'border-gray-200',
              ]"
            >
              <div class="flex items-center">
                <span class="mr-2">{{ option.id.toUpperCase() }}:</span>
                <span>{{ option.text }}</span>
                <span
                  v-if="
                    userAnswers[question.id] === option.id &&
                    isCorrectAnswer(question.id, option.id)
                  "
                  class="ml-auto inline-flex items-center justify-center h-5 w-5 rounded-full bg-green-500 text-white"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>
                <span
                  v-else-if="userAnswers[question.id] === option.id"
                  class="ml-auto inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-white"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>
                <span
                  v-else-if="isCorrectAnswer(question.id, option.id)"
                  class="ml-auto inline-flex items-center justify-center h-5 w-5 rounded-full bg-green-500 text-white"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </span>
              </div>
            </div>
          </div>
          <div class="text-sm bg-indigo-50 p-3 rounded-lg text-gray-700">
            <span class="font-medium">Explanation:</span>
            {{ question.explanation }}
          </div>
        </div>
      </div>

      <!-- Action button -->
      <div class="mt-8 text-center">
        <UiButton
          variant="primary"
          state="default"
          size="lg"
          @click="completeQuiz"
        >
          Complete & Continue
        </UiButton>
      </div>
    </div>

    <!-- Question view -->
    <div v-else class="bg-white rounded-lg border border-gray-200 p-6">
      <transition
        name="question-fade"
        mode="out-in"
        @before-leave="questionTransition = true"
        @after-enter="questionTransition = false"
      >
        <div :key="currentQuestionIndex">
          <div class="mb-6">
            <div class="flex items-center mb-2">
              <span
                class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-indigo-100 text-indigo-700 text-sm font-bold mr-2"
              >
                {{ currentQuestionIndex + 1 }}
              </span>
              <span class="text-sm text-gray-500">
                Question {{ currentQuestionIndex + 1 }} of
                {{ questions.length }}
              </span>
            </div>
            <h3 class="text-xl font-medium">{{ currentQuestion.text }}</h3>
          </div>

          <!-- Options -->
          <div class="space-y-3 mb-8">
            <div
              v-for="option in currentQuestion.options"
              :key="option.id"
              class="relative p-4 border rounded-lg cursor-pointer transition-all duration-200"
              :class="[
                userAnswers[currentQuestion.id] === option.id
                  ? 'border-indigo-600 bg-indigo-50 shadow-md transform'
                  : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50',
                optionAnimations[`${currentQuestion.id}-${option.id}`]
                  ? 'scale-102'
                  : '',
              ]"
              @click="selectAnswer(currentQuestion.id, option.id)"
            >
              <div class="flex items-center">
                <div
                  class="w-5 h-5 rounded-full border mr-3 flex items-center justify-center transition-all duration-300"
                  :class="[
                    userAnswers[currentQuestion.id] === option.id
                      ? 'border-indigo-600 bg-indigo-600 scale-110'
                      : 'border-gray-400',
                  ]"
                >
                  <svg
                    v-if="userAnswers[currentQuestion.id] === option.id"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-3 w-3 text-white animate-scale-in"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="flex items-center">
                  <span class="font-medium mr-2 text-gray-700"
                    >{{ option.id.toUpperCase() }}:</span
                  >
                  <span
                    :class="{
                      'font-medium text-indigo-700':
                        userAnswers[currentQuestion.id] === option.id,
                    }"
                  >
                    {{ option.text }}
                  </span>
                </div>
              </div>

              <!-- Option highlight effect -->
              <div
                v-if="userAnswers[currentQuestion.id] === option.id"
                class="absolute inset-0 bg-indigo-500 opacity-0 rounded-lg animate-pulse-once pointer-events-none"
              />
            </div>
          </div>

          <!-- Navigation buttons -->
          <div class="flex justify-between">
            <UiButton
              v-if="currentQuestionIndex > 0"
              variant="ghost"
              state="default"
              size="md"
              @click="previousQuestion"
            >
              <template #prefix>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </template>
              Previous
            </UiButton>
            <div v-else />

            <UiButton
              :variant="isCurrentQuestionAnswered() ? 'primary' : 'ghost'"
              state="default"
              size="md"
              :disabled="!isCurrentQuestionAnswered()"
              @click="nextQuestion"
            >
              {{
                currentQuestionIndex === questions.length - 1
                  ? "Submit Quiz"
                  : "Next Question"
              }}
              <template #suffix>
                <svg
                  v-if="currentQuestionIndex < questions.length - 1"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </template>
            </UiButton>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<style scoped>
/* Base animations */
.quiz-container {
  --quiz-primary: #4f46e5; /* indigo-600 */
  --quiz-primary-hover: #4338ca; /* indigo-700 */
  --quiz-light: #eef2ff; /* indigo-50 */
  --quiz-success: #22c55e; /* green-500 */
  --quiz-error: #ef4444; /* red-500 */
  --quiz-warning: #eab308; /* yellow-500 */
}

/* Question transition animations */
.question-fade-enter-active,
.question-fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.question-fade-enter-from,
.question-fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

.question-fade-enter-to,
.question-fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}

/* Custom animations */
@keyframes scale-in {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes bounce-once {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-12px);
  }
  70% {
    transform: translateY(-6px);
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse-once {
  0% {
    opacity: 0.2;
  }
  50% {
    opacity: 0.1;
  }
  100% {
    opacity: 0;
  }
}

/* Applied animations */
.animate-scale-in {
  animation: scale-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.animate-bounce-once {
  animation: bounce-once 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

.animate-fade-in-delayed {
  animation: fade-in 0.5s ease-out 0.2s forwards;
  opacity: 0;
}

.animate-pulse-once {
  animation: pulse-once 0.7s ease-out forwards;
}

.scale-102 {
  transform: scale(1.02);
}

/* Progress animations for the gauge */
@keyframes progress {
  0% {
    stroke-dasharray: 0 100;
  }
}

.animate-progress {
  animation: progress 1s ease-out forwards;
}

/* Timer animation for critical state */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

.animate-pulse {
  animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
