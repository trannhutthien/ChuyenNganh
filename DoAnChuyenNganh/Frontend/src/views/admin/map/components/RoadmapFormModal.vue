<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50" @click="$emit('close')"></div>

        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">
              {{ roadmap ? 'Chỉnh sửa lộ trình' : 'Thêm lộ trình mới' }}
            </h2>
            <button @click="$emit('close')" class="p-1 text-gray-400 hover:text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
            <!-- Icon -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
              <div class="flex gap-2">
                <input
                  v-model="form.icon"
                  type="text"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="🎨"
                  maxlength="4"
                />
                <div class="flex gap-1">
                  <button
                    v-for="emoji in suggestedEmojis"
                    :key="emoji"
                    type="button"
                    @click="form.icon = emoji"
                    class="w-10 h-10 text-xl hover:bg-gray-100 rounded-lg"
                  >
                    {{ emoji }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Title -->
            <BaseInput
              v-model="form.title"
              label="Tên lộ trình"
              placeholder="VD: Frontend Developer"
              required
            />

            <!-- Description -->
            <BaseInput
              v-model="form.description"
              label="Mô tả"
              input-type="textarea"
              :rows="3"
              placeholder="Mô tả ngắn về lộ trình..."
            />

            <!-- Duration -->
            <BaseInput
              v-model="form.duration"
              label="Thời gian hoàn thành"
              placeholder="VD: 6 tháng"
            />

            <!-- Level -->
            <BaseInput
              v-model="form.level"
              label="Cấp độ"
              input-type="select"
              :options="[
                { value: 1, label: 'Beginner' },
                { value: 2, label: 'Intermediate' },
                { value: 3, label: 'Advanced' }
              ]"
            />

            <!-- Status -->
            <BaseInput
              v-model="form.status"
              label="Trạng thái"
              input-type="select"
              :options="[
                { value: 0, label: 'Draft (Nháp)' },
                { value: 1, label: 'Active (Hiển thị)' }
              ]"
            />

            <!-- Order -->
            <BaseInput
              v-model="form.order"
              label="Thứ tự hiển thị"
              type="number"
            />
          </form>

          <!-- Footer -->
          <div class="flex justify-end gap-3 p-5 border-t border-gray-200">
            <BaseButton @click="$emit('close')" variant="secondary" size="sm">
              Hủy
            </BaseButton>
            <BaseButton
              @click="handleSubmit"
              :disabled="!form.title"
              :loading="loading"
              variant="primary"
              size="sm"
            >
              {{ roadmap ? 'Cập nhật' : 'Tạo mới' }}
            </BaseButton>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import BaseInput from '../../../../components/ui/BaseInput.vue'
import BaseButton from '../../../../components/ui/BaseButton.vue'

const props = defineProps({
  isOpen: Boolean,
  roadmap: Object,
  loading: Boolean
})

const emit = defineEmits(['close', 'submit'])

const suggestedEmojis = ['🎨', '⚙️', '🚀', '📱', '☁️', '🤖', '🔧', '💻']

const form = ref({
  icon: '📚',
  title: '',
  description: '',
  duration: '',
  level: 1,
  status: 0,
  order: 1
})

// Reset form khi modal mở
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    if (props.roadmap) {
      form.value = {
        icon: props.roadmap.icon || '📚',
        title: props.roadmap.title || '',
        description: props.roadmap.description || '',
        duration: props.roadmap.duration || '',
        level: props.roadmap.level || 1,
        status: props.roadmap.status || 0,
        order: props.roadmap.order || 1
      }
    } else {
      form.value = {
        icon: '📚',
        title: '',
        description: '',
        duration: '',
        level: 1,
        status: 0,
        order: 1
      }
    }
  }
})

const handleSubmit = () => {
  if (!form.value.title) return
  emit('submit', { ...form.value })
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: all 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
}
</style>
