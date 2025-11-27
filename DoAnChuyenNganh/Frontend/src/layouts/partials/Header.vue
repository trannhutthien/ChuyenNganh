<template>
  <!-- Phần bên trái -->
  <div class="flex-shrink-0">
      <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
        
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
        
        </div>
        
       
        <span class="text-lg font-semibold text-gray-800">
          Kỹ năng lập trình
        </span>
      </a>
    </div>

    <!-- Phần giữa -->
    <div class="flex-1 flex justify-center">
      <div class="relative w-full max-w-[392px]">
        <!-- Icon tìm kiếm -->
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor" 
            class="w-5 h-5"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" 
            />
          </svg>
        </div>
        
        <!-- Input tìm kiếm -->
        <input 
          v-model="searchQuery"
          @keyup.enter="handleSearch"
          type="text" 
          placeholder="Tìm kiếm khóa học, bài viết, video...." 
          class="w-full h-[36px] pl-10 pr-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
        />
      </div>
    </div>

    <!-- Phần bên phải -->
    <div class="flex-shrink-0 flex items-center gap-3">
      <!-- Khi CHƯA đăng nhập: Hiển thị button Đăng nhập & Đăng ký -->
      <div v-if="!isLoggedIn" class="flex items-center gap-3">
        <BaseButton 
          @click="handleLogin"
          variant="outline"
          size="sm"
        >
          Đăng nhập
        </BaseButton>

        <BaseButton 
          @click="handleRegister"
          variant="primary"
          size="sm"
        >
          Đăng ký
        </BaseButton>
      </div>

      <!-- Khi ĐÃ đăng nhập: Hiển thị icon chuông thông báo & avatar -->
      <div v-else class="flex items-center gap-4">
        <!-- Icon chuông thông báo -->
        <div class="relative">
          <button 
            @click="toggleNotifications"
            class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke-width="1.5" 
              stroke="currentColor" 
              class="w-6 h-6"
            >
              <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" 
              />
            </svg>
            
            <!-- Badge số lượng thông báo -->
            <span 
              v-if="notificationCount > 0"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"
            >
              {{ notificationCount > 9 ? '9+' : notificationCount }}
            </span>
          </button>

          <!-- Dropdown thông báo -->
          <Transition name="fade-slide">
            <div 
              v-if="showNotifications"
              class="absolute right-0 top-12 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50"
            >
              <!-- Header -->
              <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Thông báo</h3>
                <button class="text-xs text-primary hover:text-primary-600">Đánh dấu đã đọc</button>
              </div>

              <!-- Danh sách thông báo -->
              <div class="max-h-96 overflow-y-auto">
                <div 
                  v-for="notification in notifications" 
                  :key="notification.id"
                  class="p-4 hover:bg-gray-50 border-b border-gray-100 cursor-pointer transition-colors"
                  :class="{ 'bg-blue-50': !notification.read }"
                >
                  <div class="flex gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                      <span class="text-lg">{{ notification.icon }}</span>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm text-gray-800 font-medium">{{ notification.title }}</p>
                      <p class="text-xs text-gray-600 mt-1">{{ notification.message }}</p>
                      <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                    </div>
                  </div>
                </div>

                <!-- Nếu không có thông báo -->
                <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-2 text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                  </svg>
                  <p class="text-sm">Chưa có thông báo mới</p>
                </div>
              </div>

              <!-- Footer -->
              <div class="p-3 border-t border-gray-200 text-center">
                <a href="#" class="text-sm text-primary hover:text-primary-600 font-medium">Xem tất cả</a>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Avatar user -->
        <div class="relative">
          <button 
            @click="toggleUserMenu"
            class="flex items-center gap-2 hover:opacity-80 transition-opacity"
          >
            <!-- Avatar -->
            <img 
              :src="currentUser.avatar" 
              :alt="currentUser.name"
              class="w-10 h-10 rounded-full object-cover border-2 border-primary"
            />
            
            <!-- Icon dropdown -->
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke-width="1.5" 
              stroke="currentColor" 
              class="w-4 h-4 text-gray-600"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
          </button>

          <!-- Dropdown user menu -->
          <Transition name="fade-slide">
            <div 
              v-if="showUserMenu"
              class="absolute right-0 top-14 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50"
            >
              <!-- User Info -->
              <div class="p-4 bg-gradient-to-r from-primary/10 to-purple-500/10 border-b border-gray-200">
                <div class="flex items-center gap-3">
                  <img 
                    :src="currentUser.avatar" 
                    :alt="currentUser.name"
                    class="w-12 h-12 rounded-full object-cover border-2 border-white"
                  />
                  <div>
                    <p class="font-semibold text-gray-800">{{ currentUser.name }}</p>
                    <p class="text-xs text-gray-600">{{ currentUser.email }}</p>
                  </div>
                </div>
              </div>

              <!-- Menu items -->
              <div class="py-2">
                <a 
                  href="#" 
                  class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                  </svg>
                  <span class="text-sm">Trang cá nhân</span>
                </a>

                <a 
                  href="#" 
                  class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                  </svg>
                  <span class="text-sm">Khóa học của tôi</span>
                </a>

                <a 
                  href="#" 
                  class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                  <span class="text-sm">Cài đặt</span>
                </a>
              </div>

              <!-- Logout -->
              <div class="border-t border-gray-200">
                <button 
                  @click="handleLogout"
                  class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                  </svg>
                  <span class="text-sm font-medium">Đăng xuất</span>
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>

  <!-- Modal Đăng nhập -->
  <LoginModal 
    :isOpen="showLoginModal" 
    :loading="loginLoading"
    :error="loginError"
    @close="showLoginModal = false"
    @submit="handleLoginSubmit"
    @switchToRegister="switchToRegister"
  />

  <!-- Modal Đăng ký -->
  <RegisterModal 
    :isOpen="showRegisterModal" 
    @close="showRegisterModal = false"
    @submit="handleRegisterSubmit"
    @switchToLogin="switchToLogin"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BaseButton from '../../components/ui/BaseButton.vue'
