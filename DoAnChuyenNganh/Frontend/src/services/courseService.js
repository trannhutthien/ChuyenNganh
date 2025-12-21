import api from './api'

// Course Service - Kết nối với MySQL Backend
export const courseService = {
  // Lấy tất cả khóa học active (có phân trang)
  getAll(params = {}) {
    return api.get('/courses', { params })
  },

  // Lấy tất cả khóa học cho Admin (bao gồm inactive)
  getAllAdmin(params = {}) {
    return api.get('/courses/all', { params })
  },

  // Lấy khóa học theo ID
  getById(id) {
    return api.get(`/courses/${id}`)
  },

  // Lấy danh sách bài học của khóa học
  getLessons(courseId) {
    return api.get(`/courses/${courseId}/lessons`)
  },

  // Lấy chi tiết bài học theo ID
  getLessonById(lessonId) {
    return api.get(`/lessons/${lessonId}`)
  },

  // Lấy nội dung chi tiết của bài học
  getLessonContent(lessonId) {
    return api.get(`/lessons/${lessonId}/content`)
  },

  // Lấy khóa học Pro (có phí)
  getProCourses(limit = 8) {
    return api.get('/courses/pro', { params: { limit } })
  },

  // Lấy khóa học miễn phí
  getFreeCourses(limit = 8) {
    return api.get('/courses/free', { params: { limit } })
  },

  // Lấy khóa học theo category
  getByCategory(category, params = {}) {
    return api.get(`/courses/category/${category}`, { params })
  },

  // Lấy khóa học theo level
  getByLevel(level, params = {}) {
    return api.get(`/courses/level/${level}`, { params })
  },

  // Tìm kiếm khóa học
  search(keyword, params = {}) {
    return api.get('/courses/search', {
      params: { q: keyword, ...params }
    })
  },

  // Lấy khóa học phổ biến
  getPopular(limit = 8) {
    return api.get('/courses/popular', { params: { limit } })
  },

  // Lấy khóa học mới nhất
  getLatest(limit = 8) {
    return api.get('/courses/latest', { params: { limit } })
  },

  // Tạo khóa học mới (admin/instructor)
  create(data) {
    return api.post('/courses', data)
  },

  // Cập nhật khóa học (admin/instructor)
  update(id, data) {
    return api.put(`/courses/${id}`, data)
  },

  // Xóa khóa học (admin)
  delete(id) {
    return api.delete(`/courses/${id}`)
  },

  // Đăng ký khóa học
  enroll(courseId) {
    return api.post(`/courses/${courseId}/enroll`)
  },

  // Lấy danh sách khóa học đã đăng ký
  getEnrolledCourses() {
    return api.get('/courses/enrolled')
  }
}

// Roadmap Service - Lộ trình học tập
export const roadmapService = {
  // Lấy danh sách lộ trình active
  getAll() {
    return api.get('/lo-trinh')
  },

  // Lấy tất cả lộ trình (Admin)
  getAllAdmin() {
    return api.get('/lo-trinh/all')
  },

  // Lấy chi tiết lộ trình theo slug
  getBySlug(slug) {
    return api.get(`/lo-trinh/${slug}`)
  },

  // Lấy các khóa học trong lộ trình
  getCourses(id) {
    return api.get(`/lo-trinh/${id}/khoa-hoc`)
  },

  // Tạo lộ trình mới (Admin)
  create(data) {
    return api.post('/lo-trinh', data)
  },

  // Cập nhật lộ trình (Admin)
  update(id, data) {
    return api.put(`/lo-trinh/${id}`, data)
  },

  // Xóa lộ trình (Admin)
  delete(id) {
    return api.delete(`/lo-trinh/${id}`)
  },

  // Thêm khóa học vào lộ trình (Admin)
  addCourse(id, data) {
    return api.post(`/lo-trinh/${id}/khoa-hoc`, data)
  },

  // Cập nhật khóa học trong lộ trình (Admin)
  updateCourse(id, khoaHocId, data) {
    return api.put(`/lo-trinh/${id}/khoa-hoc/${khoaHocId}`, data)
  },

  // Xóa khóa học khỏi lộ trình (Admin)
  removeCourse(id, khoaHocId) {
    return api.delete(`/lo-trinh/${id}/khoa-hoc/${khoaHocId}`)
  }
}

// Post Service (Bài viết)
export const postService = {
  getAll(params = {}) {
    return api.get('/posts', { params })
  },

  getById(id) {
    return api.get(`/posts/${id}`)
  },

  getByCategory(category, params = {}) {
    return api.get(`/posts/category/${category}`, { params })
  },

  search(keyword, params = {}) {
    return api.get('/posts/search', {
      params: { q: keyword, ...params }
    })
  },

  // Lấy bài viết mới nhất
  getLatest(limit = 5) {
    return api.get('/posts/latest', { params: { limit } })
  },

  // Tạo bài viết mới
  create(data) {
    return api.post('/posts', data)
  },

  // Cập nhật bài viết
  update(id, data) {
    return api.put(`/posts/${id}`, data)
  },

  // Xóa bài viết
  delete(id) {
    return api.delete(`/posts/${id}`)
  }
}

// Auth Service (Xác thực)
export const authService = {
  // Đăng ký
  register(data) {
    return api.post('/auth/register', data)
  },

  // Đăng nhập
  login(email, password) {
    return api.post('/auth/login', { email, password })
  },

  // Đăng xuất
  logout() {
    return api.post('/auth/logout')
  },

  // Lấy thông tin user hiện tại
  me() {
    return api.get('/auth/me')
  },

  // Refresh token
  refreshToken() {
    return api.post('/auth/refresh')
  }
}

// User Service
export const userService = {
  // Lấy thông tin profile
  getProfile() {
    return api.get('/user/profile')
  },

  // Cập nhật profile
  updateProfile(data) {
    return api.put('/user/profile', data)
  },

  // Đổi mật khẩu
  changePassword(oldPassword, newPassword) {
    return api.post('/user/change-password', { 
      old_password: oldPassword, 
      new_password: newPassword 
    })
  },

  // Upload avatar
  uploadAvatar(file) {
    const formData = new FormData()
    formData.append('avatar', file)
    return api.post('/user/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }
}
