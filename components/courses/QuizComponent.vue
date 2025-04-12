<!-- components/courses/QuizComponent.vue -->
<script setup lang="ts">
export interface QuizQuestion {
  id: string;
  question: string;
  explanation?: string;
  type: "single" | "multiple" | "true-false" | "text";
  options?: QuizOption[];
  correctAnswer?: string | string[];
  points: number;
  image?: string;
}

export interface QuizOption {
  id: string;
  text: string;
  isCorrect?: boolean;
}

export interface QuizResult {
  score: number;
  maxScore: number;
  percentage: number;
  passingScore: number;
  passed: boolean;
  answers: Record<string, string | string[]>;
  correctAnswers: Record<string, string | string[]>;
  questionsWithExplanations: QuizQuestion[];
}

interface QuizComponentProps {
  quizId: string;
  title: string;
  description?: string;
  timeLimit?: number; // in minutes, 0 for no limit
  passingScore?: number; // percentage, e.g. 70
  questions: QuizQuestion[];
  shuffleQuestions?: boolean;
  shuffleOptions?: boolean;
  showCorrectAnswers?: boolean;
  showExplanations?: boolean;
  allowRetry?: boolean;
}

const props = withDefaults(defineProps<QuizComponentProps>(), {
  description: "",
  timeLimit: 0,
  passingScore: 70,
  shuffleQuestions: false,
  shuffleOptions: false,
  showCorrectAnswers: true,
  showExplanations: true,
  allowRetry: true,
});

const emit = defineEmits<{
  (e: "complete", result: QuizResult): void;
  (e: "retry"): void;
}>();

// Quiz state
const isStarted = ref(false);
const isSubmitted = ref(false);
const isCompleted = ref(false);
const currentQuestionIndex = ref(0);
const answers = ref<Record<string, string | string[]>>({});
const timeRemaining = ref(props.timeLimit * 60); // convert to seconds
const timerInterval = ref<number | null>(null);

// Initialize the quiz with shuffled questions/options if needed
const quizQuestions = computed(() => {
  let questions = [...props.questions];

  if (props.shuffleQuestions) {
    questions = shuffleArray(questions);
  }

  if (props.shuffleOptions) {
    questions = questions.map((question) => {
      if (question.options) {
        const shuffledOptions = shuffleArray([...question.options]);
        return { ...question, options: shuffledOptions };
      }
      return question;
    });
  }

  return questions;
});

// Get the current question
const currentQuestion = computed(() => {
  return quizQuestions.value[currentQuestionIndex.value];
});

// Track progress
const progress = computed(() => {
  return (currentQuestionIndex.value / quizQuestions.value.length) * 100;
});

// Count of answered questions
const answeredQuestions = computed(() => {
  return Object.keys(answers.value).length;
});

// Check if current question is answered
const isCurrentQuestionAnswered = computed(() => {
  return !!answers.value[currentQuestion.value?.id];
});

// Format time remaining
const formattedTimeRemaining = computed(() => {
  if (props.timeLimit === 0) return "";

  const minutes = Math.floor(timeRemaining.value / 60);
  const seconds = timeRemaining.value % 60;

  return `${minutes.toString().padStart(2, "0")}:${seconds
    .toString()
    .padStart(2, "0")}`;
});

// Functions
// Shuffle array (Fisher-Yates algorithm)
function shuffleArray<T>(array: T[]): T[] {
  const newArray = [...array];
  for (let i = newArray.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
  }
  return newArray;
}

// Start the quiz
const startQuiz = () => {
  isStarted.value = true;

  // Start timer if there's a time limit
  if (props.timeLimit > 0) {
    timeRemaining.value = props.timeLimit * 60;
    startTimer();
  }
};

// Start the timer
const startTimer = () => {
  timerInterval.value = window.setInterval(() => {
    if (timeRemaining.value > 0) {
      timeRemaining.value--;
    } else {
      submitQuiz();
    }
  }, 1000);
};

// Stop the timer
const stopTimer = () => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
    timerInterval.value = null;
  }
};

// Navigate to the next question
const nextQuestion = () => {
  if (currentQuestionIndex.value < quizQuestions.value.length - 1) {
    currentQuestionIndex.value++;
  }
};

// Navigate to the previous question
const prevQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--;
  }
};

// Navigate to a specific question
const goToQuestion = (index: number) => {
  if (index >= 0 && index < quizQuestions.value.length) {
    currentQuestionIndex.value = index;
  }
};

