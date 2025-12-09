import { ref } from 'vue'
import api from '@/services/api'

/**
 * Composable for quiz submission and grading
 * Handles answer submission, scoring, and result processing
 */
export function useQuizSubmit() {
  // State
  const isSubmitting = ref(false)
  const result = ref(null)
  const error = ref(null)

  /**
   * Submit quiz to backend for grading
   * @param {Object} payload - Submission payload
   * @param {number} payload.attemptId - Quiz attempt ID
   * @param {Object} payload.answers - User answers
   * @param {number} payload.timeTaken - Time taken in seconds
   * @param {Array} payload.markedQuestions - Marked question IDs
   */
  const submitQuiz = async (payload) => {
    const { attemptId, answers, timeTaken, markedQuestions = [] } = payload

    isSubmitting.value = true
    error.value = null
    result.value = null

    try {
      // Call API to submit quiz
      const response = await api.post(`/api/quiz/attempt/${attemptId}/submit`, {
        answers,
        time_taken: timeTaken,
        marked_questions: markedQuestions
      })

      result.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Có lỗi xảy ra khi nộp bài'
      throw err
    } finally {
      isSubmitting.value = false
    }
  }

  /**
   * Calculate result locally (for preview/mock)
   * @param {Object} payload - Calculation payload
   * @param {Array} payload.questions - List of questions
   * @param {Object} payload.answers - User answers
   * @param {number} payload.timeTaken - Time taken in seconds
   * @param {number} payload.passingScore - Passing score percentage
   */
  const calculateResult = (payload) => {
    const { questions, answers, timeTaken, passingScore = 70 } = payload

    let correctCount = 0
    let totalPoints = 0
    let earnedPoints = 0
    const details = []

    questions.forEach((question, index) => {
      totalPoints += question.points || 0
      const userAnswer = answers[question.id]
      
      const questionResult = gradeQuestion(question, userAnswer)
      
      if (questionResult.isCorrect) {
        correctCount++
        earnedPoints += question.points || 0
      }

      details.push({
        question_number: index + 1,
        question_text: question.text,
        your_answer: questionResult.yourAnswer,
        correct_answer: questionResult.correctAnswer,
        is_correct: questionResult.isCorrect,
        points_earned: questionResult.isCorrect ? (question.points || 0) : 0,
        points_max: question.points || 0,
        explanation: question.explanation || null
      })
    })

    const score = totalPoints > 0 ? Math.round((earnedPoints / totalPoints) * 100) : 0

    const calculatedResult = {
      score,
      passed: score >= passingScore,
      correct_answers: correctCount,
      total_questions: questions.length,
      time_taken: formatTime(timeTaken),
      details
    }

    result.value = calculatedResult
    return calculatedResult
  }

  /**
   * Grade individual question
   * @param {Object} question - Question object
   * @param {any} userAnswer - User's answer
   */
  const gradeQuestion = (question, userAnswer) => {
    const { type, options, correct_answer, correct_answers } = question
    
    let isCorrect = false
    let yourAnswer = 'Chưa trả lời'
    let correctAnswer = ''

    switch (type) {
      case 'multiple_choice': {
        // Multiple selection
        const correctIds = correct_answers || []
        const userIds = Array.isArray(userAnswer) ? userAnswer : []
        
        isCorrect = JSON.stringify([...userIds].sort()) === JSON.stringify([...correctIds].sort())
        
        yourAnswer = options
          ?.filter(o => userIds.includes(o.id))
          .map(o => o.text)
          .join(', ') || 'Không có'
        
        correctAnswer = options
          ?.filter(o => correctIds.includes(o.id))
          .map(o => o.text)
          .join(', ') || ''
        break
      }

      case 'true_false': {
        // Single selection
        const correctId = correct_answer
        isCorrect = userAnswer === correctId
        
        yourAnswer = options?.find(o => o.id === userAnswer)?.text || 'Không có'
        correctAnswer = options?.find(o => o.id === correctId)?.text || ''
        break
      }

      case 'matching': {
        // Object comparison {leftIndex: rightIndex}
        const correctMatches = correct_answers || {}
        const userMatches = userAnswer || {}
        
        isCorrect = JSON.stringify(correctMatches) === JSON.stringify(userMatches)
        
        yourAnswer = Object.keys(userMatches).length > 0 
          ? `${Object.keys(userMatches).length} cặp` 
          : 'Không có'
        correctAnswer = `${Object.keys(correctMatches).length} cặp đúng`
        break
      }

      case 'essay':
      case 'code': {
        // Needs manual grading
        isCorrect = null // null means pending grading
        yourAnswer = userAnswer || 'Không có'
        correctAnswer = 'Chờ giảng viên chấm'
        break
      }

      default:
        yourAnswer = 'Không xác định'
        correctAnswer = 'Không xác định'
    }

    return {
      isCorrect,
      yourAnswer,
      correctAnswer
    }
  }

  /**
   * Format time in seconds to MM:SS or HH:MM:SS
   * @param {number} seconds - Time in seconds
   */
  const formatTime = (seconds) => {
    const hours = Math.floor(seconds / 3600)
    const mins = Math.floor((seconds % 3600) / 60)
    const secs = seconds % 60

    if (hours > 0) {
      return `${hours}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
    }
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  /**
   * Get result summary for display
   */
  const getResultSummary = () => {
    if (!result.value) return null

    return {
      score: result.value.score,
      passed: result.value.passed,
      correctAnswers: result.value.correct_answers,
      totalQuestions: result.value.total_questions,
      timeTaken: result.value.time_taken,
      accuracy: Math.round((result.value.correct_answers / result.value.total_questions) * 100)
    }
  }

  /**
   * Reset submission state
   */
  const reset = () => {
    isSubmitting.value = false
    result.value = null
    error.value = null
  }

  return {
    // State
    isSubmitting,
    result,
    error,

    // Methods
    submitQuiz,
    calculateResult,
    gradeQuestion,
    formatTime,
    getResultSummary,
    reset
  }
}
