<template>
  <!-- Header tiêu đề bài học -->
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ title }}</h2>
    <div class="flex items-center gap-4 text-sm text-gray-500">
      <!-- Duration -->
      <span v-if="duration" class="flex items-center gap-1">
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          fill="none" 
          viewBox="0 0 24 24" 
          stroke-width="1.5" 
          stroke="currentColor" 
          class="w-4 h-4"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" 
          />
        </svg>
        {{ duration }}
      </span>

      <!-- Category -->
      <span v-if="category" class="flex items-center gap-1">
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          fill="none" 
          viewBox="0 0 24 24" 
          stroke-width="1.5" 
          stroke="currentColor" 
          class="w-4 h-4"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" 
          />
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            d="M6 6h.008v.008H6V6Z" 
          />
        </svg>
        {{ category }}
      </span>

      <!-- Difficulty (optional) -->
      <span v-if="difficulty" class="flex items-center gap-1">
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          fill="none" 
          viewBox="0 0 24 24" 
          stroke-width="1.5" 
          stroke="currentColor" 
          class="w-4 h-4"
        >
          <path 
            stroke-linecap="round" 
            stroke-linejoin="round" 
            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" 
          />
        </svg>
        <span :class="difficultyColor">{{ difficulty }}</span>
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  // Tiêu đề bài học
  title: {
    type: String,
    required: true
  },
  // Thời lượng
  duration: {
    type: String,
    default: ''
  },
  // Danh mục
  category: {
    type: String,
    default: ''
  },
  // Độ khó
  difficulty: {
    type: String,
    default: '',
    validator: (value) => {
      return ['', 'Dễ', 'Trung bình', 'Khó'].includes(value)
    }
  }
})

// Computed
const difficultyColor = computed(() => {
  switch (props.difficulty) {
    case 'Dễ':
      return 'text-green-600 font-medium'
    case 'Trung bình':
      return 'text-yellow-600 font-medium'
    case 'Khó':
      return 'text-red-600 font-medium'
    default:
      return 'text-gray-600'
  }
})
</script>

<style scoped>
/* Icon và text alignment */
svg {
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 640px) {
  .flex.items-center.gap-4 {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
}
</style>