// Handle single choice selection
const selectSingleChoice = (questionId: string, optionId: string) => {
  answers.value[questionId] = optionId;
};

// Handle multiple choice selection
const toggleMultipleChoice = (questionId: string, optionId: string) => {
  if (!answers.value[questionId]) {
    answers.value[questionId] = [optionId];
  } else {
    const currentAnswers = answers.value[questionId] as string[];
    const index = currentAnswers.indexOf(optionId);

    if (index === -1) {
      // Add to selected options
      answers.value[questionId] = [...currentAnswers, optionId];
    } else {
      // Remove from selected options
      answers.value[questionId] = currentAnswers.filter(
        (id) => id !== optionId
      );

      // If array is empty, delete the property to maintain answeredQuestions count
      if ((answers.value[questionId] as string[]).length === 0) {
        delete answers.value[questionId];
      }
    }
  }
};

// Handle text input
const updateTextAnswer = (questionId: string, text: string) => {
  if (text.trim() === "") {
    delete answers.value[questionId];
  } else {
    answers.value[questionId] = text.trim();
  }
};

// Handle true/false selection
const selectTrueFalse = (questionId: string, value: boolean) => {
  answers.value[questionId] = value ? "true" : "false";
};

// Check if an option is selected (single choice)
const isOptionSelected = (questionId: string, optionId: string): boolean => {
  return answers.value[questionId] === optionId;
};

// Check if an option is selected (multiple choice)
const isMultipleOptionSelected = (
  questionId: string,
  optionId: string
): boolean => {
  if (!answers.value[questionId]) return false;
  return (answers.value[questionId] as string[]).includes(optionId);
};

// Check if a true/false option is selected
const isTrueFalseSelected = (questionId: string, value: boolean): boolean => {
  return answers.value[questionId] === (value ? "true" : "false");
};

// Submit the quiz
const submitQuiz = () => {
  stopTimer();
  isSubmitted.value = true;

  // Calculate results
  const result = calculateResults();

  // Emit the result
  emit("complete", result);

  // Mark as completed
  isCompleted.value = true;
};

// Calculate quiz results
const calculateResults = (): QuizResult => {
  let totalScore = 0;
  let maxScore = 0;
  const correctAnswers: Record<string, string | string[]> = {};
  const questionsWithExplanations: QuizQuestion[] = [];

  quizQuestions.value.forEach((question) => {
    maxScore += question.points;

    // Get correct answer(s)
    if (question.type === "multiple") {
      const correctOptions =
        question.options
          ?.filter((option) => option.isCorrect)
          .map((option) => option.id) || [];
      correctAnswers[question.id] = correctOptions;

      // Check if answer is correct
      const userAnswer = (answers.value[question.id] as string[]) || [];
      if (
        correctOptions.length === userAnswer.length &&
        correctOptions.every((option) => userAnswer.includes(option))
      ) {
        totalScore += question.points;
      }
    } else if (question.type === "single") {
      const correctOption =
        question.options?.find((option) => option.isCorrect)?.id || "";
      correctAnswers[question.id] = correctOption;

      // Check if answer is correct
      if (answers.value[question.id] === correctOption) {
        totalScore += question.points;
      }
    } else if (question.type === "true-false") {
      const correctOption = question.correctAnswer as string;
      correctAnswers[question.id] = correctOption;

      // Check if answer is correct
      if (answers.value[question.id] === correctOption) {
        totalScore += question.points;
      }
    } else if (question.type === "text") {
      const correctAnswer = question.correctAnswer as string;
      correctAnswers[question.id] = correctAnswer;

      // Check if answer is correct (case insensitive)
      const userAnswer = (
        (answers.value[question.id] as string) || ""
      ).toLowerCase();
      if (userAnswer === correctAnswer.toLowerCase()) {
        totalScore += question.points;
      }
    }

    // Add question with explanation
    if (question.explanation) {
      questionsWithExplanations.push(question);
    }
  });

  const percentage = Math.round((totalScore / maxScore) * 100) || 0;
  const passed = percentage >= props.passingScore;

  return {
    score: totalScore,
    maxScore: maxScore,
    percentage: percentage,
    passingScore: props.passingScore,
    passed: passed,
    answers: answers.value,
    correctAnswers: correctAnswers,
    questionsWithExplanations: questionsWithExplanations,
  };
};

