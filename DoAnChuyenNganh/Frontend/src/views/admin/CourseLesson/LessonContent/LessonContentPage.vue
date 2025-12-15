<template>
  <div class="p-6">
    <!-- Back Button & Header -->
    <div class="mb-6">
      <BackButton :to="`/quan-ly/khoa-hoc/${courseId}/bai-hoc`" container-class="mb-4">
        Quay lại danh sách bài học
      </BackButton>
      
      <!-- Lesson Info Header -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-start gap-4">
          <!-- Lesson Type Icon -->
          <div :class="['w-16 h-16 rounded-xl flex items-center justify-center', typeIconClass]">
            <svg v-if="lesson.type === 'video'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
            <svg v-else-if="lesson.type === 'text' || lesson.type === 'paragraph'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
          </div>
          
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">
                Bài {{ lesson.order }}
              </span>
              <BadgeLabel :value="lesson.status" type="status" />
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ lesson.title }}</h1>
            <p class="text-gray-600 mt-1">{{ lesson.description || 'Quản lý nội dung chi tiết của bài học' }}</p>
            <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
              <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ lesson.duration || 0 }} phút
              </span>
              <span class="flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                {{ contents.length }} phần nội dung
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <!-- Content Type Filter -->
      <div class="flex items-center gap-2">
        <button 
          v-for="filter in contentFilters" 
          :key="filter.value"
          @click="activeFilter = filter.value"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
            activeFilter === filter.value 
              ? 'bg-primary text-white' 
              : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'
          ]"
        >
          {{ filter.label }}
        </button>
      </div>

      <!-- Add Button -->
      <BaseButton 
        @click="openAddModal"
        variant="primary"
        size="sm"
      >
        <template #icon>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
        </template>
        Thêm nội dung
      </BaseButton>
    </div>

    <!-- Content List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Loading State -->
      <div v-if="isLoading" class="p-8 text-center">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto"></div>
        <p class="mt-4 text-gray-500">Đang tải nội dung...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredContents.length === 0" class="p-12 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có nội dung nào</h3>
        <p class="mt-2 text-gray-500">Bắt đầu thêm nội dung cho bài học này</p>
        <BaseButton 
          @click="openAddModal"
          variant="primary"
          size="sm"
          class="mt-4"
        >
          Thêm nội dung đầu tiên
        </BaseButton>
      </div>

      <!-- Content Items -->
      <draggable 
        v-else
        v-model="contents"
        item-key="id"
        handle=".drag-handle"
        @end="onDragEnd"
        class="divide-y divide-gray-100"
      >
        <template #item="{ element: content, index }">
          <ContentItem
            :content="content"
            :index="index"
            @edit="editContent"
            @delete="confirmDeleteContent"
            @preview="previewContent"
          />
        </template>
      </draggable>
    </div>

    <!-- Add/Edit Content Modal -->
    <FormAddModal
      v-model="showContentModal"
      :title="isEditing ? 'Sửa nội dung' : 'Thêm nội dung mới'"
      :submit-text="isEditing ? 'Cập nhật' : 'Thêm nội dung'"
      :fields="contentFormFields"
      :initial-data="editingContent"
      :loading="isSubmitting"
      size="xl"
      @submit="handleContentSubmit"
    />

    <!-- Preview Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div 
          v-if="showPreview" 
          class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
          @click.self="showPreview = false"
        >
          <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b">
              <h3 class="text-lg font-semibold">Xem trước nội dung</h3>
              <button @click="showPreview = false" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
              <!-- Video Preview -->
              <div v-if="previewData?.type === 'video'" class="aspect-video bg-black rounded-lg overflow-hidden">
                <iframe 
                  v-if="previewData.content"
                  :src="getEmbedUrl(previewData.content)"
                  class="w-full h-full"
                  frameborder="0"
                  allowfullscreen
                ></iframe>
              </div>
              <!-- Heading Preview -->
              <div v-else-if="previewData?.type === 'heading'" class="prose max-w-none">
                <h1 class="text-3xl font-bold">{{ previewData.content }}</h1>
              </div>
              <!-- Subheading Preview -->
              <div v-else-if="previewData?.type === 'subheading'" class="prose max-w-none">
                <h2 class="text-2xl font-semibold">{{ previewData.content }}</h2>
              </div>
              <!-- Paragraph/Text Preview -->
              <div v-else-if="previewData?.type === 'paragraph'" class="prose max-w-none" v-html="previewData.content"></div>
              <!-- Quote Preview -->
              <div v-else-if="previewData?.type === 'quote'" class="border-l-4 border-primary pl-4 italic text-gray-700">
                {{ previewData.content }}
              </div>
              <!-- List Preview -->
              <div v-else-if="previewData?.type === 'list'" class="prose max-w-none" v-html="previewData.content"></div>
              <!-- Image Preview -->
              <div v-else-if="previewData?.type === 'image'" class="text-center">
                <img :src="previewData.content" class="max-w-full rounded-lg mx-auto" />
              </div>
              <!-- Default -->
              <div v-else class="text-gray-500 text-center py-8">
                Không có nội dung để xem trước
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      v-model:show="showDeleteConfirm"
      title="Xóa nội dung"
      :message="`Bạn có chắc chắn muốn xóa phần nội dung này? Hành động này không thể hoàn tác.`"
      confirm-text="Xóa"
      confirm-variant="danger"
      @confirm="handleDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import draggable from 'vuedraggable'
