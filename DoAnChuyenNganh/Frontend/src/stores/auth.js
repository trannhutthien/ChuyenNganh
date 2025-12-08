import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '@/services/authService'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const isAuthenticated = computed(() => !!token.value)
  const userRoles = computed(() => user.value?.roles || [])
  const isStudent = computed(() => userRoles.value.includes('STUDENT'))
  const isAdmin = computed(() => userRoles.value.includes('ADMIN'))

  // Actions

  /**
   * Khởi tạo auth state từ localStorage
   */
  function initAuth() {
    const savedToken = localStorage.getItem('access_token')
    const savedUser = localStorage.getItem('user')
    
    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
    }
  }

  /**
   * Đăng nhập bằng email/password
   */
  async function login(email, password) {
    isLoading.value = true
    error.value = null

    try {
      const response = await authService.login(email, password)
      
      // Kiểm tra role
      const hasValidRole = response.user.roles.includes('STUDENT') || 
                          response.user.roles.includes('ADMIN')
      
      if (!hasValidRole) {
        throw new Error('Tài khoản không có quyền truy cập')
      }

      // Lưu vào state và localStorage
      token.value = response.token
      user.value = response.user
      localStorage.setItem('access_token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))

      // Emit event để components khác cập nhật
      window.dispatchEvent(new Event('auth-changed'))

      return response
    } catch (err) {
      error.value = err.message || 'Đăng nhập thất bại'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Đăng ký tài khoản mới
   */
  async function register(data) {
    isLoading.value = true
    error.value = null

    try {
      const response = await authService.register(data)
      
      // Kiểm tra role STUDENT
      if (!response.user.roles.includes('STUDENT')) {
        throw new Error('Không thể gán role STUDENT')
      }

      // Lưu vào state và localStorage
      token.value = response.token
      user.value = response.user
      localStorage.setItem('access_token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))

      // Emit event
      window.dispatchEvent(new Event('auth-changed'))

      return response
    } catch (err) {
      error.value = err.message || 'Đăng ký thất bại'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Đăng nhập/Đăng ký bằng Google
   */
  async function loginWithGoogle(accessToken) {
    isLoading.value = true
    error.value = null

    try {
      // Gọi backend API để verify Google token và tạo/đăng nhập user
      const response = await authService.loginWithGoogle(accessToken)
      
      // Lưu vào state và localStorage
      token.value = response.token
      user.value = response.user
      localStorage.setItem('access_token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))

      // Emit event
      window.dispatchEvent(new Event('auth-changed'))

      return response
    } catch (err) {
      error.value = err.message || 'Đăng nhập Google thất bại'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Đăng xuất
   */
  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('access_token')
    localStorage.removeItem('user')
    
    // Emit event
    window.dispatchEvent(new Event('auth-changed'))
  }

  /**
   * Cập nhật thông tin user
   */
  function updateUser(userData) {
    user.value = { ...user.value, ...userData }
    localStorage.setItem('user', JSON.stringify(user.value))
  }

  return {
    // State
    user,
    token,
    isLoading,
    error,
    
    // Computed
    isAuthenticated,
    userRoles,
    isStudent,
    isAdmin,
    
    // Actions
    initAuth,
    login,
    register,
    loginWithGoogle,
    logout,
    updateUser
  }
})
