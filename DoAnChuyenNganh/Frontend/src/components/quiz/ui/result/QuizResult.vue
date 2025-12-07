<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Result Card -->
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header with Pass/Fail Icon -->
        <div 
          class="relative py-12 px-6 text-center"
          :class="headerClass"
        >
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 20px 20px;"></div>
          </div>

          <!-- Icon -->
          <div class="relative mb-6">
            <div 
              class="inline-flex items-center justify-center w-32 h-32 rounded-full mx-auto"
              :class="iconBgClass"
            >
              <!-- Passed Icon -->
              <svg 
                v-if="result.passed" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-20 h-20"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>

              <!-- Failed Icon -->
              <svg 
                v-else 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-20 h-20"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
            </div>
          </div>

          <!-- Title -->
          <h1 class="relative text-3xl md:text-4xl font-bold mb-3">
            {{ result.passed ? 'Chúc mừng! Bạn đã đạt' : 'Rất tiếc! Bạn chưa đạt' }}
          </h1>

          <!-- Score - Điểm động theo thang điểm -->
          <div class="relative text-6xl md:text-7xl font-extrabold mb-2">
            {{ typeof result.score === 'number' ? result.score.toFixed(2) : result.score }}
            <span class="text-3xl md:text-4xl opacity-80">/{{ result.max_score || 10 }}</span>
          </div>

          <p class="relative text-lg opacity-90">
            {{ result.passed ? 'Xuất sắc! Bạn đã hoàn thành bài kiểm tra' : 'Hãy cố gắng lần sau nhé!' }}
          </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-8 bg-gray-50 border-y border-gray-200">
          <!-- Correct Answers -->
          <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="text-sm font-medium text-gray-600 mb-2">Số câu đúng</div>
            <div class="text-3xl font-bold text-green-600">
              {{ result.correct_answers }}<span class="text-gray-400">/{{ result.total_questions }}</span>
            </div>
            <div class="mt-2 text-xs text-gray-500">
              {{ result.total_questions > 0 ? Math.round((result.correct_answers / result.total_questions) * 100) : 0 }}% chính xác
            </div>
          </div>

          <!-- Time Taken -->
          <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="text-sm font-medium text-gray-600 mb-2">Thời gian làm bài</div>
            <div class="text-3xl font-bold text-blue-600">
              {{ result.time_taken }}
            </div>
            <div class="mt-2 text-xs text-gray-500">
              Tổng thời gian
            </div>
          </div>

          <!-- Passing Score -->
          <div class="text-center p-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="text-sm font-medium text-gray-600 mb-2">Điểm đạt yêu cầu</div>
            <div class="text-3xl font-bold text-amber-600">
              {{ passingScore }}/10
            </div>
            <div class="mt-2 text-xs text-gray-500">
              {{ result.passed ? 'Đã đạt' : 'Chưa đạt' }}
            </div>
          </div>
        </div>

        <!-- Detailed Results Section -->
        <div class="p-8">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Chi tiết kết quả</h3>
            <button
              @click="toggleDetails"
              class="text-sm font-medium text-primary hover:text-primary-600 flex items-center gap-2 transition-colors"
            >
              {{ showDetails ? 'Thu gọn' : 'Xem chi tiết' }}
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': showDetails }"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </button>
          </div>

          <!-- Details List Slot -->
          <div v-if="showDetails" class="space-y-4">
            <slot name="details">
              <!-- Hiển thị chi tiết từng câu hỏi -->
              <div v-if="result.question_details && result.question_details.length > 0" class="space-y-6">
                <div 
                  v-for="(detail, index) in result.question_details" 
                  :key="index"
                  class="bg-white border rounded-lg p-6 shadow-sm"
                >
                  <!-- Câu hỏi header -->
                  <div class="flex items-start gap-4 mb-4">
                    <div 
                      class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-white"
                      :class="detail.is_correct ? 'bg-green-500' : 'bg-red-500'"
                    >
                      {{ index + 1 }}
                    </div>
                    <div class="flex-1">
                      <h4 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ detail.question_text }}
                      </h4>
                      <div 
                        v-if="detail.is_correct !== null"
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium"
                        :class="detail.is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                      >
                        <svg v-if="detail.is_correct" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        {{ detail.is_correct ? 'Đúng' : 'Sai' }}
                      </div>
                    </div>
                  </div>

                  <!-- Danh sách lựa chọn -->
                  <div class="space-y-2 ml-14">
                    <div 
                      v-for="(option, optIdx) in detail.options" 
                      :key="optIdx"
                      class="border rounded-lg p-3 transition-all"
                      :class="{
                        'bg-red-50 border-red-300 font-medium': option.is_user_answer && !option.is_correct,
                        'bg-green-50 border-green-300 font-medium': option.is_correct,
                        'bg-gray-50 border-gray-200': option.is_user_answer && option.is_correct,
                        'border-gray-200': !option.is_user_answer && !option.is_correct
                      }"
                    >
                      <div class="flex items-center gap-3">
                        <!-- Icon cho lựa chọn -->
                        <div class="flex-shrink-0">
                          <div 
                            v-if="option.is_user_answer && !option.is_correct"
                            class="w-6 h-6 rounded-full bg-red-500 flex items-center justify-center"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                          </div>
                          <div 
                            v-else-if="option.is_correct"
                            class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                          </div>
                          <div 
                            v-else
                            class="w-6 h-6 rounded-full border-2 border-gray-300"
                          ></div>
                        </div>

                        <!-- Nội dung lựa chọn -->
                        <div class="flex-1">
                          <span :class="{
                            'text-red-800': option.is_user_answer && !option.is_correct,
                            'text-green-800': option.is_correct,
                            'text-gray-700': !option.is_user_answer && !option.is_correct
                          }">
                            {{ option.text }}
                          </span>
                        </div>

                        <!-- Nhãn -->
                        <div class="flex-shrink-0">
                          <span 
                            v-if="option.is_user_answer && !option.is_correct"
                            class="text-xs px-2 py-1 rounded bg-red-200 text-red-800"
                          >
                            Bạn chọn
                          </span>
                          <span 
                            v-else-if="option.is_correct && option.is_user_answer"
                            class="text-xs px-2 py-1 rounded bg-green-200 text-green-800"
                          >
                            Đúng ✓
                          </span>
                          <span 
                            v-else-if="option.is_correct"
                            class="text-xs px-2 py-1 rounded bg-green-200 text-green-800"
                          >
                            Đáp án đúng
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Giải thích (nếu có) -->
                  <div v-if="detail.explanation" class="mt-4 ml-14 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
                    <p class="text-sm text-blue-900">
                      <strong>Giải thích:</strong> {{ detail.explanation }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Default: No details provided -->
              <div v-else class="text-center py-8 text-gray-400">
                <p>Không có chi tiết kết quả</p>
              </div>
            </slot>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-8 pb-8 flex flex-wrap gap-4 justify-center">
          <slot name="actions">
            <!-- Default actions will be provided by parent -->
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  // Kết quả bài kiểm tra
  result: {
    type: Object,
    required: true,
    validator: (value) => {
      console.log('QuizResult validator - received value:', value)
      // Chỉ yêu cầu các field cơ bản
      const isValid = value.score !== undefined && 
             value.passed !== undefined &&
             value.correct_answers !== undefined &&
             value.total_questions !== undefined
      
      if (!isValid) {
        console.error('QuizResult validation failed:', {
          score: value.score,
          passed: value.passed,
          correct_answers: value.correct_answers,
          total_questions: value.total_questions,
          time_taken: value.time_taken
        })
      }
      return isValid
    }
  },

  // Điểm đạt yêu cầu (thang 10)
  passingScore: {
    type: Number,
    default: 5
  },

  // Hiển thị chi tiết mặc định
  showDetailsDefault: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle-details'])

// State
const showDetails = ref(props.showDetailsDefault)

// Computed: Header class based on pass/fail
const headerClass = computed(() => {
  return props.result.passed
    ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white'
    : 'bg-gradient-to-r from-red-500 to-rose-600 text-white'
})

// Computed: Icon background class
const iconBgClass = computed(() => {
  return props.result.passed
    ? 'bg-white/20 text-white backdrop-blur-sm'
    : 'bg-white/20 text-white backdrop-blur-sm'
})

// Toggle details
const toggleDetails = () => {
  showDetails.value = !showDetails.value
  emit('toggle-details', showDetails.value)
}
</script>
