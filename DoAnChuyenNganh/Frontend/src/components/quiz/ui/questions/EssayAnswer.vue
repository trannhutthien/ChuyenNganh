<template>
  <div class="space-y-4">
    <!-- Textarea -->
    <div class="relative">
      <textarea
        :value="modelValue"
        @input="handleInput"
        :disabled="disabled"
        :placeholder="placeholder"
        :rows="rows"
        class="w-full px-4 py-3 text-base border-2 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary disabled:bg-gray-100 disabled:cursor-not-allowed resize-y"
        :class="textareaClass"
      />

      <!-- Character/Word Count -->
      <div 
        v-if="showCount"
        class="absolute bottom-3 right-3 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-lg border border-gray-200 text-xs"
        :class="countClass"
      >
        <div class="flex items-center gap-3">
          <span v-if="maxWords">
            <span class="font-semibold">{{ wordCount }}</span> / {{ maxWords }} từ
          </span>
          <span v-if="maxLength">
            <span class="font-semibold">{{ characterCount }}</span> / {{ maxLength }} ký tự
          </span>
        </div>
      </div>
    </div>

    <!-- Review Mode - Show Answer -->
    <div 
      v-if="showCorrect && sampleAnswer"
      class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg"
    >
      <div class="flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5">
          <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
        </svg>
        <div class="flex-1">
          <h4 class="text-sm font-semibold text-blue-900 mb-2">Câu trả lời mẫu</h4>
          <div 
            class="text-sm text-blue-800 whitespace-pre-wrap"
            :class="{ 'font-mono bg-blue-100 p-3 rounded': isCodeType }"
          >
            {{ sampleAnswer }}
          </div>
        </div>
      </div>
    </div>

    <!-- Teacher Feedback (Review Mode) -->
    <div 
      v-if="showCorrect && feedback"
      class="p-4 rounded-lg border-l-4"
      :class="feedbackClass"
    >
      <div class="flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 flex-shrink-0 mt-0.5" :class="feedbackIconClass">
          <path d="M4.913 2.658c2.075-.27 4.19-.408 6.337-.408 2.147 0 4.262.139 6.337.408 1.922.25 3.291 1.861 3.405 3.727a4.403 4.403 0 0 0-1.032-.211 50.89 50.89 0 0 0-8.42 0c-2.358.196-4.04 2.19-4.04 4.434v4.286a4.47 4.47 0 0 0 2.433 3.984L7.28 21.53A.75.75 0 0 1 6 21v-4.03a48.527 48.527 0 0 1-1.087-.128C2.905 16.58 1.5 14.833 1.5 12.862V6.638c0-1.97 1.405-3.718 3.413-3.979Z" />
          <path d="M15.75 7.5c-1.376 0-2.739.057-4.086.169C10.124 7.797 9 9.103 9 10.609v4.285c0 1.507 1.128 2.814 2.67 2.94 1.243.102 2.5.157 3.768.165l2.782 2.781a.75.75 0 0 0 1.28-.53v-2.39l.33-.026c1.542-.125 2.67-1.433 2.67-2.94v-4.286c0-1.505-1.125-2.811-2.664-2.94A49.392 49.392 0 0 0 15.75 7.5Z" />
        </svg>
        <div class="flex-1">
          <h4 class="text-sm font-semibold mb-1" :class="feedbackTitleClass">
            Nhận xét của giáo viên
          </h4>
          <div class="text-sm" :class="feedbackTextClass">
            {{ feedback }}
          </div>
          <div v-if="score !== null" class="mt-2 text-sm font-semibold" :class="feedbackTextClass">
            Điểm: {{ score }}
          </div>
        </div>
      </div>
    </div>

    <!-- Help Text -->
    <div v-if="!disabled && !showCorrect && helpText" class="flex items-start gap-2 text-sm text-gray-500">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-500">
        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
      </svg>
      <span>{{ helpText }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Giá trị câu trả lời
  modelValue: {
    type: String,
    default: ''
  },

  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },

  // Placeholder
  placeholder: {
    type: String,
    default: 'Nhập câu trả lời của bạn...'
  },

  // Số dòng
  rows: {
    type: Number,
    default: 10
  },

  // Loại (essay hoặc code)
  type: {
    type: String,
    default: 'essay',
    validator: (value) => ['essay', 'code'].includes(value)
  },

  // Hiển thị đáp án (review mode)
  showCorrect: {
    type: Boolean,
    default: false
  },

  // Câu trả lời mẫu
  sampleAnswer: {
    type: String,
    default: null
  },

  // Feedback từ giáo viên
  feedback: {
    type: String,
    default: null
  },

  // Điểm số
  score: {
    type: Number,
    default: null
  },

  // Max length (characters)
  maxLength: {
    type: Number,
    default: null
  },

  // Max words
  maxWords: {
    type: Number,
    default: null
  },

  // Hiển thị số ký tự/từ
  showCount: {
    type: Boolean,
    default: true
  },

  // Help text
  helpText: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

// Check if code type
const isCodeType = computed(() => props.type === 'code')

// Character count
const characterCount = computed(() => {
  return props.modelValue ? props.modelValue.length : 0
})

// Word count
const wordCount = computed(() => {
  if (!props.modelValue) return 0
  return props.modelValue.trim().split(/\s+/).filter(word => word.length > 0).length
})

// Count class
const countClass = computed(() => {
  let isWarning = false
  
  if (props.maxLength) {
    const charRatio = characterCount.value / props.maxLength
    if (charRatio >= 0.9) isWarning = true
  }
  
  if (props.maxWords) {
    const wordRatio = wordCount.value / props.maxWords
    if (wordRatio >= 0.9) isWarning = true
  }
  
  return isWarning ? 'text-red-600 font-semibold border-red-300 bg-red-50' : 'text-gray-600'
})

// Textarea class
const textareaClass = computed(() => {
  const classes = ['border-gray-300']
  
  if (isCodeType.value) {
    classes.push('font-mono text-sm')
  }
  
  return classes.join(' ')
})

// Feedback classes
const feedbackClass = computed(() => {
  if (props.score !== null) {
    if (props.score >= 8) return 'bg-green-50 border-green-500'
    if (props.score >= 5) return 'bg-yellow-50 border-yellow-500'
    return 'bg-red-50 border-red-500'
  }
  return 'bg-gray-50 border-gray-500'
})

const feedbackIconClass = computed(() => {
  if (props.score !== null) {
    if (props.score >= 8) return 'text-green-600'
    if (props.score >= 5) return 'text-yellow-600'
    return 'text-red-600'
  }
  return 'text-gray-600'
})

const feedbackTitleClass = computed(() => {
  if (props.score !== null) {
    if (props.score >= 8) return 'text-green-900'
    if (props.score >= 5) return 'text-yellow-900'
    return 'text-red-900'
  }
  return 'text-gray-900'
})

const feedbackTextClass = computed(() => {
  if (props.score !== null) {
    if (props.score >= 8) return 'text-green-800'
    if (props.score >= 5) return 'text-yellow-800'
    return 'text-red-800'
  }
  return 'text-gray-800'
})

// Handle input
const handleInput = (event) => {
  if (props.disabled) return

  let value = event.target.value

  // Apply max length
  if (props.maxLength && value.length > props.maxLength) {
    value = value.substring(0, props.maxLength)
  }

  emit('update:modelValue', value)
  emit('change', value)
}
</script>
