<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
        @click.self="$emit('update:modelValue', false)"
      >
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-xl font-semibold">
              {{ isEditing ? 'Sửa câu hỏi' : 'Thêm câu hỏi mới' }}
            </h3>
            <button
              @click="$emit('update:modelValue', false)"
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <!-- Loại câu hỏi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Loại câu hỏi <span class="text-red-500">*</span>
              </label>
              <select
                v-model="formData.loai"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option value="single">Một đáp án đúng</option>
                <option value="multiple">Nhiều đáp án đúng</option>
                <option value="true_false">Đúng/Sai</option>
                <option value="fill_blank">Điền khuyết</option>
              </select>
            </div>

            <!-- Đề bài -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Đề bài <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="formData.deBai"
                rows="4"
                placeholder="Nhập nội dung câu hỏi..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              ></textarea>
            </div>

            <!-- Độ khó -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Độ khó <span class="text-red-500">*</span>
              </label>
              <select
                v-model="formData.doKho"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option :value="1">⭐ Dễ</option>
                <option :value="2">⭐⭐ Trung bình</option>
                <option :value="3">⭐⭐⭐ Khó</option>
                <option :value="4">⭐⭐⭐⭐ Rất khó</option>
                <option :value="5">⭐⭐⭐⭐⭐ Cực khó</option>
              </select>
            </div>

            <!-- Lựa chọn -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">
                  Lựa chọn <span class="text-red-500">*</span> (Tối thiểu 2)
                </label>
                <button
                  @click="addChoice"
                  type="button"
                  class="text-sm text-primary hover:text-primary-dark font-medium"
                >
                  + Thêm lựa chọn
                </button>
              </div>
              
              <div class="space-y-2">
                <div
                  v-for="(choice, index) in localChoices"
                  :key="index"
                  class="flex items-center gap-2"
                >
                  <span class="flex-shrink-0 w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-sm font-medium">
                    {{ String.fromCharCode(65 + index) }}
                  </span>
                  <input
                    v-model="choice.noiDung"
                    type="text"
                    :placeholder="`Lựa chọn ${String.fromCharCode(65 + index)}`"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                  />
                  <label class="flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input
                      v-model="choice.dungHaySai"
                      type="checkbox"
                      class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                    />
                    <span class="text-sm text-gray-700">Đúng</span>
                  </label>
                  <button
                    v-if="localChoices.length > 2"
                    @click="removeChoice(index)"
                    type="button"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Giải thích -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Giải thích
              </label>
              <textarea
                v-model="formData.giaiThich"
                rows="3"
                placeholder="Giải thích đáp án đúng..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              ></textarea>
            </div>

            <!-- Chủ đề/Tags -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Chủ đề/Tags
              </label>
              <input
                v-model="formData.chuDeTags"
                type="text"
                placeholder="VD: HTML, CSS, JavaScript"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-end gap-3 p-6 border-t">
            <button
              @click="$emit('update:modelValue', false)"
              type="button"
              class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="handleSubmit"
              :disabled="loading"
              type="button"
              class="px-4 py-2 text-white bg-primary rounded-lg hover:bg-primary-dark disabled:opacity-50"
            >
              {{ loading ? 'Đang lưu...' : (isEditing ? 'Cập nhật' : 'Thêm câu hỏi') }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  isEditing: Boolean,
  initialData: Object,
  initialChoices: Array,
  loading: Boolean
})

const emit = defineEmits(['update:modelValue', 'submit'])

const formData = ref({
  loai: 'single',
  deBai: '',
  giaiThich: '',
  doKho: 1,
  chuDeTags: '',
  thuTu: 1
})

const localChoices = ref([
  { noiDung: '', dungHaySai: false, thuTu: 1 },
  { noiDung: '', dungHaySai: false, thuTu: 2 }
])

// Watch for initial data changes
watch(() => props.initialData, (newData) => {
  if (newData && Object.keys(newData).length > 0) {
    formData.value = { ...newData }
  } else {
    formData.value = {
      loai: 'MOT_DAP_AN',
      deBai: '',
      giaiThich: '',
      doKho: 1,
      chuDeTags: '',
      thuTu: 1
    }
  }
}, { immediate: true, deep: true })

watch(() => props.initialChoices, (newChoices) => {
  if (newChoices && newChoices.length > 0) {
    localChoices.value = [...newChoices]
  } else {
    localChoices.value = [
      { noiDung: '', dungHaySai: false, thuTu: 1 },
      { noiDung: '', dungHaySai: false, thuTu: 2 }
    ]
  }
}, { immediate: true, deep: true })

const addChoice = () => {
  localChoices.value.push({
    noiDung: '',
    dungHaySai: false,
    thuTu: localChoices.value.length + 1
  })
}

const removeChoice = (index) => {
  if (localChoices.value.length > 2) {
    localChoices.value.splice(index, 1)
    // Cập nhật lại thứ tự
    localChoices.value.forEach((choice, idx) => {
      choice.thuTu = idx + 1
    })
  }
}

const handleSubmit = () => {
  emit('submit', {
    ...formData.value,
    luaChons: localChoices.value
  })
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
