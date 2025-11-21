import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import quizService from '@/services/quizService'

/**
 * Quiz Store - Global state management cho quiz system
 * Sử dụng khi cần share state giữa nhiều components
 */
export const useQuizStore = defineStore('quiz', () => {
  // ===== State =====
  
  // Quiz data
  const quiz = ref(null)
  const questions = ref([])
  const currentQuestionIndex = ref(0)
  
  // Attempt data
  const attemptId = ref(null)
  const quizStarted = ref(false)
  const quizSubmitted = ref(false)
  
  // User answers & progress
  const userAnswers = ref({})
  const markedQuestions = ref([])
  
  // Timer
  const timeLeft = ref(0)
  const timerRunning = ref(false)
  
  // Result
  const quizResult = ref(null)
  
  // Loading & error states
  const isLoading = ref(false)
  const isSaving = ref(false)
  const isSubmitting = ref(false)
  const error = ref(null)
  
  // Auto-save
  const lastSavedAt = ref(null)
  const saveCount = ref(0)

  // ===== Computed =====
  
  const quizId = computed(() => quiz.value?.id || null)
  const quizTitle = computed(() => quiz.value?.title || '')
  const totalQuestions = computed(() => questions.value?.length || 0)
  const currentQuestion = computed(() => questions.value[currentQuestionIndex.value] || null)
  const currentQuestionNumber = computed(() => currentQuestionIndex.value + 1)
  
  const answeredCount = computed(() => {
    return Object.keys(userAnswers.value).filter(key => {
      const answer = userAnswers.value[key]
      if (Array.isArray(answer)) return answer.length > 0
      if (typeof answer === 'object') return Object.keys(answer).length > 0
      return answer !== null && answer !== undefined && answer !== ''
    }).length
  })
  
  const completionPercentage = computed(() => {
    if (totalQuestions.value === 0) return 0
    return Math.round((answeredCount.value / totalQuestions.value) * 100)
  })
  
  const hasStarted = computed(() => quizStarted.value)
  const hasSubmitted = computed(() => quizSubmitted.value)
  const isInProgress = computed(() => quizStarted.value && !quizSubmitted.value)
  
  const canSubmit = computed(() => {
    return isInProgress.value && answeredCount.value > 0 && !isSubmitting.value
  })
  
  const passed = computed(() => {
    if (!quizResult.value) return false
    return quizResult.value.passed || false
  })

  // ===== Actions =====
  
  /**
   * Load quiz data từ API
   */
  async function loadQuiz(id) {
    isLoading.value = true
    error.value = null
    
    try {
      const data = await quizService.fetchQuiz(id)
      quiz.value = data.quiz || data
      questions.value = data.questions || []
      return data
    } catch (err) {
      console.error('API Error, using mock data:', err)
      
      // MOCK DATA cho development
      const mockData = {
        quiz: {
          id: parseInt(id),
          title: 'Kiểm tra Vue.js 3 Composition API',
          description: 'Bài kiểm tra kiến thức về Vue.js 3 và Composition API',
          duration_minutes: 30,
          total_questions: 5,
          passing_score: 70,
          attempts_left: 2,
          difficulty: 'medium'
        },
        questions: [
          {
            id: 1,
            type: 'multiple_choice',
            text: 'Những hàm nào sau đây thuộc Composition API của Vue 3? (Chọn nhiều đáp án)',
            points: 3,
            options: [
              { id: 1, text: 'ref()' },
              { id: 2, text: 'reactive()' },
              { id: 3, text: 'data()' },
              { id: 4, text: 'computed()' },
              { id: 5, text: 'methods()' }
            ]
          },
          {
            id: 2,
            type: 'true_false',
            text: 'Vue.js 3 sử dụng Virtual DOM để tối ưu hiệu suất rendering?',
            points: 2,
            options: [
              { id: 6, text: 'Đúng' },
              { id: 7, text: 'Sai' }
            ]
          },
          {
            id: 3,
            type: 'fill_blank',
            text: 'Để tạo một biến reactive trong Composition API, ta sử dụng hàm ____',
            points: 2
          },
          {
            id: 4,
            type: 'matching',
            text: 'Ghép các lifecycle hooks với mô tả phù hợp',
            points: 3,
            leftItems: ['onMounted', 'onUpdated', 'onUnmounted'],
            rightItems: ['Component destroyed', 'Component mounted', 'Component updated']
          },
          {
            id: 5,
            type: 'essay',
            text: 'Giải thích sự khác biệt giữa ref() và reactive() trong Vue 3 Composition API',
            points: 5
          }
        ]
      }
      
      quiz.value = mockData.quiz
      questions.value = mockData.questions
      error.value = null // Clear error khi dùng mock data
      
      return mockData
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * Bắt đầu quiz
   */
  async function startQuiz() {
    if (!quizId.value) {
      throw new Error('Quiz ID không hợp lệ')
    }
    
    isLoading.value = true
    error.value = null
    
    try {
      const data = await quizService.startQuiz(quizId.value)
      attemptId.value = data.attempt_id || data.id
      quizStarted.value = true
      timeLeft.value = (quiz.value?.duration_minutes || 0) * 60
      
      // Initialize empty answers
      initializeAnswers()
      
      return data
    } catch (err) {
      console.error('API Error, using mock attempt:', err)
      
      // MOCK: Tạo attempt ID giả
      attemptId.value = Date.now()
      quizStarted.value = true
      timeLeft.value = (quiz.value?.duration_minutes || 0) * 60
      
      // Initialize empty answers
      initializeAnswers()
      
      error.value = null
      return { attempt_id: attemptId.value }
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * Khôi phục quiz đang làm dở
   */
  async function resumeQuiz(id) {
    isLoading.value = true
    error.value = null
    
    try {
      const data = await quizService.resumeQuiz(id)
      
      attemptId.value = id
      quiz.value = data.quiz
      questions.value = data.questions || []
      userAnswers.value = data.answers || {}
      markedQuestions.value = data.marked_questions || []
      currentQuestionIndex.value = data.current_question_index || 0
      timeLeft.value = data.time_left || 0
      quizStarted.value = true
      
      return data
    } catch (err) {
      error.value = err.message || 'Không thể khôi phục quiz'
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * Submit quiz
   */
  async function submitQuiz() {
    if (!attemptId.value) {
      throw new Error('Attempt ID không hợp lệ')
    }
    
    isSubmitting.value = true
    error.value = null
    
    try {
      const timeTaken = (quiz.value?.duration_minutes || 0) * 60 - timeLeft.value
      
      const result = await quizService.submitQuiz(
        attemptId.value,
        userAnswers.value,
        timeTaken
      )
      
      quizResult.value = result
      quizSubmitted.value = true
      timerRunning.value = false
      
      return result
    } catch (err) {
      console.error('API Error, using mock grading:', err)
      
      // MOCK: Calculate result locally
      const timeTaken = (quiz.value?.duration_minutes || 0) * 60 - timeLeft.value
      let correctCount = 0
      let totalPoints = 0
      let earnedPoints = 0
      const details = []
      
      questions.value.forEach(q => {
        totalPoints += q.points
        const userAnswer = userAnswers.value[q.id]
        let isCorrect = false
        let pointsEarned = 0
        let yourAnswer = ''
        let correctAnswer = ''
        
        if (q.type === 'multiple_choice') {
          const correctIds = [1, 2, 4] // Mock correct answers
          isCorrect = JSON.stringify(userAnswer?.sort()) === JSON.stringify(correctIds.sort())
          yourAnswer = q.options.filter(o => userAnswer?.includes(o.id)).map(o => o.text).join(', ') || 'Không có'
          correctAnswer = 'ref(), reactive(), computed()'
        } else if (q.type === 'true_false') {
          const correctId = 6 // "Đúng"
          isCorrect = userAnswer === correctId
          yourAnswer = q.options.find(o => o.id === userAnswer)?.text || 'Không có'
          correctAnswer = 'Đúng'
        } else if (q.type === 'fill_blank') {
          const validAnswers = ['ref', 'reactive']
          isCorrect = validAnswers.some(ans => userAnswer?.toLowerCase().includes(ans.toLowerCase()))
          yourAnswer = userAnswer || 'Không có'
          correctAnswer = 'ref hoặc reactive'
        } else if (q.type === 'essay') {
          isCorrect = null
          yourAnswer = userAnswer || 'Không có'
          correctAnswer = 'Chờ giảng viên chấm'
        }
        
        if (isCorrect === true) {
          correctCount++
          pointsEarned = q.points
          earnedPoints += pointsEarned
        }
        
        details.push({
          question_text: q.text,
          your_answer: yourAnswer,
          correct_answer: correctAnswer,
          is_correct: isCorrect,
          points_earned: pointsEarned,
          points_max: q.points,
          explanation: 'Giải thích chi tiết sẽ được hiển thị từ database'
        })
      })
      
      const score = Math.round((earnedPoints / totalPoints) * 100)
      
      const mockResult = {
        score,
        passed: score >= (quiz.value?.passing_score || 70),
        correct_answers: correctCount,
        total_questions: questions.value.length,
        time_taken: formatTime(timeTaken),
        details
      }
      
      quizResult.value = mockResult
      quizSubmitted.value = true
      timerRunning.value = false
      error.value = null
      
      return mockResult
    } finally {
      isSubmitting.value = false
    }
  }
  
  // Helper function for time formatting
  function formatTime(seconds) {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }
  
  /**
   * Lưu tiến trình
   */
  async function saveProgress() {
    if (!attemptId.value || !isInProgress.value) {
      return
    }
    
    isSaving.value = true
    
    try {
      await quizService.saveProgress(attemptId.value, {
        answers: userAnswers.value,
        markedQuestions: markedQuestions.value,
        currentQuestionIndex: currentQuestionIndex.value,
        timeLeft: timeLeft.value
      })
      
      lastSavedAt.value = new Date()
      saveCount.value++
      
    } catch (err) {
      console.error('Auto-save failed:', err)
      // Không throw error để không interrupt user experience
    } finally {
      isSaving.value = false
    }
  }
  
  /**
   * Load kết quả quiz
   */
  async function loadResult(id) {
    isLoading.value = true
    error.value = null
    
    try {
      const result = await quizService.getQuizResult(id)
      quizResult.value = result
      return result
    } catch (err) {
      error.value = err.message || 'Không thể tải kết quả'
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * Set answer cho question
   */
  function setAnswer(questionId, answer) {
    userAnswers.value[questionId] = answer
  }
  
  /**
   * Get answer của question
   */
  function getAnswer(questionId) {
    return userAnswers.value[questionId]
  }
  
  /**
   * Clear answer của question
   */
  function clearAnswer(questionId) {
    delete userAnswers.value[questionId]
  }
  
  /**
   * Toggle marked question
   */
  function toggleMark(questionId) {
    const index = markedQuestions.value.indexOf(questionId)
    if (index > -1) {
      markedQuestions.value.splice(index, 1)
    } else {
      markedQuestions.value.push(questionId)
    }
  }
  
  /**
   * Check if question is marked
   */
  function isMarked(questionId) {
    return markedQuestions.value.includes(questionId)
  }
  
  /**
   * Check if question is answered
   */
  function isAnswered(questionId) {
    const answer = userAnswers.value[questionId]
    if (Array.isArray(answer)) return answer.length > 0
    if (typeof answer === 'object') return Object.keys(answer).length > 0
    return answer !== null && answer !== undefined && answer !== ''
  }
  
  /**
   * Navigate to question
   */
  function goToQuestion(index) {
    if (index >= 0 && index < totalQuestions.value) {
      currentQuestionIndex.value = index
    }
  }
  
  /**
   * Next question
   */
  function nextQuestion() {
    if (currentQuestionIndex.value < totalQuestions.value - 1) {
      currentQuestionIndex.value++
    }
  }
  
  /**
   * Previous question
   */
  function previousQuestion() {
    if (currentQuestionIndex.value > 0) {
      currentQuestionIndex.value--
    }
  }
  
  /**
   * Initialize empty answers structure
   */
  function initializeAnswers() {
    const answers = {}
    questions.value.forEach(q => {
      if (q.type === 'multiple_choice') {
        answers[q.id] = []
      } else if (q.type === 'matching') {
        answers[q.id] = {}
      } else {
        answers[q.id] = null
      }
    })
    userAnswers.value = answers
  }
  
  /**
   * Start timer
   */
  function startTimer() {
    timerRunning.value = true
  }
  
  /**
   * Stop timer
   */
  function stopTimer() {
    timerRunning.value = false
  }
  
  /**
   * Update time left
   */
  function updateTimeLeft(seconds) {
    timeLeft.value = seconds
  }
  
  /**
   * Reset store to initial state
   */
  function reset() {
    quiz.value = null
    questions.value = []
    currentQuestionIndex.value = 0
    attemptId.value = null
    quizStarted.value = false
    quizSubmitted.value = false
    userAnswers.value = {}
    markedQuestions.value = []
    timeLeft.value = 0
    timerRunning.value = false
    quizResult.value = null
    isLoading.value = false
    isSaving.value = false
    isSubmitting.value = false
    error.value = null
    lastSavedAt.value = null
    saveCount.value = 0
  }
  
  /**
   * Reset for retry
   */
  function resetForRetry() {
    currentQuestionIndex.value = 0
    attemptId.value = null
    quizStarted.value = false
    quizSubmitted.value = false
    userAnswers.value = {}
    markedQuestions.value = []
    timeLeft.value = 0
    timerRunning.value = false
    quizResult.value = null
    error.value = null
    lastSavedAt.value = null
    saveCount.value = 0
  }

  return {
    // State
    quiz,
    questions,
    currentQuestionIndex,
    attemptId,
    quizStarted,
    quizSubmitted,
    userAnswers,
    markedQuestions,
    timeLeft,
    timerRunning,
    quizResult,
    isLoading,
    isSaving,
    isSubmitting,
    error,
    lastSavedAt,
    saveCount,
    
    // Computed
    quizId,
    quizTitle,
    totalQuestions,
    currentQuestion,
    currentQuestionNumber,
    answeredCount,
    completionPercentage,
    hasStarted,
    hasSubmitted,
    isInProgress,
    canSubmit,
    passed,
    
    // Actions
    loadQuiz,
    startQuiz,
    resumeQuiz,
    submitQuiz,
    saveProgress,
    loadResult,
    setAnswer,
    getAnswer,
    clearAnswer,
    toggleMark,
    isMarked,
    isAnswered,
    goToQuestion,
    nextQuestion,
    previousQuestion,
    initializeAnswers,
    startTimer,
    stopTimer,
    updateTimeLeft,
    reset,
    resetForRetry
  }
})

export default useQuizStore
