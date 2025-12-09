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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import SearchInput from '../../components/ui/SearchInput.vue'
import BaseButton from '../../components/ui/BaseButton.vue'
import TablePagination from '../../components/ui/TablePagination.vue'
import CourseStatsCards from '../../components/admin/statsCards/CourseStatsCards.vue'
import CourseTable from '../../components/admin/CourseTable.vue'
import { courseService } from '../../services/courseService'

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

// Fetch courses from API
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
      
      // Update stats
      updateStats()
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

// Update stats based on courses
const updateStats = () => {
  stats.value = {
    totalCourses: totalCourses.value,
    activeCourses: courses.value.filter(c => c.status === 'active').length,
    pendingCourses: courses.value.filter(c => c.status === 'pending' || c.status === 'draft').length,
    totalStudents: courses.value.reduce((sum, c) => sum + c.students, 0)
  }
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
  console.log('Mở modal thêm khóa học')
  // TODO: Implement add modal
}

const viewCourse = (course) => {
  console.log('Xem khóa học:', course)
  // TODO: Navigate to course detail
}

const editCourse = (course) => {
  console.log('Sửa khóa học:', course)
  // TODO: Open edit modal
}

const deleteCourse = async (course) => {
  if (confirm(`Bạn có chắc muốn xóa khóa học "${course.title}"?`)) {
    try {
      await courseService.delete(course.id)
      console.log('Đã xóa khóa học:', course)
      // Reload danh sách
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
  fetchCourses()
})
</script>
