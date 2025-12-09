<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Quiz Header -->
    <QuizHeader v-if="examInfo" :quiz="formattedQuiz">
      <template #controls v-if="hasStarted && !hasSubmitted">
        <div class="flex items-center gap-4">
          <QuizTimer 
            :timeLeft="timeLeft"
            :warningThreshold="300"
            size="lg"
          />
          <QuizProgress 
            :answeredCount="answeredCount"
            :totalQuestions="totalQuestions"
            size="lg"
            showStats
          />
        </div>
      </template>
    </QuizHeader>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="mt-4 text-gray-600">Đang tải bài kiểm tra...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-2xl mx-auto mt-8 p-6">
      <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
          <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h3 class="text-red-800 font-semibold">Không thể làm bài</h3>
            <p class="text-red-700 mt-1">{{ error }}</p>
            <button 
              @click="goBackToCourse"
              class="mt-3 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200"
            >
              Quay lại khóa học
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Screen -->
    <QuizStartScreen
      v-else-if="!hasStarted"
      :title="examInfo?.tieuDe"
      :subtitle="examInfo?.moTa || 'Bài kiểm tra cuối khóa'"
      :loading="isStarting"
      @start="handleStartExam"
    >
      <template #instructions>
        <li>Bạn có <strong>{{ examInfo?.thoiGianLamBai || 30 }} phút</strong> để hoàn thành {{ examInfo?.soCauHoi || 20 }} câu hỏi</li>
        <li>Điểm đạt: <strong>{{ examInfo?.diemDat || 5 }}/10</strong></li>
        <li>Số lần làm tối đa: <strong>{{ examInfo?.soLanLamToiDa || 'Không giới hạn' }}</strong></li>
        <li v-if="examInfo?.soLanDaLam > 0">Bạn đã làm: <strong>{{ examInfo.soLanDaLam }} lần</strong></li>
        <li v-if="examInfo?.diemCaoNhat">Điểm cao nhất: <strong>{{ examInfo.diemCaoNhat }}/10</strong></li>
        <li>Bài kiểm tra sẽ tự động nộp khi hết giờ</li>
      </template>
      
      <template #warning>
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
          <div class="flex items-start">
            <svg class="w-5 h-5 text-amber-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-amber-800 text-sm">Không được refresh hoặc thoát tab khi đang làm bài</p>
          </div>
        </div>
      </template>
    </QuizStartScreen>

    <!-- Quiz Content -->
    <div v-else-if="isInProgress" class="container mx-auto px-4 py-6">
      <div class="flex gap-6">
        <!-- Question Navigator Sidebar -->
        <aside class="w-64 flex-shrink-0">
          <div class="sticky top-6 space-y-4">
            <QuestionNavigator
              :questions="questions"
              :currentIndex="currentIndex"
              :answers="userAnswers"
              :markedQuestions="markedQuestions"
              @navigate="goToQuestion"
            />
            
            <!-- Submit Button -->
            <BaseButton
              variant="success"
              size="lg"
              class="w-full shadow-lg hover:shadow-xl transition-all"
              @click="showSubmitConfirm = true"
            >
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Nộp bài
            </BaseButton>
          </div>
        </aside>

        <!-- Main Question Area -->
        <main class="flex-1 min-w-0">
          <QuestionCard
            v-if="currentQuestion"
            :question="currentQuestion"
            :questionNumber="currentIndex + 1"
            :totalQuestions="totalQuestions"
            :isMarked="markedQuestions.includes(currentQuestion.id)"
            @toggle-mark="toggleMark(currentQuestion.id)"
          >
            <template #answer>
              <!-- Single Choice (Radio - chọn 1 đáp án) -->
              <SingleChoice
                v-if="currentQuestion.loai === 'single'"
                :question="currentQuestion"
                v-model="userAnswers[currentQuestion.id]"
                @update:modelValue="saveAnswer(currentQuestion.id)"
              />

              <!-- Multiple Choice (Checkbox - chọn nhiều đáp án) -->
              <MultipleChoice
                v-else-if="currentQuestion.loai === 'multiple'"
                :question="currentQuestion"
                v-model="userAnswers[currentQuestion.id]"
                @update:modelValue="saveAnswer(currentQuestion.id)"
              />

              <!-- True/False -->
              <TrueFalse
                v-else-if="currentQuestion.loai === 'true_false'"
                :question="currentQuestion"
                v-model="userAnswers[currentQuestion.id]"
                @update:modelValue="saveAnswer(currentQuestion.id)"
              />
            </template>

            <template #footer>
              <div class="flex items-center justify-between">
                <BaseButton
                  variant="secondary"
                  size="md"
                  :disabled="currentIndex === 0"
                  @click="goToPrevious"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                  Câu trước
                </BaseButton>

                <div class="flex items-center gap-3">
                  <span v-if="isSaving" class="text-sm text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang lưu...
                  </span>

                  <BaseButton
                    v-if="currentIndex === totalQuestions - 1"
                    variant="primary"
                    size="md"
                    @click="showSubmitConfirm = true"
                  >
                    Nộp bài
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </BaseButton>
                  
                  <BaseButton
                    v-else
                    variant="primary"
                    size="md"
                    :disabled="currentIndex === totalQuestions - 1"
                    @click="goToNext"
                  >
                    Câu tiếp
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </BaseButton>
                </div>
              </div>
            </template>
          </QuestionCard>
        </main>
      </div>
    </div>

    <!-- Result Screen -->
    <div v-else-if="hasSubmitted && examResult" class="container mx-auto px-4 py-8">
      <QuizResult
        :result="formattedResult"
        :passingScore="(examInfo?.diemDat || 5) * 10"
      >
        <template #details>
          <ResultDetailList
            v-if="examResult.chiTiet"
            :details="examResult.chiTiet"
            showFilters
          />
        </template>

        <template #actions>
          <!-- Button Làm lại - Hiển thị nếu chưa đạt hoặc còn lượt làm bài -->
          <BaseButton
            v-if="!examResult?.dat || examInfo?.conLuotLamBai"
            variant="primary"
            size="lg"
            @click="handleRetry"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Làm lại
          </BaseButton>

          <BaseButton
            variant="secondary"
            size="lg"
            @click="goBackToCourse"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại khóa học
          </BaseButton>

          <BaseButton
            v-if="examResult?.dat"
            variant="success"
            size="lg"
            @click="handleDownloadCertificate"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            Tải chứng chỉ
          </BaseButton>
        </template>
      </QuizResult>
    </div>

    <!-- Submit Confirmation Modal -->
    <ConfirmModal
      :show="showSubmitConfirm"
      title="Xác nhận nộp bài"
      variant="primary"
      :loading="isSubmitting"
      @confirm="handleSubmitExam"
      @cancel="showSubmitConfirm = false"
    >
      <div class="space-y-3">
        <p class="text-gray-700">
          Bạn đã trả lời <strong class="text-indigo-600">{{ answeredCount }}/{{ totalQuestions }}</strong> câu hỏi.
        </p>
        <p v-if="unansweredCount > 0" class="text-amber-600 flex items-start gap-2">
          <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <span>Còn {{ unansweredCount }} câu chưa trả lời!</span>
        </p>
        <p class="text-gray-600">Bạn có chắc chắn muốn nộp bài không?</p>
      </div>
    </ConfirmModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

