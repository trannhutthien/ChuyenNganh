<template>
  <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
    <!-- Question Header -->
    <div class="bg-gradient-to-r from-primary to-primary-dark px-6 py-4">
      <div class="flex items-center justify-between">
        <!-- Question Number -->
        <div class="flex items-center gap-3">
          <div class="text-white font-bold text-lg">
            Câu {{ questionNumber }}<span v-if="totalQuestions">/{{ totalQuestions }}</span>
          </div>
          
          <!-- Question Type Badge -->
          <span 
            class="px-3 py-1 rounded-full text-xs font-semibold"
            :class="questionTypeBadgeClass"
          >
            {{ questionTypeLabel }}
          </span>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
          <!-- Points -->
          <div 
            v-if="question.points"
            class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-white text-sm font-semibold flex items-center gap-1"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
              <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
            </svg>
            {{ question.points }} điểm
          </div>

          <!-- Mark Button -->
          <button
            @click="handleToggleMark"
            class="px-3 py-1 rounded-lg text-white text-sm font-medium transition-all duration-200 flex items-center gap-2 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50"
            :class="isMarked ? 'bg-amber-500/30' : 'bg-white/10'"
            :title="isMarked ? 'Bỏ đánh dấu' : 'Đánh dấu để xem lại'"
          >
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              :fill="isMarked ? 'currentColor' : 'none'" 
              viewBox="0 0 24 24" 
              stroke-width="2" 
              stroke="currentColor" 
              class="w-4 h-4"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
            </svg>
            {{ isMarked ? 'Bỏ đánh dấu' : 'Đánh dấu' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Question Content -->
    <div class="p-6 space-y-6">
      <!-- Question Text -->
      <div class="prose prose-lg max-w-none">
        <div 
          v-html="question.text"
          class="text-gray-800 leading-relaxed"
        />
      </div>

      <!-- Question Image -->
      <div v-if="question.image" class="rounded-lg overflow-hidden border border-gray-200">
        <img 
          :src="question.image" 
          :alt="'Hình ảnh câu hỏi ' + questionNumber"
          class="w-full h-auto"
        />
      </div>

      <!-- Answer Area Slot -->
      <div class="mt-6">
        <slot name="answer">
          <!-- Default: No answer component provided -->
          <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
            </svg>
            <p class="text-sm font-medium">Chưa có thành phần trả lời</p>
          </div>
        </slot>
      </div>

      <!-- Explanation (for review mode) -->
      <div 
        v-if="showExplanation && question.explanation"
        class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg"
      >
        <div class="flex items-start gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-semibold text-blue-900 mb-1">Giải thích</h4>
            <div class="text-sm text-blue-800" v-html="question.explanation"></div>
          </div>
        </div>
      </div>

      <!-- Correct Answer Display (for review mode) -->
      <div 
        v-if="showCorrectAnswer && question.correct_answer"
        class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg"
      >
        <div class="flex items-start gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
          </svg>
          <div class="flex-1">
            <h4 class="text-sm font-semibold text-green-900 mb-1">Đáp án đúng</h4>
            <div class="text-sm text-green-800" v-html="question.correct_answer"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Slot (for navigation, actions, etc.) -->
    <div v-if="$slots.footer" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Dữ liệu câu hỏi
  question: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.id !== undefined && value.type !== undefined && value.text !== undefined
    }
  },

  // Số thứ tự câu hỏi hiện tại
  questionNumber: {
    type: Number,
    required: true,
    validator: (value) => value > 0
  },

  // Tổng số câu hỏi
  totalQuestions: {
    type: Number,
    default: null
  },

  // Câu hỏi đã được đánh dấu chưa
  isMarked: {
    type: Boolean,
    default: false
  },

  // Hiển thị giải thích (dùng cho chế độ xem lại)
  showExplanation: {
    type: Boolean,
    default: false
  },

  // Hiển thị đáp án đúng (dùng cho chế độ xem lại)
  showCorrectAnswer: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle-mark'])

// Question type labels
const questionTypeLabels = {
  single: 'Một đáp án',
  multiple: 'Nhiều đáp án',
  true_false: 'Đúng/Sai',
  matching: 'Ghép cặp',
  essay: 'Tự luận',
  code: 'Code'
}

// Question type badge classes
const questionTypeBadgeClasses = {
  single: 'bg-blue-500/20 text-blue-100 border border-blue-400/30',
  multiple: 'bg-indigo-500/20 text-indigo-100 border border-indigo-400/30',
  true_false: 'bg-purple-500/20 text-purple-100 border border-purple-400/30',
  matching: 'bg-orange-500/20 text-orange-100 border border-orange-400/30',
  essay: 'bg-pink-500/20 text-pink-100 border border-pink-400/30',
  code: 'bg-gray-500/20 text-gray-100 border border-gray-400/30'
}

// Computed: Question type label
const questionTypeLabel = computed(() => {
  return questionTypeLabels[props.question.type] || 'Không xác định'
})

// Computed: Question type badge class
const questionTypeBadgeClass = computed(() => {
  return questionTypeBadgeClasses[props.question.type] || 'bg-gray-500/20 text-gray-100 border border-gray-400/30'
})

// Handler: Toggle mark
const handleToggleMark = () => {
  emit('toggle-mark')
}
</script>
