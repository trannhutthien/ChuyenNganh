import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { googleTokenLogin } from 'vue3-google-login'

export function useGoogleAuth() {
  const router = useRouter()
  const authStore = useAuthStore()
  const isLoading = ref(false)
  const error = ref('')

  /**
   * Xử lý đăng nhập bằng Google
   */
  const loginWithGoogle = async () => {
    isLoading.value = true
    error.value = ''

    try {
      // Mở Google popup để user chọn tài khoản
      const response = await googleTokenLogin()
      
      console.log('Google login response:', response)
      
      // Gửi token lên backend để verify và tạo/đăng nhập user
      await authStore.loginWithGoogle(response.access_token)
      
      // Redirect về trang chủ hoặc trang trước đó
      const redirect = router.currentRoute.value.query.redirect || '/'
      router.push(redirect)
      
      return true
    } catch (err) {
      console.error('Google login error:', err)
      error.value = err.message || 'Đăng nhập Google thất bại. Vui lòng thử lại.'
      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Xử lý đăng ký bằng Google (tương tự login)
   */
  const registerWithGoogle = async () => {
    // Google OAuth tự động tạo tài khoản nếu chưa tồn tại
    return await loginWithGoogle()
  }

  return {
    isLoading,
    error,
    loginWithGoogle,
    registerWithGoogle
  }
}
