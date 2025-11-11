import axios from 'axios'

// Tạo axios instance - Sẽ kết nối với backend MySQL
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api', // Backend API URL
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// Request interceptor - Thêm token vào mỗi request
api.interceptors.request.use(
  (config) => {
    // Lấy token từ localStorage nếu có
    const token = localStorage.getItem('access_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - Xử lý response và lỗi
api.interceptors.response.use(
  (response) => {
    // Trả về data từ response
    return response.data
  },
  (error) => {
    // Xử lý lỗi chung
    if (error.response) {
      // Server trả về lỗi (4xx, 5xx)
      const { status, data } = error.response
      
      // Xử lý lỗi 401 - Unauthorized (token hết hạn)
      if (status === 401) {
        localStorage.removeItem('access_token')
        localStorage.removeItem('user')
        // Redirect to login page
        window.location.href = '/login'
      }
      
      // Xử lý lỗi 403 - Forbidden
      if (status === 403) {
        console.error('Access denied')
      }
      
      console.error('API Error:', data?.message || error.message)
      return Promise.reject(data || error)
    } else if (error.request) {
      // Request được gửi nhưng không nhận được response
      console.error('Network Error:', error.message)
      return Promise.reject({ message: 'Không thể kết nối đến server' })
    } else {
      // Lỗi khác
      console.error('Error:', error.message)
      return Promise.reject(error)
    }
  }
)

export default api
