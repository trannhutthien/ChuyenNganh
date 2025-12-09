<template>
  <div class="space-y-3">
    <!-- Options List -->
    <label
      v-for="option in options"
      :key="option.id"
      class="relative flex items-start gap-4 p-4 rounded-lg border-2 transition-all duration-200 cursor-pointer group"
      :class="getOptionClass(option)"
    >
      <!-- Radio Button -->
      <div class="flex items-center h-6 mt-0.5">
        <input
          type="radio"
          :name="'question-' + question.id"
          :value="option.id"
          :checked="modelValue === option.id"
          @change="handleChange(option.id)"
          :disabled="disabled"
          class="w-5 h-5 text-primary border-2 border-gray-300 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
        />
      </div>

      <!-- Option Content -->
      <div class="flex-1 min-w-0">
        <!-- Option Text -->
        <div 
          class="text-lg text-gray-800 leading-relaxed"
          :class="{ 'font-semibold': modelValue === option.id }"
        >
          {{ option.text }}
        </div>
      </div>

      <!-- Selected Indicator -->
      <div 
        v-if="modelValue === option.id && !disabled"
        class="absolute -top-2 -right-2 w-6 h-6 bg-primary rounded-full flex items-center justify-center shadow-lg"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-white">
          <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
        </svg>
      </div>

      <!-- Correct/Incorrect Indicator (Review Mode) -->
      <div 
        v-if="showCorrect && option.id === correctAnswer"
        class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center shadow-lg"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-white">
          <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
        </svg>
      </div>

      <div 
        v-if="showCorrect && modelValue === option.id && option.id !== correctAnswer"
        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center shadow-lg"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-white">
          <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
      </div>
    </label>

    <!-- Help Text -->
    <div v-if="!disabled && !showCorrect" class="flex items-start gap-2 text-sm text-gray-500 mt-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-500">
        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
      </svg>
      <span>Chọn một đáp án</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Dữ liệu câu hỏi
  question: {
    type: Object,
    required: true
  },

  // Giá trị đã chọn (option ID)
  modelValue: {
    type: [String, Number],
    default: null
  },

  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },

  // Hiển thị đáp án đúng (review mode)
  showCorrect: {
    type: Boolean,
    default: false
  },

  // ID đáp án đúng (dùng cho review mode)
  correctAnswer: {
    type: [String, Number],
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

// Lấy danh sách options an toàn
const options = computed(() => {
  return props.question?.options || []
})

// Get option class
const getOptionClass = (option) => {
  const classes = []

  // Review mode - show correct/incorrect
  if (props.showCorrect) {
    if (option.id === props.correctAnswer) {
      classes.push('bg-green-50 border-green-500')
    } else if (props.modelValue === option.id) {
      classes.push('bg-red-50 border-red-500')
    } else {
      classes.push('bg-white border-gray-200')
    }
  }
  // Normal mode
  else {
    if (props.modelValue === option.id) {
      classes.push('bg-primary/5 border-primary shadow-md')
    } else {
      classes.push('bg-white border-gray-200 hover:border-primary/50 hover:bg-gray-50')
    }
  }

  // Disabled state
  if (props.disabled) {
    classes.push('opacity-60 cursor-not-allowed')
  }

  return classes.join(' ')
}

// Handle change
const handleChange = (optionId) => {
  if (props.disabled) return

  emit('update:modelValue', optionId)
  emit('change', optionId)
}
</script>