// Components
import QuizHeader from '@/components/quiz/ui/QuizHeader.vue'
import QuizTimer from '@/components/quiz/ui/QuizTimer.vue'
import QuizProgress from '@/components/quiz/ui/QuizProgress.vue'
import QuizStartScreen from '@/components/quiz/ui/QuizStartScreen.vue'
import QuestionNavigator from '@/components/quiz/ui/QuestionNavigator.vue'
import QuestionCard from '@/components/quiz/ui/QuestionCard.vue'
import ConfirmModal from '@/components/ui/ConfirmModal.vue'
import QuizResult from '@/components/quiz/ui/result/QuizResult.vue'
import ResultDetailList from '@/components/quiz/ui/result/ResultDetailList.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

// Question Types
import SingleChoice from '@/components/quiz/ui/questions/SingleChoice.vue'
import MultipleChoice from '@/components/quiz/ui/questions/MultipleChoice.vue'
import TrueFalse from '@/components/quiz/ui/questions/TrueFalse.vue'

// Service
import quizService from '@/services/quizService'

const router = useRouter()
const route = useRoute()

// ========== STATE ==========
const isLoading = ref(true)
const isStarting = ref(false)
const isSubmitting = ref(false)
const isSaving = ref(false)
const error = ref(null)
const showSubmitConfirm = ref(false)

// Exam data
const examInfo = ref(null)
const attemptId = ref(null)
const questions = ref([])
const userAnswers = ref({})
const markedQuestions = ref([])
const currentIndex = ref(0)
const timeLeft = ref(0)
const examResult = ref(null)

// Exam state
const hasStarted = ref(false)
const hasSubmitted = ref(false)

// Timer interval
let timerInterval = null

// ========== COMPUTED ==========
const totalQuestions = computed(() => questions.value.length)
const currentQuestion = computed(() => questions.value[currentIndex.value] || null)

const answeredCount = computed(() => {
  return Object.keys(userAnswers.value).filter(key => {
    const answer = userAnswers.value[key]
    if (Array.isArray(answer)) return answer.length > 0
    return answer !== null && answer !== undefined && answer !== ''
  }).length
})

const unansweredCount = computed(() => totalQuestions.value - answeredCount.value)

const isInProgress = computed(() => hasStarted.value && !hasSubmitted.value)

const formattedQuiz = computed(() => ({
  title: examInfo.value?.tieuDe || 'Bài kiểm tra cuối khóa',
  description: examInfo.value?.moTa,
  duration_minutes: examInfo.value?.thoiGianLamBai || 30,
  total_questions: examInfo.value?.soCauHoi || 20
}))

const formattedResult = computed(() => {
  if (!examResult.value) return null
  return {
    score: examResult.value.diemSo * 10, // Convert to percentage
    passed: examResult.value.dat,
    correctAnswers: examResult.value.soCauDung,
    totalQuestions: examResult.value.tongSoCau,
    timeTaken: examResult.value.thoiGianLam
  }
})

