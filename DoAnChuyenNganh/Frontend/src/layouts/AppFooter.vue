<template>
  <footer class="w-full min-h-[350px] bg-gray-900 text-gray-300 ">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-8">
        
        <!-- Cột 1: Thông tin liên hệ -->
        <div class="space-y-4">
          <h3 class="text-white text-lg font-semibold mb-4">Thông tin liên hệ</h3>
          
          <!-- Số điện thoại -->
          <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary mt-1 flex-shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
            </svg>
            <div>
              <p class="text-sm">Hotline</p>
              <a href="tel:0123456789" class="text-primary hover:text-primary-400 transition-colors">
                0123 456 789
              </a>
            </div>
          </div>

          <!-- Email -->
          <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary mt-1 flex-shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            <div>
              <p class="text-sm">Email</p>
              <a href="mailto:contact@example.com" class="text-primary hover:text-primary-400 transition-colors">
                contact@example.com
              </a>
            </div>
          </div>

          <!-- Địa chỉ -->
          <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary mt-1 flex-shrink-0">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            <div>
              <p class="text-sm">Địa chỉ</p>
              <p class="text-gray-400 text-sm">
                123 Đường ABC, Quận XYZ<br/>
                TP. Hồ Chí Minh, Việt Nam
              </p>
            </div>
          </div>
        </div>

        <!-- Cột 2: Khóa học (sẽ cập nhật sau) -->
        <div class="space-y-4">
          <h3 class="text-white text-lg font-semibold mb-4">Khóa học</h3>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Lập trình Web
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Lập trình Mobile
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Data Science
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Machine Learning
              </a>
            </li>
          </ul>
        </div>

        <!-- Cột 3: Khóa học (sẽ cập nhật sau) -->
        <div class="space-y-4">
          <h3 class="text-white text-lg font-semibold mb-4">Lộ trình học</h3>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Frontend Developer
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Backend Developer
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Full Stack Developer
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                DevOps Engineer
              </a>
            </li>
          </ul>
        </div>

        <!-- Cột 4: Khóa học (sẽ cập nhật sau) -->
        <div class="space-y-4">
          <h3 class="text-white text-lg font-semibold mb-4">Công nghệ</h3>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                JavaScript
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Python
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Java
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                PHP
              </a>
            </li>
          </ul>
        </div>

        <!-- Cột 5: Khóa học (sẽ cập nhật sau) -->
        <div class="space-y-4">
          <h3 class="text-white text-lg font-semibold mb-4">Hỗ trợ</h3>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Hướng dẫn học
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Câu hỏi thường gặp
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Chính sách bảo mật
              </a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-primary transition-colors text-sm">
                Điều khoản sử dụng
              </a>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright -->
      <div class="border-t border-gray-800 mt-12 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-gray-500 text-sm">
            © 2025 Kỹ năng lập trình. All rights reserved.
          </p>
          
          <!-- Social Media Icons -->
          <div class="flex gap-4">
            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Script sẽ được thêm sau nếu cần
</script>

<style scoped>
/* Custom styles nếu cần */
</style>
