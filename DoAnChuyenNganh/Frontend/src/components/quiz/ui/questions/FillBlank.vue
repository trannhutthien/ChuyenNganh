<template>
  <div class="space-y-4">
    <!-- Input Field -->
    <div class="relative">
      <input
        type="text"
        :value="modelValue"
        @input="handleInput"
        :disabled="disabled"
        :placeholder="placeholder"
        class="w-full px-4 py-3 text-lg border-2 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary disabled:bg-gray-100 disabled:cursor-not-allowed"
        :class="inputClass"
      />

      <!-- Character Count (Optional) -->
      <div 
        v-if="showCount && maxLength"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"
        :class="countClass"
      >
        {{ characterCount }} / {{ maxLength }}
      </div>
    </div>

    <!-- Review Mode - Correct Answer Display -->
    <div 
      v-if="showCorrect && correctAnswer"
      class="p-4 rounded-lg border-l-4"
      :class="isCorrect ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500'"
    >
      <div class="flex items-start gap-3">
        <!-- Icon -->
        <svg 
          v-if="isCorrect"
          xmlns="http://www.w3.org/2000/svg" 
          viewBox="0 0 24 24" 
          fill="currentColor" 
          class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5"
        >
          <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
        </svg>
        <svg 
          v-else
          xmlns="http://www.w3.org/2000/svg" 
          viewBox="0 0 24 24" 
          fill="currentColor" 
          class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5"
        >
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
        </svg>

        <!-- Content -->
        <div class="flex-1">
          <h4 class="text-sm font-semibold mb-1" :class="isCorrect ? 'text-green-900' : 'text-red-900'">
            {{ isCorrect ? 'Chính xác!' : 'Không chính xác' }}
          </h4>
          
          <!-- Show user answer if wrong -->
          <div v-if="!isCorrect && modelValue" class="mb-2">
            <p class="text-sm text-red-800">
              <span class="font-medium">Câu trả lời của bạn:</span> {{ modelValue }}
            </p>
          </div>

          <!-- Show correct answer -->
          <div v-if="!isCorrect">
            <p class="text-sm" :class="isCorrect ? 'text-green-800' : 'text-red-800'">
              <span class="font-medium">Đáp án đúng:</span> {{ correctAnswer }}
            </p>
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

  // Hiển thị đáp án đúng (review mode)
  showCorrect: {
    type: Boolean,
    default: false
  },

  // Đáp án đúng (dùng cho review mode)
  correctAnswer: {
    type: String,
    default: null
  },

  // Kiểu so sánh (exact, case-insensitive, contains)
  compareType: {
    type: String,
    default: 'case-insensitive',
    validator: (value) => ['exact', 'case-insensitive', 'contains'].includes(value)
  },

  // Max length
  maxLength: {
    type: Number,
    default: null
  },

  // Hiển thị số ký tự
  showCount: {
    type: Boolean,
    default: false
  },

  // Help text
  helpText: {
    type: String,
    default: 'Nhập câu trả lời chính xác'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

// Character count
const characterCount = computed(() => {
  return props.modelValue ? props.modelValue.length : 0
})

// Count class
const countClass = computed(() => {
  if (!props.maxLength) return 'text-gray-500'
  
  const ratio = characterCount.value / props.maxLength
  if (ratio >= 0.9) return 'text-red-500 font-semibold'
  if (ratio >= 0.7) return 'text-yellow-600 font-medium'
  return 'text-gray-500'
})

// Input class
const inputClass = computed(() => {
  const classes = []

  if (props.showCorrect) {
    if (isCorrect.value) {
      classes.push('border-green-500 bg-green-50 text-green-900')
    } else {
      classes.push('border-red-500 bg-red-50 text-red-900')
    }
  } else {
    classes.push('border-gray-300')
  }

  return classes.join(' ')
})

// Check if answer is correct
const isCorrect = computed(() => {
  if (!props.showCorrect || !props.correctAnswer) return false

  const userAnswer = props.modelValue || ''
  const correctAnswer = props.correctAnswer

  switch (props.compareType) {
    case 'exact':
      return userAnswer === correctAnswer
    
    case 'case-insensitive':
      return userAnswer.toLowerCase().trim() === correctAnswer.toLowerCase().trim()
    
    case 'contains':
      return correctAnswer.toLowerCase().includes(userAnswer.toLowerCase().trim())
    
    default:
      return false
  }
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
