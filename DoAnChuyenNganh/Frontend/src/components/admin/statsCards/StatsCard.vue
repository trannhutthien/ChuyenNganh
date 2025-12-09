<template>
  <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
    <div class="flex items-center gap-3">
      <div 
        class="w-12 h-12 rounded-lg flex items-center justify-center"
        :class="bgColorClass"
      >
        <slot name="icon">
          <!-- Default icon -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="['w-6 h-6', iconColorClass]">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5" />
          </svg>
        </slot>
      </div>
      <div>
        <p class="text-2xl font-bold text-gray-800">{{ formatValue(value) }}</p>
        <p class="text-sm text-gray-600">{{ label }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: [Number, String],
    default: 0
  },
  label: {
    type: String,
    default: ''
  },
  color: {
    type: String,
    default: 'blue', // blue, green, yellow, purple, red, primary
    validator: (value) => ['blue', 'green', 'yellow', 'purple', 'red', 'primary', 'orange', 'pink', 'indigo'].includes(value)
  },
  formatNumber: {
    type: Boolean,
    default: true
  }
})

// Background color classes
const bgColorClass = computed(() => {
  const colors = {
    blue: 'bg-blue-100',
    green: 'bg-green-100',
    yellow: 'bg-yellow-100',
    purple: 'bg-purple-100',
    red: 'bg-red-100',
    primary: 'bg-primary/10',
    orange: 'bg-orange-100',
    pink: 'bg-pink-100',
    indigo: 'bg-indigo-100'
  }
  return colors[props.color] || colors.blue
})

// Icon color classes
const iconColorClass = computed(() => {
  const colors = {
    blue: 'text-blue-600',
    green: 'text-green-600',
    yellow: 'text-yellow-600',
    purple: 'text-purple-600',
    red: 'text-red-600',
    primary: 'text-primary',
    orange: 'text-orange-600',
    pink: 'text-pink-600',
    indigo: 'text-indigo-600'
  }
  return colors[props.color] || colors.blue
})

// Format number with thousand separator
const formatValue = (val) => {
  if (props.formatNumber && typeof val === 'number') {
    return val.toLocaleString('vi-VN')
  }
  return val
}
</script>
