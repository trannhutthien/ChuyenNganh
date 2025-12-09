<template>
  <Teleport to="body">
    <Transition name="modal">
      <div 
        v-if="modelValue" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div class="bg-white p-6 rounded-xl shadow-xl w-full relative z-10 max-h-[90vh] overflow-y-auto" :class="sizeClass">
          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">{{ title }}</h2>
            <button 
              @click="closeModal"
              class="p-1 hover:bg-gray-100 rounded-full transition"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Dynamic Fields -->
            <template v-for="field in visibleFields" :key="field.name">
              <BaseInput
                v-model="form[field.name]"
                :label="field.label"
                :type="field.type || 'text'"
                :input-type="field.inputType || 'input'"
                :placeholder="field.placeholder"
                :required="field.required"
                :options="field.options"
                :rows="field.rows || 4"
              />
            </template>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
              <BaseButton
                type="button"
                variant="outline"
                full-width
                @click="closeModal"
              >
                {{ cancelText }}
              </BaseButton>
              <BaseButton
                type="submit"
                variant="primary"
                full-width
                :loading="loading"
                :disabled="loading"
              >
                {{ submitText }}
              </BaseButton>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Form'
  },
  submitText: {
    type: String,
    default: 'Lưu'
  },
  cancelText: {
    type: String,
    default: 'Hủy'
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg', 'xl'
    validator: (v) => ['sm', 'md', 'lg', 'xl'].includes(v)
  },
  fields: {
    type: Array,
    required: true
    // Example: [{ name: 'TieuDe', label: 'Tiêu đề', type: 'text', inputType: 'input', placeholder: '', required: true, options: [], showIf: (form) => true }]
  },
  initialData: {
    type: Object,
    default: () => ({})
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'submit'])

// Size class
const sizeClass = computed(() => {
  const sizes = {
    sm: 'max-w-md',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl'
  }
  return sizes[props.size] || sizes.md
})

// Form data
const form = ref({})

// Visible fields (dựa vào điều kiện showIf)
const visibleFields = computed(() => {
  return props.fields.filter(field => {
    if (typeof field.showIf === 'function') {
      return field.showIf(form.value)
    }
    return true
  })
})

// Initialize form khi modal mở hoặc fields/initialData thay đổi
watch(
  () => [props.modelValue, props.initialData, props.fields],
  ([isOpen]) => {
    if (isOpen) {
      const data = {}
      props.fields.forEach(field => {
        // Ưu tiên initialData, sau đó default value
        data[field.name] = props.initialData[field.name] ?? field.default ?? ''
      })
      form.value = data
    }
  },
  { immediate: true, deep: true }
)

const closeModal = () => {
  emit('update:modelValue', false)
}

const handleSubmit = () => {
  emit('submit', { ...form.value })
}
</script>

<style scoped>
/* Modal Transition */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.95);
}
</style>
