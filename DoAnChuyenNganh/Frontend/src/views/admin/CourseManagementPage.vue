<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Quản lý khóa học</h1>
      <p class="text-gray-600 mt-1">Quản lý tất cả các khóa học trong hệ thống</p>
    </div>

    <!-- Actions Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <!-- Search -->
      <SearchInput
        v-model="searchQuery"
        @search="handleSearch"
        placeholder="Tìm kiếm khóa học..."
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
        Thêm khóa học
      </BaseButton>
    </div>

    <!-- Stats Cards -->
    <CourseStatsCards :stats="stats" />

    <!-- Courses Table -->
    <CourseTable
      :columns="tableColumns"
      :data="filteredCourses"
      row-key="id"
      empty-text="Không tìm thấy khóa học nào"
      empty-sub-text="Thử thay đổi từ khóa tìm kiếm"
      @view="viewCourse"
      @edit="editCourse"
      @delete="deleteCourse"
    >
      <!-- Pagination slot -->
      <template #pagination>
        <TablePagination
          :current-page="currentPage"
          :total-pages="totalPages"
          :total="totalCourses"
          :page-size="pageSize"
          item-name="khóa học"
          @prev="prevPage"
          @next="nextPage"
        />
      </template>
    </CourseTable>

    <!-- Form Modal: Thêm/Sửa khóa học -->
    <FormAddModal
      v-model="showFormModal"
      :title="isEditing ? 'Sửa khóa học' : 'Thêm khóa học mới'"
      :submit-text="isEditing ? 'Cập nhật' : 'Thêm khóa học'"
      :fields="courseFormFields"
      :initial-data="editingCourse"
      :loading="isSubmitting"
      size="lg"
      @submit="handleFormSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import SearchInput from '../../components/ui/SearchInput.vue'
import BaseButton from '../../components/ui/BaseButton.vue'
import TablePagination from '../../components/ui/TablePagination.vue'
import FormAddModal from '../../components/modal/FormAddModal.vue'
import CourseStatsCards from '../../components/admin/statsCards/CourseStatsCards.vue'
import CourseTable from '../../components/admin/CourseTable.vue'
import { courseService } from '../../services/courseService'

const router = useRouter()

// Course Form Fields Config
const courseFormFields = [
  {
    name: 'TieuDe',
    label: 'Tiêu đề khóa học',
    type: 'text',
    placeholder: 'VD: Lập Trình PHP & MySQL',
    required: true,
    default: ''
  },
  {
    name: 'HinhAnhUrl',
    label: 'Hình ảnh (URL)',
    type: 'text',
    placeholder: '/images/phpmysql.jpg',
    required: true,
    default: ''
  },
  {
    name: 'TomTat',
    label: 'Tóm tắt khóa học',
    inputType: 'textarea',
    placeholder: 'Mô tả ngắn về nội dung khóa học...',
    rows: 4,
    required: true,
    default: ''
  },
  {
    name: 'CapDo',
    label: 'Cấp độ',
    inputType: 'select',
    required: true,
    default: 1,
    options: [
      { value: 1, label: '1 - Cơ bản' },
      { value: 2, label: '2 - Trung bình' },
      { value: 3, label: '3 - Nâng cao' }
    ]
  },
  {
    name: 'Tags',
    label: 'Tags (ngăn cách bằng dấu phẩy)',
    type: 'text',
    placeholder: 'php,mysql,backend',
    default: ''
  },
  {
    name: 'DieuKienTienQuyet',
    label: 'Điều kiện tiên quyết',
    type: 'text',
    placeholder: 'VD: PHP cơ bản',
    default: ''
  },
  {
    name: 'GiaTien',
    label: 'Giá tiền (VNĐ)',
    type: 'number',
    placeholder: '399000',
    required: true,
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
      { value: 0, label: 'Chờ duyệt' },
      { value: -1, label: 'Nháp' }
    ]
  }
]

// Table columns config
const tableColumns = [
  { key: 'title', label: 'Khóa học', align: 'left' },
  { key: 'level', label: 'Cấp độ', align: 'left' },
  { key: 'price', label: 'Giá', align: 'center' },
  { key: 'students', label: 'Học viên', align: 'center' },
  { key: 'lessons', label: 'Bài học', align: 'center' },
  { key: 'status', label: 'Trạng thái', align: 'center' },
  { key: 'actions', label: 'Thao tác', align: 'center' }
]

// Form Modal State
const showFormModal = ref(false)
const isEditing = ref(false)
const isSubmitting = ref(false)
const editingCourse = ref({})

// Loading state
const isLoading = ref(false)

// Search
const searchQuery = ref('')

const handleSearch = (query) => {
  console.log('Tìm kiếm:', query)
  currentPage.value = 1
  fetchCourses()
}

// Stats
const stats = ref({
  totalCourses: 0,
  activeCourses: 0,
  pendingCourses: 0,
  totalStudents: 0
})

// Pagination
const currentPage = ref(1)
const pageSize = ref(10)
const totalCourses = ref(0)
const totalPages = ref(1)

// Courses Data
const courses = ref([])
const allCoursesForStats = ref([]) // Lưu tất cả khóa học để tính thống kê

