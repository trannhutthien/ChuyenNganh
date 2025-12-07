<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Quiz Header -->
    <QuizHeader v-if="quiz" :quiz="quiz">
      <template #controls v-if="quizStore.hasStarted && !quizStore.hasSubmitted">
        <div class="flex items-center gap-4">
          <QuizTimer 
            :timeLeft="timer.timeLeft.value"
            :warningThreshold="300"
            size="lg"
          />
          <QuizProgress 
            :answeredCount="answer.answeredCount.value"
            :totalQuestions="quizStore.totalQuestions"
            size="lg"
            showStats
          />
        </div>
      </template>
    </QuizHeader>

    <!-- Loading State -->
    <div v-if="quizStore.isLoading" class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="mt-4 text-gray-600">Đang tải quiz...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="quizStore.error" class="max-w-2xl mx-auto mt-8 p-6">
      <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
          <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h3 class="text-red-800 font-semibold">Có lỗi xảy ra</h3>
            <p class="text-red-700 mt-1">{{ quizStore.error }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Screen -->
    <!-- File: QuizPage.vue - Dòng 45-70 -->
    <!-- Thêm @require-login để xử lý khi chưa đăng nhập -->
    <QuizStartScreen
      v-else-if="!quizStore.hasStarted"
      :title="quiz?.title"
      :subtitle="quiz?.description"
      :loading="quizStore.isLoading"
      @start="handleStartQuiz"
      @require-login="handleRequireLogin"
    >
      <template #instructions>
        <li>Bạn có <strong>{{ quiz?.duration_minutes }} phút</strong> để hoàn thành {{ quiz?.total_questions }} câu hỏi</li>
        <li>Điểm đạt: <strong>{{ quiz?.passing_score }}%</strong></li>
        <li>Bài kiểm tra sẽ tự động nộp khi hết giờ</li>
        <li>Bạn có thể đánh dấu câu hỏi để xem lại sau</li>
        <li>Câu trả lời được tự động lưu mỗi 30 giây</li>
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
    <div v-else-if="quizStore.isInProgress" class="container mx-auto px-4 py-6">
      <div class="flex gap-6">
        <!-- Question Navigator Sidebar -->
        <aside class="w-64 flex-shrink-0">
          <div class="sticky top-6 space-y-4">
            <QuestionNavigator
              :questions="quizStore.questions"
              :currentIndex="navigation.currentIndex.value"
              :answers="answer.userAnswers.value"
              :markedQuestions="answer.markedQuestions.value"
              @navigate="navigation.goToQuestion"
            />
            
            <!-- Submit Button (Always visible) -->
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
            :questionNumber="navigation.currentQuestionNumber.value"
            :totalQuestions="quizStore.totalQuestions"
            :isMarked="answer.isMarked(currentQuestion.id)"
            @toggle-mark="answer.toggleMark(currentQuestion.id)"
          >
            <template #answer>
              <!-- Multiple Choice -->
              <MultipleChoice
                v-if="currentQuestion.type === 'multiple_choice'"
                :question="currentQuestion"
                v-model="answer.userAnswers.value[currentQuestion.id]"
              />

              <!-- True/False -->
              <TrueFalse
                v-else-if="currentQuestion.type === 'true_false'"
                :question="currentQuestion"
                v-model="answer.userAnswers.value[currentQuestion.id]"
              />

              <!-- Fill in the Blank -->
              <FillBlank
                v-else-if="currentQuestion.type === 'fill_blank'"
                v-model="answer.userAnswers.value[currentQuestion.id]"
                :maxLength="500"
              />

              <!-- Matching -->
              <Matching
                v-else-if="currentQuestion.type === 'matching'"
                :question="currentQuestion"
                v-model="answer.userAnswers.value[currentQuestion.id]"
              />

              <!-- Essay -->
              <EssayAnswer
                v-else-if="currentQuestion.type === 'essay' || currentQuestion.type === 'code'"
                v-model="answer.userAnswers.value[currentQuestion.id]"
                :type="currentQuestion.type"
              />
            </template>

            <template #footer>
              <div class="flex items-center justify-between">
                <BaseButton
                  variant="secondary"
                  size="md"
                  :disabled="!navigation.canGoPrevious.value"
                  @click="handlePreviousQuestion"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                  Câu trước
                </BaseButton>

                <div class="flex items-center gap-3">
                  <span v-if="autoSave.isSaving.value" class="text-sm text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang lưu...
                  </span>
                  <span v-else-if="autoSave.lastSavedAt.value" class="text-sm text-green-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Đã lưu
                  </span>

                  <BaseButton
                    v-if="navigation.isLast.value"
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
                    :disabled="!navigation.canGoNext.value"
                    @click="handleNextQuestion"
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
    <div v-else-if="quizStore.hasSubmitted && quizStore.quizResult" class="container mx-auto px-4 py-8">
      <QuizResult
        :result="quizStore.quizResult"
        :passingScore="Number(quizStore.quizResult?.passing_score) || 5"
      >
        <template #details>
          <ResultDetailList
            :details="quizStore.quizResult.details || []"
            showFilters
          />
        </template>

        <template #actions>
          <BaseButton
            v-if="quiz?.attempts_left > 0"
            variant="primary"
            size="lg"
            @click="handleRetry"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Làm lại (Còn {{ quiz.attempts_left }} lần)
          </BaseButton>

          <BaseButton
            variant="secondary"
            size="lg"
            @click="handleBackToCourse"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Quay lại khóa học
          </BaseButton>

          <BaseButton
            v-if="quizStore.quizResult?.passed"
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
      :loading="submit.isSubmitting.value"
      @confirm="handleSubmitQuiz"
      @cancel="showSubmitConfirm = false"
    >
      <div class="space-y-3">
        <p class="text-gray-700">
          Bạn đã trả lời <strong class="text-indigo-600">{{ answer.answeredCount.value }}/{{ quizStore.totalQuestions }}</strong> câu hỏi.
        </p>
        <p v-if="answer.unansweredCount.value > 0" class="text-amber-600 flex items-start gap-2">
          <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <span>Còn {{ answer.unansweredCount.value }} câu chưa trả lời!</span>
        </p>
        <p class="text-gray-600">Bạn có chắc chắn muốn nộp bài không?</p>
      </div>
    </ConfirmModal>

    <!-- Login Modal -->
    <!-- File: QuizPage.vue - Dòng 287-298 -->
    <!-- Modal đăng nhập hiển thị khi người dùng chưa đăng nhập mà nhấn "Bắt đầu làm bài" -->
    <!-- Props: isOpen (điều khiển hiển thị), loading (trạng thái đang xử lý), error (thông báo lỗi) -->
    <!-- Events: @close (đóng modal), @submit (gửi form đăng nhập) -->
    <LoginModal
      :isOpen="showLoginModal"
      :loading="loginLoading"
      :error="loginError"
      @close="showLoginModal = false"
      @submit="handleLoginSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

// Components
// File: QuizPage.vue - Dòng 295-305
// Import các component UI cho quiz
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
// Import LoginModal để hiển thị khi chưa đăng nhập
import LoginModal from '@/components/modal/LoginModal.vue'

// Question Types
import MultipleChoice from '@/components/quiz/ui/questions/MultipleChoice.vue'
import TrueFalse from '@/components/quiz/ui/questions/TrueFalse.vue'
import FillBlank from '@/components/quiz/ui/questions/FillBlank.vue'
import Matching from '@/components/quiz/ui/questions/Matching.vue'
import EssayAnswer from '@/components/quiz/ui/questions/EssayAnswer.vue'

// Composables
import { useQuizTimer } from '@/composables/quiz/useQuizTimer'
import { useQuizAnswer } from '@/composables/quiz/useQuizAnswer'
import { useQuizSubmit } from '@/composables/quiz/useQuizSubmit'
import { useAutoSave } from '@/composables/quiz/useAutoSave'
import { useQuizNavigation } from '@/composables/quiz/useQuizNavigation'

// Store
import { useQuizStore } from '@/stores/quiz'

const router = useRouter()
const route = useRoute()
const quizStore = useQuizStore()

// ========== LOCAL STATE ==========
// File: QuizPage.vue - Dòng 331-340
const showSubmitConfirm = ref(false)
// Biến điều khiển hiển thị LoginModal khi chưa đăng nhập
const showLoginModal = ref(false)
const quiz = computed(() => quizStore.quiz)
const currentQuestion = computed(() => {
  const questions = quizStore.questions
  const index = navigation.currentIndex.value
  return questions[index] || null
})

// ========== COMPOSABLES ==========
// Timer
const timer = useQuizTimer({
  durationMinutes: computed(() => quiz.value?.duration_minutes || 0),
  onTimeUp: () => handleSubmitQuiz(),
  autoSubmit: true
})

// Answer Management
const answer = useQuizAnswer()

// Submit & Grading
const submit = useQuizSubmit()

// Auto-save
const autoSave = useAutoSave({
  interval: 30000,
  onSave: async () => {
    if (quizStore.attemptId && quizStore.isInProgress) {
      await quizStore.saveProgress()
    }
  }
})

// Navigation
const navigation = useQuizNavigation(
  computed(() => quizStore.questions),
  quizStore.currentQuestionIndex
)

// ========== METHODS ==========
// File: QuizPage.vue - Dòng 374-390
// Xử lý khi người dùng chưa đăng nhập nhấn nút "Bắt đầu làm bài"
// Hàm này được gọi từ event @require-login của QuizStartScreen
const handleRequireLogin = () => {
  showLoginModal.value = true
}

// Xử lý sau khi đăng nhập thành công từ LoginModal
const handleLoginSuccess = () => {
  showLoginModal.value = false
  // Sau khi đăng nhập thành công, tự động bắt đầu làm bài
  handleStartQuiz()
}

// ========== NAVIGATION HANDLERS ==========
// Lưu câu trả lời trước khi chuyển câu
import { quizService } from '@/services/quizService'

const handleNextQuestion = async () => {
  // Lưu câu trả lời hiện tại lên server
  await saveCurrentAnswerToServer()
  // Chuyển sang câu tiếp
  navigation.goToNext()
}

const handlePreviousQuestion = async () => {
  // Lưu câu trả lời hiện tại lên server
  await saveCurrentAnswerToServer()
  // Quay lại câu trước
  navigation.goToPrevious()
}

const saveCurrentAnswerToServer = async () => {
  const question = currentQuestion.value
  if (!question || !quizStore.attemptId) return
  
  const userAnswer = answer.userAnswers.value[question.id]
  
  // Chỉ lưu nếu có câu trả lời
  if (!userAnswer || (Array.isArray(userAnswer) && userAnswer.length === 0)) return
  
  try {
    const payload = {
      cauHoiId: question.id,
      luaChonIds: Array.isArray(userAnswer) ? userAnswer : [userAnswer],
      thoiGianGiay: 0
    }
    
    console.log('Saving answer to server:', payload)
    await quizService.saveAnswer(quizStore.attemptId, payload)
    console.log('Answer saved successfully')
  } catch (err) {
    console.error('Error saving answer:', err)
  }
}

// File: QuizPage.vue - Dòng 400-440
// Xử lý khi người dùng submit form đăng nhập từ LoginModal
// Logic giống y chang Header.vue để đảm bảo nhất quán
import { authService } from '@/services/courseService.js'
const loginLoading = ref(false)
const loginError = ref('')

const handleLoginSubmit = async (data) => {
  loginLoading.value = true
  loginError.value = ''
  
  try {
    // Gọi API đăng nhập (giống Header.vue)
    const response = await authService.login(data.email, data.password)
    
    // Kiểm tra role (giống Header.vue)
    const hasValidRole = response.user.roles.includes('STUDENT') || response.user.roles.includes('ADMIN')
    if (!hasValidRole) {
      loginError.value = 'Tài khoản không có quyền truy cập'
      return
    }
    
    // Lưu token và user vào localStorage (giống Header.vue - dùng access_token)
    localStorage.setItem('access_token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
    
    // Emit event để các component khác biết đã đăng nhập (giống Header.vue)
    window.dispatchEvent(new Event('auth-changed'))
    
    // Đóng modal và bắt đầu làm bài
    handleLoginSuccess()
    
    console.log('Đăng nhập thành công:', response.user)
  } catch (error) {
    console.error('Login failed:', error)
    // Xử lý lỗi (giống Header.vue)
    if (error.message) {
      loginError.value = error.message
    } else {
      loginError.value = 'Đăng nhập thất bại'
    }
  } finally {
    loginLoading.value = false
  }
}

const handleStartQuiz = async () => {
  try {
    await quizStore.startQuiz()
    
    // Sync with composables
    answer.loadAnswers(quizStore.userAnswers)
    timer.setTime(quizStore.timeLeft)
    timer.start()
    autoSave.start()
  } catch (error) {
    console.error('Failed to start quiz:', error)
  }
}

const handleSubmitQuiz = async () => {
  showSubmitConfirm.value = false
  
  try {
    timer.stop()
    autoSave.stop()
    
    // Gọi store submitQuiz (có mock grading built-in)
    const result = await quizStore.submitQuiz()
    
    console.log('Quiz submitted successfully:', result)
  } catch (error) {
    console.error('Failed to submit quiz:', error)
  }
}

const handleRetry = () => {
  quizStore.resetForRetry()
  answer.clearAllAnswers()
  answer.clearAllMarks()
  timer.reset()
  submit.reset()
  showSubmitConfirm.value = false
}

const handleBackToCourse = () => {
  router.push(`/learn/${route.params.courseId || '1'}`)
}

const handleDownloadCertificate = () => {
  alert('Tính năng tải chứng chỉ sẽ được triển khai!')
}

// ========== LIFECYCLE ==========
onMounted(async () => {
  const quizId = route.params.quizId || route.params.id
  
  try {
    // Load quiz data
    await quizStore.loadQuiz(quizId)
    
    // Check if there's a resume attemptId in query params
    const resumeAttemptId = route.query.attemptId
    if (resumeAttemptId) {
      await quizStore.resumeQuiz(resumeAttemptId)
      answer.loadAnswers(quizStore.userAnswers)
      answer.loadMarkedQuestions(quizStore.markedQuestions)
      navigation.goToQuestion(quizStore.currentQuestionIndex)
      timer.setTime(quizStore.timeLeft)
      timer.start()
      autoSave.start()
    }
  } catch (error) {
    console.error('Failed to load quiz:', error)
  }
  
  // Prevent accidental page refresh
  window.addEventListener('beforeunload', handleBeforeUnload)
})

onUnmounted(() => {
  timer.stop()
  autoSave.stop()
  window.removeEventListener('beforeunload', handleBeforeUnload)
})

const handleBeforeUnload = (e) => {
  if (quizStore.isInProgress) {
    e.preventDefault()
    e.returnValue = 'Bạn đang làm bài kiểm tra. Bạn có chắc muốn rời khỏi trang?'
  }
}
</script>
