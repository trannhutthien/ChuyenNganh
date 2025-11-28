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
      <div class="relative w-full sm:w-80">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Tìm kiếm khóa học..." 
          class="w-full h-10 pl-10 pr-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        />
      </div>

      <!-- Add Button -->
      <button 
        @click="openAddModal"
        class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Thêm khóa học
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.totalCourses }}</p>
            <p class="text-sm text-gray-600">Tổng khóa học</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.activeCourses }}</p>
            <p class="text-sm text-gray-600">Đang hoạt động</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.pendingCourses }}</p>
            <p class="text-sm text-gray-600">Chờ duyệt</p>
          </div>
        </div>
      </div>

      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.totalStudents }}</p>
            <p class="text-sm text-gray-600">Học viên</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Courses Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left py-4 px-6 font-semibold text-gray-700">Khóa học</th>
              <th class="text-left py-4 px-6 font-semibold text-gray-700">Danh mục</th>
              <th class="text-left py-4 px-6 font-semibold text-gray-700">Giảng viên</th>
              <th class="text-center py-4 px-6 font-semibold text-gray-700">Học viên</th>
              <th class="text-center py-4 px-6 font-semibold text-gray-700">Bài học</th>
              <th class="text-center py-4 px-6 font-semibold text-gray-700">Trạng thái</th>
              <th class="text-center py-4 px-6 font-semibold text-gray-700">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr 
              v-for="course in filteredCourses" 
              :key="course.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <!-- Course Info -->
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <img 
                    :src="course.thumbnail" 
                    :alt="course.title"
                    class="w-16 h-12 rounded-lg object-cover"
                  />
                  <div>
                    <p class="font-medium text-gray-800 line-clamp-1">{{ course.title }}</p>
                    <p class="text-sm text-gray-500">{{ course.createdAt }}</p>
                  </div>
                </div>
              </td>

              <!-- Category -->
              <td class="py-4 px-6">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                  {{ course.category }}
                </span>
              </td>

              <!-- Instructor -->
              <td class="py-4 px-6 text-gray-700">{{ course.instructor }}</td>

              <!-- Students -->
              <td class="py-4 px-6 text-center text-gray-700">{{ course.students }}</td>

              <!-- Lessons -->
              <td class="py-4 px-6 text-center text-gray-700">{{ course.lessons }}</td>

              <!-- Status -->
              <td class="py-4 px-6 text-center">
                <span 
                  class="px-3 py-1 rounded-full text-sm font-medium"
                  :class="getStatusClass(course.status)"
                >
                  {{ getStatusText(course.status) }}
                </span>
              </td>

              <!-- Actions -->
              <td class="py-4 px-6">
                <div class="flex items-center justify-center gap-2">
                  <button 
                    @click="viewCourse(course)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="Xem chi tiết"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                  </button>
                  <button 
                    @click="editCourse(course)"
                    class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                    title="Chỉnh sửa"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                  </button>
                  <button 
                    @click="deleteCourse(course)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Xóa"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="filteredCourses.length === 0">
              <td colspan="7" class="py-12 text-center">
                <div class="flex flex-col items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                  </svg>
                  <p class="text-gray-500 text-lg">Không tìm thấy khóa học nào</p>
                  <p class="text-gray-400 text-sm mt-1">Thử thay đổi từ khóa tìm kiếm</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between p-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
          Hiển thị {{ (currentPage - 1) * pageSize + 1 }} - {{ Math.min(currentPage * pageSize, totalCourses) }} trên {{ totalCourses }} khóa học
        </p>
        <div class="flex items-center gap-2">
          <button 
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Trước
          </button>
          <span class="px-3 py-1 bg-primary text-white rounded-lg">{{ currentPage }}</span>
          <button 
            @click="nextPage"
            :disabled="currentPage >= totalPages"
            class="px-3 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Sau
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Search
const searchQuery = ref('')

// Stats
const stats = ref({
  totalCourses: 25,
  activeCourses: 18,
  pendingCourses: 5,
  totalStudents: 1250
})

// Pagination
const currentPage = ref(1)
const pageSize = 10
const totalCourses = ref(25)
const totalPages = computed(() => Math.ceil(totalCourses.value / pageSize))

// Sample Courses Data
const courses = ref([
  {
    id: 1,
    title: 'JavaScript Căn bản đến Nâng cao',
    thumbnail: 'https://files.fullstack.edu.vn/f8-prod/courses/15/62f13d2424a47.png',
    category: 'Frontend',
    instructor: 'Nguyễn Văn A',
    students: 450,
    lessons: 120,
    status: 'active',
    createdAt: '15/10/2025'
  },
  {
    id: 2,
    title: 'Vue.js 3 - Từ Zero đến Hero',
    thumbnail: 'https://files.fullstack.edu.vn/f8-prod/courses/13/13.png',
    category: 'Frontend',
    instructor: 'Trần Văn B',
    students: 320,
    lessons: 85,
    status: 'active',
    createdAt: '20/10/2025'
  },
  {
    id: 3,
    title: 'Laravel 10 - Xây dựng API RESTful',
    thumbnail: 'https://files.fullstack.edu.vn/f8-prod/courses/6/6.png',
    category: 'Backend',
    instructor: 'Lê Thị C',
    students: 280,
    lessons: 95,
    status: 'active',
    createdAt: '25/10/2025'
  },
  {
    id: 4,
    title: 'React Native - Lập trình Mobile',
    thumbnail: 'https://files.fullstack.edu.vn/f8-prod/courses/13/13.png',
    category: 'Mobile',
    instructor: 'Phạm Văn D',
    students: 150,
    lessons: 60,
    status: 'pending',
    createdAt: '01/11/2025'
  },
  {
    id: 5,
    title: 'Node.js & Express - Backend Development',
    thumbnail: 'https://files.fullstack.edu.vn/f8-prod/courses/6/6.png',
    category: 'Backend',
    instructor: 'Hoàng Văn E',
    students: 0,
    lessons: 45,
    status: 'draft',
    createdAt: '10/11/2025'
  }
])

// Filtered courses
const filteredCourses = computed(() => {
  if (!searchQuery.value) return courses.value
  
  const query = searchQuery.value.toLowerCase()
  return courses.value.filter(course => 
    course.title.toLowerCase().includes(query) ||
    course.category.toLowerCase().includes(query) ||
    course.instructor.toLowerCase().includes(query)
  )
})

// Status helpers
const getStatusClass = (status) => {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-700'
    case 'pending':
      return 'bg-yellow-100 text-yellow-700'
    case 'draft':
      return 'bg-gray-100 text-gray-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'active':
      return 'Hoạt động'
    case 'pending':
      return 'Chờ duyệt'
    case 'draft':
      return 'Nháp'
    default:
      return status
  }
}

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

const deleteCourse = (course) => {
  if (confirm(`Bạn có chắc muốn xóa khóa học "${course.title}"?`)) {
    console.log('Xóa khóa học:', course)
    // TODO: Call API delete
  }
}

// Pagination
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
