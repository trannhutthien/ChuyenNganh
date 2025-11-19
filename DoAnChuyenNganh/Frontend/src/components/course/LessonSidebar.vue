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
      </div>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  // Danh sách các bài học
  lessons: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every(lesson => 
        lesson.hasOwnProperty('id') && 
        lesson.hasOwnProperty('title')
      )
    }
  },
  // Index bài học hiện tại (bắt đầu từ 0)
  currentIndex: {
    type: Number,
    required: true
  }
})

// Emits
const emit = defineEmits([
  'select-lesson' // Emit khi người dùng click vào bài học, trả về index
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
