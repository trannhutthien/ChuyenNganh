<template>
  <div class="flex items-center justify-center min-h-[60vh] px-4 py-12">
    <div class="w-full max-w-2xl">
      <!-- Card Container -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-primary/80 px-8 py-6 text-white">
          <div class="flex items-center gap-3 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
              <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
            </svg>
            <h2 class="text-2xl font-bold">{{ title }}</h2>
          </div>
          <p v-if="subtitle" class="text-primary-50 text-sm">{{ subtitle }}</p>
        </div>

        <!-- Instructions -->
        <div class="px-8 py-6">
          <slot name="instructions">
            <!-- Default instructions -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary">
                  <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
                Hướng dẫn làm bài
              </h3>
              
              <ul class="space-y-3">
                <li 
                  v-for="(instruction, index) in instructions" 
                  :key="index"
                  class="flex items-start gap-3 text-gray-700"
                >
                  <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold mt-0.5">
                    {{ index + 1 }}
                  </span>
                  <span class="flex-1" v-html="instruction"></span>
                </li>
              </ul>
            </div>
          </slot>

          <!-- Custom Content Slot -->
          <div v-if="$slots.content" class="mt-6">
            <slot name="content"></slot>
          </div>

          <!-- Warning Message (Optional) -->
          <div v-if="showWarning" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <div class="flex items-start gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5">
                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
              </svg>
              <div class="flex-1">
                <slot name="warning">
                  <p class="text-sm font-medium text-amber-900">
                    {{ warningMessage }}
                  </p>
                </slot>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
          <slot name="actions">
            <!-- Default Start Button -->
            <BaseButton
              @click="handleStart"
              variant="primary"
              size="lg"
              :loading="loading"
              :disabled="disabled"
              full-width
            >
              <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
              </template>
              {{ startButtonText }}
            </BaseButton>
          </slot>
        </div>
      </div>

      <!-- Additional Info (Optional) -->
      <div v-if="showTips" class="mt-6 text-center text-sm text-gray-600">
        <slot name="tips">
          <p>💡 <strong>Mẹo:</strong> Đọc kỹ câu hỏi trước khi trả lời</p>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseButton from '../../ui/BaseButton.vue'

const props = defineProps({
  // Tiêu đề màn hình
  title: {
    type: String,
    default: 'Hướng dẫn làm bài'
  },

  // Phụ đề
  subtitle: {
    type: String,
    default: null
  },

  // Danh sách hướng dẫn
  instructions: {
    type: Array,
    default: () => [
      'Bạn có <strong>30 phút</strong> để hoàn thành 20 câu hỏi',
      'Điểm đạt: <strong>70%</strong>',
      'Bài kiểm tra sẽ tự động nộp khi hết giờ',
      'Bạn có thể đánh dấu câu hỏi để xem lại sau',
      'Câu trả lời được tự động lưu mỗi 30 giây',
      'Không được refresh hoặc thoát tab khi đang làm bài'
    ]
  },

  // Text nút bắt đầu
  startButtonText: {
    type: String,
    default: 'Bắt đầu làm bài'
  },

  // Loading state
  loading: {
    type: Boolean,
    default: false
  },

  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },

  // Hiển thị cảnh báo
  showWarning: {
    type: Boolean,
    default: false
  },

  // Text cảnh báo
  warningMessage: {
    type: String,
    default: 'Không được refresh hoặc thoát tab khi đang làm bài'
  },

  // Hiển thị tips
  showTips: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['start', 'require-login'])

const handleStart = () => {
  // File: QuizStartScreen.vue - Dòng 168-178
  // Kiểm tra đã đăng nhập chưa (dùng access_token giống Header.vue)
  const token = localStorage.getItem('access_token')
  if (!token) {
    // Chưa đăng nhập -> yêu cầu mở LoginModal
    emit('require-login')
    return
  }
  // Đã đăng nhập -> bắt đầu làm bài
  emit('start')
}
</script>
