<template>
  <div 
    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300"
    :class="timerClass"
  >
    <!-- Clock Icon -->
    <div class="flex-shrink-0">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-6 h-6 transition-transform"
        :class="{ 'animate-pulse': isWarning }"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
      </svg>
    </div>

    <!-- Time Display -->
    <div class="flex flex-col">
      <span class="text-xs font-medium opacity-80">{{ label }}</span>
      <span class="text-2xl font-bold tabular-nums tracking-tight">
        {{ formattedTime }}
      </span>
    </div>

    <!-- Warning Icon (Optional) -->
    <div v-if="showWarningIcon && isWarning" class="ml-auto flex-shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 animate-bounce">
        <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Thời gian còn lại (giây)
  timeLeft: {
    type: Number,
    required: true,
    validator: (value) => value >= 0
  },

  // Ngưỡng cảnh báo (giây)
  warningThreshold: {
    type: Number,
    default: 300 // 5 phút
  },

  // Label hiển thị
  label: {
    type: String,
    default: 'Thời gian còn lại'
  },

  // Hiển thị icon cảnh báo
  showWarningIcon: {
    type: Boolean,
    default: true
  },

  // Variant màu sắc
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'primary', 'danger'].includes(value)
  },

  // Size
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

// Kiểm tra đang trong trạng thái cảnh báo
const isWarning = computed(() => {
  return props.timeLeft <= props.warningThreshold
})

// Format thời gian
const formattedTime = computed(() => {
  const mins = Math.floor(props.timeLeft / 60)
  const secs = props.timeLeft % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
})

// Class cho timer dựa trên trạng thái
const timerClass = computed(() => {
  const baseClass = []
  
  // Variant classes
  if (isWarning.value) {
    baseClass.push('bg-red-50 text-red-700 border-2 border-red-300')
  } else {
    switch (props.variant) {
      case 'primary':
        baseClass.push('bg-primary/10 text-primary border border-primary/30')
        break
      case 'danger':
        baseClass.push('bg-red-50 text-red-700 border border-red-300')
        break
      default:
        baseClass.push('bg-gray-50 text-gray-700 border border-gray-300')
    }
  }

  // Size classes
  switch (props.size) {
    case 'sm':
      baseClass.push('text-sm')
      break
    case 'lg':
      baseClass.push('text-lg')
      break
    default:
      baseClass.push('text-base')
  }

  return baseClass.join(' ')
})
</script>
