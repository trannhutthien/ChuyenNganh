<template>
  <div class="grid grid-cols-1 gap-4" :class="gridClass">
    <!-- Correct Answers Stat -->
    <div 
      v-if="showCorrectAnswers"
      class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border-2 transition-all duration-200 hover:shadow-md"
      :class="correctAnswersBorderClass"
    >
      <!-- Icon -->
      <div 
        class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center"
        :class="correctAnswersBgClass"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
          <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-gray-600 mb-1">{{ correctAnswersLabel }}</div>
        <div class="text-2xl font-bold" :class="correctAnswersTextClass">
          {{ correctAnswers }}<span class="text-gray-400">/{{ totalQuestions }}</span>
        </div>
        <div class="text-xs text-gray-500 mt-1">
          {{ accuracyPercentage }}% chính xác
        </div>
      </div>
    </div>

    <!-- Time Taken Stat -->
    <div 
      v-if="showTimeTaken"
      class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border-2 transition-all duration-200 hover:shadow-md"
      :class="timeTakenBorderClass"
    >
      <!-- Icon -->
      <div 
        class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center"
        :class="timeTakenBgClass"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-gray-600 mb-1">{{ timeTakenLabel }}</div>
        <div class="text-2xl font-bold" :class="timeTakenTextClass">
          {{ timeTaken }}
        </div>
        <div class="text-xs text-gray-500 mt-1">
          {{ timeTakenSubtext }}
        </div>
      </div>
    </div>

    <!-- Passing Score Stat -->
    <div 
      v-if="showPassingScore"
      class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border-2 transition-all duration-200 hover:shadow-md"
      :class="passingScoreBorderClass"
    >
      <!-- Icon -->
      <div 
        class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center"
        :class="passingScoreBgClass"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
          <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-gray-600 mb-1">{{ passingScoreLabel }}</div>
        <div class="text-2xl font-bold" :class="passingScoreTextClass">
          {{ passingScore }}%
        </div>
        <div class="text-xs mt-1" :class="passedStatusClass">
          {{ passed ? '✓ Đã đạt' : '✗ Chưa đạt' }}
        </div>
      </div>
    </div>

    <!-- Score Stat -->
    <div 
      v-if="showScore"
      class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border-2 transition-all duration-200 hover:shadow-md"
      :class="scoreBorderClass"
    >
      <!-- Icon -->
      <div 
        class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center"
        :class="scoreBgClass"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
          <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
          <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
        </svg>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-gray-600 mb-1">{{ scoreLabel }}</div>
        <div class="text-2xl font-bold" :class="scoreTextClass">
          {{ score }}<span class="text-gray-400">/100</span>
        </div>
        <div class="text-xs text-gray-500 mt-1">
          {{ scoreSubtext }}
        </div>
      </div>
    </div>

    <!-- Custom Stats Slot -->
    <slot name="custom-stats" />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Số câu trả lời đúng
  correctAnswers: {
    type: Number,
    default: 0
  },

  // Tổng số câu hỏi
  totalQuestions: {
    type: Number,
    required: true
  },

  // Thời gian làm bài
  timeTaken: {
    type: String,
    default: '0 phút'
  },

  // Điểm đạt yêu cầu
  passingScore: {
    type: Number,
    default: 70
  },

  // Điểm số
  score: {
    type: Number,
    default: 0
  },

  // Đã đạt hay chưa
  passed: {
    type: Boolean,
    default: false
  },

  // Số cột grid (responsive)
  columns: {
    type: Number,
    default: 3,
    validator: (value) => [1, 2, 3, 4].includes(value)
  },

  // Hiển thị từng stat
  showCorrectAnswers: {
    type: Boolean,
    default: true
  },

  showTimeTaken: {
    type: Boolean,
    default: true
  },

  showPassingScore: {
    type: Boolean,
    default: true
  },

  showScore: {
    type: Boolean,
    default: false
  },

  // Custom labels
  correctAnswersLabel: {
    type: String,
    default: 'Số câu đúng'
  },

  timeTakenLabel: {
    type: String,
    default: 'Thời gian làm bài'
  },

  passingScoreLabel: {
    type: String,
    default: 'Điểm đạt yêu cầu'
  },

  scoreLabel: {
    type: String,
    default: 'Tổng điểm'
  },

  // Subtext
  timeTakenSubtext: {
    type: String,
    default: 'Tổng thời gian'
  },

  scoreSubtext: {
    type: String,
    default: 'Kết quả cuối cùng'
  }
})

// Grid class
const gridClass = computed(() => {
  const cols = {
    1: 'md:grid-cols-1',
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-3',
    4: 'md:grid-cols-4'
  }
  return cols[props.columns] || cols[3]
})

// Accuracy percentage
const accuracyPercentage = computed(() => {
  if (!props.totalQuestions) return 0
  return Math.round((props.correctAnswers / props.totalQuestions) * 100)
})

// Correct Answers styling
const correctAnswersBorderClass = computed(() => 'border-green-200')
const correctAnswersBgClass = computed(() => 'bg-green-100 text-green-600')
const correctAnswersTextClass = computed(() => 'text-green-600')

// Time Taken styling
const timeTakenBorderClass = computed(() => 'border-blue-200')
const timeTakenBgClass = computed(() => 'bg-blue-100 text-blue-600')
const timeTakenTextClass = computed(() => 'text-blue-600')

// Passing Score styling
const passingScoreBorderClass = computed(() => 'border-amber-200')
const passingScoreBgClass = computed(() => 'bg-amber-100 text-amber-600')
const passingScoreTextClass = computed(() => 'text-amber-600')
const passedStatusClass = computed(() => 
  props.passed ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'
)

// Score styling
const scoreBorderClass = computed(() => 
  props.passed ? 'border-green-200' : 'border-red-200'
)
const scoreBgClass = computed(() => 
  props.passed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
)
const scoreTextClass = computed(() => 
  props.passed ? 'text-green-600' : 'text-red-600'
)
</script>
