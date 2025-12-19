<template>
  <!-- Cột BÊN PHẢI - Danh sách bài học -->
  <div class="w-64 bg-gray-800 border-l border-gray-700 flex flex-col overflow-hidden">
    <!-- Header danh sách bài học -->
    <div class="px-4 py-3 border-b border-gray-700">
      <h2 class="text-sm font-bold text-white">Nội dung khóa học</h2>
    </div>

    <!-- Danh sách bài học (scrollable) -->
    <div class="flex-1 overflow-y-auto">
      <div class="p-2 space-y-1">
        <!-- Lesson item -->
        <div 
          v-for="(lesson, index) in lessons" 
          :key="lesson.id"
          @click="$emit('select-lesson', index)"
          class="rounded-lg px-3 py-2 cursor-pointer transition-all flex items-center gap-2"
          :class="[
            currentIndex === index 
              ? 'bg-primary/20 text-primary font-semibold' 
              : 'text-gray-300 hover:bg-gray-700',
            lesson.completed ? 'opacity-75' : ''
          ]"
        >
          <!-- Icon đơn giản -->
          <div 
            class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs font-bold"
            :class="lesson.completed ? 'bg-green-500 text-white' : 'bg-gray-600 text-gray-300'"
          >
            <span v-if="lesson.completed">✓</span>
            <span v-else>{{ index + 1 }}</span>
          </div>

          <!-- Tên bài học -->
          <span class="text-sm flex-1 truncate">{{ lesson.title }}</span>

          <!-- Playing indicator -->
          <div v-if="currentIndex === index" class="flex-shrink-0">
            <div class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></div>
          </div>
        </div>

        <!-- Danh sách bài kiểm tra cuối khóa (render động) -->
        <!-- Chỉ hiển thị các bài kiểm tra có BaiHocId = null -->
        <div v-if="courseExams.length > 0" class="mt-4 pt-4 border-t border-gray-700">
          <div class="px-2 mb-2">
            <span class="text-xs font-semibold text-gray-400 uppercase">Bài kiểm tra</span>
          </div>
          <div class="px-2 space-y-2">
            <BaseButton 
              v-for="exam in courseExams"
              :key="exam.id"
              variant="primary" 
              class="w-full text-left"
              @click="$emit('start-exam', exam.id)"
            >
              <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                </svg>
              </template>
              {{ exam.tieuDe }}
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseButton from '../ui/BaseButton.vue'

// Props
defineProps({
  // Danh sách các bài học
  lessons: {
    type: Array,
    required: true
  },
  // Index bài học hiện tại (bắt đầu từ 0)
  currentIndex: {
    type: Number,
    required: true
  },
  // Danh sách bài kiểm tra cuối khóa (BaiHocId = null)
  courseExams: {
    type: Array,
    default: () => []
  }
})

// Emits
defineEmits([
  'select-lesson',  // Emit khi người dùng click vào bài học
  'start-exam'      // Emit khi click nút làm bài kiểm tra (truyền quizId)
])
</script>

<style scoped>
/* Custom scrollbar cho theme tối */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #1f2937;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Smooth transition cho highlight */
.rounded-lg {
  transition: background-color 0.2s ease, color 0.2s ease;
}
</style>
