<template>
  <div 
    class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow cursor-pointer"
    @click="$emit('click', course.id)"
  >
    <div class="flex items-start gap-4">
      <!-- Thumbnail -->
      <div class="hidden sm:block w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
        <img 
          v-if="course.thumbnail && !imageError" 
          :src="getImageUrl(course.thumbnail)" 
          :alt="course.title"
          class="w-full h-full object-cover"
          @error="imageError = true"
        />
        <div v-else class="w-full h-full flex items-center justify-center text-2xl bg-gradient-to-br from-primary/20 to-purple-500/20">
          📚
        </div>
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <!-- Badges -->
        <div class="flex items-center gap-2 mb-1">
          <span 
            class="px-2 py-0.5 text-xs font-medium rounded"
            :class="course.required ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-600'"
          >
            {{ course.required ? 'Bắt buộc' : 'Tùy chọn' }}
          </span>
          <span v-if="course.note" class="text-xs text-gray-500">• {{ course.note }}</span>
        </div>

        <!-- Title -->
        <h3 class="text-lg font-semibold text-gray-800 mb-1 hover:text-primary transition-colors">
          {{ course.title }}
        </h3>

        <!-- Description -->
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ course.description }}</p>
        
        <!-- Stats -->
        <div class="flex items-center gap-4 text-sm text-gray-500">
          <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            {{ course.lessons }} bài học
          </span>
          <span 
            class="font-medium"
            :class="course.price > 0 ? 'text-orange-500' : 'text-green-500'"
          >
            {{ course.price > 0 ? formatPrice(course.price) : 'Miễn phí' }}
          </span>
        </div>
      </div>

      <!-- Arrow -->
      <div class="hidden sm:flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  course: {
    type: Object,
    required: true
  }
})

defineEmits(['click'])

const imageError = ref(false)

const getImageUrl = (thumbnail) => {
  if (thumbnail.startsWith('http')) {
    return thumbnail
  }
  return `http://127.0.0.1:8000/storage/courses/${thumbnail}`
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
