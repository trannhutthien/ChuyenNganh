<template>
  <div class="w-full">
    <!-- Label -->
    <label 
      v-if="label" 
      :for="inputId"
      class="block text-sm font-medium text-gray-700 mb-1"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative">
      <!-- Prefix Icon -->
      <div 
        v-if="$slots.prefix" 
        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
      >
        <slot name="prefix" />
      </div>

      <!-- Input / Textarea / Select -->
      <component
        :is="componentType"
        :id="inputId"
        :value="modelValue"
        @input="handleInput"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        :class="[
          'w-full border rounded-lg transition-all duration-200',
          'focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary',
          sizeClasses,
          $slots.prefix ? 'pl-10' : 'pl-3',
          $slots.suffix ? 'pr-10' : 'pr-3',
          disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white',
          error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300',
          inputType === 'textarea' ? 'resize-none' : ''
        ]"
      >
        <!-- Select Options -->
        <template v-if="inputType === 'select'">
          <option 
            v-for="option in options" 
            :key="option.value" 
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </template>
      </component>

      <!-- Suffix Icon -->
      <div 
        v-if="$slots.suffix" 
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
      >
        <slot name="suffix" />
      </div>
    </div>

    <!-- Error Message -->
    <p v-if="error" class="mt-1 text-sm text-red-500">
      {{ error }}
    </p>

    <!-- Hint -->
    <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  inputType: {
    type: String,
    default: 'input', // 'input', 'textarea', 'select'
    validator: (value) => ['input', 'textarea', 'select'].includes(value)
  },
  placeholder: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  rows: {
    type: Number,
    default: 4
  },
  options: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

// Generate unique ID
const inputId = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)

// Component type
const componentType = computed(() => {
  if (props.inputType === 'textarea') return 'textarea'
  if (props.inputType === 'select') return 'select'
  return 'input'
})

// Size classes
const sizeClasses = computed(() => {
  const sizes = {
    sm: 'py-1.5 text-sm',
    md: 'py-2 text-base',
    lg: 'py-3 text-lg'
  }
  return sizes[props.size] || sizes.md
})

// Handle input
const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>
