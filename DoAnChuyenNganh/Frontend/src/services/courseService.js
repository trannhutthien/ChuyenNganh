import api from './api'

// Course Service
export const courseService = {
  // Lấy tất cả khóa học
  getAll(params = {}) {
    return api.get('/courses', { params })
  },

  // Lấy khóa học theo ID
  getById(id) {
    return api.get(`/courses/${id}`)
  },

  // Lấy khóa học theo category
  getByCategory(category) {
    return api.get('/courses', {
      params: { category }
    })
  },

  // Lấy khóa học theo level
  getByLevel(level) {
    return api.get('/courses', {
      params: { level }
    })
  },

  // Tìm kiếm khóa học
  search(keyword) {
    return api.get('/courses', {
      params: { q: keyword }
    })
  },

  // Tạo khóa học mới (admin)
  create(data) {
    return api.post('/courses', data)
  },

  // Cập nhật khóa học (admin)
  update(id, data) {
    return api.put(`/courses/${id}`, data)
  },

  // Xóa khóa học (admin)
  delete(id) {
    return api.delete(`/courses/${id}`)
  }
}

// Roadmap Service
export const roadmapService = {
  getAll() {
    return api.get('/roadmaps')
  },

  getById(id) {
    return api.get(`/roadmaps/${id}`)
  }
}

// Post Service
export const postService = {
  getAll(params = {}) {
    return api.get('/posts', { params })
  },

  getById(id) {
    return api.get(`/posts/${id}`)
  },

  getByCategory(category) {
    return api.get('/posts', {
      params: { category }
    })
  },

  search(keyword) {
    return api.get('/posts', {
      params: { q: keyword }
    })
  }
}

// User Service
export const userService = {
  register(data) {
    return api.post('/users', data)
  },

  login(email, password) {
    // Mock login - JSON Server không hỗ trợ authentication
    return api.get('/users', {
      params: { email, password }
    })
  },

  getProfile(id) {
    return api.get(`/users/${id}`)
  },

  updateProfile(id, data) {
    return api.put(`/users/${id}`, data)
  }
}