import BackButton from '../../../../components/ui/BackButton.vue'
import BaseButton from '../../../../components/ui/BaseButton.vue'
import BadgeLabel from '../../../../components/admin/BadgeLabel.vue'
import FormAddModal from '../../../../components/modal/FormAddModal.vue'
import ConfirmModal from '../../../../components/modal/ConfirmModal.vue'
import ContentItem from '../../../../components/admin/CourseLesson/ContentItem.vue'
import api from '../../../../services/api'

const route = useRoute()
const router = useRouter()

// Route params
const courseId = computed(() => route.params.courseId)
const lessonId = computed(() => route.params.lessonId)

// Lesson info
const lesson = ref({
  title: '',
  description: '',
  type: 'video',
  order: 1,
  duration: 0,
  status: 1
})

// Content data
const contents = ref([])
const isLoading = ref(false)

// Filter
const activeFilter = ref('all')
const contentFilters = [
  { value: 'all', label: 'Tất cả' },
  { value: 'paragraph', label: 'Văn bản' },
  { value: 'heading', label: 'Tiêu đề' },
  { value: 'video', label: 'Video' },
  { value: 'image', label: 'Hình ảnh' },
  { value: 'quote', label: 'Trích dẫn' },
  { value: 'list', label: 'Danh sách' }
]

// Modal states
const showContentModal = ref(false)
const isEditing = ref(false)
const isSubmitting = ref(false)
const editingContent = ref({})

// Preview
const showPreview = ref(false)
const previewData = ref(null)

// Delete
const showDeleteConfirm = ref(false)
const deletingContent = ref(null)

// Form fields config
const contentFormFields = computed(() => [
  {
    name: 'LoaiNoiDung',
    label: 'Loại nội dung',
    inputType: 'select',
    required: true,
    default: 'paragraph',
    options: [
      { value: 'heading', label: '📌 Tiêu đề chính' },
      { value: 'subheading', label: '📍 Tiêu đề phụ' },
      { value: 'paragraph', label: '📝 Đoạn văn bản/HTML' },
      { value: 'image', label: '🖼️ Hình ảnh' },
      { value: 'video', label: '🎬 Video (YouTube/Vimeo)' },
      { value: 'quote', label: '💬 Trích dẫn' },
      { value: 'list', label: '📋 Danh sách' }
    ]
  },
  {
    name: 'TieuDe',
    label: 'Tiêu đề',
    type: 'text',
    placeholder: 'VD: Giới thiệu về HTML',
    required: true,
    default: ''
  },
  {
    name: 'NoiDung',
    label: 'Nội dung',
    inputType: 'textarea',
    placeholder: 'Nhập nội dung văn bản, URL video, URL hình ảnh...',
    rows: 8,
    required: true,
    default: ''
  },
  {
    name: 'ThuTu',
    label: 'Thứ tự',
    type: 'number',
    placeholder: '1',
    required: true,
    default: contents.value.length + 1
  }
])

// Computed
const filteredContents = computed(() => {
  if (activeFilter.value === 'all') return contents.value
  return contents.value.filter(c => c.type === activeFilter.value)
})

