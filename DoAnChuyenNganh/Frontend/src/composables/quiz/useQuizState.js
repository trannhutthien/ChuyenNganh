import { ref, computed } from 'vue'
import api from '@/services/api'

/**
 * Composable for managing overall quiz state
 * Central state management for quiz lifecycle and metadata
 */
export function useQuizState() {
  // State
  const quiz = ref(null)
  const questions = ref([])
  const attemptId = ref(null)
  const quizStarted = ref(false)
  const quizSubmitted = ref(false)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const quizId = computed(() => quiz.value?.id)
  
  const quizTitle = computed(() => quiz.value?.title || '')
  
  const quizDescription = computed(() => quiz.value?.description || '')
  
  const duration = computed(() => quiz.value?.duration_minutes || 0)
  
  const totalQuestions = computed(() => questions.value.length)
  
  const passingScore = computed(() => quiz.value?.passing_score || 70)
  
  const attemptsLeft = computed(() => quiz.value?.attempts_left || 0)
  
  const difficulty = computed(() => quiz.value?.difficulty || 'medium')
  
  const hasStarted = computed(() => quizStarted.value)
  
  const hasSubmitted = computed(() => quizSubmitted.value)
  
  const isInProgress = computed(() => quizStarted.value && !quizSubmitted.value)
  
  const canRetry = computed(() => attemptsLeft.value > 0)

  /**
   * Load quiz data from API
   * @param {number} quizId - Quiz ID
   */
  const loadQuiz = async (quizId) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get(`/api/quiz/${quizId}`)
      quiz.value = response.data.quiz
      questions.value = response.data.questions || []
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Không thể tải bài kiểm tra'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Start quiz and create attempt
   * @param {number} quizId - Quiz ID
   */
  const startQuiz = async (quizId) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.post(`/api/quiz/${quizId}/start`)
      attemptId.value = response.data.attempt_id
      quizStarted.value = true
      
      // If questions not loaded yet, load them
      if (response.data.questions) {
        questions.value = response.data.questions
      }
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Không thể bắt đầu bài kiểm tra'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Resume quiz from saved attempt
   * @param {number} attemptId - Attempt ID
   */
  const resumeQuiz = async (attemptIdToResume) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get(`/api/quiz/attempt/${attemptIdToResume}/resume`)
      
      quiz.value = response.data.quiz
      questions.value = response.data.questions
      attemptId.value = attemptIdToResume
      quizStarted.value = true
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Không thể tiếp tục bài kiểm tra'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Mark quiz as submitted
   */
  const markAsSubmitted = () => {
    quizSubmitted.value = true
  }

  /**
   * Reset quiz state for retry
   */
  const resetForRetry = () => {
    attemptId.value = null
    quizStarted.value = false
    quizSubmitted.value = false
    error.value = null
    
    // Decrement attempts left
    if (quiz.value && quiz.value.attempts_left > 0) {
      quiz.value.attempts_left--
    }
  }

  /**
   * Reset all state
   */
  const reset = () => {
    quiz.value = null
    questions.value = []
    attemptId.value = null
    quizStarted.value = false
    quizSubmitted.value = false
    isLoading.value = false
    error.value = null
  }

  /**
   * Get quiz metadata
   */
  const getMetadata = () => {
    return {
      id: quizId.value,
      title: quizTitle.value,
      description: quizDescription.value,
      duration: duration.value,
      totalQuestions: totalQuestions.value,
      passingScore: passingScore.value,
      attemptsLeft: attemptsLeft.value,
      difficulty: difficulty.value
    }
  }

  /**
   * Get quiz status
   */
  const getStatus = () => {
    return {
      attemptId: attemptId.value,
      started: quizStarted.value,
      submitted: quizSubmitted.value,
      inProgress: isInProgress.value,
      canRetry: canRetry.value,
      loading: isLoading.value,
      error: error.value
    }
  }

  /**
   * Set quiz data (for mock/testing)
   */
  const setQuizData = (quizData, questionsData) => {
    quiz.value = quizData
    if (questionsData) {
      questions.value = questionsData
    }
  }

  /**
   * Initialize answers structure based on question types
   * - single: array (chứa 1 ID đáp án đúng)
   * - multiple: array (chứa nhiều ID đáp án đúng)
   * - true_false: single value
   * - matching: object
   */
  const initializeAnswers = () => {
    const initialAnswers = {}
    
    questions.value.forEach(question => {
      if (question.type === 'single' || question.type === 'multiple') {
        // Cả single và multiple đều dùng array để lưu đáp án
        initialAnswers[question.id] = []
      } else if (question.type === 'matching') {
        initialAnswers[question.id] = {}
      } else {
        initialAnswers[question.id] = ''
      }
    })
    
    return initialAnswers
  }

  /**
   * Get question by ID
   */
  const getQuestionById = (questionId) => {
    return questions.value.find(q => q.id === questionId)
  }

  /**
   * Get question by index
   */
  const getQuestionByIndex = (index) => {
    return questions.value[index]
  }

  /**
   * Check if quiz is valid
   */
  const isValid = computed(() => {
    return quiz.value !== null && 
           questions.value.length > 0 &&
           quiz.value.duration_minutes > 0
  })

  return {
    // State
    quiz,
    questions,
    attemptId,
    quizStarted,
    quizSubmitted,
    isLoading,
    error,

    // Computed
    quizId,
    quizTitle,
    quizDescription,
    duration,
    totalQuestions,
    passingScore,
    attemptsLeft,
    difficulty,
    hasStarted,
    hasSubmitted,
    isInProgress,
    canRetry,
    isValid,

    // Methods
    loadQuiz,
    startQuiz,
    resumeQuiz,
    markAsSubmitted,
    resetForRetry,
    reset,
    getMetadata,
    getStatus,
    setQuizData,
    initializeAnswers,
    getQuestionById,
    getQuestionByIndex
  }
}
