<template>
  <div class="flex items-center justify-between p-4 border-t border-gray-200">
    <!-- Info text -->
    <p class="text-sm text-gray-600">
      Hiển thị {{ startItem }} - {{ endItem }} trên {{ total }} {{ itemName }}
    </p>

    <!-- Pagination controls -->
    <div class="flex items-center gap-2">
      <button 
        @click="$emit('prev')"
        :disabled="currentPage === 1"
        class="px-3 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        {{ prevText }}
      </button>

      <!-- Page numbers (optional) -->
      <template v-if="showPageNumbers">
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="$emit('go-to', page)"
          :class="[
            'px-3 py-1 rounded-lg transition-colors',
            page === currentPage 
              ? 'bg-primary text-white' 
              : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
      </template>

      <!-- Current page indicator (when not showing page numbers) -->
      <span 
        v-else
        class="px-3 py-1 bg-primary text-white rounded-lg"
      >
        {{ currentPage }}
      </span>

      <button 
        @click="$emit('next')"
        :disabled="currentPage >= totalPages"
        class="px-3 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        {{ nextText }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  },
  pageSize: {
    type: Number,
    default: 10
  },
  itemName: {
    type: String,
    default: 'mục'
  },
  prevText: {
    type: String,
    default: 'Trước'
  },
  nextText: {
    type: String,
    default: 'Sau'
  },
  showPageNumbers: {
    type: Boolean,
    default: false
  },
  maxVisiblePages: {
    type: Number,
    default: 5
  }
})

defineEmits(['prev', 'next', 'go-to'])

// Computed
const startItem = computed(() => {
  if (props.total === 0) return 0
  return (props.currentPage - 1) * props.pageSize + 1
})

const endItem = computed(() => {
  return Math.min(props.currentPage * props.pageSize, props.total)
})

const visiblePages = computed(() => {
  const pages = []
  const half = Math.floor(props.maxVisiblePages / 2)
  
  let start = Math.max(1, props.currentPage - half)
  let end = Math.min(props.totalPages, start + props.maxVisiblePages - 1)
  
  // Adjust start if we're near the end
  if (end - start + 1 < props.maxVisiblePages) {
    start = Math.max(1, end - props.maxVisiblePages + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})
</script>