// Fetch stats từ tất cả khóa học (không phân trang)
const fetchStats = async () => {
  try {
    // Lấy tất cả khóa học để tính thống kê
    const response = await courseService.getAllAdmin({
      per_page: 9999 // Lấy tất cả
    })
    
    if (response.success) {
      allCoursesForStats.value = response.data
      
      // Tính stats từ tất cả khóa học
      // Status: 1 = Hoạt động, 2 = Đã xuất bản (cả 2 đều hiển thị trang chủ)
      //         0 = Chờ duyệt, -1 = Nháp
      const allCourses = response.data
      stats.value = {
        totalCourses: response.pagination?.total || allCourses.length,
        // Status 1 hoặc 2 = Đang hoạt động (hiển thị trang chủ)
        activeCourses: allCourses.filter(c => 
          c.status === 1 || c.status === '1' || 
          c.status === 2 || c.status === '2'
        ).length,
        // Status 0 = Chờ duyệt
        pendingCourses: allCourses.filter(c => 
          c.status === 0 || c.status === '0'
        ).length,
        // Tổng học viên
        totalStudents: allCourses.reduce((sum, c) => sum + (c.students || 0), 0)
      }
    }
  } catch (error) {
    console.error('Lỗi khi tải thống kê:', error)
  }
}

// Fetch courses from API (có phân trang)
const fetchCourses = async () => {
  isLoading.value = true
  try {
    // Gọi API lấy tất cả khóa học (admin) - dùng /courses/all
    const response = await courseService.getAllAdmin({
      per_page: pageSize.value,
      page: currentPage.value,
      search: searchQuery.value || undefined
    })
    
    console.log('API Response:', response) // Debug
    
    if (response.success) {
      // Format dữ liệu từ API
      courses.value = response.data.map(course => ({
        id: course.id,
        title: course.title || 'Không có tiêu đề',
        thumbnail: course.thumbnail || 'https://via.placeholder.com/150',
        level: String(course.level || '1'), // Chuyển thành string
        price: course.price || 0,
        students: course.students || 0,
        lessons: course.lessons || 0,
        status: String(course.status || '1'), // Chuyển thành string
        createdAt: formatDate(course.createdAt)
      }))
      
      console.log('Mapped courses:', courses.value) // Debug
      
      // Update pagination
      if (response.pagination) {
        totalCourses.value = response.pagination.total
        totalPages.value = response.pagination.last_page
        currentPage.value = response.pagination.current_page
      }
    }
  } catch (error) {
    console.error('Lỗi khi tải khóa học:', error)
  } finally {
    isLoading.value = false
  }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN')
}

// Filtered courses (local search)
const filteredCourses = computed(() => {
  if (!searchQuery.value) return courses.value
  
  const query = searchQuery.value.toLowerCase()
  return courses.value.filter(course => 
    course.title.toLowerCase().includes(query) ||
    course.level?.toLowerCase().includes(query)
  )
})

// Actions
const openAddModal = () => {
  isEditing.value = false
  editingCourse.value = {}
  showFormModal.value = true
}

const viewCourse = (course) => {
  // Navigate to course lessons page
  router.push(`/quan-ly/khoa-hoc/${course.id}/bai-hoc`)
}

const editCourse = (course) => {
  isEditing.value = true
  // Map course data to form fields
  editingCourse.value = {
    id: course.id,
    TieuDe: course.title,
    HinhAnhUrl: course.thumbnail,
    TomTat: course.description || '',
    CapDo: Number(course.level) || 1,
    Tags: course.tags || '',
    DieuKienTienQuyet: course.prerequisites || '',
    GiaTien: course.price || 0,
    TrangThai: Number(course.status) || 1
  }
  showFormModal.value = true
}

const handleFormSubmit = async (formData) => {
  isSubmitting.value = true
  try {
    // Map form data to API format
    const apiData = {
      title: formData.TieuDe,
      thumbnail: formData.HinhAnhUrl,
      description: formData.TomTat,
      level: formData.CapDo,
      tags: formData.Tags,
      prerequisites: formData.DieuKienTienQuyet,
      price: formData.GiaTien,
      status: formData.TrangThai
    }

    if (isEditing.value) {
      // Update existing course
      await courseService.update(editingCourse.value.id, apiData)
      console.log('Đã cập nhật khóa học:', apiData)
    } else {
      // Create new course
      await courseService.create(apiData)
      console.log('Đã thêm khóa học:', apiData)
    }

    // Close modal and refresh data
    showFormModal.value = false
    fetchStats()
    fetchCourses()
  } catch (error) {
    console.error('Lỗi khi lưu khóa học:', error)
    alert('Không thể lưu khóa học. Vui lòng thử lại.')
  } finally {
    isSubmitting.value = false
  }
}

const deleteCourse = async (course) => {
  if (confirm(`Bạn có chắc muốn xóa khóa học "${course.title}"?`)) {
    try {
      await courseService.delete(course.id)
      console.log('Đã xóa khóa học:', course)
      // Reload thống kê và danh sách
      fetchStats()
      fetchCourses()
    } catch (error) {
      console.error('Lỗi khi xóa khóa học:', error)
      alert('Không thể xóa khóa học. Vui lòng thử lại.')
    }
  }
}

// Pagination
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchCourses()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchCourses()
  }
}

// Fetch data on mount
onMounted(() => {
  fetchStats()   // Lấy thống kê từ tất cả khóa học
  fetchCourses() // Lấy khóa học theo trang
})
</script>
