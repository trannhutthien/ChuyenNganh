<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Variant của button
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'outline', 'ghost', 'link'].includes(value)
  },
  // Size của button
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  // Loading state
  loading: {
    type: Boolean,
    default: false
  },
  // Disabled state
  disabled: {
    type: Boolean,
    default: false
  },
  // Full width
  fullWidth: {
    type: Boolean,
    default: false
  },
  // Icon position
  iconPosition: {
    type: String,
    default: 'left',
    validator: (value) => ['left', 'right'].includes(value)
  },
  // Rounded
  rounded: {
    type: String,
    default: 'md',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'full'].includes(value)
  }
})

const emit = defineEmits(['click'])

// Computed classes dựa trên variant
const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-primary text-white hover:bg-primary-600 shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40',
    secondary: 'bg-gray-600 text-white hover:bg-gray-700 shadow-lg shadow-gray-600/30 hover:shadow-xl hover:shadow-gray-600/40',
    success: 'bg-green-500 text-white hover:bg-green-600 shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40',
    danger: 'bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40',
    warning: 'bg-yellow-500 text-white hover:bg-yellow-600 shadow-lg shadow-yellow-500/30 hover:shadow-xl hover:shadow-yellow-500/40',
    info: 'bg-blue-500 text-white hover:bg-blue-600 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40',
    outline: 'bg-transparent border-2 border-primary text-primary hover:bg-primary hover:text-white shadow-md hover:shadow-lg hover:shadow-primary/20',
    ghost: 'bg-transparent text-primary hover:bg-primary/10 hover:shadow-md',
    link: 'bg-transparent text-primary hover:text-primary-600 underline-offset-4 hover:underline'
  }
  return variants[props.variant] || variants.primary
})

// Computed classes dựa trên size
const sizeClasses = computed(() => {
  const sizes = {
    xs: 'px-3 py-1.5 text-xs',
    sm: 'px-4 py-2 text-sm',
    md: 'px-6 py-3 text-base',
    lg: 'px-8 py-4 text-lg',
    xl: 'px-10 py-5 text-xl'
  }
  return sizes[props.size] || sizes.md
})

// Computed classes dựa trên rounded
const roundedClasses = computed(() => {
  const rounded = {
    none: 'rounded-none',
    sm: 'rounded-sm',
    md: 'rounded-lg',
    lg: 'rounded-xl',
    full: 'rounded-full'
  }
  return rounded[props.rounded] || rounded.md
})

// Computed disabled classes
const disabledClasses = computed(() => {
  return props.disabled || props.loading
    ? 'opacity-50 cursor-not-allowed pointer-events-none'
    : 'cursor-pointer'
})

// Computed full width
const widthClass = computed(() => {
  return props.fullWidth ? 'w-full' : ''
})

// Handle click
const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<template>
  <button
    :class="[
      'inline-flex items-center justify-center gap-2 font-semibold transition-all duration-300 transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary',
      variantClasses,
      sizeClasses,
      roundedClasses,
      disabledClasses,
      widthClass
    ]"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <!-- Loading spinner -->
    <svg
      v-if="loading"
      class="animate-spin h-5 w-5"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>

    <!-- Icon bên trái -->
    <span v-if="$slots.icon && iconPosition === 'left' && !loading" class="flex-shrink-0">
      <slot name="icon" />
    </span>

    <!-- Text content -->
    <span v-if="!loading || $slots.default">
      <slot />
    </span>

    <!-- Icon bên phải -->
    <span v-if="$slots.icon && iconPosition === 'right' && !loading" class="flex-shrink-0">
      <slot name="icon" />
    </span>
  </button>
</template>

<style scoped>
/* Custom animation cho hover */
button {
  position: relative;
  overflow: hidden;
}

button::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

button:hover::before {
  width: 300px;
  height: 300px;
}

button:active::before {
  width: 0;
  height: 0;
}
</style>
