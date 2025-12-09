<template>
  <button 
    @click="handleClick"
    class="flex items-center gap-2 text-gray-600 hover:text-primary transition-colors"
    :class="containerClass"
  >
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
    <slot>Quay lại</slot>
  </button>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  to: {
    type: [String, Object],
    default: null
  },
  containerClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['click'])
const router = useRouter()

const handleClick = () => {
  emit('click')
  
  if (props.to) {
    router.push(props.to)
  } else {
    router.back()
  }
}
</script>
