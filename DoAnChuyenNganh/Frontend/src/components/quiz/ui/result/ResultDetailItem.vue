<template>
  <div 
    class="bg-white rounded-lg border-2 shadow-sm transition-all duration-200 hover:shadow-md overflow-hidden"
    :class="borderClass"
  >
    <!-- Header -->
    <div 
      class="flex items-center justify-between px-4 py-3 border-b"
      :class="headerClass"
    >
      <div class="flex items-center gap-3">
        <!-- Icon -->
        <div 
          class="w-8 h-8 rounded-full flex items-center justify-center"
          :class="iconClass"
        >
          <!-- Correct Icon -->
          <svg 
            v-if="detail.is_correct" 
            xmlns="http://www.w3.org/2000/svg" 
            viewBox="0 0 24 24" 
            fill="currentColor" 
            class="w-5 h-5"
          >
            <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
          </svg>

          <!-- Incorrect Icon -->
          <svg 
            v-else 
            xmlns="http://www.w3.org/2000/svg" 
            viewBox="0 0 24 24" 
            fill="currentColor" 
            class="w-5 h-5"
          >
            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
          </svg>
        </div>

        <!-- Question Number -->
        <span class="font-semibold text-gray-900">
          {{ questionLabel }}
        </span>
      </div>

      <!-- Points -->
      <div class="flex items-center gap-2">
        <span 
          class="text-sm font-bold"
          :class="pointsEarnedClass"
        >
          {{ detail.points_earned || 0 }}
        </span>
        <span class="text-sm text-gray-500">/</span>
        <span class="text-sm font-medium text-gray-700">
          {{ detail.points_max || 0 }} điểm
        </span>
      </div>
    </div>

    <!-- Content -->
    <div class="px-4 py-4 space-y-4">
      <!-- Question Text -->
      <div class="text-gray-800 leading-relaxed" v-html="detail.question_text"></div>

      <!-- Your Answer -->
      <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
        <div class="flex items-start gap-2">
          <div class="flex-shrink-0 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-600">
              <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold text-gray-600 mb-1">{{ yourAnswerLabel }}</div>
            <div 
              class="text-sm font-medium"
              :class="yourAnswerClass"
              v-html="detail.your_answer || emptyAnswerText"
            ></div>
          </div>
        </div>
      </div>

      <!-- Correct Answer (if incorrect) -->
      <div 
        v-if="!detail.is_correct && detail.correct_answer"
        class="bg-green-50 rounded-lg p-3 border border-green-200"
      >
        <div class="flex items-start gap-2">
          <div class="flex-shrink-0 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-green-600">
              <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold text-green-700 mb-1">{{ correctAnswerLabel }}</div>
            <div class="text-sm font-medium text-green-800" v-html="detail.correct_answer"></div>
          </div>
        </div>
      </div>

      <!-- Explanation -->
      <div 
        v-if="detail.explanation && showExplanation"
        class="bg-blue-50 rounded-lg p-3 border border-blue-200"
      >
        <div class="flex items-start gap-2">
          <div class="flex-shrink-0 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-blue-600">
              <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold text-blue-700 mb-1">{{ explanationLabel }}</div>
            <div class="text-sm text-blue-800" v-html="detail.explanation"></div>
          </div>
        </div>
      </div>

      <!-- Custom Content Slot -->
      <slot name="additional-info" :detail="detail" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Chi tiết câu hỏi
  detail: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.question_text !== undefined && 
             value.is_correct !== undefined
    }
  },

  // Số thứ tự câu hỏi
  questionNumber: {
    type: Number,
    default: null
  },

  // Hiển thị giải thích
  showExplanation: {
    type: Boolean,
    default: true
  },

  // Custom labels
  yourAnswerLabel: {
    type: String,
    default: 'Câu trả lời của bạn:'
  },

  correctAnswerLabel: {
    type: String,
    default: 'Đáp án đúng:'
  },

  explanationLabel: {
    type: String,
    default: '💡 Giải thích:'
  },

  // Empty answer text
  emptyAnswerText: {
    type: String,
    default: '<em class="text-gray-400">Chưa trả lời</em>'
  }
})

// Computed: Question label
const questionLabel = computed(() => {
  const num = props.questionNumber || props.detail.question_number
  return num ? `Câu ${num}` : 'Câu hỏi'
})

// Computed: Border class
const borderClass = computed(() => {
  return props.detail.is_correct ? 'border-green-200' : 'border-red-200'
})

// Computed: Header class
const headerClass = computed(() => {
  return props.detail.is_correct ? 'bg-green-50' : 'bg-red-50'
})

// Computed: Icon class
const iconClass = computed(() => {
  return props.detail.is_correct 
    ? 'bg-green-500 text-white' 
    : 'bg-red-500 text-white'
})

// Computed: Points earned class
const pointsEarnedClass = computed(() => {
  return props.detail.is_correct ? 'text-green-600' : 'text-red-600'
})

// Computed: Your answer class
const yourAnswerClass = computed(() => {
  return props.detail.is_correct ? 'text-green-700' : 'text-red-700'
})
</script>