// Retry the quiz
const retryQuiz = () => {
  // Reset quiz state
  isSubmitted.value = false;
  isCompleted.value = false;
  currentQuestionIndex.value = 0;
  answers.value = {};

  // Restart timer if needed
  if (props.timeLimit > 0) {
    timeRemaining.value = props.timeLimit * 60;
    startTimer();
  }

  // Emit retry event
  emit("retry");
};

// Clean up timer on component unmount
watch(
  () => isCompleted.value,
  (newValue) => {
    if (newValue) {
      stopTimer();
    }
  }
);

watch(
  () => isStarted.value,
  (newValue) => {
    if (!newValue) {
      stopTimer();
    }
  }
);

// Computed result
const quizResult = computed(() => {
  if (!isSubmitted.value) return null;
  return calculateResults();
});
</script>

<template>
  <div class="quiz-component bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Quiz intro (shown before starting) -->
    <div v-if="!isStarted" class="p-6">
      <h2 class="text-2xl font-bold mb-4">{{ title }}</h2>

      <div v-if="description" class="mb-6 text-gray-600">
        {{ description }}
      </div>

      <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-500 mr-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <span class="text-gray-600">
              {{ quizQuestions.length }} Questions
            </span>
          </div>

          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-500 mr-2"
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
            <span class="text-gray-600">
              {{ timeLimit ? `${timeLimit} Minutes` : "No Time Limit" }}
            </span>
          </div>

          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-500 mr-2"
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
            <span class="text-gray-600">
              Passing Score: {{ passingScore }}%
            </span>
          </div>

          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-500 mr-2"
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
            <span class="text-gray-600">
              {{ allowRetry ? "Retries Allowed" : "No Retries" }}
            </span>
          </div>
        </div>
      </div>

      <button
        @click="startQuiz"
        class="w-full py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
      >
        Start Quiz
      </button>
    </div>

    <!-- Quiz in progress -->
    <div v-else-if="!isSubmitted" class="quiz-in-progress">
      <!-- Quiz header -->
      <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 class="font-bold text-lg">
          Question {{ currentQuestionIndex + 1 }} of {{ quizQuestions.length }}
        </h3>

        <!-- Timer -->
        <div
          v-if="timeLimit > 0"
          :class="[
            'text-lg font-medium rounded-lg px-3 py-1',
            timeRemaining < 60 ? 'text-red-600 bg-red-100' : 'text-gray-700',
          ]"
        >
          <span class="mr-1">⏱</span> {{ formattedTimeRemaining }}
        </div>
      </div>

      <!-- Progress bar -->
      <div class="w-full h-1 bg-gray-200">
        <div
          class="h-full bg-indigo-600 transition-all duration-300"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>

      <!-- Question content -->
      <div class="p-6">
        <!-- Question text -->
        <div class="mb-6">
          <p class="text-xl font-medium mb-2">
            {{ currentQuestion.question }}
          </p>

          <!-- Question image if present -->
          <img
            v-if="currentQuestion.image"
            :src="currentQuestion.image"
            alt="Question image"
            class="max-w-full h-auto rounded-lg mb-4"
          />

          <!-- Points display -->
          <div class="text-sm text-gray-500">
            Points: {{ currentQuestion.points }}
          </div>
        </div>

        <!-- Question options based on type -->
        <!-- Single choice question -->
        <div v-if="currentQuestion.type === 'single'" class="space-y-3">
          <div
            v-for="option in currentQuestion.options"
            :key="option.id"
            @click="selectSingleChoice(currentQuestion.id, option.id)"
            :class="[
              'p-4 border rounded-lg cursor-pointer transition-all duration-200 flex items-start',
              isOptionSelected(currentQuestion.id, option.id)
                ? 'border-indigo-600 bg-indigo-50'
                : 'border-gray-300 hover:border-indigo-300',
            ]"
          >
            <div class="flex-shrink-0 mt-0.5">
              <div
                class="w-5 h-5 rounded-full border flex items-center justify-center mr-3"
                :class="
                  isOptionSelected(currentQuestion.id, option.id)
                    ? 'border-indigo-600 bg-indigo-600'
                    : 'border-gray-400'
                "
              >
                <div
                  v-if="isOptionSelected(currentQuestion.id, option.id)"
                  class="w-2 h-2 rounded-full bg-white"
                ></div>
              </div>
            </div>
            <div>{{ option.text }}</div>
          </div>
        </div>

        <!-- Multiple choice question -->
        <div v-else-if="currentQuestion.type === 'multiple'" class="space-y-3">
          <div
            v-for="option in currentQuestion.options"
            :key="option.id"
            @click="toggleMultipleChoice(currentQuestion.id, option.id)"
            :class="[
              'p-4 border rounded-lg cursor-pointer transition-all duration-200 flex items-start',
              isMultipleOptionSelected(currentQuestion.id, option.id)
                ? 'border-indigo-600 bg-indigo-50'
                : 'border-gray-300 hover:border-indigo-300',
            ]"
          >
            <div class="flex-shrink-0 mt-0.5">
              <div
                class="w-5 h-5 rounded border flex items-center justify-center mr-3"
                :class="
                  isMultipleOptionSelected(currentQuestion.id, option.id)
                    ? 'border-indigo-600 bg-indigo-600'
                    : 'border-gray-400'
                "
              >
                <svg
                  v-if="isMultipleOptionSelected(currentQuestion.id, option.id)"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-3 w-3 text-white"
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
            </div>
            <div>{{ option.text }}</div>
          </div>
        </div>

        <!-- True/False question -->
        <div
          v-else-if="currentQuestion.type === 'true-false'"
          class="space-y-3"
        >
          <div
            @click="selectTrueFalse(currentQuestion.id, true)"
            :class="[
              'p-4 border rounded-lg cursor-pointer transition-all duration-200 flex items-start',
              isTrueFalseSelected(currentQuestion.id, true)
                ? 'border-indigo-600 bg-indigo-50'
                : 'border-gray-300 hover:border-indigo-300',
            ]"
          >
            <div class="flex-shrink-0 mt-0.5">
              <div
                class="w-5 h-5 rounded-full border flex items-center justify-center mr-3"
                :class="
                  isTrueFalseSelected(currentQuestion.id, true)
                    ? 'border-indigo-600 bg-indigo-600'
                    : 'border-gray-400'
                "
              >
                <div
                  v-if="isTrueFalseSelected(currentQuestion.id, true)"
                  class="w-2 h-2 rounded-full bg-white"
                ></div>
              </div>
            </div>
            <div>True</div>
          </div>

          <div
            @click="selectTrueFalse(currentQuestion.id, false)"
            :class="[
              'p-4 border rounded-lg cursor-pointer transition-all duration-200 flex items-start',
              isTrueFalseSelected(currentQuestion.id, false)
                ? 'border-indigo-600 bg-indigo-50'
                : 'border-gray-300 hover:border-indigo-300',
            ]"
          >
            <div class="flex-shrink-0 mt-0.5">
              <div
                class="w-5 h-5 rounded-full border flex items-center justify-center mr-3"
                :class="
                  isTrueFalseSelected(currentQuestion.id, false)
                    ? 'border-indigo-600 bg-indigo-600'
                    : 'border-gray-400'
                "
              >
                <div
                  v-if="isTrueFalseSelected(currentQuestion.id, false)"
                  class="w-2 h-2 rounded-full bg-white"
                ></div>
              </div>
            </div>
            <div>False</div>
          </div>
        </div>

        <!-- Text input question -->
        <div v-else-if="currentQuestion.type === 'text'" class="space-y-3">
          <textarea
            :value="answers[currentQuestion.id] as string || ''"
            @input="
              updateTextAnswer(
                currentQuestion.id,
                ($event.target as HTMLTextAreaElement)?.value
              )
            "
            rows="4"
            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Type your answer here..."
          ></textarea>
        </div>
      </div>

      <!-- Navigation buttons -->
      <div class="px-6 py-4 border-t flex justify-between items-center">
        <div>
          <button
            v-if="currentQuestionIndex > 0"
            @click="prevQuestion"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            Previous
          </button>
        </div>

        <div class="flex items-center">
          <!-- Question navigation bullets -->
          <div class="hidden sm:flex space-x-2 mr-4">
            <button
              v-for="(question, index) in quizQuestions"
              :key="question.id"
              @click="goToQuestion(index)"
              :class="[
                'w-2.5 h-2.5 rounded-full transition-all',
                index === currentQuestionIndex
                  ? 'bg-indigo-600 scale-125'
                  : answers[question.id]
                  ? 'bg-indigo-300'
                  : 'bg-gray-300',
              ]"
              :aria-label="`Go to question ${index + 1}`"
            ></button>
          </div>

          <!-- Progress text (sm screens) -->
          <div class="sm:hidden text-sm text-gray-500 mr-4">
            {{ answeredQuestions }}/{{ quizQuestions.length }} answered
          </div>

          <button
            v-if="currentQuestionIndex < quizQuestions.length - 1"
            @click="nextQuestion"
            :disabled="!isCurrentQuestionAnswered"
            :class="[
              'px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors',
              isCurrentQuestionAnswered
                ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed',
            ]"
          >
            Next
          </button>

          <button
            v-else
            @click="submitQuiz"
            :disabled="answeredQuestions < quizQuestions.length"
            :class="[
              'px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors',
              answeredQuestions === quizQuestions.length
                ? 'bg-green-600 text-white hover:bg-green-700'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed',
            ]"
          >
            Submit Quiz
          </button>
        </div>
      </div>
    </div>

    <!-- Quiz results (after submission) -->
    <div v-else class="quiz-results p-6">
      <div class="text-center mb-6">
        <div
          v-if="quizResult?.passed"
          class="inline-block p-3 rounded-full bg-green-100 text-green-600 mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div
          v-else
          class="inline-block p-3 rounded-full bg-red-100 text-red-600 mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd"
            />
          </svg>
        </div>

        <h2 class="text-2xl font-bold mb-2">
          {{
            quizResult?.passed
              ? "Congratulations! You Passed"
              : "Quiz Not Passed"
          }}
        </h2>

        <p class="text-gray-600">
          {{
            quizResult?.passed
              ? `You've successfully completed the quiz with a score of ${quizResult.percentage}%.`
              : `You scored ${quizResult?.percentage}%. The passing score is ${quizResult?.passingScore}%.`
          }}
        </p>
      </div>

      <!-- Score details -->
      <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <div
          class="flex flex-col sm:flex-row justify-between items-center gap-4"
        >
          <div class="text-center flex-1">
            <div class="text-3xl font-bold text-indigo-600">
              {{ quizResult?.score }} / {{ quizResult?.maxScore }}
            </div>
            <div class="text-sm text-gray-500">Total Score</div>
          </div>

          <div class="h-10 w-px bg-gray-300 hidden sm:block"></div>

          <div class="text-center flex-1">
            <div
              class="text-3xl font-bold"
              :class="quizResult?.passed ? 'text-green-600' : 'text-red-600'"
            >
              {{ quizResult?.percentage }}%
            </div>
            <div class="text-sm text-gray-500">Percentage</div>
          </div>

          <div class="h-10 w-px bg-gray-300 hidden sm:block"></div>

          <div class="text-center flex-1">
            <div class="text-3xl font-bold text-gray-800">
              {{ answeredQuestions }} / {{ quizQuestions.length }}
            </div>
            <div class="text-sm text-gray-500">Questions Answered</div>
          </div>
        </div>
      </div>

      <!-- Answer explanations -->
      <div
        v-if="showExplanations && quizResult?.questionsWithExplanations.length"
      >
        <h3 class="font-bold text-lg mb-4">Explanations</h3>

        <div class="space-y-4">
          <div
            v-for="question in quizResult.questionsWithExplanations"
            :key="question.id"
            class="p-4 border rounded-lg"
          >
            <p class="font-medium mb-2">
              {{ question.question }}
            </p>
            <div class="p-3 bg-indigo-50 rounded mb-2 text-gray-700">
              <div class="font-medium mb-1 text-indigo-700">Explanation:</div>
              <p>{{ question.explanation }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="mt-6 flex flex-col sm:flex-row gap-4">
        <button
          v-if="allowRetry"
          @click="retryQuiz"
          class="px-4 py-3 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        >
          Retry Quiz
        </button>

        <button
          class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          Continue to Next Lesson
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom styling for radio buttons and checkboxes */
.custom-radio,
.custom-checkbox {
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
}

/* Custom animation for timer when time is running low */
@keyframes pulse-red {
  0%,
  100% {
    background-color: rgba(254, 226, 226);
    color: rgba(220, 38, 38);
  }
  50% {
    background-color: rgba(253, 242, 242);
    color: rgba(239, 68, 68);
  }
}

.animate-pulse-red {
  animation: pulse-red 2s infinite;
}
</style>
