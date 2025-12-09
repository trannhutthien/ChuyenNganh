<template>
  <div class="p-6">
    <!-- Back Button & Header -->
    <div class="mb-6">
      <BackButton to="/quan-ly/khoa-hoc" container-class="mb-4">
        Quay lại danh sách khóa học
      </BackButton>
      
      <!-- Course Info -->
      <LessonHeader
        :title="course.title"
        :thumbnail="course.thumbnail"
        :lesson-count="lessons.length"
        :total-duration="totalDuration"
      />
    </div>

    <!-- Actions Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <!-- Search -->
      <SearchInput
        v-model="searchQuery"
        placeholder="Tìm kiếm bài học..."
        size="lg"
        container-class="w-full sm:w-80"
        realtime
        :debounce="300"
      />

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
        Thêm bài học
      </BaseButton>
    </div>

    <!-- Lessons List -->
    <LessonList
      :lessons="filteredLessons"
      :loading="isLoading"
      @view="viewLesson"
      @edit="editLesson"
      @delete="deleteLesson"
    >
      <template #empty-action>
        <BaseButton 
          @click="openAddModal"
          variant="primary"
          size="sm"
          class="mt-4"
        >
          Thêm bài học đầu tiên
        </BaseButton>
      </template>
    </LessonList>

    <!-- Add/Edit Lesson Modal -->
    <FormAddModal
      v-model="showLessonModal"
      :title="isEditing ? 'Sửa bài học' : 'Thêm bài học mới'"
      :submit-text="isEditing ? 'Cập nhật' : 'Thêm bài học'"
      :fields="lessonFormFields"
      :initial-data="editingLesson"
      :loading="isSubmitting"
      size="lg"
      @submit="handleLessonSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import SearchInput from '../../components/ui/SearchInput.vue'
import BaseButton from '../../components/ui/BaseButton.vue'
import BackButton from '../../components/ui/BackButton.vue'
import LessonHeader from '../../components/admin/CourseLesson/LessonHeader.vue'
import LessonList from '../../components/admin/CourseLesson/LessonList.vue'
import FormAddModal from '../../components/modal/FormAddModal.vue'
import { courseService } from '../../services/courseService'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()

// Course ID from route
const courseId = computed(() => route.params.id)

// Lesson Form Fields Config
const lessonFormFields = computed(() => [
  {
    name: 'TieuDe',
    label: 'Tiêu đề bài học',
    type: 'text',
    placeholder: 'VD: Giới thiệu về HTML',
    required: true,
    default: ''
  },
  {
    name: 'LoaiBaiHoc',
    label: 'Loại bài học',
    inputType: 'select',
    required: true,
    default: 'video',
    options: [
      { value: 'video', label: 'Video bài giảng' },
      { value: 'text', label: 'Bài viết/Văn bản' },
      { value: 'quiz', label: 'Bài kiểm tra' },
      { value: 'assignment', label: 'Bài tập' }
    ]
  },
  {
    name: 'MoTa',
    label: 'Mô tả ngắn',
    inputType: 'textarea',
    placeholder: 'Mô tả nội dung bài học...',
    rows: 3,
    default: ''
  },
  {
    name: 'VideoUrl',
    label: 'URL Video',
    type: 'text',
    placeholder: 'https://youtube.com/watch?v=...',
    default: '',
    showIf: (form) => form.LoaiBaiHoc === 'video'
  },
  {
    name: 'NoiDung',
    label: 'Nội dung bài học',
    inputType: 'textarea',
    placeholder: 'Nhập nội dung bài học...',
    rows: 6,
    default: '',
    showIf: (form) => form.LoaiBaiHoc === 'text'
  },
  {
    name: 'ThuTu',
    label: 'Thứ tự',
    type: 'number',
    placeholder: '1',
    required: true,
    default: lessons.value.length + 1
  },
  {
    name: 'ThoiLuong',
    label: 'Thời lượng (phút)',
    type: 'number',
    placeholder: '30',
    default: 0
  },
  {
    name: 'TrangThai',
    label: 'Trạng thái',
    inputType: 'select',
    required: true,
    default: 1,
    options: [
      { value: 1, label: 'Hoạt động' },
      { value: 0, label: 'Nháp' },
      { value: -1, label: 'Ẩn' }
    ]
  }
])