// ========== METHODS ==========

// Load thông tin bài kiểm tra
const loadExamInfo = async () => {
  const courseId = route.params.courseId
  
  try {
    const response = await quizService.getFinalExam(courseId)
    
    if (!response.canLamBai && response.lyDoKhongDuocLam) {
      error.value = response.lyDoKhongDuocLam
      return
    }
    
    examInfo.value = response.baiKiemTra
  } catch (err) {
    console.error('Error loading exam info:', err)
    error.value = err.response?.data?.message || 'Không thể tải thông tin bài kiểm tra'
  } finally {
    isLoading.value = false
  }
}

// Bắt đầu làm bài
const handleStartExam = async () => {
  if (!examInfo.value) return
  
  isStarting.value = true
  
  try {
    const response = await quizService.startFinalExam(examInfo.value.id)
    
    attemptId.value = response.lanLamBai.id
    questions.value = response.cauHois || []
    timeLeft.value = response.lanLamBai.thoiGianConLai || (examInfo.value.thoiGianLamBai * 60)
    
    // Khởi tạo answers
    questions.value.forEach(q => {
      userAnswers.value[q.id] = null
    })
    
    hasStarted.value = true
    
    // Bắt đầu đếm ngược
    startTimer()
  } catch (err) {
    console.error('Error starting exam:', err)
    error.value = err.response?.data?.message || 'Không thể bắt đầu bài kiểm tra'
  } finally {
    isStarting.value = false
  }
}

// Timer
const startTimer = () => {
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      // Hết giờ - tự động nộp bài
      handleSubmitExam()
    }
  }, 1000)
}

const stopTimer = () => {
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
}

// Navigation
const goToQuestion = (index) => {
  currentIndex.value = index
}

const goToPrevious = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  }
}

const goToNext = () => {
  if (currentIndex.value < totalQuestions.value - 1) {
    currentIndex.value++
  }
}

// Mark question
const toggleMark = (questionId) => {
  const index = markedQuestions.value.indexOf(questionId)
  if (index === -1) {
    markedQuestions.value.push(questionId)
  } else {
    markedQuestions.value.splice(index, 1)
  }
}

// Save answer to server
const saveAnswer = async (questionId) => {
  if (!attemptId.value) return
  
  isSaving.value = true
  
  try {
    const answer = userAnswers.value[questionId]
    await quizService.saveFinalExamAnswer(attemptId.value, {
      cauHoiId: questionId,
      luaChonIds: Array.isArray(answer) ? answer : (answer ? [answer] : [])
    })
  } catch (err) {
    console.error('Error saving answer:', err)
  } finally {
    isSaving.value = false
  }
}

// Nộp bài
const handleSubmitExam = async () => {
  showSubmitConfirm.value = false
  isSubmitting.value = true
  stopTimer()
  
  try {
    // LƯU CÂU TRẢ LỜI CUỐI CÙNG TRƯỚC KHI NỘP BÀI
    const currentQ = questions.value[currentIndex.value]
    if (currentQ && userAnswers.value[currentQ.id]) {
      await saveAnswer(currentQ.id)
    }
    
    const response = await quizService.submitFinalExam(attemptId.value)
    examResult.value = response.ketQua
    hasSubmitted.value = true
    
    // Load kết quả chi tiết
    const detailResponse = await quizService.getFinalExamResult(attemptId.value)
    examResult.value = {
      ...examResult.value,
      chiTiet: detailResponse.chiTiet
    }
  } catch (err) {
    console.error('Error submitting exam:', err)
    error.value = err.response?.data?.message || 'Không thể nộp bài'
  } finally {
    isSubmitting.value = false
  }
}

// Làm lại
const handleRetry = () => {
  hasStarted.value = false
  hasSubmitted.value = false
  examResult.value = null
  questions.value = []
  userAnswers.value = {}
  markedQuestions.value = []
  currentIndex.value = 0
  timeLeft.value = 0
  attemptId.value = null
  
  // Reload exam info
  loadExamInfo()
}

// Quay lại khóa học
const goBackToCourse = () => {
  // Lấy courseId từ nhiều nguồn khác nhau
  const courseId = route.params.courseId 
    || examInfo.value?.khoaHocId
    || examResult.value?.khoaHocId
    || route.query.courseId
    
  if (courseId) {
    router.push(`/learn/${courseId}`)
  } else {
    // Fallback: Quay về trang khóa học chung
    router.push('/courses')
  }
}

// Download certificate
const handleDownloadCertificate = () => {
  alert('Tính năng tải chứng chỉ sẽ được triển khai!')
}

// Prevent page leave
const handleBeforeUnload = (e) => {
  if (isInProgress.value) {
    e.preventDefault()
    e.returnValue = 'Bạn đang làm bài kiểm tra. Bạn có chắc muốn rời khỏi trang?'
  }
}

// ========== LIFECYCLE ==========
onMounted(() => {
  loadExamInfo()
  window.addEventListener('beforeunload', handleBeforeUnload)
})

onUnmounted(() => {
  stopTimer()
  window.removeEventListener('beforeunload', handleBeforeUnload)
})
</script>
