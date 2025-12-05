<template>
  <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
      <h3 class="text-sm font-semibold text-gray-900">{{ title }}</h3>
    </div>

    <!-- Question Grid -->
    <div class="p-4">
      <div 
        class="grid gap-2"
        :class="gridClass"
      >
        <button
          v-for="(question, index) in questions"
          :key="question.id"
          @click="handleSelect(index)"
          class="relative aspect-square rounded-lg font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
          :class="getQuestionClass(question, index)"
          :title="getQuestionTooltip(question, index)"
        >
          <!-- Question Number -->
          <span class="relative z-10">{{ index + 1 }}</span>

          <!-- Marked Flag Icon -->
          <div 
            v-if="isMarked(question.id)"
            class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center shadow-sm z-20"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-white">
              <path fill-rule="evenodd" d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054a8.25 8.25 0 0 0 5.58.652l3.109-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" clip-rule="evenodd" />
            </svg>
          </div>

          <!-- Active Indicator -->
          <div 
            v-if="currentIndex === index"
            class="absolute inset-0 rounded-lg border-2 border-primary animate-pulse"
          />
        </button>
      </div>
    </div>

    <!-- Legend -->
    <div class="px-4 pb-4 space-y-2">
      <div class="flex items-center gap-2 text-xs text-gray-600">
        <span class="w-5 h-5 rounded bg-primary/20 border-2 border-primary/50"></span>
        <span>Chưa trả lời</span>
      </div>
      <div class="flex items-center gap-2 text-xs text-gray-600">
        <span class="w-5 h-5 rounded bg-white border-2 border-primary shadow-md"></span>
        <span>Đang làm</span>
      </div>
      <div class="flex items-center gap-2 text-xs text-gray-600">
        <span class="w-5 h-5 rounded bg-primary border-2 border-primary"></span>
        <span>Đã trả lời</span>
      </div>
      <div class="flex items-center gap-2 text-xs text-gray-600">
        <span class="relative w-5 h-5 rounded bg-primary/20 border-2 border-primary/50 flex items-center justify-center">
          <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
        </span>
        <span>Đánh dấu</span>
      </div>
    </div>

    <!-- Stats (Optional) -->
    <div v-if="showStats" class="px-4 pb-4 pt-2 border-t border-gray-200">
      <div class="grid grid-cols-2 gap-3 text-center">
        <div class="bg-green-50 rounded-lg px-3 py-2">
          <div class="text-lg font-bold text-green-700">{{ answeredCount }}</div>
          <div class="text-xs text-gray-600">Đã trả lời</div>
        </div>
        <div class="bg-gray-50 rounded-lg px-3 py-2">
          <div class="text-lg font-bold text-gray-700">{{ unansweredCount }}</div>
          <div class="text-xs text-gray-600">Còn lại</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Danh sách câu hỏi
  questions: {
    type: Array,
    required: true,
    validator: (value) => value.every(q => q.id !== undefined)
  },

  // Index câu hỏi hiện tại
  currentIndex: {
    type: Number,
    required: true,
    validator: (value) => value >= 0
  },

  // Object chứa câu trả lời {questionId: answer}
  answers: {
    type: Object,
    default: () => ({})
  },

  // Array chứa ID câu hỏi đã đánh dấu
  markedQuestions: {
    type: Array,
    default: () => []
  },

  // Tiêu đề
  title: {
    type: String,
    default: 'Danh sách câu hỏi'
  },

  // Hiển thị thống kê
  showStats: {
    type: Boolean,
    default: true
  },

  // Số cột grid
  columns: {
    type: Number,
    default: 5,
    validator: (value) => value >= 3 && value <= 8
  }
})

const emit = defineEmits(['navigate', 'select'])

// Grid class dựa trên số cột
const gridClass = computed(() => {
  const cols = {
    3: 'grid-cols-3',
    4: 'grid-cols-4',
    5: 'grid-cols-5',
    6: 'grid-cols-6',
    7: 'grid-cols-7',
    8: 'grid-cols-8'
  }
  return cols[props.columns] || cols[5]
})

// Kiểm tra câu hỏi đã trả lời chưa
const isAnswered = (questionId) => {
  const answer = props.answers[questionId]
  return answer !== undefined && answer !== null && answer !== ''
}

// Kiểm tra câu hỏi đã đánh dấu chưa
const isMarked = (questionId) => {
  return props.markedQuestions.includes(questionId)
}

// Class cho từng câu hỏi
const getQuestionClass = (question, index) => {
  const classes = []
  
  // Active state (đang làm) - nền trắng, viền xanh đậm
  if (props.currentIndex === index) {
    classes.push('bg-white text-primary border-2 border-primary shadow-lg scale-110')
  }
  // Answered state (đã trả lời) - nền xanh đậm (primary)
  else if (isAnswered(question.id)) {
    classes.push('bg-primary text-white border-2 border-primary hover:bg-primary/90')
  }
  // Unanswered state (chưa trả lời) - nền xanh nhạt
  else {
    classes.push('bg-primary/20 text-primary border-2 border-primary/50 hover:bg-primary/30')
  }

  return classes.join(' ')
}

// Tooltip cho từng câu hỏi
const getQuestionTooltip = (question, index) => {
  const parts = [`Câu ${index + 1}`]
  
  if (isAnswered(question.id)) {
    parts.push('(Đã trả lời)')
  }
  
  if (isMarked(question.id)) {
    parts.push('(Đã đánh dấu)')
  }
  
  return parts.join(' ')
}

// Số câu đã trả lời
const answeredCount = computed(() => {
  return props.questions.filter(q => isAnswered(q.id)).length
})

// Số câu chưa trả lời
const unansweredCount = computed(() => {
  return props.questions.length - answeredCount.value
})

// Handler khi chọn câu hỏi
const handleSelect = (index) => {
  emit('navigate', index) // Emit 'navigate' để match với QuizPage
  emit('select', index)   // Giữ lại 'select' cho backward compatibility
}
</script>
