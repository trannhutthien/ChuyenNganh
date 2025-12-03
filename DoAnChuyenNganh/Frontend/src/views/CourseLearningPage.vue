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
      <div class="flex-1 flex flex-col overflow-hidden bg-white">
        <!-- Header của bài học -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-lg font-bold text-gray-800">{{ courseTitle }}</h1>
              <p class="text-sm text-gray-500">{{ currentLesson?.title }}</p>
            </div>
            <div class="flex items-center gap-4">
              <div class="text-sm text-gray-500">
                Bài {{ currentLessonIndex + 1 }}/{{ lessons.length }}
              </div>
              <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-primary transition-all duration-300"
                  :style="{ width: progress + '%' }"
                ></div>
              </div>
              <span class="text-sm text-primary font-semibold">{{ progress }}%</span>
            </div>
          </div>
        </div>

        <!-- Nội dung bài học (scrollable) -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
          <div class="max-w-4xl mx-auto p-8">
            <!-- Loading content -->
            <div v-if="loadingContent" class="flex items-center justify-center py-12">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
              <span class="ml-3 text-gray-500">Đang tải nội dung...</span>
            </div>

            <!-- Tiêu đề bài học -->
            <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
              <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ currentLesson?.title }}</h2>
              <p v-if="currentLesson?.description" class="text-gray-600">{{ currentLesson?.description }}</p>
            </div>

            <!-- Nội dung chi tiết từ database -->
            <div v-if="lessonContents.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
              <LessonContentItem
                v-for="item in lessonContents"
                :key="item.id"
                :item="item"
              />
            </div>

            <!-- Placeholder nếu chưa có nội dung -->
            <div v-else-if="!loadingContent" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center text-gray-500">
              <p>Bài học này chưa có nội dung chi tiết.</p>
            </div>
          </div>
        </div>

        <!-- Footer - Navigation buttons -->
        <div class="bg-white border-t border-gray-200 px-6 py-4 shadow-sm">
          <div class="flex items-center justify-between">
            <button 
              @click="previousLesson"
              :disabled="currentLessonIndex === 0"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
              </svg>
              Bài trước
            </button>

            <button 
              @click="markAsCompleted"
              class="px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary/10 flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
              </svg>
              {{ currentLesson?.completed ? 'Đã hoàn thành' : 'Hoàn thành' }}
            </button>

            <button 
              @click="nextLesson"
              :disabled="currentLessonIndex === lessons.length - 1"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              Bài tiếp theo
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Cột BÊN PHẢI - Danh sách bài học -->
      <LessonSidebar
        :lessons="lessons"
        :current-index="currentLessonIndex"
        @select-lesson="selectLesson"
        @start-final-exam="goToFinalExam"
      />
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import LessonSidebar from '../components/course/LessonSidebar.vue'
import LessonContentItem from '../components/course/LessonContentItem.vue'
import { courseService } from '../services/courseService.js'
import quizService from '../services/quizService.js'

const router = useRouter()
const route = useRoute()

// ========== STATE CHÍNH ==========
const courseTitle = ref('')
const currentLessonIndex = ref(0)
const loading = ref(true)
const loadingContent = ref(false)
const error = ref(null)

// ========== DATA - Load từ API ==========
const lessons = ref([])
const lessonContents = ref([])  // Nội dung chi tiết của bài học hiện tại
const finalExam = ref(null)     // Thông tin bài kiểm tra cuối khóa

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
const selectLesson = async (index) => {
  currentLessonIndex.value = index
  // Load nội dung bài học mới
  await loadLessonContent(lessons.value[index].id)
}

const nextLesson = async () => {
  if (currentLessonIndex.value < lessons.value.length - 1) {
    currentLessonIndex.value++
    await loadLessonContent(currentLesson.value.id)
  }
}

const previousLesson = async () => {
  if (currentLessonIndex.value > 0) {
    currentLessonIndex.value--
    await loadLessonContent(currentLesson.value.id)
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

// Đi đến trang bài kiểm tra cuối khóa
const goToFinalExam = () => {
  const quizId = 1 // TODO: Lấy quiz ID thực tế
  router.push(`/quiz/${quizId}`)
}

// ========== API CALLS ==========
const loadLessons = async (courseId) => {
  loading.value = true
  error.value = null
  try {
    const response = await courseService.getLessons(courseId)
    console.log('API Response:', response)
    
    // Set course title
    if (response.course) {
      courseTitle.value = response.course.title
    }
    
    // Set lessons
    lessons.value = response.data || []
    console.log('Loaded lessons:', lessons.value)

    // Load nội dung bài học đầu tiên
    if (lessons.value.length > 0) {
      await loadLessonContent(lessons.value[0].id)
    }

    // Load thông tin bài kiểm tra cuối khóa
    await loadFinalExam(courseId)
  } catch (err) {
    console.error('Error loading lessons:', err)
    error.value = 'Không thể tải bài học. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

// Load thông tin bài kiểm tra cuối khóa
const loadFinalExam = async (courseId) => {
  try {
    const response = await quizService.getFinalExam(courseId)
    console.log('Final exam response:', response)
    finalExam.value = response.baiKiemTra || null
  } catch (err) {
    console.log('No final exam found or error:', err)
    finalExam.value = null
  }
}

// Load nội dung chi tiết của bài học
const loadLessonContent = async (lessonId) => {
  loadingContent.value = true
  try {
    const response = await courseService.getLessonContent(lessonId)
    console.log('Lesson content response:', response)
    
    // Set nội dung bài học
    lessonContents.value = response.contents || []
    console.log('Loaded lesson contents:', lessonContents.value)
  } catch (err) {
    console.error('Error loading lesson content:', err)
    lessonContents.value = []
  } finally {
    loadingContent.value = false
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
