<template>
  <div class="w-full">
    <!-- Progress Label (Optional) -->
    <div v-if="showLabel" class="flex items-center justify-between mb-2">
      <span class="text-sm font-medium text-gray-700">{{ label }}</span>
      <span class="text-sm font-semibold" :class="progressTextClass">
        {{ displayText }}
      </span>
    </div>

    <!-- Progress Bar -->
    <div 
      class="relative w-full rounded-full overflow-hidden transition-all"
      :class="barHeightClass"
      :style="{ backgroundColor: trackColor }"
    >
      <!-- Progress Fill -->
      <div 
        class="h-full rounded-full transition-all duration-500 ease-out relative overflow-hidden"
        :class="progressBarClass"
        :style="progressStyle"
      >
        <!-- Shimmer Effect (Optional) -->
        <div 
          v-if="showShimmer && percentage > 0 && percentage < 100"
          class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"
        />
      </div>

      <!-- Percentage Text Inside Bar (Optional) -->
      <div 
        v-if="showPercentageInside && percentage > 15"
        class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white"
      >
        {{ Math.round(percentage) }}%
      </div>
    </div>

    <!-- Stats Below (Optional) -->
    <div v-if="showStats" class="flex items-center justify-between mt-2 text-xs text-gray-600">
      <span>{{ answeredCount }} đã trả lời</span>
      <span>{{ totalQuestions - answeredCount }} còn lại</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Số câu đã trả lời
  answeredCount: {
    type: Number,
    required: true,
    validator: (value) => value >= 0
  },

  // Tổng số câu hỏi
  totalQuestions: {
    type: Number,
    required: true,
    validator: (value) => value > 0
  },

  // Label hiển thị
  label: {
    type: String,
    default: 'Tiến độ'
  },

  // Hiển thị label
  showLabel: {
    type: Boolean,
    default: true
  },

  // Hiển thị thống kê
  showStats: {
    type: Boolean,
    default: false
  },

  // Hiển thị % bên trong thanh
  showPercentageInside: {
    type: Boolean,
    default: false
  },

  // Hiệu ứng shimmer
  showShimmer: {
    type: Boolean,
    default: true
  },

  // Variant màu sắc
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'warning', 'danger', 'info'].includes(value)
  },

  // Size thanh progress
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },

  // Màu track (nền)
  trackColor: {
    type: String,
    default: '#e5e7eb' // gray-200
  }
})

// Tính phần trăm
const percentage = computed(() => {
  if (props.totalQuestions === 0) return 0
  return (props.answeredCount / props.totalQuestions) * 100
})

// Text hiển thị
const displayText = computed(() => {
  return `${props.answeredCount}/${props.totalQuestions}`
})

// Style cho thanh progress
const progressStyle = computed(() => {
  return {
    width: `${percentage.value}%`
  }
})

// Class cho màu thanh progress
const progressBarClass = computed(() => {
  const classes = {
    primary: 'bg-gradient-to-r from-primary to-primary/80',
    success: 'bg-gradient-to-r from-green-500 to-green-600',
    warning: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
    danger: 'bg-gradient-to-r from-red-500 to-red-600',
    info: 'bg-gradient-to-r from-blue-500 to-blue-600'
  }
  return classes[props.variant] || classes.primary
})

// Class cho text màu
const progressTextClass = computed(() => {
  const classes = {
    primary: 'text-primary',
    success: 'text-green-600',
    warning: 'text-yellow-600',
    danger: 'text-red-600',
    info: 'text-blue-600'
  }
  return classes[props.variant] || classes.primary
})

// Class cho chiều cao thanh
const barHeightClass = computed(() => {
  const heights = {
    sm: 'h-1.5',
    md: 'h-2.5',
    lg: 'h-4'
  }
  return heights[props.size] || heights.md
})
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-shimmer {
  animation: shimmer 2s infinite;
}
</style>
