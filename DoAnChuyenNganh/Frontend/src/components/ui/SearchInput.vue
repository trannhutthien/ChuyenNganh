<template>
  <div class="relative" :class="containerClass">
    <!-- Icon tìm kiếm -->
    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke-width="1.5" 
        stroke="currentColor" 
        class="w-5 h-5"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" 
        />
      </svg>
    </div>
    
    <!-- Input tìm kiếm -->
    <input 
      :value="modelValue"
      @input="handleInput"
      @keyup.enter="$emit('search', modelValue)"
      type="text" 
      :placeholder="placeholder"
      :class="[
        'w-full pl-10 pr-4 rounded-xl border border-gray-300',
        'focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
        'text-sm transition-all',
        sizeClasses
      ]"
    />

    <!-- Nút clear (hiện khi có text) -->
    <button
      v-if="modelValue && showClear"
      @click="handleClear"
      type="button"
      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
    >
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
          d="M6 18 18 6M6 6l12 12" 
        />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Tìm kiếm...'
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg'
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  containerClass: {
    type: String,
    default: ''
  },
  showClear: {
    type: Boolean,
    default: true
  },
  // Tìm kiếm realtime khi gõ
  realtime: {
    type: Boolean,
    default: false
  },
  // Debounce delay (ms) cho realtime search
  debounce: {
    type: Number,
    default: 300
  }
})

const emit = defineEmits(['update:modelValue', 'search', 'clear'])

// Size classes
const sizeClasses = computed(() => {
  const sizes = {
    sm: 'h-8 text-xs',
    md: 'h-[36px] text-sm',
    lg: 'h-11 text-base'
  }
  return sizes[props.size]
})

// Debounce timer
let debounceTimer = null

// Handle input với debounce cho realtime search
const handleInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
  
  // Nếu bật realtime search
  if (props.realtime) {
    // Clear timer cũ
    if (debounceTimer) {
      clearTimeout(debounceTimer)
    }
    // Set timer mới
    debounceTimer = setTimeout(() => {
      emit('search', value)
    }, props.debounce)
  }
}

// Clear input
const handleClear = () => {
  emit('update:modelValue', '')
  emit('clear')
  // Trigger search với empty string khi clear
  if (props.realtime) {
    emit('search', '')
  }
}
</script>
