<template>
  <header class="w-full h-[65px] bg-gray-100 flex items-center px-4 sticky top-0 z-50 shadow-md">
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
      <button 
        @click="handleLogin"
        class="px-5 py-2 text-sm font-medium text-primary border border-primary rounded-xl hover:bg-primary/10 transition-colors"
      >
        Đăng nhập
      </button>

      <!-- Button Đăng ký -->
      <button 
        @click="handleRegister"
        class="px-5 py-2 text-sm font-medium text-white bg-primary rounded-xl hover:bg-primary-600 transition-colors"
      >
        Đăng ký
      </button>
    </div>
  </header>

  <!-- Modal Đăng ký -->
  <RegisterModal 
    :isOpen="showRegisterModal" 
    @close="showRegisterModal = false"
    @submit="handleRegisterSubmit"
  />
</template>

<script setup>
import { ref } from 'vue'
import RegisterModal from '../components/RegisterModal.vue'

// State
const searchQuery = ref('')
const showRegisterModal = ref(false)

// Methods
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Tìm kiếm:', searchQuery.value)
    // Thêm logic tìm kiếm
  }
}

const handleLogin = () => {
  console.log('Chuyển đến trang đăng nhập')
  // Thêm logic chuyển trang đăng nhập
  // router.push('/login')
}

const handleRegister = () => {
  showRegisterModal.value = true
}

const handleRegisterSubmit = (data) => {
  console.log('Dữ liệu đăng ký:', data)
  // Thêm logic xử lý đăng ký (gọi API, etc.)
}
</script>

<style scoped>
</style>
