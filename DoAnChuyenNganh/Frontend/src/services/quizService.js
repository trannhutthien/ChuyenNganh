import api from './api'

/**
 * Quiz Service - Xử lý các API liên quan đến quiz
 */
export const quizService = {
  /**
   * Lấy thông tin quiz theo ID
   * @param {number|string} quizId - ID của quiz
   * @returns {Promise} Quiz data với questions
   */
  fetchQuiz(quizId) {
    return api.get(`/quiz/${quizId}`)
  },

  /**
   * Bắt đầu quiz mới
   * @param {number|string} quizId - ID của quiz
   * @returns {Promise} Attempt data với attemptId
   */
  startQuiz(quizId) {
    return api.post(`/quiz/${quizId}/start`)
  },

  /**
   * Submit quiz và nhận kết quả
   * @param {number|string} attemptId - ID của attempt
   * @param {Object} answers - User answers { questionId: answer }
   * @param {number} timeTaken - Thời gian làm bài (giây)
   * @returns {Promise} Result data với score, passed, details
   */
  submitQuiz(attemptId, answers, timeTaken = 0) {
    return api.post(`/quiz/attempt/${attemptId}/submit`, {
      answers,
      time_taken: timeTaken
    })
  },

  /**
   * Lưu tiến trình làm bài (auto-save)
   * @param {number|string} attemptId - ID của attempt
   * @param {Object} data - Progress data
   * @param {Object} data.answers - Current answers
   * @param {Array} data.markedQuestions - Marked question IDs
   * @param {number} data.currentQuestionIndex - Current question index
   * @param {number} data.timeLeft - Remaining time (seconds)
   * @returns {Promise} Save confirmation
   */
  saveProgress(attemptId, data) {
    return api.patch(`/quiz/attempt/${attemptId}/auto-save`, {
      answers: data.answers || {},
      marked_questions: data.markedQuestions || [],
      current_question_index: data.currentQuestionIndex || 0,
      time_left: data.timeLeft || 0,
      saved_at: new Date().toISOString()
    })
  },

  /**
   * Khôi phục quiz đang làm dở
   * @param {number|string} attemptId - ID của attempt
   * @returns {Promise} Saved progress data
   */
  resumeQuiz(attemptId) {
    return api.get(`/quiz/attempt/${attemptId}/resume`)
  },

  /**
   * Lấy kết quả quiz theo attemptId
   * @param {number|string} attemptId - ID của attempt
   * @returns {Promise} Result data với score, details, time_taken
   */
  getQuizResult(attemptId) {
    return api.get(`/quiz/attempt/${attemptId}/result`)
  },

  /**
   * Lấy lịch sử làm quiz
   * @param {number|string} quizId - ID của quiz
   * @returns {Promise} Array of attempts với scores, dates
   */
  getQuizHistory(quizId) {
    return api.get(`/quiz/${quizId}/history`)
  },

  /**
   * Lấy danh sách quiz theo courseId
   * @param {number|string} courseId - ID của course
   * @returns {Promise} Array of quizzes
   */
  getQuizzesByCourse(courseId) {
    return api.get(`/courses/${courseId}/quizzes`)
  },

  /**
   * Lấy danh sách quiz theo lessonId
   * @param {number|string} lessonId - ID của lesson
   * @returns {Promise} Array of quizzes
   */
  getQuizzesByLesson(lessonId) {
    return api.get(`/lessons/${lessonId}/quizzes`)
  },

  /**
   * Xóa một attempt (nếu chưa submit)
   * @param {number|string} attemptId - ID của attempt
   * @returns {Promise} Delete confirmation
   */
  deleteAttempt(attemptId) {
    return api.delete(`/quiz/attempt/${attemptId}`)
  },

  /**
   * Lấy thống kê quiz (admin/instructor)
   * @param {number|string} quizId - ID của quiz
   * @returns {Promise} Statistics data
   */
  getQuizStatistics(quizId) {
    return api.get(`/quiz/${quizId}/statistics`)
  },

  /**
   * Tạo quiz mới (admin/instructor)
   * @param {Object} data - Quiz data
   * @returns {Promise} Created quiz
   */
  createQuiz(data) {
    return api.post('/quiz', data)
  },

  /**
   * Cập nhật quiz (admin/instructor)
   * @param {number|string} quizId - ID của quiz
   * @param {Object} data - Updated quiz data
   * @returns {Promise} Updated quiz
   */
  updateQuiz(quizId, data) {
    return api.put(`/quiz/${quizId}`, data)
  },

  /**
   * Xóa quiz (admin/instructor)
   * @param {number|string} quizId - ID của quiz
   * @returns {Promise} Delete confirmation
   */
  deleteQuiz(quizId) {
    return api.delete(`/quiz/${quizId}`)
  }
}

export default quizService
