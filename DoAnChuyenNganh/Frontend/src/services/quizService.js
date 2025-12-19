import api from './api'

/**
 * Quiz Service - Xử lý các API liên quan đến quiz/bài kiểm tra
 * File: quizService.js
 */
export const quizService = {
  /**
   * Lấy thông tin bài kiểm tra theo ID
   * API: GET /api/bai-kiem-tra/{id}
   * @param {number|string} quizId - ID của bài kiểm tra
   * @returns {Promise} Quiz data
   */
  fetchQuiz(quizId) {
    return api.get(`/bai-kiem-tra/${quizId}`)
  },

  /**
   * Bắt đầu làm bài kiểm tra
   * API: POST /api/lam-bai/bat-dau
   * Backend sẽ trả về câu hỏi từ database dựa vào ThietLapJson
   * @param {number|string} quizId - ID của bài kiểm tra
   * @returns {Promise} { data: lanLamBai, cauHois: [...] }
   */
  startQuiz(quizId) {
    return api.post('/lam-bai/bat-dau', { baiKiemTraId: quizId })
  },

  /**
   * Lưu câu trả lời
   * API: POST /api/lam-bai/{lanLamBaiId}/tra-loi
   * @param {number|string} attemptId - ID lần làm bài
   * @param {Object} answer - { cauHoiId, luaChonIds, noiDungTraLoi }
   * @returns {Promise}
   */
  saveAnswer(attemptId, answer) {
    return api.post(`/lam-bai/${attemptId}/tra-loi`, answer)
  },

  /**
   * Nộp bài kiểm tra
   * API: POST /api/lam-bai/{lanLamBaiId}/nop-bai
   * @param {number|string} attemptId - ID lần làm bài
   * @returns {Promise} Kết quả bài kiểm tra
   */
  submitQuiz(attemptId) {
    return api.post(`/lam-bai/${attemptId}/nop-bai`)
  },

  /**
   * Xem kết quả bài kiểm tra
   * API: GET /api/lam-bai/{lanLamBaiId}/ket-qua
   * @param {number|string} attemptId - ID lần làm bài
   * @returns {Promise}
   */
  getQuizResult(attemptId) {
    return api.get(`/lam-bai/${attemptId}/ket-qua`)
  },

  /**
   * Lấy bài làm đang thực hiện (nếu có)
   * API: GET /api/lam-bai/dang-lam/{baiKiemTraId}
   * @param {number|string} quizId - ID bài kiểm tra
   * @returns {Promise}
   */
  getInProgressAttempt(quizId) {
    return api.get(`/lam-bai/dang-lam/${quizId}`)
  },

  /**
   * Lấy lịch sử làm bài
   * API: GET /api/lam-bai/lich-su
   * @returns {Promise}
   */
  getHistory() {
    return api.get('/lam-bai/lich-su')
  },

  /**
   * Lấy lịch sử làm bài theo bài kiểm tra
   * API: GET /api/lam-bai/lich-su/{baiKiemTraId}
   * @param {number|string} quizId - ID bài kiểm tra
   * @returns {Promise}
   */
  getHistoryByQuiz(quizId) {
    return api.get(`/lam-bai/lich-su/${quizId}`)
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
   * Lấy danh sách bài kiểm tra của khóa học (bao gồm cả cuối khóa và theo bài học)
   * API: GET /api/bai-kiem-tra/khoa-hoc/{khoaHocId}
   * @param {number|string} courseId - ID của khóa học
   * @returns {Promise} Array of quizzes
   */
  getQuizzesByCourseOnly(courseId) {
    return api.get(`/bai-kiem-tra/khoa-hoc/${courseId}`)
  },

  /**
   * Lấy danh sách quiz theo lessonId
   * API: GET /api/bai-kiem-tra/bai-hoc/{baiHocId}
   * @param {number|string} lessonId - ID của lesson
   * @returns {Promise} Array of quizzes
   */
  getQuizzesByLesson(lessonId) {
    return api.get(`/bai-kiem-tra/bai-hoc/${lessonId}`)
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
  },

  // ========== BÀI KIỂM TRA CUỐI KHÓA ==========

  /**
   * Lấy thông tin bài kiểm tra cuối khóa của một khóa học
   * @param {number|string} courseId - ID của khóa học
   * @returns {Promise} Thông tin bài kiểm tra và điều kiện làm bài
   */
  getFinalExam(courseId) {
    return api.get(`/khoa-hoc/${courseId}/kiem-tra-cuoi-khoa`)
  },

  /**
   * Bắt đầu làm bài kiểm tra cuối khóa
   * @param {number|string} examId - ID của bài kiểm tra
   * @returns {Promise} Thông tin lần làm bài và câu hỏi
   */
  startFinalExam(examId) {
    return api.post(`/kiem-tra-cuoi-khoa/${examId}/bat-dau`)
  },

  /**
   * Lưu câu trả lời bài kiểm tra cuối khóa
   * @param {number|string} attemptId - ID lần làm bài
   * @param {Object} answer - Câu trả lời { cauHoiId, luaChonIds, thoiGianGiay }
   * @returns {Promise}
   */
  saveFinalExamAnswer(attemptId, answer) {
    return api.post(`/kiem-tra-cuoi-khoa/${attemptId}/tra-loi`, answer)
  },

  /**
   * Nộp bài kiểm tra cuối khóa
   * @param {number|string} attemptId - ID lần làm bài
   * @returns {Promise} Kết quả bài kiểm tra
   */
  submitFinalExam(attemptId) {
    return api.post(`/kiem-tra-cuoi-khoa/${attemptId}/nop-bai`)
  },

  /**
   * Xem kết quả chi tiết bài kiểm tra cuối khóa
   * @param {number|string} attemptId - ID lần làm bài
   * @returns {Promise} Kết quả chi tiết
   */
  getFinalExamResult(attemptId) {
    return api.get(`/kiem-tra-cuoi-khoa/${attemptId}/ket-qua`)
  }
}

export default quizService