// Course info
const course = ref({
  title: '',
  thumbnail: '',
  description: ''
})

// Lessons data
const lessons = ref([])
const isLoading = ref(false)
const searchQuery = ref('')

// Modal states
const showLessonModal = ref(false)
const isEditing = ref(false)
const isSubmitting = ref(false)
const editingLesson = ref({})



// Computed
const totalDuration = computed(() => {
  return lessons.value.reduce((sum, lesson) => sum + (lesson.duration || 0), 0)
})

const filteredLessons = computed(() => {
  if (!searchQuery.value) return lessons.value
  const query = searchQuery.value.toLowerCase()
  return lessons.value.filter(lesson => 
    lesson.title.toLowerCase().includes(query)
  )
})

// Fetch course info
const fetchCourse = async () => {
  try {
    const response = await courseService.getById(courseId.value)
    if (response.success) {
      course.value = {
        title: response.data.title,
        thumbnail: response.data.thumbnail,
        description: response.data.description
      }
    }
  } catch (error) {
    console.error('Lỗi khi tải thông tin khóa học:', error)
  }
}

// Fetch lessons
const fetchLessons = async () => {
  isLoading.value = true
  try {
    const response = await courseService.getLessons(courseId.value)
    if (response.success) {
      lessons.value = response.data.map(lesson => ({
        id: lesson.id,
        title: lesson.title,
        description: lesson.description,
        content: lesson.content,
        type: lesson.type || 'video',
        order: lesson.order,
        duration: lesson.duration || 0,
        videoUrl: lesson.videoUrl,
        status: lesson.status || 1
      }))
    }
  } catch (error) {
    console.error('Lỗi khi tải bài học:', error)
  } finally {
    isLoading.value = false
  }
}

// Actions
const openAddModal = () => {
  isEditing.value = false
  editingLesson.value = {
    order: lessons.value.length + 1
  }
  showLessonModal.value = true
}

const viewLesson = (lesson) => {
  // Điều hướng đến trang nội dung bài học
  router.push(`/quan-ly/khoa-hoc/${courseId.value}/bai-hoc/${lesson.id}/noi-dung`)
}

const editLesson = (lesson) => {
  isEditing.value = true
  // Map dữ liệu từ API sang form fields (tiếng Việt)
  editingLesson.value = {
    id: lesson.id,
    TieuDe: lesson.title,
    MoTa: lesson.description || '',
    NoiDung: lesson.content || '',
    LoaiBaiHoc: lesson.type || 'video',
    ThuTu: lesson.order || 1,
    ThoiLuong: lesson.duration || 0,
    VideoUrl: lesson.videoUrl || '',
    TrangThai: lesson.status || 1
  }
  showLessonModal.value = true
}

const deleteLesson = async (lesson) => {
  try {
    await api.delete(`/lessons/${lesson.id}`)
    console.log('Đã xóa bài học:', lesson)
    fetchLessons()
  } catch (error) {
    console.error('Lỗi khi xóa bài học:', error)
    alert('Không thể xóa bài học. Vui lòng thử lại.')
  }
}

const handleLessonSubmit = async (formData) => {
  isSubmitting.value = true
  try {
    const apiData = {
      KhoaHocId: courseId.value,
      TieuDe: formData.TieuDe,
      MoTa: formData.MoTa,
      NoiDung: formData.NoiDung,
      LoaiBaiHoc: formData.LoaiBaiHoc,
      ThuTu: formData.ThuTu,
      ThoiLuong: formData.ThoiLuong,
      VideoUrl: formData.VideoUrl,
      TrangThai: formData.TrangThai
    }

    if (isEditing.value) {
      await api.put(`/lessons/${editingLesson.value.id}`, apiData)
      console.log('Đã cập nhật bài học:', apiData)
    } else {
      await api.post('/lessons', apiData)
      console.log('Đã thêm bài học:', apiData)
    }

    showLessonModal.value = false
    fetchLessons()
  } catch (error) {
    console.error('Lỗi khi lưu bài học:', error)
    alert('Không thể lưu bài học. Vui lòng thử lại.')
  } finally {
    isSubmitting.value = false
  }
}

// Init
onMounted(() => {
  fetchCourse()
  fetchLessons()
})
</script>