import RegisterModal from '../../components/modal/RegisterModal.vue'
import LoginModal from '../../components/modal/LoginModal.vue'
import { authService } from '../../services/courseService.js'

// State
const searchQuery = ref('')
const showRegisterModal = ref(false)
const showLoginModal = ref(false)
const loginLoading = ref(false)
const loginError = ref('')

// ========== AUTH STATE ==========
// Trạng thái đăng nhập - Đổi thành true để test giao diện đã đăng nhập
const isLoggedIn = ref(false)

// Thông tin user hiện tại
const currentUser = ref({
  name: 'nguoidung',
  email: 'nguoidung@example.com',
  avatar: 'https://i.pravatar.cc/150?img=12'
})

// ========== NOTIFICATIONS ==========
const showNotifications = ref(false)
const notificationCount = ref(3) // Số lượng thông báo chưa đọc

const notifications = ref([
  {
    id: 1,
    icon: '🎉',
    title: 'Chào mừng bạn đến với nền tảng!',
    message: 'Bắt đầu học lập trình ngay hôm nay',
    time: '5 phút trước',
    read: false
  },
  {
    id: 2,
    icon: '📚',
    title: 'Khóa học mới',
    message: 'Vue.js 3 Composition API đã được cập nhật',
    time: '2 giờ trước',
    read: false
  },
  {
    id: 3,
    icon: '⭐',
    title: 'Hoàn thành bài học',
    message: 'Bạn đã hoàn thành "JavaScript Căn bản"',
    time: '1 ngày trước',
    read: true
  },
  
])

// ========== USER MENU ==========
const showUserMenu = ref(false)

// Methods
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Tìm kiếm:', searchQuery.value)
    // Thêm logic tìm kiếm
  }
}

const handleLogin = () => {
  showLoginModal.value = true
}

const handleRegister = () => {
  showRegisterModal.value = true
}

const handleLoginSubmit = async (data) => {
  loginLoading.value = true
  loginError.value = ''
  
  try {
    // Call backend API (response interceptor already unwraps .data)
    const response = await authService.login(data.email, data.password)
    
    // Check role STUDENT (backend already checked, but double verification)
    if (!response.user.roles.includes('STUDENT')) {
      loginError.value = 'Không phải học viên'
      return
    }
    
    // Save token and user data to localStorage
    localStorage.setItem('access_token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
    
    // Update UI state
    isLoggedIn.value = true
    currentUser.value = {
      name: response.user.name,
      email: response.user.email,
      avatar: 'https://i.pravatar.cc/150?img=12'
    }
    
    // Close modal on success
    showLoginModal.value = false
    loginError.value = ''
    
    console.log('Đăng nhập thành công:', response.user)
  } catch (error) {
    console.error('Lỗi đăng nhập:', error)
    
    // Handle error response
    if (error.message) {
      loginError.value = error.message
    } else {
      loginError.value = 'Đăng nhập thất bại'
    }
  } finally {
    loginLoading.value = false
  }
}

const handleRegisterSubmit = (data) => {
  console.log('Dữ liệu đăng ký:', data)
  // TODO: Call API đăng ký
  showRegisterModal.value = false
  // Sau khi đăng ký thành công, có thể tự động đăng nhập
}

const switchToRegister = () => {
  showLoginModal.value = false
  showRegisterModal.value = true
}

const switchToLogin = () => {
  showRegisterModal.value = false
  showLoginModal.value = true
}

// ========== NOTIFICATION FUNCTIONS ==========
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false // Đóng user menu khi mở notifications
}

// ========== USER MENU FUNCTIONS ==========
const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false // Đóng notifications khi mở user menu
}

const handleLogout = async () => {
  try {
    // Call backend logout API
    await authService.logout()
  } catch (error) {
    console.error('Lỗi logout:', error)
  } finally {
    // Clear local state regardless of API result
    isLoggedIn.value = false
    showUserMenu.value = false
    currentUser.value = null
    
    // Clear localStorage
    localStorage.removeItem('access_token')
    localStorage.removeItem('user')
    
    console.log('Đã đăng xuất')
  }
}

// Đóng dropdown khi click bên ngoài
window.addEventListener('click', (e) => {
  if (!e.target.closest('.relative')) {
    showNotifications.value = false
    showUserMenu.value = false
  }
})

// Restore login state on component mount
onMounted(() => {
  const token = localStorage.getItem('access_token')
  const savedUser = localStorage.getItem('user')
  
  if (token && savedUser) {
    try {
      const userData = JSON.parse(savedUser)
      isLoggedIn.value = true
      currentUser.value = {
        name: userData.name,
        email: userData.email,
        avatar: 'https://i.pravatar.cc/150?img=12'
      }
      console.log('Đã khôi phục phiên đăng nhập')
    } catch (error) {
      console.error('Lỗi khôi phục phiên:', error)
      localStorage.removeItem('access_token')
      localStorage.removeItem('user')
    }
  }
})
</script>

<style scoped>
/* Transition cho dropdown */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
