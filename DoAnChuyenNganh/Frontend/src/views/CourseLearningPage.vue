<template>
  <!-- Container có chiều cao trừ đi header (65px) -->
  <div class="flex bg-white" style="height: calc(100vh - 65px);">
    
    <!-- Loading State -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
        <p class="text-gray-500">Đang tải bài học...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex-1 flex items-center justify-center">
      <div class="text-center">
        <p class="text-red-500 mb-4">{{ error }}</p>
        <button 
          @click="loadLessons(route.params.courseId)"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
        >
          Thử lại
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="lessons.length === 0" class="flex-1 flex items-center justify-center">
      <div class="text-center">
        <p class="text-gray-500">Khóa học này chưa có bài học nào.</p>
      </div>
    </div>

    <!-- Main Content -->
    <template v-else>
      <!-- Cột BÊN TRÁI - Nội dung bài học -->
      <LessonContent
        :lesson="currentLesson"
        :course-title="courseTitle"
        :current-index="currentLessonIndex"
        :total-lessons="lessons.length"
        :progress="progress"
        @next="nextLesson"
        @previous="previousLesson"
        @toggle-complete="markAsCompleted"
        @go-to-quiz="goToQuiz"
      />

      <!-- Cột BÊN PHẢI - Danh sách bài học -->
      <LessonSidebar
        :lessons="lessons"
        :current-index="currentLessonIndex"
        @select-lesson="selectLesson"
      />
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import LessonContent from '../components/course/LessonContent.vue'
import LessonSidebar from '../components/course/LessonSidebar.vue'
import { courseService } from '../services/courseService.js'

const router = useRouter()
const route = useRoute()

// ========== STATE CHÍNH ==========
const courseTitle = ref('')
const currentLessonIndex = ref(0)
const loading = ref(true)
const error = ref(null)

// ========== DATA - Load từ API ==========
const lessons = ref([])

// ========== COMPUTED ==========
const currentLesson = computed(() => {
  if (lessons.value.length === 0) return null
  return lessons.value[currentLessonIndex.value]
})

const completedCount = computed(() => {
  return lessons.value.filter(lesson => lesson.completed).length
})

const progress = computed(() => {
  if (lessons.value.length === 0) return 0
  return Math.round((completedCount.value / lessons.value.length) * 100)
})

// ========== METHODS - Navigation & Actions ==========
const selectLesson = (index) => {
  currentLessonIndex.value = index
}

const nextLesson = () => {
  if (currentLessonIndex.value < lessons.value.length - 1) {
    currentLessonIndex.value++
  }
}

const previousLesson = () => {
  if (currentLessonIndex.value > 0) {
    currentLessonIndex.value--
  }
}

const markAsCompleted = () => {
  if (lessons.value[currentLessonIndex.value]) {
    lessons.value[currentLessonIndex.value].completed = !lessons.value[currentLessonIndex.value].completed
  }
}

const goToQuiz = () => {
  const quizId = 1 // TODO: Lấy từ course data
  router.push(`/quiz/${quizId}`)
}

// ========== API CALLS ==========
const loadLessons = async (courseId) => {
  loading.value = true
  error.value = null
  try {
    const response = await courseService.getLessons(courseId)
    // response đã được unwrap bởi interceptor, nên response chính là object { success, course, data }
    console.log('API Response:', response)
    
    // Set course title
    if (response.course) {
      courseTitle.value = response.course.title
    }
    
    // Set lessons - response.data là mảng bài học
    lessons.value = response.data || []
    console.log('Loaded lessons:', lessons.value)
  } catch (err) {
    console.error('Error loading lessons:', err)
    error.value = 'Không thể tải bài học. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

// Load lessons khi component mount
onMounted(() => {
  const courseId = route.params.courseId
  console.log('CourseLearningPage mounted, courseId:', courseId)
  if (courseId) {
    loadLessons(courseId)
  } else {
    error.value = 'Không tìm thấy khóa học'
    loading.value = false
  }
})
</script>

<style scoped>
/* Minimal styles - Components handle their own styling */
</style>
