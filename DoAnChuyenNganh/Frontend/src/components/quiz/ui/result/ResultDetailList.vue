<template>
  <div class="space-y-4">
    <!-- Header -->
    <div v-if="showHeader" class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-gray-900">{{ title }}</h3>
      <div class="text-sm text-gray-600">
        {{ details.length }} câu hỏi
      </div>
    </div>

    <!-- Filter Tabs (Optional) -->
    <div v-if="showFilters" class="flex gap-2 mb-4 border-b border-gray-200">
      <button
        @click="filter = 'all'"
        class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
        :class="filter === 'all' 
          ? 'text-primary border-primary' 
          : 'text-gray-600 border-transparent hover:text-gray-900'"
      >
        Tất cả ({{ details.length }})
      </button>
      <button
        @click="filter = 'correct'"
        class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
        :class="filter === 'correct' 
          ? 'text-green-600 border-green-600' 
          : 'text-gray-600 border-transparent hover:text-gray-900'"
      >
        Đúng ({{ correctCount }})
      </button>
      <button
        @click="filter = 'incorrect'"
        class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
        :class="filter === 'incorrect' 
          ? 'text-red-600 border-red-600' 
          : 'text-gray-600 border-transparent hover:text-gray-900'"
      >
        Sai ({{ incorrectCount }})
      </button>
    </div>

    <!-- Results List -->
    <div class="space-y-3">
      <div
        v-for="(detail, index) in filteredDetails"
        :key="index"
        class="bg-white rounded-lg border-2 shadow-sm transition-all duration-200 hover:shadow-md overflow-hidden"
        :class="getItemBorderClass(detail)"
      >
        <!-- Item Header -->
        <div 
          class="flex items-center justify-between px-4 py-3 border-b"
          :class="getItemHeaderClass(detail)"
        >
          <div class="flex items-center gap-3">
            <!-- Icon -->
            <div 
              class="w-8 h-8 rounded-full flex items-center justify-center"
              :class="getIconClass(detail)"
            >
              <svg 
                v-if="detail.is_correct" 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 24 24" 
                fill="currentColor" 
                class="w-5 h-5"
              >
                <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
              </svg>
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
              Câu {{ detail.question_number || (index + 1) }}
            </span>
          </div>

          <!-- Points -->
          <div class="flex items-center gap-2">
            <span 
              class="text-sm font-bold"
              :class="detail.is_correct ? 'text-green-600' : 'text-red-600'"
            >
              {{ detail.points_earned || 0 }}
            </span>
            <span class="text-sm text-gray-500">/</span>
            <span class="text-sm font-medium text-gray-700">
              {{ detail.points_max || 0 }} điểm
            </span>
          </div>
        </div>

        <!-- Item Content -->
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
                <div class="text-xs font-semibold text-gray-600 mb-1">Câu trả lời của bạn:</div>
                <div 
                  class="text-sm font-medium"
                  :class="detail.is_correct ? 'text-green-700' : 'text-red-700'"
                  v-html="detail.your_answer || '<em class=\'text-gray-400\'>Chưa trả lời</em>'"
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
                <div class="text-xs font-semibold text-green-700 mb-1">Đáp án đúng:</div>
                <div class="text-sm font-medium text-green-800" v-html="detail.correct_answer"></div>
              </div>
            </div>
          </div>

          <!-- Explanation -->
          <div 
            v-if="detail.explanation"
            class="bg-blue-50 rounded-lg p-3 border border-blue-200"
          >
            <div class="flex items-start gap-2">
              <div class="flex-shrink-0 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-blue-600">
                  <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-blue-700 mb-1">💡 Giải thích:</div>
                <div class="text-sm text-blue-800" v-html="detail.explanation"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div 
      v-if="filteredDetails.length === 0"
      class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-gray-400">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
      </svg>
      <p class="text-gray-600 font-medium">
        {{ emptyMessage }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  // Danh sách chi tiết kết quả
  details: {
    type: Array,
    required: true,
    default: () => []
  },

  // Tiêu đề
  title: {
    type: String,
    default: 'Chi tiết kết quả'
  },

  // Hiển thị header
  showHeader: {
    type: Boolean,
    default: true
  },

  // Hiển thị filters
  showFilters: {
    type: Boolean,
    default: true
  },

  // Empty message
  emptyMessage: {
    type: String,
    default: 'Không có kết quả nào'
  }
})

// State
const filter = ref('all')

// Computed: Correct count
const correctCount = computed(() => {
  return props.details.filter(d => d.is_correct).length
})

// Computed: Incorrect count
const incorrectCount = computed(() => {
  return props.details.filter(d => !d.is_correct).length
})

// Computed: Filtered details
const filteredDetails = computed(() => {
  if (filter.value === 'correct') {
    return props.details.filter(d => d.is_correct)
  }
  if (filter.value === 'incorrect') {
    return props.details.filter(d => !d.is_correct)
  }
  return props.details
})

// Get item border class
const getItemBorderClass = (detail) => {
  return detail.is_correct ? 'border-green-200' : 'border-red-200'
}

// Get item header class
const getItemHeaderClass = (detail) => {
  return detail.is_correct ? 'bg-green-50' : 'bg-red-50'
}

// Get icon class
const getIconClass = (detail) => {
  return detail.is_correct 
    ? 'bg-green-500 text-white' 
    : 'bg-red-500 text-white'
}
</script>
