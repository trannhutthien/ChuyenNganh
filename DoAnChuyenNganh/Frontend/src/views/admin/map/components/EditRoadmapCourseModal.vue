<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Chỉnh sửa khóa học</h2>
            <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Course Info -->
          <div class="p-5 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
              <div class="w-14 h-10 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                <img 
                  v-if="course?.thumbnail" 
                  :src="course.thumbnail" 
                  :alt="course.title"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-lg">📖</div>
              </div>
              <div class="min-w-0">
                <h4 class="font-medium text-gray-800 truncate">{{ course?.title }}</h4>
                <p class="text-xs text-gray-500">{{ course?.lessons || 0 }} bài học</p>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
            <!-- Required -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Loại khóa học</label>
              <div class="flex gap-3">
                <label 
                  class="flex-1 flex items-center gap-2 p-3 rounded-xl cursor-pointer border-2 transition-all"
                  :class="form.required ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'"
                >
                  <input type="radio" v-model="form.required" :value="true" class="hidden" />
                  <div 
                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                    :class="form.required ? 'border-primary' : 'border-gray-300'"
                  >
                    <div v-if="form.required" class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                  </div>
                  <span class="font-medium" :class="form.required ? 'text-primary' : 'text-gray-600'">Bắt buộc</span>
                </label>
                <label 
                  class="flex-1 flex items-center gap-2 p-3 rounded-xl cursor-pointer border-2 transition-all"
                  :class="!form.required ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'"
                >
                  <input type="radio" v-model="form.required" :value="false" class="hidden" />
                  <div 
                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                    :class="!form.required ? 'border-primary' : 'border-gray-300'"
                  >
                    <div v-if="!form.required" class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                  </div>
                  <span class="font-medium" :class="!form.required ? 'text-primary' : 'text-gray-600'">Tùy chọn</span>
                </label>
              </div>
            </div>

            <!-- Note -->
            <BaseInput
              v-model="form.note"
              label="Ghi chú"
              input-type="textarea"
              :rows="3"
              placeholder="VD: Nên hoàn thành trước khi học khóa tiếp theo..."
              hint="Ghi chú sẽ hiển thị cho học viên khi xem lộ trình"
            />

            <!-- Order -->
            <BaseInput
              v-model="form.order"
              label="Thứ tự"
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
              :loading="loading"
              variant="primary"
              size="sm"
            >
              Cập nhật
            </BaseButton>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { roadmapService } from '../../../../services/courseService.js'
import BaseInput from '../../../../components/ui/BaseInput.vue'
import BaseButton from '../../../../components/ui/BaseButton.vue'

const props = defineProps({
  isOpen: Boolean,
  course: Object,
  roadmapId: [String, Number]
})

const emit = defineEmits(['close', 'updated'])

const loading = ref(false)
const form = ref({
  required: true,
  note: '',
  order: 1
})

// Reset form khi mở modal
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.course) {
    form.value = {
      required: props.course.required ?? true,
      note: props.course.note || '',
      order: props.course.order || 1
    }
  }
})

const handleSubmit = async () => {
  loading.value = true
  try {
    await roadmapService.updateCourse(
      props.roadmapId, 
      props.course.courseId || props.course.id, 
      form.value
    )
    emit('updated')
  } catch (error) {
    console.error('Lỗi cập nhật:', error)
  } finally {
    loading.value = false
  }
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
</style>
