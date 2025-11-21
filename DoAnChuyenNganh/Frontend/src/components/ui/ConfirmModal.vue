<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="show" 
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click="handleOverlayClick"
      >
        <Transition
          enter-active-class="transition-all duration-300"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div 
            v-if="show"
            class="w-full bg-white rounded-2xl shadow-2xl max-h-[90vh] overflow-y-auto"
            :class="sizeClass"
            @click.stop
          >
            <!-- Header -->
            <div 
              v-if="showHeader" 
              class="flex items-center justify-between px-6 py-4 border-b border-gray-200"
            >
              <div class="flex items-center gap-3">
                <h3 class="text-xl font-bold text-gray-900">{{ title }}</h3>
              </div>
              <button 
                v-if="showClose"
                @click="handleCancel" 
                :disabled="loading"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-50"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
              <slot>
                <p class="text-gray-600">{{ message }}</p>
              </slot>
            </div>

            <!-- Footer -->
            <div 
              v-if="showFooter" 
              class="flex gap-3 px-6 py-4 border-t border-gray-200"
            >
              <slot name="footer">
                <BaseButton 
                  v-if="showCancelButton"
                  @click="handleCancel"
                  variant="secondary"
                  :disabled="loading"
                  class="flex-1"
                >
                  {{ cancelText }}
                </BaseButton>

                <BaseButton 
                  v-if="showConfirmButton"
                  @click="handleConfirm"
                  :variant="confirmVariant"
                  :loading="loading"
                  class="flex-1"
                >
                  {{ loading ? loadingText : confirmText }}
                </BaseButton>
              </slot>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import BaseButton from './BaseButton.vue'

const props = defineProps({
  show: { type: Boolean, required: true },
  title: { type: String, default: 'Xác nhận' },
  message: { type: String, default: 'Bạn có chắc chắn?' },
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  loading: { type: Boolean, default: false },
  loadingText: { type: String, default: 'Đang xử lý...' },
  showHeader: { type: Boolean, default: true },
  showClose: { type: Boolean, default: true },
  showFooter: { type: Boolean, default: true },
  showCancelButton: { type: Boolean, default: true },
  showConfirmButton: { type: Boolean, default: true },
  cancelText: { type: String, default: 'Hủy' },
  confirmText: { type: String, default: 'Xác nhận' },
  confirmVariant: { type: String, default: 'primary' },
  closeOnOverlay: { type: Boolean, default: true },
  closeOnEscape: { type: Boolean, default: true },
  closeOnConfirm: { type: Boolean, default: true }
})

const emit = defineEmits(['confirm', 'cancel', 'update:show'])

const sizeClass = computed(() => {
  const sizes = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    full: 'max-w-full'
  }
  return sizes[props.size] || sizes.md
})

const handleConfirm = () => {
  if (props.loading) return
  emit('confirm')
  if (props.closeOnConfirm) {
    emit('update:show', false)
  }
}

const handleCancel = () => {
  if (props.loading) return
  emit('cancel')
  emit('update:show', false)
}

const handleOverlayClick = () => {
  if (props.closeOnOverlay && !props.loading) {
    handleCancel()
  }
}
</script>
