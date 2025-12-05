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
            ],
            correct_answers: [1, 2, 4], // ref(), reactive(), computed()
            explanation: 'ref(), reactive(), và computed() là các hàm cốt lõi của Composition API. data() và methods() là Options API.'
          },
          {
            id: 2,
            type: 'true_false',
            text: 'Vue.js 3 sử dụng Virtual DOM để tối ưu hiệu suất rendering?',
            points: 2,
            options: [
              { id: 6, text: 'Đúng' },
              { id: 7, text: 'Sai' }
            ],
            correct_answer: 6, // Đúng
            explanation: 'Vue 3 sử dụng Virtual DOM kết hợp với compiler-informed optimizations để tối ưu hiệu suất.'
          },
          {
            id: 3,
            type: 'fill_blank',
            text: 'Để tạo một biến reactive trong Composition API, ta sử dụng hàm ____',
            points: 2,
            correct_answer: 'ref',
            explanation: 'Có thể dùng ref() hoặc reactive(), nhưng ref() phổ biến hơn cho primitive values.'
          },
          {
            id: 4,
            type: 'matching',
            text: 'Ghép các lifecycle hooks với mô tả phù hợp',
            points: 3,
            leftItems: ['onMounted', 'onUpdated', 'onUnmounted'],
            rightItems: ['Component destroyed', 'Component mounted', 'Component updated'],
            correct_pairs: {
              'onMounted': 'Component mounted',
              'onUpdated': 'Component updated',
              'onUnmounted': 'Component destroyed'
            },
            explanation: 'onMounted chạy khi component được mount, onUpdated khi re-render, onUnmounted khi destroy.'
          },
          {
            id: 5,
            type: 'essay',
            text: 'Giải thích sự khác biệt giữa ref() và reactive() trong Vue 3 Composition API',
            points: 5,
            correct_answer: null, // Cần chấm thủ công
            explanation: 'ref() dùng cho primitive values và cần .value, reactive() dùng cho objects và không cần .value.'
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
   * Bắt đầu quiz - Gọi API và nhận câu hỏi từ database
   * File: quiz.js - Dòng 180-220
   */
  async function startQuiz() {
    if (!quizId.value) {
      throw new Error('Quiz ID không hợp lệ')
    }
    
    isLoading.value = true
    error.value = null
    
    try {
      // Gọi API bắt đầu làm bài - Backend trả về câu hỏi từ database
      const response = await quizService.startQuiz(quizId.value)
      
      // Lấy thông tin lần làm bài
      attemptId.value = response.data?.id || response.data?.LanLamBaiId
      
      // Lấy câu hỏi từ response (đã được format từ backend)
      if (response.cauHois && response.cauHois.length > 0) {
        questions.value = response.cauHois
        console.log('Loaded questions from database:', questions.value.length)
      }
      
      // Cập nhật thời gian từ thietLap
      const thietLap = response.data?.thietLap || quiz.value?.thietLap || {}
      const thoiGianPhut = thietLap.thoiGianLamBai || quiz.value?.duration_minutes || 30
      timeLeft.value = thoiGianPhut * 60
      
      quizStarted.value = true
      
      // Initialize empty answers
      initializeAnswers()
      
      return response
    } catch (err) {
      console.error('API Error:', err)
      error.value = err.message || 'Không thể bắt đầu bài kiểm tra'
      throw err
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
      
      // MOCK: Calculate result locally with smart grading
      const timeTaken = (quiz.value?.duration_minutes || 0) * 60 - timeLeft.value
      let correctCount = 0
      let totalPoints = 0
      let earnedPoints = 0
      const details = []
      
      questions.value.forEach((q, index) => {
        totalPoints += q.points
        const userAnswer = userAnswers.value[q.id]
        let isCorrect = false
        let pointsEarned = 0
        let yourAnswer = ''
        let correctAnswer = ''
        
        if (q.type === 'multiple_choice') {
          // Use actual correct_answers from question data
          const correctIds = q.correct_answers || []
          const userIds = Array.isArray(userAnswer) ? userAnswer : []
          isCorrect = JSON.stringify([...userIds].sort()) === JSON.stringify([...correctIds].sort())
          yourAnswer = q.options?.filter(o => userIds.includes(o.id)).map(o => o.text).join(', ') || 'Không có'
          correctAnswer = q.options?.filter(o => correctIds.includes(o.id)).map(o => o.text).join(', ') || 'Không có'
        } else if (q.type === 'true_false') {
          // Use actual correct_answer from question data
          const correctId = q.correct_answer
          isCorrect = userAnswer === correctId
          yourAnswer = q.options?.find(o => o.id === userAnswer)?.text || 'Không có'
          correctAnswer = q.options?.find(o => o.id === correctId)?.text || 'Không có'
        } else if (q.type === 'fill_blank') {
          // Use actual correct_answer from question data
          const correctText = q.correct_answer || ''
          const userText = userAnswer || ''
          isCorrect = userText.trim().toLowerCase() === correctText.trim().toLowerCase()
          yourAnswer = userText || 'Không có'
          correctAnswer = correctText
        } else if (q.type === 'matching') {
          // Check if all pairs match correctly
          const correctPairs = q.correct_pairs || {}
          const userPairs = userAnswer || {}
          isCorrect = JSON.stringify(userPairs) === JSON.stringify(correctPairs)
          yourAnswer = JSON.stringify(userPairs)
          correctAnswer = JSON.stringify(correctPairs)
        } else if (q.type === 'essay') {
          isCorrect = null // Needs manual grading
          yourAnswer = userAnswer || 'Không có'
          correctAnswer = 'Chờ giảng viên chấm'
        }
        
        if (isCorrect === true) {
          correctCount++
          pointsEarned = q.points
          earnedPoints += pointsEarned
        }
        
        details.push({
          question_number: index + 1,
          question_text: q.text,
          your_answer: yourAnswer,
          correct_answer: correctAnswer,
          is_correct: isCorrect,
          points_earned: pointsEarned,
          points_max: q.points,
          explanation: q.explanation || 'Giải thích sẽ được cập nhật'
        })
      })
      
      const score = totalPoints > 0 ? Math.round((earnedPoints / totalPoints) * 100) : 0
      
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
   * Navigate to question - Lưu câu trả lời trước khi chuyển
   */
  async function goToQuestion(index) {
    if (index >= 0 && index < totalQuestions.value) {
      // Lưu câu trả lời hiện tại trước khi chuyển
      await saveCurrentAnswer()
      currentQuestionIndex.value = index
    }
  }
  
  /**
   * Next question - Lưu câu trả lời hiện tại lên server trước khi chuyển câu
   */
  async function nextQuestion() {
    // Lưu câu trả lời hiện tại lên server
    await saveCurrentAnswer()
    
    // Chuyển sang câu tiếp theo
    if (currentQuestionIndex.value < totalQuestions.value - 1) {
      currentQuestionIndex.value++
    }
  }
  
  /**
   * Previous question - Lưu câu trả lời hiện tại trước khi quay lại
   */
  async function previousQuestion() {
    // Lưu câu trả lời hiện tại lên server
    await saveCurrentAnswer()
    
    if (currentQuestionIndex.value > 0) {
      currentQuestionIndex.value--
    }
  }
  
  /**
   * Lưu câu trả lời hiện tại lên server
   */
  async function saveCurrentAnswer() {
    const question = currentQuestion.value
    if (!question || !attemptId.value) return
    
    const answer = userAnswers.value[question.id]
    
    // Chỉ lưu nếu có câu trả lời
    if (!answer || (Array.isArray(answer) && answer.length === 0)) return
    
    try {
      // Chuẩn bị dữ liệu gửi lên server
      const payload = {
        cauHoiId: question.id,
        luaChonIds: Array.isArray(answer) ? answer : [answer], // Chuyển thành mảng
        thoiGianGiay: 0 // TODO: Tính thời gian làm câu này
      }
      
      console.log('Saving answer:', payload)
      
      await quizService.saveAnswer(attemptId.value, payload)
      console.log('Answer saved successfully for question', question.id)
    } catch (err) {
      console.error('Error saving answer:', err)
      // Không throw lỗi để không block việc chuyển câu
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
    saveCurrentAnswer,
    initializeAnswers,
    startTimer,
    stopTimer,
    updateTimeLeft,
    reset,
    resetForRetry
  }
})

export default useQuizStore
