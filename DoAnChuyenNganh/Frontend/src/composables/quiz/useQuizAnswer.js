import { ref, computed } from 'vue'

/**
 * Composable for managing quiz answers and marked questions
 * Handles answer storage, validation, and question marking
 */
export function useQuizAnswer(questions = []) {
  // State
  const userAnswers = ref({})
  const markedQuestions = ref([])
  const serverAnsweredCount = ref(null)

  // Computed: Số câu đã trả lời
  const answeredCount = computed(() => {
    return Object.keys(userAnswers.value).filter(questionId => {
      const answer = userAnswers.value[questionId]
      
      // Check if answer exists and is not empty
      if (answer === undefined || answer === null) return false
      
      // For string answers
      if (typeof answer === 'string') return answer.trim() !== ''
      
      // For array answers (multiple choice)
      if (Array.isArray(answer)) return answer.length > 0
      
      // For object answers (matching)
      if (typeof answer === 'object') return Object.keys(answer).length > 0
      
      return true
    }).length
  })

  // Computed: Số câu chưa trả lời
  const unansweredCount = computed(() => {
    return questions.length - answeredCount.value
  })

  // Computed: Phần trăm hoàn thành
  const completionPercentage = computed(() => {
    if (questions.length === 0) return 0
    return Math.round((answeredCount.value / questions.length) * 100)
  })

  // Computed: Danh sách ID câu đã trả lời
  const answeredQuestionIds = computed(() => {
    return Object.keys(userAnswers.value).filter(questionId => {
      return isAnswered(questionId)
    })
  })

  // Computed: Danh sách ID câu chưa trả lời
  const unansweredQuestionIds = computed(() => {
    return questions.map(q => q.id).filter(id => !isAnswered(id))
  })

  // Methods: Kiểm tra câu hỏi đã được trả lời chưa
  const isAnswered = (questionId) => {
    const answer = userAnswers.value[questionId]
    
    if (answer === undefined || answer === null) return false
    
    if (typeof answer === 'string') return answer.trim() !== ''
    
    if (Array.isArray(answer)) return answer.length > 0
    
    if (typeof answer === 'object') return Object.keys(answer).length > 0
    
    return true
  }

  // Methods: Kiểm tra câu hỏi đã được đánh dấu chưa
  const isMarked = (questionId) => {
    return markedQuestions.value.includes(questionId)
  }

  // Methods: Set câu trả lời cho một câu hỏi
  const setAnswer = (questionId, answer) => {
    userAnswers.value[questionId] = answer
  }

  // Methods: Get câu trả lời của một câu hỏi
  const getAnswer = (questionId) => {
    return userAnswers.value[questionId]
  }

  // Methods: Xóa câu trả lời của một câu hỏi
  const clearAnswer = (questionId) => {
    delete userAnswers.value[questionId]
  }

  // Methods: Xóa tất cả câu trả lời
  const clearAllAnswers = () => {
    userAnswers.value = {}
  }

  // Methods: Toggle đánh dấu câu hỏi
  const toggleMark = (questionId) => {
    const index = markedQuestions.value.indexOf(questionId)
    if (index > -1) {
      markedQuestions.value.splice(index, 1)
    } else {
      markedQuestions.value.push(questionId)
    }
  }

  // Methods: Đánh dấu câu hỏi
  const markQuestion = (questionId) => {
    if (!isMarked(questionId)) {
      markedQuestions.value.push(questionId)
    }
  }

  // Methods: Bỏ đánh dấu câu hỏi
  const unmarkQuestion = (questionId) => {
    const index = markedQuestions.value.indexOf(questionId)
    if (index > -1) {
      markedQuestions.value.splice(index, 1)
    }
  }

  // Methods: Xóa tất cả đánh dấu
  const clearAllMarks = () => {
    markedQuestions.value = []
  }

  // Methods: Kiểm tra option có được chọn không (cho multiple choice)
  const isOptionSelected = (questionId, optionId) => {
    const answer = userAnswers.value[questionId]
    return Array.isArray(answer) && answer.includes(optionId)
  }

  // Methods: Load answers từ saved data
  const loadAnswers = (savedAnswers) => {
    if (savedAnswers && typeof savedAnswers === 'object') {
      userAnswers.value = { ...savedAnswers }
    }
  }

  // Methods: Load marked questions từ saved data
  const loadMarkedQuestions = (savedMarks) => {
    if (Array.isArray(savedMarks)) {
      markedQuestions.value = [...savedMarks]
    }
  }

  // Methods: Export answers data (để lưu hoặc submit)
  const exportAnswers = () => {
    return {
      answers: { ...userAnswers.value },
      markedQuestions: [...markedQuestions.value],
      answeredCount: answeredCount.value,
      completionPercentage: completionPercentage.value
    }
  }

  // Methods: Validate all answers
  const validateAnswers = () => {
    const errors = []
    
    questions.forEach(question => {
      if (!isAnswered(question.id)) {
        errors.push({
          questionId: question.id,
          message: 'Câu hỏi chưa được trả lời'
        })
      }
    })
    
    return {
      isValid: errors.length === 0,
      errors
    }
  }

  // Methods: Get unanswered questions
  const getUnansweredQuestions = () => {
    return questions.filter(q => !isAnswered(q.id))
  }

  // Methods: Get marked questions
  const getMarkedQuestions = () => {
    return questions.filter(q => isMarked(q.id))
  }

  // Methods: Get answer summary
  const getAnswerSummary = () => {
    return {
      total: questions.length,
      answered: answeredCount.value,
      unanswered: unansweredCount.value,
      marked: markedQuestions.value.length,
      completion: completionPercentage.value
    }
  }

  return {
    // State
    userAnswers,
    markedQuestions,
    
    // Computed
    answeredCount,
    unansweredCount,
    completionPercentage,
    answeredQuestionIds,
    unansweredQuestionIds,
    
    // Methods
    isAnswered,
    isMarked,
    setAnswer,
    getAnswer,
    clearAnswer,
    clearAllAnswers,
    toggleMark,
    markQuestion,
    unmarkQuestion,
    clearAllMarks,
    isOptionSelected,
    loadAnswers,
    loadMarkedQuestions,
    exportAnswers,
    validateAnswers,
    getUnansweredQuestions,
    getMarkedQuestions,
    getAnswerSummary
  }
}