const typeIconClass = computed(() => {
  const classes = {
    video: 'bg-blue-100 text-blue-600',
    text: 'bg-green-100 text-green-600',
    quiz: 'bg-purple-100 text-purple-600',
    assignment: 'bg-orange-100 text-orange-600'
  }
  return classes[lesson.value.type] || 'bg-blue-100 text-blue-600'
})

// Fetch lesson info
const fetchLesson = async () => {
  try {
    const response = await api.get(`/lessons/${lessonId.value}`)
    if (response.data.success) {
      const data = response.data.data
      lesson.value = {
        title: data.title,
        description: data.description,
        type: data.type || 'video',
        order: data.order,
        duration: data.duration || 0,
        status: data.status || 1
      }
    }
  } catch (error) {
    console.error('Lỗi khi tải thông tin bài học:', error)
  }
}

// Fetch contents
const fetchContents = async () => {
  isLoading.value = true
  try {
    const response = await api.get(`/lessons/${lessonId.value}/content`)
    console.log('API Response:', response) // Debug
    if (response.success) {
      contents.value = response.contents.map(item => ({
        id: item.id,
        type: item.type,
        title: item.title || '',
        content: item.content,
        order: item.order
      }))
      console.log('Contents loaded:', contents.value) // Debug
    }
  } catch (error) {
    console.error('Lỗi khi tải nội dung:', error)
  } finally {
    isLoading.value = false
  }
}

// Actions
const openAddModal = () => {
  isEditing.value = false
  editingContent.value = {
    ThuTu: contents.value.length + 1
  }
  showContentModal.value = true
}

const editContent = (content) => {
  isEditing.value = true
  editingContent.value = {
    id: content.id,
    LoaiNoiDung: content.type,
    TieuDe: content.title || '',
    NoiDung: content.content || '',
    ThuTu: content.order
  }
  showContentModal.value = true
}

const previewContent = (content) => {
  previewData.value = content
  showPreview.value = true
}

const confirmDeleteContent = (content) => {
  deletingContent.value = content
  showDeleteConfirm.value = true
}

// Handle form submit
const handleContentSubmit = async (formData) => {
  isSubmitting.value = true
  try {
    const apiData = {
      LoaiNoiDung: formData.LoaiNoiDung,
      TieuDe: formData.TieuDe || null,
      NoiDung: formData.NoiDung || null,
      ThuTu: formData.ThuTu
    }

    console.log('Submitting data:', apiData) // Debug

    if (isEditing.value) {
      await api.put(`/lesson-contents/${editingContent.value.id}`, apiData)
      console.log('Đã cập nhật nội dung:', apiData)
    } else {
      await api.post(`/lessons/${lessonId.value}/contents`, apiData)
      console.log('Đã thêm nội dung:', apiData)
    }

    showContentModal.value = false
    fetchContents()
  } catch (error) {
    console.error('Lỗi khi lưu nội dung:', error)
    alert('Không thể lưu nội dung. Vui lòng thử lại.')
  } finally {
    isSubmitting.value = false
  }
}

// Handle delete
const handleDelete = async () => {
  if (!deletingContent.value) return
  
  try {
    await api.delete(`/lesson-contents/${deletingContent.value.id}`)
    console.log('Đã xóa nội dung:', deletingContent.value)
    fetchContents()
  } catch (error) {
    console.error('Lỗi khi xóa nội dung:', error)
    alert('Không thể xóa nội dung. Vui lòng thử lại.')
  } finally {
    deletingContent.value = null
  }
}

// Handle drag end - update order
const onDragEnd = async () => {
  try {
    // Update order for all items
    const updates = contents.value.map((item, index) => ({
      id: item.id,
      order: index + 1
    }))
    
    await api.put(`/lessons/${lessonId.value}/contents/order`, { items: updates })
    console.log('Đã cập nhật thứ tự')
  } catch (error) {
    console.error('Lỗi khi cập nhật thứ tự:', error)
  }
}

// Get embed URL for videos
const getEmbedUrl = (url) => {
  if (!url) return ''
  
  // YouTube
  const youtubeMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/)
  if (youtubeMatch) {
    return `https://www.youtube.com/embed/${youtubeMatch[1]}`
  }
  
  // Vimeo
  const vimeoMatch = url.match(/vimeo\.com\/(\d+)/)
  if (vimeoMatch) {
    return `https://player.vimeo.com/video/${vimeoMatch[1]}`
  }
  
  return url
}

// Init
onMounted(() => {
  fetchLesson()
  fetchContents()
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
