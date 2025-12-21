<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>

        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <div>
              <h2 class="text-xl font-bold text-gray-800">Thêm khóa học vào lộ trình</h2>
              <p class="text-sm text-gray-500 mt-1">Chọn khóa học từ danh sách có sẵn</p>
            </div>
            <button @click="$emit('close')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Search -->
          <div class="p-4 border-b border-gray-100">
            <SearchInput
              v-model="searchQuery"
              placeholder="Tìm kiếm khóa học..."
              size="md"
              container-class="w-full"
            />
          </div>

          <!-- Course List -->
          <div class="flex-1 overflow-y-auto p-4">
            <!-- Loading -->
            <div v-if="loadingCourses" class="flex justify-center py-10">
              <div class="w-8 h-8 border-3 border-primary border-t-transparent rounded-full animate-spin"></div>
            </div>

            <!-- Course Items -->
            <div v-else class="space-y-2">
              <div
                v-for="course in filteredCourses"
                :key="course.id"
                @click="selectCourse(course)"
                class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all"
                :class="selectedCourseId === course.id 
                  ? 'bg-primary/10 border-2 border-primary' 
                  : 'bg-gray-50 hover:bg-gray-100 border-2 border-transparent'"
              >
                <!-- Thumbnail -->
                <div class="w-16 h-12 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                  <img 
                    v-if="course.thumbnail" 
                    :src="course.thumbnail" 
                    :alt="course.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-xl">📖</div>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <h4 class="font-medium text-gray-800 truncate">{{ course.title }}</h4>
                  <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                    <span>{{ course.lessons || 0 }} bài học</span>
                    <span :class="course.price > 0 ? 'text-orange-500' : 'text-green-500'">
                      {{ course.price > 0 ? formatPrice(course.price) : 'Miễn phí' }}
                    </span>
                  </div>
                </div>

                <!-- Check Icon -->
                <div v-if="selectedCourseId === course.id" class="text-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
                </div>
              </div>

              <!-- Empty -->
              <div v-if="filteredCourses.length === 0" class="text-center py-10 text-gray-500">
                <p>Không tìm thấy khóa học phù hợp</p>
              </div>
            </div>
          </div>

          <!-- Options (khi đã chọn khóa học) -->
          <div v-if="selectedCourseId" class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
                <select
                  v-model="courseOptions.required"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                  <option :value="1">Bắt buộc</option>
                  <option :value="0">Tùy chọn</option>
                </select>
              </div>
              <BaseInput
                v-model="courseOptions.note"
                label="Ghi chú"
                placeholder="VD: Nên học trước..."
              />
            </div>
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-3 p-4 border-t border-gray-200">
            <BaseButton @click="$emit('close')" variant="secondary" size="sm">
              Hủy
            </BaseButton>
            <BaseButton
              @click="handleAdd"
              :disabled="!selectedCourseId"
              :loading="loading"
              variant="primary"
              size="sm"
            >
              Thêm vào lộ trình
            </BaseButton>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { courseService, roadmapService } from '../../../../services/courseService.js'
import SearchInput from '../../../../components/ui/SearchInput.vue'
import BaseInput from '../../../../components/ui/BaseInput.vue'
import BaseButton from '../../../../components/ui/BaseButton.vue'

const props = defineProps({
  isOpen: Boolean,
  roadmapId: [String, Number],
  existingCourseIds: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'added'])

const allCourses = ref([])
const loadingCourses = ref(false)
const searchQuery = ref('')
const selectedCourseId = ref(null)
const loading = ref(false)

const courseOptions = ref({
  required: 1,
  note: ''
})

// Lọc khóa học chưa có trong lộ trình
const filteredCourses = computed(() => {
  let result = allCourses.value.filter(c => !props.existingCourseIds.includes(c.id))
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(c => c.title?.toLowerCase().includes(q))
  }
  
  return result
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(price)
}

const fetchCourses = async () => {
  loadingCourses.value = true
  try {
    const response = await courseService.getAllAdmin()
    allCourses.value = response.data || []
  } catch (error) {
    console.error('Lỗi tải khóa học:', error)
  } finally {
    loadingCourses.value = false
  }
}

const selectCourse = (course) => {
  selectedCourseId.value = course.id
}

const handleAdd = async () => {
  if (!selectedCourseId.value) return
  
  loading.value = true
  try {
    await roadmapService.addCourse(props.roadmapId, {
      khoaHocId: selectedCourseId.value,
      required: courseOptions.value.required,
      note: courseOptions.value.note
    })
    emit('added')
  } catch (error) {
    console.error('Lỗi thêm khóa học:', error)
  } finally {
    loading.value = false
  }
}

// Reset khi mở modal
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    selectedCourseId.value = null
    searchQuery.value = ''
    courseOptions.value = { required: 1, note: '' }
    fetchCourses()
  }
})
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
