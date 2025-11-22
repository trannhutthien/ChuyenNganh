import { ref, computed, unref } from 'vue'

/**
 * Composable for quiz navigation
 * Handles moving between questions and tracking current position
 */
export function useQuizNavigation(questionsOrTotal = 0, initialIndex = 0) {
  // State
  const currentIndex = ref(initialIndex)

  // Computed: Support both array of questions or total number
  const totalQuestions = computed(() => {
    const value = unref(questionsOrTotal)
    return Array.isArray(value) ? value.length : (typeof value === 'number' ? value : 0)
  })

  // Computed
  const currentQuestionNumber = computed(() => currentIndex.value + 1)
  
  const isFirst = computed(() => currentIndex.value === 0)
  
  const isLast = computed(() => currentIndex.value >= totalQuestions.value - 1)
  
  const canGoPrevious = computed(() => !isFirst.value)
  
  const canGoNext = computed(() => !isLast.value)
  
  const progress = computed(() => {
    if (totalQuestions.value === 0) return 0
    return Math.round(((currentIndex.value + 1) / totalQuestions.value) * 100)
  })

  // Methods
  const goToPrevious = () => {
    if (canGoPrevious.value) {
      currentIndex.value--
      return true
    }
    return false
  }

  const goToNext = () => {
    if (canGoNext.value) {
      currentIndex.value++
      return true
    }
    return false
  }

  const goToQuestion = (index) => {
    if (index >= 0 && index < totalQuestions.value) {
      currentIndex.value = index
      return true
    }
    return false
  }

  const goToFirst = () => {
    currentIndex.value = 0
  }

  const goToLast = () => {
    currentIndex.value = totalQuestions.value - 1
  }

  /**
   * Go to next unanswered question
   * @param {Function} isAnsweredCallback - Function to check if question is answered
   */
  const goToNextUnanswered = (isAnsweredCallback) => {
    if (!isAnsweredCallback) return false

    // Search forward from current position
    for (let i = currentIndex.value + 1; i < totalQuestions.value; i++) {
      if (!isAnsweredCallback(i)) {
        currentIndex.value = i
        return true
      }
    }

    // Search from beginning if not found
    for (let i = 0; i < currentIndex.value; i++) {
      if (!isAnsweredCallback(i)) {
        currentIndex.value = i
        return true
      }
    }

    return false // All questions answered
  }

  /**
   * Go to first unanswered question
   * @param {Function} isAnsweredCallback - Function to check if question is answered
   */
  const goToFirstUnanswered = (isAnsweredCallback) => {
    if (!isAnsweredCallback) return false

    for (let i = 0; i < totalQuestions.value; i++) {
      if (!isAnsweredCallback(i)) {
        currentIndex.value = i
        return true
      }
    }

    return false // All questions answered
  }

  /**
   * Go to next marked question
   * @param {Array} markedIndices - Array of marked question indices
   */
  const goToNextMarked = (markedIndices) => {
    if (!markedIndices || markedIndices.length === 0) return false

    // Search forward from current position
    const nextMarked = markedIndices.find(idx => idx > currentIndex.value)
    if (nextMarked !== undefined) {
      currentIndex.value = nextMarked
      return true
    }

    // Wrap around to first marked
    if (markedIndices.length > 0) {
      currentIndex.value = markedIndices[0]
      return true
    }

    return false
  }

  /**
   * Get relative position info
   */
  const getPositionInfo = () => {
    return {
      current: currentIndex.value,
      number: currentQuestionNumber.value,
      total: totalQuestions.value,
      isFirst: isFirst.value,
      isLast: isLast.value,
      canGoPrevious: canGoPrevious.value,
      canGoNext: canGoNext.value,
      progress: progress.value
    }
  }

  /**
   * Reset to first question
   */
  const reset = () => {
    currentIndex.value = 0
  }

  /**
   * Get distance to question
   */
  const getDistanceTo = (targetIndex) => {
    return Math.abs(targetIndex - currentIndex.value)
  }

  /**
   * Check if on specific question
   */
  const isOnQuestion = (index) => {
    return currentIndex.value === index
  }

  return {
    // State
    currentIndex,

    // Computed
    currentQuestionNumber,
    isFirst,
    isLast,
    canGoPrevious,
    canGoNext,
    progress,

    // Methods
    goToPrevious,
    goToNext,
    goToQuestion,
    goToFirst,
    goToLast,
    goToNextUnanswered,
    goToFirstUnanswered,
    goToNextMarked,
    getPositionInfo,
    reset,
    getDistanceTo,
    isOnQuestion
  }
}
