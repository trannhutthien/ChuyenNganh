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
        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-2xl relative z-10 max-h-[90vh] overflow-y-auto">
          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">
              {{ isEdit ? 'Sửa khóa học' : 'Thêm khóa học mới' }}
            </h2>
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
            <!-- Tiêu đề -->
            <BaseInput
              v-model="form.TieuDe"
              label="Tiêu đề khóa học"
              placeholder="VD: Lập Trình PHP & MySQL"
              required
            />

            <!-- Hình ảnh URL -->
            <BaseInput
              v-model="form.HinhAnhUrl"
              label="Hình ảnh (URL)"
              placeholder="/images/phpmysql.jpg"
              required
            />

            <!-- Tóm tắt -->
            <BaseInput
              v-model="form.TomTat"
              label="Tóm tắt khóa học"
              input-type="textarea"
              placeholder="Mô tả ngắn về nội dung khóa học..."
              :rows="4"
              required
            />

            <!-- Cấp độ -->
            <BaseInput
              v-model="form.CapDo"
              label="Cấp độ"
              input-type="select"
              :options="levelOptions"
              required
            />

            <!-- Tags -->
            <BaseInput
              v-model="form.Tags"
              label="Tags (ngăn cách bằng dấu phẩy)"
              placeholder="php,mysql,backend"
            />

            <!-- Điều kiện tiên quyết -->
            <BaseInput
              v-model="form.DieuKienTienQuyet"
              label="Điều kiện tiên quyết"
              placeholder="VD: PHP cơ bản"
            />

            <!-- Giá tiền -->
            <BaseInput
              v-model="form.GiaTien"
              label="Giá tiền (VNĐ)"
              type="number"
              placeholder="399000"
              required
            />

            <!-- Trạng thái -->
            <BaseInput
              v-model="form.TrangThai"
              label="Trạng thái"
              input-type="select"
              :options="statusOptions"
              required
            />

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
              <BaseButton
                type="button"
                variant="outline"
                full-width
                @click="closeModal"
              >
                Hủy
              </BaseButton>
              <BaseButton
                type="submit"
                variant="primary"
                full-width
                :loading="loading"
                :disabled="loading"
              >
                {{ isEdit ? 'Cập nhật' : 'Thêm khóa học' }}
              </BaseButton>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  isEdit: {
    type: Boolean,
    default: false
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

// Select options
const levelOptions = [
  { value: 1, label: '1 - Cơ bản' },
  { value: 2, label: '2 - Trung bình' },
  { value: 3, label: '3 - Nâng cao' }
]

const statusOptions = [
  { value: 1, label: 'Hoạt động' },
  { value: 0, label: 'Chờ duyệt' },
  { value: -1, label: 'Nháp' }
]

// Form data
const form = ref({
  TieuDe: '',
  HinhAnhUrl: '',
  TomTat: '',
  CapDo: 1,
  Tags: '',
  DieuKienTienQuyet: '',
  GiaTien: 0,
  TrangThai: 1
})

// Reset form when modal opens
watch(
  () => [props.modelValue, props.initialData],
  ([isOpen]) => {
    if (isOpen) {
      if (props.isEdit && props.initialData) {
        form.value = { ...props.initialData }
      } else {
        // Reset to default
        form.value = {
          TieuDe: '',
          HinhAnhUrl: '',
          TomTat: '',
          CapDo: 1,
          Tags: '',
          DieuKienTienQuyet: '',
          GiaTien: 0,
          TrangThai: 1
        }
      }
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
