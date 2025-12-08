/**
 * Auth Service
 * File: src/services/authService.js
 * Mục đích: Xử lý các API liên quan đến xác thực người dùng (đăng nhập, đăng ký, đăng xuất)
 */

import api from './api'

export const authService = {
  /**
   * Đăng nhập
   * @param {string} email - Email người dùng
   * @param {string} password - Mật khẩu
   * @returns {Promise} Response chứa token và thông tin user
   */
  async login(email, password) {
    const response = await api.post('/login', { email, password })
    return response
  },

  /**
   * Đăng ký tài khoản mới
   * @param {Object} userData - Thông tin đăng ký { email, password, hoTen, ... }
   * @returns {Promise} Response chứa thông tin user mới
   */
  async register(userData) {
    const response = await api.post('/register', userData)
    return response
  },

  /**
   * Đăng nhập/Đăng ký bằng Google
   * @param {string} accessToken - Google access token từ OAuth
   * @returns {Promise} Response chứa token và thông tin user
   */
  async loginWithGoogle(accessToken) {
    const response = await api.post('/auth/google', { access_token: accessToken })
    return response
  },

  /**
   * Đăng xuất
   * @returns {Promise}
   */
  async logout() {
    const response = await api.post('/logout')
    // Xóa token và user khỏi localStorage
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    return response
  },

  /**
   * Lấy thông tin user hiện tại
   * @returns {Promise} Thông tin user
   */
  async getCurrentUser() {
    const response = await api.get('/user')
    return response
  },

  /**
   * Kiểm tra đã đăng nhập chưa
   * @returns {boolean}
   */
  isAuthenticated() {
    return !!localStorage.getItem('token')
  },

  /**
   * Lấy token từ localStorage
   * @returns {string|null}
   */
  getToken() {
    return localStorage.getItem('token')
  },

  /**
   * Lấy thông tin user từ localStorage
   * @returns {Object|null}
   */
  getUser() {
    const user = localStorage.getItem('user')
    return user ? JSON.parse(user) : null
  }
}

export default authService
