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

          <!-- Score -->
          <div class="relative text-6xl md:text-7xl font-extrabold mb-2">
            {{ result.score }}
            <span class="text-3xl md:text-4xl opacity-80">/100</span>
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
              {{ Math.round((result.correct_answers / result.total_questions) * 100) }}% chính xác
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
              {{ passingScore }}%
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
              <!-- Default: No details provided -->
              <div class="text-center py-8 text-gray-400">
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
      return value.score !== undefined && 
             value.passed !== undefined &&
             value.correct_answers !== undefined &&
             value.total_questions !== undefined &&
             value.time_taken !== undefined
    }
  },

  // Điểm đạt yêu cầu
  passingScore: {
    type: Number,
    default: 70
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
